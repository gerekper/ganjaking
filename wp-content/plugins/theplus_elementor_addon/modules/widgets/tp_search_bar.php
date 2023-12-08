<?php 
/*
Widget Name: TP Search Bar
Description: Content of text text block.
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
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

use TheplusAddons\Theplus_Element_Load;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class ThePlus_Search_Bar extends Widget_Base {
		
	public function get_name() {
		return 'tp-search-bar';
	}

    public function get_title() {
        return esc_html__('WP Search Bar', 'theplus');
    }

    public function get_icon() {
        return 'fa fa-search theplus_backend_icon';
    }

    public function get_categories() {
        return array('plus-search-filter');
    }

	public function get_keywords() {
		return ['search bar','search','bar','wp search'];
	}

    protected function register_controls() {

		$this->start_controls_section('Custom_section',
			[
				'label' => esc_html__( 'Search Bar Fields', 'theplus' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);
		$repeater = new \Elementor\Repeater();
		$repeater->add_control('sourceType',
			[
				'label'=>__('Source','theplus'),
				'type'=>Controls_Manager::SELECT,
				'default'=>'',
				'options'=>[
					''=>__('Select Source','theplus'),
					'post'=>__('Post','theplus'),
					'taxonomy'=>__('Taxonomy','theplus'),
				],
			]
		);
		$repeater->add_control('postType',
			[
				'label'=>__('Select Type','theplus'),
				'type'=>Controls_Manager::SELECT2,
				'multiple'=>true,
				'options'=>theplus_get_post_type(), 
				'default'=>array('post'),
				'condition' => [
                    'sourceType'=>'post',
				],
			]
		);
		$repeater->add_control('TaxonomyType',
			[
				'label'=>esc_html__('Select Taxonomy','theplus'),
				'type'=>Controls_Manager::SELECT,
				'default'=>'',
                'options'=>theplus_get_post_taxonomies(),
                'condition'=>[
                    'sourceType'=>'taxonomy',
				],
			]
		);
		$repeater->add_control('fieldLabel',
			[
				'label'=>__('Label','theplus'),
				'type'=>Controls_Manager::TEXT,
				'default'=>__('Label','theplus'),
				'placeholder'=>__('Type your title here','theplus'),
			]
		);
		$repeater->add_control('DefText',
			[
				'label'=>__('Placeholder','theplus'),
				'type'=>Controls_Manager::TEXT,
				'default'=>__('All Posts','theplus'),
				'placeholder'=>__('Enter Value','theplus'),
			]
		);

		$repeater->add_control('showCount',
			[
				'label'=>esc_html__('Show Index','theplus'),
				'type'=>Controls_Manager::SWITCHER,
				'label_on'=>esc_html__('Show','theplus'),
				'label_off'=>esc_html__('Hide','theplus'),
				'default'=>'yes',
			]
		);
		$repeater->add_control('showsubcat',
			[
				'label'=>esc_html__('Show Sub category','theplus'),
				'type'=>Controls_Manager::SWITCHER,
				'label_on'=>esc_html__('Show','theplus'),
				'label_off'=>esc_html__('Hide','theplus'),
				'default'=>'',
				'condition'=>[
                    'sourceType'=>'taxonomy',
					'TaxonomyType'=>'category',
				],
			]
		);
		$this->add_control('searchField',
			[
				'label' => __('Search Bar','theplus'),
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => [
					[	
						'_id'=>uniqid('RId-'),
						'sourceType'=>'',
						'fieldLabel'=>'Label',
						'showCount'=>'no',
					],
				],
				'title_field' => '{{{ sourceType }}}',
			]
		);
		$this->end_controls_section();

		$this->start_controls_section('Search_section',
			[
				'label' => esc_html__('Search Input', 'theplus' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_control('searchLabel',
			[
				'label'=>__('Label','theplus'),
				'type'=>Controls_Manager::TEXT,
				'default'=>__('Label','theplus'),
				'placeholder'=>__('Type your title here','theplus'),
			]
		);
		$this->add_control('placeholder',
			[
				'label'=>__('Placeholder','theplus'),
				'type'=>Controls_Manager::TEXT,
				'default'=>__('Search ...','theplus'),
				'placeholder'=>__('Type your title here','theplus'),
			]
		);
		$this->add_control('InputIcon',
			[
				'label'=>__( 'Icon', 'theplus' ),
				'type'=>Controls_Manager::ICONS,
				'default'=>[
					'value'=>'fas fa-search',
					'library'=>'solid',
				],
			]
		);
        $this->end_controls_section();

		$this->start_controls_section('Results_section',
			[
				'label' => esc_html__('Results Area','theplus'),
				'tab' => Controls_Manager::TAB_CONTENT,
				'condition' => [
                    'ajaxsearch'=>'yes',
				],
			]
		);
		$this->add_control('ResultStyle',
			[
				'label'=>esc_html__('Result Style','theplus'),
				'type'=>Controls_Manager::SELECT,
				'default'=>'style-1',
				'options'=>[
					'style-1'=>esc_html__('Style 1','theplus'),
					'style-2'=>esc_html__('Style 2','theplus'),
				],
			]
		);
		$this->add_control('postCount',
			[
				'label'=>__('Posts Per Page','theplus'),
				'type'=>Controls_Manager::SLIDER,
				'size_units'=>['px'],
				'range'=>[
					'px'=>[
						'min'=>0,
						'max'=>100,
						'step'=>1,
					],
				],
				'default'=>[
					'unit'=>'px',
					'size'=>3,
				],
				'condition'=>[
					'ajaxsearch'=>'yes',
				],
			]
		);
		$this->add_control('columnResult',
			[
				'label'=>__('Column','theplus'),
				'type'=>Controls_Manager::POPOVER_TOGGLE,
				'label_off'=>__('Default','theplus'),
				'label_on'=>__('Custom','theplus'),
				'return_value'=>'yes',
				'default'=>'yes',
				'condition' => [
					'ResultStyle'=>'style-2',
				],
			]
		);
		$this->start_popover();
		$this->add_control('inputRADc',
			[
				'label' => esc_html__( 'Desktop', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 6,
				'options' => theplus_get_columns_list(),
			]
		);
		$this->add_control('inputRATc',
			[
				'label'=>esc_html__('Tablet','theplus'),
				'type'=>Controls_Manager::SELECT,
				'default'=>'4',
				'options'=>theplus_get_columns_list(),
			]
		);
		$this->add_control('inputRAMc',
			[
				'label'=>esc_html__('Mobile','theplus'),
				'type'=>Controls_Manager::SELECT,
				'default'=>'6',
				'separator'=>'after',
				'options'=>theplus_get_columns_list(),
			]
		);
		$this->end_popover();
		$this->add_control('ResultSetting',
			[
				'label'=>__('Result Visibility Settings','theplus'),
				'type'=>Controls_Manager::POPOVER_TOGGLE,
				'label_off'=>__('Default','theplus'),
				'label_on'=>__('Custom','theplus'),
				'return_value'=>'yes',
				'default'=>'yes'
			]
		);
		$this->start_popover();
		$this->add_control('TitleOn',
			[
				'label'=>esc_html__('Enable Title','theplus'),
				'type'=>Controls_Manager::SWITCHER,
				'label_on'=>esc_html__('Show','theplus'),
				'label_off'=>esc_html__('Hide','theplus'),
				'default'=>'yes'
			]
		);
		$this->add_control('ContentOn',
			[
				'label'=>esc_html__('Enable Content','theplus'),
				'type'=>Controls_Manager::SWITCHER,
				'label_on'=>esc_html__('Show','theplus'),
				'label_off'=>esc_html__('Hide','theplus'),
				'default'=>'yes'
			]
		);
		$this->add_control('ThubOn',
			[
				'label'=>esc_html__('Enable Thumb','theplus'),
				'type'=>Controls_Manager::SWITCHER,
				'label_on'=>esc_html__('Show','theplus'),
				'label_off'=>esc_html__('Hide','theplus'),
				'default'=>'yes'
			]
		);
		$this->add_control('PriceOn',
			[
				'label'=>esc_html__('Enable Price (Woo)','theplus'),
				'type'=>Controls_Manager::SWITCHER,
				'label_on'=>esc_html__('Show','theplus'),
				'label_off'=>esc_html__('Hide','theplus'),
				'default'=>'yes'
			]
		);
		$this->add_control('ShortDescOn',
			[
				'label'=>esc_html__('Enable Short Description','theplus'),
				'type'=>Controls_Manager::SWITCHER,
				'label_on'=>esc_html__('Show','theplus'),
				'label_off'=>esc_html__('Hide','theplus'),
				'default'=>'yes'
			]
		);
		$this->add_control('totalresult',
			[
				'label'=>esc_html__('Enable Total Count','theplus'),
				'type'=>Controls_Manager::SWITCHER,
				'label_on'=>esc_html__('Show','theplus'),
				'label_off'=>esc_html__('Hide','theplus'),
				'default'=>'yes'
			]
		);
		$this->add_control('totalresulttxt',
			[
				'label'=>__('Total Result Text','theplus'),
				'type'=>Controls_Manager::TEXT,
				'default'=>__('Results','theplus'),
				'placeholder'=>__('Type your title here','theplus'),
			]
		);
		$this->end_popover();
	
		$this->add_control('TextLimit',
			[
				'label'=>__('Text Limit','theplus'),
				'type'=>Controls_Manager::POPOVER_TOGGLE,
				'label_off'=>__('Default','theplus'),
				'label_on'=>__('Custom','theplus'),
				'return_value'=>'yes',
				'default'=>'yes',
			]
		);
		$this->start_popover();
		$this->add_control('TxtTitle',
			[
				'label'=>esc_html__('Title Limit','theplus'),
				'type'=>Controls_Manager::SWITCHER,
				'label_on'=>esc_html__('Show','theplus'),
				'label_off'=>esc_html__('Hide','theplus'),
				'default'=>'',
			]
		);
		$this->add_control('TextType',
			[
				'label'=>esc_html__('Limit On','theplus'),
				'type'=>Controls_Manager::SELECT,
				'default'=>'char',
				'options'=>[
					'char'=>esc_html__('Character','theplus'),
					'word'=>esc_html__('Word','theplus'),							
				],
				'condition' => [
					'TextLimit'=>'yes',
					'TxtTitle'=>'yes',
				],
			]
		);
		$this->add_control('TextCount',
			[
				'label'=>esc_html__( 'Limit Count', 'theplus' ),
				'type'=>Controls_Manager::NUMBER,
				'min'=>1,
				'max'=>2000,
				'step'=>1,
				'default'=>100,
				'condition' => [
					'TextLimit'=>'yes',
					'TxtTitle'=>'yes',
				],
			]
		);
		$this->add_control('TextDots',
			[
				'label'=>esc_html__( 'Display Dots','theplus'),
				'type'=>Controls_Manager::TEXT,
				'default'=>esc_html__('...','theplus'),
				'separator'=>'after',
				'condition' => [
					'TextLimit'=>'yes',
					'TxtTitle'=>'yes',
				],
			]
		);
		$this->add_control('ContentTitle',
			[
				'label'=>esc_html__('Content Limit','theplus'),
				'type'=>Controls_Manager::SWITCHER,
				'label_on'=>esc_html__('Show','theplus'),
				'label_off'=>esc_html__('Hide','theplus'),
				'default'=>'',
			]
		);
		$this->add_control('ContentType',
			[
				'label'=>esc_html__('Limit On','theplus'),
				'type'=>Controls_Manager::SELECT,
				'default'=>'char',
				'options'=>[
					'char'=>esc_html__('Character','theplus'),
					'word'=>esc_html__('Word','theplus'),							
				],
				'condition' => [
					'TextLimit'=>'yes',
					'ContentTitle'=>'yes',
				],
			]
		);
		$this->add_control('ContentCount',
			[
				'label'=>esc_html__('Limit Count','theplus'),
				'type'=>Controls_Manager::NUMBER,
				'min'=>1,
				'max'=>2000,
				'step'=>1,
				'default'=>100,
				'condition' => [
					'TextLimit'=>'yes',
					'ContentTitle'=>'yes',
				],
			]
		);
		$this->add_control('ContentDots',
			[
				'label'=>esc_html__('Display Dots','theplus'),
				'type'=>Controls_Manager::TEXT,
				'default'=>esc_html__('...','theplus'),
				'condition' => [
					'TextLimit'=>'yes',
					'ContentTitle'=>'yes',
				],
			]
		);
		$this->end_popover();

		$this->add_control('Resultlink',
			[
				'label'=>__('Result area link','theplus'),
				'type'=>Controls_Manager::POPOVER_TOGGLE,
				'label_off'=>__('Default','theplus'),
				'label_on'=>__('Custom','theplus'),
				'return_value'=>'yes',
				'default'=>'yes',
			]
		);
		$this->start_popover();
		$this->add_control('ResultlinkOn',
			[
				'label'=>__('Result link Enable','theplus'),
				'type'=>Controls_Manager::SWITCHER,
				'label_on'=>__('Show','theplus'),
				'label_off'=>__('Hide','theplus'),
				'return_value'=>'yes',
				'default'=>'yes',
			]
		);
		$this->add_control('Resultlinktarget',
			[
				'label'=>__('Result link target','theplus'),
				'type'=>Controls_Manager::SELECT,
				'default'=>'_blank',
				'options'=>[
					'_blank'=>__('blank','theplus'),
					'_self'=>__('self','theplus'),
				],
				'condition' => [
					'ResultlinkOn'=>'yes',
				],
			]
		);
		$this->end_popover();

		$this->add_control('ScrollBar',
			[
				'label'=>esc_html__('Enable ScrollBar','theplus'),
				'type'=>Controls_Manager::SWITCHER,
				'label_on'=>esc_html__('Show','theplus'),
				'label_off'=>esc_html__('Hide','theplus'),
				'default'=>'',
				'separator'=>'before',
			]
		);
		$this->add_control('resultArea',
			[
				'label'=>__('ScrollBar Height','theplus'),
				'type'=>Controls_Manager::SLIDER,
				'size_units'=>['px','%'],
				'range'=>[
					'px'=>[
						'min' => 100,
						'max' => 1000,
						'step' => 5,
					],
					'%' => [
						'min' => 100,
						'max' => 1000,
					],
				],
				'selectors'=>[
					'{{WRAPPER}} .tp-search-bar .tp-search-scrollbar'=>'height:{{SIZE}}{{UNIT}}',
				],
				'condition'=>[
					'ScrollBar'=>'yes',
				],
			]
		);
		$this->end_controls_section();

		$this->start_controls_section('StandardSearch_section',
			[
				'label' => esc_html__('Standard Search','theplus'),
				'tab' => Controls_Manager::TAB_CONTENT,
				'condition'=>[
					'ajaxsearch'=>'yes',
				],
			]
		);
		$this->add_control('SearchType',
			[
				'label'=>__('Search Type','theplus'),
				'type'=>Controls_Manager::SELECT,
				'multiple'=>true,
				'options'=>[
					'fullMatch'=>__('Full Match','theplus'),
					'wordmatch'=>__('Word Match','theplus'),
					'otheroption'=>__('Default','theplus'),
				],
				'default'=>'otheroption',
			]
		);
		$this->add_control('GenericFilter',
			[
				'label'=>__('Generic Filters','theplus'),
				'type'=>Controls_Manager::POPOVER_TOGGLE,
				'label_off'=>__('Default','theplus'),
				'label_on'=>__('Custom','theplus'),
				'return_value'=>'yes',
				'default'=>'yes',
			]
		);
		$this->start_popover();
		$this->add_control('haddingGF',
			[
				'label'=>'',
				'type'=>Controls_Manager::RAW_HTML,
				'raw'=>__('Generic Filters','theplus'),
				'content_classes'=>'gfdhaki',
				'separator'=>'after',
			]
		);
		$this->add_control('sintitle',
			[
				'label'=>esc_html__('Search in Title','theplus'),
				'type'=>Controls_Manager::SWITCHER,
				'label_on'=>esc_html__('Show','theplus'),
				'label_off'=>esc_html__('Hide','theplus'),
				'default'=>'yes',
			]
		);
		$this->add_control('sinexcerpt',
			[
				'label'=>esc_html__('Search in Excerpt','theplus'),
				'type'=>Controls_Manager::SWITCHER,
				'label_on'=>esc_html__('Show','theplus'),
				'label_off'=>esc_html__('Hide','theplus'),
				'default'=>'',
			]
		);
		$this->add_control('sincontent',
			[
				'label'=>esc_html__('Search in Content','theplus'),
				'type'=>Controls_Manager::SWITCHER,
				'label_on'=>esc_html__('Show','theplus'),
				'label_off'=>esc_html__('Hide','theplus'),
				'default'=>'',
			]
		);
		$this->add_control('sinname',
			[
				'label'=>esc_html__('Search in Permalink','theplus'),
				'type'=>Controls_Manager::SWITCHER,
				'label_on'=>esc_html__('Show','theplus'),
				'label_off'=>esc_html__('Hide','theplus'),
				'default'=>'',
			]
		);
		$this->add_control('sincategory',
			[
				'label'=>esc_html__('Search in Category','theplus'),
				'type'=>Controls_Manager::SWITCHER,
				'label_on'=>esc_html__('Show','theplus'),
				'label_off'=>esc_html__('Hide','theplus'),
				'default'=>'',
			]
		);
		$this->add_control('sinTags',
			[
				'label'=>esc_html__('Search in Tags','theplus'),
				'type'=>Controls_Manager::SWITCHER,
				'label_on'=>esc_html__('Show','theplus'),
				'label_off'=>esc_html__('Hide','theplus'),
				'default'=>'',
			]
		);
		$this->end_popover();
		$this->add_control('ACFFilter',
			[
				'label'=>__('ACF Filters','theplus'),
				'type'=>Controls_Manager::POPOVER_TOGGLE,
				'label_off'=>__('Default','theplus'),
				'label_on'=>__('Custom','theplus'),
				'return_value'=>'yes',
				'default'=>'',
			]
		);
		$this->start_popover();
		$this->add_control('HeaderACF',
			[
				'label'=>'',
				'type'=>Controls_Manager::RAW_HTML,
				'raw'=>__('ACF Options','theplus'),
				'content_classes'=>'acfdhaki',
				'separator'=>'after',
			]
		);
		$this->add_control('ACFkey',
			[
				'label'=>__('ACF Key','theplus'),
				'type'=>Controls_Manager::TEXT,
				'default'=>'',
				'placeholder'=>__('Enter ACF Key','theplus'),
			]
		);
		$this->end_popover();
		$this->end_controls_section();

		/* Load More/Lazy Load Option start */
		$this->start_controls_section('loadmore_lazyload_section',
			[
				'label'=>esc_html__('Load More/Lazy Load','theplus'),
				'tab'=>Controls_Manager::TAB_CONTENT,
				'condition' => [
					'ajaxsearch'=>'yes',
				],
			]
		);		
		$this->add_control('post_extra_option',
			[
				'label'=>esc_html__('Loading Options','theplus'),
				'type'=>Controls_Manager::SELECT,
				'default'=>'none',
				'options'=>theplus_post_loading_option(),
			]
		);
		$this->add_control('showcounter',
			[
				'label'=>__('Counter Enable','theplus'),
				'type'=>Controls_Manager::SWITCHER,
				'label_on'=>__('Show','theplus'),
				'label_off'=>__('Hide','theplus'),
				'return_value'=>'yes',
				'default'=>'yes',
				'condition'=>[
					'post_extra_option'=>['pagination']
				],
			]
		);
		$this->add_control('counterlimit',
			[
				'label'=>__('Counter Limit','theplus'),
				'type'=>Controls_Manager::NUMBER,
				'min'=> 0,
				'step'=> 1,
				'default'=>'',
				'condition' => [
					'post_extra_option'=>['pagination'],
					'showcounter'=>'yes'
				],
			]
		);
		$this->add_control('shownextprev',
			[
				'label'=>__('Arrow Navigation','theplus'),
				'type'=>Controls_Manager::SWITCHER,
				'label_on'=>__('Show','theplus'),
				'label_off'=>__('Hide','theplus'),
				'return_value'=>'yes',
				'default'=>'yes',
				'condition'=>[
					'post_extra_option'=>['pagination']
				],
			]
		);
		$this->add_control('counterstyle',
			[
				'label'=>__('Counter Style','theplus'),
				'type'=>Controls_Manager::SELECT,
				'default'=>'center',
				'options'=>[
					'after'=>__('After Arrow','theplus'),
					'center'=>__('Between Arrow','theplus'),
					'before'=>__('Before Arrow','theplus'),
				],
				'condition' => [
					'post_extra_option'=>['pagination'],
					'showcounter'=>'yes',
					'shownextprev'=>'yes'
				],
			]
		);
		$this->add_control('nexttxt',
			[
				'label'=>__('Next text','theplus'),
				'type'=>Controls_Manager::TEXT,
				'default'=>__('Next','theplus'),
				'placeholder'=>__('Enter Text','theplus'),
				'condition' => [
					'shownextprev'=>'yes', 
					'post_extra_option'=>['pagination']
				],
			]
		);
		$this->add_control('nexticon',
			[
				'label'=>__('Next Icon','theplus'),
				'type'=>Controls_Manager::ICONS,
				'default'=>[
					'value'=>'',
					'library'=>'solid',
				],
				'condition' => [
					'shownextprev'=>'yes', 
					'post_extra_option'=>['pagination']
				],
			]
		);
		$this->add_control('prevtxt',
			[
				'label'=>__('Previous text','theplus'),
				'type'=>Controls_Manager::TEXT,
				'default'=>__('Prev','theplus' ),
				'placeholder'=>__('Enter Text','theplus'),
				'condition' => [
					'shownextprev'=>'yes', 
					'post_extra_option'=>['pagination']
				],
			]
		);
		$this->add_control('previcon',
			[
				'label'=>__('Previous Icon','theplus'),
				'type'=>Controls_Manager::ICONS,
				'default'=>[
					'value'=>'',
					'library'=>'solid',
				],
				'condition' => [
					'shownextprev'=>'yes', 
					'post_extra_option'=>['pagination']
				],
			]
		);
		$this->add_control('load_more_btn_text',
			[
				'label'=>esc_html__('Button Text','theplus'),
				'type'=>Controls_Manager::TEXT,
				'default'=>esc_html__('Load More','theplus'),
				'condition'=>[
					'post_extra_option'=>'load_more',
				],
			]
		);
		$this->add_control('tp_loading_text',
			[
				'label'=>esc_html__('Loading Text','theplus'),
				'type'=>Controls_Manager::TEXT,
				'default'=>esc_html__('Loading...','theplus'),
				'condition'=>[
					'post_extra_option'=>['load_more','lazy_load']
				],
			]
		);
		$this->add_control('loaded_posts_text',
			[
				'label'=>esc_html__('All Posts Loaded Text','theplus'),
				'type'=>Controls_Manager::TEXT,
				'default'=>esc_html__('All done!','theplus'),
				'condition'=>[
					'post_extra_option'=>['load_more','lazy_load']
				],
			]
		);
		$this->add_control('load_more_post',
			[
				'label'=>esc_html__('More posts on click/scroll','theplus'),
				'type'=>Controls_Manager::NUMBER,
				'min'=>1,
				'max'=>30,
				'step'=>1,
				'default'=>4,
				'condition'=>[
					'post_extra_option'=>['load_more','lazy_load'],
				],
			]
		);
		$this->add_control('Load_page',
			[
				'label'=>__('Counter','theplus'),
				'type'=>Controls_Manager::SWITCHER,
				'label_on'=>__('Show','theplus'),
				'label_off'=>__('Hide','theplus'),
				'return_value'=>'yes',
				'default'=>'yes',
				'condition'=>[
					'post_extra_option'=>['load_more'],
				],
			]
		);
		$this->add_control('loadPagetxt',
			[
				'label'=>__('Counter Text','theplus'),
				'type'=>Controls_Manager::TEXT,
				'default'=>__('Total :','theplus'),
				'placeholder'=>__('Enter Title','theplus'),
				'condition'=>[
					'post_extra_option'=>['load_more'],
					'Load_page'=>'yes'
				],
			]
		);
		$this->end_controls_section();
		/*Load More/Lazy Load Option End*/
		/*Extra Option start*/
		$this->start_controls_section('Extra_section',
			[
				'label' => esc_html__('Extra Option','theplus'),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);	
		$this->add_control('ajaxsearch',
			[
				'label'=>esc_html__('AJAX Search','theplus'),
				'type'=>Controls_Manager::SWITCHER,
				'label_on'=>esc_html__('Enable','theplus'),
				'label_off'=>esc_html__('Disable','theplus'),
				'default'=>'yes',
			]
		);
		$this->add_control('ajaxsearch_Note',
			[				
				'type' => Controls_Manager::RAW_HTML,
				'raw' => '<b>Note :</b> If you disable this option, Search results will be on search page after redirection.',
				'content_classes'=>'tp-widget-description',
			]
		);
		$this->add_control('ajaxsearchCharLimit',
			[
				'label'=>esc_html__( 'Search Character Limit', 'theplus' ),
				'type'=>Controls_Manager::NUMBER,
				'min'=>1,
				'max'=>25,
				'step'=>1,
				'default'=>3,
				'condition'=>[
					'ajaxsearch'=>'yes',
				],
			]
		);
		$this->add_control('ajaxsearchCharLimit_note',
			[				
				'type' => Controls_Manager::RAW_HTML,
				'raw' => '<b>Note :</b>  After how many characters want to start search AJAX results?',
				'content_classes'=>'tp-widget-description',
				'condition'=>[
					'ajaxsearch'=>'yes',
				],
			]
		);
		
		$this->add_control('SpecialCTP',
			[
				'label'=>__('Only for specific CPT','theplus'),
				'type'=>Controls_Manager::SWITCHER,
				'label_on'=>__('Show','theplus'),
				'label_off'=>__('Hide','theplus'),
				'return_value'=>'yes',
				'default'=>'',
			]
		);
		$this->add_control('SpecialCTP_Note',
			[				
				'type'=>Controls_Manager::RAW_HTML,
				'raw'=>'<b>Note :</b> If you disable this option, Search results will be on search page after redirection.',
				'content_classes'=>'tp-widget-description',
				'condition'=>[
					'SpecialCTP'=>'yes',
				],
			]
		);
		$this->add_control('SpecialCTPType',
			[
				'label'=>esc_html__('Special CPT Type','theplus'),
				'type'=>Controls_Manager::SELECT,
				'default'=>'post',
                'options'=>theplus_get_post_type(),
                'condition'=>[
                    'SpecialCTP'=>'yes',
				],
			]
		);

		$this->add_control('RelatedSearchPen',
			[
				'label'=>__('Keyword Suggestions Area','theplus'),
				'type'=>Controls_Manager::POPOVER_TOGGLE,
				'label_off'=>__('Default','theplus'),
				'label_on'=>__('Custom','theplus'),
				'return_value'=>'yes',
				'default'=>'',
			]
		);
		$this->start_popover();
		$this->add_control('RelatedSearchhead',
			[
				'label'=>__('Keyword Suggestions Area','theplus'),
				'type'=>Controls_Manager::HEADING,
				'separator'=>'after'
			]
		);
		$this->add_control('relatedSBtn',
			[
				'label'=>esc_html__('Below Search Bar','theplus'),
				'type'=>Controls_Manager::SWITCHER,
				'label_on'=>esc_html__('Show','theplus'),
				'label_off'=>esc_html__('Hide','theplus'),
				'default'=>'',
			]
		);
		$this->add_control('related_note',
			[				
				'type' => Controls_Manager::RAW_HTML,
				'raw' => '<b>Note :</b> Add Search terms below search bar for easy access of your popular terms.',
				'content_classes'=>'tp-widget-description',				
			]
		);
		$this->add_control('relatedSBtnText',
			[
				'label'=>__('Label Text','theplus'),
				'type'=>Controls_Manager::TEXT,
				'default'=>__('Related Search','theplus'),
				'placeholder'=>__('Enter label Text','theplus'),
				'condition'=>[
					'relatedSBtn'=>'yes',
				],
			]
		);
		$this->add_control('relatedSBtnTag',
			[
				'label' => esc_html__( 'Related Tag', 'theplus' ),
				'type' => Controls_Manager::TEXTAREA,
				'rows' => 4,
				'default' => '',
				'placeholder' => esc_html__( 'Enter Value', 'theplus' ),
				'dynamic' => [
					'active'   => true,
				],	
				'condition' => [
					'relatedSBtn'=>'yes',
				],			
			]
		);
		$this->add_control('relatedtxt_note',
			[				
				'type' => Controls_Manager::RAW_HTML,
				'raw' => '<b>Note :</b>  Use “|” to enter multiple values in suggestion words.',
				'content_classes'=>'tp-widget-description',
				'condition'=>[
					'relatedSBtn'=>'yes',
				],
			]
		);

		$this->add_control('searchsuggest',
			[
				'label'=>esc_html__('Prefilled Suggestions','theplus'),
				'type'=>Controls_Manager::SWITCHER,
				'label_on'=>esc_html__('Enable','theplus'),
				'label_off'=>esc_html__('Disable','theplus'),
				'default'=>'',
			]
		);
		$this->add_control('suggest_note',
			[				
				'type' => Controls_Manager::RAW_HTML,
				'raw' => '<b>Note :</b> These values will come default in search results in the beginning.',
				'content_classes'=>'tp-widget-description',				
			]
		);
		$this->add_control('suggesttxt',
			[
				'label'=>__('Enter Suggestions Word','theplus'),
				'type'=>Controls_Manager::TEXTAREA,
				'rows'=>2,
				'default'=>'',
				'placeholder'=>__('Enter Keyword','theplus'),
				'condition'=>[
					'searchsuggest'=>'yes',
				],
			]
		);
		$this->add_control('suggesttxt_note',
			[				
				'type' => Controls_Manager::RAW_HTML,
				'raw' => '<b>Note :</b>  Use “|” to enter multiple values in suggestion words.',
				'content_classes'=>'tp-widget-description',
				'condition'=>[
					'searchsuggest'=>'yes',
				],
			]
		);
		$this->end_popover();

		$this->add_control('showBtnPen',
			[
				'label'=>__('Search Button','theplus'),
				'type'=>Controls_Manager::POPOVER_TOGGLE,
				'label_off'=>__('Default','theplus'),
				'label_on'=>__('Custom','theplus'),
				'return_value'=>'yes',
				'default'=>'yes',
			]
		);
		$this->start_popover();
		$this->add_control('showHead',
			[
				'label'=>__('Search Button','theplus'),
				'type'=>Controls_Manager::HEADING,
				'separator'=>'after'
			]
		);
		$this->add_control('showBtn',
			[
				'label'=>esc_html__('Search Button','theplus'),
				'type'=>Controls_Manager::SWITCHER,
				'label_on'=>esc_html__('Show','theplus'),
				'label_off'=>esc_html__('Hide','theplus'),
				'default'=>'yes',
			]
		);
		$this->add_control('BtnText',
			[
				'label'=>__('Button Text','theplus'),
				'type'=>Controls_Manager::TEXT,
				'default'=>__('Search','theplus'),
				'placeholder'=>__('Enter Button Text','theplus'),
				'condition'=>[
					'showBtn'=>'yes',
				],
			]
		);
		$this->add_control('BtnMedia',
			[
				'label'=>__('Button Icon','theplus'),
				'type'=>Controls_Manager::SELECT,
				'default'=>'icon',
				'options'=> [
					''=>__('None','theplus'),
					'icon'=>__('Icon','theplus'),
					'image'=>__('Image','theplus')
				],
				'condition'=>[
					'showBtn'=>'yes',
				],
			]
		);
		$this->add_control('BtnIcon',
			[
				'label'=>__('Icon','theplus'),
				'type'=>Controls_Manager::ICONS,
				'default'=>[
					'value'=>'fas fa-search',
					'library'=>'solid',
				],
				'condition'=>[
					'showBtn'=>'yes',
					'BtnMedia'=>'icon',
				],
			]
		);
		$this->add_control('BtnImage',
			[
				'label'=>__('Choose Image','theplus'),
				'type'=>Controls_Manager::MEDIA,
				'default'=>['url'=>Utils::get_placeholder_image_src()],
				'condition'=>[
					'showBtn'=>'yes',
					'BtnMedia'=>'image'
				],
			]
		);
		$this->add_control('BtnPosition',
			[
				'label'=>__('Button Position','theplus'),
				'type'=>Controls_Manager::SELECT,
				'options'=> [
					'before'=>__('Before','theplus'),
					'after'=>__('After','theplus')
				],
				'default'=>'before',
				'condition' => [
					'showBtn'=>'yes',
					'BtnText!'=>'',
					'BtnMedia!'=>''
				],
			]
		);
		$this->end_popover();
		$this->add_control('errormsg',
			[
				'label'=>__('Post Not Found Message','theplus'),
				'type'=>Controls_Manager::TEXTAREA,
				'rows'=> 2,
				'default'=> __('Sorry, No Results Were Found.','theplus'),
				'placeholder'=>__('Enter Error Text','theplus'),
				'condition'=>[
					'ajaxsearch'=>'yes',
				],
			]
		);
		$this->end_controls_section();
		/*Extra Option End*/
		/*style*/
		/*Lable start*/
		$this->start_controls_section('label_styling',
			[
				'label' => esc_html__('Label','theplus'),
				'tab' => Controls_Manager::TAB_STYLE			
			]
		);	
		$this->add_responsive_control('lblPadding',
			[
				'label'=>__('Padding','theplus'),
				'type'=>Controls_Manager::DIMENSIONS,
				'size_units'=>['px','%'],
				'selectors'=>[
					'{{WRAPPER}} .tp-search-bar .tp-search-form .tp-search-label'=>'padding:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control('lblmargin',
			[
				'label'=>__('Margin','theplus'),
				'type'=>Controls_Manager::DIMENSIONS,
				'size_units'=>['px','%'],
				'separator'=>'after',
				'selectors'=>[
					'{{WRAPPER}} .tp-search-bar .tp-search-form .tp-search-label'=>'margin:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(Group_Control_Typography::get_type(),
			[
				'name'=>'lblTypo',
				'label'=>__('Typography','theplus'),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector'=>'{{WRAPPER}} .tp-search-bar .tp-search-form .tp-search-label',
			]
		);		
		$this->start_controls_tabs('lbl_tabs');
		$this->start_controls_tab('lbl_Normal',
			[
				'label' => esc_html__('Normal','theplus')
			]
		);
		$this->add_control('lblNCr',
			[
				'label'=>__('Text Color','theplus'),
				'type'=>Controls_Manager::COLOR,
				'selectors'=>[
					'{{WRAPPER}} .tp-search-bar .tp-search-form .tp-search-label'=>'color:{{VALUE}}'
				]
			]
		);
		$this->add_group_control(Group_Control_Background::get_type(),
			[
				'name'=>'lblNBg',
				'label'=>__('Background','theplus'),
				'types'=>['classic', 'gradient'],
				'selector'=>'{{WRAPPER}} .tp-search-bar .tp-search-form .tp-search-label',
			]
		);
		$this->add_group_control(Group_Control_Border::get_type(),
			[
				'name'=>'lblNB',
				'label'=>esc_html__('Border','theplus'),
				'selector'=>'{{WRAPPER}} .tp-search-bar .tp-search-form .tp-search-label'
			]
		);
		$this->add_responsive_control('lblNBrs',
			[
				'label'=>__('Border Radius','theplus'),
				'type'=>Controls_Manager::DIMENSIONS,
				'size_units'=>['px','%'],
				'selectors'=>[
					'{{WRAPPER}} .tp-search-bar .tp-search-form .tp-search-label'=>'border-radius:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(Group_Control_Box_Shadow::get_type(),
			[
				'name'=>'lblNSd',
				'label'=>__('Box Shadow','theplus'),
				'selector'=>'{{WRAPPER}} .tp-search-bar .tp-search-form .tp-search-label',
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab('lbl_hover',
			[
				'label' => esc_html__('Hover','theplus')
			]
		);
		$this->add_control('lblHCr',
			[
				'label'=>__('Text Color','theplus'),
				'type'=>Controls_Manager::COLOR,
				'selectors'=>[
					'{{WRAPPER}} .tp-search-bar .tp-search-form:hover .tp-search-label'=>'color:{{VALUE}}'
				]
			]
		);
		$this->add_group_control(Group_Control_Background::get_type(),
			[
				'name'=>'lblHBg',
				'label'=>__('Background','theplus'),
				'types'=>['classic', 'gradient'],
				'selector'=>'{{WRAPPER}} .tp-search-bar .tp-search-form:hover .tp-search-label',
			]
		);
		$this->add_group_control(Group_Control_Border::get_type(),
			[
				'name'=>'lblHB',
				'label'=>esc_html__('Border','theplus'),
				'selector'=>'{{WRAPPER}} .tp-search-bar .tp-search-form:hover .tp-search-label'
			]
		);
		$this->add_responsive_control('lblHBrs',
			[
				'label'=>__('Border Radius','theplus'),
				'type'=>Controls_Manager::DIMENSIONS,
				'size_units'=>['px','%'],
				'selectors'=>[
					'{{WRAPPER}} .tp-search-bar .tp-search-form:hover .tp-search-label'=>'border-radius:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(Group_Control_Box_Shadow::get_type(),
			[
				'name'=>'lblHSd',
				'label'=>__('Box Shadow','theplus'),
				'selector'=>'{{WRAPPER}} .tp-search-bar .tp-search-form:hover .tp-search-label',
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		/*Lable End*/

		/*Search Input start*/
		$this->start_controls_section('InputF_styling',
			[
				'label' => esc_html__('SearchBox','theplus'),
				'tab' => Controls_Manager::TAB_STYLE			
			]
		);	
		$this->add_responsive_control('inPad',
			[
				'label'=>__('Padding','theplus'),
				'type'=>Controls_Manager::DIMENSIONS,
				'size_units'=>['px','%'],
				'selectors'=>[
					'{{WRAPPER}} .tp-search-bar .tp-search-form .tp-input-inner-field'=>'padding:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);		
		$this->add_responsive_control('InInnPad',
			[
				'label'=>__('Inner Padding','theplus'),
				'type'=>Controls_Manager::DIMENSIONS,
				'size_units'=>['px','%'],
				'separator'=>'after',
				'selectors'=>[
					'{{WRAPPER}} .tp-search-bar .tp-search-form .tp-search-input'=>'padding:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);	
		$this->add_responsive_control('InWidth',
			[
				'label'=>__('Width','theplus'),
				'type'=>Controls_Manager::SLIDER,
				'size_units'=>['px','%'],
				'range' => [
					'px' => [
						'min'=>0,
						'max'=>1000,
						'step'=>5,
					],
					'%'=>[
						'min'=>0,
						'max'=>100,
					],
				],
				'default'=>[
					'unit'=>'%',
					'size'=>'',
				],
				'selectors'=>[
					'{{WRAPPER}} .tp-search-bar .tp-input-field'=>'width:{{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(Group_Control_Typography::get_type(),
			[
				'name'=>'SinputTypo',
				'label'=>__('Placeholder Typography','theplus'),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector'=>'{{WRAPPER}} .tp-search-bar .tp-search-form .tp-search-input',
			]
		);
		$this->add_control('ClosePen',
			[
				'label'=>__('Ajax close/spinner Icon','theplus'),
				'type'=>Controls_Manager::POPOVER_TOGGLE,
				'label_off'=>__('Default','theplus'),
				'label_on'=>__('Custom','theplus'),
				'return_value'=>'yes',
				'default'=>'yes',
				'condition' => [
					'ajaxsearch'=>'yes',
				],
			]
		);
		$this->start_popover();
		$this->add_control('SiBoxClose_had',
			[
				'label'=>__('Close Icon','theplus'),
				'type'=>Controls_Manager::HEADING,
				'separator'=>'after',
				'condition' => [
					'ajaxsearch'=>'yes',
				],
			]
		);	
		$this->add_control('EnableCloseBE',
			[
				'label'=>__('Enable In Backend','theplus'),
				'type'=>Controls_Manager::SWITCHER,
				'label_on'=>__('Show','theplus'),
				'label_off'=>__('Hide','theplus'),
				'return_value'=>'flex',
				'default'=>'',
				'selectors'=>[
					'{{WRAPPER}} .tp-search-bar.tp-search-backend .tp-search-form .tp-close-btn'=>'display:{{VALUE}}'
				],
				'condition' => [
					'ajaxsearch'=>'yes',
				],
			]
		);
		$this->add_responsive_control('CloseIconsize',
			[
				'label'=>__('Size','theplus'),
				'type'=>Controls_Manager::SLIDER,
				'size_units'=>['px','%'],
				'range' => [
					'px' => [
						'min'=>0,
						'max'=>100,
						'step'=>5,
					],
					'%'=>[
						'min'=>0,
						'max'=>100,
					],
				],
				'default'=>[
					'unit'=>'px',
					'size'=>'',
				],
				'selectors'=>[
					'{{WRAPPER}} .tp-search-bar .tp-search-form .tp-close-btn-icon'=>'font-size:{{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'ajaxsearch'=>'yes',
				],
			]
		);
		$this->add_control('CloseIconCr',
			[
				'label'=>__('Text Color','theplus'),
				'type'=>Controls_Manager::COLOR,
				'selectors'=>[
					'{{WRAPPER}} .tp-search-bar .tp-search-form .tp-close-btn'=>'color:{{VALUE}}'
				],
				'condition' => [
					'ajaxsearch'=>'yes',
				],
			]
		);
		$this->add_control('spinner_had',
			[
				'label'=>__('Spinner','theplus'),
				'type'=>Controls_Manager::HEADING,
				'separator'=>'after',
				'condition' => [
					'ajaxsearch'=>'yes',
				],
			]
		);
		$this->add_control('EnableSpinnerBE',
			[
				'label'=>__('Enable In Backend','theplus'),
				'type'=>Controls_Manager::SWITCHER,
				'label_on'=>__('Show','theplus'),
				'label_off'=>__('Hide','theplus'),
				'return_value'=>'flex',
				'default'=>'',
				'selectors'=>[
					'{{WRAPPER}} .tp-search-bar.tp-search-backend .tp-search-form .tp-ajx-loading'=>'display:{{VALUE}}'
				],
				'condition' => [
					'ajaxsearch'=>'yes',
				],
			]
		);
		$this->add_responsive_control('spinnerImgsize',
			[
				'label'=>__('Size','theplus'),
				'type'=>Controls_Manager::SLIDER,
				'size_units'=>['px','%'],
				'range' => [
					'px' => [
						'min'=>0,
						'max'=>100,
						'step'=>5,
					],
					'%'=>[
						'min'=>0,
						'max'=>100,
					],
				],
				'default'=>[
					'unit'=>'px',
					'size'=>'',
				],
				'selectors'=>[
					'{{WRAPPER}} .tp-search-bar .tp-search-form .tp-ajx-loading .tp-spinner-loader'=>'width:{{SIZE}}{{UNIT}};height:{{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'ajaxsearch'=>'yes',
				],
			]
		);
		$this->add_control('spinNCr',
			[
				'label'=>__('Search Icon Color','theplus'),
				'type'=>Controls_Manager::COLOR,
				'selectors'=>[
					'{{WRAPPER}} .tp-search-bar .tp-search-form .tp-ajx-loading .tp-spinner-loader'=>'border-top-color:{{VALUE}}'
				]
			]
		);
		$this->end_popover();
		$this->start_controls_tabs('input_tabs' );
		$this->start_controls_tab('input_Normal',
			[
				'label' => esc_html__('Normal','theplus')
			]
		);
		$this->add_control('PlastxtNCr',
			[
				'label'=>__('Placeholder Color','theplus'),
				'type'=>Controls_Manager::COLOR,
				'selectors'=>[
					'{{WRAPPER}} .tp-search-bar .tp-search-form .tp-search-input::-webkit-input-placeholder'=>'color:{{VALUE}}'
				]
			]
		);
		$this->add_control('intextColor',
			[
				'label'=>__('Text Color','theplus'),
				'type'=>Controls_Manager::COLOR,
				'selectors'=>[
					'{{WRAPPER}} .tp-search-bar .tp-search-form .tp-search-input'=>'color:{{VALUE}}'
				]
			]
		);		
		$this->add_control('inNiconCr',
			[
				'label'=>__('Search Icon Color','theplus'),
				'type'=>Controls_Manager::COLOR,
				'selectors'=>[
					'{{WRAPPER}} .tp-search-bar .tp-search-form .tp-search-input-icon'=>'color:{{VALUE}}',
					'{{WRAPPER}} .tp-search-bar .tp-search-form .tp-search-input-icon svg'=>'width:{{VALUE}}'
				]
			]
		);
		$this->add_responsive_control('inNiconSvg',
			[
				'label'=>__('Svg Icon Size','theplus'),
				'type'=>Controls_Manager::SLIDER,
				'size_units'=>['px'],
				'range' => [
					'px' => [
						'min'=>1,
						'max'=>150,
						'step'=>2,
					],
				],
				'default'=>[
					'unit'=>'px',
					'size'=>'20',
				],
				'selectors'=>[
					'{{WRAPPER}} .tp-search-bar .tp-search-form .tp-search-input-icon svg'=>'width:{{SIZE}}{{UNIT}};height:{{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(Group_Control_Background::get_type(),
			[
				'name'=>'inbgType',
				'types'=>['classic','gradient'],
				'selector'=>'{{WRAPPER}} .tp-search-bar .tp-search-form .tp-search-input',
			]
		);
		$this->add_group_control(Group_Control_Border::get_type(),
			[
				'name'=>'inNBorder',
				'label'=>esc_html__('Border','theplus'),
				'selector'=>'{{WRAPPER}} .tp-search-bar .tp-search-form input.tp-search-input'
			]
		);
		$this->add_responsive_control('inNBradius',
			[
				'label'=>__('Border Radius','theplus'),
				'type'=>Controls_Manager::DIMENSIONS,
				'size_units'=>['px','%'],
				'selectors'=>[
					'{{WRAPPER}} .tp-search-bar .tp-search-form .tp-search-input'=>'border-radius:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(Group_Control_Box_Shadow::get_type(),
			[
				'name'=>'inNBshadow',
				'selector'=>'{{WRAPPER}} .tp-search-bar .tp-search-form .tp-search-input',			
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab('input_Focus',
			[
				'label' => esc_html__('Focus','theplus')
			]
		);
		$this->add_control('PlastxtHCr',
			[
				'label'=>__('Placeholder Color','theplus'),
				'type'=>Controls_Manager::COLOR,
				'selectors'=>[
					'{{WRAPPER}} .tp-search-bar .tp-search-form .tp-search-input:focus::placeholder'=>'color:{{VALUE}}'
				]
			]
		);
		$this->add_control('intxtFcolor',
			[
				'label'=>__('Text Color','theplus'),
				'type'=>Controls_Manager::COLOR,
				'selectors'=>[
					'{{WRAPPER}} .tp-search-bar .tp-search-form .tp-search-input:focus'=>'color:{{VALUE}}'
				]
			]
		);
		$this->add_control('inHiconCr',
			[
				'label'=>__('Search Icon Color','theplus'),
				'type'=>Controls_Manager::COLOR,
				'selectors'=>[
					'{{WRAPPER}} .tp-search-bar .tp-search-form .tp-search-input:focus + .tp-search-input-icon '=>'color:{{VALUE}}',
					'{{WRAPPER}} .tp-search-bar .tp-search-form .tp-search-input:focus + .tp-search-input-icon svg'=>'color:{{VALUE}}'
				]
			]
		);
		$this->add_group_control(Group_Control_Background::get_type(),
			[
				'name'=>'inFbgType',
				'types'=>['classic','gradient'],
				'selector'=>'{{WRAPPER}} .tp-search-bar .tp-search-form .tp-search-input:focus',
			]
		);
		$this->add_group_control(Group_Control_Border::get_type(),
			[
				'name'=>'inFBorder',
				'label'=>esc_html__('Border','theplus'),
				'selector'=>'{{WRAPPER}} .tp-search-bar .tp-search-form .tp-search-input:focus'
			]
		);
		$this->add_responsive_control('inHBradius',
			[
				'label'=>__('Border Radius','theplus'),
				'type'=>Controls_Manager::DIMENSIONS,
				'size_units'=>['px','%'],
				
				'selectors'=>[
					'{{WRAPPER}} .tp-search-bar .tp-search-form .tp-search-input'=>'border-radius:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(Group_Control_Box_Shadow::get_type(),
			[
				'name'=>'inHFBshadow',
				'selector'=> '{{WRAPPER}} .tp-search-bar .tp-search-form .tp-search-input:focus',			
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_control(
			'inFB_bf',
			[
				'label' => esc_html__( 'Backdrop Filter', 'theplus' ),
				'type' => Controls_Manager::POPOVER_TOGGLE,
				'label_off' => __( 'Default', 'theplus' ),
				'label_on' => __( 'Custom', 'theplus' ),
				'return_value' => 'yes',
			]
		);
		$this->add_control(
			'inFB_bf_blur',
			[
				'label' => esc_html__( 'Blur', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'max' => 100,
						'min' => 1,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 10,
				],
				'condition'    => [
					'inFB_bf' => 'yes',
				],
			]
		);
		$this->add_control(
			'inFB_bf_grayscale',
			[
				'label' => esc_html__( 'Grayscale', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'max' => 1,
						'min' => 0,
						'step' => 0.1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 0,
				],
				'selectors' => [
					'{{WRAPPER}} .tp-search-bar .tp-search-form .tp-search-input' => '-webkit-backdrop-filter:grayscale({{inFB_bf_grayscale.SIZE}})  blur({{inFB_bf_blur.SIZE}}{{inFB_bf_blur.UNIT}}) !important;backdrop-filter:grayscale({{inFB_bf_grayscale.SIZE}})  blur({{inFB_bf_blur.SIZE}}{{inFB_bf_blur.UNIT}}) !important;',
				 ],
				'condition'    => [
					'inFB_bf' => 'yes',
				],
			]
		);
		$this->end_popover();

		$this->add_control('SiBox_had',
			[
				'label'=>__('Box Options','theplus'),
				'type'=>Controls_Manager::HEADING,
				'separator'=>'before',
			]
		);
		$this->add_control('SiBoxPad',
			[
				'label'=>__('Padding','theplus'),
				'type'=>Controls_Manager::DIMENSIONS,
				'size_units'=>['px','%'],
				'selectors'=>[
					'{{WRAPPER}} .tp-search-form .tp-input-field'=>'padding:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_control('SiBoxMrg',
			[
				'label'=>__('Margin','theplus'),
				'type'=>Controls_Manager::DIMENSIONS,
				'size_units'=>['px','%'],
				'selectors'=>[
					'{{WRAPPER}} .tp-search-form .tp-input-field'=>'margin:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->start_controls_tabs('SiBox_tabs' );
		$this->start_controls_tab('SiBox_Normal',
			[
				'label' => esc_html__('Normal','theplus')
			]
		);
		$this->add_group_control(Group_Control_Background::get_type(),
			[
				'name' => 'SiBoxNBg',
				'types' => ['classic','gradient'],
				'selector' => '{{WRAPPER}} .tp-search-form .tp-input-field',
			]
		);
		$this->add_group_control(Group_Control_Border::get_type(),
			[
				'name'=>'SiBoxNB',
				'label'=>esc_html__('Border','theplus'),
				'selector'=>'{{WRAPPER}} .tp-search-form .tp-input-field'
			]
		);
		$this->add_control('SiBoxNBrs',
			[
				'label'=>__('Border Radius','theplus'),
				'type'=>Controls_Manager::DIMENSIONS,
				'size_units'=>['px','%'],
				'selectors'=>[
					'{{WRAPPER}} .tp-search-form .tp-input-field'=>'border-radius:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'SiBoxNSd',
				'selector' => '{{WRAPPER}} .tp-search-form .tp-input-field',			
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab('SiBox_Hover',
			[
				'label' => esc_html__('Hover','theplus')
			]
		);
		$this->add_group_control(Group_Control_Background::get_type(),
			[
				'name' => 'SiBoxHBg',
				'types' => ['classic','gradient'],
				'selector' => '{{WRAPPER}} .tp-search-form .tp-input-field:hover',
			]
		);
		$this->add_group_control(Group_Control_Border::get_type(),
			[
				'name'=>'SiBoxHB',
				'label'=>esc_html__('Border','theplus'),
				'selector'=>'{{WRAPPER}} .tp-search-form .tp-input-field:hover'
			]
		);
		$this->add_control('SiBoxHBrs',
			[
				'label'=>__('Border Radius','theplus'),
				'type'=>Controls_Manager::DIMENSIONS,
				'size_units'=>['px','%'],
				'selectors'=>[
					'{{WRAPPER}} .tp-search-form .tp-input-field:hover'=>'border-radius:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'SiBoxHSd',
				'selector' => '{{WRAPPER}} .tp-search-form .tp-input-field:hover',			
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->add_control(
			'SiBoxSd_bf',
			[
				'label' => esc_html__( 'Backdrop Filter', 'theplus' ),
				'type' => Controls_Manager::POPOVER_TOGGLE,
				'label_off' => __( 'Default', 'theplus' ),
				'label_on' => __( 'Custom', 'theplus' ),
				'return_value' => 'yes',
			]
		);
		$this->add_control(
			'SiBoxSd_bf_blur',
			[
				'label' => esc_html__( 'Blur', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'max' => 100,
						'min' => 1,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 10,
				],
				'condition'    => [
					'SiBoxSd_bf' => 'yes',
				],
			]
		);
		$this->add_control(
			'SiBoxSd_bf_grayscale',
			[
				'label' => esc_html__( 'Grayscale', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'max' => 1,
						'min' => 0,
						'step' => 0.1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 0,
				],
				'selectors' => [
					'{{WRAPPER}} .tp-search-form .tp-input-field' => '-webkit-backdrop-filter:grayscale({{SiBoxSd_bf_grayscale.SIZE}})  blur({{SiBoxSd_bf_blur.SIZE}}{{SiBoxSd_bf_blur.UNIT}}) !important;backdrop-filter:grayscale({{SiBoxSd_bf_grayscale.SIZE}})  blur({{SiBoxSd_bf_blur.SIZE}}{{SiBoxSd_bf_blur.UNIT}}) !important;',
				 ],
				'condition'    => [
					'SiBoxSd_bf' => 'yes',
				],
			]
		);
		$this->end_popover();
		$this->end_controls_section();
		/*Search Input End*/

		/*Dropdown start*/
		$this->start_controls_section('selectdd_styling',
			[
				'label'=>esc_html__('DropDown','theplus'),
				'tab'=>Controls_Manager::TAB_STYLE			
			]
		);
		$this->add_responsive_control('DDpad',
			[
				'label'=> __('Inner Padding','theplus'),
				'type'=>Controls_Manager::DIMENSIONS,
				'size_units'=>['px','%'],
				'selectors'=>[
					'{{WRAPPER}} .tp-search-bar .tp-form-field .tp-select'=>'padding:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control('DDmargin',
			[
				'label'=> __('Margin','theplus'),
				'type'=>Controls_Manager::DIMENSIONS,
				'size_units'=>['px','%'],
				'separator'=>'after',
				'selectors'=>[
					'{{WRAPPER}} .tp-search-bar .tp-form-field .tp-select'=>'margin:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control('DDWidth',
			[
				'label'=>__('Width','theplus'),
				'type'=>Controls_Manager::SLIDER,
				'size_units'=>['px','%'],
				'range' => [
					'px' => [
						'min'=>0,
						'max'=>1000,
						'step'=>5,
					],
					'%'=>[
						'min'=>0,
						'max'=>100,
					],
				],
				'default'=>[
					'unit'=>'%',
					'size'=>'',
				],
				'selectors'=>[
					'{{WRAPPER}} .tp-search-bar .tp-form-field .tp-post-dropdown'=>'width:{{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(Group_Control_Typography::get_type(),
			[
				'name'=>'DDTypo',
				'label'=>__('Typography','theplus'),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector'=>'{{WRAPPER}} .tp-search-bar .tp-form-field .tp-select,{{WRAPPER}} .tp-search-bar .tp-form-field .tp-sbar-dropdown-menu',
			]
		);
		$this->start_controls_tabs('dd_tabs' );
		$this->start_controls_tab('dd_Normal',
			[
				'label' => esc_html__('Normal','theplus')
			]
		);
		$this->add_control('DdTxtCrN',
			[
				'label'=>__('Text Color','theplus'),
				'type'=>Controls_Manager::COLOR,
				'selectors'=>[
					'{{WRAPPER}} .tp-search-bar .tp-form-field .tp-select,{{WRAPPER}} .tp-search-bar .tp-form-field .tp-sbar-dropdown-menu'=>'color:{{VALUE}}'
				]
			]
		);
		$this->add_control('DdIconCrN',
			[
				'label'=>__('Icon Color','theplus'),
				'type'=>Controls_Manager::COLOR,
				'selectors'=>[
					'{{WRAPPER}} .tp-search-bar .tp-form-field .tp-select .tp-dd-icon'=>'color:{{VALUE}}'
				]
			]
		);
		$this->add_group_control(Group_Control_Background::get_type(),
			[
				'name'=>'DdBgN',
				'types'=>['classic','gradient'],
				'selector'=>'{{WRAPPER}} .tp-search-bar .tp-form-field .tp-sbar-dropdown,{{WRAPPER}} .tp-search-bar .tp-form-field .tp-sbar-dropdown .tp-sbar-dropdown-menu',
			]
		);
		$this->add_group_control(Group_Control_Border::get_type(),
			[
				'name'=>'DdTxtBN',
				'label'=>esc_html__('Border','theplus'),
				'selector'=>'{{WRAPPER}} .tp-search-bar .tp-form-field .tp-sbar-dropdown,{{WRAPPER}} .tp-search-bar .tp-form-field .tp-sbar-dropdown .tp-sbar-dropdown-menu',
			]
		);
		$this->add_responsive_control('DdTxtBRsN',
			[
				'label'=>__('Border Radius','theplus'),
				'type'=>Controls_Manager::DIMENSIONS,
				'size_units'=>['px','%'],
				'selectors'=>[
					'{{WRAPPER}} .tp-search-bar .tp-form-field .tp-sbar-dropdown,{{WRAPPER}} .tp-search-bar .tp-form-field .tp-sbar-dropdown .tp-sbar-dropdown-menu'=>'border-radius:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(Group_Control_Box_Shadow::get_type(),
			[
				'name'=>'DdBsdN',
				'selector'=>'{{WRAPPER}} .tp-search-bar .tp-form-field .tp-sbar-dropdown,{{WRAPPER}} .tp-search-bar .tp-form-field .tp-sbar-dropdown .tp-sbar-dropdown-menu',
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab('dd_Hover',
			[
				'label' => esc_html__('Hover','theplus')
			]
		);
		$this->add_control('DdTxtCrH',
			[
				'label'=>__('Text Color','theplus'),
				'type'=>Controls_Manager::COLOR,
				'selectors'=>[
					'{{WRAPPER}} .tp-search-bar .tp-form-field .tp-sbar-dropdown:hover .tp-select,{{WRAPPER}} .tp-search-bar .tp-form-field .tp-sbar-dropdown:hover .tp-sbar-dropdown-menu'=>'color:{{VALUE}}'
				]
			]
		);
		$this->add_control('DdIconCrH',
			[
				'label'=>__('Icon Color','theplus'),
				'type'=>Controls_Manager::COLOR,
				'selectors'=>[
					'{{WRAPPER}} .tp-search-bar .tp-form-field .tp-select:hover .tp-dd-icon,{{WRAPPER}} .tp-search-bar .tp-form-field .tp-sbar-dropdown:hover .tp-dd-icon'=>'color:{{VALUE}}'
				]
			]
		);
		$this->add_group_control(Group_Control_Background::get_type(),
			[
				'name'=>'DdBgH',
				'types'=>['classic','gradient'],
				'selector'=>'{{WRAPPER}} .tp-search-bar .tp-form-field .tp-sbar-dropdown:hover,{{WRAPPER}} .tp-search-bar .tp-form-field .tp-sbar-dropdown:hover .tp-sbar-dropdown-menu',
			]
		);
		$this->add_group_control(Group_Control_Border::get_type(),
			[
				'name'=>'DdTxtBH',
				'label'=>esc_html__('Border','theplus'),
				'selector'=>'{{WRAPPER}} .tp-search-bar .tp-form-field .tp-sbar-dropdown:hover,{{WRAPPER}} .tp-search-bar .tp-form-field .tp-sbar-dropdown:hover .tp-sbar-dropdown-menu',
			]
		);
		$this->add_responsive_control('DdTxtBRsH',
			[
				'label'=>__('Border Radius','theplus'),
				'type'=>Controls_Manager::DIMENSIONS,
				'size_units'=>['px','%',],
				'selectors'=>[
					'{{WRAPPER}} .tp-search-bar .tp-form-field .tp-sbar-dropdown:hover,{{WRAPPER}} .tp-search-bar .tp-form-field .tp-sbar-dropdown:hover .tp-sbar-dropdown-menu'=>'border-radius:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(Group_Control_Box_Shadow::get_type(),
			[
				'name'=>'DdBsdH',
				'selector'=>'{{WRAPPER}} .tp-search-bar .tp-form-field .tp-sbar-dropdown:hover,{{WRAPPER}} .tp-search-bar .tp-form-field .tp-sbar-dropdown:hover .tp-sbar-dropdown-menu',			
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_control('DDMouseHeading',
			[
				'label'=>__('Mouse Hover','theplus'),
				'type'=>Controls_Manager::HEADING,
				'separator'=>'before',
			]
		);
		$this->add_control('DdTxtCrMH',
			[
				'label'=>__('Text Color','theplus'),
				'type'=>Controls_Manager::COLOR,
				'selectors'=>[
					'{{WRAPPER}} .tp-search-bar .tp-form-field .tp-sbar-dropdown .tp-sbar-dropdown-menu .tp-searchbar-li:hover'=>'color:{{VALUE}}'
				]
			]
		);
		$this->add_group_control(Group_Control_Background::get_type(),
			[
				'name'=>'DdBgMH',
				'types'=>['classic','gradient'],
				'selector'=>'{{WRAPPER}} .tp-search-bar .tp-form-field .tp-sbar-dropdown .tp-sbar-dropdown-menu .tp-searchbar-li:hover',
			]
		);
		$this->add_group_control(Group_Control_Box_Shadow::get_type(),
			[
				'name'=>'DdBsdMH',
				'selector'=>'{{WRAPPER}} .tp-search-bar .tp-form-field .tp-sbar-dropdown .tp-sbar-dropdown-menu .tp-searchbar-li:hover',			
			]
		);
		$this->add_control('DDScrollHeading',
			[
				'label'=>__('Scroll Bar','theplus'),
				'type'=>Controls_Manager::HEADING,
				'separator'=>'before',
			]
		);
		$this->start_controls_tabs('DDscrollC_style');
		$this->start_controls_tab('DDscrollC_Bar',
			[
				'label' => esc_html__('Scrollbar','theplus'),
			]
		);
		$this->add_group_control(Group_Control_Background::get_type(),
			[
				'name'=>'DDScrollBg',
				'types'=>['classic','gradient'],
				'selector'=>'{{WRAPPER}} .tp-search-bar .tp-form-field .tp-sbar-dropdown .tp-sbar-dropdown-menu::-webkit-scrollbar',
			]
		);
		$this->add_responsive_control('DDScrollWidth',
			[
				'type'=>Controls_Manager::SLIDER,
				'label'=>esc_html__('Width', 'theplus'),
				'size_units'=>[ 'px' ],
				'range'=>[
					'px'=>[
						'min'=>1,
						'max'=>100,
						'step'=>1,
					],
				],
				'render_type'=>'ui',
				'selectors'=>[
					'{{WRAPPER}} .tp-search-bar .tp-form-field .tp-sbar-dropdown .tp-sbar-dropdown-menu::-webkit-scrollbar' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab('DDscrollC_Tmb',
			[
				'label'=>esc_html__('Thumb','theplus'),
			]
		);
		$this->add_group_control(Group_Control_Background::get_type(),
			[
				'name'=>'DDThumbBg',
				'types'=>['classic','gradient'],
				'selector'=>'{{WRAPPER}} .tp-search-bar .tp-form-field .tp-sbar-dropdown .tp-sbar-dropdown-menu::-webkit-scrollbar-thumb',
			]
		);
		$this->add_responsive_control('DDThumbBrs',
			[
				'label'=>esc_html__('Border Radius','theplus'),
				'type'=>Controls_Manager::DIMENSIONS,
				'size_units'=>['px', '%'],
				'selectors'=>[
					'{{WRAPPER}} .tp-search-bar .tp-form-field .tp-sbar-dropdown .tp-sbar-dropdown-menu::-webkit-scrollbar-thumb'=>'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',				
				],
			]
		);
		$this->add_group_control(Group_Control_Box_Shadow::get_type(),
			[
				'name'=>'DDThumbBsw',
				'selector'=>'{{WRAPPER}} .tp-search-bar .tp-form-field .tp-sbar-dropdown .tp-sbar-dropdown-menu::-webkit-scrollbar-thumb',	
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab('DDscrollC_Trk',
			[
				'label' => esc_html__('Track','theplus'),
			]
		);
		$this->add_group_control(Group_Control_Background::get_type(),
			[
				'name'=>'DDTrackBg',
				'types'=>['classic','gradient'],
				'selector'=>'{{WRAPPER}} .tp-search-bar .tp-form-field .tp-sbar-dropdown .tp-sbar-dropdown-menu::-webkit-scrollbar-track',
			]
		);
		$this->add_responsive_control('DDTrackBRs',
			[
				'label'=>esc_html__( 'Border Radius', 'theplus' ),
				'type'=>Controls_Manager::DIMENSIONS,
				'size_units'=>['px','%'],
				'selectors'=>[
					'{{WRAPPER}} .tp-search-bar .tp-form-field .tp-sbar-dropdown .tp-sbar-dropdown-menu::-webkit-scrollbar-track'=>'border-radius:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',				
				],
			]
		);
		$this->add_group_control(Group_Control_Box_Shadow::get_type(),
			[
				'name'=>'DDTrackBsw',
				'selector'=>'{{WRAPPER}} .tp-search-bar .tp-form-field .tp-sbar-dropdown .tp-sbar-dropdown-menu::-webkit-scrollbar-track',	
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_control('DDBox_had',
			[
				'label'=>__('Box Options','theplus'),
				'type'=>Controls_Manager::HEADING,
				'separator'=>'before',
			]
		);
		$this->add_control('DDBoxPad',
			[
				'label'=>__('Padding','theplus'),
				'type'=>Controls_Manager::DIMENSIONS,
				'size_units'=>['px','%'],
				'selectors'=>[
					'{{WRAPPER}} .tp-search-bar .tp-search-form .tp-post-dropdown'=>'padding:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_control('DDBoxMrg',
			[
				'label'=>__('Margin','theplus'),
				'type'=>Controls_Manager::DIMENSIONS,
				'size_units'=>['px','%'],
				'selectors'=>[
					'{{WRAPPER}} .tp-search-bar .tp-search-form .tp-post-dropdown'=>'margin:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->start_controls_tabs('DDBox_tabs');
		$this->start_controls_tab('DDBox_Normal',
			[
				'label' => esc_html__('Normal','theplus')
			]
		);
		$this->add_group_control(Group_Control_Background::get_type(),
			[
				'name'=>'DDBoxNBg',
				'types'=>['classic','gradient'],
				'selector'=>'{{WRAPPER}} .tp-search-bar .tp-search-form .tp-post-dropdown',
			]
		);
		$this->add_group_control(Group_Control_Border::get_type(),
			[
				'name'=>'DDBoxNB',
				'label'=>esc_html__('Border','theplus'),
				'selector'=>'{{WRAPPER}} .tp-search-bar .tp-search-form .tp-post-dropdown'
			]
		);
		$this->add_control('DDBoxNBrs',
			[
				'label'=>__('Border Radius','theplus'),
				'type'=>Controls_Manager::DIMENSIONS,
				'size_units'=>['px','%'],
				'selectors'=>[
					'{{WRAPPER}} .tp-search-bar .tp-search-form .tp-post-dropdown'=>'border-radius:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(Group_Control_Box_Shadow::get_type(),
			[
				'name'=>'DDBoxNSd',
				'selector'=>'{{WRAPPER}} .tp-search-bar .tp-search-form .tp-post-dropdown',
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab('DDBox_Hover',
			[
				'label'=>esc_html__('Hover','theplus')
			]
		);
		$this->add_group_control(Group_Control_Background::get_type(),
			[
				'name'=>'DDBoxHBg',
				'types'=>['classic','gradient'],
				'selector'=>'{{WRAPPER}} .tp-search-bar .tp-search-form .tp-post-dropdown:hover',
			]
		);
		$this->add_group_control(Group_Control_Border::get_type(),
			[
				'name'=>'DDBoxHB',
				'label'=>esc_html__('Border','theplus'),
				'selector'=>'{{WRAPPER}} .tp-search-bar .tp-search-form .tp-post-dropdown:hover'
			]
		);
		$this->add_control('DDBoxHBrs',
			[
				'label'=>__('Border Radius','theplus'),
				'type'=>Controls_Manager::DIMENSIONS,
				'size_units'=>['px','%'],
				'selectors'=>[
					'{{WRAPPER}} .tp-search-bar .tp-search-form .tp-post-dropdown:hover'=>'border-radius:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'DDBoxHSd',
				'selector' => '{{WRAPPER}} .tp-search-bar .tp-search-form .tp-post-dropdown:hover',			
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
  		/*Dropdown End*/

		/*Button Start*/
		$this->start_controls_section('Button_styling',
			[
				'label' => esc_html__('Button','theplus'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
                    'showBtn'=>'yes',
				],	
			]
		);
		$this->add_responsive_control('BtnPadding',
			[
				'label'=>__('Padding','theplus'),
				'type'=>Controls_Manager::DIMENSIONS,
				'size_units'=>['px','%'],
				'selectors'=>[
					'{{WRAPPER}} .tp-search-bar .tp-form-field .tp-search-btn'=>'padding:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control('BtnMargin',
			[
				'label'=>__('Margin','theplus'),
				'type'=>Controls_Manager::DIMENSIONS,
				'size_units'=>['px','%'],
				'separator'=>'after',
				'selectors'=>[
					'{{WRAPPER}} .tp-search-bar .tp-form-field .tp-search-btn'=>'margin:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(Group_Control_Typography::get_type(),
			[
				'name'=>'btnTypo',
				'label'=>__('Typography','theplus'),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector'=>'{{WRAPPER}} .tp-search-form .tp-search-btn',
				'separator'=>'before',
				'condition' => [
					'showBtn'=>'yes',
				],	
			]
		);
		$this->add_control('Btnalign',
			[
				'label'=>esc_html__('Button Alignment','theplus'),
				'type'=>Controls_Manager::CHOOSE,
				'options'=>[
					'left'=>[
						'title'=>esc_html__('Left','theplus'),
						'icon'=>'eicon-text-align-left',
					],
					'center'=>[
						'title'=>esc_html__( 'Center','theplus'),
						'icon'=>'eicon-text-align-center',
					],
					'right'=>[
						'title'=>esc_html__('Right','theplus'),
						'icon'=>'eicon-text-align-right',
					],
				],
				'default'=>'left',
				'toggle'=>false,
				'selectors'=>[
					'{{WRAPPER}} .tp-search-bar .tp-form-field .tp-btn-wrap'=>'justify-content:{{VALUE}}'
				]
			]
		);
		$this->add_responsive_control('Btntxtoffset',
			[
				'label'=>__('Offset','theplus'),
				'type'=>Controls_Manager::SLIDER,
				'size_units'=>['px','%'],
				'range'=>[
					'px'=>[
						'min'=>0,
						'max'=>1000,
						'step'=>5,
					],
					'%'=>[
						'min'=>0,
						'max'=>100,
					],
				],
				'default'=>[
					'unit'=>'px',
					'size'=>'',
				],
				'selectors'=>[
					'{{WRAPPER}} .tp-search-bar .tp-form-field .tp-search-btn-txt.before'=>'padding-left:{{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .tp-search-bar .tp-form-field .tp-search-btn-txt.after'=>'padding-right:{{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'showBtn'=>'yes',
					'BtnMedia'=>['icon','image']
				],
			]
		);
		$this->start_controls_tabs('Button_tabs');
		$this->start_controls_tab('Button_Normal',
			[
				'label' => esc_html__('Normal','theplus')
			]
		);
		$this->add_control('BtnNtxtCr',
			[
				'label'=>__('Text Color','theplus'),
				'type'=>Controls_Manager::COLOR,
				'selectors'=>[
					'{{WRAPPER}} .tp-search-bar .tp-search-form .tp-search-btn-txt'=>'color:{{VALUE}}'
				],
				'condition' => [
					'showBtn'=>'yes',
				],	
			]
		);
		$this->add_control('BtnNIconCr',
			[
				'label'=>__('Icon Color','theplus'),
				'type'=>Controls_Manager::COLOR,
				'selectors'=>[
					'{{WRAPPER}} .tp-search-bar .tp-search-form .tp-button-icon'=>'color:{{VALUE}}',
					'{{WRAPPER}} .tp-search-bar .tp-search-form .tp-button-icon svg'=>'fill:{{VALUE}}'
				],
				'condition' => [
					'showBtn'=>'yes',
					'BtnMedia'=>'icon',
				],	
			]
		);
		$this->add_responsive_control('BtnNIconSvgSize',
			[
				'label'=>__('Width','theplus'),
				'type'=>Controls_Manager::SLIDER,
				'size_units'=>['px'],
				'range' => [
					'px' => [
						'min'=>0,
						'max'=>150,
						'step'=>2,
					],
				],
				'default'=>[
					'unit'=>'px',
					'size'=>'20',
				],
				'condition' => [
					'showBtn'=>'yes',
					'BtnMedia'=>'icon',
				],
				'selectors'=>[
					'{{WRAPPER}} .tp-search-bar .tp-search-form .tp-button-icon svg'=>'width:{{SIZE}}{{UNIT}};height:{{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(Group_Control_Background::get_type(),
			[
				'name'=>'sbtnBgtype',
				'types'=>['classic','gradient'],
				'selector'=>'{{WRAPPER}} .tp-search-bar .tp-search-form .tp-search-btn',
				'condition'=>[
					'showBtn'=>'yes',
				],	
			]
		);
		$this->add_group_control(Group_Control_Border::get_type(),
			[
				'name'=>'sbtnBorder',
				'label'=>esc_html__('Border','theplus'),
				'selector'=>'{{WRAPPER}} .tp-search-bar .tp-search-form .tp-search-btn',
				'condition' => [
					'showBtn'=>'yes',
				],	
			]
		);
		$this->add_responsive_control('sbtnBradius',
			[
				'label'=>__('Border Radius','theplus'),
				'type'=>Controls_Manager::DIMENSIONS,
				'size_units'=>['px','%',],
				'selectors'=>[
					'{{WRAPPER}} .tp-search-bar .tp-search-form .tp-search-btn'=>'border-radius:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'showBtn'=>'yes',
				],	
			]
		);
		$this->add_group_control( Group_Control_Box_Shadow::get_type(),
			[
				'name'=>'sbtnBshadow',
				'selector'=>'{{WRAPPER}} .tp-search-form .tp-search-btn',
				'condition'=>[
					'showBtn'=>'yes',
				],	
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab('Button_Hover',
			[
				'label'=>esc_html__('Hover','theplus')
			]
		);
		$this->add_control('BtnHtxtCr',
			[
				'label'=>__('Text Color','theplus'),
				'type'=>Controls_Manager::COLOR,
				'selectors'=>[
					'{{WRAPPER}}  .tp-search-bar .tp-search-form .tp-search-btn-txt:hover'=>'color:{{VALUE}}'
				],
				'condition' => [
					'showBtn'=>'yes',
				],	
			]
		);
		$this->add_control('BtnHIconCr',
			[
				'label'=>__('Icon Color','theplus'),
				'type'=>Controls_Manager::COLOR,
				'selectors'=>[
					'{{WRAPPER}} .tp-search-bar .tp-search-btn:hover .tp-button-icon'=>'color:{{VALUE}}',
					'{{WRAPPER}} .tp-search-bar .tp-search-btn:hover .tp-button-icon svg'=>'fill:{{VALUE}}'
				],
				'condition' => [
					'showBtn'=>'yes',
					'BtnMedia'=>'icon',
				],	
			]
		);
		$this->add_group_control(Group_Control_Background::get_type(),
			[
				'name'      => 'sbtnHbg',
				'types'     => ['classic','gradient'],
				'selector'  => '{{WRAPPER}} .tp-search-form .tp-search-btn:hover',
				'condition' => [
					'showBtn'=>'yes',
				],	
			]
		);
		$this->add_group_control(Group_Control_Border::get_type(),
			[
				'name'=>'sbtnHborder',
				'label'=>esc_html__('Border','theplus'),
				'selector'=>'{{WRAPPER}} .tp-search-form .tp-search-btn:hover',
				'condition' => [
					'showBtn'=>'yes',
				],	
			]
		);
		$this->add_responsive_control('sbtnHBradius',
			[
				'label'=>__('Border Radius','theplus'),
				'type'=>Controls_Manager::DIMENSIONS,
				'size_units'=>['px','%',],
				'selectors'=>[
					'{{WRAPPER}} .tp-search-form .tp-search-btn:hover'=>'border-radius:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'showBtn'=>'yes',
				],	
			]
		);
		$this->add_group_control(Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'sbtnHshadow',
				'selector' => '{{WRAPPER}} .tp-search-form .tp-search-btn:hover',
				'condition' => [
					'showBtn'=>'yes',
				],	
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();	
		$this->add_control('Btnimg_Hading',
			[
				'label'=>__('Image Options','theplus'),
				'type'=>Controls_Manager::HEADING,
				'separator'=>'before',
				'condition' => [
					'showBtn'=>'yes',
					'BtnMedia'=>'image'
				],
			]
		);
		$this->add_responsive_control('BtnImgWidth',
			[
				'label'=>__('Image Width','theplus'),
				'type'=>Controls_Manager::SLIDER,
				'size_units'=>['px','%'],
				'range'=>[
					'px'=>[
						'min'=>0,
						'max'=>1000,
						'step'=>5,
					],
					'%'=>[
						'min'=>0,
						'max'=>100,
					],
				],
				'default'=>[
					'unit'=>'px',
					'size'=>'',
				],
				'selectors'=>[
					'{{WRAPPER}} .tp-search-bar .tp-form-field .tp-button-ImageTag'=>'width:{{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'showBtn'=>'yes',
					'BtnMedia'=>'image'
				],	
			]
		);
		$this->add_group_control(Group_Control_Border::get_type(),
			[
				'name'=>'BtnImgB',
				'label'=>__('Border','theplus'),
				'selector'=>'{{WRAPPER}} .tp-search-bar .tp-form-field .tp-button-ImageTag',
				'condition' => [
					'showBtn'=>'yes',
					'BtnMedia'=>'image'
				],
			]
		);
		$this->add_responsive_control('BtnImgBrs',
			[
				'label'=> __('Border Radius','theplus'),
				'type'=>Controls_Manager::DIMENSIONS,
				'size_units'=>['px','%'],
				'selectors'=>[
					'{{WRAPPER}} .tp-search-bar .tp-form-field .tp-button-ImageTag'=>'border-radius:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'showBtn'=>'yes',
					'BtnMedia'=>'image'
				],
			]
		);


		$this->add_control('BtnBox_had',
			[
				'label'=>__('Box Options','theplus'),
				'type'=>Controls_Manager::HEADING,
				'separator'=>'before',
			]
		);
		$this->add_control('BTNBoxPad',
			[
				'label'=>__('Padding','theplus'),
				'type'=>Controls_Manager::DIMENSIONS,
				'size_units'=>['px','%'],
				'selectors'=>[
					'{{WRAPPER}} .tp-search-bar .tp-search-form .tp-btn-wrap'=>'padding:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_control('BTNBoxMrg',
			[
				'label'=>__('Margin','theplus'),
				'type'=>Controls_Manager::DIMENSIONS,
				'size_units'=>['px','%'],
				'selectors'=>[
					'{{WRAPPER}} .tp-search-bar .tp-search-form .tp-btn-wrap'=>'margin:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->start_controls_tabs('BTNBox_tabs');
		$this->start_controls_tab('BTNBox_Normal',
			[
				'label' => esc_html__('Normal','theplus')
			]
		);
		$this->add_group_control(Group_Control_Background::get_type(),
			[
				'name'=>'BTNBoxNBg',
				'types'=>['classic','gradient'],
				'selector'=>'{{WRAPPER}} .tp-search-bar .tp-search-form .tp-btn-wrap',
			]
		);
		$this->add_group_control(Group_Control_Border::get_type(),
			[
				'name'=>'BTNBoxNB',
				'label'=>esc_html__('Border','theplus'),
				'selector'=>'{{WRAPPER}} .tp-search-bar .tp-search-form .tp-btn-wrap'
			]
		);
		$this->add_control('BTNBoxNBrs',
			[
				'label'=>__('Border Radius','theplus'),
				'type'=>Controls_Manager::DIMENSIONS,
				'size_units'=>['px','%'],
				'selectors'=>[
					'{{WRAPPER}} .tp-search-bar .tp-search-form .tp-btn-wrap'=>'border-radius:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(Group_Control_Box_Shadow::get_type(),
			[
				'name'=>'BTNBoxNSd',
				'selector'=>'{{WRAPPER}} .tp-search-bar .tp-search-form .tp-btn-wrap',
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab('BTNBox_Hover',
			[
				'label'=>esc_html__('Hover','theplus')
			]
		);
		$this->add_group_control(Group_Control_Background::get_type(),
			[
				'name'=>'BTNBoxHBg',
				'types'=>['classic','gradient'],
				'selector'=>'{{WRAPPER}} .tp-search-bar .tp-search-form .tp-btn-wrap:hover',
			]
		);
		$this->add_group_control(Group_Control_Border::get_type(),
			[
				'name'=>'BTNBoxHB',
				'label'=>esc_html__('Border','theplus'),
				'selector'=>'{{WRAPPER}} .tp-search-bar .tp-search-form .tp-btn-wrap:hover'
			]
		);
		$this->add_control('BTNBoxHBrs',
			[
				'label'=>__('Border Radius','theplus'),
				'type'=>Controls_Manager::DIMENSIONS,
				'size_units'=>['px','%'],
				'selectors'=>[
					'{{WRAPPER}} .tp-search-bar .tp-search-form .tp-btn-wrap:hover'=>'border-radius:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(Group_Control_Box_Shadow::get_type(),
			[
				'name'=>'BTNBoxHSd',
				'selector'=>'{{WRAPPER}} .tp-search-bar .tp-search-form .tp-btn-wrap:hover',			
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		/*Button End*/

		/*Results start*/
		$this->start_controls_section('RA_styling',
			[
				'label'=>esc_html__('Results Box','theplus'),
				'tab'=>Controls_Manager::TAB_STYLE,
				'condition'=>[
                    'ajaxsearch'=>'yes',
				],		
			]
		);
		$this->add_control('RaBoxHad',
			[
				'label'=>__('Box Option','theplus'),
				'type'=>Controls_Manager::HEADING,
			]
		);
		$this->add_responsive_control('RabPadding',
			[
				'label'=>__('Padding','theplus'),
				'type'=>Controls_Manager::DIMENSIONS,
				'size_units'=>['px','%',],
				'selectors'=>[
					'{{WRAPPER}} .tp-search-bar .tp-search-area'=>'padding:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control('RabMargin',
			[
				'label'=>__('Margin','theplus'),
				'type'=>Controls_Manager::DIMENSIONS,
				'size_units'=>['px','%',],
				'separator'=>'after',
				'selectors'=>[
					'{{WRAPPER}} .tp-search-bar .tp-search-area'=>'margin:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],				
			]
		);
		$this->add_responsive_control('RaWidth',
			[
				'label'=>__('Width','theplus'),
				'type'=>Controls_Manager::SLIDER,
				'size_units'=>['px','%'],
				'range'=>[
					'px'=>[
						'min'=>0,
						'max'=>1000,
						'step'=>1,
					],
					'%'=>[
						'min'=>0,
						'max'=>100,
					],
				],
				'default'=>[
					'unit'=>'%',
					'size'=>'',
				],
				'selectors'=>[
					'{{WRAPPER}} .tp-search-bar .tp-search-area'=>'width:{{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(Group_Control_Typography::get_type(),
			[
				'name'=>'RaTypo',
				'label'=>__('Typography','theplus'),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector'=>'{{WRAPPER}} .tp-search-bar .tp-search-area',
			]
		);
		$this->start_controls_tabs('Ra_tabs');
		$this->start_controls_tab('Ra_Normal',
			[
				'label' => esc_html__('Normal','theplus')
			]
		);
		$this->add_control('RaTxtCrN',
			[
				'label'=>__('Text Color','theplus'),
				'type'=>Controls_Manager::COLOR,
				'selectors'=>[
					'{{WRAPPER}} .tp-search-bar .tp-search-area'=>'color:{{VALUE}}'
				],
			]
		);
		$this->add_group_control(Group_Control_Background::get_type(),
			[
				'name'=>'RaTxtBgCrN',
				'types'=>['classic','gradient'],
				'selector'=>'{{WRAPPER}} .tp-search-bar .tp-search-area',
			]
		);
		$this->add_group_control(Group_Control_Border::get_type(),
			[
				'name'=>'RaBN',
				'label'=>esc_html__('Border','theplus'),
				'selector'=>'{{WRAPPER}} .tp-search-bar .tp-search-area',	
			]
		);
		$this->add_responsive_control('RaBRsN',
			[
				'label'=>__('Border Radius','theplus'),
				'type'=>Controls_Manager::DIMENSIONS,
				'size_units'=>['px','%',],
				'selectors'=>[
					'{{WRAPPER}} .tp-search-bar .tp-search-area'=>'border-radius:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],	
			]
		);
		$this->add_group_control(Group_Control_Box_Shadow::get_type(),
			[
				'name'=>'RaBsdN',
				'selector'=>'{{WRAPPER}} .tp-search-bar .tp-search-area',	
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab('Ra_Hover',
			[
				'label' => esc_html__('Hover','theplus')
			]
		);
		$this->add_control('RaTxtBgCrH',
			[
				'label'=>__('Color','theplus'),
				'type'=>Controls_Manager::COLOR,
				'selectors'=>[
					'{{WRAPPER}} .tp-search-bar .tp-search-area:hover'=>'color:{{VALUE}}'
				],
			]
		);
		$this->add_group_control(Group_Control_Background::get_type(),
			[
				'name'=>'listBgtype',
				'types'=>['classic','gradient'],
				'selector'=>'{{WRAPPER}} .tp-search-bar .tp-search-area:hover',
			]
		);
		$this->add_group_control(Group_Control_Border::get_type(),
			[
				'name'=>'RaBH',
				'label'=>esc_html__('Border','theplus'),
				'selector'=>'{{WRAPPER}} .tp-search-bar .tp-search-area:hover',	
			]
		);
		$this->add_responsive_control('RaBRsH',
			[
				'label'=>__('Border Radius','theplus'),
				'type'=>Controls_Manager::DIMENSIONS,
				'size_units'=>['px','%',],
				'selectors'=>[
					'{{WRAPPER}} .tp-search-bar .tp-search-area:hover'=>'border-radius:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],	
			]
		);
		$this->add_group_control(Group_Control_Box_Shadow::get_type(),
			[
				'name'=>'RaBsdH',
				'selector'=>'{{WRAPPER}} .tp-search-bar .tp-search-area:hover',
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		/*Results Box End*/

		/*Results Heading Start*/
		$this->start_controls_section('RaIn_styling',
			[
				'label'=>esc_html__('Results Heading','theplus'),
				'tab'=>Controls_Manager::TAB_STYLE,
				'condition'=>[
                    'ajaxsearch'=>'yes',
				],		
			]
		);
		$this->add_responsive_control('RaInPadding',
			[
				'label'=>__('Padding','theplus'),
				'type'=>Controls_Manager::DIMENSIONS,
				'size_units'=>['px','%',],
				'selectors'=>[
					'{{WRAPPER}} .tp-search-bar .tp-search-area .tp-search-header'=>'padding:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control('RaInMargin',
			[
				'label'=>__('Margin','theplus'),
				'type'=>Controls_Manager::DIMENSIONS,
				'size_units'=>['px','%',],
				'separator'=>'after',
				'selectors'=>[
					'{{WRAPPER}} .tp-search-bar .tp-search-area .tp-search-header'=>'margin:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(Group_Control_Typography::get_type(),
			[
				'name'=>'RaInTypo',
				'label'=>__('Total Count Typography','theplus'),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector'=>'{{WRAPPER}} .tp-search-bar .tp-search-area .tp-search-header .tp-search-resultcount',
				'condition'=>[
					'totalresult'=>'yes',
				],
			]
		);
		$this->start_controls_tabs('RaIn_tabs');
		$this->start_controls_tab('RaIn_Normal',
			[
				'label' => esc_html__('Normal','theplus')
			]
		);	
		$this->add_control('RaInTxtCrN',
			[
				'label'=>__('Total Count Text Color','theplus'),
				'type'=>Controls_Manager::COLOR,
				'selectors'=>[
					'{{WRAPPER}} .tp-search-bar .tp-search-area .tp-search-header .tp-search-resultcount'=>'color:{{VALUE}}'
				],
				'condition' => [
					'totalresult'=>'yes',
				],
			]
		);
		$this->add_group_control(Group_Control_Background::get_type(),
			[
				'name'=>'RaInBgCrN',
				'types'=>['classic','gradient'],
				'selector'=>'{{WRAPPER}} .tp-search-bar .tp-search-area .tp-search-header',
			]
		);
		$this->add_group_control(Group_Control_Border::get_type(),
			[
				'name'=>'RaInBN',
				'label'=>esc_html__('Border','theplus'),
				'selector'=>'{{WRAPPER}} .tp-search-bar .tp-search-area .tp-search-header',	
			]
		);
		$this->add_responsive_control('RaInBRsN',
			[
				'label'=>__('Border Radius','theplus'),
				'type'=>Controls_Manager::DIMENSIONS,
				'size_units'=>['px','%',],
				'selectors'=>[
					'{{WRAPPER}} .tp-search-bar .tp-search-area .tp-search-header'=>'border-radius:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],	
			]
		);
		$this->add_group_control(Group_Control_Box_Shadow::get_type(),
			[
				'name'=>'RaInBsdN',
				'selector'=>'{{WRAPPER}} .tp-search-bar .tp-search-area .tp-search-header',	
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab('RaIn_Hover',
			[
				'label' => esc_html__('Hover','theplus')
			]
		);
		$this->add_control('RaInTxtCrH',
			[
				'label'=>__('Total Text Color','theplus'),
				'type'=>Controls_Manager::COLOR,
				'selectors'=>[
					'{{WRAPPER}} .tp-search-bar .tp-search-area:hover .tp-search-header .tp-search-resultcount'=>'color:{{VALUE}}'
				],
				'condition' => [
					'totalresult'=>'yes',
				],
			]
		);
		$this->add_group_control(Group_Control_Background::get_type(),
			[
				'name'=>'RaInBgCrH',
				'types'=>['classic','gradient'],
				'selector'=>'{{WRAPPER}} .tp-search-bar .tp-search-area:hover .tp-search-header',
			]
		);
		$this->add_group_control(Group_Control_Border::get_type(),
			[
				'name'=>'RaInBH',
				'label'=>esc_html__('Border','theplus'),
				'selector'=>'{{WRAPPER}} .tp-search-bar .tp-search-area:hover .tp-search-header',	
			]
		);
		$this->add_responsive_control('RaInBRsH',
			[
				'label'=>__('Border Radius','theplus'),
				'type'=>Controls_Manager::DIMENSIONS,
				'size_units'=>['px','%',],
				'selectors'=>[
					'{{WRAPPER}} .tp-search-bar .tp-search-area:hover .tp-search-header'=>'border-radius:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],	
			]
		);
		$this->add_group_control(Group_Control_Box_Shadow::get_type(),
			[
				'name'=>'RaInBsdH',
				'selector'=>'{{WRAPPER}} .tp-search-bar .tp-search-area:hover .tp-search-header',	
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		/*Results Heading End*/

		/*Results Content Start*/
		$this->start_controls_section('RaInBody_styling',
			[
				'label'=>esc_html__('Results Content','theplus'),
				'tab'=>Controls_Manager::TAB_STYLE,
				'condition'=>[
                    'ajaxsearch'=>'yes',
				],
			]
		);
		$this->add_control('RainBPadingPop',
			[
				'label'=>__('Padding Option','theplus'),
				'type'=>Controls_Manager::POPOVER_TOGGLE,
				'label_off'=>__('Default','theplus'),
				'label_on'=>__('Custom','theplus'),
				'return_value'=>'yes',
				'default'=>'yes',
			]
		);
		$this->start_popover();	
		$this->add_responsive_control('RaInBTitlePad',
			[
				'label'=>__('Title Padding','theplus'),
				'type'=>Controls_Manager::DIMENSIONS,
				'size_units'=>['px','%',],
				'selectors'=>[
					'{{WRAPPER}} .tp-search-bar .tp-search-area .tp-serpost-title'=>'padding:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'=>[
					'TitleOn'=>'yes',
				],
			]
		);
		$this->add_responsive_control('RaInBContPad',
			[
				'label'=>__('Content Padding','theplus'),
				'type'=>Controls_Manager::DIMENSIONS,
				'size_units'=>['px','%',],
				'selectors'=>[
					'{{WRAPPER}} .tp-search-bar .tp-search-area .tp-serpost-excerpt'=>'padding:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'=>[
					'ContentOn'=>'yes',
				],
			]
		);
		$this->add_responsive_control('RaInBPricePad',
			[
				'label'=>__('Woo Price Padding','theplus'),
				'type'=>Controls_Manager::DIMENSIONS,
				'size_units'=>['px','%',],
				'selectors'=>[
					'{{WRAPPER}} .tp-search-bar .tp-search-area .tp-serpost-price'=>'padding:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'=>[
					'PriceOn'=>'yes',
				],
			]
		);
		$this->add_responsive_control('RaInBSdPad',
			[
				'label'=>__('Woo ShortDesc Padding','theplus'),
				'type'=>Controls_Manager::DIMENSIONS,
				'size_units'=>['px','%',],
				'selectors'=>[
					'{{WRAPPER}} .tp-search-bar .tp-search-area .tp-serpost-shortDesc'=>'padding:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'=>[
					'ShortDescOn'=>'yes',
				],
			]
		);
		$this->end_popover();

		$this->add_group_control(Group_Control_Typography::get_type(),
			[
				'name'=>'RaInBTitleTypo',
				'label'=>__('Title Typography','theplus'),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector'=>'{{WRAPPER}} .tp-search-bar .tp-search-area .tp-serpost-title',
				'condition'=>[
					'TitleOn'=>'yes',
				],
			]
		);
		$this->add_group_control(Group_Control_Typography::get_type(),
			[
				'name'=>'RaInBContentTypo',
				'label'=>__('Content Typography','theplus'),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector'=>'{{WRAPPER}} .tp-search-bar .tp-search-area .tp-serpost-excerpt',
				'condition'=>[
					'ContentOn'=>'yes',
				],
			]
		);
		$this->add_group_control(Group_Control_Typography::get_type(),
			[
				'name'=>'RaInBPriceTypo',
				'label'=>__('Woo Price Typography','theplus'),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector'=>'{{WRAPPER}} .tp-search-bar .tp-search-area .tp-serpost-price',
				'condition'=>[
					'PriceOn'=>'yes',
				],
			]
		);
		$this->add_group_control(Group_Control_Typography::get_type(),
			[
				'name'=>'RaInBSdTypo',
				'label'=>__('Woo ShortDesc Typography','theplus'),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector'=>'{{WRAPPER}} .tp-search-bar .tp-search-area .tp-serpost-shortDesc',
				'condition'=>[
					'ShortDescOn'=>'yes',
				],
			]
		);

		$this->start_controls_tabs('RaInBody_tabs');
		$this->start_controls_tab('RaInBody_Normal',
			[
				'label' => esc_html__('Normal','theplus')
			]
		);
		$this->add_control('RaTilteTxCrN',
			[
				'label'=>__('Title Color','theplus'),
				'type'=>Controls_Manager::COLOR,
				'selectors'=>[
					'{{WRAPPER}} .tp-search-bar .tp-search-area .tp-serpost-title'=>'color:{{VALUE}}'
				],
				'condition'=>[
					'TitleOn'=>'yes',
				],
			]
		);
		$this->add_control('RaContentTxCrN',
			[
				'label'=>__('Content Color','theplus'),
				'type'=>Controls_Manager::COLOR,
				'selectors'=>[
					'{{WRAPPER}} .tp-search-bar .tp-serpost-title:hover .tp-serpost-title'=>'color:{{VALUE}}',
				],
				'condition' => [
					'ContentOn'=>'yes',
				],
			]
		);
		$this->add_control('RaPriceTxCrN',
			[
				'label'=>__('Woo Price Color','theplus'),
				'type'=>Controls_Manager::COLOR,
				'selectors'=>[
					'{{WRAPPER}} .tp-search-bar .tp-search-area .tp-serpost-price'=>'color:{{VALUE}}'
				],
				'condition' => [
					'PriceOn'=>'yes',
				],
			]
		);
		$this->add_control('RaSdTxCrN',
			[
				'label'=>__('Woo Short Description Color','theplus'),
				'type'=>Controls_Manager::COLOR,
				'selectors'=>[
					'{{WRAPPER}} .tp-search-bar .tp-search-area .tp-serpost-shortDesc'=>'color:{{VALUE}}'
				],
				'condition' => [
					'ShortDescOn'=>'yes',
				],
			]
		);
		$this->add_group_control(Group_Control_Background::get_type(),
			[
				'name'=>'RatitleBgCrN',
				'types'=>['classic','gradient'],
				'selector'=>'{{WRAPPER}} .tp-search-bar .tp-search-area .tp-search-slider .tp-ser-item',
			]
		);			
		$this->add_group_control(Group_Control_Border::get_type(),
			[
				'name'=>'RaInBoxN',
				'label'=>esc_html__('Border','theplus'),
				'selector'=>'{{WRAPPER}} .tp-search-bar .tp-search-area .tp-search-slider .tp-ser-item',	
			]
		);
		$this->add_responsive_control('RaInBoxBrsN',
			[
				'label'=>__('Border Radius','theplus'),
				'type'=>Controls_Manager::DIMENSIONS,
				'size_units'=>['px','%',],
				'selectors'=>[
					'{{WRAPPER}} .tp-search-bar .tp-search-area .tp-search-slider .tp-ser-item'=>'border-radius:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(Group_Control_Box_Shadow::get_type(),
			[
				'name'=>'RaInBoxBsdN',
				'selector'=>'{{WRAPPER}} .tp-search-bar .tp-search-area .tp-search-slider .tp-ser-item',
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab('RaInBody_Hover',
			[
				'label' => esc_html__('Hover','theplus')
			]
		);
		$this->add_control('RaTilteTxCrH',
			[
				'label'=>__('Title Color','theplus'),
				'type'=>Controls_Manager::COLOR,
				'selectors'=>[
					'{{WRAPPER}} .tp-search-bar .tp-serpost-title:hover .tp-serpost-title'=>'color:{{VALUE}}'
				],
				'condition'=>[
					'TitleOn'=>'yes',
				],
			]
		);
		$this->add_control('RaContentTxCrH',
			[
				'label'=>__('Content Color','theplus'),
				'type'=>Controls_Manager::COLOR,
				'selectors'=>[
					'{{WRAPPER}} .tp-search-bar .tp-search-area:hover .tp-serpost-excerpt'=>'color:{{VALUE}}'
				],
				'condition' => [
					'ContentOn'=>'yes',
				],
			]
		);
		$this->add_control('RaPriceTxCrH',
			[
				'label'=>__('Woo Price Color','theplus'),
				'type'=>Controls_Manager::COLOR,
				'selectors'=>[
					'{{WRAPPER}} .tp-search-bar .tp-search-area:hover .tp-serpost-price'=>'color:{{VALUE}}'
				],
				'condition' => [
					'PriceOn'=>'yes',
				],
			]
		);
		$this->add_control('RaSdTxCrH',
			[
				'label'=>__('Woo Short Description Color','theplus'),
				'type'=>Controls_Manager::COLOR,
				'selectors'=>[
					'{{WRAPPER}} .tp-search-bar .tp-search-area:hover .tp-serpost-shortDesc'=>'color:{{VALUE}}'
				],
				'condition' => [
					'ShortDescOn'=>'yes',
				],
			]
		);
		$this->add_group_control(Group_Control_Background::get_type(),
			[
				'name'=>'RatitleBgCrH',
				'types'=>['classic','gradient'],
				'selector'=>'{{WRAPPER}} .tp-search-bar .tp-search-area .tp-search-slider .tp-ser-item:hover',
			]
		);
		$this->add_group_control(Group_Control_Border::get_type(),
			[
				'name'=>'RaInBoxH',
				'label'=>esc_html__('Border','theplus'),
				'selector'=>'{{WRAPPER}} .tp-search-bar .tp-search-area .tp-search-slider .tp-ser-item:hover',	
			]
		);
		$this->add_responsive_control('RaInBoxBrsH',
			[
				'label'=>__('Border Radius','theplus'),
				'type'=>Controls_Manager::DIMENSIONS,
				'size_units'=>['px','%',],
				'selectors'=>[
					'{{WRAPPER}} .tp-search-bar .tp-search-area .tp-search-slider .tp-ser-item:hover'=>'border-radius:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],	
			]
		);
		$this->add_group_control(Group_Control_Box_Shadow::get_type(),
			[
				'name'=>'RaInBoxBsdH',
				'selector'=>'{{WRAPPER}} .tp-search-bar .tp-search-area .tp-search-slider .tp-ser-item:hover',
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->add_control('RaBodyImage_had',
			[
				'label'=>__('Image Options','theplus'),
				'type'=>Controls_Manager::HEADING,
				'separator'=>'before',
				'condition'=>[
					'ThubOn'=>'yes',
				],
			]
		);	
		$this->add_responsive_control('RaInBimgPad',
			[
				'label'=>__('Image Padding','theplus'),
				'type'=>Controls_Manager::DIMENSIONS,
				'size_units'=>['px','%',],
				'selectors'=>[
					'{{WRAPPER}} .tp-search-bar .tp-search-area .tp-search-list .tp-item-image'=>'padding:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'=>[
					'ThubOn'=>'yes',
				],
			]
		);
		$this->add_control('imagewidth',
			[
				'label'=>__('Image Box Width','theplus'),
				'type'=>Controls_Manager::SLIDER,
				'size_units'=>['px','%'],
				'range'=>[
					'px'=>[
						'min'=>0,
						'max'=>1000,
						'step'=>5,
					],
					'%'=>[
						'min'=>0,
						'max'=>100,
					],
				],
				'default'=>[
					'unit'=>'px',
					'size'=>'',
				],
				'selectors'=>[
					'{{WRAPPER}} .tp-search-bar .tp-search-area .tp-search-list .tp-serpost-thumb'=>'width:{{SIZE}}{{UNIT}};',
				],
				'condition'=>[
					'ThubOn'=>'yes',
				],
			]
		);
		$this->add_group_control(Group_Control_Border::get_type(),
			[
				'name'=>'ImageB',
				'label'=>esc_html__('Border','theplus'),
				'selector'=>'{{WRAPPER}} .tp-search-bar .tp-search-area .tp-search-list .tp-item-image',	
				'condition'=>[
					'ThubOn'=>'yes',
				],
			]
		);
		$this->add_responsive_control('ImageBRs',
			[
				'label'=>__('Border Radius','theplus'),
				'type'=>Controls_Manager::DIMENSIONS,
				'size_units'=>['px','%',],
				'selectors'=>[
					'{{WRAPPER}} .tp-search-bar .tp-search-area .tp-search-list .tp-item-image'=>'border-radius:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'=>[
					'ThubOn'=>'yes',
				],	
			]
		);
		$this->add_group_control(Group_Control_Box_Shadow::get_type(),
			[
				'name'=>'ImageBSd',
				'selector'=>'{{WRAPPER}} .tp-search-bar .tp-search-area .tp-search-list .tp-item-image',
				'condition'=>[
					'ThubOn'=>'yes',
				],
			]
		);

		$this->add_control('RaBodyBox_had',
			[
				'label'=>__('Result Box Options','theplus'),
				'type'=>Controls_Manager::HEADING,
				'separator'=>'before',
			]
		);
		$this->add_responsive_control('RaInBPadding',
			[
				'label'=>__('Padding','theplus'),
				'type'=>Controls_Manager::DIMENSIONS,
				'size_units'=>['px','%',],
				'selectors'=>[
					'{{WRAPPER}} .tp-search-bar .tp-search-area .tp-search-list'=>'padding:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control('RaInBMargin',
			[
				'label'=>__('Margin','theplus'),
				'type'=>Controls_Manager::DIMENSIONS,
				'size_units'=>['px','%',],
				'selectors'=>[
					'{{WRAPPER}} .tp-search-bar .tp-search-area .tp-search-list'=>'margin:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs('RaInBBG_tabs');
		$this->start_controls_tab('RaInBBG_Normal',
			[
				'label' => esc_html__('Normal','theplus')
			]
		);
		$this->add_group_control(Group_Control_Background::get_type(),
			[
				'name'=>'RaInBBGN',
				'types'=>['classic','gradient'],
				'selector'=>'{{WRAPPER}} .tp-search-bar .tp-search-area .tp-search-list',
			]
		);
		$this->add_group_control(Group_Control_Border::get_type(),
			[
				'name'=>'RaInBBBGN',
				'label'=>esc_html__('Border','theplus'),
				'selector'=>'{{WRAPPER}} .tp-search-bar .tp-search-area .tp-search-list',	
			]
		);
		$this->add_responsive_control('RaInBrsBGN',
			[
				'label'=>__('Border Radius','theplus'),
				'type'=>Controls_Manager::DIMENSIONS,
				'size_units'=>['px','%',],
				'selectors'=>[
					'{{WRAPPER}} .tp-search-bar .tp-search-area .tp-search-list'=>'border-radius:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],	
			]
		);
		$this->add_group_control(Group_Control_Box_Shadow::get_type(),
			[
				'name'=>'RaInBsdBGN',
				'selector'=>'{{WRAPPER}} .tp-search-bar .tp-search-area .tp-search-list',	
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab('RaInBBG_Hover',
			[
				'label' => esc_html__('Hover','theplus')
			]
		);
		$this->add_group_control(Group_Control_Background::get_type(),
			[
				'name'=>'RaInBBGH',
				'types'=>['classic','gradient'],
				'selector'=>'{{WRAPPER}} .tp-search-bar .tp-search-area:hover .tp-search-list',
			]
		);
		$this->add_group_control(Group_Control_Border::get_type(),
			[
				'name'=>'RaInBBBGH',
				'label'=>esc_html__('Border','theplus'),
				'selector'=>'{{WRAPPER}} .tp-search-bar .tp-search-area:hover .tp-search-list',	
			]
		);
		$this->add_responsive_control('RaInBrsBGH',
			[
				'label'=>__('Border Radius','theplus'),
				'type'=>Controls_Manager::DIMENSIONS,
				'size_units'=>['px','%',],
				'selectors'=>[
					'{{WRAPPER}} .tp-search-bar .tp-search-area:hover .tp-search-list'=>'border-radius:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],	
			]
		);
		$this->add_group_control(Group_Control_Box_Shadow::get_type(),
			[
				'name'=>'RaInBsdBGH',
				'selector'=>'{{WRAPPER}} .tp-search-bar .tp-search-area:hover .tp-search-list',	
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		/*Results Content End*/

		/*Normal Pagination*/
		$this->start_controls_section('Pagi_styling',
			[
				'label' => esc_html__('Pagination','theplus'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'ajaxsearch'=>'yes',
					'post_extra_option'=>'pagination',
				],	
			]
		);
		$this->add_group_control(Group_Control_Typography::get_type(),
			[
				'name'=>'pagitypo',
				'label'=>__('Typography','theplus'),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector'=>'{{WRAPPER}} .tp-search-bar .tp-search-area .tp-pagelink',
			]
		);
		$this->add_responsive_control('pagitypoSvgIconSize',
			[
				'type'=>Controls_Manager::SLIDER,
				'label'=>esc_html__('Svg Icon Size', 'theplus'),
				'size_units'=>[ 'px' ],
				'range'=>[
					'px'=>[
						'min'=>1,
						'max'=>150,
						'step'=>1,
					],
				],
				'default'=>[
					'unit'=>'px',
					'size'=>'20',
				],
				'render_type'=>'ui',
				'selectors'=>[
					'{{WRAPPER}} .tp-search-bar .tp-search-area .tp-pagelink svg' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}}',
				],
			]
		);
		$this->start_controls_tabs('Pagi_tabs');
		$this->start_controls_tab('Pagi_Normal',
			[
				'label' => esc_html__('Normal','theplus')
			]
		);
		$this->add_control('pagiColor',
			[
				'label'=>__('Text Color','theplus'),
				'type'=>Controls_Manager::COLOR,
				'selectors'=>[
					'{{WRAPPER}} .tp-search-bar .tp-search-area .tp-pagelink'=>'color:{{VALUE}}',
					'{{WRAPPER}} .tp-search-bar .tp-search-area .tp-pagelink svg'=>'fill:{{VALUE}}'
				],
			]
		);
		$this->add_group_control( Group_Control_Background::get_type(),
			[
				'name'=>'pagiBgtype',
				'types'=>['classic','gradient'],
				'selector' =>'{{WRAPPER}} .tp-search-bar .tp-search-area .tp-pagelink',
			]
		);
		$this->add_group_control( Group_Control_Border::get_type(),
			[
				'name'=>'pagiBorder',
				'label'=>esc_html__('Border','theplus'),
				'selector'=>'{{WRAPPER}} .tp-search-bar .tp-search-area .tp-pagelink',
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab( 'Pagi_Hover',
			[
				'label' => esc_html__('Hover','theplus')
			]
		);
		$this->add_control('pagiHColor',
			[
				'label'=>__('Text Color','theplus'),
				'type'=>Controls_Manager::COLOR,
				'selectors'=>[
					'{{WRAPPER}} .tp-search-bar .tp-search-area .tp-pagelink:hover'=>'color:{{VALUE}}',
					'{{WRAPPER}} .tp-search-bar .tp-search-area .tp-pagelink:hover svg'=>'fill:{{VALUE}}'
				],
			]
		);
		$this->add_group_control( Group_Control_Background::get_type(),
			[
				'name'=>'pagihoverBg',
				'types'=>['classic','gradient'],
				'selector'=>'{{WRAPPER}} .tp-search-bar .tp-search-area .tp-pagelink:hover',
			]
		);
		$this->add_group_control( Group_Control_Border::get_type(),
			[
				'name'=>'pagihoverbor',
				'label'=>esc_html__('Border','theplus'),
				'selector'=>'{{WRAPPER}} .tp-search-bar .tp-search-area .tp-pagelink:hover',
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab( 'Pagi_Active',
			[
				'label' => esc_html__('Active','theplus')
			]
		);
		$this->add_control('pagiActColor',
			[
				'label'=>__('Text Color','theplus'),
				'type'=>Controls_Manager::COLOR,
				'selectors'=>[
					'{{WRAPPER}} .tp-search-bar .tp-search-area .tp-pagelink.active'=>'color:{{VALUE}}',
					'{{WRAPPER}} .tp-search-bar .tp-search-area .tp-pagelink.active svg'=>'fill:{{VALUE}}'
				],
			]
		);
		$this->add_group_control( Group_Control_Background::get_type(),
			[
				'name'=>'pagiActBg',
				'types'=> ['classic','gradient'],
				'selector'=>'{{WRAPPER}} .tp-search-bar .tp-search-area .tp-pagelink.active',
			]
		);
		$this->add_group_control( Group_Control_Border::get_type(),
			[
				'name'=>'pagiActbor',
				'label'=>esc_html__('Border','theplus'),
				'selector'=>'{{WRAPPER}} .tp-search-bar .tp-search-area .tp-pagelink.active',
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_control('Nextbtnhad',
			[
				'label'=>__('Next Button','theplus'),
				'type'=>Controls_Manager::HEADING,
				'separator'=>'before',
			]
		);
		$this->start_controls_tabs('NextM_Normal');
		$this->start_controls_tab('Next_Normal',
			['label' => esc_html__('Normal','theplus')]
		);
		$this->add_control('nxtbtntxtNcr',
			[
				'label'=>__('Next button Text Color','theplus'),
				'type'=>Controls_Manager::COLOR,
				'selectors'=>[
					'{{WRAPPER}} .tp-search-area .tp-pagelink.next'=>'color:{{VALUE}}',
					'{{WRAPPER}} .tp-search-area .tp-pagelink.next svg'=>'fill:{{VALUE}}'
				],
			]
		);
		$this->add_group_control( Group_Control_Background::get_type(),
			[
				'name'      => 'nxtbtntxtNBg',
				'types'     => ['classic','gradient'],
				'selector'  => '{{WRAPPER}} .tp-search-area .tp-pagelink.next',
			]
		);
		$this->add_group_control( Group_Control_Border::get_type(),
			[
				'name'=>'nextbtnN',
				'label'=>esc_html__('Border','theplus'),
				'selector'=>'{{WRAPPER}} .tp-search-area .tp-pagelink.next',
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab('Next_hover',
			['label' => esc_html__('Hover','theplus')]
		);
		$this->add_control('nxtbtntxtHcr',
			[
				'label'=>__('Next button Text Color','theplus'),
				'type'=>Controls_Manager::COLOR,
				'selectors'=>[
					'{{WRAPPER}} .tp-search-area .tp-pagelink.next:hover'=>'color:{{VALUE}}',
					'{{WRAPPER}} .tp-search-area .tp-pagelink.next:hover svg'=>'fill:{{VALUE}}'
				],
			]
		);
		$this->add_group_control(Group_Control_Background::get_type(),
			[
				'name' =>'nxtbtntxtHBg',
				'types'  =>['classic','gradient'],
				'selector' =>'{{WRAPPER}} .tp-search-area .tp-pagelink.next:hover',
			]
		);
		$this->add_group_control(Group_Control_Border::get_type(),
			[
				'name'=>'nextbtnH',
				'label'=>esc_html__('Border','theplus'),
				'selector'=>'{{WRAPPER}} .tp-search-area .tp-pagelink.next:hover',
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_control('prevbtnhad',
			[
				'label'=>__('Prev Button','theplus'),
				'type'=>Controls_Manager::HEADING,
				'separator'=>'before',
			]
		);
		$this->start_controls_tabs('prevM_Normal');
		$this->start_controls_tab('prev_Normal',
			['label' => esc_html__('Normal','theplus')]
		);
		$this->add_control('prebtntxtNcr',
			[
				'label'=>__('Prev button Text Color','theplus'),
				'type'=>Controls_Manager::COLOR,
				'selectors'=>[
					'{{WRAPPER}} .tp-search-area .tp-pagelink.prev'=>'color:{{VALUE}}',
					'{{WRAPPER}} .tp-search-area .tp-pagelink.prev svg'=>'fill:{{VALUE}}'
				],
			]
		);
		$this->add_group_control( Group_Control_Background::get_type(),
			[
				'name'      => 'prebtntxtNBg',
				'types'     => ['classic','gradient'],
				'selector'  => '{{WRAPPER}} .tp-search-area .tp-pagelink.prev',
			]
		);
		$this->add_group_control( Group_Control_Border::get_type(),
			[
				'name'=>'PrevbtnN',
				'label'=>esc_html__('Border','theplus'),
				'selector'=>'{{WRAPPER}} .tp-search-area .tp-pagelink.prev',
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab('Prev_hover',
			[
				'label' => esc_html__('Hover','theplus')
			]
		);
		$this->add_control('prebtntxtHcr',
			[
				'label'=>__('Prev button Text Color','theplus'),
				'type'=>Controls_Manager::COLOR,
				'selectors'=>[
					'{{WRAPPER}} .tp-search-area .tp-pagelink.prev:hover'=>'color:{{VALUE}}',
					'{{WRAPPER}} .tp-search-area .tp-pagelink.prev:hover svg'=>'fill:{{VALUE}}'
				],
			]
		);
		$this->add_group_control(Group_Control_Background::get_type(),
			[
				'name' => 'prebtntxtHBg',
				'types' => ['classic','gradient'],
				'selector' => '{{WRAPPER}} .tp-search-area .tp-pagelink.prev:hover',
			]
		);
		$this->add_group_control(Group_Control_Border::get_type(),
			[
				'name'=>'PrevbtnH',
				'label'=>esc_html__('Border','theplus'),
				'selector'=>'{{WRAPPER}} .tp-search-area .tp-pagelink.prev:hover',
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		/*Normal Pagination*/

		/*loadmore start*/
		$this->start_controls_section('adspages_section',
			[
				'label'=>esc_html__('Load More/Lazy Load','theplus'),
				'tab'=>Controls_Manager::TAB_STYLE,
				'condition'=>[
					'ajaxsearch'=>'yes',
					'post_extra_option'=>['load_more','lazy_load'],
				],
			]
		);
		$this->add_responsive_control('loadmore_Padding',
			[
				'label'=>esc_html__('Padding','theplus'),
				'type'=>Controls_Manager::DIMENSIONS,
				'size_units'=>['px','%'],
				'selectors'=>[
					'{{WRAPPER}} .tp-search-bar .ajax_load_more .post-load-more'=>'padding:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'=>[
					'post_extra_option'=>'load_more',
				],
			]
		);
		$this->add_responsive_control('loadmore_Margin',
			[
				'label'=>esc_html__('Margin','theplus'),
				'type'=>Controls_Manager::DIMENSIONS,
				'size_units'=>['px','%'],
				'separator'=>'after',
				'selectors'=>[
					'{{WRAPPER}} .tp-search-bar .ajax_load_more .post-load-more'=>'margin:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'=>[
					'post_extra_option'=>'load_more',
				],
			]
		);
		$this->add_group_control(Group_Control_Typography::get_type(),
			[
				'name'=>'load_more_typography',
				'label'=> esc_html__('Load More Typography','theplus'),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector'=>'{{WRAPPER}} .ajax_load_more .post-load-more',
				'condition'=>[
					'post_extra_option'=>'load_more',
				],
			]
		);
		$this->add_group_control(Group_Control_Typography::get_type(),
			[
				'name'=>'loaded_posts_typo',
				'label'=>esc_html__('Loaded All Posts Typography','theplus'),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector'=>'{{WRAPPER}} .plus-all-posts-loaded',
				'condition'=>[
					'post_extra_option'=>['load_more','lazy_load'],
				],
			]
		);
		$this->start_controls_tabs('tabs_load_more_style');
		$this->start_controls_tab('tab_load_more_normal',
			[
				'label'=>esc_html__('Normal','theplus'),				
				'condition'=>[
					'post_extra_option'=>'load_more',
				],
			]
		);
		$this->add_control('load_more_color',
			[
				'label'=>esc_html__('Text Color','theplus'),
				'type'=>Controls_Manager::COLOR,
				'default'=>'',
				'selectors'=>[
					'{{WRAPPER}} .ajax_load_more .post-load-more,{{WRAPPER}} .ajax_load_more .tp-morefilter:hover'=>'color: {{VALUE}}',
				],
				'condition'=>[
					'post_extra_option'=>'load_more',
				],
			]
		);
		$this->add_control('loaded_posts_color',
			[
				'label'=>esc_html__('Loaded Posts Text Color','theplus'),
				'type'=>Controls_Manager::COLOR,
				'default'=>'',
				'selectors'=>[
					'{{WRAPPER}} .plus-all-posts-loaded'=>'color: {{VALUE}}',
				],
				'condition' => [
					'post_extra_option'=>['load_more','lazy_load'],
				],
			]
		);
		$this->add_control('loading_spin_heading',
			[
				'label'=>esc_html__('Loading Spinner ','theplus'),
				'type'=>Controls_Manager::HEADING,
				'condition'=>[
					'post_extra_option'=>'lazy_load',
				],
			]
		);
		$this->add_control('loading_spin_color',
			[
				'label'=>esc_html__('Color','theplus'),
				'type'=>Controls_Manager::COLOR,
				'default'=>'',
				'selectors'=>[
					'{{WRAPPER}} .ajax_lazy_load .post-lazy-load .tp-spin-ring div'=>'border-color: {{VALUE}} transparent transparent transparent',
				],	
				'condition'=>[
					'post_extra_option'=>'lazy_load',
				],
			]
		);
		$this->add_responsive_control('loading_spin_size',
			[
				'type'=>Controls_Manager::SLIDER,
				'label'=>esc_html__('Size', 'theplus'),
				'size_units'=>[ 'px' ],
				'range'=>[
					'px'=>[
						'min'=>1,
						'max'=>200,
						'step'=>1,
					],
				],
				'render_type'=>'ui',
				'selectors'=>[
					'{{WRAPPER}} .ajax_lazy_load .post-lazy-load .tp-spin-ring div' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}}',
				],
				'condition'=>[
					'post_extra_option'=>'lazy_load',
				],
			]
		);
		$this->add_responsive_control('loading_spin_border_size',
			[
				'type'=>Controls_Manager::SLIDER,
				'label'=>esc_html__('Border Size','theplus'),
				'size_units'=>['px'],
				'range'=>[
					'px'=>[
						'min'=>1,
						'max'=>20,
						'step'=>1,
					],
				],
				'render_type'=>'ui',
				'selectors'=>[
					'{{WRAPPER}} .ajax_lazy_load .post-lazy-load .tp-spin-ring div'=>'border-width: {{SIZE}}{{UNIT}};',
				],
				'condition'=>[
					'post_extra_option'=>'lazy_load',
				],
			]
		);
		$this->add_group_control(Group_Control_Background::get_type(),
			[
				'name'=>'load_more_background',
				'types'=>['classic','gradient'],
				'selector'=>'{{WRAPPER}} .ajax_load_more .post-load-more,{{WRAPPER}} .ajax_load_more .tp-morefilter',
				'condition'=>[
					'post_extra_option'=>'load_more',
				],
			]
		);
		$this->add_group_control(Group_Control_Border::get_type(),
			[
				'name'=>'load_more_border_N',
				'label'=>esc_html__( 'Border', 'theplus' ),
				'selector'=>'{{WRAPPER}} .tp-search-bar .ajax_load_more .post-load-more',
			]
		);
		$this->add_responsive_control('load_more_border_radius',
			[
				'label'=>esc_html__('Border Radius','theplus'),
				'type'=>Controls_Manager::DIMENSIONS,
				'size_units'=>[ 'px', '%' ],
				'selectors'=>[
					'{{WRAPPER}} .tp-search-bar .ajax_load_more .post-load-more'=>'border-radius:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'=>[
					'post_extra_option'=>'load_more',
				],
			]
		);		
		$this->add_group_control(Group_Control_Box_Shadow::get_type(),
			[
				'name'=>'load_more_shadow',
				'selector'=>'{{WRAPPER}} .ajax_load_more .post-load-more,{{WRAPPER}} .ajax_load_more .tp-morefilter',
				'condition'=>[
					'post_extra_option'=>'load_more',
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab('tab_load_more_hover',
			[
				'label'=>esc_html__('Hover','theplus'),
				'condition'=>[
					'post_extra_option'=>'load_more',
				],
			]
		);
		$this->add_control('load_more_color_hover',
			[
				'label'=>esc_html__('Text Hover Color','theplus'),
				'type'=>Controls_Manager::COLOR,
				'default'=>'',
				'selectors'=>[
					'{{WRAPPER}} .ajax_load_more .post-load-more:hover,{{WRAPPER}} .ajax_load_more .tp-morefilter:hover'=>'color: {{VALUE}}',
				],
				'condition'=>[
					'post_extra_option'=>'load_more',
				],
			]
		);
		$this->add_group_control(Group_Control_Background::get_type(),
			[
				'name'=>'load_more_hover_background',
				'types'=>[ 'classic', 'gradient' ],
				'selector'=>'{{WRAPPER}} .ajax_load_more .post-load-more:hover,{{WRAPPER}} .ajax_load_more .tp-morefilter:hover',
				'condition'=>[
					'ajaxsearch'=>'yes',
					'post_extra_option'=>'load_more',
				],
			]
		);
		$this->add_group_control(Group_Control_Border::get_type(),
			[
				'name'=>'load_more_border_H',
				'label'=>esc_html__( 'Border', 'theplus' ),
				'selector'=>'{{WRAPPER}} .tp-search-bar .ajax_load_more .post-load-more:hover',
			]
		);
		$this->add_responsive_control('load_more_border_hover_radius',
			[
				'label'=>esc_html__('Border Radius','theplus'),
				'type'=>Controls_Manager::DIMENSIONS,
				'size_units'=>['px','%'],
				'selectors'=>[
					'{{WRAPPER}} .tp-search-bar .ajax_load_more .post-load-more:hover'=>'border-radius:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'post_extra_option'=>'load_more',
					'load_more_border'=>'yes',
				],
			]
		);
		$this->add_group_control(Group_Control_Box_Shadow::get_type(),
			[
				'name'=>'load_more_hover_shadow',
				'selector'=>'{{WRAPPER}} .ajax_load_more .post-load-more:hover,{{WRAPPER}} .ajax_load_more .tp-morefilter:hover',
				'condition'=>[
					'post_extra_option'=>'load_more',
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();	

		/*Overlay Option start*/
		$this->start_controls_section('Overlay_section',
			[
				'label'=>esc_html__('Overlay Option','theplus'),
				'tab'=>Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_control('OverlayOn',
			[
				'label'=>esc_html__('Overlay Enable','theplus'),
				'type'=>Controls_Manager::SWITCHER,
				'label_on'=>esc_html__('Show','theplus'),
				'label_off'=>esc_html__('Hide','theplus'),
				'default'=>'',
			]
		);
		$this->add_group_control(Group_Control_Background::get_type(),
			[
				'name'=>'OverlayBg',
				'label'=>__('Background','theplus'),
				'types'=>['classic','gradient'],
				'selector'=>'{{WRAPPER}} .tp-search-bar .tp-rental-overlay',
				'condition' => [
					'OverlayOn'=>'yes',
				],
			]
		);
		$this->end_controls_section();
		/*Overlay Option End*/
		/*Keyword Suggestions Area start*/
		$this->start_controls_section('keywordsuggestionsAreastyle',
			[
				'label'=>esc_html__('Keyword Suggestions Area','theplus'),
				'tab'=>Controls_Manager::TAB_STYLE,
				'condition' => [
					'relatedSBtn' => 'yes',
				],
			]
		);
		$this->add_control('RelatedSl_label',
			[
				'label' => esc_html__('Related Text label', 'theplus'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'after',
			]
		);
		$this->add_group_control(Group_Control_Typography::get_type(),
			[
				'name'=>'BelowSearchlabel',
				'label'=> esc_html__('Typography','theplus'),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector'=>'{{WRAPPER}} .tp-search-bar .tp-rsearch-title',
			]
		);
		$this->add_responsive_control('RelatedSl_Margin',
			[
				'label'=>esc_html__('Margin','theplus'),
				'type'=>Controls_Manager::DIMENSIONS,
				'size_units'=>['px','%'],
				'selectors'=>[
					'{{WRAPPER}} .tp-search-bar .tp-rsearch-title'=>'margin:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control('RelatedSl_Padding',
			[
				'label'=>esc_html__('Padding','theplus'),
				'type'=>Controls_Manager::DIMENSIONS,
				'size_units'=>['px','%'],
				'selectors'=>[
					'{{WRAPPER}} .tp-search-bar .tp-rsearch-title'=>'padding:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->start_controls_tabs('RelatedSl_tabs');
		$this->start_controls_tab('RelatedSl_normal',
			[
				'label'=>esc_html__('Normal','theplus'),
			]
		);
		$this->add_control('RelatedSl_N_cr',
			[
				'label'=>esc_html__('Text Color','theplus'),
				'type'=>Controls_Manager::COLOR,
				'default'=>'',
				'selectors'=>[
					'{{WRAPPER}} .tp-search-bar .tp-rsearch-title'=>'color: {{VALUE}}',
				],
			]
		);
		$this->add_group_control(Group_Control_Background::get_type(),
			[
				'name'=>'RelatedSl_N_bcr',
				'types'=>['classic','gradient'],
				'selector'=>'{{WRAPPER}} .tp-search-bar .tp-related-search-area .tp-rsearch-title',
			]
		);
		$this->add_group_control(Group_Control_Border::get_type(),
			[
				'name'=>'RelatedSl_N_b',
				'label'=>esc_html__( 'Border', 'theplus' ),
				'selector'=>'{{WRAPPER}} .tp-search-bar .tp-related-search-area .tp-rsearch-title',
			]
		);
		$this->add_responsive_control('RelatedSl_n_brs',
			[
				'label'=>esc_html__('Border Radius','theplus'),
				'type'=>Controls_Manager::DIMENSIONS,
				'size_units'=>['px','%'],
				'selectors'=>[
					'{{WRAPPER}} .tp-search-bar .tp-related-search-area .tp-rsearch-title'=>'border-radius:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(Group_Control_Box_Shadow::get_type(),
			[
				'name'=>'RelatedSl_N_bsd',
				'selector'=>'{{WRAPPER}} .tp-search-bar .tp-related-search-area .tp-rsearch-title',
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab('RelatedSl_hover',
			[
				'label'=>esc_html__('Hover','theplus'),
			]
		);
		$this->add_control('RelatedSl_H_cr',
			[
				'label'=>esc_html__('Text Color','theplus'),
				'type'=>Controls_Manager::COLOR,
				'default'=>'',
				'selectors'=>[
					'{{WRAPPER}} .tp-search-bar .tp-related-search-area:hover .tp-rsearch-title'=>'color: {{VALUE}}',
				],
			]
		);
		$this->add_group_control(Group_Control_Background::get_type(),
			[
				'name'=>'RelatedSl_H_bcr',
				'types'=>['classic','gradient'],
				'selector'=>'{{WRAPPER}} .tp-search-bar .tp-related-search-area:hover .tp-rsearch-title',
			]
		);
		$this->add_group_control(Group_Control_Border::get_type(),
			[
				'name'=>'RelatedSl_H_b',
				'label'=>esc_html__( 'Border', 'theplus' ),
				'selector'=>'{{WRAPPER}} .tp-search-bar .tp-related-search-area:hover .tp-rsearch-title',
			]
		);
		$this->add_responsive_control('RelatedSl_H_brs',
			[
				'label'=>esc_html__('Border Radius','theplus'),
				'type'=>Controls_Manager::DIMENSIONS,
				'size_units'=>['px','%'],
				'selectors'=>[
					'{{WRAPPER}} .tp-search-bar .tp-related-search-area:hover .tp-rsearch-title'=>'border-radius:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(Group_Control_Box_Shadow::get_type(),
			[
				'name'=>'RelatedSl_H_bsd',
				'selector'=>'{{WRAPPER}} .tp-search-bar .tp-related-search-area:hover .tp-rsearch-title',
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_control('RelatedSl_tag_label',
			[
				'label' => esc_html__('Related Tag Style', 'theplus'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_group_control(Group_Control_Typography::get_type(),
			[
				'name'=>'BelowSearchtag',
				'label'=> esc_html__('Typography','theplus'),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector'=>'{{WRAPPER}} .tp-search-bar .tp-rsearch-tag .tp-rsearch-tagname',
			]
		);
		$this->add_responsive_control('RelatedSl_tag_Margin',
			[
				'label'=>esc_html__('Margin','theplus'),
				'type'=>Controls_Manager::DIMENSIONS,
				'size_units'=>['px','%'],
				'selectors'=>[
					'{{WRAPPER}} .tp-search-bar .tp-rsearch-tag .tp-rsearch-tagname'=>'margin:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control('RelatedSl_tag_Padding',
			[
				'label'=>esc_html__('Padding','theplus'),
				'type'=>Controls_Manager::DIMENSIONS,
				'size_units'=>['px','%'],
				'selectors'=>[
					'{{WRAPPER}} .tp-search-bar .tp-rsearch-tag .tp-rsearch-tagname'=>'padding:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->start_controls_tabs('RelatedSl_tag_tabs');
		$this->start_controls_tab('RelatedSl_tag_normal',
			[
				'label'=>esc_html__('Normal','theplus'),
			]
		);
		$this->add_control('RelatedSl_Ntag_cr',
			[
				'label'=>esc_html__('Text Color','theplus'),
				'type'=>Controls_Manager::COLOR,
				'default'=>'',
				'selectors'=>[
					'{{WRAPPER}} .tp-search-bar .tp-rsearch-tag .tp-rsearch-tagname'=>'color: {{VALUE}}',
				],
			]
		);
		$this->add_group_control(Group_Control_Background::get_type(),
			[
				'name'=>'RelatedSl_Ntag_bcr',
				'types'=>['classic','gradient'],
				'selector'=>'{{WRAPPER}} .tp-search-bar .tp-rsearch-tag .tp-rsearch-tagname',
			]
		);
		$this->add_group_control(Group_Control_Border::get_type(),
			[
				'name'=>'RelatedSl_Ntag_b',
				'label'=>esc_html__( 'Border', 'theplus' ),
				'selector'=>'{{WRAPPER}} .tp-search-bar .tp-rsearch-tag .tp-rsearch-tagname',
			]
		);
		$this->add_responsive_control('RelatedSl_Ntag_brs',
			[
				'label'=>esc_html__('Border Radius','theplus'),
				'type'=>Controls_Manager::DIMENSIONS,
				'size_units'=>['px','%'],
				'selectors'=>[
					'{{WRAPPER}} .tp-search-bar .tp-rsearch-tag .tp-rsearch-tagname'=>'border-radius:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(Group_Control_Box_Shadow::get_type(),
			[
				'name'=>'RelatedSl_Ntag_bsd',
				'selector'=>'{{WRAPPER}} .tp-search-bar .tp-rsearch-tag .tp-rsearch-tagname',
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab('RelatedSl_tag_hover',
			[
				'label'=>esc_html__('Hover','theplus'),
			]
		);
		$this->add_control('RelatedSl_Htag_cr',
			[
				'label'=>esc_html__('Text Color','theplus'),
				'type'=>Controls_Manager::COLOR,
				'default'=>'',
				'selectors'=>[
					'{{WRAPPER}} .tp-search-bar .tp-rsearch-tag .tp-rsearch-tagname:hover'=>'color: {{VALUE}}',
				],
			]
		);
		$this->add_group_control(Group_Control_Background::get_type(),
			[
				'name'=>'RelatedSl_Htag_bcr',
				'types'=>['classic','gradient'],
				'selector'=>'{{WRAPPER}} .tp-search-bar .tp-rsearch-tag .tp-rsearch-tagname:hover',
			]
		);
		$this->add_group_control(Group_Control_Border::get_type(),
			[
				'name'=>'RelatedSl_Htag_b',
				'label'=>esc_html__( 'Border', 'theplus' ),
				'selector'=>'{{WRAPPER}} .tp-search-bar .tp-rsearch-tag .tp-rsearch-tagname:hover',
			]
		);
		$this->add_responsive_control('RelatedSl_Htag_brs',
			[
				'label'=>esc_html__('Border Radius','theplus'),
				'type'=>Controls_Manager::DIMENSIONS,
				'size_units'=>['px','%'],
				'selectors'=>[
					'{{WRAPPER}} .tp-search-bar .tp-rsearch-tag .tp-rsearch-tagname:hover'=>'border-radius:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(Group_Control_Box_Shadow::get_type(),
			[
				'name'=>'RelatedSl_tagH_bsd',
				'selector'=>'{{WRAPPER}} .tp-search-bar .tp-rsearch-tag .tp-rsearch-tagname:hover',
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();
		/*Keyword Suggestions Area end*/
		/*Background Option start*/
		$this->start_controls_section('BG_section',
			[
				'label'=>esc_html__('Background Option','theplus'),
				'tab'=>Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_control('BG_align',
			[
				'label'=>esc_html__('Alignment','theplus'),
				'type'=> Controls_Manager::CHOOSE,
				'default'=>'flex-end',
				'options'=>[
					'flex-start'=>[
						'title'=>esc_html__('Top','theplus'),
						'icon'=>'eicon-text-align-left',
					],
					'center'=>[
						'title'=>esc_html__('Center','theplus'),
						'icon'=>'eicon-text-align-center',
					],
					'flex-end'=>[
						'title'=>esc_html__('Bottom','theplus'),
						'icon'=>'eicon-text-align-right',
					],
				],
				'selectors'=>[
					'{{WRAPPER}} .tp-search-bar .tp-search-form .tp-form-field'=>'align-items:{{VALUE}};',
				],
			]
		);
		$this->add_control('Bg_Padding',
			[
				'label'=>__('Padding','theplus'),
				'type'=>Controls_Manager::DIMENSIONS,
				'size_units'=>['px','%','em'],
				'selectors' => [
					'{{WRAPPER}} .tp-search-bar .tp-search-form'=>'padding:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_control('Bg_Margin',
			[
				'label'=>__('Margin','theplus'),
				'type'=>Controls_Manager::DIMENSIONS,
				'size_units'=>['px','%','em'],
				'selectors' => [
					'{{WRAPPER}} .tp-search-bar .tp-search-form'=>'margin:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->start_controls_tabs('BGControl');
		$this->start_controls_tab('BGo_Normal',
			[
				'label'=>esc_html__('Normal','theplus')
			]
		); 	
		$this->add_group_control(Group_Control_Background::get_type(),
			[
				'name'=>'formBGN',
				'label'=>__('Background','theplus'),
				'types'=>['classic','gradient'],
				'selector'=>'{{WRAPPER}} .tp-search-bar .tp-search-form',
			]
		);
		$this->add_group_control(Group_Control_Border::get_type(),
			[
				'name'=>'formBN',
				'label'=>__('Border','theplus'),
				'selector'=>'{{WRAPPER}} .tp-search-bar .tp-search-form',
			]
		);
		$this->add_responsive_control('formBBrN',
			[
				'label'=>__('Border Radius','theplus'),
				'type'=>Controls_Manager::DIMENSIONS,
				'size_units'=>['px','%','em'],
				'selectors'=>[
					'{{WRAPPER}} .tp-search-bar .tp-search-form'=>'border-radius:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(Group_Control_Box_Shadow::get_type(),
			[
				'name'=>'formNsd',
				'label'=>__('Box Shadow','theplus'),
				'selector'=>'{{WRAPPER}} .tp-search-bar .tp-search-form',
			]
		);
		$this->add_control('secbackdropshadown',
			[
				'label'=>esc_html__('Backdrop Filter','theplus'),
				'type'=>Controls_Manager::POPOVER_TOGGLE,
				'label_off'=>__('Default','theplus'),
				'label_on'=>__('Custom','theplus'),
				'return_value'=>'yes',
			]
		);
		$this->add_control('secbackdropshadown_blur',
			[
				'label'=>esc_html__('Blur','theplus'),
				'type'=>Controls_Manager::SLIDER,
				'size_units'=>['px'],
				'range'=>[
					'px'=>[
						'max'=>100,
						'min'=>1,
						'step'=>1,
					],
				],
				'default'=>[
					'unit'=>'px',
					'size'=>10,
				],
				'condition'=>[
					'secbackdropshadown'=>'yes',
				],
			]
		);
		$this->add_control('secbackdropshadown_grayscale',
			[
				'label'=>esc_html__('Grayscale','theplus'),
				'type'=>Controls_Manager::SLIDER,
				'size_units'=>['px'],
				'range'=>[
					'px'=>[
						'max'=>1,
						'min'=>0,
						'step'=>0.1,
					],
				],
				'default'=>[
					'unit'=>'px',
					'size'=>0,
				],
				'selectors'=>[
					'{{WRAPPER}} .tp-search-bar .tp-search-form'=>'-webkit-backdrop-filter:grayscale({{secbackdropshadown_grayscale.SIZE}})  blur({{secbackdropshadown_blur.SIZE}}{{secbackdropshadown_blur.UNIT}}) !important;backdrop-filter:grayscale({{secbackdropshadown_grayscale.SIZE}})  blur({{secbackdropshadown_blur.SIZE}}{{secbackdropshadown_blur.UNIT}}) !important;',
				],
				'condition'=>[
					'secbackdropshadown'=>'yes',
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab('BGo_Hover',
			[
				'label'=>esc_html__('Hover','theplus')
			]
		); 
		$this->add_group_control(Group_Control_Background::get_type(),
			[
				'name'=>'formBGH',
				'label'=>__('Background','theplus'),
				'types'=>['classic','gradient'],
				'selector'=>'{{WRAPPER}} .tp-search-bar .tp-search-form:hover',
			]
		);
		$this->add_group_control(Group_Control_Border::get_type(),
			[
				'name'=>'formBH',
				'label'=>__('Border','theplus'),
				'selector'=>'{{WRAPPER}} .tp-search-bar .tp-search-form',
			]
		);
		$this->add_responsive_control('formBBrH',
			[
				'label'=>__('Border Radius','theplus'),
				'type'=>Controls_Manager::DIMENSIONS,
				'size_units'=>['px','%','em'],
				'selectors'=>[
					'{{WRAPPER}} .tp-search-bar .tp-search-form'=>'border-radius:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(Group_Control_Box_Shadow::get_type(),
			[
				'name'=>'formHsd',
				'label'=>__('Box Shadow','theplus'),
				'selector'=>'{{WRAPPER}} .tp-search-bar .tp-search-form:hover',
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->add_control(
			'formsd_bf',
			[
				'label' => esc_html__( 'Backdrop Filter', 'theplus' ),
				'type' => Controls_Manager::POPOVER_TOGGLE,
				'label_off' => __( 'Default', 'theplus' ),
				'label_on' => __( 'Custom', 'theplus' ),
				'return_value' => 'yes',
			]
		);
		$this->add_control(
			'formsd_bf_blur',
			[
				'label' => esc_html__( 'Blur', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'max' => 100,
						'min' => 1,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 10,
				],
				'condition'    => [
					'formsd_bf' => 'yes',
				],
			]
		);
		$this->add_control(
			'formsd_bf_grayscale',
			[
				'label' => esc_html__( 'Grayscale', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'max' => 1,
						'min' => 0,
						'step' => 0.1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 0,
				],
				'selectors' => [
					'{{WRAPPER}} .tp-search-bar .tp-search-form' => '-webkit-backdrop-filter:grayscale({{formsd_bf_grayscale.SIZE}})  blur({{formsd_bf_blur.SIZE}}{{formsd_bf_blur.UNIT}}) !important;backdrop-filter:grayscale({{formsd_bf_grayscale.SIZE}})  blur({{formsd_bf_blur.SIZE}}{{formsd_bf_blur.UNIT}}) !important;',
				 ],
				'condition'    => [
					'formsd_bf' => 'yes',
				],
			]
		);
		$this->end_popover();
		$this->end_controls_section();
		/*Background Option End*/
		/*Scroll Bar Option start*/
		$this->start_controls_section('ScrollBarTab',
			[
				'label' => esc_html__('Scroll Bar', 'theplus' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'ScrollBar'=>'yes',
				],
			]
		);
		$this->start_controls_tabs('scrollC_style');
		$this->start_controls_tab('scrollC_Bar',
			[
				'label'=>esc_html__('Scrollbar','theplus'),
			]
		);
		$this->add_group_control(Group_Control_Background::get_type(),
			[
				'name'=>'ScrollBg',
				'types'=>['classic','gradient'],
				'selector'=>'{{WRAPPER}} .tp-search-bar .tp-search-scrollbar::-webkit-scrollbar',
			]
		);
		$this->add_responsive_control('ScrollWidth',
			[
				'type'=>Controls_Manager::SLIDER,
				'label'=>esc_html__('Width','theplus'),
				'size_units'=>['px'],
				'range'=>[
					'px'=>[
						'min' => 1,
						'max' => 100,
						'step' => 1,
					],
				],
				'render_type'=>'ui',
				'selectors'=>[
					'{{WRAPPER}} .tp-search-bar .tp-search-scrollbar::-webkit-scrollbar'=>'width:{{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab('scrollC_Tmb',
			[
				'label' => esc_html__('Thumb','theplus'),
			]
		);
		$this->add_group_control(Group_Control_Background::get_type(),
			[
				'name'=>'ThumbBg',
				'types'=>['classic','gradient'],
				'selector'=>'{{WRAPPER}} .tp-search-bar .tp-search-scrollbar::-webkit-scrollbar-thumb',
			]
		);
		$this->add_responsive_control('ThumbBrs',
			[
				'label'=>esc_html__('Border Radius','theplus'),
				'type'=>Controls_Manager::DIMENSIONS,
				'size_units'=>['px','%'],
				'selectors'=> [
					'{{WRAPPER}} .tp-search-bar .tp-search-scrollbar::-webkit-scrollbar-thumb'=>'border-radius:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',				
				],
			]
		);
		$this->add_group_control(Group_Control_Box_Shadow::get_type(),
			[
				'name'=>'ThumbBsw',
				'selector'=>'{{WRAPPER}} .tp-search-bar .tp-search-scrollbar::-webkit-scrollbar-thumb',
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab('scrollC_Trk',
			[
				'label'=>esc_html__('Track','theplus'),
			]
		);
		$this->add_group_control(Group_Control_Background::get_type(),
			[
				'name'=>'TrackBg',
				'types'=>['classic','gradient'],
				'selector'=>'{{WRAPPER}} .tp-search-bar .tp-search-scrollbar::-webkit-scrollbar-track',
			]
		);
		$this->add_responsive_control('TrackBRs',
			[
				'label'=>esc_html__('Border Radius','theplus'),
				'type'=>Controls_Manager::DIMENSIONS,
				'size_units'=>[ 'px', '%' ],
				'selectors'=>[
					'{{WRAPPER}} .tp-search-bar .tp-search-scrollbar::-webkit-scrollbar-track'=>'border-radius:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',				
				],
			]
		);
		$this->add_group_control(Group_Control_Box_Shadow::get_type(),
			[
				'name'=>'TrackBsw',
				'selector'=>'{{WRAPPER}} .tp-search-bar .tp-search-scrollbar::-webkit-scrollbar-track',
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		/*Scroll Bar Option End*/

		/*Error Option start*/
		$this->start_controls_section('errorsection',
			[
				'label'=>esc_html__('Error Option','theplus'),
				'tab'=>Controls_Manager::TAB_STYLE,
				'condition' => [
					'ajaxsearch'=>'yes',
				],
			]
		);
		$this->add_group_control(Group_Control_Typography::get_type(),
			[
				'name'=>'ErrorTypo',
				'label'=>__('Typography','theplus'),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector'=>'{{WRAPPER}} .tp-search-area .tp-search-error',
			]
		);
		$this->add_control('errorpadding',
			[
				'label'=>__('Padding','theplus'),
				'type'=>Controls_Manager::DIMENSIONS,
				'size_units'=>['px','%'],
				'selectors'=>[
					'{{WRAPPER}} .tp-search-area .tp-search-error.active'=>'padding:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->start_controls_tabs('errortabs');
		$this->start_controls_tab('errorNormal',
			['label' => esc_html__('Normal','theplus')]
		);	
		$this->add_control('errortxtNCr',
			[
				'label'=>__('Text color','theplus'),
				'type'=>Controls_Manager::COLOR,
				'selectors'=>[
					'{{WRAPPER}} .tp-search-area .tp-search-error'=>'color:{{VALUE}}'
				]
			]
		);
		$this->add_group_control( Group_Control_Background::get_type(),
			[
				'name'=>'errorNbg',
				'types'=>['classic','gradient'],
				'selector'=>'{{WRAPPER}} .tp-search-area .tp-search-error'
			]
		);
		$this->add_group_control(Group_Control_Border::get_type(),
			[
				'name'=>'errorNb',
				'label'=>esc_html__('Border','theplus'),
				'selector'=>'{{WRAPPER}} .tp-search-area .tp-search-error'
			]
		);
		$this->add_group_control( Group_Control_Box_Shadow::get_type(),
			[
				'name'=>'errornsd',
				'selector'=>'{{WRAPPER}} .tp-search-area .tp-search-error',			
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab('errorhover',
			['label' => esc_html__('Hover','theplus')]
		);
		$this->add_control('errortxtHCr',
			[
				'label'=>__('Text color','theplus'),
				'type'=>Controls_Manager::COLOR,
				'selectors'=>[
					'{{WRAPPER}} .tp-search-area .tp-search-error:hover'=>'color:{{VALUE}}'
				]
			]
		);
		$this->add_group_control( Group_Control_Background::get_type(),
			[
				'name'=>'errorHbg',
				'types'=>['classic','gradient'],
				'selector'=>'{{WRAPPER}} .tp-search-area .tp-search-error:hover'
			]
		);
		$this->add_group_control(Group_Control_Border::get_type(),
			[
				'name'=>'errorHb',
				'label'=>esc_html__('Border','theplus'),
				'selector'=>'{{WRAPPER}} .tp-search-area .tp-search-error:hover'
			]
		);
		$this->add_group_control( Group_Control_Box_Shadow::get_type(),
			[
				'name'=>'errorhsd',
				'selector'=>'{{WRAPPER}} .tp-search-area .tp-search-error:hover',			
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		/*Error Option End*/

		/*--On Scroll View Animation ---*/
		// $Plus_Listing_block = "Plus_Listing_block";
		// include THEPLUS_PATH. 'modules/widgets/theplus-widget-animation.php';

		$this->start_controls_section(
			'section_animation_styling',
			[
				'label'=>esc_html__('On Scroll View Animation', 'theplus'),
				'tab'=>Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_control(
			'animation_effects',
			[
				'label'=>esc_html__('In Animation Effect', 'theplus'),
				'type'=>Controls_Manager::SELECT,
				'default'=>'no-animation',
				'options'=>theplus_get_animation_options(),
			]
		);
		$this->add_control(
			'animation_delay',
			[
				'type'=>Controls_Manager::SLIDER,
				'label'=>esc_html__('Animation Delay', 'theplus'),
				'default'=>[
					'unit'=>'',
					'size'=>50,
				],
				'range'=>[
					''=>[
						'min'=>0,
						'max'=>4000,
						'step'=>15,
					],
				],
				'condition'=>[
					'animation_effects!'=>'no-animation',
				],
			]
		);

		$this->add_control('animated_column_list',
			[
				'label'=>esc_html__('List Load Animation','theplus'),
				'type'=>Controls_Manager::SELECT,
				'default'=>'',
				'options'=>[
					'stagger'=>esc_html__('Stagger Based Animation','theplus'),
				],
				'condition' => [
					'animation_effects!' => [ 'no-animation' ],
				],
			]
		);
		$this->add_control('animation_stagger',
			[
				'type'=>Controls_Manager::SLIDER,
				'label'=>esc_html__('Animation Stagger', 'theplus'),
				'default'=>[
					'unit'=>'',
					'size'=>150,
				],
				'range'=>[
					'' => [
						'min'=>0,
						'max'=>6000,
						'step'=>10,
					],
				],				
				'condition'=>[
					'animation_effects!'=>['no-animation'],
					'animated_column_list'=>'stagger',
				],
			]
		);

		$this->add_control('animation_duration_default',
			[
				'label' => esc_html__( 'Animation Duration', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'no',
				'condition' => [
					'animation_effects!' => 'no-animation',
				],
			]
		);
		$this->add_control('animate_duration',
			[
				'type'=>Controls_Manager::SLIDER,
				'label'=>esc_html__('Duration Speed', 'theplus'),
				'default'=>[
					'unit'=>'px',
					'size'=>50,
				],
				'range'=>[
					'px'=>[
						'min'=>100,
						'max'=>10000,
						'step'=>100,
					],
				],
				'condition' => [
					'animation_effects!'=>'no-animation',
					'animation_duration_default'=>'yes',
				],
			]
		);
		$this->end_controls_section();
		
	}

	protected function render() {
        $settings = $this->get_settings_for_display();
        $ElementerID = $this->get_unique_selector();
        $PageID = get_the_ID();
		$WidgetId = uniqid("uId-");
		$output='';	
		$Backclass = (\Elementor\Plugin::$instance->editor->is_edit_mode()) ? 'tp-search-backend' : '';
		$AJAXSearch = !empty($settings['ajaxsearch']) ? true : false;
		$PageStyle = isset($settings['post_extra_option']) ? $settings['post_extra_option'] : 'none';
		$LoadPage = !empty($settings['Load_page']) ? 1 : 0;
		$scrollclass = !empty($settings['ScrollBar']) ? 'tp-search-scrollbar' : '';
		$Overlay = !empty($settings['OverlayOn']) ? $settings['OverlayOn'] : '';
		$SearchType = !empty($settings['SearchType']) ? $settings['SearchType'] : 'otheroption';
		$totalresult = !empty($settings['totalresult']) ? 1 : 0;
		$RelatedSBtn = !empty($settings['relatedSBtn']) ? 1 : 0;
		$SubRefresh = !empty($settings['showBtn']) ? 'true' : 'false';

		/*--On Scroll View Animation ---*/
		$Plus_Listing_block = "Plus_Listing_block";
		$animated_columns='';
		include THEPLUS_PATH. 'modules/widgets/theplus-widget-animation-attr.php';

		$Rcolumn='';
		$RStyle = !empty($settings['ResultStyle']) ? $settings['ResultStyle'] : 'style-1';
		if ($RStyle == 'style-2') {
			$Rcolumn = $this->tp_search_column($settings['inputRADc'], $settings['inputRATc'], $settings['inputRAMc']);
		}else{
			$Rcolumn = 'tp-col-lg-12 tp-col-md-12 tp-col-sm-12 tp-col-12';
		}

		if (!empty($AJAXSearch)) {
			$PageData=$GFilter=[];

			$AcfData = array('ACFEnable' => !empty($settings['ACFFilter']) ? 1 : 0,
				'ACFkey' => !empty($settings['ACFkey']) ? $settings['ACFkey'] : '',
			);

			$ResultOnOff = array(
				'ONTitle' => !empty($settings['TitleOn']) ? 1 : 0,
				'ONContent' => !empty($settings['ContentOn']) ? 1 : 0,
				'ONThumb' => !empty($settings['ThubOn']) ? 1 : 0,
				'ONPrice' => !empty($settings['PriceOn']) ? 1 : 0,
				'ONShortDesc' => !empty($settings['ShortDescOn']) ? 1 : 0,
				'TotalResult' => $totalresult,
				'TotalResultTxt' => !empty($totalresult) ? $settings['totalresulttxt'] : '',
				'ResultlinkOn' => !empty($settings['ResultlinkOn']) ? 1 : 0,
				'Resultlinktarget' => !empty($settings['Resultlinktarget']) ? $settings['Resultlinktarget'] : '',
				'textlimit' => !empty($settings['TextLimit']) ? 1 : 0,
				'TxtTitle' => !empty($settings['TxtTitle']) ? 1 : 0,
				'texttype' => !empty($settings['TextType']) ? $settings['TextType'] : '',
				'textcount' => !empty($settings['TextCount']) ? $settings['TextCount'] : 100,
				'textdots'=> !empty($settings['TextDots']) ? $settings['TextDots'] : '',
				'Txtcont' => !empty($settings['ContentTitle']) ? 1 : 0,
				'ContType' => !empty($settings['ContentType']) ? $settings['ContentType'] : '',
				'ContCount' => !empty($settings['ContentCount']) ? $settings['ContentCount'] : 100,
				'ContDots'=> !empty($settings['ContentDots']) ? $settings['ContentDots'] : '',
				'animation_effects'=> !empty($settings['animation_effects']) ? $settings['animation_effects'] : 'no-animation',
				'errormsg' => !empty($settings['errormsg']) ? $settings['errormsg'] : 'Sorry, But Nothing Matched Your Search Terms.',
			);

			if ($PageStyle == 'pagination') {
				$PageData = array(
					'Pagestyle' => $PageStyle,
					'Pcounter' => !empty($settings['showcounter']) ? 1 : 0,
					'PClimit' => !empty($settings['counterlimit']) ? $settings['counterlimit'] : 5,
					'PNavigation' => !empty($settings['shownextprev']) ? 1 : 0,	
					'PNxttxt' => !empty($settings['nexttxt']) ? $settings['nexttxt'] : '',
					'PPrevtxt' => !empty($settings['prevtxt']) ? $settings['prevtxt'] : '',
					'PNxticon' => !empty($settings['nexticon']) ? $settings['nexticon']['value'] : '',
					'PPrevicon' => !empty($settings['previcon']) ? $settings['previcon']['value'] : '',
					'Pstyle' => !empty($settings['counterstyle']) ? $settings['counterstyle'] : 'center',
				);
			}else{
				$PageData = array(
					'Pagestyle' => $PageStyle,
					'loadbtntxt' => !empty($settings['load_more_btn_text']) ? $settings['load_more_btn_text'] : '',
					'loadingtxt' => !empty($settings['tp_loading_text']) ? $settings['tp_loading_text'] : '',
					'loadedtxt' => !empty($settings['loaded_posts_text']) ? $settings['loaded_posts_text'] : '',
					'loadnumber' => !empty($settings['load_more_post']) ? $settings['load_more_post'] : '',
					'loadpage' => $LoadPage,
					'loadPagetxt' => !empty($settings['loadPagetxt']) ? $settings['loadPagetxt'] : '',
				);
			}

			if(!empty($settings['GenericFilter'])){
				$GFilter = array(
					'GFEnable'=> 1,
					'GFSType' => $SearchType,
					'GFTitle' => !empty($settings['sintitle']) ? 1 : 0,
					'GFContent' => !empty($settings['sincontent']) ? 1 : 0,
					'GFName' => !empty($settings['sinname']) ? 1 : 0,
					'GFExcerpt' => !empty($settings['sinexcerpt']) ? 1 : 0,
					'GFCategory' => !empty($settings['sincategory']) ? 1 : 0,
					'GFTags' => !empty($settings['sinTags']) ? 1 : 0,
				);
			}else{
				$GFilter = array( 
					'GFEnable'=> 0, 
					'GFSType' => $SearchType 
				);
			}

			$AcfData = 'data-acfdata="'.htmlspecialchars(json_encode($AcfData, true), ENT_QUOTES, 'UTF-8').'"';
			$ResultOnOff = 'data-result-setting="'.htmlspecialchars(json_encode($ResultOnOff, true), ENT_QUOTES, 'UTF-8').'"';
			$PageJson = 'data-pagination-data="'.htmlspecialchars(json_encode($PageData, true), ENT_QUOTES, 'UTF-8').'"';
			$GFarray = 'data-genericfilter="'.htmlspecialchars(json_encode($GFilter, true), ENT_QUOTES, 'UTF-8').'"';
		}else{
			$PageJson=$GFarray=$ResultOnOff=$AcfData="";
		}

		$Defa_Postype=$Defa_tex=[];
		$temp=!empty($settings['searchField']) ? $settings['searchField'] : [];
		if(!empty($temp)){
			foreach($temp as $item){
				$STY = !empty($item['sourceType']) ? $item['sourceType'] : 'post'; 
				$PostType = !empty($item['postType']) ? $item['postType'] : array('post'); 

				if($STY == 'post'){
					foreach($PostType as $item1){
						$Defa_Postype[] = $item1;
					}
				}
			}
		}

		$SpecialCTP = !empty($settings['SpecialCTP']) ? 1 : 0;
		$SpecialCTPType = !empty($settings['SpecialCTPType']) ? $settings['SpecialCTPType'] : 'post';
		$DefaultSettingg = array(
			'Def_Post' => $Defa_Postype,	
			'SpecialCTP' => $SpecialCTP,
			'SpecialCTPType' => $SpecialCTPType,
		);
		$DefaultSetting = json_encode( $DefaultSettingg, true);

		$dataattr = array(
			'ajax' => !empty($settings['ajaxsearch']) ? 'yes' : 'no',
			'nonce' => wp_create_nonce("tp-searchbar"),
			'ajaxsearchCharLimit' => !empty($settings['ajaxsearchCharLimit']) ? $settings['ajaxsearchCharLimit'] : 3,
			'style' => $RStyle,
			'styleColumn' => $Rcolumn,
			'post_page' => (!empty($settings['postCount']) && !empty($settings['postCount']['size'])) ? (int)$settings['postCount']['size'] : 3,
			'Postype_Def' => $Defa_Postype,
		);
		$dataattr = htmlspecialchars(json_encode($dataattr), ENT_QUOTES, 'UTF-8');

		$output .= '<div class="tp-search-bar '.esc_attr($Backclass).' '.esc_attr($WidgetId).'" data-id="'.esc_attr($WidgetId).'" data-ajax_search=\''.$dataattr.'\' '.$GFarray.' '.$ResultOnOff.' '.$PageJson.' '.$AcfData.' data-default-data= \''.$DefaultSetting.'\' >';
			if($Overlay=='yes'){
				$output .= '<div class="tp-rental-overlay"></div>';
			}

			$output .= '<form class="tp-search-form" method="get" action="'.esc_url(site_url()).'" onSubmit="return '.esc_attr($SubRefresh).';">';
				$output .= '<div class="tp-form-field tp-row">';
					$output .= $this->tp_search_repeater($settings,$WidgetId);
					$output .= $this->tp_search_button($settings);

					if(!empty($SpecialCTP)){
						$output .= '<input type="hidden" name="post_type" value="'.esc_attr($SpecialCTPType).'" />';
					}
				$output .= '</div>';
			$output .= '</form>';

			if (!empty($AJAXSearch)) {
				$output .= '<div class="tp-search-area '.esc_attr($RStyle).'">';
					$output .= '<div class="tp-search-error"></div>';
					$output .= '<div class="tp-search-header">';
						if(!empty($totalresult)){
							$output .= '<div class="tp-search-resultcount"></div>';
						}
						if( ($PageStyle == 'pagination') || ($PageStyle == 'load_more' && !empty($LoadPage)) ){
							$output .= '<div class="tp-search-pagina"></div>';
						}
					$output .= '</div>';
					$output .= '<div class="tp-search-list ">';
						$output .= '<div class="tp-search-list-inner '.$animated_class.'" '.$animation_attr.'></div>';
					$output .= '</div>';
					if($PageStyle == 'load_more'){
						$output .= '<div class="ajax_load_more"></div>';
					}else if($PageStyle == 'lazy_load'){
						$output .= '<div class="ajax_lazy_load"></div>';
					}
				$output .= '</div>';
			}

			if(!empty($RelatedSBtn)){
				$RelatedSBtn = !empty($settings['relatedSBtnText']) ? $settings['relatedSBtnText'] : '';
				$RelatedSBtnTag = !empty($settings['relatedSBtnTag']) ? explode("|", $settings['relatedSBtnTag']) : [];

				$output .= '<div class="tp-related-search-area">';
					if(!empty($RelatedSBtn)){
						$output .= '<div class="tp-rsearch-title">'.esc_html($RelatedSBtn).'</div>';	
					}

					if(!empty($RelatedSBtnTag)){
						$output .= '<div class="tp-rsearch-tag">';
							foreach ($RelatedSBtnTag as $item) {
								$tagname = ltrim(rtrim(ucwords($item)));
								$output .= '<a href="" class="tp-rsearch-tagname">'.esc_html($tagname).'</a>';
							}
						$output .= '</div>';
					}
				$output .= '</div>';
			}
		$output .= '</div>';
		echo $output;
	}

	protected function tp_search_repeater($attr, $WidgetId){
		$output='';
		$placeholder=!empty($attr['placeholder']) ? $attr['placeholder'] :'';
		$searchLabel=!empty($attr['searchLabel']) ? $attr['searchLabel'] :'';
		$InputIcon=!empty($attr['InputIcon']) ? $attr['InputIcon']['value'] :'';	
		$searchField=!empty($attr['searchField']) ? $attr['searchField'] : [];
		$suggestOn=!empty($attr['searchsuggest']) ? 1 : 0;
		$suggestTxt=!empty($attr['suggesttxt']) ? $attr['suggesttxt'] :'';
		
		$suggest=$suggestlist="";
		if($suggestOn){
			$suggestlist = 'list="tp-input-suggestions"';
			$sugExplod = explode("|", $suggestTxt);
			$suggest .= '<datalist id="tp-input-suggestions">';
				foreach ($sugExplod as $two) {
					$suggest .= '<option value="'.ltrim(rtrim($two)).'">';
				}
			$suggest .= '</datalist>';
		}

		$output .= '<div class="tp-input-field">';
			$output .= '<div class="tp-input-label-field">';
				if(!empty($searchLabel)){
					$output .= '<label class="tp-search-label">'.esc_attr($searchLabel).'</label>';
				}
			$output .= '</div>';

			$output .= '<div class="tp-input-inner-field">';
				$output .= '<input type="text" name="s" '.$suggestlist.' class="tp-search-input" id="seatxt-'.esc_attr($WidgetId).'" placeholder="'.esc_attr($placeholder).'" autocomplete="off" />';
				$output .= $suggest;

				if(!empty($attr['InputIcon']) && !empty($attr['InputIcon']['value'])){
					ob_start();
						\Elementor\Icons_Manager::render_icon($attr['InputIcon'],['aria-hidden'=>'true']);
						$Icon = ob_get_contents();
					ob_end_clean();	
					$output .= '<span class="tp-search-input-icon">'.$Icon.'</span>';
				}

				$output .= '<div class="tp-ajx-loading"><div class="tp-spinner-loader"></div></div>';
				$output .= '<span class="tp-close-btn"><i class="fas fa-times-circle tp-close-btn-icon"></i></span>';
			$output .= '</div>';		
		$output .= '</div>';

		if(!empty($searchField)){
			foreach($searchField as $index => $item){
				$FieldValue='';
				$sourceType = !empty($item['sourceType']) ? $item['sourceType'] : '';
				$PostData = !empty($item['postType']) ? $item['postType'] : array('post');
				$taxonomyData = !empty($item['TaxonomyType']) ? $item['TaxonomyType'] : '';
				$showsubcat = !empty($item['showsubcat']) ? $item['showsubcat'] : '';

				$DataArray=[];
				if(($sourceType == 'post') && (!empty($PostData) && is_array($PostData) || is_object($PostData))){
					foreach ($PostData as $value) {					
						$count = wp_count_posts($value);
						$countNum =  !empty($count->publish) ? $count->publish : 0;
						$DataArray[$value] = ['name'=>ucfirst($value), 'count'=>$countNum];
					}

					if(!empty($DataArray)){
						$FieldValue .= $this->tp_search_drop_down($DataArray, 'post', $WidgetId, $taxonomy='', $item);
					}
				}else if($sourceType == 'taxonomy' && !empty($taxonomyData)) {
					$cat_args = ['taxonomy'=>$taxonomyData, 'parent' => 0, 'hide_empty'=>false];
					$tax_terms = get_categories($cat_args);

					foreach ($tax_terms as $index => $value) {
						$Name = !empty($value->name) ? $value->name : '';
						$Number = !empty($value->category_count) ? $value->category_count : 0;
						$TermId = !empty($value->term_id) ? $value->term_id : '';

						$DataArray[$TermId] = ['name'=>$Name,'count'=>$Number,'parent'=>''];

						if($taxonomyData == 'category' && $showsubcat == 'yes'){
							$args2 = array(
								'taxonomy'     => $taxonomyData,
								'child_of'     => 0,
								'parent'       => $TermId,
								'orderby'      => 'name',
								'show_count'   => 1,
								'pad_counts'   => 0,
								'hierarchical' => 1,
								'title_li'     => '',
								'hide_empty'   => 0
							);
							$tax_terms2 = get_categories($args2);
							foreach ($tax_terms2 as $one) {
								$Oname = !empty($one->name) ? $one->name :''; 
								$Ocount = !empty($one->count) ? $one->count :''; 
								$DataArray[$one->term_id] = ['name'=>' - '.ucwords($Oname),'count'=>$Ocount,'parent'=>$Name];
							}
						}
					}

					if(!empty($DataArray)){
						$FieldValue .= $this->tp_search_drop_down($DataArray, 'category', $WidgetId, $taxonomy=$taxonomyData, $item);
					}
				}

				if(!empty($FieldValue)){
					$output .= '<div class="tp-post-dropdown">'.$FieldValue.'</div>';
				}
			}
		}

		return $output;
	}

	protected function tp_search_drop_down($data, $name, $id, $taxo, $repeater){
		$output='';
		$showCnt = !empty($repeater['showCount']) ? 'yes' : '';
		$label = !empty($repeater['fieldLabel']) ? $repeater['fieldLabel'] : '';
		$DefText = !empty($repeater['DefText']) ? $repeater['DefText'] : '';

		if(!empty($taxo)){
			$output .= '<input name="taxonomy" type="hidden" value="'.esc_attr($taxo).'">';
		}
		if(!empty($label)){
			$output .= '<label class="tp-search-label">'.esc_attr($label).'</label>';
		}

		$DatName='';
		if($name == 'post'){
			$DatName = 'post_type';
		}else if($name == 'category'){
			$DatName = 'cat';
		}

		$output .= '<div class="tp-sbar-dropdown">';
			$output .= '<div class="tp-select">';
				$output .= '<span>'.esc_attr($DefText).'</span><i class="fas fa-chevron-down tp-dd-icon"></i>';
			$output .= '</div>';
			$output .= '<input type="hidden" name="'.esc_attr($DatName).'" id="'.esc_attr($DatName).'" >';
			$output .= '<ul class="tp-sbar-dropdown-menu">';
				$output .= '<li id="" class="tp-searchbar-li">'.esc_attr($DefText).'</li>';
	
				foreach($data as $key => $label){
					$LName = !empty($label['name']) ? $label['name'] : '';
					$Lcount = !empty($label['count']) ? $label['count'] : 0;

					$output .= '<li id="'.esc_attr($key).'" class="tp-searchbar-li" >';
						if( !empty($showCnt) ){
							$output .= esc_attr($LName) .' ('.esc_attr($Lcount).')';
						}else{
							$output .= esc_attr($LName);
						}
					$output .= '</li>';
				}
			$output .= '</ul>';
		$output .= '</div>';
		
		return $output;
	}

	protected function tp_search_button($attr){
		$output='';
		$showBtn=!empty($attr['showBtn']) ? $attr['showBtn'] :'';
		
		if(!empty($showBtn)){
			$BtnText=!empty($attr['BtnText']) ? $attr['BtnText'] :'';
			$BtnMedia=!empty($attr['BtnMedia']) ? $attr['BtnMedia'] :'';
			$BtnPos=!empty($attr['BtnPosition']) ? $attr['BtnPosition'] :'before';
			$BtnImage = (!empty($attr['BtnImage']) && !empty($attr['BtnImage']['url'])) ? $attr['BtnImage']['url'] : THEPLUS_ASSETS_URL .'images/placeholder-grid.jpg';

			$GetMedia='';
			if($BtnMedia == 'icon' && !empty($attr['BtnIcon']) && !empty($attr['BtnIcon']['value']) ){
				ob_start();
					\Elementor\Icons_Manager::render_icon($attr['BtnIcon'],['aria-hidden'=>'true']);
					$Icon = ob_get_contents();
				ob_end_clean();	
				$GetMedia = '<span class="tp-button-icon">'.$Icon.'</span>';
			}else if($BtnMedia == 'image'){
				$GetMedia = '<span class="tp-button-Image"><img src="'.esc_url($BtnImage).'" class="tp-button-ImageTag"></span>';
			}

			$output .= '<div class="tp-btn-wrap">';
				$output .= '<button class="tp-search-btn" name="submit" >';
					$output .= ($BtnPos == 'before') ? $GetMedia : '';
					if(!empty($BtnText)){
						$output .= '<span class="tp-search-btn-txt '.esc_attr($BtnPos).'">'.esc_attr($BtnText).'</span>';
					}
					$output .= ($BtnPos == 'after') ? $GetMedia : '';
				$output .= '</button>';
			$output .= '</div>';
		}

		return $output;
	}

	protected function tp_search_column($Desktop, $Tablet, $Mobile){
		$Rcolumn = 'tp-col-lg-'.esc_attr($Desktop);
		$Rcolumn .= ' tp-col-md-'.esc_attr($Tablet);
		$Rcolumn .= ' tp-col-sm-'.esc_attr($Mobile);
		$Rcolumn .= ' tp-col-'.esc_attr($Mobile);

		return $Rcolumn;
	}

    protected function content_template() {	
    }
}