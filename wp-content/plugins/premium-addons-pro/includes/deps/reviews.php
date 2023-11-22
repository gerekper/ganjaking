<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'PREMIUM_FB_REV_GRAPH_API', 'https://graph.facebook.com/v5.0/' );

define( 'PREMIUM_GOOGLE_PLACE_API', 'https://maps.googleapis.com/maps/api/place/' );

define( 'PREMIUM_FB_REV_AVATAR', '<svg class="premium-fb-rev-img" id="Capa_1" data-name="Capa 1" xmlns="http://www.w3.org/2000/svg" width="512" height="512" viewBox="0 0 512 512"><defs><style>.cls-1{fill:#f2f2f5;}.cls-2{fill:#c0c0c9;}</style></defs><rect class="cls-1" width="512" height="512"/><path class="cls-2" d="M421.58,368.64a84.27,84.27,0,0,0-35-16.19l-62.38-12.52A14.07,14.07,0,0,1,312.89,326V311.68c4-5.62,7.8-13.11,11.76-20.93,3.06-6.06,7.7-15.19,10-17.55,12.64-12.69,24.84-26.94,28.62-45.33,3.56-17.24.06-26.29-4-33.57,0-18.18-.57-40.89-4.86-57.49-.52-22.42-4.58-35-14.82-46-7.23-7.83-17.87-9.65-26.43-11.1-3.36-.57-8-1.37-9.7-2.27a100.27,100.27,0,0,0-48-12.52c-37.47,1.54-83.56,25.37-98.94,67.87-4.77,12.94-4.29,34.2-3.89,51.27l-.37,10.26c-3.67,7.17-7.28,16.27-3.73,33.57,3.75,18.4,16,32.68,28.82,45.54,2.1,2.16,6.85,11.38,10,17.47,4,7.79,7.83,15.24,11.84,20.84V326a14.14,14.14,0,0,1-11.33,13.94l-62.5,12.53a84.65,84.65,0,0,0-34.91,16.17,14.22,14.22,0,0,0-2.3,20c.2.25.41.49.62.73a226.48,226.48,0,0,0,334.52,0,14.21,14.21,0,0,0-1-20.09c-.23-.22-.47-.42-.72-.62Z"/></svg>' );

define( 'PREMIUM_YELP_API', 'https://api.yelp.com/v3/businesses' );

define( 'PREMIUM_INSTA_LINK', 'https://www.instagram.com/' );

define( 'PREMIUM_INSTA_API_ENDPOINT', '/?__a=1' );

use PremiumAddons\Includes\Helper_Functions;


/**
 * Get Instagram Profile Info.
 *
 * @param array $settings widget settings.
 */
function premium_insta_profile_info( $settings ) {

	$user_name = $settings['user_name'];

	if ( 0 !== strlen( $user_name ) ) {

		$cached = is_profile_cached( $user_name );

		if ( false === $cached ) {

			$expire_time = $settings['reload'];

			$trans_name = 'instaFeed_acc_' . $user_name;

			$api_url = PREMIUM_INSTA_LINK . $user_name . PREMIUM_INSTA_API_ENDPOINT;

			$api_response = rplg_urlopen( $api_url );

			if ( is_wp_error( $api_response ) ) {

				$error_message = $api_response->get_error_message(); ?>

				<div class="premium-error-notice">
					<?php echo wp_kses_post( sprintf( 'Something went wrong: %s', $error_message ) ); ?>
				</div>
				<?php
				return;
			}

			$api_response = rplg_json_decode( $api_response['data'] );

			if ( ! isset( $api_response->graphql ) ) {
				?>
				<div class="premium-error-notice">
					<?php echo esc_html( __( 'It seems there was a problem fetching your profile data, please make sure the username is correct', 'premium-addons-pro' ) ); ?>
				</div>
				<?php
				return;
			}

			$profile = $api_response->graphql->user;

			$profile_info = array(
				'pic_url'     => $profile->profile_pic_url_hd,
				'full_name'   => $profile->full_name,
				'bio'         => $profile->biography,
				'followed_by' => $profile->edge_followed_by->count,
				'following'   => $profile->edge_follow->count,
				'is_verified' => $profile->is_verified,
				'posts'       => $profile->edge_owner_to_timeline_media->count,
			);

			set_transient( $trans_name, $profile_info, $expire_time );

			return premium_parse_insta_profile( $settings, $profile_info );
		} else {
			return premium_parse_insta_profile( $settings, $cached );
		}
	} else {
		?>
		<div class="premium-error-notice">
			<?php echo esc_html( __( 'Please fill the required fields: Username', 'premium-addons-pro' ) ); ?>
		</div>
		<?php
		return;
	}
}

/**
 * Parse Insta Profile Data.
 *
 * @param array $settings widget settings.
 * @param array $info profile info.
 */
