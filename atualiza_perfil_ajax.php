<?php
session_start();
header('Content-Type: application/json');

require 'conexao.php';

if (!isset($_SESSION['id_usuario'])) {
    echo json_encode(['status' => 'erro', 'mensagem' => 'Usuário não autenticado.']);
    exit;
}

try {
    $id = $_SESSION['id_usuario'];
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $senha = !empty($_POST['senha']) ? password_hash($_POST['senha'], PASSWORD_DEFAULT) : null;
    $secoes = $_POST['secoes'] ?? [];

    $pdo->beginTransaction();

    // Atualizar usuário
    if ($senha) {
        $sql = "UPDATE usuarios SET nome = :nome, email = :email, senha = :senha WHERE id_usuario = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':senha', $senha);
    } else {
        $sql = "UPDATE usuarios SET nome = :nome, email = :email WHERE id_usuario = :id";
        $stmt = $pdo->prepare($sql);
    }

    $stmt->bindParam(':nome', $nome);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':id', $id);
    $stmt->execute();

    // Classificações
    $pdo->prepare("DELETE FROM perfil_usuario WHERE id_usuario = :id")->execute([':id' => $id]);
    foreach ($secoes as $secao) {
        $pdo->prepare("INSERT INTO perfil_usuario (id_usuario, categoria, descricao) VALUES (:id, :cat, '')")
            ->execute([':id' => $id, ':cat' => $secao]);
    }

    // Dados familiares
    if (in_array("FAMILIAR", $secoes)) {
        $relacao = $_POST['familiar_relacao'] ?? '';
        $tipo = $_POST['familiar_deficiencia'] ?? '';
        $descricao = $_POST['descricao_deficiencia_familiar'] ?? '';

        $check = $pdo->prepare("SELECT id FROM dados_familiares WHERE id_usuario = :id");
        $check->execute([':id' => $id]);

        if ($check->rowCount()) {
            $stmt = $pdo->prepare("UPDATE dados_familiares SET relacao = :r, tipo_deficiencia = :t, descricao = :d WHERE id_usuario = :id");
        } else {
            $stmt = $pdo->prepare("INSERT INTO dados_familiares (relacao, tipo_deficiencia, descricao, id_usuario) VALUES (:r, :t, :d, :id)");
        }

        $stmt->execute([
            ':r' => $relacao,
            ':t' => $tipo,
            ':d' => $descricao,
            ':id' => $id
        ]);
    }

    $pdo->commit();
    echo json_encode(['status' => 'ok', 'mensagem' => 'Perfil atualizado com sucesso!']);

} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['status' => 'erro', 'mensagem' => 'Erro ao atualizar: ' . $e->getMessage()]);
}
