<?php 
/*
Widget Name: Social Reviews
Description: Social Reviews
Author: Theplus
Author URI: http://posimyththemes.com
*/

namespace TheplusAddons\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Utils;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

use TheplusAddons\Theplus_Element_Load;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class ThePlus_Social_Reviews extends Widget_Base {
	
	public function get_name() {
		return 'tp-social-reviews';
	}

    public function get_title() {
        return esc_html__('Social Reviews', 'theplus');
    }

    public function get_icon() {
        return 'fa fa-star-o theplus_backend_icon';
    }

    public function get_categories() {
        return array('plus-essential');
    }

	public function get_keywords() {
		return [ 'social', 'fb', 'facebook', 'rating', 'rate', 'recommendation', 'gg', 'google', 'tp', 'theplus'];
	}

	public function is_reload_preview_required() {
		return true;
	}

    protected function register_controls() {

		/* Content Tab Start */
		$this->start_controls_section(
			'content_section',
			[
				'label' => esc_html__( 'Content', 'theplus' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_control(
			'RType',
			[
				'label' => esc_html__( 'Type', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'review',
				'options' => [
					'review'  => esc_html__( 'Reviews', 'theplus' ),
					'beach' => esc_html__( 'Badge', 'theplus' ),
				],
			]
		);		
		$this->add_control(
			'style',
			[
				'label' => esc_html__( 'Style', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'style-1',
				'options' => [
					'style-1'  => esc_html__( 'Style-1', 'theplus' ),
					'style-2' => esc_html__( 'Style-2', 'theplus' ),
					'style-3' => esc_html__( 'Style-3', 'theplus' ),
				],
				'condition' => [
					'RType' => 'review',
				],
			]
		);
		$this->add_control(
			'layoutstyle2',
			[
				'label' => esc_html__( 'Style layout', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'layout-1',
				'options' => [
					'layout-1'  => esc_html__( 'layout-1', 'theplus' ),
					'layout-2' => esc_html__( 'layout-2', 'theplus' ),
				],
				'condition' => [
					'RType' => 'review',
					'style' => 'style-2',
				],
			]
		);
		$this->add_control(
			'layout',
			[
				'label' => esc_html__( 'Layout', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'grid',
				'options' => [
					'grid'  => esc_html__( 'Grid', 'theplus' ),
					'masonry' => esc_html__( 'Masonry', 'theplus' ),
					'carousel' => esc_html__( 'Carousel', 'theplus' ),
				],
				'condition' => [
					'RType' => 'review',
				],
			]
		);
		$this->add_control(
			'Bstyle',
			[
				'label' => esc_html__( 'Style', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'style-1',
				'options' => [
					'style-1'  => esc_html__( 'Style-1', 'theplus' ),
					'style-2' => esc_html__( 'Style-2', 'theplus' ),
					'style-3' => esc_html__( 'Style-3', 'theplus' ),
				],
				'condition' => [
					'RType' => 'beach',
				],
			]
		);
		$this->add_control(
			'BType',
			[
				'label' => esc_html__( 'Source', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'b-facebook',
				'options' => [
					'b-facebook'  => esc_html__( 'Facebook', 'theplus' ),
					'b-google' => esc_html__( 'Google', 'theplus' ),
				],
				'condition' => [
					'RType' => 'beach',
				],
			]
		);
		$this->add_control(
			'BeachFacebookButton',
			[
				'type' => Controls_Manager::RAW_HTML,
				'raw' => '<a class="tp-beach-fb-button" id="tp-beach-fb-button" ><i class="fa fa-facebook-official" aria-hidden="true"></i>Generate Access Token</a>',
				'content_classes' => 'tp-beach-fb-btn',
				'label_block'=> true,
				'condition' => [
					'RType' => 'beach',
					'BType' => 'b-facebook',
				],
			]
		);
		$this->add_control(
			'BTypeFacebook',
			[
				'label' => esc_html__( 'Facebook Review', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'rows' => 5,
				'default' => esc_html__( 'Facebook Reviews', 'theplus' ),
				'placeholder' => esc_html__( 'Enter TEXT', 'theplus' ),
				'dynamic' => [
					'active'   => true,
				],	
				'condition' => [
					'RType' => 'beach',
					'BType' => 'b-facebook',
				],			
			]
		);
		$this->add_control(
			'BTypeGoogle',
			[
				'label' => esc_html__( 'Google Review', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'rows' => 5,
				'default' => esc_html__( 'Google Reviews', 'theplus' ),
				'placeholder' => esc_html__( 'Enter TEXT', 'theplus' ),
				'dynamic' => [
					'active'   => true,
				],	
				'condition' => [
					'RType' => 'beach',
					'BType' => 'b-google',
				],			
			]
		);
		$this->add_control(
			'BToken',
			[
				'label' => esc_html__( 'Access Token', 'theplus' ),
				'type' => Controls_Manager::TEXTAREA,
				'rows' => 5,
				'default' => '',
				'placeholder' => esc_html__( 'Enter Value', 'theplus' ),
				'dynamic' => [
					'active' => true,
				],	
				'condition' => [
					'RType' => 'beach',
				],			
			]
		);
		$this->add_control(
			'B_TokenGoogle',
			[				
				'type' => Controls_Manager::RAW_HTML,
				'raw' => 'Note : How to <a href="https://console.cloud.google.com/apis/credentials" target="_blank" rel="noopener noreferrer">(Create Token ?)</a>',
				'content_classes' => 'tp-widget-description',
				'condition' => [
					'RType' => 'beach',
					'BType' => 'b-google',
				],
			]
		);
		$this->add_control(
			'BPPId',
			[
				'label' => esc_html__( 'Page/Place Id', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'dynamic' => [
					'active' => true,
				],
				'condition' => [
					'RType' => 'beach',
				],
			]
		);
		$this->add_control(
			'B_TokenPlaceId',
			[				
				'type' => Controls_Manager::RAW_HTML,
				'raw' => 'Note : How to <a href="https://developers.google.com/places/web-service/place-id" target="_blank" rel="noopener noreferrer">(Place Id ?)</a>',
				'content_classes' => 'tp-widget-description',
				'condition' => [
					'RType' => 'beach',
					'BType' => 'b-google',
				],
			]
		);	
		$repeater = new \Elementor\Repeater();
		$repeater->add_control(
			'ReviewsType',[
				'label' => esc_html__( 'Source','theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'facebook',
				'options' => [
					'facebook' => esc_html__( 'Facebook','theplus' ),
					'google' => esc_html__( 'Google','theplus' ),
					'manual' => esc_html__( 'Manual','theplus' ),					
				],
			]
		);
		$repeater->add_control('GLanguage',
			[
				'label'=>__('Language','theplus'),
				'type'=>Controls_Manager::SELECT,
				'default'=>'en',
				'options'=>[
					'af'=>__('Afrikaans','theplus'),
					'sq'=>__('Albanian','theplus'),
					'am'=>__('Amharic','theplus'),
					'ar'=>__('Arabic','theplus'),
					'hy'=>__('Armenian','theplus'),
					'az'=>__('Azerbaijani','theplus'),
					'eu'=>__('Basque','theplus'),
					'be'=>__('Belarusian','theplus'),
					'bn'=>__('Bengali','theplus'),
					'bs'=>__('Bosnian','theplus'),
					'bg'=>__('Bulgarian','theplus'),
					'my'=>__('Burmese','theplus'),
					'ca'=>__('Catalan','theplus'),
					'zh'=>__('Chinese','theplus'),
					'zh-CN'=>__('Chinese (Simplified)','theplus'),
					'zh-HK'=>__('Chinese (Hong Kong)','theplus'),
					'zh-TW'=>__('Chinese (Traditional)','theplus'),
					'hr'=>__('Croatian','theplus'),
					'cs'=>__('Czech','theplus'),
					'da'=>__('Danish','theplus'),
					'nl'=>__('Dutch','theplus'),
					'en'=>__('English','theplus'),
					'en-AU'=>__('English (Australian)','theplus'),
					'en-GB'=>__('English (Great Britain)','theplus'),
					'et'=>__('Estonian','theplus'),
					'fa'=>__('Farsi','theplus'),
					'fi'=>__('Finnish','theplus'),
					'fil'=>__('Filipino','theplus'),
					'fr'=>__('French','theplus'),
					'fr-CA'=>__('French (Canada)','theplus'),
					'gl'=>__('Galician','theplus'),
					'ka'=>__('Georgian','theplus'),
					'de'=>__('German','theplus'),
					'el'=>__('Greek','theplus'),
					'gu'=>__('Gujarati','theplus'),
					'iw'=>__('Hebrew','theplus'),
					'hi'=>__('Hindi','theplus'),
					'hu'=>__('Hungarian','theplus'),
					'is'=>__('Icelandic','theplus'),
					'id'=>__('Indonesian','theplus'),
					'it'=>__('Italian','theplus'),
					'ja'=>__('Japanese','theplus'),
					'kn'=>__('Kannada','theplus'),
					'kk'=>__('Kazakh','theplus'),
					'km'=>__('Khmer','theplus'),
					'ko'=>__('Korean','theplus'),
					'ky'=>__('Kyrgyz','theplus'),
					'lo'=>__('Lao','theplus'),
					'lv'=>__('Latvian','theplus'),
					'lt'=>__('Lithuanian','theplus'),
					'mk'=>__('Macedonian','theplus'),
					'ms'=>__('Malay','theplus'),
					'ml'=>__('Malayalam','theplus'),
					'mr'=>__('Marathi','theplus'),
					'mn'=>__('Mongolian','theplus'),
					'ne'=>__('Nepali','theplus'),
					'no'=>__('Norwegian','theplus'),
					'pl'=>__('Polish','theplus'),
					'pt'=>__('Portuguese','theplus'),
					'pt-BR'=>__('Portuguese (Brazil)','theplus'),
					'pt-PT'=>__('Portuguese (Portugal)','theplus'),
					'pa'=>__('Punjabi','theplus'),
					'ro'=>__('Romanian','theplus'),
					'ru'=>__('Russian','theplus'),
					'sr'=>__('Serbian','theplus'),
					'si'=>__('Sinhalese','theplus'),
					'sk'=>__('Slovak','theplus'),
					'sl'=>__('Slovenian','theplus'),
					'es'=>__('Spanish','theplus'),
					'es-419'=>__('Spanish (Latin America)','theplus'),
					'sw'=>__('Swahili','theplus'),
					'sv'=>__('Swedish','theplus'),
					'ta'=>__('Tamil','theplus'),
					'te'=>__('Telugu','theplus'),
					'th'=>__('Thai','theplus'),
					'tr'=>__('Turkish','theplus'),
					'uk'=>__('Ukrainian','theplus'),
					'ur'=>__('Urdu','theplus'),
					'uz'=>__('Uzbek','theplus'),
					'vi'=>__('Vietnamese','theplus'),
					'zu'=>__('Zulu','theplus'),
				],
				'condition' => [
					'ReviewsType' => 'google',
				],
			]
		);
		$repeater->add_control(
			'FacebookButton',
			[
				'type' => Controls_Manager::RAW_HTML,
				'raw' => '<a class="tp-review-fb-button" id="tp-review-fb-button" ><i class="fa fa-facebook-official" aria-hidden="true"></i>Generate Access Token</a>',
				'content_classes' => 'tp-review-fb-btn',
				'label_block'=>true,
				'condition' => [
					'ReviewsType' => 'facebook',
				],
			]
		);
		$repeater->add_control(
			'Token',
			[
				'label' => esc_html__( 'Access Token', 'theplus' ),
				'type' => Controls_Manager::TEXTAREA,
				'rows' => 5,
				'default' => '',
				'placeholder' => esc_html__( 'Enter Value', 'theplus' ),
				'dynamic' => [
					'active'   => true,
				],
				'condition' => [
					'ReviewsType!' => 'manual',
				],			
			]
		);
		$repeater->add_control(
			'FbPageId',
			[
				'label' => esc_html__( 'Page/Place ID', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'dynamic' => [
					'active' => true,
				],
				'condition' => [
					'ReviewsType' => 'facebook',
				],
			]
		);
		$repeater->add_control(
			'FbRType',[
				'label' => esc_html__( 'Result Type','theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'default',
				'options' => [
					'default' => esc_html__( 'Default','theplus' ),
					'negative' => esc_html__( 'Negative','theplus' ),
					'positive' => esc_html__( 'Positive','theplus' ),					
				],
				'condition' => [
					'ReviewsType' => 'facebook',
				],
			]
		);
		$repeater->add_control(
			'TokenGoogle',
			[				
				'type' => Controls_Manager::RAW_HTML,
				'raw' => 'Note : How to <a href="https://console.cloud.google.com/apis/credentials" target="_blank" rel="noopener noreferrer">(Create Token ?)</a>',
				'content_classes' => 'tp-widget-description',
				'condition' => [
					'ReviewsType' => 'google',
				],
			]
		);
		$repeater->add_control(
			'GPlaceID',
			[
				'label' => esc_html__( 'Page/Place ID', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'dynamic' => [
					'active' => true,
				],
				'condition' => [
					'ReviewsType' => 'google',
				],
			]
		);
		
		$repeater->add_control(
			'TokenPlaceId',
			[				
				'type' => Controls_Manager::RAW_HTML,
				'raw' => 'Note : How to <a href="https://developers.google.com/places/web-service/place-id" target="_blank" rel="noopener noreferrer">(Place Id ?)</a>',
				'content_classes' => 'tp-widget-description',
				'condition' => [
					'ReviewsType' => 'google',
				],
			]
		);	
		$repeater->add_control(
			'CUImg',
			[
				'label' => esc_html__( 'User Profile Image', 'theplus' ),
				'type' => \Elementor\Controls_Manager::GALLERY,
				'dynamic' => ['active'   => true],
				'default' => [
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				],
				'condition' => [
					'ReviewsType' => 'manual',
				],
				'separator' => 'after',
			]
		);
		$repeater->add_control(
			'Cuname',
			[
				'label' => esc_html__( 'Username', 'theplus' ),
				'type' => Controls_Manager::TEXTAREA,
				'rows' => 2,
				'placeholder' => esc_html__( 'Enter Username', 'theplus' ),
				'dynamic' => [
					'active' => true,
				],
				'condition' => [
					'ReviewsType' => 'manual',
				],
			]
		);
		$repeater->add_control(
			'Cmassage',
			[
				'label' => esc_html__( 'Review Message', 'theplus' ),
				'type' => Controls_Manager::TEXTAREA,
				'rows' => 2,
				'placeholder' => esc_html__( 'Enter Message', 'theplus' ),
				'dynamic' => [
					'active' => true,
				],
				'condition' => [
					'ReviewsType' => 'manual',
				],
			]
		);
		$repeater->add_control(
			'CPFname',[
				'label' => esc_html__( 'Platform','theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'facebook',
				'options' => [
					'facebook' => esc_html__( 'Facebook','theplus' ),
					'google' => esc_html__( 'Google','theplus' ),
					'manual' => esc_html__( 'Manual','theplus' ),					
				],
				'condition' => [
					'ReviewsType' => 'manual',
				],
			]
		);
		$repeater->add_control(
			'CcuSname',
			[
				'label' => esc_html__( 'Platform Name', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'dynamic' => [
					'active' => true,
				],
				'condition' => [
					'ReviewsType' => 'manual',
					'CPFname' => 'manual',
				],
			]
		);
		$repeater->add_control(
			'CImg',
			[
				'label' => esc_html__( 'Platform Logo', 'theplus' ),
				'type' => Controls_Manager::MEDIA,
				'dynamic' => ['active'   => true,],
				'default' => [
					'url' => '',
				],
				'condition' => [
					'ReviewsType' => 'manual',
					'CPFname' => 'manual',
				],
			]
		);
		$repeater->add_control(
			'Cdate',
			[
				'label' => esc_html__( 'Date', 'theplus' ),
				'type' => Controls_Manager::TEXTAREA,
				'rows' => 2,
				'placeholder' => esc_html__( 'Enter Date', 'theplus' ),
				'dynamic' => [
					'active' => true,
				],
				'condition' => [
					'ReviewsType' => 'manual',
				],
			]
		);
		$repeater->add_control(
			'Cstar',
			[
				'label' => esc_html__( 'Rating Value (1-5)', 'theplus' ),
				'type' => Controls_Manager::TEXTAREA,
				'rows' => 2,
				'placeholder' => esc_html__( 'Enter Value', 'theplus' ),
				'dynamic' => [
					'active' => true,
				],
				'condition' => [
					'ReviewsType' => 'manual',
				],
			]
		);	
		$repeater->add_control(
			'icons',
			[
				'label' => esc_html__( 'Rating Icon', 'theplus' ),
				'type' => Controls_Manager::ICONS,
				'default' => [
					'value' => 'fas fa-star',
					'library' => 'solid',
				],
			]
		);
		$repeater->add_control(
			'RCategory',
			[
				'label' => esc_html__( 'Category (For Filter)', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'placeholder' => esc_html__( 'e.g. Category1, Category2', 'theplus' ),	
				'label_block' => true,							
			]
		);
		$repeater->add_control(
			'MaxR',
			[
				'label' => esc_html__( 'Max Results', 'theplus' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 1,
				'max' => 50,
				'step' => 1,
				'default' => 6,
			]
		);
		$this->add_control(
            'Rreviews',
            [
				'label' => esc_html__( 'Social Reviews', 'theplus' ),
                'type' => Controls_Manager::REPEATER,
                'default' => [
                    [
                        'ReviewsType' => 'facebook',
                        'MaxR' => 6,    
                        'CUImg' => ['url' => \Elementor\Utils::get_placeholder_image_src(),],                                             
                    ],

                ],
                'separator' => 'before',
				'fields' => $repeater->get_controls(),
                'title_field' => '{{{ ReviewsType }}}',
                'condition' => [
					'RType' => 'review',
				],		
            ]
        ); 		
		$this->end_controls_section();
	    /*Content Tab End */
	    /*Reviews Option Start */
		$this->start_controls_section(
			'review_optn_section',
			[
				'label' => esc_html__( 'Review Option', 'theplus' ),
				'tab' => Controls_Manager::TAB_CONTENT,
				'condition' => [
					'RType' => 'review',
				],	
			]
		);
		$this->add_control(
			'FBNagative',
			[
				'label' => esc_html__( 'Negative Review Stars','theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => '1',
				'options' => [
					'1' => esc_html__( '1','theplus' ),
					'2' => esc_html__( '2','theplus' ),
					'3' => esc_html__( '3','theplus' ),
					'4' => esc_html__( '4','theplus' ),
				],
				'description' => 'Note : In Facebook Reviews, You can set value of stars you want to show for negative review you got.'
			]
		);
        $this->add_control(
            'ShowFeedId',
            [
				'label' => esc_html__( 'Display Id & Exclude', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'default' => '',
			]
        );
        $this->add_control(
			'FeedId',
			[
				'label' => esc_html__( 'Exclude Post Ids', 'theplus' ),
				'type' => Controls_Manager::TEXTAREA,
				'rows' => 2,
				'default' => '',
				'placeholder' => esc_html__( 'Add Ids With A Comma To Exclude', 'theplus' ),
				'dynamic' => [
					'active' => true,
				],
				'condition' => [
					'ShowFeedId' => 'yes',
				],
			]
		);
		$this->add_control(
			'SRExcldPIdsNote',
			[				
				'type' => Controls_Manager::RAW_HTML,
				'raw' => 'Note: By Enabling This Option, You Will See The Post Id Of Each In The Back-end, And Then You Can Use Those To Exclude Posts You Want To.',
				'content_classes' => 'tp-widget-description',
				'condition' => [
					'ShowFeedId' => 'yes',
				],
			]
		);
		$this->end_controls_section();
	    /*Reviews Option End */
	    /*Badge Options Start */
		$this->start_controls_section(
			'badge_optn_section',
			[
				'label' => esc_html__( 'Badge Options', 'theplus' ),
				'tab' => Controls_Manager::TAB_CONTENT,
				'condition' => [
					'RType' => 'beach',
				],	
			]
		);
		$this->add_control(
			'Btxt1',
			[
				'label' => esc_html__( 'Postfix Content', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Recommended By' , 'theplus' ),
				'dynamic' => [
					'active' => true,
				],
			]
		);
		$this->add_control(
			'Btxt2',
			[
				'label' => esc_html__( 'Prefix Content', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'People' , 'theplus' ),
				'dynamic' => [
					'active' => true,
				],
				'condition' => [					
					'Bstyle!' => 'style-3',
				],	
			]
		);
		$this->add_control(
            'BRecommend',
            [
				'label' => esc_html__( 'Show Recommended', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'default' => 'yes',	
				'condition' => [					
					'Bstyle' => ['style-1','style-3'],
				],
			]
        );		
        $this->add_control(
			'Blinktxt',
			[
				'label' => esc_html__( 'Prefix Content Text', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Would You Recommend' , 'theplus' ),
				'dynamic' => [
					'active' => true,
				],
				'label_block' => true,
				'condition' => [
					'Bstyle' => 'style-1',
					'BRecommend' => 'yes',
				],	
			]
		);
        $this->add_control(
            'BSButton',
            [
				'label' => esc_html__( '(Single | Multiple) Button', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'default' => 'yes',
				'condition' => [
					'Bstyle' => 'style-1',
					'BRecommend' => 'yes',
				],	
			]
        );
        $this->add_control(
			'BBtnName',
			[
				'label' => esc_html__( 'First Button', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'YES', 'theplus' ),
				'placeholder' => esc_html__( 'Enter Button Name', 'theplus' ),
				'dynamic' => [
					'active' => true,
				],
				'condition' => [
					'Bstyle' => 'style-1',
					'BRecommend' => 'yes',
				],
			]
		);
		$this->add_control(
			'BBtnTName',
			[
				'label' => esc_html__( 'Second Button', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'NO', 'theplus' ),
				'placeholder' => esc_html__( 'Enter Button Name', 'theplus' ),
				'dynamic' => [
					'active' => true,
				],
				'condition' => [
					'Bstyle' => 'style-1',
					'BRecommend' => 'yes',
					'BSButton' => 'yes',
				],
			]
		);
		$this->add_control(
            'IconHidden',
            [
				'label' => esc_html__( 'Show Icon', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'default' => 'yes',	
				'condition' => [
					'Bstyle' => 'style-2',
				],
			]
        );
		$this->end_controls_section();
	    /*Badge Options End */
	    /*columns start*/
		$this->start_controls_section(
			'columns_manage_section',
			[
				'label' => esc_html__( 'Columns Manage', 'theplus' ),
				'tab' => Controls_Manager::TAB_CONTENT,
				'condition' => [
					'RType' => 'review',
					'layout!' => 'carousel',
				],
			]
		);
		$this->add_control(
			'desktop_column',
			[
				'label' => esc_html__( 'Desktop Column', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => '3',
				'options' => theplus_get_columns_list(),
			]
		);
		$this->add_control(
			'tablet_column',
			[
				'label' => esc_html__( 'Tablet Column', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => '4',
				'options' => theplus_get_columns_list(),
			]
		);
		$this->add_control(
			'mobile_column',
			[
				'label' => esc_html__( 'Mobile Column', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => '6',
				'options' => theplus_get_columns_list(),
			]
		);
		$this->add_responsive_control(
			'columnSpace',
			[
				'label' => esc_html__( 'Columns Gap/Space Between', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'default' =>[
					'top' => "15",
					'right' => "15",
					'bottom' => "15",
					'left' => "15",				
				],
				'separator' => 'before',
				'selectors' => [
					'{{WRAPPER}} .tp-social-reviews .post-inner-loop .grid-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->end_controls_section();
		/*columns end*/
		/*Filters Option Start*/
		$this->start_controls_section(
			'filters_optn_section',
			[
				'label' => esc_html__( 'Filter Option', 'theplus' ),
				'tab' => Controls_Manager::TAB_CONTENT,
				'condition' => [
					'RType' => 'review',
					'layout!' => 'carousel',
				],
			]
		);
		$this->add_control(
			'filter_category',
			[
				'label' => esc_html__( 'Category Wise Filter', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'theplus' ),
				'label_off' => esc_html__( 'Hide', 'theplus' ),
				'default' => 'no',
			]
		);
        $this->add_control(
			'all_filter_category',
			[
				'label' => esc_html__( 'All Filter Category Text', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'All', 'theplus' ),
				'condition' => [
					'filter_category' => 'yes',
				],
			]
		);
		$this->add_control(
			'filter_style',
			[
				'label' => esc_html__( 'Category Filter Style', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'style-1',
				'options' => theplus_get_style_list(4),
				'condition'   => [
					'filter_category' => 'yes',
					'layout!' => 'carousel',
				],
			]
		);
		$this->add_control(
			'filter_hover_style',
			[
				'label' => esc_html__( 'Filter Hover Style', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'style-1',
				'options' => theplus_get_style_list(4),
				'condition' => [
					'filter_category' => 'yes',
				],
			]
		);		
		$this->add_control(
			'filter_category_align',
			[
				'label' => esc_html__( 'Filter Alignment', 'theplus' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => esc_html__( 'Left', 'theplus' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'theplus' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'theplus' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'default' => 'center',
				'toggle' => true,
				'label_widget' => false,
				'condition' => [
					'filter_category' => 'yes',
				],
			]
		);
		$this->end_controls_section();
		/*Filters Option End*/
		/*Load More/Lazy Load Option start*/
		$this->start_controls_section(
			'loadmore_lazyload_section',
			[
				'label' => esc_html__( 'Load More/Lazy Load', 'theplus' ),
				'tab' => Controls_Manager::TAB_CONTENT,
				'condition' => [
					'RType' => 'review',
					'layout!' => 'carousel',
				],
			]
		);
		$this->add_control(
			'post_extra_option',
			[
				'label' => esc_html__( 'More Post Loading', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'none',
				'options' => [
					'none' => esc_html__( 'Select Options','theplus' ),
					'load_more' => esc_html__( 'Load More','theplus' ),	
					'lazy_load' => esc_html__( 'Lazy Load','theplus' ),						
				],
			]
		);
		//load more style
		$this->add_control(
			'display_posts',
			[
				'label' => esc_html__( 'Maximum Posts Display', 'theplus' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 1,
				'max' => 200,
				'step' => 1,
				'default' => 8,
				'separator' => 'before',
				'condition' => [
					'post_extra_option' => ['load_more','lazy_load'],
				],
			]
		);
		$this->add_control(
			'load_more_post',
			[
				'label' => esc_html__( 'More Posts On Click/Scroll', 'theplus' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 1,
				'max' => 30,
				'step' => 1,
				'default' => 4,
				'condition'   => [
					'post_extra_option' => ['load_more','lazy_load'],
				],
			]
		);
		$this->add_control(
			'load_more_btn_text',
			[
				'label' => esc_html__( 'Button Text', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Load More', 'theplus' ),
				'condition' => [
					'post_extra_option' => 'load_more',
				],
			]
		);
		$this->add_control(
			'tp_loading_text',
			[
				'label' => esc_html__( 'Loading Text', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Loading...', 'theplus' ),
				'condition' => [
					'post_extra_option' => ['load_more','lazy_load']
				],
			]
		);
		$this->add_control(
			'loaded_posts_text',
			[
				'label' => esc_html__( 'All Posts Loaded Text', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'All done!', 'theplus' ),
				'condition' => [
					'post_extra_option' => ['load_more','lazy_load']
				],
			]
		);	
		$this->end_controls_section();
		/*Load More/Lazy Load Option end*/
		/*Extra options review*/
		$this->start_controls_section(
			'extrabeach_options_section',
			[
				'label' => esc_html__( 'Extra Options', 'theplus' ),
				'tab' => Controls_Manager::TAB_CONTENT,
				'condition' => [
					'RType' => 'beach',
				],
			]
		);
		$this->add_control(
			'beach_TimeFrq',[
				'label' => esc_html__( 'Refresh Time','theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => '86400',
				'options' => [
					'3600' => esc_html__( '1 hour','theplus' ),	
					'7200' => esc_html__( '2 hour','theplus' ),
					'21600' => esc_html__( '6 hour','theplus' ),
					'86400' => esc_html__( '1 day','theplus' ),
					'604800' => esc_html__( '1 Week','theplus' ),
				],
			]
		);
		$this->end_controls_section();
		/*Extra options review*/
		$this->start_controls_section(
			'extra_options_section',
			[
				'label' => esc_html__( 'Extra Options', 'theplus' ),
				'tab' => Controls_Manager::TAB_CONTENT,
				'condition' => [
					'RType' => 'review',
				],
			]
		);
		$this->add_control(
			'TimeFrq',[
				'label' => esc_html__( 'Refresh Time','theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => '86400',
				'options' => [
					'3600' => esc_html__( '1 hour','theplus' ),	
					'7200' => esc_html__( '2 hour','theplus' ),
					'21600' => esc_html__( '6 hour','theplus' ),
					'86400' => esc_html__( '1 day','theplus' ),
					'604800' => esc_html__( '1 Week','theplus' ),
				],
			]
		);
		$this->add_control(
            'TextLimit',
            [
				'label' => esc_html__( 'Text Limit', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'default' => 'yes',
				'separator' => 'before',
			]
        );
        $this->add_control(
			'TextType',[
				'label' => esc_html__( 'Limit On','theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'char',
				'options' => [
					'char' => esc_html__( 'Character','theplus' ),
					'word' => esc_html__( 'Word','theplus' ),							
				],
				'condition' => [
					'TextLimit' => 'yes',
				],
			]
		);
		$this->add_control(
			'TextMore',
			[
				'label' => esc_html__( 'More Text', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Show More', 'theplus' ),
				'condition' => [
					'TextLimit' => 'yes',
				],
			]
		);
		$this->add_control(
			'TextLess',
			[
				'label' => esc_html__( 'Less Text', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Show Less', 'theplus' ),
				'condition' => [
					'TextLimit' => 'yes',
				],
			]
		);
		$this->add_control(
			'TextCount',
			[
				'label' => esc_html__( 'Limit Count', 'theplus' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 1,
				'max' => 2000,
				'step' => 1,
				'default' => 100,
				'condition' => [
					'TextLimit' => 'yes',
				],
			]
		);
		$this->add_control(
            'TextDots',
            [
				'label' => esc_html__( 'Display Dots', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'default' => 'yes',	
				'condition' => [
					'TextLimit' => 'yes',
				],
			]
        );
		$this->add_control(
            'ScrollOn',
            [
				'label' => esc_html__( 'Content Scrolling Bar', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'default' => '',
				'separator' => 'before',
			]
        );
        $this->add_responsive_control(
            'ScrollHgt',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Height', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 500,
						'step' => 10,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 100,
				],
				'render_type' => 'ui',
				'condition' => [
					'ScrollOn' => 'yes',
				],
            ]
        );
		$this->end_controls_section();
		/* Extra options end*/

		/*Performance Start*/
		$this->start_controls_section(
			'performance_options_section',
			[
				'label' => esc_html__( 'Performance', 'theplus' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_control(
            'perf_manage',
            [
				'label' => esc_html__( 'Performance', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'default' => '',
			]
        );
		$this->add_control(
			'SF_delete_transient',
			[
				'type' => Controls_Manager::RAW_HTML,
				'raw' => '<span>Delete All Transient </span><a class="tp-SReview-delete-transient" id="tp-SReview-delete-transient" > Delete </a>',
				'content_classes' => 'tp-SReview-delete-transient-btn',
				'label_block' => true,
			]
		);
		$this->end_controls_section();
		/*Performance End*/

		/*Universal Options Start*/
		$this->start_controls_section(
			'Unisl_optn_stl_section',
			[
				'label' => esc_html__( 'Universal', 'theplus' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'RType' => 'review',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'UnnameTypo',
				'label' => esc_html__( 'Username Typography', 'theplus' ),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector' => '{{WRAPPER}} .tp-social-reviews .tp-SR-username a',
			]
		);
        $this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'UnMsgTypo',
				'label' => esc_html__( 'Message Typography', 'theplus' ),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector' => '{{WRAPPER}} .tp-social-reviews .tp-SR-content',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'UnPostOnTypo',
				'label' => esc_html__( 'Post On Text Typography', 'theplus' ),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector' => '{{WRAPPER}} .tp-social-reviews .tp-SR-logotext',
				'condition' => [
					'style!' => 'style-3',
				],
			]
		);
		$this->start_controls_tabs( 'unisl_color_style' );
		$this->start_controls_tab(
			'unisl_optn_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);
		$this->add_control(
			'UnnameCr',
			[
				'label' => esc_html__( 'Username Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .tp-social-reviews .tp-SR-username a' => 'color: {{VALUE}}',					
				],
			]
		);
		$this->add_control(
			'UnMassageCr',
			[
				'label' => esc_html__( 'Message Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .tp-social-reviews .tp-SR-content' => 'color: {{VALUE}}',					
				],
			]
		);
		$this->add_control(
			'UnPostONCr',
			[
				'label' => esc_html__( 'Post On Text Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .tp-social-reviews .tp-SR-logotext .tp-newline:nth-child(n)' => 'color: {{VALUE}}',					
				],
				'condition' => [
					'style!' => 'style-3',
				],
			]
		);
		$this->add_control(
			'UnTimeCr',
			[
				'label' => esc_html__( 'Time Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .tp-social-reviews .tp-SR-time' => 'color: {{VALUE}}',					
				],
			]
		);
		$this->add_control(
			'BoxNlable',
			[
				'label' => esc_html__( 'Box Background Options', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			'BgBoxNpd',
			[
				'label' => esc_html__( 'Box Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'default' => [
					'px' => [
					'top' => '',
					'right' => '',
					'bottom' => '',
					'left' => '',					
					],
				],
				'selectors' => [
					'{{WRAPPER}} .tp-social-reviews .grid-item .tp-review' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'UnNBg',
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .tp-social-reviews .tp-review',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'UnB',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-social-reviews .tp-review',
			]
		);
		$this->add_responsive_control(
			'UnNBRs',
			[
				'label' => esc_html__( 'Border Radius', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .tp-social-reviews .tp-review' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'UnNBs',
				'selector' => '{{WRAPPER}} .tp-social-reviews .tp-review',
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'unisl_optn_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
		$this->add_control(
			'UnHnameCr',
			[
				'label' => esc_html__( 'Username Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .grid-item:hover .tp-SR-username a' => 'color: {{VALUE}}',	
				],
			]
		);
		$this->add_control(
			'UnHMassageCr',
			[
				'label' => esc_html__( 'Message Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .grid-item:hover .tp-SR-content' => 'color: {{VALUE}}',		
				],
			]
		);
		$this->add_control(
			'UnHPostONCr',
			[
				'label' => esc_html__( 'Post On Text Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .grid-item:hover .tp-SR-logotext .tp-newline:nth-child(n)' => 'color: {{VALUE}}',		
				],
				'condition' => [					
					'style!' => 'style-3',
				],
			]
		);
		$this->add_control(
			'UnHTimeCr',
			[
				'label' => esc_html__( 'Time Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .grid-item:hover .tp-SR-time' => 'color: {{VALUE}}',	
				],
			]
		);
		$this->add_control(
			'BoxHlable',
			[
				'label' => esc_html__( 'Box Background Options', 'theplus' ),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			'BgBoxHpd',
			[
				'label' => esc_html__( 'Box Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'default' => [
					'px' => [
					'top' => '',
					'right' => '',
					'bottom' => '',
					'left' => '',					
					],
				],
				'selectors' => [
					'{{WRAPPER}} .tp-social-reviews .grid-item:hover .tp-review' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'UnHBg',
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .tp-social-reviews .tp-review:hover',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'UnHB',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-social-reviews .tp-review:hover',
			]
		);
		$this->add_responsive_control(
			'UnHBRs',
			[
				'label' => esc_html__( 'Border Radius', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .tp-social-reviews .tp-review:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'UnHBs',
				'selector' => '{{WRAPPER}} .tp-social-reviews .tp-review:hover',
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->start_controls_tabs( 'unitop_color_style' );
		$this->start_controls_tab(
			'unisl_optnR_top',
			[
				'label' => esc_html__( 'Top', 'theplus' ),
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'topBg',
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .tp-social-reviews .tp-SR-header',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'topB',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-social-reviews .tp-SR-header',
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'unisl_optnR_btm',
			[
				'label' => esc_html__( 'Bottom', 'theplus' ),
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'BottomBg',
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .tp-social-reviews .tp-SR-bottom',
				'condition' => [					
					'style!' => 'style-3',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'BottomB',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-social-reviews .tp-SR-bottom',
				'condition' => [
					'style!' => 'style-3',
				],
			]
		);
		$this->add_control(
			'BottomPostCr',
			[
				'label' => esc_html__( 'Posted On Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .tp-social-reviews .tp-newline:nth-child(1)' => 'color: {{VALUE}}',					
				],
				'condition' => [
					'style!' => 'style-3',
				],
			]
		);
		$this->add_control(
			'BottomPltCr',
			[
				'label' => esc_html__( 'Platform Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .tp-social-reviews .tp-newline:nth-child(2)' => 'color: {{VALUE}}',					
				],
				'condition' => [
					'style!' => 'style-3',
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'unisl_optn_Icon',
			[
				'label' => esc_html__( 'Icon', 'theplus' ),
			]
		);
		$this->add_responsive_control(
			'Starpadding',
			[
				'label' => esc_html__( 'Star Box Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'default' => [
					'px' => [
					'top' => '',
					'right' => '',
					'bottom' => '',
					'left' => '',					
					],
				],
				'selectors' => [
					'{{WRAPPER}} .tp-social-reviews .tp-SR-star' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
			'StarIconCr',
			[
				'label' => esc_html__( 'Icon Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .tp-social-reviews .tp-SR-star .SR-star' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_responsive_control(
            'StarIconspace',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Icon space', 'theplus'),
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => '',
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .tp-social-reviews .tp-SR-star .SR-star' => 'width: {{SIZE}}{{UNIT}};',
				],
            ]
        );
        $this->add_responsive_control(
            'StarIconsize',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Icon size', 'theplus'),
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => '',
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .tp-social-reviews .tp-SR-star .SR-star' => 'font-size: {{SIZE}}{{UNIT}}',			
				],
            ]
        );
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->add_control(
			'UserNameArea',
			[
				'label' => esc_html__( 'Username Area', 'theplus' ),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			'Unnamemargin',
			[
				'label' => esc_html__( 'Username Margin', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'default' => [
					'px' => [
					'top' => '',
					'right' => '',
					'bottom' => '',
					'left' => '',					
					],
				],
				'selectors' => [
					'{{WRAPPER}} .tp-social-reviews .tp-SR-username' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'Unnamepadding',
			[
				'label' => esc_html__( 'Username Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'default' => [
					'px' => [
					'top' => '',
					'right' => '',
					'bottom' => '',
					'left' => '',					
					],
				],
				'selectors' => [
					'{{WRAPPER}} .tp-social-reviews .tp-SR-username' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
			'ProfileArea',
			[
				'label' => esc_html__( 'Profile Image', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_control(
			'OverlayImage',
			[
				'label'   => esc_html__( 'Overlay Image', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'no',
				'condition' => [				
					'style!' => 'style-3',
				],
			]
		);
		$this->add_control(
			'OverlayImgLeft',
			[
				'label' => __( 'Image Position', 'plugin-domain' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => -100,
						'max' => 100,
						'step' => 5,
					],
					'%' => [
						'min' => -100,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .social-reviews-style-1.overlayimage img.tp-SR-profile' => 'left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .social-reviews-style-2.overlayimage .tp-SR-profile' => 'top: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'style!' => 'style-3',
					'OverlayImage' => 'yes',
				],
			]
		);
		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name' => 'border',
				'label' => __( 'Profile Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-social-reviews .tp-SR-profile',
			]
		);
		$this->add_responsive_control(
			'BgPRs',
			[
				'label' => esc_html__( 'Profile Radius', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-social-reviews .tp-SR-profile' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			\Elementor\Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'box_shadow',
				'label' => __( 'Profile Box Shadow', 'plugin-domain' ),
				'selector' => '{{WRAPPER}} .tp-social-reviews .tp-SR-profile',
			]
		);
		$this->add_responsive_control(
			'BgHpd',
			[
				'label' => esc_html__( 'Header Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'default' => [
					'px' => [
					'top' => '',
					'right' => '',
					'bottom' => '',
					'left' => '',					
					],
				],
				'selectors' => [
					'{{WRAPPER}} .tp-social-reviews .tp-SR-header' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			'BgBpd',
			[
				'label' => esc_html__( 'Content Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'default' => [
					'px' => [
					'top' => '',
					'right' => '',
					'bottom' => '',
					'left' => '',					
					],
				],
				'selectors' => [
					'{{WRAPPER}} .tp-social-reviews .tp-SR-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			'BgFpd',
			[
				'label' => esc_html__( 'Footer Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'default' => [
					'px' => [
					'top' => '',
					'right' => '',
					'bottom' => '',
					'left' => '',					
					],
				],
				'selectors' => [
					'{{WRAPPER}} .tp-social-reviews .tp-SR-bottom' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'style!' => 'style-3',
				],
				'separator' => 'before',
			]
		);
		$this->end_controls_section();
		/* Universal options end */
		/* Show More Text Start */
		$this->start_controls_section(
			'ShW_MTxt_stl_section',
			[
				'label' => esc_html__( 'Show More Text', 'theplus' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'RType' => 'review',
					'TextLimit' => 'yes',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'SmTxtTypo',
				'label' => esc_html__( 'Typography', 'theplus' ),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector' => '{{WRAPPER}} .tp-social-reviews .tp-message a.readbtn',
				'condition' => [
					'TextLimit' => 'yes',
				],
			]
		);
		$this->start_controls_tabs( 'ShW_MTxt_style' );
		$this->start_controls_tab(
			'ShW_MTxt_NMl',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
				'condition' => [
					'TextLimit' => 'yes',
				],
			]
		);
		$this->add_control(
			'SmTxtNCr',
			[
				'label' => esc_html__( 'Show More', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .tp-social-reviews .tp-message a.readbtn' => 'color: {{VALUE}}',					
				],
				'condition'   => [
					'TextLimit' => 'yes',
				],
			]
		);
		$this->add_control(
			'SlTxtNCr',
			[
				'label' => esc_html__( 'Show Less', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .tp-social-reviews .tp-message.show-less a.readbtn' => 'color: {{VALUE}}',					
				],
				'condition'   => [
					'TextLimit' => 'yes',
				],
			]
		);
		$this->add_control(
			'DotTxtNCr',
			[
				'label' => esc_html__( 'Dot Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .tp-social-reviews .tp-message .sf-dots' => 'color: {{VALUE}}',
				],
				'condition' => [
					'TextLimit' => 'yes',
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'ShW_MTxt_Hvr',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
				'condition' => [
					'TextLimit' => 'yes',
				],
			]
		);
		$this->add_control(
			'SmTxtHCr',
			[
				'label' => esc_html__( 'Show More', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .tp-social-reviews .tp-message a.readbtn:hover' => 'color: {{VALUE}}',
				],
				'condition' => [
					'TextLimit' => 'yes',
				],
			]
		);
		$this->add_control(
			'SlTxtHCr',
			[
				'label' => esc_html__( 'Show Less', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .tp-social-reviews .tp-message.show-less a.readbtn:hover' => 'color: {{VALUE}}',
				],
				'condition' => [
					'TextLimit' => 'yes',
				],
			]
		);
		$this->add_control(
			'DotTxtHCr',
			[
				'label' => esc_html__( 'Dot Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .tp-social-reviews .tp-message:hover .sf-dots' => 'color: {{VALUE}}',					
				],
				'condition' => [
					'TextLimit' => 'yes',
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		/* Show More Text End*/

		/* Scroll Text Start*/
		$this->start_controls_section(
			'scroll_section',
			[
				'label' => esc_html__( 'Scroll Text', 'theplus' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'RType' => 'review',
					'ScrollOn' => 'yes',
				],
			]
		);
        $this->start_controls_tabs( 'scrollC_style' );
		$this->start_controls_tab(
			'scrollC_Bar',
			[
				'label' => esc_html__( 'Scrollbar', 'theplus' ),
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'  => 'ScrollBg',
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .tp-social-reviews .tp-normal-scroll::-webkit-scrollbar',
			]
		);
		$this->add_responsive_control(
			'ScrollWidth',
			[
				'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Width', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 100,
						'step' => 1,
					],
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .tp-social-reviews .tp-normal-scroll::-webkit-scrollbar' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'scrollC_Tmb',
			[
				'label' => esc_html__( 'Thumb', 'theplus' ),
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'ThumbBg',
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .tp-social-reviews .tp-normal-scroll::-webkit-scrollbar-thumb',
			]
		);
		$this->add_responsive_control(
			'ThumbBrs',
			[
				'label' => esc_html__( 'Border Radius', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .tp-social-reviews .tp-normal-scroll::-webkit-scrollbar-thumb' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',				
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'ThumbBsw',
				'selector' => '{{WRAPPER}} .tp-social-reviews .tp-normal-scroll::-webkit-scrollbar-thumb',
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'scrollC_Trk',
			[
				'label' => esc_html__( 'Track', 'theplus' ),
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'TrackBg',
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .tp-social-reviews .tp-normal-scroll::-webkit-scrollbar-track',
			]
		);
		$this->add_responsive_control(
			'TrackBRs',
			[
				'label' => esc_html__( 'Border Radius', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .tp-social-reviews .tp-normal-scroll::-webkit-scrollbar-track' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',				
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'TrackBsw',
				'selector' => '{{WRAPPER}} .tp-social-reviews .tp-normal-scroll::-webkit-scrollbar-track',
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		/* Scroll Text End */

		/* Load More/Lazy Load Start*/
		$this->start_controls_section(
			'LoadMore_style_section',
			[
				'label' => esc_html__( 'Load More/Lazy Load', 'theplus' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'RType' => 'review',
					'layout!' => 'carousel',
					'post_extra_option' => ['load_more','lazy_load']
				],
			]
		);
			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' => 'load_more_typography',
					'label' => esc_html__( 'Load More Typography', 'theplus' ),
					'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
					'selector' => '{{WRAPPER}} .ajax_load_more .post-load-more',
					'condition' => [
						'post_extra_option' => 'load_more',
					],
				]
			);
			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' => 'loaded_posts_typo',
					'label' => esc_html__( 'Loaded All Posts Typography', 'theplus' ),
					'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
					'selector' => '{{WRAPPER}} .plus-all-posts-loaded',
					'separator' => 'before',
					'condition'  => [
						'post_extra_option' => ['load_more','lazy_load'],
					],
				]
			);
			$this->add_control(
				'load_more_border',
				[
					'label' => esc_html__( 'Load More Border', 'theplus' ),
					'type' => Controls_Manager::SWITCHER,
					'label_on' => esc_html__( 'Show', 'theplus' ),
					'label_off' => esc_html__( 'Hide', 'theplus' ),
					'default' => 'no',
					'separator' => 'before',
					'condition' => [
						'post_extra_option' => 'load_more',
					],
				]
			);		
			$this->add_control(
				'load_more_border_style',
				[
					'label' => esc_html__( 'Border Style', 'theplus' ),
					'type' => Controls_Manager::SELECT,
					'default' => 'solid',
					'options' => theplus_get_border_style(),
					'selectors'  => [
						'{{WRAPPER}} .ajax_load_more .post-load-more' => 'border-style: {{VALUE}};',
					],
					'condition' => [
						'post_extra_option' => 'load_more',
						'load_more_border' => 'yes',
					],
				]
			);
			$this->add_responsive_control(
				'load_more_border_width',
				[
					'label' => esc_html__( 'Border Width', 'theplus' ),
					'type'  => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%' ],
					'default' => [
						'top'=> 1,
						'right' => 1,
						'bottom' => 1,
						'left' => 1,
					],
					'selectors'  => [
						'{{WRAPPER}} .ajax_load_more .post-load-more' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
					'condition' => [
						'post_extra_option' => 'load_more',
						'load_more_border' => 'yes',
					],
				]
			);
			$this->start_controls_tabs( 'tabs_load_more_border_style' );
			$this->start_controls_tab(
				'tab_load_more_border_normal',
				[
					'label' => esc_html__( 'Normal', 'theplus' ),
					'condition' => [
						'post_extra_option' => 'load_more',
						'load_more_border' => 'yes',
					],
				]
			);
			$this->add_control(
				'load_more_border_color',
				[
					'label' => esc_html__( 'Border Color', 'theplus' ),
					'type' => Controls_Manager::COLOR,
					'default' => '#252525',
					'selectors'  => [
						'{{WRAPPER}} .ajax_load_more .post-load-more' => 'border-color: {{VALUE}};',
					],
					'condition' => [
						'post_extra_option' => 'load_more',
						'load_more_border' => 'yes',
					],
				]
			);		
			$this->add_responsive_control(
				'load_more_border_radius',
				[
					'label' => esc_html__( 'Border Radius', 'theplus' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%' ],
					'selectors' => [
						'{{WRAPPER}} .ajax_load_more .post-load-more' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
					'separator' => 'after',
					'condition' => [
						'post_extra_option' => 'load_more',
						'load_more_border' => 'yes',
					],
				]
			);
			$this->end_controls_tab();
			$this->start_controls_tab(
				'tab_load_more_border_hover',
				[
					'label' => esc_html__( 'Hover', 'theplus' ),
					'condition' => [
						'post_extra_option' => 'load_more',
						'load_more_border' => 'yes',
					],
				]
			);
			$this->add_control(
				'load_more_border_hover_color',
				[
					'label' => esc_html__( 'Border Color', 'theplus' ),
					'type' => Controls_Manager::COLOR,
					'default' => '#252525',
					'selectors'  => [
						'{{WRAPPER}} .ajax_load_more .post-load-more:hover' => 'border-color: {{VALUE}};',
					],
					'condition' => [
						'post_extra_option' => 'load_more',
						'load_more_border' => 'yes',
					],
				]
			);
			$this->add_responsive_control(
				'load_more_border_hover_radius',
				[
					'label' => esc_html__( 'Border Radius', 'theplus' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%' ],
					'selectors' => [
						'{{WRAPPER}} .ajax_load_more .post-load-more:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
					'separator' => 'after',
					'condition' => [
						'post_extra_option'    => 'load_more',
						'load_more_border' => 'yes',
					],
				]
			);
			$this->end_controls_tab();
			$this->end_controls_tabs();
			$this->start_controls_tabs( 'tabs_load_more_style' );
			$this->start_controls_tab(
				'tab_load_more_normal',
				[
					'label' => esc_html__( 'Normal', 'theplus' ),				
					'condition' => [
						'post_extra_option'    => 'load_more',
					],
				]
			);
			$this->add_control(
				'load_more_color',
				[
					'label' => esc_html__( 'Text Color', 'theplus' ),
					'type' => Controls_Manager::COLOR,
					'default' => '',
					'selectors' => [
						'{{WRAPPER}} .ajax_load_more .post-load-more' => 'color: {{VALUE}}',
					],
					'condition' => [
						'post_extra_option' => 'load_more',
					],
				]
			);
			$this->add_control(
				'loaded_posts_color',
				[
					'label' => esc_html__( 'Loaded Posts Text Color', 'theplus' ),
					'type' => Controls_Manager::COLOR,
					'default' => '',
					'selectors' => [
						'{{WRAPPER}} .plus-all-posts-loaded' => 'color: {{VALUE}}',
					],
					'separator' => 'after',
					'condition' => [
						'post_extra_option' => ['load_more','lazy_load'],
					],
				]
			);
			$this->add_control(
				'loading_spin_heading',
				[
					'label' => esc_html__( 'Loading Spinner ', 'theplus' ),
					'type' => Controls_Manager::HEADING,
					'separator' => 'before',
					'condition' => [
						'post_extra_option' => 'lazy_load',
					],
				]
			);
			$this->add_control(
				'loading_spin_color',
				[
					'label' => esc_html__( 'Color', 'theplus' ),
					'type' => Controls_Manager::COLOR,
					'default' => '',
					'selectors' => [
						'{{WRAPPER}} .ajax_lazy_load .post-lazy-load .tp-spin-ring div' => 'border-color: {{VALUE}} transparent transparent transparent',
					],				
					'condition' => [
						'post_extra_option' => 'lazy_load',
					],
				]
			);
			$this->add_responsive_control(
				'loading_spin_size',
				[
					'type' => Controls_Manager::SLIDER,
					'label' => esc_html__('Size', 'theplus'),
					'size_units' => [ 'px' ],
					'range' => [
						'px' => [
							'min' => 1,
							'max' => 200,
							'step' => 1,
						],
					],
					'render_type' => 'ui',
					'selectors' => [
						'{{WRAPPER}} .ajax_lazy_load .post-lazy-load .tp-spin-ring div' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}}',
					],
					'condition' => [
						'post_extra_option' => 'lazy_load',
					],
				]
			);
			$this->add_responsive_control(
				'loading_spin_border_size',
				[
					'type' => Controls_Manager::SLIDER,
					'label' => esc_html__('Border Size', 'theplus'),
					'size_units' => [ 'px' ],
					'range' => [
						'px' => [
							'min' => 1,
							'max' => 20,
							'step' => 1,
						],
					],
					'render_type' => 'ui',
					'selectors' => [
						'{{WRAPPER}} .ajax_lazy_load .post-lazy-load .tp-spin-ring div' => 'border-width: {{SIZE}}{{UNIT}};',
					],
					'condition' => [
						'post_extra_option' => 'lazy_load',
					],
				]
			);
			$this->add_group_control(
				Group_Control_Background::get_type(),
				[
					'name'      => 'load_more_background',
					'types'     => [ 'classic', 'gradient' ],
					'selector'  => '{{WRAPPER}} .ajax_load_more .post-load-more',
					'condition' => [
						'post_extra_option' => 'load_more',
					],
				]
			);		
			$this->add_control(
				'load_more_shadow_options',
				[
					'label' => esc_html__( 'Box Shadow Options', 'theplus' ),
					'type' => Controls_Manager::HEADING,
					'separator' => 'before',
					'condition' => [
						'post_extra_option' => 'load_more',
					],
				]
			);
			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'name' => 'load_more_shadow',
					'selector' => '{{WRAPPER}} .ajax_load_more .post-load-more',
					'condition' => [
						'post_extra_option' => 'load_more',
					],
				]
			);
			$this->end_controls_tab();
			$this->start_controls_tab(
				'tab_load_more_hover',
				[
					'label' => esc_html__( 'Hover', 'theplus' ),
					'condition' => [
						'post_extra_option' => 'load_more',
					],
				]
			);
			$this->add_control(
				'load_more_color_hover',
				[
					'label' => esc_html__( 'Text Hover Color', 'theplus' ),
					'type' => Controls_Manager::COLOR,
					'default' => '',
					'selectors' => [
						'{{WRAPPER}} .ajax_load_more .post-load-more:hover' => 'color: {{VALUE}}',
					],
					'separator' => 'after',
					'condition' => [
						'post_extra_option' => 'load_more',
					],
				]
			);
			$this->add_group_control(Group_Control_Background::get_type(),
				[
					'name' => 'load_more_hover_background',
					'types' => [ 'classic', 'gradient' ],
					'selector' => '{{WRAPPER}} .ajax_load_more .post-load-more:hover',
					'separator' => 'after',
					'condition' => [
						'post_extra_option' => 'load_more',
					],
				]
			);
			$this->add_control(
				'load_more_shadow_hover_options',
				[
					'label' => esc_html__( 'Hover Shadow Options', 'theplus' ),
					'type' => Controls_Manager::HEADING,
					'separator' => 'before',
					'condition' => [
						'post_extra_option' => 'load_more',
					],
				]
			);
			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'name' => 'load_more_hover_shadow',
					'selector' => '{{WRAPPER}} .ajax_load_more .post-load-more:hover',
					'condition' => [
						'post_extra_option' => 'load_more',
					],
				]
			);
			$this->end_controls_tab();
			$this->end_controls_tabs();
		$this->end_controls_section();
		/* Load More/Lazy Load End */

		/* carousel option */
		$this->start_controls_section(
            'section_carousel_options_styling',
            [
                'label' => esc_html__('Carousel', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'RType' => 'review',
					'layout' => 'carousel',
				],
            ]
        );
		$this->add_control(
			'slider_direction',
			[
				'label' => esc_html__( 'Slider Mode', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'horizontal',
				'options' => [
					'horizontal'  => esc_html__( 'Horizontal', 'theplus' ),
					'vertical' => esc_html__( 'Vertical', 'theplus' ),
				],
			]
		);		
		$this->add_control(
            'slide_speed',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Slide Speed', 'theplus'),
				'size_units' => '',
				'range' => [
					'' => [
						'min' => 0,
						'max' => 10000,
						'step' => 100,
					],
				],
				'default' => [
					'unit' => '',
					'size' => 1500,
				],
            ]
        );		
		$this->start_controls_tabs( 'tabs_carousel_style' );
		$this->start_controls_tab(
			'tab_carousel_desktop',
			[
				'label' => esc_html__( 'Desktop', 'theplus' ),
			]
		);
		$this->add_control(
			'slider_desktop_column',
			[
				'label' => esc_html__( 'Desktop Columns', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => '4',
				'options' => theplus_carousel_desktop_columns(),
			]
		);
		$this->add_control(
			'steps_slide',
			[
				'label' => esc_html__( 'Next Previous', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => '1',
				'description' => esc_html__( 'Select option of column scroll on previous or next in carousel.','theplus' ),
				'options' => [
					'1'  => esc_html__( 'One Column', 'theplus' ),
					'2' => esc_html__( 'All Visible Columns', 'theplus' ),
				],
			]
		);
		$this->add_responsive_control(
			'slider_padding',
			[
				'label' => esc_html__( 'Slide Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'default' => [
					'top' => "0",
					'right' => "15",
					'bottom' => "0",
					'left' => "15",				
				],
				'selectors' => [
					'{{WRAPPER}} .list-carousel-slick .slick-initialized .slick-slide' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
			'slider_draggable',
			[
				'label' => esc_html__( 'Draggable', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'yes',
			]
		);
		$this->add_control(
			'slider_infinite',
			[
				'label' => esc_html__( 'Infinite Mode', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'yes',
			]
		);
		$this->add_control(
			'slider_pause_hover',
			[
				'label' => esc_html__( 'Pause On Hover', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'no',
			]
		);
		$this->add_control(
			'slider_adaptive_height',
			[
				'label' => esc_html__( 'Adaptive Height', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'no',
			]
		);
		$this->add_control(
			'slider_animation',
			[
				'label' => esc_html__( 'Animation Type', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'ease',
				'options' => [
					'ease' => esc_html__( 'With Hold', 'theplus' ),
					'linear' => esc_html__( 'Continuous', 'theplus' ),
				],
			]
		);
		$this->add_control(
			'slider_autoplay',
			[
				'label' => esc_html__( 'Autoplay', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'yes',
			]
		);
		$this->add_control(
            'autoplay_speed',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Autoplay Speed', 'theplus'),
				'size_units' => '',
				'range' => [
					'' => [
						'min' => 500,
						'max' => 10000,
						'step' => 200,
					],
				],
				'default' => [
					'unit' => '',
					'size' => 3000,
				],
				'condition' => [
					'slider_autoplay' => 'yes',
				],
            ]
        );		
		$this->add_control(
			'slider_dots',
			[
				'label' => esc_html__( 'Show Dots', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'yes',
				'separator' => 'before',
			]
		);
		$this->add_control(
			'slider_dots_style',
			[
				'label' => esc_html__( 'Dots Style', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'style-1',
				'options' => [
					'style-1' => esc_html__( 'Style 1', 'theplus' ),
					'style-2' => esc_html__( 'Style 2', 'theplus' ),
					'style-3' => esc_html__( 'Style 3', 'theplus' ),
					'style-4' => esc_html__( 'Style 4', 'theplus' ),
					'style-5' => esc_html__( 'Style 5', 'theplus' ),
					'style-6' => esc_html__( 'Style 6', 'theplus' ),
					'style-7' => esc_html__( 'Style 7', 'theplus' ),
				],
				'condition' => [
					'slider_dots' => ['yes'],
				],
			]
		);
		$this->add_control(
			'dots_border_color',
			[
				'label' => esc_html__( 'Dots Border Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#252525',
				'selectors' => [
					'{{WRAPPER}} .list-carousel-slick .slick-dots.style-1 li button,{{WRAPPER}} .list-carousel-slick .slick-dots.style-6 li button' => '-webkit-box-shadow:inset 0 0 0 8px {{VALUE}};-moz-box-shadow: inset 0 0 0 8px {{VALUE}};box-shadow: inset 0 0 0 8px {{VALUE}};',
					'{{WRAPPER}} .list-carousel-slick .slick-dots.style-1 li.slick-active button' => '-webkit-box-shadow:inset 0 0 0 1px {{VALUE}};-moz-box-shadow: inset 0 0 0 1px {{VALUE}};box-shadow: inset 0 0 0 1px {{VALUE}};',
					'{{WRAPPER}} .list-carousel-slick .slick-dots.style-2 li button' => 'border-color:{{VALUE}};',
					'{{WRAPPER}} .list-carousel-slick ul.slick-dots.style-3 li button' => '-webkit-box-shadow: inset 0 0 0 1px {{VALUE}};-moz-box-shadow: inset 0 0 0 1px {{VALUE}};box-shadow: inset 0 0 0 1px {{VALUE}};',
					'{{WRAPPER}} .list-carousel-slick .slick-dots.style-3 li.slick-active button' => '-webkit-box-shadow: inset 0 0 0 8px {{VALUE}};-moz-box-shadow: inset 0 0 0 8px {{VALUE}};box-shadow: inset 0 0 0 8px {{VALUE}};',
					'{{WRAPPER}} .list-carousel-slick ul.slick-dots.style-4 li button' => '-webkit-box-shadow: inset 0 0 0 0px {{VALUE}};-moz-box-shadow: inset 0 0 0 0px {{VALUE}};box-shadow: inset 0 0 0 0px {{VALUE}};',
					'{{WRAPPER}} .list-carousel-slick .slick-dots.style-1 li button:before' => 'color: {{VALUE}};',
				],
				'condition' => [
					'slider_dots_style' => ['style-1','style-2','style-3','style-5'],
					'slider_dots' => 'yes',
				],
			]
		);
		$this->add_control(
			'dots_bg_color',
			[
				'label' => esc_html__( 'Dots Background Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#fff',
				'selectors' => [
					'{{WRAPPER}} .list-carousel-slick .slick-dots.style-2 li button,{{WRAPPER}} .list-carousel-slick ul.slick-dots.style-3 li button,{{WRAPPER}} .list-carousel-slick .slick-dots.style-4 li button:before,{{WRAPPER}} .list-carousel-slick .slick-dots.style-5 button,{{WRAPPER}} .list-carousel-slick .slick-dots.style-7 button' => 'background: {{VALUE}};',
				],
				'condition' => [
					'slider_dots_style' => ['style-2','style-3','style-4','style-5','style-7'],
					'slider_dots' => 'yes',
				],
			]
		);
		$this->add_control(
			'dots_active_border_color',
			[
				'label' => esc_html__( 'Dots Active Border Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#000',
				'selectors' => [
					'{{WRAPPER}} .list-carousel-slick .slick-dots.style-2 li::after' => 'border-color: {{VALUE}};',
					'{{WRAPPER}} .list-carousel-slick .slick-dots.style-4 li.slick-active button' => '-webkit-box-shadow: inset 0 0 0 1px {{VALUE}};-moz-box-shadow: inset 0 0 0 1px {{VALUE}};box-shadow: inset 0 0 0 1px {{VALUE}};',
					'{{WRAPPER}} .list-carousel-slick .slick-dots.style-6 .slick-active button:after' => 'color: {{VALUE}};',
				],
				'condition' => [
					'slider_dots_style' => ['style-2','style-4','style-6'],
					'slider_dots' => 'yes',
				],
			]
		);
		$this->add_control(
			'dots_active_bg_color',
			[
				'label' => esc_html__( 'Dots Active Background Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#000',
				'selectors' => [
					'{{WRAPPER}} .list-carousel-slick .slick-dots.style-2 li::after,{{WRAPPER}} .list-carousel-slick .slick-dots.style-4 li.slick-active button:before,{{WRAPPER}} .list-carousel-slick .slick-dots.style-5 .slick-active button,{{WRAPPER}} .list-carousel-slick .slick-dots.style-7 .slick-active button' => 'background: {{VALUE}};',					
				],
				'condition' => [
					'slider_dots_style' => ['style-2','style-4','style-5','style-7'],
					'slider_dots' => 'yes',
				],
			]
		);
		$this->add_control(
            'dots_top_padding',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Dots Top Padding', 'theplus'),
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
						'step' => 2,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 0,
				],
				'selectors' => [
					'{{WRAPPER}} .list-carousel-slick .slick-slider.slick-dotted' => 'padding-bottom: {{SIZE}}{{UNIT}};',					
				],				
				'condition' => [
					'slider_dots' => 'yes',
				],
            ]
        );
		$this->add_control(
			'hover_show_dots',
			[
				'label' => esc_html__( 'On Hover Dots', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'no',
				'condition' => [
					'slider_dots' => 'yes',
				],
			]
		);
		$this->add_control(
			'slider_arrows',
			[
				'label' => esc_html__( 'Show Arrows', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'no',
				'separator' => 'before',
			]
		);
		$this->add_control(
			'slider_arrows_style',
			[
				'label' => esc_html__( 'Arrows Style', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'style-1',
				'options' => [
					'style-1' => esc_html__( 'Style 1', 'theplus' ),
					'style-2' => esc_html__( 'Style 2', 'theplus' ),
					'style-3' => esc_html__( 'Style 3', 'theplus' ),
					'style-4' => esc_html__( 'Style 4', 'theplus' ),
					'style-5' => esc_html__( 'Style 5', 'theplus' ),
					'style-6' => esc_html__( 'Style 6', 'theplus' ),
				],
				'condition' => [
					'slider_arrows' => ['yes'],
				],
			]
		);
		$this->add_control(
			'arrows_position',
			[
				'label' => esc_html__( 'Arrows Style', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'top-right',
				'options' => [
					'top-right' => esc_html__( 'Top-Right', 'theplus' ),
					'bottm-left' => esc_html__( 'Bottom-Left', 'theplus' ),
					'bottom-center' => esc_html__( 'Bottom-Center', 'theplus' ),
					'bottom-right' => esc_html__( 'Bottom-Right', 'theplus' ),
				],				
				'condition' => [
					'slider_arrows' => ['yes'],
					'slider_arrows_style' => ['style-3','style-4'],
				],
			]
		);
		$this->add_control(
			'arrow_bg_color',
			[
				'label' => esc_html__( 'Arrow Background Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#c44d48',
				'selectors' => [
					'{{WRAPPER}} .list-carousel-slick .slick-nav.slick-prev.style-1,{{WRAPPER}} .list-carousel-slick .slick-nav.slick-next.style-1,{{WRAPPER}} .list-carousel-slick .slick-nav.style-3:before,{{WRAPPER}} .list-carousel-slick .slick-prev.style-3:before,{{WRAPPER}} .list-carousel-slick .slick-prev.style-6:before,{{WRAPPER}} .list-carousel-slick .slick-next.style-6:before' => 'background: {{VALUE}};',					
					'{{WRAPPER}} .list-carousel-slick .slick-prev.style-4:before,{{WRAPPER}} .list-carousel-slick .slick-nav.style-4:before' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'slider_arrows_style' => ['style-1','style-3','style-4','style-6'],
					'slider_arrows' => 'yes',
				],
			]
		);
		$this->add_control(
			'arrow_icon_color',
			[
				'label' => esc_html__( 'Arrow Icon Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#fff',
				'selectors' => [
					'{{WRAPPER}} .list-carousel-slick .slick-nav.slick-prev.style-1:before,{{WRAPPER}} .list-carousel-slick .slick-nav.slick-next.style-1:before,{{WRAPPER}} .list-carousel-slick .slick-prev.style-3:before,{{WRAPPER}} .list-carousel-slick .slick-nav.style-3:before,{{WRAPPER}} .list-carousel-slick .slick-prev.style-4:before,{{WRAPPER}} .list-carousel-slick .slick-nav.style-4:before,{{WRAPPER}} .list-carousel-slick .slick-nav.style-6 .icon-wrap' => 'color: {{VALUE}};',					
					'{{WRAPPER}} .list-carousel-slick .slick-prev.style-2 .icon-wrap:before,{{WRAPPER}} .list-carousel-slick .slick-prev.style-2 .icon-wrap:after,{{WRAPPER}} .list-carousel-slick .slick-next.style-2 .icon-wrap:before,{{WRAPPER}} .list-carousel-slick .slick-next.style-2 .icon-wrap:after,{{WRAPPER}} .list-carousel-slick .slick-prev.style-5 .icon-wrap:before,{{WRAPPER}} .list-carousel-slick .slick-prev.style-5 .icon-wrap:after,{{WRAPPER}} .list-carousel-slick .slick-next.style-5 .icon-wrap:before,{{WRAPPER}} .list-carousel-slick .slick-next.style-5 .icon-wrap:after' => 'background: {{VALUE}};',
				],
				'condition' => [
					'slider_arrows' => 'yes',
				],
			]
		);
		$this->add_control(
			'arrow_hover_bg_color',
			[
				'label' => esc_html__( 'Arrow Hover Background Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#fff',
				'selectors' => [
					'{{WRAPPER}} .list-carousel-slick .slick-nav.slick-prev.style-1:hover,{{WRAPPER}} .list-carousel-slick .slick-nav.slick-next.style-1:hover,{{WRAPPER}} .list-carousel-slick .slick-prev.style-2:hover::before,{{WRAPPER}} .list-carousel-slick .slick-next.style-2:hover::before,{{WRAPPER}} .list-carousel-slick .slick-prev.style-3:hover:before,{{WRAPPER}} .list-carousel-slick .slick-nav.style-3:hover:before' => 'background: {{VALUE}};',
					'{{WRAPPER}} .list-carousel-slick .slick-prev.style-4:hover:before,{{WRAPPER}} .list-carousel-slick .slick-nav.style-4:hover:before' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'slider_arrows_style' => ['style-1','style-2','style-3','style-4'],
					'slider_arrows' => 'yes',
				],
			]
		);
		$this->add_control(
			'arrow_hover_icon_color',
			[
				'label' => esc_html__( 'Arrow Hover Icon Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#c44d48',
				'selectors' => [
					'{{WRAPPER}} .list-carousel-slick .slick-nav.slick-prev.style-1:hover:before,{{WRAPPER}} .list-carousel-slick .slick-nav.slick-next.style-1:hover:before,{{WRAPPER}} .list-carousel-slick .slick-prev.style-3:hover:before,{{WRAPPER}} .list-carousel-slick .slick-nav.style-3:hover:before,{{WRAPPER}} .list-carousel-slick .slick-prev.style-4:hover:before,{{WRAPPER}} .list-carousel-slick .slick-nav.style-4:hover:before,{{WRAPPER}} .list-carousel-slick .slick-nav.style-6:hover .icon-wrap' => 'color: {{VALUE}};',
					'{{WRAPPER}} .list-carousel-slick .slick-prev.style-2:hover .icon-wrap::before,{{WRAPPER}} .list-carousel-slick .slick-prev.style-2:hover .icon-wrap::after,{{WRAPPER}} .list-carousel-slick .slick-next.style-2:hover .icon-wrap::before,{{WRAPPER}} .list-carousel-slick .slick-next.style-2:hover .icon-wrap::after,{{WRAPPER}} .list-carousel-slick .slick-prev.style-5:hover .icon-wrap::before,{{WRAPPER}} .list-carousel-slick .slick-prev.style-5:hover .icon-wrap::after,{{WRAPPER}} .list-carousel-slick .slick-next.style-5:hover .icon-wrap::before,{{WRAPPER}} .list-carousel-slick .slick-next.style-5:hover .icon-wrap::after' => 'background: {{VALUE}};',
				],
				'condition' => [
					'slider_arrows' => 'yes',
				],
			]
		);
		$this->add_control(
			'outer_section_arrow',
			[
				'label' => esc_html__( 'Outer Content Arrow', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'no',
				'condition' => [
					'slider_arrows' => 'yes',
					'slider_arrows_style' => ['style-1','style-2','style-5','style-6'],
				],
			]
		);
		$this->add_control(
			'hover_show_arrow',
			[
				'label' => esc_html__( 'On Hover Arrow', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'no',
				'condition' => [
					'slider_arrows' => 'yes',
				],
			]
		);
		$this->add_control(
			'slider_center_mode',
			[
				'label' => esc_html__( 'Center Mode', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'no',
				'separator' => 'before',
			]
		);
		$this->add_control(
            'center_padding',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Center Padding', 'theplus'),
				'size_units' => '',
				'range' => [
					'' => [
						'min' => 0,
						'max' => 500,
						'step' => 2,
					],
				],
				'default' => [
					'unit' => '',
					'size' => 0,
				],
				'condition'    => [
					'slider_center_mode' => ['yes'],
				],
            ]
        );
		$this->add_control(
			'slider_center_effects',
			[
				'label' => esc_html__( 'Center Slide Effects', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'none',
				'options' => theplus_carousel_center_effects(),
				'condition' => [
					'slider_center_mode' => ['yes'],
				],
			]
		);
		$this->add_control(
            'scale_center_slide',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Center Slide Scale', 'theplus'),
				'size_units' => '',
				'range' => [
					'' => [
						'min' => 0.3,
						'max' => 2,
						'step' => 0.02,
					],
				],
				'default' => [
					'unit' => '',
					'size' => 1,
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .list-carousel-slick .slick-slide.slick-current.slick-active.slick-center,
					{{WRAPPER}} .list-carousel-slick .slick-slide.scc-animate' => '-webkit-transform: scale({{SIZE}});-moz-transform:    scale({{SIZE}});-ms-transform:     scale({{SIZE}});-o-transform:      scale({{SIZE}});transform:scale({{SIZE}});opacity:1;',
				],
				'condition' => [
					'slider_center_mode' => 'yes',
					'slider_center_effects' => 'scale',
				],
            ]
        );
		$this->add_control(
            'scale_normal_slide',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Normal Slide Scale', 'theplus'),
				'size_units' => '',
				'range' => [
					'' => [
						'min' => 0.3,
						'max' => 2,
						'step' => 0.02,
					],
				],
				'default' => [
					'unit' => '',
					'size' => 0.8,
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .list-carousel-slick .slick-slide' => '-webkit-transform: scale({{SIZE}});-moz-transform:    scale({{SIZE}});-ms-transform:     scale({{SIZE}});-o-transform:      scale({{SIZE}});transform:scale({{SIZE}});transition: .3s all linear;',
				],
				'condition' => [
					'slider_center_mode' => 'yes',
					'slider_center_effects' => 'scale',
				],
            ]
        );
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'shadow_active_slide',
				'selector' => '{{WRAPPER}} .list-carousel-slick .slick-slide.slick-current.slick-active.slick-center .blog-list-content',
				'condition' => [
					'slider_center_mode' => 'yes',
					'slider_center_effects' => 'shadow',
				],
			]
		);
		$this->add_control(
            'opacity_normal_slide',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Normal Slide Opacity', 'theplus'),
				'size_units' => '',
				'range' => [
					'' => [
						'min' => 0.1,
						'max' => 1,
						'step' => 0.1,
					],
				],
				'default' => [
					'unit' => '',
					'size' => 0.7,
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .list-carousel-slick .slick-slide' => 'opacity:{{SIZE}}',
				],
				'condition' => [
					'slider_center_mode' => 'yes',
					'slider_center_effects!' => 'none',
				],
            ]
        );
		$this->add_control(
			'slider_rows',
			[
				'label' => esc_html__( 'Number Of Rows', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => '1',
				'options' => [
					"1" => esc_html__("1 Row", 'theplus'),
					"2" => esc_html__("2 Rows", 'theplus'),
					"3" => esc_html__("3 Rows", 'theplus'),
				],
				'separator' => 'before',
			]
		);
		$this->add_control(
            'slide_row_top_space',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Row Top Space', 'theplus'),
				'size_units' => '',
				'range' => [
					'' => [
						'min' => 0,
						'max' => 500,
						'step' => 2,
					],
				],
				'default' => [
					'unit' => '',
					'size' => 15,
				],
				'selectors' => [
					'{{WRAPPER}} .list-carousel-slick[data-slider_rows="2"] .slick-slide > div:last-child,{{WRAPPER}} .list-carousel-slick[data-slider_rows="3"] .slick-slide > div:nth-last-child(-n+2)' => 'padding-top:{{SIZE}}px',
				],
				'condition' => [
					'slider_rows' => ['2','3'],
				],
            ]
        );
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_carousel_tablet',
			[
				'label' => esc_html__( 'Tablet', 'theplus' ),
			]
		);
		$this->add_control(
			'slider_tablet_column',
			[
				'label' => esc_html__( 'Tablet Columns', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => '3',
				'options' => theplus_carousel_tablet_columns(),
			]
		);
		$this->add_control(
			'tablet_steps_slide',
			[
				'label' => esc_html__( 'Next Previous', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => '1',
				'description' => esc_html__( 'Select option of column scroll on previous or next in carousel.','theplus' ),
				'options' => [
					'1'  => esc_html__( 'One Column', 'theplus' ),
					'2' => esc_html__( 'All Visible Columns', 'theplus' ),
				],
			]
		);
		
		$this->add_control(
			'slider_responsive_tablet',
			[
				'label' => esc_html__( 'Responsive Tablet', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'no',
			]
		);
		$this->add_control(
			'tablet_slider_draggable',
			[
				'label' => esc_html__( 'Draggable', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'yes',
				'condition' => [
					'slider_responsive_tablet' => 'yes',
				],
			]
		);
		$this->add_control(
			'tablet_slider_infinite',
			[
				'label' => esc_html__( 'Infinite Mode', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'yes',
				'condition' => [
					'slider_responsive_tablet' => 'yes',
				],
			]
		);
		$this->add_control(
			'tablet_slider_autoplay',
			[
				'label' => esc_html__( 'Autoplay', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'yes',
				'condition' => [
					'slider_responsive_tablet' => 'yes',
				],
			]
		);
		$this->add_control(
            'tablet_autoplay_speed',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Autoplay Speed', 'theplus'),
				'size_units' => '',
				'range' => [
					'' => [
						'min' => 500,
						'max' => 10000,
						'step' => 200,
					],
				],
				'default' => [
					'unit' => '',
					'size' => 1500,
				],
				'condition' => [
					'slider_responsive_tablet' => 'yes',
					'tablet_slider_autoplay' => 'yes',
				],
            ]
        );
		$this->add_control(
			'tablet_slider_dots',
			[
				'label' => esc_html__( 'Show Dots', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'yes',
				'condition' => [
					'slider_responsive_tablet' => 'yes',
				],
			]
		);
		$this->add_control(
			'tablet_slider_arrows',
			[
				'label' => esc_html__( 'Show Arrows', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'no',
				'condition' => [
					'slider_responsive_tablet' => 'yes',
				],
			]
		);
		$this->add_control(
			'tablet_slider_rows',
			[
				'label' => esc_html__( 'Number Of Rows', 'theplus' ),
				'type'=> Controls_Manager::SELECT,
				'default' => '1',
				'options' => [
					"1" => esc_html__("1 Row", 'theplus'),
					"2" => esc_html__("2 Rows", 'theplus'),
					"3" => esc_html__("3 Rows", 'theplus'),
				],
				'condition' => [
					'slider_responsive_tablet' => 'yes',
				],
			]
		);
		$this->add_control(
			'tablet_center_mode',
			[
				'label' => esc_html__( 'Center Mode', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),
				'default' => 'no',
				'separator' => 'before',
				'condition' => [
					'slider_responsive_tablet' => 'yes',
				],
			]
		);
		$this->add_control(
            'tablet_center_padding',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Center Padding', 'theplus'),
				'size_units' => '',
				'range' => [
					'' => [
						'min' => 0,
						'max' => 500,
						'step' => 2,
					],
				],
				'default' => [
					'unit' => '',
					'size' => 0,
				],
				'condition'    => [
					'slider_responsive_tablet' => 'yes',
					'tablet_center_mode' => ['yes'],
				],
            ]
        );
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_carousel_mobile',
			[
				'label' => esc_html__( 'Mobile', 'theplus' ),
			]
		);
		$this->add_control(
			'slider_mobile_column',
			[
				'label' => esc_html__( 'Mobile Columns', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => '2',
				'options' => theplus_carousel_mobile_columns(),
			]
		);
		$this->add_control(
			'mobile_steps_slide',
			[
				'label' => esc_html__( 'Next Previous', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => '1',
				'description' => esc_html__( 'Select option of column scroll on previous or next in carousel.','theplus' ),
				'options' => [
					'1' => esc_html__( 'One Column', 'theplus' ),
					'2' => esc_html__( 'All Visible Columns', 'theplus' ),
				],
			]
		);		
		$this->add_control(
			'slider_responsive_mobile',
			[
				'label' => esc_html__( 'Responsive Mobile', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'no',
			]
		);
		$this->add_control(
			'mobile_slider_draggable',
			[
				'label' => esc_html__( 'Draggable', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'yes',
				'condition' => [
					'slider_responsive_mobile' => 'yes',
				],
			]
		);
		$this->add_control(
			'mobile_slider_infinite',
			[
				'label' => esc_html__( 'Infinite Mode', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'yes',
				'condition' => [
					'slider_responsive_mobile' => 'yes',
				],
			]
		);
		$this->add_control(
			'mobile_slider_autoplay',
			[
				'label' => esc_html__( 'Autoplay', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'yes',
				'condition' => [
					'slider_responsive_mobile' => 'yes',
				],
			]
		);
		$this->add_control(
            'mobile_autoplay_speed',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Autoplay Speed', 'theplus'),
				'size_units' => '',
				'range' => [
					'' => [
						'min' => 500,
						'max' => 10000,
						'step' => 200,
					],
				],
				'default' => [
					'unit' => '',
					'size' => 1500,
				],
				'condition' => [
					'slider_responsive_mobile' => 'yes',
					'mobile_slider_autoplay' => 'yes',
				],
            ]
        );
		$this->add_control(
			'mobile_slider_dots',
			[
				'label' => esc_html__( 'Show Dots', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'yes',
				'condition'    => [
					'slider_responsive_mobile' => 'yes',
				],
			]
		);
		$this->add_control(
			'mobile_slider_arrows',
			[
				'label' => esc_html__( 'Show Arrows', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'no',
				'condition' => [
					'slider_responsive_mobile' => 'yes',
				],
			]
		);
		$this->add_control(
			'mobile_slider_rows',
			[
				'label' => esc_html__( 'Number Of Rows', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => '1',
				'options' => [
					"1" => esc_html__("1 Row", 'theplus'),
					"2" => esc_html__("2 Rows", 'theplus'),
					"3" => esc_html__("3 Rows", 'theplus'),
				],
				'condition' => [
					'slider_responsive_mobile' => 'yes',
				],
			]
		);
		$this->add_control(
			'mobile_center_mode',
			[
				'label' => esc_html__( 'Center Mode', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'no',
				'separator' => 'before',
				'condition' => [
					'slider_responsive_mobile' => 'yes',
				],
			]
		);
		$this->add_control(
            'mobile_center_padding',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Center Padding', 'theplus'),
				'size_units' => '',
				'range' => [
					'' => [
						'min' => 0,
						'max' => 500,
						'step' => 2,
					],
				],
				'default' => [
					'unit' => '',
					'size' => 0,
				],
				'condition'    => [
					'slider_responsive_mobile' => 'yes',
					'mobile_center_mode' => ['yes'],
				],
            ]
        );
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->add_control(
			'carousel_unique_id',
			[
				'label' => esc_html__( 'Unique Carousel ID', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'separator' => 'after',
				'description' => esc_html__('Keep this blank or Setup Unique id for carousel which you can use with "Carousel Remote" widget.','theplus'),
			]
		);
		$this->end_controls_section();
		/*carousel option*/
		/*Filter Category style*/
		$this->start_controls_section(
            'section_filter_category_styling',
            [
                'label' => esc_html__('Filter Category', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'RType' => 'review',
					'layout!' => 'carousel',
					'filter_category' => 'yes',
				],
			]
        );
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'filter_category_typography',
				'selector' => '{{WRAPPER}} .pt-plus-filter-post-category .category-filters li a,{{WRAPPER}} .pt-plus-filter-post-category .category-filters.style-1 li a.all span.all_post_count',
				'separator' => 'after',
			]
		);
		$this->add_responsive_control(
			'filter_category_padding',
			[
				'label' => esc_html__( 'Inner Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .pt-plus-filter-post-category .category-filters.hover-style-1 li a span:not(.all_post_count),{{WRAPPER}} .pt-plus-filter-post-category .category-filters.hover-style-2 li a span:not(.all_post_count),{{WRAPPER}} .pt-plus-filter-post-category .category-filters.hover-style-2 li a span:not(.all_post_count)::before,{{WRAPPER}} .pt-plus-filter-post-category .category-filters.hover-style-3 li a,{{WRAPPER}} .pt-plus-filter-post-category .category-filters.hover-style-4 li a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'filter_category_marign',
			[
				'label' => esc_html__( 'Margin', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .pt-plus-filter-post-category .category-filters li' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'after',
			]
		);
		$this->add_control(
			'filters_text_color',
			[
				'label' => esc_html__( 'Filters Text Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'separator' => 'after',
				'selectors' => [
					'{{WRAPPER}} .pt-plus-filter-post-category .post-filter-data.style-4 .filters-toggle-link' => 'color: {{VALUE}}',
					'{{WRAPPER}} .pt-plus-filter-post-category .post-filter-data.style-4 .filters-toggle-link line,{{WRAPPER}} .pt-plus-filter-post-category .post-filter-data.style-4 .filters-toggle-link circle,{{WRAPPER}} .pt-plus-filter-post-category .post-filter-data.style-4 .filters-toggle-link polyline' => 'stroke: {{VALUE}}',
				],
				'condition' => [
					'filter_style' => ['style-4'],
				],
			]
		);
		$this->start_controls_tabs( 'tabs_filter_color_style' );
		$this->start_controls_tab(
			'tab_filter_category_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);
		$this->add_control(
			'filter_category_color',
			[
				'label' => esc_html__( 'Category Filter Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'separator' => 'after',
				'selectors' => [
					'{{WRAPPER}} .pt-plus-filter-post-category .category-filters li a' => 'color: {{VALUE}}',
				],				
			]
		);
		$this->add_control(
			'filter_category_4_border_color',
			[
				'label' => esc_html__( 'Border Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'separator' => 'after',
				'selectors' => [
					'{{WRAPPER}} .pt-plus-filter-post-category .category-filters.hover-style-4 li a:before' => 'border-top-color: {{VALUE}}',
				],
				'condition' => [
					'filter_hover_style' => ['style-4'],
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'filter_category_background',
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .pt-plus-filter-post-category .category-filters.hover-style-2 li a span:not(.all_post_count),{{WRAPPER}} .pt-plus-filter-post-category .category-filters.hover-style-4 li a:after',
				'separator' => 'before',
				'condition' => [
					'filter_hover_style' => ['style-2','style-4'],
				],
			]
		);
		$this->add_responsive_control(
			'filter_category_radius',
			[
				'label' => esc_html__( 'Border Radius', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .pt-plus-filter-post-category .category-filters.hover-style-2 li a span:not(.all_post_count)' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',
				'condition' => [
					'filter_hover_style' => 'style-2',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'filter_category_shadow',
				'selector' => '{{WRAPPER}} .pt-plus-filter-post-category .category-filters.hover-style-2 li a span:not(.all_post_count)',
				'separator' => 'before',
				'condition' => [
					'filter_hover_style' => 'style-2',
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_filter_category_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
		$this->add_control(
			'filter_category_hover_color',
			[
				'label' => esc_html__( 'Category Filter Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .pt-plus-filter-post-category .category-filters:not(.hover-style-2) li a:hover,{{WRAPPER}}  .pt-plus-filter-post-category .category-filters:not(.hover-style-2) li a:focus,{{WRAPPER}}  .pt-plus-filter-post-category .category-filters:not(.hover-style-2) li a.active,{{WRAPPER}} .pt-plus-filter-post-category .category-filters.hover-style-2 li a span:not(.all_post_count)::before' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'filter_category_hover_background',
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .pt-plus-filter-post-category .category-filters.hover-style-2 li a span:not(.all_post_count)::before',
				'separator' => 'before',
				'condition' => [
					'filter_hover_style' => 'style-2',
				],				
			]
		);
		$this->add_responsive_control(
			'filter_category_hover_radius',
			[
				'label' => esc_html__( 'Border Radius', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .pt-plus-filter-post-category .category-filters.hover-style-2 li a span:not(.all_post_count)::before' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',
				'condition' => [
					'filter_hover_style' => 'style-2',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'filter_category_hover_shadow',
				'selector' => '{{WRAPPER}} .pt-plus-filter-post-category .category-filters.hover-style-2 li a span:not(.all_post_count)::before',
				'separator' => 'before',
				'condition' => [
					'filter_hover_style' => 'style-2',
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->add_control(
			'filter_border_hover_color',
			[
				'label' => esc_html__( 'Hover Border Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pt-plus-filter-post-category .category-filters.hover-style-1 li a::after' => 'background: {{VALUE}};',
				],
				'separator' => 'before',
				'condition' => [
					'filter_hover_style' => 'style-1',
				],
			]
		);
		$this->add_control(
			'count_filter_category_options',
			[
				'label' => esc_html__( 'Count Filter Category', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_control(
			'category_count_color',
			[
				'label' => esc_html__( 'Category Count Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .pt-plus-filter-post-category .category-filters li a span.all_post_count' => 'color: {{VALUE}}',
				],
			]
		);		
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'category_count_background',
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .pt-plus-filter-post-category .category-filters.style-1 li a.all span.all_post_count',
				'condition' => [
					'filter_style' => ['style-1'],
				],
			]
		);
		$this->add_control(
			'category_count_bg_color',
			[
				'label' => esc_html__( 'Count Background Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .pt-plus-filter-post-category .category-filters.style-3 a span.all_post_count' => 'background: {{VALUE}}',
					'{{WRAPPER}} .pt-plus-filter-post-category .category-filters.style-3 a span.all_post_count:before' => 'border-top-color: {{VALUE}}',
				],
				'condition' => [
					'filter_style' => ['style-3'],
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'filter_category_count_shadow',
				'selector' => '{{WRAPPER}} .pt-plus-filter-post-category .category-filters.style-1 li a.all span.all_post_count',
				'separator' => 'before',
				'condition' => [
					'filter_style' => ['style-1'],
				],
			]
		);
		$this->end_controls_section();
		/*Filter Category style*/
		/*Facebook Options Start*/
		$this->start_controls_section(
			'Fb_optn_stl_section',
			[
				'label' => esc_html__( 'Facebook', 'theplus' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'RType' => 'review',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'FbnameTypo',
				'label' => esc_html__( 'Username Typography', 'theplus' ),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector' => '{{WRAPPER}} .tp-social-reviews .facebook .tp-SR-username a',
			]
		);
        $this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'FbMsgTypo',
				'label' => esc_html__( 'Message Typography', 'theplus' ),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector' => '{{WRAPPER}} .tp-social-reviews .facebook .tp-SR-content',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'FbPostOnTypo',
				'label' => esc_html__( 'Post On Text Typography', 'theplus' ),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector' => '{{WRAPPER}} .tp-social-reviews .facebook .tp-SR-logotext',
				'condition' => [
					'style!' => 'style-3',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'FbTimeTypo',
				'label' => esc_html__( 'Time Typography', 'theplus' ),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector' => '{{WRAPPER}} .tp-social-reviews .facebook .tp-SR-time',
			]
		);
		$this->start_controls_tabs( 'fb_color_style' );
		$this->start_controls_tab(
			'fb_optn_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);
		$this->add_control(
			'FbnameCr',
			[
				'label' => esc_html__( 'Username Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .grid-item.facebook .tp-SR-username a' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'FbMassageCr',
			[
				'label' => esc_html__( 'Message Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .grid-item.facebook .tp-SR-content' => 'color: {{VALUE}}',	
				],
			]
		);
		$this->add_control(
			'FbPostONCr',
			[
				'label' => esc_html__( 'Post On Text Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .grid-item.facebook .tp-SR-logotext .tp-newline:nth-child(n)' => 'color: {{VALUE}}',					
				],
				'condition' => [
					'style!' => 'style-3',
				],
			]
		);
		$this->add_control(
			'FbTimeCr',
			[
				'label' => esc_html__( 'Time Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .grid-item.facebook .tp-SR-time' => 'color: {{VALUE}}',					
				],
			]
		);
		$this->add_control(
			'BoxFblable',
			[
				'label' => esc_html__( 'Box Background Options', 'theplus' ),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			'FbBpadding',
			[
				'label' => esc_html__( 'Box Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'default' => [
					'px' => [
					'top' => '',
					'right' => '',
					'bottom' => '',
					'left' => '',					
					],
				],
				'selectors' => [
					'{{WRAPPER}} .tp-social-reviews .facebook .tp-review' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'FbNBg',
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .tp-social-reviews .facebook .tp-review',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'FbNB',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-social-reviews .facebook .tp-review',
			]
		);
		$this->add_responsive_control(
			'FbCRs',
			[
				'label' => esc_html__( 'Border Radius', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .tp-social-reviews .facebook .tp-review' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'FbBs',
				'selector' => '{{WRAPPER}} .tp-social-reviews .facebook .tp-review',
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'fb_optn_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
		$this->add_control(
			'FbHnameCr',
			[
				'label' => esc_html__( 'Username Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .tp-social-reviews .facebook:hover .tp-SR-username a' => 'color: {{VALUE}}',					
				],
			]
		);
		$this->add_control(
			'FbHMassageCr',
			[
				'label' => esc_html__( 'Message Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .tp-social-reviews .facebook:hover .tp-SR-content' => 'color: {{VALUE}}',					
				],
			]
		);
		$this->add_control(
			'FbHPostONCr',
			[
				'label' => esc_html__( 'Post On Text Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .tp-social-reviews .facebook:hover .tp-SR-logotext .tp-newline:nth-child(n)' => 'color: {{VALUE}}',					
				],
				'condition'   => [
					'style!' => 'style-3',
				],
			]
		);
		$this->add_control(
			'FbHTimeCr',
			[
				'label' => esc_html__( 'Time Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .tp-social-reviews .facebook:hover .tp-SR-time' => 'color: {{VALUE}}',					
				],
			]
		);
		$this->add_responsive_control(
			'FbBHpadding',
			[
				'label' => esc_html__( 'Box Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'default' => [
					'px' => [
					'top' => '',
					'right' => '',
					'bottom' => '',
					'left' => '',					
					],
				],
				'selectors' => [
					'{{WRAPPER}} .grid-item.facebook:hover .tp-review' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'FbHBg',
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .tp-social-reviews .facebook:hover .tp-review',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'FbHB',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-social-reviews .facebook:hover .tp-review',
			]
		);
		$this->add_responsive_control(
			'FbBHRs',
			[
				'label' => esc_html__( 'Border Radius', 'theplus' ),
				'type'  => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .tp-social-reviews .facebook:hover .tp-review' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'FbHBs',
				'selector' => '{{WRAPPER}} .tp-social-reviews .facebook:hover .tp-review',
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->add_responsive_control(
			'FbPRs',
			[
				'label' => esc_html__( 'Profile Radius', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .tp-social-reviews .facebook .tp-SR-profile' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			'FbHpd',
			[
				'label' => esc_html__( 'Header Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'default' => [
					'px' => [
					'top' => '',
					'right' => '',
					'bottom' => '',
					'left' => '',					
					],
				],
				'selectors' => [
					'{{WRAPPER}} .tp-social-reviews .facebook .tp-SR-header' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			'FbBpd',
			[
				'label' => esc_html__( 'Content Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'default' => [
					'px' => [
					'top' => '',
					'right' => '',
					'bottom' => '',
					'left' => '',					
					],
				],
				'selectors' => [
					'{{WRAPPER}} .tp-social-reviews .facebook .tp-SR-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			'FbFpd',
			[
				'label' => esc_html__( 'Footer Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'default' => [
					'px' => [
					'top' => '',
					'right' => '',
					'bottom' => '',
					'left' => '',					
					],
				],
				'selectors' => [
					'{{WRAPPER}} .tp-social-reviews .facebook .tp-SR-bottom' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);
		$this->end_controls_section();
		/*Facebook Options End*/
		/*Google Options Start*/
		$this->start_controls_section(
			'google_optn_stl_section',
			[
				'label' => esc_html__( 'Google', 'theplus' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'RType' => 'review',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'GnameTypo',
				'label' => esc_html__( 'Username Typography', 'theplus' ),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector' => '{{WRAPPER}} .tp-social-reviews .google .tp-SR-username a',
			]
		);
        $this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'GMsgTypo',
				'label' => esc_html__( 'Message Typography', 'theplus' ),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector' => '{{WRAPPER}} .tp-social-reviews .google .tp-SR-content',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'GPostOnTypo',
				'label' => esc_html__( 'Post On Text Typography', 'theplus' ),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector' => '{{WRAPPER}} .tp-social-reviews .google .tp-SR-logotext',
				'condition' => [
					'style!' => 'style-3',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'GTimeTypo',
				'label' => esc_html__( 'Time Typography', 'theplus' ),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector' => '{{WRAPPER}} .tp-social-reviews .google .tp-SR-time',
			]
		);
		$this->start_controls_tabs( 'google_color_style' );
		$this->start_controls_tab(
			'google_optn_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);
		$this->add_control(
			'GNnameCr',
			[
				'label' => esc_html__( 'Username Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .tp-social-reviews .google .tp-SR-username a' => 'color: {{VALUE}}',					
				],
			]
		);
		$this->add_control(
			'GNMassageCr',
			[
				'label' => esc_html__( 'Message Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .tp-social-reviews .google .tp-SR-content' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'GNPostONCr',
			[
				'label' => esc_html__( 'Post On Text Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .tp-social-reviews .google .tp-SR-logotext' => 'color: {{VALUE}}',
				],
				'condition' => [
					'style!' => 'style-3',
				],
			]
		);
		$this->add_control(
			'GNTimeCr',
			[
				'label' => esc_html__( 'Time Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .tp-social-reviews .google .tp-SR-time' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'BoxGGlable',
			[
				'label' => esc_html__( 'Box Background Options', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			'GNBpadding',
			[
				'label' => esc_html__( 'Box Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'default' => [
					'px' => [
					'top' => '',
					'right' => '',
					'bottom' => '',
					'left' => '',					
					],
				],
				'selectors' => [
					'{{WRAPPER}} .tp-social-reviews .google .tp-review' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);	
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'GNBg',
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .tp-social-reviews .google .tp-review',
				'condition' => [
					'RType' => 'review',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'GNBr',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-social-reviews .google .tp-review',
			]
		);
		$this->add_responsive_control(
			'GNRs',
			[
				'label' => esc_html__( 'Border Radius', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .tp-social-reviews .google .tp-review' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'GNBs',
				'selector' => '{{WRAPPER}} .tp-social-reviews .google .tp-review',
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'google_optn_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
		$this->add_control(
			'GHnameCr',
			[
				'label' => esc_html__( 'Username Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .tp-social-reviews .google:hover .tp-SR-username a' => 'color: {{VALUE}}',					
				],
			]
		);
		$this->add_control(
			'GHMassageCr',
			[
				'label' => esc_html__( 'Message Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .tp-social-reviews .google:hover .tp-SR-content' => 'color: {{VALUE}}',					
				],
			]
		);
		$this->add_control(
			'GHPostONCr',
			[
				'label' => esc_html__( 'Post On Text Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .tp-social-reviews .google:hover .tp-SR-logotext' => 'color: {{VALUE}}',					
				],
				'condition'  => [
					'style!' => 'style-3',
				],
			]
		);
		$this->add_control(
			'GHTimeCr',
			[
				'label' => esc_html__( 'Time Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .tp-social-reviews .google:hover .tp-SR-time' => 'color: {{VALUE}}',					
				],
			]
		);
		$this->add_responsive_control(
			'GHBpadding',
			[
				'label' => esc_html__( 'Box Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'default' => [
					'px' => [
					'top' => '',
					'right' => '',
					'bottom' => '',
					'left' => '',					
					],
				],
				'selectors' => [
					'{{WRAPPER}} .tp-social-reviews .google:hover .tp-review' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);	
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'GHBg',
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .tp-social-reviews .google:hover .tp-review',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'GHBr',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-social-reviews .google:hover .tp-review',
			]
		);
		$this->add_responsive_control(
			'GHRs',
			[
				'label' => esc_html__( 'Border Radius', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .tp-social-reviews .google:hover .tp-review' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'GHBs',
				'selector' => '{{WRAPPER}} .tp-social-reviews .google:hover .tp-review',
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->add_responsive_control(
			'GPRs',
			[
				'label' => esc_html__( 'Profile Radius', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .tp-social-reviews .google .tp-SR-profile' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			'GHpd',
			[
				'label' => esc_html__( 'Header Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'default' => [
					'px' => [
					'top' => '',
					'right' => '',
					'bottom' => '',
					'left' => '',					
					],
				],
				'selectors' => [
					'{{WRAPPER}} .tp-social-reviews .grid-item.google .tp-SR-header' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			'GBpd',
			[
				'label' => esc_html__( 'Content Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'default' => [
					'px' => [
					'top' => '',
					'right' => '',
					'bottom' => '',
					'left' => '',					
					],
				],
				'selectors' => [
					'{{WRAPPER}} .tp-social-reviews .grid-item.google .tp-SR-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			'GFpd',
			[
				'label' => esc_html__( 'Footer Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'default' => [
					'px' => [
					'top' => '',
					'right' => '',
					'bottom' => '',
					'left' => '',					
					],
				],
				'selectors' => [
					'{{WRAPPER}} .tp-social-reviews .google .tp-SR-bottom' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);
		$this->end_controls_section();
		/*Google Options End*/
		/*manual Options Start*/
		$this->start_controls_section(
			'cstm_optn_stl_section',
			[
				'label' => esc_html__( 'Manual', 'theplus' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'RType' => 'review',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'CnameTypo',
				'label' => esc_html__( 'Username Typography', 'theplus' ),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector' => '{{WRAPPER}} .tp-social-reviews .custom .tp-SR-username a',
			]
		);
        $this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'CMsgTypo',
				'label' => esc_html__( 'Message Typography', 'theplus' ),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector' => '{{WRAPPER}} .tp-social-reviews .custom .tp-SR-content',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'CPostOnTypo',
				'label' => esc_html__( 'Post On Text Typography', 'theplus' ),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector' => '{{WRAPPER}} .tp-social-reviews .custom .tp-SR-logotext',
				'condition' => [
					'style!' => 'style-3',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'CTimeTypo',
				'label' => esc_html__( 'Time Typography', 'theplus' ),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector' => '{{WRAPPER}} .tp-social-reviews .custom .tp-SR-time',
			]
		);
		$this->start_controls_tabs( 'cstm_color_style' );
		$this->start_controls_tab(
			'cstm_optn_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);
		$this->add_control(
			'CnameCr',
			[
				'label' => esc_html__( 'Username Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .tp-social-reviews .custom .tp-SR-username a' => 'color: {{VALUE}}',					
				],
			]
		);
		$this->add_control(
			'CMassageCr',
			[
				'label' => esc_html__( 'Message Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .tp-social-reviews .custom .tp-SR-content' => 'color: {{VALUE}}',					
				],
			]
		);
		$this->add_control(
			'CPostONCr',
			[
				'label' => esc_html__( 'Post On Text Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .tp-social-reviews .custom .tp-SR-logotext' => 'color: {{VALUE}}',					
				],
				'condition' => [
					'style!' => 'style-3',
				],
			]
		);
		$this->add_control(
			'CTimeCr',
			[
				'label' => esc_html__( 'Time Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .tp-social-reviews .custom .tp-SR-time' => 'color: {{VALUE}}',	
				],
			]
		);
		$this->add_control(
			'content_CBBg_opt',
			[
				'label' => esc_html__( 'Bottom Background', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'style' => 'style-3',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'CBBg',
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .tp-social-reviews .custom .tp-SR-header',
				'condition' => [
					'style' => 'style-3',
				],
			]
		);
		$this->add_control(
			'BoxCustomlable',
			[
				'label' => esc_html__( 'Box Background Options', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			'CusNBpadding',
			[
				'label' => esc_html__( 'Box Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'default' => [
					'px' => [
					'top' => '',
					'right' => '',
					'bottom' => '',
					'left' => '',					
					],
				],
				'selectors' => [
					'{{WRAPPER}} .tp-social-reviews .custom .tp-review' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);	
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'CNBg',
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .tp-social-reviews .custom .tp-review',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'CusNBr',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-social-reviews .custom .tp-review',
			]
		);
		$this->add_responsive_control(
			'CusNCRs',
			[
				'label' => esc_html__( 'Border Radius', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-social-reviews .custom .tp-review' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'CusNBs',
				'selector' => '{{WRAPPER}} .tp-social-reviews .custom .tp-review',
			]
		);	
		$this->end_controls_tab();
		$this->start_controls_tab(
			'cstm_optn_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
		$this->add_control(
			'CHnameCr',
			[
				'label' => esc_html__( 'Username Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .tp-social-reviews .custom:hover .tp-SR-username a' => 'color: {{VALUE}}',					
				],
			]
		);
		$this->add_control(
			'CHMassageCr',
			[
				'label' => esc_html__( 'Message Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .tp-social-reviews .custom:hover .tp-SR-content' => 'color: {{VALUE}}',					
				],
			]
		);
		$this->add_control(
			'CHPostONCr',
			[
				'label' => esc_html__( 'Post On Text Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .tp-social-reviews .custom:hover .tp-SR-logotext' => 'color: {{VALUE}}',					
				],
				'condition'   => [
					'style!' => 'style-3',
				],
			]
		);
		$this->add_control(
			'CHTimeCr',
			[
				'label' => esc_html__( 'Time Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .tp-social-reviews .custom:hover .tp-SR-time' => 'color: {{VALUE}}',					
				],
			]
		);
		$this->add_control(
			'content_CHBBg_opt',
			[
				'label' => esc_html__( 'Bottom Background', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'style' => 'style-3',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'CHBBg',
				'types' => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .tp-social-reviews .custom:hover .tp-SR-header',
				'condition' => [
					'style' => 'style-3',
				],
			]
		);
		$this->add_responsive_control(
			'CusHBpadding',
			[
				'label' => esc_html__( 'Box Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'default' => [
					'px' => [
					'top' => '',
					'right' => '',
					'bottom' => '',
					'left' => '',					
					],
				],
				'selectors' => [
					'{{WRAPPER}} .tp-social-reviews .custom:hover .tp-review' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);	
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'CHBg',
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .tp-social-reviews .custom:hover .tp-review',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'CusHBr',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-social-reviews .custom:hover .tp-review',
			]
		);
		$this->add_responsive_control(
			'CusHCRs',
			[
				'label' => esc_html__( 'Border Radius', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .tp-social-reviews .custom:hover .tp-review' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'CusHBs',
				'selector' => '{{WRAPPER}} .tp-social-reviews .custom:hover .tp-review',
			]
		);	
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->add_responsive_control(
			'CusPRs',
			[
				'label' => esc_html__( 'Profile Radius', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-social-reviews .custom .tp-SR-profile' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			'CusHpd',
			[
				'label' => esc_html__( 'Header Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'default' => [
					'px' => [
					'top' => '',
					'right' => '',
					'bottom' => '',
					'left' => '',					
					],
				],
				'selectors' => [
					'{{WRAPPER}} .tp-social-reviews .custom .tp-SR-header' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			'CusBpd',
			[
				'label' => esc_html__( 'Content Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'default' => [
					'px' => [
					'top' => '',
					'right' => '',
					'bottom' => '',
					'left' => '',					
					],
				],
				'selectors' => [
					'{{WRAPPER}} .tp-social-reviews .custom .tp-SR-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			'CusFpd',
			[
				'label' => esc_html__( 'Footer Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'default' => [
					'px' => [
					'top' => '',
					'right' => '',
					'bottom' => '',
					'left' => '',					
					],
				],
				'selectors' => [
					'{{WRAPPER}} .tp-social-reviews .custom .tp-SR-bottom' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);
		$this->end_controls_section();
		/*Custom Options End*/
		/*Main Area Options Start*/
		$this->start_controls_section(
			'bg_optn_stl_section',
			[
				'label' => esc_html__( 'Main Area', 'theplus' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'RType' => 'beach',
				],
			]
		);
		$this->add_responsive_control(
            'Bboxwidth',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Box Width', 'theplus'),
				'size_units' => ['%'],
				'range' => [
					'%' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => '%',
					'size' => 100,
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .tp-social-reviews .tp-review' => 'width: {{SIZE}}{{UNIT}};',
				],
				'condition'   => [
					'Bstyle' => ['style-2','style-3'],
				],
            ]
        );
		$this->add_control(
			'alignmentbeach',
			[
				'label' => esc_html__( 'Alignment', 'theplus' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => esc_html__( 'Left', 'theplus' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'theplus' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'theplus' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .social-RB-style-2 .tp-batch-top,{{WRAPPER}} .social-RB-style-2 .tp-batch-rating,{{WRAPPER}} .social-RB-style-2 .tp-batch-contant' => 'text-align: {{VALUE}}',
				],
				'default' => 'center',
				'toggle' => true,
				'label_widget' => false,
				'condition'   => [
					'Bstyle' => ['style-2'],
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'BTypo',
				'label' => esc_html__( 'Title Typography', 'theplus' ),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector' => '{{WRAPPER}} .social-RB-style-1.tp-review .tp-batch-user,{{WRAPPER}} .social-RB-style-2.tp-review .tp-batch-user,{{WRAPPER}} .social-RB-style-3.tp-review .tp-batch-user',
				'condition'   => [
					'Bstyle' => ['style-2'],
				],	
			]
		);
		$this->add_control(
			'TCr',
			[
				'label' => esc_html__( 'Title Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .social-RB-style-1.tp-review .tp-batch-user,{{WRAPPER}} .social-RB-style-2.tp-review .tp-batch-user,{{WRAPPER}} .social-RB-style-3.tp-review .tp-batch-user' => 'color: {{VALUE}}',					
				],
				'condition'   => [
					'Bstyle' => ['style-2'],
				],
				'separator' => 'after',	
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'BRbyCr',
				'label' => esc_html__( 'Extra Title Typography', 'theplus' ),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector' => '{{WRAPPER}} .tp-review .tp-batch-total',
			]
		);
		$this->add_control(
			'TRbyCr',
			[
				'label' => esc_html__( 'Extra Title Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .tp-review .tp-batch-total' => 'color: {{VALUE}}',					
				],
			]
		);
		$this->start_controls_tabs( 'bdg_imgR_style' );
		$this->start_controls_tab(
			'bdg_Iimg',
			[
				'label' => esc_html__( 'Images', 'theplus' ),
				'condition' => [
					'Bstyle' => ['style-1'],
				],
			]
		);
		$this->add_responsive_control(
            'Imgsize',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Size', 'theplus'),
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => '',
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .tp-social-reviews .social-RB-style-1 .tp-batch-Img' => 'width: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'Bstyle' => ['style-1'],
				],
            ]
        );
        $this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'ImgBorder',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-social-reviews .social-RB-style-1 .tp-batch-Img',
				'condition' => [
					'Bstyle' => ['style-1'],
				],	
			]
		);	
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'ImgBS',
				'selector' => '{{WRAPPER}} .tp-social-reviews .social-RB-style-1 .tp-batch-Img',
				'condition' => [
					'Bstyle' => ['style-1'],
				],				
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'bdg_Icicn',
			[
				'label' => esc_html__( 'Icon', 'theplus' ),
				'condition' => [
					'Bstyle' => ['style-2'],
				],
			]
		);
		$this->add_responsive_control(
			'BSISize',
			[
				'label' => esc_html__( 'Social Icon Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .tp-social-reviews .social-RB-style-2 .tp-SR-logo' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'Bstyle' => ['style-2'],
				],
			]
		);
		$this->add_responsive_control(
            'BSITopB',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Social Icon Top-Bottom', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => -100,
						'max' => 100,
						'step' => 1,
					],
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .tp-social-reviews .social-RB-style-2 .tp-SR-logo' => 'top: {{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'Bstyle' => ['style-2'],
				],
            ]
        );
		$this->end_controls_tab();
		$this->start_controls_tab(
			'bdg_AavgR',
			[
				'label' => esc_html__( 'Average', 'theplus' ),
				'condition' => [
					'Bstyle' => ['style-3'],
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'AvrageTxtTypo',
				'label' => esc_html__( 'Text Typography', 'theplus' ),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector' => '{{WRAPPER}} .tp-social-reviews .tp-batch-number span',
				'condition' => [
					'Bstyle' => ['style-3'],
				],
			]
		);
		$this->add_control(
			'AvrageTxtCr',
			[
				'label' => esc_html__( 'Text Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .tp-social-reviews .tp-batch-number span' => 'color: {{VALUE}}',					
				],
				'condition' => [
					'Bstyle' => ['style-3'],
				],
			]
		);
		$this->add_control(
			'AvrageCr',
			[
				'label' => esc_html__( 'Background Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .tp-social-reviews .tp-batch-number span' => 'background-color: {{VALUE}}',					
				],
				'condition' => [
					'Bstyle' => ['style-3'],
				],
			]
		);
		$this->add_responsive_control(
			'AvragePadding',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'default' => [
					'px' => [
					'top' => '',
					'right' => '',
					'bottom' => '',
					'left' => '',					
					],
				],
				'selectors' => [
					'{{WRAPPER}} .tp-social-reviews .tp-batch-number span' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'Bstyle' => ['style-3'],
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'bdg_Rrating',
			[
				'label' => esc_html__( 'Rating', 'theplus' ),
			]
		);
		$this->add_control(
			'BDyIcon',
			[
				'label' => esc_html__( 'Icon', 'theplus' ),
				'type' => Controls_Manager::ICONS,
				'default' => [
					'value' => 'fas fa-star',
					'library' => 'solid',
				],
			]
		);
		$this->add_control(
			'BstarCr',
			[
				'label' => esc_html__( 'Icon Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .tp-social-reviews .tp-batch-start' => 'color: {{VALUE}}',					
				],
			]
		);
		$this->add_responsive_control(
            'BstarIsize',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Icon Size', 'theplus'),
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => '',
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .tp-social-reviews .tp-batch-start' => 'font-size: {{SIZE}}{{UNIT}}',			
				],
            ]
        );
        $this->add_responsive_control(
            'BstarIwidth',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Icon Space', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 100,
						'step' => 1,
					],
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .tp-social-reviews .b-star' => 'width: {{SIZE}}{{UNIT}};',
				],
            ]
        );
         $this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'BstarBgCr',
				'types'  => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .tp-social-reviews .social-RB-style-2 .tp-batch-start',
				'condition' => [
					'Bstyle' => ['style-2'],
				],
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'BstarBr',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-social-reviews .social-RB-style-2 .tp-batch-start',
				'separator' => 'before',
				'condition' => [
					'Bstyle' => ['style-2'],
				],
			]
		);
		$this->add_responsive_control(
			'BstarRsBr',
			[
				'label' => esc_html__( 'Border Radius', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .tp-social-reviews .social-RB-style-2 .tp-batch-start' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',				
				],
				'condition' => [
					'Bstyle' => ['style-2'],
				],	
			]
		);
		$this->add_responsive_control(
			'BiconPadd',
			[
				'label' => esc_html__( 'Icon Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .tp-social-reviews .social-RB-style-2 .tp-batch-start' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'Bstyle' => ['style-2'],
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		
		$this->add_control(
			'bdg_InnerContent_opt',
			[
				'label' => esc_html__( 'Badge Inner Content Options', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			'bdg_InnerContent_Padd',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .tp-social-reviews .tp-review .tp-batch-contant' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->start_controls_tabs( 'bdg_InnerContent_style' );
		$this->start_controls_tab(
			'bdg_InnerContent_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
				'condition' => [
					'Bstyle' => ['style-2','style-3'],
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'bdg_InnerContent_BGN',
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .tp-social-reviews .social-RB-style-2.tp-review .tp-batch-contant,{{WRAPPER}} .tp-social-reviews .social-RB-style-3.tp-review .tp-batch-top',				
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'bdg_InnerContent_BrN',
				'label' => esc_html__( 'Border', 'theplus' ),				
				'selector' => '{{WRAPPER}} .tp-social-reviews .social-RB-style-2.tp-review .tp-batch-contant,{{WRAPPER}} .tp-social-reviews .social-RB-style-3.tp-review .tp-batch-top',
				'condition' => [
					'Bstyle' => ['style-2','style-3'],
				],
			]
		);
		$this->add_responsive_control(
			'bdg_InnerContent_BDRsN',
			[
				'label' => esc_html__( 'Border Radius', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-social-reviews .social-RB-style-2.tp-review .tp-batch-contant' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .tp-social-reviews .social-RB-style-3.tp-review .tp-batch-top' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'Bstyle' => ['style-2','style-3'],
				],	
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'bdg_InnerContent_BswN',
				'selector' => '{{WRAPPER}} .tp-social-reviews .social-RB-style-2.tp-review .tp-batch-contant,{{WRAPPER}} .tp-social-reviews .social-RB-style-3.tp-review .tp-batch-top',
				'condition' => [
					'Bstyle' => ['style-2','style-3'],
				],			
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'bdg_InnerContent_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
				'condition' => [
					'Bstyle' => ['style-2','style-3'],
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'bdg_InnerContent_BGH',
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .tp-social-reviews:hover .social-RB-style-2.tp-review .tp-batch-contant,{{WRAPPER}} .tp-social-reviews:hover .social-RB-style-3.tp-review .tp-batch-top',
				'condition' => [
					'Bstyle' => ['style-2','style-3'],
				],
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'bdg_InnerContent_BrH',
				'label' => esc_html__( 'Border', 'theplus' ),				
				'selector' => '{{WRAPPER}} .tp-social-reviews:hover .social-RB-style-2.tp-review .tp-batch-contant,{{WRAPPER}} .tp-social-reviews:hover .social-RB-style-3.tp-review .tp-batch-top',
				'condition' => [
					'Bstyle' => ['style-2','style-3'],
				],
			]
		);
		$this->add_responsive_control(
			'bdg_InnerContent_BDRsH',
			[
				'label' => esc_html__( 'Border Radius', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-social-reviews:hover .social-RB-style-2.tp-review .tp-batch-contant' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .tp-social-reviews:hover .social-RB-style-3.tp-review .tp-batch-top' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'bdg_InnerContent_BswH',
				'selector' => '{{WRAPPER}} .tp-social-reviews:hover .social-RB-style-2.tp-review .tp-batch-contant,{{WRAPPER}} .tp-social-reviews:hover .social-RB-style-3.tp-review .tp-batch-top',				
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();		
        $this->add_control(
			'bdg_Bgoutr_opt',
			[
				'label' => esc_html__( 'Badge Outer Background Options', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',				
			]
		);
		$this->add_responsive_control(
			'bdg_Bgoutr_Padd',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .tp-social-reviews .tp-review' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->start_controls_tabs( 'bdg_Bgoutr_style' );
		$this->start_controls_tab(
			'bdg_Bgoutr_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'bdg_Bgoutr_BGN',
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .tp-social-reviews .tp-review',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'bdg_Bgoutr_BrN',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-social-reviews .tp-review',
			]
		);
		$this->add_responsive_control(
			'bdg_Bgoutr_BDRsN',
			[
				'label' => esc_html__( 'Border Radius', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .tp-social-reviews .tp-review' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',				
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'bdg_Bgoutr_BswN',
				'selector' => '{{WRAPPER}} .tp-social-reviews .tp-review',
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'bdg_Bgoutr_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'bdg_Bgoutr_BGH',
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .tp-social-reviews:hover .tp-review',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'bdg_Bgoutr_BrH',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-social-reviews:hover .tp-review',
			]
		);
		$this->add_responsive_control(
			'bdg_Bgoutr_BDRsH',
			[
				'label' => esc_html__( 'Border Radius', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-social-reviews:hover .tp-review' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',				
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'bdg_Bgoutr_BswH',
				'selector' => '{{WRAPPER}} .tp-social-reviews:hover .tp-review',
				'condition' => [
					'Bstyle' => ['style-1','style-2','style-3'],
				],				
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		/*Main Area Options Start*/
		/*Sub Area Options Start*/
		$this->start_controls_section(
			'rcmnd_optn_stl_section',
			[
				'label' => esc_html__( 'Sub Area', 'theplus' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'Bstyle' => 'style-1',
				],
			]
		);
		$this->add_control(
			'RBalignment',
			[
				'label' => esc_html__( 'Alignment', 'theplus' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'space-around' => [
						'title' => esc_html__( 'Space Around', 'theplus' ),
						'icon' => 'eicon-text-align-justify',
					],
					'flex-start' => [
						'title' => esc_html__( 'Left', 'theplus' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'theplus' ),
						'icon' => 'eicon-text-align-center',
					],
					'flex-end' => [
						'title' => esc_html__( 'Right', 'theplus' ),
						'icon' => 'eicon-text-align-right',
					],					
					'space-between' => [
						'title' => esc_html__( 'Space Between', 'theplus' ),
						'icon' => 'eicon-text-align-justify',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .tp-social-reviews .tp-batch-recommend' => 'justify-content: {{VALUE}}',
				],
				'default' => 'center',
				'toggle' => true,
				'label_widget' => false,
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'RBTypo',
				'label' => esc_html__( 'Text Typography', 'theplus' ),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector' => '{{WRAPPER}} .tp-batch-recommend .tp-batch-recommend-text',
			]
		);
        $this->add_control(
			'RTCr',
			[
				'label' => esc_html__( 'Text Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .tp-batch-recommend' => 'color: {{VALUE}}',					
				],
			]
		);
		$this->add_control(
			'RBCr',
			[
				'label' => esc_html__( 'Background Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .tp-social-reviews .tp-batch-recommend' => 'background-color: {{VALUE}}',					
				],
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'RBr',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-batch-recommend',
			]
		);
		$this->add_responsive_control(
			'RRs',
			[
				'label' => esc_html__( 'Border Radius', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .tp-batch-recommend' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->start_controls_tabs( 'rcmnd_btn_style' );
		$this->start_controls_tab(
			'rcmnd_optn_btnone',
			[
				'label' => esc_html__( 'First Button', 'theplus' ),
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'BtnOtypo',
				'label' => esc_html__( 'Text Typography', 'theplus' ),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector' => '{{WRAPPER}} .tp-social-reviews .batch-btn-yes',
			]
		);
		$this->add_control(
			'BtnOCr',
			[
				'label' => esc_html__( 'Text Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .tp-social-reviews .batch-btn-yes' => 'color: {{VALUE}}',					
				],
			]
		);
		$this->add_control(
			'BtnOBg',
			[
				'label' => esc_html__( 'Background Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .tp-social-reviews .batch-btn-yes' => 'background-color: {{VALUE}}',					
				],
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'BtnOB',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-social-reviews .batch-btn-yes',
			]
		);
		$this->add_responsive_control(
			'BtnOBRs',
			[
				'label' => esc_html__( 'Border Radius', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .tp-social-reviews .batch-btn-yes' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'BtnOMargin',
			[
				'label' => esc_html__( 'Margin', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .tp-social-reviews .tp-batch-recommend a.batch-btn-yes' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'rcmnd_optn_btntwo',
			[
				'label' => esc_html__( 'Second Button', 'theplus' ),
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'BtnTtypo',
				'label' => esc_html__( 'Text Typography', 'theplus' ),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector' => '{{WRAPPER}} .tp-social-reviews .batch-btn-no',
			]
		);
		$this->add_control(
			'BtnTCr',
			[
				'label' => esc_html__( 'Text Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .tp-social-reviews .batch-btn-no' => 'color: {{VALUE}}',					
				],
			]
		);
		$this->add_control(
			'BtnTBg',
			[
				'label' => esc_html__( 'Background Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .tp-social-reviews .batch-btn-no' => 'background-color: {{VALUE}}',					
				],
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'BtnTB',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-social-reviews .batch-btn-no',				
			]
		);
		$this->add_responsive_control(
			'BtnTBRs',
			[
				'label' => esc_html__( 'Border Radius', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .tp-social-reviews .batch-btn-no' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'BtnTMargin',
			[
				'label' => esc_html__( 'Margin', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .tp-social-reviews .tp-batch-recommend .batch-btn-no' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',				
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		/*Sub Area Options End*/
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$WidgetUID = $this->get_unique_selector();
		$uid_socirw = uniqid("tp-socirw");
		$WidgetId = $this->get_id();
		$layout = !empty($settings['layout']) ? $settings['layout'] : 'grid';
	    $RType = !empty($settings['RType']) ? $settings['RType'] : 'review';
	    $style = !empty($settings['style']) ? $settings['style'] : 'style-1';
	    $Repeater = !empty($settings['Rreviews']) ? $settings['Rreviews'] : [];
		$RefreshTime = !empty($settings['TimeFrq']) ? $settings['TimeFrq'] : '3600';
	    $TimeFrq = array( 'TimeFrq' => $RefreshTime );
	    $OverlayImage = !empty($settings['OverlayImage'] == 'yes') ? 'overlayimage' : '';
	    $UserFooter = !empty($settings['layoutstyle2']) ? $settings['layoutstyle2'] : 'layout-1';
	    $FeedId = !empty($settings['FeedId']) ? preg_split("/\,/", $settings['FeedId']) : [];
		$CategoryWF = !empty($settings['filter_category']) ? $settings['filter_category'] : 'no';
	    $Postdisplay = !empty($settings['display_posts']) ? (int)$settings['display_posts'] : '';
		$postLodop = !empty($settings['post_extra_option']) ? $settings['post_extra_option'] : '';
		$postview = !empty($settings['load_more_post']) ? (int)$settings['load_more_post'] : '';
		$loadbtnText = !empty($settings['load_more_btn_text']) ? $settings['load_more_btn_text'] : '';
		$loadingtxt = !empty($settings['tp_loading_text']) ? $settings['tp_loading_text'] : '';
	    $allposttext = !empty($settings['loaded_posts_text']) ? $settings['loaded_posts_text'] : '';
	    $txtLimt = !empty($settings['TextLimit'] == 'yes') ? true : false;
		$TextCount = !empty($settings['TextCount']) ? $settings['TextCount'] : 100 ;
		$TextType = !empty($settings['TextType']) ? $settings['TextType'] : 'char' ;
		$TextMore = !empty($settings['TextMore']) ? $settings['TextMore'] : 'Show More' ;
		$TextLess = !empty($settings['TextLess']) ? $settings['TextLess'] : 'Show Less' ;
		$TextDots = !empty($settings['TextDots'] == 'yes') ? '...' : '' ;
		$ShowFeedId = !empty($settings['ShowFeedId']) ? $settings['ShowFeedId'] : false;
		$Performance = !empty($settings['perf_manage']) ? $settings['perf_manage'] : false;

	   //layout
		$layout_attr=$data_class='';
		if(!empty($layout)){
			$data_class .= theplus_get_layout_list_class($layout);
			$layout_attr = theplus_get_layout_list_attr($layout);
		}else{
			$data_class .= ' list-isotope';
		}

		//columns
		$desktop_class=$tablet_class=$mobile_class='';
		if($layout != 'carousel'){
			$desktop_class .= 'tp-col-12';
			$desktop_class = ' tp-col-lg-'.esc_attr($settings['desktop_column']);
			$tablet_class = ' tp-col-md-'.esc_attr($settings['tablet_column']);
			$mobile_class = ' tp-col-sm-'.esc_attr($settings['mobile_column']);
			$mobile_class .= ' tp-col-'.esc_attr($settings['mobile_column']);
		}

	    $output=$data_attr='';
	    //carousel
		if($layout == 'carousel'){
			if(!empty($settings["hover_show_dots"]) && $settings["hover_show_dots"]=='yes'){
				$data_class .=' hover-slider-dots';
			}
			if(!empty($settings["hover_show_arrow"]) && $settings["hover_show_arrow"]=='yes'){
				$data_class .=' hover-slider-arrow';
			}
			if(!empty($settings["outer_section_arrow"]) && $settings["outer_section_arrow"]=='yes' && ($settings["slider_arrows_style"]=='style-1' || $settings["slider_arrows_style"]=='style-2' || $settings["slider_arrows_style"]=='style-5' || $settings["slider_arrows_style"]=='style-6')){
				$data_class .=' outer-slider-arrow';
			}
			$data_attr .=$this->get_carousel_options();
			if($settings["slider_arrows_style"]=='style-3' || $settings["slider_arrows_style"]=='style-4'){
				$data_class .=' '.$settings["arrows_position"];
			}
			if(($settings["slider_rows"] > 1) || ($settings["tablet_slider_rows"] > 1) || ($settings["mobile_slider_rows"] > 1)){
				$data_class .= ' multi-row';
			}
		}
		if($CategoryWF == 'yes'){
			$data_class .=' pt-plus-filter-post-category ';
		}

		$ji=1;$ij='';
		$uid_socirw = uniqid("post");
		if(!empty($settings["carousel_unique_id"])){
			$uid_socirw = "tpca_".$settings["carousel_unique_id"];
		}
			$data_attr .=' data-id="'.esc_attr($uid_socirw).'"';
			$data_attr .=' data-style="'.esc_attr($style).'"';	

		$NormalScroll="";
		$ScrollOn = !empty($settings['ScrollOn']) ? true : false;
		if( !empty($ScrollOn)){
			$ScrollData = array(
				'className' => 'tp-normal-scroll',
				'ScrollOn'  => $ScrollOn,
				'Height'    => !empty($settings['ScrollHgt']['SIZE']) ? (int)$settings['ScrollHgt']['SIZE'] : 100,
				'TextLimit' => $txtLimt,
			);
			$NormalScroll = json_encode($ScrollData, true);
		}

		$txtlimitData='';
		if(!empty($txtLimt)){
			$txtlimitDataa = array(
				'showmoretxt' => $TextMore,
				'showlesstxt' => $TextLess,
			);
		   $txtlimitData = json_encode($txtlimitDataa, true);
		}
		
		$output .= '<div id="'.esc_attr($uid_socirw).'" class="'.esc_attr($uid_socirw).' tp-social-reviews '.esc_attr($data_class).'" '.$layout_attr.' '.$data_attr.' data-enable-isotope="1"  data-scroll-normal="'.esc_attr($NormalScroll).'" data-textlimit="'.esc_attr($txtlimitData).'">';

		    if($RType == "review"){
				$FinalData = [];
				$Perfo_transient = get_transient("SR-Performance-$WidgetId");
				if( ($Performance == false) || ($Performance == true && $Perfo_transient === false) ){
					$AllData = [];
					foreach ($Repeater as $index => $R) {
						$RRT = !empty($R['ReviewsType']) ? $R['ReviewsType'] : 'facebook';
						$R = array_merge($TimeFrq, $R);
	
						if($RRT == 'facebook'){
							$AllData[] = $this->Facebook_Reviews($R);
						}else if($RRT == 'google'){
							$AllData[] = $this->Google_Reviews($R);
						}else if($RRT == 'manual'){
							$AllData[] = $this->Custom_Reviews($R);
						}
					}

					if(!empty($AllData)){
						foreach($AllData as $val){
							foreach($val as $vall){ 
								$FinalData[] =  $vall; 
							}
						}
					}

					$Reviews_Index = array_column($FinalData, 'Reviews_Index');
					array_multisort($Reviews_Index, SORT_ASC, $FinalData);	 

					set_transient("SR-Performance-$WidgetId", $FinalData, $RefreshTime);
				}else {
					$FinalData = get_transient("SR-Performance-$WidgetId");
				}

				if(!empty($FinalData)){
					foreach ($FinalData as $index => $data) {
						$PostId = !empty($data['PostId']) ? $data['PostId'] : [];
						if(in_array($PostId, $FeedId)){
							unset($FinalData[$index]);
						}
					}

					if($CategoryWF == 'yes' && $layout != 'carousel'){
						$FilterTotal='';
	                    if($postLodop=='load_more' || $postLodop=='lazy_load'){
	                        $FilterTotal = $Postdisplay;
	                    }else{
	                        $FilterTotal = count($FinalData);
	                    }
						$output .= $this->get_filter_category($FilterTotal, $FinalData);
					}

					if($postLodop == 'load_more' || $postLodop == 'lazy_load'){
						$totalReviews = count($FinalData);
						$remindata = array_slice($FinalData, $Postdisplay);	                

						$RemingC = count($remindata);
						$FinalData = array_slice($FinalData, 0, $Postdisplay);

						$trans_store = get_transient("SR-LoadMore-$WidgetId");
						if( $trans_store === false ){
							set_transient("SR-LoadMore-$WidgetId", $remindata, $RefreshTime);
						}else if( !empty($trans_store) && is_array($trans_store) && count($trans_store) != $totalReviews ){
							set_transient("SR-LoadMore-$WidgetId", $remindata, $RefreshTime);
						}

						$postattr = [
							'load_class' => esc_attr($WidgetId),
							'layout' => esc_attr($layout),
							'style' => esc_attr($style),
							'desktop_column' => esc_attr($settings['desktop_column']),
							'tablet_column' => esc_attr($settings['tablet_column']),
							'mobile_column' => esc_attr($settings['mobile_column']),
							'DesktopClass' => esc_attr($desktop_class),
							'TabletClass' => esc_attr($tablet_class),
							'MobileClass' => esc_attr($mobile_class),
							'TimeFrq' => esc_attr($RefreshTime),
							'FeedId' => $FeedId,
							'categorytext' => esc_attr($CategoryWF),
							'TextLimit' => esc_attr($txtLimt),
							'TextCount' => esc_attr($TextCount),
							'TextType' => esc_attr($TextType),
							'TextMore' => esc_attr($TextMore),
							'TextLess' => esc_attr($TextLess),
							'TextDots' => esc_attr($TextDots),
							'loadingtxt' => esc_attr($loadingtxt),
							'allposttext' => esc_attr($allposttext),
							'TotalReview' => esc_attr($totalReviews),
							'postview' => esc_attr((int)$postview),
							'display' => esc_attr($Postdisplay),
							'FilterStyle' => esc_attr($settings['filter_style']),
							'loadview' => esc_attr($postview),
							'theplus_nonce' => wp_create_nonce("theplus-addons"),
						];				
						$data_loadkey = tp_plus_simple_decrypt( json_encode($postattr), 'ey' );
					}
					
					$output .= '<div id="'.esc_attr($uid_socirw).'" class="tp-row post-inner-loop '.esc_attr($uid_socirw).' social-reviews-'.esc_attr($style).' '.esc_attr($OverlayImage).'" >';
						foreach ($FinalData as $F_index => $Review) {
							$RKey = !empty($Review['RKey']) ? $Review['RKey'] : '';
							$RIndex = !empty($Review['Reviews_Index']) ? $Review['Reviews_Index'] : '';
							$PostId = !empty($Review['PostId']) ? $Review['PostId'] : '';
							$Type = !empty($Review['Type']) ? $Review['Type'] : '';
							$Time = !empty($Review['CreatedTime']) ? $Review['CreatedTime'] : '';
							$UName = !empty($Review['UserName']) ? $Review['UserName'] : '';
							$UImage = !empty($Review['UserImage']) ? $Review['UserImage'] : '';
							$ULink = !empty($Review['UserLink']) ? $Review['UserLink'] : '';
							$PageLink = !empty($Review['PageLink']) ? $Review['PageLink'] : '';
							$Massage = !empty($Review['Massage']) ? $Review['Massage'] : '';
							$Icon = !empty($Review['Icon']['value']) ? $Review['Icon']['value'] : 'fas fa-star';
							$Logo = !empty($Review['Logo']) ? $Review['Logo'] : '';
							$rating = !empty($Review['rating']) ? $Review['rating'] : '';
							$CategoryText = !empty($Review['FilterCategory']) ? $Review['FilterCategory'] : '';
							$ErrClass = !empty($Review['ErrorClass']) ? $Review['ErrorClass'] : '';
							$ReviewClass = !empty($Review['selectType']) ? ' '.$Review['selectType'] : '';
							$PlatformName = !empty($ReviewClass) ? ucwords(str_replace('custom', '', $ReviewClass)) : '';	               
							$category_filter = $loop_category = '';

							if( !empty($CategoryWF == 'yes') && !empty($CategoryText)  && $layout != 'carousel' ){
								$loop_category = explode(',', $CategoryText);
								foreach( $loop_category as $category ) {
									$category = $this->Reviews_Media_createSlug($category);
									$category_filter .= ' '.esc_attr($category).' ';
								}
							}
							if(!in_array($PostId, $FeedId)){
								ob_start();
									include THEPLUS_PATH. 'includes/social-reviews/social-review-' . sanitize_file_name($style) . '.php';
									$output .= ob_get_contents();
								ob_end_clean();
							}
						}
					$output .='</div>';

					if( !empty($totalReviews) && $totalReviews > $Postdisplay && $layout != 'carousel' ){
						if($postLodop == 'load_more'){
							$output .= '<div class="ajax_load_more">';
								$output .= '<a class="post-load-more" data-loadingtxt="'.esc_attr($loadingtxt).'" data-layout="'.esc_attr($layout).'"  data-loadclass="'.esc_attr($uid_socirw).'" data-loadview="'.esc_attr($postview).'" data-loadattr= \'' . $data_loadkey . '\'>';
									$output .= $loadbtnText;
								$output .= '</a>';
							$output .= '</div>';
						}else if($postLodop == 'lazy_load'){
							$output .= '<div class="ajax_lazy_load">';
								$output .= '<a class="post-lazy-load" data-loadingtxt="'.esc_attr($loadingtxt).'" data-lazylayout="'.esc_attr($layout).'" data-lazyclass="'.esc_attr($uid_socirw).'" data-lazyview="'.esc_attr($postview).'" data-lazyattr= \'' . $data_loadkey . '\'>';
									$output .= '<div class="tp-spin-ring"><div></div><div></div><div></div></div>';
								$output .= '</a>';
							$output .= '</div>';
						}
					}

				}else{
					$output .= '<div class="error-handal"> All Social Feed </div>';
				}

	        }else if($RType == "beach"){
	        	$Bstyle = !empty($settings['Bstyle']) ? $settings['Bstyle'] : 'style-1';
	            $BRecommend = !empty($settings['BRecommend']) ? $settings['BRecommend'] : '';
	            $BSButton = !empty($settings['BSButton']) ? $settings['BSButton'] : '';
	            $BBtnName = !empty($settings['BBtnName']) ? $settings['BBtnName'] : '';
	            $Btxt1 = !empty($settings['Btxt1']) ? $settings['Btxt1'] : '' ;
	            $Btxt2 = !empty($settings['Btxt2']) ? $settings['Btxt2'] : '' ;
	            $Blinktxt = !empty($BRecommend) && !empty($settings['Blinktxt']) ? $settings['Blinktxt'] : '';
	            $Btn2NO = !empty($BRecommend) && !empty($settings['BBtnTName']) ? $settings['BBtnTName'] : '';
	            $BIcon = !empty($settings['BDyIcon']['value']) ? $settings['BDyIcon']['value'] : "fas fa-star" ;
	            $BIconHidden2 = !empty($settings['IconHidden']) ? $settings['IconHidden'] : '' ;
	            $BeachData = $this->Beach_Reviews($settings);
	            $Beach = !empty($BeachData[0]) ? $BeachData[0] : [];
	            $BTotal = !empty($Beach['Total']) ? $Beach['Total'] : '';
	            $BLink = !empty($Beach['UserLink']) ? $Beach['UserLink'] : '';
	            $BLogo = !empty($Beach['Logo']) ? $Beach['Logo'] : '';
	            $BType = !empty($Beach['Type']) ? $Beach['Type'] : '';
	            $BUname = !empty($Beach['Username']) ? $Beach['Username'] : '';
	            $BUImage = !empty($Beach['UserImage']) ? $Beach['UserImage'] : [];
	            $BRating = !empty($Beach['Rating']) ? $Beach['Rating'] : '';
	            $BErrClass = !empty($Beach['ErrorClass']) ? $Beach['ErrorClass'] : '';
	            $BMassage = !empty($Beach['Massage']) ? $Beach['Massage'] : '';

	            ob_start();
	            include THEPLUS_PATH. 'includes/social-reviews/social-review-b-'.esc_attr($Bstyle).'.php';
	            $output .= ob_get_contents();
	            ob_end_clean();
	        }
		$output .='</div>';
	    echo $output;
    }

    protected function Facebook_Reviews($RData){
		$settings = $this->get_settings_for_display();
	    $Key = !empty($RData['_id']) ? $RData['_id'] : '';
	    $Token = !empty($RData['Token']) ? $RData['Token'] : '';
	    $PageId = !empty($RData['FbPageId']) ? $RData['FbPageId'] : '';
	    $RType = !empty($RData['FbRType']) ? $RData['FbRType'] : 'default';
	    $MaxR = !empty($RData['MaxR']) ? $RData['MaxR'] : 6;
	    $Ricon = !empty($RData['icons']) ? $RData['icons'] : 'fas fa-star';
	    $TimeFrq = !empty($RData['TimeFrq']) ? $RData['TimeFrq'] : '';
		$FBNagative = !empty($settings['FBNagative']) ? $settings['FBNagative'] : 1;
		$RCategory = !empty($RData['RCategory']) ? $RData['RCategory'] : '';
		$ReviewsType = !empty($RData['ReviewsType']) ? $RData['ReviewsType'] : '';
		$Fb_Icon = THEPLUS_ASSETS_URL.'images/social-review/facebook.svg';

	    $API = "https://graph.facebook.com/v9.0/{$PageId}?access_token={$Token}&fields=ratings.fields(reviewer{id,name,picture.width(120).height(120)},created_time,rating,recommendation_type,review_text,open_graph_story{id}).limit($MaxR),overall_star_rating,rating_count";
		$Fbdata=$FbArr=[];

	    $GetAPI = get_transient("Fb-R-Url-$Key");
	    $GetTime = get_transient("Fb-R-Time-$Key");
	    if( $GetAPI != $API || $GetTime != $TimeFrq ){
	        $Fbdata = $this->Review_Api($API);
	        set_transient("Fb-R-Url-$Key", $API, $TimeFrq);
	        set_transient("Fb-R-Data-$Key", $Fbdata, $TimeFrq);
	        set_transient("Fb-R-Time-$Key", $TimeFrq, $TimeFrq);
	    }else{
	        $Fbdata = get_transient("Fb-R-Data-$Key");
	    }

	   	$facebook_status = !empty($Fbdata['HTTP_CODE']) ? $Fbdata['HTTP_CODE'] : 400;
	    if($facebook_status == 200){
	        $Rating = !empty($Fbdata['ratings']) && !empty($Fbdata['ratings']['data']) ? $Fbdata['ratings']['data'] : [];	        
	        foreach ($Rating as $index => $Data){
				
	            $FB = !empty($Data['reviewer']) ? $Data['reviewer'] : '';
	            $RT = !empty($Data['recommendation_type']) ? $Data['recommendation_type'] : '';
				$Userlink = (!empty($Data['open_graph_story']) && !empty($Data['open_graph_story']['id']) ?$Data['open_graph_story']['id'] : '');
	            $FType = '';
				
	            if($RType == "default"){
	                $FType = $RT;
	            }else{
	                $FType = $RType;
	            }

				$rating = 5;
				if($RT == "negative"){
					$rating = $FBNagative;
				}

	            if($FType == $RT){
	                $FbArr[] = array(
	                    "Reviews_Index"	=> $index,
	                    "PostId"		=> !empty($FB['id']) ? $FB['id'] : '',
	                    "Type" 			=> $RT,
	                    "CreatedTime" 	=> !empty($Data['created_time']) ? $this->Review_Time($Data['created_time']) : '',
	                    "UserName" 		=> !empty($FB['name']) ? $FB['name'] : '',
	                    "UserImage" 	=> (!empty($FB['picture']) && !empty($FB['picture']['data']['url']) ? $FB['picture']['data']['url'] : THEPLUS_ASSETS_URL.'images/tp-placeholder.jpg'),
						"UserLink"  	=> "https://facebook.com/$Userlink",
						"PageLink"  	=> "https://www.facebook.com/{$PageId}/reviews",
	                    "Massage" 		=> !empty($Data['review_text']) ? $Data['review_text'] : '',
	                    "Icon" 	        => $Ricon,
	                    "rating"        => $rating,
	                    "Logo"          => $Fb_Icon,
	                    "selectType"    => $ReviewsType,
	                    "FilterCategory"=> $RCategory,
	                    "RKey" 			=> "tp-repeater-item-$Key",
	                );
	            }
	        }
	    }else{
			$FbArr[] = $this->Review_Error_array( $Fbdata, $Key, $Fb_Icon, $ReviewsType, $RCategory );
	    }

      return $FbArr;
    }
	protected function Google_Reviews($RData){
	    $Key = !empty($RData['_id']) ? $RData['_id'] : '';
	    $Token = !empty($RData['Token']) ? $RData['Token'] : '';
	    $GPlace = !empty($RData['GPlaceID']) ? $RData['GPlaceID'] : '';
	    $TimeFrq = !empty($RData['TimeFrq']) ? $RData['TimeFrq'] : '';
	    $Ricon = !empty($RData['icons']) ? $RData['icons'] : 'fas fa-star';
	    $MaxR = !empty($RData['MaxR']) ? $RData['MaxR'] : '';
		$ReviewsType = !empty($RData['ReviewsType']) ? $RData['ReviewsType'] : '';
		$RCategory = !empty($RData['RCategory']) ? $RData['RCategory'] : '';
		$GG_Icon = THEPLUS_ASSETS_URL.'images/social-review/google.webp';
		$GLanguage = !empty($RData['GLanguage']) ? $RData['GLanguage'] : 'en';

		$Gdata=$GArr=[];
		$API = "https://maps.googleapis.com/maps/api/place/details/json?placeid={$GPlace}&key={$Token}&language={$GLanguage}";
	    $GetAPI = get_transient("G-R-Url-$Key");
	    $GetTime = get_transient("G-R-Time-$Key");
	    if( $GetAPI != $API || $GetTime != $TimeFrq ){
	        $Gdata = $this->Review_Api($API);
			set_transient("G-R-Url-$Key", $API, $TimeFrq);
			set_transient("G-R-Time-$Key", $TimeFrq, $TimeFrq);
			set_transient("G-R-Data-$Key", $Gdata, $TimeFrq);
	    }else{
	        $Gdata = get_transient("G-R-Data-$Key");
	    }

        $G_status = !empty($Gdata['HTTP_CODE']) ? $Gdata['HTTP_CODE'] : 400;
	    if($G_status == 200 && $Gdata['status'] == 'OK' && empty($Gdata['error_message'])){
			$GR = !empty($Gdata['result']['reviews']) ? $Gdata['result']['reviews'] : [];
			$PlaceName = strtolower(str_replace(' ', '_', $Gdata['result']['name']));
			$PlaceURL = !empty($Gdata['result']['url']) ? $Gdata['result']['url'] : '';

			$GG_Databash = get_option("elementor_google_review_{$PlaceName}_{$GLanguage}");
			if ( !empty($GR) && (empty($GG_Databash) || $GG_Databash == false) ) {
				add_option( "elementor_google_review_{$PlaceName}_{$GLanguage}", $GR, "", "yes" );
			}else if( !empty($GR) && !empty($GG_Databash) ) {
				$AarayTemp = [];
				foreach ($GG_Databash as $i1 => $Gdata){
					$AarayTemp[] = $Gdata['author_url'];
				}

				foreach ($GR as $i1 => $DataOne){
					$AuthorUrlOne = !empty($DataOne['author_url']) ? $DataOne['author_url'] : [];
					foreach ($GG_Databash as $i2 => $DataTwo){
						$AuthorUrlTwo = !empty($DataTwo['author_url']) ? $DataTwo['author_url'] : [];
						if( $AuthorUrlOne != $AuthorUrlTwo ){
							if( !in_array( $AuthorUrlOne, $AarayTemp ) ){
								$AarayTemp[] = $DataOne['author_url'];
								$GG_Databash[] = array(
									"author_name" => !empty($DataOne['author_name']) ? $DataOne['author_name'] : '',
									"author_url" => !empty($DataOne['author_url']) ? $DataOne['author_url'] : '',
									"language" => !empty($DataOne['language']) ? $DataOne['language'] : 'en',
									"profile_photo_url" => !empty($DataOne['profile_photo_url']) ? $DataOne['profile_photo_url'] : '',
									"rating" => !empty($DataOne['rating']) ? $DataOne['rating'] : '',
									"relative_time_description" => !empty($DataOne['relative_time_description']) ? $DataOne['relative_time_description'] : '',
									"text" => !empty($DataOne['text']) ? $DataOne['text'] : '',
									"time" => !empty($DataOne['time']) ? $DataOne['time'] : '',
								);
								update_option( "elementor_google_review_{$PlaceName}_{$GLanguage}", $GG_Databash);
							}
						}
					}
				}
				$GR = $GG_Databash;
			}
		
	        foreach ($GR as $index => $G){
	        	if($index < $MaxR){
	        		$UnqURl = explode('/', trim($G['author_url']));
                    $UnqName = explode(' ', trim($G['author_name']));
		            $Time = (!empty($G['relative_time_description']) ? $G['relative_time_description'] : '');
		            $GArr[] = array(
		                "Reviews_Index"	=> $index,
		                "PostId"		=> (!empty($UnqName[0]) && !empty($UnqURl[5]) ? $UnqName[0].'-'.substr($UnqURl[5], 0, 10) : ''),
		                "Type" 			=> "",
		                "CreatedTime" 	=> $Time,
		                "UserName" 		=> !empty($G['author_name']) ? $G['author_name'] : '',
		                "UserImage" 	=> !empty($G['profile_photo_url']) ? $G['profile_photo_url'] : '',
		                "UserLink" 	    => !empty($G['author_url']) ? $G['author_url'] : '',
						"PageLink"  	=> $PlaceURL,
		                "Massage" 		=> !empty($G['text']) ? $G['text'] : '',
		                "Icon" 	        => $Ricon,
		                "rating"        => !empty($G['rating']) ? $G['rating'] : '',
		                "Logo"          => $GG_Icon,
		                "selectType"    => $ReviewsType,
		                "FilterCategory"=> $RCategory,
		                "RKey" 			=> "tp-repeater-item-$Key",
		            );
	            }
	        }
	    }else{
			$GArr[] = $this->Review_Error_array( $Gdata, $Key, $GG_Icon, $ReviewsType, $RCategory );
	    }
	    return $GArr;
    }
    protected function Custom_Reviews($RData){
	    $Key = !empty($RData['_id']) ? $RData['_id'] : '';
	    $MaxR = !empty($RData['MaxR']) ? $RData['MaxR'] : '';   
	    $CType = !empty($RData['CPFname']) ? $RData['CPFname'] : 'facebook'; 
	    $Ricon = !empty($RData['icons']) ? $RData['icons'] : 'fas fa-star';
		
	    $Name=[];
	    if(!empty($RData['Cuname'])){
	        $Cuname = explode('|', $RData['Cuname']);
	        foreach ($Cuname as $D){ $Name[] = array("Name"=> $D); }
	    }else{
	        $Name[] = array("Name"=> "Gabriel");
	    }
	    $Massage=[];
	    if(!empty($RData['Cmassage'])){
	        $Cmassage = explode('|', $RData['Cmassage']);
	        foreach ($Cmassage as $D){ $Massage[] = array("Message"=> $D); }
	    }	    
	    $Date=[];
	    if(!empty($RData['Cdate'])){
	        $Cdate = explode('|', $RData['Cdate']);
	        foreach ($Cdate as $D){ $Date[] = array("Date"=>$D); }
	    }
	    $Star=[];
	    if(!empty($RData['Cstar'])){
	        $Cstar = explode('|', $RData['Cstar']);
	        foreach ($Cstar as $D){ $Star[] = array("Star"=>$D); }
	    }
	    $Platform=$logo="";
	    if($CType == 'manual'){
	        $Platform = (!empty($RData['CcuSname']) ? $RData['CcuSname'] : '');
	        $logo = ((!empty($RData['CImg']) && !empty($RData['CImg']['url'])) ? $RData['CImg']['url'] : THEPLUS_ASSETS_URL.'images/tp-placeholder.jpg');
	    }else if($CType == 'facebook'){
	        $Platform = $CType;
	        $logo = THEPLUS_ASSETS_URL.'images/social-review/facebook.svg';
	    }else if($CType == 'google'){
	        $Platform = $CType;
	        $logo = THEPLUS_ASSETS_URL.'images/social-review/google.webp';
	    }

	    $PImg=[];
	    if(!empty($RData['CUImg'])){
	        foreach ($RData['CUImg'] as $D){ 
				$csturl = !empty($D['url']) ? $D['url'] : THEPLUS_ASSETS_URL.'images/tp-placeholder.jpg';
	            $PImg[] = array("Profile" => $csturl); 
	        }
	    }

	    $All=[];
	    foreach ($Name as $key => $value){
	        $FImg = !empty($PImg[$key]) ? $PImg[$key] : array("Profile" => THEPLUS_ASSETS_URL.'images/tp-placeholder.jpg') ;
	        $FMsg = !empty($Massage[$key]) ? $Massage[$key] : array("Message"=> "Good") ;
	        $FStar = !empty($Star[$key]) ? $Star[$key] : array("Star"=> "3") ;
	        $FDate = !empty($Date[$key]) ? $Date[$key] : array("Date"=> "3 day ago") ;

	        $All[] = array_merge( (array)$value,$FMsg,$FDate,$FStar,$FImg );
	    }
	    if($CType == 'manual'){ 
           $Platform = "custom $Platform"; 
        }	    
	    $Arr=[];
	    if(!empty($All)){
	        foreach ($All as $i => $v){
	        	if($i < $MaxR){
		        	$C_Name = explode(' ', trim($v['Name']));
		        	$C_MSG = explode(' ', trim($v['Message']));
	                $Arr[] = array(
	                    "Reviews_Index"	=> $i,
	                    "PostId" => (!empty($C_Name[0]) && !empty($C_MSG[0]) ? $C_Name[0].$C_MSG[0] : ''),
	                    "UserName" => !empty($v['Name']) ? $v['Name'] : '',
	                    "UserImage" => !empty($v['Profile']) ? $v['Profile'] : THEPLUS_ASSETS_URL.'images/tp-placeholder.jpg',
	                    "Massage" => $v['Message'],
	                    "CreatedTime" => $v['Date'],
	                    "Icon" => $Ricon,
	                    "rating" => $v['Star'],
	                    "selectType" => $Platform,
	                    "FilterCategory" => !empty($RData['RCategory']) ? $RData['RCategory'] : '',
	                    "Logo" => $logo,
	                    "RKey" => "tp-repeater-item-$Key",
	                );
	            }
	        }
	    }
	    return $Arr;
    }
    protected function Beach_Reviews($attr){
		$settings = $this->get_settings_for_display();
		$WidgetID = $this->get_unique_selector();
		$BTypeFacebook = !empty($settings['BTypeFacebook']) ? $settings['BTypeFacebook'] : '';
		$BTypeGoogle = !empty($settings['BTypeGoogle']) ? $settings['BTypeGoogle'] : '';
		$BTimeFrq = !empty($settings['beach_TimeFrq']) ? $settings['beach_TimeFrq'] : '3600' ;
	    $BType = !empty($attr['BType']) ? $attr['BType'] : 'b-facebook';
	    $BToken = !empty($attr['BToken']) ? $attr['BToken'] : '';
	    $BPPId = !empty($attr['BPPId']) ? $attr['BPPId'] : '';
		
	    $API = "";
	    $Arr = [];
	    if($BType == "b-facebook"){
	        $API = "https://graph.facebook.com/v9.0/{$BPPId}?access_token={$BToken}&fields=ratings.fields(reviewer{id,name,picture.width(120).height(120)},created_time,rating,recommendation_type,review_text,open_graph_story{id}).limit(100),overall_star_rating,rating_count,username";
	        $Type = $BTypeFacebook;
	        $Logo = THEPLUS_ASSETS_URL.'images/social-review/facebook.svg';
	    }else if($BType == "b-google"){
	        $API = "https://maps.googleapis.com/maps/api/place/details/json?placeid={$BPPId}&key={$BToken}";
	        $Type = $BTypeGoogle;
	        $Logo = THEPLUS_ASSETS_URL.'images/social-review/google.webp';
	    }

		$Data=[];
		$BGetAPI = get_transient("Beach-Url-$WidgetID");
	    $BGetTime = get_transient("Beach-Time-$WidgetID");
	    if( $BGetAPI != $API || $BGetTime != $BTimeFrq ){
	        $Data = $this->Review_Api($API);
			set_transient("Beach-Url-$WidgetID", $API, $BTimeFrq);
			set_transient("Beach-Time-$WidgetID", $BTimeFrq, $BTimeFrq);
			set_transient("Beach-Data-$WidgetID", $Data, $BTimeFrq);
	    }else{
	        $Data = get_transient("Beach-Data-$WidgetID");
	    }
	
		$Beach_CODE = !empty($Data['HTTP_CODE']) ? $Data['HTTP_CODE'] : 400;
	    if($Beach_CODE == 200 && empty($Data['error_message'])){
	        $Image = [];
			$RatingsTotal = 0;
	        if($BType == "b-facebook"){
	            $uname = !empty($Data['username']) ? $Data['username'] : '';
	            // $Rating = !empty($Data['rating_count']) ? $Data['rating_count'] : 5;
				$Rating = !empty($Data['overall_star_rating']) ? $Data['overall_star_rating'] : '';
	            $link = "https://www.facebook.com/$BPPId";

	            $RatingImg = !empty($Data['ratings']) && !empty($Data['ratings']['data']) ? $Data['ratings']['data'] : [];
				$RatingsTotal = count($RatingImg);

	            foreach ($RatingImg as $index => $Bdata){
	                if($index > 3){ break; }
	                $FB = !empty($Bdata['reviewer']) ? $Bdata['reviewer'] : '';
	                $Image[] = (!empty($FB['picture']) && !empty($FB['picture']['data']['url']) ? $FB['picture']['data']['url'] : THEPLUS_ASSETS_URL.'images/tp-placeholder.jpg');
	            }
	        }

	        if($BType == "b-google"){
	            $uname = !empty($Data['result']['name']) ? $Data['result']['name'] : '';
	            $Rating = !empty($Data['result']['rating']) ? $Data['result']['rating'] : '';
	            $RatingsTotal = !empty($Data['result']['user_ratings_total']) ? $Data['result']['user_ratings_total'] : 0;
	            $link = "https://www.google.com/search?q=$uname";

	            $GR = !empty($Data['result']) ? $Data['result']['reviews'] : [];
	            foreach ($GR as $index => $GI){
	                if($index > 3){ break; }
	                $Image[] = !empty($GI['profile_photo_url']) ? $GI['profile_photo_url'] : THEPLUS_ASSETS_URL.'images/tp-placeholder.jpg';
	            }          
	        }

			if( ($BType == "b-facebook" && !empty($Rating)) || $BType == "b-google" ){

				$Arr[] = array(
					"Total" => $RatingsTotal,
					"Username" => $uname,
					"UserImage" => $Image,
					"UserLink" => $link,
					"Type" => $Type,
					"Logo" => $Logo,
					"Rating" => $Rating,
				);
			}else{
				$Arr[] = array(
					"Total" => 0,
					"Type" => 'Oops',     
					"Massage" => "Error : Your facebook account doesn't provide overall ratings due to insufficient reviews on your page. ",
					"UserImage" => array($Logo,$Logo,$Logo,$Logo),
					"ErrorClass" => "danger-error",
					"Logo" => $Logo,
				);
			}
	    }else{
	        $Error = !empty($Data['error']) ? $Data['error'] : [];
	        if($BType == "b-facebook"){
	            $Etype = !empty($Error['type']) ? $Error['type'] : '';

				if( !empty($Error['message']) ){
					$message = str_replace( ". ", "<br/>", $Error['message'] );
				}else if( !empty($Error['Message_Errorcurl']) ){
					$message = $Error['Message_Errorcurl'];
				}else{
					$message = 'Something Wrong';
				}
	        }
	        if($BType == "b-google"){
	            $Etype = !empty($Data['status']) ? $Data['status'] : '';
	            $message = !empty($Data['error_message']) ? str_replace(", ","<br/>", $Data['error_message']) : 'Something wrong';
	        }    
	        $Arr[] = array(
	            "Total" => $Etype,
	            "Type" => !empty($Data['HTTP_CODE']) ? "Error No : ".$Data['HTTP_CODE'] : 400,     
	            "Massage" => $message,
	            "UserImage" => array($Logo,$Logo,$Logo,$Logo),
	            "ErrorClass" => "danger-error",
	            "Logo" => $Logo,
	        );
	    }
	    return $Arr;
    }
    protected function Review_Time($datetime, $full = false) { 
	    $now = new \DateTime;
	    $ago = new \DateTime($datetime);
	    $diff = $now->diff($ago);
	    $diff->w = floor($diff->d / 7);
	    $diff->d -= $diff->w * 7;	 
	    $string = array(
	        'y' => 'year',
	        'm' => 'month',
	        'w' => 'week',
	        'd' => 'day',
	        'h' => 'hour',
	        'i' => 'minute',
	        's' => 'second',
	    );
	    foreach ($string as $k => &$v) {
	        if ($diff->$k) {
	            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
	        } else {
	            unset($string[$k]);
	        }
	    } 
	    if (!$full) $string = array_slice($string, 0, 1);
	    return $string ? implode(', ', $string) . ' ago' : 'just now';
	}
    protected function Review_Api($API){
		$settings = $this->get_settings_for_display();
		$Final=[];

		$URL = wp_remote_get($API);
		$StatusCode = wp_remote_retrieve_response_code($URL);
		$GetDataOne = wp_remote_retrieve_body($URL);
		$Statuscode = array( "HTTP_CODE" => $StatusCode );

		$Response = json_decode($GetDataOne, true);
		if( is_array($Statuscode) && is_array($Response) ){
			$Final = array_merge($Statuscode, $Response);
		}
		
		return $Final;
    }
    protected function get_carousel_options() {
		$settings = $this->get_settings_for_display();
		$data_slider ='';
		$slider_direction = ($settings['slider_direction']=='vertical') ? 'true' : 'false';
		$data_slider .=' data-slider_direction="'.esc_attr($slider_direction).'"';
		$data_slider .=' data-slide_speed="'.esc_attr($settings["slide_speed"]["size"]).'"';		
		$data_slider .=' data-slider_desktop_column="'.esc_attr($settings['slider_desktop_column']).'"';
		$data_slider .=' data-steps_slide="'.esc_attr($settings['steps_slide']).'"';		
		$slider_draggable= ($settings["slider_draggable"]=='yes') ? 'true' : 'false';
		$data_slider .=' data-slider_draggable="'.esc_attr($slider_draggable).'"';
		$slider_infinite= ($settings["slider_infinite"]=='yes') ? 'true' : 'false';
		$data_slider .=' data-slider_infinite="'.esc_attr($slider_infinite).'"';
		$slider_pause_hover= ($settings["slider_pause_hover"]=='yes') ? 'true' : 'false';
		$data_slider .=' data-slider_pause_hover="'.esc_attr($slider_pause_hover).'"';
		$slider_adaptive_height= ($settings["slider_adaptive_height"]=='yes') ? 'true' : 'false';
		$data_slider .=' data-slider_adaptive_height="'.esc_attr($slider_adaptive_height).'"';
		$slider_animation=$settings['slider_animation'];
		$data_slider .=' data-slider_animation="'.esc_attr($slider_animation).'"';
		$slider_autoplay= ($settings["slider_autoplay"]=='yes') ? 'true' : 'false';
		$data_slider .=' data-slider_autoplay="'.esc_attr($slider_autoplay).'"';
		$data_slider .=' data-autoplay_speed="'.(isset($settings["autoplay_speed"]["size"]) ? esc_attr($settings["autoplay_speed"]["size"]) : 3000).'"';
		//tablet
		$data_slider .=' data-slider_tablet_column="'.esc_attr($settings['slider_tablet_column']).'"';
		$data_slider .=' data-tablet_steps_slide="'.esc_attr($settings['tablet_steps_slide']).'"';
		$slider_responsive_tablet=$settings['slider_responsive_tablet'];
		$data_slider .=' data-slider_responsive_tablet="'.esc_attr($slider_responsive_tablet).'"';
		if(!empty($settings['slider_responsive_tablet']) && $settings['slider_responsive_tablet']=='yes'){				
			$tablet_slider_draggable= ($settings["tablet_slider_draggable"]=='yes') ? 'true' : 'false';
			$data_slider .=' data-tablet_slider_draggable="'.esc_attr($tablet_slider_draggable).'"';
			$tablet_slider_infinite= ($settings["tablet_slider_infinite"]=='yes') ? 'true' : 'false';
			$data_slider .=' data-tablet_slider_infinite="'.esc_attr($tablet_slider_infinite).'"';
			$tablet_slider_autoplay= ($settings["tablet_slider_autoplay"]=='yes') ? 'true' : 'false';
			$data_slider .=' data-tablet_slider_autoplay="'.esc_attr($tablet_slider_autoplay).'"';
			$data_slider .=' data-tablet_autoplay_speed="'.esc_attr($settings["tablet_autoplay_speed"]["size"]).'"';
			$tablet_slider_dots= ($settings["tablet_slider_dots"]=='yes') ? 'true' : 'false';
			$data_slider .=' data-tablet_slider_dots="'.esc_attr($tablet_slider_dots).'"';
			$tablet_slider_arrows= ($settings["tablet_slider_arrows"]=='yes') ? 'true' : 'false';
			$data_slider .=' data-tablet_slider_arrows="'.esc_attr($tablet_slider_arrows).'"';
			$data_slider .=' data-tablet_slider_rows="'.esc_attr($settings["tablet_slider_rows"]).'"';
			$tablet_center_mode= ($settings["tablet_center_mode"]=='yes') ? 'true' : 'false';
			$data_slider .=' data-tablet_center_mode="'.esc_attr($tablet_center_mode).'" ';
			$data_slider .=' data-tablet_center_padding="'.esc_attr(!empty($settings["tablet_center_padding"]["size"]) ? $settings["tablet_center_padding"]["size"] : 0).'" ';
		}			
		//mobile 
		$data_slider .=' data-slider_mobile_column="'.esc_attr($settings['slider_mobile_column']).'"';
		$data_slider .=' data-mobile_steps_slide="'.esc_attr($settings['mobile_steps_slide']).'"';
		$slider_responsive_mobile=$settings['slider_responsive_mobile'];			
		$data_slider .=' data-slider_responsive_mobile="'.esc_attr($slider_responsive_mobile).'"';
		if(!empty($settings['slider_responsive_mobile']) && $settings['slider_responsive_mobile']=='yes'){
			$mobile_slider_draggable= ($settings["mobile_slider_draggable"]=='yes') ? 'true' : 'false';
			$data_slider .=' data-mobile_slider_draggable="'.esc_attr($mobile_slider_draggable).'"';
			$mobile_slider_infinite= ($settings["mobile_slider_infinite"]=='yes') ? 'true' : 'false';
			$data_slider .=' data-mobile_slider_infinite="'.esc_attr($mobile_slider_infinite).'"';
			$mobile_slider_autoplay= ($settings["mobile_slider_autoplay"]=='yes') ? 'true' : 'false';
			$data_slider .=' data-mobile_slider_autoplay="'.esc_attr($mobile_slider_autoplay).'"';
			$data_slider .=' data-mobile_autoplay_speed="'.esc_attr($settings["mobile_autoplay_speed"]["size"]).'"';
			$mobile_slider_dots= ($settings["mobile_slider_dots"]=='yes') ? 'true' : 'false';
			$data_slider .=' data-mobile_slider_dots="'.esc_attr($mobile_slider_dots).'"';
			$mobile_slider_arrows= ($settings["mobile_slider_arrows"]=='yes') ? 'true' : 'false';
			$data_slider .=' data-mobile_slider_arrows="'.esc_attr($mobile_slider_arrows).'"';
			$data_slider .=' data-mobile_slider_rows="'.esc_attr($settings["mobile_slider_rows"]).'"';
			$mobile_center_mode= ($settings["mobile_center_mode"]=='yes') ? 'true' : 'false';
			$data_slider .=' data-mobile_center_mode="'.esc_attr($mobile_center_mode).'" ';
			$data_slider .=' data-mobile_center_padding="'.esc_attr($settings["mobile_center_padding"]["size"]).'" ';
		}		
		$slider_dots= ($settings["slider_dots"]=='yes') ? 'true' : 'false';
		$data_slider .=' data-slider_dots="'.esc_attr($slider_dots).'"';
		$data_slider .=' data-slider_dots_style="slick-dots '.esc_attr($settings["slider_dots_style"]).'"';	
		$slider_arrows= ($settings["slider_arrows"]=='yes') ? 'true' : 'false';
		$data_slider .=' data-slider_arrows="'.esc_attr($slider_arrows).'"';
		$data_slider .=' data-slider_arrows_style="'.esc_attr($settings["slider_arrows_style"]).'" ';
		$data_slider .=' data-arrows_position="'.esc_attr($settings["arrows_position"]).'" ';
		$data_slider .=' data-arrow_bg_color="'.esc_attr($settings["arrow_bg_color"]).'" ';
		$data_slider .=' data-arrow_icon_color="'.esc_attr($settings["arrow_icon_color"]).'" ';
		$data_slider .=' data-arrow_hover_bg_color="'.esc_attr($settings["arrow_hover_bg_color"]).'" ';
		$data_slider .=' data-arrow_hover_icon_color="'.esc_attr($settings["arrow_hover_icon_color"]).'" ';		
		$slider_center_mode= ($settings["slider_center_mode"]=='yes') ? 'true' : 'false';
		$data_slider .=' data-slider_center_mode="'.esc_attr($slider_center_mode).'" ';
		$data_slider .=' data-center_padding="'.esc_attr((!empty($settings["center_padding"]["size"])) ? $settings["center_padding"]["size"] :0).'" ';
		$data_slider .=' data-scale_center_slide="'.esc_attr((!empty($settings["scale_center_slide"]["size"])) ? $settings["scale_center_slide"]["size"] : 1).'" ';
		$data_slider .=' data-scale_normal_slide="'.esc_attr((!empty($settings["scale_normal_slide"]["size"])) ? $settings["scale_normal_slide"]["size"] : 0.8).'" ';
		$data_slider .=' data-opacity_normal_slide="'.esc_attr((!empty($settings["opacity_normal_slide"]["size"])) ? $settings["opacity_normal_slide"]["size"] : 0.7).'" ';		
		$data_slider .=' data-slider_rows="'.esc_attr($settings["slider_rows"]).'" ';
	    return $data_slider;
	}
	Protected function get_filter_category($count, $allreview){
		$settings = $this->get_settings_for_display();	
		$CategoryWF = !empty($settings['filter_category']) ? $settings['filter_category'] : '';	
		$category_filter='';
		$TeamMemberR = !empty($settings['Rreviews']) ? $settings['Rreviews'] : [];  
		if($CategoryWF == 'yes'){		
		    $filter_style = $settings["filter_style"];
			$filter_hover_style = $settings["filter_hover_style"];
			$all_filter_category = !empty($settings["all_filter_category"]) ? $settings["all_filter_category"] : esc_html__('All','theplus');

			$loop_category = [];	
			foreach ( $TeamMemberR as $TMFilter ) {
				$TMCategory = !empty($TMFilter['RCategory']) ? $TMFilter['RCategory'] : '';
				if(!empty($TMCategory)){
					$loop_category[] = $TMCategory;
		        }
			}

			$loop_category = array_unique($loop_category);
			$loop_category = $this->Reviews_Split_Array_Category($loop_category);
	        $count_category = array_count_values($loop_category);

			$all_category=$category_post_count='';			
			if($filter_style == 'style-1'){
				$all_category='<span class="all_post_count">'.esc_html($count).'</span>';
			}
			if($filter_style == 'style-2' || $filter_style == 'style-3'){
				$category_post_count = '<span class="all_post_count">'.esc_attr($count).'</span>';
			}
		    $category_filter .= '<div class="post-filter-data '.esc_attr($filter_style).' text-'.esc_attr($settings['filter_category_align']).'">';
				if($filter_style == 'style-4'){
					$category_filter .= '<span class="filters-toggle-link">'.esc_html__('Filters','theplus').'<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 64 64" style="enable-background:new 0 0 64 64;" xml:space="preserve"><g><line x1="0" y1="32" x2="63" y2="32"></line></g><polyline points="50.7,44.6 63.3,32 50.7,19.4 "></polyline><circle cx="32" cy="32" r="31"></circle></svg></span>';
				}
				$category_filter .='<ul class="category-filters '.esc_attr($filter_style).' hover-'.esc_attr($filter_hover_style).'">';
					$category_filter .= '<li><a href="#" class="filter-category-list active all" data-filter="*" >'.$category_post_count.'<span data-hover="'.esc_attr($all_filter_category).'">'.esc_html($all_filter_category).'</span>'.$all_category.'</a></li>';

					foreach ( $loop_category as $i => $key ) {
						$slug = $this->Reviews_Media_createSlug($key) ;		
						$category_post_count = '';
						if($filter_style == 'style-2' || $filter_style == 'style-3'){
							$CategoryCount=0;
							foreach ($allreview as $index => $value) {
								$CategoryName = !empty($value['FilterCategory']) ? $value['FilterCategory'] : '';
								if($CategoryName == $key && $index < $count){
									$CategoryCount++;
								}
							}
							$category_post_count = '<span class="all_post_count">'.esc_html($CategoryCount).'</span>';
						}

						$category_filter .= '<li>';
							$category_filter .= '<a href="#" class="filter-category-list"  data-filter=".'.esc_attr($slug).'">';
								$category_filter .= $category_post_count;
								$category_filter .= '<span data-hover="'.esc_attr($key).'">'.esc_html($key).'</span>';
							$category_filter .= '</a>';
						$category_filter .= '</li>';
					}
				$category_filter .= '</ul>';
			$category_filter .= '</div>';
	    }
		return $category_filter;
	}
	protected function Reviews_Split_Array_Category($array){
		if (!is_array($array)) { 
			return FALSE; 
		} 
		$result = array();
		foreach ($array as $key => $value) { 
		  if (is_array($value)) { 
			$result = array_merge($result, $this->Reviews_Split_Array_Category($value)); 
		  } 
		  else { 
			$result[$key] = $value; 
		  }
		}	
	    return $result; 
    }
    protected function Reviews_Media_createSlug($str, $delimiter = '-'){
	   $slug = preg_replace('/[^A-Za-z0-9-]+/', $delimiter, $str);
	   return $slug;
    }

	protected function Review_Error_array( $Data, $RKey, $Icon, $ReviewsType, $RCategory ){
		$Message='';
		if( !empty($Data) && !empty($Data['error_message']) ){
			$Message = $Data['error_message'];
		}else if( !empty($Data) && !empty($Data['error']) ){
			$Message = $Data['error']['message'];
		}else if( !empty($Data) && !empty($Data['status']) ){
			$Message = $Data['status'];
		}else{
			$Message = 'Something Wrong';
		}

		return array(
			"Reviews_Index" => 1,
			"ErrorClass" => "danger-error",
			"CreatedTime" => !empty($Data['status']) ? $Data['status'] : '',
			"Massage" => $Message,
			"UserName" => !empty($Data['HTTP_CODE']) ? 'Error No : '.$Data['HTTP_CODE'] : '',
			"UserImage" => $Icon,
			"Logo" => $Icon,
			"selectType" => $ReviewsType,
			"FilterCategory"=> $RCategory,
			"RKey" => "tp-repeater-item-{$RKey}",
		);
	}

    protected function content_template() {
		
	}
}