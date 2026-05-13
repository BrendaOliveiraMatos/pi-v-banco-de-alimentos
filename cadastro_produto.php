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
    $peso = $_POST['peso']; // Novo campo de peso
    $unidade = $_POST['unidade'];
    $categoria_id = $_POST['categoria_id'];

    $sql = "INSERT INTO produtos_catalogo (nome, peso, unidade, categoria_id) 
            VALUES ('$nome', '$peso', '$unidade', '$categoria_id')";

    if ($conn->query($sql) === TRUE) {
        $mensagem = "<div class='alerta-sucesso'>Produto adicionado ao catálogo com sucesso! Agora você pode dar entrada nele.</div>";
    } else {
        $mensagem = "<div class='alerta-erro'>Erro ao cadastrar: " . $conn->error . "</div>";
    }
}

// Busca categorias
$sql_categorias = "SELECT * FROM categorias";
$resultado_categorias = $conn->query($sql_categorias);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catálogo de Produtos - Mãos Amigas</title>
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
            <a href="cadastro_produto.php" class="ativo">Produtos (Catálogo)</a>
            <a href="cadastro_doador.php">Novo Doador</a>
            <a href="entradas.php">Entradas</a>
            <a href="estoque.php">Estoque Geral</a>
            <a href="saidas.php">Saídas</a>
            <a href="relatorios.php">Relatórios</a>
            <a href="index.php" style="margin-top: auto; border-top: 1px solid #ccc;">Sair</a>
        </nav>
    </aside>

    <main class="main-content">
        <h1 class="main-header">Cadastrar Produto no Catálogo</h1>
        <p style="margin-bottom: 20px; color: #555;">Defina aqui a ficha do produto (Ex: Arroz Branco, 5, Kg).</p>
        
        <?php echo $mensagem; ?>

        <div class="form-container">
            <form method="POST" action="">
                <div class="linha-form">
                    <div class="grupo-form" style="flex: 2;">
                        <label>Nome do Produto</label>
                        <input type="text" name="nome" required placeholder="Ex: Arroz Branco">
                    </div>
                    <div class="grupo-form">
                        <label>Categoria</label>
                        <select name="categoria_id" required>
                            <option value="">Selecione...</option>
                            <?php
                            while($cat = $resultado_categorias->fetch_assoc()) {
                                echo "<option value='{$cat['id']}'>{$cat['nome']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <div class="linha-form">
                    <div class="grupo-form" style="flex: 1;">
                        <label>Peso / Tamanho do Pacote</label>
                        <input type="number" step="0.01" name="peso" required placeholder="Ex: 5">
                    </div>
                    <div class="grupo-form" style="flex: 1;">
                        <label>Unidade de Medida</label>
                        <select name="unidade" required>
                            <option value="Kg">Quilogramas (Kg)</option>
                            <option value="Gramas">Gramas (g)</option>
                            <option value="Litros">Litros (L)</option>
                            <option value="Unidades">Unidades (Un)</option>
                        </select>
                    </div>
                </div>

                <button type="submit" class="btn-primario">Salvar no Catálogo</button>
            </form>
        </div>
    </main>

</body>
</html>