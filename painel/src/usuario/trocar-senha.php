<?php
session_start();

// Função para carregar usuários do arquivo JSON
function carregarUsuarios() {
    $usuariosJson = file_get_contents(__DIR__ . '/../data/usuarios.json');
    return json_decode($usuariosJson, true);
}

$usuarios = carregarUsuarios();

$nomeCompleto = $_POST['nome_completo'] ?? '';
$usuario = $_POST['usuario'] ?? '';
$telefone = $_POST['telefone'] ?? '';

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verificar se os campos foram preenchidos
    if (empty($nomeCompleto) || empty($usuario) || empty($telefone)) {
        $erro = 'Por favor, preencha todos os campos.';
    } else {
        // Verificar se as informações correspondem a um usuário válido
        $usuarioEncontrado = false;
        foreach ($usuarios as $user) {
            if ($user['nome_completo'] === $nomeCompleto && 
                $user['usuario'] === $usuario && 
                $user['telefone'] === $telefone) {
                $usuarioEncontrado = true;
                // Definir o nome de usuário na sessão para referência posterior
                $_SESSION['username'] = $usuario;
                // Redirecionar para a página de troca de senha
                header('Location: trocar-senha-confirmacao.php');
                exit;
            }
        }

        if (!$usuarioEncontrado) {
            $erro = '<p class="mensagem-de-erro">Informações inválidas. Verifique os dados e tente novamente.</p>';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trocar Senha</title>
    <?php $baseDirCss = './../../';
    include '../../css.php'; ?>  
</head>
<body>
    <?php if ($erro): ?>
        <p class="error"><?= $erro ?></p>
    <?php endif; ?>
    <div class="container-login">
        <div class="box-login">
            <h2>Trocar Senha</h2>
            <form method="POST" action="">
                <label for="nome_completo">Nome Completo:</label>
                <input type="text" id="nome_completo" name="nome_completo" value="<?= htmlspecialchars($nomeCompleto) ?>" required>
                <label for="usuario">Usuário:</label>
                <input type="text" id="usuario" name="usuario" value="<?= htmlspecialchars($usuario) ?>" required>
                <label for="telefone">Telefone:</label>
                <input type="text" id="telefone" name="telefone" value="<?= htmlspecialchars($telefone) ?>" required>
                <button type="submit">Verificar Informações</button>
            </form>
        </div>
    </div>
</body>
</html>