<?php
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'loja07';

// Criando a conexão
$conn = new mysqli($host, $username, $password, $database);

// Checando a conexão
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

require '../vendor/autoload.php';

use app\library\Cart;
use Stripe\StripeClient;

session_start();

$stripeSecretKey = 'sk_test_51O8rFwENHpKhutKZMJ9MGzRtkZApsBUaesgmu4vaMPnav2S7ZiKqJFueOBcxjAMsFc9jGQu19UGZHdVkV4cJXOvC00JqeDbWTf';
$stripe = new StripeClient($stripeSecretKey);

$ip_address = $_SERVER['REMOTE_ADDR']; // Pegando o IP do usuário

$sql = "SELECT id_produto, nome, valor, ip, data FROM carrinho WHERE ip = '$ip_address'";
$result = $conn->query($sql);

$productsInCart = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $productsInCart[] = [
            'id' => $row['id_produto'],
            // Adicionando o ID do produto
            'name' => $row['nome'],
            'price' => $row['valor'],
            'ip' => $row['ip'],
            'date' => $row['data']
        ];
    }
}



$paymentDetails = null;

if (isset($_SESSION['checkout_session_id'])) {
    $checkoutSessionId = $_SESSION['checkout_session_id'];

    try {
        $session = $stripe->checkout->sessions->retrieve($checkoutSessionId);
        // Outras verificações...

        if ($session && $session->payment_status == 'paid') {
            $createdTimestamp = $session->created;
            $createdDate = date("d-m-Y H:i:s", $createdTimestamp);
            $paymentIntentId = $session->payment_intent;
            $paymentIntent = $stripe->paymentIntents->retrieve($paymentIntentId);

            // Insira o código de salvamento aqui
            $stmt = $conn->prepare("INSERT INTO pagamentos (valor, moeda, status, data_pagamento) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("dsss", $valor, $moeda, $status, $data);

            $valor = $paymentIntent->amount / 100;
            $moeda = strtoupper($paymentIntent->currency);
            $status = $paymentIntent->status;
            $data = date("Y-m-d H:i:s", $createdTimestamp); // Convertendo timestamp para formato DATETIME
            $productIds = array_map(function ($product) {
                return $product['id'];
            }, $productsInCart);

            // Para armazenar como string separada por vírgulas
            $idProdutos = implode(',', $productIds);

            // Ou para armazenar como JSON
            // $idProdutos = json_encode($productIds);

            $stmt = $conn->prepare("INSERT INTO pagamentos (valor, moeda, status, data_pagamento, idProdutos) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("dssss", $valor, $moeda, $status, $data, $idProdutos);
            $stmt->execute();

            $idPagamento = $conn->insert_id; // Captura o ID do pagamento inserido
            $_SESSION['idPagamento'] = $idPagamento; // Armazena na sessão


            // Agora defina $paymentDetails para exibir na página
            $paymentDetails = [
                'amount' => number_format($valor, 2),
                'currency' => $moeda,
                'status' => $status,
                'date' => $createdDate
            ];

            unset($_SESSION['checkout_session_id']);
        }
    } catch (\Stripe\Exception\ApiErrorException $e) {
        // Tratamento de erro
        $error = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalhes do Pagamento</title>
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <div class="text-center mb-4">
            <img src="/lojaFinalTeste/assets/img/Eletro Radio - logo menor.jpg" alt="Logo da Loja"
                style="max-height: 100px; margin-bottom: 40px;">
            </a>
        </div>
        <h2>Detalhes do Pagamento</h2>

        <?php if ($paymentDetails): ?>
            <table class="table">
                <tbody>
                    <tr>
                        <td>Valor:</td>
                        <td>R$
                            <?= $paymentDetails['amount']; ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Moeda:</td>
                        <td>
                            <?= $paymentDetails['currency']; ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Status:</td>
                        <td>
                            <?= $paymentDetails['status']; ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Data do Pedido:</td>
                        <td>
                            <?= $paymentDetails['date']; ?>
                        </td>
                    </tr>
                </tbody>
            </table>

            <h3>Produto(s)</h3>
            <table class="table">
                <thead>
                    <tr>
                        <th>ID do Produto</th>
                        <th>Nome do Produto</th>
                        <th>Quantidade</th>
                        <th>Preço</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Parte do HTML para exibir os produtos -->
                    <?php foreach ($productsInCart as $product): ?>
                        <tr>
                            <td>
                                <?= htmlspecialchars($product['id'], ENT_QUOTES, 'UTF-8'); ?>
                            </td>
                            <td>
                                <?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'); ?>
                                <!-- Supomos que ID seja o nome -->
                            </td>
                            <td>
                                1 <!-- Supomos que a quantidade seja sempre 1, pois não temos essa informação na tabela -->
                            </td>
                            <td>R$
                                <?= number_format($product['price'], 2); ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                </tbody>
            </table>

        <?php else: ?>
            <p>Parece que houve um problema ao recuperar os detalhes do pagamento.</p>
            <?php if (isset($error)): ?>
                <p class="text-danger">Erro:
                    <?= $error; ?>
                </p>
            <?php endif; ?>
        <?php endif; ?>
        <a href="/lojaFinalTeste/indexLogado.php" class="btn btn-primary" style="background-color: #28a745;
            border-color: #28a745; ">Voltar para a Loja</a>

        <h3 style="margin-top: 30px;">Nos ajude a melhorar. Avalie sua compra abaixo &#65516;</h3>
        <form action="avaliacao_pedido.php" method="post">
            <input type="hidden" name="idPagamento" value="<?= $idPagamento; ?>">
            <div class="mb-3">
                <label for="rating" class="form-label">Avaliação</label>
                <select class="form-select" id="rating" name="rating">
                    <option value="5">Excelente</option>
                    <option value="4">Muito Bom</option>
                    <option value="3">Bom</option>
                    <option value="2">Regular</option>
                    <option value="1">Ruim</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="comments" class="form-label">Comentários</label>
                <textarea class="form-control" id="comments" name="comments" rows="3"></textarea>
            </div>
            <button type="submit" class="btn btn-primary" style="background-color: #28a745;
            border-color: #28a745; margin-bottom: 40px;">Enviar Avaliação</button>
        </form>
    </div>
</body>

</html>