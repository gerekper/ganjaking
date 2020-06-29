ctpw_url_field<?php
/**
 * Order Social Box Template
 *
 * Override this template by copying it to [your theme folder]/woocommerce/yith_ctpw_social_box.php
 *
 * @author        Yithemes
 * @package       YITH Custom ThankYou Page for Woocommerce
 * @version       1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

// getting order to get products infos.
if ( ( isset( $_GET['ctpw'] ) && '' !== $_GET['ctpw'] && isset( $_GET['order-received'] ) ) ) { //phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$order = wc_get_order( intval( $_GET['order-received'] ) ); //phpcs:ignore
} else {
	$order = wc_get_order( $social_box_info['order'] );//phpcs:ignore
}

// DO_ACTION yith_ctpw_before_social_box: hook before the social box.
do_action( 'yith_ctpw_before_social_box' );

?>
	<!-- Yith Custom Thank You Page Social Box -->
	<div id="yith-ctpw-social-box" class="yith-ctpw-tabs" style="display: none">

		<h2>
			<?php
			// APPLY_FILTER ctpw_sharebox_title: change the title of Share Box.
			echo wp_kses_post( apply_filters( 'ctpw_sharebox_title', esc_html__( 'Share on...', 'yith-custom-thankyou-page-for-woocommerce' ) ) );
			?>
		</h2>
		<?php /* socials tabs */ ?>
		<div class="yith-ctpw-tabs-nav">
			<?php if ( ( ! $is_shortcode && 'yes' === get_option( 'yith_ctpw_enable_fb_social_box', 'yes' ) ) || ( $is_shortcode && yith_plugin_fw_is_true( $social_box_info['facebook'] ) ) ) { ?>
				<a href="#" class="yith-ctpw-tabs-nav__link is-active">
					<span><img src="<?php echo YITH_CTPW_ASSETS_URL . 'images/facebook.png'; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>"/></span>
					<span><?php esc_html_e( 'Facebook', 'yith-custom-thankyou-page-for-woocommerce' ); ?></span>
				</a>
				<?php
			}
			if ( ( ! $is_shortcode && 'yes' === get_option( 'yith_ctpw_enable_twitter_social_box', 'yes' ) ) || ( $is_shortcode && yith_plugin_fw_is_true( $social_box_info['twitter'] ) ) ) {
				?>
				<a href="#" class="yith-ctpw-tabs-nav__link">
					<span><img src="<?php echo YITH_CTPW_ASSETS_URL . 'images/twitter.png'; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>"/></span>
					<span><?php esc_html_e( 'Twitter', 'yith-custom-thankyou-page-for-woocommerce' ); ?></span>
				</a>
				<?php
			}
			if ( ( ! $is_shortcode && 'yes' === get_option( 'yith_ctpw_enable_pinterest_social_box', 'yes' ) ) || ( $is_shortcode && yith_plugin_fw_is_true( $social_box_info['pinterest'] ) ) ) {
				?>
				<a href="#" class="yith-ctpw-tabs-nav__link">
					<span> <img src="<?php echo YITH_CTPW_ASSETS_URL . 'images/pinterest.png'; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>"/></span>
					<span><?php esc_html_e( 'Pinterest', 'yith-custom-thankyou-page-for-woocommerce' ); ?></span>
				</a>
			<?php } ?>
		</div>

		<?php
		/*FACEBOOK Container *************************************************************************/

		if ( ( ! $is_shortcode && 'yes' === get_option( 'yith_ctpw_enable_fb_social_box', 'yes' ) ) || ( $is_shortcode && yith_plugin_fw_is_true( $social_box_info['facebook'] ) ) ) :
			?>

			<div class="yith-ctpw-tab is-active">
				<div class="yith-ctpw-tab__content">
					<div id="yith-ctwp-social-slider" class="ctpw_facebook">

						<?php
						// print the nav header only if there's more than one product.
						if ( count( $order->get_items() ) > 1 ) :
							?>

							<div class="yith-ctwp-social_nav_container">
								<p class="yith-ctwp-social_navigation yith-ctwp-slider_prev">
									<img src="<?php echo esc_url( apply_filters( 'yith_ctpw_slider_prev', YITH_CTPW_ASSETS_URL . 'images/prev.png' ) ); ?>"/>
								</p>
								<p class="yith-ctwp-social_navigation_message"><?php esc_html_e( 'Select the product to share', 'yith-custom-thankyou-page-for-woocommerce' ); ?></p>
								<p class="yith-ctwp-social_navigation yith-ctwp-slider_next">
									<img src="<?php echo esc_url( apply_filters( 'yith_ctpw_slider_next', YITH_CTPW_ASSETS_URL . 'images/next.png' ) ); ?>"/>
								</p>
							</div>
							<?php
						endif;

						// print a slide for each product.
						foreach ( $order->get_items() as $item ) {
							$_product = apply_filters( 'woocommerce_order_item_product', $order->get_product_from_item( $item ), $item );
							?>

							<div class="yith-ctwp-social-slider_container">
								<div id="yith-ctpw-tab_sharing_product">
									<div class="yith-ctpw-tab_sharing_product_thumb">
										<?php echo $_product->get_image(); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
									</div>
									<div id="ctpw_facebook_p_id_<?php echo $_product->get_id(); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>" class="yith-ctpw-tab_sharing_product_info">
										<?php
										// getting the image url.
										if ( has_post_thumbnail( $_product->get_id() ) ) {
											$att_id[0] = get_post_thumbnail_id( $_product->get_id() );
											$att_url   = wp_get_attachment_image_src( $att_id[0], 'full' );
										} else {
											$att_url[0] = wc_placeholder_img_src();
										}

										// check the url to use, shortened or normal.
										if ( 'none' !== get_option( 'ctpw_url_shortening' ) && get_option( 'ctpw_url_shortening' ) !== null && function_exists( 'YITH_url_shortener' ) ) {
											$p_url = YITH_url_shortener()->url_shortening( apply_filters( 'yith_ctwp_social_url', $_product->get_permalink() ) );
										} else {
											$p_url = esc_url( apply_filters( 'yith_ctwp_social_url', $_product->get_permalink() ) );
										}

										?>
										<input class="ctpw_image_field" type="hidden" value="<?php echo esc_url( $att_url[0] ); ?>"/>
										<input class="ctpw_url_field" type="hidden" value="<?php echo esc_url( $p_url ); ?>"/>
										<input class="ctpw_sharer_field" type="hidden" value="https://www.facebook.com/sharer/sharer.php?u=ctpw_url&picture=ctpw_img&title=ctpw_title&description=ctpw_description"/>
										<input class="ctpw_title_field" ctpw_default_title="<?php echo apply_filters( 'yctpw_just_purchased_string', __( 'I\'ve just purchased: ', 'yith-custom-thankyou-page-for-woocommerce' ) . '\'' . $_product->get_title() . '\'', $_product ); ?>" type="text" value="<?php echo wp_kses_post( apply_filters( 'yctpw_just_purchased_string', esc_html__( 'I\'ve just purchased: ', 'yith-custom-thankyou-page-for-woocommerce' ) . '\'' . $_product->get_title() . '\'', $_product ) ); ?>"/>
										<?php
										$description = '';
										if ( $_product instanceof WC_Product_Variation ) {
											$tp          = get_post( $_product->get_parent_id() );
											$description = $tp->post_excerpt;
										} else {
											$description = ( '' !== yit_get_prop( $_product, 'short_description' ) ) ? yit_get_prop( $_product, 'short_description' ) : $_product->get_short_description();
										}

										if ( empty( $description ) ) {
											$description = yit_get_prop( $_product, 'description' );
										}

										$description = substr( wp_strip_all_tags( strip_shortcodes( $description ) ), 0, 250 );
										?>
										<textarea class="ctpw_excerpt" ctpw_default_description="<?php echo $description . '...'; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>"><?php echo $description . '...'; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></textarea>
									</div>
									<div class="ctpw_share_it">
										<a href="javascript:void(0);" onclick="ctpw_socialize('ctpw_facebook_p_id_<?php echo $_product->get_id(); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>')">
											<?php esc_html_e( 'Share', 'yith-custom-thankyou-page-for-woocommerce' ); ?>
										</a>
									</div>
									<div style="clear:both"></div>
								</div>
								<div style="clear:both"></div>
							</div>

							<?php
						}//end for
						?>
					</div> <?php // end slider. ?>
				</div>
			</div>
		<?php endif; ?>
		<?php
		/* TWITTER  *************************************************************************/
		if ( ( ! $is_shortcode && 'yes' === get_option( 'yith_ctpw_enable_twitter_social_box', 'yes' ) ) || ( $is_shortcode && yith_plugin_fw_is_true( $social_box_info['twitter'] ) ) ) :
			?>
			<div class="yith-ctpw-tab">
				<div class="yith-ctpw-tab__content">
					<div id="yith-ctwp-social-slider" class="ctpw_twitter">
						<?php
						// print the nav header only if there's more than one product.
						if ( count( $order->get_items() ) > 1 ) :
							?>
							<div class="yith-ctwp-social_nav_container">
								<p class="yith-ctwp-social_navigation yith-ctwp-slider_prev">
									<img src="<?php echo esc_url( apply_filters( 'yith_ctpw_slider_prev', YITH_CTPW_ASSETS_URL . 'images/prev.png' ) ); ?>"/>
								</p>
								<p class="yith-ctwp-social_navigation_message"><?php esc_html_e( 'Select the product to share', 'yith-custom-thankyou-page-for-woocommerce' ); ?></p>
								<p class="yith-ctwp-social_navigation yith-ctwp-slider_next">
									<img src="<?php echo esc_url( apply_filters( 'yith_ctpw_slider_next', YITH_CTPW_ASSETS_URL . 'images/next.png' ) ); ?>"/>
								</p>
							</div>
							<?php
						endif;
						// print a slide for each product.
						foreach ( $order->get_items() as $item ) {
							$_product = apply_filters( 'woocommerce_order_item_product', $order->get_product_from_item( $item ), $item );
							?>
							<div class="yith-ctwp-social-slider_container">
								<div id="yith-ctpw-tab_sharing_product">
									<div class="yith-ctpw-tab_sharing_product_thumb">
										<?php echo $_product->get_image(); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
									</div>
									<div id="ctpw_twitter_p_id_<?php echo $_product->get_id(); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>" class="yith-ctpw-tab_sharing_product_info">
										<?php

										// check the url to use, shortened or normal.
										if ( 'none' === get_option( 'ctpw_url_shortening' ) && get_option( 'ctpw_url_shortening' ) !== null && function_exists( 'YITH_url_shortener' ) ) {
											$p_url = YITH_url_shortener()->url_shortening( apply_filters( 'yith_ctwp_social_url', $_product->get_permalink() ) );
										} else {
											$p_url = esc_url( apply_filters( 'yith_ctwp_social_url', $_product->get_permalink() ) );
										}
										?>

										<input class="ctpw_image_field" type="hidden" value=""/>
										<input class="ctpw_url_field" type="hidden" value="<?php echo $p_url; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>"/>
										<input class="ctpw_sharer_field" type="hidden" value="https://twitter.com/share?url=ctpw_url&text=ctpw_description"/>
										<input class="ctpw_title_field" type="hidden" value=""/>
										<textarea class="ctpw_excerpt" ctpw_default_description="<?php echo wp_kses_post( apply_filters( 'yctpw_just_purchased_string', __( 'I\'ve just purchased: ', 'yith-custom-thankyou-page-for-woocommerce' ) ) . '\'' . $_product->get_title() . '\'', $_product ); ?>">
											<?php echo wp_kses_post( apply_filters( 'yctpw_just_purchased_string', __( 'I\'ve just purchased: ', 'yith-custom-thankyou-page-for-woocommerce' ) . '\'' . $_product->get_title() . '\'', $_product ) ); ?>
										</textarea>
										<p id="twit_c_counter" style="display: none;"><?php esc_html_e( 'characters left', 'yith-custom-thankyou-page-for-woocommerce' ); ?> <span></span></p>
									</div>
									<div class="ctpw_share_it"><a href="javascript:void(0);" onclick="ctpw_socialize('ctpw_twitter_p_id_<?php echo $_product->get_id(); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>')"><?php esc_html_e( 'Tweet', 'yith-custom-thankyou-page-for-woocommerce' ); ?></a>
									</div>
									<div style="clear:both"></div>
								</div>
								<div style="clear:both"></div>
							</div>
							<?php
						}//end for
						?>
					</div><?php // end slider. ?>
				</div>
			</div>
		<?php endif; ?>

		<?php
		/* PINTEREST  *************************************************************************/
		if ( ( ! $is_shortcode && 'yes' === get_option( 'yith_ctpw_enable_pinterest_social_box', 'yes' ) ) || ( $is_shortcode && yith_plugin_fw_is_true( $social_box_info['pinterest'] ) ) ) :
			?>
			<div class="yith-ctpw-tab">
				<div class="yith-ctpw-tab__content">
					<div id="yith-ctwp-social-slider" class="ctpw_pinterest">
						<?php
						// print the nav header only if there's more than one product.
						if ( count( $order->get_items() ) > 1 ) :
							?>
							<div class="yith-ctwp-social_nav_container">
								<p class="yith-ctwp-social_navigation yith-ctwp-slider_prev">
									<img src="<?php echo esc_url( apply_filters( 'yith_ctpw_slider_prev', YITH_CTPW_ASSETS_URL . 'images/prev.png' ) ); ?>"/>
								</p>
								<p class="yith-ctwp-social_navigation_message"><?php esc_html_e( 'Select the product to share', 'yith-custom-thankyou-page-for-woocommerce' ); ?></p>
								<p class="yith-ctwp-social_navigation yith-ctwp-slider_next">
									<img src="<?php echo esc_url( apply_filters( 'yith_ctpw_slider_next', YITH_CTPW_ASSETS_URL . 'images/next.png' ) ); ?>"/>
								</p>
							</div>
							<?php
						endif;
						// print a slide for each product.
						foreach ( $order->get_items() as $item ) {
							$_product = apply_filters( 'woocommerce_order_item_product', $order->get_product_from_item( $item ), $item );
							?>
							<div class="yith-ctwp-social-slider_container">
								<div id="yith-ctpw-tab_sharing_product">
									<div class="yith-ctpw-tab_sharing_product_thumb">
										<?php echo $_product->get_image(); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
									</div>
									<div id="ctpw_pinterest_p_id_<?php echo $_product->get_id(); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>" class="yith-ctpw-tab_sharing_product_info">
										<?php
										if ( has_post_thumbnail( $_product->get_id() ) ) {
											$att_id[0] = get_post_thumbnail_id( $_product->get_id() );
											$att_url   = wp_get_attachment_image_src( $att_id[0], 'full' );
										} else {
											$att_url[0] = wc_placeholder_img_src();
										}

										// check the url to use, shortened or normal.
										if ( 'none' !== get_option( 'ctpw_url_shortening' ) && get_option( 'ctpw_url_shortening' ) !== null && function_exists( 'YITH_url_shortener' ) ) {
											$p_url = YITH_url_shortener()->url_shortening( apply_filters( 'yith_ctwp_social_url', $_product->get_permalink() ) );
										} else {
											$p_url = apply_filters( 'yith_ctwp_social_url', $_product->get_permalink() );
										}
										?>
										<input type="hidden" value="<?php echo rawurlencode( $att_url[0] ); ?>"/>
										<input type="hidden" value="<?php echo rawurlencode( $p_url ); ?>"/>
										<input type="hidden" value="http://pinterest.com/pin/create/button/?url=ctpw_url&media=ctpw_img&description=ctpw_title - ctpw_description" />
										<input class="ctpw_title"
											ctpw_default_title="<?php echo wp_kses_post( apply_filters( 'yctpw_just_purchased_string', __( 'I\'ve just purchased: ', 'yith-custom-thankyou-page-for-woocommerce' ) . '\'' . $_product->get_title() . '\'', $_product ) ); ?>"
											type="text"
											value="<?php echo wp_kses_post( apply_filters( 'yctpw_just_purchased_string', __( 'I\'ve just purchased: ', 'yith-custom-thankyou-page-for-woocommerce' ) . '\'' . $_product->get_title() . '\'', $_product ) ); ?>"/>
										<?php

										$description = '';
										if ( $_product instanceof WC_Product_Variation ) {
											$tp          = get_post( $_product->get_parent_id() );
											$description = $tp->post_excerpt;
										} else {
											$description = ( '' !== yit_get_prop( $_product, 'short_description' ) ) ? yit_get_prop( $_product, 'short_description' ) : $_product->get_short_description();
										}

										if ( empty( $description ) ) {
											$description = yit_get_prop( $_product, 'description' );
										}

										$description = substr( wp_strip_all_tags( strip_shortcodes( $description ) ), 0, 250 );

										?>
										<textarea ctpw_default_description="<?php echo $description . '...'; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>" class="ctpw_excerpt"><?php echo $description . '...'; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></textarea>

									</div>
									<div class="ctpw_share_it">
										<a href="javascript:void(0);" onclick="ctpw_socialize('ctpw_pinterest_p_id_<?php echo $_product->get_id(); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>')"><?php esc_html_e( 'Pin it', 'yith-custom-thankyou-page-for-woocommerce' ); ?></a>
									</div>
									<div style="clear:both"></div>
								</div>
								<div style="clear:both"></div>
							</div>

							<?php
						}//end for
						?>
					</div> <?php // end slider. ?>

				</div>
			</div>
		<?php endif; ?>
	</div>
<?php
// DO_ACTION yith_ctpw_after_social_box: hook before the social box.
do_action( 'yith_ctpw_after_social_box' );
?>

