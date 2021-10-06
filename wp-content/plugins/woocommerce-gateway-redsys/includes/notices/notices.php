<?php
/**
 * Copyright: (C) 2013 - 2021 José Conti
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Copyright: (C) 2013 - 2021 José Conti
 */
function redsys_oficial_woocommerce_gateway_redsys_init_check() {
	$class   = 'error';
	$message = __( 'WARNING: Please, deactivate any Redsys, InSite or Bizum plugins other than WooCommerce Redsys Gateway by Jose Conti (WooCommerce.com) or you will have problems with WooCommerce Redsys Gateway in Payment methods.', 'woocommerce-redsys' );
	echo '<div class="' . esc_attr( $class ) . '" style="background-color: #c0392b; color: white; border-left-color: white;"> <p style="font-size: 20px;">' . esc_html( $message ) . '</p></div>';
}

/**
 * Copyright: (C) 2013 - 2021 José Conti
 */
function redsys_admin_notice_lite_version() {
	if ( is_plugin_active( 'woo-redsys-gateway-light/woocommerce-redsys.php' ) || is_plugin_active( 'redsys/wc-redsys.php' ) || is_plugin_active( 'redsysoficial/wc-redsys.php' ) || is_plugin_active( 'bizum/class-wc-bizum.php' ) ) {
		add_action( 'admin_notices', 'redsys_oficial_woocommerce_gateway_redsys_init_check' );
	}
}
add_action( 'admin_init', 'redsys_admin_notice_lite_version', 0 );

/**
 * Copyright: (C) 2013 - 2021 José Conti
 */
function redsys_add_notice_intalled_new() {

	$hide = get_option( 'hide-install-redsys-notice' );

	if ( 'yes' !== $hide ) {
		if ( isset( $_REQUEST['redsys-hide-install'] ) && 'hide-install-redsys' === $_REQUEST['redsys-hide-install'] ) {
			$nonce = sanitize_text_field( $_REQUEST['_redsys_hide_install_nonce'] );
			if ( wp_verify_nonce( $nonce, 'redsys_hide_install_nonce' ) ) {
				update_option( 'hide-install-redsys-notice', 'yes' );
			}
		} else {
			?>
			<div id="message" class="updated woocommerce-message woocommerce-redsys-messages">
				<div class="logo-redsys-notice">
					<img src="<?php echo REDSYS_PLUGIN_URL_P; ?>assets/images/redsys-woo-notice.png" alt="Logo Plugn Redsys" height="100" width="100">
				</div>
				<div class="contenido-redsys-notice">
					<a class="woocommerce-message-close notice-dismiss" style="top:0;" href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'redsys-hide-install', 'hide-install-redsys' ), 'redsys_hide_install_nonce', '_redsys_hide_install_nonce' ) ); ?>"><?php esc_html_e( 'Before Dismiss it, read it plase', 'woocommerce-redsys' ); ?></a>
					<p>
						<h3>
							<?php esc_html_e( 'Thank you for purchase WooCommerce Redsys Gateway', 'woocommerce-redsys' ); ?>
						</h3>
					</p>
					<p>
						<!--
						¿Vas a tener los cojones de modificar esto?
						Si te da vergÜenza, dame una parte de tus benegicios para contribuir al desarrollo como dicta el el esítitu de la GPL en vez de eliminar y modificar estas líneas, ya que estás hundiendo el desarrollo
						-->
						<?php printf( __( 'This plugin is developed by José Conti. It can only be purchased and downloaded from WooCommerce.com. If you have paid for it in another site, the owner of that site is earning money at my expense and at yours without valuing the effort I put in the development Doing so is against the GPL, as much as there are people who say it is legal and permissible. I invite you to <a href="%1$s" target="_blank">read this entry</a> so you know what the GPL is.', 'woocommerce-redsys' ), REDSYS_GPL ); ?>
					</p>
					<p>
					<?php esc_html_e( 'Please, at the slightest problem in the activation or configuration open a ticket for me to help you.', 'woocommerce-redsys' ); ?>
					</p>
					<p>
					<?php esc_html_e( 'I can install and configure the Redsys plugin for you, it goes with the price of the license you purchased at WooCommerce.com', 'woocommerce-redsys' ); ?>
					</p>
					<p class="submit">
						<a href="<?php echo REDSYS_TICKET; ?>" class="button-primary" target="_blank"><?php esc_html_e( 'Open a ticket NOW for help with installation', 'woocommerce-redsys' ); ?></a>
						<a href="<?php echo admin_url(); ?>admin.php?page=wc-addons&section=helper" class="button-primary" target="_blank"><?php esc_html_e( 'Connect your Site for get future extension updates', 'woocommerce-redsys' ); ?></a>
						<a href="<?php echo REDSYS_GPL; ?>" class="button-primary" target="_blank"><?php esc_html_e( 'Learn what is GPL', 'woocommerce-redsys' ); ?></a>
					</p>
				</div>
			</div>
			<?php
		}
	}
}
add_action( 'admin_notices', 'redsys_add_notice_intalled_new' );

