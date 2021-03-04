<?php

/**
 * FUE_Sending_Mailer class
 *
 * Handles the sending of emails
 */
class FUE_Sending_Mailer {

	/**
	 * @var Follow_Up_Emails $fue
	 */
	public $fue;

	/**
	 * @var FUE_Sending_Email_Variables $vars
	 */
	public $variables;

	/**
	 * @var object Row from followup_email_orders
	 */
	private $queue;

	/**
	 * @var FUE_Email
	 */
	private $email;

	const MULTIPART_BOUNDARY = 'fue_email';

	/**
	 * Class constructor
	 *
	 * @param Follow_Up_Emails $fue
	 * @param FUE_Sending_Email_Variables $variables
	 */
	public function __construct( Follow_Up_Emails $fue, FUE_Sending_Email_Variables $variables ) {
		$this->fue          = $fue;
		$this->variables    = $variables;
	}

	/**
	 * Set the proper DKIM values if enabled
	 * @param PHPMailer $phpmailer
	 */
	public static function set_dkim( $phpmailer ) {
		$dkim = get_option( 'fue_dkim', array() );
		$dkim_defaults = array(
			'enabled'       => false,
			'domain'        => '',
			'selector'      => '',
			'identity'      => '',
			'passphrase'    => '',
			'public_key'    => '',
			'private_key'   => '',
			'key_size'      => ''
		);
		$dkim = wp_parse_args( $dkim, $dkim_defaults );

		if ( $dkim['enabled'] ) {
			$phpmailer->DKIM_selector   = $dkim['selector'];
			$phpmailer->DKIM_domain     = $dkim['domain'];
			$phpmailer->DKIM_private    = get_option( 'fue_dkim_hash_file', '' );
			$phpmailer->DKIM_passphrase = $dkim['passphrase'];
			$phpmailer->DKIM_identity   = $dkim['identity'];
		}
	}

