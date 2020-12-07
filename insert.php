<?php

// Redirecting to registration page for GET requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    return header('Location: /register.php');
}

// Starting session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Validating role
if (empty(trim($_POST['role']))) {
    $_SESSION['error']['role'] = 'Role cannot be empty!';
} elseif (trim($_POST['role']) !== 'admin' && trim($_POST['role']) !== 'manager') {
    $_SESSION['error']['role'] = 'Role is invalid!';
}

// Validating email
if (empty(trim($_POST['email']))) {
    $_SESSION['error']['email'] = 'Email cannot be empty!';
} elseif (empty(filter_input(INPUT_POST, 'email',  FILTER_VALIDATE_EMAIL))) {
    $_SESSION['error']['email'] = 'Email seems invalid!';
}

// Validating password
if (empty(trim($_POST['password']))) {
    $_SESSION['error']['password'] = 'Password cannot be empty!';
} elseif (
    empty(trim($_POST['password2'])) ||
    trim($_POST['password2'] !== trim($_POST['password']))
) {
    $_SESSION['error']['password'] = 'Passwords do not match!';
}

// Redirecting to registration page to display errors
if (isset($_SESSION['error'])) {
    $_SESSION['input'] = [
        'role' => $_POST['role'],
        'email' => $_POST['email'],
    ];
    return header('Location: /register.php');
}

// Sanitizing data
$role = filter_input(INPUT_POST, 'role', FILTER_SANITIZE_STRING);
$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
$password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);

/** @var Database */
$db = require_once './helpers/Database.php';
$conn = $db->getConnection();

// Checking for existing user
$stmt = $conn->prepare('SELECT id FROM `users` WHERE `email` = :email LIMIT 1');
$stmt->bindParam(':email', $email);
$stmt->execute();

$user = $stmt->fetch();
if (!empty($user)) {
    $_SESSION['error']['email'] = 'Email is already registered!';
    $_SESSION['input'] = [
        'role' => $_POST['role'],
        'email' => $_POST['email'],
    ];
    return header('Location: /register.php');
}

// Hashing password
$password = password_hash($password, PASSWORD_DEFAULT);

// Inserting new user into database
$stmt = $conn->prepare(
    'INSERT INTO `users` (`email`, `password`, `role`) VALUES (:email, :password, :role)'
);
$stmt->bindParam(':email', $email);
$stmt->bindParam(':password', $password);
$stmt->bindParam(':role', $role);
$stmt->execute();

// Redirecting to login page to display confirmation of registration
$_SESSION['flash']['success'] = 'Registration is successful! Please login.';
return header('Location: /login.php');
