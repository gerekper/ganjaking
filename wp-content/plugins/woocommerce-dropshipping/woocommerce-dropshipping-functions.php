<?php

add_action('admin_head', 'wp_my_custom_fonts');
function wp_my_custom_fonts() {
    
    if( is_user_logged_in() ) {
        $user = wp_get_current_user();
        $roles = ( array ) $user->roles;
 
        if($roles[0] == "dropshipper"){
            echo '<style>
               @media (max-width: 680px){
            #adminmenuwrap {
                display: block!important;
                max-width: 56px!important;
                width: 100%!important;
            }
            
            #wpcontent{
                padding-left: 67px!important;
            }
            .auto-fold #adminmenu {
                
                max-width: 60px!important;
            }
            
            .auto-fold #adminmenu .wp-menu-name {
                position: static!important;
                display: none!important;
            }
            #collapse-menu {
                display: block !important;
            }
            
            }
            </style>';
        }
    }  
}

if (!function_exists('supplier_admins_mobile_menu')) {

    function supplier_admins_mobile_menu() {
        if( is_user_logged_in() ) {
            $user = wp_get_current_user();
            $roles = ( array ) $user->roles;
         
            if($roles[0] == "dropshipper"){
                wp_enqueue_style('admin-styles', plugins_url( '/assets/css/supplier_mobile_menu.css', __FILE__ ));
            }
        }
    }
    add_action('admin_enqueue_scripts', 'supplier_admins_mobile_menu');   
}

if (!function_exists('generate_aliexpress_key')) {

    function generate_aliexpress_key($domain) {

        $passphrase = '107029c9969d644eca7321f9c4df2e6b';

        $key = md5($domain . $passphrase);

        return $key;

    }

} // Generate aliexpress api key and send to the admin mailbox. 

if (!function_exists('get_dropship_option')) {

    function get_dropship_option() {

        $dOptions = get_option('opmc_dropshipping_options');

        if ($dOptions !== false && is_array($dOptions) && !empty($dOptions)) {

            return $dOptions;

        } else {

            return array();

        }

    }

}

if (!function_exists('update_dropship_option')) {

    function update_dropship_option($dOptions) {

        if (is_array($dOptions)) {

            update_option('opmc_dropshipping_options', $dOptions);

        }

    }

}

if (!function_exists('wc_dropshipping_get_dropship_supplier')) {

    function wc_dropshipping_get_dropship_supplier($id = '') {

        $term = get_term(intval($id), 'dropship_supplier');

        $supplier = get_term_meta(intval($id), 'meta', true);

        if (isset($term->term_id)) {

            $supplier['id'] = $term->term_id;

            $supplier['slug'] = $term->slug;

            $supplier['name'] = $term->name;

            $supplier['description'] = $term->description;

        }

        return $supplier;

    }

}

if (!function_exists('wc_dropshipping_get_dropship_supplier_by_product_id')) {

    function wc_dropshipping_get_dropship_supplier_by_product_id($product_id) {

        $supplier = array();

        $productdata = get_post_meta($product_id, '_virtual', true);

        if ($productdata != 'yes') {

            $terms = get_the_terms(intval($product_id), 'dropship_supplier');

        }

        if (isset($terms)) {

            if ($terms && !is_wp_error($terms) && 0 < count($terms)) {

                $supplier = wc_dropshipping_get_dropship_supplier(intval($terms[0]->term_id)); // load the term. there can only be one supplier notified per product

            }

        }

        return $supplier;

    }

}

if (!function_exists('wc_dropshipping_get_base_path')) {

    function wc_dropshipping_get_base_path() {

        return plugin_dir_path(__FILE__);

    }

}

add_action('wp_ajax_woocommerce_dropshippers_pod_received', 'pod_received_callback');

add_action('wp_ajax_nopriv_woocommerce_dropshippers_pod_received', 'pod_received_callback');

add_action('wp_ajax_woocommerce_dropshippers_mark_as_shipped', 'woocommerce_dropshippers_mark_as_shipped_callback');

add_action('wp_ajax_nopriv_woocommerce_dropshippers_mark_as_shipped', 'woocommerce_dropshippers_mark_as_shipped_callback');

function pod_received_callback() {

    $order_id = $_GET['orderid'];

    $supplier_id = $_GET['supplierid'];

    $order_number = $_GET['order_number'];

    update_post_meta($order_id, $order_number . '_' . $supplier_id . '_status', 'received');

    header('Location:' . $_GET['return'] . 'admin.php?page=dropshipper-order-list');

    die;

}

