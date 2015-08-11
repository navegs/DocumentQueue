<?php

use DocManager\Course\Course;

$app->get('/submissions', $authenticated(), function () use ($app) {
    $userQueues = $app->auth->queues->sortBy('name');
    $mycourses = Course::where('id_coordinator', $app->auth->id_user)->with('queues')->get()->sortBy('name');
    $submissions = $app->auth->submissions;

    $app->render('home.view.submissions.html.twig', [
        'userqueues' => $userQueues,
        'mycourses' => $mycourses,
        'submissions' => $submissions
    ]);
})->name('home.view.submissions');
