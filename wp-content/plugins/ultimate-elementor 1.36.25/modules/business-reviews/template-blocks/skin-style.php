<?php
/**
 * UAEL Base Skin.
 *
 * @package UAEL
 */

namespace UltimateElementor\Modules\BusinessReviews\TemplateBlocks;

use Elementor\Group_Control_Image_Size;
use Elementor\Utils;

use UltimateElementor\Classes\UAEL_Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class Skin_Base
 */
abstract class Skin_Style {

	/**
	 * Settings
	 *
	 * @since 1.13.0
	 * @var object $settings
	 */
	public static $settings;

	/**
	 * Skin
	 *
	 * @since 1.13.0
	 * @var object $skin
	 */
	public static $skin;

	/**
	 * Node ID of element
	 *
	 * @since 1.13.0
	 * @var object $node_id
	 */
	public static $node_id;

	/**
	 * Rendered Settings
	 *
	 * @since 1.13.0
	 * @var object $_render_attributes
	 */
	public $_render_attributes; // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore

	/**
	 * Get google api status
	 *
	 * @since 1.13.0
	 * @var object $google_api_status
	 */
	public static $google_api_status = false;

	/**
	 * Get Wrapper Classes.
	 *
	 * @since 1.13.0
	 * @access public
	 */
	public function get_carousel_attr() {

		$settings = self::$settings;

		if ( 'carousel' !== $settings['review_structure'] ) {
			return;
		}

		$is_rtl      = is_rtl();
		$direction   = $is_rtl ? 'rtl' : 'ltr';
		$show_dots   = ( in_array( $settings['navigation'], array( 'dots', 'both' ), true ) );
		$show_arrows = ( in_array( $settings['navigation'], array( 'arrows', 'both' ), true ) );

		$slick_options = array(
			'slidesToShow'   => ( $settings['slides_to_show'] ) ? absint( $settings['slides_to_show'] ) : 4,
			'slidesToScroll' => ( $settings['slides_to_scroll'] ) ? absint( $settings['slides_to_scroll'] ) : 1,
			'autoplaySpeed'  => ( $settings['autoplay_speed'] ) ? absint( $settings['autoplay_speed'] ) : 5000,
			'autoplay'       => ( 'yes' === $settings['autoplay'] ),
			'infinite'       => ( 'yes' === $settings['infinite'] ),
			'pauseOnHover'   => ( 'yes' === $settings['pause_on_hover'] ),
			'speed'          => ( $settings['transition_speed'] ) ? absint( $settings['transition_speed'] ) : 500,
			'arrows'         => $show_arrows,
			'dots'           => $show_dots,
			'rtl'            => $is_rtl,
		);

		if ( $settings['slides_to_show_tablet'] || $settings['slides_to_show_mobile'] ) {

			$slick_options['responsive'] = array();

			if ( $settings['slides_to_show_tablet'] ) {

				$tablet_show   = absint( $settings['slides_to_show_tablet'] );
				$tablet_scroll = ( $settings['slides_to_scroll_tablet'] ) ? absint( $settings['slides_to_scroll_tablet'] ) : $tablet_show;

				$slick_options['responsive'][] = array(
					'breakpoint' => 1024,
					'settings'   => array(
						'slidesToShow'   => $tablet_show,
						'slidesToScroll' => $tablet_scroll,
					),
				);
			}

			if ( $settings['slides_to_show_mobile'] ) {

				$mobile_show   = absint( $settings['slides_to_show_mobile'] );
				$mobile_scroll = ( $settings['slides_to_scroll_mobile'] ) ? absint( $settings['slides_to_scroll_mobile'] ) : $mobile_show;

				$slick_options['responsive'][] = array(
					'breakpoint' => 767,
					'settings'   => array(
						'slidesToShow'   => $mobile_show,
						'slidesToScroll' => $mobile_scroll,
					),
				);
			}
		}

		$slick_options = apply_filters( 'uael_reviews_carousel_options', $slick_options );

		$this->add_render_attribute(
			'uael-reviews-slider',
			array(
				'data-reviews_slider' => wp_json_encode( $slick_options ),
			)
		);

		return $this->get_render_attribute_string( 'uael-reviews-slider' );
	}

