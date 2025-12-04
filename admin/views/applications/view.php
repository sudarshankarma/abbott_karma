<?php $title = 'Application Details - ' . $application['application_id']; ?>
<div class="row">
    <div class="col s12">
        <div class="card">
            <div class="card-content">
                <div class="card-title">
                    <div class="row valign-wrapper">
                        <div class="col s12 m6">
                            <h4>
                                <i class="material-icons left">description</i>
                                Application Details
                            </h4>
                            <p class="grey-text">Application ID: <strong><?php echo $application['application_id']; ?></strong></p>
                        </div>
                        <div class="col s12 m6 right-align">
                            <a href="?controller=applications" class="btn blue waves-effect waves-light">
                                <i class="material-icons left">arrow_back</i>Back to List
                            </a>
                            <?php if ($_SESSION['user_role'] !== 'viewer'): ?>
                            <a href="?controller=applications&action=edit&id=<?php echo $application['id']; ?>" class="btn orange waves-effect waves-light">
                                <i class="material-icons left">edit</i>Edit Application
                            </a>
                            <?php endif; ?>
                            <a href="?controller=documents&action=repository&application_id=<?php echo $application['application_id']; ?>" class="btn green waves-effect waves-light">
                                <i class="material-icons left">archive</i>View Documents
                            </a>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Personal Information -->
                    <div class="col s12 m6">
                        <div class="card-panel">
                            <h5 class="card-title">
                                <i class="material-icons left">person</i>
                                Personal Information
                            </h5>
                            <div class="divider"></div>
                            <div class="section">
                                <table class="striped">
                                    <tbody>
                                        <tr>
                                            <td><strong>Full Name</strong></td>
                                            <td><?php echo htmlspecialchars($application['full_name']); ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Email</strong></td>
                                            <td><?php echo htmlspecialchars($application['email']); ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Phone</strong></td>
                                            <td><?php echo htmlspecialchars($application['phone']); ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>WhatsApp</strong></td>
                                            <td><?php echo htmlspecialchars($application['whatsapp']); ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>PAN Number</strong></td>
                                            <td><?php echo htmlspecialchars($application['pan_number']); ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Aadhar Number</strong></td>
                                            <td><?php echo htmlspecialchars($application['aadhar_number']); ?></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Company Information -->
                        <div class="card-panel">
                            <h5 class="card-title">
                                <i class="material-icons left">business</i>
                                Company Information
                            </h5>
                            <div class="divider"></div>
                            <div class="section">
                                <table class="striped">
                                    <tbody>
                                        <tr>
                                            <td><strong>Piramal UAN</strong></td>
                                            <td><?= htmlspecialchars($application['piramal_uan'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Abbott UAN</strong></td>
                                            <td><?= htmlspecialchars($application['abbott_uan'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Piramal ID</strong></td>
                                            <td><?= htmlspecialchars($application['piramal_id'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Abbott ID</strong></td>
                                            <td><?= htmlspecialchars($application['abbott_id'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Documents & Status -->
                    <div class="col s12 m6">
                        <!-- Application Status -->
                        <div class="card-panel">
                            <h5 class="card-title">
                                <i class="material-icons left">assessment</i>
                                Application Status
                            </h5>
                            <div class="divider"></div>
                            <div class="section">
                                <?php
                                $statusClass = [
                                    'pending' => 'orange',
                                    'under_review' => 'blue',
                                    'approved' => 'green',
                                    'rejected' => 'red'
                                ][$application['admin_status']] ?? 'grey';
                                ?>
                                <div class="center">
                                    <span class="badge large white-text <?php echo $statusClass; ?>" style="font-size: 1.2rem; padding: 0px 20px;">
                                        <?php echo strtoupper($application['admin_status']); ?>
                                    </span>
                                </div>
                                <p class="center grey-text">
                                    Submitted: <?php echo date('F j, Y g:i A', strtotime($application['created_at'])); ?>
                                </p>
                            </div>
                        </div>

                        <!-- Document Status -->
                        <div class="card-panel">
                            <h5 class="card-title">
                                <i class="material-icons left">folder</i>
                                Document Status
                            </h5>
                            <div class="divider"></div>
                            
                            <!-- PAN Card -->
                            <div class="section">
                                <h6>PAN Card</h6>
                                <div class="row valign-wrapper">
                                    <div class="col s8">
                                        <span class="badge <?php echo $application['pan_status'] === 'approved' ? 'green' : ($application['pan_status'] === 'rejected' ? 'red' : 'orange'); ?> white-text">
                                            <?php echo ucfirst($application['pan_status']); ?>
                                        </span>
                                        <p class="grey-text">PAN: <?php echo htmlspecialchars($application['pan_number']); ?></p>
                                    </div>
                                    <div class="col s4 right-align">
                                        <!-- <button class="btn-small blue waves-effect waves-light" 
                                                onclick="previewDocument('<?php echo $application['application_id']; ?>', '<?php echo addslashes($application['full_name']); ?>', 'pan', '<?php echo $application['pan_card']; ?>', '<?php echo $application['pan_status']; ?>', '<?php echo date('M j, Y', strtotime($application['created_at'])); ?>')">
                                            <i class="material-icons">visibility</i>
                                        </button> -->
                                      <button class="btn-small green waves-effect waves-light tooltipped" 
                                                data-tooltip="Chat about this document"
                                                onclick="openDocumentChat('<?php echo $application['application_id']; ?>', 'pan_card')">
                                            <i class="material-icons">chat</i>
                                        </button>
                                        <button class="btn-small blue waves-effect waves-light tooltipped" 
                                            data-tooltip="Preview Document" 
                                            onclick="previewDocument('<?php echo $application['application_id']; ?>', '<?php echo addslashes($application['full_name']); ?>', 'pan', '<?php echo $application['pan_card']; ?>', '<?php echo $application['pan_status']; ?>', '<?php echo date('M j, Y', strtotime($application['created_at'])); ?>')">
                                        <i class="material-icons">visibility</i>
                                    </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Aadhar Card -->
                            <div class="section">
                                <h6>Aadhar Card</h6>
                                <div class="row valign-wrapper">
                                    <div class="col s8">
                                        <span class="badge <?php echo $application['aadhar_status'] === 'approved' ? 'green' : ($application['aadhar_status'] === 'rejected' ? 'red' : 'orange'); ?> white-text">
                                            <?php echo ucfirst($application['aadhar_status']); ?>
                                        </span>
                                        <p class="grey-text">Aadhar: <?php echo htmlspecialchars($application['aadhar_number'] ?? '', ENT_QUOTES, 'UTF-8'); ?></p>
                                    </div>
                                    <div class="col s4 right-align">
                                        <button class="btn-small green waves-effect waves-light tooltipped" 
                                                data-tooltip="Chat" 
                                                onclick="openDocumentChat('<?php echo $application['application_id']; ?>', 'aadhar')">
                                            <i class="material-icons">chat</i>
                                        </button>
                                        <button class="btn-small blue waves-effect waves-light"
                                                onclick="previewDocument('<?php echo $application['application_id']; ?>', '<?php echo addslashes($application['full_name']); ?>', 'aadhar', '<?php echo $application['aadhar_card']; ?>', '<?php echo $application['aadhar_status']; ?>', '<?php echo date('M j, Y', strtotime($application['created_at'])); ?>')">
                                            <i class="material-icons">visibility</i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Cancelled Cheque -->
                            <div class="section">
                                <h6>Cancelled Cheque</h6>
                                <div class="row valign-wrapper">
                                    <div class="col s8">
                                        <span class="badge <?php echo $application['cheque_status'] === 'approved' ? 'green' : ($application['cheque_status'] === 'rejected' ? 'red' : 'orange'); ?> white-text">
                                            <?php echo ucfirst($application['cheque_status']); ?>
                                        </span>
                                        <p class="grey-text">Bank verification document</p>
                                    </div>
                                    <div class="col s4 right-align">
                                        <button class="btn-small green waves-effect waves-light tooltipped" 
                                                data-tooltip="Chat" 
                                                onclick="openDocumentChat('<?php echo $application['application_id']; ?>', 'cheque')">
                                            <i class="material-icons">chat</i>
                                        </button>
                                        <button class="btn-small blue waves-effect waves-light"
                                                onclick="previewDocument('<?php echo $application['application_id']; ?>', '<?php echo addslashes($application['full_name']); ?>', 'cheque', '<?php echo $application['cancelled_cheque']; ?>', '<?php echo $application['cheque_status']; ?>', '<?php echo date('M j, Y', strtotime($application['created_at'])); ?>')">
                                            <i class="material-icons">visibility</i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Admin Notes -->
                        <?php if (!empty($application['admin_notes'])): ?>
                        <div class="card-panel">
                            <h5 class="card-title">
                                <i class="material-icons left">note</i>
                                Admin Notes
                            </h5>
                            <div class="divider"></div>
                            <div class="section">
                                <p><?php echo nl2br(htmlspecialchars($application['admin_notes'])); ?></p>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Document Preview Modal -->
<div id="documentModal" class="modal modal-fixed-footer">
    <div class="modal-content">
        <h4 id="modalTitle">Document Preview</h4>
        <div class="row">
            <div class="col s8">
                <div class="document-preview center">
                    <img id="documentImage" class="responsive-img" style="max-height: 500px; display: none;">
                    <iframe id="documentPdf" style="width: 100%; height: 500px; display: none;"></iframe>
                    <div id="documentLoading" class="center" style="padding: 100px;">
                        <div class="preloader-wrapper big active">
                            <div class="spinner-layer spinner-blue-only">
                                <div class="circle-clipper left">
                                    <div class="circle"></div>
                                </div>
                                <div class="gap-patch">
                                    <div class="circle"></div>
                                </div>
                                <div class="circle-clipper right">
                                    <div class="circle"></div>
                                </div>
                            </div>
                        </div>
                        <p>Loading document...</p>
                    </div>
                    <div id="documentError" class="center" style="display: none; padding: 100px;">
                        <i class="material-icons large grey-text">error_outline</i>
                        <p>Document not available for preview</p>
                    </div>
                </div>
            </div>
            <div class="col s4">
                <div class="card-panel">
                    <h6>Document Information</h6>
                    <div class="divider"></div>
                    <p><strong>Application ID:</strong><br><span id="infoAppId">-</span></p>
                    <p><strong>Applicant Name:</strong><br><span id="infoApplicant">-</span></p>
                    <p><strong>Document Type:</strong><br><span id="infoDocType">-</span></p>
                    <p><strong>Status:</strong><br><span id="infoStatus">-</span></p>
                    <p><strong>Submitted:</strong><br><span id="infoSubmitted">-</span></p>
                    
                    <div class="divider" style="margin: 20px 0;"></div>
                    
                    <h6>Quick Actions</h6>
                    <div class="action-buttons-vertical">
                        <button class="btn green waves-effect waves-light btn-small" onclick="approveCurrentDocument()">
                            <i class="material-icons left">check</i>Approve
                        </button>
                        <button class="btn red waves-effect waves-light btn-small" onclick="rejectCurrentDocument()">
                            <i class="material-icons left">close</i>Reject
                        </button>
                        <button class="btn orange waves-effect waves-light btn-small" onclick="pendingCurrentDocument()">
                            <i class="material-icons left">schedule</i>Pending
                        </button>
                        <a id="downloadBtn" class="btn blue waves-effect waves-light btn-small" download>
                            <i class="material-icons left">download</i>Download
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <a href="#!" class="modal-close waves-effect waves-red btn-flat">Close</a>
    </div>
</div>

<script>
let currentDocument = {};

function applyFilters() {
    const search = document.getElementById('search').value;
    const status = document.getElementById('status_filter').value;
    
    let url = '?controller=applications&action=index';
    const params = [];
    
    if (search) params.push(`search=${encodeURIComponent(search)}`);
    if (status) params.push(`status=${encodeURIComponent(status)}`);
    
    if (params.length > 0) {
        url += '&' + params.join('&');
    }
    
    window.location.href = url;
}

function clearFilters() {
    window.location.href = '?controller=applications&action=index';
}

function previewDocument(appId, applicantName, docType, filePath, currentStatus, submittedDate) {
    const fullFilePath = `../uploads/${appId}/${filePath}`;
    currentDocument = {
        appId: appId,
        applicantName: applicantName,
        docType: docType,
        filePath: fullFilePath,
        currentStatus: currentStatus,
        submittedDate: submittedDate
    };

    // Update modal information
    document.getElementById('modalTitle').textContent = `${docType.toUpperCase()} Document - ${appId}`;
    document.getElementById('infoAppId').textContent = appId;
    document.getElementById('infoApplicant').textContent = applicantName;
    document.getElementById('infoDocType').textContent = docType.toUpperCase();
    document.getElementById('infoSubmitted').textContent = submittedDate;
    
    // Update status badge
    const statusElement = document.getElementById('infoStatus');
    statusElement.textContent = currentStatus.charAt(0).toUpperCase() + currentStatus.slice(1);
    statusElement.className = `badge ${currentStatus === 'approved' ? 'green' : currentStatus === 'rejected' ? 'red' : 'orange'} white-text`;
    
    // Set download link
    
    document.getElementById('downloadBtn').href = fullFilePath;
    document.getElementById('downloadBtn').download = `${appId}_${docType}`;

    // Show loading, hide other content
    document.getElementById('documentLoading').style.display = 'block';
    document.getElementById('documentImage').style.display = 'none';
    document.getElementById('documentPdf').style.display = 'none';
    document.getElementById('documentError').style.display = 'none';

    // Open modal
    const modal = M.Modal.getInstance(document.getElementById('documentModal'));
    modal.open();

    // Load document
    if (!fullFilePath || fullFilePath === 'null') {
        document.getElementById('documentLoading').style.display = 'none';
        document.getElementById('documentError').style.display = 'block';
        return;
    }

    if (fullFilePath.toLowerCase().endsWith('.pdf')) {
        setTimeout(() => {
            document.getElementById('documentLoading').style.display = 'none';
            document.getElementById('documentPdf').style.display = 'block';
            document.getElementById('documentPdf').src = fullFilePath;
        }, 1000);
    } else {
        const img = new Image();
        img.onload = function() {
            document.getElementById('documentLoading').style.display = 'none';
            document.getElementById('documentImage').style.display = 'block';
            document.getElementById('documentImage').src = fullFilePath;
        };
        img.onerror = function() {
            document.getElementById('documentLoading').style.display = 'none';
            document.getElementById('documentError').style.display = 'block';
        };
        img.src = fullFilePath;
    }
}

function approveCurrentDocument() {
    updateDocumentStatus(currentDocument.appId, currentDocument.docType, 'approved');
}

function rejectCurrentDocument() {
    updateDocumentStatus(currentDocument.appId, currentDocument.docType, 'rejected');
}

function pendingCurrentDocument() {
    updateDocumentStatus(currentDocument.appId, currentDocument.docType, 'pending');
}

function updateDocumentStatus(appId, docType, status) {
    if (!confirm(`Are you sure you want to mark this ${docType} document as ${status}?`)) return;

    $.post('?controller=applications&action=updateDocument', {
        application_id: appId,
        document_type: docType,
        status: status
    }, function(response) {
        const result = JSON.parse(response);
        if (result.success) {
            M.toast({html: 'Document status updated successfully!', classes: 'green'});
            // Close modal and reload page
            const modal = M.Modal.getInstance(document.getElementById('documentModal'));
            modal.close();
            setTimeout(() => location.reload(), 1000);
        } else {
            M.toast({html: 'Error: ' + result.message, classes: 'red'});
        }
    });
}
function openDocumentChat(appId, docType) {
    window.location.href = `?controller=support&action=documentChat&app_id=${appId}&doc_type=${docType}_card`;
}
// Initialize tooltips
document.addEventListener('DOMContentLoaded', function() {
    const tooltips = document.querySelectorAll('.tooltipped');
    M.Tooltip.init(tooltips);
});
</script>