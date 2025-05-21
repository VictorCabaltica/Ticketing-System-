<?php
date_default_timezone_set('Asia/Manila'); // or your correct timezone
session_start();
if (!isset($_SESSION['user_email'])) {
    echo "<script>alert('You must be logged in to submit a ticket.'); window.location.href = 'registration-users.php';</script>";
    exit();
}

$userEmail = $_SESSION['user_email'];

// Database connection
$host = 'localhost';
$db   = 'ticketing_db';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Retrieve user details
$stmt = $pdo->prepare("SELECT name, email FROM users WHERE email = ?");
$stmt->execute([$userEmail]);
$user = $stmt->fetch();

if (!$user) {
    echo "<script>alert('User not found. Please log in.'); window.location.href = 'registration-users.php';</script>";
    exit();
}

$userName = $user['name'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $department = $_POST['department'];
    $priority = $_POST['priority'];
    $subject = $_POST['subject'];
    $description = $_POST['description'];

    // Handle file upload
    $attachment = null;
    if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] == 0) {
        $attachment = 'uploads/' . basename($_FILES['attachment']['name']);
        move_uploaded_file($_FILES['attachment']['tmp_name'], $attachment);
    }
    $created_at = date('Y-m-d H:i:s');

    // SLA Computation
    switch ($priority) {
        case 'critical':
            $response_due = date('Y-m-d H:i:s', strtotime($created_at . ' + 2 minutes'));
            $resolution_due = date('Y-m-d H:i:s', strtotime($response_due . ' +8 minutes'));
            break;
        case 'high':
            $response_due = date('Y-m-d H:i:s', strtotime($created_at . ' +3 minutes '));
            $resolution_due = date('Y-m-d H:i:s', strtotime($response_due . ' +12 minutes'));
            break;
        case 'medium':
            $response_due = date('Y-m-d H:i:s', strtotime($created_at . ' +4 minutes'));
            $resolution_due = date('Y-m-d H:i:s', strtotime($response_due . ' +24 minutes'));
            break;
        case 'low':
        default:
            $response_due = date('Y-m-d H:i:s', strtotime($created_at . ' +5 minutes'));
            $resolution_due = date('Y-m-d H:i:s', strtotime($response_due . ' +48 minutes'));
            break;
    }

