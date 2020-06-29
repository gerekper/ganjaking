<?php

namespace GroovyMenu;

use \Walker_Nav_Menu as Walker_Nav_Menu;
use \GroovyMenuUtils as GroovyMenuUtils;


defined( 'ABSPATH' ) || die( 'This script cannot be accessed directly.' );


/**
 * Class WalkerNavMenu
 */
class WalkerNavMenu extends Walker_Nav_Menu {

	const GM_NAV_MENU_META              = 'groovy_menu_nav_menu_meta';
	const IS_MEGAMENU_META              = 'groovy_menu_is_megamenu';
	const DO_NOT_SHOW_TITLE             = 'groovy_menu_do_not_show_title';
	const MEGAMENU_META_COLS            = 'groovy_menu_megamenu_cols';
	const MENU_BLOCK_URL                = 'groovy_menu_block_url';
	const MEGAMENU_META_POST            = 'groovy_menu_megamenu_post';
	const MEGAMENU_META_POST_NOT_MOBILE = 'groovy_menu_megamenu_post_not_mobile';
	const IS_SHOW_FEATURED_IMAGE        = 'groovy_menu_is_show_featured_image';
	const ICON_CLASS                    = 'groovy_menu_icon_class';
	const MEGAMENU_BACKGROUND           = 'groovy_menu_megamenu_background';
	const MEGAMENU_BACKGROUND_POSITION  = 'groovy_menu_megamenu_background_position';
	const MEGAMENU_BACKGROUND_REPEAT    = 'groovy_menu_megamenu_background_repeat';
	const MEGAMENU_BACKGROUND_SIZE      = 'groovy_menu_megamenu_background_size';
	const GM_THUMB_ENABLE               = 'groovy_menu_thumb_enable';
	const GM_THUMB_POSITION             = 'groovy_menu_thumb_position';
	const GM_THUMB_MAX_HEIGHT           = 'groovy_menu_thumb_max_height';
	const GM_THUMB_WITH_URL             = 'groovy_menu_thumb_with_url';
	const GM_THUMB_IMAGE                = 'groovy_menu_thumb_image';
	const GM_BADGE_ENABLE               = 'groovy_menu_badge_enable';
	const GM_BADGE_TYPE                 = 'groovy_menu_badge_type';
	const GM_BADGE_PLACEMENT            = 'groovy_menu_badge_placement';
	const GM_BADGE_GENERAL_POSITION     = 'groovy_menu_badge_general_position';
	const GM_BADGE_Y_POSITION           = 'groovy_menu_badge_y_position';
	const GM_BADGE_X_POSITION           = 'groovy_menu_badge_x_position';
	const GM_BADGE_IMAGE                = 'groovy_menu_badge_image';
	const GM_BADGE_IMAGE_HEIGHT         = 'groovy_menu_badge_image_height';
	const GM_BADGE_IMAGE_WIDTH          = 'groovy_menu_badge_image_width';
	const GM_BADGE_ICON                 = 'groovy_menu_badge_icon';
	const GM_BADGE_ICON_SIZE            = 'groovy_menu_badge_icon_size';
	const GM_BADGE_ICON_COLOR           = 'groovy_menu_badge_icon_color';
	const GM_BADGE_TEXT                 = 'groovy_menu_badge_text';
	const GM_BADGE_TEXT_FONT_FAMILY     = 'groovy_menu_badge_text_font_family';
	const GM_BADGE_TEXT_FONT_VARIANT    = 'groovy_menu_badge_text_font_variant';
	const GM_BADGE_TEXT_FONT_SIZE       = 'groovy_menu_badge_text_font_size';
	const GM_BADGE_TEXT_FONT_COLOR      = 'groovy_menu_badge_text_font_color';
	const GM_BADGE_CONTAINER_PADDING    = 'groovy_menu_badge_container_padding';
	const GM_BADGE_CONTAINER_RADIUS     = 'groovy_menu_badge_container_radius';
	const GM_BADGE_CONTAINER_BG         = 'groovy_menu_badge_container_bg';

	/**
	 * Mass meta storage
	 *
	 * @var array
	 */
	private $gm_mass_meta = array();

	/**
	 * Array with fonts params
	 *
	 * @var array
	 */
	private $gm_google_fonts = array();

