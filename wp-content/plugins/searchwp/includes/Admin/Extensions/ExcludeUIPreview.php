<?php

namespace SearchWP\Admin\Extensions;

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
	function __construct() {

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
	 * @param $screen
	 *
	 * @return void
	 */
    public function register_block_type( $screen ) {

        if ( ! empty( $screen ) && isset( $screen->post_type ) && ! empty( $screen->post_type ) ) {
			$source_name    = \SearchWP\Utils::get_post_type_source_name( $screen->post_type );
			$source         = \SearchWP::$index->get_source_by_name( $source_name );

			if ( ! \SearchWP\Utils::any_engine_has_source( $source ) ) {
				return;
			}
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
		$post_type      = get_post_type_object( $post_type_name );
		$source_name    = \SearchWP\Utils::get_post_type_source_name( $post_type_name );
		$source         = \SearchWP::$index->get_source_by_name( $source_name );

        if ( ! \SearchWP\Utils::any_engine_has_source( $source ) ) {
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
					<?php _e( 'Exclude from SearchWP', 'searchwp' ); ?>
				</label>
				<span style="display:block;margin-top:10px">
					<span>
						<?php echo sprintf(
							__( 'Activate the SearchWP Exclude UI extension and exclude any %s from your search results.', 'searchwp' ),
							$post_type->labels->singular_name
						); ?>
					</span>
					<br>
					<a href="https://searchwp.com/extensions/exclude-ui/" target="_blank"><?php _e( 'View Docs', 'searchwp' ); ?></a>,
					<a href="<?php echo esc_url( admin_url( 'admin.php?page=searchwp-extensions' ) ); ?>" target="_blank"><?php _e( 'Activate', 'searchwp' ); ?></a>
				</span>
			</div>
		</div>

        <?php
        // Output the JavaScript to dismiss the Exclude UI Preview.
        add_action( 'admin_footer', function() {
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
        } );
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
        }
        else {
            wp_send_json_error();
		}
    }
}
