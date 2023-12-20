<?php 
/*
Widget Name: Social Feed
Description: Social Feed
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

class ThePlus_Social_Feed extends Widget_Base {
		
	public function get_name() {
		return 'tp-social-feed';
	}

    public function get_title() {
        return esc_html__('Social Feed', 'theplus');
    }

    public function get_icon() {
        return 'fa fa-rss theplus_backend_icon';
    }

    public function get_categories() {
        return array('plus-essential');
    }

	public function get_keywords() {
		return ['social', 'feed', 'fb', 'facebook', 'ig', 'instagram', 'tw', 'twitter', 'vimeo', 'tp', 'yt', 'youtube', 'theplus'];
	}

	public function is_reload_preview_required() {
		return true;
	}

    protected function register_controls() {
		/* Content Feed Start */
		$this->start_controls_section(
			'content_section',
			[
				'label' => esc_html__( 'Content Feed', 'theplus' ),
				'tab' => Controls_Manager::TAB_CONTENT,
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
			]
		);
		$this->add_control(
			'style',
			[
				'label' => esc_html__( 'Style', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'style-1',
				'options' => [
					'style-1'  => esc_html__( 'Style 1', 'theplus' ),
					'style-2' => esc_html__( 'Style 2', 'theplus' ),
					'style-3' => esc_html__( 'Style 3', 'theplus' ),
					'style-4' => esc_html__( 'Style 4', 'theplus' ),
				],
			]
		);	
		$repeater = new \Elementor\Repeater();
		$repeater->add_control(
			'selectFeed',[
				'label' => esc_html__( 'Source','theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'Facebook',
				'options' => [
					'Facebook' => esc_html__( 'Facebook','theplus' ),
					'Instagram' => esc_html__( 'Instagram','theplus' ),
					'Twitter' => esc_html__( 'Twitter','theplus' ),
					'Youtube' => esc_html__( 'Youtube','theplus' ),
					'Vimeo' => esc_html__( 'Vimeo','theplus' ),					
				],
			]
		);
		$repeater->add_control(
			'InstagramType',
			[
				'label' => esc_html__( 'Select Option','theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'Instagram_Basic',
				'options' => [
					'Instagram_Basic' => esc_html__( 'Personal','theplus' ),
					'Instagram_Graph' => esc_html__( 'Business','theplus' ),
				],
				'condition' => [
					'selectFeed' => 'Instagram',
				],
			]
		);
		$repeater->add_control(
			'SFFbAppId',
			[
				'label' => esc_html__( 'App ID', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'dynamic' => [
					'active' => true,
				],
				'condition' => [
					'selectFeed' => ['Facebook','Instagram'],
				],
				'conditions' => [
					'relation' => 'or',
					'terms' => [						
						[							
							'name' => 'selectFeed', 'operator' => '===', 'value' => 'Facebook',
						],
						[							
							'name' => 'selectFeed', 'operator' => '===', 'value' => 'Instagram',
							'name' => 'InstagramType', 'operator' => '===', 'value' => 'Instagram_Graph',
						],
					],
				],
			]
		);
		$repeater->add_control(
			'SFFbAppSecretId',
			[
				'label' => esc_html__( 'App Secret', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'dynamic' => [
					'active' => true,
				],
				'condition' => [
					'selectFeed' => ['Facebook','Instagram'],
				],
				'conditions' => [
					'relation' => 'or',
					'terms' => [						
						[							
							'name' => 'selectFeed', 'operator' => '===', 'value' => 'Facebook',
						],
						[							
							'name' => 'selectFeed', 'operator' => '===', 'value' => 'Instagram',
							'name' => 'InstagramType', 'operator' => '===', 'value' => 'Instagram_Graph',
						],
					],
				],
			]
		);
		$repeater->add_control(
			'SFFacebookButton',
			[
				'type' => Controls_Manager::RAW_HTML,
				'raw' => '<a class="tp-feed-fb-button" id="tp-feed-fb-button" ><i class="fa fa-facebook-official" aria-hidden="true"></i>Generate Access Token</a>',
				'content_classes' => 'tp-feed-fb-btn',
				'label_block' => true,
				'condition' => [
					'selectFeed' => 'Facebook',
				],
			]
		);
		$repeater->add_control(
			'SFInstagramButton',
			[
				'type' => Controls_Manager::RAW_HTML,
				'raw' => '<a class="tp-feed-IG-button" id="tp-feed-IG-button" ><i class="fa fa-instagram" aria-hidden="true"></i>Generate Access Token</a>',
				'content_classes' => 'tp-feed-IG-btn',
				'label_block' => true,
				'condition' => [
					'selectFeed' => 'Instagram',
					'InstagramType' => 'Instagram_Graph',
				],
			]
		);
		$repeater->add_control(
			'RAToken',
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
					'selectFeed!' => 'Twitter',
				],			
			]
		);
		$repeater->add_control(
			'RATokenFacebook',
			[				
				'type' => \Elementor\Controls_Manager::RAW_HTML,
				'raw' => 'Step 1 : How to <a href="https://developers.facebook.com/apps/"  target="_blank" rel="noopener noreferrer">(Create App ?)</a>,<br>Step 2 : How to <a href="https://developers.facebook.com/tools/accesstoken" target="_blank" rel="noopener noreferrer">( Create User Token ? )</a>',
				'content_classes' => 'tp-widget-description',
				'condition' => [
					'selectFeed' => 'Facebook',
				],
			]
		);	
		$repeater->add_control(
			'RATokenInstagram',
			[				
				'type' => \Elementor\Controls_Manager::RAW_HTML,
				'raw' => 'Note : How to <a href="https://developers.facebook.com/apps/"  target="_blank" rel="noopener noreferrer">(Create App ?)</a>',
				'content_classes' => 'tp-widget-description',
				'condition' => [
					'selectFeed' => 'Instagram',
				],
			]
		);
		$repeater->add_control(
			'RATokenYoutube',
			[				
				'type' => \Elementor\Controls_Manager::RAW_HTML,
				'raw' => 'Note : How to <a href="https://console.cloud.google.com/apis/credentials"  target="_blank" rel="noopener noreferrer">(Create Token ?)</a>',
				'content_classes' => 'tp-widget-description',
				'condition' => [
					'selectFeed' => 'Youtube',
				],
			]
		);	
		$repeater->add_control(
			'RATokenVimeo',
			[				
				'type' => \Elementor\Controls_Manager::RAW_HTML,
				'raw' => 'Note : How to <a href="https://developer.vimeo.com/apps"  target="_blank" rel="noopener noreferrer">(Create Token ?)</a>',
				'content_classes' => 'tp-widget-description',
				'condition' => [
					'selectFeed' => 'Vimeo',
				],
			]
		);	
		$repeater->add_control(
			'ProfileType',[
				'label' => esc_html__( 'Select Option','theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'post',
				'options' => [
					'post' => esc_html__( 'Individual','theplus' ),
					'page' => esc_html__( 'Page','theplus' ),					
				],
				'condition' => [
					'selectFeed' => 'Facebook',
				],
			]
		);
		$repeater->add_control(
			'Pageid',
			[
				'label' => esc_html__( 'Page ID', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'placeholder' => esc_html__( 'Enter Page ID', 'theplus' ),	
				'label_block' => true,
				'condition' => [
					'selectFeed' => 'Facebook',
					'ProfileType' => 'page',
				],							
			]
		);
		$repeater->add_control(
			'content',
			[
				'label' => esc_html__( 'Content Type', 'theplus' ),
				'type' => Controls_Manager::SELECT2,
				'multiple' => true,
				'label_block' => true,
				'options' => [
					'photo' => esc_html__( 'Photo','theplus' ),
					'video' => esc_html__( 'Video','theplus' ),	
					'status' => esc_html__( 'Status','theplus' ),					
				],
				'default' => [ 'photo', 'video' ],
				'condition' => [
					'selectFeed' => 'Facebook',
				],
			]
		);
		$repeater->add_control(
            'fbAlbum',
            [
				'label' => esc_html__( 'Show Album', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'default' => 'no',
				'separator' => 'before',
				'condition' => [
					'selectFeed' => 'Facebook',
				],	
			]
        );
        $repeater->add_control(
			'AlbumMaxR',
			[
				'label' => esc_html__( 'Max Album Photo', 'theplus' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 1,
				'max' => 50,
				'step' => 1,
				'default' => 8,
				'condition' => [
					'selectFeed' => 'Facebook',
					'fbAlbum' => 'yes',
				],
			]
		);
		$repeater->add_control(
			'IGImgPic',
			[
				'label' => esc_html__( 'Profile Image', 'theplus' ),
				'type' => Controls_Manager::MEDIA,
				'dynamic' => ['active'   => true,],
				'default' => [
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				],
				'condition' => [
					'selectFeed' => 'Instagram',
					'InstagramType' => 'Instagram_Basic',
				],
			]
		);
		$repeater->add_control(
			'IGPageId',
			[
				'label' => __( 'Facebook Page Id', 'theplus' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => '',
				'placeholder' => __( 'Enter Page Id', 'theplus' ),
				'condition' => [
					'selectFeed' => 'Instagram',
					'InstagramType' => 'Instagram_Graph',
				],
			]
		);
		$repeater->add_control(
			'IG_FeedTypeGp',[
				'label' => esc_html__( 'Feed Type','theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'IGUserdata',
				'options' => [
					'IGUserdata' => esc_html__( 'Userfeed','theplus' ),
					'IGHashtag' => esc_html__( 'Hashtag','theplus' ),
					'IGTag' => esc_html__( 'Mentions','theplus' ),
				],
				'condition' => [
					'selectFeed' => 'Instagram',
					'InstagramType' => 'Instagram_Graph',
				],
			]
		);
		$repeater->add_control(
			'IGUserName_GP',
			[
				'label' => __( 'UserName', 'theplus' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => '',
				'placeholder' => __( 'Enter UserName', 'theplus' ),
				'condition' => [
					'selectFeed' => 'Instagram',
					'InstagramType' => 'Instagram_Graph',
					'IG_FeedTypeGp' => 'IGUserdata',
				],
			]
		);
		$repeater->add_control(
			'IGHashtagName_GP',
			[
				'label' => __( 'Hashtag (#)', 'theplus' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => '',
				'placeholder' => __( 'Enter Hashtag', 'theplus' ),
				'condition' => [
					'selectFeed' => 'Instagram',
					'InstagramType' => 'Instagram_Graph',
					'IG_FeedTypeGp' => 'IGHashtag',
				],
			]
		);
		$repeater->add_control(
			'IG_hashtagType',
			[
				'label' => esc_html__( 'Hashtag Type','theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'top_media',
				'options' => [
					'top_media' => esc_html__( 'Top Media','theplus' ),
					'recent_media' => esc_html__( 'Recent Media','theplus' ),					
				],
				'condition' => [
					'selectFeed' => 'Instagram',
					'InstagramType' => 'Instagram_Graph',
					'IG_FeedTypeGp' => 'IGHashtag',
				],
			]
		);
		$repeater->add_control(
			'TwApi',
			[
				'label' => esc_html__( 'Consumer Key (API Key)', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'label_block' => true,
				'condition' => [
					'selectFeed' => 'Twitter',
				],							
			]
		);
		$repeater->add_control(
			'TwApiSecret',
			[
				'label' => esc_html__( 'Consumer Secret (API Secret)', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',	
				'label_block' => true,
				'condition' => [
					'selectFeed' => 'Twitter',
				],							
			]
		);
		$repeater->add_control(
			'TwAccesT',
			[
				'label' => esc_html__( 'Access Token', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',	
				'label_block' => true,
				'condition' => [
					'selectFeed' => 'Twitter',
				],							
			]
		);
		$repeater->add_control(
			'TwAccesTS',
			[
				'label' => esc_html__( 'Access Token Secret', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',	
				'label_block' => true,
				'condition' => [
					'selectFeed' => 'Twitter',
				],							
			]
		);
		$repeater->add_control(
			'RATokenTwitter',
			[				
				'type' => \Elementor\Controls_Manager::RAW_HTML,
				'raw' => 'Note : How To <a href="https://developer.twitter.com/en/apps"  target="_blank" rel="noopener noreferrer">(Create App ?)</a>',
				'content_classes' => 'tp-widget-description',
				'condition' => [
					'selectFeed' => 'Twitter',
				],
			]
		);	
		$repeater->add_control(
			'TwfeedType',[
				'label' => esc_html__( 'Twitter Feed Type','theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'userfeed',
				'options' => [
					'wptimline' => esc_html__( 'User Timeline','theplus' ),
					'userfeed' => esc_html__( 'User Feed','theplus' ),	
					'userlikes' => esc_html__( 'Users Likes','theplus' ),
					'userlist' => esc_html__( 'Tweets List','theplus' ),	
					'twcollection' => esc_html__( 'Tweets Collection','theplus' ),
					'twsearch' => esc_html__( 'Tweets By Search','theplus' ),	
					'twtrends' => esc_html__( 'Tweets Trends','theplus' ),	
					'twRTMe' => esc_html__( 'Retweets Of Me','theplus' ),	
					'Twcustom' => esc_html__( 'Custom Tweets','theplus' ),					
				],
				'condition' => [
					'selectFeed' => 'Twitter',
				],
			]
		);
		$repeater->add_control(
			'Twtimeline',[
				'label' => esc_html__( 'Timeline Type','theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'Hometimline',
				'options' => [
					'Hometimline' => esc_html__( 'Home Timeline','theplus' ),
					'mentionstimeline' => esc_html__( 'Mentions Timeline','theplus' ),						
				],
				'condition' => [
					'selectFeed' => 'Twitter',
					'TwfeedType' => 'wptimline',
				],
			]
		);
		$repeater->add_control(
			'TwSearch',
			[
				'label' => esc_html__( 'Search', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'placeholder' => esc_html__( 'Enter Value', 'theplus' ),
				'condition' => [
					'selectFeed' => 'Twitter',
					'TwfeedType' => 'twsearch',
				],	
			]
		);
		$repeater->add_control(
			'TwRtype',[
				'label' => esc_html__( 'Result Type','theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'recent',
				'options' => [
					'mixed' => esc_html__( 'Mixed','theplus' ),
					'recent' => esc_html__( 'Recent','theplus' ),	
					'popular' => esc_html__( 'Popular','theplus' ),						
				],
				'condition' => [
					'selectFeed' => 'Twitter',
					'TwfeedType' => 'twsearch',
				],
			]
		);
		$repeater->add_control(
			'TwWOEID',
			[
				'label' => esc_html__( 'WOEID Code', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'placeholder' => esc_html__( 'Enter WOEID Code', 'theplus' ),
				'condition' => [
					'selectFeed' => 'Twitter',
					'TwfeedType' => 'twtrends',
				],							
			]
		);
		$repeater->add_control(
			'TwWOEID_link',
			[				
				'type' => \Elementor\Controls_Manager::RAW_HTML,
				'raw' => 'Note : How to Get <a href="https://www.findmecity.com/"  target="_blank" rel="noopener noreferrer">(WOEID Code)</a>',
				'content_classes' => 'tp-widget-description',
				'condition' => [
					'selectFeed' => 'Twitter',
					'TwfeedType' => 'twtrends',
				],	
			]
		);	
		$repeater->add_control(
			'TwcustId',
			[
				'label' => esc_html__( 'Tweet ID (Separated By Comma)', 'theplus' ),
				'type' => Controls_Manager::TEXTAREA,
				'rows' => 2,
				'default' => '',
				'placeholder' => esc_html__( 'e.g. Tweet ID 1, Tweet ID 2', 'theplus' ),
				'dynamic' => [
					'active'   => true,
				],
				'condition' => [
					'selectFeed' => 'Twitter',
					'TwfeedType' => 'Twcustom',
				],
			]
		);
		$repeater->add_control(
			'TwUsername',
			[
				'label' => esc_html__( 'Username', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'placeholder' => esc_html__( 'Enter Username', 'theplus' ),
				'condition' => [
					'selectFeed' => 'Twitter',					
				],
				'conditions' => [
				    'relation' => 'or',
				    'terms' => [
				    	[
				        	'name' => 'TwfeedType','operator' => 'in','value' => ['userlikes','userfeed']
				        ],
						[   
							'name' => 'TwfeedType','operator' => '==','value' => 'wptimline',	
							'name' => 'Twtimeline','operator' => '==','value' => 'Hometimline',
						],
				    ],
				],
			]
		);
		$repeater->add_control(
			'Twlistsid',
			[
				'label' => esc_html__( 'Lists ID', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'placeholder' => esc_html__( 'Enter List ID', 'theplus' ),	
				'condition' => [
					'selectFeed' => 'Twitter',
					'TwfeedType' => 'userlist',
				],							
			]
		);
		$repeater->add_control(
			'Twcollsid',
			[
				'label' => esc_html__( 'Collection ID', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'placeholder' => esc_html__( 'Enter Collection ID', 'theplus' ),
				'condition' => [
					'selectFeed' => 'Twitter',
					'TwfeedType' => 'twcollection',
				],
			]
		);
		$repeater->add_control(
            'TwDmedia',
            [
				'label' => esc_html__( 'Show Media', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'default' => 'no',
				'separator' => 'before',
				'condition' => [
					'selectFeed' => 'Twitter',
					'TwfeedType!' => 'twtrends',
				],	
			]
        );
        $repeater->add_control(
            'TwRetweet',
            [
				'label' => esc_html__( 'Show Retweet', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'default' => 'no',
				'condition' => [
					'selectFeed' => 'Twitter',
					'TwfeedType!' => 'twsearch',
				],	
			]
        );
        $repeater->add_control(
            'TwComRep',				
            [
				'label' => esc_html__( 'Show Comment Replies', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'default' => 'no',
				'condition' => [
					'selectFeed' => 'Twitter',					
				],
				'conditions' => [
				    'relation' => 'or',
				    'terms' => [
				    	[
				        	'name' => 'TwfeedType','operator' => 'in','value' => ['userfeed']
				        ],
						[   
							'name' => 'TwfeedType','operator' => '==','value' => 'wptimline',	
							'name' => 'Twtimeline','operator' => '==','value' => 'Hometimline',
						],
				    ],
				],
			]					
        );
        $repeater->add_control(
			'VimeoType',[
				'label' => esc_html__( 'Vimeo Type','theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'Vm_Channel',
				'options' => [
					'Vm_User' => esc_html__( 'User Video','theplus' ),
					'Vm_search' => esc_html__( 'Search Video','theplus' ),	
					'Vm_liked' => esc_html__( 'Liked Video','theplus' ),
					'Vm_Channel' => esc_html__( 'Channel Video','theplus' ),
					'Vm_Group' => esc_html__( 'Group Video','theplus' ),
					'Vm_Album' => esc_html__( 'Album (Showcases) Video','theplus' ),
					'Vm_categories' => esc_html__( 'Categories Video','theplus' ),	
				],
				'condition' => [
					'selectFeed' => 'Vimeo',
				],
			]
		);
		$repeater->add_control(
			'VmUname',
			[
				'label' => esc_html__( 'Username', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'placeholder' => esc_html__( 'Enter Username', 'theplus' ),	
				'label_block' => true,
				'condition' => [
					'selectFeed' => 'Vimeo',
					'VimeoType' => ['Vm_User','Vm_liked','Vm_Album'],
				],				
			]
		);
		$repeater->add_control(
			'VmQsearch',
			[
				'label' => esc_html__( 'Search', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'placeholder' => esc_html__( 'Enter Value', 'theplus' ),	
				'label_block' => true,
				'condition' => [
					'selectFeed' => 'Vimeo',
					'VimeoType' => 'Vm_search',
				],		
			]
		);
		$repeater->add_control(
			'VmChannel',
			[
				'label' => esc_html__( 'Channel Name', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'placeholder' => esc_html__( 'Enter Channel Name', 'theplus' ),	
				'label_block' => true,
				'condition' => [
					'selectFeed' => 'Vimeo',
					'VimeoType' => 'Vm_Channel',
				],							
			]
		);
		$repeater->add_control(
			'VmGroup',
			[
				'label' => esc_html__( 'Group Name', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'placeholder' => esc_html__( 'Enter Group Name', 'theplus' ),	
				'label_block' => true,
				'condition' => [
					'selectFeed' => 'Vimeo',
					'VimeoType' => 'Vm_Group',
				],	
			]
		);
		$repeater->add_control(
			'VmAlbum',
			[
				'label' => esc_html__( 'Album ID', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'placeholder' => esc_html__( 'Enter Album ID', 'theplus' ),	
				'label_block' => true,
				'condition' => [
					'selectFeed' => 'Vimeo',
					'VimeoType' => 'Vm_Album',
				],							
			]
		);
		$repeater->add_control(
			'VmAlbumPass',
			[
				'label' => esc_html__( 'Password', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'placeholder' => esc_html__( 'Enter Password', 'theplus' ),	
				'label_block' => true,
				'condition' => [
					'selectFeed' => 'Vimeo',
					'VimeoType' => 'Vm_Album',
				],	
			]
		);
		$repeater->add_control(
			'VmCategories',
			[
				'label' => esc_html__( 'Categories', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'placeholder' => esc_html__( 'Enter Categories', 'theplus' ),	
				'label_block' => true,
				'condition' => [
					'selectFeed' => 'Vimeo',
					'VimeoType' => 'Vm_categories',
				],
			]
		);
		$repeater->add_control(
			'RYtType',[
				'label' => esc_html__( 'YouTube Type','theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'YT_Channel',
				'options' => [
					'YT_Userfeed' => esc_html__( 'User feed','theplus' ),
					'YT_Channel' => esc_html__( 'Channel','theplus' ),	
					'YT_Playlist' => esc_html__( 'Playlist','theplus' ),
					'YT_Search' => esc_html__( 'Search','theplus' ),						
				],
				'condition' => [
					'selectFeed' => 'Youtube',
				],
			]
		);
		$repeater->add_control(
			'YtName',
			[
				'label' => esc_html__( 'Username', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'placeholder' => esc_html__( 'Enter Username', 'theplus' ),	
				'label_block' => true,
				'condition' => [
					'selectFeed' => 'Youtube',
					'RYtType' => 'YT_Userfeed',
				],
			]
		);
		$repeater->add_control(
			'YTChannel',
			[
				'label' => esc_html__( 'Channel ID', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'placeholder' => esc_html__( 'Enter Channel ID', 'theplus' ),	
				'label_block' => true,
				'condition' => [
					'selectFeed' => 'Youtube',
					'RYtType' => 'YT_Channel',
				],
			]
		);
		$repeater->add_control(
			'YTPlaylist',
			[
				'label' => esc_html__( 'Playlist ID', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'placeholder' => esc_html__( 'Enter Playlist ID', 'theplus' ),	
				'label_block' => true,
				'condition' => [
					'selectFeed' => 'Youtube',
					'RYtType' => 'YT_Playlist',
				],
			]
		);
		$repeater->add_control(
			'YTsearchQ',
			[
				'label' => esc_html__( 'Search Query', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'placeholder' => esc_html__( 'Enter Value', 'theplus' ),	
				'label_block' => true,
				'condition' => [
					'selectFeed' => 'Youtube',
					'RYtType' => 'YT_Search',
				],
			]
		);
		$repeater->add_control(
			'YTvOrder',[
				'label' => esc_html__( 'Video Order','theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'date',
				'options' => [
					'date' => esc_html__( 'Date','theplus' ),
					'Title' => esc_html__( 'Title','theplus' ),	
					'rating' => esc_html__( 'Rating','theplus' ),
					'relevance' => esc_html__( 'Relevance','theplus' ),
					'viewCount' => esc_html__( 'ViewCount','theplus' ),
					'videoCount' => esc_html__( 'VideoCount','theplus' ),						
				],
				'condition' => [
					'selectFeed' => 'Youtube',
					'RYtType' => ['YT_Userfeed','YT_Channel']
				],	
			]
		);
		$repeater->add_control(
			'YTthumbnail',[
				'label' => esc_html__( 'Thumbnail Size','theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'medium',
				'options' => [
					'default' => esc_html__( 'Thumbnail','theplus' ),
					'medium' => esc_html__( 'Medium','theplus' ),	
					'high' => esc_html__( 'High','theplus' ),
					'standard' => esc_html__( 'Standard','theplus' ),
					'maxres' => esc_html__( 'Max Resolution','theplus' ),
				],
				'condition' => [
					'selectFeed' => 'Youtube',
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
				'max' => 100,
				'step' => 1,
				'default' => 6,
			]
		);
		$this->add_control(
            'AllReapeter',
            [
				'label' => esc_html__( 'Social Feeds', 'theplus' ),
                'type' => Controls_Manager::REPEATER,
                'default' => [
                    [
                        'selectFeed' => 'Facebook',                      
                        'TwfeedType' => 'userfeed',
                        'Twtimeline' => 'Hometimline',                      
                        'VimeoType' => 'Vm_Channel',
                        'RYtType' => 'YT_Channel',
                        'MaxR' => 6,        
                    ],
                ],
                'separator' => 'before',
				'fields' => $repeater->get_controls(),
                'title_field' => '{{{ selectFeed }}}',				
            ]
        );  		
		$this->end_controls_section();
		/* Content Feed end */
		/* social feed Start */
		$this->start_controls_section(
			'social_feed_section',
			[
				'label' => esc_html__( 'Social Feed Options', 'theplus' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_control(
			'TotalPost',
			[
				'label' => esc_html__( 'Maximum Posts', 'theplus' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 1,
				'max' => 2000,
				'step' => 1,
				'default' => 1000,
			]
		);
		$this->add_control(
            'DescripBTM',
            [
				'label' => esc_html__( 'Description Bottom', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'default' => 'no',	
				'condition' => [
					'style' => 'style-2',
				],
			]
        );
        $this->add_control(
			'MediaFilter',
			[
				'label' => esc_html__( 'Media Filter', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'default',
				'options' => [
					'default'  => esc_html__( 'Default', 'theplus' ),
					'ompost' => esc_html__( 'Only Media Posts', 'theplus' ),
					'hmcontent' => esc_html__( 'Hide Media Posts', 'theplus' ),
				],
			]
		);
		$this->add_control(
            'ShowTitle',
            [
				'label' => esc_html__( 'Show Title', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'default' => '',
				'separator' => 'before',	
			]
        );
		$this->add_control(
			'ShowTitleNote',
			[				
				'type' => \Elementor\Controls_Manager::RAW_HTML,
				'raw' => 'Note: It Will Work in Youtube, Vimeo and Instagram(Mention)',
				'content_classes' => 'tp-widget-description',
			]
		);
		$this->add_control(
            'ShowFeedId',
            [
				'label' => esc_html__( 'Display Id & Exclude', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'default' => 'no',
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
					'active'   => true,
				],
				'condition' => [
					'ShowFeedId' => 'yes',
				],
			]
		);
		$this->add_control(
			'ExcldPIdsNote',
			[				
				'type' => \Elementor\Controls_Manager::RAW_HTML,
				'raw' => 'Note: By Enabling This Option, You Will See The Post Id Of Each In The Back-end, And Then You Can Use Those To Exclude Posts You Want To.',
				'content_classes' => 'tp-widget-description',
				'condition' => [
					'ShowFeedId' => 'yes',
				],
			]
		);
        $this->add_control(
            'showFooterIn',
            [
				'label' => esc_html__( 'Emotions Titles', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'default' => 'no',
				'condition' => [
					'style' => 'style-2',
				],	
			]
        );
		$this->end_controls_section();
		/*social feed end*/
		/*columns start*/
		$this->start_controls_section(
			'columns_manage_section',
			[
				'label' => esc_html__( 'Columns Manage', 'theplus' ),
				'tab' => Controls_Manager::TAB_CONTENT,
				'condition' => [
					'layout!' => 'carousel',
				],
			]
		);
		$this->add_control(
			'desktop_column',
			[
				'label' => esc_html__( 'Desktop', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => '3',
				'options' => theplus_get_columns_list(),
				'condition' => [
					'layout!' => 'carousel',
				],
			]
		);
		$this->add_control(
			'tablet_column',
			[
				'label' => esc_html__( 'Tablet', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => '4',
				'options' => theplus_get_columns_list(),
				'condition' => [
					'layout!' => 'carousel',
				],
			]
		);
		$this->add_control(
			'mobile_column',
			[
				'label' => esc_html__( 'Mobile', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => '6',
				'options' => theplus_get_columns_list(),
				'condition' => [
					'layout!' => 'carousel',
				],
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
				'condition' => [
					'layout!' => 'carousel',
				],
				'selectors' => [
					'{{WRAPPER}} .tp-social-feed .post-inner-loop .grid-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'layout!' => 'carousel',
				],
			]
		);
		$this->add_control(
			'filter_category',
			[
				'label' => esc_html__( 'Category Wise Filter', 'theplus' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'theplus' ),
				'label_off' => esc_html__( 'Hide', 'theplus' ),
				'default' => 'no',
				'condition' => [
					'layout!' => 'carousel',
				],
			]
		);
        $this->add_control(
			'all_filter_category',
			[
				'label' => esc_html__( 'All Filter Category Text', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'All', 'theplus' ),
				'condition'   => [
					'filter_category'    => 'yes',
					'layout!' => 'carousel',
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
					'filter_category'    => 'yes',
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
				'condition'   => [
					'filter_category'    => 'yes',
					'layout!' => 'carousel',
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
				'label_block' => false,
				'condition'   => [
					'filter_category'    => 'yes',
					'layout!' => 'carousel',
				],
			]
		);
		$this->end_controls_section();
		/*Filters Option End*/

		/*Load More/Lazy Load Option start*/
		$this->start_controls_section(
			'loadmore_lazyload_section',
			[
				'label' => esc_html__( 'Load More/Lazy Load Option', 'theplus' ),
				'tab' => Controls_Manager::TAB_CONTENT,
				'condition' => [
					'layout!' => 'carousel',
				],
			]
		);
			$this->add_control(
				'post_extra_option',
				[
					'label' => esc_html__( 'More Post Loading Options', 'theplus' ),
					'type' => Controls_Manager::SELECT,
					'default' => 'none',
					'options' => [
						'none' => esc_html__( 'Select Options','theplus' ),
						'load_more' => esc_html__( 'Load More','theplus' ),	
						'lazy_load' => esc_html__( 'Lazy Load','theplus' ),						
					],
					'condition' => [
						'layout!' => ['carousel'],
					],
				]
			);
			$this->add_control(
				'display_posts',
				[
					'label' => esc_html__( 'Maximum Posts Display', 'theplus' ),
					'type' => Controls_Manager::NUMBER,
					'min' => 1,
					'max' => 200,
					'step' => 1,
					'default' => 8,
					'condition'   => [
						'layout!' => ['carousel'],
						'post_extra_option'    => ['load_more','lazy_load'],
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
						'layout!' => ['carousel'],
						'post_extra_option'    => ['load_more','lazy_load'],
					],
				]
			);
			$this->add_control(
				'load_more_btn_text',
				[
					'label' => esc_html__( 'Button Text', 'theplus' ),
					'type' => \Elementor\Controls_Manager::TEXT,
					'default' => esc_html__( 'Load More', 'theplus' ),
					'condition'   => [
						'layout!' => ['carousel'],
						'post_extra_option'    => 'load_more',
					],
				]
			);
			$this->add_control(
				'tp_loading_text',
				[
					'label' => esc_html__( 'Loading Text', 'theplus' ),
					'type' => Controls_Manager::TEXT,
					'default' => esc_html__( 'Loading...', 'theplus' ),
					'condition'   => [
						'layout!' => ['carousel'],
						'post_extra_option'    => ['load_more','lazy_load']
					],
				]
			);
			$this->add_control(
				'loaded_posts_text',
				[
					'label' => esc_html__( 'All Posts Loaded Text', 'theplus' ),
					'type' => Controls_Manager::TEXT,
					'default' => esc_html__( 'All done!', 'theplus' ),
					'condition'   => [
						'layout!' => ['carousel'],
						'post_extra_option'    => ['load_more','lazy_load']
					],
				]
			);
		
		$this->end_controls_section();
		/*Load More/Lazy Load Option end*/

		/*Extra options*/
		$this->start_controls_section(
			'extra_options_section',
			[
				'label' => esc_html__( 'Extra Options', 'theplus' ),
				'tab' => Controls_Manager::TAB_CONTENT,
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
			'TimFreqNote',
			[				
				'type' => \Elementor\Controls_Manager::RAW_HTML,
				'raw' => 'Note: It Will Send API Requests To Social Media For Feed Refresh Based On Your Selected Value Above.',
				'content_classes' => 'tp-widget-description',
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
				'default' => 'no',
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
						'max' => 2000,
						'step' => 10,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 150,
				],
				'render_type' => 'ui',
				'condition' => [
					'ScrollOn'    => 'yes',
				],
			]
		);
		$this->add_control(
			'FcySclOn',
			[
				'label' => esc_html__( 'Fancybox Scrolling Bar', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'default' => 'no',
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			'FcySclHgt',
			[
				'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Height', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 2000,
						'step' => 10,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 150,
				],
				'render_type' => 'ui',
				'condition' => [
					'FcySclOn'    => 'yes',
				],
			]
		);
		$this->add_control(
			'OnPopup',[
				'label' => esc_html__( 'On Post Click','theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'OnFancyBox',
				'options' => [
					'Donothing' => esc_html__( 'Do Nothing','theplus' ),
					'GoWebsite' => esc_html__( 'Go To Website','theplus' ),
					'OnFancyBox' => esc_html__( 'Open Fancy Box','theplus' ),
				],
				'separator' => 'before',
			]
		);
		$this->add_control(
			'CURLOPT_SSL_VERIFYPEER',
			[
				'label' => esc_html__( 'Curl SSL Verify Peer', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'default' => 'yes',
				'separator' => 'before',
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
				'raw' => '<span>Delete All Transient </span><a class="tp-feed-delete-transient" id="tp-feed-delete-transient" > Delete </a>',
				'content_classes' => 'tp-feed-delete-transient-btn',
				'label_block' => true,
			]
		);
		$this->end_controls_section();
		/*Performance End*/

		/*All Content Style Start*/
		$this->start_controls_section(
            'section_alcontnt_styling',
            [
                'label' => esc_html__('Universal', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'AllMsgTp',
				'label' => esc_html__( 'Extra Title Typography', 'theplus' ),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector' => '{{WRAPPER}} .tp-social-feed .tp-sf-feed .tp-title',
				'condition'   => [
					'ShowTitle' => 'yes',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'AllDesTp',
				'label' => esc_html__( 'Description Typography', 'theplus' ),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector' => '{{WRAPPER}} .tp-social-feed .tp-sf-feed .tp-message',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'AllNameTp',
				'label' => esc_html__( 'Name Typography', 'theplus' ),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector' => '{{WRAPPER}} .tp-social-feed .tp-sf-feed .tp-sf-username a',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'AllTimeTp',
				'label' => esc_html__( 'Time Typography', 'theplus' ),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector' => '{{WRAPPER}} .tp-social-feed .tp-sf-feed .tp-sf-time a',
				'separator' => 'after',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'AllFooterTp',
				'label' => esc_html__( 'Footer Area Typography', 'theplus' ),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector' => '{{WRAPPER}} .tp-social-feed .tp-sf-feed .tp-sf-footer',
				'separator' => 'after',
			]
		);
		$this->start_controls_tabs( 'sfd_alcontnt_clr_tabs' );
		$this->start_controls_tab(
			'sfd_alcontnt_clr_n',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);	
		$this->add_control(
			'AllNTitleC',
			[
				'label' => esc_html__( 'Title Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .tp-social-feed .tp-sf-feed .tp-title' => 'color: {{VALUE}}',					
				],
				'condition' => [
					'ShowTitle' => 'yes',
				],
			]
		);
		$this->add_control(
			'AllNDesC',
			[
				'label' => esc_html__( 'Description Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .tp-social-feed .tp-sf-feed .tp-message' => 'color: {{VALUE}}',					
				],
			]
		);
		$this->add_control(
			'AllNNameC',
			[
				'label' => esc_html__( 'Name Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .tp-social-feed .tp-sf-feed .tp-sf-username a' => 'color: {{VALUE}}',					
				],
			]
		);
		$this->add_control(
			'AllNTimeC',
			[
				'label' => esc_html__( 'Time Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .tp-social-feed .tp-sf-feed .tp-sf-time a' => 'color: {{VALUE}}',					
				],
			]
		);
		$this->add_control(
			'AllNIconCr',
			[
				'label' => esc_html__( 'Icon Footer Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .tp-social-feed .tp-sf-feed .tp-sf-footer,{{WRAPPER}} .tp-social-feed .tp-sf-feed .tp-sf-footer a' => 'color: {{VALUE}}',					
				],
			]
		);
		$this->add_control(
			'AllNurlC',
			[
				'label' => esc_html__( 'URL Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .tp-social-feed .tp-feedurl' => 'color: {{VALUE}}',					
				],
			]
		);
		$this->add_control(
			'AllNMtC',
			[
				'label' => esc_html__( '@Mention Tag Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .tp-social-feed .tp-mantion' => 'color: {{VALUE}}',					
				],
			]
		);
		$this->add_control(
			'AllNHtC',
			[
				'label' => esc_html__( '#Hashtag Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .tp-social-feed .tp-hashtag' => 'color: {{VALUE}}',					
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'sfd_alcontnt_clr_h',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
		$this->add_control(
			'AllHTitleC',
			[
				'label' => esc_html__( 'Title Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .tp-social-feed .tp-sf-feed:hover .tp-title' => 'color: {{VALUE}}',					
				],
				'condition' => [
					'ShowTitle' => 'yes',
				],
			]
		);
		$this->add_control(
			'AllHDesC',
			[
				'label' => esc_html__( 'Description Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .tp-social-feed .tp-sf-feed:hover .tp-message' => 'color: {{VALUE}}',					
				],
			]
		);
		$this->add_control(
			'ALLHNameC',
			[
				'label' => esc_html__( 'Name Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .tp-social-feed .tp-sf-feed:hover .tp-sf-username a' => 'color: {{VALUE}}',					
				],
			]
		);
		$this->add_control(
			'AllHTimeC',
			[
				'label' => esc_html__( 'Time Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .tp-social-feed .tp-sf-feed:hover .tp-sf-time a' => 'color: {{VALUE}}',					
				],
			]
		);
		$this->add_control(
			'AllHIconCr',
			[
				'label' => esc_html__( 'Icon Footer Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .tp-social-feed .tp-sf-feed:hover .tp-sf-footer,{{WRAPPER}} .tp-social-feed .tp-sf-feed:hover .tp-sf-footer a' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'AllHurlC',
			[
				'label' => esc_html__( 'URL Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .tp-social-feed .tp-message:hover .tp-feedurl' => 'color: {{VALUE}}',					
				],
			]
		);
		$this->add_control(
			'AllHMtC',
			[
				'label' => esc_html__( 'Mention Tag Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .tp-social-feed .tp-message:hover .tp-mantion' => 'color: {{VALUE}}',					
				],
			]
		);
		$this->add_control(
			'AllHHtC',
			[
				'label' => esc_html__( '#Hashtag Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .tp-social-feed .tp-message:hover .tp-hashtag' => 'color: {{VALUE}}',					
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->add_responsive_control(
            'SocIconSize',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Icon Size', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 150,
						'step' => 1,
					],
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .tp-social-feed .social-logo-fb,
					{{WRAPPER}} .tp-social-feed .social-logo-yt,
					{{WRAPPER}} .tp-social-feed .social-logo-tw' => 'font-size: {{SIZE}}{{UNIT}};',
				],
				'separator' => 'before',
            ]
        );
		$this->add_control(
			'SocIconColor',
			[
				'label' => esc_html__( 'Icon Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .tp-social-feed .social-logo-fb,
					{{WRAPPER}} .tp-social-feed .social-logo-yt,
					{{WRAPPER}} .tp-social-feed .social-logo-tw' => 'color: {{VALUE}}',					
				],
			]
		);
		$this->add_control(
			'PostArea',
			[
				'label' => esc_html__( 'Post Thumbnail Area', 'theplus' ),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			'AllImg',
			[
				'label' => esc_html__( 'Post Thumbnail Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .tp-social-feed .tp-sf-feed .tp-soc-img-cls' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'AllImgBR',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-social-feed img.tp-post-thumb',
			]
		);
		$this->add_responsive_control(
			'AllImgbr',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-social-feed img.tp-post-thumb' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',				
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'AllImgBoxSh',
				'selector' => '{{WRAPPER}} .tp-social-feed img.tp-post-thumb',
			]
		);
		$this->add_responsive_control(
			'AllTitle',
			[
				'label' => esc_html__( 'Title Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .tp-social-feed .tp-sf-feed .tp-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'   => [
					'ShowTitle' => 'yes',
				],
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'AllTitleBR',
				'label' => esc_html__( 'Title Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-social-feed .tp-sf-feed .tp-title',
				'condition'   => [
					'ShowTitle' => 'yes',
				],				
			]
		);
		$this->add_control(
			'DescriptionArea',
			[
				'label' => esc_html__( 'Description Area', 'theplus' ),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			'Alldescription',
			[
				'label' => esc_html__( 'Description Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .tp-social-feed .tp-sf-feed .tp-message' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'AllDesBR',
				'label' => esc_html__( 'Description Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-social-feed .tp-sf-feed .tp-message',
			]
		);
		$this->add_control(
			'ProfileArea',
			[
				'label' => esc_html__( 'Profile Area', 'theplus' ),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			'AllProfile',
			[
				'label' => esc_html__( 'Profile Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .tp-social-feed .tp-sf-feed .tp-sf-header' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'AllProfBR',
				'label' => esc_html__( 'Profile Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-social-feed .tp-sf-feed .tp-sf-header',
			]
		);
		$this->add_responsive_control(
			'AllPRs',
			[
				'label'      => esc_html__( 'Profile Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-social-feed .tp-sf-feed img.tp-sf-logo' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',				
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'AllBoxSh',
				'selector' => '{{WRAPPER}} .tp-social-feed .tp-sf-feed img.tp-sf-logo',
			]
		);
		$this->add_control(
			'FooterArea',
			[
				'label' => esc_html__( 'Footer Area', 'theplus' ),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			'AllFooter',
			[
				'label' => esc_html__( 'Footer Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .tp-social-feed .tp-sf-feed .tp-sf-footer' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'AllbtmBR',
				'label' => esc_html__( 'Footer Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-social-feed .tp-sf-feed .tp-sf-footer',
			]
		);
		$this->add_control(
			'UniVBgin_opt',
			[
				'label' => esc_html__( 'Box Inner Options', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition'   => [
					'style'    => ['style-3','style-4'],
				],
			]
		);
		$this->add_responsive_control(
			'inAllboxpadd',
			[
				'label' => esc_html__( 'Box Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .tp-social-feed .tp-sf-contant' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'   => [
					'style'    => ['style-3','style-4'],
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'inAllNBgCr',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .tp-social-feed .tp-sf-contant',
				'condition'   => [
					'style'    => ['style-3','style-4'],
				],
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'inAllNBcr',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-social-feed .tp-sf-contant',
				'condition'   => [
					'style'    => ['style-3','style-4'],
				],
			]
		);
		$this->add_responsive_control(
			'inAllNBrs',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-social-feed .tp-sf-contant' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',				
				],
				'condition'   => [
					'style'    => ['style-3','style-4'],
				],	
			]
		);
		$this->add_control(
			'UniVBg_opt',
			[
				'label' => esc_html__( 'Box Background Options', 'theplus' ),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',				
			]
		);
		$this->start_controls_tabs( 'sfd_alcontnt_tabs' );
		$this->start_controls_tab(
			'sfd_alcontnt_n',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);	
		$this->add_responsive_control(
			'Allboxpadd',
			[
				'label' => esc_html__( 'Box Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .tp-social-feed .tp-sf-feed' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'AllNBgCr',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .tp-social-feed .tp-sf-feed',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'AllNBcr',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-social-feed .tp-sf-feed',
			]
		);
		$this->add_responsive_control(
			'AllNBrs',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-social-feed .tp-sf-feed' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',				
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'AllNBs',
				'selector' => '{{WRAPPER}} .tp-social-feed .tp-sf-feed',
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'sfd_alcontnt_h',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
		$this->add_responsive_control(
			'AllHboxpadd',
			[
				'label' => esc_html__( 'Box Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .tp-social-feed .tp-sf-feed:hover' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'AllHBgCr',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .tp-social-feed .tp-sf-feed:hover',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'AllHBcr',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-social-feed .tp-sf-feed:hover',
			]
		);
		$this->add_responsive_control(
			'AllHBrs',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-social-feed .tp-sf-feed:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',				
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'AllHBs',
				'selector' => '{{WRAPPER}} .tp-social-feed .tp-sf-feed:hover',
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
        $this->end_controls_section();
		/*All Content Style End*/ 
		/*FancyBox Option Style Start*/
		$this->start_controls_section(
            'section_Fncbox_optn_styling',
            [
                'label' => esc_html__('FancyBox', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
        $this->add_control(
			'FancyStyle',
			[
				'label' => esc_html__( 'FancyBox Style', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'default',
				'options' => [
					'default' => esc_html__( 'Default','theplus' ),
					'style-1' => esc_html__( 'Style 1','theplus' ),	
					'style-2' => esc_html__( 'Style 2','theplus' ),					
				],
			]
		);
		$this->start_controls_tabs( 'sfd_Fncbox_optn_tabs' );
		$this->start_controls_tab(
			'sfd_Fncbox_optn_n',
			[
				'label' => esc_html__( 'Option', 'theplus' ),
			]
		);			
		$this->add_control(
			'FancyOption',
			[
				'label' => esc_html__( 'Features', 'theplus' ),
				'type' => Controls_Manager::SELECT2,				
				'multiple' => true,				
				'options' => [
					'slideShow' => esc_html__( 'SlideShow','theplus' ),
					'share' => esc_html__( 'Share','theplus' ),	
					'zoom' => esc_html__( 'Zoom','theplus' ),
					'thumbs' => esc_html__( 'Thumbs','theplus' ),
					'fullScreen' => esc_html__( 'FullScreen','theplus' ),
					'download' => esc_html__( 'Download','theplus' ),
					'close' => esc_html__( 'Close','theplus' ),					
				],
				'default' => [ 'fullScreen', 'close' ],
			]
		);
		$this->add_control(
            'LoopFancy',
            [
				'label' => esc_html__( 'Loop', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'default' => 'yes',	
			]
        );
        $this->add_control(
            'infobar',
            [
				'label' => esc_html__( 'Image Counter', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'default' => 'yes',	
			]
        );
        $this->add_control(
            'ArrowsFancy',
            [
				'label' => esc_html__( 'Show Arrows', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'default' => 'yes',	
			]
        );
		$this->add_control(
			'TransitionFancy',
			[
				'label' => esc_html__( 'Transition Effect', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'slide',
				'options' => [
					'false' => esc_html__( 'None','theplus' ),
					'tube' => esc_html__( 'Tube','theplus' ),	
					'fade' => esc_html__( 'Fade','theplus' ),
					'slide' => esc_html__( 'Slide','theplus' ),
					'rotate' => esc_html__( 'Rotate','theplus' ),
					'circular' => esc_html__( 'Circular','theplus' ),
					'zoom-in-out' => esc_html__( 'Zoom-in-out','theplus' ),					
				],
			]
		);
		$this->add_responsive_control(
            'TranDuration',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Transition Duration ( ms )', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
						'step' => 1,
					],
				],
				'default' => [
						'unit' => 'px',
						'size' => 366,
					],
				'render_type' => 'ui',
            ]
        );
        $this->add_control(
			'AnimationFancy',
			[
				'label' => esc_html__( 'Animation Effect', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'zoom',
				'options' => [
					'false' => esc_html__( 'None','theplus' ),
					'zoom' => esc_html__( 'Zoom','theplus' ),	
					'fade' => esc_html__( 'Fade','theplus' ),
					'zoom-in-out' => esc_html__( 'Zoom-in-out','theplus' ),					
				],
			]
		);
		$this->add_responsive_control(
            'DurationFancy',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Animation Duration ( ms )', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
						'step' => 1,
					],
				],
				'default' => [
						'unit' => 'px',
						'size' => 366,
					],
				'render_type' => 'ui',
            ]
        );
        $this->add_control(
			'ClickContent',
			[
				'label' => esc_html__( 'Content Click', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'next',
				'options' => [
					'false' => esc_html__( 'None','theplus' ),
					'next' => esc_html__( 'Next','theplus' ),	
					'zoom' => esc_html__( 'Zoom','theplus' ),
					'close' => esc_html__( 'Close','theplus' ),					
				],
			]
		);
		$this->add_control(
			'Slideclick',
			[
				'label' => esc_html__( 'Outer Click', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'close',
				'options' => [
					'false' => esc_html__( 'None','theplus' ),
					'next' => esc_html__( 'Next','theplus' ),	
					'zoom' => esc_html__( 'Zoom','theplus' ),
					'close' => esc_html__( 'Close','theplus' ),					
				],
			]
		);
		$this->add_control(
            'ThumbsOption',
            [
				'label' => esc_html__( 'Thumbs Option', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'default' => 'no',
				'separator' => 'before',	
			]
        );
		$this->add_control(
			'ThumbsOptionNote',
			[				
				'type' => \Elementor\Controls_Manager::RAW_HTML,
				'raw' => 'Note : Make sure It"s selected from Features',
				'content_classes' => 'tp-widget-description',
				'condition' => [
					'ThumbsOption' => 'yes',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'ThumbsBrCr',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '.fancybox-thumbs__list a.fancybox-thumbs-active:before,.fancybox-thumbs__list a:before',
				'condition'   => [
					'ThumbsOption' => 'yes',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'ThumbsBg',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '.fancybox-thumbs .fancybox-thumbs__list',
				'condition'   => [
					'ThumbsOption'    => 'yes',
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'sfd_Fncbox_optn_h',
			[
				'label' => esc_html__( 'Style', 'theplus' ),
			]
		);
		$this->add_control(
			'Fancy_out_Bg',
			[
				'label' => esc_html__( 'Outer Background Options', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',				
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'FancyBg',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '.fancybox-container .fancybox-bg',
			]
		);
		$this->add_control(
			'Fancy_Outer_filter',
			[
				'label' => esc_html__( 'Backdrop Filter', 'theplus' ),
				'type' => Controls_Manager::POPOVER_TOGGLE,
				'label_off' => __( 'Default', 'theplus' ),
				'label_on' => __( 'Custom', 'theplus' ),
				'return_value' => 'yes',
			]
		);
		$this->add_control(
			'Fancy_Outer_filter_blur',
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
					'Fancy_Outer_filter' => 'yes',
				],
			]
		);
		$this->add_control(
			'Fancy_Outer_filter_grayscale',
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
					'.fancybox-container' => '-webkit-backdrop-filter:grayscale({{Fancy_Outer_filter_grayscale.SIZE}})  blur({{Fancy_Outer_filter_blur.SIZE}}{{Fancy_Outer_filter_blur.UNIT}}) !important;backdrop-filter:grayscale({{Fancy_Outer_filter_grayscale.SIZE}})  blur({{Fancy_Outer_filter_blur.SIZE}}{{Fancy_Outer_filter_blur.UNIT}}) !important;',
				 ],
				'condition'    => [
					'Fancy_Outer_filter' => 'yes',
				],
			]
		);
		$this->end_popover();
		$this->add_control(
			'Fancy_inn_Bg',
			[
				'label' => esc_html__( 'Inner Background Options', 'theplus' ),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
				'condition'   => [
					'FancyStyle' => ['style-1','style-2'],
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'FancyInBg',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '.fancybox-si,.fancybox-si.fancy-style-1,.fancybox-si.fancy-style-2',
				'condition'   => [
					'FancyStyle' => ['style-1','style-2'],
				],
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'FancyInBgB',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '.fancybox-si',
				'condition' => [
					'FancyStyle' => ['style-1','style-2'],
				],
			]
		);
		$this->add_responsive_control(
			'FancyInBgBs',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'.fancybox-si' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'FancyStyle' => ['style-1','style-2'],
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'FancyInBoxSw',
				'selector' => '.fancybox-si',
				'separator' => 'after',
				'condition' => [
					'FancyStyle' => ['style-1','style-2'],
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'FancyName',
				'label' => esc_html__( 'Name Typography', 'theplus' ),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector' => '.fancybox-si .tp-fcb-username a',
				'separator' => 'before',
				'condition'   => [
					'FancyStyle' => ['style-1','style-2'],
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'FancyTime',
				'label' => esc_html__( 'Time Typography', 'theplus' ),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector' => '.fancybox-si .tp-fcb-time a',
				'condition'   => [
					'FancyStyle' => ['style-1','style-2'],
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'FancyTitle',
				'label' => esc_html__( 'Title Typography', 'theplus' ),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector' => '.fancybox-si .tp-fcb-title',
				'condition'   => [
					'FancyStyle' => ['style-1','style-2'],
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'FancyDes',
				'label' => esc_html__( 'Description Typography', 'theplus' ),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector' => '.fancybox-si .tp-message',
				'condition'   => [
					'FancyStyle' => ['style-1','style-2'],
				],
			]
		);
		$this->add_control(
			'FancyNameCr',
			[
				'label' => esc_html__( 'Name Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'.fancybox-si .tp-fcb-username a' => 'color: {{VALUE}}',					
				],
				'condition'   => [
					'FancyStyle' => ['style-1','style-2'],
				],
				'separator' => 'before',
			]
		);
		$this->add_control(
			'FancyTimeCr',
			[
				'label' => esc_html__( 'Time Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'.fancybox-si .tp-fcb-time a' => 'color: {{VALUE}}',					
				],
				'condition'   => [
					'FancyStyle' => ['style-1','style-2'],
				],
			]
		);
		$this->add_control(
			'FancytitleCr',
			[
				'label' => esc_html__( 'Title Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'.fancybox-si .tp-fcb-title' => 'color: {{VALUE}}',					
				],
				'condition'   => [
					'FancyStyle' => ['style-1','style-2'],
					'ShowTitle' => 'yes',
				],
			]
		);
		$this->add_control(
			'FancyDesCr',
			[
				'label' => esc_html__( 'Description Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'.fancybox-si .tp-message' => 'color: {{VALUE}}',					
				],
				'condition'   => [
					'FancyStyle' => ['style-1','style-2'],
				],
			]
		);
		$this->add_control(
			'FancyiconCr',
			[
				'label' => esc_html__( 'Footer Icon Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'.fancybox-si .tp-sf-footer span' => 'color: {{VALUE}}',					
				],
				'condition'   => [
					'FancyStyle' => ['style-1','style-2'],
				],
			]
		);
		$this->add_control(
			'FancpaginateCr',
			[
				'label' => esc_html__( 'Paginate Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'.fancybox-infobar' => 'color: {{VALUE}}',					
				],
				'condition'   => [
					'FancyStyle' => ['style-1','style-2'],
				],
			]
		);
		$this->add_control(
			'FancySICr',
			[
				'label' => esc_html__( 'Social Icon Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'.fancybox-si .social-logo-fb,.fancybox-si .social-logo-tw,.fancybox-si .social-logo-yt' => 'color: {{VALUE}}',					
				],
				'condition'   => [
					'FancyStyle' => ['style-1','style-2'],
				],
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
            'FancySIs',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Social Icon Size', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 100,
						'step' => 1,
					],
				],
				'render_type' => 'ui',
				'condition'   => [
					'FancyStyle' => ['style-1','style-2'],
				],
				'selectors' => [
					'.fancybox-si .social-logo-fb,.fancybox-si .social-logo-tw,.fancybox-si .social-logo-yt' => 'font-size: {{SIZE}}{{UNIT}};',
				],
            ]
        );
        $this->add_control(
			'Fancy_btn_opt',
			[
				'label' => esc_html__( 'Button Options', 'theplus' ),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
				'condition'   => [
					'FancyStyle' => ['style-1','style-2'],
				],
			]
		);
        $this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'FancyBtnCr',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '.fancybox-si .tp-fcb-footer .tp-btn-viewpost',
				'condition'   => [
					'FancyStyle' => ['style-1','style-2'],
				],
			]
		);
		$this->add_control(
			'FancyBtnTxtCr',
			[
				'label' => esc_html__( 'Button Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'.fancybox-si .tp-fcb-footer .tp-btn-viewpost a' => 'color: {{VALUE}}',					
				],
				'condition'   => [
					'FancyStyle' => ['style-1','style-2'],
				],
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'FancyBtnBr',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '.fancybox-si .tp-fcb-footer .tp-btn-viewpost',
				'separator' => 'before',
				'condition'   => [
					'FancyStyle' => ['style-1','style-2'],
				],
			]
		);
		$this->add_responsive_control(
			'FancyBtnpadd',
			[
				'label' => esc_html__( 'Box Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'.fancybox-si .tp-fcb-footer .tp-btn-viewpost' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'   => [
					'FancyStyle' => ['style-1','style-2'],
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();	
        $this->end_controls_section();
		/*FancyBox Option Style End*/
		/*Show More Text Option Start*/
		$this->start_controls_section(
            'shw_more_opt_styling',
            [
                'label' => esc_html__('Show More Text', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [					
					'TextLimit' => 'yes',
				],
            ]
        );
		$this->add_control(
			'ContentShowMore',
			[
				'label' => esc_html__( 'Content Show More', 'theplus' ),
				'type' => \Elementor\Controls_Manager::HEADING,
				'TextLimit' => 'yes',
			]
		);
        $this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'SmTxtTypo',
				'label' => esc_html__( 'Show More Typography', 'theplus' ),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector' => '{{WRAPPER}} .tp-social-feed .tp-message a.readbtn',
				'condition'   => [					
					'TextLimit' => 'yes',
				],
			]
		);
        $this->start_controls_tabs( 'shw_more_opt_tabs' );
		$this->start_controls_tab(
			'shw_more_opt_n',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
				'condition'   => [					
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
						'{{WRAPPER}} .tp-social-feed .tp-message a.readbtn' => 'color: {{VALUE}};',
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
						'{{WRAPPER}} .tp-social-feed .tp-message.show-less a.readbtn' => 'color: {{VALUE}};',
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
						'{{WRAPPER}} .tp-social-feed .tp-message .sf-dots' => 'color: {{VALUE}};',
					],
					'condition'   => [
						'TextLimit' => 'yes',
					],
					'separator' => 'after',
				]
			);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'shw_more_opt_h',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
				'condition'   => [
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
						'{{WRAPPER}} .tp-social-feed .tp-message a.readbtn:hover' => 'color: {{VALUE}};',
					],
					'condition'   => [
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
						'{{WRAPPER}} .tp-social-feed .tp-message.show-less a.readbtn:hover' => 'color: {{VALUE}};',
					],
					'condition'   => [
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
						'{{WRAPPER}} .tp-social-feed .tp-message:hover .sf-dots' => 'color: {{VALUE}};',
					],
					'condition'   => [
						'TextLimit' => 'yes',
					],
					'separator' => 'after',
				]
			);
			$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->add_control(
			'FancyShowMore',
			[
				'label' => esc_html__( 'Fancybox Show More', 'theplus' ),
				'type' => \Elementor\Controls_Manager::HEADING,
				'TextLimit' => 'yes',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'Fy_SmTxtTypo',
				'label' => esc_html__( 'Show More Typography', 'theplus' ),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector' => '.fancybox-si .tp-message a.readbtn',
				'condition'   => [
					'style'    => ['style-1','style-2','style-3','style-4'],
					'TextLimit' => 'yes',
				],
			]
		);	
		$this->start_controls_tabs( 'Fy_shw_more_opt_tabs' );
		$this->start_controls_tab(
			'Fy_shw_more_opt_n',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
				'condition'   => [
					'TextLimit' => 'yes',
				],
			]
		);
			$this->add_control(
				'Fy_SmTxtNCr',
				[
					'label' => esc_html__( 'Show More', 'theplus' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => [
						'.fancybox-si .tp-message a.readbtn' => 'color: {{VALUE}};',
					],
					'condition'   => [
						'TextLimit' => 'yes',
					],
				]
			);
			$this->add_control(
				'Fy_SlTxtNCr',
				[
					'label' => esc_html__( 'Show Less', 'theplus' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => [
						'.fancybox-si .tp-message.show-text a.readbtn' => 'color:{{VALUE}};',
					],
					'condition'   => [
						'TextLimit' => 'yes',
					],
				]
			);
			$this->add_control(
				'Fy_DotTxtNCr',
				[
					'label' => esc_html__( 'Dot Color', 'theplus' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => [
						'.fancybox-si .tp-message .sf-dots' => 'color: {{VALUE}};',
					],
					'condition'   => [
						'style'    => ['style-1','style-2','style-3','style-4'],
						'TextLimit' => 'yes',
					],
					'separator' => 'after',
				]
			);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'Fy_shw_more_opt_h',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
				'condition'   => [
					'TextLimit' => 'yes',
				],
			]
		);
			$this->add_control(
				'Fy_SmTxtHCr',
				[
					'label' => esc_html__( 'Show More', 'theplus' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => [
						'.fancybox-si .tp-message a.readbtn:hover' => 'color: {{VALUE}};',
					],
					'condition'   => [
						'TextLimit' => 'yes',
					],
				]
			);
			$this->add_control(
				'Fy_SlTxtHCr',
				[
					'label' => esc_html__( 'Show Less', 'theplus' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => [
						'.fancybox-si .tp-message.show-text a.readbtn:hover' => 'color: {{VALUE}};',
					],
					'condition'   => [
						'TextLimit' => 'yes',
					],
				]
			);
			$this->add_control(
				'Fy_DotTxtHCr',
				[
					'label' => esc_html__( 'Dot Color', 'theplus' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => [
						'.fancybox-si .tp-message:hover .sf-dots' => 'color: {{VALUE}};',
					],
					'condition'   => [
						'TextLimit' => 'yes',
					],
					'separator' => 'after',
				]
			);
			$this->end_controls_tab();
		$this->end_controls_tabs();
        $this->end_controls_section();
		/*Show More Text Option End*/

		/* Scroll Bar Option start*/
		$this->start_controls_section(
				'ScrollBarTab',
				[
					'label' => esc_html__( 'Scroll Bar', 'theplus' ),
					'tab' => Controls_Manager::TAB_STYLE,
					'conditions' => [
						'relation' => 'or',
						'terms' => [
							[ 'terms' => [ ['name' => 'ScrollOn', 'operator' => '===', 'value' => 'yes'] ] ],
							[ 'terms' => [ ['name' => 'FcySclOn', 'operator' => '===', 'value' => 'yes'] ] ],
						]
					],
				]
			);
			$this->add_control(
				'ContentScroll',
				[
					'label' => esc_html__( 'Content Scrolling Bar', 'theplus' ),
					'type' => \Elementor\Controls_Manager::HEADING,
					'condition' => [
						'ScrollOn'    => 'yes',
					],
				]
			);
			$this->start_controls_tabs( 'scrollC_style' );
			$this->start_controls_tab(
				'scrollC_Bar',
				[
					'label' => esc_html__( 'Scrollbar', 'theplus' ),
					'condition' => [
						'ScrollOn'    => 'yes',
					],
				]
			);
			$this->add_group_control(
				Group_Control_Background::get_type(),
				[
					'name'      => 'ScrollBg',
					'types'     => [ 'classic', 'gradient' ],
					'selector'  => '{{WRAPPER}} .tp-social-feed .tp-normal-scroll::-webkit-scrollbar',
					'condition' => [
						'ScrollOn'    => 'yes',
					],
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
						'{{WRAPPER}} .tp-social-feed .tp-normal-scroll::-webkit-scrollbar' => 'width: {{SIZE}}{{UNIT}};',
					],
					'condition' => [
						'ScrollOn'    => 'yes',
					],
				]
			);
			$this->end_controls_tab();
			$this->start_controls_tab(
				'scrollC_Tmb',
				[
					'label' => esc_html__( 'Thumb', 'theplus' ),
					'condition' => [
						'ScrollOn'    => 'yes',
					],
				]
			);
			$this->add_group_control(
				Group_Control_Background::get_type(),
				[
					'name'      => 'ThumbBg',
					'types'     => [ 'classic', 'gradient' ],
					'selector'  => '{{WRAPPER}} .tp-social-feed .tp-normal-scroll::-webkit-scrollbar-thumb',
					'condition' => [
						'ScrollOn'    => 'yes',
					],
				]
			);
			$this->add_responsive_control(
				'ThumbBrs',
				[
					'label'      => esc_html__( 'Border Radius', 'theplus' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%' ],
					'selectors'  => [
						'{{WRAPPER}} .tp-social-feed .tp-normal-scroll::-webkit-scrollbar-thumb' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',				
					],
					'condition' => [
						'ScrollOn'    => 'yes',
					],
				]
			);
			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'name'     => 'ThumbBsw',
					'selector' => '{{WRAPPER}} .tp-social-feed .tp-normal-scroll::-webkit-scrollbar-thumb',
					'condition' => [
						'ScrollOn'    => 'yes',
					],				
				]
			);
			$this->end_controls_tab();
			$this->start_controls_tab(
				'scrollC_Trk',
				[
					'label' => esc_html__( 'Track', 'theplus' ),
					'condition' => [
						'ScrollOn'    => 'yes',
					],
				]
			);
			$this->add_group_control(
				Group_Control_Background::get_type(),
				[
					'name'      => 'TrackBg',
					'types'     => [ 'classic', 'gradient' ],
					'selector'  => '{{WRAPPER}} .tp-social-feed .tp-normal-scroll::-webkit-scrollbar-track',
					'condition' => [
						'ScrollOn'    => 'yes',
					],
				]
			);
			$this->add_responsive_control(
				'TrackBRs',
				[
					'label'      => esc_html__( 'Border Radius', 'theplus' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%' ],
					'selectors'  => [
						'{{WRAPPER}} .tp-social-feed .tp-normal-scroll::-webkit-scrollbar-track' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',				
					],
					'condition' => [
						'ScrollOn'    => 'yes',
					],	
				]
			);
			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'name'     => 'TrackBsw',
					'selector' => '{{WRAPPER}} .tp-social-feed .tp-normal-scroll::-webkit-scrollbar-track',
					'condition' => [
						'ScrollOn'    => 'yes',
					],				
				]
			);
			$this->end_controls_tab();
			$this->end_controls_tabs();
			$this->add_control(
				'FancyboxScroll',
				[
					'label' => esc_html__( 'Fancybox Scrolling Bar', 'theplus' ),
					'type' => \Elementor\Controls_Manager::HEADING,
					'condition' => [
						'FcySclOn'    => 'yes',
					],
					'separator' => 'before',
				]
			);
			$this->start_controls_tabs( 'fancyC_style' );
			$this->start_controls_tab(
				'fancyC_Bar',
				[
					'label' => esc_html__( 'Scrollbar', 'theplus' ),
					'condition' => [
						'FcySclOn'    => 'yes',
					],
				]
			);
			$this->add_group_control(
				Group_Control_Background::get_type(),
				[
					'name'      => 'FcySclBg',
					'types'     => [ 'classic', 'gradient' ],
					'selector'  => '.fancybox-si .tp-fancy-scroll::-webkit-scrollbar',
					'condition' => [
						'FcySclOn'    => 'yes',
					],
				]
			);
			$this->add_responsive_control(
				'FcySclWidth',
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
						'.fancybox-si .tp-fancy-scroll::-webkit-scrollbar' => 'width: {{SIZE}}{{UNIT}};',
					],
					'condition' => [
						'FcySclOn'    => 'yes',
					],
				]
			);
			$this->end_controls_tab();
			$this->start_controls_tab(
				'fancyC_Tmb',
				[
					'label' => esc_html__( 'Thumb', 'theplus' ),
					'condition' => [
						'FcySclOn'    => 'yes',
					],
				]
			);
			$this->add_group_control(
				Group_Control_Background::get_type(),
				[
					'name'      => 'FcyThumbBg',
					'types'     => [ 'classic', 'gradient' ],
					'selector'  => '.fancybox-si .tp-fancy-scroll::-webkit-scrollbar-thumb',
					'condition' => [
						'FcySclOn'    => 'yes',
					],
				]
			);
			$this->add_responsive_control(
				'FcyThumbBrs',
				[
					'label'      => esc_html__( 'Border Radius', 'theplus' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%' ],
					'selectors'  => [
						'.fancybox-si .tp-fancy-scroll::-webkit-scrollbar-thumb' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
					'condition' => [
						'FcySclOn'    => 'yes',
					],
				]
			);
			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'name'     => 'FcyThumbBsw',
					'selector' => '.fancybox-si .tp-fancy-scroll::-webkit-scrollbar-thumb',
					'condition' => [
						'FcySclOn'    => 'yes',
					],				
				]
			);
			$this->end_controls_tab();
			$this->start_controls_tab(
				'fancyC_Trk',
				[
					'label' => esc_html__( 'Track', 'theplus' ),
					'condition' => [
						'FcySclOn'    => 'yes',
					],
				]
			);
			$this->add_group_control(
				Group_Control_Background::get_type(),
				[
					'name'      => 'FcyTrackBg',
					'types'     => [ 'classic', 'gradient' ],
					'selector'  => '.fancybox-si .tp-fancy-scroll::-webkit-scrollbar-track',
					'condition' => [
						'FcySclOn'    => 'yes',
					],
				]
			);
			$this->add_responsive_control(
				'FcyTrackBRs',
				[
					'label'      => esc_html__( 'Border Radius', 'theplus' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%' ],
					'selectors'  => [
						'.fancybox-si .tp-fancy-scroll::-webkit-scrollbar-track' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',				
					],
					'condition' => [
						'FcySclOn'    => 'yes',
					],	
				]
			);
			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'name'     => 'FcyTrackBsw',
					'selector' => '.fancybox-si .tp-fancy-scroll::-webkit-scrollbar-track',
					'condition' => [
						'FcySclOn'    => 'yes',
					],				
				]
			);
			$this->end_controls_tab();
			$this->end_controls_tabs();
		$this->end_controls_section();
		/* Scroll Bar Option End */

		/*Load More/Lazy Load style Start*/
		$this->start_controls_section(
			'LoadMoreStyle',
			[
				'label' => esc_html__( 'Load More Style', 'theplus' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'layout!' => 'carousel',
					'post_extra_option!' => 'none',
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
					'condition'   => [
						'layout!' => ['carousel'],
						'post_extra_option'    => 'load_more',
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
					'condition'   => [
						'layout!' => ['carousel'],
						'post_extra_option'    => ['load_more','lazy_load'],
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
					'condition'   => [
						'layout!' => ['carousel'],
						'post_extra_option'    => 'load_more',
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
						'layout!' => ['carousel'],
						'post_extra_option'    => 'load_more',
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
						'top'    => 1,
						'right'  => 1,
						'bottom' => 1,
						'left'   => 1,
					],
					'selectors'  => [
						'{{WRAPPER}} .ajax_load_more .post-load-more' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
					'condition' => [
						'layout!' => ['carousel'],
						'post_extra_option'    => 'load_more',
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
							'layout!' => ['carousel'],
							'post_extra_option'    => 'load_more',
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
							'layout!' => ['carousel'],
							'post_extra_option'    => 'load_more',
							'load_more_border' => 'yes',
						],
					]
				);		
				$this->add_responsive_control(
					'load_more_border_radius',
					[
						'label'      => esc_html__( 'Border Radius', 'theplus' ),
						'type'       => Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%' ],
						'selectors'  => [
							'{{WRAPPER}} .ajax_load_more .post-load-more' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
						'separator' => 'after',
						'condition' => [
							'layout!' => ['carousel'],
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
							'layout!' => ['carousel'],
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
							'layout!' => ['carousel'],
							'post_extra_option'    => 'load_more',
							'load_more_border' => 'yes',
						],
					]
				);
				$this->add_responsive_control(
					'load_more_border_hover_radius',
					[
						'label'      => esc_html__( 'Border Radius', 'theplus' ),
						'type'       => Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%' ],
						'selectors'  => [
							'{{WRAPPER}} .ajax_load_more .post-load-more:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
						'separator' => 'after',
						'condition' => [
							'layout!' => ['carousel'],
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
							'layout!' => ['carousel'],
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
							'layout!' => ['carousel'],
							'post_extra_option'    => 'load_more',
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
							'layout!' => ['carousel'],
							'post_extra_option'    => ['load_more','lazy_load'],
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
							'layout!' => ['carousel'],
							'post_extra_option'    => 'lazy_load',
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
							'layout!' => ['carousel'],
							'post_extra_option'    => 'lazy_load',
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
							'layout!' => ['carousel'],
							'post_extra_option'    => 'lazy_load',
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
							'layout!' => ['carousel'],
							'post_extra_option'    => 'lazy_load',
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
							'layout!' => ['carousel'],
							'post_extra_option'    => 'load_more',
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
							'layout!' => ['carousel'],
							'post_extra_option'    => 'load_more',
						],
					]
				);
				$this->add_group_control(
					Group_Control_Box_Shadow::get_type(),
					[
						'name'     => 'load_more_shadow',
						'selector' => '{{WRAPPER}} .ajax_load_more .post-load-more',
						'condition' => [
							'layout!' => ['carousel'],
							'post_extra_option'    => 'load_more',
						],
					]
				);
			$this->end_controls_tab();
			$this->start_controls_tab(
				'tab_load_more_hover',
				[
					'label' => esc_html__( 'Hover', 'theplus' ),
					'condition' => [
						'layout!' => ['carousel'],
						'post_extra_option'    => 'load_more',
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
						'layout!' => ['carousel'],
						'post_extra_option'    => 'load_more',
					],
				]
			);
			$this->add_group_control(
				Group_Control_Background::get_type(),
				[
					'name'      => 'load_more_hover_background',
					'types'     => [ 'classic', 'gradient' ],
					'selector'  => '{{WRAPPER}} .ajax_load_more .post-load-more:hover',
					'separator' => 'after',
					'condition' => [
						'layout!' => ['carousel'],
						'post_extra_option'    => 'load_more',
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
						'layout!' => ['carousel'],
						'post_extra_option'    => 'load_more',
					],
				]
			);
			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'name'     => 'load_more_hover_shadow',
					'selector' => '{{WRAPPER}} .ajax_load_more .post-load-more:hover',
					'condition' => [
						'layout!' => ['carousel'],
						'post_extra_option'    => 'load_more',
					],
				]
			);
			$this->end_controls_tab();
			$this->end_controls_tabs();
		$this->end_controls_section();
		/*Load More/Lazy Load style End*/

		/*Carousel Option Start*/
		$this->start_controls_section(
            'section_carousel_options_styling',
            [
                'label' => esc_html__('Carousel', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'layout' => 'carousel',
				],
            ]
        );
		$this->add_control(
			'slider_direction',
			[
				'label'   => esc_html__( 'Slider Mode', 'theplus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'horizontal',
				'options' => [
					'horizontal'  => esc_html__( 'Horizontal', 'theplus' ),
					'vertical' => esc_html__( 'Vertical', 'theplus' ),
				],
			]
		);	
		$this->add_control(
			'carousel_direction',
			[
				'label' => esc_html__( 'Slide Direction', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'rtl',
				'options' => [
					'rtl'  => esc_html__( 'Right to Left', 'theplus' ),
					'ltr' => esc_html__( 'Left to Right', 'theplus' ),
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
				'label'   => esc_html__( 'Desktop Columns', 'theplus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '4',
				'options' => theplus_carousel_desktop_columns(),
			]
		);
		$this->add_control(
			'steps_slide',
			[
				'label'   => esc_html__( 'Next Previous', 'theplus' ),
				'type'    => Controls_Manager::SELECT,
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
				'default' =>[
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
				'label'   => esc_html__( 'Draggable', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'yes',
			]
		);
		$this->add_control(
			'slider_infinite',
			[
				'label'   => esc_html__( 'Infinite Mode', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'yes',
			]
		);
		$this->add_control(
			'slider_pause_hover',
			[
				'label'   => esc_html__( 'Pause On Hover', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'no',
			]
		);
		$this->add_control(
			'slider_adaptive_height',
			[
				'label'   => esc_html__( 'Adaptive Height', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'no',
			]
		);
		$this->add_control(
			'slider_animation',
			[
				'label'   => esc_html__( 'Animation Type', 'theplus' ),
				'type'    => Controls_Manager::SELECT,
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
				'label'   => esc_html__( 'Autoplay', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
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
				'label'   => esc_html__( 'Show Dots', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'yes',
				'separator' => 'before',
			]
		);
		$this->add_control(
			'slider_dots_style',
			[
				'label'   => esc_html__( 'Dots Style', 'theplus' ),
				'type'    => Controls_Manager::SELECT,
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
				'condition'    => [
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
				'condition'    => [
					'slider_dots' => 'yes',
				],
            ]
        );
		$this->add_control(
			'hover_show_dots',
			[
				'label'   => esc_html__( 'On Hover Dots', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'no',
				'condition'    => [
					'slider_dots' => 'yes',
				],
			]
		);
		$this->add_control(
			'slider_arrows',
			[
				'label'   => esc_html__( 'Show Arrows', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'no',
				'separator' => 'before',
			]
		);
		$this->add_control(
			'slider_arrows_style',
			[
				'label'   => esc_html__( 'Arrows Style', 'theplus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'style-1',
				'options' => [
					'style-1' => esc_html__( 'Style 1', 'theplus' ),
					'style-2' => esc_html__( 'Style 2', 'theplus' ),
					'style-3' => esc_html__( 'Style 3', 'theplus' ),
					'style-4' => esc_html__( 'Style 4', 'theplus' ),
					'style-5' => esc_html__( 'Style 5', 'theplus' ),
					'style-6' => esc_html__( 'Style 6', 'theplus' ),
				],
				'condition'    => [
					'slider_arrows' => ['yes'],
				],
			]
		);
		$this->add_control(
			'arrows_position',
			[
				'label'   => esc_html__( 'Arrows Style', 'theplus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'top-right',
				'options' => [
					'top-right' => esc_html__( 'Top-Right', 'theplus' ),
					'bottm-left' => esc_html__( 'Bottom-Left', 'theplus' ),
					'bottom-center' => esc_html__( 'Bottom-Center', 'theplus' ),
					'bottom-right' => esc_html__( 'Bottom-Right', 'theplus' ),
				],				
				'condition'    => [
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
					'slider_arrows_style' => ['style-1','style-2','style-3','style-4','style-5','style-6'],
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
					'slider_arrows_style' => ['style-1','style-2','style-3','style-4','style-5','style-6'],
					'slider_arrows' => 'yes',
				],
			]
		);
		$this->add_control(
			'outer_section_arrow',
			[
				'label'   => esc_html__( 'Outer Content Arrow', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'no',
				'condition'    => [
					'slider_arrows' => 'yes',
					'slider_arrows_style' => ['style-1','style-2','style-5','style-6'],
				],
			]
		);
		$this->add_control(
			'hover_show_arrow',
			[
				'label'   => esc_html__( 'On Hover Arrow', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'no',
				'condition'    => [
					'slider_arrows' => 'yes',
				],
			]
		);
		$this->add_control(
			'slider_center_mode',
			[
				'label'   => esc_html__( 'Center Mode', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
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
				'label'   => esc_html__( 'Center Slide Effects', 'theplus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'none',
				'options' => theplus_carousel_center_effects(),
				'condition'    => [
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
				'name'     => 'shadow_active_slide',
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
				'label'   => esc_html__( 'Number Of Rows', 'theplus' ),
				'type'    => Controls_Manager::SELECT,
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
				'condition'    => [
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
				'label'   => esc_html__( 'Tablet Columns', 'theplus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '3',
				'options' => theplus_carousel_tablet_columns(),
			]
		);
		$this->add_control(
			'tablet_steps_slide',
			[
				'label'   => esc_html__( 'Next Previous', 'theplus' ),
				'type'    => Controls_Manager::SELECT,
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
				'label'   => esc_html__( 'Responsive Tablet', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'no',
			]
		);
		$this->add_control(
			'tablet_slider_draggable',
			[
				'label'   => esc_html__( 'Draggable', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'yes',
				'condition'    => [
					'slider_responsive_tablet' => 'yes',
				],
			]
		);
		$this->add_control(
			'tablet_slider_infinite',
			[
				'label'   => esc_html__( 'Infinite Mode', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'yes',
				'condition'    => [
					'slider_responsive_tablet' => 'yes',
				],
			]
		);
		$this->add_control(
			'tablet_slider_autoplay',
			[
				'label'   => esc_html__( 'Autoplay', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'yes',
				'condition'    => [
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
				'label'   => esc_html__( 'Show Dots', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'yes',
				'condition'    => [
					'slider_responsive_tablet' => 'yes',
				],
			]
		);
		$this->add_control(
			'tablet_slider_arrows',
			[
				'label'   => esc_html__( 'Show Arrows', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'no',
				'condition'    => [
					'slider_responsive_tablet' => 'yes',
				],
			]
		);
		$this->add_control(
			'tablet_slider_rows',
			[
				'label'   => esc_html__( 'Number Of Rows', 'theplus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '1',
				'options' => [
					"1" => esc_html__("1 Row", 'theplus'),
					"2" => esc_html__("2 Rows", 'theplus'),
					"3" => esc_html__("3 Rows", 'theplus'),
				],
				'condition'    => [
					'slider_responsive_tablet' => 'yes',
				],
			]
		);
		$this->add_control(
			'tablet_center_mode',
			[
				'label'   => esc_html__( 'Center Mode', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'no',
				'separator' => 'before',
				'condition'    => [
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
				'label'   => esc_html__( 'Mobile Columns', 'theplus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '2',
				'options' => theplus_carousel_mobile_columns(),
			]
		);
		$this->add_control(
			'mobile_steps_slide',
			[
				'label'   => esc_html__( 'Next Previous', 'theplus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '1',
				'description' => esc_html__( 'Select option of column scroll on previous or next in carousel.','theplus' ),
				'options' => [
					'1'  => esc_html__( 'One Column', 'theplus' ),
					'2' => esc_html__( 'All Visible Columns', 'theplus' ),
				],
			]
		);		
		$this->add_control(
			'slider_responsive_mobile',
			[
				'label'   => esc_html__( 'Responsive Mobile', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'no',
			]
		);
		$this->add_control(
			'mobile_slider_draggable',
			[
				'label'   => esc_html__( 'Draggable', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'yes',
				'condition'    => [
					'slider_responsive_mobile' => 'yes',
				],
			]
		);
		$this->add_control(
			'mobile_slider_infinite',
			[
				'label'   => esc_html__( 'Infinite Mode', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'yes',
				'condition'    => [
					'slider_responsive_mobile' => 'yes',
				],
			]
		);
		$this->add_control(
			'mobile_slider_autoplay',
			[
				'label'   => esc_html__( 'Autoplay', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'yes',
				'condition'    => [
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
				'label'   => esc_html__( 'Show Dots', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
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
				'label'   => esc_html__( 'Show Arrows', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'no',
				'condition'    => [
					'slider_responsive_mobile' => 'yes',
				],
			]
		);
		$this->add_control(
			'mobile_slider_rows',
			[
				'label'   => esc_html__( 'Number Of Rows', 'theplus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '1',
				'options' => [
					"1" => esc_html__("1 Row", 'theplus'),
					"2" => esc_html__("2 Rows", 'theplus'),
					"3" => esc_html__("3 Rows", 'theplus'),
				],
				'condition'    => [
					'slider_responsive_mobile' => 'yes',
				],
			]
		);
		$this->add_control(
			'mobile_center_mode',
			[
				'label'   => esc_html__( 'Center Mode', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'no',
				'separator' => 'before',
				'condition'    => [
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
				'separator' => 'before',
				'description' => esc_html__('Keep this blank or Setup Unique id for carousel which you can use with "Carousel Remote" widget.','theplus'),
			]
		);
		$this->end_controls_section();
		/*Carousel Option End*/
		/*Filter Category Style Start*/
		$this->start_controls_section(
            'section_filter_category_styling',
            [
                'label' => esc_html__('Filter Category', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
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
				'name'      => 'filter_category_background',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .pt-plus-filter-post-category .category-filters.hover-style-2 li a span:not(.all_post_count),{{WRAPPER}} .pt-plus-filter-post-category .category-filters.hover-style-4 li a:after',
				'separator' => 'before',
				'condition' => [
					'filter_hover_style' => ['style-2','style-4'],
				],
			]
		);
		$this->add_responsive_control(
			'filter_category_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
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
				'name'     => 'filter_category_shadow',
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
				'name'      => 'filter_category_hover_background',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .pt-plus-filter-post-category .category-filters.hover-style-2 li a span:not(.all_post_count)::before',
				'separator' => 'before',
				'condition' => [
					'filter_hover_style' => 'style-2',
				],				
			]
		);
		$this->add_responsive_control(
			'filter_category_hover_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
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
				'name'     => 'filter_category_hover_shadow',
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
				'name'      => 'category_count_background',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .pt-plus-filter-post-category .category-filters.style-1 li a.all span.all_post_count',
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
				'name'     => 'filter_category_count_shadow',
				'selector' => '{{WRAPPER}} .pt-plus-filter-post-category .category-filters.style-1 li a.all span.all_post_count',
				'separator' => 'before',
				'condition' => [
					'filter_style' => ['style-1'],
				],
			]
		);
		$this->end_controls_section();
		/*Filter Category Style End*/
		/*Facebook Style Start*/
		$this->start_controls_section(
            'section_facebook_styling',
            [
                'label' => esc_html__('Facebook', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'FbMsgTp',
				'label' => esc_html__( 'Extra Title Typography', 'theplus' ),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector' => '{{WRAPPER}} .tp-social-feed .feed-Facebook .tp-sf-feed .tp-title',
				'condition'   => [
					'ShowTitle' => 'yes',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'FbDesTp',
				'label' => esc_html__( 'Description Typography', 'theplus' ),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector' => '{{WRAPPER}} .tp-social-feed .feed-Facebook .tp-sf-feed .tp-message',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'FbNameTp',
				'label' => esc_html__( 'Name Typography', 'theplus' ),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector' => '{{WRAPPER}} .tp-social-feed .feed-Facebook .tp-sf-feed .tp-sf-username a',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'FbTimeTp',
				'label' => esc_html__( 'Time Typography', 'theplus' ),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector' => '{{WRAPPER}} .tp-social-feed .feed-Facebook .tp-sf-feed .tp-sf-time a',
			]
		);
		$this->start_controls_tabs( 'sfd_fb_clr_tabs' );
		$this->start_controls_tab(
			'sfd_fb_clr_n',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);
		$this->add_control(
			'FbNTitleC',
			[
				'label' => esc_html__( 'Title Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .tp-social-feed .feed-Facebook .tp-sf-feed .tp-title' => 'color: {{VALUE}}',					
				],
				'condition'   => [
					'ShowTitle' => 'yes',
				]
			]
		);
		$this->add_control(
			'FbNDesC',
			[
				'label' => esc_html__( 'Description Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .tp-social-feed .feed-Facebook .tp-sf-feed .tp-message' => 'color: {{VALUE}}',					
				],
			]
		);	
		$this->add_control(
			'FbNNameC',
			[
				'label' => esc_html__( 'Name Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .tp-social-feed .feed-Facebook .tp-sf-feed .tp-sf-username a' => 'color: {{VALUE}}',					
				],
			]
		);
		$this->add_control(
			'FbNTimeC',
			[
				'label' => esc_html__( 'Time Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .tp-social-feed .feed-Facebook .tp-sf-feed .tp-sf-time a' => 'color: {{VALUE}}',					
				],
			]
		);
		$this->add_control(
			'FbNIconCr',
			[
				'label' => esc_html__( 'Icon Footer Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .tp-social-feed .feed-Facebook .tp-sf-feed .tp-sf-footer' => 'color: {{VALUE}}',
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'sfd_fb_clr_h',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
		$this->add_control(
			'FbHTitleC',
			[
				'label' => esc_html__( 'Title Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .tp-social-feed .feed-Facebook:hover .tp-sf-feed .tp-title' => 'color: {{VALUE}}',					
				],
				'condition'   => [
					'ShowTitle' => 'yes',
				]
			]
		);
		$this->add_control(
			'FbHDesC',
			[
				'label' => esc_html__( 'Description Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .tp-social-feed .feed-Facebook:hover .tp-sf-feed .tp-message' => 'color: {{VALUE}}',					
				],
			]
		);
		$this->add_control(
			'FbHNameC',
			[
				'label' => esc_html__( 'Name Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .tp-social-feed .feed-Facebook:hover .tp-sf-feed .tp-sf-username a' => 'color: {{VALUE}}',					
				],
			]
		);
		$this->add_control(
			'FbHTimeC',
			[
				'label' => esc_html__( 'Time Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .tp-social-feed .feed-Facebook:hover .tp-sf-feed .tp-sf-time a' => 'color: {{VALUE}}',					
				],
			]
		);
		$this->add_control(
			'FbHIconCr',
			[
				'label' => esc_html__( 'Icon Footer Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .tp-social-feed .feed-Facebook:hover .tp-sf-feed .tp-sf-footer' => 'color: {{VALUE}}',
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->start_controls_tabs( 'sfd_fb_tabs' );
		$this->start_controls_tab(
			'sfd_fb_n',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);	
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'FbNBgCr',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .tp-social-feed .feed-Facebook .tp-sf-feed',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'FbNBcr',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-social-feed .feed-Facebook .tp-sf-feed',
			]
		);
		$this->add_responsive_control(
			'FbNBRcr',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-social-feed .feed-Facebook .tp-sf-feed' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',				
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'FbNBs',
				'selector' => '{{WRAPPER}} .tp-social-feed .feed-Facebook .tp-sf-feed',
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'sfd_fb_h',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'FbHBgCr',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .tp-social-feed .feed-Facebook:hover .tp-sf-feed',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'FbHBcr',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-social-feed .feed-Facebook:hover .tp-sf-feed',
			]
		);
		$this->add_responsive_control(
			'FbHBRcr',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-social-feed .feed-Facebook:hover .tp-sf-feed' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',				
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'FbHBs',
				'selector' => '{{WRAPPER}} .tp-social-feed .feed-Facebook:hover .tp-sf-feed',
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->add_responsive_control(
			'FbPRs',
			[
				'label'      => esc_html__( 'Profile Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-social-feed .feed-Facebook .tp-sf-feed .tp-sf-logo' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',				
				],
				'separator' => 'before',	
			]
		);
		$this->add_responsive_control(
            'SocIconSizefb',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Icon Size', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 150,
						'step' => 1,
					],
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .tp-social-feed .fa-facebook.social-logo-fb' => 'font-size: {{SIZE}}{{UNIT}};',
				],
				'separator' => 'before',
            ]
        );
		$this->add_control(
			'SocIconColorfb',
			[
				'label' => esc_html__( 'Icon Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .tp-social-feed .fa-facebook.social-logo-fb' => 'color: {{VALUE}}',					
				],
			]
		);
        $this->end_controls_section();
		/*Facebook Style End*/
		/*Vimeo Style Start*/
		$this->start_controls_section(
            'section_vimeo_styling',
            [
                'label' => esc_html__('Vimeo', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'VmMsgTp',
				'label' => esc_html__( 'Extra Title Typography', 'theplus' ),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector' => '{{WRAPPER}} .tp-social-feed .feed-Vimeo .tp-sf-feed .tp-title',
				'condition'   => [
					'ShowTitle' => 'yes',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'VmDesTp',
				'label' => esc_html__( 'Description Typography', 'theplus' ),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector' => '{{WRAPPER}} .tp-social-feed .feed-Vimeo .tp-sf-feed .tp-message',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'VmNameTp',
				'label' => esc_html__( 'Name Typography', 'theplus' ),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector' => '{{WRAPPER}} .tp-social-feed .feed-Vimeo .tp-sf-feed .tp-sf-username a',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'VmTimeTp',
				'label' => esc_html__( 'Time Typography', 'theplus' ),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector' => '{{WRAPPER}} .tp-social-feed .feed-Vimeo .tp-sf-feed .tp-sf-time a',
			]
		);
		$this->start_controls_tabs( 'sfd_vm_clr_tabs' );
		$this->start_controls_tab(
			'sfd_vm_clr_n',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);	
		$this->add_control(
			'VmNTitleC',
			[
				'label' => esc_html__( 'Title Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .tp-social-feed .feed-Vimeo .tp-sf-feed .tp-title' => 'color: {{VALUE}}',					
				],
				'condition'   => [
					'ShowTitle' => 'yes',
				]
			]
		);
		$this->add_control(
			'VmNDesC',
			[
				'label' => esc_html__( 'Description Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .tp-social-feed .feed-Vimeo .tp-sf-feed .tp-message' => 'color: {{VALUE}}',					
				],
			]
		);
		$this->add_control(
			'VmNNameC',
			[
				'label' => esc_html__( 'Name Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .tp-social-feed .feed-Vimeo .tp-sf-feed .tp-sf-username a' => 'color: {{VALUE}}',					
				],
			]
		);
		$this->add_control(
			'VmNTimeC',
			[
				'label' => esc_html__( 'Time Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .tp-social-feed .feed-Vimeo .tp-sf-feed .tp-sf-time a' => 'color: {{VALUE}}',					
				],
			]
		);
		$this->add_control(
			'VmNIconCr',
			[
				'label' => esc_html__( 'Icon Footer Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .tp-social-feed .feed-Vimeo .tp-sf-feed .tp-sf-footer' => 'color: {{VALUE}}',				
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'sfd_vm_clr_h',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
		$this->add_control(
			'VmHTitleC',
			[
				'label' => esc_html__( 'Title Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .tp-social-feed .feed-Vimeo:hover .tp-sf-feed .tp-title' => 'color: {{VALUE}}',					
				],
				'condition'   => [
					'ShowTitle' => 'yes',
				]
			]
		);
		$this->add_control(
			'VmHDesC',
			[
				'label' => esc_html__( 'Description Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .tp-social-feed .feed-Vimeo:hover .tp-sf-feed .tp-message' => 'color: {{VALUE}}',					
				],
			]
		);
		$this->add_control(
			'VmHNameC',
			[
				'label' => esc_html__( 'Name Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .tp-social-feed .feed-Vimeo:hover .tp-sf-feed .tp-sf-username a' => 'color: {{VALUE}}',					
				],
			]
		);
		$this->add_control(
			'VmHTimeC',
			[
				'label' => esc_html__( 'Time Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .tp-social-feed .feed-Vimeo:hover .tp-sf-feed .tp-sf-time a' => 'color: {{VALUE}}',					
				],
			]
		);
		$this->add_control(
			'VmHIconCr',
			[
				'label' => esc_html__( 'Icon Footer Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .tp-social-feed .feed-Vimeo:hover .tp-sf-feed .tp-sf-footer' => 'color: {{VALUE}}',
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->start_controls_tabs( 'sfd_vm_tabs' );
		$this->start_controls_tab(
			'sfd_vm_n',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);	
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'VmNBgCr',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .tp-social-feed .feed-Vimeo .tp-sf-feed',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'VmNBcr',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-social-feed .feed-Vimeo .tp-sf-feed',
			]
		);
		$this->add_responsive_control(
			'VmNBRs',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-social-feed .feed-Vimeo .tp-sf-feed' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',				
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'VmNBs',
				'selector' => '{{WRAPPER}} .tp-social-feed .feed-Vimeo .tp-sf-feed',
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'sfd_vm_h',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'VmHBgCr',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .tp-social-feed .feed-Vimeo:hover .tp-sf-feed',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'VmHBcr',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-social-feed .feed-Vimeo:hover .tp-sf-feed',
			]
		);
		$this->add_responsive_control(
			'VmHBrs',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-social-feed .feed-Vimeo:hover .tp-sf-feed' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',				
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'VmHBs',
				'selector' => '{{WRAPPER}} .tp-social-feed .feed-Vimeo:hover .tp-sf-feed',
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->add_responsive_control(
			'VmPRs',
			[
				'label'      => esc_html__( 'Profile Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-social-feed .feed-Vimeo .tp-sf-feed .tp-sf-logo' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',				
				],	
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
            'SocIconSizevimeo',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Icon Size', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 150,
						'step' => 1,
					],
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .tp-social-feed .fa-vimeo-v.social-logo-yt' => 'font-size: {{SIZE}}{{UNIT}};',
				],
				'separator' => 'before',
            ]
        );
		$this->add_control(
			'SocIconColorvimeo',
			[
				'label' => esc_html__( 'Icon Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .tp-social-feed .fa-vimeo-v.social-logo-yt' => 'color: {{VALUE}}',					
				],
			]
		);
        $this->end_controls_section();
		/*Vimeo Style End*/
		/*Youtube Style Start*/
		$this->start_controls_section(
            'section_ytube_styling',
            [
                'label' => esc_html__('Youtube', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'YtMsgTp',
				'label' => esc_html__( 'Extra Title Typography', 'theplus' ),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector' => '{{WRAPPER}} .tp-social-feed .feed-Youtube .tp-sf-feed .tp-title',
				'condition'   => [
					'ShowTitle' => 'yes',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'YtDesTp',
				'label' => esc_html__( 'Description Typography', 'theplus' ),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector' => '{{WRAPPER}} .tp-social-feed .feed-Youtube .tp-sf-feed .tp-message',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'YtNameTp',
				'label' => esc_html__( 'Name Typography', 'theplus' ),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector' => '{{WRAPPER}} .tp-social-feed .feed-Youtube .tp-sf-feed .tp-sf-username a',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'YtTimeTp',
				'label' => esc_html__( 'Time Typography', 'theplus' ),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector' => '{{WRAPPER}} .tp-social-feed .feed-Youtube .tp-sf-feed .tp-sf-time a',
			]
		);
		$this->start_controls_tabs( 'sfd_ytube_clr_tabs' );
		$this->start_controls_tab(
			'sfd_ytube_clr_n',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);	
		$this->add_control(
			'YtNTitleC',
			[
				'label' => esc_html__( 'Title Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .tp-social-feed .feed-Youtube .tp-sf-feed .tp-title' => 'color: {{VALUE}}',					
				],
				'condition'   => [
					'ShowTitle' => 'yes',
				]
			]
		);
		$this->add_control(
			'YtNDesC',
			[
				'label' => esc_html__( 'Description Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .tp-social-feed .feed-Youtube .tp-sf-feed .tp-message' => 'color: {{VALUE}}',					
				],
			]
		);
		$this->add_control(
			'YtNNameC',
			[
				'label' => esc_html__( 'Name Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .tp-social-feed .feed-Youtube .tp-sf-feed .tp-sf-username a' => 'color: {{VALUE}}',					
				],
			]
		);
		$this->add_control(
			'YtNTimeC',
			[
				'label' => esc_html__( 'Time Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .tp-social-feed .feed-Youtube .tp-sf-feed .tp-sf-time a' => 'color: {{VALUE}}',					
				],
			]
		);
		$this->add_control(
			'YtNIconCr',
			[
				'label' => esc_html__( 'Icon Footer Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .tp-social-feed .feed-Youtube .tp-sf-feed .tp-sf-footer' => 'color: {{VALUE}}',					
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'sfd_ytube_clr_h',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
		$this->add_control(
			'YtHTitleC',
			[
				'label' => esc_html__( 'Title Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .tp-social-feed .feed-Youtube:hover .tp-sf-feed .tp-title' => 'color: {{VALUE}}',					
				],
				'condition'   => [
					'ShowTitle' => 'yes',
				]
			]
		);
		$this->add_control(
			'YtHDesC',
			[
				'label' => esc_html__( 'Description Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .tp-social-feed .feed-Youtube:hover .tp-sf-feed .tp-message' => 'color: {{VALUE}}',					
				],
			]
		);
		$this->add_control(
			'YtHNameC',
			[
				'label' => esc_html__( 'Name Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .tp-social-feed .feed-Youtube:hover .tp-sf-feed .tp-sf-username a' => 'color: {{VALUE}}',					
				],
			]
		);
		$this->add_control(
			'YtHTimeC',
			[
				'label' => esc_html__( 'Time Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .tp-social-feed .feed-Youtube:hover .tp-sf-feed .tp-sf-time a' => 'color: {{VALUE}}',					
				],
			]
		);
		$this->add_control(
			'YtHIconCr',
			[
				'label' => esc_html__( 'Icon Footer Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .tp-social-feed .feed-Youtube:hover .tp-sf-feed .tp-sf-footer' => 'color: {{VALUE}}',
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->start_controls_tabs( 'sfd_ytube_tabs' );
		$this->start_controls_tab(
			'sfd_ytube_n',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);	
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'YtNBgCr',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .tp-social-feed .feed-Youtube .tp-sf-feed',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'YtNBcr',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-social-feed .feed-Youtube .tp-sf-feed',
			]
		);
		$this->add_responsive_control(
			'YtNBrs',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-social-feed .feed-Youtube .tp-sf-feed' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',				
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'YtNBs',
				'selector' => '{{WRAPPER}} .tp-social-feed .feed-Youtube .tp-sf-feed',
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'sfd_ytube_h',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'YtHBgCr',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .tp-social-feed .feed-Youtube:hover .tp-sf-feed',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'YtHBcr',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-social-feed .feed-Youtube:hover .tp-sf-feed',
			]
		);
		$this->add_responsive_control(
			'YtHBrs',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-social-feed .feed-Youtube:hover .tp-sf-feed' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',				
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'YtHBs',
				'selector' => '{{WRAPPER}} .tp-social-feed .feed-Youtube:hover .tp-sf-feed',
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->add_responsive_control(
			'YtPRs',
			[
				'label'      => esc_html__( 'Profile Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-social-feed .feed-Youtube .tp-sf-feed .tp-sf-logo' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',				
				],	
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
            'SocIconSizeyoutube',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Icon Size', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 150,
						'step' => 1,
					],
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .tp-social-feed .fa-youtube.social-logo-yt' => 'font-size: {{SIZE}}{{UNIT}};',
				],
				'separator' => 'before',
            ]
        );
		$this->add_control(
			'SocIconColoryoutube',
			[
				'label' => esc_html__( 'Icon Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .tp-social-feed .fa-youtube.social-logo-yt' => 'color: {{VALUE}}',					
				],
			]
		);
        $this->end_controls_section();
		/*Youtube Style End*/
		/*Twitter Style Start*/
		$this->start_controls_section(
            'section_twitter_styling',
            [
                'label' => esc_html__('Twitter', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'TwMsgTp',
				'label' => esc_html__( 'Extra Title Typography', 'theplus' ),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector' => '{{WRAPPER}} .tp-social-feed .feed-Twitter .tp-sf-feed .tp-title',
				'condition'   => [
					'ShowTitle' => 'yes',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'TwDesTp',
				'label' => esc_html__( 'Description Typography', 'theplus' ),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector' => '{{WRAPPER}} .tp-social-feed .feed-Twitter .tp-sf-feed .tp-message',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'TwNameTp',
				'label' => esc_html__( 'Name Typography', 'theplus' ),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector' => '{{WRAPPER}} .tp-social-feed .feed-Twitter .tp-sf-feed .tp-sf-username a',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'TwTimeTp',
				'label' => esc_html__( 'Time Typography', 'theplus' ),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector' => '{{WRAPPER}} .tp-social-feed .feed-Twitter .tp-sf-feed .tp-sf-time a',
			]
		);
		$this->start_controls_tabs( 'sfd_twitter_clr_tabs' );
		$this->start_controls_tab(
			'sfd_twitter_clr_n',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);
		$this->add_control(
			'TwNTitleC',
			[
				'label' => esc_html__( 'Title Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .tp-social-feed .feed-Twitter .tp-sf-feed .tp-title' => 'color: {{VALUE}}',					
				],
				'condition'   => [
					'ShowTitle' => 'yes',
				]
			]
		);
		$this->add_control(
			'TwNDesC',
			[
				'label' => esc_html__( 'Description Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .tp-social-feed .feed-Twitter .tp-sf-feed .tp-message' => 'color: {{VALUE}}',					
				],
			]
		);	
		$this->add_control(
			'TwNNameC',
			[
				'label' => esc_html__( 'Name Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .tp-social-feed .feed-Twitter .tp-sf-feed .tp-sf-username a' => 'color: {{VALUE}}',					
				],
			]
		);
		$this->add_control(
			'TwNTimeC',
			[
				'label' => esc_html__( 'Time Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .tp-social-feed .feed-Twitter .tp-sf-feed .tp-sf-time a' => 'color: {{VALUE}}',					
				],
			]
		);
		$this->add_control(
			'TwNIconCr',
			[
				'label' => esc_html__( 'Icon Footer Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .tp-social-feed .feed-Twitter .tp-sf-feed .tp-sf-footer *' => 'color: {{VALUE}}',					
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'sfd_twitter_clr_h',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
		$this->add_control(
			'TwHTitleC',
			[
				'label' => esc_html__( 'Title Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .tp-social-feed .feed-Twitter:hover .tp-sf-feed .tp-title' => 'color: {{VALUE}}',					
				],
				'condition'   => [
					'ShowTitle' => 'yes',
				]
			]
		);
		$this->add_control(
			'TwHDesC',
			[
				'label' => esc_html__( 'Description Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .tp-social-feed .feed-Twitter:hover .tp-sf-feed .tp-message' => 'color: {{VALUE}}',					
				],
			]
		);
		$this->add_control(
			'TwHNameC',
			[
				'label' => esc_html__( 'Name Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .tp-social-feed .feed-Twitter:hover .tp-sf-feed .tp-sf-username a' => 'color: {{VALUE}}',					
				],
			]
		);
		$this->add_control(
			'TwHTimeC',
			[
				'label' => esc_html__( 'Time Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .tp-social-feed .feed-Twitter:hover .tp-sf-feed .tp-sf-time a' => 'color: {{VALUE}}',					
				],
			]
		);
		$this->add_control(
			'TwHIconCr',
			[
				'label' => esc_html__( 'Icon Footer Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .tp-social-feed .feed-Twitter:hover .tp-sf-feed .tp-sf-footer *' => 'color: {{VALUE}}',				
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->start_controls_tabs( 'sfd_twitter_tabs' );
		$this->start_controls_tab(
			'sfd_twitter_n',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);	
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'TwNBgCr',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .tp-social-feed .feed-Twitter .tp-sf-feed',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'TwNBcr',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-social-feed .feed-Twitter .tp-sf-feed',
			]
		);
		$this->add_responsive_control(
			'TwNBrs',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-social-feed .feed-Twitter .tp-sf-feed' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',				
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'TwNBs',
				'selector' => '{{WRAPPER}} .tp-social-feed .feed-Twitter .tp-sf-feed',
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'sfd_twitter_h',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'TwHBgCr',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .tp-social-feed .feed-Twitter:hover .tp-sf-feed',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'TwHBcr',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-social-feed .feed-Twitter:hover .tp-sf-feed',
			]
		);
		$this->add_responsive_control(
			'TwHBrs',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-social-feed .feed-Twitter:hover .tp-sf-feed' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',				
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'TwHBs',
				'selector' => '{{WRAPPER}} .tp-social-feed .feed-Twitter:hover .tp-sf-feed',
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->add_responsive_control(
			'TwPRs',
			[
				'label'      => esc_html__( 'Profile Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-social-feed .feed-Twitter .tp-sf-feed .tp-sf-logo' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',				
				],	
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
            'SocIconSizetwt',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Icon Size', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 150,
						'step' => 1,
					],
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .tp-social-feed .fa-twitter.social-logo-tw' => 'font-size: {{SIZE}}{{UNIT}};',
				],
				'separator' => 'before',
            ]
        );
		$this->add_control(
			'SocIconColortwt',
			[
				'label' => esc_html__( 'Icon Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .tp-social-feed .fa-twitter.social-logo-tw' => 'color: {{VALUE}}',					
				],
			]
		);
        $this->end_controls_section();
		/*Twitter Style End*/
		/*Instagram Style Start*/
		$this->start_controls_section(
            'section_insta_styling',
            [
                'label' => esc_html__('Instagram', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'IgMsgTp',
				'label' => esc_html__( 'Extra Title Typography', 'theplus' ),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector' => '{{WRAPPER}} .tp-social-feed .feed-Instagram .tp-sf-feed .tp-title',
				'condition'   => [
					'ShowTitle' => 'yes',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'IgNameTp',
				'label' => esc_html__( 'Name Typography', 'theplus' ),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector' => '{{WRAPPER}} .tp-social-feed .feed-Instagram .tp-sf-feed .tp-sf-username a',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'IgTimeTp',
				'label' => esc_html__( 'Time Typography', 'theplus' ),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector' => '{{WRAPPER}} .tp-social-feed .feed-Instagram .tp-sf-feed .tp-sf-time a',
			]
		);
		$this->start_controls_tabs( 'sfd_insta_clr_tabs' );
		$this->start_controls_tab(
			'sfd_insta_clr_n',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);
		$this->add_control(
			'IgNTitleC',
			[
				'label' => esc_html__( 'Title Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .tp-social-feed .feed-Instagram .tp-sf-feed .tp-title' => 'color: {{VALUE}}',					
				],
				'condition'   => [
					'ShowTitle' => 'yes',
				]
			]
		);	
		$this->add_control(
			'IgNNameC',
			[
				'label' => esc_html__( 'Name Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .tp-social-feed .feed-Instagram .tp-sf-feed .tp-sf-username a' => 'color: {{VALUE}}',					
				],
			]
		);
		$this->add_control(
			'IgNTimeC',
			[
				'label' => esc_html__( 'Time Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .tp-social-feed .feed-Instagram .tp-sf-feed .tp-sf-time a' => 'color: {{VALUE}}',					
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'sfd_insta_clr_h',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
		$this->add_control(
			'IgHTitleC',
			[
				'label' => esc_html__( 'Title Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .tp-social-feed .feed-Instagram:hover .tp-sf-feed .tp-title' => 'color: {{VALUE}}',					
				],
				'condition'   => [
					'ShowTitle' => 'yes',
				]
			]
		);
		$this->add_control(
			'IgHNameC',
			[
				'label' => esc_html__( 'Name Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .tp-social-feed .feed-Instagram:hover .tp-sf-feed .tp-sf-username a' => 'color: {{VALUE}}',					
				],
			]
		);
		$this->add_control(
			'IgHTimeC',
			[
				'label' => esc_html__( 'Time Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .tp-social-feed .feed-Instagram:hover .tp-sf-feed .tp-sf-time a' => 'color: {{VALUE}}',					
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->start_controls_tabs( 'sfd_insta_tabs' );
		$this->start_controls_tab(
			'sfd_insta_n',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);	
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'IgNBgCr',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .tp-social-feed .feed-Instagram .tp-sf-feed',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'IgNBcr',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-social-feed .feed-Instagram .tp-sf-feed',
			]
		);
		$this->add_responsive_control(
			'IgNBrs',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-social-feed .feed-Instagram .tp-sf-feed' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',				
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'IgNBs',
				'selector' => '{{WRAPPER}} .tp-social-feed .feed-Instagram .tp-sf-feed',
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'sfd_insta_h',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'IgHBgCr',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .tp-social-feed .feed-Instagram:hover .tp-sf-feed',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'IgHBcr',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-social-feed .feed-Instagram:hover .tp-sf-feed',
			]
		);
		$this->add_responsive_control(
			'IgHBrs',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-social-feed .feed-Instagram:hover .tp-sf-feed' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',				
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'IgHBs',
				'selector' => '{{WRAPPER}} .tp-social-feed .feed-Instagram:hover .tp-sf-feed',
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->add_responsive_control(
			'IgPRs',
			[
				'label'      => esc_html__( 'Profile Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-social-feed .feed-Instagram .tp-sf-feed .tp-sf-logo' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',				
				],	
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
            'SocIconSizeinsta',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Icon Size', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 150,
						'step' => 1,
					],
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .tp-social-feed .fa-instagram.social-logo-fb' => 'font-size: {{SIZE}}{{UNIT}};',
				],
				'separator' => 'before',
            ]
        );
		$this->add_control(
			'SocIconColorinsta',
			[
				'label' => esc_html__( 'Icon Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .tp-social-feed .fa-instagram.social-logo-fb' => 'color: {{VALUE}}',					
				],
			]
		);
        $this->end_controls_section();
		/*Instagram Style End*/
	}

	protected function render() {
        $settings = $this->get_settings_for_display();
		$WidgetUID = $this->get_unique_selector();
		$uid_sfeed = uniqid("tp-sfeed");
		$WidgetId = $this->get_id();
		$layout = !empty($settings['layout']) ? $settings['layout'] : 'grid';
		$style = !empty($settings['style']) ? $settings['style'] : 'style-1';
		$Rsocialfeed = !empty($settings['AllReapeter']) ? $settings['AllReapeter'] : [];
		$RefreshTime = !empty($settings['TimeFrq']) ? $settings['TimeFrq'] : '3600';
		$TimeFrq = array( 'TimeFrq' => $RefreshTime );
		$TotalPost = !empty($settings['TotalPost']) ? $settings['TotalPost'] : 1000;
		$FeedId = !empty($settings['FeedId']) ? preg_split("/\,/", $settings['FeedId']) : [];
		$ShowTitle = !empty($settings['ShowTitle']) ? $settings['ShowTitle'] : false;
		$showFooterIn = !empty($settings['showFooterIn'] == 'yes') ? true : false;
		$txtLimt = !empty($settings['TextLimit'] == 'yes') ? true : false;
		$TextCount = !empty($settings['TextCount']) ? $settings['TextCount'] : 100 ;
		$TextType = !empty($settings['TextType']) ? $settings['TextType'] : 'char' ;
		$TextMore = !empty($settings['TextMore']) ? $settings['TextMore'] : 'Show More';
		$TextLess = !empty($settings['TextLess']) ? $settings['TextLess'] : '';
		$TextDots = !empty($settings['TextDots'] == 'yes') ? '...' : '';
		$FancyStyle = !empty($settings['FancyStyle']) ? $settings['FancyStyle'] : 'default' ;
		$DescripBTM = !empty($settings['DescripBTM'] == 'yes') ? true : false;
		$MediaFilter = !empty($settings['MediaFilter']) ? $settings['MediaFilter'] : 'default' ;
		$CategoryWF = !empty($settings['filter_category']) ? $settings['filter_category'] : '';
		$Postdisplay = !empty($settings['display_posts']) ? (int)$settings['display_posts'] : 8;
		$postview = !empty($settings['load_more_post']) ? $settings['load_more_post'] : '';
		$postLodop = !empty($settings['post_extra_option']) ? $settings['post_extra_option'] : '';
		$loadbtnText = !empty($settings['load_more_btn_text']) ? $settings['load_more_btn_text'] : '';
		$loadingtxt = !empty($settings['tp_loading_text']) ? $settings['tp_loading_text'] : '';
		$allposttext = !empty($settings['loaded_posts_text']) ? $settings['loaded_posts_text'] : '';
		$ShowFeedId = !empty($settings['ShowFeedId']) ? $settings['ShowFeedId'] : 'no';
		$PopupOption = !empty($settings['OnPopup']) ? $settings['OnPopup'] : 'OnFancyBox';
		$Performance = !empty($settings['perf_manage']) ? $settings['perf_manage'] : false;
		$NormalScroll = '';
		$ScrollOn = !empty($settings['ScrollOn'] == 'yes') ? true : false;
		$FcyScrolllOn = !empty($settings['FcySclOn'] == 'yes') ? true : false;
		$OffsetPost = !empty($FeedId) ? $Postdisplay - count($FeedId) : '';

		$FeedArray = array();
		$ShomoreArray = array();
		if( !empty($txtLimt) ){
			$ShomoreArray = array(
				'TextMore' => $TextMore,
				'TextLess' => $TextLess,
			);

			array_merge($FeedArray , $ShomoreArray);
		}

		$NormalShomore = json_encode($ShomoreArray, true);

		if( !empty($ScrollOn) || !empty($FcyScrolllOn) ){
			$ScrollData = array(
				'className'     => 'tp-normal-scroll',
				'ScrollOn'      => $ScrollOn,
				'Height'        => !empty($settings['ScrollHgt']['size']) ? (int)$settings['ScrollHgt']['size'] : 150,
				'TextLimit'     => $txtLimt,

				'Fancyclass'    => 'tp-fancy-scroll',
				'FancyScroll'   => $FcyScrolllOn,
				'FancyHeight'   => !empty($settings['FcySclHgt']['size']) ? (int)$settings['FcySclHgt']['size'] : 150

			);
			$NormalScroll = json_encode($ScrollData, true);
		}
		
		$layout_attr=$data_class='';
		if($layout!=''){
			$data_class .= theplus_get_layout_list_class($layout);
			$layout_attr = theplus_get_layout_list_attr($layout);
		}else{
			$data_class .=' list-isotope';
		}

		$carousel_direction=$carousel_slider='';
		if($layout=='carousel'){
			$carousel_direction = !empty($settings['carousel_direction']) ? $settings['carousel_direction'] : 'rtl';
		
			if ( !empty($carousel_direction) ) {
				$carousel_data = array(
					'carousel_direction' => $carousel_direction,
				);
	
				$carousel_slider = 'data-result="' . htmlspecialchars(wp_json_encode($carousel_data, true), ENT_QUOTES, 'UTF-8') . '"';
			}
		}

		//columns
		$desktop_class=$tablet_class=$mobile_class='';
		if($layout != 'carousel'){
			$desktop_class = 'tp-col-lg-'.esc_attr($settings['desktop_column']);
			$tablet_class = 'tp-col-md-'.esc_attr($settings['tablet_column']);
			$mobile_class = 'tp-col-sm-'.esc_attr($settings['mobile_column']);
			$mobile_class .= ' tp-col-'.esc_attr($settings['mobile_column']);
		}

		$output=$data_attr='';		
		//carousel
		if($layout=='carousel'){
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
   
		$fancybox_settings =$this->tp_social_feed_fancybox($settings);
	    $fancybox_settings = json_encode($fancybox_settings);
		if($CategoryWF == 'yes'){
			$data_class .=' pt-plus-filter-post-category ';
		}

		$ji=1;$ij='';
		$uid_sfeed = uniqid("post");
		if(!empty($settings["carousel_unique_id"])){
			$uid_sfeed="tpca_".$settings["carousel_unique_id"];
		}

		$Fancyboxids = json_encode( array( $WidgetId, $uid_sfeed ) );
		$data_attr .=' data-id="'.esc_attr($uid_sfeed).'"';
		$data_attr .=' data-style="'.esc_attr($style).'"';
        $output .= '<div id="'.esc_attr($uid_sfeed).'" class="'.esc_attr($uid_sfeed).' tp-social-feed '.esc_attr($data_class).'" '.$layout_attr.' '.$data_attr.' data-fancy-option=\''.$fancybox_settings.'\' data-scroll-normal=\''.esc_attr($NormalScroll).'\' data-feed-data=\''.esc_attr($NormalShomore).'\' '.$carousel_slider.' dir='.esc_attr($carousel_direction).' data-ids=\''.$Fancyboxids.'\' data-enable-isotope="1" >';

			$FancyBoxJS = '';
			if($PopupOption == 'OnFancyBox'){
				$FancyBoxJS = "data-fancybox=".esc_attr($uid_sfeed);
			}

			$FinalData = [];
			$Perfo_transient = get_transient("SF-Performance-$WidgetId");
			if( ($Performance == false) || ($Performance == true && $Perfo_transient === false) ){
				$AllData = [];
				foreach ($Rsocialfeed as $index => $social) {
					$RFeed = !empty($social['selectFeed']) ? $social['selectFeed'] : 'Facebook';
					$social = array_merge($TimeFrq, $social);
					if($RFeed == 'Facebook'){
						$AllData[] = $this->FacebookFeed($social,$settings);
					}else if($RFeed == 'Twitter'){
						$AllData[] = $this->TwetterFeed($social,$settings);
					}else if($RFeed == 'Instagram'){
						$AllData[] = $this->InstagramFeed($social,$settings);
					}else if($RFeed == 'Vimeo'){
						$AllData[] = $this->VimeoFeed($social,$settings);
					}else if($RFeed == 'Youtube'){
						$AllData[] = $this->YouTubeFeed($social,$settings);
					}
				}

				foreach($AllData as $key => $val){
					foreach($val as $key => $vall){ 
						$FinalData[] =  $vall; 
					}
				}

				$Feed_Index = array_column($FinalData, 'Feed_Index');
				array_multisort($Feed_Index, SORT_ASC, $FinalData);

				set_transient("SF-Performance-$WidgetId", $FinalData, $RefreshTime);
			}else{
				$FinalData = get_transient("SF-Performance-$WidgetId");
			}

		    if(!empty($FinalData)){
				foreach ($FinalData as $index => $data) {
					$PostId = !empty($data['PostId']) ? $data['PostId'] : [];
					if(in_array($PostId, $FeedId)){
						unset($FinalData[$index]);
					}
				}

				if($CategoryWF == 'yes' && $layout != 'carousel'){
					$FilterTotal = '';
					if($postLodop == 'load_more' || $postLodop == 'lazy_load'){
                        $FilterTotal = $Postdisplay;
                    }else{
                        $FilterTotal = count($FinalData);
                    }
					$output .= $this->get_filter_category($FilterTotal, $FinalData);
				}

				if($postLodop == 'load_more' || $postLodop == 'lazy_load'){
                    $totalFeed = (count($FinalData));
					$remindata = array_slice($FinalData, $Postdisplay);

					$RemingC = count($remindata);
					$FinalData = array_slice($FinalData, 0, $Postdisplay);

					$FRemingC = count($FinalData);
					$trans_store = get_transient("SF-Loadmore-$WidgetId");
					if( $trans_store === false ){
						set_transient("SF-Loadmore-$WidgetId", $remindata, $RefreshTime);
					}else if( !empty($trans_store) && is_array($trans_store) && count($trans_store) != $totalFeed ){
						set_transient("SF-Loadmore-$WidgetId", $remindata, $RefreshTime);
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
						'postview' => esc_attr((int)$postview),
						'display' => esc_attr($Postdisplay),
						'TextLimit' => esc_attr($txtLimt),
						'TextCount' => esc_attr($TextCount),
						'TextType' => esc_attr($TextType),
						'TextMore' => esc_attr($TextMore),
						'TextDots' => esc_attr($TextDots),
						'loadingtxt' => esc_attr($loadingtxt),
						'allposttext' => esc_attr($allposttext),
						'totalFeed' => esc_attr($totalFeed),
						'FancyStyle' => esc_attr($FancyStyle),
						'DescripBTM' => esc_attr($DescripBTM),
						'MediaFilter' => esc_attr($MediaFilter),
						'TotalPost' => esc_attr($TotalPost),
						'categorytext' => esc_attr($CategoryWF),
						'PopupOption' => esc_attr($PopupOption),
						'FilterStyle' => esc_attr($settings['filter_style']),
						'theplus_nonce' => wp_create_nonce("theplus-addons"),
					];
					$data_loadkey = tp_plus_simple_decrypt( json_encode($postattr), 'ey' );
				}
				
				$output .= '<div id="'.esc_attr($uid_sfeed).'" class="tp-row post-inner-loop '.esc_attr($uid_sfeed).' social-feed-'.esc_attr($style).'">';
					foreach ($FinalData as $F_index => $AllVmData) {
						$PopupTarget=$PopupLink='';
						$uniqEach = uniqid();
						$PopupSylNum = "{$uid_sfeed}-{$F_index}-{$uniqEach}";
						$RKey = !empty($AllVmData['RKey']) ? $AllVmData['RKey'] : '';
						$PostId = !empty($AllVmData['PostId']) ? $AllVmData['PostId'] : '';
						$selectFeed = !empty($AllVmData['selectFeed']) ? $AllVmData['selectFeed'] : '';
						$Massage = !empty($AllVmData['Massage']) ? $AllVmData['Massage'] : '';
						$Description = !empty($AllVmData['Description']) ? $AllVmData['Description'] : '';
						$Type = !empty($AllVmData['Type']) ? $AllVmData['Type'] : '';
						$PostLink = !empty($AllVmData['PostLink']) ? $AllVmData['PostLink'] : '';
						$CreatedTime = !empty($AllVmData['CreatedTime']) ? $AllVmData['CreatedTime'] : '';
						$PostImage = !empty($AllVmData['PostImage']) ? $AllVmData['PostImage'] : '';
						$UserName = !empty($AllVmData['UserName']) ? $AllVmData['UserName'] : '';
						$UserImage = !empty($AllVmData['UserImage']) ? $AllVmData['UserImage'] : '';
						$UserLink = !empty($AllVmData['UserLink']) ? $AllVmData['UserLink'] : '';
						$socialIcon = !empty($AllVmData['socialIcon']) ? $AllVmData['socialIcon'] : '';
						$CategoryText = !empty($AllVmData['FilterCategory']) ? $AllVmData['FilterCategory'] : '';
						$ErrorClass = !empty($AllVmData['ErrorClass']) ? $AllVmData['ErrorClass'] : '';						
						$EmbedURL = !empty($AllVmData['Embed']) ? $AllVmData['Embed'] : '';
						$EmbedType = !empty($AllVmData['EmbedType']) ? $AllVmData['EmbedType'] : '';
						$category_filter = $loop_category = '';

						if( !empty($CategoryWF == 'yes') && !empty($CategoryText)  && $layout != 'carousel' ){
							$loop_category = explode(',', $CategoryText);
							foreach( $loop_category as $category ) {
								$category = $this->SF_Media_createSlug($category);
								$category_filter .=' '.esc_attr($category).' ';
							}
						}
						if($selectFeed == 'Facebook'){
							$Fblikes = !empty($AllVmData['FbLikes']) ? $AllVmData['FbLikes'] : 0;
							$comment = !empty($AllVmData['comment']) ? $AllVmData['comment'] : 0;
							$share = !empty($AllVmData['share']) ? $AllVmData['share'] : 0;
							$likeImg=THEPLUS_ASSETS_URL.'images/social-feed/like.png';
							$ReactionImg=THEPLUS_ASSETS_URL.'images/social-feed/love.png';
							$FbAlbum = !empty($AllVmData['FbAlbum']) ? true : false;
							if(!empty($FbAlbum)){
								$FancyBoxJS = "data-fancybox=".esc_attr("album-Facebook{$F_index}-{$uid_sfeed}");
							}
							
						}
						if($selectFeed == 'Twitter'){
							$TwRT = !empty($AllVmData['TWRetweet']) ? $AllVmData['TWRetweet'] : 0;
							$TWLike = !empty($AllVmData['TWLike']) ? $AllVmData['TWLike'] : 0;
							
							$TwReplyURL = !empty($AllVmData['TwReplyURL']) ? $AllVmData['TwReplyURL'] : '';
							$TwRetweetURL = !empty($AllVmData['TwRetweetURL']) ? $AllVmData['TwRetweetURL'] : '';
							$TwlikeURL = !empty($AllVmData['TwlikeURL']) ? $AllVmData['TwlikeURL'] : '';
							$TwtweetURL = !empty($AllVmData['TwtweetURL']) ? $AllVmData['TwtweetURL'] : '';
						}
						if($selectFeed == 'Vimeo'){
							$share = !empty($AllVmData['share']) ? $AllVmData['share'] : 0;
							$likes = !empty($AllVmData['likes']) ? $AllVmData['likes']: 0;
							$comment = !empty($AllVmData['comment']) ? $AllVmData['comment'] : 0;
						}
						if($selectFeed == 'Youtube'){
							$view = !empty($AllVmData['view']) ? $AllVmData['view'] : 0;
							$likes = !empty($AllVmData['likes']) ? $AllVmData['likes'] : 0;
							$comment = !empty($AllVmData['comment']) ? $AllVmData['comment'] : 0;
							$Dislike = !empty($AllVmData['Dislike']) ? $AllVmData['Dislike'] : 0;
						}
						$ImageURL=$videoURL="";
						if( ($Type == 'video' || $Type == 'photo') && $selectFeed != 'Instagram' ){
							$videoURL = $PostLink;
							$ImageURL = $PostImage;
						}
						$IGGP_Icon='';
						if($selectFeed == 'Instagram'){
							$IGGP_Type = !empty($AllVmData['IG_Type']) ? $AllVmData['IG_Type'] : 'Instagram_Basic';
							if($IGGP_Type == 'Instagram_Graph'){
								$IGGP_Icon = !empty($AllVmData['IGGP_Icon']) ? $AllVmData['IGGP_Icon'] : '';
								$likes = !empty($AllVmData['likes']) ? $AllVmData['likes']: 0;
								$comment = !empty($AllVmData['comment']) ? $AllVmData['comment'] : 0;
								$videoURL = $PostLink;
								$PostLink = !empty($AllVmData['IGGP_PostLink']) ? $AllVmData['IGGP_PostLink'] : '';
								$ImageURL = $PostImage;

								$IGGP_CAROUSEL = !empty($AllVmData['IGGP_CAROUSEL']) ? $AllVmData['IGGP_CAROUSEL'] : '';
								if( $Type == "CAROUSEL_ALBUM" && $FancyStyle == 'default' ){
									$FancyBoxJS = "data-fancybox=".esc_attr("IGGP-CAROUSEL-{$F_index}-{$uniqEach}");
								}else{
									$FancyBoxJS = "data-fancybox=".esc_attr($uid_sfeed);
								}
							}else if($IGGP_Type == 'Instagram_Basic'){
								$videoURL = $PostLink;
								$ImageURL = $PostImage;
							}
						}
						if(!empty($FbAlbum)){
							$PostLink = !empty($PostLink[0]['link']) ? $PostLink[0]['link'] : 0;
						}

						if( ($F_index < $TotalPost) && ( ($MediaFilter == 'default') || ($MediaFilter == 'ompost' && !empty($PostLink) && !empty($PostImage)) || ($MediaFilter == 'hmcontent' &&  empty($PostLink) && empty($PostImage) )) ){
							$output .= '<div class="grid-item '.esc_attr('feed-'.$selectFeed.' '.$desktop_class.' '.$tablet_class.' '.$mobile_class.' '.$RKey.' '.$category_filter).'" data-index="'.esc_attr($selectFeed.$F_index).'" >';					
								ob_start();
									include THEPLUS_PATH. 'includes/social-feed/social-feed-' . sanitize_file_name($style) . '.php'; 
									$output .= ob_get_contents();
								ob_end_clean();
							$output .= '</div>';
						}
					}
				$output .='</div>';

				if( !empty($totalFeed) && $totalFeed > $Postdisplay ){
					if($postLodop == 'load_more' && $layout != 'carousel'){
						$output  .= '<div class="ajax_load_more">';
							$output  .= '<a class="post-load-more" data-loadingtxt="'.esc_attr($loadingtxt).'" data-layout="'.esc_attr($layout).'"  data-loadclass="'.esc_attr($uid_sfeed).'" data-loadview="'.esc_attr($postview).'" data-loadattr= \'' . $data_loadkey . '\'>';
								$output .= $loadbtnText;
							$output  .= '</a>';
						$output  .= '</div>';
					}else if($postLodop == 'lazy_load' && $layout != 'carousel'){
						$output .= '<div class="ajax_lazy_load">';
							$output .= '<a class="post-lazy-load" data-loadingtxt="'.esc_attr($loadingtxt).'" data-lazylayout="'.esc_attr($layout).'" data-lazyclass="'.esc_attr($uid_sfeed).'" data-lazyview="'.esc_attr($postview).'" data-lazyattr= \'' . $data_loadkey . '\'>';
								$output .= '<div class="tp-spin-ring"><div></div><div></div><div></div></div>';
							$output .= '</a>';
						$output .= '</div>';
					}
				}
		    }else{
			  $output .= '<div class="error-handal"> All Social Feed </div>';
		    }

		$output .= '</div>';		
		echo $output;
	}

	protected function FacebookFeed($social){
		$settings = $this->get_settings_for_display();
		$BaseURL = 'https://graph.facebook.com/v11.0';
		$FbKey = !empty($social['_id']) ? $social['_id'] : '';
		$FbAcT = !empty($social['RAToken']) ? $social['RAToken'] : '';
		$FbPType = !empty($social['ProfileType']) ? $social['ProfileType'] : 'post';
		$FbPageid = !empty($social['Pageid']) ? $social['Pageid'] : '';
		$FbAlbum = !empty($social['fbAlbum'] == 'yes') ? true : false;
		$FbLimit = !empty($social['MaxR']) ? $social['MaxR'] : 6;
		$FbALimit = !empty($social['AlbumMaxR']) ? $social['AlbumMaxR'] : 6;	
		$Fbcontent = !empty($social['content']) ? $social['content'] : [];
		$FbTime = !empty($social['TimeFrq']) ? $social['TimeFrq'] : '3600';	
		$FbCategory = !empty($social['RCategory']) ? $social['RCategory'] : '';
		$FbselectFeed = !empty($social['selectFeed']) ? $social['selectFeed'] : '';
		$FbIcon = 'fab fa-facebook social-logo-fb'; 
		
		$content = [];
		if(!empty($Fbcontent) && (is_array($Fbcontent) || is_object($Fbcontent)) ){
			foreach ($Fbcontent as $Data) {
				$Filter = (!empty($Data)) ? $Data : 'photo';
				array_push($content,$Filter);
			}
		}else{
			array_push($content,'photo');
		}	
		$url = '';
		$FbAllData = '';
		$FbArr = [];
		if(!empty($FbAcT) && $FbPType == 'post'){
			$url = "{$BaseURL}/me?fields=id,name,first_name,last_name,link,email,birthday,picture,posts.limit($FbLimit){type,message,story,caption,description,shares,picture,full_picture,source,created_time,reactions.summary(true),comments.summary(true).filter(toplevel)},albums.limit($FbLimit){id,type,link,picture,created_time,name,count,photos.limit($FbALimit){id,link,created_time,likes,images,name,comments.summary(true).filter(toplevel)}}&access_token={$FbAcT}";
		}else if(!empty($FbAcT) && !empty($FbPageid) && $FbPType == 'page'){
			$url = "{$BaseURL}/{$FbPageid}?fields=id,name,username,link,fan_count,new_like_count,phone,emails,about,birthday,category,picture,posts.limit($FbLimit){id,full_picture,created_time,message,attachments{media,media_type,title,url},picture,story,status_type,shares,reactions.summary(true),likes.summary(true),comments.summary(true).filter(toplevel)},albums.limit($FbLimit){id,type,link,picture,created_time,name,count,photos.limit($FbALimit){id,link,created_time,images,name}}&access_token={$FbAcT}";
		}

		if(!empty($url)){
			$GetFbRL = get_transient("Fb-Url-$FbKey");
			$GetFbTime = get_transient("Fb-Time-$FbKey");

			if( $GetFbRL != $url || $GetFbTime != $FbTime ){
				$FbAllData = $this->tp_api_call($url);

				set_transient("Fb-Url-$FbKey", $url, $FbTime);
				set_transient("Data-Fb-$FbKey", $FbAllData, $FbTime);
				set_transient("Fb-Time-$FbKey", $FbTime, $FbTime);
			}else{
				$FbAllData = get_transient("Data-Fb-$FbKey");
			}
			
			$status = !empty($FbAllData['HTTP_CODE']) ? $FbAllData['HTTP_CODE'] : '';
			if($status == 200){
				$FbPost = '';
				if(!empty($FbAlbum)){
					$FbPost = (!empty($FbAllData['albums']['data'])) ? $FbAllData['albums']['data'] : [];
				}else{
					$FbPost = (!empty($FbAllData['posts']['data'])) ? $FbAllData['posts']['data'] : [];
				}
				foreach ($FbPost as $index => $FbData){
					$id = !empty($FbData['id']) ? $FbData['id'] : '';
					$link = !empty($FbAllData['link']) ? $FbAllData['link'] : '';
					$type = !empty($FbData['type']) ? $FbData['type'] : '';
					$name = !empty($FbAllData['name']) ? $FbAllData['name'] : '';
					$FbMessage = !empty($FbData['message']) ? $FbData['message'] : '';
					$FbPicture = $FbSource = !empty($FbData['full_picture']) ? $FbData['full_picture'] : '';
					$Created_time = !empty($FbData['created_time']) ? $this->feed_Post_time($FbData['created_time']) : '';
					$FbReactions = !empty($FbData['reactions']['summary']['total_count']) ? $this->tp_number_short($FbData['reactions']['summary']['total_count']) : 0;
					$FbComments = !empty($FbData['comments']['summary']['total_count']) ? $this->tp_number_short($FbData['comments']['summary']['total_count']) : 0;
					$Fbshares = !empty($FbData['shares']['count']) ? $this->tp_number_short($FbData['shares']['count']) : '';
					if($type == "video"){
						$FbSource = !empty($FbData['source']) ? $FbData['source'] : '';
					}
					$FbCaption = !empty($FbData['caption']) ? $FbData['caption'] : '';
					$FbDescription = !empty($FbData['description']) ? $FbData['description'] : '';
					if($FbPType == 'page'){
						$type = !empty($FbData['attachments']['data'][0]['media_type']) ? $FbData['attachments']['data'][0]['media_type'] : '';
						if($type == 'album'){
							$type = "photo";
						}
						if($type == 'video'){
							if( !empty($FbData['attachments']['data'][0]['media']['source']) ){
								$FbSource = $FbData['attachments']['data'][0]['media']['source'];
							}else if( !empty($FbData['attachments']['data'][0]['url']) ){
								$FbSource = "https://www.facebook.com/plugins/video.php?href=".$FbData['attachments']['data'][0]['url'];
							}else{
								$FbSource = '';
							}
						}
					}
					if($type == 'video'){
						$FbSource = str_replace("autoplay=1", "autoplay=0", $FbSource);
					}

					if(!empty($FbAlbum)){
						$type = 'video'; 
						$link = !empty($FbData['link']) ? $FbData['link'] : '';
						$FbMessage = !empty($FbData['name']) ? $FbData['name'] : '';
						$Fbcount = !empty($FbData['count']) ? $FbData['count'] : '';
						$FbPicture = !empty($FbData['picture']['data']['url']) ? $FbData['picture']['data']['url'] : '';
						$FbSource = !empty($FbData['photos']['data']) ? $FbData['photos']['data'] : [];
					}

					if( (in_array('photo',$content) && $type == 'photo') || (in_array('video', $content) && $type == 'video') || ( in_array('status',$content) && ($type == 'status' || $type == 'link')) ){							
						$FbArr[] = array(
							"Feed_Index"	=> $index,
							"PostId"		=> $id,
							"Massage" 		=> '',
							"Description"	=> $FbMessage . $FbCaption . $FbDescription,
							"Type" 			=> "video",
							"PostLink" 		=> $FbSource,
							"CreatedTime" 	=> $Created_time,
							"PostImage" 	=> $FbPicture,
							"UserName" 		=> $name,
							"UserImage" 	=> (!empty($FbAllData['picture']['data']['url']) ? $FbAllData['picture']['data']['url'] : ''),
							"UserLink" 		=> $link,
							"share" 		=> $Fbshares,
							"comment" 		=> $FbComments,
							"FbLikes" 		=> $FbReactions,
							"Embed" 		=> "Alb",
							"EmbedType"     => $type,
							"FbAlbum" 		=> $FbAlbum,
							"socialIcon" 	=> $FbIcon,
							"selectFeed"    => $FbselectFeed,
							"FilterCategory"=> $FbCategory,
							"RKey" 			=> "tp-repeater-item-$FbKey",
						);
					}
				}		
			}else{
				$FbArr[] = $this->SF_Error_handler($FbAllData, $FbKey, $FbCategory, $FbselectFeed, $FbIcon);
			}
		}else{	
			$Msg = "";
			if(empty($FbAcT)){
				$Msg .= 'Empty Access Token </br>';
			}
			if($FbPType == 'page' && empty($FbPageid)){
				$Msg .= 'Empty Page ID';
			}
			$ErrorData['error']['message'] = $Msg;
			$FbArr[] = $this->SF_Error_handler($ErrorData, $FbKey, $FbCategory, $FbselectFeed, $FbIcon);
		}		

	    return $FbArr;
    }
	protected function InstagramFeed($social){
		$IGKey = !empty($social['_id']) ? $social['_id'] : '';
		$IGAcT = !empty($social['RAToken']) ? $social['RAToken'] : '';
		$Profile = (!empty($social['IGImgPic']) && !empty($social['IGImgPic']['url']) ) ? $social['IGImgPic']['url'] : '';
		$TimeFrq = !empty($social['TimeFrq']) ? $social['TimeFrq'] : '3600';
		$IGType = !empty($social['InstagramType']) ? $social['InstagramType'] : 'Instagram_Basic';
		$HashtagType = !empty($social['IG_hashtagType']) ? $social['IG_hashtagType'] : 'top_media';
		$RCategory = !empty($social['RCategory']) ? $social['RCategory'] : '';
		$selectFeed = !empty($social['selectFeed']) ? $social['selectFeed'] : '';
		$Default_Img = THEPLUS_ASSETS_URL.'images/tp-placeholder.jpg';
		$IGIcon = 'fab fa-instagram social-logo-fb';
		$IGcount = !empty($social['MaxR']) ? $social['MaxR'] : 6;

		$IGArr = [];
		if($IGType == "Instagram_Basic"){
			$IGAPI = "https://graph.instagram.com/me/?fields=account_type,id,media_count,username,media.limit($IGcount){id,caption,permalink,thumbnail_url,timestamp,username,media_type,media_url}&access_token={$IGAcT}";
			$GetURL = get_transient("IG-Url-$IGKey");
			$GetTime = get_transient("IG-Time-$IGKey");
			$GetProfile = get_transient("IG-Profile-$IGKey");
			$IGData = [];
			if( $GetURL != $IGAPI || $GetProfile != $Profile || $GetTime != $TimeFrq ){
				$IGData = $this->tp_api_call($IGAPI);
							set_transient("IG-Url-$IGKey", $IGAPI, $TimeFrq);
							set_transient("Data-IG-$IGKey", $IGData, $TimeFrq);
							set_transient("IG-Profile-$IGKey", $Profile, $TimeFrq);
							set_transient("IG-Time-$IGKey", $TimeFrq, $TimeFrq);
			}else{
				$IGData = get_transient("Data-IG-$IGKey");
			}

			$IGStatus = !empty($IGData['HTTP_CODE']) ? $IGData['HTTP_CODE'] : 400;
			if( $IGStatus == 200 ){
				$posts = (!empty($IGData['media']) && !empty($IGData['media']['data']) ) ? $IGData['media']['data'] : [];
				foreach ($posts as $index => $IGPost) {
					$media_type = !empty($IGPost['media_type']) ? $IGPost['media_type'] : '';
					if( $media_type == 'IMAGE' ){ 
						$type = 'photo'; 
					}

					$PostImage='';
					if( !empty($IGPost['media_url']) && $IGPost['media_type'] == 'VIDEO' ) {	
						$PostImage = !empty($IGPost['thumbnail_url']) ? $IGPost['thumbnail_url'] : $Default_Img;
					}else if(!empty($IGPost['media_url'])){
						$PostImage = $IGPost['media_url'];
					}

					$IGArr[] = array(
						"Feed_Index"	=> $index,
						"PostId"		=> !empty($IGPost['id']) ? $IGPost['id'] : '',
						"Massage" 		=> '',
						"Description"	=> !empty($IGPost['caption']) ? $IGPost['caption'] : '',
						"Type" 			=> 'video',
						"PostLink" 		=> !empty($IGPost['media_url']) ? $IGPost['media_url'] : '',
						"CreatedTime" 	=> !empty($IGPost['timestamp']) ? $this->feed_Post_time($IGPost['timestamp']) : '',
						"PostImage" 	=> $PostImage,
						"UserName" 		=> !empty($IGData['username']) ? $IGData['username'] : '',
						"UserImage" 	=> $Profile,
						"UserLink" 		=> !empty($IGPost['permalink']) ? $IGPost['permalink'] : '',
						"IG_Type"		=> $IGType,
						"socialIcon" 	=> $IGIcon,
						"selectFeed"    => $selectFeed,
						"FilterCategory"=> $RCategory,
						"RKey" 			=> "tp-repeater-item-$IGKey",
					);
				}
			}else{
				if(empty($IGAcT)){
					$IGData['error']['message'] = 'Enter Access Token';
				}
				$IGArr[] = $this->SF_Error_handler($IGData, $IGKey, $RCategory, $selectFeed, $IGIcon);
			}
		}else if($IGType == "Instagram_Graph"){
			$BashURL = "https://graph.facebook.com/v11.0";
			$IGPageId = !empty($social['IGPageId']) ? $social['IGPageId'] : '';
			$IGFeedType = !empty($social['IG_FeedTypeGp']) ? $social['IG_FeedTypeGp'] : 'IGUserdata';
			$IGGPcount = ($IGcount > 49) ? $IGcount : $IGcount * 6;
			
			$UserID_API = "{$BashURL}/{$IGPageId}?fields=instagram_business_account{id,profile_picture_url,username,ig_id,media_count}&access_token={$IGAcT}";
			$GetURL = get_transient("IG-GP-Url-$IGKey");
			$GetTime = get_transient("IG-GP-Time-$IGKey");
			$UserID_Res = [];
			if( ($GetURL != $UserID_API) || ($GetTime != $TimeFrq) ){
				$UserID_Res = $this->tp_api_call($UserID_API);
							set_transient("IG-GP-Url-$IGKey", $UserID_API, $TimeFrq);
							set_transient("IG-GP-Time-$IGKey", $TimeFrq, $TimeFrq);
							set_transient("IG-GP-Data-$IGKey", $UserID_Res, $TimeFrq);
			}else{
				$UserID_Res = get_transient("IG-GP-Data-$IGKey");
			}

			$UserID_CODE = !empty($UserID_Res['HTTP_CODE']) ? $UserID_Res['HTTP_CODE'] : 400;
			if($UserID_CODE == 200){
				$GET_UserID = !empty($UserID_Res['instagram_business_account']) ? $UserID_Res['instagram_business_account']['id'] : '';
				$GET_UserName = !empty($UserID_Res['instagram_business_account']['username']) ? $UserID_Res['instagram_business_account']['username'] : '';
				$GET_Profile = !empty($UserID_Res['instagram_business_account']['profile_picture_url']) ? $UserID_Res['instagram_business_account']['profile_picture_url'] : $Default_Img;
				$IGGP_CountFiler = 0;

				if($IGFeedType == 'IGUserdata'){
					$IGUserName = !empty($social['IGUserName_GP']) ? $social['IGUserName_GP'] : $GET_UserName;
					$UserPost_API = "{$BashURL}/{$GET_UserID}?fields=business_discovery.username({$IGUserName}){username,profile_picture_url,followers_count,media_count,media.limit({$IGGPcount}){permalink,media_type,media_url,like_count,comments_count,timestamp,caption,id,media_product_type,children{media_url,permalink,media_type}}}&access_token={$IGAcT}";

					$UserPost_Databash = get_transient("IG-GP-UserFeed-Url-$IGKey");
					$UserPost_Res=[];
					if( $UserPost_Databash != $UserPost_API || $GetTime != $TimeFrq ){
						$UserPost_Res = $this->tp_api_call($UserPost_API);
									 set_transient("IG-GP-UserFeed-Url-$IGKey", $UserPost_API, $TimeFrq);
									 set_transient("IG-GP-UserFeed-Data-$IGKey", $UserPost_Res, $TimeFrq);
					}else{
						$UserPost_Res = get_transient("IG-GP-UserFeed-Data-$IGKey");
					}

					$UserPost_CODE = !empty($UserPost_Res['HTTP_CODE']) ? $UserPost_Res['HTTP_CODE'] : 400;
					if($UserPost_CODE == 200){
						$GET_Profile = !empty($UserPost_Res['business_discovery']['profile_picture_url']) ? $UserPost_Res['business_discovery']['profile_picture_url'] : $GET_Profile;
						$BD = !empty($UserPost_Res['business_discovery']['media']) ? $UserPost_Res['business_discovery']['media']['data'] : [];
						
						foreach ($BD as $index => $IGGA) {
							$Permalink = !empty($IGGA['permalink']) ? $IGGA['permalink'] : '';
							
							$PostImage='';
							if( !empty($IGGA['media_url']) && $IGGA['media_type'] == 'VIDEO' ) {	
								$PostImage = THEPLUS_ASSETS_URL.'images/placeholder-grid.jpg';
							}else if(!empty($IGGA['media_url'])){
								$PostImage = $IGGA['media_url'];
							}

							$IGGP_Icon="";
							$Media_type = !empty($IGGA['media_type']) ? $IGGA['media_type'] : '';
							if($Media_type == 'IMAGE'){
							}else if($Media_type == 'VIDEO'){
								$IGGP_Icon = '<svg aria-hidden="true" focusable="false" data-prefix="fad" data-icon="video" class="svg-inline--fa fa-video fa-w-18 IGGP_video" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><g class="fa-group"><path class="fa-secondary" fill="currentColor" d="M525.6 410.2L416 334.7V177.3l109.6-75.6c21.3-14.6 50.4.4 50.4 25.8v256.9c0 25.5-29.2 40.4-50.4 25.8z" opacity="0.4"></path><path class="fa-primary" fill="currentColor" d="M0 400.2V111.8A47.8 47.8 0 0 1 47.8 64h288.4a47.8 47.8 0 0 1 47.8 47.8v288.4a47.8 47.8 0 0 1-47.8 47.8H47.8A47.8 47.8 0 0 1 0 400.2z"></path></g></svg>';
							}else if( $Media_type == 'CAROUSEL_ALBUM' ){
								$IGGP_Icon = '<svg aria-hidden="true" focusable="false" data-prefix="fad" data-icon="clone" class="svg-inline--fa fa-clone fa-w-16 IGGP_Multiple" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><g class="fa-group"><path class="fa-secondary" fill="currentColor" d="M48 512a48 48 0 0 1-48-48V176a48 48 0 0 1 48-48h48v208a80.09 80.09 0 0 0 80 80h208v48a48 48 0 0 1-48 48H48z" opacity="0.4"></path><path class="fa-primary" fill="currentColor" d="M512 48v288a48 48 0 0 1-48 48H176a48 48 0 0 1-48-48V48a48 48 0 0 1 48-48h288a48 48 0 0 1 48 48z"></path></g></svg>';
							}

							$CAROUSEL_ALBUM = !empty($IGGA['children']) ? $IGGA['children']['data'] : [];
							$IGGP_CAROUSEL_ALBUM=[];
							foreach ($CAROUSEL_ALBUM as $key => $IGGP){
								$IGGP_MediaType = !empty($IGGP['media_type']) ? $IGGP['media_type'] : 'IMAGE'; 
                                $IGGP_MediaURl = !empty($IGGP['media_url']) ? $IGGP['media_url'] : '';

								if($key == 0 && $IGGP_MediaType == 'VIDEO'){
									foreach ($CAROUSEL_ALBUM as $thumb_i => $IGGP_Thumb){
										$IGGP_ThumbImg = !empty($IGGP_Thumb['media_type']) ? $IGGP_Thumb['media_type'] : 'IMAGE'; 
										if($IGGP_ThumbImg == 'IMAGE'){
											$PostImage = !empty($IGGP_Thumb['media_url']) ? $IGGP_Thumb['media_url'] : '';
											break;
										}
									}
								}
								if($IGGP_MediaType == 'IMAGE'){
									$IGGP_CAROUSEL_ALBUM[] = array(
										"IGGPCAR_Index" => $index,
										"IGGPImg_Type" => $IGGP_MediaType,
										"IGGPURL_Media" => $IGGP_MediaURl,
									);
								}
							}

							if( $Media_type != 'VIDEO' && $IGGP_CountFiler < $IGcount ){
								$IGArr[] = array(
									"Feed_Index"	=> $index,
									"PostId"		=> !empty($IGGA['id']) ? $IGGA['id'] : '',
									"Massage" 		=> '',
									"Description"	=> !empty($IGGA['caption']) ? $IGGA['caption'] : '',
									"Type" 			=> $Media_type,
									"PostLink" 		=> !empty($IGGA['media_url']) ? $IGGA['media_url'] : '',
									"CreatedTime" 	=> !empty($IGGA['timestamp']) ? $this->feed_Post_time($IGGA['timestamp']) : '',
									"PostImage" 	=> $PostImage,
									"UserName" 		=> $IGUserName,
									"UserImage" 	=> !empty($GET_Profile) ? $GET_Profile : $Default_Img,
									"UserLink" 		=> "https://www.instagram.com/{$IGUserName}",
									"comment" 		=> !empty($IGGA['comments_count']) ?  $this->tp_number_short($IGGA['comments_count']) : 0,
									"likes" 		=> !empty($IGGA['like_count']) ? $this->tp_number_short($IGGA['like_count']) : 0,
									"IGGP_PostLink" => $Permalink,
									"IG_Type"		=> $IGType,
									"IGGP_Icon"		=> $IGGP_Icon,
									"IGGP_CAROUSEL" => $IGGP_CAROUSEL_ALBUM,
									"socialIcon" 	=> $IGIcon,
									"selectFeed"    => $selectFeed,
									"FilterCategory"=> $RCategory,
									"RKey" 			=> "tp-repeater-item-$IGKey",
								);
								$IGGP_CountFiler++;
							}
						}
					}else{
						$IGArr[] = $this->SF_Error_handler($UserPost_Res, $IGKey, $RCategory, $selectFeed, $IGIcon);
					}
				}else if($IGFeedType == "IGHashtag"){
					$HashtagName = !empty($social['IGHashtagName_GP']) ? $social['IGHashtagName_GP'] : 'words';

					$HashtagID_API = "{$BashURL}/ig_hashtag_search?user_id={$GET_UserID}&q={$HashtagName}&access_token={$IGAcT}";
					$Hashtag_Databash = get_transient("IG-GP-HashtagID-Url-$IGKey");
					$Hashtag_Res = [];
					if( $Hashtag_Databash != $HashtagID_API || $GetTime != $TimeFrq ){
						$Hashtag_Res = $this->tp_api_call($HashtagID_API);
									set_transient("IG-GP-HashtagID-Url-$IGKey", $HashtagID_API, $TimeFrq);
									set_transient("IG-GP-HashtagID-data-$IGKey", $Hashtag_Res, $TimeFrq);
					}else{
						$Hashtag_Res = get_transient("IG-GP-HashtagID-data-$IGKey");
					}

					$Hashtag_CODE = !empty($Hashtag_Res['HTTP_CODE']) ? $Hashtag_Res['HTTP_CODE'] : 400;
					if($Hashtag_CODE == 200){
						$Hashtag_GetID = !empty($Hashtag_Res['data'][0]['id']) ? $Hashtag_Res['data'][0]['id'] : '';

						$Hashtag_Data = "{$BashURL}/{$Hashtag_GetID}/{$HashtagType}?user_id={$GET_UserID}&fields=id,media_type,media_url,comments_count,like_count,caption,permalink,timestamp,children{media_url,permalink,media_type}&limit=50&access_token={$IGAcT}";
						$Hashtag_Data_Databash = get_transient("IG-GP-HashtagData-Url-$IGKey");
						$Hashtag_Data_Res = [];
						if( $Hashtag_Data_Databash != $Hashtag_Data || $GetTime != $TimeFrq ){
							$Hashtag_Data_Res = $this->tp_api_call($Hashtag_Data);
										set_transient("IG-GP-HashtagData-Url-$IGKey", $Hashtag_Data, $TimeFrq);
										set_transient("IG-GP-Hashtag-Data-$IGKey", $Hashtag_Data_Res, $TimeFrq);
						}else{
							$Hashtag_Data_Res = get_transient("IG-GP-Hashtag-Data-$IGKey");
						}

						$Hashtag_Data_CODE = !empty($Hashtag_Data_Res['HTTP_CODE']) ? $Hashtag_Data_Res['HTTP_CODE'] : 400;
						if($Hashtag_Data_CODE == 200){
							
							$HashtagData = !empty($Hashtag_Data_Res['data']) ? $Hashtag_Data_Res['data'] : [];
							foreach ($HashtagData as $index => $IGHash) {
								$media_url = !empty($IGHash['media_url']) ? $IGHash['media_url'] : '';
								$permalink = !empty($IGHash['permalink']) ? $IGHash['permalink'] : '';

								$IGGP_Icon=$PostImage="";
								$Media_type = !empty($IGHash['media_type']) ? $IGHash['media_type'] : '';
								if($Media_type == 'IMAGE'){
									$PostImage = $media_url;
								}else if($Media_type == 'VIDEO'){
									$IGGP_Icon = '<svg aria-hidden="true" focusable="false" data-prefix="fad" data-icon="video" class="svg-inline--fa fa-video fa-w-18 IGGP_video" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><g class="fa-group"><path class="fa-secondary" fill="currentColor" d="M525.6 410.2L416 334.7V177.3l109.6-75.6c21.3-14.6 50.4.4 50.4 25.8v256.9c0 25.5-29.2 40.4-50.4 25.8z" opacity="0.4"></path><path class="fa-primary" fill="currentColor" d="M0 400.2V111.8A47.8 47.8 0 0 1 47.8 64h288.4a47.8 47.8 0 0 1 47.8 47.8v288.4a47.8 47.8 0 0 1-47.8 47.8H47.8A47.8 47.8 0 0 1 0 400.2z"></path></g></svg>';
									$PostImage = $media_url;
								}else if( $Media_type == 'CAROUSEL_ALBUM' ){
									$IGGP_Icon = '<svg aria-hidden="true" focusable="false" data-prefix="fad" data-icon="clone" class="svg-inline--fa fa-clone fa-w-16 IGGP_Multiple" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><g class="fa-group"><path class="fa-secondary" fill="currentColor" d="M48 512a48 48 0 0 1-48-48V176a48 48 0 0 1 48-48h48v208a80.09 80.09 0 0 0 80 80h208v48a48 48 0 0 1-48 48H48z" opacity="0.4"></path><path class="fa-primary" fill="currentColor" d="M512 48v288a48 48 0 0 1-48 48H176a48 48 0 0 1-48-48V48a48 48 0 0 1 48-48h288a48 48 0 0 1 48 48z"></path></g></svg>';
									$PostImage = !empty($IGHash['children']['data'][0]['media_url']) ? $IGHash['children']['data'][0]['media_url'] : '';
								}

								$CAROUSEL_ALBUM = !empty($IGHash['children']) ? $IGHash['children']['data'] : [];
								$IGGP_CAROUSEL_ALBUM=[];
								foreach ($CAROUSEL_ALBUM as $key => $IGGP){
									$IGGP_MediaType = !empty($IGGP['media_type']) ? $IGGP['media_type'] : 'IMAGE'; 
									$IGGP_MediaURl = !empty($IGGP['media_url']) ? $IGGP['media_url'] : '';

									if($key == 0 && $IGGP_MediaType == 'VIDEO'){
										foreach ($CAROUSEL_ALBUM as $thumb_i => $IGGP_Thumb){
											$IGGP_ThumbImg = !empty($IGGP_Thumb['media_type']) ? $IGGP_Thumb['media_type'] : 'IMAGE'; 
											if($IGGP_ThumbImg == 'IMAGE'){
												$PostImage = !empty($IGGP_Thumb['media_url']) ? $IGGP_Thumb['media_url'] : '';
												break;
											}
										}
									}
									if($IGGP_MediaType == 'IMAGE'){
										$IGGP_CAROUSEL_ALBUM[] = array(
											"IGGPCAR_Index" => $index,
											"IGGPImg_Type" => $IGGP_MediaType,
											"IGGPURL_Media" => $IGGP_MediaURl,
										);
									}
								}

								if( $Media_type != 'VIDEO' && $IGGP_CountFiler < $IGcount ){
									$IGArr[] = array(
										"Feed_Index"	=> $index,
										"PostId"		=> !empty($IGHash['id']) ? $IGHash['id'] : '',
										"Massage" 		=> '',
										"Description"	=> !empty($IGHash['caption']) ? $IGHash['caption'] : '',
										"Type" 			=> $Media_type,
										"PostLink" 		=> $media_url,
										"PostImage" 	=> $PostImage,
										"CreatedTime" 	=> !empty($IGHash['timestamp']) ? $this->feed_Post_time($IGHash['timestamp']) : '',
										"UserLink" 		=> $permalink,
										"comment" 		=> !empty($IGHash['comments_count']) ?  $this->tp_number_short($IGHash['comments_count']) : 0,
										"likes" 		=> !empty($IGHash['like_count']) ? $this->tp_number_short($IGHash['like_count']) : 0,
										"IG_Type"		=> $IGType,
										"IGGP_Icon"		=> $IGGP_Icon,
										"IGGP_CAROUSEL" => $IGGP_CAROUSEL_ALBUM,
										"IGGP_PostLink" => $permalink,
										"socialIcon" 	=> $IGIcon,
										"selectFeed"    => $selectFeed,
										"FilterCategory"=> $RCategory,
										"RKey" 			=> "tp-repeater-item-$IGKey",
									);
									$IGGP_CountFiler++;
								}
							}

						}else{
							$IGArr[] = $this->SF_Error_handler($Hashtag_Data_Res, $IGKey, $RCategory, $selectFeed);
						}
					}else{
						$IGArr[] = $this->SF_Error_handler($Hashtag_Res, $IGKey, $RCategory, $selectFeed, $IGIcon);
					}
				}else if($IGFeedType == "IGTag"){
					$Tag_API = "{$BashURL}/{$GET_UserID}/tags?fields=id,username,media_type,media_url,like_count,caption,timestamp,permalink,comments_count,media_product_type,children{media_url,permalink,media_type}&limit={$IGGPcount}&access_token={$IGAcT}";
					$Tag_Databash = get_transient("IG-GP-Tag-Url-$IGKey");
					$Tag_Res=[];
					if( $Tag_Databash != $Tag_API || $GetTime != $TimeFrq ){
						$Tag_Res = $this->tp_api_call($Tag_API);
									 set_transient("IG-GP-Tag-Url-$IGKey", $Tag_API, $TimeFrq);
									 set_transient("IG-GP-Tag-Data-$IGKey", $Tag_Res, $TimeFrq);
					}else{
						$Tag_Res = get_transient("IG-GP-Tag-Data-$IGKey");
					}

					$Tag_CODE = !empty($Tag_Res['HTTP_CODE']) ? $Tag_Res['HTTP_CODE'] : 400;
					$Tag_Data = !empty($Tag_Res['data']) ? $Tag_Res['data'] : [];
					if( $Tag_CODE == 200 && !empty($Tag_Data) ){
						foreach ($Tag_Data as $index => $Tag) {
							$CAROUSEL_ALBUM = !empty($Tag['children']) ? $Tag['children']['data'] : [];
							$Permalink = !empty($Tag['permalink']) ? $Tag['permalink'] : '';
							$Tag_Username = !empty($Tag['username']) ? $Tag['username'] : '';

							$IGGP_Icon="";
							$Media_type = !empty($Tag['media_type']) ? $Tag['media_type'] : '';
							if($Media_type == 'IMAGE'){
							}else if($Media_type == 'VIDEO'){
								$IGGP_Icon = '<svg aria-hidden="true" focusable="false" data-prefix="fad" data-icon="video" class="svg-inline--fa fa-video fa-w-18 IGGP_video" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><g class="fa-group"><path class="fa-secondary" fill="currentColor" d="M525.6 410.2L416 334.7V177.3l109.6-75.6c21.3-14.6 50.4.4 50.4 25.8v256.9c0 25.5-29.2 40.4-50.4 25.8z" opacity="0.4"></path><path class="fa-primary" fill="currentColor" d="M0 400.2V111.8A47.8 47.8 0 0 1 47.8 64h288.4a47.8 47.8 0 0 1 47.8 47.8v288.4a47.8 47.8 0 0 1-47.8 47.8H47.8A47.8 47.8 0 0 1 0 400.2z"></path></g></svg>';
							}else if( $Media_type == 'CAROUSEL_ALBUM' ){
								$IGGP_Icon = '<svg aria-hidden="true" focusable="false" data-prefix="fad" data-icon="clone" class="svg-inline--fa fa-clone fa-w-16 IGGP_Multiple" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><g class="fa-group"><path class="fa-secondary" fill="currentColor" d="M48 512a48 48 0 0 1-48-48V176a48 48 0 0 1 48-48h48v208a80.09 80.09 0 0 0 80 80h208v48a48 48 0 0 1-48 48H48z" opacity="0.4"></path><path class="fa-primary" fill="currentColor" d="M512 48v288a48 48 0 0 1-48 48H176a48 48 0 0 1-48-48V48a48 48 0 0 1 48-48h288a48 48 0 0 1 48 48z"></path></g></svg>';
							}

							$CAROUSEL_ALBUM = !empty($Tag['children']) ? $Tag['children']['data'] : [];
							$IGGP_CAROUSEL_ALBUM=[];
							foreach ($CAROUSEL_ALBUM as $key => $IGGP){
								$IGGP_MediaType = !empty($IGGP['media_type']) ? $IGGP['media_type'] : 'IMAGE'; 
								$IGGP_MediaURl = !empty($IGGP['media_url']) ? $IGGP['media_url'] : '';

								if($key == 0 && $IGGP_MediaType == 'VIDEO'){
									foreach ($CAROUSEL_ALBUM as $thumb_i => $IGGP_Thumb){
										$IGGP_ThumbImg = !empty($IGGP_Thumb['media_type']) ? $IGGP_Thumb['media_type'] : 'IMAGE'; 
										if($IGGP_ThumbImg == 'IMAGE'){
											$PostImage = !empty($IGGP_Thumb['media_url']) ? $IGGP_Thumb['media_url'] : $Default_Img;
											break;
										}
									}
								}
								if($IGGP_MediaType == 'IMAGE'){
									$IGGP_CAROUSEL_ALBUM[] = array(
										"IGGPCAR_Index" => $index,
										"IGGPImg_Type" => $IGGP_MediaType,
										"IGGPURL_Media" => $IGGP_MediaURl,
									);
								}
							}

							$Taggedby = 'Tagged by <a href="https://www.instagram.com/'.esc_attr($Tag_Username).'" class="tp-mantion" target="_blank" rel="noopener noreferrer"> @'.esc_attr($Tag_Username).'<a>';

							if( $Media_type != 'VIDEO' && $IGGP_CountFiler < $IGcount ) {
								$IGArr[] = array(
									"Feed_Index"	=> $index,
									"PostId"		=> !empty($Tag['id']) ? $Tag['id'] : '',
									"Massage" 		=> $Taggedby,
									"Description"	=> !empty($Tag['caption']) ? $Tag['caption'] : '',
									"Type" 			=> $Media_type,
									"PostLink" 		=> !empty($Tag['media_url']) ? $Tag['media_url'] : '',
									"CreatedTime" 	=> !empty($Tag['timestamp']) ? $this->feed_Post_time($Tag['timestamp']) : '',
									"PostImage" 	=> !empty($Tag['media_url']) ? $Tag['media_url'] : '',
									"UserName" 		=> $GET_UserName,
									"UserImage" 	=> $GET_Profile,
									"UserLink" 		=> $Permalink,
									"comment" 		=> !empty($Tag['comments_count']) ?  $this->tp_number_short($Tag['comments_count']) : 0,
									"likes" 		=> !empty($Tag['like_count']) ? $this->tp_number_short($Tag['like_count']) : 0,
									"IG_Type"		=> $IGType,
									"IGGP_Icon"		=> $IGGP_Icon,
									"IGGP_CAROUSEL" => $IGGP_CAROUSEL_ALBUM,
									"IGGP_PostLink" => $Permalink,
									"socialIcon" 	=> $IGIcon,
									"selectFeed"    => $selectFeed,
									"FilterCategory"=> $RCategory,
									"RKey" 			=> "tp-repeater-item-$IGKey",
								);
								$IGGP_CountFiler++;
							}
						}
					}else{
						$IGArr[] = $this->SF_Error_handler($Tag_Res, $IGKey, $RCategory, $selectFeed, $IGIcon);
					}
				}

			}else{
				$IGArr[] = $this->SF_Error_handler($UserID_Res, $IGKey, $RCategory, $selectFeed, $IGIcon);
			}
		}
	  return $IGArr;
    }
    protected function VimeoFeed($social){
		$BaseURL = 'https://api.vimeo.com';
		$VmKey = !empty($social['_id']) ? $social['_id'] : '';
		$VmAcT = !empty($social['RAToken']) ? $social['RAToken'] : '';
		$VmType = !empty($social['VimeoType']) ? $social['VimeoType'] : 'Vm_User';
		$VmUname = !empty($social['VmUname']) ? $social['VmUname'] : '';
		$VmQsearch = !empty($social['VmQsearch']) ? $social['VmQsearch'] : '';
		$VmChannel = !empty($social['VmChannel']) ? $social['VmChannel'] : '';
		$VmGroup = !empty($social['VmGroup']) ? $social['VmGroup'] : '';
		$VmCategories = !empty($social['VmCategories']) ? str_replace(' ','', $social['VmCategories']) : '';
		$VmAlbum = !empty($social['VmAlbum']) ? $social['VmAlbum'] : '';
		$VmMax = !empty($social['MaxR']) ? $social['MaxR'] : 6;
		$VmTime = !empty($social['TimeFrq']) ? $social['TimeFrq'] : '3600';
		$VmSelectFeed = !empty($social['selectFeed']) ? $social['selectFeed'] : '';
		$VmRCategory = !empty($social['RCategory']) ? $social['RCategory'] : '';
		$VmIcon = 'fab fa-vimeo-v social-logo-yt';

		$URL='';$Vimeo='';
		if($VmType == "Vm_User"){
			$URL = "{$BaseURL}/users/{$VmUname}/videos?access_token={$VmAcT}&per_page={$VmMax}&page=1";
		}else if($VmType == "Vm_search"){
			$URL = "{$BaseURL}/videos?access_token={$VmAcT}&query={$VmQsearch}&per_page={$VmMax}&page=1";
		}else if($VmType == "Vm_liked"){
			$URL = "{$BaseURL}/users/{$VmUname}/likes?access_token={$VmAcT}&per_page={$VmMax}&page=1";
		}else if($VmType == "Vm_Channel"){
			$URL = "{$BaseURL}/channels/{$VmChannel}/videos?access_token={$VmAcT}&per_page={$VmMax}&page=1";
		}else if($VmType == "Vm_Group"){
			$URL = "{$BaseURL}/groups/{$VmGroup}/videos?access_token={$VmAcT}&per_page={$VmMax}&page=1";
		}else if($VmType == "Vm_Album"){
			$VmAPass = !empty($social['VmAlbumPass']) ? "&password=".$social['VmAlbumPass'] : '';
			$URL = "{$BaseURL}/users/{$VmUname}/albums/{$VmAlbum}/videos?access_token={$VmAcT}&per_page={$VmMax}&page=1$VmAPass";
		}else if($VmType == "Vm_categories"){
			$URL = "{$BaseURL}/categories/{$VmCategories}/videos?access_token={$VmAcT}&per_page={$VmMax}&page=1";
		}
		$GetVmURL = get_transient("Vm-Url-$VmKey");
		$GetVmTime = get_transient("Vm-Time-$VmKey");
		if( ($GetVmURL != $URL) || ($GetVmTime != $VmTime) ){
			$Vimeo = $this->tp_api_call($URL);
				set_transient("Vm-Url-$VmKey", $URL, $VmTime);
				set_transient("Vm-Time-$VmKey", $VmTime, $VmTime);
				set_transient("Data-Vm-$VmKey", $Vimeo, $VmTime);
		}else{
			$Vimeo = get_transient("Data-Vm-$VmKey");
		}

		$VmArr = [];
		$HTTP_CODE = !empty($Vimeo['HTTP_CODE']) ? $Vimeo['HTTP_CODE'] : '';
		if($HTTP_CODE == 200){
			$VmData = !empty($Vimeo['data']) ? $Vimeo['data'] : [];
			foreach ($VmData as $index => $Vmsocial) {
				$VmUrl = !empty($Vmsocial['uri']) ?  str_replace('videos', 'video', $Vmsocial['uri'])  : '';
				$VmImg = !empty($Vmsocial['pictures']) ? $Vmsocial['pictures']["sizes"] : [];
				$VmThumb = [];
				foreach ($VmImg as $VmValue) {
					$VmThumb[] = $VmValue["link"]; 
				}
				$VmImage = end($VmThumb);
				$VmProfile = !empty($Vmsocial["user"]) ? $Vmsocial["user"]["pictures"]["sizes"] : [];
				$VmPThumb = [];
				foreach ($VmProfile as $Vmlink) { 
					$VmPThumb[] = $Vmlink["link"];
				}

				$VmProfileLink = end($VmPThumb);				
				$VmArr[] = array(
					"Feed_Index"	=> $index,
					"PostId"		=> !empty($Vmsocial['resource_key']) ? $Vmsocial['resource_key'] : '',
					"Massage" 		=> !empty($Vmsocial['name']) ? $Vmsocial['name'] : '',
					"Description"	=> !empty($Vmsocial['description']) ? $Vmsocial['description'] : '',
					"Type" 			=> !empty($Vmsocial['type']) ? $Vmsocial['type'] : '',
					"PostLink" 		=> !empty($Vmsocial['link']) ? $Vmsocial['link'] : '',
					"CreatedTime" 	=> !empty($Vmsocial['created_time']) ?  $this->feed_Post_time($Vmsocial['created_time']) : '',
					"PostImage" 	=> !empty($VmImage) ? $VmImage : '',
					"UserName" 		=> !empty($Vmsocial["user"]["name"]) ? $Vmsocial["user"]["name"] : '',
					"UserImage" 	=> !empty($VmProfileLink) ? $VmProfileLink : '',
					"UserLink" 		=> !empty($Vmsocial["user"]["link"]) ? $Vmsocial["user"]["link"] : '',
					"share" 		=> !empty($Vmsocial["user"]["metadata"]) ? $this->tp_number_short($Vmsocial["user"]["metadata"]["connections"]["shared"]["total"]) : 0,
					"likes" 		=> !empty($Vmsocial['metadata']) ? $this->tp_number_short($Vmsocial["metadata"]["connections"]["likes"]["total"]) : 0,
					"comment" 		=> !empty($Vmsocial['metadata']) ? $this->tp_number_short($Vmsocial["metadata"]["connections"]["comments"]["total"]) : 0,
					"Embed" 		=> "https://player.vimeo.com{$VmUrl}",
					"EmbedType"     => !empty($Vmsocial['type']) ? $Vmsocial['type'] : '',
					"socialIcon" 	=> $VmIcon,
					"selectFeed"    => $VmSelectFeed,
					"FilterCategory"=> $VmRCategory,
					"RKey" 			=> "tp-repeater-item-$VmKey",
				);
			}
		}else{
			$Error = !empty($Vimeo['error']) ? $Vimeo['error'] : '';
			$ErrorData['error']['message'] = !empty($Vimeo['error']) && !empty($Vimeo['developer_message']) ? '<b>'.$Vimeo['error'].'</b></br>'.$Vimeo['developer_message'] : '';
			$ErrorData['error']['HTTP_CODE'] = !empty($Vimeo['HTTP_CODE']) ? $Vimeo['HTTP_CODE'] : 400;

			$VmArr[] = $this->SF_Error_handler($ErrorData, $VmKey, $VmRCategory, $VmSelectFeed, $VmIcon);
	    }
	    return $VmArr;
    }
    protected function TwetterFeed($social){
		$settings = $this->get_settings_for_display();
		$BaseURL = "https://api.twitter.com/1.1";
		$TwKey = !empty($social['_id']) ? $social['_id'] : '';
		$TwApi = !empty($social['TwApi']) ? $social['TwApi'] : '';
		$TwApiSecret = !empty($social['TwApiSecret']) ? $social['TwApiSecret'] : '';
		$TwAccesT = !empty($social['TwAccesT']) ? $social['TwAccesT'] : '';
		$TwAccesTS = !empty($social['TwAccesTS']) ? $social['TwAccesTS'] : '';
		$twcount = !empty($social['MaxR']) ? $social['MaxR'] * 5 : 6 * 5;
		$TwTime = !empty($social['TimeFrq']) ? $social['TimeFrq'] : '3600';
		$MediaFilter = !empty($settings['MediaFilter']) ? $settings['MediaFilter'] : 'default';
		$RCategory = !empty($social['RCategory']) ? $social['RCategory'] : '';
		$selectFeed = !empty($social['selectFeed']) ? $social['selectFeed'] : '';
		$TwIcon = 'fab fa-twitter social-logo-tw';

		$url = '';
		$getfield = '';
		$TwArr = [];
		$TwResponce = [];
		if( !empty($TwApi) && !empty($TwApiSecret) && !empty($TwAccesT) && !empty($TwAccesTS) ){
			$TwUsername = !empty($social['TwUsername']) ? $social['TwUsername'] : '';
			$TwType = !empty($social['TwfeedType']) ? $social['TwfeedType'] : '';
			$TwSearch = !empty($social['TwSearch']) ? $social['TwSearch'] : '';
			$TwDmedia = !empty($social['TwDmedia'] == 'yes') ? true : false;
			$TwComRep = !empty($social['TwComRep']) ? false : true;
			$TwRetweet = !empty($social['TwRetweet'] == 'yes') ? true : false;

			require_once(THEPLUS_PATH.'includes/social-feed/TwitterAPIExchange.php');
			$Twsettings = array(
				'consumer_key' => $TwApi,
				'consumer_secret' => $TwApiSecret,
				'oauth_access_token' => $TwAccesT,
				'oauth_access_token_secret' => $TwAccesTS
			);

			if( $TwType == 'wptimline' ){
				$Twtimeline = !empty($social['Twtimeline']) ? $social['Twtimeline'] : '';
				if( $Twtimeline == 'Hometimline' ){
					$url = "{$BaseURL}/statuses/home_timeline.json";
					$getfield = "?screen_name={$TwUsername}&count={$twcount}&exclude_replies={$TwComRep}&include_entities={$TwDmedia}&tweet_mode=extended";
				}else if( $Twtimeline == 'mentionstimeline' ){
					$url = "{$BaseURL}/statuses/mentions_timeline.json";
					$getfield = "?count={$twcount}&include_entities={$TwDmedia}&tweet_mode=extended";
				}
			}else if( $TwType == 'userfeed' ){
				$url = "{$BaseURL}/statuses/user_timeline.json";
				$getfield = "?screen_name={$TwUsername}&count={$twcount}&include_entities={$TwDmedia}&include_rts={$TwRetweet}&exclude_replies={$TwComRep}&tweet_mode=extended";
			}else if( $TwType == 'twsearch' ){
				$TwSearch = !empty($social['TwSearch']) ? $social['TwSearch'] : 'twitter';
				$TwRtype = !empty($social['TwRtype']) ? $social['TwRtype'] : 'recent';

				$url = "{$BaseURL}/search/tweets.json";
				$getfield = "?q={$TwSearch}&result_type={$TwRtype}&count={$twcount}&include_entities={$TwDmedia}&tweet_mode=extended";
			}else if( $TwType == 'userlist' ){
				$Twlistsid = !empty($social['Twlistsid']) ? $social['Twlistsid'] : '99921778';
				$url = "{$BaseURL}/lists/statuses.json";
				$getfield = "?list_id={$Twlistsid}&count={$twcount}&include_rts={$TwRetweet}&include_entities={$TwDmedia}&tweet_mode=extended";
			}else if( $TwType == 'twcollection' ){
				$Twcollsid = !empty($social['Twcollsid']) ? $social['Twcollsid'] : '539487832448843776';
				$url = "{$BaseURL}/collections/entries.json";
				$getfield = "?id=custom-{$Twcollsid}&count={$twcount}&tweet_mode=extended";
			}else if( $TwType == 'userlikes' ){
				$url = "{$BaseURL}/favorites/list.json";
				$getfield = "?screen_name={$TwUsername}&count={$twcount}&include_entities={$TwDmedia}&tweet_mode=extended";
			}else if( $TwType == 'twtrends' ){
				$TwWOEID = !empty($social['TwWOEID']) ? $social['TwWOEID'] : '23424848';
				$url = "{$BaseURL}/trends/place.json";
				$getfield = "?id={$TwWOEID}";
			}else if($TwType == 'twRTMe'){
				$url = "{$BaseURL}/statuses/retweets_of_me.json";
				$getfield = "?count={$twcount}&include_entities={$TwDmedia}&include_user_entities=true&tweet_mode=extended";
			}else if( $TwType == 'Twcustom' ){
				$TwcustId = !empty($social['TwcustId']) ? $social['TwcustId'] : '';
				$url = "{$BaseURL}/statuses/lookup.json";
				$getfield = "?id={$TwcustId}&include_entities={$TwDmedia}&tweet_mode=extended";
			}			
			$GetTwBaseUrl = get_transient("Tw-BaseUrl-$TwKey");
			$GetTwURL = get_transient("Tw-Url-$TwKey");
			$GetTwTime = get_transient("Tw-Time-$TwKey");
			if( ($GetTwURL != $getfield) || ($GetTwBaseUrl != $url) || ($TwTime != $GetTwTime) ){
					$requestMethod = 'GET';
					$twitter = new \TwitterAPIExchange($Twsettings);
					$TwResponse = $twitter->setGetfield($getfield)->buildOauth( $url, $requestMethod )->performRequest();
					$TwResponce = json_decode($TwResponse,true);

					set_transient("Tw-BaseUrl-$TwKey", $url, $TwTime);
					set_transient("Tw-Url-$TwKey", $getfield, $TwTime);
					set_transient("Tw-Time-$TwKey", $TwTime, $TwTime);
					set_transient("Data-tw-$TwKey", $TwResponce, $TwTime);
			}else{
				$TwResponce = get_transient("Data-tw-$TwKey");
			}			
		}

		$Twcode="";		
		if(!empty($TwResponce['errors'])){	
			$Twcode = 400; 
		}

		if(!empty($TwResponce && $TwType != 'twtrends' && $Twcode != 400 )){
			if( $TwType == 'twsearch' ){
				$TwResponce = !empty($TwResponce['statuses']) ? $TwResponce['statuses'] : [];
			}else if( $TwType == 'twcollection' ){
				$TwColluser = !empty($TwResponce['objects']['users']) ? $TwResponce['objects']['users'] : [];
				$TwResponce = !empty($TwResponce['objects']['tweets']) ? $TwResponce['objects']['tweets'] : [];
			}
			$CountFiler = 0;
			foreach ($TwResponce as $index => $TwData) {
				$twid = !empty($TwData['id']) ? $TwData['id'] : '';
				$retweet_count = !empty($TwData['retweet_count']) ? $this->tp_number_short($TwData['retweet_count']) : 0;
				$favorite_count = !empty($TwData['favorite_count']) ? $this->tp_number_short($TwData['favorite_count']) : 0;			
				$Full_Text = !empty($TwData['full_text']) ? $TwData['full_text'] : '';
				$TwUsername = !empty($TwData['user']['name']) ? $TwData['user']['name'] : '';
				$twname = !empty($TwData['user']['screen_name']) ? $TwData['user']['screen_name'] : '';
				$TwProfile = !empty($TwData['user']['profile_image_url']) ? $TwData['user']['profile_image_url'] : '';
				
				if(!empty($TwData['extended_entities']['media'][0]['media_url']) && ((!empty($social['TwDmedia']) && $social['TwDmedia']=='yes') || (!empty($settings['layout']) && $settings['layout']=='carousel'))){
					$TwImg = !empty($TwData['extended_entities']['media'][0]['media_url']) ? $TwData['extended_entities']['media'][0]['media_url'] : '';
					$Twlink = !empty($TwData['extended_entities']['media'][0]['media_url']) ? $TwData['extended_entities']['media'][0]['media_url'] : '';
					$Type = !empty($TwData['extended_entities']['media'][0]['type']) ? $TwData['extended_entities']['media'][0]['type'] : '';
				}else{
					$TwImg = !empty($TwData['entities']['media'][0]['media_url']) ? $TwData['entities']['media'][0]['media_url'] : '';
					$Twlink = !empty($TwData['entities']['media'][0]['media_url']) ? $TwData['entities']['media'][0]['media_url'] : '';
					$Type = !empty($TwData['entities']['media'][0]['type']) ? $TwData['entities']['media'][0]['type'] : '';
				}				
				
				if( $TwType == 'twcollection' ){
					$twCuser = !empty($TwData['user']) ? $TwData['user']['id'] : '';
					foreach ($TwColluser as $data) {
						$twUid = !empty($data['id']) ? $data['id'] : '';
						if( $twCuser == $twUid ) {
							$TwUsername = !empty($data['name']) ? $data['name'] : '';
							$Fbname = !empty($data['screen_name']) ? $data['screen_name'] : '';
							$TwProfile = !empty($data['profile_image_url']) ? $data['profile_image_url'] : '';
						}
					}
				}

				$TwFilter = !empty($social['MaxR']) ? $social['MaxR'] : 6; 
				if( ($MediaFilter == 'default' && $TwFilter > $index) || ($MediaFilter == 'ompost' && !empty($Twlink) && $CountFiler <= $TwFilter ) || ($MediaFilter == 'hmcontent' && empty($Twlink) && $CountFiler <= $TwFilter) ){
						$TwArr[] = array(
							"Feed_Index"	=> $index,
							"PostId"		=> $twid,
							"Description"	=> $Full_Text,
							"Type" 			=> $Type,
							"PostLink" 		=> !empty($Twlink) ? $Twlink : '',
							"CreatedTime" 	=> !empty($TwData['created_at']) ? $this->feed_Post_time($TwData['created_at']) : '',
							"PostImage" 	=> !empty($TwImg) ? $TwImg : '',
							"UserName" 		=> $TwUsername,
							"UserImage" 	=> $TwProfile,
							"UserLink" 		=> "https://twitter.com/{$twname}",
							"TWRetweet"		=> $retweet_count,
							"TWLike"		=> $favorite_count,
							"TwReplyURL" 	=> "https://twitter.com/intent/tweet?in_reply_to={$twid}",
							"TwRetweetURL" 	=> "https://twitter.com/intent/retweet?tweet_id={$twid}",
							"TwlikeURL" 	=> "https://twitter.com/intent/like?tweet_id={$twid}",
							"TwtweetURL" 	=> "https://twitter.com/{$twname}/status/{$twid}",
							"socialIcon" 	=> $TwIcon,
							"selectFeed"    => $selectFeed,
							"FilterCategory"=> $RCategory,
							"RKey" 			=> "tp-repeater-item-$TwKey",
						);
					$CountFiler++;
				}
			}

		}else if(!empty($TwResponce && $TwType == 'twtrends' && $Twcode != 400 )){
			$TwResTrends = !empty($TwResponce[0]['trends']) ? $TwResponce[0]['trends'] : [];
			foreach ($TwResTrends as $index => $trends) {
				$TrendName = !empty($trends['name']) ? $trends['name'] : '';
				$TrendURL = !empty($trends['url']) ? $trends['url'] : '';

				$TwArr[] = array(
					"Feed_Index" => $index,
					"UserName"   => $TrendName,
					"UserLink"	 => $TrendURL,
					"socialIcon" => $TwIcon,
				);
			}

		}else{
			$Msg = "";
			if(empty($TwApi)){
				$Msg .= "Empty Consumer Key </br>";
			}
			if(empty($TwApiSecret)){
				$Msg .= "Empty Consumer Secret </br>";
			}
			if(empty($TwAccesT)){
				$Msg .= "Empty Access Token </br>";
			}
			if(empty($TwAccesTS)){
				$Msg .= "Empty Access Token Secret </br>";
			}

			$Error = !empty($TwResponce['errors']) ? $TwResponce['errors'][0] : [];
			$ErrorData['error']['HTTP_CODE'] = !empty($Error['code']) ? $Error['code'] : 400;
			$ErrorData['error']['message'] = !empty($Error['message']) ? $Error['message'] : $Msg;

			$TwArr[] = $this->SF_Error_handler($ErrorData, $TwKey, $RCategory, $selectFeed, $TwIcon);
		}

	   return $TwArr;
    }
    protected function YouTubeFeed($social){
		$BaseURL = 'https://www.googleapis.com/youtube/v3';
		$YtKey = !empty($social['_id']) ? $social['_id'] : '';
		$YtAcT = !empty($social['RAToken']) ? $social['RAToken'] : '';
		$YtType = !empty($social['RYtType']) ? $social['RYtType'] : 'YT_Channel';
		$YtName = !empty($social['YtName']) ? $social['YtName'] : '';
		$YtOrder = !empty($social['YTvOrder']) ? $social['YTvOrder'] : 'date';
		$YTthumbnail = !empty($social['YTthumbnail']) ? $social['YTthumbnail'] : 'medium';
		$YtMax = !empty($social['MaxR']) ? $social['MaxR'] : 6;
		$YtTime = !empty($social['TimeFrq']) ? $social['TimeFrq'] : '3600';
		$YtCategory = !empty($social['RCategory']) ? $social['RCategory'] : '';
		$YtselectFeed = !empty($social['selectFeed']) ? $social['selectFeed'] : '';
		$YtIcon = 'fab fa-youtube social-logo-yt';

		$URL = '';
		$UserLink = '';
		$YTData = [];
		$YtArr = [];
		if($YtType == 'YT_Userfeed'){
			$YTUserAPI = "{$BaseURL}/channels?part=snippet&forUsername={$YtName}&key={$YtAcT}";
			$GetYtuser = get_transient("Yt-user-$YtKey");
			$GetYtUserTime = get_transient("Yt-user-Time-$YtKey");
			if( ($GetYtuser != $YTUserAPI) || ($GetYtUserTime != $YtTime) ){
				$YtUNdata = $this->tp_api_call($YTUserAPI);
					set_transient("Data-Yt-user-$YtKey", $YtUNdata, $YtTime);
					set_transient("Yt-user-$YtKey", $YTUserAPI, $YtTime);
					set_transient("Yt-user-Time-$YtKey", $YtTime, $YtTime);
			}else{
				$YtUNdata = get_transient("Data-Yt-user-$YtKey");
			}
			$YTStatus = !empty($YtUNdata['HTTP_CODE']) ? $YtUNdata['HTTP_CODE'] : '';
			if($YTStatus == 200){
				$YTUserID = !empty($YtUNdata['items'][0]['id']) ? $YtUNdata['items'][0]['id'] : '';
				$YtPic = '';
				$YtPicPath = $YtUNdata['items'][0]['snippet']['thumbnails'];
				if(!empty($YtPicPath)){
					if(!empty($YtPicPath['default']['url'])){ $YtPic = $YtPicPath['default']['url']; }
					if(!empty($YtPicPath['medium']['url'])){ $YtPic = $YtPicPath['medium']['url']; }
					if(!empty($YtPicPath['high']['url'])){ $YtPic = $YtPicPath['high']['url']; }
				}
				$UserLink = array( 'UserLink'=> "https://www.youtube.com/user/{$YtName}", 'YTprofile'=> $YtPic );
				$URL = "{$BaseURL}/search?part=snippet&type=video&order={$YtOrder}&maxResults={$YtMax}&channelId={$YTUserID}&key={$YtAcT}";
			}
		}else if($YtType == 'YT_Channel'){
			$YtChannel = !empty($social['YTChannel']) ? $social['YTChannel'] : '';
			$UserLink = array('UserLink'=> "https://www.youtube.com/channel/{$YtChannel}");
			$URL = "{$BaseURL}/search?part=snippet&type=video&order={$YtOrder}&maxResults={$YtMax}&channelId={$YtChannel}&key={$YtAcT}";
		}else if($YtType == 'YT_Playlist'){
			$YtPlaylist = !empty($social['YTPlaylist']) ? $social['YTPlaylist'] : '';
			$UserLink = array('UserLink'=> "https://www.youtube.com/playlist?list={$YtPlaylist}");
			$URL = "{$BaseURL}/playlistItems?part=snippet&playlistId={$YtPlaylist}&maxResults={$YtMax}&key={$YtAcT}";
		}else if($YtType == 'YT_Search'){
			$Ytsearch = !empty($social['YTsearchQ']) ? $social['YTsearchQ'] : '';
			$UserLink = array('UserLink'=> "https://www.youtube.com/channel/");
			$URL = "{$BaseURL}/search?part=id,snippet&q={$Ytsearch}&type=video&maxResults={$YtMax}&key={$YtAcT}";
		}

		$GetYtURL = get_transient("Yt-Url-$YtKey");
		$GetYtTime = get_transient("Yt-Time-$YtKey");
		if( ($GetYtURL != $URL) || ($GetYtTime != $YtTime) ){
			$YTPData = $this->tp_api_call($URL);
			$YTData = array_merge($UserLink, $YTPData);
				set_transient("Yt-Url-$YtKey", $URL, $YtTime);
				set_transient("Yt-Time-$YtKey", $YtTime, $YtTime);
				set_transient("Data-Yt-$YtKey", $YTData, $YtTime);
		}else{
			$Yt_S_Data = get_transient("Data-Yt-$YtKey");
			$YTData = array_merge($UserLink, $Yt_S_Data);
		}

		$HTTP_CODE = !empty($YTData['HTTP_CODE']) ? $YTData['HTTP_CODE'] : '';
		if($HTTP_CODE == 200){
			$UserLink = !empty($YTData['UserLink']) ? $YTData['UserLink'] : '';
			$YtProfile = !empty($YTData['YTprofile']) ? $YTData['YTprofile'] : '';			
			$Ytpost = !empty($YTData['items']) ? $YTData['items'] : [];			
			foreach ($Ytpost as $index => $YtSearch) {
				$snippet = !empty($YtSearch['snippet']) ? $YtSearch['snippet'] : '';
				$VideoId = !empty($YtSearch['id']['videoId']) ? $YtSearch['id']['videoId'] : '';

				$thumbnails = '';
				if($YTthumbnail == 'default' && !empty($snippet['thumbnails']['default']['url']) ){
					$thumbnails = $snippet['thumbnails']['default']['url'];
				}else if($YTthumbnail == 'medium' && !empty($snippet['thumbnails']['medium']['url']) ){
					$thumbnails = $snippet['thumbnails']['medium']['url'];
				}else if($YTthumbnail == 'high' && !empty($snippet['thumbnails']['high']['url']) ){
					$thumbnails = $snippet['thumbnails']['high']['url'];
				}else if($YTthumbnail == 'standard' && !empty($snippet['thumbnails']['standard']['url']) ){
					$thumbnails = $snippet['thumbnails']['standard']['url'];
				}else if($YTthumbnail == 'maxres' && !empty($snippet['thumbnails']['maxres']['url']) ){
					$thumbnails = $snippet['thumbnails']['maxres']['url'];
				}

				if($YtType == 'YT_Userfeed' || $YtType == 'YT_Channel' || $YtType == 'YT_Search'){
					$YtVideoUrl = "https://www.youtube.com/watch?v={$VideoId}";
				}else if($YtType == 'YT_Playlist'){
					$V_ID = $VideoId = !empty($snippet['resourceId']['videoId']) ? $snippet['resourceId']['videoId'] : '';
					$P_ID = !empty($snippet['playlistId']) ? $snippet['playlistId'] : '';
					$YtVideoUrl = "https://www.youtube.com/watch?v={$V_ID}&list={$P_ID}";
				}
				
				if($YtType == 'YT_Playlist' || $YtType == 'YT_Search' || $YtType == 'YT_Channel'){
					$channelId = !empty($snippet['channelId']) ? $snippet['channelId'] : '';
					$YTsPic = "{$BaseURL}/channels?part=snippet&id={$channelId}&key={$YtAcT}";	
					if( (get_transient("Yt-C-Url-$YtKey") != $YTsPic) || (get_transient("Yt-c-Time-$YtKey") != $YtTime) ){
						$YTRPic = $this->tp_api_call($YTsPic);
							set_transient("Yt-C-Url-$YtKey", $YTsPic, $YtTime);
							set_transient("Yt-c-Time-$YtKey", $YtTime, $YtTime);
							set_transient("Data-c-Yt-$YtKey", $YTRPic, $YtTime);
					}else{
						$YTRPic = get_transient("Data-c-Yt-$YtKey");	
					}
					$YtSstatus = !empty($YTRPic['HTTP_CODE']) ? $YTRPic['HTTP_CODE'] : '';
					if($YtSstatus == 200){
						$YtProfile = (($YTRPic['items'][0]['snippet']['thumbnails']['high']['url']) ? $YTRPic['items'][0]['snippet']['thumbnails']['high']['url'] : '');
					}
				}
				$GetComment = "{$BaseURL}/videos?part=statistics&id={$VideoId}&maxResults={$YtMax}&key={$YtAcT}";
				$YtCommentAll = $this->tp_api_call($GetComment);
				$HTTP_CODE_C = !empty($YtCommentAll['HTTP_CODE']) ? $YtCommentAll['HTTP_CODE'] : '';
				if($HTTP_CODE_C == 200){
					$statistics = !empty($YtCommentAll['items'][0]['statistics']) ? $YtCommentAll['items'][0]['statistics'] : '';
					$YtCMTstatus = !empty($YtCommentAll['HTTP_CODE']) ? $YtCommentAll['HTTP_CODE'] : '';
					if($YtCMTstatus == 200 && !empty($statistics)){
						$view = !empty($statistics) && !empty($statistics['viewCount']) ? $statistics['viewCount'] : 0;
						$like = !empty($statistics) && !empty($statistics['likeCount']) ? $statistics['likeCount'] : 0;
						$Dislike = !empty($statistics) && !empty($statistics['dislikeCount']) ? $statistics['dislikeCount'] : 0;
						$comment = !empty($statistics) && !empty($statistics['commentCount'])  ? $statistics['commentCount'] : 0;
					}
				}
				$YtArr[] = array(
					"Feed_Index"	=> $index,
					"PostId"		=> $VideoId,
					"Massage" 		=> !empty($snippet['title']) ? $snippet['title'] : '',
					"Description"	=> !empty($snippet['description']) ? $snippet['description'] : '',
					"Type" 			=> 'video',
					"PostLink" 		=> !empty($YtVideoUrl) ? $YtVideoUrl : '',
					"CreatedTime" 	=> (!empty($snippet['publishedAt'])) ? $this->feed_Post_time($snippet['publishedAt']) : '',
					"PostImage" 	=> !empty($thumbnails) ? $thumbnails : '',
					"UserName" 		=> !empty($snippet['channelTitle']) ? $snippet['channelTitle'] : '',
					"UserImage" 	=> !empty($YtProfile) ? $YtProfile : '',
					"UserLink" 		=> !empty($UserLink) ? $UserLink : '',
					"view" 			=> (isset($view)) ? $this->tp_number_short($view) : 0,
					"likes" 		=> (isset($like)) ? $this->tp_number_short($like) : 0,
					"comment" 		=> (isset($comment)) ? $this->tp_number_short($comment) : 0,
					"Dislike" 		=> (isset($Dislike)) ? $this->tp_number_short($Dislike) : 0,
					"Embed" 		=> "https://www.youtube.com/embed/{$VideoId}",
					"EmbedType"     => 'video',
					"socialIcon" 	=> 'fab fa-youtube social-logo-yt',
					"selectFeed"    => !empty($social['selectFeed']) ? $social['selectFeed'] : '',
					"FilterCategory"=> !empty($social['RCategory']) ? $social['RCategory'] : '',
					"RKey" 			=> "tp-repeater-item-".$social['_id'],
				);
			}
		}else{
			$Error = !empty($YTData['error']) ? $YTData['error'] : [];
			$ErrorData['error']['message'] = !empty($Error['message']) ? $Error['message'] : '';
			$ErrorData['error']['HTTP_CODE'] = !empty($Error['HTTP_CODE']) ? $Error['HTTP_CODE'] : 400;
			$YtArr[] = $this->SF_Error_handler($ErrorData, $YtKey, $YtCategory, $YtselectFeed, $YtIcon);
		}	    
	    return $YtArr;
    }

    protected function tp_api_call($API){
		$settings = $this->get_settings_for_display();
		$CURLOPT_SSL_VERIFYPEER = !empty($settings['CURLOPT_SSL_VERIFYPEER']) ? TRUE : FALSE;		
		$curl = curl_init();
				curl_setopt_array($curl, array(
					CURLOPT_URL => $API,
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_ENCODING => '',
					CURLOPT_MAXREDIRS => 10,
					CURLOPT_TIMEOUT => 0,
					CURLOPT_FOLLOWLOCATION => true,
					CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
					CURLOPT_CUSTOMREQUEST => 'GET',
					CURLOPT_SSL_VERIFYPEER => $CURLOPT_SSL_VERIFYPEER,
				));
		$response = json_decode(curl_exec($curl),true);
		$statuscode = array("HTTP_CODE"=>curl_getinfo($curl, CURLINFO_HTTP_CODE));	
		
		$Final=[];
		if(is_array($statuscode) && is_array($response)){
			$Final = array_merge($statuscode,$response);
		}
		
		curl_close($curl);
		return $Final;
    }
    protected function feed_Post_time($datetime, $full = false) {
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
	protected function tp_number_short( $n, $precision = 1 ) {
	    if ($n < 900) {
	        $n_format = number_format($n, $precision);
	        $suffix = '';
	    } else if ($n < 900000) {
	        $n_format = number_format($n / 1000, $precision);
	        $suffix = 'K';
	    } else if ($n < 900000000) {
	        $n_format = number_format($n / 1000000, $precision);
	        $suffix = 'M';
	    } else if ($n < 900000000000) {
	        $n_format = number_format($n / 1000000000, $precision);
	        $suffix = 'B';
	    } else {
	        $n_format = number_format($n / 1000000000000, $precision);
	        $suffix = 'T';
		}		
	    if ( $precision > 0 ) {
	        $dotzero = '.' . str_repeat( '0', $precision );
	        $n_format = str_replace( $dotzero, '', $n_format );
	    }
	    return $n_format . $suffix;
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
		$autoplay_speed= !empty($settings["autoplay_speed"]["size"]) ? $settings["autoplay_speed"]["size"] : '1500';
		$data_slider .=' data-autoplay_speed="'.esc_attr($autoplay_speed).'"';
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
			$data_slider .=' data-mobile_center_padding="'.(isset($settings["mobile_center_padding"]["size"]) ? esc_attr($settings["mobile_center_padding"]["size"]) : '0').'"';
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
	protected function tp_social_feed_fancybox($settings){
		$FancyData = !empty($settings['FancyOption']) ? $settings['FancyOption'] : [];

		$button = array();
		if (is_array($FancyData)) {
			foreach ($FancyData as $value) {
				$button[] = $value;
			}
		}
		$fancybox = array();
		$fancybox['loop'] = !empty($settings['LoopFancy']) ? true : false;
		$fancybox['infobar'] = !empty($settings['infobar']) ? true : false;
		$fancybox['arrows'] = !empty($settings['ArrowsFancy']) ? true : false;		
		$fancybox['animationEffect'] = $settings['AnimationFancy'];
		$fancybox['animationDuration'] = $settings['DurationFancy']['size'];		
		$fancybox['clickContent'] = $settings['ClickContent'];
		$fancybox['slideclick'] = $settings['Slideclick'];
		$fancybox['transitionEffect'] = $settings['TransitionFancy'];
		$fancybox['transitionDuration'] = $settings['TranDuration']['size'];
		$fancybox['button'] = $button;
	    return $fancybox;
    }
    Protected function get_filter_category($count, $allfeed){
		$settings = $this->get_settings_for_display();	
		$CategoryWF = !empty($settings['filter_category']) ? $settings['filter_category'] : '';	
		$category_filter='';
		$TeamMemberR = !empty($settings['AllReapeter']) ? $settings['AllReapeter'] : [];
		if($CategoryWF=='yes'){
		    $filter_style=$settings["filter_style"];
			$filter_hover_style=$settings["filter_hover_style"];
			$all_filter_category= !empty($settings["all_filter_category"]) ? $settings["all_filter_category"] : esc_html__('All','theplus');

			$loop_category = [];
			foreach ( $TeamMemberR as $TMFilter ) {
				$TMCategory = !empty($TMFilter['RCategory']) ? $TMFilter['RCategory'] : '';
				if(!empty($TMCategory)){
					$loop_category[] = $TMCategory;
				}
			}

			$loop_category = array_unique($loop_category);
			$loop_category = $this->SF_Split_Array_Category($loop_category);
			$count_category = array_count_values($loop_category);

			$all_category=$category_post_count='';
			if($filter_style=='style-1'){
				$all_category='<span class="all_post_count">'.esc_html($count).'</span>';
			}
			if($filter_style=='style-2' || $filter_style=='style-3'){
				$category_post_count='<span class="all_post_count">'.esc_attr($count).'</span>';
			}
		    $category_filter .='<div class="post-filter-data '.esc_attr($filter_style).' text-'.esc_attr($settings['filter_category_align']).'">';
				if($filter_style=='style-4'){
					$category_filter .= '<span class="filters-toggle-link">'.esc_html__('Filters','theplus').'<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 64 64" style="enable-background:new 0 0 64 64;" xml:space="preserve"><g><line x1="0" y1="32" x2="63" y2="32"></line></g><polyline points="50.7,44.6 63.3,32 50.7,19.4 "></polyline><circle cx="32" cy="32" r="31"></circle></svg></span>';
				}
				$category_filter .='<ul class="category-filters '.esc_attr($filter_style).' hover-'.esc_attr($filter_hover_style).'">';
					$category_filter .= '<li><a href="#" class="filter-category-list active all" data-filter="*" >'.$category_post_count.'<span data-hover="'.esc_attr($all_filter_category).'">'.esc_html($all_filter_category).'</span>'.$all_category.'</a></li>';

					foreach ( $loop_category as $i => $key ) {
						$slug = $this->SF_Media_createSlug($key) ;
						$category_post_count = '';
						if($filter_style=='style-2' || $filter_style=='style-3'){
							$CategoryCount=0;
							foreach ($allfeed as $index => $value) {
								$CategoryName = !empty($value['FilterCategory']) ? $value['FilterCategory'] : '';
								if($CategoryName == $key && $index < $count){
									$CategoryCount++;
								}
							}
							$category_post_count='<span class="all_post_count">'.esc_html($CategoryCount).'</span>';
						}
						$category_filter .= '<li>';
							$category_filter .= '<a href="#" class="filter-category-list"  data-filter=".'.esc_attr($slug).'">';
								$category_filter .= $category_post_count;
								$category_filter .= '<span data-hover="'.esc_attr($key).'">';
									$category_filter .= esc_html($key);
								$category_filter .= '</span>';
							$category_filter .= '</a>';
						$category_filter .= '</li>';
					}
				$category_filter .= '</ul>';
			$category_filter .= '</div>';
	    }
		return $category_filter;
	}
	protected function SF_Split_Array_Category($array){
		if (!is_array($array)) { 
		  return FALSE; 
		} 
		$result = array(); 
		foreach ($array as $key => $value) { 
		  if (is_array($value)) { 
			$result = array_merge($result, $this->SF_Split_Array_Category($value)); 
		  } 
		  else { 
			$result[$key] = $value; 
		  }
		}		
		return $result; 
    }
    protected function SF_Media_createSlug($str, $delimiter = '-'){
	  $slug = preg_replace('/[^A-Za-z0-9-]+/', $delimiter, $str);
	  return $slug;
    }

	Protected function SF_Error_handler($ErrorData, $Rkey='', $RCategory='', $selectFeed='', $Icon=''){
		$Error = !empty($ErrorData['error']) ? $ErrorData['error'] : [];
		return array(
					"Feed_Index" 	=> 0,
					"ErrorClass"    => "error-class",
					"socialIcon" 	=> $Icon,
					"CreatedTime" 	=> "<b>{$selectFeed}</b>",
					"Description" 	=> !empty($Error['message']) ? $Error['message'] : 'Somthing Wrong',
					"UserName" 		=> !empty($Error['HTTP_CODE']) ? 'Error Code : '.$Error['HTTP_CODE'] : 400,
					"UserImage" 	=> THEPLUS_ASSETS_URL.'images/tp-placeholder.jpg',
					"selectType"    => $selectFeed,
					"FilterCategory"=> $RCategory,
					"RKey" 			=> "tp-repeater-item-$Rkey",
				);
	}

    protected function content_template() {
		
	}
}