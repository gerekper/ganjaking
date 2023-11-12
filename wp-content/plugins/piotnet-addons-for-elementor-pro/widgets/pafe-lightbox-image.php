<?php

class PAFE_Lightbox_Image extends \Elementor\Widget_Base {

	public function get_name() {
		return 'pafe-lightbox-image';
	}

	public function get_title() {
		return __( 'PAFE Lightbox Image', 'pafe' );
	}

	public function get_icon() {
		return 'far fa-image';
	}

	public function get_categories() {
		return [ 'pafe' ];
	}

	public function get_script_depends() {
		return [ 
			'pafe-widget'
		];
	}

	public function get_style_depends() {
		return [ 
			'pafe-widget-style'
		];
	}

	protected function _register_controls() {
		$this->start_controls_section(
			'section_image',
			[
				'label' => __( 'PAFE Lightbox Image', 'elementor' ),
			]
		);

		$this->add_control(
			'important_note',
			[
				'type' => \Elementor\Controls_Manager::RAW_HTML,
				'raw' => __( 'This feature only works on the frontend.', 'pafe' ),
			]
		);

		$this->add_control(
			'image',
			[
				'label' => __( 'Choose Image', 'elementor' ),
				'type' => \Elementor\Controls_Manager::MEDIA,
				'dynamic' => [
					'active' => true,
				],
				'default' => [
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Image_Size::get_type(),
			[
				'name' => 'image', // Usage: `{name}_size` and `{name}_custom_dimension`, in this case `image_size` and `image_custom_dimension`.
				'default' => 'large',
				'separator' => 'none',
			]
		);

		$this->add_responsive_control(
			'align',
			[
				'label' => __( 'Alignment', 'elementor' ),
				'type' => \Elementor\Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => __( 'Left', 'elementor' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'elementor' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'elementor' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}}' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'light_skin',
			[
				'label' => __( 'Enable Light Skin', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => '',
				'label_on' => 'Yes',
				'label_off' => 'No',
				'return_value' => 'yes',
			]
		);

		$this->add_control(
			'background_color',
			[
				'label' => __( 'Background Color', 'pafe' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '',
			]
		);

		$this->add_control(
			'background_opacity',
			[
				'label' => __( 'Background Opacity', 'pafe' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'default' => 1,
				'min' => 0.1,
				'max' => 1,
				'step' => 0.1,
			]
		);

		$this->add_control(
			'share_buttons',
			[
				'type' => \Elementor\Controls_Manager::RAW_HTML,
				'raw' => __( 'Share Buttons', 'pafe' ),
				'separator' => 'before',
			]
		);

		$this->add_control(
			'facebook',
			[
				'label' => __( 'Facebook', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => 'yes',
				'label_on' => 'Yes',
				'label_off' => 'No',
				'return_value' => 'yes',
			]
		);

		$this->add_control(
			'tweeter',
			[
				'label' => __( 'Tweeter', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => 'yes',
				'label_on' => 'Yes',
				'label_off' => 'No',
				'return_value' => 'yes',
			]
		);

		$this->add_control(
			'pinterest',
			[
				'label' => __( 'Pinterest', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => 'yes',
				'label_on' => 'Yes',
				'label_off' => 'No',
				'return_value' => 'yes',
			]
		);

		$this->add_control(
			'download_image',
			[
				'label' => __( 'Download Image', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => 'yes',
				'label_on' => 'Yes',
				'label_off' => 'No',
				'return_value' => 'yes',
			]
		);

		$this->add_control(
			'title_source',
			[
				'label' => __( 'Title', 'elementor' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'none' => __( 'None', 'elementor' ),
					'attachment' => __( 'Attachment Title', 'pafe' ),
					'custom_field' => __( 'Custom Field', 'pafe' ),
					'acf_field' => __( 'ACF Field', 'pafe' ),
				],
				'default' => 'none',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'title',
			[
				'label' => __( 'Custom Title', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => '',
				'placeholder' => __( 'Enter your image title', 'pafe' ),
				'condition' => [
					'title_source' => 'custom',
				],
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'caption_source',
			[
				'label' => __( 'Caption', 'elementor' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'none' => __( 'None', 'elementor' ),
					'attachment' => __( 'Attachment Caption', 'elementor' ),
					'description' => __( 'Attachment Description', 'pafe' ),
					'custom_field' => __( 'Custom Field', 'pafe' ),
					'acf_field' => __( 'ACF Field', 'pafe' ),
				],
				'default' => 'none',
			]
		);

		$this->add_control(
			'caption',
			[
				'label' => __( 'Custom Caption', 'elementor' ),
				'type' => \Elementor\Controls_Manager::WYSIWYG,
				'default' => '',
				'placeholder' => __( 'Enter your image caption', 'elementor' ),
				'condition' => [
					'caption_source' => 'custom',
				],
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'view',
			[
				'label' => __( 'View', 'elementor' ),
				'type' => \Elementor\Controls_Manager::HIDDEN,
				'default' => 'traditional',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_image',
			[
				'label' => __( 'Image', 'elementor' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'width',
			[
				'label' => __( 'Width', 'elementor' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'default' => [
					'unit' => '%',
				],
				'tablet_default' => [
					'unit' => '%',
				],
				'mobile_default' => [
					'unit' => '%',
				],
				'size_units' => [ '%', 'px', 'vw' ],
				'range' => [
					'%' => [
						'min' => 1,
						'max' => 100,
					],
					'px' => [
						'min' => 1,
						'max' => 1000,
					],
					'vw' => [
						'min' => 1,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-image img' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'space',
			[
				'label' => __( 'Max Width', 'elementor' ) . ' (%)',
				'type' => \Elementor\Controls_Manager::SLIDER,
				'default' => [
					'unit' => '%',
				],
				'tablet_default' => [
					'unit' => '%',
				],
				'mobile_default' => [
					'unit' => '%',
				],
				'size_units' => [ '%' ],
				'range' => [
					'%' => [
						'min' => 1,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-image img' => 'max-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'separator_panel_style',
			[
				'type' => \Elementor\Controls_Manager::DIVIDER,
				'style' => 'thick',
			]
		);

		$this->start_controls_tabs( 'image_effects' );

		$this->start_controls_tab( 'normal',
			[
				'label' => __( 'Normal', 'elementor' ),
			]
		);

		$this->add_control(
			'opacity',
			[
				'label' => __( 'Opacity', 'elementor' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 1,
						'min' => 0.10,
						'step' => 0.01,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-image img' => 'opacity: {{SIZE}};',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Css_Filter::get_type(),
			[
				'name' => 'css_filters',
				'selector' => '{{WRAPPER}} .elementor-image img',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab( 'hover',
			[
				'label' => __( 'Hover', 'elementor' ),
			]
		);

		$this->add_control(
			'opacity_hover',
			[
				'label' => __( 'Opacity', 'elementor' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 1,
						'min' => 0.10,
						'step' => 0.01,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-image:hover img' => 'opacity: {{SIZE}};',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Css_Filter::get_type(),
			[
				'name' => 'css_filters_hover',
				'selector' => '{{WRAPPER}} .elementor-image:hover img',
			]
		);

		$this->add_control(
			'background_hover_transition',
			[
				'label' => __( 'Transition Duration', 'elementor' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 3,
						'step' => 0.1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-image img' => 'transition-duration: {{SIZE}}s',
				],
			]
		);

		$this->add_control(
			'hover_animation',
			[
				'label' => __( 'Hover Animation', 'elementor' ),
				'type' => \Elementor\Controls_Manager::HOVER_ANIMATION,
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name' => 'image_border',
				'selector' => '{{WRAPPER}} .elementor-image img',
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'image_border_radius',
			[
				'label' => __( 'Border Radius', 'elementor' ),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .elementor-image img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'image_box_shadow',
				'exclude' => [
					'box_shadow_position',
				],
				'selector' => '{{WRAPPER}} .elementor-image img',
			]
		);

		$this->end_controls_section();

	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		if ( empty( $settings['image']['url'] ) ) {
			return;
		}

		$this->add_render_attribute( 'wrapper', 'class', 'elementor-image' );

		if ( ! empty( $settings['shape'] ) ) {
			$this->add_render_attribute( 'wrapper', 'class', 'elementor-image-shape-' . $settings['shape'] );
		}

		$id = $settings['image']['id'];

		$image = wp_get_attachment_metadata( $id );

		$image_post = get_post($id);
		$image_title = $image_post->post_title;
		$image_caption = $image_post->post_excerpt;

		$title = '';
		$title_source = $settings['title_source'];
		if($title_source=='attachment') {
			$title = $image_post->post_title;
		}
		if($title_source=='custom') {
			$title = $settings['title'];
		}

		$caption = '';
		$caption_source = $settings['caption_source'];
		if($caption_source=='attachment') {
			$caption = $image_post->post_excerpt;
		}
		if($caption_source=='custom') {
			$caption = $settings['caption'];
		}

		$gallery_div = '';

		if( !empty($settings['light_skin']) ) {
			if( $settings['light_skin'] == 'yes' ) {
				$gallery_div .= " data-pafe-lightbox-gallery-light-skin";
			}
		} else {
			$gallery_div .= " data-pafe-lightbox-gallery-dark-skin";
		}

		if ( ! empty( $settings['background_color'] ) ) {
            $gallery_div .= " data-pafe-lightbox-gallery-background-color='" . $settings['background_color'] . "'";
		} else {
            $gallery_div .= " data-pafe-lightbox-gallery-background-color=''";
        }

		if( !empty($settings['background_opacity']) ) {
			$gallery_div .= " data-pafe-lightbox-gallery-background-opacity='" . $settings['background_opacity'] . "'";
		} else {
			$gallery_div .= " data-pafe-lightbox-gallery-background-opacity='1'";
		}

		if( isset($settings['facebook']) ) {
			$gallery_div .= " data-pafe-lightbox-gallery-facebook='" . $settings['facebook'] . "'";
		} else {
			if (!empty($settings['facebook'])) {
				$gallery_div .= " data-pafe-lightbox-gallery-facebook='yes'";
			}
		}

		if( isset($settings['tweeter']) ) {
			$gallery_div .= " data-pafe-lightbox-gallery-tweeter='" . $settings['tweeter'] . "'";
		} else {
			if (!empty($settings['tweeter'])) {
				$gallery_div .= " data-pafe-lightbox-gallery-tweeter='yes'";
			}
		}

		if( isset($settings['pinterest']) ) {
			$gallery_div .= " data-pafe-lightbox-gallery-pinterest='" . $settings['pinterest'] . "'";
		} else {
			if (!empty($settings['pinterest'])) {
				$gallery_div .= " data-pafe-lightbox-gallery-pinterest='yes'";
			}
		}

		if( isset($settings['download_image']) ) {
			$gallery_div .= " data-pafe-lightbox-gallery-download-image='" . $settings['download_image'] . "'";
		} else {
			if (!empty($settings['download_image'])) {
				$gallery_div .= " data-pafe-lightbox-gallery-download-image='yes'";
			}
		}

		echo '<style>.pswp {display: none;}</style>';
	?>
	<div class="pafe-lightbox" <?php echo $gallery_div; ?> data-pafe-lightbox-image>
	    <div class="pafe-pswp">
            <a data-href="<?php echo $settings['image']['url']; ?>" class="elementor-image pafe-lightbox__item"  data-width="<?php echo $image['width']; ?>" data-height="<?php echo $image['height']; ?>" data-med="<?php echo $settings['image']['url']; ?>" data-med-size="<?php echo $image['width'] . 'x' .$image['height']; ?>">
                <?php
                    echo \Elementor\Group_Control_Image_Size::get_attachment_image_html( $settings );
                ?>
                <div class="pafe-lightbox__text">
                    <?php if(!empty($title)) : ?>
                        <div class="pafe-lightbox__title"><strong><?php echo $title; ?></strong></div>
                    <?php endif; ?>
                    <?php if(!empty($caption)) : ?>
                        <div class="pafe-lightbox__caption"><?php echo $caption; ?></div>
                    <?php endif; ?>
                </div>
            </a>
        </div>
	</div>
	<?php
	}

}
