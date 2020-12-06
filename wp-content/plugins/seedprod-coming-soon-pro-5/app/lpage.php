<?php
/*
 * Get lpage Lists
 */
function seedprod_pro_get_lpage_list()
{
    if (check_ajax_referer('seedprod_nonce')) {
        global $wpdb;

        $tablename = $wpdb->prefix . 'posts';
        $meta_tablename = $wpdb->prefix . 'postmeta';

        $sql = "SELECT id,post_title as name,meta_value as uuid FROM $tablename p LEFT JOIN $meta_tablename pm ON (pm.post_id = p.ID)";

        $sql .= ' WHERE post_status != "trash" AND post_type = "page" AND meta_key = "_seedprod_page_uuid"' ;
        $response = $wpdb->get_results($sql);

        wp_send_json($response);
    }
}

/* Check Slug */

function seedprod_pro_slug_exists()
{
    if (check_ajax_referer('seedprod_pro_slug_exists')) {
        $post_name = $_POST['post_name'];
        global $wpdb;
        $tablename = $wpdb->prefix . 'posts';
        $sql = "SELECT post_name FROM $tablename";
        $sql .= " WHERE post_name = %s" ;
        $safe_sql = $wpdb->prepare($sql, $post_name);
        $result = $wpdb->get_var($safe_sql);
        if (empty($result)) {
            wp_send_json_success();
        } else {
            wp_send_json_error();
        }
    }
}

/*
 * New lpage
 */
function seedprod_pro_new_lpage()
{
    if (isset($_GET['page']) && $_GET['page'] == 'seedprod_pro_template' && isset($_GET['id']) && $_GET['id'] == '0') {
        // get theme code

        $id = absint($_GET['id']) ;

        $type = 'lp';
        if (!empty($_GET['type'])) {
            $type = sanitize_text_field($_GET['type']);
        }

        // base page settings
        require_once(SEEDPROD_PRO_PLUGIN_PATH.'resources/data-templates/basic-page.php');
        $settings = json_decode($seedprod_basic_lpage);
        $settings->is_new = true;
        $settings->page_type = $type;


        $cpt = 'page';
        if ($type == 'cs' || $type == 'mm' || $type == 'p404') {
            $cpt = 'seedprod';
        }

        $slug = '';
        if ($type == 'cs') {
            $slug= 'sp-cs';
            $lpage_name = $slug;
            $settings->no_conflict_mode = true;
        }
        if ($type == 'mm') {
            $slug= 'sp-mm';
            $lpage_name = $slug;
            $settings->no_conflict_mode = true;
        }
        if ($type == 'p404') {
            $slug= 'sp-p404';
            $lpage_name = $slug;
            $settings->no_conflict_mode = true;
        }
        $settings = wp_json_encode($settings);

        // Insert
        $id = wp_insert_post(
            array(
            'comment_status' => 'closed',
            'ping_status' => 'closed',
            'post_content'=>'',
            'post_status' => 'draft',
            'post_title'=>'seedprod',
            'post_type'=> $cpt,
            'post_name'=>$slug,
            'post_content_filtered'=> $settings,
            'meta_input' => array(
                '_seedprod_page' => true,
                '_seedprod_page_uuid' => wp_generate_uuid4(),
        )),
            true
        );


        // record coming soon page_id
        if ($type == 'cs') {
            update_option('seedprod_coming_soon_page_id', $id);
        }
        if ($type == 'mm') {
            update_option('seedprod_maintenance_mode_page_id', $id);
        }
        if ($type == 'p404') {
            update_option('seedprod_404_page_id', $id);
        }

        if ($type == 'lp') {
            if (is_numeric($id)) {
                $lpage_name =  esc_html__('New Page', 'seedprod-pro') . " (ID #$id)";
            } else {
                $lpage_name =  esc_html__('New Page', 'seedprod-pro');
            }
        }



        wp_update_post(
            array(
                'ID' => $id,
                'post_title'=>$lpage_name,
              )
        );


        wp_redirect('admin.php?page=seedprod_pro_template&id='.$id.'#/template/'.$id);
        exit();
    }
}

/*
 * lpage Datatable
 */
