<?php
/**
 * WooCommerce Memberships
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Memberships to newer
 * versions in the future. If you wish to customize WooCommerce Memberships for your
 * needs please refer to https://docs.woocommerce.com/document/woocommerce-memberships/ for more information.
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2014-2020, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

use SkyVerge\WooCommerce\PluginFramework\v5_7_1 as Framework;

defined( 'ABSPATH' ) or exit;

/**
 * Membership Emails handler.
 *
 * This class handles all email-related functionality in Memberships.
 *
 * @since 1.0.0
 */
class WC_Memberships_Emails {


	/**
	 * Sets up membership emails.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		// load email classes
		add_action( 'woocommerce_loaded', array( $this, 'init_emails' ) );

		// add emails
		add_filter( 'woocommerce_email_classes', array( $this, 'get_email_classes' ) );

		// add links to members area if active for the purchased plan(s)
		add_action( 'woocommerce_email_order_meta', array( $this, 'maybe_render_thank_you_content' ), 5, 2 );

		// add triggers
		foreach ( $this->get_email_class_names() as $email ) {
			add_action( $email, array( 'WC_Emails', 'send_transactional_email' ), 10, 10 );
		}

		// add the Memberships emails to the list of emails that should display the Jilt prompt
		add_filter( 'sv_wc_jilt_prompt_email_ids', [ $this, 'add_jilt_prompt_email_ids' ] );

		// adjust the Jilt install prompt wording for Memberships emails
		add_filter( 'sv_wc_jilt_prompt_description', [ $this, 'adjust_jilt_prompt_membership_email_description' ], 10, 2 );

		// adjust the Jilt install prompt wording for the general Emails screen
		add_filter( 'sv_wc_jilt_general_prompt_description', [ $this, 'adjust_jilt_prompt_general_description' ] );
	}


	/**
	 * Adds the Memberships emails to the list of emails that should display the Jilt prompt.
	 *
	 * @internal
	 *
	 * @since 1.17.3
	 *
	 * @param array $email_ids existing email IDs
	 * @return array
	 */
	public function add_jilt_prompt_email_ids( $email_ids ) {

		if ( is_array( $email_ids ) ) {
			$email_ids = array_merge( $email_ids, $this->get_email_class_names() );
		}

		return $email_ids;
	}


	/**
	 * Adjusts the Jilt install prompt wording for Memberships emails.
	 *
	 * @internal
	 *
	 * @since 1.17.3
	 *
	 * @param string $description existing description
	 * @param string $email_id targeted email ID
	 * @return string
	 */
	public function adjust_jilt_prompt_membership_email_description( $description, $email_id ) {

		if ( in_array( $email_id, $this->get_email_class_names(), true ) ) {

			$description = sprintf(
				/* translators: Placeholders: %1$s - <a> tag, %2$s - </a> tag, %3$s - <a> tag, %4$s - </a> tag */
				__( 'Supercharge member communication: create personalized, automated emails using a drag-and-drop editor with %1$sJilt%2$s. Send welcome or winback series, member newsletters, and more. Brought to you by %3$sSkyVerge%4$s.', 'woocommerce-memberships' ),
				'<a href="https://jilt.com/go/wc-email-settings" target="_blank">', '</a>',
				'<a href="https://skyverge.com/go/wc-email-settings" target="_blank">', '</a>'
			);
		}

		return $description;
	}


