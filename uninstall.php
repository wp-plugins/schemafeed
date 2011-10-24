<?php
    
    if( !defined( 'ABSPATH') && !defined('WP_UNINSTALL_PLUGIN') )
    exit();
     
    function wpsf_uninstall_plugin() {
    
        global $wpdb;
    
        // ## remove tables
        $wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}sf_schemas" );
        $wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}sf_schema_fields" );
        $wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}sf_schema_flags" );
        $wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}sf_settings" );
    
        // ## remove options
        delete_option( "wpsf_db_version" );
        delete_option( "wpsf_activation_message_run_once" );  
        
        // ## remove user data from "wp_postmeta"  
        // have to find the best way to do this
    }     
     
    wpsf_uninstall_plugin();
    
?>