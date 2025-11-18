<?php
require_once 'config.php';

// Check if user has given consent and phone is verified
if (!isset($_SESSION['consent_given']) || $_SESSION['consent_given'] !== true) {
    header('Location: index.php');
    exit;
}

if (!isset($_SESSION['verified_phone'])) {
    header('Location: verify_phone.php');
    exit;
}

$verifiedPhone = $_SESSION['verified_phone'];

// Check application status
$applicationData = null;
try {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT * FROM applications WHERE phone = ?");
    $stmt->execute([$verifiedPhone]);
    $applicationData = $stmt->fetch();
} catch (Exception $e) {
    error_log("Error fetching application data: " . $e->getMessage());
}

// Determine current step
$currentStep = 1;
if ($applicationData) {
    if ($applicationData['status'] === 'personal_details_completed' || 
        $applicationData['status'] === 'documents_uploaded' ||
        $applicationData['status'] === 'completed') {
        $currentStep = 2;
    }
    
    // Only go to step 3 if ALL documents are uploaded
    if ($applicationData['pan_card'] && $applicationData['aadhar_card'] && $applicationData['cancelled_cheque']) {
        $currentStep = 3;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Registration Form</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.8/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-blue: #3E4095;
            --accent-orange: #F26B35;
            --step-active: #107c10;
            --step-pending: #f7931e;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #faf9f8;
        }

        .logo-header {
            padding: 20px;
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo-container {
            flex: 1;
        }

        .logo-left {
            text-align: left;
        }

        .logo-right {
            text-align: right;
        }

        .logo-placeholder {
            width: 150px;
            height: 80px;
            display: inline-block;
            background-size: contain;
            background-repeat: no-repeat;
            background-position: center;
            /*border: 1px solid #ddd;*/
            background-color: #ffffff;
        }

        .progress-container {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        .step-indicator {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        
        .step {
            flex: 1;
            text-align: center;
            position: relative;
        }
        
        .step-circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #e9ecef;
            color: #6c757d;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            margin: 0 auto 10px;
            transition: all 0.3s;
        }
        
        .step.active .step-circle {
            background: var(--primary-blue);
            color: white;
        }
        
        .step.completed .step-circle {
            background: var(--step-active);
            color: white;
        }
        
        .step.current .step-circle {
            background: var(--accent-orange);
            color: white;
            transform: scale(1.1);
        }
        
        .step-line {
            position: absolute;
            top: 20px;
            left: 50%;
            width: 100%;
            height: 2px;
            background: #e9ecef;
            z-index: -1;
        }
        
        .step.completed .step-line {
            background: var(--step-active);
        }
        
        .step:last-child .step-line {
            display: none;
        }
        
        .form-container {
            background: white;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            min-height: 500px;
        }
        
        .step-content {
            display: none;
        }
        
        .step-content.active {
            display: block;
            animation: fadeIn 0.5s;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .btn-primary {
            background: var(--primary-blue);
            border-color: var(--primary-blue);
        }
        
        .btn-primary:hover {
            background: #2d2f70;
            border-color: #2d2f70;
        }
        
        .form-section {
            margin-bottom: 25px;
        }
        
        .section-title {
            color: var(--primary-blue);
            font-weight: 600;
            margin-bottom: 15px;
            padding-bottom: 8px;
            border-bottom: 2px solid #e9ecef;
        }
        
        .step-navigation {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
        }
        
        .resume-notice {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .required-field::after {
            content: " *";
            color: #dc3545;
        }

        .document-upload-card {
            background: #ffffff;
            border: 2px dashed #dee2e6;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 25px;
            transition: all 0.3s ease;
        }

        .document-upload-card.has-file {
            border-color: var(--step-active);
            background: #f0f9f0;
        }

        .file-preview {
            margin-top: 10px;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 4px;
            font-size: 0.875rem;
        }

        .verified-badge {
            background: var(--step-active);
            color: white;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            margin-left: 10px;
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <!-- Logo Header -->
                <div class="logo-header">
                    <div class="logo-container logo-left">
                        <div class="logo-placeholder" style="background-image: url('images/karma_logo.png');"></div>
                    </div>
                    <div class="logo-container logo-right">
                        <div class="logo-placeholder" style="background-image: url('images/Abbott_Laboratories_logo.png');"></div>
                    </div>
                </div>

                <!-- Phone Verification Status -->
                <div class="alert alert-success mb-4">
                    <i class="fas fa-check-circle me-2"></i>
                    <strong>Phone Verified:</strong> +91 <?php echo htmlspecialchars($verifiedPhone); ?>
                    <span class="verified-badge"><i class="fas fa-shield-alt me-1"></i>Verified</span>
                </div>

                <!-- Resume Notice -->
                <?php if ($applicationData): ?>
                <div class="resume-notice">
                    <h5><i class="fas fa-sync-alt me-2"></i>Resuming Application</h5>
                    <p class="mb-0">Welcome back! We found your existing application. Please complete the remaining steps.</p>
                    <?php if ($applicationData['application_id']): ?>
                    <small><strong>Application ID:</strong> <?php echo htmlspecialchars($applicationData['application_id']); ?></small>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <!-- Progress Indicator -->
                <div class="progress-container">
                    <div class="step-indicator">
                        <div class="step <?php echo $currentStep >= 1 ? 'completed' : ''; ?> <?php echo $currentStep == 1 ? 'current' : ''; ?>" data-step="1">
                            <div class="step-circle">1</div>
                            <div class="step-line"></div>
                            <small>Personal Details</small>
                        </div>
                        <div class="step <?php echo $currentStep >= 2 ? 'completed' : ''; ?> <?php echo $currentStep == 2 ? 'current' : ''; ?>" data-step="2">
                            <div class="step-circle">2</div>
                            <div class="step-line"></div>
                            <small>Documents Upload</small>
                        </div>
                        <div class="step <?php echo $currentStep >= 3 ? 'completed' : ''; ?> <?php echo $currentStep == 3 ? 'current' : ''; ?>" data-step="3">
                            <div class="step-circle">✓</div>
                            <small>Complete</small>
                        </div>
                    </div>
                </div>

                <!-- Form Container -->
                <div class="form-container">
                    <!-- Step 1: Personal Details -->
                    <div class="step-content <?php echo $currentStep == 1 ? 'active' : ''; ?>" id="step1">
                        <h4 class="text-primary mb-4">
                            <i class="fas fa-user me-2"></i>Step 1: Personal Details
                            <?php if ($applicationData): ?>
                            <span class="badge bg-warning float-end">Resuming</span>
                            <?php endif; ?>
                        </h4>
                        
                        <form id="personalForm" novalidate>
                            <input type="hidden" name="phone" value="<?php echo htmlspecialchars($verifiedPhone); ?>">
                            
                            <div class="form-section">
                                <div class="section-title">Basic Information</div>
                                
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label required-field">Full Name</label>
                                        <input 
                                            type="text" 
                                            class="form-control" 
                                            id="fullName" 
                                            name="fullName" 
                                            required
                                            minlength="3"
                                            maxlength="100"
                                            value="<?php echo htmlspecialchars($applicationData['full_name'] ?? ''); ?>"
                                            placeholder="Enter your full name">
                                        <div class="invalid-feedback">Please enter a valid name (3-100 characters)</div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label required-field">Email Address</label>
                                        <input 
                                            type="email" 
                                            class="form-control" 
                                            id="email" 
                                            name="email"
                                            required
                                            value="<?php echo htmlspecialchars($applicationData['email'] ?? ''); ?>"
                                            placeholder="your.email@example.com">
                                        <div class="invalid-feedback">Please enter a valid email address</div>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label required-field">Phone Number</label>
                                        <input 
                                            type="tel" 
                                            class="form-control" 
                                            id="phone" 
                                            name="phone" 
                                            required
                                            pattern="[0-9]{10}"
                                            maxlength="10"
                                            value="<?php echo htmlspecialchars($verifiedPhone); ?>"
                                            readonly
                                            style="background-color: #e9ecef;">
                                        <div class="form-text text-success">
                                            <i class="fas fa-check-circle me-1"></i>Phone number verified
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">WhatsApp Number</label>
                                        <input 
                                            type="tel" 
                                            class="form-control" 
                                            id="whatsapp" 
                                            name="whatsapp"
                                            pattern="[0-9]{10}"
                                            maxlength="10"
                                            value="<?php echo htmlspecialchars($applicationData['whatsapp'] ?? $verifiedPhone); ?>"
                                            placeholder="10-digit WhatsApp number">
                                        <small class="text-muted">Leave blank to use phone number</small>
                                        <div class="invalid-feedback">Please enter a valid 10-digit WhatsApp number</div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-section">
                                <div class="section-title">Employee Information (Optional)</div>
                                
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Piramal Employee ID</label>
                                        <input 
                                            type="text" 
                                            class="form-control" 
                                            id="piramalId" 
                                            name="piramalId" 
                                            placeholder="Enter Piramal Employee ID"
                                            maxlength="50"
                                            value="<?php echo htmlspecialchars($applicationData['piramal_id'] ?? ''); ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Abbott Employee ID</label>
                                        <input 
                                            type="text" 
                                            class="form-control" 
                                            id="abbottId" 
                                            name="abbottId" 
                                            placeholder="Enter Abbott Employee ID"
                                            maxlength="50"
                                            value="<?php echo htmlspecialchars($applicationData['abbott_id'] ?? ''); ?>">
                                    </div>
                                </div>
                            </div>

                            <div class="form-section">
                                <div class="section-title">UAN Information</div>
                                
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Piramal UAN Number</label>
                                        <input 
                                            type="text" 
                                            class="form-control" 
                                            id="piramalUAN" 
                                            name="piramalUAN" 
                                            maxlength="12"
                                            pattern="[0-9]{12}"
                                            placeholder="12-digit UAN number"
                                            value="<?php echo htmlspecialchars($applicationData['piramal_uan'] ?? ''); ?>">
                                        <small class="text-muted">Example: 123456789012</small>
                                        <div class="invalid-feedback">Please enter a valid 12-digit UAN number</div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Abbott UAN Number</label>
                                        <input 
                                            type="text" 
                                            class="form-control" 
                                            id="abbottUAN" 
                                            name="abbottUAN" 
                                            maxlength="12"
                                            pattern="[0-9]{12}"
                                            placeholder="12-digit UAN number"
                                            value="<?php echo htmlspecialchars($applicationData['abbott_uan'] ?? ''); ?>">
                                        <small class="text-muted">Example: 123456789012</small>
                                        <div class="invalid-feedback">Please enter a valid 12-digit UAN number</div>
                                    </div>
                                </div>
                            </div>

                            <div class="step-navigation">
                                <a href="index.php" class="btn btn-outline-secondary">
                                    <i class="fas fa-home me-1"></i>Back to Home
                                </a>
                                <button type="submit" class="btn btn-primary" id="step1SubmitBtn">
                                    Save & Continue <i class="fas fa-arrow-right ms-1"></i>
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Step 2: Documents Upload -->
                    <div class="step-content <?php echo $currentStep == 2 ? 'active' : ''; ?>" id="step2">
                        <h4 class="text-primary mb-4">Step 2: Document Upload</h4>
                        
                        <form id="documentsForm" novalidate>
                            <input type="hidden" name="application_id" id="applicationId" value="<?php echo htmlspecialchars($applicationData['application_id'] ?? ''); ?>">
                            
                            <div class="form-section">
                                <div class="section-title">Required Documents</div>
                                <p class="text-muted mb-4">Please upload clear copies. Maximum file size: 10MB per file.</p>
                                
                                <!-- PAN Card -->
                                <div class="document-upload-card" id="panCardSection">
                                    <div class="row align-items-start">
                                        <div class="col-md-12">
                                            <div class="document-title">PAN Card</div>
                                            
                                            <div class="mb-3">
                                                <label class="form-label required-field">PAN Card Number</label>
                                                <input 
                                                    type="text" 
                                                    class="form-control" 
                                                    id="panNumber" 
                                                    name="panNumber" 
                                                    maxlength="10"
                                                    placeholder="ABCDE1234F"
                                                    required
                                                    style="text-transform: uppercase;"
                                                    value="<?php echo htmlspecialchars($applicationData['pan_number'] ?? ''); ?>">
                                                <small class="text-muted">Format: 5 letters, 4 digits, 1 letter (e.g., ABCDE1234F)</small>
                                                <div class="invalid-feedback">Please enter a valid PAN number</div>
                                            </div>
                                            
                                            <div>
                                                <label class="form-label required-field">Upload PAN Card</label>
                                                <input 
                                                    type="file" 
                                                    class="form-control" 
                                                    id="panCard" 
                                                    name="panCard" 
                                                    accept=".pdf,.jpg,.jpeg,.png"
                                                    required
                                                    onchange="validateFile(this, 'panCardSection')">
                                                <small class="file-info">PDF, JPG, PNG (Max 10MB)</small>
                                                <div id="panCardPreview" class="file-preview" style="display:none;"></div>
                                                <div class="invalid-feedback">Please upload PAN Card document</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Aadhar Card -->
                                <div class="document-upload-card" id="aadharCardSection">
                                    <div class="row align-items-start">
                                        <div class="col-md-12">
                                            <div class="document-title">Aadhar Card</div>
                                            
                                            <div class="mb-3">
                                                <label class="form-label required-field">Aadhar Card Number</label>
                                                <input 
                                                    type="text" 
                                                    class="form-control" 
                                                    id="aadharNumber" 
                                                    name="aadharNumber" 
                                                    maxlength="12"
                                                    placeholder="123456789012"
                                                    required
                                                    value="<?php echo htmlspecialchars($applicationData['aadhar_number'] ?? ''); ?>">
                                                <small class="text-muted">Enter 12-digit Aadhar number</small>
                                                <div class="invalid-feedback">Please enter a valid 12-digit Aadhar number</div>
                                            </div>
                                            
                                            <div>
                                                <label class="form-label required-field">Upload Aadhar Card</label>
                                                <input 
                                                    type="file" 
                                                    class="form-control" 
                                                    id="aadharCard" 
                                                    name="aadharCard" 
                                                    accept=".pdf,.jpg,.jpeg,.png"
                                                    required
                                                    onchange="validateFile(this, 'aadharCardSection')">
                                                <small class="file-info">PDF, JPG, PNG (Max 10MB)</small>
                                                <div id="aadharCardPreview" class="file-preview" style="display:none;"></div>
                                                <div class="invalid-feedback">Please upload Aadhar Card document</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Cancelled Cheque -->
                                <div class="document-upload-card" id="cancelledChequeSection">
                                    <div class="row align-items-start">
                                        <div class="col-md-12">
                                            <div class="document-title">Cancelled Cheque</div>
                                            
                                            <div>
                                                <label class="form-label required-field">Upload Cancelled Cheque</label>
                                                <input 
                                                    type="file" 
                                                    class="form-control" 
                                                    id="cancelledCheque" 
                                                    name="cancelledCheque" 
                                                    accept=".pdf,.jpg,.jpeg,.png"
                                                    required
                                                    onchange="validateFile(this, 'cancelledChequeSection')">
                                                <small class="file-info">Upload cancelled cheque for bank verification - PDF, JPG, PNG (Max 10MB)</small>
                                                <div id="cancelledChequePreview" class="file-preview" style="display:none;"></div>
                                                <div class="invalid-feedback">Please upload Cancelled Cheque</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="step-navigation">
                                <button type="button" class="btn btn-outline-secondary" onclick="previousStep()">
                                    <i class="fas fa-arrow-left me-1"></i> Previous
                                </button>
                                <button type="submit" class="btn btn-primary" id="step2SubmitBtn">
                                    <span id="submitBtnText">Complete Application</span> 
                                    <i class="fas fa-check ms-1" id="submitIcon"></i>
                                    <span class="spinner-border spinner-border-sm ms-1" role="status" id="submitSpinner" style="display:none;"></span>
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Step 3: Completion -->
                    <div class="step-content <?php echo $currentStep == 3 ? 'active' : ''; ?>" id="step3">
                        <div class="text-center py-5">
                            <div class="mb-4">
                                <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                            </div>
                            <h3 class="text-success mb-3">Application Completed Successfully!</h3>
                            <p class="text-muted mb-4">Your employee registration application has been submitted successfully.</p>
                            
                            <div class="alert alert-info">
                                <strong>Application ID:</strong> <span id="applicationIdDisplay"><?php echo htmlspecialchars($applicationData['application_id'] ?? 'EPF-2024-0000'); ?></span><br>
                                <strong>Status:</strong> Under Review<br>
                                <strong>Expected Processing Time:</strong> 2-3 business days
                            </div>

                            <div class="mt-4">
                                <button class="btn btn-primary me-2" onclick="downloadReceipt()">
                                    <i class="fas fa-download me-1"></i>Download Receipt
                                </button>
                                <a href="index.php" class="btn btn-outline-primary">
                                    <i class="fas fa-home me-1"></i>Back to Home
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.8/js/bootstrap.bundle.min.js"></script>
    <script>
        let currentStep = <?php echo $currentStep; ?>;
        let formData = {
            personalDetails: {},
            documents: {}
        };

        document.addEventListener('DOMContentLoaded', function() {
            updateStepIndicator();
            
            // Input validation
            document.getElementById('phone').addEventListener('input', function() {
                this.value = this.value.replace(/[^0-9]/g, '');
            });

            document.getElementById('whatsapp').addEventListener('input', function() {
                this.value = this.value.replace(/[^0-9]/g, '');
            });

            document.getElementById('piramalUAN').addEventListener('input', function() {
                this.value = this.value.replace(/[^0-9]/g, '');
            });

            document.getElementById('abbottUAN').addEventListener('input', function() {
                this.value = this.value.replace(/[^0-9]/g, '');
            });

            // PAN number validation
            document.getElementById('panNumber').addEventListener('input', function() {
                this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
            });

            // Aadhar number validation
            document.getElementById('aadharNumber').addEventListener('input', function() {
                this.value = this.value.replace(/[^0-9]/g, '');
            });
        });

        function updateStepIndicator() {
            document.querySelectorAll('.step').forEach((step, index) => {
                const stepNum = index + 1;
                step.classList.remove('current', 'completed', 'active');
                
                if (stepNum < currentStep) {
                    step.classList.add('completed');
                } else if (stepNum === currentStep) {
                    step.classList.add('current');
                }
            });
        }

        function goToStep(step) {
            document.querySelectorAll('.step-content').forEach(content => {
                content.classList.remove('active');
            });
            
            document.getElementById(`step${step}`).classList.add('active');
            currentStep = step;
            updateStepIndicator();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        function nextStep() {
            if (currentStep < 3) {
                currentStep++;
                goToStep(currentStep);
            }
        }

        function previousStep() {
            if (currentStep > 1) {
                currentStep--;
                goToStep(currentStep);
            }
        }

        function validateFile(input, sectionId) {
            const file = input.files[0];
            const maxSize = 10 * 1024 * 1024; // 10MB
            const allowedTypes = ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png'];
            
            if (!file) return true;
            
            if (file.size > maxSize) {
                input.classList.add('is-invalid');
                alert(`File ${file.name} is too large. Maximum size is 10MB.`);
                input.value = '';
                return false;
            }
            
            if (!allowedTypes.includes(file.type)) {
                input.classList.add('is-invalid');
                alert(`File ${file.name} has invalid type. Only PDF, JPG, and PNG are allowed.`);
                input.value = '';
                return false;
            }
            
            input.classList.remove('is-invalid');
            
            // Add success styling to card
            if (sectionId) {
                document.getElementById(sectionId).classList.add('has-file');
            }
            
            const previewId = input.id + 'Preview';
            const preview = document.getElementById(previewId);
            if (preview) {
                preview.style.display = 'block';
                preview.innerHTML = `<i class="fas fa-check-circle text-success me-2"></i><strong>${file.name}</strong> (${formatFileSize(file.size)})`;
            }
            
            return true;
        }

        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
        }

        document.getElementById('personalForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            let isValid = true;
            
            // Basic validation
            const requiredFields = ['fullName', 'email', 'phone'];
            requiredFields.forEach(field => {
                const element = document.getElementById(field);
                if (!element.value.trim()) {
                    element.classList.add('is-invalid');
                    isValid = false;
                } else {
                    element.classList.remove('is-invalid');
                }
            });
            
            // Email validation
            const email = document.getElementById('email');
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (email.value && !emailRegex.test(email.value)) {
                email.classList.add('is-invalid');
                isValid = false;
            }
            
            if (!isValid) {
                alert('Please fill all required fields correctly.');
                return;
            }
            
            // Collect form data
            formData.personalDetails = {
                fullName: document.getElementById('fullName').value,
                email: document.getElementById('email').value,
                phone: document.getElementById('phone').value,
                whatsapp: document.getElementById('whatsapp').value || document.getElementById('phone').value,
                piramalId: document.getElementById('piramalId').value,
                abbottId: document.getElementById('abbottId').value,
                piramalUAN: document.getElementById('piramalUAN').value,
                abbottUAN: document.getElementById('abbottUAN').value
            };
            
            // Submit personal details
            submitPersonalDetails();
        });

        async function submitPersonalDetails() {
            const formDataToSend = new FormData();
            
            // Add personal details
            Object.keys(formData.personalDetails).forEach(key => {
                formDataToSend.append(key, formData.personalDetails[key] || '');
            });
            
            const submitBtn = document.getElementById('step1SubmitBtn');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Saving...';
            
            try {
                const response = await fetch('submit_personal_details.php', {
                    method: 'POST',
                    body: formDataToSend
                });
                
                const result = await response.json();
                
                if (result.success) {
                    // Store application ID for document upload
                    if (result.applicationId) {
                        document.getElementById('applicationId').value = result.applicationId;
                        sessionStorage.setItem('applicationId', result.applicationId);
                    }
                    nextStep();
                } else {
                    alert('Error: ' + (result.message || 'Failed to save personal details'));
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred while saving personal details. Please try again.');
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = 'Save & Continue <i class="fas fa-arrow-right ms-1"></i>';
            }
        }

        async function submitDocuments() {
            const applicationId = document.getElementById('applicationId').value || sessionStorage.getItem('applicationId');
            if (!applicationId) {
                alert('Application ID not found. Please start over.');
                return;
            }

            const formDataToSend = new FormData();
            formDataToSend.append('application_id', applicationId);
            
            // Only append if values exist
            const panNumber = document.getElementById('panNumber').value;
            const aadharNumber = document.getElementById('aadharNumber').value;
            
            if (panNumber) {
                formDataToSend.append('pan_number', panNumber);
            }
            
            if (aadharNumber) {
                formDataToSend.append('aadhar_number', aadharNumber);
            }
            
            // Only append files if they were actually selected
            const panCardFile = document.getElementById('panCard').files[0];
            const aadharCardFile = document.getElementById('aadharCard').files[0];
            const cancelledChequeFile = document.getElementById('cancelledCheque').files[0];
            
            if (panCardFile) {
                formDataToSend.append('pan_card', panCardFile);
            }
            
            if (aadharCardFile) {
                formDataToSend.append('aadhar_card', aadharCardFile);
            }
            
            if (cancelledChequeFile) {
                formDataToSend.append('cancelled_cheque', cancelledChequeFile);
            }
            
            const submitBtn = document.getElementById('step2SubmitBtn');
            const submitBtnText = document.getElementById('submitBtnText');
            const submitIcon = document.getElementById('submitIcon');
            const submitSpinner = document.getElementById('submitSpinner');
            
            submitBtn.disabled = true;
            submitBtnText.textContent = 'Submitting...';
            submitIcon.style.display = 'none';
            submitSpinner.style.display = 'inline-block';
            
            try {
                const response = await fetch('submit_documents.php', {
                    method: 'POST',
                    body: formDataToSend
                });
                
                const result = await response.json();
                
                if (result.success) {
                    // Show success message and update UI
                    alert('Documents submitted successfully! You can upload remaining documents later.');
                    
                    // Update application status display
                    document.getElementById('applicationIdDisplay').textContent = applicationId;
                    
                    // Show which documents were uploaded
                    if (result.documents_uploaded) {
                        let uploadedDocs = [];
                        if (result.documents_uploaded.pan_card) uploadedDocs.push('PAN Card');
                        if (result.documents_uploaded.aadhar_card) uploadedDocs.push('Aadhar Card');
                        if (result.documents_uploaded.cancelled_cheque) uploadedDocs.push('Cancelled Cheque');
                        
                        if (uploadedDocs.length > 0) {
                            alert('Successfully uploaded: ' + uploadedDocs.join(', '));
                        }
                    }
                    
                    sessionStorage.removeItem('applicationId');
                    nextStep();
                } else {
                    throw new Error(result.message || 'Failed to submit documents');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred while submitting documents: ' + error.message);
            } finally {
                submitBtn.disabled = false;
                submitBtnText.textContent = 'Save Documents';
                submitIcon.style.display = 'inline';
                submitSpinner.style.display = 'none';
            }
        }

        function downloadReceipt() {
            const appId = document.getElementById('applicationIdDisplay').textContent;
            const fullName = document.getElementById('fullName').value;
            
            const receiptHTML = `
                <!DOCTYPE html>
                <html>
                <head>
                    <meta charset="UTF-8">
                    <title>Application Receipt - ${appId}</title>
                    <style>
                        body { font-family: Arial, sans-serif; padding: 40px; line-height: 1.6; }
                        .header { text-align: center; margin-bottom: 30px; border-bottom: 3px solid #3E4095; padding-bottom: 20px; }
                        .header h1 { color: #3E4095; margin: 0; }
                        .content { margin: 20px 0; }
                        .footer { margin-top: 40px; padding-top: 20px; border-top: 2px solid #ddd; text-align: center; color: #666; font-size: 12px; }
                        .status-box { background: #e3f2fd; border: 2px solid #2196F3; padding: 15px; margin: 20px 0; border-radius: 5px; }
                    </style>
                </head>
                <body>
                    <div class="header">
                        <h1>Employee UAN Onboarding</h1>
                        <p>Piramal & Abbott Employee Onboarding System</p>
                    </div>
                    
                    <h2 style="text-align: center; color: #4CAF50;">Application Submitted Successfully</h2>
                    
                    <div class="status-box">
                        <strong>Application ID:</strong> ${appId}<br>
                        <strong>Status:</strong> Under Review<br>
                        <strong>Submission Date:</strong> ${new Date().toLocaleString('en-IN')}<br>
                        <strong>Expected Processing Time:</strong> 2-3 business days
                    </div>
                    
                    <div class="footer">
                        <p>© ${new Date().getFullYear()} Karma Management Global Consulting Solutions Pvt Ltd.</p>
                    </div>
                </body>
                </html>
            `;
            
            const printWindow = window.open('', '_blank');
            printWindow.document.write(receiptHTML);
            printWindow.document.close();
            printWindow.print();
        }

        document.getElementById('documentsForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            let isValid = true;
            let errorMessages = [];
            
            // Validate that if a file is uploaded, the corresponding number is provided
            const panCard = document.getElementById('panCard');
            const panNumber = document.getElementById('panNumber');
            
            if (panCard.files.length > 0) {
                const panRegex = /^[A-Z]{5}[0-9]{4}[A-Z]{1}$/;
                if (!panNumber.value || !panRegex.test(panNumber.value)) {
                    panNumber.classList.add('is-invalid');
                    isValid = false;
                    errorMessages.push('Please enter a valid PAN number (format: ABCDE1234F)');
                } else {
                    panNumber.classList.remove('is-invalid');
                }
            }
            
            const aadharCard = document.getElementById('aadharCard');
            const aadharNumber = document.getElementById('aadharNumber');
            
            if (aadharCard.files.length > 0) {
                const aadharRegex = /^[0-9]{12}$/;
                if (!aadharNumber.value || !aadharRegex.test(aadharNumber.value)) {
                    aadharNumber.classList.add('is-invalid');
                    isValid = false;
                    errorMessages.push('Please enter a valid 12-digit Aadhar number');
                } else {
                    aadharNumber.classList.remove('is-invalid');
                }
            }
            
            // Check if at least one document is being uploaded
            const panCardFile = document.getElementById('panCard').files[0];
            const aadharCardFile = document.getElementById('aadharCard').files[0];
            const cancelledChequeFile = document.getElementById('cancelledCheque').files[0];
            
            if (!panCardFile && !aadharCardFile && !cancelledChequeFile) {
                if (confirm('No documents selected. Do you want to submit without uploading any documents? You can upload them later.')) {
                    isValid = true; // Allow submission with no documents
                } else {
                    isValid = false;
                }
            }
            
            if (!isValid) {
                if (errorMessages.length > 0) {
                    alert('Please fix the following errors:\n\n' + errorMessages.join('\n'));
                }
                return;
            }
            
            await submitDocuments();
        });
    </script>
</body>
</html>