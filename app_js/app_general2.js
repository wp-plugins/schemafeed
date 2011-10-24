
function wpsf_show_fields(schema) {
    
    if ( schema == '' ) {
        return;
    }    
    
    $( "#schema_fields" ).css('visibility','visible');
    $( "#schema_fields" ).html('');
    $( "#schema_fields" ).append( '<img style="margin: 8px 0 0 8px;" src="'+domain_root()+'wp-content/plugins/schemafeed/img/ajax-loader.gif">' );
    $( "#schema_fields" ).append( '<span style="font-size: 15px;">&nbsp;...Loading Schema Properties.</span>' );

    $.get(
        "admin.php?page=schema__get_schema_fields&noheader=1&v=json",
        "schema="+schema,
        function( data ) {
    
            var fields = '<p>Tick the property which you do not want to display.</p>';
            
            fields += '<form action="admin.php?page=wp__save_schema_property_flags&noheader=1" method="post">';  
        
            var field_content2 = $.parseJSON( data );
            
            if ( field_content2.length > 0 ) {
            
                for(var i=0; i<field_content2.length; i++) {

                    field_name = field_content2[i][ 'schema_fields.field_name' ];
                    data_type = field_content2[i][ 'schema_fields.data_type' ];
                    field_name_display = field_content2[i][ 'schema_fields.field_name_display' ];
                    admin_display_field = field_content2[i][ 'schema_fields.admin_display_field' ];
                             
                    if ( admin_display_field == 0 ) { continue; }                             
                                        
                    description = field_content2[i][ 'schema_fields.description' ]+' '+field_content2[i][ 'schema_fields.description2' ];
                    description = description.replace('"','\"');
                    
                    display_type = field_content2[i][ 'schema_fields.display_type' ];
                    description = description+' This is a '+display_type+'.'; 
                                       
                    state = field_content2[i][ 'schema_flags.state' ];
                    state2 = '';
                    if ( state == 1 ) {
                        state2 = ' checked="" ';
                    }
                    
                    fields += '<p>';
                    fields += '<input type="hidden" id="'+field_name+'" name="wpsf_'+field_name+'" value="0">&nbsp;';
                    fields += '<input type="checkbox" id="'+field_name+'" name="wpsf_'+field_name+'" value="1" '+state2+'>&nbsp;';
                    fields += '<label for="'+field_name+'">'+field_name_display+'</label>';
                    fields += '<a title="'+description+'" href="javascript://" class="schema_prop_help" style="text-decoration: none;">?</a>';
                    fields += '</p>';
                }
            }
             
            fields = '<p>'+fields+'</p>';                        
            fields += '<input type="hidden" name="type_name" value="'+schema+'">';
            fields += '<p class="submit"><input type="submit" value="Update Settings" name="submit" class="button-primary action"></p>';    
            fields += '<input type="hidden" name="redirect_by_token" value="CALLER"><input type="hidden" name="v" value="html">';
            fields += '</form>';                        
                        
            $( "#schema_fields" ).html( fields );
            
            $( ".schema_prop_help" ).easyTooltip();
        },
        "html"
    );
}

