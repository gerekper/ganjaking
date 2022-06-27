<?php
/**
 * Plugin Name: WooCommerce Cart Add-Ons
 * Plugin URI: https://woocommerce.com/products/cart-add-ons/
 * Description: A tool for driving incremental and impulse purchases once customers are in the shopping cart. It extends the concept of upsells and cross-sells at the product level, and engages your customers at the moment they are most likely to increase spending.
 * Version: 2.3.0
 * Author: WooCommerce
 * Tested up to: 6.0
 * WC requires at least: 4.0
 * WC tested up to: 6.5
 * Author URI: https://woocommerce.com/
 * Text domain: sfn_cart_addons
 * Woo: 18717:3a8ef25334396206f5da4cf208adeda3
 *
 * Copyright 2020 WooCommerce.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package woocommerce-cart-add-ons
 */

defined( 'ABSPATH' ) || exit;

use Automattic\WooCommerce\Admin\Features\Navigation\Menu;
use Automattic\WooCommerce\Admin\Features\Navigation\Screen;
use Automattic\WooCommerce\Admin\Features\Features;


// Subscribe to automated translations.
add_filter( 'woocommerce_translations_updates_for_woocommerce-cart-add-ons', '__return_true' );

define( 'WC_CART_ADDONS_VERSION', '2.3.0' ); // WRCS: DEFINED_VERSION.

require 'widget.php';

class SFN_Cart_Addons {

