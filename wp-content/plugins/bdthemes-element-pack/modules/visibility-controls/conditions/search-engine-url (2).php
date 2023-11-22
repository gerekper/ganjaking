<?php

namespace ElementPack\Modules\VisibilityControls\Conditions;

use ElementPack\Base\Condition;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
	
class Search_Engine_Url extends Condition {
	
	/**
	 * Get the name of condition
	 * @return string as per our condition control name
	 * @since  5.11.0
	 */
	public function get_name() {
		return 'search_engine_url';
	}
	
	/**
	 * Get the title of condition
	 * @return string as per condition control title
	 * @since  5.11.0
	 */
	public function get_title() {
		return esc_html__( 'From Search Engine URL', 'bdthemes-element-pack' );
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
			'type'        => Controls_Manager::SELECT2,
			'label'       => esc_html__( 'Choose from dropdown', 'bdthemes-element-pack' ),
			'label_block' => true,
			'multiple'    => true,
			'default'    => 'google.com',
			'description' => esc_html__( 'Don\'t leave it blank', 'bdthemes-element-pack' ),
			'options'     => [
				'google.com' => esc_html__( 'Google', 'bdthemes-element-pack' ),
				'yahoo.com'  => esc_html__( 'Yahoo', 'bdthemes-element-pack' ),
				'bing.com'   => esc_html__( 'Bing', 'bdthemes-element-pack' ),
				'yandex.com' => esc_html__( 'Yandex', 'bdthemes-element-pack' ),
				'baidu.com'  => esc_html__( 'Baidu', 'bdthemes-element-pack' ),
			],
		];
	}
	
	/**
	 * Check the condition
	 * @param string $relation Comparison operator for compare function
	 * @param mixed $val will check the control value as per condition needs
	 * @since 5.11.0
	 */
	public function check( $relation, $val ) {

		$res    = false;
		$sename = false;
		if(isset($_SERVER['HTTP_REFERER'])) {
			$url = $_SERVER['HTTP_REFERER'];

			if(!empty($val)) {
				foreach ($val as $value) {
				  if (in_array($value, ['google.com','yahoo.com','bing.com','yandex.com','baidu.com'])) {
				    $sename = $value;
				    break;
				  }
				}
			}

			if (strpos($url, $sename) !== false) {
				$res = true;
			}
		}

		return  $this->compare( $res, true, $relation );
	}
}
