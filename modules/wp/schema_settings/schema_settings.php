<?php

class wp__schema_settings extends root {

    var $module_output = 'html';  

    function _module_outputs() {
    
        $this->allowed_outputs[ 'html' ] = array( 'path' => '/modules/wp/schema_settings/views/html.php', 'template' => 'wp1' ); 
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
	   	                                      
        $result = wpsf_query( "select * from ".WPPF."sf_settings" );                    
            
        $this->result[ 'records' ] = $result;                             
    }        

}

?>