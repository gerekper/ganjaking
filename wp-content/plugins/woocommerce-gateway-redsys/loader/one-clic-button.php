<?php
/**
 * One Click Buy Button.
 *
 * @package WooCommerce Redsys Gateway
 * @since 23.2.0
 * @author José Conti.
 * @link https://joseconti.com
 * @link https://redsys.joseconti.com
 * @link https://woo.com/products/redsys-gateway/
 * @license GNU General Public License v3.0
 * @license URI: http://www.gnu.org/licenses/gpl-3.0.html
 * @copyright 2013-2024 José Conti.
 */

defined( 'ABSPATH' ) || exit;

/**
 * Get customer shipping or billing zone.
 */
function redsys_get_customer_shipping_or_billing_zone() {
	$customer          = WC()->customer;
	$shipping_country  = $customer->get_shipping_country();
	$shipping_state    = $customer->get_shipping_state();
	$shipping_postcode = $customer->get_shipping_postcode();

	// Si no existe una dirección de envío, usa la dirección de facturación.
	if ( empty( $shipping_country ) ) {
		$shipping_country  = $customer->get_billing_country();
		$shipping_state    = $customer->get_billing_state();
		$shipping_postcode = $customer->get_billing_postcode();
	}

	// Retorna la zona de envío que coincide con la dirección del cliente.
	return WC_Shipping_Zones::get_zone_matching_package(
		array(
			'destination' => array(
				'country'  => $shipping_country,
				'state'    => $shipping_state,
				'postcode' => $shipping_postcode,
			),
		)
	);
}
/**
 * Get shipping costs for a product.
 *
 * @param WC_Product       $product      Product.
 * @param WC_Shipping_Zone $shipping_zone Shipping zone.
 * @return array Shipping costs.
 */
