<?php
session_start();

if (!isset($_SESSION['user_email'])) {
    echo "<script>alert('Access denied. Please log in first.'); window.location.href = 'registration-users.php';</script>";
    exit();
}

$userEmail = $_SESSION['user_email'];
 // Now you can use this throughout the page

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

// Fetch ticket statistics
$totalStmt = $pdo->prepare("SELECT COUNT(*) FROM tickets WHERE email = ?");
$totalStmt->execute([$userEmail]);
$totalTickets = $totalStmt->fetchColumn();

$openStmt = $pdo->prepare("SELECT COUNT(*) FROM tickets WHERE email = ? AND status = 'open'");
$openStmt->execute([$userEmail]);
$openTickets = $openStmt->fetchColumn();

$progressStmt = $pdo->prepare("SELECT COUNT(*) FROM tickets WHERE email = ? AND status = 'in-progress'");
$progressStmt->execute([$userEmail]);
$inProgressTickets = $progressStmt->fetchColumn();

$resolvedStmt = $pdo->prepare("SELECT COUNT(*) FROM tickets WHERE email = ? AND status = 'resolved'");
$resolvedStmt->execute([$userEmail]);
$resolvedTickets = $resolvedStmt->fetchColumn();

// Fetch recent tickets
$ticketStmt = $pdo->prepare("SELECT * FROM tickets WHERE email = ? ORDER BY created_at DESC LIMIT 5");
$ticketStmt->execute([$userEmail]);
$tickets = $ticketStmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>User Dashboard - Ticketing System</title>
  <style>
    /* (same styles as before — keep your styles here) */
    body {
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
      background-color: #f4f6f9;
      color: #333;
    }
    .container {
      max-width: 1200px;
      margin: auto;
      padding: 20px;
    }
    .dashboard-header h1 {
      font-size: 1.8rem;
    }
    .dashboard-header p {
      color: #7f8c8d;
    }
    .cards {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
      gap: 20px;
      margin: 30px 0;
    }
    .card {
      background: white;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
    }
    .card h2 {
      font-size: 1rem;
      color: #7f8c8d;
    }
    .card p {
      font-size: 1.7rem;
      font-weight: bold;
      color: #2c3e50;
      margin-top: 5px;
    }
    .recent-tickets {
      background-color: white;
      border-radius: 10px;
      padding: 20px;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
    }
    .recent-tickets h3 {
      margin-bottom: 20px;
    }
    table {
      width: 100%;
      border-collapse: collapse;
    }
    th, td {
      text-align: left;
      padding: 12px;
      border-bottom: 1px solid #ecf0f1;
    }
    th {
      background-color: #ecf0f1;
    }
    .status {
      padding: 6px 10px;
      border-radius: 20px;
      font-size: 0.85rem;
      color: white;
      display: inline-block;
    }
    .open { background-color: #e67e22; }
    .in-progress { background-color: #3498db; }
    .resolved { background-color: #2ecc71; }
    .actions {
      margin-top: 30px;
    }
    .actions a {
      display: inline-block;
      background-color: #3498db;
      color: white;
      padding: 10px 18px;
      border-radius: 6px;
      text-decoration: none;
      font-weight: 500;
      margin-right: 15px;
      transition: background 0.3s ease-in-out;
    }
    .actions a:hover {
      background-color: #2980b9;
    }
  </style>
</head>
<body>

  <?php include 'navbar.php'; ?>

  <div class="container">
    <div class="dashboard-header">
      <h1>Welcome back, <?= htmlspecialchars($userEmail) ?>!</h1>
      <p>Manage your support tickets and track updates in one place.</p>
    </div>

    <div class="cards">
      <div class="card">
        <h2>Your Tickets</h2>
        <p><?= $totalTickets ?></p>
      </div>
      <div class="card">
        <h2>Open</h2>
        <p><?= $openTickets ?></p>
      </div>
      <div class="card">
        <h2>In Progress</h2>
        <p><?= $inProgressTickets ?></p>
      </div>
      <div class="card">
        <h2>Resolved</h2>
        <p><?= $resolvedTickets ?></p>
      </div>
    </div>

    <div class="actions">
      <a href="submit_ticket.php">➕ Submit New Ticket</a>
      <a href="view_ticket.php">📄 View My Tickets</a>
    </div>

    <div class="recent-tickets" style="margin-top: 40px;">
      <h3>Recent Ticket Activity</h3>
      <table>
        <thead>
          <tr>
            <th>Ticket ID</th>
            <th>Priority</th>
            <th>Subject</th>
            <th>Status</th>
            <th>Last Updated</th>
          </tr>
        </thead>
        <tbody>
          <?php if (count($tickets) > 0): ?>
            <?php foreach ($tickets as $ticket): ?>
              <tr>
                <td>#<?= htmlspecialchars($ticket['ticket_id']) ?></td>
                <td><?= htmlspecialchars($ticket['priority']) ?></td>
                <td><?= htmlspecialchars($ticket['subject']) ?></td>
                <td>
                  <span class="status <?= htmlspecialchars($ticket['status']) ?>">
                    <?= ucfirst($ticket['status']) ?>
                  </span>
                </td>
                <td><?= date("F j, Y", strtotime($ticket['created_at'] ?? 'now')) ?></td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="5">No recent tickets found.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</body>
</html>
