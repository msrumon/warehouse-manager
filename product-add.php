<?php $isThisHome = false ?>

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

$input = $_SESSION['input'] ?? null;
$error = $_SESSION['error'] ?? null;

unset($_SESSION['input']);
unset($_SESSION['error']);

// $db is coming from ./includes/head.php
$conn = $db->getConnection();
$stmt = $conn->query('SELECT * FROM `suppliers`');
$suppliers = $stmt->fetchAll();

?>

<body>
    <?php require_once './includes/nav.php' ?>

    <main class="container">
        <section class="row mt-5">
            <form action="/product-store.php" method="post" enctype="multipart/form-data" class="col-md-6 col-lg-4">
                <h3 class="mb-3">Add New Product</h3>
                <div class="form-group">
                    <label for="supplier">Supplier</label>
                    <select id="supplier" class="form-control<?= isset($error['supplier']) ? ' is-invalid' : '' ?>" name="supplier">
                        <option disabled selected>Choose One</option>
                        <?php foreach ($suppliers as $supplier) : ?>
                            <option value="<?= $supplier->id ?>" <?php if (isset($input['supplier']) && $input['supplier'] === $supplier->id) : ?>selected<?php endif ?>>
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
                    <input type="text" class="form-control<?= isset($error['title']) ? ' is-invalid' : '' ?>" id="productTitle" name="title" value="<?= $input['title'] ?? null ?>">
                    <?php if (isset($error['title'])) : ?>
                        <span class="invalid-feedback">
                            <?= $error['title'] ?>
                        </span>
                    <?php endif ?>
                </div>
                <div class="form-group">
                    <label for="productDescription">Description</label>
                    <!-- Needs to keep it in one long line -->
                    <textarea class="form-control<?= isset($error['description']) ? ' is-invalid' : '' ?>" id="productDescription" rows="3" name="description"><?= $input['description'] ?? null ?></textarea>
                    <?php if (isset($error['description'])) : ?>
                        <span class="invalid-feedback">
                            <?= $error['description'] ?>
                        </span>
                    <?php endif ?>
                </div>
                <div class="form-group">
                    <label for="productPrice">Price</label>
                    <input type="number" class="form-control<?= isset($error['price']) ? ' is-invalid' : '' ?>" id="productPrice" name="price" value="<?= $input['price'] ?? null ?>">
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
                <div class="d-flex justify-content-between">
                    <button type="submit" class="btn btn-dark">Add</button>
                    <a href="/index.php" class="btn btn-light">Cancel</a>
                </div>
            </form>
        </section>
    </main>

    <?php require_once './includes/foot.php' ?>
</body>

</html>