<?php
// processa_recuperacao.php - Implementação de envio de email para recuperação de senha
require 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    
    $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $token = bin2hex(random_bytes(50));
        $expira_em = date("Y-m-d H:i:s", strtotime("+1 hour"));

        $stmt = $pdo->prepare("INSERT INTO recuperacao_senhas (id_usuario, token, expira_em) VALUES (:id_usuario, :token, :expira_em)");
        $stmt->bindParam(':id_usuario', $user['id']);
        $stmt->bindParam(':token', $token);
        $stmt->bindParam(':expira_em', $expira_em);
        $stmt->execute();
        
        // Simulação de envio de email
        mail($email, "Recuperação de Senha", "Use este link para redefinir sua senha: http://seusite.com/redefinir_senha.php?token=$token");
        echo "<script>alert('Email de recuperação enviado!'); window.location='login.php';</script>";
    } else {
        echo "<script>alert('Email não encontrado!'); window.location='recuperacao_senha.php';</script>";
    }
}
?>
