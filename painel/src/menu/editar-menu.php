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

$sistema = carregarMenus();

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    if (isset($sistema[$id])) {
        $labelAtual = $sistema[$id]['label'];
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $novaLabel = $_POST['label'];
            
            if ($novaLabel !== $labelAtual) {
                $labelJaExiste = false;
                foreach ($sistema as $menu) {
                    if ($menu['label'] === $novaLabel) {
                        $labelJaExiste = true;
                        break;
                    }
                }
                
                if (!$labelJaExiste) {
                    $sistema[$id]['label'] = $novaLabel;
                    
                    $nomeAntigo = $sistema[$id]['nome'];
                    $novoNome = strtolower(str_replace(' ', '-', $novaLabel));
                    $sistema[$id]['nome'] = $novoNome;
                    $sistema[$id]['arquivo'] = $novoNome . '.json';
                    
                    $caminhoAntigo = $caminhoData . $nomeAntigo . '.json';
                    $caminhoNovo = $caminhoData . $novoNome . '.json';
                    rename($caminhoAntigo, $caminhoNovo);
                    
                    salvarSistema($sistema);
                    
                    header('Location: ./listar-menu.php');
                    exit;
                } else {
                    echo '<p class="mensagem-de-erro">Esta label já está sendo utilizada. Por favor, escolha outra.</p>';
                }
            } else {
                echo '<p class="mensagem-de-sucesso">A nova label é igual à atual. Nenhuma alteração foi realizada.</p>';
            }
        }
    } else {
        echo '<p class="mensagem-de-erro">ID de menu inválido.</p>';
    }
} else {
    echo '<p class="mensagem-de-erro">ID de menu não fornecido.</p>';
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Menu</title>
    <?php 
    $baseDirCss = './../../';
    include '../../css.php'; ?>   
</head>
<body>
    <?php 
        $baseDir = './../../';
        include '../../menu.php';
        renderMenu($baseDir);
    ?>    

    <main>
        <div class="container-conteudo">
            <div class="container">
                <h2>Editar Menu</h2>
                
                <?php if (isset($id) && isset($sistema[$id])): ?>
                    <form method="POST">
                        <label for="label">Label do Menu:</label>
                        <input type="text" id="label" name="label" value="<?= $sistema[$id]['label'] ?>" required>
                        <button type="submit">Salvar</button>
                    </form>
                <?php else: ?>
                    <p>Erro ao carregar o menu para edição.</p>
                <?php endif; ?>
                
                <!-- <a href="./listar-menu.php">Voltar para a Lista de Menus</a> -->
            </div>
        </div>
    </main>    
    <?php include '../../js.php'; ?>   
</body>
</html>