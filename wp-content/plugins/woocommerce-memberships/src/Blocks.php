<?php
/**
 * WooCommerce Memberships
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Memberships to newer
 * versions in the future. If you wish to customize WooCommerce Memberships for your
 * needs please refer to https://docs.woocommerce.com/document/woocommerce-memberships/ for more information.
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2014-2022, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\Memberships;

use SkyVerge\WooCommerce\PluginFramework\v5_10_13 as Framework;

defined( 'ABSPATH' ) or exit;

/**
 * Blocks handler for the Gutenberg editor.
 *
 * @since 1.15.0
 */
class Blocks {


	/** @var string the minimum supported version of the block editor */
	private $min_block_editor_version = '6.2';

	/** @var bool whether the block editor is supported */
	private $has_block_editor;

	/** @var \SkyVerge\WooCommerce\Memberships\Blocks\Block[] array of block instances */
	private $blocks = [];


	/**
	 * Initializes blocks.
	 *
	 * @since 1.15.0
	 */
	public function __construct() {

		// register the Memberships block category
		if ( version_compare( get_bloginfo( 'version' ), '5.8', '<' ) ) {
			// @TODO remove this version compare and default to only using 'block_categories_all' when we drop support for WP versions below 5.8
			add_filter( 'block_categories', [ $this, 'add_memberships_block_category' ], 9 );
		} else {
			add_filter( 'block_categories_all', [ $this, 'add_memberships_block_category' ], 9 );
		}

		// register blocks
		add_action( 'init', [ $this, 'register_blocks' ] );
	}


	/**
	 * Gets the block editor version in use.
	 *
	 * Note: since WordPress 5.5 this may no longer be a valid semver string!
	 *
	 * @since 1.15.0
	 *
	 * @return string
	 */
	private function get_block_editor_version() {
		global $wp_scripts;

		if ( defined( 'GUTENBERG_VERSION' ) ) {
			$version = GUTENBERG_VERSION;
		} elseif ( isset( $wp_scripts->registered['wp-blocks']->ver ) ) {
			$version = $wp_scripts->registered['wp-blocks']->ver;
		} else {
			$version = '';
		}

		return $version;
	}


	/**
	 * Determines whether the block editor is supported
	 *
	 * @since 1.15.0
	 *
	 * @return bool
	 */
	private function is_block_editor_supported() {

		if ( null === $this->has_block_editor ) {

			$block_editor_version   = $this->get_block_editor_version();
			$this->has_block_editor = '' !== $block_editor_version
			                          && function_exists( 'register_block_type' )
			                          && function_exists( 'wp_set_script_translations' );

			// we may only use version compare with semver strings
			if ( $this->has_block_editor && false !== strpos( $block_editor_version, '.' ) ) {

				$this->has_block_editor = version_compare( $block_editor_version, $this->min_block_editor_version, '>=' );
			}
		}

		return $this->has_block_editor;
	}


	/**
	 * Adds a Memberships block category.
	 *
	 * @internal
	 *
	 * @since 1.15.0
	 *
	 * @param array $categories block categories
	 * @return array
	 */
	public function add_memberships_block_category( $categories ) {

		return ! $this->is_block_editor_supported() ? $categories : array_merge( $categories, [
			[
				'slug'  => 'woocommerce-memberships',
				'title' => __( 'Memberships', 'woocommerce-memberships' ),
			],
		] );
	}


	/**
	 * Registers blocks with the block editor.
	 *
	 * @internal
	 *
	 * @since 1.15.0
	 */
	public function register_blocks() {

		// bail if block editor not available
		if ( ! $this->is_block_editor_supported() ) {
			return;
		}

		// register blocks scripts & styles
		$this->register_blocks_scripts_styles();

		// blocks abstracts & interfaces
		require_once( wc_memberships()->get_plugin_path() . '/src/blocks/Block.php' );
		require_once( wc_memberships()->get_plugin_path() . '/src/blocks/Dynamic_Content_Block.php' );

		// initialize and register individual blocks
		$this->blocks = [
			'member-content'     => wc_memberships()->load_class( '/src/blocks/Member_Content.php', '\\SkyVerge\\WooCommerce\\Memberships\\Blocks\\Member_Content' ),
			'non-member-content' => wc_memberships()->load_class(  '/src/blocks/Non_Member_Content.php', '\\SkyVerge\\WooCommerce\\Memberships\\Blocks\\Non_Member_Content' ),
			'directory' => wc_memberships()->load_class(  '/src/blocks/Members_Directory.php', '\\SkyVerge\\WooCommerce\\Memberships\\Blocks\\Members_Directory' ),
		];
	}


