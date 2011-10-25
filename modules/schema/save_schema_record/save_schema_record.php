<?php

class schema__save_schema_record extends root {

    var $module_output = 'html';  

    function _module_outputs() {
    
        $this->allowed_outputs[ 'html' ] = array( 'path' => '/modules/schema/save_schema_record/views/html.php', 'template' => 'blank' );  
        $this->allowed_outputs[ 'json' ] = array( 'path' => '/modules/schema/save_schema_record/views/json.php', 'template' => 'blank' );
    }

    function _init() {
        
    }

    function _error() {
	
        // assume no errors
        $result = false;
        
        $check_input = array();
           
        $errors = wpsf_error_check( $this->inputs, $check_input, $this );
        
        if ( $errors ) {
        
            $this->set_result( 'INPUT_ERROR' );
            
            $this->result[ 'errors' ] = $errors;
            $this->module_output = 'errors'; 
            
            $result = true; 
        }
        
        return $result;
    }           
    
    function _auth() {
    
        $result = true;
          
        return $result;
    }       

	function _bus_logic() {
	
        $inputs = $this->inputs;	   
        $post_vars = array();

        $post_vars[ 'post_title' ] = '';
        $post_vars[ 'post_content' ] = '';
        $post_vars[ 'post_status' ] = 'publish'; 
        $post_vars[ 'post_author' ] = 1;  
        $post_vars[ 'post_category' ] = array( 8,39 );

        // gather fields together
        foreach( $inputs as $key => $value ) {
        
            if ( strstr( $key, 'wpsf__' ) ) {
            
                $field_name = wpsf_mb_replace( 'wpsf__', '', $key );
                
                if ( $field_name == 'name' ) {
                    $post_vars[ 'post_title' ] = $value;
                }
                
                if ( $field_name == 'description' ) {
                    $post_vars[ 'post_content' ] = $value;
                }
            }                       
        }    

        // Insert the post into the database
        wp_insert_post( $post_vars );
    }   
    
}

?>