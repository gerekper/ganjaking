<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Porto Elementor Countdown Widget
 *
 * Porto Elementor widget to display a countdown timer.
 *
 * @since 5.2.0
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

	protected function _register_controls() {

		$floating_options = porto_update_vc_options_to_elementor( porto_shortcode_floating_fields() );

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
					'porto-cd-s1' => __( 'Digit and Unit Side by Side', 'porto-functionality' ),
					'porto-cd-s2' => __( 'Digit and Unit Up and Down', 'porto-functionality' ),
				),
				'default' => 'porto-cd-s1',
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
			)
		);

		$this->add_control(
			'porto_tz',
			array(
				'type'    => Controls_Manager::SELECT,
				'label'   => __( 'Countdown Timer Depends on', 'porto-functionality' ),
				'options' => array(
					'porto-wptz'  => __( 'WordPress Defined Timezone', 'porto-functionality' ),
					'porto-usrtz' => __( "User's System Timezone", 'porto-functionality' ),
				),
				'default' => 'porto-wptz',
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

		$this->add_control(
			'string_days',
			array(
				'type'    => Controls_Manager::TEXT,
				'label'   => __( 'Day (Singular)', 'porto-functionality' ),
				'default' => 'Day',
			)
		);

		$this->add_control(
			'string_days2',
			array(
				'type'    => Controls_Manager::TEXT,
				'label'   => __( 'Days (Plural)', 'porto-functionality' ),
				'default' => 'Days',
			)
		);

		$this->add_control(
			'string_weeks',
			array(
				'type'    => Controls_Manager::TEXT,
				'label'   => __( 'Week (Singular)', 'porto-functionality' ),
				'default' => 'Week',
			)
		);

		$this->add_control(
			'string_weeks2',
			array(
				'type'    => Controls_Manager::TEXT,
				'label'   => __( 'Weeks (Plural)', 'porto-functionality' ),
				'default' => 'Weeks',
			)
		);

		$this->add_control(
			'string_months',
			array(
				'type'    => Controls_Manager::TEXT,
				'label'   => __( 'Month (Singular)', 'porto-functionality' ),
				'default' => 'Month',
			)
		);

		$this->add_control(
			'string_months2',
			array(
				'type'    => Controls_Manager::TEXT,
				'label'   => __( 'Months (Plural)', 'porto-functionality' ),
				'default' => 'Months',
			)
		);

		$this->add_control(
			'string_years',
			array(
				'type'    => Controls_Manager::TEXT,
				'label'   => __( 'Year (Singular)', 'porto-functionality' ),
				'default' => 'Year',
			)
		);

		$this->add_control(
			'string_years2',
			array(
				'type'    => Controls_Manager::TEXT,
				'label'   => __( 'Years (Plural)', 'porto-functionality' ),
				'default' => 'Years',
			)
		);

		$this->add_control(
			'string_hours',
			array(
				'type'    => Controls_Manager::TEXT,
				'label'   => __( 'Hour (Singular)', 'porto-functionality' ),
				'default' => 'Hour',
			)
		);

		$this->add_control(
			'string_hours2',
			array(
				'type'    => Controls_Manager::TEXT,
				'label'   => __( 'Hours (Plural)', 'porto-functionality' ),
				'default' => 'Hours',
			)
		);

		$this->add_control(
			'string_minutes',
			array(
				'type'    => Controls_Manager::TEXT,
				'label'   => __( 'Minute (Singular)', 'porto-functionality' ),
				'default' => 'Minute',
			)
		);

		$this->add_control(
			'string_minutes2',
			array(
				'type'    => Controls_Manager::TEXT,
				'label'   => __( 'Minutes (Plural)', 'porto-functionality' ),
				'default' => 'Minutes',
			)
		);

		$this->add_control(
			'string_seconds',
			array(
				'type'    => Controls_Manager::TEXT,
				'label'   => __( 'Second (Singular)', 'porto-functionality' ),
				'default' => 'Second',
			)
		);

		$this->add_control(
			'string_seconds2',
			array(
				'type'    => Controls_Manager::TEXT,
				'label'   => __( 'Seconds (Plural)', 'porto-functionality' ),
				'default' => 'Seconds',
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

		$this->add_group_control(
			Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'countdown_typograpy',
				'scheme'   => Elementor\Core\Schemes\Typography::TYPOGRAPHY_1,
				'label'    => __( 'Timer Digit Typograhy', 'porto-functionality' ),
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
				'label'    => __( 'Timer Unit Typograhy', 'porto-functionality' ),
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
