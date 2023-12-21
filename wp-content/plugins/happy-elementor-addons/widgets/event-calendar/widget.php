<?php
/**
 * Event Calendar widget class
 *
 * @package Happy_Addons
 */

namespace Happy_Addons\Elementor\Widget;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Image_Size;
use Elementor\Utils;
use Elementor\Repeater;

defined( 'ABSPATH' ) || die();

class Event_Calendar extends Base {

	/**
	 * Get widget title.
	 *
	 * @return string Widget title.
	 * @since 1.0.0
	 * @access public
	 *
	 */
	public function get_title() {
		return __( 'Event Calendar', 'happy-elementor-addons' );
	}

	public function get_custom_help_url() {
		return 'https://happyaddons.com/docs/happy-addons-for-elementor/widgets/events-calendar/';
	}

	/**
	 * Get widget icon.
	 *
	 * @return string Widget icon.
	 * @since 1.0.0
	 * @access public
	 *
	 */
	public function get_icon() {
		return 'hm hm-event-calendar';
	}

	public function get_keywords() {
		return [ 'event-calendar', 'event', 'calender', 'time', 'shedule', 'google-calender' ];
	}

	/**
	 * Get a list of all event
	 *
	 * @return array
	 */
	public static function get_events() {
		if ( ! function_exists( 'tribe_get_events' ) ) {
			return [];
		}
		$posts = [];

		$_posts = tribe_get_events();

		if ( ! empty( $_posts ) ) {
			$posts = wp_list_pluck( $_posts, 'post_title', 'ID' );
		}
		return $posts;

	}

	/**
	 * Get the event calender category list
	 *
	 * @return array
	 */
	public static function get_the_event_calendar_cat() {
		if ( ! function_exists( 'tribe_get_events' ) ) {
			return [];
		}
		$args    = [
			'taxonomy'   => 'tribe_events_cat',
			'hide_empty' => false,
		];
		$options = [];
		$tags    = get_tags( $args );

		if ( is_wp_error( $tags ) ) {
			return [];
		}

		foreach ( $tags as $tag ) {
			$options[ $tag->term_id ] = $tag->name;
		}
		return $options;
	}

	/**
	 * Get a language code
	 *
	 * @return array
	 */
	protected function language_code_list() {
		return [
			'af'    => 'Afrikaans',
			'sq'    => 'Albanian',
			'ar'    => 'Arabic',
			'eu'    => 'Basque',
			'bn'    => 'Bengali',
			'bs'    => 'Bosnian',
			'bg'    => 'Bulgarian',
			'ca'    => 'Catalan',
			'zh-cn' => 'Chinese',
			'zh-tw' => 'Chinese-tw',
			'hr'    => 'Croatian',
			'cs'    => 'Czech',
			'da'    => 'Danish',
			'nl'    => 'Dutch',
			'en'    => 'English',
			'et'    => 'Estonian',
			'fi'    => 'Finnish',
			'fr'    => 'French',
			'gl'    => 'Galician',
			'ka'    => 'Georgian',
			'de'    => 'German',
			'el'    => 'Greek (Modern)',
			'he'    => 'Hebrew',
			'hi'    => 'Hindi',
			'hu'    => 'Hungarian',
			'is'    => 'Icelandic',
			'io'    => 'Ido',
			'id'    => 'Indonesian',
			'it'    => 'Italian',
			'ja'    => 'Japanese',
			'kk'    => 'Kazakh',
			'ko'    => 'Korean',
			'lv'    => 'Latvian',
			'lb'    => 'Letzeburgesch',
			'lt'    => 'Lithuanian',
			'lu'    => 'Luba-Katanga',
			'mk'    => 'Macedonian',
			'mg'    => 'Malagasy',
			'ms'    => 'Malay',
			'ro'    => 'Moldovan, Moldavian, Romanian',
			'nb'    => 'Norwegian Bokmål',
			'nn'    => 'Norwegian Nynorsk',
			'fa'    => 'Persian',
			'pl'    => 'Polish',
			'pt'    => 'Portuguese',
			'ru'    => 'Russian',
			'sr'    => 'Serbian',
			'sk'    => 'Slovak',
			'sl'    => 'Slovenian',
			'es'    => 'Spanish',
			'sv'    => 'Swedish',
			'tr'    => 'Turkish',
			'uk'    => 'Ukrainian',
			'vi'    => 'Vietnamese',
		];
	}

	/**
	 * Register widget content controls
	 */
	protected function register_content_controls() {

		$this->event_content_controls();

		$this->event_google_content_controls();

		$this->the_event_calendar_content_controls();

		$this->event_settings_content_controls();
	}

