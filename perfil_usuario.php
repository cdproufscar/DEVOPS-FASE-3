<?php
session_start();
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

require 'conexao.php';
$id_usuario = $_SESSION['id_usuario'];

// Usuário
$sql = "SELECT nome, email, foto FROM usuarios WHERE id_usuario = :id_usuario";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
$stmt->execute();
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

// Classificações
$sqlPerfis = "SELECT categoria FROM perfil_usuario WHERE id_usuario = :id";
$stmtPerfis = $pdo->prepare($sqlPerfis);
$stmtPerfis->execute([':id' => $id_usuario]);
$categorias = $stmtPerfis->fetchAll(PDO::FETCH_COLUMN);

// Dados familiares
$sqlFam = "SELECT relacao, tipo_deficiencia, descricao FROM dados_familiares WHERE id_usuario = :id";
$stmtFam = $pdo->prepare($sqlFam);
$stmtFam->execute([':id' => $id_usuario]);
$dadosFamiliares = $stmtFam->fetch(PDO::FETCH_ASSOC);

// Produtos do usuário
$sqlProdutos = "SELECT id_produto, nome_produto, descricao, imagens FROM produtos WHERE id_usuario = :id_usuario";
$stmtProdutos = $pdo->prepare($sqlProdutos);
$stmtProdutos->execute([':id_usuario' => $id_usuario]);
$produtos = $stmtProdutos->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Perfil do Usuário</title>
  <link rel="stylesheet" href="css/global.css">
  <link rel="stylesheet" href="css/perfil_usuario.css">
  <link rel="stylesheet" href="css/perfil_usuario_modal.css">

  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="js/perfil_usuario.js" defer></script>
  <script src="js/perfil_usuario_modal.js"></script>

</head>
<body>
<?php include 'header_dinamico.php'; ?>

