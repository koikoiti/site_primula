<?php
    class bancoentradaproduto extends banco{
    	
    	function BuscaProdutosEditar($identrada){
    		$Sql = "SELECT * FROM t_entrada_produtos WHERE identrada = $identrada";
    		$result = parent::Execute($Sql);
    		while($rs = parent::ArrayData($result)){
    			$auxProd = explode("_", $rs['produto_kit']);
    			if($auxProd[0] == 'prod'){
    				$SqlProduto = "SELECT * FROM t_produtos P INNER JOIN t_imagens_produto I ON I.idproduto = P.idproduto WHERE P.idproduto = " . $auxProd[1] . " AND I.ordem = 1";
    				$resultProduto = parent::Execute($SqlProduto);
    				$rsProduto = parent::ArrayData($resultProduto);
    				$retorno .= '<div id="novo'.$rs['produto_kit'].'" class="novo-produto">
		    					<div id="outer'.$rs['produto_kit'].'" class="col-md-2 text-center">
		    						<img id="img'.$rs['produto_kit'].'" src="'.UrlFoto.$rsProduto['caminho'].'" style="width: 100px; height: 100px;">
		    					</div>
		    					<div class="col-sm-10 no-padding">
		    						<div class="col-sm-11 no-padding">
		    							<div class="col-md-12" id="div_produto'.$rs['produto_kit'].'">
		    								Produto: <input readonly id="produtonovo'.$rs['produto_kit'].'" value="'.$rsProduto['nome'].'" type="text" class="form-control produto ui-autocomplete-input" autocomplete="off">
		    							</div>
		    							<div class="col-md-2">
		    								Quantidade: <input type="text" class="form-control quantidade" value="'.$rs['quantidade'].'" name="quantidade[]">
		    							</div>
		    							<div class="col-md-2">
		    								Lote: <input name="lote[]" type="text" class="form-control" value="'.$rs['lote'].'">
		    							</div>
		    							<div class="col-md-2">
		    								Validade: <input value="'.$rs['validade'].'" name="validade[]" type="date" class="form-control">
		    							</div>
		    						</div>
		    						<div class="col-sm-1 no-padding">
		    							<div class="col-sm-1">
		    								<br><button onclick="menos(\''.$rs['produto_kit'].'\')" type="button" class="btn btn-danger">-</button>
		    							</div>
		    						</div>
		    					</div>
		    					<input type="hidden" name="produtos[]" id="hid_produtoeditar'.$rs['produto_kit'].'" value="'.$rs['produto_kit'].'">
		    				</div>';
    			}else{
    				$SqlKit = "SELECT * FROM t_kit K INNER JOIN t_imagens_kit I ON I.idkit = K.idkit WHERE K.idkit = " . $auxProd[1] . " AND I.ordem = 1";
    				 
    				$resultKit = parent::Execute($SqlKit);
    				$rsKit = parent::ArrayData($resultKit);
    				$retorno .= '<div id="novo'.$rs['produto_kit'].'" class="novo-produto">
		    					<div id="outer'.$rs['produto_kit'].'" class="col-md-2 text-center">
		    						<img id="img'.$rs['produto_kit'].'" src="'.UrlFoto.$rsKit['caminho'].'" style="width: 100px; height: 100px;">
		    					</div>
		    					<div class="col-sm-10 no-padding">
		    						<div class="col-sm-11 no-padding">
		    							<div class="col-md-12" id="div_produto'.$rs['produto_kit'].'">
		    								Produto: <input readonly id="produtonovo'.$rs['produto_kit'].'" value="'.$rsKit['nome'].'" type="text" class="form-control produto ui-autocomplete-input" autocomplete="off">
		    							</div>
		    							<div class="col-md-2">
		    								Quantidade: <input type="text" class="form-control quantidade" value="'.$rs['quantidade'].'" name="quantidade[]">
		    							</div>
		    							<div class="col-md-2">
		    								Lote: <input name="lote[]" type="text" class="form-control" value="'.$rs['lote'].'">
		    							</div>
		    							<div class="col-md-2">
		    								Validade: <input value="'.$rs['validade'].'" name="validade[]" type="date" class="form-control">
		    							</div>
		    						</div>
		    						<div class="col-sm-1 no-padding">
		    							<div class="col-sm-1">
		    								<br><button onclick="menos(\''.$rs['produto_kit'].'\')" type="button" class="btn btn-danger">-</button>
		    							</div>
		    						</div>
		    					</div>
		    					<input type="hidden" name="produtos[]" id="hid_produtoeditar'.$rs['produto_kit'].'" value="'.$rs['produto_kit'].'">
		    				</div>';
    			}
    		}
    		return utf8_encode($retorno);
    	}
    	
    	function BuscaEntradaPorId($identrada){
    		$Sql = "SELECT * FROM t_entrada WHERE identrada = $identrada";
    		$result = parent::Execute($Sql);
    		return parent::ArrayData($result);
    	}
        
        #Insere Entrada
        function InsereEntrada($fornecedor, $nf, $valor, $frete, $arrProdutos, $arrQuantidade, $arrLote, $arrVencimento, $dataEntrada){
            $Sql = "INSERT INTO t_entrada (fornecedor, nf, valor, frete, data_entrada) VALUES ('$fornecedor', '$nf', '$valor', '$frete', '$dataEntrada')";
            parent::Execute($Sql);
            $lastID = mysql_insert_id();
            
            #Insere produtos
            foreach($arrProdutos as $key => $value){
            	$SqlProdutos = "INSERT INTO t_entrada_produtos (identrada, produto_kit, quantidade, lote, validade) VALUES ('$lastID', '$value', '{$arrQuantidade[$key]}', '{$arrLote[$key]}', '$arrVencimento[$key]')";
            	parent::Execute($SqlProdutos);
            }
            
            #Arruma Estoque
            $this->arrumaEstoqueAdiciona($lastID);
            
            parent::RedirecionaPara('lista-entrada-produto');
        }
        
        #Atualiza Entrada
        function AtualizaEntrada($identrada, $fornecedor, $nf, $valor, $frete, $arrProdutos, $arrQuantidade, $arrLote, $arrVencimento, $dataEntrada){
        	#Remove os produtos
        	$this->arrumaEstoqueDiminui($identrada);
        	
        	#Deleta os produtos
        	$SqlDeleta = "DELETE FROM t_entrada_produtos WHERE identrada = $identrada";
        	parent::Execute($SqlDeleta);
        	
        	#Atualiza
        	$Sql = "UPDATE t_entrada SET fornecedor = '$fornecedor', nf = '$nf', valor = '$valor', frete = '$frete', data_entrada = '$dataEntrada' WHERE identrada = $identrada";
        	parent::Execute($Sql);
        	
        	#Insere produtos
        	foreach($arrProdutos as $key => $value){
        		$SqlProdutos = "INSERT INTO t_entrada_produtos (identrada, produto_kit, quantidade, lote, validade) VALUES ('$identrada', '$value', '{$arrQuantidade[$key]}', '{$arrLote[$key]}', '$arrVencimento[$key]')";
        		parent::Execute($SqlProdutos);
        	}
        	
        	#Arruma Estoque
        	$this->arrumaEstoqueAdiciona($identrada);
        	
        	parent::RedirecionaPara('lista-entrada-produto');
        }
        
        #Exclui entrada
        function ExcluirEntrada($identrada){
        	#Remove os produtos
        	$this->arrumaEstoqueDiminui($identrada);
        	
        	#Deleta
        	$Sql = "DELETE FROM t_entrada WHERE identrada = $identrada";
        	parent::Execute($Sql);
        	
        	parent::RedirecionaPara('lista-entrada-produto');
        }
        
        #Arruma Estoque Diminui 
        function arrumaEstoqueDiminui($identrada){
        	$Sql = "SELECT * FROM t_entrada_produtos WHERE identrada = $identrada";
        	$result = parent::Execute($Sql);
        	while($rs = parent::ArrayData($result)){
        		#Verifica se é kit ou produto
        		$auxPK = explode('_', $rs['produto_kit']);
        		if($auxPK[0] == 'prod'){
        			$idproduto = $auxPK[1];
        			$quantidade = $rs['quantidade'];
        			$SqlProduto = "UPDATE t_produtos SET estoque = estoque - ".$quantidade . " WHERE idproduto = $idproduto";
        			parent::Execute($SqlProduto);
        		}elseif($auxPK[0] == 'kit'){
        			$idkit = $auxPK[1];
        			$SqlKit = "SELECT idproduto, quantidade FROM t_kit_produtos WHERE idkit = $idkit";
        			$resultKit = parent::Execute($SqlKit);
        			while($rsKit = parent::ArrayData($resultKit)){
        				$SqlProduto = "UPDATE t_produtos SET estoque = estoque - ".$rsKit['quantidade'] * $rs['quantidade'] . " WHERE idproduto = " . $rsKit['idproduto'];
        				parent::Execute($SqlProduto);
        			}
        		}
        	}
        }
        
        #Arruma Estoque Adiciona
        function arrumaEstoqueAdiciona($identrada){
        	$Sql = "SELECT * FROM t_entrada_produtos WHERE identrada = $identrada";
        	$result = parent::Execute($Sql);
        	while($rs = parent::ArrayData($result)){
        		#Verifica se é kit ou produto
        		$auxPK = explode('_', $rs['produto_kit']);
        		if($auxPK[0] == 'prod'){
        			$idproduto = $auxPK[1];
        			$quantidade = $rs['quantidade'];
        			$SqlProduto = "UPDATE t_produtos SET estoque = estoque + $quantidade WHERE idproduto = $idproduto";
        			parent::Execute($SqlProduto);
        		}elseif($auxPK[0] == 'kit'){
        			$idkit = $auxPK[1];
        			$SqlKit = "SELECT idproduto, quantidade FROM t_kit_produtos WHERE idkit = $idkit";
        			$resultKit = parent::Execute($SqlKit);
        			while($rsKit = parent::ArrayData($resultKit)){
        				$SqlProduto = "UPDATE t_produtos SET estoque = estoque + ".$rsKit['quantidade'] * $rs['quantidade'] . " WHERE idproduto = " . $rsKit['idproduto'];
        				parent::Execute($SqlProduto);
        			}
        		}
        	}
        }
        
        #Lista entrada
        function ListaEntradaProdutos(){
            $Auxilio = parent::CarregaHtml('Produtos/itens/lista-entrada-produto-itens');
            $Sql = "SELECT * FROM t_entrada";
            $result = parent::Execute($Sql);
            $linha = parent::Linha($result);
            if($linha){
                while($rs = parent::ArrayData($result)){
                    $Linha = $Auxilio;
                    $Linha = str_replace('<%ID%>', $rs['identrada'], $Linha);
                    $Linha = str_replace('<%DATA%>', date("d/m/Y", strtotime($rs['data_entrada'])), $Linha);
                    $Linha = str_replace('<%NF%>', $rs['nf'], $Linha);
                    $Linha = str_replace('<%FORNECEDOR%>', $rs['fornecedor'], $Linha);
                    $Linha = str_replace('<%VALOR%>', number_format($rs['valor'], 2, ',', '.'), $Linha);
                    if($_SESSION['idsetor'] == 1 || $_SESSION['idusuario'] == 33){
                    	$menu_add = '<li>
					                    <a href="<%URLPADRAO%>entrada-produto/editar/'.$rs['identrada'].'">Editar</a>
					                </li>
					                <li>
					                    <a onclick="excluir('.$rs['identrada'].')" href="javascript:void(0)">Excluir</a>
					                </li>';
                    }else{
                    	$menu_add = '';
                    }
                    $Linha = str_replace('<%MENUADD%>', $menu_add, $Linha);
                    $retorno .= $Linha;
                }
            }else{
                $retorno = '<tr class="odd gradeX">
                               <td colspan="3">Não foi encontrado nenhum registro.</td>
                           <tr>';
            }
            return utf8_encode($retorno);
        }
    }
?>