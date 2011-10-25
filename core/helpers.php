<?php

    function wpsf_show_page() {
    
        if( isset( $_GET[ 'page' ] ) ) {

            echo wpsf_get_mod_http( $_GET[ 'page' ] );
        }
    }

    function wpsf_module_exist( $module_name = '' ) {
    
        $module_names = array();
    
        $module_name_1 = explode( '__', $module_name );
    
        if ( file_exists( WP_PLUGIN_DIR . '/schemafeed/modules/'.$module_name_1[0] ) ) {
            if ( file_exists( WP_PLUGIN_DIR . '/schemafeed/modules/'.$module_name_1[0].'/'.$module_name_1[1] ) ) {
                if ( file_exists( WP_PLUGIN_DIR . '/schemafeed/modules/'.$module_name_1[0].'/'.$module_name_1[1].'/'.$module_name_1[1].'.php' ) ) {
                    $module_names = array( $module_name_1[0], $module_name_1[1] );
                }
            }
        }
        
        return $module_names;
    }

    function wpsf_go_mod( $inputs = array(), $call_method = '' ) {
    
        $module = '';
    	$contents = '';
        	
    	$module_result = array();
    	$go_mod = 1;
    	
    	// for backward compatibility
    	if ( isset( $inputs[ 'mn' ] ) ) {
    	   $inputs[ 'page' ] = $inputs[ 'mn' ]; 
    	}
   
        // ## find module to go with the page
    	if ( $inputs[ 'page' ] ) {
    
            $module_name = wpsf_sql_clean( $inputs[ 'page' ] );
    
            $module_exist = wpsf_module_exist( $module_name );
            
    		if ( $module_exist ) {
    
    			$package = $module_exist[0];
    			$module_token = $module_exist[1];
    			
    			// include root class
    			include_once( WP_PLUGIN_DIR . '/schemafeed/modules/root.php' );
    
				include_once( WP_PLUGIN_DIR . '/schemafeed/modules/'.$package.'/'.$module_token.'/'.$module_token.'.php' );

				$real_class_name = $package.'__'.$module_token;

				if ( class_exists( $real_class_name ) ) {

					$module = new $real_class_name();
    
					// set inputs
					$module->inputs = $inputs;

					// default outputs which all modules must have
					$module->allowed_outputs[ 'blank' ] = array( 'path' => '/views/blank.php', 'template' => 'blank' );
					$module->allowed_outputs[ 'no_auth' ] = array( 'path' => '/views/no_auth.php', 'template' => 'blank' );
					$module->allowed_outputs[ 'errors' ] = array( 'path' => '/views/errors.php', 'template' => 'blank' );
					$module->allowed_outputs[ 'json' ] = array( 'path' => '/views/json.php', 'template' => 'blank' );
					    
					// check authentication
					if ( $module->_auth() ) {

						// module init
						$module->_init();

						// module specific output views
						$module->_module_outputs();

						// now check error
						if ( !$module->_error() ) {

							// by default, the action is processed
							$module->result[ 'result_value' ] = wpsf_result_value( MODULE_EXECUTED );
							$module->result[ 'result_message' ] = wpsf_result_message( MODULE_EXECUTED );

                            $module->_bus_logic();
                        }
						
						// we also attach the inputs to the results
						// and give it back to the caller
						$module->result[ 'inputs' ] = $module->inputs;

						// we store the result in the session, for error feedbacks
						wpsf_set_flash_vars( $real_class_name, $module->result );
                    }
					else {

						// by default, the action is processed
						$module->result[ 'result_value' ] = wpsf_result_value( NOT_AUTH );
						$module->result[ 'result_message' ] = wpsf_result_message( NOT_AUTH );
					}

					// now decide the "view"
					if ( !empty( $module->redirect_view ) ) {
						if ( !wpsf_vset( $inputs[ 'v' ] ) ) {

							header( $module->redirect_view );
							die;
						}
					}
					
					$choosen_output = array( 'path' => '', 'template' => '' );

					// json is assumed if there is no default output selected
					$module_output = ( !empty( $module->module_output ) ) ? $module->module_output : 'json';

					if ( !empty( $module_output ) ) {
						$use_output = wpsf_use_output( $module_output, $module->allowed_outputs );
						if ( $use_output ) {
							$choosen_output = $use_output;
						}
					}
 
					// caller can override default view
					if ( isset( $inputs[ 'v' ] ) ) {

						$user_view = $inputs[ 'v' ];

                        if ( $user_view == 'internal' ) {
                            // internal is a special case
                            $choosen_output = array( 'path' => '', 'template' => '' );
                        }
                        else {
                            $use_output = wpsf_use_output( $user_view, $module->allowed_outputs );
                            if ( $use_output ) {
                                $choosen_output = $use_output;
                            }
						}
                    }

					// If caller need to use a different template for a particular view
					// Then that view needs to be manually coded into the module class
					// User cannot specify it.
					// This is just a particular school of thought. It partly doesn't seem
					// to make sense ( v=xml, template=print_template ?? ). And also
					// from an access point of view, its difficult at the moment.
					// We need to rethink this at a later stage.

					// now assign view and template
					if ( !empty( $choosen_output[ 'path' ] ) ) {
						
						$target_view_path = WP_PLUGIN_DIR . '/schemafeed' . $choosen_output[ 'path' ];
						$view_template = $choosen_output[ 'template' ];
    
						if ( file_exists( $target_view_path ) ) {
						
                            $module->contents = wpsf_do_view( $module->result, $target_view_path, $view_template );

                            // after render
                            $module->_post_render();
                            
                            $contents = $module->contents; 
						}
						else {
							// No module found.
                        }
					}
                }
				else {
					// No module found.
                }
            }
    		else {
    			// No module found.
            }
    	}
        
        // ## internal view is a special case
        if ( isset( $inputs[ 'v' ] ) ) {
            $user_view = $inputs[ 'v' ];
            if ( $user_view == 'internal' ) {
                return $module;
            }
        }
    
    	return $contents;
    }
    
    // for "post+get then redirect" situations so redirect page has same post+get values
    function wpsf_set_post_requests() {
    
        if ( $_SERVER[ 'REQUEST_METHOD' ] == 'POST' ) {
            $result = array_merge( $_POST, $_GET, $_FILES );
            wpsf_set_flash_vars( "input_requests", $result );
        }
    }
    
    // for "post+get then redirect" situations so redirect page has same post+get values
    function wpsf_get_last_requests() {
        
        return (array) wpsf_get_flash_vars( "input_requests" );
    }
    
    // is output allowed
    function wpsf_use_output( $output_name = '', $output_list = array() ) {
    
    	$result = false;
    
    	if ( !empty( $output_name ) ) {
    
    		foreach( $output_list as $output_name_key => $current_view ) {
    
    			if ( $output_name_key == $output_name ) {
    				// found one
    				$result = $current_view;
    				break;
    			}
    		}
    	}
    
    	return $result;
    }
    
    // get module result via http
    function wpsf_get_mod_http() {
    
    	$result = '';
    
        // for "post+get then redirect" situations so redirect page has same post+get values
        wpsf_set_post_requests();
    
    	$inputs = wpsf_request_clean();
    	    	
    	$result = wpsf_go_mod( $inputs, 'http' );
    
    	return $result;
    }
    
    // get module result internally
    function wpsf_get_mod_direct( $inputs = array() ) {
    
    	$result = '';
    
    	if ( !empty( $inputs ) ) {
    
    		$result = wpsf_go_mod( $inputs, '' );
    	}
    
    	return $result;
    }
    
    function wpsf_domain_name() {
    
        $result = '';
    
        $result = $_SERVER[ 'SERVER_NAME' ];
        
        if (    ( $_SERVER[ 'SERVER_PORT' ] != '80' ) &&
                ( $_SERVER[ 'SERVER_PORT' ] != '443' ) )
        {
            $result .= ':'.$_SERVER[ 'SERVER_PORT' ];
        }
        
        return $result;
    }
    
    function wpsf_domain_local_path() {
    
        $result = '';
    
        $script_name = $_SERVER[ 'SCRIPT_NAME' ];
        $script_name2 = explode( '/', $script_name );
        
        if ( isset( $script_name2[1] ) ) {
            $result = wpsf_chttp().'://'.wpsf_domain_name().'/'.$script_name2[1].'/'.WPSF_PATH;            
        }
        
        return $result;
    }
    
    function wpsf_do_view( $mod_result = array(), $view_path = '', $view_template ) {
    
    	$result = '';
    	$content = '';
    	$template = '';
    
    	// this is for the view to use
    	$mod = $mod_result;
    	 
    	// 1. get content
    	ob_start();
    	include( $view_path );
    	$content = ob_get_contents();
    	ob_end_clean();
    
    	// 2. get template
    	if ( file_exists( WP_PLUGIN_DIR . '/schemafeed/templates/'.$view_template.'.php' ) ) {
    		ob_start();
    		include( WP_PLUGIN_DIR . '/schemafeed/templates/'.$view_template.'.php' );
    		$template = ob_get_contents();
    		ob_end_clean();
    
    		$result = wpsf_mb_replace( '{{module}}', $content, $template );
    	}
    	else {
    		$result = $content;
    	}
    
    	return $result;
    }
        
    function wpsf_connect_root_db() {
    
    	$result = false;
    	$db_name = wpsf_db_name( 'wpsfdb' );
    	
    	// prevent plugin headers sent due to multiple calls during install.
    	if ( $db_name ) {
        	
        	global $$db_name;
    
        	$$db_name = mysql_pconnect( wpsf_db_host( 'wpsfdb' ), wpsf_db_user( 'wpsfdb' ), wpsf_db_password( 'wpsfdb' ) );
        	
            if ( $db_name && $$db_name ) {
        
            	$select_result = mysql_select_db( $db_name, $$db_name );
            
            	if ( !$select_result ) {
                    // echo mysql_error($$db_name);
                    // die;
            	}
            
            	if (!$$db_name) {
                    // echo mysql_error($$db_name);
            		// die;
            	}
            }
        }
    
    	return $result;
    }
    
    // short cut way of calling root
    function wpsf_query_root( $query ) {
    
        $result = wpsf_query_master( "root", wpsf_db_name( 'wpsfdb' ), "", $query );
        
        return $result;
    }
    
    // standard sql
    function wpsf_query( $query ) {
    
        $server_name = 'wpsfdb';
    
        $db_name = wpsf_db_name( $server_name );
    
        $result = wpsf_query_master( $server_name, $db_name, "", $query );
        
        return $result;
    }
    
    // for data sql
    function wpsf_query2( $server_name, $table_field, $query ) {
    
        $db_name = wpsf_db_name( $server_name );
    
        $result = wpsf_query_master( $server_name, $db_name, $table_field, $query );
        
        return $result;
    }
    
    // "$server_name" can be app1, app2 ...etc.
    function wpsf_connect_db( $server_name ) {
    
    	$result = false;
    
    	if ( !empty( $server_name ) ) {
    
    		$result = mysql_pconnect( wpsf_db_host( $server_name ), wpsf_db_user( $server_name ), wpsf_db_password( $server_name ) );
    
    		if (!$result) {
    			echo mysql_error( $result );
    			$result = false;
    		}
    	}
    
    	return $result;
    }
    
    // Used for app tables and data tables
    function wpsf_query_master( $server_name, $db_name, $table_field, $query ) {
   
    	// resource name is just the server name combined with db_name e.g. projectx_root + projectx_123
    	$resource_name = $server_name.$db_name;
    
    	global $$resource_name;
    
    	$result = array();
    
        $host = wpsf_db_host( $server_name );        
   
    	$$resource_name = wpsf_connect_db( $server_name );
    
    	if ( !empty( $db_name ) ) {
    		mysql_select_db( $db_name, $$resource_name );
    	}
    	
    	$q_result = mysql_query( $query, $$resource_name );
    	
    	if ( $q_result !== false ) {
    
    		if ( preg_match( "/^select /i", trim( $query ) ) ) {
                if ( $table_field == '0' ) {  
                    $result = wpsf__resultSet2( $q_result );
                }
                else {
                    $result = wpsf__resultSet( $q_result );
                }
    		}
    		elseif ( preg_match( "/^insert /i", trim( $query ) ) ) {
    			
    			// if insert supplies its own id, then this will be zero.
                $last_id = mysql_insert_id();
    			
    			if ( $last_id > 0 ) {
    				$result = $last_id;
    			}
    			else {
                    $result = true;
    			}
    		}
    		else {
                if ( $table_field == '0' ) {    		  
                    $result = wpsf__resultSet2( $q_result );
                }
                else {
                    $result = wpsf__resultSet( $q_result );
                }
    		}
    	}
    	else {
       		
    		echo mysql_error();
    		
    		$result = false;
    	}
    
    	return $result;
    }
    
    // automatically returns it as "table_name.field_name"
    function wpsf__resultSet( $q_result ) {
    
    	$result = array();                            
        $map = array();
        global $wpdb;
          
        if ( !is_bool( $q_result ) ) {   
                            
            $index = 0;
            while ( $column = mysql_fetch_field( $q_result ) ) {
                $map[ $index++ ] = array( $column->table, $column->name );
            }
                   
            while ( $row = mysql_fetch_row( $q_result ) ) {
                    
                $current_row = array();                        
                        
                foreach ( $row as $index => $field ) {
                
                    list( $table, $column ) = $map[ $index ];
                    
                    if ( $table ) {
                        $table2 = wpsf_mb_replace( $wpdb->prefix.'sf_', '', $table );
                        $current_row[ $table2.'.'.$column ] = $row[ $index ];
                    }
                    else {
                        $current_row[ $column ] = $row[ $index ];
                    }
                }
                
                $result[] = $current_row;
            }
        }
        
        return $result;
    }
    
    // returns whole result as is, no table_name.field_name replacement
    function wpsf__resultSet2( $q_result ) {
    
    	$result = array();                            
                  
        if ( !is_bool( $q_result ) ) {   
                            
            while ( $row = mysql_fetch_array( $q_result, MYSQL_ASSOC ) ) {
                $result[] = $row;
            }
        }
        
        return $result;
    }
     
    if ( DEBUG ) {             
        function x( $var ) {
        	echo '<pre>';
        	print_r( $var );
        	echo '</pre>';
        }
    }
    
    // all input must be clean
    // we don't sql clean it because not all parameters will be used in sql
    // we also support xxx[] but not xxx[][]
    function wpsf_request_clean() {
    
    	$request = array_merge( $_REQUEST, $_FILES );
    	
    	/*
    	Only used in some environments
        foreach( $request as $key => $value ) {
        
            $purifier = new HTMLPurifier();
            
            if ( is_array( $value ) ) {
                foreach( $value as $key2 => $value2 ) {
                    if ( !is_array( $value2 ) ) {        
                        $request[ $key ][ $key2 ] = $purifier->purify( $value2 );
                    }
                }
            }
            else {
                $request[ $key ] = $purifier->purify( $value );
            }
        }
        */
    	
    	return $request;
    }
    
    function wpsf_clean_text1( $clean_text = '' ) {
    
        $result = $clean_text;        
    
        global $purifier; 
        
        // $config = HTMLPurifier_Config::createDefault();
        // $config->set( 'HTML', 'AllowedElements', 'b,br,p,i,pre' );
        
        if ( empty( $purifier ) ) {
            $purifier = new HTMLPurifier();
        }

        $purifier = $purifier->purify( $clean_text );

        return $result;
    }
    
    function wpsf_sql_clean( $string = '' ) {
    
    	$result = mysql_real_escape_string( $string );
    	
    	return $result;
    }
    
    function wpsf_single_quote_clean( $string = '' ) {
    
    	$result = wpsf_mb_replace( "'", "\'", $string );
    	
    	return $result;
    }
       
    function wpsf_uniq_id(){
    	 
    	srand((double)microtime()*1000000);
    	$unique_id = md5( uniqid( rand(), true) );
    
    	return wpsf_mb_replace( ".", "", $unique_id );
    }
    
    function wpsf_nl( $string = '' ) {
    	echo $string.chr(13).chr(10);
    }
    
    function wpsf_nl2( $string = '' ) {
    	return $string.chr(13).chr(10);
    }
    
    function wpsf_nl2array( $string = '' ) {
    
        $result = array();
    
        $result_1 = preg_replace( '/[\n|\r]/', '¬' , $string );
        
        $result_2 = explode( '¬', $result_1 );
        
        if ( is_array( $result_2 ) ) {
            for( $idx=0; $idx<sizeof( $result_2 ); $idx++ ) {
                if ( !empty( $result_2[$idx] ) ) {
                    $result[] = $result_2[$idx]; 
                }
            }
        }
        
        return $result;
    }
    
    function wpsf_array_check_2( $arr, $string ) {
    	 
    	preg_match_all( '/\[([^\]]*)\]/', $string, $arr_matches, PREG_PATTERN_ORDER );
    
    	$return = $arr;
    
    	foreach( $arr_matches[1] as $dimension ) {
    
    		if ( !isset( $return[ $dimension ] ) ) {
    			// no need to continue
    			$return = false;
    			break;
    		}
    		else {
    			$return = $return[ $dimension ];
    		}
    	}
    	 
    	return $return;
    }
    
    function wpsf_array_check_1( $inputs, $string_array = '' ) {
    
    	$result = true;
    
    	if ( strstr( $string_array, '[' ) ) {
    
    		$string_array = wpsf_mb_replace( ']', '', $string_array );
    		$string_array = explode( '[', $string_array );
    
    		$array_name_1 = '';
    
    		foreach( $string_array as $keys ) {
    			$array_name_1 .= "[".$keys."]";
    		}
    
    		$result = wpsf_array_check_2( $inputs, $array_name_1 );
    	}
    	else {
    		if ( isset( $inputs[ $string_array ] ) ) {
    			$result = $inputs[ $string_array ];
    		}
    		else {
    			$result = false;
    		}
    	}
    
    	return $result;
    
    }
    
    function wpsf_error_check( $inputs = array(), $check_input = array(), $module_obj = null ) {
    
    	$result = array();
    
    	// we use the fields we have specified to check
    	// not the inputs
    	foreach( $check_input as  $check_types ) {
    
    		$field_name = $check_types[ 'field_name' ];
    		$field_exist_values = wpsf_array_check_1( $inputs, $field_name );
    
    		// check against
    		foreach( $check_types as $check_type => $check_vals ) {
    
    			switch ( $check_type ) {
    
    				case 'min_char':
    
    					if ( $field_exist_values !== false ) {
    
    						$value_against = $check_vals;
    
    						if ( $error_message = wpsf_error_check_min_char( $field_exist_values, $value_against ) ) {
    							// there is an error
                                $result[ $field_name ] = array( 'error_message' => $error_message,
                                                                'field_value' => $field_exist_values
    							);
    						}
    					}
    
    					break;
    
    				case 'required':
    
    					$value_against = $check_vals;
    
    					if ( $value_against == 1 ) {
    
    						// the parameters is required
    						if ( !$field_exist_values ) {
    							// there is an error
    							$result[ $field_name ] = array( 'error_message' => wpsf_result_message( INPUT_PARAMETER_REQUIRED ),
                                                                'field_value' => $field_exist_values
    							);
    						}
    					}
    
    					break;
    					
                    case 'nonempty':
    
    					$value_against = $check_vals;
    
    					if ( $value_against == 1 ) {
    
    						// the parameters is required
    						if ( $field_exist_values !== false ) {
                                
                                $field_exist_values = trim( $field_exist_values );
                                    						  
                                if ( $field_exist_values == '' ) {    						  
        							// there is an error
        							$result[ $field_name ] = array( 'error_message' => wpsf_result_message( INPUT_PARAMETER_NON_EMPTY ),
                                                                    'field_value' => $field_exist_values
        							);
        						}
    						}
    						else {
                                // it must also exist
    							$result[ $field_name ] = array( 'error_message' => wpsf_result_message( INPUT_PARAMETER_REQUIRED ),
                                                                'field_value' => $field_exist_values
    							);    						  
    						}
    					}
    
    					break;    					
    
    				case 'integer':
    
    					$value_against = $check_vals;
    
    					if ( $value_against == 1 ) {
    						// the parameters is required
    						if ( $field_exist_values !== false ) {
    							if ( $error_message = wpsf_error_check_numeric( $field_exist_values ) ) {
    								// there is an error
    								$result[ $field_name ] = array( 'error_message' => $error_message,
                                                                        'field_value' => $field_exist_values
    								);
    							}
    						}
    					}
    
    					break;
    					
                    case 'email':
    
    					$value_against = $check_vals;
    
    					if ( $value_against == 1 ) {
    						// the parameters is required
    						if ( $field_exist_values !== false ) {
                                if ( !preg_match( "/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $field_exist_values ) ) {
                                    $result[ $field_name ] = array( 'error_message' => result_message( INPUT_PARAMETER_EMAIL ),
                                                                    'field_value' => $field_exist_values );
                                }
                            }
    					}
    
    					break;    					
    
    				case 'callbacks':
    
    					$check_methods = $check_vals;
    
    					foreach( $check_methods as $method_call ) {
    
                            if ( method_exists( $module_obj, $method_call ) ) {
                            
                                $callback_result = $module_obj->$method_call();
        
                                if ( !empty( $callback_result ) ) {
        						
                                    $result[ $field_name ] = array(    'error_message' => $callback_result,
                                                                        'field_value' => $field_exist_values
        							);
        						}
        					}
                        }
    
    					break;
    					
                    case 'maxlength':
    
    					if ( $field_exist_values !== false ) {
    
    						$value_against = $check_vals;
    
    						if ( $error_message = wpsf_error_check_max_char( $field_exist_values, $value_against ) ) {
    							// there is an error
                                $result[ $field_name ] = array( 'error_message' => $error_message,
                                                                'field_value' => $field_exist_values
    							);
    						}
    					}
    
    					break;  					
    			}
    		}
    	}
    
    	return $result;
    }
    
    // error check
    // minimum length char check
    function wpsf_error_check_min_char( $value_given = array(), $value_against = '' ) {
    
    	$error_message = '';
    
    	foreach( (array) $value_given as $current_value ) {
    		if ( strlen( $current_value ) < (int) $value_against ) {
    			$error_message = result_message( MIN_LENGTH_CHAR );
    			break;
    		}
    	}
    
    	return $error_message;
    
    }
    
    // error check
    // maximum length char check
    function wpsf_error_check_max_char( $value_given = array(), $value_against = '' ) {
    
    	$error_message = '';
    
    	foreach( (array) $value_given as $current_value ) {
    		if ( strlen( $current_value ) > (int) $value_against ) {
    			$error_message = MAX_LENGTH_CHAR;
    			break;
    		}
    	}
    
    	return $error_message;
    
    }
    
    // error check
    // numeric value
    function wpsf_error_check_numeric( $value_given = array() ) {
    
    	$error_message = '';
    
    	foreach( (array) $value_given as $current_value ) {
    		if ( !is_numeric( $current_value ) ) {
    			$error_message = INPUT_PARAMETER_INTEGER;
    			break;
    		}
    	}
    
    	return $error_message;
    
    }
    
    function wpsf_set_flash_vars( $key, $value ) {
    
    	if ( isset( $_SESSION[ 'wpsf' ][ "flash" ] ) ) {
    
    		if ( $key != '' ) {
    			$_SESSION[ 'wpsf' ][ "flash" ][ $key ] = array( $value, 0 );
    		}
    	}
    	else {
    
    		$_SESSION[ 'wpsf' ][ "flash" ] = array();
    		$_SESSION[ 'wpsf' ][ "flash" ][ $key ] = array( $value, 0 );
    	}
    }
    
    function wpsf_get_flash_vars( $key ) {
    
    	$result = '';
    
    	if ( isset( $_SESSION[ 'wpsf' ][ "flash" ][ $key ][0] ) ) {
    
    		$result = $_SESSION[ 'wpsf' ][ "flash" ][ $key ][0];
    
    		// set as read
    		$_SESSION[ 'wpsf' ][ "flash" ][ $key ][1] = 1;
    	}
    
    	return $result;
    }
    
    function wpsf_input_error( $module = '', $input_name = '' ) {
    
    	$result = '';
    
    	$flash_var = wpsf_get_flash_vars( $module );
    
    	if ( isset( $flash_var[ 'errors' ] ) ) {
    
    		if ( isset( $flash_var[ 'errors' ][ $input_name ] ) ) {
    			$result = $flash_var[ 'errors' ][ $input_name ];
    		}
    	}
    
    	return $result;
    }
    
    // Are there any errors relating to the module
    function wpsf_input_module_errors( $module = '' ) {
    
    	$result = '';
    
    	$flash_var = wpsf_get_flash_vars( $module );

        if ( isset( $flash_var[ 'result_value' ] ) ) {
        
            $capture_errors = array(    wpsf_result_value( MODULE_ERROR ),
                                        wpsf_result_value( INPUT_ERROR ),
                                        wpsf_result_value( INPUT_PARAMETER_REQUIRED ),
                                        wpsf_result_value( INPUT_PARAMETER_EMAIL ),
                                        wpsf_result_value( INPUT_PARAMETER_NON_EMPTY ),
                                        wpsf_result_value( INPUT_VALUE_NOT_VALID ),
                                        wpsf_result_value( MIN_LENGTH_CHAR ),
                                        wpsf_result_value( SYSTEM_ERROR )
                                        );
    
        	if ( in_array( $flash_var[ 'result_value' ], $capture_errors ) ) {
                $result = $flash_var[ 'result_message' ];
        	}
        }
    
    	return $result;
    }
    
    // just a simple message for next page during html call
    function wpsf_set_page_flash( $message = '' ) {
    
    	wpsf_set_flash_vars( 'page_flash', $message );
    
    	return $message;
    }
    
    // just a simple message for next page during html call
    function wpsf_get_page_flash() {
    
    	return wpsf_get_flash_vars( 'page_flash' );
    }
        
    // uuid
    function wpsf_uuid()
    {
    	srand((double)microtime()*1000000);
    	$unique_id = md5( uniqid( rand(), true) );
    
        // because db tables must start with a letter, and uuid is used so much, we artificially append and "a"
    
    	return wpsf_mb_replace( ".", "", 'a'.$unique_id );
    }
    
    // Note, this actually creates the variable globally if not set
    // If this feature is not wanted, then use something like:
    // isset( $test[ 'qqq' ] ) ? $test[ 'qqq' ]:'';
    function wpsf_vset( &$value, $default = '' ) {
    
        if ( empty( $value ) ) {
    	   if ( !empty( $default ) ) {
    	       $value = $default;
            }               
    	}
            	    
    	return $value;
    }
    
    // get error number in format "NUM##Message"
    function wpsf_result_value( $error_string ) {
    
    	$result = '';
    
    	$error_1 = explode( '##', $error_string );
    
    	if ( isset( $error_1[0] ) ) {
    		$result = $error_1[0];
    	}
    
    	return $result;
    }
    
    // get error message in format "NUM# Message"
    function wpsf_result_message( $error_string ) {
    
    	$result = '';
    
    	$error_1 = explode( '##', $error_string );
    
    	if ( isset( $error_1[1] ) ) {
    		$result = $error_1[1];
    	}
    
    	return $result;
    }
    
    // return back to the page that contains the link
    function wpsf_redirect_to_caller() {	
    
        $caller_browser_url = wpsf_vset( $_SERVER[ "HTTP_REFERER" ] );
                   
        if ( $caller_browser_url ) {
        
            header( "Location: ".$caller_browser_url );
                    
            die;
        }
    }   
    
    function wpsf_redirect_to_source() {	
    
        $current_browser_url = DOMAIN_URL."?".$_SERVER[ "QUERY_STRING" ];

        header( "Location: ".$current_browser_url );
        
        die;
    }  
    
    function wpsf_redirect( $redirect = '', $perm = 0 ) {	
    
        if ( empty( $redirect ) ) {
            return;
        }
        
        if ( $perm ) {
            header( "HTTP/1.1 301 Moved Permanently" ); 
        }
    
        header( "Location: ".$redirect );
                
        die;
    }  
    
    function wpsf_current_url() {	
    
        $current_browser_url = 'http://'.wpsf_domain_name()."/?".$_SERVER[ "QUERY_STRING" ];

        return $current_browser_url;
    }   
    
    function wpsf_relative_domain() {	
    
        $relative_domain = $_SERVER[ "SCRIPT_NAME" ];

        return $relative_domain;
    }  
    
    
    function wpsf_get_group_db_server() {	
    
        $group_server = $_SESSION[ 'login__login_process' ][ 'user_groups.assigned_server' ];

        return $group_server;
    } 
            
    // global var
    function wpsf_gv( $var = '' ) {
    
        global $$var;
        
        return $$var;
    }
    
    function wpsf_get_rnd_iv($iv_len) {
    
        $iv = '';
        while ($iv_len-- > 0) {
            $iv .= chr(mt_rand() & 0xff);
        }
        return $iv;
    }
    
    function wpsf_md5_encrypt($plain_text, $password, $iv_len = 16) {
    
        $plain_text .= "\x13";
        $n = strlen($plain_text);
        if ($n % 16) $plain_text .= str_repeat("\0", 16 - ($n % 16));
        $i = 0;
        $enc_text = wpsf_get_rnd_iv($iv_len);
        $iv = substr($password ^ $enc_text, 0, 512);
        while ($i < $n) {
            $block = substr($plain_text, $i, 16) ^ pack('H*', md5($iv));
            $enc_text .= $block;
            $iv = substr($block . $iv, 0, 512) ^ $password;
            $i += 16;
        }
        return strtr( base64_encode($enc_text), '+/=', '-_,' );
    }
    
    function wpsf_md5_decrypt($enc_text, $password, $iv_len = 16){
    
        $enc_text = strtr( $enc_text, '-_,', '+/=' );
        $enc_text = base64_decode($enc_text);
        $n = strlen($enc_text);
        $i = $iv_len;
        $plain_text = '';
        $iv = substr($password ^ substr($enc_text, 0, $iv_len), 0, 512);
        while ($i < $n) {
            $block = substr($enc_text, $i, 16);
            $plain_text .= $block ^ pack('H*', md5($iv));
            $iv = substr($block . $iv, 0, 512) ^ $password;
            $i += 16;
        }
        return preg_replace('/\\x13\\x00*$/', '', $plain_text);
    }

    function wpsf_db_host( $name ) {
    
        $result = '';
    
        global $WPSFSERVERS;
        
        for( $idx=0; $idx<sizeof( $WPSFSERVERS ); $idx++ ) {
            if ( $WPSFSERVERS[$idx][ 'NAME' ] == $name ) {
                $result = $WPSFSERVERS[$idx][ 'DBHOST' ];
            }
        }
        
        return $result;
    }     
    
    function wpsf_db_name( $name ) {
    
        $result = '';
    
        global $WPSFSERVERS;
        
        for( $idx=0; $idx<sizeof( $WPSFSERVERS ); $idx++ ) {
            if ( $WPSFSERVERS[$idx][ 'NAME' ] == $name ) {
                $result = $WPSFSERVERS[$idx][ 'DB_NAME' ];
            }
        }
        
        return $result;
    }     
    
    function wpsf_db_user( $name ) {
    
        $result = '';
    
        global $WPSFSERVERS;
        
        for( $idx=0; $idx<sizeof( $WPSFSERVERS ); $idx++ ) {
            if ( $WPSFSERVERS[$idx][ 'NAME' ] == $name ) {
                $result = $WPSFSERVERS[$idx][ 'DB_USER' ];
            }
        }
        
        return $result;
    }     
    
    function wpsf_db_password( $name ) {
    
        $result = '';
    
        global $WPSFSERVERS;
        
        for( $idx=0; $idx<sizeof( $WPSFSERVERS ); $idx++ ) {
            if ( $WPSFSERVERS[$idx][ 'NAME' ] == $name ) {
                $result = $WPSFSERVERS[$idx][ 'DB_PASSWORD' ];
            }
        }
        
        return $result;
    }    
    
    function wpsf_web_server( $name ) {
    
        $result = '';
    
        global $WPSFSERVERS;
        
        for( $idx=0; $idx<sizeof( $WPSFSERVERS ); $idx++ ) {
            if ( $WPSFSERVERS[$idx][ 'NAME' ] == $name ) {
                // we always pick the first one
                $web_servers = (array) $WPSFSERVERS[$idx][ 'WEB' ];
                $result = $web_servers[0];
            }
        }
        
        return $result;
    }     
    
    function wpsf_cr_split( $string = '' ) {
    
        $result = array();
    
        $string_split = preg_split( "[\n|\r]", $string );
        
        foreach( $string_split as $key => $value) {
            if( !empty( $value ) ) {
                $result[] = $value; 
            }
        } 
        
        return $result;
    }    
    
    function wpsf_clean_output( $buffer ) {
    
        $pat[0] = "/^\s+/";
        $pat[1] = "/\s{2,}/";
        $pat[2] = "/\s+\$/";
        
        $rep[0] = "";
        $rep[1] = " ";
        $rep[2] = "";
        
        $buffer = preg_replace( $pat, $rep, $buffer );
    
        return $buffer;
    }
    
    function wpsf_seperate_lines( $string_array = array() ) {
    
        $result = '';
        
        for( $idx=0; $idx<sizeof( $string_array ); $idx++ ) {
            $result .= $string_array[$idx].chr(13).chr(10);
        }
        
        return $result;
    }
    
    function wpsf_line_split( $string_array = '' ) {
    
        $result = array();
        
        $lines = preg_split( '/[\r\n]/', $string_array );
        
        for( $idx=0; $idx<sizeof( $lines ); $idx++ ) {
            $value = trim( $lines[$idx] );
            if ( !empty( $value ) ) {
                $result[] = $value;
            }
        }
        
        return $result;
    }
    
    function wpsf_valid_email( $email ) {
    
        $result = true;
    
        if ( !preg_match( "/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $email ) ) {
            $result = false;                                    
        }
        
        return $result;
    }
    
    function wpsf_gen_password( $length = 7 ){
    
        srand((double)microtime()*1000000);
        
        $vowels = array( "a", "e", "i", "o", "u" );
        $cons = array( "b", "c", "d", "g", "h", "j", "k", "l", "m", "n", "p", "r", "s", "t", "u", "v", "w", "tr", "cr", "br", "fr", "th", "dr", "ch", "ph", "wr", "st", "sp", "sw", "pr", "sl", "cl" );
        
        $num_vowels = count($vowels);
        $num_cons = count($cons);
        
        for($i = 0; $i < $length; $i++){
            $password .= $cons[rand(0, $num_cons - 1)] . $vowels[rand(0, $num_vowels - 1)];
        }
        
        return substr( $password, 0, $length );
    }
    
    function wpsf_sub_token( $message, $tokens ) {
    
        $result = $message;
        
        if ( is_array( $tokens ) ) {
            foreach( $tokens as $token_name => $token_value ) {
                $result = wpsf_mb_replace( '{{'.$token_name.'}}', $token_value, $result );
            }
        }
        
        return $result;
    }

    // (c)heck http or https
    function wpsf_chttp() {
    
        $result = 'http';
    
        if ( wpsf_url_https() ) {
            $result = 'https';
        }
                
        return $result;
    }
    
    function wpsf_http_detect( $url = '' ) {
    
        $result = trim( $url );
            
        $result = preg_replace( "/^https:\/\//i", '', $result );
        $result = preg_replace( "/^http:\/\//i", '', $result );
        
        $result = wpsf_chttp().'://'.$result;
                
        return $result;
    }
    
    function wpsf_var_safe( $string ) {
    
        $result = '';

        if ( preg_match( '/^[A-Za-z][A-Za-z0-9]+$/', $string ) ) {
            $result = $string;
        }
        
        return $result;
    }
    
    function wpsf_teaser( $string, $length, $tail = '..' ) {
    
        $result = substr( $string, 0, $length );

        if ( strlen( $string ) > $length ) {
            $result .= $tail;
        }
        
        return $result;
    }

    function wpsf_add_http( $url ) {

        if ( preg_match( "/^http/i", trim( $url ) ) ) {
            return $url;
        }
        elseif ( preg_match( "/^https/i", trim( $url ) ) ) {
            return $url;
        }
        
        return wpsf_chttp().'://'.$url;
    }
        
    function wpsf_url_https() {
    
        if ( !empty( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443 ) {
            return true;
        }
        
        return false;
    }
         
    function wpsf_seo_url( $str = '' ) {
    
        $str = strtolower(trim($str));
        $str = preg_replace('/[^a-z0-9-]/', '-', $str);
        $str = preg_replace('/-+/', "-", $str);
        
        return $str;
    }
    
    function wpsf_microtime_float() {
        list($usec, $sec) = explode(" ", microtime());
        return ((float)$usec + (float)$sec);
    }
    
    function wpsf_schemafeed_settings() {
    
        $result = wpsf_query( "select * from ".WPPF."sf_settings" );
        
        return $result;
    }
    
    function wpsf_get_created_date( $post_id ) {
    
        $result = '';
    
        $create_date = wpsf_query( "    select post_date from ".WPPF."posts
                                        where
                                        post_name = '{$post_id}-revision'
                                        " );
        
        if ( isset( $create_date[0] ) ) {
        
            $result = date( "c", strtotime( $create_date[0][ WPPF.'posts.post_date' ] ) );
        }
        
        return $result;
    }
    
    function wpsf_format__url( $value ) {
    
        $result = $value;
    
        // $href_1 = wpsf_http_detect( $value );
        // $result = '<a href="'.$href_1.'">publishingPrinciples</a>';
        
        return $result;
    }
    
    function wpsf_format__datetime( $value ) {
    
        $result = date( "F j, Y, g:i a", strtotime( $value ) );
        
        return $result;
    }
    
    function wpsf_format__datetime2( $value ) {
    
        $result = date( "g:i:a", strtotime( $value ) );
        
        return $result;
    }
    
    function wpsf_sub_values( $template, $sub_vals = array() ) {
    
        $result = $template;
                        
        foreach( $sub_vals as $key => $value ) {
        
            $result = wpsf_mb_replace( '[['.$key.']]', $value, $result );
        }                    
        
        return $result;
    }
       
    function wpsf_word_count( $content ) {
    
        $result = str_word_count( strip_tags( $content ), 0 );
    
        return $result;
    }
    
    function wpsf_csv2array( $input, $delimiter='¬' ) {
    
        $fields = explode( $delimiter, $input );
        
        foreach ( $fields as $key => $value ) {
            $fields[ $key ] = $value;
        }
        
        return $fields;
    } 
    
    function wpsf_mb_replace( $search, $replace, $subject, &$count=0 ) {
        
        if (!is_array($search) && is_array($replace)) {
            return false;
        }
        if (is_array($subject)) {
            // call mb_replace for each single string in $subject
            foreach ($subject as &$string) {
                $string = &mb_replace($search, $replace, $string, $c);
                $count += $c;
            }
        } elseif (is_array($search)) {
            if (!is_array($replace)) {
                foreach ($search as &$string) {
                    $subject = mb_replace($string, $replace, $subject, $c);
                    $count += $c;
                }
            } else {
                $n = max(count($search), count($replace));
                while ($n--) {
                    $subject = mb_replace(current($search), current($replace), $subject, $c);
                    $count += $c;
                    next($search);
                    next($replace);
                }
            }
        } else {
            $parts = mb_split(preg_quote($search), $subject);
            $count = count($parts)-1;
            $subject = implode($replace, $parts);
        }
        
        return $subject;
    }

  
?>