	/**
	 * Array with menu item options.
	 *
	 * @var array
	 */
	static $menu_item_options = array(
		'groovymenu-megamenu'                 => array(
			'meta_name' => self::IS_MEGAMENU_META,
			'mass'      => self::GM_NAV_MENU_META,
		),
		'groovymenu-megamenu-cols'            => array(
			'meta_name' => self::MEGAMENU_META_COLS,
			'default'   => '5',
			'mass'      => self::GM_NAV_MENU_META,
		),
		'groovymenu-do-not-show-title'        => array(
			'meta_name' => self::DO_NOT_SHOW_TITLE,
			'mass'      => self::GM_NAV_MENU_META,
		),
		'groovymenu-icon-class'               => array(
			'meta_name' => self::ICON_CLASS,
			'mass'      => self::GM_NAV_MENU_META,
		),
		'groovymenu-is-show-featured'         => array(
			'meta_name' => self::IS_SHOW_FEATURED_IMAGE,
			'mass'      => self::GM_NAV_MENU_META,
		),
		'groovymenu-megamenu-bg'              => array(
			'meta_name' => self::MEGAMENU_BACKGROUND,
			'mass'      => self::GM_NAV_MENU_META,
		),
		'groovymenu-megamenu-bg-position'     => array(
			'meta_name' => self::MEGAMENU_BACKGROUND_POSITION,
			'mass'      => self::GM_NAV_MENU_META,
		),
		'groovymenu-megamenu-bg-repeat'       => array(
			'meta_name' => self::MEGAMENU_BACKGROUND_REPEAT,
			'mass'      => self::GM_NAV_MENU_META,
		),
		'groovymenu-megamenu-bg-size'         => array(
			'meta_name' => self::MEGAMENU_BACKGROUND_SIZE,
			'mass'      => self::GM_NAV_MENU_META,
		),
		'groovymenu-block-url'                => array(
			'meta_name' => self::MENU_BLOCK_URL,
			'mass'      => self::GM_NAV_MENU_META,
		),
		'groovymenu-megamenu-post-not-mobile' => array(
			'meta_name' => self::MEGAMENU_META_POST_NOT_MOBILE,
			'mass'      => self::GM_NAV_MENU_META,
		),
		// Thumb
		'gm-thumb-enable'                     => array(
			'meta_name' => self::GM_THUMB_ENABLE,
			'mass'      => self::GM_NAV_MENU_META,
		),
		'gm-thumb-position'                   => array(
			'meta_name' => self::GM_THUMB_POSITION,
			'mass'      => self::GM_NAV_MENU_META,
		),
		'gm-thumb-max-height'                 => array(
			'meta_name' => self::GM_THUMB_MAX_HEIGHT,
			'mass'      => self::GM_NAV_MENU_META,
		),
		'gm-thumb-with-url'                   => array(
			'meta_name' => self::GM_THUMB_WITH_URL,
			'mass'      => self::GM_NAV_MENU_META,
		),
		'gm-thumb-image'                      => array(
			'meta_name' => self::GM_THUMB_IMAGE,
			'mass'      => self::GM_NAV_MENU_META,
		),
		// Badge.
		'gm-badge-enable'                     => array(
			'meta_name' => self::GM_BADGE_ENABLE,
			'mass'      => self::GM_NAV_MENU_META,
		),
		'gm-badge-type'                       => array(
			'meta_name' => self::GM_BADGE_TYPE,
			'mass'      => self::GM_NAV_MENU_META,
		),
		'gm-badge-placement'                  => array(
			'meta_name' => self::GM_BADGE_PLACEMENT,
			'mass'      => self::GM_NAV_MENU_META,
		),
		'gm-badge-general-position'           => array(
			'meta_name' => self::GM_BADGE_GENERAL_POSITION,
			'mass'      => self::GM_NAV_MENU_META,
		),
		'gm-badge-y-position'                 => array(
			'meta_name' => self::GM_BADGE_Y_POSITION,
			'mass'      => self::GM_NAV_MENU_META,
		),
		'gm-badge-x-position'                 => array(
			'meta_name' => self::GM_BADGE_X_POSITION,
			'mass'      => self::GM_NAV_MENU_META,
		),
		// Badge image.
		'gm-badge-image'                      => array(
			'meta_name' => self::GM_BADGE_IMAGE,
			'mass'      => self::GM_NAV_MENU_META,
		),
		'gm-badge-image-width'                => array(
			'meta_name' => self::GM_BADGE_IMAGE_WIDTH,
			'mass'      => self::GM_NAV_MENU_META,
		),
		'gm-badge-image-height'               => array(
			'meta_name' => self::GM_BADGE_IMAGE_HEIGHT,
			'mass'      => self::GM_NAV_MENU_META,
		),
		// Badge icon.
		'gm-badge-icon'                       => array(
			'meta_name' => self::GM_BADGE_ICON,
			'mass'      => self::GM_NAV_MENU_META,
		),
		'gm-badge-icon-size'                  => array(
			'meta_name' => self::GM_BADGE_ICON_SIZE,
			'mass'      => self::GM_NAV_MENU_META,
		),
		'gm-badge-icon-color'                 => array(
			'meta_name' => self::GM_BADGE_ICON_COLOR,
			'mass'      => self::GM_NAV_MENU_META,
		),
		// Badge text.
		'gm-badge-text'                       => array(
			'meta_name' => self::GM_BADGE_TEXT,
			'mass'      => self::GM_NAV_MENU_META,
		),
		'gm-badge-text-font-family'           => array(
			'meta_name' => self::GM_BADGE_TEXT_FONT_FAMILY,
			'mass'      => self::GM_NAV_MENU_META,
		),
		'gm-badge-text-font-variant'          => array(
			'meta_name' => self::GM_BADGE_TEXT_FONT_VARIANT,
			'mass'      => self::GM_NAV_MENU_META,
		),
		'gm-badge-text-font-size'             => array(
			'meta_name' => self::GM_BADGE_TEXT_FONT_SIZE,
			'mass'      => self::GM_NAV_MENU_META,
		),
		'gm-badge-text-font-color'            => array(
			'meta_name' => self::GM_BADGE_TEXT_FONT_COLOR,
			'mass'      => self::GM_NAV_MENU_META,
		),
		'gm-badge-container-padding'          => array(
			'meta_name' => self::GM_BADGE_CONTAINER_PADDING,
			'mass'      => self::GM_NAV_MENU_META,
		),
		'gm-badge-container-radius'           => array(
			'meta_name' => self::GM_BADGE_CONTAINER_RADIUS,
			'mass'      => self::GM_NAV_MENU_META,
		),
		'gm-badge-container-bg'               => array(
			'meta_name' => self::GM_BADGE_CONTAINER_BG,
			'mass'      => self::GM_NAV_MENU_META,
		),
	);


