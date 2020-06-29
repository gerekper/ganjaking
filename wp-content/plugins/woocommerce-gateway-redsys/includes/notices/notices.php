<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

function redsys_woocommerce_gateway_redsys_init_check() {
	$class   = 'error';
	$message = '<span class="dashicons dashicons-dismiss"></span>' . ' ' . __( 'WARNING: Please, deactivate my WooCommerce Redsys Gateway Light version before activate WooCommerce Gateway Redsys (by Jos&eacute; Conti & WooCommerce.com).', 'woocommerce-redsys' );
	echo '<div class="' . esc_attr( $class ) . '"> <p>' . esc_html( $message ) . '</p></div>';
}

function redsys_admin_notice_lite_version() {
	if ( is_plugin_active( 'woo-redsys-gateway-light/woocommerce-redsys.php' ) ) {
		add_action( 'admin_notices', 'redsys_woocommerce_gateway_redsys_init_check' );
	}
}
add_action( 'admin_init', 'redsys_admin_notice_lite_version', 0 );

require_once REDSYS_PLUGIN_PATH . 'includes/persist-admin-notices-dismissal.php';

add_action( 'admin_init', array( 'PAnD', 'init' ) );

function redsys_ask_for_twitt() {

	if ( ! PAnD::is_admin_notice_active( 'notice-redsys-ask-for-twitt-forever' ) ) {
		return;
	}

	$activation_date = get_option( 'redsys-woocommerce-redsys-twitt' );

	if ( ! $activation_date ) {
		update_option( 'redsys-woocommerce-redsys-twitt', time() );
		$activation_date = get_option( 'redsys-woocommerce-redsys-twitt' );
	}
	$activation_date_30 = $activation_date + ( 30 * 24 * 60 * 60 );

	if ( time() > $activation_date_30 ) {
		$class   = 'notice notice-info is-dismissible';
		$message = '<a href="https://twitter.com/home?status=Utilizo%20el%20plugin%20premium%20de%20%23Redsys%20de%20WooCommerce%5B.%5Dcom%20de%20%40josecontic.%0ATiene%20todas%20las%20opciones%20posible%20de%20configuraci%C3%B3n%20para%20utilizar%20Redsys%20al%20l%C3%ADmite.%0A%C3%89chale%20un%20ojo%0Ahttps%3A//woocommerce.com/products/redsys-gateway/" target="_blank">Twitter</a> and/or <a href="https://www.facebook.com/sharer/sharer.php?u=https%3A//woocommerce.com/products/redsys-gateway/" target="_blank">Facebook</a>';
		printf( '<div data-dismissible="notice-redsys-ask-for-twitt-forever" class="%1$s"><p>', esc_attr( $class ) );
		printf( __( '<p>You\'ve been using WooCommerce.com&apos;s Redsys plugin for over 30 days. I would greatly appreciate it if you could share your experience on %s.</p><p>Thanks a lot!</p>', 'woocommerce-redsys' ), $message );
		echo '</p></div>';
	}

}
add_action( 'admin_notices', 'redsys_ask_for_twitt' );

function redsys_woo_help_admin_notice() {
	if ( ! PAnD::is_admin_notice_active( 'redsys-woo-help-admin-notice-forever' ) ) {
		return;
	}

	$class = 'notice notice-info is-dismissible';
	$message = '<a href="https://woocommerce.com/my-account/create-a-ticket/" target="_blank">WooCommerce.com</a>';


	printf( '<div data-dismissible="redsys-woo-help-admin-notice-forever" class="%1$s"><p>', esc_attr( $class ) );
	printf( __( '<p><strong>PLEASE READ THIS:</strong> Thank you very much for purchasing the Redsys extension from WooCommerce.com.</p><p>Before closing this notice, wait until you test the plugin and everything works. If your orders are kept on waiting for Redsys payment, please open a ticket in %s Support (select "Help with my Extensions" - "Redsys Gateway"), it has solution and it is not the fault of the plugin.</p><p>You can contact me 7 days a week and I&apos;ll get back to you. If you give me the access data, I can fix your installation without problems.</p><p>If you wish, you can contact in Spanish, since I, Jos&eacute; Conti, give the support directly from the plugin.</p><p>Some options may not work if they have not been activated in Redsys. If something doesn&apos;t work for you, like 1-click payment, preauthorizations, etc, they must be activated first by Redsys. Check it out or contact me through the WooCommerce.com for help.</p><p>Read everything that is written under each configuration option, it is very important in some of them.</p><p>Thanks a lot</p><p>Jos&eacute; Conti</p>', 'woocommerce-redsys' ), $message );
	echo '</p></div>';
}

add_action( 'admin_notices', 'redsys_woo_help_admin_notice' );

