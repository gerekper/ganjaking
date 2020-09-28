<?php

if ( ! function_exists( 'porto_add_url_parameters' ) ) :

	function porto_add_url_parameters( $url, $name, $value ) {

		$url_data = parse_url( str_replace( '#038;', '&', $url ) );
		if ( ! isset( $url_data['query'] ) ) {
			$url_data['query'] = '';
		}
		$params = array();
		parse_str( $url_data['query'], $params );
		$params[ $name ]   = $value;
		$url_data['query'] = http_build_query( $params );
		return porto_build_url( $url_data );
	}
endif;


if ( ! function_exists( 'porto_remove_url_parameters' ) ) :

	function porto_remove_url_parameters( $url, $name ) {

		$url_data = parse_url( str_replace( '#038;', '&', $url ) );

		if ( ! isset( $url_data['query'] ) ) {
			$url_data['query'] = '';
		}

		$params = array();

		parse_str( $url_data['query'], $params );

		$params[ $name ] = '';

		$url_data['query'] = http_build_query( $params );

		return porto_build_url( $url_data );
	}

endif;


if ( ! function_exists( 'porto_build_url' ) ) :

	function porto_build_url( $url_data ) {

		$url = '';

		if ( isset( $url_data['host'] ) ) {

			$url .= $url_data['scheme'] . '://';

			if ( isset( $url_data['user'] ) ) {

				$url .= $url_data['user'];

				if ( isset( $url_data['pass'] ) ) {

					$url .= ':' . $url_data['pass'];
				}

				$url .= '@';

			}

			$url .= $url_data['host'];

			if ( isset( $url_data['port'] ) ) {

				$url .= ':' . $url_data['port'];
			}
		}

		if ( isset( $url_data['path'] ) ) {

			$url .= $url_data['path'];
		}

		if ( isset( $url_data['query'] ) ) {

			$url .= '?' . $url_data['query'];
		}

		if ( isset( $url_data['fragment'] ) ) {

			$url .= '#' . $url_data['fragment'];
		}

		return str_replace( '#038;', '&', $url );
	}

endif;

function porto_get_blank_image() {
	return 'data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==';
}

if ( ! function_exists( 'array2json' ) ) :

	function array2json( $arr ) {

		if ( function_exists( 'json_encode' ) ) {
			return json_encode( $arr ); //Lastest versions of PHP already has this functionality.
		}

		$parts   = array();
		$is_list = false;

		//Find out if the given array is a numerical array
		$keys       = array_keys( $arr );
		$max_length = count( $arr ) - 1;

		if ( ( 0 == $keys[0] ) and ( $keys[ $max_length ] == $max_length ) ) { //See if the first key is 0 and last key is length - 1

			$is_list = true;
			for ( $i = 0; $i < count( $keys ); $i++ ) { //See if each key correspondes to its position

				if ( $i != $keys[ $i ] ) { //A key fails at position check.
					$is_list = false; //It is an associative array.
					break;
				}
			}
		}

		foreach ( $arr as $key => $value ) {

			if ( is_array( $value ) ) { //Custom handling for arrays

				if ( $is_list ) {
					$parts[] = array2json( $value ); /* :RECURSION: */

				} else {
					$parts[] = '"' . $key . '":' . array2json( $value ); /* :RECURSION: */
				}
			} else {
				$str = '';
				if ( ! $is_list ) {
					$str = '"' . $key . '":';
				}

				// Custom handling for multiple data types

				if ( is_numeric( $value ) ) {
					$str .= $value; //Numbers
				} elseif ( false === $value ) {
					$str .= 'false'; //The booleans
				} elseif ( true === $value ) {
					$str .= 'true';
				} else {
					$str .= '"' . addslashes( $value ) . '"'; //All other things
				}

				$parts[] = $str;
			}
		}

		$json = implode( ',', $parts );

		if ( $is_list ) {
			return '[' . $json . ']';//Return numerical JSON
		}

		return '{' . $json . '}';//Return associative JSON
	}

endif;

