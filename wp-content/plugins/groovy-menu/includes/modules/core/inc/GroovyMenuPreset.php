<?php defined( 'ABSPATH' ) || die( 'This script cannot be accessed directly.' );

if ( ! class_exists( 'GroovyMenuPreset' ) ) {

	/**
	 * Class GroovyMenuPreset
	 *
	 * @since 1.5
	 */
	class GroovyMenuPreset {
		protected $id;
		protected $name;
		protected $lver = false;

		const TABLE                 = 'groovy_preset';
		const DEFAULT_PRESET_OPTION = 'groovy_menu_default_preset';


		/**
		 * GroovyMenuPreset constructor.
		 *
		 * @param mixed $id      id.
		 * @param bool  $install is install table.
		 */
		public function __construct( $id = null, $install = false ) {

			if ( $install ) {
				self::install();

				return;
			}

			$preset = $this->getById( $id );

			if ( empty( $id ) && ! empty( $preset->id ) ) {
				$this->id = $preset->id;
			} else {
				$this->id = $id;
			}

			if ( isset( $preset->name ) ) {
				$this->name = $preset->name;
			}

			if ( defined( 'GROOVY_MENU_LVER' ) && '2' === GROOVY_MENU_LVER ) {
				$this->lver = true;
			}
		}

		public static function install() {

			if ( ! get_option( self::DEFAULT_PRESET_OPTION ) ) {
				self::initialize();
			}

		}


		public static function initialize() {

			$posts = get_posts(
				array(
					'post_type'      => 'groovy_menu_preset',
					'numberposts'    => 1,
					'posts_per_page' => 1,
					'fields'         => 'ids',
				)
			);

			// if no one groovy_menu_preset.
			if ( empty( $posts ) ) {

				// create first.
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
		 * @param      $name
		 * @param bool $force
		 * @param null $preset_id
		 *
		 * @return int
		 */
		public static function create( $name, $force = false, $preset_id = null ) {

			$name = self::sanityStringUTF( $name, 'UTF-8' );

			$new_post_args = array(
				'post_author'  => get_current_user_id(),
				'post_content' => '',
				'post_excerpt' => '',
				'post_name'    => $name,
				'post_status'  => 'publish',
				'post_title'   => $name,
				'post_type'    => 'groovy_menu_preset',
				'post_date'    => date( 'Y-m-d H:i:s', intval( current_time( 'timestamp' ) ) ),
			);

			// Inset post.
			$new_post_id = wp_insert_post( $new_post_args );

			if ( ! empty( $preset_id ) && $new_post_id ) {
				update_post_meta( $new_post_id, 'gm_old_id', strval( $preset_id ) );
			}

			return $new_post_id;
		}


		/**
		 * @param $post_id
		 * @param $new_title
		 */
		public static function rename( $post_id, $new_title ) {

			// if new_title isn't defined, return.
			if ( empty( $new_title ) ) {
				return;
			}

			// ensure title case of $new_title.
			$new_title = self::sanityStringUTF( $new_title, 'UTF-8' );

			// if $new_title is defined, but it matches the current title, return.
			if ( $post_id === $new_title ) {
				return;
			}

			// place the current post and $new_title into array.
			$post_update = array(
				'ID'         => $post_id,
				'post_title' => $new_title,
			);

			wp_update_post( $post_update );
		}

		/**
		 * @param $string
		 * @param $encoding
		 *
		 * @return string
		 */
		public static function sanityStringUTF( $string, $encoding = 'UTF-8' ) {

			if ( function_exists( 'mb_convert_case' ) ) {

				// ensure title case of $new_title.
				$string = mb_convert_case( $string, MB_CASE_TITLE, $encoding );

			} elseif ( function_exists( 'ucfirst' ) ) {

				$string = ucfirst( $string );

			}

			return $string;
		}

		/**
		 * @param $post_id
		 *
		 * @return array|null|object
		 */
		public static function getById( $post_id ) {
			if ( ! $post_id || 'default' === $post_id ) {
				$post_id = self::getDefaultPreset();
			}

			if ( ! $post_id ) {
				$post_id = self::getFirstDbPreset();
			}

			if ( ! $post_id ) {
				return null;
			}

			$result  = null;
			$presets = self::getAll();

			foreach ( $presets as $preset ) {
				if ( isset( $preset->id ) && intval( $preset->id ) === intval( $post_id ) ) {
					$result = $preset;
				}
			}

			return $result;
		}


		/**
		 * @param      $post_id
		 * @param bool $force
		 *
		 * @return array|null|bool
		 */
		public static function deleteById( $post_id, $force_delete = false ) {

			$post_id = empty( $post_id ) ? null : intval( $post_id );

			if ( empty( $post_id ) ) {
				return null;
			}

			$used_in = GroovyMenuUtils::get_preset_used_in_by_id( $post_id );

			if ( ! empty( $used_in ) && ! $force_delete ) {
				return $used_in;
			}

			// Delete thumb image.
			global $wp_filesystem;
			if ( empty( $wp_filesystem ) ) {
				if ( file_exists( ABSPATH . '/wp-admin/includes/file.php' ) ) {
					require_once ABSPATH . '/wp-admin/includes/file.php';
					WP_Filesystem();
				}
			}
			if ( empty( $wp_filesystem ) ) {
				delete_post_meta( intval( $post_id ), 'gm_preset_screenshot' );
			} else {
				$upload_dir      = GroovyMenuUtils::getUploadDir();
				$upload_filename = 'preset_' . $post_id . '.png';
				$file_path       = $upload_dir . $upload_filename;

				if ( is_file( $file_path ) ) {
					$wp_filesystem->delete( $file_path, false, true );
					delete_post_meta( intval( $post_id ), 'gm_preset_screenshot' );
				}

				$upload_filename = 'preset_' . $post_id . '.css';
				$file_path       = $upload_dir . $upload_filename;

				if ( is_file( $file_path ) ) {
					$wp_filesystem->delete( $file_path, false, true );
				}

				$upload_filename = 'preset_' . $post_id . '_rtl.css';
				$file_path       = $upload_dir . $upload_filename;

				if ( is_file( $file_path ) ) {
					$wp_filesystem->delete( $file_path, false, true );
				}
			}

			// delete post.
			return wp_delete_post( $post_id, $force_delete );
		}


		/**
		 * @param bool $key_value if true return simple array key value.
		 *
		 * @return array|null|object
		 */
		public static function getAll( $key_value = false, $disable_cache = false ) {

			static $cache_enable = true;
			static $cache        = array(
				'obj'       => array(),
				'key_value' => array(),
			);

			if ( $disable_cache ) {
				$cache_enable = false;

				return null;
			}

			if ( $cache_enable && $key_value && ! empty( $cache['key_value'] ) ) {
				return $cache['key_value'];
			} elseif ( $cache_enable && ! $key_value && ! empty( $cache['obj'] ) ) {
				return $cache['obj'];
			}
			$lver = false;
			if ( defined( 'GROOVY_MENU_LVER' ) && '2' === GROOVY_MENU_LVER ) {
				$lver = true;
			}

			// get posts.
			$args          = array(
				'fields' => array( 'ID', 'post_title' ),
				'order'  => 'ASC',
			);
			$raw_base_data = GroovyMenuUtils::get_posts_fields( $args );

			if ( empty( $raw_base_data ) ) {
				$raw_base_data = array();
			}

			// load cache with data.
			foreach ( $raw_base_data as $preset ) {
				// as key_value.
				$cache['key_value'][ strval( $preset->ID ) ] = $preset->post_title;

				// as object.
				$preset_obj       = new stdClass();
				$preset_obj->id   = strval( $preset->ID );
				$preset_obj->name = $preset->post_title;
				$cache['obj'][]   = $preset_obj;

				if ( $lver ) {
					break;
				}
			}

			$presets = array();

			if ( $key_value ) {
				$presets = $cache['key_value'];
			} else {
				$presets = $cache['obj'];
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
		 * @param $post_id
		 * @param $img
		 */
		public static function setPreviewById( $post_id, $img ) {
			update_post_meta( $post_id, 'gm_preset_preview', $img );
		}


		/**
		 * @param $post_id
		 *
		 * @return bool
		 */
		public static function isPreviewThumb( $post_id ) {
			return ( self::getThumb( $post_id ) != false );
		}


		/**
		 * @param $post_id
		 *
		 * @return bool|mixed
		 */
		public static function getPreviewById( $post_id ) {

			$preview = GroovyMenuPreset::getThumb( $post_id );

			if ( ! $preview ) {

				$preview = get_post_meta( $post_id, 'gm_preset_preview', true );

				if ( strlen( $preview ) > 1024 ) {
					update_post_meta( $post_id, 'gm_preset_preview', '' );
				} elseif (
					'api.groovy.grooni.com' !== wp_parse_url( $preview, PHP_URL_HOST )
					&&
					$_SERVER['SERVER_NAME'] !== wp_parse_url( $preview, PHP_URL_HOST )
				) {
					update_post_meta( $post_id, 'gm_preset_preview', '' );
				}

			}

			if ( ! $preview ) {
				$preview = get_post_meta( $post_id, 'gm_preset_screenshot', true );
			}

			return $preview;

		}


		/**
		 * @param $post_id
		 * @param $img
		 */
		public static function setThumb( $post_id, $img ) {
			update_post_meta( $post_id, 'gm_preset_thumb', $img );
		}

		/**
		 * @param $post_id
		 *
		 * @return bool
		 */
		public static function getThumb( $post_id ) {
			$thumbId = intval( get_post_meta( $post_id, 'gm_preset_thumb', true ) );
			if ( ! $thumbId ) {
				return false;
			}
			$src = wp_get_attachment_image_src( $thumbId, 'full' );

			return $src[0];
		}


	}

}
