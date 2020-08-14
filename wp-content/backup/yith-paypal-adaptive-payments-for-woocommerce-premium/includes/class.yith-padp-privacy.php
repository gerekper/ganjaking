<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'YITH_Adaptive_Payments_Privacy' ) && class_exists( 'YITH_Privacy_Plugin_Abstract' ) ) {

	class YITH_Adaptive_Payments_Privacy extends YITH_Privacy_Plugin_Abstract{

		public function __construct() {

			$plugin_info = get_plugin_data( YITH_PAYPAL_ADAPTIVE_FILE );

			$name = $plugin_info['Name'];

			parent::__construct( $name );

			add_action( 'admin_init', array( $this, 'privacy_personal_data_init' ), 99 );
		}

		public function privacy_personal_data_init() {
			// set up vendors data exporter

			add_filter( 'wp_privacy_personal_data_exporters', array( $this, 'register_exporter' ) );

			// set up vendors data eraser
			add_filter( 'wp_privacy_personal_data_erasers', array( $this, 'register_eraser' ) );
		}

		/**
		 * @param array $exporters
		 */
		public function register_exporter( $exporters ) {

			$exporter_commission = get_option( 'ywpadp_export_commission', 'yes' );

			if ( 'yes' == $exporter_commission ) {

				$exporters['yith_padp_exporter'] = array(
					'exporter_friendly_name' => __( 'User commission data', 'yith-paypal-adaptive-payments-for-woocommerce' ),
					'callback'               => array( $this, 'exporter_user_commission' )

				);
			}

			$exporters['yith_padp_user_exporter'] = array(
				'exporter_friendly_name' => __( 'User data in Adaptive Payment', 'yith-paypal-adaptive-payments-for-woocommerce' ),
				'callback'               => array( $this, 'exporter_user_data' )
			);

			return $exporters;
		}


		/**
		 * @param string $user_email
		 */
		public function exporter_user_data( $user_email ) {

			$user = get_user_by( 'email', $user_email );

			$data_to_export = array();
			if ( $user instanceof WP_User ) {

				$user_id = $user->ID;

				$paypal_email = get_user_meta( $user_id, 'yith_paypal_email', true );

				$data = array( array( 'name' => 'PayPal Email', 'value' => $paypal_email ) );

				$data_to_export[] = array(
					'group_id'    => 'ywpadp_user_info',
					'group_label' => __( 'PayPal Adaptive Payments', 'yith-paypal-adaptive-payments-for-woocommerce' ),
					'data'        => $data,
					'item_id'     => 'adaptive_paypal_email'
				);
			}

			return array(
				'data' => $data_to_export,
				'done' => true
			);
		}

		/**
		 * @param string $user_email
		 *
		 * @return array
		 */
		public function exporter_user_commission( $user_email, $page ) {

			$user           = get_user_by( 'email', $user_email );
			$data_to_export = $personal_data = array();
			$number         = 50;
			$page           = (int) $page;
			$offset         = $number * ( $page - 1 );
			$done           = true;
			if ( $user instanceof WP_User ) {

				$user_id = $user->ID;

				$commissions = YITH_PADP_Receiver_Commission()->get_transaction( $user_id, false, $offset, $page );

				$labels = array(
					'ID'                 => __( 'Commission ID', 'yith-paypal-adaptive-payments-for-woocommerce' ),
					'order_id'           => __( 'Order Number', 'yith-paypal-adaptive-payments-for-woocommerce' ),
					'user_id'            => __( 'User ID', 'yith-paypal-adaptive-payments-for-woocommerce' ),
					'transaction_id'     => __( 'Transaction ID', 'yith-paypal-adaptive-payments-for-woocommerce' ),
					'transaction_status' => __( 'Transaction status', 'yith-paypal-adaptive-payments-for-woocommerce' ),
					'transaction_value'  => __( 'Transaction value', 'yith-paypal-adaptive-payments-for-woocommerce' ),
					'transaction_date'   => __( 'Transaction date', 'yith-paypal-adaptive-payments-for-woocommerce' ),

				);
				if ( 0 < count( $commissions ) ) {

					foreach ( $commissions as $commission ) {
						$id = $commission['ID'];
						foreach ( $commission as $label => $value ) {

							if ( 'transaction_value' == $label ) {
								$value = wc_price( $value );
							}
							$personal_data[] = array(
								'name'  => $labels[ $label ],
								'value' => $value
							);
						}

						$data_to_export[] = array(
							'group_id'    => 'yith_padp_commissions_data',
							'group_label' => __( 'User Commissions Data (Adaptive Payment)', 'yith-paypal-adaptive-payments-for-woocommerce' ),
							'item_id'     => 'padp_commissions-' . $id,
							'data'        => $personal_data,
						);
					}

					$done = $number > count( $commissions );
				} else {
					$done = true;
				}
			}

			return array(
				'data' => $data_to_export,
				'done' => $done
			);
		}

		/**
		 * @param array $erasers
		 *
		 * @return array
		 */
		public function register_eraser( $erasers ) {

			$eraser_commission = get_option( 'ywpadp_eraser_commission', 'no' );

			if ( 'yes' == $eraser_commission ) {

				$erasers['yith_padp_eraser'] = array(
					'eraser_friendly_name' => __( 'User commission data', 'yith-paypal-adaptive-payments-for-woocommerce' ),
					'callback'             => array( $this, 'eraser_user_commission' )
				);
			}


			$eraser_user_info = get_option( 'ywpadp_eraser_user_data', 'no' );

			if ( 'yes' == $eraser_user_info ) {
				$erasers['yith_padp_user_info_eraser'] = array(
					'eraser_friendly_name' => __( 'User info data', 'yith-paypal-adaptive-payments-for-woocommerce' ),
					'callback'             => array( $this, 'eraser_user_info' )
				);
			}

			return $erasers;
		}

		/**
		 * @param string $user_email
		 * @param int $page
		 *
		 * @return array
		 */
		public function eraser_user_commission( $user_email, $page ) {

			$user     = get_user_by( 'email', $user_email );
			$number   = 50;
			$page     = (int) $page;
			$offset   = $number * ( $page - 1 );
			$response = array(
				'items_removed'  => false,
				'items_retained' => false,
				'messages'       => array(),
				'done'           => true,
			);


			if ( $user instanceof WP_User ) {

				$user_id     = $user->ID;
				$commissions = YITH_PADP_Receiver_Commission()->get_transaction( $user_id, false, $offset, $page );

				if ( 0 < count( $commissions ) ) {

					foreach ( $commissions as $commission ) {
						$id = $commission['ID'];

						$anonymize = YITH_PADP_Receiver_Commission()->anonymize_transaction( $id );
					}
					$message                   = _x( 'Removed User information From Adaptive Payment Commissions', '[GDPR Message]', 'yith-paypal-adaptive-payments-for-woocommerce' );
					$response['done']          = $number > count( $commissions );
					$response['messages'][]    = sprintf( '%s (%s/%s)', $message, $offset, ( $offset + $number ) );
					$response['items_removed'] = true;
				} else {
					$response['done'] = true;
				}
			}

			return $response;
		}

		/**
		 * @param string $user_email
		 *
		 * @return array
		 */
		public function eraser_user_info( $user_email ) {

			$user     = get_user_by( 'email', $user_email );
			$response = array(
				'items_removed'  => false,
				'items_retained' => false,
				'messages'       => array(),
				'done'           => true,
			);
			if ( $user instanceof WP_User ) {

				$user_id      = $user->ID;
				$paypal_email = function_exists( 'wp_privacy_anonymize_data' ) ? wp_privacy_anonymize_data( 'email' ) : 'deleted@email.com';

				update_user_meta( $user_id, 'yith_paypal_email', $paypal_email );

				$response['messages'][]    = __( 'PayPal email removed', 'yith-paypal-adaptive-payments-for-woocommerce' );
				$response['items_removed'] = true;
			}

			return $response;
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
					$message =  '<p>' . __( 'We collect information about you during the registration and checkout process on our store.', 'yith-paypal-adaptive-payments-for-woocommerce' ) . '</p>' .
					            '<p>' . __( 'While you visit our site, we’ll track:', 'yith-paypal-adaptive-payments-for-woocommerce' ) . '</p>' .
					            '<ul>' .
					            '<li>' . __( 'User information: we will use these data to allows them to sell products on this website in exchange of a commission fee on each sale.', 'yith-paypal-adaptive-payments-for-woocommerce' ) . '</li>' .
					            '<li>' . __( 'The information required are the following: paypal email and commission rate', 'yith-paypal-adaptive-payments-for-woocommerce' ) . '</li>' .
					            '</ul>';
					break;

				case 'has_access':
					$message = '<p>' . __( 'Members of our team have access to the information you provide us. For example, both Administrators and Shop Managers can access:', 'yith-paypal-adaptive-payments-for-woocommerce' ) . '</p>' .
					           '<ul>' .
					           '<li>' . __( 'User information', 'yith-paypal-adaptive-payments-for-woocommerce' ) .'</li>' .
					           '<li>' . __( 'Data concerning commissions earned by the user','yith-paypal-adaptive-payments-for-woocommerce'  ) .'</li>' .
					           '<li>' . __( 'Data about payments','yith-paypal-adaptive-payments-for-woocommerce'  ) .'</li>' .
					           '</ul>' .
					           '<p>' . __( 'Our team members have access to this information to help fulfill orders, process orders and support you.', 'yith-paypal-adaptive-payments-for-woocommerce' ) . '</p>';
					break;

				case 'payments':
					$message = '<p>' . __( 'We send payments to vendors through PayPal. When processing payments, some of your data will be passed to PayPal, including information required to process or support the payment, such as the purchase total and billing information.', 'woocommerce' ) . '</p>' .
					           '<p>' . __( 'Please see the <a href="https://www.paypal.com/us/webapps/mpp/ua/privacy-full">PayPal Privacy Policy</a> for more details.', 'woocommerce' ) . '</p>' ;
					break;

				case 'share':
					$message = '<p>' . __( 'We share information with third parties who help us provide our orders and store services to you.', 'woocommerce' ) . '</p>';
					break;

			}

			return $message;
		}
	}
}