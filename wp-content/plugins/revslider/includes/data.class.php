<?php
/**
 * @author    ThemePunch <info@themepunch.com>
 * @link      https://www.themepunch.com/
 * @copyright 2019 ThemePunch
 */

if(!defined('ABSPATH')) exit();

define('RS_T', '	');
define('RS_T2', '		');
define('RS_T3', '			');
define('RS_T4', '				');
define('RS_T5', '					');
define('RS_T6', '						');
define('RS_T7', '							');
define('RS_T8', '								');
define('RS_T9', '									');
define('RS_T10', '										');
define('RS_T11', '											');

class RevSliderData {
	public $css;
	public $animations;

	/**
	 * get all font family types
	 * before: RevSliderOperations::getArrFontFamilys()
	 */
	public function get_font_familys(){
		$fonts = array();
		
		//add custom added fonts
		$gs = $this->get_global_settings();
		$cfl = $this->get_val($gs, 'customFontList', array());
		
		if(!empty($cfl) && is_array($cfl)){
			foreach($cfl as $_cfl){
				$fonts[] = array(
					'type'		=> 'custom',
					'version'	=> __('Custom Fonts', 'revslider'),
					'url'		=> $this->get_val($_cfl, 'url'),
					'frontend'	=> $this->_truefalse($this->get_val($_cfl, 'frontend', false)),
					'backend'	=> $this->_truefalse($this->get_val($_cfl, 'backend', true)),
					'label'		=> $this->get_val($_cfl, 'family'),
					'variants'	=> explode(',', $this->get_val($_cfl, 'weights')),
				);
			}
		}
		
		//Web Safe Fonts
		// GOOGLE Loaded Fonts
		$fonts[] = array('type' => 'websafe', 'version' => __('Loaded Google Fonts', 'revslider'), 'label' => 'Dont Show Me');

		//Serif Fonts
		$fonts[] = array('type' => 'websafe', 'version' => __('Serif Fonts', 'revslider'), 'label' => 'Georgia, serif');
		$fonts[] = array('type' => 'websafe', 'version' => __('Serif Fonts', 'revslider'), 'label' => '"Palatino Linotype", "Book Antiqua", Palatino, serif');
		$fonts[] = array('type' => 'websafe', 'version' => __('Serif Fonts', 'revslider'), 'label' => '"Times New Roman", Times, serif');

		//Sans-Serif Fonts
		$fonts[] = array('type' => 'websafe', 'version' => __('Sans-Serif Fonts', 'revslider'), 'label' => 'Arial, Helvetica, sans-serif');
		$fonts[] = array('type' => 'websafe', 'version' => __('Sans-Serif Fonts', 'revslider'), 'label' => '"Arial Black", Gadget, sans-serif');
		$fonts[] = array('type' => 'websafe', 'version' => __('Sans-Serif Fonts', 'revslider'), 'label' => '"Comic Sans MS", cursive, sans-serif');
		$fonts[] = array('type' => 'websafe', 'version' => __('Sans-Serif Fonts', 'revslider'), 'label' => 'Impact, Charcoal, sans-serif');
		$fonts[] = array('type' => 'websafe', 'version' => __('Sans-Serif Fonts', 'revslider'), 'label' => '"Lucida Sans Unicode", "Lucida Grande", sans-serif');
		$fonts[] = array('type' => 'websafe', 'version' => __('Sans-Serif Fonts', 'revslider'), 'label' => 'Tahoma, Geneva, sans-serif');
		$fonts[] = array('type' => 'websafe', 'version' => __('Sans-Serif Fonts', 'revslider'), 'label' => '"Trebuchet MS", Helvetica, sans-serif');
		$fonts[] = array('type' => 'websafe', 'version' => __('Sans-Serif Fonts', 'revslider'), 'label' => 'Verdana, Geneva, sans-serif');

		//Monospace Fonts
		$fonts[] = array('type' => 'websafe', 'version' => __('Monospace Fonts', 'revslider'), 'label' => '"Courier New", Courier, monospace');
		$fonts[] = array('type' => 'websafe', 'version' => __('Monospace Fonts', 'revslider'), 'label' => '"Lucida Console", Monaco, monospace');
		
		
		//push all variants to the websafe fonts
		foreach($fonts as $f => $font){
			if(!empty($cfl) && is_array($cfl) && $font['type'] === 'custom') continue; //already manually added before on these
			
			$font[$f]['variants'] = array('100', '100italic', '200', '200italic', '300', '300italic', '400', '400italic', '500', '500italic', '600', '600italic', '700', '700italic', '800', '800italic', '900', '900italic');
		}

		include(RS_PLUGIN_PATH . 'includes/googlefonts.php');

		foreach($googlefonts as $f => $val){
			$fonts[] = array('type' => 'googlefont', 'version' => __('Google Fonts', 'revslider'), 'label' => $f, 'variants' => $val['variants'], 'subsets' => $val['subsets'], 'category' => $val['category']);
		}
		
		return apply_filters('revslider_data_get_font_familys', apply_filters('revslider_operations_getArrFontFamilys', $fonts));
	}

	/**
	 * get animations array
	 * @before: RevSliderOperations::getArrAnimations();
	 */
	public function get_animations(){
		return $this->get_custom_animations_full_pre('in');
	}

	/**
	 * get "end" animations array
	 * @before: RevSliderOperations::getArrEndAnimations();
	 */
	public function get_end_animations(){
		return $this->get_custom_animations_full_pre('out');
	}

	public function get_loop_animations(){
		return $this->get_custom_animations_full_pre('loop');
	}
	
	/**
	 * get the version 5 animations only, if available
	 **/
	public function get_animations_v5(){
		$custom = array();
		$temp = array();
		$sort = array();

		$this->fill_animations();

		foreach($this->animations as $value){
			$type = $this->get_val($value, array('params', 'type'), '');
			if(!in_array($type, array('customout', 'customin'))) continue;
			
			$settings = $this->get_val($value, 'settings', '');
			$type = $this->get_val($value, 'type', '');
			if($type == '' && $settings == '' || $type == $pre){
				$temp[$value['id']] = $value;
				$temp[$value['id']]['id'] = $value['id'];
				$sort[$value['id']] = $value['handle'];
			}

			if($settings == 'in' && $pre == 'in' || $settings == 'out' && $pre == 'out' || $settings == 'loop' && $pre == 'loop'){
				$temp[$value['id']] = $value['params'];
				$temp[$value['id']]['settings'] = $settings;
				$temp[$value['id']]['id'] = $value['id'];
				$sort[$value['id']] = $value['handle'];
			}
		}
		if(!empty($sort)){
			asort($sort);
			foreach ($sort as $k => $v){
				$custom[$k] = $temp[$k];
			}
		}
		
		return $custom;
	}
	
