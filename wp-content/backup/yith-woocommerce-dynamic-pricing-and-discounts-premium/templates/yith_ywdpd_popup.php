<?php
/**
 * Popup gift.
 *
 * @package YITH WooCommerce Dynamic Pricing and Discounts Premium
 * @since   1.0.0
 * @version 1.6.0
 * @author  YITH
 *
 * @var array $gift_rules_to_apply
 */

if ( ! defined( 'ABSPATH' ) && ! isset( $gift_rules_to_apply ) ) {
	exit;
}
/* translators: 1: quantity of gifts 2: gift rule name to */
$title = __( 'Choose %1$s free gift for %2$s', 'ywdpd' );
?>
<div class="ywdpd_popup">
    <div class="ywdpd_popup_wrapper">
        <div class="ywdpd_popup_general_content">
            <span class="ywdpd_close"></span>
            <div id="ywdpd_popup_container" class="ywdpd_popup_content">
                <div class="ywdpd_step1">
					<?php
					foreach ( $gift_rules_to_apply as $rule_id => $rule ) :
						$total_items_in_cart = YITH_WC_Dynamic_Pricing_Gift_Product()->get_total_gift_product_by_rule( $rule_id );
						$allowed_items = $gift_rules_to_apply[ $rule_id ]['amount_gift_product_allowed'];
						$product_to_gift = isset( $rule['gift_product_selection'] ) ? $rule['gift_product_selection'] : array();
						if ( $total_items_in_cart < $allowed_items && count( $product_to_gift ) > 0 ) :
							$span_qty = sprintf( ' <span class="ywdpd_quantity">%s</span>', ( $allowed_items - $total_items_in_cart ) );
							$title = sprintf( $title, $span_qty, get_the_title( $rule['id'] ) );
							?>
                            <div id="ywdpd_single_rule_<?php echo esc_attr( $rule_id ); ?>"
                                 class="ywdpd_single_rule_container"
                                 data-allowed_items="<?php echo esc_attr( $allowed_items - $total_items_in_cart ); ?>">
                                <h3><?php echo wp_kses_post( $title ); ?></h3>
                                <div class="ywdpd_product_stage ">
                                    <ul class="ywdpd_products products owl-carousel owl-theme">
										<?php
										foreach ( $product_to_gift as $product_id ) :
											$product = wc_get_product( $product_id );
											$image = $product->get_image();

											if ( ! YITH_WC_Dynamic_Pricing_Gift_Product()->is_gift_product_in_cart( $rule_id, $product_id ) ) {
												?>
                                                <li class="product item"
                                                    data-product_id="<?php echo esc_attr( $product_id ); ?>"
                                                    data-ywdpd_rule_id="<?php echo esc_attr( $rule_id ); ?>"
                                                    data-product_type="<?php echo esc_attr( $product->get_type() ); ?>">
                                                    <a href="" rel="nofollow" class="ywdpd_single_product">
                                                        <span class="ywdpd_image_badge"></span>
														<?php echo $image; //phpcs:ignore ?>
                                                        <h2><?php echo wp_kses_post( $product->get_formatted_name() ); ?></h2>
                                                        <span class="price">
														<del><?php echo wp_kses_post( wc_price( $product->get_price() ) ); ?></del><ins><?php esc_html_e( 'Free!', 'ywdpd' ); ?></ins>

													</span>
                                                    </a>
                                                </li>
												<?php
											};
											?>
										<?php endforeach; ?>
                                    </ul>
                                </div>
                            </div>
						<?php
						endif;
					endforeach;
					?>
                </div>
                <div class="ywdpd_step2"></div>
                <div class="ywdpd_footer">
                    <a href="" rel="nofollow"><?php esc_html_e( 'No, thanks', 'ywdpd' ); ?></a>
                </div>
            </div>
        </div>
    </div>
</div>
