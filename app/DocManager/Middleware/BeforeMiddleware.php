<?php

namespace DocManager\Middleware;

use Slim\Middleware;
use DocManager\User\User;

class BeforeMiddleware extends Middleware
{
    public function call()
    {
        $this->app->hook('slim.before', [$this, 'run']);

        $this->next->call();
    }

    public function run()
    {
        if (isset($_SESSION[$this->app->config->get('auth.session')])) {
            $this->app->auth = User::with('roles', 'advisor', 'queues')->where('id_user', $_SESSION[$this->app->config->get('auth.session')])->first();
        }

        $this->app->view()->appendData([
            'auth' => $this->app->auth,
            'baseUrl' => $this->app->config->get('app.url')
        ]);
    }
}