	/**
	 * Process a specific item from the queue
	 *
	 * @param FUE_Sending_Queue_Item $queue_item A row from wp_followup_email_orders
	 * @param bool $force_send Set to TRUE to send email regardless of the scheduled send date
	 * @return bool|WP_Error TRUE if the email was sent or a WP_Error object if an error occured
	 */
	public function send_queue_item( $queue_item, $force_send = false ) {
		$wpdb = Follow_Up_Emails::instance()->wpdb;

		// if a queue item doesn't have an email_id, it needs to have either a
		// subscription_notification or daily_summary meta key
		if ( empty( $queue_item->email_id ) ) {

			if ( isset( $queue_item->meta['daily_summary'] ) ) {
				if ( 'no' == get_option( 'fue_enable_daily_summary' ) ) {
					$error = __( 'Queue item deleted because daily summary is not enabled', 'follow_up_emails' );
					fue_debug_log( $error, $queue_item->id );
					return new WP_Error( 'fue_queue_error', $error );
				}

				$last_send = get_option( 'fue_daily_summary_last_send', 0 );
				$now       = current_time( 'timestamp', true );

				if ( ( $now - $last_send ) < 86000 ) {
					$error = __( 'Queue item deleted because the daily summary is attempting to send too early', 'follow_up_emails' );
					fue_debug_log( $error, $queue_item->id );
					return new WP_Error( 'fue_queue_error', $error );
				}

				return $this->send_adhoc_email( $queue_item );

				update_option( 'fue_daily_summary_last_send', $now );
			} elseif ( isset( $queue_item->meta['adhoc'] ) || isset( $queue_item->meta['subscription_notification'] ) ) {
				if ( isset( $queue_item->meta['adhoc'] ) ) {
					fue_debug_log( __( 'Sending adhoc email', 'follow_up_emails' ), $queue_item->id );
				} else {
					fue_debug_log( __( 'Sending subscription notification email', 'follow_up_emails' ), $queue_item->id );
				}
				return $this->send_adhoc_email( $queue_item );
			} else {
				// invalid queue item. delete!
				Follow_Up_Emails::instance()->scheduler->delete_item( $queue_item->id );
				$error = __( 'Queue item deleted because no email was assigned to it', 'follow_up_emails' );
				fue_debug_log( $error, $queue_item->id );
				return new WP_Error( 'fue_queue_error', $error );
			}
		}

		// only process unsent email orders
		if ( $queue_item->is_sent != 0 ) {
			$error = __( 'Queue item has already been sent', 'follow_up_emails' );
			fue_debug_log( $error, $queue_item->id );
			return new WP_Error( 'fue_queue_error', $error );
		}

		$email = new FUE_Email( $queue_item->email_id );

		// Make sure that the email is not disabled.
		// Since 4.4.11, Inactive emails that are in the queue shall still be sent.
		if (
			!in_array( $email->status, array( FUE_Email::STATUS_ACTIVE, FUE_Email::STATUS_INACTIVE ) ) &&
			$email->type != 'manual'
		) {
			$error = __( 'Could not send email because it is not active', 'follow_up_emails' );
			fue_debug_log( $error, $queue_item->id );
			return new WP_Error( 'fue_queue_error', $error );
		}

		// set local variables
		$this->email = $email;
		$this->queue = $queue_item;

		// email cannot be active and have an empty message
		if ( empty( $email->message ) && empty( $queue_item->meta['message'] ) ) {
			// set the status to inactive
			$email->update_status( FUE_Email::STATUS_INACTIVE );
			$error = __( 'Cannot send emails without a message. Email has been deactivated', 'follow_up_emails' );
			fue_debug_log( $error, $queue_item->email_id );
			return new WP_Error( 'fue_queue_error', $error );
		}

		// test for email conditions prior to sending
		$passed_conditions = apply_filters( 'fue_queue_item_filter_conditions_before_sending', true, $queue_item );

		if ( is_wp_error( $passed_conditions ) ) {
			fue_debug_log( $passed_conditions->get_error_message(), $queue_item->id );
			return new WP_Error( 'fue_send_queue_item', $passed_conditions->get_error_message() );
		}

		// if the queue item is cron locked, reschedule to avoid duplicate emails being sent
		if ( FUE_Sending_Scheduler::is_queue_item_locked( $queue_item ) ) {
			FUE_Sending_Scheduler::reschedule_queue_item( $queue_item );
			$error = __( 'This email is locked due to a previous attempt in sending the email. Sending has been rescheduled to avoid duplicate emails.', 'follow_up_emails' );
			fue_debug_log( $error, $queue_item->id );
			return new WP_Error( 'fue_queue_notice', $error );
		}

		// place a cron lock to prevent duplicates
		FUE_Sending_Scheduler::lock_email_queue( $queue_item );

		$email_data = $this->get_email_data( $queue_item, $email );

		// remove from queue if the user chose to not receive emails
		// non-order related emails
		if ( $queue_item->user_id > 0 && $queue_item->order_id == 0 && fue_user_opted_out( $queue_item->user_id ) ) {

			Follow_Up_Emails::instance()->scheduler->delete_item( $queue_item->id );
			$error = __( 'The customer opted out of recieving non-order related emails. Email has been deleted.', 'follow_up_emails' );
			fue_debug_log( $error, $queue_item->id );
			return new WP_Error( 'fue_queue_notice', $error );

		}

		// do not send if the recipient's email is on the excludes list
		if (
			fue_is_email_excluded( $email_data['email_to'] ) ||
			( $queue_item->order_id > 0 && fue_is_email_excluded( $email_data['email_to'], 0, $queue_item->order_id ) )
		) {

			do_action( 'fue_email_excluded', $email_data['email_to'], $queue_item->id );
			Follow_Up_Emails::instance()->scheduler->delete_item( $queue_item->id );
			$error = __( 'The customer is on the exclude emails list. Email deleted.', 'follow_up_emails' );
			fue_debug_log( $error, $queue_item->id );
			return new WP_Error( 'fue_queue_notice', $error );

		}

		// allow other extensions to "skip" sending this email
		$skip = apply_filters( 'fue_skip_email_sending', false, $email, $queue_item );

		if ( $skip ) {
			FUE_Sending_Scheduler::remove_queue_item_lock( $queue_item );
			$error = __( 'An add-on has marked this email to be skipped.', 'follow_up_emails' );
			fue_debug_log( $error, $queue_item->id );
			return new WP_Error( 'fue_queue_notice', $error );
		}

		$email_data['subject'] = apply_filters( 'fue_email_subject', $email_data['subject'], $email, $queue_item );
		$email_data['message'] = apply_filters( 'fue_email_message', $email_data['message'], $email, $queue_item );

		$email_data = $this->process_variable_replacements( $email_data );

		// hook to variable replacement
		$email_data['subject'] = apply_filters( 'fue_send_email_subject', $email_data['subject'], $queue_item );
		$email_data['message'] = apply_filters( 'fue_send_email_message', $email_data['message'], $queue_item );

		// look for store_url with path
		$link_meta = array(
			'email_order_id' => $queue_item->id,
			'email_id'       => $email->id,
			'user_id'        => $email_data['user_id'],
			'user_email'     => $email_data['email_to'],
			'codes'          => self::get_tracking_codes( $queue_item, $email ),
		);
		fue_set_link_meta( $link_meta );

		// apply the email template
		if ( !empty( $email->template ) ) {
			$email_data['message'] = $email->apply_template( $email_data['message'] );
		}

		require_once FUE_INC_DIR . '/lib/fue-utils/class-fue-autolink.php';

		$email_data['message']  = $this->process_link_variables( $email_data, $queue_item, $email );
		$email_data['message']  = $this->add_tracking_to_uris( $email_data['message'] );
		$email_data['message']  = FUE_Autolink::link( $email_data['message'] );

		$headers = apply_filters( 'fue_email_headers', array(
			$this->get_bcc_header(),
			$this->get_reply_to_header()
		), $email, $queue_item );

		$site_id = FUE_Newsletter::get_site_id();
		$headers[] = "X-FUE: $site_id";
		$headers[] = "X-FUE-EMAIL: {$email->id}";
		$headers[] = "X-FUE-QUEUE-ID: {$queue_item->id}";


	    if ( !empty( $email_data['unsubscribe'] ) ) {
		    $headers[] = "List-Unsubscribe: <{$email_data['unsubscribe']}>";
	    }

		// bounce handling
		$fue_bounce = new FUE_Bounce_Handler();
		if ( $fue_bounce->settings['handle_bounces'] && is_email( $fue_bounce->settings['email']) ) {
			$headers[] = "Return-Path: <{$fue_bounce->settings['email']}>";
		}

		add_filter( 'wp_mail_from', array( $this, 'get_wp_email_from' ), 20 );
		add_filter( 'wp_mail_from_name', array( $this, 'get_wp_email_from_name' ), 20 );

		// send the email
		do_action( 'fue_before_email_send', $email_data['subject'], $email_data['message'], $headers, $queue_item );

		// Decode html entities to UTF-8 (http://php.net/html_entity_decode#104617)
		if ( function_exists('mb_convert_encoding') ) {
			$email_data['subject'] = preg_replace_callback("/(&#[0-9]+;)/", array( $this, 'convert_encoding' ), $email_data['subject']);
		}

		// allow plugins to take care of the sending
		// plugins that override the sending must return TRUE to stop FUE from sending this queue item
		$sent = apply_filters(
			'fue_send_queue_item',
			false,
			$queue_item,
			$email_data,
			$headers
		);

		// return if an error occured
		if ( is_wp_error( $sent ) ) {
			FUE_Sending_Scheduler::remove_queue_item_lock( $queue_item );
			fue_debug_log( $sent->get_error_message(), $queue_item->id );
			return $sent;
		}

		if ( !$sent ) {
			$email_data = apply_filters( 'fue_before_sending_email', $email_data, $email, $queue_item );
			self::mail( $email_data['email_to'], $email_data['subject'], $email_data['message'], $headers );
		}

		do_action( 'fue_after_email_sent', $email_data['subject'], $email_data['message'], $headers, $queue_item );

		$this->log_sent_email( $email_data );

		// increment usage count
		fue_update_email( array(
			'id'            => $email->id,
			'usage_count'   => $email->usage_count + 1
		) );

		// update the email order
		$now = get_date_from_gmt( date('Y-m-d H:i:s'), 'Y-m-d H:i:s' );
		$wpdb->update(
			$wpdb->prefix .'followup_email_orders',
			array(
				'is_sent'       => 1,
				'date_sent'     => $now,
				'email_trigger' => $email->get_trigger_string()
			),
			array(
				'id' => $queue_item->id
			)
		);

		do_action( 'fue_email_order_sent', $queue_item->id );
		do_action(
			'fue_email_sent_details',
			$queue_item,
			$queue_item->user_id,
			$email,
			$email_data['email_to'],
			$email_data['cname'],
			$email->get_trigger_string()
		);

		// remove queue lock
		FUE_Sending_Scheduler::remove_queue_item_lock( $queue_item );

		// if this is a 'date' email and there are no more unsent emails
		// of the same kind in the queue, archive it
		self::maybe_archive_email( $email );

		return true;
	}

