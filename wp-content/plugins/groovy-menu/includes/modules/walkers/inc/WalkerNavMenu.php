<?php

namespace GroovyMenu;

use \Walker_Nav_Menu as Walker_Nav_Menu;
use \GroovyMenuUtils as GroovyMenuUtils;


defined( 'ABSPATH' ) || die( 'This script cannot be accessed directly.' );


/**
 * Class WalkerNavMenu
 */
class WalkerNavMenu extends Walker_Nav_Menu {

	const GM_NAV_MENU_META = 'groovy_menu_nav_menu_meta';
	const IS_MEGAMENU_META = 'groovy_menu_is_megamenu';
	const DO_NOT_SHOW_TITLE = 'groovy_menu_do_not_show_title';
	const FROZEN_LINK = 'groovy_menu_frozen_link';
	const USE_HTML_AS_ICON = 'groovy_menu_use_html_as_icon';
	const HTML_ICON_CONTENT = 'groovy_menu_html_icon_content';
	const MEGAMENU_META_COLS = 'groovy_menu_megamenu_cols';
	const MENU_BLOCK_URL = 'groovy_menu_block_url';
	const MEGAMENU_META_POST = 'groovy_menu_megamenu_post';
	const MEGAMENU_META_POST_NOT_MOBILE = 'groovy_menu_megamenu_post_not_mobile';
	const IS_SHOW_FEATURED_IMAGE = 'groovy_menu_is_show_featured_image';
	const ICON_CLASS = 'groovy_menu_icon_class';
	const MEGAMENU_DROPDOWN_CUSTOM_WIDTH = 'groovy_menu_megamenu_dropdown_custom_width';
	const MEGAMENU_BACKGROUND = 'groovy_menu_megamenu_background';
	const MEGAMENU_BACKGROUND_POSITION = 'groovy_menu_megamenu_background_position';
	const MEGAMENU_BACKGROUND_REPEAT = 'groovy_menu_megamenu_background_repeat';
	const MEGAMENU_BACKGROUND_SIZE = 'groovy_menu_megamenu_background_size';
	const GM_THUMB_ENABLE = 'groovy_menu_thumb_enable';
	const GM_THUMB_POSITION = 'groovy_menu_thumb_position';
	const GM_THUMB_MAX_HEIGHT = 'groovy_menu_thumb_max_height';
	const GM_THUMB_WITH_URL = 'groovy_menu_thumb_with_url';
	const GM_THUMB_IMAGE = 'groovy_menu_thumb_image';
	const GM_BADGE_ENABLE = 'groovy_menu_badge_enable';
	const GM_BADGE_TYPE = 'groovy_menu_badge_type';
	const GM_BADGE_PLACEMENT = 'groovy_menu_badge_placement';
	const GM_BADGE_GENERAL_POSITION = 'groovy_menu_badge_general_position';
	const GM_BADGE_Y_POSITION = 'groovy_menu_badge_y_position';
	const GM_BADGE_X_POSITION = 'groovy_menu_badge_x_position';
	const GM_BADGE_IMAGE = 'groovy_menu_badge_image';
	const GM_BADGE_IMAGE_HEIGHT = 'groovy_menu_badge_image_height';
	const GM_BADGE_IMAGE_WIDTH = 'groovy_menu_badge_image_width';
	const GM_BADGE_ICON = 'groovy_menu_badge_icon';
	const GM_BADGE_ICON_SIZE = 'groovy_menu_badge_icon_size';
	const GM_BADGE_ICON_COLOR = 'groovy_menu_badge_icon_color';
	const GM_BADGE_TEXT = 'groovy_menu_badge_text';
	const GM_BADGE_TEXT_FONT_FAMILY = 'groovy_menu_badge_text_font_family';
	const GM_BADGE_TEXT_FONT_VARIANT = 'groovy_menu_badge_text_font_variant';
	const GM_BADGE_TEXT_FONT_SIZE = 'groovy_menu_badge_text_font_size';
	const GM_BADGE_TEXT_FONT_COLOR = 'groovy_menu_badge_text_font_color';
	const GM_BADGE_CONTAINER_PADDING = 'groovy_menu_badge_container_padding';
	const GM_BADGE_CONTAINER_RADIUS = 'groovy_menu_badge_container_radius';
	const GM_BADGE_CONTAINER_BG = 'groovy_menu_badge_container_bg';

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

