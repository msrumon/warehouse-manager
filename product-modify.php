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

$stmt = $conn->query('SELECT * FROM `suppliers`');
$suppliers = $stmt->fetchAll();

$input = $_SESSION['input'];
$error = $_SESSION['error'];

unset($_SESSION['input']);
unset($_SESSION['error']);

?>

<body>
    <?php require_once './includes/nav.php' ?>

    <main class="container">
        <section class="row mt-5">
            <form action="/product-update.php" method="post" enctype="multipart/form-data" class="col-md-6 col-lg-4">
                <h3 class="mb-3">Update Product: <?= $product->title ?></h3>
                <div class="form-group">
                    <label for="supplier">Supplier</label>
                    <select id="supplier" class="form-control<?= isset($error['supplier']) ? ' is-invalid' : '' ?>" name="supplier">
                        <option disabled selected>Choose One</option>
                        <?php foreach ($suppliers as $supplier) : ?>
                            <option value="<?= $supplier->id ?>" <?php if ($input['supplier'] ?? $product->supplier_id === $supplier->id) : ?>selected<?php endif ?>>
                                <?= ucwords($supplier->name) ?>
                            </option>
                        <?php endforeach ?>
                    </select>
                    <?php if (isset($error['supplier'])) : ?>
                        <span class="invalid-feedback">
                            <?= $error['supplier'] ?>
                        </span>
                    <?php endif ?>
                </div>
                <div class="form-group">
                    <label for="productTitle">Title</label>
                    <input type="text" class="form-control<?= isset($error['title']) ? ' is-invalid' : '' ?>" id="productTitle" name="title" value="<?= $input['title'] ?? $product->title ?>">
                    <?php if (isset($error['title'])) : ?>
                        <span class="invalid-feedback">
                            <?= $error['title'] ?>
                        </span>
                    <?php endif ?>
                </div>
                <div class="form-group">
                    <label for="productDescription">Description</label>
                    <!-- Needs to keep it in one long line -->
                    <textarea class="form-control<?= isset($error['description']) ? ' is-invalid' : '' ?>" id="productDescription" rows="3" name="description"><?= $input['description'] ?? $product->description ?></textarea>
                    <?php if (isset($error['description'])) : ?>
                        <span class="invalid-feedback">
                            <?= $error['description'] ?>
                        </span>
                    <?php endif ?>
                </div>
                <div class="form-group">
                    <label for="productPrice">Price</label>
                    <input type="number" class="form-control<?= isset($error['price']) ? ' is-invalid' : '' ?>" id="productPrice" name="price" value="<?= $input['price'] ?? $product->price ?>">
                    <?php if (isset($error['price'])) : ?>
                        <span class="invalid-feedback">
                            <?= $error['price'] ?>
                        </span>
                    <?php endif ?>
                </div>
                <div class="form-group">
                    <label for="productImage">Image</label>
                    <input type="file" class="form-control-file" id="productImage" name="image">
                    <?php if (isset($error['image'])) : ?>
                        <small class="text-danger">
                            <?= $error['image'] ?>
                        </small>
                    <?php endif ?>
                </div>
                <input type="hidden" name="id" value="<?= $product->id ?>">
                <div class="d-flex justify-content-between">
                    <button type="submit" class="btn btn-dark">Save</button>
                    <a href="/index.php" class="btn btn-light">Cancel</a>
                </div>
            </form>
        </section>
    </main>

    <?php require_once './includes/foot.php' ?>
</body>

</html>