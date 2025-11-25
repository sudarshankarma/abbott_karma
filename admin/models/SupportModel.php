<?php
// [file name]: SupportModel.php
class SupportModel {
    private $conn;
    private $table_tickets = "support_tickets";
    private $table_messages = "support_messages";
    private $table_comments = "document_comments";
    private $table_applications = "applications";

    public function __construct($db) {
        $this->conn = $db;
    }

    // public function getAllTickets($filters = []) {
    //     $query = "SELECT st.*, a.full_name, a.application_id 
    //              FROM {$this->table_tickets} st 
    //              LEFT JOIN {$this->table_applications} a ON st.application_id = a.application_id 
    //              WHERE 1=1";
    //     $params = [];

    //     if (!empty($filters['search'])) {
    //         $query .= " AND (a.application_id LIKE ? OR a.full_name LIKE ? OR st.subject LIKE ?)";
    //         $searchTerm = "%{$filters['search']}%";
    //         $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm]);
    //     }

    //     if (!empty($filters['status'])) {
    //         $query .= " AND st.status = ?";
    //         $params[] = $filters['status'];
    //     }

    //     $query .= " ORDER BY st.updated_at DESC";

    //     $stmt = $this->conn->prepare($query);
    //     $stmt->execute($params);
    //     return $stmt->fetchAll(PDO::FETCH_ASSOC);
    // }

    // public function getTicketById($id) {
    //     $query = "SELECT st.*, a.full_name, a.application_id, a.phone, a.email 
    //              FROM {$this->table_tickets} st 
    //              LEFT JOIN {$this->table_applications} a ON st.application_id = a.application_id 
    //              WHERE st.id = ?";
    //     $stmt = $this->conn->prepare($query);
    //     $stmt->execute([$id]);
    //     return $stmt->fetch(PDO::FETCH_ASSOC);
    // }

    // public function getTicketMessages($ticket_id) {
    //     $query = "SELECT * FROM {$this->table_messages} 
    //              WHERE ticket_id = ? 
    //              ORDER BY created_at ASC";
    //     $stmt = $this->conn->prepare($query);
    //     $stmt->execute([$ticket_id]);
    //     return $stmt->fetchAll(PDO::FETCH_ASSOC);
    // }

        public function getAllTickets($filters = []) {
        try {
            $query = "SELECT st.*, a.full_name, a.application_id 
                     FROM support_tickets st 
                     LEFT JOIN applications a ON st.application_id = a.application_id 
                     ORDER BY st.updated_at DESC";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("SupportModel getAllTickets error: " . $e->getMessage());
            return [];
        }
    }

    public function getTicketById($id) {
        try {
            $query = "SELECT st.*, a.full_name, a.application_id, a.phone, a.email 
                     FROM support_tickets st 
                     LEFT JOIN applications a ON st.application_id = a.application_id 
                     WHERE st.id = ?";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("SupportModel getTicketById error: " . $e->getMessage());
            return null;
        }
    }

    public function getTicketMessages($ticket_id) {
        try {
            $query = "SELECT * FROM support_messages 
                     WHERE ticket_id = ? 
                     ORDER BY created_at ASC";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$ticket_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("SupportModel getTicketMessages error: " . $e->getMessage());
            return [];
        }
    }


    public function addAdminMessage($ticket_id, $message, $admin_id) {
        $query = "INSERT INTO {$this->table_messages} (ticket_id, message, sender_type, admin_id) 
                 VALUES (?, ?, 'admin', ?)";
        $stmt = $this->conn->prepare($query);
        
        if ($stmt->execute([$ticket_id, $message, $admin_id])) {
            // Update ticket's updated_at timestamp
            $this->updateTicketTimestamp($ticket_id);
            return true;
        }
        return false;
    }

    public function updateTicketStatus($ticket_id, $status, $admin_id) {
        $query = "UPDATE {$this->table_tickets} 
                 SET status = ?, updated_at = NOW(), admin_id = ? 
                 WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$status, $admin_id, $ticket_id]);
    }

    public function updateTicketTimestamp($ticket_id) {
        $query = "UPDATE {$this->table_tickets} SET updated_at = NOW() WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$ticket_id]);
    }

    public function getApplicationById($app_id) {
        $query = "SELECT * FROM {$this->table_applications} WHERE application_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$app_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getDocumentComments($app_id, $doc_type) {
        $query = "SELECT dc.*, au.username as admin_name 
                 FROM {$this->table_comments} dc 
                 LEFT JOIN admin_users au ON dc.admin_id = au.id 
                 WHERE dc.application_id = ? AND dc.document_type = ? 
                 ORDER BY dc.created_at ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$app_id, $doc_type]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addDocumentComment($app_id, $doc_type, $comment, $admin_id) {
        $query = "INSERT INTO {$this->table_comments} (application_id, document_type, comment, commented_by, admin_id) 
                 VALUES (?, ?, ?, 'admin', ?)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$app_id, $doc_type, $comment, $admin_id]);
    }

    // public function getUnreadCount() {
    //     $query = "SELECT COUNT(*) as unread_count 
    //              FROM {$this->table_messages} 
    //              WHERE sender_type = 'user' AND is_read = 0";
    //     $stmt = $this->conn->prepare($query);
    //     $stmt->execute();
    //     $result = $stmt->fetch(PDO::FETCH_ASSOC);
    //     return $result['unread_count'] ?? 0;
    // }

    public function markMessagesAsRead($ticket_id) {
        $query = "UPDATE {$this->table_messages} 
                 SET is_read = 1 
                 WHERE ticket_id = ? AND sender_type = 'user' AND is_read = 0";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$ticket_id]);
    }
    public function getUnreadCount() {
        $query = "SELECT COUNT(*) as unread_count 
                FROM support_messages 
                WHERE sender_type = 'user' AND (is_read = 0 OR is_read IS NULL)";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['unread_count'] ?? 0;
    }
}
?>