	/**
	 * Gets registered blocks.
	 *
	 * @since 1.15.0
	 *
	 * @return array|Blocks\Block[]
	 */
	public function get_blocks() {

		return $this->blocks;
	}


	/**
	 * Gets a block instance.
	 *
	 * @since 1.15.0
	 *
	 * @param string $which_block block type
	 * @return Blocks\Block|Blocks\Member_Content|Blocks\Non_Member_Content
	 */
	public function get_block( $which_block ) {

		return isset( $this->blocks[ $which_block ] ) ? $this->blocks[ $which_block ] : null;
	}


	/**
	 * Registers scripts and styles shared by all blocks.
	 *
	 * @since 1.15.0
	 */
	private function register_blocks_scripts_styles() {

		$blocks_handle = 'wc-memberships-blocks';

		// register styles shared by all blocks for editor
		wp_register_style( $blocks_handle.'-editor', wc_memberships()->get_plugin_url() . '/assets/css/blocks/wc-memberships-blocks-editor.min.css', [ 'wc-blocks-editor-style', 'wc-blocks-style' ], \WC_Memberships::VERSION );
		wp_register_style( $blocks_handle, wc_memberships()->get_plugin_url() . '/assets/css/blocks/wc-memberships-blocks.min.css', [], \WC_Memberships::VERSION );


		// register scripts shared by all blocks
		wp_register_script( $blocks_handle, wc_memberships()->get_plugin_url() . '/assets/js/blocks/wc-memberships-blocks.min.js', $this->get_script_dependencies(), \WC_Memberships::VERSION, true );
		wp_localize_script( $blocks_handle, 'wc_memberships_blocks', $this->get_script_variables() );

		// configure localization files location
		wp_set_script_translations( $blocks_handle, 'woocommerce-memberships', wc_memberships()->get_plugin_path() . '/i18n/languages/blocks' );

		// register scripts shared by all blocks
		wp_register_script( $blocks_handle.'-common', wc_memberships()->get_plugin_url() . '/assets/js/frontend/wc-memberships-blocks-common.min.js', ['selectWoo'], \WC_Memberships::VERSION, true );
		wp_localize_script( $blocks_handle.'-common', 'wc_memberships_blocks_common', $this->get_common_script_variables() );
	}



	/**
	 * Gets the blocks script dependencies.
	 *
	 * Helper method, shouldn't be opened to public.
	 *
	 * @since 1.15.0
	 *
	 * @return string[] array of script handles
	 */
	private function get_script_dependencies() {

		return [
			'lodash',
			'react',
			'react-dom',
			'wp-api',
			'wp-api-fetch',
			'wp-blocks',
			'wp-components',
			'wp-data',
			'wp-element',
			'wp-i18n',
		];
	}

	/**
	 * Gets the blocks script helper variables to print in the screen.
	 *
	 * Helper method, shouldn't be opened to public.
	 *
	 * @since 1.23.0
	 *
	 * @return array
	 */
	private function get_common_script_variables() {

		$keywords = [
			'email'   => __( 'Email', 'woocommerce-memberships' ),
			'phone'   => __( 'Phone', 'woocommerce-memberships' ),
			'plan'    => __( 'Plan', 'woocommerce-memberships' ),
			'address' => __( 'Address', 'woocommerce-memberships' ),
			'search_not_found' => __ ( 'We didn’t find any members. Please try a different search or check for typos.', 'woocommerce-memberships' ),
			'results_not_found' => __ ( 'No records found...', 'woocommerce-memberships' )
		];

		return [
			'keywords'     => $keywords,
			'ajaxUrl'   => admin_url( 'admin-ajax.php' ),
			'restUrl'   => esc_url_raw( rest_url() ),
			'restNonce' => wp_create_nonce( 'wp_rest' )
		];
	}

