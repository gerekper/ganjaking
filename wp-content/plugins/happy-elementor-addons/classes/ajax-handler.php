<?php

namespace Happy_Addons\Elementor;

defined('ABSPATH') || die();

class Ajax_Handler {

	public static function init() {

		add_action( 'wp_ajax_ha_twitter_feed_action', [ __CLASS__, 'twitter_feed_ajax' ] );
		add_action( 'wp_ajax_nopriv_ha_twitter_feed_action', [ __CLASS__, 'twitter_feed_ajax' ] );

		add_action( 'wp_ajax_ha_post_tab_action', [ __CLASS__, 'post_tab' ] );
		add_action( 'wp_ajax_nopriv_ha_post_tab_action', [ __CLASS__, 'post_tab' ] );

		add_action( 'wp_ajax_ha_mailchimp_ajax', [__CLASS__, 'mailchimp_prepare_ajax'] );
		add_action( 'wp_ajax_nopriv_ha_mailchimp_ajax', [__CLASS__, 'mailchimp_prepare_ajax'] );

	}

	/**
	* Twitter Feed Ajax call
	*/
	public static function twitter_feed_ajax() {

		$security = check_ajax_referer( 'happy_addons_nonce', 'security' );

		if ( true == $security && isset( $_POST['query_settings'] ) ) :
			$settings    = ha_sanitize_array_recursively($_POST['query_settings']);
			$loaded_item = absint($_POST['loaded_item']);

			$user_name      = trim( $settings['user_name'] );
			$ha_tweets_cash = '_' . $settings['id'] . '_tweet_cash';

			$transient_key = $user_name . $ha_tweets_cash;
			$twitter_data  = get_transient( $transient_key );
			$credentials   = $settings['credentials'];

			$auth_response = wp_remote_post(
				'https://api.twitter.com/oauth2/token',
				[
					'method'      => 'POST',
					'httpversion' => '1.1',
					'blocking'    => true,
					'headers'     => [
						'Authorization' => 'Basic ' . $credentials,
						'Content-Type'  => 'application/x-www-form-urlencoded;charset=UTF-8',
					],
					'body'        => ['grant_type' => 'client_credentials'],
				]
			);

			$body = json_decode( wp_remote_retrieve_body( $auth_response ) );

			if ( ! empty( $body ) ) {
				$token           = $body->access_token;
				$tweets_response = wp_remote_get(
					'https://api.twitter.com/1.1/statuses/user_timeline.json?screen_name=' . $settings['user_name'] . '&count=999&tweet_mode=extended',
					[
						'httpversion' => '1.1',
						'blocking'    => true,
						'headers'     => ['Authorization' => "Bearer $token"],
					]
				);

				if ( ! is_wp_error( $tweets_response ) ) {
					$twitter_data = json_decode( wp_remote_retrieve_body( $tweets_response ), true );
					set_transient( $transient_key, $twitter_data, 0 );
				}
			}
			if ( 'yes' == $settings['remove_cache'] ) {
				delete_transient( $transient_key );
			}

			switch ( $settings['sort_by'] ) {
				case 'old-posts':
					usort(
						$twitter_data,
						function ( $a, $b ) {
							if ( $a['created_at'] == $b['created_at'] ) {
								return 0;
							}

							return ( $a['created_at'] < $b['created_at'] ) ? -1 : 1;
						}
					);
					break;
				case 'favorite_count':
					usort(
						$twitter_data,
						function ( $a, $b ) {
							if ( $a['favorite_count'] == $b['favorite_count'] ) {
								return 0;
							}

							return ( $a['favorite_count'] > $b['favorite_count'] ) ? -1 : 1;
						}
					);
					break;
				case 'retweet_count':
					usort(
						$twitter_data,
						function ( $a, $b ) {
							if ( $a['retweet_count'] == $b['retweet_count'] ) {
								return 0;
							}

							return ( $a['retweet_count'] > $b['retweet_count'] ) ? -1 : 1;
						}
					);
					break;
				default:
					$twitter_data;
			}

			$items = array_splice( $twitter_data, $loaded_item, $settings['tweets_limit'] );

			foreach ( $items as $item ) :
				if ( ! empty( $item['entities']['urls'] ) ) {
					$content = str_replace( $item['entities']['urls'][0]['url'], '', $item['full_text'] );
				} else {
					$content = $item['full_text'];
				}

				$description = explode( ' ', $content );
				if ( ! empty( $settings['content_word_count'] ) && count( $description ) > $settings['content_word_count'] ) {
					$description_shorten = array_slice( $description, 0, $settings['content_word_count'] );
					$description         = implode( ' ', $description_shorten ) . '...';
				} else {
					$description = $content;
				}
				?>
				<div class="ha-tweet-item">

					<?php if ( 'yes' == $settings['show_twitter_logo'] ) : ?>
						<div class="ha-tweeter-feed-icon">
							<i class="fa fa-twitter"></i>
						</div>
					<?php endif; ?>

					<div class="ha-tweet-inner-wrapper">

						<div class="ha-tweet-author">
							<?php if ( 'yes' == $settings['show_user_image'] ) : ?>
								<a href="<?php echo esc_url( 'https://twitter.com/' . $user_name ); ?>">
									<img src="<?php echo esc_url( $item['user']['profile_image_url_https'] ); ?>" alt="<?php echo esc_attr( $item['user']['name'] ); ?>" class="ha-tweet-avatar">
								</a>
							<?php endif; ?>

							<div class="ha-tweet-user">
								<?php if ( 'yes' == $settings['show_name'] ) : ?>
									<a href="<?php echo esc_url( 'https://twitter.com/' . $user_name ); ?>" class="ha-tweet-author-name">
										<?php echo esc_html( $item['user']['name'] ); ?>
									</a>
								<?php endif; ?>

								<?php if ( 'yes' == $settings['show_user_name'] ) : ?>
									<a href="<?php echo esc_url( 'https://twitter.com/' . $user_name ); ?>" class="ha-tweet-username">
										<?php echo esc_html( $settings['user_name'] ); ?>
									</a>
								<?php endif; ?>
							</div>
						</div>

						<div class="ha-tweet-content">
							<p>
								<?php echo esc_html( $description ); ?>

								<?php if ( 'yes' == $settings['read_more'] ) : ?>
									<a href="<?php echo esc_url( '//twitter.com/' . $item['user']['screen_name'] . '/status/' . $item['id'] ); ?>" target="_blank">
										<?php echo esc_html( $settings['read_more_text'] ); ?>
									</a>
								<?php endif; ?>
							</p>

							<?php if ( 'yes' == $settings['show_date'] ) : ?>
								<div class="ha-tweet-date">
									<?php echo esc_html( date( 'M d Y', strtotime( $item['created_at'] ) ) ); ?>
								</div>
							<?php endif; ?>
						</div>

					</div>

					<?php if ( 'yes' == $settings['show_favorite'] || 'yes' == $settings['show_retweet'] ) : ?>
						<div class="ha-tweet-footer-wrapper">
							<div class="ha-tweet-footer">

								<?php if ( 'yes' == $settings['show_favorite'] ) : ?>
									<div class="ha-tweet-favorite">
										<?php echo esc_html( $item['favorite_count'] ); ?>
										<i class="fa fa-heart-o"></i>
									</div>
								<?php endif; ?>

								<?php if ( 'yes' == $settings['show_retweet'] ) : ?>
									<div class="ha-tweet-retweet">
										<?php echo esc_html( $item['retweet_count'] ); ?>
										<i class="fa fa-retweet"></i>
									</div>
								<?php endif; ?>

							</div>
						</div>
					<?php endif; ?>

				</div>
				<?php
			endforeach;
		endif;
		wp_die();
	}

