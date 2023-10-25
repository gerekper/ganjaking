<?php


/**
 * Get relative time from date.
 *
 * @param string $date Date.
 * @return string $render Relative Date.
 */
function seedprod_pro_timeago( $date ) {
	$timestamp = strtotime( $date );

	$str_time = array( 'second', 'minute', 'hour', 'day', 'month', 'year' );
	$length   = array( '60', '60', '24', '30', '12', '10' );

	$current_time = time();
	if ( $current_time >= $timestamp ) {
		$diff        = time() - $timestamp;
		$countlength = count( $length ) - 1;
		for ( $i = 0; $diff >= $length[ $i ] && $i < $countlength; $i++ ) {
			$diff = $diff / $length[ $i ];
		}

		$diff  = round( $diff );
		$extra = $diff > 1 ? 's' : '';

		$render = $diff . ' ' . $str_time[ $i ] . $extra . ' ago ';
		return $render;
	}
}

/**
 * Get sorted array by review
 *
 * @param array $review1 Review Array.
 * @param array $review2 Review Array.
 */
function seedprod_pro_filter_by_rating( $review1, $review2 ) {
	return strcmp( $review2['rating'], $review1['rating'] );
}

/**
 * Get sorted array by time
 *
 * @param array $review1 Review Array.
 * @param array $review2 Review Array.
 */
function seedprod_pro_filter_by_date( $review1, $review2 ) {
	return strcmp( $review2['time'], $review1['time'] );
}

add_shortcode( 'businessreview', 'seedprod_pro_render_business_review_block_shortcode' );

/**
 * Business Review - Google and Yelp Reviews shorcode
 *
 * @param array $atts Shortcode attributes.
 * @return string|void $render Html render.
 */
function seedprod_pro_render_business_review_block_shortcode( array $atts ) {
	global $wp_query;

	$shortcode_args = shortcode_atts(
		array(
			'reviewsource'     => 'googleplaces',
			'googleplaceid'    => '',
			'yelpbusinessid'   => '',
			'languagecode'     => '',
			'imageposition'    => 'abovename',
			'filterby'         => 'rating',
			'minimumrating'    => 'no',
			'reviewerimage'    => 'true',
			'reviewername'     => 'true',
			'reviewerlinkname' => 'true',
			'reviewdate'       => 'true',
			'reviewrating'     => 'true',
			'reviewtext'       => 'true',
			'layout'           => 'grid',
			'reviewdatetype'   => 'relative',
			'slidetoshow'      => 4,
			'numofreviews'     => 5,
			'reviewtextlength' => 13,
			'navcolor'         => 'd',
			'readmoretext'     => 'read more',
		),
		$atts
	);

	$render = '';
	$args   = array();

	$data = seedprod_pro_render_business_review_layout( $shortcode_args, 'shortcode' );

	$review_data = json_decode( $data );
	$render      = $review_data->html;

	return $render;
}

/**
 * Fetch Business Review Layout data.
 *
 * @param array  $settings_data Settings.
 * @param string $setting_type Type.
 * @return JSON object.
 */
