<?php 
/*
Widget Name: scroll_sequence
Description: scroll_sequence
Author: Theplus
Author URI: https://posimyth.com
*/

namespace TheplusAddons\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Utils;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Image_Size;

use TheplusAddons\Theplus_Element_Load; 

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class ThePlus_Scroll_Sequence extends Widget_Base {

	public $TpDoc = THEPLUS_TPDOC;

	public function get_name() {
		return 'tp-scroll-sequence';
	}

    public function get_title() {
        return esc_html__('Scroll Sequence', 'theplus');
    }

    public function get_icon() {
        return 'fa fa-scroll-sequence theplus_backend_icon';
    }

    public function get_categories() {
        return array('plus-essential');
    }

	public function get_keywords() {
		return ['Scroll Sequence', 'Image Sequence', 'Video Scroll Sequence', 'Image Scroll Sequence', 'Cinematic Scroll Sequence', 'Cinematic Scroll Animation', 'Image Scroll Animation'];
	}

	public function get_custom_help_url() {
		$DocUrl = $this->TpDoc . "scroll-sequence";

		return esc_url($DocUrl);
	}

	public function is_reload_preview_required() {
		return true;
	}

    protected function register_controls() {

		$this->start_controls_section('scroll_sequence_tab_content',
			[
				'label' => esc_html__( 'Scroll Sequence', 'theplus' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_control('applyTo',
			[
				'label' => esc_html__( 'Apply To', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'default',
				'options' => [
					'default'  => esc_html__( 'Default', 'theplus' ),
					'body' => esc_html__( 'Body', 'theplus' ),
					'innerContainer'  => esc_html__( 'Inner Column', 'theplus' ),
				],
			]
		);
		$this->add_control('applbodyNote',
			[				
				'type' => Controls_Manager::RAW_HTML,
				'raw' => wp_kses_post( "Note : To make content visible, You need to set z-index value to 1 from Style tab of this widget. <a class='tp-docs-link' href='" . esc_url($this->TpDoc) . "add-image-sequence-on-page-body-in-elementor/?utm_source=wpbackend&utm_medium=elementoreditor&utm_campaign=widget' target='_blank' rel='noopener noreferrer'> <i class='eicon-help-o'></i> </a>", 'theplus' ),
				'content_classes' => 'tp-widget-description',
				'condition' => [
					'applyTo' => 'body',
				],
			]
		);
		$this->add_control('imageUpldType',
			[
				'label' => esc_html__( 'Upload Type', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'gallery',
				'options' => [
					'gallery' => esc_html__( 'Gallery', 'theplus' ),
					'server' => esc_html__( 'Remote Server', 'theplus' ),
				],
			]
		);
		$this->add_control('imgUpldNote',
			[				
				'type' => \Elementor\Controls_Manager::RAW_HTML,
				'raw' => esc_html__( 'Note : Only Image files are accepted here.', 'theplus' ),
				'content_classes' => 'tp-widget-description',	
			]
		);
		$this->add_control('imageGallery',
			[
				'label' => esc_html__( 'Image Upload', 'theplus' ),
				'type' => Controls_Manager::GALLERY,
				'default' => [],
				'condition' => [
					'imageUpldType' => 'gallery',
				],
			]
		);
        $this->add_control('imagePath',
			[
				'label' => wp_kses_post( "Folder Path <a class='tp-docs-link' href='" . esc_url($this->TpDoc) . "create-image-sequence-scroll-animation-from-a-url/?utm_source=wpbackend&utm_medium=elementoreditor&utm_campaign=widget' target='_blank' rel='noopener noreferrer'> <i class='eicon-help-o'></i> </a>", 'theplus' ),
				'type' => Controls_Manager::URL,
				'dynamic' => [
					'active' => true,
				],
				'placeholder' => esc_html__( 'https://www.demo-link.com', 'theplus' ),							
				'condition' => [
					'imageUpldType' => 'server',
				],
			]
		);
		$this->add_control('imagePathNote',
			[				
				'type' => \Elementor\Controls_Manager::RAW_HTML,
				'raw' => 'Note : Include full folder path. Add digits in increment mode at the end of each image to load all in sequence. <br> e.g. https://xyz.com/uploads/image(001).jpg and so on.',
				'content_classes' => 'tp-widget-description',
				'condition' => [
					'imageUpldType' => 'server',
				],
			]
		);
		
		$this->add_control('imagePrefix',
			[
				'label' => esc_html__( 'Prefix', 'theplus' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Enter your prefix', 'theplus' ),
				'condition' => [
					'imageUpldType' => 'server',
				],
			]
		);
		$this->add_control('prefixtxtNote',
			[				
				'type' => \Elementor\Controls_Manager::RAW_HTML,
				'raw' => 'Note : Enter the name of image your have used above without digits. e.g. image.',
				'content_classes' => 'tp-widget-description',
				'condition' => [
					'imageUpldType' => 'server',
				],
			]
		);
		$this->add_control('imageDigit',
			[
				'label' => esc_html__( 'Digit', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => '1',
				'options' => [
					'1' => esc_html__( '1-9', 'theplus' ),
					'2' => esc_html__( '01-99', 'theplus' ),
					'3' => esc_html__( '001-999', 'theplus' ),
					'4' => esc_html__( '0001-9999', 'theplus' ),
				],
				'condition' => [
					'imageUpldType' => 'server',
				],
			]
		);
		$this->add_control('imageDigitNote',
			[				
				'type' => \Elementor\Controls_Manager::RAW_HTML,
				'raw' => 'Note : Choose right number of digits based on your total number of images. e.g. If you are having 39 images, choose 01-99.',
				'content_classes' => 'tp-widget-description',
				'condition' => [
					'imageUpldType' => 'server',
				],
			]
		);
        $this->add_control('imageType',
			[
				'label' => esc_html__( 'Image Type', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'jpg',
				'options' => [
					'jpg' => esc_html__( 'JPG', 'theplus' ),
					'png'  => esc_html__( 'PNG', 'theplus' ),
					'webp'  => esc_html__( 'WebP', 'theplus' ),
				],
				'condition' => [
					'imageUpldType' => 'server',
				],
			]
		);
        $this->add_control('totalImage',
			[
				'label' => esc_html__( 'Total Image', 'theplus' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 1,
				'max' => 5000,
				'step' => 1,
				'default' => 20,
				'condition' => [
					'imageUpldType' => 'server',
				],
			]            
		);
		$this->end_controls_section();
		$this->start_controls_section('extra_Opt_section',
			[
				'label' => esc_html__( 'Extra Option', 'theplus' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_control('preloadImg',
			[
				'label' => wp_kses_post( "Preload Image (%) <a class='tp-docs-link' href='" . esc_url($this->TpDoc) . "scroll-sequence-elementor-widget-settings-overview/?utm_source=wpbackend&utm_medium=elementoreditor&utm_campaign=widget' target='_blank' rel='noopener noreferrer'> <i class='eicon-help-o'></i> </a>", 'theplus' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'min' => 1,
				'max' => 100,
				'step' => 1,
				'default' => 20,
			]            
		);
		$this->add_control('stickySec',
			[
				'label' => wp_kses_post( "Sticky Sections <a class='tp-docs-link' href='" . esc_url($this->TpDoc) . "add-sticky-sections-in-image-scroll-sequence/?utm_source=wpbackend&utm_medium=elementoreditor&utm_campaign=widget' target='_blank' rel='noopener noreferrer'> <i class='eicon-help-o'></i> </a>", 'theplus' ),
				'type' =>  Controls_Manager::SWITCHER,
				'default' => '',
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_on' => esc_html__( 'Off', 'theplus' ),	
			]
		);
		$repeater = new \Elementor\Repeater();
		$repeater->add_control('sectionId',
			[
				'label' => esc_html__( 'Section ID', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'title' => 'Add Section Id to make content sticky',
			]
		);
		$repeater->add_control('secStart',
			[
				'label' => esc_html__( 'Start', 'theplus' ),
				'type' => Controls_Manager::NUMBER,
				'title' => 'Enter image number where element becomes visible',
			]
		);
		$repeater->add_control('secEnd',
			[
				'label' => esc_html__( 'End', 'theplus' ),
				'type' => Controls_Manager::NUMBER,
				'title' => 'Enter image number where element becomes Hidden',
			]
		);
		$repeater->add_control('offsetop',
			[
				'label' => esc_html__( 'Top (%)', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ '%' ],
				'range' => [
					'%' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => '%',
					'size' => '5',
				],
			]
		);
		$repeater->add_control('secAnimationstart',
			[
				'label' => esc_html__('Start Animation', 'theplus'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'none' => esc_html__('None', 'theplus'),
					'tp-fadein' => esc_html__('Fade In', 'theplus'),
					'tp-fadeinup' => esc_html__('Fade In Up', 'theplus'),
					'tp-fadeindown' => esc_html__('Fade In Down', 'theplus'),
					'tp-fadeinleft' => esc_html__('Fade In Left', 'theplus'),
					'tp-fadeinright' => esc_html__('Fade In Right', 'theplus'),
					'tp-rotatein' => esc_html__('Rotate In', 'theplus'),
				],
				'default' => 'none',
			]
		);
		$repeater->add_control('secAnimationend',
			[
				'label' => esc_html__('End Animation', 'theplus'),
				'type'  => Controls_Manager::SELECT,
				'options' => [
					'none' => esc_html__('None', 'theplus'),
					'tp-fadeout' => esc_html__('Fade Out', 'theplus'),
					'tp-fadeoutup' => esc_html__('Fade Out Up', 'theplus'),
					'tp-fadeoutdown' => esc_html__('Fade Out Down', 'theplus'),
					'tp-fadeoutleft' => esc_html__('Fade Out Left', 'theplus'),
					'tp-fadeoutright' => esc_html__('Fade Out Right', 'theplus'),
					'tp-rotateout' => esc_html__('Rotate Out', 'theplus'),
				],
				'default' => 'none',
			]
		);
		$this->add_control('seclist',
			[
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'condition' => [
					'stickySec' => 'yes'
				],
				'default' => [
                    [
                        'secAnimationstart' => esc_html__( 'none', 'theplus' ),
                        'secAnimationend' => esc_html__( 'none', 'theplus' ),
                    ],
                ],
				'title_field' => '{{{ sectionId }}}',
			]
		);
		$this->end_controls_section();

		/**Scroll Sequence Style Start*/
		$this->start_controls_section('scroll_seq_section_style',
            [
                'label' => esc_html__('Scroll Sequence', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
		$this->add_control('canVidPosition',
			[
				'label'=>__('Position','theplus'),
				'type'=>Controls_Manager::POPOVER_TOGGLE,
				'label_off'=>__('Default','theplus'),
				'label_on'=>__('Custom','theplus'),
				'return_value'=>'yes',
				'default'=>'no',
			]
		);
		$this->start_popover();
		$this->add_responsive_control('posTop',
			[
				'label' => esc_html__( 'Top (%)', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ '%' ],
				'range' => [
					'%' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
				],	
				'default' => [
					'unit' => '%',
					'size' => '',
				],
				'render_type' => 'ui',			
				'selectors' => [
					'.tp-scroll-seq-inner.elementor-element-{{ID}}-canvas' => 'top: {{SIZE}}% !important;',					
				],
				'condition' => [
					'canVidPosition' => 'yes',
				],
			]
		);
		$this->add_responsive_control('posLeft',
			[
				'label' => esc_html__( 'Left (%)', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ '%' ],
				'range' => [
					'%' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
				],	
				'default' => [
					'unit' => '%',
					'size' => '',
				],
				'render_type' => 'ui',			
				'selectors' => [
					'.tp-scroll-seq-inner.elementor-element-{{ID}}-canvas' => 'left: {{SIZE}}% !important;',					
				],
				'condition' => [
					'canVidPosition' => 'yes',
				],
			]
		);
		$this->end_popover();
		$this->add_control('canVidZIndex',
			[
				'label' => esc_html__( 'Z-Index', 'theplus' ),
				'type' => Controls_Manager::NUMBER,
				'min' => -10,
				'max' => 10,
				'step' => 1,
				'default'=> 0,				
				'selectors' => [
					'.tp-scroll-seq-inner.elementor-element-{{ID}}-canvas' => 'z-index: {{VALUE}} !important;',
				],
			]
		);
		$this->add_responsive_control('canVidWidth',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Width', 'theplus'),
				'size_units' => [ 'px','%' ],
				'range' => [
					'%' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 2,
					],

				],
				'default' => [
					'unit' => 'px',
					'size' => '',
				],
				'render_type' => 'ui',
				'selectors' => [
					'.tp-scroll-seq-inner.elementor-element-{{ID}}-canvas' => 'width: {{SIZE}}{{UNIT}} !important;',

				],
            ]
        );
		$this->add_responsive_control('canVidHeight',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Height', 'theplus'),
				'size_units' => [ 'px','%' ],
				'range' => [
					'%' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 2,
					],

				],
				'default' => [
					'unit' => 'px',
					'size' => '',
				],
				'render_type' => 'ui',
				'selectors' => [
					'.tp-scroll-seq-inner.elementor-element-{{ID}}-canvas' => 'height: {{SIZE}}{{UNIT}} !important;',
				],
            ]
        );
		$this->add_responsive_control('canStartOffset',
			[
				'label' => esc_html__( 'Start Offset (px)', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => -1000,
						'max' => 1000,
						'step' => 10,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => '',
				],
				'render_type' => 'ui',				
			]
		);
		$this->add_responsive_control('canEndOffset',
			[
				'label' => esc_html__( 'End Offset (px)', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => -1000,
						'max' => 1000,
						'step' => 10,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => '',
				],
				'render_type' => 'ui',				
			]
		);
		$this->end_controls_section();
		/**Scroll Sequence Style End*/

		include THEPLUS_PATH . 'modules/widgets/theplus-needhelp.php';
    }

	protected function render() { 	
		$settings = $this->get_settings_for_display();
		$uid_scroll = uniqid("tp-scs");
		$imageUpldType = !empty($settings['imageUpldType']) ? $settings['imageUpldType'] : 'gallery';
		$imageGallery = !empty($settings['imageGallery']) ? $settings['imageGallery'] : [];
		$imagePath = !empty($settings['imagePath']['url']) ? $settings['imagePath']['url'] : '';
		$imagePrefix = !empty($settings['imagePrefix']) ? $settings['imagePrefix'] : '';
		$imageDigit = !empty($settings['imageDigit']) ? $settings['imageDigit'] : '1';
		$imageType = !empty($settings['imageType']) ? $settings['imageType'] : 'jpg';
		$totalImage = !empty($settings['totalImage']) ? $settings['totalImage'] : 20;
		$applyTo = !empty($settings['applyTo']) ? $settings['applyTo'] : 'default';
		$preloadImg = !empty($settings['preloadImg']) ? $settings['preloadImg'] : 20;		
		$canStartOffset = !empty($settings['canStartOffset']['size']) ? $settings['canStartOffset']['size'] : 0;
		$canEndOffset = isset($settings['canEndOffset']['size']) ? $settings['canEndOffset']['size'] : 0;
		$stickySec = !empty($settings['stickySec']) ? $settings['stickySec'] : 0;
		$seclist = !empty($settings['seclist']) ? $settings['seclist'] : '';
		$data_attr=$imgGlr=[];

		if($imageUpldType == 'gallery' && !empty($imageGallery)){
			$imgGlr = array_column($imageGallery, 'url');
		}else if( !empty($imagePath) && !empty($totalImage)){
			
			for($i=1; $i<=$totalImage; $i++){
				$immm = str_pad($i, $imageDigit, '0', STR_PAD_LEFT);
				$ImgURL = $imagePath.'/'.$imagePrefix.$immm.'.'.$imageType;

				$URLexists = @file_get_contents($ImgURL);
				if( !empty($URLexists) ){
					$imgGlr[] = $ImgURL;
				}
			}
		}

		$Massage=$icon='';
		if(!empty($imgGlr)){
			$data_attr = array(
				'widget_id' => $this->get_id(),
				'imgGellary' => $imgGlr,
				'applyto' => esc_attr($applyTo),
				'imgUpdType' => esc_attr($imageUpldType),
				'preloadImg' => esc_attr($preloadImg),
				'startOffset' => esc_attr($canStartOffset),
				'endOffset' => esc_attr($canEndOffset),
				'stickySec' => esc_attr($stickySec),
				'seclist' => $seclist
			);				
		}else{
			$ErrorTitle = 'No Image Selected!';
			$ErrorMassage = 'Please Select Image To Get The Desired Result';

			$Massage = theplus_get_widgetError($ErrorTitle, $ErrorMassage);
		}

		$data_attr = 'data-attr="'.htmlspecialchars(json_encode($data_attr, true), ENT_QUOTES, 'UTF-8').'"';
		
		$output = '';
		$output .= '<div class="tp-scroll-sequence tp-widget-'.esc_attr($uid_scroll).'" '.$data_attr.'>';
			$output .= $Massage;
		$output .= '</div>';

		echo $output;
	}
}
?>