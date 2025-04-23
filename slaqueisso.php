<?php
session_start();
require 'conexao.php';

if (!isset($_SESSION['id_usuario'])) {
    die("Usuário não autenticado.");
}

$id_usuario = $_SESSION['id_usuario'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pdo->beginTransaction();

    try {
        // Dados principais do produto
        $nome = $_POST['nome_produto'];
        $descricao = $_POST['descricao'];
        $para_quem = $_POST['para_quem'];
        $por_quem = $_POST['por_quem'];
        $por_que = $_POST['por_que'];
        $para_que = $_POST['para_que'];
        $pre_requisitos = $_POST['pre_requisitos'];
        $modo_de_uso = $_POST['modo_de_uso'];

        $uploadDir = "uploads/produtos/";
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Imagens
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

        // Arquivos
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

        // Insere produto
        $stmt = $pdo->prepare("INSERT INTO produtos (
            id_usuario, nome_produto, descricao, para_quem, por_quem, por_que, para_que, pre_requisitos, modo_de_uso, imagens, arquivos
        ) VALUES (
            :id_usuario, :nome_produto, :descricao, :para_quem, :por_quem, :por_que, :para_que, :pre_requisitos, :modo_de_uso, :imagens, :arquivos
        )");

        $stmt->execute([
            ':id_usuario'     => $id_usuario,
            ':nome_produto'   => $nome,
            ':descricao'      => $descricao,
            ':para_quem'      => $para_quem,
            ':por_quem'       => $por_quem,
            ':por_que'        => $por_que,
            ':para_que'       => $para_que,
            ':pre_requisitos' => $pre_requisitos,
            ':modo_de_uso'    => $modo_de_uso,
            ':imagens'        => json_encode($imagens),
            ':arquivos'       => json_encode($arquivos),
        ]);

        $id_produto = $pdo->lastInsertId();

        // INSERIR COMPONENTES TEMPORÁRIOS
        if (!empty($_SESSION['componentes_temp'])) {
            foreach ($_SESSION['componentes_temp'] as $comp) {
                // Inserir componente
                $stmtComp = $pdo->prepare("INSERT INTO componentes (
                    id_produto, nome_componente, descricao, imagens, arquivos
                ) VALUES (
                    :id_produto, :nome_componente, :descricao, :imagens, :arquivos
                )");

                $stmtComp->execute([
                    ':id_produto'      => $id_produto,
                    ':nome_componente' => $comp['nome'],
                    ':descricao'       => $comp['descricao'],
                    ':imagens'         => json_encode($comp['imagens'] ?? []),
                    ':arquivos'        => json_encode($comp['arquivos'] ?? [])
                ]);

                $id_componente = $pdo->lastInsertId();

                // Inserir passo a passo (básico, 1 entrada por enquanto)
                $stmtPasso = $pdo->prepare("INSERT INTO passo_a_passo (
                    id_componente, descricao
                ) VALUES (
                    :id_componente, :descricao
                )");

                $stmtPasso->execute([
                    ':id_componente' => $id_componente,
                    ':descricao'     => $comp['descricao']
                ]);

                $id_passo = $pdo->lastInsertId();

                // Materiais
                if (!empty($comp['materiais'])) {
                    foreach ($comp['materiais'] as $id_mat) {
                        $pdo->prepare("INSERT INTO componente_materiais (id_componente, id_material) VALUES (:id_componente, :id_material)")
                            ->execute([':id_componente' => $id_componente, ':id_material' => $id_mat]);

                        $pdo->prepare("INSERT INTO passo_materiais (id_passo, id_material) VALUES (:id_passo, :id_material)")
                            ->execute([':id_passo' => $id_passo, ':id_material' => $id_mat]);
                    }
                }

                // Ferramentas
                if (!empty($comp['ferramentas'])) {
                    foreach ($comp['ferramentas'] as $id_ferr) {
                        $pdo->prepare("INSERT INTO componente_ferramentas (id_componente, id_ferramenta) VALUES (:id_componente, :id_ferramenta)")
                            ->execute([':id_componente' => $id_componente, ':id_ferramenta' => $id_ferr]);

                        $pdo->prepare("INSERT INTO passo_ferramentas (id_passo, id_ferramenta) VALUES (:id_passo, :id_ferramenta)")
                            ->execute([':id_passo' => $id_passo, ':id_ferramenta' => $id_ferr]);
                    }
                }
            }
        }

        $pdo->commit();
        unset($_SESSION['componentes_temp']);

        header("Location: produto.php");
        exit();

    } catch (Exception $e) {
        $pdo->rollBack();
        die("Erro ao cadastrar produto: " . $e->getMessage());
    }
}
?>
