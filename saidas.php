<?php
session_start();
require_once 'includes/config.php';

if (!isset($_SESSION['logado'])) {
    header("Location: index.php");
    exit;
}

$mensagem = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $produto_id = $_POST['produto_id'];
    $quantidade_saida = $_POST['quantidade'];
    $beneficiario = $_POST['beneficiario'];

    // 1. Verifica se a ONG tem essa quantidade no estoque
    $sql_verifica = "SELECT quantidade, nome, unidade FROM produtos WHERE id = '$produto_id'";
    $produto = $conn->query($sql_verifica)->fetch_assoc();

    if ($produto['quantidade'] < $quantidade_saida) {
        $mensagem = "<div class='alerta-erro'>Erro: O estoque atual de {$produto['nome']} é de apenas {$produto['quantidade']} {$produto['unidade']}.</div>";
    } else {
        // 2. Registra a saída no histórico
        $sql_insert = "INSERT INTO saidas (produto_id, quantidade, beneficiario) VALUES ('$produto_id', '$quantidade_saida', '$beneficiario')";
        
        if ($conn->query($sql_insert) === TRUE) {
            // 3. Atualiza (subtrai) o estoque na tabela de produtos
            $nova_quantidade = $produto['quantidade'] - $quantidade_saida;
            $sql_update = "UPDATE produtos SET quantidade = '$nova_quantidade' WHERE id = '$produto_id'";
            $conn->query($sql_update);

            $mensagem = "<div class='alerta-sucesso'>Saída registrada com sucesso! O estoque foi atualizado.</div>";
        } else {
            $mensagem = "<div class='alerta-erro'>Erro: " . $conn->error . "</div>";
        }
    }
}

// Busca os produtos que têm mais de 0 no estoque para popular o select
$sql_produtos = "SELECT id, nome, quantidade, unidade FROM produtos WHERE quantidade > 0 ORDER BY nome ASC";
$resultado_produtos = $conn->query($sql_produtos);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Saída - Mãos Amigas</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <aside class="sidebar">
        <div class="sidebar-logo">
            <img src="img/logo.png" alt="Logo Lions Club">
            <div class="sidebar-logo-text">LIONS<br>CLUB</div>
        </div>
        <nav class="sidebar-nav">
            <a href="dashboard.php">Início (Dashboard)</a>
            <a href="cadastro_produto.php">Produtos (Catálogo)</a>
            <a href="cadastro_doador.php">Novo Doador</a>
            <a href="entradas.php">Entradas</a>
            <a href="estoque.php">Estoque Geral</a>
            <a href="saidas.php" class="ativo">Saídas</a>
            <a href="relatorios.php">Relatórios</a>
            <a href="index.php" style="margin-top: auto; border-top: 1px solid #ccc;">Sair</a>
        </nav>
    </aside>

    <main class="main-content">
        <h1 class="main-header">Registrar Saída / Doação</h1>
        
        <?php echo $mensagem; ?>

        <div class="form-container">
            <form method="POST" action="">
                
                <div class="linha-form">
                    <div class="grupo-form" style="flex: 2;">
                        <label>Selecione o Produto em Estoque</label>
                        <select name="produto_id" required>
                            <option value="">Selecione...</option>
                            <?php
                            while($prod = $resultado_produtos->fetch_assoc()) {
                                echo "<option value='{$prod['id']}'>{$prod['nome']} (Disponível: {$prod['quantidade']} {$prod['unidade']})</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <div class="linha-form">
                    <div class="grupo-form">
                        <label>Quantidade Doada</label>
                        <input type="number" name="quantidade" min="1" required placeholder="Ex: 5">
                    </div>
                    <div class="grupo-form" style="flex: 2;">
                        <label>Beneficiário (Família ou Instituição)</label>
                        <input type="text" name="beneficiario" required placeholder="Ex: Família Silva">
                    </div>
                </div>

                <button type="submit" class="btn-primario">Registrar Saída e Baixar Estoque</button>
            </form>
        </div>
    </main>

</body>
</html>