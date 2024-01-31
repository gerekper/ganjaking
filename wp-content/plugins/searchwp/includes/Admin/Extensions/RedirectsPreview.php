<?php

namespace SearchWP\Admin\Extensions;

use SearchWP\Admin\NavTab;
use SearchWP\License;
use SearchWP\Settings;
use SearchWP\Utils;

/**
 * SearchWP RedirectsPreview.
 *
 * @since 4.3.10
 */
class RedirectsPreview {

	/**
	 * Slug for this view.
	 *
	 * @since 4.3.10
	 *
	 * @var string
	 */
	private static $slug = 'redirects';

	/**
	 * Parent page slug for this view.
	 *
	 * @since 4.3.10
	 *
	 * @var string
	 */
	private static $parent_slug = 'settings';

	/**
	 * RedirectsPreview constructor.
	 *
	 * @since 4.3.10
	 */
	function __construct() {

		if ( Utils::is_swp_admin_page( self::$parent_slug ) ) {
			new NavTab(
				[
					'page'       => self::$parent_slug,
					'tab'        => 'extensions',
					'label'      => __( 'Redirects', 'searchwp' ),
					'classes'    => [ 'swp-nav-link--preview' ],
					'query_args' => [
						'extension' => self::$slug,
					],
				]
			);
		}

		if (
			Utils::is_swp_admin_extension_settings_page( self::$slug, self::$parent_slug ) &&
			current_user_can( Settings::get_capability() )
		) {
			add_action( 'searchwp\settings\view',  [ __CLASS__, 'render' ] );
			add_action( 'admin_enqueue_scripts', [ __CLASS__, 'assets' ] );
		}
	}

	/**
	 * Output the view for the Redirects preview screen.
	 *
	 * @since 4.3.10
	 */
	public static function render() {

		$license_type = License::get_type();

		$title      = '';
		$link_text  = '';
		$link_url   = '';
		$bonus_text = '';
		$target     = '';

		switch ( $license_type ) {
			case '':
			case 'standard':
				$title      = __( 'Redirects is a PRO Feature', 'searchwp' );
				$link_text  = __( 'Upgrade to SearchWP PRO', 'searchwp' );
				$link_url   = 'https://searchwp.com/account/downloads/?utm_source=WordPress&utm_medium=Redirects+Upsell+Button&utm_campaign=SearchWP&utm_content=Upgrade+to+SearchWP+PRO';
				$bonus_text = empty( $license_type ) ? '' : __( '<strong>Bonus:</strong> SearchWP Standard users get up to <span class="green">$200 off their upgrade price</span>, automatically applied at checkout.', 'searchwp' );
			    $target     = '_blank';
				break;

			case 'pro':
			case 'agency':
			    $title     = __( 'Redirects extension is not active', 'searchwp' );
			    $link_text = __( 'Activate Extension', 'searchwp' );
			    $link_url  = admin_url( 'admin.php?page=searchwp-extensions' );
				break;

			default:
				break;
		}

        self::render_preview();
		?>

		<div id="extension-preview-upsell">
			<div id="extension-preview-upsell-background">
                <h5><?php echo esc_html( $title ); ?></h5>
                <p>
					<?php
						echo wp_kses(
							sprintf(
								__( 'Automatically redirect to a specific page when certain searches are performed! While SearchWP will find the most relevant search results for you, there are times where you simply want to help your visitor move directly to a page of your choosing, saving them a click in the process. <a href="%s" target="_blank">View&nbsp;Docs</a>', 'searchwp' ),
								'https://searchwp.com/extensions/redirects/'
							),
							[
								'a' => [
									'href'   => [],
									'target' => [],
								],
							]
						);
					?>
                </p>

                <div class="list">
                    <ul>
                        <li><?php esc_html_e( 'Easily redirect searches', 'searchwp' ); ?></li>
                        <li><?php esc_html_e( 'Increase Conversion Rates', 'searchwp' ); ?></li>
                    </ul>
                    <ul>
                        <li><?php esc_html_e( 'Improve User Experience', 'searchwp' ); ?></li>
                    </ul>
                </div>

				<?php if ( ! empty( $bonus_text ) ) : ?>
                    <p>
						<?php
							echo wp_kses(
								$bonus_text,
								[
									'strong' => [],
									'span'   => [
										'class' => [],
									],
								]
							);
						?>
					</p>
				<?php endif; ?>

				<a class="swp-button swp-button--green" href="<?php echo esc_url( $link_url ); ?>" target="<?php echo esc_attr( $target ); ?>" rel="noopener noreferrer" title="<?php echo esc_html( $link_text ); ?>"><?php echo esc_html( $link_text ); ?></a>

			</div>
		</div>
		<?php
	}

