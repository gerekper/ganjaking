<?php

namespace Yoast\WP\SEO\Integrations\Admin\Prominent_Words;

use WPSEO_Admin_Asset_Manager;
use WPSEO_Language_Utils;
use Yoast\WP\SEO\Actions\Indexation\Indexable_General_Indexation_Action;
use Yoast\WP\SEO\Actions\Indexation\Indexable_Post_Indexation_Action;
use Yoast\WP\SEO\Actions\Indexation\Indexable_Post_Type_Archive_Indexation_Action;
use Yoast\WP\SEO\Actions\Indexation\Indexable_Term_Indexation_Action;
use Yoast\WP\SEO\Actions\Prominent_Words\Content_Action;
use Yoast\WP\SEO\Conditionals\Admin_Conditional;
use Yoast\WP\SEO\Conditionals\Migrations_Conditional;
use Yoast\WP\SEO\Conditionals\Yoast_Tools_Page_Conditional;
use Yoast\WP\SEO\Helpers\Language_Helper;
use Yoast\WP\SEO\Helpers\Options_Helper;
use Yoast\WP\SEO\Integrations\Integration_Interface;
use Yoast\WP\SEO\Presenters\Admin\Prominent_Words\Indexation_List_Item_Presenter;
use Yoast\WP\SEO\Presenters\Admin\Prominent_Words\Indexation_Modal_Presenter;
use Yoast\WP\SEO\Routes\Indexable_Indexation_Route;
use Yoast\WP\SEO\Routes\Prominent_Words_Route;

/**
 * Class Indexation_Integration.
 *
 * @package Yoast\WP\SEO\Integrations\Admin\Prominent_Words
 */
class Indexation_Integration implements Integration_Interface {

	/**
	 * Amount of prominent words to index per indexable.
	 *
	 * @var int
	 */
	const PER_INDEXABLE_LIMIT = 20;

	/**
	 * Holds the content action.
	 *
	 * @var Content_Action
	 */
	protected $content_action;

	/**
	 * The post indexation action.
	 *
	 * @var Indexable_Post_Indexation_Action
	 */
	protected $post_indexation;

	/**
	 * The term indexation action.
	 *
	 * @var Indexable_Term_Indexation_Action
	 */
	protected $term_indexation;

	/**
	 * The post type archive indexation action.
	 *
	 * @var Indexable_Post_Type_Archive_Indexation_Action
	 */
	protected $post_type_archive_indexation;

	/**
	 * Represents the general indexation.
	 *
	 * @var Indexable_General_Indexation_Action
	 */
	protected $general_indexation;

	/**
	 * Represents the admin asset manager.
	 *
	 * @var WPSEO_Admin_Asset_Manager
	 */
	protected $asset_manager;

	/**
	 * Represents the options helper.
	 *
	 * @var Options_Helper
	 */
	protected $options;

	/**
	 * Represents the languahe helper.
	 *
	 * @var Language_Helper
	 */
	protected $language_helper;

	/**
	 * Holds the total amount of unindexed objects.
	 *
	 * @var int
	 */
	protected $total_unindexed;

	/**
	 * WPSEO_Premium_Prominent_Words_Recalculation constructor.
	 *
	 * @param WPSEO_Admin_Asset_Manager                     $asset_manager                The asset manager.
	 * @param Content_Action                                $content_action               The content action.
	 * @param Indexable_Post_Indexation_Action              $post_indexation              The post indexation action.
	 * @param Indexable_Term_Indexation_Action              $term_indexation              The term indexation action.
	 * @param Indexable_Post_Type_Archive_Indexation_Action $post_type_archive_indexation The archive indexation action.
	 * @param Indexable_General_Indexation_Action           $general_indexation           The general indexation action.
	 * @param Options_Helper                                $options                      The options helper.
	 * @param Language_Helper                               $language_helper              The language helper.
	 */
	public function __construct(
		WPSEO_Admin_Asset_Manager $asset_manager,
		Content_Action $content_action,
		Indexable_Post_Indexation_Action $post_indexation,
		Indexable_Term_Indexation_Action $term_indexation,
		Indexable_Post_Type_Archive_Indexation_Action $post_type_archive_indexation,
		Indexable_General_Indexation_Action $general_indexation,
		Options_Helper $options,
		Language_Helper $language_helper
	) {
		$this->asset_manager                = $asset_manager;
		$this->content_action               = $content_action;
		$this->post_indexation              = $post_indexation;
		$this->term_indexation              = $term_indexation;
		$this->post_type_archive_indexation = $post_type_archive_indexation;
		$this->general_indexation           = $general_indexation;
		$this->options                      = $options;
		$this->language_helper              = $language_helper;
	}