	/**
	 * Return the email body after the variable replacements have been done
	 *
	 * @param array     $email_data
	 * @param FUE_Email $email
	 *
	 * @return string
	 */
	public function get_email_preview_html( $email_data, $email ) {
		// set local variables
		$this->email    = $email;
		$this->queue    = null;

		$email_data = $this->process_variable_replacements( $email_data, null, $email );

		// look for links
		$email_data['message'] = preg_replace('|\{link url=([^}]+)\}|', '$1', $email_data['message']);

		// look for store_url with path
		$email_data['message'] = preg_replace_callback('|\{store_url=([^}]+)\}|', 'FUE_Sending_Mailer::add_test_store_url', $email_data['message']);

		// apply the email template
		$email_data['message'] = $email->apply_template( $email_data['message'] );

		// Include CSS rules for text and image alignment.
		$email_data['message'] .= '<style>' . self::get_html_email_css() . '</style>';

		$email_data = apply_filters( 'fue_before_sending_email', $email_data, $email, null );

		return $email_data['message'];
	}

	/**
	 * Return the email body after the variable replacements have been done
	 *
	 * @param FUE_Sending_Queue_Item $queue_item
	 *
	 * @return string
	 */
	public function get_email_web_version( $queue_item ) {
		$email = new FUE_Email( $queue_item->email_id );

		// set local variables
		$this->email    = $email;
		$this->queue    = $queue_item;

		$email_data = $this->get_email_data( $queue_item, $email );

		$email_data['message']  = apply_filters('fue_email_message', $email_data['message'], $email, $queue_item);

		$email_data = $this->process_variable_replacements( $email_data );

		// hook to variable replacement
		$email_data['subject']  = apply_filters( 'fue_send_email_subject', $email_data['subject'], $queue_item );
		$email_data['message']  = apply_filters( 'fue_send_email_message', $email_data['message'], $queue_item );

		// look for store_url with path
		$link_meta = array(
			'email_order_id'    => $queue_item->id,
			'email_id'          => $email->id,
			'user_id'           => $email_data['user_id'],
			'user_email'        => $email_data['email_to'],
			'codes'             => self::get_tracking_codes( $queue_item, $email )
		);
		fue_set_link_meta( $link_meta );

		$email_data['message']  = $this->add_tracking_to_uris( $email_data['message'] );
		$email_data['message']  = $this->process_link_variables( $email_data, $queue_item, $email );

		// apply the email template
		if ( !empty( $email->template ) ) {
			$email_data['message'] = $email->apply_template( $email_data['message'] );
		}

		// remove pixel tracking for web version views
		remove_filter( 'fue_before_sending_email', array( $GLOBALS['fue_reports'], 'inject_pixel_tracker' ), 30, 3 );
		$email_data = apply_filters( 'fue_before_sending_email', $email_data, $email, $queue_item );

		return $email_data['message'];
	}

	/**
	 * Send a test email that mocks what a real FUE Email would look like
	 *
	 * @param array     $email_data Look at @see FUE_Sending_Mailer::get_email_data() for the array structure
	 * @param FUE_Email $email
	 */
	public function send_test_email( $email_data, $email ) {

		// set local variables
		$this->email    = $email;
		$this->queue    = null;

		$email_data = $this->process_variable_replacements( $email_data, null, $email );

		$headers = apply_filters( 'fue_email_headers', array(
			$this->get_bcc_header(),
			$this->get_reply_to_header()
		), $email, null );

		// look for links
		//$replacer   = new FUE_Sending_Link_Replacement( $email_order->id, $email->id, $user_id, $email_to );
		$email_data['message']    = preg_replace('|\{link url=([^}]+)\}|', '$1', $email_data['message']);

		// look for store_url with path
		$email_data['message']    = preg_replace_callback('|\{store_url=([^}]+)\}|', 'FUE_Sending_Mailer::add_test_store_url', $email_data['message']);

		// Decode html entities to UTF-8 (http://php.net/html_entity_decode#104617)
		if ( function_exists('mb_convert_encoding') ) {
			$email_data['subject'] = preg_replace_callback("/(&#[0-9]+;)/", array( $this, 'convert_encoding' ), $email_data['subject']);
		}

		add_filter( 'wp_mail_from', array( $this, 'get_wp_email_from' ), 20 );
		add_filter( 'wp_mail_from_name', array( $this, 'get_wp_email_from_name' ), 20 );

		// apply the email template
		$email_data['message'] = $email->apply_template( $email_data['message'] );

		$email_data = apply_filters( 'fue_before_sending_email', $email_data, $email, null );

		do_action( 'fue_before_test_email_send', $email_data['subject'], $email_data['message'] );

		self::mail( $email_data['email_to'], $email_data['subject'], $email_data['message'], $headers );

		do_action( 'fue_after_test_email_sent', $email_data['subject'], $email_data['message'] );
		fue_debug_log( __( 'Sent test email', 'follow_up_emails' ), $email_data );

		die("OK");
	}

