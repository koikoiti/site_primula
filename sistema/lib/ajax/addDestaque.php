<?php
	include('../../functions/banco.php');
	include('../../conf/tags.php');
	
    $banco = new banco;
	$banco->Conecta();
	session_start('login');
    
    $cod = $_POST['cod'];
    
    $SqlVerifica = "SELECT D.* FROM t_destaques D 
                    INNER JOIN t_produtos P ON D.idproduto = P.idproduto 
                    WHERE P.cod_barras = '$cod'";
    $resultVerifica = $banco->Execute($SqlVerifica);
    $linhasVerifica = $banco->Linha($resultVerifica);
    
    if($linhasVerifica){
        echo 444;
    }else{
        $SqlID = "SELECT * FROM t_produtos WHERE cod_barras = '$cod'";
        $resultID = $banco->Execute($SqlID);
        $linhaID = $banco->Linha($resultID);
        if($linhaID){
            $rsID = $banco->ArrayData($resultID);
            if($rsID['ativo'] == 1){
                $SqlInsert = "INSERT INTO t_destaques (idproduto) VALUES (".$rsID['idproduto'].")";
                $banco->Execute($SqlInsert);
                echo 1;
            }else{
                echo 555;
            }
        }else{
            echo 666;
        }
    }
?>