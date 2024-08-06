<?php
    session_start(); 

    if (isset($_SESSION['username'])) {
        $nomeUsuario = $_SESSION['username'];
    } else {
        header('Location: index.php');
        exit;
    }
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <?php 
     $baseDirCss = './';
     include 'css.php'; ?> 
    <link rel="stylesheet" href="css/bem-vindo.css">

</head>
<body> 
    <main>
        <?php 
            $baseDir = './';
            include 'menu.php';
            renderMenu($baseDir);
        ?>
        <div class="container-conteudo">
            <div class="container">
                <h2>Dashboard</h2>
                <?php
                echo '<p>Bem-vindo, ' . htmlspecialchars($nomeUsuario) . '!</p>';
                ?>
            </div>
        </div>
    </main>
   <?php include 'js.php'; ?>
</body>
</html>
