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

// Verifica se os parâmetros necessários foram passados na URL
if (isset($_GET['campos']) && !empty($_GET['campos']) && isset($_GET['id']) && !empty($_GET['id'])) {
    $nomeMenu = $_GET['campos'];
    $idItem = $_GET['id'];

    $sistema = json_decode(file_get_contents($arquivoSistema), true);

    // Busca pelo menu no sistema utilizando o nome tratado
    $menu = buscarMenuPorNome($sistema, $nomeMenu);

    if ($menu && isset($menu['arquivo'])) {
        $arquivoCadastro = './../data/' . $menu['arquivo'];

        // Verifica se o arquivo de cadastro existe
        if (file_exists($arquivoCadastro)) {
            $registros = json_decode(file_get_contents($arquivoCadastro), true);

            // Encontrar o registro pelo ID
            $registroParaEditar = null;
            foreach ($registros as &$registro) {
                if ($registro['id'] == $idItem) {
                    // Inverte o valor do campo 'ativo'
                    $registro['ativo'] = !$registro['ativo'];
                    $registroParaEditar = $registro;
                    break;
                }
            }

            if (!$registroParaEditar) {
                echo '<p class="mensagem-de-erro">Registro não encontrado.</p>';
                header ('Location: ../../dashboard.php');
                exit;
            }

            // Salvar as alterações de volta no arquivo de cadastro
            file_put_contents($arquivoCadastro, json_encode($registros, JSON_PRETTY_PRINT));

            $statusAcao = $registroParaEditar['ativo'] ? 'ativado' : 'desativado';
            echo '<p style="color: green;">Registro ' . $statusAcao . ' com sucesso!</p>';
            header('Location: ./listar-itens-cadastrados.php?campos=' . $nomeMenu );
        } else {
            echo '<p class="mensagem-de-erro">Arquivo de cadastro não encontrado.</p>';
            header ('Location: ../../dashboard.php');
        }
    } else {
        echo '<p class="mensagem-de-erro">Menu não encontrado ou arquivo de cadastro não especificado.</p>';
        header ('Location: ../../dashboard.php');
    }
} else {
    echo '<p class="mensagem-de-erro">Parâmetros inválidos na URL.</p>';
    header ('Location: ../../dashboard.php');
}