<?php

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Used data
 *
 * @var array $data
 */

?>

<style>
    .boards-wrapp {
        position: relative;
    }
    .boards-created-response {
        position: absolute;
        bottom: -5px;
        left: 3px;
        margin: 0;
        font-weight: normal;
        display: none;
    }
    .boards-created-response.success {
        color: green;
    }
    .boards-created-response.error {
        color: red;
    }
</style>

<tr valign="top" class="boards-wrapp">
	<th scope="row" style="position:static">
		<button class="button" id="start_create_and_set_up_boards" data-prompt="<?php
		  esc_attr_e( 'Are you sure you want to create boards according to product category names?', 'woocommerce-pinterest' );
		?>">
			<?php echo esc_attr( $data['title'] ); ?>
		</button>
        <p class="boards-created-response"></p>
        <td>
            <p class="description">
              <?php esc_attr_e( 'Board names will be generated from category names.', 'woocommerce-pinterest' ); ?>
            </p>
        </td>
	</th>
</tr>
