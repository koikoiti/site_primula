<?php
	class bancoproduto extends banco{
		
		#
		function ListaProdutos(){
			$Sql = "SELECT * FROM t_produtos LIMIT 0, " . Limite;
			$result = parent::Execute($Sql);
			$Auxilio = parent::CarregaHtml('itens/lista-produto-itens');
			$Produtos = '<div class="row-fluid">';
			$cont = 0;
			while($rs = parent::ArrayData($result)){
				$Linha = $Auxilio;
				$Linha = str_replace('<%IDPRODUTO%>', $rs['idproduto'], $Linha);
				$Linha = str_replace('<%NOMEPRODUTO%>', $rs['nome'], $Linha);
				$Linha = str_replace('<%PRECO%>', number_format($rs['valor_consumidor'], 2, ',', '.'), $Linha);
				$Linha = str_replace('<%MARCA%>', $rs['marca'], $Linha);
				$SqlImagem = "SELECT caminho FROM t_imagens_produto WHERE idproduto = " . $rs['idproduto'] . " AND ordem = 1";
				$resultImagem = parent::Execute($SqlImagem);
				$rsImagem = parent::ArrayData($resultImagem);
				$Linha = str_replace('<%CAMINHOIMAGEM%>', UrlFoto.$rsImagem['caminho'], $Linha);
				$Produtos .= $Linha;
				if($cont == 2){
					$Produtos .= '</div><div class="row-fluid">';
					$cont = 0;
				}else{
					$cont++;
				}
				
			}
			$Produtos = rtrim($Produtos, '<div class="row-fluid">');
			return utf8_encode($Produtos);
		}
		
		#
		function ListaCategorias(){
			$Sql = "SELECT * FROM fixo_categorias_produto ORDER BY nome ASC";
			$result = parent::Execute($Sql);
			while($rs = parent::ArrayData($result)){
				$Categorias .= '<li><a href="'.UrlPadrao.'lista-produtos/categoria/'.$rs['idcategoria'].'/'.$rs['nome'].'">'.$rs['nome'].'</a></li>';
			}
			return utf8_encode($Categorias);
		}
		
		#
		function BuscaProdutoPorId($idproduto){
			$Sql = "SELECT P.*, C.nome AS categoria FROM t_produtos P 
					INNER JOIN fixo_categorias_produto C ON P.idcategoria = C.idcategoria 
					WHERE P.idproduto = $idproduto";
			$result = parent::Execute($Sql);
			$rs = parent::ArrayData($result);
			return $rs;
		}
		
		#
		function MontaFotosProdutoUnico($idproduto){
			$Sql = "SELECT * FROM t_imagens_produto WHERE idproduto = $idproduto ORDER BY ordem ASC";
			$result = parent::Execute($Sql);
			while($rs = parent::ArrayData($result)){
				$fotos .= '<li class="text-center" data-thumb="'.UrlFoto.$rs['caminho'].'">
								<img alt="" src="'.UrlFoto.$rs['caminho'].'" style="height: 375px;" />
                           </li>';
			}
			return $fotos;
		}
		
		#
		function MontaSemelhantes($idcategoria){
			$Sql = "SELECT P.*, C.nome AS categoria, C.idcategoria, I.caminho FROM t_produtos P 
					INNER JOIN fixo_categorias_produto C ON P.idcategoria = C.idcategoria 
					INNER JOIN t_imagens_produto I ON I.idproduto = P.idproduto
					WHERE P.idcategoria = $idcategoria AND I.ordem = 1 ORDER BY RAND() LIMIT 3";
			$result = parent::Execute($Sql);
			$Auxilio = utf8_decode(parent::CarregaHtml('itens/produto-semelhante'));
			while($rs = parent::ArrayData($result)){
				$Linha = $Auxilio;
				$Linha = str_replace('<%IDPRODUTO%>', $rs['idproduto'], $Linha);
				$Linha = str_replace('<%NOMEPRODUTO%>', $rs['nome'], $Linha);
				$Linha = str_replace('<%PRECO%>', number_format($rs['valor_consumidor'], 2, ',', '.'), $Linha);
				$Linha = str_replace('<%MARCA%>', $rs['marca'], $Linha);
				$Linha = str_replace('<%CAMINHOIMAGEM%>', UrlFoto.$rs['caminho'], $Linha);
				$Semelhantes .= $Linha;
			}
			return utf8_encode($Semelhantes);
		}
	}
?>