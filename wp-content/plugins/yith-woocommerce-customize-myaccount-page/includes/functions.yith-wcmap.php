<?php
/**
 * Plugins Functions and Hooks
 *
 * @author  YITH
 * @package YITH WooCommerce Customize My Account Page
 * @version 1.0.0
 */

defined( 'YITH_WCMAP' ) || exit; // Exit if accessed directly.

if ( ! function_exists( 'YITH_WCMAP_Admin' ) ) {
	/**
	 * The admin class
	 *
	 * @since  2.5.0
	 * @author Francesco Licandro
	 * @return YITH_WCMAP_Admin|null
	 */
	function YITH_WCMAP_Admin() { // phpcs:ignore
		return YITH_WCMAP()->admin;
	}
}

if ( ! function_exists( 'YITH_WCMAP_Frontend' ) ) {
	/**
	 * The frontend class
	 *
	 * @since  2.5.0
	 * @author Francesco Licandro
	 * @return YITH_WCMAP_Frontend|null
	 */
	function YITH_WCMAP_Frontend() { // phpcs:ignore
		return YITH_WCMAP()->frontend;
	}
}

if ( ! function_exists( 'yith_wcmap_sanitize_item_key' ) ) {
	/**
	 * Sanitize an item key/slug
	 *
	 * @access public
	 * @since  1.0.0
	 * @author Francesco Licandro
	 * @param string $key The item key to sanitize.
	 * @return string
	 */
	function yith_wcmap_sanitize_item_key( $key ) {
		// Build endpoint key.
		$field_key = strtolower( $key );
		$field_key = trim( $field_key );
		// Clear from space and add -.
		$field_key = sanitize_title( $field_key, '' );

		return $field_key;
	}
}

if ( ! function_exists( 'yith_wcmap_build_label' ) ) {
	/**
	 * Build endpoint label by name
	 *
	 * @since  2.0.0
	 * @author Francesco Licandro
	 * @param string $name The name to use for the label.
	 * @return string
	 */
	function yith_wcmap_build_label( $name ) {

		$label = preg_replace( '/[^a-z]/', ' ', $name );
		$label = trim( $label );
		$label = ucfirst( $label );

		return $label;
	}
}

if ( ! function_exists( 'yith_wcmap_get_default_endpoint_options' ) ) {
	/**
	 * Get default options for new endpoints
	 *
	 * @since  2.0.0
	 * @author Francesco Licandro
	 * @param string $endpoint The endpoint key.
	 * @return array
	 */
	function yith_wcmap_get_default_endpoint_options( $endpoint ) {

		$endpoint_name = yith_wcmap_build_label( $endpoint );

		// Build endpoint options.
		$options = array(
			'slug'             => $endpoint,
			'active'           => true,
			'label'            => $endpoint_name,
			'icon_type'        => 'default',
			'icon'             => '',
			'custom_icon'      => '',
			'class'            => '',
			'content'          => '',
			'visibility'       => 'all',
			'content_position' => 'override',
		);

		return apply_filters( 'yith_wcmap_get_default_endpoint_options', $options, $endpoint );
	}
}

if ( ! function_exists( 'yith_wcmap_get_default_group_options' ) ) {
	/**
	 * Get default options for new group
	 *
	 * @since  2.0.0
	 * @author Francesco Licandro
	 * @param string $group The group key.
	 * @return array
	 */
	function yith_wcmap_get_default_group_options( $group ) {

		$group_name = yith_wcmap_build_label( $group );

		// Build endpoint options.
		$options = array(
			'active'      => true,
			'label'       => $group_name,
			'usr_roles'   => '',
			'icon_type'   => 'default',
			'icon'        => '',
			'custom_icon' => '',
			'class'       => '',
			'open'        => true,
			'visibility'  => 'all',
			'children'    => array(),
		);

		return apply_filters( 'yith_wcmap_get_default_group_options', $options, $group );
	}
}

