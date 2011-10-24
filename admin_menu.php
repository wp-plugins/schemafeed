<?php

    if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die( 'This page cannot be called directly.' ); }

    add_action('admin_menu', 'wpsf_admin_menu');
        
    function wpsf_admin_menu() {

        add_menu_page( __('SchemaFeed', 'wpsf'), __('SchemaFeed', 'wpsf'), 'administrator', 'wp__schema_settings', 'wpsf_show_page', plugins_url('schemafeed/img/sf_icon.png'));                
    
        add_submenu_page('wp__schema_settings', __('Schema Settings', 'wpsf'), __('Schema Settings', 'wpsf'), 'administrator', 'wp__schema_settings', 'wpsf_show_page');           
        // add_submenu_page('wp__schema_settings', __('Display Flags', 'wpsf'), __('Display Flags', 'wpsf'), 'administrator', 'wp__display_flags', 'wpsf_show_page'); 
        add_submenu_page('wp__schema_settings', __('Help', 'wpsf'), __('Help', 'wpsf'), 'administrator', 'wp__help', 'wpsf_show_page');
    
        add_submenu_page('dummy', __('Test', 'wpsf'), __('Test', 'wpsf'), 'administrator', 'test__wptest', 'wpsf_show_page');
        
        add_submenu_page('dummy', '', '', 'administrator', 'schema__get_schema_fields', 'wpsf_show_page');
        add_submenu_page('dummy', '', '', 'administrator', 'wp__save_schema_settings', 'wpsf_show_page');
        add_submenu_page('dummy', '', '', 'administrator', 'wp__save_schema_property_flags', 'wpsf_show_page');
    }

?>