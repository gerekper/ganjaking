<?php
/**
 * Class YITH_WCBK_Wpml_Person_Types
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking
 */

defined( 'YITH_WCBK' ) || exit;

/**
 * Class YITH_WCBK_Wpml_Person_Types
 *
 * @since   1.0.3
 */
class YITH_WCBK_Wpml_Person_Types {
	/**
	 * Single intance of the class.
	 *
	 * @var YITH_WCBK_Wpml_Person_Types
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
	 * @return YITH_WCBK_Wpml_Person_Types
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

		// Translate the title of the person type.
		add_filter( 'yith_wcbk_get_person_type_title', array( $this, 'translate_person_type_title' ), 10, 2 );

		// Retrieve only the person types in Default Language.
		add_action( 'yith_wcbk_before_get_person_types', array( $this->wpml_integration, 'set_current_language_to_default' ) );
		add_action( 'yith_wcbk_after_get_person_types', array( $this->wpml_integration, 'restore_current_language' ) );
	}

	/**
	 * Translate the person type title in current language
	 *
	 * @param string $title          The title.
	 * @param int    $person_type_id Person type ID.
	 *
	 * @return string
	 */
	public function translate_person_type_title( $title, $person_type_id ) {
		return get_the_title( $this->wpml_integration->get_current_language_id( $person_type_id ) );
	}
}
