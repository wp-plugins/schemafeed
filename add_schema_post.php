<?php

    if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die( 'This page cannot be called directly.' ); }

    function wpsf_add_schema_fields( $content ) {
        
        global $post, $wpdb;
        
        // This is where we add the majority of the properties at the bottom of the post
        
        $schema_fields = '';
        
        // current post ID
        $post_id = $post->ID;
        
        // ## schemafeed settings
        $schemafeed_settings = wpsf_schemafeed_settings();
        
        // ## get schema type
        $schema_type = '';
        
        $schema_type_1 = wpsf_query( "  select * 
                                        from {$wpdb->postmeta}
                                        where
                                        post_id = '$post_id' and
                                        meta_key = '_wpsf_schema_type'
                                        " );
                                    
        if ( isset( $schema_type_1[0] ) ) {
            $schema_type = $schema_type_1[0][ $wpdb->postmeta.'.meta_value' ];
        }                                    
        
        // ## get meta fields
        $meta_keys = array();

        if ( !empty( $schema_type ) ) {
                    
            $records_1 = wpsf_query( "  select * 
                                        from {$wpdb->postmeta}
                                        left join ".WPPF."sf_schema_fields on CONCAT( '_wpsf_{$schema_type}_', ".WPPF."sf_schema_fields.field_name ) = {$wpdb->postmeta}.meta_key
                                        where
                                        post_id = '$post_id' and
                                        meta_key like '_wpsf_{$schema_type}_%'
                                        " );
                                 
            for( $idx=0; $idx<sizeof( $records_1 ); $idx++ ) {
                
                $schema_meta_key = $records_1[$idx][ $wpdb->postmeta.'.meta_key' ];
                $schema_meta_value = $records_1[$idx][ $wpdb->postmeta.'.meta_value' ];
                            
                $meta_keys[ $schema_meta_key ] = $records_1[$idx];
            }   
        }                                      
       
        // ## user properties at end of post
        $schema_property = '';
        $post_template = $schemafeed_settings[0][ 'settings.post_template' ];
            
        foreach( $meta_keys as $key => $values ) {

            $field_name = $values[ 'schema_fields.field_name' ];
            $field_name_display = $values[ 'schema_fields.field_name_display' ];
            $meta_value = $values[ $wpdb->postmeta.'.meta_value' ];
            $meta_field = $values[ 'schema_fields.meta_field' ];
            $sf_display_filter = $values[ 'schema_fields.sf_display_filter' ];
            $plain_display_field = $values[ 'schema_fields.plain_display_field' ];
            $content_attrib_fill = $values[ 'schema_fields.content_attrib_fill' ];
            
            // can field be displayed as is
            if ( $plain_display_field == 0 ) {
                continue;
            }
            
            if ( $meta_field == '1' ) {
                $schema_fields .= '<meta itemprop="'.$field_name.'" content="'.$meta_value.'">';  
            }
            else {
            
                $display_meta_value = $meta_value;
            
                if ( !empty( $sf_display_filter ) ) {
                    if ( function_exists( $sf_display_filter ) ) {
                        $display_meta_value = $sf_display_filter( $meta_value );
                    }                                           
                }
                            
                $sub_values = array();
                $sub_values[ 'property_display_name' ] = $field_name_display;
                $sub_values[ 'schema_property_name' ] = $field_name;
                
                $sub_values[ 'schema_property_value' ] = '';
                if ( $content_attrib_fill ) {
                    $sub_values[ 'schema_property_value' ] = htmlentities( $meta_value );
                }
                
                $sub_values[ 'schema_property_display_value' ] = htmlentities( $display_meta_value );
            
                $post_template_val = wpsf_sub_values( $post_template, $sub_values );
            
                $schema_property .= $post_template_val;  
            }
        }                 
        
        $schema_fields .= '<div class="schema_property_wrap">'.$schema_property.'</div>';                    
                                
        // ## "image" property
        // we consider the first one as the "image" of the schema.
        $content = mb_ereg_replace( '<img ', '<img itemprop="image" ', $content, 1 );
        
        // ## "description" property
        // This is added to the start of the post   
        $description_html = '';
        $description_prop = wpsf_vset( $meta_keys[ '_wpsf_'.$schema_type.'_description' ] );
        if ( !empty( $description_prop ) ) {
            $description_html = '<strong itemprop="description">'.htmlentities( $description_prop[ 'wp_postmeta.meta_value' ] ).'</strong><br />';
        }   
        
        // ## "alternativeHeadline" property
        // This is added to the start of the post, before the "description" property
        $alternativeHeadline_html = '';
        $alternativeHeadline_prop = wpsf_vset( $meta_keys[ '_wpsf_'.$schema_type.'_alternativeHeadline' ][ $wpdb->postmeta.'.meta_value' ] );
        if ( !empty( $alternativeHeadline_prop ) ) {
            if ( is_single( $post_id ) ) {
                $alternativeHeadline_html = '<h2 itemprop="alternativeHeadline" class="schema_alternativeHeadline">'.htmlentities( $alternativeHeadline_prop ).'</h2>';
            }
        }      
        
        // ## "url" property
        // This is the permalink                            
        $permalink = get_permalink( $post_id );
        $schema_fields .= '<meta itemprop="url" content="'.$permalink.'">';  
        $schema_fields .= '<meta itemprop="discussionUrl" content="'.$permalink.'">';         
        
        // ## "datePublished" property
        $first_post_date = get_the_date( 'c' );
		$schema_fields .= '<meta itemprop="datePublished" content="'.$first_post_date.'">'; 
        
        // ## "dateModified" property
        $mod_post_date = get_the_modified_time( 'c' );
		$schema_fields .= '<meta itemprop="dateModified" content="'.$mod_post_date.'">'; 
        
        // ## "dateCreated" property, this is approx
        $created_post_date = wpsf_get_created_date( $post_id );
		$schema_fields .= '<meta itemprop="dateCreated" content="'.$created_post_date.'">'; 
        
        // ## "keywords" property
        $tags = wp_get_post_tags( $post_id );
        $keywords = array();
        
        if ( !empty( $tags ) ) {
        
            for( $idx=0; $idx<sizeof( $tags ); $idx++ ) {
                $keywords[] = htmlentities( $tags[$idx]->name );          
            }
        
            $schema_fields .= '<meta itemprop="keywords" content="'.implode( ',', $keywords ).'">';
        }
        
        // ## "thumbnailUrl" property
        $thumbnailUrl = '';
        $thumbnailUrl_prop = wpsf_vset( $meta_keys[ '_wpsf_'.$schema_type.'_thumbnailUrl' ][ $wpdb->postmeta.'.meta_value' ] );
        if ( !empty( $thumbnailUrl_prop ) ) {
            if ( !is_single( $post_id ) ) {
                $thumbnailUrl = '<img align="right" src="'.$thumbnailUrl_prop.'">';
                
                // place it within the content to the right 
                // $content = wpsf_mb_replace( '<p>', '<p>'.$thumbnailUrl, $content );     
            }
        }  
        
        // ## "articleBody" property
        $wrapper_1_start = '<span itemprop="articleBody">';
        $wrapper_1_end = '</span>';
        
        // ## "wordCount" property
        $schema_fields .= '<meta itemprop="wordCount" content="'.wpsf_word_count( $content ).'">';
        
        // ## "blogPosts" property
        $url = home_url();
        $schema_fields .= '<meta itemprop="blogPosts" content="'.$url.'">';
        
        // ## Media properties:
        // ## "Bitrate" property
        // ## "contentSize" property
        // ## "contentURL" property
        // ## "duration" property
        // ## "encodingFormat" property
        // ## "height" property
        // ## "width" property
        // ## "uploadDate" property
        // ## "exifData" property
        // ## "videoQuality" property
        // ## "videoFrameSize" property
        
        // ## "recipeInstructions" property
        if ( $schema_type == 'Recipe' ) {
            $wrapper_1_start = '<span itemprop="recipeInstructions">';
            $wrapper_1_end = '</span>';            
        }
        
        // ## "reviewBody" property
        if ( $schema_type == 'Review' ) {
            $wrapper_1_start = '<span itemprop="reviewBody">';
            $wrapper_1_end = '</span>';            
        }
        
        // ## "breadcrumb" property
        
        // ## "mainContentOfPage" property
        $wrapper_2_start = '<span itemprop="mainContentOfPage">';
        $wrapper_2_end = '</span>';   
               
        // ## collect property strings together 
        $content = $wrapper_1_start.$content.$wrapper_1_end;
        $content = $wrapper_2_start.$content.$wrapper_2_end;
        
        return $alternativeHeadline_html.$description_html.$content.$schema_fields;
    }
    
    // 1000 priority so it runs hopefully last.
    add_filter( 'the_content', 'wpsf_add_schema_fields', 1000 );
    
    // ## the excerpt filter
    function wpsf_add_schema_the_excerpt( $the_excerpt ) {
        
        // excerpt should be a summary rather than the description, to do later.
        // return wpsf_mb_replace( '<p>', '<p itemprop="description">', $the_excerpt );
        return $the_excerpt;
    }
    
    add_filter( 'the_excerpt', 'wpsf_add_schema_the_excerpt' );


?>