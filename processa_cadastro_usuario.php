<?php
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

    try {
        $pdo->beginTransaction();

        // Inserção principal
        $stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, senha, tipo_usuario) VALUES (:nome, :email, :senha, 'FAMILIAR')");
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':senha', $senha_hashed);
        $stmt->execute();

        $id_usuario = $pdo->lastInsertId();

        // Dados familiares (se informados)
        if (in_array("FAMILIAR", $_POST['secoes'] ?? [])) {
            $relacao = $_POST['familiar_relacao'] ?? '';
            $tipo_deficiencia = $_POST['familiar_deficiencia'] ?? '';
            $descricao = $_POST['descricao_deficiencia_familiar'] ?? '';

            $stmt_fam = $pdo->prepare("INSERT INTO dados_familiares (id_usuario, relacao, tipo_deficiencia, descricao)
                                       VALUES (:id_usuario, :relacao, :tipo_deficiencia, :descricao)");
            $stmt_fam->bindParam(':id_usuario', $id_usuario);
            $stmt_fam->bindParam(':relacao', $relacao);
            $stmt_fam->bindParam(':tipo_deficiencia', $tipo_deficiencia);
            $stmt_fam->bindParam(':descricao', $descricao);
            $stmt_fam->execute();
        }

        $pdo->commit();
        echo "<script>alert('Cadastro realizado com sucesso! Faça login.'); window.location='login.php';</script>";

    } catch (Exception $e) {
        $pdo->rollBack();
        echo "<script>alert('Erro ao cadastrar: " . $e->getMessage() . "'); window.location='cadastro_usuario.php';</script>";
    }
}