function redsys_calculate_shipping_costs_for_product( $product, $shipping_zone ) {
	$costs = array();
	$log   = new WC_Logger();

	if ( ! is_a( $product, 'WC_Product' ) ) {
		return $costs;
	}

	$shipping_country  = WC()->customer->get_shipping_country();
	$shipping_state    = WC()->customer->get_shipping_state();
	$shipping_postcode = WC()->customer->get_shipping_postcode();

	if ( empty( $shipping_country ) || empty( $shipping_state ) || empty( $shipping_postcode ) ) {

		$shipping_country  = WC()->customer->get_billing_country();
		$shipping_state    = WC()->customer->get_billing_state();
		$shipping_postcode = WC()->customer->get_billing_postcode();
	}

	$package = array(
		'contents'      => array(
			$product->get_id() => array(
				'product_id' => $product->get_id(),
				'quantity'   => 1,
				'data'       => $product,
			),
		),
		'contents_cost' => $product->get_price(),
		'destination'   => array(
			'country'  => $shipping_country,
			'state'    => $shipping_state,
			'postcode' => $shipping_postcode,
		),
	);
	/**
	 * Filter the shipping methods that are accepted for One Click Buy.
	 *
	 * @since 24.2.0
	 */
	$accepted_methods  = apply_filters( 'redsys_accepted_shipping_methods', get_option( 'woocommerce_redsys_shipping_methods', array() ) );
	$available_methods = $shipping_zone->get_shipping_methods( true );

	foreach ( $available_methods as $shipping_method ) {
		$method_base_id = $shipping_method->id;
		$instance_id    = $shipping_method->get_instance_id();

		// Construir el method_id completo (base_id:instance_id).
		$method_id = $instance_id ? $method_base_id . ':' . $instance_id : $method_base_id;

		if ( in_array( $method_base_id, $accepted_methods, true ) && $shipping_method->is_available( $package ) ) {
			$shipping_method->calculate_shipping( $package );
			$rates = $shipping_method->rates;

			if ( ! empty( $rates ) ) {
				foreach ( $rates as $rate ) {
					$label        = $rate->label;
					$cost         = $rate->cost;
					$label_base64 = base64_encode( $label ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
					$log->add( 'redsys-pay', 'label: ' . $label );
					$log->add( 'redsys-pay', 'cost: ' . $cost );
					$log->add( 'redsys-pay', 'label_base64: ' . $label_base64 );

					$label_text            = wp_strip_all_tags( $label );
					$cost_text             = $cost > 0 ? wc_price( $cost ) : __( 'Free', 'woocommerce' );
					$value                 = $method_id . '-' . $label_base64 . '-' . ( $cost > 0 ? (int) ( $cost * 100 ) : 0 );
					$costs[ $method_id ][] = $label_text . ' - ' . $cost_text . '|' . $value;
				}
			}
		}
	}

	return $costs;
}
/**
 * Add One Click Buy Buttons.
 */
function redsys_add_one_click_buy_button() {
		global $product;

		$enabled = get_option( 'redsys_enable_one_click_button', false );

	if ( 'yes' === $enabled && is_user_logged_in() ) {

		// Inicializa $is_virtual a false.
		$is_virtual  = false;
		$product_id  = $product->get_id();
		$show_button = false;

		// Verifica si el producto es una instancia de WC_Product_Variable.
		$is_variable = $product instanceof WC_Product_Variable;

		// Si el producto es variable, revisa si todas las variaciones son virtuales.
		if ( $is_variable ) {
			$variations = $product->get_available_variations();
			$is_virtual = true;  // Asume que todas las variaciones son virtuales hasta que se demuestre lo contrario.
			foreach ( $variations as $variation ) {
				$variation_product = wc_get_product( $variation['variation_id'] );
				if ( ! $variation_product->is_virtual() ) {
					$is_virtual = false;
					break;
				}
			}
		} else {
			// Si el producto no es variable, revisa si es virtual.
			$is_virtual = $product->is_virtual();
		}

		$token_type = WCRed()->check_product_for_subscription( $product_id );

		// Si el producto es virtual o si es un producto variable con todas las variaciones virtuales, ejecuta el código siguiente.
		if ( 'C' === $token_type || 'R' === $token_type ) {
			if ( 'R' === $token_type ) {
				$tokens_type = WCRed()->get_redsys_users_token( 'R', 'id' );
			} else {
				$tokens_type = WCRed()->get_redsys_users_token( 'C', 'id' );
			}
			if ( ! empty( $tokens_type ) ) {
				$user_id = get_current_user_id();
				$tokens  = WC_Payment_Tokens::get_customer_tokens( $user_id, 'redsys' );
				foreach ( $tokens as $token ) {
					if ( WCRed()->get_token_type( $token->get_id() ) === $token_type ) {
						$brand    = $token->get_card_type();
						$last4    = $token->get_last4();
						$token_id = $token->get_id();
					}
				}
				echo '
						<style>
							#one-click-buy-button {
								background-color: #f39c12;
								border: none;
								color: white;
								padding: 15px 32px;
								padding-left: 60px; /* Ajustado para dar espacio a la imagen */
								text-align: center;
								text-decoration: none;
								display: inline-block;
								font-size: 16px;
								margin: 4px 2px;
								cursor: pointer;
								background-image: url(' . esc_html( REDSYS_PLUGIN_URL_P ) . 'assets/images/visa-mastercard.svg);
								background-position: 10px center; /* Ajusta la posición de la imagen */
								background-repeat: no-repeat; /* Evita que la imagen se repita */
								background-size:48px 24px; /* Ajusta el tamaño de la imagen */
							}
						</style>
					';
				if ( $is_virtual ) {
					$show_button = true;
				} else {
					$shipping_zone = redsys_get_customer_shipping_or_billing_zone();

					// Calcular costos de envío para el producto.
					$shipping_costs = redsys_calculate_shipping_costs_for_product( $product, $shipping_zone );

					if ( ! empty( $shipping_costs ) ) {
						$options_html = '';
						foreach ( $shipping_costs as $method_id => $method_details ) {
							foreach ( $method_details as $detail ) {
								list($label, $value) = explode( '|', $detail );
								$label_safe_html     = wp_kses_post( $label );
								$options_html       .= '<option value="' . esc_attr( $value ) . '">' . $label_safe_html . '</option>';
							}
						}
						$show_button = ! empty( $options_html );
					}

					// Mostrar el desplegable si hay métodos de envío.
					if ( $show_button ) {

						echo '<table class="variations" cellspacing="0" role="presentation">';
						echo '<tbody>';
						echo '<tr>';
						echo '<th class="label">';
						echo '<label for="one-click-shipping-method">' . esc_html__( 'Shipping Method (approx. price)', 'woocommerce-redsys' ) . '</label>';
						echo '</th>';
						echo '<td class="redsys-hook-shipping-before">';
						/**
						 * Action hook before the shipping dropdown is displayed for a product.
						 *
						 * @since 24.2.0
						 */
						do_action( 'redsys_before_shipping_dropdown_product' );
						echo '</td>';
						echo '<td class="value">';
						echo '<select id="one-click-shipping-method">';
						$allowed_html = array(
							'option' => array(
								'value' => array(),
							),
						);
						echo wp_kses( $options_html, $allowed_html );
						echo '</select>';
						echo '</td>';
						echo '<td class="redsys-hook-shipping-after">';
						/**
						 * Action hook after the shipping dropdown is displayed for a product.
						 *
						 * @since 24.2.0
						 */
						do_action( 'redsys_after_shipping_dropdown_product' );
						echo '</td>';
						echo '</tr>';
						echo '</tbody>';
						echo '</table>';
					}
				}
				if ( $show_button ) {
					echo '<input type="hidden" id="redsys_token_id" value="' . esc_html( $token_id ) . '">';
					echo '<input type="hidden" id="billing_agente_navegador" value="">';
					echo '<input type="hidden" id="billing_idioma_navegador" value="">';
					echo '<input type="hidden" id="billing_altura_pantalla" value="">';
					echo '<input type="hidden" id="billing_anchura_pantalla" value="">';
					echo '<input type="hidden" id="billing_profundidad_color" value="">';
					echo '<input type="hidden" id="billing_diferencia_horaria" value="">';
					echo '<input type="hidden" id="billing_http_accept_headers" value="">';
					echo '<input type="hidden" id="billing_tz_horaria" value="">';
					echo '<input type="hidden" id="billing_js_enabled_navegador" value="">';
					echo '<input type="hidden" id="redsys_token_type" value="' . esc_html( $token_type ) . '">';
					/**
					 * Hook: redsys_before_buy_now_button_product.
					 *
					 * @since 24.2.0
					 */
					do_action( 'redsys_before_buy_now_button_product' );
					echo '<button type="button" id="one-click-buy-button">' . esc_html__( 'Buy now with', 'woocommerce-redsys' ) . ' ' . esc_html( $brand ) . ' ' . esc_html__( 'ending in', 'woocommerce-redsys' ) . ' ' . esc_html( $last4 ) . '</button>';
					/**
					 * Hook: redsys_after_buy_now_button_product.
					 *
					 * @since 24.2.0
					 */
					do_action( 'redsys_after_buy_now_button_product' );
				}
				?>
					<script type="text/javascript">
						// Script necesario para capturar los datos a enviar a Redsys por la PSD2
						var RedsysDate = new Date();
						if (document.getElementById('billing_agente_navegador')) {
							document.getElementById('billing_agente_navegador').value = btoa(navigator.userAgent);
						}
						if (document.getElementById('billing_idioma_navegador')) {
							document.getElementById('billing_idioma_navegador').value = navigator.language;
						}
						if (document.getElementById('billing_js_enabled_navegador')) {
							document.getElementById('billing_js_enabled_navegador').value = navigator.javaEnabled();
						}
						if (document.getElementById('billing_altura_pantalla')) {
							document.getElementById('billing_altura_pantalla').value = screen.height;
						}
						if (document.getElementById('billing_anchura_pantalla')) {
							document.getElementById('billing_anchura_pantalla').value = screen.width;
						}
						if (document.getElementById('billing_profundidad_color')) {
							document.getElementById('billing_profundidad_color').value = screen.colorDepth;
						}
						if (document.getElementById('billing_diferencia_horaria')) {
							document.getElementById('billing_diferencia_horaria').value = RedsysDate.getTimezoneOffset();
						}
						if (document.getElementById('billing_tz_horaria')) {
							document.getElementById('billing_tz_horaria').value = RedsysDate.getTimezoneOffset();
						}
				<?php
				if ( isset( $_SERVER['HTTP_ACCEPT'] ) ) {
					?>
						if ( document.getElementById( 'billing_http_accept_headers') ) {
							document.getElementById( 'billing_http_accept_headers').value = btoa( <?php echo '"' . esc_html( wp_unslash( $_SERVER['HTTP_ACCEPT'] ) ) . '"'; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized ?> );
						}
						<?php
				} else {
					?>
					if ( document.getElementById( 'billing_http_accept_headers') ) {
						document.getElementById( 'billing_http_accept_headers').value = btoa( "text\/html,application\/xhtml+xml,application\/xml;q=0.9,*\/*;q=0.8" );
					}
					<?php
				}
				?>
				</script>
				<?php
			}
		}
	}
}
add_action( 'woocommerce_after_add_to_cart_form', 'redsys_add_one_click_buy_button' );

