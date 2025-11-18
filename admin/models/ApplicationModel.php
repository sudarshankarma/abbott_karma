<?php
class ApplicationModel {
    private $conn;
    private $table_name = "applications";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll($filters = []) {
        try {
            $query = "SELECT * FROM " . $this->table_name . " WHERE 1=1";
            $params = [];

            if (!empty($filters['search'])) {
                $query .= " AND (application_id LIKE ? OR full_name LIKE ? OR email LIKE ?)";
                $searchTerm = "%{$filters['search']}%";
                $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm]);
            }

            if (!empty($filters['status'])) {
                $query .= " AND admin_status = ?";
                $params[] = $filters['status'];
            }

            $query .= " ORDER BY created_at DESC";

            if (!empty($filters['limit'])) {
                $query .= " LIMIT " . (int)$filters['limit'];
            }

            $stmt = $this->conn->prepare($query);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("ApplicationModel getAll error: " . $e->getMessage());
            return [];
        }
    }

    public function getById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getByApplicationId($app_id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE application_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$app_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getStats() {
        $stats = [];
        
        // Total applications
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $stats['total'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        // Status counts
        $query = "SELECT admin_status, COUNT(*) as count FROM " . $this->table_name . " GROUP BY admin_status";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $status_counts = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($status_counts as $row) {
            $stats[$row['admin_status']] = $row['count'];
        }

        // Today's applications
        $query = "SELECT COUNT(*) as count FROM " . $this->table_name . " WHERE DATE(created_at) = CURDATE()";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $stats['today'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

        return $stats;
    }

    public function updateDocumentStatus($app_id, $doc_type, $status) {
        $column = $doc_type . '_status';
        $query = "UPDATE " . $this->table_name . " SET {$column} = ?, admin_reviewer_id = ?, document_reviewed_at = NOW() WHERE application_id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$status, $_SESSION['user_id'], $app_id]);
    }

    public function update($id, $data) {
        $fields = [];
        $params = [];
        
        foreach ($data as $key => $value) {
            $fields[] = "{$key} = ?";
            $params[] = $value;
        }
        
        $params[] = $id;
        $query = "UPDATE " . $this->table_name . " SET " . implode(', ', $fields) . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute($params);
    }
}
?>