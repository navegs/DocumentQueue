<?php

$authenticationCheck = function ($rolesArray) use ($app) {
    return function () use ($rolesArray, $app) {
        // Retrieve the currently authenticated users roles as an array
        $userRoles = array_column($app->auth->roles->toArray(), 'name');
        // Walk over each array's elements and convert to lowercase
        // This is to ensure a case insensitive comparison
        array_walk($userRoles, function (&$value) {
            $value = strtolower($value);
        });
        array_walk($rolesArray, function (&$value) {
            $value = strtolower($value);
        });
        // If no matching role is found between the two arrays
        // then the user doesn't have the required role for this route
        // Redirect user back to home
        if (!array_intersect($userRoles, $rolesArray)) {
            $app->redirect($app->urlFor('home'));
        }
    };
};
