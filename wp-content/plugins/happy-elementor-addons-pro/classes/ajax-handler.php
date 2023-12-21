<?php

namespace Happy_Addons_Pro;

use Happy_Addons_Pro\Traits\Smart_Post_List_Markup;
use Happy_Addons_Pro\Traits\Post_Grid_Markup;
use Happy_Addons_Pro\Traits\Post_Grid_Markup_New;
use WP_Query;

defined('ABSPATH') || die();

/**
 * Ajax class
 */
class Ajax_Handler {

	use Smart_Post_List_Markup;
	use Post_Grid_Markup;
	use Post_Grid_Markup_New;

	private static $instance = null;

	public static function instance() {
		if (is_null(self::$instance)) {
			self::$instance = new self();
			// self::$instance->init();
		}

		return self::$instance;
	}

	/**
	 * Initialization
	 *
	 * @return void
	 */
	public function init() {

		add_action('wp_ajax_ha_smart_post_list_action', [__CLASS__, 'smart_post_list_ajax']);
		add_action('wp_ajax_nopriv_ha_smart_post_list_action', [__CLASS__, 'smart_post_list_ajax']);

		add_action('wp_ajax_hapro_post_grid_action', [$this, 'post_grid_ajax']);
		add_action('wp_ajax_nopriv_hapro_post_grid_action', [$this, 'post_grid_ajax']);

		add_action('wp_ajax_ha_instagram_feed_action', [__CLASS__, 'instagram_feed_ajax']);
		add_action('wp_ajax_nopriv_ha_instagram_feed_action', [__CLASS__, 'instagram_feed_ajax']);

		add_action('wp_ajax_ha_facebook_feed_action', [__CLASS__, 'facebook_feed_ajax']);
		add_action('wp_ajax_nopriv_ha_facebook_feed_action', [__CLASS__, 'facebook_feed_ajax']);

		add_action('wp_ajax_ha_show_product_quick_view', [__CLASS__, 'show_product_quick_view']);
		add_action('wp_ajax_nopriv_ha_show_product_quick_view', [__CLASS__, 'show_product_quick_view']);

		add_action('wp_ajax_ha_show_edd_product_quick_view', [__CLASS__, 'edd_show_product_quick_view']);
		add_action('wp_ajax_nopriv_ha_show_edd_product_quick_view', [__CLASS__, 'edd_show_product_quick_view']);

		add_action('wp_ajax_ha_save_menuitem_settings', [__CLASS__, 'save_happy_menu_item_settings']);

		add_action('wp_ajax_ha_get_menuitem_settings', [__CLASS__, 'get_happy_menu_item_settings']);

		add_action('wp_ajax_ha_get_content_editor', [__CLASS__, 'get_happy_menu_content_editor']);

		add_action('wp_ajax_ha_edd_ajax_add_to_cart_link', [__CLASS__, 'ha_edd_ajax_add_to_cart_link']);
		add_action('wp_ajax_nopriv_ha_edd_ajax_add_to_cart_link', [__CLASS__, 'ha_edd_ajax_add_to_cart_link']);

		add_action('wp_ajax_ha_get_cart_subtotal_action', [$this, 'ha_get_cart_subtotal_action']);
		add_action('wp_ajax_nopriv_ha_get_cart_subtotal_action', [$this, 'ha_get_cart_subtotal_action']);

		// add_action('wp_ajax_ha_get_cart_subtotal_action', [__CLASS__, 'ha_get_cart_subtotal_action']);
		// add_action('wp_ajax_nopriv_ha_get_cart_subtotal_action', [__CLASS__, 'ha_get_cart_subtotal_action']);
	}

	public function ha_get_cart_subtotal_action() {
		$security = check_ajax_referer('happy_addons_pro_nonce', 'security');
		$result = [];

		if (true == $security && class_exists('WooCommerce')) {
			if (isset(WC()->cart) && !empty(WC()->cart)) {
				$subTotalAmount = WC()->cart->get_displayed_subtotal();
				$result = ["status" => 'true', 'subTotalAmount' => $subTotalAmount];
			} else {
				$result = ['status' => 'false'];
			}
		} else {
			$result = ['status' => 'false'];
		}

		echo json_encode($result, JSON_UNESCAPED_UNICODE);
		die();
	}

