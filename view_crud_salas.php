<?php
require '../principal/db_connect.php';

$mensagemSucesso = $_GET['sucesso'] ?? null;
$mensagemErro = $_GET['erro'] ?? null;

$querySalas = "SELECT nome FROM sala ORDER BY nome ASC"; 
$resultSalas = [];

try {
    $stmt = $pdo->query($querySalas);
    $resultSalas = $stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    $mensagemErro = "Erro ao listar salas: " . $e->getMessage();
}

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/style.css">
    <title>Gerenciar Salas</title>
</head>
<body>
    <section class="principal">
        <div style="text-align: right; margin-bottom: 20px; font-size: 0.9em;">
            <span style="color: #666;">Usuário: <?= htmlspecialchars($_SESSION['username'] ?? 'N/A') ?></span> |
            <a href="../principal/index.php" class="btn-voltar-index">Voltar para Salas</a> |
            <a href="../login_logout/logout.php" class="btn-deletar">Sair</a>
        </div>

        <h1>Gerenciar Salas</h1>
        
        <?php if ($mensagemSucesso): ?>
            <div class="alerta-patrimonio-movido" style="background-color: #d4edda; border-color: #c3e6cb; color: #155724;">
                <?= htmlspecialchars($mensagemSucesso) ?>
                <span class="fechar" onclick="this.parentElement.style.display='none';">x</span>
            </div>
        <?php endif; ?>
        
        <?php if ($mensagemErro): ?>
            <div class="alerta-erro">
                <?= htmlspecialchars($mensagemErro) ?>
                <span class="fechar" onclick="this.parentElement.style.display='none';">x</span>
            </div>
        <?php endif; ?>

        <h2>Criar Nova Sala</h2>
        <form method="POST" action="../principal/controller_cria_sala.php" class="form-crud">
            <label for="nome_sala">Nome da Sala:</label>
            <input type="text" id="nome_sala" name="nome_sala" required maxlength="100">
            
            <button type="submit" class="btn-filtrar" style="width: 100%; margin-top: 10px;">
                Criar Sala
            </button>
        </form>

        <div class="lista-salas" style="margin-top: 50px;">
            <h2>Salas Cadastradas (Exclusão)</h2>
            <?php if (empty($resultSalas)): ?>
                <p>Nenhuma sala cadastrada.</p>
            <?php else: ?>
                <?php foreach ($resultSalas as $sala): ?>
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 1px dashed #ddd;">
                        <span><?= htmlspecialchars($sala) ?></span>
                        <a href="../principal/controller_deleta_sala.php?nome_sala=<?= urlencode($sala) ?>" 
                           class="btn-deletar"
                           onclick="return confirm('ATENÇÃO: A sala só será excluída se estiver vazia (sem patrimônio). Deseja prosseguir com a exclusão de <?= htmlspecialchars($sala) ?>?');"
                           style="padding: 5px 10px;">
                            Excluir Sala
                        </a>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>
</body>
</html>