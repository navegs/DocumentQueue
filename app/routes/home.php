<?php

use DocManager\Course\Course;
use DocManager\Queue\Queue;

$app->get('/', function () use ($app) {
    $userQueues = $app->auth->queues->sortBy('name');
    $mycourses = Course::where('id_coordinator', $app->auth->id_user)->with('queues')->get()->sortBy('name');
    $activeQueues = Queue::where('is_enabled', true)->with('queueable')->get()->sortBy('name');

    $app->render('home.default.html.twig', [
        'userqueues' => $userQueues,
        'mycourses' => $mycourses,
        'activequeues' => $activeQueues
    ]);
})->name('home');
