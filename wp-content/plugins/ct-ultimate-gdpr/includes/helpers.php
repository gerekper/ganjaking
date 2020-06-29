<?php

if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Locate view template in stylesheet or plugin directory.
 *
 * @param $name
 * @param bool $include
 * @param array $options
 *
 * @return string
 */
function ct_ultimate_gdpr_locate_template( $name, $include = true, $options = array() ) {

	// view options
	$options = apply_filters( 'ct_ultimate_gdpr_locate_template_options', $options, $name );

	$include_dir_path = rtrim( get_stylesheet_directory(), '/' ) . "/ct-ultimate-gdpr";
	$path_to_file     = rtrim( $include_dir_path, '/' ) . "/$name.php";

	if ( ! is_readable( $path_to_file ) ) {
		$include_dir_path = __DIR__ . "/views";
	}

	$include_dir_path = apply_filters( 'ct_ultimate_gdpr_locate_template_path', $include_dir_path, $name );
	$path_to_file     = rtrim( $include_dir_path, '/' ) . "/$name.php";

	if ( $include ) {
		include $path_to_file;
	}

	return $path_to_file;
}

/**
 * @param string $append
 *
 * @return string
 */
function ct_ultimate_gdpr_url( $append = '' ) {
	return plugins_url( $append, __DIR__ );
}

/**
 * @param string $append
 *
 * @return string
 */
function ct_ultimate_gdpr_path( $append = '' ) {
	$path = dirname( __DIR__ );
	if ( $append ) {
		$path .= "/" . ltrim( $append, '/' );
	}

	return $path;
}

/**
 * Get value from array helper
 *
 * @param $variable
 * @param $array
 * @param mixed $default
 * @param bool $allow_empty
 *
 * @return bool|mixed
 */
function ct_ultimate_gdpr_get_value( $variable, $array, $default = false, $allow_empty = true ) {
	return is_array( $array ) && isset( $array[ $variable ] ) && ( $allow_empty || ! empty( $array[ $variable ] ) ) ? $array[ $variable ] : $default;
}

/**
 * Format date
 *
 * @param $time
 *
 * @return false|string
 */
function ct_ultimate_gdpr_date( $time ) {
	$format = apply_filters( 'ct_ultimate_gdpr_date_format', 'd M Y H:i:s' );

	return date( $format, $time );
}

/**
 * @param string $to
 * @param string $title
 * @param $service_id
 * @param string $salt
 * @param $url
 */
function ct_ultimate_gdpr_send_confirm_mail( $to, $title, $service_id, $salt, $url ) {
	$hash    = ct_ultimate_gdpr_hash( $to, $salt );

	$site_name = get_bloginfo('name');
	$site_email = get_bloginfo('admin_email');

	$url     = add_query_arg(
		array(
			'e' => $to,
			'h' => $hash,
			'i' => $service_id,
		),
		esc_url( $url )
	);

	$message = sprintf( esc_html__( 'To confirm, please follow this link: %s', 'ct-ultimate-gdpr' ), $url );
	$headers = array(
	    "From: $site_name <$site_email>"
    );

	wp_mail( $to, $title, $message, $headers );
}

/**
 * @param $email
 * @param $salt
 * @param $hash
 *
 * @return bool
 */
function ct_ultimate_gdpr_check_confirm_mail( $email, $salt, $hash ) {
	return ct_ultimate_gdpr_hash( $email, $salt ) === $hash;
}

/**
 * @param string $string
 * @param string $salt
 *
 * @return string
 */
function ct_ultimate_gdpr_hash( $string, $salt ) {
	return substr( sha1( $string . $salt ), 5, 10 );
}

/**
 * @param $path
 * @param bool $output
 * @param array $options view options
 *
 * @return string
 */
function ct_ultimate_gdpr_render_template( $path, $output = false, $options = array() ) {

	$options = apply_filters( 'ct_ultimate_gdpr_render_template_options', $options, $path );

	ob_start();
	include $path;
	$rendered = ob_get_clean();

	$rendered = CT_Ultimate_GDPR_Model_Placeholders::instance()->replace( $rendered );

	if ( $output ) {
		echo $rendered;
	}

	return $rendered;
}

/**
 * Returns the translated object ID(post_type or term) or original if missing
 *
 * @param $object_id integer|string|array The ID/s of the objects to check and return
 * @param string $type the object type: post, page, {custom post type name}, nav_menu, nav_menu_item, category, tag etc.
 *
 * @return string|array of object ids
 */
function ct_ultimate_gdpr_wpml_translate_id( $object_id, $type = 'page' ) {

	$current_language = apply_filters( 'wpml_current_language', null );

	if ( is_array( $object_id ) ) {

		// if array

		$translated_object_ids = array();

		foreach ( $object_id as $id ) {
			$translated_object_ids[] = apply_filters( 'wpml_object_id', $id, $type, true, $current_language );
		}

		return $translated_object_ids;

	} elseif ( is_string( $object_id ) ) {

		// if string

		// check if we have a comma separated ID string
		$is_comma_separated = strpos( $object_id, "," );

		if ( $is_comma_separated !== false ) {

			// explode the comma to create an array of IDs
			$object_id = explode( ',', $object_id );

			$translated_object_ids = array();

			foreach ( $object_id as $id ) {
				$translated_object_ids[] = apply_filters( 'wpml_object_id', $id, $type, true, $current_language );
			}

			// make sure the output is a comma separated string (the same way it came in!)
			return implode( ',', $translated_object_ids );

		} else {

			// if we don't find a comma in the string then this is a single ID
			return apply_filters( 'wpml_object_id', intval( $object_id ), $type, true, $current_language );

		}

	} else {

		// if int
		return apply_filters( 'wpml_object_id', $object_id, $type, true, $current_language );

	}
}

/**
 * Get all original, not translated posts
 *
 * @param array $args get_posts args
 *
 * @param bool $with_translated_title Whether to return only posts which can be translated to current language? Then return posts with title translated.
 *
 * @return array
 */
