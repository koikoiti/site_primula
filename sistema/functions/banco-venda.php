<?php
    class bancovenda extends banco{
        
        #Tipo frete
        function SelectTipoFrete($idtipofrete){
            $Sql = "SELECT * FROM fixo_tipo_frete ORDER BY tipo ASC";
            $select_frete = "<select required class='form-control' name='tipofrete'>";
			$select_frete .= "<option selected value=''>Tipo do Frete</option>";
			$result = parent::Execute($Sql);
			if($result){
				while($rs = parent::ArrayData($result)){
					if($rs['idtipofrete'] == $idtipofrete){
						$select_frete .= "<option selected value='".$rs['idtipofrete']."'>".$rs['tipo']."</option>";
					}else{
						$select_frete .= "<option value='".$rs['idtipofrete']."'>".$rs['tipo']."</option>";
					}
				}
				$select_frete .= "</select>";
				return utf8_encode($select_frete);
			}else{
				return false;
			}
        }
        
        #Lista Vendas
        function ListaVendas(){
            $Auxilio = parent::CarregaHtml('Vendas/itens/lista-venda-itens');
            $Sql = "SELECT V.*, C.* FROM t_vendas V 
                    INNER JOIN t_clientes C ON V.idcliente = C.idcliente
                    ";
            $result = parent::Execute($Sql);
            $linha = parent::Linha($result);
            if($linha){
                while($rs = parent::ArrayData($result)){
                    $Linha = $Auxilio;
                    $valorTotal = $this->valorTotalVenda($rs['idvenda'], $rs['valor_frete'], $rs['frete_porconta'], $rs['idtipoprofissional']);
                    $Linha = str_replace('<%ID%>', str_pad($rs['idvenda'], 5, "0", STR_PAD_LEFT), $Linha);
                    $Linha = str_replace('<%DATA%>', date("d/m/Y H:i", strtotime($rs['data'])), $Linha);
                    $Linha = str_replace('<%CLIENTE%>', $rs['nome'], $Linha);
                    $Linha = str_replace('<%NF%>', $rs['nf'], $Linha);
                    $Linha = str_replace('<%VALOR%>', $valorTotal, $Linha);
                    $Linha = str_replace('<%VENDIDOPOR%>', parent::BuscaUsuarioPorId($rs['idusuario']), $Linha);
                    if($rs['orcamento'] == 1){
                        $auxVO = 'Orçamento';
                        $editar = '<a href="<%URLPADRAO%>venda/editar/<%ID%>">Editar</a>';
                    }else{
                        $auxVO = 'Venda';
                        $editar = '';
                    }
                    $Linha = str_replace('<%VENDAORCAMENTO%>', $auxVO, $Linha);
                    $Linha = str_replace('<%EDITAR%>', $editar, $Linha);
                    $Vendas .= $Linha;
                }
            }else{
                $Vendas = '<tr class="odd gradeX">
                                <td colspan="7">Não foi encontrada nenhuma venda.</td>
                             <tr>';
            }
            return utf8_encode($Vendas);
        }
        
        #Calcula valor total da venda
        function valorTotalVenda($idvenda, $frete, $porConta, $idtipoprofissional){
            $SqlValor = "SELECT valor FROM t_valor_profissional WHERE idtipoprofissional = $idtipoprofissional";
            $resultValor = parent::Execute($SqlValor);
            $rsValor = parent::ArrayData($resultValor);
            $Sql = "SELECT * FROM t_vendas_produtos V 
                    INNER JOIN t_produtos P ON P.idproduto = V.idproduto 
                    WHERE V.idvenda = $idvenda";
            $result = parent::Execute($Sql);
            while($rs = parent::ArrayData($result)){
                $produtos += ($rs[$rsValor['valor']] - $rs['desconto']) * $rs['quantidade'];
            }
            if($porConta == 1){
                $produtos -= $frete;
            }
            return "R$ " . number_format($produtos, 2, ',', '.');
        }
        
        #Insere Venda/Orçamento
        function InsereOrcamento($idcliente, $tipoFrete, $valorFrete, $fretePorConta, $arrProdutos, $arrQuantidade, $arrDesconto, $arrBrinde, $orcamento){
            if($fretePorConta){
                $fretePorConta = 1;
            }else{
                $fretePorConta = 0;
            }
            $Sql = "INSERT INTO t_vendas (idcliente, data, idtipofrete, valor_frete, frete_porconta, orcamento, idusuario) VALUES ('$idcliente', '".date("Y-m-d H:i:s")."', '$tipoFrete', '$valorFrete', '$fretePorConta', '$orcamento', '".$_SESSION['idusuario']."')";
            parent::Execute($Sql);
            $lastID = mysql_insert_id();
            
            #Insere produtos
            foreach($arrProdutos as $key => $value){
                if($arrBrinde[$key]){
                    $brinde = 1;
                }else{
                    $brinde = 0;
                }
                $SqlProdutos = "INSERT INTO t_vendas_produtos (idvenda, produto_kit, quantidade, desconto_valor, brinde) VALUES ('$lastID', '$value', '{$arrQuantidade[$key]}', '{$arrDesconto[$key]}', '$brinde')";
                parent::Execute($SqlProdutos);
            }
            
            return $lastID;
        }
    }
?>