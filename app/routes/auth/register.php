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
    if (!isset($advisor)) {
        $advisor = 0;
    }
    $password = $request->post('password');
    $passwordConfirm = $request->post('password_confirm');

    /*
    $v = $app->validation;
    
    $v->validate([
        'email' => [$email, 'required|email|max(30)'],
        'first_name' => [$firstName, 'required|max(30)'],
        'last_name' => [$lastName, 'required|max(30)'],
        'password' => [$password, 'required|min(6)'],
        'password_confirm' => [$passwordConfirm, 'required|matches(password)'],
    ]);

    */


    $app->user->create([
        'email' => $email,
        'first_name' => $firstname,
        'last_name' => $lastname,
        'major' => $major,
        'id_advisor' => $advisor,
        'password' => $app->hash->password($password)
    ]);

    $test = $app->mail->send('email/auth/registered.php', ['user' => $user], function ($message) use ($user) {
        $message->to($user->email);//, $user->name);
        $message->subject('Thanks for registring.');
    });

    $app->flash('global', "$email is now registered.");

    return $app->response->redirect($app->urlFor('login'));

})->name('register.post');
