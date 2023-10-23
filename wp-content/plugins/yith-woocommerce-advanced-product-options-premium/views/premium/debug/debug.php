<?php
/**
 * Debug Template
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\ProductAddOns
 * @version 2.0.0
 */

defined( 'YITH_WAPO' ) || exit; // Exit if accessed directly.

global $wpdb;
$nonce = wp_create_nonce( 'wapo_action' );

$block_table_results        = array();
$addon_table_results        = array();
$blocks_table_exists        = false;
$blocks_assoc_table_exists  = false;
$addons_table_exists        = false;
$blocks_backup_table_exists = false;
$addons_backup_table_exists = false;

$blocks_table_name       = $wpdb->prefix . 'yith_wapo_blocks';
$blocks_assoc_table_name = $wpdb->prefix . 'yith_wapo_blocks_assoc';
$addons_table_name       = $wpdb->prefix . 'yith_wapo_addons';

$blocks_backup_table_name = $wpdb->prefix . 'yith_wapo_blocks_backup';
$addons_backup_table_name = $wpdb->prefix . 'yith_wapo_addons_backup';

if ( $wpdb->get_var( "SHOW TABLES LIKE '$blocks_table_name'" ) === $blocks_table_name ) {
	$blocks_table_exists = true; // phpcs:ignore
}

if ( $wpdb->get_var( "SHOW TABLES LIKE '$blocks_assoc_table_name'" ) === $blocks_assoc_table_name ) {
    $blocks_assoc_table_exists = true; // phpcs:ignore
}
if ( $wpdb->get_var( "SHOW TABLES LIKE '$addons_table_name'" ) === $addons_table_name ) {
	$addons_table_exists = true;
}

if ( false !== $blocks_table_exists && false !== $addons_table_exists ) {
	$block_table_results = $wpdb->get_results( "SELECT id from {$wpdb->prefix}yith_wapo_blocks WHERE id IS NOT NULL" );
	$addon_table_results = $wpdb->get_results( "SELECT id from {$wpdb->prefix}yith_wapo_addons WHERE id IS NOT NULL" );
}

if ( $wpdb->get_var( "SHOW TABLES LIKE '$blocks_backup_table_name'" ) === $blocks_backup_table_name ) {
	$blocks_backup_table_exists = true; // phpcs:ignore
}
if ( $wpdb->get_var( "SHOW TABLES LIKE '$addons_backup_table_name'" ) === $addons_backup_table_name ) {
	$addons_backup_table_exists = true; // phpcs:ignore
}

?>

