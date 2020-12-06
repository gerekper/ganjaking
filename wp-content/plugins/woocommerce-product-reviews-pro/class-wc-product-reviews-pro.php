<?php
/**
 * WooCommerce Product Reviews Pro
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Product Reviews Pro to newer
 * versions in the future. If you wish to customize WooCommerce Product Reviews Pro for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-product-reviews-pro/ for more information.
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2015-2020, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;

/**
 * WooCommerce Product Reviews Pro Main Plugin Class.
 *
 * @since 1.0.0
 */
class WC_Product_Reviews_Pro extends Framework\SV_WC_Plugin {


	/** plugin version number */
	const VERSION = '1.17.0';

	/** @var WC_Product_Reviews_Pro single instance of this plugin */
	protected static $instance;

	/** plugin id */
	const PLUGIN_ID = 'product_reviews_pro';

	/** plugin meta prefix */
	const PLUGIN_PREFIX = 'wc_product_reviews_pro_';

	/** @var \WC_Product_Reviews_Pro_Admin instance */
	protected $admin;

	/** @var \WC_Product_Reviews_Pro_Frontend instance */
	protected $frontend;

	/** @var \WC_Product_Reviews_Pro_AJAX instance */
	protected $ajax;

	/** @var \WC_Product_Reviews_Pro_Review_Qualifiers instance */
	protected $review_qualifiers;

	/** @var \WC_Product_Reviews_Pro_Contribution_Factory instance */
	protected $contribution_factory;

	/** @var \WC_Product_Reviews_Pro_Query instance */
	protected $query;

	/** @var \WC_Product_Reviews_Pro_Emails instance */
	private $emails;

	/** @var \WC_Product_Reviews_Pro_Widgets instance */
	private $widgets;

	/** @var \WC_Product_Reviews_Pro_Integrations instance */
	private $integrations;


	/**
	 * Initializes the plugin.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		parent::__construct(
			self::PLUGIN_ID,
			self::VERSION,
			array(
				'text_domain' => 'woocommerce-product-reviews-pro',
			)
		);

		// make sure template files are searched for in our plugin
		add_filter( 'woocommerce_locate_template',      array( $this, 'locate_template' ), 20, 3 );
		add_filter( 'woocommerce_locate_core_template', array( $this, 'locate_template' ), 20, 3 );

		// GDPR compliance: erase additional comment meta data when a comment is anonymized
		add_filter( 'wp_anonymize_comment', array( $this, 'erase_contribution_personal_data' ), 40, 3 );
	}


	/**
	 * Initializes the lifecycle handler.
	 *
	 * @since 1.13.0
	 */
	protected function init_lifecycle_handler() {

		require_once( $this->get_plugin_path() . '/includes/Lifecycle.php' );

		$this->lifecycle_handler = new \SkyVerge\WooCommerce\Product_Reviews_Pro\Lifecycle( $this );
	}


	/**
	 * Loads the plugin handlers and initializes the plugin.
	 *
	 * @internal
	 *
	 * @since 1.13.0
	 */
	public function init_plugin() {

		// these need to be included here rather than in includes() or calls to wc_product_reviews_pro() get stuck in a loop
		require_once( $this->get_plugin_path() . '/includes/functions/wc-product-reviews-pro-functions.php' );
		require_once( $this->get_plugin_path() . '/includes/class-wc-product-reviews-pro-products.php' );

		// query handler
		$this->query = $this->load_class( '/includes/class-wc-product-reviews-pro-query.php', 'WC_Product_Reviews_Pro_Query' );

		// emails handler
		$this->emails = $this->load_class( '/includes/class-wc-product-reviews-pro-emails.php', 'WC_Product_Reviews_Pro_Emails' );

		// main objects handlers
		$this->review_qualifiers    = $this->load_class( '/includes/class-wc-product-reviews-pro-review-qualifiers.php', 'WC_Product_Reviews_Pro_Review_Qualifiers' );
		$this->contribution_factory = $this->load_class( '/includes/class-wc-product-reviews-pro-contribution-factory.php', 'WC_Product_Reviews_Pro_Contribution_Factory' );

		// frontend handler
		if ( ! is_admin() || is_ajax() ) {
			$this->frontend = $this->load_class( '/includes/frontend/class-wc-product-reviews-pro-frontend.php', 'WC_Product_Reviews_Pro_Frontend' );
		}

		// admin includes
		if ( is_admin() && ! is_ajax() ) {
			$this->admin = $this->load_class( '/includes/admin/class-wc-product-reviews-pro-admin.php', 'WC_Product_Reviews_Pro_Admin' );
		}

		// ajax handler
		$this->ajax = $this->load_class( '/includes/class-wc-product-reviews-pro-ajax.php', 'WC_Product_Reviews_Pro_AJAX' );

		// widgets handler
		$this->widgets = $this->load_class( '/includes/class-wc-product-reviews-pro-widgets.php', 'WC_Product_Reviews_Pro_Widgets' );

		// integrations handler
		$this->integrations = $this->load_class( '/includes/integrations/class-wc-product-reviews-pro-integrations.php', 'WC_Product_Reviews_Pro_Integrations' );
	}


