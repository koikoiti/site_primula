<?php
	#include das funcoes da tela login
	include('functions/banco-login.php');

	#Instancia o objeto
	$banco = new bancologin();
    
    #Trabalha com Post
	if(isset($_POST["acao"]) && $_POST["acao"] != '' ){
        #recupera os dados passados por POST
		$login = strip_tags(trim(addslashes($_POST["login"])));
        $senha = sha1($_POST["senha"]);
        
		#Busca Usuario no banco e verifica se ele existe
		$result = $banco->BuscaUsuarioPorLogin($login);
		$num_rows = $banco->Linha($result);
		$rs = $banco->ArrayData($result);
		$senhab = $rs['senha'];
		if(!$num_rows){
			#Se usuario nao existir, msg de erro de usuario
			$msg = "MsgErro_Usuario";
		#se o usuario existir, verifica se as senhas batem
		}elseif($senha === $senhab){
			#se o usuario estiver ativo, inicia a sessao e redireciona para a tela principal
			$banco->AbreSessao($login);
			$banco->RedirecionaPara('inicio');
		}else{
			$msg = "MsgErro_Senha";
		}
    }
     
	#Imprime valores
	$Conteudo = utf8_encode($banco->CarregaHtml('login'));
?>