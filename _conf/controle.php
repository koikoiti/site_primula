<?php
    #Inicializa a sessao
	session_start('login');
    
	include('_functions/banco.php');
	include('tags.php');
	
	class controle{
		public function __construct(){
			$banco = new banco;
			$banco->Conecta();
			$banco->CarregaPaginas();
            
            $Conteudo = $banco->MontaConteudo();
            $banco->Imprime($Conteudo);
		}
	}
?>