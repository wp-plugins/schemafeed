<?php

    if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die( 'This page cannot be called directly.' ); }

    function wpsf_backward_stripos($haystack, $needle, $offset = 0){
    
        $length = mb_strlen($haystack);
        $offset = ($offset > 0)?($length - $offset):abs($offset);
        $pos = mb_stripos(strrev($haystack), strrev($needle), $offset);
        file_put_contents( "c:\\Temp\\aaa2.txt", $offset );
        return ($pos === false)?false:( $length - $pos - mb_strlen($needle) );
    }
    
    function wpsf_mb_substr_replace($output, $replace, $posOpen, $posClose) {
        return mb_substr($output, 0, $posOpen).$replace.mb_substr($output, $posClose+1);
    } 
    
    function wpsf_page_callback( $buffer ) {
    	
        global $post;    	
        
        // simple way of adding schema.org values without using complex dom parser, more reliable?
                    
        // ## h1 "itemprop" name, we assume there can only be one h1 for seo reasons, but cater for many just in case
        $stop_infin = 0;   
        
        while ( $h1_start = stripos( $buffer, '<h1', $h1_start ) ) {
        
            // we only allow 100 posts per page
            if ( $stop_infin++ > 100 ) {
                break;
            }
        
            $h1_start2 = mb_stripos( $buffer, '>', $h1_start );
            $h1_end = mb_stripos( $buffer, '</h1>', $h1_start2 );
            $h1_tag = mb_substr( $buffer, $h1_start2 + 1, $h1_end - $h1_start2 - 1 );
            // $buffer = htmlentities( $h1_tag ).'=='.$buffer;
            $buffer = wpsf_mb_substr_replace( $buffer, '<span itemprop="name">'.$h1_tag.'</span>', $h1_start2+1, $h1_start2+mb_strlen( $h1_tag ) );
            
            // this is needed so we don't find the same h1 again as a result of adding more characters in parent tag
            $h1_start = stripos( $buffer, '<h1', $h1_start )+1;
        }
        
        // ## h1 parent "itemtype"
        $h1_start = 0; 
        $stop_infin = 0;   
        
        while ( $h1_start = stripos( $buffer, '<h1', $h1_start ) ) {
            
            // we only allow 100 posts per page
            if ( $stop_infin++ > 100 ) {
                break;
            }
            
            // find parent id
            $length = mb_strlen( $buffer );
            $parent_id_start = mb_strrpos( $buffer, ' id="post-', -( $length - $h1_start ) );
            
            if ( $parent_id_start ) {
                
                $parent_id_end = mb_stripos( $buffer, '" ', $parent_id_start );
                
                if ( $parent_id_end ) {
                    
                    $parent_id_1 = mb_substr( $buffer, $parent_id_start, $parent_id_end - $parent_id_start + 1 );
        
                    $id_val_2 = wpsf_mb_replace( 'id="post-', '', $parent_id_1 ); 
                    $id_val_2 = wpsf_mb_replace( '"', '', $id_val_2 ); 
                    $id_val_2 = trim( $id_val_2 );
                    $tmp .= '='.$h1_start.'=';
                    if ( is_numeric( $id_val_2 ) ) {
                          
                        // find schema type and item type
                        $schema_type = get_post_meta( $id_val_2, '_wpsf_schema_type' );
                        
                        if ( sizeof( $schema_type ) > 0 ) {
                            
                            $item_type = "http://schema.org/".wpsf_vset( $schema_type[0] );
                
                            // add parent itemtype and itemscope                
                            $buffer = wpsf_mb_substr_replace( $buffer, ' itemscope="" itemtype="'.$item_type.'" ', $parent_id_start, $parent_id_start );
                        }
                    }                        
                }
            }
            
            // this is needed so we don't find the same h1 again as a result of adding more characters in parent tag
            $h1_start = stripos( $buffer, '<h1', $h1_start )+1;
        }
        
        // ## h2 "itemprop" name, we assume there can only be one h2 for seo reasons, but cater for many just in case
        // same/similiar to h1, but less complex
        $stop_infin = 0;   
        
        while ( $h2_start = stripos( $buffer, '<h2', $h2_start ) ) {
        
            // we only allow 100 posts per page
            if ( $stop_infin++ > 100 ) {
                break;
            }
        
            $h2_start2 = mb_stripos( $buffer, '>', $h2_start );
            $h2_end = mb_stripos( $buffer, '</h2>', $h2_start2 );
            $h2_tag = mb_substr( $buffer, $h2_start2 + 1, $h2_end - $h2_start2 - 1 );
            // $buffer = htmlentities( $h2_tag ).'=='.$buffer;
            $buffer = wpsf_mb_substr_replace( $buffer, '<span itemprop="name">'.$h2_tag.'</span>', $h2_start2+1, $h2_start2+mb_strlen( $h2_tag ) );
            
            // this is needed so we don't find the same h2 again as a result of adding more characters in parent tag
            $h2_start = stripos( $buffer, '<h2', $h2_start )+1;
        }
        
        // ## h2 parent "itemtype"
        $h2_start = 0; 
        $stop_infin = 0;   
        
        while ( $h2_start = stripos( $buffer, '<h2', $h2_start ) ) {
            
            // we only allow 100 posts per page
            if ( $stop_infin++ > 100 ) {
                break;
            }
            
            // find parent id
            $length = mb_strlen( $buffer );
            $parent_id_start = mb_strrpos( $buffer, ' id="post-', -( $length - $h2_start ) );
            
            if ( $parent_id_start ) {
                
                $parent_id_end = mb_stripos( $buffer, '" ', $parent_id_start );
                
                if ( $parent_id_end ) {
                    
                    $parent_id_1 = mb_substr( $buffer, $parent_id_start, $parent_id_end - $parent_id_start + 1 );
        
                    $id_val_2 = wpsf_mb_replace( 'id="post-', '', $parent_id_1 ); 
                    $id_val_2 = wpsf_mb_replace( '"', '', $id_val_2 ); 
                    $id_val_2 = trim( $id_val_2 );
                    $tmp .= '='.$h2_start.'=';
                    if ( is_numeric( $id_val_2 ) ) {
                          
                        // find schema type and item type
                        $schema_type = get_post_meta( $id_val_2, '_wpsf_schema_type' );
                        
                        if ( sizeof( $schema_type ) > 0 ) {
                            
                            $item_type = "http://schema.org/".wpsf_vset( $schema_type[0] );
                
                            // add parent itemtype and itemscope                
                            $buffer = wpsf_mb_substr_replace( $buffer, ' itemscope="" itemtype="'.$item_type.'" ', $parent_id_start, $parent_id_start );
                        }
                    }                        
                }
            }
            
            // this is needed so we don't find the same h2 again as a result of adding more characters in parent tag
            $h2_start = stripos( $buffer, '<h2', $h2_start )+1;
        }
        
        // ## do body tag
        $buffer = wpsf_mb_replace( '<body ', '<body itemtype="http://schema.org/WebPage" itemscope="" ', $buffer ); 
        
        // ## do global "comment" property, see also add_schema_comment.php
        $buffer = wpsf_mb_replace( 'id="comments"', 'id="comments" itemprop="comment"', $buffer ); 
        
        /*
        // to add again in next version
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
        */

    	return $buffer;
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