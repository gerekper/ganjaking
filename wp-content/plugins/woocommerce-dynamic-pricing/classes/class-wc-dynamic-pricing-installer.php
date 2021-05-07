<?php


class WC_Dynamic_Pricing_Installer {

	private static $instance;

	public static function init() {
		if ( self::$instance == null ) {
			self::$instance = new WC_Dynamic_Pricing_Installer();
		}
	}

	private function __construct() {
		add_action( 'admin_notices', array( $this, 'on_admin_notices' ) );
	}

	public function on_admin_notices() {
		if ( ! defined( 'IFRAME_REQUEST' ) && get_option( 'wc_dynamic_pricing_db_version' ) !== WC_Dynamic_Pricing::instance()->db_version ) {
			$html = __( "<strong>Update for Dynamic Pricing</strong> Percentage discounts are now calculated using the exact amount you enter.  Previously you could enter in 50 or .5 in the amount box and the result would be 50% discount.

Now if you enter in .5 or 0.5 you will actually be getting a half a percent discount.

Please review your rules if you have previously entered in amounts such as .5 for a 50% discount.   If you now want to have a 50% discount the amount must read 50, not the previously allowed value of .5", 'wc_dynamic_pricing' );

			?>
			<div class="notice notice-success is-dismissible">
				<p><?php echo $html; ?></p>
			</div>
			<?php

			update_option('wc_dynamic_pricing_db_version', WC_Dynamic_Pricing::instance()->db_version );
		}
	}

}