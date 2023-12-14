<?php
session_start();

if (isset($_POST['submit']) && !empty($_POST['email']) && !empty($_POST['senha'])) {
    include_once('config.php');
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    $sql = "SELECT * FROM adm WHERE email = '$email' and senha = '$senha'";

    $result = $conexao->query($sql);

    if(mysqli_num_rows($result) < 1) {
        unset($_SESSION['email']);
        unset($_SESSION['senha']);
        header('Location: loginAdm.php'); // Redireciona para a página de login em caso de falha de autenticação
    } else {
        $_SESSION['email'] = $email;
        $_SESSION['senha'] = $senha;
        header('Location: sistemaAdm.php'); // Redireciona para a página de sistema de administrador em caso de sucesso de autenticação
    }
} else {
    header('Location: loginAdm.php');
}
?>
