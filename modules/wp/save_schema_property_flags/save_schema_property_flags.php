<?php

class wp__save_schema_property_flags extends root {

    var $module_output = 'html';  

    function _module_outputs() {
    
        $this->allowed_outputs[ 'html' ] = array( 'path' => '/modules/wp/save_schema_property_flags/views/html.php', 'template' => 'blank' );  
    }

    function _init() {
               
    }

    function _error() {
	
        // assume no errors
        $result = false;
        
        $check_input = array();
        
        $check_input[] = array( 'field_name' => 'type_name' );

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

        // ## type_name
        $type_name = wpsf_sql_clean( wpsf_vset( $this->inputs[ 'type_name' ] ) );

        if ( !empty( $type_name ) ) {
        
            foreach( $inputs as $post_key => $post_value ) {

                if ( strstr( $post_key, 'wpsf_' ) ) {
    
                    $current_field_name = str_replace( 'wpsf_', '', $post_key );
        
                    if ( !empty( $current_field_name ) ) {
                    
                        $result = wpsf_query( " select * from ".WPPF."sf_schema_flags
                                                where
                                                type_name = '$type_name' and
                                                field_name = '$current_field_name'
                                                " );
                        
                        if ( empty( $result ) ) {
                            
                            // only store selected
                            if ( $post_value == 1 ) {
                                
                                $result = wpsf_query(   "   insert into ".WPPF."sf_schema_flags
                                                            ( type_name, field_name, state )
                                                            values
                                                            ( '$type_name', '$current_field_name', '$post_value' ) 
                                                            " );
                            }                                                    
                        }
                        else {
                        
                            if ( $post_value == 0 ) {
                                               
                                wpsf_query(    "    delete from ".WPPF."sf_schema_flags
                                                    where
                                                    type_name = '$type_name' and
                                                    field_name = '$current_field_name'
                                                    " );
                            }
                        }
                    }
                }
            }
        }
    }
    
}

?>