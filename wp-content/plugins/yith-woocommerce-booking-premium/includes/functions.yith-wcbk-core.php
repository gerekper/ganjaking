<?php
/**
 * Core Functions
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking\Functions
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! function_exists( 'yith_wcbk_get_duration_units' ) ) {
	/**
	 * Retrieve duration units
	 *
	 * @param int $plural_control Plural flag.
	 *
	 * @return array
	 */
	function yith_wcbk_get_duration_units( $plural_control = 1 ) {
		$duration_units = array(
			'month'  => _n( 'month', 'months', $plural_control, 'yith-booking-for-woocommerce' ),
			'day'    => _n( 'day', 'days', $plural_control, 'yith-booking-for-woocommerce' ),
			'hour'   => _n( 'hour', 'hours', $plural_control, 'yith-booking-for-woocommerce' ),
			'minute' => _n( 'minute', 'minutes', $plural_control, 'yith-booking-for-woocommerce' ),
		);

		return apply_filters( 'yith_wcbk_get_duration_units', $duration_units, $plural_control );
	}
}

if ( ! function_exists( 'yith_wcbk_get_cancel_duration_units' ) ) {
	/**
	 * Get the available cancellation units for duration
	 *
	 * @return array
	 * @since 3.0.0
	 */
	function yith_wcbk_get_cancel_duration_units() {
		$duration_units = array(
			'day'   => __( 'Day(s)', 'yith-booking-for-woocommerce' ),
			'month' => __( 'Month(s)', 'yith-booking-for-woocommerce' ),
		);

		return apply_filters( 'yith_wcbk_get_cancel_duration_units', $duration_units );
	}
}

if ( ! function_exists( 'yith_wcbk_booking_admin_screen_ids' ) ) {

	/**
	 * Return booking admin screen ids.
	 * Useful to enqueue correct styles/scripts in Booking's pages.
	 *
	 * @return array
	 */
	function yith_wcbk_booking_admin_screen_ids(): array {
		$screen_ids = array(
			'product',
			'edit-product',
		);

		/**
		 * FILTER: yith_wcbk_booking_admin_screen_ids.
		 *
		 * @see YITH_WCBK_Post_Type_Admin::add_booking_admin_screen_ids
		 * @see YITH_WCBK_Service_Tax_Admin::add_booking_admin_screen_ids
		 */
		return apply_filters( 'yith_wcbk_booking_admin_screen_ids', $screen_ids );
	}
}

if ( ! function_exists( 'yith_wcbk_get_minimum_minute_increment' ) ) {
	/**
	 * Get the minimum minute increment: default 15
	 *
	 * @return string
	 * @since 2.0.5
	 */
	function yith_wcbk_get_minimum_minute_increment() {
		return apply_filters( 'yith_wcbk_get_minimum_minute_increment', 15 );
	}
}

if ( ! function_exists( 'yith_wcbk_get_max_months_to_load' ) ) {
	/**
	 * Get max month to load.
	 *
	 * @param string $unit The unit.
	 *
	 * @return mixed|void
	 */
	function yith_wcbk_get_max_months_to_load( $unit = 'day' ) {
		$months_to_load = 12;
		if ( 'hour' === $unit ) {
			$months_to_load = 3;
		} elseif ( 'minute' === $unit ) {
			$months_to_load = 1;
		}

		return apply_filters( 'yith_wcbk_get_max_months_to_load', $months_to_load, $unit );
	}
}

if ( ! function_exists( 'yith_wcbk_array_add' ) ) {
	/**
	 * Add key and value after a specific key in array
	 *
	 * @param array  $array  The array.
	 * @param string $search The key to search for.
	 * @param string $key    The key to add.
	 * @param mixed  $value  The value to add.
	 * @param bool   $after  The value to add.
	 */
	function yith_wcbk_array_add( &$array, $search, $key, $value, $after = true ) {
		$position = array_search( $search, array_keys( $array ), true );
		if ( false !== $position ) {
			$position = $after ? $position + 1 : $position;
			$first    = array_slice( $array, 0, $position, true );
			$current  = array( $key => $value );
			$last     = array_slice( $array, $position, count( $array ), true );
			$array    = array_merge( $first, $current, $last );
		} else {
			$array = array_merge( $array, array( $key => $value ) );
		}
	}
}

if ( ! function_exists( 'yith_wcbk_array_add_after' ) ) {
	/**
	 * Add key and value after a specific key in array
	 *
	 * @param array  $array  The array.
	 * @param string $search The key to search for.
	 * @param string $key    The key to add.
	 * @param mixed  $value  The value to add.
	 */
	function yith_wcbk_array_add_after( &$array, $search, $key, $value ) {
		yith_wcbk_array_add( $array, $search, $key, $value, true );
	}
}

if ( ! function_exists( 'yith_wcbk_array_add_before' ) ) {
	/**
	 * Add key and value after a specific key in array.
	 *
	 * @param array  $array  The array.
	 * @param string $search The key to search for.
	 * @param string $key    The key to add.
	 * @param mixed  $value  The value to add.
	 */
	function yith_wcbk_array_add_before( &$array, $search, $key, $value ) {
		yith_wcbk_array_add( $array, $search, $key, $value, false );
	}
}

