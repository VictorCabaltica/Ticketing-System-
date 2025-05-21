<?php
include("connection.php"); // your database connection file

if (!isset($_GET['id'])) {
  echo "Ticket ID is missing.";
  exit;
}

$ticket_id = $_GET['id'];

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
?>

<!-- Now your HTML design with $row will work -->

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>View Ticket</title>
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

    .ticket-info p {
      background-color: #ffffff10;
      padding: 10px;
      border-radius: 8px;
      margin-bottom: 15px;
      color: #fff;
    }

    .back-btn {
      display: block;
      background-color: #ff6b6b;
      border: none;
      padding: 10px 20px;
      color: white;
      border-radius: 6px;
      text-align: center;
      cursor: pointer;
      margin-top: 20px;
    }
  </style>
</head>
<body>
  <div class="card">
    <h2>Ticket Details</h2>
    <div class="ticket-info">
      <label>Name:</label>
      <p><?= $row['name'] ?></p>

      <label>Email:</label>
      <p><?= $row['email'] ?></p>

      <label>Department:</label>
      <p><?= $row['department'] ?></p>

      <label>Priority:</label>
      <p><?= ucfirst($row['priority']) ?></p>

      <label>Subject:</label>
      <p><?= $row['subject'] ?></p>

      <label>Status:</label>
      <p><?= ucfirst($row['status']) ?></p>

      <label>Created At:</label>
      <p><?= $row['created_at'] ?></p>
    </div>
    <a href="manage-tickets.php" class="back-btn">← Back</a>
  </div>
</body>
</html>
