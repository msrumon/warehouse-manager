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
    if ($prouct === false) {
        $_SESSION['flash']['danger'] = 'Product not found!';
        return header('Location: /index.php');
    }
}

// Validating supplier
$supplierId = trim($_POST['supplier']);
if (empty($supplierId)) {
    $_SESSION['error']['supplier'] = 'Supplier cannot be empty!';
} else {
    $stmt = $conn->prepare('SELECT `id` FROM `suppliers` WHERE `id` = :id LIMIT 1');
    $stmt->bindParam(':id', $supplierId);
    $stmt->execute();
    if ($stmt->fetch() === false) {
        $_SESSION['error']['supplier'] = 'Supplier not found!';
    }
}

// Validating title
if (empty(trim($_POST['title']))) {
    $_SESSION['error']['title'] = 'Title cannot be empty!';
}

// Validating description
if (empty(trim($_POST['description']))) {
    $_SESSION['error']['description'] = 'Description cannot be empty!';
}

// Validating price
if (empty(trim($_POST['price']))) {
    $_SESSION['error']['price'] = 'Price cannot be empty!';
}

// Validating image
if (!empty($_FILES['image'])) {
    $info = pathinfo($_FILES['image']['name']);
    if (
        $info['extension'] !== 'jpg' &&
        $info['extension'] !== 'png' &&
        $info['extension'] !== 'jpeg'
    ) {
        $_SESSION['error']['image'] = 'Image is invalid!';
    }
}

// Redirecting to product modify page to display errors
if (isset($_SESSION['error'])) {
    $_SESSION['input'] = [
        'supplier' => $_POST['supplier'],
        'title' => $_POST['title'],
        'description' => $_POST['description'],
        'price' => $_POST['price'],
    ];
    return header('Location: /product-modify.php?id=' . $productId);
}

// Sanitizing data
unset($supplierId);
unset($productId);
$id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
$supplier = filter_input(INPUT_POST, 'supplier', FILTER_SANITIZE_NUMBER_INT);
$title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
$description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);
$price = filter_input(INPUT_POST, 'price', FILTER_SANITIZE_NUMBER_FLOAT);

// Processing image
$imgName = $_FILES['image']['tmp_name'];
if (is_uploaded_file($imgName)) {
    $name = hash_file('sha1', $imgName);
    $image = $name . '.' . $info['extension'];
    move_uploaded_file(
        $imgName,
        // Needs to keep everything separated
        __DIR__ . '/uploads' . '/' . $image
    );
    unlink(
        __DIR__ . '/uploads' . '/' . $product->image
    );
}

// Updating current product
$stmt = $conn->prepare(
    sprintf(
        'UPDATE `products`
        SET
            `title` = :title,
            `description` = :description,
            `price` = :price,
            %s
            `supplier_id` = :supplier
        WHERE `id` = :id',
        isset($image) ? '`image` = :image,' : ''
    )
);
$stmt->bindParam(':id', $id);
$stmt->bindParam(':title', $title);
$stmt->bindParam(':description', $description);
$stmt->bindParam(':price', $price);
if (isset($image)) {
    $stmt->bindParam(':image', $image);
}
$stmt->bindParam(':supplier', $supplier);
$stmt->execute();

// Redirecting to home page to display confirmation of product modification
$_SESSION['flash']['success'] = 'Product modified successfully!';
return header('Location: /index.php');
