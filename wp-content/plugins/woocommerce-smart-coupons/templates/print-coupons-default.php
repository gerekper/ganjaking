<?php
/**
 * Print coupons html content
 *
 * @author      StoreApps
 * @since       4.7.0
 * @version     1.2.0
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
			#sc-cc .wc-sc-print-coupons-wrapper {
				display: table;
			}
			#sc-cc .coupon-container {
				display: inline-block;
				page-break-inside: avoid;
				margin: .8rem 1rem;
				border: 1px solid #ccc !important;
			}
		</style>
		<style type="text/css"><?php echo ( isset( $coupon_styles ) && ! empty( $coupon_styles ) ) ? esc_html( wp_strip_all_tags( $coupon_styles, true ) ) : ''; // phpcs:ignore ?></style>
		<?php
		if ( 'custom-design' !== $design ) {
			?>
				<style type="text/css">
					:root {
						--sc-color1: <?php echo esc_html( $background_color ); ?>;
						--sc-color2: <?php echo esc_html( $foreground_color ); ?>;
						--sc-color3: <?php echo esc_html( $third_color ); ?>;
					}
				</style>
				<?php
		}
		?>
	</head>
	<body <?php body_class(); ?>>
		<div id="sc-cc" class="woocommerce wc-sc-print-coupons-container">
			<div class="wc-sc-print-coupons-wrapper sc-coupons-list">
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

				$coupon_type = ( ! empty( $coupon_meta['coupon_type'] ) ) ? $coupon_meta['coupon_type'] : '';

				if ( 'yes' === $is_free_shipping ) {
					if ( ! empty( $coupon_type ) ) {
						$coupon_type .= __( ' & ', 'woocommerce-smart-coupons' );
					}
					$coupon_type .= __( 'Free Shipping', 'woocommerce-smart-coupons' );
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
				}

				$coupon_description = '';

				if ( ! empty( $coupon_post->post_excerpt ) && 'yes' === $show_coupon_description ) {
					$coupon_description = $coupon_post->post_excerpt;
				}

				$is_percent = $woocommerce_smart_coupon->is_percent_coupon( array( 'coupon_object' => $coupon ) );

				$args = array(
					'coupon_object'      => $coupon,
					'coupon_amount'      => $coupon_amount,
					'amount_symbol'      => ( true === $is_percent ) ? '%' : get_woocommerce_currency_symbol(),
					'discount_type'      => wp_strip_all_tags( $coupon_type ),
					'coupon_description' => ( ! empty( $coupon_description ) ) ? $coupon_description : wp_strip_all_tags( $woocommerce_smart_coupon->generate_coupon_description( array( 'coupon_object' => $coupon ) ) ),
					'coupon_code'        => $coupon_code,
					'coupon_expiry'      => ( ! empty( $expiry_date ) ) ? $woocommerce_smart_coupon->get_expiration_format( $expiry_date ) : __( 'Never expires', 'woocommerce-smart-coupons' ),
					'thumbnail_src'      => $woocommerce_smart_coupon->get_coupon_design_thumbnail_src(
						array(
							'design'        => $design,
							'coupon_object' => $coupon,
						)
					),
					'classes'            => '',
					'template_id'        => $design,
					'is_percent'         => $is_percent,
				);

				wc_get_template( 'coupon-design/' . $design . '.php', $args, '', plugin_dir_path( WC_SC_PLUGIN_FILE ) . 'templates/' );

			}
			?>
			</div>
			<div class="wc-sc-terms-page-wrapper">
				<?php
				if ( ! empty( $terms_page_content ) ) {
					?>
					<div class="wc-sc-terms-page-content">
						<?php
							echo wp_kses_post( $terms_page_content ); // phpcs:ignore 
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
