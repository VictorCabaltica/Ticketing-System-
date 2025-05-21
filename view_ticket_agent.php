<?php
session_start();

if (!isset($_SESSION['agent_email'])) {
    echo "<script>alert('Access denied. Please log in as an agent.'); window.location.href = 'login-agent.php';</script>";
    exit();
}

$agentEmail = $_SESSION['agent_email'];

// Database connection
$host = 'localhost';
$db   = 'ticketing_db';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Fetch the ticket details
if (isset($_GET['ticket_id'])) {
    $ticket_id = $_GET['ticket_id'];
    $stmt = $pdo->prepare("SELECT * FROM tickets WHERE ticket_id = ?");
    $stmt->execute([$ticket_id]);
    $ticket = $stmt->fetch();
    
    // Fetch the replies
    $repliesStmt = $pdo->prepare("SELECT * FROM replies WHERE ticket_id = ? ORDER BY reply_date ASC");
    $repliesStmt->execute([$ticket_id]);
    $replies = $repliesStmt->fetchAll();
    $replyCount = count($replies);
}

// Handle agent's reply submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ticket_id']) && isset($_POST['response'])) {
    $ticket_id = $_POST['ticket_id'];
    $response = $_POST['response'];

    $insertReplyStmt = $pdo->prepare("INSERT INTO replies (ticket_id, sender_email, reply, sender_role) VALUES (?, ?, ?, 'agent')");
    $insertReplyStmt->execute([$ticket_id, $agentEmail, $response]);

    $updateTicketStmt = $pdo->prepare("UPDATE tickets SET status = 'in_progress' WHERE ticket_id = ?");
    $updateTicketStmt->execute([$ticket_id]);

    header("Location: view_ticket_agent.php?ticket_id=" . $ticket_id);
    exit();
}

// Handle ticket status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $new_status = $_POST['status'];

    $updateStatusStmt = $pdo->prepare("UPDATE tickets SET status = ? WHERE ticket_id = ?");
    $updateStatusStmt->execute([$new_status, $ticket_id]);

    // No more deleting replies on close

    header("Location: view_ticket_agent.php?ticket_id=" . $ticket_id);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Ticket - Agent</title>
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background-color: #f4f6f9;
            color: #333;
        }
        .container {
            max-width: 1100px;
            margin: 40px auto;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h2, h3 {
            margin-bottom: 20px;
        }
        .ticket-details {
            margin-bottom: 20px;
            background-color: #ecf0f1;
            padding: 15px;
            border-radius: 8px;
        }
        .response {
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            padding: 15px;
            margin-top: 10px;
            border-radius: 5px;
        }
        .reply-form {
            margin-top: 30px;
            background-color: #f4f6f9;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        .reply-form textarea {
            width: 100%;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-size: 14px;
            margin-bottom: 10px;
            height: 120px;
        }
        .reply-form button, button, select {
            background-color: #007bff;
            color: white;
            padding: 8px 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }
        .reply-form button:hover, button:hover {
            background-color: #0056b3;
        }
        .back-btn {
            display: inline-block;
            margin-top: 20px;
            text-decoration: none;
            color: #007bff;
        }
        select {
            margin: 10px 0;
            padding: 5px;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Ticket #<?= htmlspecialchars($ticket['ticket_id']) ?> - <?= htmlspecialchars($ticket['subject']) ?></h2>

    <div class="ticket-details">
        <p><strong>Department:</strong> <?= htmlspecialchars($ticket['department']) ?></p>
        <p><strong>Priority:</strong> <?= ucfirst(htmlspecialchars($ticket['priority'])) ?></p>
        <p><strong>Status:</strong> <span class="status"><?= ucfirst(htmlspecialchars($ticket['status'])) ?></span></p>
        <p><strong>Description:</strong></p>
        <p><?= nl2br(htmlspecialchars($ticket['description'])) ?></p>
    </div>

    <!-- Status Update Form -->
    <?php if ($ticket['status'] !== 'closed'): ?>
        <form method="POST" action="view_ticket_agent.php?ticket_id=<?= htmlspecialchars($ticket['ticket_id']) ?>">
            <label for="status"><strong>Update Status:</strong></label>
            <select name="status" id="status" required>
                <option value="open" <?= $ticket['status'] === 'open' ? 'selected' : '' ?>>Open</option>
                <option value="in_progress" <?= $ticket['status'] === 'in_progress' ? 'selected' : '' ?>>In Progress</option>
                <option value="closed">Closed</option>
            </select>
            <button type="submit" name="update_status">Update</button>
        </form>
    <?php else: ?>
        <p><strong>Status:</strong> This ticket is <span style="color: green;"><strong>Closed</strong></span>.</p>
    <?php endif; ?>

    <!-- Collapsible Replies -->
    <h3>Conversation History:</h3>
    <button onclick="toggleReplies()">Toggle Replies (<?= $replyCount ?>)</button>

    <div id="replyContainer" style="display: none; margin-top: 10px;">
        <?php foreach ($replies as $reply): ?>
            <div class="response">
                <p><strong><?= ucfirst($reply['sender_role']) ?>:</strong> <?= nl2br(htmlspecialchars($reply['reply'])) ?></p>
                <p><small>Posted on: <?= date("F j, Y g:i A", strtotime($reply['reply_date'])) ?></small></p>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Agent's Reply Form -->
    <?php if ($ticket['status'] !== 'closed'): ?>
        <div class="reply-form">
            <h3>Agent's Response</h3>
            <form action="view_ticket_agent.php?ticket_id=<?= htmlspecialchars($ticket['ticket_id']) ?>" method="POST">
                <input type="hidden" name="ticket_id" value="<?= htmlspecialchars($ticket['ticket_id']) ?>">
                <textarea name="response" required placeholder="Enter your reply here..."></textarea>
                <button type="submit">Send Reply</button>
            </form>
        </div>
    <?php endif; ?>

    <a href="agent-dashboard.php" class="back-btn">← Back to Dashboard</a>
</div>

<script>
    function toggleReplies() {
        const container = document.getElementById('replyContainer');
        container.style.display = container.style.display === 'none' ? 'block' : 'none';
    }
</script>

</body>
</html>
