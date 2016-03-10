<?php
    $titulo = "Cadastro de Aviso";
    $botao_voltar = '<button onclick="voltar()" style="box-shadow: none;background-color: #000000;border-color: transparent;border-color: #CCCCCC;border-radius: 0;-webkit-border-radius: 0;outline: none;margin-bottom: 5px;margin-left: 3px;font-size: 13px;padding: 7px 11px;" type="button" class="btn btn-success btn-flat">Voltar</button>';
    $msg= '';
    $botao_salvar = '<button type="submit" class="btn btn-success btn-flat"><i class="fa fa-check"></i> Salvar</button>';
    
	#include das funcoes da tela 
	include('functions/banco-aviso.php');

	#Instancia o objeto
	$banco = new bancoaviso();
    
    if($this->PaginaAux[0] == 'editar'){
        $idaviso = $this->PaginaAux[1];
        $rs = $banco->BuscaAvisoPorId($idaviso);
        if($rs['idusuario_criar'] == $_SESSION['idusuario'] || $_SESSION['idsetor'] == 1){
            $disabled = '';
        }else{
            $disabled = 'disabled';
            $msg = '<div class="alert alert-warning alert-dismissible" role="alert">
                    <strong>Atenção!</strong> Somente o usuário que criou este aviso pode editá-lo.</div>';
            $botao_salvar = '';
        }
        $datahoraAux = explode(' ', $rs['data']);
        $aviso = utf8_encode($rs['aviso']);
        $data = $datahoraAux[0];
        if($datahoraAux[1] == '00:00:00'){
            $hora = '';
        }else{
            $hora = $datahoraAux[1];
        }
        $titulo = 'Edita Aviso';
    }elseif($this->PaginaAux[0] == 'excluir'){
    	$idaviso = $this->PaginaAux[1];
    	$banco->Excluir($idaviso);
    	$banco->RedirecionaPara('lista-avisos');
    }
    
    if(isset($_POST["acao"]) && $_POST["acao"] != '' ){
        $aviso = utf8_decode(strip_tags(trim(addslashes($_POST["aviso"]))));
        $data = $_POST['data'];
        $hora = $_POST['hora'];
        $arrFunc = $_POST['funcionarios'];
        
        if($idaviso){
            $banco->AtualizaAviso($idaviso, $aviso, $data, $hora, $arrFunc);
        }else{
            $banco->InsereAviso($aviso, $data, $hora, $arrFunc);
        }
    }
    
    $cbfuncionarios = $banco->MontaCBFuncionarios($idaviso, $disabled);
    
	#Imprime valores
	$Conteudo = utf8_encode($banco->CarregaHtml('aviso'));
    $Conteudo = str_replace("<%TITULO%>", $titulo, $Conteudo);
    $Conteudo = str_replace("<%AVISO%>", $aviso, $Conteudo);
    $Conteudo = str_replace("<%DATA%>", $data, $Conteudo);
    $Conteudo = str_replace("<%HORA%>", $hora, $Conteudo);
    $Conteudo = str_replace("<%BOTAOEXCLUIR%>", $botao_excluir, $Conteudo);
    $Conteudo = str_replace("<%BOTAOVOLTAR%>", $botao_voltar, $Conteudo);
    $Conteudo = str_replace("<%BOTAOSALVAR%>", $botao_salvar, $Conteudo);
    $Conteudo = str_replace("<%CBFUNCIONARIOS%>", $cbfuncionarios, $Conteudo);
    $Conteudo = str_replace("<%DISABLED%>", $disabled, $Conteudo);
    $Conteudo = str_replace("<%MSG%>", utf8_encode($msg), $Conteudo);
?>