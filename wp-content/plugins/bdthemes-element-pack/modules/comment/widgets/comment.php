<?php

namespace ElementPack\Modules\Comment\Widgets;

use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Comment extends Module_Base {

	protected $_has_template_content = false;

	public function get_name() {
		return 'bdt-comment';
	}

	public function get_title() {
		return BDTEP . esc_html__('Comment', 'bdthemes-element-pack');
	}

	public function get_icon() {
		return 'bdt-wi-comment';
	}

	public function get_categories() {
		return ['element-pack'];
	}

	public function get_keywords() {
		return ['comment', 'remark', 'note'];
	}

	public function get_script_depends() {
		if ($this->ep_is_edit_mode()) {
			return ['ep-scripts'];
		} else {
			return ['ep-comment'];
		}
	}

	public function get_custom_help_url() {
		return 'https://youtu.be/csvMTyUx7Hs';
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_content_layout',
			[
				'label' => esc_html__('Layout', 'bdthemes-element-pack'),
			]
		);


		$this->add_control(
			'layout',
			[
				'label'   => esc_html__('Comment Type', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => '',
				'options' =>  [
					''         => esc_html__('Select', 'bdthemes-element-pack'),
					'disqus'   => esc_html__('Disqus', 'bdthemes-element-pack'),
					'facebook' => esc_html__('Facebook', 'bdthemes-element-pack'),
				],
			]
		);


		$this->add_control(
			'comments_number',
			[
				'label'       => esc_html__('Comment Count', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::NUMBER,
				'min'         => 5,
				'max'         => 100,
				'default'     => 10,
				'description' => esc_html__('Minimum number of comments: 5', 'bdthemes-element-pack'),
				'condition' => [
					'layout' => 'facebook',
				]
			]
		);

		$this->add_control(
			'order_by',
			[
				'label'   => esc_html__('Order By', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'social',
				'options' => [
					'social'       => esc_html__('Social', 'bdthemes-element-pack'),
					'reverse_time' => esc_html__('Reverse Time', 'bdthemes-element-pack'),
					'time'         => esc_html__('Time', 'bdthemes-element-pack'),
				],
				'condition' => [
					'layout' => 'facebook',
				]
			]
		);

		$this->end_controls_section();
	}


	public function render() {
		$settings  = $this->get_settings_for_display();
		$id        = $this->get_id();
		$permalink = get_the_permalink();
		$options   = get_option('element_pack_api_settings');
		$user_name = (!empty($options['disqus_user_name'])) ? $options['disqus_user_name'] : 'bdthemes';
		$app_id    = (!empty($options['facebook_app_id'])) ? $options['facebook_app_id'] : '461738690569028';

		$this->add_render_attribute('comment', 'class', 'bdt-comment-container');

		$this->add_render_attribute(
			[
				'comment' => [
					'data-settings' => [
						wp_json_encode(array_filter([
							"layout" => $settings["layout"],
							"username" => $user_name,
							"permalink" => $permalink,
							"app_id" => $app_id,
						]))
					],
					"style" => "min-height: 1px;",
				]
			]
		);

?>

		<div <?php echo $this->get_render_attribute_string('comment'); ?>>
			<?php if ('disqus' === $settings['layout']) : ?>
				<div id="disqus_thread"></div>

				<noscript>Please enable JavaScript to view the <a href="https://disqus.com/?ref_noscript">comments powered by Disqus.</a></noscript>

			<?php elseif ('facebook' === $settings['layout']) : ?>
				<?php
				$attributes = [
					'class'         => 'fb-comments',
					'data-href'     => $permalink,
					'data-numposts' => $settings['comments_number'],
					'data-order-by' => $settings['order_by'],
				];

				$this->add_render_attribute('fb-comment', $attributes);
				?>
				<div <?php echo $this->get_render_attribute_string('fb-comment'); ?>></div>
				<div id="fb-root"></div>
			<?php else : ?>
				<div class="bdt-alert-warning" bdt-alert>
					<a class="bdt-alert-close" bdt-close></a>
					<p>Select your comment provider from settings.</p>
				</div>
			<?php endif; ?>
			<div class="bdt-clearfix"></div>
		</div>
<?php
	}
}
