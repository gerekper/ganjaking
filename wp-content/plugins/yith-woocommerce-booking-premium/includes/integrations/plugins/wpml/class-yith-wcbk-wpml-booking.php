<?php
/**
 * Class YITH_WCBK_Wpml_Booking
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking
 */

defined( 'YITH_WCBK' ) || exit;

/**
 * Class YITH_WCBK_Wpml_Booking
 *
 * @since   2.1.28
 */
class YITH_WCBK_Wpml_Booking {
	/**
	 * Single intance of the class.
	 *
	 * @var YITH_WCBK_Wpml_Booking
	 */
	private static $instance;

	/**
	 * WPML Integration instance.
	 *
	 * @var YITH_WCBK_Wpml_Integration
	 */
	public $wpml_integration;

	/**
	 * The previous language.
	 *
	 * @var string
	 */
	private $previous_language;

	/**
	 * The meta key where store the WPML language.
	 *
	 * @var string
	 */
	const LANGUAGE_KEY = 'wpml_language';

	/**
	 * Singleton implementation
	 *
	 * @param YITH_WCBK_Wpml_Integration $wpml_integration WPML Integration instance.
	 *
	 * @return YITH_WCBK_Wpml_Booking
	 */
	public static function get_instance( $wpml_integration ) {
		return ! is_null( self::$instance ) ? self::$instance : self::$instance = new static( $wpml_integration );
	}

	/**
	 * Constructor
	 *
	 * @param YITH_WCBK_Wpml_Integration $wpml_integration WPML Integration instance.
	 */
	private function __construct( $wpml_integration ) {
		$this->wpml_integration = $wpml_integration;

		add_filter( 'yith_wcbk_booking_get_title', array( $this, 'translate_booking_title' ), 10, 2 );
		add_filter( 'yith_wcbk_booking_details_product_title', array( $this, 'translate_booking_product_title' ), 10, 2 );

		add_action( 'yith_wcbk_email_before_sending', array( $this, 'switch_language_to_translate_email' ), 10, 2 );
		add_action( 'yith_wcbk_email_after_sending', array( $this, 'switch_language_after_translating_email' ), 10 );
		add_action( 'woocommerce_email_get_option', array( $this, 'filter_emails_strings' ), 10, 4 );

		add_action( 'yith_wcbk_before_booking_object_save', array( $this, 'set_current_language_before_saving' ) );
		add_filter( 'yith_wcbk_pre_get_bookings_initial_args', array( $this, 'translate_product_id_when_retrieving_bookings' ), 10, 1 );
	}

	/**
	 * Translate the product ID when retrieving Bookings.
	 *
	 * @param array $args Arguments.
	 *
	 * @return array
	 */
	public function translate_product_id_when_retrieving_bookings( $args ) {
		if ( ! empty( $args['product_id'] ) ) {
			$args['product_id'] = apply_filters( 'yith_wcbk_booking_product_id_to_translate', $args['product_id'] );
		}

		return $args;
	}

	/**
	 * Switch language to translate the email.
	 *
	 * @param YITH_WCBK_Email $email             The email.
	 * @param bool            $is_customer_email Customer email flag.
	 */
	public function switch_language_to_translate_email( $email, $is_customer_email ) {
		$booking = $email->object;
		if ( $is_customer_email ) {
			$language = $this->get_booking_language( $booking );
			if ( $language ) {
				$current_language = $this->wpml_integration->sitepress->get_current_language();

				if ( $language !== $current_language ) {
					$this->previous_language = $current_language;
					$this->wpml_integration->sitepress->switch_lang( $language );

					$email->set_default_params();
				};
			}
		}

	}

	/**
	 * Switch language to the previous language.
	 */
	public function switch_language_after_translating_email() {
		if ( $this->previous_language ) {
			$this->wpml_integration->sitepress->switch_lang( $this->previous_language );
			$this->previous_language = '';
		}
	}

	/**
	 * Translate Booking Title
	 *
	 * @param string            $title   The title.
	 * @param YITH_WCBK_Booking $booking The booking.
	 */
	public function translate_booking_title( $title, $booking ) {

		$product_id = $this->wpml_integration->get_current_language_id( $booking->get_product_id() );
		if ( absint( $product_id ) !== absint( $booking->get_product_id() ) ) {
			$product = wc_get_product( $product_id );
			if ( $product ) {
				$title = sprintf( '#%s %s', $booking->get_id(), $product->get_title() );
			}
		}

		return $title;
	}

	/**
	 * Translate Booking Product Title
	 *
	 * @param string            $title   The title.
	 * @param YITH_WCBK_Booking $booking The booking.
	 */
	public function translate_booking_product_title( $title, $booking ) {
		$product_id = $this->wpml_integration->get_current_language_id( $booking->get_product_id() );
		if ( absint( $product_id ) !== absint( $booking->get_product_id() ) ) {
			$product = wc_get_product( $product_id );

			if ( $product ) {
				$title = $product->get_title();
			}
		}

		return $title;
	}

	/**
	 * Set the current language before saving the booking.
	 *
	 * @param YITH_WCBK_Booking $booking The booking.
	 *
	 * @return void
	 * @since  4.6.1
	 */
	public function set_current_language_before_saving( $booking ) {
		if ( ! $booking->get_meta( self::LANGUAGE_KEY ) ) {
			$current_language = $this->wpml_integration->sitepress->get_current_language();
			$booking->add_meta_data( self::LANGUAGE_KEY, $current_language, true );
		}
	}

	/**
	 * Get the booking language set in the booking or in the related order.
	 *
	 * @param YITH_WCBK_Booking $booking The booking.
	 *
	 * @return string
	 * @since  4.6.1
	 */
	protected function get_booking_language( $booking ) {
		$language = $booking->get_meta( self::LANGUAGE_KEY );
		if ( ! $language && $booking->get_order() ) {
			$language = $booking->get_order()->get_meta( 'wpml_language' );
		}

		return $language;
	}

	/**
	 * Filter email strings to translate them.
	 *
	 * @param string   $value     The value.
	 * @param WC_Email $email     The email.
	 * @param string   $old_value The old value.
	 * @param string   $key       The key.
	 *
	 * @return mixed
	 */
	public function filter_emails_strings( $value, $email, $old_value, $key ) {
		if ( $email instanceof YITH_WCBK_Email && $email->is_customer_email() && $email->object ) {
			$email_id             = $email->id;
			$options_to_translate = array( 'custom_message' );
			if ( in_array( $key, $options_to_translate, true ) ) {
				$domain   = "admin_texts_woocommerce_{$email_id}_settings";
				$name     = '[woocommerce_' . $email_id . '_settings]' . $key;
				$language = $this->get_booking_language( $email->object );

				$translated_value = apply_filters( 'wpml_translate_single_string', false, $domain, $name, $language );
				if ( $translated_value ) {
					$value = $translated_value;
				}
			}
		}

		return $value;
	}
}
