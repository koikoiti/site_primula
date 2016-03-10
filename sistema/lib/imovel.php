<?php
    #Variáveis
    $titulo = "Novo Imóvel";
    $botao_excluir = '';
    $vbaverbada = 'hidden';
       
	#include das funcoes da tela imovel
	include('functions/banco-imovel.php');

	#Instancia o objeto
	$banco = new bancoimovel();
    
    if($this->PaginaAux[0] == 'editar'){
        #Trabalha com editar
        $idimovel = $this->PaginaAux[1];
        $rImovel = $banco->BuscaImovelPorId($idimovel);
        $rsImovel = $banco->ArrayData($rImovel);
        
        $titulo = "Editar Imóvel";
        
        $referencia = $rsImovel['referencia'];
        $angariador = $rsImovel['angariador'];
        $idcategoria = $rsImovel['idcategoria'];
        $cep = $rsImovel['cep'];
        $cidade = $rsImovel['cidade'];
        $estado = $rsImovel['estado'];
        $endereco = $rsImovel['endereco'];
        $numero = $rsImovel['numero'];
        $bairro = $rsImovel['bairro'];
        $complemento = $rsImovel['complemento'];
        $ponto_referencia = $rsImovel['ponto_referencia'];
        $entre_ruas = $rsImovel['entre_ruas'];
        $area_util = $rsImovel['area_util'];
        $area_total = $rsImovel['area_total'];
        $proprietario = $rsImovel['proprietario'];
        $telefone = $rsImovel['telefone'];
        $dormitorios = $rsImovel['dormitorios'];
        $garagem = $rsImovel['garagem'];
        $sala = $rsImovel['sala'];
        $churrasqueira = $rsImovel['churrasqueira'];
        $piso = $rsImovel['piso'];
        $esquadrias = $rsImovel['esquadrias'];
        $idade = $rsImovel['idade'];
        $valor = $rsImovel['valor'];
        $descricao = $rsImovel['descricao'];
        $informacoes = $rsImovel['informacoes'];
        #Checkboxes
            #Verificar averbada
        if($rsImovel['averbada'] != ''){
            $averbada = $rsImovel['averbada'];
            $cbaverbada = 'checked';
            $vbaverbada = 'visible';
        }
        if($rsImovel['copa'] == 1){
            $cbcopa = 'checked';
        }
        if($rsImovel['cozinha'] == 1){
            $cbcozinha = 'checked';
        }
        if($rsImovel['lavabo'] == 1){
            $cblavabo = 'checked';
        }
        if($rsImovel['lavanderia'] == 1){
            $cblavanderia = 'checked';
        }
        if($rsImovel['suite'] == 1){
            $cbsuite = 'checked';
        }
        if($rsImovel['closet'] == 1){
            $cbcloset = 'checked';
        }
        if($rsImovel['hidromassagem'] == 1){
            $cbhidromassagem = 'checked';
        }
        if($rsImovel['bwc_social'] == 1){
            $cbbwc_social = 'checked';
        }
        if($rsImovel['lareira'] == 1){
            $cblareira = 'checked';
        }
        if($rsImovel['atico'] == 1){
            $cbatico = 'checked';
        }
        if($rsImovel['armarios'] == 1){
            $cbarmarios = 'checked';
        }
        if($rsImovel['sacada'] == 1){
            $cbsacada = 'checked';
        }
        if($rsImovel['escritorio'] == 1){
            $cbescritorio = 'checked';
        }
        if($rsImovel['dep_empregada'] == 1){
            $cbdep_empregada = 'checked';
        }
        if($rsImovel['playground'] == 1){
            $cbplayground = 'checked';
        }
        if($rsImovel['salao_festas'] == 1){
            $cbsalao_festas = 'checked';
        }
        if($rsImovel['piscina'] == 1){
            $cbpiscina = 'checked';
        }
        if($rsImovel['portao_eletronico'] == 1){
            $cbportao_eletronico = 'checked';
        }
        
        #Imagens
        $imagens = $banco->MontaImagens($idimovel);
        
        #Botões
        $botao_excluir = '<button onclick="excluir(\''.$idimovel.'\')" style="box-shadow: none;background-color: #B6195B;border-color: transparent;border-color: #CCCCCC;border-radius: 0;-webkit-border-radius: 0;outline: none;margin-bottom: 5px;margin-left: 3px;font-size: 13px;padding: 7px 11px;" type="button" class="btn btn-success btn-flat">Excluir</button>';
        if($rsImovel['ativo'] == 0){
            $botao_ativar_inativar = '<button onclick="ativar(\''.$idimovel.'\')" style="box-shadow: none;background-color: #191BB6;border-color: transparent;border-color: #CCCCCC;border-radius: 0;-webkit-border-radius: 0;outline: none;margin-bottom: 5px;margin-left: 3px;font-size: 13px;padding: 7px 11px;" type="button" class="btn btn-success btn-flat">Ativar</button>';;
        }else{
            $botao_ativar_inativar = '<button onclick="inativar(\''.$idimovel.'\')" style="box-shadow: none;background-color: #B0A46A;border-color: transparent;border-color: #CCCCCC;border-radius: 0;-webkit-border-radius: 0;outline: none;margin-bottom: 5px;margin-left: 3px;font-size: 13px;padding: 7px 11px;" type="button" class="btn btn-success btn-flat">Inativar</button>';;
        }
        $botao_voltar = '<button onclick="voltar()" style="box-shadow: none;background-color: #000000;border-color: transparent;border-color: #CCCCCC;border-radius: 0;-webkit-border-radius: 0;outline: none;margin-bottom: 5px;margin-left: 3px;font-size: 13px;padding: 7px 11px;" type="button" class="btn btn-success btn-flat">Voltar</button>'; 
        
    }elseif($this->PaginaAux[0] == 'remover'){
        #Trabalha com remover
        $idimovel = $this->PaginaAux[1];
        
        $banco->RemoveImovel($idimovel);
        $banco->RedirecionaPara('lista-imovel');
    }elseif($this->PaginaAux[0] == 'visualizar'){
        $idimovel = $this->PaginaAux[1];
        $banco->VisualizaFichaImovel($idimovel);
    }elseif($this->PaginaAux[0] == 'ativar'){
        $idimovel = $this->PaginaAux[1];
        $banco->Ativar($idimovel);
    }elseif($this->PaginaAux[0] == 'inativar'){
        $idimovel = $this->PaginaAux[1];
        $banco->Inativar($idimovel);
    }
        
    #Trabalha com Post
	if(isset($_POST["acao"]) && $_POST["acao"] != '' ){
        $referencia = strip_tags(trim(addslashes($_POST["referencia"])));
        $angariador = utf8_decode(strip_tags(trim(addslashes($_POST["angariador"]))));
        $idcategoria = $_POST['categoria'];
        $cep = strip_tags(trim(addslashes($_POST["cep"])));
        $cidade = utf8_decode(strip_tags(trim(addslashes($_POST["cidade"]))));
        $estado = utf8_decode(strip_tags(trim(addslashes($_POST["estado"]))));
        $endereco = utf8_decode(strip_tags(trim(addslashes($_POST["endereco"]))));
        $numero = strip_tags(trim(addslashes($_POST["numero"])));
        $bairro = utf8_decode(strip_tags(trim(addslashes($_POST["bairro"]))));
        $complemento = utf8_decode(strip_tags(trim(addslashes($_POST["complemento"]))));
        $ponto_referencia = utf8_decode(strip_tags(trim(addslashes($_POST["ponto_referencia"]))));
        $entre_ruas = utf8_decode(strip_tags(trim(addslashes($_POST["entre_ruas"]))));
        $area_util = utf8_decode(strip_tags(trim(addslashes($_POST["area_util"]))));
        $area_total = utf8_decode(strip_tags(trim(addslashes($_POST["area_total"]))));
        $proprietario = utf8_decode(strip_tags(trim(addslashes($_POST["proprietario"]))));
        $telefone = strip_tags(trim(addslashes($_POST["telefone"])));
        $dormitorios = strip_tags(trim(addslashes($_POST["dormitorios"])));
        $garagem = strip_tags(trim(addslashes($_POST["garagem"])));
        $sala = strip_tags(trim(addslashes($_POST["sala"])));
        $churrasqueira = strip_tags(trim(addslashes($_POST["churrasqueira"])));
        $piso = utf8_decode(strip_tags(trim(addslashes($_POST["piso"]))));
        $esquadrias = utf8_decode(strip_tags(trim(addslashes($_POST["esquadrias"]))));
        $idade = strip_tags(trim(addslashes($_POST["idade"])));
        #Valor
        $valor = strip_tags(trim(addslashes($_POST["valor"])));
        $valor = str_replace('.', '', $valor);
        $valor = str_replace(',', '.', $valor);
        $descricao = utf8_decode(strip_tags(trim(addslashes($_POST["descricao"]))));
        $informacoes = utf8_decode(strip_tags(trim(addslashes($_POST["informacoes"]))));
        #Checkboxes
        if($_POST['cbaverbada']){
            $averbada = strip_tags(trim(addslashes($_POST["averbada"])));
        }
        if($_POST['copa']){
            $copa = 1;
        }
        if($_POST['cozinha']){
            $cozinha = 1;
        }
        if($_POST['lavabo']){
            $lavabo = 1;
        }
        if($_POST['lavanderia']){
            $lavanderia = 1;
        }
        if($_POST['suite']){
            $suite = 1;
        }
        if($_POST['closet']){
            $closet = 1;
        }
        if($_POST['hidromassagem']){
            $hidromassagem = 1;
        }
        if($_POST['bwc_social']){
            $bwc_social = 1;
        }
        if($_POST['lareira']){
            $lareira = 1;
        }
        if($_POST['atico']){
            $atico = 1;
        }
        if($_POST['armarios']){
            $armarios = 1;
        }
        if($_POST['sacada']){
            $sacada = 1;
        }
        if($_POST['escritorio']){
            $escritorio = 1;
        }
        if($_POST['dep_empregada']){
            $dep_empregada = 1;
        }
        if($_POST['playground']){
            $playground = 1;
        }
        if($_POST['salao_festas']){
            $salao_festas = 1;
        }
        if($_POST['piscina']){
            $piscina = 1;
        }
        if($_POST['portao_eletronico']){
            $portao_eletronico = 1;
        }
        
        #Pega as imagens e arruma num array
        if($_FILES['imagens']['name'][0] !== ""){
            $files=array();
            $fdata=$_FILES['imagens'];
            if(is_array($fdata['name'])){
                for($i=0;$i<count($fdata['name']);++$i){
                    $files[]=array(
                        'name'     => $fdata['name'][$i],
                        'tmp_name' => $fdata['tmp_name'][$i],
                    );
                }
            }else{
                $files[]=$fdata;
            }
        }
        
        if($idimovel){
            #Update
            $banco->AtualizaImovel($idimovel, $idcategoria, $referencia, $angariador, $cep ,$cidade, $estado, $endereco, $numero, $bairro, $complemento, $ponto_referencia, $entre_ruas, $area_util, $area_total, $proprietario, $telefone, $dormitorios, $garagem, $sala, $churrasqueira, $piso, $esquadrias, $idade, $valor, $descricao, $informacoes, $averbada, $copa, $cozinha, $lavabo, $lavanderia, $suite, $closet, $hidromassagem, $bwc_social, $lareira, $atico, $armarios, $sacada, $escritorio, $dep_empregada, $playground, $salao_festas, $piscina, $portao_eletronico, $files);
            $banco->RedirecionaPara('lista-imovel');
        }else{
            #Busca Imovel no banco e verifica se ele existe
            $result = $banco->BuscaImovelPorReferencia($referencia);
            $num_rows = $banco->Linha($result);
            if($num_rows){
                $msg = "Número de referência já cadastrado!";
            }else{
                #Insert
                $banco->InsereImovel($referencia, $angariador, $idcategoria, $cep ,$cidade, $estado, $endereco, $numero, $bairro, $complemento, $ponto_referencia, $entre_ruas, $area_util, $area_total, $proprietario, $telefone, $dormitorios, $garagem, $sala, $churrasqueira, $piso, $esquadrias, $idade, $valor, $descricao, $informacoes, $averbada, $copa, $cozinha, $lavabo, $lavanderia, $suite, $closet, $hidromassagem, $bwc_social, $lareira, $atico, $armarios, $sacada, $escritorio, $dep_empregada, $playground, $salao_festas, $piscina, $portao_eletronico, $files);
                $banco->RedirecionaPara('lista-imovel');
            }
        }
    }#Fim POST
    
    #Monta Categorias
    $select_categorias = $banco->SelectCategorias($idcategoria);
       
    #Imprime valores
	$Conteudo = $banco->CarregaHtml('Imovel/imovel');
    $Conteudo = str_replace("<%TITULO%>", $titulo, $Conteudo);
    $Conteudo = str_replace("<%SELECTCATEGORIAS%>", $select_categorias, $Conteudo);
    $Conteudo = str_replace("<%REFERENCIA%>", $referencia, $Conteudo);
    $Conteudo = str_replace("<%ANGARIADOR%>", $angariador, $Conteudo);
    $Conteudo = str_replace("<%CEP%>", $cep, $Conteudo);
    $Conteudo = str_replace("<%ENDERECO%>", $endereco, $Conteudo);
    $Conteudo = str_replace("<%NUMERO%>", $numero, $Conteudo);
    $Conteudo = str_replace("<%CIDADE%>", $cidade, $Conteudo);
    $Conteudo = str_replace("<%ESTADO%>", $estado, $Conteudo);
    $Conteudo = str_replace("<%BAIRRO%>", $bairro, $Conteudo);
    $Conteudo = str_replace("<%COMPLEMENTO%>", $complemento, $Conteudo);
    $Conteudo = str_replace("<%PONTOREFERENCIA%>", $ponto_referencia, $Conteudo);
    $Conteudo = str_replace("<%ENTRERUAS%>", $entre_ruas, $Conteudo);
    $Conteudo = str_replace("<%AREAUTIL%>", $area_util, $Conteudo);
    $Conteudo = str_replace("<%AREATOTAL%>", $area_total, $Conteudo);
    $Conteudo = str_replace("<%PROPRIETARIO%>", $proprietario, $Conteudo);
    $Conteudo = str_replace("<%TELEFONE%>", $telefone, $Conteudo);
    $Conteudo = str_replace("<%DORMITORIOS%>", $dormitorios, $Conteudo);
    $Conteudo = str_replace("<%GARAGEM%>", $garagem, $Conteudo);
    $Conteudo = str_replace("<%SALA%>", $sala, $Conteudo);
    $Conteudo = str_replace("<%CHURRASQUEIRA%>", $churrasqueira, $Conteudo);
    $Conteudo = str_replace("<%PISO%>", $piso, $Conteudo);
    $Conteudo = str_replace("<%ESQUADRIAS%>", $esquadrias, $Conteudo);
    $Conteudo = str_replace("<%IDADE%>", $idade, $Conteudo);
    $Conteudo = str_replace("<%VALOR%>", $valor, $Conteudo);
    $Conteudo = str_replace("<%DESCRICAO%>", $descricao, $Conteudo);
    $Conteudo = str_replace("<%INFORMACOES%>", $informacoes, $Conteudo);
    #Checkboxes
        #Averbada
    $Conteudo = str_replace("<%AVERBADA%>", $averbada, $Conteudo);
    $Conteudo = str_replace("<%VBAVERBADA%>", $vbaverbada, $Conteudo);
    $Conteudo = str_replace("<%CBAVERBADA%>", $cbaverbada, $Conteudo);
    $Conteudo = str_replace("<%CBCOPA%>", $cbcopa, $Conteudo);
    $Conteudo = str_replace("<%CBCOZINHA%>", $cbcozinha, $Conteudo);
    $Conteudo = str_replace("<%CBLAVABO%>", $cblavabo, $Conteudo);
    $Conteudo = str_replace("<%CBLAVANDERIA%>", $cblavanderia, $Conteudo);
    $Conteudo = str_replace("<%CBSUITE%>", $cbsuite, $Conteudo);
    $Conteudo = str_replace("<%CBCLOSET%>", $cbcloset, $Conteudo);
    $Conteudo = str_replace("<%CBHIDROMASSAGEM%>", $cbhidromassagem, $Conteudo);
    $Conteudo = str_replace("<%CBBWCSOCIAL%>", $cbbwc_social, $Conteudo);
    $Conteudo = str_replace("<%CBLAREIRA%>", $cblareira, $Conteudo);
    $Conteudo = str_replace("<%CBATICO%>", $cbatico, $Conteudo);
    $Conteudo = str_replace("<%CBARMARIOS%>", $cbarmarios, $Conteudo);
    $Conteudo = str_replace("<%CBSACADA%>", $cbsacada, $Conteudo);
    $Conteudo = str_replace("<%CBESCRITORIO%>", $cbescritorio, $Conteudo);
    $Conteudo = str_replace("<%CBDEPEMPREGADA%>", $cbdep_empregada, $Conteudo);
    $Conteudo = str_replace("<%CBPLAYGROUND%>", $cbplayground, $Conteudo);
    $Conteudo = str_replace("<%CBSALAOFESTAS%>", $cbsalao_festas, $Conteudo);
    $Conteudo = str_replace("<%CBPISCINA%>", $cbpiscina, $Conteudo);
    $Conteudo = str_replace("<%CBPORTAOELETRONICO%>", $cbportao_eletronico, $Conteudo);
    #Imagens
    $Conteudo = str_replace("<%IMAGENS%>", $imagens, $Conteudo);
    #Botões
    $Conteudo = str_replace("<%BOTAOEXCLUIR%>", $botao_excluir, $Conteudo);
    $Conteudo = str_replace("<%BOTAOATIVARINATIVAR%>", $botao_ativar_inativar, $Conteudo);
    $Conteudo = str_replace("<%BOTAOVOLTAR%>", $botao_voltar, $Conteudo);
    $Conteudo = utf8_encode($Conteudo);
?>