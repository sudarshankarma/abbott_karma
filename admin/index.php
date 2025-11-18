<?php
require_once 'config/config.php';

// Get controller and action from URL
$controller = $_GET['controller'] ?? 'dashboard';
$action = $_GET['action'] ?? 'index';
$id = $_GET['id'] ?? null;
$app_id = $_GET['app_id'] ?? null;

// Route mapping
$routes = [
    'auth' => 'AuthController',
    'dashboard' => 'DashboardController',
    'applications' => 'ApplicationsController',
    'documents' => 'DocumentsController',
    'users' => 'UsersController',
    'activity' => 'ActivityController'
];

// Check if controller exists
if (!array_key_exists($controller, $routes)) {
    $controller = 'dashboard';
}

$controller_class = $routes[$controller];

// Check if controller file exists
$controller_file = "controllers/{$controller_class}.php";
if (!file_exists($controller_file)) {
    die("Controller file not found: {$controller_file}");
}

// Include and instantiate controller
require_once $controller_file;

// Check if controller class exists
if (!class_exists($controller_class)) {
    die("Controller class not found: {$controller_class}");
}

$controller_instance = new $controller_class();

// Check if action exists
if (!method_exists($controller_instance, $action)) {
    die("Action not found: {$action} in {$controller_class}");
}

// Execute the action with proper parameters
try {
    // Get method reflection to check parameters
    $method = new ReflectionMethod($controller_class, $action);
    $params = $method->getParameters();
    
    if (count($params) > 0) {
        // Method requires parameters
        $first_param = $params[0];
        
        // Check if we have an ID parameter
        if ($id) {
            $controller_instance->$action($id);
        } elseif ($app_id) {
            $controller_instance->$action($app_id);
        } else {
            // No ID provided, redirect to index
            switch ($controller) {
                case 'applications':
                    if ($action === 'view' || $action === 'edit') {
                        header('Location: ?controller=applications&action=index&error=missing_id');
                        exit;
                    }
                    break;
                default:
                    $controller_instance->$action();
            }
        }
    } else {
        // Method doesn't require parameters
        $controller_instance->$action();
    }
} catch (Exception $e) {
    error_log("Controller error in {$controller_class}::{$action}: " . $e->getMessage());
    
    // Show user-friendly error message
    if (ENVIRONMENT == 'development') {
        echo "Error: " . $e->getMessage() . "<br>";
        echo "File: " . $e->getFile() . " on line " . $e->getLine() . "<br>";
        echo "Stack trace: <pre>" . $e->getTraceAsString() . "</pre>";
    } else {
        http_response_code(500);
        echo "An error occurred. Please try again later.";
        
        // Log the error
        app_log("Controller error: " . $e->getMessage(), 'ERROR');
    }
}
?>