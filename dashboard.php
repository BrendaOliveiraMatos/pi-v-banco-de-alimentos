<?php
session_start();
require_once 'includes/config.php';

if (!isset($_SESSION['logado'])) {
    header("Location: index.php");
    exit;
}

// 1. DADOS EXATOS (KPIs)
// Total de volumes/pacotes físicos no estoque
$sql_total = "SELECT SUM(quantidade) as total_itens FROM produtos";
$total_itens = $conn->query($sql_total)->fetch_assoc()['total_itens'] ?? 0;

// CORREÇÃO: Conta o NÚMERO DE DOAÇÕES (entradas) e não a soma dos quilos
$mes_atual = date('m');
$ano_atual = date('Y');
$sql_entradas = "SELECT COUNT(id) as entradas_mes FROM produtos WHERE MONTH(data_cadastro) = '$mes_atual' AND YEAR(data_cadastro) = '$ano_atual'";
$entradas_mes = $conn->query($sql_entradas)->fetch_assoc()['entradas_mes'] ?? 0;

// Total de saídas realizadas no MÊS ATUAL
$sql_saidas = "SELECT SUM(quantidade) as saidas_mes FROM saidas WHERE MONTH(data_saida) = '$mes_atual' AND YEAR(data_saida) = '$ano_atual'";
$saidas_mes = $conn->query($sql_saidas)->fetch_assoc()['saidas_mes'] ?? 0;

// 2. LÓGICA DE DATAS (Alertas)
$hoje = date('Y-m-d');
$daqui_15_dias = date('Y-m-d', strtotime('+15 days'));
$daqui_30_dias = date('Y-m-d', strtotime('+30 days'));

$sql_critica = "SELECT p.*, c.nome as categoria_nome FROM produtos p LEFT JOIN categorias c ON p.categoria_id = c.id WHERE p.data_validade >= '$hoje' AND p.data_validade <= '$daqui_15_dias' ORDER BY p.data_validade ASC";
$resultado_critica = $conn->query($sql_critica);

$sql_media = "SELECT p.*, c.nome as categoria_nome FROM produtos p LEFT JOIN categorias c ON p.categoria_id = c.id WHERE p.data_validade > '$daqui_15_dias' AND p.data_validade <= '$daqui_30_dias' ORDER BY p.data_validade ASC";
$resultado_media = $conn->query($sql_media);

// 3. CONSULTAS PARA O FEED LATERAL
$sql_doadores = "SELECT nome FROM doadores ORDER BY id DESC LIMIT 3";
$resultado_doadores = $conn->query($sql_doadores);

$sql_recentes = "SELECT nome, quantidade, unidade FROM produtos ORDER BY id DESC LIMIT 3";
$resultado_recentes = $conn->query($sql_recentes);

$sql_recentes_saidas = "SELECT s.quantidade, s.beneficiario, p.nome, p.unidade 
                        FROM saidas s 
                        JOIN produtos p ON s.produto_id = p.id 
                        ORDER BY s.id DESC LIMIT 3";
