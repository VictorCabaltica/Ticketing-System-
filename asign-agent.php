<?php
// assign-agent.php

session_start();
$host = 'localhost';
$db   = 'ticketing_db';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
 // include your database connection here

// Fetch tickets that are not yet assigned
$tickets = $pdo->query("SELECT * FROM tickets WHERE ticket_id NOT IN (SELECT ticket_id FROM assignment)")->fetchAll();

// Fetch available agents
$agents = $pdo->query("SELECT * FROM agent")->fetchAll();

// Handle assignment form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ticket_id = $_POST['ticket_id'];
    $agent_id = $_POST['agent_id'];

    try {
        $stmt = $pdo->prepare("INSERT INTO assignment (ticket_id, agent_id) VALUES (?, ?)");
        $stmt->execute([$ticket_id, $agent_id]);
        echo "<script>alert('Agent successfully assigned!'); window.location.href='asign-agent.php';</script>";
    } catch (PDOException $e) {
        echo "Assignment failed: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Assign Agent</title>
    <link rel="stylesheet" href="styles.css"> <!-- optional external CSS -->
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 2rem;
            background-color: #f4f4f4;
        }
        .assign-container {
            background: white;
            padding: 2rem;
            max-width: 600px;
            margin: auto;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            border-radius: 10px;
        }
        h2 {
            text-align: center;
            margin-bottom: 1rem;
        }
        select, button {
            width: 100%;
            padding: 0.8rem;
            margin: 0.5rem 0 1rem 0;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        button {
            background-color: #007bff;
            color: white;
            font-weight: bold;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="assign-container">
        <h2>Assign Agent to Ticket</h2>
        <form method="POST">
            <label for="ticket_id">Select Ticket:</label>
            <select name="ticket_id" id="ticket_id" required>
                <option value="">-- Select a Ticket --</option>
                <?php foreach ($tickets as $ticket): ?>
                    <option value="<?= $ticket['ticket_id'] ?>">
                        [<?= $ticket['priority'] ?>] <?= $ticket['subject'] ?> - <?= $ticket['name'] ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="agent_id">Select Agent:</label>
            <select name="agent_id" id="agent_id" required>
                <option value="">-- Select an Agent --</option>
                <?php foreach ($agents as $agent): ?>
                    <option value="<?= $agent['agent_id'] ?>"><?= $agent['name'] ?> (<?= $agent['email'] ?>)</option>
                <?php endforeach; ?>
            </select>

            <button type="submit">Assign Agent</button>
            <a href="admin.php" style="
                display: block;
                width: fit-content;
                margin: 1rem auto 0 auto;
                text-align: center;
                padding: 0.6rem 1.2rem;
                background-color: #6c757d;
                color: white;
                text-decoration: none;
                border-radius: 5px;
                font-weight: bold;
            ">← Back to Dashboard</a>

        </form>
    </div>
</body>
</html>
