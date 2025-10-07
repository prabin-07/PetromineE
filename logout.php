<?php
require_once 'includes/session.php';
require_once 'includes/auth.php';

// Perform secure logout
logout();

// Redirect to home page with logout message
header('Location: index.php?logged_out=1');
exit();
?>