<?php echo $before_field; ?>
	<label class="<?php echo esc_attr( $label_class ); ?>" for="<?php echo esc_attr( $id ); ?>">
		<?php echo esc_html( $label ); ?>:
		<?php if ( $required ) : ?>
		<span class="required">*</span>
		<?php endif;?>
	</label>
	<select name="<?php echo esc_attr( $name ); ?>" id="<?php echo esc_attr( $id ); ?>" <?php disabled( $disabled ); ?>>
	<?php foreach ( $options as $option ) : ?>
		<option <?php selected( $option, $value ) ?> value="<?php echo esc_attr( $option ); ?>"><?php echo esc_html( $option ); ?></option>
	<?php endforeach; ?>
	</select>
<?php echo $after_field; ?>
