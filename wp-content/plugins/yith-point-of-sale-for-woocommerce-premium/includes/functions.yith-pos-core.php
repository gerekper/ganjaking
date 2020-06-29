<?php

if ( ! function_exists( 'yith_pos_get_view' ) ) {
	/**
	 * print a view
	 *
	 * @param string $view
	 * @param array  $args
	 */
	function yith_pos_get_view( $view, $args = array() ) {
		$view_path = trailingslashit( YITH_POS_VIEWS_PATH ) . $view;
		extract( $args );
		if ( file_exists( $view_path ) ) {
			include $view_path;
		}
	}
}

if ( ! function_exists( 'yith_pos_is_store_wizard' ) ) {
	/**
	 * is this the Store Wizard?
	 *
	 * @return bool
	 */
	function yith_pos_is_store_wizard() {
		global $pagenow, $post, $post_type;

		return ! ! $pagenow && ! ! $post && ! ! $post_type && $post_type === YITH_POS_Post_Types::$store && ( $pagenow === 'post-new.php' || 'draft' === $post->post_status );
	}
}

if ( ! function_exists( 'yith_pos_get_employee_name' ) ) {
	/**
	 * Get the employee name
	 *
	 * @param int   $user_id
	 * @param array $options
	 *
	 * @return string
	 */
	function yith_pos_get_employee_name( $user_id, $options = array() ) {
		$defaults = array(
			'hide_nickname' => false,
		);
		$options  = wp_parse_args( $options, $defaults );

		$user_info = get_userdata( $user_id );
		if ( $user_info ) {
			if ( $user_info->first_name || $user_info->last_name ) {
				if ( $options[ 'hide_nickname' ] ) {
					$name = esc_html( sprintf( _x( '%1$s %2$s', 'full name', 'woocommerce' ), ucfirst( $user_info->first_name ), ucfirst( $user_info->last_name ) ) );
				} else {
					$name = esc_html( sprintf( _x( '%1$s %2$s (%3$s)', 'full name', 'woocommerce' ), ucfirst( $user_info->first_name ), ucfirst( $user_info->last_name ), $user_info->nickname ) );
				}
			} else {
				$name = esc_html( ucfirst( $user_info->display_name ) );
			}
		} else {
			$name = esc_html( sprintf( __( 'User #%s', 'yith-point-of-sale-for-woocommerce' ), $user_id ) );
		}

		return apply_filters( 'yith_pos_get_employee_name', $name, $user_id, $user_info );
	}
}


if ( ! function_exists( 'yith_pos_get_employees' ) ) {
	/**
	 * Get the employee list
	 *
	 * @param $role  (manager|cashier)
	 * @param $store int
	 *
	 * @return array
	 */
	function yith_pos_get_employees( $role = 'manager', $store = null ) {

		$employees = array();
		if ( is_null( $store ) ) {
			$user_query = new WP_User_Query( array( 'role' => 'yith_pos_' . $role, 'fields' => 'ID' ) );
			$employees  = $user_query->get_results();
		} else {
			$store_obj = yith_pos_get_store( $store );
			if ( $store_obj ) {
				$employees = $role == 'manager' ? $store_obj->get_managers() : $store_obj->get_cashiers();
			}
		}

		return apply_filters( 'yith_pos_get_employees', $employees, $role, $store );
	}
}

if ( ! function_exists( 'yith_pos_admin_screen_ids' ) ) {

	/**
	 * Return POS screen ids
	 *
	 * @return array
	 */
	function yith_pos_admin_screen_ids() {
		$screen_ids = array(
			'yith-plugins_page_yith_pos_panel'
		);
		$post_types = array(
			YITH_POS_Post_Types::$store,
			YITH_POS_Post_Types::$receipt,
			YITH_POS_Post_Types::$register
		);
		foreach ( $post_types as $post_type ) {
			$screen_ids[] = $post_type;
			$screen_ids[] = 'edit-' . $post_type;
		}

		return apply_filters( 'yith_pos_admin_screen_ids', $screen_ids );
	}
}