if ( ! function_exists( 'yith_wcmap_get_default_link_options' ) ) {
	/**
	 * Get default options for new links
	 *
	 * @since  2.3.0
	 * @author Francesco Licandro
	 * @param string $endpoint The endpoint link key.
	 * @return array
	 */
	function yith_wcmap_get_default_link_options( $endpoint ) {

		$endpoint_name = yith_wcmap_build_label( $endpoint );
		// Build endpoint options.
		$options = array(
			'url'          => '#',
			'active'       => true,
			'label'        => $endpoint_name,
			'icon_type'    => 'default',
			'icon'         => '',
			'custom_icon'  => '',
			'class'        => '',
			'visibility'   => 'all',
			'target_blank' => false,
		);

		return apply_filters( 'yith_wcmap_get_default_link_options', $options, $endpoint );
	}
}

if ( ! function_exists( 'yith_wcmap_is_default_item' ) ) {
	/**
	 * Check if an item is a default
	 *
	 * @since  2.4.0
	 * @author Francesco Licandro
	 * @param string $item The item key to check if is default or not.
	 * @return boolean
	 */
	function yith_wcmap_is_default_item( $item ) {
		$defaults = YITH_WCMAP()->items->get_default_items();

		return array_key_exists( $item, $defaults );
	}
}

if ( ! function_exists( 'yith_wcmap_item_already_exists' ) ) {
	/**
	 * Check if item already exists
	 *
	 * @since  2.4.0
	 * @author Francesco Licandro
	 * @param string $endpoint The endpoint key to check if exists or not.
	 * @return boolean
	 */
	function yith_wcmap_item_already_exists( $endpoint ) {

		// Check first in key.
		$field_key = YITH_WCMAP()->items->get_items_keys();
		$exists    = in_array( $endpoint, $field_key, true );

		// Check also in slug.
		if ( ! $exists ) {
			$endpoint_slug = YITH_WCMAP()->items->get_items_slug();
			$exists        = in_array( $endpoint, $endpoint_slug, true );
		}

		return $exists;
	}
}

if ( ! function_exists( 'yith_wcmap_get_current_endpoint' ) ) {
	/**
	 * Check if and endpoint is active on frontend. Used for add class 'active' on account menu in frontend
	 *
	 * @since  2.0.0
	 * @author Francesco Licandro
	 * @return string
	 */
	function yith_wcmap_get_current_endpoint() {

		global $wp;

		$current = '';
		foreach ( WC()->query->get_query_vars() as $key => $value ) {
			// Check for dashboard.
			if ( isset( $wp->query_vars['page'] ) || empty( $wp->query_vars ) ) {
				$current = 'dashboard';
				break;
			} elseif ( isset( $wp->query_vars[ $key ] ) ) {
				$current = $key;
				break;
			}
		}

		return apply_filters( 'yith_wcmap_get_current_endpoint', $current );
	}
}

if ( ! function_exists( 'yith_wcmap_endpoints_list' ) ) {
	/**
	 * Get endpoints slugs for register endpoints
	 *
	 * @since  2.0.0
	 * @author Francesco Licandro
	 * @return array
	 */
	function yith_wcmap_endpoints_list() {

		$return = array();
		$fields = YITH_WCMAP()->items->get_items();

		foreach ( $fields as $key => $field ) {
			if ( isset( $field['children'] ) ) {
				foreach ( $field['children'] as $child_key => $child ) {
					if ( isset( $child['slug'] ) ) {
						$return[ $child_key ] = $child['label'];
					}
				}
				continue;
			}

			if ( isset( $field['slug'] ) ) {
				$return[ $key ] = $field['label'];
			}
		}

		return $return;
	}
}

if ( ! function_exists( 'yith_wcmap_get_endpoint_by' ) ) {
	/**
	 * Get endpoint by a specified key
	 *
	 * @since  2.0.0
	 * @author Francesco Licandro
	 * @param string $value The value to search.
	 * @param string $key The value type. Can be key or slug.
	 * @param array  $items Endpoint array.
	 * @return array
	 */
	function yith_wcmap_get_endpoint_by( $value, $key = 'key', $items = array() ) {

		$accepted = apply_filters( 'yith_wcmap_get_endpoint_by_accepted_key', array( 'key', 'slug' ) );

		if ( ! in_array( $key, $accepted, true ) ) {
			return array();
		}

		if ( empty( $items ) ) {
			$items = YITH_WCMAP()->items->get_items();
		}
		$find = array();

		foreach ( $items as $id => $item ) {
			if ( ( 'key' === $key && $id === $value ) || ( isset( $item[ $key ] ) && $item[ $key ] === $value ) ) {
				$find[ $id ] = $item;
				continue;
			} elseif ( isset( $item['children'] ) ) {
				foreach ( $item['children'] as $child_id => $child ) {
					if ( ( 'key' === $key && $child_id === $value ) || ( isset( $child[ $key ] ) && $child[ $key ] === $value ) ) {
						$find[ $child_id ] = $child;
						continue;
					}
				}
				continue;
			}
		}

		return apply_filters( 'yith_wcmap_get_endpoint_by_result', $find );
	}
}