	/**
	 * Send email using WP's mailer. If WooCommerce is installed, WooCommerce's mailer
	 * will be used unless WPMandrill is installed and configured
	 *
	 * @param string $to
	 * @param string $subject
	 * @param string $message
	 * @param string|array $headers
	 * @param string $attachments
	 */
	public static function mail($to, $subject, $message, $headers = '', $attachments = '') {

		// inject CSS rules for text and image alignment
		$css = self::get_html_email_css();

		require_once FUE_INC_DIR .'/lib/class-emogrifier.php';

		$emogrifier = new Emogrifier( $message, $css );
		$message = $emogrifier->emogrify();

		// Send through Mandrill if WP Mandrill is installed
		if ( class_exists('wpMandrill') ) {
			$site_url_parts = parse_url( get_bloginfo('url') );
			$ga_campaign    = (defined('FUE_GA_CAMPAIGN')) ? FUE_GA_CAMPAIGN : '';
			$ga_domain      = ($ga_campaign) ? array($site_url_parts['host']) : array();
			$settings       = get_option( 'wpmandrill', array() );

			$from_name  = Follow_Up_Emails::instance()->mailer->get_email_from_name();
			$from_email = Follow_Up_Emails::instance()->mailer->get_email_from_address();

			if ( empty( $from_name ) ) {
				$from_name = $settings['from_name'];
			}

			if ( empty( $from_email ) ) {
				$from_email = $settings['from_username'];
			}

			if (! empty( $ga_campaign ) ) {
				$ga_campaign = json_decode( $ga_campaign );
			}

			wpMandrill::mail(
				$to, $subject, $message, $headers, $attachments,
				array(), // tags
				$from_name, // from_name
				$from_email, // from_email
				'', // template_name
				true, // track_opens
				true, // track_clicks
				false, // url_strip_qs
				true, // merge
				array(), // global_merge_vars
				array(), // merge_vars
				$ga_domain,
				$ga_campaign
			);
		} else {
			// allow add-ons to use their own mailers
			$method  = apply_filters( 'fue_mail_method', 'wp_mail' );
			$staging = get_option( 'fue_staging', 'no' );

			if ( 'yes' === $staging ) {
				return;
			}

			add_filter( 'wp_mail_content_type', 'FUE_Sending_Mailer::set_html_content_type', 1000 );
			add_filter( 'wp_mail', 'FUE_Sending_Mailer::set_wp_mail_message', 10, 1 );

			// if 'wp_mail' is still the mailer, see if Mandrill is available
			if ( $method == 'wp_mail' ) {
				wp_mail($to, $subject, $message, $headers, $attachments);
			} else {
				call_user_func( $method, $to, $subject, $message, $headers, $attachments );
			}

			remove_filter( 'wp_mail', 'FUE_Sending_Mailer::set_wp_mail_message', 10, 1 );
			remove_filter( 'wp_mail_content_type', 'FUE_Sending_Mailer::set_html_content_type', 1000 );
		}
	}

	/**
	 * Format wp_mail message depending if we use multipart or not.
	 *
	 * @param  array $args
	 * @return array
	 */
	public static function set_wp_mail_message( $args ) {
		if ( apply_filters( 'fue_multipart_mail', false ) ) {
			$args['message'] = self::format_multipart_message( $args['message'] );
		}

		return $args;
	}

	/**
	 * Format multipart message given html.
	 *
	 * @param  string $message_html
	 * @return string
	 */
	protected static function format_multipart_message( $message_html ) {
		$message_plain = $message_html;

		// Strip HTML tags.
		$message_plain = strip_tags( $message_plain, '<br>' );
		$message_plain = preg_replace( '#(\n|\r|\r\n)#i', '', $message_plain );

		// Cleanup the message as it may have a lot of tabs due to the strip tags above
		$message_plain = trim( preg_replace( '#\t+#i', "\t", $message_plain ) );

		// Convert <br> tags to newlines.
		$message_plain = trim( preg_replace( '#<br\s*/?>#i', "\n", $message_plain ) );

		$mails = array(
			'text/plain' => $message_plain,
			'text/html'  => $message_html,
		);

		$mails = array_map( function( $k, $v ) {
			$message  = "\r\n\r\n--" . self::MULTIPART_BOUNDARY . "\r\n";
			$message .= "Content-Type: " . $k . ";charset=utf-8\r\n\r\n";

			// Lines should not exceed 1000 chars.
			// See https://tools.ietf.org/html/rfc5322#section-2.1.1.
			$message .= wordwrap( $v, 998, "\r\n" );

			return $message;
		}, array_keys( $mails ), $mails );

		return implode( $mails ) . "\r\n\r\n--" . self::MULTIPART_BOUNDARY . '--';
	}

	/**
	 * Sets the content-type header to HTML
	 * @return string
	 */
	public static function set_html_content_type() {
		return apply_filters( 'fue_multipart_mail', false ) ? 'multipart/alternative; boundary=' . self::MULTIPART_BOUNDARY : 'text/html';
	}

