<?php
/**
* @author	ThemePunch <info@themepunch.com>
* @link		https://www.themepunch.com/
* @copyright 2022 ThemePunch
*/


if(!defined('ABSPATH')) exit();


class RevSliderTracking extends RevSliderFunctions {
	
	public $tracking_enabled;
	public $tracking_status;
	private $tracking_url = 'tracking.php';

	public function __construct(){
		$gs = $this->get_global_settings();

		$this->tracking_status = $this->get_val($gs, 'tracking', '1999-01-01');
		$this->tracking_enabled = ($this->tracking_status === 'enabled') ? true : false;

		if($this->is_enabled() === true){
			add_filter('revslider_doing_html_export', array($this, 'count_html_export'), 10, 1);
			add_filter('revslider_exportSlider_export_data', array($this, 'count_regular_exports'), 10, 1);
			add_filter('revslider_retrieve_version_info_addition', array($this, 'add_additional_data'), 10, 1);
			add_filter('revslider_deactivate_plugin_info_addition', array($this, 'add_additional_data'), 10, 1);
			add_filter('revslider_activate_plugin_info_addition', array($this, 'add_additional_data'), 10, 1);
			add_action('revslider-retrieve_version_info', array($this, '_run'), 10);
		}
	}

	public function is_enabled(){
		return $this->tracking_enabled;
	}

	public function get_status(){
		return $this->tracking_status;
	}

	public function get_tracking_data(){
		$data = get_option('rs-tracking-data', array());

		return (!is_array($data)) ? array() : $data;
	}

	public function update_tracking_data($data){
		return update_option('rs-tracking-data', $data);
	}

	public function delete_tracking_data(){
		return delete_option('rs-tracking-data');
	}

	public function count_regular_exports($_data){
		$data = $this->get_tracking_data();
		if(!isset($data['regular_exports'])) $data['regular_exports'] = 0;
		$data['regular_exports']++;

		$this->update_tracking_data($data);

		return $_data;
	}

	public function count_html_export($slider){
		$data = $this->get_tracking_data();
		if(!isset($data['html_exports'])) $data['html_exports'] = 0;
		$data['html_exports']++;

		$this->update_tracking_data($data);

		return $slider;
	}

	public function get_unique_identifier(){
		$uid = get_option('revslider-uid');
		if(strlen($uid) !== 12){
			$uid = substr(md5(mt_rand()), 0, 12);
			update_option('revslider-uid', $uid);
		}

		return $uid;
	}

