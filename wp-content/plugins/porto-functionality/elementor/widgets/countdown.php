<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Porto Elementor Countdown Widget
 *
 * Porto Elementor widget to display a countdown timer.
 *
 * @since 1.5.2
 */

use Elementor\Controls_Manager;

class Porto_Elementor_Countdown_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'porto_countdown';
	}

	public function get_title() {
		return __( 'Porto Countdown Timer', 'porto-functionality' );
	}

	public function get_categories() {
		return array( 'porto-elements' );
	}

	public function get_keywords() {
		return array( 'date', 'timer', 'countdown' );
	}

	public function get_icon() {
		return 'eicon-countdown';
	}

	public function get_script_depends() {
		if ( ( isset( $_REQUEST['action'] ) && 'elementor' == $_REQUEST['action'] ) || isset( $_REQUEST['elementor-preview'] ) ) {
			return array( 'countdown', 'porto_shortcodes_countdown_loader_js', 'porto-elementor-widgets-js' );
		} else {
			return array();
		}
	}

	protected function register_controls() {

		// $floating_options = porto_update_vc_options_to_elementor( porto_shortcode_floating_fields() );
		$left = is_rtl() ? 'right' : 'left';

		$this->start_controls_section(
			'section_countdown',
			array(
				'label' => __( 'Countdown Timer', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'count_style',
			array(
				'type'    => Controls_Manager::SELECT,
				'label'   => __( 'Countdown Timer Style', 'porto-functionality' ),
				'options' => array(
					'porto-cd-s1' => __( 'Inline', 'porto-functionality' ),
					'porto-cd-s2' => __( 'Block', 'porto-functionality' ),
				),
				'default' => 'porto-cd-s1',
			)
		);

		$this->add_control(
			'enable_dynamic_date',
			array(
				'type'  => Controls_Manager::SWITCHER,
				'label' => __( 'Enable Dynamic Date Time.', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'dynamic_datetime',
			array(
				'type'        => Controls_Manager::TEXT,
				'label'       => __( 'Target Time For Countdown', 'porto-functionality' ),
				'label_block' => true,
				'dynamic'     => array(
					'active' => true,
				),
				'condition'   => array(
					'enable_dynamic_date' => 'yes',
				),
			)
		);

		$this->add_control(
			'datetime',
			array(
				'type'           => Controls_Manager::DATE_TIME,
				'label'          => __( 'Target Time For Countdown', 'porto-functionality' ),
				'description'    => __( 'Date and time format (yyyy/mm/dd hh:mm:ss).', 'porto-functionality' ),
				'picker_options' => array(
					'enableSeconds' => true,
					'dateFormat'    => 'Y/m/d H:i:S',
				),
				'condition'      => array(
					'enable_dynamic_date!' => 'yes',
				),
			)
		);

		$this->add_control(
			'porto_tz',
			array(
				'type'        => Controls_Manager::SELECT,
				'label'       => __( 'Countdown Timer Depends on', 'porto-functionality' ),
				'label_block' => true,
				'options'     => array(
					'porto-wptz'  => __( 'WordPress Defined Timezone', 'porto-functionality' ),
					'porto-usrtz' => __( "User's System Timezone", 'porto-functionality' ),
				),
				'default'     => 'porto-wptz',
			)
		);

		$this->add_control(
			'countdown_opts',
			array(
				'type'     => Controls_Manager::SELECT2,
				'label'    => __( 'Time Units', 'porto-functionality' ),
				'options'  => array(
					'syear'  => __( 'Years', 'porto-functionality' ),
					'smonth' => __( 'Months', 'porto-functionality' ),
					'sweek'  => __( 'Weeks', 'porto-functionality' ),
					'sday'   => __( 'Days', 'porto-functionality' ),
					'shr'    => __( 'Hours', 'porto-functionality' ),
					'smin'   => __( 'Minutes', 'porto-functionality' ),
					'ssec'   => __( 'Seconds', 'porto-functionality' ),
				),
				'multiple' => true,
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_countdown_texts',
			array(
				'label' => __( 'Texts', 'porto-functionality' ),
			)
		);

			$this->add_control(
				'string_years',
				array(
					'type'       => Controls_Manager::TEXT,
					'label'      => __( 'Year', 'porto-functionality' ),
					'default'    => 'Year',
					'conditions' => array(
						'terms' => array(
							array(
								'name'     => 'countdown_opts',
								'operator' => 'contains',
								'value'    => 'syear',
							),
						),
					),
				)
			);
			$this->add_control(
				'string_months',
				array(
					'type'       => Controls_Manager::TEXT,
					'label'      => __( 'Month', 'porto-functionality' ),
					'default'    => 'Month',
					'conditions' => array(
						'terms' => array(
							array(
								'name'     => 'countdown_opts',
								'operator' => 'contains',
								'value'    => 'smonth',
							),
						),
					),
				)
			);
			$this->add_control(
				'string_weeks',
				array(
					'type'       => Controls_Manager::TEXT,
					'label'      => __( 'Week', 'porto-functionality' ),
					'default'    => 'Week',
					'conditions' => array(
						'terms' => array(
							array(
								'name'     => 'countdown_opts',
								'operator' => 'contains',
								'value'    => 'sweek',
							),
						),
					),
				)
			);
			$this->add_control(
				'string_days',
				array(
					'type'       => Controls_Manager::TEXT,
					'label'      => __( 'Day', 'porto-functionality' ),
					'default'    => 'Day',
					'conditions' => array(
						'terms' => array(
							array(
								'name'     => 'countdown_opts',
								'operator' => 'contains',
								'value'    => 'sday',
							),
						),
					),
				)
			);
			$this->add_control(
				'string_hours',
				array(
					'type'       => Controls_Manager::TEXT,
					'label'      => __( 'Hour', 'porto-functionality' ),
					'default'    => 'Hour',
					'conditions' => array(
						'terms' => array(
							array(
								'name'     => 'countdown_opts',
								'operator' => 'contains',
								'value'    => 'shr',
							),
						),
					),
				)
			);
			$this->add_control(
				'string_minutes',
				array(
					'type'       => Controls_Manager::TEXT,
					'label'      => __( 'Minute', 'porto-functionality' ),
					'default'    => 'Minute',
					'conditions' => array(
						'terms' => array(
							array(
								'name'     => 'countdown_opts',
								'operator' => 'contains',
								'value'    => 'smin',
							),
						),
					),
				)
			);
			$this->add_control(
				'string_seconds',
				array(
					'type'       => Controls_Manager::TEXT,
					'label'      => __( 'Second', 'porto-functionality' ),
					'default'    => 'Second',
					'conditions' => array(
						'terms' => array(
							array(
								'name'     => 'countdown_opts',
								'operator' => 'contains',
								'value'    => 'ssec',
							),
						),
					),
				)
			);
		$this->end_controls_section();

		$this->start_controls_section(
			'section_countdown_texts_plural',
			array(
				'label' => __( 'Texts Plural', 'porto-functionality' ),
			)
		);

			$this->add_control(
				'string_years2',
				array(
					'type'       => Controls_Manager::TEXT,
					'label'      => __( 'Years (Plural)', 'porto-functionality' ),
					'default'    => 'Years',
					'conditions' => array(
						'terms' => array(
							array(
								'name'     => 'countdown_opts',
								'operator' => 'contains',
								'value'    => 'syear',
							),
						),
					),
				)
			);
			$this->add_control(
				'string_months2',
				array(
					'type'       => Controls_Manager::TEXT,
					'label'      => __( 'Months (Plural)', 'porto-functionality' ),
					'default'    => 'Months',
					'conditions' => array(
						'terms' => array(
							array(
								'name'     => 'countdown_opts',
								'operator' => 'contains',
								'value'    => 'smonth',
							),
						),
					),
				)
			);
			$this->add_control(
				'string_weeks2',
				array(
					'type'       => Controls_Manager::TEXT,
					'label'      => __( 'Weeks (Plural)', 'porto-functionality' ),
					'default'    => 'Weeks',
					'conditions' => array(
						'terms' => array(
							array(
								'name'     => 'countdown_opts',
								'operator' => 'contains',
								'value'    => 'sweek',
							),
						),
					),
				)
			);
			$this->add_control(
				'string_days2',
				array(
					'type'       => Controls_Manager::TEXT,
					'label'      => __( 'Days (Plural)', 'porto-functionality' ),
					'default'    => 'Days',
					'conditions' => array(
						'terms' => array(
							array(
								'name'     => 'countdown_opts',
								'operator' => 'contains',
								'value'    => 'sday',
							),
						),
					),
				)
			);
			$this->add_control(
				'string_hours2',
				array(
					'type'       => Controls_Manager::TEXT,
					'label'      => __( 'Hours (Plural)', 'porto-functionality' ),
					'default'    => 'Hours',
					'conditions' => array(
						'terms' => array(
							array(
								'name'     => 'countdown_opts',
								'operator' => 'contains',
								'value'    => 'shr',
							),
						),
					),
				)
			);
			$this->add_control(
				'string_minutes2',
				array(
					'type'       => Controls_Manager::TEXT,
					'label'      => __( 'Minutes (Plural)', 'porto-functionality' ),
					'default'    => 'Minutes',
					'conditions' => array(
						'terms' => array(
							array(
								'name'     => 'countdown_opts',
								'operator' => 'contains',
								'value'    => 'smin',
							),
						),
					),
				)
			);
			$this->add_control(
				'string_seconds2',
				array(
					'type'       => Controls_Manager::TEXT,
					'label'      => __( 'Seconds (Plural)', 'porto-functionality' ),
					'default'    => 'Seconds',
					'conditions' => array(
						'terms' => array(
							array(
								'name'     => 'countdown_opts',
								'operator' => 'contains',
								'value'    => 'ssec',
							),
						),
					),
				)
			);
		$this->end_controls_section();

		$this->start_controls_section(
			'section_countdown_style_counter_box',
			array(
				'label' => __( 'Counter Box', 'porto-functionality' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

			$this->add_control(
				'is_middle',
				array(
					'type'      => Controls_Manager::SWITCHER,
					'label'     => __( 'Align Middle ?', 'porto-functionality' ),
					'selectors' => array(
						'{{WRAPPER}} .porto_countdown-amount, {{WRAPPER}} .porto_countdown-period' => 'vertical-align: middle;',
					),
					'condition' => array(
						'count_style' => 'porto-cd-s1',
					),
				)
			);
			$this->add_responsive_control(
				'padding',
				array(
					'label'      => __( 'Padding', 'porto-functionality' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'selectors'  => array(
						'{{WRAPPER}} .porto_countdown-section' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
					'size_units' => array( 'px', 'em' ),
				)
			);
			$this->add_control(
				'margin',
				array(
					'label'      => __( 'Margin', 'porto-functionality' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'selectors'  => array(
						'{{WRAPPER}} .porto_countdown-section' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
					'size_units' => array( 'px', 'em' ),
				)
			);
			$this->add_control(
				'spacing',
				array(
					'type'        => Controls_Manager::SLIDER,
					'label'       => __( 'Spacing Between units and label', 'porto-functionality' ),
					'range'       => array(
						'px' => array(
							'step' => 1,
							'min'  => 0,
							'max'  => 32,
						),
						'em' => array(
							'step' => 0.1,
							'min'  => 0,
							'max'  => 2,
						),
					),
					'size_units'  => array(
						'px',
						'em',
					),
					'qa_selector' => '.porto_countdown-section:first-child',
					'selectors'   => array(
						'{{WRAPPER}} .porto-cd-s2 .porto_countdown-section .porto_countdown-period' => 'margin-top: {{SIZE}}{{UNIT}};',
						'{{WRAPPER}} .porto-cd-s1 .porto_countdown-section .porto_countdown-period'  => 'margin-' . $left . ': {{SIZE}}{{UNIT}};',
					),
				)
			);
			$this->add_control(
				'bottom_spacing',
				array(
					'type'        => Controls_Manager::SLIDER,
					'label'       => __( 'Bottom Spacing', 'porto-functionality' ),
					'description' => __( 'Controls the bottom spacing of counter boxes.', 'porto-functionality' ),
					'range'       => array(
						'px' => array(
							'step' => 1,
							'min'  => 0,
							'max'  => 32,
						),
						'em' => array(
							'step' => 0.1,
							'min'  => 0,
							'max'  => 2,
						),
					),
					'size_units'  => array(
						'px',
						'em',
					),
					'selectors'   => array(
						'{{WRAPPER}} .porto_countdown' => 'margin-bottom: {{SIZE}}{{UNIT}};',
					),
				)
			);
			$this->add_responsive_control(
				'item_width',
				array(
					'type'        => Controls_Manager::SLIDER,
					'label'       => __( 'Item Width', 'porto-functionality' ),
					'description' => __( 'Controls the width of each item', 'porto-functionality' ),
					'range'       => array(
						'%'  => array(
							'step' => 1,
							'min'  => 0,
							'max'  => 100,
						),
						'px' => array(
							'step' => 1,
							'min'  => 0,
							'max'  => 300,
						),
					),
					'size_units'  => array(
						'%',
						'px',
					),
					'selectors'   => array(
						'{{WRAPPER}} .porto_countdown-section' => 'width: {{SIZE}}{{UNIT}};',
					),
				)
			);
			$this->add_control(
				'flexbox_align',
				array(
					'label'       => esc_html__( 'FlexBox Alignment', 'porto-functionality' ),
					'description' => esc_html__( 'Controls the alignment of counter boxes.', 'porto-functionality' ),
					'type'        => Controls_Manager::CHOOSE,
					'options'     => array(
						'flex-start' => array(
							'title' => esc_html__( 'Left', 'porto-functionality' ),
							'icon'  => 'eicon-text-align-left',
						),
						'center'     => array(
							'title' => esc_html__( 'Center', 'porto-functionality' ),
							'icon'  => 'eicon-text-align-center',
						),
						'flex-end'   => array(
							'title' => esc_html__( 'Right', 'porto-functionality' ),
							'icon'  => 'eicon-text-align-right',
						),
					),
					'selectors'   => array(
						'{{WRAPPER}} .porto_countdown-row' => 'display: flex; flex-wrap: wrap;',
						'.elementor-element-{{ID}} .porto_countdown-row' => 'justify-content: {{VALUE}}',
					),
					'condition'   => array(
						'item_width[size]!' => '',
					),
				)
			);
		$this->end_controls_section();

		$this->start_controls_section(
			'section_countdown_style_options',
			array(
				'label' => __( 'Style', 'porto-functionality' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

			$this->add_control(
				'countdown_align',
				array(
					'label'     => esc_html__( 'Text Alignment', 'porto-functionality' ),
					'type'      => Controls_Manager::CHOOSE,
					'options'   => array(
						'left'   => array(
							'title' => esc_html__( 'Left', 'porto-functionality' ),
							'icon'  => 'eicon-text-align-left',
						),
						'center' => array(
							'title' => esc_html__( 'Center', 'porto-functionality' ),
							'icon'  => 'eicon-text-align-center',
						),
						'right'  => array(
							'title' => esc_html__( 'Right', 'porto-functionality' ),
							'icon'  => 'eicon-text-align-right',
						),
					),
					'selectors' => array(
						'.elementor-element-{{ID}} .porto_countdown' => 'text-align: {{VALUE}}',
					),
				)
			);

			$this->add_control(
				'cdsection_bg',
				array(
					'type'      => Controls_Manager::COLOR,
					'label'     => __( 'Timer Box Background', 'porto-functionality' ),
					'selectors' => array(
						'.elementor-element-{{ID}} .porto_countdown-section' => 'background-color: {{VALUE}};',
					),
				)
			);

			$this->add_group_control(
				Elementor\Group_Control_Typography::get_type(),
				array(
					'name'     => 'countdown_typograpy',
					'scheme'   => Elementor\Core\Schemes\Typography::TYPOGRAPHY_1,
					'label'    => __( 'Timer Digit Typography', 'porto-functionality' ),
					'selector' => '{{WRAPPER}} .porto_countdown-amount',
				)
			);

			$this->add_control(
				'tick_col1',
				array(
					'type'      => Controls_Manager::COLOR,
					'label'     => __( 'Timer Digit Text Color', 'porto-functionality' ),
					'selectors' => array(
						'{{WRAPPER}} .porto_countdown-amount' => 'color: {{VALUE}};',
					),
				)
			);

			$this->add_group_control(
				Elementor\Group_Control_Typography::get_type(),
				array(
					'name'     => 'countdown_typograpy1',
					'scheme'   => Elementor\Core\Schemes\Typography::TYPOGRAPHY_1,
					'label'    => __( 'Timer Unit Typography', 'porto-functionality' ),
					'selector' => '{{WRAPPER}} .porto_countdown-period, {{WRAPPER}} .porto_countdown-row:before',
				)
			);

			$this->add_control(
				'tick_sep_col1',
				array(
					'type'      => Controls_Manager::COLOR,
					'label'     => __( 'Timer Unit Text Color', 'porto-functionality' ),
					'selectors' => array(
						'{{WRAPPER}} .porto_countdown-period, {{WRAPPER}} .porto_countdown-row:before' => 'color: {{VALUE}};',
					),
				)
			);

		$this->end_controls_section();
	}

	protected function render() {
		$atts = $this->get_settings_for_display();
		if ( $template = porto_shortcode_template( 'porto_countdown' ) ) {
			$atts['page_builder'] = 'elementor';
			if ( ! empty( $atts['__dynamic__'] ) && ! empty( $atts['__dynamic__']['dynamic_datetime'] ) && strpos( $atts['__dynamic__']['dynamic_datetime'], 'porto-custom-field-woo' ) > 0 && strpos( $atts['__dynamic__']['dynamic_datetime'], 'sale_date' ) > 0 ) {
				$atts['is_sp_countdown'] = true;
			}
			include $template;
		}
	}

	protected function content_template() {
		?>
		<#
			view.addRenderAttribute( 'countdown', 'class', 'porto_countdown-div porto_countdown-dateAndTime ' + settings.porto_tz );
			let count_frmt = '', content_html = '';
			settings.countdown_opts && settings.countdown_opts.forEach(function(opt, index) {
				if ( 'syear' == opt ) {
					count_frmt += 'Y';
				}
				if ( 'smonth' == opt ) {
					count_frmt += 'O';
				}
				if ( 'sweek' == opt ) {
					count_frmt += 'W';
				}
				if ( 'sday' == opt ) {
					count_frmt += 'D';
				}
				if ( 'shr' == opt ) {
					count_frmt += 'H';
				}
				if ( 'smin' == opt ) {
					count_frmt += 'M';
				}
				if ( 'ssec' == opt ) {
					count_frmt += 'S';
				}
			});
			let times = { years: '', months: '', weeks: '', days: '', hours: '', minutes: '', seconds: '' };

			if ( 'yes' == settings.enable_dynamic_date ) {
				settings.datetime = settings.dynamic_datetime.replace(/-/gi,'/');
			}
			let time = new Date( settings.datetime );
			if( Number.isNaN( time.getTime() ) ){
				return;
			}
			if ( settings.datetime ) {
				view.addRenderAttribute( 'countdown', 'data-labels', ( settings.string_years2 ? settings.string_years2 : 'Years' ) + ',' + ( settings.string_months2 ? settings.string_months2 : 'Months' ) + ',' + ( settings.string_weeks2 ? settings.string_weeks2 : 'Weeks' ) + ',' + ( settings.string_days2 ? settings.string_days2 : 'Days' ) + ',' + ( settings.string_hours2 ? settings.string_hours2 : 'Hours' ) + ',' + ( settings.string_minutes2 ? settings.string_minutes2 : 'Minutes' ) + ',' + ( settings.string_seconds2 ? settings.string_seconds2 : 'Seconds' ) );
				view.addRenderAttribute( 'countdown', 'data-labels2', ( settings.string_years ? settings.string_years : 'Year' ) + ',' + ( settings.string_months ? settings.string_months : 'Month' ) + ',' + ( settings.string_weeks ? settings.string_weeks : 'Week' ) + ',' + ( settings.string_days ? settings.string_days : 'Day' ) + ',' + ( settings.string_hours ? settings.string_hours : 'Hour' ) + ',' + ( settings.string_minutes ? settings.string_minutes : 'Minute' ) + ',' + ( settings.string_seconds ? settings.string_seconds : 'Second' ) );

				view.addRenderAttribute( 'countdown', 'data-terminal-date', settings.datetime );
				view.addRenderAttribute( 'countdown', 'data-countformat', count_frmt );

				let inttime = new Date( settings.datetime ),
					currenttime = new Date(),
					difftime = Math.round( (inttime.getTime() - currenttime.getTime()) / 1000 );

				if ( difftime > 0 ) {
					if ( -1 !== settings.countdown_opts.indexOf( 'syear' ) ) {
						times['years'] = inttime.getFullYear() - currenttime.getFullYear();
						inttime = new Date( currenttime.getFullYear(), inttime.getMonth(), inttime.getDate(), inttime.getHours(), inttime.getMinutes(), inttime.getSeconds() );
					}
					if ( -1 !== settings.countdown_opts.indexOf( 'smonth' ) ) {
						times['months'] = ( inttime.getFullYear() - currenttime.getFullYear() ) * 12 + inttime.getMonth() - currenttime.getMonth();
						if ( times['months'] < 0 ) {
							times['months'] += 12;
						}
					}
					inttime = Math.round( inttime.getTime() / 1000 );
					currenttime = Math.round( currenttime.getTime() / 1000 );
					if ( -1 !== settings.countdown_opts.indexOf( 'sweek' ) ) {
						times['weeks'] = Math.floor( difftime / 3600 / 24 / 7 );
						inttime = currenttime + difftime % ( 24 * 3600 * 7 );
					}
					if ( -1 !== settings.countdown_opts.indexOf( 'sday' ) ) {
						times['days'] = Math.floor( ( inttime - currenttime ) / 24 / 3600 );
						inttime = currenttime + difftime % ( 24 * 3600 );
					}
					if ( -1 !== settings.countdown_opts.indexOf( 'shr' ) ) {
						if ( -1 !== settings.countdown_opts.indexOf( 'smin' ) || -1 !== settings.countdown_opts.indexOf( 'ssec' ) ) {
							times['hours'] = Math.floor( ( inttime - currenttime ) / 3600 );
						} else {
							times['hours'] = Math.round( ( inttime - currenttime ) / 3600 );
						}
						inttime = currenttime + difftime % 3600;
						if ( times['hours'] < 10 ) {
							times['hours'] = '0' + times['hours'];
						}
					}
					if ( -1 !== settings.countdown_opts.indexOf( 'smin' ) ) {
						if ( -1 !== settings.countdown_opts.indexOf( 'ssec' ) ) {
							times['minutes'] = Math.floor( ( inttime - currenttime ) / 60 );
						} else {
							times['minutes'] = Math.round( ( inttime - currenttime ) / 60 );
						}
						inttime = currenttime + difftime % 60;
						if ( times['minutes'] < 10 ) {
							times['minutes'] = '0' + times['minutes'];
						}
					}
					if ( -1 !== settings.countdown_opts.indexOf( 'ssec' ) ) {
						times['seconds'] = inttime - currenttime;
						if ( times['seconds'] < 10 ) {
							times['seconds'] = '0' + times['seconds'];
						}
					}
				}

				let section_arr = {
					'year': 'syear',
					'month': 'smonth',
					'week': 'sweek',
					'day': 'sday',
					'hour': 'shr',
					'minute': 'smin',
					'second': 'ssec'
				};
				content_html += '<span class="porto_countdown-row porto_countdown-show4">';
				jQuery.each(section_arr, function(section, section_key) {
					if ( -1 !== settings.countdown_opts.indexOf( section_key ) ) {
						content_html += '<span class="porto_countdown-section"><span class="porto_time-mid"><span class="porto_countdown-amount">' + ( times[ section + 's' ] ? times[ section + 's' ] : '00' ) + '</span><span class="porto_countdown-period">' + ( times[ section + 's' ] && Number( times[ section + 's' ] ) === 1 ? settings[ 'string_' + section + 's' ] : settings[ 'string_' + section + 's2' ] ) + '</span></span></span>';
					}
				});
				content_html += '</span>';
			}
		#>
		<div class="porto_countdown {{ settings.count_style }}">
		<# if ( settings.datetime ) { #>
			<div {{{ view.getRenderAttributeString( 'countdown' ) }}}>
			<# print( content_html ); #>
			</div>
		<# } #>
		</div>
		<?php
	}
}
