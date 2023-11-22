<?php
namespace ElementPack\Modules\PostGallery\Skins;
use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Utils;


use Elementor\Skin_Base as Elementor_Skin_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Skin_Fedara extends Elementor_Skin_Base {
	public function get_id() {
		return 'bdt-fedara';
	}

	public function get_title() {
		return __( 'Fedara', 'bdthemes-element-pack' );
	}

	public function _register_controls_actions() {
		parent::_register_controls_actions();
		add_action( 'elementor/element/bdt-post-gallery/section_design_layout/after_section_end', [ $this, 'register_fedara_overlay_animation_controls'   ] );
	}

	public function register_fedara_overlay_animation_controls( Module_Base $widget ) {
		$this->parent = $widget;
		$this->start_controls_section(
			'section_style_fedara',
			[
				'label' => __( 'Fedara Style', 'bdthemes-element-pack' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'desc_background_color',
			[
				'label'     => esc_html__( 'Background Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-post-gallery-skin-fedara-desc' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'desc_color',
			[
				'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-post-gallery-skin-fedara-desc *' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'desc_padding',
			[
				'label'      => __( 'Padding', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-post-gallery-skin-fedara-desc' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'desc_alignment',
			[
				'label'       => __( 'Alignment', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options'     => [
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
				'default'      => 'center',
				'prefix_class' => 'bdt-post-gallery-skin-fedara-style-',
				'selectors'    => [
					'{{WRAPPER}} .bdt-post-gallery-skin-fedara-desc' => 'text-align: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'label'    => esc_html__( 'Typography', 'bdthemes-element-pack' ),
				//'scheme'   => Schemes\Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} .bdt-gallery-item .bdt-gallery-item-title',
			]
		);

		$this->end_controls_section();
	}

	public function render_overlay() {
		$settings = $this->parent->get_settings();

		$this->parent->add_render_attribute(
			[
				'overlay-settings' => [
					'class' => [
						'bdt-position-cover',
						'bdt-overlay',
						'bdt-overlay-default',
						$settings['overlay_animation'] ? 'bdt-transition-' . $settings['overlay_animation'] : ''
					]
				]
			], '', '', true
		);

		?>
		<div <?php echo $this->parent->get_render_attribute_string( 'overlay-settings' ); ?>>
			<div class="bdt-post-gallery-content">
				<div class="bdt-gallery-content-inner">
					<?php

					$placeholder_img_src = Utils::get_placeholder_image_src();

					$img_url = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full' );

					if ( ! $img_url ) {
						$img_url = $placeholder_img_src;
					} else {
						$img_url = $img_url[0];
					}

					$this->parent->add_render_attribute(
						[
							'lightbox-settings' => [
								'class' => [
									'bdt-gallery-item-link',
									'bdt-gallery-lightbox-item',
									('icon' == $settings['link_type']) ? 'bdt-link-icon' : 'bdt-link-text'
								],
								'data-elementor-open-lightbox' => 'no',
								'data-caption'                 => get_the_title(),
								'href'                         => esc_url($img_url)
							]
						], '', '', true
					);

					if ( 'none' !== $settings['show_link'])  : ?>
						<div class="bdt-flex-inline bdt-gallery-item-link-wrapper">
							<?php if (( 'lightbox' == $settings['show_link'] ) || ( 'both' == $settings['show_link'] )) : ?>
								<a <?php echo $this->parent->get_render_attribute_string( 'lightbox-settings' ); ?>>
									<?php if ( 'icon' == $settings['link_type'] ) : ?>
										<i class="ep-icon-plus" aria-hidden="true"></i>
									<?php elseif ( 'text' == $settings['link_type'] ) : ?>
										<span><?php esc_html_e( 'ZOOM', 'bdthemes-element-pack' ); ?></span>
									<?php endif; ?>
								</a>
							<?php endif; ?>

							<?php if (( 'post' == $settings['show_link'] ) || ( 'both' == $settings['show_link'] )) : ?>
								<?php
									$link_type_class =  ( 'icon' == $settings['link_type'] ) ? ' bdt-link-icon' : ' bdt-link-text';
									$target =  ( $settings['external_link'] ) ? 'target="_blank"' : '';

									?>
								<a class="bdt-gallery-item-link<?php echo esc_attr($link_type_class); ?>" href="<?php echo get_permalink(); ?>" <?php echo esc_attr($target); ?>>
									<?php if ( 'icon' == $settings['link_type'] ) : ?>
										<i class="ep-icon-link" aria-hidden="true"></i>
									<?php elseif ( 'text' == $settings['link_type'] ) : ?>
										<span><?php esc_html_e( 'VIEW', 'bdthemes-element-pack' ); ?></span>
									<?php endif; ?>
								</a>
							<?php endif; ?>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
		<?php
	}

	public function render_desc() {
		?>
		<div class="bdt-post-gallery-skin-fedara-desc bdt-padding-small">
			<?php
			$this->parent->render_title();
			$this->parent->render_excerpt();
			$this->parent->render_categories_names();
			?>
		</div>
		<?php
	}
	public function render_post() {
		$settings = $this->parent->get_settings();
		$categories = $this->parent->filter_menu_terms();


		if ('yes' === $settings['tilt_show']) {
			$this->parent->add_render_attribute('post-gallery-item', 'data-tilt', '', true);
			if ('yes' === $settings['tilt_scale']) {
				$this->parent->add_render_attribute('post-gallery-item', 'data-tilt-scale', '1.2', true);
			}
		}

		$this->parent->add_render_attribute('post-gallery-item', 'class', ['bdt-gallery-item bdt-transition-toggle'], true);

		if ('yes' === $settings['show_filter_bar']) {
			$this->parent->add_render_attribute('post-gallery-item', 'data-filter', $categories, true);
		}

		$columns_mobile = isset($settings['columns_mobile']) ? $settings['columns_mobile'] : 1;
		$columns_tablet = isset($settings['columns_tablet']) ? $settings['columns_tablet'] : 2;
		$columns 		= isset($settings['columns']) ? $settings['columns'] : 3;

		$this->parent->add_render_attribute('post-gallery-item', 'class', 'bdt-width-1-'. $columns_mobile);
		$this->parent->add_render_attribute('post-gallery-item', 'class', 'bdt-width-1-'. $columns_tablet .'@s');
		$this->parent->add_render_attribute('post-gallery-item', 'class', 'bdt-width-1-'. $columns .'@m');

		?>
		<div <?php echo $this->parent->get_render_attribute_string( 'post-gallery-item' ); ?>>
			<div class="bdt-post-gallery-inner">
				<?php
				$this->parent->render_thumbnail();
				$this->render_overlay();
				?>
			</div>
			<?php $this->render_desc(); ?>
		</div>
		<?php
	}

	public function render() {
		$settings = $this->parent->get_settings();
		$this->parent->query_posts($settings['posts_per_page']);
		$wp_query = $this->parent->get_query();

		if ( ! $wp_query->found_posts ) {
			return;
		}

		// $this->parent->get_posts_tags();
		$this->parent->render_header('fedara');

		while ( $wp_query->have_posts() ) {
			$wp_query->the_post();
			$this->render_post();
		}

		$this->parent->render_footer();

		if ($settings['show_pagination']) {
			element_pack_post_pagination($wp_query);
		}

		wp_reset_postdata();
	}
}

