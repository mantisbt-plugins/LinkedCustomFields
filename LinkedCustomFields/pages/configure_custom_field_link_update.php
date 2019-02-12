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

require_once( 'core.php' );

access_ensure_global_level( config_get( 'manage_custom_fields_threshold' ) );

form_security_validate( 'configure_custom_field_link' );

$t_value_mappings = array();

$f_source_field_id =  gpc_get_int( 'custom_field_id' );
$t_source_field = custom_field_get_definition( $f_source_field_id );
$t_source_field_values = explode ( '|', $t_source_field['possible_values'] );

$f_target_field_id = gpc_get_int('target_custom_field' );

foreach( $_POST as $f_post_key => $f_post_value ) {

    if( strpos($f_post_key, 'custom_field_linked_values_' ) === 0 ) {

        $t_source_value_index = substr( $f_post_key, strlen ('custom_field_linked_values_') );
        $t_source_value = $t_source_field_values[ $t_source_value_index ];
        $t_linked_value = gpc_get( $f_post_key );
        $t_value_mappings[$t_source_value] = $t_linked_value;
    }
}

form_security_purge( 'configure_custom_field_link' );

if( LinkedCustomFieldsDao::getLinkedFieldId( gpc_get_int('target_custom_field') ) ) {
    // plugin_get_current('target_field_already_linked')
    trigger_error( ERROR_GENERIC , ERROR );
} else {
    LinkedCustomFieldsDao::replaceValues( $f_source_field_id, $f_target_field_id , $t_value_mappings );
    header("Location: " . plugin_page( 'configure_custom_field_links.php' ) );
}
