<?php
    class bancocliente extends banco{
        
        #Insere Cliente
        function InsereCliente($idtipocliente, $idtipoprofissional, $nome, $cnpj_cpf, $idtipoendereco, $cep, $cidade, $estado, $endereco, $numero, $bairro, $complemento, $ponto_referencia, $telefone, $celular, $email, $nome_socio, $cpf_socio, $arrTelefones, $arrTipoTelefones, $arrEmails, $arrTipoEnd, $arrCeps, $arrEnderecos, $arrNumeros, $arrBairros, $arrCidades, $arrEstados, $arrComps, $arrRefs){
            if($idtipocliente == 1){
                $auxcnpjcpf = 'cpf';
            }elseif($idtipocliente == 2){
                $auxcnpjcpf = 'cnpj';
            }
            $Sql = "INSERT INTO t_clientes (nome, idtipocliente, endereco, numero, bairro, idtipoenderecoprincipal, cep, cidade, estado, complemento, ponto_referencia, telefone, celular, $auxcnpjcpf, idtipoprofissional, email, data_cadastro, nome_socio, cpf_socio) 
                    VALUES ('".ucwords($nome)."', '$idtipocliente', '".ucwords($endereco)."', '$numero', '".ucwords($bairro)."', '$idtipoendereco', '$cep', '".ucwords($cidade)."', '$estado', '".ucfirst($complemento)."', '".ucfirst($ponto_referencia)."', '$telefone', '$celular', '$cnpj_cpf', '$idtipoprofissional', '$email', '".date("Y-m-d H:i:s")."', '".ucwords($nome_socio)."', '$cpf_socio')";
            if(parent::Execute($Sql)){
                $lastid = mysql_insert_id();
            }else{
                parent::ChamaManutencao();
            }
            
            #Verifica adicionais
            if($arrTipoTelefones[0] != ''){
                $this->InsereTelefonesAdicionais($lastid, $arrTipoTelefones, $arrTelefones);
            }
            
            if($arrEmails[0] != ''){
                $this->InsereEmailsAdicionais($lastid, $arrEmails);
            }
            
            if($arrCeps[0] != ''){
                $this->InsereEnderecosAdicionais($lastid, $arrTipoEnd, $arrCeps, $arrEnderecos, $arrNumeros, $arrBairros, $arrCidades, $arrEstados, $arrComps, $arrRefs);
            }
            
            parent::RedirecionaPara('lista-cliente');
        }
        
        #Telefones adicionais
        function InsereTelefonesAdicionais($idcliente, $arrTipoTelefones, $arrTelefones){
            foreach($arrTipoTelefones as $key => $value){
                if($arrTelefones[$key] != ''){
                    $Sql = "INSERT INTO t_clientes_telefonesadicionais (tipotelefone, telefone, idcliente) VALUES ('".utf8_decode($value)."', '{$arrTelefones[$key]}', '$idcliente')";
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
        
        #Endere�os adicionais
        function InsereEnderecosAdicionais($idcliente, $arrTipoEnd, $arrCeps, $arrEnderecos, $arrNumeros, $arrBairros, $arrCidades, $arrEstados, $arrComps, $arrRefs){
            foreach($arrCeps as $key => $value){
                if($value != ''){
                    $Sql = "INSERT INTO t_clientes_enderecosadicionais (idtipoendereco, cep, endereco, numero, bairro, cidade, estado, ponto_referencia, complemento, idcliente) VALUES ('".$arrTipoEnd[$key]."', '$value', '".ucwords(utf8_decode($arrEnderecos[$key]))."', '{$arrNumeros[$key]}', '".ucwords(utf8_decode($arrBairros[$key]))."', '".ucwords(utf8_decode($arrCidades[$key]))."', '".utf8_decode($arrEstados[$key])."', '".ucfirst(utf8_decode($arrRefs[$key]))."', '".ucfirst(utf8_decode($arrComps[$key]))."', '$idcliente')";
                    parent::Execute($Sql);
                }
            }
        }
        
        #EDIT Endere�os Adicionais
        function MontaEnderecosAdicionais($idcliente){
            $Sql = "SELECT * FROM t_clientes_enderecosadicionais WHERE idcliente = $idcliente";
            $result = parent::Execute($Sql);
            while($rs = parent::ArrayData($result)){
                $tipoenderecoadd = $this->SelectTipoEndAdd($rs['idtipoendereco'], $rs['idenderecoadicional']);
                $retorno .= '<div id="divEndAdd'.$rs['idenderecoadicional'].'" class="col-sm-12" style="margin-top: 5px; background-color: aliceblue; border-radius: 4px">
                                <div class="form-group" style="width: 50%; float: left">
                                    <label class="col-sm-6 control-label">*Tipo Endere�o</label>
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
                                    <label class="col-sm-3 control-label form-margin">*Endere�o</label>
                                    <div class="col-sm-6">
                                        <input required="" type="text" id="endereco_add'.$rs['idenderecoadicional'].'" class="form-control" name="enderecoadd['.$rs['idenderecoadicional'].']" value="'.$rs['endereco'].'">
                                    </div>
                                </div>
                                <div class="form-group" style="width: 50%; float: left">
                                    <label class="col-sm-3 control-label form-margin">*N�mero</label>
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
                                    <label style="width: 100%;" class="col-sm-3 control-label form-margin">Ponto de Refer�ncia</label>
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
        
        #Monta Tipo Endere�o Adicional
        function SelectTipoEndAdd($idtipoendereco, $idenderecoadicional){
            $Sql = "SELECT * FROM fixo_tipo_endereco ORDER BY tipo";
			$select_tend = "<select required class='form-control' name='tipoenderecoadd[$idenderecoadicional]'>";
			$select_tend .= "<option selected value=''>Tipo do Endere�o</option>";
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
        
        #Atualiza Endere�os Adicionais
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
                                <div class="col-sm-5">
                                    <input type="text" class="form-control telefone" name="telAdicionaladd['.$rs['idtelefoneadicional'].']" placeholder="(00) 0000-0000#" maxlength="15" autocomplete="off" value="'.$rs['telefone'].'">
                                </div>
                                <div class="col-sm-3">
                                    <button type="button" onclick="removerTelefoneAdicional(\''.$rs['idtelefoneadicional'].'\')" class="btn btn-danger">Remover</button>
                                </div>
                            </div>';
            }
            return $retorno;
        }
                
        #Atualiza Telefones Adicionais
        function AtualizaTelefonesAdicionais($tipoTelefoneadd, $telAdicionaladd){
            foreach($tipoTelefoneadd as $key => $value){
                $Sql = "UPDATE t_clientes_telefonesadicionais SET tipotelefone = '$value', telefone = '{$telAdicionaladd[$key]}' WHERE idtelefoneadicional = $key";
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
        function AtualizaCliente($idcliente, $idtipocliente, $idtipoprofissional, $nome, $cnpj_cpf, $idtipoendereco, $cep, $cidade, $estado, $endereco, $numero, $bairro, $complemento, $ponto_referencia, $telefone, $celular, $email, $nome_socio, $cpf_socio, $arrTelefones, $arrTipoTelefones, $arrEmails, $arrTipoEnd, $arrCeps, $arrEnderecos, $arrNumeros, $arrBairros, $arrCidades, $arrEstados, $arrComps, $arrRefs){
            if($idtipocliente == 1){
                $auxcnpjcpf = 'cpf';
            }elseif($idtipocliente == 2){
                $auxcnpjcpf = 'cnpj';
            }
            $Sql = "UPDATE t_clientes SET nome = '$nome', idtipocliente = '$idtipocliente', endereco = '".ucwords($endereco)."', numero = '$numero', bairro = '".ucwords($bairro)."', idtipoenderecoprincipal = '$idtipoendereco', cep = '$cep', cidade = '".ucwords($cidade)."', estado = '$estado', complemento = '".ucfirst($complemento)."', ponto_referencia = '".ucfirst($ponto_referencia)."', 
                    telefone = '$telefone', celular = '$celular', $auxcnpjcpf = '$cnpj_cpf', idtipoprofissional = '$idtipoprofissional', email = '$email', nome_socio = '".ucwords($nome_socio)."', cpf_socio = '$cpf_socio' WHERE idcliente = $idcliente";
            if(parent::Execute($Sql)){
                #Verifica adicionais
                if($arrTipoTelefones[0] != ''){
                    $this->InsereTelefonesAdicionais($idcliente, $arrTipoTelefones, $arrTelefones);
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
        function ListaClientes($busca_nome, $busca_cnpj, $busca_cpf, $busca_bairro, $busca_cliente, $pagina){
            $Auxilio = parent::CarregaHtml('Clientes/itens/lista-cliente-itens');
            $inicio = ($pagina * Limite) - Limite;
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
            if($busca_cliente != ''){
            	$Sql .= " AND C.idtipoprofissional = $busca_cliente";
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
                    $addr = $rs['endereco'] . ", N� " . $rs['numero'] . " - " . $rs['bairro'] . " - " . $rs['cidade'] . "/" . $rs['estado'];
                    $Linha = str_replace('<%ENDERECO%>', $addr, $Linha);
                    $Linha = str_replace('<%TELEFONE%>', $rs['telefone'], $Linha);
                    if($rs['idtipocliente'] == 1){
                        $cnpjcpf = 'CPF: ' . $rs['cpf'];
                    }elseif($rs['idtipocliente'] == 2){
                        $cnpjcpf = 'CNPJ: ' . $rs['cnpj'];
                    }
                    if($rs['ativo'] == 1){
                    	$Linha = str_replace("<%ATIVOINATIVO%>", 'Ativo', $Linha);
                    	$Linha = str_replace("<%BOTAOAI%>", '<a href="javascript:void(0)" onclick="inativar('.$rs['idcliente'].', \''.$rs['nome'].'\')">Inativar</a>', $Linha);
                    }else{
                    	$Linha = str_replace("<%ATIVOINATIVO%>", 'Inativo', $Linha);
                    	$Linha = str_replace("<%BOTAOAI%>", '<a href="javascript:void(0)" onclick="ativar('.$rs['idcliente'].', \''.$rs['nome'].'\')">Ativar</a>', $Linha);
                    }
                    $Linha = str_replace('<%CNPJCPF%>', $cnpjcpf, $Linha);
                    $Clientes .= $Linha;
                }
            }else{
                $Clientes = '<tr class="odd gradeX">
                                <td colspan="7">N�o foram encontrados clientes cadastrados.</td>
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
            $Sql = "SELECT * FROM fixo_tipo_profissional ORDER BY tipo";
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
        
        #Monta Tipo Endere�o
        function SelectTipoEndereco($idtipoendereco){
            $Sql = "SELECT * FROM fixo_tipo_endereco ORDER BY tipo";
			$select_tend = "<select required class='form-control' name='tipoendereco_p'>";
			$select_tend .= "<option selected value=''>Tipo do Endere�o</option>";
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
        
        #Busca cliente por ID
        function BuscaClientePorId($idcliente){
            $Sql = "SELECT * FROM t_clientes WHERE idcliente = $idcliente";
            $result = parent::Execute($Sql);
            return parent::ArrayData($result);
        }
        
        #Monta paginacao
        function MontaPaginacao($busca_nome, $busca_cnpj, $busca_cpf, $busca_bairro, $busca_cliente, $pagina){
            $totalPaginas = $this->TotalPaginas($busca_nome, $busca_cnpj, $busca_cpf, $busca_bairro, $busca_cliente, $pagina);
            $pag = '';
            if($busca_nome || $busca_cnpj || $busca_cpf || $busca_bairro || $busca_cliente){
                $url = "busca_nome=$busca_nome&busca_cnpj=$busca_cnpj&busca_cpf=$busca_cpf&busca_bairro=".utf8_encode($busca_bairro)."&busca_cliente=$busca_cliente";
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
                
                #Monta a pagina��o do meio
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
        function TotalPaginas($busca_nome, $busca_cnpj, $busca_cpf, $busca_bairro, $busca_cliente, $pagina){
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
            if($busca_cliente != ''){
            	$Sql .= " AND C.idtipoprofissional = $busca_cliente";
            }
            $result = parent::Execute($Sql);
			$num_rows = parent::Linha($result);
			$totalPag = ceil($num_rows/Limite);
			return $totalPag;
        }
        
        #Select Busca Bairro
        function SelectBuscaBairro($busca_bairro){
        	$Sql = "SELECT DISTINCT C.bairro AS bairro FROM t_clientes C";
        			#UNION SELECT DISTINCT A.bairro AS bairro FROM t_clientes_enderecosadicionais A";
        	$result = parent::Execute($Sql);
        	$linha = parent::Linha($result);
        	if($linha){
        		$retorno = "<select name='bairro' id='busca_bairro' class='form-control' style='width: 10%; float: left;'>";
        		$retorno .= "<option value=''>Bairro</option>";
	        	while($rs = parent::ArrayData($result)){
	        		if($rs['bairro'] == $busca_bairro){
	        			$retorno .= "<option value='".utf8_encode($rs['bairro'])."' selected>".utf8_encode($rs['bairro'])."</option>";
	        		}else{
	        			$retorno .= "<option value='".utf8_encode($rs['bairro'])."'>".utf8_encode($rs['bairro'])."</option>";
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
        		echo utf8_encode("<script>alert('Esse cliente possui uma venda/or�amento.O sistema impede a exclus�o para n�o alterar o fluxo.');location.href='".UrlPadrao."lista-cliente'</script>");
        	}else{
        		$SqlDelete = "DELETE FROM t_clientes WHERE idcliente = $idcliente";
        		$result = parent::Execute($SqlDelete);
        	}
        }
    }
?>