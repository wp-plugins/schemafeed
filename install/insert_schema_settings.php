<?php

    $wpsf_schema_setting_count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->prefix}sf_settings" ) );
    
    if ( $wpsf_schema_setting_count == 0 ) {
    
        $post_template = '
<span class="schema_property">
    <span class="schema_property_name"><b>[[property_display_name]]:</b> </span>
    <span class="schema_property_value" itemprop="[[schema_property_name]]" content="[[schema_property_value]]">[[schema_property_display_value]]</span>
</span>&nbsp;&bull;&nbsp;
';
        
        $wpsf_current_entry_2 = array(  'post_template' => $post_template,
                                        'all_schemas_off' => '0' );
        
        $wpdb->insert( $table_sf_settings, $wpsf_current_entry_2 );
    }    

?>