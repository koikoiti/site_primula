<?php
	class bancousuario extends banco{
		
        #Lista os usuarios
        function ListaUsuarios(){
            $Auxilio = parent::CarregaHtml("Usuario/itens/lista-usuario-itens");
            
            $Sql = "SELECT U.*, S.nome AS setor FROM t_usuarios U 
                    INNER JOIN fixo_setor S ON U.idsetor = S.idsetor
                    WHERE login != 'admin'";
            $result = parent::Execute($Sql);
            $num_rows = parent::Linha($result);
            if($num_rows){
                while($rs = parent::ArrayData($result)){
                    $Linha = $Auxilio;
                    $Linha = str_replace("<%NOME%>", $rs['nome_exibicao'], $Linha);
                    $Linha = str_replace("<%LOGIN%>", $rs['login'], $Linha);
                    $Linha = str_replace("<%ID%>", $rs['idusuario'], $Linha);
                    $Linha = str_replace("<%SETOR%>", $rs['setor'], $Linha);
                    if($rs['ativo'] == 1){
                        $ativo = 'Ativo';
                        $botao_AI = '<a href="#" onclick="remover('.$rs['idusuario'].', \''.$rs['nome_exibicao'].'\')">Inativar</a>';
                    }else{
                        $ativo = 'Inativo';
                        $botao_AI = '<a href="#" onclick="ativar('.$rs['idusuario'].', \''.$rs['nome_exibicao'].'\')">Ativar</a>';
                    }
                    $Linha = str_replace("<%ATIVOINATIVO%>", $ativo, $Linha);
                    $Linha = str_replace("<%BOTAOATIVARINATIVAR%>", $botao_AI, $Linha);
                    $Linha = str_replace("<%TIPOVENDEDOR%>", $rs['tipo_vendedor'], $Linha);                    
                    $Usuarios .= $Linha;
                }
            }else{
                $Usuarios = '<tr class="odd gradeX">
                                <td colspan="6">Não foram encontrados usuários cadastrados.</td>
                             <tr>';
            }
            return utf8_encode($Usuarios);
        }
        
        #Insere Usuario no banco
        function InsereUsuario($nome, $login, $senha, $idsetor, $tipo_vendedor){
            $senha = sha1($senha);
            $Sql = "INSERT INTO t_usuarios (nome_exibicao, login, senha, idsetor, tipo_vendedor) VALUES ('$nome','$login','$senha', '$idsetor', '$tipo_vendedor')";
            if(parent::Execute($Sql)){
                return true;
            }else{
                parent::ChamaManutencao();
            }
        }
        
        #Atualiza Usuario
        function AtualizaUsuario($idusuario, $nome, $login, $senha, $idsetor, $tipo_vendedor){
            if($senha == ""){
                $Sql = "UPDATE t_usuarios SET nome_exibicao = '$nome', login = '$login', idsetor = '$idsetor', tipo_vendedor = '$tipo_vendedor' WHERE idusuario = '$idusuario'";
            }else{
                $senha = sha1($senha);
                $Sql = "UPDATE t_usuarios SET nome_exibicao = '$nome', login = '$login', senha = '$senha', idsetor = '$idsetor', tipo_vendedor = '$tipo_vendedor' WHERE idusuario = '$idusuario'";
            }
            if(parent::Execute($Sql)){
                return true;
            }else{
                parent::ChamaManutencao();
            }
        }
        
        #Remove Usuario
        function RemoveUsuario($idusuario){
            $Sql = "UPDATE t_usuarios SET ativo = 0 WHERE idusuario = '$idusuario'";
            if(parent::Execute($Sql)){
                return true;
            }else{
                parent::ChamaManutencao();
            }
        }
        
        #Ativa Usuario
        function AtivaUsuario($idusuario){
            $Sql = "UPDATE t_usuarios SET ativo = 1 WHERE idusuario = '$idusuario'";
            if(parent::Execute($Sql)){
                return true;
            }else{
                parent::ChamaManutencao();
            }
        }
        
        #Busca Usuario por ID
        function BuscaUsuarioPorId($idusuario){
            $Sql = "SELECT * FROM t_usuarios WHERE idusuario = '$idusuario'";
            $result = parent::Execute($Sql);
            return $result;
        }
        
        #Monta Select Setor
        function SelectSetor($idsetor){
            $Sql = "SELECT * FROM fixo_setor";
            $result = parent::Execute($Sql);
            $select_setor = "<select id='setor' onchange='tipo()' name='setor' class='form-control'>";
            $select_setor .= "<option value=''>Selecione o Setor</option>";
            while($rs = parent::ArrayData($result)){
                if($idsetor == $rs['idsetor']){
                    $select_setor .= "<option selected value='".$rs['idsetor']."'>".$rs['nome']."</option>";
                }else{
                    $select_setor .= "<option value='".$rs['idsetor']."'>".$rs['nome']."</option>";
                }
            }
            $select_setor .= "</select>";
            return $select_setor;
        }
	}
?>