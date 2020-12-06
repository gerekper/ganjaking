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
 * Product Reviews Pro review update confirmation email.
 *
 * Email notifications are sent when Logged-out/Guest user tries to leave a review with an email tied to a registered user.
 *
 * @since 1.8.0
 */
class WC_Product_Reviews_Pro_Emails_Review_Update_Confirmation extends \WC_Email {


	/** @var \WC_Product product being reviewed */
	private $product = null;

	/** @var \WC_Contribution contribution being updated */
	private $contribution = null;


	/**
	 * Sets properties.
	 *
	 * @since 1.8.0
	 */
	public function __construct() {

		$this->id          = 'wc_product_reviews_pro_review_update_confirmation_email';
		$this->title       = __( 'Review Update Confirmation', 'woocommerce-product-reviews-pro' );
		$this->description = __( 'Email users when they try to leave a review with an email tied to a registered user.', 'woocommerce-product-reviews-pro' );

		$this->template_html  = 'emails/review-update-confirmation.php';
		$this->template_plain = 'emails/plain/review-update-confirmation.php';

		$this->subject = __( 'Review update confirmation needed on a {product_name}', 'woocommerce-product-reviews-pro' );
		$this->heading = __( 'A review update confirmation needed on a {product_name} on {site_title}', 'woocommerce-product-reviews-pro' );

		$site_title = $this->get_blogname();

		$this->placeholders['{blogname}']     = $site_title;
		$this->placeholders['{site_title}']   = $site_title;
		$this->placeholders['{product_name}'] = '';

		// triggers
		add_action( "{$this->id}_notification", array( $this, 'trigger' ), 10, 3 );

		parent::__construct();
	}


	/**
	 * Flags the email as a customer email.
	 *
	 * @since 1.8.0
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
	 * Triggers the review update confirmation email.
	 *
	 * @since 1.8.0
	 *
	 * @param int[] $users an array of users IDs
	 * @param \WC_Product $product product contributed to
	 * @param \WC_Contribution $contribution contribution object
	 */
	public function trigger( $users, $product, $contribution ) {

		if ( ! empty( $users ) && is_array( $users ) && $this->is_enabled() ) {

			foreach ( $users as $user_id ) {

				// flag to send mail for comment/replies
				$send_mail = false;

				// checking if user_id is numeric or an email address
				if ( is_numeric( $user_id ) ) {

					$this->object = get_user_by( 'id', $user_id );

					if ( $this->object instanceof \WP_User ) {
						$send_mail = true;
					}

				} elseif ( is_email( $user_id ) ) {

					$send_mail = true;

					$this->object = new stdClass();

					$this->object->user_email   = $user_id;
					$this->object->display_name = $user_id;
					$this->object->ID           = $user_id;
				}

				if ( true === $send_mail ) {

					$this->recipient    = $this->object->user_email;
					$this->product      = $product;
					$this->contribution = $contribution;

					if ( ! $this->contribution instanceof \WC_Contribution || ! $this->get_recipient() ) {
						continue;
					}

					$this->placeholders['{product_name}'] = $this->product->get_title();

					$this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
				}
			}
		}
	}


	/**
	 * Returns the email's HTML content.
	 *
	 * @since 1.8.0
	 *
	 * @return string HTML content
	 */
	public function get_content_html() {

		ob_start();

		wc_get_template( $this->template_html, array(
			'user'          => $this->object,
			'product'       => $this->product,
			'contribution'  => $this->contribution,
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
	 * @since 1.8.0
	 *
	 * @return string plain text content
	 */
	public function get_content_plain() {

		ob_start();

		wc_get_template( $this->template_plain, array(
			'user'          => $this->object,
			'product'       => $this->product,
			'contribution'  => $this->contribution,
			'site_title'    => $this->get_blogname(),
			'email_heading' => $this->get_heading(),
			'sent_to_admin' => false,
			'plain_text'    => true,
		) );

		return ob_get_clean();
	}


}
