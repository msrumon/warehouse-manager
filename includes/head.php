<?php

// Preparing database
/** @var Database */
$db = require_once './helpers/Database.php';
$db->createTables();

$app = 'Warehouse Manager';

// Starting session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['user'])) {
    $conn = $db->getConnection();
    $stmt = $conn->prepare('SELECT * FROM `users` WHERE `id` = :id LIMIT 1');
    $stmt->bindParam(':id', $_SESSION['user']);
    $stmt->execute();
    $user = $stmt->fetch();
}

?>

<!-- Required meta tags -->
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

<!-- Bootstrap CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">