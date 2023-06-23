<?php
/**
 * Redsys Site Health
 *
 * @package WooCommerce Redsys Gateway WooCommerce.com > https://woocommerce.com/products/redsys-gateway/
 * @since 13.0.0
 * @author José Conti.
 * @link https://joseconti.com
 * @license GNU General Public License v3.0
 * @license URI: http://www.gnu.org/licenses/gpl-3.0.html
 * @copyright 2013-2023 José Conti.
 */

defined( 'ABSPATH' ) || exit;

add_filter( 'site_status_tests', 'redsys_add_oficial_redsys_test' );
add_filter( 'site_status_tests', 'redsys_add_oficial_bizum_test' );
add_filter( 'site_status_tests', 'redsys_add_soap_test' );
if ( class_exists( 'SOAPClient' ) ) {
	add_filter( 'site_status_tests', 'redsys_add_test_url_soap_test' );
	add_filter( 'site_status_tests', 'redsys_add_real_url_soap_test' );
}
/**
 * Add test for Redsys to Site Health
 *
 * @param array $tests Site Health tests.
 */
function redsys_add_oficial_redsys_test( $tests ) {
	$tests['direct']['oficial_redsys'] = array(
		'label' => __( 'Checking Redsys Plugin' ),
		'test'  => 'redsys_oficial_redsys_test',
	);
	return $tests;
}
/**
 * Add test for Bizum to Site Health
 *
 * @param array $tests Site Health tests.
 */
function redsys_add_oficial_bizum_test( $tests ) {
	$tests['direct']['oficial_bizum'] = array(
		'label' => __( 'Checking Bizum Plugin' ),
		'test'  => 'redsys_oficial_bizum_test',
	);
	return $tests;
}
/**
 * Add test for SOAP to Site Health
 *
 * @param array $tests Site Health tests.
 */
function redsys_add_test_url_soap_test( $tests ) {
	$tests['direct']['redsys_url_test'] = array(
		'label' => __( 'Checking SOAP Test Terminal' ),
		'test'  => 'redsys_test_url_soap_test',
	);
	return $tests;
}
/**
 * Add test for SOAP to Site Health
 *
 * @param array $tests Site Health tests.
 */
function redsys_add_real_url_soap_test( $tests ) {
	$tests['direct']['redsys_url_real'] = array(
		'label' => __( 'Checking SOAP Real Terminal' ),
		'test'  => 'redsys_real_url_soap_test',
	);
	return $tests;
}
/**
 * Add test for SOAP to Site Health
 *
 * @param array $tests Site Health tests.
 */
function redsys_add_soap_test( $tests ) {
	$tests['direct']['redsys_soap_tst'] = array(
		'label' => __( 'Checking SOAP' ),
		'test'  => 'redsys_soap_test',
	);
	return $tests;
}
/**
 * Test for Redsys Plugin in Site Health
 *
 * @return array
 */
function redsys_oficial_redsys_test() {
	if ( ( ! in_array( 'redsysoficial/class-wc-redsys.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ), true ) ) && ( ! in_array( 'redsys/class-wc-redsys.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ), true ) ) ) {
		$result = array(
			'label'       => __( 'Oficial Redsys and InSite Plugin not active, ok', 'woocommerce-redsys' ),
			'status'      => 'good',
			'badge'       => array(
				'label' => __( 'Redsys', 'woocommerce-redsys' ),
				'color' => 'red',
			),
			'description' => sprintf(
				'<p>%s</p>',
				__( 'This is Ok.', 'woocommerce-redsys' )
			),
			'actions'     => '',
			'test'        => 'redsys-oficial-redsys-test',
		);
		return $result;
	} else {
		$result = array(
			'label'       => __( 'WARNING: Oficial Redsys or InSite Plugin Active', 'woocommerce-redsys' ),
			'status'      => 'critical',
			'badge'       => array(
				'label' => __( 'Redsys', 'woocommerce-redsys' ),
				'color' => 'red',
			),
			'description' => sprintf(
				'<p>%s</p>',
				sprintf(
					__( // phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment
						'Oficial Redsys or InSIte plugin active, please deactivate it.
If you have both plugin active, WooCommerce Redsys Gateway by José Conti (WooCommerce.com), and Redsys WooCommerce (oficial plugin), you will have many problems. <a href="%1$s" target="_blank">Deactivate the Oficial Redsys Plugin</a>.',
						'woocommerce-redsys'
					),
					admin_url( 'plugins.php?s=redsys%20woocommerce&plugin_status=active' )
				)
			),
			'actions'     => '',
			'test'        => '',
		);
		return $result;
	}
}
/**
 * Test for Bizum Plugin in Site Health
 *
 * @return array
 */
