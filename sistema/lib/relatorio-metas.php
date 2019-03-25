<?php
	#include das funcoes da tela 
	include('functions/banco-relatorio-metas.php');
	
	if($_SESSION['idsetor'] != 1){
	    $idresponsavel = $_SESSION['idusuario'];
	}
	$dataIni = date("Y-m-01");
	$dataFim = date("Y-m-d");
	$botao_limpar = '';
	$botao_imprimir = '';
	
	#Instancia o objeto
	$banco = new bancorelatoriometas();
    
	if($this->PaginaAux[0] == 'imprimir'){
	    if($_GET){
	        $dataIni = $_GET['dataIni'];
	        $dataFim = $_GET['dataFim'];
	        if($_SESSION['idsetor'] != 1){
	            $idresponsavel = $_SESSION['idusuario'];
	        }else{
	            $idresponsavel = $_GET['busca_responsavel'];
	        }
	        $marca = $_GET['marca'];
	        $idtipopagamento = $_GET['idtipopagamento'];
	        $cidade = $_GET['cidade'];
	        $banco->MontaImpressao($dataIni, $dataFim, $idresponsavel, $marca, $idtipopagamento, $cidade);
	    }
	}
	
    if($_GET){
    	$dataIni = $_GET['dataIni'];
    	$dataFim = $_GET['dataFim'];
    	if($_SESSION['idsetor'] != 1){
    	    $idresponsavel = $_SESSION['idusuario'];
    	}else{
    	   $idresponsavel = $_GET['busca_responsavel'];
    	}
    	$marca = $_GET['busca_marca'];
    	$idtipopagamento = $_GET['busca_pgto'];
    	$cidade = $_GET['busca_cidade'];
    	$botao_limpar = '<a href="'.UrlPadrao.'relatorio-metas" class="btn btn-danger"><i class="fa fa-times"></i></a>';
    	if($idresponsavel){
    	    $urlImprimir = "dataIni=$dataIni&dataFim=$dataFim&idresponsavel=$idresponsavel&marca=$marca&idtipopagamento=$idtipopagamento&cidade=$cidade";
    	    $botao_imprimir = '<a target="_blank" href="' . UrlPadrao . 'relatorio-metas/imprimir/?' . $urlImprimir . '" class="btn btn-info"><i class="fa fa-print"></i> Imprimir</a>';
    	}
    }
    
    $Relatorio = $banco->MontaRelatorio($dataIni, $dataFim, $idresponsavel, $marca, $idtipopagamento, $cidade);
    $select_usuarios = $banco->MontaUsuarios($idresponsavel);
    $select_tipoPagamento = $banco->MontaSelectTipoPagamento($idtipopagamento);
    $select_cidades = $banco->MontaCidadesClientes($cidade);
    
    #Imprime valores
	$Conteudo = utf8_encode($banco->CarregaHtml('relatorio-metas'));
    $Conteudo = str_replace("<%RELATORIO%>", $Relatorio, $Conteudo);
    $Conteudo = str_replace("<%SELECTUSUARIOS%>", $select_usuarios, $Conteudo);
    $Conteudo = str_replace("<%SELECTTIPOPAGAMENTO%>", $select_tipoPagamento, $Conteudo);
    $Conteudo = str_replace("<%SELECTCIDADESCLIENTES%>", $select_cidades, $Conteudo);
    $Conteudo = str_replace("<%BUSCADATAINI%>", $dataIni, $Conteudo);
    $Conteudo = str_replace("<%BUSCADATAFIM%>", $dataFim, $Conteudo);
    $Conteudo = str_replace("<%BUSCAMARCA%>", $marca, $Conteudo);
    $Conteudo = str_replace("<%BOTAOLIMPARFILTRO%>", $botao_limpar, $Conteudo);
    $Conteudo = str_replace("<%BOTAOIMPRIMIR%>", $botao_imprimir, $Conteudo);
?>