<?php
/**
 * Coupon Email Content
 *
 * @author      StoreApps
 * @version     1.4.0
 * @package     woocommerce-smart-coupons/templates/plain/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

global $store_credit_label, $woocommerce_smart_coupon;

if ( ! isset( $email ) ) {
	$email = null;
}

if ( has_action( 'woocommerce_email_header' ) ) {
	do_action( 'woocommerce_email_header', $email_heading, $email );
} else {
	if ( function_exists( 'wc_get_template' ) ) {
		wc_get_template( 'emails/email-header.php', array( 'email_heading' => $email_heading ) );
	} else {
		woocommerce_get_template( 'emails/email-header.php', array( 'email_heading' => $email_heading ) );
	}
}
?>

<style type="text/css">
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

<p>
<?php
	echo esc_html__( 'To redeem your discount click on the following coupon(s):', 'woocommerce-smart-coupons' );
?>
</p>

<?php

if ( ! empty( $receiver_details ) ) {
	echo '<div id="sc-cc"><div class="sc-coupons-list">';
	foreach ( $receiver_details as $receiver_data ) {

		$coupon_code    = $receiver_data['code'];
		$sender_message = isset( $receiver_data['message'] ) ? $receiver_data['message'] : '';
		$coupon         = new WC_Coupon( $coupon_code );

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
			$expiry_date      = $coupon->get_date_expires();
			$coupon_code      = $coupon->get_code();
		} else {
			$coupon_id        = ( ! empty( $coupon->id ) ) ? $coupon->id : 0;
			$coupon_amount    = ( ! empty( $coupon->amount ) ) ? $coupon->amount : 0;
			$is_free_shipping = ( ! empty( $coupon->free_shipping ) ) ? $coupon->free_shipping : '';
			$expiry_date      = ( ! empty( $coupon->expiry_date ) ) ? $coupon->expiry_date : '';
			$coupon_code      = ( ! empty( $coupon->code ) ) ? $coupon->code : '';
		}

		$coupon_post = get_post( $coupon_id );

		$coupon_data = $woocommerce_smart_coupon->get_coupon_meta_data( $coupon );

		$coupon_type = ( ! empty( $coupon_data['coupon_type'] ) ) ? $coupon_data['coupon_type'] : '';

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

			$coupon_target              = '';
			$wc_url_coupons_active_urls = get_option( 'wc_url_coupons_active_urls' ); // From plugin WooCommerce URL coupons.
		if ( ! empty( $wc_url_coupons_active_urls ) ) {
			$coupon_target = ( ! empty( $wc_url_coupons_active_urls[ $coupon_id ]['url'] ) ) ? $wc_url_coupons_active_urls[ $coupon_id ]['url'] : '';
		}
		if ( ! empty( $coupon_target ) ) {
			$coupon_target = home_url( '/' . $coupon_target );
		} else {
			$coupon_target = home_url( '/?sc-page=shop&coupon-code=' . $coupon_code );
		}

			$coupon_target = apply_filters( 'sc_coupon_url_in_email', $coupon_target, $coupon );
		?>
		<div>
		<?php
		if ( ! empty( $sender_message ) ) {
			?>
			<div>
			<?php
				echo wp_unslash( $sender_message ); // phpcs:ignore
			?>
			</div>
			<?php
		}
		?>
		<div style="margin: 10px 0;" title="<?php echo esc_html__( 'Click to visit store. This coupon will be applied automatically.', 'woocommerce-smart-coupons' ); ?>">
			<a href="<?php echo esc_url( $coupon_target ); ?>" style="color: #444;">
				<?php wc_get_template( 'coupon-design/' . $design . '.php', $args, '', plugin_dir_path( WC_SC_PLUGIN_FILE ) . 'templates/' ); ?>				
			</a>
		</div>
		</div>
		<?php
	}
	echo '</div></div>';
}

$site_url = ! empty( $url ) ? $url : home_url();
?>
<center>
	<a href="<?php echo esc_url( $site_url ); ?>"><?php echo esc_html__( 'Visit store', 'woocommerce-smart-coupons' ); ?></a>
	<?php
	$is_print         = get_option( 'smart_coupons_is_print_coupon', 'yes' );
	$is_print         = apply_filters( 'wc_sc_email_show_print_link', wc_string_to_bool( $is_print ), array( 'source' => $woocommerce_smart_coupon ) );
	$coupons_to_print = ( ! empty( $receiver_details ) ) ? wp_list_pluck( $receiver_details, 'code' ) : array();
	if ( true === $is_print && ! empty( $coupons_to_print ) ) {
		$print_coupon_url = add_query_arg(
			array(
				'print-coupons' => 'yes',
				'source'        => 'wc-smart-coupons',
				'coupon-codes'  => implode(
					',',
					$coupons_to_print
				),
			),
			home_url()
		);
		?>
		|
		<a href="<?php echo esc_url( $print_coupon_url ); ?>" target="_blank"><?php echo esc_html( _n( 'Print coupon', 'Print all coupons', count( $coupons_to_print ), 'woocommerce-smart-coupons' ) ); ?></a>
		<?php
	}
	?>
</center>

<?php if ( ! empty( $from ) ) { ?>
	<p>
		<?php
			echo esc_html__( 'You got these coupons ', 'woocommerce-smart-coupons' ) . ' ' . esc_html( $from ) . esc_html( $sender );
		?>
	</p>
<?php } ?>

<div style="clear:both;"></div>

<?php
if ( has_action( 'woocommerce_email_footer' ) ) {
	do_action( 'woocommerce_email_footer', $email );
} else {
	if ( function_exists( 'wc_get_template' ) ) {
		wc_get_template( 'emails/email-footer.php' );
	} else {
		woocommerce_get_template( 'emails/email-footer.php' );
	}
}
