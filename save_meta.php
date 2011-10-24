<?php

    add_action( 'save_post', 'wpsf_submit_product_add', 10, 2 );
    
    function wpsf_submit_product_add( $post_ID, $post ) {
       
        global $current_screen, $wpdb, $flag;
        
        if ($flag == 0) {
    
            $post_data = $_POST;
            
            foreach( $post_data as $key => $value ) {
                
                if ( strstr( $key, '_wpsf_' ) ) {
                                            
                    if ( !empty( $value ) ) {
                        update_post_meta( $post_ID, $key, $value );
                    }
                }
            }
            
            // save schema type
            if ( !empty( $post_data[ '_wpsf_schema_type' ] ) ) {
                update_post_meta( $post_ID, '_wpsf_schema_type', $post_data[ '_wpsf_schema_type' ] );
            }
        }
        
        $flag = 1;
    }

?>