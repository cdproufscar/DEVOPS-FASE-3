<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Redefinir Senha - Assistiverse</title>
  <link rel="stylesheet" href="css/global.css">
  <link rel="stylesheet" href="css/redefinir_senha.css">
</head>
<body>

<?php include 'header_dinamico.php'; ?>

<main>
  <h1>Redefinir Senha</h1>
  <form action="php/processa_redefinir_senha.php" method="POST">
    <input type="hidden" name="token" value="<?php echo isset($_GET['token']) ? htmlspecialchars($_GET['token']) : ''; ?>">
    
    <label for="nova_senha">Nova Senha:</label>
    <input type="password" id="nova_senha" name="nova_senha" required>
    
    <label for="confirmar_senha">Confirmar Nova Senha:</label>
    <input type="password" id="confirmar_senha" name="confirmar_senha" required>
    
    <button type="submit">Redefinir Senha</button>
  </form>
</main>

<?php include 'footer.php'; ?>

</body>
</html>
