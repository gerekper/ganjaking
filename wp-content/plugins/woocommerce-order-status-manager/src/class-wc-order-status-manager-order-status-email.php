<?php
/**
 * WooCommerce Order Status Manager
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Order Status Manager to newer
 * versions in the future. If you wish to customize WooCommerce Order Status Manager for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-order-status-manager/ for more information.
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2015-2020, SkyVerge, Inc. (info@skyverge.com)
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;

if ( ! class_exists( 'WC_Order_Status_Manager_Order_Status_Email' ) ) :

/**
 * Order Status Manager Order Status Email
 *
 * A generic email class for custom order status emails.
 *
 * @since 1.0.0
 */
class WC_Order_Status_Manager_Order_Status_Email extends WC_Email {


	/** @var int Related Post ID */
	public $post_id;

	/** @var string Admin or Customer */
	public $type;

	/** @var string Email body text */
	private $body_text;

	/** @var array Trigger/dispatch conditions */
	public $dispatch_conditions;

	/** @var bool True if email should be dispatched when a new order is created */
	public $dispatch_on_new_order;

	/** @var string|void Default body text used for settings */
	public $default_body_text;

	/** @var bool Show purchase notes for custom statuses marked paid */
	public $show_purchase_note = true;


	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 * @param string $id
	 * @param array $args {
	 *     An array of arguments. Required.
	 *
	 *     @type string $title               Email Title.
	 *     @type string $description         Email Description. Optional.
	 *     @type string $type                Email Type - one of admin or customer. Defaults to admin.
	 *     @type array  $dispatch_conditions An array of email dispatch conditions. Optional.
	 * }
	 */
	public function __construct( $id, array $args ) {

		$this->id                    = $id;
		$this->post_id               = $args['post_id'];
		$this->type                  = $args['type'];
		$this->recipient             = $this->get_option( 'recipient' );
		$this->title                 = $args['title'];
		$this->heading               = $args['title'];
		$this->description           = ! empty( $args['description'] ) ? $args['description'] : '';
		$this->dispatch_conditions   = $args['dispatch_conditions'];
		$this->dispatch_on_new_order = $args['dispatch_on_new_order'];

		// Configure email defaults based on type
		switch ( $this->type ) {

			case 'customer':

				$this->heading           = __( 'Order has been updated', 'woocommerce-order-status-manager' );
				$this->subject           = __( 'Regarding your {site_title} order from {order_date}', 'woocommerce-order-status-manager' );
				$this->default_body_text = __( 'Your order is now {order_status}. Order details are shown below for your reference:', 'woocommerce-order-status-manager' );

				if ( ! $this->recipient ) {
					$this->recipient = __( 'Customer', 'woocommerce-order-status-manager' );
				}

			break;

			case 'admin':

				$this->heading           = __( 'Order has been updated', 'woocommerce-order-status-manager' );
				$this->subject           = __( '[{site_title}] Customer order ({order_number}) updated', 'woocommerce-order-status-manager' );
				$this->default_body_text = __( 'This order is now {order_status}. Order details are as follows:', 'woocommerce-order-status-manager' );

				if ( ! $this->recipient ) {
					$this->recipient = get_option( 'admin_email' );
				}

			break;

		}

		$this->body_text      = $this->get_option( 'body_text' );
		$this->template_html  = $this->locate_email_template();
		$this->template_plain = $this->locate_email_template( 'plain' );

		// Cue this email to be sent on hooks
		if ( ! empty( $this->dispatch_conditions ) || 'yes' === $this->dispatch_on_new_order ) {
			add_action( 'wc_order_status_manager_order_status_change_notification', array( $this, 'maybe_trigger' ), 10, 3 );
		}

		// When a template is deleted
		if ( ! empty( $_GET['delete_template'] ) && ( $template = esc_attr( basename( $_GET['delete_template'] ) ) ) && ! empty( $this->$template ) ) {
			add_action( 'woocommerce_delete_email_template', 'reset_templates', 10, 2 );
		}

		// Call parent constuctor
		parent::__construct();
	}


	/**
	 * Is customer email
	 *
	 * @since 1.4.0
	 * @return bool
	 */
	public function is_customer_email() {
		return 'customer' === $this->type;
	}


