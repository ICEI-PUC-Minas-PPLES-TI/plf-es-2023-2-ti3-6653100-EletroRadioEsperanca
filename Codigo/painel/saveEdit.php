<?php
// isset -> serve para saber se uma variável está definida
include_once('config.php');
if (isset($_POST['update'])) {

    $id = $_POST['id'];
    $capa = $_POST['capa'];
    $nome = $_POST['nome'];
    $valor = $_POST['valor'];

    $sqlInsert = "UPDATE produtos 
        SET capa ='$capa',nome = '$nome',valor = '$valor'
        WHERE id=$id";
    $result = $conexao->query($sqlInsert);
    print_r($result);
}
header('Location: editarProduto.php');

?>