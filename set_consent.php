<?php
require_once 'config.php';

// Set consent in session
if (isset($_POST['consent']) && $_POST['consent'] === 'true') {
    $_SESSION['consent_given'] = true;
}

// Redirect to form
header('Location: form.php');
exit;
?>