if ( ! function_exists( 'yith_pos_compact_list' ) ) {
	/**
	 * Print a compact list
	 *
	 * @param array $items
	 * @param array $args
	 */
	function yith_pos_compact_list( $items, $args = array() ) {
		$defaults          = array(
			'limit'             => 5,
			'class'             => '',
			'show_more_message' => __( 'and other %s...', 'yith-point-of-sale-for-woocommerce' ),
			'hide_more_message' => __( 'hide', 'yith-point-of-sale-for-woocommerce' ),
		);
		$args              = wp_parse_args( $args, $defaults );
		$total             = count( $items );
		$limit             = absint( $args[ 'limit' ] );
		$hidden            = max( 0, $total - $limit );
		$class             = $args[ 'class' ];
		$show_more_message = sprintf( $args[ 'show_more_message' ], $hidden );
		$hide_more_message = $args[ 'hide_more_message' ];

		echo "<div class='yith-pos-compact-list {$class}' data-total='{$total}' data-limit='{$limit}' data-show-more-message='{$show_more_message}' data-hide-more-message='{$hide_more_message}'>";
		$index = 1;

		foreach ( $items as $item ) {
			$item_class = 'yith-pos-compact-list__item';
			if ( $index === ( $limit + 1 ) ) {
				echo "<div class='yith-pos-compact-list__hidden-items'>";
			}
			echo "<div class='{$item_class}' data-index='{$index}'>{$item}</div>";
			$index ++;
		}
		if ( $hidden ) {
			echo "</div>";
			echo "<div class='clear'></div>";
			echo "<span class='yith-pos-compact-list__show-more'>{$show_more_message}<span class='yith-icon yith-icon-arrow_down'></span></span>";
			echo "<span class='yith-pos-compact-list__hide-more'>{$hide_more_message}<span class='yith-icon yith-icon-arrow_up'></span></span>";
		}
		echo "</div>";
	}
}

if ( ! function_exists( 'yith_pos_get_current_post_type' ) ) {
	/**
	 * In admin return the current post type.
	 *
	 * @return mixed|void
	 */
	function yith_pos_get_current_post_type() {
		global $pagenow;
		$post_type = '';
		if ( isset( $_POST[ 'post_type' ] ) ) {
			$post_type = $_POST[ 'post_type' ];
		} elseif ( isset( $_GET[ 'post' ] ) ) {
			$post_type = get_post_type( $_GET[ 'post' ] );
		} elseif ( 'post-new.php' === $pagenow && isset( $_GET[ 'post_type' ] ) ) {
			$post_type = $_GET[ 'post_type' ];
		}

		return apply_filters( 'yith_pos_current_post_type', $post_type );
	}
}

if ( ! function_exists( 'yith_pos_get_stores' ) ) {
	/**
	 * Return the list of stores.
	 *
	 * @param array $args
	 *
	 * @return mixed|void
	 */
	function yith_pos_get_stores( $args = array() ) {

		$defaults = array(
			'posts_per_page' => - 1,
			'offset'         => 0,
			'orderby'        => 'date',
			'order'          => 'DESC',
			'include'        => '',
			'exclude'        => '',
			'meta_key'       => '',
			'meta_value'     => '',
			'post_type'      => YITH_POS_Post_Types::$store,
			'post_status'    => 'publish',
			'fields'         => 'ids',
		);

		$args = wp_parse_args( $args, $defaults );

		$return_stores = 'stores' === $args[ 'fields' ];

		if ( $return_stores ) {
			$args[ 'fields' ] = 'ids';
		}

		$stores = get_posts( $args );

		if ( $return_stores ) {
			$stores = array_filter( array_map( 'yith_pos_get_store', $stores ) );
		}

		return apply_filters( 'yith_pos_stores', $stores );
	}
}

if ( ! function_exists( 'yith_pos_get_registers' ) ) {
	/**
	 * Return the list of registers.
	 *
	 * @param array $args
	 *
	 * @return array
	 */
	function yith_pos_get_registers( $args = array() ) {
		$defaults = array(
			'posts_per_page' => 5,
			'offset'         => 0,
			'include'        => '',
			'exclude'        => '',
			'meta_key'       => '',
			'meta_value'     => '',
			'post_type'      => YITH_POS_Post_Types::$register,
			'post_status'    => 'publish',
			'fields'         => 'ids',
		);

		$args             = wp_parse_args( $args, $defaults );
		$return_registers = 'registers' === $args[ 'fields' ];

		if ( $return_registers ) {
			$args[ 'fields' ] = 'ids';
		}

		$registers = get_posts( $args );

		if ( $return_registers ) {
			$registers = array_filter( array_map( 'yith_pos_get_register', $registers ) );
		}

		return apply_filters( 'yith_pos_get_registers', $registers );
	}
}