if ( ! function_exists( 'porto_generate_rand' ) ) :
	function porto_generate_rand() {

		$valid_characters = 'abcdefghijklmnopqrstuvwxyz0123456789';
		$rand             = '';
		$length           = 32;
		for ( $n = 1; $n < $length; $n++ ) {

			$which_character = rand( 0, strlen( $valid_characters ) - 1 );
			$rand           .= $valid_characters{$which_character};
		}

		return $rand;
	}
endif;

if ( ! function_exists( 'porto_is_ajax' ) ) :

	function porto_is_ajax() {

		global $porto_is_ajax;

		if ( is_bool( $porto_is_ajax ) ) {
			return $porto_is_ajax;
		}

		$porto_is_ajax = false;
		if ( wp_doing_ajax() ) {
			$porto_is_ajax = true;
		} elseif ( isset( $_REQUEST['portoajax'] ) && $_REQUEST['portoajax'] ) {
			$porto_is_ajax = true;
		} elseif ( function_exists( 'porto_shortcode_is_ajax' ) ) {
			$porto_is_ajax = porto_shortcode_is_ajax();
		}

		return $porto_is_ajax;
	}
endif;

if ( ! function_exists( 'porto_stringify_attributes' ) ) :
	function porto_stringify_attributes( $attributes ) {

		$atts = array();
		foreach ( $attributes as $name => $value ) {
			$atts[] = $name . '="' . esc_attr( $value ) . '"';
		}

		return implode( ' ', $atts );
	}
endif;

function porto_has_class( $class, $classes ) {
	return in_array( $class, explode( ' ', strtolower( $classes ) ) );
}

function porto_strip_tags( $content ) {

	$content = str_replace( ']]>', ']]&gt;', $content );
	$content = preg_replace( '/<script.*?\/script>/s', '', $content ) ? : $content;
	$content = preg_replace( '/<style.*?\/style>/s', '', $content ) ? : $content;
	$content = strip_tags( $content );
	return $content;
}

if ( ! function_exists( 'porto_strip_script_tags' ) ) :
	function porto_strip_script_tags( $content ) {
		$content = str_replace( ']]>', ']]&gt;', $content );
		$content = preg_replace( '/<script.*?\/script>/s', '', $content ) ? : $content;
		$content = preg_replace( '/<style.*?\/style>/s', '', $content ) ? : $content;
		return $content;
	}
endif;

if ( ! function_exists( 'porto_filter_output' ) ) :
	function porto_filter_output( $output_escaped ) {
		return $output_escaped;
	}
endif;

if ( ! function_exists( 'porto_sanitize_array' ) ) :
	function porto_sanitize_array( $arr ) {
		if ( $arr && is_array( $arr ) ) {
			foreach ( $arr as $index => $a ) {
				if ( is_array( $a ) ) {
					$arr[ $index ] = porto_sanitize_array( $a );
				} else {
					$arr[ $index ] = sanitize_text_field( $a );
				}
			}
			return $arr;
		} elseif ( $arr ) {
			return sanitize_text_field( $arr );
		}
		return false;
	}
endif;
/**
 * Modifies WordPress's built-in comments_popup_link() function to return a string instead of echo comment results
 */
