<?php

class PAFE_Lightbox_Gallery extends \Elementor\Widget_Base {

	public function get_name() {
		return 'pafe-lightbox-gallery';
	}

	public function get_title() {
		return __( 'PAFE Lightbox Gallery', 'pafe' );
	}

	public function get_icon() {
		return 'eicon-gallery-grid';
	}

	public function get_categories() {
		return [ 'pafe' ];
	}

	public function get_keywords() {
		return [ 'image', 'photo', 'visual', 'gallery' ];
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
			'section_gallery',
			[
				'label' => __( 'Image Gallery', 'elementor' ),
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
			'wp_gallery',
			[
				'label' => __( 'Add Images', 'elementor' ),
				'type' => \Elementor\Controls_Manager::GALLERY,
				'show_label' => false,
				'dynamic' => [
					'active' => true,
				],
			]
		);

		// $this->add_control(
		// 	'pafe_lightbox_gallery_custom_field',
		// 	[
		// 		'label' => __( 'Get images from Custom Field', 'pafe' ),
		// 		'type' => \Elementor\Controls_Manager::SWITCHER,
		// 		'description' => __('The value structure of the custom field must be like this "url1,url2,url3". And type of the custom field is text. You should use this feature when use Image Upload field + Submit Post Feature and use Theme Builder to build Post Template.','pafe'),
		// 		'default' => '',
		// 		'label_on' => 'Yes',
		// 		'label_off' => 'No',
		// 		'return_value' => 'yes',
		// 	]
		// );

		// $this->add_control(
		// 	'pafe_lightbox_gallery_custom_field_source',
		// 	[
		// 		'label' => __( 'Custom Field', 'elementor' ),
		// 		'type' => \Elementor\Controls_Manager::SELECT,
		// 		'options' => [
		// 			'post_custom_field' => __( 'Post Custom Field', 'pafe' ),
		// 			'acf_field' => __( 'ACF Field', 'pafe' ),
		// 		],
		// 		'default' => 'post_custom_field',
		// 		'condition' => [
		// 			'pafe_lightbox_gallery_custom_field' => 'yes',
		// 		],
		// 	]
		// );

		// $this->add_control(
		// 	'pafe_lightbox_gallery_custom_field_key',
		// 	[
		// 		'label' => __( 'Custom Field Key', 'pafe' ),
		// 		'type' => \Elementor\Controls_Manager::TEXT,
		// 		'condition' => [
		// 			'pafe_lightbox_gallery_custom_field' => 'yes',
		// 		],
		// 	]
		// );

		$this->add_group_control(
			\Elementor\Group_Control_Image_Size::get_type(),
			[
				'name' => 'thumbnail', // Usage: `{name}_size` and `{name}_custom_dimension`, in this case `thumbnail_size` and `thumbnail_custom_dimension`.
				'exclude' => [ 'custom' ],
				'separator' => 'none',
			]
		);

		$this->add_control(
			'thumbnail_custom_size',
			[
				'label' => __( 'Thumbnail Custom Size', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => '',
				'label_on' => 'Yes',
				'label_off' => 'No',
				'return_value' => 'yes',
			]
		);

		$this->add_control(
			'thumbnail_aspect_ratio',
			[
				'label' => __( 'Thumbnail Aspect Ratio', 'pafe' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'description' => 'Aspect Ratio = Height / Width * 100. E.g Width = 100, Height = 100 => Ratio = 1; Width = 100, Height = 50 => Ratio = 50',
				'default' => 100,
				'selectors' => [
					'{{WRAPPER}} .pafe-lightbox-gallery__item-inner::before' => 'content: ""; display: block; padding-top: {{VALUE}}%',
				],
				'condition' => [
					'thumbnail_custom_size' => 'yes',
				],
			]
		);

		$this->add_control(
			'thumbnail_first',
			[
				'label' => __( 'Show only the first thumbnail', 'pafe' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => '',
				'label_on' => 'Yes',
				'label_off' => 'No',
				'return_value' => 'yes',
			]
		);

		$gallery_columns = range( 1, 10 );
		$gallery_columns = array_combine( $gallery_columns, $gallery_columns );

		$this->add_control(
			'gallery_columns',
			[
                'label' => __( 'Column Width (%)', 'pafe' ),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 50,
                'min' => 1,
                'max' => 100,
                'selectors' => [
                    '{{WRAPPER}} .pafe-pswp' => 'width: {{VALUE}}%; max-width: 100%;',
                    '{{WRAPPER}} .pafe-pswp .gallery-item' => 'width:100%;',
                ],
			]
		);

		$this->add_control(
			'gallery_rand',
			[
				'label' => __( 'Ordering', 'elementor' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'' => __( 'Default', 'elementor' ),
					'rand' => __( 'Random', 'elementor' ),
				],
				'default' => '',
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
			'masonry',
			[
				'label' => __( 'Enable Masonry', 'pafe' ),
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
			'title_key',
			[
				'label' => __( 'Title Key', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'condition' => [
					'title_source' => [
						'custom_field',
						'acf_field',
					]
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
			'caption_key',
			[
				'label' => __( 'Caption Key', 'pafe' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'condition' => [
					'caption_source' => [
						'custom_field',
						'acf_field',
					]
				]
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
			'section_gallery_images',
			[
				'label' => __( 'Images', 'elementor' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'image_spacing',
			[
				'label' => __( 'Spacing', 'elementor' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'' => __( 'Default', 'elementor' ),
					'custom' => __( 'Custom', 'elementor' ),
				],
				'prefix_class' => 'gallery-spacing-',
				'default' => '',
			]
		);

		$columns_margin = is_rtl() ? '0 0 -{{SIZE}}{{UNIT}} -{{SIZE}}{{UNIT}};' : '0 -{{SIZE}}{{UNIT}} -{{SIZE}}{{UNIT}} 0;';
		$columns_padding = is_rtl() ? '0 0 {{SIZE}}{{UNIT}} {{SIZE}}{{UNIT}};' : '0 {{SIZE}}{{UNIT}} {{SIZE}}{{UNIT}} 0;';

		$this->add_control(
			'image_spacing_custom',
			[
				'label' => __( 'Image Spacing', 'elementor' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'show_label' => false,
				'range' => [
					'px' => [
						'max' => 100,
					],
				],
				'default' => [
					'size' => 15,
				],
				'selectors' => [
					'{{WRAPPER}} .gallery-item' => 'padding:' . $columns_padding,
					'{{WRAPPER}} .gallery' => 'margin: ' . $columns_margin,
				],
				'condition' => [
					'image_spacing' => 'custom',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name' => 'image_border',
				'selector' => '{{WRAPPER}} .gallery-item img',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'image_border_radius',
			[
				'label' => __( 'Border Radius', 'elementor' ),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .gallery-item img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

	}

	public function add_lightbox_data_to_image_link_pafe( $link_html ) {
		$link = preg_replace( '/^<a/', '<a ' . $this->get_render_attribute_string( 'link' ), $link_html );
		return str_replace('href=', 'data-href=', $link);
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$ids = array();

		if ( ! $settings['wp_gallery'] && empty($settings['pafe_lightbox_gallery_custom_field']) && empty($settings['pafe_lightbox_gallery_custom_field_key']) ) {
			return;
		}

		if ( $settings['wp_gallery'] ) {
			$ids = wp_list_pluck( $settings['wp_gallery'], 'id' );
		}

		if (!empty($settings['pafe_lightbox_gallery_custom_field']) && !empty($settings['pafe_lightbox_gallery_custom_field_key'])) {
			if (function_exists('update_field') && $settings['pafe_lightbox_gallery_custom_field_source'] == 'acf_field') {
				$images_url = get_field( $settings['pafe_lightbox_gallery_custom_field_key'], get_the_ID());
			}
			if ($settings['pafe_lightbox_gallery_custom_field_source'] == 'post_custom_field') {
				$images_url = get_post_meta(get_the_ID(), $settings['pafe_lightbox_gallery_custom_field_key'], true);
			}
			if (!empty($images_url)) {
				$images_url_array = explode(',', $images_url);
				$ids = array();
				foreach ($images_url_array as $images_url_item) {
					$ids[] = attachment_url_to_postid($images_url_item);
				}
			}
		}

		if ( empty($ids) ) {
			return;
		}

		$this->add_render_attribute( 'shortcode', 'ids', implode( ',', $ids ) );
		$this->add_render_attribute( 'shortcode', 'size', $settings['thumbnail_size'] );

		if ( ! empty( $settings['gallery_columns'] ) ) {
			$this->add_render_attribute( 'shortcode', 'columns', $settings['gallery_columns'] );
		}

		if ( ! empty( $settings['gallery_link'] ) ) {
			$this->add_render_attribute( 'shortcode', 'link', $settings['gallery_link'] );
		}

		if ( ! empty( $settings['gallery_rand'] ) ) {
			$this->add_render_attribute( 'shortcode', 'orderby', $settings['gallery_rand'] );
		}

		if ( ! empty( $settings['title_source'] ) ) {
			$this->add_render_attribute( 'shortcode', 'title_source', $settings['title_source'] );
		}

		if ( ! empty( $settings['title_key'] ) ) {
			$this->add_render_attribute( 'shortcode', 'title_key', $settings['title_key'] );
		}

		if ( ! empty( $settings['caption_source'] ) ) {
			$this->add_render_attribute( 'shortcode', 'caption_source', $settings['caption_source'] );
		}

		if ( ! empty( $settings['caption_key'] ) ) {
			$this->add_render_attribute( 'shortcode', 'caption_key', $settings['caption_key'] );
		}

		if ( ! empty( $settings['thumbnail_custom_size'] ) ) {
			$this->add_render_attribute( 'shortcode', 'thumbnail_custom_size', $settings['thumbnail_custom_size'] );
		}

		if ( ! empty( $settings['thumbnail_first'] ) ) {
			$this->add_render_attribute( 'shortcode', 'thumbnail_first', $settings['thumbnail_first'] );
		}

		if ( ! empty( $settings['masonry'] ) ) {
			$this->add_render_attribute( 'shortcode', 'masonry', $settings['masonry'] );
		}

		if ( ! empty( $settings['light_skin'] ) ) {
			$this->add_render_attribute( 'shortcode', 'light_skin', $settings['light_skin'] );
		}

		if ( ! empty( $settings['background_color'] ) ) {
			$this->add_render_attribute( 'shortcode', 'background_color', $settings['background_color'] );
		}

		if ( ! empty( $settings['background_opacity'] ) ) {
			$this->add_render_attribute( 'shortcode', 'background_opacity', $settings['background_opacity'] );
		}

		if ( ! empty( $settings['facebook'] ) ) {
			$this->add_render_attribute( 'shortcode', 'facebook', $settings['facebook'] );
		}

		if ( ! empty( $settings['tweeter'] ) ) {
			$this->add_render_attribute( 'shortcode', 'tweeter', $settings['tweeter'] );
		}

		if ( ! empty( $settings['pinterest'] ) ) {
			$this->add_render_attribute( 'shortcode', 'pinterest', $settings['pinterest'] );
		}

		if ( ! empty( $settings['download_image'] ) ) {
			$this->add_render_attribute( 'shortcode', 'download_image', $settings['download_image'] );
		}

		echo '<style>.pswp {display: none;}</style>';
		
		?>
		<div class="elementor-image-gallery pafe-lightbox-gallery" data-pafe-lightbox-gallery>
			<?php
			// $this->add_render_attribute( 'link', [
			// 	'data-elementor-open-lightbox' => $settings['open_lightbox'],
			// 	'data-elementor-lightbox-slideshow' => $this->get_id(),
			// ] );

			if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
				$this->add_render_attribute( 'link', [
					'class' => 'elementor-clickable',
				] );
			}
			add_filter( 'wp_get_attachment_link', [ $this, 'add_lightbox_data_to_image_link_pafe' ] );

			echo do_shortcode( '[pafe_gallery ' . $this->get_render_attribute_string( 'shortcode' ) . ']' );

			remove_filter( 'wp_get_attachment_link', [ $this, 'add_lightbox_data_to_image_link_pafe' ] );
			?>
		</div>
		<?php
	}
}
