<?php
/**
 * Label class.
 *
 * @package WC_Stamps_Integration
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Represent a single label (a post of wc_stamps_label).
 */
class WC_Stamps_Label {

	/**
	 * Label ID (post ID).
	 *
	 * @since 1.0.0
	 *
	 * @var int
	 */
	private $id;

	/**
	 * URL of label.
	 *
	 * Stored as post content.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private $url;

	/**
	 * Tracking number of the label.
	 *
	 * Stored as post title.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private $tracking_number;

	/**
	 * Constructor.
	 *
	 * Set internal props for this label.
	 *
	 * @param int $label_id Label ID / Post ID of CPT wc_stamps_label.
	 */
	public function __construct( $label_id ) {
		$label = get_post( $label_id );

		if ( $label && 'wc_stamps_label' === $label->post_type ) {
			$this->id              = $label_id;
			$this->tracking_number = $label->post_title;
			$this->url             = $label->post_content;
		}
	}

	/**
	 * See if label is valid.
	 *
	 * Valid label contains post ID and URL (post_content).
	 *
	 * @return bool Returns true if label is valid.
	 */
	public function is_valid() {
		return ! empty( $this->id ) && ! empty( $this->url );
	}

	/**
	 * Magic method to check if a given label contains meta data specified
	 * by `$key`.
	 *
	 * @param string $key Key name.
	 *
	 * @return bool Returns true if a given `$key` exists in the post meta.
	 */
	public function __isset( $key ) {
		return metadata_exists( 'post', $this->id, $key );
	}

	/**
	 * Magic method to retrieve a given label meta / post meta.
	 *
	 * @param string $key Key name.
	 *
	 * @return mixed
	 */
	public function __get( $key ) {
		return get_post_meta( $this->id, $key, true );
	}

	/**
	 * Get value.
	 *
	 * Same as `__get()`.
	 *
	 * @param string $key Key name.
	 *
	 * @return mixed
	 */
	public function get_value( $key ) {
		return get_post_meta( $this->id, $key, true );
	}

	/**
	 * Get ID of the label.
	 *
	 * @return int Label ID.
	 */
	public function get_id() {
		return $this->id;
	}

	/**
	 * Get the URL to the label.
	 *
	 * @return string Label URL.
	 */
	public function get_label_url() {
		return $this->url;
	}

	/**
	 * Get the tracking number.
	 *
	 * @return string Label tracking number.
	 */
	public function get_tracking_number() {
		return $this->tracking_number;
	}

	/**
	 * Get the excerpted tracking number.
	 *
	 * @see https://github.com/woocommerce/woocommerce-shipping-stamps/issues/63.
	 *
	 * @since 1.3.3
	 * @version 1.3.3
	 *
	 * @param int    $length Number of chars to return from start.
	 * @param string $suffix Suffix to add to the substr'ed string.
	 *
	 * @return string
	 */
	public function get_tracking_number_excerpt( $length = 16, $suffix = '...' ) {
		if ( strlen( $this->tracking_number ) > $length ) {
			return substr( $this->tracking_number, 0, $length ) . $suffix;
		}
		return $this->tracking_number;
	}
}