	/**
	 * Post Tab Ajax call
	 */
	public static function post_tab() {

		$security = check_ajax_referer( 'happy_addons_nonce', 'security' );

		if ( true == $security ) :
			$settings   = ha_sanitize_array_recursively($_POST['post_tab_query']);
			$post_type  = $settings['post_type'];
			$taxonomy   = $settings['taxonomy'];
			$item_limit = $settings['item_limit'];
			$excerpt    = $settings['excerpt'];
			$title_tag  = $settings['title_tag'];
			$term_id    = absint($_POST['term_id']);
			$orderby    = $settings['orderby'];
			$order      = $settings['order'];

			$args = [
				'post_status'      => 'publish',
				'post_type'        => $post_type,
				'posts_per_page'   => $item_limit,
				'orderby'          => $orderby,
				'order'            => $order,
				'suppress_filters' => false,
				'tax_query'        => [
					[
						'taxonomy' => $taxonomy,
						'field'    => 'term_id',
						'terms'    => $term_id,
					],
				],
			];

			$posts = get_posts( $args );

			if ( count( $posts ) !== 0 ) :
				?>
				<div class="ha-post-tab-item-wrapper active" data-term="<?php echo esc_attr( $term_id ); ?>">
					<?php foreach ( $posts as $post ) : ?>
						<div class="ha-post-tab-item">
							<div class="ha-post-tab-item-inner">
								<?php if ( has_post_thumbnail( $post->ID ) ) : ?>
									<a href="<?php echo esc_url( get_the_permalink( $post->ID ) ); ?>" class="ha-post-tab-thumb">
										<?php echo get_the_post_thumbnail( $post->ID, 'full' ); ?>
									</a>
								<?php endif; ?>
								<?php
									printf(
										'<%1$s class="ha-post-tab-title"><a href="%2$s">%3$s</a></%1$s>',
										ha_escape_tags( $title_tag, 'h2' ),
										esc_url( get_the_permalink( $post->ID ) ),
										esc_html( $post->post_title )
									);
								?>
								<?php if ( ( 'yes' == $settings['show_user_meta'] ) || ( 'yes' == $settings['show_date_meta'] ) ) : ?>
									<div class="ha-post-tab-meta">
										<?php if ( 'yes' == $settings['show_user_meta'] ) : ?>
											<span class="ha-post-tab-meta-author">
												<i class="fa fa-user-o"></i>
												<a href="<?php echo esc_url( get_author_posts_url( $post->post_author ) ); ?>"><?php echo esc_html( get_the_author_meta( 'display_name', $post->post_author ) ); ?></a>
											</span>
										<?php endif; ?>
										<?php if ( 'yes' == $settings['show_date_meta'] ) : ?>
											<?php
											$archive_year  = get_the_time( 'Y', $post->ID );
											$archive_month = get_the_time( 'm', $post->ID );
											$archive_day   = get_the_time( 'd', $post->ID );
											?>
											<span class="ha-post-tab-meta-date">
												<i class="fa fa-calendar-o"></i>
												<a href="<?php echo esc_url( get_day_link( $archive_year, $archive_month, $archive_day ) ); ?>"><?php echo get_the_date( get_option( 'date_format' ), $post->ID ); ?></a>
											</span>
										<?php endif; ?>
									</div>
								<?php endif; ?>
								<?php if ( 'yes' === $excerpt && ! empty( $post->post_excerpt ) ) : ?>
									<div class="ha-post-tab-excerpt">
										<p><?php echo esc_html( $post->post_excerpt ); ?></p>
									</div>
								<?php endif; ?>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
				<?php

			endif;
		endif;
		wp_die();
	}

	/**
	 * Mailchimp subscriber handler Ajax call
	 */
	public static function mailchimp_prepare_ajax() {

		$security = check_ajax_referer( 'happy_addons_nonce', 'security' );

		if ( ! $security ) {
			return;
		}

		parse_str( isset( $_POST['subscriber_info'] ) ? ha_sanitize_array_recursively($_POST['subscriber_info']) : '', $subsciber );

		if ( ! class_exists( 'Happy_Addons\Elementor\Widget\Mailchimp\Mailchimp_Api' ) ) {
			include_once HAPPY_ADDONS_DIR_PATH . 'widgets/mailchimp/mailchimp-api.php';
		}

		$response = Widget\Mailchimp\Mailchimp_Api::insert_subscriber_to_mailchimp( $subsciber );

		echo wp_send_json( $response );

		wp_die();
	}
}

Ajax_Handler::init();
