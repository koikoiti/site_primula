<?php
    class bancokit extends banco{
        
        #Visualiza Kit
        function VisualizaFichaKit($idkit){
            $Sql = "SELECT K.*, X.* FROM t_kit K 
                    INNER JOIN t_imagens_kit X ON X.idkit = K.idkit
                    WHERE K.idkit = $idkit AND X.ordem = 1";
            $result = parent::Execute($Sql);
            $rs = parent::ArrayData($result);
            
            #Inicia mPDF
            require_once('app/mpdf60/mpdf.php');
            $mpdf = new mPDF('utf-8', 'A4', '', '', 8, 8, 0, 9);
                        
            #HTML Auxilio
            $Auxilio = utf8_encode(parent::CarregaHtml('Produtos/ficha-kit'));
            
            #Replaces
            $data = 'Curitiba, ' . date('d/m/Y');
            $logo = "<img style='height: 80px;' src='".UrlPdf."html/images/logo.png"."' />";
            $Auxilio = str_replace('<%DATA%>', $data, $Auxilio);
            $Auxilio = str_replace('<%LOGO%>', $logo, $Auxilio);
            $Auxilio = str_replace('<%CODIGO%>', $rs['codigo'], $Auxilio);
            $Auxilio = str_replace('<%NOME%>', utf8_encode($rs['nome']), $Auxilio);
            $Auxilio = str_replace('<%VALORPROFISSIONAL%>', 'R$ ' . number_format($rs['valor_profissional'], 2, ',', '.'), $Auxilio);
            $Auxilio = str_replace('<%VALORCONSUMIDOR%>', 'R$ ' . number_format($rs['valor_consumidor'], 2, ',', '.'), $Auxilio);
            $Auxilio = str_replace('<%ESTOQUE%>', $rs['estoque'] . ' UN', $Auxilio);
            $Auxilio = str_replace('<%DESCRICAO%>', utf8_encode($rs['descricao']), $Auxilio);
            $Auxilio = str_replace('<%INFORMACOES%>', utf8_encode($rs['informacoes']), $Auxilio);
            
            #Produtos
            $produtos = '<br/>';
            $SqlProdutos = "SELECT P.*, K.* FROM t_kit_produtos K 
                                    INNER JOIN t_produtos P ON P.idproduto = K.idproduto 
                                    WHERE K.idkit = " . $rs['idkit'];
            $resultProdutos = parent::Execute($SqlProdutos);
            while($rsProdutos = parent::ArrayData($resultProdutos)){
                $produtos .= "» <span style='font-size: 12px;'>{$rsProdutos['nome']} - {$rsProdutos['marca']} - Qtd: {$rsProdutos['quantidade']}</span><br/>";
            }
            $Auxilio = str_replace('<%PRODUTOS%>', utf8_encode($produtos), $Auxilio);
            
            #Imagem
            $Imagem = "<img style='max-height: 200px;' src='".UrlPdf.$rs['caminho']."'/>";
            $Auxilio = str_replace('<%IMG%>', $Imagem, $Auxilio);
            
            $mpdf->WriteHTML($Auxilio);
            $mpdf->SetFooter(' ');
            $mpdf->Output();
            exit;
        }
        
        #Hidden
        function MontaHidden($idkit){
            $Sql = "SELECT * FROM t_imagens_kit WHERE idkit = $idkit ORDER BY ordem";
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
        
        #Busca kit por id
        function BuscaKitPorId($idkit){
            $Sql = "SELECT * FROM t_kit WHERE idkit = '$idkit'";
            $result = parent::Execute($Sql);
            return $result;
        }
        
        #Monta produtos kit
        function MontaProdutosKit($idkit){
            $Sql = "SELECT K.*, P.*, I.* FROM t_kit_produtos K 
                    INNER JOIN t_produtos P ON K.idproduto = P.idproduto 
                    INNER JOIN t_imagens_produto I ON I.idproduto = P.idproduto 
                    WHERE K.idkit = $idkit AND I.ordem = 1";
            $result = parent::Execute($Sql);
            while($rs = parent::ArrayData($result)){
                $retorno .= '<div id="divProduto'.$rs['idkitproduto'].'" class="novo-produto">
                                <img src="'.UrlFoto.$rs['caminho'].'" style="width: 100px; height: 100px;">
                                <div class="col-md-3">
                                    *Produto: <input required="" type="text" class="form-control ui-autocomplete-input" autocomplete="off" value="'.utf8_encode($rs['nome']).'">
                                </div>
                                <div class="col-md-1">
                                    *Quantidade: <input required="" type="number" class="form-control" name="quantidade[]" value="'.$rs['quantidade'].'">
                                </div>
                                <div class="col-sm-1"><br>
                                    <button onclick="menos('.$rs['idkitproduto'].', \'divProduto\')" type="button" class="btn btn-danger">-</button>
                                </div><input type="hidden" name="produtos[]" value="prod_'.$rs['idproduto'].'"></div>';
            }
            return $retorno;
        }        
        
        #Monta imagens editar
        function MontaImagens($idkit){
            $Sql = "SELECT * FROM t_imagens_kit WHERE idkit = $idkit ORDER BY ordem";
            $result = parent::Execute($Sql);
            $num_rows = parent::Linha($result);
            if($num_rows){
                while($rs = parent::ArrayData($result)){
                    $imagens .= "<div id='i=%".$rs['idimagem']."' class='colFoto span_1_of_5'><div style='height: 12.6em;'><img class='img-responsive' style='max-height: 100%;display: block; margin-left: auto; margin-right: auto;' src=\"".UrlFoto.$rs['caminho']."\" data-file='".$rs['idimagem']."'/></div><button type='button' onclick='removeFotoKit(\"".$rs['idimagem']."\", \"".$idkit."\")' style='width: 100%; padding: 1px; margin: 1px 0;' class='btn btn-danger'>Remover</button></div>";
                }
            }
            return $imagens;
        }
        
        #Insere Kit
        function InsereKit($nome, $codigo, $codigo_fornecedor, $valor, $arrProdutos, $arrQuantidade, $files, $estoque, $valor_profissional, $valor_consumidor, $descricao, $informacoes){
            $Sql = "INSERT INTO t_kit (nome, codigo, codigo_fornecedor, valor_unitario, valor_profissional, valor_consumidor, estoque, descricao, informacoes) VALUES ('".ucwords($nome)."', '$codigo', '$codigo_fornecedor', '$valor', '$valor_profissional', '$valor_consumidor', '$estoque', '".ucfirst($descricao)."', '".ucfirst($informacoes)."')";
            parent::Execute($Sql);
            $lastID = mysql_insert_id();
            if($files){
                $this->InsereFotosInsertKit($lastID, $files);
            }
            #Insere produtos
            foreach($arrProdutos as $key => $value){
            	$aux_produto = explode("_", $value);
                $SqlProdutos = "INSERT INTO t_kit_produtos (idkit, idproduto, quantidade) VALUES ('$lastID', '".$aux_produto[1]."', '{$arrQuantidade[$key]}')";
                parent::Execute($SqlProdutos);
            }
            parent::RedirecionaPara('lista-kit');
        }
        
        #Fotos insert
        function InsereFotosInsertKit($idkit, $files){
            $cont = 1;
            $caminhoCriar = $_SERVER['DOCUMENT_ROOT'] . AuxCaminhoFotoKits . $idkit;
            $caminho = "arq/kits/$idkit";
            
			mkdir($caminhoCriar, 0755);
            foreach($files as $file){
                #Pega extensão da imagem
                preg_match("/\.(gif|png|jpg|jpeg){1}$/i", $file["name"], $ext);
                $caminhoMover = "/$idkit - $cont" . "." . $ext[1];
                move_uploaded_file($file["tmp_name"], $caminhoCriar.$caminhoMover);
                $Sql = "INSERT INTO t_imagens_kit (idkit, caminho, ordem) VALUES ('$idkit', '".$caminho.$caminhoMover."', '$cont')";
                parent::Execute($Sql);
                $cont++;
            }
        }
        
        #Atualiza Kit
        function AtualizaKit($idkit, $nome, $codigo, $codigo_fornecedor, $valor, $arrProdutos, $arrQuantidade, $files, $estoque, $valor_profissional, $valor_consumidor, $descricao, $informacoes){
            $Sql = "UPDATE t_kit SET nome = '".ucwords($nome)."', codigo = '".$codigo."', codigo_fornecedor = '".$codigo_fornecedor."', valor_unitario = '$valor', valor_profissional = '$valor_profissional', valor_consumidor = '$valor_consumidor', descricao = '".ucfirst($descricao)."', informacoes = '".ucfirst($informacoes)."', estoque = '$estoque' WHERE idkit = $idkit";
            if(parent::Execute($Sql)){
                #Deleta produtos
                $SqlDeletaProdutos = "DELETE FROM t_kit_produtos WHERE idkit = $idkit";
                parent::Execute($SqlDeletaProdutos);
                
                #Adiciona produtos
                foreach($arrProdutos as $key => $value){
                	$aux_produto = explode("_", $value);
                	$SqlProdutos = "INSERT INTO t_kit_produtos (idkit, idproduto, quantidade) VALUES ('$idkit', '".$aux_produto[1]."', '{$arrQuantidade[$key]}')";
                    parent::Execute($SqlProdutos);
                }
                
                if($files){
                    $this->InsereFotosUpdate($idkit, $files);
                }
                parent::RedirecionaPara('lista-kit');
            }else{
                parent::ChamaManutencao();
            }
        }
        
        #Fotos update
        function InsereFotosUpdate($idkit, $files){
            $order = 1;
            $caminhoCriar = $_SERVER['DOCUMENT_ROOT'] . AuxCaminhoFotoKits . $idkit;
			$caminho = "arq/kits/$idkit";
			
			#Pega a numeração da última imagem caso precise adicionar 1 novo
			$SqlIMG = "SELECT * FROM t_imagens_kit WHERE idkit = $idkit ORDER BY idimagem DESC LIMIT 0, 1";
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
					$SqlUpdate = "UPDATE t_imagens_kit SET ordem = $order WHERE idimagem = " . ltrim($file['name'], '%');
					parent::Execute($SqlUpdate);
				}else{
					#INSERT
					$ultimo++;
					#Pega extensão da imagem
					preg_match("/\.(gif|png|jpg|jpeg){1}$/i", $file["name"], $ext);
					$caminhoMover = "/$idkit - $ultimo" . "." . $ext[1];
                    move_uploaded_file($file["tmp_name"], $caminhoCriar.$caminhoMover);
					$Sql = "INSERT INTO t_imagens_kit (idkit, caminho, ordem) VALUES ('$idkit', '".$caminho.$caminhoMover."', '$order')";
					parent::Execute($Sql);
				}
				$order++;
			}   
        }
        
        #Lista Kits
        function ListaKits(){
            $Auxilio = parent::CarregaHtml('Produtos/itens/lista-kit-itens');
            $Sql = "SELECT * FROM t_kit";
            $result = parent::Execute($Sql);
            $linha = parent::Linha($result);
            if($linha){
                while($rs = parent::ArrayData($result)){
                    $Linha = $Auxilio;
                    $Linha = str_replace('<%NOME%>', $rs['nome'], $Linha);
                    $Linha = str_replace('<%ID%>', $rs['idkit'], $Linha);
                    $Linha = str_replace('<%CODIGO%>', $rs['codigo'], $Linha);
                    $Linha = str_replace('<%VALOR%>', number_format($rs['valor_unitario'], 2, ',', '.'), $Linha);
                    $produtos = '';
                    $SqlProdutos = "SELECT P.*, K.* FROM t_kit_produtos K 
                                    INNER JOIN t_produtos P ON P.idproduto = K.idproduto 
                                    WHERE K.idkit = " . $rs['idkit'];
                    $resultProdutos = parent::Execute($SqlProdutos);
                    while($rsProdutos = parent::ArrayData($resultProdutos)){
                        $produtos .= "<span style='font-size: 10px;'>{$rsProdutos['nome']} - Qtd: {$rsProdutos['quantidade']}</span><br/>";
                    }
                    $Linha = str_replace('<%PRODUTOS%>', $produtos, $Linha);
                    if($rs['ativo'] == 1){
                        $Linha = str_replace("<%ATIVOINATIVO%>", 'Ativo', $Linha);
                        $Linha = str_replace("<%BOTAOAI%>", '<a href="javascript:void(0)" onclick="inativar('.$rs['idkit'].', \''.$rs['nome'].'\')">Inativar</a>', $Linha);
                    }else{
                        $Linha = str_replace("<%ATIVOINATIVO%>", 'Inativo', $Linha);
                        $Linha = str_replace("<%BOTAOAI%>", '<a href="javascript:void(0)" onclick="ativar('.$rs['idkit'].', \''.$rs['nome'].'\')">Ativar</a>', $Linha);
                    }
                    $retorno .= $Linha;
                }
            }else{
                $retorno = '<tr class="odd gradeX">
                               <td colspan="6">Não foram encontrados kits cadastrados.</td>
                           <tr>';
            }
            return utf8_encode($retorno);
        }
        
        #Ativar Kit
        function Ativar($idkit){
            $Sql = "UPDATE t_kit SET ativo = 1 WHERE idkit = $idkit";
            parent::Execute($Sql);
            parent::RedirecionaPara('monta-kit/editar/'.$idkit);
        }
        
        #Inativar Kit
        function Inativar($idkit){
            $Sql = "UPDATE t_kit SET ativo = 0 WHERE idkit = $idkit";
            parent::Execute($Sql);
            /*$SqlVerifica = "SELECT * FROM t_destaques WHERE idproduto = $idproduto";
            $resultVerifica = parent::Execute($SqlVerifica);
            $linhaVerifica = parent::Linha($resultVerifica);
            if($linhaVerifica){
                $SqlTiraDestaque = "DELETE FROM t_destaques WHERE idproduto = $idproduto";
                parent::Execute($SqlTiraDestaque);
            }*/
            parent::RedirecionaPara('monta-kit/editar/'.$idkit);
        }
    }
?>