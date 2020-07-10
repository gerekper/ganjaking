<?php

/**
 * WC_Bookable_Resource_Details_Meta_Box.
 */
class WC_Bookable_Resource_Details_Meta_Box {

	/**
	 * Meta box ID.
	 *
	 * @var string
	 */
	public $id;

	/**
	 * Meta box title.
	 *
	 * @var string
	 */
	public $title;

	/**
	 * Meta box context.
	 *
	 * @var string
	 */
	public $context;

	/**
	 * Meta box priority.
	 *
	 * @var string
	 */
	public $priority;

	/**
	 * Meta box post types.
	 * @var array
	 */
	public $post_types;

	/**
	 * Is meta boxes saved once?
	 *
	 * @var boolean
	 */
	private static $saved_meta_box = false;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->id         = 'woocommerce-bookable-resource-data';
		$this->title      = __( 'Resource details', 'woocommerce-bookings' );
		$this->context    = 'normal';
		$this->priority   = 'high';
		$this->post_types = array( 'bookable_resource' );
		add_action( 'save_post', array( $this, 'meta_box_save' ), 10, 2 );
	}

	/**
	 * Show meta box.
	 */
	public function meta_box_inner( $post ) {
		$post_id  = $post->ID;
		$resource = new WC_Product_Booking_Resource( $post_id );
		wp_enqueue_script( 'wc_bookings_admin_js' );
		wp_nonce_field( 'bookable_resource_details_meta_box', 'bookable_resource_details_meta_box_nonce' );
		?>
		<style type="text/css">
			#minor-publishing-actions, #visibility { display:none; }
		</style>
		<div class="woocommerce_options_panel woocommerce">
			<div class="panel-wrap" id="bookings_availability">
				<div class="options_group">
					<?php
						woocommerce_wp_text_input( array(
							'id'                => '_wc_booking_qty',
							'label'             => __( 'Available Quantity', 'woocommerce-bookings' ),
							'description'       => __( 'The quantity of this resource available at any given time.', 'woocommerce-bookings' ),
							'value'             => max( $resource->get_qty( 'edit' ), 1 ),
							'desc_tip'          => true,
							'type'              => 'number',
							'custom_attributes' => array(
								'min'  => '',
								'step' => '1',
							),
							'style'             => 'width: 50px;',
						) );
					?>
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
									<th><?php esc_html_e( 'Bookable', 'woocommerce-bookings' ); ?>&nbsp;<a class="tips" data-tip="<?php echo wc_sanitize_tooltip( __( 'If not bookable, users won\'t be able to choose this block for their booking.', 'woocommerce-bookings' ) ); ?>">[?]</a></th>
									<th><?php esc_html_e( 'Priority', 'woocommerce-bookings' ); ?>&nbsp;<a class="tips" data-tip="<?php echo wc_sanitize_tooltip( get_wc_booking_priority_explanation() ); ?>">[?]</a></th>
									<th class="remove" width="1%">&nbsp;</th>
								</tr>
							</thead>
							<tfoot>
								<tr>
									<th colspan="6">
										<a href="#" class="button add_row" data-row="<?php
										ob_start();
										include( 'views/html-booking-availability-fields.php' );
										$html = ob_get_clean();
										echo esc_attr( $html );
										?>"><?php esc_html_e( 'Add Range', 'woocommerce-bookings' ); ?></a>
										<span class="description"><?php echo esc_html( get_wc_booking_rules_explanation() ); ?></span>
									</th>
								</tr>
							</tfoot>
							<tbody id="availability_rows">
								<?php
								$values = $resource->get_availability( 'edit' );
								if ( ! empty( $values ) && is_array( $values ) ) {
									foreach ( $values as $availability ) {
										include( 'views/html-booking-availability-fields.php' );
									}
								}
								?>
							</tbody>
						</table>
					</div>
				</div>
				<div class="clear"></div>
			</div>
		</div>
		<?php
	}

	/**
	 * Get posted availability fields and format.
	 *
	 * @return array
	 */
	private function get_posted_availability() {
		$availability = array();
		$row_size     = isset( $_POST['wc_booking_availability_type'] ) ? sizeof( $_POST['wc_booking_availability_type'] ) : 0;
		for ( $i = 0; $i < $row_size; $i ++ ) {
			$availability[ $i ]['type']     = wc_clean( $_POST['wc_booking_availability_type'][ $i ] );
			$availability[ $i ]['bookable'] = wc_clean( $_POST['wc_booking_availability_bookable'][ $i ] );
			$availability[ $i ]['priority'] = intval( $_POST['wc_booking_availability_priority'][ $i ] );

			switch ( $availability[ $i ]['type'] ) {
				case 'custom':
					$availability[ $i ]['from'] = wc_clean( $_POST['wc_booking_availability_from_date'][ $i ] );
					$availability[ $i ]['to']   = wc_clean( $_POST['wc_booking_availability_to_date'][ $i ] );
					break;
				case 'months':
					$availability[ $i ]['from'] = wc_clean( $_POST['wc_booking_availability_from_month'][ $i ] );
					$availability[ $i ]['to']   = wc_clean( $_POST['wc_booking_availability_to_month'][ $i ] );
					break;
				case 'weeks':
					$availability[ $i ]['from'] = wc_clean( $_POST['wc_booking_availability_from_week'][ $i ] );
					$availability[ $i ]['to']   = wc_clean( $_POST['wc_booking_availability_to_week'][ $i ] );
					break;
				case 'days':
					$availability[ $i ]['from'] = wc_clean( $_POST['wc_booking_availability_from_day_of_week'][ $i ] );
					$availability[ $i ]['to']   = wc_clean( $_POST['wc_booking_availability_to_day_of_week'][ $i ] );
					break;
				case 'time':
				case 'time:1':
				case 'time:2':
				case 'time:3':
				case 'time:4':
				case 'time:5':
				case 'time:6':
				case 'time:7':
					$availability[ $i ]['from'] = wc_booking_sanitize_time( $_POST['wc_booking_availability_from_time'][ $i ] );
					$availability[ $i ]['to']   = wc_booking_sanitize_time( $_POST['wc_booking_availability_to_time'][ $i ] );
					break;
				case 'time:range':
				case 'custom:daterange':
					$availability[ $i ]['from'] = wc_booking_sanitize_time( $_POST['wc_booking_availability_from_time'][ $i ] );
					$availability[ $i ]['to']   = wc_booking_sanitize_time( $_POST['wc_booking_availability_to_time'][ $i ] );

					$availability[ $i ]['from_date'] = wc_clean( $_POST['wc_booking_availability_from_date'][ $i ] );
					$availability[ $i ]['to_date']   = wc_clean( $_POST['wc_booking_availability_to_date'][ $i ] );
					break;
			}
		}
		return $availability;
	}

	/**
	 * Save handler.
	 *
	 * @param  int     $post_id
	 * @param  WP_Post $post
	 */
	public function meta_box_save( $post_id, $post ) {
		if ( ! isset( $_POST['bookable_resource_details_meta_box_nonce'] ) || ! wp_verify_nonce( $_POST['bookable_resource_details_meta_box_nonce'], 'bookable_resource_details_meta_box' ) ) {
			return $post_id;
		}
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}
		// Check the post being saved == the $post_id to prevent triggering this call for other save_post events
		if ( empty( $_POST['post_ID'] ) || intval( $_POST['post_ID'] ) !== $post_id ) {
			return $post_id;
		}
		if ( ! in_array( $post->post_type, $this->post_types ) ) {
			return $post_id;
		}
		if ( self::$saved_meta_box ) {
			return $post_id;
		}

		// We need this save event to run once to avoid potential endless loops. This would have been perfect:
		// remove_action( current_filter(), __METHOD__ );
		// But cannot be used due to https://github.com/woocommerce/woocommerce/issues/6485
		// When that is patched in core we can use the above. For now:
		self::$saved_meta_box = true;

		$resource = new WC_Product_Booking_Resource( $post_id );
		$resource->set_props( array(
			'qty'          => wc_clean( $_POST['_wc_booking_qty'] ),
			'availability' => $this->get_posted_availability(),
		) );
		$resource->save();
	}
}
