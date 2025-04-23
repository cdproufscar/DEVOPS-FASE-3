<?php
session_start();
require 'conexao.php';

if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

$id_usuario = $_SESSION['id_usuario'];
$componentes_temp = $_SESSION['componentes_temp'] ?? [];

$mensagem_sucesso = '';
if (isset($_SESSION['sucesso_componente'])) {
    $mensagem_sucesso = $_SESSION['sucesso_componente'];
    unset($_SESSION['sucesso_componente']);
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Cadastro de Produto</title>
  <link rel="stylesheet" href="css/global.css">
  <link rel="stylesheet" href="css/cadastro_produto.css">
</head>
<body>
<?php include 'header_dinamico.php'; ?>

<main class="cad-prod">
<?php if (!empty($mensagem_sucesso)): ?>
  <script>
    alert("<?= htmlspecialchars($mensagem_sucesso) ?>");
  </script>
<?php endif; ?>


  <h1>Cadastro de Produto</h1>

  <form action="processa_cadastro_produto.php" method="POST" enctype="multipart/form-data">
    <label>Nome do Produto:</label>
    <input type="text" name="nome_produto" required>

    <label>Descrição:</label>
    <textarea name="descricao"></textarea>

    <label>Para Quem?</label>
    <input type="text" name="para_quem">

    <label>Por Quem?</label>
    <input type="text" name="por_quem">

    <label>Por Que?</label>
    <textarea name="por_que"></textarea>

    <label>Para Que?</label>
    <textarea name="para_que"></textarea>

    <label>Pré-requisitos:</label>
    <textarea name="pre_requisitos"></textarea>

    <label>Modo de Uso:</label>
    <textarea name="modo_de_uso"></textarea>

    <label>Imagens do Produto:</label>
    <input type="file" name="imagens[]" multiple accept="image/*">

    <label>Arquivos Diversos:</label>
    <input type="file" name="arquivos[]" multiple accept=".sql,.dwg,.mp4,.avi,.zip,.rar,.pdf">

    <h2>Componentes</h2>
    <a href="cadastro_componente.php" target="_blank">
      <button type="button">➕ Adicionar Componente</button>
    </a>

    <?php if (!empty($componentes_temp)): ?>
      <table>
        <thead>
          <tr>
            <th>#</th>
            <th>Nome</th>
            <th>Descrição</th>
            <th>Ação</th>
            <th>Excluir </th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($componentes_temp as $index => $comp): ?>
            <tr>
              <td><?= $index + 1 ?></td>
              <td><?= htmlspecialchars($comp['nome']) ?></td>
              <td><?= htmlspecialchars(substr($comp['descricao'], 0, 60)) ?>...</td>
              <td>
                <a href="visualizar_componente_temp.php?id=<?= $comp['id'] ?>" target="_blank" class="btn-detalhes">
                  Ver Detalhes
                </a>
              </td>
              <td> 
              <a href="excluir_componente_temp.php?id=<?= $comp['id'] ?>" class="btn-excluir-comp" onclick="return confirm('Deseja excluir este componente?')"> 🗑 </a>
              </td>

            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php else: ?>
      <p>Nenhum componente adicionado ainda.</p>
    <?php endif; ?>

    <button type="submit">Cadastrar Produto</button>
  </form>
</main>

<?php include 'footer.php'; ?>
</body>
</html>
