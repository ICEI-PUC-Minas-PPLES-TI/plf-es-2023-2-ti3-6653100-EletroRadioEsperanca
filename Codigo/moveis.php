<?php
ob_start();
session_start();
require('./sheep_core/config.php');

$ip = $_SERVER['REMOTE_ADDR'];
$_SESSION['ip'] = $ip;

if (isset($_POST['buscar'])) {
    $categoria = $_POST['categoria'];
    header("Location: resultados_pesquisa.php?categoria=$categoria");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <title>Eletro Rádio Esperança</title>
    <style>
        .linha-produtos1 {
            display: grid;
            width: 90%;
            grid-template-columns: repeat(4, 1fr);
            grid-gap: 40px;
            margin: 0 auto;
            margin-top: 100px;

        }

        @media screen and (max-width: 768px) {
            .linha-produtos1 {
                grid-template-columns: repeat(3, 1fr);
                grid-gap: 20px;
                margin-top: 50px;
            }
        }

        @media screen and (max-width: 480px) {
            .linha-produtos1 {
                grid-template-columns: 1fr;
                grid-gap: 10px;
                margin-top: 30px;
            }
        }

        .corpoProduto {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-direction: column;
            padding: 15px;
            border: 1px solid #00822b;
            border-radius: 5px;

        }

        .imgProduto {
            width: 100%;
            height: 190px;
            display: flex;
            justify-content: center;
            align-items: center;

        }

        .produtoMiniatura {
            max-width: 90%;
            max-height: 90%;
            object-fit: cover;
            object-position: center;
        }

        .titulo {
            display: flex;
            width: 100%;
            height: 110px;
            align-items: center;
            justify-content: space-between;
            text-align: center;
            flex-direction: column;

        }

        h2 {
            font-size: 20px;
            color: red;
        }

        .button {
            background-color: #00822b;
            width: 100%;
            border: none;
            border-radius: 5px;
            position: relative;
            padding: 7px 25px;
            color: #fff;
            font-size: 15px;
            cursor: pointer;

        }

        .button:hover {
            background-color: #00822b;

        }
    </style>
</head>

<body>

    <div class="background2">
        <header>
            <a href="index.php">
                <div class="logo">
                    <img src="assets/img/Eletro Radio - logo menor.jpg" />
                </div><!--logo-->
            </a>

            <div class="cabeçalho-link">
                <li>
                    <a href="televisoes.php">Televisões</a>
                </li>
                <li>
                    <a href="celulares.php">Celulares</a>
                </li>
                <li>
                    <a href="eletrodomesticos.php">Eletrodomésticos</a>
                </li>
                <li>
                    <a href="moveis.php">Móveis</a>
                </li>
                <li>
                    <a href="login/login.php">Login</a>
                </li>
            </div><!--cabeçalho-link-->
            <div class="icon"><span><ion-icon name="bag-outline"></ion-icon></span></div><!--icon-->
            <div class="search">
                <i class="fas fa-search"></i> <!-- Ícone de lupa -->
                <form action="" method="post">
                    <input type="text" name="categoria" placeholder="Pesquisar por categoria...">
                    <button type="submit" name="buscar">Pesquisar</button>
                </form>
            </div>
        </header>


    </div><!--background-->



    <div class="linha-produtos1">

        <?php
        $ler = new Ler();
        $ler->Leitura('produtos', "WHERE categoria = 'movel' ORDER BY data DESC");
        if ($ler->getResultado()) {
            foreach ($ler->getResultado() as $produto) {
                $produto = (object) $produto;
                ?>

                <!-- INICIO PRODUTO -->
                <form action="filtros/criar.php" method="post">
                    <div class="corpoProduto">
                        <div class="imgProduto">
                            <img src="<?= HOME ?>/uploads/<?= $produto->capa ?>" alt="<?= $produto->nome ?>"
                                class="produtoMiniatura" />
                        </div>
                        <div class="titulo">
                            <p>
                                <?= $produto->nome ?>
                            </p>
                            <h2>R$
                                <?= number_format($produto->valor, 2, ',', '.') ?>
                            </h2>
                            <input type="hidden" name="id_produto" value="<?= $produto->id ?>">
                            <input type="hidden" name="valor" value="<?= $produto->valor ?>">
                            <input type="hidden" name="ip" value="<?= $ip ?>">
                            <button type="submit" class="button" name="addcarrinho">Adicionar ao carrinho</button>

                        </div>
                    </div>
                </form>
                <!-- FIM PRODUTO -->
                <?php
            }
        }
        ?>
    </div>






    <footer class="site-footer">
        <div class="container">
            <div class="row">
                <div class="col-sm-12 col-md-6">
                    <h6>Sobre</h6>
                    <p class="text-justify">Na Eletro Rádio Esperança, nossa missão é transformar casas em lares, proporcionando soluções modernas e eficientes em eletrodomésticos para atender às necessidades da sua vida cotidiana. Somos mais do que uma loja de eletrodomésticos; somos o seu parceiro confiável na busca por qualidade, conveniência e inovação.</p>
                </div>

                <div class="col-xs-6 col-md-3">
                    <h6>Categorias</h6>
                    <ul class="footer-links">
                    <li><a href="eletrodomesticos.php">Eletrodomésticos</a></li>
                        <li><a href="moveis.php">Móveis</a></li>
                        <li><a href="celulares.php">Celulares</a></li>
                        <li><a href="televisoes.php">Televisões</a></li>
                    </ul>
                </div>

                <div class="col-xs-6 col-md-3">
                    <h6>Outros Links</h6>
                    <ul class="footer-links">
                        <li><a href="contato.php">Contate-nos</a></li>
                    </ul>
                </div>
            </div>
            <hr>
        </div>
        <div class="container">
            <div class="row">
                <div class="col-md-4 col-sm-6 col-xs-12">
                    <ul class="social-icons">
                        <li><a class="instagram" href="https://www.instagram.com/_eletroradio/"><i
                                    class="fa fa-instagram"></i></a></li>
                        <li><a class="whatsapp" href="#"><i class="fa fa-whatsapp"></i></a></li>
                    </ul>
                </div>
            </div>
            <div class="container">
                <div class="col-md-8 col-sm-6 col-xs-12">
                    <p class="copyright-text">Copyright &copy; 2023 Todos os direitos reservados.
                    </p>
                </div>
            </div>
        </div>
    </footer>
</body>

</html>