
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Agent Navbar</title>
  <style>
    body {
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
      background: #34495e;
    }

    nav {
      background: #fff;
      padding: 15px 30px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      display: flex;
      justify-content: space-between;
      align-items: center;
      background-color:  #34495e;
      color: white;
    }

    .logo {
      font-size: 20px;
      font-weight: bold;
      color: white;
    }

    .nav-links {
      display: flex;
      gap: 25px;
    }

    .nav-links a {
      text-decoration: none;
      color: white;
      font-weight: 500;
    }

    .nav-links a:hover {
      color: orange;
    }

    .profile {
      position: relative;
    }

    .profile-button {
      display: flex;
      align-items: center;
      cursor: pointer;
    }

    .profile-button img {
      width: 32px;
      height: 32px;
      border-radius: 50%;
      margin-right: 8px;
    }

    .profile-name {
      font-weight: 600;
      color: white;
    }

    .dropdown {
      display: none;
      position: absolute;
      top: 45px;
      right: 0;
      background-color: #fff;
      border: 1px solid #ddd;
      border-radius: 5px;
      width: 150px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
      flex-direction: column;
    }

    .dropdown a {
      padding: 10px;
      text-decoration: none;
      color: #333;
    }

    .dropdown a:hover {
      background-color: #f5f5f5;
    }

    .profile:hover .dropdown {
      display: flex;
    }
  </style>
</head>
<body>

<nav>
  <div class="logo">Agent Portal</div>

  <div class="nav-links">
    <a href="agent-dashboard.php">Dashboard</a>
    <a href="a-dashboard.php">Assigned Tickets</a>
    <a href="sla-agent.php">SLA Performance</a>
  </div>

  <div class="profile">
    <div class="profile-button">
      <span class="profile-name">Agent Name</span>
    </div>
    <div class="dropdown">
      <a href="logout.php" style="color: red;">Logout</a>
    </div>
  </div>
</nav>

</body>
</html>
