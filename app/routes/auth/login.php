<?php

$app->get('/login', $guest(), function () use ($app) {
    $app->render('auth/login.html.twig');
})->name('login');

$app->post('/login', $guest(), function () use ($app) {
    
    $request = $app->request;

    $email = strip_tags(trim($request->post('email')));
    $password = strip_tags(trim($request->post('password')));

    $v = $app->validation;
    
    $v->validate([
        'email|Email' => [$email, 'required'],
        'password|Password' => [$password, 'required'],
    ]);

    if ($v->passes()) {
        $user = $app->user->where('email', $email)->first();

        if ($user && $app->hash->passwordCheck($password, $user->password)) {
            $_SESSION[$app->config->get('auth.session')] = $user->id_user;

            $app->flash('global', $user->email.' is now signed in!');

            return $app->response->redirect($app->urlFor('home'));
        } else {
            $app->flash('global', 'Could not log in user '.$email);

            return $app->response->redirect($app->urlFor('login'));
        }
    } else {
        $app->render('auth/login.html.twig', [
                'errors' => $v->errors(),
                'request' => $request
            ]);
    }


})->name('login.post');
