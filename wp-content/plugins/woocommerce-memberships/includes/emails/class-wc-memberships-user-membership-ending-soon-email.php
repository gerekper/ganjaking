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
 * Membership Ending Soon Email.
 *
 * Membership ending soon emails are sent to plan members when their membership is about to expire.
 *
 * @since 1.7.0
 */
class WC_Memberships_User_Membership_Ending_Soon_Email extends \WC_Memberships_User_Membership_Email {


	/** @var string string schedule option key */
	protected $schedule_name = 'send_days_before';


	/**
	 * Email constructor.
	 *
	 * @since 1.7.0
	 */
	public function __construct() {

		$this->id            = __CLASS__;
		$this->plan_editable = true;

		$this->title       = __( 'Membership ending soon', 'woocommerce-memberships' );
		$this->description = __( 'Membership ending soon emails are sent to plan members when their membership is about to expire.', 'woocommerce-memberships' );
		$this->subject     = __( 'Your {site_title} membership ends soon!', 'woocommerce-memberships');
		$this->heading     = __( 'An update about your {membership_plan}', 'woocommerce-memberships');

		$this->template_html  = 'emails/membership-ending-soon.php';
		$this->template_plain = 'emails/plain/membership-ending-soon.php';

		$this->default_schedule       = 3;
		$this->reschedule_description = __( 'You are about to update the schedule to send emails when a membership is about to expire.', 'woocommerce-memberships' );

		// call parent constructor
		parent::__construct();
	}


	/**
	 * Triggers the Membership Ending Soon email.
	 *
	 * @since 1.7.0
	 *
	 * @param int $user_membership_id the ID of the user membership about to expire
	 */
	public function trigger( $user_membership_id ) {

		// set the email object, recipient and parse merge tags
		if (    is_numeric( $user_membership_id )
		     && ( $this->object = wc_memberships_get_user_membership( $user_membership_id ) ) ) {

			if ( $member = get_userdata( $this->object->get_user_id() ) ) {
				$this->recipient = $member->user_email;
			}

			$this->body = $this->object instanceof \WC_Memberships_User_Membership ? $this->object->get_plan()->get_email_content( $this->id ) : '';

			$this->parse_merge_tags();
		}

		// sanity checks
		if (    ! $this->object instanceof \WC_Memberships_User_Membership
		     || ! $this->body
		     || ! $this->is_enabled()
		     || ! $this->get_recipient()
		     ||   $this->object->is_expired()
		     ||   $this->object->is_cancelled()
		     || ! $this->object->is_in_active_period() ) {

			return;
		}

		// send the email
		$this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
	}


	/**
	 * Filters the email settings form fields.
	 *
	 * Extends and overrides parent method.
	 *
	 * @since 1.7.0
	 */
	public function init_form_fields() {

		// set the default fields from parent
		parent::init_form_fields();

		$form_fields = $this->form_fields;

		if ( isset( $form_fields['enabled'] ) ) {

			// set email disabled by default
			$form_fields['enabled']['default'] = 'no';

			// add a field for scheduling the email
			$form_fields = Framework\SV_WC_Helper::array_insert_after( $form_fields, 'enabled', array(
				$this->schedule_name => array(
					'title'             => __( 'Send Email Days Before', 'woocommerce-memberships' ),
					'type'              => 'number',
					'css'               => 'width: 50px;',
					/* translators: Days before a membership expires */
					'description'       => __( 'day(s) before', 'woocommerce-memberships' ),
					'desc_tip'          => __( "Number of days before the membership expires the email will be sent. Note: this shouldn't exceed the length of the Membership plan itself.", 'woocommerce-memberships' ),
					'default'           => $this->default_schedule,
					'custom_attributes' => array(
						'step' => 1,
						'min'  => 1,
						'max'  => 60,
					),
				),
			) );
		}

		if ( isset( $form_fields['subject'] ) ) {

			// adds a subject merge tag hint in field description
			$form_fields['subject']['desc_tip']    = $form_fields['subject']['description'];
			/* translators: Placeholder: %s - merge tag */
			$form_fields['subject']['description'] = sprintf( __( '%s inserts your site name.', 'woocommerce-memberships' ), '<strong><code>{site_title}</code></strong>' );
		}

		if ( isset( $form_fields['heading'] ) ) {

			// adds a heading merge tag hint in field description
			$form_fields['heading']['desc_tip']    = $form_fields['heading']['description'];
			/* translators: Placeholder: %s - merge tag */
			$form_fields['heading']['description'] = sprintf( __( '%s inserts the membership plan name.', 'woocommerce-memberships' ), '<strong><code>{membership_plan}</code></strong>' );
		}

		// email body is set on a membership plan basis in plan settings
		if ( isset( $form_fields['body'] ) ) {
			unset( $form_fields ['body'] );
		}

		// set the updated fields
		$this->form_fields = $form_fields;
	}


	/**
	 * Returns the default body content.
	 *
	 * @since 1.7.0
	 *
	 * @return string HTML
	 */
	public function get_default_body() {

		/* translators: Placeholders: the text within curly braces consists of email merge tags that shouldn't be changed in translation */
		$body_html = __( '
			<p>Hey {member_name},</p>
			<p>Heads up: your {membership_plan} at {site_title} is ending soon! Your membership access will stop on {membership_expiration_date}.</p>
			<p>If you would like to continue to access members-only content and perks, please renew your membership.</p>
			<p><a href="{membership_renewal_url}">Click here to log in and renew your membership now</a>.</p>
			<p>{site_title}</p>
		', 'woocommerce-memberships' );

		return wp_kses_post( $body_html );
	}


}
