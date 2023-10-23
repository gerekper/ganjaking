<?php
/**
 * WAPO Template
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\ProductAddOns
 * @version 2.0.0
 *
 * @var object $addon
 * @var string $addon_type
 * @var int    $x
 */

defined( 'YITH_WAPO' ) || exit; // Exit if accessed directly.

?>

<div class="fields">
	<?php
	yith_wapo_get_view(
		'addon-editor/option-common-fields.php',
		array(
			'x'          => $x,
			'addon_type' => $addon_type,
			'addon'      => $addon
		),
        defined( 'YITH_WAPO_PREMIUM' ) && YITH_WAPO_PREMIUM ? 'premium/' : ''

    );
	?>
</div>