	/**
	 * Gets the blocks script helper variables to print in the screen.
	 *
	 * Helper method, shouldn't be opened to public.
	 *
	 * @since 1.15.0
	 *
	 * @return array
	 */
	private function get_script_variables() {

		$membership_plans = $membership_statuses = $profile_fields = $merge_tags = [];
		foreach ( wc_memberships_get_membership_plans() as $membership_plan ) {
			// the data below is prepared for react-select, which expects an array objects with values and labels
			$membership_plans[] = [
				'value' => $membership_plan->get_id(),
				'label' => $membership_plan->get_name(),
			];
		}

		foreach ( wc_memberships_get_user_membership_statuses() as $status_key => $membership_status) {
			// the data below is prepared for react-select, which expects an array objects with values and labels
			$membership_statuses[] = [
				'value' => $status_key,
				'label' => $membership_status['label'] ?? '',
			];
		}

		foreach ( get_option('wc_memberships_profile_fields', []) as $profile_field) {
			// the data below is prepared for react-select, which expects an array objects with values and labels
			$profile_fields[] = [
				'value' => $profile_field['slug'],
				'label' => $profile_field['name'] ?? '',
			];
		}

		foreach ( \WC_Memberships_User_Messages::get_available_merge_tags( true ) as $merge_tag => $help_text ) {

			// content created in blocks isn't a product so the discount merge tag isn't applicable
			if ( 'discount' === $merge_tag ) {
				continue;
			}

			$merge_tags[] = [
				'tag'  => $merge_tag,
				'help' => $help_text,
			];
		}

		$version = $this->get_block_editor_version();

		// blocks keywords for shortcut and blocks search uses
		if ( false !== strpos( $version, '.' ) && version_compare( $version, '6.2', '<' ) ) {
			// older versions of Gutenberg may throw an error if more than 3 keywords are defined per block
			$keywords = [
				__( 'restricted', 'woocommerce-memberships' ),
				__( 'hide', 'woocommerce-memberships' ),
				__( 'protected', 'woocommerce-memberships' ),
			];
		} else {
			// note: some Gutenberg versions may just ignore keywords past the first three in the list
			$keywords = [
				__( 'restricted', 'woocommerce-memberships' ),
				__( 'hide', 'woocommerce-memberships' ),
				__( 'protected', 'woocommerce-memberships' ),
				__( 'memberships', 'woocommerce-memberships' ),
				__( 'members', 'woocommerce-memberships' ),
				__( 'non-members', 'woocommerce-memberships' ),
				__( 'non members', 'woocommerce-memberships' ),
				__( 'restrictions', 'woocommerce-memberships' ),
				__( 'content', 'woocommerce-memberships' ),
				__( 'hidden', 'woocommerce-memberships' ),
				__( 'private', 'woocommerce-memberships' ),
				__( 'public', 'woocommerce-memberships' ),
			];
		}

		return [
			'block_editor_version'           => $this->get_block_editor_version(),
			'is_wc_subscriptions_active'     => wc_memberships()->get_integrations_instance()->is_subscriptions_active(),
			'membership_plans'               => $membership_plans,
			'membership_statuses'            => $membership_statuses,
			'profile_fields'                 => $profile_fields,
			'custom_message_default_content' => \WC_Memberships_User_Messages::get_message( 'content_restricted_message_no_products' ),
			'custom_message_merge_tags'      => $merge_tags,
			'plugin'                         => [
				'settings_url'      => wc_memberships()->get_settings_url(),
				'documentation_url' => wc_memberships()->get_documentation_url(),
				'directory_url' 	=> plugin_dir_url( __DIR__ ),
			],
			'i18n'                           => [
				'keywords' => $keywords,
			]
		];
	}


}
