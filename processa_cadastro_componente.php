<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);



session_start();
require 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');

    if (empty($nome) || empty($descricao)) {
        $_SESSION['erro_componente'] = "Nome e descrição são obrigatórios!";
        header("Location: cadastro_componente.php");
        exit;
    }

    // Corrige materiais
    $materiais_raw = $_POST['materiais'] ?? [];
    $materiais = [];
    foreach ($materiais_raw as $index => $mat) {
        if (isset($mat['id'], $mat['quantidade'], $mat['unidade'])) {
            $materiais[$mat['id']] = [
                'quantidade' => $mat['quantidade'],
                'unidade' => $mat['unidade']
            ];
        }
    }

    // Corrige ferramentas
    $ferramentas_raw = $_POST['ferramentas'] ?? [];
    $ferramentas = [];
    foreach ($ferramentas_raw as $index => $fer) {
        if (isset($fer['id'], $fer['dimensoes'])) {
            $ferramentas[$fer['id']] = [
                'dimensoes' => $fer['dimensoes']
            ];
        }
    }

    // Corrige passos
    $passos = $_POST['passos'] ?? [];

    // Uploads
    $uploadDir = 'uploads/componentes/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

    $imagens = [];
    if (!empty($_FILES['imagens']['name'][0])) {
        foreach ($_FILES['imagens']['tmp_name'] as $i => $tmp) {
            if ($_FILES['imagens']['error'][$i] === 0) {
                $ext = pathinfo($_FILES['imagens']['name'][$i], PATHINFO_EXTENSION);
                $nomeArq = uniqid('img_') . ".$ext";
                $caminho = $uploadDir . $nomeArq;
                if (move_uploaded_file($tmp, $caminho)) {
                    $imagens[] = $caminho;
                }
            }
        }
    }

    $arquivos = [];
    if (!empty($_FILES['arquivos']['name'][0])) {
        foreach ($_FILES['arquivos']['tmp_name'] as $i => $tmp) {
            if ($_FILES['arquivos']['error'][$i] === 0) {
                $ext = pathinfo($_FILES['arquivos']['name'][$i], PATHINFO_EXTENSION);
                $nomeArq = uniqid('file_') . ".$ext";
                $caminho = $uploadDir . $nomeArq;
                if (move_uploaded_file($tmp, $caminho)) {
                    $arquivos[] = $caminho;
                }
            }
        }
    }

    // Armazena na sessão
    $componentes = $_SESSION['componentes_temp'] ?? [];
    $id_temp = count($componentes) + 1;

    $componentes[] = [
        'id' => $id_temp,
        'nome' => $nome,
        'descricao' => $descricao,
        'imagens' => $imagens,
        'arquivos' => $arquivos,
        'materiais' => $materiais,
        'ferramentas' => $ferramentas,
        'passos' => $passos
    ];

    $_SESSION['componentes_temp'] = $componentes;
    $_SESSION['sucesso_componente'] = "Componente cadastrado com sucesso!";
    header("Location: cadastro_produto.php");
    exit;
}
