<?php
class DashboardController extends BaseController {
    public function __construct() {
        parent::__construct('Application');
    }

    public function index() {
        $this->checkAuth([ROLE_SUPER_ADMIN, ROLE_ADMIN, ROLE_VIEWER]);
        
        try {
            // Get application statistics
            $app_stats = $this->model->getStats();
            
            // Get document statistics
            $doc_model = new DocumentModel($this->db);
            $doc_stats = $doc_model->getDocumentStats();
            
            // Get recent applications
            $recent_apps = $this->model->getAll(['limit' => 6]);
            
            $this->render('dashboard/index', [
                'app_stats' => $app_stats,
                'doc_stats' => $doc_stats,
                'recent_apps' => $recent_apps
            ]);
        } catch (Exception $e) {
            error_log("DashboardController index error: " . $e->getMessage());
            // Render dashboard even if stats fail
            $this->render('dashboard/index', [
                'app_stats' => [],
                'doc_stats' => [],
                'recent_apps' => []
            ]);
        }
    }
}
?>