if ( ! function_exists( 'yith_pos_get_registers_by_store' ) ) {
	/**
	 * Return the list of registers of a specific store
	 *
	 * @param int   $store_id
	 * @param array $args
	 *
	 * @return array
	 */
	function yith_pos_get_registers_by_store( $store_id, $args = array() ) {
		$defaults = array(
			'order'          => 'ASC',
			'posts_per_page' => - 1,
			'meta_key'       => '_store_id',
			'meta_value'     => absint( $store_id ),
			'post_status'    => 'any',
			'fields'         => 'registers',
		);

		$args = wp_parse_args( $args, $defaults );

		return yith_pos_get_registers( $args );
	}
}

if ( ! function_exists( 'yith_post_rest_get_register_list' ) ) {
	/**
	 * Callback that return to the store rest api the list of registers
	 * as array ('id','name')
	 *
	 * @param $object
	 * @param $field_name
	 * @param $request
	 *
	 * @return array
	 */
	function yith_post_rest_get_register_list( $object, $field_name, $request ) {
		$registers = yith_pos_get_registers_by_store( $object[ 'id' ] );

		$register_list = array();
		if ( $registers ) {
			foreach ( $registers as $register ) {
				array_push( $register_list, array( 'id' => $register->get_id(), 'name' => $register->get_name() ) );
			}
		}

		return $register_list;
	}
}

if ( ! function_exists( 'yith_pos_get_receipts_options' ) ) {
	/**
	 * Return the list of receipts.
	 */
	function yith_pos_get_receipts_options() {
		$args = array(
			'posts_per_page' => - 1,
			'post_type'      => YITH_POS_Post_Types::$receipt
		);

		$receipts = array( '' => __( 'No receipt', 'yith-point-of-sale-for-woocommerce' ) );

		$receipts_posts = get_posts( $args );

		if ( $receipts_posts ) {
			foreach ( $receipts_posts as $receipt ) {
				$receipts[ $receipt->ID ] = $receipt->post_title;
			}
		}

		return $receipts;
	}
}

if ( ! function_exists( 'yith_pos_create_user_form' ) ) {
	/**
	 * Print a form for creating a new user
	 *
	 * @param array $args
	 * @param bool  $echo
	 *
	 * @return false|string|void
	 */
	function yith_pos_register_user_form( $args = array(), $echo = true ) {
		static $form_id = 0;
		$form_id ++;

		$defaults          = array(
			'title'               => __( 'Create new user', 'yith-point-of-sale-for-woocommerce' ),
			'button_text'         => __( 'Create new user', 'yith-point-of-sale-for-woocommerce' ),
			'button_close_text'   => __( 'Close new user creation', 'yith-point-of-sale-for-woocommerce' ),
			'save_text'           => __( 'Create user', 'yith-point-of-sale-for-woocommerce' ),
			'user_type'           => '',
			'select2_to_populate' => '',
		);
		$args              = wp_parse_args( $args, $defaults );
		$html              = '';
		$args[ 'form_id' ] = $form_id;

		if ( current_user_can( 'create_users' ) ) {
			ob_start();
			yith_pos_get_view( 'fields/create-user.php', $args );
			$html = ob_get_clean();
		}

		if ( $echo ) {
			echo $html;

			return;
		}

		return $html;
	}
}

if ( ! function_exists( 'yith_pos_get_required_field_message' ) ) {
	/**
	 * get the required message for fields
	 *
	 * @return string
	 */
	function yith_pos_get_required_field_message() {
		$message = sprintf( '<span class="yith-pos-required-field-message">%s</span>', __( 'This field is required.', 'yith-point-of-sale-for-woocommerce' ) );

		return apply_filters( 'yith_pos_get_required_message', $message );
	}
}

