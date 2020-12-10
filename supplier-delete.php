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

// Sanitizing data
unset($supplierId);
$id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);

// Deleting current supplier
$stmt = $conn->prepare('DELETE FROM `suppliers` WHERE `id` = :id');
$stmt->bindParam(':id', $id);
try {
    $stmt->execute();
} catch (Exception $e) {
    $_SESSION['flash']['danger'] = 'Supplier cannot be deleted! One or more products are bound with it.';
    return header('Location: /index.php');
}

// Redirecting to home page to display confirmation of supplier removal
$_SESSION['flash']['success'] = 'Supplier deleted successfully!';
return header('Location: /index.php');
