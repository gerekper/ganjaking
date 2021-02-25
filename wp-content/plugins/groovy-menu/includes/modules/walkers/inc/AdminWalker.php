<?php

namespace GroovyMenu;

use \GroovyMenu\WalkerNavMenu as WalkerNavMenu;
use \GroovyMenuStyle as GroovyMenuStyle;
use \GroovyMenuUtils as GroovyMenuUtils;


defined( 'ABSPATH' ) || die( 'This script cannot be accessed directly.' );


/**
 * Class AdminWalker
 */
class AdminWalker extends WalkerNavMenu {

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

	public static function registerWalker() {

		$admin_walker_priority = 10;

		if ( self::getGroovyMenuStyleClass()->getGlobal( 'tools', 'admin_walker_priority' ) ) {
			$admin_walker_priority = PHP_INT_MAX;
		}

		add_filter( 'wp_edit_nav_menu_walker', '\GroovyMenu\AdminWalker::get_edit_walker', $admin_walker_priority, 2 );
		add_filter( 'wp_setup_nav_menu_item', '\GroovyMenu\AdminWalker::setup_fields' );

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
		return '\GroovyMenu\AdminWalker';
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
						if ( self::get_edit_walker() . '::get_edit_walker' === $callback ) {
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
	 * @param \WP_Post $item
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
			$original_object = get_term( (int) $item->object_id, $item->object );
			if ( $original_object && ! is_wp_error( $original_title ) ) {
				$original_title = $original_object->name;
			}
		} elseif ( 'post_type' === $item->type ) {
			$original_object = get_post( $item->object_id );
			if ( $original_object ) {
				$original_title = get_the_title( $original_object->ID );
			}
		} elseif ( 'post_type_archive' === $item->type ) {
			$original_object = get_post_type_object( $item->object );
			if ( $original_object ) {
				$original_title = $original_object->labels->archives;
			}
		}

		$classes = array(
			'menu-item menu-item-depth-' . $depth,
			'menu-item-' . esc_attr( $item->object ),
			'menu-item-edit-' . ( ( isset( $_GET['edit-menu-item'] ) && strval( $item_id ) === $_GET['edit-menu-item'] ) ? 'active' : 'inactive' ), // @codingStandardsIgnoreLine
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

		$lver = false;
		if ( defined( 'GROOVY_MENU_LVER' ) && '2' === GROOVY_MENU_LVER ) {
			$lver = true;
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

		if ( $this->frozenLink( $item ) ) {
			$itemTypeLabel .= ' [' . esc_html__( 'Frozen', 'groovy-menu' ) . ']';
		}

		?>
	<li id="menu-item-<?php echo $item_id; ?>" class="<?php echo implode( ' ', $classes ); ?>">
		<div class="menu-item-bar">
			<div class="menu-item-handle">
				<span class="item-title"><span class="menu-item-title"><?php echo esc_html( $title ); ?></span> <span
						class="is-submenu" <?php echo $submenu_text_escaped; ?>><?php _e( 'sub item', 'groovy-menu' ); ?></span></span>
				<span class="item-controls">
						<span class="item-type"><?php echo esc_html( $itemTypeLabel ); ?></span>
						<span class="item-order hide-if-js">
							<?php
							printf(
								'<a href="%s" class="item-move-up" aria-label="%s">&#8593;</a>',
								wp_nonce_url(
									add_query_arg(
										array(
											'action'    => 'move-up-menu-item',
											'menu-item' => $item_id,
										),
										remove_query_arg( $removed_args, admin_url( 'nav-menus.php' ) )
									),
									'move-menu_item'
								),
								esc_attr__( 'Move up', 'groovy-menu' )
							);
							?>
							|
							<?php
							printf(
								'<a href="%s" class="item-move-down" aria-label="%s">&#8595;</a>',
								wp_nonce_url(
									add_query_arg(
										array(
											'action'    => 'move-down-menu-item',
											'menu-item' => $item_id,
										),
										remove_query_arg( $removed_args, admin_url( 'nav-menus.php' ) )
									),
									'move-menu_item'
								),
								esc_attr__( 'Move down', 'groovy-menu' )
							);
							?>
						</span>
					<?php
					if ( isset( $_GET['edit-menu-item'] ) && strval( $item_id ) === $_GET['edit-menu-item'] ) {
						$edit_url = admin_url( 'nav-menus.php' );
					} else {
						$edit_url = add_query_arg(
							array(
								'edit-menu-item' => $item_id,
							),
							remove_query_arg( $removed_args, admin_url( 'nav-menus.php#menu-item-settings-' . $item_id ) )
						);
					}

					printf(
						'<a class="item-edit" id="edit-%s" href="%s" aria-label="%s"><span class="screen-reader-text">%s</span></a>',
						$item_id,
						$edit_url,
						esc_attr__( 'Edit menu item', 'groovy-menu' ),
						__( 'Edit', 'groovy-menu' )
					);
					?>
					</span>
			</div>
		</div>

		<div class="menu-item-settings wp-clearfix" id="menu-item-settings-<?php echo $item_id; ?>">
			<?php if ( 'custom' === $item->type ) : ?>
				<p class="field-url description description-wide">
					<label for="edit-menu-item-url-<?php echo $item_id; ?>">
						<?php _e( 'URL', 'groovy-menu' ); ?><br/>
						<input type="text" id="edit-menu-item-url-<?php echo $item_id; ?>"
							class="widefat code edit-menu-item-url" name="menu-item-url[<?php echo $item_id; ?>]"
							value="<?php echo esc_attr( $item->url ); ?>"/>
					</label>
				</p>
			<?php endif; ?>
			<p class="description description-wide">
				<label for="edit-menu-item-title-<?php echo esc_attr( $item_id ); ?>">
					<?php esc_html_e( 'Navigation Label ("-" to hide)', 'groovy-menu' ); ?>
					<input type="text" id="edit-menu-item-title-<?php echo esc_attr( $item_id ); ?>"
						class="widefat edit-menu-item-title" name="menu-item-title[<?php echo esc_attr( $item_id ); ?>]"
						value="<?php echo esc_attr( $item->title ); ?>"/>
				</label>
			</p>
			<p class="field-title-attribute field-attr-title description description-wide">
				<label for="edit-menu-item-attr-title-<?php echo $item_id; ?>">
					<?php _e( 'Title Attribute', 'groovy-menu' ); ?><br/>
					<input type="text" id="edit-menu-item-attr-title-<?php echo $item_id; ?>"
						class="widefat edit-menu-item-attr-title" name="menu-item-attr-title[<?php echo $item_id; ?>]"
						value="<?php echo esc_attr( $item->post_excerpt ); ?>"/>
				</label>
			</p>
			<p class="field-link-target description">
				<label for="edit-menu-item-target-<?php echo $item_id; ?>">
					<input type="checkbox" id="edit-menu-item-target-<?php echo $item_id; ?>" value="_blank"
						name="menu-item-target[<?php echo $item_id; ?>]"<?php checked( $item->target, '_blank' ); ?> />
					<?php _e( 'Open link in a new tab', 'groovy-menu' ); ?>
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
			<p class="field-css-classes description description-thin">
				<label for="edit-menu-item-classes-<?php echo $item_id; ?>">
					<?php _e( 'CSS Classes (optional)', 'groovy-menu' ); ?><br/>
					<input type="text" id="edit-menu-item-classes-<?php echo $item_id; ?>"
						class="widefat code edit-menu-item-classes" name="menu-item-classes[<?php echo $item_id; ?>]"
						value="<?php echo esc_attr( $item_classes ); ?>"/>
				</label>
			</p>
			<p class="field-xfn description description-thin">
				<label for="edit-menu-item-xfn-<?php echo $item_id; ?>">
					<?php _e( 'Link Relationship (XFN)', 'groovy-menu' ); ?><br/>
					<input type="text" id="edit-menu-item-xfn-<?php echo $item_id; ?>"
						class="widefat code edit-menu-item-xfn" name="menu-item-xfn[<?php echo $item_id; ?>]"
						value="<?php echo esc_attr( $item->xfn ); ?>"/>
				</label>
			</p>
			<p class="field-description description description-wide">
				<label for="edit-menu-item-description-<?php echo $item_id; ?>">
					<?php _e( 'Description', 'groovy-menu' ); ?><br/>
					<textarea id="edit-menu-item-description-<?php echo $item_id; ?>"
						class="widefat edit-menu-item-description" rows="3" cols="20"
						name="menu-item-description[<?php echo $item_id; ?>]"><?php echo esc_html( $item->description ); // textarea_escaped ?></textarea>
					<span
						class="description"><?php _e( 'The description will be displayed in the menu if the current theme supports it.', 'groovy-menu' ); ?></span>
				</label>
			</p>


			<?php
			/**
			 * Fires just before the move buttons of a nav menu item in the menu editor.
			 *
			 * @since 5.4.0
			 *
			 * @param int       $item_id Menu item ID.
			 * @param \WP_Post  $item    Menu item data object.
			 * @param int       $depth   Depth of menu item. Used for padding.
			 * @param \stdClass $args    An object of menu item arguments.
			 * @param int       $id      Nav menu ID.
			 */
			do_action( 'wp_nav_menu_item_custom_fields', $item_id, $item, $depth, $args, $id );
			?>


			<fieldset class="field-move hide-if-no-js description description-wide">
				<span class="field-move-visual-label" aria-hidden="true"><?php _e( 'Move', 'groovy-menu' ); ?></span>
				<button type="button" class="button-link menus-move menus-move-up"
					data-dir="up"><?php _e( 'Up one', 'groovy-menu' ); ?></button>
				<button type="button" class="button-link menus-move menus-move-down"
					data-dir="down"><?php _e( 'Down one', 'groovy-menu' ); ?></button>
				<button type="button" class="button-link menus-move menus-move-left" data-dir="left"></button>
				<button type="button" class="button-link menus-move menus-move-right" data-dir="right"></button>
				<button type="button" class="button-link menus-move menus-move-top"
					data-dir="top"><?php _e( 'To the top', 'groovy-menu' ); ?></button>
			</fieldset>

			<div class="menu-item-actions description-wide submitbox">
				<?php if ( 'custom' !== $item->type && false !== $original_title ) : ?>
					<p class="link-to-original">
						<?php
						/* translators: %s: Link to menu item's original object. */
						printf( __( 'Original: %s', 'groovy-menu' ), '<a href="' . esc_attr( $item->url ) . '">' . esc_html( $original_title ) . '</a>' );
						?>
					</p>
				<?php endif; ?>

				<?php
				printf(
					'<a class="item-delete submitdelete deletion" id="delete-%s" href="%s">%s</a>',
					$item_id,
					wp_nonce_url(
						add_query_arg(
							array(
								'action'    => 'delete-menu-item',
								'menu-item' => $item_id,
							),
							admin_url( 'nav-menus.php' )
						),
						'delete-menu_item_' . $item_id
					),
					__( 'Remove', 'groovy-menu' )
				);
				?>
				<span class="meta-sep hide-if-no-js"> | </span>
				<?php
				printf(
					'<a class="item-cancel submitcancel hide-if-no-js" id="cancel-%s" href="%s#menu-item-settings-%s">%s</a>',
					$item_id,
					esc_url(
						add_query_arg(
							array(
								'edit-menu-item' => $item_id,
								'cancel'         => time(),
							),
							admin_url( 'nav-menus.php' )
						)
					),
					$item_id,
					__( 'Cancel', 'groovy-menu' )
				);
				?>
			</div>

			<input class="menu-item-data-db-id" type="hidden" name="menu-item-db-id[<?php echo $item_id; ?>]"
				value="<?php echo $item_id; ?>"/>
			<input class="menu-item-data-object-id" type="hidden" name="menu-item-object-id[<?php echo $item_id; ?>]"
				value="<?php echo esc_attr( $item->object_id ); ?>"/>
			<input class="menu-item-data-object" type="hidden" name="menu-item-object[<?php echo $item_id; ?>]"
				value="<?php echo esc_attr( $item->object ); ?>"/>
			<input class="menu-item-data-parent-id" type="hidden" name="menu-item-parent-id[<?php echo $item_id; ?>]"
				value="<?php echo esc_attr( $item->menu_item_parent ); ?>"/>
			<input class="menu-item-data-position" type="hidden" name="menu-item-position[<?php echo $item_id; ?>]"
				value="<?php echo esc_attr( $item->menu_order ); ?>"/>
			<input class="menu-item-data-type" type="hidden" name="menu-item-type[<?php echo $item_id; ?>]"
				value="<?php echo esc_attr( $item->type ); ?>"/>
		</div><!-- .menu-item-settings-->
		<ul class="menu-item-transport"></ul>
		<?php
		$output .= ob_get_clean();
	}

}
