<?php

namespace SearchWP\Debug\Console;

use SearchWP\Settings;
use SearchWP\Utils;

/**
 * Console class manages SearchWP Debugging Console functionality.
 *
 * @since 4.2.9
 */
class Console {

    /**
	 * Init.
	 *
	 * @since 4.2.9
	 */
	public static function init() {

		if ( self::is_enabled() ) {
			self::hooks();
		}
	}

	/**
	 * Hooks.
	 *
	 * @since 4.2.9
	 */
	public static function hooks() {

		add_action( 'wp_before_admin_bar_render', [ __CLASS__, 'add_admin_bar_menu' ] );
		add_action( 'wp_enqueue_scripts', [ __CLASS__, 'assets' ] );
		add_action( 'wp_footer', [ __CLASS__, 'render' ], 999 );
    }

	/**
	 * Check if current user can access the Console.
	 *
	 * @since 4.2.9
	 */
    public static function is_enabled() {

        if ( ! apply_filters( 'searchwp\debug', Settings::get( 'debug', 'boolean' ) ) ) {
            return false;
        }

	    if ( ! apply_filters( 'searchwp\debug\console', true ) ) {
		    return false;
	    }

	    if ( ! current_user_can( Settings::get_capability() ) ) {
		    return false;
	    }

        return true;
    }

	/**
	 * Add Debug to the SearchWP admin bar menu.
	 *
	 * @since 4.2.9
	 */
	public static function add_admin_bar_menu() {

		global $wp_admin_bar;

		if ( is_admin() ) {
			return;
		}

		if ( ! is_admin_bar_showing() || ! apply_filters( 'searchwp\admin_bar', current_user_can( Settings::get_capability() ) ) ) {
			return;
		}

		$wp_admin_bar->add_menu(
			[
				'id'    => Utils::$slug . '-debug',
				'title' => __( 'SearchWP Debug', 'searchwp' ),
				'href'  => '#debug',
			]
		);
	}

	/**
	 * Register assets.
	 *
	 * @since 4.2.9
	 */
	public static function assets() {

        $handle = Utils::$slug . '_debug_console';

		wp_enqueue_style( $handle, SEARCHWP_PLUGIN_URL . 'assets/css/debug-console.css', [], SEARCHWP_VERSION );
		wp_enqueue_script( $handle, SEARCHWP_PLUGIN_URL . 'assets/js/debug-console.js', [ 'jquery' ], SEARCHWP_VERSION );
	}

	/**
	 * Render the console.
	 *
	 * @since 4.2.9
	 */
	public static function render() {

		?>
        <div id="searchwp-debug-console-main">
			<?php self::print_header_html(); ?>
			<?php self::print_panels_html(); ?>
        </div>
		<?php
	}

	/**
	 * Print console header markup.
	 *
	 * @since 4.2.9
	 */
	private static function print_header_html() {

		?>
        <div id="searchwp-console-header">
            <h1 id="searchwp-console-header-title">
				<?php esc_html_e( 'SearchWP Console', 'searchwp' ); ?>
            </h1>
            <button class="searchwp-console-header-button searchwp-console-close" aria-label="<?php esc_html_e( 'Close Console', 'searchwp' ); ?>">
                    <span class="searchwp-console-icon" aria-hidden="true">
                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="20" height="20" viewBox="0 0 20 20">
                            <path d="M14.95 6.46l-3.54 3.54 3.54 3.54-1.41 1.41-3.54-3.53-3.53 3.53-1.42-1.42 3.53-3.53-3.53-3.53 1.42-1.42 3.53 3.53 3.54-3.53z"></path>
                        </svg>
                    </span>
            </button>
        </div>
		<?php
	}

	/**
	 * Print console panels markup.
	 *
	 * @since 4.2.9
	 */
	private static function print_panels_html() {

		$panels = Panels::get_panels();

		$nav_html     = '';
		$content_html = '';

		foreach ( $panels as $panel ) {
			$nav_html     .= self::get_panel_nav_html( $panel );
			$content_html .= self::get_panel_content_html( $panel );
		}

		?>
        <div id="searchwp-console-panels">
            <nav id="searchwp-console-panels-nav">
                <ul role="tablist">
					<?php echo wp_kses_post( $nav_html ); ?>
                </ul>
            </nav>
            <div id="searchwp-console-panels-content">
				<?php echo wp_kses_post( $content_html ); ?>
            </div>
        </div>
		<?php
	}

	/**
	 * Get console panel nav markup.
     *
     * @param array $panel Panel data.
	 *
	 * @since 4.2.9
	 */
    private static function get_panel_nav_html( $panel ) {

	    ob_start();

	    ?>
        <li role="presentation">
            <button role="tab" data-swp-href="#swp-<?php echo esc_attr( $panel['id'] ); ?>" data-swp-slug="<?php echo esc_attr( empty( $panel['slug'] ) ? $panel['id'] : $panel['slug'] ); ?>">
		        <?php echo esc_html( $panel['title'] ); ?>
            </button>

		    <?php if ( isset( $panel['sub_panels'] ) ) : ?>
                <ul role="presentation">
				    <?php foreach ( $panel['sub_panels'] as $sub_panel ) : ?>
                        <li role="presentation">
                            <button role="tab" data-swp-href="#swp-<?php echo esc_attr( $sub_panel['id'] ); ?>" data-swp-slug="<?php echo esc_attr( empty( $sub_panel['slug'] ) ? $sub_panel['id'] : $sub_panel['slug'] ); ?>">
		                        <?php echo esc_html( $sub_panel['title'] ); ?>
                            </button>
                        </li>
				    <?php endforeach; ?>
                </ul>
		    <?php endif; ?>
        </li>
	    <?php

	    return ob_get_clean();
    }

	/**
	 * Get console panel content markup.
	 *
	 * @param array $panel Panel data.
	 *
	 * @since 4.2.9
	 */
	private static function get_panel_content_html( $panel ) {

		ob_start();

		?>
        <div class="searchwp-panel-content" id="swp-<?php echo esc_attr( $panel['id'] ); ?>" role="tabpanel" tabindex="-1">
            <section>
                <pre><?php echo wp_kses_post( $panel['content'] ); ?></pre>
            </section>
        </div>
        <?php

        if ( isset( $panel['sub_panels'] ) ) {
            foreach ( $panel['sub_panels'] as $sub_panel ) {
	            ?>
                <div class="searchwp-panel-content" id="swp-<?php echo esc_attr( $sub_panel['id'] ); ?>" role="tabpanel" tabindex="-1">
                    <section>
                        <pre><?php echo wp_kses_post( $sub_panel['content'] ); ?></pre>
                    </section>
                </div>
                <?php
            }
        }

		return ob_get_clean();
	}
}
