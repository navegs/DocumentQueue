<?php

$app->get('/admin', function () use ($app) {
    $app->render('admin/admin.html.twig');
})->name('admin');
