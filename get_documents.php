<?php
require_once 'config.php';

header('Content-Type: application/json');

try {
    if (empty($_POST['application_id'])) {
        throw new Exception("Application ID is required");
    }

    $applicationId = $_POST['application_id'];
    $pdo = getDBConnection();

    $stmt = $pdo->prepare("SELECT pan_card, aadhar_card, cancelled_cheque FROM applications WHERE application_id = ?");
    $stmt->execute([$applicationId]);
    $application = $stmt->fetch();

    $documents = [];
    
    if ($application['pan_card']) {
        $documents[] = [
            'document_type' => 'PAN Card',
            'filename' => $application['pan_card'],
            'type' => pathinfo($application['pan_card'], PATHINFO_EXTENSION)
        ];
    }
    
    if ($application['aadhar_card']) {
        $documents[] = [
            'document_type' => 'Aadhar Card',
            'filename' => $application['aadhar_card'],
            'type' => pathinfo($application['aadhar_card'], PATHINFO_EXTENSION)
        ];
    }
    
    if ($application['cancelled_cheque']) {
        $documents[] = [
            'document_type' => 'Cancelled Cheque',
            'filename' => $application['cancelled_cheque'],
            'type' => pathinfo($application['cancelled_cheque'], PATHINFO_EXTENSION)
        ];
    }

    echo json_encode([
        'success' => true,
        'documents' => $documents
    ]);

} catch (Exception $e) {
    error_log("Get documents error: " . $e->getMessage());
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>