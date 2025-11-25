<?php /*
class DocumentModel {
    private $conn;
    private $table_name = "applications";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAllDocuments($filters = []) {
        $documents = [];
        
        // Get applications based on filters
        $query = "SELECT * FROM " . $this->table_name . " WHERE 1=1";
        $params = [];

        if (!empty($filters['application_id'])) {
            $query .= " AND application_id LIKE ?";
            $params[] = "%{$filters['application_id']}%";
        }

        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        $applications = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Convert to document-centric view
        foreach ($applications as $app) {
            $documents[] = [
                'application_id' => $app['application_id'],
                'applicant_name' => $app['full_name'],
                'type' => 'pan',
                'file_path' => $app['pan_card'],
                'status' => $app['pan_status'],
                'submitted' => $app['created_at']
            ];

            $documents[] = [
                'application_id' => $app['application_id'],
                'applicant_name' => $app['full_name'],
                'type' => 'aadhar',
                'file_path' => $app['aadhar_card'],
                'status' => $app['aadhar_status'],
                'submitted' => $app['created_at']
            ];

            $documents[] = [
                'application_id' => $app['application_id'],
                'applicant_name' => $app['full_name'],
                'type' => 'cheque',
                'file_path' => $app['cancelled_cheque'],
                'status' => $app['cheque_status'],
                'submitted' => $app['created_at']
            ];
        }

        // Apply additional filters
        if (!empty($filters['document_type'])) {
            $documents = array_filter($documents, function($doc) use ($filters) {
                return $doc['type'] == $filters['document_type'];
            });
        }

        if (!empty($filters['status'])) {
            $documents = array_filter($documents, function($doc) use ($filters) {
                if ($filters['status'] == 'unclassified') {
                    return !in_array($doc['status'], ['approved', 'rejected', 'pending']);
                }
                return $doc['status'] == $filters['status'];
            });
        }

        return $documents;
    }

    public function getDocumentStats() {
        $stats = [];
        
        $query = "SELECT 
            SUM(pan_status = 'approved') as pan_approved,
            SUM(pan_status = 'pending') as pan_pending,
            SUM(pan_status = 'rejected') as pan_rejected,
            SUM(aadhar_status = 'approved') as aadhar_approved,
            SUM(aadhar_status = 'pending') as aadhar_pending,
            SUM(aadhar_status = 'rejected') as aadhar_rejected,
            SUM(cheque_status = 'approved') as cheque_approved,
            SUM(cheque_status = 'pending') as cheque_pending,
            SUM(cheque_status = 'rejected') as cheque_rejected
        FROM " . $this->table_name;
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}*/
?>

<?php
class DocumentModel {
    private $conn;
    private $table_name = "applications";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAllDocuments($filters = []) {
        $documents = [];
        
        // Get applications based on filters
        $query = "SELECT * FROM " . $this->table_name . " WHERE 1=1";
        $params = [];

        if (!empty($filters['application_id'])) {
            $query .= " AND application_id LIKE ?";
            $params[] = "%{$filters['application_id']}%";
        }

        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        $applications = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Convert to document-centric view
        foreach ($applications as $app) {
            $documents[] = [
                'application_id' => $app['application_id'],
                'applicant_name' => $app['full_name'],
                'type' => 'pan',
                'file_path' => $app['pan_card'],
                'status' => $app['pan_status'],
                'submitted' => $app['created_at']
            ];

            $documents[] = [
                'application_id' => $app['application_id'],
                'applicant_name' => $app['full_name'],
                'type' => 'aadhar',
                'file_path' => $app['aadhar_card'],
                'status' => $app['aadhar_status'],
                'submitted' => $app['created_at']
            ];

            $documents[] = [
                'application_id' => $app['application_id'],
                'applicant_name' => $app['full_name'],
                'type' => 'cheque',
                'file_path' => $app['cancelled_cheque'],
                'status' => $app['cheque_status'],
                'submitted' => $app['created_at']
            ];
        }

        // Apply additional filters
        if (!empty($filters['document_type'])) {
            $documents = array_filter($documents, function($doc) use ($filters) {
                return $doc['type'] == $filters['document_type'];
            });
        }

        if (!empty($filters['status'])) {
            $documents = array_filter($documents, function($doc) use ($filters) {
                if ($filters['status'] == 'unclassified') {
                    return !in_array($doc['status'], ['approved', 'rejected', 'pending']);
                }
                return $doc['status'] == $filters['status'];
            });
        }

        return $documents;
    }