/**
 * Copyright: (C) 2013 - 2021 José Conti
 */
function redsys_add_notice_new_version() {

	$version = get_option( 'hide-new-version-redsys-notice' );

	if ( $version !== REDSYS_VERSION ) {
		if ( isset( $_REQUEST['redsys-hide-new-version'] ) && 'hide-new-version-redsys' === $_REQUEST['redsys-hide-new-version'] ) {
			$nonce = sanitize_text_field( $_REQUEST['_redsys_hide_new_version_nonce'] );
			if ( wp_verify_nonce( $nonce, 'redsys_hide_new_version_nonce' ) ) {
				update_option( 'hide-new-version-redsys-notice', REDSYS_VERSION );
			}
		} else {
			?>
			<div id="message" class="updated woocommerce-message woocommerce-redsys-messages">
				<div class="logo-redsys-notice">
					<img src="<?php echo REDSYS_PLUGIN_URL_P; ?>assets/images/redsys-woo-notice.png" alt="Logo Plugn Redsys" height="100" width="100">
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
						<a href="<?php echo REDSYS_POST_UPDATE_URL_P; ?>" class="button-primary" target="_blank"><?php esc_html_e( 'Discover the improvements', 'woocommerce-redsys' ); ?></a>
						<a href="<?php echo REDSYS_REVIEW_P; ?>" class="button-primary" target="_blank"><?php esc_html_e( 'Leave a review', 'woocommerce-redsys' ); ?></a>
						<a href="<?php echo REDSYS_TELEGRAM_SIGNUP_P; ?>" class="button-primary" target="_blank"><?php esc_html_e( 'Sign up for the Telegram channel', 'woocommerce-redsys' ); ?></a>
						<a href="<?php echo REDSYS_GPL; ?>" class="button-primary" target="_blank"><?php esc_html_e( 'Learn what is GPL', 'woocommerce-redsys' ); ?></a>
					</p>
				</div>
			</div>
			<?php
		}
	}
}
add_action( 'admin_notices', 'redsys_add_notice_new_version' );

/**
 * Copyright: (C) 2013 - 2021 José Conti
 */
