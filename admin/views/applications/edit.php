<?php $title = 'Edit Application - ' . $application['application_id']; ?>
<div class="row">
    <div class="col s12">
        <div class="card">
            <div class="card-content">
                <div class="card-title">
                    <div class="row valign-wrapper">
                        <div class="col s12 m6">
                            <h4>
                                <i class="material-icons left">edit</i>
                                Edit Application
                            </h4>
                            <p class="grey-text">Application ID: <strong><?php echo $application['application_id']; ?></strong></p>
                        </div>
                        <div class="col s12 m6 right-align">
                            <a href="?controller=applications&action=view&id=<?php echo $application['id']; ?>" class="btn blue waves-effect waves-light">
                                <i class="material-icons left">arrow_back</i>Back to View
                            </a>
                            <a href="?controller=applications" class="btn grey waves-effect waves-light">
                                <i class="material-icons left">list</i>All Applications
                            </a>
                        </div>
                    </div>
                </div>

                <form method="POST" id="editForm">
                    <div class="row">
                        <!-- Personal Information -->
                        <div class="col s12 m6">
                            <div class="card-panel">
                                <h5 class="card-title">
                                    <i class="material-icons left">person</i>
                                    Personal Information
                                </h5>
                                <div class="divider"></div>
                                
                                <div class="input-field">
                                    <input id="full_name" name="full_name" type="text" 
                                           value="<?php echo htmlspecialchars($application['full_name']); ?>" required>
                                    <label for="full_name">Full Name *</label>
                                </div>

                                <div class="input-field">
                                    <input id="email" name="email" type="email" 
                                           value="<?php echo htmlspecialchars($application['email']); ?>" required>
                                    <label for="email">Email *</label>
                                </div>

                                <div class="input-field">
                                    <input id="phone" name="phone" type="text" 
                                           value="<?php echo htmlspecialchars($application['phone']); ?>" required>
                                    <label for="phone">Phone *</label>
                                </div>

                                <div class="input-field">
                                    <input id="whatsapp" name="whatsapp" type="text" 
                                           value="<?php echo htmlspecialchars($application['whatsapp']); ?>">
                                    <label for="whatsapp">WhatsApp</label>
                                </div>

                                <div class="input-field">
                                    <input id="pan_number" name="pan_number" type="text" 
                                           value="<?php echo htmlspecialchars($application['pan_number']); ?>" required>
                                    <label for="pan_number">PAN Number *</label>
                                </div>

                                <div class="input-field">
                                    <input id="aadhar_number" name="aadhar_number" type="text" 
                                           value="<?php echo htmlspecialchars($application['aadhar_number']); ?>" required>
                                    <label for="aadhar_number">Aadhar Number *</label>
                                </div>
                            </div>
                        </div>

                        <!-- Company Information & Admin Controls -->
                        <div class="col s12 m6">
                            <!-- Company Information -->
                            <div class="card-panel">
                                <h5 class="card-title">
                                    <i class="material-icons left">business</i>
                                    Company Information
                                </h5>
                                <div class="divider"></div>
                                
                                <div class="input-field">
                                    <input id="piramal_uan" name="piramal_uan" type="text" 
                                           value="<?php echo htmlspecialchars($application['piramal_uan']); ?>">
                                    <label for="piramal_uan">Piramal UAN</label>
                                </div>

                                <div class="input-field">
                                    <input id="abbott_uan" name="abbott_uan" type="text" 
                                           value="<?php echo htmlspecialchars($application['abbott_uan']); ?>">
                                    <label for="abbott_uan">Abbott UAN</label>
                                </div>

                                <div class="input-field">
                                    <input id="piramal_id" name="piramal_id" type="text" 
                                           value="<?php echo htmlspecialchars($application['piramal_id']); ?>">
                                    <label for="piramal_id">Piramal ID</label>
                                </div>

                                <div class="input-field">
                                    <input id="abbott_id" name="abbott_id" type="text" 
                                           value="<?php echo htmlspecialchars($application['abbott_id']); ?>">
                                    <label for="abbott_id">Abbott ID</label>
                                </div>
                            </div>

                            <!-- Admin Controls -->
                            <div class="card-panel">
                                <h5 class="card-title">
                                    <i class="material-icons left">admin_panel_settings</i>
                                    Admin Controls
                                </h5>
                                <div class="divider"></div>
                                
                                <div class="input-field">
                                    <select id="admin_status" name="admin_status" required>
                                        <option value="pending" <?php echo $application['admin_status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                        <option value="under_review" <?php echo $application['admin_status'] === 'under_review' ? 'selected' : ''; ?>>Under Review</option>
                                        <option value="approved" <?php echo $application['admin_status'] === 'approved' ? 'selected' : ''; ?>>Approved</option>
                                        <option value="rejected" <?php echo $application['admin_status'] === 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                                    </select>
                                    <label>Application Status</label>
                                </div>

                                <div class="input-field">
                                    <textarea id="admin_notes" name="admin_notes" class="materialize-textarea"><?php echo htmlspecialchars($application['admin_notes'] ?? ''); ?></textarea>
                                    <label for="admin_notes">Admin Notes</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="row">
                        <div class="col s12 center">
                            <button type="submit" class="btn-large green waves-effect waves-light">
                                <i class="material-icons left">save</i>
                                Update Application
                            </button>
                            <a href="?controller=applications&action=view&id=<?php echo $application['id']; ?>" 
                               class="btn-large grey waves-effect waves-light">
                                <i class="material-icons left">cancel</i>
                                Cancel
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize select elements
    const selects = document.querySelectorAll('select');
    M.FormSelect.init(selects);

    // Form submission handler
    document.getElementById('editForm').addEventListener('submit', function(e) {
        const submitBtn = this.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="material-icons left">hourglass_empty</i> Updating...';
    });
});
</script>