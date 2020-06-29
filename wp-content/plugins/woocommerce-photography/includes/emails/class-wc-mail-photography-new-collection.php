<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * WooCommerce Photography New Collection email.
 *
 * @package  WC_Photography/Emails
 * @category Class
 * @author   WooThemes
 */
class WC_Email_Photography_New_Collection extends WC_Email {

	/**
	 * Initialize tracking template.
	 */
	public function __construct() {

		$this->id             = 'wc_photography_new_collection';
		$this->title          = __( 'New Photography Collection', 'woocommerce-photography' );
		$this->description    = __( 'This email is sent to customers when you assign a collection to their account.', 'woocommerce-photography' );

		// Options.
		$this->subject        = __( '[{site_title}] Your photographs are ready! - {collections}', 'woocommerce' );
		$this->heading        = __( 'Photos from your event(s) have been added to your account & are ready to be viewed.', 'woocommerce' );

		// Templates.
		$this->template_html  = 'emails/photography-new-collection.php';
		$this->template_plain = 'emails/plain/photography-new-collection.php';

		// Call parent constructor.
		parent::__construct();

		$this->template_base = WC_Photography::get_templates_path();
	}

	/**
	 * Initialise settings form fields.
	 *
	 * @return void
	 */
	public function init_form_fields() {
		$this->form_fields = array(
			'enabled' => array(
				'title'   => __( 'Enable/Disable', 'woocommerce-photography' ),
				'type'    => 'checkbox',
				'label'   => __( 'Enable this email notification', 'woocommerce-photography' ),
				'default' => 'yes'
			),
			'subject' => array(
				'title'       => __( 'Subject', 'woocommerce-photography' ),
				'type'        => 'text',
				'description' => sprintf( __( 'This controls the email subject line. Leave blank to use the default subject: <code>%s</code>.', 'woocommerce-photography' ), $this->subject ),
				'placeholder' => '',
				'default'     => ''
			),
			'heading' => array(
				'title'       => __( 'Email Heading', 'woocommerce-photography' ),
				'type'        => 'text',
				'description' => sprintf( __( 'This controls the main heading contained within the email. Leave blank to use the default heading: <code>%s</code>.', 'woocommerce-photography' ), $this->heading ),
				'placeholder' => '',
				'default'     => ''
			),
			'email_type' => array(
				'title'       => __( 'Email type', 'woocommerce-photography' ),
				'type'        => 'select',
				'description' => __( 'Choose which format of email to send.', 'woocommerce-photography' ),
				'default'     => 'html',
				'class'       => 'email_type',
				'options'     => array(
					'plain'     => __( 'Plain text', 'woocommerce-photography' ),
					'html'      => __( 'HTML', 'woocommerce-photography' ),
					'multipart' => __( 'Multipart', 'woocommerce-photography' ),
				)
			)
		);
	}

	/**
	 * Get collections name.
	 *
	 * @param  array $collections
	 *
	 * @return array
	 */
	protected function get_collections( $collections ) {
		$names = array();

		foreach ( $collections as $collection_id ) {
			$collection = get_term( $collection_id, 'images_collections' );
			$names[ $collection_id ] = $collection->name;
		}

		return $names;
	}

	/**
	 * Trigger email.
	 *
	 * @param  int   $user_id
	 * @param  array $collections
	 *
	 * @return void
	 */
	public function trigger( $user_id, $collections ) {
		if ( $user_id ) {
			// Email params.
			$this->object      = new WP_User( $user_id );
			$this->recipient   = $this->object->user_email;
			$this->collections = $this->get_collections( $collections );

			// Find and replace.
			$this->find[]    = '{collections}';
			$this->replace[] = implode( ', ', $this->collections );
		}

		if ( ! $this->is_enabled() || ! $this->get_recipient() || ! $this->collections ) {
			return;
		}

		$this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
	}

	/**
	 * Get content HTML.
	 *
	 * @return string
	 */
	public function get_content_html() {
		ob_start();

		wc_get_template(
			$this->template_html,
			array(
				'customer'      => $this->object,
				'email_heading' => $this->get_heading(),
				'collections'   => $this->collections,
				'sent_to_admin' => false,
				'plain_text'    => false
			),
			'woocommerce/',
			$this->template_base
		);

		return ob_get_clean();
	}

	/**
	 * Get content plain text.
	 *
	 * @return string
	 */
	public function get_content_plain() {
		ob_start();

		wc_get_template(
			$this->template_plain,
			array(
				'customer'      => $this->object,
				'email_heading' => $this->get_heading(),
				'collections'   => $this->collections,
				'sent_to_admin' => false,
				'plain_text'    => true
			),
			'woocommerce/',
			$this->template_base
		);

		return ob_get_clean();
	}
}

return new WC_Email_Photography_New_Collection();
