<?php
session_start();
require 'conexao.php';

if (!isset($_GET['id'])) {
    die("Componente não especificado.");
}

$id = $_GET['id'];
$componentes = $_SESSION['componentes_temp'] ?? [];
$componente = null;

foreach ($componentes as $comp) {
    if ($comp['id'] == $id) {
        $componente = $comp;
        break;
    }
}

if (!$componente) {
    die("Componente não encontrado.");
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Componente Temporário</title>
    <link rel="stylesheet" href="css/global.css">
    <link rel="stylesheet" href="css/editar_componente_temp.css">
</head>
<body>
<?php include 'header_dinamico.php'; ?>

<main class="edit-comp">
    <h1>Editar Componente Temporário</h1>
    <form action="salvar_edicao_componente_temp.php" method="POST">
        <input type="hidden" name="id" value="<?= htmlspecialchars($componente['id']) ?>">

        <label>Nome:</label>
        <input type="text" name="nome" value="<?= htmlspecialchars($componente['nome']) ?>" required>

        <label>Descrição:</label>
        <textarea name="descricao"><?= htmlspecialchars($componente['descricao']) ?></textarea>

        <button type="submit">Salvar Alterações</button>
    </form>

    <div class="botoes">
        <a href="visualizar_componente_temp.php" class="btn-voltar">← Cancelar</a>
    </div>
</main>

<?php include 'footer.php'; ?>
</body>
</html>
