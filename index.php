<?php
session_start();
require_once 'includes/config.php';

$erro = "";

// Verifica se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    $sql = "SELECT * FROM usuarios WHERE email = '$email' AND senha = '$senha'";
    $resultado = $conn->query($sql);

    if ($resultado->num_rows > 0) {
        $usuario = $resultado->fetch_assoc();
        $_SESSION['logado'] = true;
        $_SESSION['nome_usuario'] = $usuario['nome'];
        header("Location: dashboard.php");
        exit;
    } else {
        $erro = "E-mail ou senha incorretos!";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Lions Club</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="bg-login">

    <div class="login-box">
        <img src="img/logo.png" alt="Logo Lions Club" style="max-width: 90px; margin-bottom: 10px;">
        <div class="logo-login">LIONS CLUB</div>
        <p>Sistema de Gestão de Banco de Alimentos</p>
        
        <?php if($erro != "") { echo "<div class='alerta-erro'>$erro</div>"; } ?>
        
        <form method="POST" action="">
            <div class="grupo-form">
                <label for="email">E-mail de Acesso</label>
                <input type="email" name="email" id="email" required placeholder="admin@ong.com.br">
            </div>
            
            <div class="grupo-form" style="margin-top: 15px;">
                <label for="senha">Senha</label>
                <input type="password" name="senha" id="senha" required placeholder="123456">
            </div>
            
            <button type="submit" class="btn-primario">Entrar no Sistema</button>
        </form>
    </div>

</body>
</html>