function wpsf_add_fields(schema,post_id) {
    
    if ( schema == '' ) {
        return;
    } 
    
    $( "#schema_fields" ).css('visibility','visible');
    $( "#schema_fields" ).html('');
    $( "#schema_fields" ).append( '<img style="margin: 8px 0 0 8px;" src="'+domain_root()+'wp-content/plugins/schemafeed/img/ajax-loader.gif">' );
    $( "#schema_fields" ).append( '<span style="font-size: 15px;">&nbsp;...Loading Schema Properties.</span>' );

    $.get(
        "admin.php?page=schema__get_schema_fields&noheader=1&v=json",
        "schema="+schema+"&post_id="+post_id,
        function( data ) {
    
            var fields = '';
        
            var field_content2 = $.parseJSON( data );
            
            if ( field_content2.length > 0 ) {
            
                for(var i=0; i<field_content2.length; i++) {

                    field_name = field_content2[i][ 'schema_fields.field_name' ];
                    data_type = field_content2[i][ 'schema_fields.data_type' ];
                    field_name_display = field_content2[i][ 'schema_fields.field_name_display' ];
                    auto_fill = field_content2[i][ 'schema_fields.auto_fill' ];
                    sf_data_type_body = field_content2[i][ 'sf_data_type_body' ];
                    admin_display_field = field_content2[i][ 'schema_fields.admin_display_field' ];
                    fields_ass_tmp = field_content2[i][ 'schema_fields.fields_ass_tmp' ];
                    fields_ass_tmp = '';
                    
                    if ( admin_display_field == 0 ) { continue; }    
                    
                    description = field_content2[i][ 'schema_fields.description' ]+' '+field_content2[i][ 'schema_fields.description2' ];
                    description = description.replace('"','\"');
                    
                    display_type = field_content2[i][ 'schema_fields.display_type' ];
                    description = description+' This is a '+display_type+'.'; 

                    meta_field = field_content2[i][ 'schema_fields.meta_field' ];
                    if ( meta_field == 1 ) {
                        description = description+' Not displayed, renders as a meta tag in HTML source.';    
                    }
                    
                    value = field_content2[i][ 'value' ];
                    lookup = field_content2[i][ 'value_lookup' ];
                                       
                    dialog_id = Math.floor(Math.random()*1000000000);
                    field_id = Math.floor(Math.random()*1000000000);
                    field_input = '<tr id="'+field_id+'">';
                    field_input += '<td style="white-space: nowrap; width: 20%;" valign="top" scope="row">'+field_name_display+' '+fields_ass_tmp+':&nbsp;<a style="text-decoration: none;" title="'+description+'" href="javascript://" class="schema_prop_help">?</a></td>';
                    field_input += '<td valign="top" scope="row">';
                        
                        if ( auto_fill == 1 ) {
                            field_input += '<div style="line-height: 15px;">Automatically added.</div>';
                        }
                        else {
                            if ( sf_data_type_body != '' ) {
                                field_input += sf_data_type_body;
                            }
                            else {
                                field_input += '<input style="width: 80%;" type="text" id="'+field_id+'_wpsf__'+field_name+'" name="_wpsf_'+schema+'_'+field_name+'" value="'+value+'">';
                                field_input += '<input type="hidden" id="'+field_id+'_wpsf__'+field_name+'_lookups" name="_wpsf_lookups_'+schema+'_'+field_name+'" value="'+lookup+'">';
                            }
                        }
                    field_input += '</td>';    
                    
                    /*
                    // lookup
                    if (    ( data_type != 'Boolean' ) &&
                            ( data_type != 'Date' ) &&
                            ( data_type != 'Number' ) &&
                            ( data_type != 'Float' ) &&
                            ( data_type != 'Integer' ) &&
                            ( data_type != 'Text' ) &&
                            ( data_type != 'URL' ) &&
                            ( data_type != 'Duration' ) )
                    {
                        field_input += '<a href="javascript://" onclick="wpsf_record_lookup( \''+data_type+'\', \''+field_id+'\', this, \''+field_name+'\' );">LU</a>&nbsp;';
                        field_input += '<a style="font-size: 11px;" href="javascript://" onclick="wpsf_add_record( \''+data_type+'\', \''+dialog_id+'\' );">Add New Entry</a>';
                    } 
                    */     
                    
                    field_input += '</tr>';              
                    
                    fields += field_input;
                }
            }
            
            fields = '<table class="form-table">'+fields+'</table><br /><br /><br />';
            
            $( "#schema_fields" ).html( fields );
            
            $( ".schema_prop_help" ).easyTooltip();
    
        },
        "html"
    );
    
}

function wpsf_add_record(schema, dialog_win) {
    
    // 1. bring up window
    
    if ( $( '#'+dialog_win ).length ) {
    
        $( "#"+dialog_win ).dialog( "open" );
    }
	else {
	
        // get fields
        $.get(
            "http://localhost/wordpress/wp-admin/admin.php?page=schema__get_schema_fields&noheader=1&v=json",
            "schema="+schema,
            function( data ) {
        
                var fields = '';
            
                var field_content2 = $.parseJSON( data );
                
                if ( field_content2.length > 0 ) {
                    for(var i=0; i<field_content2.length; i++) {
                    
                        field_name = field_content2[i][ 'schema_fields.field_name' ];
                        data_type = field_content2[i][ 'schema_fields.data_type' ];
                                                
                        dialog_id = Math.floor(Math.random()*1000000000);
                        field_input = field_name+':&nbsp;<input type="text" name="_wpsf__'+field_name+'" value="">';
                        
                        if (    ( data_type != 'Boolean' ) &&
                                ( data_type != 'Date' ) &&
                                ( data_type != 'Number' ) &&
                                ( data_type != 'Float' ) &&
                                ( data_type != 'Integer' ) &&
                                ( data_type != 'Text' ) &&
                                ( data_type != 'URL' ) &&
                                ( data_type != 'Duration' ) )
                        {
                            field_input += '<a style="font-size: 11px;" href="javascript://" onclick="wpsf_add_record( \''+data_type+'\', \''+dialog_id+'\' );">Add New Entry</a>';
                        }      
                        
                        field_input += '<br />';              
                        
                        fields += field_input;
                    }
                }
                
                // dialog 
                var dialog = '';
                dialog += '<div id="'+dialog_win+'" title="Create New Record: '+schema+'">';
                // dialog += '<p class="validateTips">All form fields are required'+dialog_win+'.</p>';
                dialog += '<form id="dialog_form_'+dialog_win+'">';
                    dialog += fields;
                dialog += '</form>';
                dialog += '</div>';
                
                $( "#dialog_div" ).append( dialog );
                    
                // now create dialog                
                $( "#"+dialog_win ).dialog({
        			autoOpen: true,
        			height: 400,
        			width: 500,
        			modal: false,
        			buttons: {
        				"Create Record": function() {
                            
                            dataString = $( '#dialog_form_'+dialog_win ).serialize();
                            
                            $.ajax({  
                                type: "POST",  
                                url: "http://localhost/wordpress/wp-admin/admin.php?page=schema__save_schema_record&noheader=1",  
                                data: dataString,  
                                success: function() {  
                                    alert('Saved.');
                                }  
                            });
        					
        				},
        				Cancel: function() {
        					$( this ).dialog( "close" );
        				}
        			},
        			close: function() {
        				
        			}
        		}); 
        
            },
            "html"
        );         
           	   
	}
}

