<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @package    Revslider_Sharing_Addon
 * @subpackage Revslider_Sharing_Addon/public
 * @author     ThemePunch <info@themepunch.com>
 */
class Revslider_Sharing_Addon_Public extends RevSliderFunctions{

	/**
	 * The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 */
	private $version;
	
	/**
	 * Initialize the class and set its properties.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 */
	public function enqueue_styles() {
		
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/revslider-sharing-addon-public.js', array( 'jquery' ), $this->version, false );
		wp_localize_script( $this->plugin_name, 'revslider_sharing_addon', array(
				'revslider_sharing_addon_sizes' => unserialize(get_option('revslider_sharing_addon_sizes')),
				'ajax_url' => admin_url( 'admin-ajax.php' )
			)
		);

	}
	
	private function isSocialLink($action) {
		return $action === 'share_facebook' || $action === 'share_twitter' || $action === 'share_linkedin' || $action === 'share_pinterest';
	}
	
	/**
	 * Convert Action tag
	 *
	 * @since    2.0.0
	 */
	public function rs_action_type($action) {
		return $this->isSocialLink($action) ? 'link' : $action;
	}
	
	/**
	 * Convert Action link_type
	 *
	 * @since    2.0.0
	 */
	public function rs_action_link_type($action) {
		return $this->isSocialLink($action) ? 'a' : $action;
	}

	/**
	 * Add Sharing Actions in Output
	 */
	public function rs_action_output_layer_simple_link( $html_simple_link="", $action, $all_actions, $num, $slide="", $slider, $a_events="", $ouput_obj=''){
		
		$caller_url = plugin_dir_url( __FILE__ ).'revslider-sharing-addon-call.php';
		$slide_id = $slide->get_id();
		
		$source = $slider->get_param('sourcetype');
		$url = $source == "post" ? urlencode(get_permalink($slide_id)) : $slide_id;
		
		$actionType = $this->get_val($action, 'action', '');
		$tabs = "\t\t\t\t\t\t\t\t";
		if($ouput_obj !== '') $tabs .= $ouput_obj->ld();
		
		switch ($actionType) {
			case 'share_twitter':
				// Get Values
				$a_sharetext = $this->get_val($action, 'twitter_text', '');
				$a_sharelink = $this->get_val($action, 'twitter_link', '');

				// Attach link to text
				switch ($a_sharelink) {
					case '%site_url%':
						$sharelink = $this->curPageURL();
					break;
					case '%post_url%':
						$sharelink = "tp_revslider_sharing_get_permalink";//get_permalink($slide->get_id());
					break;
					default:
						$sharelink = $a_sharelink;
					break;
				}
				$sharetext = empty($a_sharetext) ? '&url='.$sharelink : $a_sharetext.'&url='.$sharelink;
				$html_simple_link = 'target="_blank" ' . "\n" . $tabs . 'href="'.$caller_url.'?tpurl='.$url.'&share=https://twitter.com/intent/tweet?text='.urlencode(str_replace("'","%27",$sharetext)).'&slider='.$slide->getSliderID().'&source='.$source.'"';
				break;
			case 'share_facebook':
				$a_sharelink = $this->get_val($action, 'facebook_link', '');
				$a_sharelink_custom = $this->get_val($action, 'facebook_link_url', '');

				switch ($a_sharelink) {
					case '%post_url%':
						$sharelink = "tp_revslider_sharing_get_permalink";
						break;
					case '%site_url%':
						$sharelink = $this->curPageURL();
						break;
					default:
						$sharelink = isset($a_sharelink) ? $a_sharelink : "";
						break;
				}
				
				$html_simple_link = 'target="_blank" ' . "\n" . $tabs . 'href="'.$caller_url.'?tpurl='.$url.'&share=https://www.facebook.com/sharer/sharer.php?u='.urlencode($sharelink).'&slider='.$slide->getSliderID().'&source='.$source.'"';
				break;
			case 'share_googleplus':
				$a_sharelink = $this->get_val($action, 'googleplus_link', '');
				$a_sharelink_custom = $this->get_val($action, 'googleplus_link_url', '');

				switch ($a_sharelink) {
					case '%site_url%':
						$sharelink = $this->curPageURL();
						break;
					case '%post_url%':
						$sharelink = "tp_revslider_sharing_get_permalink";
						break;
					default:
						$sharelink = isset($a_sharelink) ? $a_sharelink : "";
						break;
				}
				
				$html_simple_link = 'target="_blank" ' . "\n" . $tabs . 'href="'.$caller_url.'?tpurl='.$url.'&share=https://plus.google.com/share?url='.urlencode($sharelink).'&slider='.$slide->getSliderID().'&source='.$source.'"';
				break;
			case 'share_pinterest':
				$a_sharelink = $this->get_val($action, 'pinterest_link', '');
				$a_sharelink_custom = $this->get_val($action, 'pinterest_link_url', '');
				$shareimage = $this->get_val($action, 'pinterest_image', '');
				$a_shareimage_url = $this->get_val($action, 'pinterest_image_url', '');
				$sharedesc = $this->get_val($action, 'pinterest_link_description', '');

				switch ($a_sharelink) {
					case '%site_url%':
						$sharelink = $this->curPageURL();
						//$sharedesc = $a_sharedesc[$num];
						break;
					case '%post_url%':
						$sharelink = 'tp_revslider_sharing_get_permalink';
						//$sharedesc =  $a_sharedesc[$num];
						break;
					default:
						$sharelink = $a_sharelink;
						//$sharedesc = $a_sharedesc[$num];
						break;
				}
				
				if($shareimage === '%background_image%') {
					$shareimage = $slide->image_url();
				}
				
				$html_simple_link = 'target="_blank" ' . "\n" . $tabs . 'href="'.$caller_url.'?tpurl='.$url.'&share=https://pinterest.com/pin/create/button/?url='.urlencode($sharelink).',media='.urlencode($shareimage).',description='.urlencode(str_replace("'","%27",$sharedesc)).'&slider='.$slide->getSliderID().'&source='.$source.'"';
				break;
			case 'share_linkedin':
				$a_sharelink = $this->get_val($action, 'linkedin_link', '');
				$a_sharelink_custom = $this->get_val($action, 'linkedin_link_url', '');
				$a_share_title = $this->get_val($action, 'linkedin_link_title', '');
				$a_share_summary = $this->get_val($action, 'linkedin_link_summary', '');

				if(isset($a_sharelink)){	
					switch ($a_sharelink) {
						case '%site_url%':
							$sharelink 	= $this->curPageURL();
							break;
						case '%post_url%':
							$sharelink 	= 'tp_revslider_sharing_get_permalink';
							break;
						default:
							$sharelink 	= empty($a_sharelink) ? '' : $a_sharelink;
							break;
					}
					$sharetitle 	= empty($a_share_title) ? '' : $a_share_title;
					$sharesummary 	= empty($a_share_summary) ? '' : $a_share_summary;
				}
				else {
					$sharelink 	= '';
					$sharetitle 	= '';
					$sharesummary 	= '';
				}
				
				$html_simple_link = 'target="_blank" ' . "\n" . $tabs . 'href="'.$caller_url.'?tpurl='.$url.'&share=https://www.linkedin.com/shareArticle?mini=true,url='.urlencode($sharelink).',title='.urlencode($sharetitle).',summary='.urlencode(str_replace("'","%27",$sharesummary)).'&slider='.$slide->getSliderID().'&source='.$source.'"';
				break;
			default:
				break;
		}
		
		return $html_simple_link;
	}

