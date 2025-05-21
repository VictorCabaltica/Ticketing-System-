<?php
// DB credentials
$host = 'localhost';
$db   = 'ticketing_db';
$user = 'root';
$pass = ''; // Change if needed
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name       = $_POST['name'];
    $email      = $_POST['email'];
    $department = $_POST['department'];
    $priority   = $_POST['priority'];
    $subject    = $_POST['subject'];
    $description= $_POST['description'];

    // File upload logic
    $uploadPath = null; // default null
    if (isset($_FILES['attachments']) && $_FILES['attachments']['error'] == 0) {
        $file = $_FILES['attachments'];
        $filename = basename($file['name']);
        $uploadDir = 'uploads/';

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $uploadPath = $uploadDir . time() . "_" . $filename;

        if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
            $uploadPath = null; // fallback if move failed
        }
    }

    // Save to database
    $stmt = $pdo->prepare("INSERT INTO tickets 
        (name, email, department, priority, subject, description, attachment)
        VALUES (?, ?, ?, ?, ?, ?, ?)");

    $stmt->execute([$name, $email, $department, $priority, $subject, $description, $uploadPath]);

    echo "<script>
        alert('Your ticket has been submitted successfully.');
        window.location.href = 'thank_you.html';
    </script>";
}
?>