	public function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'widgets_init', array( $this, 'register_widget' ) );
		add_action( 'admin_menu', array( $this, 'menu' ), 20 );
		add_filter( 'woocommerce_screen_ids', array( $this, 'register_screen_id' ) );

		// Help format variations.
		add_filter( 'post_class', array( $this, 'post_class' ) );
		add_filter( 'the_title', array( $this, 'the_title' ), 10, 2 );
		add_filter( 'the_permalink', array( $this, 'the_permalink' ) );

		// Settings styles and scripts.
		add_action( 'admin_enqueue_scripts', array( $this, 'settings_scripts' ) );
		add_action( 'admin_post_sfn_cart_addons_update_settings', array( $this, 'update_settings' ) );
		add_action( 'wp_ajax_sfn_product_is_variable', array( $this, 'ajax_product_is_variable' ) );

		// Cart page.
		add_action( 'woocommerce_after_cart', array( $this, 'cart_display_addons' ), 20 );

		// Shortcode.
		add_shortcode( 'display-addons', array( $this, 'sc_display_addons' ) );

	}

	public function register_widget() {
		register_widget( 'cart_addons_widget' );
	}

	/**
	 * Enqueue scripts/styles needed.
	 *
	 * @since 1.5.25
	 */
	public function enqueue_scripts() {
		wp_enqueue_style( 'wc_cart_addons_styles', plugins_url( 'assets/css/frontend.css', __FILE__ ), array(), WC_CART_ADDONS_VERSION );

	}

	public function menu() {
		$admin_page = add_submenu_page( 'woocommerce', __( 'Cart Add-Ons', 'sfn_cart_addons' ), __( 'Cart Add-Ons', 'sfn_cart_addons' ), 'manage_woocommerce', 'sfn-cart-addons', array( $this, 'settings' ) );

		// Register our help tab.
		add_action( 'load-' . $admin_page, array( $this, 'register_help_tab' ) );

		if (
			! class_exists( 'Features' ) ||
			! method_exists( Screen::class, 'register_post_type' ) ||
			! method_exists( Menu::class, 'add_plugin_item' ) ||
			! method_exists( Menu::class, 'add_plugin_category' ) ||
			! Features::is_enabled( 'navigation' )
		) {
			return;
		}

		Menu::add_plugin_item(
			array(
				'id'         => 'sfn-cart-addons',
				'title'      => __( 'Cart Add-Ons', 'sfn_cart_addons' ),
				'url'        => 'sfn-cart-addons',
				'capability' => 'manage_woocommerce',
			)
		);
	}

	/**
	 * Display a new section in the "Help" tab on our settings screen.
	 *
	 * @since  1.6.0
	 */
	public function register_help_tab() {
		$screen = get_current_screen();

		$contents  = '<h4>' . esc_html__( 'Use shortcodes on any page or post', 'sfn_cart_addons' ) . '</h4>' . "\n";
		$contents .= '<p><code>[display-addons length=5 mode=loop]</code> - ' . esc_html__( 'Will use your theme\'s template', 'sfn_cart_addons' ) . '<br /><code>[display-addons length=4 mode=images_name]</code> - ' . esc_html__( 'Shows the product image along with the product name', 'sfn_cart_addons' ) . '<br /><code>[display-addons length=8 mode=images_name_price]</code> - ' . esc_html__( 'Will display product images with name and price', 'sfn_cart_addons' ) . '</p>' . "\n";
		$contents .= '<h4>' . esc_html__( 'Use directly in your theme', 'sfn_cart_addons' ) . '</h4>' . "\n";
		$contents .= '<p><code>&lt;?php if ( function_exists(\'sfn_display_cart_addons\') ) sfn_display_cart_addons($num, $display); ?&gt;</code><br />' . sprintf( esc_html__( '%s is the maximum number of add-ons to display.', 'sfn_cart_addons' ), '<code>$num</code>' ) . ' ' . sprintf( esc_html__( '%s can be one of the following: \'loop\', \'images\', \'images_name\', \'images_name_price\', \'names\', \'names_price\'', 'sfn_cart_addons' ), '<code>$display</code>' ) . '</p>';

		// Add my_help_tab if current screen is My Admin Page.
		$screen->add_help_tab(
			array(
				'id'      => 'woocommerce-cart-add-ons-help',
				'title'   => __( 'Using Cart Add-ons', 'sfn_cart_addons' ),
				'content' => $contents,
			)
		);
	}

	public function register_screen_id( $screens ) {
		$screens[] = 'woocommerce_page_sfn-cart-addons';
		return $screens;
	}

	public function settings_scripts() {
		global $woocommerce;

		if ( isset( $_GET['page'] ) && 'sfn-cart-addons' === $_GET['page'] ) {
			wp_enqueue_style( 'sfn-cart-addons', plugins_url( 'assets/css/settings.css', __FILE__ ), array(), WC_CART_ADDONS_VERSION );

			$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

			if ( ! wp_script_is( 'sfn-product-search', 'registered' ) ) {
				wp_register_script( 'sfn-product-search', plugins_url( 'assets/js/product-search' . $suffix . '.js', __FILE__ ), array( 'jquery', 'selectWoo' ), WC_CART_ADDONS_VERSION, true );
			}

			wp_enqueue_script( 'sfn-product-search' );
			wp_localize_script(
				'sfn-product-search',
				'sfn_product_search',
				array(
					'security'     => wp_create_nonce( 'search-products' ),
					'errorLoading' => __( 'Searching...', 'sfn_cart_addons' ), // Workaround for https://github.com/select2/select2/issues/4355 instead of `the results could not be loaded` flash.
				)
			);

			$cart_addons_settings = array(
				'all_categories_used' => __( 'All categories have been used', 'sfn_cart_addons' ),
				'search_products'     => __( 'Search for a product &hellip;', 'sfn_cart_addons' ),
				'security'            => wp_create_nonce( 'sfn-settings' ),
			);

			wp_enqueue_script( 'cart_addons_settings', plugins_url( 'assets/js/settings' . $suffix . '.js', __FILE__ ), array( 'jquery', 'jquery-ui-sortable', 'jquery-ui-core' ), WC_CART_ADDONS_VERSION, true );
			wp_localize_script( 'cart_addons_settings', 'cart_addons_settings', $cart_addons_settings );

			wp_enqueue_style( 'woocommerce_admin_styles', $woocommerce->plugin_url() . '/assets/css/admin.css', array(), WC_CART_ADDONS_VERSION );
		}
	}

	public function settings() {
		require_once 'settings.php';
	}

	public function update_settings() {
		global $wpdb, $woocommerce;

		if ( ! isset( $_POST['sfn_cart_addons_update_settings_nonce'] ) || ! wp_verify_nonce( wc_clean( wp_unslash( $_POST['sfn_cart_addons_update_settings_nonce'] ) ), 'sfn_cart_addons_update_settings' )
		) {
			return;
		}

		$settings = get_option(
			'sfn_cart_addons',
			array(
				'header_title'   => __( 'Product Add-ons', 'sfn_cart_addons' ),
				'default_addons' => array(),
			)
		);
		$default  = array();

		if ( isset( $_POST['header_title'] ) ) {
			$settings['header_title'] = wc_clean( wp_unslash( $_POST['header_title'] ) );
		}

		if ( isset( $_POST['upsell_number'] ) ) {
			$settings['upsell_number'] = absint( $_POST['upsell_number'] );
		}

		if ( isset( $_POST['default_products'] ) && is_array( $_POST['default_products'] ) ) {
			if ( ! empty( $_POST['default_products'] ) ) {
				$default_products = $this->csv_to_array( wc_clean( wp_unslash( $_POST['default_products'] ) ) );

				foreach ( $default_products as $product_id ) {
					$default[] = $product_id;
				}
			}
		}
		$settings['default_addons'] = array_filter( $default );
		update_option( 'sfn_cart_addons', $settings );

		// Delete all entries.
		$product_addons  = array();
		$category_addons = array();

		if ( isset( $_POST['category'] ) && is_array( $_POST['category'] ) ) {
			$categories          = array_map( 'absint', wp_unslash( $_POST['category'] ) );
			$category_priorities = isset( $_POST['category_priorities'] ) && is_array( $_POST['category_priorities'] ) ? array_map( 'absint', wp_unslash( $_POST['category_priorities'] ) ) : array();

			foreach ( $category_priorities as $idx => $key ) {
				$category_id               = $categories[ $key ];
				$category_products[ $key ] = isset( $_POST['category_products'], $_POST['category_products'][ $key ] ) ? $this->csv_to_array( wc_clean( wp_unslash( $_POST['category_products'][ $key ] ) ) ) : array();

				// Make sure there are products selected.
				if ( ! empty( $category_products[ $key ] ) ) {
					// Insert.
					$addon = array(
						'category_id' => $category_id,
						'priority'    => $idx + 1,
						'products'    => array(),
					);

					foreach ( $category_products[ $key ] as $product_id ) {
						$addon['products'][] = $product_id;
					}

					$category_addons[] = $addon;
				}
			}
		}

		if ( isset( $_POST['product'] ) && is_array( $_POST['product'] ) ) {
			$products           = array_map( 'absint', wp_unslash( $_POST['product'] ) );
			$product_priorities = isset( $_POST['product_priorities'] ) && is_array( $_POST['product_priorities'] ) ? array_map( 'absint', wp_unslash( $_POST['product_priorities'] ) ) : array();

			foreach ( $product_priorities as $idx => $key ) {
				$product_id               = $products[ $key ];
				$include_variations       = isset( $_POST['product_include_variations'][ $key ] ) ? 1 : 0;
				$product_products[ $key ] = isset( $_POST['product_products'], $_POST['product_products'][ $key ] ) ? $this->csv_to_array( wc_clean( wp_unslash( $_POST['product_products'][ $key ] ) ) ) : array();

				// Make sure there are products selected.
				if ( ! empty( $product_products[ $key ] ) ) {
					// Insert.
					$addon = array(
						'product_id'         => $product_id,
						'include_variations' => (bool) $include_variations,
						'priority'           => $idx + 1,
						'products'           => array(),
					);

					foreach ( $product_products[ $key ] as $product_id ) {
						$addon['products'][] = $product_id;
					}

					$product_addons[] = $addon;
				}
			}
		}

		$tmp = array();
		foreach ( $category_addons as $key => $row ) {
			$tmp[ $key ] = $row['priority'];
		}
		array_multisort( $tmp, SORT_ASC, $category_addons );

		$tmp = array();
		foreach ( $product_addons as $key => $row ) {
			$tmp[ $key ] = $row['priority'];
		}
		array_multisort( $tmp, SORT_ASC, $product_addons );

		update_option( 'sfn_cart_addons_categories', $category_addons );
		update_option( 'sfn_cart_addons_products', $product_addons );

		wp_safe_redirect( 'admin.php?page=sfn-cart-addons&updated=1' );
		exit;
	}

	/**
	 * AJAX method to check if the given $_GET['product_id'] is a variable product
	 */
	public function ajax_product_is_variable() {
		check_ajax_referer( 'sfn-settings', 'security' );

		ob_start();

		$is_variable = false;
		$type        = '';
		$product_id  = absint( $_GET['product_id'] );
		$product     = wc_get_product( $product_id );

		if ( $product->is_type( 'variable' ) ) {
			$is_variable = true;
		}

		ob_end_clean();

		die( wp_json_encode( array( 'is_variable' => $is_variable ) ) );
	}

	/**
	 * csv_to_array
	 *
	 * Used for backwards compatibility with the switch from Chosen to Select2.
	 * Converts array{0: "1,2,3"} to array{0: "1", 1: "2", 2: "3"}
	 *
	 * @param array|string $array Array of data.
	 * @return array
	 */
	public function csv_to_array( $array ) {
		$new_array = $array;

		if ( empty( $array ) ) {
			return $array;
		}

		if ( 1 === count( $array ) && strpos( $array[0], ',' ) !== false ) {
			$new_array = explode( ',', $array[0] );
		}

		return array_map( 'absint', $new_array );
	}

	public function cart_display_addons() {
		$this->display_addons( null, 'loop', 0, false );
	}

	public function display_addons( $length = null, $display_mode = 'loop', $add_to_cart = 0, $return = true ) {
		global $wpdb;

		if ( ! WC()->cart ) {
			return;
		}

		$add_to_cart = (bool) $add_to_cart;
		$settings    = get_option(
			'sfn_cart_addons',
			array(
				'header_title'   => __( 'Product Add-ons', 'sfn_cart_addons' ),
				'default_addons' => array(),
			)
		);
		$addon       = false;
		$args        = false;
		$addon_ids   = array();
		$contents    = WC()->cart->cart_contents;

		if ( ! is_null( $length ) && ! empty( $length ) ) {
			$max = $length;
		} else {
			$max = isset( $settings['upsell_number'] ) ? (int) $settings['upsell_number'] : false;
		}

		// Extract all the product ids from the cart.
		$products_in_cart = array();

		foreach ( $contents as $product ) {
			$products_in_cart[] = isset( $product['variation_id'] ) && $product['variation_id'] > 0 ? $product['variation_id'] : $product['product_id'];
		}

		// Search for product matches.
		$product_addons = get_option( 'sfn_cart_addons_products', array() );

		foreach ( $product_addons as $addons ) {

			if ( isset( $addons['include_variations'] ) && $addons['include_variations'] ) {
				$product = function_exists( 'wc_get_product' ) ? wc_get_product( $addons['product_id'] ) : new WC_Product( $addons['product_id'] );

				if ( ! $product ) {
					continue;
				}

				$children = $product->get_children();

				foreach ( $children as $child_id ) {
					if ( in_array( $child_id, $products_in_cart ) ) {
						foreach ( $addons['products'] as $pid ) {
							$addon_ids[] = $pid;
						}
					}
				}
			} else {
				if ( in_array( $addons['product_id'], $products_in_cart ) ) {
					foreach ( $addons['products'] as $pid ) {
						$addon_ids[] = $pid;
					}
				}
			}
		}

		// Search for category matches.
		$all_categories = array();

		foreach ( $products_in_cart as $product_id ) {
			$product_cats = $this->get_product_categories( $product_id );

			if ( ! empty( $product_cats ) ) {
				$all_categories = array_merge( $all_categories, $product_cats );
			}
		}

		$category_addons = get_option( 'sfn_cart_addons_categories', array() );
		foreach ( $category_addons as $addons ) {
			if ( in_array( $addons['category_id'], $all_categories ) ) {
				foreach ( $addons['products'] as $pid ) {
					$addon_ids[] = $pid;
				}
			}
		}

		// Default addons.
		if ( false !== $max && empty( $addon_ids ) ) {
			$default_addons = $settings['default_addons'];

			$addon_ids = $default_addons;
		}

		$args = false;
		if ( ! empty( $addon_ids ) ) {
			// Remove the products that are already in the cart.
			foreach ( $addon_ids as $idx => $prod_id ) {
				if ( in_array( $prod_id, $products_in_cart ) ) {
					unset( $addon_ids[ $idx ] );
				}
			}

			if ( ! empty( $addon_ids ) ) {
				$args = array(
					'post_type'      => array( 'product', 'product_variation' ),
					'post__in'       => $addon_ids,
					'posts_per_page' => $max,
					'orderby'        => apply_filters( 'woocommerce_cart_add_ons_orderby', null ),
					'order'          => apply_filters( 'woocommerce_cart_add_ons_order', null ),
				);
			} else {
				$args = false;
			}
		}

		// No addons to display!
		if ( false === $args ) {
			return;
		}

		$loop = new WP_Query( $args );

		// Output buffering, we need to return the output.
		ob_start();

		if ( 'loop' === $display_mode ) {
			do_action( 'woocommerce_before_shop_loop' );

			?>
			<div class="woocommerce sfn-cart-addons">
				<h2><?php echo esc_html( $settings['header_title'] ); ?></h2>
				<ul class="products sfn-cart-addons">
					<?php
					do_action( 'woocommerce_before_shop_loop_products' );
					$x = 0;

					if ( $loop->have_posts() ) :
						while ( $loop->have_posts() ) :
							$loop->the_post();
							global $product;
							$product = wc_get_product( get_the_ID() );
							if ( ! $product || ! $product->is_visible() ) {
								continue;
							}
							wc_get_template( 'content-product.php', array( 'product' => $product ) );
						endwhile;
					endif;
					?>

				</ul>

				<div style="clear:both; height:1px;"></div>
			</div>
			<?php
			do_action( 'woocommerce_after_shop_loop' );
		} elseif ( 'images' === $display_mode ) {
			?>
			<div class="woocommerce sfn-cart-addons-images">
				<ul class="products sfn-cart-addons">
					<?php
					$x = 0;
					if ( $loop->have_posts() ) :
						while ( $loop->have_posts() ) :
							$loop->the_post();
							global $product;
							$product = wc_get_product( get_the_ID() );
							if ( ! $product || ! $product->is_visible() ) {
								continue;
							}
							echo '<li class="product"><a href="' . esc_url( get_permalink( $product->get_id() ) ) . '">' . woocommerce_get_product_thumbnail() . '</a>';
							if ( $add_to_cart ) {
								woocommerce_template_loop_add_to_cart();
							}
							echo '</li>';
						endwhile;
					endif;
					?>
				</ul>
			</div>
			<?php
		} elseif ( 'images_name' === $display_mode ) {
			?>
			<div class="woocommerce sfn-cart-addons-images">
				<ul class="products sfn-cart-addons">
					<?php
					$x = 0;
					if ( $loop->have_posts() ) :
						while ( $loop->have_posts() ) :
							$loop->the_post();
							global $product;
							$product = wc_get_product( get_the_ID() );
							if ( ! $product || ! $product->is_visible() ) {
								continue;
							}
							echo '<li class="product">
								<a href="' . esc_url( get_permalink( $product->get_id() ) ) . '">' . woocommerce_get_product_thumbnail() . '</a>
								<h2 class="woocommerce-loop-product__title"><a href="' . esc_url( get_permalink( $product->get_id() ) ) . '">' . wp_kses_post( get_the_title() ) . '</a></h2>';

							if ( $add_to_cart ) {
								woocommerce_template_loop_add_to_cart();
							}

							echo '</li>';
						endwhile;
					endif;
					?>
				</ul>
			</div>
			<?php
		} elseif ( 'images_name_price' === $display_mode ) {
			?>
			<div class="woocommerce sfn-cart-addons-images">
				<ul class="products sfn-cart-addons">
					<?php
					$x = 0;
					if ( $loop->have_posts() ) :
						while ( $loop->have_posts() ) :
							$loop->the_post();
							global $product;
							$product = wc_get_product( get_the_ID() );
							if ( ! $product || ! $product->is_visible() ) {
								continue;
							}

							echo '<li class="product"><a href="' . esc_url( get_permalink( $product->get_id() ) ) . '">' . woocommerce_get_product_thumbnail() . '</a>
							<h2 class="woocommerce-loop-product__title"><a href="' . esc_url( get_permalink( $product->get_id() ) ) . '">' . wp_kses_post( get_the_title() ) . '</a></h2> <span class="price">' . wc_price( $product->get_price() ) . '</span>';


							if ( $add_to_cart ) {
								woocommerce_template_loop_add_to_cart();
							}

							echo '</li>';
						endwhile;
					endif;
					?>
				</ul>
			</div>
			<?php
		} elseif ( 'names' === $display_mode ) {
			?>
			<div class="woocommerce sfn-cart-addons-names">
				<ul class="products sfn-cart-addons">
					<?php
					$x = 0;
					if ( $loop->have_posts() ) :
						while ( $loop->have_posts() ) :
							$loop->the_post();
							global $product;

							$product = wc_get_product( get_the_ID() );

							if ( ! $product || ! $product->is_visible() ) {
								continue;
							}

							echo '<li class="product"><a href="' . esc_url( get_permalink( $product->get_id() ) ) . '">' . wp_kses_post( get_the_title() ) . '</a>';

							if ( $add_to_cart ) {
								woocommerce_template_loop_add_to_cart();
							}

							echo '</li>';
						endwhile;
					endif;
					?>
				</ul>
			</div>
			<?php
		} elseif ( 'names_price' === $display_mode ) {
			?>
			<div class="woocommerce sfn-cart-addons-names">
				<ul class="products sfn-cart-addons">
					<?php
					$x = 0;
					if ( $loop->have_posts() ) :
						while ( $loop->have_posts() ) :
							$loop->the_post();
							global $product;
							$product = wc_get_product( get_the_ID() );

							if ( ! $product || ! $product->is_visible() ) {
								continue;
							}
							echo '<li class="product"><a href="' . esc_url( get_permalink( $product->get_id() ) ) . '">' . wp_kses_post( get_the_title() ) . ' ' . wc_price( $product->get_price() ) . '</a>';

							if ( $add_to_cart ) {
								woocommerce_template_loop_add_to_cart();
							}

							echo '</li>';
						endwhile;
					endif;
					?>
				</ul>
			</div>
			<?php
		}

		$content = ob_get_clean();

		// Reset data.
		wp_reset_postdata();

		if ( $return ) {
			return $content;
		} else {
			echo $content;
		}

	}

	public function sc_display_addons( $atts ) {
		$attributes = shortcode_atts(
			array(
				'length'      => 4,
				'mode'        => 'loop',
				'add_to_cart' => 0,
			),
			$atts
		);
		return $this->display_addons( $attributes['length'], $attributes['mode'], $attributes['add_to_cart'] );
	}

	public function post_class( $classes ) {
		if ( in_array( 'product_variation', $classes ) ) {
			$classes[] = 'product';
		}

		return $classes;
	}

	public function the_title( $title, $id = null ) {
		global $post;

		if ( is_null( $id ) ) {
			$id = $post->ID;
		}

		$product = wc_get_product( $id );

		if ( $product && $product->is_type( 'variation' ) ) {
			$attributes = $product->get_variation_attributes();
			$extra_data = ' &ndash; ' . implode( ', ', $attributes );

			$title = sprintf( '%1$s%2$s', $product->get_title(), $extra_data );
		}

		return $title;
	}

	public function the_permalink( $permalink ) {
		global $product;

		if ( $product && $product->is_type( 'variation' ) ) {
			return get_permalink( $product->get_id() );
		}

		return $permalink;
	}

	public function get_product_categories( $product_id ) {

		$product    = wc_get_product( $product_id );
		$parent_id  = $product->get_parent_id();
		$categories = array();

		// If product is a variation, use the parent product.
		if ( $parent_id > 0 ) {
			$product_id = $parent_id;
		}

		$terms = wp_get_post_terms( $product_id, 'product_cat' );

		if ( is_array( $terms ) && count( $terms ) > 0 ) {
			foreach ( $terms as $term ) {
				$categories[] = $term->term_id;
			}
		}

		return $categories;
	}

}

