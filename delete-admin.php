<?php
include 'connection.php';

if (isset($_GET['id'])) {
    $user_id = $_GET['id'];
    
    // SQL query to delete the user from the database
    $sql = "DELETE FROM admin WHERE admin_id = ?";
    
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $user_id); // "i" stands for integer
        if ($stmt->execute()) {
            // Redirect back to user management page with success message
            header("Location: admin-management.php?message=Admin deleted successfully.");
            exit();
        } else {
            // If there was an error deleting the user
            echo "<script>alert('Error deleting Admin.'); window.location.href = 'admin-management.php';</script>";
        }
    } else {
        echo "<script>alert('Error preparing query.'); window.location.href = 'admin-management.php';</script>";
    }
    
    $stmt->close();
    $conn->close();
} else {
    echo "<script>alert('Invalid admin ID.'); window.location.href = 'admin-management.php';</script>";
}
?>
