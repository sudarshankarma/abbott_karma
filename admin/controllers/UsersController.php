<?php
class UsersController extends BaseController {
    public function __construct() {
        parent::__construct('User');
    }

    public function index() {
        $this->checkAuth([ROLE_SUPER_ADMIN]);
        
        try {
            $users = $this->model->getAll();
            
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $action = $this->sanitize($_POST['action'] ?? '');
                
                if ($action == 'create') {
                    $this->createUser($_POST);
                } elseif ($action == 'update') {
                    $this->updateUser($_POST);
                } elseif ($action == 'delete') {
                    $this->deleteUser($this->sanitize($_POST['id']));
                }
            }
            
            $this->render('users/index', ['users' => $users]);
        } catch (Exception $e) {
            error_log("UsersController index error: " . $e->getMessage());
            $this->redirect('dashboard', 'index', 'error=users_load_failed');
        }
    }

    private function createUser($data) {
        $user_data = [
            'username' => $this->sanitize($data['username']),
            'email' => $this->sanitize($data['email']),
            'password' => $this->generatePassword(),
            'role' => $this->sanitize($data['role']),
            'is_active' => isset($data['is_active']) ? 1 : 0
        ];
        
        if ($this->model->create($user_data)) {
            $this->logActivity('User created', "New user: {$user_data['username']}");
            $this->redirect('users', 'index', 'success=user_created');
        } else {
            $this->redirect('users', 'index', 'error=user_create_failed');
        }
    }

    private function updateUser($data) {
        $id = $this->sanitize($data['id']);
        $update_data = [
            'username' => $this->sanitize($data['username']),
            'email' => $this->sanitize($data['email']),
            'role' => $this->sanitize($data['role']),
            'is_active' => isset($data['is_active']) ? 1 : 0,
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        // Update password if provided
        if (!empty($data['password'])) {
            $update_data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        
        if ($this->model->update($id, $update_data)) {
            $this->logActivity('User updated', "User ID: {$id}");
            $this->redirect('users', 'index', 'success=user_updated');
        } else {
            $this->redirect('users', 'index', 'error=user_update_failed');
        }
    }

    private function deleteUser($id) {
        if ($id == $_SESSION['user_id']) {
            $this->redirect('users', 'index', 'error=cannot_delete_self');
        }
        
        if ($this->model->delete($id)) {
            $this->logActivity('User deleted', "User ID: {$id}");
            $this->redirect('users', 'index', 'success=user_deleted');
        } else {
            $this->redirect('users', 'index', 'error=user_delete_failed');
        }
    }
}
?>