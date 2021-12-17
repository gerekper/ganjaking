<?php

defined( 'ABSPATH' ) || exit;

class Redsys_WP_Dashboard {

	function __construct() {
		add_action( 'wp_dashboard_setup', array( $this, 'redsys_news_dashboard_widgets' ) );
		add_action( 'wp_dashboard_setup', array( $this, 'redsys_posts_dashboard_widgets' ) );
	}

	function redsys_news_dashboard_widgets() {
		global $wp_meta_boxes;

		wp_add_dashboard_widget( 'redsys_link_widget', __( 'Redsys Guides', 'woocommerce-redsys' ), array( $this, 'redsys_reder_links' ) );
	}

	function redsys_reder_links() {
		$rss      = fetch_feed( 'https://redsys.joseconti.com/guias/feed/' );
		$maxitems = 0;
		if ( ! is_wp_error( $rss ) ) { // Checks that the object is created correctly.

			// Figure out how many total items there are, but limit it to 5.
			$maxitems = $rss->get_item_quantity( 5 );

			// Build an array of all the items, starting with element 0 (first element).
			$rss_items = $rss->get_items( 0, $maxitems );

		}
		?>
			<ul>
		<?php if ( $maxitems == 0 ) : ?>
			<li><?php esc_html_e( 'No items', 'woocommerce-redsys' ); ?></li>
		<?php else : ?>
			<?php // Loop through each feed item and display each item as a hyperlink. ?>
			<?php foreach ( $rss_items as $item ) : ?>
				<li>
					<a href="<?php echo esc_url( $item->get_permalink() ); ?>" title="<?php printf( __( 'Posted %s', 'woocommerce-redsys' ), $item->get_date( 'j F Y | g:i a' ) ); ?>">
						<?php echo esc_html( $item->get_title() ); ?>
					</a>
				</li>
			<?php endforeach; ?>
		<?php endif; ?>
	</ul>
	<p class="community-events-footer">
			<a href="https://redsys.joseconti.com/guias/" target="_blank"><?php _e( 'Visit Guides ', 'woocommerce-redsys' ); ?><span class="screen-reader-text"><?php _e( '(opens in a new tab)', 'woocommerce-redsys' ); ?></span><span aria-hidden="true" class="dashicons dashicons-external"></span></a>
			|

			<a href="https://redsys.joseconti.com/api-woocommerce-redsys-gateway/" target="_blank"><?php _e( 'Plugin API ', 'woocommerce-redsys' ); ?><span class="screen-reader-text"><?php _e( '(opens in a new tab)', 'woocommerce-redsys' ); ?></span><span aria-hidden="true" class="dashicons dashicons-external"></span></a>
			|

			<a href="https://redsys.joseconti.com/guias/como-abrir-un-ticket-en-woocommerce-com/" target="_blank"><?php _e( 'Get Help ', 'woocommerce-redsys' ); ?><span class="screen-reader-text"><?php _e( '(opens in a new tab)', 'woocommerce-redsys' ); ?></span><span aria-hidden="true" class="dashicons dashicons-external"></span></a></p>
			<?php
	}

	function redsys_posts_dashboard_widgets() {
		global $wp_meta_boxes;

		wp_add_dashboard_widget( 'redsys_link_posts_widget', __( 'Redsys Blog', 'woocommerce-redsys' ), array( $this, 'redsys_reder_posts_links' ) );
	}

	function redsys_reder_posts_links() {
		$rss      = fetch_feed( 'https://redsys.joseconti.com/feed/' );
		$maxitems = 0;
		if ( ! is_wp_error( $rss ) ) { // Checks that the object is created correctly.

			// Figure out how many total items there are, but limit it to 5.
			$maxitems = $rss->get_item_quantity( 5 );

			// Build an array of all the items, starting with element 0 (first element).
			$rss_items = $rss->get_items( 0, $maxitems );

		}
		?>
			<ul>
		<?php if ( $maxitems == 0 ) : ?>
			<li><?php esc_html_e( 'No items', 'woocommerce-redsys' ); ?></li>
		<?php else : ?>
			<?php // Loop through each feed item and display each item as a hyperlink. ?>
			<?php foreach ( $rss_items as $item ) : ?>
				<li>
					<a href="<?php echo esc_url( $item->get_permalink() ); ?>" title="<?php printf( __( 'Posted %s', 'woocommerce-redsys' ), $item->get_date( 'j F Y | g:i a' ) ); ?>">
						<?php echo esc_html( $item->get_title() ); ?>
					</a>
				</li>
			<?php endforeach; ?>
		<?php endif; ?>
	</ul>
	<p class="community-events-footer">
		<a href="https://redsys.joseconti.com/noticias/" target="_blank"><?php esc_html_e( 'Visit blogs ', 'woocommerce-redsys' ); ?><span class="screen-reader-text"><?php esc_html_e( '(opens in a new tab)', 'woocommerce-redsys' ); ?></span><span aria-hidden="true" class="dashicons dashicons-external"></span></a>
		|

		<a href="https://redsys.joseconti.com/redsys-for-woocommerce/" target="_blank"><?php esc_html_e( 'FAQ ', 'woocommerce-redsys' ); ?><span class="screen-reader-text"><?php esc_html_e( '(opens in a new tab)', 'woocommerce-redsys' ); ?></span><span aria-hidden="true" class="dashicons dashicons-external"></span></a></p>
		<?php
	}
}
return new Redsys_WP_Dashboard();