function seedprod_pro_lpage_datatable()
{
    if (check_ajax_referer('seedprod_nonce')) {
        $data = array('');
        $current_page = 1;
        if (!empty(absint($_GET['current_page']))) {
            $current_page = absint($_GET['current_page']);
        }
        $per_page=10;

        $filter = null;
        if (!empty($_GET['filter'])) {
            $filter = sanitize_text_field($_GET['filter']);
            if ($filter == 'all') {
                $filter = null;
            }
        }

        if (!empty($_GET['s'])) {
            $filter = null;
        }

        // Get records
        global $wpdb;
        $tablename = $wpdb->prefix . 'posts';
        $meta_tablename = $wpdb->prefix . 'postmeta';

        $sql = "SELECT * FROM $tablename p LEFT JOIN $meta_tablename pm ON (pm.post_id = p.ID)";

        $sql .= ' WHERE 1 = 1 AND post_type = "page" AND meta_key = "_seedprod_page"' ;

        if (! empty($filter)) {
            if (esc_sql($filter) == 'published') {
                $sql .= ' AND  post_status = "publish" ';
            }
            if (esc_sql($filter) == 'drafts') {
                $sql .= ' AND  post_status = "draft" ';
            }
            if (esc_sql($filter) == 'scheduled') {
                $sql .= ' AND  post_status = "future" ';
            }
            if (esc_sql($filter) == 'archived') {
                $sql .= ' AND  post_status = "trash" ';
            }
        } else {
            $sql .= 'AND post_status != "trash"';
        }

        if (!empty($_GET['s'])) {
            $sql .= ' AND post_title LIKE "%'. esc_sql(trim(sanitize_text_field($_GET['s']))) .'%"';
        }

        if (! empty($_GET['orderby'])) {
            $orderby = $_GET['orderby'];
            if ($orderby == 'date') {
                $orderby  = 'post_modified';
            }
            if ($orderby == 'name') {
                $orderby  = 'post_title';
            }
            $sql .= ' ORDER BY ' . esc_sql($orderby);

            if (sanitize_text_field($_GET['order']) === 'desc') {
                $order = 'DESC';
            } else {
                $order = 'ASC';
            }
            $sql .=  ' ' . $order;
        } else {
            $sql .= ' ORDER BY id DESC';
        }

        $sql .= " LIMIT $per_page";
        if (empty($_POST['s'])) {
            $sql .= ' OFFSET ' . ($current_page - 1) * $per_page;
        }

        $results = $wpdb->get_results($sql);

        $data = array();
        foreach ($results as $v) {

            // Format Date
            //$modified_at = date(get_option('date_format').' '.get_option('time_format'), strtotime($v->post_modified));

            $modified_at = date("Y/m/d", strtotime($v->post_modified));

            $posted_at = date("Y/m/d", strtotime($v->post_date));

            $url = get_permalink($v->ID);

            if ($v->post_status == 'publish') {
                $status = 'Published';
            }
            if ($v->post_status == 'draft') {
                $status = 'Draft';
            }
            if ($v->post_status == 'future') {
                $status = 'Scheduled';
            }
            if ($v->post_status == 'trash') {
                $status = 'Trash';
            }

            // Load Data

            $data[] = array(
                'id' => $v->ID,
                'name' => $v->post_title,
                'status' => $status,
                'post_status' => $v->post_status,
                'url' => $url,
                'modified_at' => $modified_at,
                'posted_at' => $posted_at,
                );
        }

        $totalitems = seedprod_pro_lpage_get_data_total($filter);
        $views = seedprod_pro_lpage_get_views($filter);

        $response = array(
            'rows' => $data,
            'totalitems' => $totalitems,
            'totalpages' => ceil($totalitems/10),
            'currentpage'=> $current_page,
            'views'=>$views,
        );

        wp_send_json($response);
    }
}


