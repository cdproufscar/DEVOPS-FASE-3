<?php
session_start();
require 'conexao.php';

if (!isset($_GET['id'])) {
    die("Componente não especificado.");
}

$id_temp = $_GET['id'];
$componentes = $_SESSION['componentes_temp'] ?? [];

$componente = null;
foreach ($componentes as $c) {
    if ($c['id'] == $id_temp) {
        $componente = $c;
        break;
    }
}

if (!$componente) {
    die("Componente não encontrado na sessão.");
}

function getNomeById($pdo, $tabela, $id_col, $nome_col, $id) {
    $stmt = $pdo->prepare("SELECT $nome_col FROM $tabela WHERE $id_col = :id");
    $stmt->execute([':id' => $id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ? $row[$nome_col] : "ID $id";
}

function getDescById($pdo, $tabela, $id_col, $desc_col, $id) {
    $stmt = $pdo->prepare("SELECT $desc_col FROM $tabela WHERE $id_col = :id");
    $stmt->execute([':id' => $id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ? $row[$desc_col] : '';
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Detalhes Temporários do Componente</title>
  <link rel="stylesheet" href="css/global.css">
  <link rel="stylesheet" href="css/visualizar_componente_temp.css">
</head>
<body>

<?php include 'header_dinamico.php'; ?>

<main class="temp-comp">
  <h1>Componente: <?= htmlspecialchars($componente['nome']) ?></h1>

  <!-- Descrição -->
  <div class="comp-bloco">
    <h2>Descrição</h2>
    <p><?= nl2br(htmlspecialchars($componente['descricao'])) ?></p>
  </div>

  <!-- Imagens -->
  <?php if (!empty($componente['imagens'])): ?>
    <div class="comp-bloco">
      <h2>Imagens</h2>
      <div class="midia">
        <?php foreach ($componente['imagens'] as $img): ?>
          <img src="<?= htmlspecialchars($img) ?>" class="comp-img" alt="Imagem do componente">
        <?php endforeach; ?>
      </div>
    </div>
  <?php endif; ?>

  <!-- Arquivos -->
  <?php if (!empty($componente['arquivos'])): ?>
    <div class="comp-bloco">
      <h2>Arquivos</h2>
      <ul>
        <?php foreach ($componente['arquivos'] as $file): ?>
          <li><a href="<?= htmlspecialchars($file) ?>" target="_blank">📁 <?= basename($file) ?></a></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <!-- Materiais -->
  <div class="comp-bloco">
    <h2>Materiais Utilizados</h2>
    <?php if (!empty($componente['materiais'])): ?>
      <div class="vinculos">
        <ul>
          <?php foreach ($componente['materiais'] as $id => $mat): ?>
            <li>
              <strong><?= htmlspecialchars(getNomeById($pdo, 'materiais', 'id_material', 'nome_material', $id)) ?></strong>:
              <?= htmlspecialchars($mat['quantidade']) ?> <?= htmlspecialchars($mat['unidade']) ?> —
              <?= htmlspecialchars(getDescById($pdo, 'materiais', 'id_material', 'descricao_material', $id)) ?>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php else: ?>
      <p>Nenhum material vinculado.</p>
    <?php endif; ?>
  </div>

  <!-- Ferramentas -->
  <div class="comp-bloco">
    <h2>Ferramentas Utilizadas</h2>
    <?php if (!empty($componente['ferramentas'])): ?>
      <div class="vinculos">
        <ul>
          <?php foreach ($componente['ferramentas'] as $id => $fer): ?>
            <li>
              <strong><?= htmlspecialchars(getNomeById($pdo, 'ferramentas', 'id_ferramenta', 'nome_ferramenta', $id)) ?></strong>:
              <?= htmlspecialchars($fer['dimensoes']) ?> —
              <?= htmlspecialchars(getDescById($pdo, 'ferramentas', 'id_ferramenta', 'descricao_ferramenta', $id)) ?>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php else: ?>
      <p>Nenhuma ferramenta vinculada.</p>
    <?php endif; ?>
  </div>

  <!-- Passo-a-Passo -->
<div class="midia-bloco">
  <h2>Passo a Passo</h2>
  <?php if (!empty($componente['passos']) && is_array($componente['passos'])): ?>
    <table>
      <thead>
        <tr>
          <th>Passo</th>
          <th>Materiais</th>
          <th>Ferramentas</th>
          <th>Descrição</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $n = 1;
        foreach ($componente['passos'] as $p):
            // Valida arrays
            $listaMateriais = isset($p['materiais']) && is_array($p['materiais']) ? $p['materiais'] : [];
            $listaFerramentas = isset($p['ferramentas']) && is_array($p['ferramentas']) ? $p['ferramentas'] : [];

            // Busca os nomes de materiais
            $materiaisNomes = array_map(function($id) use ($pdo) {
                return getNomeById($pdo, 'materiais', 'id_material', 'nome_material', $id);
            }, $listaMateriais);

            // Busca os nomes de ferramentas
            $ferramentasNomes = array_map(function($id) use ($pdo) {
                return getNomeById($pdo, 'ferramentas', 'id_ferramenta', 'nome_ferramenta', $id);
            }, $listaFerramentas);

            $descricaoPasso = nl2br(htmlspecialchars($p['descricao'] ?? ''));
        ?>
          <tr>
            <td><?= $n++ ?></td>
            <td><?= !empty($materiaisNomes) ? htmlspecialchars(implode(', ', $materiaisNomes)) : '—' ?></td>
            <td><?= !empty($ferramentasNomes) ? htmlspecialchars(implode(', ', $ferramentasNomes)) : '—' ?></td>
            <td><?= $descricaoPasso ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php else: ?>
    <p>Nenhum passo registrado.</p>
  <?php endif; ?>
</div>

  <div class="botoes">
    <a href="cadastro_produto.php" class="btn-voltar">← Voltar ao Produto</a>
  </div>
</main>

<?php include 'footer.php'; ?>
</body>
</html>