if ( ! function_exists( 'yith_pos_svg' ) ) {
	/**
	 * @param string $svg
	 * @param bool   $echo
	 *
	 * @return false|string|void
	 */
	function yith_pos_svg( $svg, $echo = true ) {
		$path = trailingslashit( YITH_POS_ASSETS_PATH ) . 'svg/' . $svg . '.svg';
		$html = '';

		if ( file_exists( $path ) ) {
			ob_start();
			include $path;
			$html = ob_get_clean();
		}

		if ( $echo ) {
			echo $html;

			return;
		}

		return $html;
	}
}

if ( ! function_exists( 'yith_pos_get_post_edit_link_html' ) ) {
	/**
	 * Return the Post Edit link html
	 *
	 * @param int $post_id
	 *
	 * @return string
	 */
	function yith_pos_get_post_edit_link_html( $post_id ) {
		if ( $post_id ) {
			$link = get_edit_post_link( $post_id );
			$name = get_the_title( $post_id );

			return "<a href='{$link}'>{$name}</a>";
		}

		return "";
	}
}

if ( ! function_exists( 'yith_pos_get_register_options' ) ) {
	/**
	 * Return the array of the options of the register
	 *
	 * @param YITH_POS_Register | int $register
	 *
	 * @return array
	 */
	function yith_pos_get_register_options( $register ) {
		$register = yith_pos_get_register( $register );

		return include YITH_POS_DIR . '/plugin-options/metabox/register-options.php';
	}
}

if ( ! function_exists( 'yith_pos_register_statuses' ) ) {
	/**
	 * Return the statuses for registers
	 *
	 * @return array
	 */
	function yith_pos_register_statuses() {
		$statuses = array(
			'opened' => __( 'Opened', 'yith-point-of-sale-for-woocommerce' ),
			'closed' => __( 'Closed', 'yith-point-of-sale-for-woocommerce' ),
		);

		return apply_filters( 'yith_pos_register_statuses', $statuses );
	}
}

if ( ! function_exists( 'yith_pos_get_register_status_name' ) ) {
	/**
	 * Return the name of the status
	 *
	 * @return string
	 */
	function yith_pos_get_register_status_name( $status ) {
		$statuses = yith_pos_register_statuses();

		return array_key_exists( $status, $statuses ) ? $statuses[ $status ] : '';
	}
}

if ( ! function_exists( 'yith_pos_get_register_full_name' ) ) {
	/**
	 * Return the name of the register including the name of the store
	 *
	 * @param int $register_id
	 *
	 * @return string
	 */
	function yith_pos_get_register_full_name( $register_id ) {
		$register_name = yith_pos_get_register_name( $register_id );
		$store_id      = absint( get_post_meta( $register_id, '_store_id', true ) );
		$store_name    = $store_id ? yith_pos_get_store_name( $store_id ) : '';

		if ( $store_name ) {
			$full_name = sprintf( '%s (%s)', $register_name, $store_name );
		} else {
			$full_name = $register_name;
		}

		return apply_filters( 'yith_pos_get_register_full_name', $full_name, $register_id );
	}
}


if ( ! function_exists( 'yith_pos_get_pos_page_url' ) ) {
	/**
	 * Return the URL of YITH Pos page
	 *
	 * @return string
	 * @since 1.0.0
	 */
	function yith_pos_get_pos_page_url() {
		$option_value = get_option( 'settings_pos_page' );

		if ( function_exists( 'wpml_object_id_filter' ) ) {
			global $sitepress;

			if ( ! is_null( $sitepress ) && is_callable( array( $sitepress, 'get_current_language' ) ) ) {
				$option_value = wpml_object_id_filter( $option_value, 'post', true, $sitepress->get_current_language() );
			}
		}

		$base_url = get_the_permalink( $option_value );

		return apply_filters( 'yith_pos_page_url', $base_url );
	}
}

if ( ! function_exists( 'yith_pos_get_pos_page_id' ) ) {
	/**
	 * Return the id of YITH Pos Page
	 *
	 * @return int
	 */
	function yith_pos_get_pos_page_id() {
		$page_id = get_option( 'settings_pos_page' );

		if ( function_exists( 'wpml_object_id_filter' ) ) {
			global $sitepress;

			if ( ! is_null( $sitepress ) && is_callable( array( $sitepress, 'get_current_language' ) ) ) {
				$page_id = wpml_object_id_filter( $page_id, 'post', true, $sitepress->get_current_language() );
			}
		}

		return apply_filters( 'yith_pos_page_id', $page_id );
	}
}

