<?php
/**
 * Class YITH_WCBK_Wpml_Integration
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking
 */

defined( 'YITH_WCBK' ) || exit;

/**
 * Class YITH_WCBK_Wpml_Integration
 *
 * @since   1.0.3
 */
class YITH_WCBK_Wpml_Integration extends YITH_WCBK_Integration {
	use YITH_WCBK_Singleton_Trait;

	/**
	 * SitePress
	 *
	 * @var SitePress
	 */
	public $sitepress;

	/**
	 * Current language.
	 *
	 * @var string
	 */
	public $current_language;

	/**
	 * Default language.
	 *
	 * @var string
	 */
	public $default_language;

	/**
	 * WPML Services integration class.
	 *
	 * @var YITH_WCBK_Wpml_Services
	 */
	public $services;

	/**
	 * Init
	 */
	protected function init() {
		if ( $this->is_enabled() ) {
			$this->init_wpml_vars();
			$this->load_classes();
		}
	}


	/**
	 * Init WPML vars
	 */
	protected function init_wpml_vars() {
		if ( $this->is_enabled() ) {
			global $sitepress;
			$this->sitepress        = $sitepress;
			$this->current_language = $this->sitepress->get_current_language();
			$this->default_language = $this->sitepress->get_default_language();
		}
	}

	/**
	 * Get the class name from slug
	 *
	 * @param string $slug Class slug.
	 *
	 * @return string
	 */
	public function get_class_name_from_slug( $slug ) {
		$class_slug = str_replace( '-', ' ', $slug );
		$class_slug = ucwords( $class_slug );
		$class_slug = str_replace( ' ', '_', $class_slug );

		return 'YITH_WCBK_WPML_' . $class_slug;
	}

	/**
	 * Load classes.
	 */
	protected function load_classes() {
		$utils = array(
			'booking',
			'booking-product',
			'services',
			'person-types',
			'extra-costs',
			'search-forms',
			'cart',
			'multi-currency',
		);

		foreach ( $utils as $util ) {
			$filename  = YITH_WCBK_INCLUDES_PATH . '/integrations/plugins/wpml/class-yith-wcbk-wpml-' . $util . '.php';
			$classname = $this->get_class_name_from_slug( $util );

			$var = str_replace( '-', '_', $util );
			if ( file_exists( $filename ) && ! class_exists( $classname ) ) {
				require_once $filename;
			}

			if ( method_exists( $classname, 'get_instance' ) ) {
				$this->$var = $classname::get_instance( $this );
			}
		}
	}


	/**
	 * Return an array of meta to copy from parent booking product
	 *
	 * @return array
	 */
	public static function get_meta_to_copy_from_parent_product() {
		$meta = array(
			'yith_booking_duration_type',
			'yith_booking_duration',
			'yith_booking_duration_unit',
			'yith_booking_minimum_duration',
			'yith_booking_maximum_duration',
			'yith_booking_enable_calendar_range_picker',
			'yith_booking_request_confirmation',
			'yith_booking_can_be_cancelled',
			'yith_booking_cancelled_duration',
			'yith_booking_cancelled_unit',
			'yith_booking_location',
			'yith_booking_location_lat',
			'yith_booking_location_lng',
			'yith_booking_max_per_block',
			'yith_booking_allow_after',
			'yith_booking_allow_after_unit',
			'yith_booking_allow_until',
			'yith_booking_allow_until_unit',
			'yith_booking_availability_range',
			'yith_booking_costs_range',
			'yith_booking_base_cost',
			'yith_booking_block_cost',
			'yith_booking_has_persons',
			'yith_booking_min_persons',
			'yith_booking_max_persons',
			'yith_booking_multiply_costs_by_persons',
			'yith_booking_enable_person_types',
			'yith_booking_person_types',
			'yith_booking_services',
		);

		return apply_filters( 'yith_wcbk_wpml_integration_meta_to_copy_from_parent_product', $meta );
	}

	/**
	 * Retrieve the WPML parent product id
	 *
	 * @param int $id ID.
	 *
	 * @return int
	 */
	public static function get_parent_id( $id ) {
		/**
		 * WPML Post Translations
		 *
		 * @var WPML_Post_Translation $wpml_post_translations
		 */
		global $wpml_post_translations;

		$parent_id = ! ! $wpml_post_translations ? $wpml_post_translations->get_original_element( $id ) : false;

		if ( $parent_id ) {
			$id = $parent_id;
		}

		return $id;
	}

	/**
	 * Get the id for the current language.
	 *
	 * @param int  $id                         ID.
	 * @param bool $return_original_if_missing Return original if missing - flag.
	 *
	 * @return int|null
	 */
	public function get_current_language_id( $id, $return_original_if_missing = true ) {
		return $this->get_language_id( $id, $return_original_if_missing );
	}

	/**
	 * Get the id for the specified language
	 *
	 * @param int    $id                         ID.
	 * @param bool   $return_original_if_missing Return original if missing - flag.
	 * @param string $language                   Language.
	 *
	 * @return int|null
	 */
	public function get_language_id( $id, $return_original_if_missing = true, $language = '' ) {
		$language = ! ! $language ? $language : $this->current_language;
		if ( function_exists( 'icl_object_id' ) ) {
			$id = icl_object_id( $id, get_post_type( $id ), $return_original_if_missing, $language );
		} elseif ( function_exists( 'wpml_object_id_filter' ) ) {
			$id = wpml_object_id_filter( $id, get_post_type( $id ), $return_original_if_missing, $language );
		}

		return $id;
	}

	/**
	 * Return true if WPML is active.
	 *
	 * @return bool
	 */
	public function is_enabled() {
		global $sitepress;

		return ! empty( $sitepress );
	}

	/**
	 * Restore the current language
	 */
	public function restore_current_language() {
		$this->sitepress->switch_lang( $this->current_language );
	}

	/**
	 * Set the current language to default language
	 */
	public function set_current_language_to_default() {
		$this->sitepress->switch_lang( $this->default_language );
	}

	/**
	 * Get original term ID.
	 *
	 * @param int    $id       Term ID.
	 * @param string $taxonomy The taxonomy.
	 *
	 * @return bool
	 */
	public function get_original_term_id( $id, $taxonomy ) {
		global $sitepress;

		return is_callable( array( $sitepress, 'get_original_element_id' ) ) ? $sitepress->get_original_element_id( $id, 'tax_' . $taxonomy ) : $id;
	}
}