	public static function ha_edd_ajax_add_to_cart_link() {
		$security = check_ajax_referer('happy_addons_pro_nonce', 'security');

		if (true == $security && isset($_POST['download_id'])) {
			if (!function_exists('EDD')) {
				return;
			}
			$download_id = isset($_POST['download_id']) ? absint($_POST['download_id']) : '';

			edd_add_to_cart($download_id);

			wp_send_json_success();

			die();
		}
	}

	/**
	 * Smart Post List Ajax Handler
	 */
	public static function smart_post_list_ajax() {

		$security = check_ajax_referer('happy_addons_pro_nonce', 'security');

		if (true == $security && isset($_POST['querySettings'])) :

			$settings = ha_pro_sanitize_array_recursively($_POST['querySettings']);

			$list_column = $settings['list_column'];
			$class_array = [];
			if ('yes' === $settings['make_featured_post']) {
				$class_array['featured'] = 'ha-spl-column ha-spl-featured-post-wrap ' . esc_attr($settings['featured_post_column']);
				$class_array['featured_inner'] = 'ha-spl-featured-post ' . 'ha-spl-featured-' . esc_attr($settings['featured_post_style']);
			}

			$per_page = $settings['per_page'];
			$args = $settings['args'];
			$args['posts_per_page'] = $per_page;

			if (isset($_POST['offset'])) {
				$args['offset'] = intval($_POST['offset']);
			}
			if (isset($_POST['termId']) && is_numeric($_POST['termId'])) {
				$args['tax_query'] = array(
					array(
						'taxonomy' => '',
						'field' => 'term_taxonomy_id',
						'terms' => absint($_POST['termId']),
					),
				);
			}

			$args['suppress_filters'] = false;

			$posts = get_posts($args);
			$loop = 1;

			if (count($posts) !== 0) {

				self::render_spl_markup($settings, $posts, $class_array, $list_column, $per_page);
			}


		endif;
		wp_die();
	}

	/**
	 * Post Grid Ajax Handler
	 */
	public function post_grid_ajax() {

		$security = check_ajax_referer('happy_addons_pro_nonce', 'security');

		if (true == $security && isset($_POST['querySettings'])) :

			$settings = ha_pro_sanitize_array_recursively($_POST['querySettings']);
			$loaded_item = intval($_POST['loadedItem']);

			$args = $settings['args'];
			$args['offset'] = $loaded_item;
			$_query = new WP_Query($args);

			if ($_query->have_posts()) :
				while ($_query->have_posts()) : $_query->the_post();

					if (!empty($settings['_skin'])) {
						self::{'render_' . $settings['_skin'] . '_markup'}($settings, $_query);
					}

					if (!empty($settings['skin'])) {
						$this->{'new_render_' . $settings['skin'] . '_markup'}($settings, $_query);
					}

				// if( 'classic' == $settings['_skin'] ){
				// 	self::render_classic_markup( $settings, $_query );
				// }
				// elseif( 'hawai' == $settings['_skin'] ){
				// 	self::render_hawai_markup( $settings, $_query );
				// }
				// elseif( 'standard' == $settings['_skin'] ){
				// 	self::render_standard_markup( $settings, $_query );
				// }
				// elseif( 'monastic' == $settings['_skin'] ){
				// 	self::render_monastic_markup( $settings, $_query );
				// }
				// elseif( 'stylica' == $settings['_skin'] ){
				// 	self::render_stylica_markup( $settings, $_query );
				// }
				// elseif( 'outbox' == $settings['_skin'] ){
				// 	self::render_outbox_markup( $settings, $_query );
				// }
				// elseif( 'crossroad' == $settings['_skin'] ){
				// 	self::render_crossroad_markup( $settings, $_query );
				// }

				endwhile;
				wp_reset_postdata();
			else :
				wp_send_json(false);
			endif;
		endif;
		wp_die();
	}

