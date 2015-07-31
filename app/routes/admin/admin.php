<?php

/*
    Routes for administrator functionality
    Notice that these routes require a role authorization check for the
    ADMIN role. This is performed by calling the $authorizationCheck function
    defined in RouteAuthorizationFilter.php and passing in the name of roles
    required to access this route.
 */
$app->get('/admin', $authorizationCheck(['ADMIN']), function () use ($app) {
    $app->render('admin/admin.html.twig');
})->name('admin');
