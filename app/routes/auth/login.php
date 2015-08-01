<?php

$app->get('/login', $guest(), function () use ($app) {
    $app->render('auth/login.html.twig');
})->name('login');

$app->post('/login', $guest(), function () use ($app) {

    $request = $app->request;

    $email = $request->post('email');
    $password = $request->post('password');

    /*
    TODO: FORM VALIDATION
     */
    
    $user = $app->user->where('email', $email)->first();

    if ($user && $app->hash->passwordCheck($password, $user->password)) {
        $_SESSION[$app->config->get('auth.session')] = $user->id_user;

        $app->flash('global', $user->email.' is now signed in!');

        $app->response->redirect($app->urlFor('home'));
    } else {
        $app->flash('global', 'Could not log in user '.$email);

        $app->response->redirect($app->urlFor('login'));
    }


})->name('login.post');
