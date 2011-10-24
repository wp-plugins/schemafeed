<?php

    // include helpers
    require_once( WP_PLUGIN_DIR . '/schemafeed/core/helpers.php' );
           
    // schemafeed constants
    define( "MODULE_EXECUTED", "2000##Module executed." );
    define( "MODULE_ERROR", "3000##Problem executing module." );
    define( "INPUT_ERROR", "3005##Problem with your input values." );
    define( "INPUT_PARAMETER_REQUIRED", "3010##Input Value Required." );
    define( "INPUT_PARAMETER_EMAIL", "3015##Email is not valid." );
    define( "INPUT_PARAMETER_NON_EMPTY", "3025##Please enter a value." );
    define( "INPUT_VALUE_NOT_VALID", "3030##Input parameter not valid." );
    define( "MIN_LENGTH_CHAR", "3035##The number of characters is too low." );
    define( "SYSTEM_ERROR", "3055##Sorry, there seems to be some system error." );
    // ## Note these need to be also in the function "input_module_errors"
    
    // wp related constants
    define( "WPPF", $wpdb->prefix );
    define( "WPSF_PATH", 'wp-content/plugins/schemafeed' );
    
    // global vars    
    $wpsf_db_version = "1.0";
    
    // Application servers
    require( 'current_servers.php' );
    
?>