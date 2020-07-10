<?php

/**
 * WC_Bookings_Meta_Boxes.
 */
class WC_Bookings_Meta_Boxes {

	/**
	 * Stores an array of meta boxes we include.
	 *
	 * @var array
	 */
	private $meta_boxes = array();

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->meta_boxes[] = new WC_Bookings_Details_Meta_Box();
		$this->meta_boxes[] = new WC_Bookings_Customer_Meta_Box();
		$this->meta_boxes[] = new WC_Bookings_Save_Meta_Box();
		$this->meta_boxes[] = new WC_Bookable_Resource_Details_Meta_Box();

		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ), 10, 1 );
		add_action( 'admin_menu', array( $this, 'remove_submitdiv' ) );
	}

	/**
	 * Add meta boxes to edit product page.
	 */
	public function add_meta_boxes() {
		foreach ( $this->meta_boxes as $meta_box ) {
			foreach ( $meta_box->post_types as $post_type ) {
				add_meta_box(
					$meta_box->id,
					$meta_box->title,
					array( $meta_box, 'meta_box_inner' ),
					$post_type,
					$meta_box->context,
					$meta_box->priority
				);
			}
		}
	}

	/**
	 * Removes built-in submitdiv meta box.
	 *
	 * The post_status field from submitdiv meta box causing unexpected transition
	 * booking status events.
	 *
	 * @see https://github.com/woocommerce/woocommerce-bookings/issues/1014
	 *
	 * @since 1.10.0
	 *
	 * @version 1.10.0
	 */
	public function remove_submitdiv() {
		remove_meta_box( 'submitdiv', 'wc_booking', 'side' );
	}
}
