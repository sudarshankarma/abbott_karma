<?php
require_once 'config.php';

// Check if consent is given and phone is set
if (!isset($_SESSION['consent_given']) || $_SESSION['consent_given'] !== true) {
    header('Location: index.php');
    exit;
}

if (!isset($_SESSION['otp_phone'])) {
    header('Location: verify_phone.php');
    exit;
}

$phone = $_SESSION['otp_phone'];
$error = $_GET['error'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP - Employee Registration</title>
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

        .otp-input {
            font-size: 1.5rem;
            text-align: center;
            letter-spacing: 0.5rem;
            font-weight: bold;
            height: 60px;
        }

        .countdown {
            font-weight: bold;
            color: var(--accent-orange);
        }

        .resend-link {
            cursor: pointer;
            color: var(--primary-blue);
        }

        .resend-link:hover {
            text-decoration: underline;
        }

        .resend-link.disabled {
            color: #6c757d;
            cursor: not-allowed;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="verify-container">
            <h3 class="page-title text-center">Enter Verification Code</h3>
            <p class="page-subtitle text-center">We've sent a 6-digit code to <strong>+91 <?php echo htmlspecialchars($phone); ?></strong></p>

            <!-- Error Message -->
            <?php if ($error): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle me-2"></i><?php echo htmlspecialchars($error); ?>
            </div>
            <?php endif; ?>

            <!-- OTP Verification Form -->
            <form id="otpVerifyForm">
                <input type="hidden" name="phone" value="<?php echo htmlspecialchars($phone); ?>">
                
                <div class="mb-4">
                    <label class="form-label">Verification Code</label>
                    <input 
                        type="text" 
                        class="form-control otp-input" 
                        id="otpCode" 
                        name="otp" 
                        maxlength="6" 
                        placeholder="000000" 
                        pattern="[0-9]{6}"
                        required>
                    <div class="invalid-feedback">Please enter the 6-digit code</div>
                </div>

                <div class="text-center mb-3">
                    <small class="text-muted">
                        Code expires in <span id="countdown" class="countdown">10:00</span>
                    </small>
                </div>

                <div class="text-center mb-4">
                    <small class="text-muted">
                        Didn't receive code? 
                        <span id="resendLink" class="resend-link disabled">Resend</span>
                    </small>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary" id="verifyOtpBtn">
                        <i class="fas fa-check-circle me-2"></i>Verify & Continue
                    </button>
                    <a href="verify_phone.php" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Change Phone Number
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.8/js/bootstrap.bundle.min.js"></script>
    <script>
        let countdownInterval;
        let timeLeft = 600; // 10 minutes in seconds

        document.addEventListener('DOMContentLoaded', function() {
            const otpInput = document.getElementById('otpCode');
            const form = document.getElementById('otpVerifyForm');
            const verifyOtpBtn = document.getElementById('verifyOtpBtn');
            const resendLink = document.getElementById('resendLink');
            const countdownElement = document.getElementById('countdown');

            // Start countdown
            startCountdown();

            // OTP input validation
            otpInput.addEventListener('input', function() {
                this.value = this.value.replace(/[^0-9]/g, '');
                
                if (this.value.length === 6) {
                    this.classList.remove('is-invalid');
                    this.classList.add('is-valid');
                } else {
                    this.classList.remove('is-valid');
                }
            });

            // Form submission
            form.addEventListener('submit', async function(e) {
                e.preventDefault();
                
                const otp = otpInput.value;
                const phone = document.querySelector('input[name="phone"]').value;
                
                if (!otp || otp.length !== 6) {
                    otpInput.classList.add('is-invalid');
                    otpInput.focus();
                    return;
                }

                // Disable button and show loading
                verifyOtpBtn.disabled = true;
                verifyOtpBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Verifying...';

                try {
                    const response = await fetch('verify_otp.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `phone=${encodeURIComponent(phone)}&otp=${encodeURIComponent(otp)}`
                    });

                    const result = await response.json();

                    if (result.success) {
                        if (result.application_exists) {
                            // Redirect to form - it will detect the existing application
                            window.location.href = 'form.php';
                        } else {
                            // New application - go to personal details
                            window.location.href = 'form.php';
                        }
                    } else {
                        throw new Error(result.message);
                    }
                } catch (error) {
                    console.error('Error:', error);
                    alert('Error: ' + error.message);
                    verifyOtpBtn.disabled = false;
                    verifyOtpBtn.innerHTML = '<i class="fas fa-check-circle me-2"></i>Verify & Continue';
                }
            });

            // Resend OTP functionality
            resendLink.addEventListener('click', function() {
                if (this.classList.contains('disabled')) return;

                const phone = document.querySelector('input[name="phone"]').value;
                
                this.classList.add('disabled');
                this.innerHTML = 'Sending...';

                fetch('send_otp.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `phone=${encodeURIComponent(phone)}`
                })
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        alert('New OTP sent successfully!');
                        resetCountdown();
                    } else {
                        alert('Error: ' + result.message);
                    }
                    this.innerHTML = 'Resend';
                    this.classList.remove('disabled');
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Failed to resend OTP');
                    this.innerHTML = 'Resend';
                    this.classList.remove('disabled');
                });
            });

            // Auto-focus on OTP input
            otpInput.focus();
        });

        function startCountdown() {
            updateCountdownDisplay();
            
            countdownInterval = setInterval(() => {
                timeLeft--;
                updateCountdownDisplay();
                
                if (timeLeft <= 0) {
                    clearInterval(countdownInterval);
                    document.getElementById('resendLink').classList.remove('disabled');
                }
            }, 1000);
        }

        function resetCountdown() {
            clearInterval(countdownInterval);
            timeLeft = 600;
            startCountdown();
            document.getElementById('resendLink').classList.add('disabled');
        }

        function updateCountdownDisplay() {
            const minutes = Math.floor(timeLeft / 60);
            const seconds = timeLeft % 60;
            document.getElementById('countdown').textContent = 
                `${minutes}:${seconds.toString().padStart(2, '0')}`;
        }
    </script>
</body>
</html>