<?php //phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * YWSBS_Subscription_Shortcode set all plugin shortcodes
 *
 * @class   YWSBS_Subscription_Shortcodes
 * @package YITH WooCommerce Subscription
 * @since   2.0.0
 * @author  YITH
 */

if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWSBS_VERSION' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Implements the YWSBS_Subscription_Shortcodes class.
 *
 * @class   YWSBS_Subscription_Shortcodes
 * @package YITH
 * @since   2.0.0
 * @author  YITH
 */
class YWSBS_Subscription_Shortcodes {


	/**
	 * Constructor for the shortcode class
	 */
	public function __construct() {
		add_shortcode( 'ywsbs_my_account_subscriptions', array( __CLASS__, 'my_account_subscriptions_shortcode' ) );
	}


	/**
	 * Add subscription section on my-account page
	 *
	 * @param array  $atts Attributes.
	 * @param string $content Shortcode content.
	 * @return  string
	 * @since   1.0.0
	 */
	public static function my_account_subscriptions_shortcode( $atts, $content = null ) {

		$args                                     = shortcode_atts(
			array(
				'page' => 1,
			),
			$atts
		);
		$num_of_subscription_on_a_page_my_account = apply_filters( 'ywsbs_num_of_subscription_on_a_page_my_account', 10 );
		$all_subs                                 = YWSBS_Subscription_Helper()->get_subscriptions_by_user( get_current_user_id(), -1 );
		$max_pages                                = ceil( count( $all_subs ) / 10 );
		$subscriptions                            = YWSBS_Subscription_Helper()->get_subscriptions_by_user( get_current_user_id(), $args['page'] );
		ob_start();
		wc_get_template(
			'myaccount/my-subscriptions-view.php',
			array(
				'subscriptions' => $subscriptions,
				'max_pages'     => $max_pages,
				'current_page'  => $args['page'],
			),
			'',
			YITH_YWSBS_TEMPLATE_PATH . '/'
		);
		return ob_get_clean();
	}
}
