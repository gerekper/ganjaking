<?php
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

/**
 * This class defines all ajax callbacks in the plugin.
 */

if (!class_exists('WCCS_Ajax')) {

	class WCCS_Ajax {
	
		
		public function __construct() {
			// add new currency in settings page
			add_action('wp_ajax_wccs_add_currency', array( $this, 'wccs_add_currency_callback' ));
			
			// update all currencies rates by api
			add_action('wp_ajax_wccs_update_all', array( $this, 'wccs_update_all_callback' ));
			
			// update single currency rate by api
			add_action('wp_ajax_wccs_update_single_rate', array( $this, 'wccs_update_single_rate_callback' ));

			add_action('wp_ajax_wccs_add_zone_pricing', array( $this, 'wccs_add_zone_pricing' ));

			add_action('wp_ajax_wccs_delete_zone_pricing', array( $this, 'wccs_delete_zone_pricing' ));

			add_action('wp_ajax_wccs_toggle_zone_pricing', array( $this, 'wccs_toggle_zone_pricing' ));

			add_action('wp_ajax_wccs_check_currency_before_early_renew', array( $this, 'wccs_check_currency_before_early_renew' ));
		}

		public function wccs_check_currency_before_early_renew() {

			$result = array(
			'status' => 'success',
			'msg' => '',
			);

			// if ( isset( $_POST['nonce'] ) && ! wp_verify_nonce( sanitize_text_field( $_POST['nonce'] ), 'wccs_early_renewal_subscription' ) ) {
			//     exit('Unauthorized Request!');
			// }
			check_ajax_referer('wccs_early_renewal_subscription', 'nonce');

			if (isset($_POST['subscription_renewal_id']) && ! empty(sanitize_text_field($_POST['subscription_renewal_id'])) ) {
				$renewal_id = sanitize_text_field($_POST['subscription_renewal_id']);
				$renewal_order = wc_get_order($renewal_id);
				$order_currency = $renewal_order->get_meta('_order_currency', true);
				// $order_currency = get_post_meta( $renewal_id, '_order_currency', true );
				$storage = new WCCS_Storage(get_option('wccs_currency_storage', 'transient'));
				$current_currency = $storage->get_val('wccs_current_currency');
				if (! empty($order_currency) && $current_currency != $order_currency ) {
					// current currency is not equal to order currency. Before renewal you need to change the currency.
					$result['status'] = 'failed';
					$result['msg'] = 'Currency should be same as order currency ( ' . $order_currency . ' ).';
				}
			}

			echo wp_json_encode($result);
			wp_die();
		}

		public function wccs_toggle_zone_pricing() {
			
			check_ajax_referer('wccs', 'zp_nonce');

			$post = $_POST;

			if (isset($post['zp_toggle']) ) {
				update_option('wccs_zp_toggle', $post['zp_toggle']);
				wp_die('done');
			}
		}

		public function wccs_delete_zone_pricing() {
			
			check_ajax_referer('wccs', 'zp_nonce');

			$post = $_POST;

			if (isset($post['id']) && '' != $post['id'] ) {
				$id = $post['id'];

				// Get existing values from db.
				$wccs_zp_data = get_option('wccs_zp_data', false);
				if (false != $wccs_zp_data ) {
					if (isset($wccs_zp_data[ $id ]) ) {
						unset($wccs_zp_data[ $id ]);
						//pushed to db.
						update_option('wccs_zp_data', $wccs_zp_data);
						wp_die('done');
					}
				}
			}            
		}

		public function wccs_add_zone_pricing() {
			
			check_ajax_referer('wccs', 'zp_nonce');

			$post = $_POST;
			$html = '';

			if (isset($post['zp_name']) && isset($post['zp_countries']) && isset($post['zp_currency']) && isset($post['zp_rate']) && isset($post['zp_decimal']) && ! empty(trim($post['zp_name'])) && ! empty($post['zp_countries']) && ! empty($post['zp_currency']) && ! empty($post['zp_rate']) && '' != $post['zp_decimal'] ) {
				

				// Get existing values from db.
				$wccs_zp_data = get_option('wccs_zp_data', false);

				if (isset($post['id']) && '' != $post['id'] && isset($wccs_zp_data[ $post['id'] ]) ) {
					$wccs_zp_data[ $post['id'] ]['zp_name'] = $post['zp_name'];
					$wccs_zp_data[ $post['id'] ]['zp_countries'] = $post['zp_countries'];
					$wccs_zp_data[ $post['id'] ]['zp_currency'] = $post['zp_currency'];
					$wccs_zp_data[ $post['id'] ]['zp_rate'] = str_replace(',', '', $post['zp_rate']);
					$wccs_zp_data[ $post['id'] ]['zp_decimal'] = $post['zp_decimal'];
				} else {
					// new data
					$temp = array(
					'zp_name'      => $post['zp_name'],
					'zp_countries' => $post['zp_countries'],
					'zp_currency'  => $post['zp_currency'],
					'zp_rate'      => str_replace(',', '', $post['zp_rate']),
					'zp_decimal'  => $post['zp_decimal'],
					);

					if (false == $wccs_zp_data ) {
						$wccs_zp_data[0] = $temp;
					} else {
						array_push($wccs_zp_data, $temp);
					}
				}

				//pushed to db.
				update_option('wccs_zp_data', $wccs_zp_data);

				// get new data from db
				$wccs_zp_data = get_option('wccs_zp_data', false);

				if (false != $wccs_zp_data ) {
					foreach ( $wccs_zp_data as $key => $zp_data ) {
						$zp_countries = implode(', ', $zp_data['zp_countries']);
						$decimal = isset($zp_data['zp_decimal']) ? $zp_data['zp_decimal'] : 0;
						$html .= '<tr><td><span class="zp_name">' . $zp_data['zp_name'] . '</span><div class="wccs_zp_action"><a href="#" class="wccs_zp_edit" data-id="' . $key . '">Edit</a> | <a href="#" class="wccs_zp_delete" data-id="' . $key . '">Delete</a></div></td><td><span class="zp_countries">' . $zp_countries . '</span></td><td><span class="zp_currency">' . $zp_data['zp_currency'] . '</span></td><td><span class="zp_rate">' . wp_kses_post($zp_data['zp_rate']) . '</span></td><td><span class="zp_decimal">' . $decimal . '</span></td></tr>';
					}
				}                
				
			}
			wp_send_json( $html );
		}
		
		public function wccs_add_currency_callback() {
			$status = false;
			$html = '';

			// if ( isset( $_POST['nonce'] ) && !wp_verify_nonce(sanitize_text_field($_POST['nonce']), 'wccs') ) {
			//     exit( 'Not Authorized' );
			// }
			check_ajax_referer('wccs', 'nonce');

			if (isset($_POST['code']) && isset($_POST['label']) ) {
				$currency[sanitize_text_field($_POST['code'])] = array( 'label' => sanitize_text_field($_POST['label']) );

				//$html = wccs_get_currencies_list($currency);
				$html = '';

				if (!empty($currency) && count($currency) > 0) {
					
					foreach ($currency as $code => $info) {
						$symbol = get_woocommerce_currency_symbol($code);
						$flags = wccs_get_all_flags();
						
						$html .= '<tr>';
						
						$html .= '<td>' . $code . '</td>';
						
						$html .= '<td><input type="text" name="wccs_currencies[' . $code . '][label]" value="';
						if (isset($info['label'])) {
							   $html .= $info['label'];
						}
						$html .= '" required></td>';
						
						$html .= '<td><input class="wccs_w_100" type="text" name="wccs_currencies[' . $code . '][rate]" value="';
						if (isset($info['rate'])) {
							$html .= $info['rate'];
						}
						$html .= '"';
						if (get_option('wccs_update_type', 'fixed') == 'api') {
							$html .= ' readonly';
						} else {
							$html .= ' required';
						}
						$html .= '></td>';
						
						$html .= '<td><select name="wccs_currencies[' . $code . '][format]" class="wccs_w_150">';
						$html .= '<option value="left"';
						if (isset($info['format']) && 'left'==$info['format'] ) {
										  $html .= ' selected';
						}
						$html .= '>' . __('Left', 'wccs') . '</option>';
						$html .= '<option value="right"';
						if (isset($info['format']) && 'right'==$info['format'] ) {
									$html .= ' selected';
						}
						$html .= '>' . __('Right', 'wccs') . '</option>';
						$html .= '<option value="left_space"';
						if (isset($info['format']) && 'left_space'==$info['format'] ) {
							  $html .= ' selected';
						}
						$html .= '>' . __('Left with space', 'wccs') . '</option>';
						$html .= '<option value="right_space"';
						if (isset($info['format']) && 'right_space'==$info['format']) {
							$html .= ' selected';
						}
						$html .= '>' . __('Right with space', 'wccs') . '</option>';                                                        
						$html .= '</select></td>';

						$html .= '<td>';
						$html .= '<input class="wccs_w_50" maxlength="4" type="text" name="wccs_currencies[' . $code . '][symbol_prefix]" value="' . ( isset($info['symbol_prefix']) ? $info['symbol_prefix'] : '' ) . '">';
						$html .= '</td>';
						
						$html .= '<td><select name="wccs_currencies[' . $code . '][decimals]" class="wccs_w_50">';
						$html .= '<option value="0"';
						if (isset($info['decimals']) && '0'==$info['decimals']) {
							$html .= ' selected';
						}
						$html .= '>' . __('0', 'wccs') . '</option>';
						$html .= '<option value="1"';
						if (isset($info['decimals']) && '1'==$info['decimals']) {
							$html .= ' selected';
						}
						$html .= '>' . __('1', 'wccs') . '</option>';
						$html .= '<option value="2"';
						if (( !isset($info['decimals']) ) || ( isset($info['decimals']) && '2'==$info['decimals'] ) ) {
							$html .= ' selected';
						}
						$html .= '>' . __('2', 'wccs') . '</option>';
						$html .= '<option value="3"';
						if (isset($info['decimals']) && '3'==$info['decimals']) {
							$html .= ' selected';
						}
						$html .= '>' . __('3', 'wccs') . '</option>';
						$html .= '<option value="4"';
						if (isset($info['decimals']) && '4'==$info['decimals']) {
							$html .= ' selected';
						}
						$html .= '>' . __('4', 'wccs') . '</option>';
						$html .= '<option value="5"';
						if (isset($info['decimals']) && '5'==$info['decimals']) {
							$html .= ' selected';
						}
						$html .= '>' . __('5', 'wccs') . '</option>';
						$html .= '<option value="6"';
						if (isset($info['decimals']) && '6'==$info['decimals']) {
							$html .= ' selected';
						}
						$html .= '>' . __('6', 'wccs') . '</option>';
						$html .= '<option value="7"';
						if (isset($info['decimals']) && '7'==$info['decimals']) {
							$html .= ' selected';
						}
						$html .= '>' . __('7', 'wccs') . '</option>';
						$html .= '<option value="8"';
						if (isset($info['decimals']) && '8'==$info['decimals'] ) {
							$html .= ' selected';
						}
						$html .= '>' . __('8', 'wccs') . '</option>';
						$html .= '</select></td>';

						if (isset($info['rounding']) ) {
							$rounding = $info['rounding'];
						} else {
							$rounding = '0';
						}

						if (isset($info['charming']) ) {
							$charming = $info['charming'];
						} else {
							$charming = '0';
						}

						$html .= '<td><select name="wccs_currencies[' . $code . '][rounding]">
								<option value="0" ' . selected($rounding, '0', false) . '>' . esc_html__('none', 'wccs') . '</option>
								<option value="0.25" ' . selected($rounding, '0.25', false) . '>0.25</option>
								<option value="0.5" ' . selected($rounding, '0.5', false) . '>0.5</option>
								<option value="1" ' . selected($rounding, '1', false) . '>1</option>
								<option value="5" ' . selected($rounding, '5', false) . '>5</option>
								<option value="10" ' . selected($rounding, '10', false) . '>10</option>
							</select></td>';
						
						$html .= '<td><select name="wccs_currencies[' . $code . '][charming]">
								<option value="0" ' . selected($charming, '0', false) . '>' . esc_html__('none', 'wccs') . '</option>
								<option value="-0.01" ' . selected($charming, '-0.01', false) . '>-0.01</option>
								<option value="-0.05" ' . selected($charming, '-0.05', false) . '>-0.05</option>
								<option value="-0.10" ' . selected($charming, '-0.10', false) . '>-0.10</option>
							</select></td>';
						
						$html .= '<td><select class="flags" name="wccs_currencies[' . $code . '][flag]">';
						$html .= '<option value="">' . __('Choose Flag', 'wccs') . '</option>';
						
						$currency_countries = get_currency_countries($code);

						foreach ($flags as $country => $flag) {
							
							foreach ($currency_countries as $value) {
								if ($country == $value) {
									if (count($currency_countries) == 1 ) {
										$selected = 'selected="selected"';
									} else {
										$selected = '';
									}
									
									$html .= '<option value="' . strtolower($country) . '" ' . @$selected . ' data-prefix="';
									//$html .= "<img width='30' height='20' src='".$flag."'>";
									$html .= "<span class='wcc-flag flag-icon flag-icon-" . strtolower($country) . "'></span>";
									$html .= '"';
									if (isset($info['flag']) && strtolower($country)==$info['flag'] ) {
										$html .= ' selected';
									}
									$html .= '> (' . $country . ')</option>';
								}                
							}
										
						}
						$html .= '</select></td>';

						$html .= '<td class="wccs-payment-gateway-td">';
						$avl_payment_gateways  = get_all_active_payment_gateways();
						if (isset($avl_payment_gateways) && ! empty($avl_payment_gateways) ) {
							$html .= '<button data-code="' . $code . '" class="button button-secondary wccs-close">' . __('Hide Gateway', 'wccs') . '</button>';
							$html .= '<div class="wccs_payment_gateways_container"><ul>';
							foreach ( $avl_payment_gateways as $payment ) {
								// echo '<pre>';
								// print_r( $info['payment_gateways'] );
								// echo '</pre>';
								$checked = ( isset($info['payment_gateways']) && in_array($payment['id'], $info['payment_gateways']) ) ? 'checked="true"' : '';
								$html .= '<li> <label for="' . $code . '_' . $payment['id'] . '" > <input type="checkbox" id="' . $code . '_' . $payment['id'] . '" name="wccs_currencies[' . $code . '][payment_gateways][]" value="' . $payment['id'] . '" ' . $checked . ' />' . $payment['title'] . '</label></li>';
							}
							$html .= '</ul></div>';
						} else {
							$html .=  __('No payment Gateway is enabled', 'wccs');
						}

						$html .= '</td>';
						
						$html .= '<td>';
						$html .= '<div class="wccs_actions">';
						$html .= '<input type="hidden" name="wccs_currencies[' . $code . '][symbol]" value="' . $symbol . '">';
						if (get_option('wccs_update_type', 'fixed') == 'api') {
							$html .= '<a href="javascript:void(0);" title="' . __('Update rate', 'wccs') . '" class="wccs_update_rate" data-code="' . $code . '"><i class="dashicons dashicons-update"></i></a>';
						}
						$html .= '<span title="' . __('Sort', 'wccs') . '" style="cursor:grab;"><i class="dashicons dashicons-move"></i></span>';
						$html .= '<a href="javascript:void(0);" title="' . __('Remove', 'wccs') . '" class="wccs_remove_currency" data-value="' . $code . '" data-label="';
						if (isset($info['label']) ) {
							$html .= $info['label'];
						}
						$html .= '"><i class="dashicons dashicons-trash"></i></a>';
						$html .= '</div>';
						$html .= '</td>';
						
						$html .= '</tr>';
					}
				}
				
				if ($html) {
					$status = true;
				}
				
				print_r(json_encode(array( 'status' => $status, 'html' => $html )));
			}

			wp_die(); // this is required to terminate immediately and return a proper respons
		}
		
		public function wccs_update_all_callback() {
			$status = true;
			
			WCCS_Cron::wccs_update_rates_callback(false);
			
			$currencies = get_option('wccs_currencies', array());
			
			$rates = array();
			foreach ($currencies as $code => $info) {
				$rates[$code] = $info['rate'];
			}
			
			wp_send_json(array( 'status' => $status, 'rates' => $rates ));

			wp_die(); // this is required to terminate immediately and return a proper respons
		}
		
		public function wccs_update_single_rate_callback() {

			// if ( isset( $_POST['nonce'] ) && !wp_verify_nonce(sanitize_text_field($_POST['nonce']), 'wccs') ) {
			//     exit( 'Not Authorized' );
			// }
			check_ajax_referer('wccs', 'nonce');

			if (isset($_POST['code']) ) {
				$status = false;
				$code = sanitize_text_field($_POST['code']);
				
				$latest = wccs_get_exchange_rates($code);
				if (!isset($latest['error'])) {
					if (isset($latest['rates'][$code])) {
						$status = true;
						$rate = $latest['rates'][$code];
						$currencies = get_option('wccs_currencies', array());
						if (isset($currencies[$code]['rate'])) {
							   $currencies[$code]['rate'] = $rate;
							   update_option('wccs_currencies', $currencies);
						}
					}
				}
				
				print_r(json_encode(array( 'status' => $status, 'rate' => $rate )));
			}

			wp_die(); // this is required to terminate immediately and return a proper respons
		}
	}
	
	$wccs_ajax = new WCCS_Ajax();
}
