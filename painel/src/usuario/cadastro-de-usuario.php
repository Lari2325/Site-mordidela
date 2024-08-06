<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Usuário</title>
    <?php 
        $baseDirCss = '../../';
        include '../../css.php'; 
    ?>   
</head>
<body>
    <main>
        <?php 
            session_start();

            if (!isset($_SESSION['username'])) {
                header('Location: ../../index.php');
                exit;
            }

            $baseDir = './../../';
            include '../../menu.php';
            renderMenu($baseDir);

            function carregarUsuarios() {
                $usuariosJson = file_get_contents(__DIR__ . '/../data/usuarios.json');
                return json_decode($usuariosJson, true);
            }

            function salvarUsuarios($usuarios) {
                $usuariosJson = json_encode($usuarios, JSON_PRETTY_PRINT);
                file_put_contents(__DIR__ . '/../data/usuarios.json', $usuariosJson);
            }

            function formatarDataBrasil($data) {
                $timestamp = strtotime($data);
                return date('d-m-Y', $timestamp);
            }

            $errors = [];
            
            ?>

        <div class="container-conteudo">
            <div class="container">
                <h2>Cadastro de Usuário</h2>
                <?php
                    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                        $nomeCompleto = $_POST['nome_completo'];
                        $usuario = strtolower(str_replace(' ', '', $_POST['usuario']));
                        $senha = $_POST['senha'];
                        $telefone = $_POST['telefone'];
                        $emailInstitucional = $_POST['email_institucional'];
                        $dataCadastro = formatarDataBrasil(date('Y-m-d')); 
                        $ativo = true; 
        
                        $usuarios = carregarUsuarios();
        
                        foreach ($usuarios as $user) {
                            if ($user['usuario'] === $usuario) {
                                $errors[] = 'Nome de usuário já existe. Por favor, escolha outro nome de usuário.';
                                break;
                            }
                        }
        
                        if (strlen($senha) < 8 || !preg_match('/[A-Z]/', $senha) || !preg_match('/[a-z]/', $senha) || !preg_match('/\d/', $senha)) {
                            $errors[] = 'A senha deve ter pelo menos 8 caracteres e incluir pelo menos uma letra maiúscula, uma letra minúscula e um número.';
                        }
        
                        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
        
                        $tipoUsuario = $_POST['tipo'];
        
                        if ($_SESSION['tipo'] === 'dev' || $_SESSION['tipo'] === 'admin') {
                            if ($_SESSION['tipo'] === 'admin' && $tipoUsuario === 'dev') {
                                $errors[] = 'Acesso negado. Você não tem permissão para cadastrar usuários como "desenvolvedor".';
                            }
        
                            if (empty($errors)) {
                                $novoUsuario = [
                                    "nome_completo" => $nomeCompleto,
                                    "usuario" => $usuario,
                                    "senha" => $senhaHash,
                                    "telefone" => $telefone,
                                    "email_institucional" => $emailInstitucional,
                                    "data_cadastro" => $dataCadastro,
                                    "ativo" => $ativo,
                                    "tipo" => $tipoUsuario
                                ];
        
                                $usuarios[] = $novoUsuario;
        
                                salvarUsuarios($usuarios);
                            }
                        } else {
                            $errors[] = 'Acesso negado. Você não tem permissão para cadastrar usuários como "desenvolvedor".';
                        }   
                        if (!empty($errors)) {
                            foreach ($errors as $error) {
                            echo '<p class="mensagem-de-erro">' . $error . '</p>';
                            }
                        } else {
                            echo '<p class="mensagem-de-sucesso">Usuário cadastrado com sucesso!</p>';
                        }
                    }

                ?>
                <form method="POST" action="">
                    <label for="nome_completo">Nome Completo:</label>
                    <input type="text" id="nome_completo" name="nome_completo" required>
    
                    <label for="usuario">Nome de Usuário:</label>
                    <input type="text" id="usuario" name="usuario" required>
    
                    <label for="senha">Senha:</label>
                    <input type="password" id="senha" name="senha" required>
    
                    <label for="telefone">Telefone:</label>
                    <input type="text" id="telefone" name="telefone" required>
    
                    <label for="email_institucional">Email Institucional:</label>
                    <input type="email" id="email_institucional" name="email_institucional" required>
    
                    <label for="tipo">Tipo de Usuário:</label>
                    <select id="tipo" name="tipo">
                        <option value="admin">Administrador</option>
                        <option value="normal">Usuário Normal</option>
                        <?php if ($_SESSION['tipo'] === 'dev'): ?>
                            <option value="dev">Desenvolvedor</option>
                        <?php endif; ?>
                    </select>
    
                    <button type="submit">Cadastrar Usuário</button>
                </form>
            </div>
        </div>
    </main>

    <?php 
    $baseDirCss =  '../../';
    include '../../js.php';?>
</body>
</html>