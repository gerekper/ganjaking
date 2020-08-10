<?php

/**
* EnergyPlus Helpers
*
* Helper functions
*
* @since      1.0.0
* @package    EnergyPlus
* @subpackage EnergyPlus/framework
* @author     EN.ER.GY <support@en.er.gy>
* */


if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

class EnergyPlus_Helpers extends EnergyPlus {


  /**
  * Generate and verify nonce
  *
  * @since  1.0.0
  * @param  boolean   $get_nonce
  * @param  string    $action
  */

  public static function ajax_nonce( $get_nonce = false, $action = 'energyplus-general') {
    if (TRUE === $get_nonce) {
      $nonce = sanitize_key($_REQUEST['_wpnonce']);

      if ( ! wp_verify_nonce( $nonce, $action ) ) {
        exit;
      }
    }

    return "_wpnonce: jQuery('input[name=_wpnonce]').val(), _wp_http_referer: jQuery('input[name=_wp_http_referer]').val()";
  }

  /**
  * Generate submenu at segments
  *
  * @since  1.0.0
  * @param  string    $panel
  */

  public static function submenu($panel) {

    $menu = EnergyPlus::option('menu', array());
    $class = '__A__Button1';

    $panel_parent = 'energyplus-'.$panel;

    foreach ($menu AS $_m_k => $_m)  {

      if (isset($_m['parent']) && (1 === $_m['active']) && ($panel_parent === $_m['parent'])) {
        $secure_url = EnergyPlus_Helpers::secure_url($panel, md5($_m_k), array('go' => $_m_k, 'status'=>'none', 'action'=>'none'));

        if ($_m_k === EnergyPlus_Helpers::get('go')) {
          $class='__A__Button1 __A__Selected';
        }
        echo sprintf('<li>
        <a class="%s" href="%s">%s</a>
        </li>', $class, $secure_url, $_m['title']);
      }
    }
  }

  /**
  * Get sub menu url by id
  *
  * @since  1.0.0
  * @param  int    $id
  */

  public static function get_submenu_url($id) {

    $menu = EnergyPlus::option('menu', array());

    if (isset($menu[$id]) && filter_var($menu[$id]['admin_link'], FILTER_VALIDATE_URL)) {
      return esc_url($menu[$id]['admin_link']);
    }

  }

  /**
  * Generate an url with nonce
  *
  * @since  1.0.0
  * @param  string    $segment
  * @param  string    $action
  * @param  array     $others
  * @return string
  */

  public static function secure_url($segment, $action='asterik-u', $others = array()) {
    if ('frame' === $segment OR isset($others['go'])) {
      return self::admin_page($segment, $others);
    }

    return wp_nonce_url( self::admin_page($segment, $others), $action );
  }

  /**
  * Sanitize a string
  *
  * @since  1.0.0
  * @param  string    $string
  * @param  string    $secondary
  * @return string
  */

  public static function clean (&$string, $secondary = '') {

    if (!isset($string) OR '' === trim($string) ) {
      $string =  $secondary;
    }

    return sanitize_text_field($string);
  }

  /**
  * Generate admin link for EnergyPlus panels
  *
  * @since  1.0.0
  */

  public static function admin_page($segment = '', $others = array()) {
    return admin_url( "admin.php?page=energyplus&segment=" . $segment ."&". implode('&', array_map(
      function ($v, $k) { return "$k=$v"; },
      $others,
      array_keys($others)
    )));
  }

  /**
  * Generate iframe object for given url
  *
  * @since  1.0.0
  * @param  string    $page
  */

  public static function frame ($page) {
    echo EnergyPlus_View::run('core/frame', array( 'page' => esc_url($page) ));
    return;
  }

  /**
  * Rebuild url
  *
  * @since  1.0.0
  */

  public static function change_url($from, $to, $old_classes, $new_classes, $is_default = false) {

    $class = '" class="'.$old_classes.'"';
    $url = 		remove_query_arg( $from, filter_input(INPUT_SERVER, 'REQUEST_URI'));

    if ( ( isset( $_GET[ $from ] ) && ( $to  === $_GET[ $from ] ) ) OR (!isset( $_GET[ $from ] ) && $is_default === TRUE)) {
      $class = '" class="'.$old_classes. ' '. $new_classes. ' __A__Selected"';
    }

    return esc_url( add_query_arg (
      array($from=>$to)
    ), $url ) . $class;
  }

  /**
  * Generate product image div by id
  *
  * @since  1.0.0
  */
  public static function product_image($product_id, $quantity = 0, $style='', $title = false) {

    if ($title) {
      echo '<div class="__A__Product_Image_Container">';
      $image = get_the_post_thumbnail_url($product_id, array(150,150));
      if ($image) {
        echo '<img src="' . esc_url_raw($image) .'" title="' . esc_attr($title) .'" class="__A__Product_Image" style="'. esc_attr($style). '" >';
        if (1 < $quantity) {
          echo '<span class="badge badge-pill badge-danger __A__Product_Image_Qny">'. esc_html($quantity). '</span>';
        }
      }
      echo '</div>';
    } else {

      if (0 < absint($product_id)) {
        $product = wc_get_product($product_id);

        if (!$product) {
          return;
        }

        echo '<div class="__A__Product_Image_Container">';
        $image = get_the_post_thumbnail_url($product_id, array(150,150));
        if ($image) {
          echo '<img src="' . esc_url_raw($image) .'" title="' . esc_attr($product->get_title()) .'" class="__A__Product_Image" style="'. esc_attr($style). '" >';
          if (1 < $quantity) {
            echo '<span class="badge badge-pill badge-danger __A__Product_Image_Qny">'. esc_html($quantity). '</span>';
          }
        }
        echo '</div>';

      }
    }

  }

  /**
  * Generate an url for sorting
  *
  * @since  1.0.0
  */

  public static function thead_sort($sort) {

    $class = '';
    $url = 		remove_query_arg( array( 'orderby', 'order' ), filter_input(INPUT_SERVER, 'REQUEST_URI'));
    $order = ( isset( $_GET[ 'order' ] ) && $_GET[ 'order'] === "ASC" ) ? "DESC" : "ASC";

    if ( isset( $_GET[ 'orderby' ] ) && ( $sort === $_GET[ 'orderby' ] ) )
    {
      $class = '" class="__A__Order_'. esc_attr($order);
    }

    return esc_url( add_query_arg (
      array ( 'orderby' => $sort, 'order' => $order )
    ), $url ) . $class;

  }

  /**
  * Group arrays by date
  *
  * @since  1.0.0
  * @param  string    $date
  */

  public static function grouped_time ($date)  {

    $date = wc_format_datetime($date,'Y-m-d H:i:s');

    if (intval(EnergyPlus_Helpers::strtotime('now', 'z')) === intval(EnergyPlus_Helpers::strtotime($date, 'z'))) {
      return array('key'=>'d'.EnergyPlus_Helpers::strtotime($date, 'z'), 'title'=>esc_html__('Today', 'energyplus'));
    }

    if (intval((EnergyPlus_Helpers::strtotime('now', 'z')-1)) === intval(EnergyPlus_Helpers::strtotime($date, 'z'))) {
      return array('key'=>'d'.EnergyPlus_Helpers::strtotime($date, 'z'), 'title'=> esc_html__('Yesterday', 'energyplus'));
    }

    if (EnergyPlus_Helpers::strtotime($date,'Ymd') >=  EnergyPlus_Helpers::strtotime('monday this week','Ymd')) {
      return array('key'=>'w'.EnergyPlus_Helpers::strtotime($date,'mW'), 'title'=> esc_html__('This Week', 'energyplus'));
    }

    if ((EnergyPlus_Helpers::strtotime($date, 'Ymd') <=  EnergyPlus_Helpers::strtotime('monday this week', 'Ymd')) AND (EnergyPlus_Helpers::strtotime($date, 'Ymd') >=  EnergyPlus_Helpers::strtotime('monday last week','Ymd'))) {
      return array('key'=>'w'.EnergyPlus_Helpers::strtotime($date,'mW'), 'title'=> esc_html__('Last Week', 'energyplus'));
    }

    if ((EnergyPlus_Helpers::strtotime($date, 'Ymd') <=  EnergyPlus_Helpers::strtotime('monday last week', 'Ymd')) AND (EnergyPlus_Helpers::strtotime($date, 'Ymd') >=  EnergyPlus_Helpers::strtotime('first day of this month','Ymd'))) {
      return array('key'=>'m'.EnergyPlus_Helpers::strtotime($date,'m'), 'title'=> esc_html__('This Month', 'energyplus'));
    }

    if ((EnergyPlus_Helpers::strtotime($date, 'Ymd') <  EnergyPlus_Helpers::strtotime('first day of this month', 'Ymd')) AND (EnergyPlus_Helpers::strtotime($date, 'Ymd') >=  EnergyPlus_Helpers::strtotime('first day of January','Ymd'))) {
      return array('key'=>'m'.EnergyPlus_Helpers::strtotime($date,'m'), 'title'=> date_i18n('F', strtotime($date)));
    }

    return array('key'=>'y'.EnergyPlus_Helpers::strtotime($date,'Y'), 'title'=> esc_html__('Year ', 'energyplus') . EnergyPlus_Helpers::strtotime($date,'Y'));

  }

  /**
  * Determine selected items by GET
  *
  * @since  1.0.0
  */

  public static function selected($key = '', $selected = false, $class = ' __A__Selected') {

    if (!isset( $_GET[$key] ) ) {
      $_GET[$key] = false;
    }

    if ( $_GET[$key] === $selected) {
      echo esc_attr($class);
    }
  }

  /**
  * Send query results to $api variable for using global
  *
  * @since  1.0.0
  */

  public static function api_pagination($_this, $query) {

    EnergyPlus_Admin::$api = array('query'=>$query);

  }

  /**
  * Sanitize GET
  *
  * @since  1.0.0
  */

  public static function get($key = '', $default = '') {
    if (!isset($_GET[$key]) OR empty($_GET[$key])  )
    {
      return $default;
    }

    return sanitize_text_field ( $_GET[$key] );
  }

  /**
  * Sanitize POST
  *
  * @since  1.0.0
  */

  public static function post($key = '', $default = '') {
    if (!isset( $_POST[$key] )) {
      return $default;
    }

    return sanitize_text_field ( $_POST[$key] );
  }

  /**
  * Is this request an ajax?
  *
  * @since  1.0.0
  */

  public static function is_ajax() {

    if (!empty(filter_input(INPUT_SERVER, 'HTTP_X_REQUESTED_WITH')) && strtolower(filter_input(INPUT_SERVER, 'HTTP_X_REQUESTED_WITH')) === 'xmlhttprequest') {
      return true;
    } else {
      return false;
    }
  }

  /**
  * Groups array by key
  *
  * @since  1.0.0
  */

  public static function group_by($key, $data) {
    $result = array();
    foreach($data as $val) {
      if(array_key_exists($key, $val)){
        $result[$val[$key]][] = $val;
      }else{
        $result[""][] = $val;
      }
    }

    return $result;
  }



  /**
  * Is this a desktop app?
  *
  * @since  1.0.0
  */

  public static function is_desktop_app() {

    $user_agent = EnergyPlus_Helpers::clean($_SERVER ['HTTP_USER_AGENT']);

    $platform = 'none';

    if (stripos( 'EN.ER.GY Plus (Mac)', $user_agent)!==false) {
      $platform = 'Mac';
    } else	if (stripos($user_agent, 'EN.ER.GY Plus (Windows)')!==false) {
      $platform = 'Windows';
    }

    if ('none' !== $platform ) {
      echo wp_kses_post('<div class="__A__Desktop_Controls_Drag"></div>
      <div class="__A__Desktop_Controls __A__Desktop_' . esc_attr($platform) .'">
      <a href="#" class="__A__Desktop_Control __A__Desktop_Control_Minimize" data-do="minimize">-</a>
      <a href="#" class="__A__Desktop_Control __A__Desktop_Control_Close"  data-do="close">x</a>
      </div>');

    }

  }

  /**
  * Forked from WooCommerce's native get_woocommerce_currency_symbol() because of some incompatible with some 3rd party plugins
  *
  * @since  1.0.0
  */
  public static function get_woocommerce_currency_symbol( $currency = '' ) {
    if ( ! $currency ) {
      $currency = get_woocommerce_currency();
    }

    $symbols = apply_filters( 'woocommerce_currency_symbols', array(
      'AED' => '&#x62f;.&#x625;',
      'AFN' => '&#x60b;',
      'ALL' => 'L',
      'AMD' => 'AMD',
      'ANG' => '&fnof;',
      'AOA' => 'Kz',
      'ARS' => '&#36;',
      'AUD' => '&#36;',
      'AWG' => 'Afl.',
      'AZN' => 'AZN',
      'BAM' => 'KM',
      'BBD' => '&#36;',
      'BDT' => '&#2547;&nbsp;',
      'BGN' => '&#1083;&#1074;.',
      'BHD' => '.&#x62f;.&#x628;',
      'BIF' => 'Fr',
      'BMD' => '&#36;',
      'BND' => '&#36;',
      'BOB' => 'Bs.',
      'BRL' => '&#82;&#36;',
      'BSD' => '&#36;',
      'BTC' => '&#3647;',
      'BTN' => 'Nu.',
      'BWP' => 'P',
      'BYR' => 'Br',
      'BYN' => 'Br',
      'BZD' => '&#36;',
      'CAD' => '&#36;',
      'CDF' => 'Fr',
      'CHF' => '&#67;&#72;&#70;',
      'CLP' => '&#36;',
      'CNY' => '&yen;',
      'COP' => '&#36;',
      'CRC' => '&#x20a1;',
      'CUC' => '&#36;',
      'CUP' => '&#36;',
      'CVE' => '&#36;',
      'CZK' => '&#75;&#269;',
      'DJF' => 'Fr',
      'DKK' => 'DKK',
      'DOP' => 'RD&#36;',
      'DZD' => '&#x62f;.&#x62c;',
      'EGP' => 'EGP',
      'ERN' => 'Nfk',
      'ETB' => 'Br',
      'EUR' => '&euro;',
      'FJD' => '&#36;',
      'FKP' => '&pound;',
      'GBP' => '&pound;',
      'GEL' => '&#x10da;',
      'GGP' => '&pound;',
      'GHS' => '&#x20b5;',
      'GIP' => '&pound;',
      'GMD' => 'D',
      'GNF' => 'Fr',
      'GTQ' => 'Q',
      'GYD' => '&#36;',
      'HKD' => '&#36;',
      'HNL' => 'L',
      'HRK' => 'Kn',
      'HTG' => 'G',
      'HUF' => '&#70;&#116;',
      'IDR' => 'Rp',
      'ILS' => '&#8362;',
      'IMP' => '&pound;',
      'INR' => '&#8377;',
      'IQD' => '&#x639;.&#x62f;',
      'IRR' => '&#xfdfc;',
      'IRT' => '&#x062A;&#x0648;&#x0645;&#x0627;&#x0646;',
      'ISK' => 'kr.',
      'JEP' => '&pound;',
      'JMD' => '&#36;',
      'JOD' => '&#x62f;.&#x627;',
      'JPY' => '&yen;',
      'KES' => 'KSh',
      'KGS' => '&#x441;&#x43e;&#x43c;',
      'KHR' => '&#x17db;',
      'KMF' => 'Fr',
      'KPW' => '&#x20a9;',
      'KRW' => '&#8361;',
      'KWD' => '&#x62f;.&#x643;',
      'KYD' => '&#36;',
      'KZT' => 'KZT',
      'LAK' => '&#8365;',
      'LBP' => '&#x644;.&#x644;',
      'LKR' => '&#xdbb;&#xdd4;',
      'LRD' => '&#36;',
      'LSL' => 'L',
      'LYD' => '&#x644;.&#x62f;',
      'MAD' => '&#x62f;.&#x645;.',
      'MDL' => 'MDL',
      'MGA' => 'Ar',
      'MKD' => '&#x434;&#x435;&#x43d;',
      'MMK' => 'Ks',
      'MNT' => '&#x20ae;',
      'MOP' => 'P',
      'MRO' => 'UM',
      'MUR' => '&#x20a8;',
      'MVR' => '.&#x783;',
      'MWK' => 'MK',
      'MXN' => '&#36;',
      'MYR' => '&#82;&#77;',
      'MZN' => 'MT',
      'NAD' => '&#36;',
      'NGN' => '&#8358;',
      'NIO' => 'C&#36;',
      'NOK' => '&#107;&#114;',
      'NPR' => '&#8360;',
      'NZD' => '&#36;',
      'OMR' => '&#x631;.&#x639;.',
      'PAB' => 'B/.',
      'PEN' => 'S/.',
      'PGK' => 'K',
      'PHP' => '&#8369;',
      'PKR' => '&#8360;',
      'PLN' => '&#122;&#322;',
      'PRB' => '&#x440;.',
      'PYG' => '&#8370;',
      'QAR' => '&#x631;.&#x642;',
      'RMB' => '&yen;',
      'RON' => 'lei',
      'RSD' => '&#x434;&#x438;&#x43d;.',
      'RUB' => '&#8381;',
      'RWF' => 'Fr',
      'SAR' => '&#x631;.&#x633;',
      'SBD' => '&#36;',
      'SCR' => '&#x20a8;',
      'SDG' => '&#x62c;.&#x633;.',
      'SEK' => '&#107;&#114;',
      'SGD' => '&#36;',
      'SHP' => '&pound;',
      'SLL' => 'Le',
      'SOS' => 'Sh',
      'SRD' => '&#36;',
      'SSP' => '&pound;',
      'STD' => 'Db',
      'SYP' => '&#x644;.&#x633;',
      'SZL' => 'L',
      'THB' => '&#3647;',
      'TJS' => '&#x405;&#x41c;',
      'TMT' => 'm',
      'TND' => '&#x62f;.&#x62a;',
      'TOP' => 'T&#36;',
      'TRY' => '&#8378;',
      'TTD' => '&#36;',
      'TWD' => '&#78;&#84;&#36;',
      'TZS' => 'Sh',
      'UAH' => '&#8372;',
      'UGX' => 'UGX',
      'USD' => '&#36;',
      'UYU' => '&#36;',
      'UZS' => 'UZS',
      'VEF' => 'Bs F',
      'VND' => '&#8363;',
      'VUV' => 'Vt',
      'WST' => 'T',
      'XAF' => 'CFA',
      'XCD' => '&#36;',
      'XOF' => 'CFA',
      'XPF' => 'Fr',
      'YER' => '&#xfdfc;',
      'ZAR' => '&#82;',
      'ZMW' => 'ZK',
    ) );
    $currency_symbol = isset( $symbols[ $currency ] ) ? $symbols[ $currency ] : '';

    return  $currency_symbol;
  }


  /**
  * Time functions
  *
  * @since  1.0.0
  */

  public static function time($default = null) {
    return current_time('mysql') ;
  }

  public static function timestamp($time) {

    return gmdate( 'Y-m-d H:i:s', ( $time + ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS )));

  }

  /**
  * Get strtotime and parse it to date
  *
  * @since  1.0.0
  */

  public static function strtotime($time, $format = 'Y-m-d H:i:s', $tz = true) {

    $tz_string = get_option('timezone_string');
    $tz_offset = get_option('gmt_offset', 0);

    if (!empty($tz_string)) {
      // If site timezone option string exists, use it
      $timezone = $tz_string;

    } elseif ($tz_offset === 0) {
      // get UTC offset, if it isn’t set then return UTC
      $timezone = 'UTC';

    } else {
      $timezone = $tz_offset;

      if(substr($tz_offset, 0, 1) != "-" && substr($tz_offset, 0, 1) != "+" && substr($tz_offset, 0, 1) != "U") {
        $timezone = "+" . $tz_offset;
      }
    }

    $datetime = new DateTime($time, new DateTimeZone($timezone));
    return $datetime->format($format);

    $diff = 0;

    if ('today' === $time) {
      $time = 'now';
      $format = str_replace('H:i:s', '00:00:00', $format);
    }

    if ($tz) {
      $diff =  get_option( 'gmt_offset' ) * HOUR_IN_SECONDS ;
    }

    return gmdate( $format,  strtotime($time) + $diff);

  }

  public static function sanitize_array($array_or_string = array()) {
    if( is_string($array_or_string) ){
      $array_or_string = sanitize_text_field($array_or_string);
    }elseif( is_array($array_or_string) ){
      foreach ( $array_or_string as $key => &$value ) {
        if ( is_array( $value ) ) {
          $value = self::sanitize_array($value);
        }
        else {
          $value = sanitize_text_field( $value );
        }
      }
    }

    return $array_or_string;
  }

  public static function option_color($args = array()) {
    echo '<div class="__A__Helpers_Row row">
    <div class="float-left"><input name="' . esc_attr($args['name']). '" type="text" value="' . esc_attr($args['value']). '" class="__A__Reactors_Color_' . esc_attr($args['name']). ' ' . esc_attr($args['css']). ' energyplus-color-field energyplus-color-field-tmpID" data-default-color="' . esc_attr($args['value']). '"
    /></div><div class="float-left pt-0"> &nbsp; '.esc_html($args['label']) . '</div></div>';
    if (!isset($args['no-js']) || (isset($args['no-js']) && $args['no-js'] === false)) {
      echo '<script>
      jQuery(document).ready(function() {
        "use strict";
        jQuery(".__A__Reactors_Color_' . esc_attr($args['name']). '").wpColorPicker({ width:160 });
      });
      </script>';
    }
  }

  public static function need() {
    global $wpdb;

    $active = EnergyPlus::option('ac' . 'tive', false);
    $need = false;

    if (false !== $active) {
      $parts = explode(':', $active);
      $control = md5($parts[0].esc_url_raw(get_bloginfo('url')));
      if ($active === $parts[0].":".$control.":".md5($control)) {
        $need = true;
      }
    }

    if (!$need) {
      $query = $wpdb->prepare("SELECT time FROM {$wpdb->prefix}energyplus_events ORDER BY event_id ASC LIMIT %d", 1);

      if ($query) {
        $first = $wpdb->get_var($query);
        $first = strtotime($first);

        if ($first < (time()-(1*24*60*60))) {
          echo '<div style="position:relative;background:#353535; width:90%; margin:0 auto; margin-bottom:30px; border-radius:10px; padding:20px;color:#fff;text-align:center">To activate your plugin, please <a href="'.EnergyPlus_Helpers::admin_page('reactors', array('action'=>'energy-activate')).'" class="trig text-warning">click here</a></div>';
        }
      }
    }
  }


}
?>
