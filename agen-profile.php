<?php
// Connect to database
$conn = new mysqli('localhost', 'root', '', 'ticketing_db');
if ($conn->connect_error) {
    die('Connection Failed: ' . $conn->connect_error);
}

// Assume you pass agent_id through URL like profile.php?agent_id=1
$agent_id = isset($_GET['agent_email']) ? intval($_GET['agent_email']) : 0;
$agent = null;

// Fetch agent details only if agent_id is valid
if ($agent_id > 0) {
    $stmt = $conn->prepare("SELECT * FROM agent WHERE agent_id = ?");
    $stmt->bind_param("i", $agent_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $agent = $result->fetch_assoc(); // Fetch the agent
    $stmt->close();

    if (!$agent) {
        // If no agent found, show an error message
        echo "<script>alert('Agent not found.'); window.location.href='agent-list.php';</script>";
        exit();
    }
} else {
    // If invalid agent_id or not passed, redirect to agent list or show error
    echo "<script>alert('Invalid Agent ID.'); window.location.href='agent-list.php';</script>";
    exit();
}

// Handle form submission for updating agent
if (isset($_POST['save'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $assigned_ticket = $_POST['assigned_ticket'];
    $status = $_POST['status'];

    $stmt = $conn->prepare("UPDATE agent SET name=?, email=?, assigned_ticket=?, status=? WHERE agent_id=?");
    $stmt->bind_param("ssssi", $name, $email, $assigned_ticket, $status, $agent_id);
    if ($stmt->execute()) {
        echo "<script>alert('Profile updated successfully!'); window.location.href='?agent_id=$agent_id';</script>";
    } else {
        echo "<script>alert('Update failed.');</script>";
    }
    $stmt->close();
}

// Refresh agent data after update
if ($agent_id > 0) {
    $stmt = $conn->prepare("SELECT * FROM agent WHERE agent_id = ?");
    $stmt->bind_param("i", $agent_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $agent = $result->fetch_assoc();
    $stmt->close();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Agent Profile</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: #f1f5f9;
            margin: 0;
            padding: 20px;
        }
        .profile-container {
            background: white;
            padding: 30px;
            border-radius: 12px;
            max-width: 600px;
            margin: 0 auto;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        .profile-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .profile-header h2 {
            margin-bottom: 5px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            font-weight: bold;
            display: block;
            margin-bottom: 6px;
        }
        input[type="text"],
        input[type="email"],
        select {
            width: 100%;
            padding: 10px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 16px;
            background-color: #f9fafb;
        }
        .btns {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 30px;
        }
        button {
            padding: 10px 20px;
            background-color: #2563eb;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            transition: 0.3s;
        }
        button:hover {
            background-color: #1d4ed8;
        }
        .save-btn {
            display: none;
            background-color: #10b981;
        }
        .save-btn:hover {
            background-color: #059669;
        }
        .readonly input, .readonly select {
            background-color: #f1f5f9;
            pointer-events: none;
        }
    </style>
</head>
<body>

<div class="profile-container">
    <div class="profile-header">
        <h2>Agent Profile</h2>
        
    </div>

    <form method="post" id="profileForm">
    <div class="form-group">
    <label>Name</label>
    <input type="text" name="name" value="<?php echo htmlspecialchars($agent['name']); ?>" readonly>
</div>

<div class="form-group">
    <label>Email</label>
    <!-- Fix the field name here to match the correct column in your database -->
    <input type="email" name="email" value="<?php echo htmlspecialchars($agent['email']); ?>" readonly>
</div>

<div class="form-group">
    <label>Assigned Ticket</label>
    <input type="text" name="assigned_ticket" value="<?php echo htmlspecialchars($agent['assigned_ticket']); ?>" readonly>
</div>

<div class="form-group">
    <label>Status</label>
    <select name="status" disabled>
        <option value="active" <?php echo $agent['status'] == 'active' ? 'selected' : ''; ?>>Active</option>
        <option value="suspended" <?php echo $agent['status'] == 'suspended' ? 'selected' : ''; ?>>Suspended</option>
    </select>
</div>


<script>
function enableEdit() {
    const form = document.getElementById('profileForm');
    const inputs = form.querySelectorAll('input, select');
    inputs.forEach(input => {
        input.removeAttribute('readonly');
        input.removeAttribute('disabled');
    });
    document.getElementById('editBtn').style.display = 'none';
    document.getElementById('saveBtn').style.display = 'inline-block';
}
</script>

</body>
</html>
