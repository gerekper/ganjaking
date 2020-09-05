<?php

namespace wpbuddy\rich_snippets;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly


/**
 * Class Admin_Plugins.
 *
 * Does things on the plugins screen.
 *
 * @package wpbuddy\rich_snippets
 *
 * @since   2.13.1
 */
final class Admin_Plugins_Controller {

	/**
	 * Magic method for setting upt the class.
	 *
	 * Disallow external instances.
	 *
	 * @since 2.13.1
	 */
	public function __construct() {
		add_filter( 'plugin_action_links_' . plugin_basename( rich_snippets()->get_plugin_file() ),
			[
				$this,
				'action_links'
			]
		);

		add_action( 'admin_enqueue_scripts', [ $this, 'scripts' ] );

		add_action(
			'after_plugin_row_' . plugin_basename( rich_snippets()->get_plugin_file() ),
			[ $this, 'after_plugin_row' ],
			10,
			3
		);

		add_action(
			'in_plugin_update_message-' . plugin_basename( rich_snippets()->get_plugin_file() ),
			[ $this, 'force_check_link' ],
			10,
			2
		);
	}


	/**
	 * Modifies the plugin action links.
	 *
	 * @param array $actions
	 *
	 * @return array
	 * @since 2.13.1
	 *
	 */
	public function action_links( $actions ) {

		$actions['uninstall'] = sprintf(
			'<a href="%1$s" aria-label="%2$s">%3$s</a>',
			esc_url( add_query_arg( [
				'page'     => 'rich-snippets-uninstall',
				'_wpnonce' => wp_create_nonce( 'rich-snippets-uninstall' )
			], admin_url( 'index.php' ) ) ),
			__( 'Uninstall the SNIP plugin', 'rich-snippets-schema' ),
			__( 'Uninstall', 'rich-snippets-schema' )
		);

		return $actions;
	}


	/**
	 * Enqueues plugin page styles and scripts.
	 *
	 * @since 2.13.1
	 */
	public function scripts() {
		wp_register_style(
			'wpb-rs-admin-plugins',
			plugins_url( 'css/admin-plugins.css', rich_snippets()->get_plugin_file() ),
			[],
			filemtime( plugin_dir_path( rich_snippets()->get_plugin_file() ) . 'css/admin-plugins.css' )
		);

		wp_enqueue_style( 'wpb-rs-admin-plugins' );
	}

	/**
	 * Prints information about updates after the plugins row.
	 *
	 * @since 2.17.12
	 */
	public function after_plugin_row( $plugin_file, $plugin_data, $status ) {

		if ( array_key_exists( 'upgrade_notice', $plugin_data ) && ! empty( $plugin_data['upgrade_notice'] ) ) {
			?>
            <tr class="plugin-update-tr active" id="rich-snippets-wordpress-plugin-update-information">
                <td colspan="3" class="plugin-update colspanchange">
                    <div class="notice inline notice-warning notice-alt">
                        <p>
							<?php
							printf(
								__( 'An update may fail due to the following error: %1$s - <a target="_blank" href="%2$s">Read here what you can do about it</a>.', 'rich-snippets-schema' ),
								make_clickable( esc_html( $plugin_data['upgrade_notice'] ) ),
								Helper_Model::instance()->get_campaignify( 'https://rich-snippets.io/automatic-updates/', 'upgrade-notice' )
							);
							?>
                        </p>
                    </div>
                </td>
            </tr>
			<?php
		};
	}

	/**
	 * Adds a force-check link.
	 *
	 * @param $plugin_data
	 * @param $response
	 *
	 * @since 2.17.12
	 */
	public function force_check_link( $plugin_data, $response ) {
		if ( ! empty( $response->package ) ) {
			return;
		}

		printf(
			' <a href="%s">%s</a>',
			admin_url( 'update-core.php?force-check=1' ),
			__( 'Perform a force-check now.', 'rich-snippets-schema' )
		);
	}
}
