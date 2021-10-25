<?php
/**
 * WooCommerce Local Pickup Plus
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Local Pickup Plus to newer
 * versions in the future. If you wish to customize WooCommerce Local Pickup Plus for your
 * needs please refer to http://docs.woocommerce.com/document/local-pickup-plus/
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2012-2021, SkyVerge, Inc.
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

use SkyVerge\WooCommerce\PluginFramework\v5_10_9 as Framework;

defined( 'ABSPATH' ) or exit;

/**
 * Abstract Meta Box for Local Pickup Plus.
 *
 * Serves as a base meta box class for different meta boxes. One of the goals
 * is to keep meta box classes as self-contained as possible, removing any
 * external setup or configuration.
 *
 * @since 2.0.0
 */
abstract class WC_Local_Pickup_Plus_Meta_Box {


	/** @var string meta box ID **/
	protected $id;

	/** @var string meta box context **/
	protected $context = 'normal';

	/** @var string meta box priority **/
	protected $priority = 'default';

	/** @var array list of supported screen IDs **/
	protected $screens = array();

	/** @var array list of additional postbox classes for this meta box **/
	protected $postbox_classes = array( 'woocommerce', 'wc-local-pickup-plus' );

	/** @var \WP_Post current post where the meta box appears */
	protected $post;


	/**
	 * Meta box constructor.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {

		// add/edit screen hooks
		add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );

		// enqueue meta box scripts and styles, but only if the meta box has scripts or styles
		if ( method_exists( $this, 'enqueue_scripts_and_styles' ) ) {
			add_action( 'admin_enqueue_scripts', array( $this, 'maybe_enqueue_scripts_and_styles' ) );
		}

		// update meta box data when saving post, but only if the meta box supports data updates
		if ( method_exists( $this, 'update_data' ) ) {
			add_action( 'save_post', array( $this, 'save_post' ), 10, 2 );
		}
	}


	/**
	 * Get the meta box title.
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	abstract public function get_title();


	/**
	 * Get the meta box ID.
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	public function get_id() {
		return $this->id;
	}


	/**
	 * Get the meta box ID, with underscores instead of dashes.
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	protected function get_id_underscored() {
		return str_replace( '-', '_', $this->id );
	}


	/**
	 * Get the nonce name for this meta box.
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	protected function get_nonce_name() {
		return '_' . $this->get_id_underscored() . '_nonce';
	}


	/**
	 * Get the nonce action for this meta box.
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	protected function get_nonce_action() {
		return 'update-' . $this->id;
	}


	/**
	 * Get the post object.
	 *
	 * @since 2.0.0
	 *
	 * @return \WP_Post
	 */
	public function get_post() {
		return $this->post;
	}


	/**
	 * Get a post meta for the post object in context.
	 *
	 * @since 2.0.0
	 *
	 * @param string $meta_key post meta key
	 * @param string $default_value default value of the post meta (optional, defaults to empty string)
	 * @return string|array|float|int
	 */
	protected function get_post_meta( $meta_key, $default_value = '' ) {
		global $post;

		if ( ! isset( $this->post->ID ) ) {
			$post_id = $this->post->ID;
		} elseif ( $post && isset( $post->ID ) ) {
			$post_id = $post->ID;
		} else {
			return null;
		}

		$value = get_post_meta( $post_id, $meta_key, true );

		if ( empty( $value ) && ! empty( $default_value ) ) {
			return $default_value;
		}

		return $value;
	}


	/**
	 * Enqueue scripts & styles for this meta box, if conditions are met.
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 */
	public function maybe_enqueue_scripts_and_styles() {

		$screen = get_current_screen();

		if ( ! $screen || ! in_array( $screen->id, $this->screens, true ) ) {
			return;
		}

		$this->enqueue_scripts_and_styles();
	}


	/**
	 * Enqueue scripts and styles for this meta box.
	 *
	 * Note by default this method in abstract class returns nor does anything.
	 * If a meta box needs to enqueue scripts or styles, then it can override this
	 * method which will be automatically passed to maybe_enqueue_scripts_and_styles()
	 * and thus to the 'admin_enqueue_scripts' admin action.
	 *
	 * @since 2.0.0
	 */
	protected function enqueue_scripts_and_styles() {}


	/**
	 * Add meta box to the supported screen(s).
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 */
	public function add_meta_box() {
		global $post, $current_screen;

		// sanity checks
		if (    ! $post instanceof \WP_Post
		     || ! $current_screen
		     || ! in_array( $current_screen->id, $this->screens, true )
		     || ! current_user_can( 'manage_woocommerce_pickup_locations' ) ) {
			return;
		}

		add_meta_box(
			$this->id,
			$this->get_title(),
			array( $this, 'do_output' ),
			$current_screen->id,
			$this->context,
			$this->priority
		);

		add_filter( "postbox_classes_{$current_screen->id}_{$this->id}", array( $this, 'postbox_classes' ) );
	}


	/**
	 * Add meta box classes.
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 *
	 * @param string[] $classes
	 * @return string[]
	 */
	public function postbox_classes( $classes ) {
		return array_merge( $classes, $this->postbox_classes );
	}


	/**
	 * Output basic meta box contents.
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 */
	public function do_output() {
		global $post;

		$this->post = $post;

		// add a nonce field
		if ( method_exists( $this, 'update_data' ) ) {
			wp_nonce_field( $this->get_nonce_action(), $this->get_nonce_name() );
		}

		// output the child meta box HTML ?>
		<div class="wc-local-pickup-plus wc-local-pickup-plus-meta-box <?php echo $this->id; ?>">
			<?php $this->output( $post ); ?>
		</div>
		<?php
	}


	/**
	 * Output meta box contents.
	 *
	 * @internal
	 *
	 * @param \WP_Post $post the post object.
	 * @since 2.0.0
	 */
	abstract public function output( \WP_Post $post );


	/**
	 * Process and save meta box data.
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 *
	 * @param int $post_id the post ID
	 * @param \WP_Post $post the post object
	 */
	public function save_post( $post_id, \WP_Post $post ) {

		// check nonce
		if ( ! isset( $_POST[ $this->get_nonce_name() ] ) || ! wp_verify_nonce( $_POST[ $this->get_nonce_name() ], $this->get_nonce_action() ) ) {
			return;
		}

		// if this is an autosave, our form has not been submitted, so we don't want to do anything
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// bail out if not a supported post type
		if ( ! in_array( $post->post_type, $this->screens, true ) ) {
			return;
		}

		// Check the user's permissions.
		if ( isset( $_POST['post_type'] ) && 'page' === $_POST['post_type'] ) {
			if ( ! current_user_can( 'edit_page', $post_id ) ) {
				return;
			}
		} else {
			if ( ! current_user_can( 'edit_post', $post_id ) ) {
				return;
			}
		}

		if ( ! current_user_can( 'manage_woocommerce_pickup_locations' ) ) {
			return;
		}

		// implementation-specific meta box data update
		if ( method_exists( $this, 'update_data' ) ) {
			$this->update_data( $post_id, $post );
		}

		/**
		 * Save meta box posted data.
		 *
		 * @since 2.0.0
		 *
		 * @param array $_POST the Post data
		 * @param string $meta_box_id the meta box ID
		 * @param int $post_id \WP_Post ID
		 * @param \WP_Post $post \WP_Post object
		 */
		do_action( 'wc_local_pickup_plus_save_meta_box', $_POST, $this->id, $post_id, $post );
	}


}
