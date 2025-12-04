<?php /*<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME . ' - ' . ($title ?? 'Dashboard'); ?></title>
    
    <!-- Material Design Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    
    <!-- Materialize CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/custom.css">
    
    <!-- DataTables -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/dataTables.material.min.css"/>
</head>
<body class="grey lighten-4">
    <?php if (isset($_SESSION['user_id'])): ?>
        <!-- Navigation -->
        <?php include 'navbar.php'; ?>
        
        <!-- Sidebar -->
        <?php include 'sidebar.php'; ?>
        
        <!-- Main Content -->
        <main>
            <div class="container">
                <?php if (isset($_GET['success'])): ?>
                    <div class="card-panel green lighten-4 green-text text-darken-4">
                        <?php
                        $messages = [
                            'updated' => 'Application updated successfully!',
                            'user_created' => 'User created successfully!',
                            'user_updated' => 'User updated successfully!',
                            'user_deleted' => 'User deleted successfully!'
                        ];
                        echo $messages[$_GET['success']] ?? 'Operation completed successfully!';
                        ?>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($_GET['error'])): ?>
                    <div class="card-panel red lighten-4 red-text text-darken-4">
                        <?php
                        $errors = [
                            'not_found' => 'Record not found!',
                            'update_failed' => 'Update failed!',
                            'user_create_failed' => 'User creation failed!',
                            'user_update_failed' => 'User update failed!',
                            'user_delete_failed' => 'User deletion failed!',
                            'cannot_delete_self' => 'You cannot delete your own account!',
                            'unauthorized' => 'You are not authorized to access this page!'
                        ];
                        echo $errors[$_GET['error']] ?? 'An error occurred!';
                        ?>
                    </div>
                <?php endif; ?>
<?php endif; */ ?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME . ' - ' . ($title ?? 'Dashboard'); ?></title>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Material Design Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    
    <!-- DataTables Bootstrap 5 CSS (Updated version) -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">

    
    <!-- Materialize CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    
    <!-- DataTables Bootstrap 5 CSS -->
    <!-- <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css"> -->
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/custom.css">
    <link rel="shortcut icon" href="../images/abbott_favicon.ico" type="image/x-icon"/>
</head>
<body class="grey lighten-4">
    <?php if (isset($_SESSION['user_id'])): ?>
        <!-- Navigation -->
        <?php include 'navbar.php'; ?>
        
        <!-- Sidebar -->
        <?php include 'sidebar.php'; ?>
        
        <!-- Main Content -->
        <main>
            <div class="container-fluid">
                <?php if (isset($_GET['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
                        <?php
                        $messages = [
                            'updated' => 'Application updated successfully!',
                            'user_created' => 'User created successfully!',
                            'user_updated' => 'User updated successfully!',
                            'user_deleted' => 'User deleted successfully!'
                        ];
                        echo $messages[$_GET['success']] ?? 'Operation completed successfully!';
                        ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($_GET['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
                        <?php
                        $errors = [
                            'not_found' => 'Record not found!',
                            'update_failed' => 'Update failed!',
                            'user_create_failed' => 'User creation failed!',
                            'user_update_failed' => 'User update failed!',
                            'user_delete_failed' => 'User deletion failed!',
                            'cannot_delete_self' => 'You cannot delete your own account!',
                            'unauthorized' => 'You are not authorized to access this page!'
                        ];
                        echo $errors[$_GET['error']] ?? 'An error occurred!';
                        ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
<?php endif; ?>