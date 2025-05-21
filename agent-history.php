<?php
// Start session to access session variables
session_start();

// Include your database connection file (make sure the path is correct)

// Database credentials
$host = 'localhost';       // Change to your host if needed
$dbname = 'ticketing_system';  // Change to your database name
$username = 'root';        // Your database username (usually 'root' in local development)
$password = '';            // Your database password (leave empty if none)

// Set up PDO connection
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);  // Set error mode to exceptions
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit();
}



// Check if session is active and the user is logged in (assuming 'email' is stored in session)
if (!isset($_SESSION['agent_email'])) {
    // Redirect to login page if not logged in
    header("Location: login.php"); // Update with your actual login page
    exit();
}

// Get agent's email from session
$agentEmail = $_SESSION['agent_email']; 

// Fetch all tickets assigned to the agent (including closed tickets)
$ticketStmt = $pdo->prepare("SELECT t.ticket_id, t.subject, t.status, t.priority, t.created_at, t.description 
                             FROM tickets t
                             INNER JOIN assignment a ON t.ticket_id = a.ticket_id
                             WHERE a.agent_id = (SELECT agent_id FROM agent WHERE email = ?)
                             ORDER BY t.created_at DESC");
$ticketStmt->execute([$agentEmail]);
$tickets = $ticketStmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket History - All Tickets</title>
    <style>
        /* General container */
        .container {
            width: 80%;
            margin: 0 auto;
            padding: 20px;
        }

        /* Table Styles */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #f4f6f9;
        }

        td a {
            color: #3498db;
            text-decoration: none;
        }

        td a:hover {
            text-decoration: underline;
        }

        /* Ticket Details */
        .ticket-details {
            background: #f4f6f9;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .ticket-details p {
            font-size: 16px;
            color: #555;
        }

        /* Back Button */
        .back-btn {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }

        .back-btn:hover {
            background-color: #2980b9;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Ticket History - All Tickets</h2>

        <?php if (count($tickets) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Ticket ID</th>
                        <th>Subject</th>
                        <th>Status</th>
                        <th>Priority</th>
                        <th>Date Submitted</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tickets as $ticket): ?>
                        <tr>
                            <td>#<?= htmlspecialchars($ticket['ticket_id']) ?></td>
                            <td><?= htmlspecialchars($ticket['subject']) ?></td>
                            <td><?= ucfirst($ticket['status']) ?></td>
                            <td><?= ucfirst($ticket['priority']) ?></td>
                            <td><?= date("F j, Y g:i A", strtotime($ticket['created_at'])) ?></td>
                            <td><a href="view_ticket_history.php?ticket_id=<?= htmlspecialchars($ticket['ticket_id']) ?>">View Details</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No tickets found.</p>
        <?php endif; ?>
    </div>

    <?php
    // Ticket Detail Page (view_ticket_history.php)
    if (isset($_GET['ticket_id'])) {
        $ticket_id = $_GET['ticket_id'];
        
        // Fetch ticket details
        $ticketStmt = $pdo->prepare("SELECT * FROM tickets WHERE ticket_id = ?");
        $ticketStmt->execute([$ticket_id]);
        $ticket = $ticketStmt->fetch();
        
        // Fetch all replies for the ticket
        $repliesStmt = $pdo->prepare("SELECT * FROM replies WHERE ticket_id = ? ORDER BY created_at DESC");
        $repliesStmt->execute([$ticket_id]);
        $replies = $repliesStmt->fetchAll();
    }
    ?>

    <?php if (isset($ticket)): ?>
        <div class="container">
            <h2>Ticket Details - #<?= htmlspecialchars($ticket['ticket_id']) ?></h2>

            <div class="ticket-details">
                <p><strong>Subject:</strong> <?= htmlspecialchars($ticket['subject']) ?></p>
                <p><strong>Priority:</strong> <?= htmlspecialchars($ticket['priority']) ?></p>
                <p><strong>Status:</strong> <?= ucfirst($ticket['status']) ?></p>
                <p><strong>Description:</strong> <?= nl2br(htmlspecialchars($ticket['description'])) ?></p>
                <p><strong>Created At:</strong> <?= date("F j, Y g:i A", strtotime($ticket['created_at'])) ?></p>
            </div>

            <div class="replies">
                <h3>Replies</h3>
                <?php if (count($replies) > 0): ?>
                    <?php foreach ($replies as $reply): ?>
                        <div class="reply">
                            <p><strong><?= htmlspecialchars($reply['sender_role']) ?>:</strong> <?= nl2br(htmlspecialchars($reply['reply'])) ?></p>
                            <p><small>On <?= date("F j, Y g:i A", strtotime($reply['created_at'])) ?></small></p>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No replies found for this ticket.</p>
                <?php endif; ?>
            </div>

            <a class="back-btn" href="ticket-history.php">← Back to Ticket History</a>
        </div>
    <?php endif; ?>
</body>
</html>
