<?php
/**
 * One Click Buy Button.
 *
 * @package WooCommerce Redsys Gateway
 * @version 23.2.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Add One Click Buy Buttons.
 */
function add_one_click_buy_button() {
	global $product;

	$enabled = get_option( 'redsys_enable_one_click_button', false );

	if ( 'yes' === $enabled && is_user_logged_in() ) {

		// Inicializa $is_virtual a false.
		$is_virtual = false;
		$product_id = $product->get_id();

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
		if ( $is_virtual && ( 'C' === $token_type || 'R' === $token_type ) ) {
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
				echo '<button type="button" id="one-click-buy-button">' . esc_html__( 'Buy now with', 'woocommerce-redsys' ) . ' ' . esc_html( $brand ) . ' ' . esc_html__( 'ending in', 'woocommerce-redsys' ) . ' ' . esc_html( $last4 ) . '</button>';
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
add_action( 'woocommerce_after_add_to_cart_form', 'add_one_click_buy_button' );

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
	if ( ! empty( $_POST['redsys_token_type'] ) ) {
		$token_type = sanitize_text_field( wp_unslash( $_POST['redsys_token_type'] ) );
	} else {
		$token_type = 'no';
	}

	$order_id = WCRed_pay()->create_order( get_current_user_id(), $product_id, $qty );

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
