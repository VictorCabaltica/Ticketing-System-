<?php
include 'connection.php'; // (your connection file)

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    date_default_timezone_set('Asia/Manila'); // Set timezone

    $ticket_id = $_POST['ticket_id'];
    $new_status = $_POST['status']; // e.g., 'in_progress'

    // Fetch the priority of the ticket first
    $ticket_query = "SELECT priority FROM tickets WHERE ticket_id = $ticket_id";
    $ticket_result = mysqli_query($conn, $ticket_query);
    $ticket = mysqli_fetch_assoc($ticket_result);
    $priority = $ticket['priority'];

    if ($new_status == 'in_progress') {
        $in_progress_time = date('Y-m-d H:i:s');

        // Compute Resolution Due based on Priority
        switch ($priority) {
            case 'critical':
                $resolution_due = date('Y-m-d H:i:s', strtotime('+8 hours', strtotime($in_progress_time)));
                break;
            case 'high':
                $resolution_due = date('Y-m-d H:i:s', strtotime('+12 hours', strtotime($in_progress_time)));
                break;
            case 'medium':
                $resolution_due = date('Y-m-d H:i:s', strtotime('+24 hours', strtotime($in_progress_time)));
                break;
            case 'low':
                $resolution_due = date('Y-m-d H:i:s', strtotime('+48 hours', strtotime($in_progress_time)));
                break;
            default:
                $resolution_due = date('Y-m-d H:i:s', strtotime('+48 hours', strtotime($in_progress_time)));
                break;
        }

        // Update status and resolution_due
        $update_query = "UPDATE tickets 
                         SET status = 'in_progress', resolution_due = '$resolution_due' 
                         WHERE ticket_id = $ticket_id";
    } else {
        // If changing to any other status (closed, etc.) without setting resolution_due
        $update_query = "UPDATE tickets 
                         SET status = '$new_status' 
                         WHERE ticket_id = $ticket_id";
    }

    if (mysqli_query($conn, $update_query)) {
        echo "Ticket updated successfully!";
    } else {
        echo "Error: " . mysqli_error($conn);
    }

    mysqli_close($conn);
}
?>
