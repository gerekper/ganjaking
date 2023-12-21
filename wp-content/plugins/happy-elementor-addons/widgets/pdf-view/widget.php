<?php
/**
 * PDF View widget class
 *
 * @package Happy_Addons
 */
namespace Happy_Addons\Elementor\Widget;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Icons_Manager;
use Elementor\Group_Control_Background;

defined('ABSPATH') || die();

class PDF_View extends Base {

    /**
     * Get widget title.
     *
     * @since 1.0.0
     * @access public
     *
     * @return string Widget title.
     */
    public function get_title() {
        return __('PDF View', 'happy-elementor-addons');
    }

    public function get_custom_help_url() {
        return 'https://happyaddons.com/docs/happy-addons-for-elementor/widgets/pdf-view/';
    }

    /**
     * Get widget icon.
     *
     * @since 1.0.0
     * @access public
     *
     * @return string Widget icon.
     */
    public function get_icon() {
        return 'hm hm-pdf2';
    }

    public function get_keywords() {
        return ['pdf', 'document', 'docs'];
    }

    /**
     * Register widget content controls
     */
    protected function register_content_controls() {
        $this->__pdf_content_controls();
        $this->__pdf_settings_controls();
    }

    protected function __pdf_content_controls() {
        $this->start_controls_section(
            '_section_pdf_content',
            [
                'label' => __('PDF Source', 'happy-elementor-addons'),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );
        
		$this->add_control(
			'file_type',
			[
				'label' => __('File Source', 'happy-elementor-addons'),
				'type' => Controls_Manager::SELECT,
				'options' =>[
					'url' => __('URL', 'happy-elementor-addons'),
					'upload_file' => __('Upload File', 'happy-elementor-addons'),
				],
                'default' => 'url'
			]
		);

		$this->add_control(
			'pdf_url',
			[
				'label' => __('PDF URL', 'happy-elementor-addons'),
				'type' => Controls_Manager::URL,
				'placeholder' => __( 'http://www.example.com/sample.pdf', 'happy-elementor-addons'),
                'default' => [
                    'url' =>  'https://demo.happyaddons.com/wp-content/uploads/2022/03/file-sample.pdf'
                ],
				'show_external' => false,
				'dynamic' => [
					'active' => true,
				],
				'condition' => [
					'file_type' => 'url'
				],
            ]
		);

		$this->add_control(
			'pdf_file',
			[
				'label' => __( 'Choose PDF',  'happy-elementor-addons' ),
				'type' => Controls_Manager::MEDIA,
				'media_type' => 'application/pdf',
                'default' => [
                    'url' => HAPPY_ADDONS_ASSETS . 'vendor/pdfjs/sample.pdf'
                ],
				'dynamic' => [
					'active' => true,
				],
				'condition' => [
					'file_type' => 'upload_file'
				],
			]
		);

		$this->add_control(
			'page_number',
			[
				'label' => __( 'Page Number', 'happy-elementor-addons' ),
				'type' => Controls_Manager::NUMBER,
				'default' => '1',
			]
		);
        
        $this->add_control(
			'pdf_title',
			[
				'label' => __( 'PDF Title', 'happy-elementor-addons' ),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'default' => __( 'PDF Title', 'happy-elementor-addons' ),
				'placeholder' => __( 'Type PDF title', 'happy-elementor-addons' ),
				'separator' => 'before',
				'dynamic' => [
					'active' => true,
				]
			]
		);
        
        $this->end_controls_section();
    }
	protected function __pdf_settings_controls(){
		$this->start_controls_section(
            '_section_pdf_settings',
            [
                'label' => __('Settings', 'happy-elementor-addons'),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );
		$this->add_control(
			'enable_download',
			[
				'label'        => __( 'Show Download?', 'happy-elementor-addons' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			]
		);

        $this->add_control(
			'enable_icon',
			[
				'label'        => __( 'Show Icon?', 'happy-elementor-addons' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			]
		);

        $this->add_control(
			'icon',
			[
				'label' => __( 'Icon', 'happy-elementor-addons' ),
				'type' => Controls_Manager::ICONS,
				'default' => [
					'value' => 'fas fa-file-pdf',
					'library' => 'solid',
				],
                'condition' => [
                    'enable_icon' => 'yes'
                ]
			]
		);
        $this->add_responsive_control(
            'pdf_width',
            [
                'label'      => __('Width', 'happy-elementor-addons'),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => ['%','px',],
                'range'      => [
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                    'px' => [
                        'min'  => 0,
                        'max'  => 2000,
                        'step' => 1,
                    ],
                ],
                'default'    => [
                    'size' => 100,
                    'unit' => '%',
                ]
            ]
        );

        $this->add_responsive_control(
            'pdf_height',
            [
                'label'      => __('Height', 'happy-elementor-addons'),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => ['px', 'vh', 'em'],
                'range'      => [
                    'px' => [
                        'min'  => 0,
                        'max'  => 1000,
                        'step' => 1,
                    ],
                    'em' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                    'vh' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'default'    => [
                    'size' => 600,
                    'unit' => 'px',
                ],
            ]
        );

		$this->add_responsive_control(
			'align',
			[
				'label' => __( 'Alignment', 'happy-elementor-addons' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => __( 'Left', 'happy-elementor-addons' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'happy-elementor-addons' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'happy-elementor-addons' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'toggle' => true,
				'selectors' => [
					'{{WRAPPER}} .elementor-widget-container' => 'text-align: {{VALUE}};'
				]
			]
		);
        
        $this->end_controls_section();
	}

    /**
     * Register widget style controls
     */
    protected function register_style_controls() {
        $this->__title_desc_style_controls();
        $this->__button_style_controls();
        // $this->__icon_style_controls();
    }

    protected function __title_desc_style_controls() {

		$this->start_controls_section(
			'_section_style_content',
			[
				'label' => __( 'Title', 'happy-elementor-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'title_color',
			[
				'label' => __( 'Text Color', 'happy-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-pdf-title' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
				'label' => __( 'Typography', 'happy-elementor-addons' ),
				'selector' => '{{WRAPPER}} .ha-pdf-title',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_SECONDARY,
				],
			]
		);

		$this->add_control(
			'_heading_title_bar',
			[
				'type' => Controls_Manager::HEADING,
				'label' => __( 'Title Bar', 'happy-elementor-addons' ),
				'separator' => 'before'
			]
		);
		
		$this->add_responsive_control(
			'title_spacing',
			[
				'label' => __( 'Bottom Spacing', 'happy-elementor-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'selectors' => [
					'{{WRAPPER}} .pdf_viewer_options' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'title_background',
				'label' => __( 'Background', 'happy-elementor-addons' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .pdf_viewer_options',
			]
		);

		$this->add_responsive_control(
			'title_bar_padding',
			[
				'label' => __( 'Padding', 'happy-elementor-addons' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .pdf_viewer_options' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'title_bar_radius',
			[
				'label' => __( 'Border Radius', 'happy-elementor-addons' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .pdf_viewer_options' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'icon_heading',
			[
				'label' => __( 'Icon', 'happy-elementor-addons' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'icon_color',
			[
				'label' => __( 'Icon Color', 'happy-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pdf-icon' => 'color: {{VALUE}}',
					'{{WRAPPER}} .pdf-icon svg' => 'fill: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'icon_size',
			[
				'label' => __( 'Icon Size', 'happy-elementor-addons' ),
				'type' => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .pdf-icon' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .pdf-icon svg' => 'width: {{SIZE}}{{UNIT}}; height: auto;'
				],
				'default' => [
					'size' => 30
				]
			]
		);

		$this->add_responsive_control(
			'icon_spacing',
			[
				'label' => __( 'Icon Spacing', 'happy-elementor-addons' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'default' => [
					'size' => 10
				],
				'selectors' => [
					'{{WRAPPER}} .pdf-icon' => 'margin-right: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function __button_style_controls() {

		$this->start_controls_section(
			'_section_style_button',
			[
				'label' => __( 'Button', 'happy-elementor-addons' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'button_padding',
			[
				'label' => __( 'Padding', 'happy-elementor-addons' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .ha-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'button_typography',
				'selector' => '{{WRAPPER}} .ha-btn',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_ACCENT,
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'button_border',
				'selector' => '{{WRAPPER}} .ha-btn',
			]
		);

		$this->add_control(
			'button_border_radius',
			[
				'label' => __( 'Border Radius', 'happy-elementor-addons' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .ha-btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'hr',
			[
				'type' => Controls_Manager::DIVIDER,
				'style' => 'thick',
			]
		);

		$this->start_controls_tabs( '_tabs_button' );

		$this->start_controls_tab(
			'_tab_button_normal',
			[
				'label' => __( 'Normal', 'happy-elementor-addons' ),
			]
		);

		$this->add_control(
			'button_color',
			[
				'label' => __( 'Text Color', 'happy-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .ha-btn' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_bg_color',
			[
				'label' => __( 'Background Color', 'happy-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-btn' => 'background-color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'button_box_shadow',
				'selector' => '{{WRAPPER}} .ha-btn',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'_tab_button_hover',
			[
				'label' => __( 'Hover', 'happy-elementor-addons' ),
			]
		);

		$this->add_control(
			'button_hover_color',
			[
				'label' => __( 'Text Color', 'happy-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-btn:hover, {{WRAPPER}} .ha-btn:focus' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_hover_bg_color',
			[
				'label' => __( 'Background Color', 'happy-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-btn:hover, {{WRAPPER}} .ha-btn:focus' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_hover_border_color',
			[
				'label' => __( 'Border Color', 'happy-elementor-addons' ),
				'type' => Controls_Manager::COLOR,
				'condition' => [
					'button_border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .ha-btn:hover, {{WRAPPER}} .ha-btn:focus' => 'border-color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'button_box_shadow_hover',
				'selector' => '{{WRAPPER}} .ha-btn:hover',
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();
	}
   
    /**
     * @return null
     */
    protected function render() {
        $settings = $this->get_settings_for_display();
        $unique_id = 'viewer-' . $this->get_id();
		$file_type = $settings['file_type'];

        $pdf_url_i = '';

        if('url' == $file_type){
            $pdf_url_i =  $settings['pdf_url']['url'];
        }else{
            $pdf_url_i =  $settings['pdf_file']['url'];
        }

		if (isset($settings['pdf_width'])) {
			$width = $settings['pdf_width']['size'] . $settings['pdf_width']['unit'];
		}
		if (isset($settings['pdf_height'])) {
			$height = $settings['pdf_height']['size'] . $settings['pdf_height']['unit'];
		}

		if(empty($pdf_url_i)){
			$pdf_url_i = HAPPY_ADDONS_ASSETS . 'vendor/pdfjs/sample.pdf';
		}

		$json_settings = [
            'unique_id' 	=> $unique_id,
            'pdf_url' 		=> $pdf_url_i,
			'file_type' 	=> $file_type,
			'page_number' 	=> $settings['page_number'],
			'width' 		=> $width,
			'height' 		=> $height,
        ];



		$this->add_render_attribute( 'pdf_settings', 'data-pdf-settings', wp_json_encode( $json_settings ) );
        
        ?>
        <div class="pdf_viewer_container">
            <div class="pdf_viewer_options <?php echo esc_attr($unique_id); ?>" <?php $this->print_render_attribute_string( 'pdf_settings' ) ?>>
				<span class="ha-title-flex">
					<span class="pdf-icon">
						<?php Icons_Manager::render_icon($settings['icon'], ['aria-hidden' => 'true']);  ?>
					</span>
					<?php
					if($settings['pdf_title']){
						echo sprintf( '<h2 class="ha-pdf-title">%s</h2>',
						esc_html( $settings['pdf_title'] )
						);
					}
					?>
				</span>
				<?php 
                ?>
                <div class="pdf-button">
                <?php
                    if('yes' == $settings['enable_download']){
                        printf( '<a href="%1$s" class="ha-btn" download title="%2$s">%3$s</a>',
                            esc_url($pdf_url_i),
                            esc_html( $settings['pdf_title'] ),
                            __('Download', 'happy-elementor-addons')
                        );
                    }
                ?>
                </div>
            </div>
            
			<div>
				<div id="<?php echo esc_attr( $unique_id ); ?>"></div>
			</div>
			
        </div>
        <?php
	}

}
