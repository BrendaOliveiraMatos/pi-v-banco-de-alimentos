<?php
session_start();
require_once 'includes/config.php';

if (!isset($_SESSION['logado'])) {
    header("Location: index.php");
    exit;
}

// Verifica se recebeu um ID pela URL (ex: excluir_produto.php?id=5)
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Deleta o produto onde o ID for igual ao enviado
    $sql = "DELETE FROM produtos WHERE id = $id";
    $conn->query($sql);
}

// Retorna para o dashboard
header("Location: dashboard.php");
exit;
?>