<?php
namespace ElementPack\Modules\BuddypressFriends\Widgets;

use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Buddypress_Friends extends Module_Base {

	//protected $_has_template_content = false;

	public function get_name() {
		return 'bdt-buddypress-friends';
	}

	public function get_title() {
		return BDTEP . esc_html__( 'BuddyPress Friends', 'bdthemes-element-pack' );
	}

	public function get_icon() {
		return 'bdt-wi-buddypress-friends';
	}

	public function get_categories() {
		return [ 'element-pack' ];
	}

	public function get_keywords() {
		return [ 'buddypress', 'user', 'friends', 'activity', 'streams', 'profiles' ];
	}

	public function get_custom_help_url() {
		return 'https://youtu.be/CLV9RCdq09k';
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_content_layout',
			[
				'label'     => esc_html__( 'Layout', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'friends_type',
			[
				'label'   => esc_html__( 'Friends Type', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'newest',
				'options' => [
					'newest'  => esc_html__('Newest', 'bdthemes-element-pack'),
					'popular' => esc_html__('Popular', 'bdthemes-element-pack'),
					'active'  => esc_html__('Active', 'bdthemes-element-pack'),
				],
			]
		);

		$this->add_responsive_control(
			'max_friends',
			[
				'label'   => esc_html__( 'Max Friends', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 5,
				],
				'range' => [
					'px' => [
						'min'  => 1,
						'max'  => 20,
						'step' => 1,
					],
				],
			]
		);

		$this->add_responsive_control(
			'columns',
			[
				'label'          => esc_html__( 'Columns', 'bdthemes-element-pack' ),
				'type'           => Controls_Manager::SELECT,
				'default'        => '6',
				'tablet_default' => '4',
				'mobile_default' => '2',
				'options'        => [
					'1'    => '1',
					'2'    => '2',
					'3'    => '3',
					'4'    => '4',
					'5'    => '5',
					'6'    => '6',
					'auto' => 'Auto',
				],
			]
		);

		$this->add_responsive_control(
			'column_gap',
			[
				'label'   => esc_html__( 'Column Gap', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 15,
				],
				'range' => [
					'px' => [
						'min'  => 0,
						'max'  => 100,
						'step' => 5,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-buddypress-friends .bdt-grid'     => 'margin-left: -{{SIZE}}px',
					'{{WRAPPER}} .bdt-buddypress-friends .bdt-grid > *' => 'padding-left: {{SIZE}}px',
				],
			]
		);

		$this->add_responsive_control(
			'row_gap',
			[
				'label'   => esc_html__( 'Row Gap', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 15,
				],
				'range' => [
					'px' => [
						'min'  => 0,
						'max'  => 100,
						'step' => 5,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-buddypress-friends .bdt-grid'     => 'margin-top: -{{SIZE}}px',
					'{{WRAPPER}} .bdt-buddypress-friends .bdt-grid > *' => 'margin-top: {{SIZE}}px',
				],
			]
		);

		$this->add_control(
			'align',
			[
				'label'   => __( 'Alignment', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => __( 'Left', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'default' => 'center',
			]
		);

		$this->add_control(
			'show_avatar',
			[
				'label'   => esc_html__( 'Show Avatar', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_title',
			[
				'label'   => esc_html__( 'Show Title', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_meta_as_tooltip',
			[
				'label'   => esc_html__( 'Show Meta as Tooltip', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_avatar',
			[
				'label'     => esc_html__( 'Avatar', 'bdthemes-element-pack' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_avatar' => 'yes',
				],
			]
		);

		$this->add_control(
			'avatar_size',
			[
				'label'     => __( 'Size', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SLIDER,
				'default' => [
					'size' => 100,
				],
				'range' => [
					'px' => [
						'min'  => 5,
						'max'  => 150,
						'step' => 5,
					],
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'avatar_border',
				'label'       => __( 'Border', 'bdthemes-element-pack' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .bdt-bp-friend-avatar img',
				'separator'   => 'before',
			]
		);

		$this->add_control(
			'avatar_border_radius',
			[
				'label'      => __( 'Border Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-bp-friend-avatar img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
				],
			]
		);

		$this->add_control(
			'avatar_opacity',
			[
				'label'   => __( 'Opacity (%)', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 1,
				],
				'range' => [
					'px' => [
						'max'  => 1,
						'min'  => 0.10,
						'step' => 0.01,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-bp-friend-avatar img' => 'opacity: {{SIZE}};',
				],
			]
		);

		$this->add_control(
			'avatar_spacing',
			[
				'label' => __( 'Spacing', 'bdthemes-element-pack' ),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-bp-friend-avatar img'  => 'margin-bottom: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_title',
			[
				'label'     => __( 'Title', 'bdthemes-element-pack' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_title' => 'yes',
				],
			]
		);

		$this->add_control(
			'title_color',
			[
				'label'     => __( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-bp-friend-title a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .bdt-bp-friend-title a',
			]
		);

		$this->end_controls_section();
	}

	public function render() {
		$settings = $this->get_settings_for_display();
		$type     = $settings['friends_type'];

		$friends_args = array(
			'user_id'         => bp_displayed_user_id(),
			'type'            => esc_attr($type),
			'max'             => esc_attr($settings['max_friends']['size']),
			'populate_extras' => 1,
		);

		$avatar = array(
			'type'   => 'full',
			'width'  => esc_attr($settings['avatar_size']['size']),
			'class'  => 'avatar',
		);

		if ( bp_has_members( $friends_args ) ) : ?>

			<div class="bdt-buddypress-friends">			
				<div class="bdt-grid bdt-grid-small bdt-text-<?php echo esc_attr($settings['align']); ?> bdt-flex-<?php echo esc_attr($settings['align']); ?>" data-bdt-grid>

			<?php while ( bp_members() ) : bp_the_member(); ?>
				<?php
				$this->add_render_attribute('bp-friend', 'class', 'bdt-bp-friend');
				if ('auto' !== $settings['columns']) {
					$columns_mobile = isset($settings['columns_mobile']) ? $settings['columns_mobile'] : 2;
					$columns_tablet = isset($settings['columns_tablet']) ? $settings['columns_tablet'] : 4;
					$columns 		 = isset($settings['columns']) ? $settings['columns'] : 6;

					$this->add_render_attribute('bp-friend', 'class', 'bdt-width-1-'. $columns_mobile);
					$this->add_render_attribute('bp-friend', 'class', 'bdt-width-1-'. $columns_tablet .'@s');
					$this->add_render_attribute('bp-friend', 'class', 'bdt-width-1-'. $columns .'@m');
				} else {
					$this->add_render_attribute('bp-friend', 'class', 'bdt-width-auto');
				}
				?>

				<?php if ($settings['show_meta_as_tooltip']) : ?>
					<?php if ( 'active' === $type ) : ?>
						<?php $this->add_render_attribute('bp-friend', 'data-bdt-tooltip', 'title: ' . bp_get_member_last_active(), true); ?>
					<?php elseif ( 'newest' === $type ) : ?>
						<?php $this->add_render_attribute('bp-friend', 'data-bdt-tooltip', 'title: ' . bp_get_member_registered(), true); ?>
					<?php elseif ( bp_is_active( 'friends' ) ) : ?>
						<?php $this->add_render_attribute('bp-friend', 'data-bdt-tooltip', 'title: ' . bp_get_member_total_friend_count(), true); ?>
					<?php endif; ?>
				<?php endif; ?>

				<div <?php echo $this->get_render_attribute_string('bp-friend'); ?>>
					<?php if ($settings['show_avatar']) : ?>
						<div class="bdt-bp-friend-avatar">
							<a href="<?php bp_member_permalink(); ?>" title=""><?php bp_member_avatar($avatar); ?></a>
						</div>
					<?php endif; ?>

					<?php if ($settings['show_title']) : ?>
						<div class="bdt-bp-friend-title"><a href="<?php bp_member_permalink(); ?>" title=""><?php bp_member_name(); ?></a></div>
					<?php endif; ?>								
				</div>
			<?php endwhile; ?></div>
		</div>

		<?php else: ?>
			<div class="bdt-alert-warning" data-bdt-alert>There were no members found, please try another filter.</div>
		<?php endif;


	}

}
