<?php
require_once 'config.php';

// Check if consent is given
if (!isset($_SESSION['consent_given']) || $_SESSION['consent_given'] !== true) {
    header('Location: index.php');
    exit;
}

$phone = $_GET['phone'] ?? '';
$error = $_GET['error'] ?? '';
$success = $_GET['success'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Phone - Employee Registration</title>
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

        .verify-container {
            background: white;
            border-radius: 15px;
            padding: 40px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            max-width: 500px;
            margin: 20px auto;
        }

        .logo-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo-container {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 40px;
            margin-bottom: 20px;
        }

        .logo-placeholder {
            width: 120px;
            height: 60px;
            background-size: contain;
            background-repeat: no-repeat;
            background-position: center;
            border: 1px solid #ddd;
            background-color: #f8f9fa;
        }

        .page-title {
            color: var(--primary-blue);
            font-weight: 700;
            margin-bottom: 10px;
        }

        .page-subtitle {
            color: #666;
            margin-bottom: 30px;
        }

        .btn-primary {
            background: var(--primary-blue);
            border-color: var(--primary-blue);
        }

        .btn-primary:hover {
            background: #2d2f70;
            border-color: #2d2f70;
        }

        .verification-info {
            background: #e3f2fd;
            border-left: 4px solid var(--primary-blue);
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }

        .otp-input {
            font-size: 1.5rem;
            text-align: center;
            letter-spacing: 0.5rem;
            font-weight: bold;
        }

        .countdown {
            font-weight: bold;
            color: var(--accent-orange);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="verify-container">
            <!-- Logo Header -->
            <div class="logo-header">
                <div class="logo-container">
                    <div class="logo-placeholder" style="background-image: url('images/karma_logo.png');"></div>
                    <div class="logo-placeholder" style="background-image: url('images/Abbott_Laboratories_logo.png');"></div>
                </div>
                <h3 class="page-title">Phone Verification</h3>
                <p class="page-subtitle">Enter your phone number to verify your identity</p>
            </div>

            <!-- Error/Success Messages -->
            <?php if ($error): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle me-2"></i><?php echo htmlspecialchars($error); ?>
            </div>
            <?php endif; ?>

            <?php if ($success): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle me-2"></i><?php echo htmlspecialchars($success); ?>
            </div>
            <?php endif; ?>

            <!-- Information Box -->
            <div class="verification-info">
                <i class="fas fa-info-circle me-2"></i>
                <strong>Note:</strong> We'll send a verification code to this number. Existing applications will be resumed.
            </div>

            <!-- Phone Verification Form -->
            <form id="phoneVerifyForm" method="POST" action="send_otp.php">
                <div class="mb-3">
                    <label for="phone" class="form-label">Phone Number</label>
                    <div class="input-group">
                        <span class="input-group-text">+91</span>
                        <input 
                            type="tel" 
                            class="form-control" 
                            id="phone" 
                            name="phone" 
                            value="<?php echo htmlspecialchars($phone); ?>"
                            placeholder="10-digit mobile number"
                            required
                            pattern="[0-9]{10}"
                            maxlength="10">
                    </div>
                    <div class="form-text">We'll send a 6-digit verification code via SMS</div>
                    <div class="invalid-feedback">Please enter a valid 10-digit phone number</div>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary" id="sendOtpBtn">
                        <i class="fas fa-mobile-alt me-2"></i>Send Verification Code
                    </button>
                    <a href="index.php" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back to Welcome
                    </a>
                </div>
            </form>

            <!-- Support Information -->
            <div class="mt-4 text-center">
                <small class="text-muted">
                    <i class="fas fa-headset me-1"></i>Need help? 
                    <a href="mailto:support@example.com">Contact Support</a>
                </small>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.8/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
        const phoneInput = document.getElementById('phone');
        const form = document.getElementById('phoneVerifyForm');
        const sendOtpBtn = document.getElementById('sendOtpBtn');

        // Phone number input validation
        phoneInput.addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
            
            if (this.value.length === 10) {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
            } else {
                this.classList.remove('is-valid');
            }
        });

        // Form submission with AJAX
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const phone = phoneInput.value;
            
            if (!phone || phone.length !== 10) {
                phoneInput.classList.add('is-invalid');
                phoneInput.focus();
                return;
            }

            // Disable button and show loading
            sendOtpBtn.disabled = true;
            sendOtpBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Sending OTP...';

            try {
                const response = await fetch('send_otp.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `phone=${encodeURIComponent(phone)}`
                });

                const result = await response.json();

                if (result.success) {
                    // Store phone in session and redirect to OTP verification
                    window.location.href = 'otp_verification.php';
                } else {
                    throw new Error(result.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error: ' + error.message);
                sendOtpBtn.disabled = false;
                sendOtpBtn.innerHTML = '<i class="fas fa-mobile-alt me-2"></i>Send Verification Code';
            }
        });

        // Auto-focus on phone input
        phoneInput.focus();
    });
    
    </script>
</body>
</html>