    public function getDocumentStats() {
        $stats = [];
        
        $query = "SELECT 
            SUM(pan_status = 'approved') as pan_approved,
            SUM(pan_status = 'pending') as pan_pending,
            SUM(pan_status = 'rejected') as pan_rejected,
            SUM(aadhar_status = 'approved') as aadhar_approved,
            SUM(aadhar_status = 'pending') as aadhar_pending,
            SUM(aadhar_status = 'rejected') as aadhar_rejected,
            SUM(cheque_status = 'approved') as cheque_approved,
            SUM(cheque_status = 'pending') as cheque_pending,
            SUM(cheque_status = 'rejected') as cheque_rejected,
            COUNT(*) as total_documents,
            (SUM(pan_status = 'approved') + SUM(aadhar_status = 'approved') + SUM(cheque_status = 'approved')) as total_approved,
            (SUM(pan_status = 'pending') + SUM(aadhar_status = 'pending') + SUM(cheque_status = 'pending')) as total_pending,
            (SUM(pan_status = 'rejected') + SUM(aadhar_status = 'rejected') + SUM(cheque_status = 'rejected')) as total_rejected
        FROM " . $this->table_name;
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Calculate approval rate
        $total_processed = $result['total_approved'] + $result['total_rejected'];
        $total_documents = $result['total_approved'] + $result['total_pending'] + $result['total_rejected'];
        
        $result['approval_rate'] = $total_processed > 0 ? 
            round(($result['total_approved'] / $total_processed) * 100) : 0;
        
        return $result;
    }

    public function getDocumentTypeStats() {
        $query = "SELECT 
            COUNT(*) as pan_total,
            SUM(pan_status = 'approved') as pan_approved,
            SUM(pan_status = 'pending') as pan_pending,
            SUM(pan_status = 'rejected') as pan_rejected,
            
            COUNT(*) as aadhar_total,
            SUM(aadhar_status = 'approved') as aadhar_approved,
            SUM(aadhar_status = 'pending') as aadhar_pending,
            SUM(aadhar_status = 'rejected') as aadhar_rejected,
            
            COUNT(*) as cheque_total,
            SUM(cheque_status = 'approved') as cheque_approved,
            SUM(cheque_status = 'pending') as cheque_pending,
            SUM(cheque_status = 'rejected') as cheque_rejected
        FROM " . $this->table_name;
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return [
            'pan_total' => $result['pan_total'] ?? 0,
            'pan_approved' => $result['pan_approved'] ?? 0,
            'pan_pending' => $result['pan_pending'] ?? 0,
            'pan_rejected' => $result['pan_rejected'] ?? 0,
            
            'aadhar_total' => $result['aadhar_total'] ?? 0,
            'aadhar_approved' => $result['aadhar_approved'] ?? 0,
            'aadhar_pending' => $result['aadhar_pending'] ?? 0,
            'aadhar_rejected' => $result['aadhar_rejected'] ?? 0,
            
            'cheque_total' => $result['cheque_total'] ?? 0,
            'cheque_approved' => $result['cheque_approved'] ?? 0,
            'cheque_pending' => $result['cheque_pending'] ?? 0,
            'cheque_rejected' => $result['cheque_rejected'] ?? 0
        ];
    }

