<?php
// processa_cadastro_usuario.php - Melhorando segurança e validação
require 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = htmlspecialchars($_POST['nome']);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $senha = $_POST['senha'];
    $confirmar_senha = $_POST['confirmar_senha'];

    if ($senha !== $confirmar_senha) {
        die("<script>alert('As senhas não coincidem!'); window.location='cadastro_usuario.php';</script>");
    }

    $senha_hashed = password_hash($senha, PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, senha) VALUES (:nome, :email, :senha)");
    $stmt->bindParam(':nome', $nome);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':senha', $senha_hashed);
    
    if ($stmt->execute()) {
        echo "<script>alert('Cadastro realizado com sucesso! Faça login.'); window.location='login.php';</script>";
    } else {
        echo "<script>alert('Erro ao cadastrar!'); window.location='cadastro_usuario.php';</script>";
    }
}
?>