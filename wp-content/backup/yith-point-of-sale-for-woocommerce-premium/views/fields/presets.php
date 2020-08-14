<?php
// Exit if accessed directly
! defined( 'YITH_POS' ) && exit();

extract( $field );
$min_max_attr = '';
$step_attr = '';
$slot_num = isset( $slot_num ) ? $slot_num : 4;
if ( isset( $min ) ) {
	$min_max_attr .= " min='{$min}'";
}

if ( isset( $max ) ) {
	$min_max_attr .= " max='{$max}'";
}

if ( isset( $step ) ) {
	$step_attr .= "step='{$step}'";
}
for ( $i = 0; $i < $slot_num; $i ++ ):

	?>
    <input type="number" id="<?php echo $id . '_' . $i ?>" class="presets <?php echo esc_attr( $class ) ?>"
           name="<?php echo $name ?>[]" <?php echo $step_attr ?> <?php echo $min_max_attr ?>
           value="<?php echo isset( $value[ $i ] ) ? esc_attr( $value[ $i ] ) : esc_attr( $std[ $i ] ) ?>"
	       <?php if ( isset( $std ) ) : ?>data-std="<?php echo $std[ $i ] ?>"<?php endif ?>
		<?php echo $custom_attributes ?>
		<?php if ( isset( $data ) ) {
			echo yith_plugin_fw_html_data_to_string( $data );
		} ?>/>
<?php endfor; ?>