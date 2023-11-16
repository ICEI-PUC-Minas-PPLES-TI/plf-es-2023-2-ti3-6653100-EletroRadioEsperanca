<?php
session_start();

// Conexão com o banco de dados
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'loja07';
$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $rating = isset($_POST['rating']) ? (int) $_POST['rating'] : 0;
    $comments = isset($_POST['comments']) ? trim($_POST['comments']) : '';
    $idPagamento = $_SESSION['idPagamento'] ?? null;

    // Aqui você deve substituir 1 pelo ID real do usuário logado
    $usuarioId = $_SESSION['id_usuario_logado'] ?? 1; // Exemplo

    if (!$idPagamento) {
        die("Erro: ID de pagamento não encontrado.");
    }

    $stmt = $conn->prepare("INSERT INTO avaliacoes_compra (usuario_id, idPagamento, nota, comentario) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiis", $usuarioId, $idPagamento, $rating, $comments);

    if ($stmt->execute()) {
        header("Location: /lojaFinalTeste/indexLogado.php");
        exit;
    } else {
        // Tratar erro de forma adequada
        // Logar o erro e mostrar uma mensagem genérica ao usuário, por exemplo
        error_log("Erro ao inserir avaliação: " . $stmt->error);
        echo "Ocorreu um erro ao enviar sua avaliação.";
    }

    $stmt->close();
}

$conn->close();
?>