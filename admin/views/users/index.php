<?php $title = 'User Management'; ?>
<div class="row">
    <div class="col s12">
        <div class="card">
            <div class="card-content">
                <div class="card-title">
                    <div class="row">
                        <div class="col s12 m6">
                            <h4 class="card-title">
                                <i class="material-icons left">people</i>
                                User Management
                            </h4>
                            <p class="grey-text">Manage system users and permissions</p>
                        </div>
                        <div class="col s12 m6 right-align">
                            <a class="btn green waves-effect waves-light modal-trigger" href="#addUserModal">
                                <i class="material-icons left">person_add</i>Add New User
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Users Table -->
                <div class="row">
                    <div class="col s12">
                        <table class="striped highlight responsive-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Username</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                    <th>Last Login</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($users as $user): ?>
                                <tr>
                                    <td><?php echo $user['id']; ?></td>
                                    <td>
                                        <strong><?php echo $user['username']; ?></strong>
                                        <?php if ($user['id'] == $_SESSION['user_id']): ?>
                                        <span class="badge blue white-text">You</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo $user['email']; ?></td>
                                    <td>
                                        <?php
                                        $roleClass = [
                                            'super_admin' => 'red',
                                            'admin' => 'orange',
                                            'viewer' => 'blue'
                                        ][$user['role']] ?? 'grey';
                                        ?>
                                        <span class="badge white-text <?php echo $roleClass; ?>">
                                            <?php echo ucfirst(str_replace('_', ' ', $user['role'])); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge <?php echo $user['is_active'] ? 'green' : 'red'; ?> white-text">
                                            <?php echo $user['is_active'] ? 'Active' : 'Inactive'; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php echo $user['last_login'] ? date('M j, Y g:i A', strtotime($user['last_login'])) : 'Never'; ?>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <a class="btn-small orange waves-effect waves-light tooltipped modal-trigger"
                                               href="#editUserModal"
                                               data-tooltip="Edit User"
                                               onclick="loadUserData(<?php echo $user['id']; ?>)">
                                                <i class="material-icons">edit</i>
                                            </a>
                                            <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                            <a class="btn-small red waves-effect waves-light tooltipped"
                                               data-tooltip="Delete User"
                                               onclick="confirmDelete(<?php echo $user['id']; ?>, '<?php echo $user['username']; ?>')">
                                                <i class="material-icons">delete</i>
                                            </a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add User Modal -->
<div id="addUserModal" class="modal">
    <div class="modal-content">
        <h4>Add New User</h4>
        <form method="POST">
            <input type="hidden" name="action" value="create">
            <div class="row">
                <div class="input-field col s12 m6">
                    <input id="new_username" name="username" type="text" required>
                    <label for="new_username">Username *</label>
                </div>
                <div class="input-field col s12 m6">
                    <input id="new_email" name="email" type="email" required>
                    <label for="new_email">Email *</label>
                </div>
            </div>
            <div class="row">
                <div class="input-field col s12 m6">
                    <select name="role" required>
                        <option value="viewer">Viewer</option>
                        <option value="admin">Admin</option>
                        <option value="super_admin">Super Admin</option>
                    </select>
                    <label>Role</label>
                </div>
                <div class="input-field col s12 m6">
                    <p>
                        <label>
                            <input type="checkbox" name="is_active" checked="checked" />
                            <span>Active User</span>
                        </label>
                    </p>
                </div>
            </div>
            <div class="row">
                <div class="col s12">
                    <p class="grey-text">A random password will be generated and emailed to the user.</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn green waves-effect waves-light">
                    <i class="material-icons left">person_add</i>Add User
                </button>
                <a href="#!" class="modal-close btn grey waves-effect waves-light">Cancel</a>
            </div>
        </form>
    </div>
</div>

<!-- Edit User Modal -->
<div id="editUserModal" class="modal">
    <div class="modal-content">
        <h4>Edit User</h4>
        <form method="POST">
            <input type="hidden" name="action" value="update">
            <input type="hidden" id="edit_id" name="id">
            <div class="row">
                <div class="input-field col s12 m6">
                    <input id="edit_username" name="username" type="text" required>
                    <label for="edit_username">Username *</label>
                </div>
                <div class="input-field col s12 m6">
                    <input id="edit_email" name="email" type="email" required>
                    <label for="edit_email">Email *</label>
                </div>
            </div>
            <div class="row">
                <div class="input-field col s12 m6">
                    <select id="edit_role" name="role" required>
                        <option value="viewer">Viewer</option>
                        <option value="admin">Admin</option>
                        <option value="super_admin">Super Admin</option>
                    </select>
                    <label>Role</label>
                </div>
                <div class="input-field col s12 m6">
                    <p>
                        <label>
                            <input type="checkbox" id="edit_is_active" name="is_active" />
                            <span>Active User</span>
                        </label>
                    </p>
                </div>
            </div>
            <div class="row">
                <div class="input-field col s12">
                    <input id="edit_password" name="password" type="password">
                    <label for="edit_password">New Password (leave blank to keep current)</label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn orange waves-effect waves-light">
                    <i class="material-icons left">save</i>Update User
                </button>
                <a href="#!" class="modal-close btn grey waves-effect waves-light">Cancel</a>
            </div>
        </form>
    </div>
</div>

<script>
function loadUserData(userId) {
    // In a real application, you would fetch user data via AJAX
    // For now, we'll use a simplified approach
    console.log('Loading user data for ID:', userId);
    // This would typically make an AJAX call to get user data
}

function confirmDelete(userId, username) {
    if (confirm(`Are you sure you want to delete user "${username}"? This action cannot be undone.`)) {
        // Create a form and submit it
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '?controller=users';
        
        const actionInput = document.createElement('input');
        actionInput.type = 'hidden';
        actionInput.name = 'action';
        actionInput.value = 'delete';
        form.appendChild(actionInput);
        
        const idInput = document.createElement('input');
        idInput.type = 'hidden';
        idInput.name = 'id';
        idInput.value = userId;
        form.appendChild(idInput);
        
        document.body.appendChild(form);
        form.submit();
    }
}

// Initialize modals and selects
document.addEventListener('DOMContentLoaded', function() {
    const modals = document.querySelectorAll('.modal');
    M.Modal.init(modals);
    
    const selects = document.querySelectorAll('select');
    M.FormSelect.init(selects);
    
    const tooltips = document.querySelectorAll('.tooltipped');
    M.Tooltip.init(tooltips);
});
</script>