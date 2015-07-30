<?php

$app->get('/advisor', function () use ($app) {
    $app->render('advisor.html.twig');
})->name('advisor');
