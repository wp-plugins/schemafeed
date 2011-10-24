<?php

class schema__get_schema_fields extends root {

    var $module_output = 'html';
    var $stop_infin = 0;

    function _module_outputs() {

        $this->allowed_outputs[ 'html' ] = array( 'path' => '/modules/schema/get_schema_fields/views/html.php', 'template' => 'blank' );
        $this->allowed_outputs[ 'json' ] = array( 'path' => '/modules/schema/get_schema_fields/views/json.php', 'template' => 'blank' );
    }

    function _init() {

    }

    function _error() {

        // assume no errors
        $result = false;

        $check_input = array();
        $check_input[] = array( 'field_name' => 'schema', 'nonempty' => '1' );
        $check_input[] = array( 'field_name' => 'post_id' );

        $errors = wpsf_error_check( $this->inputs, $check_input, $this );

        if ( $errors ) {

            $this->set_result( 'INPUT_ERROR' );

            $this->result[ 'errors' ] = $errors;
            
            $result = true;
        }

        return $result;
    }

    function _auth() {
    
        $result = true;
           
        return $result;
    }      

	function _bus_logic() {
	
        $inputs = $this->inputs;	
	
        $schema = $inputs[ 'schema' ]; 
        $post_id = $inputs[ 'post_id' ]; 

        $records_1 = wpsf_query( "  select * 
                                    from ".WPPF."sf_schema_fields
                                    left join ".WPPF."sf_schema_flags on ".WPPF."sf_schema_flags.type_name = ".WPPF."sf_schema_fields.type_name and ".WPPF."sf_schema_flags.field_name = ".WPPF."sf_schema_fields.field_name
                                    where
                                    ".WPPF."sf_schema_fields.type_name = '$schema'
                                    order by field_order
                                    " );
         
        // apply values to fields               
        for( $idx=0; $idx<sizeof( $records_1 ); $idx++ ) {
            
            $schema_type = $records_1[$idx][ 'schema_fields.type_name' ];
            $prop_name = $records_1[$idx][ 'schema_fields.field_name' ];
            
            $value = '';
            $value_lookup = '';
            if ( !empty( $post_id ) ) {
                $value = get_post_meta( $post_id, '_wpsf_'.$schema_type.'_'.$prop_name );
                $value_lookup = get_post_meta( $post_id, '_wpsf_lookups_'.$schema_type.'_'.$prop_name );
            }
            
            $records_1[$idx][ 'value' ] = $value; 
            $records_1[$idx][ 'value_lookup' ] = $value_lookup;
            
            $records_1[$idx][ 'sf_data_type_body' ] = $this->get_sf_data_type( $records_1[$idx][ 'schema_fields.sf_data_type' ], $schema, $prop_name, wpsf_vset( $value[0] ) );              
        }               
               
        $this->result[ 'schema_fields' ] = $records_1;         
    }
    
    function get_sf_data_type( $sf_data_type, $schema, $field_name, $user_value ) {
    
        $result = '';
        
        if ( $sf_data_type == 'lang_type_1' ) {
        
            $lang = array( "" => "", "af" => "Afrikaans","sq" => "Albanian","ar" => "Arabic","hy" => "Armenian","az" => "Azerbaijani","eu" => "Basque","be" => "Belarusian","bn" => "Bengali","bg" => "Bulgarian","ca" => "Catalan","zh-CN" => "Chinese (Simplified)","zh-TW" => "Chinese (Traditional)","hr" => "Croatian","cs" => "Czech","da" => "Danish","nl" => "Dutch","en" => "English","et" => "Estonian","tl" => "Filipino","fi" => "Finnish","fr" => "French","gl" => "Galician","ka" => "Georgian","de" => "German","el" => "Greek","gu" => "Gujarati","ht" => "Haitian Creole","iw" => "Hebrew","hi" => "Hindi","hu" => "Hungarian","is" => "Icelandic","id" => "Indonesian","ga" => "Irish","it" => "Italian","ja" => "Japanese","kn" => "Kannada","ko" => "Korean","la" => "Latin","lv" => "Latvian","lt" => "Lithuanian","mk" => "Macedonian","ms" => "Malay","mt" => "Maltese","no" => "Norwegian","fa" => "Persian","pl" => "Polish","pt" => "Portuguese","ro" => "Romanian","ru" => "Russian","sr" => "Serbian","sk" => "Slovak","sl" => "Slovenian","es" => "Spanish","sw" => "Swahili","sv" => "Swedish","ta" => "Tamil","te" => "Telugu","th" => "Thai","tr" => "Turkish","uk" => "Ukrainian","ur" => "Urdu","vi" => "Vietnamese","cy" => "Welsh","yi" => "Yiddish" );        
        
            $result .= '<select name="_wpsf_'.$schema.'_'.$field_name.'">';
            
            foreach( $lang as $key => $value ) {
            
                $selected = '';
                if ( $user_value == $key ) { $selected = ' selected="" '; }
                
                $result .= '<option value="'.$key.'" '.$selected.'>'.$value.'</option>';
            }
            
            $result .= '</select>';            
        }
        elseif ( $sf_data_type == 'boolean_type_1' ) {
        
            $lang = array( "" => "", "Y" => "Yes","N" => "No" );        
        
            $result .= '<select name="_wpsf_'.$schema.'_'.$field_name.'">';
            
            foreach( $lang as $key => $value ) {
            
                $selected = '';
                if ( $user_value == $key ) { $selected = ' selected="" '; }
                
                $result .= '<option value="'.$key.'" '.$selected.'>'.$value.'&nbsp;&nbsp;</option>';
            }
            
            $result .= '</select>';             
        }
        elseif ( $sf_data_type == 'duration_type_1' ) {
        
            // $('#date').datetime( { withTime: false, format: 'yy-mm-dd' } );
            // $('#time').datetime( { withDate: false, format: 'hh:ii' } );
            // $('#time').datetime( { format: "yy-mm-dd hh:ii O" } ); 
        
            $result = ' <input style="width: 50px;" type="text" id="'.$field_id.'_wpsf__'.$field_name.'" name="_wpsf_'.$schema.'_'.$field_name.'" value="'.$value.'">
                        <script>
                            $( "#'.$field_id.'_wpsf__'.$field_name.'" ).datetime( { withDate: false, format: "hh:ii" } );
                        </script>
                        ';             
        }
        elseif ( $sf_data_type == 'date_type_1' ) {
        
            $result = ' <input style="width: 200px;" type="text" id="'.$field_id.'_wpsf__'.$field_name.'" name="_wpsf_'.$schema.'_'.$field_name.'" value="'.$value.'">
                        <script>
                            $( "#'.$field_id.'_wpsf__'.$field_name.'" ).datetime( { format: "yy-mm-dd hh:ii O" } );
                        </script>
                        ';             
        }
        elseif ( $sf_data_type == 'date_type_2' ) {
        
            $result = ' <input style="width: 200px;" type="text" id="'.$field_id.'_wpsf__'.$field_name.'" name="_wpsf_'.$schema.'_'.$field_name.'" value="'.$value.'">
                        <script>
                            $( "#'.$field_id.'_wpsf__'.$field_name.'" ).datetime( { format: "yy-mm-dd" } );
                        </script>
                        ';             
        }
        elseif ( $sf_data_type == 'gender_type_1' ) {
        
            $lang = array( "" => "", "M" => "Male","F" => "Female" );        
        
            $result .= '<select name="_wpsf_'.$schema.'_'.$field_name.'">';
            
            foreach( $lang as $key => $value ) {
            
                $selected = '';
                if ( $user_value == $key ) { $selected = ' selected="" '; }
                
                $result .= '<option value="'.$key.'" '.$selected.'>'.$value.'&nbsp;&nbsp;</option>';
            }
            
            $result .= '</select>';             
        }
        elseif ( $sf_data_type == 'textarea_1' ) {
        
            $result = '<textarea style="width: 400px; height: 80px;" id="'.$field_id.'_wpsf__'.$field_name.'" name="_wpsf_'.$schema.'_'.$field_name.'">'.$value.'</textarea>';             
        }
        elseif ( $sf_data_type == 'currency_1' ) {
        
            $currency = array(  "" => "", "AUD" => "Australian Dollar (AUD)","BRL" => "Brazilian Real (BRL)", "GBP" => "British Pound (GBP)", "CAD" => "Canadian Dollar (CAD)", 
                                "CNY" => "Chinese Yuan Renminbi (CNY)", "DKK" => "Danish Krone (DKK)", "EUR" => "Euro (EUR)", "HKD" => "Hong Kong Dollar (HKD)", 
                                "INR" => "Indian Rupee (INR)", "JPY" => "Japanese Yen (JPY)", "KRW" => "Korean Won (KRW)", "MYR" => "Malaysian Ringgit (MYR)", 
                                "MXN" => "Mexican Peso (MXN)", "NZD" => "New Zealand Dollar (NZD)", "NOK" => "Norwegian Krone (NOK)", "SGD" => "Singapore Dollar (SGD)", 
                                "ZAR" => "South African Rand (ZAR)", "LKR" => "Sri Lanka Rupee (LKR)", "SEK" => "Swedish Krona (SEK)", "CHF" => "Swiss Franc (CHF)", 
                                "SYP" => "Syrian Pound (SYP)", "THB" => "Thai Baht (THB)", "USD" => "US Dollar (USD)" );        
        
            $result .= '<select name="_wpsf_'.$schema.'_'.$field_name.'">';
            
            foreach( $currency as $key => $value ) {
            
                $selected = '';
                if ( $user_value == $key ) { $selected = ' selected="" '; }
                
                $result .= '<option value="'.$key.'" '.$selected.'>'.$value.'</option>';
            }
            
            $result .= '</select>';            
        }
        
        return $result;
    }
       
    
}

?>
