<?php

    if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die( 'This page cannot be called directly.' ); }

    function wpsf_page_callback( $buffer ) {
    	
        global $post;    	
        
        $dom = new DOMDocument;
        $dom->loadHTML( $buffer );
        $xpath = new DOMXPath($dom);
        
        // ## do h2 tags, only for loop pages
        if ( in_the_loop() ) {
                        
            $h2_tag = $dom->getElementsByTagName( 'h2' );
            
            for ($i = 0; $i < $h2_tag->length; $i++) {
                
                $id_val_1 = $h2_tag->item($i)->parentNode->getAttribute( "id" );
                $id_val_2 = str_replace( 'post-', '', $id_val_1 ); 
                
                $schema_type = get_post_meta( $id_val_2, '_wpsf_schema_type' );
                
                $h2_tag->item($i)->parentNode->setAttribute( "itemtype", "http://schema.org/".wpsf_vset( $schema_type[0] ) );
            }
        }
        
        // ## do "audio" and "video" property
        $a_tags = $dom->getElementsByTagName('a');
        
        for ($i = 0; $i < $a_tags->length; $i++) {
            
            $href = $a_tags->item($i)->getAttribute( "href" );
            
            if ( strstr( $href, '.mp3' ) ) {
                $a_tags->item($i)->setAttribute( "itemprop", "audio" );
            }
            elseif ( strstr( $href, '.wmv' ) ) {
                $a_tags->item($i)->setAttribute( "itemprop", "video" );
            }
            elseif ( strstr( $href, '.avi' ) ) {
                $a_tags->item($i)->setAttribute( "itemprop", "video" );
            }
            elseif ( strstr( $href, '.mov' ) ) {
                $a_tags->item($i)->setAttribute( "itemprop", "video" );
            }
            elseif ( strstr( $href, '.mpg' ) ) {
                $a_tags->item($i)->setAttribute( "itemprop", "video" );
            }
            elseif ( strstr( $href, '.mpeg' ) ) {
                $a_tags->item($i)->setAttribute( "itemprop", "video" );
            }
        }
        
        // ## do h1, only for single pages
        if ( is_single( $post ) ) {
        
            $h1_tag = $dom->getElementsByTagName('h1');
            
            $id_val_1 = $h1_tag->item(0)->parentNode->getAttribute( "id" );
            $id_val_2 = str_replace( 'post-', '', $id_val_1 ); 
            
            $schema_type = get_post_meta( $id_val_2, '_wpsf_schema_type' );
            
            if ( $h1_tag->length == 1 ) {
                $h1_tag->item(0)->parentNode->setAttribute( "itemtype", "http://schema.org/".wpsf_vset( $schema_type[0] ) );
                $h1_tag->item(0)->parentNode->setAttribute( "itemscope", "" );
            }
        }
        
        $buffer2 = $dom->saveHTML();
        
        // ## the following is done after saveHTML
        
        // ## do body tag, noddy solution, lets use dom later
        $buffer2 = str_replace( '<body ', '<body itemscope itemtype="http://schema.org/WebPage" ', $buffer2 );    
        
        // ## do global "comment" property, noddy solution, lets use dom later, see also add_schema_comment.php
        $buffer2 = str_replace( 'id="comments"', 'id="comments" itemprop="comment"', $buffer2 ); 

        // bit noddy, but find proper solution later.
        $buffer2 = str_replace( 'itemscope=""', 'itemscope', $buffer2 );
        
    	return $buffer2;
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