function seedprod_pro_lpage_get_data_total($filter = null)
{
    global $wpdb;

    $tablename = $wpdb->prefix . 'posts';
    $meta_tablename = $wpdb->prefix . 'postmeta';

    $sql = "SELECT count(*) FROM $tablename p LEFT JOIN $meta_tablename pm ON (pm.post_id = p.ID)";

    $sql .= ' WHERE 1 = 1 AND post_type = "page"  AND meta_key = "_seedprod_page"' ;

    if (! empty($filter)) {
        if (esc_sql($filter) == 'published') {
            $sql .= ' AND  post_status = "publish" ';
        }
        if (esc_sql($filter) == 'drafts') {
            $sql .= ' AND  post_status = "draft" ';
        }
        if (esc_sql($filter) == 'scheduled') {
            $sql .= ' AND  post_status = "future" ';
        }
        if (esc_sql($filter) == 'archived') {
            $sql .= ' AND  post_status = "trash" ';
        }
    } else {
        $sql .= ' AND post_status != "trash"';
    }

    if (!empty($_GET['s'])) {
        $sql .= ' AND post_name LIKE "%'. esc_sql(sanitize_text_field($_GET['s'])) .'%"';
    }

    $results = $wpdb->get_var($sql);
    return $results;
}



function seedprod_pro_lpage_get_views($filter = null)
{
    $views = array();
    $current = (!empty($filter) ? $filter : 'all');
    $current = sanitize_text_field($current);

    global $wpdb;
    $tablename = $wpdb->prefix . 'posts';
    $meta_tablename = $wpdb->prefix . 'postmeta';

    //All link
    $sql = "SELECT count(*) FROM $tablename p LEFT JOIN $meta_tablename pm ON (pm.post_id = p.ID)";

    $sql .= ' WHERE 1 = 1 AND post_type = "page" AND post_status != "trash"  AND meta_key = "_seedprod_page"' ;

    $results = $wpdb->get_var($sql);
    $class = ($current == 'all' ? ' class="current"' :'');
    $all_url = remove_query_arg('filter');
    $views['all'] = $results;

    //Published link
    $sql = "SELECT count(*) FROM $tablename p LEFT JOIN $meta_tablename pm ON (pm.post_id = p.ID)";

    $sql .= ' WHERE 1 = 1 AND post_type = "page"  AND meta_key = "_seedprod_page" AND post_status = "publish" ' ;

    $results = $wpdb->get_var($sql);
    $running_url = add_query_arg('filter', 'publish');
    $class = ($current == 'publish' ? ' class="current"' :'');
    $views['published'] = $results;

    //Drafts link
    $sql = "SELECT count(*) FROM $tablename p LEFT JOIN $meta_tablename pm ON (pm.post_id = p.ID)";

    $sql .= ' WHERE 1 = 1 AND post_type = "page"  AND meta_key = "_seedprod_page" AND post_status = "draft" ' ;

    $results = $wpdb->get_var($sql);
    $upcoming_url = add_query_arg('filter', 'drafts');
    $class = ($current == 'drafts' ? ' class="current"' :'');
    $views['drafts'] = $results;

    //Scheduled link
    $sql = "SELECT count(*) FROM $tablename p LEFT JOIN $meta_tablename pm ON (pm.post_id = p.ID)";

    $sql .= ' WHERE 1 = 1 AND post_type = "page"  AND meta_key = "_seedprod_page" AND post_status = "future" ' ;

    $results = $wpdb->get_var($sql);
    $ended_url = add_query_arg('filter', 'scheduled');
    $class = ($current == 'scheduled' ? ' class="current"' :'');
    $views['scheduled'] = $results;

    //Trash link
    $sql = "SELECT count(*) FROM $tablename p LEFT JOIN $meta_tablename pm ON (pm.post_id = p.ID)";

    $sql .= ' WHERE 1 = 1 AND post_type = "page"  AND meta_key = "_seedprod_page" AND post_status = "trash" ' ;

    $results = $wpdb->get_var($sql);
    $archived_url = add_query_arg('filter', 'archived');
    $class = ($current == 'archived' ? ' class="current"' :'');
    $views['archived'] = $results;

    return $views;
}

/*
 * Duplicate lpage
 */

