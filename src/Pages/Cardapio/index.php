<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mordidela - Cardápio</title>
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="./src/assets/css/style.css">
    <link rel="stylesheet" href="./src/assets/awesome/css/all.min.css">
</head>
<body>
    <?php
        include('../menu.php');
    ?>
    <main class="pg-3">
        <section class="banner">
            <iframe 
                height="300px"
                src="https://www.youtube.com/embed/HtfL9_8gTU0?autoplay=1&loop=1&muted=1&playlist=HtfL9_8gTU0&si=MrgM1MFmoBIXR_7j" 
                title="Mordidela" 
                frameborder="0" 
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 
                referrerpolicy="strict-origin-when-cross-origin" 
                allowfullscreen>
            </iframe>
        </section>

        <?php

            $categoria_json_file = '../../../painel/src/data/cardapio-categoria.json';
            $menu_json_file = '../../../painel/src/data/cardapio.json';
            $docs_directory = './painel/src/docs/';

            function readJsonFile($file)
            {
                $data = file_get_contents($file);
                return json_decode($data, true);
            }

            $categorias = readJsonFile($categoria_json_file);

            $menu_items = [];

            $menu_data = readJsonFile($menu_json_file);
            foreach ($menu_data as $item) {
                if ($item['ativo']) { 
                    $categoria = $item['categoria'];
                    if (!isset($menu_items[$categoria])) {
                        $menu_items[$categoria] = [];
                    }
                    $menu_items[$categoria][] = [
                        'nome' => $item['nome'],
                        'descricao' => $item['descricao']
                    ];
                }
            }

            echo '<section class="cardapio-pg">
                <div class="container">
                    <div class="box">
                        <div class="box-cardapio">
                            <div class="button-container">
                                <div class="popover" id="popover">
                                    <img id="popover-image" src="' . $docs_directory . $categorias[0]['foto'] . '" alt="Categoria Imagem">
                                </div>
                                <div class="box-btn">';

                foreach ($categorias as $index => $categoria) {
                    $class_name = strtolower(str_replace(' ', '-', $categoria['nome']));
                    if ($index === 0) {
                        echo '<button class="active" onclick="showCategory(event, \'' . $class_name . '\', \'' . $docs_directory . $categoria['foto'] . '\')">' . $categoria['nome'] . '</button>';
                    } else {
                        echo '<button onclick="showCategory(event, \'' . $class_name . '\', \'' . $docs_directory . $categoria['foto'] . '\')">' . $categoria['nome'] . '</button>';
                    }
                }

                echo '</div>
                        </div>

                        <table>
                        '  /* 
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th>Descrição</th>
                                </tr>
                            </thead>
                        */ . '
                            <tbody id="menu-table">';

                // Itens de menu
                foreach ($menu_items as $categoria => $itens) {
                    $class_name = strtolower(str_replace(' ', '-', $categoria));
                    foreach ($itens as $item) {
                        $hidden_class = ($class_name !== strtolower(str_replace(' ', '-', $categorias[0]['nome']))) ? ' hidden' : '';

                        $descricao = $item['descricao'];

                        if($descricao == '-'){
                            $descricao = '';
                        }
                        echo '<tr class="' . $class_name . $hidden_class . '">
                                <td><h4>' . $item['nome'] . '</h4></td>
                                <td><p>' . $descricao . '</p></td>
                            </tr>';
                    }
                }   

                echo '</tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </section>';
        ?>
    </main>
    <?php
        include('../footer.php');
    ?>

    <script src="./src/assets/js/cardapio.js"></script>
</body>
</html>