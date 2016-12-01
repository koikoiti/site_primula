<?php
	#include das funcoes da tela inico
	include('functions/banco-cliente.php');

	#Instancia o objeto
	$banco = new bancocliente();
    
    $pagina = 1;
    
    if($_GET){
        $busca_nome = $_GET['busca_nome'];
        $busca_cnpj = $_GET['busca_cnpj'];
        $busca_cpf = $_GET['busca_cpf'];
        $busca_bairro = $_GET['busca_bairro'];
        $busca_cidade = $_GET['busca_cidade'];
        $busca_telefone = $_GET['busca_telefone'];
        if($_GET['page']){
            $pagina = $_GET['page'];
        }else{
            $pagina = 1;
        }
    }
    
    $select_busca_bairro = $banco->SelectBuscaBairro(utf8_decode($busca_bairro));
    $select_busca_cidade = $banco->SelectBuscaCidade(utf8_decode($busca_cidade));
    #$select_busca_tipo_cliente = $banco->SelectBuscaTipoCliente(utf8_decode($busca_cliente));
    
    if(isset($_GET['minhas_interacoes'])){
    	$Clientes = $banco->MontaMinhasInteracoes();
    	$paginacao = '';
    	$minhas_interacoes_title = utf8_encode(" - Minhas Interações");
    }else{
    	$Clientes = $banco->ListaClientes(utf8_decode($busca_nome), $busca_cnpj, $busca_cpf, utf8_decode($busca_bairro), $busca_telefone, $pagina, utf8_decode($busca_cidade));
    	$paginacao = $banco->MontaPaginacao($busca_nome, $busca_cnpj, $busca_cpf, utf8_decode($busca_bairro), $busca_telefone, $pagina, utf8_decode($busca_cidade));
    	$minhas_interacoes_title = '';
    }
           
	#Imprime valores
	$Conteudo = utf8_encode($banco->CarregaHtml('Clientes/lista-cliente'));
    $Conteudo = str_replace("<%CLIENTES%>", $Clientes, $Conteudo);
    $Conteudo = str_replace("<%PAGINACAO%>", $paginacao, $Conteudo);
    $Conteudo = str_replace("<%BUSCANOME%>", $busca_nome, $Conteudo);
    $Conteudo = str_replace("<%BUSCACNPJ%>", $busca_cnpj, $Conteudo);
    $Conteudo = str_replace("<%BUSCACPF%>", $busca_cpf, $Conteudo);
    $Conteudo = str_replace("<%BUSCATELEFONE%>", $busca_telefone, $Conteudo);
    $Conteudo = str_replace("<%SELECTBUSCABAIRRO%>", $select_busca_bairro, $Conteudo);
    $Conteudo = str_replace("<%SELECTBUSCACIDADE%>", $select_busca_cidade, $Conteudo);
    $Conteudo = str_replace("<%MINHASINTERACOESTITLE%>", $minhas_interacoes_title, $Conteudo);
?>