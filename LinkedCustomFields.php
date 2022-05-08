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

class LinkedCustomFieldsPlugin extends MantisPlugin {

	/**
	 * Error strings
	 */
	const ERROR_ALREADY_LINKED = 'error_already_linked';

	public function register() {
		$this->name = plugin_lang_get( "title" );
		$this->description = plugin_lang_get( "description" );

		$this->version = "2.0.0-dev";
		$this->requires = array(
			"MantisCore" => "2.3.0",
		);

		$this->author = "Robert Munteanu";
		$this->contact = "robert@lmn.ro";
		$this->url = "https://mantisbt.org/wiki/doku.php/mantisbt:linkedcustomfields";
	}

	public function hooks() {
		return array(
			'EVENT_MENU_MANAGE' => 'manage_custom_field_links',
			'EVENT_LAYOUT_RESOURCES' => 'resources',
			'EVENT_REST_API_ROUTES' => 'routes',
		);
	}

	function errors() {
		return array(
			self::ERROR_ALREADY_LINKED => plugin_lang_get( self::ERROR_ALREADY_LINKED ),
		);
	}

	/** @noinspection PhpUnused, PhpUnusedParameterInspection */
	public function manage_custom_field_links( $p_is_admin ) {
		return array(
			'<a href="' . plugin_page( 'configure_links' ) . '">'
			. plugin_lang_get( 'configure_custom_field_links')
			. '</a>',
		);
	}

	/**
	 * @param $p_event
	 * @return string
	 *
	 * @noinspection PhpUnusedParameterInspection
	 */
	function resources( $p_event ) {
		$t_bug_id = gpc_get_int( 'bug_id', -1 );
		$t_m_id = gpc_get_int( 'm_id', 0 );
		if( $t_bug_id == -1
			&& basename( $_SERVER['SCRIPT_NAME'] ) == 'bug_report_page.php'
		) {
			$t_bug_id = 0;
		}
		if( $t_bug_id != -1 ) {
			return '<script type="text/javascript" src="'
				. plugin_page( 'bug_page_custom_field_links.php' )
				. '&amp;bug_id=' . $t_bug_id . '&amp;m_id=' . $t_m_id
				. '"></script>';
		}
		return '';
	}

	public function init() {
		require_once 'LinkedCustomFields.API.php';
	}

	public function schema() {
		return array(
			array( 'CreateTableSQL',
				array( plugin_table( 'data' ), "
					custom_field_id             I      NOTNULL,
					custom_field_value_order    I      NOTNULL,
					custom_field_value          C(255) NOTNULL DEFAULT \" '' \",
					target_field_id             I      NOTNULL,
					target_field_values         C(255) NOTNULL DEFAULT \" '' \"
				"),
			),
			array( 'AlterColumnSQL',
				array( plugin_table( 'data' ), " custom_field_value XL, target_field_values XL")
			)
		);
	}

	/**
	 * Add the RESTful routes handled by this plugin.
	 *
	 * @param string $p_event_name The event name
	 * @param array  $p_event_args The event arguments
	 * @return void
	 *
	 * @noinspection PhpUnused, PhpUnusedParameterInspection
	 */
	public function routes( $p_event_name, $p_event_args ) {
		$t_app = $p_event_args['app'];
		$t_plugin = $this;
		$t_app->group(
			plugin_route_group(),
			function() use ( $t_app, $t_plugin ) {
				$t_app->get( '/values/{field_id}', [$t_plugin, 'route_values'] );
				$t_app->get( '/mapping/{field_id}', [$t_plugin, 'route_mapping'] );
			}
		);
	}

	/**
	 * RESTful route for Custom Fields values.
	 *
	 * Returned JSON structure:
	 * - {array}      List of possible values for given custom field
	 *
	 * @param Slim\Http\Request $request
	 * @param Slim\Http\Response $response
	 * @param array $args [bug_id = Bug Id for patterns replacement]
	 * @return Slim\Http\Response
	 *
	 * @noinspection PhpUnusedParameterInspection
	 */
	public function route_values( $request, $response, $args ) {
		# Set the reference Bug Id for placeholders replacements
		if( isset( $args['field_id'] ) ) {
			$t_field_id = (int)$args['field_id'];

			# Retrieve Custom Field definition
			$t_custom_field_def = custom_field_cache_row( $t_field_id, false );
		} else {
			$t_custom_field_def = false;
		}

		# Make sure Custom Field exists and its type is supported
		if( !$t_custom_field_def ) {
			return $response->withStatus(
				HTTP_STATUS_BAD_REQUEST,
				"Invalid Custom Field Id"
			);
		}
		switch( $t_custom_field_def['type'] ) {
			case CUSTOM_FIELD_TYPE_ENUM:
			case CUSTOM_FIELD_TYPE_MULTILIST:
				break;
			default:
				return $response->withStatus(
					HTTP_STATUS_BAD_REQUEST,
					"Unsupported Custom Field Type"
				);
		}

		# Return possible values
		return $response
			->withStatus( HTTP_STATUS_SUCCESS )
			->withJson(
				explode( '|', $t_custom_field_def['possible_values'] )
			);
	}

	/**
	 * RESTful route for Custom Fields link mappings.
	 *
	 * Returned JSON structure:
	 * - {array}      List of CF link mappings, with structure
	 *   - {array}    CF mapping
	 *     - {string}  Source CF value
	 *     - {array}   Allowed target CF values
	 *
	 * @param Slim\Http\Request $request
	 * @param Slim\Http\Response $response
	 * @param array $args [bug_id = Bug Id for patterns replacement]
	 * @return Slim\Http\Response
	 *
	 * @noinspection PhpUnusedParameterInspection
	 */
	public function route_mapping( $request, $response, $args ) {
		# Set the reference Bug Id for placeholders replacements
		if( isset( $args['field_id'] ) ) {
			$t_field_id = (int)$args['field_id'];

			plugin_push_current( $this->basename );
			$t_map = LinkedCustomFieldsDao::getLinkedValuesMap( $t_field_id );
			plugin_pop_current();
		} else {
			return $response->withStatus(
				HTTP_STATUS_BAD_REQUEST,
				"Invalid Custom Field Id"
			);
		}

		# Return possible values
		return $response
			->withStatus( HTTP_STATUS_SUCCESS )
			->withJson( $t_map );
	}

}
