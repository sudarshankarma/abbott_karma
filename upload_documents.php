<?php
require_once 'config.php';

// Check if user has submitted personal details
if (!isset($_SESSION['application_data']) && !isset($_GET['application_id'])) {
    header('Location: verify_phone.php');
    exit;
}

// Get application data
$applicationData = $_SESSION['application_data'] ?? null;
$applicationId = $_GET['application_id'] ?? ($applicationData['application_id'] ?? '');

if (!$applicationId) {
    header('Location: verify_phone.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Documents - Employee Registration</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.8/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-blue: #3E4095;
            --accent-orange: #F26B35;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #faf9f8;
        }

        .logo-header {
            padding: 20px;
            margin-bottom: 20px;
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
        }

        .form-container {
            background: white;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }

        .application-info {
            background: linear-gradient(135deg, var(--primary-blue), #2d2f70);
            color: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 25px;
        }

        .document-upload-card {
            background: #ffffff;
            border: 2px dashed #dee2e6;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 25px;
            transition: all 0.3s ease;
        }

        .document-upload-card:hover {
            border-color: var(--primary-blue);
            box-shadow: 0 4px 12px rgba(62, 64, 149, 0.1);
        }

        .document-upload-card.has-file {
            border-color: var(--step-active);
            background: #f0f9f0;
        }

        .document-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, var(--primary-blue), var(--accent-orange));
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
            margin-bottom: 15px;
        }

        .document-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--primary-blue);
            margin-bottom: 10px;
        }

        .btn-primary {
            background: var(--primary-blue);
            border-color: var(--primary-blue);
        }

        .btn-primary:hover {
            background: #2d2f70;
            border-color: #2d2f70;
        }

        .uploaded-documents {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-top: 30px;
        }

        .uploaded-file {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px;
            background: white;
            border-radius: 6px;
            margin-bottom: 10px;
            border-left: 4px solid var(--primary-blue);
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <!-- Logo Header -->
                 <link rel="shortcut icon" href="images/abbott_favicon.ico" type="image/x-icon"/>
                <div class="logo-header">
                    <div class="logo-container logo-left">
                        <div class="logo-placeholder" style="background-image: url('images/karma_logo.png');"></div>
                    </div>
                    <div class="logo-container logo-right">
                        <div class="logo-placeholder" style="background-image: url('images/Abbott_Laboratories_logo.png');"></div>
                    </div>
                </div>

                <!-- Application Information -->
                <div class="application-info">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h4 class="mb-2"><i class="fas fa-file-alt me-2"></i>Upload Required Documents</h4>
                            <p class="mb-0">Application ID: <strong><?php echo htmlspecialchars($applicationId); ?></strong></p>
                        </div>
                        <div class="col-md-4 text-end">
                            <a href="form.php" class="btn btn-outline-light btn-sm">
                                <i class="fas fa-edit me-1"></i>Edit Personal Details
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Uploaded Documents Summary -->
                <div class="uploaded-documents" id="uploadedDocuments" style="display: none;">
                    <h5 class="text-primary mb-3"><i class="fas fa-check-circle me-2"></i>Uploaded Documents</h5>
                    <div id="uploadedFilesList"></div>
                </div>

                <!-- Documents Upload Form -->
                <div class="form-container">
                    <form id="documentsForm" novalidate>
                        <div class="mb-4">
                            <h4 class="text-primary"><i class="fas fa-upload me-2"></i>Upload Required Documents</h4>
                            <p class="text-muted">Please upload clear copies of the required documents. Maximum file size: 10MB per file.</p>
                        </div>

                        <!-- PAN Card -->
                        <div class="document-upload-card" id="panCardSection">
                            <div class="row align-items-start">
                                <div class="col-md-1">
                                    <div class="document-icon">
                                        <i class="fas fa-id-card"></i>
                                    </div>
                                </div>
                                <div class="col-md-11">
                                    <div class="document-title">PAN Card</div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label required-field">PAN Card Number</label>
                                        <div class="input-with-icon">
                                            <i class="fas fa-credit-card"></i>
                                            <input 
                                                type="text" 
                                                class="form-control" 
                                                id="panNumber" 
                                                name="panNumber" 
                                                maxlength="10"
                                                placeholder="ABCDE1234F"
                                                required
                                                style="text-transform: uppercase;">
                                        </div>
                                        <small class="text-muted">Format: 5 letters, 4 digits, 1 letter (e.g., ABCDE1234F)</small>
                                        <div class="invalid-feedback">Please enter a valid PAN number</div>
                                        <div class="valid-feedback">Valid PAN number!</div>
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
                                <div class="col-md-1">
                                    <div class="document-icon">
                                        <i class="fas fa-address-card"></i>
                                    </div>
                                </div>
                                <div class="col-md-11">
                                    <div class="document-title">Aadhar Card</div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label required-field">Aadhar Card Number</label>
                                        <div class="input-with-icon">
                                            <i class="fas fa-fingerprint"></i>
                                            <input 
                                                type="text" 
                                                class="form-control" 
                                                id="aadharNumber" 
                                                name="aadharNumber" 
                                                maxlength="12"
                                                placeholder="123456789012"
                                                required>
                                        </div>
                                        <small class="text-muted">Enter 12-digit Aadhar number</small>
                                        <div class="invalid-feedback">Please enter a valid 12-digit Aadhar number</div>
                                        <div class="valid-feedback">Valid Aadhar number!</div>
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
                                <div class="col-md-1">
                                    <div class="document-icon">
                                        <i class="fas fa-university"></i>
                                    </div>
                                </div>
                                <div class="col-md-11">
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

                        <!-- Submit Button -->
                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary btn-lg w-100" id="submitBtn">
                                <i class="fas fa-check-circle me-2"></i>
                                <span id="submitBtnText">Complete Application</span>
                                <span class="spinner-border spinner-border-sm ms-2" role="status" id="submitSpinner" style="display:none;"></span>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Progress Info -->
                <div class="text-center text-muted">
                    <small><i class="fas fa-info-circle me-1"></i>You can come back later to complete document upload</small>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.8/js/bootstrap.bundle.min.js"></script>
    <script>
        const applicationId = '<?php echo $applicationId; ?>';
        let uploadedDocuments = [];

        document.addEventListener('DOMContentLoaded', function() {
            // Check for existing uploaded documents
            checkExistingDocuments();
            
            // PAN number validation
            document.getElementById('panNumber').addEventListener('input', function() {
                this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
                validatePANNumber(this);
            });

            // Aadhar number validation
            document.getElementById('aadharNumber').addEventListener('input', function() {
                this.value = this.value.replace(/[^0-9]/g, '');
                validateAadharNumber(this);
            });
        });

        function validatePANNumber(input) {
            const panRegex = /^[A-Z]{5}[0-9]{4}[A-Z]{1}$/;
            const value = input.value;
            
            if (!value) {
                input.classList.remove('is-valid', 'is-invalid');
                return false;
            }
            
            if (panRegex.test(value)) {
                input.classList.remove('is-invalid');
                input.classList.add('is-valid');
                return true;
            } else {
                input.classList.remove('is-valid');
                input.classList.add('is-invalid');
                return false;
            }
        }

        function validateAadharNumber(input) {
            const aadharRegex = /^[0-9]{12}$/;
            const value = input.value;
            
            if (!value) {
                input.classList.remove('is-valid', 'is-invalid');
                return false;
            }
            
            if (aadharRegex.test(value)) {
                input.classList.remove('is-invalid');
                input.classList.add('is-valid');
                return true;
            } else {
                input.classList.remove('is-valid');
                input.classList.add('is-invalid');
                return false;
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
            input.classList.add('is-valid');
            
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

        async function checkExistingDocuments() {
            try {
                const response = await fetch('get_documents.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `application_id=${applicationId}`
                });
                
                const data = await response.json();
                
                if (data.success && data.documents) {
                    uploadedDocuments = data.documents;
                    displayUploadedDocuments();
                }
            } catch (error) {
                console.error('Error fetching documents:', error);
            }
        }

        function displayUploadedDocuments() {
            if (uploadedDocuments.length === 0) return;
            
            const container = document.getElementById('uploadedDocuments');
            const list = document.getElementById('uploadedFilesList');
            
            list.innerHTML = '';
            
            uploadedDocuments.forEach(doc => {
                const fileItem = document.createElement('div');
                fileItem.className = 'uploaded-file';
                fileItem.innerHTML = `
                    <div>
                        <i class="fas fa-file-${doc.type === 'pdf' ? 'pdf' : 'image'} text-primary me-2"></i>
                        <strong>${doc.document_type}</strong>
                        <small class="text-muted ms-2">${doc.filename}</small>
                    </div>
                    <div>
                        <small class="text-success me-2">Uploaded</small>
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeDocument('${doc.document_type}')">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                `;
                list.appendChild(fileItem);
            });
            
            container.style.display = 'block';
        }

        document.getElementById('documentsForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            // Validate form
            if (!validateForm()) {
                alert('Please fill all required fields correctly.');
                return;
            }
            
            await submitDocuments();
        });

        function validateForm() {
            let isValid = true;
            
            // Validate PAN number
            const panNumber = document.getElementById('panNumber');
            if (!validatePANNumber(panNumber)) {
                isValid = false;
            }
            
            // Validate Aadhar number
            const aadharNumber = document.getElementById('aadharNumber');
            if (!validateAadharNumber(aadharNumber)) {
                isValid = false;
            }
            
            // Validate file uploads
            const panCard = document.getElementById('panCard');
            if (!panCard.files.length) {
                panCard.classList.add('is-invalid');
                isValid = false;
            }
            
            const aadharCard = document.getElementById('aadharCard');
            if (!aadharCard.files.length) {
                aadharCard.classList.add('is-invalid');
                isValid = false;
            }
            
            const cancelledCheque = document.getElementById('cancelledCheque');
            if (!cancelledCheque.files.length) {
                cancelledCheque.classList.add('is-invalid');
                isValid = false;
            }
            
            return isValid;
        }

        async function submitDocuments() {
            const formData = new FormData();
            formData.append('application_id', applicationId);
            formData.append('pan_number', document.getElementById('panNumber').value);
            formData.append('aadhar_number', document.getElementById('aadharNumber').value);
            formData.append('pan_card', document.getElementById('panCard').files[0]);
            formData.append('aadhar_card', document.getElementById('aadharCard').files[0]);
            formData.append('cancelled_cheque', document.getElementById('cancelledCheque').files[0]);
            
            const submitBtn = document.getElementById('submitBtn');
            const submitBtnText = document.getElementById('submitBtnText');
            const submitSpinner = document.getElementById('submitSpinner');
            
            submitBtn.disabled = true;
            submitBtnText.textContent = 'Submitting...';
            submitSpinner.style.display = 'inline-block';
            
            try {
                const response = await fetch('submit_documents.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    // Redirect to success page
                    window.location.href = 'complete.php?application_id=' + applicationId;
                } else {
                    alert('Error: ' + (result.message || 'Failed to submit documents'));
                    submitBtn.disabled = false;
                    submitBtnText.textContent = 'Complete Application';
                    submitSpinner.style.display = 'none';
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred while submitting documents. Please try again.');
                submitBtn.disabled = false;
                submitBtnText.textContent = 'Complete Application';
                submitSpinner.style.display = 'none';
            }
        }

        function removeDocument(documentType) {
            if (confirm('Are you sure you want to remove this document?')) {
                // Implement document removal logic
                console.log('Remove document:', documentType);
            }
        }
    </script>
</body>
</html>