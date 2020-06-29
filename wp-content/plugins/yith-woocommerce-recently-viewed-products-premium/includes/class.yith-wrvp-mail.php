<?php
/**
 * Email class
 *
 * @author YITH
 * @package YITH WooCommerce Recently Viewed Products
 * @version 1.0.0
 */

if ( ! defined( 'YITH_WRVP' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WRVP_Mail' ) ) {
	/**
	 * Email Class
	 * Extend WC_Email to send mail to customer
	 *
	 * @class   YITH_WRVP_Mail
	 * @extends  WC_Email
	 */
	class YITH_WRVP_Mail extends WC_Email {

		/**
		 * Msg type for test mail
		 *
		 * @var string
		 */
		public $_test_msg_type = 'error';

		/**
		 * Constructor
		 *
		 * @author Francesco Licandro <francesco.licandro@yithemes.com>
		 */
		public function __construct() {

			$this->id           = 'yith_wrvp_mail';
			$this->title        = __( 'YITH Recently Viewed Products Email', 'yith-woocommerce-recently-viewed-products' );
			$this->customer_email = true;
            $this->description = '';

			$this->heading      = __( '{blogname}', 'yith-woocommerce-recently-viewed-products' );
			$this->subject      = __( 'You may be interested in these products.', 'yith-woocommerce-recently-viewed-products' );
			$this->mail_content = __( 'According to your research, you may be interested in the following products. Moreover, purchasing one of these products will entitle you to receive a discount with the following coupon {coupon_code}{products_list}', 'yith-woocommerce-recently-viewed-products' );

			$this->template_base    = YITH_WRVP_TEMPLATE_PATH . '/email/';
			$this->template_html    = 'ywrvp-mail-template.php';
			$this->template_plain   = 'plain/ywrvp-mail-template.php';

			// Triggers for this email
			add_action( 'send_yith_wrvp_mail_notification', array( $this, 'trigger' ), 10, 1 );

			// filter style for email
			add_filter( 'woocommerce_email_styles', array( $this, 'my_email_style' ), 10, 1 );

			// Call parent constructor
			parent::__construct();
		}

		/**
		 * Trigger Function
		 *
		 * @access public
		 * @since 1.0.0
		 * @param mixed $data
		 * @return void
		 * @author Francesco Licandro <francesco.licandro@yithemes.com>
		 */
		public function trigger( $data ) {

			if ( ! $this->is_enabled() ) {
				return;
			}

			$is_test = false;

			// get option mail
			$content = $this->get_option('mail_content');
			$coupon_enabled = ( $this->get_option('coupon_enable' ) == 'yes' && get_option( 'woocommerce_enable_coupons' ) == 'yes' );
			$custom_products = $this->get_option( 'custom_products', array() );
			$most_viewed_cat = $this->get_option( 'cat_most_viewed' ) == 'yes';

			$products_type = $this->get_option('products_type');

			// find logo image
			$find['blogname']       = '{blogname}';
			$replace['blogname']    = $this->get_blogname();
			$find['logo-image']     = '{logo_image}';
			$replace['logo-image']  = $this->print_logo_image();

			foreach( $data as $customer => $products ) {

				// if products is empty that is test
				$is_test = empty( $products );

				$products_list = '';
				$custom_products_list = '';
				$coupon_code    = '';
				$coupon_expire  = '';
				$cat_id = false;
				$product_title = 'Product-Name';

				if ( ! $is_test ) {
					// most viewed cat
					if ( $most_viewed_cat ) {
						$cat_id = YITH_WRVP_Helper::most_viewed_cat( $products );
					}
					// get similar
					if ( $products_type == 'similar' ) {
						$products = YITH_WRVP_Helper::get_similar_products( array( $cat_id ), '', $products );
					}

					// remove from products list the custom products to avoid duplication
                    if( ( strpos( $content, '{custom_products_list}' ) >= 0 ) && ! empty( $custom_products ) ){
                        // get products list html;
                        ! is_array( $custom_products ) && $custom_products = explode( ',', $custom_products );
                        $products = array_diff( $products, $custom_products );
                    }

					// set subject based on first product title
					if ( strpos( $this->subject, '{first_product_title}' ) >= 0) {

						$first_product = array_slice($products, 0, 1);
						if ( ! is_null( $first_product ) ) {
							$product_title = get_the_title( array_shift( $first_product ) );
						}

					}
				}

				// products list
				if ( strpos($content, '{products_list}') >= 0 ) {
					// get products list html;
					$products_list = YITH_WRVP_Mail_Handler()->get_products_list_html( $products, false, $cat_id, $this );
				}

				$find['products-list']      = '{products_list}';
				$replace['products-list']   = $products_list;
				$find['first-product']      = '{first_product_title}';
				$replace['first-product']   = $product_title;

                // user fields
                preg_match( '/\{customer_(.*?)\}/', $content, $customer_data );
                if( $customer_data ){
                    $customer_obj = get_user_by( 'email', $customer );
                    if( $customer ) {
                        foreach ( $customer_data as $data ) {
                            if ( isset( $customer_obj->$data ) ) {
                                $find["customer-$data"]     = "{customer_$data}";
			                    $replace["customer-$data"]  = $customer_obj->$data;
                            }
                        }
                    }
                }

				// coupon code
				if( $coupon_enabled && ( strpos( $content, '{coupon_code}' ) >= 0 ) ) {
                    $type = $this->get_option( 'coupon_type' );
                    if( $type == 'exs' ) {
                        $coupon_code = $this->get_option( 'coupon_code' );
                        if( $coupon_code ){
                            $id     = wc_get_coupon_id_by_code( $coupon_code );
                            // make sure coupon exists
                            if( ! $id ) {
                                $coupon_code = '';
                            }
                            else {
                                $coupon = new WC_Coupon( $coupon_code );
                                $expire = $coupon->get_date_expires();
                                if( ! is_null( $expire ) && $expire->getTimestamp() > time() ){
                                    $coupon_expire = $expire->getTimestamp();
                                }
                            }
                        }
                    }
                    else {
                        $coupon_expire = time() + ( intval( $this->get_option( 'coupon_expiry' ) ) * DAY_IN_SECONDS );
                        $coupon_value = $this->get_option( 'coupon_amount' );
                        // create coupon
                        $coupon_code = $is_test ? 'aaabbbccc' : YITH_WRVP_Mail_Handler()->add_coupon_to_mail( $customer, $products, $coupon_expire, $coupon_value );
                    }
				}

                // coupon expire
                $find['coupon-expire']      = '{coupon_expire}';
                $replace['coupon-expire']   = date( 'Y-m-d', intval( $coupon_expire ) );
                $find['coupon-code']        = '{coupon_code}';
                $replace['coupon-code']     = yith_wrvp_get_mail_copuon_code_html( $coupon_code );

				// custom products list
				if( ( strpos( $content, '{custom_products_list}' ) >= 0 ) && ! empty( $custom_products ) ) {
					// get products list html;
                    ! is_array( $custom_products ) && $custom_products = explode( ',', $custom_products );
					$custom_products_list = YITH_WRVP_Mail_Handler()->get_products_list_html( $custom_products, true, false, $this );
				}

				$find['custom-products-list']       = '{custom_products_list}';
				$replace['custom-products-list']    = $custom_products_list;

				// search for unsubscribe link
				preg_match( '/\{{(.*?)\}}/', $content, $unsub_link );
				if( $unsub_link && isset( $unsub_link[1] ) ) {

					$find['unsubscribe-from-list']      = '{{'.$unsub_link[1].'}}';
					$replace['unsubscribe-from-list']   = YITH_WRVP_Mail_Handler()->get_unsubscribe_link( $customer, $unsub_link[1], $is_test );
				}

				
				// change placeholder values
				if( version_compare( wc()->version, '3.2.0', '>=' ) ){
					$this->placeholders = array_merge(
						$this->placeholders,
						array_combine( array_values( $find ), array_values( $replace ) )
					);
				}
				else{
					$this->find = array_merge( $this->find, $find );
					$this->replace = array_merge( $this->replace, $replace );
				}

				// send!
				if ( $this->send( $customer, $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() ) ) {
					do_action( 'yith_wrvp_mail_sent_correctly', $customer );
					$this->_test_msg_type = 'success';
				}
				else {
                    do_action( 'yith_wrvp_mail_sent_error', $customer );
                }
			}

			if( $is_test ) {
				add_action( 'woocommerce_email_settings_before', array( YITH_WRVP_Mail_Handler(), 'add_test_mail_message' ), 10, 1 );
			}
		}

		/**
		 * Send mail using standard WP Mail or Mandrill Service
		 *
		 * @access public
		 * @since 1.0.0
		 * @param string $to
		 * @param string $subject
		 * @param string $message
		 * @param string $headers
		 * @param string $attachments
		 *
		 * @return bool | void
		 * @author Francesco Licandro <francesco.licandro@yithemes.com>
		 */
		public function send( $to, $subject, $message, $headers, $attachments ) {

			// Retrieve Mandrill API KEY
			$api_key = get_option( 'yith-wrvp-mandrill-api-key' );

			if( get_option( 'yith-wrvp-use-mandrill' ) != 'yes' || empty( $api_key ) ) {
				return parent::send( $to, $subject, $message, $headers, $attachments );
			}
			else {

				/**
				 * Filter the wp_mail() arguments.
				 *
				 * @since 2.2.0
				 *
				 * @param array $args A compacted array of wp_mail() arguments, including the "to" email,
				 *                    subject, message, headers, and attachments values.
				 */
				$atts = apply_filters( 'wp_mail', compact( 'to', 'subject', 'message', 'headers', 'attachments' ) );

				if ( isset( $atts['to'] ) ) {
					$to = $atts['to'];
				}

				if ( isset( $atts['subject'] ) ) {
					$subject = $atts['subject'];
				}

				if ( isset( $atts['message'] ) ) {
					$message = $atts['message'];
				}

				if ( isset( $atts['headers'] ) ) {
					$headers = $atts['headers'];
				}

				if ( isset( $atts['attachments'] ) ) {
					$attachments = $atts['attachments'];
				}

				if ( ! is_array( $attachments ) ) {
					$attachments = explode( "\n", str_replace( "\r\n", "\n", $attachments ) );
				}

				// include lib
				include_once( YITH_WRVP_DIR . 'includes/third-party/Mandrill/Mandrill.php' );

				// Headers
				if ( empty( $headers ) ) {
					$headers = array();
				}
				else {
					if ( ! is_array( $headers ) ) {
						// Explode the headers out, so this function can take both
						// string headers and an array of headers.
						$tempheaders = explode( "\n", str_replace( "\r\n", "\n", $headers ) );
					}
					else {
						$tempheaders = $headers;
					}

					$headers = array();
					$cc = array();
					$bcc = array();

					// If it's actually got contents
					if ( ! empty( $tempheaders ) ) {
						// Iterate through the raw headers
						foreach ( (array) $tempheaders as $header ) {
							if ( strpos($header, ':') === false ) {
								if ( false !== stripos( $header, 'boundary=' ) ) {
									$parts = preg_split('/boundary=/i', trim( $header ) );
									$boundary = trim( str_replace( array( "'", '"' ), '', $parts[1] ) );
								}
								continue;
							}
							// Explode them out
							list( $name, $content ) = explode( ':', trim( $header ), 2 );

							// Cleanup crew
							$name    = trim( $name    );
							$content = trim( $content );

							switch ( strtolower( $name ) ) {
								// Mainly for legacy -- process a From: header if it's there
								case 'from':
									if ( strpos($content, '<' ) !== false ) {
										// So... making my life hard again?
										$from_name = substr( $content, 0, strpos( $content, '<' ) - 1 );
										$from_name = str_replace( '"', '', $from_name );
										$from_name = trim( $from_name );

										$from_email = substr( $content, strpos( $content, '<' ) + 1 );
										$from_email = str_replace( '>', '', $from_email );
										$from_email = trim( $from_email );
									} else {
										$from_email = trim( $content );
									}
									break;
								default:
									// Add it to our grand headers array
									$headers[trim( $name )] = trim( $content );
									break;
							}
						}
					}
				}

				// From email and name
				// If we don't have a name from the input headers
				if ( !isset( $from_name ) )
					$from_name = $this->get_from_name();

				// If we don't have an email from the input headers
				if ( !isset( $from_email ) ) {
					$from_email = $this->get_from_address();
				}

				// Set destination addresses
				if ( ! is_array( $to ) ){
					$to = explode( ',', $to );
				}

				$recipients = array();

				foreach ( (array) $to as $recipient ) {
					try {
						// Break $recipient into name and address parts if in the format "Foo <bar@baz.com>"
						$recipient_name = '';
						if( preg_match( '/(.*)<(.+)>/', $recipient, $matches ) ) {
							if ( count( $matches ) == 3 ) {
								$recipient_name = $matches[1];
								$recipient = $matches[2];
							}
						}
						$recipients[] = array(
							'email' => $recipient,
							'name' 	=> $recipient_name,
							'type' 	=> 'to'
						);
					}
					catch ( phpmailerException $e ) {
						continue;
					}
				}

				$files_to_attach = array();

				if( ! empty( $attachments ) ){
					foreach ( $attachments as $attachment ) {
						try {
							$new_attachment = $this->get_attachment_struct( $attachment );

							if( $new_attachment == false ){
								continue;
							}

							$files_to_attach[] = $new_attachment;
						} catch ( Exception $e ) {
							continue;
						}
					}
				}

				try{
					$mandrill = new Mandrill( $api_key );
					$message = apply_filters( 'ywrvp_mandrill_send_mail_message', array(
						'html' => apply_filters( 'woocommerce_mail_content', $this->style_inline( $message ) ),
						'subject' => $subject,
						'from_email' => apply_filters( 'wp_mail_from', $from_email ),
						'from_name' => apply_filters( 'wp_mail_from_name', $from_name ),
						'to' => $recipients,
						'headers' => $headers,
						'attachments' => $files_to_attach
					) );

					$async = apply_filters( 'ywrvp_mandrill_send_mail_async', false );
					$ip_pool = apply_filters( 'ywrvp_mandrill_send_mail_ip_pool', null );
					$send_at = apply_filters( 'ywrvp_mandrill_send_mail_send_at', null );

					$results = $mandrill->messages->send( $message, $async, $ip_pool, $send_at );
					$return = true;

					if( ! empty( $results ) ){
						foreach( $results as $result ){
							if( ! isset( $result['status'] ) || in_array( $result['status'], array( 'rejected', 'invalid' ) ) ){
								$return = false;
							}
						}
					}

					return $return;
				}
				catch( Mandrill_Error $e ) {
					return false;
				}
			}
		}

		/**
		 * Using file path, build an attachment struct, to use in Mandrill send request
		 *
		 * @param $path string File absolute path
		 *
		 * @static
		 * @throws Exception When some error occurs with file handling
		 * @return bool|array
		 * [
		 *     type => mime type of the file
		 *     name => file name with extension
		 *     content => file complete content, divided in chunks
		 * ]
		 * @since  1.0.0
		 */
		public static function get_attachment_struct( $path ) {

			$struct = array();

			try {
				if ( !@is_file($path) ) throw new Exception($path.' is not a valid file.');

				$filename = basename($path);

				$file_buffer  = file_get_contents($path);
				$file_buffer  = chunk_split( base64_encode( $file_buffer ), 76, "\n" );

				$mime_type = '';
				if ( function_exists('finfo_open') && function_exists('finfo_file') ) {
					$finfo = finfo_open( FILEINFO_MIME_TYPE );
					$mime_type = finfo_file( $finfo, $path );
				}
				elseif ( function_exists('mime_content_type') ) {
					$mime_type = mime_content_type($path);
				}

				if ( !empty( $mime_type ) ){
					$struct['type']     = $mime_type;
				}

				$struct['name']     = $filename;
				$struct['content']  = $file_buffer;

			} catch (Exception $e) {
				return false;
			}

			return $struct;
		}

		/**
		 * @return string|void
		 */
		public function init_form_fields() {

			parent::init_form_fields();

			unset( $this->form_fields['additional_content'] );

			if( isset( $this->form_fields['subject'] ) ) {
				$this->form_fields['subject']['desc_tip'] = sprintf( __( 'Use this placeholder to show the title of the first product %s', 'yith-woocommerce-recently-viewed-products'), '{first_product_title}');
			}

			$upload['upload_logo'] = array(
				'title'         => __( 'Logo image', 'yith-woocommerce-recently-viewed-products' ),
				'type'          => 'yith_wrvp_upload',
				'description'   => __( 'Upload logo image for email header. Use {logo_image} placeholder in the header to show it.', 'yith-woocommerce-recently-viewed-products' ),
				'default'       => ''
			);
			// move upload after mail header
			$this->form_fields = array_slice( $this->form_fields, 0, 3, true ) + $upload + array_slice( $this->form_fields, 3, count( $this->form_fields ) -1, true);

			// add other options
			$this->form_fields['mail_content'] = array(
				'title'         => __( 'Email content', 'yith-woocommerce-recently-viewed-products' ),
				'type'          => 'yith_wrvp_textarea',
				'description'   => sprintf( __( 'Defaults to <code>%s</code>. Add text between {{}} to make it an unsubscribe link.', 'yith-woocommerce-recently-viewed-products' ), $this->mail_content ),
				'placeholder'   => '',
				'default'       => $this->mail_content
			);
			$this->form_fields['custom_products'] = array(
				'title'         => __( 'Add custom products', 'yith-woocommerce-recently-viewed-products' ),
				'type'          => 'yith_wrvp_select_products',
				'description'   => __( 'Add custom products to the email', 'yith-woocommerce-recently-viewed-products' ),
				'default'       => ''
			);
			$this->form_fields['number_products'] = array(
				'title'         => __( 'Number of products', 'yith-woocommerce-recently-viewed-products' ),
				'type'          => 'number',
				'description'   => __( 'Choose how many products from users\' product list show in the email', 'yith-woocommerce-recently-viewed-products' ),
				'default'       => '5',
				'custom_attributes' => array(
					'min'	=> 0
				)
			);
			$this->form_fields['products_type'] = array(
				'title'         => __( 'Product type', 'yith-woocommerce-recently-viewed-products' ),
				'description'   => __( 'Select which type of products to add in email', 'yith-woocommerce-recently-viewed-products' ),
                'type'          => 'yith_wrvp_radio',
				'options'			=> array(
					'viewed'	=> __( 'Only viewed products', 'yith-woocommerce-recently-viewed-products' ),
					'similar'	=> __( 'Include similar products', 'yith-woocommerce-recently-viewed-products' )
				),
				'default'       => 'viewed'
			);
			$this->form_fields['products_order'] = array(
				'title'         => __( 'Products ordered by', 'yith-woocommerce-recently-viewed-products' ),
				'description'   => __( 'Choose in which order you want to show products.', 'yith-woocommerce-recently-viewed-products' ),
                'type'          => 'yith_wrvp_radio',
				'options'			=> array(
					'rand'		=> __( 'Random', 'yith-woocommerce-recently-viewed-products' ),
					'sales'		=> __( 'Sales', 'yith-woocommerce-recently-viewed-products' ),
					'newest'	=> __( 'Newest', 'yith-woocommerce-recently-viewed-products' ),
					'high-low'	=> __( 'Price: High to Low', 'yith-woocommerce-recently-viewed-products' ),
					'low-high'	=> __( 'Price: Low to High', 'yith-woocommerce-recently-viewed-products' ),
				),
				'default'       => 'rand'
			);
			$this->form_fields['cat_most_viewed'] = array(
				'title'         => __( 'Only the most viewed category', 'yith-woocommerce-recently-viewed-products' ),
				'description'   => __( 'Enable this option if you want to display only the products of the most viewed category.', 'yith-woocommerce-recently-viewed-products' ),
				'type'          => 'checkbox',
				'default'       => 'no'
			);

			// add coupon options
			if( get_option( 'woocommerce_enable_coupons' ) == 'yes' ) {

				$this->form_fields['title_coupon_section'] = array(
					'title' => __('Add coupon to email', 'yith-woocommerce-recently-viewed-products'),
					'type' => 'title'
				);

				$this->form_fields['coupon_enable'] = array(
					'title' => __('Enable coupon', 'yith-woocommerce-recently-viewed-products'),
					'type' => 'checkbox',
					'default' => 'yes',
				);

                $this->form_fields['coupon_type'] = array(
                    'title' => __( 'Coupon type', 'yith-woocommerce-recently-viewed-products' ),
                    'description' => __('Choose to use an existing coupon or to create automatically a coupon for the products added in the email.', 'yith-woocommerce-recently-viewed-products'),
                    'type' => 'yith_wrvp_radio',
                    'options'   => array(
                        'exs'   => __( 'Use an existing coupon', 'yith-woocommerce-recently-viewed-products' ),
                        'new'   => __( 'Create a coupon', 'yith-woocommerce-recently-viewed-products' ),
                    ),
                    'default' => 'new',
                );

                $this->form_fields['coupon_code'] = array(
                    'title' => __( 'Coupon code', 'yith-woocommerce-recently-viewed-products' ),
                    'description' => __('Type the coupon code to use in the email.', 'yith-woocommerce-recently-viewed-products'),
                    'type' => 'text',
                    'default' => '',
                    'class' => 'yith_wrvp_coupon_validate',
                    'custom_attributes' => array(
                        'data-deps'         => 'woocommerce_yith_wrvp_mail_coupon_type',
                        'data-deps_value'   => 'exs'
                    )
                );

				$this->form_fields['coupon_amount'] = array(
					'title' => __('Coupon amount', 'yith-woocommerce-recently-viewed-products'),
					'description' => __('The coupon amount (Product % Discount).', 'yith-woocommerce-recently-viewed-products'),
					'type' => 'number',
					'default' => '',
					'placeholder' => '%',
					'custom_attributes' => array(
						'min'               => 0,
						'max'               => 100,
                        'data-deps'         => 'woocommerce_yith_wrvp_mail_coupon_type',
                        'data-deps_value'   => 'new'
					)
				);

				$this->form_fields['coupon_expiry'] = array(
					'title' => __('Coupon expiration date', 'yith-woocommerce-recently-viewed-products'),
					'description' => __('Set for how many days the coupon sent with the email can be used.', 'yith-woocommerce-recently-viewed-products'),
					'type' => 'number',
					'default' => '7',
					'custom_attributes' => array(
						'min'               => 1,
                        'data-deps'         => 'woocommerce_yith_wrvp_mail_coupon_type',
                        'data-deps_value'   => 'new'
					)
				);
			}
		}

		/**
		 * Generate YITh Select products Input HTML.
		 *
		 * @param  mixed $key
		 * @param  mixed $data
		 * @since  1.0.0
		 * @return string
		 */
		public function generate_yith_wrvp_select_products_html( $key, $data ) {
			// get html
			$html = ywrvp_email_select_products_html( $key, $data, $this );

			return $html;
		}

		/**
		 * Return YITh Texteditor HTML.
		 *
		 * @param $key
		 * @param $data
		 * @return string
		 */
		public function generate_yith_wrvp_textarea_html( $key, $data ) {
			// get html
			$html = ywrvp_email_textarea_editor_html( $key, $data, $this );

			return $html;
		}

		/**
		 * Return YITh Upload HTML.
		 *
		 * @param $key
		 * @param $data
		 * @return string
		 */
		public function generate_yith_wrvp_upload_html( $key, $data ) {
			// get html
			$html = ywrvp_email_upload_html( $key, $data, $this );

			return $html;
		}

		/**
		 * Return YITh Radio HTML.
		 *
		 * @param $key
		 * @param $data
		 * @return string
		 */
		public function generate_yith_wrvp_radio_html( $key, $data ) {
			// get html
			$html = ywrvp_email_radio_html( $key, $data, $this );

			return $html;
		}

		/**
		 * get custom email content from options
		 *
		 * @access public
		 * @since 1.0.0
		 * @return string
		 * @author Francesco Licandro <francesco.licandro@yithemes.com>
		 */
		public function get_custom_option_content() {
			$content = $this->get_option( 'mail_content' );

			return $this->format_string( $content );
		}

		/**
		 * get_content_html function.
		 *
		 * @access public
		 * @since 1.0.0
		 * @return string
		 * @author Francesco Licandro <francesco.licandro@yithemes.com>
		 */
		public function get_content_html() {
			ob_start();

			wc_get_template( $this->template_html, array(
					'email_heading' => $this->get_heading(),
					'email_content' => $this->get_custom_option_content(),
					'email'         => $this
			), false, $this->template_base
			);

			return ob_get_clean();
		}

		/**
		 * get_content_plain function.
		 *
		 * @access public
		 * @since 1.0.0
		 * @return string
		 * @author Francesco Licandro <francesco.licandro@yithemes.com>
		 */
		public function get_content_plain() {
			ob_start();

			wc_get_template( $this->template_plain, array(
					'email_heading' => $this->get_heading(),
					'email_content' => $this->get_custom_option_content(),
					'email'         => $this
			), false, $this->template_base
			);

			return ob_get_clean();
		}

		/**
		 * Get products list html
		 *
		 * @access public
		 * @since 1.0.0
		 * @param array $products
		 * @param bool $is_custom
		 * @param string|bool $cat_id
		 * @return mixed
		 * @author Francesc Licandro
		 * @deprecated Use YITH_WRVP_Mail_Handler()->get_products_list_html() instead
		 */
		public function get_products_list_html( $products, $is_custom = false, $cat_id = false ) {
			return YITH_WRVP_Mail_Handler()->get_products_list_html( $products, $is_custom, $cat_id, $this );
		}

		/**
		 * Filter email style and add custom style
		 *
		 * @access public
		 * @since 1.0.0
		 * @param string $css
		 * @return string
		 * @author Francesco Licandro
		 */
		public function my_email_style( $css ) {

			if( $this->id != 'yith_wrvp_mail' ) {
				return $css;
			}

			ob_start();
			wc_get_template( 'ywrvp-mail-style.php', array(), '', YITH_WRVP_TEMPLATE_PATH . '/email/' );
			$css .= ob_get_clean();

			return $css;
		}

		/**
		 * Get coupon code html
		 *
		 * @access public
		 * @since 1.0.0
		 * @param string $coupon_code
		 * @return string
		 * @author Francesco Licandro
		 * @deprecated Use function yith_wrvp_get_mail_copuon_code_html instead
		 */
		public function get_copuon_code_html( $coupon_code ) {
			return yith_wrvp_get_mail_copuon_code_html( $coupon_code );
		}

		/**
		 * Print html for logo image
		 *
		 * @access public
		 * @since 1.0.0
		 * @author Francesco Licandro
		 */
		public function print_logo_image(){

			$logo = $this->get_option('upload_logo');

			if( empty( $logo ) ) {
				return '';
			}

			ob_start();
			?>

			<img src="<?php echo esc_url( $logo ) ?>" alt="<?php esc_html_e('Logo Image', 'yith-woocommerce-recently-viewed-products' ); ?>">

			<?php

			return ob_get_clean();
		}

		/**
		 * Get unsubscribe from mailing list link
		 *
		 * @access public
		 * @since 1.0.0
		 * @param $customer_mail
		 * @param $label
		 * @param $is_test
		 * @return string
		 * @author Francesco Licandro
		 * @deprecated Use YITH_WRVP_Mail_Handler()->get_unsubscribe_link() instead 
		 */
		public function get_unsubscribe_link( $customer_mail, $label = '', $is_test = false ){
			return YITH_WRVP_Mail_Handler()->get_unsubscribe_link( $customer_mail, $label = '', $is_test = false );
		}

		/**
         * Validate field select product
         *
         * @since 1.1.0
         * @author Francesco Licandro
         * @param string $key
         * @param mixed $value
         * @return mixed
         */
		public function validate_yith_wrvp_select_products_field( $key, $value = false ) {
		    if( $value === false ) {
		        $value = isset( $_POST[ $key ] ) ? $_POST[ $key ] : '';
            }
		    return is_array( $value ) ? array_filter( $value ) : (string) $value;
        }
	}
}

return new YITH_WRVP_Mail();