function premium_parse_insta_profile( $settings, $info ) {

	$user_name = $settings['user_name'];

	// user preferences [to show].
	$username    = $settings['show_username'];
	$profile_pic = $settings['profile_pic'];
	$followers   = $settings['followers'];
	$following   = $settings['following'];
	$bio         = $settings['bio'];
	$posts       = $settings['posts'];
	$verfiy      = $settings['verify'];
	$v_icon      = ( $verfiy && $info['is_verified'] ) ? '<span class="instafeed-v-icon"><i class="far fa-check-circle"></i></span>' : '';
	$html_name   = '<span class="premium-instafeed-username">' . $info['full_name'] . ' (@' . $user_name . ')</span>';
	$link        = '<a href="' . PREMIUM_INSTA_LINK . $user_name . '" target="_blank">' . $html_name . '</a>';
	?>
	<div class="premium-instafeed-header-upper">
		<?php if ( $profile_pic ) : ?>
			<div class="premium-instafeed-header-pic-wrapper">
				<img src="<?php echo esc_url( $info['pic_url'] ); ?>" alt="<?php echo esc_attr( $user_name ) . '\'s profile picture'; ?>">
			</div>
		<?php endif; ?>
		<div class="premium-instafeed-header-user-wrapper">
		<?php if ( $username ) : ?>
			<span class="premium-instafeed-username-outer">
				<?php echo wp_kses_post( $link . $v_icon ); ?>
			</span>
		<?php endif; ?>
		<?php if ( $bio ) : ?>
			<span class="premium-instafeed-bio"><?php echo esc_html( $info['bio'] ); ?></span>
		<?php endif; ?>
			<div class="premium-instafeed-user-activity">
				<?php if ( $posts ) : ?>
					<div class="premium-instafeed-user-activity-item">
						<span class='premium-insta-lower-item-val'><?php echo wp_kses_post( Helper_Functions::premium_format_numbers( $info['posts'] ) ); ?></span>
						<span><?php echo esc_html( __( 'Posts', 'premium-addons-pro' ) ); ?></span>
					</div>
				<?php endif; ?>
				<?php if ( $followers ) : ?>
					<div class="premium-instafeed-user-activity-item">
						<span class='premium-insta-lower-item-val'><?php echo wp_kses_post( Helper_Functions::premium_format_numbers( $info['followed_by'] ) ); ?></span>
						<span><?php echo esc_html( __( 'Followers', 'premium-addons-pro' ) ); ?></span>
					</div>
				<?php endif; ?>
				<?php if ( $following ) : ?>
					<div class="premium-instafeed-user-activity-item">
						<span class='premium-insta-lower-item-val'><?php echo wp_kses_post( Helper_Functions::premium_format_numbers( $info['following'] ) ); ?></span>
						<span><?php echo esc_html( __( 'Following', 'premium-addons-pro' ) ); ?></span>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>

	<?php
}

/**
 * Checks If Profile Info Is Cached.
 *
 * @param string $username  account username.
 */
function is_profile_cached( $username ) {

	$trans_name = 'instaFeed_acc_' . $username;

	$cached = get_transient( $trans_name );

	return $cached;
}


/**
 * Gets JSON Data from Facebook
 *
 * @since 1.0.0
 *
 * @param string $page_id page ID.
 * @param string $page_access_token page access token.
 */
function premium_fb_rev_api_rating( $page_id, $page_access_token ) {

	$api_url = PREMIUM_FB_REV_GRAPH_API . $page_id . '/ratings?access_token=' . $page_access_token . '&fields=reviewer{id,name,picture.width(100).height(100)},created_time,rating,recommendation_type,review_text,open_graph_story{id}&limit=9999';

	$api_response = rplg_urlopen( $api_url );

	return $api_response;
}

/**
 * Gets Page Data from Facebook
 *
 * @since 1.0.0
 *
 * @param string $page_id page ID.
 * @param object $settings widget settings.
 */
function premium_fb_rev_page( $page_id, $settings ) {

	$custom_image = $settings['image'];

	$page_name = $settings['name'];

	$page_rate = $settings['rate'];

	$rating = $settings['rating'];

	$reviews_number = $settings['rev_number'];

	$reviews_count = $settings['rev_count'];

	$fill_color = $settings['fill_color'];

	$empty_color = $settings['empty_color'];

	$show_stars = $settings['stars'];

	$star_size = $settings['size'];

	if ( $settings['show_image'] ) {
		if ( empty( $custom_image ) ) {
			$page_img = 'https://graph.facebook.com/' . $page_id . '/picture';
		} else {
			$page_img = $custom_image;
		}
	}

	if ( $settings['show_name'] ) {
		$page_link = sprintf( '<a class="premium-fb-rev-page-link" href="https://fb.com/%s" target="_blank" title="%2$s">%2$s</a>', $page_id, $page_name );
	}

	if ( $settings['show_image'] ) :
		?>
		<div class="premium-fb-rev-page-left">
			<img class="premium-fb-rev-img" src="<?php echo esc_url( $page_img ); ?>" alt="<?php echo esc_attr( $page_name ); ?>">
		</div>
	<?php endif; ?>

	<div class="premium-fb-rev-page-right">
		<?php if ( ! empty( $page_name ) && $settings['show_name'] ) : ?>
			<div class="premium-fb-rev-page-link-wrapper">
				<?php echo wp_kses_post( $page_link ); ?>
			</div>
		<?php endif; ?>

		<div class="premium-fb-rev-page-rating-wrapper">
			<?php if ( $page_rate ) : ?>
				<span class="premium-fb-rev-page-rating"><?php echo wp_kses_post( $rating ); ?></span>
			<?php endif; ?>

			<?php if ( $show_stars ) : ?>
				<span class="premium-fb-rev-page-stars"><?php premium_fb_rev_stars( $rating, $fill_color, $empty_color, $star_size ); ?></span>
			<?php endif; ?>
		</div>
		<?php if ( isset( $reviews_count ) > 0 && 'yes' === $reviews_number ) : ?>
			<div class="premium-fb-rev-rating-count">
				<span><?php echo wp_kses_post( sprintf( $settings['number_text'], $reviews_count ) ); ?></span>
			</div>
		<?php endif; ?>
	</div>
	<?php
}

