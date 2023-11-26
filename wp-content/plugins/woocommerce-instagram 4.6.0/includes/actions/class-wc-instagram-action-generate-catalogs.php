<?php
/**
 * Action: Generate catalogs.
 *
 * @package WC_Instagram_Action/Actions
 * @since   4.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_Instagram_Action_Generate_Catalogs.
 */
class WC_Instagram_Action_Generate_Catalogs extends WC_Instagram_Action {

	/**
	 * Constructor.
	 *
	 * @since 4.0.0
	 */
	public function __construct() {
		$this->action = 'generate_catalogs';

		parent::__construct( HOUR_IN_SECONDS * wc_instagram_get_setting( 'generate_catalogs_interval', 1 ) );
	}

	/**
	 * Processes the action.
	 *
	 * @since 4.0.0
	 */
	public function action() {
		$catalog_ids = wc_instagram_get_product_catalogs( array(), 'ids' );

		if ( empty( $catalog_ids ) ) {
			return;
		}

		$background = WC_Instagram_Backgrounds::get( 'generate_catalog' );

		if ( ! $background ) {
			return;
		}

		foreach ( $catalog_ids as $catalog_id ) {
			$background->maybe_push_catalog( $catalog_id, 'xml' );
		}

		$background->save()->dispatch();
	}
}
