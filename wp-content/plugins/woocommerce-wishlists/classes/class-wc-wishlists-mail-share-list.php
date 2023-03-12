<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WC_Wishlists_Mail_Share_List' ) ) :

	/**
	 * Wish List Email Sharing.
	 *
	 * An email sent to a wishlist owner to verify email.
	 *
	 * @class       WC_Wishlists_Mail_Share_List
	 * @version     1.0.0
	 * @extends     WC_Email
	 */
	class WC_Wishlists_Mail_Share_List extends WC_Email {

		public $additional_content;
		public $from_name;

		/**
		 * Constructor.
		 */
		public function __construct() {
			$this->id             = 'wc_wishlist_email_share_list';
			$this->title          = __( 'Wishlist Sharing', 'wc_wishlist' );
			$this->description    = __( 'Email sent to customers when someone shares a list', 'wc_wishlist' );
			$this->customer_email = true;
			$this->template_html  = 'emails/wishlist-email-share-list.php';
			$this->template_plain = 'emails/plain/wishlist-email-share-list.php';

			$this->placeholders = array(
				'{list_title}'      => '',
				'{list_url}'        => '',
				'{list_first_name}' => '',
				'{list_last_name}'  => '',
				'{list_email}'      => '',
				'{list_url}'        => '',
			);

			$this->template_base = WC_Wishlists_Plugin::plugin_path() . '/templates/';


			// Call parent constructor
			parent::__construct();

		}

		/**
		 * Get email subject.
		 *
		 * @since  3.1.0
		 * @return string
		 */
		public function get_default_subject() {
			return sprintf( __( 'A wishlist has been shared with you from %s', 'wc_wishlist' ), get_bloginfo( 'name' ) );
		}

		/**
		 * Get email heading.
		 *
		 * @since  3.1.0
		 * @return string
		 */
		public function get_default_heading() {
			return __( 'A wishlist has been shared with you', 'wc_wishlist' );
		}

		/**
		 * Trigger the sending of this email.
		 *
		 * @param int $order_id The order ID.
		 * @param WC_Order $order Order object.
		 */
		public function trigger( $address, $wishlist, $wishlist_id, $additional_content, $from_name ) {
			$this->setup_locale();

			$this->recipient = $address;
			$this->from_name = $from_name;

			$this->additional_content                = $additional_content;
			$this->object                            = $wishlist;
			$this->placeholders['{list_title}']      = get_the_title( $this->object->post->ID );
			$this->placeholders['{list_date}}']      = $this->object->post->post_date;
			$this->placeholders['{list_first_name}'] = get_post_meta( $wishlist_id, '_wishlist_first_name', true );
			$this->placeholders['{list_last_name}']  = get_post_meta( $wishlist_id, '_wishlist_last_name', true );
			$this->placeholders['{list_email}']      = get_post_meta( $wishlist_id, '_wishlist_email', true );
			$this->placeholders['{list_url}']        = get_permalink( $wishlist_id );

			if ( $this->is_enabled() && $this->get_recipient() ) {
				return $this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
			}

			$this->restore_locale();
		}

		/**
		 * Get content html.
		 *
		 * @access public
		 * @return string
		 */
		public function get_content_html() {

			$additional_content = $this->get_additional_content();

			return wc_get_template_html( $this->template_html, array(
				'wishlist'           => $this->object,
				'additional_content' => $this->additional_content . '<p>' .  $additional_content . '</p>',
				'name'               => $this->from_name,
				'email_heading'      => $this->get_heading(),
				'sent_to_admin'      => false,
				'plain_text'         => false,
				'email'              => $this,
			), '', WC_Wishlists_Plugin::plugin_path() . '/templates/' );
		}

		/**
		 * Get content plain.
		 *
		 * @access public
		 * @return string
		 */
		public function get_content_plain() {
			return wc_get_template_html( $this->template_plain, array(
				'wishlist'           => $this->object,
				'additional_content' => $this->additional_content,
				'name'               => $this->from_name,
				'email_heading'      => $this->get_heading(),
				'sent_to_admin'      => false,
				'plain_text'         => false,
				'email'              => $this,
			), '', WC_Wishlists_Plugin::plugin_path() . '/templates/' );
		}

		/**
		 * Initialise settings form fields.
		 */
		public function init_form_fields() {
			$placeholder_text  = sprintf( __( 'Available placeholders: %s', 'woocommerce' ), '<code>' . esc_html( implode( '</code>, <code>', array_keys( $this->placeholders ) ) ) . '</code>' );
			$this->form_fields = array(
				'enabled'    => array(
					'title'   => __( 'Enable/Disable', 'woocommerce' ),
					'type'    => 'checkbox',
					'label'   => __( 'Enable this email notification', 'woocommerce' ),
					'default' => 'yes',
				),
				'subject'    => array(
					'title'       => __( 'Default Subject', 'woocommerce' ),
					'type'        => 'text',
					'desc_tip'    => true,
					/* translators: %s: list of placeholders */
					'description' => sprintf( __( 'Available placeholders: %s', 'woocommerce' ), '<code>{site_title}, {order_date}, {order_number}</code>' ),
					'placeholder' => $this->get_default_subject(),
					'default'     => '',
				),
				'heading'    => array(
					'title'       => __( 'Email heading', 'woocommerce' ),
					'type'        => 'text',
					'desc_tip'    => true,
					/* translators: %s: list of placeholders */
					'description' => sprintf( __( 'Available placeholders: %s', 'woocommerce' ), '<code>{site_title}, {order_date}, {order_number}</code>' ),
					'placeholder' => $this->get_default_heading(),
					'default'     => '',
				),
				'additional_content' => array(
					'title'       => __( 'Additional content', 'woocommerce' ),
					'description' => __( 'Default content to appear below the users email content.', 'woocommerce' ) . ' ' . $placeholder_text,
					'css'         => 'width:400px; height: 75px;',
					'placeholder' => __( '', 'woocommerce' ),
					'type'        => 'textarea',
					'default'     => $this->get_default_additional_content(),
					'desc_tip'    => true,
				),
				'email_type' => array(
					'title'       => __( 'Email type', 'woocommerce' ),
					'type'        => 'select',
					'description' => __( 'Choose which format of email to send.', 'woocommerce' ),
					'default'     => 'html',
					'class'       => 'email_type wc-enhanced-select',
					'options'     => $this->get_email_type_options(),
					'desc_tip'    => true,
				),
			);
		}
	}

endif;
