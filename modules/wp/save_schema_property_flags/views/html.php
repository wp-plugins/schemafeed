<?php

    // if errors, we redirect back to caller
    $errors = isset( $mod[ 'errors' ] ) ? $mod[ 'errors' ]:'';
    
    if ( $errors ) {
        wpsf_redirect_to_caller();
    }

    // now we can process if no errors
    $inputs = $mod[ 'inputs' ];
    
    // is html meta redirect here
    if ( isset( $inputs[ 'redirect_by_token' ] ) ) {
    
        $redirect_by_token = $inputs[ 'redirect_by_token' ];
    
        if ( $redirect_by_token == 'CALLER' ) {
        
            // redirect back to caller
            wpsf_redirect_to_caller();
        }
    
    }
    elseif ( isset( $inputs[ 'wpsf_redirect' ] ) ) {
    
        $redirect = $inputs[ 'wpsf_redirect' ];
        echo '<meta http-equiv="refresh" content="0;url='.$redirect.'">';        
    }
  
?>