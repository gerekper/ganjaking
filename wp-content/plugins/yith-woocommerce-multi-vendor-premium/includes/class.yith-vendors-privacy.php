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

if( ! function_exists( 'YITH_Vendors_Privacy' ) ){
	function YITH_Vendors_Privacy(){
		if ( ! class_exists( 'YITH_Vendors_Privacy' ) ) {

			class YITH_Vendors_Privacy extends YITH_Privacy_Plugin_Abstract {

				/** @protected array Main Instance */
				protected static $_instance = null;

				/**
				 * YITH_Vendors_Privacy constructor.
				 */
				public function __construct() {
					if( ! function_exists( 'get_plugin_data' ) ){
						require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
					}
					$plugin_data = get_plugin_data( YITH_WPV_FILE );
					$plugin_name = $plugin_data['Name'];

					parent::__construct( $plugin_name );

					add_action( 'init', array( $this, 'privacy_personal_data_init' ), 99 );
					add_filter( 'wp_privacy_anonymize_data', array( $this, 'privacy_anonymize_data_filter' ), 10, 3 );
				}

				/*
				 * GDPR Privacy Init
				 *
				 * @author Andrea Grillo <andrea.grillo@yithemes.com>
				 * @since 2.6.0
				 * @return void
				 *
				 */
				public function privacy_personal_data_init(){
					// set up vendors data exporter
					add_filter( 'wp_privacy_personal_data_exporters', array( $this, 'register_exporter' ) );

					// set up vendors data eraser
					add_filter( 'wp_privacy_personal_data_erasers', array( $this, 'register_eraser' ) );
				}

				/**
				 * Register exporters for affiliates plugin
				 *
				 * @param $exporters array Array of currently registered exporters
				 * @return array Array of filtered exporters
				 * @since 2.6.0
				 */
				public function register_exporter( $exporters ) {
					if( apply_filters( 'yith_wcmv_export_vendor_data', true ) ){
						// exports data about vendor store details
						$exporters['yith_wcmv_vendor_details'] = array(
							'exporter_friendly_name' => __( 'Vendor Data', 'yith-woocommerce-product-vendors' ),
							'callback'               => array( $this, 'store_details_export' )
						);
					}

					if( 'yes' == get_option( 'yith_vendor_exports_commissions', 'yes' ) ){
						// exports data about vendor store details
						$exporters['yith_wcmv_vendor_commissions_data'] = array(
							'exporter_friendly_name' => __( 'Vendor Commissions Data', 'yith-woocommerce-product-vendors' ),
							'callback'               => array( $this, 'commissions_details_export' )
						);
					}

					return $exporters;
				}

				/**
				 * Register eraser for affiliate plugin
				 *
				 * @param $erasers array Array of currently registered erasers
				 * @return array Array of filtered erasers
				 * @since 2.6.0
				 */
				public function register_eraser( $erasers ) {

					if ( 'yes' == get_option( 'yith_vendor_remove_user_id_in_commissions', 'no' ) ) {
						// exports data about vendor store details
						$erasers['yith_wcmv_vendor_commissions_data'] = array(
							'eraser_friendly_name' => __( 'Vendor Commissions Data', 'yith-woocommerce-product-vendors' ),
							'callback'             => array( $this, 'commissions_details_eraser' )
						);
					}

					if ( 'yes' == get_option( 'yith_vendor_remove_vendor_profile_data', 'no' ) ) {
						// exports data about vendor store details
						$erasers['yith_wcmv_vendor_details'] = array(
							'eraser_friendly_name' => __( 'Vendor Data', 'yith-woocommerce-product-vendors' ),
							'callback'             => array( $this, 'store_details_eraser' )
						);
					}

					return $erasers;
				}

				/**
				 * Export vendor details
				 */
				public function store_details_export( $user_email ){
					$user = $this->get_user_by_email( $user_email );
					$data_to_export = $personal_data = array();
					$done = true;

					if( $user instanceof WP_User ){
						$user_id = $user->get( 'ID' );
						$vendor = yith_get_vendor( $user_id, 'user' );

						if( $vendor->is_valid() && $vendor->is_owner( $user_id ) ){
							$to_exports = $this->get_vendor_personal_data_fields();
							foreach( $to_exports as $to_export => $label ){
								$new_record = $new_social_record = array();
								if( 'socials' != $to_export ){
									$value = $vendor->$to_export;
									if( ! empty( $value ) ){
										$new_record = array(
											'name'  => $label,
											'value' => $vendor->$to_export
										);

										$personal_data[] = $new_record;
									}
								}

								else {
									$social_fields = YITH_Vendors()->get_social_fields();
									foreach( $vendor->socials as $social => $uri ){
										if( ! empty( $uri ) ){
											$new_social_record = array(
												'name'  => $social_fields['social_fields'][ $social ]['label'],
												'value' => $uri
											);

											$personal_data[] = $new_social_record;
										}
									}
								}
							}

							$data_to_export[] = array(
								'group_id'    => 'yith_wcmv_vendor_data',
								'group_label' => __( 'Vendor Data', 'yith-woocommerce-product-vendors' ),
								'item_id'     => 'vendor-' . $vendor->id,
								'data'        => $personal_data,
							);

							$done = true;
						}
					}

					return array(
						'data' => $data_to_export,
						'done' => $done
					);
				}

				/**
				 * Export vendor commissions details
				 */
				public function commissions_details_export( $user_email, $page ){
					$user           = $this->get_user_by_email( $user_email );
					$data_to_export = $personal_data = array();
					$number         = 50;
					$page           = (int) $page;
					$offset         = $number * ( $page - 1 );
					$done           = true;
					if( $user instanceof WP_User ) {
						$user_id = $user->get( 'ID' );
						$vendor = yith_get_vendor( $user_id, 'user' );
						if ( $vendor->is_valid() && $vendor->is_owner( $user_id ) ) {
							$args = array(
								'vendor_id' => $vendor->id,
								'status' => 'all',
								'number' => $number,
								'paged' => $page,
								'offset' => $offset
							);
							$commissions = YITH_Commissions()->get_commissions( $args );

							if( 0 < count( $commissions ) ){
								$to_exports = array(
									'id'            => __( 'Commission ID', 'yith-woocommerce-product-vendors' ),
									'order_id'      => __( 'Refer to Order ID', 'yith-woocommerce-product-vendors' ),
									'user_id'       => __( 'User ID', 'yith-woocommerce-product-vendors' ),
									'vendor_id'     => __( 'Vendor ID', 'yith-woocommerce-product-vendors' ),
									'rate'          => __( 'Commission rate (%)', 'yith-woocommerce-product-vendors' ),
									'amount'        => __( 'Commission amount', 'yith-woocommerce-product-vendors' ),
									'status'        => __( 'Commission status', 'yith-woocommerce-product-vendors' ),
									'type'          => __( 'Commission Type', 'yith-woocommerce-product-vendors' ),
									'last_edit'     => __( 'Last Update', 'yith-woocommerce-product-vendors' ),
									'last_edit_gmt' => __( 'Last Update (GMT)', 'yith-woocommerce-product-vendors' ),
								);

								foreach( $commissions as $commission_id ){
									$commission = YITH_Commission( $commission_id );
									if( ! empty( $commission ) && $commission instanceof YITH_Commission ){
										$personal_data = array();
										foreach( $to_exports as $to_export => $label ){
											$new_record = array();
											$value = $commission->$to_export;
											if( ! empty( $value ) ){

												if( 'rate' == $to_export ){
													$value = $value * 100 ;
												}

												if( 'amount' == $to_export ){
													$value = wc_price( $value, array( 'currency' => yith_wcmv_get_order_currency( $commission->get_order() ) ) );
												}

												$new_record = array(
													'name'  => $label,
													'value' => $value
												);

												$personal_data[] = $new_record;
											}
										}

										$data_to_export[] = array(
											'group_id'    => 'yith_wcmv_vendor_commissions_data',
											'group_label' => __( 'Vendor Commissions Data', 'yith-woocommerce-product-vendors' ),
											'item_id'     => 'commissions-' . $commission->id,
											'data'        => $personal_data,
										);
									}
								}
								$done = $number > count( $commissions );
							}

							else {
								$done = true;
							}
						}
					}

					return array(
						'data' => $data_to_export,
						'done' => $done
					);
				}

				/**
				 * Eraser Vendor Details
				 */
				public function store_details_eraser( $user_email ){
					$user     = $this->get_user_by_email( $user_email );
					$response = array(
						'items_removed'  => false,
						'items_retained' => false,
						'messages'       => array(),
						'done'           => true,
					);

					if( $user instanceof WP_USer ){
						//Check if current user is a vendor
						$user_id = $user->get( 'ID' );
						$vendor  = yith_get_vendor( $user_id, 'user' );
						if( $vendor->is_valid()  ){
							if( $vendor->is_owner( $user_id ) ){
								$to_eraser          = $this->get_vendor_personal_data_fields_type();
								$fields_description = $this->get_vendor_personal_data_fields();

								//Remove Vendor Owner
								foreach( $vendor->get_admins() as $admin_id ){
									$user_meta_key = delete_user_meta( $admin_id, YITH_Vendors()->get_user_meta_key() );
								}

								$user_meta_owner = delete_user_meta( $vendor->get_owner(), YITH_Vendors()->get_user_meta_owner() );

								if( $user_meta_key ){
									$response['messages'][] = _x( 'Removed vendor "Admins"', '[GDPR Message]', 'yith-woocommerce-product-vendors' );
								}

								if( $user_meta_owner ){
									$response['messages'][] = _x( 'Removed vendor "Owner"', '[GDPR Message]', 'yith-woocommerce-product-vendors' );
								}

								$user->remove_role( YITH_Vendors()->get_role_name() );
								$user->add_role( 'customer' );

								/**
								 * No Vendor Owner no admins
								 */
								$vendor->admins = array();

								foreach( $to_eraser as $field => $type ){
									if( 'socials' != $field ){
										$vendor->$field = $this->privacy_anonymize_data( $type, $vendor->$field );
										$label = isset( $fields_description[ $field ] ) ? $fields_description[ $field ] : ucfirst ( str_replace( '_', ' ', $field ) );
										$response['messages'][] = sprintf( '%s "%s"', _x( 'Removed vendor', '[GDPR Message]', 'yith-woocommerce-product-vendors' ), $label );
									}

									if ( 'socials' == $field ) {
										$socials         = array();
										$removed_socials = false;
										foreach ( $vendor->socials as $social => $uri ) {
											if ( ! empty( $uri ) ) {
												$socials[ $social ] = $this->privacy_anonymize_data( 'url', $uri );
												$removed_socials    = true;
											}
										}
										if ( $removed_socials ) {
											$response['messages'][] = sprintf( 'Removed vendor "Social Network" urls', $fields_description[ $field ] );
										}

										$vendor->socials = $socials;					}
								}
								$response['items_removed'] = true;
							}

							else {
								//Vendor is valid, but it's not owner...so the user is an administrator
								$admins = $vendor->admins;
								$admin_key = array_search( $user_id, $admins );

								if( ! empty( $admin_key ) ){
									unset( $admins[ $admin_key ] );
								}

								$vendor->admins = $admins;

								$user_meta_key = delete_user_meta( $user_id, YITH_Vendors()->get_user_meta_key() );

								if( $user_meta_key ){
									$response['messages'][] = _x( 'Removed vendor "Admins"', '[GDPR Message]', 'yith-woocommerce-product-vendors' );
								}
							}
						}
					}

					return $response;
				}

				/**
				 * Eraser Vendor Commissions Details
				 */
				public function commissions_details_eraser( $user_email, $page ){
					$user     = $this->get_user_by_email( $user_email );
					$number   = 50;
					$page     = (int) $page;
					$offset   = $number * ( $page - 1 );
					$response = array(
						'items_removed'  => false,
						'items_retained' => false,
						'messages'       => array(),
						'done'           => true,
					);

					if( $user instanceof WP_USer ){
						$user_id = $user->get( 'ID' );
						$vendor = yith_get_vendor( $user_id, 'user' );
						if ( $vendor->is_valid() && $vendor->is_owner( $user_id )) {

							$args        = array(
								'vendor_id' => $vendor->id,
								'status'    => 'all',
								'number'    => $number,
								'paged'     => $page,
								'offset'    => $offset
							);
							$commissions = YITH_Commissions()->get_commissions( $args );

							if ( 0 < count( $commissions ) ) {
								foreach ( $commissions as $commission_id ) {
									$commission = YITH_Commission( $commission_id );
									if ( ! empty( $commission ) && $commission instanceof YITH_Commission ) {
										$commission->user_id = 0;
									}
								}
								$message = _x( 'Removed User information From Vendor Commissions', '[GDPR Message]', 'yith-woocommerce-product-vendors' );;
								$response['done']          = $number > count( $commissions );
								$response['messages'][]    = sprintf( '%s (%s/%s)', $message, $offset, ( $offset + $number ) );
								$response['items_removed'] = true;
							}

							else {
								$response['done'] = true;
							}
						}
					}

					return $response;
				}

				/**
				 * Get WP_User
				 *
				 * @param $user_email
				 */
				public function get_user_by_email( $user_email ){
					return get_user_by( 'email', $user_email );
				}

				/**
				 * @return array Vendor Personal data fields
				 */
				public function get_vendor_personal_data_fields(){
					return apply_filters( 'yith_wcmv_get_vendor_personal_data_fields', array(
							'id'           => __( 'Vendor ID', 'yith-woocommerce-product-vendors' ),
							'name'         => __( 'Store Name', 'yith-woocommerce-product-vendors' ),
							'slug'         => __( 'Store Slug', 'yith-woocommerce-product-vendors' ),
							'description'  => __( 'Store Description', 'yith-woocommerce-product-vendors' ),
							'paypal_email' => __( 'Owner PayPal Email', 'yith-woocommerce-product-vendors' ),
						)
					);
				}

				/**
				 * @return array Vendor Personal data fields
				 */
				public function get_vendor_personal_data_fields_type() {
					return apply_filters( 'yith_wcmv_get_vendor_personal_data_fields_type', array(
							'name'         => 'yith_wcmv_taxonomy_name',
							'slug'         => 'yith_wcmv_taxonomy_slug',
							'description'  => 'longtext',
							'paypal_email' => 'email',
						)
					);
				}

				/**
				 * Wrapper for Anonymize data
				 *
				 * @return string
				 */
				public function privacy_anonymize_data( $data_type, $value ){
					$return = function_exists( 'wp_privacy_anonymize_data' ) ? wp_privacy_anonymize_data( $data_type, $value ) : '';
					return $return;
				}

				/**
				 * Main YITH_Reports Instance
				 *
				 * @static
				 *
				 * @return YITH_Vendors_Report Main instance
				 * @since  1.0
				 * @author Andrea Grillo <andrea.grillo@yithemes.com>
				 */
				public static function instance() {
					if ( ! isset( self::$_instance ) || is_null( self::$_instance ) ) {
						self::$_instance = new self();
					}

					return self::$_instance;
				}

				/**
				 * Filters anonymize data
				 */
				public function privacy_anonymize_data_filter( $anonymous, $type, $data ){
					if( 'yith_wcmv_profile_media' == $type ){
						if( 'yes' == get_option( 'yith_vendor_delete_vendor_media_profile_data', 'no' ) ){
							wp_delete_attachment( $data, true );
						}
						$anonymous = 0;
					}

					if( 'yith_wcmv_taxonomy_name' == $type ){
						$anonymous = sprintf( '[%s %s]', __( 'deleted vendor', 'yith-woocommerce-product-vendors' ), wc_rand_hash() );
					}

					if( 'yith_wcmv_taxonomy_slug' == $type ){
						$anonymous = sprintf( '[%s%s]', __( 'deleted-vendor-', 'yith-woocommerce-product-vendors' ), wc_rand_hash() );
					}

					return $anonymous;
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
							            '<li>' . __( 'The information required to start a vendor shop are the following: name and store description, email, telephone number, PayPal email, information about commissions and issued payments.', 'yith-woocommerce-product-vendors' ) . '</li>' .
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
					}

					return $message;
				}
			}
		}

		if( defined( 'YITH_WPV_FREE_INIT' ) && YITH_WPV_FREE_INIT ){
			return new YITH_Vendors_Privacy();
		}
	}
}

add_action( 'plugins_loaded', 'YITH_Vendors_Privacy', 20 );

