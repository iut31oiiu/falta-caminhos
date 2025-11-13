<?php
session_start();

if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    header('Location: ../views/view_login.php'); // Redireciona para o login
    exit;
}
require "db_connect.php"; 

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$codigo = $_POST['codigo'] ?? null;
$salaOrigem = $_POST['sala_origem'] ?? null;
$novaSala = $_POST['nova_sala'] ?? null;

if (!$codigo || !$salaOrigem || !$novaSala) {
    $erro = "Erro: Dados incompletos para mover o patrimônio.";
    header('Location: controller_lista_sala.php?nome_sala=' . urlencode($salaOrigem) . '&erro_movimento=' . urlencode($erro));
    exit;
}

$queryUpdate = "UPDATE patrimonio SET nome_sala = :nova_sala WHERE codigo = :codigo";

try {
    $stmt = $pdo->prepare($queryUpdate);
    $stmt->bindValue(':nova_sala', $novaSala, PDO::PARAM_STR);
    $stmt->bindValue(':codigo', $codigo, PDO::PARAM_INT);
    $stmt->execute();
    
    $mensagemSucesso = "Patrimônio (Cód. " . htmlspecialchars($codigo) . ") movido da sala " . htmlspecialchars($salaOrigem) . " para " . htmlspecialchars($novaSala) . " com sucesso.";
    
    $redirectURL = "controller_lista_sala.php?nome_sala=" . urlencode($salaOrigem) . 
                   "&aviso_movimento=" . urlencode($mensagemSucesso);
    
    header('Location: ' . $redirectURL);
    exit;
    
} catch (PDOException $e) {
    error_log("Erro ao mover patrimônio: " . $e->getMessage());
    $erro = "Erro interno ao tentar mover patrimônio.";
    header('Location: controller_lista_sala.php?nome_sala=' . urlencode($salaOrigem) . '&erro_movimento=' . urlencode($erro));
    exit;
}
?>