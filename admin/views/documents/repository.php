<?php $title = 'Document Repository'; ?>
<div class="row">
    <div class="col s12">
        <div class="card">
            <div class="card-content">
                <div class="card-title">
                    <div class="row">
                        <div class="col s12 m6">
                            <h4 class="card-title">
                                <i class="material-icons left">archive</i>
                                Document Repository
                            </h4>
                            <p class="grey-text">Manage and review all application documents</p>
                        </div>
                        <div class="col s12 m6 right-align">
                            <a href="?controller=applications" class="btn blue waves-effect waves-light">
                                <i class="material-icons left">list</i>Applications
                            </a>
                            <a href="?controller=dashboard" class="btn green waves-effect waves-light">
                                <i class="material-icons left">dashboard</i>Dashboard
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Document Statistics -->
                <div class="row">
                    <?php
                    $totalDocs = count($documents);
                    $pendingDocs = count(array_filter($documents, fn($doc) => $doc['status'] === 'pending'));
                    $approvedDocs = count(array_filter($documents, fn($doc) => $doc['status'] === 'approved'));
                    $rejectedDocs = count(array_filter($documents, fn($doc) => $doc['status'] === 'rejected'));
                    ?>
                    <div class="col s12 m6 l3">
                        <div class="card-panel blue white-text center">
                            <i class="material-icons medium">description</i>
                            <h5><?php echo $totalDocs; ?></h5>
                            <p>Total Documents</p>
                        </div>
                    </div>
                    <div class="col s12 m6 l3">
                        <div class="card-panel orange white-text center">
                            <i class="material-icons medium">pending</i>
                            <h5><?php echo $pendingDocs; ?></h5>
                            <p>Pending Review</p>
                        </div>
                    </div>
                    <div class="col s12 m6 l3">
                        <div class="card-panel green white-text center">
                            <i class="material-icons medium">check_circle</i>
                            <h5><?php echo $approvedDocs; ?></h5>
                            <p>Approved</p>
                        </div>
                    </div>
                    <div class="col s12 m6 l3">
                        <div class="card-panel red white-text center">
                            <i class="material-icons medium">cancel</i>
                            <h5><?php echo $rejectedDocs; ?></h5>
                            <p>Rejected</p>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <!-- <div class="row">
                    <div class="col s12">
                        <div class="card-panel grey lighten-4">
                            <h6>Filter Documents</h6>
                            <div class="row">
                                <div class="col s12 m4">
                                    <div class="input-field">
                                        <select id="document_type">
                                            <option value="">All Document Types</option>
                                            <option value="pan" <?php echo ($filters['document_type'] ?? '') === 'pan' ? 'selected' : ''; ?>>PAN Card</option>
                                            <option value="aadhar" <?php echo ($filters['document_type'] ?? '') === 'aadhar' ? 'selected' : ''; ?>>Aadhar Card</option>
                                            <option value="cheque" <?php echo ($filters['document_type'] ?? '') === 'cheque' ? 'selected' : ''; ?>>Cancelled Cheque</option>
                                        </select>
                                        <label>Document Type</label>
                                    </div>
                                </div>
                                <div class="col s12 m4">
                                    <div class="input-field">
                                        <select id="status">
                                            <option value="">All Status</option>
                                            <option value="pending" <?php echo ($filters['status'] ?? '') === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                            <option value="approved" <?php echo ($filters['status'] ?? '') === 'approved' ? 'selected' : ''; ?>>Approved</option>
                                            <option value="rejected" <?php echo ($filters['status'] ?? '') === 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                                            <option value="unclassified" <?php echo ($filters['status'] ?? '') === 'unclassified' ? 'selected' : ''; ?>>Unclassified</option>
                                        </select>
                                        <label>Status</label>
                                    </div>
                                </div>
                                <div class="col s12 m4">
                                    <div class="input-field">
                                        <input type="text" id="application_id" value="<?php echo htmlspecialchars($filters['application_id'] ?? ''); ?>">
                                        <label for="application_id">Application ID</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col s12 right-align">
                                    <button class="btn blue waves-effect waves-light" onclick="applyFilters()">
                                        <i class="material-icons left">search</i>Apply Filters
                                    </button>
                                    <button class="btn grey waves-effect waves-light" onclick="clearFilters()">
                                        <i class="material-icons left">clear</i>Clear
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> -->

                <!-- Documents Table -->
                <div class="row">
                    <div class="col s12">
                        <table id="documentsTable" class="table table-bordered table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Application ID</th>
                                    <th>Applicant Name</th>
                                    <th>Document Type</th>
                                    <th>File Name</th>
                                    <th>Status</th>
                                    <th>Submitted</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($documents as $document): ?>
                                <tr>
                                    <td>
                                        <strong><?php echo $document['application_id']; ?></strong>
                                    </td>
                                    <td><?php echo htmlspecialchars($document['applicant_name']); ?></td>
                                    <td>
                                        <?php
                                        $docTypeNames = [
                                            'pan' => 'PAN Card',
                                            'aadhar' => 'Aadhar Card',
                                            'cheque' => 'Cancelled Cheque'
                                        ];
                                        echo $docTypeNames[$document['type']] ?? $document['type'];
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        $fileName = basename($document['file_path']);
                                        echo $fileName ?: 'No file uploaded';
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        $statusClass = [
                                            'pending' => 'orange',
                                            'approved' => 'green',
                                            'rejected' => 'red'
                                        ][$document['status']] ?? 'grey';
                                        ?>
                                        <span class="badge white-text <?php echo $statusClass; ?>">
                                            <?php echo ucfirst($document['status']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('M j, Y', strtotime($document['submitted'])); ?></td>
                                    <td>
                                        <div class="action-buttons">
                                            <?php //$fullpath = '../uploads/'.$document['application_id'].'/'.$document['file_path']; ?>
                                            <button class="btn-small blue waves-effect waves-light tooltipped"
                                                    data-tooltip="Preview Document"
                                                    onclick="previewDocument('<?php echo $document['application_id']; ?>', '<?php echo addslashes($document['applicant_name']); ?>', '<?php echo $document['type']; ?>', '<?php echo $document['file_path']; ?>', '<?php echo $document['status']; ?>', '<?php echo date('M j, Y', strtotime($document['submitted'])); ?>')">
                                                <i class="material-icons">visibility</i>
                                            </button>
                                            <a href="?controller=applications&action=view&app_id=<?php echo $document['application_id']; ?>" 
                                               class="btn-small green waves-effect waves-light tooltipped"
                                               data-tooltip="View Application">
                                                <i class="material-icons">open_in_new</i>
                                            </a>
                                            <button class="btn-small green waves-effect waves-light tooltipped"
                                                    data-tooltip="Chat about this document"
                                                    onclick="openDocumentChat('<?php echo $document['application_id']; ?>', '<?php echo $document['type']; ?>_card')">
                                                <i class="material-icons">chat</i>
                                            </button>
                                            <!-- <a href="admin/index.php?controller=support&action=document&params[]=<?= (int)$document['id'] ?>" class="btn btn-sm btn-secondary">Chat</a> -->
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>

                        <?php if (empty($documents)): ?>
                        <div class="center" style="padding: 40px;">
                            <i class="material-icons large grey-text">inbox</i>
                            <h5 class="grey-text">No documents found</h5>
                            <p class="grey-text">No documents match your current filters.</p>
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
function applyFilters() {
    const docType = document.getElementById('document_type').value;
    const status = document.getElementById('status').value;
    const appId = document.getElementById('application_id').value;
    
    let url = '?controller=documents&action=repository';
    const params = [];
    
    if (docType) params.push(`document_type=${encodeURIComponent(docType)}`);
    if (status) params.push(`status=${encodeURIComponent(status)}`);
    if (appId) params.push(`application_id=${encodeURIComponent(appId)}`);
    
    if (params.length > 0) {
        url += '&' + params.join('&');
    }
    
    window.location.href = url;
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


function clearFilters() {
    window.location.href = '?controller=documents&action=repository';
}

// Initialize components
document.addEventListener('DOMContentLoaded', function() {
    const selects = document.querySelectorAll('select');
    M.FormSelect.init(selects);
    
    const tooltips = document.querySelectorAll('.tooltipped');
    M.Tooltip.init(tooltips);
});
function openDocumentChat(appId, docType) {
    // Convert document type to match your database (pan -> pan_card, aadhar -> aadhar_card, etc.)
    const documentTypeMap = {
        'pan': 'pan_card',
        'aadhar': 'aadhar_card', 
        'cheque': 'cancelled_cheque'
    };
    
    const mappedDocType = documentTypeMap[docType] || docType;
    window.location.href = `?controller=support&action=documentChat&app_id=${appId}&doc_type=${mappedDocType}`;
}
document.addEventListener('DOMContentLoaded', function() {
    let table = new DataTable('#documentsTable');
});
</script>