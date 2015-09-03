<?php

use DocManager\User\User;
use DocManager\Role\Role;
use DocManager\Course\Course;
use DocManager\Queue\Queue;
use DocManager\Queue\QueueElement;

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

    // Render the view passing in a used objects
    $app->render('admin/admin.user.html.twig', [
        'users' => $users
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
    if (!isset($userId) || !is_numeric($userId)) {
            $app->flash('global', 'User Id invalid or not provided!');

            $app->redirect($app->urlFor('admin.users'));
    }

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

        return $app->response->redirect($app->urlFor('admin.users'));
    }
})->name('admin.editUser');

/*
    Route: Admin Save New User
    Name: admin.saveUser
 */
$app->post('/admin/user/save', $authorizationCheck(['ADMIN']), function () use ($app) {

    $request = $app->request;

    $userId = strip_tags(trim($request->post('userId')));
    $email = strip_tags(trim($request->post('email')));
    $firstname = strip_tags(trim($request->post('firstname')));
    $lastname = strip_tags(trim($request->post('lastname')));
    $major = strip_tags(trim($request->post('major')));
    $password = strip_tags(trim($request->post('password')));
    $passwordConfirm = strip_tags(trim($request->post('password_confirm')));
    $advisor = strip_tags(trim($request->post('advisor')));
    $roles = $request->post('roles');

    $v = $app->validation;
    
    if (!empty($userId)) {
        // User Id provided, so we are updating an existing user
        // Ensure user id is valide or redirect user
        $user = User::where('id_user', $userId)->first()->load('roles', 'advisor');
        
        if (empty($user)) {
            // No user was found with this id, redirect to admin users page
            $app->flash('global', 'Invalid User Id');
            return $app->response->redirect($app->urlFor('admin.users'));
        }

        // No need to check for email and password fields
        $v->validate([
            'firstname|First Name' => [$firstname, 'required|max(30)'],
            'lastname|Last Name' => [$lastname, 'required|max(30)'],
            'major|Major' => [$major, 'required|max(100)'],
            'advisor|Advisor' => [$advisor, 'required'],
        ]);

        if ($v->passes()) {
            $user->update([
                'id_user' => $userId,
                'first_name' => $firstname,
                'last_name' => $lastname,
                'major' => $major,
                'id_advisor' => $advisor
            ]);

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

            $app->flash('global', 'User Info Saved');
            return $app->response->redirect($app->urlFor('admin.editUser', array('id' => $user->id_user)));
        } else {
            // Update User Validation Failed
            //
            // Retrieve a collection of users that have the 'ADVISOR'
            // role. This will be passed into the view so users can select
            // a valid advisor when registering.
            $advisors = $app->user->whereHas('roles', function ($q) {
                $q->where('name', '=', 'ADVISOR');
            })->get()->sortBy('last_name');

            // Retrieve a collection of all roles
            $roles = Role::all()->toArray();

            $app->render('admin/admin.edituser.html.twig', [
                    'errors' => $v->errors(),
                    'request' => $request,
                    'user' => $user,
                    'roles' => $roles,
                    'advisors' => $advisors
            ]);
        }
    } else {
        // No user id provided, so this is a new user
        // Validate everything and create user
        $v->validate([
            'email|Email' => [$email, 'required|email|max(30)|uniqueEmail'],
            'firstname|First Name' => [$firstname, 'required|max(30)'],
            'lastname|Last Name' => [$lastname, 'required|max(30)'],
            'major|Major' => [$major, 'required|max(100)'],
            'advisor|Advisor' => [$advisor, 'required'],
            'password|Password' => [$password, 'required|min(6)'],
            'password_confirm|Password Confirmation' => [$passwordConfirm, 'required|matches(password)'],
        ]);

        if ($v->passes()) {
            $user = User::create([
                'email' => $email,
                'first_name' => $firstname,
                'last_name' => $lastname,
                'major' => $major,
                'id_advisor' => $advisor,
                'password' => $app->hash->password($password)
            ]);

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

            $app->flash('global', 'User Info Saved');
            return $app->response->redirect($app->urlFor('admin.editUser', array('id' => $user->id_user)));
        } else {
            // New user validation failed
            //
            // Retrieve a collection of users that have the 'ADVISOR'
            // role. This will be passed into the view so users can select
            // a valid advisor when registering.
            $advisors = $app->user->whereHas('roles', function ($q) {
                $q->where('name', '=', 'ADVISOR');
            })->get()->sortBy('last_name');

            // Retrieve a collection of all roles
            $roles = Role::all()->toArray();

            if (!empty($userId)) {
                $user = User::where('id_user', $userId)->first()->load('roles', 'advisor');

                $app->render('admin/admin.edituser.html.twig', [
                        'errors' => $v->errors(),
                        'request' => $request,
                        'user' => $user,
                        'roles' => $roles,
                        'advisors' => $advisors
                    ]);
            } else {
                $app->render('admin/admin.newuser.html.twig', [
                    'errors' => $v->errors(),
                    'request' => $request,
                    'roles' => $roles,
                    'advisors' => $advisors
                ]);
            }
        }
    }
})->name('admin.saveUser');

