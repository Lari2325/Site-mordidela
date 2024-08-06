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

if (isset($_GET['nome']) && !empty($_GET['nome']) && isset($_GET['campo']) && !empty($_GET['campo'])) {
    $nomeMenuAtual = strtolower(str_replace(' ', '-', $_GET['nome']));
    $nomeCampoAtual = strtolower(str_replace(' ', '-', $_GET['campo']));
    
    $sistema = json_decode(file_get_contents($arquivoSistema), true);
    $menuAtual = buscarMenuPorNome($sistema, $nomeMenuAtual);

    if ($menuAtual) {
        $labelMenu = $menuAtual['label']; 
        $campos = $menuAtual['campos']; 
        
        $campoParaExcluirIndex = null;
        foreach ($campos as $index => $campo) {
            if ($campo['nome'] === $nomeCampoAtual) {
                $campoParaExcluirIndex = $index;
                break;
            }
        }

        if ($campoParaExcluirIndex === null) {
            echo '<p class="mensagem-de-erro">Campo não encontrado.</p>';
        } else {
            array_splice($campos, $campoParaExcluirIndex, 1);

            foreach ($sistema as &$menu) {
                if ($menu['nome'] === $nomeMenuAtual) {
                    $menu['campos'] = $campos;
                    break;
                }
            }

            file_put_contents($arquivoSistema, json_encode($sistema, JSON_PRETTY_PRINT));

            $arquivoMenu = './../data/' . $menuAtual['arquivo'];
            if (file_exists($arquivoMenu)) {
                $registros = json_decode(file_get_contents($arquivoMenu), true);

                foreach ($registros as &$registro) {
                    unset($registro[$nomeCampoAtual]); 
                }

                file_put_contents($arquivoMenu, json_encode($registros, JSON_PRETTY_PRINT));
            }

            echo '<p class="mensagem-de-sucesso">Campo excluído com sucesso!</p>';
        }
    } else {
        echo '<p class="mensagem-de-erro">Menu não encontrado.</p>';
    }
}

if (isset($_GET['nome']) && !empty($_GET['nome'])) {
    $nomeMenuAtual = strtolower(str_replace(' ', '-', $_GET['nome']));
    $sistema = json_decode(file_get_contents($arquivoSistema), true);
    $menuAtual = buscarMenuPorNome($sistema, $nomeMenuAtual);

    if ($menuAtual) {
        $labelMenu = $menuAtual['label']; 
        $campos = $menuAtual['campos']; 

        function compararCamposPorLabel($a, $b) {
            return strcmp($a['label'], $b['label']);
        }

        usort($campos, 'compararCamposPorLabel');

        
    } else {
        echo '<p class="mensagem-de-erro">Menu não encontrado.</p>';
    }
} else {
    echo '<p class="mensagem-de-erro">Nome de menu inválido.</p>';
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Listar Campos - <?php echo isset($labelMenu) ? ucfirst($labelMenu) : 'Menu não encontrado'; ?></title>
    <?php 
        $baseDirCss = '../../';
        include '../../css.php'; 
    ?>  
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
                <h2>Listar Campos - <?php echo ucfirst($labelMenu); ?></h2>
                
                <?php 

                if (empty($campos)) {
                    echo '<p class="mensagem-de-erro">Não há campos cadastrados para este menu.</p>';
                }
                
                if (isset($labelMenu) && !empty($campos)): ?>
                <div class="container-listagem-usuario">
                    <table border="1">
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>Tipo</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($campos as $campo): ?>
                                <tr>
                                    <td><?php echo $campo['label']; ?></td>
                                    <td><?php echo ucfirst($campo['tipo']); ?></td>
                                    <td>
                                        <a href="editar-campo.php?nomeMenu=<?php echo $nomeMenuAtual; ?>&campo=<?php echo $campo['nome']; ?>">Editar</a>
                                        <a href="?nome=<?php echo $nomeMenuAtual; ?>&campo=<?php echo $campo['nome']; ?>">Excluir</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
    <?php include '../../js.php'; ?>
</body>
</html>