	/**
	 * Gets the layout of five star.
	 *
	 * @since 1.13.0
	 * @access public
	 *
	 * @param array $total_rating total_rating.
	 * @param array $review data of single review.
	 * @param array $settings The settings array.
	 * @return the layout of Google reviews star rating.
	 */
	public function render_stars( $total_rating, $review, $settings ) {
		$rating     = $total_rating;
		$stars_html = '';
		$flag       = 0;

		if ( 'default' === $this->get_instance_value( 'select_star_style' ) ) {

			if ( 'google' === $review['source'] ) {
				$marked_icon_html   = '<i class="fa fa-star uael-star-full uael-star-default" aria-hidden="true"></i>';
				$unmarked_icon_html = '<i class="fa fa-star uael-star-empty uael-star-default" aria-hidden="true"></i>';
				$flag               = 1;
			} else {
				$stars_html = '<svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
	 width="100px" height="18px" viewBox="-1 0.054 32 5.642" enable-background="new -1 0.054 32 5.642" xml:space="preserve" class="uael-yelp-rating-svg-' . $rating . '">
<g>
	<path fill="#CECECE" d="M4.075,0.055h-4.511C-0.744,0.055-1,0.307-1,0.626v4.497c0,0.314,0.256,0.572,0.564,0.572h4.511
		c0.308,0,0.557-0.258,0.557-0.572V0.626C4.632,0.307,4.383,0.055,4.075,0.055z M3.973,2.486L2.889,3.434l0.322,1.396
		C3.241,4.927,3.13,5.004,3.05,4.945L1.82,4.214L0.59,4.945C0.501,5,0.399,4.926,0.42,4.829l0.33-1.396l-1.086-0.947
		c-0.08-0.061-0.041-0.187,0.062-0.19l1.432-0.123l0.56-1.327c0.03-0.088,0.161-0.088,0.205,0L2.48,2.173l1.433,0.123
		C4.003,2.302,4.046,2.428,3.973,2.486z" class="uael-yelp-rating-1"/>
	<path fill="#CECECE" d="M10.663,0.055H6.159c-0.311,0-0.571,0.252-0.571,0.571v4.497c0,0.314,0.26,0.572,0.571,0.572h4.504
		c0.315,0,0.564-0.258,0.564-0.572V0.626C11.227,0.307,10.978,0.055,10.663,0.055z M10.567,2.486L9.483,3.434l0.322,1.396
		C9.83,4.927,9.717,5.004,9.64,4.945L8.414,4.214l-1.23,0.731C7.096,5,6.994,4.925,7.008,4.829l0.329-1.396L6.25,2.486
		C6.172,2.426,6.216,2.3,6.319,2.296l1.425-0.123l0.565-1.327c0.032-0.088,0.164-0.088,0.208,0l0.557,1.327L10.5,2.296
		C10.597,2.302,10.641,2.428,10.567,2.486z" class="uael-yelp-rating-2"/>
	<path fill="#CECECE" d="M17.246,0.055h-4.497c-0.318,0-0.571,0.252-0.571,0.571v4.497c0,0.314,0.253,0.572,0.571,0.572h4.497
		c0.32,0,0.572-0.258,0.572-0.572V0.626C17.818,0.307,17.566,0.055,17.246,0.055z M17.158,2.486l-1.084,0.947l0.322,1.396
		c0.018,0.098-0.088,0.175-0.172,0.116l-1.228-0.73l-1.225,0.732c-0.086,0.054-0.191-0.021-0.174-0.117l0.322-1.396l-1.084-0.944
		c-0.073-0.062-0.029-0.188,0.073-0.191l1.421-0.123l0.562-1.325c0.039-0.09,0.172-0.09,0.211,0l0.561,1.325l1.422,0.123
		C17.188,2.302,17.232,2.428,17.158,2.486z" class="uael-yelp-rating-3"/>
	<path fill="#CECECE" d="M23.838,0.055h-4.503c-0.315,0-0.565,0.252-0.565,0.571v4.497c0,0.314,0.25,0.572,0.565,0.572h4.503
		c0.314,0,0.572-0.258,0.572-0.572V0.626C24.41,0.307,24.152,0.055,23.838,0.055z M23.742,2.486l-1.083,0.947l0.323,1.396
		c0.026,0.098-0.082,0.175-0.17,0.116l-1.229-0.731l-1.226,0.731C20.279,5,20.168,4.925,20.191,4.829l0.322-1.396L19.43,2.486
		C19.355,2.426,19.4,2.3,19.496,2.296l1.426-0.123l0.563-1.327c0.037-0.088,0.17-0.088,0.205,0l0.559,1.327l1.438,0.123
		C23.773,2.302,23.824,2.428,23.742,2.486z" class="uael-yelp-rating-4"/>
	<path fill="#CECECE" d="M30.43,0.055h-4.505c-0.3,0-0.563,0.252-0.563,0.571v4.497c0,0.314,0.266,0.572,0.563,0.572h4.505
		c0.321,0,0.57-0.258,0.57-0.572V0.626C31,0.307,30.751,0.055,30.43,0.055z M30.34,2.486l-1.083,0.947l0.323,1.396
		c0.027,0.098-0.09,0.175-0.176,0.116l-1.229-0.731l-1.229,0.731C26.868,5,26.764,4.925,26.791,4.829l0.326-1.396l-1.086-0.945
		c-0.088-0.062-0.035-0.188,0.059-0.191l1.438-0.123l0.557-1.326c0.031-0.089,0.169-0.089,0.207,0l0.557,1.326l1.436,0.123
		C30.371,2.302,30.416,2.428,30.34,2.486z" class="uael-yelp-rating-5"/>
</g>
</svg>';
			}
		} else {
			$marked_icon_html   = '<i class="fa fa-star uael-star-full uael-star-custom" aria-hidden="true"></i>';
			$unmarked_icon_html = '<i class="fa fa-star uael-star-empty uael-star-custom" aria-hidden="true"></i>';
			$flag               = 1;
		}

		if ( $flag ) {
			for ( $stars = 1; $stars <= 5; $stars++ ) {
				if ( $stars <= $rating ) {
					$stars_html .= $marked_icon_html;
				} else {
					$stars_html .= $unmarked_icon_html;
				}
			}
		}

		return $stars_html;
	}

