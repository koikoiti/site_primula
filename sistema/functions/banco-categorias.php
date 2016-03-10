<?php
    class bancocategorias extends banco{
        
        function BuscaCategorias(){
            #<button type='button' class='btn btn-danger'>Inativar</button>
            #Inativar categ?
            $Sql = "SELECT * FROM fixo_categorias_produto";
            $result = parent::Execute($Sql);
            while($rs = parent::ArrayData($result)){
                $retorno .= "<div class='col-md-2'>*Nome: <input required name='categOld[".$rs['idcategoria']."]' type='text' class='form-control' value='".utf8_encode($rs['nome'])."' /></div><div class='col-sm-1'><br/></div>";
            }
            return $retorno;
        }
        
        function InsereCateg($arrCateg){
            foreach($arrCateg as $categ){
                $Sql = "INSERT INTO fixo_categorias_produto (nome) VALUES ('".utf8_decode(ucwords($categ))."')";
                parent::Execute($Sql);
            }
        }
        
        function UpdateCateg($arrCategOld){
            foreach($arrCategOld as $key => $categ){
                $Sql = "UPDATE fixo_categorias_produto SET nome = '".utf8_decode(ucwords($categ))."' WHERE idcategoria = $key";
                parent::Execute($Sql);
            }
        }
    }
?>