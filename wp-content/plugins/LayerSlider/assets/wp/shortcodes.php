<?php

// Prevent direct file access
defined( 'LS_ROOT_FILE' ) || exit;

$GLOBALS['lsLoadPlugins'] 	= array();
$GLOBALS['lsLoadFonts'] 	= array();

function layerslider( $id = 0, $filters = '', $options = array() ) {

	$attrs = array_merge(
		array(
			'id' => $id,
			'filters' => $filters
		),
		$options
	);

	echo LS_Shortcode::handleShortcode( $attrs );
}


class LS_Shortcode {

	// List of already included sliders on page.
	// Using to identify duplicates and give them
	// a unique slider ID to avoid issues with caching.
	public static $slidersOnPage = array();

	private function __construct() {}


	/**
	 * Registers the LayerSlider shortcode.
	 *
	 * @since 5.3.3
	 * @access public
	 * @return void
	 */

	public static function registerShortcode() {
		if(!shortcode_exists('layerslider')) {
			add_shortcode('layerslider', array(__CLASS__, 'handleShortcode'));
		}
	}




	/**
	 * Handles the shortcode workflow to display the
	 * appropriate content.
	 *
	 * @since 5.3.3
	 * @access public
	 * @param array $atts Shortcode attributes
	 * @return bool True on successful validation, false otherwise
	 */

	public static function handleShortcode( $atts = array() ) {

		if(self::validateFilters($atts)) {

			$output = '';
			$item = self::validateShortcode( $atts );

			// Show error messages (if any)
			if( ! empty( $item['error'] ) ) {

				// Bail out early if the visitor has no permission to see error messages
				if( ! current_user_can(get_option('layerslider_custom_capability', 'manage_options')) ) {
					return '';
				}

				// Prevent showing errors for Popups
				if( ! empty($atts['popup']) || ! empty( $item['data']['flag_popup'] ) ) {
					return '';
				}


				$output .= $item['error'];
			}


			if( $item['data'] ) {
				$output .= self::processShortcode( $item['data'], $atts );
			}

			return $output;
		}
	}




	/**
	 * Validates the provided shortcode filters (if any).
	 *
	 * @since 5.3.3
	 * @access public
	 * @param array $atts Shortcode attributes
	 * @return bool True on successful validation, false otherwise
	 */

	public static function validateFilters($atts = array()) {

		// Bail out early and pass the validation
		// if there aren't filters provided
		if(empty($atts['filters'])) {
			return true;
		}

		// Gather data needed for filters
		$pages = explode(',', $atts['filters']);
		$currSlug = basename(get_permalink());
		$currPageID = (string) get_the_ID();

		foreach($pages as $page) {

			if(($page == 'homepage' && is_front_page())
				|| $currPageID == $page
				|| $currSlug == $page
				|| in_category($page)
			) {
				return true;
			}
		}

		// No filters matched,
		// return false
		return false;
	}



	/**
	 * Validates the shortcode parameters and checks
	 * the references slider.
	 *
	 * @since 5.3.3
	 * @access public
	 * @param array $atts Shortcode attributes
	 * @return bool True on successful validation, false otherwise
	 */

	public static function validateShortcode($atts = array()) {

		$error = false;
		$slider = false;

		// Has ID attribute
		if( ! empty( $atts['id'] ) ) {

			$sliderID 	= $atts['id'];
			$slider 	= self::cacheForSlider( $sliderID );

			if( empty( $slider ) ) {
				$slider = LS_Sliders::find( $sliderID );

				// Second attempt to retrieve cache (if any)
				// based on the actual slider ID instead of alias
				if( $cache = self::cacheForSlider( $slider['id'] ) ) {
					$slider = $cache;
				}
			}

			// ERROR: No slider with ID was found
			if( empty( $slider ) ) {
				$error = self::generateErrorMarkup(
					__('The slider cannot be found', 'LayerSlider'),
					null
				);

			// ERROR: The slider is not published
			} elseif( (int)$slider['flag_hidden'] ) {
				$error = self::generateErrorMarkup(
					__('Unpublished slider', 'LayerSlider'),
					sprintf(__('The slider you’ve inserted here is yet to be published, thus it won’t be displayed to your visitors. You can publish it by enabling the appropriate option in %sSlider Settings → Publish%s. ', 'LayerSlider'), '<a href="'.admin_url('admin.php?page=layerslider&action=edit&id='.(int)$slider['id'].'&showsettings=1#publish').'" target="_blank">', '</a>.'),
					'dashicons-hidden'
				);

			// ERROR: The slider was removed
			} elseif( (int)$slider['flag_deleted'] ) {
				$error = self::generateErrorMarkup(
					__('Removed slider', 'LayerSlider'),
					sprintf(__('The slider you’ve inserted here was removed in the meantime, thus it won’t be displayed to your visitors. This slider is still recoverable on the admin interface. You can enable listing removed sliders with the Screen Options → Removed sliders option, then choose the Restore option for the corresponding item to reinstate this slider, or just click %shere%s.', 'LayerSlider'), '<a href="'.wp_nonce_url( admin_url('admin.php?page=layerslider&action=restore&id='.$slider['id'].'&ref='.urlencode(get_permalink()) ), 'restore_'.$slider['id']).'">', '</a>'),
					'dashicons-trash'
				);

			// ERROR: Scheduled sliders
			} else {

				if( ! empty($slider['schedule_start']) && (int) $slider['schedule_start'] > time() ) {
					$error = self::generateErrorMarkup(
						sprintf(__('This slider is scheduled to display on %s', 'LayerSlider'), ls_date(get_option('date_format') .' '. get_option('time_format'), $slider['schedule_start']) ),
						'', 'dashicons-calendar-alt', 'scheduled'
					);
				} elseif( ! empty($slider['schedule_end']) && (int) $slider['schedule_end'] < time() ) {
					$error = self::generateErrorMarkup(
						sprintf(__('This slider was scheduled to hide on %s ','LayerSlider'), ls_date(get_option('date_format') .' '. get_option('time_format'), $slider['schedule_end']) ),
						sprintf(__('Due to scheduling, this slider is no longer visible to your visitors. If you wish to reinstate this slider, just remove the schedule in %sSlider Settings → Publish%s.', 'LayerSlider'), '<a href="'.admin_url('admin.php?page=layerslider&action=edit&id='.(int)$slider['id'].'&showsettings=1#publish').'" target="_blank">', '</a>'),
						'dashicons-no-alt', 'dead'
					);
				}

			}

		// ERROR: No slider ID was provided
		} else {
			$error = self::generateErrorMarkup();
		}

		return array(
			'error' => $error,
			'data' => $slider
		);
	}



