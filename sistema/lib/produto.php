<?php


    $titulo = "Novo Produto";
    $require_foto = 'required';
    $hidden = "";
    $selected_hc = '';
    $selected_p = '';
    
	#include das funcoes da tela inico
	include('functions/banco-produto.php');

	#Instancia o objeto
	$banco = new bancoproduto();
	
	if($this->PaginaAux[0] == 'visualizar'){
	    $idproduto = $this->PaginaAux[1];
	    $banco->VisualizaFichaProduto($idproduto);
	}
	
	
	#Inserir e editar, conforme solicitado, liberado apenas para 2 usu�rios e administra��o
	if($_SESSION['idsetor'] == 1 || $_SESSION['idusuario'] == 33 || $_SESSION['idusuario'] == 43){
        if($this->PaginaAux[0] == 'editar'){
            $require_foto = '';
            #Trabalha com editar
            $idproduto = $this->PaginaAux[1];
            $hidden = $banco->MontaHidden($idproduto);
            $resultProduto = $banco->BuscaProdutoPorId($idproduto);
            $rsProduto = $banco->ArrayData($resultProduto);
            
            $titulo = "Editar Produto";
            
            $idcategoria = $rsProduto['idcategoria'];
            $cod_barras = $rsProduto['cod_barras'];
            $ncm = $rsProduto['ncm'];
            $cod_fornecedor = $rsProduto['cod_fornecedor'];
            $nome = utf8_encode($rsProduto['nome']);
            $marca = utf8_encode($rsProduto['marca']);
            $valor_unitario = $rsProduto['valor_unitario'];
            $valor_profissional = $rsProduto['valor_profissional'];
            $valor_consumidor = $rsProduto['valor_consumidor'];
            $valor_app = $rsProduto['valor_app'];
            $descricao = utf8_encode($rsProduto['descricao']);
            $informacoes = utf8_encode($rsProduto['informacoes']);
            $estoque = $rsProduto['estoque'];
            switch($rsProduto['campo_status']){
                case "Home Care":
                    $selected_hc = "selected";
                    break;
                case "Profissional":
                    $selected_p = "selected";
                    break;
            }
            
            #Imagens
            $imagens = $banco->MontaImagens($idproduto);
            
            #Bot�es
            #$botao_excluir = '<button onclick="excluir(\''.$idproduto.'\')" style="box-shadow: none;background-color: #B6195B;border-color: transparent;border-color: #CCCCCC;border-radius: 0;-webkit-border-radius: 0;outline: none;margin-bottom: 5px;margin-left: 3px;font-size: 13px;padding: 7px 11px;" type="button" class="btn btn-success btn-flat">Excluir</button>';
            if($rsProduto['ativo'] == 0){
                $botao_ativar_inativar = '<button onclick="ativar(\''.$idproduto.'\')" style="box-shadow: none;background-color: #191BB6;border-color: transparent;border-color: #CCCCCC;border-radius: 0;-webkit-border-radius: 0;outline: none;margin-bottom: 5px;margin-left: 3px;font-size: 13px;padding: 7px 11px;" type="button" class="btn btn-success btn-flat">Ativar</button>';
            }else{
                $botao_ativar_inativar = '<button onclick="inativar(\''.$idproduto.'\')" style="box-shadow: none;background-color: #B0A46A;border-color: transparent;border-color: #CCCCCC;border-radius: 0;-webkit-border-radius: 0;outline: none;margin-bottom: 5px;margin-left: 3px;font-size: 13px;padding: 7px 11px;" type="button" class="btn btn-success btn-flat">Inativar</button>';
            }
            $botao_voltar = '<button onclick="voltar()" style="box-shadow: none;background-color: #000000;border-color: transparent;border-color: #CCCCCC;border-radius: 0;-webkit-border-radius: 0;outline: none;margin-bottom: 5px;margin-left: 3px;font-size: 13px;padding: 7px 11px;" type="button" class="btn btn-success btn-flat">Voltar</button>'; 
        	
            if($rsProduto['ocultar'] == 0){
            	$botao_ocultar = '<button onclick="ocultarNoSite(\''.$idproduto.'\', 1)" style="box-shadow: none;background-color: #784d9e;border-color: transparent;border-color: #CCCCCC;border-radius: 0;-webkit-border-radius: 0;outline: none;margin-bottom: 5px;margin-left: 3px;font-size: 13px;padding: 7px 11px;" type="button" class="btn btn-success btn-flat">Ocultar no Site</button>';
            }else{
            	$botao_ocultar = '<button onclick="ocultarNoSite(\''.$idproduto.'\', 0)" style="box-shadow: none;background-color: #784d9e;border-color: transparent;border-color: #CCCCCC;border-radius: 0;-webkit-border-radius: 0;outline: none;margin-bottom: 5px;margin-left: 3px;font-size: 13px;padding: 7px 11px;" type="button" class="btn btn-success btn-flat">Mostrar no Site</button>';
            }
            
        }elseif($this->PaginaAux[0] == 'ativar'){
            $idproduto = $this->PaginaAux[1];
            $banco->Ativar($idproduto);
            $banco->RedirecionaPara('lista-produto');
        }elseif($this->PaginaAux[0] == 'inativar'){
            $idproduto = $this->PaginaAux[1];
            $banco->Inativar($idproduto);
            $banco->RedirecionaPara('lista-produto');
        }elseif($this->PaginaAux[0] == 'ocultar'){
        	$idproduto = $this->PaginaAux[1];
        	$valor = $this->PaginaAux[2];
        	$banco->OcultarNoSite($idproduto, $valor);
        	$banco->RedirecionaPara('produto/editar/'.$idproduto);
        }
        
        #Trabalha com Post
    	if(isset($_POST["acao"]) && $_POST["acao"] != '' ){
            $cod_barras = strip_tags(trim(addslashes($_POST["cod_barras"])));
            $ncm = strip_tags(trim(addslashes($_POST["ncm"])));
            $cod_fornecedor = strip_tags(trim(addslashes($_POST["cod_fornecedor"])));
            $nome = utf8_decode(strip_tags(trim(addslashes($_POST["nome"]))));
            $marca = utf8_decode(strip_tags(trim(addslashes($_POST["marca"]))));
            $idcategoria = strip_tags(trim(addslashes($_POST["categoria"])));
            $estoque = strip_tags(trim(addslashes($_POST["estoque"])));
            $campo_status = strip_tags(trim(addslashes($_POST["campo_status"])));
            #Valor Unit�rio
            $valor_unitario = strip_tags(trim(addslashes($_POST["valor_unitario"])));
            $valor_unitario = str_replace('.', '', $valor_unitario);
            $valor_unitario = str_replace(',', '.', $valor_unitario);
            #Valor Profissional
            $valor_profissional = strip_tags(trim(addslashes($_POST["valor_profissional"])));
            $valor_profissional = str_replace('.', '', $valor_profissional);
            $valor_profissional = str_replace(',', '.', $valor_profissional);
            #Valor Consumidor
            $valor_consumidor = strip_tags(trim(addslashes($_POST["valor_consumidor"])));
            $valor_consumidor = str_replace('.', '', $valor_consumidor);
            $valor_consumidor = str_replace(',', '.', $valor_consumidor);
            #Valor App
            $valor_app = strip_tags(trim(addslashes($_POST["valor_app"])));
            $valor_app = str_replace('.', '', $valor_app);
            $valor_app = str_replace(',', '.', $valor_app);
            $descricao = utf8_decode(strip_tags(trim(addslashes($_POST["descricao"]))));
            $informacoes = utf8_decode(strip_tags(trim(addslashes($_POST["informacoes"]))));
            
            if($idproduto){
                #Update
                #Pega as imagens e arruma num array
        		if($_FILES['imagens']['name'][0] !== ""){
        			$files = array();
        			$fdata = $_FILES['imagens'];
        			if(is_array($fdata['name'])){
        				for($i = 0; $i < count($fdata['name']); ++$i){
        					$files[] = array(
        							'name'     => $fdata['name'][$i],
        							'tmp_name' => $fdata['tmp_name'][$i],
        					);
        				}
        			}else{
        				$files[] = $fdata;
        			}
        		}
        		$order = explode('i[]=', $_POST['imgOrder']);
        		foreach($order as $key => $value){
        			$nomeImagem = rtrim($value, '&');
        			if($nomeImagem != ''){
        				if($nomeImagem[0] == "%"){
        					$auxImagens[] = array(
        						'name'     => $nomeImagem,
        						'tmp_name' => '',
        					);
        				}else{
        					foreach($files as $f){
        						if($nomeImagem == $f['name']){
        							$auxImagens[] = array(
        								'name'     => $f['name'],
        								'tmp_name' => $f['tmp_name'],
        							);
        						break;
        						}
        					}
        				}
        			}
        		}
                $banco->AtualizaProduto($idproduto, $cod_barras, $cod_fornecedor, $nome, $marca, $idcategoria, $estoque, $valor_unitario, $valor_profissional, $valor_consumidor, $valor_app, $descricao, $informacoes, $auxImagens, $ncm, $campo_status);
                $banco->RedirecionaPara('lista-produto');
            }else{
                #Pega as imagens e arruma num array
        		if($_FILES['imagens']['name'][0] !== ""){
        			$files = array();
        			$fdata = $_FILES['imagens'];
        			if(is_array($fdata['name'])){
        				for($i = 0; $i < count($fdata['name']); ++$i){
        					$files[] = array(
        							'name'     => $fdata['name'][$i],
        							'tmp_name' => $fdata['tmp_name'][$i],
        					);
        				}
        			}else{
        				$files[] = $fdata;
        			}
        		}
        		$order = explode('i[]=', $_POST['imgOrder']);
        		foreach($order as $key => $value){
        			$nomeImagem = rtrim($value, '&');
        			foreach($files as $f){
        				if($nomeImagem == $f['name']){
        					$auxImagens[] = array(
        									'name'     => $f['name'],
        									'tmp_name' => $f['tmp_name'],
        									);
        					break;
        				}
        			}
        		}
                #Busca Produto no banco e verifica se ele existe
                #$result = $banco->BuscaProdutoPorCodBarras($cod_barras);
                #$num_rows = $banco->Linha($result);
                if($num_rows){
                    $msg = "Produto com esse c�digo j� cadastrado!";
                }else{
                    #Insert
                    $banco->InsereProduto($cod_barras, $cod_fornecedor, $nome, $marca, $idcategoria, $estoque, $valor_unitario, $valor_profissional, $valor_consumidor, $valor_app, $descricao, $informacoes, $auxImagens, $ncm, $campo_status);
                    $banco->RedirecionaPara('lista-produto');
                }
            }
        }#Fim POST
        
        #Monta Categorias
        $select_categorias = utf8_encode($banco->SelectCategorias($idcategoria));
        
        if($_SESSION['idsetor'] == 1){
            $campo_custo = '<div class="form-group" style="width: 100%">
                <label class="col-sm-3 control-label form-margin">Custo</label>
                <div class="col-sm-6">
                    <input required type="text" class="form-control money" name="valor_unitario" value="'.$valor_unitario.'">
                </div>
            </div>';
        }
            
        #Imprime valores
    	$Conteudo = utf8_encode($banco->CarregaHtml('Produtos/produto'));
        $Conteudo = str_replace("<%TITULO%>", $titulo, $Conteudo);
        $Conteudo = str_replace("<%SELECTCATEGORIAS%>", $select_categorias, $Conteudo);
        $Conteudo = str_replace("<%CODBARRAS%>", $cod_barras, $Conteudo);
        $Conteudo = str_replace("<%NCM%>", $ncm, $Conteudo);
        $Conteudo = str_replace("<%CODFORNECEDOR%>", $cod_fornecedor, $Conteudo);
        $Conteudo = str_replace("<%NOME%>", $nome, $Conteudo);
        $Conteudo = str_replace("<%MARCA%>", $marca, $Conteudo);
        $Conteudo = str_replace("<%ESTOQUE%>", $estoque, $Conteudo);
        $Conteudo = str_replace("<%VALORPROFISSIONAL%>", $valor_profissional, $Conteudo);
        $Conteudo = str_replace("<%VALORCONSUMIDOR%>", $valor_consumidor, $Conteudo);
        $Conteudo = str_replace("<%VALORAPP%>", $valor_app, $Conteudo);
        $Conteudo = str_replace("<%DESCRICAO%>", $descricao, $Conteudo);
        $Conteudo = str_replace("<%INFORMACOES%>", $informacoes, $Conteudo);
        $Conteudo = str_replace("<%CAMPOCUSTO%>", $campo_custo, $Conteudo);
        $Conteudo = str_replace("<%SELECTEDHC%>", $selected_hc, $Conteudo);
        $Conteudo = str_replace("<%SELECTEDP%>", $selected_p, $Conteudo);
        #Imagens
        $Conteudo = str_replace("<%IMAGENS%>", $imagens, $Conteudo);
        $Conteudo = str_replace("<%REQUIREFOTO%>", $require_foto, $Conteudo);
        #Bot�es
        $Conteudo = str_replace("<%BOTAOEXCLUIR%>", $botao_excluir, $Conteudo);
        $Conteudo = str_replace("<%BOTAOATIVARINATIVAR%>", $botao_ativar_inativar, $Conteudo);
        $Conteudo = str_replace("<%BOTAOVOLTAR%>", $botao_voltar, $Conteudo);
        $Conteudo = str_replace("<%BOTAOOCULTAR%>", $botao_ocultar, $Conteudo);
        $Conteudo = str_replace("<%HIDDEN%>", $hidden, $Conteudo);
    }else{
        echo utf8_encode("<script>alert('Voc� n�o tem autoriza��o para acessar essa p�gina');location.href='".UrlPadrao."lista-produto'</script>");
    }
?>