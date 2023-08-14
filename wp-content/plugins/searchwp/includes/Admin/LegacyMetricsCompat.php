<?php

namespace SearchWP\Admin;

use SearchWP\Settings;
use SearchWP\Statistics;
use SearchWP\Utils;

/**
 * Legacy version of Metrics extension compatibility class.
 * This class has to be removed once Metrics v1.4.2 is released.
 *
 * @since 4.2.0
 */
class LegacyMetricsCompat {

	/**
	 * Run LegacyMetricsCompat hooks.
	 *
	 * @since 4.2.0
     *
     * @return void
	 */
	public static function hooks() {

		if ( ! defined( 'SEARCHWP_METRICS_VERSION' ) ) {
			return;
		}

		// Run for the legacy version of Metrics extension only.
		if ( version_compare( SEARCHWP_METRICS_VERSION, '1.4.1', '>' ) ) {
			return;
		}

		$user_can = current_user_can( Settings::get_capability() ) ||
		            current_user_can( Statistics::get_capability() );

		if ( ! $user_can ) {
			return;
		}

		// Standalone dashboard page and main SearchWP menu page hooks.
		add_action( 'admin_enqueue_scripts', [ __CLASS__, 'load_legacy_assets' ] );
		add_action( 'searchwp\settings\nav\before', [ __CLASS__, 'remove_nav_tab' ] );
		add_action( 'searchwp\options\submenu_pages', [ __CLASS__, 'modify_submenu_items' ] );

		if ( current_user_can( Settings::get_capability() ) ) {
			// Main SearchWP menu page hooks.
			add_action( 'admin_menu', [ __CLASS__, 'redirect_metrics' ], 910 );
			add_action( 'wp_before_admin_bar_render', [ __CLASS__, 'change_admin_bar_menu_metrics_url' ], 110 );
		} else {
			// Standalone dashboard page hooks.
			add_action( 'searchwp\settings\page\title', [ __CLASS__, 'page_title' ] );
		}
	}

	/**
     * Enqueue legacy assets from a legacy version of Metrics extension.
     *
	 * @param string $hook_suffix The current admin page.
     *
     * @since 4.2.0
	 *
	 * @return void
	 */
    public static function load_legacy_assets( $hook_suffix ) {

        if ( $hook_suffix === 'searchwp_page_searchwp-metrics' ) {
	        do_action( 'admin_enqueue_scripts', 'dashboard_page_searchwp-metrics' );
        }
    }

	/**
     * Remove a NavTab registered by the legacy version of Metrics extension.
     *
     * @since 4.2.0
     *
	 * @return void
	 */
    public static function remove_nav_tab() {

	    global $wp_filter;

		if ( ! isset( $wp_filter['searchwp\settings\nav\statistics']->callbacks[10] ) ) {
			return;
		}

		// Since we don't have an instance of SearchWP_Metrics stored, we need to iterate through filters.
		foreach ( $wp_filter['searchwp\settings\nav\statistics']->callbacks[10] as $key => $callback ) {
			if ( ! isset( $callback['function'][0], $callback['function'][1] ) ) {
				continue;
			}
			if ( $callback['function'][1] !== 'settings_nav_tab' ) {
				continue;
			}
			if ( ! ( $callback['function'][0] instanceof \SearchWP_Metrics ) ) {
				continue;
			}
			unset( $wp_filter['searchwp\settings\nav\statistics']->callbacks[10][ $key ] );

			break;
		}
    }

	/**
	 * Change Statistics to Metrics in the main menu.
	 *
	 * @since 4.2.0
     *
	 * @param array $submenu_pages Submenu pages config.
	 *
	 * @return array
	 */
    public static function modify_submenu_items( $submenu_pages ) {

	    if ( ! isset( $submenu_pages['statistics'] ) ) {
		    return $submenu_pages;
	    }

	    $submenu_pages['statistics']['menu_title'] = esc_html__( 'Metrics', 'searchwp' );
	    $submenu_pages['statistics']['menu_slug']  = 'searchwp-metrics';

	    $submenu_pages['statistics']['function'] = function () {
			echo '<div id="searchwp-metrics"></div>';
		};

	    if ( Utils::is_swp_admin_page( 'metrics' ) ) {
		    new NavTab(
			    [
				    'page'       => 'metrics',
				    'tab'        => 'metrics',
				    'label'      => __( 'Metrics', 'searchwp' ),
				    'is_default' => true,
			    ]
		    );
	    }

	    return $submenu_pages;
    }

	/**
	 * Redirect Metrics dashboard menu item to the SearchWP main menu.
	 *
	 * @since 4.2.0
	 *
	 * @return void
	 */
	public static function redirect_metrics() {

		global $submenu;

		if ( ! is_array( $submenu ) || ! array_key_exists( 'index.php', $submenu ) ) {
			return;
		}

		// Override the link for the Metrics dashboard menu item.
		foreach ( $submenu['index.php'] as $index => $dashboard_submenu ) {
			if ( $dashboard_submenu[2] !== 'searchwp-metrics' ) {
				continue;
			}

			$submenu['index.php'][ $index ][2] = esc_url_raw(
				add_query_arg( [ 'page' => 'searchwp-metrics' ], admin_url( 'admin.php' ) )
			);

			break;
		}
	}

	/**
	 * Change Metrics URL in the admin bar menu.
	 *
	 * @since 4.2.0
	 *
	 * @return void
	 */
	public static function change_admin_bar_menu_metrics_url() {

		global $wp_admin_bar;

		$menu_item = $wp_admin_bar->get_node( 'searchwp_metrics' );

		if ( ! isset( $menu_item->href ) ) {
			return;
		}

		$menu_item->href = esc_url( add_query_arg( [ 'page' => 'searchwp-metrics' ], admin_url( 'admin.php' ) ) );

		$wp_admin_bar->add_node( $menu_item );
	}

	/**
	 * Metrics main page title.
	 *
	 * @since 4.2.2
	 */
	public static function page_title() {
		?>
		<h1 class="page-title">
			<?php esc_html_e( 'SearchWP Metrics', 'searchwp' ); ?>
		</h1>
		<style>
			.searchwp-metrics__title h1 {
				display: none;
			}
		</style>
		<?php
	}
}
