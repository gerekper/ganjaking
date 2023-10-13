<?php
/**
 * Class YITH_WCBK_Assets
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking\Classes
 */

defined( 'YITH_WCBK' ) || exit;

// phpcs:disable WordPress.Arrays.MultipleStatementAlignment.LongIndexSpaceBeforeDoubleArrow, WordPress.Arrays.MultipleStatementAlignment.DoubleArrowNotAligned

if ( ! class_exists( 'YITH_WCBK_Assets' ) ) {
	/**
	 * Class YITH_WCBK_Assets
	 *
	 * @since  4.0.0
	 */
	class YITH_WCBK_Assets {
		use YITH_WCBK_Singleton_Trait;

		/**
		 * The constructor.
		 */
		protected function __construct() {
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ), 11 );
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend_scripts' ), 11 );
			add_action( 'admin_enqueue_scripts', array( $this, 'maybe_dequeue_woocommerce_admin_scripts' ), 99 );

			add_action( 'wp_enqueue_scripts', array( $this, 'custom_frontend_styles' ), 11 );

			add_filter( 'woocommerce_screen_ids', array( $this, 'add_screen_ids' ), 99, 1 );

			add_filter( 'pre_load_script_translations', array( $this, 'script_translations' ), 10, 4 );
		}

		/**
		 * Get scripts.
		 *
		 * @param string $context The context [admin or frontend].
		 *
		 * @return array
		 */
		public function get_scripts( string $context ): array {
			$booking_form_params = array(
				'ajaxurl'                                 => admin_url( 'admin-ajax.php' ),
				'is_admin'                                => is_admin(),
				'form_error_handling'                     => yith_wcbk()->settings->get_form_error_handling(),
				'ajax_update_non_available_dates_on_load' => get_option( 'yith-wcbk-ajax-update-non-available-dates-on-load', 'no' ),
				'i18n_empty_duration'                     => __( 'Choose a duration', 'yith-booking-for-woocommerce' ),
				'i18n_empty_date'                         => __( 'Select a date', 'yith-booking-for-woocommerce' ),
				'i18n_empty_date_for_time'                => __( 'Select a date to choose the time', 'yith-booking-for-woocommerce' ),
				'i18n_empty_time'                         => __( 'Select Time', 'yith-booking-for-woocommerce' ),
				// translators: %s is the minimum number of people.
				'i18n_min_persons'                        => __( 'Minimum people: %s', 'yith-booking-for-woocommerce' ),
				// translators: %s is the maximum number of people.
				'i18n_max_persons'                        => __( 'Maximum people: %s', 'yith-booking-for-woocommerce' ),
				// translators: %s is the minimum duration.
				'i18n_min_duration'                       => __( 'Minimum duration: %s', 'yith-booking-for-woocommerce' ),
				// translators: %s is the maximum duration.
				'i18n_max_duration'                       => __( 'Maximum duration: %s', 'yith-booking-for-woocommerce' ),
				'i18n_days'                               => array(
					'singular' => yith_wcbk_get_duration_label_string( 'day' ),
					'plural'   => yith_wcbk_get_duration_label_string( 'day', true ),
				),
				'price_first_only'                        => 'yes',
				'dom'                                     => array(
					'product_container' => '.product',
					'price'             => '.price,.wc-block-components-product-price',
				),
			);

			$common_scripts = array(
				'yith-wcbk-ajax'            => array(
					'src'              => YITH_WCBK_ASSETS_URL . '/js/ajax.js',
					'context'          => 'common',
					'deps'             => array( 'jquery' ),
					'localize_globals' => array( 'bk' ),
				),
				'yith-wcbk-people-selector' => array(
					'src'              => YITH_WCBK_ASSETS_URL . '/js/yith-wcbk-people-selector.js',
					'context'          => 'common',
					'deps'             => array( 'jquery' ),
					'localize_globals' => array( 'bk' ),
					'localize'         => array(
						'yith_people_selector_params' => apply_filters(
							'yith_wcbk_js_people_selector_params',
							array(
								'i18n_zero_person'  => __( 'Select people', 'yith-booking-for-woocommerce' ),
								'i18n_one_person'   => __( '1 person', 'yith-booking-for-woocommerce' ),
								// translators: %s is the number of persons.
								'i18n_more_persons' => __( '%s persons', 'yith-booking-for-woocommerce' ),
							)
						),
					),
				),
				'yith-wcbk-monthpicker'     => array(
					'src'     => YITH_WCBK_ASSETS_URL . '/js/monthpicker.js',
					'context' => 'common',
					'deps'    => array( 'jquery' ),
				),
				'yith-wcbk-datepicker'      => array(
					'src'              => YITH_WCBK_ASSETS_URL . '/js/datepicker.js',
					'context'          => 'common',
					'deps'             => array( 'jquery', 'jquery-ui-datepicker', 'jquery-blockui', 'yith-wcbk-dates' ),
					'localize_globals' => array( 'bk' ),
					'localize'         => array(
						'yith_wcbk_datepicker_params' => array(
							'i18n_clear' => apply_filters( 'yith_wcbk_i18n_clear', __( 'Clear', 'yith-booking-for-woocommerce' ) ),
						),
					),
					'admin_enqueue'    => 'all-plugin-pages',
				),
				'yith-wcbk-dates'           => array(
					'src'     => YITH_WCBK_ASSETS_URL . '/js/yith-wcbk-dates.js',
					'context' => 'common',
				),
				'yith-wcbk-fields'          => array(
					'src'     => YITH_WCBK_ASSETS_URL . '/js/fields.js',
					'deps'    => array( 'jquery-tiptip' ),
					'context' => 'common',
				),
				'yith-wcbk-booking-form'    => array(
					'src'              => YITH_WCBK_ASSETS_URL . '/js/booking_form.js',
					'context'          => 'common',
					'deps'             => array( 'jquery', 'yith-wcbk-fields', 'yith-wcbk-dates', 'yith-wcbk-datepicker', 'yith-wcbk-monthpicker', 'yith-wcbk-people-selector', 'yith-wcbk-ajax' ),
					'localize_globals' => array( 'bk' ),
					'localize'         => array(
						'yith_booking_form_params' => apply_filters( 'yith_booking_form_params', $booking_form_params ),
					),
					'admin_enqueue'    => array( 'edit-' . YITH_WCBK_Post_Types::BOOKING, 'panel/dashboard/bookings-calendar' ),
					'frontend_enqueue' => true,
				),
			);

			if ( 'admin' === $context ) {
				$admin_scripts = array(
					'yith-wcbk-admin-ajax'                       => array(
						'src'              => YITH_WCBK_ASSETS_URL . '/js/admin/admin-ajax.js',
						'context'          => 'admin',
						'deps'             => array( 'jquery', 'jquery-blockui' ),
						'localize_globals' => array( 'bk', 'wcbk_admin' ),
					),
					'yith-wcbk-admin'                            => array(
						'src'              => YITH_WCBK_ASSETS_URL . '/js/admin/admin.js',
						'context'          => 'admin',
						'deps'             => array( 'jquery', 'jquery-tiptip', 'yith-wcbk-datepicker', 'yith-plugin-fw-fields', 'wp-i18n' ),
						'localize_globals' => array( 'bk', 'wcbk_admin' ),
						'enqueue'          => 'all-plugin-pages',
					),
					'yith-wcbk-admin-booking-availability-rules' => array(
						'src'     => YITH_WCBK_ASSETS_URL . '/js/admin/admin-booking-availability-rules.js',
						'context' => 'admin',
						'deps'    => array( 'jquery', 'yith-wcbk-datepicker', 'wp-util', 'wp-i18n', 'yith-ui' ),
						'enqueue' => array( 'product', 'panel/configuration/availability-rules' ),
					),
					'yith-wcbk-admin-booking-price-rules'        => array(
						'src'     => YITH_WCBK_ASSETS_URL . '/js/admin/admin-booking-price-rules.js',
						'context' => 'admin',
						'deps'    => array( 'jquery', 'yith-wcbk-datepicker', 'wp-util', 'wp-i18n', 'yith-ui' ),
						'enqueue' => array( 'product', 'panel/configuration/price-rules' ),
					),
					'yith-wcbk-admin-booking-calendar'           => array(
						'src'     => YITH_WCBK_ASSETS_URL . '/js/admin/admin-booking-calendar.js',
						'context' => 'admin',
						'deps'    => array( 'jquery', 'jquery-blockui' ),
						'enqueue' => 'panel/dashboard/bookings-calendar',
					),
					'yith-wcbk-admin-booking-create'             => array(
						'src'              => YITH_WCBK_ASSETS_URL . '/js/admin/admin-booking-create.js',
						'deps'             => array( 'jquery', 'yith-wcbk-booking-form', 'yith-wcbk-fields', 'wp-util' ),
						'context'          => 'admin',
						'localize_globals' => array( 'bk', 'wcbk_admin' ),
						'enqueue'          => array( 'edit-' . YITH_WCBK_Post_Types::BOOKING, 'panel/dashboard/bookings-calendar' ),
					),
					'yith-wcbk-admin-booking-meta-boxes'         => array(
						'src'         => YITH_WCBK_ASSETS_URL . '/js/admin/admin-booking-meta-boxes.js',
						'context'     => 'admin',
						'deps'        => array( 'jquery' ),
						'enqueue'     => YITH_WCBK_Post_Types::BOOKING,
						'localize_cb' => array(
							'wcbk_admin_booking_meta_boxes' => function () {
								global $post;
								$post_id = ! ! $post && isset( $post->ID ) ? $post->ID : '';

								return array(
									'post_id'                   => $post_id,
									'add_booking_note_nonce'    => wp_create_nonce( 'add-booking-note' ),
									'delete_booking_note_nonce' => wp_create_nonce( 'delete-booking-note' ),
									'i18n_delete_note'          => __( 'Are you sure you want to delete this note? This action cannot be undone.', 'yith-booking-for-woocommerce' ),
								);
							},
						),
					),
					'yith-wcbk-admin-booking-product'            => array(
						'src'              => YITH_WCBK_ASSETS_URL . '/js/admin/admin-booking-product.js',
						'context'          => 'admin',
						'deps'             => array( 'jquery', 'jquery-blockui', 'yith-wcbk-datepicker', 'jquery-ui-sortable' ),
						'localize_globals' => array( 'bk', 'wcbk_admin' ),
						'enqueue'          => 'product',
					),
					'yith-wcbk-admin-booking-settings-sections'  => array(
						'src'              => YITH_WCBK_ASSETS_URL . '/js/admin/admin-booking-settings-sections.js',
						'context'          => 'admin',
						'deps'             => array( 'jquery', 'jquery-ui-sortable' ),
						'localize_globals' => array( 'wcbk_admin' ),
						'enqueue'          => array( 'product', 'panel/configuration/availability-rules', 'panel/configuration/price-rules' ),
					),
					'yith-wcbk-admin-prevent-leave-on-changes'   => array(
						'src'              => YITH_WCBK_ASSETS_URL . '/js/admin/admin-prevent-leave-on-changes.js',
						'context'          => 'admin',
						'deps'             => array( 'jquery' ),
						'localize_globals' => array( 'wcbk_admin' ),
					),
					'yith-wcbk-admin-suggested-themes'           => array(
						'src'              => YITH_WCBK_ASSETS_URL . '/js/admin/suggested-themes.js',
						'context'          => 'admin',
						'deps'             => array( 'jquery', 'updates' ),
						'localize_globals' => array( 'wcbk_admin' ),
						'enqueue'          => 'panel/settings/general-settings',
					),
					'yith-wcbk-admin-modules'                    => array(
						'src'              => YITH_WCBK_ASSETS_URL . '/js/admin/modules.js',
						'context'          => 'admin',
						'deps'             => array( 'jquery' ),
						'localize_globals' => array( 'bk', 'wcbk_admin' ),
						'enqueue'          => 'panel/modules',
					),
					'yith-wcbk-admin-email-settings'             => array(
						'src'     => YITH_WCBK_ASSETS_URL . '/js/admin/email-settings.js',
						'context' => 'admin',
						'deps'    => array( 'jquery', 'yith-wcbk-admin-ajax' ),
						'enqueue' => array( 'panel/emails', 'woocommerce_page_wc-settings' ),
					),
					'yith-wcbk-admin-welcome'                    => array(
						'src'     => YITH_WCBK_ASSETS_URL . '/js/admin/welcome.js',
						'context' => 'admin',
						'deps'    => array( 'jquery', 'wp-util' ),
					),
					'yith-wcbk-enhanced-select'                  => array(
						'src'      => YITH_WCBK_ASSETS_URL . '/js/admin/enhanced-select.js',
						'context'  => 'admin',
						'deps'     => array( 'jquery' ),
						'localize' => array(
							'yith_wcbk_enhanced_select_params' => array(
								'ajax_url'              => admin_url( 'admin-ajax.php' ),
								'search_bookings_nonce' => wp_create_nonce( 'search-bookings' ),
								'search_orders_nonce'   => wp_create_nonce( 'search-orders' ),
								'i18n'                  => array(
									'no_matches'        => _x( 'No matches found', 'Enhanced select', 'yith-booking-for-woocommerce' ),
									'input_too_short_1' => _x( 'Please enter 1 or more characters', 'Enhanced select', 'yith-booking-for-woocommerce' ),
									// translators: %s is the number of characters.
									'input_too_short_n' => _x( 'Please enter %s or more characters', 'Enhanced select', 'yith-booking-for-woocommerce' ),
									'searching'         => _x( 'Searching&hellip;', 'Enhanced select', 'yith-booking-for-woocommerce' ),
								),
							),
						),
						'enqueue'  => 'all-plugin-pages',
					),
					'jquery-tiptip'                              => array(
						'context' => 'admin',
						'enqueue' => 'all-plugin-pages',
					),
					'jquery-ui-datepicker'                       => array(
						'context' => 'admin',
						'enqueue' => 'all-plugin-pages',
					),
				);
				$scripts       = $common_scripts + $admin_scripts;
			} else {
				$frontend_scripts = array(
					'yith-wcbk-mobile-fixed-form' => array(
						'src'     => YITH_WCBK_ASSETS_URL . '/js/mobile-fixed-form.js',
						'context' => 'frontend',
						'deps'    => array( 'jquery' ),
					),
					'yith-wcbk-confirm-button'    => array(
						'src'     => YITH_WCBK_ASSETS_URL . '/js/confirm-button.js',
						'context' => 'frontend',
						'deps'    => array( 'jquery' ),
					),
					'yith-wcbk-popup'             => array(
						'src'              => YITH_WCBK_ASSETS_URL . '/js/yith-wcbk-popup.js',
						'context'          => 'frontend',
						'deps'             => array( 'jquery', 'jquery-blockui' ),
						'localize_globals' => array( 'bk' ),
					),
					'yith-wcbk-services-selector' => array(
						'src'     => YITH_WCBK_ASSETS_URL . '/js/yith-wcbk-services-selector.js',
						'context' => 'frontend',
						'deps'    => array( 'jquery' ),
					),
					'jquery-tiptip'               => array(
						'src'     => WC()->plugin_url() . '/assets/js/jquery-tiptip/jquery.tipTip.js',
						'context' => 'frontend',
						'deps'    => array( 'jquery' ),
						'version' => WC_VERSION,
					),
				);
				$scripts          = $common_scripts + $frontend_scripts;
			}

			if ( 'admin' === $context ) {
				$library_packages = array( 'components', 'date', 'styles' );

				foreach ( $library_packages as $package ) {
					$asset_file = YITH_WCBK_ASSETS_PATH . "/js/ui-library/{$package}/index.asset.php";
					if ( file_exists( $asset_file ) ) {
						$asset_info         = include $asset_file;
						$handle             = 'yith-wcbk-ui-' . $package;
						$scripts[ $handle ] = array(
							'src'     => YITH_WCBK_ASSETS_URL . "/js/ui-library/{$package}/index.js",
							'context' => 'admin',
							'deps'    => $asset_info['dependencies'] ?? array(),
							'version' => $asset_info['version'] ?? '1.0.0',
							'use_min' => false,
						);
					}
				}

				$react_scripts = array(
					'yith-wcbk-admin-global-availability-rules' => array(
						'path'             => 'admin/global-availability-rules',
						'context'          => 'admin',
						'enqueue'          => 'panel/configuration/availability-rules',
						'localize_globals' => array( 'wcbk_admin' ),
					),
					'yith-wcbk-admin-global-price-rules'        => array(
						'path'             => 'admin/global-price-rules',
						'context'          => 'admin',
						'enqueue'          => 'panel/configuration/price-rules',
						'localize_globals' => array( 'wcbk_admin' ),
						'localize_cb'      => array(
							'yithBookingGlobalPriceRulesSettings' => function () {
								$people_types = array();

								if ( yith_wcbk_is_people_module_active() ) {
									$ids = yith_wcbk()->person_type_helper()->get_person_type_ids();
									if ( ! ! $ids ) {
										foreach ( $ids as $id ) {
											$people_types[] = array(
												'id'   => $id,
												'name' => yith_wcbk()->person_type_helper()->get_person_type_title( $id ),
											);
										}
									}
								}

								return array(
									'isPeopleModuleActive' => yith_wcbk_is_people_module_active() ? 'yes' : 'no',
									'peopleTypes'          => $people_types,
								);
							},
						),
					),
				);

				foreach ( $react_scripts as $handle => $react_script ) {
					$path = $react_script['path'];
					unset( $react_script['path'] );
					$asset_file = YITH_WCBK_DIR . "dist/{$path}/index.asset.php";
					if ( file_exists( $asset_file ) ) {
						$asset_info         = include $asset_file;
						$scripts[ $handle ] = array_merge(
							array(
								'src'     => YITH_WCBK_URL . "dist/{$path}/index.js",
								'deps'    => $asset_info['dependencies'] ?? array(),
								'version' => $asset_info['version'] ?? '1.0.0',
								'use_min' => false,
							),
							$react_script
						);
					}
				}
			}

			$scripts = (array) apply_filters( 'yith_wcbk_scripts', $scripts, $context );

			return $this->filter_assets_by_context( $scripts, $context );
		}

		/**
		 * Enqueue Styles
		 *
		 * @param string $context The context [admin or frontend].
		 *
		 * @return array
		 */
		public function get_styles( string $context ): array {
			global $wp_scripts;

			$common_styles = array(
				'yith-wcbk'                   => array(
					'src'     => YITH_WCBK_ASSETS_URL . '/css/global.css',
					'context' => 'common',
				),
				'yith-wcbk-people-selector'   => array(
					'src'              => YITH_WCBK_ASSETS_URL . '/css/people-selector.css',
					'context'          => 'common',
					'deps'             => array( 'yith-wcbk' ),
					'frontend_enqueue' => true,
				),
				'yith-wcbk-date-range-picker' => array(
					'src'              => YITH_WCBK_ASSETS_URL . '/css/date-range-picker.css',
					'context'          => 'common',
					'deps'             => array( 'yith-wcbk' ),
					'frontend_enqueue' => true,
				),
				'yith-wcbk-datepicker'        => array(
					'src'              => YITH_WCBK_ASSETS_URL . '/css/datepicker.css',
					'context'          => 'common',
					'deps'             => array( 'yith-wcbk' ),
					'admin_enqueue'    => 'all-plugin-pages',
					'frontend_enqueue' => true,
				),
				'yith-wcbk-fields'            => array(
					'src'     => YITH_WCBK_ASSETS_URL . '/css/fields.css',
					'context' => 'common',
					'deps'    => array( 'yith-wcbk' ),
				),
				'yith-wcbk-booking-form'      => array(
					'src'              => YITH_WCBK_ASSETS_URL . '/css/booking-form.css',
					'context'          => 'common',
					'deps'             => array( 'yith-wcbk', 'yith-wcbk-fields', 'yith-wcbk-people-selector', 'yith-wcbk-date-range-picker', 'yith-plugin-fw-icon-font' ),
					'admin_enqueue'    => array( 'edit-' . YITH_WCBK_Post_Types::BOOKING, 'panel/dashboard/bookings-calendar' ),
					'frontend_enqueue' => true,
				),
				'jquery-ui-style'             => array(
					'src'              => YITH_WCBK_ASSETS_URL . '/css/jquery-ui/jquery-ui.min.css',
					'context'          => 'common',
					'version'          => '1.13.1',
					'admin_enqueue'    => 'all-plugin-pages',
					'frontend_enqueue' => true,
				),
			);

			if ( 'admin' === $context ) {
				$admin_styles = array(
					'yith-wcbk-admin-fields'            => array(
						'src'     => YITH_WCBK_ASSETS_URL . '/css/admin/admin-fields.css',
						'context' => 'admin',
						'deps'    => array( 'yith-wcbk', 'yith-plugin-fw-fields' ),
					),
					'yith-wcbk-admin-settings-sections' => array(
						'src'     => YITH_WCBK_ASSETS_URL . '/css/admin/admin-settings-sections.css',
						'context' => 'admin',
						'deps'    => array( 'yith-wcbk' ),
					),
					'yith-wcbk-admin'                   => array(
						'src'     => YITH_WCBK_ASSETS_URL . '/css/admin/admin.css',
						'context' => 'admin',
						'deps'    => array( 'yith-wcbk', 'yith-wcbk-admin-fields', 'yith-wcbk-admin-settings-sections' ),
						'enqueue' => array( 'all-plugin-pages', 'woocommerce_page_wc-settings' ),
					),
					'yith-wcbk-admin-rtl'               => array(
						'src'        => YITH_WCBK_ASSETS_URL . '/css/admin/admin-rtl.css',
						'context'    => 'admin',
						'enqueue_cb' => 'is_rtl',
					),
					'yith-wcbk-admin-booking'           => array(
						'src'     => YITH_WCBK_ASSETS_URL . '/css/admin/admin-booking.css',
						'context' => 'admin',
						'deps'    => array( 'yith-wcbk' ),
						'enqueue' => YITH_WCBK_Post_Types::BOOKING,
					),
					'yith-wcbk-admin-email-settings'    => array(
						'src'     => YITH_WCBK_ASSETS_URL . '/css/admin/email-settings.css',
						'context' => 'admin',
						'deps'    => array( 'yith-wcbk', 'yith-wcbk-admin-fields', 'yith-wcbk-admin' ),
						'enqueue' => array( 'panel/emails', 'woocommerce_page_wc-settings' ),
					),
					'yith-wcbk-admin-booking-calendar'  => array(
						'src'     => YITH_WCBK_ASSETS_URL . '/css/admin/booking-calendar.css',
						'context' => 'admin',
						'deps'    => array( 'yith-wcbk' ),
						'enqueue' => 'panel/dashboard/bookings-calendar',
					),
					'yith-wcbk-admin-modules'           => array(
						'src'     => YITH_WCBK_ASSETS_URL . '/css/admin/modules.css',
						'context' => 'admin',
						'deps'    => array( 'yith-wcbk' ),
						'enqueue' => 'panel/modules',
					),
					'yith-wcbk-admin-welcome'           => array(
						'src'     => YITH_WCBK_ASSETS_URL . '/css/admin/welcome.css',
						'context' => 'admin',
						'deps'    => array( 'yith-wcbk' ),
					),
					'yith-wcbk-admin-logs'              => array(
						'src'     => YITH_WCBK_ASSETS_URL . '/css/admin/admin-logs.css',
						'context' => 'admin',
						'deps'    => array( 'yith-wcbk' ),
						'enqueue' => 'panel/tools/logs',
					),
					'yith-wcbk-admin-orders'            => array(
						'src'     => YITH_WCBK_ASSETS_URL . '/css/admin/admin-orders.css',
						'context' => 'admin',
						'deps'    => array( 'yith-wcbk' ),
						'enqueue' => array( 'shop_order', wc_get_page_screen_id( 'shop-order' ) ), // todo: HPOS - remove shop_order when removing support for older WC versions.
					),
				);

				$styles = $common_styles + $admin_styles;
			} else {
				$frontend_styles = array(
					'yith-wcbk-frontend-style' => array(
						'src'     => YITH_WCBK_ASSETS_URL . '/css/frontend/frontend.css',
						'context' => 'frontend',
						'deps'    => array( 'yith-wcbk' ),
						'enqueue' => true,
					),
					'yith-wcbk-popup'          => array(
						'src'     => YITH_WCBK_ASSETS_URL . '/css/frontend/popup.css',
						'context' => 'frontend',
						'deps'    => array( 'yith-wcbk' ),
						'enqueue' => true,
					),
					'yith-wcbk-frontend-rtl'   => array(
						'src'        => YITH_WCBK_ASSETS_URL . '/css/frontend/frontend-rtl.css',
						'context'    => 'frontend',
						'enqueue_cb' => 'is_rtl',
					),
					'dashicons'                => array(
						'context' => 'frontend',
						'enqueue' => true,
					),
					'yith-plugin-fw-icon-font' => array(
						'context' => 'frontend',
						'enqueue' => true,
					),
				);

				if ( ! wp_style_is( 'select2', 'registered' ) ) {
					$wc_assets_path             = str_replace( array( 'http:', 'https:' ), '', WC()->plugin_url() ) . '/assets/';
					$frontend_styles['select2'] = array(
						'context' => 'frontend',
						'src'     => $wc_assets_path . 'css/select2.css',
						'version' => defined( 'WC_VERSION' ) ? WC_VERSION : YITH_WCBK_VERSION,
						'enqueue' => true,
					);
				} else {
					$frontend_styles['select2'] = array(
						'context' => 'frontend',
						'enqueue' => true,
					);
				}

				$styles = $common_styles + $frontend_styles;
			}

			$styles = (array) apply_filters( 'yith_wcbk_styles', $styles, $context );

			return $this->filter_assets_by_context( $styles, $context );
		}

		/**
		 * Get styles.
		 *
		 * @param string $context The context [admin or frontend].
		 *
		 * @return array
		 */
		protected function get_globals( string $context ): array {
			$globals = array(
				'bk' => self::get_bk_global_params( $context ),
			);

			if ( 'admin' === $context ) {
				$globals['wcbk_admin'] = array(
					'prod_type'                    => YITH_WCBK_Product_Post_Type_Admin::$prod_type,
					'loader_svg'                   => yith_wcbk_print_svg( 'loader', false ),
					'i18n_delete_log_confirmation' => esc_js( __( 'Are you sure you want to delete logs?', 'yith-booking-for-woocommerce' ) ),
					'i18n_untitled'                => __( 'Untitled', 'yith-booking-for-woocommerce' ),
					'i18n_leave_page_confirmation' => __( 'The changes you made will be lost if you navigate away from this page.', 'yith-booking-for-woocommerce' ),
					'i18n_copied'                  => __( 'Copied!', 'yith-booking-for-woocommerce' ),
					'i18n'                         => array(
						'create_booking'          => _x( 'Create Booking', 'Popup title', 'yith-booking-for-woocommerce' ),
						'themeInstallationFailed' => _x( 'Installation failed!', 'Theme', 'yith-booking-for-woocommerce' ),
						'themeInstall'            => _x( 'Install', 'Theme', 'yith-booking-for-woocommerce' ),
						'themeInstalled'          => _x( 'Installed!', 'Theme', 'yith-booking-for-woocommerce' ),
						'themeInstalling'         => _x( 'Installing...', 'Theme', 'yith-booking-for-woocommerce' ),
						'themeActivate'           => _x( 'Activate', 'Theme', 'yith-booking-for-woocommerce' ),
						'themeNetworkEnable'      => _x( 'Network enable', 'Theme', 'yith-booking-for-woocommerce' ),
						'themeNetworkEnabling'    => _x( 'Enabling...', 'Theme', 'yith-booking-for-woocommerce' ),
						'themeNetworkEnabled'     => _x( 'Enabled', 'Theme', 'yith-booking-for-woocommerce' ),
					),
					'adminAjaxAction'              => YITH_WCBK_AJAX::ADMIN_AJAX_ACTION,
					'nonces'                       => array(
						'get_booking_form' => wp_create_nonce( 'yith-wcbk-get-booking-form' ),
						'themeAction'      => wp_create_nonce( 'yith-wcbk-theme-action' ),
						'modulesAction'    => wp_create_nonce( YITH_WCBK_Modules::AJAX_ACTION ),
						'adminAjax'        => wp_create_nonce( YITH_WCBK_AJAX::ADMIN_AJAX_ACTION ),
						'panelPageNonce'   => wp_create_nonce( yith_wcbk_get_current_admin_page() ),
					),
					'disableWcCheckForChanges'     => apply_filters( 'yith_wcbk_admin_js_disable_wc_check_for_changes', false ),
					'isCreatingNewBookingProduct'  => ! empty( $_GET['yith-wcbk-new-booking-product'] ) ? 'yes' : 'no', // phpcs:ignore WordPress.Security.NonceVerification.Recommended,
					'productCount'                 => array_sum( (array) wp_count_posts( 'product' ) ),
					'currencySymbol'               => get_woocommerce_currency_symbol(),
					'panelPage'                    => yith_wcbk_get_current_admin_page(),
				);
			}

			return $globals;
		}

		/**
		 * Get Booking global params.
		 *
		 * @param string $context The context.
		 *
		 * @return array
		 */
		public static function get_bk_global_params( string $context = 'common' ): array {
			$loader_svg = yith_wcbk_print_svg( 'loader', false );
			$bk         = array(
				'ajaxurl'             => admin_url( 'admin-ajax.php' ),
				'frontendAjaxAction'  => YITH_WCBK_AJAX::FRONTEND_AJAX_ACTION,
				'loader_svg'          => $loader_svg,
				'settings'            => array(
					'check_min_max_duration_in_calendar' => yith_wcbk()->settings->check_min_max_duration_in_calendar() ? 'yes' : 'no',
					'datepickerFormat'                   => yith_wcbk()->settings->get_date_picker_format(),
				),
				'blockParams'         => array(
					'message'         => $loader_svg,
					'blockMsgClass'   => 'yith-wcbk-block-ui-element',
					'css'             => array(
						'border'     => 'none',
						'background' => 'transparent',
					),
					'overlayCSS'      => array(
						'background' => '#ffffff',
						'opacity'    => '0.7',
					),
					'ignoreIfBlocked' => false,
				),
				'blockParamsNoLoader' => array(
					'message'         => '',
					'css'             => array(
						'border'     => 'none',
						'background' => 'transparent',
					),
					'overlayCSS'      => array(
						'background' => '#ffffff',
						'opacity'    => '0.7',
					),
					'ignoreIfBlocked' => false,
				),
				'blockParamsEmpty'    => array(
					'message'         => false,
					'overlayCSS'      => array(
						'opacity' => '0',
					),
					'ignoreIfBlocked' => false,
				),
				'blockParamsDisable'  => array(
					'message'         => ' ',
					'css'             => array(
						'border'     => 'none',
						'background' => '#fff',
						'top'        => '0',
						'left'       => '0',
						'height'     => '100%',
						'width'      => '100%',
						'opacity'    => '0.7',
						'cursor'     => 'default',
					),
					'overlayCSS'      => array(
						'opacity' => '0',
					),
					'ignoreIfBlocked' => true,
				),
				'i18n_durations'      => array(
					'month'  => array(
						'singular_unit' => yith_wcbk_get_duration_unit_label( 'month', 1 ),
						'plural_unit'   => yith_wcbk_get_duration_unit_label( 'month', 2 ),
						'singular'      => yith_wcbk_get_duration_label_string( 'month' ),
						'plural'        => yith_wcbk_get_duration_label_string( 'month', true ),
						'singular_qty'  => yith_wcbk_get_duration_label_string( 'month', false, 'unit' ),
						'plural_qty'    => yith_wcbk_get_duration_label_string( 'month', true, 'unit' ),
					),
					'day'    => array(
						'singular_unit' => yith_wcbk_get_duration_unit_label( 'day', 1 ),
						'plural_unit'   => yith_wcbk_get_duration_unit_label( 'day', 2 ),
						'singular'      => yith_wcbk_get_duration_label_string( 'day' ),
						'plural'        => yith_wcbk_get_duration_label_string( 'day', true ),
						'singular_qty'  => yith_wcbk_get_duration_label_string( 'day', false, 'unit' ),
						'plural_qty'    => yith_wcbk_get_duration_label_string( 'day', true, 'unit' ),
					),
					'hour'   => array(
						'singular_unit' => yith_wcbk_get_duration_unit_label( 'hour', 1 ),
						'plural_unit'   => yith_wcbk_get_duration_unit_label( 'hour', 2 ),
						'singular'      => yith_wcbk_get_duration_label_string( 'hour' ),
						'plural'        => yith_wcbk_get_duration_label_string( 'hour', true ),
						'singular_qty'  => yith_wcbk_get_duration_label_string( 'hour', false, 'unit' ),
						'plural_qty'    => yith_wcbk_get_duration_label_string( 'hour', true, 'unit' ),
					),
					'minute' => array(
						'singular_unit' => yith_wcbk_get_duration_unit_label( 'minute', 1 ),
						'plural_unit'   => yith_wcbk_get_duration_unit_label( 'minute', 2 ),
						'singular'      => yith_wcbk_get_duration_label_string( 'minute' ),
						'plural'        => yith_wcbk_get_duration_label_string( 'minute', true ),
						'singular_qty'  => yith_wcbk_get_duration_label_string( 'minute', false, 'unit' ),
						'plural_qty'    => yith_wcbk_get_duration_label_string( 'minute', true, 'unit' ),
					),
				),
				'nonces'              => array(
					'searchBookingProductsPaged'  => wp_create_nonce( 'search-booking-products-paged' ),
					'getBookingData'              => wp_create_nonce( 'get-booking-data' ),
					'getAvailableTimes'           => wp_create_nonce( 'get-available-times' ),
					'getProductNonAvailableDates' => wp_create_nonce( 'get-product-non-available-dates' ),
				),
			);

			return apply_filters( 'yith_wcbk_assets_bk_global_params', $bk, $context );
		}

		/**
		 * Retrieve an asset prop by context for common assets.
		 *
		 * @param array  $asset   The asset array.
		 * @param string $prop    The prop.
		 * @param string $context The context [admin or frontend].
		 * @param mixed  $default The default value.
		 *
		 * @return mixed
		 */
		protected function get_common_asset_prop( array $asset, string $prop, string $context, $default = false ) {
			$context_prop  = $context . '_' . $prop;
			$asset_context = $asset['context'] ?? false;
			$value         = $asset[ $prop ] ?? $default;
			if ( 'common' === $asset_context ) {
				$value = $asset[ $context_prop ] ?? $value;
			}

			return $value;
		}

		/**
		 * Should enqueue script/style?
		 *
		 * @param array  $asset   The asset info.
		 * @param string $context The context [admin or frontend].
		 *
		 * @return bool
		 */
		protected function should_enqueue( array $asset, string $context ): bool {
			$enqueue    = $this->get_common_asset_prop( $asset, 'enqueue', $context, false );
			$enqueue_cb = $this->get_common_asset_prop( $asset, 'enqueue_cb', $context, false );

			$should_enqueue = true === $enqueue;

			if ( ! $should_enqueue ) {
				if ( $enqueue ) {
					$should_enqueue = 'admin' === $context ? yith_wcbk_is_admin_page( $enqueue ) : $this->frontend_should_enqueue( $enqueue );
				} elseif ( ! ! $enqueue_cb && is_callable( $enqueue_cb ) ) {
					$should_enqueue = call_user_func( $enqueue_cb );
				}
			}

			return $should_enqueue;
		}

		/**
		 * Enqueue scripts and styles.
		 */
		public function enqueue_admin_scripts() {
			$this->enqueue_styles( 'admin' );
			$this->enqueue_scripts( 'admin' );
		}

		/**
		 * Dequeue WooCommerce scripts to avoid warnings when "leaving page".
		 *
		 * @since 5.0.0
		 */
		public function maybe_dequeue_woocommerce_admin_scripts() {
			$pages_where_dequeue_wc_scripts = array(
				'panel/dashboard/bookings-calendar',
				'panel/tools/logs',
				'panel/emails',
				'panel/modules',
			);

			if ( yith_wcbk_is_admin_page( $pages_where_dequeue_wc_scripts ) ) {
				wp_dequeue_script( 'woocommerce_settings' );
				wp_dequeue_script( 'woocommerce_admin' );
			}
		}

		/**
		 * Enqueue scripts and styles.
		 */
		public function enqueue_frontend_scripts() {
			$this->enqueue_styles( 'frontend' );
			$this->enqueue_scripts( 'frontend' );
		}

		/**
		 * Filter styles/scripts by context.
		 *
		 * @param array  $assets  Assets.
		 * @param string $context The context [admin or frontend].
		 *
		 * @return array
		 */
		protected function filter_assets_by_context( array $assets, string $context ): array {
			return array_filter(
				$assets,
				function ( $asset ) use ( $context ) {
					$asset_context = $asset['context'] ?? '';

					return in_array( $asset_context, array( $context, 'common' ), true );
				}
			);
		}

		/**
		 * Enqueue Styles
		 *
		 * @param string $context The context [admin or frontend].
		 */
		protected function enqueue_styles( string $context ) {
			$styles = $this->get_styles( $context );

			// Register.
			foreach ( $styles as $handle => $style ) {
				$src     = $style['src'] ?? '';
				$deps    = $style['deps'] ?? array();
				$version = $style['version'] ?? YITH_WCBK_VERSION;

				if ( $src ) {
					wp_register_style( $handle, $src, $deps, $version );
				}
			}

			// Enqueue.
			foreach ( $styles as $handle => $style ) {
				if ( $this->should_enqueue( $style, $context ) ) {
					wp_enqueue_style( $handle );
				}
			}
		}

		/**
		 * Enqueue scripts
		 *
		 * @param string $context The context [admin or frontend].
		 */
		protected function enqueue_scripts( string $context ) {
			$globals = $this->get_globals( $context );
			$scripts = $this->get_scripts( $context );

			// Register.
			foreach ( $scripts as $handle => $script ) {
				$src       = $script['src'] ?? '';
				$use_min   = $script['use_min'] ?? true;
				$deps      = $script['deps'] ?? array();
				$version   = $script['version'] ?? YITH_WCBK_VERSION;
				$in_footer = $script['in_footer'] ?? true;

				if ( $src ) {
					$is_script_debug = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG;
					if ( $use_min && ! $is_script_debug ) {
						$src = str_replace( '.js', '.min.js', $src );
					}
					wp_register_script( $handle, $src, $deps, $version, $in_footer );

					if ( in_array( 'wp-i18n', $deps, true ) ) {
						wp_set_script_translations( $handle, 'yith-booking-for-woocommerce', YITH_WCBK_LANGUAGES_PATH );
					}
				}
			}

			// Localize.
			foreach ( $scripts as $handle => $script ) {
				$localize         = $this->get_common_asset_prop( $script, 'localize', $context, array() );
				$localize_globals = $this->get_common_asset_prop( $script, 'localize_globals', $context, array() );

				foreach ( $localize as $object_name => $object ) {
					wp_localize_script( $handle, $object_name, $object );
				}

				foreach ( $localize_globals as $global ) {
					if ( isset( $globals[ $global ] ) ) {
						wp_localize_script( $handle, $global, $globals[ $global ] );
					}
				}
			}

			// Enqueue.
			foreach ( $scripts as $handle => $script ) {

				if ( $this->should_enqueue( $script, $context ) ) {
					$localize_cb = $this->get_common_asset_prop( $script, 'localize_cb', $context, array() );

					foreach ( $localize_cb as $object_name => $callback ) {
						if ( ! ! $callback && is_callable( $callback ) ) {
							$object = call_user_func( $callback );
							if ( $object ) {
								wp_localize_script( $handle, $object_name, $object );
							}
						}
					}
					wp_enqueue_script( $handle );
				}
			}
		}

		/**
		 * Should I enqueue the asset on frontend?
		 *
		 * @param array|string|bool $options The options.
		 *
		 * @return bool
		 */
		private function frontend_should_enqueue( $options ): bool {
			return true;
		}

		/**
		 * Custom frontend styles.
		 */
		public function custom_frontend_styles() {
			$css = '';
			foreach ( yith_wcbk_get_colors() as $var => $value ) {
				$css .= '--yith-wcbk-' . $var . ':' . $value . ';';
			}

			$css .= '--yith-wcbk-fields-font-size:' . yith_wcbk()->settings->get_fields_font_size() . ';';

			$css = ':root{' . $css . '}';

			wp_add_inline_style( 'yith-wcbk', $css );

			$booking_form_styles = $this->get_booking_form_styles();
			wp_add_inline_style( 'yith-wcbk-frontend-style', $booking_form_styles );
		}

		/**
		 * Custom booking form styles
		 *
		 * @return string
		 */
		private function get_booking_form_styles(): string {
			$css                           = '';
			$person_type_columns           = max( 1, absint( get_option( 'yith-wcbk-person-type-columns', 1 ) ) );
			$calendar_range_picker_columns = max( 1, absint( get_option( 'yith-wcbk-calendar-range-picker-columns', 1 ) ) );

			if ( ! yith_wcbk()->settings->is_people_selector_enabled() && $person_type_columns > 1 ) {
				$width_percentage = absint( 100 / $person_type_columns ) - 1;

				$css .= ".yith-wcbk-form-section.yith-wcbk-form-section-person-types {
                            width: {$width_percentage}%;
                            display: inline-block;
                        }
                ";
			}

			if ( $calendar_range_picker_columns > 1 ) {
				$css .= '.yith-wcbk-form-section.yith-wcbk-form-section-dates.calendar-day-range-picker {
                            width: calc(50% - 5px);
                            display: block;
                            float: left;
                        }
                        
                        .yith-wcbk-form-section.yith-wcbk-form-section-dates.calendar-day-range-picker + .yith-wcbk-form-section.yith-wcbk-form-section-dates.calendar-day-range-picker{
                        	margin-left: 10px;
                        }
                        
                        .yith-wcbk-form-section.yith-wcbk-form-section-dates.calendar-day-range-picker + .yith-wcbk-form-section.yith-wcbk-form-section-dates.calendar-day-range-picker .yith-wcbk-datepicker--static{
                        	right: 0;
                        }
                ';
			}

			return $css;
		}

		/**
		 * Add custom screen ids to standard WC
		 *
		 * @access public
		 *
		 * @param array $screen_ids Screen IDs.
		 *
		 * @return array
		 */
		public function add_screen_ids( $screen_ids ) {
			$screen_ids[] = 'yith_booking_page_yith-wcbk-booking-calendar';
			$screen_ids[] = YITH_WCBK_Post_Types::BOOKING;
			$screen_ids[] = 'edit-' . YITH_WCBK_Post_Types::BOOKING;
			$screen_ids[] = 'edit-' . YITH_WCBK_Post_Types::SERVICE_TAX;

			return $screen_ids;
		}

		/**
		 * Create the json translation through the PHP file.
		 * So, it's possible using normal translations (with PO files) also for JS translations
		 *
		 * @param string|null $json_translations Translations.
		 * @param string      $file              The file.
		 * @param string      $handle            The handle.
		 * @param string      $domain            The text-domain.
		 *
		 * @return string|null
		 */
		public function script_translations( $json_translations, $file, $handle, $domain ) {
			if ( 'yith-booking-for-woocommerce' === $domain ) {
				$scripts           = $this->get_scripts( 'admin' );
				$handles_with_i18n = array();
				foreach ( $scripts as $script_handle => $script ) {
					$deps = $script['deps'] ?? array();
					if ( in_array( 'wp-i18n', $deps, true ) ) {
						$handles_with_i18n[] = $script_handle;
					}
				}

				if ( in_array( $handle, $handles_with_i18n, true ) ) {
					$path = trailingslashit( YITH_WCBK_LANGUAGES_PATH ) . 'js-i18n.php';
					if ( file_exists( $path ) ) {
						$translations = include $path;

						$json_translations = wp_json_encode(
							array(
								'domain'      => 'yith-booking-for-woocommerce',
								'locale_data' => array(
									'messages' =>
										array(
											'' => array(
												'domain'       => 'yith-booking-for-woocommerce',
												'lang'         => get_locale(),
												'plural-forms' => 'nplurals=2; plural=(n != 1);',
											),
										)
										+
										$translations,
								),
							)
						);

					}
				}
			}

			return $json_translations;
		}
	}
}
