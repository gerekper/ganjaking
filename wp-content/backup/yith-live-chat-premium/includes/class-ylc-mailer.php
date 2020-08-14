<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'YLC_Mailer' ) ) {

	class YLC_Mailer {

		/**
		 * @var string from email address
		 */
		protected $from;

		/**
		 * @var string to email address
		 */
		protected $to;

		/**
		 * @var string email subject
		 */
		protected $subject;

		/**
		 * @var string email body
		 */
		protected $message;

		/**
		 * @var string reply to address
		 */
		protected $reply_to;

		/**
		 * @var string from name
		 */
		protected $from_name;

		/**
		 * Constructor
		 *
		 * @since   1.4.0
		 *
		 * @param $from      string
		 * @param $to        string
		 * @param $subject   string
		 * @param $message   string
		 * @param $reply_to  string
		 * @param $from_name string
		 *
		 * @return  void
		 * @author  Alberto Ruggiero
		 */
		public function __construct( $from, $to, $subject, $message, $reply_to, $from_name = '' ) {

			$this->from      = $from;
			$this->to        = $to;
			$this->subject   = $subject;
			$this->message   = $message;
			$this->reply_to  = $reply_to;
			$this->from_name = $from_name;

		}

		/**
		 * Send email
		 *
		 * @since   1.4.0
		 * @return  boolean
		 * @author  Alberto Ruggiero
		 */
		public function send() {

			add_filter( 'wp_mail_from', array( $this, 'get_from_address' ) );
			add_filter( 'wp_mail_from_name', array( $this, 'get_from_name' ) );
			add_filter( 'wp_mail_content_type', array( $this, 'get_content_type' ) );

			$return = wp_mail( $this->to, $this->subject, $this->message, $this->set_headers() );

			remove_filter( 'wp_mail_from', array( $this, 'get_from_address' ) );
			remove_filter( 'wp_mail_from_name', array( $this, 'get_from_name' ) );
			remove_filter( 'wp_mail_content_type', array( $this, 'get_content_type' ) );

			return $return;

		}

		/**
		 * Set headers
		 *
		 * @since   1.4.0
		 * @return  array
		 * @author  Alberto Ruggiero
		 */
		public function set_headers() {

			$headers   = array();
			$headers[] = 'content-type: text/html';
			$headers[] = 'charset=utf-8';
			$headers[] = 'Reply-To: ' . $this->reply_to;

			return apply_filters( 'ylc_headers_filter', $headers );

		}

		/**
		 * Get the from name for outgoing emails.
		 *
		 * @since   1.4.0
		 * @return  string
		 * @author  Alberto Ruggiero
		 */
		public function get_from_name() {
			return wp_specialchars_decode( esc_html( $this->from_name ), ENT_QUOTES );
		}

		/**
		 * Get the from address for outgoing emails.
		 *
		 * @since   1.4.0
		 * @return  string
		 * @author  Alberto Ruggiero
		 */
		public function get_from_address() {
			return sanitize_email( $this->from );
		}

		/**
		 * Get email content type.
		 *
		 * @since   1.4.0
		 * @return  string
		 * @author  Alberto Ruggiero
		 */
		public function get_content_type() {

			return 'text/html';

		}

	}

}