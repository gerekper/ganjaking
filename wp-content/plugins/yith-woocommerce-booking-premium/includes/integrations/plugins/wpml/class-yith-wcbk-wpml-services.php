<?php
/**
 * Class YITH_WCBK_Wpml_Services
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking
 */

defined( 'YITH_WCBK' ) || exit;

/**
 * Class YITH_WCBK_Wpml_Services
 *
 * @since   1.0.3
 */
class YITH_WCBK_Wpml_Services {
	/**
	 * Single intance of the class.
	 *
	 * @var YITH_WCBK_Wpml_Services
	 */
	private static $instance;

	/**
	 * WPML Integration instance.
	 *
	 * @var YITH_WCBK_Wpml_Integration
	 */
	public $wpml_integration;

	/**
	 * Singleton implementation
	 *
	 * @param YITH_WCBK_Wpml_Integration $wpml_integration WPML Integration instance.
	 *
	 * @return YITH_WCBK_Wpml_Services
	 */
	public static function get_instance( $wpml_integration ) {
		return ! is_null( self::$instance ) ? self::$instance : self::$instance = new static( $wpml_integration );
	}

	/**
	 * Constructor
	 *
	 * @param YITH_WCBK_Wpml_Integration $wpml_integration WPML Integration instance.
	 */
	private function __construct( $wpml_integration ) {
		$this->wpml_integration = $wpml_integration;

		if ( ! yith_wcbk_is_services_module_active() ) {
			return;
		}

		// Translate the service names.
		add_filter( 'yith_wcbk_service_get_name', array( $this, 'translate_service_name' ), 10, 2 );

		// Translate the service description.
		add_filter( 'yith_wcbk_service_get_description', array( $this, 'translate_service_description' ), 10, 2 );

		// Display extra fields for taxonomy.
		add_action( YITH_WCBK_Post_Types::SERVICE_TAX . '_add_form_fields', array( $this, 'add_taxonomy_fields' ), 1, 1 );
		add_action( YITH_WCBK_Post_Types::SERVICE_TAX . '_edit_form_fields', array( $this, 'edit_taxonomy_fields' ), 1, 1 );
		add_action( 'yith_wcbk_process_service_meta', array( $this, 'set_meta_before_saving' ), 10, 1 );

		// Display languages in service table.
		add_filter( 'yith_wcbk_booking_services_list_additional_columns', array( $this, 'get_columns' ) );
		add_filter( 'manage_' . YITH_WCBK_Post_Types::SERVICE_TAX . '_custom_column', array( $this, 'custom_columns' ), 10, 3 );
	}

	/**
	 * Add fields to Service taxonomy [Add New Service Screen]
	 *
	 * @param string $taxonomy Current taxonomy name.
	 */
	public function add_taxonomy_fields( $taxonomy ) {
		global $sitepress;
		$active_languages = $sitepress->get_active_languages();
		$languages        = $active_languages;

		if ( isset( $languages[ $this->wpml_integration->default_language ] ) ) {
			unset( $languages[ $this->wpml_integration->default_language ] );
		}

		include __DIR__ . '/views/add-service.php';
	}

	/**
	 * Add WPML Languages column
	 *
	 * @param array $columns The columns.
	 *
	 * @return array The columns list
	 */
	public function get_columns( $columns ) {
		$wpml_languages_title              = __( 'WPML Languages', 'yith-booking-for-woocommerce' );
		$columns['service_wpml_languages'] = "<span class='yith-wcbk-wpml-languages-head tips' data-tip='{$wpml_languages_title}'>$wpml_languages_title</span>";

		return $columns;
	}

	/**
	 * Display WPML flags
	 *
	 * @param string $custom_column Filter value.
	 * @param string $column_name   Column name.
	 * @param int    $term_id       The term id.
	 *
	 * @internal param \The $columns columns
	 * @use      manage_{YITH_WCBK_Post_Types::$service_tax}_custom_column filter
	 */
	public function custom_columns( $custom_column, $column_name, $term_id ) {
		$service = yith_wcbk_get_service( $term_id );
		switch ( $column_name ) {
			case 'service_wpml_languages':
				global $sitepress;
				$active_languages = $sitepress->get_active_languages();
				$languages        = $active_languages;

				if ( isset( $languages[ $this->wpml_integration->default_language ] ) ) {
					unset( $languages[ $this->wpml_integration->default_language ] );
				}

				$translated_names = $service->get_meta( 'wpml_translated_name' );
				$translated_names = ! ! $translated_names ? $translated_names : array();

				foreach ( $languages as $language_code => $language_data ) {
					if ( ! empty( $translated_names[ $language_code ] ) ) {
						$service_translated_name = $translated_names[ $language_code ];
						$flag_url                = esc_url( $this->wpml_integration->sitepress->get_flag_url( $language_code ) );
						$language_name           = esc_html( $language_data['display_name'] );
						$info                    = esc_attr( "$service_translated_name ($language_name)" );
						$flag                    = "<img class='tips' src='$flag_url' width='18' height='12' alt='$language_name' data-tip='$info' style='margin:2px' />";

						return $flag;
					}
				}
				break;
		}

		return $custom_column;
	}

	/**
	 * Edit fields to service taxonomy
	 *
	 * @param WP_Term $service_term Current service information.
	 *
	 * @return void
	 */
	public function edit_taxonomy_fields( $service_term ) {
		global $sitepress;
		$active_languages = $sitepress->get_active_languages();
		$languages        = $active_languages;

		if ( isset( $languages[ $this->wpml_integration->default_language ] ) ) {
			unset( $languages[ $this->wpml_integration->default_language ] );
		}

		$service = yith_wcbk_get_service( $service_term );

		include __DIR__ . '/views/edit-service.php';
	}

	/**
	 * Set additional meta in services before saving.
	 *
	 * @param YITH_WCBK_Service $service The service.
	 */
	public function set_meta_before_saving( $service ) {
		// phpcs:disable WordPress.Security.NonceVerification.Missing
		$meta_data = array(
			'wpml_translated_name'        => wc_clean( wp_unslash( $_POST['yith_booking_service_data']['wpml_translated_name'] ?? array() ) ),
			'wpml_translated_description' => wc_clean( wp_unslash( $_POST['yith_booking_service_data']['wpml_translated_description'] ?? array() ) ),
		);

		foreach ( $meta_data as $key => $value ) {
			$service->update_meta_data( $key, $value );
		}

		// phpcs:enable
	}

	/**
	 * Translate service name.
	 *
	 * @param string            $name    Name.
	 * @param YITH_WCBK_Service $service Service.
	 *
	 * @return string
	 */
	public function translate_service_name( $name, $service ) {

		$language_code = $this->wpml_integration->sitepress->get_current_language();

		$translated_names = $service->get_meta( 'wpml_translated_name' );
		$translated_names = ! ! $translated_names ? $translated_names : array();

		return ! empty( $translated_names[ $language_code ] ) ? $translated_names[ $language_code ] : $name;
	}

	/**
	 * Translate service description.
	 *
	 * @param string            $description Description.
	 * @param YITH_WCBK_Service $service     Service.
	 *
	 * @return string
	 */
	public function translate_service_description( $description, $service ) {
		$language_code = $this->wpml_integration->sitepress->get_current_language();

		$translated_descriptions = $service->get_meta( 'wpml_translated_description' );
		$translated_descriptions = ! ! $translated_descriptions ? $translated_descriptions : array();

		return ! empty( $translated_descriptions[ $language_code ] ) ? $translated_descriptions[ $language_code ] : $description;
	}
}
