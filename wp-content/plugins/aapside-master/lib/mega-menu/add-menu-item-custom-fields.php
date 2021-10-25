<?php

/**
 * Package Appside
 * Author Ir Tech
 * @since 1.0.0
 * */

if (!defined('ABSPATH')){
	exit(); //exit if access directly
}

if (!class_exists('Appside_Menu_Item_Custom_Fields')){
	class Appside_Menu_Item_Custom_Fields{

		/**
		 * Holds our custom fields
		 *
		 * @var    array
		 * @access protected
		 * @since  Menu_Item_Custom_Fields_Example 0.2.0
		 */
		protected static $fields = array();

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

			foreach ( self::$fields as $_key => $label ) {
				$key = sprintf( 'menu-item-%s', $_key );

				// Sanitize
				if ( ! empty( $_POST[ $key ][ $menu_item_db_id ] ) ) {
					// Do some checks here...
					$value = $_POST[ $key ][ $menu_item_db_id ];
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

		/**
		 * Initialize fileds
		 */
		public static function init() {
			add_action( 'wp_nav_menu_item_custom_fields', array( __CLASS__, '_fields' ), 10, 4 );
			add_action( 'wp_update_nav_menu_item', array( __CLASS__, '_save' ), 10, 3 );
			add_filter( 'manage_nav-menus_columns', array( __CLASS__, '_columns' ), 99 );

			self::$fields = array(
				'mega-menu' => esc_html__( 'Elementor Mega Menu (optional)', 'appside-master' )
			);
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
			$appside = appside_master();
			$all_mega_menus = $appside->get_post_list_by_post_type('apside-mega-menu');

			foreach ( self::$fields as $_key => $label ) :
				$key   = sprintf( 'menu-item-%s', $_key );
				$id    = sprintf( 'edit-%s-%s', $key, $item->ID );
				$name  = sprintf( '%s[%s]', $key, $item->ID );
				$value = get_post_meta( $item->ID, $key, true );
				$class = sprintf( 'field-%s', $_key );
				if ($_key == 'mega-menu'):

					$options = '<option value="">'. esc_html__('No Mega Menu Founds','appside-master').'</option>';

					if (!empty($all_mega_menus)){
						$options = '<option value="">'. esc_html__('Select Mega Menu','appside-master').'</option>';
						foreach ($all_mega_menus as $mid => $mtitle){
						    $checked = $value == $mid ? 'selected' : '';
							$options .= '<option '.$checked.' value="'.esc_attr($mid).'">'. esc_html($mtitle).'</option>';
						}
					}

				?>
					<p class="description description-wide <?php echo esc_attr( $class ) ?>">
						<?php printf(
							'<label for="%1$s">%2$s<br /><select id="%1$s" class="widefat %1$s" name="%3$s">%4$s</select></label>',
							esc_attr( $id ),
							esc_html( $label ),
							esc_attr( $name ),
							$options
						) ?>
					</p>
				<?php else: ?>
				<p class="description description-wide <?php echo esc_attr( $class ) ?>">
					<?php printf(
						'<label for="%1$s">%2$s<br /><input type="text" id="%1$s" class="widefat %1$s" name="%3$s" value="%4$s" /></label>',
						esc_attr( $id ),
						esc_html( $label ),
						esc_attr( $name ),
						esc_attr( $value )
					) ?>
				</p>
			<?php
				endif;
			endforeach;
		}

		/**
		 * Add our fields to the screen options toggle
		 *
		 * @param array $columns Menu item columns
		 * @return array
		 */
		public static function _columns( $columns ) {
			$columns = array_merge( $columns, self::$fields );

			return $columns;
		}

	}//end class
	if ( class_exists('Appside_Menu_Item_Custom_Fields')){
		Appside_Menu_Item_Custom_Fields::init();
	}
}