<?php
session_start();
include_once('config.php');
if (isset($_POST['alterarStatus'])) {
  $idPedido = $_POST['idPedido'];
  // Conexão com o banco de dados
  $servername = "localhost";
  $username = "root";
  $password = "";
  $dbname = "loja07";
  $conn = new mysqli($servername, $username, $password, $dbname);
  if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
  }
  // Atualizar o status no banco de dados
  $sql = "UPDATE pagamentos SET status = 'Enviado' WHERE id = $idPedido";
  if ($conn->query($sql) === TRUE) {
    echo "<script>alert('Status atualizado com sucesso!'); window.location.href=window.location.href;</script>";
  } else {
    echo "<script>alert('Erro ao atualizar o status: " . $conn->error . "');</script>";
  }
  $conn->close();
}
if (isset($_POST['salvarRastreio'])) {
  $idPedido = $_POST['idPedido'];
  $codigoRastreio = $_POST['codigoRastreio'];
  $servername = "localhost";
  $username = "root";
  $password = "";
  $dbname = "loja07";
  $conn = new mysqli($servername, $username, $password, $dbname);
  if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
  }
  // Conexão com o banco de dados
  // ...

  // Atualizar o código de rastreio no banco de dados
  $sql = "UPDATE pagamentos SET codigo_rastreio = '$codigoRastreio' WHERE id = $idPedido";
  if ($conn->query($sql) === TRUE) {
    echo "<script>alert('Código de rastreio salvo com sucesso!'); window.location.href=window.location.href;</script>";
  } else {
    echo "<script>alert('Erro ao salvar o código de rastreio: " . $conn->error . "');</script>";
  }
  $conn->close();
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
$result = $conexao->query($sql);
?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
  <link rel="stylesheet" href="assets/css/app.min.css">
  <link rel="stylesheet" href="assets/css/style.css">
  <title>SISTEMA | GN</title>
  <style>
    body {
      background: linear-gradient(to right, rgb(20, 147, 220), rgb(17, 54, 71));
      color: white;
      text-align: center;
    }

    .table-bg {
      background: rgba(0, 0, 0, 0.3);
      border-radius: 15px 15px 0 0;
    }
  </style>
</head>

<body>
  <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container-fluid">
      <a class="navbar-brand" href="#">ÁREA DO FUNCIONÁRIO</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
        aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
    </div>
    <div class="d-flex">
      <a href="../painel/index.php" class="btn btn-info me-5">Painel Produtos</a>
    </div>
    <div class="d-flex">
      <a href="sair.php" class="btn btn-danger me-5">Sair</a>
    </div>
  </nav>
  <br>
  <?php
  echo "<h1>Bem vindo <u>$logado</u></h1>";
  ?>
  <br>
  <div align="center" style="padding:20px; margin-top:120px;">
  </div>
  <script src="assets/js/custom.js"></script>
  <div class="row mt-5"> <!-- Adicionando um espaçamento de 4 unidades (por exemplo) acima da tabela -->
    <div class="col-12">
      <div class="card">
        <div class="card-header" style="background-color: #D3D3D3; color: black;">
          <!-- Alterando a cor de fundo e a cor do texto do cabeçalho do cartão -->
          <h4>Vendas</h4>
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
                  <th>Ações</th>
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
                    if ($row["status"] == "Enviado") {
                      // Se o status for "Enviado", desabilitar o botão
                      echo "<td><button disabled>Enviar</button></td>";
                    } else {
                      // Se não, exibir o botão ativo
                      echo "<td><form action='' method='post'><input type='hidden' name='idPedido' value='" . $row['id'] . "'><button type='submit' name='alterarStatus'>Enviar</button></form></td>";
                    }
                    if (!empty($row['codigo_rastreio'])) {
                      echo "<td>" . htmlspecialchars($row['codigo_rastreio']) . "</td>";
                    } else {
                      echo "<td><form action='' method='post'>";
                      echo "<input type='hidden' name='idPedido' value='" . $row['id'] . "'>";
                      echo "<input type='text' name='codigoRastreio' placeholder='Digite Aqui'>";
                      echo "<button type='submit' name='salvarRastreio'>Salvar</button>";
                      echo "</form></td>";
                    }
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