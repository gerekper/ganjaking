<?php

// Prevent direct file access
defined( 'LS_ROOT_FILE' ) || exit;

// Private filters, can change at any time
add_filter('ls_slider_title', 'ls_filter_slider_title', 10, 2);
add_filter('ls_preview_for_slider', 'ls_preview_for_slider', 10, 1);
add_filter('ls_get_thumbnail', 'ls_get_thumbnail', 10, 3);
add_filter('ls_get_image', 'ls_get_image', 10, 2);

// Public filters
add_filter('ls_parse_defaults', 'ls_parse_defaults', 10, 2);

function ls_filter_slider_title($sliderName = '', $maxLength = 50) {
	$name = empty($sliderName) ? 'Unnamed' : htmlspecialchars(stripslashes($sliderName));
	$return = isset($name[$maxLength]) ? substr($name, 0, $maxLength) . ' ...' : $name;

	return $return;
}

function ls_preview_for_slider( $slider = array() ) {

	// Attempt to find pre-defined slider banner by upload ID
	if( ! empty($slider['data']['meta']) && ! empty($slider['data']['meta']['previewId']) ) {
		if( $src = wp_get_attachment_image_src( $slider['data']['meta']['previewId'], 'large' ) ) {
			return $src[0];
		}
	}

	// Fallback to preview URL
	if( ! empty($slider['data']['meta']) && ! empty($slider['data']['meta']['preview']) ) {
		return $slider['data']['meta']['preview'];
	}


	// Find an image
	if( isset($slider['data']['layers']) ) {
		foreach( $slider['data']['layers'] as $layer) {

			if( ! empty( $layer['properties']['thumbnail'] ) ) {
				$image = $layer['properties']['thumbnail'];

				if( ! empty( $layer['properties']['thumbnailId'] ) ) {
					$src = wp_get_attachment_image_src( $layer['properties']['thumbnailId'], 'large');
					if( ! empty( $src[0] ) ) {
						$image = $src[0];
					}
				}

				break;
			}

			if( !empty($layer['properties']['background']) && $layer['properties']['background'] !== '[image-url]' ) {
				$image = $layer['properties']['background'];

				if( ! empty($layer['properties']['backgroundId'] ) ) {
					$src = wp_get_attachment_image_src( $layer['properties']['backgroundId'], 'large');
					if( ! empty( $src[0] ) ) {
						$image = $src[0];
					}
				}

				break;
			}
		}
	}

	return ! empty( $image ) ? $image : '';
}


function ls_get_thumbnail($id = null, $url = null, $blankPlaceholder = false) {

	// Image ID
	if(!empty($id)) {
		if( $image = wp_get_attachment_image_src( $id, 'medium' ) ) {
			return $image[0];
		}
	}

	if(!empty($url)) {

		$thumb = substr_replace($url, '-150x150.', strrpos($url,'.'), 1);
		$file = LS_ROOT_PATH.'/sampleslider/'.basename($thumb);

		if(file_exists($file)) { return $thumb; } else { return $url; }
	}

	return LS_ROOT_URL.'/static/admin/img/blank.gif';
}

function ls_get_image($id = null, $url = null) {

	if( ! empty( $id )) {
		if($image = wp_get_attachment_url($id, 'thumbnail')) {
			return $image;
		}
	} elseif( ! empty( $url ) ) {
		return $url;
	}

	return LS_ROOT_URL.'/static/admin/img/blank.gif';
}


function ls_parse_defaults($defaults = array(), $raw = array()) {


	$activated 	= LS_Config::isActivatedSite();
	$capability = get_option('layerslider_custom_capability', 'manage_options');
	$permission = current_user_can( $capability );
	$ret = array();


	foreach($defaults as $key => $default) {

		$phpKey = is_string($default['keys']) ? $default['keys'] : $default['keys'][0];
		$jsKey  = is_string($default['keys']) ? $default['keys'] : $default['keys'][1];
		$retKey = isset($default['props']['meta']) ? 'props' : 'attrs';

		// Check premium features
		$isPremium = false;
		if( ! empty( $default['premium'] ) && ! $activated ) {

			//if( ! $permission ) {

				if( ! empty( $raw['styles'][$phpKey] ) ) {
					unset( $ret['props']['styles'][$jsKey] );
				}

				continue;
			//}

			$isPremium = true;
		}


		if( isset($default['props']['forceoutput']) ) {
			if( ! isset($raw[$phpKey]) ) {
				$ret[$retKey][$jsKey] = $default['value'];
			} else {
				$ret[$retKey][$jsKey] = $raw[$phpKey];
			}

		} elseif(isset($raw[$phpKey]) && isset($default['props']['output']) ) {
			$ret[$retKey][$jsKey] = $raw[$phpKey];

		} elseif(isset($raw[$phpKey]) && is_array($raw[$phpKey])) {
			$ret[$retKey][$jsKey] = $raw[$phpKey];

		} elseif(isset($raw[$phpKey]) && is_bool($default['value'])) {

			if( $default['value'] == true && ( empty( $raw[$phpKey] ) || $raw[$phpKey] === 'disabled' ) ) {
				$ret[$retKey][$jsKey] = false;

			} elseif( $default['value'] == false && ( ! empty( $raw[$phpKey] ) || $raw[$phpKey] === 'enabled') ) {

				$ret[$retKey][$jsKey] = true;
			}

		} elseif(isset($raw[$phpKey])) {

			if(
				isset($default['props']['meta']) ||
				(
					(string)$default['value'] !== (string)$raw[$phpKey] &&
					(string)$raw[$phpKey] !== '')
				)
			{
				$raw[$phpKey] = isset($default['props']['raw']) ? addslashes($raw[$phpKey]) : $raw[$phpKey];
				$ret[$retKey][$jsKey] = stripslashes($raw[$phpKey]);
			}
		}


		$premiumStyle = false;
		if( $isPremium && ! empty( $raw['styles'][$phpKey] ) ) {
			if( (string)$default['value'] !== (string)$raw['styles'][$jsKey] ) {

				// v6.6.4: Fix blend-mode due to change in default value
				if( $phpKey === 'mix-blend-mode' && $raw['styles'][$phpKey] === 'normal' ) {
					// Do nothing, the 'normal' blend-mode value should not be
					// counted as

				} else {
					$premiumStyle = true;
				}
			}
		}

		if( ! $activated && $isPremium && ( isset($ret[$retKey][$jsKey]) || ! empty( $premiumStyle ) ) ) {
			$feature = ! empty( $default['name'] ) ? $default['name'] : $jsKey;
			$GLOBALS['lsPremiumNotice'][ sanitize_title($feature) ] = $feature;
		}

	}

	return $ret;
}

function ls_array_to_attr($arr, $mode = '') {
	if(!empty($arr) && is_array($arr)) {
		$ret = array();
		foreach($arr as $key => $val) {
			if($mode == 'css' && is_numeric($val) ) {
				$ret[] = ''.$key.':'.layerslider_check_unit($val, $key).';';
			} elseif(is_bool($val)) {
				$bool = $val ? 'true' : 'false';
				$ret[] = "$key:$bool;";
			} else {
				$ret[] = "$key:$val;";
			}
		}

		if( has_filter('layerslider_attr_list') ) {
			return apply_filters( 'layerslider_attr_list', $ret );
		}

		return implode('', $ret);
	}
}

function layerslider_loaded() {
	if(has_action('layerslider_ready')) {
		do_action('layerslider_ready');
	}
}

?>
