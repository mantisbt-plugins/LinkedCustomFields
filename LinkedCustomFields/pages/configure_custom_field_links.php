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

auth_reauthenticate();
access_ensure_global_level( config_get( 'manage_custom_fields_threshold' ) );

$t_page_title = plugin_lang_get( 'configure_custom_field_links' );

html_robots_noindex();
layout_page_header( $t_page_title );
layout_page_begin(__FILE__);
print_manage_menu( 'configure_custom_field_links' );

$t_custom_fields = custom_field_get_ids();
$t_supported_types = get_enum_element( 'custom_field_type', CUSTOM_FIELD_TYPE_ENUM ) . ', ' .
                     get_enum_element( 'custom_field_type', CUSTOM_FIELD_TYPE_MULTILIST ) ;

?>

<div class="col-md-12 col-xs-12">
<div class="space-10"></div>

<div class="table-container">
    <div class="widget-box widget-color-blue2">
        <div class="widget-header widget-header-small">
            <h4 class="widget-title lighter">
                <i class="ace-icon fa fa-flask"></i>
                <?php echo $t_page_title ?>
            </h4>
        </div>

        <div class="widget-body">
            <div class="widget-main no-padding">
                <div class="widget-toolbox padding-8 clearfix">
                    <?php echo sprintf(plugin_lang_get( 'unsupported_field_type' ), $t_supported_types); ?>
                </div>

                <div class="table-responsive">

                    <table class="table table-striped table-bordered table-condensed">
                        <thead>
                            <tr class="row-category">
                                <th><?php echo plugin_lang_get( 'custom_field' ) ?></th>
                                <th><?php echo plugin_lang_get( 'linked_to' ) ?></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>

<?php
foreach ( $t_custom_fields as $t_custom_field ) {
	$t_custom_field_def = custom_field_get_definition( $t_custom_field );
	if ( $t_custom_field_def['type'] == CUSTOM_FIELD_TYPE_ENUM ||
		 $t_custom_field_def['type'] == CUSTOM_FIELD_TYPE_MULTILIST)
	{
		echo '<td>'. string_display_line( $t_custom_field_def['name'] ).'</td>';
		$t_linked_field_id = LinkedCustomFieldsDao::getLinkedFieldId( $t_custom_field );
		if ( $t_linked_field_id ) {
			$t_linked_field = custom_field_get_definition( $t_linked_field_id );
			echo '<td>' . $t_linked_field['name'] .'</td>';
		} else {
			echo '<td> None </td>';
	}
		echo '<td>';
		print_small_button(
			plugin_page('configure_custom_field_link' ) . "&custom_field_id=$t_custom_field",
			plugin_lang_get('edit')
		);
		echo '</td>';
		echo '</tr>';
	}
}
?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

</div>

<?php
layout_page_end();
