<?php

/*
    Route to logout users
 */
$app->get('/logout', function () use ($app) {
    // Clear the auth session variable
    unset($_SESSION[$app->config->get('auth.session')]);
    // Create a flash message to notify user of logout
    $app->flash('global', 'You have been logged out.');
    // Redirect user to home page after logout is complete
    return $app->response->redirect($app->urlFor('home'));
})->name('logout');
