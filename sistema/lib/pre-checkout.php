<?php
    #include das funcoes da tela
	include('functions/banco-venda.php');

	#Instancia o objeto
	$banco = new bancovenda();
    
    if(isset($_POST["acao"]) && $_POST["acao"] != '' ){
    	var_dump($_POST);die;
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
        
        $idvenda = $banco->InsereOrcamento($idcliente, $tipoFrete, $valorFrete, $fretePorConta, $arrProdutos, $arrQuantidade, $arrDesconto, $arrBrinde, 0);
        echo "<script>window.open('".UrlPadrao."finalizar/$idvenda');location.href='".UrlPadrao."lista-venda'</script>";
    }
?>