	/**
	 * Apply CSS to emails for basic formatting
	 *
	 * Whitespaces needed to be removed to fix an unknown
	 * bug that's causing the CSS to be output
	 *
	 * @return string
	 */
	public static function get_html_email_css() {
		$css = apply_filters('fue_email_inline_css', '.alignleft{float:left;margin:5px 20px 5px 0;}.alignright{float:right;margin:5px 0 5px 20px;}.aligncenter{display:block;margin:5px auto;}img.alignnone{margin:5px 0;}'.
			   'blockquote,q{quotes:none;}blockquote:before,blockquote:after,q:before,q:after{content:"";content:none;}'.
			   'blockquote{font-size:24px;font-style:italic;font-weight:300;margin:24px 40px;}'.
			   'blockquote blockquote{margin-right:0;}blockquote cite,blockquote small{font-size:14px;font-weight:normal;text-transform:uppercase;}'.
			   'cite{border-bottom:0;}abbr[title]{border-bottom:1px dotted;}address{font-style:italic;margin:0 0 24px;}'.
			   'del{color:#333;}ins{background:#fff9c0;border:none;color:#333;text-decoration:none;}'.
			   'sub,sup{font-size:75%;line-height:0;position:relative;vertical-align:baseline;}'.
			   'sup{top:-0.5em;}sub{bottom:-0.25em;}');

		return $css;
	}

	/**
	 * Archive a Date FUE_Email if there are no more emails of the same
	 * kind that's unsent in the queue.
	 *
	 * The logic behind the archiving being that because it is date-based
	 * and have passed its sending date already, there will be no
	 * more new emails that can be created using this FUE_Email
	 *
	 * @param FUE_Email $email
	 */
	public static function maybe_archive_email( $email ) {

		if ( $email->interval_type != 'date' || $email->type == 'manual' ) {
			return;
		}

		// if there are no more unsent emails in the queue, archive this email
		$items = Follow_Up_Emails::instance()->scheduler->get_items( array(
			'email_id'  => $email->id,
			'is_sent'   => 0
		) );

		if ( count( $items ) == 0 ) {
			$email->update_status( FUE_Email::STATUS_ARCHIVED );
		}
	}

	public static function create_email_url( $email_order_id, $email_id, $user_id, $user_email, $target_page, $tracking_codes = array() ) {
		if ( empty( $tracking_codes ) ) {
			$tracking_codes = self::get_tracking_codes( $email_order_id, $email_id );
		}

		// append tracking code to URLs
		$target_page = add_query_arg( $tracking_codes, $target_page );

		$args = apply_filters( 'fue_create_email_url', array(
			'oid'            => $email_order_id,
			'eid'            => $email_id,
			'user_id'        => $user_id,
			'user_email'     => $user_email,
			'next'           => $target_page,
		));

		$payload = base64_encode( http_build_query( $args, '', '&' ) );

		return add_query_arg( 'sfn_data', $payload, add_query_arg( 'sfn_trk', 1, get_bloginfo( 'url' ) ) );
	}

	public function add_store_url( $matches ) {
		if ( empty($matches) ) return '';

		$store_url  = home_url( $matches[1] );
		$meta       = fue_get_link_meta();

		// convert urls
		$store_url  = self::create_email_url( $meta['email_order_id'], $meta['email_id'], $meta['user_id'], $meta['user_email'], $store_url, $meta['codes'] );

		return $store_url;
	}

	public static function add_test_store_url( $matches ) {
		if ( empty($matches) ) return '';

		$store_url  = home_url( $matches[1] );

		return $store_url;
	}

	/**
	 * Override the FROM header before sending out emails
	 * @param string $email_from
	 * @return string
	 */
	public function get_wp_email_from_name( $email_from ) {
		$name = $this->get_email_from_name();

		if ( !empty( $name ) ) {
			$email_from = $name;
		}

		return $email_from;
	}

	/**
	 * Override the FROM header before sending out emails
	 * @param string $email_from
	 * @return string
	 */
	public function get_wp_email_from( $email_from ) {
		$from = $this->get_email_from_address();

		if ( !empty( $from ) ) {
			$email_from = $from;
		}

		return $email_from;
	}

	/**
	 * Send a queue item that has no linked follow-up email.
	 * Used in sending failed subscription payment notifications and daily summary emails
	 *
	 * @param FUE_Sending_Queue_Item $queue_item
	 * @return bool|WP_Error
	 */
	public function send_adhoc_email( $queue_item ) {
		$wpdb = Follow_Up_Emails::instance()->wpdb;

		if ( !empty( $queue_item->meta['email'] ) ) {
			$recipient_email = $queue_item->meta['email'];
		} else {
			$recipient_email = $queue_item->user_email;
		}

		$subject = apply_filters( 'fue_adhoc_email_subject', $queue_item->meta['subject'], $queue_item );
		$message = apply_filters( 'fue_adhoc_email_message', $queue_item->meta['message'], $queue_item );

		if ( empty( $message ) ) {
			$wpdb->update(
				$wpdb->prefix .'followup_email_orders',
				array(
					'status' => -1,
				),
				array(
					'id' => $queue_item->id
				)
			);
			do_action( 'fue_adhoc_email_sent', $queue_item );

			return new WP_Error( 'fue_queue_error', __('Skipped sending because there was no message to be sent', 'follow_up_emails' ) );
		}

		$site_id         = FUE_Newsletter::get_site_id();
		$unsubscribe_url = $this->get_unsubscribe_page_url();

		$headers = apply_filters( 'fue_adhoc_email_headers', array(
			"X-FUE: $site_id",
			"X-FUE-QUEUE-ID: {$queue_item->id}",
			"List-Unsubscribe: <{$unsubscribe_url}>"
		), null, $queue_item );

		self::mail( $recipient_email, $subject, $message, $headers );

		// update the email order
		$now = get_date_from_gmt( date('Y-m-d H:i:s'), 'Y-m-d H:i:s' );
		$wpdb->update(
			$wpdb->prefix .'followup_email_orders',
			array(
				'is_sent'       => 1,
				'date_sent'     => $now
			),
			array(
				'id' => $queue_item->id
			)
		);

		do_action( 'fue_adhoc_email_sent', $queue_item );

		return true;
	}

