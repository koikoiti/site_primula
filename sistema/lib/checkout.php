<?php
	#include das funcoes da tela 
	include('functions/banco-checkout.php');

	#Instancia o objeto
	$banco = new bancocheckout();
    
    $idvenda = $this->PaginaAux[0];
    
    $Produtos = $banco->MontaProdutos($idvenda);
    $cliente = $banco->BuscaNomeCliente($idvenda);
    $totalProdutos = $banco->totalProdutos($idvenda);
    $tipoFrete = $banco->tipoFrete($idvenda);
    $valorFrete = $banco->valorFrete($idvenda);
    $porConta = $banco->porConta($idvenda);
    
    $total = $valorFrete + $totalProdutos;
    
    #Imprime valores
	$Conteudo = utf8_encode($banco->CarregaHtml('Vendas/checkout'));
    $Conteudo = str_replace('<%IDVENDA%>', $idvenda, $Conteudo);
    $Conteudo = str_replace('<%NUMEROVENDA%>', str_pad($idvenda, 5, "0", STR_PAD_LEFT), $Conteudo);
    $Conteudo = str_replace('<%PRODUTOS%>', $Produtos, $Conteudo);
    $Conteudo = str_replace('<%CLIENTE%>', $cliente, $Conteudo);
    $Conteudo = str_replace('<%TOTALPRODUTOS%>', number_format($totalProdutos, 2, ',', '.'), $Conteudo);
    $Conteudo = str_replace('<%TIPOFRETE%>', $tipoFrete, $Conteudo);
    $Conteudo = str_replace('<%PORCONTA%>', $porConta, $Conteudo);
    $Conteudo = str_replace('<%VALORFRETE%>', number_format($valorFrete, 2, ',', '.'), $Conteudo);
    $Conteudo = str_replace('<%TOTAL%>', number_format($total, 2, ',', '.'), $Conteudo);
?>