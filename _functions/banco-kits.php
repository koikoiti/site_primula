<?php
	class bancokit extends banco{
		
		#
		function ListaKits($pagina, $order){
			$inicio = ($pagina * Limite) - Limite;
			if($order == 'a-z'){
				$order = " ORDER BY nome ASC ";
			}elseif($order == 'z-a'){
				$order = " ORDER BY nome DESC ";
			}else{
				$order = " ORDER BY idkit DESC ";
			}
			
			$Sql = "SELECT * FROM t_kit $order LIMIT $inicio, " . Limite;
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
				$produtos .= $rs['nome'] . "<br/>";
			}
			return utf8_encode($produtos);
		}
		
		#Monta paginacao
		function MontaPaginacao($pagina, $order){
			$totalPaginas = $this->TotalPaginas($idcategoria);
			$pag = '';
			if($idcategoria || $order){
				$url = "&order=$order&";
			}else{
				$url = '';
			}
			if($totalPaginas > 1){
				if($pagina == 1){
					$pag = '<li class="disabled"><a>&laquo;</a></li>';
					#$pag .= '<li class="active"><a>1</a></li>';
				}else{
					$pag .= '<li><a href="'.UrlPadrao.'lista-kits/?'.$url.'pagina='.($pagina-1).'">&laquo;</a></li>';
					$pag .= '<li><a href="'.UrlPadrao.'lista-kits/?'.$url.'pagina=1">1</a></li>';
				}
				$pag .= '<li class="disabled"><a>...</a></li>';
		
				#Monta a paginação do meio
				if($totalPaginas < QtdPag){
					if($pagina <= $totalPaginas){
						for($i = 1; $i <= $totalPaginas; $i++){
							if($i == $pagina){
								$pag .= '<li class="active"><a>'.$i.'</a></li>';
							}else{
								$pag .= '<li><a href="'.UrlPadrao.'lista-kits/?'.$url.'pagina='.$i.'">'.$i.'</a></li>';
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
							$pag .= '<li class="active"><a>'.$i.'</a></li>';
						}else{
							if($i <= $totalPaginas){
								$pag .= '<li><a href="'.UrlPadrao.'lista-kits/?'.$url.'pagina='.$i.'">'.$i.'</a></li>';
							}
						}
					}
				}
		
				$pag .= '<li class="disabled"><a>...</a></li>';
				if($pagina == $totalPaginas){
					#$pag .= '<li class="active"><a>'.$totalPaginas.'</a></li>';
					$pag .= '<li class="disabled"><a>&raquo;</a></li>';
				}else{
					$pag .= '<li><a href="'.UrlPadrao.'lista-kits/?'.$url.'pagina='.$totalPaginas.'">'.$totalPaginas.'</a></li>';
					$pag .= '<li><a href="'.UrlPadrao.'lista-kits/?'.$url.'pagina='.($pagina+1).'">&raquo;</a></li>';
				}
		
				return $pag;
			}else{
				return '';
			}
		}/*-------------------------------------------------------------------------------------------------------------*/
		
		#Total de paginas
		function TotalPaginas(){
			$Sql = "SELECT * FROM t_kit";
			$result = parent::Execute($Sql);
			$num_rows = parent::Linha($result);
			$totalPag = ceil($num_rows/Limite);
			return $totalPag;
		}/*-------------------------------------------------------------------------------------------------------------*/
	}
?>