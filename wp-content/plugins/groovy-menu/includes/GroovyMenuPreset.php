<?php defined( 'ABSPATH' ) || die( 'This script cannot be accessed directly.' );

if ( ! class_exists( 'GroovyMenuPreset' ) ) {

	/**
	 * Class GroovyMenuPreset
	 */
	class GroovyMenuPreset {
		protected $id;
		protected $name;

		const TABLE = 'groovy_preset';
		const DEFAULT_PRESET_OPTION = 'groovy_menu_default_preset';


		/**
		 * GroovyMenuPreset constructor.
		 *
		 * @param null $id      id.
		 * @param bool $install is install table.
		 */
		public function __construct( $id = null, $install = false ) {

			if ( $install ) {
				self::install();

				return;
			}

			$preset = $this->getById( $id );

			$this->id = $id;
			if ( isset( $preset->name ) ) {
				$this->name = $preset->name;
			}
		}

		public static function install() {

			if ( ! get_option( self::DEFAULT_PRESET_OPTION ) ) {
				self::initialize();
			}

		}


		public static function initialize() {

			global $wpdb;

			$table_name = $wpdb->prefix . self::TABLE;

			if ( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) !== $table_name ) {

				$charset_collate = "DEFAULT CHARACTER SET {$wpdb->charset} COLLATE {$wpdb->collate}";

				$sql = "CREATE TABLE {$table_name} ( id mediumint(9) NOT NULL AUTO_INCREMENT, name text NOT NULL, UNIQUE KEY id (id) ) {$charset_collate};";

				require_once ABSPATH . 'wp-admin/includes/upgrade.php';
				dbDelta( $sql );

				$preset = self::create( 'first preset' );
				update_option( self::DEFAULT_PRESET_OPTION, $preset, true );

			}


			wp_mkdir_p( GroovyMenuUtils::getFontsDir() );

		}


		/**
		 * @return GroovyMenuPreset
		 */
		public static function getCurrentPreset() {
			$id = self::getDefaultPreset();
			if ( empty( $id ) ) {
				$id = self::getFirstDbPreset();
			}

			return new self( $id );
		}


		/**
		 * @return null
		 */
		public static function getFirstDbPreset() {

			$all_presets = self::getAll();

			if ( is_array( $all_presets ) && ! empty( $all_presets ) ) {
				$preset = current( $all_presets );
				if ( isset( $preset->id ) ) {
					return $preset->id;
				}
			}

			return null;
		}


		/**
		 * @param bool $get_first_from_db first db record.
		 *
		 * @return mixed|null
		 */
		public static function getDefaultPreset( $get_first_from_db = false ) {

			$return = null;

			if ( $get_first_from_db ) {
				$return = self::getFirstDbPreset();
			} else {
				$return = get_option( self::DEFAULT_PRESET_OPTION );
				if ( empty( $return ) ) {
					$return = self::getFirstDbPreset();
				}
			}

			return $return;
		}


		/**
		 * @param $id
		 *
		 * @return bool
		 */
		public static function setDefaultPreset( $id ) {

			$return_val = false;

			$global = get_option( GroovyMenuStyle::OPTION_NAME );

			if ( isset( $global['taxonomies'] ) && isset( $global['taxonomies']['default_master_preset'] ) ) {
				$global['taxonomies']['default_master_preset'] = $id;

				$styles = new GroovyMenuStyle( null );

				$styles->updateGlobal( $global );

				$return_val = true;

			} else {
				$return_val = update_option( self::DEFAULT_PRESET_OPTION, $id );
			}

			return $return_val;
		}


		/**
		 * @return null
		 */
		public function getId() {
			return $this->id;
		}


		/**
		 * @param $name
		 *
		 * @return bool
		 */
		protected static function checkName( $name ) {
			global $wpdb;
			$table        = esc_sql( $wpdb->prefix . self::TABLE );
			$name_escaped = esc_sql( $name );
			$row          = $wpdb->get_row(
				"SELECT count(1) AS cnt FROM {$table} WHERE name = \"{$name_escaped}\";"
			);

			return empty( $row->cnt );
		}


		/**
		 * @param      $name
		 * @param bool $force
		 * @param null $preset_id
		 *
		 * @return int
		 */
		public static function create( $name, $force = false, $preset_id = null ) {
			global $wpdb;

			$fields = [
				'name' => $name
			];
			if ( ! is_null( $preset_id ) ) {
				$fields['id'] = $preset_id;
			}
			if ( $force ) {

				$wpdb->insert( $wpdb->prefix . self::TABLE, $fields );

			} elseif ( self::checkName( $name ) ) {

				$wpdb->insert( $wpdb->prefix . self::TABLE, $fields );

			} else {

				for ( $i = 1; $i < 100; $i ++ ) {
					$newName = $name . ' #' . $i;
					if ( self::checkName( $newName ) ) {
						$wpdb->insert( $wpdb->prefix . self::TABLE, $fields );
						$i = 101;
					}
				}

			}

			return $wpdb->insert_id;

		}


		/**
		 * @param $id
		 * @param $name
		 */
		public static function rename( $id, $name ) {
			global $wpdb;
			$wpdb->update( $wpdb->prefix . self::TABLE, array( 'name' => $name ), array( 'id' => $id ) );
		}


		/**
		 * @param $id
		 *
		 * @return array|null|object|void
		 */
		public static function getById( $id ) {
			if ( ! $id || 'default' === $id ) {
				$id = self::getDefaultPreset();
			}

			if ( ! $id ) {
				$id = self::getFirstDbPreset();
			}

			if ( ! $id ) {
				return null;
			}

			global $wpdb;
			$result = $wpdb->get_row( 'select * from ' . $wpdb->prefix . self::TABLE . ' where id = ' . esc_sql( $id ) );

			return $result;
		}


		/**
		 * @param $id
		 *
		 * @return array|null|object|void
		 */
		public static function deleteById( $id ) {
			if ( ! $id ) {
				return null;
			}

			global $wpdb;

			return $wpdb->get_row( 'delete from ' . $wpdb->prefix . self::TABLE . ' where id = ' . esc_sql( $id ) );
		}


		/**
		 * @param bool $key_lalue
		 *
		 * @return array|null|object
		 */
		public static function getAll( $key_lalue = false ) {
			global $wpdb;

			$presets       = array();
			$raw_base_data = $wpdb->get_results( 'select * from ' . $wpdb->prefix . self::TABLE );

			if ( empty( $raw_base_data ) ) {
				$raw_base_data = array();
			}

			if ( $key_lalue ) {
				foreach ( $raw_base_data as $preset ) {
					$presets[ strval( $preset->id ) ] = $preset->name;
				}
			} else {
				$presets = $raw_base_data;
			}

			return $presets;
		}


		/**
		 * @return mixed
		 */
		public function getName() {
			return $this->name;
		}


		/**
		 * @param $id
		 * @param $img
		 */
		public static function setPreviewById( $id, $img ) {
			update_option( GroovyMenuStyle::OPTION_NAME . '_preview_' . $id, $img, false );
		}


		/**
		 * @param $id
		 *
		 * @return bool
		 */
		public static function isPreviewThumb( $id ) {
			return ( self::getThumb( $id ) != false );
		}


		/**
		 * @param $id
		 *
		 * @return bool|mixed|void
		 */
		public static function getPreviewById( $id ) {
			$preview = GroovyMenuPreset::getThumb( $id );
			if ( ! $preview ) {
				$preview = get_option( GroovyMenuStyle::OPTION_NAME . '_preview_' . $id );

				if ( strlen( $preview ) > 1024 ) {
					delete_option( GroovyMenuStyle::OPTION_NAME . '_preview_' . $id );
				} elseif (
					'api.groovy.grooni.com' !== parse_url( $preview, PHP_URL_HOST )
					&&
					$_SERVER['SERVER_NAME'] !== parse_url( $preview, PHP_URL_HOST )
				) {
					delete_option( GroovyMenuStyle::OPTION_NAME . '_preview_' . $id );
				}

			}
			if ( ! $preview ) {
				$preview = get_option( GroovyMenuStyle::OPTION_NAME . '_screenshot_' . $id );
			}

			return $preview;

		}


		/**
		 * @param $id
		 * @param $img
		 */
		public static function setThumb( $id, $img ) {
			update_option( GroovyMenuStyle::OPTION_NAME . '_thumb_' . $id, $img, false );
		}


		/**
		 * @param $id
		 *
		 * @return bool
		 */
		public static function getThumb( $id ) {
			$thumbId = get_option( GroovyMenuStyle::OPTION_NAME . '_thumb_' . $id );
			if ( ! $thumbId ) {
				return false;
			}
			$src = wp_get_attachment_image_src( $thumbId, 'full' );

			return $src[0];
		}


	}

}
