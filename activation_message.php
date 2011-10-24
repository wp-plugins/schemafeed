<?php
    
    if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die( 'This page cannot be called directly.' ); }
    
    function wpsf_my_admin_notice(){
    
        echo '  <div class="updated">
                    <p>Hi there! Thanks for installing SchemaFeed. To see how it works, just head over to the <a href="admin.php?page=wp__help">help page</a>.</p>
                </div>';
    }
    
    $wpsf_run_once = get_option( 'wpsf_activation_message_run_once' );
    
    if ( $wpsf_run_once <= 1 ) {
        
        add_action( 'admin_notices', 'wpsf_my_admin_notice' );

        update_option( 'wpsf_activation_message_run_once', $wpsf_run_once+1 );
    }
    
?>