	public static $grooniColsVariants = array(
		'1'           => '100%', // 1
		'2'           => '50% + 50%', // 2
		'60-40'       => '60% + 40%',
		'40-60'       => '40% + 60%',
		'66-33'       => '66% + 33%',
		'33-66'       => '33% + 66%',
		'25-75'       => '25% + 75%',
		'75-25'       => '75% + 25%',
		'20-80'       => '20% + 80%',
		'80-20'       => '80% + 20%',
		'90-10'       => '90% + 10%',
		'10-90'       => '10% + 90%',
		'3'           => '33% + 33% + 33%', // 3
		'50-25-25'    => '50% + 25% + 25%',
		'25-25-50'    => '25% + 25% + 50%',
		'60-20-20'    => '60% + 20% + 20%',
		'20-60-20'    => '20% + 60% + 20%',
		'20-20-60'    => '20% + 20% + 60%',
		'20-30-50'    => '20% + 30% + 50%',
		'50-30-20'    => '50% + 30% + 20%',
		'4'           => '25% + 25% + 25% + 25%', // 4
		'40-20-20-20' => '40% + 20% + 20% + 20%',
		'20-20-20-40' => '20% + 20% + 20% + 40%',
		'50-20-20-10' => '50% + 20% + 20% + 10%',
		'10-20-20-50' => '10% + 20% + 20% + 50%',
		'5'           => '5 Columns with 20% each', // 5
		'6'           => '6 Columns with 16.6% each', // 6
		'7'           => '7 Columns with 14.2% each', // 7
		'8'           => '8 Columns with 12.5% each', // 8
		'9'           => '9 Columns with 11.1% each', // 8
		'10'          => '10 Columns with 10% each', // 10
	);

	public static $backgroundPositions = array(
		'top left'      => 'top left',
		'top center'    => 'top center',
		'top right'     => 'top right',
		'center left'   => 'center left',
		'center center' => 'center center',
		'center right'  => 'center right',
		'bottom left'   => 'bottom left',
		'bottom center' => 'bottom center',
		'bottom right'  => 'bottom right',
	);

	public static $backgroundRepeats = array(
		'no-repeat' => 'no-repeat',
		'repeat'    => 'repeat',
		'repeat-x'  => 'repeat-x',
		'repeat-y'  => 'repeat-y',
	);

