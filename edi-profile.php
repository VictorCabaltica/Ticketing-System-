<?php
session_start();

if (!isset($_SESSION['user_email'])) {
    echo "Please log in to edit your profile.";
    exit();
}

include 'connection.php';

$email = $_SESSION['user_email'];

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $newName = $_POST['name'];
    $newContact = $_POST['contact'];
    $newHome = $_POST['home'];

    $stmt = $conn->prepare("UPDATE users SET name = ?, contact = ?, home = ? WHERE email = ?");
    $stmt->bind_param("ssss", $newName, $newContact, $newHome, $email);

    if ($stmt->execute()) {
        $stmt->close();
        $conn->close();
        header("Location: profile.php?updated=true");
        exit();
    }

    $stmt->close();
}

// Fetch current user details for form population
$stmt = $conn->prepare("SELECT name, email, contact, home FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($name, $email, $contact, $home);
$stmt->fetch();
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Edit Profile</title>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background-color: #f4f7fb;
      margin: 0;
      padding: 0;
    }

    .edit-container {
      max-width: 600px;
      margin: 40px auto;
      background: #fff;
      padding: 40px 30px;
      border-radius: 10px;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
    }

    h2 {
      text-align: center;
      color: #333;
      margin-bottom: 30px;
    }

    label {
      font-weight: 600;
      color: #444;
      display: block;
      margin-bottom: 8px;
    }

    input, textarea {
      width: 100%;
      padding: 12px;
      margin-bottom: 20px;
      border: 1px solid #ccc;
      border-radius: 6px;
      font-size: 16px;
      box-sizing: border-box;
    }

    .btn {
      background-color: #3498db;
      color: white;
      font-size: 16px;
      font-weight: bold;
      padding: 12px;
      width: 100%;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }

    .btn:hover {
      background-color: #2980b9;
    }

    .back-link {
      display: block;
      margin-top: 20px;
      text-align: center;
      color: #888;
      text-decoration: none;
    }

    .back-link:hover {
      color: #444;
    }
  </style>
</head>
<body>

  <div class="edit-container">
    <h2>Edit Profile</h2>

    <form method="POST" action="edi-profile.php">
      <label for="name">Name:</label>
      <input type="text" name="name" id="name" value="<?= htmlspecialchars($name) ?>" required>

      <label for="contact">Contact Number:</label>
      <input type="text" name="contact" id="contact" value="<?= htmlspecialchars($contact) ?>" required>

      <label for="home">Home Address:</label>
      <textarea name="home" id="home" rows="3" required><?= htmlspecialchars($home) ?></textarea>

      <button type="submit" class="btn" onclick="confirmSave(event)">Save Changes</button>

    </form>

    <a class="back-link" href="profile.php">← Back to Profile</a>
  </div>

</body>
</html>
<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $newName = $_POST['name'];
    $newContact = $_POST['contact'];
    $newHome = $_POST['home'];

    $stmt = $conn->prepare("UPDATE users SET name = ?, contact = ?, home = ? WHERE email = ?");
    $stmt->bind_param("ssss", $newName, $newContact, $newHome, $email);

    if ($stmt->execute()) {
        $stmt->close();
        $conn->close();
        header("Location: profile.php?updated=true"); // Redirect to profile page with success flag
        exit();
    }
}

?>
