<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

class MpElementor {

  public function __construct() {
    add_action( 'elementor/controls/controls_registered', array( $this, 'tab' ) );
    add_action( 'elementor/element/section/section_advanced/after_section_end', array( $this, 'row_settings' ) );
    add_filter( 'elementor/frontend/section/should_render', array( $this, 'should_render' ), 10, 2 );
    add_action( 'elementor/frontend/section/before_render', array( $this, 'before_render' ) );
    add_action( 'elementor/common/after_register_scripts', array( $this, 'enqueues' ) );
  }

  /**
   * Adds a new tab to the Elementor settings panel.
   *
   * @param  class  $control_manager  Controls_Manager
   *
   * @return void
   */
  public function tab( $control_manager ) {
    $control_manager::add_tab( 'memberpress', 'MemberPress' );
  }

  /**
   * Add MemberPress settings
   *
   * @param object  $element
   *
   * @return array
   */
  public function row_settings( $element ) {

    $unauth_actions = array(
      'default' => __( 'Default', 'memberpress-elementor' ),
      'hide' => __( 'Hide', 'memberpress-elementor' ),
      'custom' => __( 'Display Message', 'memberpress-elementor' )
    );

    $rule_options = array(
      'none' => __( 'None', 'memberpress-elementor' )
    );

    // Assemble MP Rules into an options array
    foreach ( MeprCptModel::all( 'MeprRule' ) as $rule ) {
      $rule_options[$rule->ID] = $rule->post_title;
    }

    $element->start_controls_section( 'section_memberpress', array(
      'label' => __( 'Content Restriction', 'memberpress-elementor' ),
      'tab' => 'memberpress'
    ) );

    $element->add_control( 'memberpress_rule', array(
      'label' => __( 'MemberPress Rule', 'memberpress-elementor' ),
      'description' => __( 'Select a Rule to determine member access.', 'memberpress-elementor' ),
      'type' => \Elementor\Controls_Manager::SELECT,
      'default' => '',
      'options' => $rule_options
    ) );

    $element->add_control( 'memberpress_unauthorized', array(
      'label' => __( 'Unauthorized Content', 'memberpress-elementor' ),
      'description' => __( 'What should be shown to unauthorized users.', 'memberpress-elementor' ),
      'type' => \Elementor\Controls_Manager::SELECT,
      'default' => 'default',
      'options' => $unauth_actions
    ) );

    $element->add_control( 'memberpress_unauthorized_message', array(
      'label' => __( 'Custom Message', 'elementor', 'memberpress-elementor' ),
      'description' => __( 'Custom message for unauthorized users.', 'memberpress-elementor' ),
      'type' => \Elementor\Controls_Manager::WYSIWYG,
      'default' => '',
      'label_black' => true
    ) );

    $element->end_controls_section();
  }

  /**
   * Whether the element should render
   *
   * @param  bool     $should_render    Whether to output the content in the frontend
   * @param  object   $element          Elementor element
   *
   * @return bool
   */
  public function should_render( $should_render, $element ) {
    $settings = $element->get_settings();
    $rule = $settings['memberpress_rule'];
    $mepr_rule = ! empty( $rule ) ? new MeprRule( $rule ) : '';
    return ! empty( $mepr_rule->ID ) && 'none' !== $rule && ! current_user_can( 'mepr-active', "rule: {$rule}" ) ? false : $should_render;
  }

  /**
   * Output unauthorized message before the element
   *
   * @param  object   $element  Elementor element
   *
   * @return void
   */
  public function before_render( $element ) {

    $settings  = $element->get_settings();

    if ( empty( $settings['memberpress_rule'] ) || 'none' === $settings['memberpress_rule'] ) {
      return;
    }

    $rule = new MeprRule( $settings['memberpress_rule'] );

    if ( empty( $rule->ID ) || current_user_can( 'mepr-active', "rule: {$settings['memberpress_rule']}" ) ) {
      return;
    }

    echo $this->unauth_content( $settings );
  }

  /**
   * Unauthorized content
   *
   * @param  array    $settings   Element settings
   *
   * @return string
   */
  public function unauth_content( $settings ) {

    if ( empty( $settings['memberpress_unauthorized'] ) ) {
      $settings['memberpress_unauthorized'] = 'default';
    }

    global $post;
    $mepr_options = MeprOptions::fetch();
    $global_message = MeprRulesCtrl::unauthorized_message( $post );
    $post_unauth_settings = MeprRule::get_post_unauth_settings( $post );

    if ( 'custom' === $settings['memberpress_unauthorized'] && ! empty( $settings['memberpress_unauthorized_message'] ) ) { // Row setting
      $output = wp_kses_post( $settings['memberpress_unauthorized_message'] );
    } else if ( ! empty( $post_unauth_settings->unauth_message ) && 'custom' === $post_unauth_settings->unauth_message_type ) { // Post setting
      $output = $post_unauth_settings->unauth_message;
    } else if ( ! empty( $global_message ) ) { // Global setting
      $output = $global_message;
    } else { // Fallback
      $output = __( 'You do not have permission to view this content.', 'memberpress-elementor' );
    }

    ob_start();
    ?>

    <section class="elementor-section elementor-top-section elementor-element">
      <div class="elementor-container elementor-column-gap-default">
        <div class="elementor-row">
          <div class="elementor-column elementor-col-100 elementor-top-column elementor-element">
            <div class="elementor-column-wrap elementor-element-populated">
              <div class="elementor-widget-wrap">
                <div class="elementor-element elementor-widget elementor-widget-text-editor">
                  <div class="elementor-widget-container">
                    <div class="elementor-text-editor elementor-clearfix">
                      <div class="memberpress-unauthorized">
                          <?php echo $output; ?>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <?php

    $output = ob_get_clean();

    if ( 'hide' === $settings['memberpress_unauthorized'] ) {
      $output = '';
    }

    return $output;
  }

  /**
   * Adds the MemberPress icon to the MemberPress tab in Elementor
   *
   * @return void
   */
  public function enqueues() {

    $editor = Elementor\Plugin::$instance->editor;

    if ( $editor->is_edit_mode() ) {

      // Load MemberPress styles
      MeprAppCtrl::load_admin_scripts( null );

      // Custom styles for Elementor
      ?>
      <style>
        .elementor-panel .elementor-tab-control-memberpress {
          vertical-align: middle;
        }
        .elementor-panel .elementor-tab-control-memberpress a:before {
          font-family: memberpress !important;
          content: "\e839";
        }
      </style>
      <?php
    }
  }
}
