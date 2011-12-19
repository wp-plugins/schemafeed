<?php

    function wpsf_loop_callback($buffer) {
    	
    	    	
    	return $buffer;
    }
    
    function wpsf_loop_buffer_start() {
    	ob_start("wpsf_loop_callback");
    }
    
    function wpsf_loop_buffer_end() {
    	ob_end_flush();
    }
    
    add_action( 'loop_start', 'wpsf_loop_buffer_start' );
    add_action( 'loop_end', 'wpsf_loop_buffer_end' );

?>