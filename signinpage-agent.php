<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Assign Agent</title>
  <style>
    body {
      margin: 0;
      padding: 0;
      align-items: center;
      justify-content: center;
      display: flex;
      min-height: 100vh;
      background: linear-gradient(to bottom, #24243e, #0f0c29);
    }

    .main {
      width: 350px;
      height: 500px;
      background: #3a3a5c;
      overflow: hidden;
      border-radius: 10px;
      box-shadow: 5px 20px 50px #000;
      position: relative;
    }

    .signup {
      position: absolute;
      width: 100%;
      height: 100%;
      top: 0;
      transition: transform 0.6s ease-in-out;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
    }

    label {
      color: #FFF5C5;
      font-size: 2.3em;
      display: flex;
      margin: 20px;
      font-weight: bold;
      cursor: pointer;
      transition: .5s ease-in-out;
    }
    label.label {
      font-size: 16px;
    }

    input {
      width: 100%;
      height: 20px;
      background-color: white;
      justify-content: center;
      display: flex;
      margin: 20px auto;
      outline: none;
      border: none;
      padding: 10px;
      border-radius: 5px;
    }

    button {
      width: 60%;
      height: 40px;
      margin: 10px auto;
      justify-content: center;
      display: block;
      color: #FFF5C5;
      background: orange;
      font-size: 1em;
      font-weight: bold;
      margin-top: 20px;
      outline: none;
      border: none;
      border-radius: 5px;
      transition: .2s ease-in;
      cursor: pointer;
    }

    button:hover {
      background: #ff6b00;
    }

    a {
      color: #FFF5C5;
      margin-left: 105px;
    }

    a:hover {
      color: white;
    }
  </style>
</head>

<body>
<div class="main">
  <div class="signup">
    <form action="signinpage-agent.php" method="post">
      <label for="chk" aria-hidden="true">Assign Agent</label>
      <label class="label">Enter Name:</label>
      <input type="text" name="txt" placeholder="Name" required autocomplete="off">
      <label class="label">Enter Email Address:</label>
      <input type="email" name="email" placeholder="Email" required autocomplete="off">
      <label class="label">Enter Password:</label>
      <input type="password" name="pswd" placeholder="Password (Min 6 characters)" required autocomplete="off">
      <button>Assign</button>
      <a href="registration-agent.php">Log In</a>
    </form>
  </div>
</div>

<?php
include 'connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['txt'];
    $email = $_POST['email'];
    $password = $_POST['pswd'];

    // Validate password length
    if (strlen($password) < 6) {
        echo "<script>alert('Password must be at least 6 characters long!'); window.location.href = 'signinpage-agent.php';</script>";
        exit();
    }

    // Check if email already exists
    $check_email = $conn->prepare("SELECT * FROM agent WHERE email = ?");
    $check_email->bind_param("s", $email);
    $check_email->execute();
    $result = $check_email->get_result();

    if ($result->num_rows > 0) {
        echo "<script>alert('Email already used! Please use another email.'); window.location.href = 'signinpage-agent.php';</script>";
    } else {
        // Insert into 'agent' table
        $stmt = $conn->prepare("INSERT INTO agent (name, email, assigned_ticket) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $email, $password);

        if ($stmt->execute()) {
            echo "<script>alert('Agent assigned successfully!'); window.location.href = 'index.html';</script>";
        } else {
            echo "<script>alert('Error: Could not assign agent.'); window.location.href = 'signinpage-agent.php';</script>";
        }

        $stmt->close();
    }

    $check_email->close();
    $conn->close();
}
?>
</body>
</html>