function get_comments_popup_link( $zero = false, $one = false, $more = false, $css_class = '', $none = false ) {
	global $wpcommentspopupfile, $wpcommentsjavascript;

	$id = get_the_ID();

	if ( false === $zero ) {
		$zero = esc_html__( 'No Comments', 'porto' );
	}
	if ( false === $one ) {
		$one = esc_html__( '1 Comment', 'porto' );
	}
	if ( false === $more ) {
		$more = esc_html__( '% Comments', 'porto' );
	}
	if ( false === $none ) {
		$none = esc_html__( 'Comments Off', 'porto' );
	}

	$number = get_comments_number( $id );

	$str = '';

	if ( 0 == $number && ! comments_open() && ! pings_open() ) {
		$str = '<span' . ( ( ! empty( $css_class ) ) ? ' class="' . esc_attr( $css_class ) . '"' : '' ) . '>' . $none . '</span>';
		return $str;
	}

	if ( post_password_required() ) {
		$str = esc_html__( 'Enter your password to view comments.', 'porto' );
		return $str;
	}

	$str = '<a href="';
	if ( $wpcommentsjavascript ) {
		if ( empty( $wpcommentspopupfile ) ) {
			$home = home_url();
		} else {
			$home = get_option( 'siteurl' );
		}
		$str .= esc_url( $home ) . '/' . $wpcommentspopupfile . '?comments_popup=' . $id;
		$str .= '" onclick="wpopen(this.href); return false"';
	} else { // if comments_popup_script() is not in the template, display simple comment link
		if ( 0 == $number ) {
			$str .= esc_url( get_permalink() ) . '#respond';
		} else {
			$str .= esc_url( get_comments_link() );
		}
		$str .= '"';
	}

	if ( ! empty( $css_class ) ) {
		$str .= ' class="' . esc_attr( $css_class ) . '" ';
	}
	$title = the_title_attribute( array( 'echo' => 0 ) );

	$str .= apply_filters( 'comments_popup_link_attributes', '' );

	/* translators: %s: Title */
	$str .= ' title="' . esc_attr( sprintf( __( 'Comment on %s', 'porto' ), $title ) ) . '">';
	$str .= get_comments_number_str( $zero, $one, $more );
	$str .= '</a>';

	return $str;
}

/**
 * Modifies WordPress's built-in comments_number() function to return string instead of echo
 */
function get_comments_number_str( $zero = false, $one = false, $more = false, $deprecated = '' ) {
	if ( ! empty( $deprecated ) ) {
		_deprecated_argument( __FUNCTION__, '1.3' );
	}

	$number = get_comments_number();

	if ( $number > 1 ) {
		$output = str_replace( '%', number_format_i18n( $number ), ( false === $more ) ? esc_html__( '% Comments', 'porto' ) : $more );
	} elseif ( 0 == $number ) {
		$output = ( false === $zero ) ? esc_html__( 'No Comments', 'porto' ) : $zero;
	} else { // must be one
		$output = ( false === $one ) ? esc_html__( '1 Comment', 'porto' ) : $one;
	}

	return apply_filters( 'comments_number', $output, $number );
}

/**
 * Nextend Facebook Login plugin
 */
if ( ! function_exists( 'porto_nextend_facebook_login' ) ) {
	function porto_nextend_facebook_login() {
		if ( class_exists( 'NextendSocialLogin', false ) ) {
			return NextendSocialLogin::isProviderEnabled( 'facebook' );
		}
		return defined( 'NEW_FB_LOGIN' );
	}
}

if ( ! function_exists( 'porto_nextend_google_login' ) ) {
	function porto_nextend_google_login() {
		if ( class_exists( 'NextendSocialLogin', false ) ) {
			return NextendSocialLogin::isProviderEnabled( 'google' );
		}
		return defined( 'NEW_GOOGLE_LOGIN' );
	}
}

if ( ! function_exists( 'porto_nextend_twitter_login' ) ) {
	function porto_nextend_twitter_login() {
		if ( class_exists( 'NextendSocialLogin', false ) ) {
			return NextendSocialLogin::isProviderEnabled( 'twitter' );
		}
		return defined( 'NEW_TWITTER_LOGIN' );
	}
}