	/**
	 * Adjusts the Jilt install prompt wording for the general Emails screen.
	 *
	 * @internal
	 *
	 * @since 1.17.3
	 *
	 * @param string $description existing description
	 * @return string
	 */
	public function adjust_jilt_prompt_general_description( $description ) {

		if ( wc_memberships()->is_plugin_active( 'woocommerce-subscriptions.php' ) ) {

			$description = sprintf(
			/* translators: Placeholders: %1$s - <a> tag, %2$s - </a> tag, %3$s - <a> tag, %4$s - </a> tag, %5$s - <a> tag, %6$s - </a> tag */
				__( 'Create beautiful automated, transactional, and marketing emails using a drag-and-drop editor with %1$sJilt%2$s. Even better, Jilt works seamlessly with Memberships and Subscriptions, making it easy for you to send welcome series, pre-renewal notices, and other automated emails to those customers. Learn more about free and paid plans in the %3$sdocumentation%4$s. Brought to you by %5$sSkyVerge%6$s.', 'woocommerce-memberships' ),
				'<a href="https://jilt.com/go/wc-email-settings" target="_blank">', '</a>',
				'<a href="https://jilt.com/go/wc-email-settings-docs" target="_blank">', '</a>',
				'<a href="https://skyverge.com/go/wc-email-settings" target="_blank">', '</a>'
			);

		} else {

			$description = sprintf(
				/* translators: Placeholders: %1$s - <a> tag, %2$s - </a> tag, %3$s - <a> tag, %4$s - </a> tag, %5$s - <a> tag, %6$s - </a> tag */
				__( 'Create beautiful automated, transactional, and marketing emails using a drag-and-drop editor with %1$sJilt%2$s. You can even segment your emails using member details, or send membership automations like a welcome series. Learn more about free and paid plans in the %3$sdocumentation%4$s. Brought to you by %5$sSkyVerge%6$s.', 'woocommerce-memberships' ),
				'<a href="https://jilt.com/go/wc-email-settings" target="_blank">', '</a>',
				'<a href="https://jilt.com/go/wc-email-settings-docs" target="_blank">', '</a>',
				'<a href="https://skyverge.com/go/wc-email-settings" target="_blank">', '</a>'
			);
		}

		return $description;
	}


	/**
	 * Initializes Memberships email classes.
	 *
	 * @since 1.11.0
	 */
	public function init_emails() {

		// loads the base WooCommerce Email class the Memberships abstraction is based on
		if ( ! class_exists( 'WC_Email' ) ) {

			WC()->mailer();
		}

		// loads the Memberships Email abstract class
		if ( ! class_exists( 'WC_Memberships_User_Membership_Email' ) ) {

			require_once( wc_memberships()->get_plugin_path() . '/includes/emails/abstract-wc-memberships-user-membership-email.php' );
		}

		// loads and initializes individual Memberships Emails objects
		foreach ( $this->get_email_class_names( true ) as $class_name => $include_path ) {

			if ( ! class_exists( $class_name ) && is_readable( wc_memberships()->get_plugin_path() . $include_path ) ) {

				$this->$class_name = wc_memberships()->load_class( $include_path, $class_name );
			}
		}
	}


	/**
	 * Returns Memberships emails classes.
	 *
	 * @since 1.7.0
	 *
	 * @param bool $include_paths whether to return an associative array with class paths
	 * @return array indexed or associative array
	 */
	public function get_email_class_names( $include_paths = false )  {

		$email_classes = array(
			'WC_Memberships_User_Membership_Note_Email'             => '/includes/emails/class-wc-memberships-user-membership-note-email.php',
			'WC_Memberships_User_Membership_Ending_Soon_Email'      => '/includes/emails/class-wc-memberships-user-membership-ending-soon-email.php',
			'WC_Memberships_User_Membership_Ended_Email'            => '/includes/emails/class-wc-memberships-user-membership-ended-email.php',
			'WC_Memberships_User_Membership_Renewal_Reminder_Email' => '/includes/emails/class-wc-memberships-user-membership-renewal-reminder-email.php',
			'WC_Memberships_User_Membership_Activated_Email'        => '/includes/emails/class-wc-memberships-user-membership-activated-email.php',
		);

		return true !== $include_paths ? array_keys( $email_classes ) : $email_classes;
	}


	/**
	 * Adds custom memberships emails to WC emails.
	 *
	 * @since 1.7.0
	 *
	 * @param array $emails optional, associative array of email objects
	 * @return \WC_Email[]|\WC_Memberships_Emails[] associative array with email objects as values
	 */
	public function get_email_classes( $emails = array() ) {

		// init emails if uninitialized
		$this->init_emails();

		foreach ( $this->get_email_class_names() as $class ) {
			$emails[ $class ] = $this->$class;
		}

		return $emails;
	}


	/**
	 * Returns the instance of an email handler.
	 *
	 * @since 1.11.0
	 *
	 * @param string $which_email email class name
	 * @return null|\WC_Memberships_User_Membership_Email
	 */
	private function get_email_instance( $which_email ) {

		$email = null;

		if ( is_string( $which_email ) ) {

			// init emails if uninitialized
			$this->init_emails();

			if ( empty( $this->$which_email ) ) {
				$emails = $this->get_email_classes();
				$email  = isset( $emails[ $which_email ] ) ? $emails[ $which_email ] : null;
			} else {
				$email  = $this->$which_email;
			}
		}

		return $email;
	}


