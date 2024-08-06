<?php
session_start();

// Verificar se o usuário está autorizado a acessar esta página
if (!isset($_SESSION['username'])) {
    header('Location: trocar-senha.php');
    exit;
}

// Função para carregar usuários do arquivo JSON
function carregarUsuarios() {
    $usuariosJson = file_get_contents(__DIR__ . '/../data/usuarios.json');
    return json_decode($usuariosJson, true);
}

function salvarUsuarios($usuarios) {
    $usuariosJson = json_encode($usuarios, JSON_PRETTY_PRINT);
    file_put_contents(__DIR__ . '/../data/usuarios.json', $usuariosJson);
}

$usuarios = carregarUsuarios();

$erroSenha = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $novaSenha = $_POST['nova_senha'] ?? '';
    $confirmarSenha = $_POST['confirmar_senha'] ?? '';

    // Verificar se ambas as senhas foram fornecidas e são iguais
    if (!empty($novaSenha) && !empty($confirmarSenha) && $novaSenha === $confirmarSenha) {
        // Criptografar a nova senha antes de salvar
        $senhaHash = password_hash($novaSenha, PASSWORD_DEFAULT);

        // Atualizar a senha do usuário correspondente
        foreach ($usuarios as &$usuario) {
            if ($usuario['usuario'] === $_SESSION['username']) {
                // Atualizar a senha com o hash da nova senha
                $usuario['senha'] = $senhaHash;
                // Salvar as alterações no arquivo JSON
                salvarUsuarios($usuarios);
                // Redirecionar para o dashboard ou outra página após a troca de senha
                header('Location: ../../index.php');
                exit;
            }
        }
    } else {
        // Senhas não correspondem, exibir mensagem de erro
        $erroSenha = "<p class=\"mensagem-de-erro\">As senhas inseridas não correspondem. Tente novamente.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trocar Senha - Confirmação</title>
    <?php $baseDirCss = './../../';
    include '../../css.php'; ?> 
</head>
<body>
    <?php if ($erroSenha): ?>
        <p class="error"><?= $erroSenha ?></p>
    <?php endif; ?>
    <div class="container-login">
        <div class="box-login">
            <h2>Trocar Senha - Confirmação</h2>
            <form method="POST" action="">
                <label for="nova_senha">Nova Senha:</label>
                <input type="password" id="nova_senha" name="nova_senha" required>
                <label for="confirmar_senha">Confirmar Nova Senha:</label>
                <input type="password" id="confirmar_senha" name="confirmar_senha" required>
                <button type="submit">Trocar Senha</button>
            </form>
        </div>
    </div>
</body>
</html>