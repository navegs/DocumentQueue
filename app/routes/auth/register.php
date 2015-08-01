<?php

$app->get('/register', $guest(), function () use ($app) {
    // Retrieve a collection of users that have the 'ADVISOR'
    // role. This will be passed into the view so users can select
    // a valid advisor when registering.
    $advisors = $app->user->whereHas('roles', function ($q) {
        $q->where('name', '=', 'ADVISOR');
    })->get()->sortBy('last_name');

    $app->render('auth/register.html.twig', [
        'advisors' => $advisors
    ]);
})->name('register');

$app->post('/register', $guest(), function () use ($app) {

    $request = $app->request;

    $email = $request->post('email');
    $firstname = $request->post('firstname');
    $lastname = $request->post('lastname');
    $major = $request->post('major');
    $advisor = $request->post('advisor');
    $password = $request->post('password');
    $passwordConfirm = $request->post('password_confirm');

    /*
    TODO: Form Validation & Check for duplicate email
     */

    $app->user->create([
        'email' => $email,
        'first_name' => $firstname,
        'last_name' => $lastname,
        'major' => $major,
        'id_advisor' => $advisor,
        'password' => $app->hash->password($password)
    ]);

    $app->flash('global', "$email is now registered.");

    $app->response->redirect($app->urlFor('login'));

})->name('register.post');
