<?php

class wp__save_schema_settings extends root {

    var $module_output = 'html';  

    function _module_outputs() {
    
        $this->allowed_outputs[ 'html' ] = array( 'path' => '/modules/wp/save_schema_settings/views/html.php', 'template' => 'blank' );  
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
                        
            $result = true; 
        }
        
        return $result;
    }           
    
    function _auth() {
    
        $result = true;
                
        // auth ok 
               
        return $result;
    }       
    
	function _bus_logic() {
	
        $inputs = $this->inputs;	   

        // ## post_template
        $post_template = wpsf_vset( $this->inputs[ 'post_template' ] );
        
        // ## post_template
        $all_schemas_off = wpsf_vset( $this->inputs[ 'all_schemas_off' ] );

        $result = wpsf_query( "select * from ".WPPF."sf_settings" );
        
        if ( empty( $result ) ) {
            
            $result = wpsf_query(   "   insert into ".WPPF."sf_settings
                                        ( post_template, all_schemas_off )
                                        values
                                        ( '$post_template', '$all_schemas_off' ) 
                                        " );
        }
        else {
        
            $update_fields = array();               
                  
            // ## post_template    
            $update_fields[] = " post_template = '{$post_template}'";            
            
            // ## all_schemas_off    
            $update_fields[] = " all_schemas_off = '{$all_schemas_off}'";                           
                   
            $update_fields_sql = implode( ',', $update_fields );                       
                   
            if ( !empty( $update_fields_sql ) ) {
                               
                $records_1 = wpsf_query(    "   update ".WPPF."sf_settings
                                                set
                                                $update_fields_sql
                                                " );
            }
        }
    }
    
}

?>