if ( ! function_exists( 'yith_wcmap_print_single_endpoint' ) ) {
	/**
	 * Print single endpoint on front menu
	 *
	 * @since  2.0.0
	 * @author Francesco Licandro
	 * @param string $endpoint The endpoint to print.
	 * @param array  $options The endpoint options.
	 * @deprecated
	 */
	function yith_wcmap_print_single_endpoint( $endpoint, $options ) {
		YITH_WCMAP_Frontend()->print_single_item( $endpoint, $options );
	}
}

if ( ! function_exists( 'yith_wcmap_print_endpoints_group' ) ) {
	/**
	 * Print endpoints group on front menu
	 *
	 * @since  2.0.0
	 * @author Francesco Licandro
	 * @param string $endpoint The endpoint group to print.
	 * @param array  $options The group options.
	 */
	function yith_wcmap_print_endpoints_group( $endpoint, $options ) {
		YITH_WCMAP_Frontend()->print_items_group( $endpoint, $options );
	}
}

if ( ! function_exists( 'yith_wcmap_generate_avatar_path' ) ) {
	/**
	 * Generate avatar path
	 *
	 * @param integer|string $attachment_id The avatar attachment ID.
	 * @param integer|string $size The avatar size.
	 * @return string
	 */
	function yith_wcmap_generate_avatar_path( $attachment_id, $size ) {
		// Retrieves attached file path based on attachment ID.
		$filename = get_attached_file( $attachment_id );

		$pathinfo  = pathinfo( $filename );
		$dirname   = $pathinfo['dirname'];
		$extension = $pathinfo['extension'];

		// i18n friendly version of basename().
		$basename = wp_basename( $filename, '.' . $extension );

		$suffix    = $size . 'x' . $size;
		$dest_path = $dirname . '/' . $basename . '-' . $suffix . '.' . $extension;

		return $dest_path;
	}
}

if ( ! function_exists( 'yith_wcmap_generate_avatar_url' ) ) {
	/**
	 * Generate avatar url
	 *
	 * @param integer|string $attachment_id The avatar attachment ID.
	 * @param integer|string $size The avatar size.
	 * @return mixed
	 */
	function yith_wcmap_generate_avatar_url( $attachment_id, $size ) {
		// Retrieves path information on the currently configured uploads directory.
		$upload_dir = wp_upload_dir();

		// Generates a file path of an avatar image based on attachment ID and size.
		$path = yith_wcmap_generate_avatar_path( $attachment_id, $size );

		return str_replace( $upload_dir['basedir'], $upload_dir['baseurl'], $path );
	}
}

if ( ! function_exists( 'yith_wcmap_resize_avatar_url' ) ) {
	/**
	 * Resize avatar
	 *
	 * @param integer|string $attachment_id The avatar attachment ID.
	 * @param integer|string $size The avatar size.
	 * @return boolean
	 */
	function yith_wcmap_resize_avatar_url( $attachment_id, $size ) {

		$dest_path = yith_wcmap_generate_avatar_path( $attachment_id, $size );

		if ( file_exists( $dest_path ) ) {
			$resize = true;
		} else {
			// Retrieves attached file path based on attachment ID.
			$path = get_attached_file( $attachment_id );

			// Retrieves a WP_Image_Editor instance and loads a file into it.
			$image = wp_get_image_editor( $path );

			if ( ! is_wp_error( $image ) ) {

				// Resizes current image.
				$image->resize( $size, $size, true );

				// Saves current image to file.
				$image->save( $dest_path );

				// Store media size to clear resized image on media delete.
				$media_sizes = get_post_meta( $attachment_id, '_wp_attachment_backup_sizes', true );
				if ( empty( $media_sizes ) ) {
					$media_sizes = array();
				}

				$media_sizes[ 'ywcmap-avatar-' . $size ] = array(
					'width'  => $size,
					'height' => $size,
					'file'   => pathinfo( $dest_path, PATHINFO_BASENAME ),
				);
				update_post_meta( $attachment_id, '_wp_attachment_backup_sizes', $media_sizes );

				$resize = true;

			} else {
				$resize = false;
			}
		}

		return $resize;
	}
}

