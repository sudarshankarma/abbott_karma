<?php include 'views/layout/header.php'; ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">Support Conversation</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="?controller=support&action=index">Support</a></li>
                            <li class="breadcrumb-item active"><?php echo htmlspecialchars($conversation['subject']); ?></li>
                        </ol>
                    </nav>
                </div>
                <div class="d-flex gap-2">
                    <?php if ($conversation['status'] === 'closed'): ?>
                        <a href="?controller=support&action=reopenConversation&id=<?php echo $conversation['id']; ?>" 
                           class="btn btn-success">
                            <i class="fas fa-redo"></i> Reopen
                        </a>
                    <?php else: ?>
                        <a href="?controller=support&action=closeConversation&id=<?php echo $conversation['id']; ?>" 
                           class="btn btn-secondary">
                            <i class="fas fa-lock"></i> Close
                        </a>
                    <?php endif; ?>
                    <a href="?controller=support&action=index" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                </div>
            </div>

            <!-- Conversation Header -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="card-title"><?php echo htmlspecialchars($conversation['subject']); ?></h5>
                            <p class="card-text text-muted"><?php echo htmlspecialchars($conversation['description']); ?></p>
                        </div>
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-6">
                                    <small class="text-muted">User</small>
                                    <div class="fw-bold"><?php echo htmlspecialchars($conversation['username']); ?></div>
                                    <div class="text-muted"><?php echo htmlspecialchars($conversation['email']); ?></div>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted">Status</small>
                                    <div>
                                        <span class="badge 
                                            <?php echo $conversation['status'] === 'open' ? 'bg-success' : 
                                                   ($conversation['status'] === 'admin_responded' ? 'bg-info' : 'bg-secondary'); ?>">
                                            <?php echo ucfirst(str_replace('_', ' ', $conversation['status'])); ?>
                                        </span>
                                    </div>
                                    <small class="text-muted">Created: <?php echo date('M j, Y g:i A', strtotime($conversation['created_at'])); ?></small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Messages -->
            <div class="card">
                <div class="card-body">
                    <div class="chat-messages" style="max-height: 500px; overflow-y: auto;">
                        <?php if (empty($messages)): ?>
                            <div class="text-center py-4 text-muted">
                                <i class="fas fa-comments fa-2x mb-2"></i>
                                <p>No messages yet</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($messages as $message): ?>
                                <div class="message mb-3 <?php echo $message['is_admin'] ? 'text-end' : 'text-start'; ?>">
                                    <div class="d-inline-block <?php echo $message['is_admin'] ? 'bg-primary text-white' : 'bg-light'; ?> rounded p-3" 
                                         style="max-width: 70%;">
                                        <div class="message-content">
                                            <?php echo nl2br(htmlspecialchars($message['message'])); ?>
                                        </div>
                                        <div class="message-time small mt-1 <?php echo $message['is_admin'] ? 'text-white-50' : 'text-muted'; ?>">
                                            <?php echo date('M j, g:i A', strtotime($message['created_at'])); ?>
                                            <?php if ($message['is_admin']): ?>
                                                <i class="fas fa-check-circle ms-1"></i>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="small mt-1 <?php echo $message['is_admin'] ? 'text-end' : 'text-start'; ?>">
                                        <?php echo $message['is_admin'] ? 'You' : htmlspecialchars($message['username']); ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <!-- Reply Form -->
                    <?php if ($conversation['status'] !== 'closed'): ?>
                        <div class="mt-4">
                            <form method="POST" action="?controller=support&action=sendMessage&id=<?php echo $conversation['id']; ?>">
                                <div class="mb-3">
                                    <label for="message" class="form-label">Reply to User</label>
                                    <textarea class="form-control" id="message" name="message" rows="4" 
                                              placeholder="Type your response..." required></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-paper-plane"></i> Send Response
                                </button>
                            </form>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info mt-4">
                            <i class="fas fa-info-circle"></i> This conversation is closed. Reopen it to send messages.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Auto-scroll to bottom of messages
document.addEventListener('DOMContentLoaded', function() {
    const chatMessages = document.querySelector('.chat-messages');
    if (chatMessages) {
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }
});
</script>

<?php include 'views/layout/footer.php'; ?>