<?php

/*
    Routes for administrator functionality
    Notice that these routes require a role authentication check for the
    ADMIN role. This is performed by calling the $authenticationCheck function
    defined in RouteAuthenticationFilter.php and passing in the name of roles
    required to access this route.
 */
$app->get('/admin', $authenticationCheck(['ADMIN']), function () use ($app) {
    $app->render('admin/admin.html.twig');
})->name('admin');
