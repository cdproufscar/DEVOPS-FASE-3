<?php
session_start();
require 'conexao.php';

if (!isset($_GET['id'])) {
    die("Erro: Produto não encontrado.");
}

$produto_id = $_GET['id'];

// Consulta principal do produto
$sql = "SELECT p.id_produto, p.nome_produto, p.descricao, p.para_quem, p.por_quem, p.por_que, p.para_que, 
               p.pre_requisitos, p.modo_de_uso, p.imagens, p.arquivos, u.nome AS nome_usuario
        FROM produtos p
        JOIN usuarios u ON p.id_usuario = u.id_usuario
        WHERE p.id_produto = :id";

$stmt = $pdo->prepare($sql);
$stmt->bindParam(':id', $produto_id, PDO::PARAM_INT);
$stmt->execute();
$produto = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$produto) {
    die("Produto não encontrado.");
}

$imagens = json_decode($produto['imagens'], true) ?? [];
$arquivos = json_decode($produto['arquivos'], true) ?? [];
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($produto['nome_produto']) ?> - Detalhes</title>
  <link rel="stylesheet" href="css/global.css">
  <link rel="stylesheet" href="css/produto_detalhado.css">
</head>
<body>

<?php include 'header_dinamico.php'; ?>

<main class="produto-container">
  <button onclick="window.history.back();" class="btn-voltar">← Voltar</button>

  <h1 class="produto-nome"><?= htmlspecialchars($produto['nome_produto']) ?></h1>

  <div class="produto-detalhes">
    <div class="produto-imagens">
      <?php if (!empty($imagens)): ?>
        <?php foreach ($imagens as $img): 
          $img = file_exists($img) ? $img : 'img/sem-imagem.png';
        ?>
          <img src="<?= htmlspecialchars($img) ?>" class="produto-img" alt="Imagem">
        <?php endforeach; ?>
      <?php else: ?>
        <img src="img/sem-imagem.png" class="produto-img" alt="Sem imagem">
      <?php endif; ?>
    </div>

    <div class="produto-info">
      <p><strong>Responsável:</strong> <?= htmlspecialchars($produto['nome_usuario']) ?></p>
      <p><strong>Descrição:</strong> <?= nl2br(htmlspecialchars($produto['descricao'])) ?></p>
      <p><strong>Para Quem:</strong> <?= nl2br(htmlspecialchars($produto['para_quem'])) ?></p>
      <p><strong>Por Quem:</strong> <?= nl2br(htmlspecialchars($produto['por_quem'])) ?></p>
      <p><strong>Por Que:</strong> <?= nl2br(htmlspecialchars($produto['por_que'])) ?></p>
      <p><strong>Para Que:</strong> <?= nl2br(htmlspecialchars($produto['para_que'])) ?></p>
      <p><strong>Pré-requisitos:</strong> <?= nl2br(htmlspecialchars($produto['pre_requisitos'])) ?></p>
      <p><strong>Modo de Uso:</strong> <?= nl2br(htmlspecialchars($produto['modo_de_uso'])) ?></p>
    </div>
  </div>

  <h2>Arquivos do Produto</h2>
  <ul class="arquivos-lista">
    <?php if (!empty($arquivos)): ?>
      <?php foreach ($arquivos as $file): ?>
        <li><a href="<?= htmlspecialchars($file) ?>" target="_blank">📁 <?= basename($file) ?></a></li>
      <?php endforeach; ?>
    <?php else: ?>
      <li>Nenhum arquivo enviado.</li>
    <?php endif; ?>
  </ul>

  <hr>

  <h2>Componentes do Produto</h2>

  <?php
  $sqlComp = "SELECT * FROM componentes WHERE id_produto = :id_produto";
  $stmtComp = $pdo->prepare($sqlComp);
  $stmtComp->execute([':id_produto' => $produto_id]);
  $componentes = $stmtComp->fetchAll(PDO::FETCH_ASSOC);
  ?>

  <?php if ($componentes): ?>
    <?php foreach ($componentes as $componente): ?>
      <div class="componente-bloco">
        <h3><?= htmlspecialchars($componente['nome_componente']) ?></h3>
        <p><strong>Descrição:</strong> <?= nl2br(htmlspecialchars($componente['descricao'])) ?></p>

        <!-- Imagens do componente -->
        <?php
        $imgs = json_decode($componente['imagens'], true) ?? [];
        if ($imgs):
        ?>
          <div class="produto-imagens">
            <?php foreach ($imgs as $img): ?>
              <img src="<?= htmlspecialchars($img) ?>" class="produto-img" alt="Imagem do componente">
            <?php endforeach; ?>
          </div>
        <?php endif; ?>

        <!-- Arquivos -->
        <?php
        $arqs = json_decode($componente['arquivos'], true) ?? [];
        if ($arqs):
        ?>
          <div class="midia-bloco">
            <h4>Arquivos</h4>
            <ul>
              <?php foreach ($arqs as $file): ?>
                <li><a href="<?= htmlspecialchars($file) ?>" target="_blank">📂 <?= basename($file) ?></a></li>
              <?php endforeach; ?>
            </ul>
          </div>
        <?php endif; ?>

        <!-- Passo a passo -->
        <?php
        $sqlPasso = "SELECT * FROM passo_a_passo WHERE id_componente = :id_componente";
        $stmtPasso = $pdo->prepare($sqlPasso);
        $stmtPasso->execute([':id_componente' => $componente['id_componente']]);
        $passos = $stmtPasso->fetchAll(PDO::FETCH_ASSOC);
        ?>
        <?php if ($passos): ?>
          <div class="passo-bloco">
            <h4>Passo a Passo</h4>
            <?php foreach ($passos as $p): ?>
              <p><?= nl2br(htmlspecialchars($p['descricao'])) ?></p>
              <?php if (!empty($p['arquivo_upload'])): ?>
                <a href="<?= htmlspecialchars($p['arquivo_upload']) ?>" target="_blank">📎 Ver Arquivo</a>
              <?php endif; ?>
              <hr>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>

        <!-- Dependências -->
        <?php
        $sqlDeps = "SELECT cd.id_componente_destino, c.nome_componente 
                    FROM componentes_dependencias cd
                    JOIN componentes c ON c.id_componente = cd.id_componente_destino
                    WHERE cd.id_componente_origem = :id_origem";

        $stmtDeps = $pdo->prepare($sqlDeps);
        $stmtDeps->execute([':id_origem' => $componente['id_componente']]);
        $deps = $stmtDeps->fetchAll(PDO::FETCH_ASSOC);
        ?>
        <?php if ($deps): ?>
          <div class="dependencias">
            <h4>Este componente depende de:</h4>
            <ul>
              <?php foreach ($deps as $dep): ?>
                <li><?= htmlspecialchars($dep['nome_componente']) ?></li>
              <?php endforeach; ?>
            </ul>
          </div>
        <?php endif; ?>
      </div>
      <hr>
    <?php endforeach; ?>
  <?php else: ?>
    <p>Nenhum componente registrado para este produto.</p>
  <?php endif; ?>
</main>

<?php include 'footer.php'; ?>
</body>
</html>
