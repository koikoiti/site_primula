<?php
	class bancocheckout extends banco{
        
        #Monta Produtos
        function MontaProdutos($idvenda){
            $cont = 0;
            #Busca tipo valor
            $SqlValor = "SELECT V.valor FROM t_valor_profissional V 
                        INNER JOIN t_clientes C ON C.idtipoprofissional = V.idtipoprofissional 
                        INNER JOIN t_vendas X ON X.idcliente = C.idcliente 
                        WHERE X.idvenda = $idvenda";
            $resultValor = parent::Execute($SqlValor);
            $rsValor = parent::ArrayData($resultValor);
            $valor = $rsValor['valor'];
            
            #Busca os produtos
            $Sql = "SELECT P.nome, P.marca, X.quantidade, P.$valor FROM t_vendas V 
                    INNER JOIN t_vendas_produtos X ON X.idvenda = V.idvenda 
                    INNER JOIN t_produtos P ON P.idproduto = X.idproduto 
                    WHERE V.idvenda = $idvenda";
            $result = parent::Execute($Sql);
            while($rs = parent::ArrayData($result)){
                if($cont % 2 == 0){
                    $cor = 'floralwhite';
                }else{
                    $cor = 'aliceblue   ';
                }
                $retorno .= '<div class="col-sm-12"><label style="background-color: '.$cor.'; padding: 5px; border-radius: 4px;">'.$rs['nome'].' - '.$rs['marca'].'<br/>
                            Quantidade: '.$rs['quantidade'].' UN - Valor Unitário: R$ '.number_format($rs[$valor], 2, ',', '.').'<br/>
                            Sub-Total Produto: R$ '.number_format($rs[$valor]*$rs['quantidade'], 2, ',', '.').'</label></div><br />';
                $cont++;
            }
            return utf8_encode($retorno);
        }
        
        #Nome Cliente
        function BuscaNomeCliente($idvenda){
            $Sql = "SELECT C.nome FROM t_vendas V 
                    INNER JOIN t_clientes C ON C.idcliente = V.idcliente 
                    WHERE V.idvenda = $idvenda";
            $result = parent::Execute($Sql);
            $rs = parent::ArrayData($result);
            return $rs['nome'];
        }
        
        #Sub total produtos
        function totalProdutos($idvenda){
            #Busca tipo valor
            $SqlValor = "SELECT V.valor FROM t_valor_profissional V 
                        INNER JOIN t_clientes C ON C.idtipoprofissional = V.idtipoprofissional 
                        INNER JOIN t_vendas X ON X.idcliente = C.idcliente 
                        WHERE X.idvenda = $idvenda";
            $resultValor = parent::Execute($SqlValor);
            $rsValor = parent::ArrayData($resultValor);
            $valor = $rsValor['valor'];
            
            #Busca os produtos
            $Sql = "SELECT P.$valor, X.quantidade, X.desconto FROM t_vendas V 
                    INNER JOIN t_vendas_produtos X ON X.idvenda = V.idvenda 
                    INNER JOIN t_produtos P ON P.idproduto = X.idproduto 
                    WHERE V.idvenda = $idvenda";
            $result = parent::Execute($Sql);
            while($rs = parent::ArrayData($result)){
                $subtotal += $rs[$valor] * $rs['quantidade'];
            }
            return $subtotal;
        }
        
        #Tipo Frete
        function tipoFrete($idvenda){
            $Sql = "SELECT F.tipo FROM t_vendas V 
                    INNER JOIN fixo_tipo_frete F ON F.idtipofrete = V.idtipofrete 
                    WHERE V.idvenda = $idvenda";
            $result = parent::Execute($Sql);
            $rs = parent::ArrayData($result);
            return $rs['tipo'];
        }
        
        #Frete
        function valorFrete($idvenda){
            $Sql = "SELECT valor_frete FROM t_vendas WHERE idvenda = $idvenda";
            $result = parent::Execute($Sql);
            $rs = parent::ArrayData($result);
            return $rs['valor_frete'];
        }
        
        #Por Conta
        function porConta($idvenda){
            $Sql = "SELECT frete_porconta FROM t_vendas WHERE idvenda = $idvenda";
            $result = parent::Execute($Sql);
            $rs = parent::ArrayData($result);
            if($rs['frete_porconta'] == 1){
                return "Frete por Conta";
            }else{
                return 'Frete pago pelo Cliente';
            }
        }
    }
?>