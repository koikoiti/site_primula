<?php
    $titulo = "Monta Kit";
    $require_foto = 'required';
    $hidden = "";
    $botao_voltar = '<button onclick="voltar()" style="box-shadow: none;background-color: #000000;border-color: transparent;border-color: #CCCCCC;border-radius: 0;-webkit-border-radius: 0;outline: none;margin-bottom: 5px;margin-left: 3px;font-size: 13px;padding: 7px 11px;" type="button" class="btn btn-success btn-flat">Voltar</button>';
    
	#include das funcoes da tela inico
	include('functions/banco-kit.php');

	#Instancia o objeto
	$banco = new bancokit();
    
    if($this->PaginaAux[0] == 'editar'){
        $require_foto = '';
        #Trabalha com editar
        $idkit = $this->PaginaAux[1];
        $hidden = $banco->MontaHidden($idkit);
        $resultKit = $banco->BuscaKitPorId($idkit);
        $rsKit = $banco->ArrayData($resultKit);
        
        $titulo = "Editar Kit";
        
        $nome = utf8_encode($rsKit['nome']);
        $codigo = utf8_encode($rsKit['codigo']);
        $codigo_fornecedor = utf8_encode($rsKit['codigo_fornecedor']);
        $valor = $rsKit['valor_unitario'];
        $valor_profissional = $rsKit['valor_profissional'];
        $valor_consumidor = $rsKit['valor_consumidor'];
        $descricao = utf8_encode($rsKit['descricao']);
        $informacoes = utf8_encode($rsKit['informacoes']);
        $estoque = $rsKit['estoque'];
        
        #Imagens
        $imagens = $banco->MontaImagens($idkit);
        
        #Produtos
        $produtos = $banco->MontaProdutosKit($idkit);
        
        #Botões
        if($rsKit['ativo'] == 0){
            $botao_ativar_inativar = '<button onclick="ativar(\''.$idkit.'\')" style="box-shadow: none;background-color: #191BB6;border-color: transparent;border-color: #CCCCCC;border-radius: 0;-webkit-border-radius: 0;outline: none;margin-bottom: 5px;margin-left: 3px;font-size: 13px;padding: 7px 11px;" type="button" class="btn btn-success btn-flat">Ativar</button>';;
        }else{
            $botao_ativar_inativar = '<button onclick="inativar(\''.$idkit.'\')" style="box-shadow: none;background-color: #B0A46A;border-color: transparent;border-color: #CCCCCC;border-radius: 0;-webkit-border-radius: 0;outline: none;margin-bottom: 5px;margin-left: 3px;font-size: 13px;padding: 7px 11px;" type="button" class="btn btn-success btn-flat">Inativar</button>';;
        }
        $botao_voltar = '<button onclick="voltar()" style="box-shadow: none;background-color: #000000;border-color: transparent;border-color: #CCCCCC;border-radius: 0;-webkit-border-radius: 0;outline: none;margin-bottom: 5px;margin-left: 3px;font-size: 13px;padding: 7px 11px;" type="button" class="btn btn-success btn-flat">Voltar</button>';
    }elseif($this->PaginaAux[0] == 'ativar'){
        $idkit = $this->PaginaAux[1];
        $banco->Ativar($idkit);
        $banco->RedirecionaPara('lista-kit');
    }elseif($this->PaginaAux[0] == 'inativar'){
        $idkit = $this->PaginaAux[1];
        $banco->Inativar($idkit);
        $banco->RedirecionaPara('lista-kit');
    }elseif($this->PaginaAux[0] == 'visualizar'){
        $idkit = $this->PaginaAux[1];
        $banco->VisualizaFichaKit($idkit);
    }
    
    if(isset($_POST["acao"]) && $_POST["acao"] != '' ){
        $nome = utf8_decode(strip_tags(trim(addslashes($_POST["nome"]))));
        $codigo = utf8_decode(strip_tags(trim(addslashes($_POST["codigo"]))));
        $codigo_fornecedor = utf8_decode(strip_tags(trim(addslashes($_POST["codigo_fornecedor"]))));
        $estoque = strip_tags(trim(addslashes($_POST["estoque"])));
        $valor = utf8_decode(strip_tags(trim(addslashes($_POST["valor"]))));
        $valor = str_replace('.', '', $valor);
        $valor = str_replace(',', '.', $valor);
        #Valor Profissional
        $valor_profissional = strip_tags(trim(addslashes($_POST["valor_profissional"])));
        $valor_profissional = str_replace('.', '', $valor_profissional);
        $valor_profissional = str_replace(',', '.', $valor_profissional);
        #Valor Consumidor
        $valor_consumidor = strip_tags(trim(addslashes($_POST["valor_consumidor"])));
        $valor_consumidor = str_replace('.', '', $valor_consumidor);
        $valor_consumidor = str_replace(',', '.', $valor_consumidor);
        $descricao = utf8_decode(strip_tags(trim(addslashes($_POST["descricao"]))));
        $informacoes = utf8_decode(strip_tags(trim(addslashes($_POST["informacoes"]))));
        #Arr
        $arrProdutos = $_POST['produtos'];
        $arrQuantidade = $_POST['quantidade'];
        
        if($idkit){
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
            $banco->AtualizaKit($idkit, $nome, $codigo, $codigo_fornecedor, $valor, $arrProdutos, $arrQuantidade, $auxImagens, $estoque, $valor_profissional, $valor_consumidor, $descricao, $informacoes);
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
                $msg = "Produto com esse código já cadastrado!";
            }else{
                #Insert
                $banco->InsereKit($nome, $codigo, $codigo_fornecedor, $valor, $arrProdutos, $arrQuantidade, $auxImagens, $estoque, $valor_profissional, $valor_consumidor, $descricao, $informacoes);
            }
        }        
    }#Fim POST
    
    $Conteudo = utf8_encode($banco->CarregaHtml('Produtos/monta-kit'));
    $Conteudo = str_replace("<%TITULO%>", $titulo, $Conteudo);
    $Conteudo = str_replace("<%NOME%>", $nome, $Conteudo);
    $Conteudo = str_replace("<%CODIGO%>", $codigo, $Conteudo);
    $Conteudo = str_replace("<%CODIGOFORNECEDOR%>", $codigo_fornecedor, $Conteudo);
    $Conteudo = str_replace("<%VALOR%>", $valor, $Conteudo);
    $Conteudo = str_replace("<%ESTOQUE%>", $estoque, $Conteudo);
    $Conteudo = str_replace("<%VALORPROFISSIONAL%>", $valor_profissional, $Conteudo);
    $Conteudo = str_replace("<%VALORCONSUMIDOR%>", $valor_consumidor, $Conteudo);
    $Conteudo = str_replace("<%DESCRICAO%>", $descricao, $Conteudo);
    $Conteudo = str_replace("<%INFORMACOES%>", $informacoes, $Conteudo);
    $Conteudo = str_replace("<%PRODUTOS%>", $produtos, $Conteudo);
    $Conteudo = str_replace("<%IMAGENS%>", $imagens, $Conteudo);
    $Conteudo = str_replace("<%REQUIREFOTO%>", $require_foto, $Conteudo);
    #Botões
    $Conteudo = str_replace("<%BOTAOEXCLUIR%>", $botao_excluir, $Conteudo);
    $Conteudo = str_replace("<%BOTAOATIVARINATIVAR%>", $botao_ativar_inativar, $Conteudo);
    $Conteudo = str_replace("<%BOTAOVOLTAR%>", $botao_voltar, $Conteudo);
    $Conteudo = str_replace("<%HIDDEN%>", $hidden, $Conteudo);
?>