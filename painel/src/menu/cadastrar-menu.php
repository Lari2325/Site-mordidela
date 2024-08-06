<?php
session_start();

if (!isset($_SESSION['username'])) {
    header('Location: ../../index.php');
    exit;
}

$caminhoData = './../data/';

$arquivoSistema = $caminhoData . 'sistema.json';

function labelJaExiste($sistema, $novaLabel) {
    foreach ($sistema as $item) {
        if ($item['label'] === $novaLabel) {
            return true;
        }
    }
    return false;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $novaLabel = $_POST['label'];

    $sistema = json_decode(file_get_contents($arquivoSistema), true);

    if (labelJaExiste($sistema, $novaLabel)) {
        echo '<p class="mensagem-de-erro">Este nome já está sendo utilizado. Por favor, escolha outro.</p>';
    } else {
        $novoId = count($sistema) + 1;

        $novoMenu = [
            "label" => $novaLabel,
            "nome" => strtolower(str_replace(' ', '-', $novaLabel)),
            "arquivo" => strtolower(str_replace(' ', '-', $novaLabel)) . '.json',
            "campos" => [],
            "data_cadastro" => date('d-m-Y'), 
            "ativo" => true 
        ];

        $sistema[$novoId] = $novoMenu;

        file_put_contents($arquivoSistema, json_encode($sistema, JSON_PRETTY_PRINT));

        $caminhoNovoArquivo = $caminhoData . $novoMenu['arquivo'];
        file_put_contents($caminhoNovoArquivo, json_encode([], JSON_PRETTY_PRINT));

        header('Location: ./cadastrar-menu.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastrar Novo Menu</title>
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
                <h2>Cadastrar Novo Menu</h2>
                <form method="POST" action="cadastrar-menu.php">
                    <label for="label">Nome do Menu:</label>
                    <input type="text" id="label" name="label" required>
                    <button type="submit">Cadastrar</button>
                </form>
            </div>
        </div> 
    </main>
    <?php include '../../js.php'; ?>   
</body>
</html>