/**
 * Gets reviews data from Facebook
 *
 * @since 1.0.0
 *
 * @param array  $reviews Facebook reviews.
 * @param object $settings widget settings.
 */
function premium_fb_rev_reviews( $reviews, $settings ) {

	$limit = $settings['limit'];

	$min_filter = $settings['filter_min'];

	$max_filter = $settings['filter_max'];

	$show_date = $settings['date'];

	$show_stars = $settings['stars'];

	$date_format = $settings['format'];

	$fill_color = $settings['fill_color'];

	$empty_color = $settings['empty_color'];

	$star_size = $settings['stars_size'];

	$rev_text = $settings['text'];

	$length = $settings['rev_length'];

	$readmore = $settings['readmore'];

	$skin_type = $settings['skin_type'];

	?>

	<div class="premium-fb-rev-reviews">
		<?php
		if ( count( $reviews ) > 0 ) {
			array_splice( $reviews, $limit );
			foreach ( $reviews as $review ) {

				if ( ! isset( $review->review_text ) && 'yes' === $settings['hide_empty'] ) {
					continue;
				}

				if ( isset( $review->rating ) ) {
					$rating = $review->rating;
				} elseif ( isset( $review->recommendation_type ) ) {
					$rating = 'negative' === $review->recommendation_type ? 1 : 5;
				} else {
					$rating = 5;
				}

				if ( $rating < $min_filter || $rating > $max_filter ) {
					continue;
				}

				$review_url = isset( $review->open_graph_story ) ? $review->open_graph_story->id : '';
				$review_url = sprintf( 'https://facebook.com/%s', $review_url );

				if ( strlen( $review->reviewer->picture->data->url ) > 0 ) {
					$author_photo = '<img class="premium-fb-rev-img" src="' . esc_url( $review->reviewer->picture->data->url ) . '" alt="' . esc_attr( $review->reviewer->name ) . '">';
				} else {
					$author_photo = PREMIUM_FB_REV_AVATAR;
				}
				?>
			<div class="premium-fb-rev-review-wrap">
				<div class="premium-fb-rev-review">
					<div class="premium-fb-rev-review-inner">
						<?php
						if ( 'yes' === $settings['show_image'] ) {
							if ( ( 'default' === $skin_type && 'left' !== $settings['image_display'] ) || ( 'card' === $skin_type && 'inline' === $settings['image_display'] ) ) {
								?>
									<div class="premium-fb-rev-content-left">
										<?php echo $author_photo; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
									</div>
								<?php
							}
						}
						?>
						<div class="premium-fb-rev-content-right">
							<?php
							if ( 'bubble' === $skin_type || 'left' === $settings['image_display'] ) {
								?>
								<div class="premium-fb-rev-reviewer-header">
							<?php } ?>
							<?php if ( isset( $review->reviewer->id ) ) : ?>
								<div class="premium-fb-rev-reviewer-wrapper">
									<?php
										$person_link = '<a class="premium-fb-rev-reviewer-link" href="' . $review_url . '" target="_blank">' . $review->reviewer->name . '</a>';
										echo wp_kses_post( $person_link );
									?>
								</div>
							<?php endif; ?>

							<?php if ( $show_date || $show_stars ) : ?>
								<div class="premium-fb-rev-info">
									<?php if ( $show_date ) : ?>
										<div class="premium-fb-rev-time"><span class="premium-fb-rev-time-text"><?php echo date( $date_format, strtotime( $review->created_time ) ); ?></span></div>
									<?php endif; ?>

									<?php if ( $show_stars ) : ?>
										<div class="premium-fb-rev-stars-container">
											<?php
												echo premium_fb_rev_stars( $rating, $fill_color, $empty_color, $star_size );
											?>
										</div>
									<?php endif; ?>
								</div>
								<?php
								endif;
							if ( 'bubble' === $skin_type || 'left' === $settings['image_display'] ) {
								?>
									</div>
								<?php
							}

							if ( isset( $review->review_text ) && $rev_text ) :
								?>
								<div class="premium-fb-rev-rating">
									<div class="premium-fb-rev-text-wrapper">
										<span class="premium-fb-rev-text reviews"><?php $review->more = premium_fb_rev_trim_text( $review->review_text, $length ); ?></span>
										<?php if ( $review->more ) : ?>
											<a class="premium-fb-rev-readmore" href="<?php echo esc_url( $review_url ); ?>" target="_blank" rel="noopener noreferrer"><?php echo wp_kses_post( $readmore ); ?></a>
										<?php endif; ?>
									</div>
									<?php if ( 'bubble' === $skin_type && $settings['bubble_arrow'] ) { ?>
										<div class="premium-rev-arrow-bubble">
											<div class="premium-rev-arrow-bubble-border"></div>
											<div class="premium-rev-arrow"></div>
										</div>
									<?php } ?>
								</div>
							<?php endif; ?>
							<?php
							if ( $settings['show_image'] ) {
								if ( ( 'card' === $skin_type && 'inline' !== $settings['image_display'] ) || 'bubble' === $skin_type || 'left' === $settings['image_display'] ) {
									?>
									<div class="premium-fb-rev-content-left">
										<?php echo $author_photo; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
									</div>
									<?php
								}
							}
							?>
						</div>

						<?php if ( 'yes' === $settings['show_icon'] ) { ?>
							<div class="premium-fb-rev-icon">
								<svg aria-hidden="true" focusable="false" data-prefix="fab" data-icon="facebook" class="svg-inline--fa fa-facebook fa-w-16" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="#1877F2" d="M504 256C504 119 393 8 256 8S8 119 8 256c0 123.78 90.69 226.38 209.25 245V327.69h-63V256h63v-54.64c0-62.15 37-96.48 93.67-96.48 27.14 0 55.52 4.84 55.52 4.84v61h-31.28c-30.8 0-40.41 19.12-40.41 38.73V256h68.78l-11 71.69h-57.78V501C413.31 482.38 504 379.78 504 256z"></path></svg>
							</div>
						<?php } ?>

					</div>
				</div>
			</div>
				<?php

			}
		}
		?>
	</div>
	<?php
}

