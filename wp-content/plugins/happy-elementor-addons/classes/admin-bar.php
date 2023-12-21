<?php
namespace Happy_Addons\Elementor;

defined( 'ABSPATH' ) || die();

class Admin_Bar {

	public static function init() {
		add_action( 'admin_bar_menu', [__CLASS__, 'add_toolbar_items'], 500 );
		add_action( 'wp_enqueue_scripts', [__CLASS__, 'enqueue_assets'] );
		add_action( 'admin_enqueue_scripts', [__CLASS__, 'enqueue_assets'] );
		add_action( 'wp_ajax_ha_clear_cache', [__CLASS__, 'clear_cache' ] );
	}

	public static function clear_cache() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		if ( ! check_ajax_referer( 'ha_clear_cache', 'nonce' ) ) {
			wp_send_json_error();
		}

		$type = isset( $_POST['type'] ) ? sanitize_text_field($_POST['type']) : '';
		$post_id = isset( $_POST['post_id'] ) ? absint($_POST['post_id']) : 0;
		$assets_cache = new Assets_Cache( $post_id );
		if ( $type === 'page' ) {
			$assets_cache->delete();
		} elseif ( $type === 'all' ) {
			$assets_cache->delete_all();
		}
		wp_send_json_success();
	}

	public static function enqueue_assets() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		wp_enqueue_style(
			'happy-elementor-addons-admin',
			HAPPY_ADDONS_ASSETS . 'admin/css/admin.min.css',
			null,
			HAPPY_ADDONS_VERSION
		);

		wp_enqueue_script(
			'happy-elementor-addons-admin',
			HAPPY_ADDONS_ASSETS . 'admin/js/admin.min.js',
			['jquery'],
			HAPPY_ADDONS_VERSION,
			true
		);
		// wp_enqueue_style('select2');

		// wp_enqueue_script( 'select2' );

		wp_enqueue_script(
			'micromodal',
			'//unpkg.com/micromodal/dist/micromodal.min.js',
			[],
			HAPPY_ADDONS_VERSION,
			true
		);

		wp_enqueue_style(
			'select2',
			'//cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css',
			null,
			HAPPY_ADDONS_VERSION
		);

		wp_enqueue_script(
			'select2',
			'//cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
			['jquery'],
			HAPPY_ADDONS_VERSION,
			true
		);

		wp_enqueue_script( 'wp-api' );
		
		wp_enqueue_script(
			'alpine',
			'//unpkg.com/alpinejs',
			[],
			HAPPY_ADDONS_VERSION,
			true
		);
		
		wp_localize_script(
			'happy-elementor-addons-admin',
			'HappyAdmin',
			[
				'nonce'    => wp_create_nonce( 'ha_clear_cache' ),
				'post_id'  => get_queried_object_id(),
				'ajax_url' => admin_url( 'admin-ajax.php' ),
			]
		);
	}

	public static function add_toolbar_items( \WP_Admin_Bar $admin_bar ) {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$icon = '<i class="dashicons dashicons-update-alt"></i> ';

		$admin_bar->add_menu( [
			'id'    => 'happy-addons',
			'title' => sprintf( '<img src="%s">', ha_get_b64_icon() ),
			'href'  => ha_get_dashboard_link(),
			'meta'  => [
				'title' => __( 'HappyAddons', 'happy-elementor-addons' ),
			]
		] );

		if ( is_singular() ) {
			$admin_bar->add_menu( [
				'id'     => 'ha-clear-page-cache',
				'parent' => 'happy-addons',
				'title'  => $icon . __( 'Page: Renew On Demand Assets', 'happy-elementor-addons' ),
				'href'   => '#',
				'meta'   => [
					'class' => 'hajs-clear-cache ha-clear-page-cache',
				]
			] );
		}

		$admin_bar->add_menu( [
			'id'     => 'ha-clear-all-cache',
			'parent' => 'happy-addons',
			'title'  => $icon . __( 'Global: Renew On Demand Assets', 'happy-elementor-addons' ),
			'href'   => '#',
			'meta'   => [
				'class' => 'hajs-clear-cache ha-clear-all-cache',
			]
		] );
	}
}

Admin_Bar::init();
