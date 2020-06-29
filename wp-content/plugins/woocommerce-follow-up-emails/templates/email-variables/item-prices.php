<?php
/**
 * Template file for the email variable "{item_prices}".
 *
 * To edit this template, copy this file over to your wp-content/[current_theme]/follow-up-emails/email-variables
 * then edit the new file. A single variable named $lists is passed along to this template.
 *
 * $lists = array('items' => array(
 *      array(
 *          id:     Product ID
 *          sku:    Product's SKU
 *          link:   Absolute URL to the product
 *          name:   Product's name
 *          price:  Price of the product - unformatted
 *          qty:    Quantity bought
 *          categories: Array of product categories
 *      )
 * ))
 */
?>
<ul>
	<?php
	foreach ( $lists['items'] as $item ) {
		echo wp_kses_post( sprintf( '<li><a href="%s">%s X %d &ndash; %s</a></li>',
			$item['link'],
			$item['name'],
			$item['qty'],
			wc_price( $item['price'] ) )
		);
	} ?>
</ul>
