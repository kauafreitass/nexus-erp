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
    <link rel="icon" href="images/nexus-logo.png" type="image/png">
    <title>Página inicial</title>
    <style>
        body {
            background-image: url('images/Imagem Landing Page.jpg');
            background-size: cover;
            background-repeat: no-repeat;
            background-position: absolute;

        }

        navbar {
            display: flex;
            justify-content: space-between;

        }

        img {
            width: 80px;
            height: auto;
            margin-left: 20px;
            margin-top: 10px;
            background-color: white;
            border-radius: 20px;
          
        }

        .botoes {
            display: flex;
            padding: 20px;
            gap: 45px;
            align-items: center;
            justify-content: center;

        }

        a {
            color: white;
            text-decoration: none;
            font-size: 20px;
        }

        .decoracao {
            background-color: #2458bf;
            width: 80px;
            height: 40px;
            display: flex;
            justify-content: center;
            border-radius: 50px;
            align-items: center;


        }

        .sobre {
            margin-top: 100px;
            background-color: white;
            width: 600px;
            height: 180px;
            position: absolute;
            top: 180px;
            left: 100px;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            border-radius: 40px;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.8);
        }

        p {
            color: black;
        }

        .texto-grande {
            font-size: 40px;
            margin-top: 70px;
            display: flex;
            text-align: center;
        }

        .texto-pequeno {
            font-size: 20px;
            margin-top: -30px;
            opacity: 0.6;
        }

        .botao {
            background-color: #2a5bd7;
            color: white;
            font-size: 20px;
            text-decoration: none;
            padding: 12px 24px;
            border-radius: 20px;
            border: none;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            margin-top: -5px;
        
        }

        .botao:hover {
            background-color: #1e4ac9;
      
        }
    </style>

</head>

<body>


    <navbar>


        <div class="logo">
            <img src="images/nexus-logo.png" alt="">
        </div>

        <div class="botoes">
            <a href="#">Sobre</a>
            <a href="#">Contato</a>
            <a class='decoracao' href="login">Entrar</a>
        </div>


    </navbar>

    <container>

        <div class="sobre">

            <p class="texto-grande">Administre seu negócio com o melhor ERP do mercado</p>
            <p class="texto-pequeno">+ de 5 mil usuários fiéis e satisfeitos</p>
            <a class="botao" href="forgot-password">Crie uma conta</a>
        </div>


    </container>



</body>

</html>


<?php //view('components/header'); 
?>



<?php //view('components/footer'); 
?>