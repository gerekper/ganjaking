<?php
/**
 * Booking Data Metabox
 *
 * @var YITH_WCBK_Booking $booking The booking.
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking
 */

defined( 'YITH_WCBK' ) || exit; // Exit if accessed directly.

?>
<div id="booking-data" class="panel">

	<h2>
		<?php
		// translators: %s is the Booking name.
		echo esc_html( sprintf( _x( '%s details', 'Booking #123 details', 'yith-booking-for-woocommerce' ), $booking->get_name() ) );
		?>
		<span class="yith-booking-status <?php echo esc_attr( $booking->get_status( 'edit' ) ); ?>"><?php echo esc_html( $booking->get_status_text() ); ?></span>
	</h2>

	<div class="booking-data__column_container yith-plugin-ui">
		<div class="booking-data__column">
			<h4><?php esc_html_e( 'General Details', 'yith-booking-for-woocommerce' ); ?></h4>


			<div class="form-field form-field-wide">
				<label for="yith-booking-date"><?php esc_html_e( 'Booking creation date:', 'yith-booking-for-woocommerce' ); ?></label>
				<div class="booking-data__date-created">
					<input type="text" class="date-picker" name="yith_booking_date" id="yith-booking-date" maxlength="10"
						value="<?php echo esc_attr( $booking->get_date_created( 'edit' )->date_i18n( 'Y-m-d' ) ); ?>"
						pattern="[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])"/>
					@
					<input type="number" class="hour" placeholder="<?php esc_attr_e( 'h', 'woocommerce' ); ?>"
						name="yith_booking_date_hour" id="yith-booking-date-hour" min="0" max="23" step="1"
						value="<?php echo esc_attr( $booking->get_date_created( 'edit' )->date_i18n( 'H' ) ); ?>" pattern="([01]?[0-9]{1}|2[0-3]{1})"/>
					:
					<input type="number" class="minute" placeholder="<?php esc_attr_e( 'm', 'woocommerce' ); ?>" name="yith_booking_date_minute" id="yith-booking-date-minute" min="0" max="59" step="1"
						value="<?php echo esc_attr( $booking->get_date_created( 'edit' )->date_i18n( 'i' ) ); ?>" pattern="[0-5]{1}[0-9]{1}"/>
				</div>
			</div>

			<div class="form-field form-field-wide">
				<label for="yith_booking_status"><?php esc_html_e( 'Booking status:', 'yith-booking-for-woocommerce' ); ?></label>
				<select id="yith_booking_status" name="yith_booking_status" class="wc-enhanced-select" style="width:100%">
					<?php
					$statuses = yith_wcbk_get_booking_statuses();
					foreach ( $statuses as $_status => $_status_name ) {
						echo '<option value="' . esc_attr( $_status ) . '" ' . selected( $_status, $booking->get_status( 'edit' ), false ) . '>' . esc_html( $_status_name ) . '</option>';
					}
					?>
				</select>
			</div>

			<div class="form-field form-field-wide yith-booking-product">
				<?php
				$product_id = $booking->get_product_id( 'edit' );
				$product    = wc_get_product( $product_id );

				if ( $product ) {
					$product_name = $product->get_formatted_name();
				} elseif ( $product_id ) {
					// translators: %s is the product ID.
					$product_name = sprintf( __( 'Deleted Product #%s', 'yith-booking-for-woocommerce' ), $product_id );
				} else {
					$product_name = '';
				}

				?>
				<label><?php esc_html_e( 'Bookable Product:', 'yith-booking-for-woocommerce' ); ?>
					<?php
					if ( $product ) {
						$product_edit_link = get_edit_post_link( $product_id );
						echo wp_kses_post( sprintf( '<a href="%s">%s &rarr;</a>', $product_edit_link, __( 'View product', 'yith-booking-for-woocommerce' ) ) );
					}
					?>
				</label>
				<input type="text" disabled value="<?php echo esc_attr( $product_name ); ?>"/>
			</div>

			<div class="form-field form-field-wide yith-booking-order">
				<label for="yith_booking_order"><?php esc_html_e( 'Order:', 'yith-booking-for-woocommerce' ); ?>
					<?php
					$order_id = $booking->get_order_id( 'edit' );
					if ( $order_id ) {
						$order_link = get_edit_post_link( $order_id );
						echo wp_kses_post( sprintf( '<a href="%s">%s &rarr;</a>', $order_link, __( 'View order', 'yith-booking-for-woocommerce' ) ) );
					}
					?>
				</label>
				<?php
				$order_string  = '';
				$order_id      = $booking->get_order_id( 'edit' );
				$data_selected = array();

				if ( $order_id ) {
					$order_string               = '#' . absint( $order_id ) . ' &ndash; ' . esc_html( get_the_title( $order_id ) );
					$data_selected[ $order_id ] = $order_string;
				}

				if ( current_user_can( 'yith_manage_bookings' ) ) {
					yit_add_select2_fields(
						array(
							'class'            => 'yith-wcbk-order-search',
							'id'               => 'yith_booking_order',
							'name'             => 'yith_booking_order',
							'data-placeholder' => __( 'N.D.', 'yith-booking-for-woocommerce' ),
							'data-allow_clear' => true,
							'data-multiple'    => false,
							'value'            => $order_id,
							'data-selected'    => $data_selected,
							'style'            => 'width:100%',
						)
					);

				} else {
					echo esc_html( $order_string );
				}
				?>
			</div>

			<div class="form-field form-field-wide yith-booking-user">
				<label for="yith_booking_user"><?php esc_html_e( 'User:', 'yith-booking-for-woocommerce' ); ?>
					<?php if ( current_user_can( 'yith_manage_bookings' ) ) : ?>
						<?php
						$user_id = $booking->get_user_id( 'edit' );
						if ( $user_id ) {
							$edit_link = get_edit_user_link( $user_id );
							wp_kses_post( sprintf( '<a href="%s">%s &rarr;</a>', $edit_link, __( 'View user', 'yith-booking-for-woocommerce' ) ) );
						}
						?>
					<?php endif; ?>

				</label>
				<?php
				$user_string   = '';
				$user_id       = $booking->get_user_id( 'edit' );
				$data_selected = array();

				if ( $user_id ) {
					$user        = get_user_by( 'id', $user_id );
					$user_string = '#' . $user_id;
					if ( $user ) {
						$user_string = esc_html( $user->display_name ) . ' (#' . absint( $user->ID ) . ' &ndash; ' . esc_html( $user->user_email ) . ')';
					}
					$data_selected[ $user_id ] = $user_string;
				}
				?>
				<?php
				if ( current_user_can( 'yith_manage_bookings' ) ) {
					yit_add_select2_fields(
						array(
							'class'            => 'wc-customer-search',
							'id'               => 'yith_booking_user',
							'name'             => 'yith_booking_user',
							'data-placeholder' => __( 'N.D.', 'yith-booking-for-woocommerce' ),
							'data-allow_clear' => true,
							'data-multiple'    => false,
							'value'            => $user_id,
							'data-selected'    => $data_selected,
							'style'            => 'width:100%',
						)
					);
				} else {
					echo esc_html( $user_string );
				}
				?>
			</div>

			<?php
			$amount         = $booking->get_sold_price( true );
			$amount_label   = __( 'Amount', 'yith-booking-for-woocommerce' );
			$amount_classes = array( 'booking-data__amount' );
			if ( false === $amount && apply_filters( 'yith_wcbk_admin_booking_show_calculated_amount', $booking->has_status( array( 'pending-confirm', 'confirmed' ) ), $booking ) ) {
				$amount           = $booking->get_calculated_price();
				$amount_label     = __( 'Calculated Amount', 'yith-booking-for-woocommerce' );
				$amount_classes[] = 'booking-data__amount--calculated';
				if ( false !== $amount ) {
					$amount = wc_get_price_including_tax( $booking->get_product(), array( 'price' => $amount ) );
				}
			}
			?>

			<?php if ( false !== $amount ) : ?>
				<h4><?php echo esc_html( $amount_label ); ?></h4>

				<div class="form-field form-field-wide <?php echo esc_attr( implode( ' ', $amount_classes ) ); ?>">
					<?php echo wp_kses_post( wc_price( $amount ) ); ?>
				</div>
			<?php endif; ?>

			<?php
			/**
			 * DO_ACTION: yith_wcbk_booking_metabox_info_after_first_column
			 * Hook to output something in the booking data meta-box after the first column.
			 *
			 * @param YITH_WCBK_Booking $booking The booking.
			 */
			do_action( 'yith_wcbk_booking_metabox_info_after_first_column', $booking );
			?>
		</div>

		<div class="booking-data__column">
			<h4><?php esc_html_e( 'Booking Date', 'yith-booking-for-woocommerce' ); ?></h4>

			<div class="form-field form-field-wide"><label><?php esc_html_e( 'Duration', 'yith-booking-for-woocommerce' ); ?>:</label>
				<?php echo wp_kses_post( $booking->get_duration_html() ); ?>
			</div>

			<div class="booking_data_half">
				<div class="form-field form-field-wide"><label><?php esc_html_e( 'From', 'yith-booking-for-woocommerce' ); ?>:</label>
					<?php
					yith_wcbk_create_date_field(
						$booking->get_duration_unit( 'edit' ),
						array(
							'id'    => 'yith-booking-from',
							'name'  => 'yith_booking_from',
							'value' => $booking->get_from( 'edit' ),
						),
						true
					);
					?>
				</div>
			</div>
			<div class="booking_data_half">

				<div class="form-field form-field-wide"><label><?php esc_html_e( 'To', 'yith-booking-for-woocommerce' ); ?>:</label>
					<?php
					yith_wcbk_create_date_field(
						$booking->get_duration_unit( 'edit' ),
						array(
							'id'    => 'yith-booking-to',
							'name'  => 'yith_booking_to',
							'value' => $booking->get_to( 'edit' ),
						),
						true
					);
					?>
				</div>
			</div>

			<div class="clear"></div>

			<?php if ( $booking->is_all_day() ) : ?>
				<div class="form-field form-field-wide yith-wcbk-booking-all-day-mark__container">
					<span class="yith-wcbk-booking-all-day-mark"><?php esc_html_e( 'All Day', 'yith-booking-for-woocommerce' ); ?></span>
				</div>
			<?php endif ?>

			<?php
			/**
			 * DO_ACTION: yith_wcbk_booking_metabox_info_after_second_column
			 * Hook to output something in the booking data meta-box after the second column.
			 *
			 * @param YITH_WCBK_Booking $booking The booking.
			 */
			do_action( 'yith_wcbk_booking_metabox_info_after_second_column', $booking );
			?>

		</div>
		<div class="booking-data__column">
			<?php
			/**
			 * DO_ACTION: yith_wcbk_booking_metabox_info_after_third_column
			 * Hook to output something in the booking data meta-box after the third column.
			 *
			 * @param YITH_WCBK_Booking $booking The booking.
			 */
			do_action( 'yith_wcbk_booking_metabox_info_after_third_column', $booking );
			?>
		</div>
	</div>

	<?php wp_nonce_field( 'save-booking', 'yith-wcbk-booking-save-nonce' ); ?>
</div>
