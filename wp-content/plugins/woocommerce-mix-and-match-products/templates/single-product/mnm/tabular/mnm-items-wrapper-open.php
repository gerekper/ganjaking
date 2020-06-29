<?php
/**
 * Mix and Match Options Wrapper
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/mnm/grid/mnm-items-wrapper-open.php.
 *
 * HOWEVER, on occasion WooCommerce Mix and Match will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @author  Kathy Darling
 * @package WooCommerce Mix and Match/Templates
 * @since   1.3.0
 * @version 1.9.0
 */
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ){
	exit;
}

?>
<table cellspacing="0" class="mnm_table mnm_child_products">
	<?php if( count( $column_headers ) ) : ?>
	<thead>
		<tr>
			<?php foreach( (array) $column_headers as $id => $title ) : ?>
			<th class="product-<?php echo $id;?>"><?php echo $title;?></th>
			<?php endforeach; ?>
		</tr>
	</thead>
	<?php endif; ?>

	<tbody>