<?php

/*
 * subscribers Datatable
 */
function seedprod_pro_subscribers_datatable()
{
    if (check_ajax_referer('seedprod_nonce')) {
        $data = array('');
        $current_page = 1;
        if (!empty(absint($_GET['current_page']))) {
            $current_page = absint($_GET['current_page']);
        }
        $per_page=100;

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

        $results = array();
        
        global $wpdb;
        $tablename = $wpdb->prefix . 'csp3_subscribers';

        // Get records

        $sql = "SELECT *
             FROM $tablename 
             ";
     
        if(!empty($_GET['id'])){
            $sql .= ' WHERE page_uuid = "'.esc_sql($_GET['id']). '"';;
        }else{
            $sql .= ' WHERE 1 =1 ';
        }
     
        if (!empty($_GET['s'])) {
            $sql .= ' AND email LIKE "%'. esc_sql(trim(sanitize_text_field($_GET['s']))).'%"';
        }
     
        if (! empty($_GET['orderby'])) {
            // $orderby = $_GET['orderby'];
            // if ($_GET['orderby'] == 'entries') {
            //     $orderby  = 'entries_count';
            // }
            // $sql .= ' ORDER BY ' . esc_sql(sanitize_text_field($orderby));
            // if(sanitize_text_field($_GET['order']) === 'desc'){
            //     $order = 'DESC';
            // }else{
            //     $order = 'ASC';
            // }
            // $sql .=  ' ' . $order;
        } else {
            $sql .= ' ORDER BY created DESC';
        }
     
        $sql .= " LIMIT $per_page";
        if (empty($_GET['s'])) {
            $sql .= ' OFFSET ' . ($current_page - 1) * $per_page;
        }
      
        $results = $wpdb->get_results($sql);
        
        //var_dump($results);
        $data = array();
        foreach ($results as $v) {
     
                // Format Date
        $created_at = date(get_option('date_format').' '.get_option('time_format'), strtotime($v->created));


            // Load Data
            $data[] = array(
                     'id' => $v->id,
                     'email' => $v->email,
                     'name' => $v->fname.' '.$v->lname,
                     'created_at' => $created_at,
                     'page_uuid' => $v->page_uuid,
                     );
        }

        $totalitems = 0;
        $views = array();
         
        $totalitems = seedprod_pro_subscribers_get_data_total($filter);
        $views = seedprod_pro_subscribers_get_views($filter);
         

        // Get recent subscriber data
        $chart_timeframe = 7;
        if(!empty($_GET['interval'])){
            $chart_timeframe = absint($_GET['interval']);
        }

        $recent_subscribers = array();
        
        if (empty($_GET['id'])) {
            $tablename = $wpdb->prefix . 'csp3_subscribers';
            $sql = 'SELECT count(id) as count,DATE_FORMAT(created,"%Y-%m-%d") as created FROM '.$tablename.' ';
            $sql .= ' WHERE created >= DATE(NOW()) - INTERVAL '.esc_sql($chart_timeframe).' DAY GROUP BY DAY(created)';
            $recent_subscribers  = $wpdb->get_results($sql);


        } else {

            $tablename = $wpdb->prefix . 'csp3_subscribers';
            $sql = 'SELECT count(id) as count,DATE_FORMAT(created,"%Y-%m-%d") as created FROM '.$tablename.' ';
            $sql .= ' WHERE page_uuid = "'.esc_sql($_GET['id']). '"';
            $sql .= ' AND created >= DATE(NOW()) - INTERVAL '.esc_sql($chart_timeframe).' DAY GROUP BY DAY(created)';
            $recent_subscribers  = $wpdb->get_results($sql);
        }
        
        

        $now = new \DateTime("$chart_timeframe days ago", new \DateTimeZone('America/New_York'));
        $interval = new \DateInterval('P1D'); // 1 Day interval
        $period = new \DatePeriod($now, $interval, $chart_timeframe); // 7 Days

        $recent_subscribers_data = array(
            array("Year","Subscribers"),
        );
        foreach ($period as $day) {
            $key = $day->format('Y-m-d');
            $display_key = $day->format('M j');
            $no_val = true;
            foreach ($recent_subscribers as $v) {
                if ($key == $v->created) {
                    $recent_subscribers_data[] = array($display_key,absint($v->count));
                    $no_val = false;
                }
            }
            if ($no_val) {
                $recent_subscribers_data[] = array($display_key,0);
            }
        }
     
        $response = array(
                 'recent_subscribers' => $recent_subscribers_data,
                 'rows' => $data,
                 'lpage_name' => '',
                 'totalitems' => $totalitems,
                 'totalpages' => ceil($totalitems/$per_page),
                 'currentpage'=> $current_page,
                 'views'=>$views,
             );
     
        wp_send_json($response);
    }
}

