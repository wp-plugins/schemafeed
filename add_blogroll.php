<?php

    if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die( 'This page cannot be called directly.' ); }

    function wpsf_add_schema_significant_links( $links ) {
        
        // ## "significantLinks" property

        $links = wpsf_mb_replace( '<li ', '<li itemprop="significantLinks"', $links );                  
                  
        return $links;
    }
    
    add_filter( 'wp_list_bookmarks', 'wpsf_add_schema_significant_links' );

?>