	/**
	 * Returns the membership note email handler instance.
	 *
	 * @since 1.11.0
	 *
	 * @return null|\WC_Memberships_User_Membership_Note_Email
	 */
	public function get_user_membership_note_email_instance() {

		return $this->get_email_instance( 'WC_Memberships_User_Membership_Note_Email' );
	}


	/**
	 * Returns the membership ending soon email handler instance.
	 *
	 * @since 1.11.0
	 *
	 * @return null|\WC_Memberships_User_Membership_Ending_Soon_Email
	 */
	public function get_user_membership_ending_soon_email_instance() {

		return $this->get_email_instance( 'WC_Memberships_User_Membership_Ending_Soon_Email' );
	}


	/**
	 * Returns the membership ended email handler instance.
	 *
	 * @since 1.11.0
	 *
	 * @return null|\WC_Memberships_User_Membership_Ended_Email
	 */
	public function get_user_membership_ended_email_instance() {

		return $this->get_email_instance( 'WC_Memberships_User_Membership_Ended_Email' );
	}


	/**
	 * Returns the renewal reminder email handler instance.
	 *
	 * @since 1.11.0
	 *
	 * @return null|\WC_Memberships_User_Membership_Renewal_Reminder_Email
	 */
	public function get_user_membership_renewal_reminder_email_instance() {

		return $this->get_email_instance( 'WC_Memberships_User_Membership_Renewal_Reminder_Email' );
	}


	/**
	 * Returns the activated delayed membership email handler instance.
	 *
	 * @since 1.12.0
	 *
	 * @return null|\WC_Memberships_User_Membership_Renewal_Reminder_Email
	 */
	public function get_user_membership_activated_email_instance() {

		return $this->get_email_instance( 'WC_Memberships_User_Membership_Activated_Email' );
	}


	/**
	 * Returns a membership email's default content.
	 *
	 * @since 1.7.0
	 *
	 * @param string $email the email
	 * @return string may contain HTML
	 */
	public function get_email_default_content( $email ) {

		// ensure the email class is capitalized
		$email   = implode( '_', array_map( 'ucfirst', explode( '_', $email ) ) );
		$emails  = $this->get_email_classes();
		$content = '';

		if ( isset( $emails[ $email ] ) && method_exists( $emails[ $email ], 'get_default_body' ) ) {
			$content = $emails[ $email ]->get_default_body();
		}

		return $content;
	}


	/**
	 * Sends a user membership email.
	 *
	 * @since 1.7.0
	 *
	 * @param string $email the type of membership email to send
	 * @param mixed $args the param to pass to the email to be sent
	 */
	public function send_email( $email, $args ) {

		// ensure the email class is capitalized
		$email  = implode( '_', array_map( 'ucfirst', explode( '_', $email ) ) );
		$emails = $this->get_email_classes();

		if ( ! isset( $emails[ $email ] ) || ! method_exists( $emails[ $email ], 'trigger' ) ) {
			return;
		}

		$emails[ $email ]->trigger( $args );
	}


	/**
	 * Sends a membership activated email for a user membership.
	 *
	 * @see \WC_Memberships_User_Membership_Activated_Email
	 *
	 * @since 1.12.0
	 *
	 * @param int $user_membership_id ID of the activated membership
	 */
	public function send_membership_activated_email( $user_membership_id ) {

		$this->send_email( 'WC_Memberships_User_Membership_Activated_Email', $user_membership_id );
	}


	/**
	 * Sends an expiring soon email for a user membership.
	 *
	 * @see \WC_Memberships_Membership_Ending_Soon_Email
	 *
	 * @since 1.7.0
	 *
	 * @param int $user_membership_id ID of the expiring membership
	 */
	public function send_membership_ending_soon_email( $user_membership_id ) {

		$this->send_email( 'WC_Memberships_User_Membership_Ending_Soon_Email', $user_membership_id );
	}


