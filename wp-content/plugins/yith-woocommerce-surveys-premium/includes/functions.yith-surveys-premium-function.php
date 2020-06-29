<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


if ( ! function_exists( 'yith_wpml_get_translated_id' ) ) {
	/**
	 * Get the id of the current translation of the post/custom type
	 *
	 * @since  2.0.0
	 * @author Andrea Frascaspata <andrea.frascaspata@yithemes.com>
	 */
	function yith_wpml_get_translated_id( $id, $post_type ) {

		if ( function_exists( 'icl_object_id' ) ) {

			$id = icl_object_id( $id, $post_type, true );

		}

		return $id;
	}
}


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

if ( ! function_exists( 'yith_woocommerce_get_orders' ) ) {

	function yith_woocommerce_get_orders() {

		$order_args = array(
			'return'     => 'ids',
			'limit'     => -1,
			'meta_query' => array(
				'relation' => 'AND',
				array(
					'key'     => '_yith_order_survey_voting',
					'compare' => 'NOT IN',
					'value'	  => array( 'a:0:{}', '' ),
				),
				array(
					'key'     => '_yith_order_converted',
					'compare' => 'NOT EXISTS'
				),

			)

		);
		add_filter( 'woocommerce_order_data_store_cpt_get_orders_query', 'yith_surveys_get_order_with_surveys', 20, 2 );
		$order_ids = wc_get_orders( $order_args );
		remove_filter( 'woocommerce_order_data_store_cpt_get_orders_query', 'yith_surveys_get_order_with_surveys', 20 );

		return $order_ids;
	}
}


if ( ! function_exists( 'yith_download_file' ) ) {

	/**
	 * Download a file
	 *
	 * @param $filepath
	 */
	function yith_download_file( $filepath ) {

		header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
		header( 'Content-Description: File Transfer' );
		header( 'Content-Disposition: attachment; filename=' . $filepath );
		header( "Content-Type: text/csv; charset=" . get_option( 'blog_charset' ), true );
		header( 'Expires: 0' );
		header( 'Pragma: public' );

		readfile( $filepath );
		exit;
	}
}

if ( ! function_exists( 'get_surveys_type' ) ) {

	function get_surveys_type() {

		$visible_option = array(
			'checkout'   => __( 'WooCommerce Checkout', 'yith-woocommerce-surveys' ),
			'product'    => __( 'WooCommerce Product', 'yith-woocommerce-surveys' ),
			'other_page' => __( 'Other Pages', 'yith-woocommerce-surveys' )
		);

		return apply_filters( 'yith_wc_surveys_types', $visible_option );
	}
}

if ( ! function_exists( 'yith_surveys_add_gutenberg_block' ) ) {

	function yith_surveys_add_gutenberg_block() {

		$orderby = get_option( 'ywcsur_orderby', 'date' );
		$order   = get_option( 'ywcsur_order', 'desc' );

		switch ( $orderby ) {

			case 'date':
				$orderby = 'post_date';
				break;
			case 'title':
				$orderby = 'post_title';
				break;
		}
		$default = array(
			'posts_per_page' => - 1,
			'post_type'      => 'yith_wc_surveys',
			'post_status'    => 'publish',
			'post_parent'    => 0,
			'orderby'        => $orderby,
			'order'          => $order,
			'meta_query'     => array(
				array(
					'key'     => '_yith_survey_visible_in',
					'value'   => 'other_page',
					'compare' => '='
				),

			),
			'fields'         => 'ids'
		);

		$surveys = get_posts( $default );

		$options = array();

		foreach ( $surveys as $survey_id ) {

			$options[ $survey_id ] = get_the_title( $survey_id );
		}
		$current_option = count( $options ) > 0 ? current( array_keys( $options ) ) : '';

		$block = array(
			'yith-wc-surveys' => array(
				'title'          => __( 'Surveys', 'yith-woocommerce-surveys' ),
				'description'    => __( 'Show a survey in the page', 'yith-woocommerce-surveys' ),
				'shortcode_name' => 'yith_wc_surveys',
				'do_shortcode'   => false,
				'keywords'       => array( __( 'Surveys', 'yith-woocommerce-surveys' ) ),
				'attributes'     => array(
					'survey_id' => array(
						'type'    => 'select',
						'label'   => __( 'Select a Survey', 'yith-woocommerce-surveys' ),
						'default' => $current_option,
						'options' => $options
					),
				)

			)
		);

		yith_plugin_fw_gutenberg_add_blocks( $block );
	}
}

if ( ! function_exists( 'yith_surveys_get_order_with_surveys' ) ) {

	/**
	 * Add query args to get all orders with sequential order number generated by WooCommerce Sequential Order Numbers
	 * @author Salvatore Strano
	 * @since 1.1.0
	 * @hook woocommerce_order_data_store_cpt_get_orders_query
	 *
	 * @param array $query
	 * @param array $query_args
	 *
	 * @return array
	 */
	function yith_surveys_get_order_with_surveys( $query, $query_args ) {

		if ( isset( $query_args['meta_query'] ) ) {
			$query['meta_query'] = $query_args['meta_query'];
		}

		return $query;
	}
}

if( !function_exists('yith_surveys_update_checkout_slugs')){

	add_action( 'admin_init', 'yith_surveys_update_checkout_slugs' );

	function yith_surveys_update_checkout_slugs(){

		$db_version = get_option( 'yith_surveys_db_version', '1.0.1' );

		if( version_compare( $db_version, '1.1.0', '<' ) ){

			$surveys = YITH_Surveys_Type()->get_checkout_surveys();

			foreach( $surveys as $survey_id ){

				$answers = YITH_Surveys_Type()->get_survey_children( array('post_parent'=> $survey_id ) );

				foreach( $answers as $answer_id ){

					$post_title = get_the_title( $answer_id);
					$post_name = sanitize_title( $post_title );

					$args = array(
						'post_name' => $post_name,
						'ID' => $answer_id
					);

					wp_update_post( $args );
				}
			}
			update_option( 'yith_surveys_db_version', '1.1.0');
		}
	}
}