	/**
	 * Menu admin walker fields.
	 *
	 * @access public
	 * @return array.
	 */
	public function menu_walker_options() {
		return apply_filters(
			'groovy_menu_walker_options',
			array(
				'icon-class'                     => [
					'id'          => 'icon-class',
					'label'       => esc_attr__( 'Icon', 'groovy-menu' ),
					'description' => esc_attr__( 'Select an icon for your menu item.', 'groovy-menu' ),
					'type'        => 'iconpicker',
					'default'     => '',
					'save_id'     => self::ICON_CLASS,
				],
				'use-html-as-icon'               => [
					'id'          => 'use-html-as-icon',
					'label'       => esc_attr__( 'Use HTML or shortcode as icon', 'groovy-menu' ),
					'description' => esc_attr__( 'if checked, the following content will be added instead of the icon', 'groovy-menu' ),
					'type'        => 'checkbox',
					'default'     => false,
					'save_id'     => self::USE_HTML_AS_ICON,
				],
				'html-icon-content'              => [
					'id'          => 'html-icon-content',
					'label'       => esc_attr__( 'HTML icon content', 'groovy-menu' ),
					'description' => esc_attr__( '&lt;script&gt; tag not allowed', 'groovy-menu' ),
					'type'        => 'textarea',
					'default'     => '',
					'save_id'     => self::HTML_ICON_CONTENT,
					'field_class' => 'gm-html-icon-depend',
				],
				'do-not-show-title'              => [
					'id'          => 'do-not-show-title',
					'label'       => esc_attr__( 'Do not show menu item title and link', 'groovy-menu' ),
					'description' => '',
					'type'        => 'checkbox',
					'default'     => false,
					'save_id'     => self::DO_NOT_SHOW_TITLE,
				],
				'frozen-link'                    => [
					'id'          => 'frozen-link',
					'label'       => esc_attr__( 'Frozen link', 'groovy-menu' ),
					'description' => esc_attr__( 'Disabled opening link at click, other features remains working.', 'groovy-menu' ),
					'type'        => 'checkbox',
					'default'     => false,
					'save_id'     => self::FROZEN_LINK,
				],
				'is-show-featured'               => [
					'id'          => 'is-show-featured',
					'label'       => esc_attr__( 'Show featured image on hover', 'groovy-menu' ),
					'description' => '',
					'type'        => 'checkbox',
					'post_type'   => 'post_type',
					'default'     => false,
					'save_id'     => self::IS_SHOW_FEATURED_IMAGE,
				],
				'megamenu-post-not-mobile'       => [
					'id'          => 'megamenu-post-not-mobile',
					'label'       => esc_attr__( 'Do not show Menu block content on mobile', 'groovy-menu' ),
					'description' => '',
					'type'        => 'checkbox',
					'default'     => false,
					'lver'        => false,
					'save_id'     => self::MEGAMENU_META_POST_NOT_MOBILE,
				],
				// -------------------------------------------------------------------------------- MEGAMENU settings
				'megamenu'                       => [
					'id'          => 'megamenu',
					'label'       => esc_attr__( 'Mega menu', 'groovy-menu' ),
					'description' => esc_attr__( 'Applies to first level menu only.', 'groovy-menu' ),
					'type'        => 'checkbox',
					'default'     => false,
					'save_id'     => self::IS_MEGAMENU_META,
					'depth'       => 0,
				],
				'megamenu-cols'                  => [
					'id'          => 'megamenu-cols',
					'label'       => esc_attr__( 'Mega menu columns', 'groovy-menu' ),
					'choices'     => self::megaMenuColsVariants(),
					'description' => '',
					'type'        => 'select',
					'default'     => '5',
					'save_id'     => self::MEGAMENU_META_COLS,
					'depth'       => 0,
					'field_class' => 'megamenu-cols megamenu-options-depend',
				],
				'megamenu-dropdown-custom-width' => [
					'id'          => 'megamenu-dropdown-custom-width',
					'label'       => esc_attr__( 'Mega menu dropdown custom width', 'groovy-menu' ),
					'description' => esc_attr__( 'Is apply for container of menu. Leave blank or write zero for default state', 'groovy-menu' ),
					'type'        => 'number',
					'default'     => '',
					'min-value'   => '0',
					'max-value'   => '5000',
					'value-type'  => 'px',
					'save_id'     => self::MEGAMENU_DROPDOWN_CUSTOM_WIDTH,
					'lver'        => false,
					'field_class' => 'megamenu-options-depend',
				],
				'megamenu-bg'                    => [
					'id'          => 'megamenu-bg',
					'label'       => esc_attr__( 'Mega menu background image', 'groovy-menu' ),
					'description' => '',
					'type'        => 'media',
					'default'     => '',
					'save_id'     => self::MEGAMENU_BACKGROUND,
					'depth'       => 0,
					'field_class' => 'megamenu-options-depend',
				],
				'megamenu-bg-position'           => [
					'id'          => 'megamenu-bg-position',
					'label'       => esc_attr__( 'Mega menu columns', 'groovy-menu' ),
					'choices'     => self::$backgroundPositions,
					'description' => '',
					'type'        => 'select',
					'default'     => 'top center',
					'save_id'     => self::MEGAMENU_BACKGROUND_POSITION,
					'depth'       => 0,
					'field_class' => 'megamenu-options-depend',
				],
				'megamenu-bg-repeat'             => [
					'id'          => 'megamenu-bg-repeat',
					'label'       => esc_attr__( 'Background repeat', 'groovy-menu' ),
					'choices'     => self::$backgroundRepeats,
					'description' => '',
					'type'        => 'select',
					'default'     => 'repeat',
					'save_id'     => self::MEGAMENU_BACKGROUND_REPEAT,
					'depth'       => 0,
					'field_class' => 'megamenu-options-depend',
				],
				'megamenu-bg-size'               => [
					'id'          => 'megamenu-bg-size',
					'label'       => esc_attr__( 'Background image size', 'groovy-menu' ),
					'choices'     => GroovyMenuUtils::get_all_image_sizes_for_select(),
					'description' => '',
					'type'        => 'select',
					'default'     => 'full',
					'save_id'     => self::MEGAMENU_BACKGROUND_SIZE,
					'depth'       => 0,
					'field_class' => 'megamenu-options-depend',
				],
				// ------------------------------------------------------------------------------- Thumbnail settings
				'thumb-enable'                   => [
					'id'          => 'thumb-enable',
					'label'       => esc_attr__( 'Enable thumbnail', 'groovy-menu' ),
					'description' => esc_attr__( 'Show thumbnail for post.', 'groovy-menu' ),
					'type'        => 'checkbox',
					'default'     => false,
					'save_id'     => self::GM_THUMB_ENABLE,
					'lver'        => false,
				],
				'thumb-position'                 => [
					'id'          => 'thumb-position',
					'label'       => esc_attr__( 'Thumbnail position.', 'groovy-menu' ),
					'choices'     => self::gmThumbPositionVariants(),
					'description' => '',
					'type'        => 'select',
					'default'     => 'above',
					'save_id'     => self::GM_THUMB_POSITION,
					'lver'        => false,
					'field_class' => 'gm-thumb-field',
				],
				'thumb-max-height'               => [
					'id'          => 'thumb-max-height',
					'label'       => esc_attr__( 'Thumbnail image maximum height.', 'groovy-menu' ),
					'description' => '',
					'type'        => 'number',
					'default'     => '128',
					'min-value'   => '0',
					'max-value'   => '3200',
					'value-type'  => 'px',
					'save_id'     => self::GM_THUMB_MAX_HEIGHT,
					'lver'        => false,
					'field_class' => 'gm-thumb-field',
				],
				'thumb-with-url'                 => [
					'id'          => 'thumb-with-url',
					'label'       => esc_attr__( 'Wrap thumbnail with menu item URL', 'groovy-menu' ),
					'description' => '',
					'type'        => 'checkbox',
					'default'     => false,
					'save_id'     => self::GM_THUMB_WITH_URL,
					'lver'        => false,
					'field_class' => 'gm-thumb-field',
				],
				'thumb-image'                    => [
					'id'          => 'thumb-image',
					'label'       => esc_attr__( 'Thumbnail Image', 'groovy-menu' ),
					'description' => '',
					'type'        => 'media',
					'default'     => '',
					'save_id'     => self::GM_THUMB_IMAGE,
					'lver'        => false,
					'field_class' => 'gm-thumb-field gm-thumb--image',
				],
				// ------------------------------------------------------------------------------------------- BADGE
				'badge-enable'                   => [
					'id'          => 'badge-enable',
					'label'       => esc_attr__( 'Enable badge', 'groovy-menu' ),
					'description' => esc_attr__( 'Show badge with menu item', 'groovy-menu' ),
					'type'        => 'checkbox',
					'default'     => false,
					'save_id'     => self::GM_BADGE_ENABLE,
					'lver'        => false,
				],
				'badge-placement'                => [
					'id'          => 'badge-placement',
					'label'       => esc_attr__( 'Badge placement', 'groovy-menu' ),
					'choices'     => self::gmBadgePlacementVariants(),
					'description' => '',
					'type'        => 'select',
					'default'     => 'left',
					'save_id'     => self::GM_BADGE_PLACEMENT,
					'lver'        => false,
					'field_class' => 'gm-badge-field gm-badge-field--shared',
				],
				'badge-general-position'         => [
					'id'          => 'badge-general-position',
					'label'       => esc_attr__( 'Badge position.', 'groovy-menu' ),
					'choices'     => self::gmBadgeGeneralPositionVariants(),
					'description' => esc_attr__( 'Absolutely positioned Badge take no space in the page layout. Relatively positioned Badge acts as normal element taking space.', 'groovy-menu' ),
					'type'        => 'select',
					'default'     => 'relative',
					'save_id'     => self::GM_BADGE_GENERAL_POSITION,
					'lver'        => false,
					'field_class' => 'gm-badge-field gm-badge-field--shared',
				],
				'badge-x-position'               => [
					'id'          => 'badge-x-position',
					'label'       => esc_attr__( 'Badge X offset.', 'groovy-menu' ),
					'description' => esc_attr__( 'Negative value will push badge left, positive right.', 'groovy-menu' ) . ' ' . esc_attr__( 'Any valid CSS units accepted e.q. % or px.', 'groovy-menu' ),
					'type'        => 'text',
					'default'     => '',
					'save_id'     => self::GM_BADGE_X_POSITION,
					'lver'        => false,
					'field_class' => 'gm-badge-field gm-badge-field--shared',
				],
				'badge-y-position'               => [
					'id'          => 'badge-y-position',
					'label'       => esc_attr__( 'Badge Y offset.', 'groovy-menu' ),
					'description' => esc_attr__( 'Negative value will push badge up, positive down.', 'groovy-menu' ) . ' ' . esc_attr__( 'Any valid CSS units accepted e.q. % or px.', 'groovy-menu' ),
					'type'        => 'text',
					'default'     => '',
					'save_id'     => self::GM_BADGE_Y_POSITION,
					'lver'        => false,
					'field_class' => 'gm-badge-field gm-badge-field--shared',
				],
				'badge-container-padding'        => [
					'id'          => 'badge-container-padding',
					'label'       => esc_attr__( 'Badge container padding.', 'groovy-menu' ),
					'description' => esc_attr__( 'One, two, three or four values accepted.', 'groovy-menu' ) . ' ' . esc_attr__( 'Any valid CSS units accepted e.q. % or px.', 'groovy-menu' ),
					'type'        => 'text',
					'default'     => '',
					'save_id'     => self::GM_BADGE_CONTAINER_PADDING,
					'lver'        => false,
					'field_class' => 'gm-badge-field gm-badge-field--shared',
				],
				'badge-container-radius'         => [
					'id'          => 'badge-container-radius',
					'label'       => esc_attr__( 'Badge container border radius.', 'groovy-menu' ),
					'description' => esc_attr__( 'One, two, three or four values accepted.', 'groovy-menu' ) . ' ' . esc_attr__( 'Any valid CSS units accepted e.q. % or px.', 'groovy-menu' ),
					'type'        => 'text',
					'default'     => '',
					'save_id'     => self::GM_BADGE_CONTAINER_RADIUS,
					'lver'        => false,
					'field_class' => 'gm-badge-field gm-badge-field--shared',
				],
				'badge-container-bg'             => [
					'id'          => 'badge-container-bg',
					'label'       => esc_attr__( 'Badge container background color', 'groovy-menu' ),
					'description' => '',
					'type'        => 'color',
					'default'     => '',
					'save_id'     => self::GM_BADGE_CONTAINER_BG,
					'lver'        => false,
					'field_class' => 'gm-badge-field gm-badge-field--shared',
				],
				'badge-type'                     => [
					'id'          => 'badge-type',
					'label'       => esc_attr__( 'Badge content type', 'groovy-menu' ),
					'choices'     => self::gmBadgeTypeVariants(),
					'description' => '',
					'type'        => 'select',
					'default'     => 'icon',
					'save_id'     => self::GM_BADGE_TYPE,
					'lver'        => false,
					'field_class' => 'gm-badge-field gm-badge-field--shared',
				],
				'badge-image'                    => [
					'id'          => 'badge-image',
					'label'       => esc_attr__( 'Badge Image', 'groovy-menu' ),
					'description' => '',
					'type'        => 'media',
					'default'     => '',
					'save_id'     => self::GM_BADGE_IMAGE,
					'lver'        => false,
					'field_class' => 'gm-badge-field gm-badge-type--image',
				],
				'badge-icon'                     => [
					'id'          => 'badge-icon',
					'label'       => esc_attr__( 'Badge Icon', 'groovy-menu' ),
					'description' => '',
					'type'        => 'iconpicker',
					'default'     => '',
					'save_id'     => self::GM_BADGE_ICON,
					'lver'        => false,
					'field_class' => 'gm-badge-field gm-badge-type--icon',
				],
				'badge-icon-size'                => [
					'id'          => 'badge-icon-size',
					'label'       => esc_attr__( 'Badge icon size', 'groovy-menu' ),
					'description' => esc_attr__( 'If left blank, it will use the font size from the menu item', 'groovy-menu' ),
					'type'        => 'number',
					'default'     => '',
					'min-value'   => '0',
					'max-value'   => '2000',
					'value-type'  => 'px',
					'save_id'     => self::GM_BADGE_ICON_SIZE,
					'lver'        => false,
					'field_class' => 'gm-badge-field gm-badge-type--icon',
				],
				'badge-icon-color'               => [
					'id'          => 'badge-icon-color',
					'label'       => esc_attr__( 'Badge icon color', 'groovy-menu' ),
					'description' => '',
					'type'        => 'color',
					'default'     => '',
					'save_id'     => self::GM_BADGE_ICON_COLOR,
					'lver'        => false,
					'field_class' => 'gm-badge-field gm-badge-type--icon',
				],
				'badge-text'                     => [
					'id'          => 'badge-text',
					'label'       => esc_attr__( 'Badge text', 'groovy-menu' ),
					'description' => '',
					'type'        => 'text',
					'default'     => '',
					'save_id'     => self::GM_BADGE_TEXT,
					'lver'        => false,
					'field_class' => 'gm-badge-field gm-badge-type--text',
				],
				'badge-text-font-family'         => [
					'id'          => 'badge-text-font-family',
					'label'       => esc_attr__( 'Badge text font family', 'groovy-menu' ),
					'choices'     => $this->gmGetFontArrayForSelect(),
					'description' => '',
					'type'        => 'font',
					'default'     => 'inherit',
					'save_id'     => self::GM_BADGE_TEXT_FONT_FAMILY,
					'lver'        => false,
					'field_class' => 'gm-badge-field gm-badge-type--text',
				],
				'badge-text-font-variant'        => [
					'id'          => 'badge-text-font-variant',
					'label'       => esc_attr__( 'Badge text font variant', 'groovy-menu' ),
					'choices'     => array( '' => esc_attr__( 'Inherit from parent', 'groovy-menu' ) ),
					'description' => '',
					'type'        => 'font_variant',
					'default'     => 'inherit',
					'save_id'     => self::GM_BADGE_TEXT_FONT_VARIANT,
					'lver'        => false,
					'field_class' => 'gm-badge-field gm-badge-type--text',
				],
				'badge-text-font-size'           => [
					'id'          => 'badge-text-font-size',
					'label'       => esc_attr__( 'Badge text font size', 'groovy-menu' ),
					'description' => '',
					'type'        => 'number',
					'default'     => '',
					'min-value'   => '0',
					'max-value'   => '512',
					'value-type'  => 'px',
					'save_id'     => self::GM_BADGE_TEXT_FONT_SIZE,
					'lver'        => false,
					'field_class' => 'gm-badge-field gm-badge-type--text',
				],
				'badge-text-font-color'          => [
					'id'          => 'badge-text-font-color',
					'label'       => esc_attr__( 'Badge text font color', 'groovy-menu' ),
					'description' => '',
					'type'        => 'color',
					'default'     => '',
					'save_id'     => self::GM_BADGE_TEXT_FONT_COLOR,
					'lver'        => false,
					'field_class' => 'gm-badge-field gm-badge-type--text',
				],

			)
		);
	}

