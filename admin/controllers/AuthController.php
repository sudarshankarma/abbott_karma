<?php
class AuthController extends BaseController {
    public function __construct() {
        parent::__construct('User');
    }

    public function login() {
        // If already logged in, redirect to dashboard
        if (isset($_SESSION['user_id'])) {
            $this->redirect('dashboard');
        }

        $error = '';
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $username = $this->sanitize($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';
            
            $user = $this->model->getByUsername($username);
            
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['user_role'] = $user['role'];
                $_SESSION['LAST_ACTIVITY'] = time();
                
                $this->model->updateLastLogin($user['id']);
                $this->logActivity('Login', 'User logged in successfully');
                
                $this->redirect('dashboard');
            } else {
                $error = 'Invalid username or password!';
                $this->logActivity('Failed Login', "Failed login attempt for username: {$username}");
            }
        }
        
        $this->render('auth/login', ['error' => $error]);
    }

    public function logout() {
        $this->logActivity('Logout', 'User logged out');
        session_unset();
        session_destroy();
        $this->redirect('auth', 'login', 'logout=1');
    }
}
?>