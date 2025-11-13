<?php
session_start();

if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    header('Location: ../views/view_login.php'); // Redireciona para o login
    exit;
}
require "db_connect.php"; 

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../views/view_cria_usuario.php');
    exit;
}

$cpf = trim($_POST['cpf'] ?? '');
$nome = trim($_POST['nome'] ?? '');
$senha_crua = $_POST['senha'] ?? null; 

if (empty($cpf) || empty($nome) || empty($senha_crua)) {
    $erro = "Todos os campos são obrigatórios.";
    header('Location: ../views/view_cria_usuario.php?erro=' . urlencode($erro));
    exit;
}

$senha_hash = password_hash($senha_crua, PASSWORD_DEFAULT); 

$queryInsert = "INSERT INTO funcionario 
                (cpf, nome, senha) 
                VALUES 
                (:cpf, :nome, :senha_hash)";

try {
    $stmt = $pdo->prepare($queryInsert);

    $stmt->bindValue(':cpf', $cpf, PDO::PARAM_STR); 
    $stmt->bindValue(':nome', $nome, PDO::PARAM_STR);
    $stmt->bindValue(':senha_hash', $senha_hash, PDO::PARAM_STR);
    
    $stmt->execute();
    
    header('Location: ../views/view_cria_usuario.php?sucesso=' . urlencode($cpf));
    exit;
    
} catch (PDOException $e) {
    if ($e->getCode() === '23000') { 
        $erro = "Erro: Já existe um funcionário com este CPF.";
    } else {
        error_log("Erro ao criar funcionário: " . $e->getMessage());
        $erro = "Erro interno ao tentar cadastrar funcionário.";
    }
    header('Location: ../views/view_cria_usuario.php?erro=' . urlencode($erro));
    exit;
}
?>