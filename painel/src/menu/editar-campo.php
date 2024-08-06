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

function obterMenusDisponiveis($sistema, $nomeMenuAtual) {
    $menusDisponiveis = [];
    foreach ($sistema as $menu) {
        if ($menu['nome'] !== $nomeMenuAtual) {
            $menusDisponiveis[$menu['nome']] = $menu['label'];
        }
    }
    return $menusDisponiveis;
}

if (isset($_GET['nomeMenu']) && !empty($_GET['nomeMenu']) && isset($_GET['campo']) && !empty($_GET['campo'])) {
    $nomeMenuAtual = strtolower(str_replace(' ', '-', $_GET['nomeMenu']));
    $nomeCampoAtual = strtolower(str_replace(' ', '-', $_GET['campo']));
    
    $sistema = json_decode(file_get_contents($arquivoSistema), true);
    $menuAtual = buscarMenuPorNome($sistema, $nomeMenuAtual);

    if ($menuAtual) {
        $labelMenu = $menuAtual['label']; 
        $campos = $menuAtual['campos']; 
        
        $campoParaEditar = null;
        foreach ($campos as $index => $campo) {
            if ($campo['nome'] === $nomeCampoAtual) {
                $campoParaEditar = &$campos[$index];
                break;
            }
        }

        if (!$campoParaEditar) {
            header('Location: ../../dashboard.php');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $novaLabel = $_POST['label'];
            $novoTipo = $_POST['tipo'];

            $labelsExistem = array_column($campos, 'label');
            if (in_array($novaLabel, $labelsExistem) && $novaLabel !== $campoParaEditar['label']) {
                echo '<p class="mensagem-de-erro">Já existe um campo com essa label. Escolha outra label.</p>';
            } else {
                $campoParaEditar['label'] = $novaLabel;
                $campoParaEditar['tipo'] = $novoTipo;

                // Verifica se é campo do tipo select para atualizar o menu concatenar
                if ($novoTipo === 'select') {
                    $menuConcatenar = $_POST['menuConcatenar'];
                    $campoParaEditar['menu_concatenar'] = $menuConcatenar;
                } else {
                    // Remove o campo menu_concatenar se não for do tipo select
                    unset($campoParaEditar['menu_concatenar']);
                }

                $nomeCampo = strtolower(str_replace(' ', '-', $novaLabel));
                $campoParaEditar['nome'] = $nomeCampo;

                foreach ($sistema as &$menu) {
                    if ($menu['nome'] === $nomeMenuAtual) {
                        $menu['campos'] = $campos;
                        break;
                    }
                }

                file_put_contents($arquivoSistema, json_encode($sistema, JSON_PRETTY_PRINT));

                echo '<p class="mensagem-de-sucesso">Campo atualizado com sucesso!</p>';
            }
        }
    } else {
        echo '<p>Menu não encontrado.</p>';
        exit;
    }
} else {
    echo '<p>Parâmetros inválidos na URL.</p>';
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Campo - <?php echo ucfirst($labelMenu); ?></title>
    <?php 
    $baseDirCss ='../../';
    include '../../css.php'; ?>   
    <style>
    .hidden {
        display: none;
    }
    </style>
</head>
<body>

    <main>
        <?php 
        $baseDir = './../../';
        include '../../menu.php'; 
        renderMenu($baseDir);
        ?> 
    
        <div class="container-conteudo">
            <div class="container">
                <h2>Editar Campo - <?php echo ucfirst($labelMenu); ?></h2>
            
                <form method="POST" action="editar-campo.php?nomeMenu=<?php echo $nomeMenuAtual; ?>&campo=<?php echo $nomeCampoAtual; ?>">
                    <label for="label">Nova Label do Campo:</label>
                    <input type="text" id="label" name="label" value="<?php echo $campoParaEditar['label']; ?>" required>
            
                    <label for="tipo">Novo Tipo do Campo:</label>
                    <select id="tipo" name="tipo" required>
                        <?php 
                        $tiposDeCampo = ['input', 'textarea', 'ckeditor', 'data', 'documento', 'select'];
                        foreach ($tiposDeCampo as $tipo) {
                            $selected = ($campoParaEditar['tipo'] === $tipo) ? 'selected' : '';
                            echo '<option value="' . $tipo . '" ' . $selected . '>' . ucfirst($tipo) . '</option>';
                        }
                        ?>
                    </select>
            
                    <div id="divMenuConcatenar" style="width: 100%; max-width: 1100px;" class="<?php echo ($campoParaEditar['tipo'] === 'select') ? '' : 'hidden'; ?>">
                        <label for="menuConcatenar">Concatenar com Menu:</label>
                        <select id="menuConcatenar" name="menuConcatenar">
                            <?php 
                            $menusDisponiveis = obterMenusDisponiveis($sistema, $nomeMenuAtual);
                            foreach ($menusDisponiveis as $nome => $label) {
                                $selected = ($campoParaEditar['menu_concatenar'] === $nome) ? 'selected' : '';
                                echo '<option value="' . $nome . '" ' . $selected . '>' . $label . '</option>';
                            }
                            ?>
                        </select>
                    </div>
            
                    <button type="submit">Salvar Alterações</button>
                </form>
            </div>
        </div>
    </main>

    <?php include '../../js.php'; ?>   
    <script>
    // Script para mostrar/ocultar o campo "Concatenar com Menu" com base no tipo de campo selecionado
    document.addEventListener('DOMContentLoaded', function() {
        var tipoCampoSelect = document.getElementById('tipo');
        var divMenuConcatenar = document.getElementById('divMenuConcatenar');

        tipoCampoSelect.addEventListener('change', function() {
            if (this.value === 'select') {
                divMenuConcatenar.classList.remove('hidden');
            } else {
                divMenuConcatenar.classList.add('hidden');
            }
        });

        // Verifica o tipo inicial ao carregar a página
        if (tipoCampoSelect.value === 'select') {
            divMenuConcatenar.classList.remove('hidden');
        } else {
            divMenuConcatenar.classList.add('hidden');
        }
    });
    </script>
</body>
</html>