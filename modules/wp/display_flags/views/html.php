<div class="wrap">

    <?php screen_icon( 'schemafeed' ); ?>
    
    <h2><?php echo esc_html( __( 'SchemaFeed: Schema Property Display Flags', 'wpsf' ) ); ?></h2>

    <p>
        On this page, you can control which Schema properties are shown in your website. By default, if a property value is not empty, then it is 
        displayed unless it is turned off explicitly.
    </p>
    
    <?php
        if ( wpsf_input_module_errors( 'wp__save_schema_property_flags' ) ) {
            echo '<div class="updated" style="color: #a00;"><p><strong>There seems to be something wrong, please correct and try again.</strong></p></div>';
        }
        else {
            $flash_var = wpsf_get_flash_vars( 'wp__save_schema_property_flags' );
            if ( !empty( $flash_var ) ) {
                echo '<div class="updated"><p><strong>Settings saved.</strong></p></div>';
            }
        }
    ?>

    <?php
    
        echo '  <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.6.0/jquery.min.js"></script>
                <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.min.js"></script>
                ';   
                
        $url = plugins_url( 'schemafeed/app_js/app_general2.js' );
    	wpsf_nl( '<script type="text/javascript" src="'.$url.'"></script>' );  
        
        $url = plugins_url( 'schemafeed/jquery/easytooltip/js/easyTooltip.js' );
    	wpsf_nl( '<script type="text/javascript" src="'.$url.'"></script>' );   
        
        echo '  <style>
                    #easyTooltip{
                    	padding: 5px 10px;
                    	border:1px solid #aaa;
                    	background-color: #444;
                    	color: #fff;
                    	width: 200px;
                    }
                </style>
                ';               
    
        $url = plugins_url( 'schemafeed/img/ajax-loader.gif' );
        $schema_fields = '<img style="margin: 8px;" src="'.$url.'">';
            
        $schema_tree = wpsf_get_mod_direct( array(  'mn' => 'schema__get_schema_tree',
                                                    'v' => 'internal' ) );
                                                
        $schema_options = '<option value="">';
        
        $schema = $schema_tree->result[ 'schema' ];
        
        for( $idx=0; $idx<sizeof( $schema ); $idx++ ) {
        
            $schema_name1 = trim( $schema[$idx][ 'schemas.schema_name' ] );
            $schema_name2 = wpsf_mb_replace( '~', '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', $schema[$idx][ 'schemas.schema_name_display' ] );
            
            $selected = '';
            if ( $schema_name == $schema_name1 ) { $selected = ' selected="" '; }
            
            $schema_options .= wpsf_nl2( '<option value="'.$schema_name1.'" '.$selected.'>'.$schema_name2.'&nbsp;&nbsp;&nbsp;' );
        }      

        echo '  Select Schema:&nbsp;
                <select name="_wpsf_schema_type" onChange="wpsf_show_fields( this.options[this.selectedIndex].value )">
                    '.$schema_options.'
                </select>

                <div id="schema_fields" style="visibility: hidden;">
                    '.$schema_fields.'                                           
                </div>
                ';
    ?>                
   
</div>