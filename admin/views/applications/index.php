<?php $title = 'Applications Management'; ?>
<div class="row">
    <div class="col s12">
        <div class="card">
            <div class="card-content">
                <div class="card-title">
                    <div class="row">
                        <div class="col s12 m6">
                            <h4 class="card-title">
                                <i class="material-icons left">list_alt</i>
                                Applications Management
                            </h4>
                            <p class="grey-text">Manage and review all submitted applications</p>
                        </div>
                        <div class="col s12 m6 right-align">
                            <div class="row valign-wrapper">
                                <div class="col s12">
                                    <a href="?controller=dashboard" class="btn blue darken-3 waves-effect waves-light">
                                        <i class="material-icons left">dashboard</i>Dashboard
                                    </a>
                                    <a href="?controller=documents&action=repository" class="btn green waves-effect waves-light">
                                        <i class="material-icons left">archive</i>Document Repository
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Stats -->
                <div class="row">
                    <?php
                    $stats = [
                        'total' => count($applications),
                        'pending' => count(array_filter($applications, fn($app) => $app['admin_status'] === 'pending')),
                        'approved' => count(array_filter($applications, fn($app) => $app['admin_status'] === 'approved')),
                        'rejected' => count(array_filter($applications, fn($app) => $app['admin_status'] === 'rejected'))
                    ];
                    ?>
                    <div class="col s12 m6 l3">
                        <div class="card-panel blue white-text center">
                            <i class="material-icons medium">description</i>
                            <h5><?php echo $stats['total']; ?></h5>
                            <p>Total Applications</p>
                        </div>
                    </div>
                    <div class="col s12 m6 l3">
                        <div class="card-panel orange white-text center">
                            <i class="material-icons medium">pending</i>
                            <h5><?php echo $stats['pending']; ?></h5>
                            <p>Pending</p>
                        </div>
                    </div>
                    <div class="col s12 m6 l3">
                        <div class="card-panel green white-text center">
                            <i class="material-icons medium">check_circle</i>
                            <h5><?php echo $stats['approved']; ?></h5>
                            <p>Approved</p>
                        </div>
                    </div>
                    <div class="col s12 m6 l3">
                        <div class="card-panel red white-text center">
                            <i class="material-icons medium">cancel</i>
                            <h5><?php echo $stats['rejected']; ?></h5>
                            <p>Rejected</p>
                        </div>
                    </div>
                </div>

                <!-- Search and Filters -->
                <!-- <div class="row">
                    <div class="col s12">
                        <div class="card-panel grey lighten-4">
                            <div class="row">
                                <div class="col s12 m4">
                                    <div class="input-field">
                                        <input type="text" id="search" value="<?php echo htmlspecialchars($search ?? ''); ?>">
                                        <label for="search">Search Applications</label>
                                    </div>
                                </div>
                                <div class="col s12 m4">
                                    <div class="input-field">
                                        <select id="status_filter">
                                            <option value="">All Status</option>
                                            <option value="pending" <?php echo ($status ?? '') === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                            <option value="under_review" <?php echo ($status ?? '') === 'under_review' ? 'selected' : ''; ?>>Under Review</option>
                                            <option value="approved" <?php echo ($status ?? '') === 'approved' ? 'selected' : ''; ?>>Approved</option>
                                            <option value="rejected" <?php echo ($status ?? '') === 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                                        </select>
                                        <label>Filter by Status</label>
                                    </div>
                                </div>
                                <div class="col s12 m4">
                                    <button class="btn blue waves-effect waves-light" onclick="applyFilters()" style="margin-top: 20px;">
                                        <i class="material-icons left">search</i>Apply Filters
                                    </button>
                                    <button class="btn grey waves-effect waves-light" onclick="clearFilters()" style="margin-top: 20px;">
                                        <i class="material-icons left">clear</i>Clear
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> -->

                <!-- Applications Table -->
                <div class="row">
                    <div class="col s12">
                        <!-- <table class="striped highlight responsive-table"> -->
                        <table id="applicationsTable" class="table table-bordered table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Application ID</th>
                                    <th>Full Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Status</th>
                                    <th>Documents</th>
                                    <th>Submitted</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($applications as $application): ?>
                                <tr>
                                    <td>
                                        <strong><?php echo $application['application_id']; ?></strong>
                                    </td>
                                    <td><?php echo htmlspecialchars($application['full_name']); ?></td>
                                    <td><?php echo htmlspecialchars($application['email']); ?></td>
                                    <td><?php echo htmlspecialchars($application['phone']); ?></td>
                                    <td>
                                        <?php
                                        $statusClass = [
                                            'pending' => 'orange',
                                            'under_review' => 'blue',
                                            'approved' => 'green',
                                            'rejected' => 'red'
                                        ][$application['admin_status']] ?? 'grey';
                                        ?>
                                        <span class="badge white-text <?php echo $statusClass; ?>">
                                            <?php echo ucfirst($application['admin_status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="document-actions">
                                            <span class="doc-btn view <?php echo $application['pan_status']; ?>" 
                                                  onclick="previewDocument('<?php echo $application['application_id']; ?>', '<?php echo addslashes($application['full_name']); ?>', 'pan', '<?php echo $application['pan_card']; ?>', '<?php echo $application['pan_status']; ?>', '<?php echo date('M j, Y', strtotime($application['created_at'])); ?>')">
                                                PAN
                                            </span>
                                            <span class="doc-btn view <?php echo $application['aadhar_status']; ?>" 
                                                  onclick="previewDocument('<?php echo $application['application_id']; ?>', '<?php echo addslashes($application['full_name']); ?>', 'aadhar', '<?php echo $application['aadhar_card']; ?>', '<?php echo $application['aadhar_status']; ?>', '<?php echo date('M j, Y', strtotime($application['created_at'])); ?>')">
                                                Aadhar
                                            </span>
                                            <span class="doc-btn view <?php echo $application['cheque_status']; ?>" 
                                                  onclick="previewDocument('<?php echo $application['application_id']; ?>', '<?php echo addslashes($application['full_name']); ?>', 'cheque', '<?php echo $application['cancelled_cheque']; ?>', '<?php echo $application['cheque_status']; ?>', '<?php echo date('M j, Y', strtotime($application['created_at'])); ?>')">
                                                Cheque
                                            </span>
                                        </div>
                                    </td>
                                    <td><?php echo date('M j, Y', strtotime($application['created_at'])); ?></td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="?controller=applications&action=view&id=<?php echo $application['id']; ?>" 
                                                class="btn-small blue waves-effect waves-light tooltipped" 
                                                data-tooltip="View Details">
                                                    <i class="material-icons">visibility</i>
                                            </a>
                                            <?php if ($_SESSION['user_role'] !== 'viewer'): ?>
                                            <a href="?controller=applications&action=edit&id=<?php echo $application['id']; ?>" 
                                               class="btn-small orange waves-effect waves-light tooltipped" 
                                               data-tooltip="Edit Application">
                                                <i class="material-icons">edit</i>
                                            </a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>

                        <?php if (empty($applications)): ?>
                        <div class="center" style="padding: 40px;">
                            <i class="material-icons large grey-text">inbox</i>
                            <h5 class="grey-text">No applications found</h5>
                            <p class="grey-text">No applications match your current filters.</p>
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

function previewDocumentOld(appId, applicantName, docType, filePath, currentStatus, submittedDate) {
    currentDocument = {
        appId: appId,
        applicantName: applicantName,
        docType: docType,
        filePath: filePath,
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
    document.getElementById('downloadBtn').href = filePath;
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
    if (!filePath || filePath === 'null') {
        document.getElementById('documentLoading').style.display = 'none';
        document.getElementById('documentError').style.display = 'block';
        return;
    }

    if (filePath.toLowerCase().endsWith('.pdf')) {
        setTimeout(() => {
            document.getElementById('documentLoading').style.display = 'none';
            document.getElementById('documentPdf').style.display = 'block';
            document.getElementById('documentPdf').src = filePath;
        }, 1000);
    } else {
        const img = new Image();
        img.onload = function() {
            document.getElementById('documentLoading').style.display = 'none';
            document.getElementById('documentImage').style.display = 'block';
            document.getElementById('documentImage').src = filePath;
        };
        img.onerror = function() {
            document.getElementById('documentLoading').style.display = 'none';
            document.getElementById('documentError').style.display = 'block';
        };
        img.src = filePath;
    }
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

// Initialize tooltips
document.addEventListener('DOMContentLoaded', function() {
    const tooltips = document.querySelectorAll('.tooltipped');
    M.Tooltip.init(tooltips);
});

document.addEventListener('DOMContentLoaded', function() {
    let table = new DataTable('#applicationsTable');
});


</script>
