<style id="ninja_table_custom_css_<?php echo $tableId; ?>" type="text/css">
<?php if($colors): ?>
<?php echo $css_prefix; ?>,
<?php echo $css_prefix; ?> table {
    background-color: <?php echo $colors['table_color_primary']; ?> !important;
    color: <?php echo $colors['table_color_secondary']; ?> !important;
    border-color: <?php echo $colors['table_color_border']; ?> !important;
}
<?php echo $css_prefix; ?> thead tr.footable-filtering th {
    background-color: <?php echo $colors['table_search_color_primary']; ?> !important;
    color: <?php echo $colors['table_search_color_secondary']; ?> !important;
}
<?php echo $css_prefix; ?>:not(.hide_all_borders) thead tr.footable-filtering th {
    <?php if($colors['table_search_color_border']): ?>
	 border : 1px solid <?php echo $colors['table_search_color_border']; ?> !important;
    <?php else: ?>
    border : 1px solid transparent !important;
    <?php endif; ?>
}
<?php echo $css_prefix; ?> .input-group-btn:last-child > .btn:not(:last-child):not(.dropdown-toggle) {
    background-color: <?php echo $colors['table_search_color_secondary']; ?> !important;
    color: <?php echo $colors['table_search_color_primary']; ?> !important;
}
<?php echo $css_prefix; ?> tr.footable-header, <?php echo $css_prefix; ?> tr.footable-header th, .colored_table table.ninja_table_pro.inverted.table.footable-details tbody tr th {
    background-color: <?php echo $colors['table_header_color_primary']; ?> !important;
    color: <?php echo $colors['table_color_header_secondary']; ?> !important;
}
<?php if($colors['table_color_header_border']) : ?>
<?php echo $css_prefix; ?>:not(.hide_all_borders) tr.footable-header th, <?php echo $css_prefix; ?>:not(.hide_all_borders) tbody tr th {
    border-color: <?php echo $colors['table_color_header_border']; ?> !important;
}
<?php endif; ?>

<?php if(isset($colors['table_color_border']) && $colors['table_color_border']) : ?>
<?php echo $css_prefix; ?>:not(.hide_all_borders) tbody tr td {
    border-color: <?php echo $colors['table_color_border']; ?> !important;
}
<?php endif; ?>
<?php echo $css_prefix; ?> tbody tr:hover {
    background-color: <?php echo $colors['table_color_primary_hover']; ?> !important;
    color: <?php echo $colors['table_color_secondary_hover']; ?> !important;
}
<?php echo $css_prefix; ?> tbody tr:hover td {
    border-color: <?php echo $colors['table_color_border_hover']; ?> !important;
}
<?php if(isset($colors['alternate_color_status']) && $colors['alternate_color_status'] == 'yes'): ?>
<?php echo $css_prefix; ?> tbody tr:nth-child(even) {
    background-color: <?php echo $colors['table_alt_color_primary']; ?> !important;
    color: <?php echo $colors['table_alt_color_secondary']; ?> !important;
}
<?php echo $css_prefix; ?> tbody tr:nth-child(odd) {
    background-color: <?php echo $colors['table_alt_2_color_primary']; ?> !important;
    color: <?php echo $colors['table_alt_2_color_secondary']; ?> !important;
}
<?php echo $css_prefix; ?> tbody tr:nth-child(even):hover {
    background-color: <?php echo $colors['table_alt_color_hover']; ?> !important;
}
<?php echo $css_prefix; ?> tbody tr:nth-child(odd):hover {
    background-color: <?php echo $colors['table_alt_2_color_hover']; ?> !important;
}
<?php endif; ?>

<?php echo $css_prefix; ?> tfoot .footable-paging {
    background-color: <?php echo $colors['table_footer_bg']; ?> !important;
}
<?php echo $css_prefix; ?> tfoot .footable-paging .footable-page.active a {
    background-color: <?php echo $colors['table_footer_active']; ?> !important;
}
<?php echo $css_prefix; ?>:not(.hide_all_borders) tfoot tr.footable-paging td {
    border-color: <?php echo $colors['table_footer_border']; ?> !important;
}
<?php endif; ?>
<?php if($cellStyles): ?>
    <?php foreach ($cellStyles as $cellStyle): ?>