	/**
	 * this will run the tracking functions and prepare it to be send to the themepunch servers
	 **/
	public function _run($deactivation = 'default'){
		if(!$this->is_enabled()) return false;
		
		$sl			= new RevSliderSlide();
		$data		= $this->get_tracking_data();
		$pages		= $this->get_all_shortcode_pages();
		$shortcodes = array();

		if(!empty($pages)) $shortcodes = $this->get_shortcode_from_page($pages);

		if(!isset($data['html_exports'])) $data['html_exports'] = 0;
		$data['environment'] = array(
			'version'		=> RS_REVISION
		);
		$data['licensed']	= (!in_array($deactivation, array(true, false), true)) ? $this->_truefalse(get_option('revslider-valid', 'true')) : $deactivation; //if $deactivation === false, we are in deactivation process, so set already to false
		$data['slider']		= array(
			'number'		=> 0,
			'premium'		=> 0,
			'import'		=> 0,
			'sources'		=> array(
				'custom'		=> 0,
				'post'			=> 0,
				'woocommerce'	=> 0,
				'social'		=> 0,
				'social_detail'	=> array(),
			),
			'navigations'	=> array(
				'arrows'	=> 0,
				'bullets'	=> 0,
				'tabs'		=> 0,
				'thumbs'	=> 0,
				'mouse'		=> 0,
				'swipe'		=> 0,
				'keyboard'	=> 0
			),
			'parallax'		=> 0,
			'scrolleffects'	=> 0,
			'timeline_scroll'=> 0,
			'color_skins'	=> 0,
		);
		$data['slides']		= array(
			'number'		=> 0,
			'background'	=> array(),
			'kenburns'		=> 0
		);
		$data['layer']		= array(
			'number'	=> 0,
			'types'		=> array(),
			'actions'	=> array(),
			'frames'	=> array(),
			'presets'	=> array(),
			'presets_modified'	=> 0,
			'loop'		=> 0,
			'library'	=> 0,
			'in'		=> array(
				'column'	=> 0,
				'group'		=> 0
			),
		);

		if(!empty($shortcodes)){
			foreach($shortcodes as $alias){
				wp_cache_flush();
				$sldr = new RevSliderSlider();
				$sldr->init_by_alias($alias);
				if($sldr->inited === false) continue;
				$premium	= $sldr->get_param('pakps', false);
				if($data['licensed'] === false && $premium === true) continue; // do not fetch premium data on unlicensed slider

				$data['slider']['number']++;
				$slides		= $sldr->get_slides();
				$static_slide = false;
				$static_id	= $sl->get_static_slide_id($sldr->get_id());
				if($static_id !== false){
					$msl = new RevSliderSlide();
					if(strpos($static_id, 'static_') === false){
						$static_id = 'static_'. $static_id;
					}
					$msl->init_by_id($static_id);
					if($msl->get_id() !== ''){
						$static_slide = $msl;
					}
					$msl = null;
				}

				$wc				= false;
				$post			= $sldr->is_posts();
				$specific_post	= $sldr->is_specific_posts();
				$stream			= $sldr->is_stream();
				$type			= $sldr->get_param('sourcetype', 'gallery');				
				$import			= $sldr->get_param('imported', false);
				if($post){
					if(in_array($type, array('woocommerce', 'woo'))){
						$wc		= true;
						$post	= false;
					}
				}

				if($type === 'gallery')	$data['slider']['sources']['custom']++;
				if($post === true || $specific_post === true)	$data['slider']['sources']['post']++;
				if($stream !== false){
					$data['slider']['sources']['social']++;
					if(!isset($data['slider']['sources']['social_detail'][$stream])) $data['slider']['sources']['social_detail'][$stream] = 0;
					$data['slider']['sources']['social_detail'][$stream]++;
				}
				if($wc === true)		$data['slider']['sources']['woocommerce']++;

				if($premium === true)	$data['slider']['premium']++;
				if($import === true)	$data['slider']['import']++;

				if($sldr->get_param('type', 'standard') !== 'hero'){
					foreach($data['slider']['navigations'] as $n => $count){
						if($sldr->get_param(array('nav', $n, 'set'), false) === true) $data['slider']['navigations'][$n]++;
					}

					if($sldr->get_param(array('nav', 'swipe', 'set'), false) === false){
						if($sldr->get_param(array('nav', 'swipe', 'setOnDesktop'), false) === true) $data['slider']['navigations']['swipe']++;
					}
				}

				if($sldr->get_param(array('parallax', 'set'), false) === true || $sldr->get_param(array('parallax', 'setDDD'), false) === true) $data['slider']['parallax']++;
				if($sldr->get_param(array('scrolleffects', 'set'), false) === true)		$data['slider']['scrolleffects']++;
				if($sldr->get_param(array('scrolltimeline', 'set'), false) === true)	$data['slider']['timeline_scroll']++;
				if($sldr->get_param(array('skins', 'colors'), array()) > 0)				$data['slider']['color_skins']++;

				if(!empty($slides)){
					$data['slides']['number'] += count($slides);
					foreach($slides as $slide){
						//'transparent', 'trans', 'solid'
						//'image'
						//'html5'
						//'streamtwitter', 'streamtwitterboth', 'streaminstagram', 'streaminstagramboth'
						//'streamyoutube', 'streamyoutubeboth', 'youtube', 'streamvimeo', 'streamvimeoboth', 'vimeo'
						$bg_type = $slide->get_param(array('bg', 'type'), 'transparent');
						if(!isset($data['slides']['background'][$bg_type])) $data['slides']['background'][$bg_type] = 0;

						$data['slides']['background'][$bg_type]++;
						
						if($slide->get_param(array('panzoom', 'set'), false) === true) $data['slides']['kenburns']++;

						$layers = $slide->get_layers();
						
						if(!empty($layers) && is_array($layers)){
							$list = array('group' => array(), 'column' => array());

							foreach($layers as $key => $layer){
								$layer_type = $this->get_val($layer, 'type', 'text');
								if($layer_type === 'column')	$list['column'][] = (string)$this->get_val($layer, 'uid');
								if($layer_type === 'group')		$list['group'][] = (string)$this->get_val($layer, 'uid');
							}
							foreach($layers as $key => $layer){
								if(in_array($key, array('top', 'middle', 'bottom'))) continue;
								$layer_type = $this->get_val($layer, 'type', 'text');
								if(in_array($layer_type, array('column', 'row'))) continue;
								
								$puid = (string)$this->get_val($layer, array('group', 'puid'), '-1');
								if($puid !== '-1'){
									if(in_array($puid, $list['column'])) $data['layer']['in']['column']++;
									if(in_array($puid, $list['group'])) $data['layer']['in']['group']++;
								}

								$data['layer']['number']++; //top bottom middle layer

								if(!isset($data['layer']['types'][$layer_type])) $data['layer']['types'][$layer_type] = 0;
								$data['layer']['types'][$layer_type]++;

								$actions	 = $this->get_val($layer, array('actions', 'action'), array());
								
								if(!empty($actions)){
									foreach($actions as $num => $action){
										$act = $this->get_val($action, 'action');

										if(!isset($data['layer']['actions'][$act])) $data['layer']['actions'][$act] = 0;

										$data['layer']['actions'][$act]++;
									}
								}
								
								$frames	 = $this->get_val($layer, array('timeline', 'frames'), false);
								if(!empty($frames)){
									foreach($frames as $fk => $frame){
										if(!isset($data['layer']['frames'][$fk])) $data['layer']['frames'][$fk] = 0;
										$data['layer']['frames'][$fk]++;
										$preset = $this->get_val($frame, array('timeline', 'preset'));
										$presetBased = $this->get_val($frame, array('timeline', 'presetBased'), 1);
										if(!empty($preset) && $presetBased < 1) $data['layer']['presets_modified']++;

										if(in_array($fk, array('frame_0', 'frame_1', 'frame_999'))){
											if(!empty($preset)){
												if(!isset($data['layer']['presets'][$preset])) $data['layer']['presets'][$preset] = 0;

												$data['layer']['presets'][$preset]++;
											}
											continue;
										}
									}
								}

								if($this->get_val($layer, array('layerLibSrc'), false) !== false) $data['layer']['library']++;
								if($this->get_val($layer, array('timeline', 'loop', 'use'), false) === true) $data['layer']['loop']++;
							}
							$layers = null;
							unset($layers);
						}
					}
					$slides = null;
					unset($slides);
				}
				$sldr = null;
				unset($sldr);
			}
		}

		$this->update_tracking_data($data);
	}