function seedprod_pro_render_business_review_layout( $settings_data, $setting_type ) {

		$business_reviews = array();

		$business_reviews_settings = $settings_data;

		$reviews_array = array();

		$display_reviews = sanitize_text_field( wp_unslash( $business_reviews_settings['numofreviews'] ) );

		$reviewsourcesettings = sanitize_text_field( wp_unslash( $business_reviews_settings['reviewsource'] ) );
		$filterbysettings     = sanitize_text_field( wp_unslash( $business_reviews_settings['filterby'] ) );

		$layoutsettings = sanitize_text_field( wp_unslash( $business_reviews_settings['layout'] ) );

		$slidetoshowsettings = absint( $business_reviews_settings['slidetoshow'] );

	if ( 'googleplaces' === $reviewsourcesettings ) {

		$reviews_array = seedprod_pro_get_google_places( $business_reviews_settings );
		$reviews_array = seedprod_pro_get_reviews_filtered( 'googleplaces', $reviews_array, $business_reviews_settings );

		$max_reviews = 5;

	} elseif ( 'yelp' === $reviewsourcesettings ) {

		$reviews_array = seedprod_pro_get_yelp_reviews( $business_reviews_settings );
		$reviews_array = seedprod_pro_get_reviews_filtered( 'yelp', $reviews_array, $business_reviews_settings );
		$max_reviews   = 3;

	} elseif ( 'googleyelp' === $reviewsourcesettings ) {

		$google_reviews = seedprod_pro_get_google_places( $business_reviews_settings );
		$google_reviews = seedprod_pro_get_reviews_filtered( 'googleplaces', $google_reviews, $business_reviews_settings );

		$yelp_reviews = seedprod_pro_get_yelp_reviews( $business_reviews_settings );
		$yelp_reviews = seedprod_pro_get_reviews_filtered( 'yelp', $yelp_reviews, $business_reviews_settings );

		if ( empty( $google_reviews ) || empty( $yelp_reviews ) ) {
			return;
		}

		$max_reviews = 8;

		$count = count( $google_reviews );

		for ( $i = 0; $i < $count; $i++ ) {
			$reviews_array[] = $google_reviews[ $i ];
			if ( $i < count( $yelp_reviews ) ) {
				$reviews_array[] = $yelp_reviews[ $i ];
			}
		}
		$reviews_array = array_filter( $reviews_array );

	}
	if ( empty( $reviews_array ) ) {
		$data = wp_json_encode(
			array(
				'html'   => '',
				'length' => 0,
			)
		);
		return $data;
	}

		$display_reviews = ( '' !== $display_reviews ) ? $display_reviews : $max_reviews;
	if ( $max_reviews !== $display_reviews ) {
		$display_number = (int) $display_reviews;
		$reviews_array  = array_slice( $reviews_array, 0, $display_number );
	}

	if ( 'rating' === $filterbysettings ) {
		usort( $reviews_array, 'seedprod_pro_filter_by_rating' );
	} elseif ( 'reviewdate' === $filterbysettings ) {
		usort( $reviews_array, 'seedprod_pro_filter_by_date' );
	}

		$reviews_array_count = count( $reviews_array );

		$return_html = '';

		$return_html .= '<div class="seedprod-business-reviews-block">';

	foreach ( $reviews_array as $k => $review ) {

		$photo_url   = ( null !== $review['profile_photo_url'] ) ? $review['profile_photo_url'] : '';
		$width_class = '';

		$index_carousel = $k + 1;

		$extra_styles = '';
		if ( 'carousel' === $layoutsettings ) {
			$width_class  = 'show-reviews-area';
			$extra_styles = 'opacity: 1;';
			if ( $index_carousel > $slidetoshowsettings ) {
				$width_class  = 'hidden-reviews-area';
				$extra_styles = 'opacity: 0; position:absolute;';
			}
		}

		if ( 'shortcode' === $setting_type ) {
			$width_class = '';
		} else {
			$extra_styles = '';
		}

		$return_html .= '<div class="seedprod-business-review-wrapper ' . $width_class . ' index-' . $index_carousel . '" 
					data-index="' . $k . '" style="' . $extra_styles . '">
						<div class="seedprod-business-inner-block">
							<div class="seedprod-business-review-header-block">
						';

		if ( 'true' === $business_reviews_settings['reviewerimage'] && 'leftofcontent' !== $business_reviews_settings['imageposition'] ) {
				 $return_html .= ' <div class="seedprod-business-review-image " style="background-image:url( ' . esc_url( $photo_url ) . ' );"></div>';
		}

				$return_html .= '<div class="seedprod-review-details sp-block">';

		if ( 'true' === $business_reviews_settings['reviewername'] ) {
			if ( 'true' === $business_reviews_settings['reviewerlinkname'] ) {

					$return_html .= '<span class="seedprod-review-name sp-block">' . wp_kses_post( "<a href={$review['author_url']} target='_blank'>{$review['author_name']}</a>" ) . '</span>';

			} else {
						$return_html .= '<span class="seedprod-review-name sp-block">' . wp_kses_post( "{$review['author_name']}" ) . '</span>';

			}
		}
						$stars_html = '';
		if ( 'true' === $business_reviews_settings['reviewrating'] ) {
						$rating = wp_kses_post( $review['rating'] );
			for ( $stars = 1; $stars <= 5; $stars++ ) {
				if ( $stars <= $rating ) {
					$stars_html .= '<i class="fa-fw fas fa-star fa-star-yellow" aria-hidden="true"></i>';
				} else {
					$stars_html .= '<i class="fa-fw fas fa-star fa-star-gray" aria-hidden="true"></i>';
				}
			}

						$return_html .= '
											<span class="seedprod-review-star-rating sp-block">
												<span class="seedprod-star-rating">
													' . wp_kses_post( $stars_html ) . '
												</span>
											</span>
										';

		}

		if ( 'true' === $business_reviews_settings['reviewdate'] ) {

			$timestamp  = ( 'googleplaces' === $reviewsourcesettings ) ? $review['time'] : strtotime( $review['time'] );
			$reviewdate = gmdate( 'd-m-Y', $timestamp );

			if ( 'relative' === $business_reviews_settings['reviewdatetype'] ) {
				$reviewdate = $review['relative_time_description'];
			}
			$source = $review['source'];

			$return_html .= '<span class="seedprod-review-time  sp-block">' . ( $reviewdate ) . ' - ' . esc_attr( ucfirst( $source ) ) . '</span>';
			?>
											
			<?php

		}

											$return_html .= '
									</div>
							</div>';

		if ( 'true' === $business_reviews_settings['reviewtext'] ) {
			$the_content = $review['text'];

			if ( '' !== $business_reviews_settings['reviewtextlength'] ) {
				$the_content    = wp_strip_all_tags( $review['text'] ); // Strips tags.
				$content_length = $business_reviews_settings['reviewtextlength']; // Sets content length by word count.
				$words          = explode( ' ', $the_content, $content_length + 1 );
				if ( count( $words ) > $content_length ) {
					array_pop( $words );
					$the_content  = implode( ' ', $words ); // put in content only the number of word that is set in $content_length.
					$the_content .= '...';
					if ( '' !== $business_reviews_settings['readmoretext'] ) {
						$the_content .= '<a href="' . $review['review_url'] . '"  target="_blank" rel="noopener noreferrer" class="seedprod-review-read-more">' . $business_reviews_settings['readmoretext'] . '</a>';
					}
				}
			}

			$return_html .= '
									<div class="seedprod-business-review-content">
										' . wp_kses_post( $the_content ) . '
									</div>
								';

		}
		?>
						
					<?php

					$return_html .= '
					</div>
					</div>
					';

	}

		$return_html .= '
					</div>
					';

	if ( $reviews_array_count > 0 ) {
		if ( 'carousel' === $business_reviews_settings['layout'] ) {
			if ( 'shortcode' === $setting_type ) {

				$numofpages = ceil( $reviews_array_count / $business_reviews_settings['slidetoshow'] );
				if ( $numofpages > 1 ) {

					$bgcolormode   = 'sp-bg-white';
					$textcolormode = 'sp-text-white';

					if ( 'd' === $business_reviews_settings['navcolor'] ) {
						$bgcolormode   = 'sp-bg-black';
						$textcolormode = 'sp-text-black';
					}

					$return_html .= ' 
							<div class="sp-businessreview-nav sp-flex sp-justify-center sp-items-center sp-mt-2">

							<button class="sp-outline-none focus:sp-outline-none sp-px-3 sp-opacity-50 sp-text-base ' . $textcolormode . '">
							<i class="fas fa-angle-left"></i>
							</button>
						';

					for ( $z = 0; $z < $numofpages; $z++ ) {
						$styles = 'opacity: 0.25;';
						if ( 0 === $z ) {
							$styles = 'opacity: 1;';
						}
						$return_html .= ' 	
								<button data-index="' . $z . '" class="focus:sp-outline-none sp-w-3 sp-h-3 sp-block sp-mx-1 sp-opacity-25 sp-rounded-full sp-opacity-75 ' . $bgcolormode . '" style="' . $styles . '"></button>
							';
					}

					$return_html .= ' 
							<button class="sp-outline-none focus:sp-outline-none sp-px-3 sp-opacity-50 text-base ' . $textcolormode . '"><i class="fas fa-angle-right"></i></button></div>
						';

				}
			}
		}
	}

		$data = wp_json_encode(
			array(
				'html'   => $return_html,
				'length' => $reviews_array_count,
			)
		);

		return $data;
}

