<?php

namespace Happy_Addons\Elementor;

defined( 'ABSPATH' ) || die();

class Dashboard_Widgets {

	private static $instance;

	public function init() {
		add_action( 'wp_dashboard_setup', [$this, 'add_dashboard_widgets'], 9999 );
	}

	/**
	 * Add a widget to the dashboard.
	 *
	 * This function is hooked into the 'wp_dashboard_setup' action below.
	 */
	public function add_dashboard_widgets() {
		wp_add_dashboard_widget(
			'happy_addons_news_update',
			esc_html__( 'HappyAddons News & Updates', 'happy-elementor-addons' ),
			[$this, 'happy_addons_news_update_function']
		);

		// Globalize the metaboxes array, this holds all the widgets for wp-admin.
		global $wp_meta_boxes;

		// Get the regular dashboard widgets array
		// (which already has our new widget but appended at the end).
		$existing_dwidgets = $wp_meta_boxes['dashboard']['normal']['core'];

		// Backup and delete our new dashboard widget from the end of the array.
		$happy_dashboard_widget = ['happy_addons_news_update' => $existing_dwidgets['happy_addons_news_update']];

		// Merge the two arrays together so our widget is at the beginning.
		$sorted_dashboard = array_merge( $happy_dashboard_widget, $existing_dwidgets );

		// Save the sorted array back into the original metaboxes.
		$wp_meta_boxes['dashboard']['normal']['core'] = $sorted_dashboard;
	}

	/**
	 * Create the function to output the content of our Dashboard Widget.
	 */
	public function happy_addons_news_update_function() {
		$promotion_banner = $this->get_banner_info();

		include_once ABSPATH . WPINC . '/feed.php';

		$rss = fetch_feed( 'https://happyaddons.com/feed/' );

		if ( ! is_wp_error( $rss ) ) :

			$maxitems = $rss->get_item_quantity( 5 );

			$rss_items = $rss->get_items( 0, $maxitems );

		endif;

		$shouldSetup = get_option( 'happy-elementor-addons_wizard_cache_key', '' );
		?>
		<div class="ha-dashboard-widget">
			<?php if ( $shouldSetup ) : ?>
			<div class="ha-setup--wrap">
				<div class="setup-wizard-finish-setup">
					<div class="description">
						<div>
							<h4>Please complete the setup wizard!</h4>
							<p>You're not done yet with the setup wizard. Please finish the wizard to get the maximum out of HappyAddons.</p>
							<a href="admin.php?page=happy-addons-setup-wizard" class="button button-primary">Resume Setup</a>
						</div>
						<img src="<?php echo HAPPY_ADDONS_ASSETS; ?>/imgs/admin/complete_steps.svg">
					</div>
					<div class="clear"></div>
				</div>
			</div>
			<?php endif; ?>
			<div class="ha-overview__feed">
				<?php if ( ! empty( $promotion_banner['image_id'] ) ) : ?>
					<?php
					$banner_url = ( ( ! empty( $promotion_banner['image_click_url'] ) ) ? $promotion_banner['image_click_url'] : '' );
					if( ! empty($banner_url)) {
						$banner_url = add_query_arg(
							array(
								'utm_source' => 'userwpdashboard',
								'utm_medium' => 'bannernotice',
								'utm_campaign' => 'usertrack',
							),
							$banner_url
						);
					}
					?>
					<?php if(!empty($banner_url)) : ?>
						<a href="<?php echo esc_url( $banner_url ); ?>" target="_blank">
					<?php endif; ?>
						<img class="ha-overview--banner" src="<?php echo esc_url( $promotion_banner['image_id'] ); ?>" alt="<?php esc_attr_e( 'HappyAddons Banner', 'happy-elementor-addons' ); ?>">
					<?php if(!empty($banner_url)) : ?>
						</a>
					<?php endif; ?>
				<?php endif; ?>
				<?php if ( ! empty( $promotion_banner['promotion_text'] ) ) : ?>
					<div class="ha-instruction ha-divider-bottom"><?php echo $promotion_banner['promotion_text']; ?></div>
				<?php endif; ?>
				<ul class="ha-overview__posts">
					<?php if ( $maxitems == 0 ) : ?>
						<li class="ha-overview__post"><?php _e( 'No items', 'happy-elementor-addons' ); ?></li>
					<?php else : ?>
						<?php foreach ( $rss_items as $item ) : ?>
							<li class="ha-overview__post">
								<a href="<?php echo esc_url( $item->get_permalink() . '?utm_source=userwpdashboard&utm_medium=bannernotice&utm_campaign=usertrack' ); ?>" title="<?php printf( __( 'Posted %s', 'happy-elementor-addons' ), $item->get_date( 'j F Y | g:i a' ) ); ?>" class="ha-overview__post-link" target="_blank"><?php echo esc_html( $item->get_title() ); ?></a>
							</li>
						<?php endforeach; ?>
					<?php endif; ?>
				</ul>
			</div>
			<div class="ha-overview__footer ha-divider-top">
				<ul>
					<li class="ha-overview__blog">
						<a href="https://happyaddons.com/blog/?utm_source=userwpdashboard&utm_medium=bannernotice&utm_campaign=usertrack" target="_blank"><?php esc_html_e( 'Blog', 'happy-elementor-addons' ); ?> <span aria-hidden="true" class="dashicons dashicons-external"></span></a>
					</li>
					<li class="ha-overview__help">
						<a href="https://happyaddons.com/docs/?utm_source=userwpdashboard&utm_medium=bannernotice&utm_campaign=usertrack" target="_blank"><?php esc_html_e( 'Help', 'happy-elementor-addons' ); ?> <span aria-hidden="true" class="dashicons dashicons-external"></span></a>
					</li>
					<li class="ha-overview__go-pro">
						<a href="https://happyaddons.com/pricing/?utm_source=userwpdashboard&utm_medium=bannernotice&utm_campaign=usertrack" target="_blank"><?php esc_html_e( 'Go Pro', 'happy-elementor-addons' ); ?> <span aria-hidden="true" class="dashicons dashicons-external"></span>
						</a>
					</li>
					<li class="ha-overview__community">
						<a href="https://www.facebook.com/groups/HappyAddonsCommunity?utm_source=userwpdashboard&utm_medium=bannernotice&utm_campaign=usertrack" target="_blank"><?php esc_html_e( 'Community', 'happy-elementor-addons' ); ?> <span aria-hidden="true" class="dashicons dashicons-external"></span></a>
					</li>
					<li class="ha-overview__whats-new">
						<a href="https://happyaddons.com/whats-new-in-happyaddons/?utm_source=userwpdashboard&utm_medium=bannernotice&utm_campaign=usertrack" target="_blank"><?php esc_html_e( 'What’s New', 'happy-elementor-addons' ); ?> <span aria-hidden="true" class="dashicons dashicons-external"></span></a>
					</li>
				</ul>
			</div>
		</div>
		<?php
	}

	public function get_banner_info() {
		$domain    = 'https://happyaddons.com/';
		$end_point = 'wp-json/happy-banner/v1/banner_info';

		$request = wp_remote_get( $domain . $end_point );

		if ( is_wp_error( $request ) ) {
			return false;
		}

		$body = wp_remote_retrieve_body( $request );

		$data = json_decode( $body, true );

		return $data;
	}

	public static function instance() {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}
}

Dashboard_Widgets::instance()->init();
