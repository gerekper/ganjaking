<?php
/**
 * Back in stock email class
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Wishlist
 * @version 3.0.0
 */

if ( ! defined( 'YITH_WCWL' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCWL_Back_In_Stock_Email' ) ) {
	/**
	 * Back in stock email
	 *
	 * @since 1.0.0
	 */
	class YITH_WCWL_Back_In_Stock_Email extends WC_Email {

		/**
		 * Receiver user
		 *
		 * @var \WP_User
		 */
		public $user = null;

		/**
		 * Items that will be used for product table rendering
		 *
		 * @var \YITH_WCWL_Wishlist_Item[]
		 */
		public $items = array();

		/**
		 * True when the email notification is sent to customers.
		 *
		 * @var bool
		 */
		protected $customer_email = true;

		/**
		 * Constructor method, used to return object of the class to WC
		 *
		 * @return \YITH_WCWL_Back_In_Stock_Email
		 * @since 3.0.0
		 */
		public function __construct() {
			$this->id          = 'yith_wcwl_back_in_stock';
			$this->title       = __( 'Wishlist "Back in stock" email', 'yith-woocommerce-wishlist' );
			$this->description = __( 'This email is sent to customers when an item of their wishlist is back in stock', 'yith-woocommerce-wishlist' );

			$this->heading = __( 'An item of your wishlist is back in stock!', 'yith-woocommerce-wishlist' );
			$this->subject = __( 'An item of your wishlist is back in stock!', 'yith-woocommerce-wishlist' );

			$this->content_html = $this->get_option( 'content_html' );
			$this->content_text = $this->get_option( 'content_text' );

			$this->template_html  = 'emails/back-in-stock.php';
			$this->template_plain = 'emails/plain/back-in-stock.php';

			// Triggers for this email.
			add_action( 'send_back_in_stock_mail_notification', array( $this, 'trigger' ), 10, 2 );

			// Call parent constructor.
			parent::__construct();
		}

		/**
		 * Method triggered to send email
		 *
		 * @param \WP_User                   $user  User object.
		 * @param \YITH_WCWL_Wishlist_Item[] $items List of wishlist items.
		 *
		 * @return void
		 * @since 3.0.0
		 */
		public function trigger( $user, $items ) {
			$this->user = $user;

			if ( ! $user || is_wp_error( $user ) ) {
				return;
			}

			$this->recipient = $user->user_email;

			if ( ! empty( $items ) ) {
				foreach ( $items as $product_id => $item_id ) {
					// retrieve item.
					$item = YITH_WCWL_Wishlist_Factory::get_wishlist_item( $item_id );
					if ( $item ) {
						$product_exclusions  = $this->get_option( 'product_exclusions', array() );
						$category_exclusions = $this->get_option( 'category_exclusions', array() );
						$product             = $item->get_product();
						if ( $product ) {
							$product_categories = $product->get_category_ids();

							if ( in_array( $product_id, $product_exclusions ) || array_intersect( $product_categories, $category_exclusions ) ) {
								continue;
							}

							$this->items[ $product_id ] = $item;
						}
					}
				}
			}

			if ( ! $this->items ) {
				return;
			}

			$this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
		}

		/**
		 * Get HTML content for the mail
		 *
		 * @return string HTML content of the mail
		 * @since 2.0.7
		 */
		public function get_content_html() {
			ob_start();
			wc_get_template(
				$this->template_html,
				array(
					'email'         => $this,
					'email_heading' => $this->get_heading(),
					'email_content' => $this->get_custom_content_html(),
					'sent_to_admin' => false,
					'plain_text'    => false,
				)
			);

			return ob_get_clean();
		}

		/**
		 * Get plain text content of the mail
		 *
		 * @return string Plain text content of the mail
		 * @since 2.0.7
		 */
		public function get_content_plain() {
			ob_start();
			wc_get_template(
				$this->template_plain,
				array(
					'email'         => $this,
					'email_heading' => $this->get_heading(),
					'email_content' => $this->get_custom_content_plain(),
					'sent_to_admin' => false,
					'plain_text'    => true,
				)
			);

			return ob_get_clean();
		}

		/**
		 * Retrieve custom email HTML content
		 *
		 * @return string custom content, with replaced values
		 * @since 3.0.0
		 */
		public function get_custom_content_html() {
			$find = array(
				'user_name'        => '{user_name}',
				'user_email'       => '{user_email}',
				'user_first_name'  => '{user_first_name}',
				'user_last_name'   => '{user_last_name}',
				'products_table'   => '{products_table}',
				'unsubscribe_link' => '{unsubscribe_link}',
			);

			$replace = array(
				'user_name'        => $this->user->user_login,
				'user_email'       => $this->user->user_email,
				'user_first_name'  => $this->user->billing_first_name,
				'user_last_name'   => $this->user->billing_last_name,
				'products_table'   => $this->get_products_table(),
				'unsubscribe_link' => sprintf( '<a href="%s">%s</a>', YITH_WCWL()->get_unsubscribe_link( $this->user->ID ), apply_filters( 'yith_wcwl_unsubscribe_link_label', __( 'unsubscribe', 'yith-woocommerce-wishlist' ), $this ) ),
			);

			if ( version_compare( wc()->version, '3.2.0', '>=' ) ) {
				$this->placeholders = array_merge(
					$this->placeholders,
					array_combine( array_values( $find ), array_values( $replace ) )
				);
			} else {
				$this->find    = array_merge( $this->find, $find );
				$this->replace = array_merge( $this->replace, $replace );
			}

			$custom_content = apply_filters( 'yith_wcwl_custom_html_content_' . $this->id, $this->format_string( stripcslashes( $this->content_html ) ), $this->object );

			return $custom_content;
		}

		/**
		 * Retrieve custom email text content
		 *
		 * @return string custom content, with replaced values
		 * @since 3.0.0
		 */
		public function get_custom_content_plain() {
			$find = array(
				'user_name'       => '{user_name}',
				'user_email'      => '{user_email}',
				'user_first_name' => '{user_first_name}',
				'user_last_name'  => '{user_last_name}',
				'products_list'   => '{products_list}',
				'unsubscribe_url' => '{unsubscribe_url}',
			);

			$replace = array(
				'user_name'       => $this->user->user_login,
				'user_email'      => $this->user->user_email,
				'user_first_name' => $this->user->billing_first_name,
				'user_last_name'  => $this->user->billing_last_name,
				'product_list'    => $this->get_products_table( true ),
				'unsubscribe_url' => YITH_WCWL()->get_unsubscribe_link( $this->user->ID ),
			);

			if ( version_compare( wc()->version, '3.2.0', '>=' ) ) {
				$this->placeholders = array_merge(
					$this->placeholders,
					array_combine( array_values( $find ), array_values( $replace ) )
				);
			} else {
				$this->find    = array_merge( $this->find, $find );
				$this->replace = array_merge( $this->replace, $replace );
			}

			$custom_content = apply_filters( 'yith_wcwl_custom_text_content_' . $this->id, $this->format_string( stripcslashes( $this->content_text ) ), $this->object );

			return $custom_content;
		}

		/**
		 * Retrieve template for product table, to be used in custom emails
		 *
		 * @param bool $plain_text Whether tempalte should be rendered as plain text (instad of HTML).
		 *
		 * @return string Rendered template
		 */
		public function get_products_table( $plain_text = false ) {
			if ( $plain_text ) {
				return yith_wcwl_get_template( 'emails/plain/products-list.php', array( 'items' => $this->items ), true );
			} else {
				return yith_wcwl_get_template( 'emails/products-table.php', array( 'items' => $this->items ), true );
			}
		}

		/**
		 * Init fields that will store admin preferences
		 *
		 * @return void
		 */
		public function init_form_fields() {
			$product_categories = get_terms(
				array(
					'taxonomy'   => 'product_cat',
					'hide_empty' => true,
					'number'     => 0,
					'fields'     => 'id=>name'
				)
			);

			$saved_exclusions   = $this->get_option( 'product_exclusions', array() );
			$exclusions_options = array();

			if ( ! empty( $saved_exclusions ) ) {
				foreach ( $saved_exclusions as $product_id ) {
					$product = wc_get_product( $product_id );

					if ( ! $product ) {
						continue;
					}

					$exclusions_options[ $product_id ] = $product->get_formatted_name();
				}
			}

			$this->form_fields = array(
				'enabled' => array(
					'title'   => __( 'Enable/Disable', 'yith-woocommerce-wishlist' ),
					'type'    => 'checkbox',
					'label'   => __( 'Enable this email notification', 'yith-woocommerce-wishlist' ),
					'default' => 'no',
				),

				'subject' => array(
					'title'       => __( 'Subject', 'yith-woocommerce-wishlist' ),
					'type'        => 'text',
					// translators: 1. Default subject.
					'description' => sprintf( __( 'This field lets you modify the email subject line. Leave blank to use the default subject: <code>%s</code>.', 'yith-woocommerce-wishlist' ), $this->subject ),
					'placeholder' => '',
					'default'     => '',
				),

				'heading' => array(
					'title'       => __( 'Email Heading', 'yith-woocommerce-wishlist' ),
					'type'        => 'text',
					// translators: 1. Default email heading.
					'description' => sprintf( __( 'This field lets you modify the main heading contained within the email notification. Leave blank to use the default heading: <code>%s</code>.', 'yith-woocommerce-wishlist' ), $this->heading ),
					'placeholder' => '',
					'default'     => '',
				),

				'product_exclusions' => array(
					'title'       => __( 'Product exclusions', 'yith-wooommerce-wishlist' ),
					'type'        => 'multiselect',
					'description' => __( 'Select the products for which you don\'t want to send a reminder email', 'yith-woocommerce-wishlist' ),
					'class'       => 'wc-product-search',
					'options'     => $exclusions_options,
				),

				'category_exclusions' => array(
					'title'       => __( 'Category exclusions', 'yith-wooommerce-wishlist' ),
					'type'        => 'multiselect',
					'class'       => 'wc-enhanced-select',
					'description' => __( 'Select the product categories for which you don\'t want to send a reminder email', 'yith-woocommerce-wishlist' ),
					'options'     => $product_categories,
				),

				'email_type' => array(
					'title'       => __( 'Email type', 'yith-woocommerce-wishlist' ),
					'type'        => 'select',
					'description' => __( 'Choose which type of email to send.', 'yith-woocommerce-wishlist' ),
					'default'     => 'html',
					'class'       => 'email_type',
					'options'     => array(
						'plain'     => __( 'Plain text', 'yith-woocommerce-wishlist' ),
						'html'      => __( 'HTML', 'yith-woocommerce-wishlist' ),
						'multipart' => __( 'Multipart', 'yith-woocommerce-wishlist' ),
					),
				),

				'content_html' => array(
					'title'       => __( 'Email HTML Content', 'yith-woocommerce-wishlist' ),
					'type'        => 'textarea',
					'description' => __( 'This field lets you modify the main content of the HTML email. You can use the following placeholders: <code>{user_name}</code> <code>{user_email}</code> <code>{user_first_name}</code> <code>{user_last_name}</code> <code>{products_table}</code> <code>{unsubscribe_link}</code>', 'yith-woocommerce-wishlist' ),
					'placeholder' => '',
					'css'         => 'min-height: 250px;',
					'default'     => self::get_default_content( 'html' ),
				),

				'content_text' => array(
					'title'       => __( 'Email text content', 'yith-woocommerce-wishlist' ),
					'type'        => 'textarea',
					'description' => __( 'This field lets you modify the main content of the text email. You can use the following placeholders: <code>{user_name}</code> <code>{user_email}</code> <code>{user_first_name}</code> <code>{user_last_name}</code> <code>{products_list}</code> <code>{unsubscribe_url}</code>', 'yith-woocommerce-wishlist' ),
					'placeholder' => '',
					'css'         => 'min-height: 250px;',
					'default'     => self::get_default_content( 'plain' ),
				),
			);
		}

		/**
		 * Returns default content for the email
		 *
		 * @param string $email_type Email type.
		 *
		 * @return string Default content.
		 */
		public static function get_default_content( $email_type ) {
			if ( 'plain' == $email_type ) {
				return __(
					"Hi {user_name}
A product of your wishlist is back in stock!

{products_list}

We only have limited stock, so don't wait any longer, and take this chance to make it yours!

****************************************************

If you don't want to receive any further notification, please follow this link [{unsubscribe_url}]

",
					'yith-woocommerce-wishlist'
				);
			} else {
				return __(
					"<p>Hi {user_name}</p>
<p>A product of your wishlist is back in stock!</p>
{products_table}
<p>We only have limited stock, so don't wait any longer, and take this chance to make it yours!</p>
<p><small>If you don't want to receive any further notification, please {unsubscribe_link}</small></p>",
					'yith-woocommerce-wishlist'
				);
			}
		}
	}
}

// returns instance of the mail on file include.
return new YITH_WCWL_Back_In_Stock_Email();
