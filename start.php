<?php

    require_once( WP_PLUGIN_DIR . '/schemafeed/core/all.php' );

    // ## we always start root server  
    wpsf_connect_root_db();

    // ## Styles
    include( WP_PLUGIN_DIR . '/schemafeed/styles.php' );

    if ( is_admin() ) {
        
        // ## Add the admin menu
        include( WP_PLUGIN_DIR . '/schemafeed/admin_menu.php' );
        
        // ## plugin activation message
        include( WP_PLUGIN_DIR . '/schemafeed/install/install.php' );
        
        // ## Add shortcode interface
        // include( WP_PLUGIN_DIR . '/schemafeed/shortcode_buttons.php' );
        
        // ## Add flash session stuff
        include( WP_PLUGIN_DIR . '/schemafeed/flash_session.php' );
        
        // ## Shortcode, may come in handy 
        // include( WP_PLUGIN_DIR . '/schemafeed/widgets.php' );
                
        // ## pages
        // include( WP_PLUGIN_DIR . '/schemafeed/wp_pages.php' );
        
        // ## admin init
        include( WP_PLUGIN_DIR . '/schemafeed/meta_box.php' );
        
        // ## post types
        // include( WP_PLUGIN_DIR . '/schemafeed/post_types.php' );
        
        // ## taxonomy, may come in handy
        // include( WP_PLUGIN_DIR . '/schemafeed/taxonomy.php' );
        
        // ## category, for next version
        // include( WP_PLUGIN_DIR . '/schemafeed/category.php' );
        
        // ## save schema meta data
        include( WP_PLUGIN_DIR . '/schemafeed/save_meta.php' );
        
        // ## plugin activation message
        include( WP_PLUGIN_DIR . '/schemafeed/activation_message.php' );
    }
    else {
        
        // public code
        
        $schemafeed_settings = wpsf_schemafeed_settings();
        $all_schemas_off = 0;
        
        if ( isset( $schemafeed_settings[0] ) ) {
            $all_schemas_off = $schemafeed_settings[0][ 'settings.all_schemas_off' ];
        }
            
        if ( !$all_schemas_off ) {
                                
            // ## add schema during loop
            include( WP_PLUGIN_DIR . '/schemafeed/loop_replace.php' );
            
            // ## add schema field via global page content
            include( WP_PLUGIN_DIR . '/schemafeed/add_schema_global.php' );
                
            // ## add schema field to the end of the post
            include( WP_PLUGIN_DIR . '/schemafeed/add_schema_post.php' );
            
            // ## add schema property "name" to the title of the post
            // seems to cause strange problems with plugin "only-tweet-like-share-and-google-1"
            // include( WP_PLUGIN_DIR . '/schemafeed/add_schema_post_title.php' );
            
            // ## add schema property "comment" to comment posts
            include( WP_PLUGIN_DIR . '/schemafeed/add_schema_comment.php' );
            
            // ## add schema property "comment" to comment posts
            include( WP_PLUGIN_DIR . '/schemafeed/add_blogroll.php' );
        }
    }

?>