/*
    Route: Admin Reset Password
    Name: admin.resetPassword
 */
$app->post('/admin/user/resetpassword', $authorizationCheck(['ADMIN']), function () use ($app) {
    $request = $app->request;

    $userId = strip_tags(trim($request->post('userId')));
    $password = strip_tags(trim($request->post('password')));
    $passwordConfirm = strip_tags(trim($request->post('password_confirm')));

    if (!isset($userId) || !is_numeric($userId)) {
            $app->flash('global', 'User Id invalid or not provided!');

            $app->redirect($app->urlFor('admin.users'));
    }

    $v = $app->validation;
    
    $v->validate([
        'password|Password' => [$password, 'required|min(6)'],
        'password_confirm|Password Confirmation' => [$passwordConfirm, 'required|matches(password)'],
    ]);

    if ($v->passes()) {
        $user = User::updateOrCreate([
            'id_user' => $userId,
            'password' => $app->hash->password($password)
        ]);

        $app->flash('global', 'User Password Saved');

        return $app->response->redirect($app->urlFor('admin.editUser', array('id' => $userId)));
    } else {
        // Retrieve a collection of users that have the 'ADVISOR'
        // role. This will be passed into the view so users can select
        // a valid advisor when registering.
        $advisors = $app->user->whereHas('roles', function ($q) {
            $q->where('name', '=', 'ADVISOR');
        })->get()->sortBy('last_name');

        // Retrieve a collection of all roles
        $roles = Role::all()->toArray();

        $user = User::where('id_user', $userId)->first()->load('roles', 'advisor');

        $app->render('admin/admin.edituser.html.twig', [
                        'errors' => $v->errors(),
                        'request' => $request,
                        'user' => $user,
                        'roles' => $roles,
                        'advisors' => $advisors
        ]);
    }
    
})->name('admin.resetPassword');

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

    return $app->response->redirect($app->urlFor('admin.users'));
})->name('admin.deleteUser');

/*
    Route: Admin Course Home
    Name: admin.courses
 */
$app->get('/admin/course', $authorizationCheck(['ADMIN']), function () use ($app) {
    /*
        Retrieve a collection of all users loading the advisor user relationship
     */
    $courses = Course::with('coordinator', 'addedBy')->get();

    // Render the view passing in a used objects
    $app->render('admin/admin.course.html.twig', [
        'courses' => $courses
    ]);
})->name('admin.courses');

/*
    Route: Admin Delete Course(s)
    Name: admin.deleteCourse
 */
$app->post('/admin/course/delete', $authorizationCheck(['ADMIN']), function () use ($app) {
    $request = $app->request;
    $courseIdsStr = $request->post('courseIds');

    if (isset($courseIdsStr) && $courseIdsStr != "") {
        // Convert the comma delimited string of course ids into an array of ints
        $courseIds = array_map('intval', explode(",", $courseIdsStr));

        // Loop through each course id
        foreach ($courseIds as $courseId) {
            $course = Course::where('id_course', $courseId)->first();
                        
            // Delete the course
            $course->delete();
        }

        $app->flash('global', "Course(s) and related data deleted");
    }

    return $app->response->redirect($app->urlFor('admin.courses'));
})->name('admin.deleteCourse');

/*
    Route: Admin Create New Course
    Name: admin.newCourse
 */
$app->get('/admin/course/new', $authorizationCheck(['ADMIN']), function () use ($app) {
    /*
        Retrieve a collection of users that have the 'INSTRUCTOR' role.
        This will be passed into the view so users can select a valid advisor when registering.
     */
    $coordinators = $app->user->whereHas('roles', function ($q) {
        $q->where('name', '=', 'INSTRUCTOR');
    })->get()->sortBy('last_name');

    // Render the view passing in a used variables
    $app->render('admin/admin.newcourse.html.twig', [
        'coordinators' => $coordinators
    ]);
})->name('admin.newCourse');

/*
    Route: Admin Edit Course
    Name: admin.editCourse
 */
