<?php

namespace GroovyMenu;


defined( 'ABSPATH' ) || die( 'This script cannot be accessed directly.' );


/**
 * Class for add support for widgets in menu
 */
class WidgetInMenu {

	/** Prefix.
	 *
	 * @var string A string prefix for html element attributes
	 */
	public $attr_prefix;

	/**
	 * Hooks to the necessary actions and filters
	 */
	public function __construct() {

		if ( ! defined( 'GROOVY_MENU_PREFIX_WIM' ) ) {
			define( 'GROOVY_MENU_PREFIX_WIM', 'groovy-menu-wim' );
		}

		// hook the sidebar registration.
		add_action( 'widgets_init', array( $this, 'sidebar' ) );

		// hook into the edit menus admin screen.
		add_action( 'admin_init', array( $this, 'menu_setup' ) );

		// filter the menu item display on edit screen.
		add_filter( 'wp_setup_nav_menu_item', array( $this, 'label' ), 10, 1 );

		// replace the default menu add ajax.
		add_action( 'admin_init', array( $this, 'filter_ajax' ) );

		// filter the nav menu item output for rendering widgets.
		add_filter( 'walker_nav_menu_start_el', array( $this, 'start_el' ), 1, 4 );
	}


	/**
	 * Regsiter a custom widget area for our widgets
	 */
	public function sidebar() {
		register_sidebar( array(
			'name'          => esc_html__( 'Widgets for Menu items', 'groovy-menu' ),
			'id'            => esc_attr( GROOVY_MENU_PREFIX_WIM ),
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget'  => '</div>',
			'description'   => esc_html__( 'Used as Groovy Menu module. Widgets in this area will be shown on the Appearance - Menus page.', 'groovy-menu' ),
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>',
		) );
	}

	/**
	 * Setup our metabox on the edit menu screen
	 */
	public function menu_setup() {
		add_meta_box(
			'add-groovy-menu-widget-section',
			__( 'Widgets for Menu items', 'groovy-menu' ),
			array( $this, 'menu_meta_box' ),
			'nav-menus',
			'side',
			'default'
		);
	}

