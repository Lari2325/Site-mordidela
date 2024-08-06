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

$labelMenu = 'Menu';
$menusDisponiveis = [];
$mostrarConcatenar = false;

if (isset($_GET['nome']) && !empty($_GET['nome'])) {
    $nomeMenuAtual = strtolower(str_replace(' ', '-', $_GET['nome']));
    $sistema = json_decode(file_get_contents($arquivoSistema), true);
    $menuAtual = buscarMenuPorNome($sistema, $nomeMenuAtual);

    if ($menuAtual) {
        $labelMenu = $menuAtual['label'];
        $menusDisponiveis = obterMenusDisponiveis($sistema, $nomeMenuAtual);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sistema = json_decode(file_get_contents($arquivoSistema), true);
    $nomeMenuAtual = strtolower(str_replace(' ', '-', $_POST['nomeMenu']));
    $menuAtual = buscarMenuPorNome($sistema, $nomeMenuAtual);

    if ($menuAtual) {
        $novaLabel = $_POST['label'];

        $labelsExistem = array_column($menuAtual['campos'], 'label');
        if (in_array($novaLabel, $labelsExistem)) {
            echo '<p class="mensagem-de-erro">Já existe um campo com esse nome. Escolha outro nome.</p>';
        } else {
            $nomeCampo = strtolower(str_replace(' ', '-', $novaLabel));

            $novoCampo = [
                'nome' => $nomeCampo,
                'label' => $novaLabel,
                'tipo' => $_POST['tipo']
            ];

            if ($_POST['tipo'] === 'select') {
                $menuConcatenar = $_POST['menuConcatenar'];
                $novoCampo['menu_concatenar'] = $menuConcatenar;
            }

            $menuAtual['campos'][] = $novoCampo;

            $sistema = atualizarMenuNoSistema($sistema, $menuAtual);
            file_put_contents($arquivoSistema, json_encode($sistema, JSON_PRETTY_PRINT));

            $arquivoMenu = './../data/' . $menuAtual['arquivo'];
            if (file_exists($arquivoMenu)) {
                $registros = json_decode(file_get_contents($arquivoMenu), true);

                foreach ($registros as &$registro) {
                    $registro[$nomeCampo] = "";
                }

                file_put_contents($arquivoMenu, json_encode($registros, JSON_PRETTY_PRINT));
            }

            header('Location: ./cadastrar-campos.php?nome=' . $nomeMenuAtual . '&success=true');
            exit;
        }
    }
}

function atualizarMenuNoSistema($sistema, $menuAtual) {
    $nomeMenuAtual = $menuAtual['nome'];
    foreach ($sistema as $indice => $menu) {
        if ($menu['nome'] === $nomeMenuAtual) {
            $sistema[$indice] = $menuAtual;
            break;
        }
    }
    return $sistema;
}

function obterTiposDeCampo() {
    return ['input', 'textarea', 'ckeditor', 'data', 'documento', 'select'];
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastrar Campos</title>
    <?php 
    $baseDirCss = '../../';
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
                <?php 
                if (isset($_GET['success']) && $_GET['success'] === 'true') {
                    echo '<p class="mensagem-de-sucesso">Campos cadastrados com sucesso!</p>';
                }
                ?>
                <h2>Cadastrar <?php echo ucfirst($labelMenu); ?></h2>

                <form method="POST" action="cadastrar-campos.php">
                    <input type="hidden" name="nomeMenu" value="<?php echo isset($_GET['nome']) ? $_GET['nome'] : ''; ?>">

                    <label for="label">Nome do Campo:</label>
                    <input type="text" id="label" name="label" required>

                    <label for="tipo">Tipo do Campo:</label>
                    <select id="tipo" name="tipo" required>
                        <?php 
                        $tiposDeCampo = obterTiposDeCampo();
                        foreach ($tiposDeCampo as $tipo) {
                            echo '<option value="' . $tipo . '">' . ucfirst($tipo) . '</option>';
                        }
                        ?>
                    </select>

                    <div id="divMenuConcatenar" class="hidden" style="width: 100%; max-width: 1100px;">
                        <label for="menuConcatenar">Concatenar com Menu:</label>
                        <select id="menuConcatenar" name="menuConcatenar" style="width: 100%">
                            <?php 
                            foreach ($menusDisponiveis as $nome => $label) {
                                echo '<option value="' . $nome . '">' . $label . '</option>';
                            }
                            ?>
                        </select>
                    </div>

                    <button type="submit">Cadastrar Campo</button>
                </form>
            </div>
        </div>
    </main>
    <?php include '../../js.php'; ?>   
    <script>
    document.getElementById('tipo').addEventListener('change', function() {
        var tipoSelecionado = this.value;
        var divMenuConcatenar = document.getElementById('divMenuConcatenar');
        if (tipoSelecionado === 'select') {
            divMenuConcatenar.classList.remove('hidden');
        } else {
            divMenuConcatenar.classList.add('hidden');
        }
    });
    </script>
</body>
</html>
