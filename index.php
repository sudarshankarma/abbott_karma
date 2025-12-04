<?php
require_once 'config.php';

// Clear any existing session data to start fresh
session_destroy();
session_start();

// Always start from welcome page - clear any previous data
unset($_SESSION['consent_given']);
unset($_SESSION['verified_phone']);
unset($_SESSION['application_data']);

// If consent is given via POST, set session and redirect to OTP verification
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['consent'])) {
    $_SESSION['consent_given'] = true;
    header('Location: verify_phone.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome - Employee Registration</title>
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

        .welcome-container {
            background: white;
            border-radius: 15px;
            padding: 40px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            max-width: 800px;
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
            width: 150px;
            height: 80px;
            background-size: contain;
            background-repeat: no-repeat;
            background-position: center;
            /*border: 1px solid #ddd;*/
            background-color: #ffffff;
        }

        .welcome-title {
            color: var(--primary-blue);
            font-weight: 700;
            margin-bottom: 20px;
        }

        .welcome-subtitle {
            color: #666;
            margin-bottom: 30px;
            font-size: 1.1rem;
        }

        .consent-box {
            background: #f8f9fa;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 25px;
            margin: 25px 0;
            max-height: 300px;
            overflow-y: auto;
        }

        .consent-title {
            color: var(--primary-blue);
            font-weight: 600;
            margin-bottom: 15px;
        }

        .btn-primary {
            background: var(--primary-blue);
            border-color: var(--primary-blue);
            padding: 12px 30px;
            font-weight: 600;
            font-size: 1.1rem;
        }

        .btn-primary:hover {
            background: #2d2f70;
            border-color: #2d2f70;
        }

        .feature-list {
            margin: 25px 0;
        }

        .feature-item {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 8px;
        }

        .feature-icon {
            width: 40px;
            height: 40px;
            background: var(--primary-blue);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="welcome-container">
            <!-- Logo Header -->
            <div class="logo-header">
                <div class="logo-container">
                    <link rel="shortcut icon" href="images/abbott_favicon.ico" type="image/x-icon"/>
                    <div class="logo-placeholder" style="background-image: url('images/karma_logo.png');"></div>
                    <div class="logo-placeholder" style="background-image: url('images/Abbott_Laboratories_logo.png');"></div>
                </div>
                <h1 class="welcome-title">Employee UAN Onboarding Portal</h1>
                <p class="welcome-subtitle">Piramal & Abbott Employee Registration System</p>
            </div>

            <!-- Features -->
            <div class="feature-list">
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-user-check"></i>
                    </div>
                    <div>
                        <strong>Quick Registration</strong>
                        <p class="mb-0 text-muted">Complete your registration in simple steps</p>
                    </div>
                </div>
                
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-mobile-alt"></i>
                    </div>
                    <div>
                        <strong>Mobile Verification</strong>
                        <p class="mb-0 text-muted">Secure OTP verification for your account</p>
                    </div>
                </div>
                
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-cloud-upload-alt"></i>
                    </div>
                    <div>
                        <strong>Document Upload</strong>
                        <p class="mb-0 text-muted">Upload required documents (up to 10MB each)</p>
                    </div>
                </div>
                
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <div>
                        <strong>Secure & Private</strong>
                        <p class="mb-0 text-muted">Your data is protected and encrypted</p>
                    </div>
                </div>
            </div>

            <!-- Consent Box -->
            <div class="consent-box">
                <h4 class="consent-title"><i class="fas fa-shield-alt me-2"></i>Data Sharing Consent</h4>
                <div class="consent-content">
                    <p>By proceeding with the registration, you acknowledge and consent to the following:</p>
                    
                    <ul class="consent-points">
                        <li>I hereby give my consent to share my personal details including name, contact information, UAN numbers, and uploaded documents with Piramal and Abbott for employment registration and verification purposes.</li>
                        
                        <li>I understand that my personal information will be processed in accordance with applicable data protection laws and company privacy policies.</li>
                        
                        <li>I acknowledge that the information provided will be used for employee onboarding, UAN registration, and related HR processes.</li>
                        
                        <li>I confirm that all information provided is accurate and true to the best of my knowledge.</li>
                    </ul>
                </div>
            </div>

            <!-- Consent Form -->
            <form method="POST" action="">
                <div class="mb-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="consentCheck" name="consent" value="true" required>
                        <label class="form-check-label" for="consentCheck">
                            <strong>I have read and agree to the terms and conditions above</strong>
                        </label>
                        <div class="invalid-feedback">
                            You must agree to the terms and conditions to proceed.
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-play-circle me-2"></i>Start Registration
                    </button>
                    <!-- <a href="verify_phone.php" class="btn btn-outline-secondary">
                        <i class="fas fa-history me-2"></i>Continue Existing Application
                    </a> -->
                </div>
            </form>

            <!-- Support Information -->
            <div class="mt-4 text-center">
                <small class="text-muted">
                    <i class="fas fa-headset me-1"></i>Need help? Contact HR Support
                </small>
            </div>
        </div>
    </div>

    <script>
        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const consentCheck = document.getElementById('consentCheck');
            if (!consentCheck.checked) {
                e.preventDefault();
                consentCheck.focus();
                consentCheck.classList.add('is-invalid');
            }
        });

        // Remove invalid state when checkbox is checked
        document.getElementById('consentCheck').addEventListener('change', function() {
            if (this.checked) {
                this.classList.remove('is-invalid');
            }
        });
    </script>
</body>
</html>