if ( ! function_exists( 'is_yith_pos' ) ) {

	/**
	 * is_yith_pos - Returns true when viewing the YITH Pos page.
	 *
	 * @return bool
	 */
	function is_yith_pos() {
		$page_id = yith_pos_get_pos_page_id();

		return ( $page_id && is_page( $page_id ) );
	}
}

if ( ! function_exists( 'yith_pos_maybe_add_user_role' ) ) {

	/**
	 * Add a specific role to an user if he/she doesn't have it
	 *
	 * @param int|WP_User $user
	 * @param string      $role
	 */
	function yith_pos_maybe_add_user_role( $user, $role ) {
		if ( ! is_object( $user ) ) {
			$user = get_userdata( $user );
		}
		if ( $user && $user->exists() && ! in_array( $role, $user->roles ) ) {
			$user->add_role( $role );
		}
	}
}

if ( ! function_exists( 'yith_pos_maybe_remove_user_role' ) ) {

	/**
	 * Remove a specific role to an user if he/she doesn't have it
	 *
	 * @param int|WP_User $user
	 * @param string      $role
	 */
	function yith_pos_maybe_remove_user_role( $user, $role, $current_store_id ) {
		if ( ! is_object( $user ) ) {
			$user = get_userdata( $user );
		}

		if ( $user && $user->exists() && in_array( 'yith_pos_' . $role, $user->roles ) ) {
			$stores      = yith_pos_get_stores( array( 'post_status' => array( 'publish', 'draft' ) ) );
			$remove_role = true;
			if ( $stores ) {
				foreach ( $stores as $store_id ) {
					if ( $store_id != $current_store_id ) {
						$employees = yith_pos_get_employees( $role, $store_id );

						if ( in_array( $user->ID, $employees ) ) {
							$remove_role = false;
							break;
						}
					}
				}
			}
			if ( $remove_role ) {
				$user->remove_role( 'yith_pos_' . $role );
				count( $user->roles ) == 0 && $user->add_role( 'customer' );
			}

		}
	}
}

if ( ! function_exists( 'yith_pos_get_format_address' ) ) {
	function yith_pos_get_format_address( $country ) {
		$format          = '';
		$address_formats = WC()->countries->get_address_formats();
		if ( isset( $address_formats[ $country ] ) ) {
			$format = $address_formats[ $country ];
		} elseif ( isset( $address_formats[ 'default' ] ) ) {
			$format = $address_formats[ 'default' ];
		}

		return $format;
	}
}

if ( ! function_exists( 'yith_pos_get_required_gateways' ) ) {
	/**
	 * Get the list of required gateways
	 *
	 * @return mixed|void
	 */
	function yith_pos_get_required_gateways() {

		$required_gateways = apply_filters( 'yith_pos_required_gateways', array(
			'yith_pos_cash_gateway',
			'yith_pos_chip_pin_gateway'
		) );

		return $required_gateways;
	}
}

if ( ! function_exists( 'yith_pos_get_enabled_gateways_option' ) ) {
	/**
	 * Get the list of gateways enabled
	 *
	 * @return mixed|void
	 */
	function yith_pos_get_enabled_gateways_option() {

		$pos_gateways = get_option( 'yith_pos_general_gateway_enabled' );

		if ( ! $pos_gateways ) {
			$pos_gateways = yith_pos_get_required_gateways();
			add_option( 'yith_pos_general_gateway_enabled', $pos_gateways );

		}

		return $pos_gateways;
	}
}

if ( ! function_exists( 'yith_pos_get_indexed_payment_methods' ) ) {
	/**
	 * Get the list of gateways indexed for plugin options.
	 *
	 * @param $all boolean if true all WC Gateways will be retrieved
	 *
	 * @return mixed|void
	 */
	function yith_pos_get_indexed_payment_methods( $all = false ) {

		$payment_methods = WC()->payment_gateways()->payment_gateways();
		$indexed_payment = array();

		if ( $all ) {
			foreach ( $payment_methods as $key => $gateway ) {
				$method_title            = $gateway->get_method_title() ? $gateway->get_method_title() : $gateway->get_title();
				$indexed_payment[ $key ] = $method_title;
			}
		} else {
			$pos_gateways = yith_pos_get_enabled_gateways_option();
			foreach ( $payment_methods as $key => $gateway ) {
				if ( in_array( $key, $pos_gateways ) ) {
					$method_title            = $gateway->get_method_title() ? $gateway->get_method_title() : $gateway->get_title();
					$indexed_payment[ $key ] = $method_title;
				}
			}
		}

		return $indexed_payment;
	}
}

