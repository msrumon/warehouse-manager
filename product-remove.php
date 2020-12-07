<!DOCTYPE html>
<html lang="en">

<head>
    <?php require_once './includes/head.php' ?>
    <title><?= $app ?></title>
</head>

<?php

if (empty($user)) {
    $_SESSION['flash']['info'] = 'You are not logged in!';
    return header('Location: /login.php');
}

if ($user->role !== 'manager') {
    unset($_SESSION['user']);
    $_SESSION['flash']['info'] = 'You are not authorized!';
    return header('Location: /login.php');
}

$productId = trim($_GET['id']);
if (empty($productId)) {
    return header('Location: /index.php');
}

// $db is coming from ./includes/head.php
$conn = $db->getConnection();
$stmt = $conn->prepare('SELECT * FROM `products` WHERE `id` = :id LIMIT 1');
$stmt->bindParam(':id', $productId);
$stmt->execute();

$product = $stmt->fetch();

?>

<body>
    <?php require_once './includes/nav.php' ?>

    <main class="container">
        <section class="row mt-5">
            <form action="/product-delete.php" method="post" class="col-md-6 col-lg-4">
                <h3 class="mb-3">Remove Product: <?= $product->title ?></h3>
                <p class="text-danger">Are you sure?</p>
                <input type="hidden" name="id" value="<?= $product->id ?>">
                <div class="d-flex justify-content-between">
                    <button type="submit" class="btn btn-dark">Remove</button>
                    <a href="/index.php" class="btn btn-light">Cancel</a>
                </div>
            </form>
        </section>
    </main>

    <?php require_once './includes/foot.php' ?>
</body>

</html>