<?php

use Instagram\Includes\WIS_Plugin;

$current_url = admin_url( 'admin.php?page=settings-' . WIS_Plugin::app()->getPluginName() );
$current_tab = 'instagram';
$TABS        = array(
	'instagram' => array(
		'current' => false,
		'caption' => 'Instagram',
		'icon'    => 'instagram',
		'url'     => $current_url . "&tab=instagram",
	),
<<<<<<< HEAD
	'youtube' => array(
		'current' => false,
		'caption' => 'Youtube',
		'icon'    => 'youtube',
		'url'     => $current_url . "&tab=youtube",
=======
	'Youtube' => array(
		'current' => false,
		'caption' => 'Youtube',
		'icon'    => 'youtube',
		'url'     => $current_url . "&tab=Youtube",
>>>>>>> 1b5ecdc13248a4b43e6ad472803763e724ada12c
	),
);
if ( isset( $_GET['tab'] ) && ! empty( $_GET['tab'] ) ) {
	$current_tab                     = htmlspecialchars( $_GET['tab'] );
	$current_url                     .= "&tab={$current_tab}";
	$TABS[ $current_tab ]['current'] = true;
} else {
	$current_tab                     = 'instagram';
	$current_url                     .= "&tab={$current_tab}";
	$TABS[ $current_tab ]['current'] = true;
}
?>
<div class="wis-container">
    <div class="wis-page-title">
        <h1><?php _e( 'Settings', 'insert-php' ) ?><?php echo WIS_Plugin::app()->getPluginTitle() . " " . WIS_Plugin::app()->getPluginVersion(); ?></h1>
    </div>
    <div id="tabs" class="tabs">
        <nav>
            <ul>
				<?php
				foreach ( $TABS as $key => $tab ) {
					if ( $tab['current'] ) {
						echo "<li class='tab-current'>";
					} else {
						echo "<li>";
					}
					echo "<a href='{$tab['url']}' class='icon-{$tab['icon']}'><span>{$tab['caption']}</span></a>";
					echo "</li>";
				}
				?>
            </ul>
        </nav>
        <div class="content">
            <section id="<?php echo $current_tab; ?>">
				<?php include_once WIS_PLUGIN_DIR . "/admin/views/{$current_tab}.php"; ?>
            </section>
        </div><!-- /content -->
    </div><!-- /tabs -->
</div>