	public static function cacheForSlider( $sliderID ) {

		// Exclude administrators to avoid serving a copy
		// where notifications and other items may not be present.
		if( current_user_can( get_option('layerslider_custom_capability', 'manage_options') ) ) {
			return false;
		}

		// Attempt to retrieve the pre-generated markup
		// set via the Transients API if caching is enabled.
		if( get_option('ls_use_cache', true) ) {

			if( $slider = get_transient('ls-slider-data-'.$sliderID) ) {
				$slider['id'] = $sliderID;
				$slider['_cached'] = true;

				return $slider;
			}
		}

		return false;
	}


	public static function getUniqueID( ) {
		return base_convert( rand( 1000000000, PHP_INT_MAX ), 10, 36 );
	}


	public static function processShortcode( $slider, $embed = array() ) {

		// Slider ID
		$sID = 'layerslider_'.$slider['id'];
		$uID = $sID .'_'. self::getUniqueID();

		// Include init code in the footer?
		$condsc = get_option( 'ls_conditional_script_loading', false );
		$condsc = apply_filters( 'ls_conditional_script_loading', $condsc );

		$footer = get_option( 'ls_include_at_footer', false );
		$footer = apply_filters( 'ls_include_at_footer', $footer );

		$footer = $condsc ? true : $footer;

		// Check for the '_cached' key in data,
		// indicating that it's a pre-generated
		// slider markup retrieved via Transients
		if( ! empty( $slider['_cached'] ) ) {
			$output = $slider;

		// No cached copy, generate new markup.
		// Make sure to include some database related
		// data, since we rely on those to display
		// notifications for admins.
		} else {

			$output = self::generateSliderMarkup( $slider, $embed );

			$output['id'] 				= $slider['id'];
			$output['schedule_start'] 	= $slider['schedule_start'];
			$output['schedule_end'] 	= $slider['schedule_end'];
			$output['flag_hidden'] 		= $slider['flag_hidden'];
			$output['flag_deleted'] 	= $slider['flag_deleted'];


			// Save generated markup if caching is enabled, except for
			// administrators to avoid serving a copy where notifications
			// and other items may be present.
			$capability = get_option('layerslider_custom_capability', 'manage_options');
			$permission = current_user_can( $capability );
			if( get_option('ls_use_cache', true) && ! $permission ) {
				set_transient('ls-slider-data-'.$slider['id'], $output, HOUR_IN_SECONDS * 6);
			}
		}

		// Replace slider ID to avoid issues with enabled caching when
		// adding the same slider to a page in multiple times
		$output['init'] = str_replace( $sID, $uID, $output['init'] );
		$output['container'] = str_replace( $sID, $uID, $output['container'] );
		$sID = $uID;

		// Override firstSlide if it is specified in embed params
		if( ! empty( $embed['firstslide'] ) ) {
			$output['init'] = str_replace('[firstSlide]', $embed['firstslide'], $output['init']);
		}

		// Filter to override the printed JavaScript init code
		if( has_filter('layerslider_slider_init') ) {
			$output['init'] = apply_filters('layerslider_slider_init', $output['init'], $slider, $sID );
		}

		// Unify the whole markup after any potential string replacement
		$output['markup'] = $output['container'].$output['markup'];

		// Filter to override the printed HTML markup
		if( has_filter('layerslider_slider_markup') ) {
			$output['markup'] = apply_filters('layerslider_slider_markup', $output['markup'], $slider, $sID);
		}

		// Plugins
		if( ! empty( $output['plugins'] ) ) {
			$GLOBALS['lsLoadPlugins'] = array_merge($GLOBALS['lsLoadPlugins'], $output['plugins']);
		}

		// Fonts
		if( ! empty( $output['fonts'] ) ) {
			$GLOBALS['lsLoadFonts'] = array_merge($GLOBALS['lsLoadFonts'], $output['fonts']);
		}


		if( $footer ) {
			$GLOBALS['lsSliderInit'][] = $output['init'];
			return $output['markup'];
		} else {
			$output['init'] = '<script type="text/javascript">'.$output['init'].'</script>';
			return $output['init'].$output['markup'];
		}
	}



