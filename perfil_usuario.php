<?php
session_start();
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

require 'conexao.php';
$id_usuario = $_SESSION['id_usuario'];

// Busca dados do usuário (nome, email, foto)
$sql = "SELECT nome, email, foto FROM usuarios WHERE id_usuario = :id_usuario";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
$stmt->execute();
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$usuario) {
    session_destroy();
    header("Location: login.php");
    exit();
}

// Busca produtos do usuário
$sql_prod = "SELECT id_produto, nome_produto, descricao, imagens FROM produtos WHERE id_usuario = :id_usuario ORDER BY data_criacao DESC";
$stmt_prod = $pdo->prepare($sql_prod);
$stmt_prod->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
$stmt_prod->execute();
$produtos = $stmt_prod->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Perfil do Usuário</title>
  <link rel="stylesheet" href="css/global.css">
  <link rel="stylesheet" href="css/perfil_usuario.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="js/perfil_usuario.js" defer></script>
</head>
<body>
<?php include 'header_dinamico.php'; ?>

<main>
  <h1>Perfil do Usuário</h1>
  <section class="perfil">
    <form action="atualiza_perfil.php" method="POST" enctype="multipart/form-data">
      <div class="perfil-foto">
        <label for="foto">Foto de Perfil:</label>
        <input type="file" name="foto" id="foto">
        <br>
        <?php if (!empty($usuario['foto'])): ?>
            <img src="uploads/<?php echo htmlspecialchars($usuario['foto']); ?>" alt="Foto de Perfil" class="perfil-img">
        <?php else: ?>
            <img src="img/default-profile.png" class="perfil-img" alt="Foto Padrão">
        <?php endif; ?>
      </div>

      <label for="nome">Nome:</label>
      <input type="text" name="nome" value="<?php echo htmlspecialchars($usuario['nome']); ?>">

      <label for="email">Email:</label>
      <input type="email" name="email" value="<?php echo htmlspecialchars($usuario['email']); ?>">

      <label for="senha">Nova Senha:</label>
      <input type="password" name="senha" placeholder="Deixe em branco para não alterar">

      <button type="submit">Atualizar Perfil</button>
    </form>
  </section>

  <section class="meus-produtos">
    <h2>Meus Produtos</h2>
    <?php if (count($produtos) > 0): ?>
      <div class="catalogo-container">
        <?php foreach ($produtos as $produto): 
          $imagens = json_decode($produto['imagens'], true);
          $imagem = (!empty($imagens[0]) && file_exists($imagens[0])) ? $imagens[0] : 'img/sem-imagem.png';
        ?>
          <div class="produto-card">
            <img src="<?php echo htmlspecialchars($imagem); ?>" class="produto-img" alt="Imagem">
            <h3><?php echo htmlspecialchars($produto['nome_produto']); ?></h3>
            <p><?php echo htmlspecialchars($produto['descricao']); ?></p>
            <div class="produto-actions">
              <a href="editar_produto.php?id=<?php echo $produto['id_produto']; ?>" class="btn-editar">Editar</a>
              <button onclick="confirmarExclusao(<?php echo $produto['id_produto']; ?>)" class="btn-excluir">Excluir</button>
            </div>
            <a href="produto_detalhado.php?id=<?php echo $produto['id_produto']; ?>" class="btn-detalhes">Ver Detalhes</a>
          </div>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <p>Você ainda não cadastrou nenhum produto.</p>
    <?php endif; ?>
  </section>
</main>

<?php include 'footer.php'; ?>
</body>
</html>
