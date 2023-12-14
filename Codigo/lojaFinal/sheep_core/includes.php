<?php

define('SHEEP_IMG', './../sheep_temas/sheep-imagens/');
define('SHEEP_IMG_LOGO', '../../../sheep_temas/sheep-imagens-logo/');
define('SHEEP_IMG_PAINEL', './sheep_temas/sheep-imagens/');
define('SHEEP_AUDIO', '../../../sheep_temas/sheep-midias/');
define('SHEEP_VERSAO', 'Versão: [ 1.0.0 ] - <b>Atualizado dia: 01/10/2021</b>');
define('sheep', '<center><h2>Atenção!</h2></center><br>');

function sheep_classes($sheepClasses)
{
    $sheepDiretorio = ['diretor', 'funcionarios', 'gerentes_operacionais', 'gerentes'];
    $sheepFiscaliza = null;
    foreach ($sheepDiretorio as $sheepNomeDiretorio):
        if (!$sheepFiscaliza && file_exists(__DIR__ . '/' . "{$sheepNomeDiretorio}" . '/' . "{$sheepClasses}.php") && !is_dir(__DIR__ . '/' . "{$sheepNomeDiretorio}" . '/' . "{$sheepClasses}.php")):
            include_once(__DIR__ . '/' . "{$sheepNomeDiretorio}" . '/' . "{$sheepClasses}.php");
            $sheepFiscaliza = true;
        endif;
    endforeach;
    if (!$sheepFiscaliza):
        echo "Não foi possível incluir {$sheepClasses}.php";
        exit();
    endif;
}

spl_autoload_register("sheep_classes");

if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
    $https = 'https://';
} else {
    $https = 'http://';
}

define('HOME', $https . SHEEP_URL);
define('PASTA_DO_PAINEL', '/login/painel');
define('URL_CAMINHO_PAINEL', HOME . '/' . PASTA_DO_PAINEL);
define('SHEEP_LAYOUT', 'site');
define('CAMINHO_TEMAS', HOME . '/' . 'sheep_temas' . '/' . SHEEP_LAYOUT);
define('SOLICITAR_TEMAS', 'sheep_temas' . '/' . SHEEP_LAYOUT);
define('MODELO', 'sheep_temas' . '/' . SHEEP_LAYOUT);
define('FILTROS', 'sheep.php?m=');

$ipsheep = filter_input(INPUT_SERVER, 'SERVER_ADDR', FILTER_SANITIZE_STRIPPED);
if ($ipsheep == '::1' || $ipsheep == '127.0.0.1') {
    null;
} else {
    $Assunto = 'UM SITE INSTALADO COM SHEEP PHP DOMINIO: ' . $_SERVER['SERVER_NAME'];
    $DestinoNome = 'Sheep PHP';
    $DestinoEmail = 'br';
    $headers = 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/html; charset=UTF8' . "\r\n";
    $Mensagem = "DOMINIO: {$_SERVER['SERVER_NAME']} <br> IP SERVIDOR: {$_SERVER['SERVER_ADDR']}";
    mail($DestinoEmail, $Assunto, $Mensagem, $headers);
}
?>