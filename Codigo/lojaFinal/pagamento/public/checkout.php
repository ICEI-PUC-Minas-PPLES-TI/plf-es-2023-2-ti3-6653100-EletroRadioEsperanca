<?php

require '../vendor/autoload.php';

use Stripe\StripeClient;

session_start();
header('Content-Type: application/json');

// Assumindo que você já tem o total da compra na sessão ou calculado de outra forma
$total = $_SESSION['cartTotal']; // Certifique-se de que este valor está correto

$stripeSecretKey = 'sk_test_51O8rFwENHpKhutKZMJ9MGzRtkZApsBUaesgmu4vaMPnav2S7ZiKqJFueOBcxjAMsFc9jGQu19UGZHdVkV4cJXOvC00JqeDbWTf';
$domain = 'http://localhost';
$stripe = new StripeClient($stripeSecretKey);

$items = [
    'mode' => 'payment',
    'success_url' => $domain . '/lojaFinalTeste/pagamento/public/sistema.php?session_id={CHECKOUT_SESSION_ID}',
    'cancel_url' => $domain . '/lojaFinalTeste/pagamento/public/cancel.php',
    'line_items' => [[
        'price_data' => [
            'currency' => 'brl',
            'product_data' => [
                'name' => 'Compra Total'
            ],
            'unit_amount' => $total * 100, // O total deve estar em centavos
        ],
        'quantity' => 1,
    ]]
];

$checkout_session = $stripe->checkout->sessions->create($items);
$_SESSION['checkout_session_id'] = $checkout_session->id;

header("HTTP/1.1 303 See Other");
header("Location: " . $checkout_session->url);

?>