function woocommerce_dropshippers_mark_as_shipped_callback() {

    $order_id = $_GET['orderid'];

    $supplier_id = @$_GET['supplierid'];

    $order_number = @$_GET['order_number'];

    $dKey = 'order_' . $order_id;

    $dOptions = get_dropship_option();

    if (!isset($dOptions[$dKey])) {

        echo '<p style="color:red;">Sorry for inconvenience: This link will work only for the newly placed orders!</p>';

        wp_die();

    }

    $shipping_status = $dOptions[$dKey]['shipping_status'];

    $my_wc_order = new WC_Order($order_id);

    $my_wc_order_number = $my_wc_order->get_order_number();

    if ($shipping_status == 'completed') {

        $my_wc_order->update_status('completed');

    } else {

        $dOptions[$dKey][$supplier_id] = 'completed';

        $dFlag = true;

        $dFlag1 = "yes";

        foreach ($dOptions[$dKey] as $k => $v) {

            //echo '-->'.$k.'<=='.$supplier_id.'==>'.$v;

            if ($k != 'shipping_status' && $v == 'processing') {

                $dFlag = false;

                $dFlag1 = "no";

            }

        }

        if ($dFlag === true) {

            $dOptions[$dKey]['shipping_status'] = 'completed';

            $my_wc_order->update_status('completed');

        }

        update_dropship_option($dOptions);

    }

    //$my_wc_order->update_status('completed');

    echo '<h1>Order #' . $order_id . '</h1>';

    echo '<p style="color:green;">Order has been notified as shipped for this supplier, and Marked as Completed if required.</p>';

    if (isset($_GET['return'])) {

        if ($dFlag1 == 'no') {

            header('Location:' . $_GET['return'] . 'admin.php?page=dropshipper-order-list&success=' . $dFlag1);

        } else {

            header('Location:' . $_GET['return'] . 'admin.php?page=dropshipper-order-list&success=' . $dFlag1);

        }

    }

    die;

}