if ( ! function_exists( 'yith_wcbk_booking_person_types_to_list' ) ) {
	/**
	 * Transform person types array to list.
	 *
	 * @param array $person_types Person types array.
	 *
	 * @return array
	 */
	function yith_wcbk_booking_person_types_to_list( $person_types ) {
		if ( $person_types && is_array( $person_types ) && yith_wcbk_is_people_module_active() ) {
			$new_person_types = array();
			$is_a_list        = is_array( current( $person_types ) );

			if ( ! $is_a_list ) {
				foreach ( $person_types as $person_type_id => $person_type_number ) {
					$person_type_title  = get_the_title( $person_type_id );
					$new_person_types[] = array(
						'id'     => $person_type_id,
						'title'  => $person_type_title,
						'number' => $person_type_number,
					);
				}
			} else {
				$new_person_types = $person_types;
			}

			return $new_person_types;
		}

		return array();
	}
}

if ( ! function_exists( 'yith_wcbk_booking_person_types_to_id_number_array' ) ) {
	/**
	 * Transform person types to id-number array.
	 *
	 * @param array $person_types Person types array.
	 *
	 * @return array
	 */
	function yith_wcbk_booking_person_types_to_id_number_array( $person_types ) {
		if ( $person_types && is_array( $person_types ) && yith_wcbk_is_people_module_active() ) {
			$new_person_types      = array();
			$is_an_id_number_array = ! is_array( current( $person_types ) );

			if ( ! $is_an_id_number_array ) {
				foreach ( $person_types as $person_type ) {
					$new_person_types[ $person_type['id'] ] = $person_type['number'];
				}
			} else {
				$new_person_types = $person_types;
			}

			return $new_person_types;
		}

		return array();
	}
}


if ( ! function_exists( 'yith_wcbk_get_person_type_title' ) ) {
	/**
	 * Get person type title.
	 *
	 * @param int $person_type_id Person type ID.
	 *
	 * @return string
	 */
	function yith_wcbk_get_person_type_title( $person_type_id ) {
		$helper = yith_wcbk()->person_type_helper();

		return ! ! $helper ? $helper->get_person_type_title( $person_type_id ) : get_the_title( $person_type_id );
	}
}

/**
 * Conditionals
 * --------------------------------------------------
 */
if ( ! function_exists( 'yith_wcbk_is_debug' ) ) {
	/**
	 * Return true if debug is active
	 *
	 * @return bool
	 */
	function yith_wcbk_is_debug() {
		return 'yes' === get_option( 'yith-wcbk-debug', 'no' );
	}
}

if ( ! function_exists( 'yith_wcbk_is_in_search_form_result' ) ) {
	/**
	 * Return true if we're in search form results.
	 *
	 * @return bool
	 */
	function yith_wcbk_is_in_search_form_result() {
		return defined( 'YITH_WCBK_IS_IN_AJAX_SEARCH_FORM_RESULTS' ) && YITH_WCBK_IS_IN_AJAX_SEARCH_FORM_RESULTS;
	}
}


/**
 * Print fields and templates functions
 * --------------------------------------------------
 */
if ( ! function_exists( 'yith_wcbk_print_field' ) ) {
	/**
	 * Print field.
	 *
	 * @param array $args Arguments.
	 * @param bool  $echo Echo flag.
	 *
	 * @return false|string
	 */
	function yith_wcbk_print_field( $args = array(), $echo = true ) {
		if ( ! $echo ) {
			ob_start();
		}

		yith_wcbk_printer()->print_field( $args );

		if ( ! $echo ) {
			return ob_get_clean();
		}

		return '';
	}
}

if ( ! function_exists( 'yith_wcbk_print_svg' ) ) {
	/**
	 * Print an svg.
	 *
	 * @param string $svg  The SVG name.
	 * @param bool   $echo Echo flag.
	 *
	 * @return false|string
	 */
	function yith_wcbk_print_svg( $svg, $echo = true ) {
		return yith_wcbk_print_field(
			array(
				'type' => 'svg',
				'svg'  => $svg,
			),
			$echo
		);
	}
}

if ( ! function_exists( 'yith_wcbk_print_fields' ) ) {
	/**
	 * Print fields
	 *
	 * @param array $fields Fields.
	 */
	function yith_wcbk_print_fields( $fields = array() ) {
		yith_wcbk_printer()->print_fields( $fields );
	}
}