function seedprod_pro_duplicate_lpage()
{
    if (check_ajax_referer('seedprod_pro_duplicate_lpage')) {
        $id = '';
        if (!empty($_GET['id'])) {
            $id = absint($_GET['id']);
        }

        $post = get_post($id);
        $json = $post->post_content_filtered;

        $args = array(
            'comment_status' => 'closed',
            'ping_status' => 'closed',
            'post_content' => $post->post_content,
            //'post_content_filtered' => $post->post_content_filtered,
            'post_status' => 'draft',
            'post_title' => $post->post_title .'- Copy',
            'post_type' => 'page',
            'post_name'=> '',
            'meta_input' => array(
                '_seedprod_page' => true,
                '_seedprod_page_uuid' => wp_generate_uuid4(),
        )
        );

        $new_post_id = wp_insert_post($args, true);
        // reinsert json due to slash bug
        global $wpdb;
        $tablename = $wpdb->prefix . 'posts';
        $wpdb->update(
            $tablename,
            array(
            'post_content_filtered' => $json,	// string
        ),
            array( 'ID' => $new_post_id ),
            array(
            '%s',	// value1
        ),
            array( '%d' )
        );



        wp_send_json(array('status'=> true));
    }
}


/*
* Archive Selected lpage
*/
function seedprod_pro_archive_selected_lpages()
{
    if (check_ajax_referer('seedprod_pro_archive_selected_lpages')) {
        if (current_user_can('list_users')) {
            if (!empty($_GET['ids'])) {
                $ids = array_map('intval', explode(",", $_GET['ids']));
                foreach ($ids as $v) {
                    wp_trash_post($v);
                }

                wp_send_json(array('status'=> true));
            }
        }
    }
}

/*
* Unarchive Selected lpage
*/
function seedprod_pro_unarchive_selected_lpages($ids)
{
    if (check_ajax_referer('seedprod_pro_unarchive_selected_lpages')) {
        if (current_user_can('list_users')) {
            if (!empty($_GET['ids'])) {
                $ids = array_map('intval', explode(",", $_GET['ids']));
                foreach ($ids as $v) {
                    wp_untrash_post($v);
                }

                wp_send_json(array('status'=> true));
            }
        }
    }
}

/*
* Delete Archived lpage
*/
function seedprod_pro_delete_archived_lpages()
{
    if (check_ajax_referer('seedprod_pro_delete_archived_lpages')) {
        if (current_user_can('list_users')) {
            if (!empty($_GET['ids'])) {
                $ids = array_map('intval', explode(",", $_GET['ids']));
                foreach ($ids as $v) {
                    wp_delete_post($v);
                }

                wp_send_json(array('status'=> true));
            }
        }
    }
}

/*
 * Save/Update lpage
 */

