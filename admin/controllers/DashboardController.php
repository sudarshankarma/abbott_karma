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
            
            // Get unread support conversations count
            $stmt = $this->db->prepare("
                SELECT COUNT(DISTINCT m.ticket_id) AS unread_count
                FROM support_messages m
                WHERE m.is_read = 0 AND m.sender_type = 'user'
            ");
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $support_unread_count = (int)($row['unread_count'] ?? 0);

            $this->render('dashboard/index', [
                'app_stats' => $app_stats,
                'doc_stats' => $doc_stats,
                'recent_apps' => $recent_apps,
                'support_unread_count' => $support_unread_count
            ]);
        } catch (Exception $e) {
            error_log("DashboardController index error: " . $e->getMessage());
            // Render dashboard with empty data if stats fail
            $this->render('dashboard/index', [
                'app_stats' => [],
                'doc_stats' => [],
                'recent_apps' => [],
                'support_unread_count' => 0
            ]);
        }
    }
}
?>