	/**
	 * Get data for a queue item that are used for sending the email
	 *
	 * @param object    $queue_item
	 * @param FUE_Email $email
	 * @return array
	 */
	private function get_email_data( $queue_item, $email ) {

		$data = array(
			'username'      => '',
			'first_name'    => '',
			'last_name'     => '',
			'cname'         => '',
			'user_id'       => $queue_item->user_id,
			'email_to'      => $queue_item->user_email,
			'tracking_codes'=> array(),
			'store_url'     => home_url(),
			'store_url_secure' => home_url(null, 'https'),
			'store_name'    => get_bloginfo('name'),
			'unsubscribe'   => $this->get_unsubscribe_page_url(),
			'web_url'       => $queue_item->get_web_version_url(),
			'subject'       => $email->subject,
			'message'       => $email->message,
			'meta'          => array()
		);

		if ( $email->type == 'manual' ) {
			$data['meta']     = maybe_unserialize( $queue_item->meta );
			$data['email_to'] = $data['meta']['email_address'];
			$data['subject']  = $data['meta']['subject'];
			$data['message']  = $data['meta']['message'];

			if ( !empty( $data['meta'][2] ) ) {
				$data['cname']    = $data['meta'][2];
			} else if ( ! empty( $data['meta']['user_name'] ) ) {
				$data['cname']    = $data['meta']['user_name'];
			}
		}

		// If we have a user email, attempt to fetch existing subscriber
		if ( ! empty( $queue_item->user_email ) ) {
			$newsletter = new FUE_Newsletter();
			$subscriber = $newsletter->get_subscriber( $queue_item->user_email );

			if ( $subscriber ) {
				$data['cname']      = $subscriber['first_name'] . ' ' . $subscriber['last_name'];
				$data['first_name'] = $subscriber['first_name'];
				$data['last_name']  = $subscriber['last_name'];
			}
		}

		if ( $queue_item->user_id == 0 ) {
			$data['username'] = '';
			$data['order']    = false;
		} else {
			$data['order']        = false;
			$data['user_id']      = $queue_item->user_id;

			$wp_user    = new WP_User( $queue_item->user_id );

			$data['username'] = $wp_user->user_login;

			// use the customer's billing data
			$billing_email      = get_user_meta( $queue_item->user_id, 'billing_email', true );
			$billing_first_name = get_user_meta( $queue_item->user_id, 'billing_first_name', true );
			$billing_last_name  = get_user_meta( $queue_item->user_id, 'billing_last_name', true );

			$order_email        = get_post_meta( $queue_item->order_id, '_billing_email', true );
			$order_first_name   = get_post_meta( $queue_item->order_id, '_billing_first_name', true );
			$order_last_name    = get_post_meta( $queue_item->order_id, '_billing_last_name', true );

			// if the customer's billing data are empty, fallback to using data from WP_User
			$data['email_to']   = $billing_email      ?: ( $order_email      ?: $wp_user->user_email );
			$data['first_name'] = $billing_first_name ?: ( $order_first_name ?: $wp_user->first_name );
			$data['last_name']  = $billing_last_name  ?: ( $order_last_name  ?: $wp_user->last_name );

			// customer's complete name
			$data['cname'] = $data['first_name'] .' '. $data['last_name'];

			// if the name is still empty, last resort is to use the display_name
			if ( empty($data['first_name']) && empty($data['last_name']) ) {
				$data['first_name'] = $wp_user->display_name;
				$data['cname']      = $wp_user->display_name;
			}

		}

		$data['tracking_codes'] = self::get_tracking_codes( $queue_item, $email );
		$data['campaigns']      = $this->get_mandrill_campaigns( $data['tracking_codes'] );

		$data = apply_filters( 'fue_send_email_data', $data, $queue_item, $email );

		// convert urls
		$data['store_url']          = self::create_email_url( $queue_item->id, $email->id, $data['user_id'], $data['email_to'], $data['store_url'], $data['tracking_codes'] );
		$data['store_url_secure']   = self::create_email_url( $queue_item->id, $email->id, $data['user_id'], $data['email_to'], $data['store_url_secure'], $data['tracking_codes'] );
		$data['unsubscribe']        = self::create_email_url( $queue_item->id, $email->id, $data['user_id'], $data['email_to'], $data['unsubscribe'], $data['tracking_codes'] );

		return $data;
	}

