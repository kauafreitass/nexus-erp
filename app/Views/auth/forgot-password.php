<?php

?>

<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
      <link rel="icon" href="images/nexus-logo.png" type="image/png">
    <title>Recupere sua senha</title>
</head>
<body>
  <div class="logo">
    <img src="images/nexus-logo.png" alt="Logo">
  </div>
  <div class="container">

    <div class="esquerda">

      <h1>Esqueceu sua senha</h1>
      <p>
        Não se preocupe! Vamos te ajudar a recuperar seu acesso
      </p>
      <p>Para recuperar sua senha, confirme sua identidade respondendo à pergunta de segurança cadastrada. Após a validação, sua senha será atualizada!</p>
    </div>
    <div class="direita">
      <form method="POST">
        <h1>Esqueci minha senha</h1>

        <input type="text" placeholder="E-mail">
        <input type="password" placeholder="Senha">
        <p class="esqueci-senha"><a href="forgot-password">Esqueci minha senha</a></p>

        <button type="submit">Redefinir senha</button>
      </form>

    </div>


  </div>
</body>
</html>