	/**
	 * @return array
	 */
	public static function megaMenuColsVariants() {
		$cols_variants = self::$grooniColsVariants;
		if ( isset( $cols_variants['5'] ) ) {
			$cols_variants['5'] = sprintf( esc_html__( '%1$d Columns with %2$s each', 'groovy-menu' ), '5', '20%' );
		}
		if ( isset( $cols_variants['6'] ) ) {
			$cols_variants['6'] = sprintf( esc_html__( '%1$d Columns with %2$s each', 'groovy-menu' ), '6', '16.6%' );
		}
		if ( isset( $cols_variants['7'] ) ) {
			$cols_variants['7'] = sprintf( esc_html__( '%1$d Columns with %2$s each', 'groovy-menu' ), '7', '14.2%' );
		}
		if ( isset( $cols_variants['8'] ) ) {
			$cols_variants['8'] = sprintf( esc_html__( '%1$d Columns with %2$s each', 'groovy-menu' ), '8', '12.5%' );
		}
		if ( isset( $cols_variants['9'] ) ) {
			$cols_variants['9'] = sprintf( esc_html__( '%1$d Columns with %2$s each', 'groovy-menu' ), '9', '11.1%' );
		}
		if ( isset( $cols_variants['10'] ) ) {
			$cols_variants['10'] = sprintf( esc_html__( '%1$d Columns with %2$s each', 'groovy-menu' ), '10', '10%' );
		}

		return $cols_variants;
	}

