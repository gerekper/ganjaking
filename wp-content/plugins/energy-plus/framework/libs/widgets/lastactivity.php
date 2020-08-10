<?php

/**
* WIDGET
*
* Activies of online visitors
*
*
* @since      1.0.0
* @package    EnergyPlus
* @subpackage EnergyPlus/framework/libs/widgets
* @author     EN.ER.GY <energyplus@en.er.gy>
* */


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Widgets__Lastactivity extends EnergyPlus_Widgets {

	public static $name = 'Last Activity';
	public static $multiple = false;

	public static function run ( $args = array(), $settings = array() ) {

		global $wpdb;

		EnergyPlus::wc_engine();

		$time  = isset( $args['lasttime'] ) ? $args['lasttime'] : 0;
		$range = $args['range'] = (isset($settings['range'])) ? $settings['range'] : 'online';
		$time = EnergyPlus_Helpers::strtotime('now - '.absint(EnergyPlus::option('feature-refresh', 30)).' seconds');

		$args['uniqid'] = uniqid();


		if (!isset($args['counter'])) {
			$args['counter'] = 0;
		}

		switch ($range) {

			case 'all':

			if ($args['counter'] === -2 OR $args['counter'] === 0) {
				$time = EnergyPlus_Helpers::strtotime('now - 24 hours');
			}

			$__last_sessions = $wpdb->get_results(
				$wpdb->prepare("
				SELECT DISTINCT(session_id)
				FROM {$wpdb->prefix}energyplus_requests
				WHERE type IN (1,2,4,5,6,7,10,17) AND date >= %s AND date <= %s ORDER BY request_id DESC LIMIT 100",
				$time, EnergyPlus_Helpers::strtotime('now')
			), ARRAY_A );
			break;

			default:

			if ($args['counter'] === -2) {
				$time = EnergyPlus_Helpers::timestamp(strtotime('now - 5 minutes'));
			} elseif ($args['counter'] === 0) {
				$time = EnergyPlus_Helpers::timestamp(strtotime('now - 5 minutes'));
			}

			$__last_sessions = $wpdb->get_results(
				$wpdb->prepare("
				SELECT DISTINCT(session_id)
				FROM {$wpdb->prefix}energyplus_requests
				WHERE type IN (1,2,4,5,6,7,10,17) AND month >= %d AND month <= %d AND date >= %s AND date <= %s ORDER BY request_id DESC LIMIT 50",
				EnergyPlus_Helpers::strtotime($time, 'm'),	EnergyPlus_Helpers::strtotime("now", 'm'), $time, EnergyPlus_Helpers::strtotime('now')
			), ARRAY_A );

			break;

		}
		$last_sessions = array_unique(array_column($__last_sessions, 'session_id'));

		if (0 === count($last_sessions)) {
			$last_sessions = array(-1);
		}

		if ($args['counter'] === -2) {
			$last_sessions[] = -2;

		}

		$sessions = self::get_session($last_sessions);

		if (EnergyPlus_Helpers::is_ajax() OR isset( $args['ajax'] ))  {
			return array("updated"=> $last_sessions, "off_time" =>  EnergyPlus_Helpers::strtotime('-5 minutes', "Hi"), "list"=>EnergyPlus_View::run('widgets/lastactivity',  array('args' => $args, 'ajax'=>1, 'result' => $sessions)));
		} else {
			echo EnergyPlus_View::run('widgets/lastactivity',  array('args' => $args,  'result' => $sessions));
		}

	}

	private static function get_session($session_ids) {

		global $wpdb;

		$cache = array();

		$session_ids = array_map('esc_sql', $session_ids);

		$query = "SELECT {$wpdb->prefix}energyplus_requests.* FROM {$wpdb->prefix}energyplus_requests WHERE type IN (1,2,4,5,6,7,10,17) AND month = %d AND (session_id='-1' ";

			foreach ($session_ids AS $ids) {
			$query .="OR session_id = '$ids'";
			}

			$query .=") ORDER BY request_id DESC";


			$__result = $wpdb->get_results(
				$wpdb->prepare($query,
				EnergyPlus_Helpers::strtotime(current_time('mysql'), 'm')
				)
			);


			$sessions = array();
			foreach ($__result AS $sess) {
				if (!isset($sessions[$sess->session_id]['date'] )) {
					$sessions[$sess->session_id]['date'] = $sess->date;
				}
				$sessions[$sess->session_id]['visitor'] = self::get_user($sess->visitor, $sess->ip);
				$sessions[$sess->session_id]['id'] = $sess->session_id;

				switch ($sess->type) {
					case 1:

					if (isset($cache[1][$sess->id])) {
						$product = $cache[1][$sess->id];
						++$cache[1][$sess->id]['cnt'];
						++$product['cnt'];
					} else {
						$object = wc_get_product($sess->id);
						if ($object) {
							$product = array(
								'id' => $sess->id,
								'name' => $object->get_name(),
								'price' => $object->get_price(),
								'cnt' => 1
							);
							$cache[1][$sess->id] = $product;
						} else {
							$product = array(
								'id' => $sess->id,
								'name' => '',
								'price' => '',
								'cnt' => 1
							);
						}
					}
					$sessions[$sess->session_id]['views'][$sess->type.$sess->id] = array('time'=>$sess->date, 'details' => $product,'type'=> 1);
					break;

					case 2:

					if (isset($cache[2][$sess->id])) {
						$category = $cache[2][$sess->id];
						++$cache[2][$sess->id]['cnt'];
						++$category['cnt'];
					} else {

						$object = get_term_by( 'id', $sess->id, 'product_cat', 'ARRAY_A' );

						if (!$object) {
							continue 2;
						}

						$category = array(
							'id' => $sess->id,
							'name' => $object['name'],
							'cnt' => 1
						);
						$cache[2][$sess->id] = $category;
					}
					$sessions[$sess->session_id]['views'][$sess->type.$sess->id] = array('time'=>$sess->date, 'details' => $category, 'type'=> 2);

					break;

					case 4:
					case 5:
					if (isset($cache[4][$sess->id])) {
						$product = $cache[4][$sess->id];
					} else {
						$object = wc_get_product($sess->id);
						if ($object) {
							$product = array(
								'id' => $sess->id,
								'name' => $object->get_name(),
								'price' => $object->get_price(),
								'cnt' => 1
							);
							$cache[4][$sess->id] = $product;
						}
					}
					$sessions[$sess->session_id]['views'][$sess->type.$sess->id] = array('time'=>$sess->date, 'details' => $product,'type'=> intval($sess->type));
					break;

					case 6:
					if (isset($cache[6][$sess->id])) {
						$product = $cache[6][$sess->id];
					} else {
						$product = array(
							'id' => $sess->id,
							'cnt' => 1
						);
						$cache[6][$sess->id] = $product;
					}
					$sessions[$sess->session_id]['views'][$sess->type.$sess->id] = array('time'=>$sess->date, 'details' => $product,'type'=> intval($sess->type));
					break;

					case 7:
					$product['name'] = 'Home page';

					if (isset($cache[7][$sess->id])) {
						$product = $cache[7][$sess->id];
						++$cache[7][$sess->id]['cnt'];
						++$product['cnt'];
					} else {
						$product['cnt'] = 1;
						$cache[7][$sess->id] = $product;
					}
					$sessions[$sess->session_id]['views'][$sess->type.$sess->id] = array('time'=>$sess->date, 'details' => $product,'type'=> intval($sess->type));
					break;


					case 10:
					$search['name'] = 'Search';

					if (!isset($search['cnt'])) {
						$search['cnt'] = 1;
					} else {
						++$search['cnt'];
					}
					$search['term'][] = $sess->extra;

					$sessions[$sess->session_id]['views'][$sess->type.$sess->id] = array('time'=>$sess->date, 'details' => $search,'type'=> intval($sess->type));
					break;

					case 17:
					$product['name'] = 'Page';

					if (isset($cache[17][$sess->id])) {
						$page = $cache[17][$sess->id];
						++$cache[17][$sess->id]['cnt'];
						++$page['cnt'];
					} else {
						$page['cnt'] = 1;
						$page['term'] = $sess->extra;
						$cache[17][$sess->id] = $page;
					}
					$sessions[$sess->session_id]['views'][$sess->type.$sess->id] = array('time'=>$sess->date, 'details' => $page,'type'=> intval($sess->type));
					break;


				}
			}

			return $sessions;
		}

		private static function get_user($user, $ip = 0) {
			if (is_numeric($user)) {
				$__visitor = WC()->api->WC_API_Customers->get_customer( absint($user) );
				if (!is_wp_error($__visitor)) {
					$visitor = sprintf("%s %s", $__visitor['customer']['first_name'],  $__visitor['customer']['last_name']);
					$city =  sprintf("%s, %s", $__visitor['customer']['billing_address']['city'], isset( WC()->countries->states[$__visitor['customer']['billing_address']['country']][$__visitor['customer']['billing_address']['state']]) ?  WC()->countries->states[$__visitor['customer']['billing_address']['country']][$__visitor['customer']['billing_address']['state']] : $__visitor['customer']['billing_address']['country']);
				} else {
					$visitor = esc_html__('Visitor', 'energyplus');
					$city = $ip;
				}
			} else {
				$visitor = esc_html__('Visitor', 'energyplus');
				$city = $ip;
			}

			return sprintf("%s <span class='__A__City'>%s</span>", $visitor, $city);
		}

		/**
		* Widget's settings
		*
		* @since  1.0.0
		* @param  array    $args
		* @return array
		*/

		public static function settings ( $args ) {
			return array(
				'dimensions' => array(
					'type' => 'wh',
					'title' => esc_html__('Dimensions', 'energyplus'),
					'values' => array(
						array(
							'title' => 'W',
							'id' => 'w',
							'values'=> array(3,4,5,6,7,8,9,10)
						),
						array(
							'title' => 'H',
							'id' => 'h',
							'values'=> array(2,3,4,5,6,7,8,9,10)
						),
					)
				),
				'range' => (isset($settings['range'])) ? $settings['range'] : 'online'
			);
		}
	}

	?>
