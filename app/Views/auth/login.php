<?php

use App\Controllers\AuthController;

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    $controller = new AuthController();
    $controller->login($_POST['email'], $_POST['password']);;
}
?>

<!doctype html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" href="images/nexus-logo.png" type="image/png">
    <title>Login</title>
    <style>
        nav {
            display: flex;
            justify-content: flex-end;
            padding: 10px;
        }

        .logo img {
            width: 80px;
        }

        .container {
            display: flex;
            height: 100vh;

        }

        .esquerda,
        .direita {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .caixa{
            display: flex;
            flex-direction: column;
            margin-top: -500px;
        }

    </style>
</head>

<body>
    <nav>
        <div class="logo">
            <img src="images/nexus-logo.png" alt="Logo">
        </div>
    </nav>

    <div class="container">

        <div class="esquerda">
            <div class="caixa">

                <h1>Login</h1>

                <input type="text" placeholder="Email">
                <input type="password" placeholder="Password">

                <p><a href="forgot-password">Esqueci minha senha</a></p>
                <form method="POST">
                    <button type="submit">Entrar</button>

                </form>
            </div>

        </div>

        <div class="direita">

            <div class="texto">
                <h1>Que bom ter você aqui!</h1>

                <p>Entre com seus dados para acessar o Nexus, um sistema que transforma negócios em gigantes do e-commerce.</p>

            </div>
        </div>

    </div>





</body>

</html>