<?php

use DocManager\Course\Course;

$app->get('/', function () use ($app) {
    $userQueues = $app->auth->queues;
    $mycourses = Course::where('id_coordinator', $app->auth->id_user)->with('queues')->get();

    $app->render('home.html.twig', [
        'userqueues' => $userQueues,
        'mycourses' => $mycourses
    ]);
})->name('home');
