<?php

class wp__help extends root {

    var $module_output = 'html';  

    function _module_outputs() {
    
        $this->allowed_outputs[ 'html' ] = array( 'path' => '/modules/wp/help/views/html.php', 'template' => 'wp1' ); 
    }

    function _init() {

    }

    function _error() {
	
        // assume no errors
        $result = false;
		   
        return $result;
    }           
    
    function _auth() {
    
        $result = true;
	   
        
                
        return $result;
    }                      

	function _bus_logic() {
	   	                                      
        
                
    }        

}

?>