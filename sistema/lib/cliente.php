<?php
    $titulo = "Novo Cliente";
    $botao_voltar = '<button onclick="voltar()" style="box-shadow: none;background-color: #000000;border-color: transparent;border-color: #CCCCCC;border-radius: 0;-webkit-border-radius: 0;outline: none;margin-bottom: 5px;margin-left: 3px;font-size: 13px;padding: 7px 11px;" type="button" class="btn btn-success btn-flat">Voltar</button>';
    $botao_precadastro = utf8_encode('<button onclick="preCadastrar();" name="botao" value="precadastrar" style="color: #000000; box-shadow: none;background-color: #f2fcad;border-color: transparent;border-color: #CCCCCC;border-radius: 0;-webkit-border-radius: 0;outline: none;margin-bottom: 5px;margin-left: 3px;font-size: 13px;padding: 7px 11px;" type="button" class="btn btn-success btn-flat">Pré-Cadastrar</button>');
    $displaysocio = 'none';
    $consultaHTML = '<label style="width: 100%;" class="col-sm-3 control-label form-margin">Consulta</label>
		            <div class="col-sm-6">
		                <input type="file" class="form-control" name="fconsulta" value="">
		            </div>';
    
	#include das funcoes da tela
	include('functions/banco-cliente.php');

	#Instancia o objeto
	$banco = new bancocliente();
    
    #Editar
    if($this->PaginaAux[0] == 'editar'){
        $idcliente = $this->PaginaAux[1];
        $rsCliente = $banco->BuscaClientePorId($idcliente);
        
        $titulo = "Editar Cliente";
        
        $idtipocliente = $rsCliente['idtipocliente'];
        $idtipoprofissional = $rsCliente['idtipoprofissional'];
        
        $nome = $rsCliente['nome'];
        if($idtipocliente == 2){
            $cnpj_cpf = $rsCliente['cnpj'];
        }elseif($idtipocliente == 1){
            $cnpj_cpf = $rsCliente['cpf'];
        }
        $inscricao_estadual = $rsCliente['inscricao_estadual'];
        $rg_novo = $rsCliente['rg_novo'];
        $data_nascimento = $rsCliente['data_nascimento'];
        $cep = $rsCliente['cep'];
        $cidade = $rsCliente['cidade'];
        $estado = $rsCliente['estado'];
        $endereco = $rsCliente['endereco'];
        $numero = $rsCliente['numero'];
        $bairro = $rsCliente['bairro'];
        $complemento = $rsCliente['complemento'];
        $ponto_referencia = $rsCliente['ponto_referencia'];
        $telefone = $rsCliente['telefone'];
        $celular = $rsCliente['celular'];
        $email = $rsCliente['email'];
        $idtipoendereco = $rsCliente['idtipoenderecoprincipal'];
        $nome_socio = $rsCliente['nome_socio'];
        $cpf_socio = $rsCliente['cpf_socio'];
        
        if($cpf_socio != '' || $nome_socio != ''){
            $displaysocio = 'block';
        }
        
        #Adicionais
        $enderecosAdicionais = $banco->MontaEnderecosAdicionais($idcliente);
        $telefonesAdicionais = $banco->MontaTelefonesAdicionais($idcliente);
        $emailsAdicionais = $banco->MontaEmailsAdicionais($idcliente);
        
        $consultaHTML = $banco->MontaConsulta($idcliente);
    }elseif($this->PaginaAux[0] == 'ativar'){
        $idcliente = $this->PaginaAux[1];
        $banco->Ativar($idcliente);
        $banco->RedirecionaPara('lista-cliente');
    }elseif($this->PaginaAux[0] == 'inativar'){
        $idcliente = $this->PaginaAux[1];
        $banco->Inativar($idcliente);
        $banco->RedirecionaPara('lista-cliente');
    }elseif($this->PaginaAux[0] == 'excluir'){
        $idcliente = $this->PaginaAux[1];
        $banco->Excluir($idcliente);
        $banco->RedirecionaPara('lista-cliente');
    }
    
    if($this->PaginaAux[0] == 'historico'){
    	$idcliente = $this->PaginaAux[1];
    	$nome_cliente = $banco->BuscaNomeCliente($idcliente);
    	$data_rever = $banco->BuscaDataRever($idcliente);
    	$historico_cliente = $banco->MontaHistoricoCliente($idcliente);
    	$Conteudo = utf8_encode($banco->CarregaHtml('Clientes/historico-cliente'));
    	$Conteudo = str_replace("<%IDCLIENTE%>", $idcliente, $Conteudo);
    	$Conteudo = str_replace("<%CLIENTE%>", $nome_cliente, $Conteudo);
    	$Conteudo = str_replace("<%HISTORICO%>", $historico_cliente, $Conteudo);
    	if($data_rever == '0000-00-00'){
    		$data_rever = '';
    		$Conteudo = str_replace("<%REVER%>", $data_rever, $Conteudo);
    	}else{
    		$data_rever = date("d/m/Y", strtotime($data_rever));
    		$Conteudo = str_replace("<%REVER%>", " - Rever Dia: " . $data_rever, $Conteudo);
    	}
    }elseif($this->PaginaAux[0] == 'historico-vendas'){
    	$idcliente = $this->PaginaAux[1];
    	$nome_cliente = $banco->BuscaNomeCliente($idcliente);
    	$historico_vendas = $banco->MontaHistoricoVendasCliente($idcliente);
    	$Conteudo = utf8_encode($banco->CarregaHtml('Clientes/historico-vendas-cliente'));
    	$Conteudo = str_replace("<%CLIENTE%>", $nome_cliente, $Conteudo);
    	$Conteudo = str_replace("<%HISTORICOVENDAS%>", $historico_vendas, $Conteudo);
    }else{
	    #Trabalha com Post
		if(isset($_POST["acao"]) && $_POST["acao"] != '' ){
	        $idtipocliente = $_POST["tipocliente"];
	        $idtipoprofissional = $_POST["tipoprofissional"];
	        if($_POST['botao'] == 'precadastrar' && $idtipoprofissional == ''){
	        	$idtipoprofissional = 13;
	        }
	        $nome = ucwords(utf8_decode(strip_tags(trim(addslashes($_POST["nome"])))));
	        $cnpj_cpf = $_POST['cnpj_cpf'];
	        $inscricao_estadual = utf8_decode(strip_tags(trim(addslashes($_POST["inscricao_estadual"]))));
	        $rg_novo = utf8_decode(strip_tags(trim(addslashes($_POST["rg"]))));
	        $data_nascimento = utf8_decode(strip_tags(trim(addslashes($_POST["data_nascimento"]))));
	        $idtipoendereco = $_POST['tipoendereco_p'];
	        if($_POST['botao'] == 'precadastrar' && $idtipoendereco == ''){
	        	$idtipoendereco = 2;
	        }
	        $cep = strip_tags(trim(addslashes($_POST["cep_p"])));
	        $cidade = utf8_decode(strip_tags(trim(addslashes($_POST["cidade_p"]))));
	        $estado = utf8_decode(strip_tags(trim(addslashes($_POST["estado_p"]))));
	        $endereco = utf8_decode(strip_tags(trim(addslashes($_POST["endereco_p"]))));
	        $numero = strip_tags(trim(addslashes($_POST["numero_p"])));
	        $bairro = utf8_decode(strip_tags(trim(addslashes($_POST["bairro_p"]))));
	        $complemento = utf8_decode(strip_tags(trim(addslashes($_POST["complemento_p"]))));
	        $ponto_referencia = utf8_decode(strip_tags(trim(addslashes($_POST["ponto_referencia_p"]))));
	        $telefone = strip_tags(trim(addslashes($_POST["telefone_p"])));
	        $email = strip_tags(trim(addslashes($_POST["email_p"])));
	        $nome_socio = ucwords(utf8_decode(strip_tags(trim(addslashes($_POST["nome_socio"])))));
	        $cpf_socio = strip_tags(trim(addslashes($_POST["cpf_socio"])));
	        #Adicionais
	        $arrTelefones = $_POST["telAdicional"];
	        $arrTipoTelefones = $_POST["tipoTelefone"];
	        $arrTelContatos = $_POST['telContato'];
	        $arrEmails = $_POST["emailAdicional"];
	        $arrTipoEnd = $_POST['tipoendereco'];
	        $arrCeps = $_POST["cep"];
	        $arrEnderecos = $_POST["endereco"];
	        $arrNumeros = $_POST["numero"];
	        $arrBairros = $_POST["bairro"];
	        $arrCidades = $_POST["cidade"];
	        $arrEstados = $_POST["estado"];
	        $arrComps = $_POST["complemento"];
	        $arrRefs = $_POST["ponto_referencia"];
	        
	        if($idcliente){
	        	if($_POST['botao'] == 'precadastrar'){
	        		$ativo = 9;
	        	}else{
	        		$ativo = 1;
	        	}
	        	
	        	if($_FILES['fconsulta']['name'] != ''){
					$banco->AddConsulta($_FILES['fconsulta'], $idcliente);
	        	}
	            #Addr Adicional
	            $cepadd = $_POST['cepadd'];
	            if(!empty($cepadd)){
	                $tipoenderecoadd = $_POST['tipoenderecoadd'];
	                $enderecoadd = $_POST['enderecoadd'];
	                $numeroadd = $_POST['numeroadd'];
	                $bairroadd = $_POST['bairroadd'];
	                $cidadeadd = $_POST['cidadeadd'];
	                $estadoadd = $_POST['estadoadd'];
	                $complementoadd = $_POST['complementoadd'];
	                $ponto_referenciaadd = $_POST['ponto_referenciaadd'];
	                
	                #Atualiza Endereços adicionais
	                $banco->AtualizaEnderecosAdicionais($tipoenderecoadd, $cepadd, $enderecoadd, $numeroadd, $bairroadd, $cidadeadd, $estadoadd, $complementoadd, $ponto_referenciaadd);
	            }
	            #Telefone Adicional
	            $tipoTelefoneadd = $_POST['tipoTelefoneadd'];
	            $telContatoadd = $_POST['telContatoadd'];
	            if(!empty($tipoTelefoneadd)){
	                $telAdicionaladd = $_POST['telAdicionaladd'];
	                
	                #Atualiza Telefones Adicionais
	                $banco->AtualizaTelefonesAdicionais($tipoTelefoneadd, $telAdicionaladd, $telContatoadd);
	            }
	            #Email Adicional
	            $emailAdicionaladd = $_POST['emailAdicionaladd'];
	            if(!empty($emailAdicionaladd)){
	                #Atualiza emails adicionais
	                $banco->AtualizaEmailsAdicionais($emailAdicionaladd);
	            }
	            if($_FILES['fconsulta']['name'] !== null){
	            	#$banco->UploadEmpenho($idcliente, $_FILES['fconsulta']);
	            }
	            
	            #Update
	            $banco->AtualizaCliente($idcliente, $idtipocliente, $idtipoprofissional, $nome, $cnpj_cpf, $idtipoendereco, $cep, $cidade, $estado, $endereco, $numero, $bairro, $complemento, $ponto_referencia, $telefone, $celular, $email, $nome_socio, $cpf_socio, $arrTelefones, $arrTipoTelefones, $arrTelContatos, $arrEmails, $arrTipoEnd, $arrCeps, $arrEnderecos, $arrNumeros, $arrBairros, $arrCidades, $arrEstados, $arrComps, $arrRefs, $inscricao_estadual, $ativo, $rg_novo, $data_nascimento);
	        }else{
	        	if($_POST['botao'] == 'salvar'){
	            	#Verifica cliente cadastrado
		            $result = $banco->BuscaClienteExistente($idtipocliente, $cnpj_cpf);
		            $num_rows = $banco->Linha($result);
	        	}else{
	        		$result = $banco->BuscaClienteExistentePorNome($nome);
	        		$num_rows = $banco->Linha($result);
	        	}	        	
	            if($num_rows){
	                echo utf8_encode("<script type='text/javascript'>alert('Cliente já cadastrado!')</script>");
	            }else{
	            	if($_POST['botao'] == 'precadastrar'){
	            		$ativo = 9;
	            	}else{
	            		$ativo = 1;
	            	}
	                #Insert
	                $banco->InsereCliente($idtipocliente, $idtipoprofissional, $nome, $cnpj_cpf, $idtipoendereco, $cep, $cidade, $estado, $endereco, $numero, $bairro, $complemento, $ponto_referencia, $telefone, $celular, $email, $nome_socio, $cpf_socio, $arrTelefones, $arrTipoTelefones, $arrTelContatos, $arrEmails, $arrTipoEnd, $arrCeps, $arrEnderecos, $arrNumeros, $arrBairros, $arrCidades, $arrEstados, $arrComps, $arrRefs, $_FILES, $inscricao_estadual, $ativo, $rg_novo, $data_nascimento);
	            }
	        }
	    }#Fim POST
	    
	    #Monta Tipo Cliente
	    $select_tipo_cliente = $banco->SelectTipoCliente($idtipocliente);
	    
	    #Monta Tipo Profissional
	    $select_tipo_profissional = $banco->SelectTipoProfissional($idtipoprofissional);
	    
	    #Monta Tipo Endereço
	    $select_tipo_endereco = $banco->SelectTipoEndereco($idtipoendereco);
	    
	    #Imprime valores
		$Conteudo = utf8_encode($banco->CarregaHtml('Clientes/cliente'));
	    $Conteudo = str_replace("<%TITULO%>", $titulo, $Conteudo);
	    $Conteudo = str_replace("<%SELECTTIPOCLIENTE%>", $select_tipo_cliente, $Conteudo);
	    $Conteudo = str_replace("<%SELECTTIPOPROFISSIONAL%>", $select_tipo_profissional, $Conteudo);
	    $Conteudo = str_replace("<%SELECTTIPOENDERECO%>", $select_tipo_endereco, $Conteudo);
	    $Conteudo = str_replace("<%DISPLAYSOCIO%>", $displaysocio, $Conteudo);
	    $Conteudo = str_replace("<%NOMESOCIO%>", $nome_socio, $Conteudo);
	    $Conteudo = str_replace("<%CPFSOCIO%>", $cpf_socio, $Conteudo);
	    $Conteudo = str_replace("<%CNPJCPF%>", $cnpj_cpf, $Conteudo);
	    $Conteudo = str_replace("<%RG%>", $rg_novo, $Conteudo);
	    $Conteudo = str_replace("<%DATANASCIMENTO%>", $data_nascimento, $Conteudo);
	    $Conteudo = str_replace("<%INSCRICAOESTADUAL%>", $inscricao_estadual, $Conteudo);
	    $Conteudo = str_replace("<%NOME%>", utf8_encode($nome), $Conteudo);
	    $Conteudo = str_replace("<%CEP%>", $cep, $Conteudo);
	    $Conteudo = str_replace("<%ENDERECO%>", utf8_encode($endereco), $Conteudo);
	    $Conteudo = str_replace("<%NUMERO%>", $numero, $Conteudo);
	    $Conteudo = str_replace("<%CIDADE%>", utf8_encode($cidade), $Conteudo);
	    $Conteudo = str_replace("<%ESTADO%>", $estado, $Conteudo);
	    $Conteudo = str_replace("<%BAIRRO%>", utf8_encode($bairro), $Conteudo);
	    $Conteudo = str_replace("<%COMPLEMENTO%>", utf8_encode($complemento), $Conteudo);
	    $Conteudo = str_replace("<%PONTOREFERENCIA%>", utf8_encode($ponto_referencia), $Conteudo);
	    $Conteudo = str_replace("<%TELEFONE%>", $telefone, $Conteudo);
	    $Conteudo = str_replace("<%CELULAR%>", $celular, $Conteudo);
	    $Conteudo = str_replace("<%EMAIL%>", $email, $Conteudo);
	    #Adicionais
	    $Conteudo = str_replace("<%ENDERECOSADICIONAIS%>", $enderecosAdicionais, $Conteudo);
	    $Conteudo = str_replace("<%EMAILSADICIONAIS%>", $emailsAdicionais, $Conteudo);
	    $Conteudo = str_replace("<%TELEFONESADICIONAIS%>", $telefonesAdicionais, $Conteudo);
	    #Botões
	    $Conteudo = str_replace("<%BOTAOEXCLUIR%>", $botao_excluir, $Conteudo);
	    $Conteudo = str_replace("<%BOTAOATIVARINATIVAR%>", $botao_ativar_inativar, $Conteudo);
	    $Conteudo = str_replace("<%BOTAOVOLTAR%>", $botao_voltar, $Conteudo);
	    $Conteudo = str_replace("<%BOTAOPRECADASTRO%>", $botao_precadastro, $Conteudo);
	    
	    $Conteudo = str_replace("<%CONSULTA%>", $consultaHTML, $Conteudo);
    }
?>