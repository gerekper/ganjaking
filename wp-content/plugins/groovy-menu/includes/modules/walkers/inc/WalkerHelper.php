<?php

namespace GroovyMenu;

use \GroovyMenu\WalkerNavMenu as WalkerNavMenu;
use \GroovyMenuStyle as GroovyMenuStyle;
use \GroovyMenuUtils as GroovyMenuUtils;


defined( 'ABSPATH' ) || die( 'This script cannot be accessed directly.' );


/**
 * Class AdminHelper
 */
class WalkerHelper extends WalkerNavMenu {

	/**
	 * Hooks to the necessary actions and filters
	 */
	public function __construct() {

		$lic_opt = get_option( GROOVY_MENU_DB_VER_OPTION . '__lic' );
		$lver    = false;
		if ( defined( 'GROOVY_MENU_LVER' ) && '2' === GROOVY_MENU_LVER ) {
			$lver = true;
		}

		// Save navigation menu item options.
		add_action( 'wp_ajax_gm_save_menu_item_options', array( $this, 'save_menu_item_options' ) );

		if ( $lic_opt || $lver ) {
			// Add the menu style button to the menu fields.
			add_action( 'wp_nav_menu_item_custom_fields', array( $this, 'add_menu_button_fields' ), 10, 4 );
		}

	}

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
	 * @return string
	 */
	public static function nonce_for_save() {
		static $nonce = '';

		if ( ! empty( $nonce ) ) {
			return $nonce;
		}

		$nonce = esc_attr( wp_create_nonce( 'gm_nonce_menu_item_save' ) );

		return $nonce;
	}

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
				'icon-class'               => [
					'id'          => 'icon-class',
					'label'       => esc_attr__( 'Icon', 'groovy-menu' ),
					'description' => esc_attr__( 'Select an icon for your menu item.', 'groovy-menu' ),
					'type'        => 'iconpicker',
					'default'     => '',
					'save_id'     => self::ICON_CLASS,
				],
				'do-not-show-title'        => [
					'id'          => 'do-not-show-title',
					'label'       => esc_attr__( 'Do not show menu item title and link', 'groovy-menu' ),
					'description' => '',
					'type'        => 'checkbox',
					'default'     => false,
					'save_id'     => self::DO_NOT_SHOW_TITLE,
				],
				'is-show-featured'         => [
					'id'          => 'is-show-featured',
					'label'       => esc_attr__( 'Show featured image on hover', 'groovy-menu' ),
					'description' => '',
					'type'        => 'checkbox',
					'post_type'   => 'post_type',
					'default'     => false,
					'save_id'     => self::IS_SHOW_FEATURED_IMAGE,
				],
				// -------------------------------------------------------------------------------- MEGAMENU settings
				'megamenu-post-not-mobile' => [
					'id'          => 'megamenu-post-not-mobile',
					'label'       => esc_attr__( 'Do not show Menu block content on mobile', 'groovy-menu' ),
					'description' => '',
					'type'        => 'checkbox',
					'default'     => false,
					'lver'        => false,
					'save_id'     => self::MEGAMENU_META_POST_NOT_MOBILE,
				],
				'megamenu'                 => [
					'id'          => 'megamenu',
					'label'       => esc_attr__( 'Mega menu', 'groovy-menu' ),
					'description' => esc_attr__( 'Applies to first level menu only.', 'groovy-menu' ),
					'type'        => 'checkbox',
					'default'     => false,
					'save_id'     => self::IS_MEGAMENU_META,
					'depth'       => 0,
				],
				'megamenu-cols'            => [
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
				'megamenu-bg'              => [
					'id'          => 'megamenu-bg',
					'label'       => esc_attr__( 'Mega menu background image', 'groovy-menu' ),
					'description' => '',
					'type'        => 'media',
					'default'     => '',
					'save_id'     => self::MEGAMENU_BACKGROUND,
					'depth'       => 0,
					'field_class' => 'megamenu-options-depend',
				],
				'megamenu-bg-position'     => [
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
				'megamenu-bg-repeat'       => [
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
				'megamenu-bg-size'         => [
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
				'thumb-enable'             => [
					'id'          => 'thumb-enable',
					'label'       => esc_attr__( 'Enable thumbnail', 'groovy-menu' ),
					'description' => esc_attr__( 'Show thumbnail for post.', 'groovy-menu' ),
					'type'        => 'checkbox',
					'default'     => false,
					'save_id'     => self::GM_THUMB_ENABLE,
					'lver'        => false,
				],
				'thumb-position'           => [
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
				'thumb-max-height'         => [
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
				'thumb-with-url'           => [
					'id'          => 'thumb-with-url',
					'label'       => esc_attr__( 'Wrap thumbnail with menu item URL', 'groovy-menu' ),
					'description' => '',
					'type'        => 'checkbox',
					'default'     => false,
					'save_id'     => self::GM_THUMB_WITH_URL,
					'lver'        => false,
					'field_class' => 'gm-thumb-field',
				],
				'thumb-image'              => [
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
				'badge-enable'             => [
					'id'          => 'badge-enable',
					'label'       => esc_attr__( 'Enable badge', 'groovy-menu' ),
					'description' => esc_attr__( 'Show badge with menu item', 'groovy-menu' ),
					'type'        => 'checkbox',
					'default'     => false,
					'save_id'     => self::GM_BADGE_ENABLE,
					'lver'        => false,
				],
				'badge-placement'          => [
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
				'badge-general-position'   => [
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
				'badge-x-position'         => [
					'id'          => 'badge-x-position',
					'label'       => esc_attr__( 'Badge X offset.', 'groovy-menu' ),
					'description' => esc_attr__( 'Negative value will push badge left, positive right.', 'groovy-menu' ) . ' ' . esc_attr__( 'Any valid CSS units accepted e.q. % or px.', 'groovy-menu' ),
					'type'        => 'text',
					'default'     => '',
					'save_id'     => self::GM_BADGE_X_POSITION,
					'lver'        => false,
					'field_class' => 'gm-badge-field gm-badge-field--shared',
				],
				'badge-y-position'         => [
					'id'          => 'badge-y-position',
					'label'       => esc_attr__( 'Badge Y offset.', 'groovy-menu' ),
					'description' => esc_attr__( 'Negative value will push badge up, positive down.', 'groovy-menu' ) . ' ' . esc_attr__( 'Any valid CSS units accepted e.q. % or px.', 'groovy-menu' ),
					'type'        => 'text',
					'default'     => '',
					'save_id'     => self::GM_BADGE_Y_POSITION,
					'lver'        => false,
					'field_class' => 'gm-badge-field gm-badge-field--shared',
				],
				'badge-container-padding'  => [
					'id'          => 'badge-container-padding',
					'label'       => esc_attr__( 'Badge container padding.', 'groovy-menu' ),
					'description' => esc_attr__( 'One, two, three or four values accepted.', 'groovy-menu' ) . ' ' . esc_attr__( 'Any valid CSS units accepted e.q. % or px.', 'groovy-menu' ),
					'type'        => 'text',
					'default'     => '',
					'save_id'     => self::GM_BADGE_CONTAINER_PADDING,
					'lver'        => false,
					'field_class' => 'gm-badge-field gm-badge-field--shared',
				],
				'badge-container-radius'   => [
					'id'          => 'badge-container-radius',
					'label'       => esc_attr__( 'Badge container border radius.', 'groovy-menu' ),
					'description' => esc_attr__( 'One, two, three or four values accepted.', 'groovy-menu' ) . ' ' . esc_attr__( 'Any valid CSS units accepted e.q. % or px.', 'groovy-menu' ),
					'type'        => 'text',
					'default'     => '',
					'save_id'     => self::GM_BADGE_CONTAINER_RADIUS,
					'lver'        => false,
					'field_class' => 'gm-badge-field gm-badge-field--shared',
				],
				'badge-container-bg'       => [
					'id'          => 'badge-container-bg',
					'label'       => esc_attr__( 'Badge container background color', 'groovy-menu' ),
					'description' => '',
					'type'        => 'color',
					'default'     => '',
					'save_id'     => self::GM_BADGE_CONTAINER_BG,
					'lver'        => false,
					'field_class' => 'gm-badge-field gm-badge-field--shared',
				],
				'badge-type'               => [
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
				'badge-image'              => [
					'id'          => 'badge-image',
					'label'       => esc_attr__( 'Badge Image', 'groovy-menu' ),
					'description' => '',
					'type'        => 'media',
					'default'     => '',
					'save_id'     => self::GM_BADGE_IMAGE,
					'lver'        => false,
					'field_class' => 'gm-badge-field gm-badge-type--image',
				],
				'badge-icon'               => [
					'id'          => 'badge-icon',
					'label'       => esc_attr__( 'Badge Icon', 'groovy-menu' ),
					'description' => '',
					'type'        => 'iconpicker',
					'default'     => '',
					'save_id'     => self::GM_BADGE_ICON,
					'lver'        => false,
					'field_class' => 'gm-badge-field gm-badge-type--icon',
				],
				'badge-icon-size'          => [
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
				'badge-icon-color'         => [
					'id'          => 'badge-icon-color',
					'label'       => esc_attr__( 'Badge icon color', 'groovy-menu' ),
					'description' => '',
					'type'        => 'color',
					'default'     => '',
					'save_id'     => self::GM_BADGE_ICON_COLOR,
					'lver'        => false,
					'field_class' => 'gm-badge-field gm-badge-type--icon',
				],
				'badge-text'               => [
					'id'          => 'badge-text',
					'label'       => esc_attr__( 'Badge text', 'groovy-menu' ),
					'description' => '',
					'type'        => 'text',
					'default'     => '',
					'save_id'     => self::GM_BADGE_TEXT,
					'lver'        => false,
					'field_class' => 'gm-badge-field gm-badge-type--text',
				],
				'badge-text-font-family'   => [
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
				'badge-text-font-variant'  => [
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
				'badge-text-font-size'     => [
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
				'badge-text-font-color'    => [
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
	 * Adds the menu button fields.
	 *
	 * @access public
	 *
	 * @param string $item_id The ID of the menu item.
	 * @param object $item    The menu item object.
	 * @param int    $depth   The depth of the current item in the menu.
	 * @param array  $args    Menu arguments.
	 *
	 * @return void.
	 */
	public function add_menu_button_fields( $item_id, $item, $depth, $args ) {
		?>
		<div class="gm-menu-options-container" data-item-id="<?php echo esc_attr( $item_id ); ?>">
			<a class="button button-primary button-large gm-menu-open-modal-button"
				href="#"><?php esc_attr_e( 'Groovy Menu item options', 'groovy-menu' ); ?></a>
			<div class="gm-walker-modal-overlay" style="display:none"></div>
			<div id="gm-menu-options-<?php echo esc_attr( $item_id ); ?>"
				class="gm-walker-modal-settings-container" style="display:none"
				data-item-id="<?php echo esc_attr( $item_id ); ?>">
				<div class="gm-walker-modal-container">
					<div class="gm-walker-modal-top-container">
						<h2><?php esc_attr_e( 'Groovy Menu item options', 'groovy-menu' ); ?></h2>
						<div class="gm-walker-modal-close dashicons dashicons-no"></span></div>
					</div>
					<div class="gm-walker-modal-bottom-container">
						<a href="#"
							class="gm-walker-modal-save" data-nonce="<?php echo esc_attr( self::nonce_for_save() ); ?>"><span><?php esc_attr_e( 'Save', 'groovy-menu' ); ?></span></a>
						<a href="#"
							class="gm-walker-modal-close"><span><?php esc_attr_e( 'Cancel', 'groovy-menu' ); ?></span></a>
					</div>
					<div class="gm-walker-modal-main-container">
						<div class="gm-walker-modal-main-container-wrapper">
							<h3 class="gm-walker-modal-item-title"><?php echo sprintf( esc_html__( 'Menu item title', 'groovy-menu' ) . ': %s', esc_js( $item->title ) ); ?></h3>
							<?php $this->parse_options( $item_id, $item, $depth, $args ); ?>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php
	}


	/**
	 * Check and make default parameters.
	 *
	 * @access public
	 *
	 * @param array $field parameters.
	 *
	 * @return array.
	 */
	public function check_defaults( $field ) {

		if ( empty( $field ) || ! is_array( $field ) ) {
			$field = array();
		}
		// Defaults.
		$field['id']          = isset( $field['id'] ) ? $field['id'] : '';
		$field['type']        = isset( $field['type'] ) ? $field['type'] : 'empty';
		$field['label']       = isset( $field['label'] ) ? $field['label'] : '';
		$field['description'] = isset( $field['description'] ) ? $field['description'] : '';
		$field['desc']        = $field['description'];
		$field['choices']     = isset( $field['choices'] ) ? $field['choices'] : array();
		$field['default']     = isset( $field['default'] ) ? $field['default'] : '';
		$field['min-value']   = isset( $field['min-value'] ) ? $field['min-value'] : '0';
		$field['max-value']   = isset( $field['max-value'] ) ? $field['max-value'] : '5000';
		$field['value-type']  = isset( $field['value-type'] ) ? $field['value-type'] : '';
		$field['post_type']   = ( isset( $field['post_type'] ) && is_string( $field['post_type'] ) ) ? $field['post_type'] : null;
		$field['save_id']     = isset( $field['save_id'] ) ? $field['save_id'] : 'groovy_menu_' . str_replace( '-', '_', $field['id'] );
		$field['lver']        = isset( $field['lver'] ) ? boolval( $field['lver'] ) : true;
		$field['depth']       = ( isset( $field['depth'] ) && is_numeric( $field['depth'] ) ) ? $field['depth'] : null;
		$field['field_class'] = ( isset( $field['field_class'] ) && is_string( $field['field_class'] ) ) ? $field['field_class'] : '';

		return $field;
	}

	/**
	 * Adds the markup for the options.
	 *
	 * @access public
	 *
	 * @param string $item_id The ID of the menu item.
	 * @param object $item    The menu item object.
	 * @param int    $depth   The depth of the current item in the menu.
	 * @param array  $args    Menu arguments.
	 *
	 * @return void.
	 */
	public function parse_options( $item_id, $item, $depth, $args ) {

		$options = $this->menu_walker_options();

		if ( ! empty( $options ) && is_array( $options ) ) {

			$lver = false;
			if ( defined( 'GROOVY_MENU_LVER' ) && '2' === GROOVY_MENU_LVER ) {
				$lver = true;
			}

			foreach ( $options as $field ) {

				$field = $this->check_defaults( $field );

				$depth_allowed = true;
				if ( null !== $field['depth'] && intval( $field['depth'] ) !== intval( $depth ) ) {
					$depth_allowed = false;
				}

				$post_type_allowed = true;
				if ( null !== $field['post_type'] && strval( $field['post_type'] ) !== strval( $item->type ) ) {
					$post_type_allowed = false;
				}

				$lver_allowed = false;
				if ( ! $lver || $field['lver'] ) {
					$lver_allowed = true;
				}

				if ( $depth_allowed && $post_type_allowed && $lver_allowed ) {
					switch ( $field['type'] ) {

						case 'text':
							$this->text( $item_id, $item, $field );
							break;
						case 'number':
							$this->number( $item_id, $item, $field );
							break;
						case 'radio':
							$this->radio( $item_id, $item, $field );
							break;
						case 'checkbox':
							$this->checkbox( $item_id, $item, $field );
							break;
						case 'select':
							$this->select( $item_id, $item, $field );
							break;
						case 'font':
						case 'font_variant':
							$this->font( $item_id, $item, $field );
							break;
						case 'color':
							$this->color( $item_id, $item, $field );
							break;
						case 'media':
							$this->media( $item_id, $item, $field );
							break;
						case 'iconpicker':
							$this->iconpicker( $item_id, $item, $field );
							break;
					}
				}
			}
		}
	}


	/**
	 * Return html classes for option field.
	 *
	 * @access public
	 *
	 * @param string $item_id The ID of the menu item.
	 * @param object $item    The menu item object.
	 * @param array  $field   parameters for field.
	 *
	 * @return string
	 */
	public function field_html_class( $item_id, $item, $field ) {
		$html_class = array(
			'gm-walker-option',
			'gm-field-id-' . esc_attr( $field['id'] ),
			'gm-field-type-' . esc_attr( $field['type'] ),
			esc_attr( $field['field_class'] ),
			esc_attr( $field['save_id'] ),
		);

		return implode( ' ', $html_class );
	}

	/**
	 * Return html classes for option input.
	 *
	 * @access public
	 *
	 * @param string $item_id The ID of the menu item.
	 * @param object $item    The menu item object.
	 * @param array  $field   parameters for field.
	 *
	 * @return string
	 */
	public function option_html_class( $item_id, $item, $field ) {
		return 'gm-option-type-' . esc_attr( $field['type'] ) . ' edit-menu-item-gm-' . esc_attr( $field['id'] );
	}

	/**
	 * Return html id for option input.
	 *
	 * @access public
	 *
	 * @param string $item_id The ID of the menu item.
	 * @param object $item    The menu item object.
	 * @param array  $field   parameters for field.
	 *
	 * @return string
	 */
	public function option_html_id( $item_id, $item, $field ) {
		return 'edit-menu-item-gm-' . esc_attr( $field['id'] ) . '-' . strval( $item_id );
	}

	/**
	 * Return html name for option input.
	 *
	 * @access public
	 *
	 * @param string $item_id The ID of the menu item.
	 * @param object $item    The menu item object.
	 * @param array  $field   parameters for field.
	 *
	 * @return string
	 */
	public function option_html_name( $item_id, $item, $field ) {
		return esc_attr( $field['save_id'] ) . '[' . strval( $item_id ) . ']';
	}

	/**
	 * Text controls.
	 *
	 * @access public
	 *
	 * @param string $item_id The ID of the menu item.
	 * @param object $item    The menu item object.
	 * @param array  $field   parameters for field.
	 */
	public function text( $item_id, $item, $field ) {
		$saved_value = $this->getGMNavMenuMetaWithCheck( $item_id, esc_attr( $field['save_id'] ), true );
		if ( false === $saved_value ) {
			$saved_value = '';
		}

		?>
		<div class="<?php echo esc_attr( $this->field_html_class( $item_id, $item, $field ) ); ?>">
			<div class="gm-option-details">
				<h3><?php echo esc_js( $field['label'] ); ?></h3>
				<p class="description"><?php echo esc_js( $field['desc'] ); ?></p>
			</div>
			<div class="gm-option-field gm-walker-option-container">
				<input type="text"
					id="<?php echo esc_attr( $this->option_html_id( $item_id, $item, $field ) ); ?>"
					class="<?php echo esc_attr( $this->option_html_class( $item_id, $item, $field ) ); ?>"
					name="<?php echo esc_attr( $this->option_html_name( $item_id, $item, $field ) ); ?>"
					value="<?php echo esc_attr( $saved_value ); ?>"/>
			</div>
		</div>
		<?php
	}

	/**
	 * Radio button set field.
	 *
	 * @access public
	 *
	 * @param string $item_id The ID of the menu item.
	 * @param object $item    The menu item object.
	 * @param array  $field   parameters for field.
	 */
	public function radio( $item_id, $item, $field ) {
		$saved_value = $this->getGMNavMenuMetaWithCheck( $item_id, esc_attr( $field['save_id'] ), true );
		if ( false === $saved_value ) {
			$saved_value = '';
		}

		?>
		<div class="<?php echo esc_attr( $this->field_html_class( $item_id, $item, $field ) ); ?>">
			<div class="gm-option-details">
				<h3><?php echo esc_js( $field['label'] ); ?></h3>
				<p class="description"><?php echo esc_js( $field['desc'] ); ?></p>
			</div>
			<div class="gm-option-field gm-walker-option-container">
				<div
					class="gm-form-radio-button-set ui-buttonset">
					<input type="hidden"
						id="<?php echo esc_attr( $this->option_html_id( $item_id, $item, $field ) ); ?>"
						name="<?php echo esc_attr( $this->option_html_name( $item_id, $item, $field ) ); ?>"
						value="<?php echo esc_attr( $saved_value ); ?>"
						class="<?php echo esc_attr( $this->option_html_class( $item_id, $item, $field ) ); ?>"/>
					<?php foreach ( $field['choices'] as $value => $label ) : ?>
						<?php $value_check = ( '' !== $saved_value ) ? $saved_value : $field['default']; ?>
						<a href="#"
							class="ui-button buttonset-item<?php echo ( $value === $value_check ) ? ' gm-state-active' : ''; ?>"
							data-value="<?php echo esc_attr( $value ); ?>"><?php echo esc_js( $label ); ?></a>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Checkbox field.
	 *
	 * @access public
	 *
	 * @param string $item_id The ID of the menu item.
	 * @param object $item    The menu item object.
	 * @param array  $field   parameters for field.
	 */
	public function checkbox( $item_id, $item, $field ) {
		$value = '';

		$saved_value = $this->getGMNavMenuMetaWithCheck( $item_id, esc_attr( $field['save_id'] ), true );
		if ( empty( $saved_value ) || ! $saved_value || 'none' === $saved_value || 'false' === $saved_value ) {
			$saved_value = false;
		}

		if ( $saved_value ) {
			$value = 'checked=checked';
		}

		?>
		<div class="<?php echo esc_attr( $this->field_html_class( $item_id, $item, $field ) ); ?>">
			<div class="gm-option-details">
				<h3><?php echo esc_js( $field['label'] ); ?></h3>
				<p class="description"><?php echo esc_js( $field['desc'] ); ?></p>
			</div>
			<div class="gm-option-field gm-walker-option-container">
				<div class="gm-form-checkbox">
					<input
						type="checkbox"
						value="enabled"
						class="<?php echo esc_attr( $this->option_html_class( $item_id, $item, $field ) ); ?>"
						id="<?php echo esc_attr( $this->option_html_id( $item_id, $item, $field ) ); ?>"
						name="<?php echo esc_attr( $this->option_html_name( $item_id, $item, $field ) ); ?>"
						<?php echo esc_attr( $value ); ?>
					/>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Select field.
	 *
	 * @access public
	 *
	 * @param string $item_id The ID of the menu item.
	 * @param object $item    The menu item object.
	 * @param array  $field   parameters for field.
	 */
	public function select( $item_id, $item, $field ) {
		$saved_value = $this->getGMNavMenuMetaWithCheck( $item_id, esc_attr( $field['save_id'] ), true );
		if ( false === $saved_value ) {
			$saved_value = $field['default'];
		}

		if ( empty( $field['choices'] ) || ! is_array( $field['choices'] ) ) {
			$field['choices'] = array( '' => '---' );
		}

		?>
		<div class="<?php echo esc_attr( $this->field_html_class( $item_id, $item, $field ) ); ?>">
			<div class="gm-option-details">
				<h3><?php echo esc_js( $field['label'] ); ?></h3>
				<p class="description"><?php echo esc_js( $field['desc'] ); ?></p>
			</div>
			<div class="gm-option-field gm-walker-option-container">
				<select class="gm-option-field-select">
					<?php foreach ( $field['choices'] as $value => $label ) { ?>
						<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $saved_value, $value ); ?>>
							<?php echo esc_js( $label ); ?>
						</option>
					<?php }; ?>
				</select>
				<input
					type="hidden"
					value="<?php echo esc_attr( $saved_value ); ?>"
					class="gm-option-field-select-hidden <?php echo esc_attr( $this->option_html_class( $item_id, $item, $field ) ); ?>"
					id="<?php echo esc_attr( $this->option_html_id( $item_id, $item, $field ) ); ?>"
					name="<?php echo esc_attr( $this->option_html_name( $item_id, $item, $field ) ); ?>">
			</div>
		</div>
		<?php
	}

	/**
	 * Select font field.
	 *
	 * @access public
	 *
	 * @param string $item_id The ID of the menu item.
	 * @param object $item    The menu item object.
	 * @param array  $field   parameters for field.
	 */
	public function font( $item_id, $item, $field ) {
		$saved_value = $this->getGMNavMenuMetaWithCheck( $item_id, esc_attr( $field['save_id'] ), true );
		if ( false === $saved_value ) {
			$saved_value = '';
		}

		?>
		<div class="<?php echo esc_attr( $this->field_html_class( $item_id, $item, $field ) ); ?>">
			<div class="gm-option-details">
				<h3><?php echo esc_js( $field['label'] ); ?></h3>
				<p class="description"><?php echo esc_js( $field['desc'] ); ?></p>
			</div>
			<div class="gm-option-field gm-walker-option-container">
				<select class="gm-option-field-select"
					data-item-id="<?php echo esc_attr( $item_id ); ?>">
					<?php foreach ( $field['choices'] as $family => $variants ) { ?>
						<?php $name = '' === $family ? esc_html__( 'Inherit from parent', 'groovy-menu' ) : $family; ?>
						<option
							value="<?php echo esc_attr( $family ); ?>" <?php selected( $saved_value, $family ); ?>
							data-variants="<?php echo esc_attr( $variants ); ?>"><?php echo esc_attr( $name ); ?></option>
					<?php } ?>
				</select>
				<input
					type="hidden"
					value="<?php echo esc_attr( $saved_value ); ?>"
					class="gm-option-field-select-hidden <?php echo esc_attr( $this->option_html_class( $item_id, $item, $field ) ); ?>"
					id="<?php echo esc_attr( $this->option_html_id( $item_id, $item, $field ) ); ?>"
					name="<?php echo esc_attr( $this->option_html_name( $item_id, $item, $field ) ); ?>">
			</div>
		</div>
		<?php
	}


	/**
	 * Number controls.
	 *
	 * @access public
	 *
	 * @param string $item_id The ID of the menu item.
	 * @param object $item    The menu item object.
	 * @param array  $field   parameters for field.
	 */
	public function number( $item_id, $item, $field ) {
		$saved_value = $this->getGMNavMenuMetaWithCheck( $item_id, esc_attr( $field['save_id'] ), true );
		if ( false === $saved_value ) {
			$saved_value = '';
		}

		?>
		<div class="<?php echo esc_attr( $this->field_html_class( $item_id, $item, $field ) ); ?>">
			<div class="gm-option-details">
				<h3><?php echo esc_js( $field['label'] ); ?></h3>
				<p class="description"><?php echo esc_js( $field['desc'] ); ?></p>
			</div>
			<div class="gm-option-field gm-walker-option-container">
				<input
					type="number"
					min="<?php echo esc_attr( $field['min-value'] ); ?>"
					max="<?php echo esc_attr( $field['max-value'] ); ?>"
					value="<?php echo esc_attr( $saved_value ); ?>"
					class="<?php echo esc_attr( $this->option_html_class( $item_id, $item, $field ) ); ?>"
					id="<?php echo esc_attr( $this->option_html_id( $item_id, $item, $field ) ); ?>"
					name="<?php echo esc_attr( $this->option_html_name( $item_id, $item, $field ) ); ?>"
				/> <?php echo esc_js( $field['value-type'] ); ?>
			</div>
		</div>
		<?php
	}


	/**
	 * Icon field.
	 *
	 * @access public
	 *
	 * @param string $item_id The ID of the menu item.
	 * @param object $item    The menu item object.
	 * @param array  $field   parameters for field.
	 */
	public function iconpicker( $item_id, $item, $field ) {
		$saved_value = $this->getGMNavMenuMetaWithCheck( $item_id, esc_attr( $field['save_id'] ), true );
		if ( false === $saved_value ) {
			$saved_value = '';
		}

		?>
		<div class="<?php echo esc_attr( $this->field_html_class( $item_id, $item, $field ) ); ?>">
			<div class="gm-option-details">
				<h3><?php echo esc_js( $field['label'] ); ?></h3>
				<p class="description"><?php echo esc_js( $field['desc'] ); ?></p>
			</div>
			<div class="gm-option-field gm-walker-option-container gm-iconpicker description">
				<div class="gm-icon-preview">
					<span class="<?php echo esc_attr( $saved_value ); ?>"></span>
				</div>
				<input
					type="text"
					value="<?php echo esc_attr( $saved_value ); ?>"
					class="gm-icon-field <?php echo esc_attr( $this->option_html_class( $item_id, $item, $field ) ); ?>"
					id="<?php echo esc_attr( $this->option_html_id( $item_id, $item, $field ) ); ?>"
					name="<?php echo esc_attr( $this->option_html_name( $item_id, $item, $field ) ); ?>"
				/>
				<button
					type="button"
					class="gm-select-icon gm-icons-modal">
					<?php esc_html_e( 'Select icon', 'groovy-menu' ); ?>
				</button>
			</div>
		</div>
		<?php
	}

	/**
	 * Color alpha field.
	 *
	 * @access public
	 *
	 * @param string $item_id The ID of the menu item.
	 * @param object $item    The menu item object.
	 * @param array  $field   parameters for field.
	 */
	public function color( $item_id, $item, $field ) {
		$saved_value = $this->getGMNavMenuMetaWithCheck( $item_id, esc_attr( $field['save_id'] ), true );
		if ( false === $saved_value ) {
			$saved_value = '';
		}

		?>
		<div class="<?php echo esc_attr( $this->field_html_class( $item_id, $item, $field ) ); ?>">
			<div class="gm-option-details">
				<h3><?php echo esc_js( $field['label'] ); ?></h3>
				<p class="description"><?php esc_js( $field['desc'] ); ?></p>
			</div>
			<div class="gm-option-field gm-walker-option-container">
				<input type="text"
					id="<?php echo esc_attr( $this->option_html_id( $item_id, $item, $field ) ); ?>"
					class="<?php echo esc_attr( $this->option_html_class( $item_id, $item, $field ) ); ?> gm-colorpicker gm-appearance-colorpicker <?php echo esc_attr( $field['save_id'] ); ?>"
					data-alpha="true"
					name="<?php echo esc_attr( $this->option_html_name( $item_id, $item, $field ) ); ?>"
					value="<?php echo esc_attr( $saved_value ); ?>"/>
			</div>
		</div>
		<?php
	}

	/**
	 * Media field.
	 *
	 * @access public
	 *
	 * @param string $item_id The ID of the menu item.
	 * @param object $item    The menu item object.
	 * @param array  $field   parameters for field.
	 */
	public function media( $item_id, $item, $field ) {
		$saved_value = $this->getGMNavMenuMetaWithCheck( $item_id, esc_attr( $field['save_id'] ), true );
		if ( false === $saved_value ) {
			$saved_value = '';
		}

		$preview_url = '';

		// Get preview of the image ($saved_value).
		if ( ! empty( $saved_value ) ) {
			if ( false === filter_var( $saved_value, FILTER_VALIDATE_URL ) ) {
				$preview_url = wp_get_attachment_url( $saved_value );
			} else {
				$preview_url = $saved_value;
			}
		}

		?>
		<div class="<?php echo esc_attr( $this->field_html_class( $item_id, $item, $field ) ); ?>">
			<div class="gm-option-details">
				<h3><?php echo esc_js( $field['label'] ); ?></h3>
				<p class="description"><?php echo esc_js( $field['desc'] ); ?></p>
			</div>
			<div class="gm-option-field gm-walker-option-container">
				<div class="gm-option-image-preview" id="gm-option-image-preview-<?php echo esc_attr( $item_id ); ?>">
					<?php if ( ! empty( $preview_url ) ) : ?>
						<img src="<?php echo esc_attr( $preview_url ); ?>" alt="">
					<?php endif; ?>
				</div>
				<input
					type="hidden"
					value="<?php echo esc_attr( $saved_value ); ?>"
					class="gm-option-img <?php echo esc_attr( $this->option_html_class( $item_id, $item, $field ) ); ?>"
					id="<?php echo esc_attr( $this->option_html_id( $item_id, $item, $field ) ); ?>"
					name="<?php echo esc_attr( $this->option_html_name( $item_id, $item, $field ) ); ?>">
				<input
					type="hidden"
					value="<?php echo esc_attr( $this->getBadgeImageWidth( $item ) ); ?>"
					class="gm-option-img-width"
					id="<?php echo esc_attr( $field['save_id'] ); ?>_width-<?php echo esc_attr( $item_id ); ?>"
					name="<?php echo esc_attr( $field['save_id'] ); ?>_width[<?php echo esc_attr( $item_id ); ?>]">
				<input type="hidden"
					value="<?php echo esc_attr( $this->getBadgeImageHeight( $item ) ); ?>"
					class="gm-option-img-height"
					id="<?php echo esc_attr( $field['save_id'] ); ?>_height-<?php echo esc_attr( $item_id ); ?>"
					name="<?php echo esc_attr( $field['save_id'] ); ?>_height[<?php echo esc_attr( $item_id ); ?>]">
				<button
					type="button"
					class="button button-primary gm-option-select-img"
					data-item-id="<?php echo esc_attr( $item_id ); ?>"
					data-save-id="<?php echo esc_attr( $field['save_id'] ); ?>"
					data-uploader_title="<?php esc_html_e( 'Select Image', 'groovy-menu' ); ?>"
					data-uploader_button_text="<?php esc_html_e( 'Insert image', 'groovy-menu' ); ?>"
				>
					<?php esc_html_e( 'Select image', 'groovy-menu' ); ?>
				</button>
				<button type="button"
					class="button gm-option-remove-img"
					data-item-id="<?php echo esc_attr( $item_id ); ?>"
					data-save-id="<?php echo esc_attr( $field['save_id'] ); ?>"
				>
					<?php esc_html_e( 'Remove image', 'groovy-menu' ); ?>
				</button>
			</div>
		</div>
		<?php
	}


	/**
	 * Save menu item options.
	 */
	function save_menu_item_options() {
		$cap_can = true;

		if ( ! isset( $_POST['gm_nonce'] ) || ! wp_verify_nonce( $_POST['gm_nonce'], 'gm_nonce_menu_item_save' ) ) { // @codingStandardsIgnoreLine
			$cap_can = false;
		}

		if ( $cap_can && defined( 'DOING_AJAX' ) && DOING_AJAX && ! empty( $_POST ) && isset( $_POST['action'] ) && $_POST['action'] === 'gm_save_menu_item_options' ) { // @codingStandardsIgnoreLine

			// @codingStandardsIgnoreStart
			$item_id      = empty( $_POST['item_id'] ) ? '' : trim( $_POST['item_id'] );
			$ajax_options = ( empty( $_POST['options'] ) || ! is_string( $_POST['options'] ) ) ? '' : trim( $_POST['options'] );
			// @codingStandardsIgnoreEnd

			$parsed_options = array();
			$ajax_options   = json_decode( stripslashes( $ajax_options ), true );

			// Check bad params ---------------------------------------------------------------------------------------.
			if ( empty( $ajax_options ) || ! is_array( $ajax_options ) ) {
				// Send a JSON response back to an AJAX request, and die().
				wp_send_json_error( esc_html__( 'Error. Corrupted data of the menu item', 'groovy-menu' ) );
			}

			if ( empty( $item_id ) ) {
				// Send a JSON response back to an AJAX request, and die().
				wp_send_json_error( esc_html__( 'Error. Missing id of the current menu', 'groovy-menu' ) );
			}

			// Parse params -------------------------------------------------------------------------------------------.
			foreach ( $ajax_options as $opt_key => $opt_value ) {
				// For input names that are arrays (e.g. `menu-item-db-id[3][4][5]`),
				// derive the array path keys via regex.
				preg_match( '#([^\[]*)(\[(.+)\])?#', $opt_key, $matches );

				$array_bits = array( $matches[1] );

				if ( isset( $matches[3] ) ) {
					$array_bits = array_merge( $array_bits, explode( '][', $matches[3] ) );
				}

				$parsed_data = array();

				// Build the new array value from leaf to trunk.
				for ( $i = count( $array_bits ) - 1; $i >= 0; $i -- ) {
					if ( count( $array_bits ) - 1 === $i ) {
						$parsed_data[ $array_bits[ $i ] ] = wp_slash( $opt_value );
					} else {
						$parsed_data = array( $array_bits[ $i ] => $parsed_data );
					}
				}

				$parsed_options = array_replace_recursive( $parsed_options, $parsed_data );
			}

			// Get prepared params names. Work only with that names. --------------------------------------------------.
			$meta_data = self::$menu_item_options;

			$mass_meta = array();

			foreach ( $meta_data as $index => $meta_datum ) {
				// Get new value.
				$new_val = isset( $meta_datum['default'] ) ? $meta_datum['default'] : '';

				$meta_name = isset( $meta_datum['meta_name'] ) ? $meta_datum['meta_name'] : null;
				if ( empty( $meta_name ) ) {
					continue;
				}

				// @codingStandardsIgnoreStart
				if ( isset( $parsed_options[ $meta_name ][ $item_id ] ) ) {
					$new_val = sanitize_text_field( wp_unslash( $parsed_options[ $meta_name ][ $item_id ] ) );
				}
				// @codingStandardsIgnoreEnd

				if ( ! empty( $meta_datum['mass'] ) ) {
					$mass_meta[ $meta_datum['mass'] ][ $meta_name ] = $new_val;
					continue;
				}

				// Update new value.
				update_post_meta( $item_id, $meta_name, $new_val );
			}

			if ( ! empty( $mass_meta ) ) {
				foreach ( $mass_meta as $meta_index => $meta_options ) {
					$meta_opt_json = wp_json_encode( $meta_options );
					update_post_meta( $item_id, $meta_index, $meta_opt_json );
				}
			}


			$respond = esc_html__( 'Options Saved', 'groovy-menu' );


			// Send a JSON response back to an AJAX request, and die() ------------------------------------------------.
			wp_send_json_success( $respond );

		}
	}

}
