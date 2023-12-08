<?php
use Elementor\Controls_Manager;
use Elementor\Widget_Base;
use Elementor\Utils;
use Elementor\Plugin;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
class Theplus_Event_Tracker extends Elementor\Widget_Base {
	
	public $TpDoc = THEPLUS_TPDOC;

	public function __construct() {
		$theplus_options=get_option('theplus_options');
		$plus_extras=theplus_get_option('general','extras_elements');		
		
		if((isset($plus_extras) && empty($plus_extras) && empty($theplus_options)) || (!empty($plus_extras) && in_array('plus_event_tracker',$plus_extras))){
			add_action( 'elementor_pro/element/common/section_custom_css/after_section_end', [ $this, 'tp_event_controls' ], 10, 2 );			
			add_action( 'elementor/element/section/section_custom_css/after_section_end', [ $this, 'tp_event_controls' ], 10, 2 );					
			add_action( 'elementor/element/common/section_custom_css_pro/after_section_end', [ $this, 'tp_event_controls' ], 10, 2 );
			
			$experiments_manager = Plugin::$instance->experiments;		
			if($experiments_manager->is_feature_active( 'container' )){		
				add_action( 'elementor/element/container/section_layout/after_section_end', [ $this, 'tp_event_controls' ], 10, 2  );
			}

			add_action( 'elementor/frontend/section/before_render', [ $this, 'plus_before_render'], 10, 1 );			
			add_action( 'elementor/frontend/widget/before_render', [ $this, 'plus_before_render' ], 10, 1 );

			if($experiments_manager->is_feature_active( 'container' )){
				add_action("elementor/frontend/container/before_render", [$this, 'plus_before_render'], 10, 1);
				add_action("elementor/frontend/container/after_render", [$this, 'plus_before_render'], 10, 1);
			}
			
			//add_action( 'elementor/frontend/before_enqueue_scripts', [ $this, 'tp_enqueue_scripts' ], 10 );
						
			add_action( 'wp_head', [ $this, 'add_event_tracker_code_to_header' ] );
			
		}		
	}
	
	public function get_name() {
		return 'plus-event-tracker';
	}