/**
 * Get Business Review data by ajax.
 *
 * @return JSON|string|void data.
 */
function seedprod_pro_render_business_review_shortcode() {

	if ( check_ajax_referer( 'seedprod_nonce' ) ) {
		if ( ! current_user_can( apply_filters( 'seedprod_builder_preview_render_capability', 'edit_others_posts' ) ) ) {
			wp_send_json_error();
		}

		$posted_data = $_POST;
		$items       = $posted_data;

		$business_reviews_settings = $items['settings'];

		$data = seedprod_pro_render_business_review_layout( $business_reviews_settings, 'default' );

		echo $data; // phpcs:ignore

		exit;

	}

}
/**
 * Get Google Review data.
 *
 * @param array $business_reviews_settings Settings.
 * @return JSON object.
 */
function seedprod_pro_get_google_places( $business_reviews_settings ) {

	$seedprod_app_settings = json_decode( get_option( 'seedprod_app_settings' ) );

	$google_api_key = $seedprod_app_settings->google_places_app_key;

	$google_placeid = trim( $business_reviews_settings['googleplaceid'] );
	$languagecode   = trim( $business_reviews_settings['languagecode'] );
	$api_url        = "https://maps.googleapis.com/maps/api/place/details/json?key=$google_api_key&placeid=$google_placeid&language=$languagecode";

	$response = wp_remote_post(
		$api_url,
		array(
			'method'      => 'POST',
			'timeout'     => 60,
			'httpversion' => '1.0',
		)
	);

	if ( is_wp_error( $response ) ) {
		return $response->get_error_message();
	}

	$response = json_decode( wp_remote_retrieve_body( $response ) );
	$status   = $response->status;
	if ( 'OK' === $status ) {
		if ( ! property_exists( $response->result, 'reviews' ) ) {
			$errors = __( 'No Reviews found.', 'seedprod-pro' );
			return $errors;
		} else {
			$response = $response->result->reviews;
		}
	} else {
		$response = '';
	}

	return $response;
}

