<?php

namespace ElementPack\Modules\NewsTicker\Widgets;

use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use ElementPack\Includes\Controls\GroupQuery\Group_Control_Query;
use ElementPack\Traits\Global_Widget_Controls;
use WP_Query;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

/**
 * Class News Ticker
 */
class News_Ticker extends Module_Base {
	use Group_Control_Query;
	use Global_Widget_Controls;

	/**
	 * @var \WP_Query
	 */
	private $_query = null;

	public function get_name() {
		return 'bdt-news-ticker';
	}

	public function get_title() {
		return BDTEP . esc_html__('News Ticker', 'bdthemes-element-pack');
	}

	public function get_icon() {
		return 'bdt-wi-news-ticker';
	}

	public function get_categories() {
		return ['element-pack'];
	}

	public function get_keywords() {
		return ['news', 'ticker', 'report', 'message', 'information', 'blog'];
	}

	public function get_style_depends() {
		if ($this->ep_is_edit_mode()) {
			return ['ep-styles'];
		} else {
			return ['ep-news-ticker'];
		}
	}

	public function get_script_depends() {
		if ($this->ep_is_edit_mode()) {
			return ['ep-scripts'];
		} else {
			return ['ep-news-ticker'];
		}
	}

	public function get_custom_help_url() {
		return 'https://youtu.be/FmpFhNTR7uY';
	}

