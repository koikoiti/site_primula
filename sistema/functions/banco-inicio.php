<?php
	class bancoinicio extends banco{
		
        #Fecha Sessao
		function FechaSessao(){
			$_SESSION = array();
			session_destroy();
            parent::RedirecionaPara('login');
		}
        
        #Mostra Avisos
        function MostraAvisos(){
            $Auxilio = parent::CarregaHtml('itens/inicio-aviso-itens');
            if($_SESSION['idsetor'] == 1){
                $where = "";
            }else{
                $where = "AND U.idusuario = " . $_SESSION['idusuario'];
            }
            $data = date("Y-m-d", strtotime("+ 3 day"));
            $where .= " AND A.data <= '$data'";
            $Sql = "SELECT DISTINCT A.* FROM t_avisos A 
                    INNER JOIN t_avisos_usuarios U ON A.idaviso = U.idaviso
                    WHERE 1 $where";
            $result = parent::Execute($Sql);
            while($rs = parent::ArrayData($result)){
                $Linha = $Auxilio;
                $Linha = str_replace('<%AVISO%>', $rs['aviso'], $Linha);
                $auxDataHora = explode(' ', $rs['data']);
                $Linha = str_replace('<%DATA%>', date("d/m/Y", strtotime($auxDataHora[0])), $Linha);
                $Linha = str_replace('<%HORA%>', $auxDataHora[1], $Linha);
                $Linha = str_replace('<%CRIADOPOR%>', parent::BuscaUsuarioPorId($rs['idusuario_criar']), $Linha);
                $Linha = str_replace('<%COMPARTILHADO%>', '', $Linha);
                $Linha = str_replace('<%FINALIZADO%>', '', $Linha);
                $avisos .= $Linha;
            }
            
            return utf8_encode($avisos);
        }
	}
?>