<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<header>
  <div class="logo">
    <a href="telainicial.html">
      <img src="img/AssistiVerse.png" alt="Logo" class="logo-image">
    </a>
  </div>
  <nav>
    <a href="sobrenos.html">Sobre Nós</a>
    <a href="catalogo.php">Projetos</a>
    <a href="contato2.html">Contato</a>
    <a href="cadastro_dispositivo.html">Criar+</a>
  </nav>
  <div class="b_login">
    <?php if (isset($_SESSION['id_usuario'])): ?>
      <button type="button" class="btn-login">
        <a href="perfil_usuario.php">Meu Perfil</a>
      </button>
      <button type="button" class="btn-login">
        <a href="logout.php">Sair</a>
      </button>
    <?php else: ?>
      <button type="button" class="btn-login">
        <a href="login.php">Login</a>
      </button>
    <?php endif; ?>
  </div>
</header>
