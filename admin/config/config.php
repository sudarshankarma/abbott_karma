<?php
// Session configuration with 1-hour timeout
ini_set('session.gc_maxlifetime', 3600);
session_set_cookie_params(3600);

session_start();

// Auto-logout after 1 hour of inactivity
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 3600)) {
    session_unset();
    session_destroy();
    header('Location: /abbott/admin/?controller=auth&action=login&timeout=1');
    exit;
}
$_SESSION['LAST_ACTIVITY'] = time();

// Base configuration
define('BASE_URL', 'http://localhost/abbott/admin');
define('SITE_NAME', 'Admin Panel');
define('UPLOAD_PATH', dirname(__DIR__) . '/../uploads/');
define('LOG_PATH', dirname(__DIR__) . '/logs/');

// Environment
define('ENVIRONMENT', 'development'); // Change to 'production' in production

// Error reporting
if (ENVIRONMENT == 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Include other config files
require_once 'database.php';
require_once 'constants.php';

// Get base directory
$base_dir = dirname(__DIR__);

// Autoload classes - FIXED VERSION
spl_autoload_register(function ($class_name) use ($base_dir) {
    $paths = [
        '/controllers/',
        '/models/',
        '/config/'
    ];
    
    foreach ($paths as $path) {
        $file = $base_dir . $path . $class_name . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
    
    // If class not found, log error
    error_log("Class not found: {$class_name}");
});

// Create logs directory if it doesn't exist
if (!is_dir(LOG_PATH)) {
    mkdir(LOG_PATH, 0755, true);
}

// Application logging function
function app_log($message, $type = 'INFO') {
    $log_file = LOG_PATH . 'application_' . date('Y-m-d') . '.log';
    $timestamp = date('Y-m-d H:i:s');
    $log_message = "[$timestamp] [$type] $message" . PHP_EOL;
    file_put_contents($log_file, $log_message, FILE_APPEND | LOCK_EX);
}

// Log application start
app_log("Application started from IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'));
?>