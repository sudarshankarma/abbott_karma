<?php
class ApplicationsController extends BaseController {
    public function __construct() {
        parent::__construct('Application');
    }

    public function index() {
        $this->checkAuth([ROLE_SUPER_ADMIN, ROLE_ADMIN, ROLE_VIEWER]);
        
        $search = $_GET['search'] ?? '';
        $status = $_GET['status'] ?? '';
        
        $applications = $this->model->getAll([
            'search' => $search,
            'status' => $status
        ]);
        
        $this->render('applications/index', [
            'applications' => $applications,
            'search' => $search,
            'status' => $status
        ]);
    }

    public function view($id = null) {
        $this->checkAuth([ROLE_SUPER_ADMIN, ROLE_ADMIN, ROLE_VIEWER]);
        
        // If no ID provided, check URL parameters
        if (!$id) {
            $id = $_GET['id'] ?? $_GET['app_id'] ?? null;
        }
        
        if (!$id) {
            $this->redirect('applications', 'index', 'error=missing_id');
            return;
        }
        
        // Try to get application by ID first, then by application_id
        $application = $this->model->getById($id);
        if (!$application) {
            $application = $this->model->getByApplicationId($id);
        }
        
        if (!$application) {
            $this->redirect('applications', 'index', 'error=not_found');
            return;
        }
        
        $this->render('applications/view', ['application' => $application]);
    }

    public function edit($id = null) {
        $this->checkAuth([ROLE_SUPER_ADMIN, ROLE_ADMIN]);
        
        // If no ID provided, check URL parameters
        if (!$id) {
            $id = $_GET['id'] ?? null;
        }
        
        if (!$id) {
            $this->redirect('applications', 'index', 'error=missing_id');
            return;
        }
        
        $application = $this->model->getById($id);
        if (!$application) {
            $this->redirect('applications', 'index', 'error=not_found');
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = [
                'full_name' => $this->sanitize($_POST['full_name']),
                'email' => $this->sanitize($_POST['email']),
                'phone' => $this->sanitize($_POST['phone']),
                'whatsapp' => $this->sanitize($_POST['whatsapp']),
                'pan_number' => $this->sanitize($_POST['pan_number']),
                'aadhar_number' => $this->sanitize($_POST['aadhar_number']),
                'piramal_uan' => $this->sanitize($_POST['piramal_uan']),
                'abbott_uan' => $this->sanitize($_POST['abbott_uan']),
                'piramal_id' => $this->sanitize($_POST['piramal_id']),
                'abbott_id' => $this->sanitize($_POST['abbott_id']),
                'admin_status' => $this->sanitize($_POST['admin_status']),
                'admin_notes' => $this->sanitize($_POST['admin_notes']),
                'updated_at' => date('Y-m-d H:i:s')
            ];
            
            if ($this->model->update($id, $data)) {
                $this->logActivity('Application updated', "Application ID: {$application['application_id']}");
                $this->redirect('applications', 'view', "id={$id}&success=updated");
            } else {
                $this->redirect('applications', 'edit', "id={$id}&error=update_failed");
            }
        }
        
        $this->render('applications/edit', ['application' => $application]);
    }

    public function updateDocument() {
        $this->checkAuth([ROLE_SUPER_ADMIN, ROLE_ADMIN]);
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $app_id = $this->sanitize($_POST['application_id']);
            $doc_type = $this->sanitize($_POST['document_type']);
            $status = $this->sanitize($_POST['status']);
            
            if ($this->model->updateDocumentStatus($app_id, $doc_type, $status)) {
                $this->logActivity("Document {$status}", "{$doc_type} for application: {$app_id}");
                $this->jsonResponse(['success' => true, 'message' => 'Document status updated']);
            } else {
                $this->jsonResponse(['success' => false, 'message' => 'Update failed']);
            }
        }
    }
}
?>