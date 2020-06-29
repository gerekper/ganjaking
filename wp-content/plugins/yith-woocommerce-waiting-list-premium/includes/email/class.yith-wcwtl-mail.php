<?php
/**
 * Plugin email common class
 *
 * @author  YITH
 * @package YITH WooCommerce Waiting List
 * @version 1.0.0
 */

if ( ! defined( 'YITH_WCWTL' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCWTL_Mail' ) ) {
	/**
	 * Email Class
	 * Extend WC_Email to waitlist email
	 *
	 * @class    YITH_WCWTL_Mail_Instock
	 * @extends  WC_Email
	 */
	class YITH_WCWTL_Mail extends WC_Email {

		/**
		 * Remove subscribe url
		 * @var string
		 */
		public $remove_url = '';

		/**
		 * The mail content
		 * @var string
		 */
		public $mail_content = '';

		/**
		 * @return string|void
		 */
		public function init_form_fields() {

			parent::init_form_fields();

			unset( $this->form_fields['additional_content'] );

			$this->form_fields['mail_content'] = array(
				'title'       => __( 'Email content', 'yith-woocommerce-waiting-list' ),
				'type'        => 'yith_wcwtl_textarea',
				'description' => sprintf( __( 'Defaults to <code>%s</code>', 'yith-woocommerce-waiting-list' ), $this->mail_content ),
				'placeholder' => '',
				'default'     => $this->mail_content,
			);
			$this->form_fields['show_thumb']   = array(
				'title'       => __( 'Show product thumbnail', 'yith-woocommerce-waiting-list' ),
				'type'        => 'checkbox',
				'description' => __( 'Enable this option to show the product thumbnail in the email', 'yith-woocommerce-waiting-list' ),
				'default'     => 'yes',
			);
		}

		/**
		 * Return YITh Texteditor HTML.
		 *
		 * @param $key
		 * @param $data
		 * @return string
		 */
		public function generate_yith_wcwtl_textarea_html( $key, $data ) {
			// get html
			$html = yith_waitlist_textarea_editor_html( $key, $data, $this );

			return $html;
		}

		/**
		 * Trigger Function
		 *
		 * @access public
		 * @since  1.0.0
		 * @author Francesco Licandro <francesco.licandro@yithemes.com>
		 * @param integer $product_id Product id
		 * @param mixed   $users      Waitlist users array
		 * @return void
		 */
		public function trigger( $users, $product_id ) {

			$this->object = wc_get_product( $product_id );

			if ( ! $this->is_enabled() || ! $this->object ) {
				return;
			}

			$response = false;

			if ( ! is_array( $users ) ) {
				$users = explode( ',', $users );
			}

			foreach ( $users as $user ) {
				$placeholders = apply_filters( 'yith_wcwtl_email_custom_placeholders', array(
					'{product_title}' => $this->object->get_name(),
					'{blogname}'      => $this->get_blogname(),
				), $this->object, $user );

				$this->set_placeholders( $placeholders );

				$response = $this->send( $user, $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
				do_action( 'restore_language' );
			}

			if ( $response ) {
				add_filter( "{$this->id}_send_response", '__return_true' );
			}
		}

		/**
		 * Set plugin custom placeholders for email
		 *
		 * @since  1.5.0
		 * @author Francesco Licandro
		 * @param array $placeholders
		 */
		public function set_placeholders( $placeholders ) {
			foreach ( $placeholders as $placeholder_key => $placeholder_value ) {
				if ( version_compare( WC()->version, '3.2.0', '>=' ) ) {
					$this->placeholders[ $placeholder_key ] = $placeholder_value;
				} else {
					$this->find[ $placeholder_key ]    = $placeholder_key;
					$this->replace[ $placeholder_key ] = $this->object->get_name();
				}
			}
		}

		/**
		 * Send mail using standard WP Mail or Mandrill Service
		 *
		 * @access public
		 * @since  1.0.0
		 * @author Francesco Licandro <francesco.licandro@yithemes.com>
		 * @param string $subject
		 * @param string $message
		 * @param string $headers
		 * @param array  $attachments
		 *
		 * @param string $to
		 * @return bool
		 */
		public function send( $to, $subject, $message, $headers, $attachments ) {
			if ( get_option( 'yith-wcwtl-use-mandrill' ) != 'yes' ) {
				return parent::send( $to, $subject, $message, $headers, $attachments );
			} else {
				return yith_waitlist_mandrill_mail( $to, $subject, $message, $headers, $attachments, $this );
			}
		}

		/**
		 * get custom email content from options
		 *
		 * @access public
		 * @since  1.0.0
		 * @author Francesco Licandro <francesco.licandro@yithemes.com>
		 * @return string
		 */
		public function get_custom_option_content() {
			$content = $this->get_option( 'mail_content' );

			return $this->format_string( $content );
		}

		/**
		 * get_content_html function.
		 *
		 * @access public
		 * @since  1.0.0
		 * @author Francesco Licandro <francesco.licandro@yithemes.com>
		 * @return string
		 */
		public function get_content_html() {

			$product_url = $this->object instanceof WC_Product ? $this->object->get_permalink() : '';

			if ( $this->get_option( 'show_thumb' ) == 'yes' ) {
				$dimensions = wc_get_image_size( 'shop_catalog' );
				$height     = esc_attr( $dimensions['height'] );
				$width      = esc_attr( $dimensions['width'] );
				$src_image  = $this->object instanceof WC_Product && $this->object->get_image_id() ? wp_get_attachment_image_src( $this->object->get_image_id(), 'shop_catalog' ) : false;
				$src        = is_array( $src_image ) ? current( $src_image ) : wc_placeholder_img_src();

				$image = '<a href="' . $product_url . '"><img src="' . $src . '" height="' . $height . '" width="' . $width . '" /></a>';
			} else {
				$image = '';
			}

			$args = apply_filters( "{$this->id}_args", array(
				'product_link'  => $product_url,
				'product_thumb' => $image,
				'email_heading' => $this->get_heading(),
				'email_content' => $this->get_custom_option_content(),
				'email'         => $this,
			), $this->object );

			ob_start();

			wc_get_template( $this->template_html, $args, false, $this->template_base );

			return ob_get_clean();
		}

		/**
		 * get_content_plain function.
		 *
		 * @access public
		 * @since  1.0.0
		 * @author Francesco Licandro <francesco.licandro@yithemes.com>
		 * @return string
		 */
		public function get_content_plain() {

			$args = apply_filters( "{$this->id}_plain_args", array(
				'product_title' => $this->object->get_name(),
				'product_link'  => $this->object->get_permalink(),
				'email_heading' => $this->get_heading(),
			), $this->object );

			ob_start();

			wc_get_template( $this->template_plain, $args, false, $this->template_base );

			return ob_get_clean();
		}
	}
}