if ( ! function_exists( 'yith_wcmap_is_plugin_item' ) ) {
	/**
	 * Check if an item is a plugin
	 *
	 * @since  2.4.0
	 * @author Francesco Licandro
	 * @param string $item The item key to check if is a plugin or not.
	 * @return boolean
	 */
	function yith_wcmap_is_plugin_item( $item ) {
		$plugins = YITH_WCMAP()->items->get_plugins_items();

		return array_key_exists( $item, $plugins );
	}
}

if ( ! function_exists( 'yith_wcmap_get_icon_list' ) ) {
	/**
	 * Get FontAwesome icon list
	 *
	 * @since  2.2.3
	 * @author Francesco Licandro
	 * @return array
	 */
	function yith_wcmap_get_icon_list() {

		$icons_list = array();

		if ( file_exists( YITH_WCMAP_DIR . 'plugin-options/icon-list.php' ) ) {
			$icons = include YITH_WCMAP_DIR . 'plugin-options/icon-list.php';
			foreach ( $icons as $id => $text ) {
				$icons_list[] = array(
					'id'   => $text,
					'text' => $text,
				);
			}
		}

		return $icons_list;
	}
}

if ( ! function_exists( 'yith_wcmap_get_custom_css' ) ) {
	/**
	 * Get plugin custom css style
	 *
	 * @since  2.3.0
	 * @author Francesco Licandro
	 * @return string
	 */
	function yith_wcmap_get_custom_css() {
		// Init variables.
		$variables = array();

		// Logout button colors.
		$logout_colors = get_option(
			'yith_wcmap_logout_button_color',
			array(
				'text_normal'       => '#ffffff',
				'text_hover'        => '#ffffff',
				'background_normal' => '#c0c0c0',
				'background_hover'  => '#333333',
			)
		);

		$variables['logout-text-color']             = $logout_colors['text_normal'];
		$variables['logout-text-color-hover']       = $logout_colors['text_hover'];
		$variables['logout-background-color']       = $logout_colors['background_normal'];
		$variables['logout-background-color-hover'] = $logout_colors['background_hover'];

		// Menu items colors.
		$items_text_colors = get_option(
			'yith_wcmap_text_color',
			array(
				'normal' => '#777777',
				'hover'  => '#000000',
				'active' => '#000000',
			)
		);

		$variables['items-text-color']        = $items_text_colors['normal'];
		$variables['items-text-color-hover']  = $items_text_colors['hover'];
		$variables['items-text-color-active'] = isset( $items_text_colors['active'] ) ? $items_text_colors['active'] : $items_text_colors['hover'];

		// Menu items background.
		$items_background_colors = get_option(
			'yith_wcmap_background_color',
			array(
				'normal' => '#ffffff',
				'hover'  => '#ffffff',
				'active' => '#ffffff',
			)
		);

		$variables['items-background-color']        = $items_background_colors['normal'];
		$variables['items-background-color-hover']  = $items_background_colors['hover'];
		$variables['items-background-color-active'] = $items_background_colors['active'];

		// Menu font size.
		$variables['font-size'] = absint( get_option( 'yith_wcmap_font_size', 16 ) ) . 'px';

		// Menu background.
		$variables['menu-background'] = get_option( 'yith_wcmap_menu_background_color', '#f4f4f4' );

		// Menu border color.
		$variables['menu-border-color'] = get_option( 'yith_wcmap_menu_border_color', '#e0e0e0' );

		// Modern menu border color yith_wcmap_menu_item_shadow_color.
		$items_border_colors = get_option(
			'yith_wcmap_menu_item_border_color',
			array(
				'normal' => '#eaeaea',
				'hover'  => '#cceae9',
				'active' => '#cceae9',
			)
		);

		$variables['items-border-color']        = $items_border_colors['normal'];
		$variables['items-border-color-hover']  = $items_border_colors['hover'];
		$variables['items-border-color-active'] = $items_border_colors['active'];

		// Modern menu shadow color yith_wcmap_menu_item_shadow_color.
		$items_shadow_colors = get_option(
			'yith_wcmap_menu_item_shadow_color',
			array(
				'normal' => 'rgba(114, 114, 114, 0.16)',
				'hover'  => 'rgba(3,163,151,0.16)',
				'active' => 'rgba(3,163,151,0.16)',
			)
		);

		$variables['items-shadow-color']        = $items_shadow_colors['normal'];
		$variables['items-shadow-color-hover']  = $items_shadow_colors['hover'];
		$variables['items-shadow-color-active'] = $items_shadow_colors['active'];

		// Avatar style.
		$avatar_options = get_option( 'yith_wcmap_avatar', array() );
		if ( ! empty( $avatar_options['border_radius'] ) ) {
			$variables['avatar-border-radius'] = ( intval( $avatar_options['border_radius'] ) * 5 ) . '%';
		}

		// Items padding.
		$items_padding = get_option( 'yith_wcmap_items_padding', array() );
		if ( ! empty( $items_padding['dimensions'] ) ) {
			// Build item padding values.
			foreach ( $items_padding['dimensions'] as &$value ) {
				$value .= ! empty( $items_padding['unit'] ) ? $items_padding['unit'] : 'px';
			}
			$variables['menu-items-padding'] = implode( ' ', $items_padding['dimensions'] ) . ';';
		}

		$variables = apply_filters( 'yith_wcmap_custom_css_variables', array_filter( $variables ) );
		if ( empty( $variables ) ) {
			return '';
		}

		$inline_css = ':root {';
		foreach ( $variables as $key => $value ) {
			$inline_css .= '--ywcmap-' . $key . ': ' . $value . ';';
		}
		$inline_css .= '}';
		// Remove whitespaces and line breaks.
		$inline_css = trim( preg_replace( '/\s\s+/', ' ', $inline_css ) );

		return apply_filters( 'yith_wcmap_get_custom_css', $inline_css );
	}
}

