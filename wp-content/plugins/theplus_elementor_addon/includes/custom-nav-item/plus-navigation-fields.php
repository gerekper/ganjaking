<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * Menu item custom fields example
 *
 * @package Menu_Item_Custom_Fields_Example
 * @version 2.1.0
 */
class Menu_Item_Custom_Fields_Example {

	/**
	 * Holds our custom fields
	 *
	 * @var    array
	 * @access protected
	 * @since  Menu_Item_Custom_Fields_Example 2.1.0
	 */
	protected static $fields = array();


	/**
	 * Initialize plugin
	 */
	public static function init() {
		add_action( 'wp_nav_menu_item_custom_fields', array( __CLASS__, '_fields' ), 10, 4 );
		add_action( 'wp_update_nav_menu_item', array( __CLASS__, '_save' ), 10, 3 );
		add_filter( 'manage_nav-menus_columns', array( __CLASS__, '_columns' ), 99 );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'plus_add_color_picker') );
		add_action( 'wp_ajax_plus-menu-icon-img', array( __CLASS__, 'wp_ajax_plus_menu_item_icon_img' ) );
		
		self::$fields = array(
			'plus-text' => array(
				'tp-text-label' => esc_html__( 'Text Label', 'theplus' ),
			),			
			'plus-color' => array(
				'tp-label-color' => esc_html__( 'Label Color', 'theplus' ),
			),
			'plus-bg-color' => array(
				'tp-label-bg-color' => esc_html__( 'Label Bg Color', 'theplus' ),
			),
			'plus-radio' => array(
				'tp-menu-icon-type' => esc_html__( 'Icon Type', 'theplus' ),
			),
			'plus-icon-class' => array(
				'tp-icon-class' => esc_html__( 'Icon Class', 'theplus' ),
			),
			'plus-img' => array(
				'tp-menu-icon-img' => esc_html__( 'Icon Image', 'theplus' ),
			),
			'plus-select' => array(
				'tp-megamenu-type' => esc_html__( 'Mega menu Type', 'theplus' ),
			),
			'plus-number' => array(
				'tp-dropdown-width' => esc_html__( 'Dropdown Max-Width(px)', 'theplus' ),
			),
			'plus-menu-alignment' => array(
				'tp-menu-alignment' => esc_html__( 'Dropdown Menu Alignment', 'theplus' ),
			),
		);
	}


	/**
	 * Save custom field value
	 *
	 * @wp_hook action wp_update_nav_menu_item
	 *
	 * @param int   $menu_id         Nav menu ID
	 * @param int   $menu_item_db_id Menu item ID
	 * @param array $menu_item_args  Menu item data
	 */
	public static function _save( $menu_id, $menu_item_db_id, $menu_item_args ) {
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			return;
		}

		check_admin_referer( 'update-nav_menu', 'update-nav-menu-nonce' );

		foreach ( self::$fields as $fieldtype ) {
			foreach ( $fieldtype as $_key => $label ) {
				$key = sprintf( 'menu-item-%s', $_key );

				// Sanitize
				if ( ! empty( $_POST[ $key ] ) && ! empty( $_POST[ $key ][ $menu_item_db_id ] ) ) {
					// Do some checks here...
					$value = wp_unslash($_POST[ $key ][ $menu_item_db_id ]);
				} else {
					$value = null;
				}

				// Update
				if ( ! is_null( $value ) ) {
					update_post_meta( $menu_item_db_id, $key, $value );
				} else {
					delete_post_meta( $menu_item_db_id, $key );
				}
			}
		}
	}


	/**
	 * Print field
	 *
	 * @param object $item  Menu item data object.
	 * @param int    $depth  Depth of menu item. Used for padding.
	 * @param array  $args  Menu item args.
	 * @param int    $id    Nav menu ID.
	 *
	 * @return string Form fields
	 */
	public static function _fields( $id, $item, $depth, $args ) {
	
		foreach ( self::$fields as $key => $fieldtype ) {
		
			if($key == 'plus-text' || $key == 'plus-icon-class' ){
				foreach ( $fieldtype as $_key => $label ) :
					$key   = sprintf( 'menu-item-%s', $_key );
					$id    = sprintf( 'edit-%s-%s', $key, $item->ID );
					$name  = sprintf( '%s[%s]', $key, $item->ID );
					$value = get_post_meta( $item->ID, $key, true );
					$class = sprintf( 'field-%s', $_key );
					
					$icon_class_select = get_post_meta( $item->ID, 'menu-item-tp-menu-icon-type', true );
					$icon_class_select = ($_key == 'tp-icon-class' && $icon_class_select =='icon_class') ? 'style="display:block;"' : 'style="display:none;"';
					$icon_class_select = ($_key != 'tp-icon-class') ? 'style="display:block;"' : $icon_class_select;
					?>
						<p class="description description-wide <?php echo esc_attr( $class ) ?>" <?php echo $icon_class_select; ?>>
							<?php printf(
								'<label for="%1$s">%2$s<input type="text" id="%1$s" class="widefat code %1$s" name="%3$s" value="%4$s" /></label>',
								esc_attr( $id ),
								esc_html( $label ),
								esc_attr( $name ),
								esc_attr( $value )
							); ?>
						</p>
					<?php
				endforeach;
			}
			if( $key == 'plus-number' ){
				foreach ( $fieldtype as $_key => $label ) :
					$key   = sprintf( 'menu-item-%s', $_key );
					$id    = sprintf( 'edit-%s-%s', $key, $item->ID );
					$name  = sprintf( '%s[%s]', $key, $item->ID );
					$value = get_post_meta( $item->ID, $key, true );
					$class = sprintf( 'field-%s', $_key );
					
					$megamenu_select = get_post_meta( $item->ID, 'menu-item-tp-megamenu-type', true );
					$megamenu_class = ($megamenu_select =='default') ? 'style="display:block;"' : 'style="display:none;"';
					?>
						<p class="description description-wide <?php echo esc_attr( $class ) ?>" <?php echo $megamenu_class; ?>>
							<?php printf(
								'<label for="%1$s">%2$s<input type="number" max="1920" min="100" step="1" id="%1$s" class="widefat %1$s" name="%3$s" value="%4$s" /></label>',
								esc_attr( $id ),
								esc_html( $label ),
								esc_attr( $name ),
								esc_attr( $value )
							); ?>
						</p>
					<?php
				endforeach;
			}
			if($key == 'plus-color' || $key == 'plus-bg-color' ){
				foreach ( $fieldtype as $_key => $label ) :
					$key   = sprintf( 'menu-item-%s', $_key );
					$id    = sprintf( 'edit-%s-%s', $key, $item->ID );
					$name  = sprintf( '%s[%s]', $key, $item->ID );
					$value = get_post_meta( $item->ID, $key, true );
					$class = sprintf( 'field-%s', $_key );
					?>
						<p class="description description-wide <?php echo esc_attr( $class ) ?>">
							<?php printf(
								'<label for="%1$s">%2$s</label>
								<input id="%1$s" class="widefat code %1$s plus-color-fields" type="text" name="%3$s" value="%4$s"/>',
								esc_attr( $id ),
								esc_html( $label ),
								esc_attr( $name ),
								esc_attr( $value )
							); ?>
						</p>
					<?php
				endforeach;
			}
			if($key == 'plus-select' ){
				foreach ( $fieldtype as $_key => $label ) :
					$key   = sprintf( 'menu-item-%s', $_key );
					$id    = sprintf( 'edit-%s-%s', $key, $item->ID );
					$name  = sprintf( '%s[%s]', $key, $item->ID );
					$value = get_post_meta( $item->ID, $key, true );
					$class = sprintf( 'field-%s', $_key );
					?>
						<p class="description description-wide <?php echo esc_attr( $class ) ?>">
							<?php 
								echo '<label for="'.esc_attr( $id ).'">'.esc_html( $label ).'</label>
								<select id="'.esc_attr( $id ).'" class="widefat code plus-select-megamenu '.esc_attr( $id ).'" name="'.esc_attr( $name ).'">';
							
							$megamenu_select = array(
								'default'   => esc_html__( 'Default', 'theplus' ),
								'container'  => esc_html__( 'Container', 'theplus' ),
								'full-width'  => esc_html__( 'Full Width', 'theplus' ),
							);
							foreach ( $megamenu_select as $select_key => $data_value ) :
								$select_value=($value == $select_key) ? ' selected="selected"' : '';
								echo '<option value="'.esc_attr( $select_key ).'" '.$select_value.' >'.esc_html( $data_value ).'</option>';
							endforeach;
							
							echo '</select>';
							?>
						</p>
					<?php
				endforeach;
			}
			if($key == 'plus-menu-alignment' ){
				foreach ( $fieldtype as $_key => $label ) :
					$key   = sprintf( 'menu-item-%s', $_key );
					$id    = sprintf( 'edit-%s-%s', $key, $item->ID );
					$name  = sprintf( '%s[%s]', $key, $item->ID );
					$value = get_post_meta( $item->ID, $key, true );
					$class = sprintf( 'field-%s', $_key );
					?>
						<p class="description description-wide <?php echo esc_attr( $class ) ?>">
							<?php 
								echo '<label for="'.esc_attr( $id ).'">'.esc_html( $label ).'</label>
								<select id="'.esc_attr( $id ).'" class="widefat code plus-select-megamenu '.esc_attr( $id ).'" name="'.esc_attr( $name ).'">';
							
							$megamenu_select = array(
								''   => esc_html__( 'Default', 'theplus' ),
								'center'  => esc_html__( 'Center', 'theplus' ),								
							);
							foreach ( $megamenu_select as $select_key => $data_value ) :
								$select_value=($value == $select_key) ? ' selected="selected"' : '';
								echo '<option value="'.esc_attr( $select_key ).'" '.$select_value.' >'.esc_html( $data_value ).'</option>';
							endforeach;
							
							echo '</select>';
							?>
						</p>
					<?php
				endforeach;
			}
			if($key == 'plus-img' ){
				foreach ( $fieldtype as $_key => $label ) :
					$key   = sprintf( 'menu-item-%s', $_key );
					$id    = sprintf( 'edit-%s-%s', $key, $item->ID );
					$name  = sprintf( '%s[%s]', $key, $item->ID );
					$value = get_post_meta( $item->ID, $key, true );
					$class = sprintf( 'field-%s', $_key );
					
					$icon_image_select = get_post_meta( $item->ID, 'menu-item-tp-menu-icon-type', true );
					$icon_image_select = ($icon_image_select =='icon_image') ? 'style="display:block;"' : 'style="display:none;"';
					
					echo '<div class="plus-menu-field-image hide-if-no-js wp-media-buttons" '.$icon_image_select.'>';
						echo self::wp_post_thumbnail_html( $item->ID );
					echo '</div>';
				endforeach;
			}
			if($key == 'plus-radio'){
				foreach ( $fieldtype as $_key => $label ) :
					$key   = sprintf( 'menu-item-%s', $_key );
					$id    = sprintf( 'edit-%s-%s', $key, $item->ID );
					$name  = sprintf( '%s[%s]', $key, $item->ID );
					$value = get_post_meta( $item->ID, $key, true );
					$class = sprintf( 'field-%s', $_key );
					
				?>
					<p class="description description-wide">					
						<?php
						printf(
							'<label for="%1$s">%2$s</label></br>', esc_attr( $id ), esc_html( $label ) );
						$icon_radio = array(
							'icon_class'   => esc_html__( 'Icon', 'theplus' ),
							'icon_image'  => esc_html__( 'Icon Image', 'theplus' ),
						);
						foreach ( $icon_radio as $icon_key => $label ) :
							printf(
								"<label><input type='radio' name='%s' class='tp-menu-item-icon-type' value='%s'%s/>%s</label>&nbsp;&nbsp;&nbsp;&nbsp;",
								$name,
								esc_attr( $icon_key ),
								$value == $icon_key ? ' checked="checked"' : '',
								$label
							);
						endforeach;
						?>
					</p>
			<?php 
				endforeach;
			}
		}
	}
	
	public static function wp_post_thumbnail_only_html( $item_id ) {
		
		$markup = '<p class="description description-thin" ><label>%s<br /><a title="%s" href="#" class="plus-menu-icon-thumbnail button %s" data-item-id="%s" style="height: auto;">%s</a>%s</label></p>';
		
		$thumbnail_id = get_post_meta( $item_id, 'tp-menu-icon-img', true );
		$content      = sprintf(
			$markup,
			esc_html__( 'Menu image', 'theplus' ),
			$thumbnail_id ? esc_attr__( 'Change menu item image', 'theplus' ) : esc_attr__( 'Set menu item image', 'theplus' ),
			$thumbnail_id ? esc_attr__( 'change-icon', 'theplus' ) : esc_attr__( 'set-icon', 'theplus' ),
			$item_id,
			$thumbnail_id ? wp_get_attachment_image( $thumbnail_id, 'medium' ) : esc_html__( 'Set image', 'theplus' ),
			$thumbnail_id ? '<a href="#" class="plus-menu-icon-remove">' . esc_html__( 'Remove', 'theplus' ) . '</a>' : ''
		);

		return $content;
	}
	
	public static function wp_post_thumbnail_html( $item_id ) {
		
		$content      = self::wp_post_thumbnail_only_html( $item_id );

		ob_start();

		$content = "<div class='plus-menu-item-icon-images' style='min-height:70px'>$content</div>" . ob_get_clean();

		/**
		 * Filter the admin menu item thumbnail HTML markup to return.
		 *
		 * @since 2.0
		 *
		 * @param string $content Admin menu item images HTML markup.
		 * @param int    $item_id Post ID.
		 */
		return apply_filters( 'admin_menu_item_thumbnail_html', $content, $item_id );
	}
	
	/**
	 * Update item thumbnail via ajax action.
	 *
	 * @since 2.0
	 */
	public static function wp_ajax_plus_menu_item_icon_img() {
		$json = ! empty( $_POST['json'] ) ? wp_validate_boolean($_POST['json']) : false;
		
		$post_ID = intval( $_POST['post_id'] );
		if ( ! current_user_can( 'edit_post', $post_ID ) ) {
			wp_die( - 1 );
		}

		$thumbnail_id = intval( $_POST['thumbnail_id'] );
		
		check_ajax_referer( 'update-menu-item' );

		if ( $thumbnail_id == '-1' ) {
			$success = delete_post_meta( $post_ID, 'tp-menu-icon-img' );
			//echo $post_ID.'/'.$thumbnail_id;
		} else {
			$success = update_post_meta( $post_ID, 'tp-menu-icon-img', $thumbnail_id );
			//echo $post_ID.'-'.$thumbnail_id;
		}

		if ( $success ) {
			$return = self::wp_post_thumbnail_only_html( $post_ID );
			$json ? wp_send_json_success( $return ) : wp_die( $return );
		}

		wp_die( 0 );
	}
	
	public static function plus_add_color_picker($hook) {
	 
		if( is_admin() ) { 
		 
			// Add the color picker css file       
			wp_enqueue_style( 'wp-color-picker' ); 
			 
			// Include our custom jQuery file with WordPress Color Picker dependency
			wp_enqueue_script( 'plus-nav-item', THEPLUS_ASSETS_URL. 'js/admin/plus-nav-item.js', array( 'wp-color-picker' ), false, true );
			wp_localize_script(
				'plus-nav-item', 'menuImage', array(
					'l10n'     => array(
						'uploaderTitle'      => esc_html__( 'Chose menu image', 'theplus' ),
						'uploaderButtonText' => esc_html__( 'Select', 'theplus' ),
					),
					'settings' => array(
						'nonce' => wp_create_nonce( 'update-menu-item' ),
					),
				)
			);
			wp_enqueue_media();
			wp_enqueue_style( 'editor-buttons' );
		}
	}

	/**
	 * Add our fields to the screen options toggle
	 *
	 * @param array $columns Menu item columns
	 * @return array
	 */
	public static function _columns( $columns ) {
		$columns_fields =array();
		foreach ( self::$fields as $fieldtype ) {
			$columns_fields = $fieldtype;
		}
	
		$columns = array_merge( $columns, $columns_fields );

		return $columns;
	}
}
Menu_Item_Custom_Fields_Example::init();