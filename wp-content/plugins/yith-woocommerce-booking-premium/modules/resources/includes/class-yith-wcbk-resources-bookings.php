<?php
/**
 * Class YITH_WCBK_Resources_Bookings
 * Handle booking for the Resources module.
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking\Modules\Resources
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! class_exists( 'YITH_WCBK_Resources_Bookings' ) ) {
	/**
	 * YITH_WCBK_Resources_Products class.
	 */
	class YITH_WCBK_Resources_Bookings {
		use YITH_WCBK_Singleton_Trait;

		/**
		 * YITH_WCBK_Resources_Products constructor.
		 */
		protected function __construct() {
			YITH_WCBK_Resources_Booking_Data_Extension::get_instance();

			// Booking data meta-box.
			add_action( 'yith_wcbk_booking_metabox_info_after_third_column', array( $this, 'show_resources_in_edit_booking' ), 30, 1 );

			// Booking data.
			add_action( 'yith_wcbk_booking_get_booking_data_to_display', array( $this, 'filter_booking_data_to_display' ), 10, 4 );

			// Admin calendar.
			add_action( 'yith_wcbk_admin_calendar_action_bar_after_product_search_field', array( $this, 'print_resource_filter_in_admin_calendar' ) );
			add_action( 'yith_wcbk_booking_helper_get_bookings_in_time_range_args', array( $this, 'filter_bookings_in_time_range_args' ) );
			add_action( 'yith_wcbk_booking_helper_get_bookings_in_time_range_include_externals', array( $this, 'filter_bookings_in_time_range_include_externals' ), 10, 2 );
		}

		/**
		 * Show resources in edit-booking page.
		 *
		 * @param YITH_WCBK_Booking $booking The booking.
		 */
		public function show_resources_in_edit_booking( YITH_WCBK_Booking $booking ) {
			if ( $booking->get_resource_ids() ) {
				yith_wcbk_get_module_view( 'resources', 'meta-boxes/booking-resources.php', compact( 'booking' ) );
			}
		}

		/**
		 * Filter booking data to display, to add resources.
		 *
		 * @param array             $data    The data.
		 * @param string            $context The context (admin or frontend).
		 * @param array             $args    Arguments.
		 * @param YITH_WCBK_Booking $booking The booking.
		 *
		 * @return array
		 */
		public function filter_booking_data_to_display( array $data, string $context, array $args, YITH_WCBK_Booking $booking ): array {
			$resource_ids = $booking->get_resource_ids();
			$is_admin     = 'admin' === $context;
			$is_frontend  = 'frontend' === $context;

			if ( $resource_ids ) {
				$product             = $booking->get_product();
				$label               = ! ! $product && $is_frontend ? $product->get_resources_label() : '';
				$resource_assignment = ! ! $product ? $product->get_resource_assignment() : '';
				$label               = ! ! $label ? $label : __( 'Resources', 'yith-booking-for-woocommerce' );
				$resources           = array_filter( array_map( 'yith_wcbk_get_resource', $resource_ids ) );
				$value               = '';
				if ( $is_admin ) {
					$value = implode(
						' - ',
						array_map(
							function ( YITH_WCBK_Resource $resource ) {
								return sprintf(
									'<a href="%s">%s <small>#%s</small></a>',
									esc_url( get_edit_post_link( $resource->get_id() ) ),
									esc_html( $resource->get_name() ),
									esc_html( $resource->get_id() )
								);
							},
							$resources
						)
					);
				} else {
					if ( in_array( $resource_assignment, array( 'customer-select-one', 'customer-select-more' ), true ) ) {
						$value = implode(
							', ',
							array_map(
								function ( YITH_WCBK_Resource $resource ) {
									return esc_html( $resource->get_name() );
								},
								$resources
							)
						);
					}
				}

				$data['resources'] = array(
					'label'    => $label,
					'display'  => $value,
					'priority' => 100,
				);
			}

			return $data;
		}

		/**
		 * Print resource filter in admin calendar.
		 */
		public function print_resource_filter_in_admin_calendar() {
			$resource = absint( $_REQUEST['resource'] ?? 0 ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			?>

			<div class="yith-wcbk-booking-calendar__filter yith-wcbk-booking-calendar__filter--by-resource">
				<div class="yith-wcbk-booking-calendar__filter__label">
					<?php esc_html_e( 'Filter by resource', 'yith-booking-for-woocommerce' ); ?>
				</div>
				<div class="yith-wcbk-booking-calendar__filter__content">
					<?php
					yith_plugin_fw_get_field(
						array(
							'type'  => 'ajax-posts',
							'style' => 'width:200px',
							'name'  => 'resource',
							'data'  => array(
								'placeholder' => __( 'Search for a resource...', 'yith-booking-for-woocommerce' ),
								'allow_clear' => true,
								'post_type'   => YITH_WCBK_Post_Types::RESOURCE,
							),
							'value' => ! ! $resource ? $resource : '',
						),
						true,
						false
					);
					?>
				</div>
			</div>
			<?php
		}

		/**
		 * Filter args when retrieving bookings in time range.
		 * Useful to filter bookings by resource in admin calendar.
		 *
		 * @param array $args Arguments.
		 *
		 * @return array
		 */
		public function filter_bookings_in_time_range_args( array $args ): array {
			if ( yith_wcbk_is_admin_page( 'panel/dashboard/bookings-calendar' ) ) {
				$resource = absint( $_REQUEST['resource'] ?? 0 ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				if ( $resource ) {
					$args['resources'] = $resource;
				}
			}

			return $args;
		}

		/**
		 * Filter 'include_externals' param when retrieving bookings in time range.
		 * Useful to exclude externals if we're searching for resources, since externals cannot be linked to any resource.
		 *
		 * @param bool  $include Include flag.
		 * @param array $args    Arguments.
		 *
		 * @return bool
		 */
		public function filter_bookings_in_time_range_include_externals( bool $include, array $args ): bool {
			if ( ! empty( $args['resources'] ) ) {
				$include = false;
			}

			return $include;
		}
	}
}