	/**
	 * Gets JSON Data from Google.
	 *
	 * @since 1.13.0
	 * @param array $settings The settings array.
	 * @return the layout of Google reviews.
	 * @access public
	 */
	public function get_google_api( $settings ) {

		$node_id   = self::$node_id;
		$is_editor = \Elementor\Plugin::instance()->editor->is_edit_mode();

		$placeid = $settings['place_id'] . '&language=' . $settings['language_id'];
		$api_key = '';

		$integration_options = UAEL_Helper::get_integrations_options();
		$widget_list         = UAEL_Helper::get_widget_list();
		$admin_link          = $widget_list['Business_Reviews']['setting_url'];

		if ( '' !== $integration_options['google_places_api'] ) {
			if ( ! self::$google_api_status ) {
				self::$google_api_status = get_option( 'uael_google_api_status' );
			}
			if ( 'yes' === self::$google_api_status ) {
				$api_key = $integration_options['google_places_api'];
			} elseif ( 'no' === self::$google_api_status ) {
				printf(
					/* translators: 1: <span> notice message <span> 2: error message <span> 3: first </span> 4: second </span> */
					esc_html__( '%1$s%2$sGoogle Error Message:%3$s Incorrect Google API key! Please set up the API key from UAE settings.%4$s', 'uael' ),
					'<span class="uael-reviews-notice-message">',
					'<span class="uael-reviews-error-message">',
					'</span>',
					'</span>'
				);
				return false;
			}
		} elseif ( $is_editor ) {
			printf(
				/* translators: 1: <span> notice message <span> 2: error message <span> 3: first </span> 4: second </span> */
				esc_html__( '%1$s%2$sGoogle Error Message:%3$s Please configure the Google API key to display the reviews.%4$s', 'uael' ),
				'<span class="uael-reviews-notice-message">',
				'<span class="uael-reviews-error-message">',
				'</span>',
				'</span>'
			);
			return false;
		} else {
			return false;
		}

		$parameters = "key=$api_key&placeid=$placeid";

		$url = "https://maps.googleapis.com/maps/api/place/details/json?$parameters";

		$reviews = '';

		$transient_name = 'uael_reviews_' . $placeid;

		do_action( 'uael_reviews_transient', $transient_name, $settings );

		$result = get_transient( $transient_name );

		if ( false === $result ) {
			sleep( 2 );
			$result = wp_remote_post(
				$url,
				array(
					'method'      => 'POST',
					'timeout'     => 60,
					'httpversion' => '1.0',
				)
			);

			if ( is_wp_error( $result ) ) {
				$error_message = $result->get_error_message();
				if ( $is_editor ) {
					printf(
					/* translators: 1: <span> notice message <span> 2: error message <span> 3: first </span> */
						esc_html__( '%1$sSomething went wrong while fetching Google reviews: %2$s%3$s', 'uael' ),
						'<span class="uael-reviews-notice-message">',
						esc_attr( $error_message ),
						'</span>'
					);
				}
				return;
			}

			$result = wp_encode_emoji( $result['body'] );
			$result = json_decode( $result );

			$expire_time = $this->get_transient_expire( $settings );

			$expire_time = apply_filters( 'uael_reviews_expire_time', $expire_time, $settings );

			set_transient( $transient_name, $result, $expire_time );
		}

		$result_status = $result->status;

		if ( $is_editor ) {
			switch ( $result_status ) {
				// @codingStandardsIgnoreStart.
				case 'OVER_QUERY_LIMIT':
					/* translators: %1$s doc link */
					echo sprintf( __( '<span class="uael-reviews-notice-message elementor-clickable"><span class="uael-reviews-error-message">Google Error Message: </span>OVER_QUERY_LIMIT</br>You have exceeded your daily request quota for this API. If you did not set a custom daily request quota, verify your project has an active billing account. Visit your %1$s Google API console %2$s to activate billing. </span>', 'uael' ), '<a href="http://g.co/dev/maps-no-account">', '</a>' );
					delete_transient( $transient_name );
					return false;
					break;

				case 'REQUEST_DENIED':
					echo __( '<span class="uael-reviews-notice-message"><span class="uael-reviews-error-message">Google Error Message: </span>REQUEST_DENIED</br>Invalid Google API key! Please verify your API key from UAE settings.</span>', 'uael' );
					delete_transient( $transient_name );
					return false;
					break;

				case 'UNKNOWN_ERROR':
					echo __( '<span class="uael-reviews-notice-message"><span class="uael-reviews-error-message">Google Error Message: </span>UNKNOWN_ERROR </br>Seems like a server-side error; Please try again later.</span>', 'uael' );
					delete_transient( $transient_name );
					return false;
					break;

				case 'ZERO_RESULTS':
				case 'INVALID_REQUEST':
					echo __( '<span class="uael-reviews-notice-message"><span class="uael-reviews-error-message">Google Error Message: </span>INVALID_REQUEST </br>Please check if the entered Place ID is invalid.</span>', 'uael' );
					delete_transient( $transient_name );
					return false;
					break;

				case 'OK':
					if ( ! property_exists( $result->result, 'reviews' ) ) {
						echo __( '<span class="uael-reviews-notice-message"><span class="uael-reviews-error-message">Google Error Message:</span> It seems like the Google place you have selected does not have any reviews.</span>', 'uael' );
						delete_transient( $transient_name );
						return false;
					}
					break;

				default:
					return false;
					break;
					// @codingStandardsIgnoreEnd.
			}
		}

		if ( 'OK' === $result_status ) {
			if ( property_exists( $result->result, 'reviews' ) ) {
				$reviews = $result->result->reviews;
			}
		}

		return $reviews;
	}

