<?php
/**
 * External Sources Instagram Class
 * @since: 5.0
 * @author    ThemePunch <info@themepunch.com>
 * @link      https://www.sliderrevolution.com/
 * @copyright 2022 ThemePunch
 */

if(!defined('ABSPATH')) exit();

use EspressoDev\InstagramBasicDisplay as InstagramBasicDisplay;

/**
 * Instagram
 *
 * with help of the API this class delivers all kind of Images from instagram
 *
 * @package    socialstreams
 * @subpackage socialstreams/instagram
 * @author     ThemePunch <info@themepunch.com>
 */

class RevSliderInstagram extends RevSliderFunctions {

	const TRANSIENT_PREFIX = 'revslider_ig_';

	const URL_IG_AUTH = 'https://updates.themepunch.tools/ig/login.php';
	const URL_IG_API = 'https://updates.themepunch.tools/ig/api.php';

	const QUERY_SHOW = 'ig_show';
	const QUERY_TOKEN = 'ig_token';
	const QUERY_CONNECTWITH = 'ig_user';
	const QUERY_ERROR = 'ig_error_message';

	/**
	 * Stream Array
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      array    $stream    Stream Data Array
	 */
	private $stream;

	/**
	 * Transient seconds
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var int  $transient_sec Transient time in seconds
	 */
	private $transient_sec;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since 1.0.0
	 * @param int $transient_sec  Transient time in seconds
	 */
	public function __construct($transient_sec = 86400){
		$this->transient_sec = $transient_sec;
	}

	/**
	 * @return int
	 */
	public function getTransientSec(){
		return $this->transient_sec;
	}

	/**
	 * @param int $transient_sec
	 */
	public function setTransientSec($transient_sec){
		$this->transient_sec = $transient_sec;
	}

	public function add_actions(){
		add_action('init', array(&$this, 'do_init'), 5);
		add_action('admin_footer', array(&$this, 'footer_js'));
		add_action('revslider_slider_on_delete_slider', array(&$this, 'on_delete_slider'), 10, 1);
	}

