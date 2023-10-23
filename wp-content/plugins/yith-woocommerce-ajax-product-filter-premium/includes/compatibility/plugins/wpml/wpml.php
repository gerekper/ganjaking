<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * WPML plugin support
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\AjaxProductFilter\Classes\Compatibility
 * @version 4.0.1
 */

if ( ! defined( 'YITH_WCAN' ) ) {
	exit;
} // Exit if accessed directly

/**
 * Class that adds WPML Metabox to preset view
 *
 * @since 4.0.3
 */
class WPML_Language_Metabox {
	/**
	 * Instance of Sitepress.
	 *
	 * @var \SitePress
	 */
	private $sitepress;

	/**
	 * Instance of $wpml_post_translations.
	 *
	 * @var \WPML_post_translation
	 */
	private $wpml_post_translations;

	/**
	 * Panel page
	 *
	 * @var string
	 */
	private $panel_slug;

	/**
	 * Language_Metabox constructor.
	 *
	 * @param \SitePress             $sitepress              An instance of SitePress class.
	 * @param \WPML_post_translation $wpml_post_translations An instance of WPML_post_translation class.
	 */
	public function __construct( \SitePress $sitepress, \WPML_post_translation $wpml_post_translations ) {
		$this->sitepress              = $sitepress;
		$this->wpml_post_translations = $wpml_post_translations;
	}

	/**
	 * Adds the actions and filters.
	 */
	public function add_hooks() {
		add_filter( 'wpml_enable_language_meta_box', array( $this, 'wpml_enable_language_meta_box_filter' ) );
		add_action( 'yith_wcan_preset_edit_after_filters', array( $this, 'add_language_meta_box' ) );
		add_filter( 'wpml_link_to_translation', array( $this, 'link_to_translation' ), 10, 4 );
		add_filter( 'wpml_admin_language_switcher_items', array( $this, 'admin_language_switcher_items' ) );
		add_action( 'icl_make_duplicate', array( $this, 'fix_duplicated_preset' ), 10, 4 );
		add_action( 'yith_wcan_save_preset', array( $this, 'process_save_post' ), 10, 2 );
	}

	/**
	 * Save WPML meta box when saving the preset
	 *
	 * @param int              $preset_id Preset id.
	 * @param YITH_WCAN_Preset $preset    Preset object.
	 *
	 * @return void
	 */
	public function process_save_post( $preset_id, $preset ) {
		global $wpml_post_translations;

		$wpml_post_translations->save_post_actions( $preset_id, $preset->get_post() );
	}

	/**
	 * Enable metabox for preset post type
	 *
	 * @param bool $enable Whether to show metabox or not.
	 *
	 * @return bool Whether to show metabox or not.
	 */
	public function wpml_enable_language_meta_box_filter( $enable ) {
		if ( $this->is_preset_page() ) {
			$enable = true;
		}

		return $enable;
	}

	/**
	 * Add the WPML meta box when editing forms.
	 *
	 * @param int|\WP_Post $post The post ID or an instance of WP_Post.
	 */
	public function add_language_meta_box( $post ) {
		$post = get_post( $post );
		$trid = filter_input( INPUT_GET, 'trid', FILTER_SANITIZE_NUMBER_INT );

		?>
		<div id="<?php echo esc_attr( WPML_Meta_Boxes_Post_Edit_HTML::WRAPPER_ID ); ?>">
			<div class="inside">
				<?php
				if ( $post ) {
					add_filter( 'wpml_post_edit_can_translate', '__return_true' );
					$this->sitepress->meta_box( $post );
				} elseif ( $trid ) {
					// Used by WPML for connecting new manual translations to their originals.
					echo '<input type="hidden" name="icl_trid" value="' . esc_attr( $trid ) . '" />';
				}
				?>
			</div>
		</div>
		<?php
	}

	/**
	 * Filters links to translations in language metabox.
	 *
	 * @param string $link    Link to translation.
	 * @param int    $post_id Post id.
	 * @param string $lang    Destination language.
	 * @param int    $trid    Translated post id.
	 *
	 * @return string
	 */
	public function link_to_translation( $link, $post_id, $lang, $trid ) {
		if ( YITH_WCAN_Presets()->get_post_type() === get_post_type( $post_id ) ) {
			$link = $this->get_link_to_translation( $post_id, $lang );
		}

		return $link;
	}

