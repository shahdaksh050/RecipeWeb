<?php
// index.php
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}
require_once 'config.php'; // Ensure this file sets up $mysqli (MySQLi connection)
require_once 'auth/google-config.php'; // Add this to define $client for Google login URL

// Initialize variables for messages and errors
$message = "";
$error   = "";

// Determine action from GET parameter; default is "login"
$action = isset($_GET['action']) ? $_GET['action'] : 'login';

// Process POST submissions for login or signup
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($action === 'signup') {
        // Retrieve and sanitize sign-up data
        $fullName        = trim($_POST['fullName']);
        $email           = trim($_POST['email']);
        $password        = trim($_POST['password']);
        $confirmPassword = trim($_POST['confirmPassword']);

        // Verify passwords match
        if ($password !== $confirmPassword) {
            $error = "Passwords do not match.";
        } else {
            // Hash the password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insert new user into the database
            $stmt = $mysqli->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            if (!$stmt) {
                $error = "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
            } else {
                $stmt->bind_param("sss", $fullName, $email, $hashed_password);
                if ($stmt->execute()) {
                    // Redirect to the login page with a success message
                    $message = "Registration successful. Please login.";
                    header("Location: index.php?action=login&message=" . urlencode($message));
                    exit();
                } else {
                    $error = "Error: " . $stmt->error;
                }
                $stmt->close();
            }
        }
    } elseif ($action === 'login') {
        // Retrieve and sanitize login data
        $email    = trim($_POST['loginEmail']);
        $password = trim($_POST['loginPassword']);

        // Query to fetch user by email
        $stmt = $mysqli->prepare("SELECT id, username, password FROM users WHERE email = ?");
        if (!$stmt) {
            $error = "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
        } else {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->store_result();

            // Check if user exists
            if ($stmt->num_rows === 1) {
                $stmt->bind_result($id, $username, $hashed_password);
                $stmt->fetch();
                // Verify password
                if (password_verify($password, $hashed_password)) {
                    // Set session variables and redirect (adjust destination as needed)
                    $_SESSION["loggedin"] = true;
                    $_SESSION["id"] = $id;
                    $_SESSION["username"] = $username;
                    header("Location: homepage.php");
                    exit();
                } else {
                    $error = "Invalid password.";
                }
            } else {
                $error = "No account found with that email.";
            }
            $stmt->close();
        }
    }
}

// If a message is passed via URL, retrieve it safely
if (isset($_GET['message'])) {
    $message = htmlspecialchars($_GET['message']);
}
?>


<!--google auth-->
<?php
// Removed Google login session handling code to avoid duplication.
// Google login session handling is now done in auth/google-callback.php.
?>

<!--google close-->

<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Auth Form</title>
  <style>
    @import url("https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap");
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: "Inter", sans-serif; background-color: #0f172a; }
    .auth-container { display: flex; justify-content: center; align-items: center; min-height: 100vh; width: 100%; }
    .auth-card { width: 480px; border-radius: 16px; background-color: #1e293b; box-shadow: 0px 4px 24px rgba(0, 0, 0, 0.3); padding: 32px; }
    .auth-tabs { display: flex; border-bottom: 1px solid #334155; margin-bottom: 24px; }
    .tab-item { font-size: 18px; font-weight: 500; width: 50%; height: 45px; background: transparent; border: none; cursor: pointer; color: #94a3b8; }
    .tab-item.active { color: #8b5cf6; border-bottom: 2px solid #8b5cf6; }
    .auth-form { display: none; flex-direction: column; gap: 24px; }
    .auth-form.active { display: flex; }
    .form-group { display: flex; flex-direction: column; gap: 8px; }
    .form-label { color: #e2e8f0; font-size: 14px; }
    .form-input { width: 100%; height: 50px; border-radius: 8px; border: 1px solid #334155; background-color: #0f172a; padding: 0 16px; color: #e2e8f0; }
    .form-input::placeholder { color: #64748b; }
    .auth-button { width: 100%; height: 48px; border-radius: 8px; background-color: #475569; color: #fff; font-size: 16px; font-weight: 500; border: none; cursor: pointer; margin-top: 16px; }
    .login-link-container { display: flex; flex-direction: column; align-items: center; gap: 8px; margin-top: 24px; }
    .login-text { color: #94a3b8; font-size: 14px; }
    .login-link { color: #8b5cf6; font-size: 14px; font-weight: 500; cursor: pointer; text-decoration: none; }
    .message { text-align: center; padding: 8px; color: #10b981; }
    .error { text-align: center; padding: 8px; color: #f87171; }
    @media (max-width: 640px) { .auth-card { width: 95%; } }
  </style>
</head>
<body>
  <section class="auth-container">
    <div class="auth-card">
      <nav class="auth-tabs">
        <!-- The tabs link to the same file with different actions -->
        <button class="tab-item <?php echo ($action === 'login') ? 'active' : ''; ?>" onclick="window.location.href='index.php?action=login'">Login</button>
        <button class="tab-item <?php echo ($action === 'signup') ? 'active' : ''; ?>" onclick="window.location.href='index.php?action=signup'">Sign Up</button>
      </nav>

      <!-- Display success or error messages -->
      <?php if ($message): ?>
        <div class="message"><?php echo $message; ?></div>
      <?php endif; ?>
      <?php if ($error): ?>
        <div class="error"><?php echo $error; ?></div>
      <?php endif; ?>

      <!-- Login Form -->
      <form class="auth-form <?php echo ($action === 'login') ? 'active' : ''; ?>" id="login-form" method="post" action="index.php?action=login">
        <div class="form-group">
          <label for="loginEmail" class="form-label">Email Address</label>
          <input type="email" name="loginEmail" id="loginEmail" placeholder="Enter your email" class="form-input" required />
        </div>
        <div class="form-group">
          <label for="loginPassword" class="form-label">Password</label>
          <input type="password" name="loginPassword" id="loginPassword" placeholder="Enter your password" class="form-input" required />
        </div>
        <button type="submit" class="auth-button">Login</button>
      </form>

      <!-- Sign Up Form -->
      <form class="auth-form <?php echo ($action === 'signup') ? 'active' : ''; ?>" id="signup-form" method="post" action="index.php?action=signup">
        <div class="form-group">
          <label for="fullName" class="form-label">Full Name</label>
          <input type="text" name="fullName" id="fullName" placeholder="Enter your full name" class="form-input" required />
        </div>
        <div class="form-group">
          <label for="email" class="form-label">Email Address</label>
          <input type="email" name="email" id="email" placeholder="Enter your email" class="form-input" required />
        </div>
        <div class="form-group">
          <label for="password" class="form-label">Password</label>
          <input type="password" name="password" id="password" placeholder="Enter your password" class="form-input" required />
        </div>
        <div class="form-group">
          <label for="confirmPassword" class="form-label">Confirm Password</label>
          <input type="password" name="confirmPassword" id="confirmPassword" placeholder="Confirm your password" class="form-input" required />
        </div>
        <button type="submit" class="auth-button">Sign Up</button>
        <div class="login-link-container">
          <p class="login-text">Already have an account?</p>
          <a class="login-link" href="index.php?action=login">Login to your account</a>
        </div>
      </form>

      <div style="text-align:center; margin-top: 20px;">
        <?php
        $login_url = $client->createAuthUrl();
        ?>
        <a href="<?= htmlspecialchars($login_url) ?>" style="display: inline-block; padding: 10px 20px; background-color: #db4437; color: white; border-radius: 5px; text-decoration: none; font-weight: bold;">
          Login with Google
        </a>
      </div>
    </div>
  </section>
</body>
</html>