	/**
	 * @param $item
	 *
	 * @return mixed
	 */
	protected function getId( $item ) {
		if ( is_object( $item ) ) {

			if ( isset( $item->object ) && 'wpml_ls_menu_item' === $item->object ) {
				return null;
			}

			if ( isset( $item->db_id ) ) {
				$item_id = $item->db_id;
			} else {
				$item_id = intval( $item->ID );
			}

			return $item_id;
		}

		return $item;
	}

	/**
	 * @param $item_id
	 * @param $param_name
	 * @param $flag
	 *
	 * @return bool
	 */
	protected function getGMNavMenuMeta( $item_id, $param_name, $flag = true ) {
		if ( empty( $item_id ) ) {
			return false;
		}

		if ( empty( $this->gm_mass_meta[ $item_id ][ self::GM_NAV_MENU_META ] ) ) {
			$meta_data = get_post_meta( $item_id, self::GM_NAV_MENU_META, $flag );

			if ( is_string( $meta_data ) ) {
				$meta_data = json_decode( $meta_data, true );
			}

			if ( empty( $meta_data ) ) {
				$meta_data = array();
			}

			$this->gm_mass_meta[ $item_id ][ self::GM_NAV_MENU_META ] = $meta_data;
		}

		if ( ! isset( $this->gm_mass_meta[ $item_id ][ self::GM_NAV_MENU_META ][ $param_name ] ) ) {
			return false;
		}

		$val = $this->gm_mass_meta[ $item_id ][ self::GM_NAV_MENU_META ][ $param_name ];


		return $val;
	}

	/**
	 * @param $item_id
	 * @param $param_name
	 * @param $flag
	 *
	 * @return bool
	 */
	protected function getGMNavMenuMetaWithCheck( $item_id, $param_name, $flag ) {

		$db_version = get_option( GROOVY_MENU_DB_VER_OPTION );

		$lver = false;
		if ( defined( 'GROOVY_MENU_LVER' ) && '2' === GROOVY_MENU_LVER ) {
			$lver = true;
		}

		if ( $lver || version_compare( $db_version, '1.7.0.619', '>=' ) ) {
			$val = $this->getGMNavMenuMeta( $item_id, $param_name, $flag );
		} else {
			$val = get_post_meta( $item_id, $param_name, $flag );
		}

		return $val;
	}

	/**
	 * Get meta data about option "Mega Menu"
	 *
	 * @param object $item Object with menu item meta data.
	 * @param bool   $check_parent Check data for parent item.
	 *
	 * @return bool
	 */
	protected function isMegaMenu( $item, $check_parent = false ) {
		global $groovyMenuSettings;

		if (
			isset( $groovyMenuSettings['header'] ) &&
			( in_array( intval( $groovyMenuSettings['header']['style'] ), array( 2, 3, 4 ), true ) )
		) {
			return false;
		}

		if ( $check_parent ) {
			$item_id = empty( $item->menu_item_parent ) ? null : intval( $item->menu_item_parent );
		} else {
			$item_id = $this->getId( $item );
		}

		if ( empty( $item_id ) ) {
			return false;
		}

		$val = $this->getGMNavMenuMetaWithCheck( $item_id, self::IS_MEGAMENU_META, true );
		if ( '' === $val ) {
			$val = false;
		}

		return $val;
	}

