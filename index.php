<?php
/**
 * superMetrics - Fetch and manipulate JSON data from a fictional Supermetrics Social Network REST API.
 * based on symfony and other packages - Simple Core Framework implementation
 *
 * @author  Mohamed Hassan.
 */

/**
 * Register The Auto Loader :
 * automatically generated class loader for our application.
 */
require __DIR__ . '/vendor/autoload.php';

/**
 * Turn On The Lights :
 * the framework and gets it ready for use
 */
require 'library/Framework/Core.php';

/**
 * Run The Application :
 * handle the incoming request through the kernel, and send the associated response back
 */
use Spatie\Blade\Blade;
$request = \Symfony\Component\HttpFoundation\Request::createFromGlobals();

$app = new Framework\Core();

/**
 * Routes
 */
require __DIR__ . '/routes/web.php';

$response = $app->handle($request);

$response->send();

