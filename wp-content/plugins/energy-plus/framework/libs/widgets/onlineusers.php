<?php

/**
* WIDGET
*
* Online users count
*
*
* @since      1.0.0
* @package    EnergyPlus
* @subpackage EnergyPlus/framework/libs/widgets
* @author     EN.ER.GY <support@en.er.gy>
* */


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Widgets__Onlineusers extends EnergyPlus_Widgets {

	public static $name = 'Online Users';
	public static $multiple = false;


	public static function run ( $args = array(), $settings = array() ) {

		global $wpdb;
		$result = $wpdb->get_var(
			$wpdb->prepare("
			SELECT COUNT(DISTINCT session_id)
			FROM {$wpdb->prefix}energyplus_requests
			WHERE week = %d AND date >= %s AND date <= %s",
			EnergyPlus_Helpers::strtotime('now - 5 minute', 'W'), EnergyPlus_Helpers::strtotime('now - 5 minute', 'Y-m-d H:i:s'), current_time('mysql')
			)
		);

		$max = intval(EnergyPlus::option("visitors_max", 0));
		$min = intval(EnergyPlus::option("visitors_min", 0));

		if ($result>$max) {
			EnergyPlus::option("visitors_max", $result, 'set');

			// Add an event and notification
			if (0 < $result) {

				EnergyPlus_Events::add(
					array(
						'user'   => 0,
						'id'     => 1, // For recognize
						'type'   => 12,
						'extra'  => serialize(array(
							'title'   => esc_html__('Congratulations! A new record...', 'energyplus'),
							'message' => sprintf(__('<h2 class="__A__Widget_onlineusers_Notice">%d → %d</h2><br>There are %d people on your site right now and this is a new record. Your last record was %d', 'energyplus'), $max, $result, $result, $max),
						)
					)
				)
			);
		}

	}

	if ($result<$min) {
		EnergyPlus::option("visitors_min", $result, 'set');
	}

	if (EnergyPlus_Helpers::is_ajax() OR isset( $args['ajax'] ))  {
		return intval($result);
	} else {
		echo EnergyPlus_View::run('widgets/online-users',  array('args' => $args, 'result' => $result, 'min' => $min, 'max' => $max));
	}
}

/**
* Widget's settings
*
* @since  1.0.0
* @param  array    $args
* @return array
*/

public static function settings ( $args ) {
	return array(
		'dimensions' => array(
			'type' => 'wh',
			'title' => esc_html__('Dimensions', 'energyplus'),
			'values' => array(
				array(
					'title' => 'W',
					'id' => 'w',
					'values'=> array(1,'1_5',2,'2_5',3)
				),
				array(
					'title' => 'H',
					'id' => 'h',
					'values'=> array(3,4)
				),
			)
		),

	);

}

}

?>
