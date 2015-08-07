<?php

use DocManager\User\User;
use DocManager\Role\Role;

/*
    Routes for administrator functionality
    Notice that these routes require a role authorization check for the
    ADMIN role. This is performed by calling the $authorizationCheck function
    defined in RouteAuthorizationFilter.php and passing in the name of roles
    required to access this route.
 */

/*
    Route: Admin Home
    Name: admin
 */
$app->get('/admin', $authorizationCheck(['ADMIN']), function () use ($app) {
    $app->render('admin/admin.html.twig');
})->name('admin');

/*
    Route: Admin User Home
    Name: admin.users
 */
$app->get('/admin/user', $authorizationCheck(['ADMIN']), function () use ($app) {
    /*
        Retrieve a collection of all users loading the advisor user relationship
     */
    $users = $app->user->get()->load('advisor');
    /*
        Retrieve a collection of all roles
     */
    $role = new Role();
    $roles = $role->get();
    /*
        Retrieve a collection of users that have the 'ADVISOR' role.
        This will be passed into the view so users can select a valid advisor when registering.
     */
    $advisors = $app->user->whereHas('roles', function ($q) {
        $q->where('name', '=', 'ADVISOR');
    })->get()->sortBy('last_name');

    // Render the view passing in a used objects
    $app->render('admin/admin.user.html.twig', [
        'users' => $users,
        'roles' => $roles,
        'advisors' => $advisors
    ]);
})->name('admin.users');

/*
    Route: Admin Create New User
    Name: admin.newUser
 */
$app->get('/admin/user/new', $authorizationCheck(['ADMIN']), function () use ($app) {
    /*
        Retrieve a collection of all roles
     */
    $role = new Role();
    $roles = $role->get();
    /*
        Retrieve a collection of users that have the 'ADVISOR' role.
        This will be passed into the view so users can select a valid advisor when registering.
     */
    $advisors = $app->user->whereHas('roles', function ($q) {
        $q->where('name', '=', 'ADVISOR');
    })->get()->sortBy('last_name');

    // Render the view passing in a used variables
    $app->render('admin/admin.newuser.html.twig', [
        'roles' => $roles,
        'advisors' => $advisors
    ]);
})->name('admin.newUser');

/*
    Route: Admin Edit User
    Name: admin.editUser
 */
$app->get('/admin/user/:id', $authorizationCheck(['ADMIN']), function ($userId) use ($app) {
    // Retrieve the user's record
    $user = User::where('id_user', $userId)->first()->load('roles');
    if (isset($user)) {
        $user->load('advisor');

        // Retrieve a collection of all roles
        //$role = new Role();
        $roles = Role::all()->toArray();

        /*
            Retrieve a collection of users that have the 'ADVISOR' role.
            This will be passed into the view so users can select a valid advisor when registering.
         */
        $advisors = $app->user->whereHas('roles', function ($q) {
            $q->where('name', '=', 'ADVISOR');
        })->get()->sortBy('last_name');

        // Render the view passing in used objects
        $app->render('admin/admin.edituser.html.twig', [
            'user' => $user,
            'roles' => $roles,
            'advisors' => $advisors
        ]);
    } else {
        $app->flash('global', 'Invalid User');

        $app->response->redirect($app->urlFor('admin.users'));
    }
})->name('admin.editUser');

/*
    Route: Admin Save New User
    Name: admin.saveUser
 */
$app->post('/admin/user/save', $authorizationCheck(['ADMIN']), function () use ($app) {

    $request = $app->request;

    $userId = $request->post('userId');
    $email = $request->post('email');
    $firstname = $request->post('firstname');
    $lastname = $request->post('lastname');
    $major = $request->post('major');
    $password = $request->post('password');
    $passwordConfirm = $request->post('confirmpassword');
    $advisor = $request->post('advisor');
    if (!isset($advisor)) {
        $advisor = 0;
    }
    $roles = $request->post('roles');

    /*
    TODO: Form Validation & Check for duplicate email
     */

    if (isset($userId)) {
        $user = User::where('id_user', $userId)->first();
        $user->email = $email;
        $user->first_name = $firstname;
        $user->last_name = $lastname;
        $user->major = $major;
        $user->id_advisor = $advisor;
        $user->password = $app->hash->password($password);

        $user->save();

        $app->flash('global', 'User Updated');
    } else {
        $user = $app->user->create([
            'email' => $email,
            'first_name' => $firstname,
            'last_name' => $lastname,
            'major' => $major,
            'id_advisor' => $advisor,
            'password' => $app->hash->password($password)
        ]);

        $app->flash('global', 'User Added');
    }

    // Remove any existing roles
    $user->roles()->detach();

    // Add user roles if provided
    if (isset($roles) && count($roles)) {
        // Convert the array of strings from the roles post variable to an array of ints
        $rolesArray = [];
        for ($i=0; $i<count($roles); $i++) {
            $rolesArray[$i] = intval($roles[$i]);
        }

        // Add the new roles
        $user->roles()->attach($rolesArray);
    }

    $app->response->redirect($app->urlFor('admin.users'));
})->name('admin.saveUser');

/*
    Route: Admin Delete User(s)
    Name: admin.deleteUser
 */
$app->post('/admin/user/delete', $authorizationCheck(['ADMIN']), function () use ($app) {
    $request = $app->request;
    $userIdsStr = $request->post('userIds');

    if (isset($userIdsStr) && $userIdsStr != "") {
        // Convert the comma delimited string of user ids into an array of ints
        $userIds = array_map('intval', explode(",", $userIdsStr));

        // Loop through each user id
        foreach ($userIds as $userId) {
            $user = User::where('id_user', $userId)->first();
            
            // Delete user role database records in the many-to-many pivot table user_roles
            $user->roles()->detach();
            
            // Delete the user record
            $user->delete();
        }

        $app->flash('global', "User(s) deleted");
    }

    $app->response->redirect($app->urlFor('admin.users'));
})->name('admin.deleteUser');

/*
    Route: Admin Courses Home
    Name: admin.courses
 */
$app->get('/admin/course', $authorizationCheck(['ADMIN']), function () use ($app) {
    $app->render('admin/admin.course.html.twig');
})->name('admin.courses');
