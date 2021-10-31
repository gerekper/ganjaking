<?php
/**
 * The main search template file
 */

$placeholder = __( 'Search', 'porto' ) . '&hellip;';
?>

<form method="get" id="searchform" class="searchform" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<div class="input-group">
		<input class="form-control" placeholder="<?php echo esc_attr( $placeholder ); ?>" name="s" id="s" type="text">
		<button type="submit" class="btn btn-dark p-2"><i class="d-inline-block porto-icon-search-3"></i></button>
	</div>
</form>