	/**
	 * Gets expire time of transient.
	 *
	 * @since 1.13.0
	 * @param array $settings The settings array.
	 * @return the reviews transient expire time.
	 * @access public
	 */
	public function get_transient_expire( $settings ) {

		$expire_value = $settings['refresh_reviews'];
		$expire_time  = 24 * HOUR_IN_SECONDS;

		if ( 'hour' === $expire_value ) {
			$expire_time = 60 * MINUTE_IN_SECONDS;
		} elseif ( 'week' === $expire_value ) {
			$expire_time = 7 * DAY_IN_SECONDS;
		} elseif ( 'month' === $expire_value ) {
			$expire_time = 30 * DAY_IN_SECONDS;
		} elseif ( 'year' === $expire_value ) {
			$expire_time = 365 * DAY_IN_SECONDS;
		}

		return $expire_time;
	}

	/**
	 * Gets JSON Data from Yelp.
	 *
	 * @since 1.13.0
	 * @param array $settings The settings array.
	 * @return the layout of Yelp reviews.
	 * @access public
	 */
	public function get_yelp_api( $settings ) {

		$business_id = $settings['yelp_business_id'];

		$is_editor = \Elementor\Plugin::instance()->editor->is_edit_mode();

		if ( '' !== $business_id ) {

			$url = 'https://api.yelp.com/v3/businesses/' . $business_id . '/reviews';

			$integration_options = UAEL_Helper::get_integrations_options();

			if ( '' !== $integration_options['yelp_api'] ) {
				$yelp_api_key = $integration_options['yelp_api'];
			} elseif ( $is_editor ) {
				printf(
				/* translators: 1: <span> notice message <span> 2: error message <span> 3: first </span> 4: second </span> */
					esc_html__( '%1$s%2$sYelp Error Message:%3$s Please set the Yelp API key to display the reviews.%4$s', 'uael' ),
					'<span class="uael-reviews-notice-message">',
					'<span class="uael-reviews-error-message">',
					'</span>',
					'</span>'
				);

				return false;
			} else {
				return false;
			}

			$reviews = '';

			$transient_name = 'uael_reviews_' . $business_id;

			$expire_time = $this->get_transient_expire( $settings );

			$expire_time = apply_filters( 'uael_reviews_expire_time', $expire_time, $settings );

			do_action( 'uael_reviews_transient', $transient_name, $settings );

			$result = get_transient( $transient_name );

			if ( false === $result ) {
				$result = wp_remote_get(
					$url,
					array(
						'method'      => 'GET',
						'timeout'     => 60,
						'httpversion' => '1.0',
						'user-agent'  => '',
						'headers'     => array(
							'Authorization' => 'Bearer ' . $yelp_api_key,
						),
					)
				);

				set_transient( $transient_name, $result, $expire_time );
			}

			if ( is_wp_error( $result ) ) {
				$error_message = $result->get_error_message();
				if ( $is_editor ) {
					printf(
						/* translators: 1: first <span> 2: doc link 3: </span> */
						esc_html__( '%1$sSomething went wrong:%2$s%3$s', 'uael' ),
						'<span class="uael-reviews-notice-message">',
						esc_attr( $error_message ),
						'</span>'
					);
				}
				delete_transient( $transient_name );
				return;
			}

			// Check the response code.
			$response_code    = wp_remote_retrieve_response_code( $result );
			$response_message = wp_remote_retrieve_response_message( $result );

			$reviews = json_decode( $result['body'] );

			if ( 200 !== $response_code && ! empty( $response_message ) ) {
				if ( $is_editor ) {
					$error_code = $reviews->error->code;
					if ( 'VALIDATION_ERROR' === $error_code ) {
						printf(
								/* translators: 1: <span> notice message <span> 2: error message <span> 3: first </span> 4: second </span> */
							esc_html__( '%1$s%2$sYelp Error Message:%3$s Incorrect Yelp API key. Please set up the API key from UAE settings.%4$s', 'uael' ),
							'<span class="uael-reviews-notice-message">',
							'<span class="uael-reviews-error-message">',
							'</span>',
							'</span>'
						);
					} elseif ( 'BUSINESS_NOT_FOUND' === $error_code ) {
						printf(
							/* translators: 1: <span> notice message <span> 2: error message <span> 3: first </span> 4: second </span> */
							esc_html__( '%1$s%2$sYelp Error Message:%3$s Incorrect Business ID.%4$s', 'uael' ),
							'<span class="uael-reviews-notice-message">',
							'<span class="uael-reviews-error-message">',
							'</span>',
							'</span>'
						);
					}
				}
				delete_transient( $transient_name );
				return;
			} elseif ( 200 !== $response_code ) {
				if ( $is_editor ) {
					printf(
						/* translators: 1: first <span> 2: doc link 3: </span> */
						esc_html__( '%1$s%2$s Unknown error occurred.%3$s', 'uael' ),
						'<span class="uael-reviews-notice-message">',
						esc_attr( $response_code ),
						'</span>'
					);
				}
				delete_transient( $transient_name );
				return;
			}

			$reviews = $reviews->reviews;

			return $reviews;
		}

	}

