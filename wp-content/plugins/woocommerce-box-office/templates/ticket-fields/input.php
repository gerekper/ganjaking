<?php echo $before_field; ?>
	<label class="<?php echo esc_attr( $label_class ); ?>" for="<?php echo esc_attr( $id ); ?>">
		<?php echo esc_html( $label ); ?>:
		<?php if ( $required ) : ?>
		<span class="required">*</span>
		<?php endif;?>
	</label>
	<input
		type="<?php echo esc_attr( $type ); ?>"
		class="<?php echo esc_attr( $input_class ); ?>"
		value="<?php echo esc_attr( $value ); ?>" name="<?php echo esc_attr( $name ); ?>"
		id="<?php echo esc_attr( $id ); ?>"<?php disabled( $disabled ); ?>
		<?php echo $required ? 'required': '' ?>
		/>
<?php echo $after_field; ?>