/**
 * Gets JSON Data from Google
 *
 * @since 1.0.0
 *
 * @param string $api_key API key.
 * @param string $place_id place ID.
 * @param string $prefix language prefix.
 * @param string $sort sort by.
 */
function premium_google_rev_api_rating( $api_key, $place_id, $prefix, $sort ) {

	$language = '';

	if ( ! empty( $prefix ) ) {
		$language = '&language=' . $prefix;
	}

	$api_url = PREMIUM_GOOGLE_PLACE_API . 'details/json?placeid=' . trim( $place_id ) . $language . '&reviews_sort=' . $sort . '&key=' . trim( $api_key );

	$api_response = rplg_urlopen( $api_url );

	return $api_response;

}

/**
 * Render Place Layout
 *
 * @since 1.0.0
 *
 * @param object $place Google place.
 * @param object $settings widget settings.
 */
function premium_reviews_place( $place, $settings ) {

	$custom_image = $settings['image'];

	$rating = $settings['rating'];

	$fill_color = $settings['color'];

	$empty_color = $settings['empty_color'];

	$show_stars = $settings['stars'];

	$star_size = $settings['stars_size'];

	$place_rate = $settings['place_rate'];

	$reviews_number = $settings['rev_number'];

	$api_key = $settings['key'];

	$id = $settings['id'];

	if ( $settings['show_image'] ) :
		?>
		<div class="premium-fb-rev-page-left">
			<?php
			if ( empty( $custom_image ) ) {

				$image = premium_place_avatar( $place, $api_key );

				if ( ! empty( $image ) ) {
					$place_img = $image;
				} elseif ( ! empty( $place->icon ) ) {
					$place_img = $place->icon;
				} else {
					$place_img = '';
				}

				$place_img = str_replace( '/o.', '/ls.', $place_img );
				if ( isset( $place_img ) ) {
					update_option( 'premium_reviews_img-' . $id, $place_img );
				} else {
					$place_img = get_option( 'premium_reviews_img-' . $id );
				}
			} else {
				$place_img = $custom_image;
			}
			?>

			<img class="premium-fb-rev-img" src="<?php echo esc_url( $place_img ); ?>" alt="<?php echo esc_attr( $place->name ); ?>">
		</div>
	<?php endif; ?>

	<div class="premium-fb-rev-page-right">
		<?php if ( ! empty( $place->name ) && $settings['show_name'] ) : ?>
			<div class="premium-fb-rev-page-link-wrapper">
				<?php
				$place_link = '<a class="premium-fb-rev-page-link" href="' . $place->url . '" target="_blank">' . $place->name . '</a>';
				echo wp_kses_post( $place_link );
				?>
			</div>
		<?php endif; ?>

		<div class="premium-fb-rev-page-rating-wrapper">
			<?php if ( $place_rate ) : ?>
				<span class="premium-fb-rev-page-rating"><?php echo wp_kses_post( $rating ); ?></span>
			<?php endif; ?>

			<?php if ( $show_stars ) : ?>
				<span class="premium-fb-rev-page-stars"><?php premium_fb_rev_stars( $rating, $fill_color, $empty_color, $star_size ); ?></span>
			<?php endif; ?>
		</div>
		<?php if ( 'yes' === $reviews_number ) : ?>
			<?php if ( isset( $place->user_ratings_total ) ) : ?>
				<div class="premium-fb-rev-rating-count">
					<span><?php echo wp_kses_post( sprintf( $settings['number_text'], $place->user_ratings_total ) ); ?></span>
				</div>
			<?php elseif ( isset( $place->review_count ) ) : ?>
				<div class="premium-fb-rev-rating-count">
					<span><?php echo wp_kses_post( sprintf( $settings['number_text'], $place->review_count ) ); ?></span>
				</div>
			<?php endif; ?>
		<?php endif; ?>
	</div>
	<?php
}

/**
 * Gets place image from Google
 *
 * @since 1.0.0
 *
 * @param object $place_data Place Data.
 * @param string $api_key API key.
 */
