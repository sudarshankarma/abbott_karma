<?php $title = 'Activity Log'; ?>
<div class="row">
    <div class="col s12">
        <div class="card">
            <div class="card-content">
                <div class="card-title">
                    <div class="row">
                        <div class="col s12 m6">
                            <h4 class="card-title">
                                <i class="material-icons left">history</i>
                                Activity Log
                            </h4>
                            <p class="grey-text">System activities and user actions</p>
                        </div>
                        <div class="col s12 m6 right-align">
                            <a href="?controller=dashboard" class="btn blue waves-effect waves-light">
                                <i class="material-icons left">dashboard</i>Dashboard
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Activity Table -->
                <div class="row">
                    <div class="col s12">
                        <table class="striped highlight responsive-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>User</th>
                                    <th>Action</th>
                                    <th>Details</th>
                                    <th>IP Address</th>
                                    <th>Timestamp</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($activities as $activity): ?>
                                <tr>
                                    <td><?php echo $activity['id']; ?></td>
                                    <td>
                                        <span class="badge blue white-text"><?php echo $activity['username'] ?? 'System'; ?></span>
                                    </td>
                                    <td><?php echo htmlspecialchars($activity['action']); ?></td>
                                    <td><?php echo htmlspecialchars($activity['details']); ?></td>
                                    <td><code><?php echo $activity['ip_address']; ?></code></td>
                                    <td><?php echo date('M j, Y g:i A', strtotime($activity['created_at'])); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>

                        <?php if (empty($activities)): ?>
                        <div class="center" style="padding: 40px;">
                            <i class="material-icons large grey-text">history</i>
                            <h5 class="grey-text">No activity records found</h5>
                            <p class="grey-text">Activity log will appear here as users perform actions.</p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>