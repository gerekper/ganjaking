<?php
/**
 * Plugin List Links Redsys Premium
 *
 * @package WooCommerce Redsys Gateway
 * @since 13.0.0
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
 * Plugin List Links Redsys Premium
 */
class Plugin_List_Links_Redsys_Premium {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_filter( 'plugin_action_links_' . REDSYS_PLUGIN_BASENAME, array( $this, 'plugin_action_links' ), 10, 1 );
		add_filter( 'plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 2 );
	}
	/**
	 * Show action links on the plugin screen.
	 *
	 * @param mixed $links Plugin Action links.
	 *
	 * @return array
	 */
	public static function plugin_action_links( $links ) {
		$action_links = array(
			'settings' => '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=checkout' ) . '" aria-label="' . esc_attr__( 'Gateways List', 'woocommerce-redsys' ) . '">' . esc_html__( 'Gateways Lists', 'woocommerce-redsys' ) . '</a>',
		);

		return array_merge( $action_links, $links );
	}
	/**
	 * Show row meta on the plugin screen.
	 *
	 * @param mixed $links Plugin Row Meta.
	 * @param mixed $file  Plugin Base file.
	 *
	 * @return array
	 */
	public static function plugin_row_meta( $links, $file ) {
		if ( REDSYS_PLUGIN_BASENAME !== $file ) {
			return $links;
		}

		$row_meta = array(
			'docs'    => '<a href="' . esc_url( apply_filters( 'redsys_docs_url', 'https://redsys.joseconti.com/guias/' ) ) . '" aria-label="' . esc_attr__( 'View Plugin documentation', 'woocommerce-redsys' ) . '">' . esc_html__( 'Docs', 'woocommerce-redsys' ) . '</a>',
			'apidocs' => '<a href="' . esc_url( apply_filters( 'redsys_apidocs_url', 'https://redsys.joseconti.com/api-woocommerce-redsys-gateway/' ) ) . '" aria-label="' . esc_attr__( 'View Plugin API docs', 'woocommerce-redsys' ) . '">' . esc_html__( 'API docs', 'woocommerce-redsys' ) . '</a>',
			'support' => '<a href="' . esc_url( apply_filters( 'redsys_support_url', 'https://woocommerce.com/my-account/create-a-ticket/' ) ) . '" aria-label="' . esc_attr__( 'Open a Support Ticket', 'woocommerce-redsys' ) . '">' . esc_html__( 'Open a Support Ticket', 'woocommerce-redsys' ) . '</a>',
		);

		return array_merge( $links, $row_meta );
	}

}
return new Plugin_List_Links_Redsys_Premium();
