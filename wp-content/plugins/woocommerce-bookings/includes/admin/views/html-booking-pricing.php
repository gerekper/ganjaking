<div id="bookings_pricing" class="panel woocommerce_options_panel">
	<div class="options_group">

		<?php
		woocommerce_wp_text_input( array(
			'id'                => '_wc_booking_cost',
			'label'             => __( 'Base cost', 'woocommerce-bookings' ),
			'description'       => __( 'One-off cost for the booking as a whole.', 'woocommerce-bookings' ),
			'value'             => $bookable_product->get_cost( 'edit' ),
			'type'              => 'number',
			'desc_tip'          => true,
			'custom_attributes' => array(
				'min'  => '',
				'step' => '0.01',
			),
		) );
		?>

		<?php do_action( 'woocommerce_bookings_after_booking_base_cost', $post->ID ); ?>

		<?php
		woocommerce_wp_text_input( array(
			'id'                => '_wc_booking_block_cost',
			'label'             => __( 'Block cost', 'woocommerce-bookings' ),
			'description'       => __( 'This is the cost per block booked. All other costs (for resources and persons) are added to this.', 'woocommerce-bookings' ),
			'value'             => $bookable_product->get_block_cost( 'edit' ),
			'type'              => 'number',
			'desc_tip'          => true,
			'custom_attributes' => array(
				'min'  => '',
				'step' => '0.01',
			),
		) );
		?>

		<?php do_action( 'woocommerce_bookings_after_booking_block_cost', $post->ID ); ?>

		<?php
		woocommerce_wp_text_input( array(
			'id'                => '_wc_display_cost',
			'label'             => __( 'Display cost', 'woocommerce-bookings' ),
			'description'       => __( 'The cost is displayed to the user on the frontend. Leave blank to have it calculated for you. If a booking has varying costs, this will be prefixed with the word "from:".', 'woocommerce-bookings' ),
			'value'             => $bookable_product->get_display_cost( 'edit' ),
			'type'              => 'number',
			'desc_tip'          => true,
			'custom_attributes' => array(
				'min'  => '',
				'step' => '0.01',
			),
		) );
		?>

		<?php do_action( 'woocommerce_bookings_after_display_cost', $post->ID ); ?>
	</div>
	<div class="options_group">
		<div class="table_grid">
			<table class="widefat">
				<thead>
					<tr>
						<th class="sort" width="1%">&nbsp;</th>
						<th><?php esc_html_e( 'Range type', 'woocommerce-bookings' ); ?></th>
						<th><?php esc_html_e( 'Range', 'woocommerce-bookings' ); ?></th>
						<th></th>
						<th></th>
						<th><?php esc_html_e( 'Base cost', 'woocommerce-bookings' ); ?>&nbsp;<a class="tips" data-tip="<?php echo wc_sanitize_tooltip( __( 'Enter a cost for this rule. Applied to the booking as a whole.', 'woocommerce-bookings' ) ); ?>">[?]</a></th>
						<th><?php esc_html_e( 'Block cost', 'woocommerce-bookings' ); ?>&nbsp;<a class="tips" data-tip="<?php echo wc_sanitize_tooltip( __( 'Enter a cost for this rule. Applied to each booking block.', 'woocommerce-bookings' ) ); ?>">[?]</a></th>
						<th class="remove" width="1%">&nbsp;</th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<th colspan="9">
							<a href="#" class="button add_row" data-row="<?php
							ob_start();
							include 'html-booking-pricing-fields.php';
							$html = ob_get_clean();
							echo esc_attr( $html );
							?>"><?php esc_html_e( 'Add Range', 'woocommerce-bookings' ); ?></a>
							<span class="description"><?php esc_html_e( 'All matching rules will be applied to the booking.', 'woocommerce-bookings' ); ?></span>
						</th>
					</tr>
				</tfoot>
				<tbody id="pricing_rows">
					<?php
					$values = $bookable_product->get_pricing( 'edit' );
					if ( ! empty( $values ) && is_array( $values ) ) {
						foreach ( $values as $index => $pricing ) {
							include 'html-booking-pricing-fields.php';

							/**
							 * Fired just after pricing fields are rendered.
							 *
							 * @since 1.7.4
							 *
							 * @param array $pricing {
							 * The pricing details for bookings
							 *
							 * @type string $type          The booking range type
							 * @type string $from          The start value for the range
							 * @type string $to            The end value for the range
							 * @type string $modifier      The arithmetic modifier for block cost
							 * @type string $cost          The booking block cost
							 * @type string $base_modifier The arithmetic modifier for base cost
							 * @type string $base_cost     The base cost
							 * }
							 */
							do_action( 'woocommerce_bookings_pricing_fields', $pricing );
						}
					}
					?>
				</tbody>
			</table>
		</div>

		<?php do_action( 'woocommerce_bookings_after_bookings_pricing', $post->ID ); ?>

	</div>
</div>
