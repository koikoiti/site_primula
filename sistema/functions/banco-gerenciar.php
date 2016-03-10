<?php
	class bancogerenciar extends banco{
		
        #Busca imagens slider
        function BuscaImagensSlider(){
            $Auxilio = parent::CarregaHtml('Slider/slider-itens');
            $Sql = "SELECT * FROM t_slider";
            $result = parent::Execute($Sql);
            while($rs = parent::ArrayData($result)){
                $Linha = $Auxilio;
                $Linha = str_replace('<%ID%>', $rs['idslider'], $Linha);
                $Linha = str_replace('<%CAMINHO%>', UrlFoto.$rs['caminho'], $Linha);
                $Linha = str_replace('<%TEXTO1%>', $rs['texto1'], $Linha);
                $Linha = str_replace('<%TEXTO2%>', $rs['texto2'], $Linha);
                $Linha = str_replace('<%LINK%>', $rs['link'], $Linha);
                $Slider .= $Linha;
            }
            return $Slider;
        }
        
        #Insere novo slide
        function InsereSlide($texto1, $texto2, $link, $file){
            $SqlNxId = "SHOW TABLE STATUS LIKE 't_slider'";
            $resultNxId = parent::Execute($SqlNxId);
            $rsNxId = parent::ArrayData($resultNxId);
            $nextId = $rsNxId['Auto_increment'];
            
            #Verifica SERVER (web/local)
            if (strpos($_SERVER['DOCUMENT_ROOT'], 'public_html') !== false) {
                $caminhoCriar = $_SERVER['DOCUMENT_ROOT'] . "/arq/slider";
            }else{
                $caminhoCriar = $_SERVER['DOCUMENT_ROOT'] . "/Primula/arq/slider";
            }
            $caminho = "arq/slider";
            
            #Pega extensão da imagem
            preg_match("/\.(gif|png|jpg|jpeg){1}$/i", $file["name"], $ext);
            $caminhoMover = "/$nextId" . "." . $ext[1];
            move_uploaded_file($file["tmp_name"], $caminhoCriar.$caminhoMover);
            $Sql = "INSERT INTO t_slider (texto1, texto2, link, caminho) VALUES ('$texto1', '$texto2', '$link', '".$caminho.$caminhoMover."')";
            parent::Execute($Sql);
        }
        
        #Monta destaques
        function MontaDestaques(){
            $Auxilio = utf8_encode(parent::CarregaHtml('Destaques/destaques-itens'));
            $Sql = "SELECT * FROM t_destaques";
            $result = parent::Execute($Sql);
            while($rsD = parent::ArrayData($result)){
                $Linha = $Auxilio;
                $SqlProduto = "SELECT P.*, M.caminho FROM t_produtos P 
                            INNER JOIN t_imagens_produto M ON P.idproduto = M.idproduto
                            WHERE P.idproduto = " . $rsD['idproduto'] . " 
                            ORDER BY caminho ASC";
                $resultProduto = parent::Execute($SqlProduto);
                $rs = parent::ArrayData($resultProduto);
                $Linha = str_replace('<%ID%>', $rs['idproduto'], $Linha);
                $Linha = str_replace('<%CAMINHO%>', UrlFoto.$rs['caminho'], $Linha);
                $Linha = str_replace('<%CODBARRAS%>', $rs['cod_barras'], $Linha);
                $Destaques .= $Linha;
            }
            return $Destaques;
        }
	}
?>