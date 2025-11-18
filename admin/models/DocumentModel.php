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
            SUM(cheque_status = 'rejected') as cheque_rejected
        FROM " . $this->table_name;
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>