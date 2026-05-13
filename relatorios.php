<?php
session_start();
require_once 'includes/config.php';

if (!isset($_SESSION['logado'])) {
    header("Location: index.php");
    exit;
}

$tipo = $_GET['tipo'] ?? 'vencidos';
$mes = $_GET['mes'] ?? date('m');
$ano = $_GET['ano'] ?? date('Y');
$doador_id = $_GET['doador_id'] ?? ''; // Novo parâmetro para o filtro individual

$titulo_relatorio = "";
$resultado = null;

// Busca todos os doadores para preencher a caixa de seleção (Apenas na aba de doadores)
$resultado_lista_doadores = null;
if ($tipo == 'doadores') {
    $sql_lista = "SELECT id, nome FROM doadores ORDER BY nome ASC";
    $resultado_lista_doadores = $conn->query($sql_lista);
}

// LÓGICA DE FILTRAGEM
switch ($tipo) {
    case 'vencidos':
        $titulo_relatorio = "Produtos Vencidos (Necessitam Descarte)";
        $hoje = date('Y-m-d');
        $sql = "SELECT p.*, c.nome as categoria_nome 
                FROM produtos p 
                LEFT JOIN categorias c ON p.categoria_id = c.id 
                WHERE p.data_validade < '$hoje'
                ORDER BY p.data_validade ASC";
        break;

    case 'doadores':
        // Se o usuário selecionou alguém específico, adicionamos a regra na consulta SQL
        $filtro_extra = "";
        if ($doador_id != '') {
            $filtro_extra = " AND d.id = '$doador_id'";
        }

        $titulo_relatorio = "Volume de Doações por Doador - Período: $mes/$ano";
        
        $sql = "SELECT d.nome as doador_nome, p.nome as produto_nome, p.unidade, SUM(p.quantidade) as total_doado
                FROM produtos p
                JOIN doadores d ON p.doador_id = d.id
                WHERE MONTH(p.data_cadastro) = '$mes' AND YEAR(p.data_cadastro) = '$ano' $filtro_extra
                GROUP BY d.id, p.nome
                ORDER BY d.nome ASC";
        break;

    case 'saidas':
        $titulo_relatorio = "Resumo de Saídas (Doações Realizadas) - Período: $mes/$ano";
        $sql = "SELECT p.nome as produto_nome, p.unidade, SUM(s.quantidade) as total_saida, COUNT(s.id) as entregas
                FROM saidas s
                JOIN produtos p ON s.produto_id = p.id
                WHERE MONTH(s.data_saida) = '$mes' AND YEAR(s.data_saida) = '$ano'
                GROUP BY p.nome
                ORDER BY total_saida DESC";
        break;
}

