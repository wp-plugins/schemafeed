<?php

class schema__schema_search extends root {

    var $module_output = 'html';
    var $stop_infin = 0;

    function _module_outputs() {

        $this->allowed_outputs[ 'html' ] = array( 'path' => '/modules/schema/schema_search/views/html.php', 'template' => 'blank' );
        $this->allowed_outputs[ 'json' ] = array( 'path' => '/modules/schema/schema_search/views/json.php', 'template' => 'blank' );
    }

    function _init() {

    }

    function _error() {

        // assume no errors
        $result = false;

        $check_input = array();
        $check_input[] = array( 'field_name' => 'search', 'nonempty' => '1' );

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
           
        return $result;
    }      

	function _bus_logic() {

        $inputs = $this->inputs;	
	
        $search = $inputs[ 'search' ]; 

        $records_1 = wpsf_query( "  select * 
                                    from ".WPPF."posts
                                    where
                                    post_content like '%{$search}%' or
                                    post_title like '%{$search}%'
                                    " );
               
        $this->result[ 'records' ] = $records_1;     
    }
    
}

?>
