<?php
include 'connection.php';
$sql1 = "SELECT COUNT(*) FROM tickets WHERE responded_at IS NULL AND NOW() > response_due";
$sql2 = "SELECT COUNT(*) FROM tickets WHERE status != 'closed' AND NOW() > resolution_due";

$result1 = mysqli_fetch_row(mysqli_query($conn, $sql1));
$result2 = mysqli_fetch_row(mysqli_query($conn, $sql2));

$late_responses = $result1[0];
$late_resolutions = $result2[0];
?>

<div class="card">
  <h3>Late Responses</h3>
  <p><?= $late_responses ?> ticket(s) missed response deadline</p>
</div>
<div class="card">
  <h3>Late Resolutions</h3>
  <p><?= $late_resolutions ?> ticket(s) missed resolution deadline</p>
</div>
