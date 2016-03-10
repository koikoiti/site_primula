<?php
    class bancofinalizar extends banco{
        
        function MontaSaida(){
            #Inicia mPDF
            require_once('app/mpdf60/mpdf.php');
            #Define a default page size/format by array - page will be 190mm wide x 236mm height
            $mpdf = new mPDF('utf-8', array(80, 300), '', '', 5, 5, 8, 8, 0, 0);
                        
            #HTML Auxilio
            $Auxilio = utf8_encode(parent::CarregaHtml('Vendas/saida'));
            
            $mpdf->WriteHTML($Auxilio);
            
            $actualHeight = $mpdf->y + 8; // Current writing position + a bottom margin in mm
            $mpdf = new mPDF('utf-8', array(80, $actualHeight), '', '', 5, 5, 8, 8, 0, 0);
            
            $mpdf->WriteHTML($Auxilio);
            
            #$mpdf->SetFooter(' ');
            $mpdf->Output();
            exit;
        }
    }
?>