<?php

/**
* Energizer
*
* @since      1.1.9
* @package    EnergyPlus
* @subpackage EnergyPlus/framework/libs/widgets
* @author     EN.ER.GY <support@en.er.gy>
* */


if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}


class Reactors__energizer__energizer  {

  public static function settings() {

    $reactor = EnergyPlus_Reactors::reactors_list('energizer');

    $saved = 0;

    $screens = self::all_screens();

    if($_POST) {

      if ( ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'energyplus_reactors' ) ) {
        exit;
      }

      EnergyPlus::option('reactors-energizer-screens', EnergyPlus_Helpers::sanitize_array($_POST['reactors-energizer-screens']), 'set');

      EnergyPlus::option('reactors-energizer-shadow', EnergyPlus_Helpers::post('reactors-energizer-shadow', '0'), 'set');
      EnergyPlus::option('reactors-energizer-bg', EnergyPlus_Helpers::post('reactors-energizer-bg', '0'), 'set');
      EnergyPlus::option('reactors-energizer-click', EnergyPlus_Helpers::post('reactors-energizer-click', '0'), 'set');

      $saved = 1;
    }

    $settings = EnergyPlus::option('reactors-energizer-settings', array());

    echo EnergyPlus_View::reactor('energizer/views/settings', array('reactor' => $reactor, 'screens'=>$screens, 'settings'=>$settings, 'saved' => $saved));
  }

  public static function init() {

  }

  public static function all_screens() {
    return array(
      'edit-post'                    => __('Posts', 'energyplus'),
      'edit-page'                    => __('Pages','energyplus'),
      'upload'                       => __('Media','energyplus'),
      'attachment'                   => __('Edit Attachment','energyplus'),
      'edit-comments'                => __('Comments','energyplus'),
      'comment'                      => __('Comment > Edit','energyplus'),
      'edit-shop_order'              => __('Orders','energyplus'),
      'shop_order'                   => __('Orders > Edit','energyplus'),
      'edit-shop_coupon'             => __('Coupons','energyplus'),
      'shop_coupon'                  => __('Coupons > Edit','energyplus'),
      'edit-product'                 => __('Products','energyplus'),
      'product'                      => __('Products > Edit','energyplus'),
      'themes'                       => __('Themes','energyplus'),
      'theme-editor'                 => __('Theme Editor','energyplus'),
      'theme-install'                => __('Theme Install','energyplus'),
      'widgets'                      => __('Appearance > Widgets','energyplus'),
      'users'                        => __('Users','energyplus'),
      'user'                         => __('Users > Add New','energyplus'),
      'profile'                      => __('Users > Profile','energyplus'),
      'nav-menus'                    => __('Nav Menus','energyplus'),
      'plugins'                      => __('Plugins','energyplus'),
      'plugin-install'               => __('Plugins > Add New','energyplus'),
      'plugin-editor'                => __('Plugins > Editor','energyplus'),
      'woocommerce_page_wc-settings' => __('WooCommerce > Settings','energyplus'),
      'woocommerce_page_wc-reports'  => __('WooCommerce > Reports','energyplus'),
      'woocommerce_page_wc-status'   => __('WooCommerce > Status','energyplus'),
      'options-general'              => __('Options > General','energyplus'),
      'options-writing'              => __('Options > Writing ','energyplus'),
      'options-reading'              => __('Options > Reading ','energyplus'),
      'options-discussion'           => __('Options > Discussion ','energyplus'),
      'options-media'                => __('Options > Media','energyplus'),
      'options-permalink'            => __('Options > Permalink','energyplus'),
      'options-privacy'              => __('Options > Privacy','energyplus'),
      'tools'                        => __('Tools','energyplus'),
      'import'                       => __('Tools > Import','energyplus'),
      'export'                       => __('Tools > Export','energyplus'),
      'export-personal-data'         => __('Tools > Export Personal Data','energyplus'),
      'erase-personal-data'          => __('Tools > Erase Personal Data','energyplus'),
      'tools_page_action-scheduler'  => __('Tools > Action Scheduler','energyplus')
    );
  }

  public static function styles() {


    $screens = EnergyPlus::option('reactors-energizer-screens', array_keys(self::all_screens()));

    if (isset(get_current_screen()->id) && in_array(get_current_screen()->id, $screens)) {
      wp_enqueue_style("energyplus-reactors-energizer",     EnergyPlus_Public . "reactors/energizer/energizer.css", null, EnergyPlus_Version);
      wp_enqueue_script( 'energyplus-reactors-energizer', EnergyPlus_Public . "reactors/energizer/energizer.js", array(  ), EnergyPlus_Version, true );

      $css = ".wrap .search-box {display:none}";

      if (isset($_GET) && count($_GET) > 3) {
        $css .= ".__A__WP_searchbox {display:none} .tablenav.top {display:block} .wrap .search-box {display:block}";
      }

      if ("1" === EnergyPlus::option('reactors-energizer-shadow', "1")) {
        $css .= ".wp-list-table > tbody {box-shadow: 0 0 15px 0 rgba(0,0,0,.05) !important;}";
      }

      if ("1" === EnergyPlus::option('reactors-energizer-bg', "1")) {
        $css .= ".wp-list-table > tbody > tr td, .wp-list-table > tbody > tr th {background: transparent !important; }";
      }

      if ("1" === EnergyPlus::option('reactors-energizer-click', "0")) {
        wp_localize_script('energyplus-reactors-energizer', 'EnergyPlus_Energizer', array('click'=>1));
        $css .= ".wp-list-table .row-actions {display: none;}";

      } else {
        wp_localize_script('energyplus-reactors-energizer', 'EnergyPlus_Energizer', array('click'=>0));
      }

      wp_add_inline_style('energyplus-reactors-energizer', $css);

    }
  }

  public static function deactivate() {
    // Remove options
    delete_option('energyplus_reactors-energizer-screens');
    delete_option('energyplus_reactors-energizer-bg');
    delete_option('energyplus_reactors-energizer-shadow');
    delete_option('energyplus_reactors-energizer-click');

  }
}
