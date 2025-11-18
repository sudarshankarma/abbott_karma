<nav class="nav-extended blue darken-3">
    <div class="nav-wrapper container">
        <a href="?controller=dashboard" class="brand-logo">
            <i class="material-icons">admin_panel_settings</i>
            <?php echo SITE_NAME; ?>
        </a>
        <a href="#" data-target="mobile-nav" class="sidenav-trigger">
            <i class="material-icons">menu</i>
        </a>
        <ul id="nav-mobile" class="right hide-on-med-and-down">
            <li>
                <a href="?controller=dashboard">
                    <i class="material-icons left">dashboard</i>Dashboard
                </a>
            </li>
            <li>
                <a href="?controller=applications">
                    <i class="material-icons left">list_alt</i>Applications
                </a>
            </li>
            <li>
                <a href="?controller=documents&action=repository">
                    <i class="material-icons left">archive</i>Documents
                </a>
            </li>
            <?php if ($_SESSION['user_role'] == ROLE_SUPER_ADMIN): ?>
            <li>
                <a href="?controller=users">
                    <i class="material-icons left">people</i>Users
                </a>
            </li>
            <li>
                <a href="?controller=activity">
                    <i class="material-icons left">history</i>Activity
                </a>
            </li>
            <?php endif; ?>
            <li>
                <a class="dropdown-trigger" href="#!" data-target="user-dropdown">
                    <i class="material-icons left">account_circle</i>
                    <?php echo $_SESSION['username']; ?>
                    <i class="material-icons right">arrow_drop_down</i>
                </a>
            </li>
        </ul>
    </div>
</nav>

<!-- User Dropdown -->
<ul id="user-dropdown" class="dropdown-content">
    <li><a href="?controller=dashboard"><i class="material-icons">dashboard</i>Dashboard</a></li>
    <li><a href="?controller=applications"><i class="material-icons">list_alt</i>Applications</a></li>
    <li class="divider"></li>
    <li><a href="?controller=auth&action=logout" class="red-text"><i class="material-icons">logout</i>Logout</a></li>
</ul>

<!-- Mobile Navigation -->
<ul class="sidenav" id="mobile-nav">
    <li>
        <div class="user-view">
            <div class="background blue darken-3"></div>
            <a href="#user">
                <i class="material-icons large white-text">account_circle</i>
            </a>
            <a href="#name"><span class="white-text name"><?php echo $_SESSION['username']; ?></span></a>
            <a href="#email"><span class="white-text email"><?php echo $_SESSION['user_role']; ?></span></a>
        </div>
    </li>
    <li><a href="?controller=dashboard"><i class="material-icons">dashboard</i>Dashboard</a></li>
    <li><a href="?controller=applications"><i class="material-icons">list_alt</i>Applications</a></li>
    <li><a href="?controller=documents&action=repository"><i class="material-icons">archive</i>Documents</a></li>
    <?php if ($_SESSION['user_role'] == ROLE_SUPER_ADMIN): ?>
    <li><a href="?controller=users"><i class="material-icons">people</i>Users</a></li>
    <li><a href="?controller=activity"><i class="material-icons">history</i>Activity</a></li>
    <?php endif; ?>
    <li><div class="divider"></div></li>
    <li><a href="?controller=auth&action=logout" class="red-text"><i class="material-icons">logout</i>Logout</a></li>
</ul>