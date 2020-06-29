<?php

namespace GroovyMenu;

use \GroovyMenuUtils as GroovyMenuUtils;
use \GroovyMenuPreset as GroovyMenuPreset;
use \GroovyMenuSingleMetaPreset as GroovyMenuSingleMetaPreset;
use \GroovyMenuCategoryPreset as GroovyMenuCategoryPreset;


defined( 'ABSPATH' ) || die( 'This script cannot be accessed directly.' );


/**
 * Class PreStorage
 */
class PreStorage {
	/**
	 * Self object instance
	 *
	 * @var null|object
	 */
	private static $instance = null;

	/**
	 * Disable storage
	 *
	 * @var array
	 */
	private static $disable_storage_flag = false;

	/**
	 * Storage of Groovy Menu (gm) compiled html
	 *
	 * @var array
	 */
	private $gm_storage = array();

	/**
	 * Storage key-value for preset
	 *
	 * @var array
	 */
	private $gm_storage_by_preset = array();

	/**
	 * Singleton self instance
	 *
	 * @return PreStorage
	 */
	public static function get_instance() {

		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		if ( defined( 'GROOVY_MENU_DISABLE_STYLE_STORAGE_CACHE' ) && GROOVY_MENU_DISABLE_STYLE_STORAGE_CACHE ) {
			self::$disable_storage_flag = true;
		}

		return self::$instance;
	}

	private function __clone() {
	}

	private function __construct() {
	}

	public function set_disable_storage() {
		self::$disable_storage_flag = true;
	}

	public function set_enable_storage() {
		self::$disable_storage_flag = false;
	}

	public function start_pre_storage() {

		if ( ! self::$disable_storage_flag ) {
			$this->collect_current_page_data_by_default();
		}

	}

	public function collect_current_page_data_by_default() {

		$post_type = GroovyMenuUtils::get_current_page_type();

		if ( ! empty( $post_type ) && $post_type ) {
			$def_val = GroovyMenuUtils::getTaxonomiesPresetByPostType( $post_type );
		}

		if ( ! isset( $args['gm_preset_id'] ) ) {
			if ( ! empty( $def_val['preset'] ) ) {
				$args['gm_preset_id'] = $def_val['preset'];
			}
			$current_preset_id = GroovyMenuSingleMetaPreset::get_preset_id_from_meta();
			if ( $current_preset_id ) {
				$args['gm_preset_id'] = $current_preset_id;
			}
		}

		if ( ! isset( $args['menu'] ) ) {
			if ( ! empty( $def_val['menu'] ) ) {
				$args['menu'] = $def_val['menu'];
			}
			$current_menu_id = GroovyMenuSingleMetaPreset::get_menu_id_from_meta();
			if ( $current_menu_id ) {
				$args['menu'] = $current_menu_id;
			}
		}

		if ( isset( $args['gm_preset_id'] ) && 'none' === $args['gm_preset_id'] ) {
			return null;
		}

		$defaults_args = array(
			'menu'           => GroovyMenuUtils::getMasterNavmenu(),
			'gm_preset_id'   => GroovyMenuUtils::getMasterPreset(),
			'theme_location' => GroovyMenuUtils::getMasterLocation(),
		);

		$args['menu'] =
			( empty( $args['menu'] ) || 'default' === $args['menu'] )
				?
				GroovyMenuUtils::getMasterNavmenu()
				:
				$args['menu'];

		$args['gm_preset_id'] =
			( empty( $args['gm_preset_id'] ) || 'default' === $args['gm_preset_id'] )
				?
				GroovyMenuUtils::getMasterPreset()
				:
				$args['gm_preset_id'];


		// Merge incoming params with defaults.
		$args = wp_parse_args( $args, $defaults_args );


		$nav_menu_obj = ! empty( $args['menu'] ) ? wp_get_nav_menu_object( $args['menu'] ) : null;

		if ( $args['menu'] && ! $nav_menu_obj ) {
			$args['menu'] = '';
		} elseif ( $args['menu'] && ! empty( $nav_menu_obj->term_id ) ) {
			$args['menu'] = $nav_menu_obj->term_id;
		}

		$category_options = gm_get_current_category_options();

		if ( $category_options && isset( $category_options['custom_options'] ) && '1' === $category_options['custom_options'] ) {
			if ( GroovyMenuCategoryPreset::getCurrentPreset() ) {
				$args['gm_preset_id'] = GroovyMenuCategoryPreset::getCurrentPreset();
			}
		}

		if ( defined( 'GROOVY_MENU_LVER' ) && '2' === GROOVY_MENU_LVER ) {
			// Get first preset id only.
			$presets_list = GroovyMenuPreset::getAll();
			if ( is_array( $presets_list ) ) {
				foreach ( $presets_list as $item ) {
					if ( isset( $item->id ) && $args['gm_preset_id'] = $item->id ) {
						break;
					}
				}
			}
		}

		$gm_id = $this->get_id( $args );

		if ( ! $this->check_is_stored_gm( $gm_id ) ) {

			$gm_html = groovy_menu(
				array(
					'gm_echo'        => false,
					'gm_pre_storage' => true,
					'theme_location' => 'gm_primary',
				)
			);

			$this->set_gm(
				$gm_id,
				array(
					'theme_location' => $args['theme_location'],
					'gm_preset_id'   => $args['gm_preset_id'],
					'menu'           => $args['menu'],
					'gm_html'        => $gm_html,
				)
			);

		}

	}