<div id="plugin-fw-wc" class="yit-admin-panel-content-wrap yith-plugin-ui">
	<div id="yith-wapo-panel-debug" class="yith-plugin-fw yit-admin-panel-container">
		<div class="">
            <div>
                <h3>Options in Database:</h3>
                <div class="options-database" style="display: grid; grid-template-columns: repeat(2, 50%);">
                    <span><b>yith_wapo_v2</b></span>
                    <div style="display: flex;"><?php echo ! empty( get_option( 'yith_wapo_v2' ) ) ? get_option( 'yith_wapo_v2' ) : 'Empty'; ?><div>&nbsp;- Empty or 'no' means that customer was in the old panel</div></div>
                    <span><b>yith_wapo_db_update_scheduled_for</b></span>
                    <div><?php echo get_option( 'yith_wapo_db_update_scheduled_for' ); ?></div>
                    <span><b>yith_wapo_db_version_option</b></span>
                    <div><?php echo get_option( 'yith_wapo_db_version_option' ); ?></div>
                    <span><b>yith_wapo_remove_del_column</b></span>
                    <div><?php echo get_option( 'yith_wapo_remove_del_column' ); ?></div>
                </div>


            </div>

			<div class="list-table-title">
				<h1 style="margin: 30px 0px"><?php echo esc_html( 'Debug panel v2' ); ?></h1>
			</div>

			<div class="fields">

				<!-- Option field -->
				<div class="field-wrap">
					<label for="option-characters-limit"><?php echo 'Create datatables'; ?>:</label>
					<div class="field">
						<?php


						if ( false !== $blocks_table_exists && false !== $addons_table_exists ) {
							echo '<span style="color: #94aa09;"><span class="dashicons dashicons-database-view"></span> '
								 . 'Tables created successfully.' . '</span><br>';
						} else {
							echo '<span style="color: #c92c2c;"><span class="dashicons dashicons-database-remove"></span> '
								 . 'Tables does not exists' . '</span><br>';
							?>
							<a href="admin.php?page=yith_wapo_panel&tab=debug&wapo_action=control_debug_options&option=create_tables&nonce=<?php echo esc_attr( $nonce ); ?>" class="yith-plugin-fw__button--primary">
								<span class="dashicons dashicons-database-add"></span> Create tables
							</a>
							<?php
						}
						?>
						<span class="description"><?php echo '<b>Create</b> the datatables yith_wapo_blocks and yith_wapo_addons'; ?></span>
					</div>
				</div>
				<!-- End option field -->

				<!-- Option field -->
				<div class="field-wrap">
					<label for="option-characters-limit"><?php echo 'Clear datatables'; ?>:</label>
					<div class="field">
						<?php


						if ( false !== $blocks_table_exists && false !== $addons_table_exists ) {
							echo '<span style="color: #94aa09;"><span class="dashicons dashicons-database-view"></span> '
								 . 'Tables exists.' . '</span><br>';
							?>
							<a href="admin.php?page=yith_wapo_panel&tab=debug&wapo_action=control_debug_options&option=clear_tables&nonce=<?php echo esc_attr( $nonce ); ?>" class="yith-plugin-fw__button--delete">
								Clear tables
							</a>
							<?php
						} else {
							echo '<span style="color: #c92c2c;"><span class="dashicons dashicons-database-remove"></span> '
								 . 'Tables does not exists. Create the tables before doing this action.' . '</span><br>';
						}
						?>
						<span class="description"><?php echo '<b>Clear</b> the datatables yith_wapo_blocks and yith_wapo_addons'; ?></span>
					</div>
				</div>
				<!-- End option field -->

				<!-- Option field -->
				<div class="field-wrap">
					<label for="option-characters-limit"><?php echo 'Restore addons from backup tables'; ?>:</label>
					<div class="field">
						<?php
						if ( count( $addon_table_results ) > 0 && count( $block_table_results ) > 0 ) {
							echo '<span style="color: #c92c2c;"><span class="dashicons dashicons-database-remove"></span> '
								 . 'Tables are not empty! Clear tables before doing this action' . '</span><br>';
						} else {
							if ( false !== $blocks_table_exists && false !== $addons_table_exists ) {
								if ( false !== $blocks_backup_table_exists && false !== $addons_backup_table_exists ) {
									echo '<span style="color: #94aa09;"><span class="dashicons dashicons-database-view"></span> '
										 . 'Backup tables exists and original tables are empty.' . '</span><br>';
									?>
									<a href="admin.php?page=yith_wapo_panel&tab=debug&wapo_action=control_debug_options&option=restore_addons&nonce=<?php echo esc_attr( $nonce ); ?>" class="yith-update-button">
										Restore addons
									</a>
									<?php
								} else {
									echo '<span style="color: #c92c2c;"><span class="dashicons dashicons-database-remove"></span> '
										 . 'Backup tables does not exists.' . '</span><br>';
								}
							} else {
								echo '<span style="color: #c92c2c;"><span class="dashicons dashicons-database-remove"></span> '
									 . 'Tables does not exists. Create the tables before doing this action.' . '</span><br>';
							}
						}

						?>
						<span class="description"><?php echo 'Copy all addons saved in the backup tables (yith_wapo_addons_backup and yith_wapo_blocks_backup)'; ?></span>
					</div>
				</div>
				<!-- End option field -->

				<div class="custom-field-addons" style="
						background: #85e6c1;
						font-size: 14px;
						font-weight: 700;
						padding: 10px;
						width: 50%;
					">
					<span>Executing both actions, the migration background process will be executed again ( Copy of all addons from v1 ).</span>
				</div>
				<div style="border: 3px solid #85e6c1;
				padding: 10px;
				padding-top: 20px;
				border-top: none;
				width: 49.78%;
				margin-bottom: 10px">

					<!-- Option field -->
					<div class="field-wrap">
						<label for="option-characters-limit"><span style="font-size: 15px"><b>STEP 1 - </b></span>Remove <b>imported</b> column from tables':</label>
						<div class="field">
							<a href="admin.php?page=yith_wapo_panel&tab=debug&wapo_action=control_debug_options&option=remove_column&nonce=<?php echo esc_attr( $nonce ); ?>" class="yith-plugin-fw__button--delete">
								Remove column
							</a>
							<span class="description">
								Remove "<b>imported</b>" column from <b>yith_wapo_groups</b> and <b>yith_wapo_types</b>
							</span>
						</div>
					</div>
					<!-- End option field -->

					<!-- Option field -->
					<div class="field-wrap">
						<label for="option-characters-limit"><span style="font-size: 15px"><b>STEP 2 - </b></span>Delete database options to test migration':</label>
						<div class="field">
							<a href="admin.php?page=yith_wapo_panel&tab=debug&wapo_action=control_debug_options&option=db_options&nonce=<?php echo esc_attr( $nonce ); ?>" class="yith-update-button">
								Delete database options
							</a>
							<span class="description">
							<?php
							echo wp_kses_post(
								'DB options:<br><b>yith_wapo_db_update_scheduled_for</b>
						<b>yith_wapo_db_version_option</b>
						'
							);
							?>
							</span>
						</div>
					</div>
					<!-- End option field -->

					<span><b>Important: The backup tables are created and all the addons are copied in the migration process. From the debug tab this process won't be executed.</b></span>
					<br><br>

					<a href="admin.php?page=wc-status&tab=action-scheduler&s=wapo&action=-1&paged=1&action2=-1" target="_blank" class="yith-plugin-fw__button--primary">
						Check action schedulers
					</a>
					<a href="admin.php?page=yith_wapo_panel&tab=debug&wapo_action=control_debug_options&option=remove_schedulers&nonce=<?php echo esc_attr( $nonce ); ?>" class="yith-plugin-fw__button--delete">
						Remove action schedulers
					</a>
				</div>
		</div>


            <div class="list-table-title" style="margin-top: 50px;">
                <h1 style="margin: 30px 0px"><?php echo esc_html( 'Debug panel v4' ); ?></h1>
            </div>
            <div class="field-wrap">
                <label for=""><?php echo 'New columns on blocks table'; ?>:</label>

                <div class="field">
                    <?php
                    $should_exists = array( 'id', 'user_id', 'vendor_id', 'settings', 'priority', 'visibility', 'creation_date', 'last_update', 'deleted', 'name', 'product_association', 'exclude_products', 'user_association', 'exclude_users' );
                    // An array of Field names
                    $existing_columns = $wpdb->get_col("DESC {$blocks_table_name}", 0);

                    if ( ! empty( $existing_columns ) ) {
                        ?>
                        <div style="display: flex; column-gap: 20px; border: 1px solid #222; padding: 15px;">
                        <?php
                        foreach( $should_exists as $column_name ) {
                            ?>

                            <div style="display: flex; flex-direction: column; align-items: center;">
                                <span style="font-size: 15px;"><?php echo esc_html( $column_name ); ?></span>
                                <span><?php echo in_array( $column_name, $existing_columns ) ? '<span style="color: forestgreen">exists</span>' : '<span style="color: #c92c2c">no exists</span>'; ?></span>
                            </div>

                            <?php
                        }
                        ?>
                        </div>
                        <?php
                    }

                    ?>
                    <span class="description">
                        Check if <b><?php echo esc_html( $blocks_table_name ); ?></b> columns are correct.
                    </span>
                </div>

            </div>
            <div class="field-wrap">
                <label for=""><?php echo 'New table'; ?>:</label>

                <div class="field">
                    <?php
                    if ( false !== $blocks_assoc_table_exists ) {
                        echo '<span style="color: #94aa09;"><span class="dashicons dashicons-database-view"></span><b>'
                             . esc_html( $blocks_assoc_table_name ) . '</b>' . ' table created correctly.' . '</span><br>';
                    } else {
                        ?>
                        <span style="color: #c92c2c">Table not created</span>
                        <?php
                    }
                    ?>
                    <span class="description">
                        Check if <b><?php echo esc_html( $blocks_assoc_table_name ); ?></b> table is created.
                    </span>
                </div>

            </div>
            <!-- Option field -->
            <div class="field-wrap">
                <label for=""><?php echo 'Rerun v4 action scheduler'; ?>:</label>
                <div class="field">
                    <a href="admin.php?page=yith_wapo_panel&tab=debug&wapo_action=control_debug_options&option=rerun_v4_action&nonce=<?php echo esc_attr( $nonce ); ?>" class="yith-update-button">
                        Execute action scheduler
                    </a>
                    <a href="admin.php?page=wc-status&tab=action-scheduler&s=wapo&action=-1&paged=1&action2=-1" target="_blank" class="yith-plugin-fw__button--primary">
                        Check action schedulers
                    </a>
                    <span class="description">
                        Execute the action scheduler to create additional columns to 'yith_wapo_blocks' and the new table 'yith_wapo_blocks_assoc'
                    </span>
                </div>
            </div>
            <!-- End option field -->
	</div>
</div>

    <style>
        .options-database > *{
            border: 1px solid grey;
            padding: 10px;
        }
        </style>
