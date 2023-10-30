<?php
/**
 * Add extra profile fields for users in admin
 *
 * @package WooCommerce Redsys Gateway WooCommerce.com > https://woocommerce.com/products/redsys-gateway/
 * @since 13.0.0
 * @author José Conti.
 * @link https://joseconti.com
 * @license GNU General Public License v3.0
 * @license URI: http://www.gnu.org/licenses/gpl-3.0.html
 * @copyright 2013-2013 José Conti
 */

defined( 'ABSPATH' ) || exit;

/**
 * Redsys_WP_Dashboard Class.
 */
class Redsys_WP_Dashboard {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'wp_dashboard_setup', array( $this, 'redsys_news_dashboard_widgets' ) );
		add_action( 'wp_dashboard_setup', array( $this, 'redsys_posts_dashboard_widgets' ) );
	}
	/**
	 * Add the dashboard widgets.
	 */
	public function redsys_news_dashboard_widgets() {
		global $wp_meta_boxes;

		wp_add_dashboard_widget( 'redsys_link_widget', __( 'Redsys Guides', 'woocommerce-redsys' ), array( $this, 'redsys_reder_links' ) );
	}
	/**
	 * Render render links.
	 */
	public function redsys_reder_links() {
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
		<?php if ( 0 === (int) $maxitems ) : ?>
			<li><?php esc_html_e( 'No items', 'woocommerce-redsys' ); ?></li>
		<?php else : ?>
			<?php // Loop through each feed item and display each item as a hyperlink. ?>
			<?php foreach ( $rss_items as $item ) : ?>
				<li>
					<a href="<?php echo esc_url( $item->get_permalink() ); ?>" title="<?php printf( esc_html__( 'Posted %s', 'woocommerce-redsys' ), esc_html( $item->get_date( 'j F Y | g:i a' ) ) ); // phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment ?>">
						<?php echo esc_html( $item->get_title() ); ?>
					</a>
				</li>
			<?php endforeach; ?>
		<?php endif; ?>
	</ul>
	<p class="community-events-footer">
			<a href="https://redsys.joseconti.com/guias/" target="_blank"><?php esc_html_e( 'Visit Guides ', 'woocommerce-redsys' ); ?><span class="screen-reader-text"><?php esc_html_e( '(opens in a new tab)', 'woocommerce-redsys' ); ?></span><span aria-hidden="true" class="dashicons dashicons-external"></span></a>
			|

			<a href="https://redsys.joseconti.com/api-woocommerce-redsys-gateway/" target="_blank"><?php esc_html_e( 'Plugin API ', 'woocommerce-redsys' ); ?><span class="screen-reader-text"><?php esc_html_e( '(opens in a new tab)', 'woocommerce-redsys' ); ?></span><span aria-hidden="true" class="dashicons dashicons-external"></span></a>
			|

			<a href="https://redsys.joseconti.com/guias/como-abrir-un-ticket-en-woocommerce-com/" target="_blank"><?php esc_html_e( 'Get Help ', 'woocommerce-redsys' ); ?><span class="screen-reader-text"><?php esc_html_e( '(opens in a new tab)', 'woocommerce-redsys' ); ?></span><span aria-hidden="true" class="dashicons dashicons-external"></span></a></p>
			<?php
	}
	/**
	 * Add the dashboard widgets.
	 */
	public function redsys_posts_dashboard_widgets() {
		global $wp_meta_boxes;

		wp_add_dashboard_widget( 'redsys_link_posts_widget', __( 'Redsys Blog', 'woocommerce-redsys' ), array( $this, 'redsys_reder_posts_links' ) );
	}
	/**
	 * Render render post links.
	 */
	public function redsys_reder_posts_links() {
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
		<?php if ( 0 === (int) $maxitems ) : ?>
			<li><?php esc_html_e( 'No items', 'woocommerce-redsys' ); ?></li>
		<?php else : ?>
			<?php // Loop through each feed item and display each item as a hyperlink. ?>
			<?php foreach ( $rss_items as $item ) : ?>
				<li>
					<a href="<?php echo esc_url( $item->get_permalink() ); ?>" title="<?php printf( esc_html__( 'Posted %s', 'woocommerce-redsys' ), esc_html( $item->get_date( 'j F Y | g:i a' ) ) ); // phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment ?>">
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
