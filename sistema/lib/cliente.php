<?php
    $titulo = "Novo Cliente";
    $botao_voltar = '<button onclick="voltar()" style="box-shadow: none;background-color: #000000;border-color: transparent;border-color: #CCCCCC;border-radius: 0;-webkit-border-radius: 0;outline: none;margin-bottom: 5px;margin-left: 3px;font-size: 13px;padding: 7px 11px;" type="button" class="btn btn-success btn-flat">Voltar</button>';
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
    
    #Trabalha com Post
	if(isset($_POST["acao"]) && $_POST["acao"] != '' ){
        $idtipocliente = $_POST["tipocliente"];
        $idtipoprofissional = $_POST["tipoprofissional"];
        $nome = ucwords(utf8_decode(strip_tags(trim(addslashes($_POST["nome"])))));
        $cnpj_cpf = $_POST['cnpj_cpf'];
        $idtipoendereco = $_POST['tipoendereco_p'];
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
            if(!empty($tipoTelefoneadd)){
                $telAdicionaladd = $_POST['telAdicionaladd'];
                
                #Atualiza Telefones Adicionais
                $banco->AtualizaTelefonesAdicionais($tipoTelefoneadd, $telAdicionaladd);
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
            $banco->AtualizaCliente($idcliente, $idtipocliente, $idtipoprofissional, $nome, $cnpj_cpf, $idtipoendereco, $cep, $cidade, $estado, $endereco, $numero, $bairro, $complemento, $ponto_referencia, $telefone, $celular, $email, $nome_socio, $cpf_socio, $arrTelefones, $arrTipoTelefones, $arrEmails, $arrTipoEnd, $arrCeps, $arrEnderecos, $arrNumeros, $arrBairros, $arrCidades, $arrEstados, $arrComps, $arrRefs);
        }else{
            #Verifica cliente cadastrado
            $result = $banco->BuscaClienteExistente($idtipocliente, $cnpj_cpf);
            $num_rows = $banco->Linha($result);
            if($num_rows){
                echo utf8_encode("<script type='text/javascript'>alert('Cliente já cadastrado!')</script>");
            }else{
                #Insert
                $banco->InsereCliente($idtipocliente, $idtipoprofissional, $nome, $cnpj_cpf, $idtipoendereco, $cep, $cidade, $estado, $endereco, $numero, $bairro, $complemento, $ponto_referencia, $telefone, $celular, $email, $nome_socio, $cpf_socio, $arrTelefones, $arrTipoTelefones, $arrEmails, $arrTipoEnd, $arrCeps, $arrEnderecos, $arrNumeros, $arrBairros, $arrCidades, $arrEstados, $arrComps, $arrRefs, $_FILES);
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
    
    $Conteudo = str_replace("<%CONSULTA%>", $consultaHTML, $Conteudo);
?>