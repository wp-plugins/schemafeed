<?php
    
    // ## internal flash messages
    if ( isset( $_SESSION[ "wpsf" ][ "flash" ] ) ) {
     
        $flash_messages = $_SESSION[ "wpsf" ][ "flash" ];
        
        foreach( $flash_messages as $key => $value ) {
            
            if ( $value[1] == '0' ) {
                $flash_count = 1;
                $_SESSION[ "wpsf" ][ "flash" ][ $key ] = array( $value[0], $flash_count );
            }
            else {
                unset( $_SESSION[ "wpsf" ][ "flash" ][ $key ] );
            }
        }
    }


?>