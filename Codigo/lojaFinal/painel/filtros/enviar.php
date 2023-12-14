<?php

ob_start();
require('../../sheep_core/config.php');

$enviarCompras = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if(isset($enviarCompras)){
    
   
    $salvar = new FinalizaPg();
    $salvar->EnviaPedido($enviarCompras);

    if($salvar->getResultado()){
      header("Location: ".HOME."/painel/clientes.php?sucesso=true");
    }else{
      header("Location: ".HOME."/painel/clientes.php?erro=true");
    }

}




?>