<?php
session_start();
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

require 'conexao.php'; // Arquivo de conexão com o banco de dados

$usuario_id = $_SESSION['id_usuario'];
$nome = trim($_POST['nome']);
$email = trim($_POST['email']);
$senha = !empty($_POST['senha']) ? password_hash($_POST['senha'], PASSWORD_DEFAULT) : null;
$foto = null;

// Verifica se um arquivo foi enviado
if (!empty($_FILES['foto']['name'])) {
    $diretorio = "uploads/";
    $foto = basename($_FILES["foto"]["name"]);
    $caminhoArquivo = $diretorio . $foto;
    
    // Verifica se o diretório existe, se não, cria
    if (!is_dir($diretorio)) {
        mkdir($diretorio, 0777, true);
    }

    // Move o arquivo para a pasta
    if (move_uploaded_file($_FILES["foto"]["tmp_name"], $caminhoArquivo)) {
        // Atualiza o campo da foto no banco
        $sql = "UPDATE usuarios SET foto = :foto WHERE id_usuario = :id_usuario";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':foto', $foto, PDO::PARAM_STR);
        $stmt->bindParam(':id_usuario', $usuario_id, PDO::PARAM_INT);
        $stmt->execute();
    }
}

// Atualiza os dados do usuário
if ($senha) {
    $sql = "UPDATE usuarios SET nome = :nome, email = :email, senha = :senha WHERE id_usuario = :id_usuario";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':senha', $senha, PDO::PARAM_STR);
} else {
    $sql = "UPDATE usuarios SET nome = :nome, email = :email WHERE id_usuario = :id_usuario";
    $stmt = $pdo->prepare($sql);
}

$stmt->bindParam(':nome', $nome, PDO::PARAM_STR);
$stmt->bindParam(':email', $email, PDO::PARAM_STR);
$stmt->bindParam(':id_usuario', $usuario_id, PDO::PARAM_INT);
$stmt->execute();

header("Location: perfil_usuario.php?sucesso=1");
exit();
?>