	/**
	 * Filters the top bar admin language switcher links.
	 *
	 * @param array $links Action links.
	 *
	 * @return array $links
	 */
	public function admin_language_switcher_items( $links ) {
		$preset = filter_input( INPUT_GET, 'preset', FILTER_SANITIZE_NUMBER_INT );
		$trid   = filter_input( INPUT_GET, 'trid', FILTER_SANITIZE_NUMBER_INT );

		if ( $this->is_preset_page() && ( $preset || $trid ) ) {
			// If we are adding a post, get the post_id from the trid and source_lang.
			if ( ! $preset ) {
				$source_lang = filter_input( INPUT_GET, 'source_lang', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
				$preset      = $this->wpml_post_translations->get_element_id( $source_lang, $trid );
				unset( $links['all'] );
				// We shouldn't get here, but just in case.
				if ( ! $preset ) {
					return $links;
				}
			}

			foreach ( $links as $lang => & $link ) {
				if ( 'all' !== $lang && ! $link['current'] ) {
					$link['url'] = $this->get_link_to_translation( $preset, $lang );
				}
			}
		}

		return $links;
	}

	/**
	 * Use translated terms for translated preset
	 *
	 * @param int    $original_id Original preset id.
	 * @param string $lang        Destination language.
	 * @param array  $post_array  Array of post data.
	 * @param int    $trid        Translated preset id.
	 */
	public function fix_duplicated_preset( $original_id, $lang, $post_array, $trid ) {
		$preset = yith_wcan_get_preset( $trid );

		if ( ! $preset ) {
			return;
		}

		// cycle through filters.
		if ( $preset->has_filters() ) {
			$filters     = $preset->get_filters();
			$raw_filters = array();

			foreach ( $filters as $filter ) {
				if ( $filter->has_terms() ) {
					$terms_options = $filter->get_terms_options();
					$new_terms     = array();

					foreach ( $terms_options as $term_id => $term_options ) {
						$translated_term_id = apply_filters( 'wpml_object_id', $term_id, $filter->get_taxonomy(), false, $lang );

						if ( ! $translated_term_id ) {
							continue;
						}

						$new_terms[ $translated_term_id ] = $term_options;
					}

					$filter->set_terms( $new_terms );
				}

				$raw_filters[ $filter->get_id() ] = $filter->get_data();
			}

			$preset->set_filters( $raw_filters );
			$preset->save();
		}
	}

	/**
	 * Returns slug for the presets panel
	 *
	 * @return string Panel slug.
	 */
	public function get_panel_slug() {
		if ( empty( $this->panel_slug ) ) {
			$this->panel_slug = YITH_WCAN()->admin->get_panel_page();
		}

		return $this->panel_slug;
	}

	/**
	 * Check if we are in YITH_WCAN preset page.
	 *
	 * @return int
	 */
	private function is_preset_page() {
		if ( empty( $this->panel_slug ) ) {
			$this->panel_slug = YITH_WCAN()->admin->get_panel_page();
		}

		return ! empty( $_GET['page'] ) && ! empty( $_GET['preset'] ) && $this->get_panel_slug() === $_GET['page']; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	}

	/**
	 * Works out the correct link to a translation
	 *
	 * @param int    $post_id The post_id being edited.
	 * @param string $lang    The target language.
	 *
	 * @return string
	 */
	private function get_link_to_translation( $post_id, $lang ) {
		$translated_post_id = $this->wpml_post_translations->element_id_in( $post_id, $lang );

		if ( $translated_post_id ) {
			// Rewrite link to edit contact form translation.
			$args = array(
				'action' => 'edit',
				'lang'   => $lang,
				'preset' => $translated_post_id,
				'page'   => $this->get_panel_slug(),
				'tab'    => 'filter-preset',
			);
		} else {
			// Rewrite link to create contact form translation.
			$trid                 = $this->wpml_post_translations->get_element_trid( $post_id, YITH_WCAN_Presets()->get_post_type() );
			$source_language_code = $this->wpml_post_translations->get_element_lang_code( $post_id );

			$args = array(
				'action'      => 'create',
				'lang'        => $lang,
				'trid'        => $trid,
				'source_lang' => $source_language_code,
				'page'        => $this->get_panel_slug(),
				'tab'         => 'filter-preset',
			);
		}

		return add_query_arg( $args, admin_url( 'admin.php' ) );
	}
}

if ( is_admin() ) {
	global $sitepress, $wpml_post_translations;

	$metabox = new WPML_Language_Metabox( $sitepress, $wpml_post_translations );
	$metabox->add_hooks();
}
