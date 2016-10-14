<?php
    $titulo = "Venda / Orçamento";
    $botao_voltar = '<button onclick="voltar()" style="box-shadow: none;background-color: #000000;border-color: transparent;border-color: #CCCCCC;border-radius: 0;-webkit-border-radius: 0;outline: none;margin-bottom: 5px;margin-left: 3px;font-size: 13px;padding: 7px 11px;" type="button" class="btn btn-success btn-flat">Voltar</button>';
    
	#include das funcoes da tela
	include('functions/banco-venda.php');

	#Instancia o objeto
	$banco = new bancovenda();
	if($this->PaginaAux[0] == 'editar'){
		$idvenda = $this->PaginaAux[1];
		$rsVenda = $banco->BuscaVendaPorId($idvenda);
		$AUX_cliente = $banco->BuscaCliente($rsVenda['idcliente']);
		$Produtos = $banco->MontaProdutosEditar($idvenda);
	}elseif($this->PaginaAux[0] == 'excluir'){
		$idvenda = $this->PaginaAux[1];
		$banco->ExcluirOrcamento($idvenda);
	}elseif($this->PaginaAux[0] == 'cancelar'){
		$idvenda = $this->PaginaAux[1];
		$banco->CancelarVenda($idvenda);
	}
	
    if(isset($_POST["acao"]) && $_POST["acao"] != '' ){
    	#var_dump($_POST);die;
    	$idcliente = $_POST['cliente'];
    	$tipoFrete = $_POST['tipofrete'];
    	$valorFrete = utf8_decode(strip_tags(trim(addslashes($_POST["frete"]))));
    	$valorFrete = str_replace('.', '', $valorFrete);
    	$valorFrete = str_replace(',', '.', $valorFrete);
    	$fretePorConta = $_POST['por_conta'];
    	$arrProdutos = $_POST['produtos'];
    	$arrQuantidade = $_POST['quantidade'];
    	$arrDesconto = $_POST['desconto_valor'];
    	$arrBrinde = $_POST['brinde'];
    	$arrTipoPagamento = $_POST['tipoPagamento'];
    	$arrPagamento = $_POST['pagamento'];
    	$total = str_replace('R$ ', '', $_POST['total']);
    	$total = str_replace('.', '', $total);
    	$total = str_replace(',', '.', $total);
    	$troco_credito = $_POST['credito'];
    	if($idvenda){
    		#Update
    	}else{
    		#Insert
    		if($_POST['acao'] == 'orcamento'){
    			$banco->InsereOrcamento($idcliente, $tipoFrete, $valorFrete, $fretePorConta, $arrProdutos, $arrQuantidade, $arrDesconto, $arrBrinde, 1, $arrTipoPagamento, $arrPagamento, $total, $troco_credito);
    			$banco->RedirecionaPara('lista-venda');
    		}elseif($_POST['acao'] == 'finaliza'){
    			$insertedID = $banco->InsereOrcamento($idcliente, $tipoFrete, $valorFrete, $fretePorConta, $arrProdutos, $arrQuantidade, $arrDesconto, $arrBrinde, 0, $arrTipoPagamento, $arrPagamento, $total, $troco_credito);
    			echo "<script>window.open('".UrlPadrao."finalizar/$insertedID');location.href='".UrlPadrao."lista-venda'</script>";
    		}
    	}
    }
    
    $select_tipo_frete = $banco->SelectTipoFrete();
    
    #Imprime valores
	$Conteudo = utf8_encode($banco->CarregaHtml('Vendas/novo'));
    $Conteudo = str_replace("<%TITULO%>", utf8_encode($titulo), $Conteudo);
    $Conteudo = str_replace("<%SELECTTIPOFRETE%>", $select_tipo_frete, $Conteudo);

    #Botões
    $Conteudo = str_replace("<%BOTAOEXCLUIR%>", $botao_excluir, $Conteudo);
    $Conteudo = str_replace("<%BOTAOATIVARINATIVAR%>", $botao_ativar_inativar, $Conteudo);
    $Conteudo = str_replace("<%BOTAOVOLTAR%>", $botao_voltar, $Conteudo);
    
    #Replaces
    $Conteudo = str_replace("<%NOMECLIENTE%>", $AUX_cliente['nome'], $Conteudo);
    $Conteudo = str_replace("<%PRODUTOS%>", $Produtos, $Conteudo);
?>