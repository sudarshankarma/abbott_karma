<?php
require_once 'config.php';

// Check if user is verified
if (!isset($_SESSION['verified_phone'])) {
    header('Location: index.php');
    exit;
}

$verifiedPhone = $_SESSION['verified_phone'];

// Get application data
$applicationData = null;
try {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT * FROM applications WHERE phone = ?");
    $stmt->execute([$verifiedPhone]);
    $applicationData = $stmt->fetch();
    
    if (!$applicationData) {
        header('Location: form.php');
        exit;
    }
} catch (Exception $e) {
    error_log("Error fetching application data: " . $e->getMessage());
}

// Get support tickets
$supportTickets = [];
try {
    $stmt = $pdo->prepare("SELECT * FROM support_tickets WHERE application_id = ? ORDER BY created_at DESC");
    $stmt->execute([$applicationData['application_id']]);
    $supportTickets = $stmt->fetchAll();
} catch (Exception $e) {
    // Table might not exist yet, that's okay
    error_log("Error fetching support tickets: " . $e->getMessage());
}
include 'header.php';
?>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Employee Registration</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.8/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-blue: #3E4095;
            --accent-orange: #F26B35;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
        }

        .dashboard-header {
            /* background: linear-gradient(135deg, var(--primary-blue) 0%, #2d2f70 100%); */
            color: black;
            padding: 30px 0;
            margin-bottom: 30px;
        }

        .status-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            border-left: 4px solid var(--primary-blue);
        }

        .document-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .document-status {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: bold;
            color: white;
        }

        .btn-primary {
            background: var(--primary-blue);
            border-color: var(--primary-blue);
        }

        .btn-primary:hover {
            background: #2d2f70;
            border-color: #2d2f70;
        }

        .comment-box {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            margin-top: 10px;
            font-size: 0.9rem;
        }

        .chat-container {
            max-height: 400px;
            overflow-y: auto;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 15px;
            background: #f8f9fa;
        }

        .chat-message {
            margin-bottom: 15px;
            padding: 10px;
            border-radius: 8px;
            position: relative;
        }

        .user-message {
            background: #e3f2fd;
            margin-left: 20%;
            border-left: 4px solid var(--primary-blue);
        }

        .admin-message {
            background: #f1f8e9;
            margin-right: 20%;
            border-right: 4px solid #28a745;
        }

        .message-header {
            font-size: 0.8rem;
            font-weight: bold;
            margin-bottom: 5px;
            color: #666;
        }

        .message-time {
            font-size: 0.7rem;
            color: #999;
            float: right;
        }

        .document-preview {
            max-height: 500px;
            overflow-y: auto;
        }

        .document-preview img, .document-preview iframe {
            max-width: 100%;
            border: 1px solid #dee2e6;
            border-radius: 4px;
        }

        .ticket-item {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .ticket-item:hover {
            background-color: #f8f9fa;
            border-color: var(--primary-blue);
        }

        .ticket-item.active {
            background-color: #e3f2fd;
            border-color: var(--primary-blue);
        }

        .status-badge {
            font-size: 0.7rem;
            padding: 4px 8px;
        }

        .unread-badge {
            background-color: #dc3545;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            font-size: 0.7rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-left: 5px;
        }
    </style>

    <!-- Header -->
    <div class="dashboard-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h3><i class="fas fa-tachometer-alt me-2"></i>Application Dashboard</h3>
                    <p class="mb-0">Welcome to your application portal</p>
                </div>
                <div class="col-md-6 text-end">
                    <div class="btn-group">
                        <button class="btn btn-outline-dark" onclick="openSupport()">
                            <i class="fas fa-headset me-1"></i>Support 
                            <span class="unread-badge" id="unreadCount" style="display: none;">0</span>
                        </button>
                        <a href="logout.php" class="btn btn-outline-dark">
                            <i class="fas fa-sign-out-alt me-1"></i>Logout
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <!-- Application Status -->
        <div class="row">
            <div class="col-lg-8">
                <div class="status-card">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h5 class="text-primary">Application Status</h5>
                            <p class="mb-1"><strong>Application ID:</strong> <?php echo htmlspecialchars($applicationData['application_id']); ?></p>
                            <p class="mb-1"><strong>Current Status:</strong> 
                                <span class="badge bg-<?php 
                                    $status = $applicationData['admin_status'] ?? 'pending';
                                    echo $status === 'approved' ? 'success' : 
                                         ($status === 'rejected' ? 'danger' : 'warning'); ?>">
                                    <?php echo ucfirst(str_replace('_', ' ', $status)); ?>
                                </span>
                            </p>
                            <p class="mb-0"><strong>Last Updated:</strong> <?php echo date('M d, Y H:i', strtotime($applicationData['updated_at'])); ?></p>
                        </div>
                        <div class="col-md-4 text-end">
                            <button class="btn btn-primary" onclick="downloadReceipt()">
                                <i class="fas fa-download me-1"></i>Download Receipt
                            </button>
                        </div>
                    </div>
                    
                    <!-- Admin Notes -->
                    <?php if (!empty($applicationData['admin_notes'])): ?>
                    <div class="alert alert-info mt-3">
                        <strong><i class="fas fa-info-circle me-1"></i>Admin Notes:</strong><br>
                        <?php echo htmlspecialchars($applicationData['admin_notes']); ?>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Documents Section -->
                <div class="document-card">
                    <h5 class="text-primary mb-4"><i class="fas fa-folder me-2"></i>Uploaded Documents</h5>
                    
                    <!-- PAN Card -->
                    <div class="document-item mb-4">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <h6>PAN Card</h6>
                                <?php if ($applicationData['pan_card']): ?>
                                    <span class="text-success"><i class="fas fa-check-circle me-1"></i>Uploaded</span>
                                    <?php if ($applicationData['pan_number']): ?>
                                        <br><small class="text-muted">Number: <?php echo htmlspecialchars($applicationData['pan_number']); ?></small>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="text-warning"><i class="fas fa-clock me-1"></i>Pending</span>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-6 text-end">
                                <div class="btn-group">
                                    <?php if ($applicationData['pan_card']): ?>
                                        <button class="btn btn-sm btn-outline-primary" onclick="openDocumentModal('pan_card', '<?php echo htmlspecialchars($applicationData['pan_card']); ?>')">
                                            <i class="fas fa-eye me-1"></i>Preview & Comment
                                        </button>
                                        <button class="btn btn-sm btn-outline-warning" onclick="reuploadDocument('pan_card')">
                                            <i class="fas fa-upload me-1"></i>Reupload
                                        </button>
                                    <?php else: ?>
                                        <button class="btn btn-sm btn-primary" onclick="uploadDocument('pan_card')">
                                            <i class="fas fa-upload me-1"></i>Upload
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php if ($applicationData['pan_status'] && $applicationData['pan_status'] !== 'pending'): ?>
                            <div class="comment-box mt-2">
                                <strong>Status:</strong> 
                                <span class="document-status bg-<?php 
                                    echo $applicationData['pan_status'] === 'approved' ? 'success' : 
                                         ($applicationData['pan_status'] === 'rejected' ? 'danger' : 'warning'); ?>">
                                    <?php echo ucfirst($applicationData['pan_status']); ?>
                                </span>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Aadhar Card -->
                    <div class="document-item mb-4">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <h6>Aadhar Card</h6>
                                <?php if ($applicationData['aadhar_card']): ?>
                                    <span class="text-success"><i class="fas fa-check-circle me-1"></i>Uploaded</span>
                                    <?php if ($applicationData['aadhar_number']): ?>
                                        <br><small class="text-muted">Number: <?php echo htmlspecialchars($applicationData['aadhar_number']); ?></small>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="text-warning"><i class="fas fa-clock me-1"></i>Pending</span>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-6 text-end">
                                <div class="btn-group">
                                    <?php if ($applicationData['aadhar_card']): ?>
                                        <button class="btn btn-sm btn-outline-primary" onclick="openDocumentModal('aadhar_card', '<?php echo htmlspecialchars($applicationData['aadhar_card']); ?>')">
                                            <i class="fas fa-eye me-1"></i>Preview & Comment
                                        </button>
                                        <button class="btn btn-sm btn-outline-warning" onclick="reuploadDocument('aadhar_card')">
                                            <i class="fas fa-upload me-1"></i>Reupload
                                        </button>
                                    <?php else: ?>
                                        <button class="btn btn-sm btn-primary" onclick="uploadDocument('aadhar_card')">
                                            <i class="fas fa-upload me-1"></i>Upload
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php if ($applicationData['aadhar_status'] && $applicationData['aadhar_status'] !== 'pending'): ?>
                            <div class="comment-box mt-2">
                                <strong>Status:</strong> 
                                <span class="document-status bg-<?php 
                                    echo $applicationData['aadhar_status'] === 'approved' ? 'success' : 
                                         ($applicationData['aadhar_status'] === 'rejected' ? 'danger' : 'warning'); ?>">
                                    <?php echo ucfirst($applicationData['aadhar_status']); ?>
                                </span>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Cancelled Cheque -->
                    <div class="document-item mb-4">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <h6>Cancelled Cheque</h6>
                                <?php if ($applicationData['cancelled_cheque']): ?>
                                    <span class="text-success"><i class="fas fa-check-circle me-1"></i>Uploaded</span>
                                <?php else: ?>
                                    <span class="text-warning"><i class="fas fa-clock me-1"></i>Pending</span>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-6 text-end">
                                <div class="btn-group">
                                    <?php if ($applicationData['cancelled_cheque']): ?>
                                        <button class="btn btn-sm btn-outline-primary" onclick="openDocumentModal('cancelled_cheque', '<?php echo htmlspecialchars($applicationData['cancelled_cheque']); ?>')">
                                            <i class="fas fa-eye me-1"></i>Preview & Comment
                                        </button>
                                        <button class="btn btn-sm btn-outline-warning" onclick="reuploadDocument('cancelled_cheque')">
                                            <i class="fas fa-upload me-1"></i>Reupload
                                        </button>
                                    <?php else: ?>
                                        <button class="btn btn-sm btn-primary" onclick="uploadDocument('cancelled_cheque')">
                                            <i class="fas fa-upload me-1"></i>Upload
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php if ($applicationData['cheque_status'] && $applicationData['cheque_status'] !== 'pending'): ?>
                            <div class="comment-box mt-2">
                                <strong>Status:</strong> 
                                <span class="document-status bg-<?php 
                                    echo $applicationData['cheque_status'] === 'approved' ? 'success' : 
                                         ($applicationData['cheque_status'] === 'rejected' ? 'danger' : 'warning'); ?>">
                                    <?php echo ucfirst($applicationData['cheque_status']); ?>
                                </span>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Acknowledge Document -->
                    <div class="document-item">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <h6>Acknowledge Document</h6>
                                <?php if ($applicationData['acknowledge_doc']): ?>
                                    <span class="text-success"><i class="fas fa-check-circle me-1"></i>Uploaded</span>
                                <?php else: ?>
                                    <span class="text-warning"><i class="fas fa-clock me-1"></i>Pending</span>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-6 text-end">
                                <div class="btn-group">
                                    <?php if ($applicationData['acknowledge_doc']): ?>
                                        <button class="btn btn-sm btn-outline-primary" onclick="openDocumentModal('acknowledge_doc', '<?php echo htmlspecialchars($applicationData['acknowledge_doc']); ?>')">
                                            <i class="fas fa-eye me-1"></i>Preview & Comment
                                        </button>
                                        <button class="btn btn-sm btn-outline-warning" onclick="reuploadDocument('acknowledge_doc')">
                                            <i class="fas fa-upload me-1"></i>Reupload
                                        </button>
                                    <?php else: ?>
                                        <button class="btn btn-sm btn-primary" onclick="uploadDocument('acknowledge_doc')">
                                            <i class="fas fa-upload me-1"></i>Upload
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Quick Actions -->
                <div class="document-card">
                    <h6 class="text-primary mb-3"><i class="fas fa-bolt me-2"></i>Quick Actions</h6>
                    <div class="d-grid gap-2">
                        <button class="btn btn-outline-primary text-start" onclick="openSupport()">
                            <i class="fas fa-headset me-2"></i>Contact Support
                        </button>
                        <button class="btn btn-outline-primary text-start" onclick="downloadReceipt()">
                            <i class="fas fa-receipt me-2"></i>Download Receipt
                        </button>
                        <button class="btn btn-outline-primary text-start" onclick="checkApplicationStatus()">
                            <i class="fas fa-sync-alt me-2"></i>Refresh Status
                        </button>
                    </div>
                </div>

                <!-- Application Details -->
                <div class="document-card">
                    <h6 class="text-primary mb-3"><i class="fas fa-user me-2"></i>Application Details</h6>
                    <div class="small">
                        <p><strong>Name:</strong><br><?php echo htmlspecialchars($applicationData['full_name']); ?></p>
                        <p><strong>Email:</strong><br><?php echo htmlspecialchars($applicationData['email']); ?></p>
                        <p><strong>Phone:</strong><br><?php echo htmlspecialchars($applicationData['phone']); ?></p>
                        <?php if ($applicationData['whatsapp']): ?>
                        <p><strong>WhatsApp:</strong><br><?php echo htmlspecialchars($applicationData['whatsapp']); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Support Chat Modal -->
    <div class="modal fade" id="supportModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-headset me-2"></i>Support Center</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6>Support Tickets</h6>
                                <button class="btn btn-sm btn-primary" onclick="showCreateTicketView()">
                                    <i class="fas fa-plus me-1"></i>New Ticket
                                </button>
                            </div>
                            <div id="ticketList" style="max-height: 400px; overflow-y: auto;">
                                <!-- Tickets will be loaded here -->
                            </div>
                        </div>
                        <div class="col-md-8">
                            <!-- Create Ticket View -->
                            <div id="createTicketView" style="display: none;">
                                <h6>Create New Support Ticket</h6>
                                <form id="createTicketForm">
                                    <input type="hidden" name="application_id" value="<?php echo htmlspecialchars($applicationData['application_id']); ?>">
                                    <div class="mb-3">
                                        <label class="form-label">Subject</label>
                                        <input type="text" class="form-control" name="subject" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Priority</label>
                                        <select class="form-select" name="priority">
                                            <option value="low">Low</option>
                                            <option value="medium" selected>Medium</option>
                                            <option value="high">High</option>
                                            <option value="urgent">Urgent</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Message</label>
                                        <textarea class="form-control" name="message" rows="4" required></textarea>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <button type="button" class="btn btn-secondary" onclick="showTicketListView()">Cancel</button>
                                        <button type="submit" class="btn btn-primary">Create Ticket</button>
                                    </div>
                                </form>
                            </div>

                            <!-- Chat View -->
                            <div id="chatView">
                                <div id="noTicketSelected" class="text-center text-muted" style="display: block;">
                                    <i class="fas fa-comments fa-3x mb-3"></i>
                                    <p>Select a ticket from the list to view messages</p>
                                </div>
                                <div id="activeChat" style="display: none;">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 id="ticketSubject">Ticket Subject</h6>
                                        <span class="badge status-badge" id="ticketStatus">Open</span>
                                    </div>
                                    <div class="chat-container" id="supportChatContainer">
                                        <!-- Messages will be loaded here -->
                                    </div>
                                    <form id="supportMessageForm" class="mt-3">
                                        <input type="hidden" id="currentTicketId" name="ticket_id">
                                        <div class="mb-3">
                                            <textarea class="form-control" id="supportMessage" name="message" rows="3" placeholder="Type your message here..." required></textarea>
                                        </div>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-paper-plane me-1"></i>Send Message
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Document Preview & Comment Modal -->
    <div class="modal fade" id="documentModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="documentModalTitle">Document Preview & Comments</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-7">
                            <div class="document-preview" id="documentPreview">
                                <!-- Document preview will be loaded here -->
                            </div>
                        </div>
                        <div class="col-md-5">
                            <h6>Comments & Chat</h6>
                            <div class="chat-container" id="chatContainer">
                                <!-- Comments will be loaded here -->
                            </div>
                            <form id="commentForm" class="mt-3">
                                <input type="hidden" id="commentDocumentType" name="document_type">
                                <input type="hidden" name="application_id" value="<?php echo htmlspecialchars($applicationData['application_id']); ?>">
                                <div class="mb-3">
                                    <label class="form-label">Add Comment</label>
                                    <textarea class="form-control" id="commentText" name="comment" rows="3" placeholder="Type your comment here..." required></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-paper-plane me-1"></i>Send Comment
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Document Upload Modal -->
    <div class="modal fade" id="uploadModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="uploadModalTitle">Upload Document</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="documentUploadForm" enctype="multipart/form-data">
                        <input type="hidden" id="documentType" name="document_type">
                        <input type="hidden" name="application_id" value="<?php echo htmlspecialchars($applicationData['application_id']); ?>">
                        
                        <div class="mb-3">
                            <label class="form-label">Select Document</label>
                            <input type="file" class="form-control" id="documentFile" name="document_file" accept=".pdf,.jpg,.jpeg,.png" required>
                            <small class="text-muted">PDF, JPG, PNG (Max 10MB)</small>
                        </div>
                        
                        <div class="mb-3" id="documentNumberSection" style="display: none;">
                            <label class="form-label" id="documentNumberLabel">Document Number</label>
                            <input type="text" class="form-control" id="documentNumber" name="document_number">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="submitDocument()">Upload</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.8/js/bootstrap.bundle.min.js"></script>
    <script>
        let documentModal = new bootstrap.Modal(document.getElementById('documentModal'));
        let supportModal = new bootstrap.Modal(document.getElementById('supportModal'));
        let uploadModal = new bootstrap.Modal(document.getElementById('uploadModal'));
        let currentDocumentType = '';
        let currentTicketId = null;
        let chatRefreshInterval = null;

        // Support Chat Functions
        function openSupport() {
            loadSupportTickets();
            supportModal.show();
            
            // Start auto-refresh for messages
            if (chatRefreshInterval) {
                clearInterval(chatRefreshInterval);
            }
            chatRefreshInterval = setInterval(() => {
                if (currentTicketId) {
                    loadSupportMessages(currentTicketId);
                }
                loadUnreadCount();
            }, 5000); // Refresh every 5 seconds
        }

        function loadSupportTickets() {
            const ticketList = document.getElementById('ticketList');
            ticketList.innerHTML = '<div class="text-center"><div class="spinner-border spinner-border-sm"></div> Loading tickets...</div>';
            
            fetch('get_support_tickets.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `application_id=<?php echo $applicationData['application_id']; ?>`
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    displayTickets(result.tickets);
                } else {
                    ticketList.innerHTML = '<div class="text-muted text-center">No support tickets yet</div>';
                }
            })
            .catch(error => {
                console.error('Error loading tickets:', error);
                ticketList.innerHTML = '<div class="text-muted text-center">Error loading tickets</div>';
            });
        }

        function displayTickets(tickets) {
            const ticketList = document.getElementById('ticketList');
            
            if (tickets.length === 0) {
                ticketList.innerHTML = '<div class="text-muted text-center">No support tickets yet</div>';
                return;
            }

            let html = '';
            tickets.forEach(ticket => {
                const isActive = ticket.id === currentTicketId;
                const unreadCount = ticket.unread_count || 0;
                
                html += `
                    <div class="ticket-item ${isActive ? 'active' : ''}" onclick="selectTicket(${ticket.id}, '${escapeHtml(ticket.subject)}', '${ticket.status}')">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <strong>${escapeHtml(ticket.subject)}</strong>
                                <div class="small text-muted">${formatTime(ticket.created_at)}</div>
                            </div>
                            <div class="text-end">
                                <span class="badge status-badge bg-${getStatusColor(ticket.status)}">${ticket.status}</span>
                                ${unreadCount > 0 ? `<span class="unread-badge">${unreadCount}</span>` : ''}
                            </div>
                        </div>
                    </div>
                `;
            });
            ticketList.innerHTML = html;
        }

        function getStatusColor(status) {
            switch(status) {
                case 'open': return 'primary';
                case 'in_progress': return 'warning';
                case 'resolved': return 'success';
                case 'closed': return 'secondary';
                default: return 'secondary';
            }
        }

        function selectTicket(ticketId, subject, status) {
            currentTicketId = ticketId;
            document.getElementById('ticketSubject').textContent = subject;
            document.getElementById('ticketStatus').textContent = status;
            document.getElementById('ticketStatus').className = `badge status-badge bg-${getStatusColor(status)}`;
            document.getElementById('currentTicketId').value = ticketId;
            
            document.getElementById('noTicketSelected').style.display = 'none';
            document.getElementById('activeChat').style.display = 'block';
            document.getElementById('createTicketView').style.display = 'none';
            
            loadSupportMessages(ticketId);
            markMessagesAsRead(ticketId);
            loadSupportTickets(); // Refresh ticket list to update unread counts
        }

        function loadSupportMessages(ticketId) {
            const chatContainer = document.getElementById('supportChatContainer');
            chatContainer.innerHTML = '<div class="text-center"><div class="spinner-border spinner-border-sm"></div> Loading messages...</div>';
            
            fetch('get_support_messages.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `ticket_id=${ticketId}`
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    displaySupportMessages(result.messages);
                } else {
                    chatContainer.innerHTML = '<div class="alert alert-danger">Error loading messages</div>';
                }
            })
            .catch(error => {
                console.error('Error loading messages:', error);
                chatContainer.innerHTML = '<div class="alert alert-danger">Error loading messages</div>';
            });
        }

        function displaySupportMessages(messages) {
            const chatContainer = document.getElementById('supportChatContainer');
            
            if (messages.length === 0) {
                chatContainer.innerHTML = '<div class="text-muted text-center">No messages yet. Start the conversation!</div>';
                return;
            }

            let html = '';
            messages.forEach(message => {
                const isUser = message.sender_type === 'user';
                const messageClass = isUser ? 'user-message' : 'admin-message';
                const sender = isUser ? 'You' : 'Admin';
                
                html += `
                    <div class="chat-message ${messageClass}">
                        <div class="message-header">
                            ${sender}
                            <span class="message-time">${formatTime(message.created_at)}</span>
                        </div>
                        <div>${escapeHtml(message.message)}</div>
                    </div>
                `;
            });
            
            chatContainer.innerHTML = html;
            chatContainer.scrollTop = chatContainer.scrollHeight;
        }

        function markMessagesAsRead(ticketId) {
            fetch('mark_messages_read.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `ticket_id=${ticketId}`
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    loadUnreadCount();
                }
            })
            .catch(error => {
                console.error('Error marking messages as read:', error);
            });
        }

        function loadUnreadCount() {
            fetch('get_unread_count.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `application_id=<?php echo $applicationData['application_id']; ?>`
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    const unreadBadge = document.getElementById('unreadCount');
                    if (unreadBadge) {
                        unreadBadge.textContent = result.unread_count;
                        if (result.unread_count === 0) {
                            unreadBadge.style.display = 'none';
                        } else {
                            unreadBadge.style.display = 'inline-flex';
                        }
                    }
                }
            })
            .catch(error => {
                console.error('Error loading unread count:', error);
            });
        }

        function showCreateTicketView() {
            document.getElementById('createTicketView').style.display = 'block';
            document.getElementById('activeChat').style.display = 'none';
            document.getElementById('noTicketSelected').style.display = 'none';
            currentTicketId = null;
        }

        function showTicketListView() {
            document.getElementById('createTicketView').style.display = 'none';
            document.getElementById('activeChat').style.display = 'none';
            document.getElementById('noTicketSelected').style.display = 'block';
            currentTicketId = null;
            loadSupportTickets();
        }

        // Event listeners for support chat
        document.getElementById('createTicketForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            
            fetch('create_support_ticket.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    alert('Support ticket created successfully!');
                    this.reset();
                    showTicketListView();
                    loadSupportTickets();
                    if (result.ticket_id) {
                        selectTicket(result.ticket_id, formData.get('subject'), 'open');
                    }
                } else {
                    alert('Error: ' + result.message);
                }
            })
            .catch(error => {
                console.error('Error creating ticket:', error);
                alert('Error creating ticket');
            });
        });

        document.getElementById('supportMessageForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const messageInput = document.getElementById('supportMessage');
            
            fetch('send_support_message.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    messageInput.value = '';
                    loadSupportMessages(currentTicketId);
                    loadSupportTickets(); // Refresh to update unread counts
                } else {
                    alert('Error: ' + result.message);
                }
            })
            .catch(error => {
                console.error('Error sending message:', error);
                alert('Error sending message');
            });
        });

        // Stop auto-refresh when modal is closed
        document.getElementById('supportModal').addEventListener('hidden.bs.modal', function() {
            if (chatRefreshInterval) {
                clearInterval(chatRefreshInterval);
                chatRefreshInterval = null;
            }
        });

        // Document Preview & Comment Functions
        function openDocumentModal(type, fileName) {
            currentDocumentType = type;
            document.getElementById('documentModalTitle').textContent = getDocumentName(type) + ' - Preview & Comments';
            document.getElementById('commentDocumentType').value = type;
            
            loadDocumentPreview(type, fileName);
            loadComments(type);
            
            documentModal.show();
        }

        function loadDocumentPreview(type, fileName) {
            const previewContainer = document.getElementById('documentPreview');
            previewContainer.innerHTML = '<div class="text-center"><div class="spinner-border text-primary"></div><p>Loading document...</p></div>';
            
            if (fileName) {
                const filePath = '<?php echo $applicationData['application_id']; ?>/' + fileName;
                const fileExtension = fileName.split('.').pop().toLowerCase();
                
                if (['jpg', 'jpeg', 'png', 'gif'].includes(fileExtension)) {
                    previewContainer.innerHTML = `<img src="view_document.php?file=${encodeURIComponent(fileName)}&app_id=<?php echo $applicationData['application_id']; ?>" class="img-fluid" alt="${type}">`;
                } else if (fileExtension === 'pdf') {
                    previewContainer.innerHTML = `<iframe src="view_document.php?file=${encodeURIComponent(fileName)}&app_id=<?php echo $applicationData['application_id']; ?>" width="100%" height="500px" style="border: none;"></iframe>`;
                } else {
                    previewContainer.innerHTML = '<div class="alert alert-info">Document preview not available for this file type. <a href="view_document.php?file=' + encodeURIComponent(fileName) + '&app_id=<?php echo $applicationData['application_id']; ?>" target="_blank">Download instead</a></div>';
                }
            } else {
                previewContainer.innerHTML = '<div class="alert alert-warning">No document uploaded yet.</div>';
            }
        }

        async function loadComments(type) {
            const chatContainer = document.getElementById('chatContainer');
            chatContainer.innerHTML = '<div class="text-center"><div class="spinner-border spinner-border-sm"></div> Loading comments...</div>';
            
            try {
                const response = await fetch('get_document_comments.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `application_id=<?php echo $applicationData['application_id']; ?>&document_type=${type}`
                });
                
                const result = await response.json();
                
                if (result.success) {
                    displayComments(result.comments);
                } else {
                    chatContainer.innerHTML = '<div class="text-muted">No comments yet. Start the conversation!</div>';
                }
            } catch (error) {
                console.error('Error loading comments:', error);
                chatContainer.innerHTML = '<div class="alert alert-danger">Error loading comments</div>';
            }
        }

        function displayComments(comments) {
            const chatContainer = document.getElementById('chatContainer');
            
            if (comments.length === 0) {
                chatContainer.innerHTML = '<div class="text-muted">No comments yet. Start the conversation!</div>';
                return;
            }
            
            let html = '';
            comments.forEach(comment => {
                const isUser = comment.commented_by === 'user';
                const messageClass = isUser ? 'user-message' : 'admin-message';
                const sender = isUser ? 'You' : 'Admin';
                
                html += `
                    <div class="chat-message ${messageClass}">
                        <div class="message-header">
                            ${sender}
                            <span class="message-time">${formatTime(comment.created_at)}</span>
                        </div>
                        <div>${escapeHtml(comment.comment)}</div>
                    </div>
                `;
            });
            
            chatContainer.innerHTML = html;
            chatContainer.scrollTop = chatContainer.scrollHeight;
        }

        document.getElementById('commentForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            try {
                const response = await fetch('submit_document_comment.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    document.getElementById('commentText').value = '';
                    loadComments(currentDocumentType);
                } else {
                    alert('Error: ' + result.message);
                }
            } catch (error) {
                console.error('Error submitting comment:', error);
                alert('Error submitting comment');
            }
        });

        function uploadDocument(type) {
            document.getElementById('documentType').value = type;
            document.getElementById('uploadModalTitle').textContent = 'Upload ' + getDocumentName(type);
            
            // Show document number field for PAN and Aadhar
            const numberSection = document.getElementById('documentNumberSection');
            const numberInput = document.getElementById('documentNumber');
            const numberLabel = document.getElementById('documentNumberLabel');
            
            if (type === 'pan_card') {
                numberSection.style.display = 'block';
                numberLabel.textContent = 'PAN Number';
                numberInput.placeholder = 'Enter PAN number';
                numberInput.pattern = '[A-Z]{5}[0-9]{4}[A-Z]{1}';
                numberInput.title = 'PAN format: ABCDE1234F';
                numberInput.value = '<?php echo htmlspecialchars($applicationData['pan_number'] ?? ''); ?>';
            } else if (type === 'aadhar_card') {
                numberSection.style.display = 'block';
                numberLabel.textContent = 'Aadhar Number';
                numberInput.placeholder = 'Enter Aadhar number';
                numberInput.pattern = '[0-9]{12}';
                numberInput.title = '12-digit Aadhar number';
                numberInput.value = '<?php echo htmlspecialchars($applicationData['aadhar_number'] ?? ''); ?>';
            } else {
                numberSection.style.display = 'none';
            }
            
            uploadModal.show();
        }

        function reuploadDocument(type) {
            if (confirm('Are you sure you want to reupload this document? The previous version will be replaced.')) {
                uploadDocument(type);
            }
        }

        function getDocumentName(type) {
            const names = {
                'pan_card': 'PAN Card',
                'aadhar_card': 'Aadhar Card',
                'cancelled_cheque': 'Cancelled Cheque',
                'acknowledge_doc': 'Acknowledge Document'
            };
            return names[type] || type;
        }

        async function submitDocument() {
            const formData = new FormData(document.getElementById('documentUploadForm'));
            const documentType = document.getElementById('documentType').value;
            
            // Validate PAN/Aadhar numbers if provided
            if (documentType === 'pan_card' || documentType === 'aadhar_card') {
                const numberInput = document.getElementById('documentNumber');
                if (documentType === 'pan_card' && !/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/.test(numberInput.value)) {
                    alert('Please enter a valid PAN number (format: ABCDE1234F)');
                    return;
                }
                if (documentType === 'aadhar_card' && !/^[0-9]{12}$/.test(numberInput.value)) {
                    alert('Please enter a valid 12-digit Aadhar number');
                    return;
                }
            }
            
            try {
                const response = await fetch('upload_single_document.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    alert('Document uploaded successfully!');
                    uploadModal.hide();
                    location.reload();
                } else {
                    throw new Error(result.message);
                }
            } catch (error) {
                alert('Error: ' + error.message);
            }
        }

        function downloadReceipt() {
            const appId = '<?php echo htmlspecialchars($applicationData['application_id']); ?>';
            window.open('download_receipt.php?application_id=' + appId, '_blank');
        }

        function checkApplicationStatus() {
            location.reload();
        }

        function formatTime(timestamp) {
            const date = new Date(timestamp);
            return date.toLocaleDateString() + ' ' + date.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
        }

        function escapeHtml(unsafe) {
            return unsafe
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }

        // Load unread count on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadUnreadCount();
        });
    </script>
<?php
// Include footer
include 'footer.php';
?>