	/**
	 * Enqueue assets for the Redirects preview screen.
	 *
	 * @since 4.3.10
	 */
	public static function assets() {

		$handle = SEARCHWP_PREFIX . self::$slug . '_preview';

		wp_enqueue_style(
			$handle,
			SEARCHWP_PLUGIN_URL . 'assets/css/admin/pages/redirects-preview.css',
			[],
			SEARCHWP_VERSION
		);
	}

    /**
     * Output the content preview for the Redirects preview screen.
     *
     * @since 4.3.10
     */
    public static function render_preview() {
        ?>
        <div id="searchwp-redirects-preview">
            <div class="extension-preview-wrap">
                <div class="wrap" id="searchwp-redirects-wrapper">
                    <div class="redirects-container">
                        <h2>SearchWP Redirects</h2>
                        <div class="searchwp-redirects-settings">
                            <p>Manage Redirects by defining a search query, applicable engine(s), and destination URL.</p>
                            <p><button class="button" id="searchwp-redirects-migrate-toggle">Toggle Import/Export</button></p>
                            <div>
                                <table class="searchwp-redirects">
                                    <colgroup>
                                        <col id="searchwp-redirects-col-query">
                                        <col id="searchwp-redirects-col-engines">
                                        <col id="searchwp-redirects-col-destination">
                                    </colgroup>
                                    <thead>
                                    <tr>
                                        <th>Query</th>
                                        <th>Engine(s)</th>
                                        <th>Redirect</th>
                                    </tr>
                                    </thead>
                                    <tbody class="ui-sortable">

                                    <tr>
                                        <td>
                                            <div class="searchwp-flexible">
                                                <div><span class="dashicons dashicons-menu searchwp-handle"></span></div>
                                                <a href="#" class="searchwp-redirect-delete">×</a>
                                                <input type="text" class="searchwp-redirect-input" value="marketing">
                                                <div class="searchwp-redirects-partial searchwp-flexible">
                                                    <input type="checkbox" value="1">
                                                    <label for="searchwp_redirects_searchwp_redirects65799ba8dba16_partial">Partial	Match</label>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <input type="text" style="width: 90%;" class="searchwp-redirect-engines select2-hidden-accessible" aria-hidden="true" value="All">
                                        </td>
                                        <td>
                                            <input type="text" class="searchwp-redirect-input" value="/our-success-stories/">
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>
                                            <div class="searchwp-flexible">
                                                <div><span class="dashicons dashicons-menu searchwp-handle"></span></div>
                                                <a href="#" class="searchwp-redirect-delete">×</a>
                                                <input type="text" class="searchwp-redirect-input" value="office">
                                                <div class="searchwp-redirects-partial searchwp-flexible">
                                                    <input type="checkbox" value="1">
                                                    <label>Partial Match</label>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <input type="text" style="width: 90%;" aria-hidden="true" value="All">
                                        </td>
                                        <td>
                                            <input type="text" class="searchwp-redirect-input" value="/about-us/">
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                                <p><a class="button searchwp-add-redirect" href="#">Add Redirect</a></p>
                                <p class="submit">
                                    <input type="submit" name="submit" id="submit" class="button button-primary" value="Save Redirects">
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
}
