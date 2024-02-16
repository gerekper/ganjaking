<?php

namespace SearchWP\Admin\Extensions;

use SearchWP\Utils;

/**
 * SearchWP ExcludeUIPreview.
 *
 * @since 4.3.10
 */
class ExcludeUIPreview {

	/**
	 * ExcludeUIPreview constructor.
	 *
	 * @since 4.3.10
	 */
	public function __construct() {

        // Don't show the Exclude UI Preview if it was already dismissed.
        if ( get_option( 'searchwp_exclude_ui_preview_dismissed', false ) ) {
            return;
        }

        // Block editor.
		if ( function_exists( 'register_block_type' ) ) {
            add_action( 'current_screen', [ $this, 'register_block_type' ] );
		}

        // Classic editor.
		add_action( 'post_submitbox_misc_actions', [ $this, 'output_exclude_checkbox' ] );
		add_action( 'attachment_submitbox_misc_actions', [ $this, 'output_exclude_checkbox' ] );

        // AJAX callback to dismiss the Exclude UI Preview.
		add_action( 'wp_ajax_searchwp_exclude_ui_preview_dismissed', [ $this, 'dismiss_notice' ] );
	}

	/**
     * Registers the block type for the block editor.
     *
     * @since 4.3.10
	 *
	 * @param \WP_Screen $screen The current screen.
	 *
	 * @return void
	 */
    public function register_block_type( $screen ) {

		if ( ! $screen instanceof \WP_Screen || $screen->base !== 'post' || empty( $screen->post_type ) ) {
			return;
		}

		if ( $this->is_post_type_excluded( $screen->post_type ) ) {
			return;
		}

		register_block_type( SEARCHWP_PLUGIN_DIR . '/assets/gutenberg/build/exclude-ui-preview/' );
    }

	/**
     * Outputs the checkbox on the classic WordPress editor.
     *
     * @since 4.3.10
	 *
	 * @return void
	 */
	public function output_exclude_checkbox() {

		global $post;

		if ( empty( $post ) ) {
			return;
		}

		$post_type_name = get_post_type( $post );

		if ( empty( $post_type_name ) ) {
			return;
		}

		if ( $this->is_post_type_excluded( $post_type_name ) ) {
			return;
		}

		$post_type = get_post_type_object( $post_type_name );

		if ( ! $post_type instanceof \WP_Post_Type ) {
			return;
		}
		?>

		<div class="misc-pub-section" id="exclude-ui-preview-wrapper">
			<div style="position:relative;padding: 10px;border-radius: 2px;background-color: #f0f0f0;color: #7f7f7f;">
                <span style="display:block;position:absolute;top:0;right:0;">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" aria-hidden="true" focusable="false"><path d="M12 13.06l3.712 3.713 1.061-1.06L13.061 12l3.712-3.712-1.06-1.06L12 10.938 8.288 7.227l-1.061 1.06L10.939 12l-3.712 3.712 1.06 1.061L12 13.061z"></path></svg>
                </span>
				<label>
					<input type="checkbox" name="searchwp_exclude" value="" checked="false" disabled/>
					<?php esc_html_e( 'Exclude from SearchWP', 'searchwp' ); ?>
				</label>
				<span style="display:block;margin-top:10px">
					<span>
						<?php
							printf(
								/* translators: %s is the post type name. */
								esc_html__( 'Activate the SearchWP Exclude UI extension and exclude any %s from your search results.', 'searchwp' ),
								esc_html( $post_type->labels->singular_name )
							);
						?>
					</span>
					<br>
					<a href="https://searchwp.com/extensions/exclude-ui/" target="_blank"><?php esc_html_e( 'View Docs', 'searchwp' ); ?></a>,
					<a href="<?php echo esc_url( admin_url( 'admin.php?page=searchwp-extensions' ) ); ?>" target="_blank"><?php esc_html_e( 'Activate', 'searchwp' ); ?></a>
				</span>
			</div>
		</div>

        <?php
        // Output the JavaScript to dismiss the Exclude UI Preview.
        add_action(
			'admin_footer',
			function () {
				?>
				<script>
					(function($) {
						$('document').ready(function() {
							$('#exclude-ui-preview-wrapper svg').on('click', function(e) {
								e.preventDefault();
								$.ajax({
									url: ajaxurl,
									type: 'POST',
									data: {
										action: 'searchwp_exclude_ui_preview_dismissed'
									},
									success: function() {
										let element = document.querySelector('#exclude-ui-preview-wrapper');
										element.parentNode.removeChild(element);
									}
								});
							});
						});
					}(jQuery));
				</script>
				<?php
			}
		);
	}

	/**
	 * Checks if the post type is excluded from SearchWP.
	 *
	 * @since 4.3.11
	 *
	 * @param string $post_type The post type name.
	 *
	 * @return bool
	 */
	private function is_post_type_excluded( $post_type ) {

		$source_name = Utils::get_post_type_source_name( $post_type );

		if ( is_wp_error( $source_name ) ) {
			return false;
		}

		$source = \SearchWP::$index->get_source_by_name( $source_name );

		if ( ! $source instanceof \SearchWP\Source ) {
			return false;
		}

		return ! Utils::any_engine_has_source( $source );
	}

    /**
     * AJAX callback that marks the Exclude UI Preview as dismissed.
     *
     * @since 4.3.10
	 *
     * @return void
     */
    public function dismiss_notice() {

        if ( update_option( 'searchwp_exclude_ui_preview_dismissed', true, false ) ) {
            wp_send_json_success();
        } else {
            wp_send_json_error();
		}
    }
}