// Woocommerce Vendor Start
if ( class_exists( 'WC_Vendors' ) ) :
	function porto_wc_vendor_header() {

		global $porto_settings, $post, $wp_query,$vendor_shop;
		$vendor_id = WCV_Vendors::get_vendor_id( $vendor_shop );
		$shop_name = get_user_meta( $vendor_id, 'pv_shop_name', true );
		if ( $vendor_shop ) {
			if ( $porto_settings['porto_wcvendors_shop_description'] ) {
				$product_id = get_the_ID();
				$author     = WCV_Vendors::get_vendor_from_product( $product_id );
				$link       = WCV_Vendors::get_vendor_shop_page( $author );
				$author     = WCV_Vendors::get_vendor_from_product( get_the_ID() );
				$user       = get_userdata( $author );

				if ( $user ) {
					$r = get_user_meta( $user->ID, 'picture', true );
				}
				if ( isset( $r ) && isset( $r['url'] ) ) {
					$r = $r['url']; ?>
					<div class="vendor-profile-bg" style="background:url('<?php echo esc_url( $r ); ?>') ;background-size:cover">
				<?php } else { ?>
					<div class="vendor-profile-bg">
				<?php } ?>
						<div class="overlay-vendor-effect">
						<?php if ( $porto_settings['porto_wcvendors_shop_avatar'] ) { ?>
							<div class="vendor_userimg">
								<div class="profile-img">
									<a href="<?php echo esc_url( $link ); ?>"> <?php echo get_avatar( $vendor_id, 80 ); ?></a>
								</div>
							</div>
						<?php } ?>
							<h1><a href="<?php echo esc_url( $link ); ?>"><?php echo esc_html( $shop_name ); ?></a></h1>
							<div class="custom_shop_description">
								<?php echo do_shortcode( get_user_meta( $vendor_id, 'pv_shop_description', true ) ); ?>
							</div>
						<?php
							$author = WCV_Vendors::get_vendor_from_product( get_the_ID() );
							$user   = get_userdata( $author );
						if ( $porto_settings['porto_wcvendors_shop_profile'] ) {
							if ( $porto_settings['porto_wcvendors_phone'] ) {
								if ( isset( $user->phone_number ) && $user->phone_number ) {
									?>
									<span class="vendorcustom-mail"><i class="fas fa-phone aligmentvendor"></i> &nbsp;<?php echo esc_html( $user->phone_number ); ?></span>
									<?php
								}
							}
							?>
							&nbsp;&nbsp;
							<?php if ( $porto_settings['porto_wcvendors_email'] ) { ?>
								<?php if ( isset( $user->user_email ) && $user->user_email ) { ?>
									<span class="vendorcustom-mail"><i class="fas fa-envelope aligmentvendor"></i> &nbsp;<?php echo esc_html( $user->user_email ); ?></span>
								<?php } ?>
							<?php } ?>
							&nbsp;&nbsp;
							<?php if ( $porto_settings['porto_wcvendors_url'] ) { ?>
								<?php if ( isset( $user->user_url ) && $user->user_url ) { ?>
									<span class="vendorcustom-mail"><i class="fas fa-globe aligmentvendor"></i> &nbsp; <?php echo esc_url( $user->user_url ); ?></span>
								<?php } ?>
							<?php } ?>

							<p class="vendor-user-social">
								<?php if ( isset( $user->facebook_url ) && $user->facebook_url ) : ?>
									<span class="user-facebook"><a rel="nofollow" href="<?php echo esc_url( $user->facebook_url ); ?>"><i class="fab fa-facebook-square"></i></a></span>
								<?php endif; ?>

								<?php if ( isset( $user->twitter_url ) && $user->twitter_url ) : ?>
									<span class="user-twitter"><a rel="nofollow" href="<?php echo esc_url( $user->twitter_url ); ?>"><i class="fab fa-twitter-square"></i></a></span>
								<?php endif; ?>

								<?php if ( isset( $user->gplus_url ) && $user->gplus_url ) : ?>
									<span class="user-googleplus"><a rel="nofollow" href="<?php echo esc_url( $user->gplus_url ); ?>"><i class="fab fa-google-plus-square"></i></a></span>
								<?php endif; ?>

								<?php if ( isset( $user->youtube_url ) && $user->youtube_url ) : ?>
									<span class="user-youtube"><a rel="nofollow" href="<?php echo esc_url( $user->youtube_url ); ?>"><i class="fab fa-youtube-square"></i></a></span>
								<?php endif; ?>

								<?php if ( isset( $user->linkedin_url ) && $user->linkedin_url ) : ?>
									<span class="user-linkedin"><a rel="nofollow" href="<?php echo esc_url( $user->linkedin_url ); ?>"><i class="fab fa-linkedin"></i></a></span>
								<?php endif; ?>

								<?php if ( isset( $user->flickr_url ) && $user->flickr_url ) : ?>
									<span class="user-flicker"><a rel="nofollow" href="<?php echo esc_url( $user->flickr_url ); ?>"><i class="fab fa-flickr"></i></a></span>
								<?php endif; ?>
							</p>

						<?php } ?>

						</div>

					</div>
			<?php } ?>
		<?php } ?>
		<?php
		if ( is_product() ) {
			$shop_name = get_user_meta( $post->post_author, 'pv_shop_name', true );
			?>
			<?php if ( $porto_settings['porto_single_wcvendors_product_description'] ) { ?>
				<?php
					$product_id = get_the_ID();
					$author     = WCV_Vendors::get_vendor_from_product( $product_id );
					$link       = WCV_Vendors::get_vendor_shop_page( $author );
					$author     = WCV_Vendors::get_vendor_from_product( get_the_ID() );
					$user       = get_userdata( $author );

				if ( $user ) {
					$r = get_user_meta( $user->ID, 'picture', true );
				}
				if ( $user && $r && isset( $r['url'] ) ) {
					$r = $r['url'];
					?>
					<div class="vendor-profile-bg" style="background:url('<?php echo esc_url( $r ); ?>') ;background-size:cover">
				<?php } else { ?>
					<div class="vendor-profile-bg">
				<?php } ?>
					<div class="overlay-vendor-effect">
					<?php if ( $porto_settings['porto_wcvendors_product_avatar'] ) { ?>
						<div class="vendor_userimg">
							<div class="profile-img">
								<a href="<?php echo esc_url( $link ); ?>"> <?php echo get_avatar( $author, 80 ); ?>	</a>
							</div>
						</div>
					<?php } ?>
						<h1><a href="<?php echo esc_url( $link ); ?>"><?php echo esc_html( $shop_name ); ?></a></h1>
						<div class="custom_shop_description">
							<?php echo do_shortcode( get_user_meta( $post->post_author, 'pv_shop_description', true ) ); ?>
						</div>
					</div>

				<?php
					$author = WCV_Vendors::get_vendor_from_product( get_the_ID() );
					$user   = get_userdata( $author );

				if ( $porto_settings['porto_wcvendors_product_profile'] ) {
					if ( $porto_settings['porto_wcvendors_phone'] ) {
						if ( $user->phone_number ) {
							?>
							<span class="vendorcustom-mail"><i class="fas fa-phone aligmentvendor"></i> &nbsp;<?php echo esc_html( $user->phone_number ); ?></span>
						<?php } ?>
					<?php } ?>
					<?php if ( $porto_settings['porto_wcvendors_email'] ) { ?>
						<?php if ( $user->user_email ) { ?>
							<span class="vendorcustom-mail"><i class="fas fa-envelope aligmentvendor"></i> &nbsp;<?php echo esc_html( $user->user_email ); ?></span>
						<?php } ?>
					<?php } ?>

					<?php if ( $porto_settings['porto_wcvendors_url'] ) { ?>
						<?php if ( $user->user_url ) { ?>
							<span class="vendorcustom-mail"><i class="fas fa-globe aligmentvendor"></i> &nbsp; <?php echo esc_url( $user->user_url ); ?></span>
						<?php } ?>
					<?php } ?>


					<p class="vendor-user-social">
						<?php if ( $user->facebook_url ) : ?>
							<span class="user-facebook"><a rel="nofollow" href="<?php echo esc_url( $user->facebook_url ); ?>"><i class="fab fa-facebook-square"></i></a></span>
						<?php endif; ?>

						<?php if ( $user->twitter_url ) : ?>
							<span class="user-twitter"><a rel="nofollow" href="<?php echo esc_url( $user->twitter_url ); ?>"><i class="fab fa-twitter-square"></i></a></span>
						<?php endif; ?>

						<?php if ( $user->gplus_url ) : ?>
							<span class="user-googleplus"><a rel="nofollow" href="<?php echo esc_url( $user->gplus_url ); ?>"><i class="fab fa-google-plus-square"></i></a></span>
						<?php endif; ?>

						<?php if ( $user->youtube_url ) : ?>
							<span class="user-youtube"><a rel="nofollow" href="<?php echo esc_url( $user->youtube_url ); ?>"><i class="fab fa-youtube-square"></i></a></span>
						<?php endif; ?>

						<?php if ( $user->linkedin_url ) : ?>
							<span class="user-linkedin"><a rel="nofollow" href="<?php echo esc_url( $user->linkedin_url ); ?>"><i class="fab fa-linkedin"></i></a></span>
						<?php endif; ?>

						<?php if ( $user->flickr_url ) : ?>
							<span class="user-flicker"><a rel="nofollow" href="<?php echo esc_url( $user->flickr_url ); ?>"><i class="fab fa-flickr"></i></a></span>
						<?php endif; ?>
					</p>

				<?php } ?>

				</div>
			<?php } ?>

		<?php } ?>
		<?php
	}
