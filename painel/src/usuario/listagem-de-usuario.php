<?php
session_start();

if (!isset($_SESSION['username'])) {
    header('Location: ../../index.php');
    exit;
}

function carregarUsuarios() {
    $usuariosJson = file_get_contents(__DIR__ . '/../data/usuarios.json');
    return json_decode($usuariosJson, true);
}

function salvarUsuarios($usuarios) {
    $usuariosJson = json_encode($usuarios, JSON_PRETTY_PRINT);
    file_put_contents(__DIR__ . '/../data/usuarios.json', $usuariosJson);
}

function canChangeType($currentUserType, $targetUserType) {
    if ($currentUserType === 'dev') {
        return true;
    } elseif ($currentUserType === 'admin') {
        return in_array($targetUserType, ['normal', 'admin']);
    }
    return false;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && isset($_POST['usuario'])) {
        $action = $_POST['action'];
        $usuario = $_POST['usuario'];

        $usuarios = carregarUsuarios();

        if ($action === 'delete') {
            foreach ($usuarios as $key => $user) {
                if ($user['usuario'] === $usuario) {
                    unset($usuarios[$key]);
                    salvarUsuarios($usuarios);
                    if ($_SESSION['username'] === $usuario) {
                        session_destroy();
                        header('Location: ../../index.php');
                        exit;
                    }
                    break;
                }
            }
        } elseif ($action === 'disable') {
            foreach ($usuarios as $key => $user) {
                if ($user['usuario'] === $usuario) {
                    if ($user['ativo']) {
                        $usuarios[$key]['ativo'] = false;
                        salvarUsuarios($usuarios);
                        if ($_SESSION['username'] === $usuario) {
                            session_destroy();
                            header('Location: ../../index.php');
                            exit;
                        }
                    }
                    break;
                }
            }
        } elseif ($action === 'enable') {
            foreach ($usuarios as $key => $user) {
                if ($user['usuario'] === $usuario) {
                    $usuarios[$key]['ativo'] = true;
                    salvarUsuarios($usuarios);
                    break;
                }
            }
        } elseif ($action === 'change_type') {
            foreach ($usuarios as $key => $user) {
                if ($user['usuario'] === $usuario) {
                    if (canChangeType($_SESSION['tipo'], $user['tipo'])) {
                        if ($user['tipo'] === 'admin') {
                            $usuarios[$key]['tipo'] = 'normal';
                        } elseif ($user['tipo'] === 'normal') {
                            $usuarios[$key]['tipo'] = 'admin';
                        }
                        salvarUsuarios($usuarios);
                    }
                    break;
                }
            }
        } elseif ($action === 'edit') {
            header('Location: editar-usuario.php?usuario=' . $usuario);
            exit;
        }
    }
}

$usuarios = carregarUsuarios();

if (isset($_POST['search']) && !empty($_POST['search'])) {
    $search = strtolower(trim($_POST['search']));
    $usuarios = array_filter($usuarios, function ($usuario) use ($search) {
        return strpos(strtolower($usuario['nome_completo']), $search) !== false ||
               strpos(strtolower($usuario['usuario']), $search) !== false ||
               strpos(strtolower($usuario['email_institucional']), $search) !== false ||
               strpos(strtolower($usuario['telefone']), $search) !== false;
    });
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listagem de Usuários</title>
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
                <h2>Listagem de Usuários</h2>
                <div class="container-buscar">
                    <input type="text" id="searchInput" placeholder="Pesquisar...">
                    <button onclick="filterUsers()">Pesquisar</button>
                </div>
                <div class="container-listagem-usuario">
                    <table id="userTable">
                        <thead>
                            <tr>
                                <th>Nome Completo</th>
                                <th>Usuário</th>
                                <th>Email Institucional</th>
                                <th>Telefone</th>
                                <th>Data de Cadastro</th>
                                <th>Tipo</th>
                                <th>Status Ativo</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($usuarios as $usuario): ?>
                                <?php if ($_SESSION['tipo'] === 'admin' && $usuario['tipo'] === 'dev') continue; ?>
                                <tr>
                                    <td><?= $usuario['nome_completo'] ?></td>
                                    <td><?= $usuario['usuario'] ?></td>
                                    <td><?= $usuario['email_institucional'] ?></td>
                                    <td><?= $usuario['telefone'] ?></td>
                                    <td><?= $usuario['data_cadastro'] ?></td>
                                    <td><?= ucfirst($usuario['tipo']) ?></td>
                                    <td><?= $usuario['ativo'] ? 'Ativo' : 'Inativo' ?></td>
                                    <td>
                                        <form method="POST" action="">
                                            <input type="hidden" name="usuario" value="<?= $usuario['usuario'] ?>">
                                            <button type="submit" name="action" value="edit">Editar</button>
                                            <button type="submit" name="action" value="delete">Excluir</button>
                                            <?php if ($usuario['ativo']): ?>
                                                <button type="submit" name="action" value="disable">Desativar</button>
                                            <?php else: ?>
                                                <button type="submit" name="action" value="enable">Ativar</button>
                                            <?php endif; ?>
                                            <?php if (canChangeType($_SESSION['tipo'], $usuario['tipo'])): ?>
                                                <button type="submit" name="action" value="change_type">Trocar Tipo</button>
                                            <?php endif; ?>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <?php 
    $baseDirCss =  '../../';
    include '../../js.php';?>

    <script>
        function filterUsers() {
            var input, filter, table, tr, td, i, j, txtValue, match;
            input = document.getElementById("searchInput");
            filter = input.value.toLowerCase();
            table = document.getElementById("userTable");
            tr = table.getElementsByTagName("tr");

            for (i = 1; i < tr.length; i++) { 
                tr[i].style.display = "none"; 
                td = tr[i].getElementsByTagName("td");
                for (j = 0; j < td.length; j++) {
                    if (td[j]) {
                        txtValue = td[j].textContent || td[j].innerText;
                        if (txtValue.toLowerCase().indexOf(filter) > -1) {
                            tr[i].style.display = ""; 
                            break;
                        }
                    }
                }
            }
        }
    </script>
</body>
</html>