<?php

namespace ElementPack\Modules\VisibilityControls\Conditions;

use ElementPack\Base\Condition;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
	
class Ex_Url extends Condition {
	
	/**
	 * Get the name of condition
	 * @return string as per our condition control name
	 * @since  5.11.0
	 */
	public function get_name() {
		return 'ex_url';
	}
	
	/**
	 * Get the title of condition
	 * @return string as per condition control title
	 * @since  5.11.0
	 */
	public function get_title() {
		return esc_html__( 'From External URL', 'bdthemes-element-pack' );
	}

	/**
	 * Get the group of condition
	 * @return string as per our condition control name
	 * @since  6.11.3
	 */
	public function get_group() {
		return 'url';
	}
	
	/**
	 * Get the control value
	 * @return array as per condition control value
	 * @since  5.11.0
	 */
	public function get_control_value() {
		return [
			'type'        => Controls_Manager::TEXT,
			'label_block' => true,
			'placeholder' => 'www.elementpack.pro',
			'description' => esc_html__( 'Leave blank for any external link', 'bdthemes-element-pack' ),
		];
	}
	
	/**
	 * Check the condition
	 * @param string $relation Comparison operator for compare function
	 * @param mixed $val will check the control value as per condition needs
	 * @since 5.11.0
	 */
	public function check( $relation, $val ) {

		$res      = false;
		$site_url = str_ireplace( 'www.', '', parse_url( home_url(), PHP_URL_HOST ) );
		
		if(isset($_SERVER['HTTP_REFERER'])) {
			$url = (!empty($val)) ? $val : $_SERVER['HTTP_REFERER'];

			$components = parse_url($url);
			if ( empty($components['host']) ) return false;  // we will treat url like '/relative.php' as relative
			if ( strcasecmp($components['host'], $site_url) === 0 ) return false; // url host looks exactly like the local host
			$res = strrpos(strtolower($components['host']), '.'.$site_url) !== strlen($components['host']) - strlen('.'.$site_url); // check if the url host is a subdomain

		}

		return  $this->compare( $res, true, $relation );
	}
}
