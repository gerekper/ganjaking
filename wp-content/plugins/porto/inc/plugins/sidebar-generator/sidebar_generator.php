<?php
/*
Plugin Name: Sidebar Generator
Plugin URI: http://www.getson.info
Description: This plugin generates as many sidebars as you need. Then allows you to place them on any page you wish. Version 1.1 now supports themes with multiple sidebars.
Version: 1.1.0
Author: Kyle Getson
Author URI: http://www.kylegetson.com
Copyright (C) 2009 Kyle Robert Getson
*/

/*
Copyright (C) 2009 Kyle Robert Getson, kylegetson.com and getson.info

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

class sidebar_generator {

	public function __construct() {
		add_action( 'widgets_init', array( 'sidebar_generator', 'init' ) );
		add_action( 'admin_menu', array( 'sidebar_generator', 'admin_menu' ) );
		add_action( 'admin_enqueue_scripts', array( 'sidebar_generator', 'admin_enqueue_scripts' ) );
		add_action( 'admin_print_scripts', array( 'sidebar_generator', 'admin_print_scripts' ) );
		if ( current_user_can( 'manage_options' ) ) {
			add_action( 'wp_ajax_add_sidebar', array( 'sidebar_generator', 'add_sidebar' ) );
			add_action( 'wp_ajax_remove_sidebar', array( 'sidebar_generator', 'remove_sidebar' ) );
		}

		//edit posts/pages
		//add_action('edit_form_advanced', array('sidebar_generator', 'edit_form'));
		//add_action('edit_page_form', array('sidebar_generator', 'edit_form'));

		//save posts/pages
		add_action( 'edit_post', array( 'sidebar_generator', 'save_form' ) );
		add_action( 'publish_post', array( 'sidebar_generator', 'save_form' ) );
		add_action( 'save_post', array( 'sidebar_generator', 'save_form' ) );
		add_action( 'edit_page_form', array( 'sidebar_generator', 'save_form' ) );

	}

	public static function init() {
		//go through each sidebar and register it
		$sidebars = sidebar_generator::get_sidebars();

		if ( is_array( $sidebars ) ) {
			foreach ( $sidebars as $sidebar ) {
				$sidebar_class = sidebar_generator::name_to_class( $sidebar );
				register_sidebar(
					array(
						'name'          => $sidebar,
						'id'            => 'porto-custom-sidebar-' . strtolower( $sidebar_class ),
						'before_widget' => '<aside id="%1$s" class="widget sbg_widget ' . $sidebar_class . ' %2$s">',
						'after_widget'  => '</aside>',
						'before_title'  => '<h3 class="widget-title sbg_title">',
						'after_title'   => '</h3>',
					)
				);
			}
		}
	}

	public static function admin_enqueue_scripts() {
		wp_enqueue_script( array( 'sack' ) );
	}

	public static function admin_print_scripts() {
		?>
		<script>
			function getParentByTagName(obj, tag)
			{
				var obj_parent = obj.parentNode;
				if (!obj_parent) return false;
				if (obj_parent.tagName.toLowerCase() == tag) return obj_parent;
				else return getParentByTagName(obj_parent, tag);
			}

			function add_sidebar( sidebar_name )
			{
				var mysack = new sack("<?php echo site_url(); ?>/wp-admin/admin-ajax.php" );

				mysack.execute = 1;
				mysack.method = 'POST';
				mysack.setVar( "action", "add_sidebar" );
				mysack.setVar( "sidebar_name", sidebar_name );
				mysack.setVar( "sidebar_generator_nonce", document.getElementById('sidebar_generator_nonce').value );
				mysack.encVar( "cookie", document.cookie, false );
				mysack.onError = function() { alert('Ajax error. Cannot add sidebar' )};
				mysack.runAJAX();
				return true;
			}

			function remove_sidebar( elem, sidebar_name )
			{
				var parent = getParentByTagName(elem, 'tr');
				var num = parent.rowIndex;
				var mysack = new sack("<?php echo site_url(); ?>/wp-admin/admin-ajax.php" );

				mysack.execute = 1;
				mysack.method = 'POST';
				mysack.setVar( "action", "remove_sidebar" );
				mysack.setVar( "sidebar_name", sidebar_name );
				mysack.setVar( "sidebar_generator_nonce", document.getElementById('sidebar_generator_nonce').value );
				mysack.setVar( "row_number", num );
				mysack.encVar( "cookie", document.cookie, false );
				mysack.onError = function() { alert('Ajax error. Cannot add sidebar' )};
				mysack.runAJAX();
				//alert('hi!:::'+sidebar_name);
				return true;
			}
		</script>
		<?php
	}

	public static function add_sidebar() {
		check_admin_referer( 'sidebar_generator', 'sidebar_generator_nonce' );
		$sidebars = sidebar_generator::get_sidebars();
		$name     = str_replace( array( "\n", "\r", "\t" ), '', sanitize_text_field( $_POST['sidebar_name'] ) );
		if ( ! $name || $name == 'null' ) {
			die( "alert('Please input sidebar name and try again.')" );
		}
		$id = sidebar_generator::name_to_class( $name );
		if ( isset( $sidebars[ $id ] ) ) {
			die( "alert('Sidebar already exists, please use a different name.')" );
		}

		$sidebars[ $id ] = $name;
		sidebar_generator::update_sidebars( $sidebars );

		$js = "
			var tbl = document.getElementById('sbg_table');
			var lastRow = tbl.rows.length;
			// if there's no header row in the table, then iteration = lastRow + 1
			var iteration = lastRow;
			var row = tbl.insertRow(lastRow);

			// left cell
			var cellLeft = row.insertCell(0);
			var textNode = document.createTextNode('$name');
			cellLeft.appendChild(textNode);

			//middle cell
			var cellLeft = row.insertCell(1);
			var textNode = document.createTextNode('$id');
			cellLeft.appendChild(textNode);

			//var cellLeft = row.insertCell(2);
			//var textNode = document.createTextNode('[<a href=\'javascript:void(0);\' onclick=\'return remove_sidebar_link(this,$name);\'>Remove</a>]');
			//cellLeft.appendChild(textNode)

			var cellLeft = row.insertCell(2);
			removeLink = document.createElement('a');
			linkText = document.createTextNode('remove');
			removeLink.setAttribute('onclick', 'remove_sidebar_link(this,\'$name\')');
			removeLink.setAttribute('href', 'javascript:void(0)');

			removeLink.appendChild(linkText);
			cellLeft.appendChild(removeLink);

			var sbg_noexist = document.getElementById('sbg_noexist');
			if (sbg_noexist)
				sbg_noexist.style.display = 'none';
		";

		die( "$js" );
	}

	public static function remove_sidebar() {
		check_admin_referer( 'sidebar_generator', 'sidebar_generator_nonce' );
		$sidebars = sidebar_generator::get_sidebars();
		$name     = str_replace( array( "\n", "\r", "\t" ), '', sanitize_text_field( $_POST['sidebar_name'] ) );
		$id       = sidebar_generator::name_to_class( $name );
		if ( ! isset( $sidebars[ $id ] ) ) {
			die( "alert('Sidebar does not exist.')" );
		}
		$row_number = (int) $_POST['row_number'];
		unset( $sidebars[ $id ] );
		sidebar_generator::update_sidebars( $sidebars );
		$js = "
			var tbl = document.getElementById('sbg_table');
			tbl.deleteRow($row_number);
		";
		die( $js );
	}

	public static function admin_menu() {
		add_theme_page( 'Sidebars', 'Sidebars', 'manage_options', 'multiple_sidebars', array( 'sidebar_generator', 'admin_page' ) );
	}

	public static function admin_page() {
		?>
		<script>
			function remove_sidebar_link(elem,name){
				answer = confirm("Are you sure you want to remove " + name + "?\nThis will remove any widgets you have assigned to this sidebar.");
				if(answer){
					//alert('AJAX REMOVE');
					remove_sidebar(elem,name);
				}else{
					return false;
				}
			}
			function add_sidebar_link(){
				var sidebar_name = prompt("Sidebar Name:","");
				if (null !== sidebar_name) {
					add_sidebar(sidebar_name);
				}
			}
		</script>
		<div class="wrap">
			<h2>Sidebar Generator</h2>
			<p>
				The sidebar name is for your use only. It will not be visible to any of your visitors.
				A CSS class is assigned to each of your sidebar, use this styling to customize the sidebars.
			</p>
			<br />
			<div class="add_sidebar">
				<a href="javascript:void(0);" onclick="return add_sidebar_link()" title="Add a sidebar" class="button-primary">+ Add Sidebar</a>
			</div>
			<br />
			<table class="widefat page" id="sbg_table" style="width:600px;">
				<tr>
					<th>Name</th>
					<th>CSS class</th>
					<th>Remove</th>
				</tr>
				<?php
				$sidebars = sidebar_generator::get_sidebars();
				//$sidebars = array('bob','john','mike','asdf');
				if ( is_array( $sidebars ) && ! empty( $sidebars ) ) {
					$cnt = 0;
					foreach ( $sidebars as $sidebar ) {
						$alt = ( $cnt % 2 == 0 ? 'alternate' : '' );
						?>
						<tr class="<?php echo esc_attr( $alt ); ?>">
							<td><?php echo esc_html( $sidebar ); ?></td>
							<td><?php echo esc_html( sidebar_generator::name_to_class( $sidebar ) ); ?></td>
							<td><a href="javascript:void(0);" onclick="return remove_sidebar_link(this,'<?php echo esc_attr( $sidebar ); ?>');" title="Remove this sidebar">remove</a></td>
						</tr>
						<?php
						$cnt++;
					}
				} else {
					?>
					<tr id="sbg_noexist">
						<td colspan="3">No Sidebars defined</td>
					</tr>
					<?php
				}
				?>
			</table>
			<br /><br />
			<?php wp_nonce_field( 'sidebar_generator', 'sidebar_generator_nonce' ); ?>
		</div>
		<?php
	}

	/**
	 * for saving the pages/post
	 */
	public static function save_form( $post_id ) {
		if ( ! isset( $_POST['sbg_edit'] ) ) {
			return;
		}

		if ( ! empty( $_POST['sbg_edit'] ) ) {
			delete_post_meta( $post_id, 'sbg_selected_sidebar' );
			delete_post_meta( $post_id, 'sbg_selected_sidebar_replacement' );
			add_post_meta( $post_id, 'sbg_selected_sidebar', porto_sanitize_array( $_POST['sidebar_generator'] ) );
			add_post_meta( $post_id, 'sbg_selected_sidebar_replacement', porto_sanitize_array( $_POST['sidebar_generator_replacement'] ) );
		}
	}

	public static function edit_form() {
		global $post;
		$post_id = $post;
		if ( is_object( $post_id ) ) {
			$post_id = $post_id->ID;
		}
		$selected_sidebar = get_post_meta( $post_id, 'sbg_selected_sidebar', true );
		if ( ! is_array( $selected_sidebar ) ) {
			$tmp                 = $selected_sidebar;
			$selected_sidebar    = array();
			$selected_sidebar[0] = $tmp;
		}
		$selected_sidebar_replacement = get_post_meta( $post_id, 'sbg_selected_sidebar_replacement', true );
		if ( ! is_array( $selected_sidebar_replacement ) ) {
			$tmp                             = $selected_sidebar_replacement;
			$selected_sidebar_replacement    = array();
			$selected_sidebar_replacement[0] = $tmp;
		}
		?>

		<div id='sbg-sortables' class='meta-box-sortables'>
			<div id="sbg_box" class="postbox " >
				<div class="handlediv" title="Click to toggle"><br /></div><h3 class='hndle'><span>Sidebars</span></h3>
				<div class="inside">
					<div class="sbg_container">
						<input name="sbg_edit" type="hidden" value="sbg_edit" />

						<p>
							Please select the sidebar you would like to display. <strong>Note:</strong> You must first create the sidebar under <strong>Appearance > Sidebars</strong>.
						</p>
						<ul>
							<?php
							global $wp_registered_sidebars;
							for ( $i = 0;$i < 1;$i++ ) {
								?>
								<li>
									<select name="sidebar_generator[<?php echo (int) $i; ?>]" style="display: none;">
										<option value="0"
										<?php
										if ( $selected_sidebar[ $i ] == '' ) {
											echo ' selected';}
										?>
										>WP Default Sidebar</option>
										<?php
										$sidebars = $wp_registered_sidebars;// sidebar_generator::get_sidebars();
										if ( is_array( $sidebars ) && ! empty( $sidebars ) ) {
											foreach ( $sidebars as $sidebar ) {
												if ( $selected_sidebar[ $i ] == $sidebar['name'] ) {
													echo "<option value='" . esc_attr( $sidebar['name'] ) . "' selected>" . esc_html( $sidebar['name'] ) . "</option>\n";
												} else {
													echo "<option value='" . esc_attr( $sidebar['name'] ) . "'>" . esc_html( $sidebar['name'] ) . "</option>\n";
												}
											}
										}
										?>
									</select>
									<select name="sidebar_generator_replacement[<?php echo (int) $i; ?>]">
										<option value="0"
										<?php
										if ( $selected_sidebar_replacement[ $i ] == '' ) {
											echo ' selected';}
										?>
										>None</option>
										<?php

										$sidebar_replacements = $wp_registered_sidebars;//sidebar_generator::get_sidebars();
										if ( is_array( $sidebar_replacements ) && ! empty( $sidebar_replacements ) ) {
											foreach ( $sidebar_replacements as $sidebar ) {
												if ( $selected_sidebar_replacement[ $i ] == $sidebar['name'] ) {
													echo "<option value='" . esc_attr( $sidebar['name'] ) . "' selected>" . esc_html( $sidebar['name'] ) . "</option>\n";
												} else {
													echo "<option value='" . esc_attr( $sidebar['name'] ) . "'>" . esc_html( $sidebar['name'] ) . "</option>\n";
												}
											}
										}
										?>
									</select>

								</li>
							<?php } ?>
						</ul>
					</div>
				</div>
			</div>
		</div>

		<?php
	}

	/**
	 * called by the action get_sidebar. this is what places this into the theme
	 */
	public static function get_sidebar( $name = '0' ) {
		if ( ! is_singular() ) {
			if ( $name != '0' ) {
				dynamic_sidebar( $name );
			} else {
				dynamic_sidebar();
			}
			return;//dont do anything
		}
		wp_reset_query();
		global $wp_query;
		$post                         = $wp_query->get_queried_object();
		$selected_sidebar             = get_post_meta( $post->ID, 'sbg_selected_sidebar', true );
		$selected_sidebar_replacement = get_post_meta( $post->ID, 'sbg_selected_sidebar_replacement', true );
		$did_sidebar                  = false;
		//this page uses a generated sidebar
		if ( $selected_sidebar != '' && $selected_sidebar != '0' ) {
			echo "\n\n<!-- begin generated sidebar -->\n";
			if ( is_array( $selected_sidebar ) && ! empty( $selected_sidebar ) ) {
				for ( $i = 0;$i < sizeof( $selected_sidebar );$i++ ) {

					if ( $name == '0' && $selected_sidebar[ $i ] == '0' && $selected_sidebar_replacement[ $i ] == '0' ) {
						//echo "\n\n<!-- [called $name selected {$selected_sidebar[$i]} replacement {$selected_sidebar_replacement[$i]}] -->";
						dynamic_sidebar();//default behavior
						$did_sidebar = true;
						break;
					} elseif ( $name == '0' && $selected_sidebar[ $i ] == '0' ) {
						//we are replacing the default sidebar with something
						//echo "\n\n<!-- [called $name selected {$selected_sidebar[$i]} replacement {$selected_sidebar_replacement[$i]}] -->";
						dynamic_sidebar( $selected_sidebar_replacement[ $i ] );//default behavior
						$did_sidebar = true;
						break;
					} elseif ( $selected_sidebar[ $i ] == $name ) {
						//we are replacing this $name
						//echo "\n\n<!-- [called $name selected {$selected_sidebar[$i]} replacement {$selected_sidebar_replacement[$i]}] -->";
						$did_sidebar = true;
						dynamic_sidebar( $selected_sidebar_replacement[ $i ] );//default behavior
						break;
					}
					//echo "<!-- called=$name selected={$selected_sidebar[$i]} replacement={$selected_sidebar_replacement[$i]} -->\n";
				}
			}
			if ( $did_sidebar == true ) {
				echo "\n<!-- end generated sidebar -->\n\n";
				return;
			}
			//go through without finding any replacements, lets just send them what they asked for
			if ( $name != '0' ) {
				dynamic_sidebar( $name );
			} else {
				dynamic_sidebar();
			}
			echo "\n<!-- end generated sidebar -->\n\n";
			return;
		} else {
			if ( $name != '0' ) {
				dynamic_sidebar( $name );
			} else {
				dynamic_sidebar();
			}
		}
	}

	/**
	 * replaces array of sidebar names
	 */
	public static function update_sidebars( $sidebar_array ) {
		$sidebars = update_option( 'sbg_sidebars', $sidebar_array );
	}

	/**
	 * gets the generated sidebars
	 */
	public static function get_sidebars() {
		$sidebars = get_option( 'sbg_sidebars' );
		return $sidebars;
	}
	public static function name_to_class( $name ) {
		$class = str_replace( array( ' ', ',', '.', '"', "'", '/', '\\', '+', '=', ')', '(', '*', '&', '^', '%', '$', '#', '@', '!', '~', '`', '<', '>', '?', '[', ']', '{', '}', '|', ':' ), '', $name );
		return $class;
	}

}
$sbg = new sidebar_generator;

function generated_dynamic_sidebar( $name = '0' ) {
	sidebar_generator::get_sidebar( $name );
	return true;
}
?>
