<?php
if( !defined('ABSPATH'))
    exit;

$args = array(

    'gen_backup' => 'yes'
);



$url = esc_url( add_query_arg( $args ) );
?>
<tr valign="top">
    <th scope="row" class="titledesc"><?php _e( 'Generate a complete backup', 'yith-woocommerce-watermark');?></th>
    <td class="forminp">
        <a href="<?php echo $url;?>" class="button button-primary"><?php _e('Backup','yith-woocommerce-watermark');?></a>
    </td>
</tr>
<?php
