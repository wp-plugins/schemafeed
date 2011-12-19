<?php

    // for dev testing
    add_action( 'init', 'wpsf_install' );

    function wpsf_install() {

        global $wpdb, $wpsf_db_version;

        $installed_ver = get_option( "wpsf_db_version" );

        if ( $installed_ver == $wpsf_db_version ) {
            // same version, no need to install/upgrade tables
            // return;
        }

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

        // ## create table
        $table_sf_schemas = $wpdb->prefix."sf_schemas";
        
        $sql = "    CREATE TABLE ".$table_sf_schemas." (
                        schema_id varchar(50) NOT NULL,
                        schema_name varchar(255) NOT NULL,
                        schema_name_display varchar(255) NOT NULL,
                        depth int(11) NOT NULL,
                        schema_parent varchar(255) NOT NULL,
                        description text NOT NULL,
                        schema_order int(11) NOT NULL,
                        PRIMARY KEY (schema_id),
                        KEY schema_name (schema_name),
                        KEY schema_parent (schema_parent)
                    );
                    ";
        
        dbDelta( $sql );
        
        // add data
        include( WP_PLUGIN_DIR . '/schemafeed/install/insert_schema_data.php' );
        
        // ## create table
        $table_sf_schema_fields = $wpdb->prefix."sf_schema_fields";
        
        $sql = "    CREATE TABLE ".$table_sf_schema_fields." (
                        schema_field_id varchar(50) NOT NULL,
                        field_name varchar(255) NOT NULL,
                        field_name_display varchar(255) NOT NULL,
                        data_type varchar(255) NOT NULL,
                        sf_data_type varchar(255) NOT NULL,
                        data_type_full varchar(255) NOT NULL,
                        display_type varchar(255) NOT NULL,
                        description text NOT NULL,
                        description2 text NOT NULL,
                        type_from varchar(255) NOT NULL,
                        type_name varchar(255) NOT NULL,
                        schema_id varchar(50) NOT NULL,
                        field_order int(11) NOT NULL,
                        plain_display_field tinyint(4) NOT NULL,
                        admin_display_field tinyint(4) NOT NULL,
                        fields_ass_tmp int(11) NOT NULL,
                        auto_fill tinyint(4) NOT NULL,
                        meta_field tinyint(4) NOT NULL,
                        content_attrib_fill tinyint(4) NOT NULL,
                        sf_display_filter varchar(255) NOT NULL,
                        not_display tinyint(4) NOT NULL,
                        PRIMARY KEY (schema_field_id),
                        KEY field_name (field_name),
                        KEY type_name (type_name),
                        KEY schema_id (schema_id)
                    );
                    ";
        
        dbDelta( $sql );
        
        // add data
        include( WP_PLUGIN_DIR . '/schemafeed/install/insert_schema_field_data.php' );
        
        // ## create table
        $table_sf_schema_flags = $wpdb->prefix."sf_schema_flags";
        
        $sql = "    CREATE TABLE ".$table_sf_schema_flags." (
                        wp_sf_schema_flag_id int(11) NOT NULL AUTO_INCREMENT,
                        type_name varchar(255) NOT NULL,
                        field_name varchar(255) NOT NULL,
                        state tinyint(4) NOT NULL,
                        PRIMARY KEY (wp_sf_schema_flag_id),
                        KEY type_name (type_name),
                        KEY field_name (field_name)
                    );
                    ";
        
        dbDelta( $sql );
        
        // ## create table
        $table_sf_settings = $wpdb->prefix."sf_settings";
        
        $sql = "    CREATE TABLE ".$table_sf_settings." (
                        wp_sf_setting_id int(11) NOT NULL AUTO_INCREMENT,
                        post_template text NOT NULL,
                        all_schemas_off tinyint(4) NOT NULL,
                        PRIMARY KEY (wp_sf_setting_id)
                    );
                    ";
        
        dbDelta( $sql );
        
        // add data
        include( WP_PLUGIN_DIR . '/schemafeed/install/insert_schema_settings.php' );

        // db version tracking 
        add_option( "wpsf_db_version", $wpsf_db_version );
    }
    
    // for new installs
    register_activation_hook( __FILE__, 'wpsf_install' );
    
    // for updates
    function wpsf_plugin_update_db_check() {
    
        global $wpsf_db_version;
    
        if ( get_site_option( 'wpsf_db_version' ) != $wpsf_db_version ) {
            wpsf_install();
        }
    }
    
    add_action( 'plugins_loaded', 'wpsf_plugin_update_db_check' );

?>