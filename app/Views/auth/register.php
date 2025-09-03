<?php
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $controller = new \App\Controllers\AuthController();
    $controller->storeAccount($_POST['name'], $_POST['email'], $_POST['password'], $_POST['gender'], $_POST['birthdate']);
}

?>

<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="<?= asset('css/auth.css') ?>">
    <title><?= $title ?></title>
</head>
<body>

</body>
</html>
