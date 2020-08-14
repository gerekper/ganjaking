<?php
if ( ! function_exists( 'yith_setcookie' ) ) {
	/**
	 * Create a cookie.
	 *
	 * @param string $name
	 * @param mixed $value
	 *
	 * @return bool
	 * @since 1.0.0
	 */
	function yith_setcookie( $name, $value = array(), $time = null ) {
		$time = $time != null ? $time : time() + 60 * 60 * 24 * 30;

		//$value = maybe_serialize( stripslashes_deep( $value ) );
		$value      = json_encode( stripslashes_deep( $value ) );
		$expiration = apply_filters( 'yith_wcwl_cookie_expiration_time', $time ); // Default 30 days

		$_COOKIE[ $name ] = $value;
		wc_setcookie( $name, $value, $expiration, false );
	}
}

if ( ! function_exists( 'yith_getcookie' ) ) {
	/**
	 * Retrieve the value of a cookie.
	 *
	 * @param string $name
	 *
	 * @return mixed
	 * @since 1.0.0
	 */
	function yith_getcookie( $name ) {
		if ( isset( $_COOKIE[ $name ] ) ) {
			return json_decode( stripslashes( $_COOKIE[ $name ] ), true );
		}

		return array();
	}
}

if ( ! function_exists( 'yith_usecookies' ) ) {
	/**
	 * Check if the user want to use cookies or not.
	 *
	 * @return bool
	 * @since 1.0.0
	 */
	function yith_usecookies() {
		return get_option( 'yith_wcwl_use_cookie' ) == 'yes' ? true : false;
	}
}

if ( ! function_exists( 'yith_destroycookie' ) ) {
	/**
	 * Destroy a cookie.
	 *
	 * @param string $name
	 *
	 * @return void
	 * @since 1.0.0
	 */
	function yith_destroycookie( $name ) {
		yith_setcookie( $name, array(), time() - 3600 );
	}
}

if ( ! function_exists( 'ywsfl_add_gutenberg_block' ) ) {

	function ywsfl_add_gutenberg_block() {

		$block = array(
			'yith-wsfl-saveforlater' => array(
				'title'          => __( 'Save for later list', 'yith-woocommerce-save-for-later' ),
				'description'    => __( 'Show the save for later list in the page', 'yith-woocommerce-save-for-later' ),
				'shortcode_name' => 'yith_wsfl_saveforlater',
				'do_shortcode'   => false,
				'keywords'       => array( __( 'Save for later', 'yith-woocommerce-save-for-later' ) ),
				'attributes'     => array(
					'title_list' => array(
						'type'    => 'text',
						'label'   => __( 'Title', 'yith-woocommerce-save-for-later' ),
						'default' => __( 'Saved for later ', 'yith-woocommerce-save-for-later' ),
					),
				)

			)
		);

		yith_plugin_fw_gutenberg_add_blocks( $block );
	}
}

function yith_save_for_later_update_db_1_1() {

	$current_db = get_option( 'ywsfl_db_version', '1.0.0' );


	if ( version_compare( $current_db, '1.1.0', '<' ) || isset( $_GET['ywsfl_force_alter_table'] ) ) {

		global $wpdb;

		$table_name = $wpdb->prefix . 'ywsfl_list';
		$sql        = "ALTER TABLE {$table_name} ADD COLUMN variations longtext DEFAULT ''";
		$sql2       = "ALTER TABLE {$table_name} ADD COLUMN cart_item_key longtext DEFAULT ''";
		$res        = $wpdb->query( $sql );
		$res2       = $wpdb->query( $sql2 );


		if ( $res && $res2 ) {
			update_option( 'ywsfl_db_version', '1.1.0' );
		}
		wp_safe_redirect( remove_query_arg( 'ywsfl_force_alter_table' ));
		exit;
	}


}

add_action( 'admin_init', 'yith_save_for_later_update_db_1_1', 15 );

if ( ! function_exists( 'yith_save_for_later_get_message' ) ) {

	function yith_save_for_later_get_message( $type = 'added' ) {

		$messages = apply_filters( 'yith_save_for_later_messages', array(
			'added'   => __( 'Product added on save for later list', 'yith-woocommerce-save-for-later' ),
			'deleted' => __( 'Product deleted from Save for later', 'yith-woocommerce-save-for-later' ),
			'error'   => __( 'Cannot add the product in save for later list', 'yith-woocommerce-save-for-later' ),
			'exist'   => __( 'Product already in Save for later', 'yith-woocommerce-save-for-later' ),
		) );

		return isset( $messages[ $type ] ) ? $messages[ $type ] : $messages;
	}
}