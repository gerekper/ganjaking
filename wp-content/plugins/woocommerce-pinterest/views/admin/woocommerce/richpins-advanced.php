<?php if ( ! defined( 'ABSPATH' ) ) {
	die;
}
/**
 * Declared variables
 *
 * @var array $fields
 * @var string $fieldKey
 * @var array $data
 */
?>
<tr valign="top">
	<th scope="row" class="titledesc">
		<label></label>
	</th>
	<td class="forminp">
		<a href="#" style="position: absolute; margin-top: -30px"
		   onclick="jQuery('#<?php echo esc_attr( $fieldKey ); ?>__wrapper').toggle(); event.preventDefault();">Advanced
			settings</a>
		<div id="<?php echo esc_attr( $fieldKey ); ?>__wrapper" style="display:none;">
			<?php foreach ( $fields as $_fieldKey => $value ) : ?>
				<fieldset>
					<legend class="screen-reader-text"><span></span></legend>
					<label for="<?php echo esc_attr( $fieldKey ); ?>[<?php echo esc_attr( $_fieldKey ); ?>]">
						<input class="" type="checkbox"
							   name="<?php echo esc_attr( $fieldKey ); ?>[<?php echo esc_attr( $_fieldKey ); ?>]"
							   style="" id="<?php echo esc_attr( $fieldKey ); ?>[<?php echo esc_attr( $_fieldKey ); ?>]"
							   value="1" <?php isset( $data[ $_fieldKey ] ) && checked( $data[ $_fieldKey ], 'yes' ); ?>>
						Enable <?php echo esc_attr( $value ); ?></label><br>
				</fieldset>
			<?php endforeach; ?>
		</div>
	</td>
</tr>
