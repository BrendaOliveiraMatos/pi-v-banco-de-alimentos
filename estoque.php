<?php
session_start();
require_once 'includes/config.php';

if (!isset($_SESSION['logado'])) {
    header("Location: index.php");
    exit;
}

// Busca TODOS os produtos para a lista geral
$sql = "SELECT p.*, c.nome as categoria_nome 
        FROM produtos p 
        LEFT JOIN categorias c ON p.categoria_id = c.id 
        ORDER BY p.data_validade ASC";
$resultado = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estoque Geral - Mãos Amigas</title>
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
            <a href="estoque.php" class="ativo">Estoque Geral</a>
            <a href="saidas.php">Saídas</a>
            <a href="relatorios.php">Relatórios</a>
            <a href="index.php" style="margin-top: auto; border-top: 1px solid #ccc;">Sair</a>
        </nav>
    </aside>

    <main class="main-content">
        <h1 class="main-header">Estoque Geral - Lista de Produtos</h1>

        <div class="tabela-box" style="border: 1px solid var(--cinza-borda);">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Produto</th>
                        <th>Categoria</th>
                        <th>Quantidade</th>
                        <th>Validade</th>
                        <th>Status</th>
                        <th>Ação</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($resultado->num_rows > 0) {
                        $hoje = new DateTime();
                        
                        while ($item = $resultado->fetch_assoc()) {
                            $validade = new DateTime($item['data_validade']);
                            $diferenca = $hoje->diff($validade);
                            $dias = (int)$diferenca->format('%R%a');
                            
                            if ($dias < 0) {
                                $badge = "<span class='badge bg-vermelho'>VENCIDO</span>";
                            } elseif ($dias <= 30) {
                                $badge = "<span class='badge bg-amarelo'>ATENÇÃO ($dias dias)</span>";
                            } else {
                                $badge = "<span class='badge bg-cinza' style='background-color:#27ae60;'>NO PRAZO</span>";
                            }
                            
                            $data_br = date("d/m/Y", strtotime($item['data_validade']));
                            
                            echo "<tr>
                                    <td>#{$item['id']}</td>
                                    <td>{$item['nome']}</td>
                                    <td>{$item['categoria_nome']}</td>
                                    <td>{$item['quantidade']} {$item['unidade']}</td>
                                    <td>{$data_br}</td>
                                    <td>{$badge}</td>
                                    <td><button onclick='confirmarExclusao({$item['id']})' class='btn-perigo' style='padding:5px; font-size:0.8em;'>Remover</button></td>
                                  </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='7' style='text-align:center;'>Nenhum produto no estoque.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </main>
    <script src="js/main.js"></script>
</body>
</html>