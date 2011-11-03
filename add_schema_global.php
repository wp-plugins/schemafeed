<?php

    if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die( 'This page cannot be called directly.' ); }

    function wpsf_page_callback( $buffer ) {
    	
        global $post;    	
        
        include( WP_PLUGIN_DIR . '/schemafeed/core/simplehtmldom/simple_html_dom.php' );
        
        $html = wpsf_str_get_html( $buffer );
        
        // ## do h2 tags, only for loop pages
        if ( in_the_loop() ) {
                      
            $h2_tags = $html->find( 'h2' );
            
            for ($i = 0; $i<sizeof($h2_tags); $i++) {
                
                $h2_tag_parent = $h2_tags[$i]->parent();
                $h2_tag_parent_id = $h2_tag_parent->id;
                
                if ( $h2_tag_parent_id ) {
                    
                    $id_val_2 = wpsf_mb_replace( 'post-', '', $h2_tag_parent_id ); 
                    $schema_type = get_post_meta( $id_val_2, '_wpsf_schema_type' );
                    $h2_tag_parent->itemtype = "http://schema.org/".wpsf_vset( $schema_type[0] );
                }
            }
        }
                
        // ## do "audio" and "video" property
        $a_tags = $html->find( 'a' );
        
        for ($i = 0; $i<sizeof($a_tags); $i++) {
            
            $href = $a_tags[$i]->href;
            
            if ( strstr( $href, '.mp3' ) ) {
                $a_tags[$i]->itemprop = "audio";
            }
            elseif ( strstr( $href, '.wmv' ) ) {
                $a_tags[$i]->itemprop = "video";
            }
            elseif ( strstr( $href, '.avi' ) ) {
                $a_tags[$i]->itemprop = "video";
            }
            elseif ( strstr( $href, '.mov' ) ) {
                $a_tags[$i]->itemprop = "video";
            }
            elseif ( strstr( $href, '.mpg' ) ) {
                $a_tags[$i]->itemprop = "video";
            }
            elseif ( strstr( $href, '.mpeg' ) ) {
                $a_tags[$i]->itemprop = "video";
            }
        }
        
        // ## do h1, only for single pages
        if ( is_single( $post ) ) {
                    
            $h1_tags = $html->find( 'h1' );
            
            if ( isset( $h1_tags[0] ) ) {
                
                $h1_tag_parent = $h1_tags[0]->parent();
                $h1_tag_parent_id = $h1_tag_parent->id;
                
                if ( $h1_tag_parent_id ) {
                    
                    $id_val_2 = wpsf_mb_replace( 'post-', '', $h1_tag_parent_id ); 
                    $schema_type = get_post_meta( $id_val_2, '_wpsf_schema_type' );
                    $h1_tag_parent->itemtype = "http://schema.org/".wpsf_vset( $schema_type[0] );
                    $h1_tag_parent->itemscope = "";
                    
                    // this replaces the "the_title" filter method
                    $h1_tags[0]->innertext = '<span itemprop="name">'.$h1_tags[0]->innertext.'</span>'; 
                }
            }
        }
        
        // ## do body tag
        $body_tags = $html->find( 'body' );
        
        if ( isset( $body_tags[0] ) ) {
            
            $body_tags[0]->itemtype = "http://schema.org/WebPage";
            $body_tags[0]->itemscope = "";
        }
        
        // ## do global "comment" property, noddy solution, lets use dom later, see also add_schema_comment.php
        $html2 = wpsf_mb_replace( 'id="comments"', 'id="comments" itemprop="comment"', $html ); 

    	return $html2;
    }
    
    function wpsf_page_buffer_start() {
    	ob_start("wpsf_page_callback");
    }
    
    function wpsf_page_buffer_end() {
    	ob_end_flush();
    }
    
    add_action( 'wp_head', 'wpsf_page_buffer_start' );
    add_action( 'wp_footer', 'wpsf_page_buffer_end' );
    
?>