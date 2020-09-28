<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

class MpWpBakery {

  public function __construct() {
    add_filter( 'vc_add_element_categories', array( $this, 'tabs' ) );
    add_action( 'init', array( $this, 'settings' ) );
    add_filter( 'vc_shortcode_output', array( $this, 'shortcode' ), 10, 4 );
  }

  /**
   * Adds a new tab to the Visual Composer editor
   *
   * @param  array  $tabs   Tabs
   *
   * @return array
   */
  public function tabs( $tabs ) {
    $tabs[] = array(
      'name' => 'MemberPress',
      'filter' => 'memberpress',
      'active' => false
    );
    return $tabs;
  }

  /**
   * Add new settings to VC row
   *
   * @return void
   */
  public function settings() {

    $unauth_actions = array(
      __( 'Default', 'memberpress-wpbakery' ) => 'default',
      __( 'Hide', 'memberpress-wpbakery' ) => 'hide',
      __( 'Display Message', 'memberpress-wpbakery' ) => 'custom'
    );

    $rule_options = array(
      __( 'None', 'memberpress-wpbakery' ) => 'none'
    );

    // Assemble MP Rules into an options array
    foreach ( MeprCptModel::all( 'MeprRule' ) as $rule ) {
      $rule_options[$rule->post_title] = $rule->ID;
    }

    vc_add_param( 'vc_row', array(
      'type' => 'dropdown',
      'class' => '',
      'heading'  => __( 'MemberPress Rule', 'memberpress-wpbakery' ),
      'description' => __( 'Select a Rule to determine member access.', 'memberpress-wpbakery' ),
      'param_name' => 'memberpress_rule',
      'default' => 'none',
      'value' => $rule_options,
      'group' => 'MemberPress',
    ) );


    vc_add_param( 'vc_row', array(
      'type' => 'dropdown',
      'class' => '',
      'heading'  => __( 'Unauthorized Content', 'memberpress-wpbakery' ),
      'description' => __( 'What should be shown to unauthorized users.', 'memberpress-wpbakery' ),
      'param_name' => 'memberpress_unauthorized',
      'value' => $unauth_actions,
      'group' => 'MemberPress',
    ) );

    vc_add_param( 'vc_row', array(
      'type' => 'textfield',
      'heading'  => __( 'Custom Message', 'memberpress-wpbakery' ),
      'description' => __( 'Custom message for unauthorized users.', 'memberpress-wpbakery' ),
      'param_name' => 'memberpress_unauthorized_message',
      'group' => 'MemberPress',
    ) );
  }

  /**
   * Modify the content of the shortcode
   *
   * @param  string   $output       Shortcode content
   * @param  object   $shortcode    Module shortcode
   * @param  array    $attrs        Shortcode attributes
   * @param  string   $tag          Shortcode tag/ID
   *
   * @return string
   */
  public function shortcode( $output, $shortcode, $prepared_atts, $tag ) {

    if ( vc_is_inline() || 'vc_row' !== $tag || empty( $prepared_atts['memberpress_rule'] ) ) {
      return $output;
    }

    $rule = isset( $prepared_atts['memberpress_rule'] ) ? $prepared_atts['memberpress_rule'] : false;

    if ( empty( $rule ) || current_user_can( 'mepr-active', "rule: {$rule}" ) ) {
      return $output;
    }

    global $post;
    $mepr_options = MeprOptions::fetch();
    $global_message = MeprRulesCtrl::unauthorized_message( $post );
    $post_unauth_settings = MeprRule::get_post_unauth_settings( $post );

    if ( empty( $prepared_atts['memberpress_unauthorized'] ) ) {
      $prepared_atts['memberpress_unauthorized'] = 'default';
    }

    if ( 'custom' === $prepared_atts['memberpress_unauthorized'] && ! empty( $prepared_atts['memberpress_unauthorized_message'] ) ) { // Row setting
      $output = wp_kses_post( $prepared_atts['memberpress_unauthorized_message'] );
    } else if ( ! empty( $post_unauth_settings->unauth_message ) && 'custom' === $post_unauth_settings->unauth_message_type ) { // Post setting
      $output = $post_unauth_settings->unauth_message;
    } else if ( ! empty( $global_message ) ) { // Global setting
      $output = $global_message;
    } else { // Fallback
      $output = __( 'You do not have permission to view this content.', 'memberpress', 'memberpress-wpbakery' );
    }

    if ( 'hide' === $prepared_atts['memberpress_unauthorized'] ) {
      $output = '';
    }

    return $output;
  }
}
