<?php
// header.php - Common header for all pages
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Registration - Piramal & Abbott</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.8/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-blue: #3E4095;
            --accent-orange: #F26B35;
        }
        
        .main-header {
            background: linear-gradient(135deg, var(--primary-blue) 0%, #2d2f70 100%);
            color: white;
            padding: 15px 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .logo-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .logo-placeholder {
            width: 150px;
            height: 60px;
            background-size: contain;
            background-repeat: no-repeat;
            background-position: center;
            background-color: #ffffff;
        }
        
        .app-title {
            text-align: center;
            margin: 0;
        }
        
        .app-title h1 {
            font-size: 1.5rem;
            margin: 0;
            font-weight: 700;
        }
        
        .app-title p {
            font-size: 0.9rem;
            margin: 0;
            opacity: 0.9;
        }
        
        .user-info {
            text-align: right;
            font-size: 0.9rem;
        }
        
        .main-footer {
            background: #2d2f70;
            color: white;
            padding: 30px 0 20px;
            margin-top: 50px;
        }
        
        .footer-links a {
            color: #ccc;
            text-decoration: none;
            margin: 0 10px;
        }
        
        .footer-links a:hover {
            color: white;
        }
        
        .copyright {
            border-top: 1px solid #3E4095;
            padding-top: 15px;
            margin-top: 20px;
            font-size: 0.8rem;
            color: #ccc;
        }
    </style>
</head>
<body>
    <!-- Main Header -->
    <header class="main-header">
        <div class="container">
            <link rel="shortcut icon" href="images/abbott_favicon.ico" type="image/x-icon"/>
             <div class="logo-container">
                <div class="logo-left">
                    <div class="logo-placeholder" style="background-image: url('images/karma_logo.png');"></div>
                </div>

                <div class="app-title">
                    <h1>Employee UAN Onboarding Portal</h1>
                    <p>Piramal & Abbott Employee Registration System</p>
                </div>
                
                <div class="logo-right">
                    <div class="logo-placeholder" style="background-image: url('images/Abbott_Laboratories_logo.png');"></div>
                </div>
            </div>
        </div>
    </header>
    