function seedprod_pro_save_lpage()
{
    if (check_ajax_referer('seedprod_nonce')) {

        // Validate
        $errors = array();
        // if(!is_email($_POST['product']['email'])){
        //     $errors['email'] = 'Please enter a valid email.';
        // }

        if (!empty($errors)) {
            header('Content-Type: application/json');
            header('Status: 400 Bad Request');
            echo json_encode($errors);
            exit();
        }

        // clean slashes post
        $sp_post = $_POST;
        $sp_post['lpage_html'] = stripslashes_deep($sp_post['lpage_html']);

        // remove uneeded code
        $html = $sp_post['lpage_html'];
        if (!empty($html)) {
            $html = preg_replace("'<span class=\"sp-hidden\">START-REMOVE</span>[\s\S]+?<span class=\"sp-hidden\">END-REMOVE</span>'", '', $html);
            $html = preg_replace("'<span class=\"sp-hidden\">START-COUNTDOWN-REMOVE</span>[\s\S]+?<span class=\"sp-hidden\">END-COUNTDOWN-REMOVE</span>'", '', $html);
            $html = preg_replace("'seedprod-jscode'", 'script', $html);
            $html = preg_replace("'<!---->'", '', $html);
            $html = preg_replace("'<!--'", '', $html);
            $html = preg_replace("'-->'", '', $html);
            $html = preg_replace("'contenteditable=\"true\"'", '', $html);
            $html = preg_replace("'spellcheck=\"false\"'", '', $html);
        }

        // sanitize post
        $lpage_id  = absint($sp_post['lpage_id']);
        $lpage_name = sanitize_text_field($sp_post['lpage_name']);
        $lpage_slug = sanitize_title($sp_post['lpage_slug']);
        $lpage_post_status = sanitize_title($sp_post['lpage_post_status']);
        $settings = $sp_post['settings'];
        //$settings = wp_json_encode(json_decode( stripslashes($sp_post['settings'])));

        // set update array
        $update = array();
        $update['ID'] = $lpage_id;
        if (!empty($lpage_name)) {
            $update['post_title'] = $lpage_name;
        }
        if (!empty($lpage_slug)) {
            $update['post_name'] = $lpage_slug;
        }
        if (!empty($lpage_post_status)) {
            $update['post_status'] = $lpage_post_status;
        }
        if (!empty($html)) {
            $update['post_content'] = $html;
        }
        if (!empty($settings)) {
            $update['post_content_filtered'] = $settings;
        }

        $status = '';
        if (empty($lpage_id)) {
            wp_die();
        } else {
            if (!empty($sp_post['save_type']) && $sp_post['save_type'] == 'autosave') {
                $update['post_ID'] = $lpage_id;
                $id = @wp_create_post_autosave($update);
                $status = 'autosave';
            } else {
                wp_update_post($update);
                $status = 'updated';
            }
        }


        $response = array(
            'status' => $status,
            'id' => $lpage_id ,
            //'revisions' => $revisions,
        );

        // clear any migration flags
        $i = get_option('seedprod_csp4_imported');
        if ($i == 1) {
            delete_option('seedprod_csp4_imported');
            delete_option('seedprod_show_csp4');
            update_option('seedprod_csp4_migrated', true);
        }

        $i = get_option('seedprod_cspv5_imported');
        if ($i == 1) {
            delete_option('seedprod_cspv5_imported');
            delete_option('seedprod_show_cspv5');
            update_option('seedprod_cspv5_migrated', true);
        }

        // migrate landing page if id exists
        $settings = json_decode(stripslashes_deep($sp_post['settings']));
        if (!empty($settings->cspv5_id)) {
            $cspv5_id = $settings->cspv5_id;
            global $wpdb;
            $tablename = $wpdb->prefix . 'cspv5_pages';
            $r = $wpdb->update(
                $tablename,
                array(
                'meta' => 'migrated',
            ),
                array( 'id' =>  $cspv5_id  ),
                array(
                '%s',
            ),
                array( '%d' )
            );
        }

        
        $domain_mapping_status = !empty($settings->domain_mapping_status) ?
            $settings->domain_mapping_status :
            false;

        $domain_mapping = !empty($settings->domain_mapping) ?
            $settings->domain_mapping :
            '';

        $domain_mapping_force_https = !empty($settings->domain_mapping_force_https) ?
            $settings->domain_mapping_force_https :
            false;

        $domain_mapping_error = seedprod_pro_domain_mapping_db_update(
            $lpage_id,
            $domain_mapping_status,
            $domain_mapping,
            $domain_mapping_force_https
        );

        if ( !empty($domain_mapping_error) ) { $response['domain_mapping_error'] = $domain_mapping_error; }
        

        wp_send_json($response);
    }
}

function seedprod_pro_get_revisisons()
{
    $lpage_id  = absint($_POST['lpage_id']);
    $revisions = wp_get_post_revisions($lpage_id, array('numberposts'=>50));
    foreach ($revisions as $v) {
        $v->time_ago = human_time_diff(strtotime($v->post_date_gmt));
        $v->post_date_formated = date('M j \a\t '.get_option('time_format'), strtotime($v->post_date));
        $authordata = get_userdata($v->post_author);
        $v->author_name = $authordata->data->user_nicename;
        $v->author_email = md5($authordata->data->user_email);
        unset($v->post_content);

        // $created_at = date(get_option('date_format').' '.get_option('time_format'), strtotime($v->post_date));
    }
    $revisions = array_values($revisions);

    $response = array(
        'id' => $lpage_id ,
        'revisions' => $revisions,
    );



    wp_send_json($response);
}



 
function seedprod_pro_backgrounds_sideload()
{
    if (check_ajax_referer('seedprod_pro_backgrounds_sideload')) {
        $image = '';
        if (isset($_REQUEST['image'])) {
            $image =  urldecode($_REQUEST['image']);
            $file = media_sideload_image($image.'&type=.jpg', 0, null, 'src');

            if (is_wp_error($file)) {
                $error_message = $file->get_error_message();
                wp_send_json(0);
            } else {
                wp_send_json($file);
            }
        }

        exit();
    }
}

