<?php
/**
 * Linked custom fields plugin for MantisBT
 *
 * Copyright (c) 2011 Robert Munteanu (robert@lmn.ro)
 * Copyright (c) 2018, 2022 Damien Regad
 *
 * Linked custom fields for MantisBT is free software:
 * you can redistribute it and/or modify it under the terms of the GNU
 * General Public License as published by the Free Software Foundation,
 * either version 2 of the License, or (at your option) any later version.
 *
 * Linked custom fields plugin for MantisBT is distributed in the hope
 * that it will be useful, but WITHOUT ANY WARRANTY; without even the
 * implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Linked custom fields plugin for MantisBT.
 * If not, see <http://www.gnu.org/licenses/>.
 */

class LinkedCustomFieldsDao {

	/**
	 * Replaces existing custom field link values with the current ones.
	 *
	 * Note that the link will be removed if there are no mappings defined.
	 *
	 * @param int $p_source_field_id
	 * @param int $p_target_field_id
	 * @param array $p_value_mappings map of source field value to target field value(s)
	 */
	static function replaceValues( $p_source_field_id, $p_target_field_id, $p_value_mappings ) {

		$t_data_table = plugin_table( 'data' );
		$t_query = "DELETE FROM " . $t_data_table . " WHERE custom_field_id = " . db_param();

		db_query( $t_query, array( $p_source_field_id ) ) ;

		$t_insert_query = "INSERT INTO " . $t_data_table . "
			(custom_field_id, custom_field_value_order, custom_field_value, target_field_id, target_field_values)
			VALUES ( " . db_param() .", " . db_param().", " . db_param().", ". db_param().", " .db_param()." )";

		$t_idx = 0 ;

		foreach( $p_value_mappings as $t_key => $t_value ) {
			db_query($t_insert_query, array( $p_source_field_id, $t_idx, $t_key, $p_target_field_id, implode( '|', $t_value ) ) );
			$t_idx++;
		}
	}

	/**
	 * Retrieve the Target custom field's Id
	 *
	 * @param int $p_source_field_id
	 * @return NULL|int the target field id or null if no link exists
	 */
	static function getLinkedFieldId( $p_source_field_id ) {

		$t_query = "SELECT target_field_id FROM " . plugin_table( 'data' ) . " WHERE custom_field_id = " . db_param();

		$t_result = db_query( $t_query, array( $p_source_field_id ) );

		if( 0 == db_num_rows( $t_result ) ) {
			return null;
		}

		$t_array = db_fetch_array( $t_result );
		return $t_array['target_field_id'];
	}

	/**
	 * Retrieve the values mappings between the source and target fields
	 *
	 * @param int $p_source_field_id
	 * @return array Structure of each row: SourceCF value => [TargetCF values]
	 */
	static function getLinkedValuesMap( $p_source_field_id ) {

		$t_query = "SELECT custom_field_value, target_field_values FROM " . plugin_table( 'data' ) .
					" WHERE custom_field_id=".db_param() ." ORDER BY custom_field_value_order" ;
		$t_result = db_query( $t_query, array( $p_source_field_id ) );
		if( 0 == db_num_rows( $t_result ) ) {
			return array();
		}

		$t_map = array();
		while( $t_row = db_fetch_array( $t_result ) ) {
			$t_source_value = $t_row['custom_field_value'];
			$t_target_values_imploded = $t_row['target_field_values'];
			$t_map[$t_source_value] = explode( '|', $t_target_values_imploded );
		}

		return $t_map;
	}
} 


class JavascriptUtils {

	const LOG_DEBUG = 1;
	const LOG_INFO = 2;
	const LOG_WARN = 3;
	const LOG_ERROR = 4;

	static function toJSArray( $p_array ) {

		$t_field_values_js = '[ ';

		foreach( $p_array as $t_custom_field_value ) {
			$t_field_values_js .= '"'.string_attribute( $t_custom_field_value ).'" ,';
		}

		$t_field_values_js = rtrim( $t_field_values_js, ',' );
		$t_field_values_js .= ']';

		return $t_field_values_js;
	}

	static function consoleLog( $p_message, $p_level = self::LOG_INFO ) {

		$t_method = false;
		switch( $p_level ) {
			case self::LOG_DEBUG:
				$t_method = 'debug';
				break;

			case self::LOG_INFO:
				$t_method = 'info';
				break;

			case self::LOG_WARN:
				$t_method = 'warn';
				break;

			case self::LOG_ERROR:
				$t_method = 'error';
				break;
		}

		if( $t_method ) {
			return 'if( console && console.' . $t_method . ' ) console.'
				. $t_method . '( "' . $p_message . '" );' . "\n";
		}
		return '';
	}
}
