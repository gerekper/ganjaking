<?php

namespace WBCR\Factory_Adverts_102;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Adverts Dashboard Widget.
 *
 * Adds a widget with a banner or a list of news.
 *
 * @author        Alexander Vitkalov <nechin.va@gmail.com>
 * @since         1.0.0 Added
 * @package       factory-adverts
 * @copyright (c) 2019 Webcraftic Ltd
 */
class Dashboard_Widget extends Rest_Request {

	/**
	 * Dashboard_Widget constructor.
	 *
	 * Call parent constructor. Registration hooks.
	 *
	 * @since 1.0.0 Added
	 *
	 * @param string $plugin_name Plugin name from parameter plugin_name
	 */
	public function __construct( $plugin_name ) {
		parent::__construct( $plugin_name, 'dashboard' );

		$this->register_hooks();
	}

	/**
	 * Registration hooks.
	 *
	 * @since 1.0.0 Added
	 */
	public function register_hooks() {
		add_action( 'wp_dashboard_setup', array( $this, 'add_dashboard_widgets' ) );
	}

	/**
	 * Add the News widget to the dashboard.
	 *
	 * @since 1.0.0 Added
	 */
	public function add_dashboard_widgets() {
		$widget_id = 'wbcr-adinserter-dashboard-widget';
		wp_add_dashboard_widget(
			$widget_id,
			__( 'News', '' ),
			array( $this, 'dashboard_widget_adverts' )
		);

		/**
		 * Set dashboard widget first in order
		 *
		 * @since 1.2.3 Added
		 */
		global $wp_meta_boxes;

		$normal_core   = $wp_meta_boxes['dashboard']['normal']['core'];
		$widget_backup = array( $widget_id => $normal_core[ $widget_id ] );
		unset( $normal_core[ $widget_id ] );
		$sorted_core = array_merge( $widget_backup, $normal_core );

		$wp_meta_boxes['dashboard']['normal']['core'] = $sorted_core;
	}

	/**
	 * Create the function to output the contents of the Dashboard Widget.
	 *
	 * @since 1.0.0 Added
	 */
	public function dashboard_widget_adverts() {
		$content = $this->get_content();
		if ( ! empty( $content ) ) {
			?>
			<div class="wordpress-news hide-if-no-js">
				<?php $this->show_adverts( $content ); ?>
			</div>
			<?php
		}
	}

	/**
	 * Output advert content.
	 *
	 * @since 1.0.0 Added
	 *
	 * @param $content string
	 */
	public function show_adverts( $content ) {
		if ( ! empty( $content ) ) :
			?>
			<div class="rss-widget">
				<?php echo $content; ?>
			</div>
			<?php
		endif;
	}

}
