<?php

$app->get('/', function () use ($app) {
    $app->render('home.html.twig');
})->name('home');

$app->get('/flash', function () use ($app) {
    $app->flash('global', 'THIS IS A TEST');
    $app->response->redirect($app->urlFor('home'));
});
