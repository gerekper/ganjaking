<?php
/**
 * Plugin Name: WooCommerce Product Enquiry Form
 * Plugin URI: https://woocommerce.com/products/product-enquiry-form/
 * Description: Adds an enquiry form tab to certain product pages which allows customers to contact you about a product. Also includes optional reCAPTCHA for preventing spam.
 * Version: 1.2.14
 * Author: WooCommerce
 * Author URI: https://woocommerce.com/
 * Requires at least: 3.1
 * Tested up to: 5.3
 * WC requires at least: 2.6
 * WC tested up to: 4.2
 * Text Domain: wc_enquiry_form
 * Domain Path: /languages
 * Woo: 18601:5a0f5d72519a8ffcc86669f042296937
 *
 * Copyright: © 2020 WooCommerce
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */

/**
 * WooCommerce fallback notice.
 *
 * @since 1.2.13
 */
function woocommerce_product_enquiry_form_missing_wc_notice() {
	/* translators: %s WC download URL link. */
	echo '<div class="error"><p><strong>' . sprintf( esc_html__( 'Product Enquiry Form requires WooCommerce to be installed and active. You can download %s here.', 'wc_enquiry_form' ), '<a href="https://woocommerce.com/" target="_blank">WooCommerce</a>' ) . '</strong></p></div>';
}