if ( ! function_exists( 'yith_wcbk_print_notice' ) ) {
	/**
	 * Print notice
	 *
	 * @param string $notice      The notice.
	 * @param string $type        Type.
	 * @param false  $dismissible Dismissible flag.
	 * @param string $key         The key.
	 */
	function yith_wcbk_print_notice( $notice, $type = 'info', $dismissible = false, $key = '' ) {
		if ( ! $key ) {
			$key = md5( $notice . '_' . $type );
		}
		$key    = sanitize_key( $key );
		$cookie = 'yith_wcbk_notice_dismiss_' . $key;
		$id     = 'yith-wcbk-notice-' . $key;

		if ( $dismissible && ! empty( $_COOKIE[ $cookie ] ) ) {
			return;
		}

		yith_plugin_fw_get_component(
			array(
				'id'          => $id,
				'type'        => 'notice',
				'notice_type' => $type,
				'message'     => $notice,
				'inline'      => false,
				'dismissible' => $dismissible,
			),
			true
		);

		if ( $dismissible ) {
			?>
			<script>
				jQuery( '#<?php echo esc_attr( $id ); ?>' ).on( 'click', '.yith-plugin-fw__notice__dismiss', function () {
					var expires     = ( new Date( Date.now() + ( 15 * 60 * 1000 ) ) ).toUTCString();
					document.cookie = "<?php echo esc_attr( $cookie ); ?>=1; expires=" + expires + ";";
				} );
			</script>
			<?php
		}
	}
}

if ( ! function_exists( 'yith_wcbk_get_view' ) ) {
	/**
	 * Print a view
	 *
	 * @param string $view The view.
	 * @param array  $args Arguments.
	 */
	function yith_wcbk_get_view( string $view, array $args = array() ) {
		$view_path = trailingslashit( YITH_WCBK_VIEWS_PATH ) . $view;
		extract( $args ); // phpcs:ignore WordPress.PHP.DontExtract.extract_extract
		if ( file_exists( $view_path ) ) {
			include $view_path;
		}
	}
}

if ( ! function_exists( 'yith_wcbk_get_view_html' ) ) {
	/**
	 * Like yith_wcbk_get_view, but returns the HTML instead of outputting.
	 *
	 * @param string $view The view.
	 * @param array  $args Arguments.
	 *
	 * @since 4.0.0
	 */
	function yith_wcbk_get_view_html( string $view, array $args = array() ) {
		ob_start();
		yith_wcbk_get_view( $view, $args );

		return ob_get_clean();
	}
}

if ( ! function_exists( 'yith_wcbk_get_template' ) ) {
	/**
	 * Print a template
	 *
	 * @param string $template The template.
	 * @param array  $args     Arguments.
	 *
	 * @since 4.0.0
	 */
	function yith_wcbk_get_template( string $template, array $args = array() ) {
		wc_get_template( $template, $args, '', YITH_WCBK_TEMPLATE_PATH );
	}
}

if ( ! function_exists( 'yith_wcbk_print_login_form' ) ) {
	/**
	 * Print the WooCommerce login form.
	 *
	 * @param bool $check_logged_in           Check logged-in flag.
	 * @param bool $add_woocommerce_container Add WooCommerce container flag.
	 *
	 * @since 1.0.5
	 */
	function yith_wcbk_print_login_form( $check_logged_in = false, $add_woocommerce_container = true ) {
		if ( ! $check_logged_in || ! is_user_logged_in() ) {
			echo ! ! $add_woocommerce_container ? '<div class="woocommerce">' : '';
			wc_get_template( 'myaccount/form-login.php' );
			echo ! ! $add_woocommerce_container ? '</div>' : '';
		}
	}
}