$app->get('/admin/course/:id', $authorizationCheck(['ADMIN']), function ($courseId) use ($app) {
    if (!isset($courseId) || !is_numeric($courseId)) {
        $app->flash('global', 'Course Id invalid or not provided!');

        $app->redirect($app->urlFor('admin.courses'));
    }

    // Retrieve the user's record
    $course = Course::where('id_course', $courseId)->first()->load('coordinator');
    if (isset($course)) {
        /*
            Retrieve a collection of users that have the 'INSTRUCTOR' role.
            This will be passed into the view so users can select a valid coordinator when adding a course.
         */
        $coordinators = $app->user->whereHas('roles', function ($q) {
            $q->where('name', '=', 'INSTRUCTOR');
        })->get()->sortBy('last_name');

        // Render the view passing in used objects
        $app->render('admin/admin.editcourse.html.twig', [
            'course' => $course,
            'coordinators' => $coordinators
        ]);
    } else {
        $app->flash('global', 'Invalid Course');

        return $app->response->redirect($app->urlFor('admin.courses'));
    }
})->name('admin.editCourse');

/*
    Route: Admin Save Course
    Name: admin.saveCourse
 */
$app->post('/admin/course/save', $authorizationCheck(['ADMIN']), function () use ($app) {

    $request = $app->request;

    $courseId = strip_tags(trim($request->post('courseId')));
    $name = strip_tags(trim($request->post('name')));
    $description = strip_tags(trim($request->post('description')));
    $coordinator = strip_tags(trim($request->post('coordinator')));
    // Get the id of the user that is adding this course
    $addedby = $app->auth->id_user;

    $v = $app->validation;

    $v->validate([
        'name|Name' => [$name, 'required|max(80)'],
        'description|Description' => [$description, 'required|max(200)'],
        'coordinator|Coordinator' => [$coordinator, 'required']
    ]);

    if (isset($courseId) && !empty($courseId)) {
        // Course Id provided, so we are updating an existing course
        // Ensure user id is valide or redirect user
        $course = Course::where('id_course', $courseId)->first();
        
        if (empty($course)) {
            // No course was found with this id, redirect to admin courses page
            $app->flash('global', 'Invalid Course Id');
            return $app->response->redirect($app->urlFor('admin.courses'));
        }

        // Check field validation results
        if ($v->passes()) {
            $course->update([
                'id_course' => $courseId,
                'name' => $name,
                'description' => $description,
                'id_coordinator' => $coordinator
            ]);

            $app->flash('global', 'Course Info Saved');
            return $app->response->redirect($app->urlFor('admin.editCourse', array('id' => $course->id_course)));
        } else {
            // Update User Validation Failed
            //
            //
            // Retrieve a collection of users that have the 'INSTRUCTOR' role.
            // This will be passed into the view so users can select a valid advisor when registering.
            $coordinators = $app->user->whereHas('roles', function ($q) {
                $q->where('name', '=', 'INSTRUCTOR');
            })->get()->sortBy('last_name');

            $app->render('admin/admin.editcourse.html.twig', [
                    'errors' => $v->errors(),
                    'request' => $request,
                    'course' => $course,
                    'coordinators' => $coordinators
            ]);
        }
    } else {
        // No course id provided, so this is a new course
        // Create course and default queues
        
        // Check field validation results
        if ($v->passes()) {
            $course = Course::create([
                'name' => $name,
                'description' => $description,
                'id_coordinator' => $coordinator,
                'id_added_by' => $addedby
            ]);

            /*
                Create an array of QueueElements to add to this queue
            */
            $elements = [new QueueElement(['name' => 'Add/Drop', 'description' => 'Add/Drop Form'])];

            // Update or create the queue and get reference
            $queue = Queue::create([
                'name' => 'Add/Drop',
                'description' => 'Queue for processing course add/drop requests',
                'is_enabled' => true
            ]);

            // Associate queue with its owner
            $course->queues()->save($queue);

            // Add queue elements for the new queue
            $queue->elements()->saveMany($elements);


            $app->flash('global', 'Course Info Saved');
            return $app->response->redirect($app->urlFor('admin.editCourse', array('id' => $course->id_course)));
        } else {
            // New course validation failed
            //
            // Retrieve a collection of users that have the 'INSTRUCTOR' role.
            // This will be passed into the view so users can select a valid advisor when registering.
            $coordinators = $app->user->whereHas('roles', function ($q) {
                $q->where('name', '=', 'INSTRUCTOR');
            })->get()->sortBy('last_name');

            $app->render('admin/admin.newcourse.html.twig', [
                    'errors' => $v->errors(),
                    'request' => $request,
                    'coordinators' => $coordinators
            ]);
        }
    }
})->name('admin.saveCourse');
