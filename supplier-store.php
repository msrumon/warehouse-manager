<?php

// Redirecting to registration page for GET requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    return header('Location: /supplier-add.php');
}

// Starting session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Preventing unauthorized access
if (!isset($_SESSION['user'])) {
    $_SESSION['flash']['info'] = 'You are not logged in!';
    return header('Location: /login.php');
} else {
    /** @var Database */
    $db = require_once './helpers/Database.php';
    $conn = $db->getConnection();

    $stmt = $conn->prepare('SELECT * FROM `users` WHERE `id` = :id');
    $stmt->bindParam(':id', $_SESSION['user']);
    $stmt->execute();

    $user = $stmt->fetch();
    if (empty($user)) {
        $_SESSION['flash']['secondary'] = 'Please register your account again!';
        return header('Location: /register.php');
    }

    if ($user->role !== 'admin') {
        unset($_SESSION['user']);
        $_SESSION['flash']['info'] = 'You are not authorized!';
        return header('Location: /login.php');
    }
}

// Validating name
if (empty(trim($_POST['name']))) {
    $_SESSION['error']['name'] = 'Name cannot be empty!';
}

// Validating address
if (empty(trim($_POST['address']))) {
    $_SESSION['error']['address'] = 'Address cannot be empty!';
}

// Redirecting to supplier add page to display errors
if (isset($_SESSION['error'])) {
    $_SESSION['input'] = [
        'name' => $_POST['name'],
        'address' => $_POST['address'],
    ];
    return header('Location: /supplier-add.php');
}

// Sanitizing data
$name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
$address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_STRING);

// Inserting new supplier into database
$stmt = $conn->prepare(
    'INSERT INTO `suppliers` (`name`, `address`) VALUES (:name, :address)'
);
$stmt->bindParam(':name', $name);
$stmt->bindParam(':address', $address);
$stmt->execute();

// Redirecting to home page to display confirmation of supplier addition
$_SESSION['flash']['success'] = 'Supplier added successfully!';
return header('Location: /index.php');
