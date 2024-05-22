<!DOCTYPE html>
<html lang="it">
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?php echo $templateParams["titolo"]; ?></title>
        <meta charset="UTF-8">
        <script src="https://unpkg.com/vue@3"></script>
        <script src="./utils/segmentation.js" defer></script>
        <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
        <script async src="./node_modules/opencv.js/opencv.js" type="text/javascript"></script>
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Major+Mono+Display">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    </head>

    <body class="bg-light">
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark p-3">
            <div class="container-fluid">
                <a class="navbar-brand" href="index.php">Tirocinio</a>
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li v-for="(page) in pages" class="nav-item">
                        <a
                            class="nav-link"
                            aria-current="page"
                            :href="page.link.url"
                        >   
                        {{page.link.text}}</a>
                    </li>
                </ul>
            </div>

            <div class="ms-auto">
                <?php
                    if(isset($_SESSION["ID"])) {
                ?>
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a
                            class="nav-link text-white"
                            aria-current="page"
                            href="logout.php"
                        > Logout</a>
                    </li>
                </ul>
                <?php
                    } else {
                ?>
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a
                            class="nav-link text-white"
                            aria-current="page"
                            href="login.php"
                        > Login</a>
                    </li>
                </ul>
                <?php
                    }

                ?>
            </div>
        </nav>

        <script>
            Vue.createApp({
                data(){
                    return{
                        pages:[
                            {
                                link:{text:'Data set', url: 'dataset.php'}
                            },
                            {
                                link:{text:'Genera Immagine', url: 'image.php'},
                            },
                            {
                                link:{text:'Immagini', url: 'images_list.php'},
                            },
                            {
                                link:{text:'Immagini finali', url: 'final_images_list.php'},
                            },
                            
                        ]
                    }
                }
            }).mount('body');
        </script>
        
        <main>
            <?php 
                if(isset($templateParams["nome"])){
                    require($templateParams["nome"]);
                }
            ?>
        </main>
    </body>
</html>