function ct_ultimate_gdpr_wpml_get_original_posts( $args, $with_translated_title = true ) {

	/** @var SitePress $sitepress */
	global $sitepress;

	// set default language
	if ( is_object( $sitepress ) && method_exists( $sitepress, 'get_default_language' ) ) {

		$default_language = $sitepress->get_default_language();
		$current_language = $sitepress->get_current_language();

		if ( ! empty( $default_language ) ) {
			$sitepress->switch_lang( $default_language );
		}

		$args['suppress_filters'] = false;

	}

	// get posts of default language
	$posts = get_posts( $args );

	// restore set language
	if (
		is_object( $sitepress ) &&
		method_exists( $sitepress, 'switch_lang' ) &&
		! empty( $current_language )
	) {
		$sitepress->switch_lang( $current_language ); //restore previous language

		if ( $with_translated_title ) {

			$translated_posts = array();

			foreach ( $posts as $key => $post ) {

				$translated_id = ct_ultimate_gdpr_wpml_translate_id( $post->ID );

				if ( $translated_id && $translated_id != $post->ID ) {

					$translated_post    = get_post( $translated_id );
					$post->post_title   = $translated_post->post_title;

				}

				$translated_posts[] = $post;

			}

			$posts = $translated_posts;

		}

	}

	return $posts;
}

/**
 * @return array
 */
function ct_ultimate_gpdr_get_default_post_types() {
	return apply_filters( 'ct_ultimate_gdpr_get_default_post_types', array( 'page' ) );
}

/**
 * @param $name
 * @param $value
 * @param $expire_time
 * @param string $path
 */
function ct_ultimate_gdpr_set_encoded_cookie( $name, $value, $expire_time, $path = '/' ) {
	$value = base64_encode( $value );
	setcookie( $name, $value, $expire_time, $path );
}

/**
 * @param $name
 *
 * @return bool|string
 */
function ct_ultimate_gdpr_get_encoded_cookie( $name ) {
	$value   = ct_ultimate_gdpr_get_value( $name, $_COOKIE );
	$decoded = base64_decode( $value );

	return $decoded ? $decoded : $value;
}

/**
 * Json encode helper
 *
 * @param $value
 *
 * @return mixed
 */
function ct_ultimate_gdpr_json_encode( $value ) {

	if ( version_compare( phpversion(), '5.4.0', '<' ) ) {

		$value = json_encode( $value );

	} else {

		$value = json_encode( $value, JSON_UNESCAPED_SLASHES );

	}

	return $value;
}

/**
 * @return string
 */
function ct_ultimate_gdpr_get_user_ip() {

	$client  = @$_SERVER['HTTP_CLIENT_IP'];
	$forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
	$remote  = $_SERVER['REMOTE_ADDR'];

	if ( filter_var( $client, FILTER_VALIDATE_IP ) ) {
		$ip = $client;
	} elseif ( filter_var( $forward, FILTER_VALIDATE_IP ) ) {
		$ip = $forward;
	} else {
		$ip = $remote;
	}

	return $ip;

}

/**
 * @return bool
 */
function ct_ultimate_gdpr_recaptcha_verify() {

	if ( $recaptcha_secret = CT_Ultimate_GDPR::instance()->get_admin_controller()->get_option_value( 'services_recaptcha_secret', '', CT_Ultimate_GDPR_Controller_Services::ID ) ) {

		$recaptcha_payload = ct_ultimate_gdpr_get_value( 'g-recaptcha-response', $_REQUEST );

		if ( ! $recaptcha_payload ) {
			return false;
		}

		$response = wp_remote_post(
			'https://www.google.com/recaptcha/api/siteverify',
			array(
				'body' => array(
					'response' => $recaptcha_payload,
					'secret'   => $recaptcha_secret,
					'remoteip' => ct_ultimate_gdpr_get_user_ip(),
				)
			)
		);

		if ( ! is_array( $response ) ) {
			return false;
		}

		$recaptcha_response = json_decode( $response['body'], true );

		if ( empty( $recaptcha_response['success'] ) ) {
			return false;
		}

	}

	return true;
}


/**
 * @return array
 */
