<?php
// processamento_login.php - Melhorando a segurança do login
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $senha = $_POST['senha'];

    $stmt = $pdo->prepare("SELECT id_usuario,  senha FROM usuarios WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($senha, $user['senha'])) {
        session_regenerate_id(true);
        $_SESSION['id_usuario'] = $user['id_usuario'];
        header("Location: perfil_usuario.php");
        exit();
    } else {
        echo "<script>alert('Email ou senha incorretos!'); window.location='login.php';</script>";
    }
}
?>