$stmt = $pdo->prepare("INSERT INTO tickets (name, email, department, priority, subject, description, attachment, status, response_due, resolution_due) 
VALUES (?, ?, ?, ?, ?, ?, ?, 'open', ?, ?)");
$stmt->execute([$userName, $userEmail, $department, $priority, $subject, $description, $attachment, $response_due, $resolution_due]);


    // Redirect to the dashboard after ticket submission
    echo "<script>alert('Ticket submitted successfully!'); window.location.href = 'user-dashboard.php';</script>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Report an Issue - Ticketing System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
<style>
        :root {
            --primary-color: #3498db;
            --secondary-color: #2980b9;
            --success-color: #2ecc71;
            --warning-color: #f39c12;
            --danger-color: #e74c3c;
            --light-gray: #ecf0f1;
            --dark-gray: #7f8c8d;
            --text-color: #2c3e50;
            --white: #ffffff;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: var(--text-color);
            background-color: #f5f7fa;
            padding: 20px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background-color: var(--white);
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .header {
            background-color: #2c3e50;
            color: var(--white);
            padding: 30px;
            text-align: center;
        }

        .header h1 {
            margin-bottom: 10px;
            font-size: 2.2rem;
        }

        .header p {
            opacity: 0.9;
            font-size: 1rem;
        }

        .ticket-form {
            padding: 30px;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--text-color);
        }

        .form-group input[type="text"],
        .form-group input[type="email"],
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }

        .form-group input[type="text"]:focus,
        .form-group input[type="email"]:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            border-color: var(--primary-color);
            outline: none;
            box-shadow: 0 0 0 2px rgba(52, 152, 219, 0.2);
        }

        .form-group textarea {
            resize: vertical;
            min-height: 120px;
        }

        .priority-options {
            display: flex;
            gap: 15px;
            margin-top: 10px;
        }

        .priority-option {
            display: flex;
            align-items: center;
            cursor: pointer;
        }

        .priority-option input[type="radio"] {
            display: none;
        }

        .priority-badge {
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
            transition: all 0.3s;
        }

        .priority-option input[type="radio"]:checked + .priority-badge {
            transform: scale(1.05);
            box-shadow: 0 0 0 2px currentColor;
        }

        .priority-badge.low {
            background-color: #e8f5e9;
            color: #2e7d32;
            border: 1px solid #a5d6a7;
        }

        .priority-badge.medium {
            background-color: #fff8e1;
            color: #f57f17;
            border: 1px solid #ffd54f;
        }

        .priority-badge.high {
            background-color: #ffebee;
            color: #c62828;
            border: 1px solid #ef9a9a;
        }

        .priority-badge.critical {
            background-color: #fce4ec;
            color: #ad1457;
            border: 1px solid #f48fb1;
        }

        .form-group input[type="file"] {
            width: 100%;
            padding: 10px;
            border: 1px dashed #ddd;
            border-radius: 4px;
            background-color: #f9f9f9;
        }

        .form-group small {
            display: block;
            margin-top: 5px;
            font-size: 0.8rem;
            color: var(--dark-gray);
        }

        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 15px;
            margin-top: 30px;
        }

        .btn {
            padding: 12px 25px;
            border: none;
            border-radius: 4px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .submit-btn {
            background-color: orange;
            color: var(--white);
        }

        .submit-btn:hover {
            background-color: orangered;
            transform: translateY(-2px);
        }

        .cancel-btn {
            background-color: var(--light-gray);
            color: var(--dark-gray);
        }

        .cancel-btn:hover {
            background-color: #ddd;
        }

        .footer {
            text-align: center;
            padding: 20px;
            background-color: var(--light-gray);
            color: var(--dark-gray);
            font-size: 0.9rem;
        }

        @media (max-width: 768px) {
            .container {
                border-radius: 0;
            }

            .priority-options {
                flex-wrap: wrap;
            }

            .form-actions {
                flex-direction: column;
            }

            .btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>
<?php include 'navbar.php'; ?>


<div class="container">
    <header class="header">
        <h1>Report an Issue</h1>
        <p>Please fill out the form below to submit your support ticket</p>
    </header>

    <main class="main-content">
        <form id="ticketForm" class="ticket-form" method="POST" action="" enctype="multipart/form-data">
            <div class="form-group">
                <label for="name">Your Name</label>
                <input type="text" id="name" name="name" required placeholder="John Doe">
            </div>

            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" required placeholder="john@example.com">
            </div>

            <div class="form-group">
                <label for="department">Department</label>
                <select id="department" name="department" required>
                    <option value="" disabled selected>Select department</option>
                    <option value="IT">IT Support</option>
                    <option value="HR">Human Resources</option>
                    <option value="Finance">Finance</option>
                    <option value="Operations">Operations</option>
                    <option value="Other">Other</option>
                </select>
            </div>

            <div class="form-group">
                <label for="priority">Priority Level</label>
                <div class="priority-options">
                    <label class="priority-option">
                        <input type="radio" name="priority" value="low" checked>
                        <span class="priority-badge low">Low</span>
                    </label>
                    <label class="priority-option">
                        <input type="radio" name="priority" value="medium">
                        <span class="priority-badge medium">Medium</span>
                    </label>
                    <label class="priority-option">
                        <input type="radio" name="priority" value="high">
                        <span class="priority-badge high">High</span>
                    </label>
                    <label class="priority-option">
                        <input type="radio" name="priority" value="critical">
                        <span class="priority-badge critical">Critical</span>
                    </label>
                </div>
            </div>

            <div class="form-group">
                <label for="subject">Subject</label>
                <input type="text" id="subject" name="subject" required placeholder="Brief description of your issue">
            </div>

            <div class="form-group">
                <label for="description">Issue Description</label>
                <textarea id="description" name="description" rows="5" required placeholder="Please describe your issue in detail..."></textarea>
            </div>

            <div class="form-group">
                <label for="attachments">Attachments (Optional)</label>
                <input type="file" id="attachments" name="attachments[]" multiple>
                <small>You can upload screenshots or documents (Max 5MB each)</small>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn submit-btn">Submit Ticket</button>
                <button type="reset" class="btn cancel-btn">Cancel</button>
            </div>
        </form>
    </main>

    <footer class="footer">
        <p>Need immediate help? Call our support line at (123) 456-7890</p>
    </footer>
</div>
</body>
</html>
