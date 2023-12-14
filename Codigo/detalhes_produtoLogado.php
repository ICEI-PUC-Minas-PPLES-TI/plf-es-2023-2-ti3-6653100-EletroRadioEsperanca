<?php
session_start();

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

if (isset($_SESSION['id_cliente'])) {
    $idClienteLogado = $_SESSION['id_cliente'];
} else {
    // O usuário não está logado, redirecione para a página de login ou exiba uma mensagem
    header("Location: login.php");
    exit;
}
?>
<?php
ob_start();

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
    <style>
        .centralizar {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .table-responsive {
            border-radius: 10px;
            overflow: hidden;
        }

        .table {
            margin-bottom: 0;
            /* Remova a margem inferior padrão da tabela */
        }

        .table thead {
            background-color: #4CAF50;
            /* Cor de fundo do cabeçalho da tabela */
            color: white;
            /* Cor do texto do cabeçalho da tabela */
        }

        .table th,
        .table td {
            text-align: center;
            /* Alinhar texto ao centro das células */
            vertical-align: middle;
            /* Alinhar conteúdo verticalmente */
        }

        /* Estilização das estrelas */
        .table td:nth-child(2) {
            font-size: 1.25em;
            color: black;
        }
    </style>
</head>

<body>
    <header>
        <style>
            header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 10px;
            }

            .logo-container {
                display: flex;
                align-items: center;
            }

            .logo {
                margin-right: 20px;
                /* Espaçamento entre a borda e o logotipo */
                max-width: 100px;
                /* Defina o máximo de largura para o logotipo */
            }

            .cabeçalho-links {
                display: flex;
                align-items: center;
            }

            .cabeçalho-links li {
                list-style-type: none;
                margin-right: 20px;
            }

            .cabeçalho-links li a {
                text-decoration: none;
                color: black;
                /* Definindo a cor do texto como preto */
            }

            .icon {
                padding-left: 20px;
            }

            .search {
                padding-left: 20px;
                display: flex;
                align-items: center;
            }

            @media (max-width: 768px) {
                header {
                    flex-direction: column;
                    align-items: flex-start;
                }

                .logo-container {
                    margin-bottom: 10px;
                }

                .cabeçalho-links,
                .icon,
                .search {
                    margin-top: 10px;
                    padding-left: 0;
                }

                .logo {
                    margin-right: 0;
                }
            }
        </style>

        <a href="indexLogado.php" class="logo-container">
            <div class="logo">
                <img src="assets/img/Eletro Radio - logo menor.jpg" style="max-width: 100%;" />
            </div><!--logo-->
        </a>

        <ul class="cabeçalho-links">
            <li><a href="televisoes.php">Televisões</a></li>
            <li><a href="celulares.php">Celulares</a></li>
            <li><a href="eletrodomesticos.php">Eletrodomésticos</a></li>
            <li><a href="moveis.php">Móveis</a></li>
            <li><a href="login/sistema.php">Área do cliente</a></li>
            <li>
                <a href="pagamento/public/mycart.php">
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
                <!-- Formulário para adicionar ao carrinho -->
                <form action="filtros/criar.php" method="post">
                    <input type="hidden" name="id_produto" value="<?= $produto['id'] ?>">
                    <input type="hidden" name="valor" value="<?= $produto['valor'] ?>">
                    <input type="hidden" name="ip" value="<?= $ip ?>">
                    <button type="submit" class="button" name="addcarrinho">Adicionar ao Carrinho</button>
                </form>
                <!-- Adicione mais elementos aqui se necessário -->
            </div>
        </div>
    </div>

    <div class="centralizar-verticalmente1">
        <div class="centralizar">
            <h2 style="text-align: center; margin-bottom: 20px;">Comentários</h2>
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
    </div>

    <div class="centralizar-verticalmente">
        <div class="container produto-container">
            <div class="produto-avaliacao">
                <h3>Avalie este Produto</h3>
                <form action="avaliacaoProduto.php" method="post">
                    <input type="hidden" name="produto_id" value="<?= $produto['id'] ?>">
                    <input type="hidden" name="usuario_id" value="<?= $idClienteLogado ?>">
                    <!-- Adiciona o ID do usuário -->
                    <!-- Classificação por Estrelas (Exemplo Simples) -->
                    <div class="avaliacao-estrelas">
                        <input type="radio" id="estrela5" name="avaliacao" value="5" class="estrela-input" /><label
                            for="estrela5" class="estrela-label fa fa-star"></label>
                        <input type="radio" id="estrela4" name="avaliacao" value="4" class="estrela-input" /><label
                            for="estrela4" class="estrela-label fa fa-star"></label>
                        <input type="radio" id="estrela3" name="avaliacao" value="3" class="estrela-input" /><label
                            for="estrela3" class="estrela-label fa fa-star"></label>
                        <input type="radio" id="estrela2" name="avaliacao" value="2" class="estrela-input" /><label
                            for="estrela2" class="estrela-label fa fa-star"></label>
                        <input type="radio" id="estrela1" name="avaliacao" value="1" class="estrela-input" /><label
                            for="estrela1" class="estrela-label fa fa-star"></label>
                    </div>
                    <!-- Campo de Texto para Comentário -->
                    <div class="campo-comentario">
                        <label for="comentario">Seu Comentário:</label>
                        <textarea id="comentario" name="comentario" rows="4"
                            placeholder="Escreva seu comentário aqui..."></textarea>
                    </div>
                    <button type="submit" class="button" name="submit_avaliacao">Enviar Avaliação</button>
                </form>
            </div>
        </div>
    </div>
</body>

</html>