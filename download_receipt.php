<?php
require_once 'config.php';

$applicationId = $_GET['application_id'] ?? '';

if (!$applicationId) {
    header('Location: index.php');
    exit;
}

// Get application data
try {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT * FROM applications WHERE application_id = ?");
    $stmt->execute([$applicationId]);
    $application = $stmt->fetch();
} catch (Exception $e) {
    die("Error fetching application data");
}

// Generate receipt
header('Content-Type: text/html');
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Application Receipt - <?php echo $applicationId; ?></title>
    <style>
        body { font-family: Arial, sans-serif; padding: 40px; line-height: 1.6; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 3px solid #3E4095; padding-bottom: 20px; }
        .header h1 { color: #3E4095; margin: 0; }
        .content { margin: 20px 0; }
        .footer { margin-top: 40px; padding-top: 20px; border-top: 2px solid #ddd; text-align: center; color: #666; font-size: 12px; }
        .status-box { background: #e3f2fd; border: 2px solid #2196F3; padding: 15px; margin: 20px 0; border-radius: 5px; }
        .document-list { margin: 20px 0; }
        .document-item { padding: 10px; background: #f8f9fa; margin: 5px 0; border-radius: 4px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Employee UAN Onboarding</h1>
        <p>Piramal & Abbott Employee Onboarding System</p>
    </div>
    
    <h2 style="text-align: center; color: #4CAF50;">Application Receipt</h2>
    
    <div class="status-box">
        <strong>Application ID:</strong> <?php echo htmlspecialchars($applicationId); ?><br>
        <strong>Applicant Name:</strong> <?php echo htmlspecialchars($application['full_name']); ?><br>
        <strong>Email:</strong> <?php echo htmlspecialchars($application['email']); ?><br>
        <strong>Phone:</strong> <?php echo htmlspecialchars($application['phone']); ?><br>
        <strong>Status:</strong> <?php echo ucfirst(str_replace('_', ' ', $application['admin_status'])); ?><br>
        <strong>Submission Date:</strong> <?php echo date('d M Y, h:i A', strtotime($application['created_at'])); ?><br>
        <strong>Last Updated:</strong> <?php echo date('d M Y, h:i A', strtotime($application['updated_at'])); ?>
    </div>

    <div class="document-list">
        <h3>Uploaded Documents</h3>
        <?php
        $documents = [
            'PAN Card' => $application['pan_card'],
            'Aadhar Card' => $application['aadhar_card'],
            'Cancelled Cheque' => $application['cancelled_cheque'],
            'Acknowledge Document' => $application['acknowledge_doc']
        ];
        
        foreach ($documents as $docName => $docFile) {
            echo '<div class="document-item">';
            echo '<strong>' . $docName . ':</strong> ';
            echo $docFile ? '<span style="color: green;">Uploaded</span>' : '<span style="color: orange;">Pending</span>';
            echo '</div>';
        }
        ?>
    </div>
    
    <div class="footer">
        <p>© <?php echo date('Y'); ?> Karma Management Global Consulting Solutions Pvt Ltd.</p>
        <p>This is an computer generated receipt. No signature required.</p>
    </div>

    <script>
        window.print();
    </script>
</body>
</html>