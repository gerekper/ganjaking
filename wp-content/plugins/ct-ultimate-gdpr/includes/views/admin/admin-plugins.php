<?php

/**
 * The template for displaying plugins controller view in wp-admin
 *
 * You can overwrite this template by copying it to yourtheme/ct-ultimate-gdpr/admin folder
 *
 * @version 1.0
 *
 */

?>

<div class="ct-ultimate-gdpr-wrap">

    <div class="ct-ultimate-gdpr-branding">
        <div class="ct-ultimate-gdpr-img">
            <img src="<?php echo ct_ultimate_gdpr_url() . '/assets/css/images/branding.jpg' ?>">
        </div>
        <div class="text">
            <div class="ct-ultimate-gdpr-plugin-name"><?php echo esc_html__( 'Ultimate GDPR', 'ct-ultimate-gdpr' ); ?></div>
            <div class="settings"><?php echo esc_html__( 'Settings', 'ct-ultimate-gdpr' ); ?></div>
        </div>
    </div>


    <!-- TABS ( cc ) -->
    <h2 class="nav-tab-wrapper">
        <a href="<?php echo admin_url( 'admin.php?page=ct-ultimate-gdpr' ); ?>" class="nav-tab">
			<?php echo esc_html__( 'Introduction', 'ct-ultimate-gdpr' ); ?>
        </a>
        <a href="#" class="nav-tab nav-tab-active">
			<?php echo esc_html__( 'Compatibility', 'ct-ultimate-gdpr' ); ?>
        </a>
    </h2>
    <!-- END TABS ( cc ) -->

    <div class="ct-ultimate-gdpr-width"><!-- ADD DIV WITH .ct-ultimate-gdpr-width ( cc ) -->
        <table class="wp-list-table widefat fixed striped ct-ultimate-gdpr-inner-wrap"><!-- ADD .ct-ultimate-gdpr-inner-wrap ( cc ) -->
            <thead>
            <tr>
                <th><?php echo esc_html__( 'Plugin', 'ct-ultimate-gdpr' ); ?></th>
                <th><?php echo esc_html__( 'Collects user data', 'ct-ultimate-gdpr' ); ?></th>
                <th><?php echo esc_html__( 'Compatible with Ultimate GDPR', 'ct-ultimate-gdpr' ); ?></th>
            </tr>
            </thead>
            <tbody>

			<?php

			if ( ! empty( $options['plugins'] ) ) :

				foreach ( $options['plugins'] as $plugin ) : ?>

					<?php

					$row_style = '';

					if (
						$plugin['compatible'] === CT_Ultimate_GDPR_Controller_Plugins::PLUGIN_COMPATIBLE_YES ||
						$plugin['collects_data'] === CT_Ultimate_GDPR_Controller_Plugins::PLUGIN_COLLECTS_DATA_NO
					) :

						$row_style = 'style="background-color:lightgreen;"';

                    elseif ( $plugin['compatible'] === CT_Ultimate_GDPR_Controller_Plugins::PLUGIN_COMPATIBLE_PARTLY ) :

						$row_style = 'style="background-color:yellow;"';

                    elseif (
						$plugin['collects_data'] === CT_Ultimate_GDPR_Controller_Plugins::PLUGIN_COLLECTS_DATA_YES ||
						$plugin['collects_data'] === CT_Ultimate_GDPR_Controller_Plugins::PLUGIN_COLLECTS_DATA_PROBABLY
					) :

						$row_style = 'style="background-color:lightsalmon;"';

					else:

						$row_style = 'style="background-color:lightyellow;"';

					endif;

					?>

                    <tr <?php echo $row_style; ?>>

                        <td><?php echo esc_html( $plugin['name'] ); ?></td>

                        <td>

							<?php

							if ( $plugin['collects_data'] === CT_Ultimate_GDPR_Controller_Plugins::PLUGIN_COLLECTS_DATA_YES ) {
								echo esc_html__( 'Yes', 'ct-ultimate-gdpr' );
							} elseif ( $plugin['collects_data'] === CT_Ultimate_GDPR_Controller_Plugins::PLUGIN_COLLECTS_DATA_NO ) {
								echo esc_html__( 'No', 'ct-ultimate-gdpr' );
							} elseif ( $plugin['collects_data'] === CT_Ultimate_GDPR_Controller_Plugins::PLUGIN_COLLECTS_DATA_PROBABLY ) {
								echo esc_html__( 'Probably', 'ct-ultimate-gdpr' );
							} else {
								echo esc_html__( 'Unknown', 'ct-ultimate-gdpr' );
							}

							?>

                        </td>

                        <td>

							<?php

							if ( $plugin['compatible'] === CT_Ultimate_GDPR_Controller_Plugins::PLUGIN_COMPATIBLE_YES ) {
								echo esc_html__( 'Yes', 'ct-ultimate-gdpr' );
							} elseif ( $plugin['compatible'] === CT_Ultimate_GDPR_Controller_Plugins::PLUGIN_COMPATIBLE_PARTLY ) {
								echo esc_html__( 'Partly', 'ct-ultimate-gdpr' );
							} else {
								echo esc_html__( 'No', 'ct-ultimate-gdpr' );
							}

							?>

                        </td>

                    </tr>

				<?php

				endforeach;
			endif;

			?>

            </tbody>
            <tfoot>
            </tfoot>
        </table>
    </div><!-- ADD CLOSING DIV FOR .ct-ultimate-gdpr-width ( cc ) -->

</div>