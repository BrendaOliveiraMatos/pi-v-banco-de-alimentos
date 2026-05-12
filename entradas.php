<?php
session_start();
require_once 'includes/config.php';

if (!isset($_SESSION['logado'])) {
    header("Location: index.php");
    exit;
}

$mensagem = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $catalogo_id = $_POST['catalogo_id'];
    $quantidade_pacotes = $_POST['quantidade']; // Agora representa o número de pacotes
    $data_validade = $_POST['data_validade'];
    $doador_id = $_POST['doador_id'];

    // 1. Busca as regras do catálogo (Incluindo o Peso)
    $sql_cat = "SELECT nome, categoria_id, unidade, peso FROM produtos_catalogo WHERE id = '$catalogo_id'";
    $produto_cat = $conn->query($sql_cat)->fetch_assoc();

    $nome = $produto_cat['nome'];
    $categoria_id = $produto_cat['categoria_id'];
    
    // 2. A MÁGICA VISUAL: Junta as palavras. Ex: "pct(s) de 5 Kg"
    $unidade_formatada = "pct(s) de " . $produto_cat['peso'] . " " . $produto_cat['unidade'];

    // 3. Salva no estoque
    $sql_insert = "INSERT INTO produtos (nome, quantidade, unidade, data_validade, categoria_id, doador_id) 
                   VALUES ('$nome', '$quantidade_pacotes', '$unidade_formatada', '$data_validade', '$categoria_id', '$doador_id')";

    if ($conn->query($sql_insert) === TRUE) {
        $mensagem = "<div class='alerta-sucesso'>Entrada de estoque registrada com sucesso!</div>";
    } else {
        $mensagem = "<div class='alerta-erro'>Erro ao dar entrada: " . $conn->error . "</div>";
    }
}

$sql_catalogo = "SELECT * FROM produtos_catalogo ORDER BY nome ASC";
$resultado_catalogo = $conn->query($sql_catalogo);

$sql_doadores = "SELECT * FROM doadores ORDER BY nome ASC";
$resultado_doadores = $conn->query($sql_doadores);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entradas de Estoque - Mãos Amigas</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <aside class="sidebar">
        <div class="sidebar-logo">MA MÃOS AMIGAS</div>
        <nav class="sidebar-nav">
            <a href="dashboard.php">Início (Dashboard)</a>
            <a href="cadastro_produto.php">Produtos (Catálogo)</a>
            <a href="cadastro_doador.php">Novo Doador</a>
            <a href="entradas.php" class="ativo">Entradas</a>
            <a href="estoque.php">Estoque Geral</a>
            <a href="saidas.php">Saídas</a>
            <a href="relatorios.php">Relatórios</a>
            <a href="index.php" style="margin-top: auto; border-top: 1px solid #ccc;">Sair</a>
        </nav>
    </aside>

    <main class="main-content">
        <h1 class="main-header">Registrar Entrada de Doação no Estoque</h1>
        
        <?php echo $mensagem; ?>

        <div class="form-container">
            <form method="POST" action="">
                
                <div class="linha-form">
                    <div class="grupo-form" style="flex: 2;">
                        <label>Qual produto está entrando?</label>
                        <select name="catalogo_id" required>
                            <option value="">Selecione no Catálogo...</option>
                            <?php
                            if ($resultado_catalogo->num_rows > 0) {
                                while($item = $resultado_catalogo->fetch_assoc()) {
                                    // Mostra no dropdown qual é o peso cadastrado para ajudar o usuário
                                    echo "<option value='{$item['id']}'>{$item['nome']} (Pacotes de {$item['peso']} {$item['unidade']})</option>";
                                }
                            } else {
                                echo "<option value=''>Nenhum produto no catálogo. Cadastre um primeiro.</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="grupo-form" style="flex: 2;">
                        <label>Quem doou?</label>
                        <select name="doador_id" required>
                            <option value="">Selecione o Doador...</option>
                            <?php
                            if ($resultado_doadores->num_rows > 0) {
                                while($doador = $resultado_doadores->fetch_assoc()) {
                                    echo "<option value='{$doador['id']}'>{$doador['nome']}</option>";
                                }
                            } else {
                                echo "<option value=''>Nenhum doador cadastrado.</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <div class="linha-form">
                    <div class="grupo-form">
                        <label>Número de Pacotes / Volumes recebidos</label>
                        <input type="number" name="quantidade" min="1" required placeholder="Ex: 10 pacotes">
                    </div>
                    <div class="grupo-form">
                        <label>Data de Validade deste lote</label>
                        <input type="date" name="data_validade" required id="data_validade">
                    </div>
                </div>

                <button type="submit" class="btn-primario">Confirmar Entrada no Estoque</button>
            </form>
        </div>
    </main>

    <script src="js/main.js"></script>
</body>
</html>