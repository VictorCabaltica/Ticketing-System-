<?php
include 'connection.php';

$search = $_GET['search'] ?? '';
$sort = $_GET['sort'] ?? 'asc';

$query = "SELECT * FROM users WHERE name LIKE ? ORDER BY name " . ($sort === 'desc' ? 'DESC' : 'ASC');
$stmt = $conn->prepare($query);
$stmt->bind_param('s', $search_param);
$search_param = "%$search%";
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>User Management</title>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background: linear-gradient(to bottom, #24243e, #0f0c29);
      margin: 0;
      padding: 0;
      color: #FFF5C5;
    }
    .container {
      max-width: 90%;
      margin: 50px auto;
      background: #3a3a5c;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 5px 20px 50px #000;
      overflow-x: auto;
    }
    h2 {
      text-align: center;
      margin-bottom: 25px;
      font-size: 28px;
    }
    .top-actions {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 20px;
      flex-wrap: wrap;
    }
    .top-actions form {
      display: flex;
      gap: 10px;
      flex-wrap: wrap;
    }
    .btn {
      padding: 8px 16px;
      margin: 2px;
      font-size: 14px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      color: white;
    }
    .btn-update { background-color: #28a745; }
    .btn-delete { background-color: #dc3545; }
    .btn-activate { background-color: #ffc107; }
    .btn-add {
      background-color: orange;
      font-size: 15px;
      padding: 10px 20px;
      font-weight: bold;
    }
    .btn-back {
      background-color: #6c757d;
      padding: 10px 20px;
      font-size: 14px;
      font-weight: bold;
    }
    .btn:hover {
      opacity: 0.85;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      background-color: white;
      color: black;
      border-radius: 8px;
      overflow: hidden;
      box-shadow: 0 0 10px rgba(0,0,0,0.2);
    }
    th, td {
      padding: 12px 15px;
      text-align: center;
      border: 1px solid #ddd;
    }
    th {
      background-color: darkviolet;
      color: #FFF5C5;
    }
    tr:nth-child(even) {
      background-color: #f8f8f8;
    }
    tr:hover {
      background-color: #e2e2e2;
    }
    .status-active {
      color: #28a745;
      font-weight: bold;
    }
    .status-suspended {
      color: #dc3545;
      font-weight: bold;
    }
    .status-pending {
      color: #ffc107;
      font-weight: bold;
    }
    input[type="text"], select {
      padding: 8px;
      border-radius: 5px;
      border: none;
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>User Management</h2>

    <div class="top-actions">
      <a href="signinpage-users.php"><button class="btn btn-add">+ Add New User</button></a>
      <form method="GET">
        <input type="text" name="search" placeholder="Search by name" value="<?= htmlspecialchars($search) ?>">
        <select name="sort">
          <option value="asc" <?= $sort === 'asc' ? 'selected' : '' ?>>Sort A-Z</option>
          <option value="desc" <?= $sort === 'desc' ? 'selected' : '' ?>>Sort Z-A</option>
        </select>
        <button class="btn btn-activate" type="submit">Apply</button>
      </form>
      <a href="admin.php"><button class="btn btn-back">← Back</button></a>
    </div>

    <table>
      <tr>
        <th>User ID</th>
        <th>Name</th>
        <th>Contact</th>
        <th>Email</th>
        <th>Status</th>
        <th>Actions</th>
      </tr>

      <?php while($row = $result->fetch_assoc()): ?>
        <tr>
          <td><?= $row['user_id'] ?></td>
          <td><?= htmlspecialchars($row['name']) ?></td>
          <td><?= htmlspecialchars($row['contact']) ?></td>
          <td><?= htmlspecialchars($row['email']) ?></td>
          <td class="<?= 'status-' . strtolower($row['status']) ?>"><?= ucfirst($row['status']) ?></td>
          <td>
            <a href="update-user.php?id=<?= $row['user_id'] ?>"><button class="btn btn-update">Update</button></a>

            <?php if($row['status'] == 'active'): ?>
              <a href="delete-user.php?id=<?= $row['user_id'] ?>" onclick="return confirm('Are you sure you want to delete this user?');"><button class="btn btn-delete">Delete</button></a>
            <?php elseif($row['status'] == 'suspended'): ?>
              <a href="suspend-user.php?id=<?= $row['user_id'] ?>&action=activate"><button class="btn btn-activate">Activate</button></a>
            <?php else: ?>
              <button class="btn btn-activate" disabled>Pending</button>
            <?php endif; ?>
          </td>
        </tr>
      <?php endwhile; ?>

    </table>
  </div>
</body>
</html>   
