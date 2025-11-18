<?php $title = 'Dashboard'; ?>
<div class="row">
    <div class="col s12">
        <div class="card-panel blue darken-3 white-text">
            <div class="row valign-wrapper">
                <div class="col s8">
                    <h4 class="white-text">Welcome back, <?php echo $_SESSION['username']; ?>! 👋</h4>
                    <p class="white-text">Here's what's happening with your applications today.</p>
                </div>
                <div class="col s4 right-align">
                    <div class="white-text">
                        <i class="material-icons medium">calendar_today</i>
                        <div><?php echo date('F j, Y'); ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Stats Cards -->
<div class="row">
    <div class="col s12 m6 l3">
        <div class="card-panel blue white-text">
            <div class="center">
                <i class="material-icons medium">description</i>
                <h4><?php echo $app_stats['total'] ?? 0; ?></h4>
                <p>Total Applications</p>
            </div>
        </div>
    </div>
    
    <div class="col s12 m6 l3">
        <div class="card-panel orange white-text">
            <div class="center">
                <i class="material-icons medium">pending</i>
                <h4><?php echo $app_stats['pending'] ?? 0; ?></h4>
                <p>Pending Review</p>
            </div>
        </div>
    </div>
    
    <div class="col s12 m6 l3">
        <div class="card-panel green white-text">
            <div class="center">
                <i class="material-icons medium">check_circle</i>
                <h4><?php echo $app_stats['approved'] ?? 0; ?></h4>
                <p>Approved</p>
            </div>
        </div>
    </div>
    
    <div class="col s12 m6 l3">
        <div class="card-panel red white-text">
            <div class="center">
                <i class="material-icons medium">cancel</i>
                <h4><?php echo $app_stats['rejected'] ?? 0; ?></h4>
                <p>Rejected</p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Recent Applications -->
    <div class="col s12 m8">
        <div class="card">
            <div class="card-content">
                <div class="card-title">
                    Recent Applications
                    <a href="?controller=applications" class="btn-floating btn-small blue right">
                        <i class="material-icons">list</i>
                    </a>
                </div>
                
                <table class="striped">
                    <thead>
                        <tr>
                            <th>App ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recent_apps as $app): ?>
                        <tr>
                            <td><strong><?php echo $app['application_id']; ?></strong></td>
                            <td><?php echo htmlspecialchars($app['full_name']); ?></td>
                            <td><?php echo htmlspecialchars($app['email']); ?></td>
                            <td>
                                <?php
                                $status_class = [
                                    'pending' => 'orange',
                                    'under_review' => 'blue',
                                    'approved' => 'green',
                                    'rejected' => 'red'
                                ][$app['admin_status']] ?? 'grey';
                                ?>
                                <span class="badge <?php echo $status_class; ?> white-text">
                                    <?php echo ucfirst($app['admin_status']); ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Quick Stats -->
    <div class="col s12 m4">
        <div class="card">
            <div class="card-content">
                <div class="card-title">Quick Stats</div>
                
                <ul class="collection">
                    <li class="collection-item">
                        <div>New Today<i class="secondary-content blue-text"><?php echo $app_stats['today'] ?? 0; ?></i></div>
                    </li>
                    <li class="collection-item">
                        <div>PAN Approved<i class="secondary-content green-text"><?php echo $doc_stats['pan_approved'] ?? 0; ?></i></div>
                    </li>
                    <li class="collection-item">
                        <div>Aadhar Approved<i class="secondary-content green-text"><?php echo $doc_stats['aadhar_approved'] ?? 0; ?></i></div>
                    </li>
                    <li class="collection-item">
                        <div>Cheque Approved<i class="secondary-content green-text"><?php echo $doc_stats['cheque_approved'] ?? 0; ?></i></div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>