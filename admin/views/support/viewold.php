<?php $title = 'Support Ticket #' . $ticket['id']; ?>
<div class="row">
    <div class="col s12">
        <div class="card">
            <div class="card-content">
                <div class="card-title">
                    <div class="row valign-wrapper">
                        <div class="col s12 m6">
                            <h4>
                                <i class="material-icons left">support_agent</i>
                                Support Ticket #<?php echo $ticket['id']; ?>
                            </h4>
                            <p class="grey-text">
                                Application: 
                                <a href="?controller=applications&action=view&id=<?php echo $ticket['application_id']; ?>" 
                                   class="blue-text">
                                    <?php echo $ticket['application_id']; ?>
                                </a>
                                - <?php echo htmlspecialchars($ticket['full_name']); ?>
                            </p>
                        </div>
                        <div class="col s12 m6 right-align">
                            <a href="?controller=support" class="btn blue waves-effect waves-light">
                                <i class="material-icons left">arrow_back</i>Back to Tickets
                            </a>
                            <a href="?controller=applications&action=view&id=<?php echo $ticket['application_id']; ?>" 
                               class="btn green waves-effect waves-light">
                                <i class="material-icons left">visibility</i>View Application
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Ticket Info -->
                <div class="row">
                    <div class="col s12 m6">
                        <div class="card-panel">
                            <h6>Ticket Information</h6>
                            <div class="divider"></div>
                            <table class="striped">
                                <tbody>
                                    <tr>
                                        <td><strong>Subject</strong></td>
                                        <td><?php echo htmlspecialchars($ticket['subject']); ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Application ID</strong></td>
                                        <td><?php echo $ticket['application_id']; ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Applicant Name</strong></td>
                                        <td><?php echo htmlspecialchars($ticket['full_name']); ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Contact</strong></td>
                                        <td>
                                            <?php echo htmlspecialchars($ticket['phone']); ?><br>
                                            <?php echo htmlspecialchars($ticket['email']); ?>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- <div class="col s12 m6">
                        <div class="card-panel">
                            <h6>Ticket Status</h6>
                            <div class="divider"></div>
                            <div class="section">
                                <div class="row">
                                    <div class="col s6">
                                        <strong>Priority:</strong><br>
                                        <span class="badge <?php 
                                            echo $ticket['priority'] === 'urgent' ? 'red' : 
                                                 ($ticket['priority'] === 'high' ? 'orange' : 
                                                 ($ticket['priority'] === 'medium' ? 'blue' : 'grey')); 
                                        ?> white-text">
                                            <?php echo ucfirst($ticket['priority']); ?>
                                        </span>
                                    </div>
                                    <div class="col s6">
                                        <strong>Status:</strong><br>
                                        <span class="badge <?php 
                                            echo $ticket['status'] === 'open' ? 'orange' : 
                                                 ($ticket['status'] === 'in_progress' ? 'blue' : 
                                                 ($ticket['status'] === 'resolved' ? 'green' : 'grey')); 
                                        ?> white-text">
                                            <?php echo ucfirst(str_replace('_', ' ', $ticket['status'])); ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="section">
                                    <p><strong>Created:</strong> <?php echo date('F j, Y g:i A', strtotime($ticket['created_at'])); ?></p>
                                    <p><strong>Last Updated:</strong> <?php echo date('F j, Y g:i A', strtotime($ticket['updated_at'])); ?></p>
                                </div>
                            </div>
                        </div>
                    </div> -->
                    <!-- In view.php - Update the Ticket Status section -->
                    <div class="col s12 m6">
                        <div class="card-panel">
                            <h6>Ticket Status</h6>
                            <div class="divider"></div>
                            <div class="section">
                                <div class="row">
                                    <div class="col s6">
                                        <strong>Priority:</strong><br>
                                        <span class="badge <?php 
                                            echo $ticket['priority'] === 'urgent' ? 'red' : 
                                                ($ticket['priority'] === 'high' ? 'orange' : 
                                                ($ticket['priority'] === 'medium' ? 'blue' : 'grey')); 
                                        ?> white-text">
                                            <?php echo ucfirst($ticket['priority']); ?>
                                        </span>
                                    </div>
                                    <div class="col s6">
                                        <strong>Status:</strong><br>
                                        <span id="ticketStatusBadge" class="badge <?php 
                                            echo $ticket['status'] === 'open' ? 'orange' : 
                                                ($ticket['status'] === 'in_progress' ? 'blue' : 
                                                ($ticket['status'] === 'resolved' ? 'green' : 'grey')); 
                                        ?> white-text">
                                            <?php echo ucfirst(str_replace('_', ' ', $ticket['status'])); ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="section">
                                    <p><strong>Created:</strong> <?php echo date('F j, Y g:i A', strtotime($ticket['created_at'])); ?></p>
                                    <p><strong>Last Updated:</strong> <span id="lastUpdated"><?php echo date('F j, Y g:i A', strtotime($ticket['updated_at'])); ?></span></p>
                                </div>
                                
                                <!-- Close/Reopen Ticket Button -->
                                <div class="section">
                                    <?php if ($ticket['status'] !== 'closed'): ?>
                                        <button type="button" id="closeTicketBtn" class="btn red waves-effect waves-light">
                                            <i class="material-icons left">lock</i>Close Ticket
                                        </button>
                                    <?php else: ?>
                                        <button type="button" id="reopenTicketBtn" class="btn green waves-effect waves-light">
                                            <i class="material-icons left">lock_open</i>Reopen Ticket
                                        </button>
                                    <?php endif; ?>
                                    
                                    <!-- Additional status buttons -->
                                    <div class="row mt-2" id="statusButtons" style="<?php echo $ticket['status'] === 'closed' ? 'display:none;' : ''; ?>">
                                        <div class="col s12">
                                            <button type="button" class="btn-small blue waves-effect waves-light status-btn" data-status="in_progress">
                                                <i class="material-icons left">update</i>In Progress
                                            </button>
                                            <button type="button" class="btn-small green waves-effect waves-light status-btn" data-status="resolved">
                                                <i class="material-icons left">check_circle</i>Resolve
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Chat Messages -->
                <div class="row">
                    <div class="col s12">
                        <div class="card-panel">
                            <h6>Conversation</h6>
                            <div class="divider"></div>
                            
                            <div class="chat-container" id="chatContainer" style="max-height: 400px; overflow-y: auto; padding: 20px; background: #f9f9f9; border-radius: 8px;">
                                <?php foreach ($messages as $message): ?>
                                    <div class="chat-message <?php echo $message['sender_type'] === 'admin' ? 'admin-message' : 'user-message'; ?>">
                                        <div class="message-header">
                                            <?php echo $message['sender_type'] === 'admin' ? 'Admin' : 'User'; ?>
                                            <span class="message-time"><?php echo date('M j, Y g:i A', strtotime($message['created_at'])); ?></span>
                                        </div>
                                        <div class="message-content"><?php echo nl2br(htmlspecialchars($message['body'])); ?></div>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <!-- Reply Form -->
                            <form method="POST" action="?controller=support&action=sendMessage" id="replyForm" class="mt-4">
                                <input type="hidden" name="ticket_id" value="<?php echo $ticket['id']; ?>">
                                <div class="input-field">
                                    <textarea id="message" name="message" class="materialize-textarea" required placeholder="Type your reply..."></textarea>
                                    <label for="message">Your Reply</label>
                                </div>
                                <button type="submit" class="btn green waves-effect waves-light">
                                    <i class="material-icons left">send</i>Send Reply
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.chat-message {
    margin-bottom: 15px;
    padding: 12px;
    border-radius: 10px;
    position: relative;
    max-width: 80%;
}

