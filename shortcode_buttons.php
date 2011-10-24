<?php

    // add_action( 'init', 'wpsf_tinymce_addbuttons' );
    
    /*
    function wpsf_tinymce_addbuttons() {
    
        if (    !current_user_can( 'edit_posts' ) &&
                !current_user_can( 'edit_pages' ) ) {
            return;
        }
        
        if ( get_user_option( 'rich_editing' ) == 'true' ) {
            add_filter( "mce_external_plugins", "wpsf_tinymce_addplugin");
            add_filter( "mce_buttons", "wpsf_tinymce_registerbutton" );
        }
    }
    
    function wpsf_tinymce_registerbutton($buttons) {
    
        array_push( $buttons, 'separator', 'schemafeed' );
    
        return $buttons;
    }
    
    function wpsf_tinymce_addplugin($plugin_array) {
    
        $plugin_array[ 'schemafeed' ] = plugins_url( 'schemafeed/tinymce/plugins/schemafeed/editor_plugin.js' );
    
        return $plugin_array;
    }
    
    // add related js popup box
    add_action( 'admin_footer-post-new.php', 'wpsf_mce_popup' );
    add_action( 'admin_footer-post.php', 'wpsf_mce_popup' );
    add_action( 'admin_footer-page-new.php', 'wpsf_mce_popup' );
    add_action( 'admin_footer-page.php', 'wpsf_mce_popup' );
    
    function wpsf_mce_popup() {
        $url = plugins_url( 'schemafeed/app_js/wpsf_mcepop.js' );
    	wpsf_nl( '<script type="text/javascript" src="'.$url.'"></script>' );
    }
    */

?>