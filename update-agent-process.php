<?php
include 'connection.php';

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get data from the form
    $agent_id = $_POST['agent_id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $status = $_POST['status'];

    // Prepare the SQL query to update agent details
    $sql = "UPDATE agent SET name = ?, email = ?, status = ? WHERE agent_id = ?";

    // Prepare and bind
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("sssi", $name, $email, $status, $agent_id);

        // Execute the query
        if ($stmt->execute()) {
            // Redirect to the agent management page
            header("Location: agent-management.php");
            exit();
        } else {
            echo "Error updating agent: " . $conn->error;
        }
    } else {
        echo "Error preparing the statement.";
    }
}
?>
