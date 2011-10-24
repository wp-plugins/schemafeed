<?php

    if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die( 'This page cannot be called directly.' ); }

    function wpsf_add_schema_title( $the_title ) {
        
        // ## "name" property
        $the_title = '<span itemprop="name">'.$the_title.'</span>';                                          
                    
        return $the_title;
    }
    
    add_filter( 'the_title', 'wpsf_add_schema_title' );

?>