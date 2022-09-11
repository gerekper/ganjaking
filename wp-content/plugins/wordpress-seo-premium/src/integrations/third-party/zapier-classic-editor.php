<?php

namespace Yoast\WP\SEO\Premium\Integrations\Third_Party;

use WP_Post;
use WPSEO_Admin_Utils;
use Yoast\WP\SEO\Integrations\Integration_Interface;
use Yoast\WP\SEO\Premium\Conditionals\Zapier_Enabled_Conditional;
use Yoast\WP\SEO\Premium\Helpers\Zapier_Helper;

/**
 * Class to manage the Zapier integration in the Classic editor.
 */
class Zapier_Classic_Editor implements Integration_Interface {

	/**
	 * The Zapier helper.
	 *
	 * @var Zapier_Helper
	 */
	protected $zapier_helper;

	/**
	 * Zapier constructor.
	 *
	 * @param Zapier_Helper $zapier_helper The Zapier helper.
	 */
	public function __construct( Zapier_Helper $zapier_helper ) {
		$this->zapier_helper = $zapier_helper;
	}

	/**
	 * Returns the conditionals based in which this loadable should be active.
	 *
	 * @return array
	 */
	public static function get_conditionals() {
		return [ Zapier_Enabled_Conditional::class ];
	}

	/**
	 * Initializes the integration.
	 *
	 * This is the place to register hooks and filters.
	 *
	 * @return void
	 */
	public function register_hooks() {
		\add_action( 'wpseo_publishbox_misc_actions', [ $this, 'add_publishbox_text' ] );
	}

	/**
	 * Adds the Zapier text to the Classic Editor publish box.
	 *
	 * @param WP_Post $post The current post object.
	 *
	 * @return void
	 */
	public function add_publishbox_text( WP_Post $post ) {
		if ( ! $this->zapier_helper->is_post_type_supported( $post->post_type )
			|| $this->zapier_helper->is_connected() ) {
			return;
		}
		?>
		<div class="misc-pub-section yoast yoast-seo-score yoast-zapier-text">
			<span class="yoast-logo svg"></span>
			<span>
			<?php
			\printf(
				/* translators: 1: Link start tag, 2: Yoast SEO, 3: Zapier, 4: Link closing tag. */
				\esc_html__( '%1$sConnect %2$s with %3$s%4$s to instantly share your published posts with 2000+ destinations such as Twitter, Facebook and more.', 'wordpress-seo-premium' ),
				'<a href="' . \esc_url( \admin_url( 'admin.php?page=wpseo_integrations' ) ) . '" target="_blank">',
				'Yoast SEO',
				'Zapier',
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- The content is already escaped.
				WPSEO_Admin_Utils::get_new_tab_message() . '</a>'
			);
			?>
			</span>
		</div>
		<?php
	}
}