function premium_place_avatar( $place_data, $api_key ) {

	if ( isset( $place_data->image_url ) ) {

		return $place_data->image_url;

	} elseif ( isset( $place_data->photos ) ) {

		$request_url = add_query_arg(
			array(
				'photoreference' => $place_data->photos[0]->photo_reference,
				'key'            => $api_key,
				'maxwidth'       => '800',
				'maxheight'      => '800',
			),
			'https://maps.googleapis.com/maps/api/place/photo'
		);

		$response = rplg_urlopen( $request_url );

		foreach ( $response['headers'] as $header ) {
			if ( strpos( $header, 'Location: ' ) !== false ) {
				return str_replace( 'Location: ', '', $header );
			}
		}
	}

	return null;
}

/**
 * Render Google Reviews Layout
 *
 * @since 1.0.0
 *
 * @param array  $reviews Google reviews.
 * @param object $settings widget settings.
 */
function premium_google_rev_reviews( $reviews, $settings ) {

	$limit = $settings['limit'];

	$min_filter = $settings['filter_min'];

	$max_filter = $settings['filter_max'];

	$show_date = $settings['date'];

	$show_stars = $settings['stars'];

	$date_format = $settings['format'];

	$fill_color = $settings['fill_color'];

	$empty_color = $settings['empty_color'];

	$star_size = $settings['stars_size'];

	$rev_text = $settings['text'];

	$length = $settings['rev_length'];

	$id = $settings['id'];

	$readmore = $settings['readmore'];

	$skin_type = $settings['skin_type'];

	?>

	<div class="premium-fb-rev-reviews">
		<?php
		if ( count( $reviews ) > 0 ) {
			array_splice( $reviews, $limit );
			foreach ( $reviews as $review ) {

				if ( $review->rating < $min_filter || $review->rating > $max_filter ) {
					continue;
				}

				if ( ( ! isset( $review->text ) || empty( $review->text ) ) && 'yes' === $settings['hide_empty'] ) {
					continue;
				}

				$review->more = false;

				if ( isset( $review->profile_photo_url ) && strlen( $review->profile_photo_url ) > 0 ) {
					$author_photo = '<img class="premium-fb-rev-img" src="' . esc_url( $review->profile_photo_url ) . '" alt="' . esc_attr( $review->author_name ) . '">';
				} else {
					$author_photo = PREMIUM_FB_REV_AVATAR;
				}

				?>
				<div class="premium-fb-rev-review-wrap">
					<div class="premium-fb-rev-review">
						<div class="premium-fb-rev-review-inner">
							<?php
							if ( 'yes' === $settings['show_image'] ) {
								if ( ( 'default' === $skin_type && 'left' !== $settings['image_display'] ) || ( 'card' === $skin_type && 'inline' === $settings['image_display'] ) ) {
									?>
										<div class="premium-fb-rev-content-left">
											<?php echo $author_photo; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
										</div>
									<?php
								}
							}
							?>
							<div class="premium-fb-rev-content-right">
								<?php if ( 'bubble' === $skin_type || 'left' === $settings['image_display'] ) { ?>
									<div class="premium-fb-rev-reviewer-header">
								<?php } ?>
								<?php if ( isset( $review->author_url ) ) { ?>
									<div class="premium-fb-rev-reviewer-wrapper">
										<?php
											$person_link = '<a class="premium-fb-rev-reviewer-link" href="' . $review->author_url . '" target="_blank">' . $review->author_name . '</a>';
												echo wp_kses_post( $person_link );
										?>
									</div>
								<?php } ?>

								<?php if ( $show_date || $show_stars ) : ?>
									<div class="premium-fb-rev-info">
										<?php if ( $show_date ) : ?>
											<div class="premium-fb-rev-time"><span class="premium-fb-rev-time-text"><?php echo date( $date_format, $review->time ); ?></span></div>
										<?php endif; ?>

										<?php if ( $show_stars ) : ?>
											<div class="premium-fb-rev-stars-container">
												<?php
													echo premium_fb_rev_stars( $review->rating, $fill_color, $empty_color, $star_size );
												?>
											</div>
										<?php endif; ?>
									</div>
									<?php
								endif;
								if ( 'bubble' === $skin_type || 'left' === $settings['image_display'] ) {
									?>
									</div>
									<?php
								}
								if ( isset( $review->text ) && $rev_text ) :
									?>
									<div class="premium-fb-rev-rating">
										<div class="premium-fb-rev-text-wrapper">
											<span class="premium-fb-rev-text"><?php $review->more = premium_fb_rev_trim_text( $review->text, $length ); ?></span>
											<?php
											if ( $review->more ) :
												$url = str_replace( 'reviews', 'place', $review->author_url );

												$review_url = sprintf( '%s/%s', $url, $id );
												?>

												<a class="premium-fb-rev-readmore" href="<?php echo esc_url( $review_url ); ?>" target="_blank" rel="noopener noreferrer"><?php echo wp_kses_post( $readmore ); ?></a>
												<?php endif; ?>
										</div>
											<?php if ( 'bubble' === $skin_type && $settings['bubble_arrow'] ) { ?>
											<div class="premium-rev-arrow-bubble">
												<div class="premium-rev-arrow-bubble-border"></div>
												<div class="premium-rev-arrow"></div>
											</div>
										<?php } ?>
										</div>
										<?php endif; ?>
								<?php
								if ( $settings['show_image'] ) {
									if ( ( 'card' === $skin_type && 'inline' !== $settings['image_display'] ) || 'bubble' === $skin_type || 'left' === $settings['image_display'] ) {
										?>
									<div class="premium-fb-rev-content-left">
										<?php echo $author_photo; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
									</div>
										<?php
									}
								}
								?>
								</div>
							<?php if ( 'yes' === $settings['show_icon'] ) { ?>
								<div class="premium-fb-rev-icon">
									<svg version="1.1" id="fi_281764" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;" xml:space="preserve"> <path style="fill:#FBBB00;" d="M113.47,309.408L95.648,375.94l-65.139,1.378C11.042,341.211,0,299.9,0,256 c0-42.451,10.324-82.483,28.624-117.732h0.014l57.992,10.632l25.404,57.644c-5.317,15.501-8.215,32.141-8.215,49.456 C103.821,274.792,107.225,292.797,113.47,309.408z"></path> <path style="fill:#518EF8;" d="M507.527,208.176C510.467,223.662,512,239.655,512,256c0,18.328-1.927,36.206-5.598,53.451 c-12.462,58.683-45.025,109.925-90.134,146.187l-0.014-0.014l-73.044-3.727l-10.338-64.535 c29.932-17.554,53.324-45.025,65.646-77.911h-136.89V208.176h138.887L507.527,208.176L507.527,208.176z"></path> <path style="fill:#28B446;" d="M416.253,455.624l0.014,0.014C372.396,490.901,316.666,512,256,512 c-97.491,0-182.252-54.491-225.491-134.681l82.961-67.91c21.619,57.698,77.278,98.771,142.53,98.771 c28.047,0,54.323-7.582,76.87-20.818L416.253,455.624z"></path> <path style="fill:#F14336;" d="M419.404,58.936l-82.933,67.896c-23.335-14.586-50.919-23.012-80.471-23.012 c-66.729,0-123.429,42.957-143.965,102.724l-83.397-68.276h-0.014C71.23,56.123,157.06,0,256,0 C318.115,0,375.068,22.126,419.404,58.936z"></path> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> <g> </g> </svg>
								</div>
							<?php } ?>
						</div>
					</div>
				</div>
				<?php

			}
		}
		?>
	</div>
	<?php
}

