<?php $title = 'Login'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-blue: #28387d;
            --accent-orange: #e55c25;
        }

        * {
            box-sizing: border-box;
        }

        body, html {
            height: 100%;
            margin: 0;
            padding: 0;
            font-family: 'Poppins', sans-serif;
            background-color: #fff;
            overflow-x: hidden; /* Prevent horizontal scroll */
        }

        /* --- LAYOUT CONTAINER (Flexbox Split) --- */
        .split-screen {
            display: flex;
            flex-direction: row;
            height: 100vh; /* Force full viewport height */
            width: 100%;
        }

        /* --- LEFT PANEL (Hero) --- */
        .left-panel {
            flex: 1.2; /* Takes slightly more width than the form on large screens */
            background-color: #F4F6F8;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 40px;
            position: relative;
        }

        .left-panel-content {
            max-width: 600px;
            width: 100%;
            z-index: 2;
        }

        .karma-logo {
            position: absolute;
            top: 40px;
            left: 50px;
            width: 140px;
            height: auto;
        }
        .abbott-logo {
            position: absolute;
            top: 40px;
            right: 50px;
            width: 140px;
            height: auto;
        }
        .left-panel h2 {
            font-weight: 700;
            color: var(--primary-blue);
            font-size: 2.5rem; /* Responsive font size */
            line-height: 1.2;
            margin-bottom: 2rem;
            text-align: left;
        }

        .hero-image {
            width: 100%;
            height: auto;
            max-height: 60vh; /* Prevent image from being too tall on small laptops */
            object-fit: contain;
            filter: drop-shadow(0 10px 20px rgba(0,0,0,0.08));
        }

        /* --- RIGHT PANEL (Form) --- */
        .right-panel {
            flex: 1;
            background-color: #ffffff;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center; /* Center horizontally */
            padding: 40px;
            position: relative;
            overflow-y: auto; /* Allow internal scrolling if screen is too short vertically */
        }

        .login-container {
            width: 100%;
            max-width: 400px; /* Keep form nice and compact */
        }

        .form-logo {
            display: flex;
            align-items: center;
            margin-bottom: 30px;
            font-weight: 700;
            font-size: 24px;
            color: var(--primary-blue);
        }

        .form-logo img {
            height: 40px;
            margin-right: 12px;
        }

        .form-header h4 {
            color: var(--primary-blue);
            font-weight: 700;
            margin: 0 0 5px 0;
            font-size: 2rem;
        }

        .form-header p {
            color: #28387d;
            font-weight: 500;
            margin-bottom: 10px;
        }

        .sub-text {
            font-size: 0.85rem;
            color: #9e9e9e;
            line-height: 1.5;
            margin-bottom: 30px;
            display: block;
        }

        /* --- INPUT STYLES --- */
        .input-field {
            margin-bottom: 20px;
        }

        .input-field input[type=text], 
        .input-field input[type=password] {
            border-bottom: 1px solid #e0e0e0;
            box-shadow: none !important;
            height: 3rem;
        }

        /* Focus State (Orange) */
        .input-field input[type=text]:focus, 
        .input-field input[type=password]:focus {
            border-bottom: 1px solid var(--accent-orange) !important;
            box-shadow: 0 1px 0 0 var(--accent-orange) !important;
        }

        .input-field input[type=text]:focus + label, 
        .input-field input[type=password]:focus + label {
            color: var(--accent-orange) !important;
        }

        /* --- BUTTONS & LINKS --- */
        .btn-login {
            background-color: var(--accent-orange);
            width: 100%;
            height: 54px;
            line-height: 54px;
            font-size: 16px;
            font-weight: 600;
            letter-spacing: 0.5px;
            border-radius: 6px;
            box-shadow: 0 4px 15px rgba(229, 92, 37, 0.25);
            margin-top: 15px;
            border: none;
            cursor: pointer;
            text-transform: uppercase;
            color: #fff;
            transition: 0.3s;
        }

        .btn-login:hover {
            background-color: #d04b1b;
            box-shadow: 0 6px 20px rgba(229, 92, 37, 0.35);
        }

        .remember-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            font-size: 0.9rem;
        }

        .forgot-link {
            color: var(--accent-orange) !important;
            font-weight: 500;
        }

        /* Checkbox Override (Orange) */
        [type="checkbox"].filled-in:checked + span:not(.lever):after {
            border: 2px solid var(--accent-orange);
            background-color: var(--accent-orange);
        }

        .form-footer {
            margin-top: 25px;
            text-align: left;
            font-size: 0.9rem;
            color: #888;
        }
        .form-footer a {
            color: var(--accent-orange);
            font-weight: 600;
        }

        /* --- RESPONSIVE MEDIA QUERIES --- */
        /* Tablet & Mobile (Below 992px) */
        @media only screen and (max-width: 992px) {
            .left-panel {
                display: none; /* Hide hero on smaller screens to focus on login */
            }
            
            .right-panel {
                width: 100%;
                flex: none;
                height: 100vh;
            }
        }

        /* Small Mobile Adjustments */
        @media only screen and (max-width: 600px) {
            .left-panel { display: none; }
            .right-panel { padding: 20px; }
            .form-header h4 { font-size: 1.8rem; }
        }

        /* Height adjustments for short screens (e.g. landscape mobile) */
        @media only screen and (max-height: 600px) {
            .split-screen { height: auto; min-height: 100vh; }
            .left-panel, .right-panel { padding: 40px 20px; }
        }
    </style>
