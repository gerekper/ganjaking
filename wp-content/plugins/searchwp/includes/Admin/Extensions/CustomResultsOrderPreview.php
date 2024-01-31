<?php

namespace SearchWP\Admin\Extensions;

use SearchWP\Admin\NavTab;
use SearchWP\License;
use SearchWP\Settings;
use SearchWP\Utils;

/**
 * SearchWP CustomResultsOrderPreview.
 *
 * @since 4.3.10
 */
class CustomResultsOrderPreview {

	/**
	 * Slug for this view.
	 *
	 * @since 4.3.10
	 *
	 * @var string
	 */
	private static $slug = 'custom-results-order';

	/**
	 * Parent page slug for this view.
	 *
	 * @since 4.3.10
	 *
	 * @var string
	 */
	private static $parent_slug = 'algorithm';

	/**
	 * CustomResultsOrderPreview constructor.
	 *
	 * @since 4.3.10
	 */
	function __construct() {

		if ( Utils::is_swp_admin_page( self::$parent_slug ) ) {
			new NavTab(
				[
					'page'       => self::$parent_slug,
					'tab'        => 'extensions',
					'label'      => __( 'Custom Results Order', 'searchwp' ),
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
     * Output the view for the Custom results Order preview screen.
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
                $title      = __( 'Custom Results Order is a PRO Feature', 'searchwp' );
				$link_text  = __( 'Upgrade to SearchWP PRO', 'searchwp' );
				$link_url   = 'https://searchwp.com/account/downloads/?utm_source=WordPress&utm_medium=Custom+Results+Order+Upsell+Button&utm_campaign=SearchWP&utm_content=Upgrade+to+SearchWP+PRO';
				$bonus_text = empty( $license_type ) ? '' : __( '<strong>Bonus:</strong> SearchWP Standard users get up to <span class="green">$200 off their upgrade price</span>, automatically applied at checkout.', 'searchwp' );
                $target     = '_blank';
				break;

			case 'pro':
			case 'agency':
                $title     = __( 'Custom Results Order extension is not active', 'searchwp' );
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
								__( 'Curate your own results order and prioritize them based on what matters most to you. Tailor your search results to ensure that the most important and relevant information is displayed prominently. <a href="%s" target="_blank">View&nbsp;Docs</a>', 'searchwp' ),
								'https://searchwp.com/extensions/custom-results-order/'
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
                        <li><?php esc_html_e( 'Customize search results', 'searchwp' ); ?></li>
                        <li><?php esc_html_e( 'Highlight important content', 'searchwp' ); ?></li>
                    </ul>
                    <ul>
                        <li><?php esc_html_e( 'Promote specific products or offers', 'searchwp' ); ?></li>
                        <li><?php esc_html_e( 'Enhance user experience', 'searchwp' ); ?></li>
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
     * Enqueue assets for the Custom Results Order preview screen.
     *
     * @since 4.3.10
	 */
	public static function assets() {

		$handle = SEARCHWP_PREFIX . self::$slug . '_preview';

		wp_enqueue_style(
			$handle,
			SEARCHWP_PLUGIN_URL . 'assets/css/admin/pages/custom-results-order-preview.css',
			[],
			SEARCHWP_VERSION
		);
	}

	/**
     * Output the content preview for the Custom results Order preview screen.
     *
     * @since 4.3.10
	 */
    public static function render_preview() {
        ?>
        <div id="searchwp-cro-preview">
            <div class="extension-preview-wrap">
                <div class="wrap" id="searchwp-custom-results-order-wrapper">

                    <div class="custom-results-order-container">
                        <h2>SearchWP Custom Results Order</h2>
                        <div class="searchwp-cro metabox-holder">
                            <div class="searchwp-cro-triggers postbox">
                                <div class="inside">
                                    <div class="searchwp-cro-triggers-container">
                                        <div class="searchwp-cro-trigger">
                                            <h3 class="searchwp-cro-trigger__heading">
                                                        <span class="searchwp-cro-trigger__label">
                                                            <span class="dashicons dashicons-arrow-down"></span>
                                                            <span class="searchwp-cro-trigger__label-details"> marketing <span><span>Exact match</span>, Default engine </span></span>
                                                        </span>
                                                <button class="searchwp-cro-trigger__remove">Remove</button>
                                            </h3>
                                            <div class="searchwp-cro-trigger__details">
                                                <div class="searchwp-cro-trigger__results">
                                                    <ol class="searchwp-cro__results-list">
                                                        <li class="searchwp-cro__result">
                                                            <div class="searchwp-cro__result">
                                                                <div class="searchwp-cro__result-meta">
                                                                    <h2><span class="searchwp-cro__result-meta-title">Effective Marketing Strategies for Small Businesses</span><span class="searchwp-cro__result-meta-id"><span>(Post ID: 15)</span></span></h2>
                                                                </div>
                                                                <ul class="searchwp-cro__result-actions">
                                                                    <li>
                                                                        <button title="Return this result to natural rank" class="button searchwp-cro__result-action-unpromote">
                                                                            <span><span class="dashicons dashicons-download"></span> <span>Remove Promotion</span></span>
                                                                        </button>
                                                                    </li>
                                                                    <li>
                                                                        <button title="Make this the first result" class="button searchwp-cro__result-action-promote searchwp-cro__result-action--unavailable">
                                                                            <span><span class="dashicons dashicons-star-filled"></span> <span>Promote to Top</span></span>
                                                                        </button>
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                        </li>
                                                        <li class="searchwp-cro__result">
                                                            <div class="searchwp-cro__result">
                                                                <div class="searchwp-cro__result-meta">
                                                                    <h2><span class="searchwp-cro__result-meta-title">Unlocking the Potential of Content Marketing: Tips and Tricks</span><span class="searchwp-cro__result-meta-id"><span>(Post ID: 8)</span></span></h2>
                                                                </div>
                                                                <ul class="searchwp-cro__result-actions">
                                                                    <li>
                                                                        <button title="Return this result to natural rank" class="button searchwp-cro__result-action-unpromote searchwp-cro__result-action--unavailable">
                                                                            <span><span class="dashicons dashicons-download"></span><span>Remove Promotion</span></span>
                                                                        </button>
                                                                    </li>
                                                                    <li>
                                                                        <button title="Make this the first result"
                                                                                class="button searchwp-cro__result-action-promote">
                                                                            <span><span class="dashicons dashicons-star-filled"></span><span>Promote to Top</span></span>
                                                                        </button>
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                        </li>
                                                        <li class="searchwp-cro__result">
                                                            <div class="searchwp-cro__result">
                                                                <div class="searchwp-cro__result-meta">
                                                                    <h2><span class="searchwp-cro__result-meta-title">How to Create an Effective Content Marketing Strategy</span><span class="searchwp-cro__result-meta-id"><span>(Post ID: 11)</span></span></h2>
                                                                </div>
                                                                <ul class="searchwp-cro__result-actions">
                                                                    <li>
                                                                        <button title="Return this result to natural rank" class="button searchwp-cro__result-action-unpromote searchwp-cro__result-action--unavailable">
                                                                            <span><span class="dashicons dashicons-download"></span><span>Remove Promotion</span></span>
                                                                        </button>
                                                                    </li>
                                                                    <li>
                                                                        <button title="Make this the first result" class="button searchwp-cro__result-action-promote">
                                                                            <span><span class="dashicons dashicons-star-filled"></span><span>Promote to Top</span></span>
                                                                        </button>
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                        </li>
                                                        <li class="searchwp-cro__result">
                                                            <div class="searchwp-cro__result">
                                                                <div class="searchwp-cro__result-meta">
                                                                    <h2><span class="searchwp-cro__result-meta-title">The Power of Email Marketing: Boosting Sales and Engagement</span><span class="searchwp-cro__result-meta-id"><span>(Post ID: 13)</span></span></h2>
                                                                </div>
                                                                <ul class="searchwp-cro__result-actions">
                                                                    <li>
                                                                        <button title="Return this result to natural rank" class="button searchwp-cro__result-action-unpromote searchwp-cro__result-action--unavailable">
                                                                            <span><span class="dashicons dashicons-download"></span><span>Remove Promotion</span></span>
                                                                        </button>
                                                                    </li>
                                                                    <li>
                                                                        <button title="Make this the first result" class="button searchwp-cro__result-action-promote">
                                                                            <span><span class="dashicons dashicons-star-filled"></span><span>Promote to Top</span></span>
                                                                        </button>
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                        </li>
                                                    </ol>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <ul class="searchwp-cro-triggers-actions">
                                <li class="searchwp-cro-trigger-query"><input type="text" placeholder="Enter search query"></li>
                                <li class="searchwp-cro-trigger-exact"><input type="checkbox" id="searchwp-cro-trigger-exact">
                                    <label for="searchwp-cro-trigger-exact">
                                                <span class="searchwp-tooltip"><span>Exact</span>
                                                    <span class="dashicons dashicons-editor-help v-popper--has-tooltip"></span>
                                                </span>
                                    </label>
                                </li>
                                <li class="searchwp-cro-trigger-engine">
                                    <select>
                                        <option value="0"> Default Engine</option>
                                    </select>
                                </li>
                                <li class="searchwp-cro-add-trigger">
                                    <button class="button button-primary">Add Search Query</button>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
	}
}
