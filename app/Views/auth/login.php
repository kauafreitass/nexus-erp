<?php

use App\Controllers\AuthController;

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $controller = new AuthController();
    $controller->login($_POST['email'], $_POST['password']);
}
?>

<!doctype html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" href="images/nexus-logo.png" type="image/png">
    <title>Login</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html,
        body {
            height: 100%;
            background-color: #050B1F;
        }

        .logo {
            position: absolute;
            top: 20px;
            right: 20px;
        }

        .logo img {
            width: 80px;
        }

        .container {
            display: flex;
            height: 100vh;
            border: 2px solid #000;
        }


        .esquerda {
            flex: 0.9;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            border-right: 2px solid #000;
            border-top-right-radius: 250px;
            border-bottom-right-radius: 250px;
            background-color: white;
        }

        .direita {
            flex: 1.1;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #050B1F;
            color: white;
            text-align: center;
        }

        .caixa {
            display: flex;
            flex-direction: column;
        }

        input {
            border-radius: 30px;
            width: 450px;
            height: 1px;
            padding: 20px;
            margin: 17px;
            border: black 3px solid;
        }

        input::placeholder {
            color: black;
            font-size: 14px;
        }

        h1 {
            display: flex;
            justify-content: center;
        }

        .titulo1 {
            font-size: 20px;
            padding: 30px;
        }

        .esqueci-senha {
            font-size: 14px;
            text-align: center;
            margin-top: 25px;
        }

        a {
            text-decoration: none;
            color: grey;
            font-size: 15px;
        }

        button {
            justify-content: center;
            align-items: center;
            background-color: #2458bf;
            display: flex;
            margin: 0 auto;
            width: 200px;
            height: 40px;
            color: white;
            border-radius: 30px;
            border: none;
            margin-top: 30px;
        }

        .titulo2 {
            font-size: 40px;
        }

        .paragrafo {
            font-size: 25px;
            width: 480px;
            padding: 15px;
            color: white;
            opacity: 0.8;

        }

        form {
            display: flex;
            justify-content: center;
            text-align: center;
            flex-direction: column;
        }
           .cadastrar {
            text-align: center;
            margin-top: 20px;
        }
        .conta{
            font-size: 25px;
        }
        .texto5{
            color: #2458BF;
        }
    </style>
</head>

<body>
    <div class="logo">
        <img src="images/nexus-logo.png" alt="Logo">
    </div>
    <div class="container">
        <div class="esquerda">
            <div class="caixa">
                <div class="titulo1">
                    <h1>Login</h1>
                </div>
                <form method="POST">
                    <input type="text" name="email" placeholder="E-mail">
                    <input type="password" name="password" placeholder="Senha">
                    <p class="esqueci-senha"><a href="forgot-password">Esqueci minha senha</a></p>

                    <button type="submit">Entrar</button>
                </form>
                <div class="cadastrar">
                    <p class="conta">Não tem uma conta?</p>
                    <p><a href="register" class="texto5">Clique aqui para criar a sua!</a></p>
                </div>
            </div>
        </div>
        <div class="direita">
            <div class="texto2">
                <h1 class="titulo2">Que bom ter você aqui!</h1>
                <p class="paragrafo">Entre com seus dados para acessar o Nexus, um sistema que transforma negócios em gigantes do e-commerce.</p>
            </div>
        </div>
    </div>
</body>

</html>