endif;

// Woocommerce Vendor End


if ( ! function_exists( 'porto_settings_google_fonts' ) ) :
	function porto_settings_google_fonts() {
		return array(
			'body'                  => array( '400', '500', '600', '700' ),
			'alt'                   => array( '400', '700' ),
			'h1'                    => array( '200', '300', '400', '600', '700', '800' ),
			'h2'                    => array( '200', '300', '400', '500', '600', '700', '800' ),
			'h3'                    => array( '200', '300', '400', '500', '600', '700', '800' ),
			'h4'                    => array( '200', '300', '400', '500', '600', '700', '800' ),
			'h5'                    => array( '200', '300', '400', '500', '600', '700', '800' ),
			'h6'                    => array( '200', '300', '400', '500', '600', '700', '800' ),
			'paragraph'             => array( '400', '600', '700' ),
			'footer'                => array( '400', '600', '700' ),
			'footer-heading'        => array( '200', '300', '400', '500', '600', '700', '800' ),
			'shortcode-testimonial' => array( '400', '700' ),
			'menu'                  => array(),
			'menu-side'             => array(),
			'menu-popup'            => array(),
			'add-to-cart'           => array( '400', '600', '700' ),
			'custom1'               => array( '400', '600', '700' ),
			'custom2'               => array( '400', '600', '700' ),
			'custom3'               => array( '400', '600', '700' ),
		);
	}
