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
echo '<script type="text/javascript" src="' . plugin_file( 'config.js' ) . '"></script>';
layout_page_begin( __FILE__ );
print_manage_menu( 'configure_links' );

$f_custom_field = custom_field_get_definition( gpc_get_int('custom_field_id') );
$t_linked_custom_field_id = LinkedCustomFieldsDao::getLinkedFieldId( $f_custom_field['id'] );

$t_target_candidates = array();

$t_custom_fields = custom_field_get_ids();
foreach( $t_custom_fields as $t_custom_field ) {
	$t_custom_field_def = custom_field_get_definition( $t_custom_field );
	if ( $t_custom_field_def['type'] != CUSTOM_FIELD_TYPE_ENUM && $t_custom_field_def['type'] != CUSTOM_FIELD_TYPE_MULTILIST ) {
		continue;
	}

	$t_target_candidates[] = $t_custom_field_def;
}
?>

<div class="col-md-12 col-xs-12">
<div class="space-10"></div>

<div class="table-container">
	<form method="post" action="<?php echo plugin_page( 'link_update' ) ?>">
		<?php echo form_security_field( 'configure_custom_field_link' ) ?>
		<input type="hidden" name="custom_field_id" id="custom_field_id"
			   value="<?php echo gpc_get_int('custom_field_id')?>" />

		<div class="widget-box widget-color-blue2">
			<div class="widget-header widget-header-small">
				<h4 class="widget-title lighter">
					<i class="ace-icon fa fa-flask"></i>
					<?php echo $t_page_title  ?>
				</h4>
			</div>

			<div class="widget-body">
				<div class="widget-main no-padding">
					<div class="table-responsive">
						<table class="table table-striped table-bordered table-condensed">
							<tbody>
                                <tr>
                                    <th width="30%"><?php echo plugin_lang_get('custom_field') ?></th>
                                    <td><?php echo $f_custom_field['name'] ?></td>
                                </tr>
                                <tr>
                                    <th><?php echo plugin_lang_get('linked_to') ?></th>
                                    <td>
                                        <select id="target_custom_field" name="target_custom_field">
                                            <option value="">None</option>
<?php
	foreach( $t_target_candidates as $t_target_candidate ) {
		if ( $t_target_candidate['id'] == $f_custom_field['id'] ) {
			continue;
		}

		$t_selected = $t_linked_custom_field_id == $t_target_candidate['id'] ? ' selected="selected"' : "";

		echo '<option' . $t_selected . ' value="' . $t_target_candidate['id'] .'">'.$t_target_candidate['name'].'</option>';
	}
?>
                                        </select>
                                        <span id="loading"><?php echo plugin_lang_get( 'loading' ); ?></span>
                                    </td>
                                </tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>

			<div class="widget-toolbox padding-8 clearfix">
				<button type="submit" class="btn btn-primary btn-white btn-round lcf_target">
					<?php echo plugin_lang_get( 'submit' ) ?>
				</button>
			</div>
		</div>

		<div class="space-10"></div>

		<div id="link_area" class="widget-box widget-color-blue2 hidden">
			<div class="widget-header widget-header-small">
				<h4 class="widget-title lighter">
					<i class="ace-icon fa fa-sitemap"></i>
					<?php echo plugin_lang_get( 'configure_mappings')  ?>
				</h4>
			</div>

            <div class="widget-toolbox padding-8 clearfix">
                <?php echo sprintf( plugin_lang_get( 'warning_no_mapping' ), $f_custom_field['name'] ) ; ?>
            </div>

			<div class="widget-body">
				<div class="widget-main no-padding">
					<div class="table-responsive">
						<table class="table table-striped table-bordered table-condensed" align="center">
							<thead>
								<tr>
									<th><?php echo plugin_lang_get('source_field_value')?></th>
									<th><?php echo plugin_lang_get('target_field_values')?></th>
								</tr>
							</thead>
							<tbody>
<?php foreach( explode('|', $f_custom_field['possible_values'] ) as $t_idx => $t_possible_value ) { ?>
								<tr>
									<td> <?php echo $t_possible_value ?></td>
									<td>
										<select id="custom_field_linked_values_<?php echo $t_idx?>"
												name="custom_field_linked_values_<?php echo $t_idx?>[]"
												class="lcf_target"
												multiple="multiple">
										</select>
									</td>
								</tr>
<?php } ?>
							</tbody>
						</table>
					</div>
				</div>

				<div class="widget-toolbox padding-8 clearfix">
					<button type="submit" class="btn btn-primary btn-white btn-round lcf_target">
						<?php echo plugin_lang_get( 'submit' ) ?>
					</button>
				</div>
			</div>
		</div>
	</form>
</div>
</div>

<?php
/*
<script type="text/javascript">
var targetValues = {};
<?php 
	foreach ( $t_target_candidates as $t_target_candidate ) {

		$t_field_values_js = JavascriptUtils::toJSArray( explode( '|', $t_target_candidate['possible_values'] ) );
		echo 'targetValues["' . $t_target_candidate['id'] .'"] = '.$t_field_values_js." ;\n";
	}
?>

var refreshTargetFieldOptions = function(targetFieldId) {
	var sourceValues = targetValues[jQuery("#custom_field_id").val()];
	var targetDisplayValues = targetValues[targetFieldId];

	var i = 0 ;
	for ( field in sourceValues ) {
		var sourceValue = sourceValues[field];
		var control = jQuery('#custom_field_linked_values_' + i);
		i++;

		control.empty();
		for ( field in targetDisplayValues ) {
			var displayValue = targetDisplayValues[field];
			control.append(jQuery('<option></option>', {
				value: displayValue,
				text: displayValue
			}));
		}
	}
}
jQuery(document).ready(function() {

	refreshTargetFieldOptions(jQuery('#target_custom_field').val());
	
	jQuery('#target_custom_field').change(function() {
		refreshTargetFieldOptions(jQuery(this).val());
	});
	<?php
		$t_existing_values = LinkedCustomFieldsDao::getLinkedValuesMap( $f_custom_field['id'] );
		foreach ( $t_existing_values as $t_idx => $t_values ) {

			list( $t_key, $t_value) = $t_values;
			echo 'jQuery("#custom_field_linked_values_'.$t_idx.'").val('. JavascriptUtils::toJSArray( $t_value ). ')'."\n";
			$t_idx++;
		}
	?>
});
</script>
*/
layout_page_end();
