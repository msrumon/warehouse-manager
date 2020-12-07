<?php

// Redirecting to registration page for GET requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    return header('Location: /product-add.php');
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
if (!is_uploaded_file($_FILES['image']['tmp_name'])) {
    $_SESSION['error']['image'] = 'Image not found!';
} else {
    $info = pathinfo($_FILES['image']['name']);
    if (
        $info['extension'] !== 'jpg' &&
        $info['extension'] !== 'png' &&
        $info['extension'] !== 'jpeg'
    ) {
        $_SESSION['error']['image'] = 'Image is invalid!';
    }
}

// Redirecting to product add page to display errors
if (isset($_SESSION['error'])) {
    $_SESSION['input'] = [
        'supplier' => $_POST['supplier'],
        'title' => $_POST['title'],
        'description' => $_POST['description'],
        'price' => $_POST['price'],
    ];
    return header('Location: /product-add.php');
}

// Sanitizing data
unset($supplierId);
$supplier = filter_input(INPUT_POST, 'supplier', FILTER_SANITIZE_NUMBER_INT);
$title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
$description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);
$price = filter_input(INPUT_POST, 'price', FILTER_SANITIZE_NUMBER_FLOAT);

// Processing image
$name = hash_file('sha1', $_FILES['image']['tmp_name']);
$image = $name . '.' . $info['extension'];
move_uploaded_file(
    $_FILES['image']['tmp_name'],
    // Needs to keep everything separated
    __DIR__ . '/uploads' . '/' . $image
);

// Inserting new product into database
$stmt = $conn->prepare(
    'INSERT INTO `products` (
        `title`,
        `description`,
        `price`,
        `image`,
        `supplier_id`
    ) VALUES (
        :title,
        :description,
        :price,
        :image,
        :supplier
    )'
);
$stmt->bindParam(':title', $title);
$stmt->bindParam(':description', $description);
$stmt->bindParam(':price', $price);
$stmt->bindParam(':image', $image);
$stmt->bindParam(':supplier', $supplier);
$stmt->execute();

// Redirecting to home page to display confirmation of product addition
$_SESSION['flash']['success'] = 'Product added successfully!';
return header('Location: /index.php');
