<?php
/**
 * class-section-assistant.php
 *
 * Copyright (c) "kento" Karim Rahimpur www.itthinx.com
 *
 * This code is provided subject to the license granted.
 * Unauthorized use and distribution is prohibited.
 * See COPYRIGHT.txt and LICENSE.txt
 *
 * This code is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * This header and all notices must be kept intact.
 *
 * @author itthinx
 * @package woocommerce-product-search
 * @since 5.0.0
 */

namespace com\itthinx\woocommerce\search\engine\admin;

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

use com\itthinx\woocommerce\search\engine\Settings;

/**
 * Assistant section.
 */
class Section_Assistant extends \WooCommerce_Product_Search_Admin_Base {

	/**
	 * Records changes made to the settings.
	 */
	public static function save() {

		if ( current_user_can( self::ASSISTANT_CONTROL_CAPABILITY ) ) {

			$sidebars_widgets     = get_option( 'sidebars_widgets', array() );
			$sidebar_id           = !empty( $_POST['wps-assistant-sidebar-id'] ) ? $_POST['wps-assistant-sidebar-id'] : null;
			$assistant_widget_ids = !empty( $_POST['wps-assistant-widget-ids'] ) ? $_POST['wps-assistant-widget-ids'] : null;
			if (
				$sidebar_id !== null &&
				$sidebar_id !== 'wp_inactive_widgets' &&
				isset( $sidebars_widgets[$sidebar_id] ) &&
				$assistant_widget_ids !== null &
				is_array( $assistant_widget_ids ) &&
				count( $assistant_widget_ids ) > 0
			) {
				$i = 0;
				foreach ( $assistant_widget_ids as $assistant_widget_id ) {
					$assistant_widget_id = explode( '-', $assistant_widget_id );
					$id_base             = isset( $assistant_widget_id[0] ) ? $assistant_widget_id[0] : null;
					$attribute_taxonomy  = isset( $assistant_widget_id[1] ) ? $assistant_widget_id[1] : null;
					if ( $id_base !== null ) {
						$widget_instances = get_option( 'widget_' . $id_base, array() );
						$numbers          = array_filter( array_keys( $widget_instances ), 'is_int' );
						$next             = ( count( $numbers ) > 0 ) ? max( $numbers ) + 1 : self::WIDGET_NUMBER_START;
						$widget           = null;
						$widget_settings  = null;
						switch( $id_base ) {
							case 'woocommerce_product_search_filter_widget' :
								$widget = new \WooCommerce_Product_Search_Filter_Widget();
								$widget_settings = $widget->update( $widget->get_default_instance(), array() );
								break;
							case 'woocommerce_product_search_filter_attribute_widget' :
								if ( $taxonomy = get_taxonomy( $attribute_taxonomy ) ) {
									$widget = new \WooCommerce_Product_Search_Filter_Attribute_Widget();
									$title = !empty( $taxonomy->labels->singular_name ) ? $taxonomy->labels->singular_name : $taxonomy->label;
									$widget_settings = $widget->update( array_merge( $widget->get_default_instance(), array( 'taxonomy' => $taxonomy->name ) ), array() );
								}
								break;
							case 'woocommerce_product_search_filter_category_widget' :
								$widget = new \WooCommerce_Product_Search_Filter_Category_Widget();
								$widget_settings = $widget->update( $widget->get_default_instance(), array() );
								break;
							case 'woocommerce_product_search_filter_price_widget' :
								$widget = new \WooCommerce_Product_Search_Filter_Price_Widget();
								$widget_settings = $widget->update( $widget->get_default_instance(), array() );
								break;
							case 'woocommerce_product_search_filter_rating_widget' :
								$widget = new \WooCommerce_Product_Search_Filter_Rating_Widget();
								$widget_settings = $widget->update( $widget->get_default_instance(), array() );
								break;
							case 'woocommerce_product_search_filter_sale_widget' :
								$widget = new \WooCommerce_Product_Search_Filter_Sale_Widget();
								$widget_settings = $widget->update( $widget->get_default_instance(), array() );
								break;
							case 'woocommerce_product_search_filter_stock_widget' :
								$widget = new \WooCommerce_Product_Search_Filter_Stock_Widget();
								$widget_settings = $widget->update( $widget->get_default_instance(), array() );
								break;
							case 'woocommerce_product_search_filter_tag_widget' :
								$widget = new \WooCommerce_Product_Search_Filter_Tag_Widget();
								$widget_settings = $widget->update( $widget->get_default_instance(), array() );
								break;
							case 'woocommerce_product_search_filter_reset_widget' :
								$widget = new \WooCommerce_Product_Search_Filter_Reset_Widget();
								$widget_settings = $widget->update( $widget->get_default_instance(), array() );
								break;
						}
						if ( $widget !== null && $widget_settings !== null ) {
							$widget_instances[$next] = $widget_settings;
							$sidebars_widgets[$sidebar_id][] = $id_base . '-' . $next;
							update_option( 'widget_' . $id_base, $widget_instances );
							$i++;
						}
						unset( $widget );
						unset( $widget_settings );
					}
				}
				update_option( 'sidebars_widgets', $sidebars_widgets );
				if ( class_exists( 'WC_Admin_Settings' ) && method_exists( 'WC_Admin_Settings', 'add_message' ) ) {
					\WC_Admin_Settings::add_message(
						_n( 'One widget has been added.', sprintf( '%d widgets have been added.', $i ), $i, 'woocommerce-product-search' )
					);
				}
			}
		}

	}

