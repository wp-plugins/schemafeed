<?php

    if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die( 'This page cannot be called directly.' ); }

    add_action( 'admin_init', 'wpsf_admin_init' );
    
    function wpsf_admin_init() {
    
        add_meta_box( 'wpsf_meta_box', "Add Schema.org Fields", 'wpsf_meta_box', 'post', 'normal' );
    }
    
    function wpsf_meta_box() {
    
        global $post;    
    
        // ## load existing schemas
        $schema_fields = '<img style="margin: 8px;" src="'.plugins_url( 'img/ajax-loader.gif', __FILE__ ).'">';
        $schema_fields2 = '';
        $schema_name = '';
    
        if ( !empty( $post->ID ) ) {
        
            $post_id = $post->ID;
            
            $schema_type = get_post_meta( $post_id, '_wpsf_schema_type' );
            
            if ( !empty( $schema_type ) ) {
            
                $schema_name = $schema_type[0]; 
                
                if ( !empty( $schema_name ) ) {
                    
                    $schema_fields2 = ' <script type="text/javascript">
                                            $(document).ready(function(){	
                                                wpsf_add_fields( \''.$schema_name.'\', \''.$post_id.'\' );
                                            });
                                        </script>
                                        ';  
                }
            }
        }
    
        // ## schema tree
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

        // jquery
        echo '  <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.6.0/jquery.min.js"></script>
                <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.min.js"></script>
                <link rel="stylesheet" type="text/css" media="screen" href="http://hotlink.jquery.com/jqueryui/themes/base/jquery.ui.all.css" />
                <link rel="stylesheet" type="text/css" media="screen" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.8/themes/flick/jquery-ui.css" />
                ';            
    
        $url = plugins_url( 'schemafeed/jquery/datetime/jquery.ui.datetime.min.js' );
    	wpsf_nl( '<script type="text/javascript" src="'.$url.'"></script>' );   
    	
    	$url = plugins_url( 'schemafeed/jquery/datetime/jquery.ui.datetime.css' );
    	wpsf_nl( '<link rel="stylesheet" type="text/css" media="screen" href="'.$url.'" />' );  
    
        $url = plugins_url( 'schemafeed/jquery/easytooltip/js/easyTooltip.js' );
    	wpsf_nl( '<script type="text/javascript" src="'.$url.'"></script>' );   
        
        echo '  <style>
                    #easyTooltip{
                    	padding: 5px 10px;
                    	border:1px solid #aaa;
                    	background-color: #444;
                    	color: #fff;
                    	width: 400px;
                    }
                </style>
                ';         
    
        $url = plugins_url( 'schemafeed/app_js/app_general2.js' );
    	wpsf_nl( '<script type="text/javascript" src="'.$url.'"></script>' );
    
        echo '  <div id="dialog_div"></div>
                       
                <p>                            
                    Pick the closest schema that matches your article. Pick a schema higher up if you can\'t find a specific schema.
                    Each post can have a different Schema. Within each property, you can seperate different values you enter with a comma.
                </p>                    
                
                <br /><br />
                            
                Pick Schema:&nbsp;<select name="_wpsf_schema_type" onChange="wpsf_add_fields( this.options[this.selectedIndex].value, $(\'#post_ID\').val() )">
                    '.$schema_options.'
                </select>
                
                <br />

                <div id="schema_fields" style="visibility: hidden;">
                    '.$schema_fields.'                                           
                </div>
                ';
                
        echo $schema_fields2;                
    }

?>