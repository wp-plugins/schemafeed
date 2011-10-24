<?php

    // For local db use
    $WPSFSERVERS = array();
                            
    $WPSFSERVERS[] = array( 'NAME' => 'wpsfdb',
                            'DBHOST' => DB_HOST,
                            'DB_NAME' => DB_NAME,
                            'DB_USER' => DB_USER,
                            'DB_PASSWORD' => DB_PASSWORD
                            );

    // Mainly for development
    define( "APP_LIVE", "1" );
    define( "DEBUG", "0" );
                            
?>