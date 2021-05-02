<?php
/**
 * @var $items
 */
?>

<ul>
	<?php foreach ( $items as $item ) : ?>
		<li><?php echo $item['value']; ?></li>
	<?php endforeach; ?>
</ul>