$resultado = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatórios Inteligentes - Lions Club</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .abas-container { display: flex; gap: 10px; margin-bottom: 25px; border-bottom: 2px solid var(--cinza-borda); padding-bottom: 10px; }
        .aba-link { text-decoration: none; padding: 10px 20px; border-radius: 8px; color: var(--azul-marinho); font-weight: 600; font-size: 0.9em; transition: 0.3s; }
        .aba-link:hover { background: rgba(0, 255, 255, 0.1); }
        .aba-link.ativa { background: var(--azul-marinho); color: white; }
        
        .filtros-box { background: #f1f4f8; padding: 20px; border-radius: 12px; margin-bottom: 30px; display: flex; align-items: flex-end; gap: 15px; }

        /* --- O SEGREDO PROFISSIONAL: ESTILOS APENAS PARA A IMPRESSORA/PDF --- */
        @media print {
            body { background-color: white !important; }
            /* Esconde o menu, as abas e os botões na hora de gerar o arquivo */
            .sidebar, .abas-container, .filtros-box, .btn-imprimir { display: none !important; }
            .main-content { padding: 0 !important; margin: 0 !important; }
            .tabela-box { box-shadow: none !important; border: 1px solid #000 !important; }
            table th { background-color: #eee !important; color: #000 !important; }
            h1.main-header { border-bottom: none !important; }
        }
    </style>
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
            <a href="saidas.php">Saídas</a>
            <a href="relatorios.php" class="ativo">Relatórios</a>
            <a href="index.php" style="margin-top: auto; border-top: 1px solid #ccc;">Sair</a>
        </nav>
    </aside>

    <main class="main-content">
        <h1 class="main-header">Relatórios de Gestão</h1>

        <div class="abas-container">
            <a href="?tipo=vencidos" class="aba-link <?php echo $tipo == 'vencidos' ? 'ativa' : ''; ?>">⚠️ Vencidos</a>
            <a href="?tipo=doadores" class="aba-link <?php echo $tipo == 'doadores' ? 'ativa' : ''; ?>">🤝 Doações por Doador</a>
            <a href="?tipo=saidas" class="aba-link <?php echo $tipo == 'saidas' ? 'ativa' : ''; ?>">📦 Saídas por Produto</a>
        </div>

        <?php if($tipo != 'vencidos'): ?>
        <form method="GET" class="filtros-box">
            <input type="hidden" name="tipo" value="<?php echo $tipo; ?>">
            
            <?php if($tipo == 'doadores'): ?>
            <div class="grupo-form" style="flex: 2;">
                <label>Filtrar por Doador Específico</label>
                <select name="doador_id">
                    <option value="">Todos os Doadores</option>
                    <?php
                    if ($resultado_lista_doadores && $resultado_lista_doadores->num_rows > 0) {
                        while ($d = $resultado_lista_doadores->fetch_assoc()) {
                            $selecionado = ($d['id'] == $doador_id) ? "selected" : "";
                            echo "<option value='{$d['id']}' $selecionado>{$d['nome']}</option>";
                        }
                    }
                    ?>
                </select>
            </div>
            <?php endif; ?>

            <div class="grupo-form">
                <label>Mês</label>
                <select name="mes">
                    <?php for($i=1; $i<=12; $i++) {
                        $sel = ($i == $mes) ? "selected" : "";
                        echo "<option value='".sprintf("%02d", $i)."' $sel>$i</option>";
                    } ?>
                </select>
            </div>
            <div class="grupo-form">
                <label>Ano</label>
                <select name="ano">
                    <option value="2025" <?php if($ano == "2025") echo "selected"; ?>>2025</option>
                    <option value="2026" <?php if($ano == "2026") echo "selected"; ?>>2026</option>
                </select>
            </div>
            <button type="submit" class="btn-primario" style="margin-top: 0;">Filtrar Dados</button>
        </form>
        <?php endif; ?>

        <div class="tabela-box">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h3 style="color: var(--azul-marinho); margin: 0; font-size: 1.1em;"><?php echo $titulo_relatorio; ?></h3>
                <button onclick="window.print()" class="btn-primario btn-imprimir" style="margin: 0; background-color: #27ae60;">🖨️ Salvar PDF / Imprimir</button>
            </div>

            <table>
                <thead>
                    <tr>
                        <?php if($tipo == 'vencidos'): ?>
                            <th>Produto</th><th>Categoria</th><th>Quantidade</th><th>Data Vencimento</th>
                        <?php elseif($tipo == 'doadores'): ?>
                            <th>Doador</th><th>Produto</th><th>Total Doado no Período</th>
                        <?php elseif($tipo == 'saidas'): ?>
                            <th>Produto</th><th>Total Saído</th><th>Qtd. de Entregas</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($resultado && $resultado->num_rows > 0) {
                        while ($row = $resultado->fetch_assoc()) {
                            echo "<tr>";
                            if($tipo == 'vencidos') {
                                $dt = date("d/m/Y", strtotime($row['data_validade']));
                                echo "<td>{$row['nome']}</td><td>{$row['categoria_nome']}</td><td>{$row['quantidade']} {$row['unidade']}</td><td style='color:red; font-weight:bold;'>$dt</td>";
                            } elseif($tipo == 'doadores') {
                                echo "<td>{$row['doador_nome']}</td><td>{$row['produto_nome']}</td><td style='font-weight: 600;'>{$row['total_doado']} {$row['unidade']}</td>";
                            } elseif($tipo == 'saidas') {
                                echo "<td>{$row['produto_nome']}</td><td>{$row['total_saida']} {$row['unidade']}</td><td>{$row['entregas']} atendimentos</td>";
                            }
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4' style='text-align:center; padding: 30px;'>Nenhum registro encontrado para este filtro.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </main>

</body>
</html>