<?php $title = 'Login'; ?>
<div class="row" style="margin-top: 100px;">
    <div class="col s12 m6 offset-m3">
        <div class="card">
            <div class="card-content">
                <div class="row">
                    <div class="col s12 center">
                        <i class="material-icons large blue-text">admin_panel_settings</i>
                        <h4 class="card-title"><?php echo SITE_NAME; ?></h4>
                        <p class="grey-text">Sign in to your account</p>
                    </div>
                </div>

                <?php if (!empty($error)): ?>
                    <div class="card-panel red lighten-4 red-text text-darken-4">
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($_GET['timeout'])): ?>
                    <div class="card-panel orange lighten-4 orange-text text-darken-4">
                        Your session has expired due to inactivity. Please login again.
                    </div>
                <?php endif; ?>

                <?php if (isset($_GET['logout'])): ?>
                    <div class="card-panel green lighten-4 green-text text-darken-4">
                        You have been successfully logged out.
                    </div>
                <?php endif; ?>

                <form method="POST">
                    <div class="row">
                        <div class="input-field col s12">
                            <i class="material-icons prefix">person</i>
                            <input id="username" name="username" type="text" class="validate" required>
                            <label for="username">Username</label>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="input-field col s12">
                            <i class="material-icons prefix">lock</i>
                            <input id="password" name="password" type="password" class="validate" required>
                            <label for="password">Password</label>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col s12">
                            <button class="btn waves-effect waves-light blue darken-3" type="submit" style="width: 100%;">
                                Login
                                <i class="material-icons right">send</i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            
            <div class="card-action center">
                <div class="card-panel blue lighten-5">
                    <strong>Demo Credentials:</strong><br>
                    <strong>Username:</strong> superadmin<br>
                    <strong>Password:</strong> admin123
                </div>
            </div>
        </div>
    </div>
</div>