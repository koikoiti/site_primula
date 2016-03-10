<?php
	#include das funcoes da tela gerenciar-slider
	include('functions/banco-gerenciar.php');

	#Instancia o objeto
	$banco = new bancogerenciar();
    
    $slider = $banco->BuscaImagensSlider();
    
    #Trabalha com Post
	if(isset($_POST["acao"]) && $_POST["acao"] != '' ){
        $texto1 = utf8_decode(strip_tags(trim(addslashes($_POST["texto1"]))));
        $texto2 = utf8_decode(strip_tags(trim(addslashes($_POST["texto2"]))));
        $link = utf8_decode(strip_tags(trim(addslashes($_POST["link"]))));
        $file = $_FILES['slide'];
        
        $banco->InsereSlide($texto1, $texto2, $link, $file);
        $banco->RedirecionaPara('gerenciar-slider');
    }
    
	#Imprime valores
	$Conteudo = utf8_encode($banco->CarregaHtml('gerenciar-slider'));
    $Conteudo = str_replace('<%SLIDER%>', $slider, $Conteudo);
?>