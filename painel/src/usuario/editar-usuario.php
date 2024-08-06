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

if (!isset($_SESSION['username'])) {
    header('Location: ../../index.php');
    exit;
}

$usuarios = carregarUsuarios();

// Verificar se o parâmetro do usuário está presente na URL
if (isset($_GET['usuario'])) {
    $usuarioEditar = $_GET['usuario'];

    // Verificar se o usuário a ser editado existe no array de usuários
    $usuarioEncontrado = null;
    foreach ($usuarios as $key => $usuario) {
        if ($usuario['usuario'] === $usuarioEditar) {
            $usuarioEncontrado = &$usuarios[$key]; // Referência para o usuário encontrado no array
            break;
        }
    }

    // Se o usuário não foi encontrado, redirecionar de volta para a página de listagem
    if (!$usuarioEncontrado) {
        header('Location: listagem-de-usuario.php');
        exit;
    }

    // Se o formulário foi enviado, processar as atualizações
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Verificar se houve alterações no formulário
        if (
            isset($_POST['nome_completo']) &&
            isset($_POST['email_institucional']) &&
            isset($_POST['telefone'])
        ) {
            // Atualizar os dados do usuário com os novos valores do formulário
            $usuarioEncontrado['nome_completo'] = $_POST['nome_completo'];
            $usuarioEncontrado['email_institucional'] = $_POST['email_institucional'];
            $usuarioEncontrado['telefone'] = $_POST['telefone'];

            // Salvar as atualizações no arquivo JSON
            salvarUsuarios($usuarios);

            // Redirecionar de volta para a página de listagem
            header('Location: listagem-de-usuario.php');
            exit;
        } else {
            // Exibir uma mensagem de erro se nenhum dado foi alterado no formulário
            $erro = "Nenhuma alteração foi feita.";
        }
    }
} else {
    // Se o parâmetro do usuário não estiver presente na URL, redirecionar de volta para a página de listagem
    header('Location: listagem-de-usuario.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuário</title>
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
                <h2>Editar Usuário</h2>
                <?php if (isset($erro)) : ?>
                    <p style="color: red;"><?= $erro ?></p>
                <?php endif; ?>
                <form method="POST" action="">
                    <input type="hidden" name="usuario" value="<?= $usuarioEditar ?>">
                    <label for="nome_completo">Nome Completo:</label>
                    <input type="text" id="nome_completo" name="nome_completo" value="<?= $usuarioEncontrado['nome_completo'] ?>">
                    <label for="email_institucional">Email Institucional:</label>
                    <input type="email" id="email_institucional" name="email_institucional" value="<?= $usuarioEncontrado['email_institucional'] ?>">
                    <label for="telefone">Telefone:</label>
                    <input type="text" id="telefone" name="telefone" value="<?= $usuarioEncontrado['telefone'] ?>">
                    <button type="submit">Salvar</button>
                </form>
            </div>
        </div>
    </main>
</body>
</html>