<?php
namespace Happy_Addons\Elementor;

defined( 'ABSPATH' ) || die();

use Elementor\Core\Files\CSS\Post as Post_CSS;

class Clone_Handler {

	/**
	 * Request and nonce action name
	 */
	const ACTION = 'ha_duplicate_thing';

	/**
	 * Register hooks and initialize
	 */
	public static function init() {
		add_action( 'admin_action_' . self::ACTION, [ __CLASS__, 'duplicate_thing' ] );
		add_filter( 'post_row_actions', [ __CLASS__, 'add_row_actions' ], 10, 2 );
		add_filter( 'page_row_actions', [ __CLASS__, 'add_row_actions' ], 10, 2 );
	}

	/**
	 * Check if current user can clone
	 *
	 * @return bool
	 */
	public static function can_clone() {
		return current_user_can( 'edit_posts' );
	}

	/**
	 * Add clone link in row actions
	 *
	 * @param array $actions
	 * @param \WP_Post $post
	 * @return array
	 */
	public static function add_row_actions( $actions, $post ) {
		if ( self::can_clone() && post_type_supports( $post->post_type, 'elementor' ) ) {
			$actions[ self::ACTION ] = sprintf(
				'<a href="%1$s" title="%2$s"><span class="screen-reader-text">%2$s</span>%3$s</a>',
				esc_url( self::get_url( $post->ID, 'list' ) ),
				sprintf( esc_attr__( 'Clone - %s', 'happy-elementor-addons' ), esc_attr( $post->post_title ) ),
				esc_html__( 'Happy Clone', 'happy-elementor-addons' )
			);
		}

		return $actions;
	}

	/**
	 * Duplicate requested post
	 *
	 * @return void
	 */
	public static function duplicate_thing() {
		if ( ! self::can_clone() ) {
			return;
		}

		$_uri = $_SERVER['REQUEST_URI'];

		// Resolve finder clone request issue
		if ( stripos( $_uri, '&amp;' ) !== false ) {
			$_uri = html_entity_decode( $_uri );
			$_uri = parse_url( $_uri, PHP_URL_QUERY );
			$valid_args = ['_wpnonce', 'post_id', 'ref'];
			parse_str( $_uri, $args );

			if ( ! empty( $args ) && is_array( $args ) ) {
				foreach ( $args as $key => $val ) {
					if ( in_array( $key, $valid_args, true ) ) {
						$_GET[ $key ] = $val;
					}
				}
			}
		}

		$nonce = isset( $_GET['_wpnonce'] ) ? $_GET['_wpnonce'] : '';
		$post_id = isset( $_GET['post_id'] ) ? absint( $_GET['post_id'] ) : 0;
		$ref = isset( $_GET['ref'] ) ? sanitize_text_field($_GET['ref']) : '';

		if ( ! wp_verify_nonce( $nonce, self::ACTION ) ) {
			return;
		}

		if ( is_null( ( $post = get_post( $post_id ) ) ) ) {
			return;
		}

		$post = sanitize_post( $post, 'db' );
		$duplicated_post_id = self::duplicate_post( $post );
		$redirect = add_query_arg( [ 'post_type' => $post->post_type ], admin_url( 'edit.php' ) );

		if ( ! is_wp_error( $duplicated_post_id ) ) {
			self::duplicate_taxonomies( $post, $duplicated_post_id );
			self::duplicate_meta_entries( $post, $duplicated_post_id );

			$css = Post_CSS::create( $duplicated_post_id );
			$css->update();

			if ( $ref === 'editor' ) {
				$document = ha_elementor()->documents->get( $duplicated_post_id );
				$redirect = $document->get_edit_url();
			}
		}

		wp_safe_redirect( $redirect );
		die();
	}

	/**
	 * Get clone url with required query params
	 *
	 * @param $post_id
	 * @param string $ref
	 * @return string
	 */
	public static function get_url( $post_id, $ref = '' ) {
		return wp_nonce_url(
			add_query_arg(
				[
					'action' => self::ACTION,
					'post_id' => $post_id,
					'ref' => $ref,
				],
				admin_url( 'admin.php' )
			),
			self::ACTION
		);
	}

	/**
	 * Clone post
	 *
	 * @param $old_post
	 * @return int $dulicated post id
	 */
	protected static function duplicate_post( $post ) {
		$current_user = wp_get_current_user();

		$duplicated_post_args = [
			'post_status'    => 'draft',
			'to_ping'        => $post->to_ping,
			'post_type'      => $post->post_type,
			'menu_order'     => $post->menu_order,
			'post_author'    => $current_user->ID,
			'post_parent'    => $post->post_parent,
			'ping_status'    => $post->ping_status,
			'post_excerpt'   => $post->post_excerpt,
			'post_content'   => $post->post_content,
			'post_password'  => $post->post_password,
			'comment_status' => $post->comment_status,
			'post_title'     => sprintf( __( '%s - [Cloned #%d]', 'happy-elementor-addons' ), $post->post_title,
				$post->ID ),
		];

		return wp_insert_post( $duplicated_post_args );
	}

	/**
	 * Copy post taxonomies to cloned post
	 *
	 * @param $post
	 * @param $duplicated_post_id
	 */
	protected static function duplicate_taxonomies( $post, $duplicated_post_id ) {
		$taxonomies = get_object_taxonomies( $post->post_type );
		if ( ! empty( $taxonomies ) && is_array( $taxonomies ) ) {
			foreach ( $taxonomies as $taxonomy ) {
				$terms = wp_get_object_terms( $post->ID, $taxonomy, [ 'fields' => 'slugs' ] );
				wp_set_object_terms( $duplicated_post_id, $terms, $taxonomy, false );
			}
		}
	}

	/**
	 * Copy post meta entries to cloned post
	 *
	 * @param $post
	 * @param $duplicated_post_id
	 */
	protected static function duplicate_meta_entries( $post, $duplicated_post_id ) {
		global $wpdb;

		$entries = $wpdb->get_results(
			$wpdb->prepare( "SELECT meta_key, meta_value FROM {$wpdb->postmeta} WHERE post_id = %d", $post->ID )
		);

		if ( is_array( $entries ) ) {
			$query = "INSERT INTO {$wpdb->postmeta} ( post_id, meta_key, meta_value ) VALUES ";
			$_records = [];
			foreach ( $entries as $entry ) {
				$_value = wp_slash( $entry->meta_value );
				$_records[] = "( $duplicated_post_id, '{$entry->meta_key}', '{$_value}' )";
			}
			$query .= implode( ', ', $_records ) . ';';
			$wpdb->query( $query  );

			// Fix Template Type Wrong issue
			$source_type = get_post_meta($post->ID, '_elementor_template_type', true);
			delete_post_meta($duplicated_post_id, '_elementor_template_type');
			update_post_meta($duplicated_post_id, '_elementor_template_type', $source_type);
		}
	}

}

Clone_Handler::init();
