<?php

// Prevent direct file access
defined( 'LS_ROOT_FILE' ) || exit;

class LS_Sliders {

	/**
	 * @var array $results Array containing the result of the last DB query
	 * @access public
	 */
	public static $results = array();



	/**
	 * @var int $count Count of found sliders in the last DB query
	 * @access public
	 */
	public static $count = null;



	/**
	 * Private constructor to prevent instantiate static class
	 *
	 * @since 5.0.0
	 * @access private
	 * @return void
	 */
	private function __construct() {

	}



	/**
	 * Returns the count of found sliders in the last DB query
	 *
	 * @since 5.0.0
	 * @access public
	 * @return int Count of found sliders in the last DB query
	 */
	public static function count() {
		return self::$count;
	}



	/**
	 * Find sliders with the provided filters
	 *
	 * @since 5.0.0
	 * @access public
	 * @param mixed $args Find any slider with the provided filters
	 * @return mixed Array on success, false otherwise
	 */
	public static function find( $args = array() ) {

		$userArgs = $args;

		// Find by slider ID
		if(is_numeric($args) && intval($args) == $args) {
			return self::_getById( (int) $args );

		// Random slider
		} elseif($args === 'random') {
			return self::_getRandom();

		// Find by slider slug
		} elseif(is_string($args)) {
			return self::_getBySlug($args);

		// Find by list of slider IDs
		} elseif(is_array($args) && isset($args[0]) && is_numeric($args[0])) {
			return self::_getByIds($args);

		// Find by query
		} else {

			// Defaults
			$defaults = array(
				'columns' => '*',
				'where' => '',
				'exclude' => array('removed'),
				'orderby' => 'date_c',
				'order' => 'DESC',
				'limit' => 30,
				'page' => 1,
				'groups' => false,
				'data' => true
			);

			// Merge user data with defaults
			foreach( $defaults as $key => $val ) {
				if( ! isset( $args[ $key ] ) ) {
					$args[ $key ] = $val;
				}
			}

			// Escape user data
			foreach( $args as $key => $val ) {
				if( $key !== 'where' ) {
					$args[ $key ] = esc_sql( $val );
				}
			}

			// Due to the nature of dynamically built queries we can't
			// use prepared statements or $wpdb::prepare(). By keeping
			// this function backwards compatible, we have even less
			// options to completely eliminate potential issues caused
			// by unhandled data.

			// In addition of using esc_sql(), we're performing some
			// further tests trying to filter out user data that might
			// not be handled properly prior to this function call.
			$columns = array('id', 'author', 'name', 'slug', 'data', 'date_c', 'date_m', 'flag_hidden', 'flag_deleted', 'schedule_start', 'schedule_end');

			$args['orderby'] 	= in_array($args['orderby'], $columns) ? $args['orderby'] : 'date_c';
			$args['order'] 		= ($args['order'] === 'DESC') ? 'DESC' : 'ASC';
			$args['limit'] 		= (int)  $args['limit'];
			$args['page'] 		= (int)  $args['page'];
			$args['groups'] 	= (bool) $args['groups'];
			$args['data'] 		= (bool) $args['data'];




			$exclude = array();
			if( $args['groups'] ) {
				$exclude[] = "group_id IS NULL";
			} else {
				$exclude[] = "flag_group = '0'";
			}


			if( ! empty( $args['exclude'] ) ) {

				if( in_array( 'hidden', $args['exclude'] ) ) {
					$exclude[] = "flag_hidden = '0'";
				}

				if( in_array( 'removed', $args['exclude'] ) ) {
					$exclude[] = "flag_deleted = '0'";
				}
			}

			$args['exclude'] = implode(' AND ', $exclude);

			// Where
			$where = '';
			if(!empty($args['where']) && !empty($args['exclude'])) {
				$where = "WHERE ({$args['exclude']}) AND ({$args['where']}) ";

			} elseif(!empty($args['where'])) {
				$where = "WHERE {$args['where']} ";

			} elseif(!empty($args['exclude'])) {
				$where = "WHERE {$args['exclude']} ";
			}

			// Some adjustments
			$args['limit'] = ($args['limit'] * $args['page'] - $args['limit']).', '.$args['limit'];

			// Build the query
			global $wpdb;
			$table = $wpdb->prefix.LS_DB_TABLE;
			$sliders = $wpdb->get_results("
				SELECT SQL_CALC_FOUND_ROWS {$args['columns']}
				FROM $table
				$where
				ORDER BY `{$args['orderby']}` {$args['order']}
				LIMIT {$args['limit']}

			", ARRAY_A);

			// Set counter
			$found = $wpdb->get_col("SELECT FOUND_ROWS()");
			self::$count = (int) $found[0];

			// Return original value on error
			if(!is_array($sliders)) { return $sliders; };

			// Parse slider data
			if($args['data']) {
				foreach($sliders as $key => $val) {
					$sliders[$key]['data'] = json_decode($val['data'], true);
				}
			}


			if( $args['groups'] ) {
				foreach( $sliders as $key => $val ) {

					if( $val['flag_group'] ) {

						$sliders[ $key ]['items'] = self::_getGroupItems( (int) $val['id'], $userArgs );
					}
				}
			}

			// Return sliders
			return $sliders;
		}
	}



	/**
	 * Add slider with the provided name and optional slider data
	 *
	 * @since 5.0.0
	 * @access public
	 * @param string $title The title of the slider to create
	 * @param array $data The settings of the slider to create
	 * @return int The slider database ID inserted
	 */
	public static function add($title = 'Unnamed', $data = array(), $slug = '', $groupId = NULL ) {

		global $wpdb;

		// Slider data
		$data = !empty($data) ? $data : array(
			'properties' => array(
				'createdWith' => LS_PLUGIN_VERSION,
				'sliderVersion' => LS_PLUGIN_VERSION,
				'title' => $title,
				'new' => true,
			),
			'layers' => array(array()),
		);

		// Fix WP 4.2 issue with longer varchars
		// than the column length
		if(strlen($title) > 99) {
			$title = substr($title, 0, (99-strlen($title)) );
		}

		// Popup?
		$popup = 0;
		if( ! empty($data['properties']['type']) && $data['properties']['type'] == 'popup') {
			$popup = 1;
		}

		// Insert slider, WPDB will escape data automatically
		$wpdb->insert($wpdb->prefix.LS_DB_TABLE, array(
			'group_id' => $groupId,
			'author' => get_current_user_id(),
			'name' => $title,
			'slug' => $slug,
			'data' => json_encode($data),
			'date_c' => time(),
			'date_m' => time(),
			'flag_popup' => $popup
		), array(
			'%d', '%d', '%s', '%s', '%s', '%d', '%d', '%d'
		));

		// Return insert database ID
		return $wpdb->insert_id;
	}



	/**
	 * Updates sliders
	 *
	 * @since 5.2.0
	 * @access public
	 * @param int $id The database ID of the slider to be updated
	 * @param string $title The new title of the slider
	 * @param array $data The new settings of the slider
	 * @return bool Returns true on success, false otherwise
	 */
	public static function update($id = 0, $title = 'Unnamed', $data = array(), $slug = '') {

		global $wpdb;

		// Slider data
		$data = !empty($data) ? $data : array(
			'properties' => array('title' => $title),
			'layers' => array(array()),
		);

		// Fix WP 4.2 issue with longer varchars
		// than the column length
		if(strlen($title) > 99) {
			$title = substr($title, 0, (99-strlen($title)) );
		}

		// Status
		$status = 0;
		if( empty($data['properties']['status']) || $data['properties']['status'] === 'false') {
			$status = 1;
		}

		// Schedule
		$schedule = array('schedule_start' => 0, 'schedule_end' => 0);
		foreach($schedule as $key => $val) {
			if( ! empty($data['properties'][$key]) ) {
				if( is_numeric($data['properties'][$key]) ) {
					$schedule[$key] = (int) $data['properties'][$key];
				} else {
					$schedule[$key] = ls_date_create_for_timezone( $data['properties'][$key] );
				}
			}
		}

		// Popup?
		$popup = 0;
		if( ! empty($data['properties']['type']) && $data['properties']['type'] == 'popup') {
			$popup = 1;
		}

		// Insert slider, WPDB will escape data automatically
		$wpdb->update($wpdb->prefix.LS_DB_TABLE, array(
				'name' => $title,
				'slug' => $slug,
				'data' => json_encode($data),
				'schedule_start' => $schedule['schedule_start'],
				'schedule_end' => $schedule['schedule_end'],
				'date_m' => time(),
				'flag_hidden' => $status,
				'flag_popup' => $popup
			),
			array('id' => $id),
			array('%s', '%s', '%s', '%d', '%d', '%d', '%d', '%d')
		);

		// Return insert database ID
		return true;
	}


	/**
	 * Marking a slider as removed without deleting it
	 * with its database ID.
	 *
	 * @since 5.0.0
	 * @access public
	 * @param int $id The database ID if the slider to remove
	 * @return bool Returns true on success, false otherwise
	 */
	public static function remove($id = null) {

		// Check ID
		if(!is_int($id)) { return false; }

		// Remove
		global $wpdb;
		$wpdb->update($wpdb->prefix.LS_DB_TABLE,
			array('flag_deleted' => 1),
			array('id' => $id),
			'%d', '%d'
		);

		return true;
	}


	/**
	 * Delete a slider by its database ID
	 *
	 * @since 5.0.0
	 * @access public
	 * @param int $id The database ID if the slider to delete
	 * @return bool Returns true on success, false otherwise
	 */
	public static function delete($id = null) {

		// Check ID
		if(!is_int($id)) { return false; }

		// Get slider data before deleting
		$slider = self::_getById( $id );

		// Delete
		global $wpdb;
		$wpdb->delete($wpdb->prefix.LS_DB_TABLE, array('id' => $id), '%d');

		// Check if the slider was part of a group
		// that might also need to be deleted.
		if( ! empty( $slider['group_id'] ) ) {
			self::checkForEmptyGroup( (int) $slider['group_id'] );
		}

		return true;
	}



	/**
	 * Restore a slider marked as removed previously by its database ID.
	 *
	 * @since 5.0.0
	 * @access public
	 * @param int $id The database ID if the slider to restore
	 * @return bool Returns true on success, false otherwise
	 */
	public static function restore($id = null) {

		// Check ID
		if(!is_int($id)) { return false; }

		// Remove
		global $wpdb;
		$wpdb->update($wpdb->prefix.LS_DB_TABLE,
			array('flag_deleted' => 0),
			array('id' => $id),
			'%d', '%d'
		);

		return true;
	}


	/**
	 * Adds the specified slider to the specified group
	 *
	 * @since 6.9.0
	 * @access public
	 * @param string $sliderId The slider database ID
	 * @param string $groupId The group database ID
	 * @return bool Returns true on success, false otherwise
	 */
	public static function addSliderToGroup( $sliderId, $groupId ) {

		global $wpdb;
		$wpdb->update( $wpdb->prefix.LS_DB_TABLE,
			array( 'group_id' => $groupId ),
			array( 'id' => $sliderId ),
			'%d',
			'%d'
		);

		return true;
	}


	/**
	 * Removes the specified slider from its group
	 *
	 * @since 6.9.0
	 * @access public
	 * @param string $sliderId The slider database ID
	 * @param string $groupId The group database ID
	 * @return bool Returns true on success, false otherwise
	 */
	public static function removeSliderFromGroup( $sliderId, $groupId ) {

		global $wpdb;
		$wpdb->update( $wpdb->prefix.LS_DB_TABLE,
			array( 'group_id' => null ),
			array( 'id' => $sliderId ),
			'%d',
			'%d'
		);

		self::checkForEmptyGroup( $groupId );

		return true;
	}


	/**
	 * Checks whether a slider group is empty and delete
	 * it if it is.
	 *
	 * @since 6.9.0
	 * @access public
	 * @param string $groupId The group database ID
	 * @return void
	 */
	public static function checkForEmptyGroup( $groupId ) {

		$sliders = self::_getGroupItems( $groupId, array(
			'exclude' => ''
		));


		if( empty( $sliders ) ) {
			self::removeGroup( $groupId );
		}
	}



	/**
	 * Adds group with the provided group name
	 *
	 * @since 6.9.0
	 * @access public
	 * @param string $name The group name
	 * @return int The group database ID inserted
	 */
	public static function addGroup( $name = 'Unnamed' ) {

		if( strlen( $name ) > 99) {
			$name = substr( $name, 0, ( 99 - strlen( $name ) ) );
		}

		global $wpdb;

		// Insert slider, WPDB will escape data automatically
		$wpdb->insert($wpdb->prefix.LS_DB_TABLE, array(
			'author' => get_current_user_id(),
			'name' => $name,
			'data' => '',
			'date_c' => time(),
			'date_m' => time(),
			'flag_popup' => 0,
			'flag_group' => 1
		), array(
			'%d', '%s', '%s', '%s', '%d', '%d', '%d', '%d'
		));

		return $wpdb->insert_id;
	}



	/**
	 * Removes the specified group
	 *
	 * @since 6.9.0
	 * @access public
	 * @param string $groupId The group database ID
	 * @return bool Returns true on success, false otherwise
	 */
	public static function removeGroup( $groupId = 0 ) {

		global $wpdb;

		// Attempt to remove any sliders from group
		// before deleting it.
		$wpdb->update(
			$wpdb->prefix.LS_DB_TABLE,
			array( 'group_id' => null ),
			array( 'group_id' => $groupId ),
			array( '%d' ),
			array( '%d' )
		);

		// Delete the group
		$wpdb->delete(
			$wpdb->prefix.LS_DB_TABLE,
			array( 'id' => $groupId ),
			array( '%d' )
		);

		return true;
	}



	/**
	 * Adds group with the provided group name
	 *
	 * @since 6.9.0
	 * @access public
	 * @param int $groupId The group ID
	 * @param string $name The group name
	 * @return bool Returns true on success, false otherwise
	 */
	public static function renameGroup( $groupId = 0, $name = 'Unnamed' ) {

		if( strlen( $name ) > 99) {
			$name = substr( $name, 0, ( 99 - strlen( $name ) ) );
		}

		global $wpdb;

		// Insert slider, WPDB will escape data automatically
		$wpdb->update(
			$wpdb->prefix.LS_DB_TABLE,
			array( 'name' => $name ),
			array( 'id' => $groupId ),
			array( '%s' ),
			array( '%d' )
		);

		// Return insert database ID
		return true;
	}



	private static function _getGroupItems( $id = null, $args = array() ) {

		// Check ID
		if( ! is_int( $id ) ) { return false; }

		// Defaults
		$defaults = array(
			'exclude' => array('removed'),
			'orderby' => 'date_c',
			'order' => 'DESC',
			'limit' => 100,
			'page' => 1,
			'data' => true
		);

		// Merge user data with defaults
		foreach( $defaults as $key => $val ) {
			if( ! isset( $args[ $key ] ) ) {
				$args[ $key ] = $val;
			}
		}

		$where = array();
		$where[] = "group_id = '$id'";

		if( ! empty( $args['exclude'] ) ) {

			if( in_array( 'hidden', $args['exclude'] ) ) {
				$where[] = "flag_hidden = '0'";
			}

			if( in_array( 'removed', $args['exclude'] ) ) {
				$where[] = "flag_deleted = '0'";
			}
		}

		$where = implode(' AND ', $where );

		$args['order'] 		= ($args['order'] === 'DESC') ? 'DESC' : 'ASC';
		$args['limit'] 		= (int)  $args['limit'];
		$args['page'] 		= (int)  $args['page'];
		$args['data'] 		= (bool) $args['data'];

		// DB stuff
		global $wpdb;
		$table = $wpdb->prefix.LS_DB_TABLE;

		// Make the call
		$result = $wpdb->get_results("
			SELECT *
			FROM $table
			WHERE $where
			ORDER BY `{$args['orderby']}` {$args['order']}
			LIMIT 100
		", ARRAY_A );

		// Decode slider data
		if(is_array($result) && !empty($result)) {

			if( ! empty( $args['data'] ) ) {
				foreach($result as $key => $slider) {
					$result[$key]['data'] = json_decode($slider['data'], true);
				}
			}

			return $result;

		// Failed query
		} else {
			return false;
		}
	}



	private static function _getById($id = null) {

		// Check ID
		if(!is_int($id)) { return false; }

		// Get Sliders
		global $wpdb;
		$table = $wpdb->prefix.LS_DB_TABLE;
		$result = $wpdb->get_row("SELECT * FROM $table WHERE id = '$id' ORDER BY id DESC LIMIT 1", ARRAY_A);

		// Check return value
		if(!is_array($result)) { return false; }

		// Return result
		$result['data'] = json_decode($result['data'], true);
		return $result;
	}



	private static function _getByIds($ids = null) {

		// Check ID
		if(!is_array($ids)) { return false; }

		// DB stuff
		global $wpdb;
		$table = $wpdb->prefix.LS_DB_TABLE;
		$limit = count($ids);

		// Collect IDs
		if(is_array($ids) && !empty($ids)) {
			$tmp = array();
			foreach($ids as $id) {
				$tmp[] = 'id = \''.intval($id).'\'';
			}
			$ids = implode(' OR ', $tmp);
			unset($tmp);
		}

		// Make the call
		$result = $wpdb->get_results("SELECT * FROM $table WHERE $ids ORDER BY id DESC LIMIT $limit", ARRAY_A);

		// Decode slider data
		if(is_array($result) && !empty($result)) {
			foreach($result as $key => $slider) {
				$result[$key]['data'] = json_decode($slider['data'], true);
			}

			return $result;

		// Failed query
		} else {
			return false;
		}
	}





	private static function _getBySlug($slug) {

		// Check slug
		if(empty($slug)) { return false; }
			else { $slug = esc_sql($slug); }

		// Get DB stuff
		global $wpdb;
		$table = $wpdb->prefix.LS_DB_TABLE;

		// Make the call
		$result = $wpdb->get_row("SELECT * FROM $table WHERE slug = '$slug' ORDER BY id DESC LIMIT 1", ARRAY_A);

		// Check return value
		if(!is_array($result)) { return false; }

		// Return result
		$result['data'] = json_decode($result['data'], true);
		return $result;
	}



	private static function _getRandom() {

		// Get DB stuff
		global $wpdb;
		$table = $wpdb->prefix.LS_DB_TABLE;

		// Make the call
		$result = $wpdb->get_row("SELECT * FROM $table WHERE flag_hidden = '0' AND flag_deleted = '0' ORDER BY RAND() LIMIT 1", ARRAY_A);

		// Check return value
		if(!is_array($result)) { return false; }

		// Return result
		$result['data'] = json_decode($result['data'], true);
		return $result;
	}
}
