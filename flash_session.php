<?php

    add_action( 'init', 'wpsf_app_session' );
    
    function wpsf_app_session() {
    
        if ( !isset( $_SESSION[ 'wpsf' ] ) ) {
            $_SESSION[ 'wpsf' ] = array();
        }
        
        include( WP_PLUGIN_DIR . '/schemafeed/core/premod.php' );
    }

?>