	public function get_query() {
		return $this->_query;
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_content_layout',
			[
				'label' => esc_html__('Layout', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'show_label',
			[
				'label'   => esc_html__('Label', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes'
			]
		);

		$this->add_control(
			'news_label',
			[
				'label'       => esc_html__('Label', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => ['active' => true],
				'default'     => esc_html__('LATEST NEWS', 'bdthemes-element-pack'),
				'placeholder' => esc_html__('LATEST NEWS', 'bdthemes-element-pack'),
				'condition' => [
					'show_label' => 'yes'
				]
			]
		);

		$this->add_control(
			'news_content',
			[
				'label'   => esc_html__('News Content', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'title',
				'options' => [
					'title'   => esc_html__('Title', 'bdthemes-element-pack'),
					'excerpt' => esc_html__('Excerpt', 'bdthemes-element-pack'),
				],
			]
		);

		$this->add_control(
			'show_date',
			[
				'label'     => esc_html__('Date', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => [
					'news_content' => 'title'
				],
			]
		);

		$this->add_control(
			'date_reverse',
			[
				'label'     => esc_html__('Date Reverse', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => [
					'show_date' => 'yes'
				],
			]
		);

		$this->add_control(
			'show_time',
			[
				'label'     => esc_html__('Time', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => [
					'news_content' => 'title'
				],
			]
		);

		$this->add_responsive_control(
			'news_ticker_height',
			[
				'label'   => __('Height', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 42,
				],
				'range' => [
					'px' => [
						'min' => 25,
						'max' => 200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-news-ticker' => 'height: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_content_navigation',
			[
				'label' => esc_html__('Navigation', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'show_navigation',
			[
				'label'   => esc_html__('Navigation', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes'
			]
		);

		$this->add_control(
			'play_pause',
			[
				'label'   => esc_html__('Play/Pause Button', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'navigation_size',
			[
				'label'   => esc_html__('Navigation Size', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 14,
				],
				'range' => [
					'px' => [
						'min' => 3,
						'max' => 26,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-news-ticker .bdt-news-ticker-navigation svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'show_navigation' => 'yes'
				]
			]
		);

		$this->end_controls_section();
		//New Query Builder Settings
		$this->start_controls_section(
			'section_post_query_builder',
			[
				'label' => __('Query', 'bdthemes-element-pack'),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->register_query_builder_controls();

		$this->update_control(
			'posts_per_page',
			[
				'default' => 5,
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_animation',
			[
				'label' => esc_html__('Animation', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'slider_animations',
			[
				'label'     => esc_html__('Animations', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'fade',
				'options'   => [
					'scroll'  	  => esc_html__('Scroll', 'bdthemes-element-pack'),
					'slide-left'  => esc_html__('Slide Left', 'bdthemes-element-pack'),
					'slide-up'    => esc_html__('Slide Up', 'bdthemes-element-pack'),
					'slide-right' => esc_html__('Slide Right', 'bdthemes-element-pack'),
					'slide-down'  => esc_html__('Slide Down', 'bdthemes-element-pack'),
					'fade'        => esc_html__('Fade', 'bdthemes-element-pack'),
					'typography'  => esc_html__('Typography', 'bdthemes-element-pack'),
				],
			]
		);

		$this->add_control(
			'autoplay',
			[
				'label'   => esc_html__('Autoplay', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);


		$this->add_control(
			'autoplay_interval',
			[
				'label'     => esc_html__('Autoplay Interval', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 5000,
				'condition' => [
					'autoplay' => 'yes',
				],
			]
		);

		$this->add_control(
			'pause_on_hover',
			[
				'label'   => esc_html__('Pause on Hover', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'speed',
			[
				'label'              => esc_html__('Animation Speed', 'bdthemes-element-pack'),
				'type'               => Controls_Manager::NUMBER,
				'default'            => 500,
			]
		);

		$this->add_control(
			'scroll_speed',
			[
				'label'   => __('Scroll Speed', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 1,
				],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 10,
					],
				],
				'condition' => [
					'slider_animations' => 'scroll',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_news_ticker',
			[
				'label'     => esc_html__('News Ticker', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'heading_label',
			[
				'label'     => esc_html__('Label', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::HEADING,
				'condition' => [
					'show_label' => 'yes'
				]
			]
		);

		$this->add_control(
			'label_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'separator' => 'before',
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-news-ticker .bdt-news-ticker-label-inner' => 'color: {{VALUE}};',
				],
				'condition' => [
					'show_label' => 'yes'
				]
			]
		);

		$border_side = is_rtl() ? 'right' : 'left';

		$this->add_control(
			'label_background',
			[
				'label'     => esc_html__('Background', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-news-ticker .bdt-news-ticker-label'       => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .bdt-news-ticker .bdt-news-ticker-label:after' => 'border-' . $border_side . '-color: {{VALUE}};',
				],
				'condition' => [
					'show_label' => 'yes'
				]
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'label_typography',
				'label'     => esc_html__('Typography', 'bdthemes-element-pack'),
				//'scheme'    => Schemes\Typography::TYPOGRAPHY_1,
				'selector'  => '{{WRAPPER}} .bdt-news-ticker .bdt-news-ticker-label-inner',
				'condition' => [
					'show_label' => 'yes'
				]
			]
		);

		$this->add_control(
			'heading_content',
			[
				'label' => esc_html__('Content', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::HEADING,
			]
		);

		$this->add_control(
			'content_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'separator' => 'before',
				'selectors' => [
					'{{WRAPPER}} .bdt-news-ticker .bdt-news-ticker-content a' => 'color: {{VALUE}};',
					'{{WRAPPER}} .bdt-news-ticker .bdt-news-ticker-content span' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'content_background',
			[
				'label'     => esc_html__('Background', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-news-ticker'     => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .bdt-news-ticker .bdt-news-ticker-content:before, {{WRAPPER}} .bdt-news-ticker .bdt-news-ticker-content:after'     => 'box-shadow: 0 0 12px 12px {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'content_typography',
				'label'    => esc_html__('Typography', 'bdthemes-element-pack'),
				'selector' => '{{WRAPPER}} .bdt-news-ticker .bdt-news-ticker-content',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_navigation',
			[
				'label'     => esc_html__('Navigation', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_navigation' => 'yes'
				]
			]
		);

		$this->add_control(
			'navigation_background',
			[
				'label'     => esc_html__('Navigation Background', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-news-ticker .bdt-news-ticker-navigation' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->start_controls_tabs('tabs_arrow_style');

		$this->start_controls_tab(
			'tab_arrow_normal',
			[
				'label' => esc_html__('Normal', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'navigation_color',
			[
				'label'     => esc_html__('Navigation Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-news-ticker .bdt-news-ticker-navigation button span svg' => 'color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_arrow_hover',
			[
				'label' => esc_html__('Hover', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'hover_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-news-ticker .bdt-news-ticker-navigation button:hover span svg' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Main query render for this widget
	 *
	 * @param $posts_per_page number item query limit
	 */
	public function query_posts($posts_per_page) {
		$settings = $this->get_settings_for_display();
		$default = $this->getGroupControlQueryArgs();
		if ($posts_per_page) {
			$args['posts_per_page'] = $posts_per_page;
			$args['paged']  = max(1, get_query_var('paged'), get_query_var('page'));
		}
		$args         = array_merge($default, $args);
		$this->_query = new WP_Query($args);
	}

	public function render() {
		$settings = $this->get_settings_for_display();

		// TODO need to delete after v6.5
		if (isset($settings['posts_limit']) and $settings['posts_per_page'] == 6) {
			$limit = $settings['posts_limit'];
		} else {
			$limit = $settings['posts_per_page'];
		}

		$this->query_posts($limit);

		$wp_query = $this->get_query();

		if (!$wp_query->found_posts) {
			return;
		}

		$this->render_header($settings);

		while ($wp_query->have_posts()) {
			$wp_query->the_post();

			$this->render_loop_item($settings);
		}

		$this->render_footer($settings);

		wp_reset_postdata();
	}

	protected function render_title() {
		$classes = ['bdt-news-ticker-content-title'];
?>

		<a href="<?php echo esc_url(get_permalink()); ?>">
			<?php $this->render_date(); ?>

			<?php $this->render_time(); ?>

			<?php the_title() ?>
		</a>
	<?php
	}


	protected function render_excerpt() {

	?>
		<a href="<?php echo esc_url(get_permalink()); ?>">
			<?php the_excerpt(); ?>
		</a>
	<?php
	}

	protected function render_header($settings) {

		$this->add_render_attribute(
			[
				'slider-settings' => [
					'class' => [
						'bdt-news-ticker',
					],
					'data-settings' => [
						wp_json_encode(array_filter([
							"effect"       => $settings["slider_animations"],
							"autoPlay"     => ($settings["autoplay"]) ? true : false,
							"interval"     => $settings["autoplay_interval"],
							"pauseOnHover" => ($settings["pause_on_hover"]) ? true : false,
							"scrollSpeed"  => (isset($settings["scroll_speed"]["size"]) ?  $settings["scroll_speed"]["size"] : 1),
							"direction"    => (is_rtl()) ? 'rtl' : false
						]))
					],
				]
			]
		);

	?>
		<div id="newsTicker1" <?php echo $this->get_render_attribute_string('slider-settings'); ?>>
			<?php if ('yes' == $settings['show_label']) : ?>
				<div class="bdt-news-ticker-label">
					<div class="bdt-news-ticker-label-inner">
						<?php echo wp_kses($settings['news_label'], element_pack_allow_tags('title')); ?>
					</div>
				</div>
			<?php endif; ?>
			<div class="bdt-news-ticker-content">
				<ul>
				<?php
			}

			public function render_date() {
				$settings = $this->get_settings_for_display();

				if (!$this->get_settings('show_date')) {
					return;
				}

				$news_month = get_the_date('m');
				$news_day = get_the_date('d');

				?>

					<span class="bdt-news-ticker-date bdt-margin-small-right" title="<?php esc_html_e('Published on:', 'bdthemes-element-pack'); ?> <?php echo get_the_date(); ?>">
						<?php if ('yes' == $settings['date_reverse']) : ?>
							<span class="bdt-news-ticker-date-day"><?php echo esc_attr($news_day); ?></span>
							<span class="bdt-news-ticker-date-sep">/</span>
							<span class="bdt-news-ticker-date-month"><?php echo esc_attr($news_month); ?></span>
						<?php else : ?>
							<span class="bdt-news-ticker-date-month"><?php echo esc_attr($news_month); ?></span>
							<span class="bdt-news-ticker-date-sep">/</span>
							<span class="bdt-news-ticker-date-day"><?php echo esc_attr($news_day); ?></span>
						<?php endif; ?>
						<span>:</span>
					</span>

				<?php
			}

			public function render_time() {

				if (!$this->get_settings('show_time')) {
					return;
				}

				$news_hour = get_the_time();

				?>

					<span class="bdt-news-ticker-time bdt-margin-small-right" title="<?php esc_html_e('Published on:', 'bdthemes-element-pack'); ?> <?php echo get_the_date(); ?> <?php echo get_the_time(); ?>">
						<span class="bdt-text-uppercase"><?php echo esc_attr($news_hour); ?></span>
						<span>:</span>
					</span>

				<?php
			}

			protected function render_footer($settings) {
				?>


				</ul>
			</div>
			<?php if ($settings['show_navigation']) : ?>
				<div class="bdt-news-ticker-controls bdt-news-ticker-navigation">

					<button class="bdt-visible@m">
						<span class="bdt-news-ticker-arrow bdt-news-ticker-prev bdt-icon">
							<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg" data-svg="chevron-left">
								<polyline fill="none" stroke="#000" stroke-width="1.03" points="13 16 7 10 13 4"></polyline>
							</svg>
						</span>
					</button>

					<?php if ($settings['play_pause']) : ?>
						<button class="bdt-visible@m">
							<span class="bdt-news-ticker-action bdt-icon">
								<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg" data-svg="play" class="bdt-news-ticker-play-pause">
									<polygon fill="none" stroke="#000" points="4.9,3 16.1,10 4.9,17 "></polygon>

									<rect x="6" y="2" width="1" height="16" />
									<rect x="13" y="2" width="1" height="16" />
								</svg>
							</span>
						</button>
					<?php endif ?>

					<button>
						<span class="bdt-news-ticker-arrow bdt-news-ticker-next bdt-icon">
							<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg" data-svg="chevron-right">
								<polyline fill="none" stroke="#000" stroke-width="1.03" points="7 4 13 10 7 16"></polyline>
							</svg>
						</span>
					</button>

				</div>

			<?php endif; ?>
		</div>

	<?php
			}

			protected function render_loop_item($settings) {
	?>
		<li class="bdt-news-ticker-item">


			<?php if ('title' == $settings['news_content']) : ?>
				<?php $this->render_title(); ?>
			<?php endif; ?>

			<?php if ('excerpt' == $settings['news_content']) : ?>
				<?php $this->render_excerpt(); ?>
			<?php endif; ?>


		</li>
<?php
			}
		}