function redsys_installation_notice() {

	$action = get_option( 'hide-installation-notice-redsys-notice' );

	if ( $action !== 'yes' ) {
		if ( isset( $_REQUEST['redsys-hide-installation-notice'] ) && 'hide-installation-notice-redsys' === $_REQUEST['redsys-hide-installation-notice'] ) {
			$nonce = sanitize_text_field( $_REQUEST['_redsys_hide_new_version_nonce'] );
			if ( wp_verify_nonce( $nonce, 'redsys_hide_installation_notice_nonce' ) ) {
				update_option( 'hide-installation-notice-redsys-notice', 'yes' );
			}
		} else {
			?>
			<div id="message" class="updated woocommerce-message woocommerce-redsys-messages">
				<div class="logo-redsys-notice">
					<img src="<?php echo REDSYS_PLUGIN_URL_P; ?>assets/images/redsys-woo-notice.png" alt="Logo Plugn Redsys" height="100" width="100">
				</div>
				<div class="contenido-redsys-notice">
					<a class="woocommerce-message-close notice-dismiss" style="top:0;" href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'redsys-hide-installation-notice', 'hide-installation-notice-redsys' ), 'redsys_hide_installation_notice_nonce', '_redsys_hide_installation_notice_nonce' ) ); ?>"><?php esc_html_e( 'Dismiss', 'woocommerce-redsys' ); ?></a>
					<p>
						<h3>
							<?php echo '<p>' . esc_html__( '¡Hola! Soy el plugin de Redsys. Mi desarrollador lleva más de 7 años trabajando en mí para poder ofreceros la mejor pasarela de pago posible para vuestras tiendas.', 'woocommerce-redsys' ) . '</p>'; ?>
							<?php echo '<p>' . esc_html__( '¡Muchas gracias por adquirirme!', 'woocommerce-redsys' ) . '</p>'; ?>
							<?php echo '<p>' . esc_html__( 'Este plugin tiene una licencia que incluye actualizaciones y soporte por parte de mi desarrollador, y sólo puede  adquirirse de forma oficial  en WooCommerce.', 'woocommerce-redsys' ) . '</p>'; ?>
							<?php echo '<p>' . esc_html__( 'Si me has «encontrado» en otro sitio, ¡recuerda que puedo tener código modificado (y posiblemente dañino) y que no tienes soporte!', 'woocommerce-redsys' ) . '</p>'; ?>
							<?php echo '<p>' . esc_html__( 'En la siguiente página podrás ver un video de como configurarme, explicación de todas mis funciones y los peligros de no adquirirme en WooCommerce.com', 'woocommerce-redsys' ) . '</p>'; ?>
						</h3>
					</p>
					<p>
						<?php esc_html_e( 'Infórmate en la siguiente entrada de todo ello', 'woocommerce-redsys' ); ?>
					</p>
					<p><a href="<?php echo REDSYS_INSTALL_URL_P; ?>" class="button" target="_blank"><?php esc_html_e( 'Check it now', 'woocommerce-redsys' ); ?></a></p>
				</div>
			</div>
			<?php
		}
	}
}

// add_action( 'admin_notices', 'redsys_installation_notice' );.

/**
 * Copyright: (C) 2013 - 2021 José Conti
 */
function redsys_deprecated_authorization() {

	$is_enabled = WCRed()->get_redsys_option( 'preauthorization', 'redsys' );
	if ( 'yes' === $is_enabled ) {
		$class   = 'notice notice-error';
		$message = __( 'ATTENTION: Preathorizations in "Redsys (by José Conti)" is deprecated, please set up "Redsys Preauthorizations (by José Conti)". Once you deactivate the Preauthorization option in "Redsys (by José Conti)" this notice will disappear.', 'woocommerce-redsys' );
		printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
	}
}

add_action( 'admin_notices', 'redsys_deprecated_authorization' );

/**
 * Copyright: (C) 2013 - 2021 José Conti
 */
function redsys_notice_style() {
	wp_register_style( 'redsys_notice_css', REDSYS_PLUGIN_URL_P . 'assets/css/redsys-notice.css', false, REDSYS_VERSION );
	wp_enqueue_style( 'redsys_notice_css' );
}
add_action( 'admin_enqueue_scripts', 'redsys_notice_style' );

/**
 * Copyright: (C) 2013 - 2021 José Conti
 */
function check_redsys_connected() {

	if ( REDSYS_CHECK_WOO_CONNECTION ) {
		if ( class_exists( WC_Helper_Options ) ) {
			$auth = WC_Helper_Options::get( 'auth' );
			if ( empty( $auth['access_token'] ) ) {
				$class    = 'notice notice-error';
				$link     = admin_url( 'admin.php?page=wc-addons&section=helper' );
				$message  = '<a href="' . esc_html( $link ) . '">' . __( 'Connect WooCommerce with WooCommerce.com', 'woocommerce-redsys' ) . '</a>';
				$message2 = __( 'to get WooCommerce Redsys Gateway updates. This connection will allow you to update the plugin automatically and be advised of new updates<br />If you don\'t connect it, you could be with an old plugin version and maybe with some bugs.', 'woocommerce-redsys' );
				printf( '<div class="%1$s"><p>%2$s %3$s</p></div>', esc_attr( $class ), $message, $message2 );
			}
		}
	}
}

add_action( 'admin_notices', 'check_redsys_connected' );
