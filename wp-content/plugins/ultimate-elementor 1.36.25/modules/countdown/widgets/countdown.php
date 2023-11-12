<?php
/**
 * UAEL Countdown Timer.
 *
 * @package UAEL
 */

namespace UltimateElementor\Modules\Countdown\Widgets;

// Elementor Classes.
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Utils;

// UltimateElementor Classes.
use UltimateElementor\Base\Common_Widget;

if ( ! defined( 'ABSPATH' ) ) {
	exit;   // Exit if accessed directly.
}

/**
 * Class Countdown.
 */
class Countdown extends Common_Widget {

	/**
	 * Retrieve Countdown Widget name.
	 *
	 * @since 1.14.0
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return parent::get_widget_slug( 'Countdown' );
	}

	/**
	 * Retrieve Countdown Widget heading.
	 *
	 * @since 1.14.0
	 * @access public
	 *
	 * @return string Widget heading.
	 */
	public function get_title() {
		return parent::get_widget_title( 'Countdown' );
	}

	/**
	 * Retrieve Countdown Widget icon.
	 *
	 * @since 1.14.0
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return parent::get_widget_icon( 'Countdown' );
	}

	/**
	 * Retrieve Widget Keywords.
	 *
	 * @since 1.14.0
	 * @access public
	 *
	 * @return string Widget keywords.
	 */
	public function get_keywords() {
		return parent::get_widget_keywords( 'Countdown' );
	}

	/**
	 * Retrieve the list of styles needed for Hotspot.
	 *
	 * Used to set styles dependencies required to run the widget.
	 *
	 * @since 1.14.0
	 * @access public
	 *
	 * @return array Widget styles dependencies.
	 */
	public function get_style_depends() {
		return array( 'uael-countdown' );
	}

	/**
	 * Retrieve the list of scripts the Hotspot widget depended on.
	 *
	 * Used to set scripts dependencies required to run the widget.
	 *
	 * @since 1.14.0
	 * @access public
	 *
	 * @return array Widget scripts dependencies.
	 */
	public function get_script_depends() {
		return array( 'uael-cookie-lib', 'uael-countdown' );
	}

	/**
	 * Register Countdown controls.
	 *
	 * @since 1.29.2
	 * @access protected
	 */
	protected function register_controls() {

		$this->register_presets_control( 'Countdown', $this );

		// Content Tab.
		$this->register_countdown_general_controls();
		$this->register_after_countdown_expire_controls();
		$this->register_countdown_label_controls();
		$this->register_countdown_style_controls();
		$this->register_helpful_information();

		$this->register_style_controls();
		$this->register_digits_style_controls();
		$this->register_label_style_controls();
		$this->register_message_style_controls();
	}


	/**
	 * Register Countdown General Controls.
	 *
	 * @since 1.14.0
	 * @access protected
	 */
	protected function register_countdown_general_controls() {

		$this->start_controls_section(
			'countdown_content',
			array(
				'label' => __( 'General', 'uael' ),
			)
		);

		$this->add_control(
			'countdown_type',
			array(
				'label'   => __( 'Type', 'uael' ),
				'type'    => Controls_Manager::SELECT,
				'options' => array(
					'fixed'     => __( 'Fixed Timer', 'uael' ),
					'evergreen' => __( 'Evergreen Timer', 'uael' ),
					'recurring' => __( 'Recurring Timer', 'uael' ),
				),
				'default' => 'fixed',
			)
		);

		if ( parent::is_internal_links() ) {
			$this->add_control(
				'doc_fixed_timer',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					/* translators: %s admin link */
					'raw'             => sprintf( __( 'Click <a href="%s" target="_blank" rel="noopener">here</a> to know more about the <span style="text-decoration:underline;font-weight:900;">Fixed Timer</span>.', 'uael' ), UAEL_DOMAIN . 'docs/types-of-timers-in-countdown-timer-widget/?utm_source=uael-pro-dashboard&utm_medium=uael-editor-screen&utm_campaign=uael-pro-plugin#fixed-timer' ),
					'content_classes' => 'uael-editor-doc',
					'condition'       => array(
						'countdown_type' => array( 'fixed' ),
					),
				)
			);
		}

		$this->add_control(
			'due_date',
			array(
				'label'       => __( 'Due Date and Time', 'uael' ),
				'type'        => Controls_Manager::DATE_TIME,
				'default'     => gmdate( 'Y-m-d H:i', strtotime( '+1 month' ) + ( get_option( 'gmt_offset' ) ) ),
				/* translators: %s: Time zone. */
				'description' => sprintf( __( 'Date set according to your timezone: %s.', 'uael' ), Utils::get_timezone_string() ),
				'condition'   => array(
					'countdown_type' => array( 'fixed' ),
				),
				'label_block' => false,
			)
		);

