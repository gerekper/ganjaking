<?php
/**
 * Add-on Name: Ultimate Video
 * Add-on URI: http://dev.brainstormforce.com
 *
 *  @package Ultimate Video
 */

if ( ! class_exists( 'Ultimate_VC_Addons_Videos' ) ) {
	/**
	 * Function that initializes Ultimate Video Module
	 *
	 * @class Ultimate_VC_Addons_Videos
	 */
	class Ultimate_VC_Addons_Videos {
		/**
		 * Constructor function that constructs default values for the Ultimate Heading module.
		 *
		 * @method __construct
		 */
		public function __construct() {
			if ( Ultimate_VC_Addons::$uavc_editor_enable ) {
				add_action( 'init', array( $this, 'ultimate_videos_module_init' ) );
			}
			add_shortcode( 'ultimate_video', array( $this, 'ultimate_videos_module_shortcode' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'register_videos_module_assets' ), 1 );
		}//end __construct()

		/**
		 * Function that register styles and scripts for Ultimate Heading Module.
		 *
		 * @method register_videos_module_assets
		 */
		public function register_videos_module_assets() {
			Ultimate_VC_Addons::ultimate_register_style( 'ultimate-vc-addons-videos-style', 'video_module' );

			Ultimate_VC_Addons::ultimate_register_script( 'ultimate-vc-addons-videos-script', 'video_module', false, array( 'jquery' ), ULTIMATE_VERSION, false );
		}//end register_videos_module_assets()

		/**
		 * Getting the video ID
		 *
		 * @param array $setting contents all the variable.
		 */
		public function get_video_id( $setting ) {

			$id = '';
			if ( 'uv_iframe' == $setting['video_type'] ) {
				$url = $setting['u_video_url'];
				if ( preg_match( '~^(?:https?://)? (?:www[.])?(?:youtube[.]com/watch[?]v=|youtu[.]be/)([^&]{11})~x', $url ) ) {
					if ( preg_match( '/[\\?\\&]v=([^\\?\\&]+)/', $url, $matches ) ) {
						$id = $matches[1];
					}
				}
			} elseif ( 'vimeo_video' == $setting['video_type'] ) {
				$url = $setting['vimeo_video_url'];
				if ( preg_match( '/https?:\/\/(?:www\.)?vimeo\.com\/\d{8}/', $url ) ) {
					$id = preg_replace( '/[^\/]+[^0-9]|(\/)/', '', rtrim( $url, '/' ) );
				}
			}

			return $id;
		}

		/**
		 * Getting the video url
		 *
		 * @param array $setting contents all the variable.
		 * @param array $params value of URL.
		 */
		public function get_url( $setting, $params ) {

			if ( 'vimeo_video' == $setting['video_type'] ) {
				$url = 'https://player.vimeo.com/video/';
			} else {
				$cookie = '';

				if ( 'on' == $setting['yt_privacy_mode'] ) {
					$cookie = '-nocookie';
				}
				$url = 'https://www.youtube' . $cookie . '.com/embed/';
			}

			$url = add_query_arg( $params, $url . $this->get_video_id( $setting ) );

			$url .= ( empty( $params ) ) ? '?' : '&';

			$url .= 'autoplay=1';

			if ( 'vimeo_video' == $setting['video_type'] && '' != $setting['vimeo_start_time'] ) {
				$time = gmdate( 'H\hi\ms\s', $setting['vimeo_start_time'] );

				$url .= '#t=' . $time;
			}
			return $url;
		}

		/**
		 * Returns Video Thumbnail Image.
		 *
		 * @param array $setting contents all the variable.
		 */
		public function get_video_thumb( $setting ) {
			$id = $this->get_video_id( $setting );
			if ( '' == $this->get_video_id( $setting ) ) {
				return '';
			}
			if ( 'custom' == $setting['thumbnail'] ) {
				$thumb = $setting['custom_thumb'];
				if ( '' !== $thumb ) {
					$img      = apply_filters( 'ult_get_img_single', $thumb, 'url' );
					$img_info = esc_url( apply_filters( 'ultimate_images', $img ) );
					return $img_info;
				}
			} else {
				if ( 'uv_iframe' == $setting['video_type'] ) {
					$thumb = 'https://i.ytimg.com/vi/' . $id . '/' . apply_filters( 'ultv_video_youtube_image_quality', $setting['default_thumb'] ) . '.jpg';
				} else {
					$vimeo = maybe_unserialize( file_get_contents( "https://vimeo.com/api/v2/video/$id.php" ) ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
					$thumb = str_replace( '_640', '_840', $vimeo[0]['thumbnail_large'] );
				}
			}
			return $thumb;
		}

		/**
		 * Get embed params. Retrieve video widget embed parameters.
		 *
		 * @param array $setting contents all the variable.
		 */
		public function get_embed_params( $setting ) {
			$params = array();
			if ( 'uv_iframe' === $setting['video_type'] ) {
				$youtube_options = array( 'autoplay', 'rel', 'controls', 'mute', 'modestbranding' );

				foreach ( $youtube_options as $option ) {
					if ( 'autoplay' == $option ) {
						if ( 'on' === $setting['yt_autoplay'] ) {
							$params[ $option ] = '1';
						}
						continue;
					}
					if ( 'rel' == $option ) {
						$value             = ( 'on' === $setting['yt_sugg_video'] ) ? '1' : '0';
						$params[ $option ] = $value;
					}
					if ( 'controls' == $option ) {
						$value             = ( 'on' === $setting['yt_play_control'] ) ? '1' : '0';
						$params[ $option ] = $value;
					}
					if ( 'mute' == $option ) {
						$value             = ( 'on' === $setting['yt_mute_control'] ) ? '1' : '0';
						$params[ $option ] = $value;
					}
					if ( 'modestbranding' == $option ) {
						$value             = ( 'on' === $setting['yt_modest_branding'] ) ? '1' : '0';
						$params[ $option ] = $value;
					}
					$params['start'] = $setting['yt_start_time'];
					$params['end']   = $setting['yt_stop_time'];
				}
			}
			if ( 'vimeo_video' === $setting['video_type'] ) {
				$vimeo_options = array( 'autoplay', 'loop', 'title', 'portrait', 'byline' );

				foreach ( $vimeo_options as $option ) {
					if ( 'autoplay' == $option ) {
						if ( 'on' === $setting['vimeo_autoplay'] ) {
							$params[ $option ] = '1';
						}
						continue;
					}
					if ( 'loop' === $option ) {
						$value             = ( 'on' === $setting['vimeo_loop'] ) ? '1' : '0';
						$params[ $option ] = $value;
					}
					if ( 'title' === $option ) {
						$value             = ( 'on' === $setting['vimeo_intro_title'] ) ? '1' : '0';
						$params[ $option ] = $value;
					}
					if ( 'portrait' === $option ) {
						$value             = ( 'on' === $setting['vimeo_intro_portrait'] ) ? '1' : '0';
						$params[ $option ] = $value;
					}
					if ( 'byline' === $option ) {
						$value             = ( 'on' === $setting['vimeo_intro_byline'] ) ? '1' : '0';
						$params[ $option ] = $value;
					}
				}
				$params['color']     = str_replace( '#', '', $setting['vimeo_control_color'] );
				$params['autopause'] = '0';
			}
			return $params;
		}

		/**
		 * Returns Vimeo Headers.
		 *
		 * @param array $setting contents all the variable.
		 */
		public function get_header_wrap( $setting ) {
			if ( 'vimeo_video' != $setting['video_type'] ) {
				return;
			}
			$id   = $this->get_video_id( $setting );
			$html = '';
			if ( isset( $id ) && '' != $id ) {
				$vimeo = maybe_unserialize( file_get_contents( "https://vimeo.com/api/v2/video/$id.php" ) ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
				if ( 'on' == $setting['vimeo_intro_portrait'] ||
				'on' == $setting['vimeo_intro_title'] ||
				'on' == $setting['vimeo_intro_byline']
				) {
					$html = '<div class="ultv-vimeo-wrap">';
					if ( 'on' == $setting['vimeo_intro_portrait'] ) {
						$html .= '<div class="ultv-vimeo-portrait">
					<a href="' . $vimeo[0]['user_url'] . '"><img src="' . $vimeo[0]['user_portrait_huge'] . '"></a></div>';
					}
					if ( 'on' == $setting['vimeo_intro_title'] ||
					'on' == $setting['vimeo_intro_byline']
					) {
						$html .= '<div class="ultv-vimeo-headers">';
						if ( 'on' == $setting['vimeo_intro_title'] ) {
							$html .= '<div class="ultv-vimeo-title">
							<a href="' . $setting['vimeo_video_url'] . '">' . $vimeo[0]['title'] . '</a>
						</div>';
						}
						if ( 'on' == $setting['vimeo_intro_byline'] ) {
							$html .= '<div class="ultv-vimeo-byline">
						' . esc_html__( 'from ', 'ultimate_vc' ) . '<a href="' . $setting['vimeo_video_url'] . '"> ' . $vimeo[0]['user_name'] . '</a>
					</div>';
						}
						$html .= '</div>';
					}
					$html .= '</div>';
				}
			}
			return $html;
		}

		/**
		 * Render the video
		 *
		 * @param array $setting contents all the variable.
		 * @param array $uid Id of the video.
		 */
		public function get_video_embed( $setting, $uid ) {
			$id                      = $this->get_video_id( $setting );
			$embed_param             = $this->get_embed_params( $setting );
			$src                     = $this->get_url( $setting, $embed_param );
			$main_video_style_inline = '';
			$main_video_responsive   = '';
			$html                    = '';
			$style                   = '';
			$hover_color             = '';
			$device                  = ( false !== ( stripos( $_SERVER['HTTP_USER_AGENT'], 'iPhone' ) ) ? 'true' : 'false' );

			if ( 'uv_iframe' == $setting['video_type'] ) {
				$autoplay = ( 'on' == $setting['yt_autoplay'] ) ? '1' : '0';
			} else {
				$autoplay = ( 'on' == $setting['vimeo_autoplay'] ) ? '1' : '0';
			}
			if ( 'defaulticon' == $setting['play_source'] ) {
				$setting['play_size'] = 'width:' . $setting['play_size'] . 'px';
				if ( 'uv_iframe' === $setting['video_type'] ) {
					$html = '<svg height="100%" version="1.1" viewBox="0 0 68 48" width="100%"><path class="ultv-youtube-icon-bg" d="m .66,37.62 c 0,0 .66,4.70 2.70,6.77 2.58,2.71 5.98,2.63 7.49,2.91 5.43,.52 23.10,.68 23.12,.68 .00,-1.3e-5 14.29,-0.02 23.81,-0.71 1.32,-0.15 4.22,-0.17 6.81,-2.89 2.03,-2.07 2.70,-6.77 2.70,-6.77 0,0 .67,-5.52 .67,-11.04 l 0,-5.17 c 0,-5.52 -0.67,-11.04 -0.67,-11.04 0,0 -0.66,-4.70 -2.70,-6.77 C 62.03,.86 59.13,.84 57.80,.69 48.28,0 34.00,0 34.00,0 33.97,0 19.69,0 10.18,.69 8.85,.84 5.95,.86 3.36,3.58 1.32,5.65 .66,10.35 .66,10.35 c 0,0 -0.55,4.50 -0.66,9.45 l 0,8.36 c .10,4.94 .66,9.45 .66,9.45 z" fill="#1f1f1e" ></path><path d="m 26.96,13.67 18.37,9.62 -18.37,9.55 -0.00,-19.17 z" fill="#fff"></path><path d="M 45.02,23.46 45.32,23.28 26.96,13.67 43.32,22.34 45.02,23.46 z" fill="#ccc"></path></svg>';
				}
				if ( 'vimeo_video' === $setting['video_type'] ) {
					$html = '<svg version="1.1" height="100%" width="100%"  viewBox="0 14.375 95 66.25"><path class="ultv-vimeo-icon-bg" d="M12.5,14.375c-6.903,0-12.5,5.597-12.5,12.5v41.25c0,6.902,5.597,12.5,12.5,12.5h70c6.903,0,12.5-5.598,12.5-12.5v-41.25c0-6.903-5.597-12.5-12.5-12.5H12.5z"/><polygon fill="#FFFFFF" points="39.992,64.299 39.992,30.701 62.075,47.5 "/></svg>';
				}
			} elseif ( 'icon' == $setting['play_source'] ) {
				$setting['play_size'] = 'font-size:' . $setting['play_size'] . 'px;';
			} else {
				$thumb = $setting['play_image'];
				$imag  = apply_filters( 'ult_get_img_single', $thumb, 'url' );
				$html  = esc_url( apply_filters( 'ultimate_images', $imag ) );
			}
			if ( 'image' == $setting['play_source'] ) {
				$setting['play_size'] = 'width:' . $setting['play_size'] . 'px';
			}

			/* 	padding  	*/
			if ( '' != $setting['padding'] ) {
				$style = $setting['padding'];
			}

			/* ---- main heading styles ---- */
			if ( '' != $setting['main_video_font_family'] ) {
				$mvfont_family = get_ultimate_font_family( $setting['main_video_font_family'] );
				if ( $mvfont_family ) {
					$main_video_style_inline .= 'font-family:\'' . $mvfont_family . '\';';
				}
			}
			// main video font style.
			$main_video_style_inline .= get_ultimate_font_style( $setting['main_video_style'] );

			// FIX: set old font size before implementing responsive param.
			if ( is_numeric( $setting['main_video_font_size'] ) ) {
				$setting['main_video_font_size'] = 'desktop:' . $setting['main_video_font_size'] . 'px;';       }
			if ( is_numeric( $setting['main_video_line_height'] ) ) {
				$setting['main_video_line_height'] = 'desktop:' . $setting['main_video_line_height'] . 'px;';       }
			// responsive {main} video styles.
			$args                  = array(
				'target'      => '.ult-video.' . $uid . ' .ultv-subscribe-bar',
				'media_sizes' => array(
					'font-size'   => $setting['main_video_font_size'],
					'line-height' => $setting['main_video_line_height'],
				),
			);
			$main_video_responsive = get_ultimate_vc_responsive_media_css( $args );

			if ( '' != $setting['icon_hover_color'] ) {
				$hover_color = 'data-hoverbg=' . esc_attr( $setting['icon_hover_color'] ) . ' ';}

			if ( '' != $setting['default_hover_color'] ) {
				$hover_color .= 'data-defaulthoverbg=' . esc_attr( $setting['default_hover_color'] ) . ' ';}

			$output          = '<div class="ultv-video ultv-aspect-ratio-' . esc_attr( $setting['aspect_ratio'] ) . ' ultv-subscribe-responsive-' . esc_attr( $setting['subscribe_bar_responsive'] ) . '" data-videotype="' . esc_attr( $setting['video_type'] ) . '">
						<div class="ultv-video__outer-wrap" data-autoplay="' . esc_attr( $autoplay ) . '" data-device="' . esc_attr( $device ) . '" ' . esc_attr( $hover_color ) . ' data-iconbg="' . esc_attr( $setting['icon_color'] ) . '" data-overcolor="' . esc_attr( $setting['overlay_color'] ) . '" data-defaultbg="' . esc_attr( $setting['default_color'] ) . '" data-defaultplay="' . esc_attr( $setting['play_source'] ) . '">';
					$output .= $this->get_header_wrap( $setting );
					$output .= '<div class="ultv-video__play" data-src="' . esc_attr( $src ) . '">
								<img class="ultv-video__thumb" src="' . $this->get_video_thumb( $setting ) . '"/>
								<div class="ultv-video__play-icon ' . esc_attr( ( 'icon' == $setting['play_source'] ) ? $setting['play_icon'] : '' ) . ' ultv-animation-' . esc_attr( $setting['hover_animation'] ) . '" style="' . $setting['play_size'] . '">';
			if ( 'image' == $setting['play_source'] ) {
				$output .= '<img src="' . esc_attr( $html ) . '"/>';
			}
			if ( 'defaulticon' == $setting['play_source'] ) {
				$output .= $html;
			}
			$output .= '</div> </div> </div>';
			if ( 'uv_iframe' == $setting['video_type'] && 'on' == $setting['enable_sub_bar'] ) {
				$channel_name = ( '' != $setting['yt_channel_name'] ) ? $setting['yt_channel_name'] : '';

				$channel_id = ( '' != $setting['yt_channel_id'] ) ? $setting['yt_channel_id'] : '';

				$youtube_text = ( '' != $setting['yt_channel_text'] ) ? $setting['yt_channel_text'] : '';

				$subscriber_count = ( 'on' == $setting['show_sub_count'] ) ? 'default' : 'hidden';
				if ( '' != $setting['yt_text_color'] ) {
					$yt_txt = 'color:' . $setting['yt_text_color'] . ';';
				}
				if ( '' != $setting['yt_background_color'] ) {
					$yt_txt_back = 'background-color:' . $setting['yt_background_color'] . ';';
				}
				$output .= '<div class="ultv-subscribe-bar ult-responsive" ' . $main_video_responsive . ' style = "' . esc_attr( $yt_txt ) . ' ' . esc_attr( $yt_txt_back ) . ' ' . esc_attr( $style ) . '">
				<div class="ultv-subscribe-bar-prefix" style="' . esc_attr( $main_video_style_inline ) . '">' . esc_attr( $youtube_text ) . '</div>
				<div class="ultv-subscribe-content">
					<script src="https://apis.google.com/js/platform.js"></script> <!-- Need to be enqueued from someplace else -->';// phpcs:ignore WordPress.WP.EnqueuedResources.NonEnqueuedScript
				if ( 'channel_name' == $setting['chanel_id_name'] ) {
					$output .= '<div class="g-ytsubscribe" data-channel="' . esc_attr( $channel_name ) . '" data-count="' . esc_attr( $subscriber_count ) . '"></div>';
				} elseif ( 'channel_id' == $setting['chanel_id_name'] ) {
					$output .= '<div class="g-ytsubscribe" data-channelid="' . esc_attr( $channel_id ) . '" data-count="' . esc_attr( $subscriber_count ) . '"></div>';
				}
				$output .= '</div> </div>';
			}
			$output .= '</div>';
			return $output;
		}

		/**
		 * Function that initializes settings of Ultimate Heading Module.
		 *
		 * @method ultimate_videos_module_init
		 */
		public function ultimate_videos_module_init() {
			if ( function_exists( 'vc_map' ) ) {
				vc_map(
					array(
						'name'        => __( 'Video', 'ultimate_vc' ),
						'base'        => 'ultimate_video',
						'class'       => 'vc_ultimate_video',
						'icon'        => 'vc_ultimate_video',
						'category'    => 'Ultimate VC Addons',
						'description' => __( 'Embed video without sacrificing Page speed.', 'ultimate_vc' ),
						'params'      => array(
							array(
								'type'             => 'ult_param_heading',
								'text'             => __( 'Video', 'ultimate_vc' ),
								'param_name'       => 'video_setting',
								'class'            => 'ult-param-heading',
								'edit_field_class' => 'ult-param-heading-wrapper vc_column vc_col-sm-12',
							),
							array(
								'type'        => 'dropdown',
								'class'       => '',
								'admin_label' => true,
								'heading'     => __( 'Video Type', 'ultimate_vc' ),
								'param_name'  => 'video_type',
								'value'       =>
								array(
									__( 'YouTube Video', 'ultimate_vc' ) => 'uv_iframe',
									__( 'Vimeo Video', 'ultimate_vc' ) => 'vimeo_video',
								),
							),
							array(
								'type'        => 'textfield',
								'class'       => '',
								'heading'     => __( 'Enter YouTube URL of the Video', 'ultimate_vc' ),
								'param_name'  => 'u_video_url',
								'value'       => 'https://www.youtube.com/watch?v=HJRzUQMhJMQ',
								'description' => __( 'Make sure you add the actual URL of the video and not the share URL.<br><b>Valid :</b>  https://www.youtube.com/watch?v=HJRzUQMhJMQ<br><b>Invalid :</b> https://youtu.be/HJRzUQMhJMQ ', 'ultimate_vc' ),
								'dependency'  => array(
									'element' => 'video_type',
									'value'   => array( 'uv_iframe' ),
								),
							),
							array(
								'type'        => 'number',
								'class'       => '',
								'heading'     => __( 'Start Time', 'ultimate_vc' ),
								'admin_label' => true,
								'param_name'  => 'yt_start_time',
								'value'       => '',
								'suffix'      => 'seconds',
								'dependency'  => array(
									'element' => 'video_type',
									'value'   => array( 'uv_iframe' ),
								),
							),
							array(
								'type'        => 'number',
								'class'       => '',
								'heading'     => __( 'Stop Time', 'ultimate_vc' ),
								'admin_label' => true,
								'param_name'  => 'yt_stop_time',
								'value'       => '',
								'suffix'      => 'seconds',
								'description' => __( 'You may start / stop the video at any point you would like.', 'ultimate_vc' ),
								'dependency'  => array(
									'element' => 'video_type',
									'value'   => array( 'uv_iframe' ),
								),
							),
							array(
								'type'        => 'textfield',
								'class'       => '',
								'heading'     => __( 'Enter Vimeo URL of the Video', 'ultimate_vc' ),
								'param_name'  => 'vimeo_video_url',
								'value'       => 'https://vimeo.com/274860274',
								'description' => __( 'Make sure you add the actual URL of the video and not the share URL.<br><b>Valid :</b>  https://vimeo.com/274860274<br><b>Invalid :</b>  https://vimeo.com/channels/staffpicks/274860274 ', 'ultimate_vc' ),
								'dependency'  => array(
									'element' => 'video_type',
									'value'   => array( 'vimeo_video' ),
								),
							),
							array(
								'type'        => 'number',
								'class'       => '',
								'heading'     => __( 'Start Time', 'ultimate_vc' ),
								'admin_label' => true,
								'param_name'  => 'vimeo_start_time',
								'value'       => '',
								'suffix'      => 'seconds',
								'dependency'  => array(
									'element' => 'video_type',
									'value'   => array( 'vimeo_video' ),
								),
							),
							array(
								'type'        => 'dropdown',
								'class'       => '',
								'heading'     => __( 'Aspect Ratio', 'ultimate_vc' ),
								'admin_label' => true,
								'param_name'  => 'aspect_ratio',
								'value'       =>
								array(
									__( '16:9', 'ultimate_vc' ) => '16_9',
									__( '4:3', 'ultimate_vc' ) => '4_3',
									__( '3:2', 'ultimate_vc' ) => '3_2',
								),
							),
							array(
								'type'             => 'ult_param_heading',
								'text'             => __( 'Video Option', 'ultimate_vc' ),
								'param_name'       => 'video_option',
								'class'            => 'ult-param-heading',
								'edit_field_class' => 'ult-param-heading-wrapper top-margin vc_column vc_col-sm-12',
							),
							array(
								'type'        => 'ult_switch',
								'class'       => '',
								'heading'     => __( 'Autoplay', 'ultimate_vc' ),
								'param_name'  => 'yt_autoplay',
								'value'       => 'off',
								'options'     => array(
									'on' => array(
										'label' => '',
										'on'    => __( 'Yes', 'ultimate_vc' ),
										'off'   => __( 'No', 'ultimate_vc' ),
									),
								),
								'description' => __( 'If autoplay mode is enabled then thumbnail option will never show.', 'ultimate_vc' ),
								'dependency'  => array(
									'element' => 'video_type',
									'value'   => array( 'uv_iframe' ),
								),
							),
							array(
								'type'        => 'ult_switch',
								'class'       => '',
								'heading'     => __( 'Suggested Video', 'ultimate_vc' ),
								'param_name'  => 'yt_sugg_video',
								'value'       => 'off',
								'options'     => array(
									'on' => array(
										'label' => '',
										'on'    => __( 'Show', 'ultimate_vc' ),
										'off'   => __( 'Hide', 'ultimate_vc' ),
									),
								),
								'description' => __( 'If set to hide - the player will display related videos from the same channel as the video that was just played.<br>If set to show - the player will display the related videos from the random channels.', 'ultimate_vc' ),
								'dependency'  => array(
									'element' => 'video_type',
									'value'   => array( 'uv_iframe' ),
								),
							),
							array(
								'type'       => 'ult_switch',
								'class'      => '',
								'heading'    => __( 'Player Control', 'ultimate_vc' ),
								'param_name' => 'yt_play_control',
								'value'      => 'on',
								'options'    => array(
									'on' => array(
										'label' => '',
										'on'    => __( 'Show', 'ultimate_vc' ),
										'off'   => __( 'Hide', 'ultimate_vc' ),
									),
								),
								'dependency' => array(
									'element' => 'video_type',
									'value'   => array( 'uv_iframe' ),
								),
							),
							array(
								'type'       => 'ult_switch',
								'class'      => '',
								'heading'    => __( 'Mute', 'ultimate_vc' ),
								'param_name' => 'yt_mute_control',
								'value'      => 'off',
								'options'    => array(
									'on' => array(
										'label' => '',
										'on'    => __( 'Yes', 'ultimate_vc' ),
										'off'   => __( 'No', 'ultimate_vc' ),
									),
								),
								'dependency' => array(
									'element' => 'video_type',
									'value'   => array( 'uv_iframe' ),
								),
							),
							array(
								'type'        => 'ult_switch',
								'class'       => '',
								'heading'     => __( 'Modest Branding', 'ultimate_vc' ),
								'param_name'  => 'yt_modest_branding',
								'value'       => 'off',
								'options'     => array(
									'on' => array(
										'label' => '',
										'on'    => __( 'Yes', 'ultimate_vc' ),
										'off'   => __( 'No', 'ultimate_vc' ),
									),
								),
								'description' => __( 'This option lets you use a YouTube player that does not show a YouTube logo.', 'ultimate_vc' ),
								'dependency'  => array(
									'element' => 'video_type',
									'value'   => array( 'uv_iframe' ),
								),
							),
							array(
								'type'        => 'ult_switch',
								'class'       => '',
								'heading'     => __( 'Privacy Mode', 'ultimate_vc' ),
								'param_name'  => 'yt_privacy_mode',
								'value'       => 'off',
								'options'     => array(
									'on' => array(
										'label' => '',
										'on'    => __( 'Yes', 'ultimate_vc' ),
										'off'   => __( 'No', 'ultimate_vc' ),
									),
								),
								'description' => __( "When you turn on privacy mode, YouTube won't store information about visitors on your website unless they play the video.", 'ultimate_vc' ),
								'dependency'  => array(
									'element' => 'video_type',
									'value'   => array( 'uv_iframe' ),
								),
							),
							array(
								'type'        => 'ult_switch',
								'class'       => '',

								'heading'     => __( 'Autoplay', 'ultimate_vc' ),
								'param_name'  => 'vimeo_autoplay',
								'value'       => 'off',
								'options'     => array(
									'on' => array(
										'label' => '',
										'on'    => __( 'Yes', 'ultimate_vc' ),
										'off'   => __( 'No', 'ultimate_vc' ),
									),
								),
								'description' => __( 'If autoplay mode is enabled then thumbnail option will never show.', 'ultimate_vc' ),
								'dependency'  => array(
									'element' => 'video_type',
									'value'   => array( 'vimeo_video' ),
								),
							),
							array(
								'type'        => 'ult_switch',
								'class'       => '',

								'heading'     => __( 'Loop', 'ultimate_vc' ),
								'param_name'  => 'vimeo_loop',
								'value'       => 'off',
								'options'     => array(
									'on' => array(
										'label' => '',
										'on'    => __( 'Yes', 'ultimate_vc' ),
										'off'   => __( 'No', 'ultimate_vc' ),
									),
								),
								'description' => __( 'Play the video again when it reaches the end, infinitely.', 'ultimate_vc' ),
								'dependency'  => array(
									'element' => 'video_type',
									'value'   => array( 'vimeo_video' ),
								),
							),
							array(
								'type'        => 'ult_switch',
								'class'       => '',

								'heading'     => __( 'Intro Title', 'ultimate_vc' ),
								'param_name'  => 'vimeo_intro_title',
								'value'       => 'on',
								'options'     => array(
									'on' => array(
										'label' => '',
										'on'    => __( 'Show', 'ultimate_vc' ),
										'off'   => __( 'Hide', 'ultimate_vc' ),
									),
								),
								'description' => __( 'Show the video’s title.', 'ultimate_vc' ),
								'dependency'  => array(
									'element' => 'video_type',
									'value'   => array( 'vimeo_video' ),
								),
							),
							array(
								'type'        => 'ult_switch',
								'class'       => '',

								'heading'     => __( 'Intro Portrait', 'ultimate_vc' ),
								'param_name'  => 'vimeo_intro_portrait',
								'value'       => 'on',
								'options'     => array(
									'on' => array(
										'label' => '',
										'on'    => __( 'Show', 'ultimate_vc' ),
										'off'   => __( 'Hide', 'ultimate_vc' ),
									),
								),
								'description' => __( 'Show the author’s profile image (portrait).', 'ultimate_vc' ),
								'dependency'  => array(
									'element' => 'video_type',
									'value'   => array( 'vimeo_video' ),
								),
							),
							array(
								'type'        => 'ult_switch',
								'class'       => '',

								'heading'     => __( 'Intro Byline', 'ultimate_vc' ),
								'param_name'  => 'vimeo_intro_byline',
								'value'       => 'on',
								'options'     => array(
									'on' => array(
										'label' => '',
										'on'    => __( 'Show', 'ultimate_vc' ),
										'off'   => __( 'Hide', 'ultimate_vc' ),
									),
								),
								'description' => __( 'Show the author of the video.', 'ultimate_vc' ),
								'dependency'  => array(
									'element' => 'video_type',
									'value'   => array( 'vimeo_video' ),
								),
							),
							array(
								'type'       => 'colorpicker',
								'class'      => '',
								'heading'    => __( 'Controls Color', 'ultimate_vc' ),
								'param_name' => 'vimeo_control_color',
								'dependency' => array(
									'element' => 'video_type',
									'value'   => array( 'vimeo_video' ),
								),
							),
							array(
								'type'        => 'textfield',
								'heading'     => __( 'Extra class name', 'ultimate_vc' ),
								'param_name'  => 'el_class',
								'description' => __( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'ultimate_vc' ),
							),
							array(
								'type'             => 'ult_param_heading',
								'text'             => __( 'Thumbnail & Overlay ', 'ultimate_vc' ),
								'param_name'       => 'thum_over',
								'class'            => 'ult-param-heading',
								'edit_field_class' => 'ult-param-heading-wrapper vc_column vc_col-sm-12',
								'group'            => 'Thumbnail',
							),
							array(
								'type'       => 'dropdown',
								'class'      => '',
								'heading'    => __( 'Thumbnail', 'ultimate_vc' ),
								'param_name' => 'thumbnail',
								'value'      =>
								array(
									__( 'Default Thumbnail', 'ultimate_vc' ) => 'default',
									__( 'Custom Thumbnail', 'ultimate_vc' ) => 'custom',
								),
								'group'      => 'Thumbnail',
							),
							array(
								'type'       => 'ult_img_single',
								'class'      => '',
								'heading'    => __( 'Select Custom Thumbnail', 'ultimate_vc' ),
								'param_name' => 'custom_thumb',
								'value'      => '',
								'dependency' => array(
									'element' => 'thumbnail',
									'value'   => array( 'custom' ),
								),
								'group'      => 'Thumbnail',
							),
							array(
								'type'       => 'dropdown',
								'class'      => '',

								'heading'    => __( 'Default Thumbnail Size', 'ultimate_vc' ),
								'param_name' => 'default_thumb',
								'value'      =>
								array(
									__( 'Maximum Resolution', 'ultimate_vc' ) => 'maxresdefault',
									__( 'High Quality', 'ultimate_vc' ) => 'hqdefault',
									__( 'Medium Quality', 'ultimate_vc' ) => 'mqdefault',
									__( 'Standard Quality', 'ultimate_vc' ) => 'sddefault',
								),
								'dependency' => array(
									'element' => 'thumbnail',
									'value'   => array( 'default' ),
								),
								'group'      => 'Thumbnail',
							),
							array(
								'type'       => 'colorpicker',
								'class'      => '',
								'heading'    => __( 'Overlay Color', 'ultimate_vc' ),
								'param_name' => 'overlay_color',
								'group'      => 'Thumbnail',
							),
							array(
								'type'             => 'ult_param_heading',
								'text'             => __( 'Play Button ', 'ultimate_vc' ),
								'param_name'       => 'playb',
								'class'            => 'ult-param-heading',
								'edit_field_class' => 'ult-param-heading-wrapper vc_column vc_col-sm-12',
								'group'            => 'Thumbnail',
							),
							array(
								'type'       => 'dropdown',
								'class'      => '',

								'heading'    => __( 'Image/Icon', 'ultimate_vc' ),
								'param_name' => 'play_source',
								'value'      =>
								array(
									__( 'Default', 'ultimate_vc' ) => 'defaulticon',
									__( 'Image', 'ultimate_vc' ) => 'image',
									__( 'Icon', 'ultimate_vc' ) => 'icon',
								),
								'group'      => 'Thumbnail',
							),
							array(
								'type'       => 'ult_img_single',
								'class'      => '',
								'heading'    => __( 'Select Image', 'ultimate_vc' ),
								'param_name' => 'play_image',
								'value'      => '',
								'dependency' => array(
									'element' => 'play_source',
									'value'   => array( 'image' ),
								),
								'group'      => 'Thumbnail',
							),
							array(
								'type'       => 'icon_manager',
								'class'      => '',
								'heading'    => __( 'Select Icon ', 'ultimate_vc' ),
								'param_name' => 'play_icon',
								'value'      => '',
								'dependency' => array(
									'element' => 'play_source',
									'value'   => array( 'icon' ),
								),
								'group'      => 'Thumbnail',
							),
							array(
								'type'        => 'number',
								'class'       => '',
								'heading'     => __( 'Size', 'ultimate_vc' ),
								'param_name'  => 'play_size',
								'value'       => 75,
								'min'         => 12,
								'suffix'      => 'px',
								'description' => __( 'How big would you like it?', 'ultimate_vc' ),
								'group'       => 'Thumbnail',
							),
							array(
								'type'       => 'colorpicker',
								'class'      => '',
								'heading'    => __( 'Color', 'ultimate_vc' ),
								'value'      => '3A3A3A',
								'param_name' => 'icon_color',
								'dependency' => array(
									'element' => 'play_source',
									'value'   => array( 'icon' ),
								),
								'group'      => 'Thumbnail',
							),
							array(
								'type'       => 'colorpicker',
								'class'      => '',
								'heading'    => __( 'Hover Color', 'ultimate_vc' ),
								'param_name' => 'icon_hover_color',
								'dependency' => array(
									'element' => 'play_source',
									'value'   => array( 'icon' ),
								),
								'group'      => 'Thumbnail',
							),
							array(
								'type'       => 'colorpicker',
								'class'      => '',
								'heading'    => __( 'Background Color', 'ultimate_vc' ),
								'value'      => '#1f1f1e',
								'param_name' => 'default_color',
								'dependency' => array(
									'element' => 'play_source',
									'value'   => array( 'defaulticon' ),
								),
								'group'      => 'Thumbnail',
							),
							array(
								'type'       => 'colorpicker',
								'class'      => '',
								'heading'    => __( 'Background Hover Color', 'ultimate_vc' ),
								'param_name' => 'default_hover_color',
								'dependency' => array(
									'element' => 'play_source',
									'value'   => array( 'defaulticon' ),
								),
								'group'      => 'Thumbnail',
							),
							array(
								'type'       => 'dropdown',
								'class'      => '',
								'heading'    => __( 'Hover Animation', 'ultimate_vc' ),
								'param_name' => 'hover_animation',
								'value'      =>
								array(
									__( 'None', 'ultimate_vc' ) => 'none',
									__( 'Float', 'ultimate_vc' ) => 'float',
									__( 'Sink', 'ultimate_vc' ) => 'sink',
									__( 'Wobble Vertical', 'ultimate_vc' ) => 'wobble-vertical',
								),
								'group'      => 'Thumbnail',
							),
							array(
								'type'             => 'ult_param_heading',
								'text'             => __( 'Youtube Subscribe Bar', 'ultimate_vc' ),
								'param_name'       => 'yt_sb_bar',
								'class'            => 'ult-param-heading',
								'edit_field_class' => 'ult-param-heading-wrapper vc_column vc_col-sm-12',
								'dependency'       => array(
									'element' => 'video_type',
									'value'   => array( 'uv_iframe' ),
								),
								'group'            => 'Youtube Subscribe Bar',
							),
							array(
								'type'       => 'ult_switch',
								'class'      => '',
								'heading'    => __( 'Enable Subscribe Bar', 'ultimate_vc' ),
								'param_name' => 'enable_sub_bar',
								'value'      => 'off',
								'options'    => array(
									'on' => array(
										'label' => '',
										'off'   => __( 'No', 'ultimate_vc' ),
										'on'    => __( 'Yes', 'ultimate_vc' ),
									),
								),
								'dependency' => array(
									'element' => 'video_type',
									'value'   => array( 'uv_iframe' ),
								),
								'group'      => 'Youtube Subscribe Bar',
							),
							array(
								'type'       => 'dropdown',
								'class'      => '',
								'heading'    => __( 'Select Channel ID/Channel Name', 'ultimate_vc' ),
								'param_name' => 'chanel_id_name',
								'value'      =>
								array(
									__( 'Channel Name', 'ultimate_vc' ) => 'channel_name',
									__( 'Channel ID', 'ultimate_vc' ) => 'channel_id',
								),
								'dependency' => array(
									'element' => 'enable_sub_bar',
									'value'   => array( 'on' ),
								),
								'group'      => 'Youtube Subscribe Bar',
							),
							array(
								'type'        => 'textfield',
								'class'       => '',
								'heading'     => __( 'YouTube Channel Name', 'ultimate_vc' ),
								'param_name'  => 'yt_channel_name',
								'value'       => 'TheBrainstormForce',
								'description' => __( 'Click' ) . " <a href='https://docs.brainstormforce.com/how-to-find-youtube-channel-name-and-channel-id/' target='_blank' rel='noopener'>" . __( 'here', 'ultimate_vc' ) . '</a>' . __( ' to find your YouTube Channel Name.', 'ultimate_vc' ),
								'dependency'  => array(
									'element' => 'chanel_id_name',
									'value'   => array( 'channel_name' ),
								),
								'group'       => 'Youtube Subscribe Bar',
							),
							array(
								'type'        => 'textfield',
								'class'       => '',
								'heading'     => __( 'YouTube Channel ID', 'ultimate_vc' ),
								'param_name'  => 'yt_channel_id',
								'value'       => 'UCtFCcrvupjyaq2lax_7OQQg',
								'description' => __( 'Click' ) . " <a href='https://docs.brainstormforce.com/how-to-find-youtube-channel-name-and-channel-id/' target='_blank' rel='noopener'>" . __( 'here', 'ultimate_vc' ) . '</a>' . __( ' to find your YouTube Channel ID.', 'ultimate_vc' ),
								'dependency'  => array(
									'element' => 'chanel_id_name',
									'value'   => array( 'channel_id' ),
								),
								'group'       => 'Youtube Subscribe Bar',
							),
							array(
								'type'       => 'textfield',
								'class'      => '',
								'heading'    => __( 'Subscribe to Channel Text', 'ultimate_vc' ),
								'param_name' => 'yt_channel_text',
								'value'      => 'Subscribe to our YouTube Channel',
								'dependency' => array(
									'element' => 'enable_sub_bar',
									'value'   => array( 'on' ),
								),
								'group'      => 'Youtube Subscribe Bar',
							),
							array(
								'type'             => 'ult_param_heading',
								'text'             => __( 'Settings', 'ultimate_vc' ),
								'param_name'       => 'yt_sb_setting',
								'class'            => 'ult-param-heading',
								'edit_field_class' => 'ult-param-heading-wrapper vc_column vc_col-sm-12',
								'dependency'       => array(
									'element' => 'enable_sub_bar',
									'value'   => array( 'on' ),
								),
								'group'            => 'Youtube Subscribe Bar',
							),
							array(
								'type'       => 'ult_switch',
								'class'      => '',
								'heading'    => __( 'Show Subscribe count', 'ultimate_vc' ),
								'param_name' => 'show_sub_count',
								'value'      => 'off',
								'options'    => array(
									'on' => array(
										'label' => '',
										'off'   => __( 'No', 'ultimate_vc' ),
										'on'    => __( 'Yes', 'ultimate_vc' ),
									),
								),
								'dependency' => array(
									'element' => 'enable_sub_bar',
									'value'   => array( 'on' ),
								),
								'group'      => 'Youtube Subscribe Bar',
							),
							array(
								'type'       => 'colorpicker',
								'class'      => '',
								'heading'    => __( 'Text Color', 'ultimate_vc' ),
								'param_name' => 'yt_text_color',
								'dependency' => array(
									'element' => 'enable_sub_bar',
									'value'   => array( 'on' ),
								),
								'group'      => 'Youtube Subscribe Bar',
							),
							array(
								'type'       => 'colorpicker',
								'class'      => '',
								'heading'    => __( 'Background Color', 'ultimate_vc' ),
								'param_name' => 'yt_background_color',
								'dependency' => array(
									'element' => 'enable_sub_bar',
									'value'   => array( 'on' ),
								),
								'group'      => 'Youtube Subscribe Bar',
							),
							array(
								'type'        => 'ultimate_google_fonts',
								'heading'     => __( 'Font Family', 'ultimate_vc' ),
								'param_name'  => 'main_video_font_family',
								'description' => __( 'Select the font of your choice.', 'ultimate_vc' ) . ' ' . __( 'You can', 'ultimate_vc' ) . " <a target='_blank' rel='noopener' href='" . admin_url( 'admin.php?page=bsf-google-font-manager' ) . "'>" . __( 'add new in the collection here', 'ultimate_vc' ) . '</a>.',
								'dependency'  => array(
									'element' => 'enable_sub_bar',
									'value'   => array( 'on' ),
								),
								'group'       => 'Youtube Subscribe Bar',
							),
							array(
								'type'       => 'ultimate_google_fonts_style',
								'heading'    => __( 'Font Style', 'ultimate_vc' ),
								'param_name' => 'main_video_style',
								'dependency' => array(
									'element' => 'enable_sub_bar',
									'value'   => array( 'on' ),
								),
								'group'      => 'Youtube Subscribe Bar',
							),
							array(
								'type'       => 'ultimate_responsive',
								'class'      => 'font-size',
								'heading'    => __( 'Font size', 'ultimate_vc' ),
								'param_name' => 'main_video_font_size',
								'unit'       => 'px',
								'media'      => array(
									'Desktop'          => '',
									'Tablet'           => '',
									'Tablet Portrait'  => '',
									'Mobile Landscape' => '',
									'Mobile'           => '',
								),
								'dependency' => array(
									'element' => 'enable_sub_bar',
									'value'   => array( 'on' ),
								),
								'group'      => 'Youtube Subscribe Bar',
							),
							array(
								'type'       => 'ultimate_responsive',
								'class'      => 'font-size',
								'heading'    => __( 'Line Height', 'ultimate_vc' ),
								'param_name' => 'main_video_line_height',
								'unit'       => 'px',
								'media'      => array(
									'Desktop'          => '',
									'Tablet'           => '',
									'Tablet Portrait'  => '',
									'Mobile Landscape' => '',
									'Mobile'           => '',
								),
								'dependency' => array(
									'element' => 'enable_sub_bar',
									'value'   => array( 'on' ),
								),
								'group'      => 'Youtube Subscribe Bar',
							),
							array(
								'type'       => 'ultimate_spacing',
								'heading'    => __( 'Padding', 'ultimate_vc' ),
								'param_name' => 'padding',
								'mode'       => 'padding',
								'unit'       => 'px',
								'positions'  => array(
									__( 'Top', 'ultimate_vc' )  => '',
									__( 'Right', 'ultimate_vc' ) => '',
									__( 'Bottom', 'ultimate_vc' ) => '',
									__( 'Left', 'ultimate_vc' ) => '',
								),
								'dependency' => array(
									'element' => 'enable_sub_bar',
									'value'   => array( 'on' ),
								),
								'group'      => 'Youtube Subscribe Bar',
							),
							array(
								'type'       => 'dropdown',
								'class'      => '',
								'heading'    => __( 'Stack on', 'ultimate_vc' ),
								'param_name' => 'subscribe_bar_responsive',
								'value'      =>
								array(
									__( 'None', 'ultimate_vc' ) => 'none',
									__( 'Desktop', 'ultimate_vc' ) => 'desktop',
									__( 'Tablet', 'ultimate_vc' ) => 'tablet',
									__( 'Mobile', 'ultimate_vc' ) => 'mobile',
								),
								'dependency' => array(
									'element' => 'enable_sub_bar',
									'value'   => array( 'on' ),
								),
								'group'      => 'Youtube Subscribe Bar',
							),
							array(
								'type'             => 'css_editor',
								'heading'          => __( 'Css', 'ultimate_vc' ),
								'param_name'       => 'css_video_design',
								'group'            => __( 'Design ', 'ultimate_vc' ),
								'edit_field_class' => 'vc_col-sm-12 vc_column no-vc-background no-vc-border creative_link_css_editor',
							),
						),
					)
				);
			}
		}//end ultimate_videos_module_init()

		/**
		 * Render function for Ultimate Heading Module.
		 *
		 * @param array  $atts represts module attribuits.
		 * @param string $content value has been set to null.
		 * @access public
		 */
		public function ultimate_videos_module_shortcode( $atts, $content = null ) {
			$output                 = '';
			$html                   = '';
			$thumb                  = '';
			$video_design_style_css = '';
				$setting            = shortcode_atts(
					array(
						'u_video_url'              => 'https://www.youtube.com/watch?v=HJRzUQMhJMQ',
						'video_type'               => 'uv_iframe',
						'vimeo_video_url'          => 'https://vimeo.com/274860274',
						'thumbnail'                => '',
						'custom_thumb'             => '',
						'default_thumb'            => 'maxresdefault',
						'yt_autoplay'              => '',
						'yt_sugg_video'            => '',
						'yt_play_control'          => 'on',
						'yt_mute_control'          => '',
						'yt_modest_branding'       => '',
						'yt_privacy_mode'          => '',
						'yt_start_time'            => '',
						'yt_stop_time'             => '',
						'vimeo_autoplay'           => '',
						'vimeo_loop'               => '',
						'vimeo_intro_title'        => 'on',
						'vimeo_intro_portrait'     => 'on',
						'vimeo_intro_byline'       => 'on',
						'vimeo_start_time'         => '',
						'play_source'              => 'defaulticon',
						'aspect_ratio'             => '16_9',
						'play_icon'                => '',
						'hover_animation'          => 'none',
						'play_image'               => '',
						'play_size'                => '75',
						'vimeo_control_color'      => '',
						'overlay_color'            => '',
						'icon_color'               => '#3A3A3A',
						'icon_hover_color'         => '',
						'default_color'            => '#1f1f1e',
						'default_hover_color'      => '',
						'enable_sub_bar'           => 'off',
						'chanel_id_name'           => 'channel_name',
						'yt_channel_name'          => 'TheBrainstormForce',
						'yt_channel_id'            => 'UCtFCcrvupjyaq2lax_7OQQg',
						'yt_channel_text'          => 'Subscribe to our YouTube Channel',
						'show_sub_count'           => '',
						'yt_text_color'            => '#fff',
						'yt_background_color'      => '#1b1b1b',
						'main_video_font_family'   => '',
						'main_video_style'         => '',
						'main_video_font_size'     => '',
						'main_video_line_height'   => '',
						'el_class'                 => '',
						'padding'                  => '',
						'subscribe_bar_responsive' => 'none',
						'css_video_design'         => '',
					),
					$atts
				);
			$vc_version             = ( defined( 'WPB_VC_VERSION' ) ) ? WPB_VC_VERSION : 0;
			$is_vc_49_plus          = ( version_compare( 4.9, $vc_version, '<=' ) ) ? 'ult-adjust-bottom-margin' : '';

			if ( '' == $setting['u_video_url'] && 'uv_iframe' == $setting['video_type'] ) {
				return '';
			}
			if ( '' == $setting['vimeo_video_url'] && 'vimeo_video' == $setting['video_type'] ) {
				return '';
			}

			$video_design_style_css = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $setting['css_video_design'], ' ' ), 'ultimate_videos', $atts );
			$video_design_style_css = esc_attr( $video_design_style_css );

			$micro   = wp_rand( 0000, 9999 );
			$id      = uniqid( 'ultimate-video-' . $micro );
			$uid     = 'ultv-' . wp_rand( 0000, 9999 );
			$output  = '<div id="' . esc_attr( $id ) . '" class="ult-video ' . esc_attr( $video_design_style_css ) . ' ' . esc_attr( $is_vc_49_plus ) . ' ' . esc_attr( $id ) . ' ' . esc_attr( $uid ) . ' ' . esc_attr( $setting['el_class'] ) . '">';
			$output .= $this->get_video_embed( $setting, $uid );
			$output .= '</div>';

			return $output;

		}//end ultimate_videos_module_shortcode()
	}//end class
	new Ultimate_VC_Addons_Videos();
	if ( class_exists( 'WPBakeryShortCode' ) && ! class_exists( 'WPBakeryShortCode_Ultimate_Video' ) ) {
		/**
		 * Function that checks if the class is exists or not.
		 */
		class WPBakeryShortCode_Ultimate_Video extends WPBakeryShortCode {
		}
	}
}