	public static function generateSliderMarkup( $slider = null, $embed = array() ) {

		// Bail out early if no params received or using Popup on unactivated sites
		if( ! $slider || ( (int)$slider['flag_popup'] && ! LS_Config::isActivatedSite() ) ) {
			return array('init' => '', 'container' => '', 'markup' => '');
		}

		// Slider and markup data
		$id 			= $slider['id'];
		$sliderID 		= 'layerslider_'.$id;
		$slides 		= $slider['data'];

		// Store generated output
		$lsInit 		= array();
		$lsContainer 	= array();
		$lsMarkup 		= array();
		$lsPlugins 		= array();
		$lsFonts 		= array();

		// Include slider file
		if(is_array($slides)) {

			// Get phpQuery
			if( ! defined('LS_phpQuery') ) {
				libxml_use_internal_errors(true);
				include LS_ROOT_PATH.'/helpers/phpQuery.php';
			}

			$GLOBALS['lsPremiumNotice'] = array();

			include LS_ROOT_PATH.'/config/defaults.php';
			include LS_ROOT_PATH.'/includes/slider_markup_setup.php';
			include LS_ROOT_PATH.'/includes/slider_markup_html.php';
			include LS_ROOT_PATH.'/includes/slider_markup_init.php';

			// Admin notice when using premium features on non-activated sites
			if( ! empty( $GLOBALS['lsPremiumNotice'] ) ) {
				array_unshift($lsContainer, self::generateErrorMarkup(
					__('Premium features is available for preview purposes only.', 'LayerSlider'),
					sprintf(__('We’ve detected that you’re using premium features in this slider, but you have not yet activated your copy of LayerSlider. Premium features in your sliders will not be available for your visitors without activation. %sClick here to learn more%s. Detected features: %s', 'LayerSlider'), '<a href="https://layerslider.kreaturamedia.com/documentation/#activation" target="_blank">', '</a>', implode(', ', $GLOBALS['lsPremiumNotice'])),
					'dashicons-star-filled', 'info'
				));
			}



			$lsInit 		= implode('', $lsInit);
			$lsContainer 	= implode('', $lsContainer);
			$lsMarkup 		= implode('', $lsMarkup);
		}

		// Concatenate output
		if( get_option('ls_concatenate_output', false) ) {
			$lsInit = trim(preg_replace('/\s+/u', ' ', $lsInit));
			$lsContainer = trim(preg_replace('/\s+/u', ' ', $lsContainer));
			$lsMarkup = trim(preg_replace('/\s+/u', ' ', $lsMarkup));
		}

		// Bug fix in v5.4.0: Use self closing tag for <source>
		$lsMarkup = str_replace('></source>', ' />', $lsMarkup);

		// Return formatted data
		return array(
			'init' 		=> $lsInit,
			'container' => $lsContainer,
			'markup' 	=> $lsMarkup,
			'plugins' 	=> array_unique( $lsPlugins ),
			'fonts' 	=> array_unique( $lsFonts )
		);
	}


	public static function generateErrorMarkup( $title = null, $description = null, $logo = 'dashicons-warning', $customClass = '' ) {

		if( ! $title ) {
			$title = __('LayerSlider encountered a problem while it tried to show your slider.', 'LayerSlider');
		}

		if( is_null($description) ) {
			$description = __('Please make sure that you’ve used the right shortcode or method to insert the slider, and check if the corresponding slider exists and it wasn’t deleted previously.', 'LayerSlider');
		}

		if( $description ) {
			$description .= '<br><br>';
		}

		$logo = $logo ? '<i class="lswp-notification-logo dashicons '.$logo.'"></i>' : '';
		$notice = __('Only you and other administrators can see this to take appropriate actions if necessary.', 'LayerSlider');

		$classes = array('error', 'info', 'scheduled', 'dead');
		if( ! empty($customClass) && ! in_array($customClass, $classes) ) {
			$customClass = '';
		}


		return '<div class="clearfix lswp-notification '.$customClass.'">
					'.$logo.'
					<strong>'.$title.'</strong>
					<span>'.$description.'</span>
					<small>
						<i class="dashicons dashicons-lock"></i>
						'.$notice.'
					</small>
				</div>';
	}
}