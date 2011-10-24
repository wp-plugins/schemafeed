<?php

class root {

    var $inputs = array();
    
    // result_value = used for calling programs to decide what to do depending on return result
    // this can be overriden at any stage
    
    var $result = array();

    // the final "view" is either "allowed_outputs" or "redirect_view".
   
    // The allowed outputs, this is only accessabile if "_auth" is true.
    var $allowed_outputs = array();  
    
    // Default template, this is only accessabile if "_auth" is true.
    var $module_output = '';  
    
    var $redirect_view = ''; 
    
    // the rendered page content
    var $contents = '';
                            
    function __construct() {

        global $wpdb , $wp_roles, $wp_version;
        
        $this->wpdb =& $wpdb;
        $this->wp_roles =& $wp_roles;
        $this->wp_version =& $wp_version;
    }
    
    function _module_outputs() {
    }
    
    function _init() {
    }
    
    function _post_render() {
    }
                            
    function set_view( $view_template, $target_view ) {
        
        $this->view_template[] = $view_template;
        $this->target_view[] = $target_view;
    }                            
    
    function set_wpsf_redirect( $redirect = '' ) {
        
        $this->redirect_view = $redirect;
    }  
    
    function set_result( $error_code ) {
    
        $this->result[ 'result_value' ] = wpsf_result_value( constant( $error_code ) );
        $this->result[ 'result_message' ] = wpsf_result_message( constant( $error_code ) );
        $this->result[ 'result_token' ] = $error_code;
    }
    
    // in "post+get then redirect", we lose the post values in the redirect stage
    // this basically gives the redirect destination the same post+get values again as in the first call to that page
    function merge_last_request_values() {
    
        $last_requests = wpsf_get_last_requests();
        $this->inputs = array_merge( $last_requests, $this->inputs );
    } 
}

?>