	/**
	 * Returns the Admin handler instance.
	 *
	 * @since 1.6.0
	 *
	 * @return \WC_Product_Reviews_Pro_Admin
	 */
	public function get_admin_instance() {

		return $this->admin;
	}


	/**
	 * Returns the Frontend handler instance.
	 *
	 * @since 1.6.0
	 *
	 * @return \WC_Product_Reviews_Pro_Frontend
	 */
	public function get_frontend_instance() {

		return $this->frontend;
	}


	/**
	 * Returns the AJAX handler instance.
	 *
	 * @since 1.6.0
	 *
	 * @return \WC_Product_Reviews_Pro_AJAX
	 */
	public function get_ajax_instance() {

		return $this->ajax;
	}


	/**
	 * Returns the Review Qualifiers instance.
	 *
	 * @since 1.6.0
	 *
	 * @return \WC_Product_Reviews_Pro_Review_Qualifiers
	 */
	public function get_review_qualifiers_instance() {

		return $this->review_qualifiers;
	}


	/**
	 * Returns the Contribution Factory instance.
	 *
	 * @since 1.6.0
	 *
	 * @return \WC_Product_Reviews_Pro_Contribution_Factory
	 */
	public function get_contribution_factory_instance() {

		return $this->contribution_factory;
	}


	/**
	 * Returns the Query handler instance.
	 *
	 * @since 1.6.0
	 *
	 * @return \WC_Product_Reviews_Pro_Query
	 */
	public function get_query_instance() {

		return $this->query;
	}


	/**
	 * Returns the Emails handler instance.
	 *
	 * @since 1.10.0
	 *
	 * @return \WC_Product_Reviews_Pro_Emails
	 */
	public function get_emails_instance() {

		return $this->emails;
	}


	/**
	 * Returns the Widgets handler instance.
	 *
	 * @since 1.10.0
	 *
	 * @return \WC_Product_Reviews_Pro_Widgets
	 */
	public function get_widgets_instance() {

		return $this->widgets;
	}


	/**
	 * Returns the Integrations handler instance.
	 *
	 * @since 1.10.0
	 *
	 * @return \WC_Product_Reviews_Pro_Integrations
	 */
	public function get_integrations_instance() {

		return $this->integrations;
	}


	/**
	 * Locates the WooCommerce template files from Product Reviews Pro templates directory.
	 *
	 * @internal
	 *
	 * @since 1.10.0
	 *
	 * @param string $template already found template
	 * @param string $template_name searchable template name
	 * @param string $template_path template path
	 * @return string search result for the template
	 */
	public function locate_template( $template, $template_name, $template_path ) {

		// only keep looking if no custom theme template was found,
		// or if a default WooCommerce template was found
		if ( ! $template || Framework\SV_WC_Helper::str_starts_with( $template, WC()->plugin_path() ) ) {

			// set the path to our templates directory
			$plugin_path = $this->get_plugin_path() . '/templates/';

			// if a template is found, make it so
			if ( is_readable( $plugin_path . $template_name ) ) {
				$template = $plugin_path . $template_name;
			}
		}

		return $template;
	}


