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

    if ($user->role !== 'manager') {
        unset($_SESSION['user']);
        $_SESSION['flash']['info'] = 'You are not authorized!';
        return header('Location: /login.php');
    }
}

// Validating ID
$productId = trim($_POST['id']);
if (empty($productId)) {
    $_SESSION['flash']['danger'] = 'Product ID is empty!';
    return header('Location: /index.php');
} else {
    $stmt = $conn->prepare('SELECT `image` FROM `products` WHERE `id` = :id LIMIT 1');
    $stmt->bindParam(':id', $productId);
    $stmt->execute();

    $product = $stmt->fetch();
    if ($product === false) {
        $_SESSION['flash']['danger'] = 'Product not found!';
        return header('Location: /index.php');
    }
}

// Sanitizing data
unset($productId);
$id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);

// Deleting current product
$stmt = $conn->prepare('DELETE FROM `products` WHERE `id` = :id');
$stmt->bindParam(':id', $id);
$stmt->execute();
unlink(__DIR__ . '/uploads' . '/' . $product->image);

// Redirecting to home page to display confirmation of product removal
$_SESSION['flash']['success'] = 'Product deleted successfully!';
return header('Location: /index.php');