</head>
<body>

<div class="split-screen">
    <link rel="shortcut icon" href="../images/abbott_favicon.ico" type="image/x-icon"/>
    <div class="left-panel">
        <img src="../images/karma_logo.png" alt="Karma Logo" class="karma-logo">
        <img src="../images/Abbott_Laboratories_logo.png" alt="Abbott Logo" class="abbott-logo">
        <div class="left-panel-content">
            <h2>EPF / EPS </h2>
            <img src="assets/images/epf-eps.jpg" alt="EPF EPS image" class="hero-image">
        </div>
    </div>

    <div class="right-panel">
        <div class="login-container">
            
            <div class="form-logo">
                <img src="../images/karma_logo.png" alt="Karma Icon">
                <!-- <span>Karma</span> -->
            </div>

            <div class="form-header">
                <h4>Login</h4>
                <p>Login to your account</p>
                <!-- <span class="sub-text">Thank you for getting back to Karma, let's access the best recommendation contacts for you.</span> -->
            </div>

            <?php if (!empty($error)): ?>
                <div class="card-panel red lighten-5 red-text text-darken-4 z-depth-0" style="border: 1px solid #ffcdd2;">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['timeout'])): ?>
                <div class="card-panel orange lighten-5 orange-text text-darken-4 z-depth-0">
                    Your session has expired due to inactivity.
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['logout'])): ?>
                <div class="card-panel green lighten-5 green-text text-darken-4 z-depth-0">
                    Logged out successfully.
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="input-field">
                    <input id="username" name="username" type="text" class="validate" required>
                    <label for="username">Username or Email</label>
                </div>
                
                <div class="input-field">
                    <input id="password" name="password" type="password" class="validate" required>
                    <label for="password">Password</label>
                </div>
                
                <!-- <div class="remember-row">
                    <label>
                        <input type="checkbox" class="filled-in" name="remember_me" />
                        <span>Remember me</span>
                    </label>
                    <a href="#" class="forgot-link">Reset Password?</a>
                </div> -->

                <button class="btn waves-effect waves-light btn-login" type="submit">
                    SIGN IN
                </button>
            </form>
<!-- 
            <div class="form-footer">
                Don't have an account yet? <a href="#">Join Karma!</a>
            </div>
             -->
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
</body>
</html>