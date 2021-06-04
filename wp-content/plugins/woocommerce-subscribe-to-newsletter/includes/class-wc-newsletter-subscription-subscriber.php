<?php
/**
 * Newsletter Subscriber
 *
 * This class represents a newsletter subscriber.
 *
 * @package WC_Newsletter_Subscription
 * @since   3.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_Newsletter_Subscription_Subscriber.
 */
class WC_Newsletter_Subscription_Subscriber extends WC_Data {

	/**
	 * Data array.
	 *
	 * @var array
	 */
	protected $data = array(
		'email'      => '',
		'first_name' => '',
		'last_name'  => '',
	);

	/**
	 * Object type.
	 *
	 * @var string
	 */
	protected $object_type = 'newsletter_subscriber';

	/**
	 * Constructor.
	 *
	 * @since 3.0.0
	 *
	 * @param array $subscriber Subscriber data.
	 */
	public function __construct( $subscriber = array() ) {
		parent::__construct( $subscriber );

		$this->set_props( $subscriber );
		$this->set_object_read( true );
	}

	/*
	 * --------------------------------------------------------------------------
	 * Getters
	 * --------------------------------------------------------------------------
	 */

	/**
	 * Gets the subscriber's email.
	 *
	 * @since 3.0.0
	 *
	 * @param string $context View or edit context.
	 * @return string
	 */
	public function get_email( $context = 'view' ) {
		return $this->get_prop( 'email', $context );
	}

	/**
	 * Gets the subscriber's first name.
	 *
	 * @since 3.0.0
	 *
	 * @param string $context View or edit context.
	 * @return string
	 */
	public function get_first_name( $context = 'view' ) {
		return $this->get_prop( 'first_name', $context );
	}

	/**
	 * Gets the subscriber's last name.
	 *
	 * @since 3.0.0
	 *
	 * @param string $context View or edit context.
	 * @return string
	 */
	public function get_last_name( $context = 'view' ) {
		return $this->get_prop( 'last_name', $context );
	}

	/**
	 * Gets the subscriber's full name.
	 *
	 * @since 3.0.0
	 *
	 * @return string
	 */
	public function get_full_name() {
		$first_name = $this->get_first_name();
		$last_name  = $this->get_last_name();

		return trim( $first_name . ' ' . $last_name );
	}

	/*
	 * --------------------------------------------------------------------------
	 * Setters
	 * --------------------------------------------------------------------------
	 */

	/**
	 * Sets the subscriber's email.
	 *
	 * @since 3.0.0
	 *
	 * @param string $email The email.
	 */
	public function set_email( $email ) {
		$this->set_prop( 'email', strtolower( sanitize_email( $email ) ) );
	}

	/**
	 * Sets the subscriber's first name.
	 *
	 * @since 3.0.0
	 *
	 * @param string $first_name The first name.
	 */
	public function set_first_name( $first_name ) {
		$this->set_prop( 'first_name', sanitize_text_field( $first_name ) );
	}

	/**
	 * Sets the subscriber's last name.
	 *
	 * @since 3.0.0
	 *
	 * @param string $last_name The last name.
	 */
	public function set_last_name( $last_name ) {
		$this->set_prop( 'last_name', sanitize_text_field( $last_name ) );
	}
}
