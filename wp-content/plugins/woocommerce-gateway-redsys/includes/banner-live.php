<?php
/**
 * Banner Live
 *
 * @package WooCommerce Redsys Gateway
 * @since 12.0.0
 * @author José Conti.
 * @link https://joseconti.com
 * @link https://redsys.joseconti.com
 * @link https://woo.com/products/redsys-gateway/
 * @license GNU General Public License v3.0
 * @license URI: http://www.gnu.org/licenses/gpl-3.0.html
 * @copyright 2013-2024 José Conti.
 */

/**
 * Redsys add noticer banner live
 */
function redsys_add_notice_banner_live() {

	$lang = get_user_locale( get_current_user_id() );

	switch ( $lang ) {
		case 'es_ES':
			$url = 'https://api.joseconti.com/banner-live.php';
			break;
		case 'es_AR':
			$url = 'https://api.joseconti.com/banner-live.php';
			break;
		case 'es_CL':
			$url = 'https://api.joseconti.com/banner-live.php';
			break;
		case 'es_CO':
			$url = 'https://api.joseconti.com/banner-live.php';
			break;
		case 'es_CR':
			$url = 'https://api.joseconti.com/banner-live.php';
			break;
		case 'es_DO':
			$url = 'https://api.joseconti.com/banner-live.php';
			break;
		case 'es_EC':
			$url = 'https://api.joseconti.com/banner-live.php';
			break;
		case 'es_GT':
			$url = 'https://api.joseconti.com/banner-live.php';
			break;
		case 'es_HN':
			$url = 'https://api.joseconti.com/banner-live.php';
			break;
		case 'es_MX':
			$url = 'https://api.joseconti.com/banner-live.php';
			break;
		case 'es_PE':
			$url = 'https://api.joseconti.com/banner-live.php';
			break;
		case 'es_PR':
			$url = 'https://api.joseconti.com/banner-live.php';
			break;
		case 'es_UY':
			$url = 'https://api.joseconti.com/banner-live.php';
			break;
		case 'es_VE':
			$url = 'https://api.joseconti.com/banner-live.php';
			break;
		case 'en_US':
			$url = 'https://api.joseconti.com/banner-live-en.php';
			break;
		case 'en_AU':
			$url = 'https://api.joseconti.com/banner-live-en.php';
			break;
		case 'en_CA':
			$url = 'https://api.joseconti.com/banner-live-en.php';
			break;
		case 'en_NZ':
			$url = 'https://api.joseconti.com/banner-live-en.php';
			break;
		case 'en_ZA':
			$url = 'https://api.joseconti.com/banner-live-en.php';
			break;
		case 'en_GB':
			$url = 'https://api.joseconti.com/banner-live-en.php';
			break;
		case 'bal':
			$url = 'https://api.joseconti.com/banner-live-ca.php';
			break;
		case 'ca':
			$url = 'https://api.joseconti.com/banner-live-ca.php';
			break;
	}
	if ( ! $url ) {
		$url = 'https://api.joseconti.com/banner-live-en.php';
	}
	$data = wp_remote_get( $url, array( 'timeout' => 5 ) );
	if ( is_wp_error( $data ) ) {
		return;
	}
	$array_respuesta = json_decode( $data['body'], true );

	$new_id      = $array_respuesta['ID'];
	$version     = $array_respuesta['version'];
	$titulo      = $array_respuesta['titulo'];
	$description = $array_respuesta['description'];
	$image       = $array_respuesta['imagen'];
	$button_link = $array_respuesta['button_link'];
	$button_text = $array_respuesta['button_text'];

	if ( 'hide' === $new_id || ! $new_id ) {
		return;
	}
	if ( 'hide' === $button_link ) {
		$button_link = false;
	}
	if ( 'hide' === $button_text ) {
		$button_text = false;
	}

	$id = get_option( 'redsys-id-live-banner' );

	if ( '' === $image ) {
		$image = REDSYS_PLUGIN_URL_P . 'assets/images/redsys-woo-notice.png';
	}

	if ( $new_id !== $id ) {
		if ( isset( $_REQUEST['redsys-hide-live-banner'] ) && 'hide-new-version-redsys' === $_REQUEST['redsys-hide-live-banner'] && isset( $_REQUEST['_redsys_hide_banner_live_nonce'] ) ) {
			$nonce = sanitize_text_field( wp_unslash( $_REQUEST['_redsys_hide_banner_live_nonce'] ) );
			if ( wp_verify_nonce( $nonce, 'redsys_hide_banner_live_nonce' ) ) {
				update_option( 'redsys-id-live-banner', $new_id );
			}
		} else {
			?>
			<div id="message" class="updated woocommerce-message woocommerce-redsys-messages">
				<div class="logo-redsys-notice">
					<img src="<?php echo wp_kses( $image, 'data' ); ?>" alt="Logo Plugn Redsys" height="100" width="100">
				</div>
				<div class="contenido-redsys-notice">
					<a class="woocommerce-message-close notice-dismiss" style="top:0;" href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'redsys-hide-live-banner', 'hide-new-version-redsys' ), 'redsys_hide_banner_live_nonce', '_redsys_hide_banner_live_nonce' ) ); ?>"><?php esc_html_e( 'Dismiss', 'woocommerce-redsys' ); ?></a>
					<p>
						<h3>
							<?php echo esc_html( $titulo ); ?>
						</h3>
					</p>
					<p>
						<?php echo esc_html( $description ); ?>
					</p>
					<?php if ( $button_link && $button_text ) { ?>
					<p class="submit">
						<a href="<?php echo esc_url( $button_link ); ?>" class="button-primary" target="_blank"><?php echo esc_html( $button_text ); ?></a>
					</p>
					<?php } ?>
				</div>
			</div>
			<?php
		}
	}
}
// add_action( 'admin_notices', 'redsys_add_notice_banner_live' );
