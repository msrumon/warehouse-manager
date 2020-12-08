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

if ($user->role !== 'admin') {
    unset($_SESSION['user']);
    $_SESSION['flash']['info'] = 'You are not authorized!';
    return header('Location: /login.php');
}

$supplierId = trim($_GET['id']);
if (empty($supplierId)) {
    return header('Location: /index.php');
}

// $db is coming from ./includes/head.php
$conn = $db->getConnection();
$stmt = $conn->prepare('SELECT * FROM `suppliers` WHERE `id` = :id LIMIT 1');
$stmt->bindParam(':id', $supplierId);
$stmt->execute();

$supplier = $stmt->fetch();

$input = $_SESSION['input'] ?? null;
$error = $_SESSION['error'] ?? null;

unset($_SESSION['input']);
unset($_SESSION['error']);

?>

<body>
    <?php require_once './includes/nav.php' ?>

    <main class="container">
        <section class="row mt-5">
            <form action="/supplier-update.php" method="post" class="col-md-6 col-lg-4">
                <h3 class="mb-3">Update Supplier: <?= $supplier->name ?></h3>
                <div class="form-group">
                    <label for="supplierName">Name</label>
                    <input type="text" class="form-control<?= isset($error['name']) ? ' is-invalid' : '' ?>" id="supplierName" name="name" value="<?= $input['name'] ?? $supplier->name ?? null ?>">
                    <?php if (isset($error['name'])) : ?>
                        <span class="invalid-feedback">
                            <?= $error['name'] ?>
                        </span>
                    <?php endif ?>
                </div>
                <div class="form-group">
                    <label for="supplierAddress">Address</label>
                    <!-- Needs to keep it in one long line -->
                    <textarea class="form-control<?= isset($error['address']) ? ' is-invalid' : '' ?>" id="supplierAddress" rows="3" name="address"><?= $input['address'] ?? $supplier->address ?? null ?></textarea>
                    <?php if (isset($error['address'])) : ?>
                        <span class="invalid-feedback">
                            <?= $error['address'] ?>
                        </span>
                    <?php endif ?>
                </div>
                <input type="hidden" name="id" value="<?= $supplier->id ?>">
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