	/**
	 * check if we have QUERY_ARG set
	 * try to login the user
	 */
	public function do_init(){
		// are we on revslider page?
		if($this->get_val($_GET, 'page') != 'revslider') return;

		//instagram returned error
		if(isset($_GET[self::QUERY_ERROR])) return;

		//we need token and slide ID to proceed with saving token
		if(!isset($_GET[self::QUERY_TOKEN]) || !isset($_GET['id'])) return;

		$token		 = $_GET[self::QUERY_TOKEN];
		$connectwith = $_GET[self::QUERY_CONNECTWITH];
		$id			 = $this->get_val($_GET, 'id');

		$slider	= new RevSliderSlider();
		$slide	= new RevSliderSlide();

		$slide->init_by_id($id);

		$slider_id = $slide->get_slider_id();
		if(intval($slider_id) == 0){
			$_GET[self::QUERY_ERROR] = __('Slider could not be loaded', 'revslider');
			return;
		}

		$slider->init_by_id($slider_id);
		if($slider->inited === false){
			$_GET[self::QUERY_ERROR] = __('Slider could not be loaded', 'revslider');
			return;
		}

		$slider->set_param(array('source', 'instagram', 'token_source'), 'account');
		$slider->set_param(array('source', 'instagram', 'token'), $token);
		$slider->set_param(array('source', 'instagram', 'connect_with'), $connectwith);
		$slider->update_params(array());

		//redirect
		$url = set_url_scheme( 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] );
		$url = add_query_arg(array(self::QUERY_TOKEN => false, self::QUERY_SHOW => 1), $url);
		wp_redirect($url);
		exit();
	}

	public function footer_js(){
		// are we on revslider page?
		if($this->get_val($_GET, 'page') != 'revslider') return;

		if(isset($_GET[self::QUERY_SHOW]) || isset($_GET[self::QUERY_ERROR])){
			echo '<script>jQuery(document).ready(function(){ RVS.DOC.one("builderInitialised", function(){RVS.F.mainMode({mode:"sliderlayout", forms:["*sliderlayout*#form_slidercontent"], set:true, uncollapse:true,slide:RVS.S.slideId});RVS.F.updateSliderObj({path:"settings.sourcetype",val:"instagram"});RVS.F.updateEasyInputs({container:jQuery("#form_slidercontent"), trigger:"init", visualUpdate:true});}); });</script>';
		}

		if(isset($_GET[self::QUERY_ERROR])){
			$err = __('Instagram Reports: ', 'revslider') . esc_html($_GET[self::QUERY_ERROR]);
			echo '<script>jQuery(document).ready(function(){ RVS.DOC.one("builderInitialised", function(){ RVS.F.showInfo({content:"' . $err . '", type:"warning", showdelay:1, hidedelay:5, hideon:"", event:"" }); });});</script>';
		}
	}

	public static function get_login_url(){
		$id			= (isset($_GET['id'])) ? $_GET['id'] : '';
		$state		= base64_encode(admin_url('admin.php?page=revslider&view=slide&id='.$id));
		return self::URL_IG_AUTH . '?state=' . $state;
	}
	
	/**
	 * get grid transient name
	 *
	 * @param int $grid_id grid id
	 * @param string $token
	 * @param int $count
	 */
	public function getTransientName($id, $token, $count)
	{
		return self::TRANSIENT_PREFIX . $id . '_' . md5(json_encode($token . '_' . $count));
	}

	/**
	 * Get Instagram User Profile
	 *
	 * @param string $token Instagram Access Token
	 * @return mixed
	 */
	public function get_user_profile($token){

		$transient_name = 'revslider_'. md5('instagram-profile-' . $token);
		if($this->transient_sec > 0 && false !== ($data = get_transient($transient_name))){
			return $data;
		} else {
			delete_transient($transient_name);
		}

		$profile = $this->_callAPI(array(
			'action' => 'profile',
			'token' => $token,
		));
		if(isset($profile['data'])){
			$profile = $profile['data'];
			set_transient($transient_name, $profile, $this->transient_sec);
			return $profile;
		}
		return null;
	}


	/**
	 * Get Instagram User Pictures
	 *
	 * @since 3.0
	 * @param int $slider_id slider ID
	 * @param string $token Instagram Access Token
	 * @param string $count media count
	 * @param string $orig_image
	 * @return mixed
	 */
	public function get_public_photos($slider_id, $token, $count, $orig_image = ''){

		$transient_name = $this->getTransientName($slider_id, $token, $count);
		if($this->transient_sec > 0 && false !== ($data = get_transient($transient_name))){
			$this->stream = $data;
			return $this->stream;
		} else {
			delete_transient($transient_name);
		}

		//Getting instagram images
		$medias = $this->_callAPI(array(
			'action' => 'public_photos',
			'token' => $token,
			'count' => $count,
		));
		if(isset($medias['data']['data'])){
			$this->instagram_output_array($medias['data']['data'], $count);
		}
		if(!empty($this->stream)){
			set_transient($transient_name, $this->stream, $this->transient_sec);
			return $this->stream;
		}else{
			$err = translate('Instagram reports: Please check the settings','revslider');
			if(isset($medias['error'])){
				$err = $medias['message'];
			}
			echo $err;
			return false;
		}
	}

	/**
	 * @param array $args
	 * @return array
	 */
	protected function _callAPI($args = [])
	{
		$rslb = RevSliderGlobals::instance()->get('RevSliderLoadBalancer');
		$request = $rslb->call_url(self::URL_IG_API, $args);
		if (is_wp_error($request)) {
			return array(
				'error' => true,
				'message' => 'Instagram API error: ' . $request->get_error_message(),
			);
		}

		$responseData = json_decode(wp_remote_retrieve_body($request), true);
		if (empty($responseData)) {
			return array(
				'error' => true,
				'message' => 'Instagram API error: Empty response body or wrong data format',
			);
		}

		return $responseData;
	}

	function input($name, $default = null){
		return isset($_REQUEST[$name]) ? $_REQUEST[$name] : $default;
	}

	/**
	 * Prepare output array $stream
	 *
	 * @since    3.0
	 * @param    array $photos Instagram Output Data
	 * @param    int $count resulting number of items
	 */
	private function instagram_output_array($photos, $count){
		$this->stream = array();

		foreach ($photos as $photo){
			if($count > 0){
				$count--;
				$shortcode = '';

				preg_match('/.+\/p\/(.+)?\//m', $photo['permalink'], $matches);
				if(isset($matches[1])){
					$shortcode = $matches[1];
				}
				$photo['display_url'] = isset($photo['media_url']) ? $photo['media_url'] : '';
				if($photo['media_type'] == 'VIDEO'){
					$photo['display_url'] = isset($photo['thumbnail_url']) ? $photo['thumbnail_url'] : '';
					$photo['thumbnail_src'] = $photo['display_url'];
					$photo['videos']['standard_resolution']['url'] = isset($photo['media_url']) ? $photo['media_url'] : '';
				}
				$photo['link'] = isset($photo['permalink']) ? $photo['permalink'] : '';
				$photo['shortcode'] = $shortcode;
				$photo['taken_at_timestamp'] = isset($photo['timestamp']) ? $photo['timestamp'] : '';
				$photo['edge_media_to_caption']['edges'][0]['node']['text'] = isset($photo['caption']) ? $photo['caption'] : '';
				$this->stream[] = $photo;
			}
		}

		return $count;
	}

	/**
	 * delete slider ig transients upon deletion
	 *
	 * @param	$id		slider id
	 * @return	void
	 */
	public function on_delete_slider($id)
	{
		global $wpdb;

		if (empty($id)) return;

		$prefix = self::TRANSIENT_PREFIX . $id;
		$wpdb->query($wpdb->prepare("DELETE FROM $wpdb->options WHERE `option_name` LIKE '%s'", '%'.$prefix.'%'));
	}

}	// End Class