	/**
	 * Add a custom metabox on edit menu screen for widgets
	 *
	 * @global        int        $_nav_menu_placeholder  A placeholder index for the menu item
	 * @global        int|string $nav_menu_selected_id   (id, name or slug) of the currently-selected menu
	 * @global      array        $wp_registered_widgets  All registered widgets
	 * @global      array        $wp_registered_sidebars All registered sidebars
	 */
	public function menu_meta_box() {

		// initialise some global variables.
		global $_nav_menu_placeholder, $nav_menu_selected_id, $wp_registered_widgets, $wp_registered_sidebars;


		// initialise the output variable.
		$output = '';

		// get all the sidebar widgets.
		$sidebars_widgets = wp_get_sidebars_widgets();

		// we don't have widgets.
		if ( empty( $wp_registered_sidebars[ GROOVY_MENU_PREFIX_WIM ] ) || empty( $sidebars_widgets[ GROOVY_MENU_PREFIX_WIM ] ) || ! is_array( $sidebars_widgets[ GROOVY_MENU_PREFIX_WIM ] ) ) {

			// the default output.
			$no_widgets_output = '<p>';
			$no_widgets_output .= sprintf( __( '<a href="%s">Please add a widget</a> to the <em>Widgets in Menu</em> area', 'groovy-menu' ), admin_url( 'widgets.php' ) );
			$no_widgets_output .= '</p>';

			/**
			 * Filters the html displayed if no widgets are present in the sidebar.
			 *
			 * @since 0.1.0
			 *
			 * @param string $no_widgets_output The default output
			 */
			$no_widgets_output = apply_filters( 'groovy_menu_no_widgets_message', $no_widgets_output );

			// add to the final output.
			$output .= $no_widgets_output;
		} else {
			// we have widgets, so we'll output them in an unordered list,
			// like WordPress does.
			$output .= '<ul>';

			// loop through our widgets.
			foreach ( ( array ) $sidebars_widgets[ GROOVY_MENU_PREFIX_WIM ] as $id ) {

				// bail if not set.
				if ( ! isset( $wp_registered_widgets[ $id ] ) ) {
					continue;
				}

				// figure the placeholder index.
				$_nav_menu_placeholder = 0 > $_nav_menu_placeholder ? $_nav_menu_placeholder - 1 : - 1;

				// this widget.
				$widget = $wp_registered_widgets[ $id ];

				// the widget number (for eg, calendar-3, 3 it is).
				$widget_num = $widget['params'][0]["number"];

				// get the widget slug from the id.
				$widget_slug = rtrim( preg_replace( "|[0-9]+|i", "", $id ), '-' );

				// get the widget's settings from the options table.
				$widget_saved = get_option( 'widget_' . $widget_slug, array() );

				// get the title from the saved settings.
				$widget_title = ( isset( $widget_saved[ $widget_num ]['title'] ) ) ? $widget_saved[ $widget_num ]['title'] : '';

				// get the name.
				$widget_name = $widget['name'];
				$widget_name .= ( empty( $widget_title ) ) ? '' : ': ' . $widget_title;

				$input_hidden_pre = '<input type="hidden" ';

				// start the list item.
				$output .= '<li>';
				$output .= '<label for="' . esc_attr( $id ) . '">';

				// checkbox.
				$output .= '<input name="menu-item['
				           . esc_attr( $_nav_menu_placeholder ) . '][menu-item-object-id]" type="checkbox" value="'
				           . esc_attr( $widget_num ) . '" id="' . esc_attr( $id ) . '" class="menu-item-checkbox ' . esc_attr( $id ) . '">';
				$output .= esc_html( $widget_name );
				$output .= '</label>';

				// db-id is 0,will be created when the menu item is created in the db.
				$output .= $input_hidden_pre . 'class="menu-item-db-id" name="menu-item['
				           . esc_attr( $_nav_menu_placeholder ) . '][menu-item-db-id]" value="0" />';

				// object is our prefix.
				$output .= $input_hidden_pre . 'class="menu-item-object" name="menu-item['
				           . esc_attr( $_nav_menu_placeholder ) . '][menu-item-object]" value="'
				           . esc_attr( GROOVY_MENU_PREFIX_WIM ) . '" />';

				// no parent-id.
				$output .= $input_hidden_pre . 'class="menu-item-parent-id" name="menu-item['
				           . esc_attr( $_nav_menu_placeholder ) . '][menu-item-parent-id]" value="0" />';

				// type is our prefix.
				$output .= $input_hidden_pre . 'class="menu-item-type" name="menu-item['
				           . esc_attr( $_nav_menu_placeholder ) . '][menu-item-type]" value="' . esc_attr( GROOVY_MENU_PREFIX_WIM ) . '" />';

				// title.
				$output .= $input_hidden_pre . 'class="menu-item-title" name="menu-item['
				           . esc_attr( $_nav_menu_placeholder ) . '][menu-item-title]" value="' . esc_attr( $widget_name ) . '" />';

				// the empty stuff.
				$output .= $input_hidden_pre . 'class="menu-item-url" name="menu-item['
				           . esc_attr( $_nav_menu_placeholder ) . '][menu-item-url]" value="" />';
				$output .= $input_hidden_pre . 'class="menu-item-target" name="menu-item['
				           . esc_attr( $_nav_menu_placeholder ) . '][menu-item-target]" value="" />';
				$output .= $input_hidden_pre . 'class="menu-item-attr_title" name="menu-item['
				           . esc_attr( $_nav_menu_placeholder ) . '][menu-item-attr_title]" value="" />';
				$output .= $input_hidden_pre . 'class="menu-item-classes" name="menu-item['
				           . esc_attr( $_nav_menu_placeholder ) . '][menu-item-classes]" value="" />';

				// storing our id in xfn. could have been any of the above.
				$output .= $input_hidden_pre . 'class="menu-item-xfn" name="menu-item['
				           . esc_attr( $_nav_menu_placeholder ) . '][menu-item-xfn]" value="' . esc_attr( $id ) . '" />';


				$output .= '<p class="gm-msg-link-widgets" data-widget-id="' . esc_attr( $id ) . '">';
				$output .= esc_html__( 'Original', 'groovy-menu' )
				           . ': '
				           . sprintf( '<a href="%s">', admin_url( 'widgets.php' ) )
				           . esc_html( $widget_name ) . '</a>';
				$output .= '</p>';

				$output .= '</li>';

			}

			$output .= '</ul>';

		}

		// submit button.
		?>
			<div class="groovy-menu-wim-div" id="groovy-menu-wim-div">
			<?php echo ( $output ) ? $output : ''; ?>

			<p class="button-controls wp-clearfix">
					<span class="list-controls">
						<a href="#"
							class="select-all aria-button-if-js groovy-menu-wim--select-all"><?php esc_attr_e( 'Select All', 'groovy-menu' ); ?></a>
					</span>

				<span class="add-to-menu">
						<input type="submit"<?php wp_nav_menu_disabled_check( $nav_menu_selected_id ); ?>
							class="button submit-add-to-menu right"
							value="<?php esc_attr_e( 'Add to Menu', 'groovy-menu' ); ?>"
							name="add-groovy-menu-menu-item"
							id="submit-groovy-menu-wim"/>
						<span class="spinner"></span>
					</span>
			</p>

		</div>
		<?php
	}

