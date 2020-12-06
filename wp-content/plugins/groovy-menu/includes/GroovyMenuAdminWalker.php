<?php defined( 'ABSPATH' ) || die( 'This script cannot be accessed directly.' );

/**
 * Class GroovyMenuAdminWalker
 */
class GroovyMenuAdminWalker extends GroovyMenuWalkerNavMenu {

	protected static $grooniColsVariants = array(
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


	protected static $groovyMenuStyleClass;

	/**
	 * @param int|null $preset_id
	 */
	public static function setGroovyMenuStyleClass( $preset_id = null ) {
		self::$groovyMenuStyleClass = new GroovyMenuStyle( $preset_id );
	}

	/**
	 * @return GroovyMenuStyle
	 */
	public static function getGroovyMenuStyleClass() {
		if ( empty( self::$groovyMenuStyleClass ) ) {
			self::setGroovyMenuStyleClass();
		}

		return self::$groovyMenuStyleClass;
	}


	/**
	 * @return array
	 */
	static function megaMenuColsVariants() {
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
	static function gmBadgeTypeVariants() {
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
	static function gmBadgePlacementVariants() {
		$variants = array(
			'left'  => esc_html__( 'Left', 'groovy-menu' ),
			'right' => esc_html__( 'Right', 'groovy-menu' ),
		);

		return $variants;
	}

	/**
	 * @return array
	 */
	static function gmThumbPositionVariants() {
		$variants = array(
			'above' => esc_html__( 'Above menu text item', 'groovy-menu' ),
			'under' => esc_html__( 'Under menu text item', 'groovy-menu' ),
		);

		return $variants;
	}

	/**
	 * @return array
	 */
	static function gmBadgeGeneralPositionVariants() {
		$variants = array(
			'relative' => esc_html__( 'Relative', 'groovy-menu' ),
			'absolute' => esc_html__( 'Absolute', 'groovy-menu' ),
		);

		return $variants;
	}

	/**
	 * @return array
	 */
	static function megaMenuPosts() {
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

	public static function registerWalker() {

		$admin_walker_priority = 10;

		if ( self::getGroovyMenuStyleClass()->getGlobal( 'tools', 'admin_walker_priority' ) ) {
			$admin_walker_priority = 999999;
		}

		add_filter( 'wp_edit_nav_menu_walker', 'GroovyMenuAdminWalker::get_edit_walker', $admin_walker_priority, 2 );
		add_filter( 'wp_setup_nav_menu_item', 'GroovyMenuAdminWalker::setup_fields' );

		add_action( 'wp_update_nav_menu_item', 'GroovyMenuAdminWalker::update_fields', 10, 2 );
	}

	/**
	 * @param string $output
	 * @param int    $depth
	 * @param array  $args
	 */
	public function start_lvl( &$output, $depth = 0, $args = array() ) {
	}

	/**
	 * @param string $output
	 * @param int    $depth
	 * @param array  $args
	 */
	public function end_lvl( &$output, $depth = 0, $args = array() ) {
	}

	/**
	 * @return string
	 */
	public static function get_edit_walker() {
		return 'GroovyMenuAdminWalker';
	}

	/**
	 * @return array
	 */
	public static function getGroovyMenuWalkerPriority() {
		$priorities = array(
			'other' => false,
			'gm_id' => false,
		);

		global $wp_filter;

		if ( isset( $wp_filter['wp_edit_nav_menu_walker'] ) ) {
			if ( is_object( $wp_filter['wp_edit_nav_menu_walker'] ) && isset( $wp_filter['wp_edit_nav_menu_walker']->callbacks ) ) {
				foreach ( $wp_filter['wp_edit_nav_menu_walker']->callbacks as $priority => $callbacks ) {
					foreach ( $callbacks as $callback => $data ) {
						if ( 'GroovyMenuAdminWalker::get_edit_walker' === $callback ) {
							$priorities['other'] = false;
						} else {
							$priorities['gm_id'] = $priority;
							$priorities['other'] = true;
						}
					}
				}
			}
		}


		return $priorities;
	}

	/**
	 * Update post meta fields
	 *
	 * @param string     $menu_id menu id.
	 * @param string     $item_id item id.
	 * @param null|array $args    arguments.
	 */
	public static function update_fields( $menu_id, $item_id, $args = null ) {

		$priorities = self::getGroovyMenuWalkerPriority();
		if ( $priorities['other'] && $priorities['gm_id'] ) {
			return;
		}
		if ( isset( $_POST['wp_customize'] ) ) {
			return;
		}

		$meta_data = array(
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
			'gm-thumb-with-url' => array(
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

		$mass_meta = array();

		foreach ( $meta_data as $index => $meta_datum ) {
			// Get new value.
			$new_val = isset( $meta_datum['default'] ) ? $meta_datum['default'] : '';
			// @codingStandardsIgnoreStart
			if ( isset( $_REQUEST[ $index ][ $item_id ] ) ) {
				$new_val = trim( $_REQUEST[ $index ][ $item_id ] );
			}
			// @codingStandardsIgnoreEnd

			$meta_name = isset( $meta_datum['meta_name'] ) ? $meta_datum['meta_name'] : null;
			if ( empty( $meta_name ) ) {
				continue;
			}

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

	}

	/**
	 * Get params from meta
	 *
	 * @param object $menu_item menu item.
	 *
	 * @return mixed
	 */
	public static function setup_fields( $menu_item ) {
		$menu_item->is_megamenu            = get_post_meta( $menu_item->ID, self::IS_MEGAMENU_META, true );
		$menu_item->is_show_featured_image = get_post_meta( $menu_item->ID, self::IS_SHOW_FEATURED_IMAGE, true );

		return $menu_item;
	}

	/**
	 * Begin of element
	 *
	 * @param string  $output
	 * @param WP_Post $item
	 * @param int     $depth
	 * @param array   $args
	 * @param int     $id
	 */
	public function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {

		global $_wp_nav_menu_max_depth;
		$_wp_nav_menu_max_depth = $depth > $_wp_nav_menu_max_depth ? $depth : $_wp_nav_menu_max_depth;

		ob_start();
		$item_id      = strval( esc_attr( $item->ID ) );
		$removed_args = array(
			'action',
			'customlink-tab',
			'edit-menu-item',
			'menu-item',
			'page-tab',
			'_wpnonce',
		);

		$original_title = '';
		if ( 'taxonomy' === $item->type ) {
			$original_title = get_term_field( 'name', $item->object_id, $item->object, 'raw' );
			if ( is_wp_error( $original_title ) ) {
				$original_title = false;
			}
		} elseif ( 'post_type' === $item->type ) {
			$original_object = get_post( $item->object_id );
			$original_title  = get_the_title( $original_object->ID );
		}

		$classes = array(
			'menu-item menu-item-depth-' . $depth,
			'menu-item-' . esc_attr( $item->object ),
			'menu-item-edit-' . ( ( isset( $_GET['edit-menu-item'] ) && strval( $item_id ) === $_GET['edit-menu-item'] ) ? 'active' : 'inactive' ),
		);

		$title = $item->title;

		if ( ! empty( $item->_invalid ) ) {
			$classes[] = 'menu-item-invalid';
			/* translators: %s: title of menu item which is invalid */
			$title = sprintf( esc_html__( '%s (Invalid)', 'groovy-menu' ), $item->title );
		} elseif ( isset( $item->post_status ) && 'draft' === $item->post_status ) {
			$classes[] = 'pending';
			/* translators: %s: title of menu item in draft status */
			$title = sprintf( esc_html__( '%s (Pending)', 'groovy-menu' ), $item->title );
		}

		$title = ( ! isset( $item->label ) || '' === $item->label ) ? $title : $item->label;

		$submenu_text_escaped = '';
		if ( 0 === $depth ) {
			$submenu_text_escaped = 'style="display: none;"';
		}

		$item_classes = array();
		if ( isset( $item->classes ) && ! empty( $item->classes ) && is_array( $item->classes ) ) {
			foreach ( $item->classes as $one_class ) {
				$elem = maybe_unserialize( $one_class );
				if ( is_array( $elem ) ) {
					foreach ( $elem as $el ) {
						if ( ! empty( $el ) ) {
							$item_classes[] = $el;
						}
					}
				} else {
					$item_classes[] = $one_class;
				}
			}
		}

		$item_classes = implode( ' ', $item_classes );

		$itemTypeLabel = $item->type_label;
		if ( $this->isMegaMenu( $item ) ) {
			$itemTypeLabel .= ' [' . esc_html__( 'Mega Menu', 'groovy-menu' ) . ']';
		}

		$gm_menu_block = false;
		if ( isset( $item->object ) && 'gm_menu_block' === $item->object ) {
			$gm_menu_block = true;
		}

		if ( 1 === $depth && ! empty( $item->menu_item_parent ) ) {
			if ( $this->isMegaMenu( $item, true ) ) {
				$itemTypeLabel .= ' [' . esc_html__( 'Sub', 'groovy-menu' ) . ' ' . esc_html__( 'Mega Menu', 'groovy-menu' ) . ']';
			}
		}

		?>
	<li id="menu-item-<?php echo esc_attr( $item_id ); ?>" class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>">
		<dl class="menu-item-bar">
			<dt class="menu-item-handle">
				<span class="item-title"><span class="menu-item-title"><?php echo esc_html( $title ); ?></span> <span
						class="is-submenu" <?php echo $submenu_text_escaped; ?>><?php esc_html_e( 'sub item', 'groovy-menu' ); ?></span></span>
				<span class="item-controls">
						<span class="item-type"><?php echo esc_html( $itemTypeLabel ); ?></span>
						<span class="item-order hide-if-js">
							<a href="<?php
							echo wp_nonce_url(
								add_query_arg(
									array(
										'action'    => 'move-up-menu-item',
										'menu-item' => $item_id,
									),
									remove_query_arg( $removed_args, admin_url( 'nav-menus.php' ) )
								),
								'move-menu_item'
							);
							?>" class="item-move-up"><abbr title="<?php esc_attr_e( 'Move up', 'groovy-menu' ); ?>">&#8593;</abbr></a>
							|
							<a href="<?php
							echo wp_nonce_url(
								add_query_arg(
									array(
										'action'    => 'move-down-menu-item',
										'menu-item' => $item_id,
									),
									remove_query_arg( $removed_args, admin_url( 'nav-menus.php' ) )
								),
								'move-menu_item'
							);
							?>" class="item-move-down"><abbr title="<?php esc_attr_e( 'Move down', 'groovy-menu' ); ?>">
									&#8595;</abbr></a>
						</span>
						<a class="item-edit" id="edit-<?php echo esc_attr( $item_id ); ?>" href="<?php
						echo ( isset( $_GET['edit-menu-item'] ) && strval( $item_id ) === $_GET['edit-menu-item'] ) ? admin_url( 'nav-menus.php' ) : add_query_arg( 'edit-menu-item', $item_id, remove_query_arg( $removed_args, admin_url( 'nav-menus.php#menu-item-settings-' . $item_id ) ) );
						?>"><span class="screen-reader-text"><?php esc_html_e( 'Edit Menu Item', 'groovy-menu' ); ?></span></a>
					</span>
			</dt>
		</dl>

		<div class="menu-item-settings" id="menu-item-settings-<?php echo esc_attr( $item_id ); ?>">
			<?php if ( 'custom' === $item->type ) : ?>
				<p class="field-url description description-wide">
					<label for="edit-menu-item-url-<?php echo esc_attr( $item_id ); ?>">
						<?php esc_html_e( 'URL', 'groovy-menu' ); ?><br/>
						<input type="text" id="edit-menu-item-url-<?php echo esc_attr( $item_id ); ?>"
							class="widefat code edit-menu-item-url"
							name="menu-item-url[<?php echo esc_attr( $item_id ); ?>]"
							value="<?php echo esc_attr( $item->url ); ?>"/>
					</label>
				</p>
			<?php endif; ?>
			<p class="description description-thin">
				<label for="edit-menu-item-title-<?php echo esc_attr( $item_id ); ?>">
					<?php if ( $depth === 1 ) { ?>
						<?php esc_html_e( 'Navigation Label ("-" to hide)', 'groovy-menu' ); ?>
					<?php } else { ?>
						<?php esc_html_e( 'Navigation Label', 'groovy-menu' ); ?>
					<?php } ?>
					<br/>
					<input type="text" id="edit-menu-item-title-<?php echo esc_attr( $item_id ); ?>"
						class="widefat edit-menu-item-title" name="menu-item-title[<?php echo esc_attr( $item_id ); ?>]"
						value="<?php echo esc_attr( $item->title ); ?>"/>
				</label>
			</p>

			<p class="description description-thin">
				<label for="edit-menu-item-attr-title-<?php echo esc_attr( $item_id ); ?>">
					<?php esc_html_e( 'Title Attribute', 'groovy-menu' ); ?><br/>
					<input type="text" id="edit-menu-item-attr-title-<?php echo esc_attr( $item_id ); ?>"
						class="widefat edit-menu-item-attr-title"
						name="menu-item-attr-title[<?php echo esc_attr( $item_id ); ?>]"
						value="<?php echo esc_attr( $item->post_excerpt ); ?>"/>
				</label>
			</p>

			<?php if ( $gm_menu_block ) : ?>
				<p class="description description-wide groovymenu-block-url">
					<?php
					$value = $this->menuBlockURL( $item );
					if ( ! $value ) {
						$value = '';
					}
					?>
					<label for="groovymenu-block-url-<?php echo esc_attr( $item_id ); ?>">
						<?php esc_html_e( 'Menu block URL', 'groovy-menu' ); ?><br/>
						<input type="text" id="groovymenu-block-url-<?php echo esc_attr( $item_id ); ?>"
							class="widefat code groovymenu-block-url"
							name="groovymenu-block-url[<?php echo esc_attr( $item_id ); ?>]"
							value="<?php echo esc_attr( $value ); ?>"/>
					</label>
				</p>
			<?php endif; ?>

			<p class="field-link-target description">
				<label for="edit-menu-item-target-<?php echo esc_attr( $item_id ); ?>">
					<input type="checkbox" id="edit-menu-item-target-<?php echo esc_attr( $item_id ); ?>" value="_blank"
						name="menu-item-target[<?php echo esc_attr( $item_id ); ?>]"<?php checked( $item->target, '_blank' ); ?> />
					<?php esc_html_e( 'Open link in a new window/tab', 'groovy-menu' ); ?>
				</label>
			</p>

			<p class="field-css-classes description description-thin">
				<label for="edit-menu-item-classes-<?php echo esc_attr( $item_id ); ?>">
					<?php esc_html_e( 'CSS Classes (optional)', 'groovy-menu' ); ?><br/>
					<input type="text" id="edit-menu-item-classes-<?php echo esc_attr( $item_id ); ?>"
						class="widefat code edit-menu-item-classes"
						name="menu-item-classes[<?php echo esc_attr( $item_id ); ?>]"
						value="<?php echo esc_attr( $item_classes ); ?>"/>
				</label>
			</p>

			<p class="field-xfn description description-thin">
				<label for="edit-menu-item-xfn-<?php echo esc_attr( $item_id ); ?>">
					<?php esc_html_e( 'Link Relationship (XFN)', 'groovy-menu' ); ?><br/>
					<input type="text" id="edit-menu-item-xfn-<?php echo esc_attr( $item_id ); ?>"
						class="widefat code edit-menu-item-xfn"
						name="menu-item-xfn[<?php echo esc_attr( $item_id ); ?>]"
						value="<?php echo esc_attr( $item->xfn ); ?>"/>
				</label>
			</p>

			<p class="field-description description description-wide">
				<label for="edit-menu-item-description-<?php echo esc_attr( $item_id ); ?>">
					<?php esc_html_e( 'Description', 'groovy-menu' ); ?><br/>
					<textarea id="edit-menu-item-description-<?php echo esc_attr( $item_id ); ?>"
						class="widefat edit-menu-item-description" rows="3" cols="20"
						name="menu-item-description[<?php echo esc_attr( $item_id ); ?>]"><?php echo esc_html( $item->description ); // textarea_escaped ?></textarea>
					<span class="description"><?php esc_html_e( 'The description will be displayed in the menu if the current theme supports it.', 'groovy-menu' ); ?></span>
				</label>
			</p>

			<p class="description description-wide">
				<?php
				$value = $this->getIcon( $item );
				?>
				<label for="edit-menu-item-icon-class-<?php echo esc_attr( $item_id ); ?>">
					<?php esc_html_e( 'Icon', 'groovy-menu' ); ?>
					<br>
					<span class="gm-icon-preview">
							<span class="<?php echo esc_attr( $value ); ?>"></span>
						</span>
					<input
							type="text"
							value="<?php echo esc_attr( $value ); ?>"
							class="groovymenu-icon-class"
							id="groovymenu-icon-class-<?php echo esc_attr( $item_id ); ?>"
							name="groovymenu-icon-class[<?php echo esc_attr( $item_id ); ?>]"
					/>
					<button
							type="button"
							class="gm-select-icon">
						<?php esc_html_e( 'Select icon', 'groovy-menu' ); ?>
					</button>
				</label>
			</p>

			<!-- // Thumbnail settings -->

			<p class="description description-wide">
				<?php
				$value = '';
				if ( $this->getThumbEnable( $item ) ) {
					$value = 'checked=checked';
				}
				?>
				<label for="gm-thumb-enable-<?php echo esc_attr( $item_id ); ?>">
					<input
						type="checkbox"
						value="enabled"
						class="gm-thumb-enable"
						id="gm-thumb-enable-<?php echo esc_attr( $item_id ); ?>"
						name="gm-thumb-enable[<?php echo esc_attr( $item_id ); ?>]"
						<?php echo esc_attr( $value ); ?>
					/>
					<?php esc_html_e( 'Enable thumbnail', 'groovy-menu' ); ?>
				</label>
			</p>

			<p class="description description-wide gm-thumb-field">
				<?php
				$value = $this->getThumbPosition( $item ) ? : 'above';
				?>
				<label for="gm-thumb-position-<?php echo esc_attr( $item_id ); ?>">
					<?php esc_html_e( 'Thumbnail position.', 'groovy-menu' ); ?>
					<br/>
					<select class="gm-thumb-position"
						id="gm-thumb-position-<?php echo esc_attr( $item_id ); ?>"
						name="gm-thumb-position[<?php echo esc_attr( $item_id ); ?>]">
						<?php
						foreach ( self::gmThumbPositionVariants() as $position => $position_name ) {
							?>
							<option
								value="<?php echo esc_attr( $position ); ?>"<?php echo ( strval( $position ) === strval( $value ) ) ? ' selected' : '' ?>><?php echo esc_attr( $position_name ); ?></option>
							<?php
						}
						?>
					</select>
				</label>
			</p>

			<p class="description description-wide gm-thumb-field">
				<?php
				$value = $this->getThumbMaxHeight( $item ) ? : '128';
				?>
				<label for="gm-thumb-max-height-<?php echo esc_attr( $item_id ); ?>">
					<?php esc_html_e( 'Thumbnail image maximum height.', 'groovy-menu' ); ?><br/>
					<input
						type="number"
						min="0" max="3200"
						value="<?php echo esc_attr( $value ); ?>"
						class="gm-thumb-max-height"
						id="gm-thumb-max-height-<?php echo esc_attr( $item_id ); ?>"
						name="gm-thumb-max-height[<?php echo esc_attr( $item_id ); ?>]"
					/> px
				</label>
			</p>

			<p class="description description-wide gm-thumb-field">
				<?php
				$value = '';
				if ( $this->getThumbWithUrl( $item ) ) {
					$value = 'checked=checked';
				}
				?>
				<label for="gm-thumb-with-url-<?php echo esc_attr( $item_id ); ?>">
					<input
						type="checkbox"
						value="enabled"
						class="gm-thumb-with-url"
						id="gm-thumb-with-url-<?php echo esc_attr( $item_id ); ?>"
						name="gm-thumb-with-url[<?php echo esc_attr( $item_id ); ?>]"
						<?php echo esc_attr( $value ); ?>
					/>
					<?php esc_html_e( 'Wrap thumbnail with menu item URL', 'groovy-menu' ); ?>
				</label>
			</p>

			<p class="description description-wide gm-thumb-field gm-thumb--image">
				<?php $value = $this->getThumbImage( $item ); ?>
				<?php esc_html_e( 'Thumbnail Image', 'groovy-menu' ); ?>
				<br>
				<input
					type="hidden"
					value="<?php echo esc_attr( $value ); ?>"
					class="gm-thumb-image"
					id="gm-thumb-image-<?php echo esc_attr( $item_id ); ?>"
					name="gm-thumb-image[<?php echo esc_attr( $item_id ); ?>]">
				<button
					type="button"
					class="button button-primary gm-select-thumb-image"
					data-item_id="<?php echo esc_attr( $item_id ); ?>"
					data-uploader_title="<?php esc_html_e( 'Select thumb Image', 'groovy-menu' ); ?>"
					data-uploader_button_text="<?php esc_html_e( 'Insert image', 'groovy-menu' ); ?>"
				>
					<?php esc_html_e( 'Select image', 'groovy-menu' ); ?>
				</button>
				<button type="button"
					class="button gm-remove-thumb-img"
				>
					<?php esc_html_e( 'Remove image', 'groovy-menu' ); ?>
				</button>
				<span class="gm-thumb-image-preview" id="gm-thumb-image-preview-<?php echo esc_attr( $item_id ); ?>">
					<?php if ( ! empty( $value ) ) : ?>
						<img src="<?php echo esc_attr( $value ); ?>" alt="">
					<?php endif; ?>
				</span>
			</p>

			<!-- // Badge settings -->

			<p class="description description-wide">
				<?php
				$value = '';
				if ( $this->getBadgeEnable( $item ) ) {
					$value = 'checked=checked';
				}
				?>
				<label for="gm-badge-enable-<?php echo esc_attr( $item_id ); ?>">
					<input
						type="checkbox"
						value="enabled"
						class="gm-badge-enable"
						id="gm-badge-enable-<?php echo esc_attr( $item_id ); ?>"
						name="gm-badge-enable[<?php echo esc_attr( $item_id ); ?>]"
						<?php echo esc_attr( $value ); ?>
					/>
					<?php esc_html_e( 'Enable badge', 'groovy-menu' ); ?>
				</label>
			</p>

			<p class="description description-wide gm-badge-field gm-badge-field--shared">
				<?php
				$value = $this->getBadgePlacement( $item ) ? : 'left';
				?>
				<label for="gm-badge-placement-<?php echo esc_attr( $item_id ); ?>">
					<?php esc_html_e( 'Badge placement', 'groovy-menu' ); ?><br/>
					<select class="gm-badge-placement"
					        id="gm-badge-placement-<?php echo esc_attr( $item_id ); ?>"
					        name="gm-badge-placement[<?php echo esc_attr( $item_id ); ?>]">
						<?php
						foreach ( self::gmBadgePlacementVariants() as $variant => $variant_name ) {
							?>
							<option value="<?php echo esc_attr( $variant ); ?>"<?php echo ( strval( $variant ) === strval( $value ) ) ? ' selected' : '' ?>><?php echo esc_attr( $variant_name ); ?></option>
							<?php
						}
						?>
					</select>
				</label>
			</p>

			<p class="description description-wide gm-badge-field gm-badge-field--shared">
				<?php
				$value = $this->getBadgeGeneralPosition( $item ) ? : 'relative';
				?>
				<label for="gm-badge-general-position-<?php echo esc_attr( $item_id ); ?>">
					<span class="gm-fat-label"><?php esc_html_e( 'Badge position.', 'groovy-menu' ); ?></span> <?php esc_html_e( 'Absolutely positioned Badge take no space in the page layout. Relatively positioned Badge acts as normal element taking space.', 'groovy-menu' ); ?><br/>
					<select class="gm-badge-general-position"
						id="gm-badge-general-position-<?php echo esc_attr( $item_id ); ?>"
						name="gm-badge-general-position[<?php echo esc_attr( $item_id ); ?>]">
						<?php
						foreach ( self::gmBadgeGeneralPositionVariants() as $position => $position_name ) {
							?>
							<option value="<?php echo esc_attr( $position ); ?>"<?php echo ( strval( $position ) === strval( $value ) ) ? ' selected' : '' ?>><?php echo esc_attr( $position_name ); ?></option>
							<?php
						}
						?>
					</select>
				</label>
			</p>

			<p class="description description-wide gm-badge-field gm-badge-field--shared">
				<?php
				$value = $this->getBadgeXPosition( $item ) ? : '';
				?>
				<label for="gm-badge-x-position-<?php echo esc_attr( $item_id ); ?>">
					<span class="gm-fat-label"><?php esc_html_e( 'Badge X offset.', 'groovy-menu' ); ?></span> <?php esc_html_e( 'Negative value will push badge left, positive right.', 'groovy-menu' ); ?> <?php esc_html_e( 'Any valid CSS units accepted e.q. % or px.', 'groovy-menu' ); ?><br/>
					<input
							type="text"
							value="<?php echo esc_attr( $value ); ?>"
							class="gm-badge-x-position"
							id="gm-badge-x-position-<?php echo esc_attr( $item_id ); ?>"
							name="gm-badge-x-position[<?php echo esc_attr( $item_id ); ?>]"
					/>
				</label>
			</p>

			<p class="description description-wide gm-badge-field gm-badge-field--shared">
				<?php
				$value = $this->getBadgeYPosition( $item ) ? : '';
				?>
				<label for="gm-badge-y-position-<?php echo esc_attr( $item_id ); ?>">
					<span class="gm-fat-label"><?php esc_html_e( 'Badge Y offset.', 'groovy-menu' ); ?></span> <?php esc_html_e( 'Negative value will push badge up, positive down.', 'groovy-menu' ); ?> <?php esc_html_e( 'Any valid CSS units accepted e.q. % or px.', 'groovy-menu' ); ?><br/>
					<input
						type="text"
						value="<?php echo esc_attr( $value ); ?>"
						class="gm-badge-y-position"
						id="gm-badge-y-position-<?php echo esc_attr( $item_id ); ?>"
						name="gm-badge-y-position[<?php echo esc_attr( $item_id ); ?>]"
					/>
				</label>
			</p>

			<p class="description description-wide gm-badge-field gm-badge-field--shared">
				<?php
				$value = $this->getBadgeContainerPadding( $item ) ? : '';
				?>
				<label for="gm-badge-container-padding-<?php echo esc_attr( $item_id ); ?>">
					<span class="gm-fat-label"><?php esc_html_e( 'Badge container padding.', 'groovy-menu' ); ?></span> <?php esc_html_e( 'One, two, three or four values accepted.', 'groovy-menu' ); ?> <?php esc_html_e( 'Any valid CSS units accepted e.q. rem or px.', 'groovy-menu' ); ?><br/>
					<input
							type="text"
							value="<?php echo esc_attr( $value ); ?>"
							class="gm-badge-container-padding"
							id="gm-badge-container-padding-<?php echo esc_attr( $item_id ); ?>"
							name="gm-badge-container-padding[<?php echo esc_attr( $item_id ); ?>]"
					/>
				</label>
			</p>

			<p class="description description-wide gm-badge-field gm-badge-field--shared">
				<?php
				$value = $this->getBadgeContainerRadius( $item ) ? : '';
				?>
				<label for="gm-badge-container-radius-<?php echo esc_attr( $item_id ); ?>">
					<span class="gm-fat-label"><?php esc_html_e( 'Badge container border radius.', 'groovy-menu' ); ?></span> <?php esc_html_e( 'One, two, three or four values accepted.', 'groovy-menu' ); ?> <?php esc_html_e( 'Any valid CSS units accepted e.q. % or px.', 'groovy-menu' ); ?><br/>
					<input
							type="text"
							value="<?php echo esc_attr( $value ); ?>"
							class="gm-badge-container-radius"
							id="gm-badge-container-radius-<?php echo esc_attr( $item_id ); ?>"
							name="gm-badge-container-radius[<?php echo esc_attr( $item_id ); ?>]"
					/>
				</label>
			</p>

			<p class="description description-wide gm-badge-field gm-badge-field--shared">
				<?php
				$value = $this->getBadgeContainerBg( $item ) ? : '';
				?>
				<?php esc_html_e( 'Badge container background color', 'groovy-menu' ); ?><br/>
				<input
						type="text"
						data-alpha="true"
						value="<?php echo esc_attr( $value ); ?>"
						class="gm-badge-container-bg gm-appearance-colorpicker"
						id="gm-badge-container-bg-<?php echo esc_attr( $item_id ); ?>"
						name="gm-badge-container-bg[<?php echo esc_attr( $item_id ); ?>]"
				/>
			</p>

			<p class="description description-wide gm-badge-field gm-badge-field--shared">
				<?php
				$value = $this->getBadgeType( $item ) ? : 'icon';
				?>
				<label for="gm-badge-type-<?php echo esc_attr( $item_id ); ?>">
					<?php esc_html_e( 'Badge content type', 'groovy-menu' ); ?><br/>
					<select class="gm-badge-type"
						id="gm-badge-type-<?php echo esc_attr( $item_id ); ?>"
						name="gm-badge-type[<?php echo esc_attr( $item_id ); ?>]">
						<?php
						foreach ( self::gmBadgeTypeVariants() as $variant => $variant_name ) {
							?>
							<option
								value="<?php echo esc_attr( $variant ); ?>"<?php echo ( strval( $variant ) === strval( $value ) ) ? ' selected' : '' ?>><?php echo esc_attr( $variant_name ); ?></option>
							<?php
						}
						?>
					</select>
				</label>
			</p>

			<p class="description description-wide gm-badge-field gm-badge-type--image">
				<?php $value = $this->getBadgeImage( $item ); ?>
				<?php esc_html_e( 'Badge Image', 'groovy-menu' ); ?>
				<br>
				<input
					type="hidden"
					value="<?php echo esc_attr( $value ); ?>"
					class="gm-badge-image"
					id="gm-badge-image-<?php echo esc_attr( $item_id ); ?>"
					name="gm-badge-image[<?php echo esc_attr( $item_id ); ?>]">
				<input
					type="hidden"
					value="<?php echo esc_attr( $this->getBadgeImageWidth( $item ) ) ?>"
					id="gm-badge-image-width-<?php echo esc_attr( $item_id ); ?>"
					name="gm-badge-image-width[<?php echo esc_attr( $item_id ); ?>]">
				<input type="hidden" value="<?php echo esc_attr( $this->getBadgeImageHeight( $item ) ) ?>"
					id="gm-badge-image-height-<?php echo esc_attr( $item_id ); ?>"
					name="gm-badge-image-height[<?php echo esc_attr( $item_id ); ?>]">
				<button
					type="button"
					class="button button-primary gm-select-badge-image"
					data-item_id="<?php echo esc_attr( $item_id ); ?>"
					data-uploader_title="<?php esc_html_e( 'Select Badge Image', 'groovy-menu' ); ?>"
					data-uploader_button_text="<?php esc_html_e( 'Insert image', 'groovy-menu' ); ?>"
				>
					<?php esc_html_e( 'Select image', 'groovy-menu' ); ?>
				</button>
				<button type="button"
				        class="button gm-remove-img"
				>
					<?php esc_html_e( 'Remove image', 'groovy-menu' ); ?>
				</button>
				<span class="gm-badge-image-preview" id="gm-badge-image-preview-<?php echo esc_attr( $item_id ); ?>">
					<?php if (!empty($value)) : ?>
						<img src="<?php echo esc_attr( $value ); ?>" alt="">
					<?php endif; ?>
				</span>
			</p>

			<p class="description description-wide gm-badge-field gm-badge-type--icon">
				<?php
				$value = $this->getBadgeIcon( $item );
				?>
				<label for="gm-badge-icon-<?php echo esc_attr( $item_id ); ?>">
					<?php esc_html_e( 'Badge Icon', 'groovy-menu' ); ?>
					<br>
					<span class="gm-icon-preview"
						id="gm-badge-icon-preview-<?php echo esc_attr( $item_id ); ?>">
							<span class="<?php echo esc_attr( $value ); ?>"></span>
						</span>
					<input
						type="text"
						value="<?php echo esc_attr( $value ); ?>"
						class="gm-badge-icon"
						id="gm-badge-icon-<?php echo esc_attr( $item_id ); ?>"
						name="gm-badge-icon[<?php echo esc_attr( $item_id ); ?>]"
					/>
					<button
						type="button"
						class="gm-select-badge-icon"
						data-item_id="<?php echo esc_attr( $item_id ); ?>">
						<?php esc_html_e( 'Select icon', 'groovy-menu' ); ?>
					</button>
				</label>
			</p>

			<p class="description description-wide gm-badge-field gm-badge-type--icon">
				<?php
				$value = $this->getBadgeIconSize( $item ) ? : '';
				?>
				<label for="gm-badge-icon-size-<?php echo esc_attr( $item_id ); ?>">
					<?php esc_html_e( 'Badge icon size', 'groovy-menu' ); ?><br/>
					<input
						type="number"
						value="<?php echo esc_attr( $value ); ?>"
						class="gm-badge-icon-size"
						id="gm-badge-icon-size-<?php echo esc_attr( $item_id ); ?>"
						name="gm-badge-icon-size[<?php echo esc_attr( $item_id ); ?>]"
					/> px
				</label>
			</p>

			<p class="description description-wide gm-badge-field gm-badge-type--icon">
				<?php
				$value = $this->getBadgeIconColor( $item ) ? : '';
				?>
				<?php esc_html_e( 'Badge icon color', 'groovy-menu' ); ?><br/>
				<input
					type="text"
					data-alpha="true"
					value="<?php echo esc_attr( $value ); ?>"
					class="gm-badge-icon-color gm-appearance-colorpicker"
					id="gm-badge-icon-color-<?php echo esc_attr( $item_id ); ?>"
					name="gm-badge-icon-color[<?php echo esc_attr( $item_id ); ?>]"
				/>
			</p>

			<p class="description description-wide gm-badge-field gm-badge-type--text">
				<?php
				$value = $this->getBadgeText( $item ) ? : '';
				?>
				<label for="gm-badge-icon-text-<?php echo esc_attr( $item_id ); ?>">
					<?php esc_html_e( 'Badge text', 'groovy-menu' ); ?><br/>
					<input
						type="text"
						value="<?php echo esc_attr( $value ); ?>"
						class="gm-badge-text"
						id="gm-badge-text-<?php echo esc_attr( $item_id ); ?>"
						name="gm-badge-text[<?php echo esc_attr( $item_id ); ?>]"
					/>
				</label>
			</p>

			<p class="description description-wide gm-badge-field gm-badge-type--text">
				<?php
				$value = $this->getBadgeTextFontFamily( $item ) ? : 'inherit';
				?>
				<label for="gm-badge-text-font-family-<?php echo esc_attr( $item_id ); ?>">
					<?php esc_html_e( 'Badge text font family', 'groovy-menu' ); ?><br/>
					<select class="gm-badge-text-font-family"
						id="gm-badge-text-font-family-<?php echo esc_attr( $item_id ); ?>"
						name="gm-badge-text-font-family[<?php echo esc_attr( $item_id ); ?>]"
						data-item_id="<?php echo esc_attr( $item_id ); ?>">
						<?php
						foreach ( GroovyMenuAdminWalker::gmGetFontArrayForSelect() as $family => $variants ) {
							$name = $family === '' ? esc_html__( 'Inherit from parent', 'groovy-menu' ) : $family;
							?>
							<option value="<?php echo esc_attr( $family ); ?>"<?php echo ( strval( $family ) === strval( $value ) ) ? ' selected' : '' ?> data-variants="<?php echo esc_attr( $variants ); ?>"><?php echo esc_attr( $name ); ?></option>
							<?php
						}
						?>
					</select>
				</label>
			</p>

			<p class="description description-wide gm-badge-field gm-badge-type--text">
				<?php
				$value = $this->getBadgeTextFontVariant( $item ) ? : 'inherit';
				?>
				<label for="gm-badge-text-font-variant-<?php echo esc_attr( $item_id ); ?>">
					<?php esc_html_e( 'Badge text font variant', 'groovy-menu' ); ?><br/>
					<select class="gm-badge-text-font-variant"
						id="gm-badge-text-font-variant-<?php echo esc_attr( $item_id ); ?>"
						name="gm-badge-text-font-variant[<?php echo esc_attr( $item_id ); ?>]"
						data-saved_value="<?php echo esc_attr( $value ); ?>">
						<option value=""<?php echo ( '' === strval( $value ) ) ? ' selected' : '' ?>><?php esc_html_e( 'Inherit from parent', 'groovy-menu' ); ?></option>
					</select>
				</label>
			</p>

			<p class="description description-wide gm-badge-field gm-badge-type--text">
				<?php
				$value = $this->getBadgeTextFontSize( $item ) ? : '';
				?>
				<label for="gm-badge-text-font-size-<?php echo esc_attr( $item_id ); ?>">
					<?php esc_html_e( 'Badge text font size', 'groovy-menu' ); ?><br/>
					<input
						type="number"
						value="<?php echo esc_attr( $value ); ?>"
						class="gm-badge-text-font-size"
						id="gm-badge-text-font-size-<?php echo esc_attr( $item_id ); ?>"
						name="gm-badge-text-font-size[<?php echo esc_attr( $item_id ); ?>]"
					/> px
				</label>
			</p>

			<p class="description description-wide gm-badge-field gm-badge-type--text">
				<?php
				$value = $this->getBadgeTextFontColor( $item ) ? : '';
				?>
				<?php esc_html_e( 'Badge text font color', 'groovy-menu' ); ?><br/>
				<input
					type="text"
					data-alpha="true"
					value="<?php echo esc_attr( $value ); ?>"
					class="gm-badge-text-font-color gm-appearance-colorpicker"
					id="gm-badge-text-font-color-<?php echo esc_attr( $item_id ); ?>"
					name="gm-badge-text-font-color[<?php echo esc_attr( $item_id ); ?>]"
				/>
			</p>

			<p class="description description-wide">
				<?php
				$value = '';
				if ( $this->doNotShowTitle( $item ) ) {
					$value = "checked='checked'";
				}
				?>
				<label for="edit-menu-item-do-not-show-title-<?php echo esc_attr( $item_id ); ?>">
					<input
						type="checkbox"
						value="enabled"
						class="groovymenu-do-not-show-title"
						id="groovymenu-do-not-show-title-<?php echo esc_attr( $item_id ); ?>"
						name="groovymenu-do-not-show-title[<?php echo esc_attr( $item_id ); ?>]"
						<?php echo esc_attr( $value ); ?>
					/>
					<?php esc_html_e( 'Do not show menu item title and link', 'groovy-menu' ); ?>
				</label>
			</p>

			<?php if ( $gm_menu_block ) : ?>
			<p class="description description-wide">
				<?php
				$value = '';
				if ( $this->megaMenuPostNotMobile( $item ) ) {
					$value = "checked='checked'";
				}
				?>
				<label for="edit-menu-item-megamenu-post-<?php echo esc_attr( $item_id ); ?>">
					<input
						type="checkbox"
						value="enabled"
						class="groovymenu-megamenu-post-not-mobile"
						id="groovymenu-megamenu-post-not-mobile-<?php echo esc_attr( $item_id ); ?>"
						name="groovymenu-megamenu-post-not-mobile[<?php echo esc_attr( $item_id ); ?>]"
						<?php echo esc_attr( $value ); ?>
					/>
					<?php esc_html_e( 'Do not show Menu block content on mobile', 'groovy-menu' ); ?>
				</label>
			</p>
			<?php endif; ?>

			<?php if ( $depth === 0 ) { ?>
				<p class="description description-wide">
					<?php
					$value = '';
					if ( $this->isMegaMenu( $item ) ) {
						$value = "checked='checked'";
					}
					?>
					<label for="edit-menu-item-megamenu-<?php echo esc_attr( $item_id ); ?>">
						<input
							type="checkbox"
							value="enabled"
							class="groovymenu-megamenu"
							id="groovymenu-megamenu-<?php echo esc_attr( $item_id ); ?>"
							name="groovymenu-megamenu[<?php echo esc_attr( $item_id ); ?>]"
							<?php echo esc_attr( $value ); ?>
						/>
						<?php esc_html_e( 'Mega menu', 'groovy-menu' ); ?>
					</label>
				</p>
				<p class="description description-wide megamenu-cols megamenu-options-depend">
					<?php
					$value = $this->megaMenuCols( $item );
					if ( ! $value ) {
						$value = '5';
					}
					?>
					<label for="edit-menu-item-megamenu-cols-<?php echo esc_attr( $item_id ); ?>">
						<?php esc_html_e( 'Mega menu columns', 'groovy-menu' ); ?><br/>
						<select class="groovymenu-megamenu-cols"
							id="groovymenu-megamenu-cols-<?php echo esc_attr( $item_id ); ?>"
							name="groovymenu-megamenu-cols[<?php echo esc_attr( $item_id ); ?>]">
							<?php
							foreach ( GroovyMenuAdminWalker::megaMenuColsVariants() as $cols => $cols_name ) {
								?>
								<option value="<?php echo esc_attr( $cols ); ?>"<?php echo ( strval( $cols ) === strval( $value ) ) ? ' selected' : '' ?>><?php echo esc_attr( $cols_name ); ?></option>
								<?php
							}
							?>
						</select>

					</label>
				</p>
				<div class="groovymenu-megamenu-bg">
					<div>
						<input type="hidden" class="groovymenu-megamenu-bg-input"
							data-url="<?php echo esc_attr( $this->getBackgroundUrl( $item ) ); ?>"
							data-thumbnail="<?php echo esc_attr( $this->getBackgroundUrlThumbnail( $item ) ); ?>"
							value="<?php echo esc_attr( $this->getBackgroundId( $item ) ); ?>"
							name="groovymenu-megamenu-bg[<?php echo esc_attr( $item_id ); ?>]">
						<button type="button"
							class="button button-primary groovymenu-megamenu-bg-select"><?php esc_html_e( 'Set background image', 'groovy-menu' ); ?>
						</button>
						<button type="button"
							class="button groovymenu-megamenu-bg-remove"><?php esc_html_e( 'Remove background image', 'groovy-menu' ); ?></button>
						<div class="groovymenu-megamenu-bg-preview"></div>

					</div>
					<div>
						<p class="description description-thin">
							<label>
								<?php esc_html_e( 'Background position', 'groovy-menu' ); ?><br/>
								<select class="widefat"
									name="groovymenu-megamenu-bg-position[<?php echo esc_attr( $item_id ); ?>]">
									<?php foreach ( self::$backgroundPositions as $position ) { ?>
										<option value="<?php echo esc_attr( $position ); ?>" <?php echo( $position === $this->getBackgroundPosition( $item ) ? 'selected' : '' ) ?>><?php echo esc_attr( $position ); ?></option>
									<?php } ?>
								</select>
							</label>
						</p>
						<p class="description description-thin">
							<label>
								<?php esc_html_e( 'Background repeat', 'groovy-menu' ); ?><br/>
								<select class="widefat"
									name="groovymenu-megamenu-bg-repeat[<?php echo esc_attr( $item_id ); ?>]">
									<?php foreach ( self::$backgroundRepeats as $repeat ) { ?>
										<option
											value="<?php echo esc_attr( $repeat ); ?>" <?php echo( $repeat === $this->getBackgroundRepeat( $item ) ? 'selected' : '' ) ?>><?php echo esc_attr( $repeat ); ?></option>
									<?php } ?>
								</select>
							</label>
						</p>
						<p class="description description-thin">
							<label>
								<?php esc_html_e( 'Background image size', 'groovy-menu' ); ?><br/>
								<select class="widefat"
									name="groovymenu-megamenu-bg-size[<?php echo esc_attr( $item_id ); ?>]">
									<?php foreach ( GroovyMenuUtils::get_all_image_sizes() as $size => $size_data ) { ?>
										<option
											value="<?php echo esc_attr( $size ); ?>" <?php echo( $size === $this->getBackgroundSize( $item ) ? 'selected' : '' ) ?>><?php echo esc_attr( $size ); ?></option>
									<?php } ?>
								</select>
							</label>
						</p>
					</div>
				</div>
			<?php } ?>

			<?php if ( 'post_type' === $item->type && ! $gm_menu_block ) : ?>
				<p class="gm-show-featured-image-wrapper description-wide">
					<?php
					$value = '';
					if ( $this->isShowFeaturedImage( $item ) ) {
						$value = "checked='checked'";
					}
					?>
					<label for="edit-menu-item-show-featured-image-<?php echo esc_attr( $item_id ); ?>">
						<input type="checkbox" value="enabled" class="groovymenu-show-featured-image"
							id="groovymenu-is-show-featured-<?php echo esc_attr( $item_id ); ?>"
							name="groovymenu-is-show-featured[<?php echo esc_attr( $item_id ); ?>]"
							<?php echo esc_attr( $value ); ?>
						/>
						<?php esc_html_e( 'Show featured image on hover', 'groovy-menu' ); ?>
					</label>
				</p>
			<?php endif; ?>

			<p class="field-move hide-if-no-js description description-wide">
				<label>
					<span><?php esc_html_e( 'Move', 'groovy-menu' ); ?></span>
					<a href="#" class="menus-move menus-move-up"
						data-dir="up"><?php esc_html_e( 'Up one', 'groovy-menu' ); ?></a>
					<a href="#" class="menus-move menus-move-down"
						data-dir="down"><?php esc_html_e( 'Down one', 'groovy-menu' ); ?></a>
					<a href="#" class="menus-move menus-move-left" data-dir="left"></a>
					<a href="#" class="menus-move menus-move-right" data-dir="right"></a>
					<a href="#" class="menus-move menus-move-top"
						data-dir="top"><?php esc_html_e( 'To the top', 'groovy-menu' ); ?></a>
				</label>
			</p>

			<div class="menu-item-actions description-wide submitbox">
				<?php if ( 'custom' != $item->type && $original_title !== false ) : ?>
					<p class="link-to-original">
						<?php printf( esc_html__( 'Original: %s', 'groovy-menu' ), '<a href="' . esc_attr( $item->url ) . '">' . esc_html( $original_title ) . '</a>' ); ?>
					</p>
				<?php endif; ?>
				<a class="item-delete submitdelete deletion" id="delete-<?php echo esc_attr( $item_id ); ?>" href="<?php
				echo wp_nonce_url(
					add_query_arg(
						array(
							'action'    => 'delete-menu-item',
							'menu-item' => $item_id,
						),
						admin_url( 'nav-menus.php' )
					),
					'delete-menu_item_' . $item_id
				); ?>"><?php esc_html_e( 'Remove', 'groovy-menu' ); ?></a> <span
					class="meta-sep hide-if-no-js"> | </span> <a
					class="item-cancel submitcancel hide-if-no-js" id="cancel-<?php echo esc_attr( $item_id ); ?>"
					href="<?php echo esc_url( add_query_arg( array(
						'edit-menu-item' => $item_id,
						'cancel'         => time()
					), admin_url( 'nav-menus.php' ) ) );
					?>#menu-item-settings-<?php echo esc_attr( $item_id ); ?>"><?php esc_html_e( 'Cancel', 'groovy-menu' ); ?></a>
			</div>

			<input class="menu-item-data-db-id" type="hidden"
				name="menu-item-db-id[<?php echo esc_attr( $item_id ); ?>]"
				value="<?php echo esc_attr( $item_id ); ?>"/>
			<input class="menu-item-data-object-id" type="hidden"
				name="menu-item-object-id[<?php echo esc_attr( $item_id ); ?>]"
				value="<?php echo esc_attr( $item->object_id ); ?>"/>
			<input class="menu-item-data-object" type="hidden"
				name="menu-item-object[<?php echo esc_attr( $item_id ); ?>]"
				value="<?php echo esc_attr( $item->object ); ?>"/>
			<input class="menu-item-data-parent-id" type="hidden"
				name="menu-item-parent-id[<?php echo esc_attr( $item_id ); ?>]"
				value="<?php echo esc_attr( $item->menu_item_parent ); ?>"/>
			<input class="menu-item-data-position" type="hidden"
				name="menu-item-position[<?php echo esc_attr( $item_id ); ?>]"
				value="<?php echo esc_attr( $item->menu_order ); ?>"/>
			<input class="menu-item-data-type" type="hidden" name="menu-item-type[<?php echo esc_attr( $item_id ); ?>]"
				value="<?php echo esc_attr( $item->type ); ?>"/>
		</div>
		<!-- .menu-item-settings-->
		<ul class="menu-item-transport"></ul>
		<?php
		$output .= ob_get_clean();
	}

}