	/**
	 * Sends a membership ended email for a user membership.
	 *
	 * @see \WC_Memberships_Membership_Ended_Email
	 *
	 * @since 1.7.0
	 *
	 * @param int $user_membership_id ID of the expired membership
	 */
	public function send_membership_ended_email( $user_membership_id ) {

		$this->send_email( 'WC_Memberships_User_Membership_Ended_Email', $user_membership_id );
	}


	/**
	 * Sends a renewal reminder email for a user membership.
	 *
	 * @see \WC_Memberships_Membership_Renewal_Reminder_Email
	 *
	 * @since 1.7.0
	 *
	 * @param int $user_membership_id ID of the expired membership
	 */
	public function send_membership_renewal_reminder_email( $user_membership_id ) {

		$this->send_email( 'WC_Memberships_User_Membership_Renewal_Reminder_Email', $user_membership_id );
	}


	/**
	 * Send a new user membership note notification for the member.
	 *
	 * @since 1.7.0
	 *
	 * @param array $args array of arguments passed to the email object {	 *
	 *     @type int $user_membership_id the user membership the email is for
	 *     @type string $membership_note the contents of the note to send
	 * }
	 */
	public function send_new_membership_note_email( array $args ) {

		$this->send_email( 'WC_Memberships_User_Membership_Note_Email', $args );
	}


	/**
	 * Return merge tags help strings.
	 *
	 * @since 1.7.0
	 *
	 * @return string[] array of text strings
	 */
	public function get_emails_merge_tags_help() {

		$merge_tags_help = array(
			/* translators: Placeholder: %s - merge tag */
			sprintf( __( '%s inserts your site name.', 'woocommerce-memberships' ),
				'<strong><code>{site_title}</code></strong>' ),
			/* translators: Placeholder: %s - merge tag */
			sprintf( __( '%s inserts the member display name.', 'woocommerce-memberships' ),
				'<strong><code>{member_name}</code></strong>' ),
			/* translators: Placeholder: %s - merge tag */
			sprintf( __( '%s inserts the member first name.', 'woocommerce-memberships' ),
				'<strong><code>{member_first_name}</code></strong>' ),
			/* translators: Placeholder: %s - merge tag */
			sprintf( __( '%s inserts the member last name.', 'woocommerce-memberships' ),
				'<strong><code>{member_last_name}</code></strong>' ),
			/* translators: Placeholder: %s - merge tag */
			sprintf( __( '%s inserts the member full name (or display name, if full name is not set).', 'woocommerce-memberships' ),
				'<strong><code>{member_full_name}</code></strong>' ),
			/* translators: Placeholder: %s - merge tag */
			sprintf( __( '%s inserts the membership plan name.', 'woocommerce-memberships' ),
				'<strong><code>{membership_plan}</code></strong>' ),
			/* translators: Placeholder: %s - merge tag */
			sprintf( __( '%s inserts the expiration date of the membership.', 'woocommerce-memberships' ),
				'<strong><code>{membership_expiration_date}</code></strong>' ),
			/* translators: Placeholder: %s - merge tag */
			sprintf( __( '%s inserts the time difference between now and the date when the membership expires or has expired (e.g. "2 days", or "1 week", etc.).', 'woocommerce-memberships' ),
				'<strong><code>{membership_expiry_time_diff}</code></strong>' ),
			/* translators: Placeholder: %s - merge tag */
			sprintf( __( '%s inserts a plain URL to the members area to view the membership.', 'woocommerce-memberships' ),
				'<strong><code>{membership_view_url}</code></strong>' ),
			/* translators: Placeholder: %s - merge tag */
			sprintf( __( '%s inserts a plain membership renewal URL.', 'woocommerce-memberships' ),
				'<strong><code>{membership_renewal_url}</code></strong>' ),
		);

		return $merge_tags_help;
	}


	/**
	 * Renders a thank you message in order emails when a membership is purchased.
	 *
	 * @since 1.8.4
	 *
	 * @param \WC_Order $order the order for the given email
	 * @param bool $sent_to_admin true if the email is sent to admins
	 */
	public function maybe_render_thank_you_content( $order, $sent_to_admin ) {

		if ( ! $sent_to_admin ) {
			echo '<br />' . wp_kses_post( wc_memberships_get_order_thank_you_links( $order ) );
		}
	}


}
