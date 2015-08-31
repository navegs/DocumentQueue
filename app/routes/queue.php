<?php

use DocManager\Queue\Queue;
use DocManager\Queue\QueueElement;
use DocManager\Course\Course;
use DocManager\User\User;
use DocManager\Submission\Submission;

$app->get('/queue/add/:type/:id', $authorizationCheck(['ADMIN','INSTRUCTOR','ADVISOR']), function ($type, $id) use ($app) {
    $course = null;

    if ($type == 'course') {
        $course = Course::with('coordinator')->find(intval($id));

        // Check to verify that the current user is an ADMINISTRATOR or a coordinator for the course
        // before allowing a queue to be created for this course
        if (!$app->auth->hasRole('ADMIN') && ($app->auth->id_user != $course->coordinator->id_user)) {
            $app->flash('global', 'You can only create course queues that you are assigned to coordinate!');

            $app->redirect($app->urlFor('home'));
        }
    } elseif ($type == "user") {
        // Check to verify that the currently authenticated user is not trying to create a queue
        // for a different user
        if (intval($id) != $app->auth->id_user) {
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
    if (!isset($type) || !isset($id)) {
            $app->flash('global', 'Queue Type or Id not provided!');

            $app->redirect($app->urlFor('home'));
    }
    if ($type != 'user' && $type != 'course') {
            $app->flash('global', 'Invalid Queue Type!');

            $app->redirect($app->urlFor('home'));
    }

    // Get the owner model for this new Queue based on the type
    // Check to verify that the user can perform this action before we continue
    switch ($type) {
        case "user":
            $queueowner = User::find($id);
            // Check to verify that the currently authenticated user is not trying to create a queue
            // for a different user
            // User shouldn't get this far since the same check was performed by the addqueue route, but
            // this is here for those who try to circumvent the security
            if (intval($id) != $app->auth->id_user) {
                $app->flash('global', 'Cannot save user queues for other users!');

                $app->redirect($app->urlFor('addqueue', array('type' => $type, 'id' => $id)));
            }
            break;
        case "course":
            $queueowner = Course::with('coordinator')->find($id);

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

    $request = $app->request;

    $id = intval($id);
    $doctotal = strip_tags($request->post('doctotal'));
    $courseid = strip_tags($request->post('courseid'));
    $name = strip_tags($request->post('name'));
    $description = strip_tags($request->post('description'));
    $queueid = strip_tags($request->post('queueid'));
    $enabled = strip_tags($request->post('enabled'));
    $enabled = isset($enabled) ? true : false;

    $v = $app->validation;

    $v->validate([
        'doctotal|Document Total' => [$doctotal, 'required'],
        'courseid|Course ID' => [$courseid, 'required'],
        'name|Name' => [$name, 'required|max(30)'],
        'description|Description' => [$description, 'required|max(200)'],
    ]);

    if ($v->passes()) {
        //Create an array of QueueElements to add to this queue
        if (isset($doctotal) && intval($doctotal)) {
            $doctotal = intval($doctotal);

            for ($i=0; $i<$doctotal; $i++) {
                $elementname = strip_tags($request->post("name$i"));
                $desc = strip_tags($request->post("description$i"));

                $vAttach = $app->validation;

                $vAttach->validate([
                    'elementname|Element Name' => [$elementname, 'required'],
                ]);

                if ($vAttach->passes()) {
                    if (!empty($elementname)) {
                        $elements[$i] = new QueueElement([
                                            'name' => $elementname,
                                            'description' => $desc
                        ]);
                    }
                }
            }
        }

        // Update or create the queue and get reference
        $queue = Queue::updateOrCreate([
            'id_queue' => $queueid,
            'name' => $name,
            'description' => $description,
            'is_enabled' => $enabled
        ]);

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
    if (!is_numeric($id)) {
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
