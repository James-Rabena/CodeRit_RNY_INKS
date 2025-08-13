<?php
session_start();
require_once __DIR__ . '/db_connection.php'; // Adjust path if necessary

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($email === '' || $password === '') {
        $_SESSION['error'] = 'Please fill in all fields.';
        header('Location: login.php');
        exit();
    }

    // Lookup by email
    $stmt = $conn->prepare("SELECT id, firstName, lastName, email, password, role FROM users WHERE email = ?");
    if ($stmt === false) {
        error_log('Prepare failed: ' . $conn->error);
        $_SESSION['error'] = 'An error occurred. Please try again later.';
        header('Location: login.php');
        exit();
    }
    
    $hashedPassword = password_hash('your_new_password', PASSWORD_DEFAULT);
    echo $hashedPassword;

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Verify password
        if (password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['user_logged_in'] = true;
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['firstName'] . ' ' . $user['lastName'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role']; // Set the user's role

            // Redirect based on role
            if ($user['role'] === 'admin') {
                header('Location: admindashboard.php');
            } else {
                header('Location: ../index.php');
            }
            exit();
        } else {
            $_SESSION['error'] = 'Invalid password.';
        }
    } else {
        $_SESSION['error'] = 'No account found with that email.';
    }

    $stmt->close();
    $conn->close();

    header('Location: login.php');
    exit();
}

// Block direct GET access
header('Location: login.php');
exit();