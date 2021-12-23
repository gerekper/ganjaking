<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Manage role.
 * @since 4.8
 *
 * Class Vc_Roles
 */
class Vc_Roles {
	protected $post_types = false;
	protected $vc_excluded_post_types = false;
	protected $parts = array(
		'post_types',
		'backend_editor',
		'frontend_editor',
		'unfiltered_html',
		'post_settings',
		'settings',
		'templates',
		'shortcodes',
		'grid_builder',
		'presets',
		'dragndrop',
	);

	protected static $parts_cache = null;

	/**
	 * Get list of parts
	 * @return mixed
	 */
	public function getParts() {
		if ( is_null( self::$parts_cache ) ) {
			self::$parts_cache = apply_filters( 'vc_roles_parts_list', $this->parts );
		}

		return self::$parts_cache;
	}

	/**
	 * Check required capability for this role to have user access.
	 *
	 * @param $part
	 *
	 * @return array|string
	 */
	public function getPartCapability( $part ) {
		return 'settings' !== $part ? array(
			'edit_posts',
			'edit_pages',
		) : 'manage_options';
	}

	/**
	 * @param $role
	 * @param $caps
	 * @return bool
	 */
	public function hasRoleCapability( $role, $caps ) {
		$has = false;
		$wp_role = get_role( $role );
		if ( is_string( $caps ) ) {
			$has = $wp_role->has_cap( $caps );
		} elseif ( is_array( $caps ) ) {
			$i = 0;
			$count = count( $caps );
			while ( false === $has && $i < $count ) {
				$has = $this->hasRoleCapability( $role, $caps[ $i ++ ] );
			}
		}

		return $has;
	}

	/**
	 * @return \WP_Roles
	 */
	public function getWpRoles() {
		global $wp_roles;
		if ( function_exists( 'wp_roles' ) ) {
			return $wp_roles;
		} else {
			if ( ! isset( $wp_roles ) ) {
				// @codingStandardsIgnoreLine
				$wp_roles = new WP_Roles();
			}
		}

		return $wp_roles;
	}

	/**
	 * @param array $params
	 * @return array
	 * @throws \Exception
	 */
	public function save( $params = array() ) {
		$data = array( 'message' => '' );
		$roles = $this->getWpRoles();
		$editable_roles = get_editable_roles();
		foreach ( $params as $role => $parts ) {
			if ( is_string( $parts ) ) {
				$parts = json_decode( stripslashes( $parts ), true );
			}
			if ( isset( $editable_roles[ $role ] ) ) {
				foreach ( $parts as $part => $settings ) {
					$part_key = vc_role_access()->who( $role )->part( $part )->getStateKey();
					$stateValue = '0';
					$roles->use_db = false; // Disable saving in DB on every cap change
					foreach ( $settings as $key => $value ) {
						if ( '_state' === $key ) {
							$stateValue = in_array( $value, array(
								'0',
								'1',
							), true ) ? (bool) $value : $value;
						} else {
							if ( empty( $value ) ) {
								$roles->remove_cap( $role, $part_key . '/' . $key );
							} else {
								$roles->add_cap( $role, $part_key . '/' . $key, true );
							}
						}
					}
					$roles->use_db = true; // Enable for the lat change in cap of role to store data in DB
					$roles->add_cap( $role, $part_key, $stateValue );
				}
			}
		}
		$data['message'] = esc_html__( 'Roles settings successfully saved.', 'js_composer' );

		return $data;
	}

	/**
	 * @return array|bool
	 */
	public function getPostTypes() {
		if ( false === $this->post_types ) {
			$this->post_types = array();
			$exclude = $this->getExcludePostTypes();
			foreach ( get_post_types( array( 'public' => true ) ) as $post_type ) {
				if ( ! in_array( $post_type, $exclude, true ) ) {
					$this->post_types[] = array(
						$post_type,
						$post_type,
					);
				}
			}
		}

		return $this->post_types;
	}

	/**
	 * @return bool|mixed|void
	 */
	public function getExcludePostTypes() {
		if ( false === $this->vc_excluded_post_types ) {
			$this->vc_excluded_post_types = apply_filters( 'vc_settings_exclude_post_type', array(
				'attachment',
				'revision',
				'nav_menu_item',
				'mediapage',
			) );
		}

		return $this->vc_excluded_post_types;
	}
}
