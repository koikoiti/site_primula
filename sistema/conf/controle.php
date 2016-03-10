<?php
    #Inicializa a sessao
	session_start('login');
    
	include('functions/banco.php');
	include('tags.php');
	
	class controle{
		public function __construct(){
			$banco = new banco;
			$banco->Conecta();
			$banco->CarregaPaginas();
            
            #Verifica se está logado
            $logado = $banco->VerificaSessao();
            
            if($logado){        
                $Conteudo = $banco->MontaConteudo();
                $banco->Imprime($Conteudo);
            }else{
                $Conteudo = $banco->ChamaLogin();
			}
		}
	}
?>