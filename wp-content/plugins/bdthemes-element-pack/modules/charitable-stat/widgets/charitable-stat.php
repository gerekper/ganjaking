<?php
namespace ElementPack\Modules\CharitableStat\Widgets;

use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Charitable_Stat extends Module_Base {

	public function get_name() {
		return 'bdt-charitable-stat';
	}

	public function get_title() {
		return BDTEP . __( 'Charitable Stat', 'bdthemes-element-pack' );
	}

	public function get_icon() {
		return 'bdt-wi-charitable-stat';
	}

	public function get_categories() {
		return [ 'element-pack' ];
	}

	public function get_keywords() {
		return [ 'charitable', 'charity', 'donation', 'donor', 'goal', 'charitable', 'wall', 'stat' ];
	}

	public function get_style_depends() {
        if ($this->ep_is_edit_mode()) {
            return ['ep-styles'];
        } else {
            return ['ep-charitable-stat'];
        }
	}
	
	public function get_custom_help_url() {
		return 'https://youtu.be/54cw85jmhtg';
	}

    protected function register_controls() {

		$this->start_controls_section(
			'section_charitable_stat',
			[
				'label' => __( 'Charitable Stat', 'bethemes-element-pack' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'campaign',
			[
				'label'       => __( 'Campaigns', 'bethemes-element-pack' ),
				'type'        => Controls_Manager::SELECT2,
				'options'     => element_pack_charitable_forms_options(),
				'multiple'    => true,
				'label_block' => true,
				'exclude'      => [ 'all' ],
			]
		);

		$this->add_control(
			'display',
			[
				'label'   => __( 'Display Type', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'total'     => esc_html__( 'Total', 'bdthemes-element-pack' ),
					'progress'  => esc_html__( 'Progress', 'bdthemes-element-pack' ),
					'donors'    => esc_html__( 'Donors', 'bdthemes-element-pack' ),
					'donations' => esc_html__( 'Donations', 'bdthemes-element-pack' ),
				],
				'default'     => 'total',
			]
		);

		$this->add_control(
			'goal',
			[
				'label' => esc_html__( 'Goal', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::TEXT,
				'default' => 1000,
				'label_block' => true,
				'condition' => [
					'display' => 'progress',
				],
			]
		);
		
		$this->end_controls_section();

		$this->start_controls_section(
			'section_text_style',
			[
				'label' => esc_html__( 'Text', 'bdthemes-element-pack' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'display!' => 'progress',
				],
			]
		);

		$this->add_control(
			'text_color',
			[
				'label' => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-charitable-stat' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'text_typography',
				'selector' => '{{WRAPPER}} .bdt-charitable-stat',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'progress_bar_style',
			[
				'label' => esc_html__( 'Progress Bar', 'bdthemes-element-pack' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'display' => 'progress',
				],
			]
		);
		
		$this->add_control(
			'progress_color',
			[
				'label' => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-charitable-stat .campaign-progress-bar>span' => 'background-color: {{VALUE}} !important;',
				],
			]
		);

		$this->add_control(
			'progress_bg_color',
			[
				'label' => esc_html__( 'Background Color', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-charitable-stat .campaign-progress-bar' => 'background-color: {{VALUE}};',
				],
			]
		);

		
		$this->add_responsive_control(
			'progress_border_radius',
			[
				'label' => __( 'Border Radius', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .bdt-charitable-stat .campaign-progress-bar' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'progress_height',
			[
				'label' => __( 'Height', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .bdt-charitable-stat .campaign-progress-bar' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

	}

	private function get_shortcode() {
		$settings = $this->get_settings_for_display();

		if (!$settings['campaign']) {
			return '<div class="bdt-alert bdt-alert-warning">'.__('Please select Charitable Campaigns From Setting!', 'bdthemes-element-pack').'</div>';
		}

		$attributes = [
			'campaigns' => implode(',', $settings['campaign']),
			'display'  => $settings['display'],
			'goal'     => $settings['goal'],
		];

		$this->add_render_attribute( 'shortcode', $attributes );

		$shortcode   = [];
		$shortcode[] = sprintf( '[charitable_stat %s]', $this->get_render_attribute_string( 'shortcode' ) );

		return implode("", $shortcode);
	}

	public function render() {

        $this->add_render_attribute( 'charitable_wrapper', 'class', 'bdt-charitable-stat' );
		
		?>

		<div <?php echo $this->get_render_attribute_string('charitable_wrapper'); ?>>

			<?php echo do_shortcode( $this->get_shortcode() ); ?>

		</div>

		<?php
	}

	public function render_plain_content() {
		echo $this->get_shortcode();
	}
	
}