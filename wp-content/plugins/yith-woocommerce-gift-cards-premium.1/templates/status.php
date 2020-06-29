<style>
</style>

<?php foreach ( $sections as $section ) : ?>
    <?php if ( isset( $section[ "header" ] ) ): ?>
        <h3><?php echo $section[ "header" ]; ?></h3>
    <?php endif; ?>

    <table class="form-table">
        <tbody>

        <?php foreach ( $section as $data ) : ?>
            <?php if ( isset( $data[ "type" ] ) && ( "text" == $data[ "type" ] ) ) : ?>

                <tr valign="top">
                    <th scope="row" class="titledesc">
                        <label><?php echo $data[ "label" ]; ?></label>
                    </th>
                    <td class="forminp forminp-number">
                        <span><?php echo $data[ "description" ]; ?></span>
                    </td>
                </tr>
            <?php endif; ?>
        <?php endforeach; ?>
        </tbody>
    </table>

<?php endforeach; ?>