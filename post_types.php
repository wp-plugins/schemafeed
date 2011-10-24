<?php

    add_action( 'init', 'wpsf_create_post_type' );
    
    function wpsf_create_post_type() {
    
        register_post_type( 'schema-org',  
            array(  
                'labels' => array(  
                    'name' => __( 'Schemas' ),  
                    'singular_name' => __( 'Schema' )  
                ),  
            'public' => true,  
            'menu_position' => 5,  
            'rewrite' => array('slug' => 'schema-org')  
            )  
        );  
    }

?>