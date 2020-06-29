<?php
/**
 * Helper class for integrating with the Amazon Inventory API
 *
 * @package NeverSettle\WooCommerce-Amazon-Fulfillment
 * @since 2.0.0
 */

if ( ! class_exists( 'NS_FBA_Maintenance' ) ) {

	class NS_FBA_Maintenance {
		
		private $ns_fba;
		
		function __construct( $ns_fba ) {
			// local reference to the main ns_fba object
			$this->ns_fba = $ns_fba;
			
			// TODO: remove these at some point ing the far future from Nov 2016 once everyone has received updated settings from 1.1.0.1
			// Add the new setting and default to ON for these settings - but only if they don't already exist
			if ( ! isset( $this->ns_fba->options['ns_fba_email_on_error'] ) ) {
				$this->ns_fba->options['ns_fba_email_on_error'] = 'yes';
			}						
			if ( ! isset( $this->ns_fba->options['ns_fba_manual_item_override'] ) ) {
				$this->ns_fba->options['ns_fba_manual_item_override'] = 'yes';
			}	
			if ( ! isset( $this->ns_fba->options['ns_fba_display_order_tracking'] ) ) {
				$this->ns_fba->options['ns_fba_display_order_tracking'] = 'yes';
			}
		}
		
		/**
		 * Translates custom order statuses for WC 2.2 upgrade.
		 *
		 * @since 1.0.7.7
		 */
		
		function update_order_statuses_for_wc_2_2 () {
			global $wpdb;
			// Update order statuses
			$wpdb->query( "
				UPDATE {$wpdb->posts} as posts
				LEFT JOIN {$wpdb->term_relationships} AS rel ON posts.ID = rel.object_ID
				LEFT JOIN {$wpdb->term_taxonomy} AS tax USING( term_taxonomy_id )
				LEFT JOIN {$wpdb->terms} AS term USING( term_id )
				SET posts.post_status = 'wc-sent-to-fba'
				WHERE posts.post_type = 'shop_order'
				AND posts.post_status = 'publish'
				AND tax.taxonomy = 'shop_order_status'
				AND	term.slug LIKE 'sent-to-fba%';
			" );
			$wpdb->query( "
				UPDATE {$wpdb->posts} as posts
				LEFT JOIN {$wpdb->term_relationships} AS rel ON posts.ID = rel.object_ID
				LEFT JOIN {$wpdb->term_taxonomy} AS tax USING( term_taxonomy_id )
				LEFT JOIN {$wpdb->terms} AS term USING( term_id )
				SET posts.post_status = 'wc-fail-to-fba'
				WHERE posts.post_type = 'shop_order'
				AND posts.post_status = 'publish'
				AND tax.taxonomy = 'shop_order_status'
				AND	term.slug LIKE 'fail-to-fba%';
			" );
		}
		
		/**
		 * Runs from the in_plugin_update_message hook
		 * 
		 * This is still in progress and not complete or tested yet. 
		 * Main hook is commented out in woocommerce-amazon-fulfillment.php 
		 *
		 * @since 3.0.1
		 */
		
		function show_upgrade_notice( $current_plugin_meta, $new_plugin_meta ) {
			// readme contents
		    $data       = file_get_contents( 'http://neversettle.it/wp-content/uploads/plugins/ns-fba-for-woocommerce/readme.txt' );
		
		    // assuming you've got a Changelog section
		    // @example == Changelog ==
		    $notice  = stristr( $data, '== Upgrade Notice ==' );

		    // only return for the current & later versions
		    $curr_ver   = get_plugin_data('Version');
		    $message  = stristr( $notice, "= {$curr_ver}" );
		
		    // uncomment the next line to var_export $var contents for dev:
		    # echo '<pre>'.var_export( $plugin_data, false ).'<br />'.var_export( $r, false ).'</pre>';

			echo '<p style="background-color: #d54e21; padding: 10px; color: #f9f9f9; margin-top: 10px"><strong>Important Upgrade Notice:</strong> ';
        	echo esc_html( $message ), '</p>';			
		}
		
	} // class
}