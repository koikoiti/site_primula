<?php
	class bancoaviso extends banco{
		
        #Monta checkboxes dos funcionários
        function MontaCBFuncionarios($idaviso, $disabled){
            $arrComp = array();
            $Sql = "SELECT * FROM t_usuarios WHERE login <> 'admin' AND login <> '{$_SESSION['nomeusuario']}'";
            $result = parent::Execute($Sql);
            
            #Check, caso edit
            if($idaviso){
                $SqlComp = "SELECT * FROM t_avisos_usuarios WHERE idaviso = $idaviso";
                $resultComp = parent::Execute($SqlComp);
                while($rsComp = parent::ArrayData($resultComp)){
                    $arrComp[] = $rsComp['idusuario'];
                }
            }
            while($rs = parent::ArrayData($result)){
                if(in_array($rs['idusuario'], $arrComp)){
                    $retorno .= "<label style='margin-left: 30px; font-size: 14px;'><input $disabled checked type='checkbox' name='funcionarios[]' value='{$rs['idusuario']}' /> {$rs['nome_exibicao']}</label>";
                }else{
                    $retorno .= "<label style='margin-left: 30px; font-size: 14px;'><input $disabled type='checkbox' name='funcionarios[]' value='{$rs['idusuario']}' /> {$rs['nome_exibicao']}</label>";
                }
            }
            return utf8_encode($retorno);
        }
        
        #Insert
        function InsereAviso($aviso, $data, $hora, $arrFunc){
            #Insere aviso
            $Sql = "INSERT INTO t_avisos (aviso, data, idusuario_criar) VALUES ('$aviso', '$data $hora', '{$_SESSION['idusuario']}')";
            parent::Execute($Sql);
            $lastID = mysql_insert_id();
            #Insere para o usuario que criou
            $SqlCriou = "INSERT INTO t_avisos_usuarios (idaviso, idusuario) VALUES ('$lastID', '{$_SESSION['idusuario']}')";
            parent::Execute($SqlCriou);
            
            #Insere usuarios compartilhados
            if(!empty($arrFunc)){
                foreach($arrFunc as $func){
                    $SqlComp = "INSERT INTO t_avisos_usuarios (idaviso, idusuario) VALUES ('$lastID', '$func')";
                    parent::Execute($SqlComp);
                }
            }
            parent::RedirecionaPara('lista-avisos');
        }
        
        #Update
        function AtualizaAviso($idaviso, $aviso, $data, $hora, $arrFunc){
            #Atualiza aviso
            $Sql = "UPDATE t_avisos SET aviso = '$aviso', data = '$data $hora' WHERE idaviso = $idaviso";
            parent::Execute($Sql);
            
            $SqlCriou = "SELECT idusuario_criar FROM t_avisos WHERE idaviso = $idaviso";
            $resultCriou = parent::Execute($SqlCriou);
            $rs = parent::ArrayData($resultCriou);
            
            if($rs['idusuario_criar'] == $_SESSION['idusuario']){
                $where = "AND idusuario <> {$rs['idusuario_criar']}";
            }else{
                $where = "";
            }
            
            #Limpa Comps
            $SqlLimpa = "DELETE FROM t_avisos_usuarios WHERE idaviso = $idaviso $where";
            parent::Execute($SqlLimpa);
            
            #Insere usuarios compartilhados
            if(!empty($arrFunc)){
                foreach($arrFunc as $func){
                    $SqlComp = "INSERT INTO t_avisos_usuarios (idaviso, idusuario) VALUES ('$idaviso', '$func')";
                    parent::Execute($SqlComp);
                }
            }
            parent::RedirecionaPara('lista-avisos');
        }
        
        #Listagem
        function ListaAvisos(){
            $Auxilio = parent::CarregaHtml('itens/lista-avisos-itens');
            if($_SESSION['idsetor'] > 1){
                $innerjoin = "INNER JOIN t_avisos_usuarios U ON U.idaviso = A.idaviso WHERE U.idusuario = '{$_SESSION['idusuario']}'";
            }
            $Sql = "SELECT A.* FROM t_avisos A $innerjoin";
            $result = parent::Execute($Sql);
            $linha = parent::Linha($result);
            if($linha){
                while($rs = parent::ArrayData($result)){
                    $Linha = $Auxilio;
                    $Linha = str_replace('<%ID%>', $rs['idaviso'], $Linha);
                    $Linha = str_replace('<%AVISO%>', $rs['aviso'], $Linha);
                    $datahoraAux = explode(' ', $rs['data']);
                    $Linha = str_replace('<%DATA%>', date("d/m/Y", strtotime($datahoraAux[0])), $Linha);
                    if($datahoraAux[1] == '00:00:00'){
                        $hora = '';
                    }else{
                        $hora = $datahoraAux[1];
                    }
                    $Linha = str_replace('<%HORA%>', $hora, $Linha);
                    $Linha = str_replace('<%CRIADOPOR%>', parent::BuscaUsuarioPorId($rs['idusuario_criar']), $Linha);
                    if($rs['idusuario_finalizar'] == 0){
                        $u_finalizar = '';
                    }else{
                        $u_finalizar = parent::BuscaUsuarioPorId($rs['idusuario_finalizar']);
                    }
                    $Linha = str_replace('<%FINALIZADOPOR%>', $u_finalizar, $Linha);
                    $SqlComp = "SELECT * FROM t_avisos_usuarios WHERE idaviso = {$rs['idaviso']} AND idusuario <> {$rs['idusuario_criar']}";
                    $resultComp = parent::Execute($SqlComp);
                    $comps = '';
                    while($rsComp = parent::ArrayData($resultComp)){
                        $comps .= parent::BuscaUsuarioPorId($rsComp['idusuario']) . ' / ';
                    }
                    $comps = rtrim($comps, ' / ');
                    $Linha = str_replace('<%COMPARTILHADOCOM%>', $comps, $Linha);
                    $Avisos .= $Linha;
                }
            }else{
                $Avisos = '<tr class="odd gradeX">
                                <td colspan="7">Não foram encontrados avisos cadastrados.</td>
                             <tr>';
            }
            
            return utf8_encode($Avisos);
        }
        
        #Busca aviso por ID
        function BuscaAvisoPorId($idaviso){
            $Sql = "SELECT * FROM t_avisos WHERE idaviso = $idaviso";
            $result = parent::Execute($Sql);
            return parent::ArrayData($result);
        }
        
        #Excluir aviso
        function Excluir($idaviso){
        	$Sql = "DELETE FROM t_avisos WHERE idaviso = $idaviso";
        	parent::Execute($Sql);
        }
        
	}
?>