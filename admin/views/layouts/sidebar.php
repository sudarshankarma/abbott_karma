<?php
$sidebarUsername = htmlspecialchars($_SESSION['username'] ?? 'User', ENT_QUOTES, 'UTF-8');
$sidebarRoleLabel = isset($_SESSION['user_role']) ? ucwords(str_replace('_', ' ', $_SESSION['user_role'])) : 'User';
$sidebarRoleDisplay = htmlspecialchars($sidebarRoleLabel, ENT_QUOTES, 'UTF-8');
?>
<!-- Fixed Sidebar -->
<div class="sidebar-fixed">
    <div class="logo-wrapper blue darken-4">
        <a href="?controller=dashboard" class="brand-logo white-text">
            <i class="material-icons">admin_panel_settings</i>
            <?php echo SITE_NAME; ?>
        </a>
    </div>
    <div class="grey lighten-5" style="padding: 15px 20px;">
        <span class="chip blue lighten-5 blue-text text-darken-2" style="margin-bottom: 8px;">
            <i class="material-icons left">account_circle</i>
            <?php echo $sidebarUsername; ?>
        </span>
        <p class="grey-text text-darken-1" style="margin: 0;">
            <i class="material-icons tiny" style="vertical-align: middle; margin-right: 4px;">shield</i>
            <?php echo $sidebarRoleDisplay; ?>
        </p>
    </div>
    <div class="divider"></div>

    <ul class="sidebar-menu">
        <li class="grey-text text-darken-1" style="padding: 10px 20px; font-size: 0.75rem; letter-spacing: 0.08em; text-transform: uppercase;">
            Workspace
        </li>
        <li class="<?php echo ($_GET['controller'] ?? '') == 'dashboard' ? 'active' : ''; ?>">
            <a href="?controller=dashboard" class="waves-effect">
                <i class="material-icons">dashboard</i>Dashboard
            </a>
        </li>
        <li class="<?php echo ($_GET['controller'] ?? '') == 'applications' ? 'active' : ''; ?>">
            <a href="?controller=applications" class="waves-effect">
                <i class="material-icons">list_alt</i>Applications
            </a>
        </li>
        <li class="<?php echo ($_GET['controller'] ?? '') == 'documents' ? 'active' : ''; ?>">
            <a href="?controller=documents&action=repository" class="waves-effect">
                <i class="material-icons">archive</i>Document Repository
            </a>
        </li>
        <?php if (($_SESSION['user_role'] ?? null) == ROLE_SUPER_ADMIN): ?>
        <li class="grey-text text-darken-1" style="padding: 10px 20px; font-size: 0.75rem; letter-spacing: 0.08em; text-transform: uppercase;">
            Administration
        </li>
        <li class="<?php echo ($_GET['controller'] ?? '') == 'users' ? 'active' : ''; ?>">
            <a href="?controller=users" class="waves-effect">
                <i class="material-icons">people</i>User Management
            </a>
        </li>
        <li class="<?php echo ($_GET['controller'] ?? '') == 'activity' ? 'active' : ''; ?>">
            <a href="?controller=activity" class="waves-effect">
                <i class="material-icons">history</i>Activity Log
            </a>
        </li>
        <?php endif; ?>
    </ul>
</div>