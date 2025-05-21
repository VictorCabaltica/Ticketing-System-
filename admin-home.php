
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
            background: linear-gradient(to bottom, #24243e, #0f0c29);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            color: #fff;
        }

        .admin-container {
            background-color: #3a3a5c;
            padding: 40px 50px;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.6);
            text-align: center;
            width: 400px;
        }

        .admin-container h2 {
            margin-bottom: 30px;
            font-size: 2rem;
            color: #FFF5C5;
        }

        .admin-btn {
            display: block;
            width: 100%;
            padding: 15px 0;
            margin: 15px 0;
            background-color: orange;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: bold;
            color: #FFF5C5;
            cursor: pointer;
            transition: background 0.3s ease-in-out;
        }

        .admin-btn:hover {
            background-color: #ff6b00;
        }

    </style>
</head>
<body>

<div class="admin-container">
    <h2>Admin Dashboard</h2>
    <button class="admin-btn" onclick="location.href='signinpage-admin.php'">Add Admin</button>
    <button class="admin-btn" onclick="location.href='signinpage-agent.php'">Assign Agent</button>
</div>

</body>
</html>