<main>
  <h1>Perfil do Usuário</h1>
  <section class="perfil">
    <div class="perfil-foto">
      <?php if (!empty($usuario['foto']) && file_exists("uploads/" . $usuario['foto'])): ?>
        <img src="uploads/<?php echo htmlspecialchars($usuario['foto']); ?>" alt="Foto de Perfil" class="perfil-img">
      <?php else: ?>
        <img src="img/user_nulo.png" class="perfil-img" alt="Foto Padrão">
      <?php endif; ?>
    </div>

    <p><strong>Nome:</strong> <?= htmlspecialchars($usuario['nome']) ?></p>
    <p><strong>Email:</strong> <?= htmlspecialchars($usuario['email']) ?></p>
    <p><strong>Classificações:</strong> <?= implode(', ', $categorias) ?></p>

    <?php if (in_array('FAMILIAR', $categorias)): ?>
      <h3>Dados do Ente com Deficiência</h3>
      <p><strong>Relação:</strong> <?= htmlspecialchars($dadosFamiliares['relacao'] ?? '') ?></p>
      <p><strong>Tipo:</strong> <?= htmlspecialchars($dadosFamiliares['tipo_deficiencia'] ?? '') ?></p>
      <p><strong>Descrição:</strong> <?= htmlspecialchars($dadosFamiliares['descricao'] ?? '') ?></p>
    <?php endif; ?>

    <button class="btn-familiar" onclick="abrirModalEdicao()">Editar Perfil Completo</button>
  </section>

  <section class="meus-produtos">
    <h2>Meus Produtos</h2>
    <?php if (count($produtos) > 0): ?>
      <div class="catalogo-container">
        <?php foreach ($produtos as $produto):
          $imagens = json_decode($produto['imagens'], true);
          $img = !empty($imagens[0]) && file_exists($imagens[0]) ? $imagens[0] : "img/sem-imagem.png";
        ?>
          <div class="produto-card">
            <img src="<?= htmlspecialchars($img) ?>" alt="Imagem do Produto">
            <h3><?= htmlspecialchars($produto['nome_produto']) ?></h3>
            <p><?= htmlspecialchars($produto['descricao']) ?></p>
            <div class="produto-actions">
              <a href="editar_produto.php?id=<?= $produto['id_produto'] ?>" class="btn-editar">Editar</a>
              <button onclick="confirmarExclusao(<?= $produto['id_produto'] ?>)" class="btn-excluir">Excluir</button>
            </div>
            <a href="produto_detalhado.php?id=<?= $produto['id_produto'] ?>" class="btn-detalhes">Ver Detalhes</a>
          </div>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <p>Você ainda não cadastrou produtos.</p>
    <?php endif; ?>
  </section>

  <!-- MODAL DE EDIÇÃO COMPLETA -->
  <div id="modal-edicao" class="modal">
    <div class="modal-content">
      <span class="fechar" onclick="fecharModalEdicao()">&times;</span>
      <h3>Editar Perfil</h3>
      <form id="form-edicao" enctype="multipart/form-data">
        <label for="foto">Foto de Perfil:</label>
        <input type="file" name="foto"><br>

        <label for="nome">Nome:</label>
        <input type="text" name="nome" value="<?= htmlspecialchars($usuario['nome']) ?>">

        <label for="email">Email:</label>
        <input type="email" name="email" value="<?= htmlspecialchars($usuario['email']) ?>">

        <label for="senha">Nova Senha:</label>
        <input type="password" name="senha" placeholder="Deixe em branco para não alterar">

        <fieldset>
          <legend>Classificações:</legend>
          <label><input type="checkbox" name="secoes[]" value="PCD" <?= in_array('PCD', $categorias) ? 'checked' : '' ?>> PCD</label>
          <label><input type="checkbox" name="secoes[]" value="MAKER" <?= in_array('MAKER', $categorias) ? 'checked' : '' ?>> Maker</label>
          <label><input type="checkbox" name="secoes[]" value="FAMILIAR" <?= in_array('FAMILIAR', $categorias) ? 'checked' : '' ?>> Familiar</label>
          <label><input type="checkbox" name="secoes[]" value="ESPECIALISTA DA SAÚDE" <?= in_array('ESPECIALISTA DA SAÚDE', $categorias) ? 'checked' : '' ?>> Especialista da Saúde</label>
          <label><input type="checkbox" name="secoes[]" value="FORNECEDOR" <?= in_array('FORNECEDOR', $categorias) ? 'checked' : '' ?>> Fornecedor</label>
        </fieldset>

        <?php if (in_array("FAMILIAR", $categorias)): ?>
        <div id="dados-familiares-modal">
          <h4>Dados do Ente com Deficiência</h4>
          <label>Relação:</label>
          <input type="text" name="familiar_relacao" value="<?= htmlspecialchars($dadosFamiliares['relacao'] ?? '') ?>">
          <label>Tipo de Deficiência:</label>
          <select name="familiar_deficiencia">
            <option value="">Selecione...</option>
            <option value="mobilidade" <?= ($dadosFamiliares['tipo_deficiencia'] ?? '') === 'mobilidade' ? 'selected' : '' ?>>Mobilidade</option>
            <option value="visual" <?= ($dadosFamiliares['tipo_deficiencia'] ?? '') === 'visual' ? 'selected' : '' ?>>Visual</option>
            <option value="auditiva" <?= ($dadosFamiliares['tipo_deficiencia'] ?? '') === 'auditiva' ? 'selected' : '' ?>>Auditiva</option>
            <option value="intelectual" <?= ($dadosFamiliares['tipo_deficiencia'] ?? '') === 'intelectual' ? 'selected' : '' ?>>Intelectual</option>
            <option value="múltipla" <?= ($dadosFamiliares['tipo_deficiencia'] ?? '') === 'múltipla' ? 'selected' : '' ?>>Múltipla</option>
            <option value="outro" <?= ($dadosFamiliares['tipo_deficiencia'] ?? '') === 'outro' ? 'selected' : '' ?>>Outro</option>
          </select>
          <label>Descrição:</label>
          <textarea name="descricao_deficiencia_familiar"><?= htmlspecialchars($dadosFamiliares['descricao'] ?? '') ?></textarea>
        </div>
        <?php endif; ?>

        <br><button type="submit">Salvar Alterações</button>
      </form>
    </div>
  </div>
</main>

<?php include 'footer.php'; ?>
</body>
</html>
