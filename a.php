<?php
include 'connection.php';

// Default SQL
$sql = "SELECT * FROM tickets WHERE 1=1";

// Apply filters if set
if (!empty($_GET['start_date']) && !empty($_GET['end_date'])) {
  $start_date = $_GET['start_date'];
  $end_date = $_GET['end_date'];
  $sql .= " AND created_at BETWEEN '$start_date' AND '$end_date'";
}

if (!empty($_GET['department'])) {
  $department = $_GET['department'];
  $sql .= " AND department = '$department'";
}

if (!empty($_GET['priority'])) {
  $priority = $_GET['priority'];
  $sql .= " AND priority = '$priority'";
}

// Execute query
$result = $conn->query($sql);

$tickets = [];
if ($result->num_rows > 0) {
  while($row = $result->fetch_assoc()) {
    $tickets[] = $row;
  }
}

// Now you have an array $tickets containing the filtered results!
?>
<h2>Filtered Ticket Results</h2>
<table border="1" cellpadding="10">
  <tr>
    <th>Ticket ID</th>
    <th>Subject</th>
    <th>Department</th>
    <th>Priority</th>
    <th>Status</th>
    <th>Created At</th>
  </tr>
  <?php foreach ($tickets as $ticket): ?>
    <tr>
      <td><?= htmlspecialchars($ticket['ticket_id']) ?></td>
      <td><?= htmlspecialchars($ticket['subject']) ?></td>
      <td><?= htmlspecialchars($ticket['department']) ?></td>
      <td><?= htmlspecialchars(ucfirst($ticket['priority'])) ?></td>
      <td><?= htmlspecialchars(ucfirst($ticket['status'])) ?></td>
      <td><?= htmlspecialchars($ticket['created_at']) ?></td>
    </tr>
  <?php endforeach; ?>
</table>
