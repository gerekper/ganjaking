<?php


class WC_Conditional_Content_Input_Order_Status {

	public function __construct() {
		// vars
		$this->type = 'Order_Status';

		$this->defaults = array(
			'multiple'      => 0,
			'allow_null'    => 0,
			'choices'       => array(),
			'default_value' => '',
			'class'         => '',
			'placeholder'   => ''
		);
	}

	public function render( $field, $value = null ) {
		$field = array_merge( $this->defaults, $field );
		if ( ! isset( $field['id'] ) ) {
			$field['id'] = sanitize_title( $field['id'] );
		}

		$value['status'] = isset( $value['status'] ) ? $value['status'] : '';

		?>

        <table style="width:100%;">
            <tr>
                <td><?php _e( 'Order Count', 'wc_conditional_content' ); ?></td>
                <td style="width:162px;"><?php _e( 'Order Status', 'wc_conditional_content' ); ?></td>
            </tr>
            <tr>
                <td style="width:162px; vertical-align:top;">
                    <input type="text" id="<?php echo $field['id']; ?>_qty" name="<?php echo $field['name']; ?>[qty]"
                           value="<?php echo isset( $value['qty'] ) ? $value['qty'] : 1; ?>"/>
                </td>
                <td>
					<?php echo '<select id="' . $field['id'] . '" class="' . $field['class'] . '" name="' . $field['name'] . '[status]">'; ?>

					<?php
					$sts = wc_get_order_statuses();
					foreach ( $sts as $key => $status ) {
						echo '<option ' . selected( $value['status'], $key ) . ' value="' . $key . '">' . $status . '</option>';
					}
					?>

					<?php echo '</select>'; ?>

                </td>
            </tr>
        </table>
		<?php
	}

}
