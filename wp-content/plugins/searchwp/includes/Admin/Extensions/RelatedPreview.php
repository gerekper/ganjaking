<?php

namespace SearchWP\Admin\Extensions;

use SearchWP\Admin\NavTab;
use SearchWP\License;
use SearchWP\Settings;
use SearchWP\Utils;

/**
 * SearchWP RelatedPreview.
 *
 * @since 4.3.10
 */
class RelatedPreview {

	/**
	 * Slug for this view.
	 *
	 * @since 4.3.10
	 *
	 * @var string
	 */
	private static $slug = 'related';

	/**
	 * Parent page slug for this view.
	 *
	 * @since 4.3.10
	 *
	 * @var string
	 */
	private static $parent_slug = 'settings';

	/**
	 * RelatedPreview constructor.
	 *
	 * @since 4.3.10
	 */
	function __construct() {

		if ( Utils::is_swp_admin_page( self::$parent_slug ) ) {
			new NavTab(
				[
					'page'       => self::$parent_slug,
					'tab'        => 'extensions',
					'label'      => __( 'Related', 'searchwp' ),
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
	 * Output the view for the Related preview screen.
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
                $title      = __( 'Related is a PRO Feature', 'searchwp' );
				$link_text  = __( 'Upgrade to SearchWP PRO', 'searchwp' );
				$link_url   = 'https://searchwp.com/account/downloads/?utm_source=WordPress&utm_medium=SearchWP+Related+Upsell+Button&utm_campaign=SearchWP&utm_content=Upgrade+to+SearchWP+PRO';
				$bonus_text = empty( $license_type ) ? '' : __( '<strong>Bonus:</strong> SearchWP Standard users get up to <span class="green">$200 off their upgrade price</span>, automatically applied at checkout.', 'searchwp' );
                $target     = '_blank';
				break;

			case 'pro':
			case 'agency':
			    $title     = __( 'Related extension is not active', 'searchwp' );
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
								__( 'Enhance your readers\' experience with SearchWP Related extension. Seamlessly weave related articles and posts into your content, captivating your audience and driving them to explore more! <a href="%s" target="_blank">View&nbsp;Docs</a>', 'searchwp' ),
								'https://searchwp.com/extensions/related/'
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
                        <li><?php esc_html_e( 'Increased engagement', 'searchwp' ); ?></li>
                        <li><?php esc_html_e( 'Cross-promotion', 'searchwp' ); ?></li>
                        <li><?php esc_html_e( 'Discoverability of older content', 'searchwp' ); ?></li>
                    </ul>
                    <ul>
                        <li><?php esc_html_e( 'Personalization options', 'searchwp' ); ?></li>
                        <li><?php esc_html_e( 'Flexible integration', 'searchwp' ); ?></li>
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
	 * Enqueue assets for the Related preview screen.
	 *
	 * @since 4.3.10
	 */
	public static function assets() {

		$handle = SEARCHWP_PREFIX . self::$slug . '_preview';

		wp_enqueue_style(
			SEARCHWP_PREFIX . 'choicesjs',
			SEARCHWP_PLUGIN_URL . 'assets/vendor/choicesjs/css/choices-10.2.0.min.css',
			[],
			'10.2.0'
		);

		wp_enqueue_style(
			$handle,
			SEARCHWP_PLUGIN_URL . 'assets/css/admin/pages/related-preview.css',
			[
				Utils::$slug . 'choicesjs',
				Utils::$slug . 'collapse-layout',
				Utils::$slug . 'input',
				Utils::$slug . 'modal',
				Utils::$slug . 'toggle-switch',
				Utils::$slug . 'radio-img',
				Utils::$slug . 'style',
			],
			SEARCHWP_VERSION
		);
	}

	/**
     * Output the content preview for the Related preview screen.
     *
     * @since 4.3.10
	 *
	 * @return void
	 */
    public static function render_preview() {
        ?>
        <div id="searchwp-related-preview">
            <div class="extension-preview-wrap">
                <div class="wrap" id="searchwp-related-wrapper">
                    <div class="related-container">
                        <h2>SearchWP Related</h2>
                        <div class="swp-content-container">
                            <div class="swp-page-header">
                                <div class="swp-flex--row sm:swp-flex--col sm:swp-flex--gap30 swp-justify-between swp-flex--align-c sm:swp-flex--align-start">
                                    <div class="swp-flex--row swp-flex--gap15 swp-flex--align-c">
                                        <h1 class="swp-h1 swp-page-header--h1"> Settings </h1>
                                    </div>
                                    <div class="swp-flex--row swp-flex--gap15 swp-flex--grow0">
                                        <button type="button" class="swp-button swp-button--flex-content">
                                            <svg width="13" height="8" viewBox="0 0 13 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M4 7.84615L0.25 3.99999L4 0.153839L4.89062 1.0673L2.01562 4.01602L4.875 6.94871L4 7.84615ZM9 7.84615L8.10938 6.93269L10.9844 3.98397L8.125 1.05128L9 0.153839L12.75 3.99999L9 7.84615Z" fill="#0E2121" fill-opacity="0.8"></path>
                                            </svg>
                                            Embed
                                        </button>
                                        <button type="button" id="swp-results-page-save" class="swp-button swp-button--green">Save</button>
                                    </div>
                                </div>
                            </div>
                            <div class="swp-collapse swp-opened">
                                <div class="swp-collapse--header">
                                    <h2 class="swp-h2"> Layout Template </h2>
                                    <button class="swp-expand--button">
                                        <svg class="swp-arrow" width="17" height="11" viewBox="0 0 17 11" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M14.2915 0.814362L8.09717 6.95819L1.90283 0.814362L0 2.7058L8.09717 10.7545L16.1943 2.7058L14.2915 0.814362Z" fill="#0E2121" fill-opacity="0.8"></path>
                                        </svg>
                                    </button>
                                </div>
                                <div class="swp-collapse--content">
                                    <div class="swp-row">
                                        Layout Themes help quickly set the style of the Related block. Fine-tune the appearance by
                                        customizing things like the layout, and maximum number of results. List the post IDs that should
                                        never appear as related items.
                                    </div>
                                    <div class="swp-row">
                                        <div class="swp-flex--row sm:swp-flex--col sm:swp-flex--gap30">
                                            <div class="swp-col swp-col--title-width--sm">
                                                <h3 class="swp-h3"> Layout Theme </h3>
                                            </div>
                                            <div class="swp-col">
                                                <div class="swp-flex--row swp-flex--gap20 swp-sf--layout-themes">
                                                    <div class="swp-flex--grow1 swp-input--radio-img">
                                                        <input type="radio" id="swp-alpha-theme" name="swp-layout-theme" value="alpha">
                                                        <label for="swp-alpha-theme">
                                                            <img src="<?php echo esc_url( SEARCHWP_PLUGIN_URL . 'assets/images/admin/pages/extensions-preview/related/alpha.svg' ); ?>" alt="">
                                                            Minimal
                                                        </label>
                                                    </div>
                                                    <div class="swp-flex--grow1 swp-input--radio-img">
                                                        <input type="radio" id="swp-beta-theme" name="swp-layout-theme" value="beta">
                                                        <label for="swp-beta-theme">
                                                            <img src="<?php echo esc_url( SEARCHWP_PLUGIN_URL . 'assets/images/admin/pages/extensions-preview/related/beta.svg' ); ?>" alt="">
                                                            Columns </label>
                                                    </div>
                                                    <div class="swp-flex--grow1 swp-input--radio-img">
                                                        <input type="radio" id="swp-gamma-theme" name="swp-layout-theme" value="gamma">
                                                        <label for="swp-gamma-theme">
                                                            <img src="<?php echo esc_url( SEARCHWP_PLUGIN_URL . 'assets/images/admin/pages/extensions-preview/related/gamma.svg' ); ?>" alt="">
                                                            Medium
                                                        </label>
                                                    </div>
                                                    <div class="swp-flex--grow1 swp-input--radio-img">
                                                        <input type="radio" id="swp-epsilon-theme" name="swp-layout-theme" value="epsilon" checked="checked">
                                                        <label for="swp-epsilon-theme">
                                                            <img src="<?php echo esc_url( SEARCHWP_PLUGIN_URL . 'assets/images/admin/pages/extensions-preview/related/epsilon.svg' ); ?>" alt="">
                                                            Thumbnails
                                                        </label>
                                                    </div>
                                                    <div class="swp-flex--grow1 swp-input--radio-img">
                                                        <input type="radio" id="swp-zeta-theme" name="swp-layout-theme" value="zeta">
                                                        <label for="swp-zeta-theme">
                                                            <img src="<?php echo esc_url( SEARCHWP_PLUGIN_URL . 'assets/images/admin/pages/extensions-preview/related/zeta.svg' ); ?>" alt="">
                                                            Rich
                                                        </label>
                                                    </div>
                                                    <div class="swp-flex--grow1 swp-input--radio-img">
                                                        <input type="radio" id="swp-combined-theme" name="swp-layout-theme" value="combined">
                                                        <label for="swp-combined-theme">
                                                            <img src="<?php echo esc_url( SEARCHWP_PLUGIN_URL . 'assets/images/admin/pages/extensions-preview/related/combined.svg' ); ?>" alt="">
                                                            Combined
                                                        </label>
                                                    </div>
                                                </div>
                                                <h4 class="swp-h4 swp-margin-t30"> Theme Preview </h4>
                                                <div class="swp-rp-theme-preview">
                                                    <div class="swp-grid swp-grid--cols-3 swp-rp--img-l swp-result-item--desc--off">
                                                        <div class="swp-result-item">
                                                            <div class="swp-result-item--img-container">
                                                                <div class="swp-result-item--img">
                                                                    <img src="<?php echo esc_url( SEARCHWP_PLUGIN_URL . 'assets/images/admin/pages/extensions-preview/related/cracker001.jpg' ); ?>" alt="">
                                                                </div>
                                                            </div>
                                                            <div class="swp-result-item--info-container">
                                                                <h2 class="swp-result-item--h2">
                                                                    <a class="swp-a" role="link" aria-disabled="true"> New crackers recipe available! </a>
                                                                </h2>
                                                                <p class="swp-result-item--desc">
                                                                    Crispy, crunchy multigrain crackers, loaded with seeds. Add
                                                                    delicious crunch to your day!
                                                                </p>
                                                            </div>
                                                        </div>
                                                        <div class="swp-result-item">
                                                            <div class="swp-result-item--img-container">
                                                                <div class="swp-result-item--img">
                                                                    <img src="<?php echo esc_url( SEARCHWP_PLUGIN_URL . 'assets/images/admin/pages/extensions-preview/related/cracker002.jpg' ); ?>" alt="">
                                                                </div>
                                                            </div>
                                                            <div class="swp-result-item--info-container">
                                                                <h2 class="swp-result-item--h2">
                                                                    <a class="swp-a" role="link" aria-disabled="true">Fresh avocado crackers recipe!</a>
                                                                </h2>
                                                                <p class="swp-result-item--desc">
                                                                    Classic multigrain crackers, featuring a fresh avocado twist.
                                                                    Elevate your snacking experience with this delicious
                                                                    combination!
                                                                </p>
                                                            </div>
                                                        </div>
                                                        <div class="swp-result-item">
                                                            <div class="swp-result-item--img-container">
                                                                <div class="swp-result-item--img">
                                                                    <img src="<?php echo esc_url( SEARCHWP_PLUGIN_URL . 'assets/images/admin/pages/extensions-preview/related/cracker003.jpg' ); ?>" alt="">
                                                                </div>
                                                            </div>
                                                            <div class="swp-result-item--info-container">
                                                                <h2 class="swp-result-item--h2">
                                                                    <a class="swp-a" role="link" aria-disabled="true">Multigrain crackers with banana topping!</a>
                                                                </h2>
                                                                <p class="swp-result-item--desc">
                                                                    Perfect blend of crunchy multigrain crackers, enriched with seeds
                                                                    and topped with sweet bananas.
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="swp-row">
                                        <div class="swp-flex--row sm:swp-flex--col sm:swp-flex--gap30">
                                            <div class="swp-col swp-col--title-width--sm">
                                                <h3 class="swp-h3"> Layout Style </h3>
                                            </div>
                                            <div class="swp-col">
                                                <div class="swp-flex--col swp-flex--gap30">
                                                    <div class="swp-flex--row swp-flex--gap12">
                                                        <div class="swp-input--radio-img">
                                                            <input type="radio" name="swp-layout-style" id="swp-layout-style-grid" value="grid" checked="checked">
                                                            <label for="swp-layout-style-grid">
                                                                <img src="<?php echo esc_url( SEARCHWP_PLUGIN_URL . 'assets/images/admin/pages/extensions-preview/related/columns-layout.svg' ); ?>" alt="">
                                                                Columns
                                                            </label>
                                                        </div>
                                                        <div class="swp-input--radio-img">
                                                            <input type="radio" name="swp-layout-style" id="swp-layout-style-list" value="list">
                                                            <label for="swp-layout-style-list">
                                                                <img src="<?php echo esc_url( SEARCHWP_PLUGIN_URL . 'assets/images/admin/pages/extensions-preview/related/list-layout.svg' ); ?>" alt="">
                                                                List
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="swp-flex--col swp-flex--gap25">
                                                        <p class="swp-desc"> Choose the maximum number of results </p>
                                                        <div class="swp-inputbox-horizontal">
                                                            <div class="swp-w-1/6">
                                                                <input type="number" min="0" class="swp-input swp-w-full" name="swp-max-results" value="3">
                                                            </div>
                                                            <label for="" class="swp-label"> Results </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="swp-row">
                                        <div class="swp-flex--col swp-flex--gap30">
                                            <div class="swp-flex--row sm:swp-flex--col sm:swp-flex--gap30">
                                                <div class="swp-col swp-col--title-width--sm">
                                                    <h3 class="swp-h3"> Block Title </h3>
                                                </div>
                                                <div class="swp-col">
                                                    <input class="swp-input swp-w-1/4" type="text" name="swp-block-title" value="Related Content" placeholder="Related Content">
                                                </div>
                                            </div>
                                            <div class="swp-flex--row sm:swp-flex--col sm:swp-flex--gap30">
                                                <div class="swp-col swp-col--title-width--sm">
                                                    <h3 class="swp-h3"> Images </h3>
                                                </div>
                                                <div class="swp-col">
                                                    <div class="swp-w-1/4">
                                                        <div class="choices" data-type="select-one">
                                                            <div class="choices__inner">Large</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="swp-flex--row sm:swp-flex--col sm:swp-flex--gap30">
                                                <div class="swp-col swp-col--title-width--sm">
                                                    <h3 class="swp-h3"> Descriptions </h3>
                                                </div>
                                                <div class="swp-col">
                                                    <label class="swp-toggle">
                                                        <input class="swp-toggle-checkbox" type="checkbox" name="swp-description-enabled">
                                                        <div class="swp-toggle-switch"></div>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="swp-row">
                                        <div class="swp-flex--row sm:swp-flex--col sm:swp-flex--gap30">
                                            <div class="swp-col swp-col--title-width--sm">
                                                <h3 class="swp-h3"> Engine </h3>
                                            </div>
                                            <div class="swp-col">
                                                <div class="swp-w-1/4">
                                                    <div class="choices" data-type="select-one">
                                                        <div class="choices__inner">Default</div>
                                                        <div class="choices__list choices__list--dropdown">
                                                            <div class="choices__list" role="listbox">
                                                                <div id="choices--swp-engine-k3-item-choice-1" class="choices__item choices__item--choice is-selected choices__item--selectable is-highlighted">Default</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="swp-row">
                                        <div class="swp-flex--row sm:swp-flex--col sm:swp-flex--gap30">
                                            <div class="swp-col swp-col--title-width--sm">
                                                <h3 class="swp-h3"> Auto-append </h3>
                                            </div>
                                            <div class="swp-col">
                                                <div class="swp-flex--col swp-flex--gap25">
                                                    <div class="swp-flex--col swp-flex--gap17">
                                                        <label class="swp-label" for="swp-qs-place">
                                                            Select what post types get Related block auto-appended to the
                                                            content.
                                                        </label>
                                                        <div class="swp-w-1/2">
                                                            <div class="choices">
                                                                <div class="choices__inner">
                                                                    <input type="search" name="search_terms" class="choices__input choices__input--cloned" style="min-width: 1ch; width: 1ch;">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="swp-flex--col swp-flex--gap17">
                                                        <label class="swp-label" for="swp-qs-place"> Choose where in the post body the related content will be placed. </label>
                                                        <div class="swp-flex--row swp-flex--gap17">
                                                            <div class="swp-input--radio-embed">
                                                                <input type="radio" name="swp-auto-append-logic"
                                                                       id="swp-auto-append-bottom" value="bottom" checked="checked">
                                                                <label for="swp-auto-append-bottom"> Bottom </label>
                                                            </div>
                                                            <div class="swp-input--radio-embed">
                                                                <input type="radio" id="swp-auto-append-after-paragraph">
                                                                <label for="swp-auto-append-after-paragraph"> After Specific Paragraph </label>
                                                            </div>
                                                        </div>
                                                        <div id="swp-append-after-paragraph-num-block" class="swp-inputbox-horizontal" style="display: none;">
                                                            <label for="" class="swp-label"> After </label>
                                                            <div class="swp-w-1/6">
                                                                <input type="number" min="0" class="swp-input swp-w-full" name="swp-append-after-paragraph-num" value="2">
                                                            </div>
                                                            <label for="" class="swp-label"> Paragraph </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="swp-row">
                                        <div class="swp-flex--row sm:swp-flex--col sm:swp-flex--gap30">
                                            <div class="swp-col swp-col--title-width--sm">
                                                <h3 class="swp-h3"> Exclude Entries </h3>
                                            </div>
                                            <div class="swp-col">
                                                <div class="swp-flex--col swp-flex--gap30">
                                                    <div class="swp-flex--col swp-flex--gap25">
                                                        <p class="swp-desc"> Choose the exclude logic and enter the IDs to limit the results. </p>
                                                        <div class="swp-w-1/3">
                                                            <div class="choices" data-type="select-one">
                                                                <div class="choices__inner">
                                                                    <div class="choices__list choices__list--single">
                                                                        <div class="choices__item choices__item--selectable">Exclude selected IDs:</div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div id="swp-post-exclude-ids-not-in-block" class="swp-flex--col swp-flex--gap17 swp-w-1/3">
                                                            <p class="swp-desc"> Exclude these IDs: </p>
                                                            <div class="choices" data-type="select-multiple">
                                                                <div class="choices__inner">
                                                                    <input type="search" name="search_terms" class="choices__input choices__input--cloned" style="min-width: 1ch; width: 1ch;"></div>
                                                                <div class="choices__list choices__list--dropdown" aria-expanded="false">
                                                                    <div class="choices__list" aria-multiselectable="true" role="listbox">
                                                                        <div class="choices__item choices__item--choice has-no-choices">Type to add new items</div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
}
