<?php
session_start();
include 'agent-navbar.php';


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

// Fetch all open tickets assigned to the agent
$stmt = $pdo->prepare("SELECT t.ticket_id, t.subject, t.status, t.priority, t.created_at FROM tickets t
                       INNER JOIN assignment a ON t.ticket_id = a.ticket_id
                       WHERE a.agent_id = (SELECT agent_id FROM agent WHERE email = ?) AND t.status != 'closed' ORDER BY t.created_at DESC");
$stmt->execute([$agentEmail]);
$tickets = $stmt->fetchAll();
$countStmt = $pdo->prepare("
    SELECT 
      SUM(CASE WHEN t.status = 'open' THEN 1 ELSE 0 END) AS open_count,
      SUM(CASE WHEN t.status = 'in_progress' THEN 1 ELSE 0 END) AS in_progress_count,
      SUM(CASE WHEN t.status = 'closed' THEN 1 ELSE 0 END) AS closed_count
    FROM tickets t
    INNER JOIN assignment a ON t.ticket_id = a.ticket_id
    WHERE a.agent_id = (SELECT agent_id FROM agent WHERE email = ?)
");
$countStmt->execute([$agentEmail]);
$counts = $countStmt->fetch();

// Handle the agent's reply submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ticket_id']) && isset($_POST['response'])) {
    $ticket_id = $_POST['ticket_id'];
    $response = $_POST['response'];

    // Insert the agent's reply into the replies table
    $insertReplyStmt = $pdo->prepare("INSERT INTO replies (ticket_id, sender_email, reply, sender_role) VALUES (?, ?, ?, 'agent')");
    $insertReplyStmt->execute([$ticket_id, $agentEmail, $response]);

    // Update the ticket status to in_progress if it isn't already
    $updateTicketStmt = $pdo->prepare("UPDATE tickets SET status = 'in_progress' WHERE ticket_id = ?");
    $updateTicketStmt->execute([$ticket_id]);

    // Redirect to the same page to see the new reply
    header("Location: agent-dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Agent Dashboard - Ticketing System</title>
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
        h2 {
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #ecf0f1;
        }
        .status {
            padding: 6px 12px;
            border-radius: 20px;
            color: white;
            font-weight: 500;
            text-transform: capitalize;
        }
        .open { background-color: #e67e22; }
        .in_progress { background-color: #3498db; }
        .closed { background-color: #2ecc71; }
        .attachment a {
            color: #2980b9;
            text-decoration: underline;
        }
        .reply-form {
            margin-top: 20px;
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
        .reply-form button {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .reply-form button:hover {
            background-color: #0056b3;
        }
        .back-btn {
            display: inline-block;
            margin-top: 30px;
            background-color: #34495e;
            color: white;
            padding: 10px 18px;
            text-decoration: none;
            border-radius: 5px;
        }
        .card-container {
    display: flex;
    gap: 20px;
    margin-bottom: 30px;
    justify-content: center;
    flex-wrap: wrap;
    width: 74%;
    margin-left: 200px;
}
.status-card {
    background: white;
    padding: 20px 30px;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    text-align: center;
    flex: 1 1 250px;
    transition: transform 0.2s;
    margin-top: 50px;
}
.status-card:hover {
    transform: translateY(-5px);
}
.status-card h3 {
    margin: 0;
    font-size: 36px;
    color: #333;
}
.status-card p {
    margin-top: 8px;
    font-size: 16px;
    color: #777;
}
.open-card {
    border-top: 5px solid #e67e22;
}
.progress-card {
    border-top: 5px solid #3498db;
}
.closed-card {
    border-top: 5px solid #2ecc71;
}

    </style>
</head>
<body>
<div class="card-container">
    <div class="status-card open-card">
        <h3><?= $counts['open_count'] ?></h3>
        <p>Open Tickets</p>
    </div>
    <div class="status-card progress-card">
        <h3><?= $counts['in_progress_count'] ?></h3>
        <p>In Progress</p>
    </div>
    <div class="status-card closed-card">
        <h3><?= $counts['closed_count'] ?></h3>
        <p>Closed Tickets</p>
    </div>
</div>

<div class="container">
    <h2>Assigned Tickets</h2>

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
                        <td><span class="status <?= str_replace('-', '_', htmlspecialchars($ticket['status'])) ?>"><?= ucfirst($ticket['status']) ?></span></td>
                        <td><?= ucfirst($ticket['priority']) ?></td>
                        <td><?= date("F j, Y g:i A", strtotime($ticket['created_at'])) ?></td>
                        <td><a href="view_ticket_agent.php?ticket_id=<?= htmlspecialchars($ticket['ticket_id']) ?>">View Ticket</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No open tickets assigned to you.</p>
    <?php endif; ?>

    <a class="back-btn" href="agent-dashboard.php">← Back to Dashboard</a>
</div>

</body>
</html>
