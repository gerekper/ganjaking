<?php

/**
* Announcements
*
* @since      1.2.2
* @package    EnergyPlus
* @subpackage EnergyPlus/framework/libs/widgets
* @author     EN.ER.GY <support@en.er.gy>
* */


if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}


class Reactors__announcements__announcements  {

  public static function settings() {
    global $wpdb;

    $reactor = EnergyPlus_Reactors::reactors_list('announcements');

    if($_POST) {

      if ( ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'energyplus_reactors' ) ) {
        exit;
      }
    }

    if ('edit' === EnergyPlus_Helpers::get('do')) {
      self::edit();
      return false;
    }

    if ('delete' === EnergyPlus_Helpers::get('do')) {
      if ( ! wp_verify_nonce( EnergyPlus_Helpers::get('_wpnonce'), absint(EnergyPlus_Helpers::get('view', 0)) ) ) {
        wp_die( 'Security check' );
      }

      $wpdb->delete( "{$wpdb->prefix}energyplus_events", array( 'event_id' => absint(EnergyPlus_Helpers::get('view', 0)) ) );

    }

    $saved = 0;

    // List

    $posts = $wpdb->get_results(
      $wpdb->prepare("SELECT event_id, user, type, id, extra, time FROM {$wpdb->prefix}energyplus_events WHERE type = %d ORDER BY event_id DESC", 15)
      , ARRAY_A);

      echo EnergyPlus_View::reactor('announcements/views/list', array('reactor' => $reactor, 'posts'=>$posts, 'saved' => $saved));
    }

    public static function edit() {
      global $wpdb;

      wp_enqueue_script( 'energyplus-footer', EnergyPlus_Public . "js/energyplus-footer.js", array( 'wp-color-picker' ), EnergyPlus_Version, true );
      wp_enqueue_style( "energyplus-iconpicker", EnergyPlus_Public . '3rd/iconpicker/css/bootstrap-iconpicker.min.css' );
      wp_enqueue_script( 'energyplus-iconpicker-js', EnergyPlus_Public . "3rd/iconpicker/js/bootstrap-iconpicker.bundle.min.js", array( ), EnergyPlus_Version, true );

      $saved = 0;

      $id = absint(EnergyPlus_Helpers::get('view', 0));

      if ($_POST) {

        $post['title']      = EnergyPlus_Helpers::post('title','');
        $post['content']    = wp_kses_post($_POST['content']);
        $post['icon']       = EnergyPlus_Helpers::post('icon','');
        $post['created_by'] = get_current_user_id();
        $post['updated_at'] = EnergyPlus_Helpers::strtotime('now');
        $post['users']      = 0;

        if (0 === $id) {
          $data = array(
            'type'  => 15,
            'id'    => 0,
            'extra' => maybe_serialize($post)
          );

          EnergyPlus_Events::add( $data );
        } else {
          $wpdb->update(
            $wpdb->prefix."energyplus_events",
            array('extra'=>maybe_serialize($post)),
            array( 'event_id' => $id )
          );
        }

        wp_redirect(EnergyPlus_Helpers::admin_page('reactors', array('action'=> 'detail', 'id'=>'announcements')));
      }

      if (0 < $id) {
        $post = $wpdb->get_row(
          $wpdb->prepare("SELECT event_id, user, type, id, extra, time FROM {$wpdb->prefix}energyplus_events WHERE type = 15 AND event_id = %d", $id)
        );

        if (!$post) {
          wp_die('Not allowed');
        }

        $post = maybe_unserialize($post->extra);

        if (!is_array($post)) {
          wp_die('Not allowed');
        }

      } else {
        $post = array('title'=>'', 'content'=>'', 'icon'=>'');
      }

      echo EnergyPlus_View::reactor('announcements/views/edit', array('post'=>$post, 'saved' => $saved));

    }



    public static function init() {
    }

    public static function deactivate() {
    }
  }