endif;

// Enable font size in the editor
if ( ! function_exists( 'porto_mce_buttons' ) ) {
	function porto_mce_buttons( $buttons ) {
		array_unshift( $buttons, 'fontsizeselect' ); // Add Font Size Select
		return $buttons;
	}
}
add_filter( 'mce_buttons_2', 'porto_mce_buttons' );

// Customize mce editor font sizes
if ( ! function_exists( 'porto_mce_text_sizes' ) ) {
	function porto_mce_text_sizes( $init_array ) {
		$init_array['fontsize_formats'] = '9px 10px 11px 12px 13px 14px 15px 16px 17px 18px 19px 20px 21px 22px 23px 24px 25px 26px 27px 28px 29px 30px 31px 32px 33px 34px 35px 36px 37px 38px 39px 40px 41px 42px 43px 44px 45px 46px 47px 48px 49px 50px 51px 52px 53px 54px 55px 56px 57px 58px 59px 60px 61px 62px 63px 64px 65px 66px 67px 68px 69px 70px 71px 72px';
		return $init_array;
	}
}
add_filter( 'tiny_mce_before_init', 'porto_mce_text_sizes' );

if ( ! function_exists( 'porto_get_post_type_items' ) ) :
	function porto_get_post_type_items( $post_type, $args_extended = array() ) {

		$result = array();

		$args = array(
			'post_type'   => $post_type,
			'post_status' => 'publish',
			'showposts'   => -1,
			'order'       => 'ASC',
			'orderby'     => 'title',
		);

		if ( $args && count( $args_extended ) ) {
			$args = array_merge( $args, $args_extended );
		}

		$query = new WP_Query( $args );

		if ( $query->have_posts() ) {

			while ( $query->have_posts() ) {
				$p                       = $query->next_post();
				$result[ $p->post_name ] = $p->post_title;
			}
		}

		return $result;
	}