	/**
	 * Get Reviews array with the same key for Google & Yelp.
	 *
	 * @since 1.13.0
	 * @param string $type The reviews source.
	 * @param array  $reviews The reviews array.
	 * @param array  $settings The settings array.
	 * @return the merged array of Google & Yelp reviews.
	 * @access public
	 */
	public function get_merged_reviews_array( $type, $reviews, $settings ) {

		if ( empty( $reviews ) ) {
			return;
		}

		$custom_reviews    = array();
		$min_rating_filter = false;

		if ( 'no' !== $settings['reviews_min_rating'] ) {
			$min_rating_filter = true;
		}

		foreach ( $reviews as $key => $value ) {

			if ( 'google' === $type ) {
				$user_review_url = explode( '/reviews', $value->author_url );
				array_pop( $user_review_url );
				$review_url                          = $user_review_url[0] . '/place/' . $settings['place_id'];
				$review['source']                    = 'google';
				$review['author_name']               = $value->author_name;
				$review['author_url']                = $value->author_url;
				$review['profile_photo_url']         = $value->profile_photo_url;
				$review['rating']                    = $value->rating;
				$review['relative_time_description'] = $value->relative_time_description;
				$review['text']                      = $value->text;
				$review['time']                      = $value->time;
				$review['review_url']                = $review_url;
			}
			if ( 'yelp' === $type ) {
				$review['source']                    = 'yelp';
				$review['author_name']               = $value->user->name;
				$review['author_url']                = $value->user->profile_url;
				$review['profile_photo_url']         = $value->user->image_url;
				$review['rating']                    = $value->rating;
				$review['relative_time_description'] = '';
				$review['text']                      = $value->text;
				$review['time']                      = $value->time_created;
				$review['review_url']                = $value->url;
			}

			if ( $min_rating_filter ) {
				if ( $value->rating >= $settings['reviews_min_rating'] ) {
					array_push( $custom_reviews, $review );
				}
			} else {
				array_push( $custom_reviews, $review );
			}
		}

		return $custom_reviews;

	}
	/**
	 * Get Reviews array with the same key for Google & Yelp.
	 *
	 * @since 1.13.0
	 * @param array $settings The settings array.
	 * @return the layout of Google reviews.
	 * @access public
	 */
	public function get_reviews_array( $settings ) {

		$reviews        = array();
		$custom_reviews = array();

		if ( 'google' === $settings['review_type'] ) {

			$reviews = $this->get_google_api( $settings );
			$reviews = $this->get_merged_reviews_array( 'google', $reviews, $settings );

		} elseif ( 'yelp' === $settings['review_type'] ) {

			$reviews = $this->get_yelp_api( $settings );
			$reviews = $this->get_merged_reviews_array( 'yelp', $reviews, $settings );

		} elseif ( 'all' === $settings['review_type'] ) {

			$google_reviews = $this->get_google_api( $settings );
			$yelp_reviews   = $this->get_yelp_api( $settings );

			$google_reviews = $this->get_merged_reviews_array( 'google', $google_reviews, $settings );
			$yelp_reviews   = $this->get_merged_reviews_array( 'yelp', $yelp_reviews, $settings );

			if ( empty( $google_reviews ) || empty( $yelp_reviews ) ) {
				return;
			}

			$count = count( $google_reviews );

			/* Merge reviews array elements inalternative order */
			for ( $i = 0; $i < $count; $i++ ) {
				$reviews[] = $google_reviews[ $i ];
				if ( $i < count( $yelp_reviews ) ) {
					$reviews[] = $yelp_reviews[ $i ];
				}
			}
			$reviews = array_filter( $reviews );
		}

		return $reviews;
	}

	/**
	 * Get sorted array of reviews by rating.
	 *
	 * @since 1.13.0
	 * @access public
	 * @param string $review1 represents review1 to compare.
	 * @param string $review2 represents review2 to compare.
	 * @return string of compared reviews.
	 */
	public function filter_by_rating( $review1, $review2 ) {
		return strcmp( $review2['rating'], $review1['rating'] );
	}