		if ( parent::is_internal_links() ) {
			$this->add_control(
				'doc_evergreen_timer',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					/* translators: %s admin link */
					'raw'             => sprintf( __( 'Click <a href="%s" target="_blank" rel="noopener">here</a> to know more about the <span style="text-decoration:underline;font-weight:900;">Evergreen Timer</span>.', 'uael' ), UAEL_DOMAIN . 'docs/types-of-timers-in-countdown-timer-widget/?utm_source=uael-pro-dashboard&utm_medium=uael-editor-screen&utm_campaign=uael-pro-plugin#evergreen-timer' ),
					'content_classes' => 'uael-editor-doc',
					'condition'       => array(
						'countdown_type' => array( 'evergreen' ),
					),
				)
			);
		}

		if ( parent::is_internal_links() ) {
			$this->add_control(
				'doc_recur_timer',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					/* translators: %s admin link */
					'raw'             => sprintf( __( 'Click <a href="%s" target="_blank" rel="noopener">here</a> to know more about <span style="text-decoration:underline;font-weight:900;">Recurring Timer</span>.', 'uael' ), UAEL_DOMAIN . 'docs/faqs-for-countdown-timer-widget/?utm_source=uael-pro-dashboard&utm_medium=uael-editor-screen&utm_campaign=uael-pro-plugin#what-is-the-difference-between-evergreen-and-recurring-timer-and-how-does-the-recurring-timer-works-' ),
					'content_classes' => 'uael-editor-doc',
					'condition'       => array(
						'countdown_type' => 'recurring',
					),
				)
			);
		}

		if ( parent::is_internal_links() ) {
			$this->add_control(
				'timezone_doc',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					/* translators: %1$s Doc Link */
					'raw'             => sprintf( __( 'Which <a href="%s" target="_blank" rel="noopener">timezone</a> does the timer use?', 'uael' ), UAEL_DOMAIN . 'docs/faqs-for-countdown-timer-widget/?utm_source=uael-pro-dashboard&utm_medium=uael-editor-screen&utm_campaign=uael-pro-plugin#which-timezone-does-the-timer-use-' ),
					'content_classes' => 'uael-editor-doc',
					'condition'       => array(
						'countdown_type' => 'fixed',
					),
				)
			);
		}

		$this->add_control(
			'start_date',
			array(
				'label'       => __( 'Start Date and Time', 'uael' ),
				'description' => __( 'Select the date & time when you want to make your countdown timer go live on your site.', 'uael' ),
				'type'        => Controls_Manager::DATE_TIME,
				'label_block' => false,
				'default'     => gmdate( 'Y-m-d H:i' ),
				'condition'   => array(
					'countdown_type' => 'recurring',
				),
			)
		);

		$this->add_control(
			'evg_days',
			array(
				'label'     => __( 'Days', 'uael' ),
				'type'      => Controls_Manager::NUMBER,
				'dynamic'   => array(
					'active' => true,
				),
				'min'       => '0',
				'default'   => '1',
				'condition' => array(
					'countdown_type' => array( 'evergreen', 'recurring' ),
				),

			)
		);

		$this->add_control(
			'evg_hours',
			array(
				'label'     => __( 'Hours', 'uael' ),
				'type'      => Controls_Manager::NUMBER,
				'dynamic'   => array(
					'active' => true,
				),
				'min'       => '0',
				'max'       => '23',
				'default'   => '5',
				'condition' => array(
					'countdown_type' => array( 'evergreen', 'recurring' ),
				),
			)
		);

		$this->add_control(
			'evg_minutes',
			array(
				'label'       => __( 'Minutes', 'uael' ),
				'description' => __( 'Set the above Days, Hours, Minutes fields for the amount of time you want the timer to display.', 'uael' ),
				'type'        => Controls_Manager::NUMBER,
				'dynamic'     => array(
					'active' => true,
				),
				'min'         => '0',
				'max'         => '59',
				'default'     => '30',
				'condition'   => array(
					'countdown_type' => array( 'evergreen', 'recurring' ),
				),
			)
		);

		$this->add_control(
			'reset_days',
			array(
				'label'       => __( 'Repeat Timer after ( Days )', 'uael' ),
				'description' => __( 'Note: This option will repeat the timer after sepcified number of days.', 'uael' ),
				'type'        => Controls_Manager::NUMBER,
				'dynamic'     => array(
					'active' => true,
				),
				'min'         => '1',
				'default'     => '7',
				'condition'   => array(
					'countdown_type' => 'recurring',
				),
			)
		);

		$this->add_control(
			'display_days',
			array(
				'label'        => __( 'Display Days', 'uael' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'uael' ),
				'label_off'    => __( 'Hide', 'uael' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'display_hours',
			array(
				'label'        => __( 'Display Hours', 'uael' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'uael' ),
				'label_off'    => __( 'Hide', 'uael' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'display_minutes',
			array(
				'label'        => __( 'Display Minutes', 'uael' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'uael' ),
				'label_off'    => __( 'Hide', 'uael' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'display_seconds',
			array(
				'label'        => __( 'Display Seconds', 'uael' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Show', 'uael' ),
				'label_off'    => __( 'Hide', 'uael' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'prefix_class' => 'uael-countdown-show-seconds-',
				'render_type'  => 'template',
			)
		);

		$this->add_control(
			'reset_evergreen',
			array(
				'label'        => __( 'Reset Timer', 'uael' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'uael' ),
				'label_off'    => __( 'No', 'uael' ),
				'return_value' => 'yes',
				'default'      => 'no',
				'condition'    => array(
					'countdown_type' => 'evergreen',
				),
			)
		);

		$this->end_controls_section();

	}

	/**
	 * Register After Expire Controls.
	 *
	 * @since 1.14.0
	 * @access protected
	 */
	protected function register_after_countdown_expire_controls() {

		$this->start_controls_section(
			'countdown_expire_actions',
			array(
				'label'      => __( 'Action After Expiry', 'uael' ),
				'conditions' => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'relation' => 'and',
							'terms'    => array(
								array(
									'name'     => 'countdown_type',
									'operator' => '==',
									'value'    => 'evergreen',
								),
								array(
									'name'     => 'reset_evergreen',
									'operator' => '!==',
									'value'    => 'yes',
								),
							),
						),
						array(
							'name'     => 'countdown_type',
							'operator' => '!==',
							'value'    => 'evergreen',
						),
					),
				),
			)
		);

		$this->add_control(
			'expire_actions',
			array(
				'label'       => __( 'Select Action', 'uael' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'hide'         => __( 'Hide', 'uael' ),
					'redirect'     => __( 'Redirect', 'uael' ),
					'show_message' => __( 'Show Message', 'uael' ),
					'none'         => __( 'None', 'uael' ),
				),
				'label_block' => false,
				'default'     => 'hide',
			)
		);

		$this->add_control(
			'message_after_expire',
			array(
				'label'       => __( 'Message', 'uael' ),
				'type'        => Controls_Manager::TEXTAREA,
				'label_block' => true,
				'condition'   => array(
					'expire_actions' => 'show_message',
				),
				'default'     => __( 'Sale has ended!!', 'uael' ),
				'dynamic'     => array(
					'active' => true,
				),
			)
		);

		$this->add_control(
			'expire_redirect_url',
			array(
				'label'         => __( 'Redirect URL', 'uael' ),
				'type'          => Controls_Manager::URL,
				'label_block'   => true,
				'show_external' => false,
				'default'       => array(
					'url' => '#',
				),
				'condition'     => array(
					'expire_actions' => 'redirect',
				),
				'dynamic'       => array(
					'active' => true,
				),
			)
		);

		$this->add_control(
			'preview_expire_message',
			array(
				'label'        => __( 'Preview after expire message', 'uael' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'uael' ),
				'label_off'    => __( 'No', 'uael' ),
				'return_value' => 'yes',
				'default'      => 'no',
				'condition'    => array(
					'expire_actions' => 'show_message',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register labels Controls.
	 *
	 * @since 1.14.0
	 * @access protected
	 */
	protected function register_countdown_label_controls() {

		$this->start_controls_section(
			'countdown_labels',
			array(
				'label' => __( 'Labels', 'uael' ),
			)
		);

		$this->add_control(
			'display_timer_labels',
			array(
				'label'   => __( 'Display Labels', 'uael' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'default',
				'options' => array(
					'default' => __( 'Default', 'uael' ),
					'custom'  => __( 'Custom', 'uael' ),
					'none'    => __( 'None', 'uael' ),
				),
			)
		);

		$this->add_control(
			'custom_days',
			array(
				'label'       => __( 'Label for Days', 'uael' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => false,
				'default'     => __( 'Days', 'uael' ),
				'condition'   => array(
					'display_timer_labels' => 'custom',
				),
				'dynamic'     => array(
					'active' => true,
				),
			)
		);

		$this->add_control(
			'custom_hours',
			array(
				'label'       => __( 'Label for Hours', 'uael' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => false,
				'default'     => __( 'Hours', 'uael' ),
				'condition'   => array(
					'display_timer_labels' => 'custom',
				),
				'dynamic'     => array(
					'active' => true,
				),
			)
		);

		$this->add_control(
			'custom_minutes',
			array(
				'label'       => __( 'Label for Minutes', 'uael' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => false,
				'default'     => __( 'Minutes', 'uael' ),
				'condition'   => array(
					'display_timer_labels' => 'custom',
				),
				'dynamic'     => array(
					'active' => true,
				),
			)
		);

		$this->add_control(
			'custom_seconds',
			array(
				'label'       => __( 'Label for Seconds', 'uael' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => false,
				'default'     => __( 'Seconds', 'uael' ),
				'condition'   => array(
					'display_timer_labels' => 'custom',
				),
				'dynamic'     => array(
					'active' => true,
				),
			)
		);

		$this->add_control(
			'countdown_label_pos',
			array(
				'label'          => __( 'Label Position', 'uael' ),
				'type'           => Controls_Manager::CHOOSE,
				'label_block'    => false,
				'options'        => array(
					'top'    => array(
						'title' => __( 'Top', 'uael' ),
						'icon'  => 'eicon-v-align-top',
					),
					'inline' => array(
						'title' => __( 'Right', 'uael' ),
						'icon'  => 'eicon-h-align-right',
					),
					'block'  => array(
						'title' => __( 'Bottom', 'uael' ),
						'icon'  => 'eicon-v-align-bottom',
					),
				),
				'toggle'         => false,
				'default'        => 'block',
				'prefix_class'   => 'uael-countdown-label-',
				'style_transfer' => true,
				'condition'      => array(
					'countdown_style!' => 'circle',
				),
			)
		);

		$this->add_control(
			'label_space',
			array(
				'label'          => __( 'Label Spacing', 'uael' ),
				'type'           => Controls_Manager::POPOVER_TOGGLE,
				'condition'      => array(
					'countdown_label_pos' => 'inline',
				),
				'style_transfer' => true,
			)
		);
		$this->start_popover();
		$this->add_control(
			'label_space_top',
			array(
				'label'          => __( 'Top', 'uael' ),
				'type'           => Controls_Manager::SLIDER,
				'size_units'     => array( 'px' ),
				'range'          => array(
					'px' => array(
						'min' => -100,
						'max' => 100,
					),
				),
				'default'        => array(
					'size' => 5,
					'unit' => 'px',
				),
				'selectors'      => array(
					'{{WRAPPER}}.uael-countdown-label-inline .uael-item-label' => 'top: {{SIZE || 0}}{{UNIT}};',
				),
				'condition'      => array(
					'countdown_label_pos' => 'inline',
				),
				'style_transfer' => true,
			)
		);

		$this->add_control(
			'label_space_left',
			array(
				'label'          => __( 'Left', 'uael' ),
				'type'           => Controls_Manager::SLIDER,
				'size_units'     => array( 'px' ),
				'range'          => array(
					'px' => array(
						'min' => -100,
						'max' => 100,
					),
				),
				'default'        => array(
					'size' => 10,
					'unit' => 'px',
				),
				'selectors'      => array(
					'{{WRAPPER}}.uael-countdown-label-inline .uael-item-label' => 'left: {{SIZE || 0}}{{UNIT}};',
				),
				'condition'      => array(
					'countdown_label_pos' => 'inline',
				),
				'style_transfer' => true,
			)
		);
		$this->end_popover(); // End Prover.
		$this->end_controls_section();
	}

	/**
	 * Register Countdown General Controls.
	 *
	 * @since 1.14.0
	 * @access protected
	 */
	protected function register_countdown_style_controls() {

		$this->start_controls_section(
			'style',
			array(
				'label' => __( 'Layout', 'uael' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'countdown_style',
			array(
				'label'        => __( 'Select Style', 'uael' ),
				'type'         => Controls_Manager::SELECT,
				'options'      => array(
					'square'  => 'Square',
					'circle'  => 'Circle',
					'rounded' => 'Rounded',
					'none'    => 'None',
				),
				'default'      => 'square',
				'prefix_class' => 'uael-countdown-shape-',
			)
		);

		$this->add_control(
			'rounded_border_radius',
			array(
				'label'      => __( 'Border Radius', 'uael' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'condition'  => array(
					'countdown_style' => 'rounded',
				),
				'selectors'  => array(
					'{{WRAPPER}} .uael-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'default'    => array(
					'top'    => '10',
					'bottom' => '10',
					'left'   => '10',
					'right'  => '10',
					'unit'   => 'px',
				),
			)
		);

		$this->add_control(
			'countdown_separator',
			array(
				'label'        => __( 'Separator', 'uael' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Enable', 'uael' ),
				'label_off'    => __( 'Disable', 'uael' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'prefix_class' => 'uael-countdown-separator-wrapper-',
				'render_type'  => 'template',
			)
		);

		$this->add_control(
			'countdown_separator_color',
			array(
				'label'     => __( 'Separator Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .uael-countdown-wrapper .uael-countdown-separator' => 'color:{{VALUE}};',
				),
				'condition' => array(
					'countdown_separator' => 'yes',
				),
			)
		);

		$this->add_control(
			'animation',
			array(
				'label'        => __( 'Flash Animation', 'uael' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Enable', 'uael' ),
				'label_off'    => __( 'Disable', 'uael' ),
				'return_value' => 'yes',
				'prefix_class' => 'uael-countdown-anim-',
			)
		);

		$this->add_control(
			'start_animation',
			array(
				'label'     => __( 'Start Animation Before (Minutes)', 'uael' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 2,
				'condition' => array(
					'animation' => 'yes',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register Countdown General Controls.
	 *
	 * @since 1.14.0
	 * @access protected
	 */
	protected function register_style_controls() {

		$this->start_controls_section(
			'countdown_timer_style',
			array(
				'label' => __( 'Countdown Items', 'uael' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'size',
			array(
				'label'      => __( 'Container Width', 'uael' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => array(
					'px' => array(
						'min' => 20,
						'max' => 240,
					),
				),
				'default'    => array(
					'size' => '90',
					'unit' => 'px',
				),
				'size_units' => array( 'px' ),
				'selectors'  => array(
					'{{WRAPPER}}.uael-countdown-label-block .uael-countdown-items-wrapper,{{WRAPPER}}.uael-countdown-label-top .uael-countdown-items-wrapper' => 'max-width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .uael-countdown-show-message .uael-countdown-items-wrapper' => 'max-width:100%;',
					'{{WRAPPER}} .uael-preview-message .uael-countdown-items-wrapper' => 'max-width:100%;',
					'{{WRAPPER}}.uael-countdown-label-block .uael-countdown-item, {{WRAPPER}}.uael-countdown-label-top .uael-countdown-item' => 'width:{{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .uael-item-label' => 'width:{{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .uael-item'       => 'height:{{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.uael-countdown-shape-none .uael-item' => 'height:calc({{SIZE}}{{UNIT}}*1.3);',
					'{{WRAPPER}}.uael-countdown-shape-none .uael-countdown-item' => 'width:{{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.uael-countdown-shape-none .uael-item-label' => 'width:{{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.uael-countdown-border-none .uael-item' => 'height:calc({{SIZE}}{{UNIT}}*1.3);',
					'{{WRAPPER}}.uael-countdown-border-none .uael-countdown-item' => 'width:{{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.uael-countdown-border-none .uael-item-label' => 'width:{{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'countdown_style!' => array( 'circle' ),
				),
			)
		);

		$this->add_responsive_control(
			'bg_size',
			array(
				'label'      => __( 'Container Width ( PX )', 'uael' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 50,
						'max' => 180,
					),
				),
				'default'    => array(
					'size' => '85',
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .uael-item'           => 'padding: calc({{SIZE}}{{UNIT}}/4); height:{{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.uael-countdown-border-none .uael-item' => 'padding: calc({{SIZE}}{{UNIT}}/4); height:calc({{SIZE}}{{UNIT}}*1.5);',
					'{{WRAPPER}} .uael-countdown-items-wrapper' => 'max-width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.uael-countdown-border-none .uael-countdown-item' => 'width:{{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.uael-countdown-border-none .uael-item-label' => 'width:{{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .uael-countdown-item' => 'width:{{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .uael-item-label'     => 'width:{{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .uael-countdown-show-message .uael-countdown-items-wrapper' => 'max-width:100%;',
					'{{WRAPPER}} .uael-preview-message .uael-countdown-items-wrapper' => 'max-width:100%;',
				),
				'condition'  => array(
					'countdown_style' => 'circle',
				),
			)
		);

		$this->add_responsive_control(
			'distance_betn_countdown_items',
			array(
				'label'      => __( 'Spacing Between Items', 'uael' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'default'    => array(
					'size' => 40,
					'unit' => 'px',
				),
				'range'      => array(
					'px' => array(
						'max' => 230,
						'min' => 0,
					),
					'%'  => array(
						'max' => 300,
						'min' => 0,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .uael-item:not(:first-child)' => 'margin-left: calc( {{SIZE}}{{UNIT}} / 2 );',
					'{{WRAPPER}} .uael-item:not(:last-child)' => 'margin-right: calc( {{SIZE}}{{UNIT}} / 2 );',
					'{{WRAPPER}} .uael-item:last-of-type' => 'margin-right: 0px;',
					'(mobile){{WRAPPER}}.uael-countdown-responsive-yes .uael-item:not(:first-child)' => 'margin-left: 0;margin-top: calc( {{SIZE}}{{UNIT}} / 2 );',
					'(mobile){{WRAPPER}}.uael-countdown-responsive-yes .uael-item:not(:last-child)' => 'margin-right: 0;margin-bottom: calc( {{SIZE}}{{UNIT}} / 2 );',
				),
			)
		);

		$this->add_responsive_control(
			'distance_betn_items_and_labels',
			array(
				'label'      => __( 'Spacing Between Digits and Labels', 'uael' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'default'    => array(
					'size' => 15,
					'unit' => 'px',
				),
				'range'      => array(
					'px' => array(
						'max' => 110,
						'min' => 0,
					),
					'%'  => array(
						'max' => 50,
						'min' => 0,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}}.uael-countdown-label-block .uael-countdown-wrapper .uael-countdown-item' => 'margin-bottom:{{SIZE}}{{UNIT}}; margin-right:0px; margin-top: 0px;',
					'{{WRAPPER}}.uael-countdown-shape-circle .uael-countdown-wrapper .uael-countdown-item' => 'margin-bottom:{{SIZE}}{{UNIT}}; margin-right:0px; margin-top: 0px;',
					'{{WRAPPER}}.uael-countdown-label-inline .uael-countdown-wrapper .uael-countdown-item' => 'margin-right:{{SIZE}}{{UNIT}}; margin-bottom:0px; margin-top: 0px;',
					'{{WRAPPER}}.uael-countdown-label-top .uael-countdown-wrapper .uael-countdown-item' => 'margin-top:{{SIZE}}{{UNIT}}; margin-bottom:0px; margin-right:0px;',
				),
				'condition'  => array(
					'display_timer_labels' => array( 'default', 'custom' ),
				),
			)
		);

		$this->add_control(
			'items_background_color',
			array(
				'label'     => __( 'Background Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'default'   => '#f5f5f5',
				'selectors' => array(
					'{{WRAPPER}} .uael-item' => 'background-color: {{VALUE}};',
					'{{WRAPPER}}.uael-countdown-shape-none .uael-item' => 'background-color:unset',
				),
				'condition' => array(
					'countdown_style!' => 'none',
				),
			)
		);

		$this->add_control(
			'items_border_style',
			array(
				'label'        => __( 'Border Style', 'uael' ),
				'type'         => Controls_Manager::SELECT,
				'default'      => 'solid',
				'options'      => array(
					'none'   => __( 'None', 'uael' ),
					'solid'  => __( 'Solid', 'uael' ),
					'double' => __( 'Double', 'uael' ),
					'dotted' => __( 'Dotted', 'uael' ),
					'dashed' => __( 'Dashed', 'uael' ),
				),
				'selectors'    => array(
					'{{WRAPPER}} .uael-item' => 'border-style: {{VALUE}};',
				),
				'prefix_class' => 'uael-countdown-border-',
				'condition'    => array(
					'countdown_style!' => 'none',
				),
			)
		);

		$this->add_control(
			'items_border_color',
			array(
				'label'     => __( 'Border Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'default'   => '#eaeaea',
				'selectors' => array(
					'{{WRAPPER}} .uael-item' => 'border-color: {{VALUE}};',
				),
				'condition' => array(
					'items_border_style!' => 'none',
					'countdown_style!'    => 'none',
				),
			)
		);

		$this->add_control(
			'items_border_size',
			array(
				'label'      => __( 'Border Width', 'uael' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'default'    => array(
					'top'    => '1',
					'bottom' => '1',
					'left'   => '1',
					'right'  => '1',
					'unit'   => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .uael-item' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; box-sizing:content-box;',
				),
				'condition'  => array(
					'items_border_style!' => 'none',
					'countdown_style!'    => 'none',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'box_box_shadow',
				'label'    => __( 'Box Shadow', 'uael' ),
				'selector' => '{{WRAPPER}} .uael-item',
			)
		);

		$this->add_responsive_control(
			'box_padding',
			array(
				'label'      => __( 'Padding', 'uael' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .uael-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'responsive_support',
			array(
				'label'        => __( 'Responsive Support', 'uael' ),
				'description'  => __( 'Enable this option to stack the Countdown items on mobile.', 'uael' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'On', 'uael' ),
				'label_off'    => __( 'Off', 'uael' ),
				'return_value' => 'yes',
				'default'      => 'no',
				'prefix_class' => 'uael-countdown-responsive-',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register Digit Style Controls.
	 *
	 * @since 1.14.0
	 * @access protected
	 */
	protected function register_digits_style_controls() {

		$this->start_controls_section(
			'countdown_digits_style',
			array(
				'label' => __( 'Digits', 'uael' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'digits_typography',
				'selector' => '{{WRAPPER}} .uael-countdown-wrapper .uael-countdown-item,{{WRAPPER}} .uael-countdown-wrapper .uael-countdown-separator',
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				),
			)
		);

		$this->add_control(
			'items_color',
			array(
				'label'     => __( 'Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_TEXT,
				),
				'default'   => '#050054',
				'selectors' => array(
					'{{WRAPPER}} .uael-countdown-item,{{WRAPPER}} .uael-countdown-separator' => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

	}

	/**
	 * Register Digit Style Controls.
	 *
	 * @since 1.14.0
	 * @access protected
	 */
	protected function register_label_style_controls() {

		$this->start_controls_section(
			'countdown_labels_style',
			array(
				'label'     => __( 'Labels', 'uael' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'display_timer_labels!' => 'none',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'labels_typography',
				'selector' => '{{WRAPPER}} .uael-item-label',
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				),
			)
		);

		$this->add_control(
			'labels_color',
			array(
				'label'     => __( 'Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_TEXT,
				),
				'default'   => '#3d424d;',
				'selectors' => array(
					'{{WRAPPER}} .uael-item-label' => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

	}

	/**
	 * Register Message Style Controls.
	 *
	 * @since 1.14.0
	 * @access protected
	 */
	protected function register_message_style_controls() {

		$this->start_controls_section(
			'countdown_message_style',
			array(
				'label'     => __( 'Expire Message', 'uael' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'expire_actions' => 'show_message',
				),
			)
		);

		$this->add_responsive_control(
			'message_align',
			array(
				'label'     => __( 'Alignment', 'uael' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'left'   => array(
						'title' => __( 'Left', 'uael' ),
						'icon'  => 'fa fa-align-left',
					),
					'center' => array(
						'title' => __( 'Center', 'uael' ),
						'icon'  => 'fa fa-align-center',
					),
					'right'  => array(
						'title' => __( 'Right', 'uael' ),
						'icon'  => 'fa fa-align-right',
					),
				),
				'default'   => 'center',
				'selectors' => array(
					'{{WRAPPER}} .uael-expire-message-wrapper' => 'text-align:{{VALUE}};',
				),
				'condition' => array(
					'expire_actions' => 'show_message',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'message_typography',
				'selector'  => '{{WRAPPER}} .uael-expire-show-message',
				'global'    => array(
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				),
				'condition' => array(
					'expire_actions' => 'show_message',
				),
			)
		);

		$this->add_control(
			'message_color',
			array(
				'label'     => __( 'Color', 'uael' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_TEXT,
				),
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .uael-expire-show-message' => 'color: {{VALUE}};',
				),
				'condition' => array(
					'expire_actions' => 'show_message',
				),
			)
		);

		$this->end_controls_section();

	}

	/**
	 * Display countdown labels
	 *
	 * @param array $settings specifies string for the time fraction.
	 * @since 1.14.0
	 * @access protected
	 */
	protected function total_interval( $settings ) {
		$total_time  = 0;
		$cnt_days    = empty( $settings['evg_days'] ) ? 0 : ( $settings['evg_days'] * 24 * 60 * 60 * 1000 );
		$cnt_hours   = empty( $settings['evg_hours'] ) ? 0 : ( $settings['evg_hours'] * 60 * 60 * 1000 );
		$cnt_minutes = empty( $settings['evg_minutes'] ) ? 0 : ( $settings['evg_minutes'] * 60 * 1000 );
		$total_time  = $cnt_days + $cnt_hours + $cnt_minutes;
		return $total_time;
	}

	/**
	 * Helpful Information.
	 *
	 * @since 1.14.0
	 * @access protected
	 */
	protected function register_helpful_information() {

		if ( parent::is_internal_links() ) {
			$this->start_controls_section(
				'section_helpful_info',
				array(
					'label' => __( 'Helpful Information', 'uael' ),
				)
			);

				$this->add_control(
					'help_doc_1',
					array(
						'type'            => Controls_Manager::RAW_HTML,
						/* translators: %1$s Doc Link */
						'raw'             => sprintf( __( '%1$s Getting started article » %2$s', 'uael' ), '<a href=' . UAEL_DOMAIN . 'docs/introducing-countdown-timer-widget/?utm_source=uael-pro-dashboard&utm_medium=uael-editor-screen&utm_campaign=uael-pro-plugin" target="_blank" rel="noopener">', '</a>' ),
						'content_classes' => 'uael-editor-doc',
					)
				);

				$this->add_control(
					'help_doc_2',
					array(
						'type'            => Controls_Manager::RAW_HTML,
						/* translators: %1$s Doc Link */
						'raw'             => sprintf( __( '%1$s Know more about the types of timers » %2$s', 'uael' ), '<a href=' . UAEL_DOMAIN . 'docs/types-of-timers-in-countdown-timer-widget/?utm_source=uael-pro-dashboard&utm_medium=uael-editor-screen&utm_campaign=uael-pro-plugin" target="_blank" rel="noopener">', '</a>' ),
						'content_classes' => 'uael-editor-doc',
					)
				);

				$this->add_control(
					'help_doc_3',
					array(
						'type'            => Controls_Manager::RAW_HTML,
						/* translators: %1$s Doc Link */
						'raw'             => sprintf( __( '%1$s What is the difference between Fixed, Evergreen timer? » %2$s', 'uael' ), '<a href=' . UAEL_DOMAIN . 'docs/faqs-for-countdown-timer-widget/?utm_source=uael-pro-dashboard&utm_medium=uael-editor-screen&utm_campaign=uael-pro-plugin#what-is-the-difference-between-fixed-evergreen-timer-" target="_blank" rel="noopener">', '</a>' ),
						'content_classes' => 'uael-editor-doc',
					)
				);

				$this->add_control(
					'help_doc_4',
					array(
						'type'            => Controls_Manager::RAW_HTML,
						/* translators: %1$s Doc Link */
						'raw'             => sprintf( __( '%1$s What is the difference between Evergreen and Recurring Timer  » %2$s', 'uael' ), '<a href=' . UAEL_DOMAIN . 'docs/faqs-for-countdown-timer-widget/?utm_source=uael-pro-dashboard&utm_medium=uael-editor-screen&utm_campaign=uael-pro-plugin#what-is-the-difference-between-evergreen-and-recurring-timer-and-how-does-the-recurring-timer-works-" target="_blank" rel="noopener">', '</a>' ),
						'content_classes' => 'uael-editor-doc',
					)
				);

				$this->add_control(
					'help_doc_5',
					array(
						'type'            => Controls_Manager::RAW_HTML,
						/* translators: %1$s Doc Link */
						'raw'             => sprintf( __( '%1$s How does the Recurring Timer works? » %2$s', 'uael' ), '<a href=' . UAEL_DOMAIN . 'docs/faqs-for-countdown-timer-widget/?utm_source=uael-pro-dashboard&utm_medium=uael-editor-screen&utm_campaign=uael-pro-plugin#what-is-the-difference-between-evergreen-and-recurring-timer-and-how-does-the-recurring-timer-works-" target="_blank" rel="noopener">', '</a>' ),
						'content_classes' => 'uael-editor-doc',
					)
				);

				$this->add_control(
					'help_doc_6',
					array(
						'type'            => Controls_Manager::RAW_HTML,
						/* translators: %1$s Doc Link */
						'raw'             => sprintf( __( '%1$s Which timezone does the timer use? » %2$s', 'uael' ), '<a href=' . UAEL_DOMAIN . 'docs/faqs-for-countdown-timer-widget/?utm_source=uael-pro-dashboard&utm_medium=uael-editor-screen&utm_campaign=uael-pro-plugin#which-timezone-does-the-timer-use-" target="_blank" rel="noopener">', '</a>' ),
						'content_classes' => 'uael-editor-doc',
					)
				);

			$this->end_controls_section();
		}
	}

	/**
	 * Render output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.14.0
	 * @access protected
	 */
	protected function render() {
		$settings        = $this->get_settings_for_display();
		$id              = $this->get_id();
		$labels          = array( 'days', 'hours', 'minutes', 'seconds' );
		$length          = count( $labels );
		$edit_mode       = \Elementor\Plugin::instance()->editor->is_edit_mode();
		$data_attributes = array( 'data-countdown-type', 'data-timer-labels', 'data-animation' );
		$data_values     = array( $settings['countdown_type'], $settings['display_timer_labels'], $settings['start_animation'] );

		$this->add_render_attribute( 'countdown-wrapper', 'class', 'uael-countdown-items-wrapper' );

		$this->add_render_attribute( 'countdown', 'class', array( 'uael-countdown-wrapper', 'countdown-active' ) );

		$this->add_render_attribute( 'digits', 'class', 'uael-countdown-item' );

		for ( $i = 0; $i < 3; $i++ ) {
			$this->add_render_attribute( 'countdown', $data_attributes[ $i ], $data_values[ $i ] );
		}

		if ( $edit_mode && 'yes' === $settings['preview_expire_message'] ) {
			$this->add_render_attribute( 'countdown', 'class', 'uael-preview-message' );
		}

		if ( 'custom' === $settings['display_timer_labels'] ) {
			for ( $i = 0; $i < $length; $i++ ) {
				$this->add_render_attribute( 'countdown', 'data-timer-' . $labels[ $i ], $settings[ 'custom_' . $labels[ $i ] ] );
			}
		}

		$due_date = $settings['due_date'];
		$gmt      = get_gmt_from_date( $due_date . ':00' );
		$due_date = strtotime( $gmt );

		if ( 'fixed' === $settings['countdown_type'] ) {
			$this->add_render_attribute( 'countdown', 'data-due-date', $due_date );
		} else {
			$this->add_render_attribute( 'countdown', 'data-evg-interval', $this->total_interval( $settings ) );
		}

		if ( 'recurring' === $settings['countdown_type'] ) {

			$reset_date      = $settings['start_date'];
			$reset_date_gmt  = get_gmt_from_date( $reset_date . ':00' );
			$reset_date_time = strtotime( $reset_date_gmt );
			$this->add_render_attribute( 'countdown', 'data-start-date', $reset_date_time );
			$this->add_render_attribute( 'countdown', 'data-reset-days', $settings['reset_days'] );
		}

		if ( 'redirect' === $settings['expire_actions'] ) {
			$this->add_render_attribute( 'countdown', 'data-redirect-url', $settings['expire_redirect_url']['url'] );
			$this->add_render_attribute( 'url', 'href', $settings['expire_redirect_url']['url'] );
		} elseif ( 'show_message' === $settings['expire_actions'] ) {
			$this->add_render_attribute( 'countdown', 'data-countdown-expire-message', $settings['preview_expire_message'] );
		}

		if ( 'evergreen' === $settings['countdown_type'] && 'yes' === $settings['reset_evergreen'] ) {

			$this->add_render_attribute( 'countdown', 'data-expire-action', 'reset' );
		} elseif ( 'evergreen' === $settings['countdown_type'] && 'yes' !== $settings['reset_evergreen'] ) {

			$this->add_render_attribute( 'countdown', 'data-expire-action', $settings['expire_actions'] );
		} elseif ( 'fixed' === $settings['countdown_type'] || 'recurring' === $settings['countdown_type'] ) {

			$this->add_render_attribute( 'countdown', 'data-expire-action', $settings['expire_actions'] );
		}

		if ( isset( $_COOKIE[ 'uael-timer-distance-' . $id ] ) ) {
			if ( 'hide' === $settings['expire_actions'] && 0 > $_COOKIE[ 'uael-timer-distance-' . $id ] && false === $edit_mode ) { // phpcs:ignore WordPressVIPMinimum.Variables.RestrictedVariables.cache_constraints___COOKIE
				$this->add_render_attribute( 'countdown', 'class', 'uael-countdown-hide' );
			}
		}

		if ( 'none' === $settings['display_timer_labels'] ) {
			$this->add_render_attribute( 'countdown', 'class', 'uael-countdown-labels-hide' );
		}

		if ( 'yes' !== $settings['display_days'] ) {
			$this->add_render_attribute( 'countdown', 'class', 'uael-countdown-show-days-no' );
		}

		if ( 'yes' !== $settings['display_hours'] ) {
			$this->add_render_attribute( 'countdown', 'class', 'uael-countdown-show-hours-no' );
		}

		if ( 'yes' !== $settings['display_minutes'] ) {
			$this->add_render_attribute( 'countdown', 'class', 'uael-countdown-show-minutes-no' );
		}

		if ( 'yes' !== $settings['display_seconds'] ) {
			$this->add_render_attribute( 'countdown', 'class', 'uael-countdown-show-seconds-no' );
		}

		?>
		<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'countdown' ) ); ?>>
			<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'countdown-wrapper' ) ); ?> >
					<?php for ( $i = 0; $i < $length; $i++ ) { ?>
						<div class="uael-countdown-<?php echo esc_attr( $labels[ $i ] ); ?> uael-item">
							<span id="<?php echo esc_attr( $labels[ $i ] ); ?>-wrapper-<?php echo esc_attr( $id ); ?>"<?php echo wp_kses_post( $this->get_render_attribute_string( 'digits' ) ); ?> >
								<?php if ( true === $edit_mode ) { ?>
									<?php echo esc_html_e( '00', 'uael' ); ?>
								<?php } ?>
							</span>
							<?php if ( 'none' !== $settings['display_timer_labels'] ) { ?>
								<span id="<?php echo esc_attr( $labels[ $i ] ); ?>-label-wrapper-<?php echo esc_attr( $id ); ?>" class='uael-countdown-<?php echo esc_attr( $labels[ $i ] ); ?>-label-<?php echo esc_attr( $id ); ?> uael-item-label'>
								</span>
							<?php } ?>
						</div>
						<?php if ( 'yes' === $settings['countdown_separator'] && $i < $length - 1 ) { ?>
							<div class="uael-countdown-separator uael-countdown-<?php echo esc_attr( $labels[ $i ] ); ?>-separator"> : </div>
						<?php } ?>
					<?php } ?>
					<div class="uael-expire-message-wrapper">
						<div class='uael-expire-show-message'><?php echo wp_kses_post( sanitize_text_field( $settings['message_after_expire'] ) ); ?></div>
					</div>
			</div>
		</div>
		<?php
	}
}

