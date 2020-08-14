<?php
/**
 * Template of table in Product Page
 *
 * @author Yithemes
 * @package YITH Product Size Charts for WooCommerce
 * @version 1.0.0
 */

if ( !defined( 'YITH_WCPSC' ) ) { exit; } // Exit if accessed directly

$t = json_decode($table_meta);
?>

<div class="yith-wcpsc-product-table-wrapper">
    <table class="yith-wcpsc-product-table">
        <thead>
        <tr>
            <?php foreach($t[0] as $col): ?>
                <th>
                    <?php echo $col; ?>
                </th>
            <?php endforeach; ?>
        </tr>
        </thead>

        <tbody>
        <?php foreach($t as $idx => $row): ?>
            <?php if ($idx == 0) continue; ?>
            <tr>
                <?php foreach($row as $col): ?>
                <td>
                    <?php echo str_replace('"', '&quot;', $col) ?>
                </td>
                <?php endforeach; ?>
            </tr>
        <?php endforeach; ?>

        </tbody>
    </table>
</div>
