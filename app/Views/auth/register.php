<!doctype html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" href="images/nexus-logo.png" type="image/png">
    <title>Cadastrar-se</title>
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
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
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
            flex: 0.4;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            border-right: 2px solid #000;
            border-top-right-radius: 250px;
            border-bottom-right-radius: 250px;
            background-color: white;
        }



        .caixa {
            display: flex;
            flex-direction: column;
        }

        input {
            border-radius: 30px;
            width: 450px;
            height: 2px;
            padding: 20px;
            margin: 10px;
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


        a {
            text-decoration: none;
            color: #2458bf;

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

        .cadastrar {
            text-align: center;
            margin-top: 20px;
        }

        .texto {
            font-size: 25px;

        }

        p {
            font-size: 30px;
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
                    <h1>Cadastro</h1>
                </div>
                <form class="caixa" method="POST" action="register">
                    <input type="text" name="name" placeholder="Nome">
                    <input type="text" name="business_name" placeholder="Razão Social">
                    <input type="text" name="email" placeholder="E-mail">
                    <input type="password" name="password" placeholder="Senha">
                    <input type="text" name="document_number" placeholder="CNPJ">
                    <input type="number" name="phone" placeholder="Telefone">
                    <button type="submit">Cadastrar</button>
                </form>

                <div class="cadastrar">
                    <p>Já tem um cadastro?</p>
                    <p class="texto"><a href="login">Clique aqui para entrar no seu sistema</a></p>
                </div>
            </div>
        </div>

    </div>
    </div>
</body>

</html>