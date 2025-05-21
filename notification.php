<?php
include("connection.php");

// Count all unread tickets
$count_query = "SELECT COUNT(*) as unread_count FROM tickets WHERE is_read = 0";
$count_result = $conn->query($count_query);
$count_row = $count_result->fetch_assoc();
$unread_count = $count_row['unread_count'];

// Get the latest 5 unread tickets
$notif_query = "SELECT * FROM tickets WHERE is_read = 0 ORDER BY created_at DESC LIMIT 5";
$notif_result = $conn->query($notif_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard</title>
  <style>
    body {
      margin: 0;
      font-family: Arial, sans-serif;
      background-color: #f4f4f4;
    }
    .navbar {
      background-color:  #0f0c29;
      padding: 15px;
      color: white;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    .notif-container {
      position: relative;
      cursor: pointer;
    }
    .notif-bell {
      font-size: 24px;
    }
    .notif-count {
      position: absolute;
      top: -5px;
      right: -10px;
      background: red;
      color: white;
      border-radius: 50%;
      padding: 2px 6px;
      font-size: 12px;
      font-weight: bold;
    }
    .notif-dropdown {
      display: none;
      position: absolute;
      right: 0;
      top: 45px;
      background-color: white;
      color: black;
      width: 300px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.3);
      border-radius: 5px;
      overflow: hidden;
      z-index: 999;
    }
    .notif-dropdown.active {
      display: block;
    }
    .notif-item {
      padding: 10px;
      border-bottom: 1px solid #ccc;
    }
    .notif-item:last-child {
      border-bottom: none;
    }
    .logout-btn {
      background: #ff4d4d;
      color: white;
      border: none;
      padding: 10px 15px;
      border-radius: 5px;
      cursor: pointer;
    }
    .main-content {
      padding: 30px;
    }
  </style>
</head>
<body>

<div class="navbar">
  <h1>Admin Dashboard</h1>

  <div style="display: flex; align-items: center; gap: 25px;">
    <div class="notif-container" onclick="toggleDropdown()">
      <span class="notif-bell">🔔</span>
      <?php if ($unread_count > 0): ?>
        <span class="notif-count" id="notifBadge"><?= $unread_count ?></span>
      <?php endif; ?>
      <div class="notif-dropdown" id="notifDropdown">
        <?php if ($notif_result->num_rows > 0): ?>
          <?php while ($notif = $notif_result->fetch_assoc()): ?>
            <div class="notif-item">
                <p> Notification: New Tickets has been added</p><br>
              <strong>Subject:<?= htmlspecialchars($notif['subject']) ?></strong><br>
              <?= htmlspecialchars($notif['priority']) ?>
            </div>
          <?php endwhile; ?>
        <?php else: ?>
          <div class="notif-item">No new notifications</div>
        <?php endif; ?>
      </div>
    </div>

    <form method="post" action="logout.php">
      <button type="submit" class="logout-btn">Logout</button>
    </form>
  </div>
</div>
<script>
  function toggleDropdown() {
    const dropdown = document.getElementById("notifDropdown");
    const badge = document.getElementById("notifBadge");

    dropdown.classList.toggle("active");

    if (dropdown.classList.contains("active")) {
      fetch('mark_tickets_read.php')
        .then(response => response.text())
        .then(data => {
          if (badge) {
            badge.style.display = 'none';
          }
        });
    }
  }
</script>

</body>
</html>
