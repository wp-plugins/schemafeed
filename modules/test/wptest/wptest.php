<?php

class test__wptest extends root {

    var $module_output = 'html';  

    function _module_outputs() {
    
        $this->allowed_outputs[ 'html' ] = array( 'path' => '/modules/test/wptest/views/default.php', 'template' => 'blank' );  
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

        // testing
        return true;
    }                 
    
	function _bus_logic( $params = array() ) {

        /*
        // order of fields
        for( $idx=0; $idx<7; $idx++ ) {
    
            $current_records = wpsf_query( "    select * 
                                                from wp_sf_schemas
                                                " );
                                                
            for( $idx2=0; $idx2<sizeof( $current_records ); $idx2++ ) {
            
                $schema_name = $current_records[$idx2][ 'schemas.schema_name' ];     
                
                $current_fields = wpsf_query( " select * 
                                                from wp_sf_schema_fields
                                                where
                                                type_name = '$schema_name'
                                                " );    
                                                
                for( $idx3=0; $idx3<sizeof( $current_fields ); $idx3++ ) {  
                
                    $schema_field_id = $current_fields[$idx3][ 'schema_fields.schema_field_id' ];
                
                    wpsf_query( "   update wp_sf_schema_fields
                                    set
                                    field_order = '$idx3'
                                    where
                                    schema_field_id = '$schema_field_id'
                                    " );                                                 
                }
            }                                                            
        }    
        */                                            
    }
   
}

?>
