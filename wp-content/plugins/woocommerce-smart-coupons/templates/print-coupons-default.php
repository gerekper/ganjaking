<?php
/**
 * Print coupons html content
 *
 * @author      StoreApps
 * @since       4.7.0
 * @version     1.0.0
 * @package     woocommerce-smart-coupons/templates/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

global $woocommerce_smart_coupon;
$bloginfo = get_bloginfo( 'name', 'display' );
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
	<head>
		<meta charset="<?php bloginfo( 'charset' ); ?>">
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=2.0">
		<title><?php echo $bloginfo;  // phpcs:ignore ?></title>
		<?php wp_head(); ?>
		<?php
		if ( ! wp_script_is( 'jquery' ) ) {
			wp_enqueue_script( 'jquery' );
		}
		?>
		<style type="text/css">
			body.custom-background {
				background-color: unset;
			}
			.wc-sc-print-coupons-wrapper {
				text-align: center;
				padding: 4em 0;
			}
			.coupon-container {
				margin: .2em;
				box-shadow: 0 0 5px #e0e0e0;
				display: inline-table;
				text-align: center;
				cursor: pointer;
				padding: .55em;
				line-height: 1.4em;
			}

			.coupon-content {
				padding: 0.2em 1.2em;
			}

			.coupon-content .code {
				font-family: monospace;
				font-size: 1.2em;
				font-weight:700;
			}

			.coupon-content .coupon-expire,
			.coupon-content .discount-info {
				font-family: Helvetica, Arial, sans-serif;
				font-size: 1em;
			}
			.coupon-content .discount-description {
				font: .7em/1 Helvetica, Arial, sans-serif;
				width: 250px;
				margin: 10px inherit;
				display: inline-block;
			}
			.wc-sc-terms-page-title h2 {
				font-weight: 600;
				padding-left: 1em;
			}
		</style>
		<style type="text/css"><?php echo ( isset( $coupon_styles ) && ! empty( $coupon_styles ) ) ? $coupon_styles : ''; // phpcs:ignore ?></style>
		<style type="text/css">
			.coupon-container.left:before,
			.coupon-container.bottom:before {
				background: <?php echo esc_html( $foreground_color ); ?> !important;
			}
			.coupon-container.left:hover, .coupon-container.left:focus, .coupon-container.left:active,
			.coupon-container.bottom:hover, .coupon-container.bottom:focus, .coupon-container.bottom:active {
				color: <?php echo esc_html( $background_color ); ?> !important;
			}
		</style>
	</head>
	<body <?php body_class(); ?>>
		<div class="woocommerce wc-sc-print-coupons-container">
			<div class="wc-sc-print-coupons-wrapper">
			<?php
			foreach ( $coupon_codes as $coupon_data ) {
				$coupon = new WC_Coupon( $coupon_data['code'] );

				if ( $woocommerce_smart_coupon->is_wc_gte_30() ) {
					if ( ! is_object( $coupon ) || ! is_callable( array( $coupon, 'get_id' ) ) ) {
						continue;
					}
					$coupon_id = $coupon->get_id();
					if ( empty( $coupon_id ) ) {
						continue;
					}
					$coupon_amount    = $coupon->get_amount();
					$is_free_shipping = ( $coupon->get_free_shipping() ) ? 'yes' : 'no';
					$discount_type    = $coupon->get_discount_type();
					$expiry_date      = $coupon->get_date_expires();
					$coupon_code      = $coupon->get_code();
				} else {
					$coupon_id        = ( ! empty( $coupon->id ) ) ? $coupon->id : 0;
					$coupon_amount    = ( ! empty( $coupon->amount ) ) ? $coupon->amount : 0;
					$is_free_shipping = ( ! empty( $coupon->free_shipping ) ) ? $coupon->free_shipping : '';
					$discount_type    = ( ! empty( $coupon->discount_type ) ) ? $coupon->discount_type : '';
					$expiry_date      = ( ! empty( $coupon->expiry_date ) ) ? $coupon->expiry_date : '';
					$coupon_code      = ( ! empty( $coupon->code ) ) ? $coupon->code : '';
				}

				if ( empty( $coupon_id ) || empty( $discount_type ) ) {
					continue;
				}

				$coupon_post = get_post( $coupon_id );

				$coupon_meta = $woocommerce_smart_coupon->get_coupon_meta_data( $coupon );

				?>
						<div class="coupon-container <?php echo esc_attr( $woocommerce_smart_coupon->get_coupon_container_classes() ); ?>" style="<?php echo $woocommerce_smart_coupon->get_coupon_style_attributes(); // phpcs:ignore ?>">
							<?php
								echo '<div class="coupon-content ' . esc_attr( $woocommerce_smart_coupon->get_coupon_content_classes() ) . '">
												<div class="discount-info">';

							$discount_title = '';

							if ( ! empty( $coupon_meta['coupon_amount'] ) && ! empty( $coupon_amount ) ) {
								$discount_title = $coupon_meta['coupon_amount'] . ' ' . $coupon_meta['coupon_type'];
							}

							$discount_title = apply_filters( 'wc_smart_coupons_display_discount_title', $discount_title, $coupon );

							if ( $discount_title ) {

								// Not escaping because 3rd party developer can have HTML code in discount title.
								echo $discount_title; // phpcs:ignore

								if ( 'yes' === $is_free_shipping ) {
									echo __( ' &amp; ', 'woocommerce-smart-coupons' ); // phpcs:ignore
								}
							}

							if ( 'yes' === $is_free_shipping ) {
								echo esc_html__( 'Free Shipping', 'woocommerce-smart-coupons' );
							}
								echo '</div>';

								echo '<div class="code">' . esc_html( $coupon_code ) . '</div>';

								$show_coupon_description = get_option( 'smart_coupons_show_coupon_description', 'no' );
							if ( ! empty( $coupon_post->post_excerpt ) && 'yes' === $show_coupon_description ) {
								echo '<div class="discount-description">' . esc_html( $coupon_post->post_excerpt ) . '</div>';
							}

							if ( ! empty( $expiry_date ) ) {

								$expiry_time = (int) get_post_meta( $coupon_id, 'wc_sc_expiry_time', true );
								if ( ! empty( $expiry_time ) ) {
									if ( $woocommerce_smart_coupon->is_wc_gte_30() && $expiry_date instanceof WC_DateTime ) {
										$expiry_date = $expiry_date->getTimestamp();
									} elseif ( ! is_int( $expiry_date ) ) {
										$expiry_date = strtotime( $expiry_date );
									}
									$expiry_date += $expiry_time; // Adding expiry time to expiry date.
								}

								$expiry_date = $woocommerce_smart_coupon->get_expiration_format( $expiry_date );

								echo '<div class="coupon-expire">' . esc_html( $expiry_date ) . '</div>';
							} else {

								echo '<div class="coupon-expire">' . esc_html__( 'Never Expires ', 'woocommerce-smart-coupons' ) . '</div>';
							}

								echo '</div>';
							?>
							</div>
							<?php
			}
			?>
			</div>
			<div class="wc-sc-terms-page-wrapper">
				<?php
				if ( ! empty( $terms_page_content ) ) {
					?>
					<div class="wc-sc-terms-page-content">
						<?php
							echo $terms_page_content; // phpcs:ignore 
						?>
					</div>
					<?php
				}
				?>
			</div>
		</div>
		<script type="text/javascript">
			jQuery(function(){
				window.print();
			});
		</script>
	</body>
</html>
