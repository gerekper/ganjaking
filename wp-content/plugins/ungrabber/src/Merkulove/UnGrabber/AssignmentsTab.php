<?php
/**
 * A most effective way to protect your online content from being copied or grabbed
 * Exclusively on Envato Market: https://1.envato.market/ungrabber
 *
 * @encoding        UTF-8
 * @version         2.0.1
 * @copyright       Copyright (C) 2018 - 2020 Merkulove ( https://merkulov.design/ ). All rights reserved.
 * @license         Commercial Software
 * @contributors    Alexander Khmelnitskiy (info@alexander.khmelnitskiy.ua), Dmitry Merkulov (dmitry@merkulov.design)
 * @support         help@merkulov.design
 **/

namespace Merkulove\UnGrabber;

use Merkulove\UnGrabber as UnGrabber;

/** Exit if accessed directly. */
if ( ! defined( 'ABSPATH' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
}

/**
 * SINGLETON: Class used to implement Assignments tab on plugin settings page.
 *
 * @since 1.0.0
 * @author Alexandr Khmelnytsky (info@alexander.khmelnitskiy.ua)
 **/
final class AssignmentsTab {

	/**
	 * The one true AssignmentsTab.
	 *
	 * @var AssignmentsTab
	 * @since 1.0.0
	 **/
	private static $instance;

	/**
	 * Sets up a new AssignmentsTab instance.
	 *
	 * @since 1.0.0
	 * @access public
	 **/
	private function __construct() {

		/** Load JS and CSS for Backend Area. */
		$this->enqueue_backend();

	}

	/**
	 * Load JS and CSS for Backend Area.
	 *
	 * @since 1.0.0
	 * @access public
	 **/
	function enqueue_backend() {

		/** Add admin javascript. */
		add_action( 'admin_enqueue_scripts', [ $this, 'add_admin_scripts' ] );

	}

	/**
	 * Add JS for admin area.
	 *
     * @since 1.0.0
     *
	 * @return void
	 **/
	public function add_admin_scripts() {

		$screen = get_current_screen();

		/** Add styles only on Settings page. */
		if ( $screen->base == "toplevel_page_mdp_ungrabber_settings" ) {
			wp_enqueue_script( 'mdp-ungrabber-assignments', UnGrabber::$url . 'js/assignments' . UnGrabber::$suffix . '.js', [ 'jquery' ], UnGrabber::$version, true );

			/** Add code editor for Custom PHP. */
			wp_enqueue_code_editor( array( 'type' => 'application/x-httpd-php' ) );
		}

	}

	/**
	 * Generate Assignments Tab.
	 *
	 * @since 1.0.0
	 * @access public
	 **/
	public function add_settings() {

		/** Assignment Tab. */
		register_setting( 'UnGrabberAssignmentsOptionsGroup', 'mdp_ungrabber_assignments_settings' );
		add_settings_section( 'mdp_ungrabber_settings_page_assignments_section', '', null, 'UnGrabberAssignmentsOptionsGroup' );

	}

	/**
	 * Render Assignments field.
	 *
	 * @since 1.0.0
     *
     * @return void
	 **/
	public function render_assignments() {

		$optval = get_option( 'mdp_ungrabber_assignments_settings' );

		/**
		 * Output options list for select
		 */
		$options  = array();
		$defaults = array(
			'search' => 'Search'
		);

		$selected = is_array( $options ) ? $options : array( '*' );

		if ( count( $selected ) > 1 && in_array( '*', $selected ) ) {
			$selected = array( '*' );
		}

		// set default options
		foreach ( $defaults as $val => $label ) {
			$attributes = in_array( $val, $selected ) ? array(
				'value'    => $val,
				'selected' => 'selected'
			) : array( 'value' => $val );
			$options[]  = sprintf( '<option value="%s">%s</option>', $attributes["value"], $label );
		}

		// set pages
		if ( $pages = get_pages() ) {
			$options[] = '<optgroup label="Pages">';

			array_unshift( $pages, (object) array( 'post_title' => 'Pages (All)' ) );

			foreach ( $pages as $page ) {
				$val        = isset( $page->ID ) ? 'page-' . $page->ID : 'page';
				$attributes = in_array( $val, $selected ) ? array(
					'value'    => $val,
					'selected' => 'selected'
				) : array( 'value' => $val );
				$options[]  = sprintf( '<option value="%s">%s</option>', $attributes["value"], $page->post_title );
			}

			$options[] = '</optgroup>';
		}

		// set posts
		$options[] = '<optgroup label="Post">';
		foreach ( array( 'home', 'single', 'archive' ) as $view ) {
			$val        = $view;
			$attributes = in_array( $val, $selected ) ? array(
				'value'    => $val,
				'selected' => 'selected'
			) : array( 'value' => $val );
			$options[]  = sprintf( '<option value="%s">%s (%s)</option>', $attributes["value"], 'Post', ucfirst( $view ) );
		}
		$options[] = '</optgroup>';

		// set custom post types
		foreach ( array_keys( get_post_types( array( '_builtin' => false ) ) ) as $posttype ) {
			$obj   = get_post_type_object( $posttype );
			$label = ucfirst( $posttype );

			if ( $obj->publicly_queryable ) {
				$options[] = '<optgroup label="' . $label . '">';

				foreach ( array( 'single', 'archive', 'search' ) as $view ) {
					$val        = $posttype . '-' . $view;
					$attributes = in_array( $val, $selected ) ? array(
						'value'    => $val,
						'selected' => 'selected'
					) : array( 'value' => $val );
					$options[]  = sprintf( '<option value="%s">%s (%s)</option>', $attributes["value"], $label, ucfirst( $view ) );
				}

				$options[] = '</optgroup>';
			}
		}

		// set categories
		foreach ( array_keys( get_taxonomies() ) as $tax ) {

			if ( in_array( $tax, array( "post_tag", "nav_menu" ) ) ) {
				continue;
			}

			if ( $categories = get_categories( array( 'taxonomy' => $tax ) ) ) {
				$options[] = '<optgroup label="Categories (' . ucfirst( str_replace( array(
						"_",
						"-"
					), " ", $tax ) ) . ')">';

				foreach ( $categories as $category ) {
					$val        = 'cat-' . $category->cat_ID;
					$attributes = in_array( $val, $selected ) ? array(
						'value'    => $val,
						'selected' => 'selected'
					) : array( 'value' => $val );
					$options[]  = sprintf( '<option value="%s">%s</option>', $attributes["value"], $category->cat_name );
				}

				$options[] = '</optgroup>';
			}
		}

		// Set Default value
		if ( ! isset( $optval['mdp_ungrabber_assignments_field'] ) ) {
			$optval['mdp_ungrabber_assignments_field'] = "{|matchingMethod|:1,|WPContent|:0,|WPContentVal|:||,|homePage|:0,|menuItems|:0,|menuItemsVal|:||,|dateTime|:0,|dateTimeStart|:||,|dateTimeEnd|:||,|userRoles|:0,|userRolesVal|:||,|URL|:0,|URLVal|:||,|devices|:0,|devicesVal|:||,|PHP|:0,|PHPVal|:||}";
		}
		?>

        <input type='hidden' class="mdp-width-1-1" id="mdp-assignInput"
               name='mdp_ungrabber_assignments_settings[mdp_ungrabber_assignments_field]'
               value='<?php echo esc_attr( $optval['mdp_ungrabber_assignments_field'] ); ?>'>

        <div id="mdp-assign-box">

            <div class="mdp-all-fields">
                <p class="mdp-alert"><?php esc_html_e( 'By selecting the specific assignments you can limit where plugin should or shouldn\'t be published. To have it published on all pages, simply do not specify any assignments.' ); ?></p>
                <div class="mdp-panel mdp-panel-box mdp-matching-method mdp-margin">
                    <h4><?php esc_html_e( 'Matching Method', 'ungrabber' ); ?></h4>
                    <div class="mdp-button-group mdp-button-group mdp-matchingMethod" data-mdp-button-radio="">
                        <button class="mdp-button mdp-button-success mdp-button-small mdp-all mdp-active"><?php esc_html_e( 'ALL', 'ungrabber' ); ?></button>
                        <button class="mdp-button mdp-button-success mdp-button-small mdp-any"><?php esc_html_e( 'ANY', 'ungrabber' ); ?></button>
                    </div>
                    <p><?php esc_html_e('The plugin is enabled on the page if ALL the selected rules matched, or if ANY of the selected rules matched', 'ungrabber' ); ?></p>
                </div>

                <div class="mdp-panel mdp-panel-box mdp-wp-content mdp-margin">

                    <h4><?php esc_html_e( 'WordPress Content', 'ungrabber' ); ?></h4>

                    <div class="mdp-button-group mdp-button-group" data-mdp-button-radio="">
                        <button class="mdp-button mdp-button-primary mdp-button-small mdp-ignore mdp-active"><?php esc_html_e( 'Ignore', 'ungrabber' ); ?></button>
                        <button class="mdp-button mdp-button-success mdp-button-small mdp-include"><?php esc_html_e( 'Include', 'ungrabber' ); ?></button>
                        <button class="mdp-button mdp-button-danger mdp-button-small mdp-exclude"><?php esc_html_e( 'Exclude', 'ungrabber' ); ?></button>
                    </div>

                    <div class="mdp-wp-content-box">
                        <p class="mdp-margin-bottom-remove mdp-margin-top">
							<?php esc_html_e( 'Select on what page types or categories the assignment should be active.', 'ungrabber' ); ?>
                        </p>
                        <!--suppress HtmlFormInputWithoutLabel -->
                        <select class="wp-content chosen-select" multiple="multiple">
                            <option value=""></option>
							<?php echo implode( "", $options ) ?>
                        </select>
                    </div>

                </div>

                <div class="mdp-panel mdp-panel-box mdp-home-page mdp-margin">

                    <h4><?php esc_html_e( 'Home Page', 'ungrabber' ); ?></h4>

                    <div class="mdp-button-group mdp-button-group" data-mdp-button-radio="">
                        <button class="mdp-button mdp-button-primary mdp-button-small mdp-ignore mdp-active"><?php esc_html_e( 'Ignore', 'ungrabber' ); ?></button>
                        <button class="mdp-button mdp-button-success mdp-button-small mdp-include"><?php esc_html_e( 'Include', 'ungrabber' ); ?></button>
                        <button class="mdp-button mdp-button-danger mdp-button-small mdp-exclude"><?php esc_html_e( 'Exclude', 'ungrabber' ); ?></button>
                    </div>

                </div>

                <div class="mdp-panel mdp-panel-box mdp-menu-items mdp-margin">
                    <h4><?php esc_html_e( 'Menu Items', 'ungrabber' ); ?></h4>
                    <div class="mdp-button-group mdp-button-group" data-mdp-button-radio="">
                        <button class="mdp-button mdp-button-primary mdp-button-small mdp-ignore mdp-active"><?php esc_html_e( 'Ignore', 'ungrabber' ); ?></button>
                        <button class="mdp-button mdp-button-success mdp-button-small mdp-include"><?php esc_html_e( 'Include', 'ungrabber' ); ?></button>
                        <button class="mdp-button mdp-button-danger mdp-button-small mdp-exclude"><?php esc_html_e( 'Exclude', 'ungrabber' ); ?></button>
                    </div>

                    <div class="mdp-menuitems-selection">
                        <p class="mdp-margin-bottom-remove mdp-margin-top"><?php esc_html_e( 'Select the menu items to assign to.', 'ungrabber' ); ?></p>
                        <!--suppress HtmlFormInputWithoutLabel -->
                        <select class="menuitems chosen-select" multiple="">
                            <option value=""></option>
		                    <?php
		                    /** Get all menus */
		                    $menus = get_terms( 'nav_menu', ['hide_empty' => true] );
		                    foreach ( $menus as $menu ) {
			                    ?><optgroup label="<?php esc_attr_e( $menu->name ); ?>"><?php
			                    $menuTree = $this->wpse_nav_menu_2_tree( $menu->slug );
			                    $this->printMenuTree( $menuTree, $menu->slug, 0 );
			                    ?></optgroup><?php
		                    }
		                    ?>
                        </select>
                    </div>

                </div>

                <div class="mdp-panel mdp-panel-box mdp-date-time mdp-margin">

                    <h4><?php esc_html_e( 'Date & Time', 'ungrabber' ); ?></h4>

                    <div class="mdp-button-group mdp-button-group" data-mdp-button-radio="">
                        <button class="mdp-button mdp-button-primary mdp-button-small mdp-ignore mdp-active"><?php esc_html_e( 'Ignore', 'ungrabber' ); ?></button>
                        <button class="mdp-button mdp-button-success mdp-button-small mdp-include"><?php esc_html_e( 'Include', 'ungrabber' ); ?></button>
                        <button class="mdp-button mdp-button-danger mdp-button-small mdp-exclude"><?php esc_html_e( 'Exclude', 'ungrabber' ); ?></button>
                    </div>

                    <div class="mdp-period-picker-box">
                        <p class="mdp-period-picker mdp-margin-top">
                            <!--suppress HtmlFormInputWithoutLabel -->
                            <input class="mdp-period-picker-start" id="mdp-period-picker-start" type="text" value=""/>
                            <!--suppress HtmlFormInputWithoutLabel -->
                            <input class="mdp-period-picker-end" id="mdp-period-picker-end" type="text" value=""/>
                        </p>

                        <p>
							<?php esc_html_e( 'The date and time assignments use the date/time of your servers, not that of the visitors system.', 'ungrabber' ); ?>
                            <br>
							<?php esc_html_e( 'Current date/time:', 'ungrabber' ); ?>
                            <strong><?php echo date( "d.m.Y H:i" ); ?></strong>
                        </p>
                    </div>

                </div>

                <div class="mdp-panel mdp-panel-box mdp-user-roles mdp-margin">

                    <h4><?php esc_html_e( 'User Roles', 'ungrabber' ); ?></h4>

                    <div class="mdp-button-group mdp-button-group" data-mdp-button-radio="">
                        <button class="mdp-button mdp-button-primary mdp-button-small mdp-ignore mdp-active"><?php esc_html_e( 'Ignore', 'ungrabber' ); ?></button>
                        <button class="mdp-button mdp-button-success mdp-button-small mdp-include"><?php esc_html_e( 'Include', 'ungrabber' ); ?></button>
                        <button class="mdp-button mdp-button-danger mdp-button-small mdp-exclude"><?php esc_html_e( 'Exclude', 'ungrabber' ); ?></button>
                    </div>

                    <div class="user-roles-box">
                        <p class="mdp-margin-remove-bottom mdp-margin-top"><?php esc_html_e( 'Select the user roles to assign to.', 'ungrabber' ); ?></p>
                        <!--suppress HtmlFormInputWithoutLabel -->
                        <select class="user-roles chosen-select" multiple="">
                            <option value=""></option>
							<?php // Get user roles
							$roles = get_editable_roles();
							foreach ( $roles as $k => $role ) {
								?>
                                <option value="<?php echo $k; ?>"><?php echo $role['name']; ?></option><?php
							} ?>
                        </select>
                    </div>

                </div>

                <div class="mdp-panel mdp-panel-box mdp-url mdp-margin" style="display: none;">

                    <h4><?php esc_html_e( 'URL', 'ungrabber' ); ?></h4>

                    <div class="mdp-button-group mdp-button-group" data-mdp-button-radio="">
                        <button class="mdp-button mdp-button-primary mdp-button-small mdp-ignore mdp-active"><?php esc_html_e( 'Ignore', 'ungrabber' ); ?></button>
                        <button class="mdp-button mdp-button-success mdp-button-small mdp-include"><?php esc_html_e( 'Include', 'ungrabber' ); ?></button>
                        <button class="mdp-button mdp-button-danger mdp-button-small mdp-exclude"><?php esc_html_e( 'Exclude', 'ungrabber' ); ?></button>
                    </div>

                    <div class="mdp-url-box">
                        <p class="mdp-margin-bottom-remove mdp-margin-top">
							<?php esc_html_e( 'Enter (part of) the URLs to match.', 'ungrabber' ); ?><br>
							<?php esc_html_e( 'Use a new line for each different match.', 'ungrabber' ); ?>
                        </p>

                        <!--suppress HtmlFormInputWithoutLabel -->
                        <textarea class="mdp-url-field"></textarea>

                    </div>

                </div>

                <div class="mdp-panel mdp-panel-box mdp-devices mdp-margin">

                    <h4><?php esc_html_e( 'Devices', 'ungrabber' ); ?></h4>

                    <div class="mdp-button-group mdp-button-group" data-mdp-button-radio="">
                        <button class="mdp-button mdp-button-primary mdp-button-small mdp-ignore mdp-active"><?php esc_html_e( 'Ignore', 'ungrabber' ); ?></button>
                        <button class="mdp-button mdp-button-success mdp-button-small mdp-include"><?php esc_html_e( 'Include', 'ungrabber' ); ?></button>
                        <button class="mdp-button mdp-button-danger mdp-button-small mdp-exclude"><?php esc_html_e( 'Exclude', 'ungrabber' ); ?></button>
                    </div>

                    <div class="mdp-devices-box">
                        <p class="mdp-margin-remove-bottom mdp-margin-top"><?php esc_html_e( 'Select the devices to assign to. Keep in mind that device detection is not always 100% accurate. Users can setup their device to mimic other devices.', 'ungrabber' ); ?></p>
                        <!--suppress HtmlFormInputWithoutLabel -->
                        <select class="devices chosen-select" multiple="">
                            <option value=""></option>
                            <option value="desktop"><?php esc_html_e( 'Desktop', 'ungrabber' ); ?></option>
                            <option value="tablet"><?php esc_html_e( 'Tablet', 'ungrabber' ); ?></option>
                            <option value="mobile"><?php esc_html_e( 'Mobile', 'ungrabber' ); ?></option>
                        </select>
                    </div>

                </div>

                <div class="mdp-panel mdp-panel-box mdp-php mdp-margin">

                    <h4><?php esc_html_e( 'Custom PHP', 'ungrabber' ); ?></h4>

                    <div class="mdp-button-group mdp-button-group" data-mdp-button-radio="">
                        <button class="mdp-button mdp-button-primary mdp-button-small mdp-ignore mdp-active"><?php esc_html_e( 'Ignore', 'ungrabber' ); ?></button>
                        <button class="mdp-button mdp-button-success mdp-button-small mdp-include"><?php esc_html_e( 'Include', 'ungrabber' ); ?></button>
                        <button class="mdp-button mdp-button-danger mdp-button-small mdp-exclude"><?php esc_html_e( 'Exclude', 'ungrabber' ); ?></button>
                    </div>

                    <div class="mdp-php-box">
                        <p class="mdp-margin-bottom-remove mdp-margin-top">
							<?php esc_html_e( 'Enter a piece of PHP code to evaluate. The code must return the value true or false.', 'ungrabber' ); ?>
                            <br>
							<?php esc_html_e( 'For instance:', 'ungrabber' ); ?>
                        </p>
                        <pre>$dayofweek = date('w');
if( '5' == $dayofweek ) { // Work only on Fridays.
    $result = true;
} else {
    $result = false;
}
return $result;</pre>

                        <!--suppress HtmlFormInputWithoutLabel -->
                        <textarea id="mdp-php-field" name="mdp-php-field" class="mdp-php-field"></textarea>

                    </div>

                </div>

            </div>

        </div>

		<?php
	}

	/**
	 * Checks if a plugin should work on current page.
	 *
	 * @return boolean
	 *
	 * @since 1.0.0
	 * @access protected
	 **/
	public function display() {

		/** Get plugin options. */
		$options = get_option( 'mdp_ungrabber_assignments_settings' );

		/** Assignments. */
		if ( ! isset( $options['mdp_ungrabber_assignments_field'] ) ) {
			$options['mdp_ungrabber_assignments_field'] = "{|matchingMethod|:1,|WPContent|:0,|WPContentVal|:||,|homePage|:0,|menuItems|:0,|menuItemsVal|:||,|dateTime|:0,|dateTimeStart|:||,|dateTimeEnd|:||,|userRoles|:0,|userRolesVal|:||,|URL|:0,|URLVal|:||,|devices|:0,|devicesVal|:||,|PHP|:0,|PHPVal|:||}";
		}

		/** Get assignments for plugin. */
		$assignment = json_decode( str_replace( '|', '"', $options['mdp_ungrabber_assignments_field'] ) );

		if ( ! $assignment ) {
			return true;
		} // If no settings - Show plugin Everywhere

		/** WordPress Content. */
		$wordPressContent = $this->WordPressContent( $assignment );

		/** Home Page. */
		$homePage = $this->HomePage( $assignment );

		/** Menu Items. */
		$menuItems = $this->MenuItems( $assignment );

		/** Date & Time */
		$dateTime = $this->DateTime( $assignment );

		/** User Roles. */
		$userRoles = $this->UserRoles( $assignment );

		/** URL. */
		$URL = $this->URL( $assignment );

		/** Devices. */
		$devices = $this->Devices( $assignment );

		/** Custom PHP. */
		$PHP = $this->PHP( $assignment );

		/** Matching Method. */
		$result = $this->MatchingMethod( $assignment, $wordPressContent, $homePage, $menuItems, $dateTime, $userRoles, $URL, $devices, $PHP );

		return $result;

	}

	/**
	 * Plugin Assignments - Date & Time.
	 *
	 * @param $assignment
	 *
	 * @since 1.0.0
	 * @access protected
     *
     * @return bool|int
	 **/
	protected function DateTime( $assignment ) {

		/** If no dateTime - ignore. */
		if ( $assignment->dateTimeStart == "" or $assignment->dateTimeEnd == "" ) {
			$result = - 1;

			return $result;
		}

		$time = time();
		$s    = strtotime( $assignment->dateTimeStart ) - $time;
		$e    = strtotime( $assignment->dateTimeEnd ) - $time;

		switch ( $assignment->dateTime ) {
			case 1: // Include
				$result = false;
				if ( $s <= 0 AND $e >= 0 ) {
					$result = true;
				}
				break;

			case 2: // Exclude
				$result = true;
				if ( $s <= 0 AND $e >= 0 ) {
					$result = false;
				}
				break;

			case 0;
			default: // Ignore
				$result = - 1;
				break;

		}

		return $result;
	}

	/**
	 * Plugin assignments - WordPress Content.
	 *
	 * @param $assignment
	 *
	 * @since 1.0.0
	 * @access protected
     *
     * @return bool|int
	 **/
	protected function WordPressContent( $assignment ) {

		$result = - 1;

		switch ( $assignment->WPContent ) {
			case 0: // Ignore
				$result = - 1;
				break;

			case 1: // Include
				$result = false;
				if ( ! $assignment->WPContentVal ) {
					$result = - 1;

					return $result;
				} // If no menu items - ignore

				$query = $this->getQuery();
				foreach ( $query as $q ) {
					if ( in_array( $q, $assignment->WPContentVal ) ) {
						$result = true;

						return $result;
					}
				}
				break;

			case 2: // Exclude
				$result = true;
				if ( ! $assignment->WPContentVal ) {
					$result = - 1;

					return $result;
				} // If no menu items - ignore

				$query = $this->getQuery();
				foreach ( $query as $q ) {
					if ( in_array( $q, $assignment->WPContentVal ) ) {
						$result = false;

						return $result;
					}
				}
				break;
		}

		return $result;
	}

	/**
	 * Plugin assignments - Home Page.
	 *
	 * @param $assignment
	 *
	 * @since 1.0.0
	 * @access protected
     *
     * @return bool|int
	 **/
	protected function HomePage( $assignment ) {
		switch ( $assignment->homePage ) {

			case 1: // Include
				$result = false;
				if ( is_front_page() ) {
					$result = true;
				}
				break;

			case 2: // Exclude
				$result = true;
				if ( is_front_page() ) {
					$result = false;
				}
				break;

			case 0; // Ignore
            default:
				$result = - 1;
				break;
		}

		return $result;
	}

	/**
	 * Plugin assignments - Menu Items.
	 *
	 * @param $assignment
	 *
	 * @since 1.0.0
	 * @access protected
     *
     * @return bool|int
	 **/
	protected function MenuItems( $assignment ) {

		$result = - 1;

		// If wrong input array - Ignore
		if ( ! is_array( $assignment->menuItemsVal ) ) {
			$result = - 1;

			return $result;
		}

		// Current URL
		if ( ! isset( $_SERVER["HTTPS"] ) || ( $_SERVER["HTTPS"] != 'on' ) ) {
			$currentUrl = 'http://' . $_SERVER["SERVER_NAME"];
		} else {
			$currentUrl = 'https://' . $_SERVER["SERVER_NAME"];
		}
		$currentUrl .= $_SERVER["REQUEST_URI"];

		switch ( $assignment->menuItems ) {
			case 0: // Ignore
				$result = - 1;
				break;

			case 1: // Include
				$result = false;
				if ( ! $assignment->menuItemsVal ) {
					$result = - 1;

					return $result;
				} // If no menu items - ignore

				$menu_items_arr = array(); // Assignments menu items
				foreach ( $assignment->menuItemsVal as $val ) {
					if ( $val == "" ) {
						continue;
					}
					list( $menuSlug, $menuItemID ) = explode( "+", $val );
					$menu_items       = wp_get_nav_menu_items( $menuSlug );
					$menu_item        = wp_filter_object_list( $menu_items, array( 'ID' => $menuItemID ) );
					$menu_items_arr[] = reset( $menu_item );
				}

				foreach ( $menu_items_arr as $mItem ) {
					if ( $currentUrl == $mItem->url ) {
						$result = true;

						return $result;
					}
				}
				break;

			case 2: // Exclude
				$result = true;
				if ( ! $assignment->menuItemsVal ) {
					$result = - 1;

					return $result;
				} // If no menu items - ignore

				$menu_items_arr = array(); // Assignments menu items

				foreach ( $assignment->menuItemsVal as $val ) {
					list( $menuSlug, $menuItemID ) = explode( "+", $val );
					$menu_items       = wp_get_nav_menu_items( $menuSlug );
					$menu_item        = wp_filter_object_list( $menu_items, array( 'ID' => $menuItemID ) );
					$menu_items_arr[] = reset( $menu_item );
				}

				foreach ( $menu_items_arr as $mItem ) {
					if ( $currentUrl == $mItem->url ) {
						$result = false;

						return $result;
					}
				}
				break;
		}

		return $result;
	}

	/**
	 * Plugin assignments - User Roles.
	 *
	 * @param $assignment
	 *
	 * @since 1.0.0
	 * @access protected
     *
     * @return bool|int
	 **/
	protected function UserRoles( $assignment ) {

		// If wrong input array - Ignore
		if ( ! is_array( $assignment->userRolesVal ) ) {
			$result = - 1;

			return $result;
		}

		include_once( ABSPATH . 'wp-includes/pluggable.php' );

		switch ( $assignment->userRoles ) {

			case 1: // Include
				$result = false;
				$user   = wp_get_current_user();
				foreach ( $user->roles as $role ) {
					if ( in_array( $role, $assignment->userRolesVal ) ) {
						$result = true;
					}
				}
				break;

			case 2: // Exclude
				$result = true;
				$user   = wp_get_current_user();
				foreach ( $user->roles as $role ) {
					if ( in_array( $role, $assignment->userRolesVal ) ) {
						$result = false;
					}
				}
				break;

			case 0;
			default: // Ignore
				$result = - 1;
				break;
		}

		return $result;
	}

	/**
	 * Plugin assignments - Devices.
	 *
	 * @param $assignment
	 *
	 * @return bool|int
	 * @since 1.0.0
	 * @access protected
	 */
	protected function Devices( $assignment ) {

		$detect = new MobileDetect;

		/** Detect current user device. */
		$device = 'desktop';
		if ( $detect->isTablet() ) {
			$device = 'tablet';
		}
		if ( $detect->isMobile() && ! $detect->isTablet() ) {
			$device = 'mobile';
		}

		/** If wrong input array - Ignore. */
		if ( ! is_array( $assignment->devicesVal ) ) {
			$result = - 1;

			return $result;
		}

		switch ( $assignment->devices ) {

			case 1: // Include
				$result = false;
				if ( in_array( $device, $assignment->devicesVal ) ) {
					$result = true;
				}
				break;

			case 2: // Exclude
				$result = true;
				if ( in_array( $device, $assignment->devicesVal ) ) {
					$result = false;
				}
				break;

			case 0;
			default: // Ignore
				$result = - 1;
				break;
		}

		return $result;
	}

	/**
	 * Plugin assignments - URL.
	 *
	 * @param $assignment
	 *
	 * @since 1.0.0
	 * @access protected
     *
     * @return bool|int
	 **/
	protected function URL( $assignment ) {

		// Current URL
		if ( ! isset( $_SERVER["HTTPS"] ) || ( $_SERVER["HTTPS"] != 'on' ) ) {
			$curUrl = 'http://' . $_SERVER["SERVER_NAME"];
		} else {
			$curUrl = 'https://' . $_SERVER["SERVER_NAME"];
		}
		$curUrl .= $_SERVER["REQUEST_URI"];

		$URLVal = (array) preg_split( '/\r\n|[\r\n]/', $assignment->URLVal );
		$URLVal = array_filter( $URLVal, function ( $value ) {
			if ( trim( $value ) != "" ) {
				return $value;
			}

			return null;
		} );

		switch ( $assignment->URL ) {

			case 1: // Include
				$result = false;
				if ( count( $URLVal ) == 0 ) {
					$result = false;
				} // If no URLS to include - hide widget
				foreach ( $URLVal as $u ) {
					if ( strpos( $curUrl, $u ) !== false ) {
						$result = true;
					}
				}

				break;

			case 2: // Exclude
				$result = true;
				if ( count( $URLVal ) == 0 ) {
					$result = true;
				} // If no URLS to exclude - show widget
				foreach ( $URLVal as $u ) {
					if ( strpos( $curUrl, $u ) !== false ) {
						$result = false;
					}
				}
				break;

			case 0;
			default: // Ignore
				$result = - 1;
				break;
		}

		return $result;
	}

	/**
	 * Plugin assignments - Custom PHP.
	 *
	 * @param $assignment
	 *
	 * @since 1.0.0
	 * @access protected
     *
     * @return bool|int
	 **/
	protected function PHP( $assignment ) {

		/** Replace <?php and other fix stuff. */
		$php = trim( $assignment->PHPVal );

		$p = substr( $php, 0, 5 );
		if ( strtolower( $p ) == '<?php' ) {
			$php = substr( $php, 5 );
		}

		$php = trim( $php );

		if ( $php == '' ) {
			$result = - 1;
			return $result;
		}

		$php .= ';return true;';

		/** Evaluate the script. */
		ob_start();
		$pass = (bool) eval( $php );
		ob_end_clean();

		switch ( $assignment->PHP ) {

			case 1: // Include
				$result = false;
				if ( $pass ) {
					$result = true;
				}

				break;

			case 2: // Exclude
				$result = true;
				if ( $pass ) {
					$result = false;
				}
				break;

			case 0; // Ignore
            default:
				$result = - 1;
				break;
		}

		return $result;
	}

	/**
	 * Plugin assignments - Matching Method.
	 *
	 * @param $assignment
	 * @param $wordPressContent
	 * @param $homePage
	 * @param $menuItems
	 * @param $dateTime
	 * @param $userRoles
	 * @param $URL
	 * @param $devices
	 * @param $PHP
	 *
	 * @since 1.0.0
	 * @access protected
     *
     * @return bool
	 */
	protected function MatchingMethod( $assignment, $wordPressContent, $homePage, $menuItems, $dateTime, $userRoles, $URL, $devices, $PHP ) {

		$arrCond = array(); // Add condition values

		// Ignore if -1
		if ( $wordPressContent != - 1 ) {
			$arrCond[] = $wordPressContent;
		}

		if ( $homePage != - 1 ) {
			$arrCond[] = $homePage;
		}

		if ( $menuItems != - 1 ) {
			$arrCond[] = $menuItems;
		}

		if ( $dateTime != - 1 ) {
			$arrCond[] = $dateTime;
		}

		if ( $userRoles != - 1 ) {
			$arrCond[] = $userRoles;
		}

		if ( $URL != - 1 ) {
			$arrCond[] = $URL;
		}

		if ( $devices != - 1 ) {
			$arrCond[] = $devices;
		}

		if ( $PHP != - 1 ) {
			$arrCond[] = $PHP;
		}

		if ( ! count( $arrCond ) ) {
			$arrCond[] = true;
		}

		// If all rules are Ignore - Show widget
		// Initialization
		$anytrue = false;
		$alltrue = true;

		// Processing
		foreach ( $arrCond as $v ) {
			$anytrue |= $v;
			$alltrue &= $v;
		}

		// Result
		if ( $alltrue ) {
			// All elements are TRUE
			$result = true;
		} elseif ( ! $anytrue ) {
			// All elements are FALSE
			$result = false;
		} else {
			// Mixed values
			if ( $assignment->matchingMethod == 0 ) { // ALL RULES
				$result = false;
			} else { // ANY OF RULES
				$result = true;
			}
		}

		return $result;
	}

	/**
	 * Transform a navigational menu to it's tree structure
	 *
	 * @param $menu_id
	 *
	 * @return array|null $tree
	 * @uses  buildTree()
	 * @uses  wp_get_nav_menu_items()
	 *
	 **/
	public function wpse_nav_menu_2_tree( $menu_id ) {
		$items = wp_get_nav_menu_items( $menu_id );
		return  $items ? $this->buildTree( $items, 0 ) : null;
	}

	/**
	 * Modification of "Build a tree from a flat array in PHP"
	 *
	 * Authors: @DSkinner, @ImmortalFirefly and @SteveEdson
	 *
	 * @link https://stackoverflow.com/a/28429487/2078474
	 **/
	public function buildTree( array &$elements, $parentId = 0 ) {
		$branch = [];
		foreach ( $elements as &$element )
		{
			if ( $element->menu_item_parent == $parentId )
			{
				$children = $this->buildTree( $elements, $element->ID );
				if ( $children )
					$element->wpse_children = $children;

				$branch[$element->ID] = $element;
				unset( $element );
			}
		}
		return $branch;
	}

	/**
	 * Output options list for select.
	 *
	 * @param $arr
	 * @param $menuslug
	 * @param int $level
	 * @since 1.0.0
     *
     * @return void
	 **/
	public function printMenuTree( $arr, $menuslug, $level = 0 ) {
		foreach ( $arr as $item ) {
			?>
            <option
            value="<?php echo esc_attr( $menuslug . "+" . $item->ID ); ?>"><?php echo str_repeat( "-", $level ) . " " . $item->title . " (" . $item->type_label . ")"; ?></option><?php
			if ( count( $item->wpse_children ) ) {
				$this->printMenuTree( $item->wpse_children, $menuslug, $level + 1 );
			}
		}
	}

	/**
	 * Get current query information.
	 *
	 * @return string[]
	 * @global \WP_Query $wp_query
	 *
	 **/
	public function getQuery() {
		global $wp_query;

		// create, if not set
		if ( empty( $this->query ) ) {

			// init vars
			$obj   = $wp_query->get_queried_object();
			$type  = get_post_type();
			$query = array();

			if ( is_home() ) {
				$query[] = 'home';
			}

			if ( is_front_page() ) {
				$query[] = 'front_page';
			}

			if ( $type == 'post' ) {

				if ( is_single() ) {
					$query[] = 'single';
				}

				if ( is_archive() ) {
					$query[] = 'archive';
				}
			} else {
				if ( is_single() ) {
					$query[] = $type . '-single';
				} elseif ( is_archive() ) {
					$query[] = $type . '-archive';
				}
			}

			if ( is_search() ) {
				$query[] = 'search';
			}

			if ( is_page() ) {
				$query[] = $type;
				$query[] = $type . '-' . $obj->ID;
			}

			if ( is_category() ) {
				$query[] = 'cat-' . $obj->term_id;
			}

			// WooCommerce
			include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {

				/** @noinspection PhpUndefinedFunctionInspection */
				if ( is_shop() && ! is_search() ) {
					$query[] = 'page';
					/** @noinspection PhpUndefinedFunctionInspection */
					$query[] = 'page-' . wc_get_page_id( 'shop' );
				}

				/** @noinspection PhpUndefinedFunctionInspection */
				if ( is_product_category() || is_product_tag() ) {
					$query[] = 'cat-' . $obj->term_id;
				}
			}

			/** @noinspection PhpUndefinedFieldInspection */
			$this->query = $query;
		}

		return $this->query;
	}

	/**
	 * Main AssignmentsTab Instance.
	 *
	 * Insures that only one instance of AssignmentsTab exists in memory at any one time.
	 *
	 * @static
	 * @return AssignmentsTab
	 * @since 1.0.0
	 **/
	public static function get_instance() {
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof AssignmentsTab ) ) {
			self::$instance = new AssignmentsTab;
		}

		return self::$instance;
	}

} // End Class AssignmentsTab.