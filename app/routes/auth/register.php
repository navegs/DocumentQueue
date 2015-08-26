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

    $email = strip_tags($request->post('email'));
    $firstname = strip_tags($request->post('firstname'));
    $lastname = strip_tags($request->post('lastname'));
    $major = strip_tags($request->post('major'));
    $advisor = strip_tags($request->post('advisor'));
    $password = strip_tags($request->post('password'));
    $passwordConfirm = strip_tags($request->post('password_confirm'));

    $v = $app->validation;
    
    $v->validate([
        'email|Email' => [$email, 'required|email|max(30)'],
        'firstname|First Name' => [$firstname, 'required|max(30)'],
        'lastname|Last Name' => [$lastname, 'required|max(30)'],
        'major|Major' => [$major, 'required|max(100)'],
        'advisor|Advisor' => [$advisor, 'required'],
        'password|Password' => [$password, 'required|min(6)'],
        'password_confirm|Password Confirmation' => [$passwordConfirm, 'required|matches(password)'],
    ]);

    if ($v->passes()) {
        $user = $app->user->create([
            'email' => $email,
            'first_name' => $firstname,
            'last_name' => $lastname,
            'major' => $major,
            'id_advisor' => $advisor,
            'password' => $app->hash->password($password)
        ]);

        $app->mail->send('email/auth/registered.html.twig', ['user' => $user], function ($message) use ($user) {
            $message->to($user->email);
            $message->subject('Welcome to Stevens Document Manager, '.$user->first_name.'! Let\'s get started.');
        });

        $app->flash('global', "$email is now registered.");

        return $app->response->redirect($app->urlFor('login'));
    }

    // Retrieve a collection of users that have the 'ADVISOR'
    // role. This will be passed into the view so users can select
    // a valid advisor when registering.
    $advisors = $app->user->whereHas('roles', function ($q) {
        $q->where('name', '=', 'ADVISOR');
    })->get()->sortBy('last_name');

    $app->render('auth/register.html.twig', [
        'errors' => $v->errors(),
        'request' => $request,
        'advisors' => $advisors
    ]);

})->name('register.post');
