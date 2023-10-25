<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName

/**
 * ANCHOR Make Upgrade To Pro link target blank and Add "Managed by SeedProd" to content area.
 */
function seedprod_pro_admin_js() {
	// Make Admin upgrade submenu link target _blank
	if ( defined( 'SEEDPROD_TEMPLATE_DEV_MODE' ) && SEEDPROD_TEMPLATE_DEV_MODE === true ) {
		echo "
        <script>
            jQuery( document ).ready(function($) {
                $('.toplevel_page_seedprod_pro .wp-first-item').hide();
            });
        </script>
        ";
	}
	echo "
    <script>
        jQuery( document ).ready(function($) {
            $('#sp-lite-admin-menu__upgrade').parent().attr('target','_blank');
            $('#sp-feature-request').parent().attr('target','_blank');
        });
    </script>
    ";

	if ( ! empty( $_GET['post'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$id          = absint( wp_unslash( $_GET['post'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$is_seedprod = 0;
		// check if is seedprod landing page
		if ( ! empty( get_post_meta( $id, '_seedprod_page', true ) ) ) {
			$is_seedprod = get_post_meta( $id, '_seedprod_page', true );
		}

		// check if is using seedprod's editor
		if ( ! empty( get_post_meta( $id, '_seedprod_edited_with_seedprod', true ) ) ) {
			$is_seedprod = get_post_meta( $id, '_seedprod_edited_with_seedprod', true );
		}

		$post_type = get_post_type( $id );
		// $edit_link = sprintf(
		//     '<a href="%1$s">%2$s</a>',
		//     admin_url().'admin.php?page=seedprod_pro_builder&id='.$id.'#/setup/'.$id,
		//     __( 'Edit with SeedProd', 'seedprod' );

		$setup_url = admin_url() . 'admin.php?page=seedprod_pro_builder&id=' . $id . '#/template/' . $id;
		$edit_url  = admin_url() . 'admin.php?page=seedprod_pro_builder&id=' . $id . '#/setup/' . $id;
		if ( 'page' == $post_type ) {
			echo "
    <script>
    jQuery( document ).ready(function($) {
        var checkExist = setInterval(function() {
            if ($('.edit-post-header-toolbar').length) {
                if(1 === " . esc_html( $is_seedprod ) . "){
                    $('.block-editor-block-list__layout').hide().after('<div style=\"text-align:center; \" class=\"managed_by_seedprod\">This page is managed by SeedProd<br><a href=\"" . esc_attr( $edit_url ) . '" class="button button-primary" style="display:flex; align-items:center; justify-content:center; margin:auto; width:200px; font-size: 18px; margin-top:10px"><img src="' . esc_attr( SEEDPROD_PRO_PLUGIN_URL ) . "public/svg/admin-bar-icon.svg\" style=\"margin-right:7px; margin-top:5px\"> Edit with SeedProd</a></div>');

                }
               clearInterval(checkExist);
            }
            if ($('#postdivrich').length) {
                if(1 === " . esc_html( $is_seedprod ) . "){
            $('#postdivrich').hide().after('<div style=\"text-align:center; \" class=\"managed_by_seedprod\">This page is managed by SeedProd<br><a href=\"" . esc_attr( $edit_url ) . '" class="button button-primary" style="display:flex; align-items:center; justify-content:center; margin:auto; width:220px; font-size: 16px; margin-top:10px"><img src="' . esc_attr( SEEDPROD_PRO_PLUGIN_URL ) . "public/svg/admin-bar-icon.svg\" style=\"margin-right:7px; margin-top:5px\"> Edit with SeedProd</a></div>');
            clearInterval(checkExist);
                }
            }
         }, 100);

    });
    </script>
    ";
		}
	}

}
add_action( 'admin_footer', 'seedprod_pro_admin_js' );


/**
 * ANCHOR Add Manage By SeedProd to Theme Themplate Parts Home and Blog
 */
function seedprod_pro_admin_js_check_theme_template_part() {
	$is_theme_template = seedprod_pro_check_home_blog_theme_template_part();
	if ( ! empty( $is_theme_template ) ) {
		$id = 0;
		if ( ! empty( $_GET['post'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$id = absint( wp_unslash( $_GET['post'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		}
		$post_type   = get_post_type( $id );
		$is_seedprod = 0;
		if ( ! empty( get_post_meta( $is_theme_template, '_seedprod_page', true ) ) ) {
			$is_seedprod = get_post_meta( $is_theme_template, '_seedprod_page', true );
		}
		if ( 'page' == $post_type ) {
			$edit_url = admin_url() . 'admin.php?page=seedprod_pro_builder&id=' . $is_theme_template . '#/setup/' . $is_theme_template . '/block-options';
			echo "
    <script>
    jQuery( document ).ready(function($) {
        var checkExist = setInterval(function() {
            if ($('.edit-post-header-toolbar').length) {
                if(1 === " . esc_html( $is_seedprod ) . "){
                    $('.block-editor-block-list__layout').hide().after('<div style=\"text-align:center; \" class=\"managed_by_seedprod\">This template page is managed by SeedProd<br><a href=\"" . esc_attr( $edit_url ) . '" class="button button-primary" style="display:flex; align-items:center; justify-content:center; margin:auto; width:200px; font-size: 18px; margin-top:10px"><img src="' . esc_attr( SEEDPROD_PRO_PLUGIN_URL ) . "public/svg/admin-bar-icon.svg\" style=\"margin-right:7px; margin-top:5px\"> Edit with SeedProd</a></div>');

                }
               clearInterval(checkExist);
            }
            if ($('#postdivrich').length) {
                if(1 === " . esc_html( $is_seedprod ) . "){
            $('#postdivrich').hide().after('<div style=\"text-align:center; \" class=\"managed_by_seedprod\">This template page is managed by SeedProd<br><a href=\"" . esc_attr( $edit_url ) . '" class="button button-primary" style="display:flex; align-items:center; justify-content:center; margin:auto; width:220px; font-size: 16px; margin-top:10px"><img src="' . esc_attr( SEEDPROD_PRO_PLUGIN_URL ) . "public/svg/admin-bar-icon.svg\" style=\"margin-right:7px; margin-top:5px\"> Edit with SeedProd</a></div>');
            clearInterval(checkExist);
                }
            }
         }, 100);

    });
    </script>
    ";
		}
	}

}
add_action( 'admin_footer', 'seedprod_pro_admin_js_check_theme_template_part' );



/**
 * ANCHOR Check if Post is Theme Themplate Parts Home or Blog
 */
function seedprod_pro_check_home_blog_theme_template_part() {

				$id               = false;
				$template_part_id = false;
	if ( ! empty( $_GET['post'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$id = absint( wp_unslash( $_GET['post'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	}

				// check if a template part home or blog
				$is_seedprod_theme_active = get_option( 'seedprod_theme_enabled' );

	if ( ! empty( $is_seedprod_theme_active ) && ! empty( $id ) ) {
		$front_page_type           = get_option( 'show_on_front' );
		$homepage_id               = get_option( 'page_on_front' );
		$blogpage_id               = get_option( 'page_for_posts' );
		$homepage_template_part_id = 0;
		$blogpage_template_part_id = 0;

		// look for template parts
		global $wpdb;
		$tablename      = $wpdb->prefix . 'posts';
		$meta_tablename = $wpdb->prefix . 'postmeta';

		$sql = "SELECT * FROM $tablename p LEFT JOIN $meta_tablename pm ON (pm.post_id = p.ID)";

		$sql .= ' WHERE post_status = "publish" AND post_type = "seedprod" AND meta_key = "_seedprod_is_theme_template"';

		// Has no separate data to prepare.
		$results = $wpdb->get_results( $sql ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

		foreach ( $results as $k => $v ) {
			// get condition meta
			$conditions = get_post_meta( $v->ID, '_seedprod_theme_template_condition', true );
			if ( ! empty( $conditions ) ) {
				// check for home template
				if ( strpos( $conditions, '"condition":"include","type":"is_front_page"' ) != false ) {
					$homepage_template_part_id = $v->ID;
				}
				// check for blog template
				if ( strpos( $conditions, '"condition":"include","type":"is_home"' ) != false ) {
					$blogpage_template_part_id = $v->ID;
				}
			}
		}

		if ( $id == $homepage_id ) {
			if ( ! empty( $homepage_template_part_id ) ) {
				$template_part_id = $homepage_template_part_id;
			}
		}

		if ( $id == $blogpage_id ) {
			if ( ! empty( $blogpage_template_part_id ) ) {
				$template_part_id = $blogpage_template_part_id;
			}
		}
	}

				return $template_part_id;
}

/**
 * ANCHOR Add "Edit with SeedProd" to classic editor and gutenberg editor logic.
 */
function seedprod_pro_add_admin_edit_seedprod() {
	$is_theme_template = seedprod_pro_check_home_blog_theme_template_part();
	if ( empty( $is_theme_template ) ) {
		$screen = get_current_screen();
		if ( 'page' === $screen->post_type ) {
			$id                      = 0;
			$is_seedprod             = 0;
			$seedprod_template_label = 'seedprod_lite';
			$is_seedprod_true        = 'seed_editor_false';
			$remove_post_callback    = 'seedprod_lite_remove_post';
			$seedprod_template_type  = 'template';

			
			if ( SEEDPROD_PRO_BUILD == 'pro' ) {
				$seedprod_template_label = 'seedprod_pro';
				$remove_post_callback    = 'seedprod_pro_remove_post';
			}
			

			if ( ! empty( $_GET['post'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				$id = absint( wp_unslash( $_GET['post'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended

				if ( ! empty( get_post_meta( $id, '_seedprod_page', true ) ) ) {
					$is_seedprod            = get_post_meta( $id, '_seedprod_page', true );
					$is_seedprod_true       = 'seed_editor_true';
					$seedprod_template_type = 'builder';
				}

				if ( ! empty( get_post_meta( $id, '_seedprod_edited_with_seedprod', true ) ) ) {
					$is_seedprod            = get_post_meta( $id, '_seedprod_edited_with_seedprod', true );
					$is_seedprod_true       = 'seed_editor_true';
					$seedprod_template_type = 'builder';
				}

				if ( ! empty( get_post_field( 'post_content_filtered', $id ) ) ) {
					$seedprod_template_type = 'builder';
				}
			}

			// can use the theme builder
			$from = 'post';

			if ( 'template' == $seedprod_template_type ) {
				$edit_url = admin_url() . 'admin.php?page=' . $seedprod_template_label . '_template&from=' . $from . '&id=' . $id . '#/template/' . $id;
			} else {
				$edit_url = admin_url() . 'admin.php?page=' . $seedprod_template_label . '_builder&from=' . $from . '&id=' . $id . '#/setup/' . $id;
			}

			$edit_seedprod_label  = '<img src="' . SEEDPROD_PRO_PLUGIN_URL . 'public/svg/admin-bar-icon.svg" style="margin-right:7px; margin-top:5px">' . __( 'Edit with SeedProd', 'seedprod-pro' );
			$back_wordpress_label = __( 'Switch Back to WordPress Editor', 'seedprod-pro' );

			$localizations = array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'nonce'    => wp_create_nonce( 'seedprod_back_to_editor_' . $id ),
			);

			printf(
				'
        <div class="active-seed-prod-buttons">
        <div class="' . esc_attr( $is_seedprod_true ) . '">
            <span class="seedprod-off">
            <a href="' . esc_attr( $edit_url ) . '" id="edit_seedprod_custom_link" class="edit_seedprod_custom_link button button-primary">
            ' .  $edit_seedprod_label  . '
            </a></span>
            <span class="seedprod-on">
            <a href="#back" class="back_to_wp_editor button">' . esc_html( $back_wordpress_label ) . '</a>
            </span>
        </div>
        </div>
        <div class="seedprod_hidden_data">
            <input type="hidden" class="_seedprod_template_type" name="_seedprod_template_type" value="' . esc_attr( $seedprod_template_type ) . '"/>
            <input type="hidden" class="_seedprod_label" name="_seedprod_label" value="' . esc_attr( $seedprod_template_label ) . '"/>
            <input type="hidden" class="_seedprod_template_edit_url" name="_seedprod_template_edit_url" value="' . esc_attr( $edit_url ) . '"/>
            <input type="hidden" class="_seedprod_true" name="_seedprod_true" value="' . esc_attr( $is_seedprod_true ) . '"/>
        </div>
        '
			);

			echo '
        <script type="text/javascript">
        
        jQuery(document).ready(function(){  

            jQuery(document).on("click", ".edit_seedprod_custom_link", function(event) { 

                if(confirm("Please note by switching to SeedProd the current page\'s content will be replaced.")){
                    
                    var url_string = window.location;
                    var url = new URL(url_string);
                    var postid = url.searchParams.get("post");
                    //console.log(postid);

                    var post_ID = 0; 
                    if(postid!=null){
                        post_ID = jQuery("#post_ID").val();
                    }
                    //console.log(post_ID);

                    var seedprod_template_type = jQuery("._seedprod_template_type").val();
                    var seedprod_label = jQuery("._seedprod_label").val();
                    var seedprod_template_edit_url = jQuery("._seedprod_template_edit_url").val();
                    var seedprod_true = jQuery("._seedprod_true").val();
                    
                    var seedprod_template_edit_url_ = "";
                    var admin_url = localizedVars.admin_url; 

                    if(seedprod_template_type=="template"){
                        seedprod_template_edit_url_ = `${admin_url}?page=${seedprod_label}_${seedprod_template_type}&from=' . esc_html( $from ) . '&id=${post_ID}#/template/${post_ID}`;
                    }else{
                        seedprod_template_edit_url_ = `${admin_url}?page=${seedprod_label}_${seedprod_template_type}&from=' . esc_html( $from ) . '&id=${post_ID}#/setup/${post_ID}`;
                    }
                    //console.log(seedprod_template_edit_url_);
                    location.href = seedprod_template_edit_url_;

                }
                

            });

            jQuery(document).on("click", ".back_to_wp_editor", function(event) { 
                if (confirm("Are you sure you want to switch back to using the WordPress Editor instead of SeedProd?") == false) {
					return false;
				}
                if (jQuery(".edit-post-header-toolbar").length) {
                    wp.data.dispatch( "core/block-editor" ).resetBlocks([]);
                    jQuery(".block-editor-block-list__layout").show();
                }

                if (jQuery("#postdivrich").length) {
                    //jQuery("#postdivrich").show();
                    //jQuery("#postdivrich .wp-editor-area").html("");
                }
                jQuery(".managed_by_seedprod").hide();
                
                var ajax_url = "' . esc_html( $localizations['ajax_url'] ) . '";
                var post_id =  jQuery("#post_ID").val();
    
                var formData = new FormData();
                formData.append("action", "' . esc_html( $remove_post_callback ) . '");
                formData.append("nonce", "' . esc_html( $localizations['nonce'] ) . '");
                formData.append("post_id", post_id);
                //console.log(formData);
    
                jQuery.ajax({ 
                    type: "POST",
                    url: ajax_url, 
                    data: formData,
                    cache: false,
                    processData : false,
                    contentType: false,
                    success: function(data) {
                        
                        jQuery(".seed_editor_true").addClass("seed_editor_false");
                        jQuery(".seed_editor_false").addClass("seed_editor_true");
                        //console.log("removed seedprod settings");

                        location.reload();

                    },
                });
                
            }); 
        });
        </script>
        ';
		}
	}
}
add_action( 'admin_footer', 'seedprod_pro_add_admin_edit_seedprod' );

/**
 * ANCHOR Adds Edit with SeedProd to Pages row
 */
add_filter( 'page_row_actions', 'seedprod_pro_filter_page_row_actions', 11, 2 );

/**
 * Filters the array of row action links on the Pages list table.
 *
 * @param string[] $actions An array of row action links.
 * @param WP_Post  $post    The post object.
 * @return string[] $actions An array of row action links.
 */
function seedprod_pro_filter_page_row_actions( $actions, $post ) {
	$has_settings    = get_post_meta( $post->ID, '_seedprod_page', true );
	$seedprod_editor = get_post_meta( $post->ID, '_seedprod_edited_with_seedprod', true );
	if ( 1 == $has_settings || 1 == $seedprod_editor ) {
		$id                       = $post->ID;
		$actions['edit_seedprod'] = sprintf(
			'<a href="%1$s">%2$s</a>',
			admin_url() . 'admin.php?page=seedprod_pro_builder&id=' . $id . '#/setup/' . $id,
			__( 'Edit with SeedProd', 'seedprod' )
		);
		// unset($actions['inline hide-if-no-js']);
	}

	return $actions;
}


/**
 * ANCHOR Set Posts datatable row label.
 */
add_filter( 'display_post_states', 'seedprod_pro_add_post_state', 10, 2 );

/**
 * Filters the default post display states used in the posts list table.
 *
 * @param string[] $post_states An array of post display states.
 * @param WP_Post  $post        The current post object.
 * @return string[] $post_states An array of post display states.
 */
function seedprod_pro_add_post_state( $post_states, $post ) {
	$has_settings    = get_post_meta( $post->ID, '_seedprod_page', true );
	$seedprod_editor = get_post_meta( $post->ID, '_seedprod_edited_with_seedprod', true );

	if ( 'page' == $post->post_type && ! empty( $seedprod_editor ) ) {
		$post_states['seedprod-editor'] = 'SeedProd';
		return $post_states;
	}

	if ( 'page' == $post->post_type && ! empty( $has_settings ) ) {
		$post_states['seedprod'] = 'SeedProd Landing Page';
		return $post_states;
	}

	return $post_states;
}

/**
 * ANCHOR Add "Edit with SeedProd" to classic editor
 */
add_action( 'edit_form_after_title', 'seedprod_pro_before_editor' );

/**
 * Fires after the title field.
 *
 * @return void
 */
function seedprod_pro_before_editor() {
	$seedprod_app_settings = get_option( 'seedprod_app_settings' );
	if ( ! empty( $seedprod_app_settings ) ) {
		$seedprod_app_settings = json_decode( stripslashes( $seedprod_app_settings ) );
	} else {
		// fail safe incase settings go missing
		require_once SEEDPROD_PRO_PLUGIN_PATH . 'resources/data-templates/default-settings.php';
		update_option( 'seedprod_app_settings', $seedprod_app_default_settings );
		$seedprod_app_settings = json_decode( $seedprod_app_default_settings );
	}
	$disable_seedprod_button = is_object( $seedprod_app_settings ) ? $seedprod_app_settings->disable_seedprod_button : false;

	if ( false == $disable_seedprod_button ) {
		echo '
        <div class="active-seed-prod-buttons-classic"></div>
        <script type="text/javascript">
        jQuery(document).ready(function(){  
            var active_seedprod_btn = jQuery(".active-seed-prod-buttons").html();
            jQuery(".active-seed-prod-buttons-classic").html(active_seedprod_btn);
        });
        </script>
    ';
	}
}


/**
 * ANCHOR Add "Edit with SeedProd" and "Back to WordPress Editor" buttons to Gutenberg, logic in *seedprod_pro_link_injection_to_gutenberg_toolbar
 */
add_action( 'enqueue_block_editor_assets', 'seedprod_pro_link_injection_to_gutenberg_toolbar' );

/**
 * Fires after block assets have been enqueued for the editing interface.
 *
 * @return void
 */
function seedprod_pro_link_injection_to_gutenberg_toolbar() {
	$is_theme_template = seedprod_pro_check_home_blog_theme_template_part();
	if ( empty( $is_theme_template ) ) {
		$seedprod_app_settings = get_option( 'seedprod_app_settings' );
		if ( ! empty( $seedprod_app_settings ) ) {
			$seedprod_app_settings = json_decode( stripslashes( $seedprod_app_settings ) );
		} else {
			// fail safe incase settings go missing
			require_once SEEDPROD_PRO_PLUGIN_PATH . 'resources/data-templates/default-settings.php';
			update_option( 'seedprod_app_settings', $seedprod_app_default_settings );
			$seedprod_app_settings = json_decode( $seedprod_app_default_settings );
		}
		$disable_seedprod_button = is_object( $seedprod_app_settings ) ? $seedprod_app_settings->disable_seedprod_button : false;

		if ( false == $disable_seedprod_button ) {
			$screen = get_current_screen();
			if ( 'page' === $screen->post_type ) {
				$localizations = array(
					'admin_url'  => admin_url() . 'admin.php',
					'ajax_url'   => admin_url( 'admin-ajax.php' ),
					'_wp_nonce'  => wp_create_nonce( 'ajax-nonce' ),
					'plugin_url' => SEEDPROD_PRO_PLUGIN_URL,
				);
				wp_enqueue_script( 'seedprod-link-in-toolbar', SEEDPROD_PRO_PLUGIN_URL . 'public/js/toolbar.js', array(), '1.0', true );
				wp_localize_script( 'seedprod-link-in-toolbar', 'localizedVars', $localizations );
			}
		}
	}
}


/**
 * ANCHOR Add "SeedProd Landing Page" link to "+ New" menu item on the WordPress admin bar.
 */
add_action( 'admin_bar_menu', 'seedprod_pro_add_menu_item', 80 );

/**
 * Load all necessary admin bar items.
 *
 * @param WP_Admin_Bar $wp_admin_bar WP_Admin_Bar instance.
 * @return void
 */
function seedprod_pro_add_menu_item( $wp_admin_bar ) {
	$seedprod_menu_link = 'admin.php?page=seedprod_lite_template&id=0#/template';
	
	if ( SEEDPROD_PRO_BUILD == 'pro' ) {
		$seedprod_menu_link = 'admin.php?page=seedprod_pro_template&id=0#/template';
	}
	

	$args = array(
		'id'     => 'seedprod_template',
		'title'  => 'SeedProd Landing Page',
		'href'   => $seedprod_menu_link,
		'parent' => 'new-content',
	);

	$wp_admin_bar->add_node( $args );
}


/**
 * ANCHOR Remove SeedProd post meta when user clicks "Back to WordPress Editor" button.
 */
add_action( 'wp_ajax_seedprod_pro_remove_post', 'seedprod_pro_remove_post' );

/**
 * Remove post.
 *
 * @return void
 */
function seedprod_pro_remove_post() {
	$post_id = isset( $_POST['post_id'] ) ? absint( wp_unslash( $_POST['post_id'] ) ) : null;

	if ( check_ajax_referer( 'seedprod_back_to_editor_' . $post_id, 'nonce' ) && current_user_can( 'delete_post', $post_id ) ) {
		$data = array(
			'ID' => $post_id,
		//'post_content' => '',
		);

		delete_post_meta( $post_id, '_seedprod_page' );
		delete_post_meta( $post_id, '_seedprod_edited_with_seedprod' );
		//wp_update_post( $data );
		wp_die();
	}
}
