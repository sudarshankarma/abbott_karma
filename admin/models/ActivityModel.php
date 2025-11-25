<?php
class ActivityModel {
    private $conn;
    private $table_name = "admin_activity_log";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function log($admin_id, $action, $details = '', $ip_address = '', $user_agent = '') {
        try {
            $query = "INSERT INTO " . $this->table_name . " 
                     (admin_id, action, details, ip_address, user_agent) 
                     VALUES (?, ?, ?, ?, ?)";
            
            $stmt = $this->conn->prepare($query);
            $result = $stmt->execute([$admin_id, $action, $details, $ip_address, $user_agent]);
            
            if (!$result) {
                error_log("Activity log insert failed: " . implode(", ", $stmt->errorInfo()));
            }
            
            return $result;
        } catch (PDOException $e) {
            error_log("ActivityModel log error: " . $e->getMessage());
            return false;
        }
    }

    public function getAll($limit = 1000) {
        try {
            $query = "SELECT al.*, au.username 
                     FROM " . $this->table_name . " al 
                     LEFT JOIN admin_users au ON al.admin_id = au.id 
                     ORDER BY al.created_at DESC LIMIT " . (int)$limit;
            
            $stmt = $this->conn->prepare($query);
            
           $stmt->execute();
          // print_r($stmt->fetchAll(PDO::FETCH_ASSOC)); die();

           return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("ActivityModel getAll error: " . $e->getMessage());
            return [];
        }
    }

    // New method for server-side processing (optional for large datasets)
    public function getForDataTables($start, $length, $search, $orderColumn, $orderDir) {
        try {
            // Base query
            $query = "SELECT SQL_CALC_FOUND_ROWS al.*, au.username 
                     FROM " . $this->table_name . " al 
                     LEFT JOIN admin_users au ON al.admin_id = au.id";
            
            // Search condition
            $searchCondition = '';
            $params = [];
            
            if (!empty($search)) {
                $searchCondition = " WHERE (al.action LIKE ? OR al.details LIKE ? OR au.username LIKE ? OR al.ip_address LIKE ?)";
                $searchTerm = "%{$search}%";
                $params = array_fill(0, 4, $searchTerm);
            }
            
            // Order by
            $order = " ORDER BY {$orderColumn} {$orderDir}";
            
            // Limit
            $limit = " LIMIT ?, ?";
            $params[] = $start;
            $params[] = $length;
            
            $fullQuery = $query . $searchCondition . $order . $limit;
            
            $stmt = $this->conn->prepare($fullQuery);
            $stmt->execute($params);
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Get total records
            $totalStmt = $this->conn->prepare("SELECT FOUND_ROWS() as total");
            $totalStmt->execute();
            $total = $totalStmt->fetch(PDO::FETCH_ASSOC)['total'];
            
            return [
                'data' => $data,
                'total' => $total
            ];
            
        } catch (PDOException $e) {
            error_log("ActivityModel getForDataTables error: " . $e->getMessage());
            return ['data' => [], 'total' => 0];
        }
    }
}
?>