/**
 * Gets rating stars SVG
 *
 * @since 1.0.0
 *
 * @param integer $rating source rating.
 * @param string  $fill_color star color.
 * @param string  $empty_color empty star color.
 * @param integer $star_size star size.
 */
function premium_fb_rev_stars( $rating, $fill_color, $empty_color, $star_size, $default = '' ) {

	?>

	<span class="premium-fb-rev-stars">
	<?php

	foreach ( array( 1, 2, 3, 4, 5 ) as $val ) {
		$score = round( ( $rating - $val ), 2 );

		if ( $score >= -0.2 ) {

			?>
				<span class="premium-fb-rev-star"><svg xmlns="http://www.w3.org/2000/svg" version="1.1" width="<?php echo esc_attr( $star_size ); ?>" height="<?php echo esc_attr( $star_size ); ?>" viewBox="0 0 1792 1792"><path d="M1728 647q0 22-26 48l-363 354 86 500q1 7 1 20 0 21-10.5 35.5t-30.5 14.5q-19 0-40-12l-449-236-449 236q-22 12-40 12-21 0-31.5-14.5t-10.5-35.5q0-6 2-20l86-500-364-354q-25-27-25-48 0-37 56-46l502-73 225-455q19-41 49-41t49 41l225 455 502 73q56 9 56 46z" fill="<?php echo esc_attr( $fill_color ); ?>"></path></svg></span>
			<?php
		} elseif ( $score > -0.8 && $score < -0.2 ) {
			?>
				<span class="premium-fb-rev-star"><svg xmlns="http://www.w3.org/2000/svg" version="1.1" width="<?php echo esc_attr( $star_size ); ?>" height="<?php echo esc_attr( $star_size ); ?>" viewBox="0 0 1792 1792"><path d="M1250 957l257-250-356-52-66-10-30-60-159-322v963l59 31 318 168-60-355-12-66zm452-262l-363 354 86 500q5 33-6 51.5t-34 18.5q-17 0-40-12l-449-236-449 236q-23 12-40 12-23 0-34-18.5t-6-51.5l86-500-364-354q-32-32-23-59.5t54-34.5l502-73 225-455q20-41 49-41 28 0 49 41l225 455 502 73q45 7 54 34.5t-24 59.5z" fill="<?php echo esc_attr( $fill_color ); ?>"></path></svg></span>
		<?php } else { ?>
				<span class="premium-fb-rev-star"><svg xmlns="http://www.w3.org/2000/svg" version="1.1" width="<?php echo esc_attr( $star_size ); ?>" height="<?php echo esc_attr( $star_size ); ?>" viewBox="0 0 1792 1792"><path d="M1201 1004l306-297-422-62-189-382-189 382-422 62 306 297-73 421 378-199 377 199zm527-357q0 22-26 48l-363 354 86 500q1 7 1 20 0 50-41 50-19 0-40-12l-449-236-449 236q-22 12-40 12-21 0-31.5-14.5t-10.5-35.5q0-6 2-20l86-500-364-354q-25-27-25-48 0-37 56-46l502-73 225-455q19-41 49-41t49 41l225 455 502 73q56 9 56 46z" fill="<?php echo esc_attr( $empty_color ); ?>"></path></svg></span>
				<?php
		}
	}
	?>
	</span>

	<?php
}

