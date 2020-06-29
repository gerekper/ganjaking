<?php foreach ( $options as $option => $value ): ?>
	<option value="<?php echo esc_attr( $option ); ?>"><?php echo esc_html( $value ); ?></option>
<?php endforeach; ?>