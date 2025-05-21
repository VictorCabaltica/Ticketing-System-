<?php
include 'notification.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Ticketing System</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Arial', sans-serif;
            background-color: #f4f7fa;
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

        .content {
            margin-left: 270px;
            padding: 40px;
            background-color: #ecf0f1;
            height: 100vh;
        }

        .content h1 {
            font-size: 2.5em;
            color: #2c3e50;
            margin-bottom: 20px;
        }

        .content p {
            font-size: 1.2em;
            color: #34495e;
            line-height: 1.6;
        }

        .card-container {
            display: flex;
            justify-content: space-around;
            margin-top: 40px;
            flex-wrap: wrap;
            
        }

        .card {
            width: 280px;
            background-color: #34495e;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            text-align: center;
        }

        .card h3 {
            color: white;
            margin-bottom: 20px;
        }

        .card a {
            display: block;
            background-color:orange;
            color: white;
            padding: 10px;
            border-radius: 5px;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }

        .card a:hover {
            background-color:orangered;
        }
        .card p{
            color: white;
        }
    .report-dropdown {
        position: relative;
    }

    .dropdown-content {
        display: none;
        flex-direction: column;
        margin-left: 15px;
        margin-top: 5px;
    }

    .dropdown-content a {
        background-color: #1f1c39;
        padding: 10px;
        border-radius: 5px;
        font-size: 0.95em;
    }

    .dropdown-content a:hover {
        background-color: #34495e;
    }

    .report-dropdown:hover .dropdown-content {
        display: flex;
    }
</style>


    </style>
</head>
<body>

    <div class="sidebar">
        <h2>Admin Dashboard</h2>
        <a href="admin.php" class="active">Dashboard</a>
        <a href="manage-tickets.php">Manage Tickets</a>
        <div class="report-dropdown">
        <a href="view-reports.php">View Reports ▾</a>
        <div class="dropdown-content">
            <a href="ticket-summary.php">Ticket Summary</a>
            <a href="sla.php">SLA Performance</a>
        </div>
    </div>
        <a href="asign-agent.php">Assign Agents</a>
        <a href="user-management.php">User Management</a>
        <a href="admin-management.php">Admin Management</a>
        <a href="agent-management.php">Agent Management </a>
    </div>

    <div class="content">
        <h1>Welcome Back, Admin</h1>
        <p>Here you can manage and monitor the system with ease. Use the quick links below to get started with ticket management, user assignment, and other essential admin tasks.</p>

        <div class="card-container">
            <div class="card">
                <h3>Manage Tickets</h3>
                <p>View and manage all tickets submitted by users.</p>
                <a href="manage-tickets.php">Go to Tickets</a>
            </div>

            <div class="card">
                <h3>Assign Agents</h3>
                <p>Assign agents to resolve tickets and monitor their progress.</p>
                <a href="asign-agent.php">Assign Agents</a>
            </div>

            <div class="card">
                <h3>User Management</h3>
                <p>Manage users, add new users, or suspend accounts.</p>
                <a href="user-management.php">Manage Users</a>
            </div>

            <div class="card">
                <h3>Admin Manegement</h3>
                <p>Manage admin, add new admin, or suspend accounts.</p>
                <a href="admin-management.php">Manage Users</a>
            </div>
            <div class="card">
                <h3>Agent Management</h3>
                <p>Manage agents, add new agents, or suspend accounts.</p>
                <a href="agent-management.php">Manage Users</a>
            </div>
        </div>
    </div>

</body>
</html>