	/**
	 * Initialise email form fields
	 *
	 * @since 1.0.0
	 */
	public function init_form_fields() {

		$form_fields = array(

			'enabled' => array(
				'title'       => __( 'Enable/Disable', 'woocommerce-order-status-manager' ),
				'type'        => 'checkbox',
				'label'       => __( 'Enable this email notification', 'woocommerce-order-status-manager' ),
				'default'     => 'no',
			),

			'recipient' => array(
				'title'       => __( 'Recipient(s)', 'woocommerce-order-status-manager' ),
				'type'        => 'text',
				/* translators: %s - admin email as default option */
				'description' => sprintf( __( 'Enter recipients (comma separated) for this email. Defaults to <code>%s</code>.', 'woocommerce-order-status-manager' ), esc_attr( get_option( 'admin_email' ) ) ),
				'placeholder' => '',
				'default'     => '',
			),

			'subject' => array(
				'title'       => __( 'Subject', 'woocommerce-order-status-manager' ),
				'type'        => 'text',
				/* translators: %s - the default subject text */
				'description' => sprintf( __( 'This controls the email subject line. Leave blank to use the default subject: <code>%s</code>.', 'woocommerce-order-status-manager' ), $this->subject ),
				'placeholder' => '',
				'default'     => '',
			),

			'heading' => array(
				'title'       => __( 'Email Heading', 'woocommerce-order-status-manager' ),
				'type'        => 'text',
				/* translators: %s - the default heading text */
				'description' => sprintf( __( 'This controls the main heading contained within the email notification. Leave blank to use the default heading: <code>%s</code>.', 'woocommerce-order-status-manager' ), $this->heading ),
				'placeholder' => '',
				'default'     => '',
			),

			'body_text' => array(
				'title'       => __( 'Email Body', 'woocommerce-order-status-manager' ),
				'type'        => 'textarea',
				'description' => __( 'Optional email body text. You can use the following placeholders: <code>{order_date}, {order_number}, {order_status}, {billing_first_name}, {billing_last_name}, {billing_company}, {blogname}, {site_title}</code>', 'woocommerce-order-status-manager' ),
				'placeholder' => '',
				'default'     => $this->default_body_text,
			),

			'email_type' => array(
				'title'       => __( 'Email type', 'woocommerce-order-status-manager' ),
				'type'        => 'select',
				'description' => __( 'Choose which format of email to send.', 'woocommerce-order-status-manager' ),
				'default'     => 'html',
				'class'       => 'email_type',
				'options'     => array(
					'plain'       => __( 'Plain text', 'woocommerce-order-status-manager' ),
					'html'        => __( 'HTML', 'woocommerce-order-status-manager' ),
					'multipart'   => __( 'Multipart', 'woocommerce-order-status-manager' ),
				),
			),

		);

		// Customer emails do not use the recipient field
		if ( 'customer' === $this->type ) {
			unset( $form_fields['recipient'] );
		}

		/**
		 * Filter email form fields
		 *
		 * @since 1.0.0
		 *
		 * @param array $form_fields Default form fields.
		 * @param string $id E-mail ID
		 * @param string $type E-mail type
		 */
		$this->form_fields = apply_filters( 'wc_order_status_manager_order_status_email_form_fields', $form_fields, $this->id, $this->type );
	}


	/**
	 * Trigger function
	 *
	 * @since 1.0.0
	 * @param int $order_id
	 */
	public function trigger( $order_id ) {

		$order = $order_id ? wc_get_order( $order_id ) : null;

		if ( $order ) {

			$this->object = $order;

			$status = new WC_Order_Status_Manager_Order_Status( $order_id );

			if ( $status->get_id() > 0 ) {
				$this->show_purchase_note = ! $status->is_core_status() && $status->is_paid();
			}

			if ( 'customer' === $this->type ) {
				$this->recipient = $this->object->get_billing_email();
			}

			// Supported variables in subject, heading and body text
			$this->find['order-date']            = '{order_date}';
			$this->find['order-number']          = '{order_number}';
			$this->find['order-status']          = '{order_status}';
			$this->find['billing-first-name']    = '{billing_first_name}';
			$this->find['billing-last-name']     = '{billing_last_name}';
			$this->find['billing-company']       = '{billing_company}';

			/**
			 * Filter the supported variables in subject, heading and body text
			 *
			 * @since 1.1.1
			 *
			 * @param array $find Associative array of placeholders.
			 * @param string $id E-mail ID
			 * @param string $type E-mail type
			 * @param \WC_Order $order the order
			 */
			$this->find = apply_filters( 'wc_order_status_manager_order_status_email_find_variables', $this->find, $this->id, $this->type, $this->object );

			$this->replace['order-date']         = $this->object->get_date_created() ? date_i18n( wc_date_format(), $this->object->get_date_created()->getTimestamp() ) : '';
			$this->replace['order-number']       = $this->object->get_order_number();
			$this->replace['order-status']       = wc_get_order_status_name( $this->object->get_status() );
			$this->replace['billing-first-name'] = $this->object->get_billing_first_name();
			$this->replace['billing-last-name']  = $this->object->get_billing_last_name();
			$this->replace['billing-company']    = $this->object->get_billing_company();

			/**
			 * Filter the the strings to replace in subject, heading and body text.
			 *
			 * @since 1.1.1
			 *
			 * @param array $replace Associative array of strings.
			 * @param string $id E-mail ID
			 * @param string $type E-mail type
			 * @param \WC_Order $order the order
			 */
			$this->replace = apply_filters( 'wc_order_status_manager_order_status_email_replace_variables', $this->replace, $this->id, $this->type, $this->object );
		}

		if ( ! $this->is_enabled() || ! $this->get_recipient() ) {
			return;
		}

		$this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
	}


