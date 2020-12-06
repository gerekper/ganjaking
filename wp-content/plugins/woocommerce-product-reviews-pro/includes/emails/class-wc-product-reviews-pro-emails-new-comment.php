<?php
/**
 * WooCommerce Product Reviews Pro
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Product Reviews Pro to newer
 * versions in the future. If you wish to customize WooCommerce Product Reviews Pro for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-product-reviews-pro/ for more information.
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2015-2020, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;

/**
 * Product Reviews Pro contribution replies notification email.
 *
 * Email notifications are sent when a new reply is posted to contributions to users that wish to receive updates on contributions they subscribed to.
 *
 * @since 1.3.0
 */
class WC_Product_Reviews_Pro_Emails_New_Comment extends \WC_Email {


	/** @var \WC_Product Product being reviewed */
	private $product;

	/** @var \WC_Contribution Contribution replied to */
	private $contribution;

	/** @var \WC_Contribution_Comment Reply to contribution */
	private $reply;


	/**
	 * Sets properties.
	 *
	 * @since 1.3.0
	 */
	public function __construct() {

		$this->id             = 'wc_product_reviews_pro_new_comment_email';
		$this->title          = __( 'Contribution reply', 'woocommerce-product-reviews-pro' );
		$this->description    = __( 'Email users that wish to be notified whenever there is a new comment on a product contribution they subscribed to.', 'woocommerce-product-reviews-pro' );

		$this->template_html  = 'emails/contribution-comment-notification.php';
		$this->template_plain = 'emails/plain/contribution-comment-notification.php';

		$this->subject        = __( 'New reply posted on a {product_name} {contribution_type}', 'woocommerce-product-reviews-pro' );
		$this->heading        = __( 'A reply has been added to a {contribution_type} on {site_title}', 'woocommerce-product-reviews-pro' );

		$site_title = $this->get_blogname();

		$this->placeholders['{blogname}']          = $site_title;
		$this->placeholders['{site_title}']        = $site_title;
		$this->placeholders['{product_name}']      = '';
		$this->placeholders['{contribution_type}'] = '';

		// triggers
		add_action( "{$this->id}_notification", array( $this, 'trigger' ), 10, 4 );

		parent::__construct();
	}


	/**
	 * Flags the email as a customer email.
	 *
	 * @since 1.4.0
	 *
	 * @return true
	 */
	public function is_customer_email() {

		return true;
	}

	/**
	 * Handles email settings fields.
	 *
	 * @internal
	 *
	 * @since 1.10.0
	 */
	public function init_form_fields() {

		// set the default fields from parent
		parent::init_form_fields();

		$form_fields = $this->form_fields;

		if ( isset( $form_fields['enabled'], $form_fields['subject'], $form_fields['heading'] ) ) {

			// set defaults
			$form_fields['subject']['default'] = $this->subject;
			$form_fields['heading']['default'] = $this->heading;
		}

		$this->form_fields = $form_fields;
	}


	/**
	 * Triggers the new contribution reply notification email.
	 *
	 * @since 1.3.0
	 *
	 * @param int[] $users an array of users IDs
	 * @param \WC_Product $product product contributed to
	 * @param \WC_Contribution $contribution original contribution comment
	 * @param \WC_Contribution_Comment $reply contribution reply comment
	 */
	public function trigger( $users, $product, $contribution, $reply ) {

		if ( ! empty( $users ) && is_array( $users ) && $this->is_enabled() ) {

			foreach ( $users as $user_id ) {

				// flag to send mail for comment/replies
				$send_mail = false;

				// checking if user_id is numeric or an email address
				if ( is_numeric( $user_id ) ) {

					// No need to notify the original author if is the one replying
					if ( (int) $user_id === (int) $reply->contributor_id ) {
						continue;
					}

					$this->object = get_user_by( 'id', $user_id );

					if ( $this->object instanceof \WP_User ) {
						$send_mail = true;
					}

				} elseif ( is_email( $user_id ) ) {

					$send_mail = true;

					$this->object = new \stdClass();

					$this->object->user_email   = $user_id;
					$this->object->display_name = $user_id;
					$this->object->ID           = $user_id;
				}

				if ( true === $send_mail ) {

					$this->recipient    = $this->object->user_email;
					$this->product      = $product;
					$this->contribution = $contribution;
					$this->reply        = $reply;

					if ( ! $this->contribution instanceof \WC_Contribution || ! $this->get_recipient() ) {
						continue;
					}

					$this->placeholders['{product_name}'] = $this->product->get_title();

					if ( $contribution_type = wc_product_reviews_pro_get_contribution_type( $contribution->type ) ) {
						$this->placeholders['{contribution_type}'] = strtolower( $contribution_type->get_title() );
					}

					$this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
				}
			}
		}
	}


	/**
	 * Returns the email's HTML content.
	 *
	 * @since 1.3.0
	 *
	 * @return string HTML content
	 */
	public function get_content_html() {

		ob_start();

		wc_get_template( $this->template_html, array(
			'user'          => $this->object,
			'product'       => $this->product,
			'contribution'  => $this->contribution,
			'reply'         => $this->reply,
			'site_title'    => $this->get_blogname(),
			'email_heading' => $this->get_heading(),
			'sent_to_admin' => false,
			'plain_text'    => false,
			'email'         => $this,
		) );

		return ob_get_clean();
	}


	/**
	 * Returns the email's plain text content.
	 *
	 * @since 1.3.0
	 *
	 * @return string plain text content
	 */
	public function get_content_plain() {

		ob_start();

		wc_get_template( $this->template_plain, array(
			'user'          => $this->object,
			'product'       => $this->product,
			'contribution'  => $this->contribution,
			'reply'         => $this->reply,
			'site_title'    => $this->get_blogname(),
			'email_heading' => $this->get_heading(),
			'sent_to_admin' => false,
			'plain_text'    => true,
			'email'         => $this,
		) );

		return ob_get_clean();
	}


}
