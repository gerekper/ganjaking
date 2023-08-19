<?php

use AC\View;

/**
 * @var array $items
 */
$items = $this->menu_items;
$active = $this->active;

?>
<?= ( new View( [ 'license_status' => '' ] ) )->set_template( 'admin/header' ) ?>

<nav class="cpac-admin-nav">
	<ul class="cpac-step-nav">
		<?php
		foreach ( $items as $name => $label ) : ?>
			<li class="cpac-step-nav__item <?= in_array( $name, $active, true ) ? '-active' : ''; ?>">
				<?= $label; ?>
			</li>
		<?php endforeach; ?>
	</ul>
</nav>