	/**
	 * will return all posts/pages that include the [rev_slider] shortcode
	 **/
	public function get_all_shortcode_pages(){
		global $wpdb;
		
		$ids = array();
		$pages = $wpdb->get_results("SELECT ID FROM ".$wpdb->posts." WHERE `post_content` LIKE '%[rev_slider %' AND post_status IN ('publish', 'private', 'draft')");
		if(!empty($pages)){
			foreach($pages as $page){
				$ids[] = $this->get_val($page, 'ID');
			}
		}

		return $ids;
	}

	/**
	 * this will return the exact alias of the rev_slider modules on given posts/pages
	 **/
	public function get_shortcode_from_page($ids){
		$_shortcodes = array();
		$ids		 = (!is_array($ids)) ? (array)$ids : $ids;

		foreach($ids as $id){
			$post = get_post($id);
			if(is_a($post, 'WP_Post') && has_shortcode($post->post_content, 'rev_slider')){
				preg_match_all('/\[rev_slider.*alias=.(.*)"\]/', $post->post_content, $shortcodes);
				
				if(isset($shortcodes[1]) && $shortcodes[1] !== ''){
					foreach($shortcodes[1] as $s){
						if(strpos($s, '"') !== false) $s = $this->get_val(explode('"', $s), 0);
						if(!RevSliderSlider::alias_exists($s)) continue;
						if(!in_array($s, $_shortcodes)) $_shortcodes[] = $s;
					}
				}
			}
		}

		return $_shortcodes;
	}

	public function add_additional_data($addition){
		if(!$this->is_enabled()) return $addition;

		$data = $this->get_tracking_data();
		$addition['tracking'] = array(
			'uid'	=> $this->get_unique_identifier(),
			'data'	=> $data,
		);

		return $addition;
	}
}

?>