	/**
	 * Process all variable replacements
	 *
	 * @param array                     $email_data
	 * @param FUE_Sending_Queue_Item    $queue_item
	 * @param FUE_Email                 $email
	 * @return array
	 */
	private function process_variable_replacements( $email_data, $queue_item = null, $email = null ) {
		$variables = clone $this->variables;

		if ( is_null( $queue_item ) ) {
			$queue_item = $this->queue;
		}

		if ( is_null( $email ) ) {
			$email = $this->email;
		}

		// define a constant that WPMandrill can pickup and use later
		if ( !empty( $email_data['campaigns'] ) )
			define( 'FUE_GA_CAMPAIGN', json_encode( array_unique( $email_data['campaigns'] ) ) );

		// default variable replacements
		$replacements = array(
			'store_url'             => fue_replacement_url_var( $email_data['store_url'] ),
			'store_url_secure'      => fue_replacement_url_var( $email_data['store_url_secure'] ),
			'store_name'            => $email_data['store_name'],
			'unsubscribe_url'       => fue_replacement_url_var( $email_data['unsubscribe'] ),
			'customer_username'     => $email_data['username'],
			'customer_first_name'   => $email_data['first_name'],
			'customer_last_name'    => $email_data['last_name'],
			'customer_name'         => $email_data['cname'],
			'customer_email'        => $email_data['email_to'],
			'webversion_url'        => '#'
		);

		if ( $queue_item ) {
			$webversion = $queue_item->get_web_version_url();
			$replacements['webversion_url'] = $webversion;
		}

		$replacements['webversion_link'] = '<a class="webversion" href="'. $replacements['webversion_url'] .'">'. __('View in browser', 'follow_up_emails') .'</a>';

		$variables->register( $replacements );

		if ( $email && $email->type == 'manual' ) {
			if ( !is_null( $queue_item ) ) {
				$meta = maybe_unserialize( $queue_item->meta );
			} else {
				// test email
				$meta = array(
					'customer_username'     => 'johndoe',
					'customer_name'         => 'John Doe'
				);
			}

			$manual_variables = array(
				'customer_username' => (isset($meta['username'])) ? $meta['username'] : $replacements['customer_username'],
				'customer_name'     => !empty( $meta['user_name'] ) ? $meta['user_name'] : $replacements['customer_name']
			);
			$variables->register( $manual_variables );

		}

		$email_data['message']  = do_shortcode( $email_data['message'] );

		// allow plugins to register their variables before applying the replacements
		do_action( 'fue_before_variable_replacements', $variables, $email_data, $email, $queue_item );

		$email_data['subject']  = $variables->apply_replacements( $email_data['subject'] );
		$email_data['message']  = $variables->apply_replacements( $email_data['message'] );

		// look for custom fields
		$email_data['message']  = preg_replace_callback('|\{cf ([0-9]+) ([^}]*)\}|', 'fue_add_custom_fields', $email_data['message'] );

		// look for post id
		$email_data['message']  = preg_replace_callback('|\{post_id=([^}]+)\}|', 'fue_add_post', $email_data['message'] );

		return $email_data;
	}

	/**
	 * Add tracking data to the URIs in $message
	 *
	 * @param string    $message
	 * @return string
	 */
	protected function add_tracking_to_uris( $message ) {
		libxml_use_internal_errors(true);

		$dom = new DOMDocument('1.0', 'UTF-8');

		// Prepend XML with the proper encoding to force DOMDocument to treat the $message as UTF-8
		// From: http://stackoverflow.com/questions/8218230/php-domdocument-loadhtml-not-encoding-utf-8-correctly
		if ( function_exists( 'mb_convert_encoding' ) ) {
			$message = mb_convert_encoding( $message, 'HTML-ENTITIES', 'UTF-8' );
		}

		$dom->loadHTML( $message );

		$xpath  = new DOMXPath( $dom );
		$nodes  = $xpath->query('//a/@href');

		if ( empty( $nodes ) ) {
			return $message;
		}

		$meta = fue_get_link_meta();

		foreach ( $nodes as $href ) {
			$url = $href->nodeValue;

			if ( strpos( $url, 'mailto:' ) !== false ) {
				continue;
			}

			if ( strpos( $url, 'sfn_trk=1' ) !== false ) {
				continue;
			}

			// remove double http:// - tinymce automatically adds one if the entered url
			// is not a proper URL as is the case for variables
			$url = str_replace( 'http://http://', 'http://', $url );

			// convert urls
			$url = self::create_email_url( $meta['email_order_id'], $meta['email_id'], $meta['user_id'], $meta['user_email'], $url, $meta['codes'] );
			$href->nodeValue = "";
			$href->appendChild($dom->createTextNode($url));
		}

		$message = $dom->saveHTML();

		return $message;
	}

	/**
	 * Replace {link...} and {store} variables with actual links
	 * @param array     $email_data
	 * @param object    $queue
	 * @param FUE_Email $email
	 * @return array The modified $email_data
	 */
	protected function process_link_variables( $email_data, $queue, $email ) {
		// look for links
		$replacer               = new FUE_Sending_Link_Replacement( $queue->id, $email->id, $email_data['user_id'], $email_data['email_to'] );
		$email_data['message']  = preg_replace_callback('|\{link url=([^}]+)\}|', array($replacer, 'replace'), $email_data['message'] );
		$codes                  = self::get_tracking_codes( $queue, $email );

		$email_data['message']  = preg_replace_callback('|\{store_url=([^}]+)\}|', array( 'FUE_Sending_Mailer', 'add_store_url'), $email_data['message'] );

		return $email_data['message'];
	}

	/**
	 * Get the BCC header string
	 * @return string
	 */
	private function get_bcc_header() {
		$bcc    = $this->get_email_cc();
		$header = '';

		if ( $bcc ) {
			$header = "Bcc: $bcc\r\n";
		}

		return $header;
	}

	/**
	 * Get the proper CC/BCC value
	 *
	 * @return string
	 */
	private function get_email_cc() {
		$headers    = array();
		$email      = $this->email;
		$email_meta = ($email) ? maybe_unserialize( $email->meta ) : array();
		$cc         = '';

		$global_bcc = get_option('fue_bcc', '');

		$email_bcc  = isset( $email_meta['bcc'] )
						? $email_meta['bcc']
						: false;

		if ( $email_bcc ) {
			$cc = $email_bcc;
		} elseif ( !empty($global_bcc) && is_email( $global_bcc ) ) {
			$cc = $global_bcc;
		}

		return $cc;
	}