function seedprod_pro_backgrounds_download()
{
    if (check_ajax_referer('seedprod_pro_backgrounds_download')) {
        $image = '';
        if (isset($_REQUEST['image'])) {
            $image =  urldecode($_REQUEST['image']);
            $response = wp_remote_get(SEEDPROD_PRO_BACKGROUND_DOWNLOAD_API_URL.'?image='.$image);
            if (!is_wp_error($response)) {
                wp_send_json(1);
            }
        }

        exit();
    }
}


function seedprod_pro_get_utc_offset()
{
    if (check_ajax_referer('seedprod_pro_get_utc_offset')) {
        $_POST = stripslashes_deep($_POST);

        $timezone = sanitize_text_field($_POST['timezone']);
        $ends = sanitize_text_field($_POST['ends']);
        $ends_time = sanitize_text_field($_POST['ends_time']);

        //$ends = substr($ends, 0, strpos($ends, 'T'));
        $ends = $ends .' '. $ends_time;
        $ends_timestamp = strtotime($ends. ' '.$timezone);
        $ends_utc =  date('Y-m-d H:i:s', $ends_timestamp);

        // countdown status
        $countdown_status = '';
        if (!empty($starts_utc) && time() < strtotime($starts_utc." UTC")) {
            $countdown_status = __('Starts in', 'seedprod-pro').' '.human_time_diff(time(), $starts_timestamp);
        } elseif (!empty($ends_utc) && time() > strtotime($ends_utc." UTC")) {
            $countdown_status = __('Ended', 'seedprod-pro').' '.human_time_diff(time(), $ends_timestamp).' ago';
        }


        $response = array(
            'ends_timestamp' =>  $ends_timestamp,
            'countdown_status' => $countdown_status,
        );

        wp_send_json($response);
    }
}



/*
 * Save/Update lpages Template
 */

function seedprod_pro_save_template()
{
    // get template code and set name and slug
    if (check_ajax_referer('seedprod_nonce')) {
        $_POST = stripslashes_deep($_POST);

        $status = false;
        $lpage_id = null;

        if (empty(absint($_POST['lpage_id']))) {
            // shouldn't get here
            $response = array(
                'status' => $status,
                'id' => $lpage_id,
                'code' => '',
            );

            wp_send_json($response, 403);
        } else {
            $lpage_id  = absint($_POST['lpage_id']);
            $template_id  = absint($_POST['lpage_template_id']);

            if ($template_id != 99999) {
                $template_code = seedprod_pro_get_template_code($template_id);
            }


            // merge in template code to settings
            global $wpdb;
            $tablename = $wpdb->prefix . 'posts';
            $sql = "SELECT * FROM $tablename WHERE id = %d";
            $safe_sql = $wpdb->prepare($sql, $lpage_id);
            $lpage = $wpdb->get_row($safe_sql);
            $settings = json_decode($lpage->post_content_filtered, true);
            $settings['template_id'] = $template_id;
            if ($template_id != 99999) {
                unset($settings['document']);
                $template_code_merge = json_decode($template_code, true);
                $settings = $settings + $template_code_merge;
            }

            $settings['page_type'] = sanitize_text_field($_POST['lpage_type']);

            // save settings
            // $r = wp_update_post(
            //     array(
            //         'ID' => $lpage_id,
            //         'post_title'=>sanitize_text_field($_POST['lpage_name']),
            //         'post_content_filtered'=> json_encode($settings),
            //         'post_name' => sanitize_title($_POST['lpage_slug']),
            //       )
            // );

            global $wpdb;
            $tablename = $wpdb->prefix . 'posts';
            $r = $wpdb->update(
                $tablename,
                array(
                'post_title'=>sanitize_text_field($_POST['lpage_name']),
                'post_content_filtered'=> json_encode($settings),
                'post_name' => sanitize_title($_POST['lpage_slug']),
            ),
                array( 'ID' => $lpage_id ),
                array(
                '%s',
                '%s',
                '%s',
            ),
                array( '%d' )
            );


            $status = 'updated';
        }

        $response = array(
            'status' => $status,
            'id' => $lpage_id ,
            'code' => $template_code,
        );

        wp_send_json($response);
    }
}