	/**
	 * Get sorted array of reviews by date.
	 *
	 * @since 1.13.0
	 * @access public
	 * @param string $review1 represents review1 to compare.
	 * @param string $review2 represents review2 to compare.
	 * @return string of compared reviews.
	 */
	public function filter_by_date( $review1, $review2 ) {
		return strcmp( $review2['time'], $review1['time'] );
	}

	/**
	 * Get reviewer name section.
	 *
	 * @since 1.13.0
	 * @access public
	 * @param string $review represents single review.
	 * @param array  $settings represents settings array.
	 */
	public function get_reviewer_name( $review, $settings ) {
		if ( 'yes' === $this->get_instance_value( 'reviewer_name' ) ) {
			?>
			<?php if ( 'yes' === $this->get_instance_value( 'reviewer_name_link' ) ) { ?>
				<span class="uael-reviewer-name"><?php echo wp_kses_post( "<a href={$review['author_url']} target='_blank'>{$review['author_name']}</a>" ); ?></span>
			<?php } else { ?>
				<span class="uael-reviewer-name"><?php echo wp_kses_post( "{$review['author_name']}" ); ?></span>
				<?php
			}
		}
	}

	/**
	 * Get review header.
	 *
	 * @since 1.13.0
	 * @access public
	 * @param string $review represents single review.
	 * @param string $photolink represents reviewer image link.
	 * @param array  $settings represents settings array.
	 */
	public function get_reviews_header( $review, $photolink, $settings ) {

		$total_rating = $review['rating'];
		$timestamp    = ( 'google' === $review['source'] ) ? $review['time'] : strtotime( $review['time'] );
		$date         = gmdate( 'd-m-Y', $timestamp );

		$google_svg = '<svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
	 width="18px" height="18px" viewBox="0 0 100 100" enable-background="new 0 0 100 100" xml:space="preserve">
<g>
	<path id="XMLID_5_" fill="#FFFFFF" d="M34.963,3.686C23.018,7.777,12.846,16.712,7.206,28.002
		c-1.963,3.891-3.397,8.045-4.258,12.315C0.78,50.961,2.289,62.307,7.2,72.002c3.19,6.328,7.762,11.951,13.311,16.361
		c5.236,4.175,11.336,7.256,17.806,8.979c8.163,2.188,16.854,2.14,25.068,0.268c7.426-1.709,14.452-5.256,20.061-10.436
		c5.929-5.449,10.158-12.63,12.399-20.342c2.441-8.415,2.779-17.397,1.249-26.011c-15.373-0.009-30.744-0.004-46.113-0.002
		c0.003,6.375-0.007,12.749,0.006,19.122c8.9-0.003,17.802-0.006,26.703,0c-1.034,6.107-4.665,11.696-9.813,15.135
		c-3.236,2.176-6.954,3.587-10.787,4.26c-3.861,0.661-7.846,0.746-11.696-0.035c-3.914-0.781-7.649-2.412-10.909-4.711
		c-5.212-3.662-9.189-9.018-11.23-15.048c-2.088-6.132-2.103-12.954,0.009-19.08c1.466-4.316,3.907-8.305,7.112-11.551
		c3.955-4.048,9.095-6.941,14.633-8.128c4.742-1.013,9.745-0.819,14.389,0.586c3.947,1.198,7.584,3.359,10.563,6.206
		c3.012-2.996,6.011-6.008,9.014-9.008c1.579-1.615,3.236-3.161,4.763-4.819C79.172,9.52,73.819,6.123,67.97,3.976
		C57.438,0.1,45.564,0.018,34.963,3.686z"/>
	<g>
		<path id="XMLID_4_" fill="#EA4335" d="M34.963,3.686C45.564,0.018,57.438,0.1,67.97,3.976c5.85,2.147,11.202,5.544,15.769,9.771
			c-1.526,1.659-3.184,3.205-4.763,4.819c-3.003,3-6.002,6.012-9.014,9.008c-2.979-2.846-6.616-5.008-10.563-6.206
			c-4.645-1.405-9.647-1.599-14.389-0.586c-5.539,1.187-10.679,4.08-14.633,8.128c-3.206,3.246-5.646,7.235-7.112,11.551
			c-5.353-4.152-10.703-8.307-16.058-12.458C12.846,16.712,23.018,7.777,34.963,3.686z"/>
	</g>
	<g>
		<path id="XMLID_3_" fill="#FBBC05" d="M2.947,40.317c0.861-4.27,2.295-8.424,4.258-12.315c5.355,4.151,10.706,8.306,16.058,12.458
			c-2.112,6.126-2.097,12.948-0.009,19.08C17.903,63.695,12.557,67.856,7.2,72.002C2.289,62.307,0.78,50.961,2.947,40.317z"/>
	</g>
	<g>
		<path id="XMLID_2_" fill="#4285F4" d="M50.981,40.818c15.369-0.002,30.74-0.006,46.113,0.002
			c1.53,8.614,1.192,17.596-1.249,26.011c-2.241,7.712-6.471,14.893-12.399,20.342c-5.18-4.039-10.386-8.057-15.568-12.099
			c5.147-3.438,8.778-9.027,9.813-15.135c-8.9-0.006-17.803-0.003-26.703,0C50.974,53.567,50.984,47.194,50.981,40.818z"/>
	</g>
	<g>
		<path id="XMLID_1_" fill="#34A853" d="M7.2,72.002c5.356-4.146,10.703-8.307,16.055-12.461c2.041,6.03,6.018,11.386,11.23,15.048
			c3.26,2.299,6.995,3.93,10.909,4.711c3.851,0.781,7.835,0.696,11.696,0.035c3.833-0.673,7.551-2.084,10.787-4.26
			c5.183,4.042,10.389,8.06,15.568,12.099c-5.608,5.18-12.635,8.727-20.061,10.436c-8.215,1.872-16.906,1.921-25.068-0.268
			c-6.469-1.723-12.57-4.804-17.806-8.979C14.962,83.953,10.39,78.33,7.2,72.002z"/>
	</g>
</g>
</svg>';

		if ( 'yes' === $this->get_instance_value( 'review_date' ) ) {
			if ( 'google' === $settings['review_type'] ) {
				$date_value = ( 'default' === $this->get_instance_value( 'review_date_type' ) ) ? $date : $review['relative_time_description'];
			} else {
				$date_value = $date;
			}
		}
		?>
		<div class="uael-review-header">
			<?php if ( 'yes' === $this->get_instance_value( 'reviewer_image' ) && 'all_left' !== $this->get_instance_value( 'image_align' ) ) { ?>
				<div class="uael-review-image" style="background-image:url( <?php echo wp_kses_post( $photolink ); ?> );"></div>
			<?php } ?>
			<div class="uael-review-details">
				<?php
				if ( 'default' === $settings['_skin'] ) {
					$this->get_reviewer_name( $review, $settings );
				}
				?>
				<?php if ( 'yes' === $this->get_instance_value( 'review_rating' ) ) { ?>
					<span class="elementor-star-rating__wrapper">
						<span class="uael-star-rating"><?php echo wp_kses_post( $this->render_stars( $total_rating, $review, $settings ) ); ?></span>
					</span>
				<?php } ?>
				<?php
				if ( 'yes' === $this->get_instance_value( 'review_date' ) ) {
					$review_source = ( 'google' === $review['source'] ) ? 'Google' : 'Yelp';
					$via_source    = ' via ' . $review_source;
					if ( 'yes' === $this->get_instance_value( 'review_source_icon' ) ) {
						$via_source = '';
					}
					?>
					<span class="uael-review-time"><?php echo esc_html( $date_value ) . esc_html( $via_source ); ?></span>
				<?php } ?>
				<?php
				if ( 'bubble' === $settings['_skin'] || 'card' === $settings['_skin'] ) {
					$this->get_reviewer_name( $review, $settings );
				}
				?>
			</div>
			<?php if ( 'yes' === $this->get_instance_value( 'review_source_icon' ) && ( 'all_left' === $this->get_instance_value( 'image_align' ) || 'left' === $this->get_instance_value( 'image_align' ) ) ) { ?>
				<div class="uael-review-icon-wrap">
					<?php if ( 'yelp' === $review['source'] ) { ?>
						<i class="fa fa-yelp" aria-hidden="true"></i>
						<?php
					} else {
						echo $google_svg; // phpcs:ignore
					}
					?>
				</div>
			<?php } ?>
		</div>
		<?php
	}