	public function add_sharing_javascript($slider, $id){
		
		$id = $slider->get_id();
		echo 'if(typeof RevSliderSocialSharing !== "undefined") RevSliderSocialSharing(revapi' . $id . ');';
		
	}

	/**
	 * Returns Current URL
	 */
	public function curPageURL() {
		$pageURL = 'http';

		if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {
			$pageURL .= "s";
		}
		$pageURL .= "://";
		
		if ($_SERVER["SERVER_PORT"] != "80") {
			$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
		} 
		else {
			$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
		}

		return esc_url($pageURL);
	}

	/**
	 * Get Post Information
	 */
	public function get_post_info() {
		if( isset($_REQUEST['revslider_sharing_addon_post_id']) ){
			$post_id = urlencode($_REQUEST['revslider_sharing_addon_post_id']);
			$revslider_sharing_addon_link = $_REQUEST['revslider_sharing_addon_link'];
			die($revslider_sharing_addon_link);
		} 
		else {
			die( '-1' );
		}
	}

	public static function mediaid_to_shortcode($mediaid){

	    if(strpos($mediaid, '_') !== false){
	        $pieces = explode('_', $mediaid);
	        $mediaid = $pieces[0];
	        $userid = $pieces[1];
	    }

	    $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-_';
	    $shortcode = '';
	    while($mediaid > 0){
	        $remainder = $mediaid % 64;
	        $mediaid = ($mediaid-$remainder) / 64;
	        $shortcode = $alphabet{$remainder} . $shortcode;
	    };
	    return $shortcode;
	}

	public function get_photo_info($photo_id,$app_id,$app_secret){
	    $oauth = wp_remote_fopen("https://graph.facebook.com/oauth/access_token?type=client_cred&client_id=".$app_id."&client_secret=".$app_secret);
	    $url = "https://graph.facebook.com/$photo_id/?".$oauth."&fields=name,link,created_time,updated_time,from,likes,picture,images";

	    $transient_name = 'revslider_' . md5($url);

	    if ($this->transient_sec > 0 && false !== ($data = get_transient( $transient_name)))
	      return ($data);

	    return $url;
	}

}
