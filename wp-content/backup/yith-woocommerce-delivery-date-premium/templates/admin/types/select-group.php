<?php
if( !defined( 'ABSPATH')){
	exit;
}
wp_enqueue_script( 'wc-enhanced-select' );

extract( $field );
$multiple      = isset( $multiple ) && $multiple;
$multiple_html = ( $multiple ) ? ' multiple' : '';

if ( $multiple && !is_array( $value ) )
	$value = array();

$class = isset( $class ) ? $class  : '';
?>
	<select<?php echo $multiple_html ?>
		id="<?php echo $id ?>"
		name="<?php echo $name ?><?php if ( $multiple ) echo "[]" ?>" <?php if ( isset( $std ) ) : ?>
		data-std="<?php echo ( $multiple ) ? implode( ' ,', $std ) : $std ?>"<?php endif ?>
		class="wc-enhanced-select <?php echo $class;?>"
		<?php echo $custom_attributes ?>
		<?php if ( isset( $data ) ) echo yith_plugin_fw_html_data_to_string( $data ); ?>>
			<?php foreach( $groups as $group => $options ) :?>
			<optgroup label="<?php echo $options['label'];?>">
				<?php foreach ( $options['options'] as $key => $item ) : ?>
					<option value="<?php echo esc_attr( $key ) ?>" <?php if ( $multiple ): selected( true, in_array( $key, $value ) );
					else: selected( $key, $value ); endif; ?> ><?php echo $item ?></option>
				<?php endforeach; ?>
			</optgroup>
			<?php endforeach;?>
	</select>

<?php
/* --------- BUTTONS ----------- */

	$button_field = array(
		'type'    => 'buttons',
		'buttons' => array(
			array(
				'name'  => __( 'Select All', 'yith-plugin-fw' ),
				'class' => 'yith-plugin-fw-select-all',
				'data'  => array(
					'select-id' => $field[ 'id' ]
				),
			),
			array(
				'name'  => __( 'Deselect All', 'yith-plugin-fw' ),
				'class' => 'yith-plugin-fw-deselect-all',
				'data'  => array(
					'select-id' => $field[ 'id' ]
				),
			)
		)
	);
	yith_plugin_fw_get_field( $button_field, true );
