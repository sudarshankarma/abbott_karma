<!-- In your main layout file (header or before </body>) -->
<!-- DataTables CSS -->
<!-- <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.6/css/dataTables.material.min.css"/>
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/material-design-lite/1.1.0/material.min.css"/> -->

<!-- jQuery and DataTables JS -->
<!-- <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.material.min.js"></script> -->
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
                        <table id="activity-table" class="striped highlight responsive-table">
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

<!-- DataTables Initialization Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize DataTable
    var table = $('#activity-table').DataTable({
        "pageLength": 25,
        "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
        "order": [[0, 'desc']], // Sort by ID descending (newest first)
        "responsive": true,
        "dom": '<"row"<"col s12 m6"l><"col s12 m6"f>>rt<"row"<"col s12 m6"i><"col s12 m6"p>>',
        "language": {
            "search": "_INPUT_",
            "searchPlaceholder": "Search activities...",
            "lengthMenu": "Show _MENU_ entries",
            "zeroRecords": "No matching activities found",
            "info": "Showing _START_ to _END_ of _TOTAL_ entries",
            "infoEmpty": "Showing 0 to 0 of 0 entries",
            "infoFiltered": "(filtered from _MAX_ total entries)",
            "paginate": {
                "first": "First",
                "last": "Last",
                "next": "Next",
                "previous": "Previous"
            }
        },
        "columnDefs": [
            {
                "targets": [0], // ID column
                "visible": true,
                "searchable": true
            },
            {
                "targets": [3], // Details column
                "render": function(data, type, row) {
                    if (type === 'display' && data.length > 50) {
                        return '<span title="' + data + '">' + data.substr(0, 50) + '...</span>';
                    }
                    return data;
                }
            }
        ]
    });

    // Add custom filter for user role/type
    $('#user-filter').on('change', function() {
        table.column(1).search(this.value).draw();
    });

    // Add date range filtering (optional)
    $('#date-filter').on('change', function() {
        table.draw();
    });
});
</script>

<style>
.dataTables_wrapper .dataTables_length,
.dataTables_wrapper .dataTables_filter {
    margin-bottom: 1rem;
}

.dataTables_wrapper .dataTables_filter input {
    border: 1px solid #ddd !important;
    border-radius: 4px;
    padding: 0 10px;
    height: 2rem;
    margin-left: 10px;
}

.dataTables_wrapper .dataTables_paginate .paginate_button {
    padding: 0 10px;
    margin: 0;
    border: none;
    border-radius: 4px;
}

.dataTables_wrapper .dataTables_paginate .paginate_button.current {
    background: #2196F3 !important;
    color: white !important;
}

.dataTables_wrapper .dataTables_paginate .paginate_button:hover {
    background: #e3f2fd !important;
    color: #2196F3 !important;
}

/* Ensure table responsiveness */
@media only screen and (max-width: 768px) {
    .dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dataTables_filter {
        text-align: left;
    }
}
</style>