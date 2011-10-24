<?php
   
    class WP_SchemaFeed_Widget extends WP_Widget {
        
        function WP_SchemaFeed_Widget(){
            $widget_ops = array( 'description' => __( 'SchemaFeed', 'schemafeed' ) );
            $this->WP_Widget( 'schemafeed-widget', __( 'SchemaFeed', 'schemafeed' ), $widget_ops );
        }
        
        function widget($args,$instance){
            
            // Output content
            extract( $args );
            $this->display();
        }
        
        function display($domain){

            echo 'test';
        }

        function update($new_instance, $old_instance){
            
            // Process and save the widget options
            return $new_instance;
        }
        
        function form( $instance ) {
        
            // Output options for admin
        }
    }
    
    function wpsf_register_widget() {
        register_widget( 'WP_SchemaFeed_Widget' );
    }
    
    add_action( 'widgets_init', 'wpsf_register_widget' );
?>