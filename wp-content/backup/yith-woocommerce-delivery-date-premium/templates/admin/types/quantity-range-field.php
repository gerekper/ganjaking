<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/*$args = array(
	'id' => '',
	'btn_class' => '',
	'name' =>'',
	'values' => array()
);
*/

extract( $field );


$from   = _x( 'from', 'Part of]: For quantity from 50 to 100 set 5 days', 'yith-woocommerce-delivery-date' );
$to     = _x( 'to', '[Part of]: For quantity from 50 to 100 set 5 days', 'yith-woocommerce-delivery-date' );
$set    = _x( 'set', '[Part of]: For quantity from 50 to 100 set 5 days', 'yith-woocommerce-delivery-date' );
$days   = _x( 'days for processing', '[Part of]: For quantity from 50 to 100 set 5 days', 'yith-woocommerce-delivery-date' );
$remove = __( 'Remove range', 'yith-woocommerce-delivery-date' );

$quantity_fields = array(
	'ywcdd_from' => array(
		'id'    => 'from_index_new_need_process_day',
		'name'  => $name . "[index][from]",
		'class' => 'ywcdd_from yith-required-field',
		'label' => $to
	),
	'ywcdd_to'   => array(
		'id'    => 'to_index_new_need_process_day',
		'name'  => $name . "[index][to]",
		'class' => 'ywcdd_to',
		'label' => $set
	),
	'ywcdd_day'  => array(
		'id'    => 'day_index_new_need_process_day',
		'name'  => $name . '[index][day]',
		'class' => 'ywcdd_day yith-required-field',
		'label' => $days
	)

);

$json_encode = '<div class="ywcdd_quantity_item">';
foreach ( $quantity_fields as $class => $qty_field ) {
	$json_encode .= '<span class="ywcdd_single_field">';
	if ( 'ywcdd_from' == $class ) {
		$json_encode .= sprintf('<span>%s</span>',$from );
	}
	$json_encode .= sprintf( '<input id="%s" type="number" class="%s" name="%s" min="0" step="1"><span>%s</span>', $qty_field['id'], $qty_field['class'], $qty_field['name'], $qty_field['label'] );
	if ( 'ywcdd_day' === $class ) {
		$json_encode .= sprintf('<span><a href ="" class="ywcdd_delete_range" title="%s"><span class="yith-icon icon-trash"></span></a></span>', $remove );
	}
	$json_encode .= '</span>';
}
$json_encode .= '</div>';

$json_encode = esc_attr( $json_encode );

$value = ! isset( $value ) || ! is_array( $value ) ? array() : $value;
if ( count( $value ) == 0 ) {

	$value = array(
		array(
			'from' => 1,
			'to'   => '',
			'day'  => 1
		)
	);
}

?>

<div class="ywcdd_quantity_day_container">
    <div class="ywcdd_quantity_list" data-row="<?php echo $json_encode; ?>">
        <div class="ywcdd_quantity_row">
			<?php foreach ( $value as $i => $single_value ): ?>
                <div class="ywcdd_quantity_item">
                    <span class="ywcdd_single_field">
                        <span><?php echo $from;?></span><input id="from_<?php echo $i; ?>_<?php echo $id; ?>" type="number"
                               class="ywcdd_from yith-required-field"
                               name="<?php echo $name; ?>[<?php echo $i; ?>][from]" min="1" step="1"
                               value="<?php echo $single_value['from']; ?>"><span><?php echo $to; ?></span>
                    </span>
                    <span class="ywcdd_single_field">
                        <input type="number" class="ywcdd_to" id="to_<?php echo $i; ?>_<?php echo $id; ?>"
                               name="<?php echo $name; ?>[<?php echo $i; ?>][to]" min="1" step="1"
                               value="<?php echo $single_value['to']; ?>"><span><?php echo $set; ?></span>
                    </span>
                    <span class="ywcdd_single_field">
                    <input type="number" class="ywcdd_day yith-required-field"
                           id="day_<?php echo $i; ?>_<?php echo $id; ?>"
                           name="<?php echo $name; ?>[<?php echo $i; ?>][day]" step="1"
                           value="<?php echo $single_value['day']; ?>" class=""><span><?php echo $days; ?></span>
					<?php if ( $i > 0 ): ?>
                        <a href="" class="ywcdd_delete_range" title="<?php echo $remove; ?>"><span
                                    class="yith-icon icon-trash"></span></a>
					<?php endif; ?>
                    </span>
                </div>
			<?php endforeach; ?>
        </div>
        <div class="ywcdd_add_new_range_container">

            <a href="" class="ywcdd_add_range">
				<?php _e( '+ Add another quantity range', 'yith-woocommerce-delivery-date' ); ?>
            </a>

        </div>
        <span class="description">
          <?php _e( 'Set custom processing days depending on the number of products ordered by the user. <br/>Leave "to" value empty if you want to set a single processing time without changes when increasing the quantity.', 'yith-woocommerce-delivery-date' ); ?>
        </span>

    </div>
</div>