if ( ! function_exists( 'yith_wcmap_users_can_upload_avatar' ) ) {
	/**
	 * Check if users are able to upload their own avatar image
	 *
	 * @since 3.0.0
	 * @author Francesco Licandro
	 * @return boolean
	 */
	function yith_wcmap_users_can_upload_avatar() {
		return YITH_WCMAP_Avatar::can_upload_avatar();
	}
}

if ( ! function_exists( 'yith_wcmap_get_menu_item_icon_html' ) ) {
	/**
	 * Get the html of the menu icon for the given item
	 *
	 * @since 3.0.0
	 * @author Francesco Licandro
	 * @param array $item_options The item options array.
	 * @return string
	 */
	function yith_wcmap_get_menu_item_icon_html( $item_options ) {
		if ( empty( $item_options['icon_type'] ) || 'empty' === $item_options['icon_type'] ) {
			return '';
		}

		$html = '<span class="item-icon">';

		if ( 'custom' === $item_options['icon_type'] ) {

			// If it is an svg try to get the svg content.
			$ext = strtolower( substr( strrchr( $item_options['custom_icon'], '.' ), 1 ) );
			if ( 'svg' === $ext ) {
				$svg_file    = file_get_contents( $item_options['custom_icon'] ); // phpcs:ignore.
				$find_string = '<svg';
				$position    = strpos( $svg_file, $find_string );
				if ( false !== $position ) {
					$svg_tag = substr( $svg_file, $position );
				}
			}

			$html .= ! empty( $svg_tag ) ? $svg_tag : '<img src="' . esc_url( $item_options['custom_icon'] ) . '">';
		} else {
			$icon  = false === strpos( $item_options['icon'], 'fa-' ) ? 'fa-' . $item_options['icon'] : $item_options['icon'];
			$html .= '<i class="fa ' . esc_attr( $icon ) . '"></i>';
		}

		$html .= '</span>';

		return $html;
	}
}