if ( ! function_exists( 'yith_pos_get_active_payment_methods' ) ) {
	/**
	 * Get the list of gateways active for YITH POS
	 *
	 * @return mixed|void
	 */
	function yith_pos_get_active_payment_methods() {

		$pos_gateways    = yith_pos_get_enabled_gateways_option();
		$payment_methods = WC()->payment_gateways()->payment_gateways();
		$active_payments = array();

		foreach ( $payment_methods as $key => $gateway ) {
			if ( in_array( $key, $pos_gateways ) ) {
				$active_payments[ $key ] = $gateway;
			}
		}


		return $active_payments;
	}
}

if ( ! function_exists( 'yith_pos_get_order_payment_methods' ) ) {
	/**
	 * Get the payment methods as array of object.
	 *
	 * @param $order
	 *
	 * @return mixed|void
	 */
	function yith_pos_get_order_payment_methods( $order ) {

		$payment_methods = array();

		if ( $order instanceof WC_Order ) {
			$order_meta = $order->get_meta_data();

			if ( $order_meta ) {
				foreach ( $order_meta as $meta ) {
					if ( strpos( $meta->key, '_yith_pos_gateway_' ) !== false ) {
						$payment_method    = str_replace( '_yith_pos_gateway_', '', $meta->key );
						$payment_methods[] = (object) array(
							'paymentMethod' => $payment_method,
							'amount'        => $meta->value
						);
					}
				}
			}
		}

		return apply_filters( 'yith_pos_get_order_payment_methods', $payment_methods, $order );
	}
}

if ( ! function_exists( 'yith_pos_validate_hex' ) ) {
	/**
	 * Validates hex color code and returns proper value
	 *
	 * @see https://github.com/mpbzh/PHP-RGB-HSL-Converter
	 *
	 * @param $hex string  -  Format #ffffff, #fff, ffffff or fff
	 *
	 * @return string | bool
	 */
	function yith_pos_validate_hex( $hex ) {
		// Complete patterns like #ffffff or #fff
		if ( preg_match( "/^#([0-9a-fA-F]{6})$/", $hex ) || preg_match( "/^#([0-9a-fA-F]{3})$/", $hex ) ) {
			// Remove #
			$hex = substr( $hex, 1 );
		}

		// Complete patterns without # like ffffff or 000000
		if ( preg_match( "/^([0-9a-fA-F]{6})$/", $hex ) ) {
			return $hex;
		}

		// Short patterns without # like fff or 000
		if ( preg_match( "/^([0-9a-f]{3})$/", $hex ) ) {
			// Spread to 6 digits
			return substr( $hex, 0, 1 ) . substr( $hex, 0, 1 ) . substr( $hex, 1, 1 ) . substr( $hex, 1, 1 ) . substr( $hex, 2, 1 ) . substr( $hex, 2, 1 );
		}

		return false;
	}

}

if ( ! function_exists( 'yith_pos_hex2hsl' ) ) {

	/**
	 * Converts hex color code to RGB color
	 *
	 * @param $hex
	 *
	 * @return array
	 */
	function yith_pos_hex2hsl( $hex ) {
		//Validate Hex Input
		$hex = yith_pos_validate_hex( $hex );

		// Split input by color
		$hex = str_split( $hex, 2 );
		// Convert color values to value between 0 and 1
		$r = ( hexdec( $hex[ 0 ] ) ) / 255;
		$g = ( hexdec( $hex[ 1 ] ) ) / 255;
		$b = ( hexdec( $hex[ 2 ] ) ) / 255;

		return yith_pos_rgb2hsl( array( $r, $g, $b ) );
	}
}

