<?php
require_once 'config.php';

echo "<h1>Session Test</h1>";
echo "<pre>";
echo "Session ID: " . session_id() . "\n";
echo "Session Status: " . session_status() . "\n";
echo "Session Data: \n";
print_r($_SESSION);
echo "</pre>";

// Test setting session
$_SESSION['test_time'] = date('Y-m-d H:i:s');
echo "<p>Test session value set: " . $_SESSION['test_time'] . "</p>";

// Test links
echo "<h2>Test Links:</h2>";
echo "<ul>";
echo "<li><a href='test_session.php'>Reload this page</a></li>";
echo "<li><a href='index_debug.php'>Go to Welcome Page (Debug)</a></li>";
echo "<li><a href='form.php?force_consent=true'>Force Consent and go to Form</a></li>";
echo "</ul>";
?>