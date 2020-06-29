<?php
if( ! defined( 'ABSPATH' ) ){
	exit; // Exit if accessed directly
}

class Mfn_Builder_Helper {

  /**
   * Unique ID
   * Generate unique ID and check for collisions
   */

  public static function unique_ID($uids = array()){

  	if (function_exists('openssl_random_pseudo_bytes')) {

  		// openssl_random_pseudo_bytes

  		$uid = substr(bin2hex(openssl_random_pseudo_bytes(5)), 0, 9);

  	} else {

  		// fallback

  		$keyspace = '0123456789abcdefghijklmnopqrstuvwxyz';
  		$keyspace_length = 36;
  		$uid = '';

  		for ($i = 0; $i < 9; $i++) {
  			$uid .= $keyspace[rand(0, $keyspace_length - 1)];
      }

  	}

   	if( in_array( $uid, $uids ) ){
   		return self::unique_ID($uids);
   	}

   	return $uid;
  }

  /**
	 * Set new uniqueID for all builder sections, wrap and items
	 * This function also checks for possible collisions
	 */

	public static function unique_ID_reset($data, $uids){

		if (! is_array($data)) {
			return false;
		}

		foreach($data as $section_k => $section){

			$uids[] = self::unique_ID($uids);
			$data[$section_k]['uid'] = end($uids);

			if(is_array($section['wraps'])){
				foreach($section['wraps'] as $wrap_k => $wrap){

					$uids[] = self::unique_ID($uids);
					$data[$section_k]['wraps'][$wrap_k]['uid'] = end($uids);

					if(is_array($wrap['items'])){
						foreach($wrap['items'] as $item_k => $item){

							$uids[] = self::unique_ID($uids);
							$data[$section_k]['wraps'][$wrap_k]['items'][$item_k]['uid'] = end($uids);

						}
					}

				}
			}

		}

		return $data;

	}

	/**
	 * GET Sliders
	 * Layer Slider
	 * Revolution Slider
	 */

	public static function get_sliders( $plugin = 'rev' ){

		global $wpdb;

		$sliders = array( 0 => esc_html__('-- Select --', 'mfn-opts') );

		if( 'layer' == $plugin ){

			// layer slider

			if (function_exists('layerslider')) {

				$table_prefix = mfn_opts_get('table_prefix', 'base_prefix');
				if ($table_prefix == 'base_prefix') {
					$table_prefix = $wpdb->base_prefix;
				} else {
					$table_prefix = $wpdb->prefix;
				}
				$table_name = $table_prefix . "layerslider";

				$array = $wpdb->get_results($wpdb->prepare("SELECT `id`, `name` FROM `$table_name` WHERE `flag_hidden` = %d AND `flag_deleted` = %d ORDER BY `name` ASC", 0, 0));

				if (is_array($array)) {
					foreach ($array as $v) {
						$sliders[$v->id] = $v->name;
					}
				}
			}

		} else {

			// revolution slider

			if (function_exists('rev_slider_shortcode')) {

				if ( 'base_prefix' == mfn_opts_get('table_prefix', 'base_prefix') ) {
					$table_prefix = $wpdb->base_prefix;
				} else {
					$table_prefix = $wpdb->prefix;
				}
				$table_name = $table_prefix . "revslider_sliders";

				$array = $wpdb->get_results($wpdb->prepare("SELECT `alias`, `title` FROM `$table_name` WHERE `type` != %s ORDER BY `title` ASC", 'template'));

				if (is_array($array)) {
					foreach ($array as $v) {
						$sliders[$v->alias] = $v->title;
					}
				}
			}

		}

		return $sliders;

	}

	/**
	 * Fiter for: GET builder items
	 */

	public static function filter_builder_get($builder){

		// FIX | Muffin builder 2 compatibility

		if( ( ! $builder ) || is_array($builder) ){
			return $builder;
		}

		return unserialize(call_user_func('base'.'64_decode', $builder));

  }

}
