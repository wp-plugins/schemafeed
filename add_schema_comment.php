<?php

    if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die( 'This page cannot be called directly.' ); }

    function wpsf_add_schema_comment_text( $the_comment ) {
        
        $comment = get_comment( comment_ID(), ARRAY_A ); 
        
        // ## "commentTime" property
        $commentTime = '<meta itemprop="commentTime" content="'.date( 'c', strtotime( $comment[ 'comment_date' ] ) ).'" />';  
        
        // ## "commentText" property
        $the_comment = '<span itemprop="commentText">'.$the_comment.'</span>'.$commentTime;                                          
                    
        return $the_comment;
    }
    
    add_filter( 'comment_text', 'wpsf_add_schema_comment_text' );

?>