	/**
	 * Get the FROM header string
	 * @return string
	 */
	private function get_from_header() {
		$from_name  = $this->get_email_from_name();
		$from       = $this->get_email_from_address();
		$header     = '';

		if ( $from ) {
			$header = "From: $from_name <$from>\r\n";
		}

		return $header;
	}

	/**
	 * Get the Reply-To header string
	 * @return string
	 */
	private function get_reply_to_header() {
		$reply_to_name  = $this->get_email_from_name();
		$reply_to       = $this->get_email_from_address();
		$header         = '';

		if ( $reply_to ) {
			$header = "Reply-To: $reply_to_name <$reply_to>\r\n";
		}

		return $header;
	}

	/**
	 * Get FROM and REPLY-TO name
	 * @return string
	 */
	private function get_email_from_name() {
		$email      = $this->email;
		$email_meta = maybe_unserialize( $email->meta );
		$from       = '';

		$global_from = get_option('fue_from_name', '');
		$types_from  = get_option('fue_from_name_types', array());

		$email_from  = (isset($email_meta['from_name']))
			? $email_meta['from_name']
			: false;

		if ( $email_from ) {
			$from = $email_from;
		} elseif ( !empty($types_from[$email->type]) ) {
			$from = $types_from[$email->type];
		} elseif ( !empty($global_from) && is_email( $global_from ) ) {
			$from = $global_from;
		}

		if ( empty( $from ) ) {
			$from = get_bloginfo('name');
		}

		// Decode html entities to UTF-8 (http://php.net/html_entity_decode#104617)
		if ( function_exists('mb_convert_encoding') ) {
			$from = preg_replace_callback("/(&#[0-9]+;)/", array( $this, 'convert_encoding' ), $from);
		}

		return $from;
	}

	/**
	 * Get the appropriate From and Reply-To headers for the current email
	 * @return string
	 */
	private function get_email_from_address() {
		$email      = $this->email;
		$email_meta = maybe_unserialize( $email->meta );
		$from       = '';

		$global_from = get_option('fue_from_email', '');
		$types_from  = get_option('fue_from_email_types', array());

		$email_from  = (isset($email_meta['from_address']) && is_email($email_meta['from_address']))
			? $email_meta['from_address']
			: false;

		if ( $email_from ) {
			$from = $email_from;
		} elseif ( !empty($types_from[$email->type]) ) {
			$from = $types_from[$email->type];
		} elseif ( !empty($global_from) && is_email( $global_from ) ) {
			$from = $global_from;
		}

		if ( empty( $from ) ) {
			if ( Follow_Up_Emails::instance()->is_woocommerce_installed() ) {
				$from = get_option('woocommerce_email_from_address');
			} else {
				$from = get_bloginfo('admin_email');
			}
		}

		return $from;
	}

	/**
	 * Log a sent email
	 * @param $email_data
	 */
	private function log_sent_email( $email_data ) {
		$email = $this->email;
		$queue = $this->queue;

		// log this email
		$email_trigger = $email->get_trigger_string();

		$log = array(
			'email_id'          => $email->id,
			'email_order_id'    => $queue->id,
			'user_id'           => $queue->user_id,
			'email_name'        => $email->name,
			'date_sent'         => current_time('mysql'),
			'customer_name'     => $email_data['cname'],
			'email_address'     => $email_data['email_to'],
			'order_id'          => $queue->order_id,
			'product_id'        => $queue->product_id,
			'email_trigger'     => $email_trigger
		);

		FUE_Reports::email_log_array( $log );
		fue_debug_log( __( 'Sent email', 'follow_up_emails' ), $log );
	}

	/**
	 * Parse tracking string into arrays and merge them into a single array
	 *
	 * @param int|object    $queue_item Queue item object or ID
	 * @param int|FUE_Email $email      Email object or ID
	 *
	 * @return array
	 */
	private static function get_tracking_codes( $queue_item, $email) {
		if ( ! is_object( $queue_item ) || ! is_object( $email ) ) {
            $queue_item = new FUE_Sending_Queue_Item( $queue_item );
			$email      = new FUE_Email( $email );
		}

		$tracking   = $email->tracking;
		$codes      = array();

		if ( !empty($tracking) ) {
			$tracking = ltrim( $tracking, '?' );
			parse_str( $tracking, $codes );

			foreach ( $codes as $key => $val ) {
				$codes[$key] = urlencode($val);
			}
		}

		// merge tracking/campaign inserted through the queue item
		$queue_item_meta = maybe_unserialize( $queue_item->meta );

		if ( isset( $queue_item_meta['codes']) && !empty( $queue_item_meta['codes'] ) ) {
			foreach ( $queue_item_meta['codes'] as $key => $val ) {
				$codes[$key] = urlencode($val);
			}
		}

		return $codes;
	}

	/**
	 * Get an array of GA campaigns from the tracking code
	 *
	 * @param array $codes
	 * @return array
	 */
	private function get_mandrill_campaigns( $codes ) {
		$campaigns  = array();

		foreach ( $codes as $key => $value ) {

			if ( $key == 'utm_campaign' && class_exists('wpMandrill') ) {
				$campaigns[] = $value;
			}

		}

		return $campaigns;
	}

	/**
	 * Get the URL of the unsubscribe page
	 *
	 * @return bool|string
	 */
	private function get_unsubscribe_page_url() {
		return fue_get_unsubscribe_url();
	}

	/**
	 * Callback function for preg_replace_callback to fix encoding issues in the email subject
	 *
	 * @param array $matches
	 * @return string
	 */
	private function convert_encoding( $matches = array() ) {
		return function_exists( 'mb_convert_encoding' )
			? mb_convert_encoding( $matches[1], "UTF-8", "HTML-ENTITIES" )
			: $matches[1];
	}

}
