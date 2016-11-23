<?php
	class bancoproduto extends banco{
		
		#
		function ListaProdutos($pagina, $idcategoria, $order){
			$inicio = ($pagina * Limite) - Limite;
			if($idcategoria){
				$where .= " AND C.idcategoria = $idcategoria";
			}
			if($order == 'a-z'){
				$order = " ORDER BY P.nome ASC ";
			}elseif($order == 'z-a'){
				$order = " ORDER BY P.nome DESC ";
			}else{
				$order = " ORDER BY P.data_cadastro DESC ";
			}
			$where .= " AND ocultar = 0";
			$Sql = "SELECT P.* FROM t_produtos P 
					INNER JOIN fixo_categorias_produto C ON C.idcategoria = P.idcategoria 
					WHERE 1 $where $order LIMIT $inicio, " . Limite;
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
								<img class="img-zoom" alt="" src="'.UrlFoto.$rs['caminho'].'" data-zoom-image="'.UrlFoto.$rs['caminho'].'" style="height: 375px;" />
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
		
		#
		function BuscaCategoriaPorId($idcategoria){
			$Sql = "SELECT nome FROM fixo_categorias_produto WHERE idcategoria = $idcategoria";
			$result = parent::Execute($Sql);
			$rs = parent::ArrayData($result);
			return utf8_encode($rs['nome']);
		}
		
		#Monta paginacao
		function MontaPaginacao($pagina, $idcategoria, $order){
			$totalPaginas = $this->TotalPaginas($idcategoria);
			$pag = '';
			if($idcategoria || $order){
				$url = "&order=$order&idcategoria=$idcategoria&";
			}else{
				$url = '';
			}
			if($totalPaginas > 1){
				if($pagina == 1){
					$pag = '<li class="disabled"><a>&laquo;</a></li>';
					#$pag .= '<li class="active"><a>1</a></li>';
				}else{
					$pag .= '<li><a href="'.UrlPadrao.'lista-produtos/?'.$url.'pagina='.($pagina-1).'">&laquo;</a></li>';
					$pag .= '<li><a href="'.UrlPadrao.'lista-produtos/?'.$url.'pagina=1">1</a></li>';
				}
				$pag .= '<li class="disabled"><a>...</a></li>';
		
				#Monta a paginação do meio
				if($totalPaginas < QtdPag){
					if($pagina <= $totalPaginas){
						for($i = 1; $i <= $totalPaginas; $i++){
							if($i == $pagina){
								$pag .= '<li class="active"><a>'.$i.'</a></li>';
							}else{
								$pag .= '<li><a href="'.UrlPadrao.'lista-produtos/?'.$url.'pagina='.$i.'">'.$i.'</a></li>';
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
								$pag .= '<li><a href="'.UrlPadrao.'lista-produtos/?'.$url.'pagina='.$i.'">'.$i.'</a></li>';
							}
						}
					}
				}
		
				$pag .= '<li class="disabled"><a>...</a></li>';
				if($pagina == $totalPaginas){
					#$pag .= '<li class="active"><a>'.$totalPaginas.'</a></li>';
					$pag .= '<li class="disabled"><a>&raquo;</a></li>';
				}else{
					$pag .= '<li><a href="'.UrlPadrao.'lista-produtos/?'.$url.'pagina='.$totalPaginas.'">'.$totalPaginas.'</a></li>';
					$pag .= '<li><a href="'.UrlPadrao.'lista-produtos/?'.$url.'pagina='.($pagina+1).'">&raquo;</a></li>';
				}
		
				return $pag;
			}else{
				return '';
			}
		}/*-------------------------------------------------------------------------------------------------------------*/
		
		#Total de paginas
		function TotalPaginas($idcategoria){
			if($idcategoria){
				$where .= " AND P.idcategoria = $idcategoria";
			}
			$where .= " AND ocultar = 0";
			$Sql = "SELECT P.* FROM t_produtos P
					INNER JOIN fixo_categorias_produto C ON C.idcategoria = P.idcategoria
					WHERE 1 $where
					";
			$result = parent::Execute($Sql);
			$num_rows = parent::Linha($result);
			$totalPag = ceil($num_rows/Limite);
			return $totalPag;
		}/*-------------------------------------------------------------------------------------------------------------*/
	}
?>