$resultado_saidas = $conn->query($sql_recentes_saidas);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Mãos Amigas</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <aside class="sidebar">
        <div class="sidebar-logo">MA MÃOS AMIGAS</div>
        <nav class="sidebar-nav">
            <a href="dashboard.php" class="ativo">Início (Dashboard)</a>
            <a href="cadastro_produto.php">Produtos (Catálogo)</a>
            <a href="cadastro_doador.php">Novo Doador</a>
            <a href="entradas.php">Entradas</a>
            <a href="estoque.php">Estoque Geral</a>
            <a href="saidas.php">Saídas</a>
            <a href="relatorios.php">Relatórios</a>
            <a href="index.php" style="margin-top: auto; border-top: 1px solid #ccc;">Sair</a>
        </nav>
    </aside>

    <main class="main-content">
        <h1 class="main-header">Painel de Controle - Visão Geral</h1>

        <div class="kpi-row">
            <div class="kpi-card">
                <h3>Total de Pacotes no Estoque:</h3>
                <div class="valor"><?php echo $total_itens; ?></div>
            </div>
            <div class="kpi-card">
                <h3>Doações Recebidas (Mês):</h3>
                <div class="valor"><?php echo $entradas_mes; ?></div>
            </div>
            <div class="kpi-card">
                <h3>Pacotes Doados (Mês):</h3>
                <div class="valor"><?php echo $saidas_mes; ?></div>
            </div>
        </div>

        <div class="dashboard-grid">
            <div class="tabelas-section">
                <div class="tabela-box box-critico">
                    <h3>Alertas de Validade Crítica (Próximos 15 dias)</h3>
                    <table>
                        <thead>
                            <tr style="background-color: #f5b7b1;">
                                <th>Produto</th><th>Categoria</th><th>Data Venc.</th><th>Qtd.</th><th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($resultado_critica->num_rows > 0) {
                                while ($item = $resultado_critica->fetch_assoc()) {
                                    $data_br = date("d/m/Y", strtotime($item['data_validade']));
                                    echo "<tr><td>{$item['nome']}</td><td>{$item['categoria_nome']}</td><td>{$data_br}</td><td>{$item['quantidade']} {$item['unidade']}</td><td><span class='badge bg-vermelho'>VENCENDO LOGO</span></td></tr>";
                                }
                            } else {
                                echo "<tr><td colspan='5' style='text-align:center;'>Nenhum item em estado crítico.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

                <div class="tabela-box box-medio">
                    <h3>Avisos de Vencimento Médio (Próximos 30 dias)</h3>
                    <table>
                        <thead>
                            <tr style="background-color: #fcf3cf;">
                                <th>Produto</th><th>Categoria</th><th>Data Venc.</th><th>Qtd.</th><th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($resultado_media->num_rows > 0) {
                                while ($item = $resultado_media->fetch_assoc()) {
                                    $data_br = date("d/m/Y", strtotime($item['data_validade']));
                                    echo "<tr><td>{$item['nome']}</td><td>{$item['categoria_nome']}</td><td>{$data_br}</td><td>{$item['quantidade']} {$item['unidade']}</td><td><span class='badge bg-amarelo'>ATENÇÃO</span></td></tr>";
                                }
                            } else {
                                echo "<tr><td colspan='5' style='text-align:center;'>Nenhum item com vencimento médio.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="feed-section">
                <div class="feed-box">
                    <h4>Doadores Recentes</h4>
                    <?php
                    if ($resultado_doadores->num_rows > 0) {
                        while ($doador = $resultado_doadores->fetch_assoc()) {
                            echo "<div class='feed-item'>👤 {$doador['nome']}</div>";
                        }
                    } else {
                        echo "<div class='feed-item'>Nenhum doador cadastrado ainda.</div>";
                    }
                    ?>
                </div>

                <div class="feed-box">
                    <h4>Últimas Entradas</h4>
                    <?php
                    if ($resultado_recentes->num_rows > 0) {
                        while ($recente = $resultado_recentes->fetch_assoc()) {
                            echo "<div class='feed-item'>+ {$recente['quantidade']} {$recente['unidade']} de {$recente['nome']}</div>";
                        }
                    } else {
                        echo "<div class='feed-item'>Sem entradas recentes.</div>";
                    }
                    ?>
                </div>
                
                <div class="feed-box">
                    <h4>Últimas Saídas</h4>
                    <?php
                    if ($resultado_saidas->num_rows > 0) {
                        while ($saida = $resultado_saidas->fetch_assoc()) {
                            echo "<div class='feed-item' style='color: var(--vermelho-alerta); font-weight: 600;'>- {$saida['quantidade']} {$saida['unidade']} de {$saida['nome']}<br><span style='color: #777; font-size: 0.9em; font-weight: normal;'>Para: {$saida['beneficiario']}</span></div>";
                        }
                    } else {
                        echo "<div class='feed-item'>Nenhuma saída registrada recentemente.</div>";
                    }
                    ?>
                </div>
            </div>
        </div>
    </main>
</body>
</html>