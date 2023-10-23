<?php
/**
 * Plugins Functions and Hooks
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH WooCommerce Customize My Account Page
 * @version 1.0.0
 */

defined( 'YITH_WCMAP' ) || exit; // Exit if accessed directly.

if ( ! function_exists( 'yith_wcmap_get_default_group_options' ) ) {
	/**
	 * Get default options for new group
	 *
	 * @since  2.0.0
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

		/**
		 * APPLY_FILTERS: yith_wcmap_get_default_group_options
		 *
		 * Filters the default options for new groups.
		 *
		 * @param array  $options Group options.
		 * @param string $group   Group key.
		 *
		 * @return array
		 */
		return apply_filters( 'yith_wcmap_get_default_group_options', $options, $group );
	}
}

if ( ! function_exists( 'yith_wcmap_get_default_link_options' ) ) {
	/**
	 * Get default options for new links
	 *
	 * @since  2.3.0
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

		/**
		 * APPLY_FILTERS: yith_wcmap_get_default_link_options
		 *
		 * Filters the default options for new links.
		 *
		 * @param array  $options  Link options.
		 * @param string $endpoint Endpoint link key.
		 *
		 * @return array
		 */
		return apply_filters( 'yith_wcmap_get_default_link_options', $options, $endpoint );
	}
}

if ( ! function_exists( 'yith_wcmap_print_endpoints_group' ) ) {
	/**
	 * Print endpoints group on front menu
	 *
	 * @since  2.0.0
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

if ( ! function_exists( 'yith_wcmap_users_can_upload_avatar' ) ) {
	/**
	 * Check if users are able to upload their own avatar image
	 *
	 * @since 3.0.0
	 * @return boolean
	 */
	function yith_wcmap_users_can_upload_avatar() {
		return YITH_WCMAP_Avatar::can_upload_avatar();
	}
}

if ( ! function_exists( 'yith_wcmap_get_icon_list' ) ) {
	/**
	 * Get FontAwesome icon list
	 *
	 * @since  2.2.3
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

if ( ! function_exists( 'yith_wcmap_get_menu_item_icon_html' ) ) {
	/**
	 * Get the html of the menu icon for the given item
	 *
	 * @since 3.0.0
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
