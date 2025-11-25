<?php $title = 'Document Chat - ' . $application['application_id']; ?>
<div class="row">
    <div class="col s12">
        <div class="card">
            <div class="card-content">
                <div class="card-title">
                    <div class="row valign-wrapper">
                        <div class="col s12 m6">
                            <h4>
                                <i class="material-icons left">chat</i>
                                Document Chat
                            </h4>
                            <p class="grey-text">
                                Application: <?php echo $application['application_id']; ?> - 
                                <?php echo htmlspecialchars($application['full_name']); ?>
                            </p>
                        </div>
                        <div class="col s12 m6 right-align">
                            <a href="?controller=applications&action=view&id=<?php echo $application['application_id']; ?>" 
                               class="btn blue waves-effect waves-light">
                                <i class="material-icons left">arrow_back</i>Back to Application
                            </a>
                            <a href="?controller=support" class="btn green waves-effect waves-light">
                                <i class="material-icons left">support_agent</i>Support Tickets
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Document Selection -->
                <div class="row">
                    <div class="col s12">
                        <div class="card-panel">
                            <h6>Select Document</h6>
                            <div class="row">
                                <div class="col s12 m3">
                                    <a href="?controller=support&action=documentChat&app_id=<?php echo $application['application_id']; ?>&doc_type=pan_card" 
                                       class="btn <?php echo $document_type === 'pan_card' ? 'blue' : 'grey'; ?> waves-effect waves-light full-width">
                                        PAN Card
                                    </a>
                                </div>
                                <div class="col s12 m3">
                                    <a href="?controller=support&action=documentChat&app_id=<?php echo $application['application_id']; ?>&doc_type=aadhar_card" 
                                       class="btn <?php echo $document_type === 'aadhar_card' ? 'blue' : 'grey'; ?> waves-effect waves-light full-width">
                                        Aadhar Card
                                    </a>
                                </div>
                                <div class="col s12 m3">
                                    <a href="?controller=support&action=documentChat&app_id=<?php echo $application['application_id']; ?>&doc_type=cancelled_cheque" 
                                       class="btn <?php echo $document_type === 'cancelled_cheque' ? 'blue' : 'grey'; ?> waves-effect waves-light full-width">
                                        Cancelled Cheque
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Chat Area -->
                <div class="row">
                    <div class="col s12">
                        <div class="card-panel">
                            <h6>
                                Chat for <?php echo ucfirst(str_replace('_', ' ', $document_type)); ?>
                            </h6>
                            <div class="divider"></div>
                            
                            <div class="chat-container" id="chatContainer" style="max-height: 400px; overflow-y: auto; padding: 20px; background: #f9f9f9; border-radius: 8px;">
                                <?php foreach ($comments as $comment): ?>
                                    <div class="chat-message <?php echo $comment['commented_by'] === 'admin' ? 'admin-message' : 'user-message'; ?>">
                                        <div class="message-header">
                                            <?php 
                                            if ($comment['commented_by'] === 'admin') {
                                                echo 'Admin' . (!empty($comment['admin_name']) ? ' (' . htmlspecialchars($comment['admin_name']) . ')' : '');
                                            } else {
                                                echo 'User';
                                            }
                                            ?>
                                            <span class="message-time"><?php echo date('M j, Y g:i A', strtotime($comment['created_at'])); ?></span>
                                        </div>
                                        <div class="message-content"><?php echo nl2br(htmlspecialchars($comment['comment'])); ?></div>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <!-- Comment Form -->
                            <form method="POST" action="?controller=support&action=sendDocumentCommentAjax" id="commentForm" class="mt-4">
                                <input type="hidden" name="application_id" value="<?php echo $application['application_id']; ?>">
                                <input type="hidden" name="document_type" value="<?php echo $document_type; ?>">
                                <div class="input-field">
                                    <textarea id="comment" name="comment" class="materialize-textarea" required placeholder="Type your comment about this document..."></textarea>
                                    <label for="comment">Add Comment</label>
                                </div>
                                <button type="submit" class="btn green waves-effect waves-light">
                                    <i class="material-icons left">send</i>Send Comment
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

.full-width {
    width: 100%;
}
</style>

<script>
    // Auto-scroll to bottom of chat
    function scrollToBottom() {
        const container = document.getElementById('chatContainer');
        container.scrollTop = container.scrollHeight;
    }

    // Handle form submission with AJAX
    document.getElementById('commentForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const submitBtn = this.querySelector('button[type="submit"]');
        
        // Disable button and show loading
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="material-icons left">hourglass_empty</i>Sending...';
        
        fetch('?controller=support&action=sendDocumentCommentAjax', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('comment').value = '';
                // Reload page to show new comment
                location.reload();
            } else {
                M.toast({html: 'Error: ' + data.error, classes: 'red'});
            }
        })
        .catch(error => {
            console.error('Error:', error);
            M.toast({html: 'Error sending comment', classes: 'red'});
        })
        .finally(() => {
            // Re-enable button
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="material-icons left">send</i>Send Comment';
        });
    });

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        scrollToBottom();
    });
</script>