	/**
	 * Instagram Feed Ajax
	 */
	public static function instagram_feed_ajax() {

		$security = check_ajax_referer('happy_addons_pro_nonce', 'security');

		if (true == $security && isset($_POST['query_settings'])) :

			$settings = ha_pro_sanitize_array_recursively($_POST['query_settings']);
			$loaded_item = intval($_POST['loaded_item']);
			$item_tag = 'yes' == $settings['show_link'] ? 'a' : 'div';
			$href_target = '';
			// $user_id = $settings['user_id'];
			$widget_id = $settings['widget_id'];
			$access_token = $settings['access_token'];
			$transient_key = 'happy_insta_feed_data' . $widget_id . str_replace('.', '_', $access_token);
			// $transient_key = 'happy_insta_feed_data' . str_replace('.', '_', $user_id).str_replace('.', '_', $access_token);
			$transient_key = substr($transient_key, 0, 170); //Transient Key Must be 172 characters or fewer in length.
			$instagram_data = get_transient($transient_key);
			if (false === $instagram_data) {
				$url = 'https://graph.instagram.com/me/media/?fields=caption,id,media_type,media_url,permalink,thumbnail_url,timestamp,username&limit=100&access_token=' . esc_html($access_token);
				// $url = 'https://graph.instagram.com/' . esc_html($user_id) . '/media?fields=caption,id,media_type,media_url,permalink,thumbnail_url,timestamp,username&limit=100&access_token=' . esc_html($access_token);
				$instagram_data = wp_remote_retrieve_body(wp_remote_get($url));
				set_transient($transient_key, $instagram_data, 1 * MINUTE_IN_SECONDS); //HOUR_IN_SECONDS
			}
			$instagram_data = json_decode($instagram_data, true);
			switch ($settings['sort_by']) {
				case 'old-posts':
					usort($instagram_data['data'], function ($a, $b) {
						if (strtotime($a['timestamp']) == strtotime($b['timestamp'])) return 0;
						return (strtotime($a['timestamp']) < strtotime($b['timestamp'])) ? -1 : 1;
					});
					break;
				default:
					$instagram_data['data'];
			}
			$instagram_data = array_splice($instagram_data['data'], $loaded_item, $settings['instagram_item']);
?>
			<?php if ('ha-hover-info' == $settings['view_style']) : ?>
				<?php foreach ($instagram_data as $key => $single) : ?>
					<?php if ('yes' == $settings['show_link']) {
						$href_target = 'href="' . esc_url($single['permalink']) . '" ' . 'target="' . esc_attr($settings['link_target']) . '"';
					} ?>
					<<?php echo tag_escape($item_tag) . ' class="ha-insta-item loaded" ' . $href_target; ?>>
						<?php $image_src = ($single['media_type'] == 'VIDEO') ? $single['thumbnail_url'] : $single['media_url']; ?>
						<img src="<?php echo esc_url($image_src); ?>" alt="">
						<div class="ha-insta-content">
							<?php if ('yes' == $settings['show_caption'] && !empty($single['caption'])) : ?>
								<div class="ha-insta-caption">
									<p><?php echo esc_html($single['caption']); ?></p>
								</div>
							<?php endif; ?>
						</div>
					</<?php echo tag_escape($item_tag); ?>><!-- Item wrap End-->
				<?php endforeach; ?>
			<?php endif; ?>
			<?php if ('ha-hover-push' == $settings['view_style']) : ?>
				<?php foreach ($instagram_data as $key => $single) : ?>
					<?php if ('yes' == $settings['show_link']) {
						$href_target = 'href="' . esc_url($single['permalink']) . '" ' . 'target="' . esc_attr($settings['link_target']) . '"';
					} ?>
					<<?php echo tag_escape($item_tag) . ' class="ha-insta-item loaded" ' . $href_target; ?>>
						<?php $image_src = ($single['media_type'] == 'VIDEO') ? $single['thumbnail_url'] : $single['media_url']; ?>
						<img src="<?php echo esc_url($image_src); ?>" alt="">
						<div class="ha-insta-likes-comments">
							<?php if ('yes' == $settings['show_caption'] && !empty($single['caption'])) : ?>
								<div class="ha-insta-caption">
									<p><?php echo esc_html($single['caption']); ?></p>
								</div>
							<?php endif; ?>
						</div>
					</<?php echo tag_escape($item_tag); ?>>
				<?php endforeach; ?>
			<?php endif; ?>
			<?php if ('ha-feed-view' == $settings['view_style']) : ?>
				<?php foreach ($instagram_data as $key => $single) : ?>
					<div class="ha-insta-item loaded">
						<?php if ('yes' == $settings['show_user_picture'] || 'yes' == $settings['show_username'] || 'yes' == $settings['show_user_postdate'] || 'yes' == $settings['show_user_insta_icon']) : ?>
							<div class="ha-insta-user-info">
								<?php if ('yes' == $settings['show_user_picture'] || 'yes' == $settings['show_username'] || 'yes' == $settings['show_user_postdate']) : ?>
									<a class="ha-insta-user" href="<?php echo esc_url('https://www.instagram.com/' . $single['username']); ?>" target="_blank">
										<?php if ('yes' == $settings['show_user_picture'] && !empty($settings['profile_image'])) : ?>
											<div class="ha-insta-user-profile-picture">
												<img src="<?php echo esc_url($settings['profile_image']); ?>" alt="">
											</div>
										<?php endif; ?>
										<div class="ha-insta-username-and-postdate">
											<?php if ('yes' == $settings['show_username'] && !empty($settings['user_name'])) : ?>
												<span class="ha-insta-user-name"><?php echo esc_html($settings['user_name']) ?></span>
											<?php endif; ?>
											<?php if ('yes' == $settings['show_user_postdate']) : ?>
												<span class="ha-insta-postdate"><?php echo esc_html(date("M d Y", strtotime($single['timestamp']))); ?></span>
											<?php endif; ?>
										</div>
									</a>
								<?php endif; ?>
								<?php if ('yes' == $settings['show_user_insta_icon']) : ?>
									<a class="ha-insta-feed-icon" href="<?php echo esc_url($single['permalink']); ?>" target="_blank">
										<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" id="Layer_1" x="0px" y="0px" width="32px" height="32px" viewBox="0 0 32 32" style="enable-background:new 0 0 32 32;" xml:space="preserve">
											<path d="M23,32H9c-5,0-9-4-9-9V9c0-5,4-9,9-9h14c5,0,9,4,9,9v14C32,28,28,32,23,32z M9,2C5.1,2,2,5.1,2,9v14c0,3.9,3.1,7,7,7h14  c3.9,0,7-3.1,7-7V9c0-3.9-3.1-7-7-7H9z"></path>
											<path d="M16,24.2c-4.5,0-8.2-3.7-8.2-8.2c0-4.5,3.7-8.2,8.2-8.2c4.5,0,8.2,3.7,8.2,8.2C24.2,20.5,20.5,24.2,16,24.2z M16,9.8  c-3.4,0-6.2,2.8-6.2,6.2s2.8,6.2,6.2,6.2s6.2-2.8,6.2-6.2S19.4,9.8,16,9.8z"></path>
											<circle cx="16" cy="16" r="1.9"></circle>
										</svg>
									</a>
								<?php endif; ?>
							</div>
						<?php endif; ?>
						<a class="ha-insta-image" href="<?php echo esc_url($single['permalink']); ?>" target="_blank">
							<?php $image_src = ($single['media_type'] == 'VIDEO') ? $single['thumbnail_url'] : $single['media_url']; ?>
							<img src="<?php echo esc_url($image_src); ?>" alt="">
						</a>
						<?php if (('yes' == $settings['show_caption']) && !empty($single['caption'])) : ?>
							<div class="ha-insta-content">
								<div class="ha-insta-caption">
									<p><?php echo esc_html($single['caption']); ?></p>
								</div>
							</div>
						<?php endif; ?>
					</div>
				<?php endforeach; ?>
			<?php endif; ?>
			<?php
		endif;
		wp_die();
	}

