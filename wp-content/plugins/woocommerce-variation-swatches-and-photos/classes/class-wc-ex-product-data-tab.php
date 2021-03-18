<?php
if ( !class_exists( 'WC_EX_Product_Data_Tab_Swatches' ) ) {

	class WC_EX_Product_Data_Tab_Swatches {

		public $tab_class = '';
		public $tab_additional_class = '';
		public $tab_id = '';
		public $tab_title = '';
		public $tab_icon = '';
		public $tab_script_src = '';

		public function __construct( $tab_class, $tab_id, $tab_title, $tab_icon = '', $script = false ) {

			if ( is_array( $tab_class ) ) {
				$this->tab_class = $tab_class[0];
				for ( $x = 1; $x < count( $tab_class ); $x++ ) {
					$this->tab_additional_class .= ' ' . $tab_class[$x];
				}
			} else {
				$this->tab_class = $tab_class;
			}
			$this->tab_id = $tab_id;
			$this->tab_title = $tab_title;
			$this->tab_icon = $tab_icon;

			$this->tab_script_src;

			add_action( 'woocommerce_init', array(&$this, 'on_woocommerce_init') );
			add_action( 'admin_head', array(&$this, 'on_admin_head') );

			add_action( 'woocommerce_product_write_panel_tabs', array(&$this, 'product_write_panel_tabs'), 99 );
			add_action( 'woocommerce_product_data_panels', array(&$this, 'product_data_panel_wrap'), 99 );
			add_action( 'woocommerce_process_product_meta', array(&$this, 'process_meta_box'), 1, 2 );
		}

		public function on_woocommerce_init() {
			if ( empty( $this->tab_icon ) ) {
				$wc_default_icons = WC()->plugin_url() . '/assets/images/icons/wc-tab-icons.png';
				$this->tab_icon = $wc_default_icons;
			}
		}

		public function on_admin_head() {
				echo '<style type="text/css">';
				echo '#' . $this->tab_id . ' { padding:10px; }';
				echo '</style>';
				return;
		}

		public function product_write_panel_tabs() {
			?>
            <li class="<?php echo $this->tab_class; ?><?php echo $this->tab_additional_class; ?>"><a href="#<?php echo $this->tab_id; ?>"><span><?php echo $this->tab_title; ?></span></a></li>
			<?php
		}

		public function product_data_panel_wrap() {
			?>
			<div id="<?php echo $this->tab_id; ?>" class="panel <?php echo $this->tab_class; ?> woocommerce_options_panel wc-metaboxes-wrapper">
			<?php $this->render_product_tab_content(); ?>
			</div>
			<?php
		}

		public function render_product_tab_content() {
			
		}

		public function process_meta_box( $post_id, $post ) {
			
		}

	}

}
