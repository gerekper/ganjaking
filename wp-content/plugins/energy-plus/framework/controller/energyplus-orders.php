<?php

/**
* EnergyPlus Orders
*
* Order management
*
* @since      1.0.0
* @package    EnergyPlus
* @subpackage EnergyPlus/framework
* @author     EN.ER.GY <support@en.er.gy>
* */


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


class EnergyPlus_Orders extends EnergyPlus {

	/**
	* Starts everything
	*
	* @return void
	*/

	public static function run() {

		EnergyPlus::wc_engine();

		wp_enqueue_script("energyplus-orders",  EnergyPlus_Public . "js/energyplus-orders.js", array(), EnergyPlus_Version);

		self::route();
	}

	/**
	* Router for sub pages
	*
	* @return void
	*/

	private static function route() {
		switch (EnergyPlus_Helpers::get('action')) {
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

	public static function filter($filter) {

		if (!$filter) {
			$filter['page']        = 1;
			$filter['limit']       = absint(EnergyPlus::option('reactors-tweaks-pg-orders', 10));
			$filter['post_status'] = array_keys( wc_get_order_statuses() );
		}

		if ($status = EnergyPlus_Helpers::get('status', null)) {
			if ('trash' === $status) {
				$filter['post_status'] = 'trash';
			} else {
				if (in_array('wc-' . $status,  array_keys( wc_get_order_statuses() ))) {
					$filter['post_status'] = "wc-". $status;
				}
				if (in_array( $status,  array_keys( wc_get_order_statuses() ))) {
					$filter['post_status'] =  $status;
				}
			}
		}


		if (EnergyPlus_Helpers::get('s', null)) {
			$filter['q'] = EnergyPlus_Helpers::get('s', '');
		}

		if (EnergyPlus_Helpers::get('go', null)) {
			$filter['mode'] = 95;
		}

		if (EnergyPlus_Helpers::get('pg', null)) {
			$filter['page'] = intval( EnergyPlus_Helpers::get( 'pg', 0 ));
		}


		if ($customer = EnergyPlus_Helpers::get('customer')) {
			$filter['meta_query'] = array(
				array(
					'key'     => '_customer_user',
					'value'   => absint( $customer ),
					'compare' => '=',
				)
			);
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
	*
	* @return null
	*/

	public static function index($filter = array()) {

		$filter = self::filter($filter);

		$list   = array();

		$list['statuses'] =  WC()->api->WC_API_Orders->get_order_statuses()['order_statuses'];

		foreach ($list['statuses'] AS $list_status_k => $list_status_k ) {
			$list['statuses_count'][$list_status_k] =  WC()->api->WC_API_Orders->get_orders_count( $list_status_k ) ['count'];
		}

		$list['statuses_count']["count"] = WC()->api->WC_API_Orders->get_orders_count() ['count'];
		$list['statuses_count']['trash'] = wp_count_posts('shop_order')->trash;

		$orders['orders'] =  self::get_orders( $filter['post_status'], $filter,  $filter['page'])['result'];

		switch ( $mode = ( !empty($filter['mode']) ? absint($filter['mode']) : EnergyPlus::option('mode-energyplus-orders', 1) ) )	{

			// Standart
			case 1:
			case 98:
			$orders['orders'] = self::_group_by($orders['orders'], 'created_at');
			echo EnergyPlus_View::run('orders/list-' . $mode,  array( 'orders' => $orders['orders'], 'list' => $list,  'ajax' =>   EnergyPlus_Helpers::is_ajax()  ));
			break;

			case 2:
			echo EnergyPlus_View::run('orders/list-2',  array( 'orders' => array('all' => array('orders'=>$orders['orders'])), 'list' => $list, 'ajax' =>   EnergyPlus_Helpers::is_ajax()  ));
			break;

			case 97:
			return EnergyPlus_View::run('orders/list-2',  array( 'orders' => array('all' => array('orders'=>$orders['orders'])), 'list' => $list,  'ajax' =>   1  ));
			break;

			// Woocommerce Native
			case 99:
			if (!EnergyPlus_Admin::is_full()) {
				EnergyPlus_Helpers::frame( admin_url( 'edit.php?post_type=shop_order' ) );
			} else {
				wp_redirect(  admin_url( 'edit.php?post_type=shop_order' ) );
			}
			break;

			// Other menus
			case 95:
			echo EnergyPlus_View::run('orders/list-95', array('list'=>$list, 'iframe_url' => EnergyPlus_Helpers::get_submenu_url(EnergyPlus_Helpers::get('go')) ));
			break;
		}
	}

	/**
	* Group titles by date
	*
	* @since  1.0.0
	* @param  array    $array
	* @param  string    $key
	* @return array
	*/

	private static function _group_by($array, $key) {

		$return = array();

		foreach($array as $val) {
			$time = EnergyPlus_Helpers::grouped_time( $val['date_created'] );

			$return[$time['key']]['title']    = $time['title'];
			$return[$time['key']]['orders'][] = $val;
		}
		return $return;
	}


	/**
	* Ajax router
	*
	* @since  1.0.0
	* @return EnergyPlus_Ajax
	*/

	public static function ajax() {
		global $woocommerce;

		EnergyPlus::wc_engine();

		EnergyPlus_Helpers::ajax_nonce(TRUE);

		$do = EnergyPlus_Helpers::post('do', 'default');

		switch ($do){

			case "filter":
			$fields = $_POST['fields'];

			$filter = array();

			foreach ($fields AS $key => $field) {

				$field['value'] = sanitize_key($field['value']);

				if ('order_id' === $field['name'] && trim($field['value']) !== '') {
					$filter['post__in'] = array(EnergyPlus_Helpers::clean($field['value']));
				}

				if ('status' === $field['name'] && trim($field['value']) !== '') {
					if (in_array($field['value'], array('pending', 'processing', 'on-hold', 'completed', 'cancelled', 'refunded', 'failed'))) {
						$filter['post_status'] = "wc-" . EnergyPlus_Helpers::clean($field['value']);
					}
				}

				if ('customer' === $field['name'] && trim($field['value']) !== '') {
					$filter['meta_query'] = array(
						array(
							'key'     => '_customer_user',
							'value'   => absint( EnergyPlus_Helpers::clean($field['value']) ),
							'compare' => '=',
						),
					);
				}
			}

			echo self::index($filter);
			wp_die();
			break;

			// Search
			case 'search':

			$status = EnergyPlus_Helpers::post('extra', '');

			if (in_array('wc-' . $status, array_keys( wc_get_order_statuses() ))) {
				$filter['post_status'] = "wc-" .$status;
			} else {
				$filter['post_status'] = array_keys( wc_get_order_statuses() );
			}

			$filter['search'] = EnergyPlus_Helpers::post('q', '');

			$filter['mode'] = (EnergyPlus_Helpers::post('mode') ? absint(EnergyPlus_Helpers::post('mode')) : null) ;
			$filter['page'] = 1;

			echo self::index($filter);
			wp_die();
			break;

			// Delete or restrore order
			case 'deleteforever':
				case 'restore':

				$id = absint(EnergyPlus_Helpers::post('id', 0));

				$order = new WC_Order($id);

				if (!$order) {
					EnergyPlus_Ajax::error(esc_html__('Order is not exists', 'energyplus'));
					wp_die();
				}

				if ('deleteforever' === $do) {
					$change= wp_delete_post( $id, true );
				} else {
					$change= wp_untrash_post( $id );

				}

				if (!$change) {
					EnergyPlus_Ajax::error(esc_html__('Order can not be restore', 'energyplus'));
				} else {
					EnergyPlus_Ajax::success('OK', array('id'=>$id, 'message'=>esc_html__('Order has been restored', 'energyplus')));
				}

				break;

				// Change status of order
				case 'changestatus':

				$result = array();
				$status = EnergyPlus_Helpers::post('status') ;
				$ids    = wp_parse_id_list(EnergyPlus_Helpers::post('id', array())) ;

				if (!is_array( $ids ))	{
					wp_die ( -1 );
				}

				if (!in_array("wc-".$status, array_keys(wc_get_order_statuses())) && !in_array($status, array_keys(wc_get_order_statuses())) && !in_array($status, array('all', 'pending', 'processing', 'on-hold', 'completed', 'cancelled', 'refunded', 'failed', 'trash', 'restore', 'deleteforever'))) {
					wp_die ( -2 );
				}

				$ids = array_map('absint', $ids);

				foreach ($ids AS $id) {

					$change = false;
					$order = new WC_Order(absint($id));

					if ($order) {

						if ('trash' === $status) {
							$change= wp_trash_post( $id );
						} else if ('deleteforever' === $status) {
							$change= wp_delete_post( $id, true );
						} else if ('restore' === $status) {
							$change= wp_untrash_post( $id );
						} else {
							$change = $order->update_status($status);
							do_action( 'woocommerce_update_order', $order->get_id() );
							do_action( 'woocommerce_order_status_changed', $order->get_id(), $order->get_status(), $status, $order );

						}

						wc_delete_shop_order_transients( absint($id) );

						EnergyPlus_Events::save_post_shop_order($id);

					}

					if ($change) {
						$result['success'][] = $id;
					} else {
						$result['errors'][] = $id;
					}
				}

				return EnergyPlus_Ajax::success('Order status has been changed', $result);

				break;


			}
		}

		/**
		* Get list of products
		*
		* @since  1.0.0
		* @param  string    $type
		* @param  array     $filter
		* @return array
		*/

		private static function get_orders( $type, $filter = array(), $page = 0 ) {

			$count = 0;
			$orders = array();

			if (!empty(EnergyPlus_Helpers::get('s'))) {
				$filter['search'] = EnergyPlus_Helpers::get('s');
			}

			if (!empty($filter['search']) && (2 < strlen($filter['search']) OR 0 ===  strlen($filter['search']))) {

				$results = wc_order_search($filter['search']);

				if (0 < count($results)) {

					$results = array_reverse($results);

					$results = array_slice($results, 0, 100); // Limit to 100 items

					foreach ( $results as $order_id ) {
						$order = wc_get_order($order_id);
						if ($order) {

							$billing_formatted = $order->get_formatted_billing_address();
							$shipping_formatted = $order->get_formatted_shipping_address();
							$std = $order;

							$order = $order->get_data();

							$order['billing_formatted'] = $billing_formatted;
							$order['shipping_formatted'] = $shipping_formatted;
							$order['std'] = $std;

							$orders[] = $order;
						}
					}
				}

				$count = count($results);

			} else {
				$query_args = array(
					'post_type'      => 'shop_order',
					'post_status'    => array_keys(wc_get_order_statuses()),
					'posts_per_page' => absint(EnergyPlus::option('reactors-tweaks-pg-orders', 10)),
					'paged'          => $page,
					'orderby'        => 'date',
					'order'          => 'DESC'
				);

				if (isset($filter['post_status']))	{
					$query_args['post_status'] = $type;
				} else {
					$query_args['orderby'] = 'date';
					$query_args['order']   = 'DESC';
				}


				if (isset($filter['post__in']))	{
					$query_args['post__in'] = $filter['post__in'];
				}

				$query_args = array_merge($query_args, $filter);

				$query = new WP_Query( $query_args );

				if ( $query->have_posts() )	{
					foreach ( $query->posts as $order_id ) {

						$order = wc_get_order($order_id);

						if (is_wp_error($order)) {
							continue;
						}

						$billing_formatted = $order->get_formatted_billing_address();
						$shipping_formatted = $order->get_formatted_shipping_address();
						$std = $order;

						$order = $order->get_data();

						$order['billing_formatted'] = $billing_formatted;
						$order['shipping_formatted'] = $shipping_formatted;
						$order['std'] = $std;

						$next_statuses = array();
						$cond = EnergyPlus::option('reactors-tweaks-order-cond',array_keys(wc_get_order_statuses()));

						if (isset($cond['wc-'.$order['status']])) {
							foreach ($cond['wc-'.$order['status']] AS $key) {
								if ('-' === $key) {
									continue;
								} else if ('trash' === $key) {
									$next_statuses[] = $key;
								} else {
									$next_statuses[] = $key;
								}
							}
						} else {
							$next_statuses = array_keys(wc_get_order_statuses());
							$next_statuses[] = 'trash';
						}

						$order['next_statuses'] = $next_statuses;

						$orders[] = $order;
					}
				}

				$count = $query->found_posts;
			}

			return array(
				'count'  => $count,
				'result' => $orders
			);
		}

	}

	?>