function redsys_add_notice_new_version() {

	$version = get_option( 'hide-new-version-redsys-notice' );

	if ( $version !== REDSYS_VERSION ) {
		if ( isset( $_REQUEST['redsys-hide-new-version'] ) &&  'hide-new-version-redsys' === $_REQUEST['redsys-hide-new-version'] ) {
			$nonce = sanitize_text_field( $_REQUEST['_redsys_hide_new_version_nonce'] );
			if ( wp_verify_nonce( $nonce, 'redsys_hide_new_version_nonce' ) ) {
				update_option( 'hide-new-version-redsys-notice', REDSYS_VERSION );
			}
		} else {
			?>
			<div id="message" class="updated woocommerce-message woocommerce-redsys-messages">
				<div class="logo-redsys-notice">
					<img src="<?php echo REDSYS_PLUGIN_URL; ?>assets/images/redsys-woo-notice.png" alt="Logo Plugn Redsys" height="100" width="100">
				</div>
				<div class="contenido-redsys-notice">
					<a class="woocommerce-message-close notice-dismiss" style="top:0;" href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'redsys-hide-new-version', 'hide-new-version-redsys' ), 'redsys_hide_new_version_nonce', '_redsys_hide_new_version_nonce' ) ); ?>"><?php esc_html_e( 'Dismiss', 'woocommerce-redsys' ); ?></a>
					<p>
					    <h3>
						    <?php echo esc_html__( 'WooCommerce Redsys Gateway has been updated to version', 'woocommerce-redsys' ) . ' ' . REDSYS_VERSION; ?>
						</h3>
					</p>
					<p>
						<?php esc_html_e( 'Discover the improvements that have been made in this version, and how to take advantage of them ', 'woocommerce-redsys' ); ?>
					</p>
					<p class="submit">
						<a href="<?php echo REDSYS_POST_UPDATE_URL; ?>" class="button-primary" target="_blank"><?php esc_html_e( 'Discover the improvements', 'woocommerce-redsys' );  ?></a>
						<a href="<?php echo REDSYS_REVIEW; ?>" class="button-primary" target="_blank"><?php esc_html_e( 'Leave a review', 'woocommerce-redsys' );  ?></a>
						<a href="<?php echo REDSYS_TELEGRAM_SIGNUP; ?>" class="button-primary" target="_blank"><?php esc_html_e( 'Sign up for the Telegram channel', 'woocommerce-redsys' );  ?></a>
					</p>
				</div>
			</div>
		<?php }
	}
}
add_action( 'admin_notices', 'redsys_add_notice_new_version' );

function redsys_installation_notice() {

	$action = get_option( 'hide-installation-notice-redsys-notice' );

	if ( $action !== 'yes' ) {
		if ( isset( $_REQUEST['redsys-hide-installation-notice'] ) &&  'hide-installation-notice-redsys' === $_REQUEST['redsys-hide-installation-notice'] ) {
			$nonce = sanitize_text_field( $_REQUEST['_redsys_hide_new_version_nonce'] );
			if ( wp_verify_nonce( $nonce, 'redsys_hide_installation_notice_nonce' ) ) {
				update_option( 'hide-installation-notice-redsys-notice', 'yes' );
			}
		} else {
			?>
			<div id="message" class="updated woocommerce-message woocommerce-redsys-messages">
				<div class="logo-redsys-notice">
					<img src="<?php echo REDSYS_PLUGIN_URL; ?>assets/images/redsys-woo-notice.png" alt="Logo Plugn Redsys" height="100" width="100">
				</div>
				<div class="contenido-redsys-notice">
					<a class="woocommerce-message-close notice-dismiss" style="top:0;" href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'redsys-hide-installation-notice', 'hide-installation-notice-redsys' ), 'redsys_hide_installation_notice_nonce', '_redsys_hide_installation_notice_nonce' ) ); ?>"><?php esc_html_e( 'Dismiss', 'woocommerce-redsys' ); ?></a>
					<p>
						<h3>
							<?php echo '<p>' . esc_html__( '¡Hola! Soy el plugin de Redsys. Mi desarrollador lleva más de 7 años trabajando en mí para poder ofreceros la mejor pasarela de pago posible para vuestras tiendas.' , 'woocommerce-redsys' ) . '</p>'; ?>
							<?php echo '<p>' . esc_html__( '¡Muchas gracias por adquirirme!' , 'woocommerce-redsys' ) . '</p>'; ?>
							<?php echo '<p>' . esc_html__( 'Este plugin tiene una licencia que incluye actualizaciones y soporte por parte de mi desarrollador, y sólo puede  adquirirse de forma oficial  en WooCommerce.' , 'woocommerce-redsys' ) . '</p>'; ?>
							<?php echo '<p>' . esc_html__( 'Si me has «encontrado» en otro sitio, ¡recuerda que puedo tener código modificado (y posiblemente dañino) y que no tienes soporte!' , 'woocommerce-redsys' ) . '</p>'; ?>
							<?php echo '<p>' . esc_html__( 'En la siguiente página podrás ver un video de como configurarme, explicación de todas mis funciones y los peligros de no adquirirme en WooCommerce.com' , 'woocommerce-redsys' ) . '</p>'; ?>
						</h3>
					</p>
					<p>
						<?php esc_html_e( 'Infórmate en la siguiente entrada de todo ello', 'woocommerce-redsys' ); ?>
					</p>
					<p><a href="<?php echo REDSYS_INSTALL_URL; ?>" class="button" target="_blank"><?php esc_html_e( 'Check it now', 'woocommerce-redsys' );  ?></a></p>
				</div>
			</div>
		<?php }
	}
}

//add_action( 'admin_notices', 'redsys_installation_notice' );

function redsys_deprecated_authorization() {
	
	$is_enabled = WCRed()->get_redsys_option( 'preauthorization', 'redsys' );
	if ( 'yes' === $is_enabled ) {
		$class = 'notice notice-error';
		$message = __( 'ATTENTION: Preathorizations in "Redsys (by José Conti)" is deprecated, please set up "Redsys Preauthorizations (by José Conti)". Once you deactivate the Preauthorization option in "Redsys (by José Conti)" this notice will disappear.', 'woocommerce-redsys' );
		printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
	}
}

add_action( 'admin_notices', 'redsys_deprecated_authorization' );

function redsys_notice_style() {
	wp_register_style( 'redsys_notice_css', REDSYS_PLUGIN_URL . 'assets/css/redsys-notice.css', false, REDSYS_VERSION );
	wp_enqueue_style( 'redsys_notice_css' );
}
add_action( 'admin_enqueue_scripts', 'redsys_notice_style' );