endif;

if ( ! function_exists( 'porto_is_wide_layout' ) ) :
	function porto_is_wide_layout( $layout = false ) {
		global $porto_layout;
		if ( ! $layout ) {
			$layout = $porto_layout;
		}
		return ( 'widewidth' == $layout || 'wide-left-sidebar' == $layout || 'wide-right-sidebar' == $layout || 'wide-both-sidebar' == $layout );
	}
endif;

function porto_get_template_part( $slug, $name = null, $args = array() ) {
	if ( empty( $args ) ) {
		return get_template_part( $slug, $name );
	}

	if ( is_array( $args ) ) {
		extract( $args ); // @codingStandardsIgnoreLine
	}

	$templates = array();
	$name      = (string) $name;
	if ( '' !== $name ) {
		$templates[] = "{$slug}-{$name}.php";
	}
	$templates[] = "{$slug}.php";
	$template    = locate_template( $templates );
	$template    = apply_filters( 'porto_get_template_part', $template, $slug, $name );

	if ( $template ) {
		include $template;
	}
}

function porto_generate_column_classes( $cols, $return_arr = false ) {
	$cols = (int) $cols;
	switch ( $cols ) {
		case 1:
			$cols_arr = array( 'xs' => 1 );
			break;
		case 2:
			$cols_arr = array( 'md' => 2 );
			break;
		case 3:
			$cols_arr = array(
				'sm' => 2,
				'lg' => 3,
			);
			break;
		case 4:
			$cols_arr = array(
				'sm' => 2,
				'md' => 3,
				'xl' => 4,
			);
			break;
		case 5:
			$cols_arr = array(
				'xs' => 2,
				'sm' => 3,
				'lg' => 4,
				'xl' => 5,
			);
			break;
		case 6:
			$cols_arr = array(
				'xs' => 2,
				'sm' => 3,
				'md' => 4,
				'lg' => 5,
				'xl' => 6,
			);
			if ( porto_is_wide_layout() ) {
				$cols_arr['lg'] = 4;
				$cols_arr['md'] = 3;
				unset( $cols_arr['sm'] );
			}
			break;
		case 7:
			$cols_arr = array(
				'xs' => 2,
				'sm' => 3,
				'md' => 4,
				'lg' => 5,
				'xl' => 7,
			);
			if ( porto_is_wide_layout() ) {
				$cols_arr['xl'] = 6;
				$cols_arr['sl'] = 7;
			}
			break;
		case 8:
			$cols_arr = array(
				'xs' => 2,
				'sm' => 3,
				'md' => 4,
				'lg' => 6,
				'xl' => 8,
			);
			if ( porto_is_wide_layout() ) {
				$cols_arr['xl'] = 7;
				$cols_arr['sl'] = 8;
			}
			break;
		default:
			$cols_arr = array(
				'md' => 2,
				'lg' => 3,
				'xl' => 4,
			);
	}
	if ( ! isset( $cols_arr['xs'] ) ) {
		$cols_arr['xs'] = 1;
	}
	if ( $return_arr ) {
		return apply_filters( 'porto_generate_column_classes', $cols_arr, $cols, true );
	}

	$class = array();
	foreach ( $cols_arr as $key => $columns ) {
		if ( 'xs' == $key ) {
			$class[] = 'ccols-' . $columns;
		} else {
			$class[] = 'ccols-' . $key . '-' . $columns;
		}
	}

	$class = apply_filters( 'porto_generate_column_classes', $class, $cols, false );
	return implode( ' ', $class );
}

// update image srcset meta
add_filter( 'wp_calculate_image_srcset', 'porto_image_srcset_filter_sizes', 10, 2 );
if ( ! function_exists( 'porto_image_srcset_filter_sizes' ) ) :
	function porto_image_srcset_filter_sizes( $sources, $size_array ) {
		foreach ( $sources as $width => $source ) {
			if ( isset( $source['descriptor'] ) && 'w' == $source['descriptor'] && ( $width < apply_filters( 'porto_mini_screen_size', 320 ) || (int) $width > (int) $size_array[0] ) ) {
				unset( $sources[ $width ] );
			}
		}
		return $sources;
	}
