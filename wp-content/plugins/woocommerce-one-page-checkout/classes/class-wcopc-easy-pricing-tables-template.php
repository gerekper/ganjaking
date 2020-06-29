<?php
/**
 * @package		WooCommerce One Page Checkout
 * @subpackage	Easy Pricing Tables
 * @category	Template Class
 * @version 1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class WCOPC_Easy_Pricing_Tables_Template extends WCOPC_Template {

	public function __construct() {

		if ( ! self::is_easy_pricing_tables_active() ) {
			return;
		}

		$this->template_key = 'easy_pricing_table';
		$this->label        = __( 'Easy Pricing Table', 'wcopc' );
		$this->description  = __( 'Include a pricing table creating with the Easy Pricing Table plugin. Best for creating highly customised pricing tables.', 'wcopc' );

		add_filter( 'wcopc_product_selection_fields_before', array( $this, 'display_pricing_table' ), 10, 2 );

		add_action( 'wcopc_shortcode_iframe_after', array( $this, 'display_shortcode_selection_fields' ), 1 );

		parent::__construct();
	}

	/**
	 * Attached to the product selection fields hook to be display the custom
	 * pricing table rather than any product selection fields
	 *
	 * @since 1.0
	 */
	public function display_pricing_table( $template, $shortcode_atts ) {
		if ( $this->template_key == $template && isset( $shortcode_atts['easy_pricing_table_id'] ) ) {
			echo '<div id="opc-product-selection" data-opc_id="' . PP_One_Page_Checkout::$shortcode_page_id . '"class="wcopc">';
			echo do_shortcode( '[easy-pricing-table id="' . $shortcode_atts['easy_pricing_table_id'] . '"]' );
			echo '</div>';
		}
	}

	/**
	 * Add a select box for all Easy Pricing Tables that have been created on the site.
	 *
	 * @since 1.0
	 */
	public function display_shortcode_selection_fields() {

		// Get all Easy Pricing Tables posts that have been created
		$easy_pricing_tables = get_posts( array(
			'posts_per_page' => -1,
			'post_type' => 'easy-pricing-table',
		));
?>
	<fieldset id="wcopc_easy_pricing_table_fields" style="margin: 1em 0;">
		<label for="wcopc_easy_pricing_table_id" style="width: 70px; display: inline-block;"><?php _e( 'Pricing Table:', 'wcopc' ); ?></label>
		<?php if ( ! empty( $easy_pricing_tables ) ) : ?>
		<select id="wcopc_easy_pricing_table_id" style="width: 75%;">
			<?php foreach( $easy_pricing_tables as $easy_pricing_table ) : ?>
			<option value="<?php echo $easy_pricing_table->ID; ?>"><?php echo $easy_pricing_table->post_title; ?></option>
			<?php endforeach; ?>
		</select>
		<?php else : ?>
			<span><?php _e( 'No pricing tables available.', 'wcopc' ); ?>
		<?php endif; ?>
	</fieldset>
<?php
	}

	private static function is_easy_pricing_tables_active() {

		$is_easy_pricing_tables_active = false;

		$easy_pricing_table_plugin_slugs = array(
			'/pricing-table-plugin.php',
			'/easy-pricing-tables-premium.php',
		);

		foreach( $easy_pricing_table_plugin_slugs as $plugin_slug ) {
			$plugin_strlen = strlen( $plugin_slug );
			foreach ( PP_One_Page_Checkout::$active_plugins as $key => $plugin ) {
				if ( substr( $plugin, $plugin_strlen * -1 ) === $plugin_slug || substr( $key, $plugin_strlen * -1 ) === $plugin_slug ) {
					$is_easy_pricing_tables_active = true;
					break 2;
				}
			}
		}

		return $is_easy_pricing_tables_active;
	}
}
return new WCOPC_Easy_Pricing_Tables_Template();