	/**
	 * Render Business Reviews output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.13.0
	 * @param string $style represents current skin style.
	 * @param array  $settings represents settings array.
	 * @param string $node_id represents current node id.
	 * @access public
	 */
	public function render( $style, $settings, $node_id ) {
		self::$settings = $settings;
		self::$skin     = str_replace( '-', '_', $style );
		self::$node_id  = $node_id;

		$reviews            = '';
		$reviews_max        = 8;
		$disply_num_reviews = 8;
		$aggregate          = 0;

		$reviews = $this->get_reviews_array( $settings );
		if ( empty( $reviews ) ) {
			return;
		}

		$layout_class = ( 'carousel' === $settings['review_structure'] ) ? 'uael-reviews-layout-carousel' : 'uael-reviews-layout-grid';
		$image_align  = ( 'yes' === $this->get_instance_value( 'reviewer_image' ) ) ? 'uael-review-image-' . $this->get_instance_value( 'image_align' ) : '';

		$this->add_render_attribute(
			'uael-reviews-parent-data',
			array(
				'class'            => 'uael-reviews-widget-wrapper ' . $image_align . ' ' . $layout_class,
				'data-review-skin' => $settings['_skin'],
				'data-layout'      => $settings['review_structure'],
			)
		);

		if ( 'carousel' === $settings['review_structure'] || 'bubble' === $settings['_skin'] ) {
			$this->add_render_attribute(
				'uael-reviews-parent-data',
				array(
					'data-equal-height' => $settings['equal_height'],
				)
			);
		}

		?>

		<div class="uael-business-reviews-widget">
			<div <?php echo wp_kses_post( sanitize_text_field( $this->get_render_attribute_string( 'uael-reviews-parent-data' ) ) ); ?> <?php echo wp_kses_post( sanitize_text_field( $this->get_carousel_attr() ) ); ?>>
				<?php
				if ( 'rating' === $settings['reviews_filter_by'] ) {
					usort( $reviews, array( $this, 'filter_by_rating' ) );
				} elseif ( 'date' === $settings['reviews_filter_by'] ) {
					usort( $reviews, array( $this, 'filter_by_date' ) );
				}

				if ( 'google' === $settings['review_type'] ) {
					$reviews_max        = 5;
					$disply_num_reviews = $settings['google_reviews_number'];
				} elseif ( 'yelp' === $settings['review_type'] ) {
					$reviews_max        = 3;
					$disply_num_reviews = $settings['yelp_reviews_number'];
				} elseif ( 'all' === $settings['review_type'] ) {
					$reviews_max        = 8;
					$disply_num_reviews = $settings['total_reviews_number'];
				}

				$disply_num_reviews = ( '' !== $disply_num_reviews ) ? $disply_num_reviews : $reviews_max;

				if ( $reviews_max !== $disply_num_reviews ) {
					$display_number = (int) $disply_num_reviews;
					$reviews        = array_slice( $reviews, 0, $display_number );
				}

				$min_rating      = $settings['reviews_min_rating'];
				$aggregate_count = count( $reviews );

				foreach ( $reviews as $key => $review ) {
					$aggregate += $review['rating'];
					include UAEL_MODULES_DIR . 'business-reviews/templates/content-reviews-' . self::$skin . '.php';
				}

				$aggregate = $aggregate / $aggregate_count;

				if ( is_float( $aggregate ) ) {
					$aggregate = number_format( $aggregate, 1 );
				}
				?>
			</div>
			<?php if ( 'both' === $settings['navigation'] || 'dots' === $settings['navigation'] ) { ?>
				<div class="uael-business-reviews-footer"></div>
			<?php } ?>
		</div>
		<?php
	}