	/**
	 * @param object $item Object with menu item meta data.
	 *
	 * @return bool
	 */
	protected function doNotShowTitle( $item ) {
		$item_id = $this->getId( $item );
		if ( empty( $item_id ) ) {
			return false;
		}

		$val = $this->getGMNavMenuMetaWithCheck( $item_id, self::DO_NOT_SHOW_TITLE, true );
		if ( '' === $val ) {
			$val = false;
		}

		return $val;
	}

	/**
	 * @param object $item Object with menu item meta data.
	 *
	 * @return int
	 */
	protected function megaMenuCols( $item ) {
		$item_id = $this->getId( $item );
		if ( empty( $item_id ) ) {
			return 5;
		}

		$val = $this->getGMNavMenuMetaWithCheck( $item_id, self::MEGAMENU_META_COLS, true );
		if ( ! $val ) {
			$val = 5;
		}

		return $val;
	}

	/**
	 * @param object $item Object with menu item meta data.
	 *
	 * @return int|null
	 */
	protected function megaMenuPost( $item ) {

		if ( isset( $item->object ) && 'gm_menu_block' === $item->object && ! empty( $item->object_id ) ) {
			$item_id = intval( $item->object_id );
			if ( $item_id ) {
				return $item_id;
			}
		}

		$item_id = $this->getId( $item );
		if ( empty( $item_id ) ) {
			return null;
		}

		$val = $this->getGMNavMenuMetaWithCheck( $item_id, self::MEGAMENU_META_POST, true );
		$val = $val ? intval( $val ) : null;

		return $val;
	}

	/**
	 * @param object $item Object with menu item meta data.
	 * @param string $reserveUrl
	 *
	 * @return int|null
	 */
	protected function menuBlockURL( $item, $reserveUrl = '' ) {

		$item_id = $this->getId( $item );
		if ( empty( $item_id ) ) {
			return null;
		}

		$val = $this->getGMNavMenuMetaWithCheck( $item_id, self::MENU_BLOCK_URL, true );
		$val = $val ? esc_url( $val ) : $reserveUrl;

		return $val;
	}

	/**
	 * Get post content
	 *
	 * @param integer $post_id post id.
	 *
	 * @return string
	 */
	public function getMenuBlockPostContent( $post_id ) {
		global $post;
		global $wp_filter;

		$mm_content = '';

		if ( $post_id ) {

			$post_id = intval( $post_id );

			$wpml_gm_menu_block_id = apply_filters( 'wpml_object_id', $post_id, 'gm_menu_block', true );

			// prevent conflict with Divi theme builder.
			if ( 'divi_builder' === GroovyMenuUtils::check_wp_builders() ) {
				return '[' . __( 'Divi Builder Conflict Prevention', 'groovy-menu' ) . ']';
			}

			// prevent conflict with Avada theme / Fusion builder.
			if ( 'fusion_builder' === GroovyMenuUtils::check_wp_builders() ) {
				return '[' . __( 'Fusion Builder Conflict Prevention', 'groovy-menu' ) . ']';
			}

			// Copy global $post exemplar.
			$_post = $post;
			$post  = get_post( $wpml_gm_menu_block_id ); // @codingStandardsIgnoreLine

			if ( empty( $post->ID ) ) {
				// Recovery global $post exemplar.
				$post = $_post; // @codingStandardsIgnoreLine

				return $mm_content;
			}

			// prevent conflict with cornerstone plugin.
			if ( isset( $_POST['cs_preview_state'] ) && isset( $_POST['_cs_nonce'] ) ) { // @codingStandardsIgnoreLine
				// Recovery global $post exemplar.
				$post = $_post; // @codingStandardsIgnoreLine

				return '[' . __( 'Cornerstone Conflict Prevention', 'groovy-menu' ) . ']';
			}

			if ( isset( $_GET['elementor-preview'] ) ) { // @codingStandardsIgnoreLine
				// Recovery global $post exemplar.
				$post = $_post; // @codingStandardsIgnoreLine

				return '[' . __( 'Elementor Conflict Prevention', 'groovy-menu' ) . ']';
			}

			if ( isset( $_GET['page_id'] ) && ! empty( $_GET['et_fb'] ) ) { // @codingStandardsIgnoreLine
				// Recovery global $post exemplar.
				$post = $_post; // @codingStandardsIgnoreLine

				return '[' . __( 'Divi builder Conflict Prevention', 'groovy-menu' ) . ']';
			}

			if (
				class_exists( '\FLBuilder' ) &&
				class_exists( '\FLBuilderModel' ) &&
				method_exists( '\FLBuilderModel', 'is_builder_enabled' ) &&
				method_exists( '\FLBuilder', 'enqueue_layout_styles_scripts_by_id' ) &&
				method_exists( '\FLBuilder', 'render_content_by_id' ) &&
				\FLBuilderModel::is_builder_enabled( $post->ID )
			) {

				ob_start();

				// Enqueue styles and scripts for this post.
				\FLBuilder::enqueue_layout_styles_scripts_by_id( $post->ID );

				// Render the builder content.
				\FLBuilder::render_content_by_id( $post->ID );

				$mm_content = ob_get_clean();

			} else {

				$mm_content = apply_filters( 'the_content', $post->post_content );

				// fix for bbPress function bbp_remove_all_filters('the_content').
				if ( empty( $wp_filter['the_content'] ) ) {
					$mm_content = do_shortcode( $mm_content );
				}

			}


			// Recovery global $post exemplar.
			$post = $_post;

		}

		return $mm_content;
	}

