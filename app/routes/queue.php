<?php

use DocManager\Queue\Queue;
use DocManager\Queue\QueueElement;
use DocManager\Course\Course;
use DocManager\User\User;

$app->get('/queue/add/:type/:id', $authorizationCheck(['ADMIN','INSTRUCTOR','ADVISOR']), function ($type, $id) use ($app) {
    $course = null;
    if ($type == 'course') {
        // Check to verify that the current user is an ADMINISTRATOR or a coordinator for the course
        // before allowing a queue to be created for this course
        if (!$app->auth->hasRole('ADMIN') && ($app->auth->id_user != Course::find(intval($id))->coordinator->id_user)) {
            $app->flash('global', 'You can only create course queues that you are assigned to coordinate!');

            $app->redirect($app->urlFor('home'));
        }
        $course = Course::find(intval($id));
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

    $request = $app->request;

    $id = intval($id);
    $doctotal = $request->post('doctotal');
    $courseid = $request->post('courseid');
    $name = $request->post('name');
    $description = $request->post('description');
    $queueid = $request->post('queueid');
    $enabled = $request->post('enabled');
    $enabled = isset($enabled) ? true : false;

    /*
    TODO: Form Validation
     */

    /*
        Create an array of QueueElements to add to this queue
     */
    if (isset($doctotal) && intval($doctotal)) {
        $doctotal = intval($doctotal);

        for ($i=0; $i<$doctotal; $i++) {
            $elementname = $request->post("name$i");
            $desc = $request->post("description$i");
            // Require an element name before we add to the array
            if (!empty($elementname)) {
                $elements[$i] = new QueueElement([
                                    'name' => $elementname,
                                    'description' => $desc
                ]);
            }
        }
    }

    // Get the owner model for this new Queue based on the type
    switch ($type) {
        case "user":
            $queueowner = User::find($id);
            break;
        case "course":
            $queueowner = Course::find($id);
            break;
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

    $app->response->redirect($app->urlFor('home'));
})->name('savequeue');
