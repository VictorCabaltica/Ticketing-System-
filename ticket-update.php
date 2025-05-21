<?php
include("connection.php"); // your database connection file

// Ensure that ticket ID is provided via the URL
if (!isset($_GET['id'])) {
    echo "Ticket ID is missing.";
    exit;
}

$ticket_id = $_GET['id'];

// Fetch ticket details from the database
$sql = "SELECT * FROM tickets WHERE ticket_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $ticket_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo "Ticket not found.";
    exit;
}

$row = $result->fetch_assoc();

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Collect form data
    $name = $_POST['name'];
    $email = $_POST['email'];
    $department = $_POST['department'];
    $priority = $_POST['priority'];
    $subject = $_POST['subject'];
    $status = $_POST['status'];

    // Update the ticket in the database
    $update_sql = "UPDATE tickets SET name = ?, email = ?, department = ?, priority = ?, subject = ?, status = ? WHERE ticket_id = ?";
    $stmt_update = $conn->prepare($update_sql);
    $stmt_update->bind_param("ssssssi", $name, $email, $department, $priority, $subject, $status, $ticket_id);

    if ($stmt_update->execute()) {
        echo "Ticket updated successfully!";
        // Redirect to the ticket view page after updating
        header("Location: ticket-view.php?id=$ticket_id");
        exit;
    } else {
        echo "Error updating ticket.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Ticket</title>
    <style>
        body {
            background: linear-gradient(to bottom, #0f0c29, #302b63);
            font-family: 'Segoe UI', sans-serif;
            color: #fff;
            margin: 0;
            padding: 0;
        }

        .card {
            background-color: #3a3a5c;
            max-width: 600px;
            margin: 50px auto;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
        }

        .card h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #FFF5C5;
        }

        .ticket-info label {
            font-weight: bold;
            color: #ffdb58;
        }

        .ticket-info p, .ticket-info input, .ticket-info select {
            background-color: #ffffff10;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 15px;
            color: #fff;
            width: 100%;
        }

        .ticket-info input, .ticket-info select {
            border: 1px solid #ccc;
            color: #fff;
        }

        button {
            background-color: darkviolet;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            margin-top: 20px;
            width: 100%;
            height: 40px;
        }

        button:hover {
            opacity: 0.8s;
        }

        .back-btn {
            display: block;
            background-color: orange;
            border: none;
            padding: 10px 20px;
            color: white;
            border-radius: 6px;
            text-align: center;
            cursor: pointer;
            margin-top: 20px;
            width: 93%;
            height: 20px;
        }

        .back-btn:hover {
            opacity: 0.8;
        }
    </style>
</head>
<body>
    <div class="card">
        <h2>Update Ticket</h2>
        <form method="POST">
            <div class="ticket-info">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" value="<?= htmlspecialchars($row['name']) ?>" required>

                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($row['email']) ?>" required>

                <label for="department">Department:</label>
                <input type="text" id="department" name="department" value="<?= htmlspecialchars($row['department']) ?>" required>

                <label for="priority">Priority:</label>
                <select id="priority" name="priority" required>
                    <option value="low" <?= $row['priority'] == 'low' ? 'selected' : '' ?>>Low</option>
                    <option value="medium" <?= $row['priority'] == 'medium' ? 'selected' : '' ?>>Medium</option>
                    <option value="high" <?= $row['priority'] == 'high' ? 'selected' : '' ?>>High</option>
                    <option value="critical" <?= $row['priority'] == 'critical' ? 'selected' : '' ?>>Critical</option>
                </select>

                <label for="subject">Subject:</label>
                <input type="text" id="subject" name="subject" value="<?= htmlspecialchars($row['subject']) ?>" required>

                <label for="status">Status:</label>
                <select id="status" name="status" required>
                    <option value="open" <?= $row['status'] == 'open' ? 'selected' : '' ?>>Open</option>
                    <option value="in_progress" <?= $row['status'] == 'in_progress' ? 'selected' : '' ?>>In Progress</option>
                    <option value="closed" <?= $row['status'] == 'closed' ? 'selected' : '' ?>>Closed</option>
                </select>

                <button type="submit">Update Ticket</button>
            </div>
        </form>

        <!-- Back button to view-ticket.php -->
        <a href="ticket-view.php?id=<?= $ticket_id ?>" class="back-btn">← Back to Ticket</a>
    </div>
</body>
</html>
