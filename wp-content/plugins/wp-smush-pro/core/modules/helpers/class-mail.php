<?php
/**
 * Mail.
 *
 * @package Smush\Core
 */

namespace Smush\Core\Modules\Helpers;

defined( 'ABSPATH' ) || exit;
/**
 * Class Mail
 */
abstract class Mail {
	/**
	 * Identifier
	 *
	 * @var mixed
	 * @access protected
	 */
	protected $identifier;

	/**
	 * Whitelabel class.
	 *
	 * @var Whitelabel
	 */
	protected $whitelabel;

	/**
	 * Constructor.
	 *
	 * @param string $identifier Identifier.
	 */
	public function __construct( $identifier ) {
		$this->identifier = $identifier;
		$this->whitelabel = new WhiteLabel();
	}

	/**
	 * Get first blog admin.
	 *
	 * @return null|WP_User.
	 */
	private function get_blog_admin() {
		$admins = get_users(
			array(
				'role'    => 'administrator',
				'orderby' => 'ID',
				'order'   => 'ASC',
				'limit'   => 1,
			)
		);
		if ( ! empty( $admins ) ) {
			return $admins[0];
		}
	}

	/**
	 * Get user meta data of recipient.
	 *
	 * @return false|WP_User
	 */
	protected function get_recipient_meta() {
		$admin_id = (int) apply_filters( $this->identifier . '_mail_admin_id', 0 );
		if ( $admin_id > 0 ) {
			$user_data = get_user_by( 'id', $admin_id );
			if ( ! empty( $user_data ) ) {
				return $user_data;
			}
		}

		$blog_admin = get_user_by( 'email', get_option( 'admin_email' ) );
		if ( empty( $blog_admin ) ) {
			$blog_admin = $this->get_blog_admin();
		}

		return $blog_admin;
	}

	/**
	 * Get first name of recipient.
	 */
	public function get_recipient_name() {
		$user_data = $this->get_recipient_meta();
		if ( empty( $user_data ) ) {
			return 'Sir';
		}
		return ! empty( $user_data->first_name ) ? $user_data->first_name : $user_data->display_name;
	}

	/**
	 * Get mail recipients.
	 *
	 * @return array
	 */
	public function get_mail_recipients() {
		$recipients = (array) apply_filters( $this->identifier . '_get_mail_recipients', array() );
		if ( ! empty( $recipients ) ) {
			return $recipients;
		}
		$user_data = $this->get_recipient_meta();
		if ( ! empty( $user_data->user_email ) ) {
			$recipients[] = $user_data->user_email;
		}

		if ( empty( $recipients ) ) {
			$recipients[] = get_option( 'admin_email' );
		}
		return $recipients;
	}

	/**
	 * Send an email.
	 */
	public function send_email() {
		$mail_attributes     = array(
			'to'      => $this->get_mail_recipients(),
			'subject' => $this->get_mail_subject(),
			'message' => $this->get_mail_message(),
			'headers' => $this->get_mail_headers(),
		);
		$modified_attributes = apply_filters( $this->identifier . '_mail_attributes', $mail_attributes );
		if ( is_array( $modified_attributes ) && $modified_attributes !== $mail_attributes ) {
			$mail_attributes = wp_parse_args( $modified_attributes, $mail_attributes );
		}

		$sender_email_callback = array( $this, 'custom_sender_email' );
		$sender_name_callback  = array( $this, 'custom_sender_name' );
		$priority              = - 10; // Let other plugins take over

		add_filter( 'wp_mail_from', $sender_email_callback, $priority );
		add_filter( 'wp_mail_from_name', $sender_name_callback, $priority );

		// Send email.
		$sent = wp_mail( $mail_attributes['to'], $mail_attributes['subject'], $mail_attributes['message'], $mail_attributes['headers'] );

		remove_filter( 'wp_mail_from', $sender_email_callback, $priority );
		remove_filter( 'wp_mail_from_name', $sender_name_callback, $priority );

		return $sent;
	}

	/**
	 * Get email header.
	 *
	 * @return array
	 */
	protected function get_mail_headers() {
		return array( 'Content-Type: text/html; charset=UTF-8' );
	}

	/**
	 * Retrieve noreply email.
	 *
	 * @return string
	 */
	public function get_noreply_email() {
		$noreply_email = apply_filters( $this->identifier . '_noreply_email', null );
		if ( $noreply_email && filter_var( $noreply_email, FILTER_VALIDATE_EMAIL ) ) {
			return $noreply_email;
		}
		// Get the site domain and get rid of www.
		$sitename      = wp_parse_url( network_home_url(), PHP_URL_HOST );
		$noreply_email = 'noreply@';

		if ( null !== $sitename ) {
			if ( 'www.' === substr( $sitename, 0, 4 ) ) {
				$sitename = substr( $sitename, 4 );
			}

			$noreply_email .= $sitename;
		}
		return $noreply_email;
	}

	/**
	 * Custom sender email.
	 *
	 * @return string
	 */
	public function custom_sender_email() {
		return $this->get_noreply_email();
	}

	/**
	 * Custom sender name.
	 *
	 * @return string
	 */
	public function custom_sender_name() {
		return $this->get_sender_name();
	}

	/**
	 * Get mail subject.
	 *
	 * @return string
	 */
	abstract protected function get_mail_message();

	/**
	 * Get mail subject.
	 *
	 * @return string
	 */
	abstract protected function get_mail_subject();

	/**
	 * Get sender name.
	 *
	 * @return string
	 */
	abstract protected function get_sender_name();
}
