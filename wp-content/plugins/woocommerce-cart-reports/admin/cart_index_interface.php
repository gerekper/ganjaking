<?php
/*
* interfaces.php - cart list page modifications
*
*
*/

class AV8_Cart_Index_Page {

	public function __construct() {
		global $start_date, $end_date;
		global $pagenow, $typenow;
		global $woocommerce;

		$current_month = date( 'j/n/Y', mktime( 0, 0, 0, 1, date( 'm' ), date( 'Y' ) ) );

		$start_date = ( isset( $_GET['start_date'] ) ) ? $_GET['start_date'] : '';
		$end_date   = ( isset( $_GET['end_date'] ) ) ? $_GET['end_date'] : '';

		if ( ! $start_date ) {
			$start_date = $current_month;
		}
		if ( ! $end_date ) {
			$end_date = date( 'Ymd', current_time( 'timestamp' ) );
		}

		$start_date = strtotime( $start_date );
		$end_date   = strtotime( $end_date );
		add_action( 'admin_menu', array( $this, 'hide_add_new_carts' ) );
		add_action(
			'views_edit-carts',
			array( $this, 'av8_remove_cart_views' )
		); //Remove the All / Published / Trash view.
		add_action( 'manage_carts_posts_custom_column', array( $this, 'av8_manage_cart_columns' ), 1, 1 );
		add_action( 'restrict_manage_posts', array( $this, 'author_filter' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_index' ) );
		add_filter( 'restrict_manage_posts', array( $this, 'restrict_manage_posts' ), 1000 );
		add_action( 'pre_get_posts', array( $this, 'exclude_category' ) );
		add_filter( 'posts_where', array( $this, 'filter_where' ) );
		add_filter( 'manage_edit-carts_columns', array( $this, 'av8_carts_columns' ) );
		add_filter( 'manage_edit-carts_sortable_columns', array( $this, 'av8_carts_sort' ) );
		add_filter( 'request', array( $this, 'cart_column_orderby' ) );
		add_filter( 'bulk_actions-' . 'edit-carts', '__return_empty_array' ); //Remove bulk edit
		add_filter( 'parse_query', array( $this, 'woocommerce_carts_search_custom_fields' ) );
		add_filter( 'get_search_query', array( $this, 'woocommerce_carts_search_label' ) );
		add_filter( 'post_row_actions', array( $this, 'remove_post_actions' ), 1, 2 );
	}

	/*
	* Include require init scripts for the index page.
	*/

	public function woocommerce_carts_search_label( $query ) {
		global $pagenow, $typenow;

		if ( 'edit.php' != $pagenow ) {
			return $query;
		}
		if ( $typenow != 'carts' ) {
			return $query;
		}
		if ( ! get_query_var( 'cart_search' ) ) {
			return $query;
		}

		return $_GET['s'];
	}

	public function woocommerce_carts_search_custom_fields( $wp ) {
		global $pagenow, $wpdb;

		if ( 'edit.php' != $pagenow ) {
			return $wp;
		}
		if ( ! isset( $wp->query_vars['s'] ) || ! $wp->query_vars['s'] ) {
			return $wp;
		}
		if ( $wp->query_vars['post_type'] != 'carts' ) {
			return $wp;
		}

		$search_fields = array(
			'av8_cartitems'
		);

		// Query matching custom fields - this seems faster than meta_query
		$post_ids = $wpdb->get_col(
			$wpdb->prepare(
				'SELECT post_id FROM ' . $wpdb->postmeta . ' WHERE meta_key IN (' . '"' . implode(
					'","',
					$search_fields
				) . '"' . ') AND meta_value LIKE "%%%s%%"',
				esc_attr( $_GET['s'] )
			)
		);
		// Query matching excerpts and titles
		$post_ids = array_merge(
			$post_ids,
			$wpdb->get_col(
				$wpdb->prepare(
					'
		SELECT ' . $wpdb->posts . '.ID
		FROM ' . $wpdb->posts . '
		LEFT JOIN ' . $wpdb->postmeta . ' ON ' . $wpdb->posts . '.ID = ' . $wpdb->postmeta . '.post_id
		LEFT JOIN ' . $wpdb->users . ' ON ' . $wpdb->postmeta . '.meta_value = ' . $wpdb->users . '.ID
		WHERE
			post_excerpt 	LIKE "%%%1$s%%" OR
			post_title 		LIKE "%%%1$s%%" OR
			user_login		LIKE "%%%1$s%%" OR
			user_nicename	LIKE "%%%1$s%%" OR
			user_email		LIKE "%%%1$s%%" OR
			display_name	LIKE "%%%1$s%%"
		',
					esc_attr( $_GET['s'] )
				)
			)
		);

		// Add ID
		$search_order_id = str_replace( 'Order #', '', $_GET['s'] );
		if ( is_numeric( $search_order_id ) ) {
			$post_ids[] = $search_order_id;
		}

		// Add blank ID so not all results are returned if the search finds nothing
		$post_ids[] = 0;

		// Remove s - we don't want to search order name
		unset( $wp->query_vars['s'] );

		// so we know we're doing this
		$wp->query_vars['cart_search'] = true;

		// Search by found posts
		$wp->query_vars['post__in'] = $post_ids;
	}

	public function enqueue_index() {
		global $pagenow;
		global $woocommerce;

		if ( $pagenow == 'edit.php' && isset( $_GET['post_type'] ) && $_GET['post_type'] == 'carts' ) {
			wp_enqueue_script( 'woocommerce_admin' );
			wp_enqueue_script( 'jquery' );
			wp_enqueue_script( 'woocommerce_admin' );
			wp_enqueue_style( 'woocommerce_admin_styles', $woocommerce->plugin_url() . '/assets/css/admin.css' );
			wp_enqueue_style(
				'jquery-ui-style',
				( is_ssl(
				) ) ? 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css' : 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css'
			);
			wp_enqueue_style(
				'woocommerce_cart_report_admin_index_css',
				plugins_url() . '/woocommerce-cart-reports/assets/css/cart_reports_admin_index.css'
			);
			wp_enqueue_script( 'jquery-ui-datepicker' );
		}
	}

	/*
	*
	* hide_add_new_carts()
	*
	* Hide the "New Carts" link
	*/

	public function hide_add_new_carts() {
		global $submenu;
		// replace my_type with the name of your post type
		unset( $submenu['edit.php?post_type=carts'][10] );
	}

	/*
	*
	* av8_carts_columns( $columns )
	*
	* Rename Columns for the new "Cart" post type
	*/

	public function av8_carts_columns( $columns ) {
		global $woocommerce_cart_reports_options;


		// Remove default fields.

		unset( $columns['author'] );
		unset( $columns['date'] );
		unset( $columns['cb'] );

		if ( $woocommerce_cart_reports_options['productsindex'] == false ) {
			$columns['cartname'] = __( 'Owner', 'woocommerce_cart_reports' );
			$columns['post__in'] = __( 'Cart Status', 'woocommerce_cart_reports' );
			$columns['updated']  = __( 'Last Online', 'woocommerce_cart_reports' );
			$columns['actions']  = __( 'Actions', 'woocommerce_cart_reports' );
		} else {
			$columns['cartname'] = __( 'Owner', 'woocommerce_cart_reports' );
			$columns['post__in'] = __( 'Cart Status', 'woocommerce_cart_reports' );
			$columns['updated']  = __( 'Last Online', 'woocommerce_cart_reports' );
			$columns['products'] = __( 'Products', 'woocommerce_cart_reports' );
			$columns['actions']  = __( 'Actions', 'woocommerce_cart_reports' );
		}

		return $columns;
	}


	public function remove_post_actions( $actions, $post ) {
		if ( $post->post_type == 'carts' ) {
			return array();
		} else {
			return $actions;
		}
	}

	/*
	*
	* my_edit_carts_columns( $columns )
	*
	* Declare our new columns as sortable columns (except the action column, for obvious reasons)\
	*
	*/

	public function av8_carts_sort( $columns ) {
		$custom = array(
			'updated' => 'post_modified',
		);

		return wp_parse_args( $custom, $columns );
	}

	/*
	*
	* cart_column_orderby( $vars )
	*
	* Hook for the actual sorting on the custom columns (when the post request comes back)
	*
	*/

	public function cart_column_orderby( $vars ) {
		global $pagenow;
		if ( $pagenow == 'edit.php' && isset( $_GET['post_type'] ) && $_GET['post_type'] == 'carts' ) {
			if ( ! isset( $vars['orderby'] ) && ! isset( $vars['order'] ) ) {
				$vars['orderby'] = 'post_modified';
				$vars['order']   = 'DESC';
			}
		}

		return $vars;
	}

	/*
	*
	* av8_remove_cart_views( $views )
	*
	* Remove drag-over action items on carts page
	*
	*/

	public function av8_remove_cart_views( $views ) {
		unset( $views['all'] );
		unset( $views['publish'] );
		unset( $views['trash'] );

		return $views;
	}

	/*
	*
	* av8_manage_cart_columns( $column, $post_id )
	*
	* Add cases for our custom columns (status, updated, actions)
	*
	*/

	public function av8_manage_cart_columns( $column, $post_id = '' ) {
		global $post;
		$cart = new AV8_Cart_Receipt();
		$cart->load_receipt( $post->ID );
		$cart->set_guest_details();
		$title = '';

		switch ( $column ) {

			case 'cartname':
				if ( $cart->is_guest_order() && $cart->has_guest_details() ) {
					$fullname = ucwords( $cart->get_guest_details( 'billing_first_name' ) ) . ' ' . ucwords(
							$cart->get_guest_details( 'billing_last_name' )
						);

					if ( $fullname != ' ' ) {
						$title .= $fullname . ' (' . __( 'Guest', 'woocommerce_cart_reports' ) . ')';
					} else {
						$title .= __( 'Guest', 'woocommerce_cart_reports' );
					}
				} elseif ( $cart->is_guest_order() && 'Converted' === $cart->status() && isset( $cart->order ) ) {
					if ( version_compare( WC_VERSION, '3.0', '<' ) ) {
						$title = ucwords( $cart->order->billing_first_name ) . ' ' . ucwords(
								$cart->order->billing_last_name
							) . ' (Guest)';
					} else {
						$title = ucwords( $cart->order->get_billing_first_name() ) . ' ' . ucwords(
								$cart->order->get_billing_last_name()
							) . ' (Guest)';
					}
				} elseif ( $cart->is_guest_order() ) {
					$title = __( 'Guest', 'woocommerce_cart_reports' );
				} elseif ( $cart->full_name() != false ) {
					$title = ucwords( $cart->full_name() );
				}
				$post_url = admin_url( 'post.php?post=' . $post->ID . '&action=edit' );
				echo __( "<a href='$post_url'>" . $title . '</a>' );
				break;

			case 'post__in':
				/* Get the post meta. */ $show_custom_state = $cart->status();
				$filter_link                                = admin_url(
					'edit.php?post_type=carts&status=' . $show_custom_state
				);
				echo __(
					'<div class="index_status"><mark class="' . strtolower( $show_custom_state ) . '_index">' . __(
						$show_custom_state,
						'woocommerce_cart_reports'
					) . '</mark></div>'
				);
				break;

			case 'updated':
				/* Get the genres for the post. */ the_modified_date( 'F j, Y' );
				echo ' at ';
				the_modified_date( 'g:i a' );
				break;


			case 'products':
				//$products = $this->extract_cart_products();
				global $woocommerce;

				$order_items = get_post_meta( $post->ID, 'av8_cartitems', true );

				if ( ! is_array( $order_items ) ) {
					$items_arr = str_replace( array( 'O:17:"WC_Product_Simple"', 'O:10:"WC_Product"' ),
						'O:8:"stdClass"',
						$order_items );

					if ( isset( $order_items ) && $order_items != false ) {
						$order_items = (array) maybe_unserialize( $items_arr );
					}
				}

				$loop = 0;

				if ( count( $order_items ) > 0 && $order_items != false ) {
					foreach ( $order_items as $item ) {
						if ( isset( $item['variation_id'] ) && $item['variation_id'] > 0 ) {
							$_product = wc_get_product( $item['variation_id'] );
						} else {
							$_product = wc_get_product( $item['product_id'] );
						}

						if ( isset( $_product ) && $_product != false ) {
							$markup = "<a href='" . get_admin_url(
									'',
									'post.php?post=' . $item['product_id'] . '&action=edit'
								) . "'>" . $_product->get_title() . '</a>';

							$variation_id   = $_product->get_id();
							$variation_data = $variation_id ? wc_get_formatted_variation(
								wc_get_product_variation_attributes( $variation_id ),
								true
							) : '';

							if ( ! empty( $variation_data ) ) {
								$markup .= '&nbsp;' . $variation_data;
							}

							if ( $item['quantity'] > 1 ) {
								$markup .= ' x' . $item['quantity'];
							}

							echo $markup;
						}
						if ( $loop < count( $order_items ) - 1 ) {
							echo ', ';
						}

						$loop ++;
					}
				} else {
					echo "<span style='color:lightgray;'>" . __(
							'No Products',
							'woocommerce_cart_reports'
						) . '</span>';
				}
				break;

			case 'actions':
				$cart->print_cart_actions();
				break;

			/* Just break out of the switch statement for everything else. */

			default:
				break;
		}
	}

	/*
	*
	* Print Available cart actions
	*
	*/
	public function restrict_manage_posts() {
		global $pagenow;
		global $woocommerce;

		if ( ( $pagenow == 'edit.php' ) && isset( $_GET['post_type'] ) && ( $_GET['post_type'] == 'carts' ) ) {
			$status_options = array(
				'Open'                   => 'Open',
				'Converted'              => 'Converted',
				'Abandoned'              => 'Abandoned',
				'Open + Abandoned Carts' => 'OandA'
			);
			global $start_date, $end_date, $woocommerce, $wpdb, $wp_locale;

			//Check to see if "lifetime" is set, and if it is set, find the date of the oldest post and set the start date to that date.

			if ( isset( $_GET['lifetime'] ) || ! isset( $_GET['mv'] ) ) {
				$args = array(
					'numberposts' => 1,
					'offset'      => 0,
					'orderby'     => 'post_modified',
					'order'       => 'ASC',
					'post_type'   => 'carts',
					'post_status' => 'publish',
				);

				$post = get_posts( $args );

				if ( isset( $post[0] ) ) {
					$start_date = strtotime( $post[0]->post_modified );
				}
			} ?>

			<label for="from">
				<?php _e( 'From:', 'woocommerce_cart_reports' ); ?>
			</label>
			<input type="text" name="start_date" id="from" readonly="readonly" value="
			<?php
			echo esc_attr( date( 'Y-m-d', $start_date ) );
			?>
			"
			/>
			<label for="to">
				<?php _e( 'To:', 'woocommerce_cart_reports' ); ?>
			</label>
			<input type="text" name="end_date" id="to" readonly="readonly" value="
			<?php
			echo esc_attr( date( 'Y-m-d', $end_date ) );
			?>
			"
			/>
			<script type="text/javascript">
              jQuery(function () {
                var dates = jQuery('#posts-filter #from, #posts-filter #to').datepicker({
                  defaultDate: '', dateFormat: 'yy-mm-dd', numberOfMonths: 1, maxDate: '+0D', showButtonPanel: true, showOn: 'both', buttonImage: "<?php echo $woocommerce->plugin_url(); ?>/assets/images/calendar.png", buttonImageOnly: true, onSelect: function (selectedDate) {
                    var option = this.id == 'from' ? 'minDate' : 'maxDate', instance = jQuery(this).data('datepicker'), date = jQuery.datepicker.parseDate(instance.settings.dateFormat || jQuery.datepicker._defaults.dateFormat, selectedDate, instance.settings)
                    dates.not(this).datepicker('option', option, date)
                  }
                })
              })
			</script>
			<select name="mv">
				<option value="">
					<?php echo __( 'Show All Carts', 'woocommerce_cart_reports' ); ?>
				</option>
				<?php
				foreach ( $status_options as $key => $value ) {
					?>
					<option value="<?php echo esc_attr( $value ); ?>"
						<?php
						if ( isset( $_GET['mv'] ) ) {
							selected( $_GET['mv'], $value );
						}
						?>
					>
						<?php echo __( esc_attr( $key ), 'woocommerce_cart_reports' ); ?>
					</option>
					<?php
				}
				?>
			</select>

			<?php
		}
	}

	public function exclude_category( $query ) {
		global $wpdb;
		global $woocommerce_cart_reports_options;
		if ( isset( $query->query_vars['post_type'] ) && 'carts' === $query->query_vars['post_type'] && isset( $_GET['mv'] ) && $_GET['mv'] != '' ) {
			if ( $_GET['mv'] == 'Converted' ) {
				$query->set( 'tax_query',
					array(
						array(
							'taxonomy' => 'shop_cart_status',
							'field'    => 'slug',
							'terms'    => 'converted'
						)
					) );
			} else {
				$query->set( 'tax_query',
					array(
						array(
							'taxonomy' => 'shop_cart_status',
							'field'    => 'slug',
							'terms'    => 'open',
						)
					) );
			}
		}
	}

	/**
	 * Replace the stock author dropdown to use customers' real names, and use billing info if available from a recent purchase instead of the built int first_name and last_name fields. Also add "Guest" to the list.
	 *
	 */
	public function author_filter() {
		global $woocommerce;
		global $pagenow;
		if ( isset( $_GET['post_type'] ) ) {
			if ( ( $pagenow == 'edit.php' ) && ( $_GET['post_type'] == 'carts' ) ) {
				$args = array(
					'name'            => 'author',
					'show_option_all' => __( 'Show All Customers', 'woocommerce_cart_reports' )
				);
				if ( isset( $_GET['user'] ) ) {
					$args['selected'] = $_GET['user'];
				}


				$this->wp_dropdown_users( $args );
			}
		}
	}

	/**
	 *
	 *
	 */
	public function wp_dropdown_users( $args = '' ) {
		$defaults = array(
			'show_option_all'         => '',
			'show_option_none'        => '',
			'hide_if_only_one_author' => '',
			'orderby'                 => 'display_name',
			'order'                   => 'ASC',
			'include'                 => '',
			'exclude'                 => '',
			'multi'                   => 0,
			'show'                    => 'display_name',
			'echo'                    => 1,
			'selected'                => 0,
			'name'                    => 'user',
			'class'                   => '',
			'id'                      => '',
			'blog_id'                 => $GLOBALS['blog_id'],
			'who'                     => '',
			'include_selected'        => false
		);

		$defaults['selected'] = is_author() ? get_query_var( 'author' ) : 0;

		$r = wp_parse_args( $args, $defaults );
		extract( $r, EXTR_SKIP );

		$query_args           = wp_array_slice_assoc( $r,
			array( 'blog_id', 'include', 'exclude', 'orderby', 'order', 'who' ) );
		$query_args['fields'] = array( 'ID', 'display_name', 'user_login' );
		$users                = get_users( $query_args );

		$output = '';
		if ( ! empty( $users ) && ( empty( $hide_if_only_one_author ) || count( $users ) > 1 ) ) {
			$name = esc_attr( $name );
			if ( $multi && ! $id ) {
				$id = '';
			} else {
				$id = $id ? " id='" . esc_attr( $id ) . "'" : " id='$name'";
			}

			$output = "<select name='{$name}'{$id} class='$class'>\n";

			if ( $show_option_all ) {
				$output .= "\t<option value='0'>$show_option_all</option>\n";
			}

			if ( $show_option_none ) {
				$_selected = selected( - 1, $selected, false );
				$output    .= "\t<option value='0'$_selected>$show_option_none</option>\n";
			}

			$found_selected = false;
			foreach ( (array) $users as $user ) {
				$user->ID = (int) $user->ID;

				if ( $user->display_name != '' ) {
					$full_name = $user->display_name;
				} else {
					$full_name = $user->user_login;
				}

				if ( $full_name != '' ) {
					$_selected = selected( $user->ID, $selected, false );
					if ( $_selected ) {
						$found_selected = true;
					}
					$display = $full_name != ' ' ? $full_name : '(' . $user->user_login . ')';
					$output  .= "\t<option value='$user->ID'$_selected>" . esc_html( $display ) . "</option>\n";
				}
			}

			if ( isset( $_GET['author'] ) ) {
				if ( $_GET['author'] == '-1' ) {
					$_guest_selected = ' selected ';
				} else {
					$_guest_selected = '';
				}
			} else {
				$_guest_selected = '';
			}

			$output .= "\t<option value='-1' $_guest_selected>Guest</option>\n";

			if ( $include_selected && ! $found_selected && ( $selected > 0 ) ) {
				$user      = get_userdata( $selected );
				$_selected = selected( $user->ID, $selected, false );
				$display   = ! empty( $user->$show ) ? $user->$show : '(' . $user->user_login . ')';
				$output    .= "\t<option value='$user->ID'$_selected>" . esc_html( $display ) . "</option>\n";
			}

			$output .= '</select>';
		}

		$output = apply_filters( 'wp_dropdown_users', $output );

		if ( $echo ) {
			echo $output;
		}

		return $output;
	}

	/**
	 * Adds a date range to the WHERE portion of our query
	 *
	 * @param string $where The current WHERE portion of the query
	 *
	 * @return string $where The updated WHERE portion of the query
	 */
	public function filter_where( $where = '' ) {
		global $pagenow;

		if ( $pagenow == 'edit.php' && isset( $_GET['post_type'] ) && $_GET['post_type'] == 'carts' ) {
			global $woocommerce_cart_reports_options;
			global $start_date, $end_date;
			global $offset;

			if ( isset( $_GET['lifetime'] ) || ! isset( $_GET['mv'] ) ) {
				$args = array(
					'numberposts' => 1,
					'offset'      => 0,
					'orderby'     => 'post_modified',
					'order'       => 'ASC',
					'post_type'   => 'carts',
					'post_status' => 'publish',
				);

				$post = get_posts( $args );
				if ( isset( $post[0] ) ) {
					$start_date = strtotime( $post[0]->post_modified ) - ( 86400 );
				}
			}

			$start = date( 'Y-m-d G:i:s', $start_date );
			$end   = date( 'Y-m-d G:i:s', $end_date + 86400 );

			$timeout = $woocommerce_cart_reports_options['timeout'];

			if ( isset( $_GET['mv'] ) ) {
				if ( $_GET['mv'] == 'Open' ) {
					$where .= " AND post_modified > '" . date( 'Y-m-d G:i:s',
							time() + ( $offset * 3600 ) - $timeout ) . "'";
				} elseif ( $_GET['mv'] == 'Abandoned' ) {
					$where .= " AND post_modified < '" . date( 'Y-m-d G:i:s',
							( time() + ( $offset * 3600 ) - $timeout ) ) . "'";
				}
			}

			$where .= " AND post_modified > '" . $start . "' AND post_modified < '" . $end . "'";
			if ( isset( $_GET['author'] ) ) {
				if ( $_GET['author'] == '-1' ) {
					$where .= " AND post_author = ''";
				}
			}
		}

		return $where;
	}

} // END CLASS

function pretty_dump( $object = null ) {
	ob_start();                    // start buffer capture
	var_dump( $object );           // dump the values
	$contents = ob_get_contents(); // put the buffer into a variable
	ob_end_clean();                // end capture
	error_log( $contents );        // log contents of the result of var_dump( $object )
}
