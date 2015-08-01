<?php
/**
 * SecurityFilter Middleware
 *
 * This file includes anonymous functions that are used to check
 * authentication and authorization for routes
 *
 */

/**
 * Anonymous Function assigned to authorizationCheck variable
 * This function is used in routes to determine if the current user has the
 * required roles to access the route. If not, the user is redirected to the
 * 'home' route
 *
 * @param (Array) $rolesArray: An array of strings that contain the role names
 *                             that are allowed for this route
 */
$authorizationCheck = function ($rolesArray) use ($app) {
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

/**
 * Anonymous Function assigned to authenticationCheck variable
 * This function is used in routes to check authentication
 * If required and the user is not authentication or if not
 * required and the user is authenticated, the user is redirected to the
 * 'home' route
 *
 * This prevents users from accessing routes that should not be used
 * based on their authenticated status.
 *
 * See examples in function below
 *
 * @param (boolean) $authRequired: true or false if authentication is
 *                                 required for a route
 */
$authenticationCheck = function ($authRequired) use ($app) {
    return function () use ($authRequired, $app) {
        if ((!$app->auth && $authRequired) || ($app->auth && !$authRequired)) {
            $app->redirect($app->urlFor('home'));
        }
    };
};

/**
 * Anonymous Function representing a route that requires authentication
 *
 * For example, some routes require a user to be authenticated, but do
 * not require a specific role for authorization.
 *
 */
$authenticated = function () use ($authenticationCheck) {
    return $authenticationCheck(true);
};

/**
 * Anonymous Function representing a route that does not
 * require authentication (i.e. guest)
 *
 * For example, some routes do not require a user to be authenticated.
 * Registration and login are good examples
 *
 */
$guest = function () use ($authenticationCheck) {
    return $authenticationCheck(false);
};
