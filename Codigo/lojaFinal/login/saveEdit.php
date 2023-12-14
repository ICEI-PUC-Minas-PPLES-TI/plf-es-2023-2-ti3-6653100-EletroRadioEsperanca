<?php
    // isset -> serve para saber se uma variável está definida
    include_once('config.php');
    if(isset($_POST['update']))
    {
        $id = $_POST['id'];
        $nome = $_POST['nome'];
        $email = $_POST['email'];
        $senha = $_POST['senha'];
       
        $sexo = $_POST['sexo'];
        $cpf = $_POST['cpf'];
        
        $sqlInsert = "UPDATE funcionarios 
        SET nome='$nome',senha='$senha',email='$email',cpf='$cpf'
        WHERE id=$id";
        $result = $conexao->query($sqlInsert);
        print_r($result);
    }
    header('Location: sistemaAdm.php');

?>