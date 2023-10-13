<?php
/**
 * Class YITH_WCBK_Product_Post_Type_Admin
 * handle the Booking product post type in Admin
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking\Classes
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! class_exists( 'YITH_WCBK_Product_Post_Type_Admin' ) ) {
	/**
	 * Class YITH_WCBK_Product_Post_Type_Admin
	 */
	class YITH_WCBK_Product_Post_Type_Admin {
		use YITH_WCBK_Singleton_Trait;

		/**
		 * Booking product type
		 *
		 * @var string
		 * @static
		 */
		public static $prod_type = 'booking';

		/**
		 * Product meta array.
		 *
		 * @var array product meta array
		 * @deprecated 3.0.0
		 */
		public $product_meta_array = array();

		/**
		 * YITH_WCBK_Product_Post_Type_Admin constructor.
		 */
		protected function __construct() {

			// Add Booking product to WC product type selector.
			add_filter( 'product_type_selector', array( $this, 'product_type_selector' ) );
			add_filter( 'product_type_options', array( $this, 'product_type_options' ) );

			// Add tabs for product booking.
			add_filter( 'woocommerce_product_data_tabs', array( $this, 'product_booking_tabs' ), 99 ); // Use high priority since we need to filter all tabs, also the ones added by other plugins.
			add_action( 'woocommerce_product_data_panels', array( $this, 'add_product_data_panels' ) );

			// Save product meta.
			add_action( 'woocommerce_admin_process_product_object', array( $this, 'set_product_meta_before_saving' ) );
			add_action( 'woocommerce_process_product_meta_booking', array( $this, 'regenerate_product_data_after_saving' ) );

			// Remove Booking Services Metabox for products.
			add_action( 'add_meta_boxes', array( $this, 'manage_meta_boxes' ) );

			// Export action.
			add_filter( 'post_row_actions', array( $this, 'customize_booking_product_row_actions' ), 10, 2 );
		}

		/**
		 * Customize Booking Product Actions
		 *
		 * @param array   $actions An array of row action links. Defaults are
		 *                         'Edit', 'Quick Edit', 'Restore, 'Trash',
		 *                         'Delete Permanently', 'Preview', and 'View'.
		 * @param WP_Post $post    The post object.
		 *
		 * @return array
		 * @since       2.0.0
		 */
		public function customize_booking_product_row_actions( $actions, $post ) {
			global $the_product;

			if ( 'product' !== $post->post_type ) {
				return $actions;
			}

			if ( empty( $the_product ) || $the_product->get_id() !== $post->ID ) {
				$the_product = wc_get_product( $post );
			}

			if ( yith_wcbk_is_booking_product( $the_product ) ) {
				/**
				 * Booking product.
				 *
				 * @var WC_Product_Booking $the_product
				 */

				$export_future_url = wp_nonce_url(
					add_query_arg(
						array(
							'yith_wcbk_exporter_action' => 'export_future_ics',
							'product_id'                => $the_product->get_id(),
						)
					),
					'export-future-ics',
					'security'
				);

				$booking_actions = array(
					'yith_wcbk_export_future_ics' => array(
						'label' => __( 'Export Future ICS', 'yith-booking-for-woocommerce' ),
						'url'   => $export_future_url,
					),
					'yith_wcbk_view_calendar'     => array(
						'label' => __( 'Booking Calendar', 'yith-booking-for-woocommerce' ),
						'url'   => $the_product->get_admin_calendar_url(),
					),
				);

				foreach ( $booking_actions as $key => $action ) {
					$actions[ $key ] = "<a href='{$action['url']}'>{$action['label']}</a>";
				}
			}

			return $actions;
		}

		/**
		 * Remove Booking Services Metabox for products
		 *
		 * @param string $post_type Post type.
		 */
		public function manage_meta_boxes( $post_type ) {
			remove_meta_box( YITH_WCBK_Post_Types::SERVICE_TAX . 'div', 'product', 'side' );
		}

		/**
		 * Add data panels to products
		 */
		public function add_product_data_panels() {
			/**
			 * Product object.
			 *
			 * @var WC_Product $product_object
			 */
			global $post, $product_object;

			$tabs = array(
				'settings'     => 'yith_booking_settings_tab',
				'costs'        => 'yith_booking_costs_tab',
				'availability' => 'yith_booking_availability_tab',
			);

			$post_id         = $post->ID;
			$prod_type       = self::$prod_type;
			$booking_product = $product_object->is_type( self::$prod_type ) ? $product_object : false;

			foreach ( $tabs as $key => $tab_id ) {
				echo '<div id="' . esc_attr( $tab_id ) . '" class="panel woocommerce_options_panel">';
				yith_wcbk_get_view( 'product-tabs/html-' . $key . '-tab.php', compact( 'post_id', 'prod_type', 'booking_product', 'product_object', 'post' ) );
				echo '</div>';
			}
		}

		/**
		 * Add tabs for booking products
		 *
		 * @param array $tabs Tabs.
		 *
		 * @return array
		 */
		public function product_booking_tabs( $tabs ) {
			$main_priority = 11;

			$tabs = array_map(
				function ( $tab ) use ( $main_priority ) {
					$tab['priority'] = $tab['priority'] ?? 999; // Fix missing priorities, to prevent issues when WooCommerce will sort the items.
					$tab['class']    = (array) $tab['class'] ?? array();

					// Prevent issues if some other plugin has priority reserved by Booking tabs.
					if ( $tab['priority'] >= $main_priority && $tab['priority'] < ( $main_priority + 1 ) && ! in_array( 'yith-wcbk-product-sub-tab', $tab['class'], true ) ) {
						$tab['priority'] = $tab['priority'] + 1;
					}

					return $tab;
				},
				$tabs
			);

			$booking_tabs = array(
				'yith_booking_options' => array(
					'label'    => _x( 'Booking Options', 'Product tab title', 'yith-booking-for-woocommerce' ),
					'target'   => 'yith_booking_settings_tab',
					'class'    => array( 'show_if_' . self::$prod_type ),
					'priority' => $main_priority,
				),
			);

			$sub_tabs = array(
				'yith_booking_settings'     => array(
					'label'    => _x( 'Settings', 'Product tab title', 'yith-booking-for-woocommerce' ),
					'target'   => 'yith_booking_settings_tab',
					'priority' => 10,
				),
				'yith_booking_costs'        => array(
					'label'    => _x( 'Costs', 'Product tab title', 'yith-booking-for-woocommerce' ),
					'target'   => 'yith_booking_costs_tab',
					'priority' => 30,
				),
				'yith_booking_availability' => array(
					'label'    => _x( 'Availability', 'Product tab title', 'yith-booking-for-woocommerce' ),
					'target'   => 'yith_booking_availability_tab',
					'priority' => 40,
				),
			);

			/**
			 * Allow filtering booking product sub-tabs to be shown in an "indented" layout.
			 *
			 * @see   YITH_WCBK_Product_Data_Extension::filter_product_sub_tabs
			 * @since 4.0
			 */
			$sub_tabs     = apply_filters( 'yith_wcbk_booking_product_sub_tabs', $sub_tabs );
			$max_priority = max( array_column( $sub_tabs, 'priority' ) );
			$max_priority = max( $max_priority, 100 ); // By default, the max priority is 100.

			foreach ( $sub_tabs as $key => $sub_tab ) {
				$label    = $sub_tab['label'] ?? '';
				$target   = $sub_tab['target'] ?? '';
				$class    = $sub_tab['class'] ?? array();
				$priority = $sub_tab['priority'] ?? 99;

				$priority = $main_priority + ( $priority / $max_priority );
				$class    = array_merge( (array) $class, array( 'yith-wcbk-product-sub-tab', 'show_if_' . self::$prod_type ) );

				$booking_tabs[ $key ] = array(
					'label'    => $label,
					'target'   => $target,
					'class'    => $class,
					'priority' => $priority,
				);
			}

			$tabs = array_merge( $tabs, $booking_tabs );

			yith_wcbk_deprecated_filter( 'yith_wcbk_product_booking_tabs', '4.0.0', 'woocommerce_product_data_tabs', 'You can use the standard WooCommerce filter with an high priority' );
			$tabs = apply_filters( 'yith_wcbk_product_booking_tabs', $tabs );

			return $tabs;
		}

		/**
		 * Add Booking Product type in product type selector
		 *
		 * @param array $types Product types.
		 *
		 * @return array
		 */
		public function product_type_selector( $types ) {
			$types[ self::$prod_type ] = _x( 'Bookable Product', 'Admin: type of product', 'yith-booking-for-woocommerce' );

			return $types;
		}

		/**
		 * Show "virtual" checkbox for Booking products
		 *
		 * @param array $options Options.
		 *
		 * @return array
		 * @since 2.0.3
		 */
		public function product_type_options( $options ) {
			$options['virtual']['wrapper_class'] .= ' show_if_' . self::$prod_type;

			return $options;
		}


		/**
		 * Set the product meta before saving the product
		 *
		 * @param WC_Product|WC_Product_Booking $product The product.
		 */
		public function set_product_meta_before_saving( $product ) {
			// phpcs:disable WordPress.Security.NonceVerification.Missing, WordPress.Arrays.MultipleStatementAlignment
			if ( $product->is_type( self::$prod_type ) ) {

				$props = array(
					'duration_type'                     => isset( $_POST['_yith_booking_duration_type'] ) ? wc_clean( wp_unslash( $_POST['_yith_booking_duration_type'] ) ) : null,
					'duration'                          => isset( $_POST['_yith_booking_duration'] ) ? absint( $_POST['_yith_booking_duration'] ) : null,
					'duration_unit'                     => isset( $_POST['_yith_booking_duration_unit'] ) ? wc_clean( wp_unslash( $_POST['_yith_booking_duration_unit'] ) ) : null,
					'enable_calendar_range_picker'      => isset( $_POST['_yith_booking_enable_calendar_range_picker'] ),
					'default_start_date'                => isset( $_POST['_yith_booking_default_start_date'] ) ? wc_clean( wp_unslash( $_POST['_yith_booking_default_start_date'] ) ) : null,
					'default_start_date_custom'         => isset( $_POST['_yith_booking_default_start_date_custom'] ) ? wc_clean( wp_unslash( $_POST['_yith_booking_default_start_date_custom'] ) ) : null,
					'default_start_time'                => isset( $_POST['_yith_booking_default_start_time'] ) ? wc_clean( wp_unslash( $_POST['_yith_booking_default_start_time'] ) ) : null,
					'full_day'                          => isset( $_POST['_yith_booking_all_day'] ),
					'max_bookings_per_unit'             => isset( $_POST['_yith_booking_max_per_block'] ) ? wc_clean( wp_unslash( $_POST['_yith_booking_max_per_block'] ) ) : null,
					'minimum_duration'                  => isset( $_POST['_yith_booking_minimum_duration'] ) ? wc_clean( wp_unslash( $_POST['_yith_booking_minimum_duration'] ) ) : null,
					'maximum_duration'                  => isset( $_POST['_yith_booking_maximum_duration'] ) ? wc_clean( wp_unslash( $_POST['_yith_booking_maximum_duration'] ) ) : null,
					'confirmation_required'             => isset( $_POST['_yith_booking_request_confirmation'] ),
					'cancellation_available'            => isset( $_POST['_yith_booking_can_be_cancelled'] ),
					'cancellation_available_up_to'      => isset( $_POST['_yith_booking_cancelled_duration'] ) ? wc_clean( wp_unslash( $_POST['_yith_booking_cancelled_duration'] ) ) : null,
					'cancellation_available_up_to_unit' => isset( $_POST['_yith_booking_cancelled_unit'] ) ? wc_clean( wp_unslash( $_POST['_yith_booking_cancelled_unit'] ) ) : null,
					'check_in'                          => isset( $_POST['_yith_booking_checkin'] ) ? wc_clean( wp_unslash( $_POST['_yith_booking_checkin'] ) ) : null,
					'check_out'                         => isset( $_POST['_yith_booking_checkout'] ) ? wc_clean( wp_unslash( $_POST['_yith_booking_checkout'] ) ) : null,
					'allowed_start_days'                => isset( $_POST['_yith_booking_allowed_start_days'] ) ? wc_clean( wp_unslash( $_POST['_yith_booking_allowed_start_days'] ) ) : array(),
					'daily_start_time'                  => isset( $_POST['_yith_booking_daily_start_time'] ) ? wc_clean( wp_unslash( $_POST['_yith_booking_daily_start_time'] ) ) : null,
					'buffer'                            => isset( $_POST['_yith_booking_buffer'] ) ? wc_clean( wp_unslash( $_POST['_yith_booking_buffer'] ) ) : null,
					'time_increment_based_on_duration'  => isset( $_POST['_yith_booking_time_increment_based_on_duration'] ),
					'time_increment_including_buffer'   => isset( $_POST['_yith_booking_time_increment_including_buffer'] ),
					'minimum_advance_reservation'       => isset( $_POST['_yith_booking_allow_after'] ) ? wc_clean( wp_unslash( $_POST['_yith_booking_allow_after'] ) ) : null,
					'minimum_advance_reservation_unit'  => isset( $_POST['_yith_booking_allow_after_unit'] ) ? wc_clean( wp_unslash( $_POST['_yith_booking_allow_after_unit'] ) ) : null,
					'maximum_advance_reservation'       => isset( $_POST['_yith_booking_allow_until'] ) ? wc_clean( wp_unslash( $_POST['_yith_booking_allow_until'] ) ) : null,
					'maximum_advance_reservation_unit'  => isset( $_POST['_yith_booking_allow_until_unit'] ) ? wc_clean( wp_unslash( $_POST['_yith_booking_allow_until_unit'] ) ) : null,
					'availability_rules'                => isset( $_POST['_yith_booking_availability_range'] ) ? wc_clean( wp_unslash( $_POST['_yith_booking_availability_range'] ) ) : array(),
					'default_availabilities'            => isset( $_POST['_yith_booking_default_availabilities'] ) ? wc_clean( wp_unslash( $_POST['_yith_booking_default_availabilities'] ) ) : array(),
					'base_price'                        => isset( $_POST['_yith_booking_block_cost'] ) ? wc_clean( wp_unslash( $_POST['_yith_booking_block_cost'] ) ) : null,
					'fixed_base_fee'                    => isset( $_POST['_yith_booking_base_cost'] ) ? wc_clean( wp_unslash( $_POST['_yith_booking_base_cost'] ) ) : null,
					'price_rules'                       => isset( $_POST['_yith_booking_costs_range'] ) ? wc_clean( wp_unslash( $_POST['_yith_booking_costs_range'] ) ) : array(),
				);

				$result = $product->set_props( $props );

				if ( is_wp_error( $result ) ) {
					$message = sprintf( 'Error when trying to set product meta before saving: %s', $result->get_error_message() );
					yith_wcbk_add_log( $message, YITH_WCBK_Logger_Types::ERROR, YITH_WCBK_Logger_Groups::GENERAL );
				}

				do_action( 'yith_wcbk_process_bookable_product_meta', $product );
			}
			// phpcs:enable
		}

		/**
		 * Regenerate product data after saving
		 *
		 * @param int $product_id Product ID.
		 */
		public function regenerate_product_data_after_saving( $product_id ) {
			yith_wcbk_regenerate_product_data( $product_id );
		}

		/**
		 * Return true if the product is Booking Product
		 *
		 * @param bool|int|WP_Post|WC_Product $product The product.
		 *
		 * @return bool
		 */
		public static function is_booking( $product = null ) {
			$product_id = false;
			if ( $product instanceof WC_Product ) {
				return $product->is_type( self::$prod_type );
			} elseif ( is_null( $product ) ) {
				$product = $GLOBALS['product'] ?? false;
				if ( $product instanceof WC_Product ) {
					return $product->is_type( self::$prod_type );
				}
				$product = $GLOBALS['post'] ?? false;
				if ( $product instanceof WP_Post ) {
					$product_id = $product->ID;
				}
			} elseif ( is_numeric( $product ) ) {
				$product_id = absint( $product );
			} elseif ( $product instanceof WP_Post ) {
				$product_id = $product->ID;
			}

			if ( ! $product_id ) {
				return false;
			}

			$terms        = get_the_terms( $product_id, 'product_type' );
			$product_type = ! empty( $terms ) && isset( current( $terms )->name ) ? sanitize_title( current( $terms )->name ) : 'simple';

			return self::$prod_type === $product_type;
		}
	}
}
