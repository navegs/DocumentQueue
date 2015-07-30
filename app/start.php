<?php

/**
 * Bootstraps the application
 *    Initializes our session
 *    Define global variables
 *    Load required vendor libraries
 *    Create our application container
 **/

use DocManager\Helpers\Hash;
use DocManager\User\User;
use DocManager\Middleware\BeforeMiddleware;
use Noodlehaus\Config;
use Slim\Slim;
use Slim\Views\Twig;
use Slim\Views\TwigExtension;

session_cache_limiter(false);
session_start();

ini_set('display_errors', 'On');

define('INC_ROOT', dirname(__DIR__));

require INC_ROOT . '/vendor/autoload.php';

$app = new Slim([
    'mode' => file_get_contents(INC_ROOT . '/mode.php'),
    'view' => new Twig(),
    'templates.path' => INC_ROOT . '/app/views'
]);

$app->add(new DocManager\Middleware\BeforeMiddleware);

$app->configureMode($app->config('mode'), function () use ($app) {
    $app->config = Config::load(INC_ROOT . "/app/config/{$app->mode}.php");
});

require 'database.php';
require 'routes.php';

$app->auth = false;

$app->container->set('user', function () {
    return new User;
});

$app->container->singleton('hash', function () use ($app) {
    return new Hash($app->config);
});

$view = $app->view();

$view->parserOptions = [
    'debug' => $app->config->get('twig.debug')
];

$view->parserExtensions = [
    new TwigExtension
];
