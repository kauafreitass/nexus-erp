<?php
if (!isset($_SESSION['auth'])) {
    $_SESSION['auth'] = 'notAuthenticated';
}
?>

<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="<?= asset('css/home.css'); ?>">
    <script src="https://kit.fontawesome.com/904bf533d7.js" crossorigin="anonymous"></script>
    <title>Página inicial</title>
</head>
<body>

<?php view('components/header'); ?>



<?php view('components/footer'); ?>

</body>
</html>
