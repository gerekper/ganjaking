<?php
namespace ElementPack\Modules\DownloadMonitor\Widgets;

use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Core\Schemes;

use Elementor\Icons_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class DownloadMonitor extends Module_Base {

	public function get_name() {
		return 'bdt-download-monitor';
	}

	public function get_title() {
		return BDTEP . esc_html__( 'Download Monitor', 'bdthemes-element-pack' );
	}

	public function get_icon() {
		return 'bdt-wi-download-monitor';
	}

	public function get_keywords() {
		return [ 'download', 'monitor' ];
	}

	public function get_categories() {
		return [ 'element-pack' ];
	}

	public function get_style_depends() {
        if ($this->ep_is_edit_mode()) {
            return ['ep-styles'];
        } else {
            return [ 'ep-download-monitor' ];
        }
    }

	public function get_custom_help_url() {
		return 'https://youtu.be/7LaBSh3_G5A';
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_content_download_monitor',
			[
				'label' => esc_html__( 'Content', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'file_id',
			[
				'label'     => esc_html__( 'Select File', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => element_pack_download_file_list(),
			]
		);


		$this->add_control(
			'file_type_show',
			[
				'label'     => esc_html__( 'Show File Type', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'condition' => [
					'file_id!' => '',
				],
			]
		);

		$this->add_control(
			'file_size_show',
			[
				'label'     => esc_html__( 'Show File Size', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'condition' => [
					'file_id!' => '',
				],
			]
		);

		$this->add_control(
			'download_count_show',
			[
				'label'     => esc_html__( 'Show Download Count', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => [
					'file_id!' => '',
				],
			]
		);



		$this->end_controls_section();


		$this->start_controls_section(
			'section_content_button',
			[
				'label' => esc_html__( 'Button', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'alt_title',
			[
				'label' => esc_html__( 'Alternative Title', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::TEXT,
			]
		);

		$this->add_control(
			'link',
			[
				'label' => esc_html__( 'Link', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::URL,
				'placeholder' => 'http://your-link.com',
				'default' => [
					'url'         => '#',
					'is_external' => '',
				],
			]
		);

		$this->add_control(
			'add_custom_attributes',
			[
				'label'     => __( 'Add Custom Attributes', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'custom_attributes',
			[
				'label' => __( 'Custom Attributes', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::TEXTAREA,
				'dynamic' => [
					'active' => true,
				],
				'placeholder' => __( 'key|value', 'bdthemes-element-pack' ),
				'description' => sprintf( __( 'Set custom attributes for the price table button tag. Each attribute in a separate line. Separate attribute key from the value using %s character.', 'bdthemes-element-pack' ), '<code>|</code>' ),
				'classes' => 'elementor-control-direction-ltr',
				'condition' => ['add_custom_attributes' => 'yes']
			]
		);

		$this->add_responsive_control(
			'align',
			[
				'label'   => esc_html__( 'Alignment', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => esc_html__( 'Left', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				// 'prefix_class' => 'elementor-align%s-',
				'selectors' => [
					'{{WRAPPER}}.elementor-widget-bdt-download-monitor' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'download_monitor_icon',
			[
				'label'       => esc_html__( 'Icon', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
			]
		);

		$this->add_control(
			'icon_align',
			[
				'label'   => esc_html__( 'Icon Position', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'right',
				'options' => [
					'left'  => esc_html__( 'Before', 'bdthemes-element-pack' ),
					'right' => esc_html__( 'After', 'bdthemes-element-pack' ),
				],
				'condition' => [
					'download_monitor_icon[value]!' => '',
				],
			]
		);

		$this->add_control(
			'icon_indent',
			[
				'label' => esc_html__( 'Icon Spacing', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 8,
				],
				'range' => [
					'px' => [
						'max' => 50,
					],
				],
				'condition' => [
					'download_monitor_icon[value]!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-download-monitor-button .bdt-button-icon-align-right' => 'margin-left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .bdt-download-monitor-button .bdt-button-icon-align-left' => 'margin-right: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();




		$this->start_controls_section(
			'section_style_button',
			[
				'label' => esc_html__( 'Button', 'bdthemes-element-pack' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'heading_footer_button',
			[
				'label' => esc_html__( 'Button', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->start_controls_tabs( 'tabs_button_style' );

		$this->start_controls_tab(
			'tab_button_normal',
			[
				'label' => esc_html__( 'Normal', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'button_text_color',
			[
				'label' => esc_html__( 'Text Color', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} a.bdt-download-monitor-button' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'button_background_color',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} a.bdt-download-monitor-button',
				'separator' => 'after',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'button_box_shadow',
				'selector' => '{{WRAPPER}} a.bdt-download-monitor-button',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(), [
				'name' => 'button_border',
				'label' => esc_html__( 'Border', 'bdthemes-element-pack' ),
				'placeholder' => '1px',
				'default' => '1px',
				'selector' => '{{WRAPPER}} a.bdt-download-monitor-button',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'button_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} a.bdt-download-monitor-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'button_text_padding',
			[
				'label' => esc_html__( 'Text Padding', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} a.bdt-download-monitor-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'button_typography',
				'label' => esc_html__( 'Title Typography', 'bdthemes-element-pack' ),
				//'scheme' => Schemes\Typography::TYPOGRAPHY_4,
				'selector' => '{{WRAPPER}} a.bdt-download-monitor-button .bdt-dm-title',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'button_meta_typography',
				'label' => esc_html__( 'Meta Typography', 'bdthemes-element-pack' ),
				//'scheme' => Schemes\Typography::TYPOGRAPHY_4,
				'selector' => '{{WRAPPER}} a.bdt-download-monitor-button .bdt-dm-meta > *',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_button_hover',
			[
				'label' => esc_html__( 'Hover', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'button_hover_color',
			[
				'label' => esc_html__( 'Text Color', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} a.bdt-download-monitor-button:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'button_background_hover_color',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} a.bdt-download-monitor-button:hover',
				'separator' => 'after',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'button_hover_box_shadow',
				'selector' => '{{WRAPPER}} a.bdt-download-monitor-button:hover',
			]
		);

		$this->add_control(
			'button_hover_border_color',
			[
				'label' => esc_html__( 'Border Color', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} a.bdt-download-monitor-button:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_hover_animation',
			[
				'label' => esc_html__( 'Animation', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::HOVER_ANIMATION,
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();




	}

	public function render() {
		$settings  = $this->get_settings_for_display();

		try {
			$download = download_monitor()->service( 'download_repository' )->retrieve_single( $settings['file_id'] );
		} catch ( \Exception $exception ) {
			$exception->getMessage();
			return;
		}

		if ( $settings['add_custom_attributes'] and ! empty( $settings['custom_attributes'] ) ) {
			$attributes = explode( "\n", $settings['custom_attributes'] );

			$reserved_attr = [ 'href', 'target' ];

			foreach ( $attributes as $attribute ) {
				if ( ! empty( $attribute ) ) {
					$attr = explode( '|', $attribute, 2 );
					if ( ! isset( $attr[1] ) ) {
						$attr[1] = '';
					}

					if ( ! in_array( strtolower( $attr[0] ), $reserved_attr ) ) {
						$this->add_render_attribute( 'download-monitor-button', trim( $attr[0] ), trim( $attr[1] ) );
					}
				}
			}
		}

		if ( ! isset( $settings['icon'] ) && ! Icons_Manager::is_migration_allowed() ) {
			// add old default
			$settings['icon'] = 'fas fa-arrow-down';
		}

		$migrated  = isset( $settings['__fa4_migrated']['download_monitor_icon'] );
		$is_new    = empty( $settings['icon'] ) && Icons_Manager::is_migration_allowed();

		if (isset($download)) {

			$this->add_render_attribute(
				[
					'download-monitor-button' => [
						'class' => [
							'bdt-download-monitor-button',
							'elementor-button',
							'elementor-size-sm',
							$settings['button_hover_animation'] ? 'elementor-animation-'.$settings['button_hover_animation'] : ''
						],
						'href' => [
							$download->get_the_download_link()
						],
						'target' => [
							$settings['link']['is_external'] ? "_blank" : "_self"
						]
					]
				]
			);

			?>
            <a <?php echo $this->get_render_attribute_string( 'download-monitor-button' ); ?>>

				<div class="bdt-dm-description">
	            	<div class="bdt-dm-title">
						<?php if ($settings['alt_title']) {
							echo esc_html( $settings['alt_title'] );
						} else {
							echo esc_html($download->get_title());
						} ?>
	            	</div>

					<div class="bdt-dm-meta">
		            	<?php if ('yes' === $settings['file_type_show']) : ?>
		            	<div class="bdt-dm-file">
		            		<?php echo esc_html($download->get_version()->get_filetype()); ?>
		            		
		            	</div>
		            	<?php endif; ?>
		            	
		            	<?php if ('yes' === $settings['file_size_show']) : ?>
		            	<div class="bdt-dm-size">
		            		<?php echo esc_html($download->get_version()->get_filesize_formatted()); ?>
		            	</div>
		            	<?php endif; ?>

		            	<?php if ('yes' === $settings['download_count_show']) : ?>
		            	<div class="bdt-dm-count">
		            		<?php esc_html_e('Downloaded', 'bdthemes-element-pack'); ?> <?php echo esc_html($download->get_download_count()); ?>
		            	</div>
		            	<?php endif; ?>
					</div>
				</div>
            	
            	<?php if ($settings['download_monitor_icon']['value']) : ?>
					<span class="bdt-dm-button-icon bdt-button-icon-align-<?php echo esc_html($settings['icon_align']); ?>">

						<?php if ( $is_new || $migrated ) :
						Icons_Manager::render_icon( $settings['download_monitor_icon'], [ 'aria-hidden' => 'true', 'class' => 'fa-fw' ] );
						else : ?>
							<i class="<?php echo esc_attr( $settings['icon'] ); ?>" aria-hidden="true"></i>
						<?php endif; ?>

					</span>
				<?php endif; ?>

            </a>
			<?php
		}
	}

}