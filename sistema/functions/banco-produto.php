<?php
    class bancoproduto extends banco{
        
        function ListaProdutos($produto, $idcategoria, $marca, $modelo, $pagina){
            $Auxilio = parent::CarregaHtml("Produtos/itens/lista-produto-itens");
            $inicio = ($pagina * Limite) - Limite;
            $Sql = "SELECT P.*, C.nome AS categoria FROM t_produtos P 
                    INNER JOIN fixo_categorias_produto C ON C.idcategoria = P.idcategoria 
                    WHERE 1
                    ";
            if($produto != ''){
                $Sql .= " AND P.nome LIKE '%".utf8_decode($produto)."%'";
            }
            if($idcategoria != ''){
                $Sql .= " AND P.idcategoria = '$idcategoria'";
            }
            if($marca != ''){
                $Sql .= " AND P.marca LIKE '%".utf8_decode($marca)."%'";
            }
            if($modelo != ''){
                $Sql .= " AND P.modelo LIKE '%".utf8_decode(modelo)."%'";
            }
            $Sql .= "  ORDER BY P.nome ASC LIMIT $inicio, ".Limite;
            $result = parent::Execute($Sql);
            $num_rows = parent::Linha($result);
            if($num_rows){
                while($rs = parent::ArrayData($result)){
                    $Linha = $Auxilio;
                    $Linha = str_replace("<%ID%>", $rs['idproduto'], $Linha);
                    $Linha = str_replace("<%NOME%>", $rs['nome'], $Linha);
                    $Linha = str_replace("<%MARCA%>", $rs['marca'], $Linha);
                    $Linha = str_replace("<%MODELO%>", $rs['modelo'], $Linha);
                    $Linha = str_replace("<%CATEGORIA%>", $rs['categoria'], $Linha);
                    $Linha = str_replace("<%ESTOQUE%>", $rs['estoque'], $Linha);
                    if($rs['ativo'] == 1){
                        $Linha = str_replace("<%ATIVOINATIVO%>", 'Ativo', $Linha);
                        $Linha = str_replace("<%BOTAOAI%>", '<a href="javascript:void(0)" onclick="inativar('.$rs['idproduto'].', \''.$rs['nome'].'\')">Inativar</a>', $Linha);
                    }else{
                        $Linha = str_replace("<%ATIVOINATIVO%>", 'Inativo', $Linha);
                        $Linha = str_replace("<%BOTAOAI%>", '<a href="javascript:void(0)" onclick="ativar('.$rs['idproduto'].', \''.$rs['nome'].'\')">Ativar</a>', $Linha);
                    }
                    $Produtos .= $Linha;
                }
            }else{
                $Produtos = '<tr class="odd gradeX">
                               <td colspan="6">Não foram encontrados produtos cadastrados.</td>
                           <tr>';
            }
            return utf8_encode($Produtos);
        }
        
        function InsereProduto($cod_barras, $cod_fornecedor, $nome, $marca, $idcategoria, $estoque, $valor_unitario, $valor_profissional, $valor_consumidor, $descricao, $informacoes, $files){
            $Sql = "INSERT INTO t_produtos (cod_barras, cod_fornecedor, nome, marca, idcategoria, valor_unitario, valor_profissional, valor_consumidor, descricao, informacoes, estoque) 
                    VALUES ('$cod_barras', '$cod_fornecedor', '".ucwords($nome)."', '".ucwords($marca)."',  '$idcategoria', '$valor_unitario', '$valor_profissional', '$valor_consumidor', '".ucfirst($descricao)."', '".ucfirst($informacoes)."', '$estoque')";
            if(parent::Execute($Sql)){
                if($files){
                    $lastID = mysql_insert_id();
                    $this->InsereFotosInsert($lastID, $files);
                }
                return true;
            }else{
                parent::ChamaManutencao();
            } 
       }
       
        #Insere caminho das fotos no banco e copia para a pasta do produto
        function InsereFotosInsert($idproduto, $files){
            $cont = 1;
            $caminhoCriar = $_SERVER['DOCUMENT_ROOT'] . AuxCaminhoFotoProduto . $idproduto;
            $caminho = "arq/produtos/$idproduto";
            
			mkdir($caminhoCriar, 0755);
            foreach($files as $file){
                #Pega extensão da imagem
                preg_match("/\.(gif|png|jpg|jpeg){1}$/i", $file["name"], $ext);
                $caminhoMover = "/$idproduto - $cont" . "." . $ext[1];
                move_uploaded_file($file["tmp_name"], $caminhoCriar.$caminhoMover);
                $Sql = "INSERT INTO t_imagens_produto (idproduto, caminho, ordem) VALUES ('$idproduto', '".$caminho.$caminhoMover."', '$cont')";
                parent::Execute($Sql);
                $cont++;
            }
        }
       
       function AtualizaProduto($idproduto, $cod_barras, $cod_fornecedor, $nome, $marca, $idcategoria, $estoque, $valor_unitario, $valor_profissional, $valor_consumidor, $descricao, $informacoes, $files){
            $Sql = "UPDATE t_produtos SET cod_barras = '$cod_barras', cod_fornecedor = '$cod_fornecedor', nome = '".ucwords($nome)."', marca = '".ucwords($marca)."', idcategoria = '$idcategoria', valor_unitario = '$valor_unitario', valor_profissional = '$valor_profissional', valor_consumidor = '$valor_consumidor', descricao = '".ucfirst($descricao)."', informacoes = '".ucfirst($informacoes)."', estoque = '$estoque' WHERE idproduto = $idproduto";
            if(parent::Execute($Sql)){
                if($files){
                    $this->InsereFotosUpdate($idproduto, $files);
                }
                return true;
            }else{
                parent::ChamaManutencao();
            }
       }
       
       function InsereFotosUpdate($idproduto, $files){
            $order = 1;
            $caminhoCriar = $_SERVER['DOCUMENT_ROOT'] . AuxCaminhoFotoProduto . $idproduto;
			$caminho = "arq/produtos/$idproduto";
			
			#Pega a numeração da última imagem caso precise adicionar 1 novo
			$SqlIMG = "SELECT * FROM t_imagens_produto WHERE idproduto = $idproduto ORDER BY idimagem DESC LIMIT 0, 1";
			$resultIMG = parent::Execute($SqlIMG);
			$rsIMG = parent::ArrayData($resultIMG);
			$ultimo = $rsIMG['caminho'];
			$ultimo = explode('/', $ultimo);
            $ultimo = explode(' ', $ultimo[3]);
            $ultimo = explode('.', $ultimo[2]);
			$ultimo = $ultimo[0];
			
			foreach($files as $file){
				#Verifica file[name]. Se tiver %, update, senão, insert
				if($file['name'][0] == '%'){
					#UPDATE
					$SqlUpdate = "UPDATE t_imagens_produto SET ordem = $order WHERE idimagem = " . ltrim($file['name'], '%');
					parent::Execute($SqlUpdate);
				}else{
					#INSERT
					$ultimo++;
					#Pega extensão da imagem
					preg_match("/\.(gif|png|jpg|jpeg){1}$/i", $file["name"], $ext);
					$caminhoMover = "/$idproduto - $ultimo" . "." . $ext[1];
                    move_uploaded_file($file["tmp_name"], $caminhoCriar.$caminhoMover);
					$Sql = "INSERT INTO t_imagens_produto (idproduto, caminho, ordem) VALUES ('$idproduto', '".$caminho.$caminhoMover."', '$order')";
					parent::Execute($Sql);
				}
				$order++;
			}
       }
       
       function BuscaProdutoPorMarcaModelo($marca, $modelo){
        
       } 
           
       function SelectCategorias($idcategoria){
            $Sql = "SELECT * FROM fixo_categorias_produto ORDER BY nome";
			$select_categorias = "<select required class='form-control' name='categoria'>";
			$select_categorias .= "<option selected value=''>Selecione uma Categoria!</option>";
			$result = parent::Execute($Sql);
			if($result){
				while($rs = parent::ArrayData($result)){
					if($rs['idcategoria'] == $idcategoria){
						$select_categorias .= "<option selected value='".$rs['idcategoria']."'>".$rs['nome']."</option>";
					}else{
						$select_categorias .= "<option value='".$rs['idcategoria']."'>".$rs['nome']."</option>";
					}
				}
				$select_categorias .= "</select>";
				return $select_categorias;
			}else{
				return false;
			}
       }
       
        #Monta paginacao
        function MontaPaginacao($produto, $idcategoria, $marca, $modelo, $pagina){
            $totalPaginas = $this->TotalPaginas($produto, $idcategoria, $marca, $modelo, $pagina);
            $pag = '';
            if($produto || $idcategoria || $marca || $modelo){
                $url = "idcategoria=$idcategoria&produto=$produto&marca=$marca&modelo=$modelo";
            }
            $url .= "&page=";
            if($totalPaginas > 1){
                if($pagina == 1){
                    $pag = '<span class="page active">&laquo;</span>';
                    $pag .= '<span class="page active">1</span>';
                }else{
                    $pag .= '<a href="'.UrlPadrao.'lista-produto/?'.$url.($pagina-1).'" class="page">&laquo;</a>';
                    $pag .= '<a href="'.UrlPadrao.'lista-produto/?'.$url.'1" class="page">1</a>';
                }
                $pag .= '<span class="page">...</span>';
                
                #Monta a paginação do meio
				if($totalPaginas < QtdPag){
				    if($pagina <= $totalPaginas){
				        for($i = 2; $i <= $totalPaginas - 1; $i++){
				            if($i == $pagina){
        						$pag .= '<span class="page active">'.$i.'</span>'; 
        					}else{
        						$pag .= '<a href="'.UrlPadrao.'lista-produto/?'.$url.$i.'" class="page">'.$i.'</a>';	
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
    						$pag .= '<span class="page active">'.$i.'</span>'; 
    					}else{
    						if($i <= $totalPaginas){
    							$pag .= '<a href="'.UrlPadrao.'lista-produto/?'.$url.$i.'" class="page">'.$i.'</a>';
    						}
    					}
    				}
				}
                
                
                $pag .= '<span class="page">...</span>';
                if($pagina == $totalPaginas){
                    $pag .= '<span class="page active">'.$totalPaginas.'</span>';
                    $pag .= '<span class="page active">&raquo;</span>';
                }else{
                    $pag .= '<a href="'.UrlPadrao.'lista-produto/?'.$url.$totalPaginas.'" class="page">'.$totalPaginas.'</a>';
                    $pag .= '<a href="'.UrlPadrao.'lista-produto/?'.$url.($pagina+1).'"class="page">&raquo;</a>';
                }
                
                
                return $pag;
            }else{
                return '';
            }
        }
        
        #Total de paginas
        function TotalPaginas($produto, $idcategoria, $marca, $modelo, $pagina){
            $Sql = "SELECT P.*, C.nome AS categoria FROM t_produtos P
                    INNER JOIN fixo_categorias_produto C ON C.idcategoria = P.idcategoria 
                    WHERE 1
                    ";
            if($produto != ''){
                $Sql .= " AND P.nome LIKE '%".utf8_decode($produto)."%'";
            }
            if($idcategoria != ''){
                $Sql .= " AND P.idcategoria = '$idcategoria'";
            }
            if($marca != ''){
                $Sql .= " AND P.marca LIKE '%".utf8_decode($marca)."%'";
            }
            if($modelo != ''){
                $Sql .= " AND P.modelo LIKE '%".utf8_decode(modelo)."%'";
            }
            $result = parent::Execute($Sql);
			$num_rows = parent::Linha($result);
			$totalPag = ceil($num_rows/Limite);
			return $totalPag;
        }
        
        #Monta select busca Categorias
        function MontaSelectBuscaCategorias($idcategoria){
			$Sql = "SELECT * FROM fixo_categorias_produto ORDER BY nome";
			$select_categorias = "<select id='categoria' style='float: left; width: 15%;' class='form-control' name='categoria'>";
			$select_categorias .= "<option selected value=''>Selecione uma Categoria!</option>";
			$result = parent::Execute($Sql);
			if($result){
				while($rs = parent::ArrayData($result)){
					if($rs['idcategoria'] == $idcategoria){
						$select_categorias .= "<option selected value='".$rs['idcategoria']."'>".$rs['nome']."</option>";
					}else{
						$select_categorias .= "<option value='".$rs['idcategoria']."'>".$rs['nome']."</option>";
					}
				}
				$select_categorias .= "</select>";
				return $select_categorias;
			}else{
				return false;
			}
        }
        
        #Busca Produto por ID
        function BuscaProdutoPorId($idproduto){
            $Sql = "SELECT * FROM t_produtos WHERE idproduto = '$idproduto'";
            $result = parent::Execute($Sql);
            return $result;
        }
        
        #Monta imagens editar
        function MontaImagens($idproduto){
            $Sql = "SELECT * FROM t_imagens_produto WHERE idproduto = $idproduto ORDER BY ordem";
            $result = parent::Execute($Sql);
            $num_rows = parent::Linha($result);
            if($num_rows){
                while($rs = parent::ArrayData($result)){
                    $imagens .= "<div id='i=%".$rs['idimagem']."' class='colFoto span_1_of_5'><div style='height: 12.6em;'><img class='img-responsive' style='max-height: 100%;display: block; margin-left: auto; margin-right: auto;' src=\"".UrlFoto.$rs['caminho']."\" data-file='".$rs['idimagem']."'/></div><button type='button' onclick='removeFotoProduto(\"".$rs['idimagem']."\", \"".$idproduto."\")' style='width: 100%; padding: 1px; margin: 1px 0;' class='btn btn-danger'>Remover</button></div>";
                }
            }
            return $imagens;
        }
        
        #Monta ordem hidden
		function MontaHidden($idproduto){
			$Sql = "SELECT * FROM t_imagens_produto WHERE idproduto = $idproduto ORDER BY ordem";
			$result = parent::Execute($Sql);
			$num_rows = parent::Linha($result);
			if($num_rows){
				while($rs = parent::ArrayData($result)){
					$hidden .= "i[]=%".$rs['idimagem'].'&';
				}
			}
			$hidden = rtrim($hidden, '&');
			return $hidden;
		}
        
        #Ativar Produto
        function Ativar($idproduto){
            $Sql = "UPDATE t_produtos SET ativo = 1 WHERE idproduto = $idproduto";
            parent::Execute($Sql);
            parent::RedirecionaPara('produto/editar/'.$idproduto);
        }
        
        #Inativar Produto
        function Inativar($idproduto){
            $Sql = "UPDATE t_produtos SET ativo = 0 WHERE idproduto = $idproduto";
            parent::Execute($Sql);
            $SqlVerifica = "SELECT * FROM t_destaques WHERE idproduto = $idproduto";
            $resultVerifica = parent::Execute($SqlVerifica);
            $linhaVerifica = parent::Linha($resultVerifica);
            if($linhaVerifica){
                $SqlTiraDestaque = "DELETE FROM t_destaques WHERE idproduto = $idproduto";
                parent::Execute($SqlTiraDestaque);
            }
            parent::RedirecionaPara('produto/editar/'.$idproduto);
        }
        
        #Ficha produto
        function VisualizaFichaProduto($idproduto){
            $Sql = "SELECT P.*, C.nome AS categoria, X.* FROM t_produtos P 
                    INNER JOIN fixo_categorias_produto C ON P.idcategoria = C.idcategoria
                    INNER JOIN t_imagens_produto X ON X.idproduto = P.idproduto
                    WHERE P.idproduto = $idproduto AND X.ordem = 1";
            $result = parent::Execute($Sql);
            $rs = parent::ArrayData($result);
            
            #Inicia mPDF
            require_once('app/mpdf60/mpdf.php');
            $mpdf = new mPDF('utf-8', 'A4', '', '', 8, 8, 0, 9);
                        
            #HTML Auxilio
            $Auxilio = utf8_encode(parent::CarregaHtml('Produtos/ficha'));
            
            #Replaces
            $data = 'Curitiba, ' . date('d/m/Y');
            $logo = "<img style='height: 80px;' src='".UrlPdf."html/images/logo.png"."' />";
            $Auxilio = str_replace('<%DATA%>', $data, $Auxilio);
            $Auxilio = str_replace('<%LOGO%>', $logo, $Auxilio);
            
            $Auxilio = str_replace('<%CODIGO%>', $rs['cod_barras'], $Auxilio);
            $Auxilio = str_replace('<%MARCA%>', utf8_encode($rs['marca']), $Auxilio);
            $Auxilio = str_replace('<%NOME%>', utf8_encode($rs['nome']), $Auxilio);
            $Auxilio = str_replace('<%CATEGORIA%>', utf8_encode($rs['categoria']), $Auxilio);
            $Auxilio = str_replace('<%VALORPROFISSIONAL%>', 'R$ ' . number_format($rs['valor_profissional'], 2, ',', '.'), $Auxilio);
            $Auxilio = str_replace('<%VALORCONSUMIDOR%>', 'R$ ' . number_format($rs['valor_consumidor'], 2, ',', '.'), $Auxilio);
            $Auxilio = str_replace('<%ESTOQUE%>', $rs['estoque'] . ' UN', $Auxilio);
            $Auxilio = str_replace('<%DESCRICAO%>', utf8_encode($rs['descricao']), $Auxilio);
            $Auxilio = str_replace('<%INFORMACOES%>', utf8_encode($rs['informacoes']), $Auxilio);
            
            #Imagem
            $Imagem = "<img style='max-height: 200px;' src='".UrlPdf.$rs['caminho']."'/>";
            $Auxilio = str_replace('<%IMG%>', $Imagem, $Auxilio);
            
            $mpdf->WriteHTML($Auxilio);
            $mpdf->SetFooter(' ');
            $mpdf->Output();
            exit;
        }
    }
?>