	/**
	 * Return id of gm by params
	 *
	 * @param array $args id of saved gm.
	 *
	 * @return string
	 */
	public function get_id( $args ) {

		$preset_id = isset( $args['gm_preset_id'] ) ? $args['gm_preset_id'] : 'default';
		$nav_menu  = isset( $args['menu'] ) ? $args['menu'] : 'default';

		$return_value = $preset_id . '::' . $nav_menu;

		return $return_value;
	}


	/**
	 * Return ids of gm by theme_loaction
	 *
	 * @param array $args params for search.
	 *
	 * @return array
	 */
	public function search_ids_by_location( $args ) {
		$return_value = array();

		if ( self::$disable_storage_flag ) {
			return $return_value;
		}

		$theme_location = isset( $args['theme_location'] ) ? $args['theme_location'] : 'gm_primary';

		if ( ! empty( $this->gm_storage ) ) {
			foreach ( $this->gm_storage as $index => $item ) {
				if ( $theme_location === $item['theme_location'] ) {
					$return_value[] = $index;
				}
			}
		}

		return $return_value;
	}

	/**
	 * Return if exist and compiled array with HTML GM Block
	 *
	 * @param string $id id of saved gm.
	 *
	 * @return array|null
	 */
	public function get_gm( $id ) {
		$return_value = null;

		if ( self::$disable_storage_flag ) {
			return $return_value;
		}

		if ( ! empty( $this->gm_storage ) ) {
			if ( ! empty( $this->gm_storage[ $id ] ) ) {
				$return_value = $this->gm_storage[ $id ];
			}
		}

		return $return_value;
	}

	/**
	 * Return if exist and compiled array with HTML GM Block
	 *
	 * @param string $id id of saved gm.
	 *
	 * @return bool
	 */
	public function check_is_stored_gm( $id ) {
		$return_value = false;

		if ( self::$disable_storage_flag ) {
			return $return_value;
		}

		if ( ! empty( $this->gm_storage ) ) {
			if ( ! empty( $this->gm_storage[ $id ] ) ) {
				$return_value = true;
			}
		}

		return $return_value;
	}

	/**
	 * Store compiled array with HTML GM Block
	 *
	 * @param string $id      id of gm.
	 * @param array  $gm_data array with HTML gm block.
	 */
	public function set_gm( $id, $gm_data ) {
		if ( empty( $gm_data['theme_location'] ) ) {
			$gm_data['theme_location'] = '-';
		}

		if ( ! self::$disable_storage_flag ) {
			$this->gm_storage[ $id ] = $gm_data;
		}

	}


	/**
	 * Add param to GM block by its preset id
	 *
	 * @param int|string $preset_id params for search.
	 * @param string     $data_name key param.
	 * @param mixed      $data      data param.
	 *
	 * @return null
	 */
	public function set_preset_data( $preset_id, $data_name, $data ) {
		if ( empty( $preset_id ) || empty( $data_name ) ) {
			return;
		}

		switch ( $data_name ) {
			case 'font_family':
				$key = md5( $data );
				$this->gm_storage_by_preset[ $preset_id ][ $data_name ][ $key ] = $data;
				break;

			default:
				$this->gm_storage_by_preset[ $preset_id ][ $data_name ] = $data;
				break;
		}
	}

	public function get_preset_data_by_key( $data_name ) {
		$data = array();

		if ( ! empty( $this->gm_storage_by_preset ) ) {
			foreach ( $this->gm_storage_by_preset as $index => $item ) {
				if ( isset( $item[ $data_name ] ) ) {
					$data[ $index ] = $item[ $data_name ];
				}
			}
		}

		return $data;
	}

	public function get_stored_gm_list() {
		$return_value = array();

		if ( ! empty( $this->gm_storage ) ) {
			foreach ( $this->gm_storage as $index => $item ) {
				$return_value[] = $index;
			}
		}

		return $return_value;
	}

	public function remove_all_gm() {
		$this->gm_storage = array();
	}

	public function get_all_gm() {
		return $this->gm_storage;
	}


}
