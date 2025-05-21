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
}

button:hover {
    background: #ff6b00;
}


   </style>

</head>
<body>
<div class="main">
    <div class="signup">
        <form action=registration-admin.php method="post">
            <label for="chk" aria-hidden="true">Log in</label>
            <input type="email" name="email" placeholder="Email" required="">
            <input type="password" name="pswd" placeholder="Password" required="">
            <button>Log in</button>
        </form>
    </div>
</div>
</body>
</html>
<?php
include 'connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['pswd'];

    $stmt = $conn->prepare("SELECT pass FROM admin WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();

    $stmt->store_result();

    if ($stmt->num_rows == 1) {
        $stmt->bind_result($hashedPassword);
        $stmt->fetch();

        if (password_verify($password, $hashedPassword)) {
            echo "<script>alert('log in successful!'); window.location.href = 'admin.php';</script>";
        } else {
            echo "<script>alert('Error: Email might already be registered.'); window.location.href = 'registration-admin.php';</script>";
        }
    } else {
        echo "No user found!";
    }

    $stmt->close();
    $conn->close();
}
?>