function wpsf_record_search( field_id, field_name ) {

    var search = $( '#lookup_search_'+field_id ).val();

    $.get(
            "http://localhost/wordpress/wp-admin/admin.php?page=schema__schema_search&noheader=1&v=json",
            "search="+search,
            function( data ) {
        
                var fields = '';
            
                var field_content2 = $.parseJSON( data );
                
                if ( field_content2.records.length > 0 ) {
                
                    for(var i=0; i<field_content2.records.length; i++) {
                    
                        ID = field_content2.records[i][ 'ID' ];
                        post_content = field_content2.records[i][ 'post_content' ];
                        post_title = field_content2.records[i][ 'post_title' ];                                                
                        
                        fields += '<a href="javascript://" onclick="wpsf_add_lookup_value(\''+ID+'\',\''+field_id+'\',\''+field_name+'\',\''+post_title+'\');">'+post_title+'<br />';
                    }
                }
                
                $( '#lookup_unselected_'+field_id ).html( fields );
                
            },
            "html"
        );     
}

function wpsf_record_lookup(schema,field_id,input_obj,field_name) {
    
    // hide/close all first
    $( '.lookup_boxes' ).hide(); 
    
    if ( $( '#lookup_div_box_'+field_id ).length ) {
        $( '#lookup_div_box_'+field_id ).show();
    }
    else {
    
        var lookup_data = '<div>Search:&nbsp;<input id="lookup_search_'+field_id+'" type="text" name="" value=""><input onclick="wpsf_record_search( \''+field_id+'\',\''+field_name+'\' );" type="button" name="search" value="Search"><a href="javascript://" onclick="wpsf_search_close( \''+field_id+'\' );">X</a></div>';
        
        // lookup_unselected, use ajax to lookup names 
        var lookup_unselected = '';
        post_id = '123';
        lookup_data += '<div id="lookup_unselected_'+field_id+'" style="border: 1px solid #0f0; height: 70px; width: 120px; display: inline-block;">'+lookup_unselected+'</div>';
        
        // lookup_selected, use ajax to lookup names
        var lookup_selected = '';
        lookup_data += '<div id="lookup_selected_'+field_id+'" style="border: 1px solid #eee; width: 100px; display: inline-block;">'+lookup_selected+'</div>';
        
        $( '#'+field_id ).append( '<div class="lookup_boxes" id="lookup_div_box_'+field_id+'" style="background-color: #fff; border: 1px solid #f00; width: 300px; height: 100px; position: absolute;">'+lookup_data+'</div>' );
    }    
}

function wpsf_search_close( field_id ) {
    $( '#lookup_div_box_'+field_id ).hide();    
}

function wpsf_add_lookup_value( post_id, field_id, field_name, post_title ) {

    // disable normal text input once there is one or more lookup
    
    var add_item = 1;
    
    // check its not already in there
    var lookups =  $( '#'+field_id+'_wpsf__'+field_name+'_lookups' ).val();
    var lookups2 = ( lookups == '' ) ? new Array() : lookups.split(',');
    
    for(i = 0; i < lookups2.length; i++){
        if ( post_id == lookups2[i] ) {
            // already in list, do nothing
            add_item = 0;
            return;
        }
    }
    
    // ## add item to hidden list
    if ( add_item == 1 ) {
        lookups2.push( post_id );
        $( '#'+field_id+'_wpsf__'+field_name+'_lookups' ).val( lookups2.join(',') );
    }
    
    // ## add item to visual list
    $( '#lookup_selected_'+field_id ).append( '<a href="">'+post_title+'</a><br />' );
}

function domain_root() {
    
    var path = window.location.pathname.split('/');

    root_path = '';

    for ( i=0; i<=path.length; i++ ) {
        if ( path[i] == 'wp-admin' ) {
            break;             
        }
        root_path += path[i]+'/';     
    }
    
    return window.location.protocol+'//'+window.location.host+root_path;
}

$(document).ready(function(){	
	
});