	/**
	 * Render settings array for selected skin
	 *
	 * @since 1.13.0
	 * @param string $control_base_id Skin ID.
	 * @access public
	 */
	public function get_instance_value( $control_base_id ) {
		if ( isset( self::$settings[ self::$skin . '_' . $control_base_id ] ) ) {
			return self::$settings[ self::$skin . '_' . $control_base_id ];
		} else {
			return null;
		}
	}

	/**
	 * Add render attribute.
	 *
	 * Used to add attributes to a specific HTML element.
	 *
	 * The HTML tag is represented by the element parameter, then you need to
	 * define the attribute key and the attribute key. The final result will be:
	 * `<element attribute_key="attribute_value">`.
	 *
	 * Example usage:
	 *
	 * `$this->add_render_attribute( 'wrapper', 'class', 'custom-widget-wrapper-class' );`
	 * `$this->add_render_attribute( 'widget', 'id', 'custom-widget-id' );`
	 * `$this->add_render_attribute( 'button', [ 'class' => 'custom-button-class', 'id' => 'custom-button-id' ] );`
	 *
	 * @since 1.13.0
	 * @access public
	 *
	 * @param array|string $element   The HTML element.
	 * @param array|string $key       Optional. Attribute key. Default is null.
	 * @param array|string $value     Optional. Attribute value. Default is null.
	 * @param bool         $overwrite Optional. Whether to overwrite existing
	 *                                attribute. Default is false, not to overwrite.
	 *
	 * @return Element_Base Current instance of the element.
	 */
	public function add_render_attribute( $element, $key = null, $value = null, $overwrite = false ) {
		if ( is_array( $element ) ) {
			foreach ( $element as $element_key => $attributes ) {
				$this->add_render_attribute( $element_key, $attributes, null, $overwrite );
			}

			return $this;
		}

		if ( is_array( $key ) ) {
			foreach ( $key as $attribute_key => $attributes ) {
				$this->add_render_attribute( $element, $attribute_key, $attributes, $overwrite );
			}

			return $this;
		}

		if ( empty( $this->_render_attributes[ $element ][ $key ] ) ) {
			$this->_render_attributes[ $element ][ $key ] = array();
		}

		settype( $value, 'array' );

		if ( $overwrite ) {
			$this->_render_attributes[ $element ][ $key ] = $value;
		} else {
			$this->_render_attributes[ $element ][ $key ] = array_merge( $this->_render_attributes[ $element ][ $key ], $value );
		}

		return $this;
	}

	/**
	 * Get render attribute string.
	 *
	 * Used to retrieve the value of the render attribute.
	 *
	 * @since 1.13.0
	 * @access public
	 *
	 * @param array|string $element The element.
	 *
	 * @return string Render attribute string, or an empty string if the attribute
	 *                is empty or not exist.
	 */
	public function get_render_attribute_string( $element ) {
		if ( empty( $this->_render_attributes[ $element ] ) ) {
			return '';
		}

		$render_attributes = $this->_render_attributes[ $element ];

		$attributes = array();

		foreach ( $render_attributes as $attribute_key => $attribute_values ) {
			$attributes[] = sprintf( '%1$s="%2$s"', $attribute_key, esc_attr( implode( ' ', $attribute_values ) ) );
		}

		return implode( ' ', $attributes );
	}

}
