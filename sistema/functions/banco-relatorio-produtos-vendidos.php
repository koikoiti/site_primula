<?php
    class bancorelatorioprodutosvendidos extends banco{
        
        function MontaRelatorio($dataIni, $dataFim){
            $Auxilio = parent::CarregaHtml('itens/relatorio-produtos-vendidos-itens');
            $where = '';
            if($dataIni){
                #$newdataIni = implode("-", array_reverse(explode("/", $dataIni)));
                $where .= " AND V.data >= '$dataIni 00:00:00'";
            }
            if($dataFim){
                #$newdataFim = implode("-", array_reverse(explode("/", $dataFim)));
                $where .= " AND V.data <= '$dataFim 23:59:59'";
            }
            
            $Sql = "SELECT *, SUM(P.quantidade) AS qtd_total FROM t_vendas V 
                    INNER JOIN t_vendas_produtos P ON V.idvenda = P.idvenda 
                    WHERE 1=1 $where 
                    GROUP BY produto_kit ORDER BY qtd_total DESC";
            
            $result = parent::Execute($Sql);
            while($rs = parent::ArrayData($result)){
                $Linha = $Auxilio;
                $auxPK = explode('_', $rs['produto_kit']);
                if($auxPK[0] == 'prod'){
                    $prod_kit = "Produto: ";
                    $Sql_nome = "SELECT nome, marca FROM t_produtos WHERE idproduto = " . $auxPK[1];
                }elseif($auxPK[0] == 'kit'){
                    $prod_kit = "Kit: ";
                    $Sql_nome = "SELECT nome, marca FROM t_kit WHERE idkit = " . $auxPK[1];
                }
                $result_nome = parent::Execute($Sql_nome);
                $rs_nome = parent::ArrayData($result_nome);
                $prod_kit .= $rs_nome['nome'] . " - " . $rs_nome['marca'];
                $Linha = str_replace("<%PRODUTO%>", $prod_kit, $Linha);
                $Linha = str_replace("<%QUANTIDADE%>", $rs['qtd_total'], $Linha);
                $Relatorio .= $Linha;
            }
            
            return utf8_encode($Relatorio);
        }
    }
?>