if ( ! function_exists( 'yith_pos_rgb2hsl' ) ) {
	/**
	 * Converts RGB color to HSL color
	 *
	 * @param $rgb
	 *
	 * @return array
	 */
	function yith_pos_rgb2hsl( $rgb ) {
		// Fill variables $r, $g, $b by array given.
		list( $r, $g, $b ) = $rgb;

		// Determine lowest & highest value and chroma
		$max    = max( $r, $g, $b );
		$min    = min( $r, $g, $b );
		$chroma = $max - $min;

		// Calculate Luminosity
		$l = ( $max + $min ) / 2;

		// If chroma is 0, the given color is grey
		// therefore hue and saturation are set to 0
		if ( $chroma == 0 ) {
			$h = 0;
			$s = 0;
		}

		// Else calculate hue and saturation.
		// Check http://en.wikipedia.org/wiki/HSL_and_HSV for details
		else {
			switch ( $max ) {
				case $r:
					$h_ = fmod( ( ( $g - $b ) / $chroma ), 6 );
					if ( $h_ < 0 ) {
						$h_ = ( 6 - fmod( abs( $h_ ), 6 ) );
					} // Bugfix: fmod() returns wrong values for negative numbers
					break;

				case $g:
					$h_ = ( $b - $r ) / $chroma + 2;
					break;

				case $b:
					$h_ = ( $r - $g ) / $chroma + 4;
					break;
				default:
					break;
			}

			$h = $h_ / 6;
			$s = 1 - abs( 2 * $l - 1 );
		}

		// Return HSL Color as array
		return array( $h, $s, $l );
	}
}

if ( ! function_exists( 'yith_pos_hsl2rgb' ) ) {
	/**
	 * Converts HSL color to RGB color
	 *
	 * @param $hsl
	 *
	 * @return array
	 */
	function yith_pos_hsl2rgb( $hsl ) {
		// Fill variables $h, $s, $l by array given.
		list( $h, $s, $l ) = $hsl;

		// If saturation is 0, the given color is grey and only
		// lightness is relevant.
		if ( $s == 0 ) {
			$rgb = array( $l, $l, $l );
		}

		// Else calculate r, g, b according to hue.
		// Check http://en.wikipedia.org/wiki/HSL_and_HSV#From_HSL for details
		else {
			$chroma = ( 1 - abs( 2 * $l - 1 ) ) * $s;
			$h_     = $h * 6;
			$x      = $chroma * ( 1 - abs( ( fmod( $h_, 2 ) ) - 1 ) ); // Note: fmod because % (modulo) returns int value!!
			$m      = $l - round( $chroma / 2, 10 ); // Bugfix for strange float behaviour (e.g. $l=0.17 and $s=1)

			if ( $h_ >= 0 && $h_ < 1 ) {
				$rgb = array( ( $chroma + $m ), ( $x + $m ), $m );
			} else if ( $h_ >= 1 && $h_ < 2 ) {
				$rgb = array( ( $x + $m ), ( $chroma + $m ), $m );
			} else if ( $h_ >= 2 && $h_ < 3 ) {
				$rgb = array( $m, ( $chroma + $m ), ( $x + $m ) );
			} else if ( $h_ >= 3 && $h_ < 4 ) {
				$rgb = array( $m, ( $x + $m ), ( $chroma + $m ) );
			} else if ( $h_ >= 4 && $h_ < 5 ) {
				$rgb = array( ( $x + $m ), $m, ( $chroma + $m ) );
			} else if ( $h_ >= 5 && $h_ < 6 ) {
				$rgb = array( ( $chroma + $m ), $m, ( $x + $m ) );
			}
		}

		return $rgb;
	}
}

if ( ! function_exists( 'yith_pos_rgb2hex' ) ) {
	/**
	 * Converts RGB color to hex code
	 *
	 * @param $rgb
	 *
	 * @return string
	 */
	function yith_pos_rgb2hex( $rgb ) {
		list( $r, $g, $b ) = $rgb;
		$r = round( 255 * $r );
		$g = round( 255 * $g );
		$b = round( 255 * $b );

		return "#" . sprintf( "%02X", $r ) . sprintf( "%02X", $g ) . sprintf( "%02X", $b );
	}
}

