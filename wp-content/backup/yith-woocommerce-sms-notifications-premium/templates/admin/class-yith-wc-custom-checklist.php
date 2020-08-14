<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 *
 * @var array $field
 */

! defined( 'ABSPATH' ) && exit; // Exit if accessed directly

extract( $field );

?>
	<div class="ywcc-checklist-div" style="vertical-align: top; margin-bottom: 3px;" id="<?php echo esc_attr( $id ); ?>">
		<input
			type="hidden"
			id="<?php echo esc_attr( $id ); ?>"
			class="ywcc-values"
			name="<?php echo esc_attr( $name ); ?>"
			value="<?php echo $value; ?>"
		/>

		<span class="ywcc-value-list select2 select2-container select2-container--default">
			<span class="selection">
				<span class="select2-selection select2-selection--multiple">
					<ul class="select2-selection__rendered">
					</ul>
				</span>
			</span>
			<div class="ywcc-checklist-ajax">
				<input
					type="text"
					id="ywcc-new-element-<?php echo esc_attr( $id ); ?>"
					class="ywcc-insert select2-input form-input-tip"
					autocomplete="off"
					autocorrect="off"
					autocapitalize="off"
					spellcheck="false"
					placeholder="<?php echo esc_attr( $field['placeholder'] ); ?>"
				/>
			</div>
		</span>
	</div>
<?php
if ( isset( $field['desc-inline'] ) ) {
	echo '<span class="description inline">' . $field['desc-inline'] . '</span>';
}
