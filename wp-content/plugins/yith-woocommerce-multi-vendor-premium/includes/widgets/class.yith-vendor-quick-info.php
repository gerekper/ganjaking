<?php
/**
 * Main class
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Ajax Navigation
 * @version 1.3.2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_Vendor_Quick_Info_Widget' ) ) {
	/**
	 * YITH_Woocommerce_Vendors_Widget
	 *
	 * @author Andrea Grillo <andrea.grillo@yithemes.com>
	 *
	 * @since  1.0.0
	 */
	class YITH_Vendor_Quick_Info_Widget extends WP_Widget {

		/**
		 * @var array The widget option
		 */
		public $_instance = array();

		public $response = array();

		public $default = array();

		/**
		 * Construct
		 */
		public function __construct() {
			$this->default = array(
				'title'                => __( 'Quick Info', 'yith-woocommerce-product-vendors' ),
				'description'          => __( 'Do you need more information? Write to us!', 'yith-woocommerce-product-vendors' ),
				'hide_from_guests'     => false,
				'cc_to_admin'          => false,
				'show_in_vendor_store' => true,
				'show_in_single'       => false,
				'submit_label'         => __( 'Submit', 'yith-woocommerce-product-vendors' ),
			);

			$this->_instance = wp_parse_args( $this->_instance, $this->default );

			$this->response = array(
				0 => array(
					'message' => __( 'Unable to send email. Please try again', 'yith-woocommerce-product-vendors' ),
					'class'   => 'error'
				),
				1 => array(
					'message' => __( 'Email sent successfully', 'yith-woocommerce-product-vendors' ),
					'class'   => 'message'
				),
			);

			add_action( 'init', array( $this, 'send_mail' ), 20 );

			$id_base        = 'yith-vendor-quick-info';
			$name           = __( 'YITH Vendor Contact Form', 'yith-woocommerce-product-vendors' );
			$widget_options = array(
				'description' => __( "Add a quick info contact form in vendor's store page and in single product page", 'yith-woocommerce-product-vendors' )
			);

			parent::__construct( $id_base, $name, $widget_options );
		}

		/**
		 * Echo the widget content.
		 *
		 * Subclasses should over-ride this function to generate their widget code.
		 *
		 * @param array $args Display arguments including before_title, after_title,
		 *                        before_widget, and after_widget.
		 * @param array $instance The settings for the particular instance of the widget.
		 *
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public function widget( $args, $instance ) {
			$instance         = wp_parse_args( $instance, $this->default );
			$hide_from_guests = ! empty( $instance['hide_from_guests'] ) ? true : false;

			if ( $hide_from_guests && ! is_user_logged_in() ) {
				return false;
			}

			$show_on_single       = ! empty( $instance['show_in_single'] ) ? true : false;
			$show_on_vendor_store = ! empty( $instance['show_in_vendor_store'] ) ? true : false;

			$is_vendor_page = ! empty( YITH_Vendors()->frontend ) ? YITH_Vendors()->frontend->is_vendor_page() : false;
			$is_singular    = is_singular( 'product' );

			if ( ( $is_vendor_page && $show_on_vendor_store ) || ( $is_singular && $show_on_single ) ) {
				global $product;
				$object = $object_type = null;

				if ( $is_vendor_page ) {
					$object      = get_query_var( 'term' );
					$object_type = 'vendor';
				} elseif ( $is_singular ) {
					$object      = $product;
					$object_type = 'product';
				}

				$vendor = yith_get_vendor( $object, $object_type );

				if ( $vendor->is_valid() ) {
					$vendor_email = $vendor->store_email;

					if ( empty( $vendor_email ) ) {
						$vendor_owner = get_user_by( 'id', absint( $vendor->get_owner() ) );
						$vendor_email = $vendor_owner instanceof WP_User ? $vendor_owner->user_email : false;
					}

					if ( ! empty( $vendor_email ) ) {
						$args = array(
							'instance'       => $instance,
							'vendor'         => $vendor,
							'current_user'   => wp_get_current_user(),
							'widget'         => $this,
							'is_singular'    => $is_singular,
							'is_vendor_page' => $is_vendor_page,
							'object'         => $object
						);

						if ( $is_singular ) {
							$args['product'] = $product;
						}

						$this->_instance = $instance;

						yith_wcpv_get_template( 'quick-info', $args, 'widgets' );
					}
				}
			}


		}

		/**
		 * Output the settings update form.
		 *
		 * @param array $instance Current settings.
		 *
		 * @return string Default return is 'noform'.
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 */

		public function form( $instance ) {

			$instance = wp_parse_args( (array) $instance, $this->default );
			?>
            <p>
                <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title', 'yith-woocommerce-product-vendors' ) ?>
                    :
                    <input type="text" id="<?php echo $this->get_field_id( 'title' ); ?>"
                           name="<?php echo $this->get_field_name( 'title' ); ?>"
                           value="<?php echo $instance['title']; ?>" class="widefat"/>
                </label>
            </p>
            <p>
                <label for="<?php echo $this->get_field_id( 'description' ); ?>"><?php _e( 'Description', 'yith-woocommerce-product-vendors' ) ?>
                    :
                    <input type="text" id="<?php echo $this->get_field_id( 'description' ); ?>"
                           name="<?php echo $this->get_field_name( 'description' ); ?>"
                           value="<?php echo $instance['description']; ?>" class="widefat"/>
                </label>
            </p>
            <p>
                <label for="<?php echo $this->get_field_id( 'submit_label' ); ?>"><?php _e( 'Submit button label text', 'yith-woocommerce-product-vendors' ) ?>
                    :
                    <input type="text" id="<?php echo $this->get_field_id( 'submit_label' ); ?>"
                           name="<?php echo $this->get_field_name( 'submit_label' ); ?>"
                           value="<?php echo $instance['submit_label']; ?>" class="widefat"/>
                </label>
            </p>
            <p>
                <label for="<?php echo $this->get_field_id( 'hide_from_guests' ); ?>"><?php _e( 'Hide from guests', 'yith-woocommerce-product-vendors' ) ?>
                    :
                    <input type="checkbox" id="<?php echo $this->get_field_id( 'hide_from_guests' ); ?>"
                           name="<?php echo $this->get_field_name( 'hide_from_guests' ); ?>"
                           value="1" <?php checked( $instance['hide_from_guests'], 1, true ) ?> class="widefat"/>
                </label>
            </p>
            <p>
                <label for="<?php echo $this->get_field_id( 'show_in_vendor_store' ); ?>"><?php _e( "Show in vendor's store page", 'yith-woocommerce-product-vendors' ) ?>
                    :
                    <input type="checkbox" id="<?php echo $this->get_field_id( 'show_in_vendor_store' ); ?>"
                           name="<?php echo $this->get_field_name( 'show_in_vendor_store' ); ?>"
                           value="1" <?php checked( $instance['show_in_vendor_store'], 1, true ) ?> class="widefat"/>
                </label>
            </p>
            <p>
                <label for="<?php echo $this->get_field_id( 'show_in_single' ); ?>"><?php _e( 'Show in single product page', 'yith-woocommerce-product-vendors' ) ?>
                    :
                    <input type="checkbox" id="<?php echo $this->get_field_id( 'show_in_single' ); ?>"
                           name="<?php echo $this->get_field_name( 'show_in_single' ); ?>"
                           value="1" <?php checked( $instance['show_in_single'], 1, true ) ?> class="widefat"/>
                </label>
            </p>
            <p>
                <label for="<?php echo $this->get_field_id( 'cc_to_admin' ); ?>"><?php _e( 'Send a copy to website owner', 'yith-woocommerce-product-vendors' ) ?>
                    :
                    <input type="checkbox" id="<?php echo $this->get_field_id( 'cc_to_admin' ); ?>"
                           name="<?php echo $this->get_field_name( 'cc_to_admin' ); ?>"
                           value="1" <?php checked( $instance['cc_to_admin'], 1, true ) ?> class="widefat"/>
                </label>
            </p>
			<?php
		}

		/**
		 * Update a particular instance.
		 *
		 * This function should check that $new_instance is set correctly. The newly-calculated
		 * value of `$instance` should be returned. If false is returned, the instance won't be
		 * saved/updated.
		 *
		 * @param array $new_instance New settings for this instance as input by the user via.
		 * @param array $old_instance Old settings for this instance.
		 *
		 * @return array Settings to save or bool false to cancel saving.
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 * @see    WP_Widget::form()
		 */
		public function update( $new_instance, $old_instance ) {
			$instance                         = $old_instance;
			$instance['title']                = strip_tags( $new_instance['title'] );
			$instance['description']          = strip_tags( $new_instance['description'] );
			$instance['hide_from_guests']     = isset( $new_instance['hide_from_guests'] ) ? true : false;
			$instance['show_in_vendor_store'] = isset( $new_instance['show_in_vendor_store'] ) ? true : false;
			$instance['show_in_single']       = isset( $new_instance['show_in_single'] ) ? true : false;
			$instance['cc_to_admin']          = isset( $new_instance['cc_to_admin'] ) ? true : false;
			$instance['submit_label']         = strip_tags( $new_instance['submit_label'] );

			return $instance;
		}

		/**
		 * Send the quick info form mail
		 *
		 * @since 1.0
		 * @return void
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public function send_mail() {
			if ( $this->check_form() ) {
				/* === Sanitize Form Value === */
				$vendor = yith_get_vendor( absint( sanitize_text_field( $_POST['quick_info']['vendor_id'] ) ) );
				$to     = sanitize_email( $vendor->store_email );

				if ( empty( $to ) ) {
					$vendor_owner = get_user_by( 'id', absint( $vendor->get_owner() ) );
					$to           = $vendor_owner instanceof WP_User ? sanitize_email( $vendor_owner->user_email ) : false;
				}

				$from_email   = ! empty( $_POST['quick_info']['email'] ) ? sanitize_email( stripslashes( $_POST['quick_info']['email'] ) ) : '';
				$subject      = ! empty( $_POST['quick_info']['subject'] ) ? sanitize_text_field( stripslashes( $_POST['quick_info']['subject'] ) ) : '';
				$from         = ! empty( $_POST['quick_info']['name'] ) ? sanitize_text_field( stripslashes( $_POST['quick_info']['name'] ) ) : '';
				$user_message = ! empty( $_POST['quick_info']['message'] ) ? sanitize_text_field( stripslashes( $_POST['quick_info']['message'] ) ) : '';

				$message = sprintf( "%s: %s\n%s: %s\n%s:\n%s",
					_x( 'Name', 'Placeholder like "Name: Andrea', 'yith-woocommerce-product-vendors' ),
					$from,
					_x( 'Email', 'Placeholder like "Email: andrea@yithemes.com', 'yith-woocommerce-product-vendors' ),
					$from_email,
					_x( 'Message', 'Placeholder like "Message: Lorem ipsume dolor sit amet', 'yith-woocommerce-product-vendors' ),
					$user_message
				);

				$message = nl2br( $message );

				$admin_name  = get_option( 'woocommerce_email_from_name' );
				$admin_email = get_option( 'woocommerce_email_from_address' );

				$headers['content-type'] = 'Content-type: text/html; charset="UTF-8"; format=flowed';
				$headers['from']         = sprintf( "From: %s <%s>", $from, $from_email );
				$headers['reply']        = sprintf( "Reply: %s <%s>", $from, $from_email );

				$widget_options = get_option( 'widget_yith-vendor-quick-info' );

				if ( $widget_options[ $this->number ]['cc_to_admin'] ) {
					if ( $admin_email && $admin_name ) {
						$headers['cc']    = "Cc: {$admin_name} <{$admin_email}>";
						$headers['reply'] = ", {$admin_name} <{$admin_email}>";
					}
				}

				$headers = apply_filters( 'yith_wcmv_widget_quick_form_email_headers', $headers );

				$message = apply_filters( 'yith_wcmv_widget_quick_form_email_message', $message );

				/* === Send Mail === */
                $check = apply_filters( 'yith_wcmv_vendor_quick_info_email_default_check', false );

                if( apply_filters( 'yith_wcmv_send_vendor_quick_info_email', true ) ){
	                $check = wp_mail( $to, $subject, $message, $headers );
                }

				/* === Prevent resubmit form === */
				$url = ! empty( $_POST['quick_info']['product_id'] ) ? get_permalink( absint( sanitize_text_field( $_POST['quick_info']['product_id'] ) ) ) : $vendor->get_url( 'frontend' );
				unset( $_POST );
				$redirect = esc_url( add_query_arg( array( 'message' => $check ? 1 : 0 ), $url ) );
				wp_redirect( $redirect );
				exit;
			}
		}

		/**
		 * Check form information
		 *
		 * @since  1.0
		 * @return void
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 * @return bool
		 */
		public function check_form() {
			$check =
				! empty( $_POST['yith_vendor_quick_info_submitted'] ) &&
				wp_verify_nonce( $_POST['yith_vendor_quick_info_submitted'], 'yith_vendor_quick_info_submitted' ) &&
				! empty( $_POST['quick_info'] ) &&
				! empty( $_POST['quick_info']['name'] ) &&
				! empty( $_POST['quick_info']['subject'] ) &&
				! empty( $_POST['quick_info']['email'] ) &&
				! empty( $_POST['quick_info']['message'] ) &&
				! empty( $_POST['quick_info']['vendor_id'] ) &&
				empty( $_POST['quick_info']['spam'] );

			if ( apply_filters( 'yith_wcmv_quick_info_form_validation', $check ) ) {
				//is valid email
				$subject_is_email = is_email( $_POST['quick_info']['subject'] );

				//is valid url
				$subject_is_url =
					filter_var( $_POST['quick_info']['subject'], FILTER_VALIDATE_URL, FILTER_FLAG_SCHEME_REQUIRED )
					||
					filter_var( $_POST['quick_info']['subject'], FILTER_VALIDATE_URL, FILTER_FLAG_HOST_REQUIRED )
					||
					filter_var( $_POST['quick_info']['subject'], FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED )
					||
					filter_var( $_POST['quick_info']['subject'], FILTER_VALIDATE_URL, FILTER_FLAG_QUERY_REQUIRED );

				if ( $subject_is_email || $subject_is_url ) {
					$check = false;
				}
			}

			return $check;
		}
	}
}
