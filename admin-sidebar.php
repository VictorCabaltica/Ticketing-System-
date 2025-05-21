<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Arial', sans-serif;
            background-color: #f4f7fa;
            display: flex;
        }

        .sidebar {
            width: 250px;
            height: 100vh;
            background-color: #0f0c29;
            color: white;
            padding: 30px 20px;
            position: fixed;
            top: 0;
            left: 0;
            transition: 0.3s;
        }

        .sidebar h2 {
            text-align: center;
            color: #fff;
            margin-bottom: 40px;
        }

        .sidebar a {
            display: block;
            color: white;
            text-decoration: none;
            padding: 15px;
            font-size: 1.1em;
            margin-bottom: 15px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .sidebar a:hover {
            background-color: #34495e;
        }

        .sidebar a.active {
            background-color: #6321D7;
        }

        /* Content Area */
        .content {
            margin-left: 250px;
            padding: 20px;
            flex: 1;
        }

        .content h1 {
            color: #34495e;
        }

        /* Dropdown Menu */
        .report-dropdown {
            margin-bottom: 15px;
        }

        .dropdown-content {
            display: none;
            margin-left: 15px;
            background-color: #34495e;
            padding-left: 10px;
            border-radius: 5px;
        }

        .report-dropdown a {
            background-color: #34495e;
        }

        .report-dropdown a:hover {
            background-color: #5D3F72;
        }

        .report-dropdown:hover .dropdown-content {
            display: block;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .sidebar {
                width: 200px;
            }

            .content {
                margin-left: 200px;
            }
        }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <h2>Admin Dashboard</h2>
        <a href="admin.php" class="active">Dashboard</a>
        <a href="manage-tickets.php">Manage Tickets</a>
        <div class="report-dropdown">
            <a href="view-reports.php">View Reports ▾</a>
            <div class="dropdown-content">
                <a href="ticket-summary.php">Ticket Summary</a>
                <a href="sla-performance.php">SLA Performance</a>
            </div>
        </div>
        <a href="asign-agent.php">Assign Agents</a>
        <a href="user-management.php">User Management</a>
        <a href="admin-management.php">Admin Management</a>
        <a href="agent-management.php">Agent Management </a>
        <a href="settings.html">Settings</a>
    </div>

    <!-- Content Area -->
    <div class="content">
        <h1>Welcome to the Admin Dashboard</h1>
        <p>Here you can manage tickets, users, and agents.</p>

        <!-- Sample Form (could be replaced with your actual forms) -->
        <form>
            <label for="ticket">Ticket ID:</label>
            <input type="text" id="ticket" name="ticket" placeholder="Enter Ticket ID"><br><br>
            <label for="status">Status:</label>
            <select id="status" name="status">
                <option value="open">Open</option>
                <option value="in_progress">In Progress</option>
                <option value="closed">Closed</option>
            </select><br><br>
            <button type="submit">Submit</button>
        </form>
    </div>

</body>
</html>