function seedprod_pro_get_template_code($id)
{
    // Get themes
    $code = '';

    $apikey = get_option('seedprod_api_token');
    if(empty($apikey)){
        $url = SEEDPROD_PRO_API_URL.'templates-preview?id='.$id.'&filter=template_code'.'&api_token='.$apikey;
    }else{
        $url = SEEDPROD_PRO_API_URL.'templates?id='.$id.'&filter=template_code'.'&api_token='.$apikey;
    }


    $response = wp_remote_get($url);

    if (is_wp_error($response)) {
        $code = $response->get_error_message();
    } else {
        $response_code = wp_remote_retrieve_response_code($response);
        if ($response_code == '200') {
            //set_transient('seedprod_template_code_'.$id,$response['body'],86400);
            $code = $response['body'];
        //error_log($code);
        } else {
            $code = __("<br><br>Please enter a valid license key to access the themes. You can still proceed to create a page with the default theme.<br> <a class='seedprod_no_themes' href='?theme=0'>Click to continue &#8594;</a>", 'seedprod-pro');
        }
    }

    return $code;
}

function seedprod_pro_get_namespaced_custom_css()
{
    if (check_ajax_referer('seedprod_pro_get_namespaced_custom_css')) {
        if(!empty($_POST['css'])){
            $css = $_POST['css'];
            require_once(SEEDPROD_PRO_PLUGIN_PATH.'app/includes/seedprod_lessc.inc.php');
            $less = new seedprod_lessc();
            $style = $less->parse('.sp-html {'.$css.'}');
            echo $style;
            exit();
        }
    }
}


/*
 * Update Domain Mapping Database Table
 * Returns nothing if success, string if error.
 */
function seedprod_pro_domain_mapping_db_update( $id, $status, $url, $force_https ) {

    global $wpdb;
    $tablename = $wpdb->prefix . 'sp_domain_mapping';

    $where = array( 'mapped_page_id' => $id );
    $where_format = array( '%d' );

    if ( $status === false || empty($url) ) {

            $delete = $wpdb->delete( $tablename, $where, $where_format );
            return ( false === $delete ?
                     'Delete Error: Not able to remove Domain Mapping entries on save.' : '' );

    } else {

        $url_no_whitespace = preg_replace( '/\s+/', '', $url );
        $url_no_scheme =  preg_replace( '(^.*:\/\/)', '', $url_no_whitespace );
        $url_add_scheme = ( $force_https ? 'https://'.$url_no_scheme : 'http://'.$url_no_scheme );

        $url_parsed = parse_url( $url_add_scheme );

        $data = array(
            'domain'         => sanitize_text_field( $url_parsed['host'] ),
            'path'           => sanitize_text_field( trim($url_parsed['path'], '/') ),
            'mapped_page_id' => absint( $id ),
            'force_https'    => rest_sanitize_boolean( ($force_https ? 1 : 0) ),
        );
        $data_format = array( '%s', '%s', '%d', '%d' );

        if ( $url_parsed !== false ) {
            $updated = $wpdb->update( $tablename, $data, $where, $data_format, $where_format );

            if ( $updated > 0 ) {
                return;
            } else {
                $delete = $wpdb->delete( $tablename, $where, $where_format );
                $insert = $wpdb->insert( $tablename, $data, $data_format );
                return ( false === $delete || false === $insert ?
                    'Error: Domain Mapping table not updated upon save.' : '' );
            }

        } else {
            return 'Invalid URL: Domain Mapping table not updated upon save.';
        }
    }
}

/*
 * Get domain_mapping row by lpage_id
 */
function seedprod_pro_get_domain_mapping_domain() {
    if (check_ajax_referer('seedprod_nonce')) {
        $lpage_id  = absint($_POST['lpage_id']);

        if ( empty($lpage_id) ) {
            $response = false;
            wp_send_json($response, 400);
        } else {
            global $wpdb;
            $tablename = $wpdb->prefix . 'sp_domain_mapping';
            $sql = "SELECT * FROM $tablename WHERE mapped_page_id = %d LIMIT 1";
            $safe_sql = $wpdb->prepare($sql, absint($lpage_id));
            $response = $wpdb->get_row($safe_sql);
            wp_send_json($response);
        }
    }
}

