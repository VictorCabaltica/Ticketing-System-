<?php
include 'connection.php';

if (isset($_GET['id'])) {
    $ticket_id = $_GET['id'];

    // Delete ticket with prepared statement
    $stmt = $conn->prepare("DELETE FROM tickets WHERE ticket_id = ?");
    $stmt->bind_param("i", $ticket_id);

    if ($stmt->execute()) {
        // Redirect after successful deletion
        header("Location: manage-tickets.php?message=deleted");
        exit;
    } else {
        echo "❌ Error deleting ticket: " . $conn->error;
    }

    $stmt->close();
} else {
    // If no ID is passed, show a message and a back link
    echo "<p style='color:white; font-family:sans-serif; background-color:#302b63; padding:20px; border-radius:10px; text-align:center;'>
            No ticket ID specified.<br><br>
            <a href='manage-tickets.php' style='color:yellow; text-decoration:none;'>← Go Back</a>
          </p>";
}
?>
