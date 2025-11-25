<?php $title = 'Dashboard'; ?>
<div class="container-fluid py-4">
    <!-- Welcome Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h4 class="card-title mb-1">Welcome back, <?php echo $_SESSION['username']; ?>! 👋</h4>
                            <p class="card-text opacity-75">Here's what's happening with your applications today.</p>
                        </div>
                        <div class="col-md-4 text-md-end">
                            <div class="d-flex align-items-center justify-content-md-end">
                                <i class="material-icons me-2">calendar_today</i>
                                <div>
                                    <div class="fw-bold"><?php echo date('F j, Y'); ?></div>
                                    <small><?php echo date('l'); ?></small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Applications
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $app_stats['total'] ?? 0; ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="material-icons text-primary fs-2">description</i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Pending Review
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $app_stats['pending'] ?? 0; ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="material-icons text-warning fs-2">pending_actions</i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Approved
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $app_stats['approved'] ?? 0; ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="material-icons text-success fs-2">check_circle</i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Rejected
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $app_stats['rejected'] ?? 0; ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="material-icons text-danger fs-2">cancel</i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row mb-4">
        <!-- Application Status Chart -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Application Status Overview</h6>
                </div>
                <div class="card-body">
                    <div class="chart-bar">
                        <canvas id="applicationStatusChart" height="300"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Document Status Chart -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Document Approval Status</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="documentStatusChart" height="300"></canvas>
                    </div>
                    <div class="mt-4 text-center small">
                        <span class="mr-2">
                            <i class="fas fa-circle text-success"></i> Approved
                        </span>
                        <span class="mr-2">
                            <i class="fas fa-circle text-warning"></i> Pending
                        </span>
                        <span class="mr-2">
                            <i class="fas fa-circle text-danger"></i> Rejected
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Applications & Quick Stats -->
    <div class="row">
        <!-- Recent Applications -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Applications</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="recentApplicationsTable" class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>App ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Status</th>
                                    <th>Submitted</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_apps as $app): ?>
                                <tr>
                                    <td><strong><?php echo $app['application_id']; ?></strong></td>
                                    <td><?php echo htmlspecialchars($app['full_name']); ?></td>
                                    <td><?php echo htmlspecialchars($app['email']); ?></td>
                                    <td>
                                        <span class="badge bg-<?php 
                                            echo $app['admin_status'] === 'approved' ? 'success' : 
                                                 ($app['admin_status'] === 'pending' ? 'warning' : 
                                                 ($app['admin_status'] === 'rejected' ? 'danger' : 'info')); 
                                        ?>">
                                            <?php echo ucfirst($app['admin_status']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('M j, Y', strtotime($app['created_at'])); ?></td>
                                    <td>
                                        <a href="?controller=applications&action=view&id=<?php echo $app['id']; ?>" 
                                           class="btn btn-sm btn-primary">
                                            <i class="material-icons" style="font-size: 16px;">visibility</i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Stats & Actions -->
        <div class="col-lg-4">
            <!-- Quick Actions -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <a href="?controller=applications" class="list-group-item list-group-item-action d-flex align-items-center">
                            <i class="material-icons text-primary me-2">list_alt</i>
                            View Applications
                        </a>
                        <a href="?controller=documents&action=repository" class="list-group-item list-group-item-action d-flex align-items-center">
                            <i class="material-icons text-success me-2">archive</i>
                            Document Repository
                        </a>
                        <a href="?controller=support" class="list-group-item list-group-item-action d-flex align-items-center">
                            <i class="material-icons text-warning me-2">support_agent</i>
                            Support Tickets
                            <?php if ($support_unread_count > 0): ?>
                                <span class="badge bg-danger ms-auto"><?php echo $support_unread_count; ?></span>
                            <?php endif; ?>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Document Stats -->
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Document Statistics</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-4">
                            <div class="border rounded p-3">
                                <div class="text-primary fw-bold fs-4"><?php echo $doc_stats['pan_approved'] ?? 0; ?></div>
                                <small class="text-muted">PAN Approved</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="border rounded p-3">
                                <div class="text-success fw-bold fs-4"><?php echo $doc_stats['aadhar_approved'] ?? 0; ?></div>
                                <small class="text-muted">Aadhar Approved</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="border rounded p-3">
                                <div class="text-info fw-bold fs-4"><?php echo $doc_stats['cheque_approved'] ?? 0; ?></div>
                                <small class="text-muted">Cheque Approved</small>
                            </div>
                        </div>
                    </div>
                    <div class="mt-3 text-center">
                        <div class="text-muted">
                            <i class="material-icons me-1" style="font-size: 16px;">today</i>
                            <?php echo $app_stats['today'] ?? 0; ?> new today
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize DataTable
    let table = new DataTable('#recentApplicationsTable', {
        pageLength: 5,
        responsive: true
    });

    // Application Status Bar Chart
    const applicationCtx = document.getElementById('applicationStatusChart').getContext('2d');
    const applicationChart = new Chart(applicationCtx, {
        type: 'bar',
        data: {
            labels: ['Pending', 'Under Review', 'Approved', 'Rejected'],
            datasets: [{
                label: 'Applications',
                data: [
                    <?php echo $app_stats['pending'] ?? 0; ?>,
                    <?php echo $app_stats['under_review'] ?? 0; ?>,
                    <?php echo $app_stats['approved'] ?? 0; ?>,
                    <?php echo $app_stats['rejected'] ?? 0; ?>
                ],
                backgroundColor: [
                    '#f6c23e',
                    '#36b9cc',
                    '#1cc88a',
                    '#e74a3b'
                ],
                borderWidth: 0
            }]
        },
        options: {
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                title: {
                    display: true,
                    text: 'Application Status Distribution'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });

    // Document Status Pie Chart
    const documentCtx = document.getElementById('documentStatusChart').getContext('2d');
    const documentChart = new Chart(documentCtx, {
        type: 'doughnut',
        data: {
            labels: ['Approved', 'Pending', 'Rejected'],
            datasets: [{
                data: [
                    <?php echo ($doc_stats['pan_approved'] ?? 0) + ($doc_stats['aadhar_approved'] ?? 0) + ($doc_stats['cheque_approved'] ?? 0); ?>,
                    <?php echo ($doc_stats['pan_pending'] ?? 0) + ($doc_stats['aadhar_pending'] ?? 0) + ($doc_stats['cheque_pending'] ?? 0); ?>,
                    <?php echo ($doc_stats['pan_rejected'] ?? 0) + ($doc_stats['aadhar_rejected'] ?? 0) + ($doc_stats['cheque_rejected'] ?? 0); ?>
                ],
                backgroundColor: [
                    '#1cc88a',
                    '#f6c23e',
                    '#e74a3b'
                ],
                hoverBackgroundColor: [
                    '#17a673',
                    '#dda20a',
                    '#be2617'
                ],
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            maintainAspectRatio: false,
            cutout: '70%',
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
});
</script>

<style>
.border-left-primary { border-left: 0.25rem solid #4e73df !important; }
.border-left-success { border-left: 0.25rem solid #1cc88a !important; }
.border-left-warning { border-left: 0.25rem solid #f6c23e !important; }
.border-left-danger { border-left: 0.25rem solid #e74a3b !important; }

.card {
    transition: transform 0.2s;
}
.card:hover {
    transform: translateY(-2px);
}

.chart-bar, .chart-pie {
    position: relative;
    height: 300px;
}

.list-group-item {
    border: none;
    padding: 0.75rem 0;
}
</style>