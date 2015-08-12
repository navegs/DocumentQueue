<?php

use DocManager\Course\Course;
use DocManager\Queue\Queue;
use DocManager\Queue\QueueElement;

/*
    Route: Admin Course Home
    Name: admin.courses
 */
$app->get('/course', function () use ($app) {
    /*
        Retrieve a collection of all courses
     */
    $courses = Course::with('coordinator', 'addedBy')->get();

    // Render the view passing in a used objects
    $app->render('course.html.twig', [
        'courses' => $courses
    ]);
})->name('courses');

/*
    Route: Course queue submission
    Name: courseItemSubmission
 */
$app->get('/course/:id', function ($courseId) use ($app) {
    // Retrieve the user's record
    $course = Course::where('id_course', $courseId)->first()->load('coordinator');
    if (isset($course)) {
        /*
            Retrieve a collection of users that have the 'INSTRUCTOR' role.
            This will be passed into the view so users can select a valid coordinator when adding a course.
         */
        $coordinators = $app->user->whereHas('roles', function ($q) {
            $q->where('name', '=', 'INSTRUCTOR');
        })->get()->sortBy('last_name');

        // Render the view passing in used objects
        $app->render('editcourse.html.twig', [
            'course' => $course,
            'coordinators' => $coordinators
        ]);
    } else {
        $app->flash('global', 'Invalid Course');

        return $app->response->redirect($app->urlFor('courses'));
    }
})->name('courseItemSubmission');

/*
    Route: Save Course Item
    Name: saveCourseItem
 */
$app->post('/admin/course/save', function () use ($app) {

    $request = $app->request;

    $courseId = $request->post('courseId');
    $name = $request->post('name');
    $description = $request->post('description');
    $coordinator = $request->post('coordinator');
    // Get the id of the user that is submittinng this course item
    $addedby = $app->auth->id_user;
    $advisor = $request->post('advisor');

    /*
    TODO: Form Validation
     */

    $course = Course::updateOrCreate([
        'id_course' => $courseId,
        'name' => $name,
        'description' => $description,
        'id_coordinator' => $coordinator,
        'id_added_by' => $addedby
    ]);

    // If no courseId provided, we'll assume this is a new course
    // and add the default queue(s) and elements
    if (empty($courseId)) {
        /*
            Create an array of QueueElements to add to this queue
        */
        $elements = [new QueueElement(['name' => 'Add/Drop', 'description' => 'Add/Drop Form'])];

        // Update or create the queue and get reference
        $queue = Queue::create([
            'name' => 'Add/Drop',
            'description' => 'Queue for processing course add/drop requests',
            'is_enabled' => true
        ]);

        // Associate queue with its owner
        $course->queues()->save($queue);

        // Add queue elements for the new queue
        $queue->elements()->saveMany($elements);
    }

    $app->flash('global', 'Course Item Saved');

    return $app->response->redirect($app->urlFor('courses'));
})->name('saveCourseItem');
