<?php

use DocManager\Queue\Queue;
use DocManager\Queue\QueueElement;
use DocManager\Course\Course;
use DocManager\User\User;
use DocManager\Submission\Submission;

$app->get('/queue/add/:type/:id', $authorizationCheck(['ADMIN','INSTRUCTOR','ADVISOR']), function ($type, $id) use ($app) {
    if ((!isset($type) || ($type != 'user' && $type != 'course')) || (!isset($id) || !intval($id))) {
            $app->flash('global', 'Invalid Queue Type or Id!');

            $app->redirect($app->urlFor('home'));
    }

    $course = null;
    $id = intval($id);

    if ($type == 'course') {
        $course = Course::with('coordinator')->find($id);

        // Check to verify that the current user is an ADMINISTRATOR or a coordinator for the course
        // before allowing a queue to be created for this course
        if (!$app->auth->hasRole('ADMIN') && ($app->auth->id_user != $course->coordinator->id_user)) {
            $app->flash('global', 'You can only create course queues that you are assigned to coordinate!');

            $app->redirect($app->urlFor('home'));
        }
    } elseif ($type == "user") {
        // Check to verify that the currently authenticated user is not trying to create a queue
        // for a different user
        if ($id != $app->auth->id_user) {
            $app->flash('global', 'Cannot create user queues for other users!');

            $app->redirect($app->urlFor('home'));
        }
    } else {
        $app->flash('global', 'Invalid type');

        $app->redirect($app->urlFor('home'));
    }

    $app->render('addqueue.html.twig', [
        "type" => $type,
        "id" => $id,
        "course" => $course
    ]);
})->name('addqueue');

$app->post('/queue/save/:type/:id', $authorizationCheck(['ADMIN','INSTRUCTOR','ADVISOR']), function ($type, $id) use ($app) {
    if ((!isset($type) || ($type != 'user' && $type != 'course')) || (!isset($id) || !intval($id))) {
            $app->flash('global', 'Invalid Queue Type or Id!');

            $app->redirect($app->urlFor('home'));
    }

    $id = intval($id);

    // Get the owner model for this new Queue based on the type
    // Check to verify that the user can perform this action before we continue
    switch ($type) {
        case "user":
            $queueowner = $app->auth;
            break;
        case "course":
            $queueowner = Course::with('coordinator')->find($id)->first();

            // Check to verify that the current user is an ADMINISTRATOR or a coordinator for the course
            // before allowing a queue to be saved for this course
            // User shouldn't get this far since the same check was performed by the addqueue route, but
            // this is here for those who try to circumvent the security
            if (!$app->auth->hasRole('ADMIN') && ($app->auth->id_user != $queueowner->coordinator->id_user)) {
                $app->flash('global', 'You can only save course queues that you are assigned to coordinate!');

                $app->redirect($app->urlFor('addqueue', array('type' => $type, 'id' => $id)));
            }
            break;
    }

    if (empty($queueowner)) {
        $app->flash('global', 'Queue owner does not exist! Queue creation failed!');

        $app->redirect($app->urlFor('home'));
    }

    $request = $app->request;
    $doctotal = intval(strip_tags(trim($request->post('doctotal'))));
    $name = strip_tags(trim($request->post('name')));
    $description = strip_tags(trim($request->post('description')));
    $queueid = strip_tags($request->post('queueid'));
    $enabled = strip_tags($request->post('enabled'));
    $enabled = isset($enabled) ? true : false;

    $v = $app->validation;

    $v->addRuleMessage('doctotal', 'required', 'Queue must have at least 1 document.');
    $v->addRuleMessage('doctotal', 'min', 'Queue must have at least 1 document.');

    // Dynamically build the fields to validate based on the number
    // of documents provided for this queue
    $fields = [
        'doctotal|Documents' => [$doctotal, 'required|int|min(1, number)'],
        'name|Name' => [$name, 'required|max(30)'],
        'description|Description' => [$description, 'required|max(200)']
    ];

    for ($i=1; $i<=$doctotal; $i++) {
        $field_name = "name$i";
        $field_desc = "description$i";
        $value_name = strip_tags(trim($request->post("name$i")));
        $value_desc = strip_tags(trim($request->post("description$i")));

        $v->addRuleMessage($field_name, 'required', "Document $i Name is required.");
        $v->addRuleMessage($field_name, 'max', "Document $i Name 20 characters maxium.");
        $v->addRuleMessage($field_desc, 'required', "Document $i Description is required.");
        $v->addRuleMessage($field_desc, 'max', "Document $i Description 200 characters maxium.");

        $fields[$field_name."|Name"] = [$value_name, 'required|max(20)'];
        $fields[$field_desc."|Description"] = [$value_desc, 'required|max(200)'];
    }


    $v->validate($fields);

    if ($v->passes()) {
        //Create an array of QueueElements to add to this queue
        for ($i=1; $i<=$doctotal; $i++) {
            $elementname = strip_tags(trim($request->post("name$i")));
            $desc = strip_tags(trim($request->post("description$i")));

            $elements[$i] = new QueueElement([
                                'name' => $elementname,
                                'description' => $desc
            ]);
        }

        // Update or create the queue and get reference
        $queue = Queue::updateOrCreate([
            'id_queue' => $queueid,
            'name' => $name,
            'description' => $description,
            'is_enabled' => $enabled
        ]);

        var_dump($queueowner);
        // Associate queue with its owner
        $queueowner->queues()->save($queue);

        // Add queue elements for the new queue
        if (isset($elements)) {
            $queue->elements()->saveMany($elements);
        }

        $app->flash('global', 'Queue Saved');

        return $app->response->redirect($app->urlFor('home'));
  
    } else {
        // Set the course variable to pass into the view if this was to
        // create a course queue.
        $course = ($type == 'course') ? $queueowner : null;

        $app->render('addqueue.html.twig', [
            "errors" => $v->errors(),
            "request" => $request,
            "type" => $type,
            "id" => $id,
            "course" => $course
        ]);
    }
})->name('savequeue');

