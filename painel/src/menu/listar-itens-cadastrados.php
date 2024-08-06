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

    // Busca pelo menu no sistema utilizando o nome tratado
    $menu = buscarMenuPorNome($sistema, $nomeMenu);

    if ($menu && isset($menu['arquivo'])) {
        $arquivoCadastro = './../data/' . $menu['arquivo'];

        if (file_exists($arquivoCadastro)) {
            $registros = json_decode(file_get_contents($arquivoCadastro), true);

            echo '<!DOCTYPE html>';
            echo '<html lang="pt-br">';
            echo '<head>';
            echo '<meta charset="UTF-8">';
            echo '<title>Listar Itens Cadastrados - ' . ucfirst($menu['label']) . '</title>';
            
            $baseDirCss = '../../';
            include '../../css.php';
            echo '</head>';
            echo '<body>';
            
            echo '<main>';

            $baseDir = './../../';
            include '../../menu.php';
            renderMenu($baseDir);

            echo '<div class="container-conteudo">';
            echo '<div class="container">';

            echo '<h2>Listar Itens Cadastrados - ' . ucfirst($menu['label']) . '</h2>';

            echo '<div class="container-buscar">';
            echo '<input type="text" id="searchInput" placeholder="Pesquisar...">';
            echo '<button onclick="filterUsers()">Pesquisar</button>';
            echo '</div>';
                

            if (isset($_GET['excluir']) && $_GET['excluir'] === 'true') {
                $idExcluir = $_GET['id'];
                $indiceExcluir = array_search($idExcluir, array_column($registros, 'id'));
                
                if ($indiceExcluir !== false) {
                    unset($registros[$indiceExcluir]);
                    $registros = array_values($registros);
                    file_put_contents($arquivoCadastro, json_encode($registros, JSON_PRETTY_PRINT));
                    echo '<p class="mensagem-de-sucesso">Registro excluído com sucesso!</p>';
                } else {
                    echo '<p</p>';
                    echo '<p class="mensagem-de-erro">Falha ao excluir o registro.</p>';
                }
            }

            if (!empty($registros)) {
                echo '<div class="container-listagem-usuario">';
                echo '<table border="1" id="userTable">';
                echo '<thead>';
                echo '<tr>';
                foreach ($menu['campos'] as $campo) {
                    echo '<th>' . $campo['label'] . '</th>';
                }
                echo '<th>Status</th>'; 
                echo '<th>Ações</th>';
                echo '</tr>';
                echo '</thead>';
                echo '<tbody>';
                foreach ($registros as $registro) {
                    echo '<tr>';
                    foreach ($menu['campos'] as $campo) {
                        $nomeCampo = $campo['nome'];
                        echo '<td><div class="conteudo">' . $registro[$nomeCampo] . '</div></td>';
                    }
                    echo '<td>' . ($registro['ativo'] ? 'Sim' : 'Não') . '</td>';
                    echo '<td>';
                    echo '<form>';
                    echo '<a href="editar-itens-cadastrados.php?campos=' . $nomeMenu . '&id=' . $registro['id'] . '">Editar</a> ';
                    echo '<a href="#" onclick="excluirItem(\'' . $nomeMenu . '\', ' . $registro['id'] . ');">Excluir</a> ';
                    if ($registro['ativo']) {
                        echo '<a href="ativar-desativar-itens-cadastrados.php?campos=' . $nomeMenu . '&id=' . $registro['id'] . '&ativo=0">Desativar</a>';
                    } else {
                        echo '<a href="ativar-desativar-itens-cadastrados.php?campos=' . $nomeMenu . '&id=' . $registro['id'] . '&ativo=1">Ativar</a>';
                    }
                    echo '</form>';
                    echo '</td>';
                    echo '</tr>';
                }
                echo '</tbody>';
                echo '</table>';
                echo '</div>';
            } else {
                echo '<p class="mensagem-de-erro">Não há registros a serem exibidos.</p>';
            }
            

            echo '</div>';
            echo '</div>';

            echo '</main>';

            $baseDirCss =  '../../';
            include '../../js.php';
            
            echo '<script>
                function excluirItem(nomeMenu, id) {
                    if (confirm("Tem certeza que deseja excluir este item?")) {
                        window.location.href = "listar-itens-cadastrados.php?campos=" + nomeMenu + "&id=" + id + "&excluir=true";
                    }
                }
            </script>';

            echo '
            <script>
                function filterUsers() {
                    var input, filter, table, tr, td, i, j, txtValue, found;
                    input = document.getElementById("searchInput");
                    filter = input.value.toLowerCase();
                    table = document.getElementById("userTable");
                    tr = table.getElementsByTagName("tr");
                    for (i = 0; i < tr.length; i++) {
                        tr[i].style.display = "none"; // Hide the row initially
                        td = tr[i].getElementsByTagName("td");
                        found = false;
                        for (j = 0; j < td.length; j++) {
                            if (td[j]) {
                                txtValue = td[j].textContent || td[j].innerText;
                                if (txtValue.toLowerCase().indexOf(filter) > -1) {
                                    found = true;
                                    break;
                                }
                            }
                        }
                        if (found) {
                            tr[i].style.display = "";
                        }
                    }
                }
            </script>';

            echo '</body>';
            echo '</html>';
        } else {
            echo '<p class="mensagem-de-erro">Arquivo de cadastro não encontrado.</p>';
        }
    } else {
        echo '<p class="mensagem-de-erro">Menu não encontrado ou arquivo de cadastro não especificado.</p>';
    }
} else {
    echo '<p class="mensagem-de-erro">Parâmetro "campos" não especificado na URL.</p>';
}