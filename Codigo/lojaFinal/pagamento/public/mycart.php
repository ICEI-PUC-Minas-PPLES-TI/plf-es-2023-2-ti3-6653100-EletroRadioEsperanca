<!doctype html>
<html lang="PT-BR" data-bs-theme="auto">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Finalizar Compra</title>
  <link href="assets/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/css/style.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
  <style>
    .btn-icon {
      background: none;
      border: none;
      color: red;
    }

    .btn-icon:hover {
      color: darkred;
    }
  </style>
  <?php
  use app\library\Cart;
  use app\library\Product;

  require '../vendor/autoload.php';
  require('../../sheep_core/config.php');

  session_start();

  $cart = new Cart;
  $productsInCart = $cart->getCart();

  if (isset($_GET['id'])) {
    $id = strip_tags($_GET['id']);
    $cart->remove($id);
    header('Location: mycart.php');
  }
  ?>

</head>

<body class="bg-body-tertiary">
  <div class="container">
    <main>
      <div class="py-5 text-center">
        <h2>Finalizar Compra</h2>
      </div>
      <div class="row g-5">
        <div class="col-md-5 col-lg-4 order-md-last">
          <h4 class="d-flex justify-content-between align-items-center mb-3">
            <span class="text-primary">Seu carrinho</span>
            
          </h4>
          <ul class="list-group mb-3">
            <?php
            $ip = $_SESSION['ip'] ?? $_SERVER['REMOTE_ADDR'];
            $cart = new Ler();
            $cart->Leitura('carrinho', "WHERE ip = :ip", "ip={$ip}");

            if ($cart->getContaLinhas() > 0) {
              foreach ($cart->getResultado() as $carts) {
                $ler = new Ler();
                $ler->Leitura('produtos', "WHERE id = :id ORDER BY data DESC", "id={$carts['id_produto']}");

                if ($ler->getResultado()) {
                  foreach ($ler->getResultado() as $produto) {
                    $produto = (object) $produto;
                    ?>
                    <li class="list-group-item d-flex justify-content-between lh-sm">
                      <div>
                        <h6 class="my-0">
                          <?= $produto->nome ?>
                        </h6>
                        <small class="text-muted">Subtotal: R$
                          <?= number_format($produto->valor, 2, ',', '.') ?>
                        </small>
                      </div>
                      <span class="text-muted">R$
                        <?= number_format($produto->valor, 2, ',', '.') ?>
                      </span>
                      <form action="../../filtros/excluir.php" method="post" style="display: inline;">
                        <input type="hidden" name="id_produto" value="<?= $produto->id ?>">
                        <button type="submit" class="btn btn-danger btn-icon">
                          <i class="fa fa-trash"></i>
                        </button>

                      </form>
                    </li>
                    <?php
                  }
                }
              }
            } else {
              ?>
              <li class="list-group-item">Seu carrinho está vazio!</li>
              <?php
            }
            $totalCarrinho = new Ler();
            $totalCarrinho->LeituraCompleta("SELECT SUM(valor) as total FROM carrinho");
            if ($totalCarrinho->getResultado()) {
              $totalCompras = number_format($totalCarrinho->getResultado()[0]['total'], 2, ',', '.');
              $_SESSION['cartTotal'] = $totalCarrinho->getResultado()[0]['total']; // Adicionando o total à variável de sessão
            } else {
              $totalCompras = 0;
              $_SESSION['cartTotal'] = 0; // Definindo o total como 0 se o carrinho estiver vazio
            }
            ?>

            <!-- Continuando o código HTML para exibir o valor total -->
            <li class="list-group-item d-flex justify-content-between">
              <span>Total</span>
              <strong>R$
                <?= $totalCompras; ?>
              </strong>
            </li>
          </ul>

        </div>
        <div class="col-md-7 col-lg-8">
          <h4 class="mb-3">Informações de Endereço</h4>
          <form class="needs-validation" novalidate>
            <div class="row g-3">
              <div class="col-sm-6">
                <label for="firstName" class="form-label">Primeiro Nome</label>
                <input type="text" class="form-control" id="firstName" placeholder="" value="" required>
                <div class="invalid-feedback">
                  Nome válido é obrigatório.
                </div>
              </div>

              <div class="col-sm-6">
                <label for="lastName" class="form-label">Segundo Nome</label>
                <input type="text" class="form-control" id="lastName" placeholder="" value="" required>
                <div class="invalid-feedback">
                  Nome válido é obrigatório.
                </div>
              </div>

              <div class="col-12">
                <label for="email" class="form-label">Email <span class="text-body-secondary">(Opcional)</span></label>
                <input type="email" class="form-control" id="email" placeholder="you@example.com">
                <div class="invalid-feedback">
                  Insira um endereço de e-mail válido para atualizações de envio.
                </div>
              </div>

              <hr class="my-4">
            </div>
            <p>A finzalizção da compra é feita em outra página. <a href="../../indexLogado.php">Voltar para home</a></p>

            <a class="w-100 btn btn-primary btn-lg" href="checkout.php">Ir para o checkout</a>
          </form>
        </div>
        <!-- Você pode adicionar o código do formulário de informações de endereço aqui (como está no segundo código) -->
      </div>
    </main>
  </div>
  <script src="assets/js/bootstrap.bundle.min.js"></script>
  <script src="checkout.js"></script>
</body>

</html>