function ct_ultimate_gdpr_get_font_icons() {

	return array(
		'fa-glass'                               => 'f000',
		'fa-music'                               => 'f001',
		'fa-search'                              => 'f002',
		'fa-envelope-o'                          => 'f003',
		'fa-heart'                               => 'f004',
		'fa-star'                                => 'f005',
		'fa-star-o'                              => 'f006',
		'fa-user'                                => 'f007',
		'fa-film'                                => 'f008',
		'fa-th-large'                            => 'f009',
		'fa-th'                                  => 'f00a',
		'fa-th-list'                             => 'f00b',
		'fa-check'                               => 'f00c',
		'fa-times'                               => 'f00d',
		'fa-search-plus'                         => 'f00e',
		'fa-search-minus'                        => 'f010',
		'fa-power-off'                           => 'f011',
		'fa-signal'                              => 'f012',
		'fa-cog'                                 => 'f013',
		'fa-trash-o'                             => 'f014',
		'fa-home'                                => 'f015',
		'fa-file-o'                              => 'f016',
		'fa-clock-o'                             => 'f017',
		'fa-road'                                => 'f018',
		'fa-download'                            => 'f019',
		'fa-arrow-circle-o-down'                 => 'f01a',
		'fa-arrow-circle-o-up'                   => 'f01b',
		'fa-inbox'                               => 'f01c',
		'fa-play-circle-o'                       => 'f01d',
		'fa-repeat'                              => 'f01e',
		'fa-refresh'                             => 'f021',
		'fa-list-alt'                            => 'f022',
		'fa-lock'                                => 'f023',
		'fa-flag'                                => 'f024',
		'fa-headphones'                          => 'f025',
		'fa-volume-off'                          => 'f026',
		'fa-volume-down'                         => 'f027',
		'fa-volume-up'                           => 'f028',
		'fa-qrcode'                              => 'f029',
		'fa-barcode'                             => 'f02a',
		'fa-tag'                                 => 'f02b',
		'fa-tags'                                => 'f02c',
		'fa-book'                                => 'f02d',
		'fa-bookmark'                            => 'f02e',
		'fa-print'                               => 'f02f',
		'fa-camera'                              => 'f030',
		'fa-font'                                => 'f031',
		'fa-bold'                                => 'f032',
		'fa-italic'                              => 'f033',
		'fa-text-height'                         => 'f034',
		'fa-text-width'                          => 'f035',
		'fa-align-left'                          => 'f036',
		'fa-align-center'                        => 'f037',
		'fa-align-right'                         => 'f038',
		'fa-align-justify'                       => 'f039',
		'fa-list'                                => 'f03a',
		'fa-outdent'                             => 'f03b',
		'fa-indent'                              => 'f03c',
		'fa-video-camera'                        => 'f03d',
		'fa-picture-o'                           => 'f03e',
		'fa-pencil'                              => 'f040',
		'fa-map-marker'                          => 'f041',
		'fa-adjust'                              => 'f042',
		'fa-tint'                                => 'f043',
		'fa-pencil-square-o'                     => 'f044',
		'fa-share-square-o'                      => 'f045',
		'fa-check-square-o'                      => 'f046',
		'fa-arrows'                              => 'f047',
		'fa-step-backward'                       => 'f048',
		'fa-fast-backward'                       => 'f049',
		'fa-backward'                            => 'f04a',
		'fa-play'                                => 'f04b',
		'fa-pause'                               => 'f04c',
		'fa-stop'                                => 'f04d',
		'fa-forward'                             => 'f04e',
		'fa-fast-forward'                        => 'f050',
		'fa-step-forward'                        => 'f051',
		'fa-eject'                               => 'f052',
		'fa-chevron-left'                        => 'f053',
		'fa-chevron-right'                       => 'f054',
		'fa-plus-circle'                         => 'f055',
		'fa-minus-circle'                        => 'f056',
		'fa-times-circle'                        => 'f057',
		'fa-check-circle'                        => 'f058',
		'fa-question-circle'                     => 'f059',
		'fa-info-circle'                         => 'f05a',
		'fa-crosshairs'                          => 'f05b',
		'fa-times-circle-o'                      => 'f05c',
		'fa-check-circle-o'                      => 'f05d',
		'fa-ban'                                 => 'f05e',
		'fa-arrow-left'                          => 'f060',
		'fa-arrow-right'                         => 'f061',
		'fa-arrow-up'                            => 'f062',
		'fa-arrow-down'                          => 'f063',
		'fa-share'                               => 'f064',
		'fa-expand'                              => 'f065',
		'fa-compress'                            => 'f066',
		'fa-plus'                                => 'f067',
		'fa-minus'                               => 'f068',
		'fa-asterisk'                            => 'f069',
		'fa-exclamation-circle'                  => 'f06a',
		'fa-gift'                                => 'f06b',
		'fa-leaf'                                => 'f06c',
		'fa-fire'                                => 'f06d',
		'fa-eye'                                 => 'f06e',
		'fa-eye-slash'                           => 'f070',
		'fa-exclamation-triangle'                => 'f071',
		'fa-plane'                               => 'f072',
		'fa-calendar'                            => 'f073',
		'fa-random'                              => 'f074',
		'fa-comment'                             => 'f075',
		'fa-magnet'                              => 'f076',
		'fa-chevron-up'                          => 'f077',
		'fa-chevron-down'                        => 'f078',
		'fa-retweet'                             => 'f079',
		'fa-shopping-cart'                       => 'f07a',
		'fa-folder'                              => 'f07b',
		'fa-folder-open'                         => 'f07c',
		'fa-arrows-v'                            => 'f07d',
		'fa-arrows-h'                            => 'f07e',
		'fa-bar-chart'                           => 'f080',
		'fa-twitter-square'                      => 'f081',
		'fa-facebook-square'                     => 'f082',
		'fa-camera-retro'                        => 'f083',
		'fa-key'                                 => 'f084',
		'fa-cogs'                                => 'f085',
		'fa-comments'                            => 'f086',
		'fa-thumbs-o-up'                         => 'f087',
		'fa-thumbs-o-down'                       => 'f088',
		'fa-star-half'                           => 'f089',
		'fa-heart-o'                             => 'f08a',
		'fa-sign-out'                            => 'f08b',
		'fa-linkedin-square'                     => 'f08c',
		'fa-thumb-tack'                          => 'f08d',
		'fa-external-link'                       => 'f08e',
		'fa-sign-in'                             => 'f090',
		'fa-trophy'                              => 'f091',
		'fa-github-square'                       => 'f092',
		'fa-upload'                              => 'f093',
		'fa-lemon-o'                             => 'f094',
		'fa-phone'                               => 'f095',
		'fa-square-o'                            => 'f096',
		'fa-bookmark-o'                          => 'f097',
		'fa-phone-square'                        => 'f098',
		'fa-twitter'                             => 'f099',
		'fa-facebook'                            => 'f09a',
		'fa-github'                              => 'f09b',
		'fa-unlock'                              => 'f09c',
		'fa-credit-card'                         => 'f09d',
		'fa-rss'                                 => 'f09e',
		'fa-hdd-o'                               => 'f0a0',
		'fa-bullhorn'                            => 'f0a1',
		'fa-bell'                                => 'f0f3',
		'fa-certificate'                         => 'f0a3',
		'fa-hand-o-right'                        => 'f0a4',
		'fa-hand-o-left'                         => 'f0a5',
		'fa-hand-o-up'                           => 'f0a6',
		'fa-hand-o-down'                         => 'f0a7',
		'fa-arrow-circle-left'                   => 'f0a8',
		'fa-arrow-circle-right'                  => 'f0a9',
		'fa-arrow-circle-up'                     => 'f0aa',
		'fa-arrow-circle-down'                   => 'f0ab',
		'fa-globe'                               => 'f0ac',
		'fa-wrench'                              => 'f0ad',
		'fa-tasks'                               => 'f0ae',
		'fa-filter'                              => 'f0b0',
		'fa-briefcase'                           => 'f0b1',
		'fa-arrows-alt'                          => 'f0b2',
		'fa-users'                               => 'f0c0',
		'fa-link'                                => 'f0c1',
		'fa-cloud'                               => 'f0c2',
		'fa-flask'                               => 'f0c3',
		'fa-scissors'                            => 'f0c4',
		'fa-files-o'                             => 'f0c5',
		'fa-paperclip'                           => 'f0c6',
		'fa-floppy-o'                            => 'f0c7',
		'fa-square'                              => 'f0c8',
		'fa-bars'                                => 'f0c9',
		'fa-list-ul'                             => 'f0ca',
		'fa-list-ol'                             => 'f0cb',
		'fa-strikethrough'                       => 'f0cc',
		'fa-underline'                           => 'f0cd',
		'fa-table'                               => 'f0ce',
		'fa-magic'                               => 'f0d0',
		'fa-truck'                               => 'f0d1',
		'fa-pinterest'                           => 'f0d2',
		'fa-pinterest-square'                    => 'f0d3',
		'fa-google-plus-square'                  => 'f0d4',
		'fa-google-plus'                         => 'f0d5',
		'fa-money'                               => 'f0d6',
		'fa-caret-down'                          => 'f0d7',
		'fa-caret-up'                            => 'f0d8',
		'fa-caret-left'                          => 'f0d9',
		'fa-caret-right'                         => 'f0da',
		'fa-columns'                             => 'f0db',
		'fa-sort'                                => 'f0dc',
		'fa-sort-desc'                           => 'f0dd',
		'fa-sort-asc'                            => 'f0de',
		'fa-envelope'                            => 'f0e0',
		'fa-linkedin'                            => 'f0e1',
		'fa-undo'                                => 'f0e2',
		'fa-gavel'                               => 'f0e3',
		'fa-tachometer'                          => 'f0e4',
		'fa-comment-o'                           => 'f0e5',
		'fa-comments-o'                          => 'f0e6',
		'fa-bolt'                                => 'f0e7',
		'fa-sitemap'                             => 'f0e8',
		'fa-umbrella'                            => 'f0e9',
		'fa-clipboard'                           => 'f0ea',
		'fa-lightbulb-o'                         => 'f0eb',
		'fa-exchange'                            => 'f0ec',
		'fa-cloud-download'                      => 'f0ed',
		'fa-cloud-upload'                        => 'f0ee',
		'fa-user-md'                             => 'f0f0',
		'fa-stethoscope'                         => 'f0f1',
		'fa-suitcase'                            => 'f0f2',
		'fa-bell-o'                              => 'f0a2',
		'fa-coffee'                              => 'f0f4',
		'fa-cutlery'                             => 'f0f5',
		'fa-file-text-o'                         => 'f0f6',
		'fa-building-o'                          => 'f0f7',
		'fa-hospital-o'                          => 'f0f8',
		'fa-ambulance'                           => 'f0f9',
		'fa-medkit'                              => 'f0fa',
		'fa-fighter-jet'                         => 'f0fb',
		'fa-beer'                                => 'f0fc',
		'fa-h-square'                            => 'f0fd',
		'fa-plus-square'                         => 'f0fe',
		'fa-angle-double-left'                   => 'f100',
		'fa-angle-double-right'                  => 'f101',
		'fa-angle-double-up'                     => 'f102',
		'fa-angle-double-down'                   => 'f103',
		'fa-angle-left'                          => 'f104',
		'fa-angle-right'                         => 'f105',
		'fa-angle-up'                            => 'f106',
		'fa-angle-down'                          => 'f107',
		'fa-desktop'                             => 'f108',
		'fa-laptop'                              => 'f109',
		'fa-tablet'                              => 'f10a',
		'fa-mobile'                              => 'f10b',
		'fa-circle-o'                            => 'f10c',
		'fa-quote-left'                          => 'f10d',
		'fa-quote-right'                         => 'f10e',
		'fa-spinner'                             => 'f110',
		'fa-circle'                              => 'f111',
		'fa-reply'                               => 'f112',
		'fa-github-alt'                          => 'f113',
		'fa-folder-o'                            => 'f114',
		'fa-folder-open-o'                       => 'f115',
		'fa-smile-o'                             => 'f118',
		'fa-frown-o'                             => 'f119',
		'fa-meh-o'                               => 'f11a',
		'fa-gamepad'                             => 'f11b',
		'fa-keyboard-o'                          => 'f11c',
		'fa-flag-o'                              => 'f11d',
		'fa-flag-checkered'                      => 'f11e',
		'fa-terminal'                            => 'f120',
		'fa-code'                                => 'f121',
		'fa-reply-all'                           => 'f122',
		'fa-star-half-o'                         => 'f123',
		'fa-location-arrow'                      => 'f124',
		'fa-crop'                                => 'f125',
		'fa-code-fork'                           => 'f126',
		'fa-chain-broken'                        => 'f127',
		'fa-question'                            => 'f128',
		'fa-info'                                => 'f129',
		'fa-exclamation'                         => 'f12a',
		'fa-superscript'                         => 'f12b',
		'fa-subscript'                           => 'f12c',
		'fa-eraser'                              => 'f12d',
		'fa-puzzle-piece'                        => 'f12e',
		'fa-microphone'                          => 'f130',
		'fa-microphone-slash'                    => 'f131',
		'fa-shield'                              => 'f132',
		'fa-calendar-o'                          => 'f133',
		'fa-fire-extinguisher'                   => 'f134',
		'fa-rocket'                              => 'f135',
		'fa-maxcdn'                              => 'f136',
		'fa-chevron-circle-left'                 => 'f137',
		'fa-chevron-circle-right'                => 'f138',
		'fa-chevron-circle-up'                   => 'f139',
		'fa-chevron-circle-down'                 => 'f13a',
		'fa-html5'                               => 'f13b',
		'fa-css3'                                => 'f13c',
		'fa-anchor'                              => 'f13d',
		'fa-unlock-alt'                          => 'f13e',
		'fa-bullseye'                            => 'f140',
		'fa-ellipsis-h'                          => 'f141',
		'fa-ellipsis-v'                          => 'f142',
		'fa-rss-square'                          => 'f143',
		'fa-play-circle'                         => 'f144',
		'fa-ticket'                              => 'f145',
		'fa-minus-square'                        => 'f146',
		'fa-minus-square-o'                      => 'f147',
		'fa-level-up'                            => 'f148',
		'fa-level-down'                          => 'f149',
		'fa-check-square'                        => 'f14a',
		'fa-pencil-square'                       => 'f14b',
		'fa-external-link-square'                => 'f14c',
		'fa-share-square'                        => 'f14d',
		'fa-compass'                             => 'f14e',
		'fa-caret-square-o-down'                 => 'f150',
		'fa-caret-square-o-up'                   => 'f151',
		'fa-caret-square-o-right'                => 'f152',
		'fa-eur'                                 => 'f153',
		'fa-gbp'                                 => 'f154',
		'fa-usd'                                 => 'f155',
		'fa-inr'                                 => 'f156',
		'fa-jpy'                                 => 'f157',
		'fa-rub'                                 => 'f158',
		'fa-krw'                                 => 'f159',
		'fa-btc'                                 => 'f15a',
		'fa-file'                                => 'f15b',
		'fa-file-text'                           => 'f15c',
		'fa-sort-alpha-asc'                      => 'f15d',
		'fa-sort-alpha-desc'                     => 'f15e',
		'fa-sort-amount-asc'                     => 'f160',
		'fa-sort-amount-desc'                    => 'f161',
		'fa-sort-numeric-asc'                    => 'f162',
		'fa-sort-numeric-desc'                   => 'f163',
		'fa-thumbs-up'                           => 'f164',
		'fa-thumbs-down'                         => 'f165',
		'fa-youtube-square'                      => 'f166',
		'fa-youtube'                             => 'f167',
		'fa-xing'                                => 'f168',
		'fa-xing-square'                         => 'f169',
		'fa-youtube-play'                        => 'f16a',
		'fa-dropbox'                             => 'f16b',
		'fa-stack-overflow'                      => 'f16c',
		'fa-instagram'                           => 'f16d',
		'fa-flickr'                              => 'f16e',
		'fa-adn'                                 => 'f170',
		'fa-bitbucket'                           => 'f171',
		'fa-bitbucket-square'                    => 'f172',
		'fa-tumblr'                              => 'f173',
		'fa-tumblr-square'                       => 'f174',
		'fa-long-arrow-down'                     => 'f175',
		'fa-long-arrow-up'                       => 'f176',
		'fa-long-arrow-left'                     => 'f177',
		'fa-long-arrow-right'                    => 'f178',
		'fa-apple'                               => 'f179',
		'fa-windows'                             => 'f17a',
		'fa-android'                             => 'f17b',
		'fa-linux'                               => 'f17c',
		'fa-dribbble'                            => 'f17d',
		'fa-skype'                               => 'f17e',
		'fa-foursquare'                          => 'f180',
		'fa-trello'                              => 'f181',
		'fa-female'                              => 'f182',
		'fa-male'                                => 'f183',
		'fa-gratipay'                            => 'f184',
		'fa-sun-o'                               => 'f185',
		'fa-moon-o'                              => 'f186',
		'fa-archive'                             => 'f187',
		'fa-bug'                                 => 'f188',
		'fa-vk'                                  => 'f189',
		'fa-weibo'                               => 'f18a',
		'fa-renren'                              => 'f18b',
		'fa-pagelines'                           => 'f18c',
		'fa-stack-exchange'                      => 'f18d',
		'fa-arrow-circle-o-right'                => 'f18e',
		'fa-arrow-circle-o-left'                 => 'f190',
		'fa-caret-square-o-left'                 => 'f191',
		'fa-dot-circle-o'                        => 'f192',
		'fa-wheelchair'                          => 'f193',
		'fa-vimeo-square'                        => 'f194',
		'fa-try'                                 => 'f195',
		'fa-plus-square-o'                       => 'f196',
		'fa-space-shuttle'                       => 'f197',
		'fa-slack'                               => 'f198',
		'fa-envelope-square'                     => 'f199',
		'fa-wordpress'                           => 'f19a',
		'fa-openid'                              => 'f19b',
		'fa-university'                          => 'f19c',
		'fa-graduation-cap'                      => 'f19d',
		'fa-yahoo'                               => 'f19e',
		'fa-google'                              => 'f1a0',
		'fa-reddit'                              => 'f1a1',
		'fa-reddit-square'                       => 'f1a2',
		'fa-stumbleupon-circle'                  => 'f1a3',
		'fa-stumbleupon'                         => 'f1a4',
		'fa-delicious'                           => 'f1a5',
		'fa-digg'                                => 'f1a6',
		'fa-pied-piper-pp'                       => 'f1a7',
		'fa-pied-piper-alt'                      => 'f1a8',
		'fa-drupal'                              => 'f1a9',
		'fa-joomla'                              => 'f1aa',
		'fa-language'                            => 'f1ab',
		'fa-fax'                                 => 'f1ac',
		'fa-building'                            => 'f1ad',
		'fa-child'                               => 'f1ae',
		'fa-paw'                                 => 'f1b0',
		'fa-spoon'                               => 'f1b1',
		'fa-cube'                                => 'f1b2',
		'fa-cubes'                               => 'f1b3',
		'fa-behance'                             => 'f1b4',
		'fa-behance-square'                      => 'f1b5',
		'fa-steam'                               => 'f1b6',
		'fa-steam-square'                        => 'f1b7',
		'fa-recycle'                             => 'f1b8',
		'fa-car'                                 => 'f1b9',
		'fa-taxi'                                => 'f1ba',
		'fa-tree'                                => 'f1bb',
		'fa-spotify'                             => 'f1bc',
		'fa-deviantart'                          => 'f1bd',
		'fa-soundcloud'                          => 'f1be',
		'fa-database'                            => 'f1c0',
		'fa-file-pdf-o'                          => 'f1c1',
		'fa-file-word-o'                         => 'f1c2',
		'fa-file-excel-o'                        => 'f1c3',
		'fa-file-powerpoint-o'                   => 'f1c4',
		'fa-file-image-o'                        => 'f1c5',
		'fa-file-archive-o'                      => 'f1c6',
		'fa-file-audio-o'                        => 'f1c7',
		'fa-file-video-o'                        => 'f1c8',
		'fa-file-code-o'                         => 'f1c9',
		'fa-vine'                                => 'f1ca',
		'fa-codepen'                             => 'f1cb',
		'fa-jsfiddle'                            => 'f1cc',
		'fa-life-ring'                           => 'f1cd',
		'fa-circle-o-notch'                      => 'f1ce',
		'fa-rebel'                               => 'f1d0',
		'fa-empire'                              => 'f1d1',
		'fa-git-square'                          => 'f1d2',
		'fa-git'                                 => 'f1d3',
		'fa-hacker-news'                         => 'f1d4',
		'fa-tencent-weibo'                       => 'f1d5',
		'fa-qq'                                  => 'f1d6',
		'fa-weixin'                              => 'f1d7',
		'fa-paper-plane'                         => 'f1d8',
		'fa-paper-plane-o'                       => 'f1d9',
		'fa-history'                             => 'f1da',
		'fa-circle-thin'                         => 'f1db',
		'fa-header'                              => 'f1dc',
		'fa-paragraph'                           => 'f1dd',
		'fa-sliders'                             => 'f1de',
		'fa-share-alt'                           => 'f1e0',
		'fa-share-alt-square'                    => 'f1e1',
		'fa-bomb'                                => 'f1e2',
		'fa-futbol-o'                            => 'f1e3',
		'fa-tty'                                 => 'f1e4',
		'fa-binoculars'                          => 'f1e5',
		'fa-plug'                                => 'f1e6',
		'fa-slideshare'                          => 'f1e7',
		'fa-twitch'                              => 'f1e8',
		'fa-yelp'                                => 'f1e9',
		'fa-newspaper-o'                         => 'f1ea',
		'fa-wifi'                                => 'f1eb',
		'fa-calculator'                          => 'f1ec',
		'fa-paypal'                              => 'f1ed',
		'fa-google-wallet'                       => 'f1ee',
		'fa-cc-visa'                             => 'f1f0',
		'fa-cc-mastercard'                       => 'f1f1',
		'fa-cc-discover'                         => 'f1f2',
		'fa-cc-amex'                             => 'f1f3',
		'fa-cc-paypal'                           => 'f1f4',
		'fa-cc-stripe'                           => 'f1f5',
		'fa-bell-slash'                          => 'f1f6',
		'fa-bell-slash-o'                        => 'f1f7',
		'fa-trash'                               => 'f1f8',
		'fa-copyright'                           => 'f1f9',
		'fa-at'                                  => 'f1fa',
		'fa-eyedropper'                          => 'f1fb',
		'fa-paint-brush'                         => 'f1fc',
		'fa-birthday-cake'                       => 'f1fd',
		'fa-area-chart'                          => 'f1fe',
		'fa-pie-chart'                           => 'f200',
		'fa-line-chart'                          => 'f201',
		'fa-lastfm'                              => 'f202',
		'fa-lastfm-square'                       => 'f203',
		'fa-toggle-off'                          => 'f204',
		'fa-toggle-on'                           => 'f205',
		'fa-bicycle'                             => 'f206',
		'fa-bus'                                 => 'f207',
		'fa-ioxhost'                             => 'f208',
		'fa-angellist'                           => 'f209',
		'fa-cc'                                  => 'f20a',
		'fa-ils'                                 => 'f20b',
		'fa-meanpath'                            => 'f20c',
		'fa-buysellads'                          => 'f20d',
		'fa-connectdevelop'                      => 'f20e',
		'fa-dashcube'                            => 'f210',
		'fa-forumbee'                            => 'f211',
		'fa-leanpub'                             => 'f212',
		'fa-sellsy'                              => 'f213',
		'fa-shirtsinbulk'                        => 'f214',
		'fa-simplybuilt'                         => 'f215',
		'fa-skyatlas'                            => 'f216',
		'fa-cart-plus'                           => 'f217',
		'fa-cart-arrow-down'                     => 'f218',
		'fa-diamond'                             => 'f219',
		'fa-ship'                                => 'f21a',
		'fa-user-secret'                         => 'f21b',
		'fa-motorcycle'                          => 'f21c',
		'fa-street-view'                         => 'f21d',
		'fa-heartbeat'                           => 'f21e',
		'fa-venus'                               => 'f221',
		'fa-mars'                                => 'f222',
		'fa-mercury'                             => 'f223',
		'fa-transgender'                         => 'f224',
		'fa-transgender-alt'                     => 'f225',
		'fa-venus-double'                        => 'f226',
		'fa-mars-double'                         => 'f227',
		'fa-venus-mars'                          => 'f228',
		'fa-mars-stroke'                         => 'f229',
		'fa-mars-stroke-v'                       => 'f22a',
		'fa-mars-stroke-h'                       => 'f22b',
		'fa-neuter'                              => 'f22c',
		'fa-genderless'                          => 'f22d',
		'fa-facebook-official'                   => 'f230',
		'fa-pinterest-p'                         => 'f231',
		'fa-whatsapp'                            => 'f232',
		'fa-server'                              => 'f233',
		'fa-user-plus'                           => 'f234',
		'fa-user-times'                          => 'f235',
		'fa-bed'                                 => 'f236',
		'fa-viacoin'                             => 'f237',
		'fa-train'                               => 'f238',
		'fa-subway'                              => 'f239',
		'fa-medium'                              => 'f23a',
		'fa-y-combinator'                        => 'f23b',
		'fa-optin-monster'                       => 'f23c',
		'fa-opencart'                            => 'f23d',
		'fa-expeditedssl'                        => 'f23e',
		'fa-battery-full'                        => 'f240',
		'fa-battery-three-quarters'              => 'f241',
		'fa-battery-half'                        => 'f242',
		'fa-battery-quarter'                     => 'f243',
		'fa-battery-empty'                       => 'f244',
		'fa-mouse-pointer'                       => 'f245',
		'fa-i-cursor'                            => 'f246',
		'fa-object-group'                        => 'f247',
		'fa-object-ungroup'                      => 'f248',
		'fa-sticky-note'                         => 'f249',
		'fa-sticky-note-o'                       => 'f24a',
		'fa-cc-jcb'                              => 'f24b',
		'fa-cc-diners-club'                      => 'f24c',
		'fa-clone'                               => 'f24d',
		'fa-balance-scale'                       => 'f24e',
		'fa-hourglass-o'                         => 'f250',
		'fa-hourglass-start'                     => 'f251',
		'fa-hourglass-half'                      => 'f252',
		'fa-hourglass-end'                       => 'f253',
		'fa-hourglass'                           => 'f254',
		'fa-hand-rock-o'                         => 'f255',
		'fa-hand-paper-o'                        => 'f256',
		'fa-hand-scissors-o'                     => 'f257',
		'fa-hand-lizard-o'                       => 'f258',
		'fa-hand-spock-o'                        => 'f259',
		'fa-hand-pointer-o'                      => 'f25a',
		'fa-hand-peace-o'                        => 'f25b',
		'fa-trademark'                           => 'f25c',
		'fa-registered'                          => 'f25d',
		'fa-creative-commons'                    => 'f25e',
		'fa-gg'                                  => 'f260',
		'fa-gg-circle'                           => 'f261',
		'fa-tripadvisor'                         => 'f262',
		'fa-odnoklassniki'                       => 'f263',
		'fa-odnoklassniki-square'                => 'f264',
		'fa-get-pocket'                          => 'f265',
		'fa-wikipedia-w'                         => 'f266',
		'fa-safari'                              => 'f267',
		'fa-chrome'                              => 'f268',
		'fa-firefox'                             => 'f269',
		'fa-opera'                               => 'f26a',
		'fa-internet-explorer'                   => 'f26b',
		'fa-television'                          => 'f26c',
		'fa-contao'                              => 'f26d',
		'fa-500px'                               => 'f26e',
		'fa-amazon'                              => 'f270',
		'fa-calendar-plus-o'                     => 'f271',
		'fa-calendar-minus-o'                    => 'f272',
		'fa-calendar-times-o'                    => 'f273',
		'fa-calendar-check-o'                    => 'f274',
		'fa-industry'                            => 'f275',
		'fa-map-pin'                             => 'f276',
		'fa-map-signs'                           => 'f277',
		'fa-map-o'                               => 'f278',
		'fa-map'                                 => 'f279',
		'fa-commenting'                          => 'f27a',
		'fa-commenting-o'                        => 'f27b',
		'fa-houzz'                               => 'f27c',
		'fa-vimeo'                               => 'f27d',
		'fa-black-tie'                           => 'f27e',
		'fa-fonticons'                           => 'f280',
		'fa-reddit-alien'                        => 'f281',
		'fa-edge'                                => 'f282',
		'fa-credit-card-alt'                     => 'f283',
		'fa-codiepie'                            => 'f284',
		'fa-modx'                                => 'f285',
		'fa-fort-awesome'                        => 'f286',
		'fa-usb'                                 => 'f287',
		'fa-product-hunt'                        => 'f288',
		'fa-mixcloud'                            => 'f289',
		'fa-scribd'                              => 'f28a',
		'fa-pause-circle'                        => 'f28b',
		'fa-pause-circle-o'                      => 'f28c',
		'fa-stop-circle'                         => 'f28d',
		'fa-stop-circle-o'                       => 'f28e',
		'fa-shopping-bag'                        => 'f290',
		'fa-shopping-basket'                     => 'f291',
		'fa-hashtag'                             => 'f292',
		'fa-bluetooth'                           => 'f293',
		'fa-bluetooth-b'                         => 'f294',
		'fa-percent'                             => 'f295',
		'fa-gitlab'                              => 'f296',
		'fa-wpbeginner'                          => 'f297',
		'fa-wpforms'                             => 'f298',
		'fa-envira'                              => 'f299',
		'fa-universal-access'                    => 'f29a',
		'fa-wheelchair-alt'                      => 'f29b',
		'fa-question-circle-o'                   => 'f29c',
		'fa-blind'                               => 'f29d',
		'fa-audio-description'                   => 'f29e',
		'fa-volume-control-phone'                => 'f2a0',
		'fa-braille'                             => 'f2a1',
		'fa-assistive-listening-systems'         => 'f2a2',
		'fa-american-sign-language-interpreting' => 'f2a3',
		'fa-deaf'                                => 'f2a4',
		'fa-glide'                               => 'f2a5',
		'fa-glide-g'                             => 'f2a6',
		'fa-sign-language'                       => 'f2a7',
		'fa-low-vision'                          => 'f2a8',
		'fa-viadeo'                              => 'f2a9',
		'fa-viadeo-square'                       => 'f2aa',
		'fa-snapchat'                            => 'f2ab',
		'fa-snapchat-ghost'                      => 'f2ac',
		'fa-snapchat-square'                     => 'f2ad',
		'fa-pied-piper'                          => 'f2ae',
		'fa-first-order'                         => 'f2b0',
		'fa-yoast'                               => 'f2b1',
		'fa-themeisle'                           => 'f2b2',
		'fa-google-plus-official'                => 'f2b3',
		'fa-font-awesome'                        => 'f2b4'
	);

}

