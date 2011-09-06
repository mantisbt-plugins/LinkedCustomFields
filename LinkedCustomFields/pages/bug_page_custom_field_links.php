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
var allFieldValues = {};
var bindings = {};
var savedValues = {};
<?php

foreach ( $t_all_custom_field_ids as $t_custom_field_id ) {
    
    if ( ! ( custom_field_has_write_access($t_custom_field_id, $t_bug_id) ) ) {
        continue;
    }
    
    $t_linked_values = LinkedCustomFieldsDao::getLinkedValuesMap( $t_custom_field_id );
    
    if ( count ( $t_linked_values ) > 0 ) {
        
        // values from $t_custom_field_id trigger filter values from $t_linked_field_id 
        
        $t_linked_field_id = LinkedCustomFieldsDao::getLinkedFieldId( $t_custom_field_id );
        echo JavascriptUtils::consoleLog('Found linked field with id '. $t_custom_field_id . ' , linked to ' . $t_linked_field_id);
        
        $t_linked_field = custom_field_get_definition( $t_linked_field_id );
        echo 'savedValues["' . $t_linked_field_id .'"] = "' . custom_field_get_value(  $t_linked_field_id, $t_bug_id ) . '"'."\n";
        echo 'bindings["' . $t_custom_field_id.'"] = "'. $t_linked_field_id.'";'."\n";
        echo 'allFieldValues["' .$t_custom_field_id.'"] = ' . JavascriptUtils::toJSArray( explode('|', $t_linked_field['possible_values']) ).";\n";
        echo 'linkedFieldValues["'.$t_custom_field_id."\"] = {};\n";
        
        foreach ( $t_linked_values as $t_source_value => $t_target_values ) {
            echo 'linkedFieldValues["'.$t_custom_field_id.'"]["'. $t_source_value.'"] = ' . JavascriptUtils::toJSArray( $t_target_values ).";\n";
        }
    }
}
?>

var refreshLinkedValues = function(fieldId, fieldValue) {
    var targetValues = linkedFieldValues[fieldId][fieldValue];
    
    if ( ! targetValues ) {
        targetValues = allFieldValues[fieldId] ;
    }
    
    var targetFieldId = bindings[fieldId];
    
    var targetFieldRef = jQuery('[name=custom_field_' + targetFieldId  +']'); 
    
    targetFieldRef.empty();
    for ( var i = 0 ; i < targetValues.length; i++ ) {
        var targetValue = targetValues[i];
        console.info("Appending target value " + targetValue);
        targetFieldRef.append(jQuery('<option/>').
            attr('value', targetValue).
            text(targetValue));
    }
    
    targetFieldRef.val(savedValues[targetFieldId]);
    
    console.info("Legal values should be " + fieldValue + " -> " + targetValues);
};

jQuery(document).ready(function() {
    for ( boundKey in bindings ) {
        console.info("Bound " + boundKey +" -> " + bindings[boundKey]);
        
        var applicable = linkedFieldValues[boundKey];
        
        var customFieldRef = jQuery('[name=custom_field_'+boundKey+']'); 
        
        refreshLinkedValues(boundKey, customFieldRef.val());
        
        customFieldRef.change(function() {
            refreshLinkedValues(boundKey, jQuery(this).val());
        });
    }
});
