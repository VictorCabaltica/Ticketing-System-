<?php
include 'connection.php';

// Check if the ID is provided
if (isset($_GET['id'])) {
    $agent_id = $_GET['id'];

    // Prepare a delete query
    $stmt = $conn->prepare("DELETE FROM agent WHERE agent_id = ?");
    $stmt->bind_param("i", $agent_id);

    // Execute the query and check if successful
    if ($stmt->execute()) {
        echo "Agent deleted successfully!";
        header("Location: agent-management.php"); // Redirect after deletion
    } else {
        echo "Error deleting agent.";
    }

    $stmt->close();
} else {
    echo "No agent ID specified.";
}
?>
