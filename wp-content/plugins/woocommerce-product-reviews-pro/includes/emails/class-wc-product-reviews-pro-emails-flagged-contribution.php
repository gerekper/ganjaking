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
 * Flagged contribution email.
 *
 * Email notifications sent to admin whenever a contribution is flagged for removal by customers.
 *
 * @since 1.10.0
 */
class WC_Product_Reviews_Pro_Emails_Flagged_Contribution extends \WC_Email {


	/** @var \WC_Contribution contribution being flagged */
	private $contribution;

	/** @var \WC_Product product reviewed by the flagged contribution */
	private $product;


	/**
	 * Sets the email properties.
	 *
	 * @since 1.10.0
	 */
	public function __construct() {

		$this->id          = 'wc_product_reviews_pro_flagged_contribution_email';
		$this->title       = __( 'Flagged Contribution', 'woocommerce-product-reviews-pro' );
		$this->description = __( 'Notifies the admin when a contribution is flagged for removal by a customer.', 'woocommerce-product-reviews-pro' );

		$this->template_html  = 'emails/flagged-contribution.php';
		$this->template_plain = 'emails/plain/flagged-contribution.php';

		$this->recipient = $this->get_recipient();

		$this->subject = __( 'A {contribution_type} on {site_title} has been flagged for removal!', 'woocommerce-product-reviews-pro' );
		$this->heading = __( 'A {contribution_type} for {product_name} was marked as inappropriate.', 'woocommerce-product-reviews-pro' );

		$site_title = $this->get_blogname();

		$this->placeholders['{blogname}']          = $site_title;
		$this->placeholders['{site_title}']        = $site_title;
		$this->placeholders['{product_name}']      = '';
		$this->placeholders['{contribution_type}'] = '';

		// triggers
		add_action( "{$this->id}_notification", array( $this, 'trigger' ), 10, 2 );

		parent::__construct();
	}


	/**
	 * Returns the flag object.
	 *
	 * @since 1.10.0
	 *
	 * @return null|\WC_Product_Reviews_Pro_Contribution_Flag
	 */
	public function get_flag() {

		return $this->object instanceof \WC_Product_Reviews_Pro_Contribution_Flag ? $this->object : null;
	}


	/**
	 * Returns the email's related contribution being flagged.
	 *
	 * @since 1.10.0
	 *
	 * @return null|\WC_Contribution
	 */
	public function get_contribution() {

		return $this->contribution;
	}


	/**
	 * Returns the email's related product.
	 *
	 * @since 1.10.0
	 *
	 * @return null|\WC_Product
	 */
	public function get_product() {

		return $this->product;
	}


	/**
	 * Returns the email's recipient.
	 *
	 * @since 1.10.0
	 *
	 * @return string either a single email or comma separated emails
	 */
	public function get_recipient() {

		$recipients = array_map( 'trim', explode( ',', $this->get_option( 'recipients', get_bloginfo( 'admin_email' ) ) ) );

		foreach ( $recipients as $i => $recipient ) {

			if ( ! is_email( $recipient ) ) {

				unset( $recipients[ $i ] );
			}
		}

		/**
		 * Filters the recipients of a flagged contribution email notification.
		 *
		 * @since 1.10.0
		 *
		 * @param string[] $recipients array with one ore more email addresses (defaults to admin email)
		 * @param \WC_Product_Reviews_Pro_Emails_Flagged_Contribution the current email object handler
		 */
		$recipients = (array) apply_filters( 'wc_product_reviews_pro_flagged_contribution_email_recipients', $recipients, $this );

		// if the user reporting the contribution is the same listed among the recipients, remove them from the recipients list
		if ( $this->object instanceof \WC_Product_Reviews_Pro_Contribution_Flag && ! $this->object->is_anonymous() ) {

			if ( ( $key = array_search( $this->object->get_user_email(), $recipients, true ) ) !== false ) {

				unset( $recipients[ $key ] );
			}
		}

		return implode( ',', $recipients );
	}


	/**
	 * Returns false (not a customer email).
	 *
	 * @since 1.10.0
	 *
	 * @return false
	 */
	public function is_customer_email() {

		return false;
	}


