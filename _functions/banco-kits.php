<?php
	class bancokit extends banco{
		
		#
		function ListaKits(){
			$Sql = "SELECT * FROM t_kit LIMIT 0, " . Limite;
			$result = parent::Execute($Sql);
			$Auxilio = parent::CarregaHtml('itens/lista-kit-itens');
			$Produtos = '<div class="row-fluid">';
			$cont = 0;
			while($rs = parent::ArrayData($result)){
				$Linha = $Auxilio;
				$Linha = str_replace('<%IDKIT%>', $rs['idkit'], $Linha);
				$Linha = str_replace('<%NOMEKIT%>', $rs['nome'], $Linha);
				$Linha = str_replace('<%PRECO%>', number_format($rs['valor_consumidor'], 2, ',', '.'), $Linha);
				$SqlImagem = "SELECT caminho FROM t_imagens_kit WHERE idkit = " . $rs['idkit'] . " AND ordem = 1";
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
		function BuscaKitPorId($idkit){
			$Sql = "SELECT K.* FROM t_kit K
					WHERE K.idkit = $idkit";
			$result = parent::Execute($Sql);
			$rs = parent::ArrayData($result);
			return $rs;
		}
		
		#
		function MontaFotosKit($idkit){
			$Sql = "SELECT * FROM t_imagens_kit WHERE idkit = $idkit ORDER BY ordem ASC";
			$result = parent::Execute($Sql);
			while($rs = parent::ArrayData($result)){
				$fotos .= '<li class="text-center" data-thumb="'.UrlFoto.$rs['caminho'].'">
								<img alt="" src="'.UrlFoto.$rs['caminho'].'" style="height: 375px;" />
                           </li>';		
			}
			$SqlProdutos = "SELECT * FROM t_kit_produtos WHERE idkit = $idkit ORDER BY idproduto ASC";
			$resultProdutos = parent::Execute($SqlProdutos);
			while($rs = parent::ArrayData($resultProdutos)){
				$SqlFotoProduto = "SELECT * FROM t_imagens_produto WHERE idproduto = " . $rs['idproduto'] . " ORDER BY ordem ASC LIMIT 0, 1";
				$resultFotoProduto = parent::Execute($SqlFotoProduto);
				$rsFotoProduto = parent::ArrayData($resultFotoProduto);
				$fotos .= '<li class="text-center" data-thumb="'.UrlFoto.$rsFotoProduto['caminho'].'">
								<img alt="" src="'.UrlFoto.$rsFotoProduto['caminho'].'" style="height: 375px;" />
                           </li>';
			}
			return $fotos;
		}
		
		#
		function BuscaProdutosKit($idkit){
			$Sql = "SELECT P.nome, P.marca FROM t_kit_produtos K 
					INNER JOIN t_produtos P ON K.idproduto = P.idproduto 
					WHERE K.idkit = $idkit";
			$result = parent::Execute($Sql);
			while($rs = parent::ArrayData($result)){
				$produtos .= $rs['nome'] . " - " . $rs['marca'] . "<br/>";
			}
			return utf8_encode($produtos);
		}
	}
?>