	/**
	 * @param object $item Object with menu item meta data.
	 *
	 * @return int|mixed
	 */
	protected function megaMenuPostNotMobile( $item ) {
		$item_id = $this->getId( $item );
		if ( empty( $item_id ) ) {
			return false;
		}

		$val = $this->getGMNavMenuMetaWithCheck( $item_id, self::MEGAMENU_META_POST_NOT_MOBILE, true );
		if ( '' === $val ) {
			$val = false;
		}

		return $val;
	}

	/**
	 * @param object $item Object with menu item meta data.
	 *
	 * @return mixed
	 */
	protected function getIcon( $item ) {
		$item_id = $this->getId( $item );
		if ( empty( $item_id ) ) {
			return '';
		}

		return $this->getGMNavMenuMetaWithCheck( $item_id, self::ICON_CLASS, true );
	}

	/**
	 * @param object $item Object with menu item meta data.
	 *
	 * @return mixed
	 */
	protected function getBackgroundRepeat( $item ) {
		$item_id = $this->getId( $item );
		if ( empty( $item_id ) ) {
			return '';
		}

		return $this->getGMNavMenuMetaWithCheck( $item_id, self::MEGAMENU_BACKGROUND_REPEAT, true );
	}

	/**
	 * @param object $item Object with menu item meta data.
	 *
	 * @return mixed
	 */
	protected function getBackgroundPosition( $item ) {
		$item_id = $this->getId( $item );
		if ( empty( $item_id ) ) {
			return '';
		}

		return $this->getGMNavMenuMetaWithCheck( $item_id, self::MEGAMENU_BACKGROUND_POSITION, true );
	}

	/**
	 * @param object $item Object with menu item meta data.
	 *
	 * @return mixed|string
	 */
	protected function getBackgroundSize( $item ) {
		$item_id = $this->getId( $item );
		if ( empty( $item_id ) ) {
			return 'full';
		}

		$size = $this->getGMNavMenuMetaWithCheck( $item_id, self::MEGAMENU_BACKGROUND_SIZE, true );
		if ( empty( $size ) ) {
			$size = 'full';
		}

		return $size;
	}

	/**
	 * @param object $item Object with menu item meta data.
	 *
	 * @return mixed
	 */
	protected function getBackgroundId( $item ) {
		$item_id = $this->getId( $item );
		if ( empty( $item_id ) ) {
			return null;
		}

		return $this->getGMNavMenuMetaWithCheck( $item_id, self::MEGAMENU_BACKGROUND, true );
	}

	/**
	 * @param object $item Object with menu item meta data.
	 * @param string $size
	 *
	 * @return false|mixed|string
	 */
	protected function getBackgroundUrl( $item, $size = 'full' ) {
		static $cache = array();

		$id = $this->getBackgroundId( $item );

		if ( empty( $id ) ) {
			return '';
		}

		if ( isset( $cache[ $id ][ $size ] ) ) {
			return $cache[ $id ][ $size ];
		}

		if ( 'full' === $size ) {
			$attach_url = wp_get_attachment_url( $id );
		} else {
			$attach_url = $this->getBackgroundUrlThumbnail( $item, $size );
		}

		$cache[ $id ][ $size ] = $attach_url;

		return $attach_url;
	}

	/**
	 * @param object $item Object with menu item meta data.
	 * @param string $size
	 *
	 * @return false|mixed|string
	 */
	protected function getBackgroundUrlThumbnail( $item, $size = 'thumbnail' ) {
		$id = $this->getBackgroundId( $item );

		if ( empty( $id ) ) {
			return '';
		}

		$thumb_url_array = wp_get_attachment_image_src( $id, $size );

		$thumb_url = empty( $thumb_url_array[0] ) ? $this->getBackgroundUrl( $item ) : $thumb_url_array[0];

		return $thumb_url;
	}

	/**
	 * @param object $item Object with menu item meta data.
	 *
	 * @return bool
	 */
	protected function isShowFeaturedImage( $item ) {
		$item_id = $this->getId( $item );
		if ( empty( $item_id ) ) {
			return false;
		}

		$val = $this->getGMNavMenuMetaWithCheck( $item_id, self::IS_SHOW_FEATURED_IMAGE, true );
		if ( '' === $val ) {
			$val = false;
		}

		return $val;
	}