function dropshipper_order_list() {	

    $base_name = explode('/', plugin_basename(__FILE__));

    wp_enqueue_style('wc_dropshipping_checkout_style', plugins_url() . '/' . $base_name[0] . '/assets/css/custom.css');

    global $wpdb;

    $current_user = wp_get_current_user();

    $uid = $current_user->ID;

    $uemail = $current_user->user_email;

    $sid = get_user_meta($uid, 'supplier_id', true);

    $term = get_term_by('id', $sid, 'dropship_supplier');
	
	

    if (!empty($term)) {

        $paged = isset($_GET['paged']) ? $_GET['paged'] : 1;
        $post_status = array('wc-processing', 'wc-completed', 'wc-on-hold');

        $options = get_option( 'wc_dropship_manager' );
        $hide_client_info_Suppliers = $options['hide_client_info_Suppliers'];
        $hide_contact_info_Suppliers = $options['hide_contact_info_Suppliers'];
        
        $store_add_shipping_add = $options['store_add_shipping_add'];
        $specific_deli_location = $options['specific_deli_location'];
        
        if ( isset ( $_POST['dateFrom'] ) && isset( $_POST['dateTo'] ) ) {
            $getFromdateIn = $_POST['dateFrom'];
            $getTodateIn = $_POST['dateTo'];
			$post_per_page = $_POST['order_per_page'];
        } else if ( isset ( $_GET['fromDate'] ) && isset( $_GET['toDate'] ) ) {
            $getFromdateIn = $_GET['fromDate'];
            $getTodateIn = $_GET['toDate'];
			$post_per_page = $_GET['perPage'];
        } else {
            $getFromdateIn = date('Y-m-d',strtotime("-7 days"));
            $getTodateIn = date('Y-m-d');
			$post_per_page = 10;
        }
		
		$getFromdateInp = strtotime($getFromdateIn);
		$getFromdateInp = date("Y-m-d",strtotime("-1 day",$getFromdateInp));
		$getTodateInp = strtotime($getTodateIn);
		$getTodateInp = date("Y-m-d",strtotime("+1 day",$getTodateInp));

        if (isset($_POST['dateFrom']) && isset($_POST['dateTo'])) {

            // From Date
            $dateFrom = $_POST['dateFrom'];
            // From Date
            $dateTo = $_POST['dateTo'];
            //update_option( 'dateTo', $dateTo );
            
            $args = array(

                'post_type' => 'shop_order',

                'post_status' => $post_status,

                'posts_per_page' => $post_per_page,

                'date_query' => array(
                        'column' => 'post_date',
						'after' =>  $getFromdateInp,
						'before' => $getTodateInp
                    ),
                'meta_query' => array(
                    array(
                        'key' => 'dropship_supplier_' . $term->term_id,
                        'value' => $term->term_id
                    )
                )
            );

        } else {			

            $args = array(

                'post_type' => 'shop_order',
                'post_status' => $post_status,
                'posts_per_page' => $post_per_page,
                'paged' => $paged,
                'date_query' => array(
                    'column' => 'post_date',
					'after' =>  $getFromdateInp,
					'before' => $getTodateInp
                ),
                'meta_query' => array(
                    array(
                        'key' => 'dropship_supplier_' . $term->term_id,
                        'value' => $term->term_id
                    )
                )
            );
        }
        
        //echo '<pre>'; print_r($args); echo '</pre>';
        $the_query = new WP_Query($args);
		
        echo '<div class="wrap">

            <h1>Supplier Orders</h1>

            <form name="Filter" method="POST" action="' . get_site_url() . '/wp-admin/admin.php?page=dropshipper-order-list">
                <table>
                    <tr>
                        From:
                        <input type="date" name="dateFrom" value="'.$getFromdateIn.'" />
                        To:
                        <input type="date" name="dateTo" value="'.$getTodateIn.'" />
						Number of items per page:
						<input type="number" min="5" max="50" name="order_per_page" value="'.$post_per_page.'" style="width: 55px;margin-right: 5px;" /> 		
                        <input type="submit" class="button button-primary" name="submit" value="Filter"/>
                    <tr>
                </table>
            </form>
        
            <table class="wp-list-table widefat fixed striped posts">

                <thead>
                    <tr>

                        <th scope="col" id="id" class="manage-column column-id column-primary sortable desc" style="width: 5%;padding-left: 10px;">ID</th>
                        <th scope="col" id="date" class="manage-column column-date" style="width: 7%;">Date</th>
                        <th scope="col" id="product" class="manage-column column-product">Product</th>';
                        
                        if( $hide_client_info_Suppliers == 1 ){
                        echo'<th scope="col" id="client" class="manage-column column-client-info" style="display:none;">Client Info</th>';
                        }else{
                            echo'<th scope="col" id="client" class="manage-column column-client-info" >Client Info</th>';
                        }

                        if( $hide_contact_info_Suppliers == 1 ){
                            echo'<th scope="col" id="contact_info" class="manage-column column-contact-info" style="display:none;">Contact Info</th>';
                            }else{
                                echo'<th scope="col" id="contact_info" class="manage-column column-contact-info">Contact Info</th>';
                        }



                        echo '
                        <th scope="col" id="shipping" class="manage-column column-shipping-info">Shipping Info</th>
                        <th scope="col" id="pod_header" class="manage-column column-pod">POD</th>
                        <th scope="col" id="status" class="manage-column column-status-info">Status</th>
                    </tr>

                </thead>

                <tbody id="the-list">';

                $user_id = get_current_user_id();

                $client_info_supp = '';

                $supplier_id = get_user_meta($user_id, 'supplier_id');

                function show_pod_content($meta_value, $order_id, $supplier_pod_id, $pod_ajax_url) {

                    if ($meta_value == 'received') {

                        return 'Received';

                    } else {

                        return 'Not Received </br> </br> <a href="' . $pod_ajax_url . '" id="pod_received_' . $order_id . '_' . $supplier_pod_id . '" class="button button-primary" href="" style="margin-top:2px">Mark as Received</a>';

                    }

                }

                if ($the_query->have_posts()) {

                    while ($the_query->have_posts()) : $the_query->the_post();

                        $order = wc_get_order(get_the_ID());
                        $new_order_id = $order->get_order_number();
                        $order_number = $new_order_id;
                        $supplier_pod_id = '_supplier_pod_' . get_current_user_id();
                        $supplier_pod = get_post_meta($order_number, $order_number . '_' . $supplier_pod_id . '_status', true);
                        $items = $order->get_items();
                        $store_address     = get_option( 'woocommerce_store_address' );

                        $fake_ajax_url = wp_nonce_url(admin_url('admin-ajax.php?action=woocommerce_dropshippers_mark_as_shipped&return=' . admin_url() . '&orderid=' . get_the_ID() . '&supplierid=' . @$supplier_id[0]), 'woocommerce_dropshippers_mark_as_shipped');

                        $pod_ajax_url = wp_nonce_url(admin_url('admin-ajax.php?action=woocommerce_dropshippers_pod_received&return=' . admin_url() . '&orderid=' . get_the_ID() . '&supplierid=' . $supplier_pod_id . '&order_number=' . $order_number), 'woocommerce_dropshippers_pod_received');

                        $upload_dir = wp_get_upload_dir();

                        $pdfpath = $upload_dir['baseurl'] . '/' . $new_order_id . '/' . $new_order_id . '_' . $term->slug . '.pdf';

                        $dropshipper_shipping_info = get_post_meta($new_order_id, 'dropshipper_shipping_info_' . get_current_user_id(), true);

                        $supplier_id = 'dropshipper_shipping_info_' . get_current_user_id();

                        if (!$dropshipper_shipping_info) {

                            $dropshipper_shipping_info = array(
                                'date' => '',
                                'tracking_number' => '',
                                'shipping_company' => '',
                                'notes' => ''
                            );

                        }

                        echo '<tr><td class="id column-id" data-colname="id">' . $new_order_id . '</td>

                            <td class="date column-date" data-colname="date">' . get_the_date() . '</td>';

                        echo '<td class="product column-product" data-colname="product">';

                        if (count($items) > 0) {

                            foreach ($items as $item_id => $item) {

                                $ds = wc_dropshipping_get_dropship_supplier_by_product_id(intval($item['product_id']));

                                if( is_array ( $ds ) && !empty( $ds ) ) {
                                    if ($ds['order_email_addresses'] == $uemail) {
                                        echo '<p>' . $product_name = $item->get_name() . '</p>';
                                    }
                                }
                            }
                        }

                        echo '</td> ';

                        if( $hide_client_info_Suppliers == 1 ){
                            
                            echo '<td class="client column-client" data-colname="client" style="display:none;" >' . $order->get_formatted_shipping_address() .'</td>';
                        }else{
                            
                            echo '<td class="client column-client" data-colname="client">' . $order->get_formatted_shipping_address() . '</td>';
                        }

                        if( $hide_contact_info_Suppliers == 1 ){
                            echo'<td class="client-email column-client-email" data-colname="client-email" style="display:none;">' . $order->get_billing_email() . '<br><div class="row-actions"><span><a href="mailto:' . $order->get_billing_email() . '">Send an Email</a></span></div></td>';
                        }else{
                                echo'<td class="client-email column-client-email" data-colname="client-email">' . $order->get_billing_email() . '<br><div class="row-actions"><span><a href="mailto:' . $order->get_billing_email() . '">Send an Email</a></span></div></td>';
                        }

                           echo '

                            <td class="shipping column-shipping" data-colname="shipping">

                            <p>Date: ' . $dropshipper_shipping_info['date'] . '</p>

                            <p>Tracking number: ' . $dropshipper_shipping_info['tracking_number'] . '</p>

                            <p>Shipping Company: ' . $dropshipper_shipping_info['shipping_company'] . '</p>

                            <p>Notes: ' . $dropshipper_shipping_info['notes'] . '</p>

                            <br>

                            <button id="open_dropshipper_dialog_' . $new_order_id . '" class="button button-primary" onclick="open_dropshipper_dialog(' . $new_order_id . ')" style="margin-top:2px">Edit Shipping Info</button>

                            </td>

                            <td class="client-email column-client-email" data-colname="client-email">' . show_pod_content($supplier_pod, $new_order_id, $supplier_pod_id, $pod_ajax_url) . '<br><div class="row-actions"><span></div></td>

                            <td class="status column-status" data-colname="status">' . $order->get_status() . '<br>';

                                if ($order->get_status() != 'completed') {

                                    echo '<a id="mark_dropshipped_' . $new_order_id . '" class="button button-primary" href="' . $fake_ajax_url . '" style="margin-top:2px">Mark as Complete</a>
                                    <br>';
                                }

                                echo '<a href="' . $pdfpath . '" target="blank" id="print_slip_' . $new_order_id . '" class="button button-primary" style="margin-top:2px">Download packing slip</a>
                            </td>
                        </tr>';

                    /* } */

                    endwhile;
                    
                } else {

                     echo '<tr><h3>Records not found on selected date.</h3></tr>';
                }

                wp_reset_query();

                echo '</tbody>

                <tfoot>

                    <tr>

                        <th scope="col" id="id" class="manage-column column-id column-primary sortable desc">ID</th>

                        <th scope="col" id="date" class="manage-column column-date">Date</th>

                        <th scope="col" id="product" class="manage-column column-product">Product</th>';

                        
                        
                        if( $hide_client_info_Suppliers == 1 ){
                        echo'<th scope="col" id="client" class="manage-column column-client-info" style="display:none;">Client Info</th>';
                        }else{
                            echo'<th scope="col" id="client" class="manage-column column-client-info" >Client Info</th>';
                        }

                        if( $hide_contact_info_Suppliers == 1 ){
                            echo'<th scope="col" id="contact_info" class="manage-column column-contact-info" style="display:none;">Contact Info</th>';
                            }else{
                                echo'<th scope="col" id="contact_info" class="manage-column column-contact-info">Contact Info</th>';
                        }



                        echo '
                        <th scope="col" id="shipping" class="manage-column column-shipping-info">Shipping Info</th>
                        <th scope="col" id="pod_header" class="manage-column column-pod">POD</th>
                        <th scope="col" id="status" class="manage-column column-status-info">Status</th>
                    </tr>

                </tfoot>

            </table>';

            echo "<nav class=\"sw-pagination\">";
            $big = 999999999;
            $link = str_replace($big, '%#%', esc_url(get_pagenum_link($big)));
            $link = str_replace('#038;', '&', $link);
            echo paginate_links(array(
               'base' => $link,
               //'format' => '?paged=%#%',
               'add_args' => array('fromDate'=>$getFromdateIn,'toDate'=>$getTodateIn,'perPage'=>$post_per_page),
               'current' => max(1, $paged),
               'total' => $the_query->max_num_pages
            ));

            echo '</nav></div>';

    } else {

        echo '<div class="wrap">

            <h1>Supplier Orders</h1>

            <table class="wp-list-table widefat fixed striped posts">

                <thead>

                    <tr>

                        <th scope="col" id="id" class="manage-column column-id column-primary sortable desc">ID</th>

                        <th scope="col" id="date" class="manage-column column-date">Date</th>

                        <th scope="col" id="product" class="manage-column column-product">Product</th>
                        <th scope="col" id="client" class="manage-column column-client-info" >Client Info</th>';
                       

                        echo '<th scope="col" id="contact_info" class="manage-column column-contact-info">Contact Info</th>
                        <th scope="col" id="shipping" class="manage-column column-shipping-info">Shipping Info</th>
                        <th scope="col" id="pod_header" class="manage-column column-pod">POD</th>
                        <th scope="col" id="status" class="manage-column column-status-info">Status</th>
                    </tr>

                </thead>
                <tbody id="the-list">

                    <tr><td class="id column-id" colspan="8">No dropshipper has assigned to this user.</td></tr>
                </tbody>

            </table>

        </div>';

    }

    echo '<link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.0/themes/smoothness/jquery-ui.css" />  

    <div id="input-dialog-template" style="display:none">

    <label for="input-dialog-date"><label for="input-dialog-date">Date</label></label>

    <input type="text" name="input-dialog-date" id="input-dialog-date" style="width:100%">

    <label for="input-dialog-trackingnumber"><label for="input-dialog-trackingnumber">Tracking Number(s)</label></label>

    <textarea name="input-dialog-trackingnumber" id="input-dialog-trackingnumber" style="width:100%"></textarea>

    <label for="input-dialog-shippingcompany"><label for="input-dialog-shippingcompany">Shipping Company</label></label>

    <textarea name="input-dialog-shippingcompany" id="input-dialog-shippingcompany" style="width:100%"></textarea>

    <label for="input-dialog-notes"><label for="input-dialog-notes">Notes</label></label>

    <textarea name="input-dialog-notes" id="input-dialog-notes" style="width:100%"></textarea>

    </div>';
}

if (isset($_GET['success'])) {

    if (@$_GET['success'] == 'no') {

        echo '<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">

            <!-- Modal -->

            <div class="modal" id="complete_order_mark_Modal">

            <div class="modal-dialog">

            <div class="modal-content"> 

            <!-- Modal Header -->

            <div class="modal-header">

            <h4 class="modal-title"></h4>

            <button type="button" class="close" data-dismiss="modal">&times;</button>

            </div> 

            <!-- Modal body -->

            <div class="modal-body">

            Thank you for completing this order. It will be under process untill all other dropshippers mark this order as

            complete.

            </div> 

            <!-- Modal footer -->

            <div class="modal-footer">

            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>

            </div> 

            </div>

            </div>

        </div>';

    }

}