	/**
	 * Removes default menu add function & replaces with custom
	 *
	 * @since 1.2.19
	 */
	public function filter_ajax() {

		// add our own function.
		add_action( 'wp_ajax_add-menu-item', array( $this, '_add_menu_item' ), 0 );

	}

	/**
	 * Ajax handler for adding a menu item. Replaces wp_ajax_add_menu_item
	 *
	 * @since 1.2.19
	 */
	public function _add_menu_item() {

		// remove default WP function.
		// first extra line in the wp_ajax_add_menu_item clone that this method actually is :( .
		remove_action( 'wp_ajax_add-menu-item', 'wp_ajax_add_menu_item' );

		check_ajax_referer( 'add-menu_item', 'menu-settings-column-nonce' );

		if ( ! current_user_can( 'edit_theme_options' ) ) {
			wp_die( - 1 );
		}

		$file_path = str_replace( array(
			'\\',
			'/'
		), DIRECTORY_SEPARATOR, ABSPATH . '/wp-admin/includes/nav-menu.php' );

		require_once $file_path;

		// For performance reasons, we omit some object properties from the checklist.
		// The following is a hacky way to restore them when adding non-custom items.

		$menu_items_data = array();

		foreach ( ( array ) $_POST['menu-item'] as $menu_item_data ) {
			if (
				! empty( $menu_item_data['menu-item-type'] ) &&
				'custom' != $menu_item_data['menu-item-type'] &&
				! empty( $menu_item_data['menu-item-object-id'] ) &&
				GROOVY_MENU_PREFIX_WIM != $menu_item_data['menu-item-type']
			) {
				switch ( $menu_item_data['menu-item-type'] ) {
					case 'post_type' :
						$_object = get_post( $menu_item_data['menu-item-object-id'] );
						break;

					case 'post_type_archive' :
						$_object = get_post_type_object( $menu_item_data['menu-item-object'] );
						break;

					case 'taxonomy' :
						$_object = get_term( $menu_item_data['menu-item-object-id'], $menu_item_data['menu-item-object'] );
						break;
				}

				if ( ! empty( $_object ) ) {
					$_menu_items = array_map( 'wp_setup_nav_menu_item', array( $_object ) );
					$_menu_item  = reset( $_menu_items );

					// Restore the missing menu item properties.
					$menu_item_data['menu-item-description'] = $_menu_item->description;
				}
			}

			$menu_items_data[] = $menu_item_data;
		}

		$item_ids = wp_save_nav_menu_items( 0, $menu_items_data );
		if ( is_wp_error( $item_ids ) ) {
			wp_die( 0 );
		}

		$menu_items = array();

		foreach ( ( array ) $item_ids as $menu_item_id ) {
			$menu_obj = get_post( $menu_item_id );
			if ( ! empty( $menu_obj->ID ) ) {
				$menu_obj        = wp_setup_nav_menu_item( $menu_obj );
				$menu_obj->label = $menu_obj->title; // don't show "(pending)" in ajax-added items.
				$menu_items[]    = $menu_obj;
			}
		}

		/** This filter is documented in wp-admin/includes/nav-menu.php */
		$walker_class_name = apply_filters( 'wp_edit_nav_menu_walker', 'Walker_Nav_Menu_Edit', esc_html( wp_unslash( $_POST['menu'] ) ) );

		if ( ! class_exists( $walker_class_name ) ) {
			wp_die( 0 );
		}

		if ( ! empty( $menu_items ) ) {
			$args = array(
				'after'       => '',
				'before'      => '',
				'link_after'  => '',
				'link_before' => '',
				'walker'      => new $walker_class_name,
			);
			echo walk_nav_menu_tree( $menu_items, 0, ( object ) $args );
		}
		wp_die();
	}


	/**
	 * Changes the label from [Custom] to [Widget] for nav-menu item
	 *
	 * @param object $item nav-menu item
	 *
	 * @return object
	 */
	function label( $item ) {

		if ( $item->object === GROOVY_MENU_PREFIX_WIM ) {

			// setup our label.
			$item->type_label = esc_html__( 'Widget', 'groovy-menu' );
		}

		return $item;
	}


