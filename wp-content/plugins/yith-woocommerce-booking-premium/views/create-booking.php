<?php
/**
 * Create booking template.
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking\Views
 */

defined( 'YITH_WCBK' ) || exit; // Exit if accessed directly.

$product_id           = ! empty( $_REQUEST['product_id'] ) ? absint( $_REQUEST['product_id'] ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
$assign_order_default = apply_filters( 'yith_wcbk_create_booking_assign_order_default', 'no' );
?>

<div class="yith-wcbk-create-booking__wrapper">
	<form method="POST">
		<div class="yith-wcbk-create-booking__content">
			<?php wp_nonce_field( 'create-booking', 'yith-wcbk-nonce' ); ?>
			<div class="yith-wcbk-create-booking__options">

				<?php
				/**
				 * DO_ACTION: yith_wcbk_before_create_booking_page
				 * Allows to render some content in the booking creation template before the default options.
				 */
				do_action( 'yith_wcbk_before_create_booking_page' );
				?>

				<div class="yith-wcbk-form-section yith-wcbk-create-booking__user-id__row">
					<label class='yith-wcbk-form-section__label' for="yith-wcbk-create-booking__user-id"><?php esc_html_e( 'User', 'yith-booking-for-woocommerce' ); ?></label>
					<div class="yith-wcbk-form-section__content">
						<?php
						yith_plugin_fw_get_field(
							array(
								'type'     => 'ajax-customers',
								'id'       => 'yith-wcbk-create-booking__user-id',
								'name'     => 'user_id',
								'multiple' => false,
								'data'     => array(
									'placeholder' => __( 'Guest', 'yith-booking-for-woocommerce' ),
									'allow_clear' => true,
								),
								'style'    => ' ',
							),
							true
						);
						?>
					</div>
				</div>

				<div class="yith-wcbk-form-section yith-wcbk-create-booking__product-id__row">
					<label class='yith-wcbk-form-section__label' for="yith-wcbk-create-booking__product-id"><?php esc_html_e( 'Bookable Product', 'yith-booking-for-woocommerce' ); ?></label>
					<div class="yith-wcbk-form-section__content">
						<?php
						yith_plugin_fw_get_field(
							array(
								'type'     => 'ajax-products',
								'id'       => 'yith-wcbk-create-booking__product-id',
								'name'     => 'product_id',
								'multiple' => false,
								'data'     => array(
									'placeholder'  => __( 'Select a bookable product...', 'yith-booking-for-woocommerce' ),
									'product_type' => YITH_WCBK_Product_Post_Type_Admin::$prod_type,
									'allow_clear'  => true,
								),
								'value'    => $product_id,
								'style'    => ' ',
							),
							true
						);
						?>
					</div>
				</div>

				<div class="yith-wcbk-form-section yith-wcbk-create-booking__assign-order__row">
					<label class='yith-wcbk-form-section__label' for="yith-wcbk-create-booking__assign-order"><?php esc_html_e( 'Assign Order', 'yith-booking-for-woocommerce' ); ?></label>
					<div class="yith-wcbk-form-section__content">
						<?php
						yith_plugin_fw_get_field(
							array(
								'type'    => 'select',
								'class'   => 'wc-enhanced-select',
								'id'      => 'yith-wcbk-create-booking__assign-order',
								'name'    => 'assign_order',
								'options' => apply_filters(
									'yith_wcbk_create_booking_assign_order_options',
									array(
										'no'       => __( 'Don\'t assign this booking to any order', 'yith-booking-for-woocommerce' ),
										'new'      => __( 'Create new order for this booking', 'yith-booking-for-woocommerce' ),
										'specific' => __( 'Assign this booking to a specific order', 'yith-booking-for-woocommerce' ),
									)
								),
								'value'   => $assign_order_default,
								'style'   => ' ',
							),
							true
						);
						?>
					</div>
				</div>

				<div class="yith-wcbk-form-section yith-wcbk-create-booking__order-id__row">
					<label class='yith-wcbk-form-section__label' for="yith-wcbk-create-booking__order-id"><?php esc_html_e( 'Related Order', 'yith-booking-for-woocommerce' ); ?></label>
					<div class="yith-wcbk-form-section__content">
						<?php
						yith_plugin_fw_get_field(
							array(
								'type'     => 'ajax-posts',
								'id'       => 'yith-wcbk-create-booking__order-id',
								'name'     => 'order_id',
								'multiple' => false,
								'style'    => 'width:350px',
								'data'     => array(
									'action'      => 'yith_wcbk_json_search_order',
									'security'    => wp_create_nonce( 'search-orders' ),
									'placeholder' => __( 'Search order', 'yith-booking-for-woocommerce' ),
									'allow_clear' => true,
								),
							),
							true
						);
						?>
					</div>
				</div>

				<?php
				/**
				 * DO_ACTION: yith_wcbk_after_create_booking_page
				 * Allows to render some content in the booking creation template after the default options.
				 */
				do_action( 'yith_wcbk_after_create_booking_page' );
				?>

			</div>
			<div class="yith-wcbk-create-booking__booking-form product"></div>
		</div>
		<div class="yith-wcbk-create-booking__footer">
			<span class="yith-wcbk-create-booking__cancel yith-plugin-fw__button--secondary"><?php echo esc_html( _x( 'Cancel', 'Action in button', 'yith-booking-for-woocommerce' ) ); ?></span>
			<button type="submit" name="create-booking" class="yith-wcbk-create-booking__create yith-plugin-fw__button--primary" disabled><?php esc_html_e( 'Create Booking', 'yith-booking-for-woocommerce' ); ?></button>
		</div>
	</form>
</div>
