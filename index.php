<?php $isThisHome = true ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php require_once './includes/head.php' ?>
    <title><?= $app ?></title>
</head>

<?php

if (!empty($user)) {
    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);

    switch ($user->role) {
        case 'admin':
            $stmt = $conn->query('SELECT * FROM `suppliers`');
            $suppliers = $stmt->fetchAll();
            break;

        case 'manager':
            $stmt = $conn->query(
                'SELECT * FROM `products` JOIN `suppliers` ON `suppliers`.`id` = `products`.`supplier_id`'
            );
            $products = $stmt->fetchAll();
            break;

        default:
    }
}

?>

<body>
    <?php require_once './includes/nav.php' ?>

    <main class="container mt-4">
        <?php if (!empty($user)) : ?>
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
            <?php switch ($user->role):
                case 'admin': ?>
                    <div class="d-flex justify-content-between align-items-center">
                        <h2>Suppliers</h2>
                        <a href="/supplier-add.php" class="btn btn-info btn-sm">
                            <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-plus" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z" />
                            </svg>
                            Add Supplier
                        </a>
                    </div>
                    <?php if (count($suppliers) > 0) : ?>
                        <section class="table-responsive">
                            <table class="table table-borderless">
                                <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Name</th>
                                        <th scope="col">Address</th>
                                        <th scope="col">Action(s)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($suppliers as $supplier) : ?>
                                        <tr>
                                            <th scope="row"><?= $supplier->id ?></th>
                                            <td><?= $supplier->name ?></td>
                                            <td><?= $supplier->address ?></td>
                                            <td>
                                                <a href="/supplier-modify.php?id=<?= $supplier->id ?>" class="btn btn-dark btn-sm">
                                                    <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-pencil" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                                        <path fill-rule="evenodd" d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168l10-10zM11.207 2.5L13.5 4.793 14.793 3.5 12.5 1.207 11.207 2.5zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293l6.5-6.5zm-9.761 5.175l-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325z" />
                                                    </svg>
                                                    Modify
                                                </a>
                                                <a href="/supplier-remove.php?id=<?= $supplier->id ?>" class="btn btn-danger btn-sm">
                                                    <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-trash" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z" />
                                                        <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4L4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z" />
                                                    </svg>
                                                    Remove
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach ?>
                                </tbody>
                            </table>
                        </section>
                    <?php else : ?>
                        <p>No supplier(s) found!</p>
                    <?php endif ?>
                <?php break;
                case 'manager': ?>
                    <div class="d-flex justify-content-between align-items-center">
                        <h2>Products</h2>
                        <a href="/product-add.php" class="btn btn-info btn-sm">
                            <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-plus" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z" />
                            </svg>
                            Add Product
                        </a>
                    </div>
                    <?php if (count($products) > 0) : ?>
                        <section class="table-responsive">
                            <table class="table table-borderless">
                                <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Image</th>
                                        <th scope="col">Title</th>
                                        <th scope="col">Description</th>
                                        <th scope="col">Price</th>
                                        <th scope="col">Supplier</th>
                                        <th scope="col">Action(s)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($products as $product) : ?>
                                        <tr>
                                            <th scope="row"><?= $product->id ?></th>
                                            <td>
                                                <img src="/uploads/<?= $product->image ?>" alt="<?= $product->name ?>" height="50">
                                            </td>
                                            <td><?= $product->title ?></td>
                                            <td><?= $product->description ?></td>
                                            <td>&dollar;<?= $product->price ?></td>
                                            <td><?= $product->name ?></td>
                                            <td>
                                                <a href="/product-modify.php?id=<?= $product->id ?>" class="btn btn-dark btn-sm">
                                                    <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-pencil" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                                        <path fill-rule="evenodd" d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168l10-10zM11.207 2.5L13.5 4.793 14.793 3.5 12.5 1.207 11.207 2.5zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293l6.5-6.5zm-9.761 5.175l-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325z" />
                                                    </svg>
                                                    Modify
                                                </a>
                                                <a href="/product-remove.php?id=<?= $product->id ?>" class="btn btn-danger btn-sm">
                                                    <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-trash" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z" />
                                                        <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4L4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z" />
                                                    </svg>
                                                    Remove
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach ?>
                                </tbody>
                            </table>
                        </section>
                    <?php else : ?>
                        <p>No product(s) found!</p>
                    <?php endif ?>
                <?php break;
                default: ?>
            <?php endswitch ?>
        <?php else : ?>
            <h2 class="text-center">
                Welcome to Warehouse Manager!
            </h2>
            <p class="text-center">
                Please login to see all products.
            </p>
        <?php endif ?>
    </main>

    <?php require_once './includes/foot.php' ?>
</body>

</html>