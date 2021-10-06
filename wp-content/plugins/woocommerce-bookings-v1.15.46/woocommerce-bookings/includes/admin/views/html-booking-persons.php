<div id="bookings_persons" class="woocommerce_options_panel panel wc-metaboxes-wrapper">

	<div class="options_group" id="persons-options">

		<?php
		woocommerce_wp_text_input(
			array(
				'id'                => '_wc_booking_min_persons_group',
				'label'             => esc_html__( 'Min persons', 'woocommerce-bookings' ),
				'description'       => esc_html__( 'The minimum number of persons per booking.', 'woocommerce-bookings' ),
				'value'             => $bookable_product->get_min_persons( 'edit' ),
				'desc_tip'          => true,
				'type'              => 'number',
				'custom_attributes' => array(
					'min'  => '0',
					'step' => '1',
				),
			)
		);
		?>

		<?php
		woocommerce_wp_text_input(
			array(
				'id'                => '_wc_booking_max_persons_group',
				'label'             => esc_html__( 'Max persons', 'woocommerce-bookings' ),
				'description'       => esc_html__( 'The maximum number of persons per booking.', 'woocommerce-bookings' ),
				'value'             => $bookable_product->get_max_persons( 'edit' ) ? $bookable_product->get_max_persons( 'edit' ) : '',
				'desc_tip'          => true,
				'type'              => 'number',
				'custom_attributes' => array(
					'min'  => '0',
					'step' => '1',
				),
			)
		);
		?>

		<?php
		woocommerce_wp_checkbox(
			array(
				'id'          => '_wc_booking_person_cost_multiplier',
				'label'       => esc_html__( 'Multiply all costs by person count', 'woocommerce-bookings' ),
				'description' => esc_html__( 'Enable this to multiply the entire cost of the booking (block and base costs) by the person count.', 'woocommerce-bookings' ),
				'desc_tip'    => true,
				'value'       => $bookable_product->get_has_person_cost_multiplier( 'edit' ) ? 'yes' : 'no',
			)
		);
		?>

		<?php
		woocommerce_wp_checkbox(
			array(
				'id'          => '_wc_booking_person_qty_multiplier',
				'label'       => esc_html__( 'Count persons as bookings', 'woocommerce-bookings' ),
				'description' => esc_html__( 'Enable this to count each person as a booking until the max bookings per block (in availability) is reached.', 'woocommerce-bookings' ),
				'desc_tip'    => true,
				'value'       => $bookable_product->get_has_person_qty_multiplier( 'edit' ) ? 'yes' : 'no',
			)
		);
		?>

		<?php
		woocommerce_wp_checkbox(
			array(
				'id'          => '_wc_booking_has_person_types',
				'label'       => esc_html__( 'Enable person types', 'woocommerce-bookings' ),
				'description' => esc_html__( 'Person types allow you to offer different booking costs for different types of individuals, for example, adults and children.', 'woocommerce-bookings' ),
				'desc_tip'    => true,
				'value'       => $bookable_product->get_has_person_types( 'edit' ) ? 'yes' : 'no',
			)
		);
		?>

	</div>

	<div class="options_group" id="persons-types">
		<div class="toolbar">
			<h3><?php esc_html_e( 'Person types', 'woocommerce-bookings' ); ?></h3>
			<span class="toolbar_links"><a href="#" class="close_all"><?php esc_html_e( 'Close all', 'woocommerce-bookings' ); ?></a><a href="#" class="expand_all"><?php esc_html_e( 'Expand all', 'woocommerce-bookings' ); ?></a></span>
		</div>

		<div class="woocommerce_bookable_persons wc-metaboxes">

			<?php
			$person_types = $bookable_product->get_person_types( 'edit' );

			if ( 0 === count( $person_types ) ) {
				echo '<div id="message" class="inline woocommerce-message" style="margin: 1em 0;">';
					echo '<div class="squeezer">';
						echo '<h4>' . esc_html__( 'Person types allow you to offer different booking costs for different types of individuals, for example, adults and children.', 'woocommerce-bookings' ) . '</h4>';
					echo '</div>';
				echo '</div>';
			}

			if ( $person_types ) {
				$loop = 0;

				foreach ( $person_types as $person_type ) {
					include 'html-booking-person.php';
					$loop++;
				}
			}
			?>
		</div>

		<p class="toolbar">
			<button type="button" class="button button-primary add_person"><?php esc_html_e( 'Add Person Type', 'woocommerce-bookings' ); ?></button>
		</p>
	</div>
</div>
