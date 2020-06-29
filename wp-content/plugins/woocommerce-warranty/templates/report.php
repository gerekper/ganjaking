<?php
$tab_status = (isset($_GET['status'])) ? $_GET['status'] : '';
?>
<div class="wrap woocommerce">
    <h2><?php _e('Reports', 'wc_warranty'); ?></h2>

    <div class="icon32"><img src="<?php echo plugins_url() .'/woocommerce-warranty/assets/images/icon.png'; ?>" /><br></div>
    <h2 class="nav-tab-wrapper woo-nav-tab-wrapper">
        <a href="admin.php?page=warranties-reports" class="nav-tab <?php echo ($tab_status == '') ? 'nav-tab-active' : ''; ?>"><?php _e('Active', 'wc_warranty'); ?></a>
        <a href="admin.php?page=warranties-reports&status=completed" class="nav-tab <?php echo ($tab_status == 'completed') ? 'nav-tab-active' : ''; ?>"><?php _e('Completed', 'wc_warranty'); ?></a>
    </h2>
<?php

if ( empty($tab_status) )
    include WooCommerce_Warranty::$includes_path .'/class.warranty_active_reports_list_table.php';
else
    include WooCommerce_Warranty::$includes_path .'/class.warranty_completed_reports_list_table.php';
