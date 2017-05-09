<?php
    class bancocliente extends banco{
    	
    	function MontaClientesCarteira(){
    		$Auxilio = parent::CarregaHtml('Clientes/itens/lista-cliente-itens');
    		$Sql = "SELECT *, X.idusuario AS carteira FROM t_clientes C 
    				INNER JOIN t_usuarios_carteira_clientes X ON X.idcliente = C.idcliente 
    				WHERE X.idusuario = " . $_SESSION['idusuario'] 
    				. " ORDER BY C.nome";
    		$result = parent::Execute($Sql);
    		$linha = parent::Linha($result);
    		if($linha){
    			while($rs = parent::ArrayData($result)){
    				$Linha = $Auxilio;
    				$Linha = str_replace('<%ID%>', $rs['idcliente'], $Linha);
    				$Linha = str_replace('<%NOME%>', $rs['nome'], $Linha);
    				$Linha = str_replace('<%TIPOPROFISSIONAL%>', $rs['tipoprofissional'], $Linha);
    				$addr = $rs['endereco'] . ", Nº " . $rs['numero'] . " - " . $rs['bairro'] . " - " . $rs['cidade'] . "/" . $rs['estado'];
    				$Linha = str_replace('<%ENDERECO%>', $addr, $Linha);
    				if($rs['carteira'] == $_SESSION['idusuario']){
    					#Remover da carteira
    					$link_carteira = '<a href="javascript:void(0)" onclick="removerCarteira('.$rs['idcliente'].')">Remover da Carteira</a>';
    				}else{
    					#Link adicionar
    					$link_carteira = '<a href="javascript:void(0)" onclick="adicionarCarteira('.$rs['idcliente'].')">Adicionar à Carteira</a>';
    				}
    				$Linha = str_replace('<%LINKCARTEIRA%>', $link_carteira, $Linha);
    				$Linha = str_replace('<%CARTEIRA%>', parent::BuscaUsuarioPorId($rs['carteira']), $Linha);
    				$Linha = str_replace('<%TELEFONE%>', $rs['telefone'], $Linha);
    				if($rs['idtipocliente'] == 1){
    					$cnpjcpf = 'CPF: ' . $rs['cpf'];
    				}elseif($rs['idtipocliente'] == 2){
    					$cnpjcpf = 'CNPJ: ' . $rs['cnpj'];
    				}
    				if($rs['ativo'] == 1){
    					$Linha = str_replace("<%ATIVOINATIVO%>", 'Ativo', $Linha);
    					$Linha = str_replace("<%BOTAOAI%>", '<a href="javascript:void(0)" onclick="inativar('.$rs['idcliente'].', \''.$rs['nome'].'\')">Inativar</a>', $Linha);
    				}elseif($rs['ativo'] == 9){
    					$Linha = str_replace("<%ATIVOINATIVO%>", 'Pré-Cadastro', $Linha);
    					$Linha = str_replace("<%BOTAOAI%>", '<a href="javascript:void(0)" onclick="inativar('.$rs['idcliente'].', \''.$rs['nome'].'\')">Inativar</a>', $Linha);
    				}else{
    					$Linha = str_replace("<%ATIVOINATIVO%>", 'Inativo', $Linha);
    					$Linha = str_replace("<%BOTAOAI%>", '<a href="javascript:void(0)" onclick="ativar('.$rs['idcliente'].', \''.$rs['nome'].'\')">Ativar</a>', $Linha);
    				}
    				$Linha = str_replace('<%CNPJCPF%>', $cnpjcpf, $Linha);
    				#Verifica consulta, se tiver
    				$SqlConsulta = "SELECT * FROM t_clientes_consulta WHERE idcliente = " . $rs['idcliente'];
    				$resultConsulta = parent::Execute($SqlConsulta);
    				$linhaConsulta = parent::Linha($resultConsulta);
    				if($linhaConsulta){
    					$rsConsulta = parent::ArrayData($resultConsulta);
    					$consultaHTML = '<a target="_blank" href="'.UrlPadrao.$rsConsulta['caminho'].'">Consulta</a>';
    				}else{
    					$consultaHTML = "";
    				}
    				$Linha = str_replace('<%CONSULTA%>', $consultaHTML, $Linha);
    				#Verifica última interação
    				$SqlInteracao = "SELECT data, usuario FROM t_clientes_historico WHERE idcliente = " . $rs['idcliente'] . " ORDER BY data DESC LIMIT 0, 1";
    				$resultInteracao = parent::Execute($SqlInteracao);
    				$linhaInteracao = parent::Linha($resultInteracao);
    				if($linhaInteracao){
    					$rsInteracao = parent::ArrayData($resultInteracao);
    					$dataInteracao = date("d/m/Y - H:i", strtotime($rsInteracao['data']));
    					$interacaoHTML = "$dataInteracao <br/> {$rsInteracao['usuario']}";
    				}else{
    					$interacaoHTML = "";
    				}
    				$Linha = str_replace('<%ULTIMAINTERACAO%>', $interacaoHTML, $Linha);
    				$Clientes .= $Linha;
    			}
    		}else{
    			$Clientes = '<tr class="odd gradeX">
                                <td colspan="9">Não foram encontrados clientes cadastrados.</td>
                             <tr>';
    		}
    		
    		return utf8_encode($Clientes);
    	}
    	
    	function MontaInteracoesUsuario($idusuario, $buscaDataIni, $buscaDataFim){
    		if($buscaDataIni == ''){
    			$buscaDataIni = '0000-00-00';
    		}
    		if($buscaDataFim == ''){
    			$buscaDataFim = '0000-00-00';
    		}
    		$Auxilio = parent::CarregaHtml('Clientes/itens/lista-cliente-itens');
    		$SqlHistorico = "SELECT idcliente FROM t_clientes_historico WHERE usuario = '".parent::BuscaUsuarioPorId($idusuario)."' AND data >= '$buscaDataIni 00:00:00' AND data <= '$buscaDataFim 23:59:59' GROUP BY idcliente ORDER BY data DESC";
    		$resultHistorico = parent::Execute($SqlHistorico);
    		$linhaHistorico = parent::Linha($resultHistorico);
    		if($linhaHistorico){
    			while($rsHistorico = parent::ArrayData($resultHistorico)){
    				$Linha = $Auxilio;
    				$Sql = "SELECT C.*, P.tipo AS tipoprofissional, X.idusuario AS carteira FROM t_clientes C
		                    INNER JOIN fixo_tipo_profissional P ON C.idtipoprofissional = P.idtipoprofissional 
    						LEFT JOIN t_usuarios_carteira_clientes X ON X.idcliente = C.idcliente 
		                    WHERE 1 AND C.idcliente = " . $rsHistorico['idcliente'];
    				$result = parent::Execute($Sql);
    				$rs = parent::ArrayData($result);
    		
    				$Linha = str_replace('<%ID%>', $rs['idcliente'], $Linha);
    				$Linha = str_replace('<%NOME%>', $rs['nome'], $Linha);
    				$Linha = str_replace('<%TIPOPROFISSIONAL%>', $rs['tipoprofissional'], $Linha);
    				$addr = $rs['endereco'] . ", Nº " . $rs['numero'] . " - " . $rs['bairro'] . " - " . $rs['cidade'] . "/" . $rs['estado'];
    				$Linha = str_replace('<%ENDERECO%>', $addr, $Linha);
    				$Linha = str_replace('<%TELEFONE%>', $rs['telefone'], $Linha);
    				$Linha = str_replace('<%CARTEIRA%>', parent::BuscaUsuarioPorId($rs['carteira']), $Linha);
    				if($rs['carteira'] == $_SESSION['idusuario']){
    					#Remover da carteira
    					$link_carteira = '<a href="javascript:void(0)" onclick="removerCarteira('.$rs['idcliente'].')">Remover da Carteira</a>';
    				}else{
    					#Link adicionar
    					$link_carteira = '<a href="javascript:void(0)" onclick="adicionarCarteira('.$rs['idcliente'].')">Adicionar à Carteira</a>';
    				}
    				$Linha = str_replace('<%LINKCARTEIRA%>', $link_carteira, $Linha);
    				if($rs['idtipocliente'] == 1){
    					$cnpjcpf = 'CPF: ' . $rs['cpf'];
    				}elseif($rs['idtipocliente'] == 2){
    					$cnpjcpf = 'CNPJ: ' . $rs['cnpj'];
    				}
    				if($rs['ativo'] == 1){
    					$Linha = str_replace("<%ATIVOINATIVO%>", 'Ativo', $Linha);
    					$Linha = str_replace("<%BOTAOAI%>", '<a href="javascript:void(0)" onclick="inativar('.$rs['idcliente'].', \''.$rs['nome'].'\')">Inativar</a>', $Linha);
    				}elseif($rs['ativo'] == 9){
                    	$Linha = str_replace("<%ATIVOINATIVO%>", 'Pré-Cadastro', $Linha);
                    	$Linha = str_replace("<%BOTAOAI%>", '<a href="javascript:void(0)" onclick="inativar('.$rs['idcliente'].', \''.$rs['nome'].'\')">Inativar</a>', $Linha);
                    }else{
    					$Linha = str_replace("<%ATIVOINATIVO%>", 'Inativo', $Linha);
    					$Linha = str_replace("<%BOTAOAI%>", '<a href="javascript:void(0)" onclick="ativar('.$rs['idcliente'].', \''.$rs['nome'].'\')">Ativar</a>', $Linha);
    				}
    				$Linha = str_replace('<%CNPJCPF%>', $cnpjcpf, $Linha);
    				#Verifica consulta, se tiver
    				$SqlConsulta = "SELECT * FROM t_clientes_consulta WHERE idcliente = " . $rs['idcliente'];
    				$resultConsulta = parent::Execute($SqlConsulta);
    				$linhaConsulta = parent::Linha($resultConsulta);
    				if($linhaConsulta){
    					$rsConsulta = parent::ArrayData($resultConsulta);
    					$consultaHTML = '<a target="_blank" href="'.UrlPadrao.$rsConsulta['caminho'].'">Consulta</a>';
    				}else{
    					$consultaHTML = "";
    				}
    				$Linha = str_replace('<%CONSULTA%>', $consultaHTML, $Linha);
    				#Verifica última interação
    				$SqlInteracao = "SELECT data, usuario FROM t_clientes_historico WHERE idcliente = " . $rs['idcliente'] . " ORDER BY data DESC LIMIT 0, 1";
    				$resultInteracao = parent::Execute($SqlInteracao);
    				$linhaInteracao = parent::Linha($resultInteracao);
    				if($linhaInteracao){
    					$rsInteracao = parent::ArrayData($resultInteracao);
    					$dataInteracao = date("d/m/Y - H:i", strtotime($rsInteracao['data']));
    					$interacaoHTML = "$dataInteracao <br/> {$rsInteracao['usuario']}";
    				}else{
    					$interacaoHTML = "";
    				}
    				$Linha = str_replace('<%ULTIMAINTERACAO%>', $interacaoHTML, $Linha);
    				$Clientes .= $Linha;
    			}
    		}else{
    			$Clientes = '<tr class="odd gradeX">
                                <td colspan="9">Não foram encontrados clientes cadastrados.</td>
                             <tr>';
    		}
    		
    		return utf8_encode($Clientes);
    	}
    	
    	function MontaSelectInteracoes($idusuario){
    		$Sql = "SELECT * FROM t_usuarios WHERE 1 AND login <> 'admin' ORDER BY ativo DESC, nome_exibicao ASC";
    		$result = parent::Execute($Sql);
    		$select = '<select id="interacoes_funcionario" class="form-control"><option value="">Selecione um funcionário</option>';
    		while($rs = parent::ArrayData($result)){
    			if($rs['ativo'] == 0){
    				$inativo = " (Inativo)";
    			}else{
    				$inativo = '';
    			}
    			if($rs['idusuario'] == $idusuario){
    				$select .= "<option selected value='{$rs['idusuario']}'>{$rs['nome_exibicao']} $inativo</option>";
    			}else{
    				$select .= "<option value='{$rs['idusuario']}'>{$rs['nome_exibicao']} $inativo</option>";
    			}
    		}
    		$select .= '</select>';
    		return utf8_encode($select);
    	}
    	
    	function MontaMinhasInteracoes($buscaDataIni, $buscaDataFim){
    		if($buscaDataIni == ''){
    			$buscaDataIni = '0000-00-00';
    		}
    		if($buscaDataFim == ''){
    			$buscaDataFim = '0000-00-00';
    		}
    		$Auxilio = parent::CarregaHtml('Clientes/itens/lista-cliente-itens');
    		$SqlHistorico = "SELECT idcliente FROM t_clientes_historico WHERE usuario = '".$_SESSION['nomeexibicao']."' AND data >= '$buscaDataIni 00:00:00' AND data <= '$buscaDataFim 23:59:59' GROUP BY idcliente ORDER BY data DESC";
    		
    		$resultHistorico = parent::Execute($SqlHistorico);
    		$linhaHistorico = parent::Linha($resultHistorico);
    		if($linhaHistorico){
    			while($rsHistorico = parent::ArrayData($resultHistorico)){
	    			$Linha = $Auxilio;
	    			$Sql = "SELECT C.*, P.tipo AS tipoprofissional, X.idusuario AS carteira FROM t_clientes C
		                    INNER JOIN fixo_tipo_profissional P ON C.idtipoprofissional = P.idtipoprofissional 
	    					LEFT JOIN t_usuarios_carteira_clientes X ON X.idcliente = C.idcliente 
		                    WHERE 1 AND C.idcliente = " . $rsHistorico['idcliente'];
	    			$result = parent::Execute($Sql);
	    			$rs = parent::ArrayData($result);
	    			
	    			$Linha = str_replace('<%ID%>', $rs['idcliente'], $Linha);
	    			$Linha = str_replace('<%NOME%>', $rs['nome'], $Linha);
	    			$Linha = str_replace('<%TIPOPROFISSIONAL%>', $rs['tipoprofissional'], $Linha);
	    			$addr = $rs['endereco'] . ", Nº " . $rs['numero'] . " - " . $rs['bairro'] . " - " . $rs['cidade'] . "/" . $rs['estado'];
	    			$Linha = str_replace('<%ENDERECO%>', $addr, $Linha);
	    			$Linha = str_replace('<%TELEFONE%>', $rs['telefone'], $Linha);
	    			$Linha = str_replace('<%CARTEIRA%>', parent::BuscaUsuarioPorId($rs['carteira']), $Linha);
	    			if($rs['carteira'] == $_SESSION['idusuario']){
	    				#Remover da carteira
	    				$link_carteira = '<a href="javascript:void(0)" onclick="removerCarteira('.$rs['idcliente'].')">Remover da Carteira</a>';
	    			}else{
	    				#Link adicionar
	    				$link_carteira = '<a href="javascript:void(0)" onclick="adicionarCarteira('.$rs['idcliente'].')">Adicionar à Carteira</a>';
	    			}
	    			$Linha = str_replace('<%LINKCARTEIRA%>', $link_carteira, $Linha);
	    			if($rs['idtipocliente'] == 1){
	    				$cnpjcpf = 'CPF: ' . $rs['cpf'];
	    			}elseif($rs['idtipocliente'] == 2){
	    				$cnpjcpf = 'CNPJ: ' . $rs['cnpj'];
	    			}
	    			if($rs['ativo'] == 1){
	    				$Linha = str_replace("<%ATIVOINATIVO%>", 'Ativo', $Linha);
	    				$Linha = str_replace("<%BOTAOAI%>", '<a href="javascript:void(0)" onclick="inativar('.$rs['idcliente'].', \''.$rs['nome'].'\')">Inativar</a>', $Linha);
	    			}elseif($rs['ativo'] == 9){
                    	$Linha = str_replace("<%ATIVOINATIVO%>", 'Pré-Cadastro', $Linha);
                    	$Linha = str_replace("<%BOTAOAI%>", '<a href="javascript:void(0)" onclick="inativar('.$rs['idcliente'].', \''.$rs['nome'].'\')">Inativar</a>', $Linha);
                    }else{
	    				$Linha = str_replace("<%ATIVOINATIVO%>", 'Inativo', $Linha);
	    				$Linha = str_replace("<%BOTAOAI%>", '<a href="javascript:void(0)" onclick="ativar('.$rs['idcliente'].', \''.$rs['nome'].'\')">Ativar</a>', $Linha);
	    			}
	    			$Linha = str_replace('<%CNPJCPF%>', $cnpjcpf, $Linha);
	    			#Verifica consulta, se tiver
	    			$SqlConsulta = "SELECT * FROM t_clientes_consulta WHERE idcliente = " . $rs['idcliente'];
	    			$resultConsulta = parent::Execute($SqlConsulta);
	    			$linhaConsulta = parent::Linha($resultConsulta);
	    			if($linhaConsulta){
	    				$rsConsulta = parent::ArrayData($resultConsulta);
	    				$consultaHTML = '<a target="_blank" href="'.UrlPadrao.$rsConsulta['caminho'].'">Consulta</a>';
	    			}else{
	    				$consultaHTML = "";
	    			}
	    			$Linha = str_replace('<%CONSULTA%>', $consultaHTML, $Linha);
	    			#Verifica última interação
	    			$SqlInteracao = "SELECT data, usuario FROM t_clientes_historico WHERE idcliente = " . $rs['idcliente'] . " ORDER BY data DESC LIMIT 0, 1";
	    			$resultInteracao = parent::Execute($SqlInteracao);
	    			$linhaInteracao = parent::Linha($resultInteracao);
	    			if($linhaInteracao){
	    				$rsInteracao = parent::ArrayData($resultInteracao);
	    				$dataInteracao = date("d/m/Y - H:i", strtotime($rsInteracao['data']));
	    				$interacaoHTML = "$dataInteracao <br/> {$rsInteracao['usuario']}";
	    			}else{
	    				$interacaoHTML = "";
	    			}
	    			$Linha = str_replace('<%ULTIMAINTERACAO%>', $interacaoHTML, $Linha);
	    			$Clientes .= $Linha;
    			}
    		}else{
    			$Clientes = '<tr class="odd gradeX">
                                <td colspan="9">Não foram encontrados clientes cadastrados.</td>
                             <tr>';
    		}
    		            
            return utf8_encode($Clientes);
    	}
    	
    	function MontaHistoricoCliente($idcliente){
    		$Sql = "SELECT * FROM t_clientes_historico WHERE idcliente = $idcliente ORDER BY data DESC";
    		$result = parent::Execute($Sql);
    		$linha = parent::Linha($result);
    		$Auxilio = parent::CarregaHtml('Clientes/itens/historico-itens');
    		if($linha){
	    		while($rs = parent::ArrayData($result)){
	    			$Linha = $Auxilio;
	    			if($rs['usuario'] == $_SESSION['nomeexibicao'] || $_SESSION['idsetor'] == 1){
	    				$opcoes = '<ul role="menu" class="dropdown-menu">
					                <li>
					                    <a href="javascript:void(0)" onclick="editarHistorico('.$rs['idhistoricocliente'].')">Editar</a>
					                </li>
					                <li class="divider"></li>
					                <li>
					                    <a href="javascript:void(0)" onclick="excluirHistorico('.$rs['idhistoricocliente'].')">Excluir</a>
					                </li>
					            </ul>';
	    			}else{
	    				$opcoes = '';
	    			}
	    			$Linha = str_replace('<%DATA%>', date("d/m/Y H:i", strtotime($rs['data'])), $Linha);
	    			$Linha = str_replace('<%FUNCIONARIO%>', $rs['usuario'], $Linha);
	    			$Linha = str_replace('<%HISTORICO%>', nl2br($rs['historico']), $Linha);
	    			$Linha = str_replace('<%OPCOES%>', $opcoes, $Linha);
	    			$historico .= $Linha;
	    		}
    		}else{
    			$historico = '<tr class="odd gradeX">
                                <td colspan="4">Não foram encontrados registros de histórico para esse cliente.</td>
                             <tr>';
    		}
    		return utf8_encode($historico);
    	}
    	
    	function BuscaNomeCliente($idcliente){
    		$Sql = "SELECT nome FROM t_clientes WHERE idcliente = $idcliente";
    		$result = parent::Execute($Sql);
    		$rs = parent::ArrayData($result);
    		return utf8_encode($rs['nome']);
    	}
    	
    	function MontaConsulta($idcliente){
    		$Sql = "SELECT * FROM t_clientes_consulta WHERE idcliente = $idcliente";
    		$result = parent::Execute($Sql);
    		$linha = parent::Linha($result);
    		if($linha){
    			$rs = parent::ArrayData($result);
    			#Botao visualizar
    			$retorno = '<div id="visualizarConsulta">
    							<label style="width: 100%;" class="col-sm-3 control-label form-margin">Consulta</label>
    							<a class="btn btn-primary" target="_blank" href="'.UrlPadrao.$rs['caminho'].'">Visualizar Consulta</a> <a href="javascript:void(0)" onclick="removeConsulta('.$idcliente.')" class="btn btn-danger">-</a><br/>
    							Relizada por: '.parent::BuscaUsuarioPorId($rs['idusuario']).'<br/>
    							Data: '.date("d/m/Y H:i:s", strtotime($rs['data'])).'
    						</div>';
    		}else{
    			$retorno = '<label style="width: 100%;" class="col-sm-3 control-label form-margin">Consulta</label>
				            <div class="col-sm-6">
				                <input type="file" class="form-control" name="fconsulta" value="">
				            </div>';
    		}
    		return $retorno;
    	}
        
    	function AddConsulta($file, $idcliente){
    		
    		$Sql = "SELECT * FROM t_clientes_consulta WHERE idcliente = $idcliente";
    		$result = parent::Execute($Sql);
    		$linha = parent::Linha($result);
    		if($linha){
    			#Update
    			$caminhoCriar = $_SERVER['DOCUMENT_ROOT'] . AuxCaminhoConsultaCliente . $idcliente;
    			$caminho = "arq/clientes/$idcliente";
    			
    			preg_match("/\.(gif|png|jpg|jpeg|doc|docx|pdf){1}$/i", $file["name"], $ext);
    			$caminhoMover = "/Consulta - $idcliente" . "." . $ext[1];
    			move_uploaded_file($file["tmp_name"], $caminhoCriar.$caminhoMover);
    			$Sql = "UPDATE t_clientes_consulta SET idcliente = '$idcliente', caminho = '".$caminho.$caminhoMover."', idusuario = '".$_SESSION['idusuario']."', data = '".date("Y-m-d H:i:s")."' WHERE idcliente = $idcliente";
    			parent::Execute($Sql);
    		}else{
    			#Novo
    			$caminhoCriar = $_SERVER['DOCUMENT_ROOT'] . AuxCaminhoConsultaCliente . $idcliente;
    			$caminho = "arq/clientes/$idcliente";
    			
    			mkdir($caminhoCriar, 0755);
    			
    			preg_match("/\.(gif|png|jpg|jpeg|doc|docx|pdf){1}$/i", $file["name"], $ext);
    			$caminhoMover = "/Consulta - $idcliente" . "." . $ext[1];
    			move_uploaded_file($file["tmp_name"], $caminhoCriar.$caminhoMover);
    			$Sql = "INSERT INTO t_clientes_consulta (idcliente, caminho, idusuario, data) VALUES ('$idcliente', '".$caminho.$caminhoMover."', '".$_SESSION['idusuario']."', '".date("Y-m-d H:i:s")."')";
    			parent::Execute($Sql);
    		}
    	}
    	
        #Insere Cliente
        function InsereCliente($idtipocliente, $idtipoprofissional, $nome, $cnpj_cpf, $idtipoendereco, $cep, $cidade, $estado, $endereco, $numero, $bairro, $complemento, $ponto_referencia, $telefone, $celular, $email, $nome_socio, $cpf_socio, $arrTelefones, $arrTipoTelefones, $arrTelContatos, $arrEmails, $arrTipoEnd, $arrCeps, $arrEnderecos, $arrNumeros, $arrBairros, $arrCidades, $arrEstados, $arrComps, $arrRefs, $file, $inscricao_estadual, $ativo){
            if($idtipocliente == 1){
                $auxcnpjcpf = 'cpf';
            }elseif($idtipocliente == 2){
                $auxcnpjcpf = 'cnpj';
            }
            $Sql = "INSERT INTO t_clientes (nome, idtipocliente, endereco, numero, bairro, idtipoenderecoprincipal, cep, cidade, estado, complemento, ponto_referencia, telefone, celular, $auxcnpjcpf, idtipoprofissional, email, data_cadastro, nome_socio, cpf_socio, inscricao_estadual, ativo) 
                    VALUES ('".ucwords($nome)."', '$idtipocliente', '".ucwords($endereco)."', '$numero', '".ucwords($bairro)."', '$idtipoendereco', '$cep', '".ucwords($cidade)."', '$estado', '".ucfirst($complemento)."', '".ucfirst($ponto_referencia)."', '$telefone', '$celular', '$cnpj_cpf', '$idtipoprofissional', '$email', '".date("Y-m-d H:i:s")."', '".ucwords($nome_socio)."', '$cpf_socio', '$inscricao_estadual', '$ativo')";
            
            if(parent::Execute($Sql)){
                $lastid = mysql_insert_id();
            }else{
                parent::ChamaManutencao();
            }
            
            #Verifica adicionais
            if($arrTipoTelefones[0] != ''){
                $this->InsereTelefonesAdicionais($lastid, $arrTipoTelefones, $arrTelefones, $arrTelContatos);
            }
            
            if($arrEmails[0] != ''){
                $this->InsereEmailsAdicionais($lastid, $arrEmails);
            }
            
            if($arrCeps[0] != ''){
                $this->InsereEnderecosAdicionais($lastid, $arrTipoEnd, $arrCeps, $arrEnderecos, $arrNumeros, $arrBairros, $arrCidades, $arrEstados, $arrComps, $arrRefs);
            }
            
            #Arquivo de consulta
            if($file['fconsulta']['name'] != ''){
            	$this->AddConsulta($file['fconsulta'], $lastid);
            }
            
            parent::RedirecionaPara('lista-cliente');
        }
        
        #Telefones adicionais
        function InsereTelefonesAdicionais($idcliente, $arrTipoTelefones, $arrTelefones, $arrTelContatos){
            foreach($arrTipoTelefones as $key => $value){
                if($arrTelefones[$key] != ''){
                    $Sql = "INSERT INTO t_clientes_telefonesadicionais (tipotelefone, telefone, idcliente, contato) VALUES ('".utf8_decode($value)."', '{$arrTelefones[$key]}', '$idcliente', '".utf8_decode($arrTelContatos[$key])."')";
                    parent::Execute($Sql);
                }
            }
        }
        
        #Emails adicionais
        function InsereEmailsAdicionais($idcliente, $arrEmails){
            foreach($arrEmails as $email){
                if($email != ''){
                    $Sql = "INSERT INTO t_clientes_emailsadicionais (email, idcliente) VALUES ('".utf8_decode($email)."', '$idcliente')";
                    parent::Execute($Sql);
                }
            }
        }
        
        #Endereços adicionais
        function InsereEnderecosAdicionais($idcliente, $arrTipoEnd, $arrCeps, $arrEnderecos, $arrNumeros, $arrBairros, $arrCidades, $arrEstados, $arrComps, $arrRefs){
            foreach($arrCeps as $key => $value){
                if($value != ''){
                    $Sql = "INSERT INTO t_clientes_enderecosadicionais (idtipoendereco, cep, endereco, numero, bairro, cidade, estado, ponto_referencia, complemento, idcliente) VALUES ('".$arrTipoEnd[$key]."', '$value', '".ucwords(utf8_decode($arrEnderecos[$key]))."', '{$arrNumeros[$key]}', '".ucwords(utf8_decode($arrBairros[$key]))."', '".ucwords(utf8_decode($arrCidades[$key]))."', '".utf8_decode($arrEstados[$key])."', '".ucfirst(utf8_decode($arrRefs[$key]))."', '".ucfirst(utf8_decode($arrComps[$key]))."', '$idcliente')";
                    parent::Execute($Sql);
                }
            }
        }
        
        #EDIT Endereços Adicionais
        function MontaEnderecosAdicionais($idcliente){
            $Sql = "SELECT * FROM t_clientes_enderecosadicionais WHERE idcliente = $idcliente";
            $result = parent::Execute($Sql);
            while($rs = parent::ArrayData($result)){
                $tipoenderecoadd = $this->SelectTipoEndAdd($rs['idtipoendereco'], $rs['idenderecoadicional']);
                $retorno .= '<div id="divEndAdd'.$rs['idenderecoadicional'].'" class="col-sm-12" style="margin-top: 5px; background-color: aliceblue; border-radius: 4px">
                                <div class="form-group" style="width: 50%; float: left">
                                    <label class="col-sm-6 control-label">*Tipo Endereço</label>
                                    <div class="col-sm-6">
                                        '.$tipoenderecoadd.'
                                    </div>
                                </div>
                                <div class="form-group" style="width: 50%; float: left">
                                    <label class="col-sm-3 control-label form-margin">CEP</label>
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control cep" name="cepadd['.$rs['idenderecoadicional'].']" onblur="buscaCep(this.value, \'add'.$rs['idenderecoadicional'].'\')" value="'.$rs['cep'].'" placeholder="Digite o CEP para autocompletar os outros campos" maxlength="9" autocomplete="off">
                                    </div>
                                </div>
                                <div class="form-group" style="width: 50%; float: left">
                                    <label class="col-sm-3 control-label form-margin">*Endereço</label>
                                    <div class="col-sm-6">
                                        <input required="" type="text" id="endereco_add'.$rs['idenderecoadicional'].'" class="form-control" name="enderecoadd['.$rs['idenderecoadicional'].']" value="'.$rs['endereco'].'">
                                    </div>
                                </div>
                                <div class="form-group" style="width: 50%; float: left">
                                    <label class="col-sm-3 control-label form-margin">*Número</label>
                                    <div class="col-sm-6">
                                        <input required="" type="text" id="numero" class="form-control" name="numeroadd['.$rs['idenderecoadicional'].']" value="'.$rs['numero'].'">
                                    </div>
                                </div>
                                <div class="form-group" style="width: 50%; float: left">
                                    <label class="col-sm-3 control-label form-margin">*Bairro</label>
                                    <div class="col-sm-6">
                                        <input required="" type="text" id="bairro_add'.$rs['idenderecoadicional'].'" class="form-control" name="bairroadd['.$rs['idenderecoadicional'].']" value="'.$rs['bairro'].'">
                                    </div>
                                </div>
                                <div class="form-group" style="width: 25%; float: left">
                                    <label class="col-sm-3 control-label form-margin">*Cidade</label>
                                    <div class="col-sm-6">
                                        <input required="" type="text" id="cidade_add'.$rs['idenderecoadicional'].'" class="form-control" name="cidadeadd['.$rs['idenderecoadicional'].']" value="'.$rs['cidade'].'">
                                    </div>
                                </div>
                                <div class="form-group" style="width: 25%; float: left">
                                    <label class="col-sm-3 control-label form-margin">*Estado</label>
                                    <div class="col-sm-6">
                                        <input required="" maxlength="2" onblur="maiusc(this);" type="text" id="estado_add'.$rs['idenderecoadicional'].'" class="form-control" name="estadoadd['.$rs['idenderecoadicional'].']" value="'.$rs['estado'].'">
                                    </div>
                                </div>
                                <div class="form-group" style="width: 50%; float: left">
                                    <label class="col-sm-3 control-label form-margin">Complemento</label>
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control" name="complementoadd['.$rs['idenderecoadicional'].']" value="'.$rs['complemento'].'">
                                    </div>
                                </div>
                                <div class="form-group" style="width: 50%; float: left">
                                    <label style="width: 100%;" class="col-sm-3 control-label form-margin">Ponto de Referência</label>
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control" name="ponto_referenciaadd['.$rs['idenderecoadicional'].']" value="'.$rs['ponto_referencia'].'">
                                    </div>
                                </div>
                                <div class="form-group" style="width: 100%; float: right">
                                    <button onclick="removerEnderecoAdicional('.$rs['idenderecoadicional'].')" type="button" class="btn btn-danger" style="float: right;">Remover</button>
                                </div>
                            </div>';
            }
            return utf8_encode($retorno);
        }
        
        #Monta Tipo Endereço Adicional
        function SelectTipoEndAdd($idtipoendereco, $idenderecoadicional){
            $Sql = "SELECT * FROM fixo_tipo_endereco ORDER BY tipo";
			$select_tend = "<select required class='form-control' name='tipoenderecoadd[$idenderecoadicional]'>";
			$select_tend .= "<option selected value=''>Tipo do Endereço</option>";
			$result = parent::Execute($Sql);
			if($result){
				while($rs = parent::ArrayData($result)){
					if($rs['idtipoendereco'] == $idtipoendereco){
						$select_tend .= "<option selected value='".$rs['idtipoendereco']."'>".$rs['tipo']."</option>";
					}else{
						$select_tend .= "<option value='".$rs['idtipoendereco']."'>".$rs['tipo']."</option>";
					}
				}
				$select_tend .= "</select>";
				return $select_tend;
			}else{
				return false;
			}
        }
        
        #Atualiza Endereços Adicionais
        function AtualizaEnderecosAdicionais($tipoenderecoadd, $cepadd, $enderecoadd, $numeroadd, $bairroadd, $cidadeadd, $estadoadd, $complementoadd, $ponto_referenciaadd){
            foreach($cepadd as $key => $value){
                $Sql = "UPDATE t_clientes_enderecosadicionais SET idtipoendereco = '".$tipoenderecoadd[$key]."', endereco = '".ucwords(utf8_decode($enderecoadd[$key]))."', numero = '{$numeroadd[$key]}', bairro = '".ucwords(utf8_decode($bairroadd[$key]))."', cidade = '".ucwords(utf8_decode($cidadeadd[$key]))."', estado = '".utf8_decode($estadoadd[$key])."', cep = '".utf8_decode($value)."', ponto_referencia = '".ucfirst(utf8_decode($ponto_referenciaadd[$key]))."', complemento = '".ucfirst(utf8_decode($complementoadd[$key]))."' WHERE idenderecoadicional = $key";
                parent::Execute($Sql);
            }
        }
        
        #EDIT Telefones Adicionais
        function MontaTelefonesAdicionais($idcliente){
            $Sql = "SELECT * FROM t_clientes_telefonesadicionais WHERE idcliente = $idcliente";
            $result = parent::Execute($Sql);
            while($rs = parent::ArrayData($result)){
                switch($rs['tipotelefone']){
                    case 'Residencial':
                        $selRes = "selected";
                        break;
                    case 'Comercial':
                        $selCom = "selected";
                        break;
                    case 'Celular';
                        $selCel = "selected";
                        break;
                }
                $retorno .= '<div id="divTelAdd'.$rs['idtelefoneadicional'].'" class="col-sm-12" style="margin-top: 5px;">
                                <div class="col-sm-4">
                                    <select class="form-control" name="tipoTelefoneadd['.$rs['idtelefoneadicional'].']">&lt;&gt;
                                        <option '.$selRes.' value="Residencial">Residencial</option>
                                        <option '.$selCom.' value="Comercial">Comercial</option>
                                        <option '.$selCel.' value="Celular">Celular</option>
                                    </select>
                                </div>
                                <div class="col-sm-3">
                                    <input type="text" class="form-control telefone" name="telAdicionaladd['.$rs['idtelefoneadicional'].']" placeholder="(00) 0000-0000#" maxlength="15" autocomplete="off" value="'.$rs['telefone'].'">
                                </div>
                                <div class="col-sm-3">
                                    <input type="text" class="form-control" name="telContatoadd['.$rs['idtelefoneadicional'].']" placeholder="Contato" autocomplete="off" value="'.utf8_encode($rs['contato']).'">
                                </div>
                                <div class="col-sm-2">
                                    <button type="button" onclick="removerTelefoneAdicional(\''.$rs['idtelefoneadicional'].'\')" class="btn btn-danger">Remover</button>
                                </div>
                            </div>';
            }
            return $retorno;
        }
                
        #Atualiza Telefones Adicionais
        function AtualizaTelefonesAdicionais($tipoTelefoneadd, $telAdicionaladd, $telContatoadd){
            foreach($tipoTelefoneadd as $key => $value){
                $Sql = "UPDATE t_clientes_telefonesadicionais SET tipotelefone = '$value', telefone = '{$telAdicionaladd[$key]}', contato = '".utf8_decode($telContatoadd[$key])."' WHERE idtelefoneadicional = $key";
                parent::Execute($Sql);
            }
        }
        
        #EDIT Emails Adicionais
        function MontaEmailsAdicionais($idcliente){
            $Sql = "SELECT * FROM t_clientes_emailsadicionais WHERE idcliente = $idcliente";
            $result = parent::Execute($Sql);
            while($rs = parent::ArrayData($result)){
                $retorno .= '<div id="divEmaAdd'.$rs['idemailadicional'].'" class="col-sm-12" style="margin-top: 5px;">
                                <div class="col-sm-9">
                                    <input type="email" class="form-control" name="emailAdicionaladd['.$rs['idemailadicional'].']" value="'.$rs['email'].'">
                                </div>
                                <div class="col-sm-3">
                                    <button type="button" onclick="removerEmailAdicional(\''.$rs['idemailadicional'].'\')" class="btn btn-danger">Remover</button>
                                </div>
                            </div>';
            }
            return $retorno;
        }
        
        #Atualiza Emails Adicionais
        function AtualizaEmailsAdicionais($emailAdicionaladd){
            foreach($emailAdicionaladd as $key => $value){
                $Sql = "UPDATE t_clientes_emailsadicionais SET email = '".utf8_decode($value)."' WHERE idemailadicional = $key";
                parent::Execute($Sql);
            }
        }
        
        #Atualiza Cliente
        function AtualizaCliente($idcliente, $idtipocliente, $idtipoprofissional, $nome, $cnpj_cpf, $idtipoendereco, $cep, $cidade, $estado, $endereco, $numero, $bairro, $complemento, $ponto_referencia, $telefone, $celular, $email, $nome_socio, $cpf_socio, $arrTelefones, $arrTipoTelefones, $arrTelContatos, $arrEmails, $arrTipoEnd, $arrCeps, $arrEnderecos, $arrNumeros, $arrBairros, $arrCidades, $arrEstados, $arrComps, $arrRefs, $inscricao_estadual, $ativo){
            if($idtipocliente == 1){
                $auxcnpjcpf = 'cpf';
            }elseif($idtipocliente == 2){
                $auxcnpjcpf = 'cnpj';
            }
            $Sql = "UPDATE t_clientes SET nome = '$nome', idtipocliente = '$idtipocliente', endereco = '".ucwords($endereco)."', numero = '$numero', bairro = '".ucwords($bairro)."', idtipoenderecoprincipal = '$idtipoendereco', cep = '$cep', cidade = '".ucwords($cidade)."', estado = '$estado', complemento = '".ucfirst($complemento)."', ponto_referencia = '".ucfirst($ponto_referencia)."', 
                    telefone = '$telefone', celular = '$celular', $auxcnpjcpf = '$cnpj_cpf', idtipoprofissional = '$idtipoprofissional', email = '$email', nome_socio = '".ucwords($nome_socio)."', cpf_socio = '$cpf_socio', inscricao_estadual = '$inscricao_estadual', ativo = $ativo WHERE idcliente = $idcliente";
            
            if(parent::Execute($Sql)){
                #Verifica adicionais
                if($arrTipoTelefones[0] != ''){
                    $this->InsereTelefonesAdicionais($idcliente, $arrTipoTelefones, $arrTelefones, $arrTelContatos);
                }
                
                if($arrEmails[0] != ''){
                    $this->InsereEmailsAdicionais($idcliente, $arrEmails);
                }
                
                if($arrCeps[0] != ''){
                    $this->InsereEnderecosAdicionais($idcliente, $arrTipoEnd, $arrCeps, $arrEnderecos, $arrNumeros, $arrBairros, $arrCidades, $arrEstados, $arrComps, $arrRefs);
                }
                parent::RedirecionaPara('lista-cliente');
            }else{
                parent::ChamaManutencao();
            }
        }

        #Lista Clientes
        function ListaClientes($busca_nome, $busca_cnpj, $busca_cpf, $busca_bairro, $busca_telefone, $pagina, $busca_cidade, $buscaDataIni, $buscaDataFim){
        	if($buscaDataIni == ''){
        		$buscaDataIni = '0000-00-00';
        	}
        	if($buscaDataFim == ''){
        		$buscaDataFim = '0000-00-00';
        	}
            $Auxilio = parent::CarregaHtml('Clientes/itens/lista-cliente-itens');
            $inicio = ($pagina * Limite) - Limite;
            $Sql = "SELECT C.*, P.tipo AS tipoprofissional, X.idusuario AS carteira FROM t_clientes C 
                    INNER JOIN fixo_tipo_profissional P ON C.idtipoprofissional = P.idtipoprofissional 
            		LEFT JOIN t_usuarios_carteira_clientes X ON X.idcliente = C.idcliente 
                    WHERE 1";
            if($busca_nome != ''){
                $Sql .= " AND (C.nome LIKE '%$busca_nome%' OR C.nome_socio LIKE '%$busca_nome%')";
            }
            if($busca_cpf != ''){
                $Sql .= " AND (C.cpf LIKE '%$busca_cpf%' OR C.cpf_socio LIKE '%$busca_cpf%')";
            }
            if($busca_cnpj != ''){
                $Sql .= " AND C.cnpj LIKE '%$busca_cnpj%'";
            }
            if($busca_bairro != ''){
            	$Sql .= " AND C.bairro LIKE '%$busca_bairro%'";
            }
            if($busca_cidade != ''){
            	$Sql .= " AND C.cidade LIKE '%$busca_cidade%'";
            }
            if($busca_telefone != ''){
            	$Sql .= " AND C.telefone LIKE '%$busca_telefone%'";
            }
            $Sql .= " ORDER BY C.nome ASC LIMIT $inicio, ".Limite;
            
            $result = parent::Execute($Sql);
            $linha = parent::Linha($result);
            if($linha){
                while($rs = parent::ArrayData($result)){
                    $Linha = $Auxilio;
                    $Linha = str_replace('<%ID%>', $rs['idcliente'], $Linha);
                    $Linha = str_replace('<%NOME%>', $rs['nome'], $Linha);
                    $Linha = str_replace('<%TIPOPROFISSIONAL%>', $rs['tipoprofissional'], $Linha);
                    $addr = $rs['endereco'] . ", Nº " . $rs['numero'] . " - " . $rs['bairro'] . " - " . $rs['cidade'] . "/" . $rs['estado'];
                    $Linha = str_replace('<%ENDERECO%>', $addr, $Linha);
                    if($rs['carteira'] == $_SESSION['idusuario']){
                    	#Remover da carteira
                    	$link_carteira = '<a href="javascript:void(0)" onclick="removerCarteira('.$rs['idcliente'].')">Remover da Carteira</a>';
                    }else{
                    	#Link adicionar
                    	$link_carteira = '<a href="javascript:void(0)" onclick="adicionarCarteira('.$rs['idcliente'].')">Adicionar à Carteira</a>';
                    }
                    $Linha = str_replace('<%LINKCARTEIRA%>', $link_carteira, $Linha);
                    $Linha = str_replace('<%CARTEIRA%>', parent::BuscaUsuarioPorId($rs['carteira']), $Linha);
                    $Linha = str_replace('<%TELEFONE%>', $rs['telefone'], $Linha);
                    if($rs['idtipocliente'] == 1){
                        $cnpjcpf = 'CPF: ' . $rs['cpf'];
                    }elseif($rs['idtipocliente'] == 2){
                        $cnpjcpf = 'CNPJ: ' . $rs['cnpj'];
                    }
                    if($rs['ativo'] == 1){
                    	$Linha = str_replace("<%ATIVOINATIVO%>", 'Ativo', $Linha);
                    	$Linha = str_replace("<%BOTAOAI%>", '<a href="javascript:void(0)" onclick="inativar('.$rs['idcliente'].', \''.$rs['nome'].'\')">Inativar</a>', $Linha);
                    }elseif($rs['ativo'] == 9){
                    	$Linha = str_replace("<%ATIVOINATIVO%>", 'Pré-Cadastro', $Linha);
                    	$Linha = str_replace("<%BOTAOAI%>", '<a href="javascript:void(0)" onclick="inativar('.$rs['idcliente'].', \''.$rs['nome'].'\')">Inativar</a>', $Linha);
                    }else{
                    	$Linha = str_replace("<%ATIVOINATIVO%>", 'Inativo', $Linha);
                    	$Linha = str_replace("<%BOTAOAI%>", '<a href="javascript:void(0)" onclick="ativar('.$rs['idcliente'].', \''.$rs['nome'].'\')">Ativar</a>', $Linha);
                    }
                    $Linha = str_replace('<%CNPJCPF%>', $cnpjcpf, $Linha);
                    #Verifica consulta, se tiver
                    $SqlConsulta = "SELECT * FROM t_clientes_consulta WHERE idcliente = " . $rs['idcliente'];
                    $resultConsulta = parent::Execute($SqlConsulta);
                    $linhaConsulta = parent::Linha($resultConsulta);
                    if($linhaConsulta){
                    	$rsConsulta = parent::ArrayData($resultConsulta);
                    	$consultaHTML = '<a target="_blank" href="'.UrlPadrao.$rsConsulta['caminho'].'">Consulta</a>';
                    }else{
                    	$consultaHTML = "";
                    }
                    $Linha = str_replace('<%CONSULTA%>', $consultaHTML, $Linha);
                    #Verifica última interação
                    $SqlInteracao = "SELECT data, usuario FROM t_clientes_historico WHERE idcliente = " . $rs['idcliente'] . " ORDER BY data DESC LIMIT 0, 1";
                    $resultInteracao = parent::Execute($SqlInteracao);
                    $linhaInteracao = parent::Linha($resultInteracao);
                    if($linhaInteracao){
                    	$rsInteracao = parent::ArrayData($resultInteracao);
                    	$dataInteracao = date("d/m/Y - H:i", strtotime($rsInteracao['data']));
                    	$interacaoHTML = "$dataInteracao <br/> {$rsInteracao['usuario']}";
                    }else{
                    	$interacaoHTML = "";
                    }
                    $Linha = str_replace('<%ULTIMAINTERACAO%>', $interacaoHTML, $Linha);
                    $Clientes .= $Linha;
                }
            }else{
                $Clientes = '<tr class="odd gradeX">
                                <td colspan="9">Não foram encontrados clientes cadastrados.</td>
                             <tr>';
            }
            
            return utf8_encode($Clientes);
        }
        
        #Monta Tipo Cliente
        function SelectTipoCliente($idtipocliente){
            $Sql = "SELECT * FROM fixo_tipo_cliente ORDER BY tipo";
			$select_tcliente = "<select onchange='mascaraCnpjCpf()' id='tipocliente' required class='form-control' name='tipocliente'>";
			$select_tcliente .= "<option selected value=''>Tipo do Cliente</option>";
			$result = parent::Execute($Sql);
			if($result){
				while($rs = parent::ArrayData($result)){
					if($rs['idtipocliente'] == $idtipocliente){
						$select_tcliente .= "<option selected value='".$rs['idtipocliente']."'>".$rs['tipo']."</option>";
					}else{
						$select_tcliente .= "<option value='".$rs['idtipocliente']."'>".$rs['tipo']."</option>";
					}
				}
				$select_tcliente .= "</select>";
				return utf8_encode($select_tcliente);
			}else{
				return false;
			}
        }
    
        #Monta Tipo Profissional
        function SelectTipoProfissional($idtipoprofissional){
            $Sql = "SELECT * FROM fixo_tipo_profissional WHERE ativo = 1 ORDER BY tipo";
			$select_tpro = "<select required class='form-control' name='tipoprofissional'>";
			$select_tpro .= "<option selected value=''>Tipo do Profissional</option>";
			$result = parent::Execute($Sql);
			if($result){
				while($rs = parent::ArrayData($result)){
					if($rs['idtipoprofissional'] == $idtipoprofissional){
						$select_tpro .= "<option selected value='".$rs['idtipoprofissional']."'>".$rs['tipo']."</option>";
					}else{
						$select_tpro .= "<option value='".$rs['idtipoprofissional']."'>".$rs['tipo']."</option>";
					}
				}
				$select_tpro .= "</select>";
				return utf8_encode($select_tpro);
			}else{
				return false;
			}
        }
        
        #Monta Tipo Endereço
        function SelectTipoEndereco($idtipoendereco){
            $Sql = "SELECT * FROM fixo_tipo_endereco ORDER BY tipo";
			$select_tend = "<select required class='form-control' name='tipoendereco_p'>";
			$select_tend .= "<option selected value=''>Tipo do Endereço</option>";
			$result = parent::Execute($Sql);
			if($result){
				while($rs = parent::ArrayData($result)){
					if($rs['idtipoendereco'] == $idtipoendereco){
						$select_tend .= "<option selected value='".$rs['idtipoendereco']."'>".$rs['tipo']."</option>";
					}else{
						$select_tend .= "<option value='".$rs['idtipoendereco']."'>".$rs['tipo']."</option>";
					}
				}
				$select_tend .= "</select>";
				return utf8_encode($select_tend);
			}else{
				return false;
			}
        }
        
        #Busca cliente existente por cpf/cnpj
        function BuscaClienteExistente($idtipocliente, $cnpj_cpf){
            if($idtipocliente == 1){
                $where = "WHERE cnpj = $cnpj_cpf";
            }elseif($idtipocliente == 2){
                $where = "WHERE cpf = $cnpj_cpf";
            }
            
            $Sql = "SELECT * FROM t_clientes $where";
            return parent::Execute($Sql);
        }
        
        #Busca cliente existente por nome - pré cadastro
        function BuscaClienteExistentePorNome($nome){
        	$Sql = "SELECT * FROM t_clientes WHERE nome = '$nome'";
        	return parent::Execute($Sql);
        }
        
        #Busca cliente por ID
        function BuscaClientePorId($idcliente){
            $Sql = "SELECT * FROM t_clientes WHERE idcliente = $idcliente";
            $result = parent::Execute($Sql);
            return parent::ArrayData($result);
        }
        
        #Monta paginacao
        function MontaPaginacao($busca_nome, $busca_cnpj, $busca_cpf, $busca_bairro, $busca_telefone, $pagina, $busca_cidade){
            $totalPaginas = $this->TotalPaginas($busca_nome, $busca_cnpj, $busca_cpf, $busca_bairro, $busca_telefone, $pagina, $busca_cidade);
            $pag = '';
            if($busca_nome || $busca_cnpj || $busca_cpf || $busca_bairro || $busca_telefone || $busca_cidade){
                $url = "busca_nome=$busca_nome&busca_cnpj=$busca_cnpj&busca_cpf=$busca_cpf&busca_bairro=".utf8_encode($busca_bairro)."&busca_cidade=".utf8_encode($busca_cidade)."&busca_telefone=$busca_telefone";
            }
            $url .= "&page=";
            if($totalPaginas > 1){
                if($pagina == 1){
                    $pag = '<span class="page active">&laquo;</span>';
                    $pag .= '<span class="page active">1</span>';
                }else{
                    $pag .= '<a href="'.UrlPadrao.'lista-cliente/?'.$url.($pagina-1).'" class="page">&laquo;</a>';
                    $pag .= '<a href="'.UrlPadrao.'lista-cliente/?'.$url.'1" class="page">1</a>';
                }
                $pag .= '<span class="page">...</span>';
                
                #Monta a paginação do meio
				if($totalPaginas < QtdPag){
				    if($pagina <= $totalPaginas){
				        for($i = 2; $i <= $totalPaginas - 1; $i++){
				            if($i == $pagina){
        						$pag .= '<span class="page active">'.$i.'</span>'; 
        					}else{
        						$pag .= '<a href="'.UrlPadrao.'lista-cliente/?'.$url.$i.'" class="page">'.$i.'</a>';	
        					}
				        }
				    }
				}else{
				    if($pagina > 2){
    					$start = $pagina - 2;
    					$end = $pagina + 2;
    				}elseif($pagina == 2){
    					$start = $pagina - 1;
    					$end = $pagina + 3;
    				}elseif($pagina == 1){
    					$start = 1;
    					$end = $pagina + 4;
    				}
    				if($pagina == $totalPaginas){
    					$start = $pagina - 4;
    					$end = $totalPaginas;
    				}elseif($pagina == ($totalPaginas - 1)){
    					$start = $pagina - 3;
    					$end = $pagina + 1;
    				}
    				for($i = $start; $i <= $end; $i++){
    					if($i == $pagina){
    						$pag .= '<span class="page active">'.$i.'</span>'; 
    					}else{
    						if($i <= $totalPaginas){
    							$pag .= '<a href="'.UrlPadrao.'lista-cliente/?'.$url.$i.'" class="page">'.$i.'</a>';
    						}
    					}
    				}
				}
                
                
                $pag .= '<span class="page">...</span>';
                if($pagina == $totalPaginas){
                    $pag .= '<span class="page active">'.$totalPaginas.'</span>';
                    $pag .= '<span class="page active">&raquo;</span>';
                }else{
                    $pag .= '<a href="'.UrlPadrao.'lista-cliente/?'.$url.$totalPaginas.'" class="page">'.$totalPaginas.'</a>';
                    $pag .= '<a href="'.UrlPadrao.'lista-cliente/?'.$url.($pagina+1).'"class="page">&raquo;</a>';
                }
                
                
                return $pag;
            }else{
                return '';
            }
        }
        
        #Total de paginas
        function TotalPaginas($busca_nome, $busca_cnpj, $busca_cpf, $busca_bairro, $busca_telefone, $pagina, $busca_cidade){
            $Sql = "SELECT C.*, P.tipo AS tipoprofissional FROM t_clientes C 
                    INNER JOIN fixo_tipo_profissional P ON C.idtipoprofissional = P.idtipoprofissional
                    WHERE 1";
            if($busca_nome != ''){
                $Sql .= " AND (C.nome LIKE '%$busca_nome%' OR C.nome_socio LIKE '%$busca_nome%')";
            }
            if($busca_cpf != ''){
                $Sql .= " AND (C.cpf LIKE '%$busca_cpf%' OR C.cpf_socio LIKE '%$busca_cpf%')";
            }
            if($busca_cnpj != ''){
                $Sql .= " AND C.cnpj LIKE '%$busca_cnpj%'";
            }
            if($busca_bairro != ''){
            	$Sql .= " AND C.bairro LIKE '%$busca_bairro%'";
            }
            if($busca_cidade != ''){
            	$Sql .= " AND C.cidade LIKE '%$busca_cidade%'";
            }
            if($busca_telefone != ''){
            	$Sql .= " AND C.telefone LIKE '%$busca_telefone%'";
            }
            $result = parent::Execute($Sql);
			$num_rows = parent::Linha($result);
			$totalPag = ceil($num_rows/Limite);
			return $totalPag;
        }
        
        #Select Busca Bairro
        function SelectBuscaBairro($busca_bairro){
        	$Sql = "SELECT DISTINCT C.bairro AS bairro FROM t_clientes C ORDER BY C.bairro ASC";
        			#UNION SELECT DISTINCT A.bairro AS bairro FROM t_clientes_enderecosadicionais A";
        	$result = parent::Execute($Sql);
        	$linha = parent::Linha($result);
        	if($linha){
        		$retorno = "<select name='bairro' id='busca_bairro' class='form-control' style='width: 10%; float: left;'>";
        		$retorno .= "<option value=''>Bairro</option>";
	        	while($rs = parent::ArrayData($result)){
	        		if($rs['bairro'] == $busca_bairro && $busca_bairro != ''){
	        			$retorno .= "<option value='".utf8_encode($rs['bairro'])."' selected>".utf8_encode($rs['bairro'])."</option>";
	        		}else{
	        			$retorno .= "<option value='".utf8_encode($rs['bairro'])."'>".utf8_encode($rs['bairro'])."</option>";
	        		}
	        	}
	        	$retorno .= "</select>";
        	}
        	return $retorno;
        }
        
        #Select Busca Bairro
        function SelectBuscaCidade($busca_cidade){
        	$Sql = "SELECT DISTINCT C.cidade AS cidade FROM t_clientes C ORDER BY C.cidade ASC";
        	#UNION SELECT DISTINCT A.bairro AS bairro FROM t_clientes_enderecosadicionais A";
        	$result = parent::Execute($Sql);
        	$linha = parent::Linha($result);
        	if($linha){
        		$retorno = "<select name='cidade' id='busca_cidade' class='form-control' style='width: 10%; float: left;'>";
        		$retorno .= "<option value=''>Cidade</option>";
        		while($rs = parent::ArrayData($result)){
        			if($rs['cidade'] == $busca_cidade && $busca_cidade != ''){
        				$retorno .= "<option value='".utf8_encode($rs['cidade'])."' selected>".utf8_encode($rs['cidade'])."</option>";
        			}else{
        				$retorno .= "<option value='".utf8_encode($rs['cidade'])."'>".utf8_encode($rs['cidade'])."</option>";
        			}
        		}
        		$retorno .= "</select>";
        	}
        	return $retorno;
        }
        
        #Select Busca Tipo Cliente
        function SelectBuscaTipoCliente($busca_cliente){
        	$Sql = "SELECT * FROM fixo_tipo_profissional";
        	$result = parent::Execute($Sql);
        	$linha = parent::Linha($result);
        	if($linha){
        		$retorno = "<select name='tipo_cliente' id='busca_cliente' class='form-control' style='width: 10%; float: left;'>";
        		$retorno .= "<option value=''>Tipo</option>";
        		while($rs = parent::ArrayData($result)){
        			if($rs['idtipoprofissional'] == $busca_cliente){
        				$retorno .= "<option value='".utf8_encode($rs['idtipoprofissional'])."' selected>".utf8_encode($rs['tipo'])."</option>";
        			}else{
        				$retorno .= "<option value='".utf8_encode($rs['idtipoprofissional'])."'>".utf8_encode($rs['tipo'])."</option>";
        			}
        		}
        		$retorno .= "</select>";
        	}
        	return $retorno;
        }
        
        #Ativar Cliente
        function Ativar($idcliente){
        	$Sql = "UPDATE t_clientes SET ativo = 1 WHERE idcliente = $idcliente";
        	parent::Execute($Sql);
        }
        
        #Inativar Cliente
        function Inativar($idcliente){
        	$Sql = "UPDATE t_clientes SET ativo = 0 WHERE idcliente = $idcliente";
        	parent::Execute($Sql);
        }
        
        #Excluir Cliente
        function Excluir($idcliente){
        	$SqlVerifica = "SELECT idvenda FROM t_vendas WHERE idcliente = $idcliente";
        	$resultVerifica = parent::Execute($SqlVerifica);
        	$linha = parent::Linha($resultVerifica);
        	if($linha){
        		echo utf8_encode("<script>alert('Esse cliente possui uma venda/orçamento. O sistema impede a exclusão para não alterar o fluxo.');location.href='".UrlPadrao."lista-cliente'</script>");
        	}else{
        		$SqlDelete = "DELETE FROM t_clientes WHERE idcliente = $idcliente";
        		$result = parent::Execute($SqlDelete);
        	}
        }
        
        function MontaHistoricoVendasCliente($idcliente){
        	$Sql = "SELECT * FROM t_vendas WHERE idcliente = $idcliente ORDER BY data DESC";
        	$result = parent::Execute($Sql);
        	$linha = parent::Linha($result);
        	$Auxilio = parent::CarregaHtml('Clientes/itens/historico-vendas-itens');
        	if($linha){
        		while($rs = parent::ArrayData($result)){
        			$produtos = '';
        			$Linha = $Auxilio;
        			if($rs['orcamento'] == 1){
        				$auxVO = 'Orçamento';
        				$opcoes = '';
        			}else{
        				$auxVO = 'Venda';
        				$opcoes = '<ul role="menu" class="dropdown-menu">
						                <li>
						                   	<a target="_blank" href="<%URLPADRAO%>finalizar/'.$rs['idvenda'].'">Reimprimir</a>
						                </li>
						                <li class="divider"></li>
						                <li>
						                    <a target="_blank" href="<%URLPADRAO%>gerar-pdf/'.$rs['idvenda'].'">Gerar PDF</a>
						                </li>
						            </ul>';
        			}
        			$SqlProdutos = "SELECT produto_kit, quantidade FROM t_vendas_produtos WHERE idvenda = " . $rs['idvenda'];
        			$resultProdutos = parent::Execute($SqlProdutos);
        			while($rsProdutos = parent::ArrayData($resultProdutos)){
        				$auxKP = explode('_', $rsProdutos['produto_kit']);
        				
        				if($auxKP[0] == 'prod'){
        					$SqlNome = "SELECT nome FROM t_produtos WHERE idproduto = " . $auxKP[1];
        					$auxNome = "Produto";
        				}else{
        					$SqlNome = "SELECT nome FROM t_kit WHERE idkit = " . $auxKP[1];
        					$auxNome = "Kit";
        				}
        				
        				$resultNome = parent::Execute($SqlNome);
        				$rsNome = parent::ArrayData($resultNome);
        				
        				$produtos .= "$auxNome: {$rsNome['nome']} / {$rsProdutos['quantidade']}UN <br/>";
        			}
        			$Linha = str_replace('<%PRODUTOS%>', $produtos, $Linha);
        			$Linha = str_replace('<%VENDAORCAMENTO%>', $auxVO, $Linha);
        			$Linha = str_replace('<%DATA%>', date("d/m/Y H:i", strtotime($rs['data'])), $Linha);
        			$Linha = str_replace('<%FUNCIONARIO%>', parent::BuscaUsuarioPorId($rs['idusuario']), $Linha);
        			$Linha = str_replace('<%VALORTOTAL%>', "R$ ".number_format($rs['valor_venda'], 2, ',', '.'), $Linha);
        			$Linha = str_replace('<%OPCOES%>', $opcoes, $Linha);
        			$historico .= $Linha;
        		}
        	}else{
        		$historico = '<tr class="odd gradeX">
                                <td colspan="6">Não foram encontrados registros de histórico para esse cliente.</td>
                             <tr>';
        	}
        	return utf8_encode($historico);
        }
        
        function BuscaTipoProfissional(){
        	$Auxilio = parent::CarregaHtml('itens/tipo-profissional-itens');
        	$Sql = "SELECT * FROM fixo_tipo_profissional WHERE idtipoprofissional != 13 ORDER BY tipo ASC";
        	$result = parent::Execute($Sql);
        	while($rs = parent::ArrayData($result)){
        		$SqlValor = "SELECT * from t_valor_profissional WHERE idtipoprofissional = {$rs['idtipoprofissional']}";
        		$resultValor = parent::Execute($SqlValor);
        		$rsValor = parent::ArrayData($resultValor);
        		$select_valor = "<select onchange='alteraValor({$rs['idtipoprofissional']})' class='form-control' id='{$rs['idtipoprofissional']}'>";
        		if($rsValor['valor'] == 'valor_consumidor'){
        			$select_valor .= "<option value='valor_consumidor' selected>Valor Consumidor</option>";
        			$select_valor .= "<option value='valor_profissional'>Valor Profissional</option>";
        		}else{
        			$select_valor .= "<option value='valor_consumidor'>Valor Consumidor</option>";
        			$select_valor .= "<option value='valor_profissional' selected>Valor Profissional</option>";
        		}
        		if($rsValor['ativo'] == 1){
        			$botao_AI = '<button type="button" style="background-color: #B6195B;" onclick="remover('.$rs['idtipoprofissional'].')" class="btn btn-success btn-flat">Inativar</button>';
        		}else{
        			$botao_AI = '<button type="button" style="background-color: #25A1B5;" onclick="remover('.$rs['idtipoprofissional'].')" class="btn btn-success btn-flat">Ativar</button>';
        		}
        		$select_valor .= "</select>";
        		$Linha = $Auxilio;
        		$Linha = str_replace('<%ATIVARINATIVAR%>', $botao_AI, $Linha);
        		$Linha = str_replace('<%ID%>', $rs['idtipoprofissional'], $Linha);
        		$Linha = str_replace('<%TIPOVALOR%>', $select_valor, $Linha);
        		$Linha = str_replace('<%TIPO%>', $rs['tipo'], $Linha);
        		$retorno .= $Linha;
        	}
        	return utf8_encode($retorno);
        }
    }
?>