	public function tp_event_controls($element) {		
		$element->start_controls_section(
			'plus_event_tracker_options_sections',
			[
				'label' => esc_html__( 'Plus Extras : Events Tracker', 'theplus' ),
				'tab'   => Controls_Manager::TAB_ADVANCED,
			]
		);
		$element->add_control('plus_eto_fb',
			[
				'label' => wp_kses_post( "Facebook Pixel <a class='tp-docs-link' href='" . esc_url($this->TpDoc) . "track-custom-facebook-events-with-elementor?utm_source=wpbackend&utm_medium=elementoreditor&utm_campaign=widget' target='_blank' rel='noopener noreferrer'> <i class='eicon-help-o'></i> </a>", 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'return_value'	=> 'yes',
				'default' => 'no',
			]
		);
		
		$element->add_control(
			'plus_eto_fb_e_name',
			[
				'label'    => esc_html__('Event Type', 'theplus'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'AddPaymentInfo' => esc_html__('AddPaymentInfo', 'theplus'),
					'AddToCart' => esc_html__('AddToCart', 'theplus'),
					'AddToWishlist' => esc_html__('AddToWishlist', 'theplus'),
					'CompleteRegistration' => esc_html__('CompleteRegistration', 'theplus'),
					'Contact' => esc_html__('Contact', 'theplus'),
					'CustomizeProduct' => esc_html__('CustomizeProduct', 'theplus'),
					'Donate' => esc_html__('Donate', 'theplus'),
					'FindLocation' => esc_html__('FindLocation', 'theplus'),
					'InitiateCheckout' => esc_html__('InitiateCheckout', 'theplus'),
					'Lead' => esc_html__('Lead', 'theplus'),
					'Purchase' => esc_html__('Purchase', 'theplus'),
					'Schedule' => esc_html__('Schedule', 'theplus'),
					'Search' => esc_html__('Search', 'theplus'),
					'StartTrial' => esc_html__('StartTrial', 'theplus'),
					'SubmitApplication' => esc_html__('SubmitApplication', 'theplus'),
					'Subscribe' => esc_html__('Subscribe', 'theplus'),
					'ViewContent' => esc_html__('ViewContent', 'theplus'),
					'Custom' => esc_html__('Custom', 'theplus'),
				],
				'default' => 'ViewContent',
				'condition' => [
					'plus_eto_fb' => 'yes',
				],				
			]
		);		
		$element->add_control(
			'plus_eto_fb_e_name_custom',
			[
				'label'    => esc_html__('Event Value', 'theplus'),
				'type'     => Elementor\Controls_Manager::TEXT,
				'show_label' => true,
				'placeholder' => esc_html__('i.e Affiliate', 'theplus'),
				'condition' => [
					'plus_eto_fb' => 'yes',
					'plus_eto_fb_e_name' => 'Custom',
				],				
			]
		);
		/**Google Start*/
		$element->add_control(
			'plus_eto_gtag',
			[
				'label' => wp_kses_post( "Google Analytics 4 <a class='tp-docs-link' href='" . esc_url($this->TpDoc) . "track-custom-google-analytics-events-with-elementor/?utm_source=wpbackend&utm_medium=elementoreditor&utm_campaign=widget' target='_blank' rel='noopener noreferrer'> <i class='eicon-help-o'></i> </a>", 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'return_value'	=> 'yes',
				'default' => '',
			]
		);
		$element->add_control('eventType',
			[
				'label' => esc_html__('Event Type', 'theplus'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'recommended' => esc_html__('Recommended', 'theplus'),
					'custom' => esc_html__('Custom', 'theplus'),
				],
				'default' => 'recommended',
				'condition' => [
					'plus_eto_gtag' => 'yes',
				],				
			]
		);
		$element->add_control('gtagName',
			[
				'label' => esc_html__('Event Name', 'theplus'),
				'type' => Elementor\Controls_Manager::TEXT,
				'show_label' => true,
				'placeholder' => esc_html__('i.e Sports', 'theplus'),
				'condition' => array(
					'plus_eto_gtag' => 'yes',
					'eventType' => 'custom',
				),
			]
		);
		$element->add_control('defaultEvent',
			[
				'label' => esc_html__('Select Event', 'theplus'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'ad_impression' => esc_html__('Ad Impression', 'theplus'),
					'add_payment_info' => esc_html__('Add Payment Info', 'theplus'),
					'add_shipping_info' => esc_html__('Add Shipping Info', 'theplus'),
					'add_to_cart' => esc_html__('Add to Cart', 'theplus'),
					'add_to_wishlist' => esc_html__('Add to Wishlist', 'theplus'),
					'begin_checkout' => esc_html__('Begin Checkout', 'theplus'),
					'earn_virtual_currency' => esc_html__('Earn Virtual Currency', 'theplus'),
					'generate_lead' => esc_html__('Generate Lead', 'theplus'),
					'join_group' => esc_html__('Join Group', 'theplus'),
					'level_end' => esc_html__('Level End', 'theplus'),
					'level_start' => esc_html__('Level Start', 'theplus'),
					'level_up' => esc_html__('Level Up', 'theplus'),
					'login' => esc_html__('Login', 'theplus'),
					'post_score' => esc_html__('Post Score', 'theplus'),
					'purchase' => esc_html__('Purchase', 'theplus'),
					'refund' => esc_html__('Refund', 'theplus'),
					'remove_from_cart' => esc_html__('Remove from Cart', 'theplus'),
					'search' => esc_html__('Search', 'theplus'),
					'select_content' => esc_html__('Select Content', 'theplus'),
					'select_item' => esc_html__('Select Item', 'theplus'),
					'select_promotion' => esc_html__('Select Promotion', 'theplus'),
					'share' => esc_html__('Share', 'theplus'),
					'sign_up' => esc_html__('Sign Up', 'theplus'),
					'spend_virtual_currency' => esc_html__('Spend Virtual Currency', 'theplus'),
					'tutorial_begin' => esc_html__('Tutorial Begin', 'theplus'),
					'tutorial_complete' => esc_html__('Tutorial Complete', 'theplus'),
					'unlock_achievement' => esc_html__('Unlock Achievement', 'theplus'),
					'view_cart' => esc_html__('View Cart', 'theplus'),
					'view_item' => esc_html__('View Item', 'theplus'),
					'view_item_list' => esc_html__('View Item List', 'theplus'),
					'view_promotion' => esc_html__('View Promotion', 'theplus'),
				],
				'default' => 'ad_impression',
				'condition' => [
					'plus_eto_gtag' => 'yes',
					'eventType' => 'recommended',
				],				
			]
		);
		$repeater = new \Elementor\Repeater();

		$repeater->add_control('eventName',
			[
				'label' => esc_html__( 'Name', 'theplus' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'label_block' => true,
			]
		);

		$repeater->add_control('eventValue',
			[
				'label' => esc_html__( 'Value', 'theplus' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'label_block' => true,
			]
		);

		$element->add_control('eventRepeater',
			[
				'label' => esc_html__( 'Event Properties', 'theplus' ),
				'type' => \Elementor\Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => [
					[
						'eventName' => esc_html__( '', 'theplus' ),
						'eventValue' => esc_html__( '', 'theplus' ),
					],
				],
				'condition' => [
					'plus_eto_gtag' => 'yes',
				],
			]
		);
		
		$element->end_controls_section();
	}
	
	public function plus_before_render($element) {		
		$settings = $element->get_settings();
		
		$data_attr = array();
		$tracker = false;
		$GoogleAnalytics = !empty($settings['plus_eto_gtag']) ? $settings['plus_eto_gtag'] : '';
		$get_extra_options = get_option( 'theplus_api_connection_data' );
		$google_analytics_id = (!empty($get_extra_options['theplus_google_analytics_id'])) ? $get_extra_options['theplus_google_analytics_id'] : '';

		/**facebook event*/
		if (isset($settings['plus_eto_fb']) && $settings['plus_eto_fb'] =='yes') {
			$tracker = true;
			$data_attr['plus-track-fb-event'] = true;			
			$data_attr['plus-fb-event'] = $settings['plus_eto_fb_e_name'];
			if((!empty($settings['plus_eto_fb_e_name']) && $settings['plus_eto_fb_e_name'] =='Custom') && (!empty($settings['plus_eto_fb_e_name_custom']))){
				$data_attr['plus-fb-event-custom'] = $settings['plus_eto_fb_e_name_custom'];
			}			
		}

		/**GA4 */
		if( !empty($GoogleAnalytics) ){
			$tracker = true;
			$data_attr['google'] = $GoogleAnalytics;
			$data_attr['gCsmEventName'] = $settings['gtagName'];
			$data_attr['eventProperties'] = $settings['eventRepeater'];
			$data_attr['gglEventType'] = $settings['eventType'];
			$data_attr['gglSelEvent'] = $settings['defaultEvent'];
			$data_attr['google_analytics_id'] = $google_analytics_id;
		}

		//check and add class in elementor-element
		if (isset($tracker) && $tracker == 'true') {
			$element->add_render_attribute( '_wrapper', array( 'class' => 'theplus-event-tracker', 'data-theplus-event-tracker' => json_encode( $data_attr ) ) );
		}
	}
	
	public function tp_enqueue_scripts() {
		wp_enqueue_script('plus-event-tracker',THEPLUS_ASSETS_URL . 'js/main/event-tracker/plus-event-tracker.min.js',array( 'jquery' ),'',true);	
	}	
	
	public function add_event_tracker_code_to_header() {
		
		$get_extra_options= get_option( 'theplus_api_connection_data' );
		$facebook_pixel_id = (!empty($get_extra_options['theplus_facebook_pixel_id'])) ? $get_extra_options['theplus_facebook_pixel_id'] : '';
		// $google_analytics_id = (!empty($get_extra_options['theplus_google_analytics_id'])) ? $get_extra_options['theplus_google_analytics_id'] : '';
		
		if(!empty($facebook_pixel_id)){
			?>
			<!-- Facebook Pixel Code -->
			<script>
			  !function(f,b,e,v,n,t,s)
			  {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
			  n.callMethod.apply(n,arguments):n.queue.push(arguments)};
			  if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
			  n.queue=[];t=b.createElement(e);t.async=!0;
			  t.src=v;s=b.getElementsByTagName(e)[0];
			  s.parentNode.insertBefore(t,s)}(window, document,'script',
			  'https://connect.facebook.net/en_US/fbevents.js');
			  fbq('init', <?php echo esc_attr($facebook_pixel_id); ?> );
			  fbq('track', 'PageView');
			</script>
			<noscript>
			  <img height="1" width="1" style="display:none" 
				   src="https://www.facebook.com/tr?id=<?php echo $facebook_pixel_id;?>&ev=PageView&noscript=1"/>
			</noscript>
			<!-- End Facebook Pixel Code -->
			<?php
		}
	}
}