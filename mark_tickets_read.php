<?php
include("connection.php");

// Mark all tickets as read
$conn->query("UPDATE tickets SET is_read = 1 WHERE is_read = 0");
?>