if ( ! class_exists( 'WC_Product_Enquiry_Form' ) ) :
	class WC_Product_Enquiry_Form {
		var $send_to;
		var $settings;
		private $public_key;
		private $private_key;
		private $recaptcha_version;

		/**
		 * __construct function.
		 *
		 * @access public
		 * @return void
		 */
		public function __construct() {

			$this->send_to = get_option( 'woocommerce_product_enquiry_send_to' );

			$this->settings_tabs = array(
				'product_enquiry' => __( 'Product Enquiries', 'wc_enquiry_form' )
			);

			$this->public_key        = get_option( 'woocommerce_recaptcha_public_key', '' );
			$this->private_key       = get_option( 'woocommerce_recaptcha_private_key', '' );
			$this->recaptcha_version = get_option( 'woocommerce_recaptcha_version', 'v2' );

			// Init settings
			$this->settings = array(
				array( 'name' => __( 'Product Enquiries', 'wc_enquiry_form' ), 'type' => 'title', 'desc' => '', 'id' => 'product_enquiry' ),
				array(
					'name'    => __('Product enquiry email', 'wc_enquiry_form'),
					'desc'    => __('Where to send product enquiries.', 'wc_enquiry_form'),
					'id'      => 'woocommerce_product_enquiry_send_to',
					'type'    => 'text',
					'default' => get_option('admin_email'),
				),
				array(
					'name'    => __('ReCaptcha site key', 'wc_enquiry_form'),
					'desc'    => __('Enter your key if you wish to use <a href="https://www.google.com/recaptcha/">recaptcha</a> on the product enquiry form.', 'wc_enquiry_form'),
					'id'      => 'woocommerce_recaptcha_public_key',
					'type'    => 'text',
					'default' => '',
				),
				array(
					'name'    => __('ReCaptcha secret key', 'wc_enquiry_form'),
					'desc'    => __('Enter your key if you wish to use <a href="https://www.google.com/recaptcha/">recaptcha</a> on the product enquiry form.', 'wc_enquiry_form'),
					'id'      => 'woocommerce_recaptcha_private_key',
					'type'    => 'text',
					'default' => '',
				),
				array(
					'name' => __( 'ReCaptcha version', 'wc_enquiry_form' ),
					'desc' => __( 'Choose the ReCaptcha version you would like to use. Click for more information on <a href="https://developers.google.com/recaptcha/docs/versions" target="_blank">different versions</a>', 'wc_enquiry_form' ),
					'id'   => 'woocommerce_recaptcha_version',
					'type' => 'select',
					'options' => array(
						'v3' => 'v3',
						'v2' => 'v2',
					),
					'default'  => 'v2',
				),
				array( 'type' => 'sectionend', 'id' => 'product_enquiry'),
			);

			if ( is_admin() ) {
				require_once 'class-wc-product-enquiry-privacy.php';
			}

			// Default options
			add_option( 'woocommerce_product_enquiry_send_to', get_option( 'admin_email' ) );

			// Settings
			if ( defined( 'WC_VERSION' ) && WC_VERSION > '2.2.0' ) {
				add_action( 'woocommerce_get_sections_products', array( $this, 'add_settings_tab' ) );
				add_action( 'woocommerce_get_settings_products', array( $this, 'add_settings_section' ), null, 2 );
			} else {
				add_action( 'woocommerce_settings_general_options_after', array( $this, 'admin_settings' ) );
			}

			add_action( 'woocommerce_update_options_catalog', array( $this, 'save_admin_settings' ) );

			/* 2.1 */
			add_action( 'woocommerce_update_options_general', array( $this, 'save_admin_settings' ) );

			// Frontend
			if ( version_compare( WOOCOMMERCE_VERSION, '2.0', '<' ) ) {
				add_action( 'woocommerce_product_tabs', array( $this, 'product_enquiry_tab' ), 25 );
				add_action( 'woocommerce_product_tab_panels', array( $this, 'product_enquiry_tab_panel' ), 25 );
			} else {
				add_filter( 'woocommerce_product_tabs', array( $this, 'add_product_enquiry_tab' ), 25 );
			}

			// AJAX
			add_action( 'wp_ajax_woocommerce_product_enquiry_post', array( $this, 'process_form' ) );
			add_action( 'wp_ajax_nopriv_woocommerce_product_enquiry_post', array( $this, 'process_form' ) );

			// Write panel
			add_action( 'woocommerce_product_options_general_product_data', array( $this, 'write_panel' ) );
			add_action( 'woocommerce_process_product_meta', array( $this, 'write_panel_save' ) );

			// Enqueue Google reCAPTCHA scripts
			add_action( 'wp_enqueue_scripts', array( $this, 'recaptcha_scripts' ) );
		}

		/**
		 * function recaptcha_scripts
		 * Queues recaptcha JS script if enabled
		 *
		 */
		public function recaptcha_scripts() {
			if ( $this->public_key && $this->private_key ) {
				// Only display on product page.
				if ( ! is_product() ) {
					return;
				}

				if ( 'v3' === $this->recaptcha_version ) {
					wp_enqueue_script( 'recaptcha', 'https://www.google.com/recaptcha/api.js?render=' . esc_js( $this->public_key ) );
				} else {
					wp_enqueue_script( 'recaptcha', 'https://www.google.com/recaptcha/api.js' );
				}
			}
		}

		/**
		 * Add the settings for the new "Product Enquiries" subtab.
		 * @access public
		 * @since  1.3.0
		 * @return  void
		 */
		public function add_settings_section( $settings, $current_section ) {
			if ( 'product_enquiry' == $current_section ) {
				$settings = $this->settings;
			}
			return $settings;
		}

		/**
		 * Add a new "Product Enquiries" subtab to the "Products" tab.
		 * @access public
		 * @since  1.3.0
		 * @return  void
		 */
		public function add_settings_tab( $sections ) {
			$sections = array_merge( $sections, $this->settings_tabs );
			return $sections;
		}

		/**
		 * add_product_enquiry_tab function.
		 *
		 * @access public
		 * @param array $tabs (default: array())
		 * @return void
		 */
		function add_product_enquiry_tab( $tabs = array() ) {
			global $post, $woocommerce;

			if ( $post && get_post_meta( $post->ID, 'woocommerce_disable_product_enquiry', true ) == 'yes' )
				return $tabs;

			$tabs['product_enquiry'] = array(
				'title'    => apply_filters( 'product_enquiry_tab_title', __( 'Product Enquiry', 'wc_enquiry_form' ) ),
				'priority' => 40,
				'callback' => array( $this, 'add_product_enquiry_tab_content' )
			);

			return $tabs;
		}

		/**
		 * add_product_enquiry_tab_content function.
		 *
		 * @access public
		 * @return void
		 */
		function add_product_enquiry_tab_content() {
			global $post, $woocommerce;

			if ( is_user_logged_in() )
				$current_user = get_user_by( 'id', get_current_user_id() );
			?>
				<h2><?php echo apply_filters( 'product_enquiry_heading', __( 'Product Enquiry', 'wc_enquiry_form' ) ); ?></h2>

				<form action="" method="post" id="product_enquiry_form">

					<?php do_action( 'product_enquiry_before_form' ); ?>

					<p class="form-row form-row-first">
						<label for="product_enquiry_name"><?php _e( 'Name', 'wc_enquiry_form' ); ?></label>
						<input type="text" class="input-text" name="product_enquiry_name" id="product_enquiry_name" placeholder="<?php _e('Your name', 'wc_enquiry_form'); ?>" value="<?php if ( isset( $current_user ) ) echo $current_user->user_nicename; ?>" />
					</p>

					<p class="form-row form-row-last">
						<label for="product_enquiry_email"><?php _e( 'Email address', 'wc_enquiry_form' ); ?></label>
						<input type="text" class="input-text" name="product_enquiry_email" id="product_enquiry_email" placeholder="<?php _e('you@yourdomain.com', 'wc_enquiry_form'); ?>" value="<?php if ( isset( $current_user ) ) echo $current_user->user_email; ?>" />
					</p>

					<div class="clear"></div>

					<?php do_action('product_enquiry_before_message'); ?>

					<p class="form-row notes">
						<label for="product_enquiry_message"><?php _e( 'Enquiry', 'wc_enquiry_form' ); ?></label>
						<textarea class="input-text" name="product_enquiry_message" id="product_enquiry_message" rows="5" cols="20" placeholder="<?php _e( 'What would you like to know?', 'wc_enquiry_form' ); ?>"></textarea>
					</p>

					<?php do_action( 'product_enquiry_after_message' ); ?>

					<div class="clear"></div>

					<?php
					if ( 'v2' === $this->recaptcha_version ) {
						$this->display_recaptcha_v2_placeholder();
					}
					?>

					<p class="product_enquiry_button_container">
						<input type="hidden" name="product_id" value="<?php echo $post->ID; ?>" />
						<input type="submit" id="send_product_enquiry" value="<?php _e( 'Send Enquiry', 'wc_enquiry_form' ); ?>" class="button" />
					</p>

					<?php do_action( 'product_enquiry_after_form' ); ?>

				</form>
				<script type="text/javascript">
					jQuery(function() {
						var prepareForm = function() {
							// Remove errors
							jQuery('.product_enquiry_result').remove();

							// Required fields
							if (!jQuery('#product_enquiry_name').val()) {
								jQuery('#product_enquiry_form').before('<p style="display:none;" class="product_enquiry_result woocommerce_error woocommerce-error"><?php _e('Please enter your name.', 'wc_enquiry_form'); ?></p>');
								jQuery('.product_enquiry_result').fadeIn();
								return false;
							}

							if (!jQuery('#product_enquiry_email').val()) {
								jQuery('#product_enquiry_form').before('<p style="display:none;" class="product_enquiry_result woocommerce_error woocommerce-error"><?php _e('Please enter your email.', 'wc_enquiry_form'); ?></p>');
								jQuery('.product_enquiry_result').fadeIn();
								return false;
							}

							if (!jQuery('#product_enquiry_message').val()) {
								jQuery('#product_enquiry_form').before('<p style="display:none;" class="product_enquiry_result woocommerce_error woocommerce-error"><?php _e('Please enter your enquiry.', 'wc_enquiry_form'); ?></p>');
								jQuery('.product_enquiry_result').fadeIn();
								return false;
							}

							// Block elements
							jQuery('#product_enquiry_form').block({message: null, overlayCSS: {background: '#fff url(<?php echo $woocommerce->plugin_url(); ?>/assets/images/ajax-loader.gif) no-repeat center', opacity: 0.6}});

							// AJAX post
							var data = {
								action: 			'woocommerce_product_enquiry_post',
								security: 			'<?php echo wp_create_nonce("product-enquiry-post"); ?>',
								post_data:			jQuery('#product_enquiry_form').serialize()
							};

							jQuery.post( '<?php echo str_replace( array('https:', 'http:'), '', admin_url( 'admin-ajax.php' ) ); ?>', data, function(response) {
								if (response=='SUCCESS') {

									jQuery('#product_enquiry_form').before('<p style="display:none;" class="product_enquiry_result woocommerce_message woocommerce-message"><?php echo apply_filters('product_enquiry_success_message', __('Enquiry sent successfully. We will get back to you shortly.', 'wc_enquiry_form')); ?></p>');

									jQuery('#product_enquiry_form textarea').val('');

								} else {
									jQuery('#product_enquiry_form').before('<p style="display:none;" class="product_enquiry_result woocommerce_error woocommerce-error">' + response + '</p>');

								}

								<?php if ( 'v2' === $this->recaptcha_version ) { ?>
									// Reset ReCaptcha if in use.
									if ( typeof grecaptcha !== 'undefined' ) {
										grecaptcha.reset();
									}
								<?php } ?>

								jQuery('#product_enquiry_form').unblock();

								jQuery('.product_enquiry_result').fadeIn();

							});

							return false;
						};

						jQuery( '#send_product_enquiry' ).click( function( e ) {
							e.preventDefault();

							<?php if ( 'v3' === $this->recaptcha_version ) { ?>
								grecaptcha.ready( function() {
									grecaptcha.execute( '<?php echo esc_js( $this->public_key ); ?>', { action: 'ecommerce' } ).then( function( token ) {
										jQuery( '#product_enquiry_form' ).prepend( '<input type="hidden" name="g-recaptcha-response" value="' + token + '">');

										prepareForm();
									} );
								} );
							<?php } else { ?>
								prepareForm();
							<?php } ?>
						});
					});
				</script>
			<?php
		}

		/**
		 * product_enquiry_tab function.
		 *
		 * @access public
		 * @return void
		 */
		public function product_enquiry_tab() {
			global $post, $woocommerce;

			if ( get_post_meta( $post->ID, 'woocommerce_disable_product_enquiry', true ) == 'yes' )
				return;

			?><li><a href="#tab-enquiry"><?php echo apply_filters( 'product_enquiry_tab_title', __( 'Product Enquiry', 'wc_enquiry_form' ) ); ?></a></li><?php
		}

		/**
		 * product_enquiry_tab_panel function.
		 *
		 * @access public
		 * @return void
		 */
		public function product_enquiry_tab_panel() {
			global $post, $woocommerce;

			if ( get_post_meta( $post->ID, 'woocommerce_disable_product_enquiry', true ) == 'yes' )
				return;
			?>
			<div class="panel" id="tab-enquiry">
				<?php $this->add_product_enquiry_tab_content(); ?>
			</div>
			<?php
		}

		/**
		 * process_form function processes the submitting form and sends the email.
		 *
		 * @access public
		 * @return void
		 *
		 * @version 1.2.3
		 */
		public function process_form() {
			global $woocommerce;

			check_ajax_referer( 'product-enquiry-post', 'security' );

			do_action( 'product_enquiry_process_form' );

			$post_data = array();
			parse_str( $_POST['post_data'], $post_data );

			$name               = isset( $post_data['product_enquiry_name'] ) ? wc_clean( $post_data['product_enquiry_name'] ) : '';
			$email              = isset( $post_data['product_enquiry_email'] ) ? wc_clean( $post_data['product_enquiry_email'] ) : '';
			$enquiry            = isset( $post_data['product_enquiry_message'] ) ? wc_clean( $post_data['product_enquiry_message'] ) : '';
			$product_id         = isset( $post_data['product_id'] ) ? (int) $post_data['product_id'] : 0;
			$recaptcha_response = isset( $post_data['g-recaptcha-response'] ) ? $post_data['g-recaptcha-response'] : '';

			if ( ! $product_id )
				die( __( 'Invalid product!', 'wc_enquiry_form' ) );

			if ( ! is_email( $email ) )
				die( __( 'Please enter a valid email.', 'wc_enquiry_form' ) );

			// Recaptcha
			if ( $this->public_key && $this->private_key ) {
				$response = wp_safe_remote_post( add_query_arg( array(
					'secret'   => $this->private_key,
					'response' => $recaptcha_response,
					'remoteip' => isset( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR']
				), 'https://www.google.com/recaptcha/api/siteverify' ) );

				if ( is_wp_error( $response ) || empty( $response['body'] ) || ! ( $json = json_decode( $response['body'] ) ) || ! $json->success ) {
					die( __('Please click on the anti-spam checkbox.', 'wc_enquiry_form') );
				}
			}

			$product 	= get_post( $product_id );
			$subject 	= apply_filters( 'product_enquiry_email_subject', sprintf( __( 'Product Enquiry - %s', 'wc_enquiry_form'), $product->post_title ) );

			$message                = array();
			$message['greet']       = __("Hello, ", 'wc_enquiry_form');
			$message['space_1']     = '';
			$message['intro']       = sprintf( __( "You have been contacted by %s (%s) about %s (%s). Their enquiry is as follows: ", 'wc_enquiry_form' ), $name, $email, $product->post_title, get_permalink( $product->ID ) );
			$message['space_2']     = '';
			$message['message']     = $enquiry;
			$message                = implode( "\n", apply_filters( 'product_enquiry_email_message', $message, $product_id, $name, $email ) );

			$headers = 'Reply-To: '. $email ."\r\n";

			$this->from_name    = $name;

			add_filter( 'wp_mail_from_name', array( $this, 'get_from_name' ) );

			if ( wp_mail( apply_filters( 'product_enquiry_send_to', $this->send_to, $product_id ), $subject, $message, $headers ) )
				echo 'SUCCESS';
			else
				echo 'Error';

			remove_filter( 'wp_mail_from_name', array( $this, 'get_from_name' ) );

			die();
		}

		/**
		 * From name for the email
		 */
		public function get_from_name() {
			return $this->from_name;
		}

		/**
		 * admin_settings function.
		 *
		 * @access public
		 * @return void
		 */
		public function admin_settings() {
			woocommerce_admin_fields( $this->settings );
		}

		/**
		 * save_admin_settings function.
		 *
		 * @access public
		 * @return void
		 */
		public function save_admin_settings() {
			woocommerce_update_options( $this->settings );
		}

		/**
		 * write_panel function.
		 *
		 * @access public
		 * @return void
		 */
		public function write_panel() {
			echo '<div class="options_group">';
			woocommerce_wp_checkbox( array( 'id' => 'woocommerce_disable_product_enquiry', 'label' => __( 'Disable enquiry form?', 'wc_enquiry_form' ) ) );
			echo '</div>';
		}

		/**
		 * write_panel_save function.
		 *
		 * @access public
		 * @param mixed $post_id
		 * @return void
		 */
		public function write_panel_save( $post_id ) {
			$woocommerce_disable_product_enquiry = isset( $_POST['woocommerce_disable_product_enquiry'] ) ? 'yes' : 'no';
			update_post_meta( $post_id, 'woocommerce_disable_product_enquiry', $woocommerce_disable_product_enquiry );
		}

		/**
		 * Generates the Google Recaptcha v2 HTML.
		 * Note that v3 does not require this.
		 *
		 * @since 1.2.11
		 * @return HTML
		 */
		public function display_recaptcha_v2_placeholder() {
			if ( $this->public_key && $this->private_key ) {
			?>
				<div class="form-row notes">
					<div class="g-recaptcha" data-sitekey="<?php echo esc_attr( $this->public_key ); ?>"></div>
				</div>
				<div class="clear"></div>
			<?php
			}
		}
	}
endif;

add_action( 'plugins_loaded', 'woocommerce_product_enquiry_form_init' );

/**
 * Initializes the extension.
 *
 * @since 1.2.13
 * @return void
 */
function woocommerce_product_enquiry_form_init() {
	load_plugin_textdomain( 'wc_enquiry_form', false, plugin_basename( dirname( __FILE__ ) ) . '/languages' );

	if ( ! class_exists( 'WooCommerce' ) ) {
		add_action( 'admin_notices', 'woocommerce_product_enquiry_form_missing_wc_notice' );
		return;
	}

	$GLOBALS['WC_Product_Enquiry_Form'] = new WC_Product_Enquiry_Form();
}
