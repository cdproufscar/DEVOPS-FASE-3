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
        $nome = $_POST['nome_produto'];
        $descricao = $_POST['descricao'];
        $para_quem = $_POST['para_quem'];
        $por_quem = $_POST['por_quem'];
        $testado_por = $_POST['testado_por'] ?? null;
        $por_que = $_POST['por_que'];
        $para_que = $_POST['para_que'];
        $pre_requisitos = $_POST['pre_requisitos'];
        $modo_de_uso = $_POST['modo_de_uso'];

        // Pasta de upload
        $uploadDir = "uploads/produtos/";
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

        // Imagens do produto
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

        // Arquivos do produto
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

        // Inserção do produto
        $stmt = $pdo->prepare("INSERT INTO produtos (
            id_usuario, nome_produto, descricao, para_quem, por_quem, testado_por, por_que, para_que, pre_requisitos, modo_de_uso, imagens, arquivos
        ) VALUES (
            :id_usuario, :nome_produto, :descricao, :para_quem, :por_quem, :testado_por, :por_que, :para_que, :pre_requisitos, :modo_de_uso, :imagens, :arquivos
        )");

        $stmt->execute([
            ':id_usuario'     => $id_usuario,
            ':nome_produto'   => $nome,
            ':descricao'      => $descricao,
            ':para_quem'      => $para_quem,
            ':por_quem'       => $por_quem,
            ':testado_por'    => $testado_por,
            ':por_que'        => $por_que,
            ':para_que'       => $para_que,
            ':pre_requisitos' => $pre_requisitos,
            ':modo_de_uso'    => $modo_de_uso,
            ':imagens'        => json_encode($imagens),
            ':arquivos'       => json_encode($arquivos),
        ]);

        $id_produto = $pdo->lastInsertId();

        // Componentes temporários
        if (!empty($_SESSION['componentes_temp'])) {
            foreach ($_SESSION['componentes_temp'] as $comp) {
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

                if (!empty($comp['materiais'])) {
                    foreach ($comp['materiais'] as $id_material => $dados) {
                        $pdo->prepare("INSERT INTO componente_materiais (
                            id_componente, id_material, quantidade, unidade
                        ) VALUES (?, ?, ?, ?)")
                        ->execute([
                            $id_componente,
                            $id_material,
                            $dados['quantidade'],
                            $dados['unidade']
                        ]);
                    }
                }

                if (!empty($comp['ferramentas'])) {
                    foreach ($comp['ferramentas'] as $id_ferramenta => $dados) {
                        $pdo->prepare("INSERT INTO componente_ferramentas (
                            id_componente, id_ferramenta, dimensoes
                        ) VALUES (?, ?, ?)")
                        ->execute([
                            $id_componente,
                            $id_ferramenta,
                            $dados['dimensoes']
                        ]);
                    }
                }

                if (!empty($comp['passos'])) {
                    foreach ($comp['passos'] as $passo) {
                        $stmtPasso = $pdo->prepare("INSERT INTO passo_a_passo (
                            id_componente, descricao
                        ) VALUES (?, ?)");

                        $stmtPasso->execute([
                            $id_componente,
                            $passo['descricao']
                        ]);

                        $id_passo = $pdo->lastInsertId();

                        if (!empty($passo['materiais'])) {
                            foreach ($passo['materiais'] as $id_mat) {
                                $pdo->prepare("INSERT INTO passo_materiais (
                                    id_passo, id_material
                                ) VALUES (?, ?)")
                                ->execute([$id_passo, $id_mat]);
                            }
                        }

                        if (!empty($passo['ferramentas'])) {
                            foreach ($passo['ferramentas'] as $id_ferr) {
                                $pdo->prepare("INSERT INTO passo_ferramentas (
                                    id_passo, id_ferramenta
                                ) VALUES (?, ?)")
                                ->execute([$id_passo, $id_ferr]);
                            }
                        }
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
