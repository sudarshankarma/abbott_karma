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
                                Application: <?php echo $ticket['application_id']; ?> - 
                                <?php echo htmlspecialchars($ticket['full_name']); ?>
                            </p>
                        </div>
                        <div class="col s12 m6 right-align">
                            <a href="?controller=support" class="btn blue waves-effect waves-light">
                                <i class="material-icons left">arrow_back</i>Back to Tickets
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
                                        <td><strong>Applicant</strong></td>
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
                                                 ($ticket['status'] === 'resolved' ? 'green' : 'grey'); 
                                        ?> white-text">
                                            <?php echo ucfirst($ticket['status']); ?>
                                        </span>
                                    </div>
                                </div>
                                
                                <!-- Action Buttons -->
                                <div class="section">
                                    <?php if ($ticket['status'] === 'open'): ?>
                                        <button type="button" class="btn green waves-effect waves-light ticket-action" data-action="resolve">
                                            <i class="material-icons left">check_circle</i>Resolve Ticket
                                        </button>
                                        <button type="button" class="btn red waves-effect waves-light ticket-action" data-action="close">
                                            <i class="material-icons left">lock</i>Close Ticket
                                        </button>
                                    <?php else: ?>
                                        <button type="button" class="btn blue waves-effect waves-light ticket-action" data-action="reopen">
                                            <i class="material-icons left">lock_open</i>Reopen Ticket
                                        </button>
                                    <?php endif; ?>
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
                            <?php if ($ticket['status'] === 'open'): ?>
                                <form method="POST" id="replyForm" class="mt-4">
                                    <input type="hidden" name="ticket_id" value="<?php echo $ticket['id']; ?>">
                                    <div class="input-field">
                                        <textarea id="message" name="message" class="materialize-textarea" required placeholder="Type your reply..."></textarea>
                                        <label for="message">Your Reply</label>
                                    </div>
                                    <button type="submit" class="btn green waves-effect waves-light">
                                        <i class="material-icons left">send</i>Send Reply
                                    </button>
                                </form>
                            <?php else: ?>
                                <div class="card-panel orange lighten-4" id="closedMessage">
                                    <i class="material-icons left">info</i>
                                    This ticket is <?php echo $ticket['status']; ?>. Reopen it to send messages.
                                </div>
                            <?php endif; ?>
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

<script>
// Auto-scroll to bottom of chat
function scrollToBottom() {
    const container = document.getElementById('chatContainer');
    container.scrollTop = container.scrollHeight;
}

// Add new message to chat
function addMessageToChat(messageData) {
    const chatContainer = document.getElementById('chatContainer');
    
    const messageDiv = document.createElement('div');
    messageDiv.className = `chat-message admin-message`;
    
    messageDiv.innerHTML = `
        <div class="message-header">
            Admin
            <span class="message-time">${new Date().toLocaleString()}</span>
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

// Handle message form submission
document.getElementById('replyForm')?.addEventListener('submit', function(e) {
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
            textarea.value = '';
            if (data.data) {
                addMessageToChat(data.data);
            }
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
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="material-icons left">send</i>Send Reply';
    });
});

// Handle ticket actions (close, resolve, reopen)
document.querySelectorAll('.ticket-action').forEach(button => {
    button.addEventListener('click', function() {
        const action = this.getAttribute('data-action');
        const actionText = {
            'close': 'close',
            'resolve': 'resolve', 
            'reopen': 'reopen'
        }[action];
        
        if (confirm(`Are you sure you want to ${actionText} this ticket?`)) {
            updateTicketStatus(action);
        }
    });
});

// Update ticket status via AJAX
function updateTicketStatus(action) {
    const buttons = document.querySelectorAll('.ticket-action');
    buttons.forEach(btn => {
        btn.disabled = true;
        btn.innerHTML = '<i class="material-icons left">hourglass_empty</i>Processing...';
    });
    
    const formData = new FormData();
    formData.append('ticket_id', <?php echo $ticket['id']; ?>);
    formData.append('action', action);
    
    fetch('?controller=support&action=supportTicketUpdate', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            M.toast({html: data.message, classes: 'green'});
            // Reload page after 1 second to show updated status
            setTimeout(() => {
                location.reload();
            }, 1000);
        } else {
            M.toast({html: 'Error: ' + data.error, classes: 'red'});
        }
    })
    .catch(error => {
        console.error('Error:', error);
        M.toast({html: 'Error updating ticket', classes: 'red'});
    })
    .finally(() => {
        buttons.forEach(btn => {
            btn.disabled = false;
            // Reset button text based on action
            const actionText = {
                'close': '<i class="material-icons left">lock</i>Close Ticket',
                'resolve': '<i class="material-icons left">check_circle</i>Resolve Ticket',
                'reopen': '<i class="material-icons left">lock_open</i>Reopen Ticket'
            }[action];
            btn.innerHTML = actionText;
        });
    });
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    scrollToBottom();
});
</script>