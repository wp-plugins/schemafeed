<?php

    // add schema aux category, these are where the main posts lookups are held

    if( !is_term( 'Schema Aux', 'category' ) ){
    
        $wpsf_args = array( 'description' => 'Schema.org Auxiluary Types', 'slug' => 'Schema-Aux' );
    
        wp_insert_term( 'Schema Aux', 'category', $wpsf_args );
    }

?>