	/**
	 * Renders the section.
	 */
	public static function render() {

		if ( current_user_can( self::ASSISTANT_CONTROL_CAPABILITY ) ) {

			global $wp_registered_sidebars;

			echo '<h3>';
			esc_html_e( 'Assistant', 'woocommerce-product-search' );
			echo '</h3>';

			echo '<p>';
			esc_html_e( 'This assistant helps you to add suitable live filters to your store.', 'woocommerce-product-search' );
			echo '</p>';

			echo '<p>';
			esc_html_e( 'We recommend to use a live search filter, one category filter, one for prices and one for each product attribute that is used to offer product variations.', 'woocommerce-product-search' );
			echo ' ';
			esc_html_e( 'If it makes sense within the context of your store, you can also add a filter for product tags.', 'woocommerce-product-search' );
			echo ' ';
			echo wp_kses(
				sprintf(
					__( 'You can add all of them now or just some, you can always come back here or simply add them in the <a href="%s">Widgets</a> section yourself.', 'woocommerce-product-search' ),
					esc_url( admin_url( 'widgets.php' ) )
				),
				array( 'a' => array( 'href' => array() ) )
			);
			echo '</p>';
			echo '<p>';
			esc_html_e( 'To get started quickly, simply choose the sidebar that seems most suitable.', 'woocommerce-product-search' );
			echo ' ';
			esc_html_e( 'It will contain the widgets that your customers use to filter the products in your store.', 'woocommerce-product-search' );
			echo ' ';
			esc_html_e( 'This assistant suggests all that are not already present in a sidebar.', 'woocommerce-product-search' );
			echo ' ';
			esc_html_e( 'The widgets will only display on relevant shop pages by default.', 'woocommerce-product-search' );
			echo ' ';
			echo wp_kses(
				sprintf(
					__( 'You can customize and <a href="%s">control</a> them further as you will find them in the <a href="%s">Widgets</a> section.', 'woocommerce-product-search' ),
					esc_url( 'https://wordpress.org/plugins/widgets-control/' ),
					esc_url( admin_url( 'widgets.php' ) )
				),
				array( 'a' => array( 'href' => array() ) )
			);
			echo '</p>';

			$sidebars_widgets = get_option( 'sidebars_widgets', array() );
			$sidebars_index = array();
			$filter_widget_id_bases = array(
				'woocommerce_product_search_filter_widget',
				'woocommerce_product_search_filter_attribute_widget',
				'woocommerce_product_search_filter_category_widget',
				'woocommerce_product_search_filter_price_widget',
				'woocommerce_product_search_filter_rating_widget',
				'woocommerce_product_search_filter_sale_widget',
				'woocommerce_product_search_filter_stock_widget',
				'woocommerce_product_search_filter_tag_widget',
				'woocommerce_product_search_filter_reset_widget'
			);
			$existing_filter_widgets = array();
			foreach( $filter_widget_id_bases as $id_base ) {
				$widget_instances = get_option( 'widget_' . $id_base, array() );
				foreach( $widget_instances as $widget_number => $widget_instance ) {
					$sidebar_id = null;
					if ( is_int( $widget_number ) ) {
						foreach ( $sidebars_widgets as $_sidebar_id => $widget_ids ) {
							if ( is_array( $widget_ids ) ) {
								if ( in_array( $id_base . '-' . $widget_number, $widget_ids ) ) {
									$sidebar_id = $_sidebar_id;
								}
							}
						}
						if ( $sidebar_id !== null && $sidebar_id !== 'wp_inactive_widgets' ) {
							switch( $id_base ) {
								case 'woocommerce_product_search_filter_attribute_widget' :
									if ( !empty( $widget_instance['taxonomy'] ) ) {
										$existing_filter_widgets[] = $id_base . '-' . $widget_instance['taxonomy'];
									} else {
										$existing_filter_widgets[] = $id_base;
									}
									break;
								default :
									$existing_filter_widgets[] = $id_base;
							}
							$sidebars_index[$existing_filter_widgets[count( $existing_filter_widgets ) - 1]][] = $sidebar_id;
						}
					}
				}
			}

			echo '<h4>';
			esc_html_e( '1. Select the sidebar', 'woocommerce-product-search' );
			echo '</h4>';

			echo '<table>';
			echo '<tr>';
			echo '<td>';
			printf(
				'<label for="%s" title="%s">',
				esc_attr( 'wps-assistant-sidebar-id' ),
				esc_attr__( 'Select the sidebar to which the assistant should add filter widgets.', 'woocommerce-product-search' )
			);
			echo esc_html( __( 'Sidebar', 'woocommerce-product-search' ) );
			echo '</label>';
			echo '</td>';
			echo '<td>';
			printf(
				'<select name="%s" title="%s">',
				esc_attr( 'wps-assistant-sidebar-id' ),
				esc_attr__( 'Add filter widgets to the selected sidebar.', 'woocommerce-product-search' )
			);
			if ( !empty( $wp_registered_sidebars ) && is_array( $wp_registered_sidebars ) ) {
				foreach ( $wp_registered_sidebars as $sidebar_id => $sidebar ) {
					printf(
						'<option value="%s">%s</option>',
						esc_attr( $sidebar_id ),
						esc_html( $sidebar['name'] )
					);
				}
			}
			echo '</select>';
			echo '</td>';
			echo '</tr>';
			echo '</table>';

			echo '<h4>';
			esc_html_e( '2. Choose the filters to add', 'woocommerce-product-search' );
			echo '</h4>';

			echo '<p>';
			esc_html_e( 'The assistant will propose to add unused filters and select them for you by default.', 'woocommerce-product-search' );
			echo '</p>';

			echo '<table class="wps-filter-widget-entries">';

			$widgets = array(
				new \WooCommerce_Product_Search_Filter_Widget(),
				new \WooCommerce_Product_Search_Filter_Price_Widget(),
				new \WooCommerce_Product_Search_Filter_Category_Widget(),
				new \WooCommerce_Product_Search_Filter_Rating_Widget(),
				new \WooCommerce_Product_Search_Filter_Sale_Widget(),
				new \WooCommerce_Product_Search_Filter_Stock_Widget(),
				new \WooCommerce_Product_Search_Filter_Tag_Widget(),
				new \WooCommerce_Product_Search_Filter_Reset_Widget()
			);
			$i = 0;
			foreach( $widgets as $widget ) {
				printf(
					'<tr class="%s" style="padding-bottom: 1em; display: block;">',
					$widget instanceof \WooCommerce_Product_Search_Filter_Reset_Widget ? 'wps-filter-reset-widget-entry' : ''
				);

				echo '<td style="vertical-align:top">';
				printf(
					'<input class="wps-assistant" name="wps-assistant-widget-ids[]" type="checkbox" value="%s" %s id="assistant-widget-%d"/>',
					esc_attr( $widget->id_base ),
					!in_array( $widget->id_base, $existing_filter_widgets ) ? ' checked="checked" ' : '',
					$i
				);
				echo '</td>';

				echo '<td style="vertical-align:top">';
				printf( '<label title="%s" for="assistant-widget-%d">',
					!empty( $widget->widget_options['description'] ) ? esc_attr__( $widget->widget_options['description'] ) : '',
					$i
				);
				echo '<div>';
				echo '<strong>';
				echo esc_html( $widget->name );
				echo '</strong>';
				echo '</div>';
				if ( isset( $sidebars_index[$widget->id_base] ) ) {
					echo '<div class="description">';
					esc_html_e( 'Present in &hellip; ', 'woocommerce-product-search' );
					$sidebar_names = array();
					foreach( $sidebars_index[$widget->id_base] as $sidebar_id ) {
						if ( isset( $wp_registered_sidebars[$sidebar_id] ) ) {
							$sidebar_names[] = $wp_registered_sidebars[$sidebar_id]['name'];
						}
					}
					echo '&nbsp;';
					echo esc_html( implode( ', ', $sidebar_names ) );
					echo '</div>';
				}
				echo '</label>';
				echo '</td>';

				echo '</tr>';
				$i++;
			}

			$product_attribute_taxonomies = wc_get_attribute_taxonomy_names();
			foreach( $product_attribute_taxonomies as $product_attribute_taxonomy ) {
				if ( $taxonomy = get_taxonomy( $product_attribute_taxonomy ) ) {
					$widget          = new \WooCommerce_Product_Search_Filter_Attribute_Widget();
					$title           = !empty( $taxonomy->labels->singular_name ) ? $taxonomy->labels->singular_name : $taxonomy->label;
					$widget_settings = $widget->update( array( 'title' => $title, 'taxonomy' => $taxonomy->name ), array() );

					echo '<tr style="padding-bottom: 1em; display: block;">';
					echo '<td style="vertical-align:top">';
					printf(
						'<input class="wps-assistant" name="wps-assistant-widget-ids[]" type="checkbox" value="%s-%s" %s id="assistant-widget-%d"/>',
						esc_attr( $widget->id_base ), esc_attr( $taxonomy->name ),
						!in_array( $widget->id_base . '-' . $taxonomy->name, $existing_filter_widgets ) ? ' checked="checked" ' : '',
						$i
					);
					echo '</td>';

					echo '<td style="vertical-align:top">';
					printf( '<label title="%s" for="assistant-widget-%d">',
						!empty( $widget->widget_options['description'] ) ? esc_attr__( $widget->widget_options['description'] ) : '',
						$i
					);
					echo '<div>';
					echo '<strong>';
					echo esc_html( $widget->name );
					echo '&nbsp;&mdash;&nbsp;';
					echo esc_html( $title );
					echo '</strong>';
					echo '</div>';
					if ( isset( $sidebars_index[$widget->id_base . '-' . $taxonomy->name] ) ) {
						echo '<div class="description">';
						esc_html_e( 'Present in &hellip; ', 'woocommerce-product-search' );
						$sidebar_names = array();
						foreach( $sidebars_index[$widget->id_base . '-' . $taxonomy->name] as $sidebar_id ) {
							if ( isset( $wp_registered_sidebars[$sidebar_id] ) ) {
								$sidebar_names[] = $wp_registered_sidebars[$sidebar_id]['name'];
							}
						}
						echo '&nbsp;';
						echo esc_html( implode( ', ', $sidebar_names ) );
						echo '</div>';
					}
					echo '</label>';
					echo '</td>';
					echo '</tr>';
				}
				$i++;
			}
			echo '</table>';

			echo '<script type="text/javascript">';
			echo 'document.addEventListener( "DOMContentLoaded", function() {';
			echo 'if ( typeof jQuery !== "undefined" ) {';

			echo 'jQuery(".wps-filter-reset-widget-entry").appendTo(".wps-filter-widget-entries");';

			echo 'jQuery("#run-assistant-confirm").prop("disabled",jQuery("input.wps-assistant:checked").length === 0);';
			echo 'jQuery("input.wps-assistant").change(function(){';
			echo 'jQuery("#run-assistant-confirm").prop("disabled",jQuery("input.wps-assistant:checked").length === 0);';
			echo '});';

			echo 'jQuery("#run-assistant-confirm").click(function(e){';
			echo 'e.stopPropagation();';
			echo 'if ( jQuery("input.wps-assistant:checked").length > 0 ) {';
			printf(
				'if ( confirm("%s") ) {',
				esc_html__( 'Add the selected filters to the chosen sidebar?', 'woocommerce-product-search' )
			);
			echo '} else {';
			echo 'e.preventDefault();';
			echo '}';
			echo '} else {';
			echo 'e.preventDefault();';
			echo '}';
			echo '});';
			echo '}';
			echo '} );';
			echo '</script>';
		}
	}

}
