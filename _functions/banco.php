<?php
	class banco{
	   
		#Verifica se est� logado
		function VerificaSessao(){
			if(isset($_SESSION['idusuario'])){
				return true;
			}else{
                return false;
			}
		}
        
        #Verifica paginas com acesso
        function VerificaAcesso(){
            $SqlPagina = "SELECT idpagina FROM t_paginas WHERE url = '".$this->Pagina."'";
            $resultPagina = $this->Execute($SqlPagina);
            $linhaPagina = $this->Linha($resultPagina);
            if($linhaPagina){
                $rsPagina = $this->ArrayData($resultPagina);
                $idpagina = $rsPagina['idpagina'];
                $Sql = "SELECT * FROM fixo_acesso_paginas WHERE idsetor = " . $_SESSION['idsetor'] . " AND idpagina = $idpagina";
                $result = $this->Execute($Sql);
                $linha = $this->Linha($result);
                if($linha){
                    return true;
                }else{
                    return false;
                }
            }else{
                return false;
            }
        }
        
		#Funcao que inicia conexao com banco
		function Conecta(){	
			$link = mysql_connect(DB_Host,DB_User,DB_Pass);
			if (!$link) {
				$this->ChamaManutencao();
			}
			$db_selected = mysql_select_db(DB_Database, $link);
			if (!$db_selected) {
				$this->ChamaManutencao();
			}
		}	
		
		#funcao imprime conteudo
		function Imprime($Conteudo){
		    $menu = $this->MontaMenu();
			$SaidaHtml = $this->CarregaHtml('modelo');
			$SaidaHtml = str_replace('<%CONTEUDO%>',$Conteudo,$SaidaHtml);
            $SaidaHtml = str_replace('<%MENU%>',$menu,$SaidaHtml);
			$SaidaHtml = str_replace('<%URLPADRAO%>',UrlPadrao,$SaidaHtml);
			$SaidaHtml = str_replace('<%ANO%>', date("Y"),$SaidaHtml);
			echo $SaidaHtml;
		}
		
        #Monta o menu
        function MontaMenu(){
            if($this->VerificaSessao()){
                $nomeExibicao = $this->BuscaNomeExibicao();
                $menu = '<div class="cl-sidebar">
                            <div class="cl-toggle"><i class="fa fa-bars"></i></div>
                            <div class="cl-navblock">
                                <div class="menu-space">
                                    <div class="content">
                                        <a href="#"></a>
                                        <div class="side-user">
                                            <div class="info">
                                                <p><b>'.$nomeExibicao.'</b></p>
                                            </div>
                                        </div>
                                        <ul class="cl-vnavigation">
                                            <li><a href="<%URLPADRAO%>inicio"><i class="fa fa-home"></i><span>In�cio</span></a>
                                            </li>';
                    $menu .= '<li class="parent"><a href="#"><i class="fa fa-plus-circle"></i><span>Vendas</span></a>
                                <ul class="sub-menu">
                                    <li><a href="<%URLPADRAO%>lista-venda">Listar</a></li>
                                    <li><a href="<%URLPADRAO%>venda">Iniciar Nova</a></li>
                                </ul>
                             </li>
                             <li class="parent"><a href="#"><i class="fa fa-plus-circle"></i><span>Produtos</span></a>
                                <ul class="sub-menu">
                                    <li><a href="<%URLPADRAO%>lista-produto">Lista Produtos</a></li>
                                    <li><a href="<%URLPADRAO%>produto">Novo Produto</a></li>
                                    <li><a href="<%URLPADRAO%>categorias">Categorias</a></li>
                                    <li><a href="<%URLPADRAO%>entrada-produto">Entrada Produtos</a></li>
                                    <li><a href="<%URLPADRAO%>lista-entrada-produto">Lista Entrada Produtos</a></li>
                                    <li><a href="<%URLPADRAO%>monta-kit">Monta Kits</a></li>
                                    <li><a href="<%URLPADRAO%>lista-kit">Lista Kits</a></li>
                                </ul>
                             </li>
                             <li class="parent"><a href="#"><i class="fa fa-plus-circle"></i><span>Clientes</span></a>
                                <ul class="sub-menu">
                                    <li><a href="<%URLPADRAO%>lista-cliente">Listar</a></li>
                                    <li><a href="<%URLPADRAO%>cliente">Inserir Novo</a></li>
                                </ul>
                             </li>
                             <li class="parent"><a href="#"><i class="fa fa-plus-circle"></i><span>Gerenciamento</span></a>
                                <ul class="sub-menu">
                                    <li><a href="<%URLPADRAO%>">Relat�rios</a></li>
                                    <li><a href="<%URLPADRAO%>">Fluxo Financeiro</a></li>
                                </ul>
                             </li>
                             <li class="parent"><a href="#"><i class="fa fa-plus-circle"></i><span>Ferramentas</span></a>
                                <ul class="sub-menu">
                                    <li><a href="<%URLPADRAO%>lista-avisos">Lista de Avisos</a></li>
                                    <li><a href="<%URLPADRAO%>aviso">Avisos</a></li>
                                </ul>
                             </li>
                             ';
                    
                    if($_SESSION['idsetor'] <= 1){
                        $menu .= '<li class="parent"><a href="#"><i class="fa  fa-user"></i><span>Usu�rios</span></a>
                                    <ul class="sub-menu">
                                        <li><a href="<%URLPADRAO%>lista-usuario">Listar</a></li>
                                        <li><a href="<%URLPADRAO%>usuario">Inserir Novo</a></li>
                                    </ul>
                                </li>
                                <li class="parent"><a href="#"><i class="fa fa-edit"></i><span>Configura��es</span></a>
                                    <ul class="sub-menu">
                                        <li><a href="<%URLPADRAO%>gerenciar-slider">Gerenciar Slider</a></li>
                                        <li><a href="<%URLPADRAO%>gerenciar-destaques">Gerenciar Destaques</a></li>
                                    </ul>
                                </li>';
                    }
                
                $menu .='<li><a href="<%URLPADRAO%>inicio/sair"><i class="fa"></i><span>Sair</span></a></li>
                        </ul>
                        </div>
                        </div>
                        </div>
                        </div>';
            }
            return utf8_encode($menu);
        }
        
        #Busca nome exibicao
        function BuscaNomeExibicao(){
            $Sql = "SELECT nome_exibicao FROM t_usuarios WHERE idusuario = " . $_SESSION['idusuario'];
            $result = $this->Execute($Sql);
            $rs = $this->ArrayData($result);
            return $rs['nome_exibicao'];
        }
        
        #funcao que chama login
		function ChamaLogin(){
            echo $this->ChamaPhp('login');
		}
        
		#funcao que chama manutencao
		function ChamaManutencao(){
			$filename = 'html/manutencao.html';
			$handle = fopen($filename,"r");
			$Html = fread($handle,filesize($filename));
			fclose($handle);
			$SaidaHtml = $this->CarregaHtml('modelo');
			$SaidaHtml = str_replace('<%CONTEUDO%>',$Html,$SaidaHtml);
			$SaidaHtml = str_replace('<%URLPADRAO%>',UrlPadrao,$SaidaHtml);
            $SaidaHtml = str_replace('<%MENU%>',$this->MontaMenu(),$SaidaHtml);
			echo utf8_encode($SaidaHtml);
		}
		
        #Abre Sessao
		function AbreSessao($nome){
			session_start('login');
			$Sql = "SELECT * FROM t_usuarios WHERE login = '$nome'";
			$result = $this->Execute($Sql);
			$rs = $this->ArrayData($result);
			$_SESSION['nomeusuario'] = $nome;
            $_SESSION['nomeexibicao'] = $rs['nome_exibicao'];
			$_SESSION['idsetor'] = $rs['idsetor'];
			$_SESSION['idusuario'] = $rs['idusuario'];
			
		}
        
		#funcao que monta o conteudo
		function MontaConteudo(){
			#verifica se nao tem nada do lado da URLPADRAO
			if(!isset($this->Pagina)){
				return $Conteudo = $this->ChamaPhp('inicio');
			#verifica se a pagina existe e chama ela
			}else{
            	return $Conteudo = $this->ChamaPhp($this->Pagina);
           	}
		} 
		
		#Busca a pagina e verifica se existe
		function BuscaPagina(){
			$Sql = "SELECT * FROM t_paginas WHERE url = '".$this->Pagina."'";
			$result = $this->Execute($Sql);
			$num_rows = $this->Linha($result);
			if($num_rows){
				return true;
			}else{
				return false;
			}
		}
		
		#Fun��o que chama a pagina.php desejada.
		public function ChamaPhp($Nome){
			@require_once('_lib/'.$Nome.'.php');
			return $Conteudo;
		}
	
		#Fun��o que monta o html da pagina
		public function CarregaHtml($Nome){
			$filename = '_html/'.$Nome.".html";
			$handle = fopen($filename,"r");
			$Html = fread($handle,filesize($filename));
			fclose($handle);
			return $Html;
		}
		
		#Funcao que executa uma Sql e retorna.
		static function Execute($Sql){
			return mysql_query($Sql);
		}
		
		#Funcao que retorna o numero de linhas 
		static function Linha($result){
			return mysql_num_rows($result);
		}
        
        #Retorna array associativo dos dados buscados no banco
        static function ArrayData($result){
            return mysql_fetch_array($result, MYSQL_ASSOC);
        } 
		
		#Funcao que redireciona para pagina solicitada
		function RedirecionaPara($nome){
			header("Location: ".UrlPadrao.$nome);
		}
		
		#Funcao que carrega as p�ginas
		function CarregaPaginas(){
			if (strpos($_SERVER['DOCUMENT_ROOT'], 'public_html') !== false) {
				$urlDesenvolve = 'scylla';
			}else{
				$urlDesenvolve = 'site_primula';
			}
			$primeiraBol = true;
			$uri = $_SERVER["REQUEST_URI"];
			$exUrls = explode('/',$uri);
			$SizeUrls = count($exUrls)-1;

			$p = 0;
			foreach( $exUrls as $chave => $valor ){
				if( $valor != '' && $valor != $urlDesenvolve ){
					$valorUri = $valor;
					$valorUri = strip_tags($valorUri);
					$valorUri = trim($valorUri);
					$valorUri = addslashes($valorUri);
					
					if( $primeiraBol ){
						$this->Pagina = $valorUri;
						$primeiraBol = false;
					}else{
						$this->PaginaAux[$p] = $valorUri;
						$p++;
					}
				}
			}
		}
        
        #Busca o usuario pelo nome
		function BuscaUsuarioPorLogin($login){
			$Sql = "SELECT * FROM t_usuarios WHERE login='$login'";
			$result = $this->Execute($Sql);
			return $result;
		}
        
        #Busca o usuario por id
		function BuscaUsuarioPorId($idusuario){
			$Sql = "SELECT * FROM t_usuarios WHERE idusuario = '$idusuario'";
			$result = $this->Execute($Sql);
			$rs = $this->ArrayData($result);
            return $rs['nome_exibicao'];
		}
		
		#Send Mail
		function enviaEmail($nome_destinatario, $email_destinatario, $assunto, $texto_email, $alt_email, $anexo){
			#Carrega classe MAILER
			include_once("./app/PHPMailer/class.phpmailer.php");
			include("./app/PHPMailer/class.smtp.php");
			 
			$mail = new PHPMailer(true);
			try{
				#$mail->SMTPDebug = 2;                                 // Enable verbose debug output
				$mail->clearAddresses();
				$mail->isSMTP();                                      // Set mailer to use SMTP
				$mail->Host = 'br652.hostgator.com.br';  // Specify main and backup SMTP servers
				$mail->SMTPAuth = true;                               // Enable SMTP authentication
				$mail->Username = mail_login;                 // SMTP username
				$mail->Password = mail_pw;                           // SMTP password
				$mail->SMTPSecure = 'ssl';                            // Enable TLS encryption, `ssl` also accepted
				$mail->Port = 465;                                    // TCP port to connect to
		
				$mail->setFrom(mail_replyTo, 'Pr�mula TKC');
				$mail->addAddress($email_destinatario, $nome_destinatario);     // Add a recipient
				$mail->addReplyTo(mail_replyTo, mail_replyTo);
		
				if($anexo != ''){
					$mail->addAttachment($anexo);         // Add attachments
				}
				$mail->isHTML(true);                                  // Set email format to HTML
		
				$mail->Subject = $assunto;
				$mail->Body    = $texto_email;
				$mail->AltBody = $alt_email;
		
				$mail->send();
				return true;
			}catch(phpmailerException $e){
				echo $e->errorMessage();
			}
		}/*-------------------------------------------------------------------------------------------------------------*/
	}
?>