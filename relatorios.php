<?php
session_start();
require_once 'includes/config.php';

if (!isset($_SESSION['logado'])) {
    header("Location: index.php");
    exit;
}

$hoje = date('Y-m-d'); // Data atual no formato do banco de dados

// Busca apenas produtos cuja data de validade é MENOR que a data de hoje
$sql = "SELECT p.*, c.nome as categoria_nome 
        FROM produtos p 
        LEFT JOIN categorias c ON p.categoria_id = c.id 
        WHERE p.data_validade < '$hoje'
        ORDER BY p.data_validade ASC";
$resultado = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatórios - Mãos Amigas</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <aside class="sidebar">
        <div class="sidebar-logo">MA MÃOS AMIGAS</div>
        <nav class="sidebar-nav">
            <a href="dashboard.php">Início (Dashboard)</a>
            <a href="cadastro_produto.php">Produtos (Catálogo)</a>
            <a href="cadastro_doador.php">Novo Doador</a>
            <a href="entradas.php">Entradas</a>
            <a href="estoque.php">Estoque Geral</a>
            <a href="saidas.php">Saídas</a>
            <a href="relatorios.php" class="ativo">Relatórios</a>
            <a href="index.php" style="margin-top: auto; border-top: 1px solid #ccc;">Sair</a>
        </nav>
    </aside>

    <main class="main-content">
        <h1 class="main-header" style="color: var(--vermelho-alerta);">Atenção: Relatório de Produtos Vencidos (Descarte)</h1>
        <p style="margin-bottom: 20px; font-size: 0.95em; color: #555;">Abaixo estão os itens que não são mais próprios para consumo humano e devem ser descartados do estoque imediatamente.</p>

        <div class="tabela-box box-critico">
            <h3>Itens Vencidos</h3>
            <table>
                <thead>
                    <tr style="background-color: #f5b7b1;">
                        <th>Produto</th>
                        <th>Categoria</th>
                        <th>Quantidade</th>
                        <th>Data que Venceu</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($resultado->num_rows > 0) {
                        while ($produto = $resultado->fetch_assoc()) {
                            $data_br = date("d/m/Y", strtotime($produto['data_validade']));
                            echo "<tr>
                                    <td>{$produto['nome']}</td>
                                    <td>{$produto['categoria_nome']}</td>
                                    <td>{$produto['quantidade']} {$produto['unidade']}</td>
                                    <td style='color: var(--vermelho-alerta); font-weight: bold;'>{$data_br}</td>
                                  </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4' style='text-align:center; padding: 20px;'>Nenhum produto vencido encontrado. Ótimo trabalho de gestão!</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </main>

</body>
</html>