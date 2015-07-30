<?php

$app->get('/course', function () use ($app) {
    $app->render('course.html.twig');
})->name('course');
