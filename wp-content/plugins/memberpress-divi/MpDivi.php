<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

class MpDivi {

  public function __construct() {
    // Don't run in the Divi Role Editor
    if ( empty( $_GET['page'] ) || 'et_divi_role_editor' !== $_GET['page'] ) {
      add_filter( 'et_builder_main_tabs', array( $this, 'tab' ) );
      add_filter( 'et_builder_get_parent_modules', array( $this, 'toggle' ) );
      add_filter( 'et_pb_module_content', array( $this, 'shortcode' ), 10, 4 );
      add_filter( 'et_pb_all_fields_unprocessed_et_pb_row', array( $this, 'row_settings' ) );
    }
  }

  /**
   * Adds the MemberPress tab to Divi Builder
   *
   * @param  array  $tabs   Tabs
   *
   * @return array
   */
  public function tab( $tabs ) {
    $tabs['memberpress'] = 'MemberPress';
    return $tabs;
  }

  /**
   * Adds toggles to the MemberPress tab
   *
   * @param  array  $modules  Modules (e.g. rows)
   *
   * @return array
   */
  public function toggle( $modules ) {

    // Add toggle to Rows
    if ( ! empty( $modules ) && is_object( $modules['et_pb_row'] ) ) {
      $modules['et_pb_row']->settings_modal_toggles['memberpress'] = array(
        'toggles' => array(
          'mp_protect_content' => array(
            'title' => __( 'Protect Content', 'memberpress-divi' ),
            'priority' => 100
          )
        )
      );
    }

    return $modules;
  }

  /**
   * Modify the content of the module's shortcode
   *
   * @param  string   $output   Shortcode content
   * @param  array    $props    Module props
   * @param  array    $attrs    Shortcode attributes
   * @param  string   $slug     Shortcode slug/ID
   *
   * @return string
   */
  public function shortcode( $output, $props, $attrs, $slug ) {

    if ( et_fb_is_enabled() ) {
      return $output;
    }

    if ( 'et_pb_row' !== $slug ) {
      return $output;
    }

    $rule = isset( $props['memberpress_rule'] ) ? $props['memberpress_rule'] : false;

    // Not set to protect
    if ( false === $rule || 'none' === $rule ) {
      return $output;
    }

    if ( current_user_can( 'mepr-active', "rule: {$rule}" ) ) { // Has access
      return $output;
    }

    $output = $this->unauth_content( $props );

    // Blank if set to just hide
    if ( 'hide' === $props['memberpress_unauthorized'] ) {
      $output = '';
    }

    return $output;
  }

  /**
   * Add settings to Rows in Divi
   *
   * @param  array  $settings   Row settings
   *
   * @return array
   */
  public function row_settings( $settings ) {

    $unauth_actions = array(
      'default' => __( 'Default', 'memberpress-divi' ),
      'hide' => __( 'Hide', 'memberpress-divi' ),
      'custom' => __( 'Display Message', 'memberpress-divi' )
    );

    $rule_options = array(
      'none' => __( 'None', 'memberpress-divi' )
    );

    // Assemble MP Rules into an options array
    foreach ( MeprCptModel::all( 'MeprRule' ) as $rule ) {
      $rule_options[$rule->ID] = $rule->post_title;
    }

    $settings['memberpress_rule'] = array(
      'tab_slug' => 'memberpress',
      'label' => __( 'MemberPress Rule', 'memberpress-divi' ),
      'description' => __( 'Select a Rule to determine member access.', 'memberpress-divi' ),
      'type' => 'select',
      'default' => 'none',
      'option_category' => 'configuration',
      'options' => $rule_options,
      'toggle_slug' => 'mp_protect_content',
    );

    $settings['memberpress_unauthorized'] = array(
      'tab_slug' => 'memberpress',
      'label' => __( 'Unauthorized Content', 'memberpress-divi' ),
      'description' => __( 'What should be shown to unauthorized users.', 'memberpress-divi' ),
      'type' => 'select',
      'default' => 'default',
      'option_category' => 'configuration',
      'options' => $unauth_actions,
      'toggle_slug' => 'mp_protect_content',
    );

    $settings['memberpress_unauthorized_message'] = array(
      'tab_slug' => 'memberpress',
      'label' => esc_html__( 'Custom Message', 'memberpress-divi' ),
      'type' => 'tiny_mce',
      'option_category' => 'basic_option',
      'description' => esc_html__( 'Custom message for unauthorized users.', 'memberpress-divi' ),
      'dynamic_content' => 'text',
      'hover' => 'tabs',
      'toggle_slug' => 'mp_protect_content',
      'show_if' => array(
        'memberpress_unauthorized' => 'custom',
      ),
    );

    return $settings;
  }

  /**
   * Unauthorized content
   *
   * @param  object   $props  Settings/props from a Divi module
   *
   * @return string
   */
  public function unauth_content( $props ) {

    if ( empty( $props['memberpress_unauthorized'] ) ) {
      $props['memberpress_unauthorized'] = 'default';
    }

    global $post;
    $mepr_options = MeprOptions::fetch();
    $global_message = MeprRulesCtrl::unauthorized_message( $post );
    $post_unauth_settings = MeprRule::get_post_unauth_settings( $post );

    if ( 'custom' === $props['memberpress_unauthorized'] && ! empty( $props['memberpress_unauthorized_message'] ) ) { // Row setting
      $output = wp_kses_post( $props['memberpress_unauthorized_message'] );
    } else if ( ! empty( $post_unauth_settings->unauth_message ) && 'custom' === $post_unauth_settings->unauth_message_type ) { // Post setting
      $output = $post_unauth_settings->unauth_message;
    } else if ( ! empty( $global_message ) ) { // Global setting
      $output = $global_message;
    } else { // Fallback
      $output = __( 'You do not have permission to view this content.', 'memberpress-divi' );
    }

    ob_start();

    echo '<div class="memberpress-unauthorized">';

    echo $output;

    echo '</div>';

    $output = ob_get_clean();

    if ( 'hide' === $props['memberpress_unauthorized'] ) {
      $output = '';
    }

    return $output;
  }

}