if ( ! function_exists( 'yith_pos_hsl2hex' ) ) {
	/**
	 * Converts HSL color to RGB hex code
	 *
	 * @param $hsl
	 *
	 * @return string
	 */
	function yith_pos_hsl2hex( $hsl ) {
		$rgb = yith_pos_hsl2rgb( $hsl );

		return yith_pos_rgb2hex( $rgb );
	}
}

if ( ! function_exists( 'yith_pos_is_wc_admin_enabled' ) ) {
	/**
	 * is WC Admin plugin enabled?
	 *
	 * @return bool
	 */
	function yith_pos_is_wc_admin_enabled() {
		return class_exists( 'Automattic\WooCommerce\Admin\Loader' ) && yith_pos_check_wc_admin_min_version();
	}
}

if ( ! function_exists( 'yith_pos_check_wc_admin_min_version' ) ) {
	/**
	 * check min version for WC Admin
	 *
	 * @return bool
	 */
	function yith_pos_check_wc_admin_min_version() {
		return defined( 'WC_ADMIN_VERSION_NUMBER' ) && version_compare( WC_ADMIN_VERSION_NUMBER, '0.24.0', '>=' );
	}
}

if ( ! function_exists( 'yith_pos_is_pos_order' ) ) {
	/**
	 * is an order created through POS?
	 *
	 * @param int|WC_Order $order
	 *
	 * @return bool
	 */
	function yith_pos_is_pos_order( $order ) {
		$order = $order instanceof WC_Order ? $order : wc_get_order( $order );

		return $order && ! ! absint( $order->get_meta( '_yith_pos_order' ) );
	}
}


if ( ! function_exists( 'yith_pos_get_cpt_object_name' ) ) {
	function yith_pos_get_cpt_object_name( $id, $type ) {
		$hook = 'yith_pos_get_' . $type . '_name';
		$meta = '_name';

		$value = metadata_exists( 'post', $id, $meta ) ? get_post_meta( $id, $meta, true ) : '';

		return apply_filters( $hook, $value, $id );
	}
}

if ( ! function_exists( 'yith_pos_get_register_name' ) ) {
	function yith_pos_get_register_name( $id ) {
		return yith_pos_get_cpt_object_name( $id, 'register' );
	}
}

if ( ! function_exists( 'yith_pos_get_store_name' ) ) {
	function yith_pos_get_store_name( $id ) {
		return yith_pos_get_cpt_object_name( $id, 'store' );
	}
}

if ( ! function_exists( 'yith_pos_rest_product_thumbnail_size' ) ) {
	function yith_pos_rest_product_thumbnail_size() {
		return apply_filters( 'yith_pos_rest_product_thumbnail_size', 'medium' );
	}
}

if ( ! function_exists( 'yith_pos_rest_get_product_thumbnail' ) ) {
	function yith_pos_rest_get_product_thumbnail( $product_id, $variation_id = false ) {
		$size = yith_pos_rest_product_thumbnail_size();

		$attachment_id = false;
		$image         = false;

		if ( $variation_id ) {
			$attachment_id = get_post_thumbnail_id( $variation_id );
		}

		if ( ! $attachment_id ) {
			$attachment_id = get_post_thumbnail_id( $product_id );
		}

		if ( $attachment_id ) {
			$attachment      = wp_get_attachment_image_src( $attachment_id, $size );
			$attachment_post = get_post( $attachment_id );

			if ( is_array( $attachment ) ) {
				$image = array(
					'id'                => (int) $attachment_id,
					'date_created'      => wc_rest_prepare_date_response( $attachment_post->post_date, false ),
					'date_created_gmt'  => wc_rest_prepare_date_response( strtotime( $attachment_post->post_date_gmt ) ),
					'date_modified'     => wc_rest_prepare_date_response( $attachment_post->post_modified, false ),
					'date_modified_gmt' => wc_rest_prepare_date_response( strtotime( $attachment_post->post_modified_gmt ) ),
					'src'               => current( $attachment ),
					'name'              => get_the_title( $attachment_id ),
					'alt'               => get_post_meta( $attachment_id, '_wp_attachment_image_alt', true ),
				);
			}
		}

		return apply_filters( 'yith_pos_rest_get_product_thumbnail', $image, $product_id, $variation_id );
	}
}
