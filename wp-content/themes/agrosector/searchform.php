
<?php $unique_id = uniqid( 'search-form-' ); ?>

<form role="search" method="get" class="search_form gt3_search_form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label for="<?php echo esc_attr($unique_id); ?>"><?php echo _x( 'Search', 'label', 'agrosector' ); ?></label>
    <input class="search_text" id="<?php echo esc_attr($unique_id); ?>" type="text" name="s">
    <input class="search_submit" type="submit" value="<?php esc_attr_e('Search', 'agrosector'); ?>">
</form>