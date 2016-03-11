<?php
	class bancoinicio extends banco{
		
		#Destaques
        function MontaDestaques(){
            $Auxilio = parent::CarregaHtml('itens/inicio-destaque-itens');
            $SqlDestaques = "SELECT * FROM t_destaques ORDER BY RAND() LIMIT 8";
            $resultDestaques = parent::Execute($SqlDestaques);
            $cont = 0;
            while($rsDestaques = mysql_fetch_array($resultDestaques, MYSQL_ASSOC)){
                $Linha = $Auxilio;
                
                #Dados imovel
                $Sql = "SELECT P.*, C.nome AS categoria FROM t_produtos P 
                        INNER JOIN fixo_categorias_produto C ON C.idcategoria = P.idcategoria 
                        WHERE P.idproduto = " . $rsDestaques['idproduto'];
                $result = parent::Execute($Sql);
                $rs = parent::ArrayData($result);
                 
                #Foto
                $SqlFoto = "SELECT * FROM t_imagens_produto WHERE idproduto = " . $rs['idproduto'] . " ORDER BY caminho ASC";
                $resultFoto = parent::Execute($SqlFoto);
                $rsFoto = parent::ArrayData($resultFoto);
                $Linha = str_replace('<%IDPRODUTO%>', $rs['idproduto'], $Linha);
                $Linha = str_replace('<%CAMINHO%>', UrlFoto.$rsFoto['caminho'], $Linha);
                $Linha = str_replace('<%NOME%>', $rs['nome'], $Linha);
                $Linha = str_replace('<%CATEGORIA%>', $rs['categoria'], $Linha);
                $Linha = str_replace('<%MARCA%>', $rs['marca'], $Linha);
                
                #Verifica linha
                if($cont == 0){
                    $retorno .= '<div class="row-fluid">';
                }
                if($cont == 4){
                    $retorno .= '</div>
                                <div class="row-fluid">';
                }
                $retorno .= $Linha;
                $cont++;
            }
            $retorno .= '</div>';
            
            return utf8_encode($retorno);
        }
		
        #Últimos Adicionados
		function MontaUltimos(){
			$Auxilio = parent::CarregaHtml('itens/inicio-destaque-itens');
			$SqlDestaques = "SELECT idproduto FROM t_produtos ORDER BY idproduto DESC LIMIT 8";
			$resultDestaques = parent::Execute($SqlDestaques);
			$cont = 0;
			while($rsDestaques = mysql_fetch_array($resultDestaques, MYSQL_ASSOC)){
				$Linha = $Auxilio;
			
				#Dados imovel
				$Sql = "SELECT P.*, C.nome AS categoria FROM t_produtos P
                        INNER JOIN fixo_categorias_produto C ON C.idcategoria = P.idcategoria
                        WHERE P.idproduto = " . $rsDestaques['idproduto'];
				$result = parent::Execute($Sql);
				$rs = parent::ArrayData($result);
				 
				#Foto
				$SqlFoto = "SELECT * FROM t_imagens_produto WHERE idproduto = " . $rs['idproduto'] . " ORDER BY caminho ASC";
				$resultFoto = parent::Execute($SqlFoto);
				$rsFoto = parent::ArrayData($resultFoto);
				$Linha = str_replace('<%IDPRODUTO%>', $rs['idproduto'], $Linha);
				$Linha = str_replace('<%CAMINHO%>', UrlFoto.$rsFoto['caminho'], $Linha);
				$Linha = str_replace('<%NOME%>', $rs['nome'], $Linha);
				$Linha = str_replace('<%CATEGORIA%>', $rs['categoria'], $Linha);
				$Linha = str_replace('<%MARCA%>', $rs['marca'], $Linha);
			
				#Verifica linha
				if($cont == 0){
					$retorno .= '<div class="row-fluid">';
				}
				if($cont == 4){
					$retorno .= '</div>
                                <div class="row-fluid">';
				}
				$retorno .= $Linha;
				$cont++;
			}
			$retorno .= '</div>';
			
			return utf8_encode($retorno);
		}
	}
?>