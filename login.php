<!DOCTYPE html>
<html lang="en">

<head>
    <?php require_once './includes/head.php' ?>
    <title>Login to <?= $app ?></title>
</head>

<?php

if (!empty($user)) {
    return header('Location: /index.php');
}

$flash = $_SESSION['flash'];
$input = $_SESSION['input'];
$error = $_SESSION['error'];

unset($_SESSION['flash']);
unset($_SESSION['input']);
unset($_SESSION['error']);

?>

<body>
    <?php require_once './includes/nav.php' ?>

    <main class="container">
        <section class="row mt-5">
            <form action="/select.php" method="post" class="col-md-6 offset-md-3 col-lg-4 offset-lg-4 p-4 border rounded-lg">
                <h3 class="text-center mb-3">Login to your account</h3>
                <?php if (isset($flash['danger'])) : ?>
                    <p class="alert alert-danger">
                        <?= $flash['danger'] ?>
                    </p>
                <?php elseif (isset($flash['info'])) : ?>
                    <p class="alert alert-info">
                        <?= $flash['info'] ?>
                    </p>
                <?php elseif (isset($flash['success'])) : ?>
                    <p class="alert alert-success">
                        <?= $flash['success'] ?>
                    </p>
                <?php endif ?>
                <div class="form-group">
                    <label for="emailAddress">Email Address</label>
                    <input type="email" class="form-control<?= isset($error['email']) ? ' is-invalid' : '' ?>" id="emailAddress" name="email" value="<?= $input['email'] ?>">
                    <?php if (isset($error['email'])) : ?>
                        <span class="invalid-feedback">
                            <?= $error['email'] ?>
                        </span>
                    <?php endif ?>
                </div>
                <div class="form-group">
                    <label for="currentPassword">Password</label>
                    <input type="password" class="form-control<?= isset($error['password']) ? ' is-invalid' : '' ?>" id="currentPassword" name="password">
                    <?php if (isset($error['password'])) : ?>
                        <span class="invalid-feedback">
                            <?= $error['password'] ?>
                        </span>
                    <?php endif ?>
                </div>
                <button type="submit" class="btn btn-dark btn-block">Login</button>
            </form>
        </section>
    </main>

    <?php require_once './includes/foot.php' ?>
</body>

</html>