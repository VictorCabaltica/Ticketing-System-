<?php
// profile.php
session_start();

// Fetch user details only if the session email exists
if (isset($_SESSION['user_email'])) {  // Check if the user is logged in
    include 'connection.php';

    $email = $_SESSION['user_email'];  // Use user_email

    // Fetch user details from the database
    $stmt = $conn->prepare("SELECT name, email, contact, home FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($name, $email, $contact, $home);
    $stmt->fetch();
    $stmt->close();
    $conn->close();
} else {
    echo "Please log in to view your profile.";
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>User Profile</title>
  <style>
    body {
      margin: 0;
      padding: 0;
      background-color: #f4f7fb;
      font-family: 'Segoe UI', sans-serif;
    }

    .profile-container {
      width: 100%;
      max-width: 600px;
      margin: 30px auto;
      background-color: #fff;
      border-radius: 8px;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
      padding: 40px 30px;
      text-align: center;
    }

    h2 {
      text-align: center;
      margin-bottom: 40px;
      color: #333;
      font-size: 26px;
      font-weight: 600;
    }

    .profile-info {
      margin-bottom: 20px;
      text-align: left;
    }

    .profile-info label {
      font-size: 15px;
      font-weight: 600;
      color: #444;
      display: block;
      margin-bottom: 5px;
    }

    .profile-info p {
      font-size: 16px;
      color: #555;
      margin: 5px 0 15px;
      padding: 10px;
      background-color: #f9f9f9;
      border-radius: 6px;
      border: 1px solid #e0e0e0;
    }

    .btn, .back-link {
      display: block;
      padding: 14px 0;
      width: 100%;
      border-radius: 6px;
      background-color: #3498db;
      font-size: 18px;
      color: white;
      font-weight: bold;
      margin-top: 25px;
      text-decoration: none;
      text-align: center;
      transition: background-color 0.3s ease, transform 0.3s ease;
    }

    .btn:hover, .back-link:hover {
      background-color: #2980b9;
      transform: translateY(-2px);
    }

    .back-link {
      background-color: #888;
    }

    .back-link:hover {
      background-color: #666;
    }

    .profile-info p {
      font-size: 16px;
      font-weight: 500;
    }

    .header-container {
      text-align: center;
      margin-bottom: 30px;
    }

    .header-container img {
      border-radius: 50%;
      width: 100px;
      height: 100px;
      margin-bottom: 15px;
    }

    .header-container h3 {
      font-size: 22px;
      color: #333;
      margin: 0;
    }
    .message {
  text-align: center;
  color: green;
  font-weight: bold;
  margin-top: -10px;
  margin-bottom: 20px;
}

  </style>
</head>
<body>
<?php include 'navbar.php'; ?>

  <div class="profile-container">
    <div class="header-container">
      <img src="https://via.placeholder.com/100" alt="User Avatar"> <!-- Placeholder for profile picture -->
      <h3>Welcome, <?= htmlspecialchars($name) ?></h3>
    </div>

    <h2>User Profile</h2>
    <?php if (isset($_GET['updated']) && $_GET['updated'] === 'true'): ?>
  <p class="message" id="successMessage">Your profile has been updated successfully!</p>
  <script>
    setTimeout(() => {
      const msg = document.getElementById('successMessage');
      if (msg) msg.style.display = 'none';
    }, 3000);
  </script>
<?php endif; ?>

    <div class="profile-info">
      <label for="name">Name:</label>
      <p id="name"><?= htmlspecialchars($name) ?></p>
    </div>

    <div class="profile-info">
      <label for="email">Email Address:</label>
      <p id="email"><?= htmlspecialchars($email) ?></p>
    </div>

    <div class="profile-info">
      <label for="contact">Contact Number:</label>
      <p id="contact"><?= htmlspecialchars($contact) ?></p>
    </div>

    <div class="profile-info">
      <label for="home">Home Address:</label>
      <p id="home"><?= htmlspecialchars($home) ?></p>
    </div>

    <a href="edi-profile.php" class="btn">Edit Profile</a>

    <a href="logout.php" class="back-link">Logout</a>
  </div>

</body>
</html>