if ( ! function_exists( 'yith_wcbk_create_date_field' ) ) {
	/**
	 * Create date field with time.
	 *
	 * @param string $unit The unit.
	 * @param array  $args The arguments.
	 * @param bool   $echo Set true to print the field directly.
	 *
	 * @return string
	 * @since 2.0.0
	 */
	function yith_wcbk_create_date_field( $unit, $args = array(), $echo = false ) {
		$value = $args['value'] ?? '';
		$id    = $args['id'] ?? '';
		$name  = $args['name'] ?? '';
		$admin = ! ! ( $args['admin'] ?? true );

		$datepicker_class = $admin ? 'yith-wcbk-admin-date-picker' : 'yith-wcbk-date-picker';

		if ( ! in_array( $unit, array( 'hour', 'minute' ), true ) ) {
			$current_value = date_i18n( 'Y-m-d', $value );
			$field         = '<input type="text" class="' . esc_attr( $datepicker_class ) . '" id="' . esc_attr( $id ) . '" name="' . esc_attr( $name ) . '" maxlength="10" value="' . esc_attr( $current_value ) . '" pattern="[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])"/>';
		} else {
			$current_value = date_i18n( 'Y-m-d H:i', $value );
			$date_value    = date_i18n( 'Y-m-d', $value );
			$time_value    = date_i18n( 'H:i', $value );

			$time_field = yith_wcbk_print_field(
				array(
					'id'    => "$id-time",
					'type'  => 'time-select',
					'value' => $time_value,
				),
				false
			);
			$field      = '<input type="hidden" class="yith-wcbk-date-time-field" name="' . esc_attr( $name ) . '" data-date="#' . esc_attr( $id ) . '-date" data-time="#' . esc_attr( $id ) . '-time" value="' . esc_attr( $current_value ) . '" />';

			$field .= '<input type="text" class="' . esc_attr( $datepicker_class ) . '" id="' . esc_attr( $id ) . '-date"  maxlength="10" value="' . esc_attr( $date_value ) . '" pattern="[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])"/>';
			$field .= "<span class='yith-wcbk-date-time-field-time'>{$time_field}</span>";
		}

		if ( $echo ) {
			echo $field; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		return $field;
	}
}

if ( ! function_exists( 'yith_wcbk_get_order_awaiting_payment' ) ) {
	/**
	 * Get the id of the order awaiting payment.
	 *
	 * @return int
	 */
	function yith_wcbk_get_order_awaiting_payment() {
		$cart = WC()->cart->get_cart_for_session();
		if ( $cart ) {
			$order_id  = absint( WC()->session->get( 'order_awaiting_payment' ) );
			$cart_hash = WC()->cart->get_cart_hash();
			$order     = $order_id ? wc_get_order( $order_id ) : null;

			$resuming_order = $order && $order->has_cart_hash( $cart_hash ) && $order->has_status( array( 'pending', 'failed' ) );

			if ( $resuming_order ) {
				return $order_id;
			}
		}

		return 0;
	}
}

if ( ! function_exists( 'yith_wcbk_admin_order_info_html' ) ) {
	/**
	 * Retrieve the admin order info html
	 *
	 * @param YITH_WCBK_Booking $booking The booking.
	 * @param array             $args    Array of arguments.
	 * @param bool              $echo    Set to true to print directly.
	 *
	 * @return string
	 * @since 3.0.0
	 */
	function yith_wcbk_admin_order_info_html( $booking, $args = array(), $echo = true ) {
		$html     = '';
		$order_id = $booking->get_order_id();

		$defaults = array(
			'show_email'  => true,
			'show_status' => true,
		);
		$args     = wp_parse_args( $args, $defaults );

		if ( $order_id ) {
			$order = $booking->get_order();
			if ( $order ) {
				$username_format   = '%1$s %2$s';
				$the_order_user_id = $order->get_user_id();
				$user_info         = ! empty( $the_order_user_id ) ? get_userdata( $the_order_user_id ) : false;

				if ( ! ! $user_info ) {
					$username = '<a href="user-edit.php?user_id=' . absint( $user_info->ID ) . '">';

					if ( $user_info->first_name || $user_info->last_name ) {
						$username .= esc_html( sprintf( $username_format, ucfirst( $user_info->first_name ), ucfirst( $user_info->last_name ) ) );
					} else {
						$username .= esc_html( ucfirst( $user_info->display_name ) );
					}

					$username .= '</a>';
				} else {
					if ( $order->get_billing_first_name() || $order->get_billing_last_name() ) {
						$username = trim( sprintf( $username_format, $order->get_billing_first_name(), $order->get_billing_last_name() ) );
					} else {
						$username = __( 'Guest', 'yith-booking-for-woocommerce' );
					}
				}

				// translators: 1. order number with link; 2. user name.
				$html .= sprintf( _x( '%1$s by %2$s', 'Order number by X', 'yith-booking-for-woocommerce' ), '<a href="' . admin_url( 'post.php?post=' . absint( $order_id ) . '&action=edit' ) . '" class="row-title"><strong>#' . esc_attr( $order->get_order_number() ) . '</strong></a>', $username );

				if ( $args['show_email'] && $order->get_billing_email() ) {
					$html .= '<small class="meta email"><a href="' . esc_url( 'mailto:' . $order->get_billing_email() ) . '">' . esc_html( $order->get_billing_email() ) . '</a></small>';
				}

				if ( $args['show_status'] ) {
					$html .= sprintf(
						'<mark class="order-status %1$s"><span>%2$s</span></mark>',
						esc_attr( sanitize_html_class( 'status-' . $order->get_status() ) ),
						esc_html( wc_get_order_status_name( $order->get_status() ) )
					);
				}
			} else {
				// translators: %s is the order ID.
				$html .= sprintf( _x( '#%s (deleted)', 'Deleted Order:#123 (deleted)', 'yith-booking-for-woocommerce' ), $order_id );
			}
		} else {
			$html .= '&ndash;';
		}

		if ( $echo ) {
			echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		return $html;
	}
}

if ( ! function_exists( 'yith_wcbk_get_user_name' ) ) {
	/**
	 * Retrieve the user name to display.
	 *
	 * @param WP_User $user The user.
	 *
	 * @return string
	 * @since 3.0.0
	 */
	function yith_wcbk_get_user_name( $user ) {
		$name = '';
		if ( $user ) {
			if ( $user->first_name || $user->last_name ) {
				$name = sprintf( '%1$s %2$s', ucfirst( $user->first_name ), ucfirst( $user->last_name ) );
			} else {
				$name = ucfirst( $user->display_name );
			}
		}

		return $name;
	}
}

if ( ! function_exists( 'yith_wcbk_admin_user_info_html' ) ) {
	/**
	 * Retrieve the user order info html
	 *
	 * @param YITH_WCBK_Booking $booking The booking.
	 * @param bool              $echo    Set to true to print directly.
	 *
	 * @return string
	 * @since 3.0.0
	 */
	function yith_wcbk_admin_user_info_html( $booking, $echo = true ) {
		$html = '';

		$user = $booking->get_user();
		if ( $user ) {
			$html = '<a href="user-edit.php?user_id=' . absint( $user->ID ) . '">';

			$html .= yith_wcbk_get_user_name( $user );
			$html .= '</a>';
			$html .= '<small class="meta email"><a href="' . esc_url( 'mailto:' . $user->user_email ) . '">' . esc_html( $user->user_email ) . '</a></small>';

			$html = apply_filters( 'yith_wcbk_admin_user_info_html', $html, $booking, $user );
		} else {
			$html = apply_filters( 'yith_wcbk_admin_no_user_info_html', '&ndash;', $booking );
		}

		if ( $echo ) {
			echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		return $html;
	}
}

if ( ! function_exists( 'yith_wcbk_get_current_screen_id' ) ) {
	/**
	 * Retrieve the current screen ID.
	 *
	 * @return string|false
	 * @since 3.0.0
	 */
	function yith_wcbk_get_current_screen_id() {
		$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : false;

		return ! ! $screen && is_a( $screen, 'WP_Screen' ) ? $screen->id : false;
	}
}

if ( ! function_exists( 'yith_wcbk_current_screen_is' ) ) {
	/**
	 * Return true if current screen is one of the $ids.
	 *
	 * @param string|string[] $ids The screen ID(s).
	 *
	 * @return bool
	 * @since 3.0.0
	 */
	function yith_wcbk_current_screen_is( $ids ) {
		$ids       = (array) $ids;
		$screen_id = yith_wcbk_get_current_screen_id();

		return $screen_id && in_array( $screen_id, $ids, true );
	}
}

if ( ! function_exists( 'yith_wcbk_is_admin_page' ) ) {
	/**
	 * Is admin page?
	 *
	 * @param array|string|bool $options The options.
	 *
	 * @return bool
	 * @since 4.0.0
	 */
	function yith_wcbk_is_admin_page( $options ): bool {
		$screen_id = yith_wcbk_get_current_screen_id();
		$is_page   = false;

		if ( $screen_id ) {

			if ( true === $options ) {
				$is_page = true;
			} else {
				$options = (array) $options;

				foreach ( $options as $option ) {
					$parts   = explode( '/', $option );
					$id      = $parts[0] ?? false;
					$tab     = $parts[1] ?? false;
					$sub_tab = $parts[2] ?? false;

					switch ( $id ) {
						case 'all-plugin-pages':
							$is_page = yith_wcbk_is_admin_page( array_merge( yith_wcbk_booking_admin_screen_ids(), array( 'panel' ) ) );
							break;
						case 'panel':
							if ( strpos( $screen_id, 'page_' . YITH_WCBK_Admin::PANEL_PAGE ) > 0 ) {
								if ( ! ! $tab ) {
									$is_page = isset( $_GET['tab'] ) && $_GET['tab'] === $tab; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

									if ( ! ! $sub_tab ) {
										$is_page = $is_page && isset( $_GET['sub_tab'] ) && "{$tab}-{$sub_tab}" === $_GET['sub_tab']; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
									}
								} else {
									$is_page = true;
								}
							}
							break;
						default:
							$is_page = $screen_id === $id;
							break;
					}

					if ( $is_page ) {
						break;
					}
				}
			}
		}

		return $is_page;
	}
}

if ( ! function_exists( 'yith_wcbk_get_current_admin_page' ) ) {
	/**
	 * Get the current admin page.
	 *
	 * @return string
	 * @since 5.0.0
	 */
	function yith_wcbk_get_current_admin_page(): string {
		global $plugin_page;

		if ( ! yith_wcbk_is_admin_page( 'all-plugin-pages' ) ) {
			return '';
		}

		$is_panel  = YITH_WCBK_Admin::PANEL_PAGE === $plugin_page;
		$screen_id = yith_wcbk_get_current_screen_id();

		if ( $is_panel ) {
			$tab            = sanitize_key( wp_unslash( $_GET['tab'] ?? '' ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$sub_tab        = sanitize_key( wp_unslash( $_GET['sub_tab'] ?? '' ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$sub_tab_prefix = $tab . '-';
			$sub_tab        = strpos( $sub_tab, $sub_tab_prefix ) === 0 ? substr( $sub_tab, strlen( $sub_tab_prefix ) ) : $sub_tab;

			return implode( '/', array_filter( array( 'panel', $tab, $sub_tab ) ) );
		}

		return ! ! $screen_id ? $screen_id : '';
	}
}


if ( ! function_exists( 'yith_wcbk_get_admin_calendar_url' ) ) {

	/**
	 * Get the calendar URL
	 *
	 * @param int|false $product_id The product ID. Set to false if you want to retrieve the general calendar URL.
	 *
	 * @return string
	 * @since 3.0.0
	 */
	function yith_wcbk_get_admin_calendar_url( $product_id ) {
		return YITH_WCBK_Booking_Calendar::get_url( $product_id );
	}
}

add_action( 'yith_wcbk_run_callback', 'yith_wcbk_run_callback', 10, 2 );

/**
 * Run callback.
 *
 * @param callable $callback The callback.
 * @param array    $args     Arguments.
 *
 * @since 3.0.0
 */
function yith_wcbk_run_callback( $callback, $args = array() ) {
	if ( is_callable( $callback ) ) {
		if ( ! ! $args ) {
			call_user_func_array( $callback, $args );
		} else {
			call_user_func( $callback );
		}
	}
}

if ( ! function_exists( 'yith_wcbk_number' ) ) {
	/**
	 * Format a number.
	 *
	 * @param int|float|string $number The number.
	 * @param array            $args   Arguments.
	 *
	 * @return string
	 * @since 3.0.0
	 */
	function yith_wcbk_number( $number, array $args = array() ): string {
		$args = apply_filters(
			'yith_wcbk_number_args',
			wp_parse_args(
				$args,
				array(
					'decimal_separator'  => wc_get_price_decimal_separator(),
					'thousand_separator' => '',
					'decimals'           => 2,
				)
			)
		);

		// Convert to float to avoid issues on PHP 8.
		$number   = (float) $number;
		$negative = $number < 0;

		$number = $negative ? $number * - 1 : $number;
		$number = number_format( $number, $args['decimals'], $args['decimal_separator'], $args['thousand_separator'] );

		if ( apply_filters( 'yith_wcbk_number_trim_zeros', true ) && $args['decimal_separator'] && $args['decimals'] > 0 ) {
			$number = preg_replace( '/' . preg_quote( $args['decimal_separator'], '/' ) . '0++$/', '', $number );
			$number = preg_replace( '/(' . preg_quote( $args['decimal_separator'], '/' ) . '[0-9]+)(0++)$/', '$1', $number );
		}

		$formatted_number = ( $negative ? '-' : '' ) . $number;

		return (string) apply_filters( 'yith_wcbk_number', $formatted_number, $number, $args );
	}
}

if ( ! function_exists( 'yith_wcbk_css' ) ) {
	/**
	 * Format a number.
	 *
	 * @param array $styles Styles arguments.
	 *
	 * @return string
	 * @since 3.0.0
	 */
	function yith_wcbk_css( array $styles ): string {
		$styles = array_map(
			function ( $style ) {
				$selector        = ! ! $style['selector'] ? $style['selector'] : '';
				$parents         = $style['parents'] ?? array();
				$styles          = $style['styles'] ?? array();
				$important       = $style['important'] ?? false;
				$maybe_important = ! ! $important ? ' !important' : '';

				if ( $parents ) {
					$selector = implode(
						', ',
						array_map(
							function ( $parent ) use ( $selector ) {
								return esc_attr( implode( ' ', array_filter( array( $parent, $selector ) ) ) );
							},
							$parents
						)
					);
				}

				$css = $selector . '{';

				$css_styles = array();

				foreach ( $styles as $prop => $value ) {
					$css_styles[] = esc_attr( $prop ) . ': ' . esc_attr( $value ) . $maybe_important;
				}

				$css .= implode( '; ', $css_styles );
				$css .= '}';

				return $css;
			},
			$styles
		);

		return implode( ' ', $styles );
	}
}

if ( ! function_exists( 'yith_wcbk_get_default_colors' ) ) {
	/**
	 * Get default colors.
	 *
	 * @return array
	 * @since 3.0.0
	 */
	function yith_wcbk_get_default_colors(): array {
		$defaults = array(
			'primary'            => '#00a7b7',
			'primary-light'      => '#00cbe0',
			'primary-contrast'   => '#ffffff',
			'border-color'       => '#d1d1d1',
			'border-color-focus' => '#a7d9ec',
			'shadow-color-focus' => 'rgba(167, 217, 236, .35)',
			'underlined-bg'      => '#e8eff1',
			'underlined-text'    => '#4e8ba2',
		);

		$colors = (array) apply_filters( 'yith_wcbk_default_colors', $defaults );

		// Default colors are mandatory.
		$colors = wp_parse_args( $colors, $defaults );

		return $colors;
	}
}

if ( ! function_exists( 'yith_wcbk_get_colors' ) ) {
	/**
	 * Get colors.
	 *
	 * @return array
	 * @since 3.0.0
	 */
	function yith_wcbk_get_colors(): array {
		$colors = get_option( 'yith-wcbk-colors', array() );
		$colors = ! ! $colors && is_array( $colors ) ? $colors : array();

		return wp_parse_args( $colors, yith_wcbk_get_default_colors() );
	}
}

if ( ! function_exists( 'yith_wcbk_get_capabilities' ) ) {
	/**
	 * Get capabilities.
	 *
	 * @param string $type        The type of capabilities: post | tax | single.
	 * @param string $object_type The object type: you can use the post_type name, the taxonomy name, or the single cap.
	 *
	 * @return array
	 * @since 4.0.0
	 */
	function yith_wcbk_get_capabilities( string $type, string $object_type ): array {
		$caps = array();
		switch ( $type ) {
			case 'post':
				$caps = array(
					'edit_post'              => "edit_{$object_type}",
					'delete_post'            => "delete_{$object_type}",
					'edit_posts'             => "edit_{$object_type}s",
					'edit_others_posts'      => "edit_others_{$object_type}s",
					'publish_posts'          => "publish_{$object_type}s",
					'read_private_posts'     => "read_private_{$object_type}s",
					'delete_posts'           => "delete_{$object_type}s",
					'delete_private_posts'   => "delete_private_{$object_type}s",
					'delete_published_posts' => "delete_published_{$object_type}s",
					'delete_others_posts'    => "delete_others_{$object_type}s",
					'edit_private_posts'     => "edit_private_{$object_type}s",
					'edit_published_posts'   => "edit_published_{$object_type}s",
					'create_posts'           => "create_{$object_type}s",
				);

				break;
			case 'tax':
				$caps = array(
					'manage_terms' => 'manage_' . $object_type . 's',
					'edit_terms'   => 'edit_' . $object_type . 's',
					'delete_terms' => 'delete' . $object_type . 's',
					'assign_terms' => 'assign' . $object_type . 's',
				);
				break;
			case 'single':
				$caps = array( $object_type );
		}

		return $caps;
	}
}

if ( ! function_exists( 'yith_wcbk_add_capabilities' ) ) {
	/**
	 * Add capabilities.
	 *
	 * @param array              $caps  The capabilities to add.
	 * @param string|array|false $roles The roles to add the capability. Default: admin and shop_manager.
	 *
	 * @since 4.0.0
	 */
	function yith_wcbk_add_capabilities( array $caps, $roles = false ) {
		$roles = false === $roles ? array( 'administrator', 'shop_manager' ) : (array) $roles;

		foreach ( $roles as $role ) {
			$the_role = get_role( $role );
			if ( $the_role ) {
				foreach ( $caps as $cap ) {
					$the_role->add_cap( $cap );
				}
			}
		}
	}
}

if ( ! function_exists( 'yith_wcbk_remove_capabilities' ) ) {
	/**
	 * Remove capabilities.
	 *
	 * @param array              $caps  The capabilities to remove.
	 * @param string|array|false $roles The roles to add the capability. Default: admin and shop_manager.
	 *
	 * @since 4.0.0
	 */
	function yith_wcbk_remove_capabilities( array $caps, $roles = false ) {
		$roles = false === $roles ? array( 'administrator', 'shop_manager' ) : (array) $roles;

		foreach ( $roles as $role ) {
			$the_role = get_role( $role );
			if ( $the_role ) {
				foreach ( $caps as $cap ) {
					$the_role->remove_cap( $cap );
				}
			}
		}
	}
}

if ( ! function_exists( 'yith_wcbk_array_sort' ) ) {
	/**
	 * Sort array of arrays.
	 *
	 * @param array      $array            The array.
	 * @param string|int $field            The field used for sorting.
	 * @param string|int $default_priority Default priority.
	 *
	 * @since 4.0.0
	 */
	function yith_wcbk_array_sort( array &$array, $field = 'priority', $default_priority = '' ) {
		uasort(
			$array,
			function ( $a, $b ) use ( $field, $default_priority ) {
				$a_field = $a[ $field ] ?? $default_priority;
				$b_field = $b[ $field ] ?? $default_priority;

				return $a_field <=> $b_field;
			}
		);
	}
}

if ( ! function_exists( 'yith_wcbk_get_query_string_param' ) ) {
	/**
	 * Get a query string parameter.
	 *
	 * @param string $key The key.
	 *
	 * @since 4.0.0
	 */
	function yith_wcbk_get_query_string_param( string $key ) {
		$value = isset( $_GET[ $key ] ) ? wc_clean( wp_unslash( $_GET[ $key ] ) ) : false; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( $value ) {
			if ( in_array( $key, array( 'from', 'to' ), true ) && is_numeric( $value ) ) {
				$value = gmdate( 'Y-m-d', $value );
			}
		}

		return apply_filters( 'yith_wcbk_get_query_string_param', $value, $key );
	}
}
if ( ! function_exists( 'yith_wcbk_ajax_start' ) ) {
	/**
	 * Start Booking AJAX call
	 *
	 * @param string $context The context (admin or frontend).
	 *
	 * @since 4.0.0
	 */
	function yith_wcbk_ajax_start( string $context = 'admin' ) {
		error_reporting( 0 ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.prevent_path_disclosure_error_reporting, WordPress.PHP.DiscouragedPHPFunctions.runtime_configuration_error_reporting

		! defined( 'YITH_WCBK_DOING_AJAX' ) && define( 'YITH_WCBK_DOING_AJAX', true );
		if ( 'admin' === $context ) {
			! defined( 'YITH_WCBK_DOING_AJAX_ADMIN' ) && define( 'YITH_WCBK_DOING_AJAX_ADMIN', true );
		} elseif ( 'frontend' === $context ) {
			! defined( 'YITH_WCBK_DOING_AJAX_FRONTEND' ) && define( 'YITH_WCBK_DOING_AJAX_FRONTEND', true );
		}
	}
}

if ( ! function_exists( 'yith_wcbk_filter_options' ) ) {
	/**
	 * Filter options based on active modules and versions.
	 *
	 * @param array $options The options to filter.
	 * @param array $args    Arguments.
	 *
	 * @return array
	 * @since 4.0.0
	 */
	function yith_wcbk_filter_options( array $options, array $args = array() ): array {
		$defaults = array(
			'sort'             => false,
			'default_priority' => '',
		);
		$args     = wp_parse_args( $args, $defaults );

		$sort             = $args['sort'];
		$default_priority = $args['default_priority'];
		$should_sort      = ! ! $sort;

		$module_key      = 'yith-wcbk-module';
		$version_key     = 'yith-wcbk-version';
		$min_version_key = 'yith-wcbk-min-version';
		$priority_key    = is_string( $sort ) ? $sort : 'yith-wcbk-priority';

		if ( $should_sort ) {
			yith_wcbk_array_sort( $options, $priority_key, $default_priority );
		}

		foreach ( $options as $key => $option ) {
			$required_module      = $option[ $module_key ] ?? false;
			$required_version     = $option[ $version_key ] ?? false;
			$required_min_version = $option[ $min_version_key ] ?? false;
			$available            = true;

			if ( $required_module && ! yith_wcbk_is_module_active( $required_module ) ) {
				$available = false;
			}

			if ( $available && $required_version ) {
				if ( 'premium' === $required_version && ! defined( 'YITH_WCBK_PREMIUM' ) ) {
					$available = false;
				}

				if ( 'extended' === $required_version && ! defined( 'YITH_WCBK_EXTENDED' ) ) {
					$available = false;
				}
			}

			if ( $available && $required_min_version ) {
				if ( 'premium' === $required_version && ! defined( 'YITH_WCBK_PREMIUM' ) ) {
					$available = false;
				}

				if ( 'extended' === $required_version && ! ( defined( 'YITH_WCBK_EXTENDED' ) || defined( 'YITH_WCBK_PREMIUM' ) ) ) {
					$available = false;
				}
			}

			if ( ! $available ) {
				unset( $options[ $key ] );
			} else {
				unset( $options[ $key ][ $module_key ] );
				unset( $options[ $key ][ $version_key ] );
				unset( $options[ $key ][ $min_version_key ] );
				if ( $should_sort ) {
					unset( $options[ $key ][ $priority_key ] );
				}
			}
		}

		return $options;
	}
}

if ( ! function_exists( 'yith_wcbk_is_wc_custom_orders_table_usage_enabled' ) ) {
	/**
	 * Return true if the WooCommerce custom orders table usage is enabled (HPOS).
	 *
	 * @return bool
	 * @since 4.4.0
	 */
	function yith_wcbk_is_wc_custom_orders_table_usage_enabled(): bool {
		// todo: HPOS - deprecate/remove this function when removing support for older WC versions.
		return class_exists( '\Automattic\WooCommerce\Utilities\OrderUtil' ) && is_callable( '\Automattic\WooCommerce\Utilities\OrderUtil::custom_orders_table_usage_is_enabled' ) && \Automattic\WooCommerce\Utilities\OrderUtil::custom_orders_table_usage_is_enabled();
	}
}

if ( ! function_exists( 'yith_wcbk_invalidate_product_cache' ) ) {
	/**
	 * Invalidate product cache.
	 *
	 * @since 5.0.0
	 */
	function yith_wcbk_invalidate_product_cache() {
		yith_wcbk_cache()->invalidate_product_cache();
	}
}

if ( ! function_exists( 'yith_wcbk_get_booking_form_positions' ) ) {

	/**
	 * Return the Booking form available positions.
	 *
	 * @return array
	 * @since 5.3.1
	 */
	function yith_wcbk_get_booking_form_positions(): array {
		$positions = array(
			'default'            => __( 'Default', 'yith-booking-for-woocommerce' ),
			'before_summary'     => __( 'Before summary', 'yith-booking-for-woocommerce' ),
			'after_title'        => __( 'After title', 'yith-booking-for-woocommerce' ),
			'before_description' => __( 'Before description', 'yith-booking-for-woocommerce' ),
			'after_description'  => __( 'After description', 'yith-booking-for-woocommerce' ),
			'after_summary'      => __( 'After summary', 'yith-booking-for-woocommerce' ),
			'widget'             => __( 'Use widget/block', 'yith-booking-for-woocommerce' ),
			'none'               => __( 'None', 'yith-booking-for-woocommerce' ),
		);

		if ( function_exists( 'yith_plugin_fw_wc_is_using_block_template_in_single_product' ) && yith_plugin_fw_wc_is_using_block_template_in_single_product() ) {
			$positions = array(
				'default' => __( 'Default "Add to cart" block', 'yith-booking-for-woocommerce' ),
				'widget'  => __( 'Use widget/block', 'yith-booking-for-woocommerce' ),
			);
		}

		return $positions;
	}
}
