<?php
session_start();

/* Destroy all company session data */
unset($_SESSION['company_id']);
unset($_SESSION['company_name']);

/* Destroy full session */
session_destroy();

/* Redirect to company login page */
header("Location: company_login.php");
exit();
?>