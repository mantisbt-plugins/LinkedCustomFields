<?php
# Copyright (c) 2011 Robert Munteanu (robert@lmn.ro)

# Linked custom fields for MantisBT is free software:
# you can redistribute it and/or modify it under the terms of the GNU
# General Public License as published by the Free Software Foundation,
# either version 2 of the License, or (at your option) any later version.
#
# Linked custom fields plugin for MantisBT is distributed in the hope
# that it will be useful, but WITHOUT ANY WARRANTY; without even the
# implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
# See the GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with Linked custom fields plugin for MantisBT.
# If not, see <http://www.gnu.org/licenses/>.
//error_reporting(E_ALL);
//ini_set('display_errors', 'On');
	require_once( 'core.php' );

	auth_reauthenticate();
	
	access_ensure_global_level( config_get( 'manage_custom_fields_threshold' ) );

    html_robots_noindex();

    layout_page_header(plugin_lang_get( 'configure_custom_field_links' ));

    layout_page_begin(__FILE__);
    print_manage_menu( 'manage_overview_page.php' );

	$t_custom_fields = custom_field_get_ids();
	$t_supported_types = get_enum_element( 'custom_field_type', CUSTOM_FIELD_TYPE_ENUM ) . ', ' . 
						 get_enum_element( 'custom_field_type', CUSTOM_FIELD_TYPE_MULTILIST ) ;
	
?>
    <br />
    <div class="table-responsive">
    <table class="table table-striped table-bordered table-condensed">
        <thead>
            <tr class="row-category">
                <th><?php echo plugin_lang_get('custom_field') ?></th>
                <th><?php echo plugin_lang_get('linked_to') ?></th>
                <th><?php echo sprintf(plugin_lang_get('unsupported_field_type'), $t_supported_types); ?></th>
            </tr>
        </thead>
        <tbody>
    
<?php 
	foreach ( $t_custom_fields as $t_custom_field ) {
	    $t_custom_field_def = custom_field_get_definition( $t_custom_field );
	    if ( $t_custom_field_def['type'] == CUSTOM_FIELD_TYPE_ENUM || 
	         $t_custom_field_def['type'] == CUSTOM_FIELD_TYPE_MULTILIST) 
	    {
		    echo '<tr '. helper_alternate_class() .'>';
		    echo '<td>'. string_display( $t_custom_field_def['name'] ).'</td>';
		    $t_linked_field_id = LinkedCustomFieldsDao::getLinkedFieldId( $t_custom_field );
		    if ( $t_linked_field_id ) {
	            $t_linked_field = custom_field_get_definition( $t_linked_field_id );
	            echo '<td>' . $t_linked_field['name'] .'</td>';	        
		    } else {
		        echo '<td> None </td>';
		    }
		    echo '<td>';
	        print_link(plugin_page('configure_custom_field_link.php&custom_field_id='.$t_custom_field), plugin_lang_get('edit'));
		    echo '</td>';
		    echo '</tr>';
	    } 
	}
?>
        </tbody>
    </table>
    </div> 
<?php

    layout_page_end();
?>