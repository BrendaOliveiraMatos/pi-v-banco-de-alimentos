<?php
session_start();
require_once 'includes/config.php';

if (!isset($_SESSION['logado'])) {
    header("Location: index.php");
    exit;
}

$mensagem = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST['nome'];
    $telefone = $_POST['telefone'];

    $sql = "INSERT INTO doadores (nome, telefone) VALUES ('$nome', '$telefone')";

    if ($conn->query($sql) === TRUE) {
        $mensagem = "<div class='alerta-sucesso'>Doador cadastrado com sucesso!</div>";
    } else {
        $mensagem = "<div class='alerta-erro'>Erro ao cadastrar: " . $conn->error . "</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Novo Doador - Mãos Amigas</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <aside class="sidebar">
        <div class="sidebar-logo">MA MÃOS AMIGAS</div>
        <nav class="sidebar-nav">
            <a href="dashboard.php">Início (Dashboard)</a>
            <a href="cadastro_produto.php">Produtos (Catálogo)</a>
            <a href="cadastro_doador.php" class="ativo">Novo Doador</a>
            <a href="entradas.php">Entradas</a>
            <a href="estoque.php">Estoque Geral</a>
            <a href="saidas.php">Saídas</a>
            <a href="relatorios.php">Relatórios</a>
            <a href="index.php" style="margin-top: auto; border-top: 1px solid #ccc;">Sair</a>
        </nav>
    </aside>

    <main class="main-content">
        <h1 class="main-header">Cadastrar Novo Doador</h1>
        
        <?php echo $mensagem; ?>

        <div class="form-container">
            <form method="POST" action="">
                <div class="linha-form">
                    <div class="grupo-form" style="flex: 2;">
                        <label>Nome do Doador ou Empresa</label>
                        <input type="text" name="nome" required placeholder="Ex: Supermercado Silva">
                    </div>
                    <div class="grupo-form">
                        <label>Telefone / WhatsApp</label>
                        <input type="text" name="telefone" placeholder="(00) 00000-0000">
                    </div>
                </div>
                <button type="submit" class="btn-primario">Salvar Doador</button>
            </form>
        </div>
    </main>

</body>
</html>