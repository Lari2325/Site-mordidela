<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuariosJson = file_get_contents('src/data/usuarios.json');
    $usuarios = json_decode($usuariosJson, true);

    $username = $_POST['username'];
    $password = $_POST['password'];

    $usuarioEncontrado = false;
    foreach ($usuarios as $usuario) {
        if ($usuario['usuario'] === $username && $usuario['ativo'] === true) {
            if (password_verify($password, $usuario['senha'])) {
                $usuarioEncontrado = true;
                $_SESSION['username'] = $username;
                $_SESSION['tipo'] = $usuario['tipo'];
                header('Location: dashboard.php');
                exit;
            } else {
                echo '<script>document.getElementById("error-message").innerHTML = "Senha incorreta. Tente novamente.";</script>';
                echo '<p style="color: #fff; font-weight: 600; width: 100%; background: #bd0d0d;  text-align: center; padding: 20px; text-transform: uppercase;">Senha incorreta!</p>';
                break;
            }
        }
    }

    if (!$usuarioEncontrado) {
        echo '<script>document.getElementById("error-message").innerHTML = "Credenciais inválidas ou usuário inativo. Tente novamente.";</script>';
        echo '<p style="color: #fff; font-weight: 600; width: 100%; background: #bd0d0d;  text-align: center; padding: 20px; text-transform: uppercase;">Credenciais inválidas ou usuário inativo. Tente novamente.</p>';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title> 
    <?php 
     $baseDirCss = './';
     include 'css.php'; ?>   
</head>
<body>
    <div class="container-login">
        <div class="box-login">
            <h2>Login</h2>

            <div id="error-message"></div>

            <form id="login-form" method="POST" action="index.php">
                <label for="username">Usuário:</label>
                <input type="text" id="username" name="username" required>
                <label for="password">Senha:</label>
                <input type="password" id="password" name="password" required>
                <button type="submit">Entrar</button>
            </form>
            <p><a href="src/usuario/trocar-senha.php">Esqueci minha senha</a></p>
        </div>
    </div>
    <?php include 'js.php'; ?>   

    <script>
        document.getElementById('login-form').addEventListener('submit', function(event) {
            let usernameInput = document.getElementById('username');
            let passwordInput = document.getElementById('password');
            let errorMessage = document.getElementById('error-message');

            usernameInput.classList.remove('error-form');
            passwordInput.classList.remove('error-form');
            errorMessage.innerHTML = '';

            if (usernameInput.value.trim() === '' || passwordInput.value.trim() === '') {
                errorMessage.innerHTML = 'Por favor, preencha todos os campos.';
                event.preventDefault(); 
            }
        });

        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            echo 'document.getElementById("username").classList.add("error-form");';
            echo 'document.getElementById("password").classList.add("error-form");';
        }
        ?>
    </script>
</body>
</html>