function redsys_oficial_bizum_test() {
	if ( ! in_array( 'bizum/class-wc-bizum.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ), true ) ) {
		$result = array(
			'label'       => __( 'Oficial Bizum Plugin not active, ok', 'woocommerce-redsys' ),
			'status'      => 'good',
			'badge'       => array(
				'label' => __( 'Redsys', 'woocommerce-redsys' ),
				'color' => 'red',
			),
			'description' => sprintf(
				'<p>%s</p>',
				__( 'This is Ok.', 'woocommerce-redsys' )
			),
			'actions'     => '',
			'test'        => 'redsys-oficial-bizum-test',
		);
		return $result;
	} else {
		$result = array(
			'label'       => __( 'WARNING: Oficial Bizum Plugin Active', 'woocommerce-redsys' ),
			'status'      => 'critical',
			'badge'       => array(
				'label' => __( 'Redsys', 'woocommerce-redsys' ),
				'color' => 'red',
			),
			'description' => sprintf(
				'<p>%s</p>',
				sprintf(
					__( // phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment
						'Oficial Bizum plugin active, please deactivate it.
If you have both plugin active, WooCommerce Redsys Gateway by José Conti (WooCommerce.com), and Bizum WooCommerce (oficial plugin), you will have many problems. <a href="%1$s" target="_blank">Deactivate the Oficial Bizum Plugin</a>.',
						'woocommerce-redsys'
					),
					admin_url( 'plugins.php?s=Bizum%20WooCommerce&plugin_status=active' )
				)
			),
			'actions'     => '',
			'test'        => '',
		);
		return $result;
	}
}
/**
 * Test for Redsys Test URL SOAP
 *
 * @return array
 */
function redsys_test_url_soap_test() {

	$exception_message = false;
	$soap_client       = new SoapClient( 'https://sis-t.redsys.es:25443/sis/services/SerClsWSEntradaV2?wsdl' );
	try {
		$result = $soap_client->__soapCall( 'trataPeticion', array() );
	} catch ( SoapFault $fault ) {
		$exception_message = $fault->getMessage();
	}
	if ( ! $exception_message ) {
		$result = array(
			'label'       => __( 'SOAP URL Test is Working, OK', 'woocommerce-redsys' ),
			'status'      => 'good',
			'badge'       => array(
				'label' => __( 'Redsys', 'woocommerce-redsys' ),
				'color' => 'red',
			),
			'description' => sprintf(
				'<p>%s</p>',
				__( 'This is Ok.', 'woocommerce-redsys' )
			),
			'actions'     => '',
			'test'        => 'redsys-test-url-soap-test',
		);
		return $result;
	} else {
		$result = array(
			'label'       => __( 'WARNING: The plugin cannot connect with Redsys Test Terminal URL via SOAP', 'woocommerce-redsys' ),
			'status'      => '',
			'badge'       => array(
				'label' => __( 'Redsys', 'woocommerce-redsys' ),
				'color' => 'red',
			),
			'description' => sprintf(
				'<p>%s</p>',
				__( 'Normally this happens because your hosting is blocking the Port 25443 for SOAP, please talk to your hosting and tell them to open port 25443 for SOAP. If they ask you the URL to which the plugin is trying to connect, it\'s https://sis-t.redsys.es:25443/sis/services/SerClsWSEntradaV2?wsdl If the hosting does not open the port, the plugin will not work correctly in test mode..', 'woocommerce-redsys' )
			),
			'actions'     => '',
			'test'        => '',
		);
		return $result;
	}
}
/**
 * Test for Redsys Real URL SOAP
 *
 * @return array
 */
function redsys_real_url_soap_test() {

	$exception_message = false;
	$soap_client       = new SoapClient( 'https://sis.redsys.es/sis/services/SerClsWSEntradaV2?wsdl' );
	try {
		$result = $soap_client->__soapCall( 'trataPeticion', array() );
	} catch ( SoapFault $fault ) {
		$exception_message = $fault->getMessage();
	}
	if ( ! $exception_message ) {
		$result = array(
			'label'       => __( 'SOAP URL Real is Working, ok', 'woocommerce-redsys' ),
			'status'      => 'good',
			'badge'       => array(
				'label' => __( 'Redsys', 'woocommerce-redsys' ),
				'color' => 'red',
			),
			'description' => sprintf(
				'<p>%s</p>',
				__( 'This is Ok.', 'woocommerce-redsys' )
			),
			'actions'     => '',
			'test'        => 'redsys-real-url-soap-test',
		);
		return $result;
	} else {
		$result = array(
			'label'       => __( 'WARNING: The plugin cannot connect with Redsys Real Terminal URL via SOAP', 'woocommerce-redsys' ),
			'status'      => 'critical',
			'badge'       => array(
				'label' => __( 'Redsys', 'woocommerce-redsys' ),
				'color' => 'red',
			),
			'description' => sprintf(
				'<p>%s</p>',
				__( 'Normally this happens because your hosting is blocking outgoing SOAP calls, Please talk to your hosting and tell them to open SOAP. If they ask you the URL to which the plugin is trying to connect, it\'s https://sis.redsys.es/sis/services/SerClsWSEntradaV2?wsdl. If the hosting does not open the port, the plugin will not work correctly. .', 'woocommerce-redsys' )
			),
			'actions'     => '',
			'test'        => '',
		);
		return $result;
	}
}
/**
 * Test for Redsys SOAP
 *
 * @return array
 */
function redsys_soap_test() {

	if ( class_exists( 'SOAPClient' ) ) {
		$result = array(
			'label'       => __( 'SOAP is active, ok', 'woocommerce-redsys' ),
			'status'      => 'good',
			'badge'       => array(
				'label' => __( 'Redsys', 'woocommerce-redsys' ),
				'color' => 'red',
			),
			'description' => sprintf(
				'<p>%s</p>',
				__( 'This is Ok.', 'woocommerce-redsys' )
			),
			'actions'     => '',
			'test'        => '',
		);
		return $result;
	} else {
		$result = array(
			'label'       => __( 'WARNING: SOAP is not active', 'woocommerce-redsys' ),
			'status'      => 'critical',
			'badge'       => array(
				'label' => __( 'Redsys', 'woocommerce-redsys' ),
				'color' => 'red',
			),
			'description' => sprintf(
				'<p>%s</p>',
				__( 'SOAP is needed for Pay with 1 clic, refunds, subscriptions, etc. Ask to your hosting to enable it. Without active SOAP on the server, the functionality of the plugin is very limited.', 'woocommerce-redsys' )
			),
			'actions'     => '',
			'test'        => 'redsys-soap-test',
		);
		return $result;
	}
}
