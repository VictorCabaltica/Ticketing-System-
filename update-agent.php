<?php
include 'connection.php';
$agent_id = $_GET['id'];
$result = $conn->query("SELECT * FROM agent WHERE agent_id = '$agent_id'");
$agent = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Update Agent</title>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background: linear-gradient(to bottom, #24243e, #0f0c29);
      margin: 0;
      padding: 0;
      color: #FFF5C5;
    }

    .container {
      max-width: 600px;
      margin: 50px auto;
      background: #3a3a5c;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 5px 20px 50px #000;
    }

    h2 {
      text-align: center;
      margin-bottom: 30px;
      font-size: 28px;
    }

    form {
      display: flex;
      flex-direction: column;
    }

    label {
      margin: 10px 0 5px;
      font-size: 16px;
      color: #FFF5C5;
    }

    input, select {
      padding: 10px;
      margin-bottom: 20px;
      border-radius: 5px;
      border: 1px solid #ddd;
      font-size: 16px;
    }

    input[type="file"] {
      padding: 5px;
    }

    input:focus, select:focus {
      border-color: #28a745;
      outline: none;
    }

    .form-actions {
      display: flex;
      justify-content: space-between;
    }

    .btn {
      padding: 10px 20px;
      font-size: 16px;
      color: white;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      text-decoration: none;
    }

    .btn-update {
      background-color: #28a745;
    }

    .btn-cancel {
      background-color: #dc3545;
    }

    .btn:hover {
      opacity: 0.85;
    }

    select {
      background-color: #2f2f3d;
      color: #FFF5C5;
      border: 1px solid #ddd;
    }

    input[type="text"], input[type="email"] {
      background-color: #2f2f3d;
      color: #FFF5C5;
    }

    input[type="text"]:focus, input[type="email"]:focus {
      border-color: #28a745;
    }

    input[type="file"]:focus {
      border-color: #28a745;
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>Update Agent Information</h2>
    <form action="update-agent-process.php" method="POST" enctype="multipart/form-data">
      <input type="hidden" name="agent_id" value="<?= $agent['agent_id'] ?>">

      <label for="name">Name</label>
      <input type="text" id="name" name="name" value="<?= $agent['name'] ?>" required>

      <label for="email">Email</label>
      <input type="email" id="email" name="email" value="<?= $agent['email'] ?>" required>

      <label for="status">Status</label>
      <select name="status" id="status" required>
        <option value="active" <?= ($agent['status'] == 'active') ? 'selected' : '' ?>>Active</option>
        <option value="suspended" <?= ($agent['status'] == 'suspended') ? 'selected' : '' ?>>Suspended</option>
        <option value="pending" <?= ($agent['status'] == 'pending') ? 'selected' : '' ?>>Pending</option>
      </select>

      <div class="form-actions">
        <button type="submit" class="btn btn-update">Update Agent</button>
        <a href="agent-management.php" class="btn btn-cancel">Cancel</a>
      </div>
    </form>
  </div>
</body>
</html>