	/**
	 * Gets the email's body text.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_body_text() {

		/**
		 * Filters the email body text.
		 *
		 * @since 1.0.0
		 *
		 * @param string $body_text the email body text
		 * @param \WC_Order $order the order object
		 * @param \WC_Order_Status_Manager_Order_Status_Email $email the email object
		 */
		return (string) apply_filters( "wc_order_status_manager_order_status_email_body_text_{$this->id}", $this->format_string( $this->body_text ), $this->object, $this );
	}


	/**
	 * Get HTML content for the email
	 *
	 * @since 1.0.0.
	 * @see \WC_Email::get_content_help
	 * @return string Email HTML content
	 */
	public function get_content_html() {

		ob_start();

		wc_get_template( $this->template_html, array(
			'order'               => $this->object,
			'show_download_links' => $this->object->is_download_permitted(),
			'show_purchase_note'  => $this->show_purchase_note,
			'email_heading'       => $this->get_heading(),
			'email_body_text'     => $this->get_body_text(),
			'sent_to_admin'       => 'admin' === $this->type,
			'plain_text'          => false,
			'email'               => $this,
		) );

		return ob_get_clean();
	}


	/**
	 * Get plain content for the email
	 *
	 * @since 1.0.0.
	 * @return string Email plain content
	 */
	public function get_content_plain() {

		ob_start();

		wc_get_template( $this->template_plain, array(
			'order'               => $this->object,
			'show_download_links' => $this->object->is_download_permitted(),
			'show_purchase_note'  => $this->show_purchase_note,
			'email_heading'       => $this->get_heading(),
			'email_body_text'     => $this->get_body_text(),
			'sent_to_admin'       => 'admin' === $this->type,
			'plain_text'          => true,
			'email'               => $this,
		) );

		return ob_get_clean();
	}


	/**
	 * Locate the template file for this email
	 *
	 * Looks for templates in the following order:
	 * 1. emails/{$type}-order-status-email-{$slug}.php
	 * 2. emails/{$type}-order-status-email-{$id}.php
	 * 3. emails/{$type}-order-status-email.php
	 *
	 * Templates are looked for in current theme, then our plugin and then WC core.
	 *
	 * @since 1.0.0
	 * @param string $type Optional. Type of template to locate. One of `html` or `plain`. Defaults to `html`
	 * @return string Path to template file
	 */
	public function locate_email_template( $type = 'html' ) {

		$type_path = 'plain' === $type ? 'plain/' : '';

		$templates = array(
			"emails/{$type_path}{$this->type}-order-status-email-{$this->post_id}.php",
			"emails/{$type_path}{$this->type}-order-status-email.php",
		);

		if ( $email_slug = sanitize_title( $this->title ) ) {
			array_unshift( $templates, "emails/{$type_path}{$this->type}-order-status-email-{$email_slug}.php" );
		}

		$located_template = '';

		// Try to locate the template file, starting from most specific
		foreach ( $templates as $template_path ) {

			$located = wc_locate_template( $template_path );

			if ( $located && file_exists( $located ) ) {

				$located_template = $template_path;
				break;
			}
		}

		return $located_template;
	}


	/**
	 * Reset current email template
	 *
	 * @since 1.0.0
	 * @param string $template Deleted template type
	 * @param string $email Email object name
	 */
	public function reset_templates( $template, $email ) {

		if ( strtolower( $email ) === $this->id ) {

			if ( 'plain' === $template ) {
				$this->template_plain = $this->locate_email_template( 'plain' );
			} elseif ( 'html' === $template ) {
				$this->template_html  = $this->locate_email_template();
			} else {
				$this->template_html  = $this->locate_email_template();
				$this->template_plain = $this->locate_email_template( 'plain' );
			}
		}
	}


	/**
	 * Check conditions and trigger email
	 *
	 * @since 1.0.0
	 * @param int $order_id
	 * @param string $old_status
	 * @param string $new_status
	 */
	public function maybe_trigger( $order_id, $old_status, $new_status ) {

		// trigger email when a new order is created
		if ( 'yes' === $this->dispatch_on_new_order && 'new' === $new_status ) {

			$this->trigger( $order_id );

			// email has been triggered, so ignore all other conditions
			return;
		}

		// Possible conditions that the current status changes creates
		$status_changes = array(
			"{$old_status}_to_{$new_status}",
			"{$old_status}_to_any",
			"any_to_{$new_status}",
		);

		if ( is_array( $this->dispatch_conditions ) ) {

			// Try to find a match between current changes and the dispatch conditions
			foreach ( $this->dispatch_conditions as $condition ) {

				if ( in_array( $condition, $status_changes, false ) ) {

					// Only trigger email once, even if multiple conditions match
					$this->trigger( $order_id );
					break;
				}
			}
		}
	}


}

endif;
