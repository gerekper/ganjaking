<?php

namespace wpbuddy\rich_snippets\pro;

use wpbuddy\rich_snippets\Cache_Model;
use wpbuddy\rich_snippets\Snippets_Model;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly


/**
 * Class Admin_Snippets_Controller.
 *
 * Starts up all the admin things needed to control snippets.
 *
 * @package wpbuddy\rich_snippets
 *
 * @since   2.19.0
 */
class Admin_Snippets_Controller extends \wpbuddy\rich_snippets\Admin_Snippets_Controller {
	/**
	 * Get the singleton instance.
	 *
	 * Creates a new instance of the class if it does not exists.
	 *
	 * @return Admin_Snippets_Controller
	 *
	 * @since 2.0.0
	 */
	public static function instance() {

		if ( null === self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}


	/**
	 * Init.
	 *
	 * @since 2.19.0
	 */
	public function init() {

		if ( $this->initialized ) {
			return;
		}

		parent::init();

		add_action( 'post_submitbox_misc_actions', array( self::$instance, 'submitbox_js' ) );

		add_action( 'admin_notices', array( self::$instance, 'predefined_notice' ) );

		add_filter( 'post_row_actions', array( self::$instance, 'filter_row_actions' ), 10, 2 );

		add_filter( 'parent_file', [ self::$instance, 'highlight_menu' ] );

		add_filter( 'manage_wpb-rs-global_posts_columns', [ self::$instance, 'manage_posts_columns' ] );

		add_action( 'manage_wpb-rs-global_posts_custom_column', [ self::$instance, 'print_custom_columns' ], 10, 2 );

		$this->install_predefined();
	}


	/**
	 * Initializes the fields.
	 *
	 * Prevents double-init.
	 *
	 * @since 2.0.0
	 */
	public function init_fields() {

		if ( ! $this->fields instanceof Fields_Model ) {
			$this->fields = new Fields_Model();
		}
	}


	/**
	 * Installs predefined global snippets.
	 *
	 * @since 2.0.0
	 */
	public function install_predefined() {

		$should_install = boolval( filter_input( INPUT_GET, 'install_predefined', FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE ) );

		if ( ! $should_install ) {
			return;
		}

		if ( false === check_admin_referer( 'wpbrs_install_predefined' ) ) {
			wp_die( __( 'It seems that you\'re not allowed to do this.', 'rich-snippets-schema' ) );
		}

		$methods = get_class_methods( '\wpbuddy\rich_snippets\pro\Predefined_Model' );

		foreach ( $methods as $method ) {
			$v = call_user_func( array( '\wpbuddy\rich_snippets\pro\Predefined_Model', $method ) );

			if ( ! is_array( $v ) ) {
				continue;
			}

			$snippet = json_decode( $v['json'], true );

			if ( ! is_array( $snippet ) ) {
				continue;
			}

			if ( array_key_exists( 'id', $snippet ) ) {
				$post_id = Helper_Model::instance()->get_post_id_by_snippet_uid( $snippet['id'] );
			} else {
				$post_id = 0;
			}

			$post_id = wp_insert_post( array(
				'ID'          => $post_id, # this will update existing posts
				'post_title'  => ! isset( $v['title'] ) ? array_values( $v['schema'] )[0]->type : $v['title'],
				'post_status' => ! isset( $v['status'] ) ? 'publish' : $v['status'],
				'post_type'   => 'wpb-rs-global',
			), true );

			if ( is_wp_error( $post_id ) ) {
				continue;
			}

			if ( isset( $snippet['@ruleset'] ) ) {
				$rules_array = $snippet['@ruleset'];
				unset( $snippet['@ruleset'] );
			} else {
				$rules_array = [];
			}

			# the snippet
			$snippet = Snippets_Model::convert_from_json( $snippet );
			Snippets_Model::update_snippets( $post_id, [ $snippet ] );

			if ( ! is_array( $rules_array ) ) {
				continue;
			}

			$rules_array = array_filter( $rules_array );

			if ( count( $rules_array ) <= 0 ) {
				continue;
			}

			$ruleset = Rules_Model::convert_to_ruleset( $rules_array );
			Rules_Model::update_ruleset( $post_id, $ruleset );

		}

		Cache_Model::clear_global_snippets_ids();

		update_option( 'wpb_rs/predefined/message/hidden', true, false );

		wp_redirect( admin_url( 'edit.php?post_type=wpb-rs-global' ) );
	}


	/**
	 * Adds JS to the submitbox to remove some fields.
	 *
	 * @since 2.0.0
	 */
	public function submitbox_js() {

		if ( ! function_exists( 'get_current_screen' ) ) {
			return;
		}

		$screen = get_current_screen();

		if ( 'wpb-rs-global' !== $screen->post_type ) {
			return;
		}


		?>
        <script type="text/javascript">
            jQuery('#visibility, .misc-pub-curtime').remove();
        </script>
		<?php
	}


	/**
	 * Print an admin notice to ask if we should install examples.
	 *
	 * @since 2.0.0
	 */
	public function predefined_notice() {

		if ( ! function_exists( 'get_current_screen' ) ) {
			return;
		}

		$screen = get_current_screen();

		if ( ! $screen instanceof \WP_Screen ) {
			return;
		}

		if ( 'edit-wpb-rs-global' !== $screen->id ) {
			return;
		}

		if ( true === (bool) get_option( 'wpb_rs/predefined/message/hidden', false ) ) {
			return;
		}

		$user = wp_get_current_user();
		?>
        <div class="notice notice-info">
            <p><?php
				printf(
					__( 'Hey <strong>%s!</strong> The plugin can install some predefined global snippets for you!', 'rich-snippets-schema' ),
					$user->display_name
				);

				printf(
					' <a class="button" href="%s">%s</a>',
					admin_url( 'edit.php?post_type=wpb-rs-global&install_predefined=1&_wpnonce=' ) . wp_create_nonce( 'wpbrs_install_predefined' ),
					__( 'Awesome! Install them please.', 'rich-snippets-schema' )
				);
				?>
            </p>
        </div>
		<?php
	}


	/**
	 * Filters the row actions.
	 *
	 * @param array $actions
	 * @param \WP_Post $post
	 *
	 * @return array
	 * @since 2.0.0
	 *
	 */
	public function filter_row_actions( $actions, $post ) {

		if ( 'wpb-rs-global' !== $post->post_type ) {
			return $actions;
		}

		# Remove the quick edit option.
		# Note that the quickedit option has a weird name ('inline hide-if-no-js')
		if ( isset( $actions['inline hide-if-no-js'] ) ) {
			unset( $actions['inline hide-if-no-js'] );
		}

		return array_merge( [ 'ID: ' . $post->ID ], $actions );
	}

	/**
	 * Make sure the main menu is highlighted if we're on the "Global Snippets"
	 *
	 * @param string $parent_file
	 *
	 * @return string
	 *
	 * @since 2.14.0
	 */
	public function highlight_menu( $parent_file ) {


		$screen = get_current_screen();

		if ( $screen instanceof \WP_Screen && $screen->id === 'wpb-rs-global' ) {
			global $plugin_page;
			$plugin_page = 'edit.php?post_type=wpb-rs-global';
		}

		return $parent_file;
	}


	/**
	 * Manage wpb-rs-global post columns.
	 *
	 * @param array $columns
	 *
	 * @return array
	 * @since 2.8.0
	 *
	 */
	public function manage_posts_columns( $columns ) {

		$columns = Helper_Model::instance()->integrate_into_array(
			$columns,
			2,
			[
				'snippet-ids' => __( 'Snippet IDs', 'rich-snippets-schema' )
			]
		);

		return $columns;
	}


	/**
	 * Print custom columns on wpb-rs-global post overview page.
	 *
	 * @param string $column_name
	 * @param int $post_id
	 *
	 * @since 2.8.0
	 */
	public function print_custom_columns( $column_name, $post_id ) {
		if ( 'snippet-ids' === $column_name || 'predefined' === $column_name ) {
			$snippets = Snippets_Model::get_snippets( $post_id );

			foreach ( $snippets as $snippet ) {
				if ( 'predefined' === $column_name ) {
					if ( false !== stripos( $snippet->id, 'snip-global-' ) ) {
						echo '<span class="dashicons dashicons-yes"></span>';
					} else {
						echo '<span class="dashicons dashicons-no"></span>';
					}
					break;
				} else {
					echo $snippet->id;
				}
			}

		}

		if ( 'sync' === $column_name ) {
			$synced_id = intval( get_post_meta( $post_id, '_wpb_rs_sync_id', true ) );

			if ( $synced_id > 0 ) {
				echo '<span class="dashicons dashicons-yes wpb-rs-global-snippet-sync-yes"></span>';
			} else {
				echo '<span class="dashicons dashicons-no wpb-rs-global-snippet-sync-no"></span>';
			}
		}
	}

	/**
	 * Returns the current scripts controller.
	 *
	 * @since 2.19.0
	 */
	public function get_scripts_controller() {
		return Admin_Scripts_Controller::instance();
	}
}