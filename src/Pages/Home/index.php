<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mordidela</title>
    <link rel="stylesheet" href="./src/assets/css/style.css">
    <link rel="stylesheet" href="./src/assets/awesome/css/all.min.css">
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
</head>
<body>
    <?php
        include('../menu.php');
    ?>

    <main class="pg-1">
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
        <section class="sobre">
            <div class="container">
                <h1>para todos os<br> <span>momentos</span></h1>
                <p>Para todos os momentos, há uma refeição perfeita que torna a ocasião ainda mais especial. No almoço em família, os sabores caseiros e a conversa ao redor da mesa fortalecem os laços e criam memórias inesquecíveis. Para um doce encontro, uma sobremesa caprichada ao lado do seu par é a receita certa para um momento romântico e delicioso.<br><br>E com a galera, nada supera a diversão e a descontração de saborear um burger suculento, compartilhando risadas e histórias. Seja qual for o momento, há sempre uma refeição que se encaixa perfeitamente, tornando-o ainda mais marcante. <span>Mordidela Burger & Grill</span>. Aqui, cada mordida é um momento especial.</p>

                <div class="cards">
                    <?php
                        $cardapios = [
                            [
                                'image' => '1',
                                'title' => 'ALMOÇO<br> EM FAMÍLIA',
                                'subtitle' => 'Receita caseira para todos os momentos',
                            ],
                            [
                                'image' => '2',
                                'title' => 'Sobremesa com <br>seu par',
                                'subtitle' => 'Receita caseira para todos os momentos',
                            ],
                            [
                                'image' => '3',
                                'title' => 'Burger com <br>a galera',
                                'subtitle' => 'Receita caseira para todos os momentos',
                            ]
                        ];

                        foreach($cardapios as $card) {
                            echo '<div class="card">';
                            echo '<img src="./src/assets/img/cardapios/'. $card['image']. '.webp" alt="">';
                            echo '<h4>'. $card['title']. '</h4>';
                            echo '<button>Ver mais</button>';
                            echo '</div>';
                        }
                    ?>
                </div>
            </div>
        </section>
        <section class="valores">
            <div class="container">
                <div class="col-1">
                    <div class="box-1">
                        <h4><span>+900</span> mil<br> hambúrgueres<br> grelhados desde 2015</h4>
                    </div>
                    <div class="box-2">
                        <h4><span>+30</span> opções<br> saborosas</h4>
                    </div>
                </div>
                <div class="col-2">
                    <img src="./src/assets/img/composicao-valores.webp" alt="">
                </div>
            </div>
        </section>
        <section class="mapa">
            <div class="container">
                <div class="col-1">
                    <img src="./src/assets/img/mapa.webp" alt="">
                </div>
                <div class="col-2">
                    <h1>ONDE <br>ESTAMOS</h1>
                    <p>Encontre uma unidade <br><span>Mordidela</span> mais próximade você!</p>
                </div>
            </div>
        </section>
        <div class="quebra"></div>
        <section class="depoimento">
            <div class="container">
                <h1>FEEDBACK DOS <br><span>MORDILOVERS</span></h1>

                <div class="container-depoimentos">
                    <div class="depoimentos">
                        <?php
                            $depoimentos = [
                                [
                                    'image' => '4',
                                    'name' => 'Mariana Lima',
                                    'message' => 'Foi uma noite maravilhosa na Mordidela Burger & Grill. O ambiente é muito agradável, perfeito para um jantar com amigos ou família. Os pratos são bem servidos e os preços justos. Sem dúvida, um dos meus novos restaurantes favoritos!',
                                ],
                                [
                                    'image' => '2',
                                    'name' => 'Carlos Mendes',
                                    'message' => 'Simplesmente o melhor hambúrguer da cidade! A Mordidela Burger & Grill superou todas as minhas expectativas. Ingredientes frescos, carne no ponto perfeito e um atendimento de primeira. Mal posso esperar para voltar',
                                ],
                                [
                                    'image' => '3',
                                    'name' => 'João Silveira',
                                    'message' => 'Minha experiência na Mordidela Burger & Grill foi incrível! Os hambúrgueres são suculentos e saborosos, e as batatas fritas crocantes são de outro mundo. O ambiente é acolhedor e o atendimento impecável. Com certeza voltarei!',
                                ],
                                [
                                    'image' => '1',
                                    'name' => 'Gustavo Silva',
                                    'message' => 'Fantástico! A Mordidela Burger & Grill é o lugar perfeito para quem ama hambúrgueres artesanais. A combinação de sabores é incrível e o ambiente é muito acolhedor. Sem dúvida, um dos melhores restaurantes que já visitei.',
                                ],
                                [
                                    'image' => '5',
                                    'name' => 'Lucas Oliveira',
                                    'message' => 'Que lugar incrível! A Mordidela Burger & Grill oferece uma experiência gastronômica maravilhosa. Os hambúrgueres são bem preparados, com ingredientes frescos e de alta qualidade. O atendimento é excepcional. Recomendo!',
                                ]
                            ];
    
                            $comentarios = 0;
                            foreach ($depoimentos as $depoimento) {
                                $comentarios++;
                                echo '<div class="depoimento-container">';
                                    echo '<div class="perfil">';
                                        echo '<img src="./src/assets/img/depoimentos/' . $depoimento['image'] . '.webp" alt="">';
                                    echo '</div>';
                                    echo '<div class="recorte">';
                                        echo '
                                            <div class="bastao-1"></div>
                                            <div class="retangulo"></div>
                                            <div class="bastao-2"></div>
                                        ';
                                    echo '</div>';
                                    echo '<div class="box-depoimento">';
                                        echo '<p>' . $depoimento['message'] . '</p>';
                                        echo '<h4>-' . $depoimento['name'] . '</h4>';
                                    echo '</div>';
                                echo '</div>';
                            }
                        ?>
                    </div>
                </div>
                <ul class="carousel-control">
                    <?php
                        for ($i = 0; $i < $comentarios; $i++) {
                            echo '<li>';
                                echo '<input type="radio" name="carousel-depoimentos" value="' . $i . '"' . ($i == 0 ? ' class="active"' : '') . '/>';
                            echo '</li>';
                        }
                    ?>
                </ul>
            </div>
        </section>
        <section class="carousel-cardapio">
            <div class="container">
                <h1>Paixão a <br><span>primeira mordida</span></h1>
            </div>
            <div class="container-carousel">
                <ul id="carouselItems">
                    <li>Isca de frango</li>
                    <li>Açai</li>
                    <li>Cone de frango</li>
                    <li>Burger</li>
                </ul>
                <div class="container-imagem">
                    <?php
                        for ($i = 0; $i < 4; $i++) {
                            echo '<img src="./src/assets/img/carousel-cardapio/' . ($i + 1) . '.webp" alt="">';
                        }
                    ?>
                    <div class="container-arrows">
                        <button id="prevCarouselCardapio"><i class="fa-solid fa-angle-left"></i></button>
                        <button id="nextCarouselCardapio"><i class="fa-solid fa-angle-right"></i></button>
                    </div>
                </div>
            </div>
        </section>
        <section class="seja-um-franqueado">
            <div class="container">
                <img src="./src/assets/img/mockup.webp" alt="">
                <button onclick="window.open('https://franquia.grupozntt.com.br/mordidela', '_blanck')"><i class="fa-solid fa-arrow-right"></i> Seja um franqueado</button>
            </div>
        </section>
    </main>

    <?php
        include('../footer.php');
    ?>

    <script src="./src/assets/js/carousel-depoimentos.js"></script>
    <script src="./src/assets/js/carousel-cardapio.js"></script>
</body>
</html>