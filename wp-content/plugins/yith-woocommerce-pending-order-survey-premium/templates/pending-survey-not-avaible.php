<?php
if( !defined('ABSPATH'))
    exit;
get_header( 'shop');
?>

<div id="survey_not_avaible" class="woocommerce">
    <h1><?php the_title();?></h1>
    <div class="survey_error woocommerce-error">
        <?php
            $message = sprintf('%s #%s', __('You have already answer this survey for this order',
                'yith-woocommerce-pending-survey' ), $_GET['order_id'] );
        echo $message;
        ?>
    </div>
</div>
<?php
get_footer('shop');