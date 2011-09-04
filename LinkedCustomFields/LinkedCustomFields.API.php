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
    
    /**
     * 
     * @param int $p_source_field_id
     * @return NULL|int the target field id or null if no link exists
     */
    static function getLinkedFieldId( $p_source_field_id ) {
        
        $t_query = "SELECT target_field_id FROM " . plugin_table('data') . " WHERE custom_field_id = " . db_param();
        
        $t_result = db_query_bound( $t_query, array ( $p_source_field_id ) );
        
        if( 0 == db_num_rows( $t_result ) ) {
            return null;
        } 
        
        $t_array = db_fetch_array( $t_result );
        return $t_array['target_field_id'];
    }
    
    static function getLinkedValuesMap( $p_source_field_id ) {
        
        $t_query = "SELECT custom_field_value, target_field_values FROM " . plugin_table('data') . " WHERE custom_field_id=".db_param();
        $t_result = db_query_bound( $t_query, array ( $p_source_field_id ) );
        if ( 0 == db_num_rows ( $t_result ) ) {
            return array();
        }
        
        $t_return = array();
        
        for ( $i = 0 ; $i < db_num_rows( $t_result); $i++ ) {
            $t_array = db_fetch_array( $t_result );            
            $t_source_value = $t_array['custom_field_value'];
            $t_target_values_imploded = $t_array['target_field_values'];
            
            $t_return[$t_source_value] = explode( '|', $t_target_values_imploded );
        }
        
        return $t_return;
    }
} 


class JavascriptUtils {
    
    static function toJSArray( $p_array ) {
        
        $t_field_values_js = '[ ';
        
        foreach (  $p_array as $t_custom_field_value ) {
            $t_field_values_js .= '"'.$t_custom_field_value.'" ,';
        }
        
        $t_field_values_js = rtrim($t_field_values_js, ',');
        $t_field_values_js .= ']';
        
        return $t_field_values_js;
    }
}
?>