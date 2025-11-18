<?php
require_once 'config.php';

$applicationId = $_GET['application_id'] ?? '';
if (!$applicationId) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Complete - Employee Registration</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.8/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-blue: #3E4095;
            --accent-orange: #F26B35;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #3E4095 0%, #2d2f70 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }

        .complete-container {
            background: white;
            border-radius: 15px;
            padding: 40px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            max-width: 600px;
            margin: 20px auto;
            text-align: center;
        }

        .success-icon {
            font-size: 4rem;
            color: #28a745;
            margin-bottom: 20px;
        }

        .application-info {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin: 25px 0;
        }

        .btn-primary {
            background: var(--primary-blue);
            border-color: var(--primary-blue);
        }

        .btn-primary:hover {
            background: #2d2f70;
            border-color: #2d2f70;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="complete-container">
            <div class="success-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            
            <h2 class="text-success mb-3">Application Completed Successfully!</h2>
            <p class="text-muted mb-4">Your employee registration application has been submitted successfully. You will receive a confirmation email shortly.</p>
            
            <div class="application-info">
                <h5 class="text-primary mb-3">Application Details</h5>
                <div class="row text-start">
                    <div class="col-md-6">
                        <strong>Application ID:</strong>
                    </div>
                    <div class="col-md-6">
                        <?php echo htmlspecialchars($applicationId); ?>
                    </div>
                    
                    <div class="col-md-6 mt-2">
                        <strong>Status:</strong>
                    </div>
                    <div class="col-md-6 mt-2">
                        <span class="badge bg-success">Under Review</span>
                    </div>
                    
                    <div class="col-md-6 mt-2">
                        <strong>Submission Date:</strong>
                    </div>
                    <div class="col-md-6 mt-2">
                        <?php echo date('d M Y, h:i A'); ?>
                    </div>
                    
                    <div class="col-md-6 mt-2">
                        <strong>Processing Time:</strong>
                    </div>
                    <div class="col-md-6 mt-2">
                        2-3 business days
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <button class="btn btn-primary me-2" onclick="downloadReceipt()">
                    <i class="fas fa-download me-1"></i>Download Receipt
                </button>
                <button class="btn btn-outline-primary me-2" onclick="trackApplication()">
                    <i class="fas fa-search me-1"></i>Track Application
                </button>
                <a href="index.php" class="btn btn-outline-secondary">
                    <i class="fas fa-home me-1"></i>Back to Home
                </a>
            </div>

            <div class="mt-4 text-center">
                <small class="text-muted">
                    <i class="fas fa-envelope me-1"></i>You will receive email updates on your application status.<br>
                    <i class="fas fa-phone me-1 mt-2"></i>For queries: support@example.com | +91 12345 67890
                </small>
            </div>
        </div>
    </div>

    <script>
        function downloadReceipt() {
            // Implement receipt download
            alert('Receipt download functionality would be implemented here');
        }

        function trackApplication() {
            // Implement application tracking
            alert('Application tracking would be implemented here');
        }
    </script>
</body>
</html>