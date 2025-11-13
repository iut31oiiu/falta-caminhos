<?php
session_start();

if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    header('Location: ../views/view_login.php'); // Redireciona para o login
    exit;
}
require "db_connect.php"; 

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: controller_lista_patrimonio.php');
    exit;
}

$codigo = $_POST['codigo'] ?? null;
$tipo = trim($_POST['tipo'] ?? '');
$marca = trim($_POST['marca'] ?? '');
$ano = $_POST['ano'] ?? null;
$quantidade = $_POST['quantidade'] ?? null;
$sala_de_origem = trim($_POST['sala_de_origem'] ?? '');
$nome_sala = trim($_POST['nome_sala'] ?? '');

if (empty($codigo) || empty($tipo) || empty($quantidade) || empty($sala_de_origem) || empty($nome_sala)) {
    $erro = "Erro: Dados obrigatórios incompletos.";
    header('Location: controller_lista_patrimonio.php?erro_edicao=' . urlencode($erro));
    exit;
}

$queryUpdate = "UPDATE patrimonio SET 
                tipo = :tipo, 
                marca = :marca, 
                ano = :ano, 
                quantidade = :quantidade, 
                sala_de_origem = :sala_de_origem, 
                nome_sala = :nome_sala
                WHERE codigo = :codigo";

try {
    $stmt = $pdo->prepare($queryUpdate);

    $stmt->bindValue(':tipo', $tipo, PDO::PARAM_STR);
    $stmt->bindValue(':marca', $marca, PDO::PARAM_STR);
    $stmt->bindValue(':ano', is_numeric($ano) ? $ano : null, PDO::PARAM_INT); 
    $stmt->bindValue(':quantidade', $quantidade, PDO::PARAM_INT);
    $stmt->bindValue(':sala_de_origem', $sala_de_origem, PDO::PARAM_STR);
    $stmt->bindValue(':nome_sala', $nome_sala, PDO::PARAM_STR);
    $stmt->bindValue(':codigo', $codigo, PDO::PARAM_INT);
    
    $stmt->execute();
    
    $mensagemSucesso = "Patrimônio (Cód. " . htmlspecialchars($codigo) . ") editado com sucesso.";
    header('Location: controller_lista_patrimonio.php?aviso_edicao=' . urlencode($mensagemSucesso));
    exit;
    
} catch (PDOException $e) {
    error_log("Erro ao editar patrimônio: " . $e->getMessage());
    $erro = "Erro interno ao tentar salvar edição. Tente novamente mais tarde.";
    header('Location: controller_lista_patrimonio.php?erro_edicao=' . urlencode($erro));
    exit;
}
?>