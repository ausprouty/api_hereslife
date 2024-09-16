<?php
// index.php
error_log('index-1');

// Load the appropriate environment configuration
require_once __DIR__ . '/App/Configuration/config.php'; // Load environment-specific config
error_log('index-7');
// Load Debugging tools
require_once __DIR__ . '/App/Services/Debugging.php'; 
error_log('index-10');
writeLog('index-13', 'Loaded Debugging tools');
writeLog('index-14', 'ENVIRONMENT: ' . ENVIRONMENT);
// Error reporting based on environment
if ($_SERVER['SERVER_NAME'] === 'localhost') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Include necessary files

require_once __DIR__ . '/Vendor/autoload.php';
use App\Controllers\PostInputController;
use App\Middleware\PreflightMiddleware;
use App\Middleware\PostAuthorizationMiddleware;
use App\Middleware\CORSMiddleware;

// Define and apply the middleware stack
$middlewares = [
    new PreflightMiddleware(),
    new CORSMiddleware(),
];

applyMiddleware($middlewares, $_SERVER);

function applyMiddleware(array $middlewares, $request) {
    $next = function($request) use (&$middlewares, &$next) {
        if (empty($middlewares)) {
            // All middlewares have been processed
            return;
        }
        // Get the next middleware in the stack
        $middleware = array_shift($middlewares);
        // Process the middleware, passing the request and the next function
        $middleware->handle($request, $next);
    };
    // Start processing the middleware stack
    return $next($request);
}

$postData = PostAuthorizationMiddleware::getDataSet();
writeLog('index-53', $postData);




// Main application logic or routing
require_once __DIR__ . '/routes.php';
