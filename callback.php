<?php
session_start();
require_once 'vendor/autoload.php';
require_once 'google_config.php';
require_once 'db.php';


error_reporting(E_ALL);
ini_set('display_errors', 1);


// Create Google Client
$client = new Google_Client();
$client->setClientId(GOOGLE_CLIENT_ID);
$client->setClientSecret(GOOGLE_CLIENT_SECRET);
$client->setRedirectUri(GOOGLE_REDIRECT_URI);

try {
    if (!isset($_GET['code'])) {
        logError("No authorization code received from Google");
        throw new Exception("No authorization code received");
    }

    // Get token from code
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    if (isset($token['error'])) {
        logError("Token Error: " . json_encode($token));
        throw new Exception("Failed to get access token");
    }
    $client->setAccessToken($token['access_token']);

    // Get user profile
    $google_oauth = new Google_Service_Oauth2($client);
    $google_account_info = $google_oauth->userinfo->get();

    $email = mysqli_real_escape_string($con, $google_account_info->email);
    $name = mysqli_real_escape_string($con, $google_account_info->name);
    $google_id = mysqli_real_escape_string($con, $google_account_info->id);

    logError("Google Account Info: " . json_encode([
        'email' => $email,
        'name' => $name,
        'google_id' => $google_id
    ]));

    // Check if user exists
    $check_user = "SELECT * FROM users WHERE email='$email' OR google_id='$google_id'";
    $result = mysqli_query($con, $check_user);

    if (!$result) {
        logError("Database Error (check user): " . mysqli_error($con));
        throw new Exception("Database error while checking user");
    }

    if (mysqli_num_rows($result) > 0) {
        // User exists - update their Google ID if needed
        $user = mysqli_fetch_assoc($result);
        if (empty($user['google_id'])) {
            $update_google_id = "UPDATE users SET google_id='$google_id' WHERE email='$email'";
            if (!mysqli_query($con, $update_google_id)) {
                logError("Database Error (update google_id): " . mysqli_error($con));
            }
        }
        
        // Set session variables
        $_SESSION['auth'] = true;
        $_SESSION['auth_user'] = [
            'id' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email']
        ];
        $_SESSION['loggedInStatus'] = true;
        
        logError("Existing user logged in successfully: " . $email);
        header("Location: user/dashboard.php");
        exit();
    } else {
        // New user - register them
        $password = bin2hex(random_bytes(16)); // Generate a random password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        $register_query = "INSERT INTO users (name, email, password, google_id) 
                          VALUES ('$name', '$email', '$hashed_password', '$google_id')";
        
        if (mysqli_query($con, $register_query)) {
            $user_id = mysqli_insert_id($con);
            
            // Set session variables
            $_SESSION['auth'] = true;
            $_SESSION['auth_user'] = [
                'id' => $user_id,
                'name' => $name,
                'email' => $email
            ];
            $_SESSION['loggedInStatus'] = true;
            
            logError("New user registered successfully: " . $email);
            header("Location: user/dashboard.php");
            exit();
        } else {
            logError("Database Error (register user): " . mysqli_error($con));
            $_SESSION['message'] = "Registration failed! Please try again.";
            header("Location: login.php");
            exit();
        }
    }
} catch (Exception $e) {
    logError("Exception: " . $e->getMessage());
    $_SESSION['message'] = "Google login failed! Please try again.";
    header("Location: login.php");
    exit();
} 