<?php
session_start();

if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    header('Location: ../views/view_login.php'); // Redireciona para o login
    exit;
}
require "db_connect.php"; 

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: view_crud_salas.php');
    exit;
}

$nome_sala = trim($_POST['nome_sala'] ?? ''); 

if (empty($nome_sala)) {
    $erro = "O nome da sala é obrigatório.";
    header('Location: view_crud_salas.php?erro=' . urlencode($erro));
    exit;
}

$queryInsert = "INSERT INTO sala (nome) VALUES (:nome)";

try {
    $stmt = $pdo->prepare($queryInsert);
    $stmt->bindValue(':nome', $nome_sala, PDO::PARAM_STR);
    $stmt->execute();
    
    $sucesso = "Sala **(" . htmlspecialchars($nome_sala) . ")** criada com sucesso!";
    header('Location: view_crud_salas.php?sucesso=' . urlencode($sucesso));
    exit;
    
} catch (PDOException $e) {
    if ($e->getCode() === '23000') { 

        $erro = "Erro: Já existe uma sala com o nome '" . htmlspecialchars($nome_sala) . "'.";
    } else {
        error_log("Erro ao criar sala: " . $e->getMessage());
        $erro = "Erro interno. Tente novamente.";
    }
    header('Location: view_crud_salas.php?erro=' . urlencode($erro));
    exit;
}
?>