/**
 * Trim review text
 *
 * @param string  $text review text.
 * @param integer $size review length.
 */
function premium_fb_rev_trim_text( $text, $size ) {

	$text = wp_strip_all_tags( $text );

	$length = count( preg_split( '/\s+/', $text ) );

	if ( 0 < $size && $length >= $size ) {

		$pieces = explode( ' ', $text );

		$text = implode( ' ', array_splice( $pieces, 0, $size ) );

		echo wp_kses_post( $text . '...' );

		return true;

	} else {

		echo wp_kses_post( $text );

	}

	return false;
}

/**
 * Get Yelp place data
 *
 * @param string $api_key API key.
 * @param string $place_id Place ID.
 */
function premium_yelp_rev_api_rating_place( $api_key, $place_id ) {

	$place_rating = rplg_urlopen( PREMIUM_YELP_API . '/' . $place_id, null, array( 'Authorization: Bearer ' . $api_key ) );

	return $place_rating;

}

/**
 * Gets Yelp Reviews API url
 *
 * @since 1.5.8
 *
 * @param string $business_id Business ID.
 * @param string $reviews_lang Language.
 */
function premium_yelp_reviews_api( $business_id, $reviews_lang = '' ) {

	$url = PREMIUM_YELP_API . '/' . $business_id . '/reviews';

	$yrw_language = strlen( $reviews_lang ) > 0 ? $reviews_lang : get_option( 'yrw_language' );

	if ( strlen( $yrw_language ) > 0 ) {

		$url = $url . '?locale=' . $yrw_language;

	}

	return $url;
}

/**
 * Gets Yelp Reviews Data
 *
 * @since 1.5.8
 *
 * @param string $api_key Yelp API.
 * @param string $place_id Place ID.
 */
function premium_yelp_reviews_data( $api_key, $place_id ) {

	$yelp_response = rplg_urlopen( premium_yelp_reviews_api( $place_id ), null, array( 'Authorization: Bearer ' . $api_key ) );

	return $yelp_response;

}

/**
 * Render Place/Reviews Layout
 *
 * @since 1.5.8
 *
 * @param array  $reviews Yelp reviews.
 * @param object $settings widget settings.
 */