	/**
	 * Facebook Feed ajax call
	 *
	 * @return array
	 */
	public static function facebook_feed_ajax() {

		$security = check_ajax_referer('happy_addons_pro_nonce', 'security');

		if (true == $security && isset($_POST['query_settings'])) :
			$settings = ha_pro_sanitize_array_recursively($_POST['query_settings']);
			$loaded_item = intval($_POST['loaded_item']);

			$ha_facebook_feed_cash = '_' . $settings['widget_id'] . '_facebook_cash';
			$transient_key = $settings['page_id'] . $ha_facebook_feed_cash;
			$facebook_feed_data = get_transient($transient_key);

			if (false === $facebook_feed_data) {
				$url_queries = 'fields=status_type,created_time,from,message,story,full_picture,permalink_url,attachments.limit(1){type,media_type,title,description,unshimmed_url},comments.summary(total_count),reactions.summary(total_count)';
				$url = "https://graph.facebook.com/v4.0/{$settings['page_id']}/posts?{$url_queries}&access_token={$settings['access_token']}";
				$data = wp_remote_get($url);
				$facebook_feed_data = json_decode(wp_remote_retrieve_body($data), true);
				set_transient($transient_key, $facebook_feed_data, 0);
			}
			if ($settings['remove_cash'] == 'yes') {
				delete_transient($transient_key);
			}

			switch ($settings['sort_by']) {
				case 'old-posts':
					usort($facebook_feed_data['data'], function ($a, $b) {
						if (strtotime($a['created_time']) == strtotime($b['created_time'])) return 0;
						return (strtotime($a['created_time']) < strtotime($b['created_time']) ? -1 : 1);
					});
					break;
				case 'like_count':
					usort($facebook_feed_data['data'], function ($a, $b) {
						if ($a['reactions']['summary'] == $b['reactions']['summary']) return 0;
						return ($a['reactions']['summary'] > $b['reactions']['summary']) ? -1 : 1;
					});
					break;
				case 'comment_count':
					usort($facebook_feed_data['data'], function ($a, $b) {
						if ($a['comments']['summary'] == $b['comments']['summary']) return 0;
						return ($a['comments']['summary'] > $b['comments']['summary']) ? -1 : 1;
					});
					break;
				default:
					$facebook_feed_data;
			}


			$items = array_splice($facebook_feed_data['data'], $loaded_item, $settings['post_limit']);

			foreach ($items as $item) :

				$page_url = "https://facebook.com/{$item['from']['id']}";
				$avatar_url = "https://graph.facebook.com/v4.0/{{$item['from']['id']}/picture";

				$description = explode(' ', $item['message']);
				if (!empty($settings['description_word_count']) && count($description) > $settings['description_word_count']) {
					$description_shorten = array_slice($description, 0, $settings['description_word_count']);
					$description = implode(' ', $description_shorten) . '...';
				} else {
					$description = $item['message'];
				}
			?>

				<div class="ha-facebook-item">

					<?php if ($settings['show_feature_image'] == 'yes' && !empty($item['full_picture'])) : ?>
						<div class="ha-facebook-feed-feature-image">
							<a href="<?php echo esc_url($item['permalink_url']); ?>" target="_blank">
								<img src="<?php echo esc_url($item['full_picture']); ?>" alt="<?php esc_url($item['from']['name']); ?>">
							</a>
						</div>
					<?php endif ?>

					<div class="ha-facebook-inner-wrapper">

						<?php if ($settings['show_facebook_logo'] == 'yes') : ?>
							<div class="ha-facebook-feed-icon">
								<i class="fa fa-facebook-square"></i>
							</div>
						<?php endif; ?>

						<div class="ha-facebook-author">
							<?php if ($settings['show_user_image'] == 'yes') : ?>
								<a href="<?php echo esc_url($page_url); ?>">
									<img src="<?php echo esc_url($avatar_url); ?>" alt="<?php echo esc_attr($item['from']['name']); ?>" class="ha-facebook-avatar">
								</a>
							<?php endif; ?>

							<div class="ha-facebook-user">
								<?php if ($settings['show_name'] == 'yes') : ?>
									<a href="<?php echo esc_url($page_url); ?>" class="ha-facebook-author-name">
										<?php echo esc_html($item['from']['name']); ?>
									</a>
								<?php endif; ?>
							</div>
						</div>

						<div class="ha-facebook-content">
							<p>
								<?php
								echo esc_html($description);
								if ($settings['read_more'] == 'yes') :
								?>
									<a href="<?php echo esc_url($item['permalink_url']); ?>" target="_blank">
										<?php echo esc_html($settings['read_more_text']); ?>
									</a>
								<?php endif; ?>
							</p>

							<?php if ($settings['show_date'] == 'yes') : ?>
								<div class="ha-facebook-date">
									<?php echo esc_html(date("M d Y", strtotime($item['created_time']))); ?>
								</div>
							<?php endif; ?>
						</div>

					</div>

					<?php if ($settings['show_likes'] == 'yes' || $settings['show_comments'] == 'yes') : ?>
						<div class="ha-facebook-footer-wrapper">
							<div class="ha-facebook-footer">

								<div class="ha-facebook-meta">
									<?php if ($settings['show_likes'] == 'yes') : ?>
										<div class="ha-facebook-likes">
											<?php echo esc_html($item['reactions']['summary']['total_count']); ?>
											<i class="fa fa-thumbs-up"></i>
										</div>
									<?php endif; ?>

									<?php if ($settings['show_comments'] == 'yes') : ?>
										<div class="ha-facebook-comments">
											<?php echo esc_html($item['comments']['summary']['total_count']); ?>
											<i class="fa fa-comment"></i>
										</div>
									<?php endif; ?>
								</div>

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
	 * Product quick view ajax handler
	 *
	 * @return void
	 */
	public static function show_product_quick_view() {
		$nonce = !empty($_GET['nonce']) ? $_GET['nonce'] : '';
		$product_id = !empty($_GET['product_id']) ? absint($_GET['product_id']) : 0;

		if (!function_exists('wc_get_product')) {
			wp_send_json_error('Looks like you are not trying a product quick view!');
		}

		if (!wp_verify_nonce($nonce, 'ha_show_product_quick_view')) {
			wp_send_json_error('Invalid request!');
		}

		$_product = get_post($product_id);

		if (empty($_product) || get_post_type($_product) !== 'product') {
			wp_send_json_error('Incomplete request!');
		}

		global $product, $post;

		$post = $_product;

		setup_postdata($post);

		?>
		<div class="ha-pqv woocommerce">
			<div class="ha-pqv__img"><?php echo woocommerce_get_product_thumbnail('woocommerce-single'); ?></div>
			<div class="ha-pqv__content">
				<h2 class="ha-pqv__title"><?php the_title(); ?></h2>
				<div class="ha-pqv__rating"><?php woocommerce_template_loop_rating(); ?></div>
				<div class="ha-pqv__price"><?php woocommerce_template_loop_price(); ?></div>
				<div class="ha-pqv__summary"><?php woocommerce_template_single_excerpt(); ?></div>
				<div class="ha-pqv__cart">
					<?php woocommerce_template_loop_add_to_cart(); ?>
				</div>
			</div>
		</div>
	<?php

		wp_reset_postdata();

		exit;
	}
	/**
	 * @param  $args
	 * @return mixed
	 */
	public static function hide_button_prices($args) {
		$args['price'] = (bool) false;

		return $args;
	}
	/**
	 * Product quick view ajax handler
	 *
	 * @return void
	 */
	public static function edd_show_product_quick_view() {
		$nonce = !empty($_GET['nonce']) ? $_GET['nonce'] : '';
		$product_id = !empty($_GET['download_id']) ? absint($_GET['download_id']) : 0;

		if (!function_exists('EDD')) {
			wp_send_json_error('Looks like you are not trying a product quick view!');
		}

		if (!wp_verify_nonce($nonce, 'ha_show_edd_product_quick_view')) {
			wp_send_json_error('Invalid request!');
		}

		$_product = get_post($product_id);

		if (empty($_product) || get_post_type($_product) !== 'download') {
			wp_send_json_error('Incomplete request!');
		}

		global $post;

		$post = $_product;

		setup_postdata($post);
		add_filter('edd_purchase_link_defaults', [__CLASS__, 'hide_button_prices']);

	?>
		<div class="ha-pqv-edd">
			<div class="ha-pqv-edd__img">
				<?php
				echo get_the_post_thumbnail($post, 'full');
				?>
			</div>
			<div class="ha-pqv-edd__content">
				<h2 class="ha-pqv-edd__title"><?php the_title(); ?></h2>
				<div class="ha-pqv-edd__price"><?php edd_price($post->ID); ?></div>
				<div class="ha-pqv-edd__summary"><?php echo wp_trim_words(get_the_content($post), 100); ?></div>
				<div class="ha-pqv-edd__cart">
					<?php
					if (edd_has_variable_prices($post->ID)) {
						printf(
							'<a href="%s" class="button" target="_blank">%s</a>',
							esc_url(get_the_permalink($post->ID)),
							__('Select Options', 'happy-addons-pro')
						);
					} else {
						printf(
							'<a href="%s" class="button" target="_blank">%s</a>',
							esc_url(get_the_permalink($post->ID)),
							__('Buy Now', 'happy-addons-pro')
						);
					}
					?>
				</div>
			</div>
		</div>
<?php

		wp_reset_postdata();

		exit;
	}

	/**
	 * Mega Menu Ajax calls
	 */
	public static function save_happy_menu_item_settings() {
		if (!current_user_can('manage_options')) {
			return;
		}

		$menu_item_id = absint($_REQUEST['settings']['menu_id']);
		$menu_item_settings = json_encode(ha_pro_sanitize_array_recursively($_REQUEST['settings']), JSON_UNESCAPED_UNICODE);
		update_post_meta($menu_item_id, \Happy_Addons_Pro\Extension\Mega_Menu::$menuitem_settings_key, $menu_item_settings);

		echo json_encode([
			'saved' => 1,
			'message' => esc_html__('Saved', 'happy-addons-pro'),
		]);

		wp_die();
	}

	/**
	 * Mega Menu get item settings
	 */
	public static function get_happy_menu_item_settings() {
		if (!current_user_can('manage_options')) {
			return;
		}
		$menu_item_id = absint($_REQUEST['menu_id']);
		$data = get_post_meta($menu_item_id, \Happy_Addons_Pro\Extension\Mega_Menu::$menuitem_settings_key, true);

		if (empty($data)) {
			$data = array(
				'menu_id' => $menu_item_id,
				'menu_has_child' => '',
				'menu_enable' => '',
				'menu_icon' => '',
				'menu_icon_color' => '',
				'menu_badge_text' => '',
				'menu_badge_color' => '',
				'menu_badge_background' => '',
				'menu_badge_radius' => '',
				'vertical_menu_width' => '',
				'mobile_submenu_content_type' => '',
				'vertical_megamenu_position_type' => '',
				'megamenu_width_type' => '',
			);
			$data = json_encode($data);
		}

		echo $data;
		wp_die();
	}

	/**
	 * Get Menu Item Iframe URL
	 */
	public static function get_happy_menu_content_editor() {

		$content_key = absint($_REQUEST['key']);

		$builder_post_title = 'ha-megamenu-content-' . $content_key;
		// $builder_post_id = get_page_by_title($builder_post_title, OBJECT, 'ha_nav_content');

		// if (is_null($builder_post_id)) {
		// 	$defaults = array(
		// 		'post_content' => '',
		// 		'post_title' => $builder_post_title,
		// 		'post_status' => 'publish',
		// 		'post_type' => 'ha_nav_content',
		// 	);
		// 	$builder_post_id = wp_insert_post($defaults);

		// 	update_post_meta($builder_post_id, '_wp_page_template', 'elementor_canvas');
		// } else {
		// 	$builder_post_id = $builder_post_id->ID;
		// }

		$query = new WP_Query(
			array(
				'post_type'              => 'ha_nav_content',
				'title'                  => $builder_post_title,
				'post_status'            => 'all',
				'posts_per_page'         => 1,
				'no_found_rows'          => true,
				'ignore_sticky_posts'    => true,
				'update_post_term_cache' => false,
				'update_post_meta_cache' => false,
				'orderby'                => 'post_date ID',
				'order'                  => 'ASC',
			)
		);

		if (!empty($query->post)) {
			$builder_post_id = $query->post->ID;
		} else {
			$defaults = array(
				'post_content' => '',
				'post_title' => $builder_post_title,
				'post_status' => 'publish',
				'post_type' => 'ha_nav_content',
			);
			$builder_post_id = wp_insert_post($defaults);

			update_post_meta($builder_post_id, '_wp_page_template', 'elementor_canvas');
		}

		$url = get_admin_url() . 'post.php?post=' . $builder_post_id . '&action=elementor';
		echo $url;
		wp_die();
	}
}

Ajax_Handler::instance()->init();