.user-message {
    background: #e3f2fd;
    margin-right: auto;
    border-left: 4px solid #2196F3;
}

.admin-message {
    background: #f1f8e9;
    margin-left: auto;
    border-right: 4px solid #4CAF50;
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

.message-content {
    line-height: 1.4;
}
</style>

<!-- Replace the entire script section in view.php -->
<script>
    // Auto-scroll to bottom of chat
    function scrollToBottom() {
        const container = document.getElementById('chatContainer');
        container.scrollTop = container.scrollHeight;
    }

    // Format date for display
    function formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleString('en-US', { 
            month: 'short', 
            day: 'numeric', 
            year: 'numeric',
            hour: 'numeric', 
            minute: '2-digit',
            hour12: true 
        });
    }

    // Add new message to chat
    function addMessageToChat(messageData) {
        const chatContainer = document.getElementById('chatContainer');
        
        const messageDiv = document.createElement('div');
        messageDiv.className = `chat-message admin-message`;
        
        messageDiv.innerHTML = `
            <div class="message-header">
                Admin
                <span class="message-time">${formatDate(messageData.created_at)}</span>
            </div>
            <div class="message-content">${escapeHtml(messageData.body)}</div>
        `;
        
        chatContainer.appendChild(messageDiv);
        scrollToBottom();
    }

    // Escape HTML to prevent XSS
    function escapeHtml(unsafe) {
        return unsafe
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;")
            .replace(/\n/g, '<br>');
    }

    // Handle form submission with AJAX
    document.getElementById('replyForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const submitBtn = this.querySelector('button[type="submit"]');
        const textarea = document.getElementById('message');
        
        // Disable button and show loading
        const originalText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="material-icons left">hourglass_empty</i>Sending...';
        
        fetch('?controller=support&action=sendMessage', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Clear textarea
                textarea.value = '';
                
                // Add new message to chat
                if (data.data) {
                    addMessageToChat(data.data);
                }
                
                // Show success message
                M.toast({html: 'Message sent successfully', classes: 'green'});
            } else {
                M.toast({html: 'Error: ' + data.error, classes: 'red'});
            }
        })
        .catch(error => {
            console.error('Error:', error);
            M.toast({html: 'Error sending message', classes: 'red'});
        })
        .finally(() => {
            // Re-enable button
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        });
    });

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        scrollToBottom();
        
        // Add materialize textarea auto-resize
        const textarea = document.getElementById('message');
        if (textarea) {
            textarea.addEventListener('input', function() {
                this.style.height = 'auto';
                this.style.height = (this.scrollHeight) + 'px';
            });
        }
    });
    // Add these methods to SupportController.php

    // Add this JavaScript to view.php after the existing script

    // Close ticket functionality
    document.addEventListener('DOMContentLoaded', function() {
        scrollToBottom();
        
        // Close ticket button
        const closeTicketBtn = document.getElementById('closeTicketBtn');
        if (closeTicketBtn) {
            closeTicketBtn.addEventListener('click', function() {
                if (confirm('Are you sure you want to close this ticket?')) {
                    closeTicket();
                }
            });
        }
        
        // Reopen ticket button
        const reopenTicketBtn = document.getElementById('reopenTicketBtn');
        if (reopenTicketBtn) {
            reopenTicketBtn.addEventListener('click', function() {
                reopenTicket();
            });
        }
        
        // Status change buttons
        const statusButtons = document.querySelectorAll('.status-btn');
        statusButtons.forEach(button => {
            button.addEventListener('click', function() {
                const status = this.getAttribute('data-status');
                updateTicketStatus(status);
            });
        });
    });

    // Close ticket via AJAX
    function closeTicket() {
        const closeBtn = document.getElementById('closeTicketBtn');
        const originalText = closeBtn.innerHTML;
        
        closeBtn.disabled = true;
        closeBtn.innerHTML = '<i class="material-icons left">hourglass_empty</i>Closing...';
        
        const formData = new FormData();
        formData.append('ticket_id', <?php echo $ticket['id']; ?>);
        
        fetch('?controller=support&action=closeTicket', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateTicketUI('closed');
                M.toast({html: data.message, classes: 'green'});
            } else {
                M.toast({html: 'Error: ' + data.error, classes: 'red'});
            }
        })
        .catch(error => {
            console.error('Error:', error);
            M.toast({html: 'Error closing ticket', classes: 'red'});
        })
        .finally(() => {
            closeBtn.disabled = false;
            closeBtn.innerHTML = originalText;
        });
    }

    // Reopen ticket via AJAX
    function reopenTicket() {
        const reopenBtn = document.getElementById('reopenTicketBtn');
        const originalText = reopenBtn.innerHTML;
        
        reopenBtn.disabled = true;
        reopenBtn.innerHTML = '<i class="material-icons left">hourglass_empty</i>Reopening...';
        
        const formData = new FormData();
        formData.append('ticket_id', <?php echo $ticket['id']; ?>);
        
        fetch('?controller=support&action=reopenTicket', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateTicketUI('open');
                M.toast({html: data.message, classes: 'green'});
            } else {
                M.toast({html: 'Error: ' + data.error, classes: 'red'});
            }
        })
        .catch(error => {
            console.error('Error:', error);
            M.toast({html: 'Error reopening ticket', classes: 'red'});
        })
        .finally(() => {
            reopenBtn.disabled = false;
            reopenBtn.innerHTML = originalText;
        });
    }

    // Update ticket status (for other statuses)
    function updateTicketStatus(status) {
        const statusText = {
            'open': 'Open',
            'in_progress': 'In Progress',
            'resolved': 'Resolved',
            'closed': 'Closed'
        };
        
        const formData = new FormData();
        formData.append('ticket_id', <?php echo $ticket['id']; ?>);
        formData.append('status', status);
        
        fetch('?controller=support&action=updateTicketStatus', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateTicketUI(status);
                M.toast({html: `Ticket marked as ${statusText[status]}`, classes: 'green'});
            } else {
                M.toast({html: 'Error: ' + data.error, classes: 'red'});
            }
        })
        .catch(error => {
            console.error('Error:', error);
            M.toast({html: 'Error updating ticket status', classes: 'red'});
        });
    }

    // Update UI after status change
    function updateTicketUI(status) {
        const statusBadge = document.getElementById('ticketStatusBadge');
        const lastUpdated = document.getElementById('lastUpdated');
        const statusButtons = document.getElementById('statusButtons');
        
        // Update status badge
        const statusClass = {
            'open': 'orange',
            'in_progress': 'blue',
            'resolved': 'green',
            'closed': 'grey'
        }[status];
        
        const statusText = {
            'open': 'Open',
            'in_progress': 'In Progress',
            'resolved': 'Resolved',
            'closed': 'Closed'
        }[status];
        
        statusBadge.className = `badge ${statusClass} white-text`;
        statusBadge.textContent = statusText;
        
        // Update timestamp
        const now = new Date();
        lastUpdated.textContent = now.toLocaleString('en-US', {
            month: 'long',
            day: 'numeric',
            year: 'numeric',
            hour: 'numeric',
            minute: '2-digit',
            hour12: true
        });
        
        // Show/hide buttons
        if (status === 'closed') {
            document.getElementById('closeTicketBtn').style.display = 'none';
            document.getElementById('reopenTicketBtn').style.display = 'block';
            statusButtons.style.display = 'none';
        } else {
            document.getElementById('closeTicketBtn').style.display = 'block';
            document.getElementById('reopenTicketBtn').style.display = 'none';
            statusButtons.style.display = 'block';
        }
    }
</script>