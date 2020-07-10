<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$current_tab = isset( $_GET['tab'] ) && isset( $tabs_metadata[ $_GET['tab'] ] ) ? sanitize_title( $_GET['tab'] ) : 'availability';

?>
<div class="wrap">
	<nav class="nav-tab-wrapper woo-nav-tab-wrapper">
	<?php
	foreach ( $tabs_metadata as $tab => $metadata ) {
		if ( current_user_can( $metadata['capability'] ) ) {
			?>
			<a class="nav-tab
			<?php
			if ( $tab === $current_tab ) {
				echo 'nav-tab-active';
			}
			?>
			" href="<?php echo esc_url( $metadata['href'] ); ?>"><?php echo esc_html( $metadata['name'] ); ?></a>
			<?php
		}
	}
	?>
		</nav>
	<?php
	if ( ! current_user_can( $tabs_metadata[ $current_tab ]['capability'] ) ) {
		esc_attr_e( 'Sorry, you are not allowed to access this tab.', 'woocommerce-bookings' );
	} else {
	?>
		<h1 class="screen-reader-text"><?php echo esc_html( $tabs_metadata[ $current_tab ]['name'] ); ?></h1>
		<h2><?php echo esc_html( $tabs_metadata[ $current_tab ]['name'] ); ?></h2>
		<div id="content">
			<?php call_user_func( $tabs_metadata[ $current_tab ]['generate_html'] ); ?>
		</div>
	<?php } ?>
</div>
