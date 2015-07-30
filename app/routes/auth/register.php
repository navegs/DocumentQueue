<?php

$app->get('/register', function () use ($app) {
    $app->render('auth/register.html.twig');
})->name('register');

$app->post('/register', function () use ($app) {

    $request = $app->request;

    $email = $request->post('email');
    $firstname = $request->post('firstname');
    $lastname = $request->post('lastname');
    $major = $request->post('major');
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
        'password' => $app->hash->password($password)
    ]);

    $app->flash('global', "$email is now registered.");

    $app->response->redirect($app->urlFor('home'));

})->name('register.post');
