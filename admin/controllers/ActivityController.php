<?php
class ActivityController extends BaseController {
    public function __construct() {
        parent::__construct('Activity');
    }

    public function index() {
        $this->checkAuth([ROLE_SUPER_ADMIN, ROLE_ADMIN]);
        try {
            $activities = $this->model->getAll();
            $this->render('activity/index', ['activities' => $activities]);
        } catch (Exception $e) {
            error_log("ActivityController index error: " . $e->getMessage());
            $this->redirect('dashboard', 'index', 'error=activity_load_failed');
        }
    }

    // AJAX endpoint for server-side processing
    public function ajax() {
        $this->checkAuth([ROLE_SUPER_ADMIN, ROLE_ADMIN]);
        
        header('Content-Type: application/json');
        
        try {
            $start = $_GET['start'] ?? 0;
            $length = $_GET['length'] ?? 25;
            $search = $_GET['search']['value'] ?? '';
            $orderColumn = $_GET['columns'][$_GET['order'][0]['column']]['data'] ?? 'created_at';
            $orderDir = $_GET['order'][0]['dir'] ?? 'desc';
            
            $result = $this->model->getForDataTables($start, $length, $search, $orderColumn, $orderDir);
            
            echo json_encode([
                'draw' => intval($_GET['draw'] ?? 1),
                'recordsTotal' => $result['total'],
                'recordsFiltered' => $result['total'],
                'data' => $result['data']
            ]);
            
        } catch (Exception $e) {
            error_log("ActivityController ajax error: " . $e->getMessage());
            echo json_encode([
                'draw' => intval($_GET['draw'] ?? 1),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => []
            ]);
        }
    }
}
?>