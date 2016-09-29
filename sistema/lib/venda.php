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
	}
	
    if(isset($_POST["acao"]) && $_POST["acao"] != '' ){
        $idcliente = $_POST['cliente'];
        $tipoFrete = $_POST['tipofrete'];
        $valorFrete = utf8_decode(strip_tags(trim(addslashes($_POST["frete"]))));
        $valorFrete = str_replace('.', '', $valorFrete);
        $valorFrete = str_replace(',', '.', $valorFrete);
        $fretePorConta = $_POST['por_conta'];
        $arrProdutos = $_POST['produtos'];
        $arrQuantidade = $_POST['quantidade'];
        $arrDesconto = $_POST['desconto'];
        $arrBrinde = $_POST['brinde'];
        
        $banco->InsereOrcamento($idcliente, $tipoFrete, $valorFrete, $fretePorConta, $arrProdutos, $arrQuantidade, $arrDesconto, $arrBrinde, 1);
        $banco->RedirecionaPara('lista-venda');
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
?>