/**
 * CT Modified
 *
 * Prints out all settings sections added to a particular settings page
 *
 * Modified Part of the Settings API. Use this in a settings page callback function
 * to output all the sections and fields that were added to that $page with
 * add_settings_section() and add_settings_field()
 *
 * @global $wp_settings_sections Storage array of all settings sections added to admin pages
 * @global $wp_settings_fields Storage array of settings fields and info about their pages/sections
 * @since 2.7.0
 *
 * @param string $page The slug name of the page whose settings sections you want to output
 */
function ct_ultimate_gdpr_do_settings_sections( $page ) {

	global $wp_settings_sections, $wp_settings_fields;

	$last_tab        = '';
	$accordion_count = 0;

	if ( ! isset( $wp_settings_sections[ $page ] ) ) {
		return;
	}

	foreach ( (array) $wp_settings_sections[ $page ] as $section ) {

		if ( $section['callback'] ) {
			call_user_func( $section['callback'], $section );
		}

		if ( ! isset( $wp_settings_fields ) || ! isset( $wp_settings_fields[ $page ] ) || ! isset( $wp_settings_fields[ $page ][ $section['id'] ] ) ) {
			continue;
		}

		$id_tab       = '';
		$id_section   = '';
		$id_accordion = '';

		// get tab and section number
		$id_parts = explode( '_', $section['id'] );

		foreach ( $id_parts as $id_part ) {

			$attribute_parts = explode( '-', $id_part );

			if ( current( $attribute_parts ) == 'tab' ) {
				$id_tab = end( $attribute_parts );
			}

			if ( current( $attribute_parts ) == 'section' ) {
				$id_section = end( $attribute_parts );
			}

			if ( current( $attribute_parts ) == 'accordion' ) {
				$id_accordion = end( $attribute_parts );
				$accordion_count ++;
			}

		}


		if ( $last_tab && $last_tab != $id_tab ) {
			echo "</div>";
		}

		if ( $id_tab && $id_tab != $last_tab ) {
			echo "<div class='ct-ultimate-gdpr-wrap ct-clearfix ct-tab-{$id_tab} ct-ultimate-gdpr-width card-columns'>";
		}
		echo "<div class='card ct-ultimate-gdpr-inner-wrap ct-ultimate-gdpr-half-width ct-ultimate-gdpr-no-pad'>";

		$table_class = $id_section ? "form-table section-{$id_section}" : 'form-table';

		if ( $id_accordion ) {
			echo "<div class='card-header' id='heading$id_accordion'>";
			echo "<h5 class='mb-0'>";

			if ( $accordion_count == 1 ) {
				echo "<button class='btn btn-link' data-toggle='collapse' data-target='#accordion-tab-$id_accordion' aria-expanded='true' aria-controls='accordion-tab-$id_accordion'>";
			} else {
				echo "<button class='btn btn-link collapsed' data-toggle='collapse' data-target='#accordion-tab-$id_accordion' aria-expanded='true' aria-controls='accordion-tab-$id_accordion'>";
			}

			echo isset( $section['title'] ) ? " {$section['title']} " : '';
			echo "</button></h5></div>";

			if ( $accordion_count == 1 ) {
				echo "<div id='accordion-tab-$id_accordion' class='collapse show' aria-labelledby='heading$id_accordion' data-parent='#accordion'>";
			} else {
				echo "<div id='accordion-tab-$id_accordion' class='collapse' aria-labelledby='heading$id_accordion' data-parent='#accordion'>";
			}

			echo "<div class='card-body'>";

		} else {

			if ( $section['title'] ) {
				echo "<h2 class='card-header'>{$section['title']}</h2>\n";
			}

		}

		echo "<table class='$table_class'>";

		foreach ( (array) $wp_settings_fields[ $page ][ $section['id'] ] as $field ) {

			$class = '';

			if ( ! empty( $field['args']['class'] ) ) {
				$class = ' class="' . esc_attr( $field['args']['class'] ) . '"';
			}

			echo "<tr{$class}>";
			if ( ! empty( $field['args']['label_for'] ) ) {
				echo '<th scope="row"><label for="' . esc_attr( $field['args']['label_for'] ) . '">' . $field['title'] . '</label>';
			} else {
				echo '<th scope="row">' . $field['title'];
			}

			if ( ! empty( $field['args']['hint'] ) ) {
				echo'<span class="ct-ultimate-gdpr-hint" title="'.  $field['args']['hint'] .'"> <i class="fa fa-question-circle"></i></span>';
			}

			echo '</th>';
			echo '<td>';
			call_user_func( $field['callback'], $field['args'] );
			echo '</td>';
			echo '</tr>';


		}

		echo '</table>';

		$last_tab = $id_tab;

		if ( $id_accordion ) {
			echo "</div></div>";
		}

		echo "</div>";

	}

	// close last tab div
	if ( $last_tab ) {
		echo "</div>";
	}

}

