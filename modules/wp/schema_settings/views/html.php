<div class="wrap">

    <?php screen_icon( 'schemafeed' ); ?>
    
    <h2><?php echo esc_html( __( 'SchemaFeed: Settings', 'wpsf' ) ); ?></h2>

    <p>
        At the bottom of each article posting, the plugin will display the Schema properties. Not all properties are displayed as some
        are for the benefit of search engines. The Post Template css classes can be changed in the plugin edit screen within the file
        "schemafeed/css/style.css". 
    </p>
    
    <?php
        if ( wpsf_input_module_errors( 'wp__save_schema_settings' ) ) {
            echo '<div class="updated" style="color: #a00;"><p><strong>There seems to be something wrong, please correct and try again.</strong></p></div>';
        }
        else {
            $flash_var = wpsf_get_flash_vars( 'wp__save_schema_settings' );
            if ( !empty( $flash_var ) ) {
                echo '<div class="updated"><p><strong>Settings saved.</strong></p></div>';
            }
        }
    ?>

    <form action="admin.php?page=wp__save_schema_settings&noheader=1" method="post">
               
        <input type="hidden" name="redirect_by_token" value="CALLER">
        <input type="hidden" name="v" value="html">
        
        <p>
            <strong>Post Template:</strong><br />
            
            <span style="font-size: 12px;">The schema values will fill the variables in the square brackets.</span><br />
            
            <?php
                if ( $post_template = wpsf_input_error( 'wp__save_schema_settings', 'post_template' ) ) {
                    echo '<div style="color: #a00;"><strong>'.$post_template[ "error_message" ].'</strong></div>';
                }
                
                // property display template
                $prop_template = '
<span class="schema_property">
    <span class="schema_property_name"><b>[[property_display_name]]:</b> </span>
    <span class="schema_property_value" itemprop="[[schema_property_name]]" content="[[schema_property_value]]">[[schema_property_display_value]]</span>
</span>&nbsp;&bull;&nbsp;
';
                                    
                $db_post_template = htmlspecialchars( wpsf_vset( $mod[ 'records' ][0][ 'settings.post_template' ] ) );
                
                if ( !empty( $db_post_template ) ) {
                    $prop_template = $db_post_template;
                }                                    
                
            ?>
            
            <textarea name="post_template" style="width: 1000px; height: 150px;"><?php echo $prop_template; ?></textarea>
        </p>
               
        <p>
            <strong>Turn off all Schema Displays:</strong>&nbsp;
            
            <?php
                if ( $all_schemas_off = wpsf_input_error( 'wp__save_schema_settings', 'all_schemas_off' ) ) {
                    echo '<div style="color: #a00;"><strong>'.$all_schemas_off[ "error_message" ].'</strong></div>';
                }
            ?>
            
            <input type="hidden" name="all_schemas_off" value="0">
            
            <?php
                if ( wpsf_vset( $mod[ 'records' ][0][ 'settings.all_schemas_off' ] ) == 1 ) {
                    echo '<input type="checkbox" name="all_schemas_off" value="1" checked="">';
                }
                else {
                    echo '<input type="checkbox" name="all_schemas_off" value="1">';
                }
            ?>
        </p>
        
        <p class="submit">
            <input type="submit" value="Update Settings" name="submit" class="button-primary action">
        </p>
    </form>
   
</div>

