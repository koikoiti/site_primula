<?php
	class bancobusca extends banco{
		
		function MontaBuscaProduto($busca){
			$busca = utf8_decode($busca);
			$Sql = "SELECT P.idproduto, P.nome, I.caminho FROM t_produtos P INNER JOIN t_imagens_produto I ON I.idproduto = P.idproduto WHERE I.ordem = 1 AND P.nome LIKE '%$busca%'";
			$result = parent::Execute($Sql);
			$linha = parent::Linha($result);
			if($linha){
				$retorno = '<div class="span12"><div id="grid-view" class="products-grid products-holder active tab-pane"><h3 style="background: #ee5971; padding: 5px; border-radius: 8px;">Produtos</h3>';
				$Auxilio = parent::CarregaHtml('itens/lista-produto-itens');
				$cont = 0;
				$retorno .= '<div class="row-fluid">';
				while($rs = parent::ArrayData($result)){
					$Linha = $Auxilio;
					$Linha = str_replace('<%IDPRODUTO%>', $rs['idproduto'], $Linha);
					$Linha = str_replace('<%NOMEPRODUTO%>', $rs['nome'], $Linha);
					$Linha = str_replace('<%PRECO%>', '', $Linha);
					$Linha = str_replace('<%MARCA%>', '', $Linha);
					$Linha = str_replace('<%CAMINHOIMAGEM%>', UrlFoto.$rs['caminho'], $Linha);
					$retorno .= $Linha;
					if($cont == 2){
						$retorno .= '</div><div class="row-fluid">';
						$cont = 0;
					}else{
						$cont++;
					}
				
				}
				$retorno = rtrim($retorno, '<div class="row-fluid">');
				$retorno .= '<hr/></div></div>';
				return utf8_encode($retorno);
			}else{
				return '';
			}
		}
		
		function MontaBuscaKit($busca){
			$busca = utf8_decode($busca);
			$Sql = "SELECT K.idkit, K.nome, I.caminho FROM t_kit K INNER JOIN t_imagens_kit I ON I.idkit = K.idkit WHERE I.ordem = 1 AND K.nome LIKE '%$busca%'";
			$result = parent::Execute($Sql);
			$linha = parent::Linha($result);
			if($linha){
				$retorno = '<div class="span12"><div id="grid-view" class="products-grid products-holder active tab-pane"><h3 style="background: #ee5971; padding: 5px; border-radius: 8px;">Kits</h3>';
				$Auxilio = parent::CarregaHtml('itens/lista-kit-itens');
				$cont = 0;
				$retorno .= '<div class="row-fluid">';
				while($rs = parent::ArrayData($result)){
					$Linha = $Auxilio;
					$Linha = str_replace('<%IDKIT%>', $rs['idkit'], $Linha);
					$Linha = str_replace('<%NOMEKIT%>', $rs['nome'], $Linha);
					$Linha = str_replace('<%CAMINHOIMAGEM%>', UrlFoto.$rs['caminho'], $Linha);
					$retorno .= $Linha;
					if($cont == 2){
						$retorno .= '</div><div class="row-fluid">';
						$cont = 0;
					}else{
						$cont++;
					}
			
				}
				$retorno = rtrim($retorno, '<div class="row-fluid">');
				$retorno .= '<hr/></div></div></div>';
				return utf8_encode($retorno);
			}else{
				return '';
			}
		}
		
		function MontaBuscaCurso($busca){
			$busca = utf8_decode($busca);
			$Sql = "SELECT * FROM t_cursos WHERE nome LIKE '%$busca%'";
			$result = parent::Execute($Sql);
			$linha = parent::Linha($result);
			if($linha){
				$retorno = '<div class="span12"><div id="list-view" class="products-list products-holder active tab-pane"><h3 style="background: #ee5971; padding: 5px; border-radius: 8px;">Cursos</h3><div class="list-item">';
				$Auxilio = parent::CarregaHtml('itens/lista-curso-itens');
				while($rs = parent::ArrayData($result)){
					$Linha = $Auxilio;
					$Linha = str_replace('<%NOMECURSO%>', $rs['nome'], $Linha);
					if($rs['data_ini'] == $rs['data_fim']){
						$data_format = date("d/m/Y", strtotime($rs['data_ini']));
					}else{
						$data_format = date("d/m/Y", strtotime($rs['data_ini'])) . " até " . date("d/m/Y", strtotime($rs['data_fim']));
					}
					if($rs['hora_ini'] == $rs['hora_fim']){
						$hora_format = date("H:i", strtotime($rs['hora_ini']));
					}else{
						$hora_format = date("H:i", strtotime($rs['hora_ini'])) . " às " . date("H:i", strtotime($rs['hora_fim'])); 
					}
					$Linha = str_replace('<%DATAFORMATADA%>', $data_format, $Linha);
					$Linha = str_replace('<%HORAFORMATADA%>', $hora_format, $Linha);
					$Linha = str_replace('<%DESCRICAO%>', $rs['descricao'], $Linha);
					if($rs['carga_horaria'] == 0){
						$carga_horaria = '';
					}else{
						$carga_horaria = "<p><span>Carga Horária: ". $rs['carga_horaria'] ." horas</span></p>";
					}
					$Linha = str_replace('<%CARGAHORARIA%>', $carga_horaria, $Linha);
					if($rs['investimento'] == '0.00'){
						$investimento = '';
					}else{
						$investimento = "<p><span>Investimento: R$ ". number_format($rs['investimento'], 2, ',', '.') ."</span></p>";
					}
					$Linha = str_replace('<%INVESTIMENTO%>', $investimento, $Linha);
					$Linha = str_replace('<%IDCURSO%>', $rs['idcurso'], $Linha);
					$retorno .= $Linha;
				}
				$retorno .= '</div></div></div>';
				return utf8_encode($retorno);
			}else{
				return '';
			}
		}
	}
?>