<?php 

class LinkedCustomFieldsDao {
    
    /**
     * Replaces existing custom field link values with the current ones
     * 
     * @param int $p_source_field_id
     * @param int $p_target_field_id
     * @param array $p_value_mappings map of source field value to target field value(s)
     */
    static function replaceValues( $p_source_field_id, $p_target_field_id, $p_value_mappings ) {
        
        $t_data_table = plugin_table('data');
        $t_query = "DELETE FROM " . $t_data_table . " WHERE custom_field_id = " . db_param();
        
        db_query_bound( $t_query, array( $p_source_field_id )) ;

        $t_insert_query = "INSERT INTO " . $t_data_table . "
            (custom_field_id, custom_field_value, target_field_id, target_field_values)
            VALUES ( " . db_param() .", " . db_param().", " . db_param().", ".db_param()." )";
        
        foreach ( $p_value_mappings as $t_key => $t_value ) {
            db_query_bound($t_insert_query, array( $p_source_field_id, $t_key, $p_target_field_id, implode('|', $t_value)));
        }
    }
} 
?>