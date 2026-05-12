<?php
// Configurações do Banco de Dados
$servidor = "localhost"; // O seu próprio computador
$usuario_bd = "root";    // Usuário padrão do XAMPP
$senha_bd = "";          // Senha padrão do XAMPP é vazia
$banco = "banco_alimentos"; // O banco que criamos no Passo 3

// Tenta realizar a conexão
$conn = new mysqli($servidor, $usuario_bd, $senha_bd, $banco);

// Verifica se houve algum erro
if ($conn->connect_error) {
    die("Falha na conexão com o banco de dados: " . $conn->connect_error);
}

// Configura o sistema para aceitar acentuação corretamente
$conn->set_charset("utf8");
?>