<?php
function renderMenu($baseDir = '') {
    if (!isset($baseDir)) {
        $baseDir = ''; 
    }

    $usuariosFile = __DIR__ . '/src/data/usuarios.json';

    if (file_exists($usuariosFile)) {
        $usuariosJson = file_get_contents($usuariosFile);
        $usuarios = json_decode($usuariosJson, true);

        if (!is_array($usuarios)) {
            $usuarios = []; 
        }
    } else {
        $usuarios = [];
    }

    function createLink($path) {
        global $baseDir;
        return $baseDir . $path;
    }

    $arquivoSistema = __DIR__ . '/src/data/sistema.json';

    $menuItems = [];

    if (file_exists($arquivoSistema)) {
        $sistemaJson = file_get_contents($arquivoSistema);
        $sistema = json_decode($sistemaJson, true);

        if (is_array($sistema)) {
            foreach ($sistema as $item) {
                if (isset($item['label']) && isset($item['nome']) && isset($item['ativo']) && $item['ativo'] === true) {
                    $menuItems[$item['nome']] = $item['label']; 
                }
            }
        }
    }
    ?>
    
    <button class="toggle-btn" id="menu-toggle" onclick="toggleMenu()">☰</button>
    <nav id="sidebar" class="sidebar">
        <a href="<?= createLink('dashboard.php') ?>">Dashboard</a>

        <?php if (isset($_SESSION['username'])): ?>
            <?php
            $isAdminOrDev = false;
            $isDev = false;

            foreach ($usuarios as $usuario) {
                if ($usuario['usuario'] === $_SESSION['username']) {
                    if ($usuario['tipo'] === 'admin' || $usuario['tipo'] === 'dev') {
                        $isAdminOrDev = true;
                    }
                    if ($usuario['tipo'] === 'dev') {
                        $isDev = true;
                    }
                    break;
                }
            }
            ?>
            <?php if ($isAdminOrDev): ?>
                <div class="dropdown">
                    <button class="dropbtn">Usuário</button>
                    <div class="dropdown-content">
                        <a href="<?= createLink('src/usuario/cadastro-de-usuario.php') ?>">Cadastrar Usuário</a>
                        <a href="<?= createLink('src/usuario/listagem-de-usuario.php') ?>">Listar Usuário</a>
                    </div>
                </div>
                <?php if ($isDev): ?>
                    <div class="dropdown">
                        <button class="dropbtn">Menu</button>
                        <div class="dropdown-content">
                            <a href="<?= createLink('src/menu/cadastrar-menu.php') ?>">Cadastrar Menu</a>
                            <a href="<?= createLink('src/menu/listar-menu.php') ?>">Listar Menu</a>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <?php foreach ($menuItems as $nomeMenu => $label): ?>
                <div class="dropdown">
                    <button class="dropbtn"><?= $label ?></button>
                    <div class="dropdown-content">
                        <?php if ($isDev): ?>
                        <a href="<?= createLink('src/menu/cadastrar-campos.php?nome=' . urlencode($nomeMenu)) ?>">Cadastrar Campos</a>
                        <a href="<?= createLink('src/menu/listar-campos.php?nome=' . urlencode($nomeMenu)) ?>">Listar Campos</a>
                        <?php endif; ?>
                        <a href="<?= createLink('src/menu/formulario-de-cadastro.php?campos=' . urlencode($nomeMenu)) ?>">Cadastrar <?= $label ?></a>
                        <a href="<?= createLink('src/menu/listar-itens-cadastrados.php?campos=' . urlencode($nomeMenu)) ?>">Listar <?= $label ?></a>
                    </div>
                </div>
            <?php endforeach; ?>

            <a href="<?= createLink('logout.php') ?>">Sair</a>
        <?php else: ?>
            <a href="<?= createLink('index.php') ?>">Login</a>
        <?php endif; ?>
    </nav>
    <?php
}