/** Get plugin version
 * @return bool|mixed
 */
function ct_ultimate_gdpr_get_plugin_version() {

	$plugin_version_array = get_file_data( dirname( __DIR__ ) . '/ct-ultimate-gdpr.php', array( 'Version' ), 'plugin' );

	return ct_ultimate_gdpr_get_value( 0, $plugin_version_array, '' );
}

/**
 * @return bool
 */
function ct_ultimate_gdpr_is_doing_cli() {
	return ( php_sapi_name() === 'cli' || defined( 'STDIN' ) );
}

/**
 * Get user IP if settings allow that
 *
 * @return string
 */
function ct_ultimate_gdpr_get_permitted_user_ip() {

	$permitted = ! ! CT_Ultimate_GDPR::instance()
	                                 ->get_admin_controller()
	                                 ->get_option_value( 'services_logger_pseudonymized_ip', false, CT_Ultimate_GDPR_Controller_Services::ID );

	$permitted |= CT_Ultimate_GDPR::instance()
	                              ->get_controller_by_id( CT_Ultimate_GDPR_Controller_Policy::ID )
	                              ->is_consent_valid();

	return $permitted ? ct_ultimate_gdpr_get_user_ip() : '';

}

/**
 * Get user agent if settings allow that
 *
 * @return string
 */
function ct_ultimate_gdpr_get_permitted_user_agent() {

	$permitted = ! ! CT_Ultimate_GDPR::instance()
	                                 ->get_admin_controller()
	                                 ->get_option_value( 'services_logger_pseudonymized_user_agent', false, CT_Ultimate_GDPR_Controller_Services::ID );

	$permitted |= CT_Ultimate_GDPR::instance()
	                              ->get_controller_by_id( CT_Ultimate_GDPR_Controller_Policy::ID )
	                              ->is_consent_valid();

	return $permitted ? $_SERVER['HTTP_USER_AGENT'] : '';

}

/**
 * Timestamp for expiry date
 */
function ct_ultimate_gdpr_is_timestamp($string)
{
    try {
        new DateTime('@' . $string);
    } catch(Exception $e) {
        return false;
    }
    return true;
}

/**
 * Check if cookies are accepted.
 *
 * @return bool
 */
if ( ! function_exists( 'gdpr_cookies_accepted' ) ) {
    function gdpr_cookies_accepted()
    {
        // Check cookie if set
        $cookie_level =   isset( $_COOKIE['ct-ultimate-gdpr-cookie-level']) ? 1 : 0;
        if($cookie_level){
            return true;
        }
        return false;
    }
}