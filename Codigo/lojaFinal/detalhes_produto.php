<?php
require_once './sheep_core/configDetalhes.php'; // Atualize o caminho conforme necessário

$loginConn = new mysqli("localhost", "root", "", "login");
if ($loginConn->connect_error) {
    die("Erro de conexão: " . $loginConn->connect_error);
}

$produtoId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($produtoId) {
    // Tente preparar a declaração SQL
    $stmt = $conn->prepare("SELECT * FROM produtos WHERE id = ?");

    // Verifique se a preparação foi bem-sucedida
    if ($stmt === false) {
        die("Erro ao preparar a declaração: " . htmlspecialchars($conn->error));
    }

    $stmt->bind_param("i", $produtoId);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        $produto = $resultado->fetch_assoc();
    } else {
        echo "Produto não encontrado.";
        exit;
    }
} else {
    echo "ID do produto não especificado.";
    exit;
}
$avaliacoes = array();

if ($produtoId) {
    $stmt = $conn->prepare("SELECT usuario_id, comentario, data_hora, avaliacao FROM avaliacoes WHERE produto_id = ? ORDER BY data_hora DESC");
    if ($stmt) {
        $stmt->bind_param("i", $produtoId);
        $stmt->execute();
        $resultado = $stmt->get_result();

        while ($linha = $resultado->fetch_assoc()) {
            // Busca o nome do usuário no outro banco de dados
            $usuarioId = $linha['usuario_id'];
            $usuarioSql = "SELECT nome FROM usuarios WHERE id = ?";
            $usuarioStmt = $loginConn->prepare($usuarioSql);
            $usuarioStmt->bind_param("i", $usuarioId);
            $usuarioStmt->execute();
            $usuarioResult = $usuarioStmt->get_result();

            if ($usuarioRow = $usuarioResult->fetch_assoc()) {
                $linha['nome_usuario'] = $usuarioRow['nome']; // Adiciona o nome do usuário ao array
            } else {
                $linha['nome_usuario'] = "Usuário Desconhecido";
            }
            $usuarioStmt->close();

            $avaliacoes[] = $linha;
        }
        $stmt->close();
    }
}

// Não se esqueça de fechar a conexão após o uso
$conn->close();
$loginConn->close();
?>


<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?= htmlspecialchars($produto['nome'], ENT_QUOTES, 'UTF-8') ?>
    </title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="detalhes.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">


</head>

<body>
    <header>

        <a href="index.php" class="logo-container">
            <div class="logo">
                <img src="assets/img/Eletro Radio - logo menor.jpg" style="max-width: 100%;" />
            </div><!--logo-->
        </a>
        <ul class="cabeçalho-links">
            <li><a href="televisoes.php">Televisões</a></li>
            <li><a href="celulares.php">Celulares</a></li>
            <li><a href="eletrodomesticos.php">Eletrodomésticos</a></li>
            <li><a href="moveis.php">Móveis</a></li>
            <li><a href="login/login.php">Login</a></li>
            <li class="carrinho">
                <a onclick="exibirMensagemLogin()">
                    <i class="fas fa-shopping-cart"></i> Carrinho
                </a>
            </li>
        </ul><!--cabeçalho-links-->
        <div class="icon"><ion-icon name="bag-outline"></ion-icon></div><!--icon-->
        <div class="search">
            <i class="fas fa-search"></i> <!-- Ícone de lupa -->
            <form action="" method="post">
                <input type="text" name="categoria" placeholder="Pesquisar por categoria...">
                <button type="submit" name="buscar">Pesquisar</button>
            </form>
        </div>
    </header>
    <div class="centralizar-verticalmente">
        <div class="container produto-container">
            <div class="produto-imagem">
                <img src="uploads/<?= htmlspecialchars($produto['capa'], ENT_QUOTES, 'UTF-8') ?>"
                    alt="<?= htmlspecialchars($produto['nome'], ENT_QUOTES, 'UTF-8') ?>" />
            </div>
            <div class="produto-info">
                <h2>
                    <?= htmlspecialchars($produto['nome'], ENT_QUOTES, 'UTF-8') ?>
                </h2>
                <p class="descricao">
                    <?= nl2br(htmlspecialchars($produto['descricao'], ENT_QUOTES, 'UTF-8')) ?>
                </p>
                <p class="preco"><strong>Preço: R$
                        <?= number_format($produto['valor'], 2, ',', '.') ?>
                    </strong></p>
                <!-- Campo de seleção de quantidade -->
                <form action="filtros/criar.php" method="post">
                    <input type="hidden" name="produto_id" value="<?= $produto['id'] ?>">
                    <div class="quantidade">
                        <label for="quantidade">Quantidade:</label>
                        <input type="number" id="quantidade" name="quantidade" value="1" min="1">
                    </div>
                    <button type="submit" class="button" name="addcarrinho">Adicionar ao Carrinho</button>
                </form>
                <!-- Adicione mais elementos aqui se necessário -->
            </div>
        </div>
    </div>
    <div class="centralizar-verticalmente">
        <!-- Seção de Comentários -->
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover" style="width:100%;">
                    <thead style="background-color: whitesmoke; color: black;">
                        <tr>
                            <th>Comentário</th>
                            <th>Estrelinhas</th>
                            <th>Cliente</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($avaliacoes as $avaliacao): ?>
                            <tr>
                                <td>
                                    <?= htmlspecialchars($avaliacao['comentario']) ?>
                                </td>
                                <td>
                                    <?= htmlspecialchars($avaliacao['avaliacao']) ?>
                                </td>
                                <td>
                                    <?= htmlspecialchars($avaliacao['nome_usuario']) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</body>

</html>