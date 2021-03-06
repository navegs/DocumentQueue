<?php

/**
 * Bootstraps the application
 *    Initializes our session
 *    Define global variables
 *    Load required vendor libraries
 *    Create our application container and set required application values
 **/

use DocManager\Helpers\Hash;
use DocManager\User\User;
use DocManager\Mail\Mailer;
use DocManager\Validation\Validator;
use DocManager\Middleware\BeforeMiddleware;
use Noodlehaus\Config;
use Slim\Slim;
use Slim\Views\Twig;
use Slim\Views\TwigExtension;

// Start the session
session_cache_limiter(false);
session_start();

// This is only used for debugging in development
ini_set('display_errors', 'On');

define('INC_ROOT', dirname(__DIR__));

require INC_ROOT . '/vendor/autoload.php';

$app = new Slim([
    'mode' => file_get_contents(INC_ROOT . '/mode.php'),
    'view' => new Twig(),
    'templates.path' => INC_ROOT . '/app/views'
]);

// Register custom Middleware class that executes before each request
$app->add(new DocManager\Middleware\BeforeMiddleware);

// Loads and sets the configuration file based on the environment specified
// in /mode.php
$app->configureMode($app->config('mode'), function () use ($app) {
    $app->config = Config::load(INC_ROOT . "/app/config/{$app->mode}.php");
});

require 'database.php';
require 'DocManager/Middleware/RouteSecurityFilter.php';
require 'routes.php';

// Set default value for auth in Slim container
// Used for for authentication
$app->auth = false;

// Make the custom User class available within the Slim container
// Used for authentication
$app->container->set('user', function () {
    return new User;
});

// Make the custom Hash helper class availabe within the Slim container
// Used for authentication and security
$app->container->singleton('hash', function () use ($app) {
    return new Hash($app->config);
});

// Make the custom Validator helper class availabe within the Slim container
// Used for validation
$app->container->singleton('validation', function () use ($app) {
    return new Validator($app->user);
});

// Make the custom PHPMailer helper class availabe within the Slim container
// Used for emailing
$app->container->singleton('mail', function () use ($app) {
    $mailer = new PHPMailer;
    $mailer->isSMTP();
    $mailer->Host = $app->config->get('mail.host');
    $mailer->SMTPAuth = $app->config->get('mail.smtp_auth');
    $mailer->SMTPSecure = $app->config->get('mail.smtp_secure');
    $mailer->Port = $app->config->get('mail.port');
    $mailer->Username = $app->config->get('mail.username');
    $mailer->Password = $app->config->get('mail.password');
    $mailer->SetFrom($app->config->get('mail.username'), 'Stevens Document Manager');
    $mailer->isHTML($app->config->get('mail.html'));
    
    return new Mailer($app->view, $mailer);
});

// Setup our views for Slim
$view = $app->view();

// Set Twigs debug mode based on the value set in the configuration file
$view->parserOptions = [
    'debug' => $app->config->get('twig.debug')
];

$view->parserExtensions = [
    new TwigExtension
];
