<?php
session_start();

if (!isset($_SESSION['username'])) {
    header('Location: ../../index.php');
    exit;
}

$caminhoData = './../data/';

$arquivoSistema = $caminhoData . 'sistema.json';

function carregarMenus() {
    global $arquivoSistema;
    
    if (file_exists($arquivoSistema)) {
        $sistema = json_decode(file_get_contents($arquivoSistema), true);
        return $sistema;
    }
    
    return [];
}

function salvarSistema($sistema) {
    global $arquivoSistema;
    
    file_put_contents($arquivoSistema, json_encode($sistema, JSON_PRETTY_PRINT));
}

function excluirArquivoMenu($nomeArquivo) {
    global $caminhoData;
    
    $caminhoArquivo = $caminhoData . $nomeArquivo;
    
    if (file_exists($caminhoArquivo)) {
        unlink($caminhoArquivo);
    }
}

$sistema = carregarMenus();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['id'])) {
        $id = $_POST['id'];
        
        if (isset($sistema[$id])) {
            if (isset($_POST['action']) && $_POST['action'] === 'change_active') {
                $sistema[$id]['ativo'] = !$sistema[$id]['ativo'];
                salvarSistema($sistema);
            }
            
            if (isset($_POST['action']) && $_POST['action'] === 'delete') {
                $nomeArquivo = $sistema[$id]['arquivo'];
                
                excluirArquivoMenu($nomeArquivo);
                
                unset($sistema[$id]);
                
                salvarSistema($sistema);
            }
            
            header('Location: ./listar-menu.php');
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Listar Menus Cadastrados</title>
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
        renderMenu($baseDir); ?>    
        
        <div class="container-conteudo">
            <div class="container">
                <h2>Listar Menus Cadastrados</h2>
    
                <div class="container-listagem-usuario ">
                    <?php if (empty($sistema)): ?>
                        <p>Nenhum menu cadastrado.</p>
                    <?php else: ?>
                        <table border="1">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Label</th>
                                    <th>Nome</th>
                                    <th>Data de Cadastro</th>
                                    <th>Ativo</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($sistema as $id => $menu): ?>
                                    <tr>
                                        <td><?= $id ?></td>
                                        <td><?= $menu['label'] ?></td>
                                        <td><?= $menu['nome'] ?></td>
                                        <td><?= $menu['data_cadastro'] ?></td>
                                        <td><?= $menu['ativo'] ? 'Sim' : 'Não' ?></td>
                                        <td>
                                            <form method="POST">
                                                <input type="hidden" name="id" value="<?= $id ?>">
                                                
                                                <button type="submit" name="action" value="change_active">
                                                    <?= $menu['ativo'] ? 'Desativar' : 'Ativar' ?>
                                                </button>
    
                                                <a href="editar-menu.php?id=<?= $id ?>">Editar</a>
                                                
                                                <button type="submit" name="action" value="delete">Excluir</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>
    <?php include '../../js.php'; ?>
</body>
</html>