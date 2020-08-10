<?php

/**
* WIDGET
*
* Hourly/Daily/Monthly visitors count
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


class Widgets__Hourly extends EnergyPlus_Widgets {
  public static $name = 'Hourly';
  public static $multiple = true;


  public static function run ( $args = array(), $settings = array() ) {

    global $wpdb;


    $time = isset( $args['lasttime'] ) ? $args['lasttime'] : time() - (24 * 60 * 60);
    $time = time() - (24*60*60);
    $max = 0;
    $range = (isset($settings['range'])) ? $settings['range'] : 'hourly';

    $args['range'] = $range;
    switch ($range) {

      case 'monthly':
      // Online visitors

		$result = $wpdb->get_results(
      $wpdb->prepare("
      SELECT month(CONCAT(day, '01')) AS step, visitors as counts
      FROM {$wpdb->prefix}energyplus_daily
      WHERE type='M' AND day >= %s AND day <= %s  ORDER BY step ASC",
      EnergyPlus_Helpers::strtotime('first day of January','Ym'), EnergyPlus_Helpers::strtotime('today', 'Ym')
    ), ARRAY_A
  );

	$result[] = array(
		'step' =>date('m'),
		'counts'=>$wpdb->get_var(
		$wpdb->prepare("
		SELECT count(distinct session_id) as counts
		FROM {$wpdb->prefix}energyplus_requests
		WHERE date >= %s AND month=%d",
		EnergyPlus_Helpers::strtotime('first day of this month'), EnergyPlus_Helpers::strtotime('today','m')
	))
);

    $_result = array_fill(1, 12, '0');
    $labels = array();
    for ($i = 1; $i< 13; ++$i) {
      $labels[] = "'".EnergyPlus_Helpers::strtotime("2019-$i-01", 'M')."'";
    }

    break;


    case 'daily':

    // Online visitors
    $result = $wpdb->get_results(
      $wpdb->prepare("
      SELECT day(day) AS step, visitors as counts
      FROM {$wpdb->prefix}energyplus_daily
      WHERE type='D' AND day >= %s AND day <= %s  ORDER BY step ASC",
      EnergyPlus_Helpers::strtotime('first day of this month','Ymd'), EnergyPlus_Helpers::strtotime('today', 'Ymd')
    ), ARRAY_A
  );


		$result[] = array(
			'step' =>date('d'),
			'counts'=>$wpdb->get_var(
			$wpdb->prepare("
			SELECT count(distinct session_id) as counts
			FROM {$wpdb->prefix}energyplus_requests
			WHERE date >= %s AND month=%d",
			EnergyPlus_Helpers::strtotime('today'), EnergyPlus_Helpers::strtotime('today','m')
		))
	);

  $_result = array_fill(1, date('t'), '0');
  $labels = range(1,date('t'));

  break;

  default:
  // Online visitors
  $result = $wpdb->get_results(
    $wpdb->prepare("
    SELECT hour(date) AS step, count(distinct session_id) as counts
    FROM {$wpdb->prefix}energyplus_requests
    WHERE week = %d AND date >= %s GROUP BY hour(date) ORDER BY step ASC",
    EnergyPlus_Helpers::strtotime('now', 'W'), EnergyPlus_Helpers::strtotime('today')
  ), ARRAY_A
);

$_result = array_fill(0, 23, '0');
$labels = range(0,23);

break;
}

foreach ($result AS $step) {
  $max = ($step['counts'] < $max) ? $max : $step['counts'];
  $_result[$step['step']] = $step['counts'];
}

if (EnergyPlus_Helpers::is_ajax() OR isset( $args['ajax'] ))  {
  return EnergyPlus_View::run('widgets/hourly',  array( 'args' => $args, 'max' => $max, 'labels'=>$labels, 'ajax' => true, 'results' => $_result ));
} else {
  echo EnergyPlus_View::run('widgets/hourly',  array( 'args' => $args, 'max' => $max,'labels'=>$labels, 'results' => $_result ));
}



}

/**
 * Widget's settings
 *
 * @since  1.0.0
 * @param  array    $args
 * @return array
 */

public static function settings ( $settings ) {
  return array(
    'dimensions' => array(
      'type' => 'wh',
      'title' => esc_html__('Dimensions', 'energyplus'),
      'values' => array(
        array(
          'title' => 'W',
          'id' => 'w',
          'values'=> array(3,'3_5',4,'4_5',5,'5_5',6,'6_5',7,'7_5',8,'8_5',9,'9_5',10)
        ),
        array(
          'title' => 'H',
          'id' => 'h',
          'values'=> array(2,3,4,5,6,7,8,9,10)
        ),
      )
    ),


    'range' => (isset($settings['range'])) ? $settings['range'] : 'hourly'
  );

}
}

?>
