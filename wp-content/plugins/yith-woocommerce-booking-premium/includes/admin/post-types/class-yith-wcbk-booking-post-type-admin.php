<?php
/**
 * Class YITH_WCBK_Booking_Post_Type_Admin
 * Handles the Booking post type on admin side.
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! class_exists( 'YITH_WCBK_Booking_Post_Type_Admin' ) ) {
	/**
	 * Class YITH_WCBK_Booking_Post_Type_Admin
	 */
	class YITH_WCBK_Booking_Post_Type_Admin extends YITH_WCBK_Post_Type_Admin {
		/**
		 * The post type.
		 *
		 * @var string
		 */
		protected $post_type = 'yith_booking';

		/**
		 * The booking object.
		 *
		 * @var YITH_WCBK_Booking
		 */
		protected $object;

		/**
		 * YITH_WCBK_Booking_Post_Type_Admin constructor.
		 */
		protected function __construct() {
			parent::__construct();

			$this->include_files();

			add_filter( 'get_search_query', array( $this, 'booking_search_label' ) );
			add_filter( 'views_edit-' . $this->post_type, array( $this, 'filter_views' ) );

			add_action( 'admin_action_yith_wcbk_mark_booking_status', array( $this, 'handle_mark_booking_status_action' ) );
			add_action( 'admin_action_yith_wcbk_generate_pdf', array( $this, 'handle_generate_pdf_action' ) );
		}

		/**
		 * Includes files
		 */
		private function include_files() {
			require_once __DIR__ . '/class-yith-wcbk-booking-metabox.php';
			require_once __DIR__ . '/class-yith-wcbk-booking-create.php';
		}

		/**
		 * Return true to use only one column in edit page.
		 *
		 * @return bool
		 */
		protected function use_single_column_in_edit_page() {
			return false;
		}

		/**
		 * Initialize the WP List handlers.
		 */
		public function init_wp_list_handlers() {
			parent::init_wp_list_handlers();
			if ( $this->should_wp_list_handlers_be_loaded() ) {
				add_action( 'manage_posts_extra_tablenav', array( $this, 'add_button_after_wp_list_title' ), 10, 1 );
			}
		}

		/**
		 * Return the post_type settings placeholder.
		 *
		 * @return array Array of settings: title_placeholder, title_description, updated_messages.
		 */
		protected function get_post_type_settings() {
			return array(
				'updated_messages' => array(
					1 => __( 'Booking updated.', 'yith-booking-for-woocommerce' ),
					4 => __( 'Booking updated.', 'yith-booking-for-woocommerce' ),
					7 => __( 'Booking saved.', 'yith-booking-for-woocommerce' ),
				),
				'hide_views'       => false,
			);
		}

		/**
		 * Retrieve an array of parameters for blank state.
		 *
		 * @return array{
		 * @type string $icon_url The icon URL.
		 * @type string $message  The message to be shown.
		 * @type string $cta      The call-to-action button title.
		 * @type string $cta_icon The call-to-action button icon.
		 * @type string $cta_url  The call-to-action button URL.
		 *                        }
		 */
		protected function get_blank_state_params() {
			$params = array(
				'icon_url' => YITH_WCBK_ASSETS_URL . '/images/empty-calendar.svg',
				'message'  => __( 'You have no bookings yet!', 'yith-booking-for-woocommerce' ),
				'cta'      => array(
					'title' => _x( 'Create booking', 'Button text', 'yith-booking-for-woocommerce' ),
					'class' => 'yith-wcbk-create-booking',
					'url'   => '#',
				),
			);

			if ( ! current_user_can( 'yith_create_booking' ) ) {
				unset( $params['cta'] );
			}

			return $params;
		}

		/**
		 * Pre-fetch any data for the row each column has access to it, by loading $this->object.
		 *
		 * @param int $post_id Post ID being shown.
		 */
		protected function prepare_row_data( $post_id ) {
			global $the_booking;
			$the_booking  = yith_get_booking( $post_id );
			$this->object = $the_booking;

			/**
			 * DO_ACTION: yith_wcbk_admin_booking_list_prepare_row_data
			 * Allows third-party plugin to handle custom actions before printing the booking details row.
			 *
			 * @param YITH_WCBK_Booking $the_booking The booking.
			 * @param int               $post_id     The booking ID.
			 */
			do_action( 'yith_wcbk_admin_booking_list_prepare_row_data', $the_booking, $post_id );
		}

		/**
		 * Define hidden columns.
		 *
		 * @return array
		 */
		protected function get_default_hidden_columns() {
			return array(
				'order',
				'duration',
				'taxonomy-yith_booking_service',
				'people',
				'booking_date',
			);
		}

		/**
		 * Define which columns are sortable.
		 *
		 * @param array $columns Existing columns.
		 *
		 * @return array
		 */
		public function define_sortable_columns( $columns ) {
			$custom = array(
				'booking'      => 'booking_id',
				'order'        => 'order_id',
				'from'         => 'from',
				'to'           => 'to',
				'people'       => 'persons',
				'booking_date' => 'date',
			);

			return wp_parse_args( $custom, $columns );
		}

		/**
		 * Define which columns to show on this screen.
		 *
		 * @param array $columns Existing columns.
		 *
		 * @return array
		 */
		public function define_columns( $columns ) {
			$has_date  = isset( $columns['date'] );
			$date_text = $has_date ? $columns['date'] : '';
			if ( $has_date ) {
				unset( $columns['date'] );
			}
			unset( $columns['title'] );

			$new_columns['cb'] = $columns['cb'];
			unset( $columns['cb'] );

			$new_columns['booking']  = __( 'Booking', 'yith-booking-for-woocommerce' );
			$new_columns['listing']  = __( 'Listing', 'yith-booking-for-woocommerce' );
			$new_columns['order']    = __( 'Order', 'yith-booking-for-woocommerce' );
			$new_columns['user']     = __( 'Booked by', 'yith-booking-for-woocommerce' );
			$new_columns['from']     = __( 'From', 'yith-booking-for-woocommerce' );
			$new_columns['to']       = __( 'To', 'yith-booking-for-woocommerce' );
			$new_columns['duration'] = __( 'Duration', 'yith-booking-for-woocommerce' );
			$new_columns['people']   = __( 'People', 'yith-booking-for-woocommerce' );
			$new_columns['amount']   = __( 'Amount', 'yith-booking-for-woocommerce' );
			$new_columns['status']   = __( 'Status', 'yith-booking-for-woocommerce' );

			if ( ! yith_wcbk_is_people_module_active() ) {
				unset( $new_columns['people'] );
			}

			$new_columns = array_merge( $new_columns, $columns );

			$new_columns = array_merge( $new_columns, apply_filters( 'yith_wcbk_booking_custom_columns', array() ) );

			if ( $has_date ) {
				$new_columns['booking_date'] = $date_text;
			}
			$new_columns['actions'] = __( 'Actions', 'yith-booking-for-woocommerce' );

			return $new_columns;
		}

		/**
		 * Define bulk actions.
		 *
		 * @param array $actions Existing actions.
		 *
		 * @return array
		 */
		public function define_bulk_actions( $actions ) {
			$actions = parent::define_bulk_actions( $actions );

			$custom = apply_filters(
				'yith_wcbk_booking_bulk_actions',
				array(
					'export_to_csv' => __( 'Export to CSV', 'yith-booking-for-woocommerce' ),
					'export_to_ics' => __( 'Export to ICS', 'yith-booking-for-woocommerce' ),
				)
			);

			$actions = array_merge( $actions, $custom );

			return $actions;
		}

		/**
		 * Handle bulk actions.
		 *
		 * @param string $redirect_to URL to redirect to.
		 * @param string $action      Action name.
		 * @param array  $ids         List of ids.
		 *
		 * @return string
		 */
		public function handle_bulk_actions( $redirect_to, $action, $ids ) {
			$ids = array_reverse( array_map( 'absint', $ids ) );

			switch ( $action ) {
				case 'export_to_csv':
					yith_wcbk()->exporter->download_csv( $ids );
					break;

				case 'export_to_ics':
					yith_wcbk()->exporter->download_ics( $ids );
					break;

				default:
			}

			return esc_url_raw( $redirect_to );
		}

		/**
		 * Set the correct label when searching for bookings.
		 *
		 * @param string $label The label.
		 *
		 * @return string
		 */
		public function booking_search_label( $label ) {
			global $pagenow, $typenow;

			// phpcs:disable WordPress.Security.NonceVerification.Recommended
			if ( 'edit.php' === $pagenow && $typenow === $this->post_type && get_query_var( 'booking_search' ) && isset( $_GET['s'] ) ) {
				$label = sanitize_text_field( wp_unslash( $_GET['s'] ) );
			}

			// phpcs:enable

			return $label;
		}

		/**
		 * Render any custom filters and search inputs for the list table.
		 */
		protected function render_filters() {
			// phpcs:disable WordPress.Security.NonceVerification.Recommended
			$date_from = ! empty( $_REQUEST['date_from'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['date_from'] ) ) : false;
			$date_to   = ! empty( $_REQUEST['date_to'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['date_to'] ) ) : false;

			$from_datepicker_args = array(
				'type'              => 'datepicker',
				'id'                => 'yith-wcbk-date-from',
				'name'              => 'date_from',
				'value'             => $date_from,
				'data'              => array(
					'date-format' => 'yy-mm-dd',
				),
				'custom_attributes' => array(
					'placeholder' => __( 'From', 'yith-booking-for-woocommerce' ),
				),
			);
			$to_datepicker_args   = array(
				'type'              => 'datepicker',
				'id'                => 'yith-wcbk-date-to',
				'name'              => 'date_to',
				'value'             => $date_to,
				'data'              => array(
					'date-format' => 'yy-mm-dd',
				),
				'custom_attributes' => array(
					'placeholder' => __( 'To', 'yith-booking-for-woocommerce' ),
				),
			);

			echo '<div class="yith-wcbk-wp-list-filters yith-plugin-ui">';

			echo '<label for="yith-wcbk-date-from" class="yith-wcbk-wp-list-filters__label">';
			esc_html_e( 'Filter by date', 'yith-booking-for-woocommerce' );
			echo '</label>';

			yith_plugin_fw_get_field( $from_datepicker_args, true, false );
			echo '<span class="yith-icon yith-icon-calendar yith-icon--right-overlay"></span>';

			echo '<span class="yith-icon yith-icon-arrow-right"></span>';

			yith_plugin_fw_get_field( $to_datepicker_args, true, false );
			echo '<span class="yith-icon yith-icon-calendar yith-icon--right-overlay"></span>';
			echo '</div>';
			// phpcs:enable
		}


		/**
		 * Handle any custom filters.
		 *
		 * @param array $query_vars Query vars.
		 *
		 * @return array
		 */
		protected function query_filters( $query_vars ) {
			// TODO: use lookup table to filter bookings.

			// phpcs:disable WordPress.Security.NonceVerification.Recommended, WordPress.DB.SlowDBQuery, WordPress.DB.DirectDatabaseQuery
			global $wpdb;

			if ( $this->is_upcoming_view() ) {
				$query_vars['meta_query'] = array(
					array(
						'key'     => '_to',
						'value'   => yith_wcbk_get_local_timezone_timestamp(), // The local timezone timestamp is used to consider the "local time".
						'compare' => '>',
					),
				);

				$query_vars['meta_key'] = '_from';
				$query_vars['orderby']  = 'meta_value_num';
				$query_vars['order']    = 'ASC';

				return $query_vars;
			}

			// Search.
			if ( ! empty( $query_vars['s'] ) ) {
				$order_search_fields = array(
					'_order_key',
					'_billing_company',
					'_billing_address_1',
					'_billing_address_2',
					'_billing_city',
					'_billing_postcode',
					'_billing_country',
					'_billing_state',
					'_billing_email',
					'_billing_phone',
					'_shipping_address_1',
					'_shipping_address_2',
					'_shipping_city',
					'_shipping_postcode',
					'_shipping_country',
					'_shipping_state',
				);

				$search_term = $query_vars['s'];

				// Search bookings.
				if ( is_numeric( $search_term ) ) {
					$post_ids = array( absint( $search_term ) );
				} else {
					$post_ids = array_unique(
						array_merge(
							$wpdb->get_col(
								$wpdb->prepare(
									"SELECT DISTINCT booking_meta.post_id
										FROM {$wpdb->postmeta} AS booking_meta
										INNER JOIN {$wpdb->postmeta} AS order_meta ON order_meta.post_id = booking_meta.meta_value AND booking_meta.meta_key = '_order_id'
										INNER JOIN {$wpdb->postmeta} AS order_meta2 ON order_meta2.post_id = order_meta.post_id
										WHERE
											( order_meta.meta_key = '_billing_first_name' AND order_meta2.meta_key = '_billing_last_name' AND CONCAT(order_meta.meta_value, ' ', order_meta2.meta_value) LIKE %s )
										OR
											( order_meta.meta_key = '_shipping_first_name' AND order_meta2.meta_key = '_shipping_last_name' AND CONCAT(order_meta.meta_value, ' ', order_meta2.meta_value) LIKE %s )
										OR
											( order_meta.meta_key IN ('" . implode( "','", array_map( 'esc_sql', $order_search_fields ) ) . "') AND order_meta.meta_value LIKE %s )", // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
									'%' . $wpdb->esc_like( $search_term ) . '%',
									'%' . $wpdb->esc_like( $search_term ) . '%',
									'%' . $wpdb->esc_like( $search_term ) . '%'
								)
							),
							$wpdb->get_col(
								$wpdb->prepare(
									"SELECT DISTINCT booking_meta.post_id
										FROM {$wpdb->postmeta} AS booking_meta
										INNER JOIN {$wpdb->users} AS user_data ON user_data.ID = booking_meta.meta_value AND booking_meta.meta_key = '_user_id'
										WHERE
											( user_data.user_login LIKE %s )
										OR
											( user_data.user_nicename LIKE %s )
										OR
											( user_data.user_email LIKE %s )
										OR
											( user_data.display_name LIKE %s )
									",
									'%' . $wpdb->esc_like( $search_term ) . '%',
									'%' . $wpdb->esc_like( $search_term ) . '%',
									'%' . $wpdb->esc_like( $search_term ) . '%',
									'%' . $wpdb->esc_like( $search_term ) . '%'
								)
							)
						)
					);
				}

				$post_ids = apply_filters( 'yith_wcbk_search_booking_post_ids', $post_ids, $query_vars );

				if ( is_array( $post_ids ) ) {
					// Remove s - we don't want to search booking name.
					unset( $query_vars['s'] );

					// so we know we're doing this.
					$query_vars['booking_search'] = true;

					// Search by found posts.
					$query_vars['post__in'] = array_merge( $post_ids, array( 0 ) );
				}
			}

			$meta_query = ! empty( $query_vars['meta_query'] ) ? $query_vars['meta_query'] : array();
			$date_from  = ! empty( $_REQUEST['date_from'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['date_from'] ) ) : false;
			$date_to    = ! empty( $_REQUEST['date_to'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['date_to'] ) ) : false;
			$changed    = false;

			if ( $date_from ) {
				$changed      = true;
				$meta_query[] = array(
					'key'     => '_from',
					'value'   => strtotime( $date_from ),
					'compare' => '>=',
				);
			}

			if ( $date_to ) {
				$changed      = true;
				$meta_query[] = array(
					'key'     => '_to',
					'value'   => strtotime( $date_to ),
					'compare' => '<=',
				);
			}

			if ( $changed ) {
				$query_vars['meta_query'] = $meta_query;
			}

			if ( isset( $query_vars['orderby'] ) ) {
				$order_by = strtolower( $query_vars['orderby'] );

				switch ( $order_by ) {
					case 'booking_id':
						$query_vars['orderby'] = 'ID';
						break;
					case 'order_id':
						$query_vars['meta_key'] = '_order_id';
						$query_vars['orderby']  = 'meta_value_num';
						break;
					case 'from':
						$query_vars['meta_key'] = '_from';
						$query_vars['orderby']  = 'meta_value_num';
						break;
					case 'to':
						$query_vars['meta_key'] = '_to';
						$query_vars['orderby']  = 'meta_value_num';
						break;
					case 'persons':
						$query_vars['meta_key'] = '_persons';
						$query_vars['orderby']  = 'meta_value_num';
						break;

				}
			}

			return apply_filters( 'yith_wcbk_admin_query_filters_vars', $query_vars );
			// phpcs:enable
		}

		/**
		 * Render Booking column
		 */
		protected function render_booking_column() {
			$booking_id = $this->object->get_id();
			$edit_url   = get_edit_post_link( $booking_id );

			echo '<a href="' . esc_url( $edit_url ) . '"><strong>#' . esc_html( $booking_id ) . '</strong></a>';
			echo wp_kses_post( $this->object->get_time_to_start_html() );
		}

		/**
		 * Render Listing column
		 */
		protected function render_listing_column() {
			$product = $this->object->get_product();
			if ( $product ) {
				$edit_url = get_edit_post_link( $product->get_id() );

				echo '<a href="' . esc_url( $edit_url ) . '">' . esc_html( $product->get_title() ) . '</a>';
			}
		}

		/**
		 * Render Order column
		 */
		protected function render_order_column() {
			yith_wcbk_admin_order_info_html( $this->object );
		}

		/**
		 * Render User column
		 */
		protected function render_user_column() {
			yith_wcbk_admin_user_info_html( $this->object );
		}

		/**
		 * Render From column
		 */
		protected function render_from_column() {
			echo esc_html( $this->object->get_formatted_from() );
		}

		/**
		 * Render To column
		 */
		protected function render_to_column() {
			echo esc_html( $this->object->get_formatted_to() );
		}

		/**
		 * Render Duration column
		 */
		protected function render_duration_column() {
			echo esc_html( $this->object->get_duration_html() );
		}

		/**
		 * Render people column
		 */
		protected function render_people_column() {
			if ( $this->object->has_persons() ) {
				$person_types_html = $this->object->get_person_types_html();
				echo '<span class="tips" data-tip="' . esc_attr( $person_types_html ) . '">' . esc_html( $this->object->get_persons() ) . '</span>';
			} else {
				echo '&ndash;';
			}
		}

		/**
		 * Render Amount column
		 */
		protected function render_amount_column() {
			$booking = $this->object;

			$amount = $booking->get_sold_price( true );
			if ( false === $amount && apply_filters( 'yith_wcbk_admin_booking_show_calculated_amount', $booking->has_status( array( 'pending-confirm', 'confirmed' ) ), $booking ) ) {
				$amount = $booking->get_calculated_price();
				if ( false !== $amount ) {
					$amount = wc_get_price_including_tax( $booking->get_product(), array( 'price' => $amount ) );
				}
			}

			$amount = false !== $amount ? wc_price( $amount ) : '&ndash;';

			echo wp_kses_post( $amount );
		}

		/**
		 * Render Amount column
		 */
		protected function render_status_column() {
			$booking     = $this->object;
			$status      = $booking->get_status();
			$status_text = $booking->get_status_text();
			$actions     = array();

			echo '<span class="yith-booking-status ' . esc_attr( $status ) . '">' . esc_html( $status_text ) . '</span>';

			if ( $booking->has_status( 'unpaid' ) ) {
				$actions['paid'] = array(
					'action' => 'paid',
					'title'  => _x( 'Set as paid', 'Booking status action', 'yith-booking-for-woocommerce' ),
					'url'    => $booking->get_mark_action_url( 'paid' ),
					'icon'   => 'cash',
				);
			} elseif ( $booking->has_status( 'paid' ) && apply_filters( 'yith_wcbk_admin_booking_status_actions_show_complete_action_if_paid', false, $booking ) ) {
				$actions['completed'] = array(
					'action' => 'completed',
					'title'  => _x( 'Complete', 'Booking status action', 'yith-booking-for-woocommerce' ),
					'url'    => $booking->get_mark_action_url( 'completed' ),
					'icon'   => 'check-alt',
				);
			} elseif ( $booking->has_status( 'pending-confirm' ) ) {
				$actions['confirmed']   = array(
					'action' => 'confirmed',
					'title'  => _x( 'Confirm', 'Booking status action', 'yith-booking-for-woocommerce' ),
					'url'    => $booking->get_mark_action_url( 'confirmed' ),
					'icon'   => 'check-alt',
				);
				$actions['unconfirmed'] = array(
					'action' => 'unconfirmed',
					'title'  => _x( 'Reject', 'Booking status action', 'yith-booking-for-woocommerce' ),
					'url'    => $booking->get_mark_action_url( 'unconfirmed' ),
					'icon'   => 'close-alt',
				);
			}

			$actions = apply_filters( 'yith_wcbk_admin_booking_status_actions', $actions, $booking );
			foreach ( $actions as $action ) {
				$action['type'] = 'action-button';
				yith_plugin_fw_get_component( $action );
			}
		}

		/**
		 * Render Booking Date column
		 */
		protected function render_booking_date_column() {
			$timestamp = $this->object->get_date_created() ? $this->object->get_date_created()->getTimestamp() : '';

			if ( ! $timestamp ) {
				echo '&ndash;';

				return;
			}

			$date_time_format = sprintf( '%s %s', wc_date_format(), wc_time_format() );
			$date_format      = wc_date_format();

			if ( $timestamp > strtotime( '-1 day', time() ) && $timestamp <= time() ) {
				$show_date = sprintf(
				// translators: %s: human-readable time difference.
					__( '%s ago', 'yith-booking-for-woocommerce' ),
					human_time_diff( $this->object->get_date_created()->getTimestamp(), time() )
				);
			} else {
				$show_date = $this->object->get_date_created()->date_i18n( $date_format );
			}
			printf(
				'<time datetime="%1$s" title="%2$s">%3$s</time>',
				esc_attr( $this->object->get_date_created()->date( 'c' ) ),
				esc_html( $this->object->get_date_created()->date_i18n( $date_time_format ) ),
				esc_html( $show_date )
			);
		}

		/**
		 * Render Actions column
		 */
		protected function render_actions_column() {
			$booking = $this->object;
			$title   = $booking->get_title();

			$options = array(
				'delete-directly'        => true,
				'more-menu'              => array(
					'download-admin-pdf'    => array(
						'name' => __( 'Admin PDF', 'yith-booking-for-woocommerce' ),
						'url'  => $booking->get_pdf_url( 'admin' ),
					),
					'download-customer-pdf' => array(
						'name' => __( 'Customer PDF', 'yith-booking-for-woocommerce' ),
						'url'  => $booking->get_pdf_url( 'customer' ),
					),
				),
				// translators: %s is the title of the booking including its ID (ex: #123 Amsterdam Room).
				'confirm-delete-message' => sprintf( __( 'Are you sure you want to delete the booking "%s"?', 'yith-booking-for-woocommerce' ), '<strong>' . $title . '</strong>' ) . '<br /><br />' . __( 'This action cannot be undone and you will be not able to recover this data.', 'yith-booking-for-woocommerce' ),
			);

			$actions = yith_plugin_fw_get_default_post_actions( $this->object->get_id(), $options );

			yith_plugin_fw_get_action_buttons( $actions, true );
		}

		/**
		 * Handle Mark Booking Status Actions
		 */
		public function handle_mark_booking_status_action() {
			$status           = isset( $_REQUEST['status'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['status'] ) ) : false;
			$booking_id       = isset( $_REQUEST['booking_id'] ) ? absint( $_REQUEST['booking_id'] ) : false;
			$default_redirect = wp_get_referer() ? wp_get_referer() : admin_url( 'edit.php?post_type=' . YITH_WCBK_Post_Types::BOOKING );
			$source           = isset( $_REQUEST['source'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['source'] ) ) : '';
			$is_email_action  = 'email' === $source && in_array( $status, array( 'confirmed', 'unconfirmed' ), true );

			if ( $is_email_action ) {
				$default_redirect = add_query_arg(
					array(
						'post'   => $booking_id,
						'action' => 'edit',
					),
					admin_url( 'post.php' )
				);
			}

			$redirect = isset( $_REQUEST['redirect'] ) ? esc_url_raw( wp_unslash( $_REQUEST['redirect'] ) ) : $default_redirect;

			// Email actions (confirm, reject) don't require nonce check, since the nonce is related to the user ID (that creates the booking).
			if ( $is_email_action || ( isset( $_REQUEST['_wpnonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['_wpnonce'] ) ), 'mark-booking-status-' . $status . '-' . $booking_id ) ) ) {
				if ( ! ! $booking_id && current_user_can( 'edit_' . YITH_WCBK_Post_Types::BOOKING, $booking_id ) ) {
					$allowed_statuses = yith_wcbk_get_mark_action_allowed_booking_statuses();

					if ( yith_wcbk_is_a_booking_status( $status ) && in_array( $status, $allowed_statuses, true ) ) {
						$booking = yith_get_booking( $booking_id );
						if ( $booking ) {
							$booking->update_status( $status );
						}
					}
				}
			}
			wp_safe_redirect( $redirect );
		}

		/**
		 * Handle Generate PDF Actions
		 */
		public function handle_generate_pdf_action() {
			$booking_id = isset( $_REQUEST['booking_id'] ) ? absint( $_REQUEST['booking_id'] ) : false;
			$pdf_type   = isset( $_REQUEST['pdf_type'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['pdf_type'] ) ) : 'customer';
			if ( isset( $_REQUEST['_wpnonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['_wpnonce'] ) ), "generate-pdf-{$pdf_type}-{$booking_id}" ) ) {
				yith_wcbk()->exporter->generate_pdf( $booking_id, 'admin' === $pdf_type );

				return;
			}
			wp_safe_redirect( wp_get_referer() ? wp_get_referer() : admin_url( 'edit.php?post_type=' . YITH_WCBK_Post_Types::BOOKING ) );
		}

		/**
		 * Add create button in WP List
		 *
		 * @param string $which The location of the extra table nav markup: 'top' or 'bottom'.
		 *
		 * @since 3.0.0
		 */
		public function add_button_after_wp_list_title( $which ) {
			if ( 'top' === $which && current_user_can( 'yith_create_booking' ) ) {
				?>
				<span id="yith-wcbk-create-booking" class="yith-wcbk-create-booking yith-plugin-fw__button yith-plugin-fw__button--primary">
					<?php echo esc_html_x( 'Create booking', 'Button text', 'yith-booking-for-woocommerce' ); ?>
				</span>
				<script type="text/javascript">
					( function ( $ ) {
						$( 'h1.wp-heading-inline' ).after( $( '#yith-wcbk-create-booking' ) );
					} )( jQuery );
				</script>
				<?php
			}
		}

		/**
		 * Filter Views
		 *
		 * @param array $views The views.
		 *
		 * @return array
		 * @since 3.0.0
		 */
		public function filter_views( $views ) {
			// phpcs:disable WordPress.Security.NonceVerification.Recommended
			if ( isset( $views['mine'] ) ) {
				unset( $views['mine'] );
			}

			$num_posts   = wp_count_posts( $this->post_type, 'readable' );
			$total_posts = array_sum( (array) $num_posts );

			$list_url = add_query_arg( array( 'post_type' => $this->post_type ), admin_url( 'edit.php' ) );

			$upcoming_url   = $list_url;
			$all_url        = add_query_arg( array( 'all_posts' => 1 ), $list_url );
			$upcoming_attrs = $this->is_upcoming_view() ? 'class="current" aria-current="page"' : '';
			$all_attrs      = isset( $_GET['all_posts'] ) ? 'class="current" aria-current="page"' : '';

			if ( isset( $views['all'] ) ) {
				unset( $views['all'] );
			}

			$new = array(
				'upcoming' => "<a href='{$upcoming_url}' {$upcoming_attrs}>" . esc_html__( 'Upcoming', 'yith-booking-for-woocommerce' ) . '</a>',
				'all'      => "<a href='{$all_url}' {$all_attrs}>" . esc_html__( 'All', 'yith-booking-for-woocommerce' ) . ' <span class="count">(' . esc_html( number_format_i18n( $total_posts ) ) . ')</span></a>',
			);

			// phpcs:enable
			return array_merge( $new, $views );
		}

		/**
		 * Render individual columns.
		 *
		 * @param string $column  Column ID to render.
		 * @param int    $post_id Post ID being shown.
		 */
		public function render_columns( $column, $post_id ) {
			parent::render_columns( $column, $post_id );

			if ( $this->object ) {
				do_action( 'yith_wcbk_booking_render_custom_columns', $column, $post_id, $this->object );
			}
		}

		/**
		 * Return true if this is the "upcoming" view.
		 *
		 * @return bool
		 */
		private function is_upcoming_view() {
			static $is_upcoming = null;
			if ( is_null( $is_upcoming ) ) {
				// phpcs:ignore WordPress.Security.NonceVerification.Recommended
				$is_upcoming = ! isset( $_GET['post_status'] ) && ! isset( $_GET['all_posts'] ) && ! isset( $_GET['orderby'] ) && ! isset( $_GET['order'] );
				if ( $is_upcoming ) {
					// Coherent sorting in the "Upcoming" view.
					$_GET['orderby'] = 'from';
					$_GET['order']   = 'ASC';
				}
			}

			return apply_filters( 'yith_wcbk_is_upcoming_view', $is_upcoming );
		}

		/**
		 * Maybe render blank state for no-bookings, Upcoming view and for empty table when searching.
		 *
		 * @param string $which String which table-nav is being shown.
		 */
		public function maybe_render_blank_state( $which ) {
			global $post_type, $wp_query;

			if ( $this->get_blank_state_params() && $post_type === $this->post_type && 'bottom' === $which ) {
				$counts = (array) wp_count_posts( $post_type );
				unset( $counts['auto-draft'] );
				$count = array_sum( $counts );

				$show_views       = false;
				$show_filters     = false;
				$show_blank_state = false;

				if ( $count <= 0 ) {
					$show_blank_state = true;
					$this->render_blank_state();

				} elseif ( $wp_query && $wp_query->post_count <= 0 ) {
					$show_blank_state = true;
					$show_views       = true;
					$show_filters     = true;
					$component        = array(
						'type'     => 'list-table-blank-state',
						'icon_url' => YITH_WCBK_ASSETS_URL . '/images/empty-calendar.svg',
						'message'  => __( 'No bookings found for the selected filters!', 'yith-booking-for-woocommerce' ),
					);

					if ( $this->is_upcoming_view() ) {
						$show_filters         = false;
						$component['message'] = implode(
							'<br />',
							array(
								__( 'You have no new bookings for now.', 'yith-booking-for-woocommerce' ),
								__( 'Don\'t worry, upcoming bookings will appear here!', 'yith-booking-for-woocommerce' ),
							)
						);
					}

					yith_plugin_fw_get_component( $component, true );
				}

				if ( $show_blank_state ) {
					$css = '#posts-filter .wp-list-table, .tablenav.bottom > * { display : none; } #posts-filter .tablenav.bottom { height  : auto; display : block }';
					if ( ! $show_views ) {
						$css .= '.wrap .subsubsub { display : none; }';
					}

					if ( ! $show_filters ) {
						$css .= '#posts-filter .tablenav.top { display : none; }';
					}
					echo '<style type="text/css">' . $css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				}
			}
		}
	}
}

return YITH_WCBK_Booking_Post_Type_Admin::instance();
