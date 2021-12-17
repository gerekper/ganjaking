<?php
/**
 * Product Changes
 *
 * This template can be overridden by copying it to yourtheme/ali2woo/emails/product-changes.php
 *
 *
 */
?>

<h2>
<?php echo $email_subheading; ?>
</h2>

<div style="margin-bottom: 40px;">
	<table class="td" cellspacing="0" cellpadding="6" style="width: 100%; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;" border="1">
		<thead>
			<tr>
				<th class="td" scope="col" style="text-align:5px;"><?php esc_html_e( 'Product', 'ali2woo' ); ?></th>
				<th class="td" scope="col" style="text-align:5px;"><?php esc_html_e( 'Quantity', 'ali2woo' ); ?></th>
				<th class="td" scope="col" style="text-align:5px;"><?php esc_html_e( 'Price', 'ali2woo' ); ?></th>
				<th class="td" scope="col" style="text-align:5px;"><?php esc_html_e( 'New variant', 'ali2woo' ); ?></th>
			</tr>
		</thead>
		<tbody>
			

            <?php foreach ($items as $item ) : ?>
				<tr>
				<td class="td" style="text-align:5px; vertical-align: middle; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif; word-wrap:break-word;">
				<a href="<?php echo $item['url']; ?>"><?php echo $item['title']; ?></a><br/><br/>
				<?php /*
				<a href="<?php echo $item['url']; ?>"><?php echo $item['image-src']; ?></a><br/>
				*/?>
				<?php if ($item['not_available_product']) : ?>
					<strong><?php esc_html_e( 'Product is not available on AliExpress!', 'ali2woo' ); ?></strong>
				<?php else: ?>
					<strong><?php esc_html_e( 'Link to AliExpress'); ?>:</strong> <a href="<?php echo $item['original_url']; ?>"><?php esc_html_e( 'click here', 'ali2woo' ); ?></a>
				<?php endif; ?>
				</td>
				<td class="td" style="text-align:5px; vertical-align:middle; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;">
				<?php if ($item['is_stock_changed']) : ?>
					<strong>Old:</strong> <?php echo $item['is_stock_changed']['old_quantity']; ?><br/>  <strong>New:</strong> <?php echo $item['is_stock_changed']['quantity']; ?>
                <?php else: ?>
	
				<?php endif; ?>
				</td>
				<td class="td" style="text-align:5px; vertical-align:middle; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;">
				<?php if ($item['is_price_changed']) : ?>
					<strong>Old:</strong> <span style="text-decoration: line-through;"><?php echo wc_price($item['is_price_changed']['old_regular_price']); ?></span> <?php echo wc_price($item['is_price_changed']['old_price']); ?><br/>
				    <strong>New:</strong> <span style="text-decoration: line-through;"><?php echo wc_price($item['is_price_changed']['regular_price']); ?></span> <?php echo wc_price($item['is_price_changed']['price']); ?>
				<?php else: ?>
		
				<?php endif; ?>
				</td>
				<td class="td" style="text-align:5px; vertical-align:middle; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;">
				<?php if ($item['has_new_variants']) : ?>
					<?php esc_html_e( 'yes', 'ali2woo' ); ?>
				<?php else: ?>
	
				<?php endif; ?>
				</td>
				</tr>
        
			<?php endforeach; ?>
		</tbody>
	</table>
</div>
