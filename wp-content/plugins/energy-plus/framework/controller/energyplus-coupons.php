<?php

/**
* EnergyPlus Coupons
*
* Coupons management
*
* @since      1.0.0
* @package    EnergyPlus
* @subpackage EnergyPlus/framework
* @author     EN.ER.GY <support@en.er.gy>
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class EnergyPlus_Coupons extends EnergyPlus {

	/**
	* Starts everything
	*
	* @return void
	*/

	public static function run() {

		EnergyPlus::wc_engine();

		wp_enqueue_script("energyplus-coupons",  EnergyPlus_Public . "js/energyplus-coupons.js");

		self::route();
	}

	/**
	* Router for sub pages
	*
	* @return void
	*/

	private static function route() {

		switch (EnergyPlus_Helpers::get('action')) {

			case 'delete':
			self::delete();
			break;

			case 'stats':
			self::stats();
			break;

			default:
			self::index();
			break;
		}
	}

	/**
	* Prepare filter array for query
	*
	* @param  mixed $filter   array of filter or false
	* @return array           new filter array
	*/

	public static function filter($filter = false) {

		if (!$filter) {
			$filter['post_status'] = array('publish');
			$filter['offset']      = 0;
			$filter['page']        = 1;
			$filter['s']           = '';
		}

		if (!isset($filter['limit'])) {
			$filter['limit'] = absint(EnergyPlus::option('reactors-tweaks-pg-coupons', 10));
		}
		if (EnergyPlus_Helpers::get('go', null)) {
			$filter['mode'] = 95;
		}

		if ('' !== EnergyPlus_Helpers::get('status', '')) {
			$filter['post_status'] = EnergyPlus_Helpers::get('status', null);
		}

		if ('' !== EnergyPlus_Helpers::get('s', '')) {
			$filter['s'] = EnergyPlus_Helpers::get('s', '');
		}

		if (EnergyPlus_Helpers::get('pg', null)) {
			$filter['offset'] = (intval( EnergyPlus_Helpers::get( 'pg', 1 )) - 1) *  $filter['limit'];
		}

		if (EnergyPlus_Helpers::get('orderby')) {
			if (false !== strpos(EnergyPlus_Helpers::get('orderby',''), 'meta_')) {
				$filter['orderby']  = "meta_value_num";
				$filter['meta_key'] = sanitize_sql_orderby(str_replace ( 'meta_', '', EnergyPlus_Helpers::get('orderby','')));
			} else {
				$filter['orderby'] =  sanitize_sql_orderby(EnergyPlus_Helpers::get('orderby',''));
			}

			$filter['order'] 	= 'ASC' === EnergyPlus_Helpers::get('order','ASC') ? 'ASC' : 'DESC';
		}

		return $filter;
	}

	/**
	* Main function
	*
	* @param  mixed $filter   array of filter
	* @return EnergyPlus_View
	*/

	public static function index($filter = false) {

		$filter = self::filter($filter);

		switch ( $mode = ( !empty($filter['mode']) ? absint($filter['mode']) : EnergyPlus::option('mode-energyplus-coupons', 1) ) ) {

			// Woocommerce Native
			case 99:
			if (!EnergyPlus_Admin::is_full()) {
				EnergyPlus_Helpers::frame( admin_url( 'edit.php?post_type=shop_coupon' ) );
			} else {
				wp_redirect( admin_url( 'edit.php?post_type=shop_coupon' ) );
			}
			break;

			// Standart
			case 1:
			case 2:
			case 95:

			if ('trash' === EnergyPlus_Helpers::get('status', 'publish')) {
				$coupons =  self::get_coupons('trash', $filter,  $filter['page']);
			} else {
				if ('' !== $filter['s'])
				{
					$coupons =  (array)self::get_coupons( 'publish', $filter, $filter['page'] );
				} else {
					$coupons =  (array)self::get_coupons( EnergyPlus_Helpers::get('status', 'publish'), $filter,  $filter['page'] );
				}
			}

			if (95 === $mode) {
				echo EnergyPlus_View::run('coupons/list-95', array('counts' => wp_count_posts('shop_coupon'), 'count' => $coupons['count'], 'iframe_url' => EnergyPlus_Helpers::get_submenu_url(EnergyPlus_Helpers::get('go')) ));
			} else {
				echo EnergyPlus_View::run('coupons/list-'. $mode,  array( 'count' => $coupons['count'], 'per_page' => $filter['limit'], 'coupons' => $coupons['result'], 'counts' => wp_count_posts('shop_coupon'), 'ajax' =>   EnergyPlus_Helpers::is_ajax() ));
			}
			break;
		}
	}

	/**
	* Show statics about a coupons
	*
	* @since  1.0.0
	* @param  integer   $coupon_id
	* @param  array     $args
	* @return void
	*/

	public static function stats($coupon_id = 0, $args = array()) {
		global $wpdb;

		if (0 === $coupon_id) {
			$coupon_id	=	absint( EnergyPlus_Helpers::get('id', 0) );
		}

		$coupon = array();
		$coupon['total_discount'] = 0;
		$coupon['total_sales']    = 0;

		$data = array();

		if ($coupon_id > 0) {

			$coupon_data = WC()->api->WC_API_Coupons->get_coupon($coupon_id)['coupon'];
			$coupon_code = $coupon_data['code'];

			$coupon['code']        = $coupon_code;
			$coupon['usage_count'] = $coupon_data['usage_count'];
			$coupon['created_at']  = date('M d, Y', strtotime($coupon_data ['created_at'] ));
			$coupon['dots']        = array(
				date('Y')                       => array(0),
				date('Y', strtotime('-1 year')) => array(0),
				date('Y', strtotime('-2 year')) => array(0)
			);
			$coupon['dots_max']    = 0;

			$query = $wpdb->prepare("
			SELECT
			count(p.ID) AS cnt, MONTH(p.post_date) AS mnt, YEAR(p.post_date) AS yr
			FROM
			{$wpdb->prefix}posts AS p
			INNER JOIN {$wpdb->prefix}woocommerce_order_items AS woi ON p.ID = woi.order_id
			WHERE
			p.post_type = 'shop_order' AND
			p.post_status IN ('" . implode("','", array_keys(wc_get_order_statuses())) . "') AND
			woi.order_item_type = 'coupon' AND
			woi.order_item_name = %s AND
			DATE(p.post_date)
			GROUP BY YEAR(p.post_date), MONTH(p.post_date)",
			$coupon_code
		);

		$_dots = $wpdb->get_results($query);

		if ($_dots) {
			foreach ($_dots AS $_dot) {
				$coupon['dots'][$_dot->yr][$_dot->mnt] = absint( $_dot->cnt );
				$coupon['dots_max'] = max(	$coupon['dots_max'],absint( $_dot->cnt ) );
			}
		}

		$query = $wpdb->prepare("
		SELECT
		p.ID AS order_id
		FROM
		{$wpdb->prefix}posts AS p
		INNER JOIN {$wpdb->prefix}woocommerce_order_items AS woi ON p.ID = woi.order_id
		WHERE
		p.post_type = 'shop_order' AND
		p.post_status IN ('" . implode("','", array_keys(wc_get_order_statuses())) . "') AND
		woi.order_item_type = 'coupon' AND
		woi.order_item_name = %s AND
		DATE(p.post_date)
		ORDER BY p.post_date DESC",
		$coupon_code);

		$orders = $wpdb->get_results($query);

		foreach ($orders AS $_orders) {
			$order      = wc_get_order( $_orders->order_id );
			$order_data = $order->get_data();
			$time       = EnergyPlus_Helpers::grouped_time( $order->get_date_created() );

			$data[$time['key']]['title'] = $time['title'];

			$data[$time['key']]['orders'][$_orders->order_id]['order_total']    = $order_data['total'];
			$data[$time['key']]['orders'][$_orders->order_id]['order_currency'] = $order_data['currency'];
			$data[$time['key']]['orders'][$_orders->order_id]['order_id']       = $_orders->order_id;
			$data[$time['key']]['orders'][$_orders->order_id]['order_date']     = date('M d, Y', strtotime( $order->get_date_created() )) ;
			$data[$time['key']]['orders'][$_orders->order_id]['discount']       = $order_data['discount_total'];
			$data[$time['key']]['orders'][$_orders->order_id]['status']         = $order_data['status'];
			$data[$time['key']]['orders'][$_orders->order_id]['line_items']     = $order->get_items();
			$data[$time['key']]['orders'][$_orders->order_id]['customer']       = array(
				'id'    => $order_data['customer_id'],
				'name'  => $order_data['billing']['first_name']. ' '.  $order_data['billing']['last_name'],
				'city'  => $order_data['billing']['city'],
				'state' => $order_data['billing']['state']
			);

			$coupon['total_discount'] += $order_data['discount_total'];
			$coupon['total_sales']    += $order_data['total'];
		}
	} else {


		$query = $wpdb->prepare("SELECT
			p.ID AS order_id
			FROM
			{$wpdb->prefix}posts AS p
			INNER JOIN {$wpdb->prefix}woocommerce_order_items AS woi ON p.ID = woi.order_id
			WHERE
			p.post_type = 'shop_order' AND
			p.post_status IN (%s) AND
			woi.order_item_type = 'coupon'",
			implode("','", array_keys(wc_get_order_statuses())));

			$orders = $wpdb->get_results($query);
		}

		if (isset($args['return'])) {
			return $coupon;
		} else {
			echo EnergyPlus_View::run('coupons/stats',  array( 'orders' => $data, 'coupon'=> $coupon, 'counts' => wp_count_posts('shop_coupon') ));
		}

	}

	/**
	* Ajax router
	*
	* @since  1.0.0
	* @return EnergyPlus_Ajax
	*/

	public static function ajax() {

		$do        = EnergyPlus_Helpers::post('do') ;
		$coupon_id = EnergyPlus_Helpers::post('id', 0) ;

		switch ($do)
		{

			// Searching
			case 'search':

			EnergyPlus::wc_engine();

			$filter['s']           = EnergyPlus_Helpers::post('q', '');
			$filter['post_status'] = array('publish', 'private');
			$filter['limit'] = 99;

			echo self::index($filter);
			wp_die();

			break;

			// Bulk operations
			case 'bulk':

			EnergyPlus::wc_engine();

			if ('' === $coupon_id) {
				exit;
			}

			$ids = explode ( ',', $coupon_id );

			if ( !is_array( $ids ) OR ( 0 === count( $ids ) ) ) {
				exit;
			}

			$ids = array_map('absint', $ids);

			$success = array();

			foreach ($ids AS $id)
			{

				$coupon = get_page($id, OBJECT, 'shop_coupon');

				if ( 0 === $coupon->ID ) {
					return EnergyPlus_Ajax::error("Invalid Coupon");
				}

				if ( 'trash' === EnergyPlus_Helpers::post( 'state' ) ) {
					WC()->api->WC_API_Coupons->delete_coupon($id);
					$success[] = $id;
					$state     = 'trashnew';
				}

				if ( 'restore' === EnergyPlus_Helpers::post( 'state' ) ) {
					$result    = wp_untrash_post( $id );
					$success[] = $id;
					$state     = 'trashnew';
				}

				if ( 'deleteforever' === EnergyPlus_Helpers::post( 'state' ) ) {
					$result    = WC()->api->WC_API_Coupons->delete_coupon($id, 'true');
					$success[] = $id;
					$state     = 'trashnew';
				}

				if ( 'private' === EnergyPlus_Helpers::post( 'state' ) OR 'publish' === EnergyPlus_Helpers::post( 'state' ) ) {
					$state = ('private' === EnergyPlus_Helpers::post('state')) ? 'private' : 'publish';

					$my_post = array(
						'ID'          => $id,
						'post_status' => $state,
					);

					$success[] = $id;

					wp_update_post($my_post);
				}
			}

			return EnergyPlus_Ajax::success('Coupon has been changed', array('id'=> $success, 'new'=>$state));

			break;


			// Active or passive the coupon
			case 'active':

			if ('' === $coupon_id) {
				exit;
			}

			EnergyPlus_Helpers::ajax_nonce( TRUE, 'energyplus-coupons--onoff-' . $coupon_id  );

			$coupon = get_page_by_title($coupon_id, OBJECT, 'shop_coupon');

			if ( 0 === $coupon->ID ) {
				return EnergyPlus_Ajax::error("Invalid Coupon");
			}

			$state = ('false' === EnergyPlus_Helpers::post('state')) ? 'private' : 'publish';

			$my_post = array(
				'ID'          => $coupon->ID,
				'post_status' => $state,
			);

			wp_update_post($my_post);

			return EnergyPlus_Ajax::success('Coupon has been changed', array('id'=>$coupon->ID, 'new'=>$state));

			break;
		}

	}

	/**
	* Delete the coupon
	*
	* @since  1.0.0
	* @return void
	*/

	public static function delete()
	{

		$coupon_id	=	intval( EnergyPlus_Helpers::get('id', 0) );
		$force = ( 'true' ===  EnergyPlus_Helpers::get('forever', 'false') ) ? 'true' : 'false';

		EnergyPlus_Helpers::ajax_nonce(TRUE, $coupon_id);

		if ($coupon_id === 0) {
			wp_die('Error');
		}

		if ( 'true' ===  EnergyPlus_Helpers::get('untrash', 'false') ) {
			$result = wp_untrash_post( $coupon_id );

			wp_safe_redirect(  EnergyPlus_Helpers::admin_page('coupons') );

		} else {
			$result = WC()->api->WC_API_Coupons->delete_coupon($coupon_id, $force);
		}
		wp_safe_redirect( wp_get_referer() ? remove_query_arg( array( 'trashed', 'untrashed', 'deleted', 'ids' ), wp_get_referer() ) : admin_url( 'edit.php?post_type=product' ) );

	}

	/**
	* Query for getting coupons
	*
	* @since  1.0.0
	* @param  string    $type   Post statuses
	* @param  array     $filter Params for filter
	* @return array             Coupons list and count
	*/

	private static function get_coupons( $type, $filter = array(), $page = 0 ) {

		$coupons = array();

		$query_args = array(
			'post_type'      => 'shop_coupon',
			'post_status'    => array('publish', 'private', 'trash'),
			'posts_per_page' =>  absint(EnergyPlus::option('reactors-tweaks-pg-coupons', 10)),
			'paged'          => $page
		);

		if ('' === $filter['s']) {
			if (!in_array($type, array('publish', 'private', 'trash'))) {
				$type = 'publish';
			}
			$query_args['post_status'] = $type;
		} else {
			$query_args['orderby'] = 'relevance';
			$query_args['order']   = 'DESC';
		}

		$query_args = array_merge($query_args, $filter);

		$query = new WP_Query( $query_args );

		if ( $query->have_posts() ) {
			foreach ( $query->posts as $coupon_id ) {
				$coupon                = array();
				$coupon['id']          = $coupon_id->ID;
				$coupon['code']        = $coupon_id->post_title;
				$coupon['description'] = $coupon_id->post_excerpt;
				$coupon['status']      = $coupon_id->post_status;
				$coupon['amount']      = get_post_meta( $coupon_id->ID, "coupon_amount", true);
				$coupon['type']        = get_post_meta( $coupon_id->ID, "discount_type", true);
				$coupon['usage_count'] = get_post_meta( $coupon_id->ID, "usage_count", true);
				$coupon['usage_limit'] = get_post_meta( $coupon_id->ID, "usage_limit", true);
				$coupon['post_date']   = $coupon_id->post_date;
				$coupon['expiry_date'] = get_post_meta( $coupon_id->ID, "date_expires", true);
				$coupon['product_ids'] = explode (',', get_post_meta( $coupon_id->ID, "product_ids", true));
				$coupon['stats']       = self::stats( $coupon_id->ID, array('only_stats' => true, 'return' => true));
				$coupons[]             = $coupon;
			}
		}

		return array(
			'count'  => $query->found_posts,
			'result' => $coupons
		);
	}

}

?>
