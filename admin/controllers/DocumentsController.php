<?php
class DocumentsController extends BaseController {
    public function __construct() {
        parent::__construct('Document');
    }

    public function repository() {
        $this->checkAuth([ROLE_SUPER_ADMIN, ROLE_ADMIN, ROLE_VIEWER]);
        
        $filters = [
            'document_type' => $_GET['document_type'] ?? '',
            'status' => $_GET['status'] ?? '',
            'application_id' => $_GET['application_id'] ?? ''
        ];
        
        try {
            $documents = $this->model->getAllDocuments($filters);
            $stats = $this->model->getDocumentStats();
            
            $this->render('documents/repository', [
                'documents' => $documents,
                'filters' => $filters,
                'stats' => $stats
            ]);
        } catch (Exception $e) {
            error_log("DocumentsController repository error: " . $e->getMessage());
            $this->redirect('dashboard', 'index', 'error=documents_load_failed');
        }
    }
}
?>