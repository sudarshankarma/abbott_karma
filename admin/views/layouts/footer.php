            <?php if (isset($_SESSION['user_id'])): ?>
            </div><!-- Close container -->
        </main>
    <?php endif; ?>

    <!-- Materialize JavaScript -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- DataTables -->
    <script type="text/javascript" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.11.5/js/dataTables.material.min.js"></script>
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Custom JavaScript -->
    <script src="assets/js/app.js"></script>
    
    <footer style="background:#f5f5f5;border-top:1px solid #e0e0e0;padding:20px 0;margin-top:40px;">
        <div class="container">
            <div class="center-align grey-text text-darken-2" style="font-size:14px;">
                <?php echo sprintf('Copyright © %s, Karma Management Global Consulting Solutions Pvt Ltd. All Rights Reserved.', date('Y')); ?>
            </div>
        </div>
    </footer>
    
    <script>
    // Initialize Materialize components
    document.addEventListener('DOMContentLoaded', function() {
        // Sidebar initialization
        const sidenavs = document.querySelectorAll('.sidenav');
        M.Sidenav.init(sidenavs);
        
        // Dropdown initialization
        const dropdowns = document.querySelectorAll('.dropdown-trigger');
        M.Dropdown.init(dropdowns, { coverTrigger: false });
        
        // Modal initialization
        const modals = document.querySelectorAll('.modal');
        M.Modal.init(modals);
        
        // Tabs initialization
        const tabs = document.querySelectorAll('.tabs');
        M.Tabs.init(tabs);
    });
    
    // Session timeout warning
    let warningTimer;
    function startSessionTimer() {
        warningTimer = setTimeout(function() {
            M.toast({html: 'Your session will expire in 5 minutes due to inactivity.', classes: 'orange'});
        }, 3300000); // 55 minutes
    }
    
    function resetSessionTimer() {
        clearTimeout(warningTimer);
        startSessionTimer();
    }
    
    // Reset timer on user activity
    document.addEventListener('mousemove', resetSessionTimer);
    document.addEventListener('keypress', resetSessionTimer);
    document.addEventListener('click', resetSessionTimer);
    
    startSessionTimer();
    </script>
</body>
</html>