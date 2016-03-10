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
        $busca_cliente = $_GET['busca_cliente'];
        if($_GET['page']){
            $pagina = $_GET['page'];
        }else{
            $pagina = 1;
        }
    }
    
    $select_busca_bairro = $banco->SelectBuscaBairro(utf8_decode($busca_bairro));
    $select_busca_tipo_cliente = $banco->SelectBuscaTipoCliente(utf8_decode($busca_cliente));
        
    $Clientes = $banco->ListaClientes(utf8_decode($busca_nome), $busca_cnpj, $busca_cpf, utf8_decode($busca_bairro), $busca_cliente, $pagina);
    
    $paginacao = $banco->MontaPaginacao($busca_nome, $busca_cnpj, $busca_cpf, utf8_decode($busca_bairro), $busca_cliente, $pagina);
        
	#Imprime valores
	$Conteudo = utf8_encode($banco->CarregaHtml('Clientes/lista-cliente'));
    $Conteudo = str_replace("<%CLIENTES%>", $Clientes, $Conteudo);
    $Conteudo = str_replace("<%PAGINACAO%>", $paginacao, $Conteudo);
    $Conteudo = str_replace("<%BUSCANOME%>", $busca_nome, $Conteudo);
    $Conteudo = str_replace("<%BUSCACNPJ%>", $busca_cnpj, $Conteudo);
    $Conteudo = str_replace("<%BUSCACPF%>", $busca_cpf, $Conteudo);
    $Conteudo = str_replace("<%SELECTBUSCABAIRRO%>", $select_busca_bairro, $Conteudo);
    $Conteudo = str_replace("<%SELECTBUSCATIPOCLIENTE%>", $select_busca_tipo_cliente, $Conteudo);
?>