    public function getDocumentTrends($period = '30 days') {
        $dateCondition = "";
        switch ($period) {
            case '7 days':
                $dateCondition = " AND created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
                break;
            case '90 days':
                $dateCondition = " AND created_at >= DATE_SUB(NOW(), INTERVAL 90 DAY)";
                break;
            default: // 30 days
                $dateCondition = " AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
        }
        
        $query = "SELECT 
            DATE(created_at) as date,
            COUNT(*) as total_documents,
            SUM(pan_status = 'approved' OR aadhar_status = 'approved' OR cheque_status = 'approved') as approved_documents,
            SUM(pan_status = 'pending' OR aadhar_status = 'pending' OR cheque_status = 'pending') as pending_documents,
            SUM(pan_status = 'rejected' OR aadhar_status = 'rejected' OR cheque_status = 'rejected') as rejected_documents
        FROM " . $this->table_name . " 
        WHERE 1=1 " . $dateCondition . "
        GROUP BY DATE(created_at)
        ORDER BY date DESC
        LIMIT 30";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getDocumentStatusDistribution() {
        $query = "SELECT 
            'PAN' as document_type,
            pan_status as status,
            COUNT(*) as count
        FROM " . $this->table_name . "
        GROUP BY pan_status
        
        UNION ALL
        
        SELECT 
            'Aadhar' as document_type,
            aadhar_status as status,
            COUNT(*) as count
        FROM " . $this->table_name . "
        GROUP BY aadhar_status
        
        UNION ALL
        
        SELECT 
            'Cheque' as document_type,
            cheque_status as status,
            COUNT(*) as count
        FROM " . $this->table_name . "
        GROUP BY cheque_status";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getRecentDocumentActivity($limit = 10) {
        $query = "SELECT 
            application_id,
            full_name as applicant_name,
            'PAN' as document_type,
            pan_status as status,
            pan_card as file_path,
            created_at,
            updated_at
        FROM " . $this->table_name . "
        WHERE pan_card IS NOT NULL
        
        UNION ALL
        
        SELECT 
            application_id,
            full_name as applicant_name,
            'Aadhar' as document_type,
            aadhar_status as status,
            aadhar_card as file_path,
            created_at,
            updated_at
        FROM " . $this->table_name . "
        WHERE aadhar_card IS NOT NULL
        
        UNION ALL
        
        SELECT 
            application_id,
            full_name as applicant_name,
            'Cheque' as document_type,
            cheque_status as status,
            cancelled_cheque as file_path,
            created_at,
            updated_at
        FROM " . $this->table_name . "
        WHERE cancelled_cheque IS NOT NULL
        
        ORDER BY updated_at DESC, created_at DESC
        LIMIT ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getDocumentApprovalRates() {
        $query = "SELECT 
            'PAN' as document_type,
            COUNT(*) as total,
            SUM(pan_status = 'approved') as approved,
            SUM(pan_status = 'rejected') as rejected,
            SUM(pan_status = 'pending') as pending,
            ROUND((SUM(pan_status = 'approved') / COUNT(*) * 100), 2) as approval_rate
        FROM " . $this->table_name . "
        
        UNION ALL
        
        SELECT 
            'Aadhar' as document_type,
            COUNT(*) as total,
            SUM(aadhar_status = 'approved') as approved,
            SUM(aadhar_status = 'rejected') as rejected,
            SUM(aadhar_status = 'pending') as pending,
            ROUND((SUM(aadhar_status = 'approved') / COUNT(*) * 100), 2) as approval_rate
        FROM " . $this->table_name . "
        
        UNION ALL
        
        SELECT 
            'Cheque' as document_type,
            COUNT(*) as total,
            SUM(cheque_status = 'approved') as approved,
            SUM(cheque_status = 'rejected') as rejected,
            SUM(cheque_status = 'pending') as pending,
            ROUND((SUM(cheque_status = 'approved') / COUNT(*) * 100), 2) as approval_rate
        FROM " . $this->table_name;
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPendingDocumentCount() {
        $query = "SELECT 
            SUM(pan_status = 'pending') as pan_pending,
            SUM(aadhar_status = 'pending') as aadhar_pending,
            SUM(cheque_status = 'pending') as cheque_pending,
            (SUM(pan_status = 'pending') + SUM(aadhar_status = 'pending') + SUM(cheque_status = 'pending')) as total_pending
        FROM " . $this->table_name;
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getDocumentStatsByDateRange($startDate, $endDate) {
        $query = "SELECT 
            COUNT(*) as total_documents,
            SUM(pan_status = 'approved' OR aadhar_status = 'approved' OR cheque_status = 'approved') as approved,
            SUM(pan_status = 'pending' OR aadhar_status = 'pending' OR cheque_status = 'pending') as pending,
            SUM(pan_status = 'rejected' OR aadhar_status = 'rejected' OR cheque_status = 'rejected') as rejected,
            DATE(created_at) as date
        FROM " . $this->table_name . "
        WHERE created_at BETWEEN ? AND ?
        GROUP BY DATE(created_at)
        ORDER BY date";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$startDate, $endDate]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>