	/**
	 * get custom animations
	 * @before: RevSliderOperations::getCustomAnimationsFullPre()
	 */
	public function get_custom_animations_full_pre($pre = 'in'){
		$custom = array();
		$temp = array();
		$sort = array();

		$this->fill_animations();

		foreach($this->animations as $value){
			$settings = $this->get_val($value, 'settings', '');
			$type = $this->get_val($value, 'type', '');
			if($type == '' && $settings == '' || $type == $pre){
				$temp[$value['id']] = $value;
				$temp[$value['id']]['id'] = $value['id'];
				$sort[$value['id']] = $value['handle'];
			}

			if($settings == 'in' && $pre == 'in' || $settings == 'out' && $pre == 'out' || $settings == 'loop' && $pre == 'loop'){
				$temp[$value['id']] = $value['params'];
				$temp[$value['id']]['settings'] = $settings;
				$temp[$value['id']]['id'] = $value['id'];
				$sort[$value['id']] = $value['handle'];
			}
		}
		if(!empty($sort)){
			asort($sort);
			foreach($sort as $k => $v){
				$custom[$k] = $temp[$k];
			}
		}
		
		return $custom;
	}

	/**
	 * Fetch all Custom Animations only one time
	 * @since: 5.2.4
	 * @before: RevSliderOperations::fillAnimations();
	 **/
	public function fill_animations(){
		if(empty($this->animations)){
			global $wpdb;

			$result = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . RevSliderFront::TABLE_LAYER_ANIMATIONS, ARRAY_A);
			$this->animations = (!empty($result)) ? $result : array();
			
			if(!empty($this->animations)){
				foreach($this->animations as $ak => $av){
					$this->animations[$ak]['params'] = json_decode(str_replace("'", '"', $av['params']), true);
				}
			}
			
			if(!empty($this->animations)){
				array_walk_recursive($this->animations, array('RevSliderData', 'force_to_boolean'));
			}
		}
	}
	
	/**
	 * make sure that all false and true are really boolean
	 **/
	public static function force_to_boolean(&$a, &$b){
		$a = ($a === 'false') ? false : $a;
		$a = ($a === 'true') ? true : $a;
		$b = ($b === 'false') ? false : $b;
		$b = ($b === 'true') ? true : $b;
	}

	/**
	 * get contents of the css table as an array
	 * before: RevSliderOperations::getCaptionsContentArray();
	 */
	public function get_captions_array($handle = false){
		$css = new RevSliderCssParser();
		if(empty($this->css)){
			$this->fill_css();
		}

		return $css->db_array_to_array($this->css, $handle);
	}

	/**
	 * Fetch all Custom CSS only one time
	 * @since: 5.2.4
	 * before: RevSliderOperations::fillCSS();
	 **/
	public function fill_css(){
		if(empty($this->css)){
			global $wpdb;

			$css_data = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . RevSliderFront::TABLE_CSS, ARRAY_A);
			$this->css = (!empty($css_data)) ? $css_data : array();
		}
	}

	/**
	 * Get all images sizes + custom added sizes
	 * @before: RevSliderBase::get_all_image_sizes($type);
	 */
	public function get_all_image_sizes($type = 'gallery'){
		$custom_sizes = array();

		switch($type){
			case 'flickr':
				$custom_sizes = array(
					'original' => __('Original', 'revslider'),
					'large' => __('Large', 'revslider'),
					'large-square' => __('Large Square', 'revslider'),
					'medium' => __('Medium', 'revslider'),
					'medium-800' => __('Medium 800', 'revslider'),
					'medium-640' => __('Medium 640', 'revslider'),
					'small' => __('Small', 'revslider'),
					'small-320' => __('Small 320', 'revslider'),
					'thumbnail' => __('Thumbnail', 'revslider'),
					'square' => __('Square', 'revslider'),
				);
			break;
			case 'instagram':
				$custom_sizes = array(
					'standard_resolution' => __('Standard Resolution', 'revslider'),
					'thumbnail' => __('Thumbnail', 'revslider'),
					'low_resolution' => __('Low Resolution', 'revslider'),
					'original_size' => __('Original Size', 'revslider'),
					'large' => __('Large Size', 'revslider'),
				);
			break;
			case 'twitter':
				$custom_sizes = array(
					'large' => __('Standard Resolution', 'revslider'),
				);
			break;
			case 'facebook':
				$custom_sizes = array(
					'full' => __('Original Size', 'revslider'),
					'thumbnail' => __('Thumbnail', 'revslider'),
				);
			break;
			case 'youtube':
				$custom_sizes = array(
					'high' => __('High', 'revslider'),
					'medium' => __('Medium', 'revslider'),
					'default' => __('Default', 'revslider'),
					'standard' => __('Standard', 'revslider'),
					'maxres' => __('Max. Res.', 'revslider'),
				);
			break;
			case 'vimeo':
				$custom_sizes = array(
					'thumbnail_large' => __('Large', 'revslider'),
					'thumbnail_medium' => __('Medium', 'revslider'),
					'thumbnail_small' => __('Small', 'revslider'),
				);
			break;
			case 'gallery':
			default:
				$added_image_sizes = get_intermediate_image_sizes();
				if(!empty($added_image_sizes) && is_array($added_image_sizes)){
					foreach($added_image_sizes as $key => $img_size_handle){
						$custom_sizes[$img_size_handle] = ucwords(str_replace('_', ' ', $img_size_handle));
					}
				}
				$img_orig_sources = array(
					'full' => __('Original Size', 'revslider'),
					'thumbnail' => __('Thumbnail', 'revslider'),
					'medium' => __('Medium', 'revslider'),
					'large' => __('Large', 'revslider'),
				);
				$custom_sizes = array_merge($img_orig_sources, $custom_sizes);
			break;
		}

		return $custom_sizes;
	}

	/**
	 * get the default layer animations
	 **/
	public function get_layer_animations($raw = false){
		$custom_in = $this->get_animations();
		$custom_out = $this->get_end_animations();
		$custom_loop = $this->get_loop_animations();

		$in = '{
			"custom":{"group":"Custom","custom":true,"transitions":' .
		json_encode($custom_in)
			. '},
			"blck":{
				"group":"Block Transitions (SFX)",
				"transitions":{
					"blockfromleft":{"name":"Block from Left","frame_0":{"transform":{"opacity":0}},"frame_1":{"transform":{"opacity":1},"sfx":{"effect":"blocktoright","color":"#ffffff"},"timeline":{"ease":"power4.inOut","speed":1200}}},
					"blockfromright":{"name":"Block from Right","frame_0":{"transform":{"opacity":0}},"frame_1":{"transform":{"opacity":1},"sfx":{"effect":"blocktoleft","color":"#ffffff"},"timeline":{"ease":"power4.inOut","speed":1200}}},
					"blockfromtop":{"name":"Block from Top","frame_0":{"transform":{"opacity":0}},"frame_1":{"transform":{"opacity":1},"sfx":{"effect":"blocktobottom","color":"#ffffff"},"timeline":{"ease":"power4.inOut","speed":1200}}},
					"blockfrombottom":{"name":"Block from Bottom","frame_0":{"transform":{"opacity":0}},"frame_1":{"transform":{"opacity":1},"sfx":{"effect":"blocktotop","color":"#ffffff"},"timeline":{"ease":"power4.inOut","speed":1200}}}
				}
			},
			"lettran":{
				"group":"Letter Transitions",
				"transitions":{
					"LettersFlyInFromLeft":{"name":"Letters Fly In From Left","frame_0":{"transform":{"opacity":1},"chars":{"use":true,"x":"-105%","opacity":"0","rotationZ":"-90deg"},"mask":{"use":true}},"frame_1":{"timeline":{"speed":1200},"transform":{"opacity":1},"chars":{"ease":"power4.inOut","use":true,"direction":"backward","delay":10,"x":0,"opacity":1,"rotationZ":"0deg"},"mask":{"use":true}}},
					"LettersFlyInFromRight":{"name":"Letters Fly In From Right","frame_0":{"transform":{"opacity":1},"chars":{"use":true,"x":"105%","opacity":"1","rotationY":"45deg","rotationZ":"90deg"},"mask":{"use":true}},"frame_1":{"timeline":{"speed":1200},"transform":{"opacity":1},"chars":{"ease":"power4.inOut","use":true,"direction":"forward","delay":10,"x":0,"opacity":1,"rotationY":0,"rotationZ":"0deg"},"mask":{"use":true}}},
					"LettersFlyInFromTop":{"name":"Letters Fly In From Top","frame_0":{"transform":{"opacity":1},"chars":{"use":true,"y":"-100%","opacity":"0","rotationZ":"35deg"},"mask":{"use":true}},"frame_1":{"timeline":{"speed":1200},"transform":{"opacity":1},"chars":{"ease":"power4.inOut","use":true,"direction":"forward","delay":10,"y":0,"opacity":1,"rotationZ":"0deg"},"mask":{"use":true}}},
					"LettersFlyInFromBottom":{"name":"Letters Fly In From Bottom","frame_0":{"transform":{"opacity":1},"chars":{"use":true,"y":"100%","opacity":"0","rotationZ":"-35deg"},"mask":{"use":true}},"frame_1":{"timeline":{"speed":1200},"transform":{"opacity":1},"chars":{"ease":"power4.inOut","use":true,"direction":"forward","delay":10,"y":0,"opacity":1,"rotationZ":"0deg"},"mask":{"use":true}}},
					"LetterFlipFromTop":{"name":"Letter Flip From Top","frame_0":{"transform":{"opacity":1},"chars":{"use":true,"opacity":0,"rotationX":"90deg","y":"0","originZ":"-50"}},"frame_1":{"timeline":{"speed":1750},"chars":{"use":true,"opacity":1,"rotationX":0,"delay":10,"originZ":"-50","ease":"power4.inOut"}}},
					"LetterFlipFromBottom":{"name":"Letter Flip From Bottom","frame_0":{"transform":{"opacity":1},"chars":{"use":true,"opacity":0,"rotationX":"-90deg","y":"0","originZ":"-50"}},"frame_1":{"timeline":{"speed":1750},"chars":{"use":true,"opacity":1,"rotationX":0,"delay":10,"originZ":"-50","ease":"power4.inOut"}}},
					"FlipAndLetterCycle":{"name":"Letter Flip Cycle","frame_0":{"transform":{"opacity":0,"rotationX":"70deg","y":"0","originZ":"-50"},"chars":{"use":true,"opacity":0,"y":"[-100||100]"}},"frame_1":{"timeline":{"speed":1750,"ease":"power4.inOut"},"transform":{"opacity":1,"originZ":"-50","rotationX":0},"chars":{"use":true,"direction":"middletoedge","opacity":1,"y":0,"delay":10,"ease":"power4.inOut"}}}
				}
			},
			"masktrans":{
				"group":"Masked Transitions",
				"transitions":{
					"MaskedZoomOut":{"name":"Masked Zoom Out","frame_0":{"transform":{"opacity":0,"scaleX":2,"scaleY":2},"mask":{"use":true}},"frame_1":{"timeline":{"speed":1000,"ease":"power2.out"},"mask":{"use":true},"transform":{"opacity":1,"scaleX":1,"scaleY":1}}},
					"SlideMaskFromBottom":{"name":"Slide From Bottom","frame_0":{"transform":{"opacity":0,"y":"100%"},"mask":{"use":true}},"frame_1":{"timeline":{"speed":1200,"ease":"power3.inOut"},"mask":{"use":true,"y":0},"transform":{"opacity":1,"y":0}}},
					"SlideMaskFromLeft":{"name":"Slide From Left","frame_0":{"transform":{"opacity":0,"x":"-100%"},"mask":{"use":true}},"frame_1":{"timeline":{"speed":1000,"ease":"power3.inOut"},"mask":{"use":true},"transform":{"opacity":1,"x":0}}},
					"SlideMaskFromRight":{"name":"Slide From Right","frame_0":{"transform":{"opacity":0,"x":"100%"},"mask":{"use":true}},"frame_1":{"timeline":{"speed":1000,"ease":"power3.inOut"},"mask":{"use":true},"transform":{"opacity":1,"x":0}}},
					"SlideMaskFromTop":{"name":"Slide From Top","frame_0":{"transform":{"opacity":0,"y":"-100%"},"mask":{"use":true}},"frame_1":{"timeline":{"speed":1200,"ease":"power3.inOut"},"mask":{"use":true},"transform":{"opacity":1,"y":0}}},
					"SmoothMaskFromRight":{"name":"Smooth Mask From Right","frame_0":{"transform":{"opacity":1,"x":"-175%"},"mask":{"use":true,"x":"100%"}},"frame_1":{"timeline":{"speed":1000,"ease":"power3.out"},"mask":{"use":true,"x":0},"transform":{"opacity":1,"x":0}}},
					"SmoothMaskFromLeft":{"name":"Smooth Mask From Left","frame_0":{"transform":{"opacity":1,"x":"175%"},"mask":{"use":true,"x":"-100%"}},"frame_1":{"timeline":{"speed":1000,"ease":"power3.out"},"mask":{"use":true,"x":0},"transform":{"opacity":1,"x":0}}}
				}
			},
			"popup":{
				"group":"Pop Ups",
				"transitions":{
					"PopUpBack":{"name":"Pop Up Back","frame_0":{"transform":{"opacity":0,"rotationY":"360deg"}},"frame_1":{"timeline":{"speed":500,"ease":"back.out"},"transform":{"opacity":1,"rotationY":0}}},
					"PopUpSmooth":{"name":"Pop Up Smooth","frame_0":{"transform":{"opacity":0,"scaleX":0.9,"scaleY":0.9}},"frame_1":{"timeline":{"speed":1000,"ease":"power3.inOut"},"transform":{"opacity":1,"scaleX":1,"scaleY":1}}},
					"SmoothPopUp_One":{"name":"Smooth Pop Up v.1","frame_0":{"transform":{"opacity":0,"scaleX":0.8,"scaleY":0.8}},"frame_1":{"timeline":{"speed":1000,"ease":"power4.out"},"transform":{"opacity":1,"scaleX":1,"scaleY":1}}},
					"SmoothPopUp_Two":{"name":"Smooth Pop Up v.2","frame_0":{"transform":{"opacity":0,"scaleX":0.9,"scaleY":0.9}},"frame_1":{"timeline":{"speed":1000,"ease":"power2.inOut"},"transform":{"opacity":1,"scaleX":1,"scaleY":1}}}
				}
			},
			"rotate":{
				"group":"Rotations",
				"transitions":{					
					"RotateInFromBottom":{"name":"Rotate In From Bottom","frame_0":{"transform":{"opacity":0,"rotationZ":"70deg","y":"bottom","scaleY":2,"scaleX":2}},"frame_1":{"timeline":{"speed":1000,"ease":"power3.inOut"},"transform":{"opacity":1,"y":0,"rotationZ":0,"scaleX":1,"scaleY":1}}},
					"RotateInFormZero":{"name":"Rotate In From Bottom v2.","frame_0":{"transform":{"opacity":1,"rotationY":"-20deg","rotationX":"-20deg","y":"200%","scaleY":2,"scaleX":2}},"frame_1":{"timeline":{"speed":1000,"ease":"power3.out"},"transform":{"opacity":1,"y":0,"rotationZ":0,"rotationY":0,"scaleX":1,"scaleY":1}}},
					"FlipFromTop":{"name":"Flip From Top","frame_0":{"transform":{"opacity":0,"rotationX":"70deg","y":"0","originZ":"-50"}},"frame_1":{"timeline":{"speed":1750,"ease":"power4.inOut"},"transform":{"opacity":1,"originZ":"-50","rotationX":0}}},
					"FlipFromBottom":{"name":"Flip From Bottom","frame_0":{"transform":{"opacity":0,"rotationX":"-70deg","y":"0","originZ":"-50"}},"frame_1":{"timeline":{"speed":1750,"ease":"power4.inOut"},"transform":{"opacity":1,"rotationX":0,"originZ":"-50"}}}
				}
			},
			"slidetrans":{
				"group":"Slide Transitions",
				"transitions":{
					"sft":{"name":"Short Slide from Top","frame_0":{"transform":{"opacity":0,"y":-50}},"frame_1":{"timeline":{"speed":1000,"ease":"power3.inOut"},"transform":{"opacity":1,"y":0}}},
					"sfb":{"name":"Short Slide from Bottom","frame_0":{"transform":{"opacity":0,"y":50}},"frame_1":{"timeline":{"speed":1000,"ease":"power3.inOut"},"transform":{"opacity":1,"y":0}}},
					"sfl":{"name":"Short Slide from Left","frame_0":{"transform":{"opacity":0,"x":-50}},"frame_1":{"timeline":{"speed":1000,"ease":"power3.inOut"},"transform":{"opacity":1,"x":0}}},
					"sfr":{"name":"Short Slide from Right","frame_0":{"transform":{"opacity":0,"x":50}},"frame_1":{"timeline":{"speed":1000,"ease":"power3.inOut"},"transform":{"opacity":1,"x":0}}},
					"lft":{"name":"Long Slide from Top","frame_0":{"transform":{"opacity":0,"y":"top"}},"frame_1":{"timeline":{"speed":1000,"ease":"power3.inOut"},"transform":{"opacity":1,"y":0}}},
					"lfb":{"name":"Long Slide from Bottom","frame_0":{"transform":{"opacity":0,"y":"bottom"}},"frame_1":{"timeline":{"speed":1000,"ease":"power3.inOut"},"transform":{"opacity":1,"y":0}}},
					"lfl":{"name":"Long Slide from Left","frame_0":{"transform":{"opacity":0,"x":"left"}},"frame_1":{"timeline":{"speed":1000,"ease":"power3.inOut"},"transform":{"opacity":1,"x":0}}},
					"lfr":{"name":"Long Slide from Right","frame_0":{"transform":{"opacity":0,"x":"right"}},"frame_1":{"timeline":{"speed":1000,"ease":"power3.inOut"},"transform":{"opacity":1,"x":0}}},
					"SmoothSlideFromBottom":{"name":"Smooth Slide From Bottom","frame_0":{"transform":{"opacity":0,"y":"100%"}},"frame_1":{"timeline":{"speed":1200,"ease":"power4.inOut"},"transform":{"opacity":1,"y":0}}}
				}
			},
			"skewtrans":{
				"group":"Skew Transitions",
				"transitions":{
					"skewfromleft":{"name":"Skew from Left","frame_0":{"transform":{"opacity":0,"skewX":85,"x":"left"}},"frame_1":{"timeline":{"speed":1000,"ease":"power3.inOut"},"transform":{"opacity":1,"skewX":0,"x":0}}},
					"skewfromright":{"name":"Skew from Right","frame_0":{"transform":{"opacity":0,"skewX":-85,"x":"right"}},"frame_1":{"timeline":{"speed":1000,"ease":"power3.inOut"},"transform":{"opacity":1,"skewX":0,"x":0}}},
					"skewfromleftshort":{"name":"Skew from Left Short","frame_0":{"transform":{"opacity":0,"skewX":45,"x":"-100%"}},"frame_1":{"timeline":{"speed":1000,"ease":"power3.inOut"},"transform":{"opacity":1,"skewX":0,"x":0}}},
					"skewfromrightshort":{"name":"Skew from Right Short","frame_0":{"transform":{"opacity":0,"skewX":-45,"x":"100%"}},"frame_1":{"timeline":{"speed":1000,"ease":"power3.inOut"},"transform":{"opacity":1,"skewX":0,"x":0}}}
				}
			},
			"simpltrans":{
				"group":"Simple Transitions",
				"transitions":{
					"noanim":{"name":"No Animation","frame_0":{"transform":{"opacity":1}},"frame_1":{"transform":{"opacity":1}}},
					"tp-fade":{"name":"Fade In","frame_0":{"transform":{"opacity":0}},"frame_1":{"timeline":{"speed":1500,"ease":"power4.inOut"},"transform":{"opacity":1}}}
				}
			},
			"randtrans":{
				"group":"Random Transitions",
				"transitions":{
					"Random":{"name":"Random","frame_0":{"transform":{"opacity":0,"y":"{-150,150}","x":"{-250,250}","scaleX":"{0,1.5}","scaleY":"{0,1.5}","rotationX":"{-90,90}","rotationY":"{-90,90}","rotationZ":"{-90,90}"}},"frame_1":{"timeline":{"speed":1500,"ease":"power4.inOut"},"transform":{"opacity":1,"x":0,"y":0,"z":0,"rotationX":0,"rotationY":0,"rotationZ":0,"scaleX":1,"scaleY":1}}},
					"RandomChars":{"name":"Random Chars","frame_0":{"transform":{"opacity":1},"chars":{"use":true,"y":"{-150,150}","x":"{-250,250}","scaleX":"{0,1.5}","scaleY":"{0,1.5}","rotationX":"{-90,90}","rotationY":"{-90,90}","rotationZ":"{-90,90}"}},"frame_1":{"timeline":{"speed":1500,"ease":"power4.inOut"},"chars":{"use":true,"direction":"random","pacity":1,"x":0,"y":0,"z":0,"rotationX":0,"rotationY":0,"rotationZ":0,"scaleX":1,"scaleY":1,"delay":10}}}
				}
			}
		}';

		$out = '{
			"custom":{"group":"Custom","custom":true,"transitions":' .
		json_encode($custom_out)
			. '},
			"blck":{
				"group":"Block Transitions (SFX)",
				"transitions":{
					"blocktoleft":{"name":"Block to Left","frame_999":{"transform":{"opacity":0},"sfx":{"effect":"blocktoright","color":"#ffffff"},"timeline":{"ease":"power4.inOut","speed":1200}}},
					"blocktoright":{"name":"Block to Right","frame_999":{"transform":{"opacity":0},"sfx":{"effect":"blocktoleft","color":"#ffffff"},"timeline":{"ease":"power4.inOut","speed":1200}}},
					"blocktotop":{"name":"Block to Top","frame_999":{"transform":{"opacity":0},"sfx":{"effect":"blocktobottom","color":"#ffffff"},"timeline":{"ease":"power4.inOut","speed":1200}}},
					"blocktobottom":{"name":"Block to Bottom","frame_999":{"transform":{"opacity":0},"sfx":{"effect":"blocktotop","color":"#ffffff"},"timeline":{"ease":"power4.inOut","speed":1200}}}
				}
			},
			"lettran":{
				"group":"Letter Transitions",
				"transitions":{
					"LettersFlyOutToLeft":{"name":"Letters Fly Out To Left","frame_999":{"transform":{"opacity":1},"chars":{"ease":"power4.inOut","direction":"forward","use":true,"x":"-105%","opacity":"0","delay":10,"rotationZ":"-90deg"},"mask":{"use":true},"timeline":{"speed":1200}}},
					"LettersFlyInFromRight":{"name":"Letters Fly In From Right","frame_999":{"transform":{"opacity":1},"chars":{"ease":"power4.inOut","delay":10,"direction":"backward","use":true,"x":"105%","opacity":"0","rotationY":"45deg","rotationZ":"90deg"},"timeline":{"speed":1200},"mask":{"use":true}}},
					"LettersFlyInFromTop":{"name":"Letters Fly In From Top","frame_999":{"transform":{"opacity":1},"chars":{"use":true,"y":"-100%","opacity":"0","rotationZ":"35deg","ease":"power4.inOut","direction":"backward","delay":10},"timeline":{"speed":1200},"mask":{"use":true}}},
					"LettersFlyInFromBottom":{"name":"Letters Fly In From Bottom","frame_999":{"transform":{"opacity":1},"chars":{"use":true,"y":"100%","opacity":"0","rotationZ":"-35deg","ease":"power4.inOut","direction":"forward","delay":10},"timeline":{"speed":1200},"mask":{"use":true}}},
					"LetterFlipFromTop":{"name":"Letter Flip From Top","frame_999":{"transform":{"opacity":1},"chars":{"use":true,"opacity":0,"rotationX":"90deg","y":"0","originZ":"-50","ease":"power4.inOut","delay":10},"timeline":{"speed":1750}}},
					"LetterFlipFromBottom":{"name":"Letter Flip From Bottom","frame_999":{"transform":{"opacity":1},"chars":{"use":true,"opacity":0,"rotationX":"-90deg","y":"0","originZ":"-50","delay":10,"ease":"power4.inOut"},"timeline":{"speed":1750}}},
					"FlipAndLetterCycle":{"name":"Letter Flip Cycle","frame_999":{"transform":{"opacity":0,"rotationX":"70deg","y":"0","originZ":"-50"},"chars":{"use":true,"direction":"middletoedge","delay":10,"ease":"power4.inOut","opacity":0,"y":"[-100||100]"},"timeline":{"speed":1750,"ease":"power4.inOut"}}}
				}
			},
			"masktrans":{
				"group":"Masked Transitions",
				"transitions":{
					"MaskedZoomOut":{"name":"Masked Zoom In","frame_999":{"transform":{"opacity":0,"scaleX":2,"scaleY":2},"mask":{"use":true},"timeline":{"speed":1000,"ease":"power2.out"}}},
					"SlideMaskToBottom":{"name":"Slide To Bottom","frame_999":{"transform":{"opacity":0,"y":"100%"},"mask":{"use":true},"timeline":{"speed":1200,"ease":"power3.inOut"}}},
					"SlideMaskToLeft":{"name":"Slide To Left","frame_999":{"transform":{"opacity":0,"x":"-100%"},"mask":{"use":true},"timeline":{"speed":1000,"ease":"power3.inOut"}}},
					"SlideMaskToRight":{"name":"Slide To Right","frame_999":{"transform":{"opacity":0,"x":"100%"},"mask":{"use":true},"timeline":{"speed":1000,"ease":"power3.inOut"}}},
					"SlideMaskToTop":{"name":"Slide To Top","frame_999":{"transform":{"opacity":0,"y":"-100%"},"mask":{"use":true},"timeline":{"speed":1200,"ease":"power3.inOut"}}},
					"SmoothMaskToRight":{"name":"Smooth Mask To Right","frame_999":{"transform":{"opacity":1,"x":"-175%"},"mask":{"use":true,"x":"100%"},"timeline":{"speed":1000,"ease":"power3.inOut"}}},
					"SmoothMaskToLeft":{"name":"Smooth Mask To Left","frame_999":{"transform":{"opacity":1,"x":"175%"},"mask":{"use":true,"x":"-100%"},"timeline":{"speed":1000,"ease":"power3.inOut"}}},
					"SmoothToBottom":{"name":"Smooth To Bottom","frame_999":{"transform":{"opacity":1,"y":"175%"},"mask":{"use":true},"timeline":{"speed":1000,"ease":"power2.inOut"}}},
					"SmoothToTop":{"name":"Smooth To Top","frame_999":{"transform":{"opacity":1,"y":"-175%"},"mask":{"use":true},"timeline":{"speed":1000,"ease":"power2.inOut"}}}
				}
			},
			"bounce":{
				"group":"Bounce and Hide",
				"transitions":{
					"BounceOut":{"name":"Bounce Out","frame_999":{"timeline":{"speed":500,"ease":"back.in"},"transform":{"opacity":0,"scaleX":0.7,"scaleY":0.7}}},
					"SlurpOut":{"name":"Slurp Out","frame_999":{"timeline":{"speed":1000,"ease":"power2.in"},"transform":{"opacity":0,"y":"100%","scaleX":0.7,"scaleY":0.7},"mask":{"use":true}}},
					"PopUpBack":{"name":"Bounce Out Rotate","frame_999":{"timeline":{"speed":500,"ease":"back.in"},"transform":{"opacity":0,"rotationY":"360deg"}}},
					"PopUpSmooth":{"name":"Hide Smooth","frame_999":{"timeline":{"speed":1000,"ease":"power3.inOut"},"transform":{"opacity":0,"scaleX":0.9,"scaleY":0.9}}},
					"SmoothPopUp_One":{"name":"Smooth Hide v.1","frame_999":{"timeline":{"speed":1000,"ease":"power4.out"},"transform":{"opacity":0,"scaleX":0.8,"scaleY":0.8}}},
					"SmoothPopUp_Two":{"name":"Smooth Hide v.2","frame_999":{"timeline":{"speed":1000,"ease":"power2.inOut"},"transform":{"opacity":0,"scaleX":0.9,"scaleY":0.9}}}
				}
			},
			"rotate":{
				"group":"Rotations",
				"transitions":{
					"RotateOutToBottom":{"name":"Rotate Out To Bottom","frame_999":{"timeline":{"speed":1000,"ease":"power3.inOut"},"transform":{"opacity":0,"rotationZ":"70deg","y":"bottom","scaleY":2,"scaleX":2}}},
					"RotateInFormZero":{"name":"Rotate Out To Bottom v2.","frame_999":{"timeline":{"speed":1000,"ease":"power3.out"},"transform":{"opacity":0,"rotationY":"-20deg","rotationX":"-20deg","y":"200%","scaleY":2,"scaleX":2}}},
					"FlipToTop":{"name":"Flip To Top","frame_999":{"timeline":{"speed":1750,"ease":"power4.inOut"},"transform":{"opacity":0,"rotationX":"70deg","y":"0","originZ":"-50"}}},
					"FlipToBottom":{"name":"Flip To Bottom","frame_999":{"timeline":{"speed":1750,"ease":"power4.inOut"},"transform":{"opacity":0,"rotationX":"-70deg","y":"0","originZ":"-50"}}}
				}
			},
			"slidetrans":{
				"group":"Slide Transitions",
				"transitions":{
					"stt":{"name":"Short Slide to Top","frame_999":{"timeline":{"speed":1000,"ease":"power3.inOut"},"transform":{"opacity":0,"y":-50}}},
					"stb":{"name":"Short Slide to Bottom","frame_999":{"timeline":{"speed":1000,"ease":"power3.inOut"},"transform":{"opacity":0,"y":50}}},
					"stl":{"name":"Short Slide to Left","frame_999":{"timeline":{"speed":1000,"ease":"power3.inOut"},"transform":{"opacity":0,"x":-50}}},
					"str":{"name":"Short Slide to Right","frame_999":{"timeline":{"speed":1000,"ease":"power3.inOut"},"transform":{"opacity":0,"x":50}}},
					"ltt":{"name":"Long Slide to Top","frame_999":{"timeline":{"speed":1000,"ease":"power3.inOut"},"transform":{"opacity":0,"y":"top"}}},
					"ltb":{"name":"Long Slide to Bottom","frame_999":{"timeline":{"speed":1000,"ease":"power3.inOut"},"transform":{"opacity":0,"y":"bottom"}}},
					"ltl":{"name":"Long Slide to Left","frame_999":{"timeline":{"speed":1000,"ease":"power3.inOut"},"transform":{"opacity":0,"x":"left"}}},
					"ltr":{"name":"Long Slide to Right","frame_999":{"timeline":{"speed":1000,"ease":"power3.inOut"},"transform":{"opacity":0,"x":"right"}}},
					"SmoothSlideToBottom":{"name":"Smooth Slide To Bottom","frame_999":{"timeline":{"speed":1200,"ease":"power4.inOut"},"transform":{"opacity":0,"y":"100%"}}}
				}
			},
			"skewtrans":{
				"group":"Skew Transitions",
				"transitions":{
					"skewfromleft":{"name":"Skew from Left","frame_999":{"timeline":{"speed":1000,"ease":"power3.inOut"},"transform":{"opacity":0,"skewX":85,"x":"left"}}},
					"skewfromright":{"name":"Skew from Right","frame_999":{"timeline":{"speed":1000,"ease":"power3.inOut"},"transform":{"opacity":0,"skewX":-85,"x":"right"}}},
					"skewfromleftshort":{"name":"Skew from Left Short","frame_999":{"timeline":{"speed":1000,"ease":"power3.inOut"},"transform":{"opacity":0,"skewX":45,"x":"-100%"}}},
					"skewfromrightshort":{"name":"Skew from Right Short","frame_999":{"timeline":{"speed":1000,"ease":"power3.inOut"},"transform":{"opacity":0,"skewX":-45,"x":"100%"}}}
				}
			},
			"simpltrans":{
				"group":"Simple Transitions",
				"transitions":{
					"noanim":{"name":"No Animation","frame_999":{"transform":{"opacity":1}}},
					"tp-fade-out":{"name":"Fade Out","frame_999":{"timeline":{"speed":1000,"ease":"power4.inOut"},"transform":{"opacity":0}}},
					"fadeoutlong":{"name":"Fade Out Long","frame_999":{"timeline":{"speed":1000,"ease":"power2.in"},"transform":{"opacity":0}}}
				}
			},
			"randtrans":{
				"group":"Random Transitions",
				"transitions":{
					"RandomOut":{"name":"Random Out","frame_999":{"timeline":{"speed":1500,"ease":"power4.inOut"},"transform":{"opacity":0,"y":"{-150,150}","x":"{-250,250}","scaleX":"{0,1.5}","scaleY":"{0,1.5}","rotationX":"{-90,90}","rotationY":"{-90,90}","rotationZ":"{-90,90}"}}},
					"RandomCharsOut":{"name":"Random Chars Out","frame_999":{"timeline":{"speed":1500,"ease":"power4.inOut"},"transform":{"opacity":1},"chars":{"direction":"random","delay":10,"use":true,"y":"{-150,150}","x":"{-250,250}","scaleX":"{0,1.5}","scaleY":"{0,1.5}","rotationX":"{-90,90}","rotationY":"{-90,90}","rotationZ":"{-90,90}"}}}
				}
			}
		}';

		$loop = '{
			"custom":{group:"Custom",custom:true,transitions:' .
		json_encode($custom_loop)
			. '},
			"pendulum":{group:"Pendulum Loops",
				transitions: {
					"inplacependulum":{name:"In Place Pendulum", loop:{use:true, yoyo_rotate:true, speed:3000, ease:"power1.inOut", frame_0:{rotationZ:-40}, frame_999:{rotationZ:40}}},
					"pendulumbelow":{name:"Pendulum Below", loop:{use:true, yoyo_rotate:true, speed:3000, originY:"-200%", ease:"sine.inOut", frame_0:{rotationZ:-40}, frame_999:{rotationZ:40}}},
					"pendulumabove":{name:"Pendulum Above",loop:{use:true, yoyo_rotate:true, speed:3000, originY:"200%", ease:"sine.inOut", frame_0:{rotationZ:-40}, frame_999:{rotationZ:40}}},
					"pendulumleft":{name:"Pendulum Left",loop:{use:true, yoyo_rotate:true, speed:3000, originX:"150%", ease:"sine.inOut", frame_0:{rotationZ:-20}, frame_999:{rotationZ:20}}},
					"pendulumright":{name:"Pendulum Right",loop:{use:true, yoyo_rotate:true, speed:3000, originX:"-50%", ease:"sine.inOut", frame_0:{rotationZ:-20}, frame_999:{rotationZ:20}}}

			}},
			"effects":{group:"Effect Loops",
				transitions: {
					"grayscale":{name:"Grayscale",loop:{use:true, yoyo_filter:true, speed:1000,  ease:"sine.inOut", frame_0:{grayscale:0}, frame_999:{grayscale:100}}},
					"blink":{name:"Blink",loop:{use:true, yoyo_filter:true, speed:1500,  ease:"sine.inOut", frame_0:{opacity:0}, frame_999:{opacity:1}}},
					"flattern":{name:"Flattern",loop:{use:true, yoyo_filter:true, speed:100,  ease:"sine.inOut", frame_0:{opacity:0.2,blur:0}, frame_999:{opacity:1,blur:4}}},
					"lighting":{name:"Lithing",loop:{use:true, yoyo_filter:true, speed:1000,  ease:"sine.inOut", frame_0:{brightness:100}, frame_999:{brightness:1000}}}
			}},
			"wave":{group:"Wave",
				transitions: {
					"littlewaveleft":{name:"Little Wave Left", loop:{use:true, curved:true, speed:3000, ease:"none", frame_0:{xr:60,yr:60}, frame_999:{xr:60,yr:60}}},
					"littlewaveright":{name:"Little Wave Right", loop:{use:true, curved:true, speed:3000, ease:"none", frame_0:{xr:60,yr:-60}, frame_999:{xr:60,yr:-60}}},
					"Bigwaveleft":{name:"Big Wave Left", loop:{use:true, curved:true, speed:3000, ease:"none", frame_0:{xr:140,yr:140}, frame_999:{xr:140,yr:140}}},
					"Bigwaveright":{name:"Big Wave Right", loop:{use:true, curved:true, speed:3000, ease:"none", frame_0:{xr:140,yr:-140}, frame_999:{xr:140,yr:-140}}},
					"eight":{name:"Curving Wave", loop:{use:true, curved:true, speed:3000, ease:"none", curviness:8, frame_0:{xr:100,yr:100}, frame_999:{xr:100,yr:100}}}
			}},
			"wiggle":{group:"Wiggles",
				transitions: {
					"smoothwigglez":{name:"Smooth Y Axis Wiggle", loop:{use:true, yoyo_rotate:true, speed:3000, ease:"sine.inOut", frame_0:{rotationY:-40}, frame_999:{rotationY:40}}},
					"smoothwigglezii":{name:"Smooth Y Axis Wiggle II.", loop:{use:true, originZ:60, yoyo_rotate:true, speed:3000, ease:"sine.inOut", frame_0:{rotationY:-40}, frame_999:{rotationY:40}}},
					"smoothwiggleziii":{name:"Smooth Y Axis Wiggle III.", loop:{use:true, originZ:-160, yoyo_rotate:true, speed:3000, ease:"sine.inOut", frame_0:{rotationY:-40}, frame_999:{rotationY:40}}},
					"smoothwigglex":{name:"Smooth X Axis Wiggle", loop:{use:true, yoyo_rotate:true, speed:3000, ease:"sine.inOut", frame_0:{rotationX:-40}, frame_999:{rotationX:40}}},
					"smoothwigglexii":{name:"Smooth X Axis Wiggle II", loop:{use:true, originZ:60, yoyo_rotate:true, speed:3000, ease:"sine.inOut", frame_0:{rotationX:-40}, frame_999:{rotationX:40}}},
					"smoothwigglexiii":{name:"Smooth X Axis Wiggle III", loop:{use:true, originZ:-160, yoyo_rotate:true, speed:3000, ease:"sine.inOut", frame_0:{rotationX:-40}, frame_999:{rotationX:40}}},
					"crazywiggle":{name:"Funny Wiggle Path", loop:{use:true, originZ:-160, originY:"-50%", yoyo_scale:true, yoyo_move:true, yoyo_rotate:true, speed:3000, ease:"circ.inOut", frame_0:{x:100, y:-70,rotationX:-20, rotationY:-20, rotationZ:10}, frame_999:{x:0, y:70,scaleX:1.4, scaleY:1.4, rotationX:30, rotationY:10, rotationZ:-5}}}
			}},
			"rotate":{group:"Rotating",
				transitions: {
					"rotating":{name:"Rotate", loop:{use:true, speed:3000, ease:"none", frame_0:{rotationZ:0}, frame_999:{rotationZ:360}}},
					"rotatingyoyo":{name:"Rotate Forw. Backw.", loop:{use:true, yoyo_rotate:true, speed:3000, ease:"none", frame_0:{rotationZ:-100}, frame_999:{rotationZ:100}}},
					"leaf":{name:"Flying Around", loop:{use:true,  curved:true, curviness:25, yoyo_rotate:true, yoyo_filter:true, speed:6000, ease:"none", frame_0:{xr:30,yr:22,zr:40}, frame_999:{xr:40,yr:12, zr:-100, rotationZ:720,blur:5}}},
			}},
			"slide":{group:"Slide and Hover",
				transitions: {
					"slidehorizontal":{name:"Slide Horizontal", loop:{use:true, yoyo_move:true, speed:3000, ease:"sine.inOut", frame_0:{x:-100}, frame_999:{x:100}}},
					"hoover":{name:"Hover", loop:{use:true, yoyo_move:true,speed:6000, ease:"sine.inOut", frame_0:{y:-10}, frame_999:{y:10}}},
			}},
			"pulse":{group:"Pulse",
				transitions: {
					"pulse":{name:"Pulse", loop:{use:true, yoyo_scale:true, yoyo_filter:true, speed:2000, ease:"power4.inOut", frame_999:{scaleX:1.2, scaleY:1.2}}},
					"pulseminus":{name:"Pulse Minus", loop:{use:true, yoyo_scale:true, yoyo_filter:true, speed:2000, ease:"power0.inOut", frame_999:{scaleX:0.8, scaleY:0.8}}},
					"pulseandopacity":{name:"Pulse and Fade", loop:{use:true, yoyo_scale:true, yoyo_filter:true, speed:2000, ease:"power0.inOut", frame_999:{scaleX:1.2, scaleY:1.2,opacity:0.6}}},
					"pulseandopacityminus":{name:"Pulse and Fade Minus", loop:{use:true, yoyo_scale:true, yoyo_filter:true, speed:2000, ease:"power2.inOut", frame_999:{scaleX:0.8, scaleY:0.8,opacity:0.6}}},
					"pulseandopablur":{name:"Pulse and Blur", loop:{use:true, yoyo_scale:true, yoyo_filter:true, speed:2000, ease:"power1.inOut", frame_999:{scaleX:1.2, scaleY:1.2,opacity:0.8,blur:5}}},
					"pulseandopablurminus":{name:"Pulse and Blur Minus", loop:{use:true, yoyo_scale:true, yoyo_filter:true, speed:2000, ease:"power0.inOut", frame_999:{scaleX:0.8, scaleY:0.8,opacity:0.8,blur:5}}}

			}},
		}';

		$anim = array();
		$anim['in'] = ($raw) ? $in : json_decode($in, true);
		$anim['out'] = ($raw) ? $out : json_decode($out, true);
		$anim['loop'] = ($raw) ? $loop : json_decode($loop, true);

		return $anim;
	}

	/**
	 * add default icon sets of Slider Revolution
	 * @since: 5.0
	 * @before: RevSliderBase::set_icon_sets();
	 **/
	public function set_icon_sets($icon_sets){

		$icon_sets[] = 'fa-icon-';
		$icon_sets[] = 'fa-';
		$icon_sets[] = 'pe-7s-';

		return $icon_sets;
	}
}

?>