function seedprod_pro_subscribers_get_data_total($filter = null)
{
    global $wpdb;

    $tablename = $wpdb->prefix . 'csp3_subscribers';

    $sql = "SELECT count(id) FROM $tablename";

    if(!empty($_GET['id'])){
        $sql .= ' WHERE page_uuid = '.esc_sql($_GET['id']);
    }else{
        $sql .= ' WHERE 1 =1 ';
    }

    if (!empty($_GET['s'])) {
        $sql .= ' AND email LIKE "%'. esc_sql(trim(sanitize_text_field($_GET['s']))).'%"';
    }

    $results = $wpdb->get_var($sql);
    return $results;
}

function seedprod_pro_subscribers_get_views($filter = null)
{
    $views = array();
    $current = (!empty($filter) ? $filter : 'all');

    global $wpdb;
    $tablename = $wpdb->prefix . 'csp3_subscribers';

    //All link
    $sql = "SELECT count(id) FROM $tablename";

    if(!empty($_GET['id'])){
        $sql .= ' WHERE lpage_id = '.esc_sql($_GET['id']);
    }else{
        $sql .= ' WHERE 1 =1 ';
    }

    $results = $wpdb->get_var($sql);
    $class = ($current == 'all' ? ' class="current"' :'');
    $all_url = remove_query_arg('filter');
    $views['all'] = $results;

    return $views;
}


/*
* Update Subscriber
*/
function seedprod_pro_update_subscriber_count()
{
    if (check_ajax_referer('seedprod_pro_update_subscriber_count')) {
        update_option('seedprod_subscriber_count', 1);
    } 

}


/*
* Delete Subscribers
*/
function seedprod_pro_delete_subscribers()
{
    if (check_ajax_referer('seedprod_pro_delete_subscribers')) {
        if (current_user_can(apply_filters('seedprod_delete_subscriber_capability', 'list_users'))) {
            $dids = $_POST['items'];
            if (is_array($dids) && !empty($dids)) {
                global $wpdb;
                $tablename = $wpdb->prefix . 'csp3_subscribers';
                $sql = "SELECT id FROM $tablename";
                $sql .= " WHERE id IN ( ".esc_sql(implode(",", $dids))." )";
                $ids = $wpdb->get_col($sql);

                $how_many = count($ids);
                $placeholders = array_fill(0, $how_many, '%d');
                $format = implode(', ', $placeholders);

                //Deleted subscribers
                $tablename = $wpdb->prefix . 'csp3_subscribers';
                $sql = "DELETE FROM " . $tablename . " WHERE id IN ($format)";
                $safe_sql = $wpdb->prepare($sql, $ids);
                $result = $wpdb->query($safe_sql);
                wp_send_json_success();
            }elseif(!empty($dids)){
                // Deleted subscriber
                global $wpdb;
                $tablename = $wpdb->prefix . 'csp3_subscribers';
                $sql = "DELETE FROM " . $tablename . " WHERE id = %d";
                $safe_sql = $wpdb->prepare($sql, $dids);
                $result = $wpdb->query($safe_sql);
                wp_send_json_success();
            }

            wp_send_json_error();

            
        }
    }
}