$app->get('/queue/view/:id', $authenticated(), function ($id) use ($app) {
    if (!isset($id) || !is_numeric($id)) {
        $app->flash('global', 'Invalid Queue Id');

        return $app->response->redirect($app->urlFor('home'));
    }

    // List of the current user's' queues
    $userQueues = $app->auth->queues->sortBy('name');
    // List of courses that the current users owns
    $mycourses = Course::where('id_coordinator', $app->auth->id_user)->with('queues')->get()->sortBy('name');
    // Declare the submissions collction that we'll load later depending on the users permissions
    $submissions;
    // This is the queue that has been requested to view
    $queue = Queue::find(intval($id))->load('queueable');

    // Loop through this user's queue collection and see if this user owns this queue
    $isOwnedByThisUser = false;
    $app->auth->queues->filter(function ($currentqueue) use ($queue, &$isOwnedByThisUser) {
        if ($currentqueue->id_queue == $queue->id_queue) {
            $isOwnedByThisUser = true;
        }
    });

    // Verify that a valid queue was found with the provided id
    if (!empty($queue)) {
        if ($app->auth->hasRole('ADMIN') || ($app->auth->hasRole(['INSTRUCTOR','ADVISOR']) &&  $isOwnedByThisUser)) {
            // Administrators or Instructors/Advisors owning this queue can see all submissions

            $submissions = $queue->submissions->load('user');

        } else {
            // Everyone else can only see their own submissions for this queue
            $submissions = Submission::where('id_user', $app->auth->id_user)->where('id_queue', $queue->id_queue)->with('user')->get();

        }
    } else {
        // Redirect to Home if no valid queue was found with this id

        return $app->response->redirect($app->urlFor('home'));

    }

    $app->render('home.view.queue.html.twig', [
        'queue' => $queue,
        'userqueues' => $userQueues,
        'mycourses' => $mycourses,
        'submissions' => $submissions
    ]);
})->name('home.view.queue');
