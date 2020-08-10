<?php

/**
* WIDGET
*
* Links
*
*
* @since      1.2.2
* @package    EnergyPlus
* @subpackage EnergyPlus/framework/libs/widgets
* @author     EN.ER.GY <support@en.er.gy>
* */


if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}


class Widgets__Links extends EnergyPlus_Widgets {

  public static $name = 'Shortcuts';
  public static $multiple = true;

  public static function run ( $args = array(), $settings = array() ) {

    $items = EnergyPlus::option('widgets-links-' . $args['id'], array());
    $style = intval(EnergyPlus::option('widgets-links-style-' . $args['id'], 1));
    $per_line = intval(EnergyPlus::option('widgets-links-items-' . $args['id'], 2));


    if (EnergyPlus_Helpers::is_ajax() OR isset( $args['ajax'] ))  {
      return EnergyPlus_View::run('widgets/links',  array('args' => $args, 'items' => $items, 'style'=>$style, 'per_line'=>$per_line));
    } else {
      echo EnergyPlus_View::run('widgets/links',  array('args' => $args, 'items' => $items, 'style'=>$style, 'per_line'=>$per_line));
    }
  }

  public static function setup($id = 0) {

    wp_enqueue_style( 'wp-color-picker' );
    wp_enqueue_script( 'energyplus-footer', EnergyPlus_Public . "js/energyplus-footer.js", array( 'wp-color-picker' ), EnergyPlus_Version, true );
    wp_enqueue_style( "energyplus-iconpicker", EnergyPlus_Public . '3rd/iconpicker/css/bootstrap-iconpicker.min.css' );
    wp_enqueue_script( 'energyplus-iconpicker-js', EnergyPlus_Public . "3rd/iconpicker/js/bootstrap-iconpicker.bundle.min.js", array( ), EnergyPlus_Version, true );

    $id = sanitize_key($id);

    $saved = 0;

    if ($_POST) {

      if ( ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'energyplus_reactors' ) ) {
        exit;
      }


      $items = array();
      foreach ($_POST['tmp_id'] AS $item) {
        $tmp = sanitize_key($item);
        if ('' !== EnergyPlus_Helpers::post('title_'.$tmp, '')) {
          $items[$tmp]['id'] = $tmp;
          $items[$tmp]['title'] = EnergyPlus_Helpers::post('title_'.$tmp, '');
          $items[$tmp]['url'] = esc_url_raw(EnergyPlus_Helpers::post('url_'.$tmp, ''));
          $items[$tmp]['open'] = EnergyPlus_Helpers::post('open_'.$tmp, '');
          $items[$tmp]['icon'] = EnergyPlus_Helpers::post('icon_'.$tmp, '');
          $items[$tmp]['users'] = EnergyPlus_Helpers::sanitize_array($_POST['users_'.$tmp]);
          $items[$tmp]['background_color'] = str_replace("#ffffff", "transparent", EnergyPlus_Helpers::post('background_color_'.$tmp, ''));
          $items[$tmp]['text_color'] =  EnergyPlus_Helpers::post('text_color_'.$tmp, '');
          $items[$tmp]['active'] = EnergyPlus_Helpers::post('active_'.$tmp, '');
        }
      }

      EnergyPlus::option('widgets-links-' . $id, $items, 'set');
      EnergyPlus::option('widgets-links-style-' . $id, EnergyPlus_Helpers::post('style', '1'), 'set');
      EnergyPlus::option('widgets-links-items-' . $id, EnergyPlus_Helpers::post('per-line', '2'), 'set');

      $saved = 1;
    }



    $items = EnergyPlus::option('widgets-links-' . $id, array());
    $items[] = array(
      'id' => 'new',
      'title'=> __('Untitled', 'energyplus'),
      'url'=>'',
      'icon'=>'',
      'users'=>'',
      'background_color'=>'',
      'text_color'=>'',
      'icon_color'=>'',
      'users'=>array(),
      'active'=>true,
      'open'=>1,
      'new' => true
    );

    echo EnergyPlus_View::run('widgets/links-setup',  array( 'saved'=>$saved, 'id'=>$id, 'items'=>$items, 'per_line'=>EnergyPlus_Helpers::post('per-line', '2')));
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
            'values'=> array(3,'3_5', 4,'4_5',5,'5_5',6,'6_5',7,'7_5',8,'8_5',9,'9_5',10)
          ),
          array(
            'title' => 'H',
            'id' => 'h',
            'values'=> array(1,2,3,4,5,6,7,8,9,10)
          ),
        )
      ),


      'infos' => array(
        'type' => 'button',
        'title' => esc_html__('Settings', 'energyplus'),
        'link' => EnergyPlus_Helpers::admin_page('dashboard', array('action'=>'wd_settings', 'id'=>'%id%'))
      )

    );

  }
}

?>
