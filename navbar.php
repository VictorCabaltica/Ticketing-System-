<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        .navbar {
    background-color: #2c3e50;
    color: var(--white);
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px 30px;
    flex-wrap: wrap;
    margin-bottom: 30px;
}

.navbar-brand {
    font-size: 1.3rem;
    font-weight: bold;
    color: white;
}

.navbar-links {
    list-style: none;
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    color: white;
}

.navbar-links li a {
    color: var(--white);
    text-decoration: none;
    font-weight: 500;
    padding: 6px 12px;
    border-radius: 4px;
    transition: background-color 0.2s ease-in-out;
}

.navbar-links li a:hover,
.navbar-links li a.active {
    background-color: solid orange;
    color: orange;
}

.navbar-links li a.logout {
    background-color: var(--danger-color);
    padding: 6px 14px;
}

.navbar-links li a.logout:hover {
    background-color: #c0392b;
}

@media (max-width: 768px) {
    .navbar {
        flex-direction: column;
        align-items: flex-start;
    }

    .navbar-links {
        flex-direction: column;
        gap: 10px;
        width: 100%;
    }

    .navbar-links li {
        width: 100%;
    }

    .navbar-links li a {
        display: block;
        width: 100%;
    }
}

    </style>
</head>
<body>
    <!-- navbar.php -->
<nav class="navbar">
    <div class="navbar-brand">🎫 Ticketing System</div>
    <ul class="navbar-links">
        <li><a href="user-dashboard.php" class="<?= basename($_SERVER['PHP_SELF']) === 'dashboard.php' ? 'active' : '' ?>">Dashboard</a></li>
        <li><a href="submit_ticket.php" class="<?= basename($_SERVER['PHP_SELF']) === 'submit_ticket.php' ? 'active' : '' ?>">Submit Ticket</a></li>
        <li><a href="view_ticket.php" class="<?= basename($_SERVER['PHP_SELF']) === 'my_tickets.php' ? 'active' : '' ?>">My Tickets</a></li>
        <li><a href="profile.php" class="<?= basename($_SERVER['PHP_SELF']) === 'profile.php' ? 'active' : '' ?>">Profile</a></li>
        <li><a href="logout.php" class="logout">Logout</a></li>
    </ul>
</nav>

</body>
</html>