	/**
	 * Checks whether the email is enabled.
	 *
	 * @since 1.10.0
	 *
	 * @return bool
	 */
	public function is_enabled() {

		$is_enabled = parent::is_enabled();

		/**
		 * Filters whether the flagged contribution email is enabled.
		 *
		 * @since 1.10.0
		 *
		 * @param bool $is_enabled whether the email is enabled (default: email setting value)
		 * @param null|\WC_Product_Reviews_Pro_Contribution_Flag $flag flag object
		 * @param null|\WC_Contribution contribution object
		 * @param null|\WC_Product product object
		 */
		return (bool) apply_filters( 'wc_product_reviews_pro_flagged_contribution_email_is_enabled', $is_enabled, $this->object, $this->contribution, $this->product );
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
			$form_fields['enabled']['default'] = 'no';
			$form_fields['subject']['default'] = $this->subject;
			$form_fields['heading']['default'] = $this->heading;

			// add custom fields
			$form_fields = Framework\SV_WC_Helper::array_insert_after( $form_fields, 'enabled', array(
				'recipients' => array(
					'title'    => __( 'Recipients', 'woocommerce-product-reviews-pro' ),
					'desc_tip' => __( 'The recipients that will be notified whenever a contribution is flagged for removal.', 'woocommerce-product-reviews-pro' ),
					'type'     => 'text',
					'default'  => get_bloginfo( 'admin_email' ),
				),
			) );
		}

		// set the updated fields
		$this->form_fields = $form_fields;
	}


	/**
	 * Triggers the email notification.
	 *
	 * @since 1.10.0
	 *
	 * @param int $contribution_id the contribution ID
	 * @param \WC_Product_Reviews_Pro_Contribution_Flag $flag the contribution flag object
	 */
	public function trigger( $contribution_id, $flag ) {

		$contribution = wc_product_reviews_pro_get_contribution( $contribution_id );

		if (    $contribution instanceof \WC_Contribution
		     && $flag instanceof \WC_Product_Reviews_Pro_Contribution_Flag
		     && $flag->is_unresolved() ) {

			$this->object       = $flag;
			$this->contribution = $contribution;
			$this->product      = $contribution->get_product();

			// check for enabled after main properties have been set for filtering purposes
			if ( $this->is_enabled() ) {

				$this->placeholders['{product_name}']      = $this->product->get_title();
				$this->placeholders['{contribution_type}'] = wc_product_reviews_pro_get_contribution_type( $contribution->get_type() )->get_title();

				$this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );

			} else {

				// reset properties if email not enabled
				$this->object       = null;
				$this->contribution = null;
				$this->product      = null;
			}
		}
	}


	/**
	 * Returns the email's HTML content.
	 *
	 * @since 1.10.0
	 *
	 * @return string HTML content
	 */
	public function get_content_html() {

		ob_start();

		wc_get_template( $this->template_html, array(
			'flag'              => $this->object,
			'product'           => $this->product,
			'contribution'      => $this->contribution,
			'contribution_type' => wc_product_reviews_pro_get_contribution_type( $this->contribution->get_type() ),
			'site_title'        => $this->get_blogname(),
			'email_heading'     => $this->get_heading(),
			'sent_to_admin'     => true,
			'plain_text'        => false,
			'email'             => $this,
		) );

		return ob_get_clean();
	}


	/**
	 * Returns the email's HTML content.
	 *
	 * @since 1.10.0
	 *
	 * @return string plain text content
	 */
	public function get_content_plain() {

		ob_start();

		wc_get_template( $this->template_plain, array(
			'flag'              => $this->object,
			'product'           => $this->product,
			'contribution'      => $this->contribution,
			'contribution_type' => wc_product_reviews_pro_get_contribution_type( $this->contribution->get_type() ),
			'site_title'        => $this->get_blogname(),
			'email_heading'     => $this->get_heading(),
			'sent_to_admin'     => true,
			'plain_text'        => true,
		) );

		return ob_get_clean();
	}


}
