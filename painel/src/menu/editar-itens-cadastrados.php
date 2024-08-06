<?php
session_start();

if (!isset($_SESSION['username'])) {
    header('Location: ../../index.php');
    exit;
}

$arquivoSistema = './../data/sistema.json';

function buscarMenuPorNome($sistema, $nomeMenuAtual) {
    foreach ($sistema as $menu) {
        if ($menu['nome'] === $nomeMenuAtual) {
            return $menu;
        }
    }
    return null;
}

if (isset($_GET['campos']) && !empty($_GET['campos']) && isset($_GET['id']) && !empty($_GET['id'])) {
    $nomeMenu = $_GET['campos'];
    $idItem = $_GET['id'];

    $sistema = json_decode(file_get_contents($arquivoSistema), true);

    $menu = buscarMenuPorNome($sistema, $nomeMenu);

    if ($menu && isset($menu['arquivo'])) {
        $arquivoCadastro = './../data/' . $menu['arquivo'];

        if (file_exists($arquivoCadastro)) {
            $registros = json_decode(file_get_contents($arquivoCadastro), true);

            $registroParaEditar = null;
            foreach ($registros as $registro) {
                if ($registro['id'] == $idItem) {
                    $registroParaEditar = $registro;
                    break;
                }
            }

            if (!$registroParaEditar) {
                echo '<p class="mensagem-de-erro">Registro não encontrado.</p>';
                exit;
            }

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                foreach ($menu['campos'] as $campo) {
                    $nomeCampo = $campo['nome'];
                    if ($campo['tipo'] === 'documento') {
                        if (isset($_FILES[$nomeCampo]['name']) && !empty($_FILES[$nomeCampo]['name'])) {
                            $nomeArquivoOriginal = $_FILES[$nomeCampo]['name'];
                            $extensaoArquivo = pathinfo($nomeArquivoOriginal, PATHINFO_EXTENSION);

                            $nomeArquivoUnico = uniqid() . '.' . $extensaoArquivo;

                            $caminhoDestino = './../docs/' . $nomeArquivoUnico;
                            move_uploaded_file($_FILES[$nomeCampo]['tmp_name'], $caminhoDestino);

                            $registroParaEditar[$nomeCampo] = $nomeArquivoUnico;
                        }
                    } elseif (isset($_POST[$nomeCampo])) {
                        $registroParaEditar[$nomeCampo] = $_POST[$nomeCampo];
                    }
                }

                // Processar o conteúdo do CKEditor se presente
                foreach ($menu['campos'] as $campo) {
                    $nomeCampo = $campo['nome'];
                    if ($campo['tipo'] === 'ckeditor') {
                        if (isset($_POST[$nomeCampo])) {
                            // Capturar o conteúdo HTML do CKEditor
                            $conteudoHTML = $_POST[$nomeCampo];

                            // Atualizar o registro existente com o conteúdo do CKEditor
                            $registroParaEditar[$nomeCampo] = $conteudoHTML;
                        }
                    }
                }

                // Atualizar o registro no array de registros
                foreach ($registros as &$registro) {
                    if ($registro['id'] == $idItem) {
                        $registro = $registroParaEditar;
                        break;
                    }
                }

                // Salvar de volta no arquivo JSON
                file_put_contents($arquivoCadastro, json_encode($registros, JSON_PRETTY_PRINT));

                echo '<p class="mensagem-de-sucesso">Registro editado com sucesso!</p>';
            }

            // Início do HTML para exibir o formulário de edição
            echo '<!DOCTYPE html>';
            echo '<html lang="pt-br">';
            echo '<head>';
            echo '<meta charset="UTF-8">';
            echo '<title>Editar Item Cadastrado - ' . ucfirst($menu['label']) . '</title>';
            $baseDirCss = './../../';
            include '../../css.php';
            echo '</head>';
            echo '<body>';

            $baseDir = './../../';
            include '../../menu.php';
            renderMenu($baseDir);

            echo '<main>';
            echo '<div class="container-conteudo">';
            echo '<div class="container">'; 

            echo '<h2>Editar Item Cadastrado - ' . ucfirst($menu['label']) . '</h2>';
            echo '<form method="POST" action="editar-itens-cadastrados.php?campos=' . $nomeMenu . '&id=' . $idItem . '" enctype="multipart/form-data">';

            foreach ($menu['campos'] as $campo) {
                $nomeCampo = $campo['nome'];
                $labelCampo = $campo['label'];
                $valorCampo = isset($registroParaEditar[$nomeCampo]) ? $registroParaEditar[$nomeCampo] : '';

                echo '<label for="' . $nomeCampo . '">' . $labelCampo . ':</label>';

                switch ($campo['tipo']) {
                    case 'input':
                    case 'textarea':
                        echo '<input type="' . $campo['tipo'] . '" id="' . $nomeCampo . '" name="' . $nomeCampo . '" value="' . htmlspecialchars($valorCampo) . '" required>';
                        break;
                    case 'ckeditor':
                        echo '<textarea id="' . $nomeCampo . '" name="' . $nomeCampo . '" required>' . htmlspecialchars($valorCampo) . '</textarea>';
                        echo '<script src="https://cdn.ckeditor.com/4.19.1/standard-all/ckeditor.js"></script>';
                        echo '<script>CKEDITOR.replace("' . $nomeCampo . '", { width: "100%", height: "300px" });</script>';
                        break;
                    case 'data':
                        echo '<input type="date" id="' . $nomeCampo . '" name="' . $nomeCampo . '" value="' . htmlspecialchars($valorCampo) . '" required>';
                        break;
                    case 'documento':
                        if (!empty($valorCampo)) {
                            echo '<p style="width: 100%; max-width: 1100px; color: #fff;">Arquivo atual: ' . $valorCampo . '</p>';
                        }
                        echo '<input type="file" id="' . $nomeCampo . '" name="' . $nomeCampo . '" accept=".pdf, .doc, .jpg, .png">';
                        break;
                    case 'select':
                        echo '<select id="' . $nomeCampo . '" name="' . $nomeCampo . '" required>';

                        $menuConcatenar = $campo['menu_concatenar'];
                        $arquivoMenuConcatenar = './../data/' . $menuConcatenar . '.json';

                        if (file_exists($arquivoMenuConcatenar)) {
                            $registrosConcatenar = json_decode(file_get_contents($arquivoMenuConcatenar), true);
                            foreach ($registrosConcatenar as $registro) {
                                $selected = ($registro['nome'] === $valorCampo) ? 'selected' : '';
                                echo '<option value="' . htmlspecialchars($registro['nome']) . '" ' . $selected . '>' . $registro['nome'] . '</option>';
                            }
                        }

                        echo '</select>';
                        break;
                    default:
                        echo '<p class="mensagem-de-erro">Tipo de campo não reconhecido.</p>';
                        break;
                }
            }

            echo '<button type="submit">Salvar Alterações</button>';
            echo '</form>';

            echo '</div>'; 
            echo '</div>'; 
            echo '</main>';

            include '../../js.php';
            echo '</body>';
            echo '</html>';
        } else {
            echo '<p class="mensagem-de-erro">Arquivo de cadastro não encontrado.</p>';
        }
    } else {
        echo '<p class="mensagem-de-erro">Menu não encontrado ou arquivo de cadastro não especificado.</p>';
    }
} else {
    echo '<p class="mensagem-de-erro">Parâmetros inválidos na URL.</p>';
}