	/**
	 * Render the widget in the nav menu
	 *
	 * @global      array  $wp_registered_widgets  All registered widgets
	 * @global      array  $wp_registered_sidebars All registered sidebars
	 *
	 * @param       string $item_output            The html output of the widget.
	 * @param       object $item                   The nav menu placeholder item, from the edit-menus ui.
	 * @param       int    $depth                  Depth of the item.
	 * @param       array  $args                   An array of additional arguments.
	 *
	 * @return      boolean|string                        The final html output
	 */
	public function start_el( $item_output, $item, $depth, $args ) {

		// bail early, if it is not our widget placeholder.
		if ( $item->object != GROOVY_MENU_PREFIX_WIM ) {
			return $item_output;
		}

		// get the list of registered widgets and sidebars.
		global $wp_registered_widgets, $wp_registered_sidebars;

		// we've saved the name of the widget in the xfn of the menu item.
		$id = $item->xfn;

		// if this widget is not set, bail.
		if ( ! isset( $wp_registered_widgets[ $id ] ) ) {
			return $item_output;
		}

		// get our sidebar/widget area.
		$sidebar = array_merge(
		// our sidebar is at the index 'yawp_wim'.
			$wp_registered_sidebars[ GROOVY_MENU_PREFIX_WIM ],
			// we merge our current widget into it.
			array(
				'widget_id'   => $id,
				'widget_name' => $wp_registered_widgets[ $id ]['name']
			)
		);

		// set up the widget parameters.
		$params = array_merge(
			array( $sidebar ), ( array ) $wp_registered_widgets[ $id ]['params']
		);

		// Substitute HTML id and class attributes into before_widget.
		$classname_ = '';
		foreach ( ( array ) $wp_registered_widgets[ $id ]['classname'] as $cn ) {
			if ( is_string( $cn ) ) {
				$classname_ .= '_' . $cn;
			} elseif ( is_object( $cn ) ) {
				$classname_ .= '_' . get_class( $cn );
			}
		}
		$classname_ = ltrim( $classname_, '_' );

		// set up more parameters.
		$params[0]['before_widget'] = sprintf( $params[0]['before_widget'], $id, $classname_ );

		/**
		 * Filter the parameters passed to the widget's display callback.
		 *
		 * Note: Similar to 'dynamic_sidebar_params' filter
		 *
		 * @since 0.0.1
		 *
		 * @see   register_sidebar()
		 *
		 * @param array $params        {
		 *
		 * @type array  $args          {
		 *         An array of widget display arguments.
		 *
		 * @type string $name          Name of the sidebar the widget is assigned to.
		 * @type string $id            ID of the sidebar the widget is assigned to.
		 * @type string $description   The sidebar description.
		 * @type string $class         CSS class applied to the sidebar container.
		 * @type string $before_widget HTML markup to prepend to each widget in the sidebar.
		 * @type string $after_widget  HTML markup to append to each widget in the sidebar.
		 * @type string $before_title  HTML markup to prepend to the widget title when displayed.
		 * @type string $after_title   HTML markup to append to the widget title when displayed.
		 * @type string $widget_id     ID of the widget.
		 * @type string $widget_name   Name of the widget.
		 *     }
		 * @type array  $widget_args   {
		 *         An array of multi-widget arguments.
		 *
		 * @type int    $number        Number increment used for multiples of the same widget.
		 *     }
		 * }
		 */
		$params = apply_filters( 'groovy_menu_wim_widget_params', $params );

		$gm_wim_widget = $wp_registered_widgets[ $id ];

		/**
		 * Fires before a widget's display callback is called.
		 *
		 * Note: Similar to 'dynamic_sidebar' action.
		 *
		 * @since 0.0.1
		 *
		 * @see   dynamic_sidebar()
		 *
		 * @param array         $gm_wim_widget {
		 *                                     An associative array of widget arguments.
		 *
		 * @type string         $name          Name of the widget.
		 * @type string         $id            Widget ID.
		 * @type array|callback $callback      When the hook is fired on the front-end, $callback is an array
		 *                                       containing the widget object. Fired on the back-end, $callback
		 *                                       is 'wp_widget_control', see $_callback.
		 * @type array          $params        An associative array of multi-widget arguments.
		 * @type string         $classname     CSS class applied to the widget container.
		 * @type string         $description   The widget description.
		 * @type array          $_callback     When the hook is fired on the back-end, $_callback is populated
		 *                                       with an array containing the widget object, see $callback.
		 * }
		 */
		do_action( 'groovy_menu_wim_pre_callback', $gm_wim_widget );

		// get the registered callback function for this widget.
		$callback = $wp_registered_widgets[ $id ]['callback'];

		// if we have a valid callback function.
		if ( is_callable( $callback ) ) {
			// since the callback echoes the output.
			// we use this to return the output in a var.
			ob_start();
			?>
			<div class="<?php echo esc_attr( GROOVY_MENU_PREFIX_WIM . '-wrap' ); ?>">
				<?php
				// call the widget callback function.
				call_user_func_array( $callback, $params );
				?>
			</div>
			<?php
			// assign to the variable.
			$item_output = ob_get_contents();
			ob_end_clean();
		}

		// return the widget output.
		return $item_output;
	}

}
