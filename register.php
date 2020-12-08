<?php $isThisHome = false ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php require_once './includes/head.php' ?>
    <title>Register to <?= $app ?></title>
</head>

<?php

if (!empty($user)) {
    return header('Location: /index.php');
}

$flash = $_SESSION['flash'] ?? null;
$input = $_SESSION['input'] ?? null;
$error = $_SESSION['error'] ?? null;

unset($_SESSION['flash']);
unset($_SESSION['input']);
unset($_SESSION['error']);

?>

<body>
    <?php require_once './includes/nav.php' ?>

    <main class="container">
        <section class="row mt-5">
            <form action="/insert.php" method="post" class="col-md-6 offset-md-3 col-lg-4 offset-lg-4 p-4 border rounded-lg">
                <h3 class="text-center mb-3">Register for an account</h3>
                <?php if (isset($flash['secondary'])) : ?>
                    <p class="alert alert-secondary">
                        <?= $flash['secondary'] ?>
                    </p>
                <?php endif ?>
                <div class="form-group">
                    <label for="userRole">Role</label>
                    <select id="userRole" class="form-control<?= isset($error['role']) ? ' is-invalid' : '' ?>" name="role">
                        <option disabled selected>Choose One</option>
                        <?php foreach (['admin', 'manager'] as $v) : ?>
                            <option value="<?= $v ?>" <?php if (isset($input['role']) && $input['role'] === $v) : ?>selected<?php endif ?>>
                                <?= ucfirst($v) ?>
                            </option>
                        <?php endforeach ?>
                    </select>
                    <?php if (isset($error['role'])) : ?>
                        <span class="invalid-feedback">
                            <?= $error['role'] ?>
                        </span>
                    <?php endif ?>
                </div>
                <div class="form-group">
                    <label for="emailAddress">Email Address</label>
                    <input type="email" class="form-control<?= isset($error['email']) ? ' is-invalid' : '' ?>" id="emailAddress" name="email" value="<?= $input['email'] ?? null ?>">
                    <?php if (isset($error['email'])) : ?>
                        <span class="invalid-feedback">
                            <?= $error['email'] ?>
                        </span>
                    <?php endif ?>
                </div>
                <div class="form-group">
                    <label for="newPassword">New Password</label>
                    <input type="password" class="form-control<?= isset($error['password']) ? ' is-invalid' : '' ?>" id="newPassword" name="password">
                    <?php if (isset($error['password'])) : ?>
                        <span class="invalid-feedback">
                            <?= $error['password'] ?>
                        </span>
                    <?php endif ?>
                </div>
                <div class="form-group">
                    <label for="confirmPassword">Confirm Password</label>
                    <input type="password" class="form-control" id="confirmPassword" name="password2">
                </div>
                <button type="submit" class="btn btn-dark btn-block">Register</button>
            </form>
        </section>
    </main>

    <?php require_once './includes/foot.php' ?>
</body>

</html>