// Plugin init hook.
add_action( 'plugins_loaded', 'wc_cart_addons_init' );

/**
 * Initialize plugin.
 */
function wc_cart_addons_init() {
	load_plugin_textdomain( 'sfn_cart_addons', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

	if ( ! class_exists( 'WooCommerce' ) ) {
		add_action( 'admin_notices', 'wc_cart_addons_woocommerce_deactivated' );
		return;
	}

	global $sfn_cart_addons;
	$sfn_cart_addons = new SFN_Cart_Addons();
}

/**
 * WooCommerce Deactivated Notice.
 */
function wc_cart_addons_woocommerce_deactivated() {
	/* translators: %s: WooCommerce link */
	echo '<div class="error"><p>' . sprintf( esc_html__( 'WooCommerce Cart Add-ons requires %s to be installed and active.', 'sfn_cart_addons' ), '<a href="https://woocommerce.com/" target="_blank">WooCommerce</a>' ) . '</p></div>';
}

function sfn_display_cart_addons( $length = 4, $display_mode = 'loop', $add_to_cart = 0 ) {
	global $sfn_cart_addons;

	if ( isset( $sfn_cart_addons ) ) {
		echo $sfn_cart_addons->display_addons( $length, $display_mode, $add_to_cart );
	}
}

if ( class_exists( 'Automattic\WooCommerce\Blocks\Package' ) && version_compare( \Automattic\WooCommerce\Blocks\Package::get_version(), '7.2.1', '>=' ) ) {
	// This class needs to run after WooCommerce Blocks is ready.
	add_action(
		'woocommerce_blocks_loaded',
		function() {
			require_once dirname( __FILE__ ) . '/includes/class-woocommerce-cart-addons-blocks.php';
			new WooCommerce_Cart_Addons_Blocks();
		}
	);
}
