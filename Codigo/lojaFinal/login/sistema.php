<?php
session_start();

require '../pagamento/vendor/autoload.php';

// Suas configurações de DB aqui
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "loja07";
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar a conexão
if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}

$stripeSecretKey = 'sk_test_51O8rFwENHpKhutKZMJ9MGzRtkZApsBUaesgmu4vaMPnav2S7ZiKqJFueOBcxjAMsFc9jGQu19UGZHdVkV4cJXOvC00JqeDbWTf';
\Stripe\Stripe::setApiKey($stripeSecretKey);
$stripe = new \Stripe\StripeClient();

// Verifica se o checkout foi bem-sucedido pelo ID da sessão
if (isset($_GET['session_id'])) {
    $session = $stripe->checkout->sessions->retrieve($_GET['session_id']);
    if ($session && $session->payment_status == "paid") {

        // Supondo que você tenha um ID de cliente na sessão
        $idDoCliente = $_SESSION['id_cliente'];

        // Placeholder para o produto - você deve substituir isso pelo seu sistema de carrinho
        $productsInCart = [
            [
                "foto" => "foto-exemplo.jpg",
                "titulo" => "Produto Exemplo",
                "valor" => "20.00"
            ]
        ];

        $product = $productsInCart[0];
        $valor = $session->amount_total / 100; // Convertendo centavos para reais

        $sql = "INSERT INTO compras (foto, titulo, valor, total, id_cliente, status, ip, numero_pedido, data) 
                VALUES (
                    '{$product["foto"]}', 
                    '{$product["titulo"]}', 
                    '{$product["valor"]}', 
                    '$valor', 
                    '$idDoCliente', 
                    'paid', 
                    '{$_SERVER["REMOTE_ADDR"]}', 
                    '{$session->id}', 
                    NOW()
                )";

        if ($conn->query($sql) === TRUE) {
            echo "Registro inserido com sucesso.";
        } else {
            echo "Erro: " . $sql . "<br>" . $conn->error;
        }
    }
}
// print_r($_SESSION);
if ((!isset($_SESSION['email']) == true) and (!isset($_SESSION['senha']) == true)) {
    unset($_SESSION['email']);
    unset($_SESSION['senha']);
    header('Location: login.php');
}
$logado = $_SESSION['email'];
if (!empty($_GET['search'])) {
    $data = $_GET['search'];
    $sql = "SELECT * FROM usuarios WHERE id LIKE '%$data%' or nome LIKE '%$data%' or email LIKE '%$data%' ORDER BY id DESC";
} else {
    $sql = "SELECT * FROM usuarios ORDER BY id DESC";
}
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <title>SISTEMA CLIENTE</title>
    <style>
        body {
            background: white;
            /* Alterando o plano de fundo para um gradiente de verde mais claro */
            color: #333;
            /* Alterando a cor do texto para um tom mais escuro para melhor legibilidade */
            text-align: center;
        }

        /* Adicione as outras regras de estilo aqui com base nas sugestões anteriores */


        .table-bg {
            background: rgba(0, 0, 0, 0.3);
            border-radius: 15px 15px 0 0;
        }

        .box-search {
            display: flex;
            justify-content: center;
            gap: .1%;
        }

        .navbar {
            background-color: #004d00;
            /* Alterando a cor de fundo da barra de navegação */
        }

        .navbarbrand1 {}

        .navbar a {
            color: white;
            /* Alterando a cor do texto dos links da barra de navegação */
        }

        .btn-danger {
            background-color: #cc0000;
            /* Alterando a cor de fundo do botão "Sair" para vermelho */
            border-color: #cc0000;

        }

        .btn-danger:hover {
            background-color: #b30000;
            /* Alterando a cor de fundo do botão "Sair" quando o cursor passa por cima */
            border-color: #b30000;
        }

        .btn-primary {
            background-color: #004d00;
            /* Alterando a cor de fundo do botão de pesquisa para verde escuro */
            border-color: #004d00;
        }

        .btn-primary svg {
            fill: white;
            /* Alterando a cor do ícone de pesquisa para branco */
        }

        .card {
            background-color: #DCDCDC;
            /* Alterando a cor de fundo dos cartões */
        }

        .card-header {
            background-color: #00822b;
            /* Alterando a cor de fundo do cabeçalho do cartão para verde */
            color: white;
            /* Alterando a cor do texto do cabeçalho do cartão para branco */
        }

        thead {
            background-color: #00822b;
            /* Alterando a cor de fundo do cabeçalho da tabela para verde */
            color: white;
            /* Alterando a cor do texto do cabeçalho da tabela para branco */
        }

        table {
            color: black;
            background-color: white;
            /* Alterando a cor do texto da tabela para preto */
        }
    </style>
