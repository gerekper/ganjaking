<?php
/**
 * Addon tabs from Editor Template
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\ProductAddOns
 * @version 2.0.0
 *
 * @var int $addon_id The add-on id.
 * @var string $addon_type The add-on type.
 */

$addon_tabs = yith_wapo_get_addon_tabs( $addon_id, $addon_type );
?>

<div id="addon-tabs">
<?php
foreach ( $addon_tabs as $tab_id => $addon_tab ) {
	?>
		<a href="#" id="<?php echo esc_html( $addon_tab['id'] ); ?>" class="<?php echo esc_html( $addon_tab['class'] ); ?>"><?php echo esc_html( $addon_tab['label'] ); ?></a>
	<?php
}
?>
</div>
