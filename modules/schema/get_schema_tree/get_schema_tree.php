<?php

class schema__get_schema_tree extends root {

    var $module_output = 'html';
    var $stop_infin = 0;

    function _module_outputs() {

        $this->allowed_outputs[ 'html' ] = array( 'path' => '/modules/schema/get_schema_tree/views/html.php', 'template' => 'blank' );
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
           
        return $result;
    }      

	function _bus_logic() {

        $records_1 = wpsf_query( "  select * 
                                    from ".WPPF."sf_schemas
                                    where
                                    schema_parent = ''
                                    order by schema_order
                                    " );
                               
        if ( isset( $records_1[0] ) ) {       
                                
            $this->schema[] = $records_1[0];
            
            $this->schema_tree( $records_1[0][ 'schemas.schema_name' ], '~' );
        }

        $this->result[ 'schema' ] = $this->schema;         
    }
       
    function schema_tree( $schema_name, $padding = '' ) {
              
        if ( $this->stop_infin++ > 1000 ) { return; }               
               
        $records_1 = wpsf_query(    "   select * 
                                        from ".WPPF."sf_schemas
                                        where
                                        schema_parent = '$schema_name'
                                        order by schema_order
                                        " );

        for( $idx=0; $idx<sizeof( $records_1 ); $idx++ ) {
            
            $schema_name_1 = $records_1[$idx][ 'schemas.schema_name' ];
            $schema_name_2 = $records_1[$idx][ 'schemas.schema_name_display' ];
            
            $records_1[$idx][ 'schemas.schema_name' ] = $schema_name_1;
            $records_1[$idx][ 'schemas.schema_name_display' ] = $padding.$schema_name_2;
            
            $this->schema[] = $records_1[$idx];
                
            $this->schema_tree( $schema_name_1, $padding.'~' );
        }
        
        return;
    }
    
}

?>
