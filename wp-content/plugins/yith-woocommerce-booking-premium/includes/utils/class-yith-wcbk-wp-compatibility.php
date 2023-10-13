<?php
/**
 * Class YITH_WCBK_WP_Compatibility
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking\Classes
 */

defined( 'YITH_WCBK' ) || exit();

/**
 * Class YITH_WCBK_WP_Compatibility
 *
 * @since   1.1.0
 */
class YITH_WCBK_WP_Compatibility {
	use YITH_WCBK_Singleton_Trait;

	/**
	 * WP Version.
	 *
	 * @var string
	 */
	public $wp_version = '';

	/**
	 * The constructor.
	 */
	protected function __construct() {
		global $wp_version;
		$this->wp_version = $wp_version;
	}

	/**
	 * Get terms.
	 *
	 * @param array $args Arguments.
	 *
	 * @return array|int|WP_Error
	 */
	public function get_terms( $args = array() ) {
		if ( $this->compare( '4.5.0', '>=' ) ) {
			return get_terms( $args );
		} else {
			$taxonomy = $args['taxonomy'] ?? '';
			if ( isset( $args['taxonomy'] ) ) {
				unset( $args['taxonomy'] );
			}

			return get_terms( $taxonomy, $args );
		}
	}

	/**
	 * Version compare with WP Version
	 *
	 * @param string      $version  Version.
	 * @param string|null $operator Operator.
	 *
	 * @return mixed By default returns
	 * -1 if the version is lower than the WP version,
	 * 0 if they are equal, and
	 * 1 if the version is higher than the WP version.
	 *
	 * When using the optional operator argument, the
	 * function will return true if the relationship is the one specified
	 * by the operator, false otherwise.
	 */
	public function compare( $version, $operator = null ) {
		return version_compare( $version, $this->wp_version, $operator );
	}
}
