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
 * Membership Activated Email.
 *
 * Membership activated emails are sent to delayed members once their membership enters active status.
 *
 * @since 1.12.0
 */
class WC_Memberships_User_Membership_Activated_Email extends \WC_Memberships_User_Membership_Email {


	/**
	 * Email constructor.
	 *
	 * @since 1.12.0
	 */
	public function __construct() {

		$this->id            = __CLASS__;
		$this->plan_editable = true;

		$this->title       = __( 'Delayed membership activated', 'woocommerce-memberships' );
		$this->description = __( 'Delayed membership activated emails are sent to members when a delayed membership is activated.', 'woocommerce-memberships' );
		$this->subject     = __( 'Your {site_title} membership is now active!', 'woocommerce-memberships');
		$this->heading     = __( 'You can now access {membership_plan}', 'woocommerce-memberships');

		$this->template_html  = 'emails/membership-activated.php';
		$this->template_plain = 'emails/plain/membership-activated.php';

		// call parent constructor
		parent::__construct();
	}


	/**
	 * Triggers the Membership Activated email.
	 *
	 * @since 1.12.0
	 *
	 * @param int $user_membership_id the ID of the expired user membership
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
		     || ! $this->object->is_active() ) {

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
	 * @since 1.12.0
	 */
	public function init_form_fields() {

		// set the default fields from parent
		parent::init_form_fields();

		$form_fields = $this->form_fields;

		if ( isset( $form_fields['enabled'] ) ) {

			// set email disabled by default
			$form_fields['enabled']['default'] = 'no';
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
	 * @since 1.12.0
	 *
	 * @return string HTML
	 */
	public function get_default_body() {

		/* translators: Placeholders: the text within curly braces consists of email merge tags that shouldn't be changed in translation */
		$body_html = __( '
			<p>Hey {member_name},</p>
			<p>Your {membership_plan} membership at {site_title} is now active!</p>
			<p>You can view more details about your membership from <a href="{membership_view_url}">your account</a>.</p>
			<p>{site_title}</p>
		', 'woocommerce-memberships' );

		return wp_kses_post( $body_html );
	}


}
