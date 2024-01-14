<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
if ( ! class_exists( 'RSFunctionForSocialRewards' ) ) {

	class RSFunctionForSocialRewards {

		public static function init() {
			if ( '1' == get_option( 'rs_global_position_sumo_social_buttons' ) ) {

				add_action( 'woocommerce_before_single_product', array( __CLASS__, 'social_buttons_for_products' ) );
			} elseif ( '2' == get_option( 'rs_global_position_sumo_social_buttons' ) ) {

				add_action( 'woocommerce_before_single_product_summary', array( __CLASS__, 'social_buttons_for_products' ) );
			} elseif ( '3' == get_option( 'rs_global_position_sumo_social_buttons' ) ) {

				add_action( 'woocommerce_single_product_summary', array( __CLASS__, 'social_buttons_for_products' ) );
			} elseif ( '4' == get_option( 'rs_global_position_sumo_social_buttons' ) ) {

				add_action( 'woocommerce_after_single_product', array( __CLASS__, 'social_buttons_for_products' ) );
			} elseif ( '6' == get_option( 'rs_global_position_sumo_social_buttons' ) ) {

				add_action( 'woocommerce_product_meta_end', array( __CLASS__, 'social_buttons_for_products' ) );
			} else {
				add_action( 'woocommerce_after_single_product_summary', array( __CLASS__, 'social_buttons_for_products' ) );
			}

			if ( '2' == get_option( 'rs_global_position_sumo_social_share_buttons' ) ) {
				add_action( 'get_footer', array( __CLASS__, 'social_buttons_for_post_and_page' ) );
			} else {
				add_action( 'loop_start', array( __CLASS__, 'social_buttons_for_post_and_page' ) );
			}
		}

		public static function localized_values_for_script( $post, $type, $data ) {
			$FBLikeScript       = array();
			$FBShareScript      = array();
			$TweetScript        = array();
			$InstaFollowScript  = array();
			$VKLikeScript       = array();
			$GplusScript        = array();
			$OKruScript         = array();
			$FollowScript       = array();
			$social_reward_data = array(
				'fblike_point'        => RS_Rewardsystem_Shortcodes::shortcode_for_social_actions( 'facebook_like_reward_points' ),
				'fbshare_point'       => RS_Rewardsystem_Shortcodes::shortcode_for_social_actions( 'facebook_share_reward_points' ),
				'tweet_point'         => RS_Rewardsystem_Shortcodes::shortcode_for_social_actions( 'twitter_tweet_reward_points' ),
				'tweet_follow_point'  => RS_Rewardsystem_Shortcodes::shortcode_for_social_actions( 'twitter_follow_reward_points' ),
				'instagram_points'    => RS_Rewardsystem_Shortcodes::shortcode_for_social_actions( 'instagram_reward_points' ),
				'vk_points'           => RS_Rewardsystem_Shortcodes::shortcode_for_social_actions( 'vk_reward_points' ),
				'google_share_points' => RS_Rewardsystem_Shortcodes::shortcode_for_social_actions( 'google_share_reward_points' ),
				'ok_share_points'     => RS_Rewardsystem_Shortcodes::shortcode_for_social_actions( 'ok_share_reward_points' ),
			);
			$LocalizedScript    = array(
				'ajaxurl'                => SRP_ADMIN_AJAX_URL,
				'post_id'                => $post->ID,
				'buttonlanguage'         => get_option( 'rs_language_selection_for_button' ),
				'wplanguage'             => get_option( 'WPLANG' ),
				'type'                   => $type,
				'fbappid'                => get_option( 'rs_facebook_application_id' ),
				'vkappid'                => get_option( 'rs_vk_application_id' ),
				'showfblike'             => get_option( 'rs_global_show_hide_facebook_like_button' ),
				'showfbshare'            => get_option( 'rs_global_show_hide_facebook_share_button' ),
				'showtweet'              => get_option( 'rs_global_show_hide_twitter_tweet_button' ),
				'showtwitterfollow'      => get_option( 'rs_global_show_hide_twitter_follow_tweet_button' ),
				'showgplus'              => get_option( 'rs_global_show_hide_google_plus_button' ),
				'showvk'                 => get_option( 'rs_global_show_hide_vk_button' ),
				'showinstagram'          => get_option( 'rs_global_show_hide_instagram_button' ),
				'instagram_button_type'  => get_option( 'rs_social_button_instagram', '1' ),
				'instagram_profile_name' => get_option( 'rs_instagram_profile_name' ),
				'showok'                 => get_option( 'rs_global_show_hide_ok_button' ),
				'fblike_point'           => $social_reward_data['fblike_point'],
				'fbshare_point'          => $social_reward_data['fbshare_point'],
				'tweet_point'            => $social_reward_data['tweet_point'],
				'tweet_follow_point'     => $social_reward_data['tweet_follow_point'],
				'instagram_points'       => $social_reward_data['instagram_points'],
				'vk_points'              => $social_reward_data['vk_points'],
				'google_share_points'    => $social_reward_data['google_share_points'],
				'ok_share_points'        => $social_reward_data['ok_share_points'],
			);

			if ( '' != get_option( 'rs_facebook_application_id' ) ) {
				if ( '1' == get_option( 'rs_global_show_hide_facebook_like_button' ) && ! empty( $social_reward_data['fblike_point'] ) ) {
					$AllowFBlike  = allow_points_for_social_action( get_current_user_id(), 'fb_like_count_per_day', get_option( 'rs_enable_fblike_restriction' ), get_option( 'rs_no_of_fblike_count' ) );
					$FBLikeScript = array(
						'fb_like'                => wp_create_nonce( 'fb-like' ),
						'allowfblike'            => $AllowFBlike,
						'fbliketooltip'          => get_option( 'rs_global_show_hide_social_tooltip_for_facebook' ),
						'fbliketooltipmsg'       => do_shortcode( get_option( 'rs_social_message_for_facebook' ) ),
						'fbliketooltipclassname' => get_option( 'rs_social_button_like' ) == '1' ? 'fb-like' : 'rs_custom_fblike_button',
						'is_tooltip_enqueued'    => get_option( 'rs_reward_point_enable_tipsy_social_rewards' ),
					);
				}
				if ( '1' == get_option( 'rs_global_show_hide_facebook_share_button' ) && ! empty( $social_reward_data['fbshare_point'] ) ) {
					$AllowFBShare  = allow_points_for_social_action( get_current_user_id(), 'fb_share_count_per_day', get_option( 'rs_enable_fbshare_restriction' ), get_option( 'rs_no_of_fbshare_count' ) );
					$PostImage     = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ) );
					$URL           = '1' == get_option( 'rs_global_social_facebook_share_url' ) ? get_permalink() : get_option( 'rs_global_social_facebook_share_url_custom' );
					$Classname     = is_product() ? 'share_wrapper1' : 'share_wrapper11';
					$FBShareScript = array(
						'fb_share'                => wp_create_nonce( 'fb-share' ),
						'allowfbshare'            => $AllowFBShare,
						'post_title'              => $post->post_title,
						'post_desc'               => $post->post_content,
						'post_url'                => $URL,
						'post_caption'            => $post->post_excerpt,
						'post_image'              => isset( $PostImage[0] ) ? $PostImage[0] : '',
						'fbsharetooltip'          => get_option( 'rs_global_show_hide_social_tooltip_for_facebook_share' ),
						'fbsharetooltipmsg'       => do_shortcode( get_option( 'rs_social_message_for_facebook_share' ) ),
						'fbsharetooltipclassname' => '1' == get_option( 'rs_social_button_share' ) ? $Classname : 'rs_custom_fbshare_button',
					);
				}
			}
			if ( '1' == get_option( 'rs_global_show_hide_twitter_tweet_button' ) && ! empty( $social_reward_data['tweet_point'] ) ) {
				$AllowTweet  = allow_points_for_social_action( get_current_user_id(), 'twitter_tweet_count_per_day', get_option( 'rs_enable_tweet_restriction' ), get_option( 'rs_no_of_tweet_count' ) );
				$TweetScript = array(
					'twitter_tweet'         => wp_create_nonce( 'twitter-tweet' ),
					'allowtweet'            => $AllowTweet,
					'tweettooltip'          => get_option( 'rs_global_show_hide_social_tooltip_for_twitter' ),
					'tweettooltipmsg'       => do_shortcode( get_option( 'rs_social_message_for_twitter' ) ),
					'tweettooltipclassname' => '1' == get_option( 'rs_social_button_tweet' ) ? 'rstwitter-button-msg' : 'rs_custom_tweet_button',
				);
			}
			if ( '1' == get_option( 'rs_global_show_hide_twitter_follow_tweet_button' ) && '' != get_option( 'rs_global_social_twitter_profile_name' ) && ! empty( $social_reward_data['tweet_follow_point'] ) ) {
				$AllowFollow  = allow_points_for_social_action( get_current_user_id(), 'twitter_follow_count_per_day', get_option( 'rs_enable_twitter_follow_restriction' ), get_option( 'rs_no_of_twitter_follow_count' ) );
				$FollowScript = array(
					'twitter_follow'         => wp_create_nonce( 'twitter-follow' ),
					'allowfollow'            => $AllowFollow,
					'followtooltip'          => get_option( 'rs_global_show_hide_social_tooltip_for_twitter_follow' ),
					'followtooltipmsg'       => do_shortcode( get_option( 'rs_social_message_for_twitter_follow' ) ),
					'followtooltipclassname' => '1' == get_option( 'rs_social_button_twitter_follow' ) ? 'rstwitterfollow-button-msg' : 'rs_custom_tweetfollow_button',
				);
			}
			if ( '1' == get_option( 'rs_global_show_hide_instagram_button' ) && '' != get_option( 'rs_instagram_profile_name' ) && ! empty( $social_reward_data['instagram_points'] ) ) {
				$AllowInstaFollow  = allow_points_for_social_action( get_current_user_id(), 'instagram_count_per_day', get_option( 'rs_enable_instagram_restriction' ), get_option( 'rs_no_of_instagram_count' ) );
				$Classname         = is_product() ? 'instagram_button' : 'instagram_button_post';
				$InstaFollowScript = array(
					'instagram_follow'      => wp_create_nonce( 'instagram-follow' ),
					'allowinstagramfollow'  => $AllowInstaFollow,
					'instagramtooltip'      => get_option( 'rs_global_show_hide_social_tooltip_for_instagram' ),
					'instagramtooltipmsg'   => do_shortcode( get_option( 'rs_social_message_for_instagram' ) ),
					'instatooltipclassname' => '1' == get_option( 'rs_social_button_instagram' ) ? $Classname : 'rs_custom_instagram_button',
				);
			}
			if ( '1' == get_option( 'rs_global_show_hide_vk_button' ) && '' != get_option( 'rs_vk_application_id' ) && ! empty( $social_reward_data['vk_points'] ) ) {
				$AllowVKLike  = allow_points_for_social_action( get_current_user_id(), 'vk_like_count_per_day', get_option( 'rs_enable_vk_restriction' ), get_option( 'rs_no_of_vk_count' ) );
				$VKLikeScript = array(
					'vk_like'            => wp_create_nonce( 'vk-like' ),
					'allowvklike'        => $AllowVKLike,
					'vktooltip'          => get_option( 'rs_global_show_hide_social_tooltip_for_vk' ),
					'vktooltipmsg'       => do_shortcode( get_option( 'rs_social_message_for_vk' ) ),
					'vktooltipclassname' => '1' == get_option( 'rs_social_button_vk_like' ) ? 'vk-like' : 'rs_custom_vklike_button',
				);
			}
			if ( '1' == get_option( 'rs_global_show_hide_google_plus_button' ) && ! empty( $social_reward_data['google_share_points'] ) ) {
				$AllowGplus  = allow_points_for_social_action( get_current_user_id(), 'gplus_share_count_per_day', get_option( 'rs_enable_gplus_restriction' ), get_option( 'rs_no_of_gplus_count' ) );
				$GplusScript = array(
					'gplus_share'           => wp_create_nonce( 'gplus-share' ),
					'allowgplus'            => $AllowGplus,
					'gplustooltip'          => get_option( 'rs_global_show_hide_social_tooltip_for_google' ),
					'gplustooltipmsg'       => do_shortcode( get_option( 'rs_social_message_for_google_plus' ) ),
					'gplustooltipclassname' => '1' == get_option( 'rs_social_button_gplus' ) ? 'google-plus-one' : 'rs_custom_gplus_button',
				);
			}
			if ( '1' == get_option( 'rs_global_show_hide_ok_button' ) && ! empty( $social_reward_data['ok_share_points'] ) ) {
				$AllowOKru  = allow_points_for_social_action( get_current_user_id(), 'ok_follow_count_per_day', get_option( 'rs_enable_ok_restriction' ), get_option( 'rs_no_of_ok_count' ) );
				$URL        = '1' == get_option( 'rs_global_social_ok_url' ) ? get_permalink() : get_option( 'rs_global_social_ok_url_custom' );
				$OKruScript = array(
					'url'                => $URL,
					'okru_share'         => wp_create_nonce( 'okru-share' ),
					'allowokru'          => $AllowOKru,
					'oktooltip'          => get_option( 'rs_global_show_hide_social_tooltip_for_ok_follow' ),
					'oktooltipmsg'       => do_shortcode( get_option( 'rs_social_message_for_ok_follow' ) ),
					'oktooltipclassname' => '1' == get_option( 'rs_social_button_ok_ru' ) ? 'ok-share-button' : 'rs_custom_ok_button',
				);
			}
			$MergedScript = array_merge( $LocalizedScript, $FBLikeScript, $FBShareScript, $TweetScript, $FollowScript, $InstaFollowScript, $VKLikeScript, $GplusScript, $OKruScript );
			return $MergedScript;
		}

		public static function social_buttons_for_post_and_page() {
			$did_action = 1 == get_option( 'rs_global_position_sumo_social_share_buttons' ) ? did_action( 'loop_start' ) : did_action( 'get_footer' );
			if ( $did_action > 1 ) {
				return;
			}

			if ( ! is_user_logged_in() ) {
				return;
			}

			$UserId      = get_current_user_id();
			$BanningType = check_banning_type( $UserId );
			if ( 'earningonly' == $BanningType || 'both' == $BanningType ) {
				return;
			}

			if ( '2' == get_option( 'rs_global_social_enable_disable_reward_post' ) ) {
				return;
			}

			if ( is_shop() || is_cart() || is_checkout() || is_product() || is_account_page() || is_product_category() ) {
				return;
			}

			global $post;
			if ( ! $post ) {
				return;
			}

			/**
						 * Hook:rs_display_social_icon_in_post_or_page.
						 *
						 * @since 1.0
						 */
			if ( ! apply_filters( 'rs_display_social_icon_in_post_or_page', true, $post ) ) {
				return;
			}

			// Return if validation is false.
			if ( ! self::validate_pages_and_posts_filter() ) {
				return;
			}

			$social_reward_data = array(
				'fblike_point'        => RS_Rewardsystem_Shortcodes::shortcode_for_social_actions( 'facebook_like_reward_points' ),
				'fbshare_point'       => RS_Rewardsystem_Shortcodes::shortcode_for_social_actions( 'facebook_share_reward_points' ),
				'tweet_point'         => RS_Rewardsystem_Shortcodes::shortcode_for_social_actions( 'twitter_tweet_reward_points' ),
				'tweet_follow_point'  => RS_Rewardsystem_Shortcodes::shortcode_for_social_actions( 'twitter_follow_reward_points' ),
				'instagram_points'    => RS_Rewardsystem_Shortcodes::shortcode_for_social_actions( 'instagram_reward_points' ),
				'vk_points'           => RS_Rewardsystem_Shortcodes::shortcode_for_social_actions( 'vk_reward_points' ),
				'google_share_points' => RS_Rewardsystem_Shortcodes::shortcode_for_social_actions( 'google_share_reward_points' ),
				'ok_share_points'     => RS_Rewardsystem_Shortcodes::shortcode_for_social_actions( 'ok_share_reward_points' ),
			);

			$OldData = array(
				'fblike'    => get_user_meta( $UserId, '_rsfacebooklikes_post', true ),
				'fbshare'   => get_user_meta( $UserId, '_rsfacebookshare_post', true ),
				'tweet'     => get_user_meta( $UserId, '_rstwittertweet_post', true ),
				'follow'    => get_user_meta( $UserId, '_rstwitterfollow_post', true ),
				'okfollow'  => get_user_meta( $UserId, '_rsokfollow_post', true ),
				'gplus'     => get_user_meta( $UserId, '_rsgoogleshares_post', true ),
				'vklike'    => get_user_meta( $UserId, '_rsvklike_post', true ),
				'instagram' => get_user_meta( $UserId, '_rsinstagram_post', true ),
			);
			wp_enqueue_script( 'fp_social_action', SRP_PLUGIN_DIR_URL . 'includes/frontend/js/modules/fp-social-action-frontend.js', array(), SRP_VERSION );
			$LocalizedScript = self::localized_values_for_script( $post, 'postorpage', $OldData );
			wp_localize_script( 'fp_social_action', 'fp_social_action_params', $LocalizedScript );
			if ( '' != get_option( 'rs_facebook_application_id' ) && ( '1' == get_option( 'rs_global_show_hide_facebook_like_button' ) || '1' == get_option( 'rs_global_show_hide_facebook_share_button' ) ) ) {
				?>
				<div id="fb-root"></div>
				<?php
			}
			if ( '1' == get_option( 'rs_global_show_hide_google_plus_button' ) ) {
								wp_enqueue_script( 'fp_gplus_social_icon', 'https://apis.google.com/js/plusone.js', array(), SRP_VERSION );
			}
			?>

			<table class="rs_social_sharing_buttons">
				<tr>
					<?php
					if ( '' != get_option( 'rs_facebook_application_id' ) ) {
						if ( '1' == get_option( 'rs_global_show_hide_facebook_like_button' ) && ! empty( $social_reward_data['fblike_point'] ) ) {
							self::fb_like_button();
						}
						if ( '1' == get_option( 'rs_global_show_hide_facebook_share_button' ) && ! empty( $social_reward_data['fbshare_point'] ) ) {
							self::fb_share_button();
						}
					}
					if ( '1' == get_option( 'rs_global_show_hide_twitter_tweet_button' ) && ! empty( $social_reward_data['tweet_point'] ) ) {
						self::tweet_button();
					}
					if ( '1' == get_option( 'rs_global_show_hide_twitter_follow_tweet_button' ) && '' != get_option( 'rs_global_social_twitter_profile_name' ) ) {
						self::twitter_follow_button();
					}
					if ( '1' == get_option( 'rs_global_show_hide_google_plus_button' ) && ! empty( $social_reward_data['google_share_points'] ) ) {
						self::gplus_share_button();
					}
					if ( '1' == get_option( 'rs_global_show_hide_vk_button' ) && '' != get_option( 'rs_vk_application_id' ) && ! empty( $social_reward_data['vk_points'] ) ) {
						self::vk_like_button();
					}
					if ( '1' == get_option( 'rs_global_show_hide_instagram_button' ) && '' != get_option( 'rs_instagram_profile_name' ) && ! empty( $social_reward_data['instagram_points'] ) ) {
						self::instagram_follow_button();
					}
					if ( '1' == get_option( 'rs_global_show_hide_ok_button' ) && ! empty( $social_reward_data['ok_share_points'] ) ) {
						self::ok_share_button();
					}
					?>
				</tr>
			</table>
			<div class="social_promotion_success_message"></div>
			<?php
		}

		public static function validate_pages_and_posts_filter() {

			global $post;
			if ( ! is_object( $post ) ) {
				return false;
			}

			$page_selection_type = get_option( 'rs_global_social_promotion_selection_type', 1 );
			switch ( $page_selection_type ) {

				case '2':
					$include_pages_and_posts = get_option( 'rs_include_posts_and_pages', array() );
					return srp_check_is_array( $include_pages_and_posts ) ? in_array( $post->ID, $include_pages_and_posts ) : false;
					break;
				case '3':
					$exclude_pages_and_posts = get_option( 'rs_exclude_posts_and_pages', array() );
					return srp_check_is_array( $exclude_pages_and_posts ) ? ! in_array( $post->ID, $exclude_pages_and_posts ) : false;
					break;
				default:
					return true;
					break;
			}

			return true;
		}

		public static function social_buttons_for_products() {
			if ( ! is_user_logged_in() ) {
				return;
			}

			$UserId      = get_current_user_id();
			$BanningType = check_banning_type( $UserId );
			if ( 'earningonly' == $BanningType || 'both' == $BanningType ) {
				return;
			}

			global $post;
			if ( ! $post ) {
				return;
			}
			if ( 'no' == get_option( 'rs_enable_product_category_level_for_social_reward' ) ) {
				$Options        = array(
					'applicable_for'      => get_option( 'rs_social_reward_global_level_applicable_for' ),
					'included_products'   => get_option( 'rs_include_products_for_social_reward' ),
					'excluded_products'   => get_option( 'rs_exclude_products_for_social_reward' ),
					'included_categories' => get_option( 'rs_include_particular_categories_for_social_reward' ),
					'excluded_categories' => get_option( 'rs_exclude_particular_categories_for_social_reward' ),
				);
				$product_filter = '2' == srp_product_filter_for_quick_setup( $post->ID, $post->ID, $Options ) ? true : false;
				$product_filter = ( '1' == get_option( 'rs_global_social_enable_disable_reward' ) ) ? $product_filter : false;
			} elseif ( 'yes' == get_option( 'rs_enable_product_category_level_for_social_reward' ) ) {
				$product_filter = ( 'yes' == get_post_meta( @$post->ID, '_socialrewardsystemcheckboxvalue', true ) );
			}

			$social_reward_data = array(
				'fblike_point'        => RS_Rewardsystem_Shortcodes::shortcode_for_social_actions( 'facebook_like_reward_points' ),
				'fbshare_point'       => RS_Rewardsystem_Shortcodes::shortcode_for_social_actions( 'facebook_share_reward_points' ),
				'tweet_point'         => RS_Rewardsystem_Shortcodes::shortcode_for_social_actions( 'twitter_tweet_reward_points' ),
				'tweet_follow_point'  => RS_Rewardsystem_Shortcodes::shortcode_for_social_actions( 'twitter_follow_reward_points' ),
				'instagram_points'    => RS_Rewardsystem_Shortcodes::shortcode_for_social_actions( 'instagram_reward_points' ),
				'vk_points'           => RS_Rewardsystem_Shortcodes::shortcode_for_social_actions( 'vk_reward_points' ),
				'google_share_points' => RS_Rewardsystem_Shortcodes::shortcode_for_social_actions( 'google_share_reward_points' ),
				'ok_share_points'     => RS_Rewardsystem_Shortcodes::shortcode_for_social_actions( 'ok_share_reward_points' ),
			);

			if ( ! $product_filter ) {
				return;
			}

			$array_social = array();
			if ( '1' === get_option( 'rs_global_show_hide_facebook_like_button' ) && ! empty( $social_reward_data['fblike_point'] ) ) {
				$array_social['fb_like'] = 'show';
			}
			if ( '1' === get_option( 'rs_global_show_hide_facebook_share_button' ) && ! empty( $social_reward_data['fbshare_point'] ) ) {
				$array_social['fb_share'] = 'show';
			}
			if ( '1' === get_option( 'rs_global_show_hide_twitter_tweet_button' ) && ! empty( $social_reward_data['tweet_point'] ) ) {
				$array_social['twitter'] = 'show';
			}
			if ( '1' === get_option( 'rs_global_show_hide_twitter_follow_tweet_button' ) ) {
				if ( '' !== get_option( 'rs_global_social_twitter_profile_name' ) && ! empty( $social_reward_data['tweet_follow_point'] ) ) {
					$array_social['twitter_follow'] = 'show';
				}
			}
			if ( '1' === get_option( 'rs_global_show_hide_google_plus_button' ) && ! empty( $social_reward_data['google_share_points'] ) ) {
				$array_social['google_share'] = 'show';
			}
			if ( '1' === get_option( 'rs_global_show_hide_vk_button' ) && ! empty( $social_reward_data['vk_points'] ) ) {
				$array_social['vk_like'] = 'show';
			}
			if ( '1' === get_option( 'rs_global_show_hide_instagram_button' ) ) {
				if ( '' !== get_option( 'rs_instagram_profile_name' ) && ! empty( $social_reward_data['instagram_points'] ) ) {
					$array_social['instagram'] = 'show';
				}
			}
			if ( '1' === get_option( 'rs_global_show_hide_ok_button' ) && ! empty( $social_reward_data['ok_share_points'] ) ) {
				$array_social['ok_share'] = 'show';
			}

			$OldData = array(
				'fblike'    => get_user_meta( $UserId, '_rsfacebooklikes', true ),
				'fbshare'   => get_user_meta( $UserId, '_rsfacebookshare', true ),
				'tweet'     => get_user_meta( $UserId, '_rstwittertweet', true ),
				'follow'    => get_user_meta( $UserId, '_rstwitterfollow', true ),
				'okfollow'  => get_user_meta( $UserId, '_rsokfollow', true ),
				'gplus'     => get_user_meta( $UserId, '_rsgoogleshares', true ),
				'vklike'    => get_user_meta( $UserId, '_rsvklike', true ),
				'instagram' => get_user_meta( $UserId, '_rsinstagram', true ),
			);
			wp_enqueue_script( 'fp_social_action', SRP_PLUGIN_DIR_URL . 'includes/frontend/js/modules/fp-social-action-frontend.js', array(), SRP_VERSION );
			$LocalizedScript = self::localized_values_for_script( $post, 'product', $OldData );
			wp_localize_script( 'fp_social_action', 'fp_social_action_params', $LocalizedScript );
			if ( '' !== get_option( 'rs_facebook_application_id' ) && ( '1' === get_option( 'rs_global_show_hide_facebook_like_button' ) || '1' == get_option( 'rs_global_show_hide_facebook_share_button' ) ) ) {
				?>
				<div id="fb-root"></div>
				<?php
			}
			if ( '1' === get_option( 'rs_global_show_hide_google_plus_button' ) ) {
				wp_enqueue_script( 'fp_gplus_social_icon', 'https://apis.google.com/js/plusone.js', array(), SRP_VERSION );
			}

			if ( srp_check_is_array( $array_social ) && count( $array_social ) < 6 ) {
				?>
				<table class="rs_social_sharing_buttons">
					<?php if ( '1' === get_option( 'rs_display_position_social_buttons' ) ) { ?>
						<tr>
							<?php
							if ( '' !== get_option( 'rs_facebook_application_id' ) ) {
								if ( '1' === get_option( 'rs_global_show_hide_facebook_like_button' ) && ! empty( $social_reward_data['fblike_point'] ) ) {
									self::fb_like_button();
								}

								if ( '1' === get_option( 'rs_global_show_hide_facebook_share_button' ) && ! empty( $social_reward_data['fbshare_point'] ) ) {
									self::fb_share_button();
								}
							}
							if ( '1' === get_option( 'rs_global_show_hide_twitter_tweet_button' ) && ! empty( $social_reward_data['tweet_point'] ) ) {
								self::tweet_button();
							}

							if ( '1' === get_option( 'rs_global_show_hide_twitter_follow_tweet_button' ) && '' != get_option( 'rs_global_social_twitter_profile_name' ) && ! empty( $social_reward_data['tweet_follow_point'] ) ) {
								self::twitter_follow_button();
							}

							if ( '1' === get_option( 'rs_global_show_hide_google_plus_button' ) && ! empty( $social_reward_data['google_share_points'] ) ) {
								self::gplus_share_button();
							}

							if ( '' !== get_option( 'rs_vk_application_id' ) && '1' == get_option( 'rs_global_show_hide_vk_button' ) && ! empty( $social_reward_data['vk_points'] ) ) {
								self::vk_like_button();
							}

							if ( '' !== get_option( 'rs_instagram_profile_name' ) && '1' == get_option( 'rs_global_show_hide_instagram_button' ) && ! empty( $social_reward_data['instagram_points'] ) ) {
								self::instagram_follow_button();
							}

							if ( '1' === get_option( 'rs_global_show_hide_ok_button' ) && ! empty( $social_reward_data['ok_share_points'] ) ) {
								self::ok_share_button();
							}
							?>
						</tr>
						<?php
					} else {
						if ( '' !== get_option( 'rs_facebook_application_id' ) ) {
							if ( '1' === get_option( 'rs_global_show_hide_facebook_like_button' ) && ! empty( $social_reward_data['fblike_point'] ) ) {
								?>
								<tr>
									<?php self::fb_like_button(); ?>
								</tr>
								<?php
							}
							if ( '1' === get_option( 'rs_global_show_hide_facebook_share_button' ) && ! empty( $social_reward_data['fbshare_point'] ) ) {
								?>
								<tr>
									<?php self::fb_share_button(); ?>
								</tr>
								<?php
							}
						}
						if ( '1' === get_option( 'rs_global_show_hide_twitter_tweet_button' ) && ! empty( $social_reward_data['tweet_point'] ) ) {
							?>
							<tr>
								<?php self::tweet_button(); ?>
							</tr>
							<?php
						}
						if ( '' !== get_option( 'rs_global_social_twitter_profile_name' ) && '1' == get_option( 'rs_global_show_hide_twitter_follow_tweet_button' ) && ! empty( $social_reward_data['tweet_follow_point'] ) ) {
							?>
							<tr class="twitter_follow_btn">
								<?php self::twitter_follow_button(); ?>
							</tr>
							<?php
						}
						if ( '1' === get_option( 'rs_global_show_hide_google_plus_button' ) && ! empty( $social_reward_data['google_share_points'] ) ) {
							?>
							<tr>
								<?php self::gplus_share_button(); ?>
							</tr>
							<?php
						}
						if ( '' !== get_option( 'rs_vk_application_id' ) && '1' === get_option( 'rs_global_show_hide_vk_button' ) && ! empty( $social_reward_data['vk_points'] ) ) {
							?>
							<tr>
								<?php self::vk_like_button(); ?>
							</tr>
							<?php
						}
						if ( '' !== get_option( 'rs_instagram_profile_name' ) && '1' === get_option( 'rs_global_show_hide_instagram_button' ) && ! empty( $social_reward_data['instagram_points'] ) ) {
							?>
							<tr>
								<?php self::instagram_follow_button(); ?>
							</tr>
							<?php
						}
						if ( '1' === get_option( 'rs_global_show_hide_ok_button' ) && ! empty( $social_reward_data['ok_share_points'] ) ) {
							?>
							<tr>
								<?php self::ok_share_button(); ?>
							</tr>
							<?php
						}
					}
					?>
				</table>
				<?php
			} elseif ( '1' === get_option( 'rs_display_position_social_buttons' ) ) {
				?>
					<table class="rs_social_sharing_buttons">
						<tr>
							<?php
							if ( '' !== get_option( 'rs_facebook_application_id' ) ) {
								if ( '1' === get_option( 'rs_global_show_hide_facebook_like_button' ) && ! empty( $social_reward_data['fblike_point'] ) ) {
									self::fb_like_button();
								}

								if ( '1' === get_option( 'rs_global_show_hide_facebook_share_button' ) && ! empty( $social_reward_data['fbshare_point'] ) ) {
									self::fb_share_button();
								}
							}
							if ( '1' === get_option( 'rs_global_show_hide_twitter_tweet_button' ) && ! empty( $social_reward_data['tweet_point'] ) ) {
								self::tweet_button();
							}

							if ( '' !== get_option( 'rs_global_social_twitter_profile_name' ) && '1' == get_option( 'rs_global_show_hide_twitter_follow_tweet_button' ) && ! empty( $social_reward_data['tweet_follow_point'] ) ) {
								self::twitter_follow_button();
							}

							if ( '1' === get_option( 'rs_global_show_hide_google_plus_button' ) && ! empty( $social_reward_data['google_share_points'] ) ) {
								self::gplus_share_button();
							}
							?>
						</tr>
					</table>
					<table class="rs_social_sharing_buttons">
						<tr>
							<?php
							if ( '' !== get_option( 'rs_vk_application_id' ) && '1' === get_option( 'rs_global_show_hide_vk_button' ) && ! empty( $social_reward_data['vk_points'] ) ) {
								self::vk_like_button();
							}

							if ( '' !== get_option( 'rs_instagram_profile_name' ) && '1' === get_option( 'rs_global_show_hide_instagram_button' ) && ! empty( $social_reward_data['instagram_points'] ) ) {
								self::instagram_follow_button();
							}

							if ( '1' === get_option( 'rs_global_show_hide_ok_button' ) && ! empty( $social_reward_data['ok_share_points'] ) ) {
								self::ok_share_button();
							}
							?>
						</tr>
					</table>
					<?php
			} else {
				?>
					<table class="rs_social_sharing_buttons">
					<?php
					if ( '' !== get_option( 'rs_facebook_application_id' ) ) {
						if ( '1' === get_option( 'rs_global_show_hide_facebook_like_button' ) && ! empty( $social_reward_data['fblike_point'] ) ) {
							?>
								<tr>
								<?php self::fb_like_button(); ?>
								</tr>
								<?php
						}
						if ( '1' === get_option( 'rs_global_show_hide_facebook_share_button' ) && ! empty( $social_reward_data['fbshare_point'] ) ) {
							?>
								<tr>
								<?php self::fb_share_button(); ?>
								</tr>
								<?php
						}
					}
					if ( '1' === get_option( 'rs_global_show_hide_twitter_tweet_button' ) && ! empty( $social_reward_data['tweet_point'] ) ) {
						?>
							<tr>
							<?php self::tweet_button(); ?>
							</tr>
							<?php
					}
					if ( '' !== get_option( 'rs_global_social_twitter_profile_name' ) && '1' == get_option( 'rs_global_show_hide_twitter_follow_tweet_button' ) && ! empty( $social_reward_data['tweet_follow_point'] ) ) {
						?>
							<tr class="twitter_follow_btn">
							<?php self::twitter_follow_button(); ?>
							</tr>
							<?php
					}
					if ( '1' === get_option( 'rs_global_show_hide_google_plus_button' ) && ! empty( $social_reward_data['google_share_points'] ) ) {
						?>
							<tr>
							<?php self::gplus_share_button(); ?>
							</tr>
							<?php
					}
					if ( '' !== get_option( 'rs_vk_application_id' ) && '1' === get_option( 'rs_global_show_hide_vk_button' ) && ! empty( $social_reward_data['vk_points'] ) ) {
						?>
							<tr>
							<?php self::vk_like_button(); ?>
							</tr>
							<?php
					}
					if ( '' !== get_option( 'rs_instagram_profile_name' ) && '1' === get_option( 'rs_global_show_hide_instagram_button' ) && ! empty( $social_reward_data['instagram_points'] ) ) {
						?>
							<tr>
							<?php self::instagram_follow_button(); ?>
							</tr>
							<?php
					}
					if ( '1' === get_option( 'rs_global_show_hide_ok_button' ) && ! empty( $social_reward_data['ok_share_points'] ) ) {
						?>
							<tr>
							<?php self::ok_share_button(); ?>
							</tr>
						<?php } ?>
					</table>
					<?php

			}
			?>
			<div class="social_promotion_success_message"></div>
			<?php
		}

		public static function fb_like_button() {
			$custom_url = get_option( 'rs_global_social_facebook_url_custom' );

			$tool_tip_message = '1' === get_option( 'rs_global_show_hide_social_tooltip_for_facebook' ) ? get_option( 'rs_social_message_for_facebook' ) : '';
			$title            = '2' === get_option( 'rs_reward_point_enable_tipsy_social_rewards' ) ? sanitize_text_field( do_shortcode( $tool_tip_message ) ) : '';

			if ( '1' === get_option( 'rs_social_button_like' ) ) {
				?>
				<td>
					<div class="fb-like" 
						title ="<?php echo esc_attr( $title ); ?>" 
						data-size="<?php echo esc_html( get_option( 'rs_facebook_like_icon_size' ) ); ?>" 
						data-href="<?php echo esc_url( '1' === get_option( 'rs_global_social_facebook_url' ) ? get_permalink() : $custom_url ); ?>" 
						data-layout="button_count"
						data-action="like" 
						data-show-faces="true" 
						data-share="false">
					</div>
				</td>
				<?php
			} else {
				$fb_like_url = '1' === get_option( 'rs_global_social_facebook_url', '1' ) ? 'http://www.facebook.com/login.php' : ( '' != $custom_url ? $custom_url : 'http://www.facebook.com/login.php' );
				?>
				<td>
					<a class="rs_custom_social_icon_a fb_like_a" 
						href="<?php echo esc_url( $fb_like_url ); ?>" 
						onClick = "window.open( this.href , 'like' , 'toolbar=0,status=0,width=580,height=325' ) ;return false ;">
						<input type="button" 
						title ="<?php echo esc_attr( $title ); ?>" 
						value="<?php esc_html_e( 'FB Like', 'rewardsystem' ); ?>" 
						class="rs_custom_fblike_button"/>
					</a>
				</td>
				<?php
			}
		}

		public static function fb_share_button() {
			$Classname = is_product() ? 'share_wrapper1' : 'share_wrapper11';

			$tool_tip_message = '1' === get_option( 'rs_global_show_hide_social_tooltip_for_facebook_share' ) ? get_option( 'rs_social_message_for_facebook_share' ) : '';
			$title            = '2' === get_option( 'rs_reward_point_enable_tipsy_social_rewards' ) ? sanitize_text_field( do_shortcode( $tool_tip_message ) ) : '';

			if ( '1' === get_option( 'rs_social_button_share' ) ) {
				?>
				<td>
					<div class="<?php echo esc_attr( $Classname ); ?>" title ="<?php echo esc_attr( $title ); ?>">
						<img class='fb_share_img'
							src="<?php echo esc_url( SRP_PLUGIN_URL ); ?>/assets/images/icon1.png"> 
							<span class="label"><?php echo esc_attr( get_option( 'rs_fbshare_button_label' ) ); ?></span>
					</div>
				</td>
				<?php
			} else {
				?>
				<td>
					<a class="rs_custom_social_icon_a" 
						href="http://www.facebook.com/sharer.php?s=100&u=<?php echo esc_url( '1' == get_option( 'rs_global_social_facebook_share_url' ) ? get_permalink() : get_option( 'rs_global_social_facebook_share_url_custom' ) ); ?>" 
						onClick = "window.open( this.href , 'sharer' , 'toolbar=0,status=0,width=580,height=325' ) ;return false ;">
						<input type="button"
						value="<?php esc_html_e( 'FB Share', 'rewardsystem' ); ?>" 
						title ="<?php echo esc_attr( $title ); ?>" 
						class="rs_custom_fbshare_button"/>
					</a>
				</td>
				<?php
			}
		}

		public static function tweet_button() {

			$tool_tip_message = '1' == get_option( 'rs_global_show_hide_social_tooltip_for_twitter' ) ? get_option( 'rs_social_message_for_twitter' ) : '';
			$title            = '2' == get_option( 'rs_reward_point_enable_tipsy_social_rewards' ) ? sanitize_text_field( do_shortcode( $tool_tip_message ) ) : '';
			if ( 1 == get_option( 'rs_social_button_tweet' ) ) {
				?>
				<td>
					<div class="rstwitter-button-msg">
						<a href="https://twitter.com/share" 
						class="twitter-share-button" 
						id="twitter-share-button" 
						data-url="<?php echo esc_url( '1' == get_option( 'rs_global_social_twitter_url' ) ? get_permalink() : get_option( 'rs_global_social_twitter_url_custom' ) ); ?>">
						</a>
					</div>
				</td>
				<?php
			} else {
				?>
				<td>
					<a class="rs_custom_social_icon_a" 
						href="http://twitter.com/share?url=<?php echo esc_url( '1' == get_option( 'rs_global_social_twitter_url' ) ? get_permalink() : get_option( 'rs_global_social_twitter_url_custom' ) ); ?>" 
						target="_blank" 
						onClick = "javascript:window.open( this.href , '' , 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=500,width=600' ) ;return false ;">
						<input type="button" 
						title ="<?php echo esc_attr( $title ); ?>" 
						value="<?php esc_html_e( 'Tweet', 'rewardsystem' ); ?>" 
						class="rs_custom_tweet_button"/>
					</a>
				</td>
				<?php
			}
		}

		public static function twitter_follow_button() {
			$tool_tip_message = '1' == get_option( 'rs_global_show_hide_social_tooltip_for_twitter_follow' ) ? get_option( 'rs_social_message_for_twitter_follow' ) : '';
			$title            = '2' == get_option( 'rs_reward_point_enable_tipsy_social_rewards' ) ? sanitize_text_field( do_shortcode( $tool_tip_message ) ) : '';

			if ( 1 == get_option( 'rs_social_button_twitter_follow' ) ) {
				?>
				<td>
					<div class="rstwitterfollow-button-msg">
						<a href='https://twitter.com/<?php echo esc_attr( get_option( 'rs_global_social_twitter_profile_name' ) ); ?>' 
						 class="twitter-follow-button" data-show-count="false"><?php esc_html_e( 'Follow @twitter', 'rewardsystem' ); ?>
						</a>
					</div>
				</td>
				<?php
			} else {
				?>
				<td>
					<a class="rs_custom_social_icon_a" 
						href='https://twitter.com/<?php echo esc_attr( get_option( 'rs_global_social_twitter_profile_name' ) ); ?>'
						target="_blank" 
						onClick = "javascript:window.open( this.href , '' , 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=500,width=600' ) ;return false ;">
						<input type="button" 
						title ="<?php echo esc_attr( $title ); ?>" 
						value="<?php esc_html_e( 'Follow@Twitter', 'rewardsystem' ); ?>" 
						class="rs_custom_tweetfollow_button"/>
					</a>
				</td>
				<?php
			}
		}

		public static function instagram_follow_button() {
			$Classname = is_product() ? 'instagram_button' : 'instagram_button_post';

			$tool_tip_message = '1' == get_option( 'rs_global_show_hide_social_tooltip_for_instagram' ) ? get_option( 'rs_social_message_for_instagram' ) : '';
			$title            = '2' == get_option( 'rs_reward_point_enable_tipsy_social_rewards' ) ? sanitize_text_field( do_shortcode( $tool_tip_message ) ) : '';

			if ( 1 == get_option( 'rs_social_button_instagram' ) ) {
				?>
				<td>
					<div class ="<?php echo esc_attr( $Classname ); ?>" title ="<?php echo esc_attr( $title ); ?>">
						<a href="https://www.instagram.com/<?php echo esc_attr( get_option( 'rs_instagram_profile_name' ) ); ?>/?ref=badge"
							class="ig-b- ig-b-32" target="_blank">
							<img src="<?php echo esc_url( SRP_PLUGIN_DIR_URL ); ?>/assets/images/instagram.png" alt="Instagram" />
						</a>
					</div>
				</td>
				<?php
			} else {
				?>
				<td>
					<a class="rs_custom_social_icon_a" 
						href="https://www.instagram.com/<?php echo esc_attr( get_option( 'rs_instagram_profile_name' ) ); ?>/?ref=badge" 
						target="_blank" 
						onClick = "javascript:window.open( this.href , '' , 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=500,width=600' ) ;return false ;">
						<input type="button" 
						title ="<?php echo esc_attr( $title ); ?>" 
						value="<?php esc_html_e( 'Instagram', 'rewardsystem' ); ?>" 
						class="rs_custom_instagram_button"/>
					</a>
				</td>
				<?php
			}
		}

		public static function vk_like_button() {

			$tool_tip_message = '1' == get_option( 'rs_global_show_hide_social_tooltip_for_vk' ) ? get_option( 'rs_social_message_for_vk' ) : '';
			$title            = '2' == get_option( 'rs_reward_point_enable_tipsy_social_rewards' ) ? sanitize_text_field( do_shortcode( $tool_tip_message ) ) : '';

			if ( 1 == get_option( 'rs_social_button_vk_like' ) ) {
				?>
				<td>
					<div id="vk_like" 
						class='vk-like' 
						title ="<?php echo esc_attr( $title ); ?>">
					</div>
				</td>
				<?php
			} else {
				?>
				<td>
					<a class="rs_custom_social_icon_a" 
						href="https://www.vk.com/"  
						target="_blank" 
						onClick = "javascript:window.open( this.href , '' , 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=500,width=600' ) ;return false ;">
						<input type="button"
						title ="<?php echo esc_attr( $title ); ?>" 
						id="vk_like" 
						value="<?php esc_html_e( 'VK Like', 'rewardsystem' ); ?>" 
						class="rs_custom_vklike_button"/>
					</a>
				</td>
				<?php
			}
		}

		public static function gplus_share_button() {
			$url = '1' == get_option( 'rs_global_social_google_url' ) ? get_permalink() : get_option( 'rs_global_social_google_url_custom' );

			$tool_tip_message = '1' == get_option( 'rs_global_show_hide_social_tooltip_for_google' ) ? get_option( 'rs_social_message_for_google_plus' ) : '';
			$title            = '2' == get_option( 'rs_reward_point_enable_tipsy_social_rewards' ) ? sanitize_text_field( do_shortcode( $tool_tip_message ) ) : '';

			if ( 1 == get_option( 'rs_social_button_gplus' ) ) {
				?>
				<td>
					<div id="google-plus-one" title ="<?php echo esc_attr( $title ); ?>"> 
						<a href="https://plus.google.com/share?url=<?php echo esc_url( $url ); ?>" 
							id='google-plus-one' 
							class="google-plus-one fp_gplus_share" 
							target='_blank' onclick="javascript:window.open( this.href ,
														'' , 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600' ) ;
												return false ;">
							<img src="https://www.gstatic.com/images/icons/gplus-32.png" 
									lt="Share on Google+"/>
						</a>
					</div>
				</td>
				<?php
			} else {
				?>
				<td>
					<a class="rs_custom_social_icon_a"
						href="https://plus.google.com/share?url=<?php echo esc_url( $url ); ?>" 
						target='_blank' 
						onclick="javascript:window.open( this.href , '' , 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600' ) ;
											return false ;">
						<input type="button" 
						title ="<?php echo esc_attr( $title ); ?>" 
						value="<?php esc_html_e( 'G PLus', 'rewardsystem' ); ?>" 
						class="rs_custom_gplus_button"/>
					</a>
				</td>
				<?php
			}
		}

		public static function ok_share_button() {

			$tool_tip_message = '1' == get_option( 'rs_global_show_hide_social_tooltip_for_ok_follow' ) ? get_option( 'rs_social_message_for_ok_follow' ) : '';
			$title            = '2' == get_option( 'rs_reward_point_enable_tipsy_social_rewards' ) ? sanitize_text_field( do_shortcode( $tool_tip_message ) ) : '';

			if ( 1 == get_option( 'rs_social_button_ok_ru' ) ) {

				?>
				<td>
					<div class="ok-share-button" 
							id="ok_shareWidget" 
							title ="<?php echo esc_attr( $title ); ?>">
						<a href="https://ok.ru/" 
							class="ok-share-button" 
							id="ok-share-button" 
							data-url="<?php echo esc_url( get_option( 'rs_global_social_ok_url' ) == '1' ? get_permalink() : get_option( 'rs_global_social_ok_url_custom' ) ); ?>">
						</a>
					</div>
				</td>
				<?php
			} else {
				?>
				<td>
					<a class="rs_custom_social_icon_a" 
						href="https://ok.ru/" 
						target='_blank' 
						onclick="javascript:window.open( this.href , '' , 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600' ) ;
											return false ;">
						<input type="button" 
						title ="<?php echo esc_attr( $title ); ?>"
						value="<?php esc_html_e( 'OK.ru', 'rewardsystem' ); ?>"
						class="rs_custom_ok_button"/>
					</a>
				</td>
				<?php
			}
		}
	}

	RSFunctionForSocialRewards::init();
}
