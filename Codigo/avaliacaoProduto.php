<?php
session_start(); // Iniciar a sessão no topo do script
require_once './sheep_core/configDetalhes.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_avaliacao'])) {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    if ($conn->connect_error) {
        die("Falha na conexão: " . $conn->connect_error);
    }

    $produtoId = isset($_POST['produto_id']) ? intval($_POST['produto_id']) : 0;
    $usuarioId = isset($_SESSION['id_cliente']) ? $_SESSION['id_cliente'] : 0; // Obter o ID do usuário da sessão
    $avaliacao = isset($_POST['avaliacao']) ? intval($_POST['avaliacao']) : 0;
    $comentario = isset($_POST['comentario']) ? $conn->real_escape_string($_POST['comentario']) : '';

    if ($produtoId > 0 && $usuarioId > 0 && $avaliacao > 0 && !empty($comentario)) {
        $stmt = $conn->prepare("INSERT INTO avaliacoes (produto_id, usuario_id, avaliacao, comentario) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiis", $produtoId, $usuarioId, $avaliacao, $comentario);

        if ($stmt->execute()) {
            $_SESSION['mensagem_sucesso'] = "Avaliação enviada com sucesso!";
        } else {
            $_SESSION['mensagem_erro'] = "Erro ao enviar avaliação: " . $stmt->error;
        }

        $stmt->close();
    } else {
        $_SESSION['mensagem_erro'] = "Dados inválidos.";
    }

    $conn->close();
    header("Location: detalhes_produtoLogado.php?id=" . $produtoId);
    exit();
}
?>