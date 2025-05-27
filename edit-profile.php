<?php
session_start();
require_once 'config.php';

// Check if the user is logged in
if (!isset($_SESSION['id'])) {
    header("Location: index.php?action=login");
    exit();
}

$user_id = $_SESSION['id'];

// Initialize message variables
$successMsg = "";
$errorMsg   = "";

// Process form submission if POST request is received
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve and trim form data
    $username = trim($_POST['username'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $address  = trim($_POST['address'] ?? '');
    
    // Simple validation
    if (empty($username) || empty($email) || empty($address)) {
        $errorMsg = "All fields are required.";
    } else {
        $mysqli = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
        if ($mysqli->connect_error) {
            $errorMsg = "Database connection failed: " . $mysqli->connect_error;
        } else {
            // Handle profile photo upload if a file was uploaded
            $profile_photo_path = null;
            if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] !== UPLOAD_ERR_NO_FILE) {
                $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                $file_type = $_FILES['profile_photo']['type'];
                if (!in_array($file_type, $allowed_types)) {
                    $errorMsg = "Only JPG, PNG, GIF, and WEBP files are allowed for profile photo.";
                } else {
                    $upload_dir = 'photos/profile/';
                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir, 0755, true);
                    }
                    $file_ext = pathinfo($_FILES['profile_photo']['name'], PATHINFO_EXTENSION);
                    $new_filename = 'profile_' . $user_id . '.' . $file_ext;
                    $destination = $upload_dir . $new_filename;
                    if (move_uploaded_file($_FILES['profile_photo']['tmp_name'], $destination)) {
                        $profile_photo_path = $destination;
                    } else {
                        $errorMsg = "Failed to upload profile photo.";
                    }
                }
            }

            if (empty($errorMsg)) {
                // Prepare update statement with or without profile_photo
                if ($profile_photo_path !== null) {
                    $stmt = $mysqli->prepare("UPDATE users SET username = ?, email = ?, address = ?, profile_photo = ? WHERE id = ?");
                    if (!$stmt) {
                        $errorMsg = "Prepare failed: " . $mysqli->error;
                    } else {
                        $stmt->bind_param("ssssi", $username, $email, $address, $profile_photo_path, $user_id);
                    }
                } else {
                    $stmt = $mysqli->prepare("UPDATE users SET username = ?, email = ?, address = ? WHERE id = ?");
                    if (!$stmt) {
                        $errorMsg = "Prepare failed: " . $mysqli->error;
                    } else {
                        $stmt->bind_param("sssi", $username, $email, $address, $user_id);
                    }
                }

                if (empty($errorMsg)) {
                    if ($stmt->execute()) {
                        $successMsg = "Profile updated successfully.";
                    } else {
                        $errorMsg = "Failed to update profile.";
                    }
                    $stmt->close();
                }
            }
            $mysqli->close();
        }
    }
}

// Fetch user data for displaying in the form
$mysqli = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
if ($mysqli->connect_error) {
    die("Database connection failed: " . $mysqli->connect_error);
}

$stmt = $mysqli->prepare("SELECT username, email, address, profile_photo FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();
$mysqli->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Edit Profile - FoodieHub</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    /* Global Styles */
    body {
      background-color: #0f172a;
      color: #ffffff;
      margin: 0;
      padding: 0;
      font-family: 'Inter', sans-serif;
    }
    a, a:hover {
      text-decoration: none;
      color: #e2e8f0;
    }
    /* Navbar */
    .navbar-dark .navbar-brand {
      display: flex;
      align-items: center;
      gap: 0.5rem;
      font-weight: 600;
    }
    .navbar-brand span {
      color: #6366f1;
    }
    .nav-link {
      color: #cbd5e1 !important;
      font-weight: 500;
    }
    .nav-link:hover {
      color: #ffffff !important;
    }
    /* Edit Profile Form Card */
    .edit-profile-card {
      background-color: #1e293b;
      border: none;
      border-radius: 0.5rem;
      padding: 1.5rem;
      margin-top: 2rem;
    }
    .edit-profile-card h4 {
      color: #ffffff;
    }
    .form-label {
      font-weight: 500;
      color: #ffffff;
    }
    .btn-custom {
      background-color: #6366f1;
      color: #ffffff;
      font-weight: 500;
      border: none;
      transition: background-color 0.2s ease-in-out;
    }
    .btn-custom:hover {
      background-color: #4f51c0;
    }
    /* Message Styles */
    .message {
      margin-bottom: 1rem;
    }
  </style>
</head>
<body>
  <!-- Navbar -->
  <nav class="navbar navbar-dark navbar-expand-lg" style="background-color: #0f172a;">
    <div class="container">
      <a class="navbar-brand" href="#">
        <div style="width: 30px; height: 30px; border-radius: 50%; background-color: #6366f1;"></div>
        <span>FoodieHub</span>
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item"><a class="nav-link" href="homepage.php">Homepage</a></li>
          <li class="nav-item"><a class="nav-link" href="profile.php">Profile</a></li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Main Container -->
  <div class="container py-4">
    <div class="card edit-profile-card">
      <h4 class="mb-4">Edit Profile</h4>
      <!-- Display success or error messages -->
      <?php if (!empty($successMsg)): ?>
        <div class="alert alert-success message"><?= htmlspecialchars($successMsg) ?></div>
      <?php endif; ?>
      <?php if (!empty($errorMsg)): ?>
        <div class="alert alert-danger message"><?= htmlspecialchars($errorMsg) ?></div>
      <?php endif; ?>
<form action="<?= htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" enctype="multipart/form-data">
        <!-- Username -->
        <div class="mb-3">
          <label for="username" class="form-label">Username</label>
          <input
            type="text"
            class="form-control"
            id="username"
            name="username"
            value="<?= htmlspecialchars($user['username'] ?? '') ?>"
            required
          />
        </div>
        <!-- Email -->
        <div class="mb-3">
          <label for="email" class="form-label">Email</label>
          <input
            type="email"
            class="form-control"
            id="email"
            name="email"
            value="<?= htmlspecialchars($user['email'] ?? '') ?>"
            required
          />
        </div>
        <!-- Address -->
        <div class="mb-3">
          <label for="address" class="form-label">Address</label>
          <textarea
            class="form-control"
            id="address"
            name="address"
            rows="3"
            required
          ><?= htmlspecialchars($user['address'] ?? '') ?></textarea>
        </div>
        <!-- Profile Photo -->
        <div class="mb-3">
          <label for="profile_photo" class="form-label">Profile Photo</label>
          <?php if (!empty($user['profile_photo'])): ?>
            <div class="mb-2">
              <img src="<?= htmlspecialchars($user['profile_photo']) ?>" alt="Profile Photo" style="max-width: 150px; max-height: 150px; border-radius: 50%;" />
            </div>
          <?php endif; ?>
          <input
            type="file"
            class="form-control"
            id="profile_photo"
            name="profile_photo"
            accept="image/*"
          />
        </div>
        <!-- Save Button -->
        <button type="submit" class="btn btn-custom">Save Changes</button>
      </form>
    </div>
  </div>

  <!-- Bootstrap JS (Optional for interactivity) -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
