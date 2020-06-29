<!DOCTYPE html>
<html>
<head>
    <title><?php printf(__('RMA Request #%d - %s', 'wc_warranty'), $warranty['ID'], get_bloginfo('name')); ?></title>
    <link rel="stylesheet" media="print" href="<?php echo plugins_url( 'assets/css/print.css', WooCommerce_Warranty::$plugin_file ); ?>" />
    <style>
        body {
            font-family: Trebuchet MS, Tahoma, Verdana, Arial, sans-serif;
            width: 800px;
        }
        #header {
            padding: 10px 30px;
        }
        
        #header img {
            max-height: 200px;
        }

        h2 {
            text-align: center;
            margin: 60px 0 0px 0;
        }

        table.details {
            padding-top: 50px;
            width: 400px;
            margin: 0 auto;
        }
        table th {
            text-align: left;
        }
        .print {
            float: right;
            background-color: #f2f2f2;
            border: 1px solid #bbb;
            border-radius: 11px;
            color: #000;
            display: block;
            font-size: 0.9em;
            height: 22px;
            line-height: 22px;
            margin-top: 7px;
            padding-left: 20px;
            padding-right: 20px;
            text-decoration: none;
            width: 30px;
        }
    </style>
</head>
<body onload="window.print()">
<a class="print" href="#" onclick="window.print()">Print</a>
<div id="header">
    <?php if ( $logo ): ?>
    <img class="logo" src="<?php echo esc_attr($logo); ?>" />
    <?php else: ?>
    <h1><?php bloginfo('name'); ?></h1>
    <?php endif; ?>

    <?php if ( $show_url == 'yes' ): ?>
    <p><small><a href="<?php bloginfo('url'); ?>"><?php bloginfo('url'); ?></a></small></p>
    <?php endif; ?>
</div>
<div id="content">
    <h2><?php printf(__('RMA Request #%d', 'wc_warranty'), $warranty['ID']); ?></h2>

    <table class="borderless details" cellpadding="5">
        <tr>
            <th><?php _e('Date', 'wc_warranty'); ?>:</th>
            <td><?php echo date_i18n( get_option('date_format') .' '. get_option('time_format'), strtotime( $warranty['post_modified'] ) ); ?></td>
        </tr>
        <tr>
            <th><?php _e('Order Number', 'wc_warranty'); ?>:</th>
            <td><?php echo ($order) ? $order->get_order_number() : '-'; ?></td>
        </tr>
        <tr>
            <th><?php _e('Customer', 'wc_warranty'); ?>:</th>
            <td><?php echo $first_name .' '. $last_name .' &ndash; '. $email .''; ?></td>
        </tr>
        <tr>
            <th><?php _e('Product', 'wc_warranty'); ?>:</th>
            <td><?php echo $product_name; ?></td>
        </tr>
        <tr>
            <th><?php _e('RMA #', 'wc_warranty'); ?>:</th>
            <td><?php echo $warranty['code']; ?></td>
        </tr>

        <?php
        foreach ( $inputs as $input ) {
            if ( $input->type == 'paragraph') {
                continue;
            }

            $field = $form['fields'][$input->key];
            $value = (isset($warranty['field_'. $input->key])) ? $warranty['field_'. $input->key] : '-';
            ?>
            <tr>
                <th><?php echo $field['name']; ?>:</th>
                <td><?php echo wp_kses_post( $value ); ?></td>
            </tr>
        <?php
        }
        ?>
        <tr>
            <th><?php _e('Tracking', 'wc_warranty'); ?>:</th>
            <td><?php echo $tracking_html; ?></td>
        </tr>
    </table>
</div>

</body>
</html>