/**
 * Enqueue custom scripts for Pay With one Click.
 */
function redsys_enqueue_custom_scripts() {
	if ( is_product() && ! is_admin() ) {
		wp_enqueue_script(
			'redsys-one-click-buy',
			REDSYS_PLUGIN_URL_P . 'assets/js/redsys-one-click-buy.js',
			array( 'jquery' ),
			REDSYS_VERSION,
			true
		);
		wp_localize_script(
			'redsys-one-click-buy',
			'redsys_pay_one',
			array(
				'ajax_url'   => admin_url( 'admin-ajax.php' ),
				'product_id' => get_the_ID(),
			)
		);
	}
}
add_action( 'wp_enqueue_scripts', 'redsys_enqueue_custom_scripts' );
	/**
	 * Handle One Click Buy.
	 */
function redsys_handle_one_click_buy() {

		$data       = array();
		$product_id = intval( $_POST['product_id'] );
		$qty        = intval( $_POST['qty'] );
		$token      = sanitize_text_field( $_POST['token_id'] );
		$order_id   = false;
		$log        = new WC_Logger();

	if ( ! empty( $_POST['billing_http_accept_headers'] ) ) {
		$headers                = base64_decode( sanitize_text_field( wp_unslash( $_POST['billing_http_accept_headers'] ) ) ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode
		$data['_accept_haders'] = sanitize_text_field( $headers );
	}
	if ( ! empty( $_POST['billing_agente_navegador'] ) ) {
		$agente                                  = base64_decode( sanitize_text_field( wp_unslash( $_POST['billing_agente_navegador'] ) ) ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode
		$data['_billing_agente_navegador_field'] = sanitize_text_field( $agente );
	}
	if ( ! empty( $_POST['billing_idioma_navegador'] ) ) {
		$data['_billing_idioma_navegador_field'] = sanitize_text_field( wp_unslash( $_POST['billing_idioma_navegador'] ) );
	}
	if ( ! empty( $_POST['billing_altura_pantalla'] ) ) {
		$data['_billing_altura_pantalla_field'] = sanitize_text_field( wp_unslash( $_POST['billing_altura_pantalla'] ) );
	}
	if ( ! empty( $_POST['billing_anchura_pantalla'] ) ) {
		$data['_billing_anchura_pantalla_field'] = sanitize_text_field( wp_unslash( $_POST['billing_anchura_pantalla'] ) );
	}
	if ( ! empty( $_POST['billing_profundidad_color'] ) ) {
		$data['_billing_profundidad_color_field'] = sanitize_text_field( wp_unslash( $_POST['billing_profundidad_color'] ) );
	}
	if ( ! empty( $_POST['billing_diferencia_horaria'] ) ) {
		$data['_billing_diferencia_horaria_field'] = sanitize_text_field( wp_unslash( $_POST['billing_diferencia_horaria'] ) );
	}
	if ( ! empty( $_POST['billing_tz_horaria'] ) ) {
		$data['_billing_tz_horaria_field'] = sanitize_text_field( wp_unslash( $_POST['billing_tz_horaria'] ) );
	}
	if ( ! empty( $_POST['billing_js_enabled_navegador'] ) ) {
		$data['_billing_js_enabled_navegador_field'] = sanitize_text_field( wp_unslash( $_POST['billing_js_enabled_navegador'] ) );
	}
	if ( ! empty( $_POST['shipping_method'] ) ) {
		$shipping_method = sanitize_text_field( wp_unslash( $_POST['shipping_method'] ) );
	} else {
		$shipping_method = '';
	}
	if ( ! empty( $_POST['redsys_token_type'] ) ) {
		$token_type = sanitize_text_field( wp_unslash( $_POST['redsys_token_type'] ) );
	} else {
		$token_type = 'no';
	}
	$log->add( 'redsys-pay', 'token_type: ' . $token_type );
	$log->add( 'redsys-pay', 'token: ' . $token );
	$log->add( 'redsys-pay', 'shipping_method: ' . $shipping_method );
	$log->add( 'redsys-pay', 'product_id: ' . $product_id );
	$log->add( 'redsys-pay', 'qty: ' . $qty );
	$log->add( 'redsys-pay', 'data: ' . print_r( $data, true ) );

	$order_id = WCRed_pay()->create_order( get_current_user_id(), $product_id, $qty, $shipping_method );

	if ( $order_id ) {
		set_transient( $order_id . '_redsys_save_token', 'no', 36000 );
		set_transient( $order_id . '_redsys_token_type', $token_type, 36000 );
		set_transient( $order_id . '_redsys_use_token', $token, 36000 );
		WCRed()->update_order_meta( $order_id, $data );
		$payment_result = WCRed_pay()->process_payment( $order_id, $token );

		if ( 'success' === $payment_result['result'] || 'ChallengeRequest' === $payment_result['result'] || 'threeDSMethodURL' === $payment_result['result'] ) {
				wp_send_json_success(
					array(
						'order_id'     => $order_id,
						'redirect_url' => $payment_result['redirect'],
					)
				);
		} else {
				wp_send_json_error(
					array( 'message' => 'Hubo un problema al procesar el pago: ' . $payment_result['error_message'] )
				);
		}
	} else {
		wp_send_json_error( array( 'message' => 'No se pudo crear el pedido.' ) );
	}
}
add_action( 'wp_ajax_redsys_one_click_buy', 'redsys_handle_one_click_buy' );
