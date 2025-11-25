<?php //include 'views/loyout/header.php'; ?>
<?php $title = 'Support Tickets'; ?>
<div class="row">
    <div class="col s12">
        <div class="card">
            <div class="card-content">
                <div class="card-title">
                    <div class="row">
                        <div class="col s12 m6">
                            <h4 class="card-title">
                                <i class="material-icons left">support_agent</i>
                                Support Tickets
                            </h4>
                            <p class="grey-text">Manage and respond to user support requests</p>
                        </div>
                        <div class="col s12 m6 right-align">
                            <a href="?controller=dashboard" class="btn blue waves-effect waves-light">
                                <i class="material-icons left">dashboard</i>Dashboard
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Support Tickets Table -->
                <div class="row">
                    <div class="col s12">
                        <table id="supportTable" class="table table-bordered table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Ticket ID</th>
                                    <th>Application ID</th>
                                    <th>Applicant Name</th>
                                    <th>Subject</th>
                                    <th>Status</th>
                                    <th>Priority</th>
                                    <th>Unread</th>
                                    <th>Last Updated</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($tickets as $ticket): ?>
                                <tr>
                                    <td><strong>#<?php echo $ticket['id']; ?></strong></td>
                                    <td><?php echo $ticket['application_id']; ?></td>
                                    <td><?php echo htmlspecialchars($ticket['full_name'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($ticket['subject']); ?></td>
                                    <td>
                                        <!-- In index.php - Update the status badge colors -->
                                      
                                        <?php
                                        $statusClass = [
                                            'open' => 'orange',
                                            'in_progress' => 'blue', 
                                            'resolved' => 'green',
                                            'closed' => 'grey'
                                        ][$ticket['status']] ?? 'grey';
                                        ?>
                                        <span class="badge white-text <?php echo $statusClass; ?>">
                                            <?php echo ucfirst(str_replace('_', ' ', $ticket['status'])); ?>
                                        </span>
                                       
                                    </td>
                                    <td>
                                        <?php
                                        $priorityClass = [
                                            'low' => 'grey',
                                            'medium' => 'blue',
                                            'high' => 'orange',
                                            'urgent' => 'red'
                                        ][$ticket['priority']] ?? 'grey';
                                        ?>
                                        <span class="badge white-text <?php echo $priorityClass; ?>">
                                            <?php echo ucfirst($ticket['priority']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($ticket['unread_from_user'] > 0): ?>
                                            <span class="badge red white-text"><?php echo $ticket['unread_from_user']; ?> unread</span>
                                        <?php else: ?>
                                            <span class="grey-text">0</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo date('M j, Y g:i A', strtotime($ticket['updated_at'])); ?></td>
                                    <td>
                                        <a href="?controller=support&action=view&id=<?php echo $ticket['id']; ?>" 
                                           class="btn-small blue waves-effect waves-light tooltipped"
                                           data-tooltip="View Ticket">
                                            <i class="material-icons">visibility</i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>

                        <?php if (empty($tickets)): ?>
                        <div class="center" style="padding: 40px;">
                            <i class="material-icons large grey-text">support_agent</i>
                            <h5 class="grey-text">No support tickets found</h5>
                            <p class="grey-text">When users contact support, their tickets will appear here.</p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Initialize tooltips
document.addEventListener('DOMContentLoaded', function() {
    const tooltips = document.querySelectorAll('.tooltipped');
    M.Tooltip.init(tooltips);
});
document.addEventListener('DOMContentLoaded', function() {
    let table = new DataTable('#supportTable');
});
</script>

<?php //include 'views/layout/footer.php'; ?>