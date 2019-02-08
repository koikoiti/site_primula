<?php
    $data_venda = date("d/m/Y H:i");
    $botao_dataHoje = '';
	$contProdutos = 0;
	$desconto_subtotal = '';
    $titulo = "Venda / Orçamento";
    $botao_voltar = '<button onclick="voltar()" style="box-shadow: none;background-color: #000000;border-color: transparent;border-color: #CCCCCC;border-radius: 0;-webkit-border-radius: 0;outline: none;margin-bottom: 5px;margin-left: 3px;font-size: 13px;padding: 7px 11px;" type="button" class="btn btn-success btn-flat">Voltar</button>';
    
	#include das funcoes da tela
	include('functions/banco-venda.php');

	#Instancia o objeto
	$banco = new bancovenda();
	if($this->PaginaAux[0] == 'editar'){
	    $flagEditar = "readonly='readonly' style='pointer-events: none; touch-action: none;' tabindex='-1'";
		$idvenda = $this->PaginaAux[1];
		$rsVenda = $banco->BuscaVendaPorId($idvenda);
		$AUX_cliente = $banco->BuscaCliente($rsVenda['idcliente']);
		$data_venda = date("d/m/Y H:i", strtotime($rsVenda['data']));
		$data_vendaAux = explode(" ", $data_venda);
		if($data_vendaAux[0] == date("d/m/Y")){
		    $botao_dataHoje = '';
		}else{
		    $botao_dataHoje = '<button onclick="mudaDataVenda()" style="box-shadow: none;background-color: #000000;border-color: transparent;border-color: #CCCCCC;border-radius: 0;-webkit-border-radius: 0;outline: none;margin-bottom: 12px !important;margin-left: 3px;font-size: 13px;padding: 7px 11px;" type="button" class="btn btn-success btn-flat">Alterar a Data da Venda para hoje</button>';
		}
		$desconto_subtotal = $rsVenda['desconto_subtotal'];
		$AUXProdutos = $banco->MontaProdutosEditar($idvenda, $AUX_cliente['idtipoprofissional'], $rsVenda['idtipovenda']);
		$Produtos = $AUXProdutos["HTML"];
		
		$contProdutos = $AUXProdutos['cont'];
		$Pagamentos = $banco->MontaPagamentosEditar($idvenda);
		if($rsVenda['frete_porconta'] == 1){
			$cbfreteporconta = 'checked';
		}else{
			$cbfreteporconta = '';
		}
	}elseif($this->PaginaAux[0] == 'excluir'){
		$idvenda = $this->PaginaAux[1];
		$banco->ExcluirOrcamento($idvenda);
	}elseif($this->PaginaAux[0] == 'cancelar'){
		$idvenda = $this->PaginaAux[1];
		$banco->CancelarVenda($idvenda);
	}
	
    if(isset($_POST["acao"]) && $_POST["acao"] != '' ){
    	#var_dump($_POST);die;
    	$data_venda = $_POST['dataVenda'];
    	$idcliente = $_POST['cliente'];
    	$tipoFrete = $_POST['tipofrete'];
    	$idtipovenda = $_POST['tipovenda'];
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
    	$arrDataPagamento = $_POST['dataPagamento'];
    	$arrValorReal = $_POST['valor_real_venda'];
    	$arrValorRelatorio = $_POST['valor_relatorio'];
    	$total = str_replace('R$ ', '', $_POST['total']);
    	$total = str_replace('.', '', $total);
    	$total = str_replace(',', '.', $total);
    	$tarifa = str_replace('R$ ', '', $_POST['tarifa']);
    	$tarifa = str_replace('.', '', $tarifa);
    	$tarifa = str_replace(',', '.', $tarifa);
    	$troco_credito = $_POST['credito'];
    	$obs = utf8_decode(strip_tags(trim(addslashes($_POST["obs"]))));
    	$desconto_subtotal = $_POST['desconto_subtotal'];
    	$desconto_subtotal = str_replace('.', '', $desconto_subtotal);
    	$desconto_subtotal = str_replace(',', '.', $desconto_subtotal);
    	if($idvenda){
    		#Update
    		if($_POST['acao'] == 'orcamento'){
    		    $banco->UpdateOrcamento($idvenda, $idtipovenda, $idcliente, $tipoFrete, $valorFrete, $fretePorConta, $arrProdutos, $arrQuantidade, $arrDesconto, $arrBrinde, 1, $arrTipoPagamento, $arrPagamento, $total, $troco_credito, $obs, $arrDataPagamento, $tarifa, $desconto_subtotal, $data_venda, $arrValorReal, $arrValorRelatorio);
    			$banco->RedirecionaPara('lista-venda');
    		}elseif($_POST['acao'] == 'finaliza'){
    		    $updatedID = $banco->UpdateOrcamento($idvenda, $idtipovenda, $idcliente, $tipoFrete, $valorFrete, $fretePorConta, $arrProdutos, $arrQuantidade, $arrDesconto, $arrBrinde, 0, $arrTipoPagamento, $arrPagamento, $total, $troco_credito, $obs, $arrDataPagamento, $tarifa, $desconto_subtotal, $data_venda, $arrValorReal, $arrValorRelatorio);
    			echo "<script>window.open('".UrlPadrao."finalizar/$updatedID');location.href='".UrlPadrao."lista-venda'</script>";
    		}
    	}else{
    		#Insert
    		if($_POST['acao'] == 'orcamento'){
    		    $banco->InsereOrcamento($idcliente, $idtipovenda, $tipoFrete, $valorFrete, $fretePorConta, $arrProdutos, $arrQuantidade, $arrDesconto, $arrBrinde, 1, $arrTipoPagamento, $arrPagamento, $total, $troco_credito, $obs, $arrDataPagamento, $tarifa, $desconto_subtotal, $data_venda, $arrValorReal, $arrValorRelatorio);
    			$banco->RedirecionaPara('lista-venda');
    		}elseif($_POST['acao'] == 'finaliza'){
    		    $insertedID = $banco->InsereOrcamento($idcliente, $idtipovenda, $tipoFrete, $valorFrete, $fretePorConta, $arrProdutos, $arrQuantidade, $arrDesconto, $arrBrinde, 0, $arrTipoPagamento, $arrPagamento, $total, $troco_credito, $obs, $arrDataPagamento, $tarifa, $desconto_subtotal, $data_venda, $arrValorReal, $arrValorRelatorio);
    			echo "<script>window.open('".UrlPadrao."finalizar/$insertedID');location.href='".UrlPadrao."lista-venda'</script>";
    		}
    	}
    }
    
    $select_tipo_frete = $banco->SelectTipoFrete($rsVenda['idtipofrete']);
    $select_tipo_venda = $banco->SelectTipoVenda($rsVenda['idtipovenda'], $flagEditar);
    
    #Imprime valores
	$Conteudo = utf8_encode($banco->CarregaHtml('Vendas/novo'));
    $Conteudo = str_replace("<%TITULO%>", utf8_encode($titulo), $Conteudo);
    $Conteudo = str_replace("<%DATAVENDA%>", $data_venda, $Conteudo);
    $Conteudo = str_replace("<%BOTAODATAHOJE%>", $botao_dataHoje, $Conteudo);
    $Conteudo = str_replace("<%SELECTTIPOFRETE%>", $select_tipo_frete, $Conteudo);
    $Conteudo = str_replace("<%SELECTTIPOVENDA%>", $select_tipo_venda, $Conteudo);    

    #Botões
    $Conteudo = str_replace("<%BOTAOEXCLUIR%>", $botao_excluir, $Conteudo);
    $Conteudo = str_replace("<%BOTAOATIVARINATIVAR%>", $botao_ativar_inativar, $Conteudo);
    $Conteudo = str_replace("<%BOTAOVOLTAR%>", $botao_voltar, $Conteudo);
    
    #Replaces
    $Conteudo = str_replace("<%IDCLIENTE%>", $AUX_cliente['idcliente'], $Conteudo);
    $Conteudo = str_replace("<%INFO%>", $AUX_cliente['cpf'] . $AUX_cliente['cnpj'], $Conteudo);
    $Conteudo = str_replace("<%IDTIPOPROFISSIONAL%>", $AUX_cliente['idtipoprofissional'], $Conteudo);
    $Conteudo = str_replace("<%NOMECLIENTE%>", utf8_encode($AUX_cliente['nome']), $Conteudo);
    $Conteudo = str_replace("<%FRETE%>", $rsVenda['valor_frete'], $Conteudo);
    $Conteudo = str_replace("<%TARIFA%>", $rsVenda['tarifa'], $Conteudo);
    $Conteudo = str_replace("<%PRODUTOS%>", $Produtos, $Conteudo);
    $Conteudo = str_replace("<%CONTPRODUTOS%>", $contProdutos, $Conteudo);
    $Conteudo = str_replace("<%PAGAMENTOS%>", $Pagamentos, $Conteudo);
    $Conteudo = str_replace("<%CBFRETEPORCONTA%>", $cbfreteporconta, $Conteudo);
    $Conteudo = str_replace("<%OBS%>", utf8_encode($rsVenda['obs']), $Conteudo);
    $Conteudo = str_replace("<%DESCONTOSUBTOTAL%>", $desconto_subtotal, $Conteudo);
?>