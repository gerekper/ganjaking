<?php

use AC\Admin\Tooltip;
use AC\View;

?>
<div class="ac-segments -admin">
	<div class="ac-segments__create">
		<span class="cpac_icons-segment"></span>
		<button class="button button-primary">
			<?php _e( 'Save Filters', 'codepress-admin-columns' ); ?>
		</button>
	</div>
	<div class="ac-segments__list -personal">
		<div class="ac-segments__list__items"></div>
	</div>
	<div class="ac-segments__list -global">
		<div class="ac-segments__list__label"><?= __( 'Public', 'codepress-admin-columns' ); ?>
		</div>
		<div class="ac-segments__list__items"></div>
	</div>

	<?php
	$content = ( new View() )->set_template( 'table/tooltip-saved-filters' )->render();

	$tooltip = new Tooltip( 'filtered_segments', [
		'content'    => $content,
		'link_label' => __( 'Instructions', 'codepress-admin-columns' ),
		'title'      => __( 'Instructions', 'codepress-admin-columns' ),
	] );

	?>
	<div class="ac-segments__instructions">
		<?php
		echo $tooltip->get_label();
		echo $tooltip->get_instructions();
		?>
	</div>

</div>