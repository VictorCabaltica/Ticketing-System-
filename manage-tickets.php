<?php
include 'connection.php';

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$sort_status = isset($_GET['status']) ? $_GET['status'] : '';

$filter_query = "SELECT * FROM tickets WHERE 1";

if (!empty($search)) {
    $safe_search = $conn->real_escape_string($search);
    $filter_query .= " AND (
        name LIKE '%$safe_search%' OR
        email LIKE '%$safe_search%' OR
        department LIKE '%$safe_search%' OR
        subject LIKE '%$safe_search%'
    )";
}

if (!empty($sort_status)) {
    $safe_status = $conn->real_escape_string($sort_status);
    $filter_query .= " AND status = '$safe_status'";
}

$filter_query .= " ORDER BY created_at DESC";
$result = $conn->query($filter_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Tickets</title>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background: linear-gradient(to bottom, #24243e, #0f0c29);
      margin: 0;
      padding: 0;
      color: #FFF5C5;
    }

    .container {
      max-width: 1200px;
      margin: 50px auto;
      background: #3a3a5c;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 5px 20px 50px #000;
    }

    h2 {
      text-align: center;
      margin-bottom: 30px;
    }

    .filter-bar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 20px;
      gap: 10px;
      flex-wrap: wrap;
    }

    .filter-bar input,
    .filter-bar select {
      padding: 10px;
      border-radius: 6px;
      border: 1px solid #ccc;
      font-size: 14px;
    }

    .filter-bar button {
      background-color: #4a3aff;
      color: white;
      border: none;
      padding: 10px 20px;
      border-radius: 6px;
      cursor: pointer;
      font-size: 14px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      background-color: white;
      color: black;
      border-radius: 10px;
      overflow: hidden;
      font-size: 14px;
    }

    th, td {
      padding: 10px 12px;
      text-align: left;
      border-bottom: 1px solid #ddd;
    }

    th {
      background-color: darkviolet;
      color: #FFF5C5;
    }

    .btn {
      padding: 6px 12px;
      font-size: 13px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      color: white;
    }

    .btn-view { background-color: #007bff; }
    .btn-update { background-color: #ffc107; color: black; }
    .btn-delete { background-color: #dc3545; }
    .btn:hover { opacity: 0.9; }

    .actions {
      display: flex;
      gap: 5px;
    }

    .scrollable {
      max-height: 500px;
      overflow-y: auto;
    }

    .closed-row.minimized td {
      background-color: #f5f5f5;
      color: gray;
    }

    .minimize-btn {
      background-color: gray;
      color: white;
      border: none;
      border-radius: 4px;
      padding: 2px 8px;
      font-size: 12px;
      margin-left: 10px;
      cursor: pointer;
    }
  </style>
  <script>
    function toggleMinimize(rowId) {
      const row = document.getElementById(rowId);
      row.classList.toggle("minimized");
    }
  </script>
</head>
<body>
  <div class="container">
    <h2>Manage Tickets</h2>

    <form method="get" class="filter-bar">
      <input type="text" name="search" placeholder="Search tickets..." value="<?= htmlspecialchars($search) ?>">
      <select name="status">
        <option value="">Filter by Status</option>
        <option value="open" <?= $sort_status == 'open' ? 'selected' : '' ?>>Open</option>
        <option value="in_progress" <?= $sort_status == 'in_progress' ? 'selected' : '' ?>>In Progress</option>
        <option value="closed" <?= $sort_status == 'closed' ? 'selected' : '' ?>>Closed</option>
      </select>
      <button type="submit">Apply</button>
    </form>

      <div style="margin-bottom: 20px;">
        <a href="admin.php" style="
          display: inline-block;
          background-color: #ff6b6b;
          color: white;
          text-decoration: none;
          padding: 12px 24px;
          border-radius: 8px;
          height: 17px;
          font-size: 15px;
          font-weight: bold;
          box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
          transition: background-color 0.3s, transform 0.3s;
        ">
          ← Back
        </a>
      </div>


    <div class="scrollable">
      <table>
        <thead>
          <tr>
            <th>Ticket ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Department</th>
            <th>Priority</th>
            <th>Subject</th>
            <th>Status</th>
            <th>Created At</th>
            <th>Actions</th>
            <th>Response Due</th>
            <th>Resolution Due</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
              <?php $row_id = "row_" . $row['ticket_id']; ?>
              <tr id="<?= $row_id ?>" class="<?= $row['status'] == 'closed' ? 'closed-row' : '' ?>">
                <td><?= $row['ticket_id'] ?></td>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><?= htmlspecialchars($row['email']) ?></td>
                <td><?= htmlspecialchars($row['department']) ?></td>
                <td><?= ucfirst($row['priority']) ?></td>
                <td><?= htmlspecialchars($row['subject']) ?></td>
                <td>
                  <?= ucfirst($row['status']) ?>
                  <?php if ($row['status'] == 'closed'): ?>
                    <button class="minimize-btn" onclick="toggleMinimize('<?= $row_id ?>')">Minimize</button>
                  <?php endif; ?>
                </td>
                <td><?= $row['created_at'] ?></td>
                <td class="actions">
                  <a href="ticket-view.php?id=<?= $row['ticket_id'] ?>"><button class="btn btn-view">View</button></a>
                  <a href="ticket-update.php?id=<?= $row['ticket_id'] ?>"><button class="btn btn-update">Update</button></a>
                  <a href="ticket-delete.php?id=<?= $row['ticket_id'] ?>" onclick="return confirm('Are you sure you want to delete this ticket?');"><button class="btn btn-delete">Delete</button></a>
                </td>

                <td>
                  <?php
                  if ($row['status'] == 'closed') {
                      echo "✔ Closed";
                  } elseif (!empty($row['responded_at'])) {
                      echo "✔ Responded at " . date('Y-m-d H:i', strtotime($row['responded_at']));
                  } else {
                      if ($row['status'] == 'in_progress') {
                          echo "⌛ Waiting for Response";
                      } elseif (!empty($row['response_due']) && strtotime($row['response_due']) !== false) {
                          echo "⏰ Response Due: " . date('Y-m-d H:i', strtotime($row['response_due']));
                          if (strtotime($row['response_due']) < time()) {
                              echo " <span style='color: red;'>[Late]</span>";
                          }
                      } else {
                          echo "⏰ Response Due: Not set";
                      }
                  }
                  ?>
                </td>

                <td>
                  <?php
                  if ($row['status'] == 'closed') {
                      echo "✔ Closed";
                  } elseif ($row['status'] == 'in_progress') {
                      if (!empty($row['resolution_due']) && strtotime($row['resolution_due']) !== false) {
                          echo "🚨 Resolution Due: " . date('Y-m-d H:i', strtotime($row['resolution_due']));
                          if (strtotime($row['resolution_due']) < time()) {
                              echo " <span style='color: red;'>[Overdue]</span>";
                          }
                      } else {
                          echo "🚨 Resolution Due: Not set";
                      }
                  } else {
                      echo "🚨 Resolution Due: Not applicable";
                  }
                  ?>
                </td>


              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr>
              <td colspan="11" style="text-align:center;">No tickets found.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</body>
</html>
