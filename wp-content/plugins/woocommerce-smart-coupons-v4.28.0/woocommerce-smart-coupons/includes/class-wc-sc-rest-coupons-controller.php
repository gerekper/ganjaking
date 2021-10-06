<?php
/**
 * REST API Coupons controller for Smart Coupons
 *
 * Handles requests to the wc/sc/v1/coupons endpoint.
 *
 * @author      StoreApps
 * @since       4.10.0
 * @version     1.1.0
 *
 * @package     woocommerce-smart-coupons/includes/
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WC_SC_REST_Coupons_Controller' ) ) {

	/**
	 * REST API Coupons controller class.
	 *
	 * @package Automattic/WooCommerce/RestApi
	 * @extends WC_REST_CRUD_Controller
	 */
	class WC_SC_REST_Coupons_Controller extends WC_REST_Coupons_Controller {

		/**
		 * Endpoint namespace.
		 *
		 * @var string
		 */
		protected $namespace = 'wc/v3/sc';

		/**
		 * Route base.
		 *
		 * @var string
		 */
		protected $rest_base = 'coupons';

		/**
		 * Post type.
		 *
		 * @var string
		 */
		protected $post_type = 'shop_coupon';

		/**
		 * Consturctor
		 */
		public function __construct() {
			add_filter( "woocommerce_rest_prepare_{$this->post_type}_object", array( $this, 'handle_response_data' ), 99, 3 );
		}

		/**
		 * Register the routes for coupons.
		 */
		public function register_routes() {
			register_rest_route(
				$this->namespace,
				'/' . $this->rest_base,
				array(
					array(
						'methods'             => WP_REST_Server::CREATABLE,
						'callback'            => array( $this, 'create_item' ),
						'permission_callback' => array( $this, 'create_item_permissions_check' ),
						'args'                => $this->get_endpoint_args_for_item_schema( WP_REST_Server::CREATABLE ),
					),
					'schema' => array( $this, 'get_public_item_schema' ),
				)
			);
		}

		/**
		 * Prepare a single coupon for create or update.
		 *
		 * @param  WP_REST_Request $request Request object.
		 * @param  bool            $creating If is creating a new object.
		 * @return WP_Error|WC_Data
		 */
		protected function prepare_object_for_database( $request, $creating = false ) {
			global $woocommerce_smart_coupon;

			$id                 = isset( $request['id'] ) ? absint( $request['id'] ) : 0;
			$coupon             = new WC_Coupon( $id );
			$schema             = $this->get_item_schema();
			$data_keys          = array_keys( array_filter( $schema['properties'], array( $this, 'filter_writable_props' ) ) );
			$email_restrictions = ( ! empty( $request['email_restrictions'] ) && count( $request['email_restrictions'] ) === 1 ) ? $request['email_restrictions'] : '';

			// Validate required POST fields.
			if ( $creating ) {
				if ( empty( $request['code'] ) ) {
					$request['code'] = $woocommerce_smart_coupon->generate_unique_code( $email_restrictions );
				} else {
					$_coupon          = new WC_Coupon( $request['code'] );
					$is_auto_generate = ( is_object( $_coupon ) && is_callable( array( $_coupon, 'get_meta' ) ) ) ? $_coupon->get_meta( 'auto_generate_coupon' ) : 'no';
					if ( 'yes' === $is_auto_generate ) {
						$request['code'] = $woocommerce_smart_coupon->generate_unique_code( $email_restrictions );
						foreach ( $data_keys as $key ) {
							if ( empty( $request[ $key ] ) ) {
								switch ( $key ) {
									case 'code':
										// do nothing.
										break;
									case 'meta_data':
										$meta_data     = ( is_object( $_coupon ) && is_callable( array( $_coupon, 'get_meta_data' ) ) ) ? $_coupon->get_meta_data() : null;
										$new_meta_data = array();
										if ( ! empty( $meta_data ) ) {
											foreach ( $meta_data as $meta ) {
												if ( is_object( $meta ) && is_callable( array( $meta, 'get_data' ) ) ) {
													$data = $meta->get_data();
													if ( isset( $data['id'] ) ) {
														unset( $data['id'] );
													}
													$new_meta_data[] = $data;
												}
											}
										}
										$request[ $key ] = $new_meta_data;
										break;
									case 'description':
										$request[ $key ] = ( is_object( $_coupon ) && is_callable( array( $_coupon, 'get_description' ) ) ) ? $_coupon->get_description() : null;
										break;
									default:
										if ( is_callable( array( $_coupon, "get_{$key}" ) ) ) {
											$request[ $key ] = $_coupon->{"get_{$key}"}();
										}
										break;
								}
							}
						}
					}
				}
			}

			// Handle all writable props.
			foreach ( $data_keys as $key ) {
				$value = $request[ $key ];

				if ( ! is_null( $value ) ) {
					switch ( $key ) {
						case 'code':
							$coupon_code  = wc_format_coupon_code( $value );
							$id           = $coupon->get_id() ? $coupon->get_id() : 0;
							$id_from_code = wc_get_coupon_id_by_code( $coupon_code, $id );

							if ( $id_from_code ) {
								return new WP_Error( 'woocommerce_rest_coupon_code_already_exists', __( 'The coupon code already exists', 'woocommerce' ), array( 'status' => 400 ) );
							}

							$coupon->set_code( $coupon_code );
							break;
						case 'meta_data':
							if ( is_array( $value ) ) {
								foreach ( $value as $meta ) {
									$coupon->update_meta_data( $meta['key'], $meta['value'], isset( $meta['id'] ) ? $meta['id'] : '' );
								}
							}
							break;
						case 'description':
							$coupon->set_description( wp_filter_post_kses( $value ) );
							break;
						default:
							if ( is_callable( array( $coupon, "set_{$key}" ) ) ) {
								$coupon->{"set_{$key}"}( $value );
							}
							break;
					}
				}
			}

			/**
			 * Filters an object before it is inserted via the REST API.
			 *
			 * The dynamic portion of the hook name, `$this->post_type`,
			 * refers to the object type slug.
			 *
			 * @param WC_Data         $coupon   Object object.
			 * @param WP_REST_Request $request  Request object.
			 * @param bool            $creating If is creating a new object.
			 */
			return apply_filters( "woocommerce_rest_pre_insert_{$this->post_type}_object", $coupon, $request, $creating );
		}

		/**
		 * Handle REST response data
		 *
		 * @param WP_REST_Response|mixed $response The response.
		 * @param WC_Coupon|mixed        $object The object.
		 * @param WP_REST_Request|mixed  $request The request.
		 * @return WP_REST_Response
		 */
		public function handle_response_data( $response = null, $object = null, $request = null ) {
			global $woocommerce_smart_coupon;

			if ( ! empty( $request['sc_is_send_email'] ) && 'yes' === $request['sc_is_send_email'] ) {
				$is_send_email      = $woocommerce_smart_coupon->is_email_template_enabled();
				$email_restrictions = ( ! empty( $request['email_restrictions'] ) ) ? current( $request['email_restrictions'] ) : '';
				if ( 'yes' === $is_send_email && ! empty( $email_restrictions ) ) {
					$coupon      = array(
						'code'   => ( is_object( $object ) && is_callable( array( $object, 'get_code' ) ) ) ? $object->get_code() : '',
						'amount' => ( is_object( $object ) && is_callable( array( $object, 'get_amount' ) ) ) ? $object->get_amount() : 0,
					);
					$action_args = apply_filters(
						'wc_sc_email_coupon_notification_args',
						array(
							'email'         => $email_restrictions,
							'coupon'        => $coupon,
							'discount_type' => ( is_object( $object ) && is_callable( array( $object, 'get_discount_type' ) ) ) ? $object->get_discount_type() : '',
						)
					);
					// Trigger email notification.
					do_action( 'wc_sc_email_coupon_notification', $action_args );
				}
			}

			if ( ! empty( $request['sc_is_html'] ) && 'yes' === $request['sc_is_html'] ) {
				$data = '';
				ob_start();
				do_action(
					'wc_sc_paint_coupon',
					array(
						'coupon'         => $object,
						'with_css'       => 'yes',
						'with_container' => 'yes',
					)
				);
				$data     = ob_get_clean();
				$response = rest_ensure_response( $data );
			}

			return $response;
		}

	}

}
