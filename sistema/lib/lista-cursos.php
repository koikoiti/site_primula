<?php
	#include das funcoes da tela
	include('functions/banco-cursos.php');

	#Instancia o objeto
	$banco = new bancocursos();
    
    $pagina = 1;
    
    if($_GET){
        $busca_nome = $_GET['busca_nome'];
        $busca_cnpj = $_GET['busca_cnpj'];
        $busca_cpf = $_GET['busca_cpf'];
        $busca_bairro = $_GET['busca_bairro'];
        $busca_cliente = $_GET['busca_cliente'];
        if($_GET['page']){
            $pagina = $_GET['page'];
        }else{
            $pagina = 1;
        }
    }
    
    $Cursos = $banco->ListaCursos();
        
	#Imprime valores
	$Conteudo = utf8_encode($banco->CarregaHtml('lista-cursos'));
    $Conteudo = str_replace("<%CURSOS%>", $Cursos, $Conteudo);
    $Conteudo = str_replace("<%PAGINACAO%>", $paginacao, $Conteudo);
?>