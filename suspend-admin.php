<?php
include 'connection.php';

if (isset($_GET['id']) && isset($_GET['action'])) {
    $id = $_GET['id'];
    $action = $_GET['action'];

    if ($action == 'suspend') {
        $status = 'suspended';
        $message = 'Admin suspended successfully!';
    } elseif ($action == 'activate') {
        $status = 'active';
        $message = 'Admin activated successfully!';
    } else {
        die("Invalid action.");
    }

    $stmt = $conn->prepare("UPDATE admin SET status = ? WHERE admin_id = ?");
    $stmt->bind_param("si", $status, $id);

    if ($stmt->execute()) {
        $success = true;
    } else {
        $success = false;
        $message = 'Something went wrong!';
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Status Update</title>
  <style>
    body {
      background: linear-gradient(to bottom, #24243e, #0f0c29);
      font-family: 'Segoe UI', sans-serif;
      color: #FFF5C5;
      margin: 0;
      padding: 0;
      display: flex;
      height: 100vh;
      align-items: center;
      justify-content: center;
    }

    .box {
      background: #3a3a5c;
      padding: 40px;
      border-radius: 12px;
      box-shadow: 5px 20px 50px #000;
      text-align: center;
      width: 350px;
    }

    .box h2 {
      margin-bottom: 20px;
      font-size: 24px;
    }

    .success {
      color: #28a745;
      font-size: 18px;
      font-weight: bold;
    }

    .error {
      color: #dc3545;
      font-size: 18px;
      font-weight: bold;
    }

    .btn-back {
      margin-top: 25px;
      padding: 10px 20px;
      background-color: orange;
      color: #FFF5C5;
      border: none;
      border-radius: 6px;
      font-size: 14px;
      cursor: pointer;
    }

    .btn-back:hover {
      background-color: #ff6b00;
    }
  </style>
</head>
<body>

  <div class="box">
    <h2>User Status Update</h2>
    <?php if (isset($success) && $success): ?>
      <div class="success"><?= $message ?></div>
    <?php else: ?>
      <div class="error"><?= $message ?></div>
    <?php endif; ?>
    <a href="admin-management.php"><button class="btn-back">← Back to User Management</button></a>
  </div>

</body>
</html>
