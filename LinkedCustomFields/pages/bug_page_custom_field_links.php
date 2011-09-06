<?php 
header ("Content-Type: text/javascript");

require_once 'core.php';

$t_bug_id = gpc_get_int('bug_id');

echo JavascriptUtils::consoleLog('Bug id is ' . $t_bug_id);

$t_project_id = bug_get_field( $t_bug_id, 'project_id' );

echo JavascriptUtils::consoleLog('Project id is ' . $t_project_id);

$t_all_custom_field_ids = custom_field_get_linked_ids( $t_project_id );

?>
var linkedFieldValues = {};
<?php

foreach ( $t_all_custom_field_ids as $t_custom_field_id ) {
    
    if ( ! ( custom_field_has_write_access($t_custom_field_id, $t_bug_id) ) ) {
        continue;
    }
    
    $t_linked_values = LinkedCustomFieldsDao::getLinkedValuesMap( $t_custom_field_id );
    if ( count ( $t_linked_values ) > 0 ) {
        $t_linked_field_id = LinkedCustomFieldsDao::getLinkedFieldId( $t_custom_field_id );
        echo JavascriptUtils::consoleLog('Found linked field with id '. $t_custom_field_id . ' , linked to ' . $t_linked_field_id);
        
        echo 'linkedFieldValues['.$t_linked_field_id.'] = [];';
        
        foreach ( $t_linked_values as $t_source_value => $t_target_values ) {
            echo 'linkedFieldValues['.$t_linked_field_id.']['. $t_source_value.'] = ' . JavascriptUtils::toJSArray( $t_target_values ).";\n";
        }
    }
}

?>
