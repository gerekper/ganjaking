<?php
defined('WYSIJA') or die('Restricted access');
/**
* Wysija Premium Utils.
* Utilities singleton not related to a specific model.
*/
class WJ_Utils {

	private function __construct(){}

	public static function to_int( $value ) {
		$boolean = (bool) $value;
		if ( $boolean ) {
			$int = 1;
		} else {
			$int = 0;
		}
		return $int;
	}

	public static function to_bool( $value ) {
		$int = (int) $value;
		if ( $int == 0 ) {
			$boolean = false;
		} else {
			$boolean = true;
		}
		return $boolean;
	}

	/**
	 * Retuns plain domain name.
	 *
	 * @return string $domain
	 */
	public static function get_domain() {
		if ( isset($_SERVER['SERVER_NAME'] ) && strlen( trim( $_SERVER['SERVER_NAME'] ) ) > 0 ) {
			$domain = strtolower( $_SERVER['SERVER_NAME'] );
		} else {
			$domain = preg_replace( '@http[s]?:\/\/@', '', get_site_url() );
		}

		return preg_replace( '@^www\.@', '', $domain );
	}

	/**
	 * Reruns necessary data for tooltip.
	 *
	 * @return array $data
	 */
	public static function get_tip_data() {
		$model_config = WYSIJA::get( 'config', 'model' );
		$is_gmail = 'false';

		if ( 'gmail' === $model_config->getValue( 'sending_method' ) ) {
			$is_gmail = 'true';
		}

		$data = array(
			'text' => sprintf( __( 'Use an email from your domain, like <strong>info@%s</strong> to avoid the spam folder, or even be blocked.', WYSIJA ), self::get_domain() ),
			'gmailText' => sprintf( __( 'If you want to use a Gmail address, you need to send with Gmail. Check <a id="tip-send-with" href="%s">sending settings</a>.', WYSIJA ), admin_url( 'admin.php?page=wysija_config#tab-sendingmethod' ) ),
			'domain' => self::get_domain(),
			'isGmail' => $is_gmail,
		);

		return $data;
	}

	public static function esc_json_attr( $json ){
		$search = array(
			'\"',
			'\'',
			'<',
			'>',
			'&',
		);
		$replace = array(
			'\\u0022',
			'\\u0027',
			'\\u003C',
			'\\u003E',
			'\\u0026',
		);

		return str_replace( $search, $replace, json_encode( $json ) );
	}

}