/*
* Export Contestants
*/
function seedprod_pro_export_subscribers()
{
    if (!empty($_REQUEST['action']) && $_REQUEST['action'] == 'seedprod_pro_export_subscribers') {
        if (!empty($_REQUEST['_wpnonce']) && wp_verify_nonce($_REQUEST['_wpnonce'], 'seedprod_pro_export_subscribers') !== false) {

            global $wpdb;
            if(!empty($_REQUEST['id'])){
                $tablename = $wpdb->prefix . 'csp3_subscribers';
                $sql = "SELECT fname, lname, email, created, page_uuid from $tablename where page_id = %d";
                $safe_sql = $wpdb->prepare($sql, absint($_REQUEST['id']));
                $data = $wpdb->get_results($safe_sql);
            }else{
                $tablename = $wpdb->prefix . 'csp3_subscribers';
                $sql = "SELECT fname, lname, email, created, page_uuid from $tablename";
                $data = $wpdb->get_results($sql);
            }


            $filename = sprintf('%1$s-%2$s-%3$s', 'subscribers', date('Ymd'), date('His'));

            $header = array(
                'First Name',
                'Last Name',
                'Email',
                'Created',
                'Page ID'
            );



            seedprod_pro_export_csv($header, $data, $filename);
        }
    }
}

function seedprod_pro_export_csv($header, $data, $filename)
{
    // No point in creating the export file on the file-system. We'll stream
    // it straight to the browser. Much nicer.

    // Open the output stream
    $fh = fopen('php://output', 'w');

    // Start output buffering (to capture stream contents)
    ob_start();

    // CSV Header
    if (is_array($header)) {
        fputcsv($fh, $header);
    }

    // CSV Data
    foreach ($data as $row) {
        $arow = array();
        foreach ($row as $k => $v) {
            $arow[$k] = $v;
        }
        fputcsv($fh,$arow);
    }

    // Get the contents of the output buffer
    $string = ob_get_clean();

    // Output CSV-specific headers
    header('Pragma: public');
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Cache-Control: private', false);
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . $filename . '.csv";');
    header('Content-Transfer-Encoding: binary');

    // Stream the CSV data
    exit($string);
}
 

function seedprod_pro_subscribe_callback(){
    // get request data

    $email = '';
    if(!empty($_POST['email'])){
        $email = sanitize_email($_POST['email']);
    }

    if(empty($email)){
        wp_send_json_error();
    }

    $page_uuid = '';
    if(!empty($_POST['page_uuid'])){
        $page_uuid = sanitize_text_field($_POST['page_uuid']);
    }

    $page_id = '';
    if(!empty($_POST['page_id'])){
        $page_id = absint($_POST['page_id']);
    }

    $name = '';
    if(!empty($_REQUEST['name'])){
        $name = sanitize_text_field($_REQUEST['name']);
    }

    $fname = '';
    $lname = '';

    if(!empty($name)){
        require_once(SEEDPROD_PRO_PLUGIN_PATH.'app/includes/nameparse.php');
        $name = seedprod_pro_parse_name($name);
        $fname = $name['first'];
        $lname = $name['last'];
    }

    $optin_confirmation = 0;
    if(!empty($_REQUEST['optin_confirmation'])){
        $optin_confirmation = 1;
    }

    // Record user in DB if they do not exist
    global $wpdb;
    $tablename = $wpdb->prefix . 'csp3_subscribers';
    $sql = "SELECT * FROM $tablename WHERE email = %s AND page_uuid = %d";
    $safe_sql = $wpdb->prepare($sql,$email,$page_uuid);
    $select_result =$wpdb->get_row($safe_sql);

    if (empty($select_result->email)) {
        $values = array(
            'email' => $email,
            'page_id' => $page_id,
            'page_uuid' => $page_uuid,
            'ip' => seedprod_pro_get_ip(),
            'fname' => $fname,
            'lname' => $lname,
            'optin_confirm' => $optin_confirmation,
        );
        $format_values = array(
            '%s',
            '%d',
            '%s',
            '%s',
            '%s',
            '%s',
            '%d',
        );
        $insert_result = $wpdb->insert(
            $tablename,
            $values,
            $format_values
        );
    }



    wp_send_json_success();
}




