<?php
require_once 'google-config.php';
session_start();

// Google OAuth response
if (isset($_GET['code'])) {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    if (!isset($token['error'])) {
        $client->setAccessToken($token['access_token']);
        $google_oauth = new Google_Service_Oauth2($client);
        $user_info = $google_oauth->userinfo->get();
        $_SESSION['email'] = $user_info->email;
        $_SESSION['name'] = $user_info->name;

        // Connect to DB
        $conn = mysqli_connect("localhost", "root", "", "foodiehub");
        if (!$conn) {
            die("Connection failed: " . mysqli_connect_error());
        }

        // Check if user exists
        $email = $user_info->email;
        $name = $user_info->name;
        $check_query = "SELECT * FROM users WHERE email = '$email'";
        $result = mysqli_query($conn, $check_query);

        if (mysqli_num_rows($result) == 0) {
            // Insert new user
            $insert_query = "INSERT INTO users (username, email) VALUES ('$name', '$email')";
            mysqli_query($conn, $insert_query);
        }

        // Fetch user id and set session id
        $id_query = "SELECT id FROM users WHERE email = '$email' LIMIT 1";
        $id_result = mysqli_query($conn, $id_query);
        if ($id_result && mysqli_num_rows($id_result) > 0) {
            $row = mysqli_fetch_assoc($id_result);
            $_SESSION["id"] = $row['id'];
            $_SESSION["loggedin"] = true;  // Set loggedin session variable for consistency
        }

        // Redirect user to the dashboard or home page
        header('Location: /recipe/homepage.php');
        exit;
    }
}
?>
