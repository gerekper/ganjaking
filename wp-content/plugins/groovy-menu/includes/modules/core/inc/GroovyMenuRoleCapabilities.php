<?php defined( 'ABSPATH' ) || die( 'This script cannot be accessed directly.' );

if ( ! class_exists( 'GroovyMenuRoleCapabilities' ) ) {

	/**
	 * Class GroovyMenuRoleCapabilities
	 */
	class GroovyMenuRoleCapabilities {

		/**
		 * Create capabilities is needed.
		 */
		public static function check_capabilities() {

			if ( get_option( 'groovy_menu_added_capabilities' ) ) {
				return;
			}

			self::add_capabilities();
		}

		/**
		 * Create capabilities.
		 */
		public static function add_capabilities() {
			global $wp_roles;

			if ( ! class_exists( 'WP_Roles' ) ) {
				return;
			}

			if ( ! isset( $wp_roles ) ) {
				$wp_roles = new WP_Roles(); // @codingStandardsIgnoreLine
			}

			$capabilities = self::get_capabilities();

			foreach ( $capabilities as $cap_group ) {
				foreach ( $cap_group as $cap ) {
					$wp_roles->add_cap( 'administrator', $cap );
				}
			}

			update_option( 'groovy_menu_added_capabilities', GROOVY_MENU_VERSION, true );
		}


		/**
		 * Get capabilities
		 *
		 * @return array
		 */
		private static function get_capabilities() {
			$capabilities = array();

			$capabilities['core'] = array(
				'groovy_menu_manage_global_options',
				'groovy_menu_can_import',
				'groovy_menu_can_export',
			);

			$cap_types = array(
				'groovy_menu_preset' => 'preset',
				'gm_menu_block'      => 'block',
			);

			foreach ( $cap_types as $cap_type => $cap_label ) {
				$capabilities[ $cap_type ] = array(
					'edit_post'           => 'groovy_menu_edit_' . $cap_label,
					'read_post'           => 'groovy_menu_read_' . $cap_label,
					'delete_post'         => 'groovy_menu_delete_' . $cap_label,
					'delete_posts'        => 'groovy_menu_delete_' . $cap_label . 's',
					'edit_others_posts'   => 'groovy_menu_edit_others_' . $cap_label . 's',
					'edit_posts'          => 'groovy_menu_edit_' . $cap_label . 's',
					'publish_posts'       => 'groovy_menu_publish_' . $cap_label . 's',
					'read_private_posts'  => 'groovy_menu_read_private_' . $cap_label . 's',
					'create_posts'        => 'groovy_menu_create_' . $cap_label,
					'delete_others_posts' => 'groovy_menu_delete_others_' . $cap_label . 's',
				);
			}

			return $capabilities;
		}


		/**
		 * Check if administrator
		 *
		 * @return string|null
		 */
		public static function administrator() {
			if ( current_user_can( 'administrator' ) ) {
				return 'manage_options';
			}

			return null;
		}

		/**
		 * Check convert var to boolean
		 *
		 * @param bool        $need_bool return boolean var instead string.
		 * @param null|string $cap       var to convert.
		 *
		 * @return string|bool
		 */
		public static function boolConvert( $need_bool, $cap ) {
			if ( $need_bool ) {
				$cap = empty( $cap ) ? false : true;
			} else {
				$cap = empty( $cap ) ? 'manage_options' : $cap;
			}

			return $cap;
		}

		/**
		 * Check if current user can manage global options
		 *
		 * @param bool $need_bool return boolean var instead string.
		 *
		 * @return string
		 */
		public static function globalOptions( $need_bool = false ) {
			$cap = self::administrator();

			if ( current_user_can( 'groovy_menu_manage_global_options' ) ) {
				$cap = 'groovy_menu_manage_global_options';
			}

			return self::boolConvert( $need_bool, $cap );
		}

		/**
		 * Check if current user can import presets
		 *
		 * @param bool $need_bool return boolean var instead string.
		 *
		 * @return string
		 */
		public static function canImport( $need_bool = false ) {
			$cap = self::administrator();

			if ( current_user_can( 'groovy_menu_can_import' ) ) {
				$cap = 'groovy_menu_can_import';
			}

			return self::boolConvert( $need_bool, $cap );
		}

		/**
		 * Check if current user can import presets
		 *
		 * @param bool $need_bool return boolean var instead string.
		 *
		 * @return string
		 */
		public static function canExport( $need_bool = false ) {
			$cap = self::administrator();

			if ( current_user_can( 'groovy_menu_can_export' ) ) {
				$cap = 'groovy_menu_can_export';
			}

			return self::boolConvert( $need_bool, $cap );
		}

		/**
		 * Check if current user can READ (view) groovy_menu_preset
		 *
		 * @param bool $need_bool return boolean var instead string.
		 *
		 * @return string
		 */
		public static function presetRead( $need_bool = false ) {
			$cap = self::administrator();

			if ( current_user_can( 'groovy_menu_read_preset' ) ) {
				$cap = 'groovy_menu_read_preset';
			} elseif ( current_user_can( 'groovy_menu_read_private_presets' ) ) {
				$cap = 'groovy_menu_read_private_presets';
			} elseif ( self::presetEdit( true ) ) {
				$cap = self::presetEdit( $need_bool );
			} elseif ( self::presetCreate( true ) ) {
				$cap = self::presetCreate( $need_bool );
			}

			return self::boolConvert( $need_bool, $cap );
		}

		/**
		 * Check if current user can EDIT groovy_menu_preset
		 *
		 * @param bool $need_bool return boolean var instead string.
		 *
		 * @return string
		 */
		public static function presetEdit( $need_bool = false ) {
			$cap = self::administrator();

			if ( current_user_can( 'groovy_menu_edit_preset' ) ) {
				$cap = 'groovy_menu_edit_preset';
			} elseif ( current_user_can( 'groovy_menu_edit_presets' ) ) {
				$cap = 'groovy_menu_edit_presets';
			} elseif ( current_user_can( 'groovy_menu_publish_presets' ) ) {
				$cap = 'groovy_menu_publish_presets';
			} elseif ( current_user_can( 'groovy_menu_edit_others_presets' ) ) {
				$cap = 'groovy_menu_edit_others_presets';
			}

			return self::boolConvert( $need_bool, $cap );
		}

		/**
		 * Check if current user can CREATE groovy_menu_preset
		 *
		 * @param bool $need_bool return boolean var instead string.
		 *
		 * @return string|boolean
		 */
		public static function presetCreate( $need_bool = false ) {
			$cap = self::administrator();

			if ( current_user_can( 'groovy_menu_create_preset' ) ) {
				$cap = 'groovy_menu_create_preset';
			}

			return self::boolConvert( $need_bool, $cap );
		}

		/**
		 * Check if current user can DELETE groovy_menu_preset
		 *
		 * @param bool $need_bool return boolean var instead string.
		 *
		 * @return string|boolean
		 */
		public static function presetDelete( $need_bool = false ) {
			$cap = self::administrator();

			if ( current_user_can( 'groovy_menu_delete_preset' ) ) {
				$cap = 'groovy_menu_delete_preset';
			} elseif ( current_user_can( 'groovy_menu_delete_presets' ) ) {
				$cap = 'groovy_menu_delete_presets';
			} elseif ( current_user_can( 'groovy_menu_delete_others_presets' ) ) {
				$cap = 'groovy_menu_delete_others_presets';
			}

			return self::boolConvert( $need_bool, $cap );
		}

		/**
		 * Check if current user can READ (view) gm_menu_block
		 *
		 * @param bool $need_bool return boolean var instead string.
		 *
		 * @return string
		 */
		public static function blockRead( $need_bool = false ) {
			$cap = self::administrator();

			if ( current_user_can( 'groovy_menu_read_block' ) ) {
				$cap = 'groovy_menu_read_block';
			} elseif ( self::blockEdit( true ) ) {
				$cap = self::blockEdit( $need_bool );
			}

			return self::boolConvert( $need_bool, $cap );
		}

		/**
		 * Check if current user can EDIT gm_menu_block
		 *
		 * @param bool $need_bool return boolean var instead string.
		 *
		 * @return string
		 */
		public static function blockEdit( $need_bool = false ) {
			$cap = self::administrator();

			if ( current_user_can( 'groovy_menu_edit_block' ) ) {
				$cap = 'groovy_menu_edit_block';
			} elseif ( current_user_can( 'groovy_menu_edit_blocks' ) ) {
				$cap = 'groovy_menu_edit_blocks';
			} elseif ( current_user_can( 'groovy_menu_edit_others_blocks' ) ) {
				$cap = 'groovy_menu_edit_others_blocks';
			}

			return self::boolConvert( $need_bool, $cap );
		}

		/**
		 * Check if current user can DELETE gm_menu_block
		 *
		 * @param bool $need_bool return boolean var instead string.
		 *
		 * @return string|boolean
		 */
		public static function blockDelete( $need_bool = false ) {
			$cap = self::administrator();

			if ( current_user_can( 'groovy_menu_delete_block' ) ) {
				$cap = 'groovy_menu_delete_block';
			} elseif ( current_user_can( 'groovy_menu_delete_blocks' ) ) {
				$cap = 'groovy_menu_delete_blocks';
			} elseif ( current_user_can( 'groovy_menu_edit_others_blocks' ) ) {
				$cap = 'groovy_menu_edit_others_blocks';
			}

			return self::boolConvert( $need_bool, $cap );
		}


	}

}
