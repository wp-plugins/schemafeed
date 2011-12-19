<?php

    // This is the cut and paste from phpMyadmin CSV data export ( fields terminated by ¬, empty enclosed by )
    
    $wpsf_schema_field_count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->prefix}sf_schema_fields" ) );
    
    if ( $wpsf_schema_field_count == 0 ) {
    
        $wpsf_handle = fopen( WP_PLUGIN_DIR . '/schemafeed/install/insert_schema_field_data.txt', "r" );
        
        $new_schema_props = array();
        
        // ## gather entries together first
        if ( $wpsf_handle ) {
        
            while ( ( $wpsf_buffer = fgets( $wpsf_handle, 4096 ) ) !== false ) {
            
                $wpsf_current_entry_1 = wpsf_csv2array( $wpsf_buffer, '¬' );
    
                $current_props = array();
                $current_props[ 'schema_field_id' ] = $wpsf_current_entry_1[0];
                $current_props[ 'field_name' ] = $wpsf_current_entry_1[1];
                $current_props[ 'field_name_display' ] = $wpsf_current_entry_1[2];
                $current_props[ 'data_type' ] = $wpsf_current_entry_1[3];
                $current_props[ 'sf_data_type' ] = $wpsf_current_entry_1[4];
                $current_props[ 'data_type_full' ] = $wpsf_current_entry_1[5];
                $current_props[ 'display_type' ] = $wpsf_current_entry_1[6];
                $current_props[ 'description' ] = $wpsf_current_entry_1[7];
                $current_props[ 'description2' ] = $wpsf_current_entry_1[8];
                $current_props[ 'type_from' ] = $wpsf_current_entry_1[9];
                $current_props[ 'type_name' ] = $wpsf_current_entry_1[10];
                $current_props[ 'schema_id' ] = $wpsf_current_entry_1[11];
                $current_props[ 'field_order' ] = $wpsf_current_entry_1[12];
                $current_props[ 'plain_display_field' ] = $wpsf_current_entry_1[13];
                $current_props[ 'admin_display_field' ] = $wpsf_current_entry_1[14];
                $current_props[ 'fields_ass_tmp' ] = $wpsf_current_entry_1[15];
                $current_props[ 'auto_fill' ] = $wpsf_current_entry_1[16];
                $current_props[ 'meta_field' ] = $wpsf_current_entry_1[17];
                $current_props[ 'content_attrib_fill' ] = $wpsf_current_entry_1[18];
                $current_props[ 'sf_display_filter' ] = $wpsf_current_entry_1[19];
                $current_props[ 'not_display' ] = $wpsf_current_entry_1[20];
                                                
                $new_schema_props[] = $current_props;                                               
            }
            
            if (!feof($wpsf_handle)) {
                // read failed
            }
            
            fclose($wpsf_handle);
        }
        
        // ## sync from new to existing
        for( $idx=0; $idx<sizeof( $new_schema_props ); $idx++ ) {
        
            $current_prop = $new_schema_props[$idx];            
                
            $wpsf_schema_prop_count = $wpdb->get_var( $wpdb->prepare( " select count(*) from {$wpdb->prefix}sf_schema_fields
                                                                        where
                                                                        schema_field_id = '$schema_field_id'
                                                                        " ) );
    
            if ( $wpsf_schema_prop_count == 0 ) {

                $wpsf_current_entry = array(    'schema_field_id' => $current_prop[ 'schema_field_id' ],
                                                'field_name' => $current_prop[ 'field_name' ],
                                                'field_name_display' => $current_prop[ 'field_name_display' ],
                                                'data_type' => $current_prop[ 'data_type' ],
                                                'sf_data_type' => $current_prop[ 'sf_data_type' ],
                                                'data_type_full' => $current_prop[ 'data_type_full' ],
                                                'display_type' => $current_prop[ 'display_type' ],
                                                'description' => $current_prop[ 'description' ],
                                                'description2' => $current_prop[ 'description2' ],
                                                'type_from' => $current_prop[ 'type_from' ],
                                                'type_name' => $current_prop[ 'type_name' ],
                                                'schema_id' => $current_prop[ 'schema_id' ],
                                                'field_order' => $current_prop[ 'field_order' ],
                                                'plain_display_field' => $current_prop[ 'plain_display_field' ],
                                                'admin_display_field' => $current_prop[ 'admin_display_field' ],
                                                'fields_ass_tmp' => $current_prop[ 'fields_ass_tmp' ],
                                                'auto_fill' => $current_prop[ 'auto_fill' ],
                                                'meta_field' => $current_prop[ 'meta_field' ],
                                                'content_attrib_fill' => $current_prop[ 'content_attrib_fill' ],
                                                'sf_display_filter' => $current_prop[ 'sf_display_filter' ],
                                                'not_display' => $current_prop[ 'not_display' ]
                                                );
                
                $wpdb->insert( $wpdb->prefix."sf_schema_fields", $wpsf_current_entry );
            }
            else {
            
                // updating to do in next version
            }                
        }
        
        // ## sync from existing to new
        $schemas_props = wpsf_query( "select * from {$wpdb->prefix}sf_schema_fields" );
    
        for( $idx=0; $idx<sizeof( $schemas_props ); $idx++ ) {
            
            $db_schema_field_id = $schemas_props[$idx][ 'schema_fields.schema_field_id' ];
            $exist = 0;              
                    
            for( $idx2=0; $idx2<sizeof( $new_schema_props ); $idx2++ ) {
            
                $new_schema_field_id = $new_schema_props[$idx2][ 'schema_field_id' ];
                
                if ( $db_schema_field_id == $new_schema_field_id ) {
                    // exist
                    $exist = 1;
                    break;
                }
            }
            
            if ( !$exist ) {
                // deprecated, delete from db
                wpsf_query( "delete from {$wpdb->prefix}sf_schema_fields where schema_field_id = '$db_schema_field_id'" );
            }
        }
    }    

?>