	/**
	 * @param object $item Object with menu item meta data.
	 *
	 * @return bool
	 */
	protected function getThumbEnable( $item ) {
		$item_id = $this->getId( $item );
		if ( empty( $item_id ) ) {
			return '';
		}

		$val = $this->getGMNavMenuMeta( $item_id, self::GM_THUMB_ENABLE, true );
		if ( empty( $val ) || ! $val || 'none' === $val || 'false' === $val ) {
			$val = false;
		}

		return $val;
	}


	/**
	 * @param object $item Object with menu item meta data.
	 *
	 * @return bool
	 */
	protected function getThumbWithUrl( $item ) {
		$item_id = $this->getId( $item );
		if ( empty( $item_id ) ) {
			return '';
		}

		$val = $this->getGMNavMenuMeta( $item_id, self::GM_THUMB_WITH_URL, true );
		if ( empty( $val ) || ! $val || 'none' === $val || 'false' === $val ) {
			$val = false;
		}

		return $val;
	}


	/**
	 * @param object $item Object with menu item meta data.
	 *
	 * @return mixed
	 */
	protected function getThumbPosition( $item ) {
		$item_id = $this->getId( $item );
		if ( empty( $item_id ) ) {
			return '';
		}

		$val = $this->getGMNavMenuMeta( $item_id, self::GM_THUMB_POSITION, true );
		if ( ! $val ) {
			$val = '';
		}

		return $val;
	}


	/**
	 * @param object $item Object with menu item meta data.
	 *
	 * @return mixed
	 */
	protected function getThumbMaxHeight( $item ) {
		$item_id = $this->getId( $item );
		if ( empty( $item_id ) ) {
			return '';
		}

		$val = $this->getGMNavMenuMeta( $item_id, self::GM_THUMB_MAX_HEIGHT, true );
		if ( ! $val ) {
			$val = '';
		}

		return $val;
	}


	/**
	 * @param object $item Object with menu item meta data.
	 *
	 * @return mixed
	 */
	protected function getThumbImage( $item ) {
		$item_id = $this->getId( $item );
		if ( empty( $item_id ) ) {
			return '';
		}

		$val = $this->getGMNavMenuMeta( $item_id, self::GM_THUMB_IMAGE, true );
		if ( ! $val ) {
			$val = '';
		}

		return $val;
	}


	/**
	 * @param object $item Object with menu item meta data.
	 *
	 * @return bool
	 */
	protected function getBadgeEnable( $item ) {
		$item_id = $this->getId( $item );
		if ( empty( $item_id ) ) {
			return '';
		}

		if ( defined( 'GROOVY_MENU_LVER' ) && '2' === GROOVY_MENU_LVER ) {
			return '';
		}

		$val = $this->getGMNavMenuMeta( $item_id, self::GM_BADGE_ENABLE, true );
		if ( empty( $val ) || ! $val || 'none' === $val || 'false' === $val ) {
			$val = false;
		}

		return $val;
	}

	/**
	 * @param object $item Object with menu item meta data.
	 *
	 * @return bool
	 */
	protected function getBadgeType( $item ) {
		$item_id = $this->getId( $item );
		if ( empty( $item_id ) ) {
			return 'icon';
		}

		$val = $this->getGMNavMenuMeta( $item_id, self::GM_BADGE_TYPE, true );
		if ( ! $val ) {
			$val = 'icon';
		}

		return $val;
	}

	/**
	 * @param object $item Object with menu item meta data.
	 *
	 * @return mixed
	 */
	protected function getBadgeImage( $item ) {
		$item_id = $this->getId( $item );
		if ( empty( $item_id ) ) {
			return '';
		}

		$val = $this->getGMNavMenuMeta( $item_id, self::GM_BADGE_IMAGE, true );
		if ( ! $val ) {
			$val = '';
		}

		return $val;
	}


	/**
	 * @param object $item Object with menu item meta data.
	 *
	 * @return mixed
	 */
	protected function getBadgeImageWidth( $item ) {
		$item_id = $this->getId( $item );
		if ( empty( $item_id ) ) {
			return '';
		}

		$val = $this->getGMNavMenuMeta( $item_id, self::GM_BADGE_IMAGE_WIDTH, true );
		if ( ! $val ) {
			$val = '100%';
		}

		if ( ! $this->getBadgeImage( $item ) ) {
			$val = '';
		}

		return $val;
	}

	/**
	 * @param object $item Object with menu item meta data.
	 *
	 * @return mixed
	 */
	protected function getBadgeImageHeight( $item ) {
		$item_id = $this->getId( $item );
		if ( empty( $item_id ) ) {
			return '';
		}

		$val = $this->getGMNavMenuMeta( $item_id, self::GM_BADGE_IMAGE_HEIGHT, true );
		if ( ! $val ) {
			$val = '100%';
		}

		if ( ! $this->getBadgeImage( $item ) ) {
			$val = '';
		}

		return $val;
	}