/**
 * Get Yelp Review data.
 *
 * @param array $business_reviews_settings Settings.
 * @return JSON object.
 */
function seedprod_pro_get_yelp_reviews( $business_reviews_settings ) {
	$seedprod_app_settings = json_decode( get_option( 'seedprod_app_settings' ) );
	$yelp_api_key          = $seedprod_app_settings->yelp_app_api_key;
	$yelpbusinessid        = $business_reviews_settings['yelpbusinessid'];
	$api_url               = 'https://api.yelp.com/v3/businesses/' . $yelpbusinessid . '/reviews';
	$response              = wp_remote_get(
		$api_url,
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

	if ( is_wp_error( $response ) ) {
		return $response->get_error_message();
	}

	$status = wp_remote_retrieve_response_code( $response );
	$result = json_decode( wp_remote_retrieve_body( $response ) );

	if ( 200 !== $status ) {
		$errors = __( 'No Reviews found.', 'seedprod-pro' );
		return $errors;
	} else {
		$response = $result->reviews;
	}
	return $response;
}

/**
 * Get Reviews array with the same key for Google & Yelp.
 *
 * @param string $sourcetype Source Type.
 * @param array  $reviews_array Review array.
 * @param array  $business_reviews_settings Settings.
 * @return the merged array of Google & Yelp reviews.
 */
function seedprod_pro_get_reviews_filtered( $sourcetype, $reviews_array, $business_reviews_settings ) {

	if ( empty( $sourcetype ) ) {
		return;
	}

	$reviews_list      = array();
	$min_rating_filter = false;

	if ( 'no' !== $business_reviews_settings['minimumrating'] ) {
		$min_rating_filter = true;
	}

	foreach ( $reviews_array as $key => $value ) {

		if ( 'googleplaces' === $sourcetype ) {
			$user_review_url = explode( '/reviews', $value->author_url );
			array_pop( $user_review_url );
			$review_url                          = $user_review_url[0] . '/place/' . $business_reviews_settings['googleplaceid'];
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
		if ( 'yelp' === $sourcetype ) {

			$review['source']                    = 'yelp';
			$review['author_name']               = $value->user->name;
			$review['author_url']                = $value->user->profile_url;
			$review['profile_photo_url']         = $value->user->image_url;
			$review['rating']                    = $value->rating;
			$review['relative_time_description'] = seedprod_pro_timeago( gmdate( 'd-m-Y', strtotime( $value->time_created ) ) );
			$review['text']                      = $value->text;
			$review['time']                      = $value->time_created;
			$review['review_url']                = $value->url;

		}

		if ( $min_rating_filter ) {
			if ( $value->rating >= $business_reviews_settings['minimumrating'] ) {
				array_push( $reviews_list, $review );
			}
		} else {
			array_push( $reviews_list, $review );
		}
	}

	return $reviews_list;

}


