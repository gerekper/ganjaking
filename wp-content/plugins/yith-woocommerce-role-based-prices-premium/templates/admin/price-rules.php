<?php
if (!defined('ABSPATH'))
    exit;

global $YITH_Role_Based_Prices;

$admin_url = admin_url('post-new.php');

$table = new YITH_Role_Based_Prices_Table(array());
$params = array(
    'post_type' => 'yith_price_rule'
);

$add_new_url = esc_url(add_query_arg($params, $admin_url));
?>

<div class="wrap">
    <h2>
        <?php _e('Price rules', 'yith-woocommerce-role-based-prices'); ?> <a href="<?php echo $add_new_url; ?>" class="add-new-h2"><?php echo YITH_Role_Based_Type()->get_taxonomy_label('add_new'); ?></a>
    </h2>
   <?php
    $table->prepare_items();
    $table->views();
   ?>
    <form method="post">
        <?php
            $table->display();
        ?>
    </form>

</div>
