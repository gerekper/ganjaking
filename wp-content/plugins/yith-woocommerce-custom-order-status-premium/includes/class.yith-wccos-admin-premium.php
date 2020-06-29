<?php
if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_WCCOS_PREMIUM' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Implements features of FREE version of YITH WooCommerce Custom Order Status
 *
 * @class   YITH_WCCOS_Admin_Premium
 * @package YITH WooCommerce Custom Order Status
 * @since   1.0.0
 * @author  Yithemes
 */

if ( ! class_exists( 'YITH_WCCOS_Admin_Premium' ) ) {
	/**
	 * Admin class.
	 * The class manage all the admin behaviors.
	 *
	 * @since 1.0.0
	 */
	class YITH_WCCOS_Admin_Premium extends YITH_WCCOS_Admin {
		/**
		 * Single instance of the class
		 *
		 * @var YITH_WCCOS_Admin_Premium
		 * @since 1.0.0
		 */
		protected static $_instance;

		public $core_order_statuses;

		/**
		 * Constructor
		 *
		 * @access public
		 * @since  1.0.0
		 */
		protected function __construct() {
			// store the default WooCommerce order statuses
			$this->core_order_statuses = wc_get_order_statuses();

			parent::__construct();
			add_action( 'woocommerce_order_status_changed', array( $this, 'woocommerce_order_status_changed' ), 10, 3 );
			add_filter( 'woocommerce_reports_order_statuses', array( $this, 'woocommerce_reports_order_statuses' ) );

			if ( is_admin() ) {
				add_filter( 'yith_wccos_tabs_metabox', array( $this, 'metabox_premium' ) );

				// register plugin to licence/update system
				add_action( 'wp_loaded', array( $this, 'register_plugin_for_activation' ), 99 );
				add_action( 'admin_init', array( $this, 'register_plugin_for_updates' ) );

				add_filter( 'yit_fw_metaboxes_type_args', array( $this, 'custom_type_icons' ) );
				add_filter( 'yit_fw_metaboxes_type_args', array( $this, 'custom_type_select' ) );

				add_filter( 'yith_wccos_settings_admin_tabs', array( $this, 'settings_admin_tabs' ) );
				/**
				 * Import Custom Order Statuses
				 *
				 * @since 1.1.4
				 */
				add_action( 'wp_loaded', array( $this, 'import_custom_statuses' ), 99 );
			}

			add_filter( 'woocommerce_screen_ids', array( $this, 'add_screen_id_to_woocommerce' ), 10, 1 );
		}

		public function settings_admin_tabs( $tabs ) {
			$tabs = array(
				'order-statuses' => __( 'Order Statuses', 'yith-woocommerce-custom-order-status' ),
				'settings'       => __( 'Settings', 'yith-woocommerce-custom-order-status' ),
			);

			if ( ! current_user_can( 'manage_options' ) ) {
				unset( $tabs['settings'] );
			}

			return $tabs;
		}

		/**
		 * import custom statuses
		 *
		 * @since 1.1.4
		 */
		public function import_custom_statuses() {
			if ( isset( $_REQUEST['yith-wcos-import-custom-statuses'] ) &&
				 isset( $_REQUEST['yith-wcos-import_nonce'] ) &&
				 wp_verify_nonce( $_REQUEST['yith-wcos-import_nonce'], 'import-custom-statuses' ) ) {
				$order_statuses        = wc_get_order_statuses();
				$yith_order_status_ids = get_posts( array(
														'posts_per_page' => - 1,
														'post_type'      => 'yith-wccos-ostatus',
														'post_status'    => 'publish',
														'fields'         => 'ids',
													) );
				$yith_order_statuses   = array();
				foreach ( $yith_order_status_ids as $id ) {
					$slug                                 = get_post_meta( $id, 'slug', true );
					$title                                = get_the_title( $id );
					$yith_order_statuses[ 'wc-' . $slug ] = $title;
				}

				$order_statuses_to_import = array_diff( array_keys( $order_statuses ), array_keys( $this->core_order_statuses ), array_keys( $yith_order_statuses ) );

				if ( ! ! $order_statuses_to_import ) {
					foreach ( $order_statuses_to_import as $slug ) {
						$title   = $order_statuses[ $slug ];
						$slug    = substr( $slug, 3 );
						$post_id = wp_insert_post( array(
													   'post_name'   => $slug,
													   'post_title'  => $title,
													   'post_type'   => 'yith-wccos-ostatus',
													   'post_status' => 'publish',
												   ) );

						if ( ! ! $post_id ) {
							update_post_meta( $post_id, 'slug', $slug );
							update_post_meta( $post_id, 'graphicstyle', 'text' );
							update_post_meta( $post_id, 'color', '#a36597' );
						}
					}
				}

				wp_redirect( add_query_arg( array( 'post_type' => 'yith-wccos-ostatus' ), admin_url( 'edit.php' ) ) );
				exit();
			}
		}

		/**
		 * Add Icon Column in WP_List_Table of order custom statuses
		 * PREMIUM
		 *
		 * @return   array
		 * @since    1.1.1
		 * @author   Leanza Francesco <leanzafrancesco@gmail.com>
		 */
		public function order_status_columns( $columns ) {
			$columns = parent::order_status_columns( $columns );
			$date    = $columns['date'];
			unset( $columns['date'] );

			$options_with_icons = array(
				'can-cancel'          => __( 'User can cancel', 'yith-woocommerce-custom-order-status' ),
				'can-pay'             => __( 'User can pay', 'yith-woocommerce-custom-order-status' ),
				'is-paid'             => __( 'Order is paid', 'yith-woocommerce-custom-order-status' ),
				'downloads-permitted' => __( 'Allow Downloads', 'yith-woocommerce-custom-order-status' ),
				'display-in-reports'  => __( 'Display in Reports', 'yith-woocommerce-custom-order-status' ),
				'restore-stock'       => __( 'Restore Stock', 'yith-woocommerce-custom-order-status' ),
				'show-in-actions'     => __( 'Show always in Actions', 'yith-woocommerce-custom-order-status' ),
				'send-email-to'       => __( 'Send email to', 'yith-woocommerce-custom-order-status' ),
			);

			$new_columns = array(
				'yith-wccos-status_type' => __( 'Status Type', 'yith-woocommerce-custom-order-status' ),
				'yith-wccos-slug'        => __( 'Slug', 'yith-woocommerce-custom-order-status' ),
				'yith-wccos-nextactions' => __( 'Next Actions', 'yith-woocommerce-custom-order-status' ),
			);

			foreach ( $options_with_icons as $key => $label ) {
				$new_columns[ 'yith-wccos-' . $key ] = "<span class='yith-wccos-{$key}-head tips' data-tip='$label'>$label</span>";
			}

			$columns = array_merge( $columns, $new_columns );

			$columns['date'] = $date;

			return $columns;
		}

		/**
		 * Print custom columns in WP_List_Table of order custom statuses
		 * PREMIUM
		 *
		 * @return   array
		 * @since    1.1.1
		 * @author   Leanza Francesco <leanzafrancesco@gmail.com>
		 */
		public function custom_columns( $column, $post_id ) {
			parent::custom_columns( $column, $post_id );
			if ( strpos( $column, 'yith-wccos-' ) === 0 ) {
				$column = str_replace( 'yith-wccos-', '', $column );
				switch ( $column ) {
					case 'status_type':
						$status_types = array(
							'custom'     => _x( 'Custom Status', 'Select status type', 'yith-woocommerce-custom-order-status' ),
							'pending'    => _x( 'Pending Payment', 'Select status type', 'yith-woocommerce-custom-order-status' ),
							'processing' => _x( 'Processing', 'Select status type', 'yith-woocommerce-custom-order-status' ),
							'on-hold'    => _x( 'On Hold', 'Select status type', 'yith-woocommerce-custom-order-status' ),
							'completed'  => _x( 'Completed', 'Select status type', 'yith-woocommerce-custom-order-status' ),
							'cancelled'  => _x( 'Cancelled', 'Select status type', 'yith-woocommerce-custom-order-status' ),
							'refunded'   => _x( 'Refunded', 'Select status type', 'yith-woocommerce-custom-order-status' ),
							'failed'     => _x( 'Failed', 'Select status type', 'yith-woocommerce-custom-order-status' ),
						);
						if ( $value = get_post_meta( $post_id, $column, true ) ) {
							echo array_key_exists( $value, $status_types ) ? $status_types[ $value ] : $value;
						}
						break;
					case 'slug':
						if ( $value = get_post_meta( $post_id, $column, true ) ) {
							echo $value;
						}
						break;
					case 'nextactions':
						$value = get_post_meta( $post_id, $column, true );
						if ( ! ! $value && is_array( $value ) ) {
							$statuses     = wc_get_order_statuses();
							$next_actions = array();
							foreach ( $value as $status_slug ) {
								$next_actions[] = array_key_exists( $status_slug, $statuses ) ? $statuses[ $status_slug ] : $status_slug;
							}
							echo implode( ', ', $next_actions );
						}
						break;
					case 'can-cancel':
					case 'can-pay':
					case 'is-paid':
					case 'downloads-permitted':
					case 'display-in-reports':
					case 'restore-stock':
					case 'show-in-actions':
						$value = get_post_meta( $post_id, $column, true );
						$icon  = yith_plugin_fw_is_true( $value ) ? 'yes' : 'no';
						echo "<span class='yith-wccos-icon-check dashicons dashicons-$icon'></span>";
						break;
					case 'send-email-to':
						$recipients = yith_wccos_get_recipients( $post_id );
						$icon       = 'no';
						$label      = __( 'None', 'yith-woocommerce-custom-order-status' );
						if ( $recipients ) {
							$recipient_labels = yith_wccos_get_allowed_recipients();
							$icon             = 'email-alt';
							$labels           = array();
							foreach ( $recipients as $recipient ) {
								if ( isset( $recipient_labels[ $recipient ] ) ) {
									$labels[] = $recipient_labels[ $recipient ];
								}
							}

							$label = implode( ', ', $labels );
						}

						echo "<span class='yith-wccos-icon-mail-info dashicons dashicons-$icon tips' data-tip='$label'></span>";
						break;
				}
			}
		}

		/**
		 * Add Custom Order Status screen id to woocommerce
		 * to include the wc-enhanced-select script
		 *
		 * @param $screen_ids
		 *
		 * @return array
		 */
		public function add_screen_id_to_woocommerce( $screen_ids ) {
			$screen_ids[] = 'yith-wccos-ostatus';
			$screen_ids[] = 'edit-yith-wccos-ostatus';

			return $screen_ids;
		}

		public function custom_type_icons( $args ) {
			if ( isset( $args['type'] ) && $args['type'] == 'yith-wccos-icons' ) {
				$new_args = array(
					'basename' => YITH_WCCOS_DIR,
					'path'     => 'metaboxes/',
					'type'     => 'yith-wccos-icons',
					'args'     => $args['args'],
				);

				return $new_args;
			}

			return $args;
		}

		public function custom_type_select( $args ) {
			if ( isset( $args['type'] ) && $args['type'] == 'yith-wccos-select' ) {
				$new_args = array(
					'basename' => YITH_WCCOS_DIR,
					'path'     => 'metaboxes/',
					'type'     => 'yith-wccos-select',
					'args'     => $args['args'],
				);

				return $new_args;
			}

			return $args;
		}

		/**
		 * Add orders with custom statuses in Reports
		 *
		 * @return array
		 * @access public
		 * @since  1.0.0
		 * @author Leanza Francesco <leanzafrancesco@gmail.com>
		 */
		public function woocommerce_reports_order_statuses( $statuses ) {
			// fix for woocommerce refund in reports
			if ( ! is_array( $statuses ) || $statuses == array( 'refunded' ) ) {
				return $statuses;
			}

			$status_ids = get_posts( array(
										 'posts_per_page' => - 1,
										 'post_type'      => 'yith-wccos-ostatus',
										 'post_status'    => 'publish',
										 'fields'         => 'ids',
									 ) );

			$new_statuses = array();

			$display_default_statuses = array();

			foreach ( (array) $statuses as $status ) {
				$display_default_statuses[ $status ] = 1;
			}

			foreach ( $status_ids as $status_id ) {
				$display = yith_plugin_fw_is_true( get_post_meta( $status_id, 'display-in-reports', true ) );
				$slug    = get_post_meta( $status_id, 'slug', true );
				if ( $display ) {
					if ( ! in_array( $slug, (array) $statuses ) ) {
						$new_statuses[] = $slug;
					}
				} else {
					if ( in_array( $slug, (array) $statuses ) ) {
						$display_default_statuses[ $slug ] = 0;
					}
				}
			}

			foreach ( $display_default_statuses as $key => $value ) {
				if ( $value ) {
					$new_statuses[] = $key;
				}
			}

			return $new_statuses;
		}

		/**
		 * Handler for status changed; send emails for custom order statuses
		 *
		 * @return void
		 * @access public
		 * @since  1.0.0
		 * @author Leanza Francesco <leanzafrancesco@gmail.com>
		 */
		public function woocommerce_order_status_changed( $order_id, $old_status, $new_status ) {
			$order = new WC_Order( $order_id );

			$custom_status = get_posts( array(
											'posts_per_page' => 1,
											'post_type'      => 'yith-wccos-ostatus',
											'post_status'    => 'publish',
											'meta_key'       => 'slug',
											'meta_value'     => $new_status,
											'fields'         => 'ids',
										) );


			if ( ! ! $custom_status ) {
				$status_id           = current( $custom_status );
				$recipients          = yith_wccos_get_recipients( $status_id );
				$downloads_permitted = yith_plugin_fw_is_true( get_post_meta( $status_id, 'downloads-permitted', true ) );
				$custom_recipient    = get_post_meta( $status_id, 'custom_recipient', true );
				$restore_stock       = yith_plugin_fw_is_true( get_post_meta( $status_id, 'restore-stock', true ) );
				if ( $downloads_permitted ) {
					wc_downloadable_product_permissions( $order_id );
				}
				if ( $restore_stock ) {
					$this->restore_order_stock( $order );
				}

				$mailer           = WC()->mailer();
				$email_recipients = array();

				foreach ( $recipients as $recipient ) {
					switch ( $recipient ) {
						case 'admin':
							$email_recipients[ get_option( 'admin_email' ) ] = true;
							break;
						case 'customer':
							$email_recipients[ $order->get_billing_email() ] = false;
							break;

						case 'custom-email':
							if ( $custom_recipient ) {
								$email_recipients[ $custom_recipient ] = apply_filters( 'yith_wcos_sent_to_admin_for_custom_recipient', false, $custom_recipient, $custom_status );
							}
							break;
						default:
							// to allow plugins to add their own recipients
							$extra_recipients = apply_filters( 'yith_wccos_custom_email_recipients', null, $recipients, $status_id, $order_id, $old_status, $new_status );
							if ( ! is_null( $extra_recipients ) && is_array( $extra_recipients ) ) {
								$email_recipients = array_merge( $extra_recipients, $email_recipients );
							}
					}
				}

				$email_recipients = apply_filters( 'yith_wccos_email_recipients', $email_recipients, $status_id, $order_id, $old_status, $new_status );

				if ( ! ! $email_recipients ) {

					$notification_args = array(
						'heading'              => get_post_meta( $status_id, 'mail_heading', true ),
						'subject'              => get_post_meta( $status_id, 'mail_subject', true ),
						'from_name'            => get_post_meta( $status_id, 'mail_name_from', true ),
						'from_email'           => get_post_meta( $status_id, 'mail_from', true ),
						'display_order_info'   => yith_plugin_fw_is_true( get_post_meta( $status_id, 'mail_order_info', true ) ),
						'custom_email_address' => $custom_recipient,
						'order'                => $order,
						'custom_message'       => get_post_meta( $status_id, 'mail_custom_message', true ),
					);

					foreach ( $email_recipients as $recipient => $sent_to_admin ) {
						$notification_args['recipient']     = $recipient;
						$notification_args['sent_to_admin'] = $sent_to_admin;
						do_action( 'yith_wccos_custom_order_status_notification', $notification_args );
					}
				}
			}
		}

		/**
		 * Restore stock levels for all line items in the order.
		 *
		 * @param WC_Order $order
		 *
		 * @since 1.0.21
		 */
		public function restore_order_stock( $order ) {
			if ( 'yes' === get_option( 'woocommerce_manage_stock' ) && apply_filters( 'woocommerce_can_reduce_order_stock', true, $this ) && sizeof( $order->get_items() ) > 0 ) {
				$order_id            = $order->get_id();
				$order_stock_reduced = get_post_meta( $order_id, '_order_stock_reduced', true );

				if ( in_array( $order_stock_reduced, array( '1', 'yes' ) ) ) {
					foreach ( $order->get_items() as $item ) {
						if ( $item['product_id'] > 0 ) {
							/** @var WC_Product $product */
							$product = is_callable( array( $item, 'get_product' ) ) ? $item->get_product() : $order->get_product_from_item( $item );

							if ( $product && $product->exists() && $product->managing_stock() ) {
								$qty       = apply_filters( 'woocommerce_order_item_quantity', $item['qty'], $this, $item );
								$new_stock = $product instanceof WC_Data ? wc_update_product_stock( $product, $qty, 'increase' ) : $product->increase_stock( $qty );
								$item_name = $product->get_sku() ? $product->get_sku() : $item['product_id'];

								if ( isset( $item['variation_id'] ) && $item['variation_id'] ) {
									$order->add_order_note( sprintf( __( 'Item %1$s variation #%2$s stock increased from %3$s to %4$s.', 'yith-woocommerce-custom-order-status' ), $item_name, $item['variation_id'], $new_stock - $qty, $new_stock ) );
								} else {
									$order->add_order_note( sprintf( __( 'Item %1$s stock increased from %2$s to %3$s.', 'yith-woocommerce-custom-order-status' ), $item_name, $new_stock - $qty, $new_stock ) );
								}
							}
						}
					}
					delete_post_meta( $order_id, '_order_stock_reduced' );

					do_action( 'yith_wccos_restore_order_stock', $order );
				}
			}
		}


		public function metabox_premium( $tabs ) {

			$statuses = wc_get_order_statuses();

			$premium_fields = array(
				'status_type' => array(
					'label'   => __( 'Status Type', 'yith-woocommerce-custom-order-status' ),
					'desc'    => __( 'Select a type for your status.', 'yith-woocommerce-custom-order-status' ),
					'type'    => 'select',
					'options' => array(
						'custom'     => _x( 'Custom Status', 'Select status type', 'yith-woocommerce-custom-order-status' ),
						'pending'    => _x( 'Pending Payment', 'Select status type', 'yith-woocommerce-custom-order-status' ),
						'processing' => _x( 'Processing', 'Select status type', 'yith-woocommerce-custom-order-status' ),
						'on-hold'    => _x( 'On Hold', 'Select status type', 'yith-woocommerce-custom-order-status' ),
						'completed'  => _x( 'Completed', 'Select status type', 'yith-woocommerce-custom-order-status' ),
						'cancelled'  => _x( 'Cancelled', 'Select status type', 'yith-woocommerce-custom-order-status' ),
						'refunded'   => _x( 'Refunded', 'Select status type', 'yith-woocommerce-custom-order-status' ),
						'failed'     => _x( 'Failed', 'Select status type', 'yith-woocommerce-custom-order-status' ),
					),
					'private' => false,
					'std'     => 'custom',
				),
			);

			$tabs['settings']['fields'] = array_merge( $premium_fields, $tabs['settings']['fields'] );

			$tabs['settings']['fields']['icon-type'] = array(
				'label'   => __( 'Icon', 'yith-woocommerce-custom-order-status' ),
				'desc'    => __( 'Icon of your status', 'yith-woocommerce-custom-order-status' ),
				'name'    => 'yit_metaboxes[icon][select]',
				'type'    => 'select',
				'private' => false,
				'options' => array(
					'none' => __( 'Default', 'yith-woocommerce-custom-order-status' ),
					'icon' => __( 'Icon', 'yith-woocommerce-custom-order-status' ),
				),
				'std'     => 'none',
			);

			$tabs['settings']['fields']['icon-icon'] = array(
				'label'   => __( 'Choose Icon', 'yith-woocommerce-custom-order-status' ),
				'name'    => 'yit_metaboxes[icon][icon]',
				'type'    => 'icons',
				'private' => false,
				'std'     => 'FontAwesome:genderless',
				'deps'    => array(
					'id'    => 'icon-type',
					'value' => 'icon',
				),
			);

			$tabs['settings']['fields']['graphicstyle'] = array(
				'label'   => __( 'Graphic Style', 'yith-woocommerce-custom-order-status' ),
				'desc'    => __( 'Style of your status button and indicator', 'yith-woocommerce-custom-order-status' ),
				'type'    => 'select',
				'options' => array(
					'icon' => __( 'Icon', 'yith-woocommerce-custom-order-status' ),
					'text' => __( 'Text', 'yith-woocommerce-custom-order-status' ),
				),
				'private' => false,

			);

			$tabs['settings']['fields']['nextactions'] = array(
				'label'    => __( 'Next Actions', 'yith-woocommerce-custom-order-status' ),
				'desc'     => __( 'Select statuses that will be enabled by this status', 'yith-woocommerce-custom-order-status' ),
				'type'     => 'select',
				'class'    => 'wc-enhanced-select',
				'options'  => $statuses,
				'std'      => array(
					'wc-completed',
				),
				'multiple' => true,
				'private'  => false,

			);

			$tabs['settings']['fields']['can-cancel'] = array(
				'label'   => __( 'User can cancel', 'yith-woocommerce-custom-order-status' ),
				'desc'    => __( 'Choose whether the customer can cancel orders when this status is applied or not', 'yith-woocommerce-custom-order-status' ),
				'type'    => 'onoff',
				'private' => false,
			);

			$tabs['settings']['fields']['can-pay'] = array(
				'label'   => __( 'User can pay', 'yith-woocommerce-custom-order-status' ),
				'desc'    => __( 'Choose whether the customer can pay orders when this status is applied or not', 'yith-woocommerce-custom-order-status' ),
				'type'    => 'onoff',
				'private' => false,
			);

			$tabs['settings']['fields']['is-paid'] = array(
				'label'   => __( 'Order is paid', 'yith-woocommerce-custom-order-status' ),
				'desc'    => __( 'Choose whether the order is considered paid or not', 'yith-woocommerce-custom-order-status' ),
				'type'    => 'onoff',
				'private' => false,
				'deps'    => array(
					'id'    => 'status_type',
					'value' => 'custom',
				),
			);

			$tabs['settings']['fields']['downloads-permitted'] = array(
				'label'   => __( 'Allow Downloads', 'yith-woocommerce-custom-order-status' ),
				'desc'    => __( 'Choose whether you want to allow downloads when this status is applied or not', 'yith-woocommerce-custom-order-status' ),
				'type'    => 'onoff',
				'private' => false,
			);

			$tabs['settings']['fields']['display-in-reports'] = array(
				'label'   => __( 'Display in Reports', 'yith-woocommerce-custom-order-status' ),
				'desc'    => __( 'Choose whether you want to include orders marked with this status in Reports', 'yith-woocommerce-custom-order-status' ),
				'type'    => 'onoff',
				'private' => false,
			);

			$tabs['settings']['fields']['restore-stock'] = array(
				'label'   => __( 'Restore Stock', 'yith-woocommerce-custom-order-status' ),
				'desc'    => __( 'Choose whether you want to restore stock quantities or not when this status is applied', 'yith-woocommerce-custom-order-status' ),
				'type'    => 'onoff',
				'private' => false,
			);

			$tabs['settings']['fields']['show-in-actions'] = array(
				'label'   => __( 'Show always in Actions', 'yith-woocommerce-custom-order-status' ),
				'desc'    => __( 'Choose whether you want to always show this status in WooCommerce Order Actions', 'yith-woocommerce-custom-order-status' ),
				'type'    => 'onoff',
				'private' => false,
			);

			$tabs['settings']['fields']['mail-settings-info'] = array(
				'label'   => __( 'Email Settings', 'yith-woocommerce-custom-order-status' ),
				'desc'    => __( 'To set emails for WooCommerce default status, use WooCommerce Panel in ', 'yith-woocommerce-custom-order-status' ) . '<a href="admin.php?page=wc-settings&tab=email">' . __( 'WooCommerce -> Settings -> Emails', 'yith-woocommerce-custom-order-status' ) . '</a>',
				'type'    => 'simple-text',
				'private' => false,
			);


			$tabs['mail_settings'] = array( //tab
											'label'  => __( 'Email Settings', 'yith-woocommerce-custom-order-status' ),
											'fields' => array(
												'recipients'          => array(
													'label'    => __( 'Recipients', 'yith-woocommerce-custom-order-status' ),
													'desc'     => __( 'Choose recipients of email notifications for this status', 'yith-woocommerce-custom-order-status' ),
													'type'     => 'select',
													'class'    => 'wc-enhanced-select',
													'multiple' => true,
													'options'  => yith_wccos_get_allowed_recipients(),
													'private'  => false,
												),
												'custom_recipient'    => array(
													'label'   => __( 'Recipient Email Address', 'yith-woocommerce-custom-order-status' ),
													'desc'    => __( 'Type here the email address to notify when the selected status is selected', 'yith-woocommerce-custom-order-status' ),
													'type'    => 'text',
													'private' => false,
													'std'     => '',
												),
												'mail_name_from'      => array(
													'label'   => __( '"From" Name', 'yith-woocommerce-custom-order-status' ),
													'desc'    => __( 'Enter the email sender name which will appear to recipients', 'yith-woocommerce-custom-order-status' ),
													'type'    => 'text',
													'private' => false,
													'std'     => get_bloginfo( 'name' ),
												),
												'mail_from'           => array(
													'label'   => __( '"From" Email Address', 'yith-woocommerce-custom-order-status' ),
													'desc'    => __( 'Enter the email address which will appear to recipients', 'yith-woocommerce-custom-order-status' ),
													'type'    => 'text',
													'private' => false,
													'std'     => get_option( 'admin_email' ),
												),
												'mail_subject'        => array(
													'label'   => __( 'Email Subject', 'yith-woocommerce-custom-order-status' ),
													'desc'    => __( 'Enter the email subject which will appear to recipients of the email', 'yith-woocommerce-custom-order-status' ),
													'type'    => 'text',
													'private' => false,
													'std'     => '',
												),
												'mail_heading'        => array(
													'label'   => __( 'Email Heading', 'yith-woocommerce-custom-order-status' ),
													'desc'    => __( 'Enter the heading you want to appear in the email sent', 'yith-woocommerce-custom-order-status' ),
													'type'    => 'text',
													'private' => false,
													'std'     => '',
												),
												'mail_custom_message' => array(
													'label'   => __( 'Custom Message', 'yith-woocommerce-custom-order-status' ),
													'desc'    => __( 'Available Shortcodes: {customer_first_name} , {customer_last_name} , {order_date} , {order_number} , {order_value} , {billing_address} , {shipping_address}', 'yith-woocommerce-custom-order-status' ),
													'type'    => 'textarea',
													'private' => false,
													'std'     => '',
												),
												'mail_order_info'     => array(
													'label'   => __( 'Include Order Information', 'yith-woocommerce-custom-order-status' ),
													'desc'    => __( 'Select whether you want to include order information (billing and shipping address, order items, total, etc)', 'yith-woocommerce-custom-order-status' ),
													'type'    => 'onoff',
													'private' => false,
													'std'     => '',
												),

											),
			);

			return $tabs;
		}

		/**
		 * Add Button Actions in Order list
		 *
		 * @param array    $actions
		 * @param WC_Order $the_order
		 *
		 * @return array
		 * @since    1.0
		 * @author   Leanza Francesco <leanzafrancesco@gmail.com>
		 */
		function add_submit_to_order_admin_actions( $actions, $the_order ) {
			global $post;
			$status_posts = get_posts( array(
										   'posts_per_page' => - 1,
										   'post_type'      => 'yith-wccos-ostatus',
										   'post_status'    => 'publish',
										   'fields'         => 'ids',
									   ) );

			$status_slugs    = array();
			$statuses_titles = array();

			foreach ( $status_posts as $sp_id ) {
				$slug                   = get_post_meta( $sp_id, 'slug', true );
				$status_slugs[]         = $slug;
				$status_titles[ $slug ] = get_the_title( $sp_id );
			}


			// Add all status to on-hold status if 'on-hold' is not customized
			if ( apply_filters( 'yith_wccos_add_all_custom_order_status_actions', ! in_array( 'on-hold', $status_slugs ) && $the_order->has_status( 'on-hold' ), $the_order ) ) {
				foreach ( $status_posts as $sp_id ) {
					$current_status = array(
						'label' => get_the_title( $sp_id ),
						'slug'  => get_post_meta( $sp_id, 'slug', true ),
					);
					$action         = $current_status['slug'];
					if ( $action == 'completed' ) {
						$actions['complete'] = array(
							'url'    => wp_nonce_url( admin_url( 'admin-ajax.php?action=woocommerce_mark_order_status&status=completed&order_id=' . $post->ID ), 'woocommerce-mark-order-status' ),
							'name'   => __( 'Complete', 'woocommerce' ),
							'action' => "complete",
						);
					} else {
						$actions[ $action ] = array(
							'url'    => wp_nonce_url( admin_url( 'admin-ajax.php?action=woocommerce_mark_order_status&status=' . $action . '&order_id=' . $post->ID ), 'woocommerce-mark-order-status' ),
							'name'   => $current_status['label'],
							'action' => $action,
						);
					}
				}
			} else {
				$order_status    = $the_order->get_status();
				$custom_statuses = get_posts( array(
												  'posts_per_page' => 1,
												  'post_type'      => 'yith-wccos-ostatus',
												  'post_status'    => 'publish',
												  'fields'         => 'ids',
												  'meta_query'     => array(
													  array(
														  'key'   => 'slug',
														  'value' => $order_status,
													  ),
												  ),
											  ) );

				$status_to_show_always = get_posts( array(
														'posts_per_page' => - 1,
														'post_type'      => 'yith-wccos-ostatus',
														'post_status'    => 'publish',
														'fields'         => 'ids',
														'meta_query'     => array(
															'relation' => 'OR',
															array(
																'key'   => 'show-in-actions',
																'value' => '1',
															),
															array(
																'key'   => 'show-in-actions',
																'value' => 'yes',
															),
														),
													) );

				$next_actions = array();
				if ( $custom_statuses ) {
					// Customized Status
					$custom_status_id = current( $custom_statuses );
					$next_actions     = get_post_meta( $custom_status_id, 'nextactions', true );
					$next_actions     = ! ! $next_actions && is_array( $next_actions ) ? $next_actions : array();

					unset( $actions['complete'] );
					unset( $actions['processing'] );
				}

				if ( ! ! $status_to_show_always ) {
					foreach ( $status_to_show_always as $status_id ) {
						$next_actions[] = 'wc-' . get_post_meta( $status_id, 'slug', true );
					}
				}
				$next_actions = array_unique( $next_actions );

				foreach ( $next_actions as $action ) {
					if ( ! wc_is_order_status( $action ) ) {
						continue;
					}
					$action = str_replace( "wc-", "", $action );
					if ( $action == 'completed' ) {
						$actions['complete'] = array(
							'url'    => wp_nonce_url( admin_url( 'admin-ajax.php?action=woocommerce_mark_order_status&status=completed&order_id=' . $post->ID ), 'woocommerce-mark-order-status' ),
							'name'   => __( 'Complete', 'woocommerce' ),
							'action' => "complete",
						);
					} else {
						$actions[ $action ] = array(
							'url'    => wp_nonce_url( admin_url( 'admin-ajax.php?action=woocommerce_mark_order_status&status=' . $action . '&order_id=' . $post->ID ), 'woocommerce-mark-order-status' ),
							'name'   => isset( $status_titles[ $action ] ) ? $status_titles[ $action ] : $action,
							'action' => $action,
						);
					}
				}
			}

			return $actions;
		}

		public function admin_enqueue_scripts() {
			parent::admin_enqueue_scripts();

			$screen = get_current_screen();
			if ( 'edit-shop_order' == $screen->id ) {
				wp_enqueue_script( 'yith_wccos_order_bulk_actions', YITH_WCCOS_ASSETS_URL . '/js/order_bulk_actions.js', array( 'jquery' ), YITH_WCCOS_VERSION, true );
				$status_ids = get_posts( array(
											 'posts_per_page' => - 1,
											 'post_type'      => 'yith-wccos-ostatus',
											 'post_status'    => 'publish',
											 'fields'         => 'ids',
										 ) );

				$wc_status = array( 'pending', 'processing', 'on-hold', 'completed', 'cancelled', 'refunded', 'failed' );

				$my_custom_status = array();

				foreach ( $status_ids as $status_id ) {
					$slug                      = get_post_meta( $status_id, 'slug', true );
					$label                     = get_the_title( $status_id );
					$my_custom_status[ $slug ] = $label;
				}
				$mark_text = __( "Mark", "woocommerce_status_actions" );

				wp_localize_script( 'yith_wccos_order_bulk_actions', 'localized_obj', array( 'my_custom_status' => $my_custom_status, 'mark_text' => $mark_text ) );
			}
		}

		public function get_status_inline_css() {
			$css        = '';
			$status_ids = get_posts( array(
										 'posts_per_page' => - 1,
										 'post_type'      => 'yith-wccos-ostatus',
										 'post_status'    => 'publish',
										 'fields'         => 'ids',
									 ) );

			foreach ( $status_ids as $status_id ) {
				$name = get_post_meta( $status_id, 'slug', true );
				$meta = array(
					'label'        => get_the_title( $status_id ),
					'color'        => get_post_meta( $status_id, 'color', true ),
					'icon'         => get_post_meta( $status_id, 'icon', true ),
					'graphicstyle' => get_post_meta( $status_id, 'graphicstyle', true ),
				);

				if ( ! is_array( $meta['icon'] ) ) {
					$meta['icon'] = array();
				}

				$my_icon                = isset( $meta['icon']['icon'] ) ? $meta['icon']['icon'] : 'FontAwesome:genderless';
				$meta['icon']['select'] = isset( $meta['icon']['select'] ) ? $meta['icon']['select'] : 'none';

				$icon_data = explode( ':', $my_icon, 2 );
				if ( count( $icon_data ) === 2 ) {
					$font_name = $icon_data[0];
					$icon_name = $icon_data[1];
				} else {
					$font_name = 'FontAwesome';
					$icon_name = 'genderless';
				}


				$icons     = YIT_Icons()->get_icons();
				$icon_key  = array_key_exists( $font_name, $icons ) ? array_search( $icon_name, $icons[ $font_name ] ) : '';
				$icon_data = array(
					'icon' => $icon_key,
					'font' => $font_name,
				);

				$no_icon = ( $meta['icon']['select'] == 'none' ) ? true : false;

				if ( $meta['graphicstyle'] == 'text' ) {
					$icon_data['icon'] = $meta['label'];
					$icon_data['font'] = 'inherit';

					if ( version_compare( WC()->version, '3.3', '>=' ) ) {
						$text_color = yith_wccos_is_light_color( $meta['color'] ) ? 'rgba(0,0,0,0.7)' : 'rgba(255,255,255,0.7)';
					} else {
						$text_color = '#ffffff';
					}
					$css .= '.post-type-yith-wccos-ostatus .widefat .column-order_status mark.' . $name . '::after{
                                content:"' . $icon_data['icon'] . '" !important;
                                color: ' . $text_color . ' !important;
                                background:' . $meta['color'] . ' !important;
                                font-family: ' . $icon_data['font'] . ' !important;
                                font-variant: normal !important;
                                text-transform: none !important;
                                line-height: 1 !important;
                                margin: 0px !important;
                                text-indent: 0px !important;
                                position: absolute !important;
                                top: 0px !important;
                                left: calc(50% - 35px) !important;
                                width: 70px !important;
                                text-align: center !important;
                                font-size:9px !important;
                                padding: 5px 3px !important;
                                box-sizing: border-box !important;
                                border-radius: 3px !important;
                                font-weight: 600;
                            }';

					if ( version_compare( WC()->version, '3.3', '>=' ) ) {
						$bg_color   = $meta['color'];
						$text_color = yith_wccos_is_light_color( $bg_color ) ? 'rgba(0,0,0,0.7)' : 'rgba(255,255,255,0.7)';

						$css .= 'mark.order-status.status-' . $name . '{
                                    background:' . $bg_color . ' !important;
                                    color: ' . $text_color . ' !important;
                        }';

						$css .= '.post-type-shop_order .wp-list-table .column-wc_actions a.wc-action-button-' . $name . '{
                                    color: ' . $bg_color . ' !important;
                                    text-indent:0;
                                    width: auto !important;
                                    padding: 0 8px !important;
                        }';


						// Multi Vendor Suborder text
						$css .= '.post-type-shop_order .wp-list-table .column-suborder mark.' . $name . '{
                                    background:' . $bg_color . ' !important;
                                    color: ' . $text_color . ' !important;
                                    text-indent:0;
                                    width: auto !important;
                                    padding: 3px 6px !important;
                                    height: auto !important;
                                    line-height: 1 !important;
                                    font-size: 11px !important;
                                    border-radius: 3px !important;
                        }';
					}

					if ( $name == 'completed' ) {
						$name = 'complete';
					}

					$css .= ".order_actions .$name, .wc_actions .$name" . '{
                                display: block;
                                padding: 0px 7px !important;
                                color:' . $meta['color'] . ' !important;
                            }';

					$css .= ".order_actions .$name::after, .wc_actions .$name::after" . '{
                                color:' . $meta['color'] . ' !important;
                            }';
				} else {
					$wc_status = array(
						'pending',
						'processing',
						'on-hold',
						'completed',
						'cancelled',
						'refunded',
						'failed',
					);

					if ( version_compare( WC()->version, '3.3', '>=' ) ) {
						$bg_color   = $meta['color'];
						$text_color = yith_wccos_is_light_color( $bg_color ) ? 'rgba(0,0,0,0.7)' : 'rgba(255,255,255,0.7)';

						$css .= 'mark.order-status.status-' . $name . '{
                                background:' . $bg_color . ' !important;
                                color: ' . $text_color . ' !important;
                        }';
					}

					if ( $no_icon && in_array( $name, $wc_status ) ) {
						$css .= '.widefat .column-order_status mark.' . $name . '::after, .yith_status_icon mark.' . $name . '::after, mark.' . $name . '::after{
		                                color:' . $meta['color'] . ' !important;
		                            }';
						if ( $name == 'completed' ) {
							$name = 'complete';
						}

						$css .= ".order_actions .$name::after, .wc_actions .$name::after" . '{
		                                color: ' . $meta['color'] . ';
		                            }';
					} else {
						// 'column-suborder' for Multi Vendor suborder icons
						$css .= '.post-type-yith-wccos-ostatus .widefat .column-order_status mark.' . $name . '::after,
                                 .post-type-shop_order .wp-list-table .column-suborder mark.' . $name . '::after{
		                               content:"' . $icon_data['icon'] . '" !important;
		                               color:' . $meta['color'] . ' !important;
		                               font-family: ' . $icon_data['font'] . ' !important;
		                               font-weight: 400;
		                               font-variant: normal;
		                               text-transform: none;
		                               line-height: 1;
		                               margin: 0px;
		                               text-indent: 0px;
		                               position: absolute;
		                               top: 0px;
		                               left: 0px;
		                               width: 100%;
		                               height: 100%;
		                               text-align: center;
		                           }';


						if ( $name == 'completed' ) {
							$name = 'complete';
						}

						$css .= ".order_actions .$name, .wc_actions .$name" . '{
		                               display: block;
		                               text-indent: -9999px;
		                               position: relative;
		                               padding: 0px !important;
		                               height: 2em !important;
		                               width: 2em;
		                           }';

						$css .= ".order_actions .$name::after, .wc_actions .$name::after" . '{
		                              	content:"' . $icon_data['icon'] . '" !important;
		                               color: ' . $meta['color'] . ';
		                               font-family: ' . $icon_data['font'] . ' !important;
		                               text-indent: 0px;
		                               position: absolute;
		                               width: 100%;
		                               height: 100%;
		                               font-weight: 400;
		                               text-align: center;
		                               margin: 0px;
		                               font-variant: normal;
		                               text-transform: none;
		                               top: 0px;
		                               left: 0px;
		                               line-height: 1.85;
		                           }';
					}
				}
			}

			return $css;
		}

		/**
		 * Register plugins for activation tab
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function register_plugin_for_activation() {
			if ( function_exists( 'YIT_Plugin_Licence' ) ) {
				YIT_Plugin_Licence()->register( YITH_WCCOS_INIT, YITH_WCCOS_SECRET_KEY, YITH_WCCOS_SLUG );
			}
		}

		/**
		 * Register plugins for update tab
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function register_plugin_for_updates() {
			if ( function_exists( 'YIT_Upgrade' ) ) {
				YIT_Upgrade()->register( YITH_WCCOS_SLUG, YITH_WCCOS_INIT );
			}
		}
	}
}

/**
 * Unique access to instance of YITH_WCCOS_Admin_Premium class
 *
 * @return YITH_WCCOS_Admin_Premium
 * @deprecated since 1.1.0 use YITH_WCCOS_Admin() instead
 * @since      1.0.0
 */
function YITH_WCCOS_Admin_Premium() {
	return YITH_WCCOS_Admin();
}