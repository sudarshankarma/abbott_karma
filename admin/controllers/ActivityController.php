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
}
?>