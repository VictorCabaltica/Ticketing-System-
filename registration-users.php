
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
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

#chk {
    display: none;
}

.signup,
.login {
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

.signup {
    transform: translateY(0%);
}

.login {
    background: white;
    border-radius: 60% / 10%;
    transform: translateY(100%);
}

#chk:checked ~ .signup {
    transform: translateY(-100%);
}

#chk:checked ~ .login {
    transform: translateY(0%);
}

label {
    color: #FFF5C5;
    font-size: 2.3em;
    justify-content: center;
    display: flex;
    margin: 20px;
    font-weight: bold;
    cursor: pointer;
    transition: .5s ease-in-out;
}

.login label {
    color: black;
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
    margin-left: 45px;
}

button:hover {
    background: #ff6b00;
}
a{
    color: #FFF5C5;
    margin-left: 67px;
}
a:hover{
    color: white;
}


   </style>

</head>
<body>
<div class="main">
    <div class="signup">
        <form action=registration-users.php method="post">
            <label for="chk" aria-hidden="true">Log in</label>
            <input type="email" name="email" placeholder="Email" required="">
            <input type="password" name="pswd" placeholder="Password" required="">
            <button>Log in</button>
            <a href="signinpage-users.php">Register</a>
        </form>
    </div>
</div>
</body>
</html>
<?php
session_start();
include 'connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['pswd'];

    $stmt = $conn->prepare("SELECT user_id, name, pass FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 1) {
        $stmt->bind_result($user_id, $name, $hashedPassword);
        $stmt->fetch();

        if (password_verify($password, $hashedPassword)) {
            $_SESSION['user_email'] = $email;
            $_SESSION['user_id'] = $user_id;
            $_SESSION['username'] = $name;

            echo "<script>alert('Log in successful!'); window.location.href = 'user-dashboard.php';</script>";
        } else {
            echo "<script>alert('Invalid password!'); window.location.href = 'login-users.php';</script>";
        }
    } else {
        echo "<script>alert('No user found with that email!'); window.location.href = 'login-users.php';</script>";
    }
}
?>
