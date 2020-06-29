<?php
/**
 * WooCommerce Tab Manager
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Tab Manager to newer
 * versions in the future. If you wish to customize WooCommerce Tab Manager for your
 * needs please refer to http://docs.woocommerce.com/document/tab-manager/
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2012-2020, SkyVerge, Inc. (info@skyverge.com)
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\Tab_Manager;

defined( 'ABSPATH' ) or exit;

use \SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;

/**
 * Tab Manager lifecycle handler.
 *
 * @since 1.10.0
 *
 * @method \WC_Tab_Manager get_plugin()
 */
class Lifecycle extends Framework\Plugin\Lifecycle {


	/**
	 * Lifecycle constructor.
	 *
	 * @since 1.10.1
	 *
	 * @param \WC_Tab_Manager $plugin
	 */
	public function __construct( $plugin ) {

		parent::__construct( $plugin );

		$this->upgrade_versions = [
			'1.0.4.1',
			'1.0.5',
			'1.3.1',
		];
	}


	/**
	 * Performs install tasks.
	 *
	 * @since 1.10.0
	 */
	protected function install() {
		global $wpdb;

		// check for a pre 1.1 version
		$legacy_version = get_option( 'wc_tab_manager_db_version' );

		if ( false !== $legacy_version ) {

			// upgrade path from previous version, trash old version option
			delete_option( 'wc_tab_manager_db_version' );

			// upgrade path
			$this->upgrade( $legacy_version );

			// and we're done
			return;
		}

		// any Custom Product Lite Tabs?
		$results = $wpdb->get_results( "
			SELECT post_id, meta_value
			FROM {$wpdb->postmeta}
			WHERE meta_key='frs_woo_product_tabs'
		" );

		// prepare the core tabs
		$core_tabs = $this->get_plugin()->get_core_tabs();
		foreach ( $core_tabs as $id => $tab ) {
			unset( $core_tabs[ $id ]['description'] );
		}

		// foreach product with a custom lite tab
		foreach ( $results as $result ) {

			$old_tabs = maybe_unserialize( $result->meta_value );

			$new_tabs = [ 'core_tab_description' => $core_tabs['core_tab_description'], 'core_tab_additional_information' => $core_tabs['core_tab_additional_information'] ];

			// keep track of tab names to avoid clashes
			$found_names = [ 'description' => 1, 'additional_information' => 1, 'reviews' => 1 ];

			foreach ( $old_tabs as $tab ) {

				// create the product tab
				if ( $tab['title'] && $tab['content'] ) {

					$new_tab = [
						'position' => count( $new_tabs ),
						'type' => 'product'
					];

					$new_tab_data = [
						'post_title'    => $tab['title'],
						'post_content'  => $tab['content'],
						'post_status'   => 'publish',
						'ping_status'   => 'closed',
						'post_author'   => get_current_user_id(),
						'post_type'     => 'wc_product_tab',
						'post_parent'   => $result->post_id,
						'post_password' => uniqid( 'tab_', false ), // Protects the post just in case
					];

					// create the post and get the id
					$id = wp_insert_post( $new_tab_data );
					$new_tab['id'] = $id;

					// determine the unique tab name
					$tab_name = sanitize_title( $tab['title'] );
					if ( ! isset( $found_names[ $tab_name ] ) ) {
						$found_names[ $tab_name ] = 1;
					} else {
						$found_names[ $tab_name ]++;
					}
					if ( $found_names[ $tab_name ] > 1 ) {
						$tab_name .= '-' . ( $found_names[ $tab_name ] - 1 );
					}
					$new_tab['name'] = $tab_name;

					// tab is complete
					$new_tabs[ 'product_tab_' . $id ] = $new_tab;
				}
			}

			// add the core reviews tab on at the end
			$new_tabs['core_tab_reviews'] = $core_tabs['core_tab_reviews'];
			$new_tabs['core_tab_reviews']['position'] = count( $new_tabs ) - 1;


			if ( count( $new_tabs ) > 3 ) {
				// if we actually had any product tabs
				add_post_meta( $result->post_id, '_product_tabs',        $new_tabs, true );
				add_post_meta( $result->post_id, '_override_tab_layout', 'yes',     true );
			}
		}
	}


	/**
	 * Updates to version 1.0.4.1
	 *
	 * In this version and before:
	 * - custom product lite tabs were imported but their status was set to 'future' meaning they appeared in the Tab Manager menu, but not at the product level
	 * - product tab layout had 'tab_name' rather than 'name' for imported custom product lite tabs
	 * - imported custom product lite tabs attached to products did not have the '_override_tab_layout' meta set
	 *
	 * @since 1.10.1
	 */
	protected function upgrade_to_1_0_4_1() {

		$tabs = get_posts( [
			'numberposts' => '',
			'post_type'   => 'wc_product_tab',
			'nopaging'    => true,
			'post_status' => 'future'
		] );

		if ( is_array( $tabs ) ) {

			foreach( $tabs as $tab ) {

				// make the tab post status 'publish'
				wp_update_post( [
					'ID'          => $tab->ID,
					'post_status' => 'publish',
				] );

				// mark the tab as migrated, in case we need to reference them one day
				add_post_meta( $tab->ID, '_migrated_future', 'yes' );

				// fix the product tab layout 'tab_name' field, which should be 'name'
				$fixed        = false;
				$product_tabs = get_post_meta( $tab->post_parent, '_product_tabs', true );

				foreach ( $product_tabs as $index => $product_tab ) {

					if ( isset( $product_tab['tab_name'] ) && $product_tab['tab_name'] && ! isset( $product_tab['name'] ) ) {

						$product_tabs[ $index ]['name'] = $product_tab['tab_name'];

						unset( $product_tabs[ $index ]['tab_name'] );

						$fixed = true;
					}
				}

				if ( $fixed ) {
					update_post_meta( $tab->post_parent, '_product_tabs', $product_tabs );
				}

				// It seems that setting the tab layout override in existing stores would be too dangerous, so for now the following is not used enable the product '_override_tab_layout' so the product tab is actually used:
				// update_post_meta( $tab->post_parent, '_override_tab_layout', 'yes' );
			}
		}

		unset( $tabs );
	}


	/**
	 * Updates to version 1.0.5
	 *
	 * In version 1.0.5 the core tab previously referred to as 'attributes' now needs to be referred to as 'additional_information' for consistency with WC 2.0+, so fix the global and any product tab layouts.
	 *
	 * @since 1.10.1
	 */
	protected function upgrade_to_1_0_5() {
		global $wpdb;

		// fix global tab layout
		$tab_layout = get_option( 'wc_tab_manager_default_layout' );

		if ( $tab_layout && isset( $tab_layout['core_tab_attributes'] ) ) {

			$tab_layout['core_tab_additional_information']       = $tab_layout['core_tab_attributes'];
			$tab_layout['core_tab_additional_information']['id'] = 'additional_information';

			unset( $tab_layout['core_tab_attributes'] );

			update_option( 'wc_tab_manager_default_layout', $tab_layout );
		}

		// fix any product-level tab layouts
		$results = $wpdb->get_results( "
			SELECT post_id, meta_value FROM {$wpdb->postmeta}
			WHERE meta_key='_product_tabs'"
		);

		if ( is_array( $results ) ) {

			foreach ( $results as $row ) {

				$tab_layout = maybe_unserialize( $row->meta_value );

				if ( $tab_layout && isset( $tab_layout['core_tab_attributes'] ) ) {

					$tab_layout['core_tab_additional_information']       = $tab_layout['core_tab_attributes'];
					$tab_layout['core_tab_additional_information']['id'] = 'additional_information';

					unset( $tab_layout['core_tab_attributes'] );

					update_post_meta( $row->post_id, '_product_tabs', $tab_layout );
				}
			}
		}

		unset( $results );
	}


	/**
	 * Updates to version 1.3.1
	 *
	 * @since 1.10.1
	 */
	protected function upgrade_to_1_3_1() {

		// enable the batch product update nag
		$user = wp_get_current_user();

		if ( $user ) {
			update_user_meta( $user->ID, 'wc_tab_manager_show_update_products_nag', 'true' );
		}

		// ensures that tabs are searchable if Relevanssi is installed
		$this->get_plugin()->get_search_instance()->update_relevanssi_searchable_tab_meta();
		$this->get_plugin()->get_search_instance()->maybe_build_relevanssi_index();
	}


}
