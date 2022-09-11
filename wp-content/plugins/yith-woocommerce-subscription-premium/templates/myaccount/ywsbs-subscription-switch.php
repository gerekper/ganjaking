<?php
/**
 * Subscription switch
 *
 * @package YITH WooCommerce Subscription
 * @since   2.0.0
 * @author  YITH
 *
 * @var YWSBS_Subscription $subscription Current Subscription.
 * @var array              $switchable_variations How to show the actions
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$old_variation = wc_get_product( $subscription->get_variation_id() );
add_filter( 'woocommerce_product_variation_title_include_attributes', '__return_true' );
?>

<div class="ywsbs-dropdown-wrapper">
	<a href="#"
		onclick="return false;"><?php echo esc_html( get_option( 'ywsbs_text_switch_plan' ) ); ?></a>
	<div class="ywsbs-dropdown">
		<?php
		foreach ( $switchable_variations as $variation_id ) :
			$variation     = wc_get_product( $variation_id );
			$relashionship = YWSBS_Subscription_Switch::get_switch_relationship_text( $old_variation, $variation );
			$price         = wc_get_price_to_display( $variation, array( 'price' => $variation->get_price() ) );
			?>
			<div class="ywsbs-dropdown-item">
				<p>
				<strong><?php echo wp_kses_post( $relashionship . ' ' . $variation->get_name() ); ?>
				- <?php echo wp_kses_post( wc_price( $price ) ) . ' <span class="price_time_opt"> / ' . wp_kses_post( YWSBS_Subscription_Helper::get_subscription_period_for_price( $variation ) ) . '</span>'; ?></strong>
				</p>
				<p class="ywsbs_plan_description"></p>
				<p class="ywsbs_go_to_checkout"><a href="<?php echo esc_url( ywsbs_get_switch_to_subscription_url( $subscription, $variation_id ) ); ?>"
						title="<?php echo esc_attr( get_option( 'ywsbs_text_buy_new_plan' ) ); ?>"><?php echo esc_html( get_option( 'ywsbs_text_buy_new_plan' ) ); ?></a></p>
			</div>
		<?php endforeach; ?>
	</div>
</div>
<?php remove_filter( 'woocommerce_product_variation_title_include_attributes', '__return_true' ); ?>
