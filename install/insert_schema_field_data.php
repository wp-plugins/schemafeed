<?php

    // csv file is the cut and paste from phpMyadmin CSV data export with no changes to csv settings
    
    $wpsf_schema_field_count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->prefix}sf_schema_fields" ) );
    
    if ( $wpsf_schema_field_count == 0 ) {
    
        $wpsf_handle = fopen( WP_PLUGIN_DIR . '/schemafeed/install/insert_schema_field_data.txt', "r" );
        
        if ( $wpsf_handle ) {
        
            while ( ( $wpsf_buffer = fgets( $wpsf_handle, 4096 ) ) !== false ) {
            
                $wpsf_current_entry_1 = wpsf_csv2array( $wpsf_buffer, '¬' );
    
                $wpsf_current_entry_2 = array(  'schema_field_id' => $wpsf_current_entry_1[0],
                                                'field_name' => $wpsf_current_entry_1[1],
                                                'field_name_display' => $wpsf_current_entry_1[2],
                                                'data_type' => $wpsf_current_entry_1[3],
                                                'sf_data_type' => $wpsf_current_entry_1[4],
                                                'data_type_full' => $wpsf_current_entry_1[5],
                                                'display_type' => $wpsf_current_entry_1[6],
                                                'description' => $wpsf_current_entry_1[7],
                                                'description2' => $wpsf_current_entry_1[8],
                                                'type_from' => $wpsf_current_entry_1[9],
                                                'type_name' => $wpsf_current_entry_1[10],
                                                'schema_id' => $wpsf_current_entry_1[11],
                                                'field_order' => $wpsf_current_entry_1[12],
                                                'plain_display_field' => $wpsf_current_entry_1[13],
                                                'admin_display_field' => $wpsf_current_entry_1[14],
                                                'fields_ass_tmp' => $wpsf_current_entry_1[15],
                                                'auto_fill' => $wpsf_current_entry_1[16],
                                                'meta_field' => $wpsf_current_entry_1[17],
                                                'content_attrib_fill' => $wpsf_current_entry_1[18],
                                                'sf_display_filter' => $wpsf_current_entry_1[19]
                                                );
                
                $wpdb->insert( $table_sf_schema_fields, $wpsf_current_entry_2 );
            }
            
            if (!feof($wpsf_handle)) {
                // read failed
            }
            
            fclose($wpsf_handle);
        }
    }    

?>