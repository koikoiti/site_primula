<?php
    class bancoentradaproduto extends banco{
        
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
        
        #Arruma Estoque Diminui @TODO
        function arrumaEstoqueDiminui($identrada){
        	$Sql = "SELECT * FROM t_vendas_produtos WHERE idvenda = $idvenda";
        	$result = parent::Execute($Sql);
        	while($rs = parent::ArrayData($result)){
        		#Verifica se é kit ou produto
        		$auxPK = explode('_', $rs['produto_kit']);
        		if($auxPK[0] == 'prod'){
        			$idproduto = $auxPK[1];
        			$SqlProduto = "UPDATE t_produtos SET estoque = estoque - ".$rs['quantidade'] . " WHERE idproduto = $idproduto";
        			parent::Execute($SqlProduto);
        		}elseif($auxPK[0] == 'kit'){
        			$idkit = $auxPK[1];
        			$SqlKit = "UPDATE t_kit SET estoque = estoque - ".$rs['quantidade']. " WHERE idkit = $idkit";
        			parent::Execute($SqlKit);
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
                    $Linha = str_replace('<%NF%>', $rs['nf'], $Linha);
                    $Linha = str_replace('<%FORNECEDOR%>', $rs['fornecedor'], $Linha);
                    $Linha = str_replace('<%VALOR%>', number_format($rs['valor'], 2, ',', '.'), $Linha);
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