<?php

// Redirecting to registration page for GET requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    return header('Location: /index.php');
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

// Validating ID
$supplierId = trim($_POST['id']);
if (empty($supplierId)) {
    $_SESSION['flash']['danger'] = 'Supplier ID is empty!';
    return header('Location: /index.php');
} else {
    $stmt = $conn->prepare('SELECT `id` FROM `suppliers` WHERE `id` = :id LIMIT 1');
    $stmt->bindParam(':id', $supplierId);
    $stmt->execute();
    if ($stmt->fetch() === false) {
        $_SESSION['flash']['danger'] = 'Supplier not found!';
        return header('Location: /index.php');
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

// Redirecting to supplier modify page to display errors
if (isset($_SESSION['error'])) {
    $_SESSION['input'] = [
        'name' => $_POST['name'],
        'address' => $_POST['address'],
    ];
    return header('Location: /supplier-modify.php?id=' . $supplierId);
}

// Sanitizing data
unset($supplierId);
$id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
$name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
$address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_STRING);

// Updating current supplier
$stmt = $conn->prepare(
    'UPDATE `suppliers` SET `name` = :name, `address` = :address WHERE `id` = :id'
);
$stmt->bindParam(':id', $id);
$stmt->bindParam(':name', $name);
$stmt->bindParam(':address', $address);
$stmt->execute();

// Redirecting to home page to display confirmation of supplier modification
$_SESSION['flash']['success'] = 'Supplier modified successfully!';
return header('Location: /index.php');
