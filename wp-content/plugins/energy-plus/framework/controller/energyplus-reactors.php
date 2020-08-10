<?php

/**
* EnergyPlus Reactors
*
* Plugins for E+
*
* @since      1.1.0
* @package    EnergyPlus
* @subpackage EnergyPlus/framework
* @author     EN.ER.GY <support@en.er.gy>
* */


if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

class EnergyPlus_Reactors extends EnergyPlus {

  /**
  * Reactor list
  *
  * @return void
  */

  public static function reactors_list($id = false) {
    $list = array(

      'announcements' => array(
        'id'          => 'announcements',
        'title'       => __('Announcements', 'energyplus'),
        'description' => __('Announcements for Admins and Shop Managers.', 'energyplus'),
        'details'     => __('Announcements for Admins and Shop Managers. It has no effect on frontend.', 'energyplus'),
        'active'      => 0,
        'order'       => 500,
        'badge'       => __('NEW', 'energyplus'),
        'url'         => false
      ),

      'energizer' => array(
        'id'          => 'energizer',
        'title'       => __('Energizer', 'energyplus'),
        'description' => __('Apply E+ styles to important WP / WC pages', 'energyplus'),
        'details'     => __('This reactor adapts some important WordPress and WooCommerce pages to the E+ style. <br><br> <strong>Important Note:</strong> You need to enable <u>Settings > Full Mode</u> to use this reactor.', 'energyplus'),
        'active'      => 0,
        'order'       => 400,
        'badge'       => __('NEW', 'energyplus'),
        'url'         => false
      ),

      'login' => array(
        'id'          => 'login',
        'title'       => __('Login Page', 'energyplus'),
        'description' => __('Customize WordPress Login page of your store', 'energyplus'),
        'details'     => __('This reactor adapts your WP Login page to the E+ style. <br><br> <strong>Important Note:</strong> If you are already using an other WP Login Page Customizer plugin, <u>do not activate</u> this reactor!', 'energyplus'),
        'active'      => 0,
        'order'       => 300,
        'badge'       => __('NEW', 'energyplus'),
        'url'         => false
      ),

      'tweaks' => array(
        'id'          => 'tweaks',
        'title'       => __('Tweaks', 'energyplus'),
        'description' => __('Allows you to set up some additional features for Energy+', 'energyplus'),
        'details'     => __('Allows you to set up some additional features for Energy+ <br> <br>For example; you can adjust the size of the detail screen opened from the right, select which statuses will appear in the Orders panel, etc.', 'energyplus'),
        'active'      => 0,
        'order'       => 200,
        'badge'       => false,
        'url'         => false
      ),

      'dah' => array(
        'id'          => 'dah',
        'title'       => __('App Helper', 'energyplus'),
        'description' => __('Helps you create a desktop app to manage your store', 'energyplus'),
        'details'     => __('Helps you create a desktop app to manage your store', 'energyplus'),
        'active'      => 0,
        'order'       => 100,
        'badge'       => false,
        'url'         => 'Create now|//en.er.gy/plus/desktop-app-helper?v=1'
      )

    );

    if ($id) {
      return $list[$id];
    }

    return $list;
  }

  /**
  * Starts everything
  *
  * @return void
  */

  public static function run() {

    if (!EnergyPlus_Admin::is_admin(null)) {
      wp_die('Not allowed');
    }

    self::route();
  }

  /**
  * Router for sub pages
  *
  * @return void
  */

  private static function route()	{

    switch (EnergyPlus_Helpers::get('action')) {

      case 'settings':
      self::settings();
      break;

      case 'detail':
      self::detail();
      break;

      case 'activate':
      self::activate();
      break;

      case 'energy-activate':
      self::energy_activate();
      break;

      default:
      self::index();
      break;
    }
  }

  /**
  * Main function
  *
  * @return void
  */

  public static function index()	{

    $available = self::reactors_list();
    $counts    = array('active'=>0, 'inactive'=>0);
    $map       = EnergyPlus::option('reactors_list', array());

    foreach ($map AS $_map)	{

      if (isset($available[$_map['id']])) {
        $available[$_map['id']]['active'] = 1;
        $available[$_map['id']]['order'] = $available[$_map['id']]['order'] + 1000;
        ++$counts['active'];
      }
    }

    $counts['inactive'] = count($available) - $counts['active'];

    array_multisort(array_map(function($element) {
      return $element['order'];
    }, $available), SORT_DESC, $available);


    echo EnergyPlus_View::run('reactors/list',  array('all' => $available, 'counts'=>$counts ) );
  }


  public static function activate() {

    $id = sanitize_key(EnergyPlus_Helpers::get('id', 0));

    EnergyPlus_Helpers::ajax_nonce(true, $id);

    
    $map = EnergyPlus::option('reactors_list', array());

    if ('deactivate' === EnergyPlus_Helpers::get('do')) {
      unset($map[$id]);

      $class = "Reactors__$id". "__" . $id;
      $class::deactivate();

    } else {
      $map[$id] = array('id' =>$id, 'date' => date('Y-m-d H:i:s'));
    }

    EnergyPlus::option('reactors_list', $map, 'set');

    wp_redirect( EnergyPlus_Helpers::admin_page('reactors', array('action'=>'detail', 'id' => $id, 'later'=>1 )));
  }



  /**
  * Details of reactor
  *
  * @return void
  */

  public static function detail()	{

    $id = sanitize_key(EnergyPlus_Helpers::get('id', 0));

    if (!isset( self::reactors_list()[$id] ) ) {
      wp_die('Not allowed');
    }

    if (!self::is_installed($id)) {
      self::needs();
      echo EnergyPlus_View::run('reactors/detail',  array( 'reactor'=>self::reactors_list($id) ) );
    } else {
      $class = "Reactors__$id". "__" . $id;
      $class::settings();
    }


  }

  public static function is_installed($id) {
    if (isset(EnergyPlus::option('reactors_list', array())[$id])) {
      return true;
    } else {
      return false;
    }
  }

  public static function needs() {
    $active = EnergyPlus::option('active', false);

    return true;
   

    if (false === $active) {
      echo EnergyPlus_View::run('reactors/needs', array('step'=>0) );
      die;
    } else {
      $parts = explode(':', $active);
      $control = md5($parts[0].esc_url_raw(get_bloginfo('url')));
      if ($active !== $parts[0].":".$control.":".md5($control)) {
        echo EnergyPlus_View::run('reactors/needs', array('step'=>0) );
        die;
      }
    }
  }

  public static function energy_activate() {

    $step = 1;
    $response = "";

    if ($_POST) {

      if ( ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'energyplus_reactors' ) ) {
        exit;
      }

      $data = array(
        'code' => 'nullmasterinbabiato',
        'url' => esc_url_raw(get_bloginfo('url'))
      );

      $response = 200;

   

      $result = json_decode(sanitize_text_field($response['body']), true);

    
       
          $step = 3;
          $response = sanitize_text_field($result['response']);

         $data['code'] = 'B5E0B5F8DD8689E6ACA49DD6E6E1A930';
          $key =  md5($data['code']).':'.md5(md5($data['code']).$data['url']).':'.sanitize_key($response);
          EnergyPlus::option('active', $key, 'set');
          wp_redirect( EnergyPlus_Helpers::admin_page('reactors', array('action'=>'detail', 'id' => EnergyPlus_Helpers::get('id', 'tweaks'), 'activated'=>1 )));
       
  }
  echo EnergyPlus_View::run('reactors/needs', array('step'=> 1, 'return' => $step, 'response'=>$response ) );

}
}