	/**
	 * Initializes the integration.
	 *
	 * This is the place to register hooks and filters.
	 *
	 * @return void
	 */
	public function register_hooks() {
		\add_action( 'wpseo_tools_overview_list_items', [ $this, 'render_indexation_list_item' ], 11 );
		\add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ], 10 );
	}

	/**
	 * Returns the conditionals based in which this loadable should be active.
	 *
	 * @return array
	 */
	public static function get_conditionals() {
		return [
			Admin_Conditional::class,
			Yoast_Tools_Page_Conditional::class,
			Migrations_Conditional::class,
		];
	}

	/**
	 * Enqueues the required scripts.
	 *
	 * @return void
	 */
	public function enqueue_scripts() {
		/*
		 * We aren't able to determine whether or not anything needs to happen at register_hooks,
		 * as indexable object types aren't registered yet. So we do most of our add_action calls here.
		 */
		if ( $this->get_total_unindexed() === 0 ) {
			$this->options->set( 'prominent_words_indexation_completed', true );

			return;
		}

		$this->options->set( 'prominent_words_indexation_completed', false );

		\add_action( 'admin_footer', [ $this, 'render_indexation_modal' ], 20 );

		$site_locale = \get_locale();
		$language    = WPSEO_Language_Utils::get_language( $site_locale );

		$indexation_data = [
			'amount'              => $this->get_total_unindexed(),
			'ids'                 => [
				'count'    => '#yoast-prominent-words-indexation-current-count',
				'progress' => '#yoast-prominent-words-indexation-progress-bar',
				'modal'    => 'yoast-prominent-words-indexation-wrapper',
				'message'  => '#yoast-prominent-words-indexation',
			],
			'restApi'             => [
				'root'      => \esc_url_raw( \rest_url() ),
				'endpoints' => [
					'prepare'        => Indexable_Indexation_Route::FULL_PREPARE_ROUTE,
					'posts'          => Indexable_Indexation_Route::FULL_POSTS_ROUTE,
					'terms'          => Indexable_Indexation_Route::FULL_TERMS_ROUTE,
					'archives'       => Indexable_Indexation_Route::FULL_POST_TYPE_ARCHIVES_ROUTE,
					'general'        => Indexable_Indexation_Route::FULL_GENERAL_ROUTE,
					'complete'       => Indexable_Indexation_Route::FULL_COMPLETE_ROUTE,
					'get_content'    => Prominent_Words_Route::FULL_GET_CONTENT_ROUTE,
					'complete_words' => Prominent_Words_Route::FULL_COMPLETE_ROUTE,
				],
				'nonce'     => \wp_create_nonce( 'wp_rest' ),
			],
			'message'             => [
				'indexingCompleted' => '<span class="wpseo-checkmark-ok-icon"></span>' . \esc_html__( 'Good job! All your internal linking suggestions are up to date. These suggestions appear alongside your content when you are writing or editing. We will notify you the next time you need to update your internal linking suggestions.', 'wordpress-seo-premium' ),
				'indexingFailed'    => \__( 'Something went wrong while calculating the internal linking suggestions of your site. Please try again later.', 'wordpress-seo-premium' ),
			],
			'l10n'                => [
				'calculationInProgress' => \__( 'Calculation in progress...', 'wordpress-seo-premium' ),
				'calculationCompleted'  => \__( 'Calculation completed.', 'wordpress-seo-premium' ),
				'calculationFailed'     => \__( 'Calculation failed, please try again later.', 'wordpress-seo-premium' ),
			],
			'locale'              => $site_locale,
			'language'            => $language,
			'prominentWords'      => [
				'endpoint'          => Prominent_Words_Route::FULL_SAVE_ROUTE,
				'perIndexableLimit' => self::PER_INDEXABLE_LIMIT,
			],
			'morphologySupported' => $this->language_helper->is_word_form_recognition_active( $language ),
		];

		\wp_localize_script( WPSEO_Admin_Asset_Manager::PREFIX . 'indexation', 'yoastProminentWordsIndexationData', $indexation_data );
		$this->asset_manager->enqueue_script( 'indexation' );
		$this->asset_manager->enqueue_style( 'admin-css' );
		\wp_enqueue_script( 'yoast-premium-prominent-words-indexation' );
	}

	/**
	 * Renders the indexation list item.
	 *
	 * @return void
	 */
	public function render_indexation_list_item() {
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Reason: Indexation_List_Item_Presenter::present is properly escaped.
		echo new Indexation_List_Item_Presenter( $this->get_total_unindexed() );
	}

	/**
	 * Renders the indexation modal.
	 *
	 * @return void
	 */
	public function render_indexation_modal() {
		\add_thickbox();

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Reason: Indexation_Modal_Presenter::present is properly escaped.
		echo new Indexation_Modal_Presenter( $this->get_total_unindexed() );
	}

	/**
	 * Returns the total number of unindexed objects.
	 *
	 * @return int The total amount of indexables to recalculate.
	 */
	protected function get_total_unindexed() {
		if ( $this->total_unindexed === null ) {
			$this->total_unindexed = $this->content_action->get_total_unindexed();

			// Count objects which have no indexable twice. Once to create the indexable and once to create the prominent words.
			$this->total_unindexed += ( 2 * $this->post_indexation->get_total_unindexed() );
			$this->total_unindexed += ( 2 * $this->term_indexation->get_total_unindexed() );
			$this->total_unindexed += ( 2 * $this->general_indexation->get_total_unindexed() );
			$this->total_unindexed += ( 2 * $this->post_type_archive_indexation->get_total_unindexed() );
		}

		return $this->total_unindexed;
	}
}
