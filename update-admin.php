<?php
include 'connection.php';

if (isset($_GET['id'])) {
    $user_id = $_GET['id'];

    // Fetch user data
    $stmt = $conn->prepare("SELECT * FROM admin WHERE admin_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if (!$user) {
        echo "<script>alert('Admin not found.'); window.location.href = 'admin-management.php';</script>";
        exit();
    }

    // Handle form submission
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $status = $_POST['status'];

        $update_stmt = $conn->prepare("UPDATE admin SET name = ?, email = ?, status = ? WHERE admin_id = ?");
        $update_stmt->bind_param("sssi", $name, $email, $status, $user_id);

        if ($update_stmt->execute()) {
            echo "<script>alert('Admin updated successfully.'); window.location.href = 'admin-management.php';</script>";
        } else {
            echo "<script>alert('Failed to update admin.');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Update Admin</title>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background: linear-gradient(to bottom, #24243e, #0f0c29);
      margin: 0;
      padding: 0;
      color: #FFF5C5;
    }

    .container {
      width: 400px;
      margin: 60px auto;
      background: #3a3a5c;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 5px 20px 50px #000;
    }

    h2 {
      text-align: center;
      margin-bottom: 25px;
      color: #FFF5C5;
    }

    label {
      display: block;
      margin-top: 15px;
      margin-bottom: 5px;
      color: #FFF5C5;
      font-weight: bold;
    }

    input, select {
      width: 100%;
      padding: 10px;
      border-radius: 6px;
      border: none;
      outline: none;
      font-size: 16px;
    }

    button {
      margin-top: 25px;
      width: 100%;
      padding: 12px;
      border: none;
      border-radius: 6px;
      background-color: orange;
      font-size: 16px;
      color: white;
      font-weight: bold;
      cursor: pointer;
    }

    button:hover {
      background-color: #ff6b00;
    }

    .back-link {
      display: block;
      text-align: center;
      margin-top: 20px;
      text-decoration: none;
      color: #FFF5C5;
    }

    .back-link:hover {
      color: white;
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>Update Admin</h2>
    <form method="POST">
      <label for="name">Name:</label>
      <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>

      <label for="email">Email:</label>
      <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>

      <label for="status">Status:</label>
      <select name="status" required>
        <option value="active" <?= $user['status'] == 'active' ? 'selected' : '' ?>>Active</option>
        <option value="suspended" <?= $user['status'] == 'suspended' ? 'selected' : '' ?>>suspended</option>
      </select>

      <button type="submit">Save Changes</button>
    </form>
    <a class="back-link" href="admin-management.php">← Back to User Management</a>
  </div>
</body>
</html>