function premium_yelp_rev_reviews( $reviews, $settings ) {

	$limit = $settings['limit'];

	$min_filter = $settings['filter_min'];

	$max_filter = $settings['filter_max'];

	$show_date = $settings['date'];

	$show_stars = $settings['stars'];

	$date_format = $settings['format'];

	$fill_color = $settings['fill_color'];

	$empty_color = $settings['empty_color'];

	$star_size = $settings['stars_size'];

	$rev_text = $settings['text'];

	$length = $settings['rev_length'];

	$readmore = $settings['readmore'];

	$skin_type = $settings['skin_type'];

	?>

	<div class="premium-fb-rev-reviews">
	<?php
	if ( count( $reviews ) > 0 ) {
		array_splice( $reviews, $limit );
		foreach ( $reviews as $review ) {

			$review->more = false;

			if ( ! isset( $review->text ) && 'yes' === $settings['hide_empty'] ) {
				continue;
			}

			if ( $review->rating < $min_filter || $review->rating > $max_filter ) {
				continue;
			}

			if ( strlen( $review->user->image_url ) > 0 ) {
				$image_url    = str_replace( '/o.', '/ms.', $review->user->image_url );
				$author_photo = '<img class="premium-fb-rev-img" src="' . esc_url( $image_url ) . '" alt="' . esc_attr( $review->user->name ) . '">';
			} else {
				$author_photo = PREMIUM_FB_REV_AVATAR;
			}
			?>

			<div class="premium-fb-rev-review-wrap">
				<div class="premium-fb-rev-review">
					<div class="premium-fb-rev-review-inner">
					<?php
					if ( 'yes' === $settings['show_image'] ) {
						if ( ( 'default' === $skin_type && 'left' !== $settings['image_display'] ) || ( 'card' === $skin_type && 'inline' === $settings['image_display'] ) ) {
							?>
									<div class="premium-fb-rev-content-left">
							<?php echo $author_photo; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
									</div>
								<?php
						}
					}
					?>
							<div class="premium-fb-rev-content-right">
							<?php

							if ( 'bubble' === $skin_type || 'left' === $settings['image_display'] ) {
								?>
									<div class="premium-fb-rev-reviewer-header">
								<?php } ?>

								<div class="premium-fb-rev-reviewer-wrapper">
									<?php
										$person_link = '<a class="premium-fb-rev-reviewer-link" href="' . $review->user->profile_url . '" target="_blank">' . $review->user->name . '</a>';
										echo wp_kses_post( $person_link );
									?>
								</div>

								<?php if ( $show_date || $show_stars ) : ?>
									<div class="premium-fb-rev-info">
										<?php if ( $show_date ) : ?>
											<div class="premium-fb-rev-time"><span class="premium-fb-rev-time-text"><?php echo wp_kses_post( date( $date_format, strtotime( $review->time_created ) ) ); ?></span></div>
										<?php endif; ?>

										<?php if ( $show_stars ) : ?>
											<div class="premium-fb-rev-stars-container">
												<?php
													echo premium_fb_rev_stars( $review->rating, $fill_color, $empty_color, $star_size );
												?>
											</div>
										<?php endif; ?>
									</div>
									<?php
								endif;
								if ( 'bubble' === $skin_type || 'left' === $settings['image_display'] ) {
									?>
									</div>
									<?php
								}
								if ( isset( $review->text ) && $rev_text ) :
									?>
									<div class="premium-fb-rev-rating">
										<div class="premium-fb-rev-text-wrapper">
											<span class="premium-fb-rev-text reviews"><?php $review->more = premium_fb_rev_trim_text( $review->text, $length ); ?></span>
											<?php
											if ( $review->more && isset( $review->url ) ) :
												$url = $review->url;
												?>
												<a class="premium-fb-rev-readmore" href="<?php echo esc_attr( $url ); ?>" target="_blank" rel="noopener noreferrer"><?php echo wp_kses_post( $readmore ); ?></a>
											<?php endif; ?>
										</div>

										<?php if ( 'bubble' === $skin_type && $settings['bubble_arrow'] ) { ?>
											<div class="premium-rev-arrow-bubble">
												<div class="premium-rev-arrow-bubble-border"></div>
												<div class="premium-rev-arrow"></div>
											</div>
										<?php } ?>
										</div>
										<?php endif; ?>

										<?php
										if ( $settings['show_image'] ) {
											if ( ( 'card' === $skin_type && 'inline' !== $settings['image_display'] ) || 'bubble' === $skin_type || 'left' === $settings['image_display'] ) {
												?>
										<div class="premium-fb-rev-content-left">
												<?php echo $author_photo; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
										</div>
												<?php
											}
										}
										?>
								</div>

									<?php if ( 'yes' === $settings['show_icon'] ) { ?>
									<div class="premium-fb-rev-icon">
										<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1016.09 1333.33" shape-rendering="geometricPrecision" text-rendering="geometricPrecision" image-rendering="optimizeQuality" fill-rule="evenodd" clip-rule="evenodd"><path d="M25.87 641.95C4.22 676.65-4.93 785.94 2.59 858.47c2.65 23.94 6.98 43.91 13.29 55.81 8.66 16.48 23.22 26.28 39.81 26.88 10.64.54 17.26-1.26 217.43-65.62 0 0 88.96-28.39 89.31-28.57 22.19-5.65 37.11-26.05 38.56-52.09 1.44-26.7-12.33-50.28-35.07-58.82 0 0-62.73-25.56-62.85-25.56-215.08-88.71-224.76-92.2-235.59-92.32-16.59-.67-31.33 7.7-41.62 23.76zM515.4 545.6c-3.91-90.1-31.04-491.27-34.22-509.86-4.57-16.84-17.74-28.87-36.63-33.62-58.04-14.37-279.86 47.76-320.94 90.16-13.23 13.78-18.1 30.74-14.14 45.78 6.5 13.29 281.3 445.68 281.3 445.68 40.6 65.86 73.74 55.63 84.63 52.2 10.76-3.31 43.72-13.54 40-90.34zm228.19 187.72c227.35-55.1 236.13-57.98 245.09-63.88 13.78-9.26 20.69-24.78 19.49-43.67 0-.6.12-1.27 0-1.93-5.84-55.81-103.63-201.01-151.81-224.58-17.08-8.19-34.16-7.64-48.35 1.86-8.78 5.71-15.22 14.38-136.95 180.86 0 0-54.97 74.88-55.63 75.6-14.49 17.62-14.73 42.88-.54 64.54 14.68 22.44 39.46 33.38 62.19 27.07 0 0-.91 1.62-1.15 1.93 11.19-4.21 31.22-9.15 67.66-17.8zm103.39 496.44c50.52-20.15 160.71-160.35 168.47-214.3 2.7-18.77-3.19-34.94-16.12-45.29-8.48-6.37-14.92-8.84-214.96-74.52 0 0-87.75-28.99-88.9-29.53-21.23-8.24-45.47-.61-61.77 19.48-16.96 20.63-19.49 47.88-5.96 68.45l35.31 57.5c118.73 192.83 127.81 206.48 136.35 213.16 13.23 10.4 30.07 12.09 47.57 5.05zm-339.94 73.2c3.49-10.11 3.91-17.02 4.51-227.3 0 0 .48-92.93.54-93.83 1.44-22.8-13.29-43.54-37.41-52.81-24.84-9.56-51.6-3.67-66.64 15.04 0 0-43.9 52.09-44.03 52.09-150.66 177.01-156.97 185.19-160.65 195.65-2.23 6.13-3.13 12.75-2.41 19.31.91 9.38 5.17 18.64 12.21 27.3 34.95 41.5 202.51 103.15 256.04 94.01 18.58-3.37 32.12-13.83 37.83-29.47z" fill="#bf2519" fill-rule="nonzero"></path></svg>
									</div>
								<?php } ?>

							</div>
						</div>
					</div>
					<?php

		}
	}
	?>
	</div>
	<?php
}
