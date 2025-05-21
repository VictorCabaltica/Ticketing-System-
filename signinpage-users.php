<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign up</title>
    <style>
    body {
        margin: 0;
        padding: 0;
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
        background: linear-gradient(to bottom, #24243e, #0f0c29);
        font-family: 'Segoe UI', sans-serif;
    }

    .main {
        width: 400px;
        background: #3a3a5c;
        border-radius: 12px;
        padding: 40px 30px;
        box-shadow: 5px 20px 50px #000;
        color: #FFF5C5;
        text-align: center;
    }

    .signup {
        width: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    label[for="chk"] {
        font-size: 26px;
        font-weight: bold;
        margin-bottom: 20px;
    }

    .label {
        font-size: 14px;
        margin-top: 15px;
        margin-bottom: 5px;
        display: block;
        width: 100%;
  
    }

    input {
        width: 100%;
        padding: 10px;
        border-radius: 6px;
        border: none;
        outline: none;
        font-size: 14px;
        background-color: #ffffff;
    }

    button {
        width: 100%;
        padding: 12px;
        margin-top: 25px;
        background: orange;
        border: none;
        border-radius: 6px;
        font-size: 16px;
        font-weight: bold;
        color: #FFF5C5;
        cursor: pointer;
        transition: background 0.3s ease;
    }

    button:hover {
        background: #ff6b00;
    }

    a {
        color: #FFF5C5;
        font-size: 14px;
        text-decoration: none;
        margin-top: 15px;
        display: inline-block;
        text-align: center;
    }

    a:hover {
        color: white;
    }
</style>


</head>
<body>
<div class="main">
    <div class="signup">
        <form action="signinpage-users.php" method="post">
            <label for="chk" aria-hidden="true">Sign up User</label>
            <label class="label">Enter Name:</label>
            <input type="text" name="txt" placeholder="Name" required autocomplete="off"> 
            <label class="label">Enter Contact Number:</label>
            <input type="text" name="contact" placeholder="Contact" required autocomplete="off">
            <label class="label">Enter Home address:</label>
            <input type="text" name="home" placeholder="Home" required autocomplete="off">
            <label class="label">Enter Email Address:</label>
            <input type="email" name="email" placeholder="Email" required autocomplete="off">
            <label class="label">Enter Password:</label>
            <input type="password" name="pswd" placeholder=" password" required autocomplete="off">
            <button>Sign Up</button>
            <a href="registration-users.php">Log In</a>
        </form>
    </div>
</div>
</body>
</html>
<?php
include 'connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['txt'];
    $email = $_POST['email'];
    $password_raw = $_POST['pswd'];
    $contact = $_POST['contact'];
    $home = $_POST['home'];

    // Check if password is at least 6 characters
    if (strlen($password_raw) < 6) {
        echo "<script>alert('Password must be at least 6 characters long.'); window.history.back();</script>";
        exit();
    }

    // Check if email already exists
    $check_email = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $check_email->bind_param("s", $email);
    $check_email->execute();
    $result = $check_email->get_result();

    if ($result->num_rows > 0) {
        echo "<script>alert('Email already registered. Please use another email.'); window.history.back();</script>";
    } else {
        // If email not used, continue inserting
        $password = password_hash($password_raw, PASSWORD_DEFAULT); // Hash password

        $stmt = $conn->prepare("INSERT INTO users (name, email, pass, contact, home) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $name, $email, $password, $contact, $home);

        if ($stmt->execute()) {
            echo "<script>alert('Registration successful!'); window.location.href = 'registration-users.php';</script>";
        } else {
            echo "<script>alert('Error: Could not register.'); window.history.back();</script>";
        }

        $stmt->close();
    }

    $check_email->close();
    $conn->close();
}
?>
