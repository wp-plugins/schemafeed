<?php

    // ## add page related style sheet
    function wpsf_add_schema_style() {
    
        $style_url = plugins_url( 'css/style.css', __FILE__ );
        $style_file = WP_PLUGIN_DIR . '/schemafeed/css/style.css';

        if ( file_exists( $style_file ) ) {
            wp_register_style( 'wpsf_stylesheets', $style_url );
            wp_enqueue_style( 'wpsf_stylesheets' );
        }
    }
    
    add_action( 'wp_print_styles', 'wpsf_add_schema_style' );
    
    add_action( 'admin_enqueue_scripts', 'wpsf_add_schema_style' );

?>