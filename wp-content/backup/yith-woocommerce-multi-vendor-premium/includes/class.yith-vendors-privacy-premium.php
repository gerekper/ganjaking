<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */
if ( ! defined( 'YITH_WPV_VERSION' ) ) {
    exit( 'Direct access forbidden.' );
}

if( ! function_exists( 'YITH_Vendors_Privacy_Premium' ) ) {
	function YITH_Vendors_Privacy_Privacy_Premium() {
		if ( ! class_exists( 'YITH_Vendors_Privacy_Premium' ) ) {


			class YITH_Vendors_Privacy_Premium extends YITH_Vendors_Privacy {

				/**
				 * YITH_Vendors_Privacy constructor.
				 */
				public function __construct() {
					add_filter( 'yith_wcmv_get_vendor_personal_data_fields', array( $this, 'get_vendor_personal_data_fields_premium' ) );
					add_filter( 'yith_wcmv_get_vendor_personal_data_fields_type', array( $this, 'get_vendor_personal_data_fields_type_premium' ) );

					parent::__construct();
				}

				/**
				 * @return array Vendor Personal data fields
				 */
				public function get_vendor_personal_data_fields_premium( $fields ){
					$premium_fields = array(
						'location'              => __( 'Store Location', 'yith-woocommerce-product-vendors' ),
						'store_email'           => __( 'Store Email', 'yith-woocommerce-product-vendors' ),
						'telephone'             => __( 'Vendor Telephone', 'yith-woocommerce-product-vendors' ),
						'vat'                   => __( 'VAT/SSN', 'yith-woocommerce-product-vendors' ),
						'bank_account'          => __( 'Vendor Bank Account', 'yith-woocommerce-product-vendors' ),
						'commission'            => __( 'Commission Rate (%)', 'yith-woocommerce-product-vendors' ),
						'registration_date'     => __( 'Registration Date', 'yith-woocommerce-product-vendors' ),
						'registration_date_gmt' => __( 'Registration Date GMT', 'yith-woocommerce-product-vendors' ),
						'socials'               => __( 'Vendor socials uri', 'yith-woocommerce-product-vendors' ),
					);

					return array_merge( $fields, $premium_fields );
				}

				/**
				 * @return array Vendor Personal data fields
				 */
				public function get_vendor_personal_data_fields_type_premium( $fields ) {
					$premium_fields = array(
						'location'     => 'text',
						'store_email'  => 'email',
						'telephone'    => 'text',
						'vat'          => 'text',
						'bank_account' => 'text',
						'socials'      => 'url',
						'legal_notes'  => 'text',
						'header_image' => 'yith_wcmv_profile_media',
						'avatar'       => 'yith_wcmv_profile_media'
					);

					return array_merge( $fields, $premium_fields );
				}

				/**
				 * Gets the message of the privacy to display.
				 * To be overloaded by the implementor.
				 *
				 * @return string
				 */
				public function get_privacy_message( $section ) {

					$message = '';
					switch( $section ){
						case 'collect_and_store':
							$message =  '<p>' . __( 'We collect information about you during the registration and checkout process on our store.', 'yith-woocommerce-product-vendors' ) . '</p>' .
							            '<p>' . __( 'While you visit our site, we’ll track:', 'yith-woocommerce-product-vendors' ) . '</p>' .
							            '<ul>' .
							                '<li>' . __( 'Vendor information: we will use these data to create a vendor profile that allows them to sell products on this website in exchange of a commission fee on each sale.', 'yith-woocommerce-product-vendors' ) . '</li>' .
							                '<li>' . __( 'The information required to start a vendor shop are the following: name and store description, header image, shop logo, address, email, telephone number, VAT/SSN, legal notes, links to social profiles (Facebook, Twitter, Google+, LinkedIn, YouTube, Vimeo, Instagram, Pinterest, Flickr, Behance, TripAdvisor), payment information (IBAN and PayPal email), information about commissions and issued payments.', 'yith-woocommerce-product-vendors' ) . '</li>' .
							            '</ul>';
							break;

						case 'has_access':
							$message = '<p>' . __( 'Members of our team have access to the information you provide us. For example, both Administrators and Shop Managers can access:', 'yith-woocommerce-product-vendors' ) . '</p>' .
							           '<ul>' .
								           '<li>' . __( 'Vendor information', 'yith-woocommerce-product-vendors' ) .'</li>' .
								           '<li>' . __( 'Data concerning commissions earned by the vendor','yith-woocommerce-product-vendors'  ) .'</li>' .
								           '<li>' . __( 'Data about payments','yith-woocommerce-product-vendors'  ) .'</li>' .
							           '</ul>' .
										'<p>' . __( 'Our team members have access to this information to help fulfill orders, process refunds and support you.', 'yith-woocommerce-product-vendors' ) . '</p>';
							break;

						case 'payments':
							$message = '<p>' . __( 'We send payments to vendors through PayPal. When processing payments, some of your data will be passed to PayPal, including information required to process or support the payment, such as the purchase total and billing information.', 'yith-woocommerce-product-vendors' ) . '</p>' .
							           '<p>' . __( 'Please see the <a href="https://www.paypal.com/us/webapps/mpp/ua/privacy-full">PayPal Privacy Policy</a> for more details.', 'yith-woocommerce-product-vendors' ) . '</p>' ;
							break;

						case 'share':
							$message = '<p>' . __( 'We share information with third parties who help us provide commissions payments to you.', 'yith-woocommerce-product-vendors' ) . '</p>';
							break;

					}

					return $message;
				}
			}
		}

		if( defined( 'YITH_WPV_PREMIUM' ) && YITH_WPV_PREMIUM ){
			return new YITH_Vendors_Privacy_Premium();
		}
	}
}

add_action( 'plugins_loaded', 'YITH_Vendors_Privacy_Privacy_Premium', 25 );