endif;


if ( ! function_exists( 'porto_is_product' ) ) :
	function porto_is_product() {
		$result = false;
		if ( class_exists( 'Woocommerce' ) && is_product() ) {
			$result = true;
		}
		return apply_filters( 'porto_is_product', $result );
	}
endif;

if ( ! function_exists( 'porto_is_elementor_preview' ) ) :
	function porto_is_elementor_preview() {
		if ( ! defined( 'ELEMENTOR_VERSION' ) ) {
			return false;
		}
		if ( ( isset( $_REQUEST['action'] ) && 'elementor' == $_REQUEST['action'] ) || isset( $_REQUEST['elementor-preview'] ) ) {
			return true;
		}
		return false;
	}
endif;

if ( ! function_exists( 'porto_output_tagged_content' ) ) :
	function porto_output_tagged_content( $content ) {
		if ( ! $content ) {
			return '';
		}
		if ( ! defined( 'ELEMENTOR_VERSION' ) ) {
			return apply_filters( 'the_content', wp_kses_post( $content ) );
		} else {
			$content = do_shortcode( wp_kses_post( $content ) );
			return function_exists( 'porto_shortcode_format_content' ) ? porto_shortcode_format_content( $content ) : $content;
		}
	}
endif;

if ( ! function_exists( 'porto_config_value' ) ) :
	function porto_config_value( $value ) {
		return isset( $value ) ? $value : 0;
	}
endif;

if ( ! function_exists( 'porto_is_gutenberg' ) ) :

	/**
	 * Check if current post is using Gutenberg editor
	 */
	function porto_is_gutenberg( $post_type ) {
		global $wp_version;
		if ( version_compare( $wp_version, '5', '>=' ) ) {
			return use_block_editor_for_post_type( $post_type );
		} elseif ( function_exists( 'gutenberg_can_edit_post_type' ) ) {
			return gutenberg_can_edit_post_type( $post_type );
		}
		return false;
	}
endif;

if ( ! function_exists( 'porto_check_using_elementor_style' ) ) :
	function porto_check_using_elementor_style() {
		if ( ! defined( 'ELEMENTOR_VERSION' ) ) {
			return false;
		}

		if ( get_theme_mod( 'elementor_edited', false ) ) {
			return true;
		}

		if ( is_home() && get_theme_mod( 'elementor_blog_edited', false ) ) {
			return true;
		}

		$elementor_sidebars = get_theme_mod( 'elementor_sidebars', array() );
		if ( ! empty( $elementor_sidebars ) ) {
			global $porto_layout, $porto_sidebar, $porto_sidebar2;
			foreach ( $elementor_sidebars as $sidebar_id => $widgets ) {
				if ( ! empty( $widgets ) && ( 0 === strpos( $sidebar_id, 'footer-' ) || 0 === strpos( $sidebar_id, 'content-bottom-' ) ) ) {
					return true;
				}
			}

			if (
				( in_array( $porto_layout, porto_options_both_sidebars() ) && ( ! empty( $elementor_sidebars[ $porto_sidebar ] ) || ! empty( $elementor_sidebars[ $porto_sidebar2 ] ) ) ) ||
				( in_array( $porto_layout, porto_options_sidebars() ) && ! empty( $elementor_sidebars[ $porto_sidebar ] ) )
				) {
				return true;
			}
		}

		if ( is_singular() ) {
			$elementor_blocks = get_theme_mod( 'elementor_blocks_post_types', array() );
			if ( ! empty( $elementor_blocks ) ) {
				foreach ( $elementor_blocks as $post_type ) {
					if ( is_singular( $post_type ) ) {
						return true;
					}
				}
			}
		}

		if ( porto_get_meta_value( '_porto_use_elementor_blocks' ) ) {
			return true;
		}
		return false;
	}
endif;

/**
 * old WordPress fallback functions
 */
if ( ! function_exists( 'wp_slash_strings_only' ) ) :
	function wp_slash_strings_only( $val ) {
		return $val;
	}
endif;