	/**
	 * @return array
	 */
	public static function gmBadgeTypeVariants() {
		$variants = array(
			'icon'  => esc_html__( 'Icon Badge', 'groovy-menu' ),
			'image' => esc_html__( 'Image Badge', 'groovy-menu' ),
			'text'  => esc_html__( 'Text Badge', 'groovy-menu' ),
		);

		return $variants;
	}

	/**
	 * @return array
	 */
	public static function gmBadgePlacementVariants() {
		$variants = array(
			'left'  => esc_html__( 'Left', 'groovy-menu' ),
			'right' => esc_html__( 'Right', 'groovy-menu' ),
		);

		return $variants;
	}

	/**
	 * @return array
	 */
	public static function gmThumbPositionVariants() {
		$variants = array(
			'above' => esc_html__( 'Above menu text item', 'groovy-menu' ),
			'under' => esc_html__( 'Under menu text item', 'groovy-menu' ),
		);

		return $variants;
	}

	/**
	 * @return array
	 */
	public static function gmBadgeGeneralPositionVariants() {
		$variants = array(
			'relative' => esc_html__( 'Relative', 'groovy-menu' ),
			'absolute' => esc_html__( 'Absolute', 'groovy-menu' ),
		);

		return $variants;
	}

	/**
	 * @return array
	 */
	public static function megaMenuPosts() {
		$mm_posts = array( '' => '--- ' . esc_html__( 'none', 'groovy-menu' ) . ' ---' );

		$args = array(
			'posts_per_page' => - 1,
			'post_type'      => 'gm_menu_block',
			'post_status'    => 'publish',
		);

		$gm_menu_block = get_posts( $args );

		foreach ( $gm_menu_block as $mega_menu_posts ) {
			$mm_posts[ $mega_menu_posts->ID ] = $mega_menu_posts->post_title;
		}

		return $mm_posts;
	}

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
	 * @param $item
	 *
	 * @return mixed
	 */
	protected function getTitle( $item ) {

		$title = '';

		if ( is_object( $item ) ) {

			if ( isset( $item->title ) ) {
				$title = $item->title;
			}

			return $title;
		}

		return $title;
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

		static $meta_data_options = array();

		if ( empty( $this->gm_mass_meta[ $item_id ][ self::GM_NAV_MENU_META ] ) ) {
			$meta_data = get_post_meta( $item_id, self::GM_NAV_MENU_META, $flag );

			if ( is_string( $meta_data ) ) {
				$meta_data = json_decode( $meta_data, true );
			}

			if ( empty( $meta_data_options ) ) {
				$meta_data_options = $this->menu_walker_options();
			}

			foreach ( $meta_data_options as $index => $meta_datum ) {
				if ( ! empty( $meta_datum['save_id'] ) && isset( $meta_datum['type'] ) && 'textarea' === $meta_datum['type'] ) {
					if ( isset( $meta_data[ $meta_datum['save_id'] ] ) ) {
						$meta_data[ $meta_datum['save_id'] ] = base64_decode( $meta_data[ $meta_datum['save_id'] ] );
					}
				}
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
	 * @param object $item         Object with menu item meta data.
	 * @param bool   $check_parent Check data for parent item.
	 *
	 * @return bool
	 */
	protected function isMegaMenu( $item, $check_parent = false ) {
		global $groovyMenuSettings;

		if (
			isset( $groovyMenuSettings['header'] ) &&
			( in_array( intval( $groovyMenuSettings['header']['style'] ), array( 2, 3, 4, 5 ), true ) )
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
	 * @return int|bool
	 */
	protected function dropdownCustomWidth( $item ) {
		$item_id = $this->getId( $item );
		if ( empty( $item_id ) ) {
			return false;
		}

		$val = $this->getGMNavMenuMetaWithCheck( $item_id, self::MEGAMENU_DROPDOWN_CUSTOM_WIDTH, true );
		if ( empty( $val ) ) {
			$val = false;
		}

		$val = intval( $val );

		if ( empty( $val ) ) {
			$val = false;
		}

		return $val;
	}

	/**
	 * @param object $item Object with menu item meta data.
	 *
	 * @return bool
	 */
	protected function frozenLink( $item ) {
		$item_id = $this->getId( $item );
		if ( empty( $item_id ) ) {
			return false;
		}

		$val = $this->getGMNavMenuMetaWithCheck( $item_id, self::FROZEN_LINK, true );
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
		global $wp_filter;

		$mm_content = '';

		if ( $post_id ) {

			$wp_builders = GroovyMenuUtils::check_wp_builders();

			$is_woocommerce_page = false;
			if (
				GroovyMenuUtils::is_shop_and_category_woocommerce_page() ||
				GroovyMenuUtils::is_additional_woocommerce_page() ||
				GroovyMenuUtils::is_product_woocommerce_page()
			) {
				$is_woocommerce_page = true;
			}

			// prevent conflict with Divi theme builder.
			if ( 'divi_builder' === $wp_builders ) {
				return '[ ' . __( 'Divi Builder Conflict Prevention', 'groovy-menu' ) . ' ]';
			}

			// prevent conflict with Avada theme / Fusion builder.
			if ( 'fusion_builder' === $wp_builders ) {
				return '[ ' . __( 'Fusion Builder Conflict Prevention', 'groovy-menu' ) . ' ]';
			}

			// prevent conflict with cornerstone plugin.
			if ( isset( $_POST['cs_preview_state'] ) && isset( $_POST['_cs_nonce'] ) ) { // @codingStandardsIgnoreLine
				return '[ ' . __( 'Cornerstone Conflict Prevention', 'groovy-menu' ) . ' ]';
			}

			if ( isset( $_GET['elementor-preview'] ) ) { // @codingStandardsIgnoreLine
				return '[ ' . __( 'Elementor Conflict Prevention', 'groovy-menu' ) . ' ]';
			}

			if ( isset( $_GET['page_id'] ) && ! empty( $_GET['et_fb'] ) ) { // @codingStandardsIgnoreLine
				return '[ ' . __( 'Divi builder Conflict Prevention', 'groovy-menu' ) . ' ]';
			}


			$post_id = intval( $post_id );

			$wpml_gm_menu_block_id = apply_filters( 'wpml_object_id', $post_id, 'gm_menu_block', true );

			$divi_builder_flag = get_post_meta( $wpml_gm_menu_block_id, '_et_pb_use_builder', true );

			// Post by default.
			$post  = null;
			$_post = null;

			// prevent conflict with Divi theme builder. But not at the Woocommerce pages.
			if ( 'on' === $divi_builder_flag ) {
				$post = null;

				$query = new \WP_Query( 'post_type=gm_menu_block&p=' . $wpml_gm_menu_block_id );
				if ( ! empty( $query->posts ) && ! empty( $query->posts[0] ) ) {
					$post = $query->posts[0];
				}
			} else {
				global $post;

				// Copy global $post exemplar.
				$_post = $post;
				$post  = get_post( $wpml_gm_menu_block_id ); // @codingStandardsIgnoreLine
			}

			if ( empty( $post ) || empty( $post->ID ) ) {
				// Recovery global $post exemplar.
				$post = $_post; // @codingStandardsIgnoreLine

				/**
				 * Reset the original query
				 */
				wp_reset_query();

				return $mm_content;
			}

			// Beaver builder.
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

				$raw_content = empty( $post->post_content ) ? '' : $post->post_content;

				// fix for bbPress function bbp_remove_all_filters('the_content').
				if ( empty( $wp_filter['the_content'] ) ) {

					$mm_content = do_shortcode( $raw_content );

				} else {

					if ( 'on' === $divi_builder_flag ) {
						// Apply all filters for enqueue styles
						$filtered_content = apply_filters( 'the_content', $raw_content );

						$mm_content = do_shortcode( $raw_content );

					} else {
						$mm_content = apply_filters( 'the_content', $raw_content );
					}

				}

			}


			// Recovery global $post exemplar.
			$post = $_post; // @codingStandardsIgnoreLine

			/**
			 * Reset the original query
			 */
			wp_reset_query();

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
	protected function getUseHtmlAsIcon( $item ) {
		$item_id = $this->getId( $item );
		if ( empty( $item_id ) ) {
			return false;
		}

		$val = $this->getGMNavMenuMetaWithCheck( $item_id, self::USE_HTML_AS_ICON, true );
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
	protected function getFirstLetterAsIcon( $item ) {
		$return_default_letter = '?';

		$item_id = $this->getId( $item );
		if ( empty( $item_id ) ) {
			return $return_default_letter;
		}

		$title = (string) $this->getTitle( $item );

		if ( empty( $title ) ) {
			return $return_default_letter;
		}

		if ( function_exists( 'mb_substr' ) ) {
			// Use mb_substr to get the first character.
			$first_letter = mb_substr( $title, 0, 1, 'UTF-8' );
		} else {
			// Use substr to get the first character.
			$first_letter = substr( $title, 0, 1 );
		}

		return $first_letter;
	}

	/**
	 * @param object $item Object with menu item meta data.
	 *
	 * @return mixed
	 */
	protected function getHtmlIconContent( $item ) {
		$item_id = $this->getId( $item );
		if ( empty( $item_id ) ) {
			return '';
		}

		$content = $this->getGMNavMenuMetaWithCheck( $item_id, self::HTML_ICON_CONTENT, true );

		if ( ! empty( $content ) ) {
			$content = do_shortcode( $content );
		}

		return $content;
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
		$this->gm_google_fonts = include GROOVY_MENU_DIR . 'includes' . DIRECTORY_SEPARATOR . 'fonts-google.php';

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