	/**
	 * Deletes additional contribution meta data when a request to anonymize a comment is issued in WordPress.
	 *
	 * GDPR compliance handler: WordPress anonymizes a comment, therefore we can just follow along and remove sensitive comment meta.
	 *
	 * @internal
	 *
	 * @since 1.11.1
	 *
	 * @param bool $erase whether the comment is being anonymized
	 * @param \WP_Comment $comment the comment object with personal data being erased
	 * @param array $anonymized_data array of anonymized data
	 * @return bool
	 */
	public function erase_contribution_personal_data( $erase, $comment, $anonymized_data ) {

		if (     $erase
			  && $comment instanceof WP_Comment
			  && isset( $anonymized_data['user_id'] )
			  && 0 === $anonymized_data['user_id']
			  && in_array( $comment->comment_type, wc_product_reviews_pro_get_contribution_types(), false ) ) {

			delete_comment_meta( $comment->comment_ID, 'attachment_type' );
			delete_comment_meta( $comment->comment_ID, 'attachment_id' );
			delete_comment_meta( $comment->comment_ID, 'attachment_url' );
		}

		return $erase;
	}


	/** Admin methods ******************************************************/


	/**
	 * Renders a notice for the user to read the docs before adding add-ons.
	 *
	 * @since 1.0.0
	 */
	public function add_admin_notices() {

		// show any dependency notices
		parent::add_admin_notices();

		$this->get_admin_notice_handler()->add_admin_notice(
			/* translators: Placeholders: %1$s opening <a> html tag - %2$s closing </a> html tag - %3$s opening <a> html tag - %4$s closing </a> html tag - %5$s opening <a> html tag - %6$s closing </a> html tag */
			sprintf(
				__( 'Thanks for installing Product Reviews Pro! Before getting started, please take a moment to %1$sread the documentation%2$s, configure %3$ssettings%4$s or %5$semails%6$s :) ', 'woocommerce-product-reviews-pro' ),
				'<a href="http://docs.woocommerce.com/document/woocommerce-product-reviews-pro/" target="_blank">',
				'</a>',
				'<a href="' . admin_url( 'admin.php?page=wc-settings&tab=products' ) . '">',
				'</a>',
				'<a href="' . admin_url( 'admin.php?page=wc-settings&tab=email&section=wc_product_reviews_pro_emails_new_comment' ) . '">',
				'</a>'
			),
			'read-the-docs-notice',
			array( 'always_show_on_settings' => false, 'notice_class' => 'updated' )
		);
	}


	/** Helper methods ******************************************************/


	/**
	 * Main Product Reviews Pro Instance, ensures only one instance is/can be loaded.
	 *
	 * @since 1.0.0
	 *
	 * @see \wc_product_reviews_pro()
	 *
	 * @return \WC_Product_Reviews_Pro
	 */
	public static function instance() {

		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}


	/**
	 * Returns the plugin name, localized.
	 *
	 * @since 1.0.0
	 *
	 * @return string the plugin name
	 */
	public function get_plugin_name() {

		return __( 'WooCommerce Product Reviews Pro', 'woocommerce-product-reviews-pro' );
	}


	/**
	 * Returns __FILE__.
	 *
	 * @since 1.0.0
	 *
	 * @return string the full path and filename of the plugin file
	 */
	protected function get_file() {

		return __FILE__;
	}


	/**
	 * Returns the URL to the settings page.
	 *
	 * @since 1.0.0
	 *
	 * @param string|null $_ unused
	 * @return string URL to the settings page
	 */
	public function get_settings_url( $_ = null ) {

		return admin_url( 'admin.php?page=wc-settings&tab=products' );
	}


	/**
	 * Returns true if we are on the settings page.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function is_plugin_settings() {

		return isset( $_GET['page'] ) && 'reviews' === $_GET['page'];
	}


	/**
	 * Returns the plugin documentation URL.
	 *
	 * @since 1.1.0
	 *
	 * @return string
	 */
	public function get_documentation_url() {

		return 'https://docs.woocommerce.com/document/woocommerce-product-reviews-pro/';
	}


	/**
	 * Returns the plugin support URL.
	 *
	 * @since 1.1.0
	 *
	 * @return string
	 */
	public function get_support_url() {

		return 'https://woocommerce.com/my-account/marketplace-ticket-form/';
	}


	/**
	 * Returns the plugin sales page URL.
	 *
	 * @since 1.13.0
	 *
	 * @return string
	 */
	public function get_sales_page_url() {

		return 'https://woocommerce.com/products/woocommerce-product-reviews-pro/';
	}


}


/**
 * Returns the One True Instance of Product Reviews Pro.
 *
 * @since 1.0.0
 *
 * @return \WC_Product_Reviews_Pro
 */
function wc_product_reviews_pro() {

	return WC_Product_Reviews_Pro::instance();
}
