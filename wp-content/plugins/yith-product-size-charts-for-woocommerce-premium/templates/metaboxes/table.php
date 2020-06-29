<?php
/**
 * Template of Metabox table
 *
 * @author Yithemes
 * @package YITH Product Size Charts for WooCommerce
 * @version 1.0.0
 */

if ( !defined( 'YITH_WCPSC' ) ) { exit; } // Exit if accessed directly

$t = json_decode($table_meta);
?>

<div id="yith-wcpsc-metabox-table-wrapper">
    <input id="yith-wcpsc-table-hidden" type="hidden" name="_table_meta" value='<?php echo str_replace( '\'', '&apos;', $table_meta )?>'>
    <table id="yith-wcpsc-metabox-table">
        <thead>
        <tr>
            <?php foreach($t[0] as $col): ?>
                <th>
                    <input type="button" class="yith-wcpsc-add-col yith-wcpsc-table-button yith-wcpsc-table-button-add" value="+" />
                    <input type="button" class="yith-wcpsc-del-col yith-wcpsc-table-button yith-wcpsc-table-button-del" value="-" />
                </th>
            <?php endforeach; ?>
            <th></th>
        </tr>
        </thead>

        <tbody>
        <?php foreach($t as $row): ?>
            <tr>
                <?php foreach($row as $col): ?>
                <td>
                    <input class="yith-wcpsc-input-table" type="text" value="<?php echo str_replace('"', '&quot;', $col) ?>"/>
                </td>
                <?php endforeach; ?>
                <td class="yith-wcpsc-table-button-container">
                    <input type="button" class="yith-wcpsc-add-row yith-wcpsc-table-button yith-wcpsc-table-button-add" value="+" />
                    <input type="button" class="yith-wcpsc-del-row yith-wcpsc-table-button yith-wcpsc-table-button-del" value="-" />
                </td>
            </tr>
        <?php endforeach; ?>


        </tbody>
    </table>
</div>
