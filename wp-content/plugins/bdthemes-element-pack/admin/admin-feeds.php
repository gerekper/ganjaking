<?php

namespace ElementPack;

if (!defined('ABSPATH')) {
	exit;
} // Exit if accessed directly

/**
 * Admin_Feeds class
 */

class Element_Pack_Admin_Feeds {

	public function __construct() {
		add_action('admin_enqueue_scripts', [$this, 'enqueue_product_feeds_styles']);
		add_action('wp_dashboard_setup', [$this, 'bdthemes_element_pack_register_rss_feeds']);
	}

	/**
	 * Enqueue Admin Style Files
	 */
	function enqueue_product_feeds_styles($hook) {
		if ('index.php' != $hook) {
			return;
		}
		$direction_suffix = is_rtl() ? '.rtl' : '';
		wp_enqueue_style('ep-product-feed', BDTEP_ADMIN_URL . 'assets/css/ep-product-feed' . $direction_suffix . '.css', [], BDTEP_VER);
	}


	/**
	 * Element Pack Feeds Register
	 */

	public function bdthemes_element_pack_register_rss_feeds() {
		wp_add_dashboard_widget('bdt-ep-dashboard-overview', esc_html__('Element Pack News &amp; Updates', 'bdthemes-element-pack'), [
			$this,
			'bdthemes_element_pack_rss_feeds_content_data'
		], null, null, 'column4', 'core');
	}

	/**
	 * Element Pack dashboard overview fetch content data
	 */
	public function bdthemes_element_pack_rss_feeds_content_data() {
		echo '<div class="bdt-ep-dashboard-widget">';
		$feeds = array();
		$feeds = $this->bdthemes_element_pack_get_feeds_remote_data();
		if (is_array($feeds)) :
			foreach ($feeds as $key => $feed) {
				printf('<div class="bdt-product-feeds-content activity-block"><a href="%s" target="_blank"><img class="bdt-ep-promo-image" src="%s"></a> <p>%s</p></div>', $feed->demo_link, $feed->image, $feed->content);
			}
		endif;
		echo $this->bdthemes_element_pack_get_feeds_posts_data();
	}

	/**
	 * Element Pack dashboard overview fetch remote data
	 */
	public function bdthemes_element_pack_get_feeds_remote_data() {

		$get_transient = get_transient('bdthemes_ep_product_feeds');
		if (!empty($get_transient)) {
			$response = json_decode($get_transient);
		} else {
			$source      = wp_remote_get('https://dashboard.bdthemes.io/wp-json/bdthemes/v1/product-feed/?product_category=element-pack');
			if (is_wp_error($source)) {
				return [];
			}

			$response_raw = wp_remote_retrieve_body($source);
			$response     = json_decode($response_raw);
			set_transient('bdthemes_ep_product_feeds', $response_raw, 60 * 60 * 6);
		}


		return $response;
	}

	/**
	 * Element Pack dashboard overview fetch posts data
	 */
	public function bdthemes_element_pack_get_feeds_posts_data() {

		// Get RSS Feed(s)
		include_once(ABSPATH . WPINC . '/feed.php');
		$rss = fetch_feed('https://bdthemes.com/feed');
		if (!is_wp_error($rss)) {
			$maxitems  = $rss->get_item_quantity(5);
			$rss_items = $rss->get_items(0, $maxitems);
		} else {
			$maxitems = 0;
		}
?>
		<!-- // Display the container -->
		<div class="bdt-ep-overview__feed">
			<ul class="bdt-ep-overview__posts">
				<?php
				// Check items
				if ($maxitems == 0) {
					echo '<li class="bdt-ep-overview__post">' . __('No item', 'bdthemes-element-pack-lite') . '.</li>';
				} else {
					foreach ($rss_items as $item) :
						$feed_url = $item->get_permalink();
						$feed_title = $item->get_title();
						$feed_date = human_time_diff($item->get_date('U'), current_time('timestamp')) . ' ' . __('ago', 'bdthemes-element-pack-lite');
						$content = $item->get_content();
						$feed_content = wp_html_excerpt($content, 120) . ' [...]';
				?>
						<li class="bdt-ep-overview__post">
							<?php printf('<a class="bdt-ep-overview__post-link" href="%1$s" title="%2$s">%3$s</a>', $feed_url, $feed_date, $feed_title);
							printf('<span class="bdt-ep-overview__post-date">%1$s</span>', $feed_date);
							printf('<p class="bdt-ep-overview__post-description">%1$s</p>', $feed_content); ?>

						</li>
				<?php
					endforeach;
				}
				?>
			</ul>
			<div class="bdt-ep-overview__footer bdt-ep-divider_top">
				<ul>
					<?php
					$footer_link = [
						[
							'url'   => 'https://bdthemes.com/blog/',
							'title' => esc_html__('Blog', 'bdthemes-element-pack-lite'),
						],
						[
							'url'   => 'https://bdthemes.com/knowledge-base/',
							'title' => esc_html__('Docs', 'bdthemes-element-pack-lite'),
						],
						[
							'url'   => 'https://www.elementpack.pro/pricing/',
							'title' => esc_html__('Get Pro', 'bdthemes-element-pack-lite'),
						],
						[
							'url'   => 'https://feedback.elementpack.pro/announcements/',
							'title' => esc_html__('Changelog', 'bdthemes-element-pack-lite'),
						],
					];
					foreach ($footer_link as $key => $link) {
						printf('<li><a href="%1$s" target="_blank">%2$s<span aria-hidden="true" class="dashicons dashicons-external"></span></a></li>', $link['url'], $link['title']);
					}
					?>
				</ul>
			</div>
		</div>
		</div>
<?php
	}
}

if (!function_exists('element_pack_pro_activated')) {
	new Element_Pack_Admin_Feeds();
}