	protected function event_content_controls() {

		$this->start_controls_section(
			'_section_event',
			[
				'label' => __( 'Event', 'happy-elementor-addons' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'event_calendar_type',
			[
				'label'       => __( 'Source', 'happy-elementor-addons' ),
				'label_block' => false,
				'type'        => Controls_Manager::SELECT,
				'default'     => 'manual',
				'options'     => [
					'manual'              => __( 'Manual', 'happy-elementor-addons' ),
					'google_calendar'     => __( 'Google Calendar', 'happy-elementor-addons' ),
					'the_events_calendar' => __( 'The Event Calendar', 'happy-elementor-addons' ),
				],
				// 'multiple' => true,
			]
		);

		$repeater = new Repeater();
		$repeater->start_controls_tabs( 'event_calendar_tabs' );

		$repeater->start_controls_tab(
			'event_calendar_content_tab',
			[
				'label' => __( 'Content', 'happy-elementor-addons' ),
			]
		);

		$repeater->add_control(
			'title',
			[
				'label'       => __( 'Title', 'happy-elementor-addons' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'default'     => __( 'Event Title', 'happy-elementor-addons' ),
				'dynamic'     => [
					'active' => true,
				],
			]
		);

		$repeater->add_control(
			'guest',
			[
				'label'       => __( 'Guest/Speaker', 'happy-elementor-addons' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'default'     => __( 'John Doe', 'happy-elementor-addons' ),
				'dynamic'     => [
					'active' => true,
				],
			]
		);

		$repeater->add_control(
			'location',
			[
				'label'       => __( 'Location', 'happy-elementor-addons' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'default'     => __( '4382 Roosevelt Road, KS, Kansas', 'happy-elementor-addons' ),
				'dynamic'     => [
					'active' => true,
				],
			]
		);

		$repeater->add_control(
			'image',
			[
				'label'   => __( 'Choose Image', 'happy-elementor-addons' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => [
					'url' => Utils::get_placeholder_image_src(),
				],
			]
		);

		$repeater->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name'      => 'thumbnail',
				'label'     => 'Thumbnail Size',
				'default'   => 'thumbnail',
				'separator' => 'before',
				'exclude'   => [
					'custom',
				],
			]
		);

		$repeater->add_control(
			'details_link',
			[
				'label'         => __( 'Details Link', 'happy-elementor-addons' ),
				'type'          => Controls_Manager::URL,
				'placeholder'   => __( 'https://example.com', 'happy-elementor-addons' ),
				'options'       => [ 'is_external', 'nofollow', 'custom_attributes' ],
				'show_external' => true,
				// 'custom_attributes' => false,
			]
		);

		$repeater->add_control(
			'all_day',
			[
				'label'        => __( 'All Day', 'happy-elementor-addons' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_block'  => false,
				'return_value' => 'yes',
			]
		);

		$repeater->add_control(
			'start_date',
			[
				'label'     => __( 'Start Date', 'happy-elementor-addons' ),
				'type'      => Controls_Manager::DATE_TIME,
				'default'   => date( 'Y-m-d H:i', current_time( 'timestamp', 0 ) ),
				'condition' => [
					'all_day' => '',
				],
			]
		);

		$repeater->add_control(
			'end_date',
			[
				'label'     => __( 'End Date', 'happy-elementor-addons' ),
				'type'      => Controls_Manager::DATE_TIME,
				'default'   => date( 'Y-m-d H:i', strtotime( '+59 minute', current_time( 'timestamp', 0 ) ) ),
				'condition' => [
					'all_day' => '',
				],
			]
		);

		$repeater->add_control(
			'start_date_allday',
			[
				'label'          => __( 'Start Date', 'happy-elementor-addons' ),
				'type'           => Controls_Manager::DATE_TIME,
				'picker_options' => ['enableTime' => false],
				'default'        => date( 'Y-m-d', current_time( 'timestamp', 0 ) ),
				'condition'      => [
					'all_day' => 'yes',
				],
			]
		);

		$repeater->add_control(
			'end_date_allday',
			[
				'label'          => __( 'End Date', 'happy-elementor-addons' ),
				'type'           => Controls_Manager::DATE_TIME,
				'picker_options' => ['enableTime' => false],
				'default'        => date( 'Y-m-d', current_time( 'timestamp', 0 ) ),
				'condition'      => [
					'all_day' => 'yes',
				],
			]
		);

		$repeater->add_control(
			'individual_style',
			[
				'label'          => __( 'Individual Style?', 'happy-elementor-addons' ),
				'type'           => Controls_Manager::SWITCHER,
				'label_on'       => __( 'Yes', 'happy-elementor-addons' ),
				'label_off'      => __( 'No', 'happy-elementor-addons' ),
				'return_value'   => 'yes',
				'default'        => 'no',
				'style_transfer' => true,
				'separator'      => 'after',
			]
		);

		$repeater->add_control(
			'text_color',
			[
				'label'          => __( 'Text Color', 'happy-elementor-addons' ),
				'type'           => Controls_Manager::COLOR,
				'selectors'      => [
					'{{WRAPPER}} .ha-ec-wrapper {{CURRENT_ITEM}}' => 'color: {{VALUE}}!important;',
					'{{WRAPPER}} .ha-ec-wrapper {{CURRENT_ITEM}} .fc-event-main' => 'color: {{VALUE}}!important;',
				],
				'condition'      => [
					'individual_style' => 'yes',
				],
				'style_transfer' => true,
				// 'render_type' => 'template',
			]
		);

		$repeater->add_control(
			'bg_color',
			[
				'label'          => __( 'Background', 'happy-elementor-addons' ),
				'type'           => Controls_Manager::COLOR,
				'selectors'      => [
					'{{WRAPPER}} .ha-ec-wrapper {{CURRENT_ITEM}}' => 'background-color: {{VALUE}}!important;',
				],
				'condition'      => [
					'individual_style' => 'yes',
				],
				'style_transfer' => true,
			]
		);

		$repeater->add_control(
			'border_color',
			[
				'label'          => __( 'Dot Color', 'happy-elementor-addons' ),
				'type'           => Controls_Manager::COLOR,
				'selectors'      => [
					'{{WRAPPER}} .ha-ec-wrapper {{CURRENT_ITEM}} .fc-daygrid-event-dot' => 'border-color: {{VALUE}}!important;',
					'{{WRAPPER}} .ha-ec-wrapper {{CURRENT_ITEM}} .fc-list-event-dot' => 'border-color: {{VALUE}}!important;',
				],
				'condition'      => [
					'individual_style' => 'yes',
				],
				'style_transfer' => true,
			]
		);

		$repeater->end_controls_tab();

		$repeater->start_controls_tab(
			'event_calendar_description_tab',
			[
				'label' => __( 'Description', 'happy-elementor-addons' ),
			]
		);

		$repeater->add_control(
			'description',
			[
				'label'      => __( 'Description', 'happy-elementor-addons' ),
				'show_label' => true,
				'type'       => Controls_Manager::WYSIWYG,
				'default'    => sprintf(
					'<strong>%s</strong> %s',
					__( 'Lorem Ipsum', 'happy-elementor-addons' ),
					__( 'is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries', 'happy-elementor-addons' )
				),

				'default'    => __( '<strong>Lorem Ipsum</strong> is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries', 'happy-elementor-addons' ),
			]
		);

		$repeater->end_controls_tab();
		$repeater->end_controls_tabs();

		$this->add_control(
			'manual_event_list',
			[
				'show_label'  => false,
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ title }}}',
				'default'     => [
					[
						'title' => __( 'Event Title', 'happy-elementor-addons' ),
					],
				],
				'condition'   => [
					'event_calendar_type' => 'manual',
				],
			]
		);

		if ( ! class_exists( 'Tribe__Events__Main' ) ) {
			$this->add_control(
				'the_event_calendar_warning_text',
				[
					'type'      => Controls_Manager::RAW_HTML,
					'raw'       => sprintf(
						'<strong>%s</strong> %s <a href="plugin-install.php?s=the-events-calendar&tab=search&type=term" target="_blank">%s</a> %s',
						__( 'The Events Calendar', 'happy-elementor-addons' ),
						__( 'is not installed/activated on your site. Please install and activate.', 'happy-elementor-addons' ),
						__( 'The Events Calendar', 'happy-elementor-addons' ),
						__( ' first.', 'happy-elementor-addons' )
					),
					// 'content_classes' => 'ha-warning',
					'condition' => [
						'event_calendar_type' => 'the_events_calendar',
					],
				]
			);
		}
		$this->end_controls_section();
	}

	protected function event_google_content_controls() {

		$this->start_controls_section(
			'_section_event_google_calendar',
			[
				'label'     => __( 'Google Calendar', 'happy-elementor-addons' ),
				'tab'       => Controls_Manager::TAB_CONTENT,
				'condition' => [
					'event_calendar_type' => 'google_calendar',
				],
			]
		);

		$this->add_control(
			'google_calendar_api_key',
			[
				'label'       => __( 'API Key', 'happy-elementor-addons' ),
				'label_block' => true,
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'description' => sprintf( __( '<a href="https://docs.simplecalendar.io/google-api-key/" target="_blank">%s</a>', 'happy-elementor-addons' ), 'Get API Key' ),
			]
		);

		$this->add_control(
			'google_calendar_id',
			[
				'label'       => __( 'Calendar ID', 'happy-elementor-addons' ),
				'label_block' => true,
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				//'description' => sprintf(__('<a href="#" target="_blank">%s</a>','happy-elementor-addons'), 'Get calendar ID'),
			]
		);

		$this->add_control(
			'google_calendar_start_date',
			[
				'label'   => __( 'Start Date', 'happy-elementor-addons' ),
				'type'    => Controls_Manager::DATE_TIME,
				'default' => date( 'Y-m-d H:i', current_time( 'timestamp', 0 ) ),
			]
		);

		$this->add_control(
			'google_calendar_end_date',
			[
				'label'   => __( 'End Date', 'happy-elementor-addons' ),
				'type'    => Controls_Manager::DATE_TIME,
				'default' => date( 'Y-m-d H:i', strtotime( '+6 months', current_time( 'timestamp', 0 ) ) ),
			]
		);

		$this->add_control(
			'google_calendar_max_item',
			[
				'label'   => __( 'Number Of Events', 'happy-elementor-addons' ),
				'type'    => Controls_Manager::NUMBER,
				'min'     => 1,
				'default' => 100,
			]
		);

		$this->end_controls_section();
	}

	protected function the_event_calendar_content_controls() {

		//the events calendar
		if ( class_exists( 'Tribe__Events__Main' ) ) {

			$this->start_controls_section(
				'_section_the_events_calendar',
				[
					'label'     => __( 'The Event Calendar', 'happy-elementor-addons' ),
					'tab'       => Controls_Manager::TAB_CONTENT,
					'condition' => [
						'event_calendar_type' => 'the_events_calendar',
					],
				]
			);

			$this->add_control(
				'the_events_calendar_source',
				[
					'label'       => __( 'Get Events By', 'happy-elementor-addons' ),
					'type'        => Controls_Manager::SELECT,
					'label_block' => true,
					'default'     => ['all'],
					'options'     => [
						'all'            => __( 'All Event', 'happy-elementor-addons' ),
						'category'       => __( 'By Category', 'happy-elementor-addons' ),
						'selected_event' => __( 'Selected Event', 'happy-elementor-addons' ),
					],
					// 'render_type' => 'none',
				]
			);

			$this->add_control(
				'the_events_calendar_category',
				[
					'label'       => __( 'Event Category', 'happy-elementor-addons' ),
					'label_block' => true,
					'type'        => Controls_Manager::SELECT2,
					'multiple'    => true,
					'options'     => self::get_the_event_calendar_cat(),
					'condition'   => [
						'the_events_calendar_source' => 'category',
					],
				]
			);

			$this->add_control(
				'the_events_calendar_selected',
				[
					'label'       => __( 'Select Events', 'happy-elementor-addons' ),
					'label_block' => true,
					'type'        => Controls_Manager::SELECT2,
					'multiple'    => true,
					'options'     => self::get_events(),
					'condition'   => [
						'the_events_calendar_source' => 'selected_event',
					],
				]
			);

			$this->add_control(
				'the_events_calendar_item',
				[
					'label'     => __( 'Event Item', 'happy-elementor-addons' ),
					'type'      => Controls_Manager::NUMBER,
					'min'       => 1,
					'default'   => 12,
					'condition' => [
						'the_events_calendar_source!' => 'selected_event',
					],
				]
			);

			$this->end_controls_section();
		}

	}

	protected function event_settings_content_controls() {

		$this->start_controls_section(
			'_section_event_settings',
			[
				'label' => __( 'Settings', 'happy-elementor-addons' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'language',
			[
				'label'   => __( 'Language', 'happy-elementor-addons' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'manual'          => __( 'Manual', 'happy-elementor-addons' ),
					'google_calendar' => __( 'Google Calendar', 'happy-elementor-addons' ),
				],
				'options' => $this->language_code_list(),
				'default' => 'en',
			]
		);

		$this->add_control(
			'default_view',
			[
				'label'   => __( 'Calendar Default View', 'happy-elementor-addons' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'timeGridDay'  => __( 'Day', 'happy-elementor-addons' ),
					'timeGridWeek' => __( 'Week', 'happy-elementor-addons' ),
					'dayGridMonth' => __( 'Month', 'happy-elementor-addons' ),
					'listMonth'    => __( 'List', 'happy-elementor-addons' ),
				],
				'default' => 'dayGridMonth',
			]
		);

		$this->add_control(
			'event_calendar_first_day',
			[
				'label'   => __( 'First Day of Week', 'happy-elementor-addons' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'0' => __( 'Sunday', 'happy-elementor-addons' ),
					'1' => __( 'Monday', 'happy-elementor-addons' ),
					'2' => __( 'Tuesday', 'happy-elementor-addons' ),
					'3' => __( 'Wednesday', 'happy-elementor-addons' ),
					'4' => __( 'Thursday', 'happy-elementor-addons' ),
					'5' => __( 'Friday', 'happy-elementor-addons' ),
					'6' => __( 'Saturday', 'happy-elementor-addons' ),
				],
				'default' => '0',
			]
		);

		$this->add_control(
			'show_event_popup',
			[
				'label'        => __( 'Show Event Popup', 'happy-elementor-addons' ),
				'label_block'  => false,
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'allday_text',
			[
				'label'       => __( 'All Day Text', 'happy-elementor-addons' ),
				'label_block' => false,
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'All Day', 'happy-elementor-addons' ),
				'condition'   => [
					'show_event_popup' => 'yes',
				],
			]
		);

		$this->add_control(
			'readmore_text',
			[
				'label'       => __( 'Read More Text', 'happy-elementor-addons' ),
				'label_block' => false,
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Read More', 'happy-elementor-addons' ),
				'condition'   => [
					'show_event_popup' => 'yes',
				],
			]
		);

		$this->add_control(
			'time_title',
			[
				'label'       => __( 'Time Title', 'happy-elementor-addons' ),
				'label_block' => false,
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Timezone UTC+6', 'happy-elementor-addons' ),
				'condition'   => [
					'show_event_popup' => 'yes',
				],
			]
		);

		$this->add_control(
			'speaker_title',
			[
				'label'       => __( 'Speaker Title', 'happy-elementor-addons' ),
				'label_block' => false,
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Speaker', 'happy-elementor-addons' ),
				'condition'   => [
					'show_event_popup' => 'yes',
				],
			]
		);

		$this->add_control(
			'location_title',
			[
				'label'       => __( 'Location Title', 'happy-elementor-addons' ),
				'label_block' => false,
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Address', 'happy-elementor-addons' ),
				'condition'   => [
					'show_event_popup' => 'yes',
				],
			]
		);

		$this->end_controls_section();
	}



	/**
	 * Register widget style controls
	 */
	protected function register_style_controls() {

		$this->calendar_style_controls();

		$this->topbar_style_controls();

		$this->event_style_controls();

		$this->popup_style_controls();

	}

	protected function calendar_style_controls() {

		$this->start_controls_section(
			'_section_style_calendar_wrapper',
			[
				'label' => __( 'Calendar', 'happy-elementor-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'calendar_typography',
				'label'    => __( 'Calendar Font Family', 'happy-elementor-addons' ),
				'include'  => [
					'font_family',
				],
				'selector' => '{{WRAPPER}} .ha-ec-wrapper * :not(i),{{WRAPPER}} .ha-ec-popup-wrapper * :not(i)',
			]
		);

		$this->add_control(
			'calendar_background_color',
			[
				'label'     => __( 'Background', 'happy-elementor-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-ec-wrapper .fc-view > table' => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .ha-ec-wrapper .fc-view table.fc-list-table' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'calendar_border_color',
			[
				'label'     => __( 'Border Color', 'happy-elementor-addons' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#CFCFDA',
				'selectors' => [
					'{{WRAPPER}} .ha-ec-wrapper table thead:first-child tr:first-child th,
					{{WRAPPER}} .ha-ec-wrapper .fc-theme-standard .fc-scrollgrid,
					{{WRAPPER}} .ha-ec-wrapper .fc-theme-standard .fc-list,
					{{WRAPPER}} .ha-ec-wrapper .fc-theme-standard td,
					{{WRAPPER}} .ha-ec-wrapper .fc-theme-standard th' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'calendar_box_shadow',
				'label'    => __( 'Box Shadow', 'happy-elementor-addons' ),
				'selector' => '{{WRAPPER}} .ha-ec-wrapper .fc-view table.fc-scrollgrid,
				{{WRAPPER}} .ha-ec-wrapper .fc-view table.fc-list-table',
			]
		);

		$this->add_control(
			'calendar_todays_background',
			[
				'label'     => __( 'Today\'s Background', 'happy-elementor-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-ec-wrapper .fc .fc-daygrid-day.fc-day-today' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .ha-ec-wrapper .fc .fc-timegrid-col.fc-day-today' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'calendar_heading_heading',
			[
				'label'     => __( 'Calendar Heading', 'happy-elementor-addons' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'calendar_heading_padding',
			[
				'label'      => __( 'Padding', 'happy-elementor-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .ha-ec-wrapper th.fc-col-header-cell.fc-day' => 'Padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .ha-ec-wrapper .fc .fc-list-table th .fc-list-day-cushion' => 'Padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'calendar_heading_font_size',
			[
				'label'      => __( 'Font Size', 'happy-elementor-addons' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 200,
						'step' => 1,
					],
					'%'  => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .ha-ec-wrapper .fc .fc-list-table th .fc-list-day-cushion' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .ha-ec-wrapper th.fc-col-header-cell.fc-day' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'calendar_heading_color',
			[
				'label'     => __( 'Color', 'happy-elementor-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-ec-wrapper .fc .fc-list-table th .fc-list-day-cushion' => 'color: {{VALUE}};',
					'{{WRAPPER}} .ha-ec-wrapper th.fc-col-header-cell.fc-day' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'calendar_heading_background',
			[
				'label'     => __( 'Background', 'happy-elementor-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-ec-wrapper th.fc-col-header-cell.fc-day' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'calendar_date_and_time_heading',
			[
				'label'     => __( 'Date&Time', 'happy-elementor-addons' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'calendar_date_and_time_font_size',
			[
				'label'      => __( 'Font Size', 'happy-elementor-addons' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 200,
						'step' => 1,
					],
					'%'  => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .ha-ec-wrapper span.fc-timegrid-axis-cushion.fc-scrollgrid-shrink-cushion.fc-scrollgrid-sync-inner' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .ha-ec-wrapper .fc-timegrid-slot-label-cushion.fc-scrollgrid-shrink-cushion' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .ha-ec-wrapper .fc .fc-daygrid-day-top' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'calendar_text_color',
			[
				'label'     => __( 'Color', 'happy-elementor-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-ec-wrapper span.fc-timegrid-axis-cushion.fc-scrollgrid-shrink-cushion.fc-scrollgrid-sync-inner' => 'color: {{VALUE}};',
					'{{WRAPPER}} .ha-ec-wrapper .fc-timegrid-slot-label-cushion.fc-scrollgrid-shrink-cushion' => 'color: {{VALUE}};',
					'{{WRAPPER}} .ha-ec-wrapper .fc .fc-daygrid-day-top' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function topbar_style_controls() {

		$this->start_controls_section(
			'_section_style_calendar_topbar',
			[
				'label' => __( 'Top Bar', 'happy-elementor-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'topbar_margin_bottom',
			[
				'label'      => __( 'Margin Bottom', 'happy-elementor-addons' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 1000,
						'step' => 1,
					],
					'%'  => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .ha-ec-wrapper .fc-toolbar.fc-header-toolbar' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'topbar_background',
			[
				'label'     => __( 'Background', 'happy-elementor-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-ec-wrapper .fc-toolbar.fc-header-toolbar' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'topbar_title_heading',
			[
				'label'     => __( 'Title', 'happy-elementor-addons' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'topbar_title_typography',
				'label'    => __( 'Typography', 'happy-elementor-addons' ),
				'exclude'  => [
					'font_family',
				],
				'selector' => '{{WRAPPER}} .ha-ec-wrapper .fc-toolbar h2.fc-toolbar-title',
			]
		);

		$this->add_control(
			'topbar_title_color',
			[
				'label'     => __( 'Color', 'happy-elementor-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-ec-wrapper .fc-toolbar h2.fc-toolbar-title' => 'color: {{VALUE}};',
				],
			]
		);

		// Buttons style
		$this->add_control(
			'topbar_buttons_heading',
			[
				'label'     => __( 'Button', 'happy-elementor-addons' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'topbar_buttons_space',
			[
				'label'      => __( 'Space Between', 'happy-elementor-addons' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 1000,
						'step' => 1,
					],
					'%'  => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .ha-ec-wrapper .fc-toolbar.fc-header-toolbar button.fc-today-button' => 'margin-left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .ha-ec-wrapper .fc-toolbar.fc-header-toolbar .fc-button-group button:not(:first-child)' => 'margin-left: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'topbar_buttons_typography',
				'label'    => __( 'Typography', 'happy-elementor-addons' ),
				'exclude'  => [
					'font_family',
				],
				'selector' => '{{WRAPPER}} .ha-ec-wrapper .fc-toolbar.fc-header-toolbar button.fc-button',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'topbar_buttons_border',
				'label'    => __( 'Border', 'happy-elementor-addons' ),
				'exclude'  => ['color'], //remove border color
				'selector' => '{{WRAPPER}} .ha-ec-wrapper .fc-toolbar.fc-header-toolbar button.fc-button',
			]
		);

		$this->start_controls_tabs( 'calendar_buttons_style' );

		// Normal
		$this->start_controls_tab(
			'topbar_buttons_normal_state',
			[
				'label' => __( 'Normal', 'happy-elementor-addons' ),
			]
		);

		$this->add_control(
			'topbar_buttons_color_normal',
			[
				'label'     => __( 'Color', 'happy-elementor-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-ec-wrapper .fc-toolbar.fc-header-toolbar button.fc-button' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'topbar_buttons_background_normal',
			[
				'label'     => __( 'Background', 'happy-elementor-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-ec-wrapper .fc-toolbar.fc-header-toolbar button.fc-button' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'topbar_buttons_border_color_normal',
			[
				'label'     => __( 'Border Color', 'happy-elementor-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-ec-wrapper .fc-toolbar.fc-header-toolbar button.fc-button' => 'border-color: {{VALUE}}',
				],
			]
		);
		$this->end_controls_tab();

		// Hover
		$this->start_controls_tab(
			'topbar_buttons_hover_state',
			[
				'label' => __( 'Hover', 'happy-elementor-addons' ),
			]
		);

		$this->add_control(
			'topbar_buttons_color_hover',
			[
				'label'     => __( 'Color', 'happy-elementor-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-ec-wrapper .fc-toolbar.fc-header-toolbar button.fc-button:hover' => 'color: {{VALUE}};',
					'{{WRAPPER}} .ha-ec-wrapper .fc-toolbar.fc-header-toolbar button.fc-button.fc-button-active' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'topbar_buttons_background_hover',
			[
				'label'     => __( 'Background', 'happy-elementor-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-ec-wrapper .fc-toolbar.fc-header-toolbar button.fc-button:hover' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .ha-ec-wrapper .fc-toolbar.fc-header-toolbar button.fc-button.fc-button-active' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'topbar_buttons_border_color_hover',
			[
				'label'     => __( 'Border Color', 'happy-elementor-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-ec-wrapper .fc-toolbar.fc-header-toolbar button.fc-button:hover' => 'border-color: {{VALUE}}',
					'{{WRAPPER}} .ha-ec-wrapper .fc-toolbar.fc-header-toolbar button.fc-button.fc-button-active' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_responsive_control(
			'topbar_buttons_border_radius_normal',
			[
				'label'      => __( 'Border Radius', 'happy-elementor-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .ha-ec-wrapper .fc-toolbar.fc-header-toolbar button.fc-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function event_style_controls() {

		$this->start_controls_section(
			'_section_style_event',
			[
				'label' => __( 'Event', 'happy-elementor-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'event_item_font_size',
			[
				'label'      => __( 'Font Size', 'happy-elementor-addons' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 200,
						'step' => 1,
					],
					'%'  => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .ha-ec-wrapper .fc-daygrid-event' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .ha-ec-wrapper .fc-daygrid-event .fc-event-main' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .ha-ec-wrapper .fc-timegrid-event' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .ha-ec-wrapper .fc-timegrid-event .fc-event-main' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .ha-ec-wrapper .fc-list-event' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'event_item_color',
			[
				'label'     => __( 'Color', 'happy-elementor-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-ec-wrapper .fc-daygrid-event' => 'color: {{VALUE}};',
					'{{WRAPPER}} .ha-ec-wrapper .fc-daygrid-event .fc-event-main' => 'color: {{VALUE}};',
					'{{WRAPPER}} .ha-ec-wrapper .fc-timegrid-event' => 'color: {{VALUE}};',
					'{{WRAPPER}} .ha-ec-wrapper .fc-timegrid-event .fc-event-main' => 'color: {{VALUE}};',
					'{{WRAPPER}} .ha-ec-wrapper .fc-list-event' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'event_item_background',
			[
				'label'     => __( 'Background', 'happy-elementor-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-ec-wrapper .fc-daygrid-event' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .ha-ec-wrapper .fc-timegrid-event' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .ha-ec-wrapper .fc-list-event' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'event_item_dot_color',
			[
				'label'     => __( 'Dot Color', 'happy-elementor-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-ec-wrapper .fc-daygrid-event .fc-daygrid-event-dot' => 'border-color: {{VALUE}};',
					'{{WRAPPER}} .ha-ec-wrapper .fc-list-event .fc-list-event-dot' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function popup_style_controls() {

		$this->start_controls_section(
			'_section_style_event_popup',
			[
				'label' => __( 'Event Popup', 'happy-elementor-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'event_popup_wrapper_background',
			[
				'label'     => __( 'Wrapper Background', 'happy-elementor-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-ec-popup-wrapper.ha-ec-popup-ready:before' => 'background: {{VALUE}}',
				],
				'separator' => 'after',
			]
		);

		$this->add_responsive_control(
			'event_popup_width',
			[
				'label'      => __( 'Width', 'happy-elementor-addons' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 1200,
						'step' => 1,
					],
					'%'  => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .ha-ec-popup-wrapper .ha-ec-popup' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'event_popup_padding',
			[
				'label'      => __( 'Padding', 'happy-elementor-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .ha-ec-popup-wrapper .ha-ec-popup' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator'  => 'after',
			]
		);

		$this->add_control(
			'event_popup_background',
			[
				'label'     => __( 'Background', 'happy-elementor-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-ec-popup-wrapper .ha-ec-popup' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'event_popup_border_radius',
			[
				'label'      => __( 'Border Radius', 'happy-elementor-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .ha-ec-popup-wrapper .ha-ec-popup' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'event_popup_border',
				'label'    => __( 'Border', 'happy-elementor-addons' ),
				'selector' => '{{WRAPPER}} .ha-ec-popup-wrapper .ha-ec-popup',
			]
		);

		$this->add_control(
			'event_popup_image_heading',
			[
				'label'     => __( 'Image', 'happy-elementor-addons' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'event_popup_image_width',
			[
				'label'      => __( 'Width', 'happy-elementor-addons' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 500,
						'step' => 1,
					],
					'%'  => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .ha-ec-popup-wrapper' => '--ha-ec-popup-image-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'event_popup_image_border_radius',
			[
				'label'      => __( 'Border Radius', 'happy-elementor-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .ha-ec-popup-wrapper .ha-ec-popup .ha-ec-popup-image img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'event_popup_title_heading',
			[
				'label'     => __( 'Title', 'happy-elementor-addons' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'event_popup_title_margin_bottom',
			[
				'label'      => __( 'Margin Bottom', 'happy-elementor-addons' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .ha-ec-popup-wrapper .ha-ec-popup-content h3' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'event_popup_title_typography',
				'label'    => __( 'Typography', 'happy-elementor-addons' ),
				'exclude'  => [
					'font_family',
				],
				'selector' => '{{WRAPPER}} .ha-ec-popup-wrapper .ha-ec-popup-content h3',
			]
		);

		$this->add_control(
			'event_popup_title_color',
			[
				'label'     => __( 'Color', 'happy-elementor-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-ec-popup-wrapper .ha-ec-popup-content h3' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'event_popup_desc_heading',
			[
				'label'     => __( 'Description', 'happy-elementor-addons' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'event_popup_desc__margin_bottom',
			[
				'label'      => __( 'Margin Bottom', 'happy-elementor-addons' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .ha-ec-popup-wrapper p.ha-ec-popup-desc' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'event_popup_desc_typography',
				'label'    => __( 'Typography', 'happy-elementor-addons' ),
				'exclude'  => [
					'font_family',
				],
				'selector' => '{{WRAPPER}} .ha-ec-popup-wrapper p.ha-ec-popup-desc',
			]
		);

		$this->add_control(
			'event_popup_desc_color',
			[
				'label'     => __( 'Color', 'happy-elementor-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-ec-popup-wrapper p.ha-ec-popup-desc' => 'color: {{VALUE}}',
				],
			]
		);

		$this->popup_meta_style_controls();

		$this->add_control(
			'event_popup_readmore_heading',
			[
				'label'     => __( 'Read More', 'happy-elementor-addons' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'event_popup_readmore_typography',
				'label'    => __( 'Typography', 'happy-elementor-addons' ),
				'exclude'  => [
					'font_family',
				],
				'selector' => '{{WRAPPER}} .ha-ec-popup-wrapper a.ha-ec-popup-readmore-link',
			]
		);

		$this->add_control(
			'event_popup_readmore_color',
			[
				'label'     => __( 'Color', 'happy-elementor-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-ec-popup-wrapper a.ha-ec-popup-readmore-link' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'event_popup_close_btn_heading',
			[
				'label'     => __( 'Close Button', 'happy-elementor-addons' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'event_popup_close_btn_font_size',
			[
				'label'      => __( 'Icon Size', 'happy-elementor-addons' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 200,
						'step' => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .ha-ec-popup-wrapper .ha-ec-popup-close' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'event_popup_close_btn_color',
			[
				'label'     => __( 'Color', 'happy-elementor-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-ec-popup-wrapper .ha-ec-popup-close' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'event_popup_close_btn_background',
			[
				'label'     => __( 'Background', 'happy-elementor-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-ec-popup-wrapper .ha-ec-popup-close' => 'background: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'event_popup_close_btn_box_shadow',
				'label'    => __( 'Box Shadow', 'happy-elementor-addons' ),
				'selector' => '{{WRAPPER}} .ha-ec-popup-wrapper .ha-ec-popup-close',
			]
		);

		$this->end_controls_section();
	}

	protected function popup_meta_style_controls() {

		$this->add_control(
			'event_popup_meta_heading',
			[
				'label'     => __( 'Meta', 'happy-elementor-addons' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'event_popup_meta_item_margin',
			[
				'label'      => __( 'Item Margin', 'happy-elementor-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .ha-ec-popup-wrapper .ha-ec-popup-content ul li' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .ha-ec-popup-wrapper .ha-ec-popup-content ul li:last-child' => 'margin-right: 0;',
				],
			]
		);

		$this->add_control(
			'event_popup_meta_icon_heading',
			[
				'label'     => __( 'Meta Icon', 'happy-elementor-addons' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'event_popup_meta_icon_font_size',
			[
				'label'      => __( 'Icon Size', 'happy-elementor-addons' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 200,
						'step' => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .ha-ec-popup-wrapper .ha-ec-popup-content ul li .ha-ec-time-icon' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .ha-ec-popup-wrapper .ha-ec-popup-content ul li .ha-ec-guest-icon' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .ha-ec-popup-wrapper .ha-ec-popup-content ul li .ha-ec-location-icon' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'event_popup_meta_icon_color',
			[
				'label'     => __( 'Color', 'happy-elementor-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-ec-popup-wrapper .ha-ec-popup-content ul li .ha-ec-time-icon' => 'color: {{VALUE}}',
					'{{WRAPPER}} .ha-ec-popup-wrapper .ha-ec-popup-content ul li .ha-ec-guest-icon' => 'color: {{VALUE}}',
					'{{WRAPPER}} .ha-ec-popup-wrapper .ha-ec-popup-content ul li .ha-ec-location-icon' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'event_popup_meta_title_heading',
			[
				'label'     => __( 'Meta Title', 'happy-elementor-addons' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'event_popup_meta_title_margin_bottom',
			[
				'label'      => __( 'Margin Bottom', 'happy-elementor-addons' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .ha-ec-popup-wrapper .ha-ec-popup-content ul li .ha-ec-time-title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .ha-ec-popup-wrapper .ha-ec-popup-content ul li .ha-ec-guest-title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .ha-ec-popup-wrapper .ha-ec-popup-content ul li .ha-ec-location-title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'event_popup_meta_title_typography',
				'label'    => __( 'Typography', 'happy-elementor-addons' ),
				'exclude'  => [
					'font_family',
				],
				'selector' => '{{WRAPPER}} .ha-ec-popup-wrapper .ha-ec-popup-content ul li .ha-ec-time-title,{{WRAPPER}} .ha-ec-popup-wrapper .ha-ec-popup-content ul li .ha-ec-guest-title,{{WRAPPER}} .ha-ec-popup-wrapper .ha-ec-popup-content ul li .ha-ec-location-title',
			]
		);

		$this->add_control(
			'event_popup_meta_title_color',
			[
				'label'     => __( 'Color', 'happy-elementor-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-ec-popup-wrapper .ha-ec-popup-content ul li .ha-ec-time-title' => 'color: {{VALUE}}',
					'{{WRAPPER}} .ha-ec-popup-wrapper .ha-ec-popup-content ul li .ha-ec-guest-title' => 'color: {{VALUE}}',
					'{{WRAPPER}} .ha-ec-popup-wrapper .ha-ec-popup-content ul li .ha-ec-location-title' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'event_popup_meta_content_heading',
			[
				'label'     => __( 'Meta Content', 'happy-elementor-addons' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'event_popup_meta_content_typography',
				'label'    => __( 'Typography', 'happy-elementor-addons' ),
				'exclude'  => [
					'font_family',
				],
				'selector' => '{{WRAPPER}} .ha-ec-popup-wrapper .ha-ec-popup-content ul li .ha-ec-event-time,{{WRAPPER}} .ha-ec-popup-wrapper .ha-ec-popup-content ul li .ha-ec-event-guest,{{WRAPPER}} .ha-ec-popup-wrapper .ha-ec-popup-content ul li .ha-ec-event-location',
			]
		);

		$this->add_control(
			'event_popup_meta_content_color',
			[
				'label'     => __( 'Color', 'happy-elementor-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-ec-popup-wrapper .ha-ec-popup-content ul li .ha-ec-event-time' => 'color: {{VALUE}}',
					'{{WRAPPER}} .ha-ec-popup-wrapper .ha-ec-popup-content ul li .ha-ec-event-guest' => 'color: {{VALUE}}',
					'{{WRAPPER}} .ha-ec-popup-wrapper .ha-ec-popup-content ul li .ha-ec-event-location' => 'color: {{VALUE}}',
				],
			]
		);

	}


	protected function render() {

		$settings = $this->get_settings_for_display();

		if ( $settings['event_calendar_type'] == 'google_calendar' ) {
			$data = $this->get_google_calendar_events( $settings );
		} elseif ( $settings['event_calendar_type'] == 'the_events_calendar' ) {
			$data = $this->get_the_events_calendar_events( $settings );
		} else {
			$data = $this->get_manual_calendar_events( $settings );
		}

		$local        = $settings['language'];
		$default_view = $settings['default_view'];

		$this->add_render_attribute( 'wrapper', 'class', 'ha-ec-wrapper' );

		$this->add_render_attribute(
			'event-calendar',
			[
				'id'               => 'ha-ec-' . $this->get_id(),
				'class'            => 'ha-ec',
				'data-cal-id'      => $this->get_id(),
				'data-locale'      => esc_attr( $local ),
				'data-initialview' => esc_attr( $default_view ),
				'data-firstday'    => $settings['event_calendar_first_day'],
				'data-events'      => htmlspecialchars( json_encode( $data ), ENT_QUOTES, 'UTF-8' ),
				'data-show-popup'  => ! empty( $settings['show_event_popup'] ) ? esc_attr( $settings['show_event_popup'] ) : '',
				'data-allday-text' => ! empty( $settings['allday_text'] ) ? esc_html( $settings['allday_text'] ) : '',
			]
		);

		if ( $data ) :?>
			<div <?php $this->print_render_attribute_string( 'wrapper' ); ?>>
				<div <?php $this->print_render_attribute_string( 'event-calendar' ); ?>></div>
			</div>

			<?php
			if ( 'yes' === $settings['show_event_popup'] ) {
				$this->get_popup_markup( $settings );
			}
		endif;
	}

	public function get_manual_calendar_events( $settings ) {
		$events = $settings['manual_event_list'];

		$data = [];
		if ( $events ) {
			$i = 0;

			foreach ( $events as $event ) {

				if ( $event['all_day'] == 'yes' ) {
					$start = $event['start_date_allday'];
					$end   = date( 'Y-m-d', strtotime( '+1 days', strtotime( $event['end_date_allday'] ) ) );

					$colors['textColor']       = ! empty( $event['text_color'] ) ? $event['text_color'] : '';
					$colors['backgroundColor'] = ! empty( $event['bg_color'] ) ? $event['bg_color'] : '';

				} else {
					$start = $event['start_date'];
					$end   = date( 'Y-m-d H:i', strtotime( $event['end_date'] ) ) . ':01';
				}

				$image = ! empty( $event['image']['url'] ) ? esc_url( $event['image']['url'] ) : '';
				if ( ! empty( $event['image']['id'] ) ) {
					$image = esc_url( wp_get_attachment_image_url( $event['image']['id'], $event['thumbnail_size'] ) );
				}
				$details_link = ! empty( $event['details_link']['url'] ) ? esc_url( $event['details_link']['url'] ) : '';
				if ( 'yes' === $settings['show_event_popup'] && empty( $details_link ) ) {
					$details_link = '#';
				}

				$data[] = [
					'id'          => esc_attr( $i ),
					'classNames'  => 'elementor-repeater-item-' . esc_attr( $event['_id'] ),
					'title'       => ! empty( $event['title'] ) ? esc_html( $event['title'] ) : '',
					'description' => ha_kses_intermediate( $event['description'] ),
					'start'       => esc_html( $start ),
					'end'         => esc_html( $end ),
					'url'         => $details_link,
					'allDay'      => esc_html( $event['all_day'] ),
					'external'    => esc_attr( $event['details_link']['is_external'] ),
					'nofollow'    => esc_attr( $event['details_link']['nofollow'] ),
					'guest'       => esc_html( $event['guest'] ),
					'location'    => esc_html( $event['location'] ),
					'image'       => $image,
				];

				$i++;
			}
		}
		return $data;
	}


	public function get_google_calendar_events( $settings ) {

		if ( empty( $settings['google_calendar_api_key'] ) && empty( $settings['google_calendar_id'] ) ) {
			$message = __( 'Please input API key & Calendar ID.', 'happy-elementor-addons' );
			printf( '<span class="ha-ec-error-message">%1$s</span>', esc_html( $message ) );
			return [];
		}

		$calendar_id = urlencode( $settings['google_calendar_id'] );
		$base_url    = 'https://www.googleapis.com/calendar/v3/calendars/' . $calendar_id . '/events';

		$start_date = strtotime( $settings['google_calendar_start_date'] );
		$end_date   = strtotime( $settings['google_calendar_end_date'] );

		$arg = [
			'key'          => $settings['google_calendar_api_key'],
			'maxResults'   => $settings['google_calendar_max_item'],
			'timeMin'      => urlencode( date( 'c', $start_date ) ),
			'singleEvents' => 'true',
		];

		if ( ! empty( $end_date ) && $end_date > $start_date ) {
			$arg['timeMax'] = urlencode( date( 'c', $end_date ) );
		}

		$transient_key = 'ha_ec_google_calendar_' . md5( urlencode( $settings['google_calendar_id'] ) . implode( '', $arg ) );
		$data          = get_transient( $transient_key );

		if ( false === $data ) {
			$data = wp_remote_retrieve_body( wp_remote_get( add_query_arg( $arg, $base_url ) ) );

			// if( is_object( json_decode($data) ) && !array_key_exists('error', json_decode($data) ) )
			if ( is_object( json_decode( $data ) ) && ! property_exists( json_decode( $data ), 'error' ) ) {
				// echo 'cacheeed';
				// set_transient($transient_key, $data, 1 * HOUR_IN_SECONDS);
				set_transient( $transient_key, $data, 10 * MINUTE_IN_SECONDS );
			}
		}

		if ( is_object( json_decode( $data ) ) && property_exists( json_decode( $data ), 'error' ) ) {
			$message = __( 'Please input valid API key & Calendar ID.', 'happy-elementor-addons' );
			printf( '<span class="ha-ec-error-message">%1$s</span>', esc_html( $message ) );
			return [];
		}

		$data = false !== $data ? json_decode( $data ) : '';

		$calendar_data = [];
		if ( isset( $data->items ) ) {

			foreach ( $data->items as $key => $item ) {

				if ( $item->status !== 'confirmed' ) {
					continue;
				}

				$all_day = '';

				if ( isset( $item->start->date ) ) {
					$all_day       = 'yes';
					$ev_start_date = $item->start->date;
					$ev_end_date   = $item->end->date;
				} else {
					$ev_start_date = $item->start->dateTime;
					$ev_end_date   = $item->end->dateTime;
				}

				$calendar_data[] = [
					'id'          => esc_attr( ++$key ),
					'title'       => ! empty( $item->summary ) ? esc_html( $item->summary ) : 'No Title',
					'description' => isset( $item->description ) ? ha_kses_intermediate( $item->description ) : '',
					'start'       => esc_html( $ev_start_date ),
					'end'         => esc_html( $ev_end_date ),
					'url'         => ! empty( $item->htmlLink ) ? esc_url( $item->htmlLink ) : '',
					'allDay'      => esc_html( $all_day ),
					'external'    => 'on',
					'nofollow'    => 'on',
					'guest'       => ! empty( $item->creator->displayName ) ? esc_html( $item->creator->displayName ) : '',
					'location'    => ! empty( $item->location ) ? esc_html( $item->location ) : '',
				];
			}
		}

		return $calendar_data;
	}


	public function get_the_events_calendar_events( $settings ) {

		if ( ! function_exists( 'tribe_get_events' ) ) {
			return [];
		}

		if ( 'selected_event' !== $settings['the_events_calendar_source'] ) {
			$arg = [
				'posts_per_page' => $settings['the_events_calendar_item'],
			];
		}

		if ( 'category' == $settings['the_events_calendar_source'] && ! empty( $settings['the_events_calendar_category'] ) ) {
			$arg['tax_query'] = [
				[
					'taxonomy' => 'tribe_events_cat',
					'field'    => 'id',
					'terms'    => $settings['the_events_calendar_category'],
				],
			];
		}

		if ( 'selected_event' == $settings['the_events_calendar_source'] && ! empty( $settings['the_events_calendar_selected'] ) ) {
			$arg['post__in'] = $settings['the_events_calendar_selected'];
		}

		$events = tribe_get_events( $arg );

		if ( empty( $events ) ) {
			return [];
		}

		$calendar_data = [];
		foreach ( $events as $key => $event ) {

			$date_format = 'Y-m-d';
			$all_day     = 'yes';

			if ( ! tribe_event_is_all_day( $event->ID ) ) {
				$date_format .= ' H:i';
				$all_day      = '';
			}

			$image = get_the_post_thumbnail_url( $event->ID );

			$calendar_data[] = [
				'id'          => esc_attr( ++$key ),
				'title'       => ! empty( $event->post_title ) ? esc_html( $event->post_title ) : '',
				'description' => ha_kses_intermediate( $event->post_content ),
				'start'       => esc_html( tribe_get_start_date( $event->ID, true, $date_format ) ),
				'end'         => esc_html( tribe_get_end_date( $event->ID, true, $date_format ) ),
				'url'         => ! empty( get_the_permalink( $event->ID ) ) ? esc_url( get_the_permalink( $event->ID ) ) : '',
				'allDay'      => esc_html( $all_day ),
				'external'    => 'on',
				'nofollow'    => 'on',
				'guest'       => esc_html( tribe_get_organizer( $event->ID ) ),
				'location'    => ! empty( tribe_get_venue( $event->ID ) ) ? esc_html( tribe_get_venue( $event->ID ) ) : '',
				'image'       => $image ? esc_url( $image ) : '',
			];
		}
		return $calendar_data;

	}

	public function get_popup_markup( $settings ) {
		$readmore_text  = ! empty( $settings['readmore_text'] ) ? esc_html( $settings['readmore_text'] ) : '';
		$time_title     = ! empty( $settings['time_title'] ) ? esc_html( $settings['time_title'] ) : '';
		$speaker_title  = ! empty( $settings['speaker_title'] ) ? esc_html( $settings['speaker_title'] ) : '';
		$location_title = ! empty( $settings['location_title'] ) ? esc_html( $settings['location_title'] ) : '';
		$popup          = '<div class="ha-ec-popup-wrapper">
					<div class="ha-ec-popup">

						<span class="ha-ec-popup-close"><i class="eicon-editor-close"></i></span>
						<div class="ha-ec-popup-body-wrap">
						<div class="ha-ec-popup-body">
							<div class="ha-ec-popup-image">
								<img src="" alt="">
							</div>
							<div class="ha-ec-popup-content">
								<ul>
									<li class="ha-ec-event-time-wrap">
										<div class="ha-ec-time-icon">' . $this->render_svg_icon( 'clock' ) . '</div>
										<div class="ha-ec-time-content">
											<span class="ha-ec-time-title">' . $time_title . '</span>
											<span class="ha-ec-event-time"></span>
										</div>
									</li>
									<li class="ha-ec-event-guest-wrap">
										<div class="ha-ec-guest-icon">' . $this->render_svg_icon( 'speaker' ) . '</div>
										<div class="ha-ec-guest-content">
											<span class="ha-ec-guest-title">' . $speaker_title . '</span>
											<span class="ha-ec-event-guest"></span>
										</div>
									</li>
									<li class="ha-ec-event-location-wrap">
										<div class="ha-ec-location-icon">' . $this->render_svg_icon( 'map' ) . '</div>
										<div class="ha-ec-location-content">
											<span class="ha-ec-location-title">' . $location_title . '</span>
											<span class="ha-ec-event-location"></span>
										</div>
									</li>
								</ul>
								<h3 class="ha-ec-event-title"></h3>
								<p class="ha-ec-popup-desc"></p>
								<div class="ha-ec-popup-readmore">
									<a class="ha-ec-popup-readmore-link" href="">' . $readmore_text . '</a>
								</div>
							</div>
						</div>
						</div>

					</div>
				</div>';
		echo $popup;
	}

	public function render_svg_icon( $icon_name ) {
		?>
			<?php if ( 'clock' === $icon_name ) :
				return '<svg xmlns="http://www.w3.org/2000/svg" height="512pt" viewBox="0 0 512 512" width="512pt"><path d="m256 0c-141.164062 0-256 114.835938-256 256s114.835938 256 256 256 256-114.835938 256-256-114.835938-256-256-256zm121.75 388.414062c-4.160156 4.160157-9.621094 6.253907-15.082031 6.253907-5.460938 0-10.925781-2.09375-15.082031-6.253907l-106.667969-106.664062c-4.011719-3.988281-6.25-9.410156-6.25-15.082031v-138.667969c0-11.796875 9.554687-21.332031 21.332031-21.332031s21.332031 9.535156 21.332031 21.332031v129.835938l100.417969 100.414062c8.339844 8.34375 8.339844 21.824219 0 30.164062zm0 0"/></svg>';
			endif; ?>
			<?php if ( 'user' === $icon_name ) :
				return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="-42 0 512 512.002"><path d="m210.351562 246.632812c33.882813 0 63.222657-12.152343 87.195313-36.128906 23.972656-23.972656 36.125-53.304687 36.125-87.191406 0-33.875-12.152344-63.210938-36.128906-87.191406-23.976563-23.96875-53.3125-36.121094-87.191407-36.121094-33.886718 0-63.21875 12.152344-87.191406 36.125s-36.128906 53.308594-36.128906 87.1875c0 33.886719 12.15625 63.222656 36.132812 87.195312 23.976563 23.96875 53.3125 36.125 87.1875 36.125zm0 0"/><path d="m426.128906 393.703125c-.691406-9.976563-2.089844-20.859375-4.148437-32.351563-2.078125-11.578124-4.753907-22.523437-7.957031-32.527343-3.308594-10.339844-7.808594-20.550781-13.371094-30.335938-5.773438-10.15625-12.554688-19-20.164063-26.277343-7.957031-7.613282-17.699219-13.734376-28.964843-18.199219-11.226563-4.441407-23.667969-6.691407-36.976563-6.691407-5.226563 0-10.28125 2.144532-20.042969 8.5-6.007812 3.917969-13.035156 8.449219-20.878906 13.460938-6.707031 4.273438-15.792969 8.277344-27.015625 11.902344-10.949219 3.542968-22.066406 5.339844-33.039063 5.339844-10.972656 0-22.085937-1.796876-33.046874-5.339844-11.210938-3.621094-20.296876-7.625-26.996094-11.898438-7.769532-4.964844-14.800782-9.496094-20.898438-13.46875-9.75-6.355468-14.808594-8.5-20.035156-8.5-13.3125 0-25.75 2.253906-36.972656 6.699219-11.257813 4.457031-21.003906 10.578125-28.96875 18.199219-7.605469 7.28125-14.390625 16.121094-20.15625 26.273437-5.558594 9.785157-10.058594 19.992188-13.371094 30.339844-3.199219 10.003906-5.875 20.945313-7.953125 32.523437-2.058594 11.476563-3.457031 22.363282-4.148437 32.363282-.679688 9.796875-1.023438 19.964844-1.023438 30.234375 0 26.726562 8.496094 48.363281 25.25 64.320312 16.546875 15.746094 38.441406 23.734375 65.066406 23.734375h246.53125c26.625 0 48.511719-7.984375 65.0625-23.734375 16.757813-15.945312 25.253906-37.585937 25.253906-64.324219-.003906-10.316406-.351562-20.492187-1.035156-30.242187zm0 0"/></svg>';
			endif; ?>
			<?php if ( 'speaker' === $icon_name ) :
				return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 841.9 595.3">
				<path d="M701.7,430.3c-5.1-8.3-14-13.2-23.7-13.2h-20.1c-5.3,0-9.6-4.1-10-9.4c-3.8-58.2-40.7-69-77.7-79.8  c-33.5-9.8-54.5-29.6-65.9-62.4c-1.3-3.7-4.8-6.2-8.8-6.2h-12.9c-0.5-10.8,1-31.6,17.3-49.9c12.7,0.8,23-10.5,27.1-22.2  c1.4-3.2,13-30.5,3.9-51.4c19.2-52-6-76.3-17.5-84.3c-5.9-51.7-87.2-51.3-91-51.4c-38.1,1.5-66.6,14.8-84.8,39.7  c-24.6,33.5-22.6,78.6-19.5,97.7c-11,11.3-2.8,34.1,2.3,48.5c0.9,2.5,1.7,4.7,2.3,6.7c2.9,9.1,7.9,15.5,14.7,18.8  c4,2,7.8,2.4,11.1,2.5c5.3,7.1,16.1,24,18.6,45.4h-4.8c-4,0-7.5,2.5-8.8,6.2c-10.9,31.2-34,52.2-68.7,62.4l-4.7,1.4  c-35.9,10.4-76.5,22.4-76.5,87.8c0,0.1-8.5,0-9.4,0h-32c-9.7,0-18.6,4.9-23.7,13.2c-5.1,8.3-5.6,18.4-1.2,27.1l37.2,74.3  c4.8,9.5,14.3,15.4,24.9,15.4h75.2c0.1,0,0.1,0,0.2,0l16.7,43.1c1.9,4.8,7.2,7.2,12.1,5.3c4.8-1.9,7.2-7.2,5.3-12l-27.4-70.7  c-1.1-2.9-0.8-6,1-8.6c1.7-2.5,4.5-4,7.6-4h260c3.1,0,5.9,1.5,7.6,4c1.7,2.6,2.1,5.7,1,8.6l-27.4,70.7c-1.9,4.8,0.5,10.2,5.3,12  c1.1,0.4,2.2,0.6,3.4,0.6c3.7,0,7.3-2.3,8.7-5.9l16.7-43h75.4c10.6,0,20.2-5.9,24.9-15.4l37.1-74.3  C707.2,448.7,706.8,438.6,701.7,430.3z M686.2,449.1l-37.2,74.3c-1.6,3.1-4.8,5.1-8.3,5.1h-68.2l3.5-9c3.3-8.6,2.2-18.2-3-25.9  c-5.2-7.6-13.8-12.1-23-12.1h-260c-9.2,0-17.8,4.5-23,12.1c-5.2,7.6-6.3,17.3-3,25.9l3.5,9h-68.2c-3.5,0-6.7-2-8.3-5.1l-37.1-74.3  c-2.1-4.1-0.4-7.7,0.4-9c0.8-1.3,3.2-4.4,7.9-4.4h254.3v-33.4c0-4.3-3.5-7.8-7.8-7.8h0c-11,0-20-9-20-20v-3c0-2.8,2.2-5,5-5h21.2  c2.7,0,5.1-2,5.3-4.6c0.3-3.1-2.1-5.6-5.1-5.6h-21.2c-2.8,0-5.1-2.3-5.1-5.1v0c0-2.8,2.3-5.1,5.1-5.1h21c2.7,0,5.1-2,5.3-4.6  c0.3-3.1-2.1-5.6-5.1-5.6h-21.2c-2.8,0-5.1-2.3-5.1-5.1l0,0c0-2.8,2.3-5.1,5.1-5.1h21c2.7,0,5.1-2,5.3-4.6c0.3-3.1-2.1-5.6-5.1-5.6  h-21.4c-2.8,0-5-2.2-5-5v-4.4c0-11,9-20,20-20H443c11,0,20,9,20,20v68.8c0,11-9,20-20,20h-0.1c-4.3,0-7.8,3.5-7.8,7.8v33.4h242.8  c4.7,0,7.1,3.1,7.9,4.4C686.6,441.4,688.3,444.9,686.2,449.1z"/>
				</svg>';
			endif; ?>
			<?php if ( 'map' === $icon_name ) :
				return '<svg xmlns="http://www.w3.org/2000/svg" id="Layer_1" enable-background="new 0 0 512 512" height="512" viewBox="0 0 512 512" width="512"><g><path d="m407.579 87.677c-31.073-53.624-86.265-86.385-147.64-87.637-2.62-.054-5.257-.054-7.878 0-61.374 1.252-116.566 34.013-147.64 87.637-31.762 54.812-32.631 120.652-2.325 176.123l126.963 232.387c.057.103.114.206.173.308 5.586 9.709 15.593 15.505 26.77 15.505 11.176 0 21.183-5.797 26.768-15.505.059-.102.116-.205.173-.308l126.963-232.387c30.304-55.471 29.435-121.311-2.327-176.123zm-151.579 144.323c-39.701 0-72-32.299-72-72s32.299-72 72-72 72 32.299 72 72-32.298 72-72 72z"/></g></svg>';
			endif; ?>
		<?php
	}

}
