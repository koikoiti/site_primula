<?php
	$dataEntrada = date("Y-m-d");
    $titulo = "Entrada Produtos";
    $botao_voltar = '<button onclick="voltar()" style="box-shadow: none;background-color: #000000;border-color: transparent;border-color: #CCCCCC;border-radius: 0;-webkit-border-radius: 0;outline: none;margin-bottom: 5px;margin-left: 3px;font-size: 13px;padding: 7px 11px;" type="button" class="btn btn-success btn-flat">Voltar</button>';
    
	#include das funcoes da tela inico
	include('functions/banco-entrada-produto.php');

	#Instancia o objeto
	$banco = new bancoentradaproduto();
    
	if($this->PaginaAux[0] == 'editar'){
		$identrada = $this->PaginaAux[1];
		$rsEntrada = $banco->BuscaEntradaPorId($identrada);
		$dataEntrada = $rsEntrada['data_entrada'];
		$fornecedor = utf8_encode($rsEntrada['fornecedor']);
		$nf = $rsEntrada['nf'];
		$valor = $rsEntrada['valor'];
		$frete = $rsEntrada['frete'];
		$Produtos = $banco->BuscaProdutosEditar($identrada);
	}elseif($this->PaginaAux[0] == 'visualizar'){
		$identrada = $this->PaginaAux[1];
	}elseif($this->PaginaAux[0] == 'excluir'){
		$identrada = $this->PaginaAux[1];
		$banco->ExcluirEntrada($identrada);
	}
	
    if(isset($_POST["acao"]) && $_POST["acao"] != '' ){
    	$dataEntrada = $_POST['data_entrada'];
        $fornecedor = utf8_decode(strip_tags(trim(addslashes($_POST["fornecedor"]))));
        $nf = utf8_decode(strip_tags(trim(addslashes($_POST["nf"]))));
        $valor = utf8_decode(strip_tags(trim(addslashes($_POST["valor"]))));
        $valor = str_replace('.', '', $valor);
        $valor = str_replace(',', '.', $valor);
        $frete = utf8_decode(strip_tags(trim(addslashes($_POST["frete"]))));
        $frete = str_replace('.', '', $frete);
        $frete = str_replace(',', '.', $frete);
        $arrProdutos = $_POST['produtos'];
        $arrQuantidade = $_POST['quantidade'];
        $arrLote = $_POST['lote'];
        $arrVencimento = $_POST['validade'];
        
        if($identrada){
        	$banco->AtualizaEntrada($identrada, $fornecedor, $nf, $valor, $frete, $arrProdutos, $arrQuantidade, $arrLote, $arrVencimento, $dataEntrada);
        }else{
        	$banco->InsereEntrada($fornecedor, $nf, $valor, $frete, $arrProdutos, $arrQuantidade, $arrLote, $arrVencimento, $dataEntrada);
        }
    }
    
    $Conteudo = utf8_encode($banco->CarregaHtml('Produtos/entrada-produto'));
    $Conteudo = str_replace("<%TITULO%>", $titulo, $Conteudo);
    $Conteudo = str_replace("<%DATAENTRADA%>", $dataEntrada, $Conteudo);
    $Conteudo = str_replace("<%FORNECEDOR%>", $fornecedor, $Conteudo);
    $Conteudo = str_replace("<%NF%>", $nf, $Conteudo);
    $Conteudo = str_replace("<%VALOR%>", $valor, $Conteudo);
    $Conteudo = str_replace("<%FRETE%>", $frete, $Conteudo);
    $Conteudo = str_replace("<%PRODUTOS%>", $Produtos, $Conteudo);
    #Botões
    $Conteudo = str_replace("<%BOTAOEXCLUIR%>", $botao_excluir, $Conteudo);
    $Conteudo = str_replace("<%BOTAOATIVARINATIVAR%>", $botao_ativar_inativar, $Conteudo);
    $Conteudo = str_replace("<%BOTAOVOLTAR%>", $botao_voltar, $Conteudo);
?>