</head>

<body>
    <nav class="navbar">
        <div class="container-fluid">
            <a class="navbar-brand">ÁREA DO CLIENTE</a>
            <a class="navbar-brand1" href="../indexLogado.php">Pagina Inicial</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="ms-auto">
                <a href="sair.php" class="btn btn-danger">Sair</a>
            </div>
        </div>
    </nav>
    <br>
    <?php
    echo "<h1>Bem vindo! </h1>";
    ?>
    <br>
    <div class="box-search">
        <input type="search" class="form-control w-25" placeholder="Pesquisar compras" id="pesquisar">
        <button onclick="searchData()" class="btn btn-primary">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search"
                viewBox="0 0 16 16">
                <path
                    d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z" />
            </svg>
        </button>
    </div>
    <div class="row mt-5"> <!-- Adicionando um espaçamento de 4 unidades (por exemplo) acima da tabela -->
        <div class="col-12">
            <div class="card">
                <div class="card-header" style="background-color: #D3D3D3; color: black;">
                    <!-- Alterando a cor de fundo e a cor do texto do cabeçalho do cartão -->
                    <h4>Histórico de Compras</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="save-stage" style="width:100%;">
                            <thead style="background-color: whitesmoke; color: black;">
                                <tr>

                                    <th>Produtos</th>
                                    <th>Valor</th>
                                    <th>Status</th>
                                    <th>Data do Pagamento</th>
                                    <th>Sua Avaliação</th>
                                    <th>Código de Rastreio</th>

                                    <!-- Adicione ou remova colunas conforme necessário -->
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Estabelecer a conexão com o banco de dados
                                $servername = "localhost";
                                $username = "root";
                                $password = "";
                                $dbname = "loja07";
                                $conn = new mysqli($servername, $username, $password, $dbname);
                                // Verificar a conexão
                                if ($conn->connect_error) {
                                    die("Erro de conexão: " . $conn->connect_error);
                                }
                                // Consulta SQL para ler dados da tabela pagamentos
                                $idDoUsuarioLogado = $_SESSION['id_cliente'] ?? 0;
                                
                                
                                // Consulta SQL para ler dados da tabela pagamentos filtrados pelo ID do usuário
                                $sql = "SELECT p.*, a.nota, a.comentario 
                                FROM pagamentos p
                                LEFT JOIN avaliacoes_compra a ON p.id = a.idPagamento
                                WHERE p.usuario_id = $idDoUsuarioLogado 
                                ORDER BY p.data_pagamento DESC";

                                $result = $conn->query($sql);

                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<tr>";
                                        // Colunas de pagamentos
                                        $idProdutos = explode(',', $row['idProdutos']); // Supondo que os IDs estão separados por vírgula
                                        $nomesProdutos = [];
                                        foreach ($idProdutos as $idProduto) {
                                            $queryProduto = "SELECT nome FROM produtos WHERE id = $idProduto";
                                            $resultadoProduto = $conn->query($queryProduto);
                                            if ($resultadoProduto && $resultadoProduto->num_rows > 0) {
                                                while ($produto = $resultadoProduto->fetch_assoc()) {
                                                    $nomesProdutos[] = $produto['nome'];
                                                }
                                            }
                                        }
                                        echo "<td>" . implode(', ', $nomesProdutos) . "</td>"; // Mostrar os nomes dos produtos
                                        echo "<td>R$ " . number_format($row["valor"], 2, ',', '.') . "</td>";
                                        echo "<td>" . $row["status"] . "</td>";
                                        echo "<td>" . date('d/m/Y', strtotime($row["data_pagamento"])) . "</td>";
                                        $nota = $row['nota'] ?? 'Não avaliado';
                                        $comentario = $row['comentario'] ?? 'N/A';
                                        echo "<td>Avaliação: $nota <br> Comentário: $comentario</td>";
                                        $codigoRastreio = $row['codigo_rastreio'] ?? 'Não disponível';
                                        echo "<td>" . htmlspecialchars($codigoRastreio) . "</td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='6'>Nenhum pagamento encontrado</td></tr>";
                                }
                                $conn->close();
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
<script>
    var search = document.getElementById('pesquisar');
    search.addEventListener("keydown", function (event) {
        if (event.key === "Enter") {
            searchData();
        }
    });
    function searchData() {
        window.location = 'sistema.php?search=' + search.value;
    }
</script>
</html>