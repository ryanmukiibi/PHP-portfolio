<?php
session_start();
session_unset();
session_destroy();
header("Location: portal_access.php");
exit;
?>