<?php defined( 'ABSPATH' ) || die( 'This script cannot be accessed directly.' );

if ( ! function_exists( 'groovy_menu_visual_composer_builder_support' ) ) {
	function groovy_menu_visual_composer_builder_support() {

		global $groovyMenuSettings;

		if ( ! empty( $groovyMenuSettings['nav_menu_data']['id'] ) ) {
			$current_menu_id = $groovyMenuSettings['nav_menu_data']['id'];
		}

		if ( ! empty( $current_menu_id ) && ! empty( $groovyMenuSettings['nav_menu_data']['data'][ $current_menu_id ] ) ) {
			$nav_menu_items = $groovyMenuSettings['nav_menu_data']['data'][ $current_menu_id ];
		}

		if ( empty( $nav_menu_items ) ) {
			return;
		}


		$menu_blocks = array();

		foreach ( $nav_menu_items as $nav_menu_item ) {
			if ( empty( $nav_menu_item->object ) || 'gm_menu_block' !== $nav_menu_item->object ) {
				continue;
			}

			// the array key eliminates duplicates.
			$menu_blocks[ $nav_menu_item->object_id ] = true;

		}

		$ids = array();

		// Add links to edit pages of Groovy Menu Block.
		foreach ( $menu_blocks as $block_id => $flag ) {
			if ( $flag ) {
				$ids[] = $block_id;
			}
		}

		$groovyMenuSettings['menu_block_list'] = $ids;

		wp_enqueue_script( 'vcv:assets:vendor:script' );
		wp_enqueue_script( 'vcv:assets:front:script' );
		wp_enqueue_script( 'vcv:assets:runtime:script' );

		if ( ! empty( $ids ) && class_exists( '\VisualComposer\Modules\Assets\EnqueueController' ) && class_exists( '\VisualComposer\Helpers\Request' ) ) {

			if ( ! class_exists( 'GroovyMenuVCVSupportEnqueueController' ) ) {
				/**
				 * Class GroovyMenuVCVSupportEnqueueController
				 */
				class GroovyMenuVCVSupportEnqueueController extends \VisualComposer\Modules\Assets\EnqueueController {

					/** Override enqueueVcvAssets with public
					 *
					 * @param $sourceIds
					 */
					public function enqueueVcvAssets( $sourceIds ) {
						if ( ! is_array( $sourceIds ) || empty( $sourceIds ) ) {
							$sourceIds = [ get_the_ID() ];
						}
						$this->enqueueAssetsVendorListener( $sourceIds );
					}
				}
			}

			$req           = new \VisualComposer\Helpers\Request();
			$vcv_enq_style = new GroovyMenuVCVSupportEnqueueController( $req );

			$vcv_enq_style->enqueueVcvAssets( $ids );
		}

		if ( ! empty( $ids ) && class_exists( '\VisualComposer\Modules\Assets\JsEnqueueController' ) && class_exists( '\VisualComposer\Helpers\Frontend' ) && class_exists( '\VisualComposer\Helpers\Options' )) {

			if ( ! class_exists( 'GroovyMenuVCVSupportJsEnqueueController' ) ) {
				/**
				 * Class GroovyMenuVCVSupportJsEnqueueController
				 */
				class GroovyMenuVCVSupportJsEnqueueController extends \VisualComposer\Modules\Assets\JsEnqueueController {

					/** @noinspection PhpMissingParentConstructorInspection
					 * @param \VisualComposer\Helpers\Frontend $frontendHelper
					 */
					public function __construct( \VisualComposer\Helpers\Frontend $frontendHelper ) {
						if (
							! $frontendHelper->isPreview()
							&& ! $frontendHelper->isPageEditable()
							&& (
								! is_admin() || $frontendHelper->isFrontend()
							)
						) {
							$this->wpAddAction( 'wp_print_scripts', 'enqueueHeadHtml', 200 );
							/** @see \VisualComposer\Modules\Assets\JsEnqueueController::enqueueFooterHtml */
							$this->wpAddAction( 'wp_print_footer_scripts', 'enqueueFooterHtml', 200 );
						}
					}

					/**
					 * Enqueue HTML or JS snippets in head.
					 *
					 * @param \VisualComposer\Helpers\Options $optionsHelper
					 */
					protected function enqueueHeadHtml( \VisualComposer\Helpers\Options $optionsHelper ) {
						global $groovyMenuSettings;

						if ( empty( $groovyMenuSettings['menu_block_list'] ) || ! is_array( $groovyMenuSettings['menu_block_list'] ) ) {
							return;
						}

						foreach ( $groovyMenuSettings['menu_block_list'] as $sourceId ) {
							$globalJs = '';
							$localJs  = '';

							if ( ! $this->globalJSHeadAdded ) {
								$globalJs                = $optionsHelper->get( 'settingsGlobalJsHead' );
								$this->globalJSHeadAdded = true;
							}
							if ( ! in_array( $sourceId, $this->localJsHeadEnqueueList, true ) ) {
								$this->localJsHeadEnqueueList[] = $sourceId;
								$localJs                        = get_post_meta( $sourceId, 'vcv-settingsLocalJsHead', true );
							}

							$this->printJs( $globalJs, $localJs, $sourceId, 'head' );
						}
					}

					/**
					 * Enqueue HTML or JS snippets in footer.
					 *
					 * @param \VisualComposer\Helpers\Options $optionsHelper
					 */
					protected function enqueueFooterHtml( \VisualComposer\Helpers\Options $optionsHelper ) {
						global $groovyMenuSettings;

						if ( empty( $groovyMenuSettings['menu_block_list'] ) || ! is_array( $groovyMenuSettings['menu_block_list'] ) ) {
							return;
						}

						foreach ( $groovyMenuSettings['menu_block_list'] as $sourceId ) {

							$globalJs = '';
							$localJs  = '';
							if ( ! $this->globalJSFooterAdded ) {
								$globalJs                  = $optionsHelper->get( 'settingsGlobalJsFooter' );
								$this->globalJSFooterAdded = true;
							}
							if ( ! in_array( $sourceId, $this->localJsFooterEnqueueList, true ) ) {
								$this->localJsFooterEnqueueList[] = $sourceId;
								$localJs                          = get_post_meta( $sourceId, 'vcv-settingsLocalJsFooter', true );
							}

							$this->printJs( $globalJs, $localJs, $sourceId, 'footer' );
						}
					}

				}
			}


			$helper_frontend = new \VisualComposer\Helpers\Frontend();
			$vcv_enq_js      = new GroovyMenuVCVSupportJsEnqueueController( $helper_frontend );

		}


	}
}


