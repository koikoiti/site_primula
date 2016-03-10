<?php
    class bancoentradaproduto extends banco{
        
        #Insere Entrada
        function InsereEntrada($fornecedor, $nf, $valor, $frete, $arrProdutos, $arrQuantidade, $arrLote, $arrVencimento){
            $Sql = "INSERT INTO t_entrada (fornecedor, nf, valor) VALUES ('$fornecedor', '$nf', '$valor')";
            parent::Execute($Sql);
            $lastID = mysql_insert_id();
            
            #Insere os produtos da nf de entrada e adiciona no estoque
            foreach($arrProdutos as $key => $value){
                $SqlEntrada = "INSERT INTO t_entrada_produtos (identrada, idproduto, quantidade, lote, validade) VALUES ('$lastID', '$value', '{$arrQuantidade[$key]}', '{$arrLote[$key]}', '{$arrVencimento[$key]}')";
                parent::Execute($SqlEntrada);
                
                $SqlEstoque = "UPDATE t_produtos SET estoque = estoque + {$arrQuantidade[$key]} WHERE idproduto = $value";
                parent::Execute($SqlEstoque);
            }
            parent::RedirecionaPara('lista-entrada-produto');
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