<?php
$cell = maybe_unserialize($cellStyle->settings);
$cellPrefix = $css_prefix.'.ninja_footable.ninja_table_pro tbody tr.nt_row_id_'.$cellStyle->id;
?>
<?php echo $cellPrefix ?> {
<?php if(@$cell['row_bg']): ?>background: <?php echo $cell['row_bg'].' !important;'; endif; ?>
<?php if(@$cell['text_color']): ?>color: <?php echo $cell['text_color'].'!important;'; endif; ?>}
<?php if($cell && isset($cell['cell']) && is_array($cell['cell'])) : foreach ($cell['cell'] as $cell_key => $values): ?>
<?php $specCellPrefix = $cellPrefix.' .ninja_clmn_nm_'.$cell_key; ?>
<?php echo $specCellPrefix ?> {
<?php foreach ($values as $value_key => $value){ ?>
<?php if($value): echo $value_key; ?> : <?php echo $value.';'; endif; ?>
<?php } ?>
}
<?php echo $specCellPrefix ?> > * { color: inherit }
<?php endforeach; endif; // end of if(is_array($cell['cell'])) ?>
<?php endforeach; ?>
<?php endif; ?>

<?php if($hasStackable): ?>
<?php echo $css_prefix; ?>.ninja_stacked_table > tbody, <?php echo $css_prefix; ?>.ninja_stacked_table {
    background: transparent !important;
}
<?php if ($colors) : ?>
<?php echo $css_prefix; ?>.ninja_stacked_table .footable-details tbody {
    background-color: <?php echo $colors['table_color_primary']; ?> !important;
    color: <?php echo $colors['table_color_secondary']; ?> !important;
    border-color: <?php echo $colors['table_color_border']; ?> !important;
}
<?php echo $stackPrefix; ?> thead tr.footable-filtering th {
                                background-color: <?php echo $colors['table_search_color_primary']; ?> !important;
                                color: <?php echo $colors['table_search_color_secondary']; ?> !important;
                            }
<?php echo $stackPrefix; ?>:not(.hide_all_borders) thead tr.footable-filtering th {
<?php if($colors['table_search_color_border']): ?>
    border : 1px solid <?php echo $colors['table_search_color_border']; ?> !important;
<?php else: ?>
    border : 1px solid transparent !important;
<?php endif; ?>
}
<?php echo $stackPrefix; ?> .input-group-btn:last-child > .btn:not(:last-child):not(.dropdown-toggle) {
                                background-color: <?php echo $colors['table_search_color_secondary']; ?> !important;
                                color: <?php echo $colors['table_search_color_primary']; ?> !important;
                            }
<?php echo $stackPrefix; ?> tr.footable-header, <?php echo $stackPrefix; ?> tr.footable-header th {
                                background-color: <?php echo $colors['table_header_color_primary']; ?> !important;
                                color: <?php echo $colors['table_color_header_secondary']; ?> !important;
                            }
<?php endif; ?>
<?php if(isset($colors['table_color_header_border']) && $colors['table_color_header_border']) : ?>
<?php echo $stackPrefix; ?>:not(.hide_all_borders) tr.footable-header th {
    border-color: <?php echo $colors['table_color_header_border']; ?> !important;
}
<?php endif; ?>

<?php if(isset($colors['table_color_border']) && $colors['table_color_border']) : ?>
<?php echo $css_prefix; ?>:not(.hide_all_borders) tbody tr td table {
    border-color: <?php echo $colors['table_color_border']; ?> !important;
}
<?php endif; ?>
<?php if(isset($css_prefix['alternate_color_status']) && $css_prefix['alternate_color_status'] == 'yes'): ?>
<?php echo $stackPrefix; ?> tbody tr:nth-child(even) {
                               background-color: <?php echo $colors['table_alt_color_primary']; ?>;
                               color: <?php echo $colors['table_alt_color_secondary']; ?>;
                           }
<?php echo $stackPrefix; ?> tbody tr:nth-child(odd) {
                               background-color: <?php echo $colors['table_alt_2_color_primary']; ?>;
                               color: <?php echo $colors['table_alt_2_color_secondary']; ?>;
                           }
<?php echo $stackPrefix; ?> tbody tr:nth-child(even):hover {
                               background-color: <?php echo $colors['table_alt_color_hover']; ?>;
                           }
<?php echo $stackPrefix; ?> tbody tr:nth-child(odd):hover {
                               background-color: <?php echo $colors['table_alt_2_color_hover']; ?>;
                           }
<?php endif; ?>
<?php endif; ?>

<?php echo $custom_css; ?>
</style>
