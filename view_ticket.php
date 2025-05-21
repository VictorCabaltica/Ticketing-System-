<?php
session_start();

if (!isset($_SESSION['user_email'])) {
    echo "<script>alert('Access denied. Please log in first.'); window.location.href = 'registration-users.php';</script>";
    exit();
}

$userEmail = $_SESSION['user_email'];

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

// Handle user's reply submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ticket_id']) && isset($_POST['response'])) {
    $ticket_id = $_POST['ticket_id'];
    $response = $_POST['response'];

    $insertReplyStmt = $pdo->prepare("INSERT INTO replies (ticket_id, sender_email, reply, sender_role) VALUES (?, ?, ?, 'user')");
    $insertReplyStmt->execute([$ticket_id, $userEmail, $response]);

    $updateTicketStmt = $pdo->prepare("UPDATE tickets SET status = 'open' WHERE ticket_id = ?");
    $updateTicketStmt->execute([$ticket_id]);

    header("Location: view_ticket.php");
    exit();
}

// Fetch all tickets submitted by the user
$stmt = $pdo->prepare("SELECT * FROM tickets WHERE email = ? ORDER BY created_at DESC");
$stmt->execute([$userEmail]);
$tickets = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Tickets - Ticketing System</title>
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
    .response {
      background-color: #f9f9f9;
      border: 1px solid #ddd;
      padding: 15px;
      margin-top: 20px;
      border-radius: 5px;
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
    hr {
      margin: 30px 0;
      border: none;
      border-top: 1px solid #ccc;
    }
    .toggle-btn {
      margin-top: 10px;
      background-color: orange ;
      color: white;
      padding: 8px 14px;
      border: none;
      border-radius: 4px;
      cursor: pointer;
    }
    .toggle-btn:hover {
      background-color: darkorange;
    }
    .hidden {
      display: none;
    }
  </style>
  <script>
    function toggleReplies(id) {
      const element = document.getElementById('replies-' + id);
      const button = document.getElementById('toggle-btn-' + id);
      if (element.classList.contains('hidden')) {
        element.classList.remove('hidden');
        button.textContent = 'Hide Replies';
      } else {
        element.classList.add('hidden');
        button.textContent = 'Show Replies';
      }
    }
  </script>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="container">
  <h2>My Submitted Tickets</h2>

  <?php if (count($tickets) > 0): ?>
    <?php foreach ($tickets as $ticket): ?>
      <div class="ticket-details">
        <h3>Ticket #<?= htmlspecialchars($ticket['ticket_id']) ?> - <?= htmlspecialchars($ticket['subject']) ?></h3>
        <p><strong>Department:</strong> <?= htmlspecialchars($ticket['department']) ?></p>
        <p><strong>Priority:</strong> <?= ucfirst(htmlspecialchars($ticket['priority'])) ?></p>
        <p><strong>Status:</strong>
          <span class="status <?= str_replace('-', '_', htmlspecialchars($ticket['status'])) ?>">
            <?= ucfirst(htmlspecialchars($ticket['status'])) ?>
          </span>
        </p>
        <p><strong>Description:</strong></p>
        <p><?= nl2br(htmlspecialchars($ticket['description'])) ?></p>
        <p><strong>Attachment:</strong>
          <?php if ($ticket['attachment']): ?>
            <a href="uploads/<?= htmlspecialchars($ticket['attachment']) ?>" target="_blank">View</a>
          <?php else: ?>
            None
          <?php endif; ?>
        </p>
        <p><strong>Date Submitted:</strong> <?= date("F j, Y g:i A", strtotime($ticket['created_at'])) ?></p>
      </div>

      <?php
        $repliesStmt = $pdo->prepare("SELECT * FROM replies WHERE ticket_id = ? ORDER BY reply_date ASC");
        $repliesStmt->execute([$ticket['ticket_id']]);
        $replies = $repliesStmt->fetchAll();
      ?>

      <?php if (count($replies) > 0): ?>
        <?php if ($ticket['status'] === 'closed'): ?>
          <button class="toggle-btn" id="toggle-btn-<?= $ticket['ticket_id'] ?>" onclick="toggleReplies(<?= $ticket['ticket_id'] ?>)">Show Replies</button>
          <div class="response hidden" id="replies-<?= $ticket['ticket_id'] ?>">
        <?php else: ?>
          <div class="response" id="replies-<?= $ticket['ticket_id'] ?>">
        <?php endif; ?>
          <h4>Replies</h4>
          <?php foreach ($replies as $reply): ?>
            <p><strong><?= $reply['sender_role'] === 'user' ? 'You' : 'Agent' ?>:</strong> <?= nl2br(htmlspecialchars($reply['reply'])) ?></p>
            <small><em>On <?= date("F j, Y g:i A", strtotime($reply['reply_date'])) ?></em></small>
            <hr>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <?php if ($ticket['status'] !== 'closed'): ?>
        <form method="post" class="reply-form">
          <input type="hidden" name="ticket_id" value="<?= htmlspecialchars($ticket['ticket_id']) ?>">
          <textarea name="response" placeholder="Write your reply here..." required></textarea>
          <button type="submit">Send Reply</button>
        </form>
      <?php endif; ?>
      <hr>
    <?php endforeach; ?>
  <?php else: ?>
    <p>You haven't submitted any tickets yet.</p>
  <?php endif; ?>

  <a href="user-dashboard.php" class="back-btn">Back to Dashboard</a>
</div>

</body>
</html>
