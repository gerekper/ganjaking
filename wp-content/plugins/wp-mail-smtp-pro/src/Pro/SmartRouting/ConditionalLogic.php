<?php

namespace WPMailSMTP\Pro\SmartRouting;

use WPMailSMTP\Pro\ConditionalLogic\CanProcessConditionalLogicTrait;
use WPMailSMTP\Pro\WPMailArgs;

/**
 * Class ConditionalLogic.
 *
 * @since 3.7.0
 */
class ConditionalLogic {

	/**
	 * Conditional logic processing trait.
	 *
	 * @since 3.7.0
	 */
	use CanProcessConditionalLogicTrait;

	/**
	 * The `wp_mail` function arguments object.
	 *
	 * @since 3.7.0
	 *
	 * @var WPMailArgs
	 */
	private $wp_mail_args;

	/**
	 * Conditional values.
	 *
	 * @since 3.7.0
	 *
	 * @var array
	 */
	private $conditional_values = [];

	/**
	 * Constructor.
	 *
	 * @since 3.7.0
	 *
	 * @param WPMailArgs $wp_mail_args The `wp_mail` function arguments object.
	 */
	public function __construct( $wp_mail_args ) {

		$this->wp_mail_args = $wp_mail_args;
	}

	/**
	 * Process conditional rules.
	 *
	 * @since 3.7.0
	 *
	 * @param array $conditionals List of conditionals.
	 *
	 * @return bool
	 */
	public function process( $conditionals ) {

		$values = $this->get_values( $conditionals );

		return $this->process_conditionals( $conditionals, $values );
	}

	/**
	 * Get conditionals required values.
	 *
	 * @since 3.7.0
	 *
	 * @param array $conditionals List of conditionals.
	 *
	 * @return array
	 */
	private function get_values( $conditionals ) {

		$values = [];

		foreach ( $conditionals as $group ) {
			foreach ( $group as $rule ) {
				if ( isset( $rule['property'] ) && ! isset( $values[ $rule['property'] ] ) ) {
					$values[ $rule['property'] ] = $this->get_value( $rule['property'] );
				}
			}
		}

		return $values;
	}

	/**
	 * Get property value.
	 *
	 * @since 3.7.0
	 *
	 * @param string $property Property name.
	 *
	 * @return mixed
	 */
	private function get_value( $property ) { // phpcs:ignore Generic.Metrics.CyclomaticComplexity.MaxExceeded, WPForms.PHP.HooksMethod.InvalidPlaceForAddingHooks

		if ( isset( $this->conditional_values[ $property ] ) ) {
			return $this->conditional_values[ $property ];
		}

		$value = '';

		switch ( $property ) {
			case 'subject':
				$value = $this->wp_mail_args->get_subject();
				break;

			case 'message':
				$value = $this->wp_mail_args->get_message();
				break;

			case 'from_email':
				$processor = wp_mail_smtp()->get_processor();

				remove_filter( 'wp_mail_from', [ $processor, 'filter_mail_from_email' ], PHP_INT_MAX );

				/** This filter is documented in wp-includes/pluggable.php. */
				$value = apply_filters( 'wp_mail_from', $this->wp_mail_args->get_from_email() ); // phpcs:ignore WPForms.PHP.ValidateHooks.InvalidHookName

				add_filter( 'wp_mail_from', [ $processor, 'filter_mail_from_email' ], PHP_INT_MAX );
				break;

			case 'from_name':
				$processor = wp_mail_smtp()->get_processor();

				remove_filter( 'wp_mail_from_name', [ $processor, 'filter_mail_from_name' ], PHP_INT_MAX );

				/** This filter is documented in wp-includes/pluggable.php. */
				$value = apply_filters( 'wp_mail_from_name', $this->wp_mail_args->get_from_name() ); // phpcs:ignore WPForms.PHP.ValidateHooks.InvalidHookName

				add_filter( 'wp_mail_from_name', [ $processor, 'filter_mail_from_name' ], PHP_INT_MAX );
				break;

			case 'to_email':
				$value = $this->wp_mail_args->get_to_email();
				break;

			case 'cc':
				$value = $this->wp_mail_args->get_header( 'cc' );
				break;

			case 'bcc':
				$value = $this->wp_mail_args->get_header( 'bcc' );
				break;

			case 'reply_to':
				$value = $this->wp_mail_args->get_header( 'reply-to' );
				break;

			case 'header_name':
				$value = array_keys( $this->wp_mail_args->get_headers() );
				break;

			case 'header_value':
				$value = array_values( $this->wp_mail_args->get_headers() );
				break;

			case 'initiator':
				$initiator = wp_mail_smtp()->get_wp_mail_initiator();

				// Change the initiator to "wp-core" if the email was sent from the reloaded method in the email controls.
				if (
					! empty( $initiator->get_file() ) &&
					strpos( str_replace( '\\', '/', $initiator->get_file() ), 'src/Pro/Emails/Control/Reload.php' ) !== false
				) {
					$value = 'wp-core';
				} else {
					$value = $initiator->get_slug();
				}
				break;
		}

		$this->conditional_values[ $property ] = $value;

		return $value;
	}
}
