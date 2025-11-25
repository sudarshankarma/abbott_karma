<?php
class BaseController {
    protected $db;
    protected $model;
    
    public function __construct($model = null) {
        $database = new Database();
        $this->db = $database->getConnection();
        
        if ($model) {
            $model_class = $model . 'Model';
            if (file_exists("models/{$model_class}.php")) {
                require_once "models/{$model_class}.php";
                $this->model = new $model_class($this->db);
            }
        }
    }
    
    protected function checkAuth($allowed_roles = []) {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ?controller=auth&action=login');
            exit;
        }
        
        if (!empty($allowed_roles) && !in_array($_SESSION['user_role'], $allowed_roles)) {
            $this->redirect('dashboard', 'index', 'error=unauthorized');
        }
        
        return true;
    }
    
    // protected function render($view, $data = []) {
    //     extract($data);
        
    //     // Check if view exists
    //     $view_file = "views/{$view}.php";
    //     if (!file_exists($view_file)) {
    //         throw new Exception("View not found: {$view}");
    //     }
        
    //     require_once 'views/layouts/header.php';
    //     require_once $view_file;
    //     require_once 'views/layouts/footer.php';
    // }

    // Base_controller.php modification for Full Page Layouts
    protected function render($view, $data = [], $use_layout = true) {
        extract($data);
        $view_file = "views/{$view}.php";
        
        if (!file_exists($view_file)) {
            throw new Exception("View not found: {$view}");
        }
        
        if ($use_layout) {
            require_once 'views/layouts/header.php';
        }
        
        require_once $view_file;
        
        if ($use_layout) {
            require_once 'views/layouts/footer.php';
        }
    }
    
    protected function redirect($controller, $action = 'index', $params = '') {
        $url = "?controller={$controller}&action={$action}";
        if ($params) {
            $url .= "&{$params}";
        }
        header("Location: {$url}");
        exit;
    }
    
    protected function jsonResponse($data) {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
    
    protected function logActivity($action, $details = '') {
        if (isset($_SESSION['user_id'])) {
            $activityModel = new ActivityModel($this->db);
            $activityModel->log(
                $_SESSION['user_id'],
                $action,
                $details,
                $_SERVER['REMOTE_ADDR'],
                $_SERVER['HTTP_USER_AGENT'] ?? ''
            );
        }
    }
    
    // Utility function to check permissions
    protected function canPerformAction($required_role) {
        if (!isset($_SESSION['user_role'])) {
            return false;
        }
        
        $role_hierarchy = [
            'viewer' => 1,
            'admin' => 2,
            'super_admin' => 3
        ];
        
        $user_level = $role_hierarchy[$_SESSION['user_role']] ?? 0;
        $required_level = $role_hierarchy[$required_role] ?? 0;
        
        return $user_level >= $required_level;
    }
    
    // Sanitize input data
    protected function sanitize($data) {
        if (is_array($data)) {
            return array_map([$this, 'sanitize'], $data);
        }
        return htmlspecialchars(strip_tags(trim($data)));
    }
    
    // Validate email
    protected function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    // Validate phone number (basic validation)
    protected function validatePhone($phone) {
        return preg_match('/^[0-9]{10,15}$/', $phone);
    }
    
    // Generate random password
    protected function generatePassword($length = 8) {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%&*_";
        return substr(str_shuffle($chars), 0, $length);
    }
    
    // Handle file upload
    protected function handleFileUpload($file, $allowed_types = ['jpg', 'jpeg', 'png', 'pdf'], $max_size = 5242880) {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'message' => 'File upload error'];
        }
        
        // Check file size
        if ($file['size'] > $max_size) {
            return ['success' => false, 'message' => 'File too large'];
        }
        
        // Check file type
        $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($file_ext, $allowed_types)) {
            return ['success' => false, 'message' => 'Invalid file type'];
        }
        
        // Generate unique filename
        $filename = uniqid() . '_' . time() . '.' . $file_ext;
        $upload_path = UPLOAD_PATH . $filename;
        
        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $upload_path)) {
            return ['success' => true, 'filename' => $filename, 'path' => '/uploads/' . $filename];
        } else {
            return ['success' => false, 'message' => 'Failed to move uploaded file'];
        }
    }
}
?>