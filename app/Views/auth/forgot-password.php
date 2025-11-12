<!doctype html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <link rel="icon" href="images/nexus-logo.png" type="image/png">
  <title>Recupere sua senha</title>

  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    html,
    body {
      height: 100%;
      background-color: #fff;
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
    }

    .logo {
      position: absolute;
      top: 20px;
      left: 20px;
    }

    .logo img {
      width: 50px;
    }

    .container {
      display: flex;
      height: 100vh;
      width: 100%;
    }

    .esquerda {
      flex: 1;
      background-color: #ffffff;
      color: #000000;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      padding: 60px;
      text-align: center;

    }

    .esquerda h1 {
      font-size: 40px;
      width: 300px;
      font-weight: 600;
      margin-bottom: 20px;
    }

    .esquerda p {
      font-size: 27 px;
      line-height: 1.5;
      max-width: 600px;
    }

    .direita {
      flex: 1;
      background-color: #050B1F;
      color: white;
      display: flex;
      align-items: center;
      justify-content: center;
      border-top-left-radius: 250px;
      border-bottom-left-radius: 250px;
      padding: 60px;
    }

    .direita form {
      width: 100%;
      max-width: 320px;
      display: flex;
      flex-direction: column;
      gap: 15px;
      align-items: center;
    }

    .direita h1 {
      font-size: 40px;
      font-weight: 600;
      margin-bottom: 20px;
      text-align: center;
    }

    input {
      padding: 12px 16px;
      border: 2px solid #ffffff;
      border-radius: 25px;
      background-color: transparent;
      color: #ffffff;
      font-size: 14px;
      width: 400px;
    }

    input::placeholder {
      color: #ccc;
    }

    button {
      padding: 10px;
      background-color: #2f62f3;
      border: none;
      border-radius: 25px;
      color: white;
      font-size: 15px;
      width: 160px;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }

    button:hover {
      background-color: #1e4bd1;
    }

    @media (max-width: 768px) {
      .container {
        flex-direction: column;
      }

      .direita,
      .esquerda {
        border-radius: 0;
        padding: 30px;
      }
    }
  </style>
</head>

<body>
  <div class="logo">
    <img src="images/nexus-logo.png" alt="Logo">
  </div>

  <div class="container">
    <div class="esquerda">
      <h1>Esqueceu sua senha?</h1>
      <p>Não se preocupe! Vamos te ajudar a recuperar seu acesso</p>
      <br>
      <p>Para recuperar sua senha, confirme sua identidade respondendo à pergunta de segurança cadastrada. Após a
        validação, sua senha será atualizada!</p>
    </div>

    <div class="direita">
      <form method="POST" action="/nexus-erp/public/forgot-password">
        <h1>Esqueci minha senha</h1>

        <input placeholder="E-mail" name="email" type="email">
        <input placeholder="Nova senha" name="nova_senha" type="password">
        <button>Redefinir senha</button>
      </form>
    </div>
  </div>
</body>

</html>