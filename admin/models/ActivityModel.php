<?php
class ActivityModel {
    private $conn;
    private $table_name = "admin_activity_log";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function log($admin_id, $action, $details = '', $ip_address = '', $user_agent = '') {
        $query = "INSERT INTO " . $this->table_name . " 
                 (admin_id, action, details, ip_address, user_agent) 
                 VALUES (?, ?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$admin_id, $action, $details, $ip_address, $user_agent]);
    }

    public function getAll($limit = 1000) {
        $query = "SELECT al.*, au.username 
                 FROM " . $this->table_name . " al 
                 LEFT JOIN admin_users au ON al.admin_id = au.id 
                 ORDER BY al.created_at DESC 
                 LIMIT ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$limit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>