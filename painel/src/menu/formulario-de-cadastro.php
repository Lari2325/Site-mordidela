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

if (isset($_GET['campos']) && !empty($_GET['campos'])) {
    $nomeMenu = $_GET['campos'];

    $sistema = json_decode(file_get_contents($arquivoSistema), true);

    $menu = buscarMenuPorNome($sistema, $nomeMenu);

    if ($menu && isset($menu['arquivo'])) {
        $arquivoCadastro = './../data/' . $menu['arquivo'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $novoRegistro = [];

            foreach ($menu['campos'] as $campo) {
                $nomeCampo = $campo['nome'];
                $labelCampo = $campo['label'];
                $tipoCampo = $campo['tipo'];

                switch ($tipoCampo) {
                    case 'input':
                    case 'textarea':
                    case 'data':
                        if (isset($_POST[$nomeCampo])) {
                            $novoRegistro[$nomeCampo] = $_POST[$nomeCampo];
                        }
                        break;
                    case 'ckeditor':
                        if (isset($_POST[$nomeCampo])) {
                            $conteudoHTML = $_POST[$nomeCampo];
                            $novoRegistro[$nomeCampo] = $conteudoHTML;
                        }
                        break;
                    case 'documento':
                        if (isset($_FILES[$nomeCampo]) && $_FILES[$nomeCampo]['error'] === UPLOAD_ERR_OK) {
                            $nomeOriginal = $_FILES[$nomeCampo]['name'];
                            $extensao = pathinfo($nomeOriginal, PATHINFO_EXTENSION);
                            
                            $nomeUnico = uniqid() . '.' . $extensao;
                            $caminhoDestino = './../docs/' . $nomeUnico;
                            move_uploaded_file($_FILES[$nomeCampo]['tmp_name'], $caminhoDestino);
                            
                            $novoRegistro[$nomeCampo] = $nomeUnico;
                        }
                        break;
                    case 'select':
                        if (isset($_POST[$nomeCampo])) {
                            $valorSelecionado = $_POST[$nomeCampo];
                            $novoRegistro[$nomeCampo] = $valorSelecionado;
                        }
                        break;
                    default:
                        break;
                }
            }

            $novoRegistro['ativo'] = true;

            $registros = [];
            if (file_exists($arquivoCadastro)) {
                $registros = json_decode(file_get_contents($arquivoCadastro), true);
            }

            $id = 1;
            if (!empty($registros)) {
                $lastRecord = end($registros);
                $id = intval($lastRecord['id']) + 1;
            }
            
            $novoRegistro['id'] = $id;

            $registros[] = $novoRegistro;

            file_put_contents($arquivoCadastro, json_encode($registros, JSON_PRETTY_PRINT));

            echo '<p class="mensagem-de-sucesso">Registro cadastrado com sucesso!</p>';
        }

        echo '<!DOCTYPE html>';
        echo '<html lang="pt-br">';
        echo '<head>';
        echo '<meta charset="UTF-8">';
        echo '<title>Cadastrar ' . ucfirst($menu['label']) . '</title>';
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

        echo '<h2>Cadastrar ' . ucfirst($menu['label']) . '</h2>';
        echo '<form method="POST" action="formulario-de-cadastro.php?campos=' . $nomeMenu . '" enctype="multipart/form-data">';

        // Renderizar campos do formulário com base no menu selecionado
        foreach ($menu['campos'] as $campo) {
            $nomeCampo = $campo['nome'];
            $labelCampo = $campo['label'];
            $tipoCampo = $campo['tipo'];

            switch ($tipoCampo) {
                case 'input':
                    echo '<label for="' . $nomeCampo . '">' . $labelCampo . ':</label>';
                    echo '<input type="text" id="' . $nomeCampo . '" name="' . $nomeCampo . '" required>';
                    break;
                case 'textarea':
                    echo '<label for="' . $nomeCampo . '">' . $labelCampo . ':</label>';
                    echo '<textarea id="' . $nomeCampo . '" name="' . $nomeCampo . '" rows="4" required></textarea>';
                    break;
                case 'ckeditor':
                    echo '<label for="' . $nomeCampo . '">' . $labelCampo . ':</label>';
                    echo '<textarea id="' . $nomeCampo . '" name="' . $nomeCampo . '" required></textarea>';
                    echo '<script src="https://cdn.ckeditor.com/4.19.1/standard-all/ckeditor.js"></script>';
                    echo '<script>CKEDITOR.replace("' . $nomeCampo . '", { width: "100%", height: "300px" });</script>';
                    break;
                case 'data':
                    echo '<label for="' . $nomeCampo . '">' . $labelCampo . ':</label>';
                    echo '<input type="date" id="' . $nomeCampo . '" name="' . $nomeCampo . '" required>';
                    break;
                case 'documento':
                    echo '<label for="' . $nomeCampo . '">' . $labelCampo . ':</label>';
                    echo '<input type="file" id="' . $nomeCampo . '" name="' . $nomeCampo . '" accept=".pdf, .doc, .jpg, .png" required>';
                    break;
                case 'select':
                    echo '<label for="' . $nomeCampo . '">' . $labelCampo . ':</label>';
                    echo '<select id="' . $nomeCampo . '" name="' . $nomeCampo . '" required>';
                                        
                    $menuConcatenar = $campo['menu_concatenar'];
                    $arquivoMenuConcatenar = './../data/' . $menuConcatenar . '.json';
                
                    if (file_exists($arquivoMenuConcatenar)) {
                        $registrosConcatenar = json_decode(file_get_contents($arquivoMenuConcatenar), true);
                        foreach ($registrosConcatenar as $registro) {
                            echo '<option value="' . htmlspecialchars($registro['nome']) . '">' . $registro['nome'] . '</option>';
                        }
                    }
                                        
                    echo '</select>';
                    break;
                default:
                    break;
            }
        }

        echo '<input type="hidden" name="ativo" value="true">';
        echo '<button type="submit">Cadastrar</button>';
        echo '</form>';

        echo '</div>';
        echo '</div>';
        echo '</main>';

        $baseDirCss =  '../../';
        include '../../js.php';

        echo '</body>';
        echo '</html>';
    } else {
        echo '<p class="mensagem-de-erro">Menu não encontrado ou arquivo de cadastro não especificado.</p>';
    }
} else {
    echo '<p class="mensagem-de-erro">Parâmetro "campos" não especificado na URL.</p>';
}