	/**
	 * @param object $item Object with menu item meta data.
	 *
	 * @return string
	 */
	protected function getBadgeImageWidthHeight( $item ) {
		$sizes = array(
			'width'  => $this->getBadgeImageWidth( $item ),
			'height' => $this->getBadgeImageHeight( $item ),
		);

		$compiled_string = '';
		foreach ( $sizes as $size => $value ) {
			if ( empty( $value ) ) {
				continue;
			}
			$compiled_string .= ' ' . $size . '="' . $value . '"';
		}

		return $compiled_string;
	}

	/**
	 * @param object $item Object with menu item meta data.
	 *
	 * @return mixed
	 */
	protected function getBadgePlacement( $item ) {
		$item_id = $this->getId( $item );
		if ( empty( $item_id ) ) {
			return 'left';
		}

		$val = $this->getGMNavMenuMeta( $item_id, self::GM_BADGE_PLACEMENT, true );
		if ( ! $val ) {
			$val = 'left';
		}

		return $val;
	}

	/**
	 * @param object $item Object with menu item meta data.
	 *
	 * @return mixed
	 */
	protected function getBadgeGeneralPosition( $item ) {
		$item_id = $this->getId( $item );
		if ( empty( $item_id ) ) {
			return 'relative';
		}

		$val = $this->getGMNavMenuMeta( $item_id, self::GM_BADGE_GENERAL_POSITION, true );
		if ( ! $val ) {
			$val = 'relative';
		}

		return $val;
	}

	/**
	 * @param object $item Object with menu item meta data.
	 *
	 * @return mixed
	 */
	protected function getBadgeYPosition( $item ) {
		$item_id = $this->getId( $item );
		if ( empty( $item_id ) ) {
			return '';
		}

		$val = $this->getGMNavMenuMeta( $item_id, self::GM_BADGE_Y_POSITION, true );
		if ( ! $val ) {
			$val = '';
		}

		return $val;
	}

	/**
	 * @param object $item Object with menu item meta data.
	 *
	 * @return mixed
	 */
	protected function getBadgeXPosition( $item ) {
		$item_id = $this->getId( $item );
		if ( empty( $item_id ) ) {
			return '';
		}

		$val = $this->getGMNavMenuMeta( $item_id, self::GM_BADGE_X_POSITION, true );
		if ( ! $val ) {
			$val = '';
		}

		return $val;
	}

	/**
	 * @param object $item Object with menu item meta data.
	 *
	 * @return mixed
	 */
	protected function getBadgeIcon( $item ) {
		$item_id = $this->getId( $item );
		if ( empty( $item_id ) ) {
			return '';
		}

		$val = $this->getGMNavMenuMeta( $item_id, self::GM_BADGE_ICON, true );
		if ( ! $val ) {
			$val = '';
		}

		return $val;
	}

	/**
	 * @param object $item Object with menu item meta data.
	 *
	 * @return mixed
	 */
	protected function getBadgeIconSize( $item ) {
		$item_id = $this->getId( $item );
		if ( empty( $item_id ) ) {
			return '';
		}

		$val = $this->getGMNavMenuMeta( $item_id, self::GM_BADGE_ICON_SIZE, true );
		if ( ! $val ) {
			$val = '';
		}

		return $val;
	}

	/**
	 * @param object $item Object with menu item meta data.
	 *
	 * @return mixed
	 */
	protected function getBadgeIconColor( $item ) {
		$item_id = $this->getId( $item );
		if ( empty( $item_id ) ) {
			return '';
		}

		$val = $this->getGMNavMenuMeta( $item_id, self::GM_BADGE_ICON_COLOR, true );
		if ( ! $val ) {
			$val = '';
		}

		return $val;
	}

	/**
	 * @param object $item Object with menu item meta data.
	 *
	 * @return mixed
	 */
	protected function getBadgeText( $item ) {
		$item_id = $this->getId( $item );
		if ( empty( $item_id ) ) {
			return '';
		}

		$val = $this->getGMNavMenuMeta( $item_id, self::GM_BADGE_TEXT, true );
		if ( ! $val ) {
			$val = '';
		}

		return $val;
	}

	/**
	 * @param object $item Object with menu item meta data.
	 *
	 * @return mixed
	 */
	protected function getBadgeTextFontFamily( $item ) {
		$item_id = $this->getId( $item );
		if ( empty( $item_id ) ) {
			return '';
		}

		$val = $this->getGMNavMenuMeta( $item_id, self::GM_BADGE_TEXT_FONT_FAMILY, true );
		if ( ! $val ) {
			$val = '';
		}

		return $val;
	}

