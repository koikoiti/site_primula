<?php
	class bancoimovel extends banco{
		#Lista os imoveis
        function ListaImoveis($referencia, $idcategoria, $endereco, $bairro, $cidade, $pagina){
            $Auxilio = parent::CarregaHtml("Imovel/itens/lista-imovel-itens");
            $inicio = ($pagina * Limite) - Limite;
            $Sql = "SELECT I.*, C.nome AS categoria FROM t_imoveis I
                    INNER JOIN fixo_categorias_imovel C ON C.idcategoria = I.idcategoria 
                    WHERE 1
                    ";
            if($referencia != ''){
                $Sql .= " AND I.referencia LIKE '%".utf8_decode($referencia)."%'";
            }
            if($idcategoria != ''){
                $Sql .= " AND I.idcategoria = '$idcategoria'";
            }
            if($endereco != ''){
                $Sql .= " AND I.endereco LIKE '%".utf8_decode($endereco)."%'";
            }
            if($bairro != ''){
                $Sql .= " AND I.bairro LIKE '%".utf8_decode($bairro)."%'";
            }
            if($cidade != ''){
                $Sql .= " AND I.cidade LIKE '%".utf8_decode($cidade)."%'";
            }
            $Sql .= "  ORDER BY I.referencia LIMIT $inicio, ".Limite;
            $result = parent::Execute($Sql);
            $num_rows = parent::Linha($result);
            if($num_rows){
                while($rs = parent::ArrayData($result)){
                    $Linha = $Auxilio;
                    $Linha = str_replace("<%ID%>", $rs['idimovel'], $Linha);
                    $Linha = str_replace("<%REFERENCIA%>", $rs['referencia'], $Linha);
                    $Linha = str_replace("<%ENDERECO%>", $rs['endereco'], $Linha);
                    $Linha = str_replace("<%BAIRRO%>", $rs['bairro'], $Linha);
                    $Linha = str_replace("<%CATEGORIA%>", $rs['categoria'], $Linha);
                    $Linha = str_replace("<%VALOR%>", number_format($rs['valor'], 2, ',', '.'), $Linha);
                    #Verifica destaque
                    $SqlDestaque = "SELECT * FROM t_destaques WHERE idimovel = " . $rs['idimovel'];
                    $resultDestaque = parent::Execute($SqlDestaque);
                    $linhaDestaque = parent::Linha($resultDestaque);
                    if($linhaDestaque){
                        $Linha = str_replace("<%DESTAQUE%>", '<i title="Este imóvel está em destaque!" class="fa fa-star"></i>', $Linha);
                    }else{
                        $Linha = str_replace("<%DESTAQUE%>", '', $Linha);
                    }
                    $Imoveis .= $Linha;
                }
            }else{
                $Imoveis = '<tr class="odd gradeX">
                               <td colspan="6">Não foram encontrados imóveis cadastrados.</td>
                           <tr>';
            }
            
            return utf8_encode($Imoveis);
        }
        
        #Insere Imovel no banco
        function InsereImovel($referencia, $angariador, $idcategoria, $cep ,$cidade, $estado, $endereco, $numero, $bairro, $complemento, $ponto_referencia, $entre_ruas, $area_util, $area_total, $proprietario, $telefone, $dormitorios, $garagem, $sala, $churrasqueira, $piso, $esquadrias, $idade, $valor, $descricao, $informacoes, $averbada, $copa, $cozinha, $lavabo, $lavanderia, $suite, $closet, $hidromassagem, $bwc_social, $lareira, $atico, $armarios, $sacada, $escritorio, $dep_empregada, $playground, $salao_festas, $piscina, $portao_eletronico, $files){
            $Sql = "INSERT INTO t_imoveis (referencia, angariador, idcategoria, cep, cidade, estado, endereco, numero, complemento, bairro, ponto_referencia, entre_ruas, data_cadastro, proprietario, telefone, area_util, area_total, sala, copa, cozinha, lavabo, dormitorios, suite, closet, hidromassagem, bwc_social, lavanderia, lareira, atico, armarios, sacada, escritorio, piso, esquadrias, garagem, dep_empregada, churrasqueira, playground, salao_festas, piscina, portao_eletronico, averbada, cadastrado_por, valor, descricao, informacoes, condominio, ativo) 
                    VALUES ('$referencia', '$angariador', '$idcategoria', '$cep', '$cidade', '$estado', '$endereco', '$numero', '$complemento', '$bairro', '$ponto_referencia', '$entre_ruas', '".date("Y-m-d H:i:s")."', '$proprietario', '$telefone', '$area_util', '$area_total', '$sala', '$copa', '$cozinha', '$lavabo', '$dormitorios', '$suite', '$closet', '$hidromassagem', '$bwc_social', '$lavanderia', '$lareira', '$atico', '$armarios', '$sacada', '$escritorio', '$piso', '$esquadrias', '$garagem', '$dep_empregada', '$churrasqueira', '$playground', '$salao_festas', '$piscina', '$portao_eletronico', '$averbada', '".$_SESSION['nomeexibicao']."', '$valor', '$descricao', '$informacoes', '$condominio', '1')";
            if(parent::Execute($Sql)){
                if($files){
                    $lastID = mysql_insert_id();
                    $this->InsereFotos($lastID, $files);
                }
                return true;
            }else{
                parent::ChamaManutencao();
            }
        }
        
        #Insere caminho das fotos no banco e copia para a pasta do imóvel
        function InsereFotos($idimovel, $files){
            
            #Verifica update
            $SqlVerificaUpdate = "SELECT * FROM t_imagens_imovel WHERE idimovel = $idimovel ORDER BY caminho DESC LIMIT 0, 1";
            $resultVerificaUpdate = parent::Execute($SqlVerificaUpdate);
            $linhaVerificaUpdate = parent::Linha($resultVerificaUpdate);
            if($linhaVerificaUpdate){
                $rsVerificaUpdate = parent::ArrayData($resultVerificaUpdate);
                $ultimo = $rsVerificaUpdate['caminho'];
                $ultimo = explode('/', $ultimo);
                $ultimo = explode(' ', $ultimo[3]);
                $ultimo = explode('.', $ultimo[2]);
                $cont = $ultimo[0] + 1;
            }else{
                $cont = 1;
            }
                        
            #Verifica SERVER (web/local)
            if (strpos($_SERVER['DOCUMENT_ROOT'], 'public_html') !== false) {
                $caminhoCriar = $_SERVER['DOCUMENT_ROOT'] . "/arq/imoveis/$idimovel";
            }else{
                $caminhoCriar = $_SERVER['DOCUMENT_ROOT'] . "/sawasakiimoveis/arq/imoveis/$idimovel";
            }
            $caminho = "arq/imoveis/$idimovel";
            #Cria diretório
        /*
            // Escrita e leitura para o proprietario, nada ninguem mais
            chmod ("/somedir/somefile", 0600);
            
            // Escrita e leitura para o proprietario, leitura para todos os outros
            chmod ("/somedir/somefile", 0644);
            
            // Tudo para o proprietario, leitura e execucao para os outros
            chmod ("/somedir/somefile", 0755);
            
            // Tudo para o proprietario, leitura e execucao para o grupo do prop
            chmod ("/somedir/somefile", 0750);
        */
			
            mkdir($caminhoCriar, 0755);
            foreach($files as $file){
                #Pega extensão da imagem
                preg_match("/\.(gif|png|jpg|jpeg){1}$/i", $file["name"], $ext);
                $caminhoMover = "/$idimovel - $cont" . "." . $ext[1];
                move_uploaded_file($file["tmp_name"], $caminhoCriar.$caminhoMover);
                $Sql = "INSERT INTO t_imagens_imovel (idimovel, caminho) VALUES ('$idimovel', '".$caminho.$caminhoMover."')";
                parent::Execute($Sql);
                $cont++;
            }
        }
        
        #Atualiza Imovel
        function AtualizaImovel($idimovel, $idcategoria, $referencia, $angariador, $cep ,$cidade, $estado, $endereco, $numero, $bairro, $complemento, $ponto_referencia, $entre_ruas, $area_util, $area_total, $proprietario, $telefone, $dormitorios, $garagem, $sala, $churrasqueira, $piso, $esquadrias, $idade, $valor, $descricao, $informacoes, $averbada, $copa, $cozinha, $lavabo, $lavanderia, $suite, $closet, $hidromassagem, $bwc_social, $lareira, $atico, $armarios, $sacada, $escritorio, $dep_empregada, $playground, $salao_festas, $piscina, $portao_eletronico, $files){
            $Sql = "UPDATE t_imoveis SET idcategoria = '$idcategoria', referencia = '$referencia', angariador = '$angariador', cep = '$cep', cidade = '$cidade', estado = '$estado', endereco = '$endereco', numero = '$numero', bairro = '$bairro', complemento = '$complemento', 
                    ponto_referencia = '$ponto_referencia', entre_ruas = '$entre_ruas', area_util = '$area_util', area_total = '$area_total', proprietario = '$proprietario', telefone = '$telefone', dormitorios = '$dormitorios', garagem = '$garagem', sala = '$sala', 
                    churrasqueira = '$churrasqueira', piso = '$piso', esquadrias = '$esquadrias', idade = '$idade', valor = '$valor', descricao = '$descricao', informacoes = '$informacoes', averbada = '$averbada', copa = '$copa', cozinha = '$cozinha', lavabo = '$lavabo', lavanderia = '$lavanderia', 
                    suite = '$suite', closet = '$closet', hidromassagem = '$hidromassagem', bwc_social = '$bwc_social', lareira = '$lareira', atico = '$atico', armarios = '$armarios', sacada = '$sacada', escritorio = '$escritorio', 
                    dep_empregada = '$dep_empregada', playground = '$playground', salao_festas = '$salao_festas', piscina = '$piscina', portao_eletronico = '$portao_eletronico' WHERE idimovel = '$idimovel'";
            if(parent::Execute($Sql)){
                if($files){
                    $this->InsereFotos($idimovel, $files);
                }
                return true;
            }else{
                parent::ChamaManutencao();
            }
        }
        
        #Remove Imovel
        function RemoveImovel($idimovel){
            $this->RemoveFotos($idimovel);
            $Sql = "DELETE FROM t_imoveis WHERE idimovel = '$idimovel'";
            if(parent::Execute($Sql)){
                return true;
            }else{
                parent::ChamaManutencao();
            }
            
        }
        
        #Remove Fotos
        function RemoveFotos($idimovel){
            $Sql = "SELECT * FROM t_imagens_imovel WHERE idimovel = $idimovel";
            $result = parent::Execute($Sql);
            #Verifica SERVER (web/local)
            if(strpos($_SERVER['DOCUMENT_ROOT'], 'public_html') !== false) {
                $caminhoRemover = $_SERVER['DOCUMENT_ROOT'] . "/";
            }else{
                $caminhoRemover = $_SERVER['DOCUMENT_ROOT'] . "/sawasakiimoveis/";
            }
            while($rs = parent::ArrayData($result)){
                unlink($caminhoRemover.$rs['caminho']);
            }
            rmdir($caminhoRemover."arq/imoveis/$idimovel");
        }
        
        #Busca Imovel por ID
        function BuscaImovelPorId($idimovel){
            $Sql = "SELECT * FROM t_imoveis WHERE idimovel = '$idimovel'";
            $result = parent::Execute($Sql);
            return $result;
        }
        
        #Monta select Categorias
        function SelectCategorias($idcategoria){
			$Sql = "SELECT * FROM fixo_categorias_imovel ORDER BY nome";
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
        
        #Monta select busca Categorias
        function SelectBuscaCategorias($idcategoria){
			$Sql = "SELECT * FROM fixo_categorias_imovel ORDER BY nome";
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
        
        #Busca Imóvel por referência
        function BuscaImovelPorReferencia($referencia){
            $Sql = "SELECT * FROM t_imoveis WHERE referencia = '$referencia'";
            $result = parent::Execute($Sql);
            return $result;
        }
        
        #Monta imagens editar
        function MontaImagens($idimovel){
            $Sql = "SELECT * FROM t_imagens_imovel WHERE idimovel = $idimovel";
            $result = parent::Execute($Sql);
            $num_rows = parent::Linha($result);
            if($num_rows){
                while($rs = parent::ArrayData($result)){
                    $imagens .= "<div id='".$rs['idimagemimovel']."' style='float: left; clear: both; width: 20%'><div class='fileinput-preview thumbnail selFile'><img style='max-height: 400px;' src='".UrlFoto.$rs['caminho']."'></div><a href='#' onclick='removeFoto(\"".$rs['idimagemimovel']."\")' class='btn btn-danger'>Remover</a></div>";
                }
            }
            return $imagens;
        }
        
        #Monta ficha imóvel
        function VisualizaFichaImovel($idimovel){
            #Busca dados imóvel
            $Sql = "SELECT I.*, C.nome AS categoria, X.* FROM t_imoveis I 
                    INNER JOIN fixo_categorias_imovel C ON I.idcategoria = C.idcategoria
                    INNER JOIN t_imagens_imovel X ON X.idimovel = I.idimovel
                    WHERE I.idimovel = $idimovel 
                    ORDER BY caminho ASC";
            $result = parent::Execute($Sql);
            $rs = parent::ArrayData($result);
            
            #Inicia mPDF
            require_once('app/mpdf60/mpdf.php');
            $mpdf = new mPDF('utf-8', 'A4', '', '', 8, 8, 0, 9);
                        
            #HTML Auxilio
            $Auxilio = utf8_encode(parent::CarregaHtml('Imovel/ficha'));
            
            #Replaces
            $data = 'Curitiba, ' . date('d/m/Y');
            $logo = "<img style='height: 80px;' src='".UrlPdf."html/img/logo.png"."' />";
            $Auxilio = str_replace('<%DATA%>', $data, $Auxilio);
            $Auxilio = str_replace('<%LOGO%>', $logo, $Auxilio);
            
            #Primeira table
            $Auxilio = str_replace('<%REFERENCIA%>', $rs['referencia'], $Auxilio);
            $Auxilio = str_replace('<%ANGARIADOR%>', utf8_encode($rs['angariador']), $Auxilio);
            $Auxilio = str_replace('<%ENDERECO%>', utf8_encode($rs['endereco']), $Auxilio);
            $Auxilio = str_replace('<%BAIRRO%>', utf8_encode($rs['bairro']), $Auxilio);
            $Auxilio = str_replace('<%ENTRERUAS%>', utf8_encode($rs['entre_ruas']), $Auxilio);
            $Auxilio = str_replace('<%PONTOREFERENCIA%>', utf8_encode($rs['ponto_referencia']), $Auxilio);
            $Auxilio = str_replace('<%CEP%>', $rs['cep'], $Auxilio);
            $Auxilio = str_replace('<%COMPLEMENTO%>', utf8_encode($rs['complemento']), $Auxilio);
            $Auxilio = str_replace('<%PROPRIETARIO%>', utf8_encode($rs['proprietario']), $Auxilio);
            $Auxilio = str_replace('<%CIDADEESTADO%>', utf8_encode($rs['cidade'] . "/" . $rs['estado']), $Auxilio);
            $Auxilio = str_replace('<%TELEFONE%>', $rs['telefone'], $Auxilio);
            $Auxilio = str_replace('<%DATACADASTRO%>', date('d/m/Y - H:i', strtotime($rs['data_cadastro'])), $Auxilio);
            $Auxilio = str_replace('<%CADASTRADOPOR%>', utf8_encode($rs['cadastrado_por']), $Auxilio);
            
            #Segunda table
            $Auxilio = str_replace('<%AREAUTIL%>', utf8_encode($rs['area_util']), $Auxilio);
            $Auxilio = str_replace('<%AREATOTAL%>', utf8_encode($rs['area_total']), $Auxilio); 
            $Auxilio = str_replace('<%CATEGORIA%>', utf8_encode($rs['categoria']), $Auxilio);
            $Auxilio = str_replace('<%CHURRASQUEIRA%>', utf8_encode($rs['churrasqueira']), $Auxilio);
            $Auxilio = str_replace('<%SALA%>', utf8_encode($rs['sala']), $Auxilio);
            $Auxilio = str_replace('<%IDADE%>', utf8_encode($rs['idade']), $Auxilio);
            $Auxilio = str_replace('<%GARAGEM%>', utf8_encode($rs['garagem']), $Auxilio);
            $Auxilio = str_replace('<%PISO%>', utf8_encode($rs['piso']), $Auxilio);
            $Auxilio = str_replace('<%ESQUADRIAS%>', utf8_encode($rs['esquadrias']), $Auxilio);
                #BOOL
            if($rs['averbada'] != ''){
                $averbada = "Sim - " . $rs['averbada'];
            }else{
                $averbada = "Não";
            }
            if($rs['copa'] == 1){
                $copa = 'Sim';
            }else{
                $copa = 'Não';
            }
            if($rs['cozinha'] == 1){
                $cozinha = 'Sim';
            }else{
                $cozinha = 'Não';
            }
            if($rs['lavabo'] == 1){
                $lavabo = 'Sim';
            }else{
                $lavabo = 'Não';
            }
            if($rs['lavanderia'] == 1){
                $lavanderia = 'Sim';
            }else{
                $lavanderia = 'Não';
            }
            if($rs['suite'] == 1){
                $suite = 'Sim';
            }else{
                $suite = 'Não';
            }
            if($rs['closet'] == 1){
                $closet = 'Sim';
            }else{
                $closet = 'Não';
            }
            if($rs['hidromassagem'] == 1){
                $hidromassagem = 'Sim';
            }else{
                $hidromassagem = 'Não';
            }
            if($rs['bwc_social'] == 1){
                $bwc_social = 'Sim';
            }else{
                $bwc_social = 'Não';
            }
            if($rs['lareira'] == 1){
                $lareira = 'Sim';
            }else{
                $lareira = 'Não';
            }
            if($rs['atico'] == 1){
                $atico = 'Sim';
            }else{
                $atico = 'Não';
            }
            if($rs['armarios'] == 1){
                $armarios = 'Sim';
            }else{
                $armarios = 'Não';
            }
            if($rs['sacada'] == 1){
                $sacada = 'Sim';
            }else{
                $sacada = 'Não';
            }
            if($rs['escritorio'] == 1){
                $escritorio = 'Sim';
            }else{
                $escritorio = 'Não';
            }
            if($rs['dep_empregada'] == 1){
                $dep_empregada = 'Sim';
            }else{
                $dep_empregada = 'Não';
            }
            if($rs['playground'] == 1){
                $playground = 'Sim';
            }else{
                $playground = 'Não';
            }
            if($rs['salao_festas'] == 1){
                $salao_festas = 'Sim';
            }else{
                $salao_festas = 'Não';
            }
            if($rs['piscina'] == 1){
                $piscina = 'Sim';
            }else{
                $piscina = 'Não';
            }
            if($rs['portao_eletronico'] == 1){
                $portao_eletronico = 'Sim';
            }else{
                $portao_eletronico = 'Não';
            }
            
            $Auxilio = str_replace('<%AVERBADA%>', utf8_encode($averbada), $Auxilio);
            $Auxilio = str_replace('<%COPA%>', utf8_encode($copa), $Auxilio);
            $Auxilio = str_replace('<%COZINHA%>', utf8_encode($cozinha), $Auxilio);
            $Auxilio = str_replace('<%LAVABO%>', utf8_encode($lavabo), $Auxilio);
            $Auxilio = str_replace('<%DORMITORIOS%>', utf8_encode($rs['dormitorios']), $Auxilio);
            $Auxilio = str_replace('<%SUITE%>', utf8_encode($suite), $Auxilio);
            $Auxilio = str_replace('<%CLOSET%>', utf8_encode($closet), $Auxilio);
            $Auxilio = str_replace('<%HIDROMASSAGEM%>', utf8_encode($hidromassagem), $Auxilio);
            $Auxilio = str_replace('<%BWCSOCIAL%>', utf8_encode($bwc_social), $Auxilio);
            $Auxilio = str_replace('<%LAVANDERIA%>', utf8_encode($lavanderia), $Auxilio);
            $Auxilio = str_replace('<%LAREIRA%>', utf8_encode($lareira), $Auxilio);
            $Auxilio = str_replace('<%ATICO%>', utf8_encode($atico), $Auxilio);
            $Auxilio = str_replace('<%ARMARIOS%>', utf8_encode($armarios), $Auxilio);
            $Auxilio = str_replace('<%SACADA%>', utf8_encode($sacada), $Auxilio);
            $Auxilio = str_replace('<%ESCRITORIO%>', utf8_encode($escritorio), $Auxilio);
            $Auxilio = str_replace('<%DEPEMPREGADA%>', utf8_encode($dep_empregada), $Auxilio);
            $Auxilio = str_replace('<%PLAYGROUND%>', utf8_encode($playground), $Auxilio);
            $Auxilio = str_replace('<%SALAOFESTAS%>', utf8_encode($salao_festas), $Auxilio);
            $Auxilio = str_replace('<%PISCINA%>', utf8_encode($piscina), $Auxilio);
            $Auxilio = str_replace('<%PORTAOELETRONICO%>', utf8_encode($portao_eletronico), $Auxilio);
            
            #Terceira table
            $Auxilio = str_replace('<%VALOR%>', number_format($rs['valor'], 2, ',', '.'), $Auxilio);
            $Auxilio = str_replace('<%DESCRICAO%>', utf8_encode($rs['informacoes']), $Auxilio);
            
            #Imagem
            $Imagem = "<img style='max-height: 200px;' src='".UrlPdf.$rs['caminho']."'/>";
            $Auxilio = str_replace('<%IMG%>', $Imagem, $Auxilio);
            
            $mpdf->WriteHTML($Auxilio);
            $mpdf->SetFooter(' ');
            $mpdf->Output();
            exit;
        }
        
        #Ativar Imóvel
        function Ativar($idimovel){
            $Sql = "UPDATE t_imoveis SET ativo = 1 WHERE idimovel = $idimovel";
            parent::Execute($Sql);
            parent::RedirecionaPara('imovel/editar/'.$idimovel);
        }
        
        #Inativar Imóvel
        function Inativar($idimovel){
            $Sql = "UPDATE t_imoveis SET ativo = 0 WHERE idimovel = $idimovel";
            parent::Execute($Sql);
            $SqlVerifica = "SELECT * FROM t_destaques WHERE idimovel = $idimovel";
            $resultVerifica = parent::Execute($SqlVerifica);
            $linhaVerifica = parent::Linha($resultVerifica);
            if($linhaVerifica){
                $SqlTiraDestaque = "DELETE FROM t_destaques WHERE idimovel = $idimovel";
                parent::Execute($SqlTiraDestaque);
            }
            parent::RedirecionaPara('imovel/editar/'.$idimovel);
        }
        
         #Monta paginacao
        function MontaPaginacao($referencia, $idcategoria, $endereco, $bairro, $cidade, $pagina){
            $totalPaginas = $this->TotalPaginas($referencia, $idcategoria, $endereco, $bairro, $cidade);
            $pag = '';
            if($referencia || $idcategoria || $endereco || $bairro || $cidade){
                $url = "idcategoria=$idcategoria&endereco=$endereco&referencia=$referencia&bairro=$bairro&cidade=$cidade";
            }
            $url .= "&page=";
            if($totalPaginas > 1){
                if($pagina == 1){
                    $pag = '<span class="page active">&laquo;</span>';
                    $pag .= '<span class="page active">1</span>';
                }else{
                    $pag .= '<a href="'.UrlPadrao.'lista-imovel/?'.$url.($pagina-1).'" class="page">&laquo;</a>';
                    $pag .= '<a href="'.UrlPadrao.'lista-imovel/?'.$url.'1" class="page">1</a>';
                }
                $pag .= '<span class="page">...</span>';
                
                #Monta a paginação do meio
				if($totalPaginas < QtdPag){
				    if($pagina <= $totalPaginas){
				        for($i = 2; $i <= $totalPaginas - 1; $i++){
				            if($i == $pagina){
        						$pag .= '<span class="page active">'.$i.'</span>'; 
        					}else{
        						$pag .= '<a href="'.UrlPadrao.'lista-imovel/?'.$url.$i.'" class="page">'.$i.'</a>';	
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
    							$pag .= '<a href="'.UrlPadrao.'lista-imovel/?'.$url.$i.'" class="page">'.$i.'</a>';
    						}
    					}
    				}
				}
                
                
                $pag .= '<span class="page">...</span>';
                if($pagina == $totalPaginas){
                    $pag .= '<span class="page active">'.$totalPaginas.'</span>';
                    $pag .= '<span class="page active">&raquo;</span>';
                }else{
                    $pag .= '<a href="'.UrlPadrao.'lista-imovel/?'.$url.$totalPaginas.'" class="page">'.$totalPaginas.'</a>';
                    $pag .= '<a href="'.UrlPadrao.'lista-imovel/?'.$url.($pagina+1).'"class="page">&raquo;</a>';
                }
                
                
                return $pag;
            }else{
                return '';
            }
        }
        
        #Total de paginas
        function TotalPaginas($referencia, $idcategoria, $endereco, $bairro, $cidade){
            $Sql = "SELECT I.*, C.nome AS categoria FROM t_imoveis I
                    INNER JOIN fixo_categorias_imovel C ON C.idcategoria = I.idcategoria 
                    WHERE 1
                    ";
            if($referencia != ''){
                $Sql .= " AND I.referencia LIKE '%".utf8_decode($referencia)."%'";
            }
            if($idcategoria != ''){
                $Sql .= " AND I.idcategoria = '$idcategoria'";
            }
            if($endereco != ''){
                $Sql .= " AND I.endereco LIKE '%".utf8_decode($endereco)."%'";
            }
            if($bairro != ''){
                $Sql .= " AND I.bairro LIKE '%".utf8_decode($bairro)."%'";
            }
            if($cidade != ''){
                $Sql .= " AND I.cidade LIKE '%".utf8_decode($cidade)."%'";
            }
            $result = parent::Execute($Sql);
			$num_rows = parent::Linha($result);
			$totalPag = ceil($num_rows/Limite);
			return $totalPag;
        }
	}
?>