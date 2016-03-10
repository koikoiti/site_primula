<?php
	#Definições do Sistema
	date_default_timezone_set('America/Sao_Paulo');
	#Verifica SERVER (web/local)
    if (strpos($_SERVER['DOCUMENT_ROOT'], 'public_html') !== false) {
        #WEB
        define('UrlPadrao' , "http://www.primulatkc.com.br/scylla/");
        define('UrlFoto', 'http://www.primulatkc.com.br/scylla/');
        define('UrlPdf', 'http://www.primulatkc.com.br/scylla/');
    	
    	#Definições do Banco de Dados
    	define('DB_Host' , "localhost");
    	define('DB_Database' , "primu621_scylla");
    	define('DB_User' , "primu621_scylla");
    	define('DB_Pass' , '$RT%,.;');
    }else{
        #LOCAL
        define('UrlPadrao' , "http://localhost/site_primula/");
        define('UrlFoto', 'http://localhost/site_primula/');
    	define('UrlPdf', 'http://127.0.0.1/site_primula/');
        
    	#Definições do Banco de Dados
    	define('DB_Host' , "localhost");
    	define('DB_Database' , "primula");
    	define('DB_User' , "root");
    	define('DB_Pass' , '');
    }
    
    #Paginacao
    define('Limite', 15);
    define('QtdPag', 5);
?>