	/**
	 * @param object $item Object with menu item meta data.
	 *
	 * @return mixed
	 */
	protected function getBadgeTextFontVariant( $item ) {
		$item_id = $this->getId( $item );
		if ( empty( $item_id ) ) {
			return '';
		}

		$val = $this->getGMNavMenuMeta( $item_id, self::GM_BADGE_TEXT_FONT_VARIANT, true );
		if ( ! $val ) {
			$val = '';
		}

		return $val;
	}

	/**
	 * @param object $item Object with menu item meta data.
	 *
	 * @return mixed
	 */
	protected function getBadgeTextFontSize( $item ) {
		$item_id = $this->getId( $item );
		if ( empty( $item_id ) ) {
			return '';
		}

		$val = $this->getGMNavMenuMeta( $item_id, self::GM_BADGE_TEXT_FONT_SIZE, true );
		if ( ! $val ) {
			$val = '';
		}

		return $val;
	}

	/**
	 * @param object $item Object with menu item meta data.
	 *
	 * @return mixed
	 */
	protected function getBadgeTextFontColor( $item ) {
		$item_id = $this->getId( $item );
		if ( empty( $item_id ) ) {
			return '';
		}

		$val = $this->getGMNavMenuMeta( $item_id, self::GM_BADGE_TEXT_FONT_COLOR, true );
		if ( ! $val ) {
			$val = '';
		}

		return $val;
	}

	/**
	 * @param object $item Object with menu item meta data.
	 *
	 * @return mixed
	 */
	protected function getBadgeContainerPadding( $item ) {
		$item_id = $this->getId( $item );
		if ( empty( $item_id ) ) {
			return '';
		}

		$val = $this->getGMNavMenuMeta( $item_id, self::GM_BADGE_CONTAINER_PADDING, true );
		if ( ! $val ) {
			$val = '';
		}

		return $val;
	}

	/**
	 * @param object $item Object with menu item meta data.
	 *
	 * @return mixed
	 */
	protected function getBadgeContainerRadius( $item ) {
		$item_id = $this->getId( $item );
		if ( empty( $item_id ) ) {
			return '';
		}

		$val = $this->getGMNavMenuMeta( $item_id, self::GM_BADGE_CONTAINER_RADIUS, true );
		if ( ! $val ) {
			$val = '';
		}

		return $val;
	}

	/**
	 * @param object $item Object with menu item meta data.
	 *
	 * @return mixed
	 */
	protected function getBadgeContainerBg( $item ) {
		$item_id = $this->getId( $item );
		if ( empty( $item_id ) ) {
			return '';
		}

		$val = $this->getGMNavMenuMeta( $item_id, self::GM_BADGE_CONTAINER_BG, true );
		if ( ! $val ) {
			$val = '';
		}

		return $val;
	}


	/**
	 * Fill font array.
	 */
	protected function gmFillFontArray() {
		$this->gm_google_fonts = include GROOVY_MENU_DIR . 'includes/fonts-google.php';

		if ( empty( $this->gm_google_fonts ) || ! is_array( $this->gm_google_fonts ) || empty( $this->gm_google_fonts[0]['items'] ) ) {
			$this->gm_google_fonts = array();
		}

		$fonts = array();

		foreach ( $this->gm_google_fonts[0]['items'] as $font_data ) {
			if ( empty( $font_data['family'] ) ) {
				continue;
			}

			$fonts[ $font_data['family'] ] = $font_data;
		}

		$this->gm_google_fonts = $fonts;

	}

	/**
	 * Return prepared font array for html tag select.
	 *
	 * @return array
	 */
	protected function gmGetFontArrayForSelect() {
		if ( empty( $this->gm_google_fonts ) ) {
			$this->gmFillFontArray();
		}

		$fonts = array(
			'' => '100;300;regular;500;700;800;900'
		);

		foreach ( $this->gm_google_fonts as $font_family => $font_data ) {
			if ( empty( $font_data['family'] ) || empty( $font_family ) ) {
				continue;
			}

			$variants = empty( $font_data['variants'] ) ? '' : implode( ';', $font_data['variants'] );

			$fonts[ $font_family ] = $variants;

		}

		return $fonts;
	}

	/**
	 * Return font data array by font family.
	 *
	 * @param string $search_family font family name.
	 *
	 * @return array
	 */
	protected function gmGetFontByFamily( $search_family ) {
		if ( empty( $this->gm_google_fonts ) ) {
			$this->gmFillFontArray();
		}

		$font = array();

		foreach ( $this->gm_google_fonts as $font_family => $font_data ) {
			if ( empty( $font_data['family'] ) || empty( $font_family ) ) {
				continue;
			}

			if ( $font_family === $search_family ) {
				$font = $font_data;
				break;
			}

		}

		return $font;
	}


}
