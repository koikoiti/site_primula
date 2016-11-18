<?php
    #include das funcoes da tela
	include('functions/banco-mostruario.php');

	#Instancia o objeto
	$banco = new bancomostruario();
    
    if($this->PaginaAux[0] == 'desfazer'){
    	$idmostruario = $this->PaginaAux[1];
    	$banco->VoltarAoEstoque($idmostruario);        
    }
    
    $Conteudo = utf8_encode($banco->CarregaHtml('retirar-mostruario'));
    $Conteudo = str_replace("<%TITULO%>", $titulo, $Conteudo);
    $Conteudo = str_replace("<%NOME%>", $nome, $Conteudo);
    $Conteudo = str_replace("<%CODIGO%>", $codigo, $Conteudo);
    $Conteudo = str_replace("<%CODIGOFORNECEDOR%>", $codigo_fornecedor, $Conteudo);
    $Conteudo = str_replace("<%VALOR%>", $valor, $Conteudo);
    $Conteudo = str_replace("<%ESTOQUE%>", $estoque, $Conteudo);
    $Conteudo = str_replace("<%VALORPROFISSIONAL%>", $valor_profissional, $Conteudo);
    $Conteudo = str_replace("<%VALORCONSUMIDOR%>", $valor_consumidor, $Conteudo);
    $Conteudo = str_replace("<%DESCRICAO%>", $descricao, $Conteudo);
    $Conteudo = str_replace("<%INFORMACOES%>", $informacoes, $Conteudo);
    $Conteudo = str_replace("<%PRODUTOS%>", $produtos, $Conteudo);
    $Conteudo = str_replace("<%IMAGENS%>", $imagens, $Conteudo);
    $Conteudo = str_replace("<%REQUIREFOTO%>", $require_foto, $Conteudo);
    #Botões
    $Conteudo = str_replace("<%BOTAOEXCLUIR%>", $botao_excluir, $Conteudo);
    $Conteudo = str_replace("<%BOTAOATIVARINATIVAR%>", $botao_ativar_inativar, $Conteudo);
    $Conteudo = str_replace("<%BOTAOVOLTAR%>", $botao_voltar, $Conteudo);
    $Conteudo = str_replace("<%HIDDEN%>", $hidden, $Conteudo);
?>